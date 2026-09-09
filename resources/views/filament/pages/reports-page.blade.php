<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Panel Kontrol Filter & Pilihan Laporan --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="mb-4 flex items-center justify-between border-b border-gray-100 pb-3 dark:border-white/5">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                        Konfigurasi & Filter Laporan
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Pilih jenis laporan dan sesuaikan parameter tanggal untuk melihat pratinjau serta mengunduh dokumen.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        wire:click="downloadExcel"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/50"
                    >
                        <x-filament::icon icon="heroicon-o-arrow-down-tray" class="h-4 w-4" />
                        <span>Unduh Excel (.xlsx)</span>
                    </button>

                    <button
                        type="button"
                        wire:click="downloadPdf"
                        class="inline-flex items-center gap-1.5 rounded-lg bg-rose-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500/50"
                    >
                        <x-filament::icon icon="heroicon-o-document-arrow-down" class="h-4 w-4" />
                        <span>Unduh PDF (.pdf)</span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                {{-- 1. Pilihan Tipe Laporan --}}
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700 dark:text-gray-300">
                        Tipe Laporan Berkala
                    </label>
                    <select
                        wire:model.live="report_type"
                        class="w-full rounded-lg border-gray-300 text-xs shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-white/10 dark:bg-gray-800 dark:text-white"
                    >
                        <option value="daily_visits">Rekap Kunjungan Harian / Periodik</option>
                        <option value="monthly_stats">Statistik Kunjungan Bulanan</option>
                        <option value="doctor_performance">Kinerja Dokter & Waktu Konsultasi</option>
                        <option value="queue_wait_time">Analisis Antrian & Waktu Tunggu</option>
                        <option value="doctor_schedules">Roster Jadwal Praktik Dokter</option>
                    </select>
                </div>

                {{-- 2. Filter Tanggal atau Bulan --}}
                @if($report_type === 'monthly_stats')
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-700 dark:text-gray-300">
                            Bulan
                        </label>
                        <select
                            wire:model.live="month"
                            class="w-full rounded-lg border-gray-300 text-xs shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-white/10 dark:bg-gray-800 dark:text-white"
                        >
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}">{{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}</option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-700 dark:text-gray-300">
                            Tahun
                        </label>
                        <select
                            wire:model.live="year"
                            class="w-full rounded-lg border-gray-300 text-xs shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-white/10 dark:bg-gray-800 dark:text-white"
                        >
                            @for($y = (int)date('Y') - 1; $y <= (int)date('Y') + 1; $y++)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                @elseif($report_type !== 'doctor_schedules')
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-700 dark:text-gray-300">
                            Dari Tanggal
                        </label>
                        <input
                            type="date"
                            wire:model.live="start_date"
                            class="w-full rounded-lg border-gray-300 text-xs shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-white/10 dark:bg-gray-800 dark:text-white"
                        />
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-700 dark:text-gray-300">
                            Sampai Tanggal
                        </label>
                        <input
                            type="date"
                            wire:model.live="end_date"
                            class="w-full rounded-lg border-gray-300 text-xs shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-white/10 dark:bg-gray-800 dark:text-white"
                        />
                    </div>
                @endif

                {{-- 3. Filter Dokter --}}
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700 dark:text-gray-300">
                        Dokter (Opsional)
                    </label>
                    <select
                        wire:model.live="doctor_id"
                        class="w-full rounded-lg border-gray-300 text-xs shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-white/10 dark:bg-gray-800 dark:text-white"
                    >
                        <option value="">Semua Dokter</option>
                        @foreach($this->doctorsList as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 4. Filter Status (Jika relevan) --}}
                @if(in_array($report_type, ['daily_visits', 'doctor_schedules']))
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-700 dark:text-gray-300">
                            Status
                        </label>
                        <select
                            wire:model.live="status"
                            class="w-full rounded-lg border-gray-300 text-xs shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-white/10 dark:bg-gray-800 dark:text-white"
                        >
                            <option value="">Semua Status</option>
                            @if($report_type === 'daily_visits')
                                @foreach($this->statusList as $val => $lbl)
                                    <option value="{{ $val }}">{{ $lbl }}</option>
                                @endforeach
                            @else
                                <option value="active">Aktif</option>
                                <option value="inactive">Tidak Aktif</option>
                                <option value="cancelled">Dibatalkan</option>
                            @endif
                        </select>
                    </div>
                @endif
            </div>

            <div class="mt-4 flex items-center justify-end">
                <button
                    type="button"
                    wire:click="generatePreview"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-4 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    <x-filament::icon icon="heroicon-o-arrow-path" class="h-4 w-4" />
                    <span>Perbarui Pratinjau</span>
                </button>
            </div>
        </div>

        {{-- AREA PRATINJAU DATA --}}
        @if(!empty($previewData))
            <div class="space-y-6">

                {{-- Preview 1: Daily Visits --}}
                @if($report_type === 'daily_visits')
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-8">
                        <div class="rounded-lg border border-teal-200 bg-teal-50 p-3 text-center dark:border-teal-900/50 dark:bg-teal-950/30">
                            <div class="text-[10px] font-bold uppercase text-teal-800 dark:text-teal-300">Total Janji</div>
                            <div class="mt-1 text-xl font-bold text-teal-950 dark:text-teal-100">{{ $previewData['summary']['total'] }}</div>
                        </div>
                        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-center dark:border-emerald-900/50 dark:bg-emerald-950/30">
                            <div class="text-[10px] font-bold uppercase text-emerald-800 dark:text-emerald-300">Selesai</div>
                            <div class="mt-1 text-xl font-bold text-emerald-950 dark:text-emerald-100">{{ $previewData['summary']['completed'] }}</div>
                        </div>
                        <div class="rounded-lg border border-blue-200 bg-blue-50 p-3 text-center dark:border-blue-900/50 dark:bg-blue-950/30">
                            <div class="text-[10px] font-bold uppercase text-blue-800 dark:text-blue-300">Checked-In</div>
                            <div class="mt-1 text-xl font-bold text-blue-950 dark:text-blue-100">{{ $previewData['summary']['checked_in'] }}</div>
                        </div>
                        <div class="rounded-lg border border-indigo-200 bg-indigo-50 p-3 text-center dark:border-indigo-900/50 dark:bg-indigo-950/30">
                            <div class="text-[10px] font-bold uppercase text-indigo-800 dark:text-indigo-300">Dalam Layanan</div>
                            <div class="mt-1 text-xl font-bold text-indigo-950 dark:text-indigo-100">{{ $previewData['summary']['in_progress'] }}</div>
                        </div>
                        <div class="rounded-lg border border-sky-200 bg-sky-50 p-3 text-center dark:border-sky-900/50 dark:bg-sky-950/30">
                            <div class="text-[10px] font-bold uppercase text-sky-800 dark:text-sky-300">Terkonfirmasi</div>
                            <div class="mt-1 text-xl font-bold text-sky-950 dark:text-sky-100">{{ $previewData['summary']['confirmed'] }}</div>
                        </div>
                        <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-center dark:border-amber-900/50 dark:bg-amber-950/30">
                            <div class="text-[10px] font-bold uppercase text-amber-800 dark:text-amber-300">Pending</div>
                            <div class="mt-1 text-xl font-bold text-amber-950 dark:text-amber-100">{{ $previewData['summary']['pending'] }}</div>
                        </div>
                        <div class="rounded-lg border border-rose-200 bg-rose-50 p-3 text-center dark:border-rose-900/50 dark:bg-rose-950/30">
                            <div class="text-[10px] font-bold uppercase text-rose-800 dark:text-rose-300">Dibatalkan</div>
                            <div class="mt-1 text-xl font-bold text-rose-950 dark:text-rose-100">{{ $previewData['summary']['cancelled'] }}</div>
                        </div>
                        <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-center dark:border-red-900/50 dark:bg-red-950/30">
                            <div class="text-[10px] font-bold uppercase text-red-800 dark:text-red-300">No-Show</div>
                            <div class="mt-1 text-xl font-bold text-red-950 dark:text-red-100">{{ $previewData['summary']['no_show'] }}</div>
                        </div>
                    </div>

                    {{-- Tabel Pratinjau 15 baris teratas --}}
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">
                        <div class="border-b border-gray-100 px-5 py-3 dark:border-white/5">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                                Pratinjau Data Janji Temu (Menampilkan maksimal 15 dari {{ count($previewData['records']) }} baris)
                            </h4>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-gray-50 text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                    <tr>
                                        <th class="px-4 py-2.5">Booking Code</th>
                                        <th class="px-4 py-2.5">Tanggal & Jam</th>
                                        <th class="px-4 py-2.5">Pasien</th>
                                        <th class="px-4 py-2.5">Dokter Pemeriksa</th>
                                        <th class="px-4 py-2.5">Tipe Kunjungan</th>
                                        <th class="px-4 py-2.5">Status</th>
                                        <th class="px-4 py-2.5">Check-In</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                    @forelse($previewData['records']->take(15) as $rec)
                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                                            <td class="px-4 py-2 font-mono font-semibold text-teal-600 dark:text-teal-400">{{ $rec->booking_code }}</td>
                                            <td class="px-4 py-2">{{ $rec->appointment_date?->format('d/m/Y') }} {{ $rec->estimated_time ? substr((string)$rec->estimated_time, 0, 5) : '' }}</td>
                                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">{{ $rec->patient?->name }}</td>
                                            <td class="px-4 py-2">{{ $rec->doctor?->name }}</td>
                                            <td class="px-4 py-2">{{ $rec->visit_type?->getLabel() ?? (string)$rec->visit_type }}</td>
                                            <td class="px-4 py-2">
                                                <span class="inline-flex rounded px-2 py-0.5 text-[10px] font-semibold {{ match($rec->status?->value ?? (string)$rec->status) {
                                                    'completed' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300',
                                                    'cancelled', 'no_show' => 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300',
                                                    default => 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300'
                                                } }}">
                                                    {{ $rec->status?->getLabel() ?? (string)$rec->status }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2">{{ $rec->checked_in_at?->format('H:i:s') ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">Tidak ada data janji temu yang cocok dengan filter.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                {{-- Preview 2: Monthly Stats --}}
                @elseif($report_type === 'monthly_stats')
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                        <div class="rounded-xl border border-teal-200 bg-teal-50/50 p-4 dark:border-teal-900/40 dark:bg-teal-950/20">
                            <div class="text-xs font-semibold text-teal-700 dark:text-teal-300">Total Kunjungan Bulan Ini</div>
                            <div class="mt-1 text-2xl font-bold text-teal-900 dark:text-white">{{ $previewData['total_visits'] }}</div>
                        </div>
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-4 dark:border-emerald-900/40 dark:bg-emerald-950/20">
                            <div class="text-xs font-semibold text-emerald-700 dark:text-emerald-300">Selesai Dilayani</div>
                            <div class="mt-1 text-2xl font-bold text-emerald-900 dark:text-white">{{ $previewData['completed_visits'] }}</div>
                        </div>
                        <div class="rounded-xl border border-blue-200 bg-blue-50/50 p-4 dark:border-blue-900/40 dark:bg-blue-950/20">
                            <div class="text-xs font-semibold text-blue-700 dark:text-blue-300">Pasien Baru (New Visit)</div>
                            <div class="mt-1 text-2xl font-bold text-blue-900 dark:text-white">{{ $previewData['new_visits'] }} <span class="text-xs font-normal">({{ $previewData['new_ratio'] }}%)</span></div>
                        </div>
                        <div class="rounded-xl border border-purple-200 bg-purple-50/50 p-4 dark:border-purple-900/40 dark:bg-purple-950/20">
                            <div class="text-xs font-semibold text-purple-700 dark:text-purple-300">Kontrol (Follow-up)</div>
                            <div class="mt-1 text-2xl font-bold text-purple-900 dark:text-white">{{ $previewData['follow_ups'] }} <span class="text-xs font-normal">({{ $previewData['follow_up_ratio'] }}%)</span></div>
                        </div>
                    </div>

                    {{-- Tabel Spesialisasi --}}
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">
                        <div class="border-b border-gray-100 px-5 py-3 dark:border-white/5">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Distribusi per Poli / Spesialisasi Medis</h4>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-gray-50 text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                    <tr>
                                        <th class="px-4 py-2.5">Spesialisasi / Poli</th>
                                        <th class="px-4 py-2.5 text-center">Jumlah Pasien</th>
                                        <th class="px-4 py-2.5 text-center">Persentase</th>
                                        <th class="px-4 py-2.5 text-center">Selesai Dilayani</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                    @foreach($previewData['by_specialty'] as $spec)
                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">{{ $spec['specialty'] }}</td>
                                            <td class="px-4 py-2 text-center font-bold">{{ $spec['total'] }}</td>
                                            <td class="px-4 py-2 text-center">{{ $spec['percentage'] }}%</td>
                                            <td class="px-4 py-2 text-center">{{ $spec['completed'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                {{-- Preview 3: Doctor Performance --}}
                @elseif($report_type === 'doctor_performance')
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                        <div class="rounded-xl border border-teal-200 bg-teal-50/50 p-4 dark:border-teal-900/40 dark:bg-teal-950/20">
                            <div class="text-xs font-semibold text-teal-700 dark:text-teal-300">Total Janji Temu</div>
                            <div class="mt-1 text-2xl font-bold text-teal-900 dark:text-white">{{ $previewData['summary']['total_patients'] }}</div>
                        </div>
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-4 dark:border-emerald-900/40 dark:bg-emerald-950/20">
                            <div class="text-xs font-semibold text-emerald-700 dark:text-emerald-300">Konsultasi Selesai</div>
                            <div class="mt-1 text-2xl font-bold text-emerald-900 dark:text-white">{{ $previewData['summary']['total_completed'] }}</div>
                        </div>
                        <div class="rounded-xl border border-indigo-200 bg-indigo-50/50 p-4 dark:border-indigo-900/40 dark:bg-indigo-950/20">
                            <div class="text-xs font-semibold text-indigo-700 dark:text-indigo-300">Rata-rata Durasi Konsultasi</div>
                            <div class="mt-1 text-2xl font-bold text-indigo-900 dark:text-white">{{ $previewData['summary']['avg_consult_overall'] }} <span class="text-xs font-normal">Menit</span></div>
                        </div>
                        <div class="rounded-xl border border-amber-200 bg-amber-50/50 p-4 dark:border-amber-900/40 dark:bg-amber-950/20">
                            <div class="text-xs font-semibold text-amber-700 dark:text-amber-300">Rata-rata Waktu Tunggu Pasien</div>
                            <div class="mt-1 text-2xl font-bold text-amber-900 dark:text-white">{{ $previewData['summary']['avg_wait_overall'] }} <span class="text-xs font-normal">Menit</span></div>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">
                        <div class="border-b border-gray-100 px-5 py-3 dark:border-white/5">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Tabel Kinerja & Efisiensi Pelayanan Dokter</h4>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-gray-50 text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                    <tr>
                                        <th class="px-4 py-2.5">Nama Dokter</th>
                                        <th class="px-4 py-2.5">Spesialisasi</th>
                                        <th class="px-4 py-2.5 text-center">Total Pasien</th>
                                        <th class="px-4 py-2.5 text-center">Selesai</th>
                                        <th class="px-4 py-2.5 text-center">No-Show (%)</th>
                                        <th class="px-4 py-2.5 text-center">Rata-rata Konsultasi</th>
                                        <th class="px-4 py-2.5 text-center">Rata-rata Tunggu</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                    @foreach($previewData['doctors'] as $d)
                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">{{ $d['name'] }}</td>
                                            <td class="px-4 py-2">{{ $d['specialty'] }}</td>
                                            <td class="px-4 py-2 text-center font-bold">{{ $d['total_appointments'] }}</td>
                                            <td class="px-4 py-2 text-center">{{ $d['completed'] }}</td>
                                            <td class="px-4 py-2 text-center font-semibold {{ $d['no_show_rate'] > 15 ? 'text-rose-600' : 'text-emerald-600' }}">{{ $d['no_show_rate'] }}%</td>
                                            <td class="px-4 py-2 text-center font-bold text-teal-600 dark:text-teal-400">{{ $d['avg_consult_minutes'] }} Menit</td>
                                            <td class="px-4 py-2 text-center">{{ $d['avg_wait_minutes'] }} Menit</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                {{-- Preview 4: Queue & Wait Time --}}
                @elseif($report_type === 'queue_wait_time')
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                        <div class="rounded-lg border border-teal-200 bg-teal-50 p-3 text-center dark:border-teal-900/50 dark:bg-teal-950/30">
                            <div class="text-[10px] font-bold uppercase text-teal-800 dark:text-teal-300">Total Tiket</div>
                            <div class="mt-1 text-xl font-bold text-teal-950 dark:text-teal-100">{{ $previewData['summary']['total_tickets'] }}</div>
                        </div>
                        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-center dark:border-emerald-900/50 dark:bg-emerald-950/30">
                            <div class="text-[10px] font-bold uppercase text-emerald-800 dark:text-emerald-300">Selesai</div>
                            <div class="mt-1 text-xl font-bold text-emerald-950 dark:text-emerald-100">{{ $previewData['summary']['completed_tickets'] }}</div>
                        </div>
                        <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-center dark:border-amber-900/50 dark:bg-amber-950/30">
                            <div class="text-[10px] font-bold uppercase text-amber-800 dark:text-amber-300">Dilewati</div>
                            <div class="mt-1 text-xl font-bold text-amber-950 dark:text-amber-100">{{ $previewData['summary']['skipped_tickets'] }}</div>
                        </div>
                        <div class="rounded-lg border border-blue-200 bg-blue-50 p-3 text-center dark:border-blue-900/50 dark:bg-blue-950/30">
                            <div class="text-[10px] font-bold uppercase text-blue-800 dark:text-blue-300">Rata-rata Tunggu</div>
                            <div class="mt-1 text-xl font-bold text-blue-950 dark:text-blue-100">{{ $previewData['summary']['avg_wait_minutes'] }} <span class="text-xs">Mnt</span></div>
                        </div>
                        <div class="rounded-lg border border-rose-200 bg-rose-50 p-3 text-center dark:border-rose-900/50 dark:bg-rose-950/30">
                            <div class="text-[10px] font-bold uppercase text-rose-800 dark:text-rose-300">Tunggu Terlama</div>
                            <div class="mt-1 text-xl font-bold text-rose-950 dark:text-rose-100">{{ $previewData['summary']['max_wait_minutes'] }} <span class="text-xs">Mnt</span></div>
                        </div>
                        <div class="rounded-lg border border-indigo-200 bg-indigo-50 p-3 text-center dark:border-indigo-900/50 dark:bg-indigo-950/30">
                            <div class="text-[10px] font-bold uppercase text-indigo-800 dark:text-indigo-300">Rata-rata Layanan</div>
                            <div class="mt-1 text-xl font-bold text-indigo-950 dark:text-indigo-100">{{ $previewData['summary']['avg_serve_minutes'] }} <span class="text-xs">Mnt</span></div>
                        </div>
                    </div>

                {{-- Preview 5: Doctor Schedules --}}
                @elseif($report_type === 'doctor_schedules')
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                        <div class="rounded-xl border border-teal-200 bg-teal-50/50 p-4 dark:border-teal-900/40 dark:bg-teal-950/20">
                            <div class="text-xs font-semibold text-teal-700 dark:text-teal-300">Total Slot Jadwal</div>
                            <div class="mt-1 text-2xl font-bold text-teal-900 dark:text-white">{{ $previewData['summary']['total_schedules'] }} Sesi</div>
                        </div>
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-4 dark:border-emerald-900/40 dark:bg-emerald-950/20">
                            <div class="text-xs font-semibold text-emerald-700 dark:text-emerald-300">Jadwal Aktif</div>
                            <div class="mt-1 text-2xl font-bold text-emerald-900 dark:text-white">{{ $previewData['summary']['active_schedules'] }}</div>
                        </div>
                        <div class="rounded-xl border border-blue-200 bg-blue-50/50 p-4 dark:border-blue-900/40 dark:bg-blue-950/20">
                            <div class="text-xs font-semibold text-blue-700 dark:text-blue-300">Dokter Terjadwal</div>
                            <div class="mt-1 text-2xl font-bold text-blue-900 dark:text-white">{{ $previewData['summary']['total_doctors'] }} Dokter</div>
                        </div>
                        <div class="rounded-xl border border-purple-200 bg-purple-50/50 p-4 dark:border-purple-900/40 dark:bg-purple-950/20">
                            <div class="text-xs font-semibold text-purple-700 dark:text-purple-300">Total Kuota Pelayanan</div>
                            <div class="mt-1 text-2xl font-bold text-purple-900 dark:text-white">{{ $previewData['summary']['total_capacity'] }} Pasien</div>
                        </div>
                    </div>
                @endif

            </div>
        @endif

    </div>
</x-filament-panels::page>
