<?php

namespace Webkul\Support\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Models\Task;
use Webkul\Project\Services\OperationalScope;
use Webkul\Security\Support\AdminAccess;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Services\AccountingTeamInsights;
use Webkul\TimeOff\Enums\State as LeaveState;
use Webkul\TimeOff\Models\Leave;

class MyDayDashboard extends Page
{
    protected string $view = 'filament.pages.my-day-dashboard';

    protected static ?string $slug = 'my-day';

    protected static ?int $navigationSort = -9;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sun';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && method_exists($user, 'hasRole');
    }

    public static function getNavigationLabel(): string
    {
        return str_starts_with((string) app()->getLocale(), 'id') ? 'Hari Saya' : 'My Day';
    }

    public function getTitle(): string|Htmlable
    {
        return static::getNavigationLabel();
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Dashboard;
    }

    /**
     * @return array<string, mixed>
     */
    public function getDayData(): array
    {
        $user = auth()->user();
        $indonesian = str_starts_with((string) app()->getLocale(), 'id');
        $role = $this->primaryRoleLabel($user);
        $scope = app(OperationalScope::class);

        $bundle = match (true) {
            AdminAccess::check($user)                                              => $this->adminBundle($indonesian, $scope),
            method_exists($user, 'hasRole') && ($user->hasRole('Accounting') || $user->hasRole('Accounting Manager')) => $this->accountingBundle($indonesian),
            method_exists($user, 'hasRole') && $user->hasRole('HRD')               => $this->hrdBundle($indonesian, $scope),
            method_exists($user, 'hasRole') && $user->hasRole('Operation Manager') => $this->omBundle($indonesian, $scope),
            method_exists($user, 'hasRole') && $user->hasRole('Kasir')             => $this->kasirBundle($indonesian),
            default                                                                => $this->staffBundle($indonesian, $user),
        };

        $cards = $bundle['cards'];
        $recommendations = $bundle['recommendations'];
        $links = $bundle['links'];

        if ($recommendations === []) {
            $recommendations = $this->fallbackRecommendations($cards, $indonesian);
        }

        return [
            'greeting'         => $indonesian
                ? 'Halo, '.($user->name ?? '').' — fokus hari ini'
                : 'Hi, '.($user->name ?? '').' — today\'s focus',
            'subtitle'         => $indonesian
                ? 'Ringkasan singkat agar Anda langsung tahu apa yang perlu ditindaklanjuti.'
                : 'A short snapshot so you know what needs attention first.',
            'role'             => $role,
            'date_label'       => $this->dateLabel($indonesian),
            'attention_count'  => $this->attentionCount($cards),
            'cards'            => $cards,
            'recommendations'  => $recommendations,
            'links'            => $links,
        ];
    }

    protected function primaryRoleLabel(mixed $user): string
    {
        if (! $user || ! method_exists($user, 'getRoleNames')) {
            return '—';
        }

        return (string) ($user->getRoleNames()->first() ?? '—');
    }

    protected function dateLabel(bool $indonesian): string
    {
        $locale = $indonesian ? 'id_ID' : 'en_US';

        try {
            return now()->locale($locale)->translatedFormat($indonesian ? 'l, d F Y' : 'l, F j, Y');
        } catch (Throwable) {
            return now()->format('Y-m-d');
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $cards
     */
    protected function attentionCount(array $cards): int
    {
        $count = 0;

        foreach ($cards as $card) {
            if (! in_array($card['color'] ?? '', ['danger', 'warning'], true)) {
                continue;
            }

            $value = $card['value'] ?? null;

            if (is_numeric($value) && (float) $value > 0) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @return array{cards: array<int, array<string, mixed>>, recommendations: array<int, array<string, mixed>>, links: array<int, array<string, mixed>>}
     */
    protected function adminBundle(bool $indonesian, OperationalScope $scope): array
    {
        $pendingLeave = $this->pendingLeavesCount($scope);
        $expiring = $this->expiringContractsCount($scope);
        $notClocked = $this->notClockedInToday();
        $withoutUser = $this->withoutUserCount($scope);

        $cards = [
            $this->card(
                $indonesian ? 'Cuti menunggu' : 'Pending leave',
                (string) $pendingLeave,
                url('/admin/time-off/time-offs'),
                'warning',
                $indonesian ? 'Perlu konfirmasi / validasi level-1' : 'Needs confirm / level-1 validation',
                'leave',
            ),
            $this->card(
                $indonesian ? 'Kontrak ≤ 30 hari' : 'Contracts ≤ 30 days',
                (string) $expiring,
                url('/admin/employees/employees'),
                'danger',
                $indonesian ? 'Segera tinjau perpanjangan / offboarding' : 'Review extension or offboarding soon',
                'contract',
            ),
            $this->card(
                $indonesian ? 'Belum absen hari ini' : 'Not clocked in today',
                (string) $notClocked,
                url('/admin/attendances'),
                'gray',
                $indonesian ? 'Karyawan aktif tanpa check-in' : 'Active employees without check-in',
                'attendance',
            ),
            $this->card(
                $indonesian ? 'Tanpa Related User' : 'Without Related User',
                (string) $withoutUser,
                url('/admin/project/assign-employees'),
                'info',
                $indonesian ? 'Belum bisa login ke proyek' : 'Cannot log in to projects yet',
                'user',
            ),
        ];

        $recommendations = [];

        if ($expiring > 0) {
            $recommendations[] = $this->recommendation(
                $indonesian ? 'Tinjau kontrak yang akan berakhir' : 'Review contracts ending soon',
                $indonesian
                    ? "Ada {$expiring} kontrak berakhir ≤ 30 hari. Prioritaskan keputusan perpanjang atau offboarding."
                    : "{$expiring} contract(s) end within 30 days. Prioritize renew or offboard decisions.",
                url('/admin/employees/employees'),
                'danger',
                $indonesian ? 'Buka karyawan' : 'Open employees',
            );
        }

        if ($pendingLeave > 0) {
            $recommendations[] = $this->recommendation(
                $indonesian ? 'Proses cuti menunggu' : 'Clear pending leave',
                $indonesian
                    ? "{$pendingLeave} pengajuan cuti menunggu tindakan agar jadwal tim tidak tertahan."
                    : "{$pendingLeave} leave request(s) waiting so team schedules stay clear.",
                url('/admin/time-off/time-offs'),
                'warning',
                $indonesian ? 'Buka cuti' : 'Open time off',
            );
        }

        if ($withoutUser > 0) {
            $recommendations[] = $this->recommendation(
                $indonesian ? 'Hubungkan Related User' : 'Link Related Users',
                $indonesian
                    ? "{$withoutUser} karyawan di proyek belum punya akun login. Tetapkan Related User agar mereka bisa masuk."
                    : "{$withoutUser} project employee(s) still lack a login account. Assign Related User access.",
                url('/admin/project/assign-employees'),
                'info',
                $indonesian ? 'Assign sekarang' : 'Assign now',
            );
        }

        if ($notClocked > 0) {
            $recommendations[] = $this->recommendation(
                $indonesian ? 'Monitor absensi hari ini' : 'Monitor today\'s attendance',
                $indonesian
                    ? "{$notClocked} karyawan aktif belum check-in. Cek anomali atau site yang belum merekam."
                    : "{$notClocked} active employee(s) have not checked in. Spot anomalies early.",
                url('/admin/attendances'),
                'gray',
                $indonesian ? 'Lihat absensi' : 'View attendance',
            );
        }

        if (WelcomeDashboard::canAccess()) {
            $recommendations[] = $this->recommendation(
                $indonesian ? 'Lihat Welcome Overview' : 'Open Welcome Overview',
                $indonesian
                    ? 'Untuk gambaran eksekutif (proyek, revenue, peta operasional), buka Welcome Overview.'
                    : 'For the executive snapshot (projects, revenue, ops map), open Welcome Overview.',
                WelcomeDashboard::getUrl(panel: 'admin'),
                'primary',
                $indonesian ? 'Buka Welcome' : 'Open Welcome',
            );
        }

        return [
            'cards'           => $cards,
            'recommendations' => array_slice($recommendations, 0, 4),
            'links'           => [
                $this->link($indonesian ? 'Welcome Overview' : 'Welcome Overview', url('/admin/welcome'), 'primary'),
                $this->link($indonesian ? 'Proyek' : 'Projects', url('/admin/project'), 'info'),
                $this->link($indonesian ? 'Karyawan' : 'Employees', url('/admin/employees/employees'), 'success'),
                $this->link($indonesian ? 'Evaluasi' : 'Evaluations', url('/admin/evaluations'), 'warning'),
            ],
        ];
    }

    /**
     * @return array{cards: array<int, array<string, mixed>>, recommendations: array<int, array<string, mixed>>, links: array<int, array<string, mixed>>}
     */
    protected function hrdBundle(bool $indonesian, OperationalScope $scope): array
    {
        $admin = $this->adminBundle($indonesian, $scope);

        return [
            'cards'           => $admin['cards'],
            'recommendations' => array_values(array_filter(
                $admin['recommendations'],
                fn (array $item): bool => ! str_contains(mb_strtolower($item['title']), 'welcome'),
            )),
            'links'           => [
                $this->link($indonesian ? 'Karyawan' : 'Employees', url('/admin/employees/employees'), 'primary'),
                $this->link($indonesian ? 'Cuti' : 'Time off', url('/admin/time-off/time-offs'), 'warning'),
                $this->link($indonesian ? 'Absensi' : 'Attendance', url('/admin/attendances'), 'info'),
                $this->link($indonesian ? 'Evaluasi' : 'Evaluations', url('/admin/evaluations'), 'success'),
            ],
        ];
    }

    /**
     * @return array{cards: array<int, array<string, mixed>>, recommendations: array<int, array<string, mixed>>, links: array<int, array<string, mixed>>}
     */
    protected function omBundle(bool $indonesian, OperationalScope $scope): array
    {
        $overdue = $this->overdueTasksCount($scope);
        $pendingLeave = $this->pendingLeavesCount($scope);
        $projects = count($scope->projectIds());
        $withoutUser = $this->withoutUserCount($scope);

        $cards = [
            $this->card(
                $indonesian ? 'Tugas terlambat (scope)' : 'Overdue tasks (scope)',
                (string) $overdue,
                url('/admin/project/tasks'),
                'danger',
                $indonesian ? 'Lewat deadline & belum selesai' : 'Past deadline and not done',
                'task',
            ),
            $this->card(
                $indonesian ? 'Cuti menunggu (scope)' : 'Pending leave (scope)',
                (string) $pendingLeave,
                url('/admin/time-off/time-offs'),
                'warning',
                $indonesian ? 'Dalam cakupan operasional Anda' : 'Within your operational scope',
                'leave',
            ),
            $this->card(
                $indonesian ? 'Proyek saya' : 'My projects',
                (string) $projects,
                url('/admin/project/projects'),
                'primary',
                $indonesian ? 'Proyek dalam scope OM' : 'Projects in OM scope',
                'project',
            ),
            $this->card(
                $indonesian ? 'Tanpa Related User' : 'Without Related User',
                (string) $withoutUser,
                url('/admin/project/assign-employees'),
                'gray',
                $indonesian ? 'Belum bisa login ke proyek' : 'Cannot log in to projects yet',
                'user',
            ),
        ];

        $recommendations = [];

        if ($overdue > 0) {
            $recommendations[] = $this->recommendation(
                $indonesian ? 'Kejar tugas terlambat' : 'Chase overdue tasks',
                $indonesian
                    ? "{$overdue} tugas di scope Anda sudah lewat deadline. Mulai dari yang paling kritis."
                    : "{$overdue} task(s) in your scope are past deadline. Start with the most critical.",
                url('/admin/project/tasks'),
                'danger',
                $indonesian ? 'Buka tugas' : 'Open tasks',
            );
        }

        if ($pendingLeave > 0) {
            $recommendations[] = $this->recommendation(
                $indonesian ? 'Validasi cuti tim' : 'Validate team leave',
                $indonesian
                    ? "{$pendingLeave} cuti menunggu agar kapasitas site tetap terencana."
                    : "{$pendingLeave} leave request(s) waiting so site capacity stays planned.",
                url('/admin/time-off/time-offs'),
                'warning',
                $indonesian ? 'Buka cuti' : 'Open time off',
            );
        }

        if ($withoutUser > 0) {
            $recommendations[] = $this->recommendation(
                $indonesian ? 'Lengkapi Related User' : 'Complete Related Users',
                $indonesian
                    ? "{$withoutUser} anggota proyek belum punya akun."
                    : "{$withoutUser} project member(s) still need a login.",
                url('/admin/project/assign-employees'),
                'info',
                $indonesian ? 'Assign' : 'Assign',
            );
        }

        return [
            'cards'           => $cards,
            'recommendations' => $recommendations,
            'links'           => [
                $this->link($indonesian ? 'Dashboard proyek' : 'Project dashboard', url('/admin/project'), 'primary'),
                $this->link($indonesian ? 'Tugas' : 'Tasks', url('/admin/project/tasks'), 'danger'),
                $this->link($indonesian ? 'Assign karyawan' : 'Assign employees', url('/admin/project/assign-employees'), 'info'),
                $this->link($indonesian ? 'Absensi' : 'Attendance', url('/admin/attendances'), 'gray'),
            ],
        ];
    }

    /**
     * @return array{cards: array<int, array<string, mixed>>, recommendations: array<int, array<string, mixed>>, links: array<int, array<string, mixed>>}
     */
    protected function staffBundle(bool $indonesian, mixed $user): array
    {
        $myOpenTasks = 0;
        $myTimesheetHours = 0.0;

        try {
            if ($user && Schema::hasTable('projects_tasks')) {
                $myOpenTasks = Task::query()
                    ->whereHas('users', fn ($q) => $q->where('users.id', $user->id))
                    ->whereNotIn('state', [TaskState::DONE->value, TaskState::CANCELLED->value, TaskState::APPROVED->value])
                    ->count();
            }

            if ($user && Schema::hasTable('analytic_records')) {
                $myTimesheetHours = (float) DB::table('analytic_records')
                    ->where('user_id', $user->id)
                    ->whereDate('date', now()->toDateString())
                    ->sum('unit_amount');
            }
        } catch (Throwable) {
        }

        $cards = [
            $this->card(
                $indonesian ? 'Tugas terbuka saya' : 'My open tasks',
                (string) $myOpenTasks,
                url('/admin/project/tasks'),
                'primary',
                $indonesian ? 'Yang masih aktif di board Anda' : 'Still active on your board',
                'task',
            ),
            $this->card(
                $indonesian ? 'Jam timesheet hari ini' : 'Timesheet hours today',
                number_format($myTimesheetHours, 1),
                url('/admin/timesheets'),
                'success',
                $indonesian ? 'Total jam tercatat hari ini' : 'Logged hours for today',
                'clock',
            ),
            $this->card(
                $indonesian ? 'Absensi' : 'Attendance',
                $indonesian ? 'Catat kehadiran' : 'Clock in/out',
                url('/admin/absen'),
                'info',
                $indonesian ? 'Check-in / check-out harian' : 'Daily check-in / check-out',
                'attendance',
            ),
            $this->card(
                $indonesian ? 'Cuti saya' : 'My time off',
                '→',
                url('/admin/time-off/my-time-offs'),
                'gray',
                $indonesian ? 'Ajukan atau lihat status cuti' : 'Request or track leave',
                'leave',
            ),
        ];

        $recommendations = [];

        if ($myOpenTasks > 0) {
            $recommendations[] = $this->recommendation(
                $indonesian ? 'Fokus selesaikan tugas terbuka' : 'Focus on open tasks',
                $indonesian
                    ? "Anda punya {$myOpenTasks} tugas terbuka. Mulai dari yang mendekati deadline."
                    : "You have {$myOpenTasks} open task(s). Start with the nearest deadline.",
                url('/admin/project/tasks'),
                'primary',
                $indonesian ? 'Buka tugas' : 'Open tasks',
            );
        }

        if ($myTimesheetHours <= 0) {
            $recommendations[] = $this->recommendation(
                $indonesian ? 'Isi timesheet hari ini' : 'Log today\'s timesheet',
                $indonesian
                    ? 'Belum ada jam tercatat hari ini. Catat pekerjaan agar progress terukur.'
                    : 'No hours logged yet today. Capture work so progress stays measurable.',
                url('/admin/timesheets'),
                'success',
                $indonesian ? 'Isi timesheet' : 'Log timesheet',
            );
        }

        $recommendations[] = $this->recommendation(
            $indonesian ? 'Pastikan sudah absen' : 'Make sure you clocked in',
            $indonesian
                ? 'Cek status absensi Anda sebelum memulai pekerjaan lapangan / kantor.'
                : 'Confirm your attendance status before starting field or office work.',
            url('/admin/absen'),
            'info',
            $indonesian ? 'Buka absensi' : 'Open attendance',
        );

        return [
            'cards'           => $cards,
            'recommendations' => array_slice($recommendations, 0, 3),
            'links'           => [
                $this->link($indonesian ? 'Tugas saya' : 'My tasks', url('/admin/project/tasks'), 'primary'),
                $this->link($indonesian ? 'Timesheet' : 'Timesheets', url('/admin/timesheets'), 'success'),
                $this->link($indonesian ? 'Absensi' : 'Attendance', url('/admin/absen'), 'info'),
                $this->link($indonesian ? 'Cuti' : 'Time off', url('/admin/time-off/my-time-offs'), 'gray'),
            ],
        ];
    }

    /**
     * @return array{cards: array<int, array<string, mixed>>, recommendations: array<int, array<string, mixed>>, links: array<int, array<string, mixed>>}
     */
    protected function kasirBundle(bool $indonesian): array
    {
        return [
            'cards'           => [
                $this->card(
                    $indonesian ? 'POS Terminal' : 'POS Terminal',
                    '→',
                    url('/admin/pos-terminal'),
                    'primary',
                    $indonesian ? 'Mulai transaksi kasir' : 'Start cashier transactions',
                    'money',
                ),
                $this->card(
                    $indonesian ? 'Absensi' : 'Attendance',
                    '→',
                    url('/admin/absen'),
                    'info',
                    $indonesian ? 'Catat kehadiran shift' : 'Log shift attendance',
                    'attendance',
                ),
            ],
            'recommendations' => [
                $this->recommendation(
                    $indonesian ? 'Siapkan shift kasir' : 'Prepare the cashier shift',
                    $indonesian
                        ? 'Buka POS Terminal dan pastikan absensi shift sudah tercatat sebelum melayani pelanggan.'
                        : 'Open POS Terminal and confirm shift attendance before serving customers.',
                    url('/admin/pos-terminal'),
                    'primary',
                    $indonesian ? 'Buka POS' : 'Open POS',
                ),
            ],
            'links'           => [
                $this->link($indonesian ? 'POS Terminal' : 'POS Terminal', url('/admin/pos-terminal'), 'primary'),
                $this->link($indonesian ? 'Absensi' : 'Attendance', url('/admin/absen'), 'info'),
            ],
        ];
    }

    /**
     * @return array{cards: array<int, array<string, mixed>>, recommendations: array<int, array<string, mixed>>, links: array<int, array<string, mixed>>}
     */
    protected function accountingBundle(bool $indonesian): array
    {
        $summary = app(AccountingTeamInsights::class)->summary();
        $scopeHint = $summary['scoped']
            ? ($indonesian ? ' (tim saya)' : ' (my team)')
            : ($indonesian ? ' (perusahaan)' : ' (company)');

        $cards = [
            $this->card(
                ($indonesian ? 'Invoice terbuka' : 'Open invoices').$scopeHint,
                (string) $summary['open_invoices'],
                $this->safeUrl([
                    'Webkul\\Invoice\\Filament\\Clusters\\Customers\\Resources\\InvoiceResource',
                ], '/admin/invoices/customers/invoices'),
                'primary',
                $indonesian ? 'Menunggu pelunasan / follow-up' : 'Awaiting collection / follow-up',
                'money',
            ),
            $this->card(
                ($indonesian ? 'Overdue AR' : 'Overdue AR').$scopeHint,
                (string) $summary['overdue_ar'],
                $this->safeUrl([
                    'Webkul\\Invoice\\Filament\\Clusters\\Customers\\Resources\\InvoiceResource',
                ], '/admin/invoices/customers/invoices'),
                'danger',
                $indonesian ? 'Piutang lewat jatuh tempo' : 'Receivables past due',
                'money',
            ),
            $this->card(
                ($indonesian ? 'Overdue AP' : 'Overdue AP').$scopeHint,
                (string) $summary['overdue_ap'],
                $this->safeUrl([
                    'Webkul\\Invoice\\Filament\\Clusters\\Vendors\\Resources\\BillResource',
                ], '/admin/invoices/vendors/bills'),
                'warning',
                $indonesian ? 'Utang vendor lewat jatuh tempo' : 'Payables past due',
                'money',
            ),
            $this->card(
                ($indonesian ? 'Payment pending' : 'Pending payments').$scopeHint,
                (string) $summary['payments_pending'],
                $this->safeUrl([
                    'Webkul\\Invoice\\Filament\\Clusters\\Customers\\Resources\\PaymentResource',
                ], '/admin/invoices/customers/payments'),
                'info',
                $indonesian ? 'Pembayaran belum selesai diproses' : 'Payments still in process',
                'clock',
            ),
            $this->card(
                ($indonesian ? 'Budget proyek tim' : 'Team project budget').$scopeHint,
                'Rp '.number_format($summary['team_budget'], 0, ',', '.'),
                url('/admin/project/projects'),
                'success',
                $indonesian ? 'Nilai proyek dalam cakupan tim' : 'Project value in team scope',
                'project',
            ),
        ];

        $recommendations = [];

        if (($summary['overdue_ar'] ?? 0) > 0) {
            $recommendations[] = $this->recommendation(
                $indonesian ? 'Follow-up Overdue AR' : 'Follow up Overdue AR',
                $indonesian
                    ? 'Ada piutang lewat jatuh tempo. Prioritaskan penagihan hari ini.'
                    : 'Receivables are past due. Prioritize collection today.',
                $this->safeUrl([
                    'Webkul\\Invoice\\Filament\\Clusters\\Customers\\Resources\\InvoiceResource',
                ], '/admin/invoices/customers/invoices'),
                'danger',
                $indonesian ? 'Buka invoice' : 'Open invoices',
            );
        }

        if (($summary['overdue_ap'] ?? 0) > 0) {
            $recommendations[] = $this->recommendation(
                $indonesian ? 'Jadwalkan pembayaran AP' : 'Schedule AP payments',
                $indonesian
                    ? 'Ada utang vendor yang sudah jatuh tempo. Hindari denda / gangguan supply.'
                    : 'Vendor payables are overdue. Avoid penalties or supply disruption.',
                $this->safeUrl([
                    'Webkul\\Invoice\\Filament\\Clusters\\Vendors\\Resources\\BillResource',
                ], '/admin/invoices/vendors/bills'),
                'warning',
                $indonesian ? 'Buka bills' : 'Open bills',
            );
        }

        return [
            'cards'           => $cards,
            'recommendations' => $recommendations,
            'links'           => [
                $this->link($indonesian ? 'Invoice' : 'Invoices', $this->safeUrl([
                    'Webkul\\Invoice\\Filament\\Clusters\\Customers\\Resources\\InvoiceResource',
                ], '/admin/invoices/customers/invoices'), 'primary'),
                $this->link($indonesian ? 'Vendor bills' : 'Vendor bills', $this->safeUrl([
                    'Webkul\\Invoice\\Filament\\Clusters\\Vendors\\Resources\\BillResource',
                ], '/admin/invoices/vendors/bills'), 'warning'),
                $this->link($indonesian ? 'Payments' : 'Payments', $this->safeUrl([
                    'Webkul\\Invoice\\Filament\\Clusters\\Customers\\Resources\\PaymentResource',
                ], '/admin/invoices/customers/payments'), 'info'),
                $this->link($indonesian ? 'Proyek' : 'Projects', url('/admin/project/projects'), 'success'),
            ],
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $cards
     * @return array<int, array<string, mixed>>
     */
    protected function fallbackRecommendations(array $cards, bool $indonesian): array
    {
        foreach ($cards as $card) {
            $value = $card['value'] ?? null;

            if (is_numeric($value) && (float) $value > 0) {
                return [
                    $this->recommendation(
                        $indonesian ? 'Tindaklanjuti metrik utama' : 'Act on your top metric',
                        $indonesian
                            ? 'Mulai dari kartu dengan angka tertinggi atau status peringatan.'
                            : 'Start with the highest number or warning-status card.',
                        (string) ($card['href'] ?? '#'),
                        (string) ($card['color'] ?? 'primary'),
                        $indonesian ? 'Buka' : 'Open',
                    ),
                ];
            }
        }

        return [
            $this->recommendation(
                $indonesian ? 'Hari ini relatif aman' : 'Today looks clear',
                $indonesian
                    ? 'Tidak ada item mendesak dari ringkasan Anda. Manfaatkan waktu untuk review berkala.'
                    : 'No urgent items in your snapshot. Use the time for a routine review.',
                (string) ($cards[0]['href'] ?? url('/admin/my-day')),
                'success',
                $indonesian ? 'Tetap pantau' : 'Keep monitoring',
            ),
        ];
    }

    /**
     * @param  array<int, class-string>  $resourceClasses
     */
    protected function safeUrl(array $resourceClasses, string $fallback): string
    {
        foreach ($resourceClasses as $class) {
            try {
                if (class_exists($class) && method_exists($class, 'getUrl')) {
                    return $class::getUrl('index');
                }
            } catch (Throwable) {
            }
        }

        return url($fallback);
    }

    /**
     * @return array<string, mixed>
     */
    protected function card(
        string $label,
        string $value,
        string $href,
        string $color,
        ?string $hint = null,
        string $icon = 'chart',
    ): array {
        return compact('label', 'value', 'href', 'color', 'hint', 'icon');
    }

    /**
     * @return array<string, mixed>
     */
    protected function recommendation(
        string $title,
        string $body,
        string $href,
        string $color,
        string $cta,
    ): array {
        return compact('title', 'body', 'href', 'color', 'cta');
    }

    /**
     * @return array<string, mixed>
     */
    protected function link(string $label, string $href, string $color): array
    {
        return compact('label', 'href', 'color');
    }

    protected function pendingLeavesCount(OperationalScope $scope): int
    {
        try {
            if (! class_exists(Leave::class) || ! Schema::hasTable('time_off_leaves')) {
                return 0;
            }

            $query = Leave::query()->whereIn('state', [LeaveState::CONFIRM->value, LeaveState::VALIDATE_ONE->value]);
            $employeeIds = $scope->employeeIdsInScope();

            if (! $scope->isUnscoped() && $employeeIds->isNotEmpty()) {
                $query->whereIn('employee_id', $employeeIds->all());
            } elseif (! $scope->isUnscoped()) {
                return 0;
            }

            return $query->count();
        } catch (Throwable) {
            return 0;
        }
    }

    protected function expiringContractsCount(OperationalScope $scope): int
    {
        try {
            if (! class_exists(Employee::class) || ! Schema::hasTable('employees_employees')) {
                return 0;
            }

            $query = Employee::query()
                ->whereNotNull('contract_end_date')
                ->whereDate('contract_end_date', '>=', now()->toDateString())
                ->whereDate('contract_end_date', '<=', now()->addDays(30)->toDateString());

            $employeeIds = $scope->employeeIdsInScope();

            if (! $scope->isUnscoped() && $employeeIds->isNotEmpty()) {
                $query->whereIn('id', $employeeIds->all());
            } elseif (! $scope->isUnscoped()) {
                return 0;
            }

            return $query->count();
        } catch (Throwable) {
            return 0;
        }
    }

    protected function overdueTasksCount(OperationalScope $scope): int
    {
        try {
            if (! Schema::hasTable('projects_tasks')) {
                return 0;
            }

            $query = Task::query()
                ->whereNotNull('deadline')
                ->where('deadline', '<', now())
                ->whereNotIn('state', [TaskState::DONE->value, TaskState::CANCELLED->value, TaskState::APPROVED->value]);

            $projectIds = $scope->projectIds();

            if (! $scope->isUnscoped()) {
                if ($projectIds === []) {
                    return 0;
                }

                $query->whereIn('project_id', $projectIds);
            }

            return $query->count();
        } catch (Throwable) {
            return 0;
        }
    }

    protected function withoutUserCount(OperationalScope $scope): int
    {
        try {
            if (! Schema::hasTable('projects_project_employees')) {
                return 0;
            }

            $query = Employee::query()->whereNull('user_id')->whereHas('projects');
            $employeeIds = $scope->employeeIdsInScope();

            if (! $scope->isUnscoped() && $employeeIds->isNotEmpty()) {
                $query->whereIn('id', $employeeIds->all());
            } elseif (! $scope->isUnscoped()) {
                return 0;
            }

            return $query->count();
        } catch (Throwable) {
            return 0;
        }
    }

    protected function notClockedInToday(): int
    {
        try {
            if (! Schema::hasTable('attendances_attendances') || ! Schema::hasTable('employees_employees')) {
                return 0;
            }

            $active = Employee::query()->where('is_active', true)->count();
            $clocked = DB::table('attendances_attendances')
                ->whereDate('check_in', now()->toDateString())
                ->distinct('employee_id')
                ->count('employee_id');

            return max(0, $active - $clocked);
        } catch (Throwable) {
            return 0;
        }
    }
}
