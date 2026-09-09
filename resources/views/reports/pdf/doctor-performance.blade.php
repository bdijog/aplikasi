@extends('reports.pdf.layout')

@section('content')
    <!-- KPI Ringkasan Kinerja Dokter -->
    <table class="kpi-table">
        <tr>
            <td style="width: 20%;">
                <div class="kpi-card">
                    <div class="kpi-title">Total Dokter Bertugas</div>
                    <div class="kpi-value">{{ $data['summary']['total_doctors'] }} Dokter</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-card">
                    <div class="kpi-title">Total Janji Temu</div>
                    <div class="kpi-value">{{ $data['summary']['total_patients'] }} Pasien</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-card" style="background-color: #f0fdf4; border-color: #bbf7d0;">
                    <div class="kpi-title" style="color: #166534;">Konsultasi Selesai</div>
                    <div class="kpi-value" style="color: #166534;">{{ $data['summary']['total_completed'] }}</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-card" style="background-color: #eff6ff; border-color: #bfdbfe;">
                    <div class="kpi-title" style="color: #1e40af;">Rata-rata Konsultasi</div>
                    <div class="kpi-value" style="color: #1e40af;">{{ $data['summary']['avg_consult_overall'] }} Menit</div>
                </div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-card" style="background-color: #fefce8; border-color: #fef08a;">
                    <div class="kpi-title" style="color: #854d0e;">Rata-rata Waktu Tunggu</div>
                    <div class="kpi-value" style="color: #854d0e;">{{ $data['summary']['avg_wait_overall'] }} Menit</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabel Detail Kinerja per Dokter -->
    <div style="font-weight: bold; font-size: 8.5pt; margin-bottom: 5px; color: #0f766e;">Detail Efisiensi Layanan & Kehadiran Pasien per Dokter</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">No.</th>
                <th style="width: 22%; text-align: left;">Nama Dokter</th>
                <th style="width: 14%; text-align: left;">No. STR / SIP</th>
                <th style="width: 16%; text-align: left;">Spesialisasi</th>
                <th style="width: 7%;">Total Janji</th>
                <th style="width: 7%;">Selesai</th>
                <th style="width: 7%;">Batal</th>
                <th style="width: 7%;">No-Show</th>
                <th style="width: 8%;">% No-Show</th>
                <th style="width: 8%;">Rata-rata Konsul</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['doctors'] as $idx => $doc)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $doc['name'] }}</strong></td>
                    <td>{{ $doc['license_number'] ?? '-' }}</td>
                    <td>{{ $doc['specialty'] }}</td>
                    <td class="text-center font-bold">{{ $doc['total_appointments'] }}</td>
                    <td class="text-center">{{ $doc['completed'] }}</td>
                    <td class="text-center">{{ $doc['cancelled'] }}</td>
                    <td class="text-center">{{ $doc['no_show'] }}</td>
                    <td class="text-center font-bold {{ $doc['no_show_rate'] > 15 ? 'text-danger' : '' }}" style="{{ $doc['no_show_rate'] > 15 ? 'color: #dc2626;' : '' }}">
                        {{ $doc['no_show_rate'] }}%
                    </td>
                    <td class="text-center font-bold" style="color: #0d9488;">
                        {{ $doc['avg_consult_minutes'] }} Menit
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="color: #64748b;">Tidak ada data dokter pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
