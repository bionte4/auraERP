<?php

require_once __DIR__.'/../../plugins/webkul/support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../plugins/webkul/support/tests/Helpers/SecurityHelper.php';

use Webkul\Employee\Models\Employee;
use Webkul\Evaluation\Enums\EvaluationStatus;
use Webkul\Evaluation\Enums\EvaluationType;
use Webkul\Evaluation\Models\Evaluation;
use Webkul\Evaluation\Services\EvaluationBulkImportService;

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('evaluations');
    TestBootstrapHelper::ensurePluginInstalled('employees');
    SecurityHelper::disableUserEvents();

    $pkwtMigration = include __DIR__.'/../../plugins/webkul/evaluations/database/migrations/2026_10_15_140000_add_pkwt_quarterly_fields_to_evaluations_tables.php';
    $pkwtMigration->up();
});

it('downloads an import template with expected headings', function (): void {
    $service = app(EvaluationBulkImportService::class);

    expect($service->headings())->toContain('employee_nik', 'evaluation_type', 'score_1', 'score_10', 'comment_1');

    $response = $service->downloadTemplate();

    expect($response->headers->get('content-disposition'))->toContain('template-import-evaluations.xlsx');
});

it('creates evaluations from import rows and seeds scores', function (): void {
    $employee = Employee::factory()->create([
        'identification_id' => 'NIK-IMP-001',
        'name'              => 'Import Karyawan Satu',
    ]);

    $start = now()->startOfQuarter()->toDateString();
    $end = now()->endOfQuarter()->toDateString();

    $service = app(EvaluationBulkImportService::class);

    $result = $service->importRows([
        $service->headings(),
        [
            'NIK-IMP-001',
            'Import Karyawan Satu',
            'Evaluasi Import Q1',
            'pkwt_q1',
            $start,
            $end,
            97,
            8,
            10,
            'extend_pkwt',
            6,
            '',
            'Ringkasan import',
            'Kelebihan',
            'Perbaikan',
            'Pengembangan',
            '',
            4, 4, 3, 4, 4, 4, 3, 4, 3, 4,
            '', '', '', '', '', '', '', '', '', '',
        ],
    ]);

    expect($result)->toMatchArray(['created' => 1, 'updated' => 0, 'skipped' => 0]);

    $evaluation = Evaluation::query()
        ->where('employee_id', $employee->id)
        ->where('evaluation_type', EvaluationType::PkwtQ1)
        ->first();

    expect($evaluation)->not->toBeNull()
        ->and($evaluation->status)->toBe(EvaluationStatus::InReview)
        ->and($evaluation->goal_achievement_percentage)->toBe(97.0)
        ->and($evaluation->ratings()->count())->toBe(10)
        ->and((int) $evaluation->ratings()->where('sort', 1)->value('score'))->toBe(4)
        ->and((float) $evaluation->finalScore())->toBeGreaterThan(0);
});

it('updates existing draft evaluations and skips completed ones', function (): void {
    $employee = Employee::factory()->create([
        'identification_id' => 'NIK-IMP-002',
        'name'              => 'Import Karyawan Dua',
    ]);

    $start = now()->startOfQuarter()->toDateString();
    $end = now()->endOfQuarter()->toDateString();

    $draft = Evaluation::factory()->create([
        'employee_id'     => $employee->id,
        'evaluation_type' => EvaluationType::PkwtQ2,
        'period_start'    => $start,
        'period_end'      => $end,
        'status'          => EvaluationStatus::Draft,
        'title'           => 'Draft lama',
    ]);

    $completed = Evaluation::factory()->create([
        'employee_id'     => $employee->id,
        'evaluation_type' => EvaluationType::PkwtQ3,
        'period_start'    => $start,
        'period_end'      => $end,
        'status'          => EvaluationStatus::Completed,
        'title'           => 'Sudah selesai',
    ]);

    $service = app(EvaluationBulkImportService::class);
    $headings = $service->headings();

    $result = $service->importRows([
        $headings,
        [
            'NIK-IMP-002',
            'Import Karyawan Dua',
            'Draft diperbarui',
            'pkwt_q2',
            $start,
            $end,
            90,
            7,
            10,
            'extend_pkwt',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            3, 3, 3, 3, 3, 3, 3, 3, 3, 3,
            '', '', '', '', '', '', '', '', '', '',
        ],
        [
            'NIK-IMP-002',
            'Import Karyawan Dua',
            'Jangan timpa',
            'pkwt_q3',
            $start,
            $end,
            90,
            7,
            10,
            'extend_pkwt',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            5, 5, 5, 5, 5, 5, 5, 5, 5, 5,
            '', '', '', '', '', '', '', '', '', '',
        ],
        [
            'NIK-TIDAK-ADA',
            'Tidak Ada',
            'Skip',
            'pkwt_q1',
            $start,
            $end,
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '', '', '', '', '', '', '', '', '', '',
            '', '', '', '', '', '', '', '', '', '',
        ],
    ]);

    expect($result)->toMatchArray(['created' => 0, 'updated' => 1, 'skipped' => 2]);

    expect($draft->fresh()->title)->toBe('Draft diperbarui')
        ->and($draft->fresh()->status)->toBe(EvaluationStatus::InReview)
        ->and($completed->fresh()->title)->toBe('Sudah selesai');
});
