<?php

namespace Webkul\Evaluation\Filament\Resources\EvaluationResource\Pages;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Evaluation\Filament\Resources\EvaluationResource;
use Webkul\Evaluation\Models\Evaluation;

class ViewEvaluation extends ViewRecord
{
    protected static string $resource = EvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print_pkwt_pdf')
                ->label('Cetak Form Evaluasi (PDF)')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->action(function (Evaluation $record): StreamedResponse {
                    $record->loadMissing(['employee.department', 'employee.workLocation', 'reviewer', 'gmApprover', 'performancePlan', 'ratings']);
                    $pdf = Pdf::loadView('evaluations::pdf.pkwt-evaluation', ['record' => $record])
                        ->setPaper('a4', 'portrait');

                    $filename = 'Evaluasi-Karyawan-'.str($record->employee?->name ?? 'Karyawan')->slug().'-'.$record->id.'.pdf';

                    return response()->streamDownload(
                        fn () => print ($pdf->output()),
                        $filename
                    );
                }),
            Action::make('sign_evaluation')
                ->label('Approve / Tanda Tangani Berjenjang')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->schema([
                    Select::make('signer_role')
                        ->label('Tahap Persetujuan Berjenjang')
                        ->options([
                            'auto'     => 'Otomatis Sesuai Akun / Tahap Saat Ini',
                            'reviewer' => 'Tahap 1: Penilai / Atasan Langsung (Level 1)',
                            'employee' => 'Tahap 2: Konfirmasi Karyawan yang Dinilai',
                            'level2'   => 'Tahap 3: Atasan Penilai / Kepala Departemen (Level 2)',
                        ])
                        ->required()
                        ->default('auto'),
                ])
                ->action(function (Evaluation $record, array $data): void {
                    $stageLabel = $record->signByRole($data['signer_role'] ?? 'auto', Auth::user());

                    Notification::make()
                        ->success()
                        ->title('Approval Berjenjang Tercatat')
                        ->body("Status tahapan saat ini: {$stageLabel}.")
                        ->send();
                }),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
