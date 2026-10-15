<?php

namespace Webkul\Evaluation\Filament\Resources\EvaluationResource\Pages;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Evaluation\Filament\Resources\EvaluationResource;
use Webkul\Evaluation\Models\Evaluation;

class EditEvaluation extends EditRecord
{
    protected static string $resource = EvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sync_goal_and_criteria')
                ->label('Sinkronkan Goal & 10 Aspek PKWT')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->action(function (Evaluation $record): void {
                    $added = $record->seedDefaultPkwtCriteria(false);
                    $goalScore = $record->syncGoalFromPerformancePlan();

                    Notification::make()
                        ->success()
                        ->title('Sinkronisasi Evaluasi PKWT Selesai')
                        ->body(
                            "Aspek baru ditambahkan: {$added}. ".
                            ($goalScore !== null ? "Pencapaian Goal disinkronkan: {$goalScore}%." : 'Belum ada data Goal Setting yang terhubung.')
                        )
                        ->send();

                    $this->refreshFormData(['goal_achievement_percentage', 'performance_plan_id']);
                }),
            Action::make('print_pkwt_pdf')
                ->label('Cetak Form Evaluasi PKWT (PDF)')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->action(function (Evaluation $record): StreamedResponse {
                    $record->loadMissing(['employee.department', 'employee.workLocation', 'reviewer', 'gmApprover', 'directorApprover', 'performancePlan', 'ratings']);
                    $pdf = Pdf::loadView('evaluations::pdf.pkwt-evaluation', ['record' => $record])
                        ->setPaper('a4', 'portrait');

                    $filename = 'Evaluasi-PKWT-'.str($record->employee?->name ?? 'Karyawan')->slug().'-'.$record->id.'.pdf';

                    return response()->streamDownload(
                        fn () => print ($pdf->output()),
                        $filename
                    );
                }),
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
