-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 06, 2026 at 08:27 AM
-- Server version: 11.4.10-MariaDB-cll-lve
-- PHP Version: 8.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `redp7931_fintrack_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `family_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'Lainnya',
  `initial_value` decimal(15,2) NOT NULL,
  `purchase_date` date NOT NULL,
  `depreciation_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`id`, `family_id`, `name`, `type`, `initial_value`, `purchase_date`, `depreciation_rate`, `created_at`, `updated_at`) VALUES
(1, 1, 'Honda Beat Yudha', 'Kendaraan', 18000000.00, '2015-12-12', 10.00, '2026-03-22 23:20:11', '2026-03-22 23:20:11'),
(2, 1, 'Honda Vario Mamas', 'Kendaraan', 4500000.00, '2025-12-12', 10.00, '2026-03-22 23:20:44', '2026-03-22 23:20:44');

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `family_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `limit_amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `budgets`
--

INSERT INTO `budgets` (`id`, `family_id`, `category_id`, `limit_amount`, `created_at`, `updated_at`) VALUES
(1, 1, 6, 600000.00, '2026-03-19 23:44:24', '2026-03-19 23:44:24'),
(2, 1, 7, 130000.00, '2026-03-19 23:44:38', '2026-03-19 23:44:38'),
(3, 1, 9, 100000.00, '2026-03-19 23:44:48', '2026-03-19 23:44:48'),
(4, 1, 10, 250000.00, '2026-03-19 23:45:12', '2026-03-19 23:45:12'),
(5, 1, 11, 150000.00, '2026-03-19 23:45:29', '2026-03-19 23:45:29'),
(6, 1, 12, 70000.00, '2026-03-19 23:45:45', '2026-03-19 23:45:45'),
(7, 1, 13, 50000.00, '2026-03-19 23:46:15', '2026-03-19 23:46:15'),
(8, 1, 8, 100000.00, '2026-03-19 23:46:28', '2026-03-19 23:46:58'),
(9, 1, 21, 100000.00, '2026-03-19 23:48:05', '2026-03-19 23:48:05'),
(10, 1, 14, 100000.00, '2026-03-19 23:48:15', '2026-03-19 23:48:15'),
(11, 1, 15, 150000.00, '2026-03-19 23:48:44', '2026-03-19 23:48:44'),
(12, 1, 22, 200000.00, '2026-03-19 23:49:11', '2026-03-19 23:49:11'),
(13, 1, 23, 10000.00, '2026-03-20 00:13:07', '2026-03-20 00:13:07');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `family_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `color` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `family_id`, `name`, `type`, `color`, `created_at`, `updated_at`) VALUES
(6, 1, 'Belanja Bulanan', 'Expense', 'bg-rose-500', '2026-03-19 23:32:09', '2026-03-19 23:34:18'),
(7, 1, 'Paket Data Yudha', 'Expense', 'bg-orange-500', '2026-03-19 23:33:16', '2026-03-19 23:34:45'),
(8, 1, 'Service Vario', 'Expense', 'bg-rose-500', '2026-03-19 23:34:07', '2026-03-19 23:34:24'),
(9, 1, 'Paket Data Mamas', 'Expense', 'bg-orange-500', '2026-03-19 23:34:54', '2026-03-19 23:34:54'),
(10, 1, 'Bensin Mamas', 'Expense', 'bg-amber-500', '2026-03-19 23:35:07', '2026-03-19 23:35:07'),
(11, 1, 'Bensin Yudha', 'Expense', 'bg-amber-500', '2026-03-19 23:35:13', '2026-03-19 23:35:20'),
(12, 1, 'Listrik', 'Expense', 'bg-amber-500', '2026-03-19 23:35:43', '2026-03-19 23:35:43'),
(13, 1, 'Dana Sosial', 'Expense', 'bg-emerald-500', '2026-03-19 23:36:05', '2026-03-19 23:36:05'),
(14, 1, 'Skincare', 'Expense', 'bg-emerald-500', '2026-03-19 23:36:24', '2026-03-19 23:36:24'),
(15, 1, 'Suryaa', 'Expense', 'bg-slate-500', '2026-03-19 23:36:32', '2026-03-19 23:36:32'),
(16, 1, 'Gaji Mamas', 'Income', 'bg-rose-500', '2026-03-19 23:37:26', '2026-03-19 23:37:26'),
(17, 1, 'Gaji Yudha', 'Income', 'bg-rose-500', '2026-03-19 23:37:36', '2026-03-19 23:37:36'),
(18, 1, 'Usaha Mamas', 'Income', 'bg-orange-500', '2026-03-19 23:37:43', '2026-03-19 23:37:43'),
(19, 1, 'TPG', 'Income', 'bg-orange-500', '2026-03-19 23:37:53', '2026-03-19 23:37:53'),
(20, 1, 'Les', 'Income', 'bg-orange-500', '2026-03-19 23:37:59', '2026-03-19 23:37:59'),
(21, 1, 'Service Beat', 'Expense', 'bg-rose-500', '2026-03-19 23:47:32', '2026-03-19 23:47:32'),
(22, 1, 'Dana Darurat', 'Expense', 'bg-rose-500', '2026-03-19 23:47:45', '2026-03-19 23:47:45'),
(23, 1, 'Kakung Daily', 'Expense', 'bg-orange-500', '2026-03-20 00:12:38', '2026-03-20 00:12:38'),
(24, 1, 'Akun Godev', 'Expense', 'bg-fuchsia-500', '2026-03-20 00:18:57', '2026-03-20 00:19:20'),
(25, 1, 'Desain grafis', 'Expense', 'bg-fuchsia-500', '2026-03-20 00:19:15', '2026-03-20 00:19:15'),
(26, 1, 'Sablon & kaos', 'Expense', 'bg-fuchsia-500', '2026-03-20 00:20:03', '2026-03-20 00:20:03'),
(27, 1, 'Pakan kucing', 'Expense', 'bg-amber-500', '2026-03-20 00:25:06', '2026-03-20 00:25:06'),
(28, 1, 'Lain lain', 'Expense', 'bg-slate-500', '2026-03-20 06:54:55', '2026-03-20 06:54:55'),
(29, 1, 'Obat', 'Expense', 'bg-orange-500', '2026-03-20 08:59:17', '2026-03-20 08:59:17'),
(30, 1, 'Lain lain', 'Income', 'bg-slate-500', '2026-03-20 10:08:31', '2026-03-20 10:08:31'),
(31, 1, 'Makan', 'Expense', 'bg-rose-500', '2026-03-20 18:06:35', '2026-03-20 18:06:35'),
(34, 1, 'Pembayaran Hutang', 'Expense', 'bg-slate-500', '2026-04-01 06:23:44', '2026-04-01 06:28:46'),
(35, 1, 'BPJS Keluarga', 'Expense', 'bg-indigo-500', '2026-04-01 06:28:38', '2026-04-01 06:34:52'),
(36, 1, 'BPJS Kakung', 'Expense', 'bg-slate-500', '2026-04-01 06:35:01', '2026-04-01 06:35:01');

