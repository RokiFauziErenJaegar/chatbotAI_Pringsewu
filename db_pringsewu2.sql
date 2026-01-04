-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 04, 2026 at 01:47 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_pringsewu2`
--

-- --------------------------------------------------------

--
-- Table structure for table `anak_berhenti_sekolah`
--

CREATE TABLE `anak_berhenti_sekolah` (
  `id` int(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `kecamatan` varchar(100) DEFAULT NULL,
  `jenjang` varchar(50) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anak_berhenti_sekolah`
--

INSERT INTO `anak_berhenti_sekolah` (`id`, `tahun`, `kecamatan`, `jenjang`, `jumlah`, `keterangan`, `created_at`) VALUES
(1, 2023, 'Pringsewu', 'SD', 12, 'Data lapangan', '2025-12-12 09:19:33'),
(2, 2023, 'Pringsewu', 'SMP', 7, 'Laporan sekolah', '2025-12-12 09:19:33'),
(3, 2023, 'Gading Rejo', 'SMA', 5, 'Putus karena ekonomi', '2025-12-12 09:19:33'),
(4, 2024, 'Pagelaran', 'SD', 9, 'Tercatat oleh Dinas Pendidikan', '2025-12-12 09:19:33');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('chatbot-ai-dashboard-pringsewu-cache-chat_answer:10a863cc37676d5d0d55299a51486e898e289899', 's:422:\"Data yang diberikan tidak menunjukkan adanya hasil untuk jenis produk terbanyak, karena \"rows\" dalam data kosong. Untuk memberikan informasi yang lebih lengkap, saya memerlukan data yang mencakup jenis produk dan jumlahnya. \n\nSaran pertanyaan lanjutan:\n1. Dapatkah Anda memberikan data yang lebih spesifik mengenai jenis produk dan jumlahnya?\n2. Apakah ada filter atau kriteria lain yang ingin diterapkan pada data produk?\";', 1767531467),
('chatbot-ai-dashboard-pringsewu-cache-chat_answer:1afea5561a60fa9d3e22e52889b36917f1fc6db8', 's:372:\"Berdasarkan data yang diberikan, berikut adalah jumlah tenaga kerja per kecamatan di Pringsewu:\n\n1. **ADILUWIH**: 96 tenaga kerja\n2. **AMBARAWA**: 41 tenaga kerja\n3. **BANYUMAS**: 4 tenaga kerja\n\nData untuk kecamatan lainnya tidak tersedia dalam informasi yang diberikan. Jika Anda memerlukan data lebih lengkap atau informasi tambahan, silakan ajukan pertanyaan lanjutan.\";', 1767531621),
('chatbot-ai-dashboard-pringsewu-cache-chat_answer:801ed7acdc2d63e5d2ba140ee9663635044a2c9a', 's:416:\"Data yang diberikan menunjukkan bahwa tidak ada kecamatan yang terdaftar dalam tabel ikm_koperindag, karena hasilnya kosong (rows: []). \n\nUntuk mendapatkan informasi lebih lanjut, Anda dapat mempertimbangkan pertanyaan lanjutan seperti:\n1. \"Apakah ada data kecamatan lain yang bisa ditambahkan ke dalam tabel ikm_koperindag?\"\n2. \"Apa kriteria yang digunakan untuk memasukkan kecamatan ke dalam tabel ikm_koperindag?\"\";', 1767531682),
('chatbot-ai-dashboard-pringsewu-cache-chat_answer:93506238479442c948452972f05238c46ad2bc19', 's:412:\"Data yang diberikan tidak menunjukkan adanya kecamatan yang terdaftar, karena hasilnya kosong (rows: []). Oleh karena itu, saya tidak dapat memberikan jumlah data kecamatan yang ada.\n\nUntuk mendapatkan informasi lebih lanjut, Anda bisa mempertimbangkan untuk mengajukan pertanyaan lain, seperti:\n- \"Berapa jumlah kecamatan yang terdaftar dalam data tanpa filter?\"\n- \"Apa saja nama kecamatan yang ada dalam data?\"\";', 1767531930),
('chatbot-ai-dashboard-pringsewu-cache-chat_answer:9635224c8b2b667d28bc098574715654c1fa421d', 's:205:\"Berdasarkan data yang diberikan, jumlah IKM di Kecamatan Ambarawa adalah 54, sedangkan di Kecamatan Adiluwih tidak terdapat IKM sama sekali, dengan jumlah 0. \n\nJika ada pertanyaan lanjutan, silakan ajukan!\";', 1767531573),
('chatbot-ai-dashboard-pringsewu-cache-chat_answer:a49feecaba6bbcf68d5570591dc1710aafc26371', 's:456:\"Data yang diberikan tidak mencantumkan nama kecamatan karena hasil query menunjukkan bahwa tidak ada data yang ditemukan (rows: []). Untuk memberikan informasi yang lebih lengkap, saya memerlukan data yang berisi nama-nama kecamatan. \n\nPertanyaan lanjutan yang bisa diajukan adalah:\n1. Apakah ada data lain yang bisa digunakan untuk mencari nama kecamatan?\n2. Apakah ada filter atau kriteria tertentu yang perlu diterapkan untuk mendapatkan data kecamatan?\";', 1767531946),
('chatbot-ai-dashboard-pringsewu-cache-chat_answer:ca95aa99c7682d05ac1296a8512a208d57fc2ee3', 's:406:\"Data yang diberikan tidak menunjukkan adanya produk dengan jenis \"random saja\" di kecamatan manapun. Oleh karena itu, tidak ada informasi yang dapat disajikan mengenai jenis produk tersebut.\n\nSaran pertanyaan lanjutan:\n- Apakah Anda ingin mengetahui jenis produk lain yang tersedia di kecamatan tertentu?\n- Atau, apakah Anda ingin melihat jumlah produk berdasarkan jenis tertentu yang sudah ada dalam data?\";', 1767531503),
('chatbot-ai-dashboard-pringsewu-cache-chat_answer:ce4e2291581285147c8a4b0d60e68d79938ba53f', 's:381:\"Data yang diberikan tidak menunjukkan adanya informasi mengenai jumlah tenaga kerja berdasarkan kecamatan. Tabel yang di-query tidak menghasilkan data (rows kosong). \n\nUntuk menjawab pertanyaan ini, saya memerlukan data yang berisi jumlah tenaga kerja per kecamatan. Pertanyaan lanjutan yang dapat diajukan adalah: \"Dapatkah Anda memberikan data jumlah tenaga kerja per kecamatan?\"\";', 1767531594),
('chatbot-ai-dashboard-pringsewu-cache-chat_answer:d15ea68a7db973da9b2f4483a2b713a23e17791a', 's:515:\"Data yang diberikan tidak mengandung informasi mengenai kecamatan, karena hasil query menunjukkan bahwa tidak ada baris data yang tersedia (rows: []). Oleh karena itu, saya tidak dapat memberikan daftar kecamatan yang ada.\n\nUntuk mendapatkan informasi lebih lanjut, Anda bisa mempertimbangkan untuk mengajukan pertanyaan lanjutan, seperti:\n- \"Dapatkah Anda memberikan data kecamatan yang ada dalam tabel ikm_koperindag tanpa filter?\" \n- \"Apakah ada filter lain yang bisa digunakan untuk mendapatkan data kecamatan?\"\";', 1767531661),
('chatbot-ai-dashboard-pringsewu-cache-chat_answer:fe9832eb5948283c53e714dfe245c29c51753118', 's:413:\"Data yang diberikan tidak mencakup informasi tentang nama perusahaan dari Ani Lailia. Untuk menjawab pertanyaan tersebut, saya memerlukan data yang lebih spesifik mengenai individu atau perusahaan yang terkait dengan Ani Lailia. \n\nSaran pertanyaan lanjutan: \"Apa data perusahaan yang terkait dengan Ani Lailia?\" atau \"Dapatkah Anda memberikan informasi lebih lanjut tentang individu atau perusahaan yang relevan?\"\";', 1767531787),
('chatbot-ai-dashboard-pringsewu-cache-dc7dd0092ad96fadce60b6dd82dabf1a', 'i:2;', 1767531869),
('chatbot-ai-dashboard-pringsewu-cache-dc7dd0092ad96fadce60b6dd82dabf1a:timer', 'i:1767531869;', 1767531869);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ikm_koperindag`
--

CREATE TABLE `ikm_koperindag` (
  `id` int(11) NOT NULL,
  `nama_perusahaan` varchar(255) NOT NULL,
  `nama_pemilik` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `kecamatan` varchar(100) DEFAULT NULL,
  `telepon` varchar(50) DEFAULT NULL,
  `jenis_produk` varchar(150) DEFAULT NULL,
  `kapasitas_produksi` varchar(100) DEFAULT NULL,
  `jumlah_tenaga_kerja` int(11) DEFAULT NULL,
  `perijinan` varchar(255) DEFAULT NULL,
  `produk_utama` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ikm_koperindag`
