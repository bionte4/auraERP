<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Webkul\Chatter\Livewire\AuraAiWidget;
use Webkul\Chatter\Services\AuraAiAssistantService;
use Webkul\Employee\Models\Employee;
use Webkul\Employee\Models\EmploymentType;

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');

    if (! Schema::hasColumn('employees_employees', 'contract_end_date')) {
        Artisan::call('migrate', [
            '--force'          => true,
            '--no-interaction' => true,
            '--path'           => 'plugins/webkul/employees/database/migrations/2026_10_14_180000_add_contract_dates_and_employment_type_to_employees_table.php',
        ]);
    }
});

it('answers ERP contract and attendance questions via AuraAiAssistantService', function (): void {
    $user = FilamentHelper::actingAs([
        'view_any_employee_employee',
    ]);

    $contractType = EmploymentType::query()->firstOrCreate(
        ['name' => 'Contract'],
        ['is_active' => true, 'creator_id' => $user->id],
    );

    Employee::withoutGlobalScopes()->create([
        'name'               => 'Rina Kontrak',
        'work_email'         => 'rina.kontrak@opsora.my.id',
        'is_active'          => true,
        'employment_type_id' => $contractType->id,
        'join_date'          => now()->subMonths(6)->toDateString(),
        'contract_end_date'  => now()->addDays(12)->toDateString(),
        'company_id'         => $user->default_company_id,
        'creator_id'         => $user->id,
    ]);

    $service = app(AuraAiAssistantService::class);

    $contractAnswer = $service->answer('Siapa saja kontrak yang akan berakhir?');
    expect($contractAnswer)
        ->toContain('Rina Kontrak')
        ->toContain('Contract');

    $attendanceAnswer = $service->answer('Bagaimana absensi hari ini?');
    expect($attendanceAnswer)->not->toBeEmpty();
});

it('posts an AI summary note directly into a record chatter thread', function (): void {
    $user = FilamentHelper::actingAs([
        'view_any_employee_employee',
    ]);

    $employee = Employee::withoutGlobalScopes()->create([
        'name'              => 'Dimas Pratama',
        'work_email'        => 'dimas.pratama@opsora.my.id',
        'is_active'         => true,
        'join_date'         => now()->subYear()->toDateString(),
        'contract_end_date' => now()->addDays(20)->toDateString(),
        'company_id'        => $user->default_company_id,
        'creator_id'        => $user->id,
    ]);

    $message = app(AuraAiAssistantService::class)->replyOnRecord($employee, 'Ringkas data karyawan ini');

    expect($message)->not->toBeNull()
        ->and($message->type)->toBe('note')
        ->and($message->body)->toContain('Dimas Pratama');
});

it('interacts with the floating AuraAiWidget livewire component', function (): void {
    FilamentHelper::actingAs([
        'view_any_employee_employee',
    ]);

    Livewire::test(AuraAiWidget::class)
        ->assertSet('isOpen', false)
        ->call('toggle')
        ->assertSet('isOpen', true)
        ->set('question', 'Ringkasan karyawan')
        ->call('ask')
        ->assertSet('question', '')
        ->assertCount('messages', 3);
});

it('restricts regular staff users to only their own employee data in AuraAiAssistantService', function (): void {
    $staffUser = FilamentHelper::actingAs([
        'view_any_attendance_attendance',
    ]);

    Employee::withoutGlobalScopes()->create([
        'name'              => 'Karyawan Lain Rahasia',
        'work_email'        => 'rahasia@opsora.my.id',
        'is_active'         => true,
        'join_date'         => now()->subMonths(5)->toDateString(),
        'contract_end_date' => now()->addDays(5)->toDateString(),
        'company_id'        => $staffUser->default_company_id,
    ]);

    Employee::withoutGlobalScopes()->create([
        'name'              => 'Budi Staf Sendiri',
        'work_email'        => 'budi.sendiri@opsora.my.id',
        'user_id'           => $staffUser->id,
        'job_title'         => 'Teknisi Lapangan',
        'is_active'         => true,
        'join_date'         => now()->subMonths(3)->toDateString(),
        'contract_end_date' => now()->addDays(18)->toDateString(),
        'company_id'        => $staffUser->default_company_id,
    ]);

    $service = app(AuraAiAssistantService::class);
    $answer = $service->answer('Siapa saja kontrak yang akan berakhir?');

    expect($service->canViewCompanyWideData($staffUser))->toBeFalse()
        ->and($answer)->toContain('Budi Staf Sendiri')
        ->and($answer)->toContain('Teknisi Lapangan')
        ->and($answer)->not->toContain('Karyawan Lain Rahasia');
});
