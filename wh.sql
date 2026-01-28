-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 24 يناير 2026 الساعة 02:19
-- إصدار الخادم: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wh`
--

-- --------------------------------------------------------

--
-- بنية الجدول `admin_menu`
--

CREATE TABLE `admin_menu` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL DEFAULT 0,
  `title` varchar(50) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `uri` varchar(255) DEFAULT NULL,
  `permission` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `admin_menu`
--

INSERT INTO `admin_menu` (`id`, `parent_id`, `order`, `title`, `icon`, `uri`, `permission`, `created_at`, `updated_at`) VALUES
(1, 0, 1, 'Dashboard', 'icon-chart-bar', '/', NULL, NULL, NULL),
(2, 0, 5, 'Admin', 'icon-server', '', NULL, NULL, NULL),
(3, 2, 6, 'Users', 'icon-users', 'auth/users', NULL, NULL, NULL),
(4, 2, 7, 'Roles', 'icon-user', 'auth/roles', NULL, NULL, NULL),
(5, 2, 8, 'Permission', 'icon-ban', 'auth/permissions', NULL, NULL, NULL),
(6, 2, 9, 'Menu', 'icon-bars', 'auth/menu', NULL, NULL, NULL),
(7, 2, 10, 'Operation log', 'icon-history', 'auth/logs', NULL, NULL, NULL),
(8, 0, 11, 'Helpers', 'icon-cogs', '', NULL, NULL, NULL),
(9, 8, 12, 'Scaffold', 'icon-keyboard', 'helpers/scaffold', NULL, NULL, NULL),
(10, 8, 13, 'Database terminal', 'icon-database', 'helpers/terminal/database', NULL, NULL, NULL),
(11, 8, 14, 'Laravel artisan', 'icon-terminal', 'helpers/terminal/artisan', NULL, NULL, NULL),
(12, 8, 15, 'Routes', 'icon-list-alt', 'helpers/routes', NULL, NULL, NULL),
(13, 0, 2, 'Customers', 'icon-user', 'customers', NULL, NULL, NULL),
(14, 0, 3, 'Trips', 'icon-ship', 'trips', NULL, NULL, NULL),
(15, 0, 4, 'Bookings', 'icon-address-book', 'trip-bookings', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- بنية الجدول `admin_operation_log`
--

CREATE TABLE `admin_operation_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  `method` varchar(10) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `input` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `admin_permissions`
--

CREATE TABLE `admin_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `http_method` varchar(255) DEFAULT NULL,
  `http_path` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `admin_permissions`
--

INSERT INTO `admin_permissions` (`id`, `name`, `slug`, `http_method`, `http_path`, `created_at`, `updated_at`) VALUES
(1, 'All permission', '*', '', '*', NULL, NULL),
(2, 'Dashboard', 'dashboard', 'GET', '/', NULL, NULL),
(3, 'Login', 'auth.login', '', '/auth/login\r\n/auth/logout', NULL, NULL),
(4, 'User setting', 'auth.setting', 'GET,PUT', '/auth/setting', NULL, NULL),
(5, 'Auth management', 'auth.management', '', '/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs', NULL, NULL),
(6, 'Admin helpers', 'ext.helpers', '', '/helpers/*', NULL, NULL),
(7, 'Added Price Percentage', 'auth.settings.added', 'GET,POST,PUT', '/settings/*', NULL, NULL),
(8, 'View Trips', 'auth.view.trips', 'GET,POST,PUT', '/trips', NULL, NULL),
(9, 'Manage Trips', 'auth.manage.trips', 'GET,POST,PUT,DELETE', '/trips/*', NULL, NULL),
(10, 'view customers', 'view.cus', 'GET', '/customers', NULL, NULL),
(11, 'View Bookings', 'auth.view.bookings', 'GET,POST,PUT', '/trip-bookings', NULL, NULL),
(12, 'Manage Bookings', 'auth.manage.bookings', 'GET,POST,PUT', '/trip-bookings/*', NULL, NULL),
(13, 'Manage Customers', 'manage.cus', 'GET,PUT,POST', '/customers*', NULL, NULL),
(14, 'Accept Trips', 'manage_acceptance', '', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- بنية الجدول `admin_roles`
--

CREATE TABLE `admin_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `admin_roles`
--

