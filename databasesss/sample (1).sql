-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 25, 2026 at 02:23 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sample`
--

-- --------------------------------------------------------

--
-- Table structure for table `anecdotals`
--

CREATE TABLE `anecdotals` (
  `id` int(200) NOT NULL,
  `counseling_logforms_id` int(200) NOT NULL,
  `name` varchar(500) NOT NULL,
  `course_and_year` varchar(500) NOT NULL,
  `contact_no` varchar(20) NOT NULL,
  `address` varchar(500) NOT NULL,
  `area_concern` text NOT NULL,
  `concern` text NOT NULL,
  `intervention` text NOT NULL,
  `personnel_id` int(200) NOT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anecdotals`
--

INSERT INTO `anecdotals` (`id`, `counseling_logforms_id`, `name`, `course_and_year`, `contact_no`, `address`, `area_concern`, `concern`, `intervention`, `personnel_id`, `created_at`, `updated_at`) VALUES
(4, 4, 'Jeric Gargarita Gabayeron', 'BSIT 3', '09876434565', 'kjhgf', 'wala', '<p>blablablablablabla</p>', '<p>balbalablablalb</p>', 1, '2026-02-12 22:10:02.000000', '2026-02-12 22:10:02.000000'),
(7, 8, 'Juan Dela Cruz', 'BSIT 3', '09876567', 'lnb n', 'mnbnm', '<p>mn nm</p>', '<p>nbnml</p>', 1, '2026-02-16 04:26:41.000000', '2026-02-16 04:26:41.000000'),
(8, 9, 'Abid Mangelen', 'BSHM 2', '09876678', 'erghgf', 'lovelife', '<p>sdfbv</p>', '<p>sdfnb</p>', 1, '2026-02-16 04:29:02.000000', '2026-02-16 04:29:02.000000');

-- --------------------------------------------------------

--
-- Table structure for table `applicants`
--

