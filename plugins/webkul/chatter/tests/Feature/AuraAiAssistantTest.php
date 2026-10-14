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
use Webkul\Employee\Services\EmployeeBulkImportService;
use Webkul\Maintenance\Enums\EquipmentMovementType;
use Webkul\Maintenance\Models\Equipment;
use Webkul\Maintenance\Models\EquipmentMovement;
use Webkul\Maintenance\Services\EquipmentBulkImportService;
use Webkul\Partner\Models\Partner;
use Webkul\Partner\Services\PartnerBulkImportService;
use Webkul\Project\Models\Project;
use Webkul\Project\Services\ProjectBulkImportService;
use Webkul\Project\Services\ProjectExpiryNotifier;
use Webkul\Security\Models\User;

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

    if (! Schema::hasColumn('projects_projects', 'project_value')) {
        Artisan::call('migrate', [
            '--force'          => true,
            '--no-interaction' => true,
            '--path'           => 'plugins/webkul/projects/database/migrations/2026_10_14_200000_add_project_value_to_projects_projects_table.php',
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

it('excludes internal users, employees, and companies from equipment vendor and customer dropdown queries', function (): void {
    $user = FilamentHelper::actingAs([
        'view_any_employee_employee',
    ]);

    $emp = Employee::withoutGlobalScopes()->create([
        'name'       => 'Edo Asri Internal',
        'is_active'  => true,
        'company_id' => $user->default_company_id,
    ]);

    $vendorId = Equipment::createExternalPartner([
        'name'  => 'PT Bhinneka Vendor',
        'email' => 'sales@bhinneka.test',
    ], 'vendor');

    $vendorNames = Equipment::filterExternalPartnersQuery(Partner::withoutGlobalScopes(), 'vendor')
        ->pluck('name')
        ->all();

    expect($vendorNames)
        ->toContain('PT Bhinneka Vendor')
        ->not->toContain('Edo Asri Internal')
        ->not->toContain($user->name);

    expect($vendorId)->toBeGreaterThan(0);
});

it('bulk imports employees and equipment assets from tabular rows', function (): void {
    $user = FilamentHelper::actingAs([
        'view_any_employee_employee',
    ]);

    $empResult = app(EmployeeBulkImportService::class)->importRows([
        ['name', 'work_email', 'job_title', 'department', 'employment_type', 'join_date', 'contract_end_date'],
        ['Nadia Import', 'nadia.import@opsora.my.id', 'QA Engineer', 'Quality Assurance', 'Contract', '2026-01-10', '2026-11-10'],
    ]);

    expect($empResult['created'])->toBe(1);

    $importedEmp = Employee::withoutGlobalScopes()->where('work_email', 'nadia.import@opsora.my.id')->first();
    expect($importedEmp)->not->toBeNull()
        ->and($importedEmp->name)->toBe('Nadia Import');

    $assetResult = app(EquipmentBulkImportService::class)->importRows([
        ['name', 'serial_no', 'model', 'category', 'vendor', 'employee', 'customer', 'location', 'cost'],
        ['ThinkPad X1 Carbon #T01', 'SN-TP-X1-99', 'Gen 12', 'Laptops', 'PT Lenovo Distribusi', 'Nadia Import', 'PT Bank Maju', 'Site Surabaya', 24000000],
    ]);

    expect($assetResult['created'])->toBe(1);

    $importedAsset = Equipment::withoutGlobalScopes()->where('serial_no', 'SN-TP-X1-99')->first();
    expect($importedAsset)->not->toBeNull()
        ->and($importedAsset->employee_id)->toBe($importedEmp->id)
        ->and($importedAsset->location)->toBe('Site Surabaya');

    $partnerResult = app(PartnerBulkImportService::class)->importRows([
        ['name', 'role', 'account_type', 'email', 'phone', 'city'],
        ['PT Telkom Pelanggan', 'customer', 'company', 'info@telkom.test', '021-147', 'Jakarta'],
        ['PT Metrodata Vendor', 'vendor', 'company', 'sales@metrodata.test', '021-29345', 'Jakarta'],
    ]);

    expect($partnerResult['created'])->toBe(2);

    $cust = Partner::withoutGlobalScopes()->where('email', 'info@telkom.test')->first();
    expect($cust)->not->toBeNull()
        ->and($cust->name)->toBe('PT Telkom Pelanggan');
});

it('supports one customer having multiple projects with start/end dates, project value, and 3-2-1 month reminders', function (): void {
    $manager = User::factory()->create();
    $this->actingAs($manager);

    $customerId = Project::createCustomerPartner([
        'name'  => 'PT Nusantara Klien Utama',
        'email' => 'project@nusantara.test',
        'phone' => '021-888999',
    ]);

    $customer = Partner::withoutGlobalScopes()->findOrFail($customerId);

    $project3M = Project::withoutGlobalScopes()->create([
        'name'          => 'Implementasi ERP Fase 1',
        'partner_id'    => $customer->id,
        'user_id'       => $manager->id,
        'creator_id'    => $manager->id,
        'start_date'    => now()->subMonths(3)->toDateString(),
        'end_date'      => now()->addDays(75)->toDateString(),
        'project_value' => 250000000,
        'is_active'     => true,
    ]);

    $project2M = Project::withoutGlobalScopes()->create([
        'name'          => 'Integrasi Gudang Fase 2',
        'partner_id'    => $customer->id,
        'user_id'       => $manager->id,
        'creator_id'    => $manager->id,
        'start_date'    => now()->subMonths(2)->toDateString(),
        'end_date'      => now()->addDays(45)->toDateString(),
        'project_value' => 150000000,
        'is_active'     => true,
    ]);

    $project1M = Project::withoutGlobalScopes()->create([
        'name'          => 'Maintenance Support Fase 3',
        'partner_id'    => $customer->id,
        'user_id'       => $manager->id,
        'creator_id'    => $manager->id,
        'start_date'    => now()->subMonths(5)->toDateString(),
        'end_date'      => now()->addDays(20)->toDateString(),
        'project_value' => 95000000,
        'is_active'     => true,
    ]);

    $projectFar = Project::withoutGlobalScopes()->create([
        'name'          => 'Pengembangan AI Tahun Depan',
        'partner_id'    => $customer->id,
        'user_id'       => $manager->id,
        'creator_id'    => $manager->id,
        'start_date'    => now()->toDateString(),
        'end_date'      => now()->addDays(150)->toDateString(),
        'project_value' => 500000000,
        'is_active'     => true,
    ]);

    expect($customer->projects()->withoutGlobalScopes()->count())->toBe(4)
        ->and($project3M->formattedProjectValue())->toBe('Rp 250.000.000')
        ->and($project3M->expiryReminderStage())->toBe('3_months')
        ->and($project2M->expiryReminderStage())->toBe('2_months')
        ->and($project1M->expiryReminderStage())->toBe('1_month')
        ->and($projectFar->expiryReminderStage())->toBeNull();

    $notifier = app(ProjectExpiryNotifier::class);

    // Calling notifyExpiringProjects again should not duplicate already-sent notifications for the same stage
    $duplicateSent = $notifier->notifyExpiringProjects();
    expect($duplicateSent)->toBe(0);

    $stagesNotified = $manager->notifications()
        ->get()
        ->map(fn ($n) => $n->data['project_expiry_stage'] ?? null)
        ->filter()
        ->values()
        ->all();

    expect($stagesNotified)->toContain('3_months')
        ->toContain('2_months')
        ->toContain('1_month');
});

it('bulk imports projects with customer, start/end dates, and project value', function (): void {
    $manager = User::factory()->create(['name' => 'Budi PM']);
    $this->actingAs($manager);

    $service = app(ProjectBulkImportService::class);

    $rows = [
        $service->headings(),
        [
            'Implementasi ERP Cabang Jakarta',
            'PT Bank Mandiri Klien',
            'Budi PM',
            'In Progress',
            '2026-06-01',
            now()->addDays(50)->toDateString(),
            '350.000.000',
            200,
            'internal',
            'Proyek ERP Jakarta',
        ],
        [
            'Implementasi ERP Cabang Surabaya',
            'PT Bank Mandiri Klien',
            'Budi PM',
            'Planned',
            '2026-07-01',
            now()->addDays(80)->toDateString(),
            175000000,
            120,
            'internal',
            'Proyek ERP Surabaya',
        ],
    ];

    $result = $service->importRows($rows);

    expect($result['created'])->toBe(2)
        ->and($result['updated'])->toBe(0);

    $customer = Partner::withoutGlobalScopes()->where('name', 'PT Bank Mandiri Klien')->first();
    expect($customer)->not->toBeNull()
        ->and($customer->projects()->withoutGlobalScopes()->count())->toBe(2);

    $projJakarta = Project::withoutGlobalScopes()->where('name', 'Implementasi ERP Cabang Jakarta')->first();
    expect($projJakarta)->not->toBeNull()
        ->and((float) $projJakarta->project_value)->toBe(350000000.0)
        ->and($projJakarta->expiryReminderStage())->toBe('2_months');
});