INSERT INTO `admin_roles` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'administrator', NULL, NULL),
(2, 'Company', 'company', NULL, NULL),
(3, 'customers.m', 'Customers.manager', NULL, NULL),
(4, 'Employee', 'Employee', NULL, NULL);

-- --------------------------------------------------------

--
-- بنية الجدول `admin_role_menu`
--

CREATE TABLE `admin_role_menu` (
  `role_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `admin_role_menu`
--

INSERT INTO `admin_role_menu` (`role_id`, `menu_id`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL),
(1, 2, NULL, NULL),
(1, 14, NULL, NULL),
(1, 16, NULL, NULL),
(2, 1, NULL, NULL),
(2, 14, NULL, NULL),
(2, 16, NULL, NULL),
(3, 1, NULL, NULL),
(3, 14, NULL, NULL),
(3, 16, NULL, NULL);

-- --------------------------------------------------------

--
-- بنية الجدول `admin_role_permissions`
--

CREATE TABLE `admin_role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `admin_role_permissions`
--

INSERT INTO `admin_role_permissions` (`role_id`, `permission_id`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL),
(2, 2, NULL, NULL),
(2, 3, NULL, NULL),
(2, 4, NULL, NULL),
(2, 9, NULL, NULL),
(2, 10, NULL, NULL),
(2, 11, NULL, NULL),
(2, 12, NULL, NULL),
(2, 13, NULL, NULL),
(3, 11, NULL, NULL),
(3, 14, NULL, NULL),
(4, 1, NULL, NULL),
(4, 2, NULL, NULL),
(4, 3, NULL, NULL),
(4, 4, NULL, NULL),
(4, 5, NULL, NULL),
(4, 6, NULL, NULL),
(4, 9, NULL, NULL),
(4, 10, NULL, NULL),
(4, 11, NULL, NULL),
(4, 12, NULL, NULL),
(4, 13, NULL, NULL),
(4, 14, NULL, NULL),
(4, 15, NULL, NULL);

-- --------------------------------------------------------

--
-- بنية الجدول `admin_role_users`
--

CREATE TABLE `admin_role_users` (
  `role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `admin_users`
--

CREATE TABLE `admin_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(190) NOT NULL,
  `password` varchar(60) NOT NULL,
  `name` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `emails` text DEFAULT NULL,
  `phone_numbers` text DEFAULT NULL,
  `files` text DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `admin_user_permissions`
--

CREATE TABLE `admin_user_permissions` (
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `app_information`
--

CREATE TABLE `app_information` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `app_name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `app_information`
--

INSERT INTO `app_information` (`id`, `app_name`, `description`, `email`, `phone`, `website`, `created_at`, `updated_at`) VALUES
(1, 'WJHTAK', 'منصة لربط شركات السياحة بالعملاء، توفر حلولًا سياحية مميزة وسهلة الاستخدام.', 'contact@wjhtak.com', '+971 50 192 7944', 'https://www.wjhtak.com', '2026-01-23 23:50:02', '2026-01-23 23:50:02');

-- --------------------------------------------------------

--
-- بنية الجدول `banners`
--

CREATE TABLE `banners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `desc` varchar(2000) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `mobile_image_path` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `trip_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `booking_fees`
--

CREATE TABLE `booking_fees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `trip_booking_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `card_last_four` varchar(255) DEFAULT NULL,
  `payment_reference` varchar(255) DEFAULT NULL,
  `payment_uid` varchar(255) DEFAULT NULL,
  `total_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('un_paid','in_procees','canceled','done') NOT NULL DEFAULT 'un_paid',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `cities`
--

CREATE TABLE `cities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `country_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `company_codes`
--

CREATE TABLE `company_codes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `type` enum('fixed','percentage') NOT NULL DEFAULT 'fixed',
  `value` decimal(8,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `countries`
--

CREATE TABLE `countries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `nicename` varchar(255) DEFAULT NULL,
  `iso` varchar(255) DEFAULT NULL,
  `iso3` varchar(255) DEFAULT NULL,
  `numcode` varchar(255) DEFAULT NULL,
  `phonecode` varchar(255) DEFAULT NULL,
  `flag` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `failed_jobs`
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
-- بنية الجدول `favorites`
--

CREATE TABLE `favorites` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `trip_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2016_01_04_173148_create_admin_tables', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2024_07_07_053116_create_users_table', 1),
(7, '2024_07_08_034804_create_trips_table', 1),
(8, '2024_07_09_035005_create_trip_images_table', 1),
(9, '2024_07_09_035011_create_trip_rates_table', 1),
(10, '2024_07_10_170044_create_countries_table', 1),
(11, '2024_07_10_171021_add_from_country_id_to_trips_table', 1),
(12, '2024_07_10_171145_add_to_country_id_to_trips_table', 1),
(13, '2024_07_10_171239_add_foreign_keys_to_trips_table', 1),
(14, '2024_07_10_172955_add_country_code_to_users', 1),
(15, '2024_07_11_233802_create_cities_table', 1),
(16, '2024_07_12_001825_add_city_id_to_trips_table', 1),
(17, '2024_07_12_015641_create_trip_bookings_table', 1),
(18, '2024_07_30_032040_create_favorites_table', 1),
(19, '2024_07_30_185644_create_questions_table', 1),
(20, '2024_07_30_222754_create_app_information_table', 1),
(21, '2024_07_31_031216_create_notifications_table', 1),
(22, '2024_08_04_211323_add_birthday_and_gender_to_users_table', 1),
(23, '2024_08_04_211324_create_trip_page_visits_table', 1),
(24, '2024_08_04_211325_alter_admin_users_table', 1),
(25, '2024_08_04_211326_alter_trips_table', 1),
(26, '2024_08_20_172619_update_favorites_foreign_key_on_delete_cascade', 1),
(27, '2024_11_04_204700_add_remember_token_to_users_table', 1),
(28, '2024_11_05_211325_alter_trips_table', 1),
(29, '2024_11_05_211326_alter_users_table', 1),
(30, '2024_11_05_211327_alter_trips_table', 1),
(31, '2024_11_05_211328_alter_trip_bookings_table', 1),
(32, '2024_11_05_211330_create_banners_table', 1),
(33, '2024_11_05_211331_updates_on_tables', 1),
(34, '2024_12_11_210415_create_settings_table', 1),
(35, '2024_12_23_224552_add_tickets_column_to_trips_table', 1),
(36, '2024_12_25_004715_add_columns_to_notifications_table', 1),
(37, '2024_7_07_211329_alter_admins_table', 1),
(38, '2025_01_10_180204_add_column_to_users_table', 1),
(39, '2025_02_26_193306_add_trip_id_column_to_banners_table', 1),
(40, '2025_03_10_175255_create_booking_fees_table', 1),
(41, '2025_04_05_231523_add_column_to_trips_table', 1),
(42, '2025_04_11_161046_add_column_for_users_column_to_trips_table', 1),
(43, '2025_04_29_143948_create_companies_table', 1),
(44, '2025_04_29_153647_create_company_codes_table', 1),
(45, '2025_04_29_180311_add_columns_to_bookings_table', 1),
(46, '2026_01_24_000456_create_sessions_table', 1);

-- --------------------------------------------------------

--
-- بنية الجدول `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `icon` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `data` longtext DEFAULT NULL,
  `is_show` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `questions`
--

CREATE TABLE `questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `questions`
--

INSERT INTO `questions` (`id`, `question`, `answer`, `created_at`, `updated_at`) VALUES
(1, 'ما هي منصة وجهتك؟', 'منصة وجهتك هي منصة تربط بين شركات السياحة والعملاء، وتوفر حلول سياحية مميزة وسهلة الاستخدام.', '2026-01-23 23:49:04', '2026-01-23 23:49:04'),
(2, 'كيف يمكنني التسجيل في منصة وجهتك؟', 'يمكنك التسجيل في منصة وجهتك من خلال زيارة موقعنا على الإنترنت والضغط على زر التسجيل. بعد ذلك، قم بإدخال المعلومات المطلوبة واتبع الخطوات لإكمال التسجيل.', '2026-01-23 23:49:04', '2026-01-23 23:49:04'),
(3, 'ما هي المزايا التي تقدمها منصة وجهتك؟', 'توفر منصة وجهتك العديد من المزايا، مثل سهولة البحث عن العروض السياحية، مقارنة الأسعار، الحصول على تقييمات وآراء المستخدمين، وإمكانية التواصل المباشر مع شركات السياحة.', '2026-01-23 23:49:04', '2026-01-23 23:49:04'),
(4, 'هل يمكنني إلغاء حجزي عبر منصة وجهتك؟', 'نعم، يمكنك إلغاء حجوزاتك من خلال منصة وجهتك. يرجى مراجعة سياسة الإلغاء الخاصة بالشركة السياحية التي حجزت معها لمزيد من التفاصيل.', '2026-01-23 23:49:04', '2026-01-23 23:49:04'),
(5, 'كيف يمكنني التواصل مع دعم العملاء في منصة وجهتك؟', 'يمكنك التواصل مع دعم العملاء في منصة وجهتك من خلال البريد الإلكتروني أو رقم الهاتف الموجودين في صفحة التواصل على موقعنا. نحن هنا لمساعدتك في أي استفسارات أو مشاكل تواجهها.', '2026-01-23 23:49:04', '2026-01-23 23:49:04');

-- --------------------------------------------------------

--
-- بنية الجدول `sessions`
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
-- إرجاع أو استيراد بيانات الجدول `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('NWmnpSrcZ96bZZH6voy4APHK252XQsqUJc75eo2x', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQTJ5UHlYQlExSFhkR2c5TUk4SFU1dVhEd2hhaGJ0NjZSdEM1RGJmOSI7czoxMToiY291bnRyeUNvZGUiO3M6Mjoic2EiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMzOiJodHRwOi8vbG9jYWxob3N0L3doX3Qvd2otdC9wdWJsaWMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1769211649),
('WGM9WmeJlNDqHiys6366fbMKUvczGP8BOBZLdCcs', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTU94RDFLZjVOU2dBNWJxMVJZakxza09MOUR2WXJvZnJ5dHZSUlh2dSI7czoxMToiY291bnRyeUNvZGUiO3M6Mjoic2EiO3M6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMzOiJodHRwOi8vbG9jYWxob3N0L3doX3Qvd2otdC9wdWJsaWMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1769212459);

-- --------------------------------------------------------

--
-- بنية الجدول `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `trips`
--

CREATE TABLE `trips` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `tickets` text DEFAULT NULL,
  `description` text NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 1,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `is_ad` tinyint(1) NOT NULL DEFAULT 0,
  `duration` varchar(255) DEFAULT NULL,
  `price` decimal(8,2) NOT NULL,
  `price_before_discount` decimal(8,2) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `personnel_capacity` int(11) NOT NULL DEFAULT 2,
  `from_country_id` bigint(20) UNSIGNED NOT NULL,
  `from_city_id` bigint(20) UNSIGNED NOT NULL,
  `to_country_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `profit` double(8,2) NOT NULL DEFAULT 0.00,
  `percentage_profit_margin` double(8,2) NOT NULL DEFAULT 0.00,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `trip_bookings`
--

CREATE TABLE `trip_bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `trip_id` bigint(20) UNSIGNED NOT NULL,
  `tickets` longtext DEFAULT NULL,
  `passport` longtext NOT NULL,
  `nationality` longtext NOT NULL,
  `passport_expiry` longtext NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `card_last_four` varchar(255) NOT NULL,
  `payment_reference` varchar(255) NOT NULL,
  `payment_uid` varchar(255) DEFAULT NULL,
  `total_paid` decimal(10,2) NOT NULL,
  `vat` decimal(10,2) NOT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `trip_price` decimal(10,2) NOT NULL,
  `app_profit` decimal(10,2) NOT NULL,
  `company_profit` decimal(10,2) NOT NULL,
  `status` bigint(20) UNSIGNED NOT NULL,
  `passport_image_path` longtext NOT NULL,
  `visa_image_path` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_code_id` bigint(20) UNSIGNED DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `company_code` varchar(255) DEFAULT NULL,
  `code_type` enum('fixed','percentage') NOT NULL DEFAULT 'fixed',
  `code_value` decimal(8,2) NOT NULL DEFAULT 0.00,
  `code_attached_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `trip_images`
--

CREATE TABLE `trip_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `trip_id` bigint(20) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `trip_page_visits`
--

CREATE TABLE `trip_page_visits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `trip_id` bigint(20) UNSIGNED NOT NULL,
  `ip` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `trip_rates`
--

CREATE TABLE `trip_rates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `trip_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rate` int(11) DEFAULT NULL,
  `review` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `country_code` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `gender` enum('male','female') DEFAULT NULL,
  `fcm` varchar(255) DEFAULT NULL,
  `balance` decimal(10,2) NOT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `login_method` varchar(255) NOT NULL DEFAULT 'normal',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_menu`
--
ALTER TABLE `admin_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_operation_log`
--
ALTER TABLE `admin_operation_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_operation_log_user_id_index` (`user_id`);

--
-- Indexes for table `admin_permissions`
--
ALTER TABLE `admin_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_permissions_name_unique` (`name`),
  ADD UNIQUE KEY `admin_permissions_slug_unique` (`slug`);

--
-- Indexes for table `admin_roles`
--
ALTER TABLE `admin_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_roles_name_unique` (`name`),
  ADD UNIQUE KEY `admin_roles_slug_unique` (`slug`);

--
-- Indexes for table `admin_role_menu`
--
ALTER TABLE `admin_role_menu`
  ADD KEY `admin_role_menu_role_id_menu_id_index` (`role_id`,`menu_id`);

--
-- Indexes for table `admin_role_permissions`
--
ALTER TABLE `admin_role_permissions`
  ADD KEY `admin_role_permissions_role_id_permission_id_index` (`role_id`,`permission_id`);

--
-- Indexes for table `admin_role_users`
--
ALTER TABLE `admin_role_users`
  ADD KEY `admin_role_users_role_id_user_id_index` (`role_id`,`user_id`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_users_username_unique` (`username`),
  ADD KEY `admin_users_admin_id_foreign` (`admin_id`);

--
-- Indexes for table `admin_user_permissions`
--
ALTER TABLE `admin_user_permissions`
  ADD KEY `admin_user_permissions_user_id_permission_id_index` (`user_id`,`permission_id`);

--
-- Indexes for table `app_information`
--
ALTER TABLE `app_information`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `banners_admin_id_foreign` (`admin_id`),
  ADD KEY `banners_trip_id_foreign` (`trip_id`);

--
-- Indexes for table `booking_fees`
--
ALTER TABLE `booking_fees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_fees_trip_booking_id_foreign` (`trip_booking_id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cities_country_id_foreign` (`country_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_codes`
--
ALTER TABLE `company_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `company_codes_code_unique` (`code`),
  ADD KEY `company_codes_company_id_foreign` (`company_id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `favorites_user_id_foreign` (`user_id`),
  ADD KEY `favorites_trip_id_foreign` (`trip_id`);

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
  ADD KEY `notifications_user_id_foreign` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trips_company_id_foreign` (`company_id`),
  ADD KEY `trips_from_country_fk` (`from_country_id`),
  ADD KEY `trips_to_country_fk` (`to_country_id`),
  ADD KEY `trips_from_city_id_foreign` (`from_city_id`),
  ADD KEY `trips_admin_id_foreign` (`admin_id`);

--
-- Indexes for table `trip_bookings`
--
ALTER TABLE `trip_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trip_bookings_user_id_foreign` (`user_id`),
  ADD KEY `trip_bookings_trip_id_foreign` (`trip_id`),
  ADD KEY `trip_bookings_company_code_id_foreign` (`company_code_id`),
  ADD KEY `trip_bookings_company_id_foreign` (`company_id`);

--
-- Indexes for table `trip_images`
--
ALTER TABLE `trip_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trip_images_trip_id_foreign` (`trip_id`);

--
-- Indexes for table `trip_page_visits`
--
ALTER TABLE `trip_page_visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trip_page_visits_user_id_foreign` (`user_id`),
  ADD KEY `trip_page_visits_trip_id_foreign` (`trip_id`);

--
-- Indexes for table `trip_rates`
--
ALTER TABLE `trip_rates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trip_rates_trip_id_foreign` (`trip_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_phone_number_unique` (`phone_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_menu`
--
ALTER TABLE `admin_menu`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `admin_operation_log`
--
ALTER TABLE `admin_operation_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_permissions`
--
ALTER TABLE `admin_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `admin_roles`
--
ALTER TABLE `admin_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_information`
--
ALTER TABLE `app_information`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `booking_fees`
--
ALTER TABLE `booking_fees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_codes`
--
ALTER TABLE `company_codes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trip_bookings`
--
ALTER TABLE `trip_bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trip_images`
--
ALTER TABLE `trip_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trip_page_visits`
--
ALTER TABLE `trip_page_visits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trip_rates`
--
ALTER TABLE `trip_rates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- قيود الجداول المُلقاة.
--

--
-- قيود الجداول `admin_users`
--
ALTER TABLE `admin_users`
  ADD CONSTRAINT `admin_users_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

--
-- قيود الجداول `banners`
--
ALTER TABLE `banners`
  ADD CONSTRAINT `banners_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `banners_trip_id_foreign` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `booking_fees`
--
ALTER TABLE `booking_fees`
  ADD CONSTRAINT `booking_fees_trip_booking_id_foreign` FOREIGN KEY (`trip_booking_id`) REFERENCES `trip_bookings` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `company_codes`
--
ALTER TABLE `company_codes`
  ADD CONSTRAINT `company_codes_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_trip_id_foreign` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- قيود الجداول `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `trips`
--
ALTER TABLE `trips`
  ADD CONSTRAINT `trips_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `trips_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trips_from_city_id_foreign` FOREIGN KEY (`from_city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trips_from_country_fk` FOREIGN KEY (`from_country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trips_from_country_id_foreign` FOREIGN KEY (`from_country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trips_to_country_fk` FOREIGN KEY (`to_country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trips_to_country_id_foreign` FOREIGN KEY (`to_country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `trip_bookings`
--
ALTER TABLE `trip_bookings`
  ADD CONSTRAINT `trip_bookings_company_code_id_foreign` FOREIGN KEY (`company_code_id`) REFERENCES `company_codes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trip_bookings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trip_bookings_trip_id_foreign` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trip_bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `trip_images`
--
ALTER TABLE `trip_images`
  ADD CONSTRAINT `trip_images_trip_id_foreign` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `trip_page_visits`
--
ALTER TABLE `trip_page_visits`
  ADD CONSTRAINT `trip_page_visits_trip_id_foreign` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trip_page_visits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- قيود الجداول `trip_rates`
--
ALTER TABLE `trip_rates`
  ADD CONSTRAINT `trip_rates_trip_id_foreign` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
