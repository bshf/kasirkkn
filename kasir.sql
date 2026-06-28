-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 28 Jun 2026 pada 19.23
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kasir`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `menu`
--

CREATE TABLE `menu` (
  `id` int(11) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `kategori` enum('makanan','minuman') NOT NULL DEFAULT 'makanan',
  `image_url` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `menu`
--

INSERT INTO `menu` (`id`, `nama`, `harga`, `kategori`, `image_url`, `created_at`, `updated_at`) VALUES
(1, 'dimsum ayam 5 pcs', 10000.00, 'makanan', '1782663401_8f81495458b0fc048f00.jpg', '2026-05-24 22:04:57', '2026-06-28 23:16:41'),
(2, 'DIMSUM AYAM 15 PCS', 28000.00, 'makanan', '1782663689_468fdfcd3db788b45fb7.jpg', '2026-06-04 12:14:31', '2026-06-28 23:21:29'),
(3, 'Dimsum goreng keju 3pcs', 10000.00, 'makanan', '1782663868_46bf849f25789c8b1a70.jpg', '2026-06-04 12:15:44', '2026-06-28 23:24:28'),
(4, 'Dimsum mentai 4pcs', 10000.00, 'makanan', '1782663915_325779eeb62cfa5ff160.jpg', '2026-06-04 12:22:03', '2026-06-28 23:25:15'),
(5, 'Dimsum mentai 12pcs', 33000.00, 'makanan', '1782663909_18405ce1a54754a5b340.jpg', '2026-06-04 12:25:24', '2026-06-28 23:25:09'),
(7, 'Mie barbar level 1', 10000.00, 'makanan', '1782663940_df021c7d17c94920e2a7.jpg', '2026-06-28 23:25:40', '2026-06-28 23:25:40'),
(8, 'Mie barbar level 2', 10000.00, 'makanan', '1782663953_12211756d9d6dc2d3813.jpg', '2026-06-28 23:25:53', '2026-06-28 23:25:53'),
(9, 'Mie barbar level 3', 10000.00, 'makanan', '1782663961_9dc90f49e49b0352f385.jpg', '2026-06-28 23:26:01', '2026-06-28 23:26:01'),
(10, 'Mie barbar level 5', 12000.00, 'makanan', '1782663981_f9ef0ac3b1ee5afb8c17.jpg', '2026-06-28 23:26:21', '2026-06-28 23:29:29'),
(11, 'Mie barbar level 6', 12000.00, 'makanan', '1782663990_4b6636c80c294b4dea08.jpg', '2026-06-28 23:26:30', '2026-06-28 23:29:34'),
(12, 'Mie barbar level 7', 12000.00, 'makanan', '1782663998_118c3ff127799d92865a.jpg', '2026-06-28 23:26:38', '2026-06-28 23:29:39'),
(13, 'Mie barbar level 8', 12000.00, 'makanan', '1782664008_99c9169fae9bb107c6cd.jpg', '2026-06-28 23:26:48', '2026-06-28 23:29:45'),
(14, 'Wonton specy level 1', 15000.00, 'makanan', '1782664034_c092b89163a160967766.jpg', '2026-06-28 23:27:14', '2026-06-28 23:27:14'),
(15, 'Wonton specy level 2', 15000.00, 'makanan', '1782664045_01274748859475906c78.jpg', '2026-06-28 23:27:25', '2026-06-28 23:27:25'),
(16, 'Wonton specy level 3', 15000.00, 'makanan', '1782664059_08429d3718849cfdfaec.jpg', '2026-06-28 23:27:39', '2026-06-28 23:27:39'),
(17, 'Cireng ayam', 1000.00, 'makanan', '1782664071_6037c366cc671e6b9790.jpg', '2026-06-28 23:27:51', '2026-06-28 23:27:51'),
(18, 'Risol ayam', 1000.00, 'makanan', '1782664083_99665aee22c966a3fab1.jpg', '2026-06-28 23:28:03', '2026-06-28 23:28:03'),
(19, 'Risol mayo', 1000.00, 'makanan', '1782664094_f5526647fc4527f893e0.jpg', '2026-06-28 23:28:14', '2026-06-28 23:28:14'),
(20, 'Samosa telur', 1000.00, 'makanan', '1782664110_122c7a4ad75cfbebdec0.jpg', '2026-06-28 23:28:30', '2026-06-28 23:28:30'),
(21, 'Tela-tela singkong', 5000.00, 'makanan', '1782664122_c6a113ac14b38b69ffdb.jpg', '2026-06-28 23:28:42', '2026-06-28 23:28:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2026-05-24-144039', 'App\\Database\\Migrations\\CreateMenuTable', 'default', 'App', 1779633706, 1),
(2, '2026-05-24-144041', 'App\\Database\\Migrations\\CreateMenuTable', 'default', 'App', 1779633875, 2),
(3, '2026-06-04-091917', 'App\\Database\\Migrations\\CreateTransaksiTable', 'default', 'App', 1780564911, 3),
(4, '2026-06-04-091929', 'App\\Database\\Migrations\\CreateTransaksiDetailTable', 'default', 'App', 1780564911, 3),
(5, '2026-06-11-044744', 'App\\Database\\Migrations\\CreateUsersTable', 'default', 'App', 1782480355, 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `level` int(2) DEFAULT NULL,
  `payment_via` varchar(50) NOT NULL,
  `total` float UNSIGNED NOT NULL DEFAULT 0,
  `bayar` float NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id`, `tanggal`, `nama`, `level`, `payment_via`, `total`, `bayar`, `created_at`, `updated_at`) VALUES
