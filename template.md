# Product Requirements Document (PRD)
## Sistem Informasi Layanan [Nama Instansi]

| | |
|--|--|
| **Nama Sistem** | Sistem Informasi Layanan [Nama Instansi] |
| **Tanggal** | [Tanggal] |
| **Penyusun** | [Nama] — [Jabatan] |
| **Instansi** | [Nama Instansi Lengkap] |

---

## Ringkasan Sistem

> *Jelaskan dalam 2–4 kalimat: apa sistemnya, untuk siapa, dan manfaat utamanya.*

[Nama Instansi] membutuhkan sistem digital terpadu untuk mengelola layanan **[sebutkan: pelatihan / sewa fasilitas / sertifikasi / dll]**. Sistem ini memudahkan klien mendaftar layanan secara online, membantu staf mengelola transaksi, dan memberikan pimpinan laporan real-time. Target: memangkas waktu proses dari **[kondisi saat ini]** menjadi **[kondisi yang diinginkan]**.

---

## Arsitektur & Teknologi

### Stack Teknologi

| Komponen | Teknologi | Versi | Keterangan |
|----------|-----------|-------|------------|
| **Bahasa** | PHP | ≥ 8.3 | Versi minimum yang didukung Laravel 13 |
| **Framework** | [Laravel](https://laravel.com/) | v13 | Full-stack PHP framework |
| **Admin Panel** | [Filament](https://filamentphp.com/) | v5 | UI framework berbasis TALL Stack (Tailwind CSS, Alpine.js, Livewire, Laravel) |
| **Database** | PostgreSQL | ≥ 16 | RDBMS utama untuk seluruh data aplikasi |
| **Frontend** | TALL Stack | — | Tailwind CSS + Alpine.js + Livewire (bawaan Filament) |

### Arsitektur Database — PostgreSQL

> *Gunakan PostgreSQL sebagai satu-satunya RDBMS. Manfaatkan fitur-fitur native PostgreSQL berikut sesuai kebutuhan:*

| Fitur PostgreSQL | Kegunaan dalam Sistem |
|------------------|-----------------------|
| **UUID** (`uuid` / `ulid`) | Primary key yang aman untuk entitas publik (invoice, sertifikat) |
| **JSONB** | Menyimpan data dinamis/metadata fleksibel (contoh: detail konfigurasi layanan) |
| **Full-Text Search** (`tsvector`) | Pencarian cepat pada data peserta, layanan, dan laporan |
| **Enum Types** | Status transaksi (`pending`, `confirmed`, `paid`, `completed`, `cancelled`) |
| **Partial Index** | Index kondisional untuk query yang sering diakses |
| **Foreign Key Constraints** | Integritas referensial antar tabel |
| **Timestamp with Time Zone** | Konsistensi waktu lintas zona (penting untuk instansi multi-lokasi) |

### Struktur Panel Filament v5

> *Filament v5 mendukung multi-panel. Definisikan panel sesuai peran pengguna.*

| Panel | Path | Peran Pengguna | Deskripsi |
|-------|------|----------------|-----------|
| **Admin** | `/admin` | Administrator, Operator | Kelola seluruh data, CRUD Resources, manajemen pengguna |
| **Pimpinan** | `/pimpinan` | Pimpinan / Direktur | Dashboard read-only, laporan, dan statistik |
| **Portal Klien** | `/portal` | Klien Individu, Klien Instansi | Pendaftaran layanan, upload dokumen, cek status, download invoice |

### Komponen Filament v5 yang Digunakan

| Komponen | Fungsi |
|----------|--------|
| **Resources** | CRUD untuk setiap entitas utama (Layanan, Pendaftaran, Peserta, Pembayaran, Sertifikat) |
| **Relation Managers** | Mengelola relasi (contoh: Peserta dalam Pendaftaran, Pembayaran dalam Invoice) |
| **Dashboard Widgets** | Stat widgets, chart widgets untuk metrik dan tren |
| **Actions & Modals** | Konfirmasi aksi, form input dalam modal (contoh: approve pendaftaran, kirim invoice) |
| **Notifications** | Notifikasi in-app untuk perubahan status dan event penting |
| **Tables** | Tabel data dengan filter, sort, search, dan bulk actions |
| **Forms** | Form builder dengan validasi, conditional fields, dan file upload |
| **Infolists** | Tampilan detail read-only untuk halaman informasi |
| **Custom Pages** | Halaman khusus (contoh: kalender ketersediaan, cetak laporan) |

---

## 1. Pengguna Sistem

| Peran | Siapa | Yang Mereka Lakukan | Panel Filament |
|-------|-------|---------------------|----------------|
| **Administrator** | [contoh: Staf TI] | Kelola seluruh data dan hak akses pengguna | Admin |
| **Operator Layanan** | [contoh: Staf loket/administrasi] | Input pendaftaran, konfirmasi, cetak invoice | Admin |
| **Pimpinan** | [contoh: Kepala Balai / Direktur] | Lihat dashboard dan laporan — hanya baca | Pimpinan |
| **Klien Individu** | [contoh: Profesional/peserta perorangan] | Daftar layanan, upload dokumen, cek status | Portal Klien |
| **Klien Instansi** | [contoh: Kepala Bagian SDM] | Daftarkan rombongan, terima invoice kolektif | Portal Klien |

---

## 2. Layanan yang Dikelola Sistem

> *Untuk setiap layanan: jelaskan alurnya dan data apa yang perlu dicatat.*
> *Aturan bisnis penting wajib dituliskan — ini yang akan menjadi validasi di sistem.*

---

### Layanan 1 — [Nama Layanan, contoh: Pelatihan Teknis]

**Deskripsi:** [contoh: Penyelenggaraan pelatihan teknis bagi pegawai instansi lain. Peserta mendaftar, mengikuti pelatihan, dan mendapatkan sertifikat.]

**Alur:**
1. Klien memilih jadwal pelatihan yang tersedia
2. Klien mengisi formulir pendaftaran (nama, asal instansi, jumlah peserta)
3. Operator memverifikasi dan mengkonfirmasi pendaftaran
4. Klien menerima invoice dan melakukan pembayaran
5. Peserta mengikuti pelatihan dan mengisi absensi
6. Sertifikat diterbitkan setelah pelatihan selesai

**Data yang dicatat:** nama peserta · asal instansi · jadwal dipilih · status pembayaran · kehadiran · nomor sertifikat

**Aturan bisnis:**
- Kuota maksimal **[X] peserta** per kelas — jika penuh, pendaftaran otomatis ditutup
- Pembayaran lunas minimal **H-[X]** sebelum pelatihan dimulai
- [Tambahkan aturan lain]

---

### Layanan 2 — [Nama Layanan, contoh: Sewa Fasilitas / Asrama]

**Deskripsi:** [contoh: Penyewaan ruang kelas, aula, dan kamar asrama untuk kegiatan instansi lain.]

**Alur:**
1. Klien mengecek ketersediaan fasilitas di kalender
2. Klien mengajukan permohonan sewa dengan detail kegiatan dan tanggal
3. Operator mengkonfirmasi dan mengirim invoice sewa
4. Klien check-in dan check-out sesuai jadwal yang disetujui

**Data yang dicatat:** nama fasilitas · tanggal check-in/out · nama kegiatan · jumlah tamu · total biaya

**Aturan bisnis:**
- Satu fasilitas **tidak bisa dipesan dua kali** di tanggal yang sama
- Pembatalan kurang dari **[X] hari** dikenakan biaya [X]%
- [Tambahkan aturan lain]

---

### Layanan 3 — [Nama Layanan, contoh: Sertifikasi Profesi]

**Deskripsi:** [Tuliskan deskripsi layanan Anda]

**Alur:**
1. [Langkah 1]
2. [Langkah 2]
3. [Langkah 3]
4. [Langkah 4]

**Data yang dicatat:** [sebutkan data yang perlu disimpan]

**Aturan bisnis:**
- [Aturan 1]
- [Aturan 2]

---

### Layanan 4 — [Salin blok di atas untuk layanan tambahan]

---

## 3. Laporan & Dashboard yang Dibutuhkan

### Dashboard Utama (tampil saat login)

| Informasi | Keterangan |
|-----------|------------|
| [contoh: Total penerimaan bulan ini] | [contoh: Jumlah PNBP yang diterima bulan berjalan] |
| [contoh: Jumlah pendaftaran minggu ini] | [contoh: Pendaftaran baru 7 hari terakhir per layanan] |
| [contoh: Fasilitas aktif hari ini] | [contoh: Ruangan/kamar yang sedang digunakan] |
| [Tambahkan] | |

### Laporan Berkala

| Laporan | Frekuensi | Isi | Format |
|---------|-----------|-----|--------|
| [contoh: Rekap PNBP] | Bulanan | [contoh: Total per jenis layanan] | Excel & PDF |
| [contoh: Statistik Peserta] | Triwulanan | [contoh: Jumlah per jenis pelatihan] | PDF |
| [Tambahkan] | | | |

---

> **Catatan untuk AI Coding Assistant:**
>
> **Stack & Arsitektur:**
> - Framework: **Laravel 13** dengan **Filament v5** (TALL Stack)
> - Database: **PostgreSQL ≥ 16** — gunakan migration Laravel dengan driver `pgsql`
> - Gunakan **UUID/ULID** sebagai primary key untuk entitas yang diekspos ke publik
> - Gunakan fitur **JSONB** PostgreSQL untuk metadata dinamis via Laravel `$casts`
> - Gunakan **Enum type** PostgreSQL untuk kolom status (atau string enum yang dicasting di Model)
>
> **Mapping PRD → Kode:**
> - Setiap **layanan** di Bagian 2 → 1 modul Filament Resource + set tabel database (migration PostgreSQL)
> - Setiap **alur** → urutan status pada kolom `status` (enum) di tabel transaksi
> - Setiap **data yang dicatat** → kolom-kolom pada migration + `$fillable` di Eloquent Model
> - Setiap **aturan bisnis** → validasi di Form schema Filament + business logic di Model/Action class
> - Setiap **peran pengguna** → Panel Filament terpisah dengan middleware auth + policy authorization
> - Bagian 3 → `StatsOverviewWidget`, `ChartWidget` di dashboard Filament + fitur ekspor PDF/Excel
>
> **Referensi:**
> - Dokumentasi Filament v5: https://filamentphp.com/docs
> - Dokumentasi Laravel: https://laravel.com/docs
> - Dokumentasi PostgreSQL: https://www.postgresql.org/docs/

---
