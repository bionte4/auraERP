<?php

namespace Webkul\Attendance\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Attendance\Enums\AttendanceStatus;
use Webkul\Attendance\Models\Attendance;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = Attendance::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'work_date'   => fake()->date(),
            'status'      => AttendanceStatus::Present,
            'note'        => null,
        ];
    }
}
