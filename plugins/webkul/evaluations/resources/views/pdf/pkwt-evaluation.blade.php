<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Evaluasi Triwulanan Karyawan PKWT - {{ $record->employee?->name }}</title>
    <style>
        @page {
            margin: 20mm 16mm 18mm 16mm;
        }
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            color: #111827;
            line-height: 1.38;
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
        ol.instructions {
            margin: 4px 0 8px 18px;
            padding: 0;
        }
        ol.instructions li {
            margin-bottom: 3px;
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
        pkwt/hcm – {{ $record->evaluation_type?->getLabel() ?? 'Evaluasi PKWT' }} | Dicetak: {{ now()->format('d/m/Y H:i') }}
    </div>

    <div class="doc-title">LAPORAN EVALUASI TRIWULANAN</div>
    <div class="doc-subtitle">KARYAWAN PKWT (PERJANJIAN KERJA WAKTU TERTENTU)</div>
    <div class="doc-period">
        PERIODE : {{ $record->period_start?->translatedFormat('F Y') ?? '__________' }}
        s/d {{ $record->period_end?->translatedFormat('F Y') ?? '__________' }}
        ({{ $record->title }})
    </div>

    <!-- A. PETUNJUK PENGISIAN -->
    <div class="section-heading">A. PETUNJUK PENGISIAN &amp; SKALA PENILAIAN</div>
    <ol class="instructions small-note">
        <li>Proses evaluasi difokuskan pada kemampuan karyawan dalam melaksanakan tugas-tugas sesuai uraian kerja.</li>
        <li>Penilai adalah atasan langsung atau pembimbing yang bertanggung jawab melakukan penilaian terhadap kinerja karyawan.</li>
        <li>Karyawan dievaluasi setiap tiga bulan terhitung sejak mulai bekerja hingga berakhirnya masa PKWT.</li>
        <li>Gunakan skala rating <strong>1 sampai dengan 5</strong> beserta komentar justifikasi pada setiap aspek penilaian:</li>
    </ol>
    <table class="grid-table small-note" style="width: 85%; margin-left: 18px;">
        <thead>
            <tr>
                <th style="width: 65px; text-align: center;">Rating</th>
                <th>Penjelasan</th>
            </tr>
        </thead>
        <tbody>
            <tr><td style="text-align: center; font-weight: bold;">5</td><td>Secara konsisten melampaui standar yang diharapkan</td></tr>
            <tr><td style="text-align: center; font-weight: bold;">4</td><td>Secara signifikan melebihi standar yang diharapkan</td></tr>
            <tr><td style="text-align: center; font-weight: bold;">3</td><td>Memenuhi standar yang diharapkan</td></tr>
            <tr><td style="text-align: center; font-weight: bold;">2</td><td>Kurang dari standar yang diharapkan</td></tr>
            <tr><td style="text-align: center; font-weight: bold;">1</td><td>Sama sekali tidak memenuhi standar yang diharapkan</td></tr>
        </tbody>
    </table>

    <!-- B. DATA KARYAWAN PKWT -->
    <div class="section-heading">B. DATA KARYAWAN PKWT</div>
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

    <!-- C. PENCAPAIAN GOAL KARYAWAN -->
    <div class="section-heading">C. PENCAPAIAN GOAL KARYAWAN</div>
    <table class="grid-table">
        <tr>
            <td style="width: 70%;">
                <strong>Program Goal Setting Terkait:</strong>
                {{ $record->performancePlan?->title ?? 'Data Pencapaian Goal Karyawan (Modul Performance)' }}
            </td>
            <td style="width: 30%; text-align: center;">
                <strong>Rata-rata Pencapaian Goal:</strong><br>
                <span style="font-size: 12pt; font-weight: bold;">
                    {{ $summary['goal_percentage'] !== null ? number_format($summary['goal_percentage'], 1).'%' : '_____ %' }}
                </span>
            </td>
        </tr>
    </table>

    <!-- D. MANAJEMEN PEKERJAAN (POIN 1 - 10) -->
    <div class="section-heading">D. MANAJEMEN PEKERJAAN</div>
    <table class="grid-table">
        <thead>
            <tr>
                <th style="width: 32px; text-align: center;">No.</th>
                <th>Aspek Penilaian dan Komentar</th>
                <th style="width: 75px; text-align: center;">Rating (1-5)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ratings as $idx => $item)
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $item->title }}</strong>
                        @if (filled($item->description))
                            <div class="small-note">{{ $item->description }}</div>
                        @endif
                        <div class="comment-label">
                            <strong>Komentar:</strong> {{ filled($item->comment) ? $item->comment : '........................................................................................................................................' }}
                        </div>
                    </td>
                    <td class="rating-box">
                        {{ $item->score !== null ? $item->score : '—' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center;">Belum ada kriteria penilaian manajemen pekerjaan yang diisi.</td>
                </tr>
            @endforelse
            <tr>
                <td colspan="2" style="text-align: right; font-weight: bold; background-color: #f9fafb;">
                    Total Nilai (&Sigma; Rating) / Rata-rata Nilai (&Sigma; Rating / {{ max(1, $ratings->count()) }})
                </td>
                <td class="rating-box" style="background-color: #f9fafb;">
                    {{ $record->totalRatingScore() }} / {{ $summary['work_management_score'] !== null ? number_format($summary['work_management_score'], 2) : '—' }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- E. PENILAIAN KOMPETENSI KARYAWAN -->
    <div class="section-heading">E. PENILAIAN KOMPETENSI KARYAWAN</div>
    <table class="grid-table">
        <tr>
            <td>
                Jumlah kompetensi yang memenuhi <em>requirement</em>:
                <strong>{{ $record->competencies_met_count ?? '___' }}</strong> dari
                <strong>{{ $record->competencies_total_count ?? '___' }}</strong> kompetensi
                (<strong>{{ $summary['competency_percentage'] !== null ? number_format($summary['competency_percentage'], 1).'%' : '___%' }}</strong>)
            </td>
        </tr>
    </table>

    <!-- F. CATATAN PENILAI -->
    <div class="section-heading">F. CATATAN PENILAI</div>
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

    <!-- G. USULAN PENGEMBANGAN -->
    <div class="section-heading">G. USULAN PENGEMBANGAN</div>
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

    <!-- H. PENILAIAN AKHIR -->
    <div class="section-heading">H. PENILAIAN AKHIR</div>
    <table class="grid-table">
        <tr>
            <td style="width: 68%;">1. Pencapaian Goal (rata-rata pencapaian seluruh goal, min. 95%)</td>
            <td style="width: 17%; text-align: center; font-weight: bold;">
                {{ $summary['goal_percentage'] !== null ? number_format($summary['goal_percentage'], 1).' %' : '_____ %' }}
            </td>
            <td style="width: 15%; text-align: center;">
                <span class="{{ $summary['goal_met'] ? 'badge-pass' : 'badge-fail' }}">
                    {{ $summary['goal_met'] ? 'MEMENUHI' : 'BELUM' }}
                </span>
            </td>
        </tr>
        <tr>
            <td>2. Manajemen Pekerjaan (rata-rata nilai pada kolom D, min. 3.0)</td>
            <td style="text-align: center; font-weight: bold;">
                {{ $summary['work_management_score'] !== null ? number_format($summary['work_management_score'], 2) : '_____' }}
            </td>
            <td style="text-align: center;">
                <span class="{{ $summary['work_management_met'] ? 'badge-pass' : 'badge-fail' }}">
                    {{ $summary['work_management_met'] ? 'MEMENUHI' : 'BELUM' }}
                </span>
            </td>
        </tr>
        <tr>
            <td>3. Jumlah kompetensi yang memenuhi requirement (min. 90%)</td>
            <td style="text-align: center; font-weight: bold;">
                {{ $record->competencies_met_count ?? '__' }} / {{ $record->competencies_total_count ?? '__' }}
                ({{ $summary['competency_percentage'] !== null ? number_format($summary['competency_percentage'], 1).'%' : '__%' }})
            </td>
            <td style="text-align: center;">
                <span class="{{ $summary['competency_met'] ? 'badge-pass' : 'badge-fail' }}">
                    {{ $summary['competency_met'] ? 'MEMENUHI' : 'BELUM' }}
                </span>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: right; font-weight: bold; background-color: #f9fafb;">
                Status Kelayakan Pengangkatan (Minimal 2 dari 3 Kriteria Terpenuhi):
            </td>
            <td style="text-align: center; background-color: #f9fafb;">
                <span class="{{ $summary['eligible_for_permanent'] ? 'badge-pass' : 'badge-fail' }}">
                    {{ $summary['criteria_met_count'] }}/3 ({{ $summary['eligible_for_permanent'] ? 'LAYAK' : 'BELUM LAYAK' }})
                </span>
            </td>
        </tr>
    </table>

    <!-- I. KESIMPULAN REKOMENDASI -->
    <div class="section-heading">I. KESIMPULAN REKOMENDASI</div>
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
                <div style="margin-bottom: 6px;">
                    <span class="checkbox">{{ $decision === 'terminate_pkwt' ? 'V' : '' }}</span>
                    <strong>Tidak direkomendasikan untuk diangkat sebagai karyawan tetap / masa PKWT diterminasi</strong>, dengan alasan:
                    <u>{{ $decision === 'terminate_pkwt' && filled($record->recommendation_reason) ? $record->recommendation_reason : '………………………………………………………………' }}</u>
                </div>
                <div class="small-note" style="border-top: 1px solid #d1d5db; padding-top: 4px;">
                    *) Kriteria yang harus dipenuhi untuk dapat diangkat sebagai karyawan tetap (setidaknya 2 dari 3 kriteria harus terpenuhi):
                    (1) Pencapaian Goal &ge; 95%, (2) Rata-rata Manajemen Pekerjaan &ge; 3.0, (3) Kompetensi memenuhi requirement &ge; 90%.
                </div>
            </td>
        </tr>
    </table>

    <!-- J. KOLOM PERSETUJUAN (KARYAWAN & PENILAI) -->
    <div class="section-heading">J. KOLOM PERSETUJUAN</div>
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