(3, '2026-06-26', 'Customer', NULL, 'CASH', 12000, 15000, '2026-06-26 21:59:52', '2026-06-26 21:59:52'),
(4, '2026-06-26', 'Customer', NULL, 'CASH', 12000, 20000, '2026-06-26 22:42:24', '2026-06-26 22:42:24'),
(5, '2026-06-26', 'cust1', NULL, 'CASH', 12000, 15000, '2026-06-26 22:45:55', '2026-06-26 22:45:55'),
(6, '2026-06-26', 'c', NULL, 'CASH', 6000, 10000, '2026-06-26 22:47:02', '2026-06-26 22:47:02'),
(7, '2026-06-26', 'Cust2', NULL, 'CASH', 10000, 10000, '2026-06-26 23:07:48', '2026-06-26 23:07:48'),
(8, '2026-06-28', 'CUST 1', NULL, 'CASH', 48000, 50000, '2026-06-28 23:30:17', '2026-06-28 23:30:17'),
(9, '2026-06-28', '2', NULL, 'CASH', 48000, 50000, '2026-06-28 23:32:39', '2026-06-28 23:32:39'),
(10, '2026-06-28', '3', NULL, 'CASH', 10000, 10000, '2026-06-28 23:33:50', '2026-06-28 23:33:50'),
(12, '2026-06-29', '1', NULL, 'CASH', 53000, 55000, '2026-06-29 00:06:03', '2026-06-29 00:06:03'),
(13, '2026-06-29', '2', NULL, 'CASH', 15000, 15000, '2026-06-29 00:06:27', '2026-06-29 00:06:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi_detail`
--

CREATE TABLE `transaksi_detail` (
  `id` int(11) UNSIGNED NOT NULL,
  `transaksi_id` int(11) UNSIGNED NOT NULL,
  `menu_id` int(11) UNSIGNED NOT NULL,
  `qty` int(5) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi_detail`
--

INSERT INTO `transaksi_detail` (`id`, `transaksi_id`, `menu_id`, `qty`) VALUES
(13, 3, 1, 1),
(14, 3, 2, 1),
(15, 3, 3, 1),
(16, 3, 4, 1),
(17, 3, 5, 1),
(18, 4, 4, 1),
(19, 4, 3, 3),
(20, 4, 2, 1),
(21, 5, 4, 1),
(22, 5, 3, 3),
(23, 5, 2, 1),
(24, 6, 3, 1),
(25, 6, 2, 1),
(26, 7, 1, 1),
(27, 7, 2, 1),
(28, 7, 4, 1),
(29, 7, 3, 1),
(30, 8, 2, 1),
(31, 8, 3, 1),
(32, 8, 4, 1),
(33, 9, 2, 1),
(34, 9, 3, 1),
(35, 9, 9, 1),
(36, 10, 9, 1),
(39, 12, 4, 2),
(40, 12, 5, 1),
(41, 13, 15, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` char(32) NOT NULL COMMENT 'MD5 hash',
  `role` enum('admin','kasir') NOT NULL DEFAULT 'kasir',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin', 'admin@example.com', '0192023a7bbd73250516f069df18b500', 'admin', '2026-06-26 20:47:45', '2026-06-26 20:47:45');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaksi_detail_transaksi_id_foreign` (`transaksi_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD CONSTRAINT `transaksi_detail_transaksi_id_foreign` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
