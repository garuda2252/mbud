-- ============================================================================
-- Database: db_it_knowledge_base
-- Internal IT Knowledge Base (Troubleshooting Wiki) - CodeIgniter 4
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `db_it_knowledge_base`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `db_it_knowledge_base`;

SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------------------------------------------------------
-- 1. Tabel: categories
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL,
  `icon` VARCHAR(50) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. Tabel: sub_categories
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `sub_categories`;
CREATE TABLE `sub_categories` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` INT(11) UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(120) NOT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `sub_categories_category_id_foreign` (`category_id`),
  CONSTRAINT `sub_categories_category_id_foreign`
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. Tabel: solutions
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `solutions`;
CREATE TABLE `solutions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sub_category_id` INT(11) UNSIGNED NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `symptoms` TEXT NOT NULL,
  `root_cause` TEXT NOT NULL,
  `steps` TEXT NOT NULL,
  `attachment` VARCHAR(255) DEFAULT NULL,
  `is_pinned` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `solutions_sub_category_id_foreign` (`sub_category_id`),
  CONSTRAINT `solutions_sub_category_id_foreign`
    FOREIGN KEY (`sub_category_id`) REFERENCES `sub_categories` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------------------------
-- Data Awal (Seed Data)
-- ----------------------------------------------------------------------------
INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Hardware', 'hardware', 'cpu', 'Panduan penanganan masalah perangkat keras komputer, printer, kabel, dan periferal operasional kantor.', NOW(), NOW()),
(2, 'Jaringan', 'jaringan', 'wifi', 'Panduan troubleshooting konektivitas internet, WiFi kantor, registrasi IP/MAC Address, serta perangkat Router dan LAN.', NOW(), NOW());

INSERT INTO `sub_categories` (`id`, `category_id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 1, 'Printer', 'printer', NOW(), NOW()),
(2, 1, 'Kabel', 'kabel', NOW(), NOW()),
(3, 2, 'WiFi', 'wifi', NOW(), NOW()),
(4, 2, 'Router / LAN', 'router-lan', NOW(), NOW());

