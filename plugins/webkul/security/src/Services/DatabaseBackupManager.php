<?php

namespace Webkul\Security\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;
use Webkul\Security\Enums\SecurityAuditEvent;

class DatabaseBackupManager
{
    public function __construct(protected SecurityAuditLogger $auditLogger) {}

    public function directory(): string
    {
        $path = (string) config('security-backup.path', storage_path('app/private/database-backups'));

        if (! File::isDirectory($path)) {
            File::makeDirectory($path, 0750, true);
        }

        return $path;
    }

    /**
     * @return array{
     *     filename: string,
     *     path: string,
     *     size: int,
     *     driver: string,
     *     created_at: string,
     *     method: string
     * }
     */
    public function create(?string $note = null): array
    {
        $driver = DB::getDriverName();
        $stamp = now()->format('Y-m-d_His');
        $basename = 'auraerp-'.$driver.'-'.$stamp;
        $method = 'php';

        $path = match ($driver) {
            'sqlite'           => $this->backupSqlite($basename),
            'mysql', 'mariadb' => $this->backupMysql($basename, $method),
            'pgsql'            => $this->backupPgsql($basename, $method),
            default            => throw new RuntimeException(__('security::backup.errors.unsupported-driver', ['driver' => $driver])),
        };

        $meta = [
            'filename'   => basename($path),
            'path'       => $path,
            'size'       => File::size($path),
            'driver'     => $driver,
            'created_at' => now()->toIso8601String(),
            'method'     => $method,
            'note'       => $note,
        ];

        File::put($path.'.json', json_encode($meta, JSON_PRETTY_PRINT));

        $this->auditLogger->log(
            SecurityAuditEvent::BackupCreated,
            __('security::backup.audit.created', ['file' => $meta['filename']]),
            properties: $meta,
        );

        return $meta;
    }

    public function restoreAllowed(): bool
    {
        $configured = config('security-backup.allow_restore');

        if ($configured === null || $configured === '') {
            return ! app()->environment('production');
        }

        return filter_var($configured, FILTER_VALIDATE_BOOLEAN);
    }

    public function restore(string $filename): void
    {
        $path = $this->resolveExistingPath($filename);
        $driver = DB::getDriverName();

        if (! $this->restoreAllowed()) {
            $this->auditLogger->log(
                SecurityAuditEvent::BackupRestoreBlocked,
                __('security::backup.audit.restore-blocked', ['file' => basename($path)]),
                properties: [
                    'filename'    => basename($path),
                    'environment' => app()->environment(),
                ],
            );

            throw new RuntimeException(__('security::backup.errors.restore-disabled'));
        }

        match ($driver) {
            'sqlite'           => $this->restoreSqlite($path),
            'mysql', 'mariadb' => $this->restoreMysql($path),
            'pgsql'            => $this->restorePgsql($path),
            default            => throw new RuntimeException(__('security::backup.errors.unsupported-driver', ['driver' => $driver])),
        };

        $this->auditLogger->log(
            SecurityAuditEvent::BackupRestored,
            __('security::backup.audit.restored', ['file' => basename($path)]),
            properties: ['filename' => basename($path), 'driver' => $driver],
        );
    }

    /**
     * @return array{ok: bool, last_backup_at: ?string, age_hours: ?float, count: int, restore_allowed: bool, disk_free_mb: ?float}
     */
    public function health(): array
    {
        $items = $this->list();
        $latest = $items->first();
        $ageHours = null;

        if ($latest && ! empty($latest['created_at'])) {
            $ageHours = round(now()->diffInMinutes(Carbon::parse((string) $latest['created_at'])) / 60, 1);
        }

        $freeBytes = @disk_free_space($this->directory());

        return [
            'ok'              => $latest !== null && ($ageHours === null || $ageHours <= 36),
            'last_backup_at'  => $latest['created_at'] ?? null,
            'age_hours'       => $ageHours,
            'count'           => $items->count(),
            'restore_allowed' => $this->restoreAllowed(),
            'disk_free_mb'    => $freeBytes === false ? null : round($freeBytes / 1024 / 1024, 1),
        ];
    }

