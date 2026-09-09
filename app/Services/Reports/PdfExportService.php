<?php

namespace App\Services\Reports;

use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PdfExportService
{
    /**
     * Ekspor Rekap Kunjungan Harian/Periodik ke PDF.
     *
     * @param  array<string, mixed>  $data
     */
    public function exportDailyVisits(array $data): StreamedResponse
    {
        $filename = 'Rekap_Kunjungan_'.now()->format('Ymd_His').'.pdf';

        $pdf = Pdf::loadView('reports.pdf.daily-visits', [
            'data' => $data,
            'title' => 'REKAPITULASI KUNJUNGAN PASIEN & JANJI TEMU',
            'period' => $data['period']['formatted'],
            'generatedAt' => now()->translatedFormat('d F Y, H:i:s'),
        ])->setPaper('a4', 'landscape');

        return $this->streamPdf($pdf, $filename);
    }

    /**
     * Ekspor Statistik Kunjungan Bulanan ke PDF.
     *
     * @param  array<string, mixed>  $data
     */
    public function exportMonthlyStats(array $data): StreamedResponse
    {
        $filename = 'Statistik_Bulanan_'.$data['year'].'_'.str_pad((string) $data['month'], 2, '0', STR_PAD_LEFT).'.pdf';

        $pdf = Pdf::loadView('reports.pdf.monthly-stats', [
            'data' => $data,
            'title' => 'LAPORAN STATISTIK KUNJUNGAN BULANAN',
            'period' => $data['month_name'],
            'generatedAt' => now()->translatedFormat('d F Y, H:i:s'),
        ])->setPaper('a4', 'portrait');

        return $this->streamPdf($pdf, $filename);
    }

    /**
     * Ekspor Laporan Kinerja Dokter ke PDF.
     *
     * @param  array<string, mixed>  $data
     */
    public function exportDoctorPerformance(array $data): StreamedResponse
    {
        $filename = 'Kinerja_Dokter_'.now()->format('Ymd_His').'.pdf';

        $pdf = Pdf::loadView('reports.pdf.doctor-performance', [
            'data' => $data,
            'title' => 'LAPORAN KINERJA DOKTER & DURASI KONSULTASI',
            'period' => $data['period']['formatted'],
            'generatedAt' => now()->translatedFormat('d F Y, H:i:s'),
        ])->setPaper('a4', 'landscape');

        return $this->streamPdf($pdf, $filename);
    }

    /**
     * Ekspor Laporan Antrian & Waktu Tunggu ke PDF.
     *
     * @param  array<string, mixed>  $data
     */
    public function exportQueueWaitTime(array $data): StreamedResponse
    {
        $filename = 'Antrian_Waktu_Tunggu_'.now()->format('Ymd_His').'.pdf';

        $pdf = Pdf::loadView('reports.pdf.queue-wait-time', [
            'data' => $data,
            'title' => 'LAPORAN ANTRIAN & ANALISIS WAKTU TUNGGU',
            'period' => $data['period']['formatted'],
            'generatedAt' => now()->translatedFormat('d F Y, H:i:s'),
        ])->setPaper('a4', 'landscape');

        return $this->streamPdf($pdf, $filename);
    }

    /**
     * Ekspor Roster Jadwal Praktik Dokter ke PDF.
     *
     * @param  array<string, mixed>  $data
     */
    public function exportDoctorSchedules(array $data): StreamedResponse
    {
        $filename = 'Jadwal_Dokter_'.now()->format('Ymd_His').'.pdf';

        $pdf = Pdf::loadView('reports.pdf.doctor-schedules', [
            'data' => $data,
            'title' => 'ROSTER JADWAL PRAKTIK DOKTER & KUOTA LAYANAN',
            'period' => now()->translatedFormat('d F Y'),
            'generatedAt' => now()->translatedFormat('d F Y, H:i:s'),
        ])->setPaper('a4', 'portrait');

        return $this->streamPdf($pdf, $filename);
    }

    /**
     * Ekspor generic table ke PDF.
     *
     * @param  array<string>  $headers
     * @param  array<array<mixed>>  $rows
     */
    public function exportGenericTable(string $title, array $headers, array $rows, string $filename, string $orientation = 'portrait'): StreamedResponse
    {
        $pdf = Pdf::loadView('reports.pdf.generic-table', [
            'title' => strtoupper($title),
            'headers' => $headers,
            'rows' => $rows,
            'generatedAt' => now()->translatedFormat('d F Y, H:i:s'),
        ])->setPaper('a4', $orientation);

        return $this->streamPdf($pdf, $filename);
    }

    /**
     * Helper stream PDF.
     */
    private function streamPdf(\Barryvdh\DomPDF\PDF $pdf, string $filename): StreamedResponse
    {
        return response()->streamDownload(
            function () use ($pdf) {
                echo $pdf->output();
            },
            $filename,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }
}