INSERT INTO `solutions` (`id`, `sub_category_id`, `title`, `symptoms`, `root_cause`, `steps`, `attachment`, `is_pinned`, `created_at`, `updated_at`) VALUES
(
  1,
  1,
  'Printer Tidak Merespons Perintah Cetak (Offline/Spooler Hang)',
  'Dokumen yang dikirim dari komputer tidak keluar di printer. Status antrean cetak (Print Queue) tertahan di status "Printing" atau "Error", atau indikator fisik printer menyala normal namun terdeteksi "Printer Offline" di sistem operasi Windows.',
  'Layanan Windows Print Spooler mengalami hang/macet akibat antrean dokumen cetak yang korup, atau koneksi fisik kabel USB/LAN antara printer dan komputer/switch mengalami gangguan.',
  '1. Periksa fisik kabel USB atau kabel LAN pada printer dan pastikan terpasang rapat serta lampu indikator menyala normal.\n2. Tekan kombinasi tombol Windows + R pada keyboard, ketik "services.msc" lalu tekan Enter.\n3. Cari layanan bernama "Print Spooler" pada daftar Services.\n4. Klik kanan pada "Print Spooler", lalu pilih "Stop".\n5. Buka File Explorer dan arahkan ke folder: C:\\Windows\\System32\\spool\\PRINTERS (klik Continue jika muncul konfirmasi akses Administrator), lalu hapus seluruh file antrean yang tertahan di dalam folder tersebut.\n6. Kembali ke jendela Services, klik kanan pada "Print Spooler" dan pilih "Start" (atau "Restart").\n7. Buka Control Panel > Devices and Printers, klik kanan printer terkait > See what''s printing > menu Printer, pastikan opsi "Use Printer Offline" tidak tercentang, lalu lakukan uji cetak ulang.',
  NULL,
  1,
  NOW(),
  NOW()
),
(
  2,
  3,
  'WiFi Terhubung Namun Tidak Ada Akses Internet',
  'Perangkat (laptop atau smartphone) berhasil terhubung ke SSID WiFi kantor, tetapi muncul status "No Internet", "Connected without internet", atau halaman web/aplikasi tidak dapat diakses sama sekali.',
  'Klien/perangkat belum terdaftar pada daftar klien resmi router (MAC Address Filtering / DHCP Static ARP Binding pada router utama belum mengenali perangkat tersebut).',
  '1. Identifikasi alamat fisik (MAC Address) pada perangkat pengguna: pada Windows buka Command Prompt (CMD) lalu ketik "ipconfig /all", atau pada smartphone buka Pengaturan WiFi > Detail Jaringan.\n2. Catat MAC Address (Physical Address) perangkat dan tentukan alokasi IP Address sesuai segmen divisi pengguna.\n3. Login ke halaman manajemen Router utama (WebFig / WinBox / Dashboard Router IT) menggunakan akun Administrator IT.\n4. Masuk ke menu IP > DHCP Server > Leases (serta menu ARP / Wireless Access List bila menggunakan autentikasi berlapis).\n5. Daftarkan IP Address dan MAC Address perangkat terlebih dahulu pada halaman manajemen router/DHCP Binding beserta keterangan nama karyawan & divisi.\n6. Minta pengguna untuk memutuskan koneksi (Forget/Disconnect) dari SSID WiFi kantor, tunggu 5 detik, lalu hubungkan kembali.',
  NULL,
  0,
  NOW(),
  NOW()
),
(
  3,
  4,
  'Koneksi Jaringan LAN Melambat atau Putus-Nyambung',
  'Akses internet maupun akses ke server lokal via kabel LAN terasa lambat tidak wajar, sering mengalami Request Time Out (RTO) saat tes ping, atau status jaringan di taskbar berubah-ubah antara terhubung dan terputus.',
  'Port LAN pada router mengalami hang ringan (buffer/negotiation port state stuck) atau konektor RJ45 pada kabel LAN kurang pas/longgar pada soket port.',
  '1. Telusuri jalur kabel LAN yang terhubung ke port pada Router atau Switch distribusi.\n2. Cabut kabel LAN dari port router secara perlahan dengan menekan klip pengunci konektor RJ45.\n3. Periksa ujung konektor RJ45 dan pastikan pin kuning tembaga bersih serta pengunci tidak patah.\n4. Tunggu selama 5–10 detik agar kapasitor dan state negosiasi pada port router benar-benar ter-reset.\n5. Pasang kembali kabel LAN ke port router hingga terdengar bunyi "klik" dan pastikan konektor terpasang rapat.\n6. Pastikan lampu indikator link/activity pada port LAN menyala hijau/oranye stabil dan lakukan uji ping kembali.',
  NULL,
  1,
  NOW(),
  NOW()
),
(
  4,
  2,
  'Kabel Display (HDMI/VGA) Monitor Tidak Menampilkan Gambar (No Signal)',
  'Komputer PC menyala normal (kipas berputar dan lampu indikator casing hidup), namun layar monitor menampilkan pesan "No Signal" atau "Check Signal Cable" lalu masuk ke mode sleep.',
  'Konektor kabel HDMI/VGA pada port PC atau belakang monitor kendur akibat getaran meja, pin konektor kotor, atau kabel salah ditancapkan ke port motherboard (onboard) padahal PC menggunakan kartu grafis diskrit (VGA Card).',
  '1. Matikan monitor sementara, lalu cabut kedua ujung kabel HDMI/VGA baik dari sisi belakang monitor maupun dari sisi belakang PC.\n2. Bersihkan ujung konektor dari debu, dan jika menggunakan VGA, pastikan tidak ada jarum pin yang bengkok.\n3. Periksa bagian belakang casing PC: jika PC memiliki VGA Card tambahan (posisi horizontal di slot bawah), pastikan kabel ditancapkan ke port VGA Card tersebut, bukan ke port bawaan motherboard di bagian atas.\n4. Pasang kembali kabel dengan rapat (kencangkan baut pengunci jika menggunakan kabel VGA/DVI).\n5. Nyalakan monitor dan tekan tombol Input/Source pada monitor untuk memastikan mode input (HDMI / Analog VGA) sudah sesuai.',
  NULL,
  0,
  NOW(),
  NOW()
);
