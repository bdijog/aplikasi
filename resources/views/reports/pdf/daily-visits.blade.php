@extends('reports.pdf.layout')

@section('content')
    <!-- KPI Ringkasan Status -->
    <table class="kpi-table">
        <tr>
            <td style="width: 12.5%;">
                <div class="kpi-card">
                    <div class="kpi-title">Total Janji Temu</div>
                    <div class="kpi-value">{{ $data['summary']['total'] }}</div>
                </div>
            </td>
            <td style="width: 12.5%;">
                <div class="kpi-card" style="background-color: #f0fdf4; border-color: #bbf7d0;">
                    <div class="kpi-title" style="color: #166534;">Selesai</div>
                    <div class="kpi-value" style="color: #166534;">{{ $data['summary']['completed'] }}</div>
                </div>
            </td>
            <td style="width: 12.5%;">
                <div class="kpi-card">
                    <div class="kpi-title">Checked-In</div>
                    <div class="kpi-value">{{ $data['summary']['checked_in'] }}</div>
                </div>
            </td>
            <td style="width: 12.5%;">
                <div class="kpi-card">
                    <div class="kpi-title">Dalam Layanan</div>
                    <div class="kpi-value">{{ $data['summary']['in_progress'] }}</div>
                </div>
            </td>
            <td style="width: 12.5%;">
                <div class="kpi-card">
                    <div class="kpi-title">Terkonfirmasi</div>
                    <div class="kpi-value">{{ $data['summary']['confirmed'] }}</div>
                </div>
            </td>
            <td style="width: 12.5%;">
                <div class="kpi-card" style="background-color: #fefce8; border-color: #fef08a;">
                    <div class="kpi-title" style="color: #854d0e;">Pending</div>
                    <div class="kpi-value" style="color: #854d0e;">{{ $data['summary']['pending'] }}</div>
                </div>
            </td>
            <td style="width: 12.5%;">
                <div class="kpi-card" style="background-color: #fef2f2; border-color: #fecaca;">
                    <div class="kpi-title" style="color: #991b1b;">Batal</div>
                    <div class="kpi-value" style="color: #991b1b;">{{ $data['summary']['cancelled'] }}</div>
                </div>
            </td>
            <td style="width: 12.5%;">
                <div class="kpi-card" style="background-color: #fef2f2; border-color: #fecaca;">
                    <div class="kpi-title" style="color: #991b1b;">No-Show</div>
                    <div class="kpi-value" style="color: #991b1b;">{{ $data['summary']['no_show'] }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabel Rincian per Dokter -->
    <div style="font-weight: bold; font-size: 8.5pt; margin-bottom: 5px; color: #0f766e;">1. Rekapitulasi Pasien per Dokter</div>
    <table class="data-table" style="margin-bottom: 12px;">
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 35%; text-align: left;">Nama Dokter</th>
                <th style="width: 25%; text-align: left;">Spesialisasi / Poli</th>
                <th style="width: 10%;">Total Pasien</th>
                <th style="width: 8%;">Selesai</th>
                <th style="width: 8%;">Batal</th>
                <th style="width: 9%;">No-Show</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['by_doctor'] as $idx => $d)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ $d['doctor_name'] }}</td>
                    <td>{{ $d['specialty'] }}</td>
                    <td class="text-center font-bold">{{ $d['total'] }}</td>
                    <td class="text-center">{{ $d['completed'] }}</td>
                    <td class="text-center">{{ $d['cancelled'] }}</td>
                    <td class="text-center">{{ $d['no_show'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="color: #64748b;">Tidak ada data kunjungan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tabel Detail Janji Temu Pasien -->
    <div style="font-weight: bold; font-size: 8.5pt; margin-bottom: 5px; color: #0f766e;">2. Daftar Detail Janji Temu Pasien</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;">No.</th>
                <th style="width: 12%;">Kode Booking</th>
                <th style="width: 8%;">Tanggal</th>
                <th style="width: 6%;">Jam</th>
                <th style="width: 16%; text-align: left;">Nama Pasien</th>
                <th style="width: 10%;">No. RM</th>
                <th style="width: 16%; text-align: left;">Dokter</th>
                <th style="width: 9%;">Kunjungan</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Check-In</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['records'] as $idx => $rec)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center font-bold">{{ $rec->booking_code }}</td>
                    <td class="text-center">{{ $rec->appointment_date?->format('d/m/Y') ?? '-' }}</td>
                    <td class="text-center">{{ $rec->estimated_time ? substr((string)$rec->estimated_time, 0, 5) : '-' }}</td>
                    <td>{{ $rec->patient?->name ?? '-' }}</td>
                    <td class="text-center">{{ $rec->patient?->medical_record_number ?? '-' }}</td>
                    <td>{{ $rec->doctor?->name ?? '-' }}</td>
                    <td class="text-center">{{ $rec->visit_type?->getLabel() ?? (string)$rec->visit_type }}</td>
                    <td class="text-center">
                        <span class="badge {{ match($rec->status?->value ?? (string)$rec->status) {
                            'completed' => 'badge-success',
                            'cancelled', 'no_show' => 'badge-danger',
                            'pending' => 'badge-warning',
                            default => 'badge-info'
                        } }}">
                            {{ $rec->status?->getLabel() ?? (string)$rec->status }}
                        </span>
                    </td>
                    <td class="text-center">{{ $rec->checked_in_at?->format('H:i:s') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="color: #64748b;">Tidak ada data pasien yang ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