    public function delete(string $filename): void
    {
        $path = $this->resolveExistingPath($filename);

        File::delete($path);
        File::delete($path.'.json');

        $this->auditLogger->log(
            SecurityAuditEvent::BackupDeleted,
            __('security::backup.audit.deleted', ['file' => basename($path)]),
            properties: ['filename' => basename($path)],
        );
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function list(): Collection
    {
        $directory = $this->directory();

        return collect(File::files($directory))
            ->filter(fn ($file): bool => in_array($file->getExtension(), ['sql', 'gz', 'sqlite'], true)
                || Str::endsWith($file->getFilename(), ['.sql.gz']))
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->values()
            ->map(function ($file): array {
                $metaPath = $file->getPathname().'.json';
                $meta = File::exists($metaPath)
                    ? (json_decode(File::get($metaPath), true) ?: [])
                    : [];

                return [
                    'filename'   => $file->getFilename(),
                    'path'       => $file->getPathname(),
                    'size'       => $file->getSize(),
                    'size_label' => $this->humanBytes($file->getSize()),
                    'created_at' => $meta['created_at'] ?? Carbon::createFromTimestamp($file->getMTime())->toDateTimeString(),
                    'driver'     => $meta['driver'] ?? '—',
                    'method'     => $meta['method'] ?? '—',
                    'note'       => $meta['note'] ?? null,
                ];
            });
    }

    public function prune(): int
    {
        $retentionDays = max(0, (int) config('security-backup.retention_days', 14));
        $retentionCount = max(0, (int) config('security-backup.retention_count', 30));
        $deleted = 0;

        $items = $this->list()->values();

        if ($retentionDays > 0) {
            $cutoff = now()->subDays($retentionDays);

            foreach ($items as $item) {
                $created = $item['created_at'] ?? null;

                if ($created && now()->parse((string) $created)->lt($cutoff)) {
                    $this->deleteQuietly($item['filename']);
                    $deleted++;
                }
            }

            $items = $this->list()->values();
        }

        if ($retentionCount > 0 && $items->count() > $retentionCount) {
            foreach ($items->slice($retentionCount) as $item) {
                $this->deleteQuietly($item['filename']);
                $deleted++;
            }
        }

        return $deleted;
    }

    public function absolutePath(string $filename): string
    {
        return $this->resolveExistingPath($filename);
    }

    protected function deleteQuietly(string $filename): void
    {
        try {
            $path = $this->resolveExistingPath($filename);
            File::delete($path);
            File::delete($path.'.json');
        } catch (Throwable) {
        }
    }

    protected function resolveExistingPath(string $filename): string
    {
        $filename = basename($filename);
        $path = $this->directory().DIRECTORY_SEPARATOR.$filename;

        if (! File::exists($path) || ! File::isFile($path)) {
            throw new RuntimeException(__('security::backup.errors.not-found', ['file' => $filename]));
        }

        if (! Str::startsWith(realpath($path) ?: $path, realpath($this->directory()) ?: $this->directory())) {
            throw new RuntimeException(__('security::backup.errors.invalid-path'));
        }

        return $path;
    }

    protected function backupSqlite(string $basename): string
    {
        $database = (string) config('database.connections.sqlite.database');

        if ($database === '' || $database === ':memory:' || ! File::exists($database)) {
            throw new RuntimeException(__('security::backup.errors.sqlite-missing'));
        }

        $target = $this->directory().DIRECTORY_SEPARATOR.$basename.'.sqlite';
        File::copy($database, $target);

        return $target;
    }

    protected function restoreSqlite(string $path): void
    {
        $database = (string) config('database.connections.sqlite.database');

        if ($database === '' || $database === ':memory:') {
            throw new RuntimeException(__('security::backup.errors.sqlite-missing'));
        }

        DB::disconnect();
        File::copy($path, $database);
        DB::reconnect();
    }

    protected function backupMysql(string $basename, string &$method): string
    {
        $target = $this->directory().DIRECTORY_SEPARATOR.$basename.'.sql';

        if (config('security-backup.prefer_native', true) && $this->binaryExists('mysqldump')) {
            $config = config('database.connections.'.config('database.default'));
            $result = Process::timeout(600)
                ->env([
                    'MYSQL_PWD' => (string) ($config['password'] ?? ''),
                ])
                ->run([
                    'mysqldump',
                    '--user='.(string) ($config['username'] ?? ''),
                    '--host='.(string) ($config['host'] ?? '127.0.0.1'),
                    '--port='.(string) ($config['port'] ?? 3306),
                    '--single-transaction',
                    '--routines',
                    '--triggers',
                    '--result-file='.$target,
                    (string) ($config['database'] ?? ''),
                ]);

            if ($result->successful() && File::exists($target) && File::size($target) > 0) {
                $method = 'mysqldump';

                return $this->maybeCompress($target);
            }
        }

        $method = 'php';
        $this->writePhpSqlDump($target, 'mysql');

        return $this->maybeCompress($target);
    }

    protected function restoreMysql(string $path): void
    {
        $sqlPath = $this->ensureUncompressedSql($path);
        $config = config('database.connections.'.config('database.default'));

        if ($this->binaryExists('mysql')) {
            $result = Process::timeout(600)
                ->env(['MYSQL_PWD' => (string) ($config['password'] ?? '')])
                ->input(File::get($sqlPath))
                ->run([
                    'mysql',
                    '--user='.(string) ($config['username'] ?? ''),
                    '--host='.(string) ($config['host'] ?? '127.0.0.1'),
                    '--port='.(string) ($config['port'] ?? 3306),
                    (string) ($config['database'] ?? ''),
                ]);

            if ($result->successful()) {
                $this->cleanupTemp($sqlPath, $path);

                return;
            }
        }

        $this->runPhpSqlFile($sqlPath);
        $this->cleanupTemp($sqlPath, $path);
    }

    protected function backupPgsql(string $basename, string &$method): string
    {
        $target = $this->directory().DIRECTORY_SEPARATOR.$basename.'.sql';

        if (config('security-backup.prefer_native', true) && $this->binaryExists('pg_dump')) {
            $config = config('database.connections.'.config('database.default'));
            $result = Process::timeout(600)
                ->env(['PGPASSWORD' => (string) ($config['password'] ?? '')])
                ->run([
                    'pg_dump',
                    '--no-owner',
                    '--no-acl',
                    '--clean',
                    '--if-exists',
                    '--host='.(string) ($config['host'] ?? '127.0.0.1'),
                    '--port='.(string) ($config['port'] ?? 5432),
                    '--username='.(string) ($config['username'] ?? ''),
                    '--dbname='.(string) ($config['database'] ?? ''),
                    '--file='.$target,
                ]);

            if ($result->successful() && File::exists($target) && File::size($target) > 0) {
                $method = 'pg_dump';

                return $this->maybeCompress($target);
            }
        }

        $method = 'php';
        $this->writePhpSqlDump($target, 'pgsql');

        return $this->maybeCompress($target);
    }

    protected function restorePgsql(string $path): void
    {
        $sqlPath = $this->ensureUncompressedSql($path);
        $config = config('database.connections.'.config('database.default'));

        if ($this->binaryExists('psql')) {
            $result = Process::timeout(600)
                ->env(['PGPASSWORD' => (string) ($config['password'] ?? '')])
                ->run([
                    'psql',
                    '--host='.(string) ($config['host'] ?? '127.0.0.1'),
                    '--port='.(string) ($config['port'] ?? 5432),
                    '--username='.(string) ($config['username'] ?? ''),
                    '--dbname='.(string) ($config['database'] ?? ''),
                    '--file='.$sqlPath,
                ]);

            if ($result->successful()) {
                $this->cleanupTemp($sqlPath, $path);

                return;
            }
        }

        $this->runPhpSqlFile($sqlPath);
        $this->cleanupTemp($sqlPath, $path);
    }

    protected function writePhpSqlDump(string $target, string $driver): void
    {
        $handle = fopen($target, 'wb');

        if ($handle === false) {
            throw new RuntimeException(__('security::backup.errors.write-failed'));
        }

        fwrite($handle, "-- auraerp database backup\n");
        fwrite($handle, '-- driver: '.$driver."\n");
        fwrite($handle, '-- generated: '.now()->toIso8601String()."\n\n");

        if ($driver === 'mysql') {
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");
        } elseif ($driver === 'pgsql') {
            fwrite($handle, "SET session_replication_role = replica;\n\n");
        }

        $tables = Schema::getTableListing();

        foreach ($tables as $table) {
            $quoted = $this->quoteIdentifier($table, $driver);
            fwrite($handle, 'DELETE FROM '.$quoted.";\n");

            $writeRow = function ($row) use ($handle, $driver, $quoted): void {
                $data = (array) $row;
                $columns = array_map(
                    fn ($column): string => $this->quoteIdentifier((string) $column, $driver),
                    array_keys($data)
                );
                $values = array_map(fn ($value): string => $this->quoteValue($value), array_values($data));
                fwrite($handle, 'INSERT INTO '.$quoted.' ('.implode(', ', $columns).') VALUES ('.implode(', ', $values).");\n");
            };

            $columnListing = Schema::getColumnListing($table);

            if (in_array('id', $columnListing, true)) {
                DB::table($table)->orderBy('id')->chunkById(200, function ($rows) use ($writeRow): void {
                    foreach ($rows as $row) {
                        $writeRow($row);
                    }
                });
            } else {
                foreach (DB::table($table)->cursor() as $row) {
                    $writeRow($row);
                }
            }

            fwrite($handle, "\n");
        }

        if ($driver === 'mysql') {
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        } elseif ($driver === 'pgsql') {
            fwrite($handle, "SET session_replication_role = DEFAULT;\n");
        }

        fclose($handle);
    }

    protected function runPhpSqlFile(string $sqlPath): void
    {
        $sql = File::get($sqlPath);
        $statements = preg_split('/;\s*\n/', $sql) ?: [];
        $driver = DB::getDriverName();

        try {
            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
            } elseif ($driver === 'pgsql') {
                DB::statement('SET session_replication_role = replica');
            }

            foreach ($statements as $statement) {
                $statement = trim($statement);

                if ($statement === '' || str_starts_with($statement, '--')) {
                    continue;
                }

                DB::unprepared($statement);
            }
        } finally {
            try {
                if (in_array($driver, ['mysql', 'mariadb'], true)) {
                    DB::statement('SET FOREIGN_KEY_CHECKS=1');
                } elseif ($driver === 'pgsql') {
                    DB::statement('SET session_replication_role = DEFAULT');
                }
            } catch (Throwable) {
            }
        }
    }