-- --------------------------------------------------------

--
-- Table structure for table `debts`
--

CREATE TABLE `debts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `family_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `counterparty` varchar(255) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `due_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'ongoing',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `debts`
--

INSERT INTO `debts` (`id`, `family_id`, `type`, `counterparty`, `total_amount`, `paid_amount`, `due_date`, `notes`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Hutang', 'Lek Y', 35400000.00, 1000000.00, '2028-01-01', '', 'ongoing', '2026-03-19 23:39:25', '2026-04-01 06:27:51'),
(2, 1, 'Hutang', 'Koperasi Beat', 4260000.00, 426000.00, '2027-01-01', '', 'ongoing', '2026-03-19 23:40:50', '2026-04-01 06:23:44'),
(3, 1, 'Hutang', 'Kakung', 3500000.00, 0.00, '2026-10-01', '', 'ongoing', '2026-03-19 23:41:41', '2026-03-19 23:41:41'),
(4, 1, 'Hutang', 'Mas Aji Solo', 150000.00, 0.00, '2026-04-02', '', 'ongoing', '2026-03-19 23:42:12', '2026-03-19 23:42:12'),
(5, 1, 'Hutang', 'Ibuk', 10000000.00, 0.00, '2026-12-31', '', 'ongoing', '2026-03-19 23:42:37', '2026-03-19 23:42:37'),
(6, 1, 'Hutang', 'Niaa', 2000000.00, 0.00, '2026-12-10', '', 'ongoing', '2026-03-19 23:43:28', '2026-03-19 23:43:28');

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
-- Table structure for table `families`
--

CREATE TABLE `families` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `invite_code` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `families`
--

INSERT INTO `families` (`id`, `name`, `invite_code`, `created_at`, `updated_at`) VALUES
(1, 'test', 'ZBC4SLUO', '2026-03-19 00:30:58', '2026-03-19 00:30:58');

-- --------------------------------------------------------

--
-- Table structure for table `goals`
--

CREATE TABLE `goals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `family_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `target_amount` decimal(15,2) NOT NULL,
  `current_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `target_date` date DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `goals`
