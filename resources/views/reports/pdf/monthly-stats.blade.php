@extends('reports.pdf.layout')

@section('content')
    <!-- KPI Ringkasan Rasio Kunjungan -->
    <table class="kpi-table">
        <tr>
            <td style="width: 25%;">
                <div class="kpi-card">
                    <div class="kpi-title">Total Kunjungan</div>
                    <div class="kpi-value">{{ $data['total_visits'] }} Pasien</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-card" style="background-color: #f0fdf4; border-color: #bbf7d0;">
                    <div class="kpi-title" style="color: #166534;">Kunjungan Selesai</div>
                    <div class="kpi-value" style="color: #166534;">{{ $data['completed_visits'] }}</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-card" style="background-color: #eff6ff; border-color: #bfdbfe;">
                    <div class="kpi-title" style="color: #1e40af;">Pasien Baru (New Visit)</div>
                    <div class="kpi-value" style="color: #1e40af;">{{ $data['new_visits'] }} ({{ $data['new_ratio'] }}%)</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-card" style="background-color: #fdf4ff; border-color: #f5d0fe;">
                    <div class="kpi-title" style="color: #86198f;">Kontrol (Follow-up)</div>
                    <div class="kpi-value" style="color: #86198f;">{{ $data['follow_ups'] }} ({{ $data['follow_up_ratio'] }}%)</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Distribusi Spesialisasi / Poli -->
    <div style="font-weight: bold; font-size: 8.5pt; margin-bottom: 5px; color: #0f766e;">1. Distribusi Kunjungan per Poli / Spesialisasi</div>
    <table class="data-table" style="margin-bottom: 15px;">
        <thead>
            <tr>
                <th style="width: 8%;">No.</th>
                <th style="width: 45%; text-align: left;">Poli / Spesialisasi Medis</th>
                <th style="width: 15%;">Jumlah Pasien</th>
                <th style="width: 16%;">Persentase (%)</th>
                <th style="width: 16%;">Selesai Dilayani</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['by_specialty'] as $idx => $spec)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $spec['specialty'] }}</strong></td>
                    <td class="text-center font-bold">{{ $spec['total'] }}</td>
                    <td class="text-center">{{ $spec['percentage'] }}%</td>
                    <td class="text-center">{{ $spec['completed'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="color: #64748b;">Tidak ada data spesialisasi pada bulan ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Rincian per Dokter -->
    <div style="font-weight: bold; font-size: 8.5pt; margin-bottom: 5px; color: #0f766e;">2. Kinerja & Kontribusi Pasien per Dokter</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 30%; text-align: left;">Nama Dokter</th>
                <th style="width: 20%; text-align: left;">Spesialisasi</th>
                <th style="width: 9%;">Total</th>
                <th style="width: 9%;">Kontribusi</th>
                <th style="width: 9%;">Pasien Baru</th>
                <th style="width: 9%;">Kontrol</th>
                <th style="width: 9%;">Selesai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['by_doctor'] as $idx => $doc)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ $doc['doctor_name'] }}</td>
                    <td>{{ $doc['specialty'] }}</td>
                    <td class="text-center font-bold">{{ $doc['total'] }}</td>
                    <td class="text-center">{{ $doc['percentage'] }}%</td>
                    <td class="text-center">{{ $doc['new_visits'] }}</td>
                    <td class="text-center">{{ $doc['follow_ups'] }}</td>
                    <td class="text-center">{{ $doc['completed'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="color: #64748b;">Tidak ada data dokter pada bulan ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