    protected function maybeCompress(string $path): string
    {
        if (! config('security-backup.compress', true) || ! function_exists('gzencode')) {
            return $path;
        }

        $compressed = $path.'.gz';
        $data = File::get($path);
        File::put($compressed, gzencode($data, 9));
        File::delete($path);

        return $compressed;
    }

    protected function ensureUncompressedSql(string $path): string
    {
        if (! Str::endsWith($path, '.gz')) {
            return $path;
        }

        $temp = $this->directory().DIRECTORY_SEPARATOR.'restore-'.Str::uuid().'.sql';
        $decoded = gzdecode(File::get($path));

        if ($decoded === false) {
            throw new RuntimeException(__('security::backup.errors.decompress-failed'));
        }

        File::put($temp, $decoded);

        return $temp;
    }

    protected function cleanupTemp(string $sqlPath, string $originalPath): void
    {
        if ($sqlPath !== $originalPath && File::exists($sqlPath)) {
            File::delete($sqlPath);
        }
    }

    protected function binaryExists(string $binary): bool
    {
        $result = Process::run(['which', $binary]);

        return $result->successful() && trim($result->output()) !== '';
    }

    protected function quoteIdentifier(string $identifier, string $driver): string
    {
        return $driver === 'mysql'
            ? '`'.str_replace('`', '``', $identifier).'`'
            : '"'.str_replace('"', '""', $identifier).'"';
    }

    protected function quoteValue(mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return DB::getPdo()->quote((string) $value);
    }

    protected function humanBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = (float) $bytes;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return number_format($size, $unit === 0 ? 0 : 1).' '.$units[$unit];
    }
}
