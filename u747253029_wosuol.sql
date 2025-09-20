-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 16, 2025 at 04:07 PM
-- Server version: 10.11.10-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u747253029_wosuol`
--

-- --------------------------------------------------------

--
-- Table structure for table `backup_settings`
--

CREATE TABLE `backup_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) DEFAULT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `backup_settings`
--

INSERT INTO `backup_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'backup_frequency', 'daily', '2025-09-01 01:37:07'),
(2, 'backup_time', '02:00', '2025-09-01 01:37:07'),
(3, 'auto_cleanup', '1', '2025-09-01 01:37:07'),
(4, 'keep_days', '30', '2025-09-01 01:37:07');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_slug` varchar(255) DEFAULT NULL,
  `bride_name_ar` varchar(255) DEFAULT NULL,
  `bride_name_en` varchar(255) DEFAULT NULL,
  `groom_name_ar` varchar(255) DEFAULT NULL,
  `groom_name_en` varchar(255) DEFAULT NULL,
  `event_date_ar` text DEFAULT NULL,
  `event_date_en` text DEFAULT NULL,
  `venue_ar` varchar(255) DEFAULT NULL,
  `venue_en` varchar(255) DEFAULT NULL,
  `Maps_link` varchar(1024) DEFAULT NULL,
  `event_paragraph_ar` text DEFAULT NULL,
  `event_paragraph_en` text DEFAULT NULL,
  `background_image_url` varchar(1024) DEFAULT NULL,
  `whatsapp_image_url` varchar(1024) DEFAULT NULL COMMENT 'رابط صورة الواتساب للدعوات',
  `qr_card_title_ar` varchar(255) DEFAULT 'بطاقة دخول شخصية',
  `qr_card_title_en` varchar(255) DEFAULT 'Personal Entry Card',
  `qr_show_code_instruction_ar` varchar(255) DEFAULT 'يرجى إبراز الكود للدخول',
  `qr_show_code_instruction_en` varchar(255) DEFAULT 'Please show code to enter',
  `qr_brand_text_ar` varchar(255) DEFAULT 'Wosuol.com',
  `qr_brand_text_en` varchar(255) DEFAULT 'Wosuol.com',
  `qr_website` varchar(255) DEFAULT 'Wosuol.com',
  `n8n_confirm_webhook` varchar(1024) DEFAULT NULL,
  `n8n_initial_invite_webhook` varchar(1024) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reminder_image_url` varchar(1024) DEFAULT NULL COMMENT 'رابط صورة التذكير',
  `max_guests_allowed` int(11) DEFAULT NULL COMMENT 'العدد الأقصى للضيوف المسموح بتسجيلهم لهذا الحدث',
  `current_guests_count` int(11) DEFAULT 0 COMMENT 'العدد الحالي للضيوف المسجلين',
  `registration_show_phone` tinyint(1) DEFAULT 1 COMMENT 'Show phone field',
  `registration_require_phone` tinyint(1) DEFAULT 1 COMMENT 'Make phone required',
  `registration_show_guest_count` tinyint(1) DEFAULT 1 COMMENT 'Show guest count field',
  `registration_show_countdown` tinyint(1) DEFAULT 1 COMMENT 'Show countdown timer',
  `registration_show_location` tinyint(1) DEFAULT 1 COMMENT 'Show location info',
  `registration_mode` enum('simple','full') DEFAULT 'full' COMMENT 'Registration mode',
  `rsvp_show_guest_count` tinyint(1) NOT NULL DEFAULT 1,
  `rsvp_show_qr_code` tinyint(1) NOT NULL DEFAULT 1,
  `rsvp_show_countdown` tinyint(1) NOT NULL DEFAULT 1,
  `event_name_en` varchar(255) DEFAULT NULL,
  `message_template` text DEFAULT NULL COMMENT 'قالب الرسالة الخاص بالحدث',
  `message_template_en` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `event_name`, `event_slug`, `bride_name_ar`, `bride_name_en`, `groom_name_ar`, `groom_name_en`, `event_date_ar`, `event_date_en`, `venue_ar`, `venue_en`, `Maps_link`, `event_paragraph_ar`, `event_paragraph_en`, `background_image_url`, `whatsapp_image_url`, `qr_card_title_ar`, `qr_card_title_en`, `qr_show_code_instruction_ar`, `qr_show_code_instruction_en`, `qr_brand_text_ar`, `qr_brand_text_en`, `qr_website`, `n8n_confirm_webhook`, `n8n_initial_invite_webhook`, `created_at`, `reminder_image_url`, `max_guests_allowed`, `current_guests_count`, `registration_show_phone`, `registration_require_phone`, `registration_show_guest_count`, `registration_show_countdown`, `registration_show_location`, `registration_mode`, `rsvp_show_guest_count`, `rsvp_show_qr_code`, `rsvp_show_countdown`, `event_name_en`, `message_template`, `message_template_en`) VALUES
(13, 'حفل زفاف يحيى و هبة', 'Yahya_Heba', 'يرجى التحديث من لوحة التحكم', NULL, 'يرجى التحديث من لوحة التحكم', NULL, '2025-10-03 18:00:00', 'Friday, October 3, 2025 06:00 PM', 'RUMMAN THEATER - JARASH', '', 'https://maps.app.goo.gl/g3haqkBS6FzMVSto6', '', '', './uploads/display_event_13_1755947548.jpg', '', 'دعوة حفل زفاف يحيى و هبة', 'Wedding Invitation', 'يرجى إظهار هذا الرمز عند الدخول', 'Please show this code at entrance', 'WOSUOL', 'Wosuol', 'wosuol.com', '', '', '2025-08-23 09:57:21', NULL, NULL, 173, 0, 0, 0, 1, 1, 'full', 0, 0, 1, 'Yahya and Heba\'s wedding', NULL, NULL),
(20, 'WOSUOL EVENTS', 'hijjawi', 'يرجى التحديث من لوحة التحكم', NULL, 'يرجى التحديث من لوحة التحكم', NULL, '2025-10-03 20:00:00', 'Friday, October 3, 2025 08:00 PM', 'The Ritz Carlton hotel', 'The Ritz Carlton hotel', 'https://maps.app.goo.gl/EXmUwYTaTxdRESUTA?g_st=ipc', 'لطفا يرجى تأكيد حضوركم لحفل الزفاف', 'Please RSVP', './uploads/display_event_20_1757180413.jpeg', '', 'دعوة حفل زفاف', 'Wedding Invitation', 'يرجى إظهار هذا الرمز عند الدخول', 'Please show this code at entrance', 'wosuol | وصول', 'wosuol | وصول', 'wosuol.com', 'https://n8n.clouditech-me.com/webhook/confirm-rsvp-qr', 'https://n8n.clouditech-me.com/webhook/wasendesapi-send-invitations', '2025-08-03 00:14:42', '', NULL, 5, 1, 1, 1, 1, 1, 'full', 1, 1, 1, 'WOSUOL EVENTS', 'مرحبا (guest_name),\r\n\r\nنتشرف بدعوتكم لحضور حفلنا. نتطلع لرؤيتكم!\r\nعدد المدعوين: (guests_count)\r\nرقم الطاولة: (table_number)\r\n\r\nللتأكيد، يرجى زيارة الرابط التالي:\r\n(invitation_link)\r\n\r\nمكان الحفل: \r\n(event_location_link)', NULL),
(25, 'Micah & Dana’s Wedding', 'hfl-zfaf-mayka-w-danh', 'يرجى التحديث من لوحة التحكم', NULL, 'يرجى التحديث من لوحة التحكم', NULL, '2025-10-03 20:00:00', 'Friday, October 3, 2025 08:00 PM', 'Intercontinental Amman Hotel', 'Intercontinental Amman Hotel', 'https://maps.app.goo.gl/ybJnASqPrRr9s5T99?g_st=ipc', 'Micah & Dana’s Wedding', 'Micah & Dana’s Wedding', './uploads/display_event_25_1756797716.jpg', '', 'دعوة حفل زفاف', 'Wedding Invitation', 'يرجى إظهار هذا الرمز عند الدخول', 'Please show this code at entrance', 'wosuol | وصول', 'wosuol | وصول', 'wosuol.com', '', '', '2025-09-02 06:59:12', NULL, NULL, 216, 0, 0, 0, 1, 1, 'full', 1, 0, 1, 'Micah & Dana’s Wedding', '\r\nالسيد (guest_name),\r\n\r\n\r\nنتشرف بدعوتكم لحضور حفلنا في \r\nفندق الانتركونتيننتال عمان \r\n\r\n\r\nعدد المدعوين: (guests_count)\r\n\r\n\r\n\r\nللتأكيد او الاعتذار  يرجى زيارة الرابط التالي قبل 25/9/2025\r\n\r\n(invitation_link)\r\n\r\n\r\n\r\nمكان الحفل: (event_location_link)(invitation_link)', NULL),
(26, 'حفل خطوبة أحمد و زينب', 'hfl-khtwbh-ahmd-w-zynb', 'يرجى التحديث من لوحة التحكم', NULL, 'يرجى التحديث من لوحة التحكم', NULL, '2025-11-01 20:00:00', 'Saturday, November 1, 2025 08:00 PM', 'W AMMAN HOTEL', 'W AMMAN HOTEL', 'https://maps.app.goo.gl/oY6supA7L1J4UCpD6?g_st=ipc', '', '', './uploads/display_event_26_1757065175.png', '', 'دعوة حفل زفاف', 'Wedding Invitation', 'يرجى إظهار هذا الرمز عند الدخول', 'Please show this code at entrance', 'wosuol | وصول', 'wosuol | وصول', 'wosuol.com', '', '', '2025-09-04 20:53:00', NULL, NULL, 2, 0, 1, 1, 1, 1, 'full', 1, 1, 1, 'Ahmed & Zainab Engagement ceremony', NULL, NULL),
(27, 'حفل زفاف حسن و سرى', 'hfl-zfaf-hsn-w-sry', 'يرجى التحديث من لوحة التحكم', NULL, 'يرجى التحديث من لوحة التحكم', NULL, '2025-10-04 20:00:00', 'Saturday, October 4, 2025 08:00 PM', 'The Ritz-Carlton, Amman', 'The Ritz-Carlton, Amman', 'https://maps.app.goo.gl/AaczrrzVeGRev3qYA?g_st=ipc', '', '', './uploads/display_event_27_1757591904.jpg', '', 'دعوة حفل زفاف', 'Wedding Invitation', 'يرجى إظهار هذا الرمز عند الدخول', 'Please show this code at entrance', 'wosuol | وصول', 'wosuol | وصول', 'wosuol.com', 'https://n8n.clouditech-me.com/webhook/confirm-rsvp-qr', 'https://n8n.clouditech-me.com/webhook/wasendesapi-send-invitations', '2025-09-11 11:50:45', NULL, NULL, 93, 0, 0, 0, 1, 1, 'full', 0, 1, 1, 'Hasan & Sura’s Wedding', '', NULL),
(28, 'حفل زفاف حيدر و ليديا', 'hfl-zfaf-hydr-w-lydya', 'يرجى التحديث من لوحة التحكم', NULL, 'يرجى التحديث من لوحة التحكم', NULL, '2025-09-25 20:00:00', 'Thursday, September 25, 2025 08:00 PM', 'White Hall', 'White Hall', 'https://maps.app.goo.gl/FgoFb3wgw1x2krFn9?g_st=ipc', '', '', './uploads/display_event_28_1757592807.jpg', '', 'دعوة حفل زفاف', 'Wedding Invitation', 'يرجى إظهار هذا الرمز عند الدخول', 'Please show this code at entrance', 'wosuol | وصول', 'wosuol | وصول', 'wosuol.com', 'https://n8n.clouditech-me.com/webhook/confirm-rsvp-qr', 'https://n8n.clouditech-me.com/webhook/wasendesapi-send-invitations', '2025-09-11 12:05:36', NULL, NULL, 2, 1, 1, 1, 1, 1, 'full', 1, 1, 1, 'Haidar & Ledya’s Wedding', 'مرحبا (guest_name),\r\n\r\nنتشرف بدعوتكم و مشاركتكم فرحتنا\r\n\r\nعدد المدعوين: (guests_count)\r\n\r\nلتأكيد حضوركم او اعتذاركم، يرجى زيارة الرابط التالي:\r\n\r\n(invitation_link)\r\n\r\nمكان الحفل: \r\n(event_location_link)', NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `event_send_stats`
-- (See below for the actual view)
--
CREATE TABLE `event_send_stats` (
`event_id` int(11)
,`event_name` varchar(255)
,`total_guests` bigint(21)
,`confirmed_guests` decimal(22,0)
,`pending_guests` decimal(22,0)
,`invited_guests` decimal(22,0)
,`last_invitation_time` datetime
,`last_send_success` int(11)
,`last_send_failed` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE `guests` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `guest_id` varchar(10) NOT NULL,
  `name_ar` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `guests_count` int(11) DEFAULT 1,
  `table_number` varchar(50) DEFAULT NULL,
  `assigned_location` varchar(255) DEFAULT NULL,
  `status` enum('pending','confirmed','canceled') NOT NULL DEFAULT 'pending',
  `invitation_sent` tinyint(1) NOT NULL DEFAULT 0,
  `checkin_status` enum('not_checked_in','checked_in') NOT NULL DEFAULT 'not_checked_in',
  `checkin_time` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_invite_sent` datetime DEFAULT NULL,
  `invite_count` int(11) DEFAULT 0,
  `last_invite_status` enum('sent','failed','pending') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `special_needs` varchar(255) DEFAULT NULL,
  `dietary_restrictions` text DEFAULT NULL,
  `last_updated` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'آخر تحديث للضيف',
  `sync_status` enum('synced','pending_sync','conflict') DEFAULT 'synced' COMMENT 'حالة المزامنة للعمليات غير المتصلة'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guests`
--

INSERT INTO `guests` (`id`, `event_id`, `guest_id`, `name_ar`, `name_en`, `phone_number`, `guests_count`, `table_number`, `assigned_location`, `status`, `invitation_sent`, `checkin_status`, `checkin_time`, `created_at`, `last_invite_sent`, `invite_count`, `last_invite_status`, `notes`, `special_needs`, `dietary_restrictions`, `last_updated`, `sync_status`) VALUES
(2365, 13, 'd5a9', 'السيد عمار الحسني', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-23 18:12:27', NULL, 0, 'pending', '', NULL, NULL, '2025-08-29 09:11:43', 'synced'),
(2382, 13, '7a16', 'زينب الخفاف', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-27 16:22:06', NULL, 0, 'pending', '', NULL, NULL, '2025-08-29 09:09:20', 'synced'),
(2383, 13, '950c', 'بشار النقيب', NULL, '', 4, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-27 17:01:23', NULL, 0, 'pending', '', NULL, NULL, '2025-08-29 09:11:28', 'synced'),
(2384, 13, '5cdf', 'ايمان الخشالي', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-27 20:03:36', NULL, 0, 'pending', '', NULL, NULL, '2025-08-30 15:33:04', 'synced'),
(2386, 13, '57bf', 'شوان سالار', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-28 09:46:09', NULL, 0, 'pending', '', NULL, NULL, '2025-08-29 09:09:42', 'synced'),
(2502, 13, '7b88', 'هبة العزاوي', NULL, '', 1, NULL, NULL, 'canceled', 0, 'not_checked_in', NULL, '2025-08-29 08:49:16', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-29 08:49:16', 'synced'),
(2503, 13, '536f', 'وسام سرمد السعدي', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 08:55:36', NULL, 0, 'pending', '', NULL, NULL, '2025-08-29 09:08:28', 'synced'),
(2504, 13, '8206', 'Marwa 👸', NULL, '', 1, NULL, NULL, 'canceled', 0, 'not_checked_in', NULL, '2025-08-29 10:07:03', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-29 10:07:03', 'synced'),
(2505, 13, 'ca77', 'زينب النقشبندي', NULL, '', 3, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 10:29:11', NULL, 0, 'pending', '', NULL, NULL, '2025-09-07 13:00:53', 'synced'),
(2506, 13, '6bbb', 'ميادة ليث الشيخ قادر', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 10:32:27', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-29 10:32:27', 'synced'),
(2507, 13, 'ddde', 'ورقاء الحاج حمود', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 10:32:55', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-29 10:32:55', 'synced'),
(2508, 13, '834c', 'رلى الدباغ', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 10:35:11', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-29 10:35:11', 'synced'),
(2509, 13, 'adbe', 'جهان الزهاوي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 10:35:25', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-29 10:35:25', 'synced'),
(2510, 13, '64ad', 'رغدة رافع', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 10:40:15', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-29 10:40:15', 'synced'),
(2511, 13, '9e98', 'ندى السوز', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 10:44:48', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-29 10:44:48', 'synced'),
(2512, 13, '60d1', 'بيريفان  النقيب', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 10:46:46', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-29 10:46:46', 'synced'),
(2513, 13, '5c2f', 'مروان خليل العزاوي', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 11:00:48', NULL, 0, 'pending', '', NULL, NULL, '2025-09-07 13:03:12', 'synced'),
(2514, 13, '8b66', 'ابراهيم خليل العزاوي', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 11:03:36', NULL, 0, 'pending', '', NULL, NULL, '2025-08-29 14:33:23', 'synced'),
(2516, 13, '2f82', 'احرار طبرة', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 11:13:49', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-29 11:13:49', 'synced'),
(2517, 13, '03d4', 'هدير مرزه', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 11:17:45', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-29 11:17:45', 'synced'),
(2518, 13, '14cf', 'سحر البلداوي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 11:24:58', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-29 11:24:58', 'synced'),
(2519, 13, 'a41d', 'زينب عادل', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 11:25:36', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-29 11:25:36', 'synced'),
(2520, 13, 'f4f7', 'سناء إبراهيم', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 11:26:06', NULL, 0, 'pending', '', NULL, NULL, '2025-08-31 16:13:47', 'synced'),
(2521, 13, 'bbbe', 'رحاب جمال', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 11:43:57', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-29 11:43:57', 'synced'),
(2522, 13, '44d7', 'غيد غانم عبد الجليل', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 11:49:46', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-29 11:49:46', 'synced'),
(2523, 13, '3e70', 'ليلى جودي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 12:01:05', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-29 12:01:05', 'synced'),
(2524, 13, 'bd4d', 'بان القاضي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 12:02:00', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-29 12:02:00', 'synced'),
(2526, 13, '3232', 'نسرين الخوري ', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 13:17:45', NULL, 0, 'pending', '', NULL, NULL, '2025-08-29 14:40:35', 'synced'),
(2527, 13, '8f42', 'ايناس حمرة', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 13:30:22', NULL, 0, 'pending', '', NULL, NULL, '2025-08-29 14:33:41', 'synced'),
(2528, 13, '7c23', 'رجاء المهداوي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 14:38:22', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-29 14:38:22', 'synced'),
(2529, 13, '97a7', 'نورهان الخوري', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 14:39:01', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-29 14:39:01', 'synced'),
(2530, 13, '340a', 'نضال زهير', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 15:06:35', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-29 15:06:35', 'synced'),
(2531, 13, '08dc', 'حيدر الملا', NULL, '', 4, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 16:32:14', NULL, 0, 'pending', '', NULL, NULL, '2025-08-29 22:04:53', 'synced'),
(2532, 13, '06a9', 'نصر الغريري وحرمه', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 18:22:30', NULL, 0, 'pending', '', NULL, NULL, '2025-08-29 19:42:27', 'synced'),
(2533, 13, '8c03', 'ايسر الحسني', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 18:37:23', NULL, 0, 'pending', '', NULL, NULL, '2025-08-29 22:04:25', 'synced'),
(2534, 13, '1248', 'عبدالله الصجري', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 21:56:53', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-29 21:56:53', 'synced'),
(2535, 13, '16de', 'ميلاد بولص', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 21:57:03', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-29 21:57:03', 'synced'),
(2536, 13, '9cdc', 'محمد الخطاوي', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-29 22:12:04', NULL, 0, 'pending', '', NULL, NULL, '2025-08-29 22:23:10', 'synced'),
(2537, 13, 'f2b3', 'امين النعيمي', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-30 02:12:23', NULL, 0, 'pending', '', NULL, NULL, '2025-08-30 05:43:18', 'synced'),
(2538, 13, '5235', 'ايمان البياع', NULL, '', 1, NULL, NULL, 'canceled', 0, 'not_checked_in', NULL, '2025-08-30 04:02:07', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 12:44:30', 'synced'),
(2539, 13, '841d', 'بشار طبرة', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-30 06:59:02', NULL, 0, 'pending', '', NULL, NULL, '2025-08-30 13:01:05', 'synced'),
(2540, 13, '8087', 'عمار الفتلاوي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-30 07:26:11', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-30 07:26:11', 'synced'),
(2541, 13, '6c24', 'طه عبد الجبار الكبيسي', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-30 07:38:52', NULL, 0, 'pending', '', NULL, NULL, '2025-08-31 16:14:08', 'synced'),
(2542, 13, '3c69', 'نعمة الدليمي', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-30 07:52:25', NULL, 0, 'pending', '', NULL, NULL, '2025-08-30 13:06:37', 'synced'),
(2543, 13, '0727', 'دار د. قيس ابراهيم العبدالله ود.سهيله طالب حمدي الكبالي', NULL, '', 1, NULL, NULL, 'canceled', 0, 'not_checked_in', NULL, '2025-08-30 07:53:26', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-30 07:53:26', 'synced'),
(2544, 13, 'ea15', 'ليث الامير', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-30 08:42:48', NULL, 0, 'pending', '', NULL, NULL, '2025-08-30 13:03:14', 'synced'),
(2545, 13, '4f02', 'ديما انمار صبري', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-30 09:16:20', NULL, 0, 'pending', '', NULL, NULL, '2025-08-31 16:12:22', 'synced'),
(2546, 13, '543d', 'نمر نعمان', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-30 09:31:25', NULL, 0, 'pending', '', NULL, NULL, '2025-08-30 13:06:06', 'synced'),
(2547, 13, '7b90', 'زينة الطائي', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-30 09:43:51', NULL, 0, 'pending', '', NULL, NULL, '2025-08-30 13:05:26', 'synced'),
(2548, 13, '9901', 'حمدون العاني', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-30 10:14:08', NULL, 0, 'pending', '', NULL, NULL, '2025-08-30 13:01:47', 'synced'),
(2549, 13, 'db4f', 'هديل ابراهيم المهداوي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-30 12:15:32', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-30 12:15:32', 'synced'),
(2550, 13, '4deb', 'Hassan Al Rawi', NULL, '', 1, NULL, NULL, 'canceled', 0, 'not_checked_in', NULL, '2025-08-30 12:18:30', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-30 12:18:30', 'synced'),
(2551, 13, '4f52', 'صبيح القشطيني', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-30 13:11:51', NULL, 0, 'pending', '', NULL, NULL, '2025-08-30 15:29:44', 'synced'),
(2552, 13, '35d3', 'سراء الأنصاري', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-30 14:42:50', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-30 14:42:50', 'synced'),
(2553, 13, '013a', 'عبدالعزيز الكبيسي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-30 15:11:44', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-30 15:11:44', 'synced'),
(2554, 13, '5d59', 'غيث الكبيسي', NULL, '0787777796', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-30 15:19:21', NULL, 0, 'pending', '', NULL, NULL, '2025-09-03 19:54:54', 'synced'),
(2555, 13, 'f74f', 'عبدالوارث الكبيسي', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-30 16:15:43', NULL, 0, 'pending', '', NULL, NULL, '2025-08-31 16:14:36', 'synced'),
(2556, 13, 'ebc5', 'عبدالجبار الكبيسي', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-30 16:26:49', NULL, 0, 'pending', '', NULL, NULL, '2025-08-31 16:14:24', 'synced'),
(2557, 13, 'fe99', 'بسام بلولة', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-30 16:38:05', NULL, 0, 'pending', '', NULL, NULL, '2025-08-31 16:12:09', 'synced'),
(2558, 13, '1276', 'Layth Alkhafaji', NULL, '', 1, NULL, NULL, 'canceled', 0, 'not_checked_in', NULL, '2025-08-30 17:15:36', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-30 17:15:36', 'synced'),
(2559, 13, 'eed4', 'ريا العطار', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-30 17:27:56', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-30 17:27:56', 'synced'),
(2560, 13, '900c', 'فيصل الخالدي', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-30 17:40:25', NULL, 0, 'pending', '', NULL, NULL, '2025-08-31 16:12:37', 'synced'),
(2561, 13, '4cbf', 'محمد وارث الكبيسي', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-30 19:17:55', NULL, 0, 'pending', '', NULL, NULL, '2025-09-02 11:11:43', 'synced'),
(2562, 13, '5b71', 'رلى ومهند', NULL, '', 1, NULL, NULL, 'canceled', 0, 'not_checked_in', NULL, '2025-08-30 19:22:15', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-30 19:22:15', 'synced'),
(2563, 13, '4c08', 'ليث الكبيسي', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-30 21:03:42', NULL, 0, 'pending', '', NULL, NULL, '2025-08-31 16:14:58', 'synced'),
(2564, 13, '3610', 'محمد اكرام', NULL, '', 1, NULL, NULL, 'canceled', 0, 'not_checked_in', NULL, '2025-08-30 21:09:34', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-30 21:09:34', 'synced'),
(2565, 13, 'a2d6', 'محمد الياسين', NULL, '', 1, NULL, NULL, 'canceled', 0, 'not_checked_in', NULL, '2025-08-30 23:58:28', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-30 23:58:28', 'synced'),
(2566, 13, '9960', 'تارا  وضاح الطائي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-31 06:13:23', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-31 06:13:23', 'synced'),
(2567, 13, '1d55', 'دانيه وضاح الطائي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-31 06:14:15', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-31 06:14:15', 'synced'),
(2568, 13, 'f568', 'تمارا وضاح الطائي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-31 06:14:59', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-31 06:14:59', 'synced'),
(2569, 13, '2c90', 'عمر الخزرجي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-31 09:59:57', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-31 09:59:57', 'synced'),
(2570, 13, 'ae3b', 'ابراهيم الاعظمي', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-31 10:55:34', NULL, 0, 'pending', '', NULL, NULL, '2025-08-31 16:12:51', 'synced'),
(2571, 13, '0877', 'محمد صداع ', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-31 13:12:50', NULL, 0, 'pending', '', NULL, NULL, '2025-08-31 16:15:13', 'synced'),
(2572, 13, '67ca', 'اوشي', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-31 13:23:29', NULL, 0, 'pending', '', NULL, NULL, '2025-08-31 22:48:24', 'synced'),
(2573, 13, '0a57', 'احمد الكناني', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-31 16:10:59', NULL, 0, 'pending', '', NULL, NULL, '2025-08-31 16:13:02', 'synced'),
(2574, 13, '27bb', 'ثابت سبوبة', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-08-31 16:15:26', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-31 16:15:26', 'synced'),
(2575, 13, '5ced', 'تانيا القره غلي', NULL, '', 1, NULL, NULL, 'canceled', 0, 'not_checked_in', NULL, '2025-08-31 17:16:04', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-31 17:16:04', 'synced'),
(2576, 13, 'e510', 'خليل الدليمي', NULL, '', 3, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-08-31 17:25:59', NULL, 0, 'pending', '', NULL, NULL, '2025-09-16 11:50:34', 'synced'),
(2577, 13, '5514', 'Yousif Shukri', NULL, '', 1, NULL, NULL, 'canceled', 0, 'not_checked_in', NULL, '2025-08-31 17:26:54', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-31 17:26:54', 'synced'),
(2578, 13, '3795', 'محمد الحسيني', NULL, '', 1, NULL, NULL, 'canceled', 0, 'not_checked_in', NULL, '2025-08-31 19:47:11', NULL, 0, 'pending', NULL, NULL, NULL, '2025-08-31 19:47:11', 'synced'),
(2746, 13, 'ecf1', 'ايات الجدوع', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-01 06:04:17', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-01 06:04:17', 'synced'),
(2747, 13, '2f32', 'ناياب طاهر دباغ', NULL, '', 1, NULL, NULL, 'canceled', 0, 'not_checked_in', NULL, '2025-09-01 07:36:52', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-01 07:36:52', 'synced'),
(2749, 13, 'ff78', 'عدي قصي الشكرجي', NULL, '', 1, NULL, NULL, 'canceled', 0, 'not_checked_in', NULL, '2025-09-01 13:35:44', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-01 13:35:44', 'synced'),
(2753, 13, 'ef80', 'إبراهيم سعيد العبيدي', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-01 16:16:20', NULL, 0, 'pending', '', NULL, NULL, '2025-09-02 11:10:21', 'synced'),
(2755, 13, '188d', 'ريم احسان سامي', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-01 16:40:01', NULL, 0, 'pending', '', NULL, NULL, '2025-09-07 12:59:50', 'synced'),
(2759, 13, 'acd0', 'نهلة الراوي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-01 18:02:18', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-01 18:02:18', 'synced'),
(2760, 13, '64a3', 'صبري ممتاز جرمكلي', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-01 18:27:21', NULL, 0, 'pending', '', NULL, NULL, '2025-09-02 11:10:37', 'synced'),
(2761, 13, 'eaae', 'رغد الطائي', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-01 19:26:41', NULL, 0, 'pending', '', NULL, NULL, '2025-09-02 11:10:10', 'synced'),
(2772, 13, '19eb', 'بسمة بهاء بهيج', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-02 07:43:31', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-02 07:43:31', 'synced'),
(2773, 13, '18ef', 'جمال الخالدي', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-02 11:09:40', NULL, 0, 'pending', '', NULL, NULL, '2025-09-02 11:11:03', 'synced'),
(2801, 13, '73e9', 'مهدي صلاح الدلال', NULL, '', 3, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-02 20:49:41', NULL, 0, 'pending', '', NULL, NULL, '2025-09-03 06:05:55', 'synced'),
(2803, 13, 'd37d', 'منار النعيمي', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-03 13:06:01', NULL, 0, 'pending', '', NULL, NULL, '2025-09-04 12:10:07', 'synced'),
(2804, 13, '1ed0', 'ابراهيم خالد البطاوي', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-03 16:51:21', NULL, 0, 'pending', '', NULL, NULL, '2025-09-03 19:01:47', 'synced'),
(2812, 13, '249d', 'محي الدين الناصري', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-03 20:03:45', NULL, 0, 'pending', '', NULL, NULL, '2025-09-04 07:52:40', 'synced'),
(2816, 13, '303c', 'داليا كوثر عدنان', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-03 23:52:29', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-03 23:52:29', 'synced'),
(2819, 13, 'b9f9', 'حمود اياد الكبيسي', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-04 07:54:59', NULL, 0, 'pending', '', NULL, NULL, '2025-09-04 08:23:55', 'synced'),
(2820, 13, '8f9b', 'ناصر حسين الجنابي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-04 08:13:31', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-04 08:13:31', 'synced'),
(2821, 13, '88f0', 'مصطفى عبدالجبار الكبيسي', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-04 08:35:30', NULL, 0, 'pending', '', NULL, NULL, '2025-09-04 08:37:23', 'synced'),
(2822, 13, 'bc19', 'عمر اسعد التكريتي', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-04 08:41:36', NULL, 0, 'pending', '', NULL, NULL, '2025-09-04 08:57:12', 'synced'),
(2823, 13, '94fe', 'مصطفى شفيق السعيدي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-04 12:26:15', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-04 12:26:15', 'synced'),
(2824, 13, 'b05c', 'سديم نجم الدين الجنابي', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-04 12:40:25', NULL, 0, 'pending', '', NULL, NULL, '2025-09-07 13:00:20', 'synced'),
(2825, 13, '99f9', 'عبدالله زهير الكبيسي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-04 12:46:50', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-04 12:46:50', 'synced'),
(2826, 13, '7ebd', 'علي الحمود', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-04 13:30:01', NULL, 0, 'pending', '', NULL, NULL, '2025-09-05 07:35:16', 'synced'),
(2827, 13, '7a58', 'علاء حسين كبه', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-04 13:51:57', NULL, 0, 'pending', '', NULL, NULL, '2025-09-04 18:27:45', 'synced'),
(2828, 13, 'f278', 'محمد خالد الكبيسي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-04 15:27:37', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-04 15:27:37', 'synced'),
(2829, 13, 'e482', 'ابراهيم فؤاد الصافي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-04 16:52:37', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-04 16:52:37', 'synced'),
(2830, 13, '40a1', 'مصطفى محمد البلداوي', NULL, '', 1, NULL, NULL, 'canceled', 0, 'not_checked_in', NULL, '2025-09-04 20:46:26', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-04 20:46:26', 'synced'),
(2836, 13, 'e3e3', 'جنان هاشم الخطاط', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-05 13:31:50', NULL, 0, 'pending', '', NULL, NULL, '2025-09-07 12:58:48', 'synced'),
(2837, 13, 'ffbb', 'بسمة سعدون الفياض', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-05 14:11:57', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-05 14:11:57', 'synced'),
(2839, 13, 'fa54', 'زهراء علي النصراوي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-05 14:42:36', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-05 14:42:36', 'synced'),
(2840, 13, '1aff', 'ام ليث الكبيسي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-05 15:05:40', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-05 15:05:40', 'synced'),
(2841, 13, 'ed41', 'علي خالد العزاوي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-05 20:10:52', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-05 20:10:52', 'synced'),
(2842, 13, '6c74', 'سيف سرمد الحمامي', NULL, '', 1, NULL, NULL, 'canceled', 0, 'not_checked_in', NULL, '2025-09-05 20:35:07', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-05 20:35:07', 'synced'),
(2847, 13, '9d3a', 'محمد رياض الحجاوي', NULL, '', 1, NULL, NULL, 'canceled', 0, 'not_checked_in', NULL, '2025-09-06 12:47:19', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-06 12:47:19', 'synced'),
(2856, 13, '272a', 'محمد نبيل العبيدي', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-06 18:51:22', NULL, 0, 'pending', '', NULL, NULL, '2025-09-06 19:26:56', 'synced'),
(2857, 13, 'fdac', 'حيدر علي العبيدي', NULL, '', 1, NULL, NULL, 'canceled', 0, 'not_checked_in', NULL, '2025-09-06 19:06:46', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-06 19:06:46', 'synced'),
(2858, 13, '24db', 'حيدر نزار الخوجة', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-06 20:55:36', NULL, 0, 'pending', '', NULL, NULL, '2025-09-06 20:57:44', 'synced'),
(2859, 13, 'a6d1', 'عمر يعرب غيدان', NULL, '', 3, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-06 20:56:04', NULL, 0, 'pending', '', NULL, NULL, '2025-09-06 21:12:37', 'synced'),
(2860, 13, 'd749', 'محمد صلاح العاني', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-07 12:32:46', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-07 12:32:46', 'synced'),
(2861, 13, '4059', 'مروة خليل العزاوي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-07 12:58:37', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-07 12:58:37', 'synced'),
(2862, 13, '999a', 'شهد محمد حلمي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-07 12:59:41', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-07 12:59:41', 'synced'),
(2865, 13, 'd6dd', 'غادة فخري الفارسي', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-08 21:42:15', NULL, 0, 'pending', '', NULL, NULL, '2025-09-09 15:56:25', 'synced'),
(2866, 13, 'a3c1', 'هديل همام الالوسي', NULL, '', 1, NULL, NULL, 'canceled', 0, 'not_checked_in', NULL, '2025-09-09 09:38:17', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-09 09:38:17', 'synced'),
(2867, 13, '8886', 'مصطفى جمال الخالدي', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-09 12:32:53', NULL, 0, 'pending', '', NULL, NULL, '2025-09-10 13:36:08', 'synced'),
(2868, 26, 'a74f', 'الانسة زينب المرسومي المحترمة', NULL, '+962790057850', 2, '', 'أهل العروس', 'pending', 0, 'not_checked_in', NULL, '2025-09-09 15:12:21', NULL, 0, 'pending', '', NULL, NULL, '2025-09-09 15:26:35', 'synced'),
(2869, 13, '4474', 'فاطمة عماد العبيدي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-09 15:52:57', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-09 15:52:57', 'synced'),
(2871, 13, 'cf26', 'رافت سعد الحمداني', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-09 19:37:21', NULL, 0, 'pending', '', NULL, NULL, '2025-09-10 07:30:47', 'synced'),
(2874, 13, '50ff', 'سيما ناجي الهاشمي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-10 16:27:30', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-10 16:27:30', 'synced'),
(2875, 13, 'ac93', 'دينا زيد محمد', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-10 16:46:46', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-10 16:46:46', 'synced'),
(2878, 13, '54f5', 'بشار عامر الصالح', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-11 10:07:17', NULL, 0, 'pending', '', NULL, NULL, '2025-09-11 15:14:20', 'synced'),
(2887, 28, '0835', 'الانسة ليديا الشمري المحترمة', NULL, '+962791331234', 2, '4', 'أهل العروس', 'pending', 0, 'not_checked_in', NULL, '2025-09-11 17:29:19', NULL, 0, 'pending', '', NULL, NULL, '2025-09-11 18:29:45', 'synced'),
(3077, 13, '2947', 'حسن مطر وعقيلته', NULL, '', 1, NULL, NULL, 'canceled', 0, 'not_checked_in', NULL, '2025-09-12 02:15:08', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 02:15:08', 'synced'),
(3090, 25, 'cb06', 'سلمان جبجي والعائلته ', NULL, '795279075', 4, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-14 10:42:04', '2025-09-12 10:43:26', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 10:42:04', 'synced'),
(3091, 25, '8f72', 'شادي جبجي والعائلة', NULL, '795051890', 4, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-14 12:21:29', '2025-09-12 10:46:13', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 12:21:29', 'synced'),
(3092, 25, 'c1f5', 'يوسف نصار المحترم', NULL, '795609915', 2, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-14 09:30:36', '2025-09-12 10:47:22', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 09:30:36', 'synced'),
(3093, 25, '1c5b', 'السيد رامي عصفور ', NULL, '795988502', 2, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-15 23:44:15', '2025-09-12 10:49:10', NULL, 0, 'pending', '', NULL, NULL, '2025-09-15 23:44:15', 'synced'),
(3094, 25, 'a64e', 'السيد بير منصور ', NULL, '+96895411616', 1, '', '', 'canceled', 0, 'not_checked_in', NULL, '2025-09-12 10:52:25', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 17:17:31', 'synced'),
(3101, 27, '641d', 'نعمان عمر الراوي', NULL, '', 1, '', 'أهل العريس', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 11:34:10', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 15:57:56', 'synced'),
(3102, 27, 'f403', 'موج قاسم الراوي', NULL, '0799050784', 1, '', 'أهل العروس', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 11:42:42', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 15:57:47', 'synced'),
(3103, 27, 'a0e8', 'ديمة فلاح حسن', NULL, '0791213677', 1, '', 'أهل العريس', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 11:45:20', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 15:57:31', 'synced'),
(3105, 27, '866b', 'ياسمين أحمد الشلال', NULL, '0792596660', 1, '', 'أهل العريس', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 12:00:46', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 15:58:01', 'synced'),
(3111, 27, '714d', 'شهد فلاح حسن', NULL, '0791213560', 1, '', 'أهل العريس', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 12:32:37', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 15:57:39', 'synced'),
(3112, 27, 'cd4a', 'Shatha Jameel Alassil ( mother of the groom )', NULL, '0797812040', 1, '', 'أهل العريس', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 12:38:03', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 15:57:22', 'synced'),
(3113, 27, '538b', 'Yasmeen Omar Alrawi ( sister of the groom )', NULL, '0797054462', 1, '', 'أهل العريس', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 12:38:36', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 15:57:10', 'synced'),
(3116, 20, '55b4', 'محمد رياض الحجاوي', NULL, '+962798797794', 5, NULL, NULL, 'pending', 0, 'not_checked_in', NULL, '2025-09-12 13:09:49', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-14 12:53:51', 'synced'),
(3117, 25, 'c79d', 'بيتر منصور والعائلة', NULL, '795594664', 3, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 14:30:22', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 14:30:22', 'synced'),
(3118, 25, 'e439', 'بديعة بطرس منصور', NULL, '797023980', 1, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 14:33:15', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 14:33:15', 'synced'),
(3119, 25, 'c8f3', 'نادر الياس الرفيدي', NULL, '797777955', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 14:34:51', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 14:34:51', 'synced'),
(3120, 25, '61c4', ' السيدة اليس الرفيدي منصور', NULL, '797616307', 2, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-14 17:24:48', '2025-09-12 14:36:51', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 17:24:48', 'synced'),
(3121, 25, 'b10f', 'وليد بوشيه وعائلته', NULL, '796031286', 2, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-14 16:09:14', '2025-09-12 14:39:09', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 16:09:14', 'synced'),
(3122, 25, 'a824', 'السيد ستيف عنز', NULL, '797136157', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 14:40:30', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 14:40:30', 'synced'),
(3126, 25, '8a6f', 'ابراهيم وهاب والعائلة', NULL, '795104870', 7, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 15:10:49', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 15:10:49', 'synced'),
(3127, 25, 'fcd3', 'خليل وهاب والعائلة', NULL, '796345888', 6, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 15:18:27', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 15:18:27', 'synced'),
(3129, 25, '86a2', 'سميره فهد وهاب', NULL, '798709385', 6, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 15:31:44', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 17:41:48', 'synced'),
(3130, 25, '8fd9', 'السيدة روبيكا وهاب والعائلة', NULL, '+972509519161', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 15:35:03', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 15:35:03', 'synced'),
(3131, 25, 'd0bc', 'السيد عماد العبو', NULL, '+972507519161', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 15:36:19', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 15:36:19', 'synced'),
(3132, 25, '59fd', 'السيد داوود العبو وعائلته', NULL, '+972503025030', 4, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 15:37:28', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 15:37:28', 'synced'),
(3133, 25, 'a309', 'السيد سهيل عناب وعائلته', NULL, '795043030', 3, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-14 09:49:01', '2025-09-12 15:39:30', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 09:49:01', 'synced'),
(3134, 25, '87ed', 'السيد جوزيف ثيودوري وعائلته', NULL, '+16619989748', 5, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 15:41:36', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 15:41:36', 'synced'),
(3135, 25, '0785', 'السيد عيسى وهاب والعائلة', NULL, '+19054662603', 6, '', '', 'canceled', 0, 'not_checked_in', NULL, '2025-09-12 15:43:15', NULL, 0, 'pending', '', NULL, NULL, '2025-09-15 01:59:59', 'synced'),
(3136, 25, 'e0cd', 'السيد شارلي وهاب وعائلته', NULL, '795556828', 4, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 15:44:33', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 15:44:33', 'synced'),
(3137, 25, 'a1b0', 'السيد بسام وهاب', NULL, '796124624', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 15:46:14', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 15:46:14', 'synced'),
(3138, 25, 'a368', 'السيد خضر غاوي', NULL, '795294303', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 15:46:56', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 06:30:38', 'synced'),
(3139, 25, 'd0af', 'السيد هشام منصور', NULL, '+971562475585', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 15:47:44', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 12:53:04', 'synced'),
(3140, 25, '8365', 'السيد وسام منصور', NULL, '+41788663662', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 15:48:25', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 12:57:13', 'synced'),
(3141, 25, '9e0a', 'السيدة ألا منصور', NULL, '795567416', 3, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-14 10:24:47', '2025-09-12 15:50:11', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 10:24:47', 'synced'),
(3142, 25, 'c7ad', 'السيد وليم حنانيا', NULL, '+97336561151', 2, '', '', 'canceled', 0, 'not_checked_in', NULL, '2025-09-12 15:51:31', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 17:25:54', 'synced'),
(3143, 25, 'e16b', 'السيد هاني حنانيا', NULL, '797250250', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 15:52:07', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 15:52:07', 'synced'),
(3144, 25, '2e8c', 'سامي عطالله وهاب', NULL, '+15148626284', 2, '', '', 'canceled', 0, 'not_checked_in', NULL, '2025-09-12 15:54:19', NULL, 0, 'pending', '', NULL, NULL, '2025-09-15 07:26:08', 'synced'),
(3145, 25, 'ab34', 'السيدة الين وهاب', NULL, '795932554', 1, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 15:54:48', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 07:16:11', 'synced'),
(3146, 25, 'a95e', 'السيدة جلاديس منصور', NULL, '', 1, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 15:55:34', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 15:55:34', 'synced'),
(3148, 25, '5e71', 'السيد نبيل منصور', NULL, '+972523216389', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 15:56:57', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 15:56:57', 'synced'),
(3150, 25, '8f94', 'السيدة  لبيبه قهوجي', NULL, '799733756', 1, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-14 15:01:11', '2025-09-12 15:58:29', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 15:01:11', 'synced'),
(3151, 25, '307a', 'السيد رامي قهوجي', NULL, '', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 15:58:52', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 15:58:52', 'synced'),
(3153, 25, 'b0c6', 'الدكتور نصر الذيابات', NULL, '796453000', 2, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-14 16:18:51', '2025-09-12 16:01:13', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 16:18:51', 'synced'),
(3154, 25, '7e9f', 'السيد حنا دحمس', NULL, '795534468', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 16:02:11', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 16:02:11', 'synced'),
(3156, 25, 'e75a', 'فريد يوسف نصار', NULL, '+4915773701648', 2, '', '', 'canceled', 0, 'not_checked_in', NULL, '2025-09-12 17:18:14', NULL, 0, 'pending', '', NULL, NULL, '2025-09-15 12:46:34', 'synced'),
(3157, 25, 'b789', 'رائد يوسف نصار', NULL, '+19173328007', 1, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 17:28:18', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 17:28:18', 'synced'),
(3158, 25, '807d', 'السيد انطون وهاب', NULL, '799889534', 2, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-14 11:15:03', '2025-09-12 17:29:22', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 11:15:03', 'synced'),
(3159, 25, 'a680', 'انطون حنانيا', NULL, '', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 17:29:41', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 17:29:41', 'synced'),
(3160, 25, '2635', 'السيد رائد الزغل', NULL, '792000097', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 17:30:50', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 17:30:50', 'synced'),
(3161, 25, '2b80', 'السيد سليمان صمادي', NULL, '796000389', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 17:32:07', NULL, 0, 'pending', '', NULL, NULL, '2025-09-15 07:04:22', 'synced'),
(3163, 25, '4127', 'السيد سلمان فرح منيزل وعائلته ', NULL, '795279075', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 17:36:19', NULL, 0, 'pending', '', NULL, NULL, '2025-09-16 06:41:25', 'synced'),
(3164, 25, '2c6e', 'السيد منير بيوك', NULL, '796532777', 2, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-14 14:13:21', '2025-09-12 17:36:50', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 14:13:21', 'synced'),
(3165, 25, '9b85', 'السيد عصام فرجالله', NULL, '795925215', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 17:38:08', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 17:38:08', 'synced'),
(3166, 25, '6ecd', 'السيد دانيال عبد المسيح', NULL, '795588077', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 17:39:23', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 17:39:23', 'synced'),
(3167, 25, '54eb', 'جيف عبد المسيح', NULL, '795959666', 1, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 17:40:16', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 17:40:16', 'synced'),
(3168, 25, 'ba90', 'السيد وائل كركر ', NULL, '795416756', 2, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-14 17:36:03', '2025-09-12 17:42:06', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 17:36:03', 'synced'),
(3169, 25, '804b', 'داوود الدلو ', NULL, '795551540', 2, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-14 17:25:21', '2025-09-12 17:42:23', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 17:25:21', 'synced'),
(3170, 25, '84ad', 'السيد سينار فلاحات', NULL, '799832205', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 17:42:42', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 09:59:18', 'synced'),
(3171, 25, 'b9f7', 'السيد زياد الزين ', NULL, '795822122', 2, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-14 15:47:57', '2025-09-12 17:43:14', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 15:47:57', 'synced'),
(3172, 25, 'e586', 'السيد نيبال فريحات', NULL, '796600089', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 17:43:33', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 10:56:08', 'synced'),
(3173, 25, 'a357', 'صاحبة فادية 3', NULL, '', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 17:43:53', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 17:43:53', 'synced'),
(3174, 25, '06db', 'صاحبة فادية 4', NULL, '', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 17:44:21', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 17:44:21', 'synced'),
(3175, 27, 'b838', 'نوار تغلب القشطيني', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 17:44:24', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 17:44:24', 'synced'),
(3176, 25, '1970', 'صاحبة فادية 5', NULL, '', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 17:45:12', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 17:45:12', 'synced'),
(3178, 25, '4cae', 'اجانب', NULL, '', 15, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 17:46:47', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 17:46:47', 'synced'),
(3180, 25, 'feb4', 'عصام سامي جبجي', NULL, '', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 17:47:51', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 17:47:51', 'synced'),
(3181, 25, 'a956', 'السيدة لليان باسيلي', NULL, '+201223110109', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 17:48:24', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 10:48:54', 'synced'),
(3182, 25, '1827', 'السيدة ام ابراهيم', NULL, '797289180', 1, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 17:49:51', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 09:41:32', 'synced'),
(3183, 25, 'c32e', 'ام عصام', NULL, '795366645', 1, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 17:50:38', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 17:51:15', 'synced'),
(3184, 25, 'bc04', 'ماهر سامي جبجي', NULL, '795654554', 4, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 17:51:49', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 17:51:49', 'synced'),
(3185, 27, 'c62e', 'رنده سمير مشربش', NULL, '0795910333', 1, '', 'أهل العريس', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 17:52:41', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 17:58:04', 'synced'),
(3186, 25, '7b92', 'السيد رجائي فروجي', NULL, '795599323', 2, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-15 06:43:28', '2025-09-12 17:53:22', NULL, 0, 'pending', '', NULL, NULL, '2025-09-15 06:43:28', 'synced'),
(3187, 25, '3b72', 'السيد هاني ربضي', NULL, '795712319', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 17:55:05', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 17:55:05', 'synced'),
(3188, 25, '0974', 'المهندس يوسف مدينات', NULL, '795554558', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 17:56:20', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 17:56:20', 'synced'),
(3189, 25, 'd1e2', 'الياس هاني الربضي', NULL, '795366657', 2, '', '', 'canceled', 0, 'not_checked_in', NULL, '2025-09-12 17:57:29', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 17:58:40', 'synced'),
(3190, 25, 'efcb', 'السيد عيسى البله', NULL, '795631216', 2, '', '', 'canceled', 0, 'not_checked_in', NULL, '2025-09-12 17:58:53', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 15:17:46', 'synced'),
(3191, 25, '71a9', 'السيد سامر البله', NULL, '795666033', 4, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-12 17:59:37', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 17:59:37', 'synced'),
(3194, 27, 'dd7b', 'د انوار ساجد الراوي', NULL, '0796615990', 1, '', 'أهل العريس', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 18:26:59', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 18:32:38', 'synced'),
(3195, 27, '832b', 'ملاذ جوير السعدي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 18:34:38', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 18:34:38', 'synced'),
(3196, 27, '1fd2', 'ملاذ جوير السعدي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 18:34:56', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 18:34:56', 'synced'),
(3197, 27, '806b', 'زينه محمد الجبوري', NULL, '0799852001', 1, '', 'أهل العريس', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 18:50:33', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 19:10:25', 'synced'),
(3198, 27, 'a2d1', 'الاء راضي الرفاعي', NULL, '+1 (253)333-4304', 1, '', 'أهل العريس', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 18:52:00', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 19:09:25', 'synced'),
(3199, 27, '60e9', 'هنوف خالد العاني', NULL, '', 1, '', 'أهل العريس', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 19:05:12', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 19:08:27', 'synced'),
(3200, 27, '97e5', 'خلود رياض الراوي', NULL, '0770000345', 5, '', 'أهل العريس', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 19:05:29', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 19:09:54', 'synced'),
(3201, 27, '50b8', 'ندى سنان الراوي', NULL, '', 4, '', 'أهل العريس', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 19:12:04', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 19:23:53', 'synced'),
(3202, 27, '7740', 'نور خاشع الراوي', NULL, '', 1, '', 'أهل العريس', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 19:17:56', NULL, 0, 'pending', '', NULL, NULL, '2025-09-12 19:23:41', 'synced'),
(3203, 27, '96f8', 'دانيه محمد فاضل', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 19:29:32', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 19:29:32', 'synced'),
(3204, 27, '17ad', 'جود هلال الراوي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 19:34:20', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 19:34:20', 'synced'),
(3205, 27, '1e5c', 'سناء عصام الكردي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 19:36:27', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 19:36:27', 'synced'),
(3206, 27, '1f38', 'وسن هاشم السامرائي وزينب السامرائي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 19:44:10', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 19:44:10', 'synced'),
(3207, 27, 'e952', 'هدى احمد  الدليمي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 19:53:36', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 19:53:36', 'synced'),
(3208, 27, 'fecf', 'Aya Ali Hendi', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 19:53:48', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 19:53:48', 'synced'),
(3209, 27, '6285', 'رياح ساهر التميمي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 19:54:59', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 19:54:59', 'synced'),
(3210, 13, 'eaa0', 'احمد موسى مسيمي', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 19:56:52', NULL, 0, 'pending', '', NULL, NULL, '2025-09-15 18:44:10', 'synced'),
(3211, 27, 'beb1', 'مينا منذر الفلاحي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 20:01:29', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 20:01:29', 'synced'),
(3212, 27, '7e2a', 'ميسم صالح الكبيسي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 20:02:40', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 20:02:40', 'synced'),
(3213, 27, '4ddc', 'هدى لؤي الفهداوي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 20:06:05', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 20:06:05', 'synced'),
(3214, 27, 'f800', 'ديما هشام حوراني', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 20:08:07', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 20:08:07', 'synced'),
(3215, 27, '957f', 'ام محمد الراوي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 20:25:23', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 20:25:23', 'synced'),
(3216, 27, 'fd70', 'سناء زغير الراوي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 20:28:43', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 20:28:43', 'synced'),
(3217, 27, '3361', 'زينب الوقاص طبرة', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 20:29:35', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 20:29:35', 'synced'),
(3218, 27, 'de11', 'علياء عبدالستار العاني', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 20:32:51', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 20:32:51', 'synced'),
(3219, 27, '7847', 'مروج مؤيد الراوي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 20:33:36', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 20:33:36', 'synced'),
(3220, 13, '0ac0', 'احمد محمد حمود', NULL, '', 1, NULL, NULL, 'canceled', 0, 'not_checked_in', NULL, '2025-09-12 20:34:11', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 20:34:11', 'synced'),
(3221, 13, '0a75', 'احمد الاعظمي وعقيلتة', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 20:40:47', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 05:56:41', 'synced'),
(3222, 27, '3bee', 'Shalimar R Fraij', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 20:44:06', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 20:44:06', 'synced'),
(3223, 27, '1784', 'ريم خيري هميم', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 20:44:26', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 20:44:26', 'synced'),
(3224, 27, '02cd', 'ملك عمر التكريتي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 20:45:36', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 20:45:36', 'synced'),
(3225, 27, 'fbd1', 'Raman FARIS alkurdi', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 20:46:21', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 20:46:21', 'synced'),
(3226, 27, '0aac', 'دكتوره غاده الجصاني', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 20:52:27', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 20:52:27', 'synced'),
(3227, 27, 'efba', 'تبارك ناصر العاني', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 20:57:35', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 20:57:35', 'synced'),
(3228, 27, 'bbff', 'سارة احمد ملاحويش', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 21:09:26', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 21:09:26', 'synced'),
(3229, 27, 'b54a', 'هديل حميد التميمي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 21:10:45', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 21:10:45', 'synced'),
(3231, 27, 'bda1', 'ياسمين أحمد ملاحويش', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 21:16:55', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 21:16:55', 'synced'),
(3232, 27, 'f856', 'صفا مؤيد الحديثي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 21:18:41', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 21:18:41', 'synced'),
(3233, 27, '447b', 'رغدة محمد السامرائي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 21:35:39', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 21:35:39', 'synced'),
(3234, 27, 'd88b', 'لينا سمير الشايب', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 21:37:11', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 21:37:11', 'synced'),
(3235, 27, '9cf3', 'زهراء الوقاص طبرة', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 21:45:49', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 21:45:49', 'synced'),
(3236, 27, '58e9', 'سندس عبدالله الطه', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-12 22:07:38', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-12 22:07:38', 'synced');
INSERT INTO `guests` (`id`, `event_id`, `guest_id`, `name_ar`, `name_en`, `phone_number`, `guests_count`, `table_number`, `assigned_location`, `status`, `invitation_sent`, `checkin_status`, `checkin_time`, `created_at`, `last_invite_sent`, `invite_count`, `last_invite_status`, `notes`, `special_needs`, `dietary_restrictions`, `last_updated`, `sync_status`) VALUES
(3237, 27, '39f8', 'مي معروف الراوي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 02:33:24', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 02:33:24', 'synced'),
(3238, 27, '217c', 'ام علي زوجة احسان فيضي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 05:48:36', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 05:48:36', 'synced'),
(3239, 27, '900f', 'راويه احمد الصراف', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 05:53:55', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 05:53:55', 'synced'),
(3240, 27, 'c60f', 'فرح ماهر عبدالرحمن', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 06:16:20', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 06:16:20', 'synced'),
(3241, 27, '56e1', 'نعم محمد السامرائي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 06:45:15', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 06:45:15', 'synced'),
(3242, 27, 'c818', 'عائشة رافع ابراهيم', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 06:55:44', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 06:55:44', 'synced'),
(3243, 27, '2821', 'رغد فوزي الهاشمي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 07:18:44', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 07:18:44', 'synced'),
(3244, 27, 'fd51', 'هاله وديالا خوري/ صيدلية دير غبار', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 08:24:09', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 08:24:09', 'synced'),
(3245, 27, '7c38', 'رضاء كامل العزاوي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 09:14:09', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 09:14:09', 'synced'),
(3246, 25, '7354', 'السيد برهان مزاهره', NULL, '+966597928770', 2, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-14 15:01:11', '2025-09-13 09:48:20', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 15:01:11', 'synced'),
(3247, 27, '0f6e', 'بسمة سعدون الفياض/ مريم سعد ابراهيم', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 10:06:16', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 10:06:16', 'synced'),
(3248, 27, 'd299', 'سناء زغير الراوي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 10:39:15', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 10:39:15', 'synced'),
(3249, 27, 'bdd0', 'شيماء ضاري الراوي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 10:46:53', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 10:46:53', 'synced'),
(3250, 27, 'ed04', 'اسماء ضاري الراوي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 10:47:19', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 10:47:19', 'synced'),
(3251, 27, '1ee6', 'مريم ضاري الراوي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 10:47:38', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 10:47:38', 'synced'),
(3252, 27, '7b97', 'زينة ضاري الراوي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 10:48:02', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 10:48:02', 'synced'),
(3253, 27, '81bd', 'Her highness queen hajer alrawi', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 10:51:37', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 10:51:37', 'synced'),
(3254, 27, 'eedb', 'ذكرى جواد القيسي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 11:06:09', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 11:06:09', 'synced'),
(3256, 27, 'd68e', 'تاله فواز بيدس', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 13:09:28', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 13:09:28', 'synced'),
(3257, 27, 'c550', 'لمياء حسن المغربي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 13:10:32', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 13:10:32', 'synced'),
(3258, 27, 'e7e6', 'سيرانوش كيفورك مادويان', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 13:16:43', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 13:16:43', 'synced'),
(3259, 27, '68b7', 'Sسراب مظفر عبد الرحمن', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 13:49:25', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 13:49:25', 'synced'),
(3260, 27, 'fd3d', 'دانا معتز سحلول', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 13:52:05', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 13:52:05', 'synced'),
(3261, 13, 'f637', 'علي فاروق المتولي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 16:15:09', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 16:15:09', 'synced'),
(3262, 27, '979d', 'زينب راجي السامرائي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 19:22:47', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 19:22:47', 'synced'),
(3263, 27, 'ddb7', 'الاء عبدالفتاح الراوي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 20:58:20', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 20:58:20', 'synced'),
(3264, 27, '95fe', 'دكتورة عالية قدوري', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-13 22:10:57', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-13 22:10:57', 'synced'),
(3267, 13, 'a629', 'محمد سمير الكبيسي', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-14 06:42:04', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 06:45:23', 'synced'),
(3268, 27, 'd4eb', 'زبيدة خالد العاني', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-14 07:16:59', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-14 07:16:59', 'synced'),
(3269, 27, '53d5', 'سارة فلاح حسن', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-14 07:30:04', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-14 07:30:04', 'synced'),
(3271, 27, '4c86', 'mai  qasim alrawi', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-14 08:51:35', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-14 08:51:35', 'synced'),
(3273, 25, 'a4de', 'السيدة سرين منصور', NULL, '795138687', 1, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-14 10:39:55', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 10:39:55', 'synced'),
(3274, 25, '975d', 'السيدة ناريمان الادهم', NULL, '799815986', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-14 11:02:42', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 20:35:39', 'synced'),
(3275, 25, '0874', 'السيدة رنيم سويدان', NULL, '797791390', 1, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-14 20:35:10', '2025-09-14 11:03:09', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 20:35:10', 'synced'),
(3277, 25, '140b', 'السيدة ملاك الشبلي', NULL, '+4915212892875', 2, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-14 20:44:26', '2025-09-14 11:04:04', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 20:44:26', 'synced'),
(3278, 25, 'b0cd', 'مهندس يوسف مدينات', NULL, '795554558', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-14 11:12:25', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 11:12:25', 'synced'),
(3280, 25, '4f81', 'السيد سامح المحترم', NULL, '+201223286965', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-14 11:21:25', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 11:21:25', 'synced'),
(3281, 25, 'e52a', 'جورج  منصور', NULL, '795579308', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-14 11:33:26', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 11:39:03', 'synced'),
(3282, 25, '287e', 'عيسى خوري', NULL, '795789500', 2, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-14 12:14:55', '2025-09-14 11:51:48', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 12:14:55', 'synced'),
(3283, 25, '7126', 'السيد ركان الداية', NULL, '796507646', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-14 11:52:28', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 11:58:30', 'synced'),
(3284, 27, '280f', 'اسبرق القيسي القيسي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-14 12:32:02', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-14 12:32:02', 'synced'),
(3285, 25, '28e4', 'السيد يزن منصور', NULL, '787252266', 1, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-14 13:31:28', '2025-09-14 12:47:19', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 13:31:28', 'synced'),
(3286, 13, 'c02f', 'ليلى عدنان الطبقجلي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-14 13:46:16', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-14 13:46:16', 'synced'),
(3287, 27, 'd230', 'براءة جميل علي . ريناد صلاح الدين', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-14 14:22:31', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-14 14:22:31', 'synced'),
(3289, 25, '0c7e', 'رامز جون منصور', NULL, '796393485', 2, '', '', 'canceled', 0, 'not_checked_in', NULL, '2025-09-14 16:03:58', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 16:26:55', 'synced'),
(3290, 27, 'cfbd', 'ديمه ال مراد', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-14 17:19:24', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-14 17:19:24', 'synced'),
(3291, 13, '427f', 'نسرين سرجل عبدالقادر', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-14 17:51:31', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-14 17:51:31', 'synced'),
(3292, 25, '042a', 'السيد عودة كرادشة', NULL, '799797526', 2, '', '', 'canceled', 0, 'not_checked_in', NULL, '2025-09-14 19:38:35', NULL, 0, 'pending', '', NULL, NULL, '2025-09-16 13:25:37', 'synced'),
(3293, 27, '739f', 'امل جمعة زبادنة', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-14 20:11:41', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-14 20:11:41', 'synced'),
(3294, 27, '8d0b', 'هنوف احمد غازي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-14 20:26:53', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-14 20:26:53', 'synced'),
(3295, 27, '5dcc', 'نضال علي مزبان', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-14 20:27:20', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-14 20:27:20', 'synced'),
(3296, 25, 'eaf8', 'السيدة رايه ثنيبات', NULL, '799040702', 1, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-15 04:16:07', '2025-09-14 20:44:30', NULL, 0, 'pending', '', NULL, NULL, '2025-09-15 04:16:07', 'synced'),
(3298, 25, '2f74', 'السيدة لجين الربابعة', NULL, '795442685', 1, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-15 23:43:52', '2025-09-14 20:49:19', NULL, 0, 'pending', '', NULL, NULL, '2025-09-15 23:43:52', 'synced'),
(3299, 25, 'b58d', 'السيدة جود موحد', NULL, '797211777', 1, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-15 06:10:04', '2025-09-14 20:52:57', NULL, 0, 'pending', '', NULL, NULL, '2025-09-15 06:10:04', 'synced'),
(3301, 25, '6f70', 'السيدة هدى أبو ظير', NULL, '795591207', 1, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-14 20:56:30', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 20:56:30', 'synced'),
(3302, 25, '8e4d', 'السيدة بسمة العزة', NULL, '796195867', 1, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-14 21:21:20', '2025-09-14 20:59:43', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 21:21:20', 'synced'),
(3303, 25, 'd027', 'مينا الونداوي', NULL, ' ', 1, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-14 21:02:50', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 21:04:35', 'synced'),
(3304, 25, 'fea2', 'السيد لؤي أبو صالح', NULL, '799777636', 1, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-14 21:11:03', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 21:11:03', 'synced'),
(3305, 25, 'cf27', 'أليك السنوي', NULL, '790635722', 1, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-14 21:13:36', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 21:13:36', 'synced'),
(3306, 25, 'b79a', 'السيد ليث الدباس', NULL, '788702865', 1, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-15 03:39:09', '2025-09-14 21:16:31', NULL, 0, 'pending', '', NULL, NULL, '2025-09-15 03:39:09', 'synced'),
(3307, 25, '6c2b', 'السيد تامر البيطار', NULL, '+905349364387', 1, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-14 21:19:32', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 21:22:12', 'synced'),
(3308, 25, 'fe76', 'السيد محمد جغوب', NULL, '797068600', 1, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-14 21:28:18', '2025-09-14 21:25:47', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 21:28:18', 'synced'),
(3309, 25, '467a', 'السيد عمر الكايد', NULL, '798384736', 1, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-14 21:30:14', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 21:30:38', 'synced'),
(3310, 25, '4f73', 'السيد يزن القسوس', NULL, '796326243', 1, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-14 21:33:24', NULL, 0, 'pending', '', NULL, NULL, '2025-09-14 21:34:56', 'synced'),
(3311, 13, 'c0c2', 'فهد عبود الجنابي', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-14 22:23:57', NULL, 0, 'pending', '', NULL, NULL, '2025-09-15 07:40:52', 'synced'),
(3312, 25, '1cfb', 'السيد عيس وهاب واولاده', NULL, '+19095199001', 7, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-15 04:38:40', NULL, 0, 'pending', '', NULL, NULL, '2025-09-15 04:38:40', 'synced'),
(3313, 25, '1a73', 'السيد الياس امسيح', NULL, '+12248297650', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-15 04:47:09', NULL, 0, 'pending', '', NULL, NULL, '2025-09-15 04:47:09', 'synced'),
(3314, 25, '6752', 'عبدو الياس امسيح', NULL, '+18473611682', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-15 04:51:12', NULL, 0, 'pending', '', NULL, NULL, '2025-09-15 04:51:12', 'synced'),
(3315, 25, '5de0', 'علاء الياس امسيح', NULL, '+17736009175', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-15 04:53:59', NULL, 0, 'pending', '', NULL, NULL, '2025-09-15 04:53:59', 'synced'),
(3316, 25, '5490', 'السيدة ام ايمن الفاضلة', NULL, '+962796360141', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-15 05:26:29', NULL, 0, 'pending', '', NULL, NULL, '2025-09-15 05:26:29', 'synced'),
(3317, 25, '02d5', 'السيدة ام عماد الفاضلة', NULL, '+962790473508', 1, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-15 05:30:58', NULL, 0, 'pending', '', NULL, NULL, '2025-09-15 05:30:58', 'synced'),
(3318, 13, '2a06', 'ريفل عماد بولص', NULL, '', 2, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-15 15:32:13', NULL, 0, 'pending', '', NULL, NULL, '2025-09-15 15:32:46', 'synced'),
(3320, 13, 'db62', 'هدى عبد النبي العطار', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-15 17:08:58', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-15 17:08:58', 'synced'),
(3321, 25, '0f17', 'السيد رامي بدور', NULL, '+962777588775', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-15 17:10:11', NULL, 0, 'pending', '', NULL, NULL, '2025-09-15 17:10:11', 'synced'),
(3322, 27, 'b984', 'نهله دروش العاني', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-15 18:29:05', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-15 18:29:05', 'synced'),
(3323, 25, '439e', 'سامر سويدان', NULL, '796362237', 2, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-15 19:49:54', '2025-09-15 19:43:53', NULL, 0, 'pending', '', NULL, NULL, '2025-09-15 19:49:54', 'synced'),
(3324, 25, 'f30b', 'دانيال عزوني', NULL, '799455177', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-15 19:49:36', NULL, 0, 'pending', '', NULL, NULL, '2025-09-15 19:49:36', 'synced'),
(3325, 25, 'bfac', 'سامر حداد', NULL, '796992993', 2, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-16 14:13:06', '2025-09-15 19:50:03', NULL, 0, 'pending', '', NULL, NULL, '2025-09-16 14:13:06', 'synced'),
(3326, 25, '1ac0', 'السيدة جنى التميمي', NULL, '791688897', 1, '', '', 'confirmed', 0, 'not_checked_in', '2025-09-16 10:57:20', '2025-09-15 20:29:56', NULL, 0, 'pending', '', NULL, NULL, '2025-09-16 10:57:20', 'synced'),
(3327, 25, '57d4', 'السيدة دارين العريان', NULL, '795244527', 1, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-15 20:30:28', NULL, 0, 'pending', '', NULL, NULL, '2025-09-15 20:30:28', 'synced'),
(3328, 13, '2e26', 'مفاخر الخياط', NULL, '', 1, '', '', 'confirmed', 0, 'not_checked_in', NULL, '2025-09-15 21:05:10', NULL, 0, 'pending', '', NULL, NULL, '2025-09-16 08:42:36', 'synced'),
(3329, 25, '1394', 'السيد انضوني حنانيا', NULL, '+17144577117', 2, '', '', 'pending', 0, 'not_checked_in', NULL, '2025-09-16 05:23:30', NULL, 0, 'pending', '', NULL, NULL, '2025-09-16 05:23:30', 'synced'),
(3330, 27, '00a7', 'فرح فاضل المختار', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-16 06:08:28', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-16 06:08:28', 'synced'),
(3331, 13, '9da6', 'سحر هاشم رنكه', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-16 07:00:45', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-16 07:00:45', 'synced'),
(3332, 13, 'ccb4', 'شذى هاشم رنكه', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-16 07:01:15', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-16 07:01:15', 'synced'),
(3333, 13, '6d3d', 'سرمد محسن آل ياسين', NULL, '', 1, NULL, NULL, 'canceled', 0, 'not_checked_in', NULL, '2025-09-16 11:40:12', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-16 11:40:12', 'synced'),
(3334, 27, '19d2', 'فاتن عبدالامير داودالتميمي', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-16 11:47:24', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-16 11:47:24', 'synced'),
(3335, 13, '2f3c', 'محمد سامي الشماع', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-16 13:05:25', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-16 13:05:25', 'synced'),
(3336, 27, '7c4a', 'نور (زوجة عبدالرحمن سحلول)', NULL, '', 1, NULL, NULL, 'confirmed', 0, 'not_checked_in', NULL, '2025-09-16 14:25:12', NULL, 0, 'pending', NULL, NULL, NULL, '2025-09-16 14:25:12', 'synced');

--
-- Triggers `guests`
--
DELIMITER $$
CREATE TRIGGER `update_current_guests_count_after_delete` AFTER DELETE ON `guests` FOR EACH ROW BEGIN
    UPDATE events 
    SET current_guests_count = (
        SELECT COALESCE(SUM(guests_count), 0) 
        FROM guests 
        WHERE event_id = OLD.event_id 
        AND status IN ('confirmed', 'pending')
    ) 
    WHERE id = OLD.event_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_current_guests_count_after_insert` AFTER INSERT ON `guests` FOR EACH ROW BEGIN
    UPDATE events 
    SET current_guests_count = (
        SELECT COALESCE(SUM(guests_count), 0) 
        FROM guests 
        WHERE event_id = NEW.event_id 
        AND status IN ('confirmed', 'pending')
    ) 
    WHERE id = NEW.event_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_current_guests_count_after_update` AFTER UPDATE ON `guests` FOR EACH ROW BEGIN
    UPDATE events 
    SET current_guests_count = (
        SELECT COALESCE(SUM(guests_count), 0) 
        FROM guests 
        WHERE event_id = NEW.event_id 
        AND status IN ('confirmed', 'pending')
    ) 
    WHERE id = NEW.event_id;
    
    -- إذا تم تغيير الحدث، حدث العدد للحدث القديم أيضاً
    IF OLD.event_id != NEW.event_id THEN
        UPDATE events 
        SET current_guests_count = (
            SELECT COALESCE(SUM(guests_count), 0) 
            FROM guests 
            WHERE event_id = OLD.event_id 
            AND status IN ('confirmed', 'pending')
        ) 
        WHERE id = OLD.event_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `attempts` int(11) DEFAULT 1,
  `locked_until` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `login_time` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('success','failed') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `username`, `ip_address`, `user_agent`, `login_time`, `status`) VALUES
(1, 'bigsam', '81.21.15.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-08-03 19:11:36', 'success'),
(2, 'bigsam', '81.21.15.89', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '2025-08-03 19:12:25', 'success');

-- --------------------------------------------------------

--
-- Table structure for table `message_logs`
--

CREATE TABLE `message_logs` (
  `id` int(11) NOT NULL,
  `workflow_id` varchar(255) DEFAULT NULL,
  `action_type` varchar(50) DEFAULT NULL,
  `total_processed` int(11) DEFAULT NULL,
  `success_count` int(11) DEFAULT NULL,
  `failure_count` int(11) DEFAULT NULL,
  `success_rate` decimal(5,2) DEFAULT NULL,
  `event_ids` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `offline_operations`
--

CREATE TABLE `offline_operations` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `guest_id` varchar(10) NOT NULL,
  `operation_type` enum('checkin','confirm_and_checkin','add_note') NOT NULL,
  `operation_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`operation_data`)),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','processed','failed') DEFAULT 'pending',
  `error_message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='تتبع العمليات غير المتزامنة للوضع غير المتصل';

-- --------------------------------------------------------

--
-- Table structure for table `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `remember_tokens`
--

