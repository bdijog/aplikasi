<?php

namespace App\Services\Reports;

use App\Models\Appointment;
use App\Models\QueueTicket;
use App\Models\Schedule;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelExportService
{
    private const PRIMARY_COLOR = '0D9488'; // Teal 600

    private const HEADER_FILL = '0F766E';    // Teal 700

    private const SUBHEADER_FILL = 'F0FDFA'; // Teal 50

    private const ACCENT_COLOR = '1E293B';   // Slate 800

    /**
     * Ekspor Rekap Kunjungan Harian/Periodik ke Excel (.xlsx).
     *
     * @param  array<string, mixed>  $data
     */
    public function exportDailyVisits(array $data): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Kunjungan');

        // 1. Header Informasi
        $this->applyDocumentHeader(
            $sheet,
            title: 'REKAPITULASI KUNJUNGAN PASIEN & JANJI TEMU',
            subtitle: 'Periode: '.$data['period']['formatted'].($data['selected_doctor'] ? ' | Dokter: '.$data['selected_doctor'] : ' | Semua Dokter'),
            lastCol: 'M'
        );

        // 2. Ringkasan Status Box (Baris 5)
        $sheet->mergeCells('A5:H5');
        $sheet->setCellValue('A5', 'RINGKASAN STATUS KUNJUNGAN');
        $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(11);

        $summaryHeaders = ['Total Janji Temu', 'Selesai (Completed)', 'Checked-In', 'Dalam Layanan', 'Terkonfirmasi', 'Pending', 'Dibatalkan', 'No-Show'];
        $summaryValues = [
            $data['summary']['total'],
            $data['summary']['completed'],
            $data['summary']['checked_in'],
            $data['summary']['in_progress'],
            $data['summary']['confirmed'],
            $data['summary']['pending'],
            $data['summary']['cancelled'],
            $data['summary']['no_show'],
        ];

        $col = 'A';
        foreach ($summaryHeaders as $i => $h) {
            $sheet->setCellValue($col.'6', $h);
            $sheet->setCellValue($col.'7', $summaryValues[$i]);
            $sheet->getStyle($col.'6')->getFont()->setBold(true)->setSize(9);
            $sheet->getStyle($col.'6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col.'7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col.'7')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle($col.'6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(self::SUBHEADER_FILL);
            $col++;
        }
        $sheet->getStyle('A6:H7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // 3. Tabel Detail Kunjungan (Mulai Baris 9)
        $sheet->mergeCells('A9:M9');
        $sheet->setCellValue('A9', 'DAFTAR DETAIL JANJI TEMU PASIEN');
        $sheet->getStyle('A9')->getFont()->setBold(true)->setSize(11);

        $tableHeaders = [
            'No.',
            'Kode Booking',
            'Tanggal',
            'Jam Estimasi',
            'Nama Pasien',
            'No. Rekam Medis',
            'Dokter Pemeriksa',
            'Poli / Spesialisasi',
            'Tipe Kunjungan',
            'Status',
            'Sumber',
            'Waktu Check-In',
            'Keluhan Utama',
        ];

        $row = 10;
        $this->writeTableHeaders($sheet, $tableHeaders, $row);

        $row = 11;
        $no = 1;
        /** @var Appointment $record */
        foreach ($data['records'] as $record) {
            $doctor = $record->doctor;
            $specialty = is_array($doctor?->specialty) ? ($doctor->specialty['id'] ?? reset($doctor->specialty)) : ($doctor?->specialty ?? '-');

            $sheet->setCellValue('A'.$row, $no++);
            $sheet->setCellValue('B'.$row, $record->booking_code);
            $sheet->setCellValue('C'.$row, $record->appointment_date?->format('d/m/Y') ?? '-');
            $sheet->setCellValue('D'.$row, $record->estimated_time ? substr((string) $record->estimated_time, 0, 5) : '-');
            $sheet->setCellValue('E'.$row, $record->patient?->name ?? '-');
            $sheet->setCellValue('F'.$row, $record->patient?->medical_record_number ?? '-');
            $sheet->setCellValue('G'.$row, $doctor?->name ?? '-');
            $sheet->setCellValue('H'.$row, $specialty);
            $sheet->setCellValue('I'.$row, $record->visit_type?->getLabel() ?? (string) $record->visit_type);
            $sheet->setCellValue('J'.$row, $record->status?->getLabel() ?? (string) $record->status);
            $sheet->setCellValue('K'.$row, $record->source?->getLabel() ?? (string) $record->source);
            $sheet->setCellValue('L'.$row, $record->checked_in_at?->format('H:i:s') ?? '-');
            $sheet->setCellValue('M'.$row, $record->chief_complaint ?? '-');

            // Alignment
            $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I'.$row.':L'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        $endRow = max(11, $row - 1);
        $sheet->getStyle('A10:M'.$endRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $this->autoFitColumns($sheet, 'A', 'M');

        $filename = 'Rekap_Kunjungan_'.now()->format('Ymd_His').'.xlsx';

        return $this->streamSpreadsheet($spreadsheet, $filename);
    }

    /**
     * Ekspor Statistik Kunjungan Bulanan ke Excel (.xlsx).
     *
     * @param  array<string, mixed>  $data
     */
    public function exportMonthlyStats(array $data): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Statistik Bulanan');

        $this->applyDocumentHeader(
            $sheet,
            title: 'LAPORAN STATISTIK KUNJUNGAN BULANAN',
            subtitle: 'Bulan: '.$data['month_name'].' | Total Kunjungan: '.$data['total_visits'].' Pasien',
            lastCol: 'I'
        );

        // Ringkasan
        $sheet->mergeCells('A5:B5');
        $sheet->setCellValue('A5', 'RINGKASAN RASIO KUNJUNGAN');
        $sheet->getStyle('A5')->getFont()->setBold(true);

        $metrics = [
            ['Total Kunjungan', $data['total_visits']],
            ['Kunjungan Selesai', $data['completed_visits']],
            ['Pasien Baru (New Visit)', $data['new_visits']." ({$data['new_ratio']}%)"],
            ['Kontrol (Follow-up)', $data['follow_ups']." ({$data['follow_up_ratio']}%)"],
        ];

        $r = 6;
        foreach ($metrics as [$label, $val]) {
            $sheet->setCellValue('A'.$r, $label);
            $sheet->setCellValue('B'.$r, $val);
            $sheet->getStyle('A'.$r)->getFont()->setBold(true);
            $sheet->getStyle('A'.$r.':B'.$r)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $r++;
        }

        // Tabel Spesialisasi
        $sheet->mergeCells('D5:G5');
        $sheet->setCellValue('D5', 'DISTRIBUSI PER SPESIALISASI / POLI');
        $sheet->getStyle('D5')->getFont()->setBold(true);

        $specHeaders = ['Spesialisasi / Poli', 'Jumlah Pasien', 'Persentase (%)', 'Selesai Dilayani'];
        $this->writeTableHeadersCustom($sheet, $specHeaders, 'D', 6);

        $specRow = 7;
        foreach ($data['by_specialty'] as $spec) {
            $sheet->setCellValue('D'.$specRow, $spec['specialty']);
            $sheet->setCellValue('E'.$specRow, $spec['total']);
            $sheet->setCellValue('F'.$specRow, $spec['percentage'].'%');
            $sheet->setCellValue('G'.$specRow, $spec['completed']);
            $sheet->getStyle('E'.$specRow.':G'.$specRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $specRow++;
        }
        $endSpecRow = max(7, $specRow - 1);
        $sheet->getStyle('D6:G'.$endSpecRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Tabel Kinerja Kunjungan Per Dokter
        $docStartRow = max($r, $endSpecRow) + 2;
        $sheet->mergeCells('A'.$docStartRow.':I'.$docStartRow);
        $sheet->setCellValue('A'.$docStartRow, 'RINCIAN KUNJUNGAN PASIEN PER DOKTER');
        $sheet->getStyle('A'.$docStartRow)->getFont()->setBold(true)->setSize(11);

        $docHeaders = ['No.', 'Nama Dokter', 'Spesialisasi', 'Total Pasien', 'Kontribusi (%)', 'Pasien Baru', 'Kontrol', 'Selesai', 'No-Show'];
        $this->writeTableHeaders($sheet, $docHeaders, $docStartRow + 1);

        $currDocRow = $docStartRow + 2;
        $no = 1;
        foreach ($data['by_doctor'] as $d) {
            $sheet->setCellValue('A'.$currDocRow, $no++);
            $sheet->setCellValue('B'.$currDocRow, $d['doctor_name']);
            $sheet->setCellValue('C'.$currDocRow, $d['specialty']);
            $sheet->setCellValue('D'.$currDocRow, $d['total']);
            $sheet->setCellValue('E'.$currDocRow, $d['percentage'].'%');
            $sheet->setCellValue('F'.$currDocRow, $d['new_visits']);
            $sheet->setCellValue('G'.$currDocRow, $d['follow_ups']);
            $sheet->setCellValue('H'.$currDocRow, $d['completed']);
            $sheet->setCellValue('I'.$currDocRow, $d['no_show']);

            $sheet->getStyle('A'.$currDocRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D'.$currDocRow.':I'.$currDocRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $currDocRow++;
        }
        $endDocRow = max($docStartRow + 2, $currDocRow - 1);
        $sheet->getStyle('A'.($docStartRow + 1).':I'.$endDocRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $this->autoFitColumns($sheet, 'A', 'I');

        $filename = 'Statistik_Bulanan_'.$data['year'].'_'.str_pad((string) $data['month'], 2, '0', STR_PAD_LEFT).'.xlsx';

        return $this->streamSpreadsheet($spreadsheet, $filename);
    }

    /**
     * Ekspor Laporan Kinerja Dokter ke Excel (.xlsx).
     *
     * @param  array<string, mixed>  $data
     */
    public function exportDoctorPerformance(array $data): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Kinerja Dokter');

        $this->applyDocumentHeader(
            $sheet,
            title: 'LAPORAN KINERJA DOKTER & DURASI KONSULTASI',
            subtitle: 'Periode: '.$data['period']['formatted'].($data['selected_doctor'] ? ' | Dokter: '.$data['selected_doctor'] : ''),
            lastCol: 'L'
        );

        // Ringkasan KPI Global
        $sheet->mergeCells('A5:E5');
        $sheet->setCellValue('A5', 'RINGKASAN INDIKATOR KINERJA');
        $sheet->getStyle('A5')->getFont()->setBold(true);

        $kpiHeaders = ['Total Dokter', 'Total Janji Temu', 'Total Pasien Selesai', 'Rata-rata Durasi Konsultasi', 'Rata-rata Waktu Tunggu Pasien'];
        $kpiValues = [
            $data['summary']['total_doctors'],
            $data['summary']['total_patients'],
            $data['summary']['total_completed'],
            $data['summary']['avg_consult_overall'].' Menit',
            $data['summary']['avg_wait_overall'].' Menit',
        ];

        $col = 'A';
        foreach ($kpiHeaders as $i => $h) {
            $sheet->setCellValue($col.'6', $h);
            $sheet->setCellValue($col.'7', $kpiValues[$i]);
            $sheet->getStyle($col.'6')->getFont()->setBold(true)->setSize(9);
            $sheet->getStyle($col.'6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col.'7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col.'7')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle($col.'6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(self::SUBHEADER_FILL);
            $col++;
        }
        $sheet->getStyle('A6:E7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Tabel Detail Kinerja per Dokter
        $sheet->mergeCells('A9:L9');
        $sheet->setCellValue('A9', 'TABEL PERFORMA & EFISIENSI LAYANAN DOKTER');
        $sheet->getStyle('A9')->getFont()->setBold(true)->setSize(11);

        $headers = [
            'No.',
            'Nama Dokter',
            'No. STR / SIP',
            'Spesialisasi',
            'Total Pasien',
            'Pasien Selesai',
            'Dibatalkan',
            'No-Show (Absen)',
            'Tingkat No-Show (%)',
            'Rata-rata Durasi Konsultasi (Menit)',
            'Rata-rata Waktu Tunggu (Menit)',
            'Total Tiket Antrian',
        ];

        $this->writeTableHeaders($sheet, $headers, 10);

        $row = 11;
        $no = 1;
        foreach ($data['doctors'] as $doc) {
            $sheet->setCellValue('A'.$row, $no++);
            $sheet->setCellValue('B'.$row, $doc['name']);
            $sheet->setCellValue('C'.$row, $doc['license_number'] ?? '-');
            $sheet->setCellValue('D'.$row, $doc['specialty']);
            $sheet->setCellValue('E'.$row, $doc['total_appointments']);
            $sheet->setCellValue('F'.$row, $doc['completed']);
            $sheet->setCellValue('G'.$row, $doc['cancelled']);
            $sheet->setCellValue('H'.$row, $doc['no_show']);
            $sheet->setCellValue('I'.$row, $doc['no_show_rate'].'%');
            $sheet->setCellValue('J'.$row, $doc['avg_consult_minutes']);
            $sheet->setCellValue('K'.$row, $doc['avg_wait_minutes']);
            $sheet->setCellValue('L'.$row, $doc['total_tickets']);

            $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E'.$row.':L'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }

        $endRow = max(11, $row - 1);
        $sheet->getStyle('A10:L'.$endRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $this->autoFitColumns($sheet, 'A', 'L');

        $filename = 'Kinerja_Dokter_'.now()->format('Ymd_His').'.xlsx';

        return $this->streamSpreadsheet($spreadsheet, $filename);
    }

    /**
     * Ekspor Laporan Antrian & Waktu Tunggu ke Excel (.xlsx).
     *
     * @param  array<string, mixed>  $data
     */
    public function exportQueueWaitTime(array $data): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Waktu Tunggu Antrian');

        $this->applyDocumentHeader(
            $sheet,
            title: 'LAPORAN ANTRIAN & ANALISIS WAKTU TUNGGU LAYANAN',
            subtitle: 'Periode: '.$data['period']['formatted'].($data['selected_doctor'] ? ' | Dokter: '.$data['selected_doctor'] : ''),
            lastCol: 'M'
        );

        // Ringkasan
        $sheet->mergeCells('A5:F5');
        $sheet->setCellValue('A5', 'RINGKASAN METRIK ANTRIAN');
        $sheet->getStyle('A5')->getFont()->setBold(true);

        $metrics = [
            'Total Tiket Antrian' => $data['summary']['total_tickets'],
            'Tiket Selesai' => $data['summary']['completed_tickets'],
            'Tiket Dilewati (Skipped)' => $data['summary']['skipped_tickets'],
            'Rata-rata Waktu Tunggu' => $data['summary']['avg_wait_minutes'].' Menit',
            'Waktu Tunggu Maksimal' => $data['summary']['max_wait_minutes'].' Menit',
            'Rata-rata Durasi Layanan' => $data['summary']['avg_serve_minutes'].' Menit',
        ];

        $col = 'A';
        foreach ($metrics as $h => $v) {
            $sheet->setCellValue($col.'6', $h);
            $sheet->setCellValue($col.'7', $v);
            $sheet->getStyle($col.'6')->getFont()->setBold(true)->setSize(9);
            $sheet->getStyle($col.'6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col.'7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col.'7')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle($col.'6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(self::SUBHEADER_FILL);
            $col++;
        }
        $sheet->getStyle('A6:F7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Tabel Harian
        $sheet->mergeCells('A9:F9');
        $sheet->setCellValue('A9', 'ANALISIS WAKTU TUNGGU PER HARI');
        $sheet->getStyle('A9')->getFont()->setBold(true)->setSize(11);

        $dayHeaders = ['Tanggal', 'Total Tiket', 'Selesai Dilayani', 'Dilewati', 'Rata-rata Tunggu (Menit)', 'Rata-rata Layanan (Menit)'];
        $this->writeTableHeaders($sheet, $dayHeaders, 10);

        $r = 11;
        foreach ($data['by_day'] as $day) {
            $sheet->setCellValue('A'.$r, $day['formatted_date']);
            $sheet->setCellValue('B'.$r, $day['total']);
            $sheet->setCellValue('C'.$r, $day['completed']);
            $sheet->setCellValue('D'.$r, $day['skipped']);
            $sheet->setCellValue('E'.$r, $day['avg_wait_minutes']);
            $sheet->setCellValue('F'.$r, $day['avg_serve_minutes']);

            $sheet->getStyle('B'.$r.':F'.$r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $r++;
        }
        $endDayRow = max(11, $r - 1);
        $sheet->getStyle('A10:F'.$endDayRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Tabel Log Tiket Antrian
        $logStart = $endDayRow + 2;
        $sheet->mergeCells('A'.$logStart.':M'.$logStart);
        $sheet->setCellValue('A'.$logStart, 'LOG DETAIL TIKET ANTRIAN PASIEN');
        $sheet->getStyle('A'.$logStart)->getFont()->setBold(true)->setSize(11);

        $ticketHeaders = [
            'No.',
            'No. Antrian',
            'Tanggal',
            'Pasien',
            'Dokter',
            'Prioritas',
            'Status',
            'Counter/Poli',
            'Waktu Panggil',
            'Mulai Dilayani',
            'Selesai',
            'Durasi Tunggu (Menit)',
            'Durasi Layanan (Menit)',
        ];

        $this->writeTableHeaders($sheet, $ticketHeaders, $logStart + 1);

        $ticketRow = $logStart + 2;
        $no = 1;
        /** @var QueueTicket $ticket */
        foreach ($data['records'] as $ticket) {
            $checkIn = $ticket->appointment?->checked_in_at ?? $ticket->created_at;
            $wait = ($checkIn && $ticket->called_at && $ticket->called_at->greaterThanOrEqualTo($checkIn))
                ? $checkIn->diffInMinutes($ticket->called_at)
                : '-';

            $serve = ($ticket->completed_at && $ticket->served_at && $ticket->completed_at->greaterThanOrEqualTo($ticket->served_at))
                ? $ticket->served_at->diffInMinutes($ticket->completed_at)
                : '-';

            $sheet->setCellValue('A'.$ticketRow, $no++);
            $sheet->setCellValue('B'.$ticketRow, $ticket->display_number);
            $sheet->setCellValue('C'.$ticketRow, $ticket->queue_date?->format('d/m/Y') ?? '-');
            $sheet->setCellValue('E'.$ticketRow, $ticket->doctor?->name ?? '-');
            $sheet->setCellValue('D'.$ticketRow, $ticket->appointment?->patient?->name ?? '-');
            $sheet->setCellValue('F'.$ticketRow, $ticket->priority?->getLabel() ?? (string) $ticket->priority);
            $sheet->setCellValue('G'.$ticketRow, $ticket->status?->getLabel() ?? (string) $ticket->status);
            $sheet->setCellValue('H'.$ticketRow, $ticket->counter ?? '-');
            $sheet->setCellValue('I'.$ticketRow, $ticket->called_at?->format('H:i:s') ?? '-');
            $sheet->setCellValue('J'.$ticketRow, $ticket->served_at?->format('H:i:s') ?? '-');
            $sheet->setCellValue('K'.$ticketRow, $ticket->completed_at?->format('H:i:s') ?? '-');
            $sheet->setCellValue('L'.$ticketRow, $wait);
            $sheet->setCellValue('M'.$ticketRow, $serve);

            $sheet->getStyle('A'.$ticketRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B'.$ticketRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C'.$ticketRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F'.$ticketRow.':M'.$ticketRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $ticketRow++;
        }
        $endTicketRow = max($logStart + 2, $ticketRow - 1);
        $sheet->getStyle('A'.($logStart + 1).':M'.$endTicketRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $this->autoFitColumns($sheet, 'A', 'M');

        $filename = 'Antrian_Waktu_Tunggu_'.now()->format('Ymd_His').'.xlsx';

        return $this->streamSpreadsheet($spreadsheet, $filename);
    }

    /**
     * Ekspor Roster Jadwal Praktik Dokter ke Excel (.xlsx).
     *
     * @param  array<string, mixed>  $data
     */
    public function exportDoctorSchedules(array $data): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Jadwal Praktik Dokter');

        $this->applyDocumentHeader(
            $sheet,
            title: 'ROSTER JADWAL PRAKTIK DOKTER & KUOTA LAYANAN',
            subtitle: 'Tanggal Cetak: '.now()->translatedFormat('d F Y').($data['selected_doctor'] ? ' | Dokter: '.$data['selected_doctor'] : ' | Seluruh Jadwal'),
            lastCol: 'K'
        );

        // Ringkasan
        $sheet->mergeCells('A5:D5');
        $sheet->setCellValue('A5', 'RINGKASAN JADWAL PRAKTIK');
        $sheet->getStyle('A5')->getFont()->setBold(true);

        $sumHeaders = ['Total Slot Jadwal', 'Jadwal Aktif', 'Jumlah Dokter Bertugas', 'Total Kapasitas / Kuota Pasien'];
        $sumVals = [
            $data['summary']['total_schedules'],
            $data['summary']['active_schedules'],
            $data['summary']['total_doctors'],
            $data['summary']['total_capacity'].' Pasien',
        ];

        $col = 'A';
        foreach ($sumHeaders as $i => $h) {
            $sheet->setCellValue($col.'6', $h);
            $sheet->setCellValue($col.'7', $sumVals[$i]);
            $sheet->getStyle($col.'6')->getFont()->setBold(true)->setSize(9);
            $sheet->getStyle($col.'6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col.'7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($col.'7')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle($col.'6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(self::SUBHEADER_FILL);
            $col++;
        }
        $sheet->getStyle('A6:D7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Tabel Jadwal
        $sheet->mergeCells('A9:K9');
        $sheet->setCellValue('A9', 'DAFTAR JADWAL PRAKTIK DOKTER');
        $sheet->getStyle('A9')->getFont()->setBold(true)->setSize(11);

        $headers = [
            'No.',
            'Nama Dokter',
            'Poli / Spesialisasi',
            'Tipe Jadwal',
            'Hari Praktik',
            'Tanggal Khusus',
            'Jam Mulai',
            'Jam Selesai',
            'Kuota Pasien',
            'Status',
            'Catatan',
        ];

        $this->writeTableHeaders($sheet, $headers, 10);

        $row = 11;
        $no = 1;
        /** @var Schedule $schedule */
        foreach ($data['records'] as $schedule) {
            $doc = $schedule->doctor;
            $specialty = is_array($doc?->specialty) ? ($doc->specialty['id'] ?? reset($doc->specialty)) : ($doc?->specialty ?? '-');
            $dayName = $schedule->day_of_week !== null ? ($data['day_names'][$schedule->day_of_week] ?? '-') : '-';

            $sheet->setCellValue('A'.$row, $no++);
            $sheet->setCellValue('B'.$row, $doc?->name ?? '-');
            $sheet->setCellValue('C'.$row, $specialty);
            $sheet->setCellValue('D'.$row, $schedule->type?->getLabel() ?? (string) $schedule->type);
            $sheet->setCellValue('E'.$row, $dayName);
            $sheet->setCellValue('F'.$row, $schedule->specific_date?->format('d/m/Y') ?? '-');
            $sheet->setCellValue('G'.$row, $schedule->start_time ? substr((string) $schedule->start_time, 0, 5) : '-');
            $sheet->setCellValue('H'.$row, $schedule->end_time ? substr((string) $schedule->end_time, 0, 5) : '-');
            $sheet->setCellValue('I'.$row, $schedule->max_patients);
            $sheet->setCellValue('J'.$row, $schedule->status?->getLabel() ?? (string) $schedule->status);
            $sheet->setCellValue('K'.$row, $schedule->notes ?? '-');

            $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D'.$row.':J'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $row++;
        }

        $endRow = max(11, $row - 1);
        $sheet->getStyle('A10:K'.$endRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $this->autoFitColumns($sheet, 'A', 'K');

        $filename = 'Jadwal_Dokter_'.now()->format('Ymd_His').'.xlsx';

        return $this->streamSpreadsheet($spreadsheet, $filename);
    }

    /**
     * Ekspor Generic Table untuk resource table header / bulk actions.
     *
     * @param  array<string>  $headers
     * @param  array<array<mixed>>  $rows
     */
    public function exportGenericTable(string $title, array $headers, array $rows, string $filename): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Ekspor');

        $lastCol = $this->getColumnLetter(max(1, count($headers)));

        $this->applyDocumentHeader(
            $sheet,
            title: strtoupper($title),
            subtitle: 'Dicetak pada: '.now()->translatedFormat('d F Y H:i:s').' | Total: '.count($rows).' Data',
            lastCol: $lastCol
        );

        $startRow = 5;
        $this->writeTableHeaders($sheet, $headers, $startRow);

        $r = $startRow + 1;

        foreach ($rows as $rowData) {
            $c = 'A';
            foreach ($rowData as $cellVal) {
                $sheet->setCellValue($c.$r, $cellVal);
                $c++;
            }
            $r++;
        }

        $endRow = max($startRow + 1, $r - 1);
        $sheet->getStyle('A'.$startRow.':'.$lastCol.$endRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $this->autoFitColumns($sheet, 'A', $lastCol);

        return $this->streamSpreadsheet($spreadsheet, $filename);
    }

    // ==========================================
    // HELPER STYLING METHODS
    // ==========================================

    private function applyDocumentHeader(Worksheet $sheet, string $title, string $subtitle, string $lastCol = 'H'): void
    {
        // 1. Baris Judul Utama Klinik (Merge selebar tabel agar tidak mendistorsi kolom A)
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', 'KLINIK AYO SEHAT - SISTEM INFORMASI LAYANAN & JADWAL DOKTER');
        $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true)->setSize(13)->setColor(new Color(self::HEADER_FILL));
        $sheet->getStyle("A1:{$lastCol}1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // 2. Baris Judul Laporan
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->setCellValue('A2', $title);
        $sheet->getStyle("A2:{$lastCol}2")->getFont()->setBold(true)->setSize(11)->setColor(new Color(self::ACCENT_COLOR));
        $sheet->getStyle("A2:{$lastCol}2")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // 3. Baris Subtitle / Periode
        $sheet->mergeCells("A3:{$lastCol}3");
        $sheet->setCellValue('A3', $subtitle);
        $sheet->getStyle("A3:{$lastCol}3")->getFont()->setItalic(true)->setSize(9)->setColor(new Color('64748B'));
        $sheet->getStyle("A3:{$lastCol}3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(3)->setRowHeight(18);

        // Baris pemisah kosong
        $sheet->getRowDimension(4)->setRowHeight(8);
    }

    /**
     * @param  array<string>  $headers
     */
    private function writeTableHeaders(Worksheet $sheet, array $headers, int $row): void
    {
        $this->writeTableHeadersCustom($sheet, $headers, 'A', $row);
    }

    /**
     * @param  array<string>  $headers
     */
    private function writeTableHeadersCustom(Worksheet $sheet, array $headers, string $startCol, int $row): void
    {
        $col = $startCol;
        foreach ($headers as $header) {
            $sheet->setCellValue($col.$row, $header);
            $sheet->getStyle($col.$row)->getFont()->setBold(true)->setColor(new Color(Color::COLOR_WHITE));
            $sheet->getStyle($col.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle($col.$row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(self::HEADER_FILL);
            $col++;
        }
        $sheet->getRowDimension($row)->setRowHeight(24);
    }

    private function autoFitColumns(Worksheet $sheet, string $fromCol, string $toCol): void
    {
        $fromIdx = Coordinate::columnIndexFromString($fromCol);
        $toIdx = Coordinate::columnIndexFromString($toCol);

        for ($i = $fromIdx; $i <= $toIdx; $i++) {
            $colLetter = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $sheet->calculateColumnWidths();

        // Pastikan kolom No. (kolom A jika sempit) memiliki lebar minimal yang proporsional dan tidak terdistorsi
        if ($fromCol === 'A') {
            $colAWidth = $sheet->getColumnDimension('A')->getWidth();
            if ($colAWidth > 0 && $colAWidth < 6) {
                $sheet->getColumnDimension('A')->setAutoSize(false);
                $sheet->getColumnDimension('A')->setWidth(7);
            }
        }
    }

    private function getColumnLetter(int $colNumber): string
    {
        return Coordinate::stringFromColumnIndex($colNumber);
    }

    private function streamSpreadsheet(Spreadsheet $spreadsheet, string $filename): StreamedResponse
    {
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(
            function () use ($writer) {
                $writer->save('php://output');
            },
            $filename,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }
}
