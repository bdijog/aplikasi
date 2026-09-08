<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Daftar hak akses (permission) sistem aplikasi klinik:
 * - Disederhanakan menjadi 10 permission praktis berbasis modul/resource
 *   dan aksi operasional lapangan.
 * - Nilai (value) menggunakan snake_case Bahasa Indonesia yang dapat dipakai
 *   langsung sebagai name permission (misal: spatie/laravel-permission).
 */
enum PermissionType: string
{
    // ── Dokter & Jadwal ─────────────────────────────────────────
    case KELOLA_DOKTER = 'kelola_dokter';           // CRUD data master dokter
    case KELOLA_JADWAL = 'kelola_jadwal';           // CRUD jadwal praktik dokter

    // ── Janji Temu & Pasien ─────────────────────────────────────
    case KELOLA_PASIEN = 'kelola_pasien';           // CRUD data identitas & rekam medis pasien
    case KELOLA_APPOINTMENT = 'kelola_appointment'; // CRUD, konfirmasi, pembatalan, & check-in janji temu

    // ── Antrean & Loket ─────────────────────────────────────────
    case KELOLA_COUNTER = 'kelola_counter';         // CRUD master ruangan/loket/poli layanan
    case KELOLA_ANTRIAN = 'kelola_antrian';         // Manajemen data & nomor tiket antrean
    case PANGGIL_ANTRIAN = 'panggil_antrian';       // Operasional panggil, panggil ulang, lewati, & selesaikan antrean

    // ── Informasi & Pengumuman ──────────────────────────────────
    case KELOLA_PENGUMUMAN = 'kelola_pengumuman';   // CRUD pengumuman klinik

    // ── Dashboard & Laporan ─────────────────────────────────────
    case LIHAT_LAPORAN = 'lihat_laporan';           // Akses rekapitulasi data, metrik, & ekspor laporan

    // ── Manajemen Sistem ────────────────────────────────────────
    case KELOLA_PENGGUNA = 'kelola_pengguna';       // CRUD user sistem & konfigurasi hak akses

    /**
     * Label untuk ditampilkan di UI (mis. form checkbox hak akses di Filament).
     */
    public function label(): string
    {
        return match ($this) {
            self::KELOLA_DOKTER => 'Kelola Data Dokter',
            self::KELOLA_JADWAL => 'Kelola Jadwal Praktik',

            self::KELOLA_PASIEN => 'Kelola Data Pasien',
            self::KELOLA_APPOINTMENT => 'Kelola Janji Temu (Appointment)',

            self::KELOLA_COUNTER => 'Kelola Loket & Poli Layanan',
            self::KELOLA_ANTRIAN => 'Kelola Data Antrean',
            self::PANGGIL_ANTRIAN => 'Operasional Pemanggilan Antrean',

            self::KELOLA_PENGUMUMAN => 'Kelola Pengumuman Klinik',

            self::LIHAT_LAPORAN => 'Lihat & Ekspor Laporan',

            self::KELOLA_PENGGUNA => 'Kelola Pengguna & Hak Akses',
        };
    }

    /**
     * Grup/kategori modul permission (untuk dikelompokkan di form Filament).
     */
    public function group(): string
    {
        return match ($this) {
            self::KELOLA_DOKTER,
            self::KELOLA_JADWAL => 'Jadwal & Dokter',

            self::KELOLA_PASIEN,
            self::KELOLA_APPOINTMENT => 'Janji Temu & Pasien',

            self::KELOLA_COUNTER,
            self::KELOLA_ANTRIAN,
            self::PANGGIL_ANTRIAN => 'Antrean Layanan',

            self::KELOLA_PENGUMUMAN => 'Master Informasi',

            self::LIHAT_LAPORAN => 'Laporan & Statistik',

            self::KELOLA_PENGGUNA => 'Manajemen Sistem',
        };
    }

    /**
     * Opsi untuk Filament CheckboxList: ['kelola_dokter' => 'Kelola Data Dokter', ...]
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $permission) => [$permission->value => $permission->label()])
            ->all();
    }

    /**
     * Semua permission dikelompokkan per grup untuk section di form.
     *
     * @return array<string, array<string, string>>
     */
    public static function groupedOptions(): array
    {
        return collect(self::cases())
            ->groupBy(fn (self $permission) => $permission->group())
            ->map(fn ($permissions) => collect($permissions)
                ->mapWithKeys(fn (self $permission) => [$permission->value => $permission->label()])
                ->all())
            ->all();
    }
}
