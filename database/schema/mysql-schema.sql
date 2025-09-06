/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `batches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive','registration','finished') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'inactive',
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `kelas_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `batches_kelas_id_foreign` (`kelas_id`),
  CONSTRAINT `batches_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `hasil_ujians`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hasil_ujians` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint unsigned NOT NULL,
  `ujian_id` bigint unsigned NOT NULL,
  `jumlah_benar` int NOT NULL DEFAULT '0',
  `jumlah_salah` int NOT NULL DEFAULT '0',
  `nilai` int NOT NULL DEFAULT '0',
  `waktu_pengerjaan` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hasil_ujians_siswa_id_foreign` (`siswa_id`),
  KEY `hasil_ujians_ujian_id_foreign` (`ujian_id`),
  CONSTRAINT `hasil_ujians_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hasil_ujians_ujian_id_foreign` FOREIGN KEY (`ujian_id`) REFERENCES `ujians` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jawaban_siswas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jawaban_siswas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint unsigned NOT NULL,
  `ujian_id` bigint unsigned NOT NULL,
  `soal_id` bigint unsigned NOT NULL,
  `jawaban_pilihan` enum('A','B','C','D') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `benar` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jawaban_siswas_siswa_id_foreign` (`siswa_id`),
  KEY `jawaban_siswas_ujian_id_foreign` (`ujian_id`),
  KEY `jawaban_siswas_soal_id_foreign` (`soal_id`),
  CONSTRAINT `jawaban_siswas_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `jawaban_siswas_soal_id_foreign` FOREIGN KEY (`soal_id`) REFERENCES `soals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `jawaban_siswas_ujian_id_foreign` FOREIGN KEY (`ujian_id`) REFERENCES `ujians` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jawabans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jawabans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `soal_id` bigint unsigned NOT NULL,
  `pilihan` enum('A','B','C','D') COLLATE utf8mb4_unicode_ci NOT NULL,
  `teks` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `benar` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jawabans_soal_id_foreign` (`soal_id`),
  CONSTRAINT `jawabans_soal_id_foreign` FOREIGN KEY (`soal_id`) REFERENCES `soals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kelas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `type` enum('intensif','partime') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dp_persen` int NOT NULL DEFAULT '0',
  `waktu_magang` int NOT NULL DEFAULT '0',
  `durasi_belajar` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lamaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lamaran` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `lowongan_id` bigint unsigned NOT NULL,
  `status` enum('Pending','Ditinjau','Diterima','Ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `resume_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `surat_lamaran` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lamaran_user_id_foreign` (`user_id`),
  KEY `lamaran_lowongan_id_foreign` (`lowongan_id`),
  CONSTRAINT `lamaran_lowongan_id_foreign` FOREIGN KEY (`lowongan_id`) REFERENCES `lowongan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lamaran_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lowongan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lowongan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `posisi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `perusahaan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lokasi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gaji` bigint unsigned DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `persyaratan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe` enum('Full-time','Part-time','Internship','Contract') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Full-time',
  `status` enum('Aktif','Ditutup') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Aktif',
  `deadline` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `materis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `materis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `materi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kelas_id` bigint unsigned NOT NULL,
  `batch_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `materis_kelas_id_foreign` (`kelas_id`),
  KEY `materis_batch_id_foreign` (`batch_id`),
  CONSTRAINT `materis_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `materis_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pembayarans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pembayarans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tagihan_id` bigint unsigned NOT NULL,
  `jumlah_dibayar` decimal(15,2) NOT NULL,
  `bukti_pembayaran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','disetujui','ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pembayarans_tagihan_id_foreign` (`tagihan_id`),
  CONSTRAINT `pembayarans_tagihan_id_foreign` FOREIGN KEY (`tagihan_id`) REFERENCES `tagihans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pendaftaran_peserta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pendaftaran_peserta` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `batch_id` bigint unsigned NOT NULL,
  `kelas_id` bigint unsigned DEFAULT NULL,
  `nama_lengkap` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link_register` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_hp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pendidikan_terakhir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mengetahui_program_dari` enum('Instagram','Tiktok','Facebook','Website','Teman/Keluarga','Google','Lain-lain') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Lain-lain',
  `status` enum('pending','confirmed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `total_tagihan` decimal(15,2) DEFAULT NULL,
  `jumlah_cicilan` int DEFAULT NULL,
  `bukti_pembayaran_dp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pendaftaran_peserta_email_unique` (`email`),
  KEY `pendaftaran_peserta_batch_id_foreign` (`batch_id`),
  KEY `pendaftaran_peserta_kelas_id_foreign` (`kelas_id`),
  CONSTRAINT `pendaftaran_peserta_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pendaftaran_peserta_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pengajar_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengajar_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pengajar_id` bigint unsigned NOT NULL,
  `nama_lengkap` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pengajar_details_pengajar_id_foreign` (`pengajar_id`),
  CONSTRAINT `pengajar_details_pengajar_id_foreign` FOREIGN KEY (`pengajar_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pengajar_kelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengajar_kelas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pengajar_detail_id` bigint unsigned NOT NULL,
  `kelas_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pengajar_kelas_pengajar_detail_id_foreign` (`pengajar_detail_id`),
  KEY `pengajar_kelas_kelas_id_foreign` (`kelas_id`),
  CONSTRAINT `pengajar_kelas_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pengajar_kelas_pengajar_detail_id_foreign` FOREIGN KEY (`pengajar_detail_id`) REFERENCES `pengajar_details` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `post_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `post_id` bigint unsigned NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_comments_user_id_foreign` (`user_id`),
  KEY `post_comments_post_id_foreign` (`post_id`),
  KEY `post_comments_parent_id_foreign` (`parent_id`),
  CONSTRAINT `post_comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `post_comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `post_comments_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `post_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `post_likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_likes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `post_id` bigint unsigned DEFAULT NULL,
  `comment_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `post_likes_user_id_post_id_comment_id_unique` (`user_id`,`post_id`,`comment_id`),
  KEY `post_likes_post_id_foreign` (`post_id`),
  KEY `post_likes_comment_id_foreign` (`comment_id`),
  CONSTRAINT `post_likes_comment_id_foreign` FOREIGN KEY (`comment_id`) REFERENCES `post_comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `post_likes_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `post_likes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `media_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `media_type` enum('image','video') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `likes_count` int NOT NULL DEFAULT '0',
  `comments_count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `posts_user_id_foreign` (`user_id`),
  CONSTRAINT `posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `siswa_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `siswa_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint unsigned NOT NULL,
  `total_tagihan` decimal(15,2) DEFAULT NULL,
  `jumlah_cicilan` int DEFAULT NULL,
  `tagihan_per_bulan` decimal(15,2) DEFAULT NULL,
  `ikut_magang` tinyint(1) NOT NULL DEFAULT '1',
  `kelas_id` bigint unsigned DEFAULT NULL,
  `batch_id` bigint unsigned DEFAULT NULL,
  `status` enum('active','alumni') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `nama_lengkap` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `no_hp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `pendidikan_terakhir` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('laki-laki','perempuan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mengetahui_program_dari` enum('instagram','tiktok','facebook','website','teman/keluarga','google','lain_lain') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'lain_lain',
  `link_instagram` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_tiktok` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_linkedin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_facebook` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_x` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link_github` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `siswa_details_siswa_id_foreign` (`siswa_id`),
  KEY `siswa_details_kelas_id_foreign` (`kelas_id`),
  KEY `siswa_details_batch_id_foreign` (`batch_id`),
  CONSTRAINT `siswa_details_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `siswa_details_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `siswa_details_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `soals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `soals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ujian_id` bigint unsigned NOT NULL,
  `soal` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `soals_ujian_id_foreign` (`ujian_id`),
  CONSTRAINT `soals_ujian_id_foreign` FOREIGN KEY (`ujian_id`) REFERENCES `ujians` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tagihans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tagihans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint unsigned NOT NULL,
  `tanggal_tagihan` date NOT NULL,
  `jumlah_tagihan` decimal(15,2) NOT NULL,
  `status` enum('belum_dibayar','menunggu_konfirmasi','lunas') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum_dibayar',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tagihans_siswa_id_foreign` (`siswa_id`),
  CONSTRAINT `tagihans_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswa_details` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ujians`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ujians` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kelas_id` bigint unsigned DEFAULT NULL,
  `status` enum('pending','active','finished') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `waktu` int DEFAULT NULL,
  `batch_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ujians_kelas_id_foreign` (`kelas_id`),
  KEY `ujians_batch_id_foreign` (`batch_id`),
  CONSTRAINT `ujians_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ujians_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `role` enum('admin','pengajar','siswa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'siswa',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2025_07_13_101605_create_batches_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2025_07_15_040000_create_kelas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2025_07_15_041757_create_siswa_details_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2025_07_15_045839_create_pengajar_details_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2025_07_15_050807_create_ujians_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2025_07_15_050836_create_soals_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2025_07_15_050934_create_jawabans_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2025_07_15_051007_create_jawaban_siswas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2025_07_15_051029_create_hasil_ujians_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2025_07_27_095145_create_pengajar_kelas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2025_07_27_193550_add_batch_id_to_ujians_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2025_08_04_034042_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2025_08_06_050731_add_kelas_id_to_batches_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2025_08_06_174119_add_more_fields_to_siswa_details_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2025_08_06_174315_add_tanggal_lahir_dan_jenis_kelamin_to_siswa_details_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2025_08_07_043958_add_status_to_siswa_details_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2025_08_07_044108_create_pendaftaran_peserta_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2025_08_09_163245_update_multiple_tables_for_tagihan',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2025_08_11_075953_create_tagihans_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2025_08_11_080246_create_pembayarans_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2025_08_11_081911_add_periode_to_batches_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2025_08_22_151848_add_link_register_to_pendaftaran_peserta_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2025_08_26_090619_create_lowongan_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2025_08_26_090620_create_lamaran_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2025_08_28_074259_create_posts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2025_08_28_074300_create_post_comments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2025_08_28_074301_create_post_likes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2025_08_28_092100_fix_posts_table_structure',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2025_08_31_230517_add_parent_id_to_post_comments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2025_09_03_075902_create_materis_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2025_09_03_094933_add_judul_to_materis_table',1);
