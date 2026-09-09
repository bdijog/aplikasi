@extends('reports.pdf.layout')

@section('content')
    <!-- KPI Ringkasan Jadwal -->
    <table class="kpi-table">
        <tr>
            <td style="width: 25%;">
                <div class="kpi-card">
                    <div class="kpi-title">Total Slot Jadwal</div>
                    <div class="kpi-value">{{ $data['summary']['total_schedules'] }} Sesi</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-card" style="background-color: #f0fdf4; border-color: #bbf7d0;">
                    <div class="kpi-title" style="color: #166534;">Jadwal Aktif</div>
                    <div class="kpi-value" style="color: #166534;">{{ $data['summary']['active_schedules'] }}</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-card" style="background-color: #eff6ff; border-color: #bfdbfe;">
                    <div class="kpi-title" style="color: #1e40af;">Dokter Terjadwal</div>
                    <div class="kpi-value" style="color: #1e40af;">{{ $data['summary']['total_doctors'] }} Dokter</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-card" style="background-color: #fdf4ff; border-color: #f5d0fe;">
                    <div class="kpi-title" style="color: #86198f;">Total Kuota Pelayanan</div>
                    <div class="kpi-value" style="color: #86198f;">{{ $data['summary']['total_capacity'] }} Pasien</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabel Roster Jadwal Praktik -->
    <div style="font-weight: bold; font-size: 8.5pt; margin-bottom: 5px; color: #0f766e;">Roster Jadwal Praktik Dokter & Kuota Pelayanan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 28%; text-align: left;">Nama Dokter</th>
                <th style="width: 22%; text-align: left;">Spesialisasi / Poli</th>
                <th style="width: 12%;">Hari / Tanggal</th>
                <th style="width: 14%;">Jam Praktik</th>
                <th style="width: 9%;">Kuota</th>
                <th style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['records'] as $idx => $s)
                @php
                    $doc = $s->doctor;
                    $specialty = is_array($doc?->specialty) ? ($doc->specialty['id'] ?? reset($doc->specialty)) : ($doc?->specialty ?? '-');
                    $dayStr = $s->day_of_week !== null ? ($data['day_names'][$s->day_of_week] ?? '-') : ($s->specific_date?->format('d/m/Y') ?? '-');
                    $timeStr = ($s->start_time ? substr((string)$s->start_time, 0, 5) : '-') . ' - ' . ($s->end_time ? substr((string)$s->end_time, 0, 5) : '-');
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $doc?->name ?? '-' }}</strong></td>
                    <td>{{ $specialty }}</td>
                    <td class="text-center">{{ $dayStr }}</td>
                    <td class="text-center font-bold">{{ $timeStr }}</td>
                    <td class="text-center">{{ $s->max_patients }} Pasien</td>
                    <td class="text-center">
                        <span class="badge {{ $s->status?->value === 'active' ? 'badge-success' : 'badge-danger' }}">
                            {{ $s->status?->getLabel() ?? (string)$s->status }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="color: #64748b;">Tidak ada jadwal dokter yang terdaftar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
