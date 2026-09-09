@extends('reports.pdf.layout')

@section('content')
    <!-- KPI Ringkasan Antrian -->
    <table class="kpi-table">
        <tr>
            <td style="width: 16.6%;">
                <div class="kpi-card">
                    <div class="kpi-title">Total Antrian</div>
                    <div class="kpi-value">{{ $data['summary']['total_tickets'] }}</div>
                </div>
            </td>
            <td style="width: 16.6%;">
                <div class="kpi-card" style="background-color: #f0fdf4; border-color: #bbf7d0;">
                    <div class="kpi-title" style="color: #166534;">Selesai Dilayani</div>
                    <div class="kpi-value" style="color: #166534;">{{ $data['summary']['completed_tickets'] }}</div>
                </div>
            </td>
            <td style="width: 16.6%;">
                <div class="kpi-card" style="background-color: #fefce8; border-color: #fef08a;">
                    <div class="kpi-title" style="color: #854d0e;">Dilewati (Skip)</div>
                    <div class="kpi-value" style="color: #854d0e;">{{ $data['summary']['skipped_tickets'] }}</div>
                </div>
            </td>
            <td style="width: 16.6%;">
                <div class="kpi-card" style="background-color: #eff6ff; border-color: #bfdbfe;">
                    <div class="kpi-title" style="color: #1e40af;">Rata-rata Tunggu</div>
                    <div class="kpi-value" style="color: #1e40af;">{{ $data['summary']['avg_wait_minutes'] }} Mnt</div>
                </div>
            </td>
            <td style="width: 16.6%;">
                <div class="kpi-card" style="background-color: #fef2f2; border-color: #fecaca;">
                    <div class="kpi-title" style="color: #991b1b;">Tunggu Terlama</div>
                    <div class="kpi-value" style="color: #991b1b;">{{ $data['summary']['max_wait_minutes'] }} Mnt</div>
                </div>
            </td>
            <td style="width: 16.6%;">
                <div class="kpi-card">
                    <div class="kpi-title">Rata-rata Layanan</div>
                    <div class="kpi-value">{{ $data['summary']['avg_serve_minutes'] }} Mnt</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabel Rata-rata per Hari -->
    <div style="font-weight: bold; font-size: 8.5pt; margin-bottom: 5px; color: #0f766e;">1. Analisis Waktu Antrian Harian</div>
    <table class="data-table" style="margin-bottom: 12px;">
        <thead>
            <tr>
                <th style="width: 30%; text-align: left;">Hari & Tanggal</th>
                <th style="width: 14%;">Total Tiket</th>
                <th style="width: 14%;">Selesai</th>
                <th style="width: 14%;">Dilewati</th>
                <th style="width: 14%;">Rata-rata Tunggu</th>
                <th style="width: 14%;">Rata-rata Layanan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['by_day'] as $day)
                <tr>
                    <td><strong>{{ $day['formatted_date'] }}</strong></td>
                    <td class="text-center font-bold">{{ $day['total'] }}</td>
                    <td class="text-center">{{ $day['completed'] }}</td>
                    <td class="text-center">{{ $day['skipped'] }}</td>
                    <td class="text-center font-bold" style="color: #0f766e;">{{ $day['avg_wait_minutes'] }} Menit</td>
                    <td class="text-center">{{ $day['avg_serve_minutes'] }} Menit</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="color: #64748b;">Tidak ada data antrian pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tabel Log Tiket Antrian -->
    <div style="font-weight: bold; font-size: 8.5pt; margin-bottom: 5px; color: #0f766e;">2. Rincian Tiket Antrian Pasien</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">No.</th>
                <th style="width: 9%;">No. Tiket</th>
                <th style="width: 8%;">Tanggal</th>
                <th style="width: 18%; text-align: left;">Pasien</th>
                <th style="width: 18%; text-align: left;">Dokter</th>
                <th style="width: 7%;">Prioritas</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 8%;">Panggil</th>
                <th style="width: 10%;">Tunggu (Mnt)</th>
                <th style="width: 10%;">Layanan (Mnt)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['records'] as $idx => $ticket)
                @php
                    $checkIn = $ticket->appointment?->checked_in_at ?? $ticket->created_at;
                    $wait = ($checkIn && $ticket->called_at && $ticket->called_at->greaterThanOrEqualTo($checkIn))
                        ? $checkIn->diffInMinutes($ticket->called_at)
                        : '-';
                    $serve = ($ticket->completed_at && $ticket->served_at && $ticket->completed_at->greaterThanOrEqualTo($ticket->served_at))
                        ? $ticket->served_at->diffInMinutes($ticket->completed_at)
                        : '-';
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center font-bold">{{ $ticket->display_number }}</td>
                    <td class="text-center">{{ $ticket->queue_date?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $ticket->appointment?->patient?->name ?? '-' }}</td>
                    <td>{{ $ticket->doctor?->name ?? '-' }}</td>
                    <td class="text-center">{{ $ticket->priority?->getLabel() ?? (string)$ticket->priority }}</td>
                    <td class="text-center">
                        <span class="badge {{ match($ticket->status?->value ?? (string)$ticket->status) {
                            'completed' => 'badge-success',
                            'skipped', 'cancelled' => 'badge-danger',
                            'serving' => 'badge-warning',
                            default => 'badge-info'
                        } }}">
                            {{ $ticket->status?->getLabel() ?? (string)$ticket->status }}
                        </span>
                    </td>
                    <td class="text-center">{{ $ticket->called_at?->format('H:i:s') ?? '-' }}</td>
                    <td class="text-center font-bold">{{ $wait }}</td>
                    <td class="text-center font-bold">{{ $serve }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="color: #64748b;">Tidak ada tiket antrian yang ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
