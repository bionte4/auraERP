<?php

namespace Webkul\Attendance\Filament\Pages;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Auth\Authenticatable;
use Livewire\Attributes\Renderless;
use Webkul\Attendance\Data\AttendanceLocation;
use Webkul\Attendance\Enums\ClockResult;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Models\AttendanceSite;
use Webkul\Attendance\Models\FaceProfile;
use Webkul\Attendance\Services\AttendanceClock;
use Webkul\Attendance\Services\AttendanceGeofence;
use Webkul\Attendance\Services\AttendanceTracker;
use Webkul\Attendance\Services\FaceMatcher;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Enums\NavigationGroup;

class ClockPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-device-phone-mobile';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'absen';

    protected string $view = 'attendances::filament.pages.clock';

    public static function getNavigationParentItem(): ?string
    {
        return __('attendances::filament/resources/attendance.navigation.title');
    }

    public bool $requiresFace = false;

    public function mount(): void
    {
        $this->requiresFace = $this->requestLooksLikePhone();
    }

    public function hydrate(): void
    {
        if ($this->requestLooksLikePhone()) {
            $this->requiresFace = true;
        }
    }

    public static function getNavigationLabel(): string
    {
        return __('attendances::filament/pages/clock.navigation');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Employee;
    }

    public function getTitle(): string
    {
        return __('attendances::filament/pages/clock.title');
    }

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        if ($user === null) {
            return false;
        }

        return $user->can('page_attendance_clock_page')
            || $user->can('view_any_attendance_attendance')
            || $user->can('create_attendance_attendance');
    }

    public function clockIn(mixed $latitude = null, mixed $longitude = null, mixed $accuracy = null, mixed $descriptor = null): bool
    {
        return $this->record('in', $latitude, $longitude, $accuracy, $descriptor);
    }

    public function clockOut(mixed $latitude = null, mixed $longitude = null, mixed $accuracy = null, mixed $descriptor = null): bool
    {
        return $this->record('out', $latitude, $longitude, $accuracy, $descriptor);
    }

    #[Renderless]
    public function track(mixed $latitude = null, mixed $longitude = null, mixed $accuracy = null): int
    {
        $user = Filament::auth()->user();
        $employee = $this->employee();

        if ($user === null || ! $user->can('create_attendance_attendance') || ! $employee) {
            return 0;
        }

        $attendance = Attendance::query()
            ->where('employee_id', $employee->id)
            ->whereDate('work_date', now()->toDateString())
            ->first();

        if ($attendance === null || $attendance->check_out !== null) {
            return 0;
        }

        $location = AttendanceLocation::tryMake($latitude, $longitude, $accuracy);

        if ($location !== null) {
            app(AttendanceTracker::class)->append($attendance, $location);
        }

        return (int) $attendance->tracks()->count();
    }

    public function enroll(mixed $descriptor): void
    {
        $user = Filament::auth()->user();

        if ($user === null || ! $user->can('create_attendance_attendance')) {
            $this->notify('danger', 'forbidden');

            return;
        }

        $employee = $this->employee();

        if (! $employee) {
            $this->notify('danger', 'missing-employee');

            return;
        }

        $normalized = app(FaceMatcher::class)->normalize($descriptor);

        if ($normalized === null) {
            $this->notify('danger', 'face-invalid');

            return;
        }

        FaceProfile::query()->updateOrCreate(
            ['employee_id' => $employee->id],
            [
                'company_id' => $employee->company_id,
                'descriptor' => $normalized,
            ],
        );

        $this->notify('success', 'face-enrolled');
    }

    public function faceNotFound(): void
    {
        $this->notify('danger', 'face-not-found');
    }

    public function cameraDenied(): void
    {
        $this->notify('danger', 'camera-denied');
    }

    public function locationDenied(): void
    {
        $this->notify('danger', 'location-denied');
    }

    public function locationUnavailable(): void
    {
        $this->notify('danger', 'location-unavailable');
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $user = Filament::auth()->user();
        $employee = $this->employee();
        $attendance = null;

        if ($employee) {
            $attendance = Attendance::query()
                ->where('employee_id', $employee->id)
                ->whereDate('work_date', now()->toDateString())
                ->first();
        }

        return [
            'employee'     => $employee,
            'attendance'   => $attendance,
            'faceEnrolled' => $employee
                ? FaceProfile::query()->where('employee_id', $employee->id)->exists()
                : false,
            'tracking'     => (bool) ($attendance?->check_in && ! $attendance?->check_out),
            'trackCount'   => (int) ($attendance?->tracks()->count() ?? 0),
            'canClockIn'   => (bool) $user?->can('create_attendance_attendance'),
            'canClockOut'  => (bool) $user?->can('update_attendance_attendance'),
            'requiresFace' => $this->requiresFace,
            'sites'        => AttendanceSite::query()->where('is_active', true)->orderBy('name')->get(),
            'faceScript'   => asset('vendor/face-api/face-api.js'),
            'faceModel'    => asset('vendor/face-api/model'),
            'faceHelper'   => asset('js/aura/clock-face.js'),
        ];
    }

    protected function record(string $direction, mixed $latitude, mixed $longitude, mixed $accuracy, mixed $descriptor = null): bool
    {
        $user = Filament::auth()->user();
        $permission = $direction === 'in' ? 'create_attendance_attendance' : 'update_attendance_attendance';

        if ($user === null || ! $user->can($permission)) {
            $this->notify('danger', 'forbidden');

            return false;
        }

        $employee = $this->employee();

        if (! $employee) {
            $this->notify('danger', 'missing-employee');

            return false;
        }

        if ($this->requiresFace && ! $this->faceMatches($employee, $descriptor)) {
            return $this->trackingActive($employee);
        }

        $location = AttendanceLocation::tryMake($latitude, $longitude, $accuracy);

        if ($location === null) {
            $this->notify('danger', 'location-invalid');

            return $this->trackingActive($employee);
        }

        $decision = app(AttendanceGeofence::class)->decide($location);

        if (! $decision->allowed) {
            $this->notify('danger', 'outside-site');

            return $this->trackingActive($employee);
        }

        $clock = app(AttendanceClock::class);
        $siteId = $decision->site?->id;
        $result = $direction === 'in'
            ? $clock->clockIn($employee, location: $location, siteId: $siteId)
            : $clock->clockOut($employee, location: $location, siteId: $siteId);

        $key = match ($result) {
            ClockResult::Recorded         => $direction === 'in' ? 'clock-in' : 'clock-out',
            ClockResult::AlreadyClockedIn => 'already-in',
            ClockResult::MissingClockIn   => 'missing-open',
        };

        $this->notify($result === ClockResult::Recorded ? 'success' : 'warning', $key);

        return $this->trackingActive($employee);
    }

    protected function requestLooksLikePhone(): bool
    {
        $agent = (string) request()->userAgent();

        return preg_match('/android|iphone|ipad|ipod|mobile|webos|blackberry|iemobile|opera mini/i', $agent) === 1;
    }

    protected function faceMatches(Employee $employee, mixed $descriptor): bool
    {
        $matcher = app(FaceMatcher::class);
        $probe = $matcher->normalize($descriptor);

        if ($probe === null) {
            $this->notify('danger', 'face-invalid');

            return false;
        }

        $profile = FaceProfile::query()->where('employee_id', $employee->id)->first();

        if ($profile === null) {
            $this->notify('danger', 'face-missing');

            return false;
        }

        $enrolled = $matcher->normalize($profile->descriptor);

        if ($enrolled === null || ! $matcher->matches($probe, $enrolled)) {
            $this->notify('danger', 'face-mismatch');

            return false;
        }

        return true;
    }

    protected function trackingActive(Employee $employee): bool
    {
        return Attendance::query()
            ->where('employee_id', $employee->id)
            ->whereDate('work_date', now()->toDateString())
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->exists();
    }

    protected function employee(): ?Employee
    {
        $user = Filament::auth()->user();

        if (! $user instanceof Authenticatable) {
            return null;
        }

        return app(AttendanceClock::class)->employeeForUser((int) $user->getAuthIdentifier());
    }

    protected function notify(string $status, string $key): void
    {
        $notification = Notification::make()
            ->title(__('attendances::filament/pages/clock.notifications.'.$key.'.title'))
            ->body(__('attendances::filament/pages/clock.notifications.'.$key.'.body'));

        match ($status) {
            'success' => $notification->success(),
            'warning' => $notification->warning(),
            default   => $notification->danger(),
        };

        $notification->send();
    }
}
