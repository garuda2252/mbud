<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class KnowledgeBaseSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // Nonaktifkan sementara foreign key check agar seeder aman dijalankan ulang (idempotent)
        $this->db->disableForeignKeyChecks();
        $this->db->table('solutions')->truncate();
        $this->db->table('sub_categories')->truncate();
        $this->db->table('categories')->truncate();
        $this->db->enableForeignKeyChecks();

        // 1. Insert Kategori Utama
        $categories = [
            [
                'id'          => 1,
                'name'        => 'Hardware',
                'slug'        => 'hardware',
                'icon'        => 'cpu',
                'description' => 'Panduan penanganan masalah perangkat keras komputer, printer, kabel, dan periferal operasional kantor.',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => 2,
                'name'        => 'Jaringan',
                'slug'        => 'jaringan',
                'icon'        => 'wifi',
                'description' => 'Panduan troubleshooting konektivitas internet, WiFi kantor, registrasi IP/MAC Address, serta perangkat Router dan LAN.',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ];
        $this->db->table('categories')->insertBatch($categories);

        // 2. Insert Sub-Kategori
        $subCategories = [
            // Kategori 1: Hardware
            [
                'id'          => 1,
                'category_id' => 1,
                'name'        => 'Printer',
                'slug'        => 'printer',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => 2,
                'category_id' => 1,
                'name'        => 'Kabel',
                'slug'        => 'kabel',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            // Kategori 2: Jaringan
            [
                'id'          => 3,
                'category_id' => 2,
                'name'        => 'WiFi',
                'slug'        => 'wifi',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => 4,
                'category_id' => 2,
                'name'        => 'Router / LAN',
                'slug'        => 'router-lan',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ];
        $this->db->table('sub_categories')->insertBatch($subCategories);

        // 3. Insert Solusi Awal Operasional Perusahaan
        $solutions = [
            // Solusi 1: Hardware > Printer
            [
                'sub_category_id' => 1,
                'title'           => 'Printer Tidak Merespons Perintah Cetak (Offline/Spooler Hang)',
                'symptoms'        => 'Dokumen yang dikirim dari komputer tidak keluar di printer. Status antrean cetak (Print Queue) tertahan di status "Printing" atau "Error", atau indikator fisik printer menyala normal namun terdeteksi "Printer Offline" di sistem operasi Windows.',
                'root_cause'      => 'Layanan Windows Print Spooler mengalami hang/macet akibat antrean dokumen cetak yang korup, atau koneksi fisik kabel USB/LAN antara printer dan komputer/switch mengalami gangguan.',
                'steps'           => "1. Periksa fisik kabel USB atau kabel LAN pada printer dan pastikan terpasang rapat serta lampu indikator menyala normal.\n2. Tekan kombinasi tombol Windows + R pada keyboard, ketik \"services.msc\" lalu tekan Enter.\n3. Cari layanan bernama \"Print Spooler\" pada daftar Services.\n4. Klik kanan pada \"Print Spooler\", lalu pilih \"Stop\".\n5. Buka File Explorer dan arahkan ke folder: C:\\Windows\\System32\\spool\\PRINTERS (klik Continue jika muncul konfirmasi akses Administrator), lalu hapus seluruh file antrean yang tertahan di dalam folder tersebut.\n6. Kembali ke jendela Services, klik kanan pada \"Print Spooler\" dan pilih \"Start\" (atau \"Restart\").\n7. Buka Control Panel > Devices and Printers, klik kanan printer terkait > See what's printing > menu Printer, pastikan opsi \"Use Printer Offline\" tidak tercentang, lalu lakukan uji cetak ulang.",
                'attachment'      => null,
                'is_pinned'       => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            // Solusi 2: Jaringan > WiFi
            [
                'sub_category_id' => 3,
                'title'           => 'WiFi Terhubung Namun Tidak Ada Akses Internet',
                'symptoms'        => 'Perangkat (laptop atau smartphone) berhasil terhubung ke SSID WiFi kantor, tetapi muncul status "No Internet", "Connected without internet", atau halaman web/aplikasi tidak dapat diakses sama sekali.',
                'root_cause'      => 'Klien/perangkat belum terdaftar pada daftar klien resmi router (MAC Address Filtering / DHCP Static ARP Binding pada router utama belum mengenali perangkat tersebut).',
                'steps'           => "1. Identifikasi alamat fisik (MAC Address) pada perangkat pengguna: pada Windows buka Command Prompt (CMD) lalu ketik \"ipconfig /all\", atau pada smartphone buka Pengaturan WiFi > Detail Jaringan.\n2. Catat MAC Address (Physical Address) perangkat dan tentukan alokasi IP Address sesuai segmen divisi pengguna.\n3. Login ke halaman manajemen Router utama (WebFig / WinBox / Dashboard Router IT) menggunakan akun Administrator IT.\n4. Masuk ke menu IP > DHCP Server > Leases (serta menu ARP / Wireless Access List bila menggunakan autentikasi berlapis).\n5. Daftarkan IP Address dan MAC Address perangkat terlebih dahulu pada halaman manajemen router/DHCP Binding beserta keterangan nama karyawan & divisi.\n6. Minta pengguna untuk memutuskan koneksi (Forget/Disconnect) dari SSID WiFi kantor, tunggu 5 detik, lalu hubungkan kembali.",
                'attachment'      => null,
                'is_pinned'       => 0,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            // Solusi 3: Jaringan > Router / LAN (is_pinned = 1)
            [
                'sub_category_id' => 4,
                'title'           => 'Koneksi Jaringan LAN Melambat atau Putus-Nyambung',
                'symptoms'        => 'Akses internet maupun akses ke server lokal via kabel LAN terasa lambat tidak wajar, sering mengalami Request Time Out (RTO) saat tes ping, atau status jaringan di taskbar berubah-ubah antara terhubung dan terputus.',
                'root_cause'      => 'Port LAN pada router mengalami hang ringan (buffer/negotiation port state stuck) atau konektor RJ45 pada kabel LAN kurang pas/longgar pada soket port.',
                'steps'           => "1. Telusuri jalur kabel LAN yang terhubung ke port pada Router atau Switch distribusi.\n2. Cabut kabel LAN dari port router secara perlahan dengan menekan klip pengunci konektor RJ45.\n3. Periksa ujung konektor RJ45 dan pastikan pin kuning tembaga bersih serta pengunci tidak patah.\n4. Tunggu selama 5–10 detik agar kapasitor dan state negosiasi pada port router benar-benar ter-reset.\n5. Pasang kembali kabel LAN ke port router hingga terdengar bunyi \"klik\" dan pastikan konektor terpasang rapat.\n6. Pastikan lampu indikator link/activity pada port LAN menyala hijau/oranye stabil dan lakukan uji ping kembali.",
                'attachment'      => null,
                'is_pinned'       => 1,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            // Solusi 4: Hardware > Kabel (Pelengkap Sub-Kategori Kabel)
            [
                'sub_category_id' => 2,
                'title'           => 'Kabel Display (HDMI/VGA) Monitor Tidak Menampilkan Gambar (No Signal)',
                'symptoms'        => 'Komputer PC menyala normal (kipas berputar dan lampu indikator casing hidup), namun layar monitor menampilkan pesan "No Signal" atau "Check Signal Cable" lalu masuk ke mode sleep.',
                'root_cause'      => 'Konektor kabel HDMI/VGA pada port PC atau belakang monitor kendur akibat getaran meja, pin konektor kotor, atau kabel salah ditancapkan ke port motherboard (onboard) padahal PC menggunakan kartu grafis diskrit (VGA Card).',
                'steps'           => "1. Matikan monitor sementara, lalu cabut kedua ujung kabel HDMI/VGA baik dari sisi belakang monitor maupun dari sisi belakang PC.\n2. Bersihkan ujung konektor dari debu, dan jika menggunakan VGA, pastikan tidak ada jarum pin yang bengkok.\n3. Periksa bagian belakang casing PC: jika PC memiliki VGA Card tambahan (posisi horizontal di slot bawah), pastikan kabel ditancapkan ke port VGA Card tersebut, bukan ke port bawaan motherboard di bagian atas.\n4. Pasang kembali kabel dengan rapat (kencangkan baut pengunci jika menggunakan kabel VGA/DVI).\n5. Nyalakan monitor dan tekan tombol Input/Source pada monitor untuk memastikan mode input (HDMI / Analog VGA) sudah sesuai.",
                'attachment'      => null,
                'is_pinned'       => 0,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
        ];

        $this->db->table('solutions')->insertBatch($solutions);
    }
}
