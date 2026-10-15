<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Evaluasi Triwulanan Karyawan - {{ $record->employee?->name }}</title>
    <style>
        @page {
            margin: 20mm 16mm 18mm 16mm;
        }
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            color: #111827;
            line-height: 1.4;
        }
        .header-meta {
            text-align: right;
            font-size: 7.5pt;
            color: #6b7280;
            margin-bottom: 4px;
        }
        .doc-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .doc-subtitle {
            text-align: center;
            font-weight: bold;
            font-size: 10.5pt;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .doc-period {
            text-align: center;
            font-weight: bold;
            font-size: 9.5pt;
            margin-bottom: 14px;
            padding-bottom: 6px;
            border-bottom: 2px solid #111827;
        }
        .section-heading {
            font-weight: bold;
            font-size: 10pt;
            background-color: #f3f4f6;
            border: 1px solid #374151;
            padding: 4px 8px;
            margin-top: 12px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        table.grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        table.grid-table th,
        table.grid-table td {
            border: 1px solid #374151;
            padding: 5px 7px;
            vertical-align: top;
        }
        table.grid-table th {
            background-color: #e5e7eb;
            font-weight: bold;
            text-align: left;
        }
        table.info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        table.info-table td {
            border: 1px solid #374151;
            padding: 5px 8px;
            width: 50%;
            vertical-align: top;
        }
        .label-inline {
            display: inline-block;
            width: 135px;
            font-weight: bold;
        }
        .rating-box {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            vertical-align: middle !important;
        }
        .comment-label {
            margin-top: 4px;
            font-style: italic;
            color: #374151;
        }
        .badge-pass {
            color: #065f46;
            font-weight: bold;
        }
        .badge-fail {
            color: #991b1b;
            font-weight: bold;
        }
        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #111827;
            text-align: center;
            line-height: 11px;
            font-size: 9pt;
            font-weight: bold;
            margin-right: 6px;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .signature-table td {
            border: 1px solid #374151;
            padding: 10px;
            text-align: center;
            vertical-align: top;
            width: 50%;
        }
        .sig-space {
            height: 56px;
            display: block;
        }
        .small-note {
            font-size: 8.5pt;
            color: #374151;
        }
    </style>
</head>
<body>
    @php
        $employee = $record->employee;
        $reviewer = $record->reviewer;
        $ratings = $record->ratings;
        $summary = $record->criteriaSummary();
        $decision = $record->recommendation_decision?->value;
    @endphp

    <div class="header-meta">
        {{ $record->evaluation_type?->getLabel() ?? 'Evaluasi Kinerja' }} | Dicetak: {{ now()->format('d/m/Y H:i') }}
    </div>

    <div class="doc-title">LAPORAN EVALUASI TRIWULANAN</div>
    <div class="doc-subtitle">KARYAWAN PKWT / KONTRAK</div>
    <div class="doc-period">
        PERIODE : {{ $record->period_start?->translatedFormat('F Y') ?? '__________' }}
        s/d {{ $record->period_end?->translatedFormat('F Y') ?? '__________' }}
        ({{ $record->title }})
    </div>

    <!-- A. DATA KARYAWAN -->
    <div class="section-heading">A. DATA KARYAWAN</div>
    <table class="info-table">
        <tr>
            <td>
                <div><span class="label-inline">Nama</span>: {{ $employee?->name ?? '—' }}</div>
                <div><span class="label-inline">NIK</span>: {{ $record->employee_nik ?: ($employee?->identification_id ?? '—') }}</div>
                <div><span class="label-inline">Tanggal Masuk</span>: {{ ($record->join_date ?? $employee?->join_date)?->format('d M Y') ?? '—' }}</div>
                <div><span class="label-inline">Jenjang Kepangkatan</span>: {{ $record->grade_level ?: '—' }}</div>
            </td>
            <td>
                <div><span class="label-inline">Jabatan</span>: {{ $record->job_title_snapshot ?: ($employee?->job_title ?? '—') }}</div>
                <div><span class="label-inline">Divisi</span>: {{ $record->division_name ?: '—' }}</div>
                <div><span class="label-inline">Departemen</span>: {{ $record->department_name ?: ($employee?->department?->name ?? '—') }}</div>
                <div><span class="label-inline">Lokasi Kerja</span>: {{ $record->work_location_name ?: ($employee?->workLocation?->name ?? '—') }}</div>
            </td>
        </tr>
    </table>

    <!-- B. PENCAPAIAN GOAL & KOMPETENSI KARYAWAN -->
    <div class="section-heading">B. PENCAPAIAN GOAL &amp; KOMPETENSI KARYAWAN</div>
    <table class="grid-table">
        <tr>
            <td style="width: 50%;">
                <strong>Program Goal Setting:</strong><br>
                {{ $record->performancePlan?->title ?? 'Pencapaian Goal Karyawan' }}<br>
                <strong>Rata-rata Pencapaian Goal:</strong>
                <span style="font-size: 11pt; font-weight: bold;">
                    {{ $summary['goal_percentage'] !== null ? number_format($summary['goal_percentage'], 1).'%' : '_____ %' }}
                </span>
            </td>
            <td style="width: 50%;">
                <strong>Penilaian Kompetensi Karyawan:</strong><br>
                Jumlah kompetensi yang memenuhi <em>requirement</em>:<br>
                <strong>{{ $record->competencies_met_count ?? '___' }}</strong> dari
                <strong>{{ $record->competencies_total_count ?? '___' }}</strong> kompetensi
                (<strong>{{ $summary['competency_percentage'] !== null ? number_format($summary['competency_percentage'], 1).'%' : '___%' }}</strong>)
            </td>
        </tr>
    </table>

    @if ($ratings->isNotEmpty())
        <!-- C. KRITERIA PENILAIAN KINERJA (JIKA DIISI) -->
        <div class="section-heading">C. KRITERIA PENILAIAN KINERJA</div>
        <table class="grid-table">
            <thead>
                <tr>
                    <th style="width: 32px; text-align: center;">No.</th>
                    <th>Kriteria &amp; Komentar</th>
                    <th style="width: 65px; text-align: center;">Bobot</th>
                    <th style="width: 75px; text-align: center;">Nilai (1-5)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ratings as $idx => $item)
                    <tr>
                        <td style="text-align: center; font-weight: bold;">{{ $idx + 1 }}</td>
                        <td>
                            <strong>{{ $item->title }}</strong>
                            @if (filled($item->description))
                                <div class="small-note">{{ $item->description }}</div>
                            @endif
                            @if (filled($item->comment))
                                <div class="comment-label"><strong>Komentar:</strong> {{ $item->comment }}</div>
                            @endif
                        </td>
                        <td style="text-align: center;">{{ $item->weight }}%</td>
                        <td class="rating-box">{{ $item->score !== null ? $item->score : '—' }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold; background-color: #f9fafb;">
                        Rata-rata Nilai Kriteria
                    </td>
                    <td class="rating-box" style="background-color: #f9fafb;">
                        {{ $summary['work_management_score'] !== null ? number_format($summary['work_management_score'], 2) : '—' }}
                    </td>
                </tr>
            </tbody>
        </table>
    @endif

    <!-- CATATAN PENILAI -->
    <div class="section-heading">CATATAN PENILAI</div>
    <table class="grid-table">
        <thead>
            <tr>
                <th style="width: 50%;">Aspek yang menjadi kekuatan:</th>
                <th style="width: 50%;">Aspek yang perlu dikembangkan:</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="min-height: 55px;">{!! nl2br(e($record->strengths ?: ($record->summary ?: '—'))) !!}</td>
                <td style="min-height: 55px;">{!! nl2br(e($record->areas_for_improvement ?: '—')) !!}</td>
            </tr>
        </tbody>
    </table>

    <!-- USULAN PENGEMBANGAN -->
    <div class="section-heading">USULAN PENGEMBANGAN</div>
    <table class="grid-table">
        <tr>
            <td style="min-height: 45px;">
                {!! nl2br(e($record->development_plan ?: '—')) !!}
                @if (filled($record->employee_comment))
                    <div style="margin-top: 6px; border-top: 1px dashed #9ca3af; padding-top: 4px;">
                        <strong>Tanggapan Karyawan:</strong> {{ $record->employee_comment }}
                    </div>
                @endif
            </td>
        </tr>
    </table>

    <!-- KESIMPULAN REKOMENDASI -->
    <div class="section-heading">KESIMPULAN REKOMENDASI</div>
    <table class="grid-table">
        <tr>
            <td>
                <div style="margin-bottom: 6px;">
                    <span class="checkbox">{{ $decision === 'appoint_permanent' ? 'V' : '' }}</span>
                    <strong>Direkomendasikan untuk diangkat sebagai karyawan tetap</strong>, efektif per tanggal:
                    <u>{{ $decision === 'appoint_permanent' && $record->permanent_effective_date ? $record->permanent_effective_date->format('d M Y') : '……………………' }}</u>
                </div>
                <div style="margin-bottom: 6px;">
                    <span class="checkbox">{{ $decision === 'extend_pkwt' ? 'V' : '' }}</span>
                    <strong>Diperpanjang masa PKWT-nya selama</strong>
                    <u>{{ $decision === 'extend_pkwt' && $record->extension_months ? $record->extension_months : '……' }}</u> bulan,
                    dengan pertimbangan:
                    <u>{{ $decision === 'extend_pkwt' && filled($record->recommendation_reason) ? $record->recommendation_reason : '………………………………………………………………' }}</u>
                </div>
                <div style="margin-bottom: 6px;">
                    <span class="checkbox">{{ $decision === 'transfer_outtask_outsource' ? 'V' : '' }}</span>
                    <strong>Dialihkan ke Outtask / Outsource</strong>
                    @if ($decision === 'transfer_outtask_outsource' && $record->permanent_effective_date)
                        efektif per tanggal <u>{{ $record->permanent_effective_date->format('d M Y') }}</u>
                    @endif
                    @if ($decision === 'transfer_outtask_outsource' && $record->extension_months)
                        ({{ $record->extension_months }} bulan)
                    @endif
                    , dengan pertimbangan:
                    <u>{{ $decision === 'transfer_outtask_outsource' && filled($record->recommendation_reason) ? $record->recommendation_reason : '………………………………………………………………' }}</u>
                </div>
                <div style="margin-bottom: 4px;">
                    <span class="checkbox">{{ $decision === 'terminate_pkwt' ? 'V' : '' }}</span>
                    <strong>Tidak direkomendasikan untuk diangkat sebagai karyawan tetap / masa PKWT diterminasi</strong>, dengan alasan:
                    <u>{{ $decision === 'terminate_pkwt' && filled($record->recommendation_reason) ? $record->recommendation_reason : '………………………………………………………………' }}</u>
                </div>
            </td>
        </tr>
    </table>

    <!-- KOLOM PERSETUJUAN (KARYAWAN & PENILAI) -->
    <div class="section-heading">KOLOM PERSETUJUAN</div>
    <table class="signature-table">
        <tr>
            <td>
                <strong>KARYAWAN YANG DINILAI,</strong>
                <span class="sig-space">
                    @if ($record->employee_signed_at)
                        <br><em style="font-size: 8pt; color: #065f46;">[Disetujui Digital]</em>
                    @endif
                </span>
                ( <strong>{{ $employee?->name ?? '____________________' }}</strong> )<br>
                <div style="text-align: left; font-size: 8.5pt; margin-top: 4px;">
                    NIK: {{ $record->employee_nik ?: ($employee?->identification_id ?? '—') }}<br>
                    TGL: {{ $record->employee_signed_at?->format('d/m/Y') ?? '____________' }}
                </div>
            </td>
            <td>
                <strong>PENILAI (ATASAN LANGSUNG),</strong>
                <span class="sig-space">
                    @if ($record->reviewer_signed_at)
                        <br><em style="font-size: 8pt; color: #065f46;">[Disetujui Digital]</em>
                    @endif
                </span>
                ( <strong>{{ $reviewer?->name ?? '____________________' }}</strong> )<br>
                <div style="text-align: left; font-size: 8.5pt; margin-top: 4px;">
                    NIK: {{ $reviewer?->identification_id ?? '—' }}<br>
                    TGL: {{ $record->reviewer_signed_at?->format('d/m/Y') ?? '____________' }}
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
