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
use Webkul\Maintenance\Enums\EquipmentMovementType;
use Webkul\Maintenance\Models\Equipment;
use Webkul\Maintenance\Models\EquipmentMovement;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Models\Project;

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    TestBootstrapHelper::ensurePluginInstalled('projects');
    TestBootstrapHelper::ensurePluginInstalled('maintenance');

    if (! Schema::hasColumn('employees_employees', 'contract_end_date')) {
        Artisan::call('migrate', [
            '--force'          => true,
            '--no-interaction' => true,
            '--path'           => 'plugins/webkul/employees/database/migrations/2026_10_14_180000_add_contract_dates_and_employment_type_to_employees_table.php',
        ]);
    }

    if (! Schema::hasColumn('maintenance_equipments', 'employee_id')) {
        Artisan::call('migrate', [
            '--force'          => true,
            '--no-interaction' => true,
            '--path'           => 'plugins/webkul/maintenance/database/migrations/2026_10_14_190000_add_employee_project_customer_to_maintenance_equipments_table.php',
        ]);
    }

    if (! Schema::hasTable('maintenance_equipment_movements')) {
        Artisan::call('migrate', [
            '--force'          => true,
            '--no-interaction' => true,
            '--path'           => 'plugins/webkul/maintenance/database/migrations/2026_10_14_193000_create_maintenance_equipment_movements_table.php',
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

it('links equipment assets to employee, project, and customer and reports them in AuraAiAssistantService', function (): void {
    $user = FilamentHelper::actingAs([
        'view_any_employee_employee',
    ]);

    $customer = Partner::withoutGlobalScopes()->create([
        'name'       => 'PT Klien Nusantara',
        'sub_type'   => 'company',
        'company_id' => $user->default_company_id,
        'creator_id' => $user->id,
    ]);

    $project = Project::withoutGlobalScopes()->create([
        'name'       => 'Instalasi Server Klien Nusantara',
        'partner_id' => $customer->id,
        'is_active'  => true,
        'company_id' => $user->default_company_id,
        'creator_id' => $user->id,
    ]);

    $employee = Employee::withoutGlobalScopes()->create([
        'name'       => 'Andi Engineer',
        'is_active'  => true,
        'company_id' => $user->default_company_id,
        'creator_id' => $user->id,
    ]);

    $asset = Equipment::withoutGlobalScopes()->create([
        'name'           => 'MacBook Pro M4 #A01',
        'serial_no'      => 'SN-MBP-001',
        'effective_date' => now()->toDateString(),
        'employee_id'    => $employee->id,
        'project_id'     => $project->id,
        'customer_id'    => $customer->id,
        'company_id'     => $user->default_company_id,
        'creator_id'     => $user->id,
    ]);

    $service = app(AuraAiAssistantService::class);

    $assetAnswer = $service->answer('Daftar aset proyek dan karyawan');
    expect($assetAnswer)
        ->toContain('MacBook Pro M4 #A01')
        ->toContain('Andi Engineer')
        ->toContain('Instalasi Server Klien Nusantara')
        ->toContain('PT Klien Nusantara');

    $empSummary = $service->summarizeRecord($employee);
    expect($empSummary)->toContain('MacBook Pro M4 #A01');

    $note = $service->replyOnRecord($asset, 'Ringkas aset ini');
    expect($note)->not->toBeNull();
});

it('records asset movements for relocation and replacement and updates equipment state', function (): void {
    $user = FilamentHelper::actingAs([
        'view_any_employee_employee',
    ]);

    $empA = Employee::withoutGlobalScopes()->create([
        'name'       => 'Sari Teknisi A',
        'is_active'  => true,
        'company_id' => $user->default_company_id,
    ]);

    $empB = Employee::withoutGlobalScopes()->create([
        'name'       => 'Doni Teknisi B',
        'is_active'  => true,
        'company_id' => $user->default_company_id,
    ]);

    $oldAsset = Equipment::withoutGlobalScopes()->create([
        'name'           => 'Router Mikrotik #R01',
        'effective_date' => now()->toDateString(),
        'employee_id'    => $empA->id,
        'location'       => 'Site Jakarta',
        'company_id'     => $user->default_company_id,
    ]);

    $newAsset = Equipment::withoutGlobalScopes()->create([
        'name'           => 'Router Mikrotik #R02 (Pengganti)',
        'effective_date' => now()->toDateString(),
        'location'       => 'Gudang Pusat',
        'company_id'     => $user->default_company_id,
    ]);

    EquipmentMovement::query()->create([
        'equipment_id'  => $oldAsset->id,
        'movement_type' => EquipmentMovementType::Relocation,
        'movement_date' => now()->toDateString(),
        'reference'     => 'BAST-REL-001',
        'to_employee_id'=> $empB->id,
        'to_location'   => 'Site Bandung',
        'notes'         => 'Relokasi router ke cabang Bandung',
    ]);

    $oldAsset->refresh();
    expect($oldAsset->employee_id)->toBe($empB->id)
        ->and($oldAsset->location)->toBe('Site Bandung');

    EquipmentMovement::query()->create([
        'equipment_id'             => $oldAsset->id,
        'movement_type'            => EquipmentMovementType::Replacement,
        'movement_date'            => now()->toDateString(),
        'reference'                => 'BAST-REP-002',
        'replacement_equipment_id' => $newAsset->id,
        'to_location'              => 'Gudang Servis',
        'notes'                    => 'Ganti unit karena port WAN rusak',
    ]);

    $oldAsset->refresh();
    $newAsset->refresh();

    expect($oldAsset->employee_id)->toBeNull()
        ->and($oldAsset->location)->toBe('Gudang Servis')
        ->and($newAsset->employee_id)->toBe($empB->id)
        ->and($newAsset->location)->toBe('Site Bandung');

    $aiReply = app(AuraAiAssistantService::class)->answer('Bagaimana riwayat pergerakan dan replacement aset?');
    expect($aiReply)->toContain('Router Mikrotik #R01');
});
