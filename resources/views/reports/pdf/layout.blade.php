<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Laporan Klinik' }}</title>
    <style>
        @page {
            margin: 1.2cm 1.2cm 1.5cm 1.2cm;
        }
        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            color: #1e293b;
            line-height: 1.35;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .clinic-name {
            font-size: 15pt;
            font-weight: bold;
            color: #0f766e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .clinic-subtitle {
            font-size: 8.5pt;
            color: #475569;
            margin-top: 2px;
        }
        .clinic-address {
            font-size: 7.5pt;
            color: #64748b;
            margin-top: 2px;
        }
        .divider {
            border-top: 2px solid #0f766e;
            border-bottom: 1px solid #cbd5e1;
            height: 3px;
            margin-bottom: 12px;
        }
        .report-title-box {
            text-align: center;
            margin-bottom: 14px;
        }
        .report-title {
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .report-period {
            font-size: 8.5pt;
            color: #475569;
            margin-top: 3px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 8pt;
        }
        .meta-table td {
            padding: 2px 4px;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 7pt;
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef9c3; color: #854d0e; }
        .badge-danger  { background: #fee2e2; color: #991b1b; }
        .badge-info    { background: #e0f2fe; color: #075985; }
        .badge-neutral { background: #f1f5f9; color: #475569; }

        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 7.5pt;
        }
        .data-table th {
            background-color: #0f766e;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            padding: 5px 4px;
            border: 1px solid #0d5f58;
        }
        .data-table td {
            padding: 4px 4px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }

        /* KPI Card Grid */
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .kpi-card {
            background-color: #f0fdfa;
            border: 1px solid #99f6e4;
            border-radius: 4px;
            padding: 6px 8px;
            text-align: center;
        }
        .kpi-title {
            font-size: 7pt;
            color: #0f766e;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kpi-value {
            font-size: 11pt;
            font-weight: bold;
            color: #115e59;
            margin-top: 2px;
        }

        /* Signatures */
        .signatures {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .signatures td {
            border: none;
            vertical-align: top;
        }
        .sign-box {
            width: 45%;
            text-align: center;
            font-size: 8pt;
        }
        .sign-space {
            height: 45px;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 20px;
            font-size: 7pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    <!-- Fixed Footer on Every Page (must be at top of body for Dompdf) -->
    <div class="footer">
        <table style="width: 100%; border: none; border-collapse: collapse;">
            <tr>
                <td style="border: none; padding: 0; text-align: left; font-size: 7pt; color: #94a3b8; background: transparent;">
                    Klinik Ayo Sehat &copy; {{ date('Y') }} &bull; Sistem Informasi Manajemen Jadwal Dokter
                </td>
                <td style="border: none; padding: 0; text-align: right; font-size: 7pt; color: #94a3b8; background: transparent;">
                    Dicetak secara otomatis oleh sistem
                </td>
            </tr>
        </table>
    </div>

    <!-- Header / Kop Surat -->
    <table class="header-table">
        <tr>
            <td style="width: 70%;">
                <div class="clinic-name">Klinik Ayo Sehat</div>
                <div class="clinic-subtitle">Sistem Informasi Manajemen Jadwal, Antrian & Pelayanan Pasien</div>
                <div class="clinic-address">Jl. Kesehatan Medika No. 123, Kotabaru, D.I. Yogyakarta | Telp: (0274) 555-0123 | Email: layanan@klinikayosehat.id</div>
            </td>
            <td style="width: 30%; text-align: right; font-size: 7.5pt; color: #64748b;">
                <strong>Dokumen Resmi</strong><br>
                Tanggal Cetak: {{ $generatedAt ?? now()->format('d/m/Y H:i') }}<br>
                Status: Terverifikasi Sistem
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Judul Laporan -->
    <div class="report-title-box">
        <div class="report-title">{{ $title }}</div>
        @if(!empty($period))
            <div class="report-period">Periode: <strong>{{ $period }}</strong></div>
        @endif
    </div>

    <!-- Content Slot -->
    @yield('content')

    <!-- Signatures -->
    <table class="signatures">
        <tr>
            <td class="sign-box">
                <div>Yogyakarta, {{ now()->translatedFormat('d F Y') }}</div>
                <div style="margin-top: 3px;">Petugas Loket / Administrasi,</div>
                <div class="sign-space"></div>
                <div class="font-bold">( Staf Front Office )</div>
                <div style="color: #64748b; font-size: 7pt;">Klinik Ayo Sehat</div>
            </td>
            <td style="width: 10%; border: none;"></td>
            <td class="sign-box">
                <div>Mengetahui,</div>
                <div style="margin-top: 3px;">Pimpinan / Kepala Pelayanan Medis,</div>
                <div class="sign-space"></div>
                <div class="font-bold">( dr. Penanggung Jawab )</div>
                <div style="color: #64748b; font-size: 7pt;">SIP: 503/SIP/DKS/2026</div>
            </td>
        </tr>
    </table>

</body>
</html>
