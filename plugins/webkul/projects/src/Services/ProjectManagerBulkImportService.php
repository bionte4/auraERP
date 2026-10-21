<?php

namespace Webkul\Project\Services;

use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\User;

/**
 * Dedicated bulk assignment of Project Managers.
 *
 * The template is prefilled with every current project and its
 * current manager email; admins edit the `new_manager_email` column
 * (leave empty to clear the manager) and upload the file back.
 * Projects are matched by `project_id` (falling back to an exact
 * name match) and managers by email — only users with the
 * Operation Manager role are accepted.
 */
class ProjectManagerBulkImportService
{
    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'project_id',
            'project_name',
            'customer',
            'current_manager_email',
            'new_manager_email',
        ];
    }

    /**
     * Current state of every project, used to prefill the template.
     * The new-manager column starts as a copy of the current one so
     * untouched rows import back as no-ops.
     *
     * @return array<int, array<int, mixed>>
     */
    public function templateRows(): array
    {
        return Project::query()
            ->with([
                'partner' => fn ($query) => $query->select('id', 'name'),
                'user'    => fn ($query) => $query->select('id', 'name', 'email'),
            ])
            ->orderBy('name')
            ->get()
            ->map(fn (Project $project): array => [
                $project->id,
                $project->name,
                $project->partner?->name ?? '',
                $project->user?->email ?? '',
                $project->user?->email ?? '',
            ])
            ->all();
    }

    /**
     * Operation Manager directory for the template's second sheet,
     * so admins can copy-paste valid manager emails.
     *
     * @return array<int, array<int, mixed>>
     */
    public function managerRows(): array
    {
        return User::query()
            ->whereHas('roles', fn ($query) => $query
                ->where('name', 'Operation Manager')
                ->where('guard_name', 'web'))
            ->orderBy('name')
            ->get(['name', 'email'])
            ->map(fn (User $user): array => [$user->name, $user->email])
            ->all();
    }

    public function downloadTemplate(): BinaryFileResponse
    {
        $assignments = new class($this->headings(), $this->templateRows()) implements FromArray, WithHeadings
        {
            /**
             * @param  array<int, string>  $headings
             * @param  array<int, array<int, mixed>>  $rows
             */
            public function __construct(
                protected array $headings,
                protected array $rows,
            ) {}

            public function headings(): array
            {
                return $this->headings;
            }

            public function array(): array
            {
                return $this->rows;
            }
        };

        $managers = new class($this->managerRows()) implements FromArray, WithHeadings
        {
            /**
             * @param  array<int, array<int, mixed>>  $rows
             */
            public function __construct(protected array $rows) {}

            public function headings(): array
            {
                return ['manager_name', 'manager_email'];
            }

            public function array(): array
            {
                return $this->rows;
            }
        };

        $export = new class($assignments, $managers) implements WithMultipleSheets
        {
            public function __construct(
                protected object $assignments,
                protected object $managers,
            ) {}

            public function sheets(): array
            {
                return [$this->assignments, $this->managers];
            }
        };

        return Excel::download($export, 'template-assign-project-managers.xlsx');
    }

    /**
     * @return array{updated: int, unchanged: int, skipped: int, errors: array<int, string>}
     */
    public function importFromPath(string $filePath): array
    {
        $sheets = Excel::toArray(new class {}, $filePath);
        $rows = $sheets[0] ?? [];

        return $this->importRows($rows);
    }

    /**
     * @param  array<int, array<int|string, mixed>>  $rows
     * @return array{updated: int, unchanged: int, skipped: int, errors: array<int, string>}
     */
    public function importRows(array $rows): array
    {
        $result = ['updated' => 0, 'unchanged' => 0, 'skipped' => 0, 'errors' => []];

        if ($rows === [] || ! Schema::hasTable('projects_projects')) {
            return $result;
        }

        $headerMap = [];
        $firstRow = $rows[0];
        $isAssoc = array_keys($firstRow) !== range(0, count($firstRow) - 1);

        if (! $isAssoc) {
            foreach ($firstRow as $idx => $colName) {
                $headerMap[$idx] = strtolower(trim((string) $colName));
            }
            array_shift($rows);
        }

        $usersByEmail = User::query()
            ->get(['id', 'email'])
            ->keyBy(fn (User $user): string => mb_strtolower(trim((string) $user->email)));

        $managerIds = User::query()
            ->whereHas('roles', fn ($query) => $query
                ->where('name', 'Operation Manager')
                ->where('guard_name', 'web'))
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        $synchronizer = app(TeamMembershipSynchronizer::class);

        foreach (array_values($rows) as $index => $rawRow) {
            $line = $index + 2;
            $row = $isAssoc ? $this->normalizeAssocRow($rawRow) : $this->mapIndexedRow($rawRow, $headerMap);

            $project = $this->resolveProject($row);

            if (! $project) {
                $label = trim((string) ($row['project_name'] ?? $row['project_id'] ?? ''));
                $result['skipped']++;
                $result['errors'][] = "Line {$line}: project \"{$label}\" not found.";

                continue;
            }

            $email = mb_strtolower(trim((string) ($row['new_manager_email'] ?? '')));

            if ($email === '') {
                $newManagerId = null;
            } else {
                $manager = $usersByEmail->get($email);

                if (! $manager) {
                    $result['skipped']++;
                    $result['errors'][] = "Line {$line}: user with email {$email} not found.";

                    continue;
                }

                if (! in_array((int) $manager->id, $managerIds, true)) {
                    $result['skipped']++;
                    $result['errors'][] = "Line {$line}: {$email} is not an Operation Manager.";

                    continue;
                }

                $newManagerId = (int) $manager->id;
            }

            if ((int) ($project->user_id ?? 0) === (int) ($newManagerId ?? 0)) {
                $result['unchanged']++;

                continue;
            }

            $project->forceFill(['user_id' => $newManagerId])->save();

            try {
                $synchronizer->syncForProject($project);
            } catch (\Throwable) {
                // Team sync is best-effort; the assignment itself stands.
            }

            $result['updated']++;
        }

        return $result;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    protected function resolveProject(array $row): ?Project
    {
        $projectId = (int) ($row['project_id'] ?? 0);

        if ($projectId > 0) {
            $project = Project::query()->find($projectId);

            if ($project) {
                return $project;
            }
        }

        $name = trim((string) ($row['project_name'] ?? ''));

        if ($name === '') {
            return null;
        }

        return Project::query()
            ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($name)])
            ->first();
    }

    /**
     * @param  array<int|string, mixed>  $rawRow
     * @param  array<int, string>  $headerMap
     * @return array<string, mixed>
     */
    protected function mapIndexedRow(array $rawRow, array $headerMap): array
    {
        $row = [];

        foreach ($rawRow as $idx => $value) {
            $key = $headerMap[$idx] ?? null;

            if ($key !== null && $key !== '') {
                $row[$this->canonicalKey($key)] = $value;
            }
        }

        return $row;
    }

    /**
     * @param  array<int|string, mixed>  $rawRow
     * @return array<string, mixed>
     */
    protected function normalizeAssocRow(array $rawRow): array
    {
        $row = [];

        foreach ($rawRow as $key => $value) {
            $row[$this->canonicalKey(strtolower(trim((string) $key)))] = $value;
        }

        return $row;
    }

    protected function canonicalKey(string $key): string
    {
        return match ($key) {
            'id' => 'project_id',
            'name', 'nama', 'proyek', 'project' => 'project_name',
            'manager_email', 'email_pm', 'pm_email', 'email_manajer' => 'new_manager_email',
            default => $key,
        };
    }
}