--

INSERT INTO `goals` (`id`, `family_id`, `name`, `target_amount`, `current_amount`, `target_date`, `color`, `created_at`, `updated_at`) VALUES
(1, 1, 'Beli Laptop', 17000000.00, 0.00, '2026-12-01', 'from-rose-500 to-orange-500', '2026-03-19 23:50:45', '2026-03-19 23:50:45'),
(2, 1, 'Beliin Motor Mamas', 25000000.00, 0.00, '2028-04-14', 'from-rose-500 to-orange-500', '2026-03-19 23:51:54', '2026-03-19 23:51:54'),
(3, 1, 'Umroh', 70000000.00, 0.00, '2030-04-14', 'from-rose-500 to-orange-500', '2026-03-19 23:52:28', '2026-03-19 23:52:28'),
(4, 1, 'Renov Rumah', 50000000.00, 0.00, '2035-04-14', 'from-emerald-400 to-teal-500', '2026-03-19 23:53:02', '2026-03-19 23:53:02'),
(5, 1, 'Tablet / Handphone', 10000000.00, 0.00, '2026-12-01', 'from-purple-500 to-pink-500', '2026-03-20 16:03:48', '2026-03-20 16:03:48');

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
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_03_17_021907_create_families_table', 1),
(5, '2026_03_17_021908_add_family_id_to_users_table', 1),
(6, '2026_03_17_031005_create_categories_table', 1),
(7, '2026_03_17_031005_create_wallets_table', 1),
(8, '2026_03_17_031006_create_assets_table', 1),
(9, '2026_03_17_031006_create_transactions_table', 1),
(10, '2026_03_17_031007_create_budgets_table', 1),
(11, '2026_03_17_031007_create_debts_table', 1),
(12, '2026_03_17_031007_create_goals_table', 1),
(13, '2026_03_17_031812_add_balance_to_wallets_table', 1),
(14, '2026_03_17_032655_add_type_to_assets_table', 1),
(15, '2026_03_17_035637_create_push_subscriptions_table', 1),
(16, '2026_03_18_164014_create_notifications_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `push_subscriptions`
--

CREATE TABLE `push_subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscribable_type` varchar(255) NOT NULL,
  `subscribable_id` bigint(20) UNSIGNED NOT NULL,
  `endpoint` varchar(500) NOT NULL,
  `public_key` varchar(255) DEFAULT NULL,
  `auth_token` varchar(255) DEFAULT NULL,
  `content_encoding` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `push_subscriptions`
--

INSERT INTO `push_subscriptions` (`id`, `subscribable_type`, `subscribable_id`, `endpoint`, `public_key`, `auth_token`, `content_encoding`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'https://fcm.googleapis.com/fcm/send/dYUxt1Xikfg:APA91bF_kuvpACW4layi30ogEIJ27QltJTLJChAK7iHZtzJY8dxnszlmLBXyv3y5Z6MWQ2-wHmlrehe4bDnT4JWkOx114ojCt_OLhXyBMExaagctAxagrDYBJT3XIbLEdiUELTCT3MWV', 'BPMBKbsjhgkCPn6pBdp65XbBf5JNeoTgUEJDElo9RJYbnybDY_CbM9dfQNaeJDnXAqjb3BTpSj79ufj_4PbhHhU', '9BGuin5DaTN61g9NyMR5uA', NULL, '2026-03-19 23:49:18', '2026-03-19 23:49:18');

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
('mj84HN0KU3ZA8jcZ3xnViEKRVGjxmjICbwZ6TSla', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNDF6WFh0YUhVUzNrd2g2YlI2SzNaN2VaU24zVEl3c1JxeHRZN09MdiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MjtzOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozNDoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3RyYW5zYWN0aW9ucyI7czo1OiJyb3V0ZSI7czoxMjoidHJhbnNhY3Rpb25zIjt9fQ==', 1773906205);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `family_id` bigint(20) UNSIGNED NOT NULL,
  `wallet_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `family_id`, `wallet_id`, `category_id`, `user_id`, `type`, `amount`, `date`, `notes`, `created_at`, `updated_at`) VALUES
(48, 1, 4, 16, 1, 'Income', 1148000.00, '2026-04-01 13:22:00', 'Gaji BMM maret', '2026-04-01 06:22:49', '2026-04-01 06:23:05'),
(49, 1, 4, 34, 1, 'Expense', 426000.00, '2026-04-01 06:23:44', 'Cicilan Hutang: Koperasi Beat', '2026-04-01 06:23:44', '2026-04-01 06:23:44'),
(50, 1, 13, 17, 1, 'Income', 1302000.00, '2026-04-01 06:24:00', 'Gaji guru maret', '2026-04-01 06:25:15', '2026-04-01 06:25:15'),
(51, 1, 13, 34, 1, 'Expense', 1000000.00, '2026-04-01 06:27:51', 'Cicilan Hutang: Lek Y', '2026-04-01 06:27:51', '2026-04-01 06:27:51'),
(52, 1, 13, 35, 1, 'Expense', 126000.00, '2026-04-01 06:35:00', 'BPJS Maret', '2026-04-01 06:35:19', '2026-04-01 06:35:19'),
(53, 1, 13, 28, 1, 'Expense', 50000.00, '2026-04-01 06:35:00', 'Ganti uang outing class', '2026-04-01 06:36:46', '2026-04-01 06:36:46'),
(54, 1, 13, 28, 1, 'Expense', 100000.00, '2026-04-01 06:36:00', 'titipan bayaran outing class', '2026-04-01 06:37:17', '2026-04-01 06:37:17'),
(55, 1, 13, 30, 1, 'Income', 126000.00, '2026-04-01 06:37:00', 'Honor tambahan', '2026-04-01 06:38:26', '2026-04-01 06:38:26'),
(56, 1, 13, 29, 1, 'Expense', 25000.00, '2026-04-01 06:38:00', 'fresh care + tolak angin', '2026-04-01 06:38:46', '2026-04-01 06:38:46'),
(57, 1, 13, 31, 1, 'Expense', 5000.00, '2026-04-01 06:39:00', 'beli gorengan', '2026-04-01 06:39:14', '2026-04-01 06:39:14'),
(58, 1, 12, 30, 1, 'Income', 9000.00, '2026-04-01 13:43:50', 'hibah', '2026-04-01 06:40:06', '2026-04-01 06:43:50'),
(59, 1, 5, 30, 1, 'Income', 600000.00, '2026-04-01 06:44:00', 'sisa saldo', '2026-04-01 06:45:05', '2026-04-01 06:45:05'),
(60, 1, 10, 30, 1, 'Income', 62868.00, '2026-04-01 06:45:00', 'sisa saldo', '2026-04-01 06:45:56', '2026-04-01 06:45:56'),
(61, 1, 6, 30, 1, 'Income', 61500.00, '2026-04-01 13:46:57', 'sisa saldo', '2026-04-01 06:46:22', '2026-04-01 06:46:57'),
(62, 1, 8, 30, 1, 'Income', 45000.00, '2026-04-01 06:47:00', 'sisa saldo', '2026-04-01 06:47:33', '2026-04-01 06:47:33'),
(63, 1, 13, 30, 2, 'Income', 50000.00, '2026-04-02 01:04:00', 'Sangu Guru Saloka', '2026-04-02 01:04:58', '2026-04-02 01:04:58'),
(64, 1, 13, 31, 2, 'Expense', 14000.00, '2026-04-02 01:05:00', 'Sarapan tenongan', '2026-04-02 01:05:24', '2026-04-02 01:05:24'),
(65, 1, 13, 31, 2, 'Expense', 50000.00, '2026-04-02 01:05:00', 'Jajan di Saloka 😁', '2026-04-02 01:06:16', '2026-04-02 01:06:16'),
(66, 1, 13, 28, 2, 'Expense', 30000.00, '2026-04-02 01:06:00', 'Beli sandal di Saloka', '2026-04-02 01:06:40', '2026-04-02 01:06:40'),
(67, 1, 13, 28, 2, 'Expense', 10000.00, '2026-04-02 01:11:00', 'Mantol saloka', '2026-04-02 01:11:18', '2026-04-02 01:11:18'),
(68, 1, 13, 31, 2, 'Expense', 22000.00, '2026-04-03 22:57:00', 'Nasi padang', '2026-04-03 22:57:40', '2026-04-03 22:57:40'),
(69, 1, 13, 31, 2, 'Expense', 5000.00, '2026-04-03 22:57:00', 'Es teh', '2026-04-03 22:58:06', '2026-04-03 22:58:06'),
(70, 1, 13, 30, 2, 'Income', 40000.00, '2026-04-03 22:58:00', 'Sangu pendamping', '2026-04-03 22:58:27', '2026-04-03 22:58:27'),
(71, 1, 8, 31, 1, 'Expense', 16000.00, '2026-04-03 22:58:00', 'Makan', '2026-04-03 22:58:43', '2026-04-03 22:58:43'),
(72, 1, 13, 31, 2, 'Expense', 20000.00, '2026-04-03 22:58:00', 'Jajan dicojri', '2026-04-03 22:58:55', '2026-04-03 22:58:55'),
(73, 1, 13, 31, 2, 'Expense', 20000.00, '2026-04-05 10:19:53', 'Jajan dicokro', '2026-04-03 22:58:55', '2026-04-05 03:19:53'),
(74, 1, 4, 16, 2, 'Income', 150000.00, '2026-04-03 22:59:00', 'Ambil cash', '2026-04-03 22:59:31', '2026-04-03 22:59:31'),
(75, 1, 13, 15, 1, 'Expense', 13000.00, '2026-04-03 23:00:00', 'Angker', '2026-04-03 23:00:46', '2026-04-03 23:00:46'),
(76, 1, 13, 28, 1, 'Expense', 20000.00, '2026-04-03 23:00:00', 'Beli Telur + mie', '2026-04-03 23:02:00', '2026-04-03 23:02:00'),
(79, 1, 13, 30, 2, 'Income', 150000.00, '2026-04-05 03:17:00', 'Ambil dari Bca mamas', '2026-04-05 03:18:25', '2026-04-05 03:18:25'),
(80, 1, 4, 31, 2, 'Expense', 150000.00, '2026-04-05 03:18:00', 'Makan', '2026-04-05 03:18:48', '2026-04-05 03:18:48'),
(81, 1, 4, 31, 2, 'Expense', 150000.00, '2026-04-05 03:27:00', 'Ambil cash', '2026-04-05 03:28:54', '2026-04-05 03:28:54'),
(82, 1, 4, 31, 2, 'Expense', 150000.00, '2026-04-05 03:29:00', 'Makan', '2026-04-05 03:31:14', '2026-04-05 03:31:14'),
(83, 1, 13, 10, 2, 'Expense', 50000.00, '2026-04-05 03:34:00', 'Bensin mamas', '2026-04-05 03:34:27', '2026-04-05 03:34:27'),
(84, 1, 13, 28, 2, 'Expense', 39000.00, '2026-04-05 03:38:00', 'Harian', '2026-04-05 03:38:52', '2026-04-05 03:38:52');

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
  `updated_at` timestamp NULL DEFAULT NULL,
  `family_id` bigint(20) UNSIGNED DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `family_id`, `avatar`) VALUES
(1, 'Muhammad Afrizal', 'kaeopo80@gmail.com', NULL, '$2y$12$0NLcZ.Idfq/e3rpmYn9cbuX5LEcjc8VXouYXkGiiSMW1E3txHe1eC', 'kLmd9v7OKSLsCuEYIgX7UOP9WaywFGdle303Xaix3tc8BXiAvzA61NF5FSB9', '2026-03-19 00:30:44', '2026-03-19 00:30:58', 1, NULL),
(2, 'Rosyitania Yudha Astari', 'rosyitania@gmail.com', NULL, '$2y$12$LGaNf5GPXmiZL6E2dB/ZPOQ0t9piM.cwNA/NcBujz70bBLqA5bsya', 'q7Anzb231qy2CTBswU2Llf8H73tYhM1Eflr3kzldhGANHOvwAI4XfMvhatJF', '2026-03-19 00:33:53', '2026-03-19 00:34:00', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `family_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `starting_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `color` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id`, `family_id`, `name`, `account_number`, `type`, `starting_balance`, `balance`, `color`, `created_at`, `updated_at`) VALUES
(4, 1, 'BCA Mamas', '8265541191', 'Bank', 0.00, 422000.00, 'bg-blue-600', '2026-03-19 23:54:24', '2026-04-05 03:31:14'),
(5, 1, 'BCA Yudha', '0480024777', 'Bank', 0.00, 600000.00, 'bg-blue-600', '2026-03-19 23:55:14', '2026-04-01 06:45:05'),
(6, 1, 'Mandiri TPG', '1380028298094', 'Bank', 0.00, 61500.00, 'bg-blue-600', '2026-03-19 23:55:51', '2026-04-01 06:46:57'),
(7, 1, 'Dana Mamas', '0895422964539', 'E-Wallet', 0.00, 0.00, 'bg-emerald-500', '2026-03-19 23:56:20', '2026-03-19 23:56:20'),
(8, 1, 'Gopay Mamas', '0895422964539', 'E-Wallet', 0.00, 29000.00, 'bg-emerald-500', '2026-03-19 23:56:49', '2026-04-03 22:58:43'),
(9, 1, 'Dana Yudha', '081359699611', 'E-Wallet', 0.00, 0.00, 'bg-emerald-500', '2026-03-19 23:57:28', '2026-03-19 23:57:28'),
(10, 1, 'Seabank Yudha', '901555080750', 'E-Wallet', 0.00, 62868.00, 'bg-emerald-500', '2026-03-19 23:58:27', '2026-04-01 06:45:56'),
(11, 1, 'BNI Mamas', '1951748559', 'Bank', 0.00, 0.00, 'bg-blue-600', '2026-03-19 23:59:20', '2026-03-19 23:59:20'),
(12, 1, 'Cash Mamas', NULL, 'Tunai', 0.00, 9000.00, 'bg-yellow-500', '2026-03-20 00:00:41', '2026-04-01 06:43:50'),
(13, 1, 'Cash Yudha', NULL, 'Tunai', 0.00, 69000.00, 'bg-yellow-500', '2026-03-20 00:01:01', '2026-04-05 03:38:52'),
(14, 1, 'BCA SYARIAH MAMAS', '0480026228', 'Bank', 75000.00, 75000.00, 'bg-blue-600', '2026-04-01 06:41:20', '2026-04-01 06:41:20'),
(15, 1, 'BRI MAMAS', '051101071122503', 'Bank', 24000.00, 24000.00, 'bg-blue-600', '2026-04-01 06:48:47', '2026-04-01 06:48:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assets_family_id_foreign` (`family_id`);

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `budgets_family_id_foreign` (`family_id`),
  ADD KEY `budgets_category_id_foreign` (`category_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_family_id_foreign` (`family_id`);

--
-- Indexes for table `debts`
--
ALTER TABLE `debts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `debts_family_id_foreign` (`family_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `families`
--
ALTER TABLE `families`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `families_invite_code_unique` (`invite_code`);

--
-- Indexes for table `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `goals_family_id_foreign` (`family_id`);

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
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `push_subscriptions`
--
ALTER TABLE `push_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `push_subscriptions_endpoint_unique` (`endpoint`),
  ADD KEY `push_subscriptions_subscribable_morph_idx` (`subscribable_type`,`subscribable_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_family_id_foreign` (`family_id`),
  ADD KEY `transactions_wallet_id_foreign` (`wallet_id`),
  ADD KEY `transactions_category_id_foreign` (`category_id`),
  ADD KEY `transactions_user_id_foreign` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_family_id_foreign` (`family_id`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wallets_family_id_foreign` (`family_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `debts`
--
ALTER TABLE `debts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `families`
--
ALTER TABLE `families`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `goals`
--
ALTER TABLE `goals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `push_subscriptions`
--
ALTER TABLE `push_subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assets`
--
ALTER TABLE `assets`
  ADD CONSTRAINT `assets_family_id_foreign` FOREIGN KEY (`family_id`) REFERENCES `families` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `budgets`
--
ALTER TABLE `budgets`
  ADD CONSTRAINT `budgets_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `budgets_family_id_foreign` FOREIGN KEY (`family_id`) REFERENCES `families` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_family_id_foreign` FOREIGN KEY (`family_id`) REFERENCES `families` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `debts`
--
ALTER TABLE `debts`
  ADD CONSTRAINT `debts_family_id_foreign` FOREIGN KEY (`family_id`) REFERENCES `families` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `goals`
--
ALTER TABLE `goals`
  ADD CONSTRAINT `goals_family_id_foreign` FOREIGN KEY (`family_id`) REFERENCES `families` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_family_id_foreign` FOREIGN KEY (`family_id`) REFERENCES `families` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_family_id_foreign` FOREIGN KEY (`family_id`) REFERENCES `families` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_family_id_foreign` FOREIGN KEY (`family_id`) REFERENCES `families` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
