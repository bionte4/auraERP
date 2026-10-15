<?php

namespace Webkul\Evaluation\Filament\Resources\EvaluationResource\Pages;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Evaluation\Enums\EvaluationStatus;
use Webkul\Evaluation\Filament\Resources\EvaluationResource;
use Webkul\Evaluation\Models\Evaluation;

class ViewEvaluation extends ViewRecord
{
    protected static string $resource = EvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
            Action::make('sign_evaluation')
                ->label('Tanda Tangani / Setujui')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->schema([
                    Select::make('signer_role')
                        ->label('Tanda Tangani Sebagai')
                        ->options([
                            'employee' => 'Karyawan yang Dinilai',
                            'reviewer' => 'Penilai (Atasan Langsung)',
                            'gm'       => 'General Manager',
                            'director' => 'Direktur Bidang',
                        ])
                        ->required()
                        ->default('reviewer'),
                ])
                ->action(function (Evaluation $record, array $data): void {
                    $column = match ($data['signer_role'] ?? 'reviewer') {
                        'employee' => 'employee_signed_at',
                        'gm'       => 'gm_signed_at',
                        'director' => 'director_signed_at',
                        default    => 'reviewer_signed_at',
                    };

                    $record->{$column} = now();

                    if ($record->reviewer_signed_at && $record->status === EvaluationStatus::Draft) {
                        $record->status = EvaluationStatus::InReview;
                    }

                    if ($record->employee_signed_at && $record->reviewer_signed_at && $record->gm_signed_at) {
                        $record->status = EvaluationStatus::Completed;
                    }

                    $record->save();

                    Notification::make()
                        ->success()
                        ->title('Persetujuan Evaluasi Tercatat')
                        ->body('Tanda tangan digital berhasil disimpan pada lembar evaluasi.')
                        ->send();
                }),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