CREATE TABLE `applicants` (
  `id` int(100) NOT NULL,
  `picture` varchar(100) DEFAULT NULL,
  `type_of_application_id` int(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `course_and_year` varchar(100) NOT NULL,
  `gender_id` int(100) NOT NULL,
  `contact_no` varchar(16) NOT NULL,
  `birthdate` date NOT NULL,
  `age` int(100) NOT NULL,
  `religion` varchar(100) NOT NULL,
  `facebook_account` varchar(100) NOT NULL,
  `fathers_name` varchar(100) NOT NULL,
  `fathers_contact_no` varchar(16) NOT NULL,
  `mothers_name` varchar(100) NOT NULL,
  `mothers_contact_no` varchar(16) NOT NULL,
  `guardian` varchar(100) NOT NULL,
  `guardian_contact_no` varchar(16) NOT NULL,
  `type_of_scholarship_id` int(100) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT 'pending',
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applicants`
--

INSERT INTO `applicants` (`id`, `picture`, `type_of_application_id`, `first_name`, `middle_name`, `last_name`, `course_and_year`, `gender_id`, `contact_no`, `birthdate`, `age`, `religion`, `facebook_account`, `fathers_name`, `fathers_contact_no`, `mothers_name`, `mothers_contact_no`, `guardian`, `guardian_contact_no`, `type_of_scholarship_id`, `status`, `created_at`, `updated_at`) VALUES
(2, 'applicant-pictures/01KHB3W6A294P9RJXZZYPFRBCB.jpg', 2, 'Mikhaela Janna', 'Jimenea', 'Lim', 'sdfghhg', 1, '09876543234', '2026-02-16', 21, 'kjhgfdsa', 'knbvcx', 'jhgfdsa', '0987654345', 'jhgfdxz', '09876543', 'kjhgfds', '09876543', 1, 'approved', '2026-02-02 20:39:54.000000', '2026-02-16 03:33:04.000000'),
(3, 'applicant-pictures/01KHBKB4HNQB418ZDFTK32WXMR.jpg', 1, 'Mary Loi Yves', 'Kipte', 'Ricalde', 'BSIT 3', 1, '0987654345', '2026-02-20', 23, 'catholic', 'sdfbbv', 'xcvbbvc', '0987654567', 'dfgb', '098765678', 'kjhgfgh', '098765456789', 1, 'approved', '2026-02-13 05:34:14.000000', '2026-02-19 07:41:16.000000'),
(4, 'applicant-pictures/01KHK4QK9R6F191QKBAASKAACN.png', 1, 'Jhoanna Christine', 'Burgos', 'Robles', 'BSIT 3', 1, '09386370921', '2026-02-12', 22, 'fbvf', 'dfvb', 'dfb', '09876789876', 'dfvb', '09456789876', 'dfvbbv', '09876545678', 1, 'rejected', '2026-02-16 03:52:49.000000', '2026-02-16 03:53:48.000000'),
(5, 'applicant-pictures/01KHZQ1YF17ETYFABNN5R5GP5H.png', 2, 'kjhgfd', 'jhgfd', 'kjhgf', 'lkjhgf', 1, '098765432', '2026-02-21', 21, 'mhgfds', 'jhgfd', 'jhgfd', '09876543', 'nbvcxz', '09876543', 'kjhgfd', '0987654', 1, 'approved', '2026-02-21 01:03:56.000000', '2026-02-21 01:04:44.000000');

-- --------------------------------------------------------

--
-- Table structure for table `applicant_requirement`
--

CREATE TABLE `applicant_requirement` (
  `id` int(100) NOT NULL,
  `applicant_id` int(100) NOT NULL,
  `requirement_id` int(100) NOT NULL,
  `is_submitted` tinyint(1) NOT NULL DEFAULT 1,
  `file_path` varchar(500) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applicant_requirement`
--

INSERT INTO `applicant_requirement` (`id`, `applicant_id`, `requirement_id`, `is_submitted`, `file_path`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'requirements/01KGGW7R03RFPYKAKDV1XVCAXZ.png', NULL, '2026-02-02 20:30:10.000000', '2026-02-02 20:30:10.000000'),
(2, 2, 2, 1, 'requirements/01KGGWVK8CMX55RT2MYGGGFESW.png', NULL, '2026-02-02 20:41:01.000000', '2026-02-02 20:41:01.000000'),
(3, 2, 3, 1, 'requirements/01KGGWXH6WSZX2VG00Z4VA50Z7.png', NULL, '2026-02-02 20:42:04.000000', '2026-02-02 20:42:04.000000'),
(4, 2, 4, 1, 'requirements/01KGGWYVS56XXQM25WJAGZ83Q6.png', NULL, '2026-02-02 20:42:48.000000', '2026-02-02 20:42:48.000000'),
(5, 2, 5, 1, 'requirements/01KGGWZHJNY54WZP1DCRCM5Z09.png', NULL, '2026-02-02 20:43:10.000000', '2026-02-02 20:43:10.000000'),
(6, 3, 11, 1, 'requirements/01KHJ7ETCHC2C23EJCC8YVJZCS.png', ',mnbvc', '2026-02-15 19:21:13.000000', '2026-02-15 23:27:31.000000');

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
('laravel-cache-da4b9237bacccdf19c0760cab7aec4a8359010b0', 'i:1;', 1771664691),
('laravel-cache-da4b9237bacccdf19c0760cab7aec4a8359010b0:timer', 'i:1771664691;', 1771664691),
('laravel-cache-livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3', 'i:1;', 1772024785),
('laravel-cache-livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3:timer', 'i:1772024785;', 1772024785);

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
-- Table structure for table `counseling_appointments`
--

CREATE TABLE `counseling_appointments` (
  `id` int(200) NOT NULL,
  `last_name` varchar(500) NOT NULL,
  `first_name` varchar(500) NOT NULL,
  `middle_name` varchar(500) DEFAULT NULL,
  `course_and_year` varchar(500) NOT NULL,
  `contact_no` varchar(20) NOT NULL,
  `present_address` varchar(200) NOT NULL,
  `counseling_date` date NOT NULL,
  `time_slot_id` int(200) NOT NULL,
  `mode_of_counseling_id` int(200) NOT NULL,
  `support_needed_id` int(200) NOT NULL,
  `concern` varchar(500) NOT NULL,
  `status` varchar(100) DEFAULT 'pending',
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `counseling_appointments`
--

INSERT INTO `counseling_appointments` (`id`, `last_name`, `first_name`, `middle_name`, `course_and_year`, `contact_no`, `present_address`, `counseling_date`, `time_slot_id`, `mode_of_counseling_id`, `support_needed_id`, `concern`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Dela Cruz', 'Juan', 'kaka', 'BSIT 3', '09123456789', 'jhghjk', '2026-02-21', 1, 1, 1, 'wala', 'rejected', '2026-02-11 00:14:58.000000', '2026-02-21 11:20:08.000000'),
(10, 'Gabayeron', 'Jeric', 'Gargarita', 'BSIT 3', '09876434565', 'kjhgf', '2026-02-19', 1, 1, 1, 'Secret', 'approved', '2026-02-12 06:21:41.000000', '2026-02-14 02:54:45.000000'),
(11, 'Mangelen', 'Abid', NULL, 'hg', '0987544567', 'lkjhgfdf', '2026-02-21', 3, 1, 1, 'wala', 'rejected', '2026-02-14 06:05:26.000000', '2026-02-17 00:55:55.000000'),
(12, 'Sambolawan', 'Shariffa Janna', 'Sabpa', 'BSIT 3', '09876543234', 'asdfg', '2026-02-18', 3, 1, 1, 'asx', 'pending', '2026-02-15 18:57:36.000000', '2026-02-15 18:57:36.000000');

-- --------------------------------------------------------

--
-- Table structure for table `counseling_logforms`
--

CREATE TABLE `counseling_logforms` (
  `id` int(200) NOT NULL,
  `counseling_appointments_id` int(200) DEFAULT NULL,
  `referral_id` int(200) NOT NULL,
  `name` varchar(500) NOT NULL,
  `course_and_year` varchar(500) NOT NULL,
  `contact_no` varchar(20) NOT NULL,
  `address` varchar(500) NOT NULL,
  `concern` varchar(500) NOT NULL,
  `remarks` varchar(200) NOT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `counseling_logforms`
--

INSERT INTO `counseling_logforms` (`id`, `counseling_appointments_id`, `referral_id`, `name`, `course_and_year`, `contact_no`, `address`, `concern`, `remarks`, `created_at`, `updated_at`) VALUES
(4, 10, 0, 'Jeric Gargarita Gabayeron', 'BSIT 3', '09876434565', 'kjhgf', 'Secret', 'tapos na', '2026-02-12 22:10:02.000000', '2026-02-12 22:10:02.000000'),
(8, NULL, 4, 'Juan Dela Cruz', 'BSIT 3', '0987654567', 'gfcgh', 'wala', 'wala', '2026-02-16 04:26:41.000000', '2026-02-16 04:26:41.000000'),
(9, NULL, 5, 'Abid Mangelen', 'BSHM 2', '098765456789', 'lhcvbnm', 'secret', 'sige', '2026-02-16 04:29:02.000000', '2026-02-16 04:29:02.000000');

-- --------------------------------------------------------

--
-- Table structure for table `counseling_time_slots`
--

CREATE TABLE `counseling_time_slots` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `counseling_time_slots`
--

INSERT INTO `counseling_time_slots` (`id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, '9:00 am - 10:00 am', 0, '2026-02-02 04:07:14.000000', '2026-02-16 23:42:45.000000'),
(2, '11:00 am - 12:00 nn', 1, '2026-02-14 04:33:28.000000', '2026-02-14 04:33:28.000000'),
(3, '1:00 pm - 2:00 pm', 1, '2026-02-14 04:33:58.000000', '2026-02-14 04:33:58.000000'),
(4, '2:00 pm - 3:00 pm', 1, '2026-02-14 04:34:17.000000', '2026-02-14 04:34:17.000000');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'College Of Engineering And Technology', 1, '2026-02-02 02:35:47.000000', '2026-02-02 02:35:47.000000');

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
-- Table structure for table `genders`
--

CREATE TABLE `genders` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genders`
--

INSERT INTO `genders` (`id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Male', 1, '2026-02-02 04:10:51.000000', '2026-02-02 04:10:51.000000');

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
(3, '0001_01_01_000002_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `mode_of_counselings`
--

CREATE TABLE `mode_of_counselings` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mode_of_counselings`
--

INSERT INTO `mode_of_counselings` (`id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Online', 1, '2026-02-02 04:24:21.000000', '2026-02-02 04:24:21.000000');

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
-- Table structure for table `personnels`
--

CREATE TABLE `personnels` (
  `id` int(200) NOT NULL,
  `first_name` varchar(500) NOT NULL,
  `middle_name` varchar(500) NOT NULL,
  `last_name` varchar(500) NOT NULL,
  `age` int(100) NOT NULL,
  `birthdate` date NOT NULL,
  `contact_no` varchar(20) NOT NULL,
  `address` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `personnels`
--

INSERT INTO `personnels` (`id`, `first_name`, `middle_name`, `last_name`, `age`, `birthdate`, `contact_no`, `address`, `email`, `created_at`, `updated_at`) VALUES
(1, 'Shariffa Janna', 'Sabpa', 'Sambolawan', 22, '2003-04-30', '098765434567', 'oiugffgh', 'shariffa@gmail.com', '2026-02-12 05:20:46.000000', '2026-02-12 05:20:46.000000');

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Bachelor Of Science In Information And Technology', '2026-02-02 04:02:11.000000', '2026-02-02 04:02:11.000000');

-- --------------------------------------------------------

--
-- Table structure for table `referrals`
--

CREATE TABLE `referrals` (
  `id` int(200) NOT NULL,
  `date` date NOT NULL,
  `name` varchar(500) NOT NULL,
  `course_and_year` varchar(500) NOT NULL,
  `age` int(200) NOT NULL,
  `case_presented` text NOT NULL,
  `referred_by` varchar(100) NOT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `referrals`
--

INSERT INTO `referrals` (`id`, `date`, `name`, `course_and_year`, `age`, `case_presented`, `referred_by`, `created_at`, `updated_at`) VALUES
(4, '2026-02-16', 'Juan Dela Cruz', 'BSIT 3', 22, 'blablablabla', 'ojh', '2026-02-16 04:15:44.000000', '2026-02-16 04:15:44.000000'),
(5, '2026-02-16', 'Abid Mangelen', 'BSHM 2', 22, 'blabla', 'blabla', '2026-02-16 04:27:48.000000', '2026-02-16 04:27:48.000000'),
(6, '2026-02-21', 'jhgfd', 'nbvcx', 21, 'jhgfd', 'jhgfds', '2026-02-21 01:12:59.000000', '2026-02-21 01:12:59.000000');

-- --------------------------------------------------------

--
-- Table structure for table `requirements`
--

CREATE TABLE `requirements` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type_of_application_id` int(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requirements`
--

INSERT INTO `requirements` (`id`, `name`, `type_of_application_id`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 'Letter of Intent', 2, 1, '2026-02-02 20:33:02.000000', '2026-02-02 20:33:02.000000'),
(3, 'Photocopies of Awards', 2, 1, '2026-02-02 20:33:37.000000', '2026-02-02 20:33:37.000000'),
(4, '1 pc of 2x2 ID picture', 2, 1, '2026-02-02 20:34:14.000000', '2026-02-02 20:34:14.000000'),
(5, 'Copy of Grades from previous School Year/Semester', 2, 1, '2026-02-02 20:35:08.000000', '2026-02-02 20:35:08.000000'),
(6, 'Study Load', 2, 1, '2026-02-02 20:35:29.000000', '2026-02-02 20:35:29.000000'),
(7, 'Application Form', 2, 1, '2026-02-02 20:35:44.000000', '2026-02-02 20:35:44.000000'),
(8, 'Certificate of Employment', 2, 1, '2026-02-02 20:36:14.000000', '2026-02-02 20:36:14.000000'),
(9, 'Copy of Grades from Previous Semester', 1, 1, '2026-02-02 20:36:44.000000', '2026-02-02 20:36:44.000000'),
(10, 'Renewal Form', 1, 1, '2026-02-02 20:36:58.000000', '2026-02-02 20:36:58.000000'),
(11, 'Accomplishment Report', 1, 1, '2026-02-02 20:37:28.000000', '2026-02-02 20:37:28.000000'),
(12, 'Study Load', 1, 1, '2026-02-02 20:37:43.000000', '2026-02-02 20:37:43.000000'),
(13, 'Daily Time Record', 1, 1, '2026-02-02 20:37:59.000000', '2026-02-02 20:37:59.000000'),
(14, 'Certificate of Employment', 1, 1, '2026-02-02 20:38:33.000000', '2026-02-02 20:38:33.000000');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', '2026-02-02 04:19:28.000000', '2026-02-02 04:19:28.000000'),
(2, 'Guidance', NULL, NULL),
(3, 'Scholarship', NULL, NULL),
(4, 'student', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `school_positions`
--

CREATE TABLE `school_positions` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `school_positions`
--

INSERT INTO `school_positions` (`id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Janitor', 1, '2026-02-02 04:16:21.000000', '2026-02-02 04:16:21.000000');

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
('sq1DGWtHpuHFXYh5boxvpEBD9tVJ1UWpGaSlKqJc', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiYkJwOE1nUXpBc2FXTHRLdTNHUHVVc05KbXBLRzNObzZRSjU4Rm5nWiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9yZWdpc3RlciI7czo1OiJyb3V0ZSI7czo4OiJyZWdpc3RlciI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6ImQ0YzdiZGQ0NjIyNTljZmY2ZGU1OGJlNjZmMzA2NGZmMjJjYTE0Y2RlNTY2NDYxZWI4YTU4MzZmMDY3ZTRkMDgiO30=', 1772025685),
('tLfnZZuvnG89KUmQf73fX11j1LhphvCrBWrB7EUg', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/2.5.20 Chrome/142.0.7444.265 Electron/39.4.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiN3BId21XTUhuVWhJcDEySVk4RExnR29oVU1nejQxVlJDRVloNW5NUCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1772024795);

-- --------------------------------------------------------

--
-- Table structure for table `support_neededs`
--

CREATE TABLE `support_neededs` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `support_neededs`
--

INSERT INTO `support_neededs` (`id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Personal', 1, '2026-02-02 04:28:04.000000', '2026-02-02 04:28:04.000000');

-- --------------------------------------------------------

--
-- Table structure for table `type_of_applications`
--

CREATE TABLE `type_of_applications` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `type_of_applications`
--

INSERT INTO `type_of_applications` (`id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Renewal', 1, '2026-02-02 04:32:33.000000', '2026-02-02 04:32:33.000000'),
(2, 'New Applicant', 1, '2026-02-02 20:31:19.000000', '2026-02-02 20:31:19.000000');

-- --------------------------------------------------------

--
-- Table structure for table `type_of_scholarships`
--

CREATE TABLE `type_of_scholarships` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp(6) NULL DEFAULT NULL,
  `updated_at` timestamp(6) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `type_of_scholarships`
--

INSERT INTO `type_of_scholarships` (`id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Academic', 1, '2026-02-02 04:37:47.000000', '2026-02-02 04:37:47.000000');

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
  `role_id` int(200) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `role_id`, `created_at`, `updated_at`) VALUES
(2, 'John Crisostomo Salazar', 'admin@gmail.com', NULL, '$2y$12$kwOQa2FMEi50YI8qBAC4yORdcF6m1HDVfZcG7E7LjG7ePL0zxck9a', NULL, 1, '2026-02-12 04:38:24', '2026-02-12 04:43:43'),
(3, 'Shariffa Janna Sambolawan', 'shariffa@gmail.com', NULL, '$2y$12$s12f4VGpsieySmwbYP5g6eybjCYbWccMSjtW29LR68YEbAh0WnaVy', NULL, 2, '2026-02-14 07:21:08', '2026-02-14 07:21:08'),
(4, 'Jeric Gabayeron', 'jeric@gmail.com', NULL, '$2y$12$1NR23kV3LkFc8M6Efo69ruDuPHT/FzTBtyC4agKWTo9BG.En6oRce', NULL, 3, '2026-02-14 07:27:22', '2026-02-14 07:27:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anecdotals`
--
ALTER TABLE `anecdotals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `counseling_logforms_id` (`counseling_logforms_id`),
  ADD KEY `personnel_id` (`personnel_id`);

--
-- Indexes for table `applicants`
--
ALTER TABLE `applicants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type_of_application_id` (`type_of_application_id`),
  ADD KEY `gender_id` (`gender_id`),
  ADD KEY `type_of_scholarship_id` (`type_of_scholarship_id`);

--
-- Indexes for table `applicant_requirement`
--
ALTER TABLE `applicant_requirement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `applicant_id` (`applicant_id`),
  ADD KEY `requirement_id` (`requirement_id`);

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
-- Indexes for table `counseling_appointments`
--
ALTER TABLE `counseling_appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `support_needed_id` (`support_needed_id`),
  ADD KEY `time_slot_id` (`time_slot_id`),
  ADD KEY `mode_of_counseling_id` (`mode_of_counseling_id`);

--
-- Indexes for table `counseling_logforms`
--
ALTER TABLE `counseling_logforms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `counseling_appointments_id` (`counseling_appointments_id`),
  ADD KEY `referral_id` (`referral_id`);

--
-- Indexes for table `counseling_time_slots`
--
ALTER TABLE `counseling_time_slots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `genders`
--
ALTER TABLE `genders`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `mode_of_counselings`
--
ALTER TABLE `mode_of_counselings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personnels`
--
ALTER TABLE `personnels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `referrals`
--
ALTER TABLE `referrals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `requirements`
--
ALTER TABLE `requirements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type_of_application_id` (`type_of_application_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `school_positions`
--
ALTER TABLE `school_positions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `support_neededs`
--
ALTER TABLE `support_neededs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `type_of_applications`
--
ALTER TABLE `type_of_applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `type_of_scholarships`
--
ALTER TABLE `type_of_scholarships`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anecdotals`
--
ALTER TABLE `anecdotals`
  MODIFY `id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `applicants`
--
ALTER TABLE `applicants`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `applicant_requirement`
--
ALTER TABLE `applicant_requirement`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `counseling_appointments`
--
ALTER TABLE `counseling_appointments`
  MODIFY `id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `counseling_logforms`
--
ALTER TABLE `counseling_logforms`
  MODIFY `id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `counseling_time_slots`
--
ALTER TABLE `counseling_time_slots`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `genders`
--
ALTER TABLE `genders`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `mode_of_counselings`
--
ALTER TABLE `mode_of_counselings`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `personnels`
--
ALTER TABLE `personnels`
  MODIFY `id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `referrals`
--
ALTER TABLE `referrals`
  MODIFY `id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `requirements`
--
ALTER TABLE `requirements`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `school_positions`
--
ALTER TABLE `school_positions`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `support_neededs`
--
ALTER TABLE `support_neededs`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `type_of_applications`
--
ALTER TABLE `type_of_applications`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `type_of_scholarships`
--
ALTER TABLE `type_of_scholarships`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anecdotals`
--
ALTER TABLE `anecdotals`
  ADD CONSTRAINT `anecdotals_ibfk_2` FOREIGN KEY (`personnel_id`) REFERENCES `personnels` (`id`),
  ADD CONSTRAINT `anecdotals_ibfk_3` FOREIGN KEY (`counseling_logforms_id`) REFERENCES `counseling_logforms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `applicants`
--
ALTER TABLE `applicants`
  ADD CONSTRAINT `applicants_ibfk_1` FOREIGN KEY (`type_of_application_id`) REFERENCES `type_of_applications` (`id`),
  ADD CONSTRAINT `applicants_ibfk_2` FOREIGN KEY (`gender_id`) REFERENCES `genders` (`id`),
  ADD CONSTRAINT `applicants_ibfk_3` FOREIGN KEY (`type_of_scholarship_id`) REFERENCES `type_of_scholarships` (`id`);

--
-- Constraints for table `counseling_appointments`
--
ALTER TABLE `counseling_appointments`
  ADD CONSTRAINT `counseling_appointments_ibfk_1` FOREIGN KEY (`time_slot_id`) REFERENCES `counseling_time_slots` (`id`),
  ADD CONSTRAINT `counseling_appointments_ibfk_2` FOREIGN KEY (`mode_of_counseling_id`) REFERENCES `mode_of_counselings` (`id`),
  ADD CONSTRAINT `counseling_appointments_ibfk_3` FOREIGN KEY (`support_needed_id`) REFERENCES `support_neededs` (`id`);

--
-- Constraints for table `counseling_logforms`
--
ALTER TABLE `counseling_logforms`
  ADD CONSTRAINT `counseling_logforms_ibfk_1` FOREIGN KEY (`counseling_appointments_id`) REFERENCES `counseling_appointments` (`id`);

--
-- Constraints for table `requirements`
--
ALTER TABLE `requirements`
  ADD CONSTRAINT `requirements_ibfk_1` FOREIGN KEY (`type_of_application_id`) REFERENCES `type_of_applications` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
