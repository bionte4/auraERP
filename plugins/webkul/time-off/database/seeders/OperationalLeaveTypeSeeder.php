<?php

namespace Webkul\TimeOff\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Webkul\Support\Models\Company;
use Webkul\TimeOff\Models\LeaveType;

class OperationalLeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('time_off_leave_types')) {
            return;
        }

        $companyId = Company::query()->value('id');

        $types = [
            ['name' => 'Cuti', 'request_unit' => 'day', 'support_document' => false, 'sort' => 1],
            ['name' => 'Izin', 'request_unit' => 'day', 'support_document' => false, 'sort' => 2],
            ['name' => 'Sakit', 'request_unit' => 'day', 'support_document' => true, 'sort' => 3],
            ['name' => 'Izin terlambat', 'request_unit' => 'hour', 'support_document' => false, 'sort' => 4],
            ['name' => 'Izin pulang cepat', 'request_unit' => 'hour', 'support_document' => false, 'sort' => 5],
        ];

        foreach ($types as $type) {
            LeaveType::query()->withoutGlobalScopes()->firstOrCreate(
                ['name' => $type['name']],
                [
                    'sort'                       => $type['sort'],
                    'company_id'                 => $companyId,
                    'leave_validation_type'      => 'both',
                    'requires_allocation'        => 'no',
                    'employee_requests'          => 'yes',
                    'allocation_validation_type' => 'hr',
                    'time_type'                  => 'leave',
                    'request_unit'               => $type['request_unit'],
                    'create_calendar_meeting'    => false,
                    'is_active'                  => true,
                    'show_on_dashboard'          => true,
                    'unpaid'                     => $type['name'] !== 'Cuti',
                    'support_document'           => $type['support_document'],
                    'color'                      => '#3b6ef6',
                ],
            );
        }
    }
}