INSERT INTO `remember_tokens` (`id`, `username`, `token_hash`, `expires_at`, `created_at`) VALUES
(11, 'amal', '$2y$10$vmkrgU9/RuL90qm8CpwJyuGfXQ62FMfXJ09XcGD7.P8hv95eF5go6', '2025-10-15 17:08:03', '2025-09-11 11:50:06'),
(16, 'hijjawi', '$2y$10$o6sMtyeoGv2SyTRUXvB6duI6b1wNlPvpF8e3FS2N0zNCmJ5M6p.hm', '2025-10-16 10:34:31', '2025-09-12 11:38:06'),
(22, 'Hasan', '$2y$10$9/VIv6zb6gZs2HtqkWz6wObMgG2NHwuaGhuqCnkPWLVYFF2K8X99y', '2025-10-14 18:11:15', '2025-09-14 18:11:15'),
(23, 'maher', '$2y$10$5DncpM1SyiyonbtMBkuW9uAY1fQzG0u9tXDBQA0tAoSIO1hEEPaj2', '2025-10-16 06:49:00', '2025-09-15 04:33:28');

-- --------------------------------------------------------

--
-- Table structure for table `reminder_logs`
--

CREATE TABLE `reminder_logs` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `reminder_type` varchar(50) NOT NULL,
  `custom_message` text DEFAULT NULL,
  `response_data` text DEFAULT NULL,
  `http_code` int(11) DEFAULT 0,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `send_results`
