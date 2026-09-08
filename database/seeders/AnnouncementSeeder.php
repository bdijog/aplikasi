<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $announcements = [
            [
                'seed' => 'clinic-holiday',
                'title' => [
                    'id' => 'Jadwal Pelayanan Poliklinik Selama Libur Nasional dan Cuti Bersama',
                    'en' => 'Polyclinic Service Schedule During National Holidays and Collective Leave',
                ],
                'summary' => [
                    'id' => 'Informasi operasional rawat jalan dan layanan gawat darurat (IGD) selama libur nasional.',
                    'en' => 'Operational information for outpatient clinics and emergency services during national holidays.',
                ],
                'content' => [
                    'id' => '<p>Diberitahukan kepada seluruh pasien dan pengunjung klinik, dalam rangka memperingati Hari Libur Nasional dan Cuti Bersama:</p>'
                        .'<ul>'
                        .'<li><strong>Layanan Poliklinik Rawat Jalan:</strong> Tutup sementara selama periode libur nasional dan akan kembali beroperasi normal pada hari kerja berikutnya.</li>'
                        .'<li><strong>Instalasi Gawat Darurat (IGD) & Ambulans:</strong> Tetap <em>Buka 24 Jam</em> melayani pasien kondisi darurat medis.</li>'
                        .'<li><strong>Layanan Farmasi & Laboratorium Cito:</strong> Tetap siaga mendukung operasional gawat darurat.</li>'
                        .'</ul>'
                        .'<p>Bagi pasien dengan pengobatan rutin, disarankan melakukan konsultasi dan pengambilan obat sebelum masa libur dimulai. Terima kasih atas pengertiannya.</p>',
                    'en' => '<p>Notice to all patients and visitors of the clinic regarding National Holidays and Collective Leave:</p>'
                        .'<ul>'
                        .'<li><strong>Outpatient Polyclinics:</strong> Temporarily closed during the holiday period and will resume normal operations on the next business day.</li>'
                        .'<li><strong>Emergency Department (ED) & Ambulance:</strong> Remains <em>Open 24 Hours</em> for medical emergencies.</li>'
                        .'<li><strong>Urgent Pharmacy & Laboratory:</strong> On standby to support emergency operations.</li>'
                        .'</ul>'
                        .'<p>Patients on regular medication are advised to consult and refill prescriptions prior to the holiday. Thank you for your cooperation.</p>',
                ],
                'is_active' => true,
                'published_at' => now()->subDays(2),
                'expired_at' => now()->addDays(14),
            ],
            [
                'seed' => 'health-screening',
                'title' => [
                    'id' => 'Pemeriksaan Kesehatan Gratis: Skrining Diabetes dan Hipertensi',
                    'en' => 'Free Health Screening: Diabetes and Hypertension Check',
                ],
                'summary' => [
                    'id' => 'Layanan cek gula darah dan tensi gratis bagi masyarakat umum dan lansia di aula klinik.',
                    'en' => 'Free blood sugar and blood pressure screening for the community and elderly at the clinic hall.',
                ],
                'content' => [
                    'id' => '<p>Dalam rangka Hari Kesehatan Nasional, klinik kami mengadakan program <strong>Pemeriksaan Kesehatan Gratis</strong> untuk masyarakat umum:</p>'
                        .'<ul>'
                        .'<li>Pemeriksaan Tekanan Darah (Tensi)</li>'
                        .'<li>Pemeriksaan Gula Darah Sewaktu / Puasa</li>'
                        .'<li>Konsultasi Singkat dengan Dokter Umum</li>'
                        .'<li>Edukasi Pola Hidup Sehat dan Diet Gizi Seimbang</li>'
                        .'</ul>'
                        .'<p><strong>Waktu & Tempat:</strong> Setiap Sabtu pagi pukul 08:00 - 11:30 WIB di Ruang Aula Lantai 1. Kuota terbatas 100 peserta per sesi. Pendaftaran dapat dilakukan langsung di loket pendaftaran atau melalui aplikasi.</p>',
                    'en' => '<p>In celebration of National Health Day, our clinic offers a <strong>Free Health Screening</strong> program for the general public:</p>'
                        .'<ul>'
                        .'<li>Blood Pressure Check</li>'
                        .'<li>Blood Glucose Test (Random / Fasting)</li>'
                        .'<li>Brief Consultation with General Practitioner</li>'
                        .'<li>Healthy Lifestyle and Nutrition Education</li>'
                        .'</ul>'
                        .'<p><strong>Time & Venue:</strong> Every Saturday morning from 08:00 to 11:30 at the 1st Floor Hall. Limited to 100 participants per session. Registration available at reception or via the mobile app.</p>',
                ],
                'is_active' => true,
                'published_at' => now()->subDay(),
                'expired_at' => now()->addMonths(1),
            ],
            [
                'seed' => 'vaccine-program',
                'title' => [
                    'id' => 'Sosialisasi Vaksinasi Influenza dan Pneumonia Dewasa',
                    'en' => 'Influenza and Adult Pneumonia Vaccination Program',
                ],
                'summary' => [
                    'id' => 'Program vaksinasi perlindungan saluran napas bagi lansia dan kelompok rentan di Poli Penyakit Dalam.',
                    'en' => 'Respiratory protection vaccination program for seniors and vulnerable groups at the Internal Medicine Clinic.',
                ],
                'content' => [
                    'id' => '<p>Penyakit pernapasan seperti flu berat dan radang paru (pneumonia) dapat dicegah secara efektif dengan vaksinasi tahunan. Klinik kini menyediakan:</p>'
                        .'<ul>'
                        .'<li><strong>Vaksin Influenza Kuadrivalen:</strong> Memberikan perlindungan terhadap 4 galur virus flu musiman terbaru.</li>'
                        .'<li><strong>Vaksin Pneumokokus (PCV13 / PPSV23):</strong> Perlindungan terhadap bakteri penyebab pneumonia dan meningitis.</li>'
                        .'</ul>'
                        .'<p>Sangat dianjurkan bagi lansia usia di atas 50 tahun, penderita diabetes, asma, penyakit jantung, serta tenaga kesehatan. Silakan jadwalkan janji temu melalui menu Appointment pada Poli Spesialis Penyakit Dalam.</p>',
                    'en' => '<p>Respiratory illnesses such as severe influenza and pneumonia can be effectively prevented through annual immunization. Our clinic now provides:</p>'
                        .'<ul>'
                        .'<li><strong>Quadrivalent Influenza Vaccine:</strong> Protection against the 4 latest seasonal influenza strains.</li>'
                        .'<li><strong>Pneumococcal Conjugate Vaccine (PCV13 / PPSV23):</strong> Protection against pneumonia and meningitis bacteria.</li>'
                        .'</ul>'
                        .'<p>Highly recommended for individuals aged 50 and older, those with diabetes, asthma, heart conditions, and healthcare workers. Please schedule your visit through the Appointment menu under Internal Medicine.</p>',
                ],
                'is_active' => true,
                'published_at' => now()->subHours(12),
                'expired_at' => null,
            ],
            [
                'seed' => 'queue-kiosk',
                'title' => [
                    'id' => 'Pembaruan Sistem Antrean Online dan Pendaftaran Mandiri (Kiosk)',
                    'en' => 'Online Queue System Update and Self-Check-in Kiosk',
                ],
                'summary' => [
                    'id' => 'Pasien kini dapat melakukan check-in mandiri menggunakan barcode booking untuk mengurangi waktu tunggu.',
                    'en' => 'Patients can now self-check-in using booking barcodes to reduce waiting times.',
                ],
                'content' => [
                    'id' => '<p>Untuk meningkatkan kenyamanan dan mempercepat proses pelayanan, kini telah tersedia mesin <strong>Anjungan Pendaftaran Mandiri (Kiosk)</strong> di lobi utama klinik:</p>'
                        .'<ul>'
                        .'<li><strong>Pasien Perjanjian:</strong> Cukup pindai (scan) kode QR atau masukkan Kode Booking pada mesin kiosk untuk mencetak tiket antrean poli.</li>'
                        .'<li><strong>Pasien Walk-in:</strong> Pilih poli tujuan pada layar sentuh kiosk dan ambil nomor antrean loket pendaftaran.</li>'
                        .'<li><strong>Display Pemanggilan:</strong> Nomor antrean akan dipanggil otomatis melalui pengeras suara dan layar televisi di ruang tunggu.</li>'
                        .'</ul>'
                        .'<p>Petugas informasi kami siap mendampingi pasien yang membutuhkan bantuan penggunaan mesin kiosk.</p>',
                    'en' => '<p>To enhance patient experience and streamline registration, <strong>Self-Check-in Kiosks</strong> are now available in the main clinic lobby:</p>'
                        .'<ul>'
                        .'<li><strong>Appointment Patients:</strong> Simply scan your QR code or enter your Booking Code to print your clinic ticket immediately.</li>'
                        .'<li><strong>Walk-in Patients:</strong> Select your desired service on the touch screen and receive your ticket.</li>'
                        .'<li><strong>Display & Audio:</strong> Queue numbers are called automatically through display monitors and voice announcements in waiting areas.</li>'
                        .'</ul>'
                        .'<p>Our customer care staff is available to assist anyone needing guidance using the kiosk.</p>',
                ],
                'is_active' => true,
                'published_at' => now()->subHours(6),
                'expired_at' => null,
            ],
            [
                'seed' => 'dengue-prevention',
                'title' => [
                    'id' => 'Edukasi Pencegahan Demam Berdarah Dengue (DBD) di Musim Hujan',
                    'en' => 'Dengue Fever Prevention and Education During Rainy Season',
                ],
                'summary' => [
                    'id' => 'Kenali gejala awal demam berdarah dan langkah 3M Plus untuk menjaga lingkungan tetap aman.',
                    'en' => 'Recognize early symptoms of dengue fever and 3M Plus measures to keep your surroundings safe.',
                ],
                'content' => [
                    'id' => '<p>Menghadapi musim penghujan, kasus demam berdarah cenderung meningkat. Kenali gejala dini dan langkah pertolongan pertama:</p>'
                        .'<ul>'
                        .'<li>Demam tinggi mendadak selama 2-7 hari tanpa sebab yang jelas.</li>'
                        .'<li>Nyeri sendi, otot, dan sakit di belakang rongga mata.</li>'
                        .'<li>Bintik-bintik merah pada kulit yang tidak hilang saat ditekan.</li>'
                        .'<li>Mual, muntah, atau rasa lemas yang berlebihan.</li>'
                        .'</ul>'
                        .'<p>Lakukan <strong>3M Plus</strong> (Menguras, Menutup, Mendaur ulang barang bekas, plus memakai lotion anti nyamuk). Segera lakukan pemeriksaan darah hematologi lengkap di laboratorium klinik jika mengalami gejala di atas.</p>',
                    'en' => '<p>During the rainy season, cases of dengue hemorrhagic fever often surge. Learn the symptoms and early care:</p>'
                        .'<ul>'
                        .'<li>Sudden high fever lasting 2 to 7 days without obvious cause.</li>'
                        .'<li>Severe muscle, joint aches, and pain behind the eyes.</li>'
                        .'<li>Red petechial spots on skin that do not fade when pressed.</li>'
                        .'<li>Nausea, vomiting, or extreme fatigue.</li>'
                        .'</ul>'
                        .'<p>Practice mosquito source reduction and prevention. Seek immediate medical evaluation and complete blood count testing at our laboratory if symptoms occur.</p>',
                ],
                'is_active' => true,
                'published_at' => now()->subDays(3),
                'expired_at' => null,
            ],
            [
                'seed' => 'clinic-renovation',
                'title' => [
                    'id' => 'Pengumuman Renovasi dan Relokasi Sementara Ruang Tunggu Poli Mata',
                    'en' => 'Notice of Renovation and Temporary Relocation of Eye Clinic Waiting Area',
                ],
                'summary' => [
                    'id' => 'Pelayanan Poli Mata sementara dipindahkan ke Gedung B Lantai 2 sehubungan dengan peningkatan fasilitas.',
                    'en' => 'Eye Clinic services temporarily relocated to Building B 2nd Floor due to facility upgrades.',
                ],
                'content' => [
                    'id' => '<p>Dalam rangka meningkatkan kenyamanan pasien serta pemasangan peralatan diagnostik optometri modern, kami menginformasikan:</p>'
                        .'<ul>'
                        .'<li>Ruang periksa dan ruang tunggu Poli Mata sementara dipindahkan ke <strong>Gedung B Lantai 2 (Kamar 204-206)</strong>.</li>'
                        .'<li>Proses renovasi diperkirakan berlangsung selama 3 minggu.</li>'
                        .'<li>Jadwal praktek dokter spesialis mata tetap berlangsung normal sesuai jadwal yang telah ditentukan.</li>'
                        .'</ul>'
                        .'<p>Mohon maaf atas ketidaknyamanan yang ditimbulkan selama masa perbaikan fasilitas ini.</p>',
                    'en' => '<p>To improve patient comfort and install state-of-the-art diagnostic optometry equipment, please be advised:</p>'
                        .'<ul>'
                        .'<li>Eye Clinic examination and waiting areas are temporarily relocated to <strong>Building B 2nd Floor (Rooms 204-206)</strong>.</li>'
                        .'<li>Renovation works are scheduled to take approximately 3 weeks.</li>'
                        .'<li>Doctor consultation schedules continue normally as previously published.</li>'
                        .'</ul>'
                        .'<p>We apologize for any temporary inconvenience during our facility modernization.</p>',
                ],
                'is_active' => false,
                'published_at' => now()->subDays(10),
                'expired_at' => now()->subDay(),
            ],
        ];

        // Ensure storage directory exists
        Storage::disk('public')->makeDirectory('announcements');

        foreach ($announcements as $data) {
            $slug = Str::slug($data['title']['id']);
            $imagePath = "announcements/{$slug}.jpg";

            // Download dummy image from picsum.photos if not already cached
            if (! Storage::disk('public')->exists($imagePath)) {
                $imageUrl = "https://picsum.photos/seed/{$data['seed']}/800/500";
                try {
                    $response = Http::timeout(8)->withoutVerifying()->get($imageUrl);
                    if ($response->successful() && strlen($response->body()) > 1000) {
                        Storage::disk('public')->put($imagePath, $response->body());
                    } else {
                        // Fallback: simple 1x1 transparent/colored JPEG header or skip image
                        $this->command?->warn("Could not download image for {$slug}, using fallback.");
                    }
                } catch (\Throwable $e) {
                    $this->command?->warn("Failed to fetch image from picsum.photos for {$slug}: {$e->getMessage()}");
                }
            }

            // Determine image attribute: only set if file actually exists
            $finalImage = Storage::disk('public')->exists($imagePath) ? $imagePath : null;

            Announcement::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $data['title'],
                    'summary' => $data['summary'],
                    'content' => $data['content'],
                    'slug' => $slug,
                    'is_active' => $data['is_active'],
                    'published_at' => $data['published_at'],
                    'expired_at' => $data['expired_at'],
                    'image' => $finalImage,
                ]
            );
        }
    }
}