--

INSERT INTO `ikm_koperindag` (`id`, `nama_perusahaan`, `nama_pemilik`, `alamat`, `kecamatan`, `telepon`, `jenis_produk`, `kapasitas_produksi`, `jumlah_tenaga_kerja`, `perijinan`, `produk_utama`, `created_at`, `updated_at`) VALUES
(1, 'CV. DANAPATI BERKAH INOVASI', 'Ani Lailia', 'Jl. Dadirejo Rt/Rw 04/02 Waringinsari Timur Kec. Adiluwih', 'ADILUWIH', '081369176887', 'Kerajinan', '2000 pcs/bln', 53, 'NIB, IUI', 'Keset Karakter \"Sumringah\"', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(2, 'BERKAH PERCA', 'Tri Kuswanto', 'Pekon Bandungbaru Barat ', 'ADILUWIH', '081272492819', 'Kerajinan', NULL, NULL, NULL, 'Sprei, Sarung Bantal, Sarung Kasur, Keset', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(3, 'KERIPIK FAMOUS', 'Maslihah', 'Bandung Baru Kec. Adiluwih', 'ADILUWIH', '085645183310', 'Makanan ', NULL, NULL, 'NIB, Halal', 'Keripik Singkong, Keripik Talas, Keripik Tempe', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(4, 'Kelompok Sahabat', 'Ani Kumala/ Yufila Lufni', 'Bandung Baru ', 'ADILUWIH', '082175858586', 'Kerajinan', NULL, NULL, NULL, 'Keset Karakter dari kain perca', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(5, 'Gula Aren Asli', 'Amelia gustiana', 'Bandung baru barat.rt 08/rw0', 'ADILUWIH', '085381852452', 'Makanan', NULL, NULL, NULL, 'Gula semut', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(6, 'Ismaya', 'Ismaya', 'Bandung Baru', 'ADILUWIH', '085269335357', 'Kerajinan', NULL, NULL, NULL, 'Kerajinan Peralatan Rumah Tangga Berbahan Aluminium', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(7, 'Makanan Ringan', 'Lestari', 'waringin Sari timur ', 'ADILUWIH', '085279027712', 'Makanan', NULL, NULL, NULL, 'kue wijen.', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(8, 'Atta cake', 'Siti Sulandari', 'Adiluwih', 'ADILUWIH', '085269513078', 'Makanan', NULL, NULL, NULL, 'Aneka Cake Kering dan basah', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(9, 'Gurenja Alfatih Grup', 'Mahfud Efendi', 'Pekon Srikaton', 'ADILUWIH', '081273775217', 'Makanan', NULL, NULL, NULL, 'Gula Aren Jahe', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(10, 'Ghema Coffe', 'Nurholis', 'Bandungbaru Rt.004/Rw.002', 'ADILUWIH', '082371813210', 'Makanan', NULL, NULL, 'PIRT', 'Kopi bubuk', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(11, 'Dapur Cinta', 'Tasmawati', 'Sinarwaya', 'ADILUWIH', '085838432208', 'Makanan', NULL, 2, 'PIRT, NIB', 'Kue Cucur khas Lampung', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(12, 'Kerajinan Craft', 'Eli Juniati', 'Adiluwih', 'ADILUWIH', '088276530259', 'Kerajinan', NULL, 2, NULL, 'Kerajinan', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(13, 'Menjahit', 'Sutiah', 'Adiluwih', 'ADILUWIH', '082306772693', 'Kerajinan', NULL, 2, NULL, 'menjahit', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(14, 'Kain Perca', 'lusin wagiyanti', 'ds. Bandung baru', 'ADILUWIH', '085378853525', 'Kerajinan', NULL, 10, 'NIB, PIRT, Halal', 'kerajinan', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(15, 'BERKAH JAYA ', 'muhibatul rahmah', 'bandung baru, rt 46 rw 14', 'ADILUWIH', '082373921435', 'makanan', '70 kg/bln', 2, 'PIRT', 'Aneka kripik', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(16, 'Pangan Lestari', 'Usriyati', 'Adiluwih', 'ADILUWIH', '082282254209', 'Makanan', NULL, 3, 'NIB, PIRT', 'Tepung Mocaf, Aneka Keripik', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(17, 'Aneka Makanan Ringan', 'Siti Munawaroh', 'Waringinsari Timur', 'ADILUWIH', '085891417281', 'Makanan', NULL, NULL, NULL, 'Kripik Pisang, Keipik Singkong, Tusuk Gigi', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(18, 'Alzahra', 'Ani Puspita', 'Jln Gg. Prima F1 Pekon Srikaton Rt.01 Rw. 01', 'ADILUWIH', '085809508299', 'Makanan', '500 kg / bln', 3, 'PIRT', 'Sale Pisang, Kopi Bubuk, Sego tiwul', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(19, 'Kopi Robusta Mbah Ndut', 'Thohari', 'Bandungbaru Rt.02 Rw.01', 'ADILUWIH', '082372287812', 'Makanan', '50 kg/bln', 4, 'NIB', 'Pengolahan kopi bubuk', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(20, 'Aneka Keripik', 'Lusin Wagiyanti', 'Bandungbaru', 'ADILUWIH', '085378853525', 'Makanan', '400 kg / bln', 4, 'NIB', 'Aneka Keripik', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(21, 'Aneka Kue kering', 'Dwi Joni Suhendra', 'Kuta Waringin', 'ADILUWIH', '082281293492', 'Makanan', '300 kg/bln', NULL, 'NIB', 'Kelanting, Keripik Singkong, Keripik Pisang', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(22, 'Aneka Kue', 'Yuniati', 'Bandungbaru', 'ADILUWIH', '083168306187', 'Makanan', NULL, NULL, 'NIB', 'Aneka Kue Kering dan Basah', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(23, 'Tahu Goreng Baba', 'Aswana', 'Bandungbaru', 'ADILUWIH', '081369026297', 'Makanan', NULL, NULL, 'NIB', 'Tahu goreng, Tahu Krispi', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(24, 'Kacang Mede \"Kapten Ri\"', 'Indah Tuti Alawiyah', 'Bandungbaru', 'ADILUWIH', '082223152686', 'Makanan', NULL, NULL, 'NIB', 'aneka snack', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(25, 'Pawon Barokah', 'Pujiani', 'Enggal Rejo', 'ADILUWIH', '087794218570', 'Makanan', NULL, NULL, NULL, 'Emping Jagung', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(26, 'Aneka Kue', 'Andika Nur Andriani', 'Enggal Rejo', 'ADILUWIH', '082372504061', 'Makanan', NULL, NULL, NULL, 'Aneka Kue Kering & Kue Basah', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(27, 'Kopi Bubuk', 'Fitri Afria Melda', 'Enggal Rejo', 'ADILUWIH', '085266601481', 'Makanan', NULL, NULL, NULL, 'Kopi Bubuk', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(28, 'Keripik Pisang', 'Eny Susanty', 'Srikaton', 'ADILUWIH', '085266316612', 'Makanan', '40 kg/bln', NULL, NULL, 'Keripik Pisang', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(29, 'Keripik Pare', 'Daryanti', 'Srikaton', 'ADILUWIH', '085788164699', 'Makanan', '40 kg/bln', NULL, NULL, 'Keripik Pare', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(30, 'Bubuk Cabe', 'Isti Paryanti', 'Srikaton', 'ADILUWIH', '085369656805', 'Makanan', '10 kg/bln', NULL, NULL, 'Bubuk Cabe', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(31, 'Aneka Kue', 'Tuti Rahayu', 'Srikaton', 'ADILUWIH', '082186408477', 'Makanan', '40 kg/bln', NULL, NULL, 'Kue Onde-onde', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(32, 'Pandu Rezeki', 'Minarsih', 'Tri Tunggal Mulya', 'ADILUWIH', '082181122323', 'Makanan', '1,5 ton/Bln', 7, 'NIB', 'Aneka olahan Daging dan Ikan', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(33, 'Naura Jaya', 'Rusmiati', 'Purwodadi', 'ADILUWIH', '085841833709', 'Makanan', NULL, NULL, 'NIB, PIRT', 'Kacang Kulit Sangrai', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(34, 'vivique', 'YULIANA VIVI AYU WARDANI ', 'Totokarto RT 7 RW 3 kec. Adiluwih kabupaten Pringsewu ', 'ADILUWIH', '085269444227', 'Makanan', NULL, NULL, NULL, 'Aneka Kue Basah', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(35, 'Sale pisang alzahra ', 'Eka Hanum Prastiwi ', 'Jl. Dari Pekon Srikaton, Adi Luwih', 'ADILUWIH', '083169667393', 'Makanan', NULL, 4, 'PIRT', 'Sale pisang ', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(36, 'SEFTY BATIK', 'Septi Handayani', 'Ambarawa RT/RW 02/03 ', 'AMBARAWA', '081246908010   085267276010', 'Kerajinan', NULL, NULL, NULL, 'Batik Tulis', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(37, 'BATIK TULIS AMBARSARI', 'Suwarti', 'RT/RW 003/002 Ambarawa Barat ', 'AMBARAWA', '0895389612148', 'Kerajinan', NULL, NULL, 'NIB', 'Batik Tulis', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(38, 'UMI\'F CRAFT', 'Musrifah', 'Dusun 2 Sumberagung ', 'AMBARAWA', '081340214913', 'Kerajinan', NULL, NULL, NULL, 'Tas, Sandal dan Sepatu Rajut', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(39, 'Kedai Siomay Umi Fatim', 'Siti Fatimah', 'Sumber Agung', 'AMBARAWA', '081279938825', 'Makanan', NULL, NULL, NULL, 'Siomay, mpekmpek, Tekwan', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(40, 'Makanan Ringan', 'Rudi Atmoko ', 'Sumber Agung RT / RW 003 / 001  ', 'AMBARAWA', '085280606654', 'Makanan', NULL, NULL, NULL, 'Makanan Ringan', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(41, 'Susi Tailor', 'Tri Susiati', 'Sumberagung RT 04 RW 03', 'AMBARAWA', '081271218285', 'Kerajinan', NULL, NULL, NULL, 'Pakaian dan Kain perca', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(42, 'Penjahit', 'Insiati', 'Sukowati Kresnomulyo', 'AMBARAWA', '087871106350', 'Kerajinan', NULL, NULL, NULL, 'Penjahit pakaian', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(43, 'Shelly Konveksi', 'Sri Hartati', 'Rt 04 Rw 01 Pekon Sumberagung', 'AMBARAWA', '085357310500', 'Kerajinan', NULL, NULL, NULL, 'Konveksi, Makanan Ringan, Kopi bubuk', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(44, 'Aneka Kreasi', 'Rumeka', 'Pekon Sumberagung', 'AMBARAWA', '087870146691', 'Kerajinan', NULL, NULL, NULL, 'Kerajinan Rajutan', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(45, 'PP Krisna', 'Budi Setiawan', 'Kresnomulyo Rt.001', 'AMBARAWA', '081379916899', 'Makanan', NULL, NULL, 'TDI', 'Industri Penggilingan Padi ', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(46, 'PP Erika', 'Kusnedi', 'Ambarawa Barat Rt.001 Rw.001', 'AMBARAWA', '081540881287', 'Makanan', NULL, NULL, 'TDI', 'Industri Penggilingan Padi ', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(47, 'Kopi Bubuk Tunas Muda', 'Lia Sekarnidi', 'Sumberagung', 'AMBARAWA', '085838464826', 'Makanan', NULL, NULL, 'PIRT', 'Pengolahan kopi bubuk', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(48, 'Kopi Bubuk 99', 'Ahmad Muadzin', 'Jl. TK Al Basyar Rt.02 Pekon Sumberagung', 'AMBARAWA', '085268626972', 'Makanan', NULL, NULL, 'SIUP', 'Pengolahan kopi bubuk', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(49, 'Aby Bubuk Jahe', 'Basuki Rahmat', 'Jl. Jatiagung Rt.03 Rw.02 Pekon Jatiagung', 'AMBARAWA', '085279217243', 'Makanan', NULL, NULL, 'PIRT', 'Produksi Jahe Bubuk', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(50, 'Arimbi', 'Hana Supinah', 'Jl. Margosari Pekon Jatiagung', 'AMBARAWA', '085788829113', 'Makanan', NULL, NULL, 'PIRT', 'Aneka Kue kering', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(51, 'Qurota Ayun', 'Emilia Susanti', 'Sumber Agung', 'AMBARAWA', '085758030454', 'Kerajinan', NULL, NULL, NULL, 'Penjahit pakaian', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(52, 'Tas Tali Kur', 'Sofi Mutazatul M.', 'Ambarawa Timur', 'AMBARAWA', '085788846882', 'Kerajinan', NULL, 1, NULL, 'Kerajinan tangan dari tali kur', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(53, 'Menjual Hasil karya', 'Romiyah', 'Ambarawa Timur', 'AMBARAWA', '085664887071', 'Kerajinan', NULL, NULL, NULL, 'Kerajinan Tas dari Tali Kur', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(54, 'Kain Perca', 'Subaiti', 'Sumberagung', 'AMBARAWA', '082373221859', 'Kerajinan', NULL, NULL, NULL, 'Penjahit Kain Perca', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(55, 'Penjahit', 'Salimah Ermawati', 'Tanjunganom', 'AMBARAWA', '085379688152', 'Kerajinan', NULL, NULL, NULL, 'Penjahit pakaian', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(56, 'Penjahit', 'Marwiyah', 'Tanjung Gunung Pekon Tanjunganom', 'AMBARAWA', '085379252256', 'Kerajinan', NULL, NULL, NULL, 'Penjahit pakaian', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(57, 'Penjahit', 'Musnaeni', 'Tanjunganom', 'AMBARAWA', '083130914922', 'Kerajinan', NULL, NULL, NULL, 'Penjahit pakaian', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(58, 'Ida Collection', 'Idah Isti Adah Eni Eliyas', 'Tanjunganom', 'AMBARAWA', '085267461202', 'Kerajinan', NULL, NULL, 'SKU', 'Kerajinan Pita Kain', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(59, 'Kerajinan Tapis', 'Ratna Aini', 'SumberJaya ', 'AMBARAWA', '082177741021', 'Kerajinan', NULL, 2, NULL, 'Kerajinan Tapis', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(60, 'JBL Herbalic', 'Irina Milda Candrasari', 'Jl. Ahmad Gardi No. 27 Ambarawa', 'AMBARAWA', '081379342898', 'Minuman', NULL, 2, 'NIB, PIRT', 'Minuman Herbal Kesehatan', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(61, 'Mak Ati Lampung', 'Suwarti', 'Ambarawa Barat Rt.001 Rw.001', 'AMBARAWA', '0895389612248', 'Makanan', NULL, 3, 'PIRT', 'Kopi Bubuk, Jahe Instan, Tepung Mocaf, Aneka Keripik, Kue Kering', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(62, 'Sekar Tanjung', 'Sunarti', 'Pekon Ambarawa Rt.06 Rw.03', 'AMBARAWA', '081328055233', 'Makanan', NULL, 3, 'SKU, PIRT', 'Manggleng, oyek, Tepung Mocaf, Kopi Bubuk', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(63, 'NauNau Snack', 'Mustajab', 'Ambarawa', 'AMBARAWA', '082183095566', 'Makanan', NULL, 2, NULL, 'Keripik Pisang Coklat, Basreng', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(64, 'CV. LEZATKU FOOD', 'HM. Sunarto', 'Jl. HM Ghardi RT 03 RW 03 Ambarawa', 'AMBARAWA', '081379342945', 'Makanan', NULL, NULL, 'Izin Edar, Halal. BPOM', 'Industri berbasis daging lumatan, Pengolahan dan Pengawetan Produk daging, daging unggas dan Ikan', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(65, 'Qinna Craft', 'Rudi Yulianto / Rizky Permata Sari', 'Ambarawa Barat', 'AMBARAWA', '082281720999', 'Kerajinan', '50 pot/bln', NULL, 'SKU', 'Bunga Stoking', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(66, 'Aa Craft By Dyata Flanel', 'Tuwi Suharti', 'Sumberdadi, Margodadi', 'AMBARAWA', '081274714210', 'Kerajinan', '50 pcs / bln', 1, 'NIB', 'Kerajinan Tangan dari Kain Flanel', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(67, 'Jajanan Pasar', 'Eka Yulianti', 'Ambarawa Barat', 'AMBARAWA', '085769969289', 'Makanan', '200 kg/Bln', 2, NULL, 'Aneka Kue basah', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(68, 'Aneka Rempeyek', 'Sugiarti', 'Ambarawa', 'AMBARAWA', '082177770884', 'Makanan', NULL, NULL, 'NIB', 'Aneka Rempeyek', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(69, 'Sewu Kopi dan Sewu Sirup', 'Angga Dwianto', 'Ambarawa Barat', 'AMBARAWA', '082186165905', 'Makanan', NULL, NULL, NULL, 'Pengolahan Kopi dan Minuman Sirup', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(70, 'Opak Seledri', 'Fajar Sidq', 'Tanjunganom', 'AMBARAWA', NULL, 'Makanan', NULL, NULL, NULL, 'Opak Seledri', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(71, 'Al-Mahera', 'Dewi Wasiyam', 'Sumber Agung', 'AMBARAWA', '085208578872', 'Makanan', NULL, NULL, NULL, 'Bakso Ikan Lele, Kopi Bubuk Asli dan Rasa Jahe, ', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(72, 'Emping Danisa', 'Ismawati', 'Ambarawa', 'AMBARAWA', '082376882076', 'Makanan', NULL, NULL, NULL, 'Emping', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(73, 'Kedai Apersa', 'Endo Apersa', 'Sumber Agung', 'AMBARAWA', '085838788563', 'Makanan', NULL, NULL, NULL, 'Bakso Selimut', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(74, 'Dapur Aura', 'Fitri Sugesti', 'Sumber Agung', 'AMBARAWA', '\'082282465611', 'Makanan', NULL, NULL, 'PIRT', 'Kue Kering', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(75, 'Citra Rasa Nasi Box & Kue', 'Sriyatmi', 'Sumberagung', 'AMBARAWA', '085268772924', 'Makanan', NULL, NULL, NULL, 'Catering & aneka kue', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(76, 'Telor Asin Assyifa Tiyas', 'Okta Yurika Sari', 'Ambarawa Rt.002 Rw.002', 'AMBARAWA', NULL, 'Makanan', NULL, NULL, NULL, 'Telor Asin', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(77, 'Azis sinta Bakery', 'Sri Yanti', 'Ambarawa', 'AMBARAWA', '081273522937', 'Makanan', NULL, 2, 'NIB, PIRT', 'Aneka Cake & Cookies', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(78, 'Dapur Liwet', 'Siti Khotimah', 'Kresnomulyo Rt.001', 'AMBARAWA', '081274884928', 'Makanan', NULL, 4, NULL, 'Kue Kering , Catering', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(79, 'Mami Kenzoe', 'Sumi Harti', 'Pekon Sumber Agung', 'AMBARAWA', '081368443431', 'Makanan', '10 kg/Bln', 1, 'Halal', 'Kue Kering & Kue Basah', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(80, 'Kripik Aruna', 'Indah Tri Pertiwi', 'Ambarawa Timur', 'AMBARAWA', '085669618000', 'Makanan', '5 ton/bln', 14, 'NIB, PIRT', 'Keripik mantang ungu', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(81, 'Anyaman Tas Tali Jally', 'Ngadinem', 'Ambarawa Timur', 'AMBARAWA', '085839966082', 'Kerajinan', NULL, NULL, NULL, 'Tas dari tali Jally', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(82, 'Kerajinan Manik-manik', 'Marwati', 'Ambarawa Timur', 'AMBARAWA', '085369064111', 'Kerajinan', NULL, NULL, NULL, 'kotak tissu ', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(83, 'Assyfa', 'Imawati', 'Jati Agung', 'AMBARAWA', '085783148300', 'Makanan', NULL, NULL, NULL, 'Aneka Keripik', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(84, 'Cha-Cha Bella', 'Dewi Aminah', 'Ambarawa Barat', 'AMBARAWA', '085766901988', NULL, NULL, NULL, NULL, NULL, '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(85, 'Aneka Kue ', 'Yusra Winarni', 'Ambarawa Barat', 'AMBARAWA', '085156695952', NULL, NULL, NULL, NULL, 'Aneka Kue', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(86, 'Wilujeng Kopi', 'Wilujeng Khoirul', 'Sumberagung', 'AMBARAWA', '082280565920', 'Makanan', NULL, NULL, NULL, 'Kopi Bubuk', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(87, 'Dapoer Hanny', 'Hanny Teta Rizza', 'Ambarawa', 'AMBARAWA', '082182864859', 'Makanan', NULL, NULL, NULL, 'Aneka Kue Basah', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(88, 'Cahaya Karya Pilar dan Kubah Pringsewu', 'Khafid Wahyu Hidayat', 'Ambarawa Barat', 'AMBARAWA', '0895333718315', 'Jasa', NULL, NULL, NULL, 'Pembuatan pilar & kubah', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(89, 'KEDAI JUS DAN JAJANAN SOMAY', 'MUHAMMAD SYAHRONNY', 'Ambarawa', 'AMBARAWA', '085280019509', 'Makanan', NULL, 4, 'NIB, Halal', 'JUS DAN SOMAY', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(90, 'CV. LIMBAH JAYA', 'Sita Astri Ambarupi', 'Jl. Pajajaran RT/RW 09/02 Sukamulya', 'BANYUMAS', '081272806060', 'Kerajinan', NULL, NULL, NULL, 'Sprei, Sarung Bantal, Sarung Kasur, Keset', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(91, 'PK TRIJAYA', 'Nova Kurohman', 'Sukamulya  kec. Banyumas ', 'BANYUMAS', '085369631162', 'Kerajinan', NULL, NULL, NULL, 'Sprei, Sarung Bantal, Sarung Kasur, Keset', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(92, 'KARYA PERCA', 'Hendro Siswo Jatmiko', 'Sukamulya RT/RW  011/003  ', 'BANYUMAS', '082175450936', 'Kerajinan', NULL, NULL, NULL, 'Sprei, Sarung Bantal, Sarung Kasur, Keset', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(93, 'Telur Gabus Tri Makmur', 'Nurhayati.', 'Jl. Kecamatan RT/RW 015/005 Banyumas ', 'BANYUMAS', '081379093989', 'Makanan ', NULL, NULL, 'PIRT, TDI', 'Telur Gabus', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(94, 'Keripik Pisang', 'M. Yusuf', 'Pekon Banyuwangi', 'BANYUMAS', '085357398952', 'Makanan', NULL, NULL, NULL, 'Keripik Pisang', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(95, 'Gula Aren ', 'Marlan', 'Pekon Banjarejo', 'BANYUMAS', '081379710200', 'Makanan', NULL, NULL, NULL, 'Gula Aren', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(96, 'Gula Aren', 'S. Damasus', 'Pekon Banjarejo', 'BANYUMAS', '085379762283', 'Makanan', NULL, NULL, NULL, 'Gula Aren', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(97, 'Keripik Wahyu', 'Nurwahyudi', 'Nusawungu Rt/Rw 01/02 ', 'BANYUMAS', '085789673571', 'Makanan', NULL, 4, 'NIB, PIRT, HALAL', 'Keripik Pisang', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(98, 'PP Bagus Pandawa III', 'Supardi', 'Sinar Ukir, Banjarejo', 'BANYUMAS', '081272056648', 'Makanan', NULL, NULL, 'TDI', 'Industri Penggilingan Padi ', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(99, 'PP Bagus Pandawa ', 'Supardi', 'Pekon Banyumas', 'BANYUMAS', '\'081272056648', 'Makanan', NULL, NULL, 'TDI', 'Industri Penggilingan Padi ', '2025-10-15 22:11:10', '2025-10-15 22:11:10'),
(100, 'Mysha Roti', 'Heriyanto', 'Pagersari RT.015 Rw.005 Pekon Banyumas', 'BANYUMAS', '085381140250', 'Makanan', NULL, NULL, 'TDI', 'Industri Roti', '2025-10-15 22:11:10', '2025-10-15 22:11:10');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kemiskinan`
--

CREATE TABLE `kemiskinan` (
  `id` int(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `kecamatan` varchar(100) DEFAULT NULL,
  `persentase` decimal(5,2) NOT NULL,
  `sumber` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kemiskinan`
--

INSERT INTO `kemiskinan` (`id`, `tahun`, `kecamatan`, `persentase`, `sumber`, `created_at`) VALUES
(1, 2023, 'Pringsewu', 12.45, 'BPS', '2025-12-12 09:19:33'),
(2, 2023, 'Gading Rejo', 10.32, 'BPS', '2025-12-12 09:19:33'),
(3, 2023, 'Pagelaran', 14.10, 'Dinas Sosial', '2025-12-12 09:19:33'),
(4, 2024, 'Pringsewu', 11.80, 'BPS', '2025-12-12 09:19:33');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('JqILKaI0B5pDR7X3ll3Lad4V7QUrBAE5U2qqxcw1', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiV1hlQzV5SDI4SlhLbjZQNElmMW83WnNrUzJPSXN1bU9DNGxFQ2FTbyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czoxMDoiY2hhdC5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NzoiY29udl9pZCI7czozNjoiMTMyZDcwNDMtMGEwZC00MWMwLTk2MTItM2VmN2QwYmZlZTk4Ijt9', 1767534424);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anak_berhenti_sekolah`
--
ALTER TABLE `anak_berhenti_sekolah`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tahun` (`tahun`),
  ADD KEY `jenjang` (`jenjang`),
  ADD KEY `kecamatan` (`kecamatan`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kemiskinan`
--
ALTER TABLE `kemiskinan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tahun` (`tahun`),
  ADD KEY `kecamatan` (`kecamatan`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anak_berhenti_sekolah`
--
ALTER TABLE `anak_berhenti_sekolah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kemiskinan`
--
ALTER TABLE `kemiskinan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