--

CREATE TABLE `send_results` (
  `id` int(11) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `action_type` varchar(50) NOT NULL,
  `success_count` int(11) DEFAULT 0,
  `failed_count` int(11) DEFAULT 0,
  `total_processed` int(11) DEFAULT 0,
  `target_count` int(11) DEFAULT NULL,
  `response_data` text DEFAULT NULL,
  `http_code` int(11) DEFAULT 0,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `send_results`
--

INSERT INTO `send_results` (`id`, `event_id`, `action_type`, `success_count`, `failed_count`, `total_processed`, `target_count`, `response_data`, `http_code`, `created_at`) VALUES
(470, 20, 'send_selected', 0, 1, 1, 1, '{\"success\":true,\"message\":\"\\u062a\\u0645 \\u0625\\u0631\\u0633\\u0627\\u0644 0 \\u0631\\u0633\\u0627\\u0644\\u0629 \\u0628\\u0646\\u062c\\u0627\\u062d \\u0645\\u0646 \\u0623\\u0635\\u0644 1 (0%)\",\"summary\":{\"totalProcessed\":1,\"successCount\":0,\"failureCount\":1,\"successRate\":0,\"eventsAffected\":1,\"eventIds\":[20]},\"details\":{\"successfulSends\":[],\"failedSends\":[{\"name\":\"\\u0627\\u0644\\u0633\\u064a\\u062f \\u0645\\u062d\\u0645\\u062f \\u062e\\u0627\\u0644\\u062f \\u0627\\u0644\\u0645\\u062d\\u062a\\u0631\\u0645\",\"phone\":\"+962798797794\",\"guestId\":\"c1f2\",\"eventId\":20,\"error\":\"Unknown error\"}],\"processedAt\":\"2025-09-10T17:28:47.843Z\"}}', 200, '2025-09-10 17:28:47'),
(471, 20, 'send_selected', 0, 1, 1, 1, '{\"success\":true,\"message\":\"\\u062a\\u0645 \\u0625\\u0631\\u0633\\u0627\\u0644 0 \\u0631\\u0633\\u0627\\u0644\\u0629 \\u0628\\u0646\\u062c\\u0627\\u062d \\u0645\\u0646 \\u0623\\u0635\\u0644 1 (0%)\",\"summary\":{\"totalProcessed\":1,\"successCount\":0,\"failureCount\":1,\"successRate\":0,\"eventsAffected\":1,\"eventIds\":[20]},\"details\":{\"successfulSends\":[],\"failedSends\":[{\"name\":\"\\u0627\\u0644\\u0633\\u064a\\u062f \\u064a\\u0632\\u064a\\u062f \\u0631\\u064a\\u0627\\u0636 \\u0627\\u0644\\u062d\\u062c\\u0627\\u0648\\u064a \\u0627\\u0644\\u0645\\u062d\\u062a\\u0631\\u0645\",\"phone\":\"+971567540084\",\"guestId\":\"8d67\",\"eventId\":20,\"error\":\"Unknown error\"}],\"processedAt\":\"2025-09-10T18:18:15.204Z\"}}', 200, '2025-09-10 18:18:15'),
(472, 27, 'send_event_all', 0, 1, 1, NULL, '{\"success\":true,\"message\":\"\\u062a\\u0645 \\u0625\\u0631\\u0633\\u0627\\u0644 0 \\u0631\\u0633\\u0627\\u0644\\u0629 \\u0628\\u0646\\u062c\\u0627\\u062d \\u0645\\u0646 \\u0623\\u0635\\u0644 1 (0%)\",\"summary\":{\"totalProcessed\":1,\"successCount\":0,\"failureCount\":1,\"successRate\":0,\"eventsAffected\":1,\"eventIds\":[27]},\"details\":{\"successfulSends\":[],\"failedSends\":[{\"name\":\"Mohammad Higgawi\",\"phone\":\"+962798797794\",\"guestId\":\"863a\",\"eventId\":27,\"error\":\"Unknown error\"}],\"processedAt\":\"2025-09-11T12:33:46.093Z\"}}', 200, '2025-09-11 12:33:46'),
(473, 27, 'send_selected', 0, 1, 1, 1, '{\"success\":true,\"message\":\"\\u062a\\u0645 \\u0625\\u0631\\u0633\\u0627\\u0644 0 \\u0631\\u0633\\u0627\\u0644\\u0629 \\u0628\\u0646\\u062c\\u0627\\u062d \\u0645\\u0646 \\u0623\\u0635\\u0644 1 (0%)\",\"summary\":{\"totalProcessed\":1,\"successCount\":0,\"failureCount\":1,\"successRate\":0,\"eventsAffected\":1,\"eventIds\":[27]},\"details\":{\"successfulSends\":[],\"failedSends\":[{\"name\":\"\\u0627\\u0644\\u0633\\u064a\\u062f \\u062d\\u0633\\u0646 \\u0648\\u0639\\u0642\\u064a\\u0644\\u062a\\u0647 \\u0627\\u0644\\u0645\\u062d\\u062a\\u0631\\u0645\\u064a\\u0646\",\"phone\":\"+962790094111\",\"guestId\":\"4605\",\"eventId\":27,\"error\":\"Unknown error\"}],\"processedAt\":\"2025-09-11T13:22:05.387Z\"}}', 200, '2025-09-11 13:22:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','viewer','checkin_user') NOT NULL DEFAULT 'viewer',
  `event_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `permissions` varchar(255) DEFAULT NULL,
  `allowed_pages` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `role`, `event_id`, `created_at`, `permissions`, `allowed_pages`) VALUES
(1, 'hijjawi', '$2y$10$/c7EOW3V1PAA.4l6TPclVeY4F/kumOOU3/B8PN1yrs6wIfRF1LSzK', 'admin', NULL, '2025-07-27 14:11:41', NULL, NULL),
(6, 'checkin', '$2y$10$N69yfTaoYYIq3dZ8dhVtLe2tuK4CV17xgFt3cVWT.k.N.Cw9mb3gm', 'checkin_user', 20, '2025-08-02 19:08:42', NULL, NULL),
(11, 'yehya', '$2y$10$..bEYsIV1GJTx01hZpca6ukxXJn.hOlBlO.PVtSOAl5HENduN/B8i', 'viewer', 13, '2025-08-23 09:59:33', NULL, NULL),
(15, 'maher', '$2y$10$PgZ3fbWfhY/g5r9ln93AuOA0FsS5BzYwcS90RadNeRRnVJCAEO6au', 'viewer', 25, '2025-09-02 11:23:31', NULL, NULL),
(16, 'Ahmed', '$2y$10$ibiMlv0AwIXWnqf9YYx8euVQChmA2G4vuxE5zJOO5J0181327VXsG', 'viewer', 26, '2025-09-05 09:41:21', NULL, NULL),
(17, 'amal', '$2y$10$Virf7Y9iJ4YFobH2rAdTeOtdiYuEd6.JIJzooq5md2HFvV9fLKPjK', 'admin', NULL, '2025-09-11 01:28:19', NULL, NULL),
(18, 'Hasan', '$2y$10$eBDxnemAAnFCRGsfrYQuJO3XT1./vDvRIJ.2/a7TZGHV4HbixtZvG', 'viewer', 27, '2025-09-11 13:33:01', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `backup_settings`
--
ALTER TABLE `backup_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `event_slug` (`event_slug`);

--
-- Indexes for table `guests`
--
ALTER TABLE `guests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `guest_id` (`guest_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `idx_event_status` (`event_id`,`status`),
  ADD KEY `idx_last_invite` (`last_invite_sent`),
  ADD KEY `idx_guest_search` (`name_ar`,`phone_number`,`table_number`),
  ADD KEY `idx_checkin_date` (`checkin_status`,`checkin_time`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ip_time` (`ip_address`,`updated_at`),
  ADD KEY `idx_locked_until` (`locked_until`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_username_time` (`username`,`login_time`),
  ADD KEY `idx_ip_time` (`ip_address`,`login_time`);

--
-- Indexes for table `message_logs`
--
ALTER TABLE `message_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offline_operations`
--
ALTER TABLE `offline_operations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status_created` (`status`,`created_at`),
  ADD KEY `idx_event_guest` (`event_id`,`guest_id`);

--
-- Indexes for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Indexes for table `reminder_logs`
--
ALTER TABLE `reminder_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `send_results`
--
ALTER TABLE `send_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `backup_settings`
--
ALTER TABLE `backup_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `guests`
--
ALTER TABLE `guests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3337;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `message_logs`
--
ALTER TABLE `message_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `offline_operations`
--
ALTER TABLE `offline_operations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `reminder_logs`
--
ALTER TABLE `reminder_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `send_results`
--
ALTER TABLE `send_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=474;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

-- --------------------------------------------------------

--
-- Structure for view `event_send_stats`
--
DROP TABLE IF EXISTS `event_send_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u747253029_dbhijjawi`@`127.0.0.1` SQL SECURITY DEFINER VIEW `event_send_stats`  AS SELECT `e`.`id` AS `event_id`, `e`.`event_name` AS `event_name`, count(`g`.`id`) AS `total_guests`, sum(case when `g`.`status` = 'confirmed' then 1 else 0 end) AS `confirmed_guests`, sum(case when `g`.`status` = 'pending' then 1 else 0 end) AS `pending_guests`, sum(case when `g`.`last_invite_status` = 'sent' then 1 else 0 end) AS `invited_guests`, max(`g`.`last_invite_sent`) AS `last_invitation_time`, coalesce(`sr`.`last_send_success`,0) AS `last_send_success`, coalesce(`sr`.`last_send_failed`,0) AS `last_send_failed` FROM ((`events` `e` left join `guests` `g` on(`e`.`id` = `g`.`event_id`)) left join (select `send_results`.`event_id` AS `event_id`,max(`send_results`.`success_count`) AS `last_send_success`,max(`send_results`.`failed_count`) AS `last_send_failed` from `send_results` where `send_results`.`created_at` >= current_timestamp() - interval 1 day group by `send_results`.`event_id`) `sr` on(`e`.`id` = `sr`.`event_id`)) GROUP BY `e`.`id`, `e`.`event_name` ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `guests`
--
ALTER TABLE `guests`
  ADD CONSTRAINT `guests_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `offline_operations`
--
ALTER TABLE `offline_operations`
  ADD CONSTRAINT `offline_operations_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reminder_logs`
--
ALTER TABLE `reminder_logs`
  ADD CONSTRAINT `reminder_logs_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `send_results`
--
ALTER TABLE `send_results`
  ADD CONSTRAINT `send_results_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
