-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2026 at 11:20 AM
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
-- Database: `drynutri`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_notification_reads`
--

CREATE TABLE `admin_notification_reads` (
  `staff_id` int(11) NOT NULL,
  `item_type` varchar(20) NOT NULL,
  `item_id` int(11) NOT NULL,
  `read_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_notification_reads`
--

INSERT INTO `admin_notification_reads` (`staff_id`, `item_type`, `item_id`, `read_at`) VALUES
(1, 'order', 13, '2026-11-11 16:06:13'),
(1, 'order', 3, '2026-11-11 16:06:16'),
(1, 'order', 26, '2026-11-11 16:06:18'),
(1, 'order', 22, '2026-11-11 16:06:20'),
(1, 'order', 2, '2026-11-11 16:06:22'),
(1, 'order', 16, '2026-11-11 16:06:24');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `name_en` varchar(120) NOT NULL,
  `name_bn` varchar(120) DEFAULT NULL,
  `slug` varchar(140) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `icon` varchar(12) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `show_home` tinyint(1) DEFAULT 1,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name_en`, `name_bn`, `slug`, `image`, `icon`, `sort_order`, `show_home`, `status`) VALUES
(1, 0, 'Dried Mango', 'শুকনো আম', 'dried-mango', '261111-a188ea0864.jpg', '🥭', 1, 1, 1),
(2, 0, 'Dried Orange', 'শুকনো কমলা', 'dried-orange', NULL, '🍊', 2, 1, 1),
(3, 0, 'Dried Banana', 'শুকনো কলা', 'dried-banana', NULL, '🍌', 3, 1, 1),
(4, 0, 'Dried Berries', 'শুকনো বেরি', 'dried-berries', NULL, '🍓', 4, 1, 1),
(5, 0, 'Pineapple & Tropical', 'আনারস ও ট্রপিক্যাল', 'pineapple-tropical', NULL, '🍍', 5, 1, 1),
(6, 0, 'Dates & Figs', 'খেজুর ও ডুমুর', 'dates-figs', NULL, '🌴', 6, 1, 1),
(7, 0, 'Nuts & Seeds', 'বাদাম ও বীজ', 'nuts-seeds', NULL, '🥜', 7, 1, 1),
(8, 0, 'Mixed Dried Fruit', 'মিক্সড শুকনো ফল', 'mixed-dried-fruit', NULL, '🥣', 8, 1, 1),
(9, 1, 'Mango Slices', 'ম্যাঙ্গো স্লাইস', 'mango-slices', NULL, '🥭', 1, 0, 1),
(10, 1, 'Mango Bars', 'ম্যাঙ্গো বার', 'mango-bars', NULL, '🥭', 2, 0, 1),
(11, 2, 'Orange Slices', 'কমলার স্লাইস', 'orange-slices', NULL, '🍊', 1, 0, 1),
(12, 3, 'Banana Chips', 'কলার চিপস', 'banana-chips', NULL, '🍌', 1, 0, 1),
(13, 7, 'Cashew Nuts', 'কাজুবাদাম', 'cashew-nuts', NULL, '🥜', 1, 0, 1),
(14, 7, 'Almonds', 'কাঠবাদাম', 'almonds', NULL, '🌰', 2, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `checkout_visits`
--

CREATE TABLE `checkout_visits` (
  `id` int(11) NOT NULL,
  `sid` varchar(64) NOT NULL,
  `user_id` int(11) DEFAULT 0,
  `name` varchar(120) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(160) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `device` varchar(12) DEFAULT NULL,
  `cart_json` text DEFAULT NULL,
  `cart_total` decimal(10,2) DEFAULT 0.00,
  `items_count` int(11) DEFAULT 0,
  `status` varchar(12) DEFAULT 'visited',
  `order_id` int(11) DEFAULT 0,
  `contacted` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `checkout_visits`
--

INSERT INTO `checkout_visits` (`id`, `sid`, `user_id`, `name`, `phone`, `email`, `address`, `ip`, `device`, `cart_json`, `cart_total`, `items_count`, `status`, `order_id`, `contacted`, `created_at`, `updated_at`) VALUES
(1, 'cfcd208495d565ef66e7dff9f98764da', 0, 'Shahin Alam', '01720111222', 'shahin@example.com', 'Uttara, Dhaka', '103.38.159.112', 'desktop', '[{\"n\": \"Premium Almonds 500g\", \"v\": \"250 g\", \"q\": 2, \"p\": 340}]', 680.00, 2, 'visited', 0, 0, '2026-11-10 04:00:00', '2026-11-10 04:08:00'),
(2, 'c4ca4238a0b923820dcc509a6f75849b', 0, '', '', '', '', '103.32.243.3', 'mobile', '[{\"n\": \"Dried Mango Bar 200g\", \"v\": \"200 g\", \"q\": 2, \"p\": 290}]', 580.00, 2, 'visited', 0, 0, '2026-11-03 01:00:00', '2026-11-03 01:05:00'),
(3, 'c81e728d9d4c2f636f067f89cc14862c', 0, 'Rima Begum', '01933444555', '', 'Uttara, Dhaka', '103.45.144.238', 'mobile', '[{\"n\": \"Organic Raisins 500g\", \"v\": \"500 g\", \"q\": 2, \"p\": 420}]', 840.00, 2, 'visited', 0, 0, '2026-11-08 23:00:00', '2026-11-08 23:05:00'),
(4, 'eccbc87e4b5ce2fe28308fd9f2a7baf3', 0, 'Jahid Hasan', '01844666777', 'jahid@example.com', 'Uttara, Dhaka', '103.48.136.192', 'mobile', '[{\"n\": \"Mixed Dried Fruit Pack 400g\", \"v\": \"200 g\", \"q\": 2, \"p\": 310}]', 620.00, 2, 'visited', 0, 1, '2026-11-03 21:00:00', '2026-11-03 21:04:00'),
(5, 'a87ff679a2f3e71d9181a67b7542122c', 0, '', '', '', '', '103.36.45.29', 'desktop', '[{\"n\": \"Organic Raisins 500g\", \"v\": \"100 g\", \"q\": 2, \"p\": 120}]', 240.00, 2, 'visited', 0, 0, '2026-11-05 02:00:00', '2026-11-05 02:07:00'),
(6, 'e4da3b7fbbce2345d7772b0674a318d5', 0, 'Mou Khan', '01555888999', '', 'Uttara, Dhaka', '103.56.131.54', 'mobile', '[{\"n\": \"Dried Blueberry 150g\", \"v\": \"150 g\", \"q\": 2, \"p\": 560}]', 1120.00, 2, 'visited', 0, 0, '2026-11-01 23:00:00', '2026-11-01 23:08:00'),
(7, '1679091c5a880faf6fb5e6087eb1b2dc', 0, 'Imran Hossain', '01677000111', '', 'Uttara, Dhaka', '103.48.106.3', 'mobile', '[{\"n\": \"Dried Banana Chips 250g\", \"v\": \"125 g\", \"q\": 1, \"p\": 155}]', 155.00, 1, 'visited', 0, 0, '2026-11-08 11:00:00', '2026-11-08 11:06:00'),
(8, '8f14e45fceea167a5a36dedd4bea2543', 0, '', '01711999000', '', 'Uttara, Dhaka', '103.57.26.151', 'mobile', '[{\"n\": \"Dried Mango Slices 250g\", \"v\": \"125 g\", \"q\": 2, \"p\": 210}]', 420.00, 2, 'visited', 0, 0, '2026-10-29 19:00:00', '2026-10-29 19:02:00'),
(9, '108fc0999e244d1d53a6ffd0fb599ae3', 1, 'Super Admin', '', 'admin@drynutri.com', '', '::1', 'desktop', '[{\"n\":\"Tropical Trail Mix 300g\",\"v\":\"300 g\",\"q\":1,\"p\":520}]', 520.00, 1, 'visited', 0, 0, '2026-11-11 12:47:07', '2026-11-11 12:47:07');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(120) DEFAULT NULL,
  `email` varchar(160) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(40) NOT NULL,
  `type` varchar(10) DEFAULT 'percent',
  `value` decimal(10,2) DEFAULT 0.00,
  `min_order` decimal(10,2) DEFAULT 0.00,
  `max_uses` int(11) DEFAULT 0,
  `used` int(11) DEFAULT 0,
  `expires_at` date DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `type`, `value`, `min_order`, `max_uses`, `used`, `expires_at`, `status`) VALUES
(1, 'WELCOME10', 'percent', 10.00, 500.00, 0, 0, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `category` varchar(60) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `expense_date` date NOT NULL,
  `note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `title`, `category`, `amount`, `expense_date`, `note`) VALUES
(2, 'Facebook Ads', 'Marketing', 4500.00, '2026-10-31', 'Demo expense'),
(3, 'Courier charges', 'Delivery', 1800.00, '2026-11-04', 'Demo expense'),
(4, 'Packaging materials', 'Packaging', 2200.00, '2026-10-28', 'Demo expense'),
(5, 'Staff salary', 'Salary', 15000.00, '2026-10-13', 'Demo expense'),
(6, 'Shop rent', 'Rent', 8000.00, '2026-11-10', 'Demo expense'),
(7, 'Stock purchase', 'Inventory', 25000.00, '2026-11-10', 'Demo expense');

-- --------------------------------------------------------

--
-- Table structure for table `features`
--

CREATE TABLE `features` (
  `id` int(11) NOT NULL,
  `icon` varchar(12) DEFAULT NULL,
  `title_en` varchar(80) DEFAULT NULL,
  `title_bn` varchar(80) DEFAULT NULL,
  `sub_en` varchar(80) DEFAULT NULL,
  `sub_bn` varchar(80) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `features`
--

INSERT INTO `features` (`id`, `icon`, `title_en`, `title_bn`, `sub_en`, `sub_bn`, `sort_order`, `status`) VALUES
(1, 'sprout', 'No Added Sugar', 'চিনি ছাড়া', '100% Natural', '১০০% প্রাকৃতিক', 1, 1),
(2, 'award', 'Premium Quality', 'প্রিমিয়াম মান', 'Hand-picked Fruit', 'হাতে বাছাই ফল', 2, 1),
(3, 'shield', 'Safe & Hygienic', 'নিরাপদ ও স্বাস্থ্যসম্মত', 'Sealed Packs', 'সিল করা প্যাক', 3, 1),
(4, 'truck', 'Fast & Reliable', 'দ্রুত ও নির্ভরযোগ্য', 'Delivery', 'ডেলিভারি', 4, 1),
(5, 'headphones', '24/7 Customer', '২৪/৭ গ্রাহক', 'Support', 'সহায়তা', 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `ip` varchar(45) NOT NULL,
  `tries` int(11) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mail_log`
--

CREATE TABLE `mail_log` (
  `id` int(11) NOT NULL,
  `to_email` varchar(160) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `error` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `location` varchar(20) NOT NULL DEFAULT 'header',
  `item_type` varchar(20) NOT NULL DEFAULT 'link',
  `ref_id` int(11) DEFAULT 0,
  `label_en` varchar(100) DEFAULT NULL,
  `label_bn` varchar(100) DEFAULT NULL,
  `badge` varchar(20) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT 0,
  `auto_children` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `location`, `item_type`, `ref_id`, `label_en`, `label_bn`, `badge`, `url`, `parent_id`, `auto_children`, `sort_order`, `status`) VALUES
(1, 'header', 'link', 0, 'Home', 'হোম', NULL, 'index.php', 0, 0, 1, 1),
(2, 'header', 'link', 0, 'All Products', 'সকল পণ্য', NULL, 'shop.php', 0, 0, 2, 1),
(3, 'header', 'category', 1, 'Dried Mango', '', NULL, '', 0, 1, 3, 1),
(4, 'header', 'category', 2, 'Dried Orange', '', NULL, '', 0, 1, 4, 1),
(5, 'header', 'category', 3, 'Dried Banana', '', NULL, '', 0, 1, 5, 1),
(6, 'header', 'category', 6, 'Dates & Figs', '', NULL, '', 0, 0, 6, 1),
(7, 'header', 'link', 0, 'Offers', 'অফার', 'Hot', 'shop.php?offer=1', 0, 0, 7, 1),
(8, 'header', 'link', 0, 'About Us', 'আমাদের সম্পর্কে', NULL, 'page.php?slug=about', 0, 0, 8, 1),
(9, 'footer_quick', 'link', 0, 'Home', 'হোম', NULL, 'index.php', 0, 0, 1, 1),
(10, 'footer_quick', 'link', 0, 'All Products', 'সকল পণ্য', NULL, 'shop.php', 0, 0, 2, 1),
(11, 'footer_quick', 'link', 0, 'Offers', 'অফার', NULL, 'shop.php?offer=1', 0, 0, 3, 1),
(12, 'footer_quick', 'link', 0, 'About Us', 'আমাদের সম্পর্কে', NULL, 'page.php?slug=about', 0, 0, 4, 1),
(13, 'footer_quick', 'link', 0, 'Contact', 'যোগাযোগ', NULL, 'contact.php', 0, 0, 5, 1),
(14, 'footer_care', 'link', 0, 'Track Order', 'অর্ডার ট্র্যাক', NULL, 'track.php', 0, 0, 1, 1),
(15, 'footer_care', 'link', 0, 'Return Policy', 'রিটার্ন পলিসি', NULL, 'page.php?slug=return-policy', 0, 0, 2, 1),
(16, 'footer_care', 'link', 0, 'Shipping Info', 'শিপিং তথ্য', NULL, 'page.php?slug=shipping', 0, 0, 3, 1),
(17, 'footer_care', 'link', 0, 'FAQ', 'সাধারণ জিজ্ঞাসা', NULL, 'page.php?slug=faq', 0, 0, 4, 1),
(18, 'footer_care', 'link', 0, 'Terms & Conditions', 'শর্তাবলী', NULL, 'page.php?slug=terms', 0, 0, 5, 1),
(19, 'header', 'category', 8, '', '', '', '', 0, 0, 10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `newsletter`
--

CREATE TABLE `newsletter` (
  `id` int(11) NOT NULL,
  `email` varchar(160) NOT NULL,
  `token` varchar(40) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `source` varchar(20) DEFAULT 'site',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `newsletter`
--

INSERT INTO `newsletter` (`id`, `email`, `token`, `status`, `source`, `created_at`) VALUES
(1, 'demo1@example.com', '0d1e2f3a4b5c6d7e8f90a1b1', 1, 'site', '2026-11-11 10:58:20'),
(2, 'demo2@example.com', '0d1e2f3a4b5c6d7e8f90a1b2', 1, 'site', '2026-11-11 10:58:20'),
(3, 'demo3@example.com', '0d1e2f3a4b5c6d7e8f90a1b3', 0, 'site', '2026-11-11 10:58:20'),
(4, 'mdjhk19@gmail.com', 'f327a664f00e11c1e4143e01', 1, 'site', '2026-11-11 12:32:08');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `type` varchar(12) DEFAULT 'message',
  `title_en` varchar(200) DEFAULT NULL,
  `title_bn` varchar(200) DEFAULT NULL,
  `body_en` varchar(500) DEFAULT NULL,
  `body_bn` varchar(500) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `emailed` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `title_en`, `title_bn`, `body_en`, `body_bn`, `link`, `image`, `emailed`, `status`, `created_at`) VALUES
(1, 'offer', 'Welcome to DryNutri! 🥭', 'ড্রাইনিউট্রিতে স্বাগতম! 🥭', 'Use code WELCOME10 for 10% off your first order.', 'প্রথম অর্ডারে ১০% ছাড় পেতে কোড WELCOME10 ব্যবহার করুন।', 'shop.php', '', 0, 1, '2026-11-11 10:58:20'),
(2, 'offer', 'Welcome to DryNutri!', 'ড্রাইনিউট্রিতে স্বাগতম!', 'Use code WELCOME10 for 10% off your first order.', 'প্রথম অর্ডারে ১০% ছাড় পেতে কোড WELCOME10 ব্যবহার করুন।', 'shop.php', '', 0, 1, '2026-11-11 10:58:20'),
(3, 'offer', 'Welcome to DryNutri!', 'ড্রাইনিউট্রিতে স্বাগতম!', 'Use code WELCOME10 for 10% off your first order.', 'প্রথম অর্ডারে ১০% ছাড় পেতে কোড WELCOME10 ব্যবহার করুন।', 'shop.php', '', 0, 1, '2026-11-11 10:58:20'),
(4, 'offer', 'New offer', '', 'Freee offefr', '', '', '', 0, 1, '2026-11-11 13:18:00');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_no` varchar(30) NOT NULL,
  `user_id` int(11) DEFAULT 0,
  `name` varchar(120) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(160) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(80) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT 0.00,
  `shipping` decimal(10,2) DEFAULT 0.00,
  `discount` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) DEFAULT 0.00,
  `coupon` varchar(40) DEFAULT NULL,
  `delivery_zone` varchar(10) DEFAULT NULL,
  `payment_method` varchar(30) DEFAULT 'cod',
  `payment_status` varchar(10) DEFAULT 'unpaid',
  `status` varchar(20) DEFAULT 'pending',
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_no`, `user_id`, `name`, `phone`, `email`, `address`, `city`, `subtotal`, `shipping`, `discount`, `total`, `coupon`, `delivery_zone`, `payment_method`, `payment_status`, `status`, `note`, `created_at`) VALUES
(1, 'DN261031D001', 8, 'Ahmed Karim', '01711000007', 'ahmed@example.com', 'House 12, Road 5', 'Dhaka', 830.00, 60.00, 0.00, 890.00, NULL, 'inside', 'cod', 'paid', 'delivered', NULL, '2026-10-31 08:00:00'),
(2, 'DN261107D002', 8, 'Ahmed Karim', '01711000007', 'ahmed@example.com', 'House 12, Road 5', 'Dhaka', 240.00, 120.00, 0.00, 360.00, NULL, 'outside', 'cod', 'unpaid', 'pending', NULL, '2026-11-07 19:00:00'),
(3, 'DN261103D003', 4, 'Md. Hasan', '01811000003', 'md.@example.com', 'House 12, Road 5', 'Chattogram', 1280.00, 0.00, 128.00, 1152.00, 'WELCOME10', 'outside', 'cod', 'unpaid', 'pending', NULL, '2026-11-03 11:00:00'),
(4, 'DN261030D004', 3, 'Fatema Akter', '01711000002', 'fatema@example.com', 'House 12, Road 5', 'Uttara', 2350.00, 0.00, 0.00, 2350.00, NULL, 'inside', 'cod', 'unpaid', 'confirmed', NULL, '2026-10-30 09:00:00'),
(5, 'DN261019D005', 3, 'Fatema Akter', '01711000002', 'fatema@example.com', 'House 12, Road 5', 'Uttara', 1740.00, 0.00, 0.00, 1740.00, NULL, 'outside', 'bkash', 'paid', 'delivered', NULL, '2026-10-19 05:00:00'),
(6, 'DN261105D006', 8, 'Ahmed Karim', '01711000007', 'ahmed@example.com', 'House 12, Road 5', 'Dhaka', 960.00, 60.00, 0.00, 1020.00, NULL, 'inside', 'nagad', 'paid', 'shipped', NULL, '2026-11-05 21:00:00'),
(7, 'DN261022D007', 3, 'Fatema Akter', '01711000002', 'fatema@example.com', 'House 12, Road 5', 'Uttara', 645.00, 60.00, 0.00, 705.00, NULL, 'inside', 'bkash', 'paid', 'delivered', NULL, '2026-10-22 22:00:00'),
(8, 'DN261020D008', 9, 'Mitu Sarker', '01811000008', 'mitu@example.com', 'House 12, Road 5', 'Rajshahi', 540.00, 60.00, 0.00, 600.00, NULL, 'inside', 'cod', 'unpaid', 'cancelled', NULL, '2026-10-20 18:00:00'),
(9, 'DN261017D009', 4, 'Md. Hasan', '01811000003', 'md.@example.com', 'House 12, Road 5', 'Chattogram', 1065.00, 0.00, 0.00, 1065.00, NULL, 'inside', 'bkash', 'paid', 'delivered', NULL, '2026-10-17 03:00:00'),
(10, 'DN261018D010', 7, 'Sabrina Rahman', '01511000006', 'sabrina@example.com', 'House 12, Road 5', 'Gazipur', 705.00, 60.00, 0.00, 765.00, NULL, 'inside', 'cod', 'paid', 'delivered', NULL, '2026-10-18 23:00:00'),
(11, 'DN261102D011', 2, 'Rahim Ahmed', '01711000001', 'rahim@example.com', 'House 12, Road 5', 'Dhaka', 760.00, 120.00, 0.00, 880.00, NULL, 'outside', 'cod', 'unpaid', 'cancelled', NULL, '2026-11-02 03:00:00'),
(12, 'DN261018D012', 2, 'Rahim Ahmed', '01711000001', 'rahim@example.com', 'House 12, Road 5', 'Dhaka', 880.00, 120.00, 0.00, 1000.00, NULL, 'outside', 'cod', 'unpaid', 'confirmed', NULL, '2026-10-18 20:00:00'),
(13, 'DN261108D013', 9, 'Mitu Sarker', '01811000008', 'mitu@example.com', 'House 12, Road 5', 'Rajshahi', 465.00, 60.00, 0.00, 525.00, NULL, 'inside', 'cod', 'unpaid', 'pending', NULL, '2026-11-08 06:00:00'),
(14, 'DN261024D014', 7, 'Sabrina Rahman', '01511000006', 'sabrina@example.com', 'House 12, Road 5', 'Gazipur', 1490.00, 0.00, 0.00, 1490.00, NULL, 'inside', 'cod', 'unpaid', 'cancelled', NULL, '2026-10-24 09:00:00'),
(15, 'DN261013D015', 9, 'Mitu Sarker', '01811000008', 'mitu@example.com', 'House 12, Road 5', 'Rajshahi', 550.00, 120.00, 0.00, 670.00, NULL, 'outside', 'cod', 'unpaid', 'shipped', NULL, '2026-10-13 21:00:00'),
(16, 'DN261025D016', 5, 'Nusrat Jahan', '01911000004', 'nusrat@example.com', 'House 12, Road 5', 'Dhaka', 2050.00, 0.00, 0.00, 2050.00, NULL, 'inside', 'bkash', 'paid', 'pending', NULL, '2026-10-25 12:00:00'),
(17, 'DN261017D017', 7, 'Sabrina Rahman', '01511000006', 'sabrina@example.com', 'House 12, Road 5', 'Gazipur', 1760.00, 0.00, 176.00, 1584.00, 'WELCOME10', 'outside', 'cod', 'unpaid', 'shipped', NULL, '2026-10-17 05:00:00'),
(18, 'DN261016D018', 9, 'Mitu Sarker', '01811000008', 'mitu@example.com', 'House 12, Road 5', 'Rajshahi', 1150.00, 0.00, 115.00, 1035.00, 'WELCOME10', 'inside', 'nagad', 'paid', 'delivered', NULL, '2026-10-16 04:00:00'),
(19, 'DN261104D019', 5, 'Nusrat Jahan', '01911000004', 'nusrat@example.com', 'House 12, Road 5', 'Dhaka', 1380.00, 0.00, 0.00, 1380.00, NULL, 'inside', 'cod', 'paid', 'delivered', NULL, '2026-11-04 02:00:00'),
(20, 'DN261026D020', 8, 'Ahmed Karim', '01711000007', 'ahmed@example.com', 'House 12, Road 5', 'Dhaka', 1450.00, 0.00, 145.00, 1305.00, 'WELCOME10', 'inside', 'nagad', 'paid', 'delivered', NULL, '2026-10-26 07:00:00'),
(21, 'DN261015D021', 4, 'Md. Hasan', '01811000003', 'md.@example.com', 'House 12, Road 5', 'Chattogram', 2355.00, 0.00, 0.00, 2355.00, NULL, 'outside', 'cod', 'paid', 'delivered', NULL, '2026-10-15 16:00:00'),
(22, 'DN261028D022', 5, 'Nusrat Jahan', '01911000004', 'nusrat@example.com', 'House 12, Road 5', 'Dhaka', 220.00, 60.00, 0.00, 280.00, NULL, 'inside', 'bkash', 'paid', 'pending', NULL, '2026-10-28 06:00:00'),
(23, 'DN261101D023', 8, 'Ahmed Karim', '01711000007', 'ahmed@example.com', 'House 12, Road 5', 'Dhaka', 580.00, 120.00, 0.00, 700.00, NULL, 'outside', 'nagad', 'paid', 'confirmed', NULL, '2026-11-01 19:00:00'),
(24, 'DN261014D024', 4, 'Md. Hasan', '01811000003', 'md.@example.com', 'House 12, Road 5', 'Chattogram', 695.00, 60.00, 0.00, 755.00, NULL, 'inside', 'cod', 'paid', 'delivered', NULL, '2026-10-14 20:00:00'),
(25, 'DN261024D025', 7, 'Sabrina Rahman', '01511000006', 'sabrina@example.com', 'House 12, Road 5', 'Gazipur', 780.00, 60.00, 0.00, 840.00, NULL, 'inside', 'cod', 'paid', 'delivered', NULL, '2026-10-24 11:00:00'),
(26, 'DN261017D026', 9, 'Mitu Sarker', '01811000008', 'mitu@example.com', 'House 12, Road 5', 'Rajshahi', 855.00, 120.00, 0.00, 975.00, NULL, 'outside', 'cod', 'unpaid', 'shipped', NULL, '2026-10-17 10:00:00'),
(27, 'DN261018D027', 6, 'Tariqul Islam', '01611000005', 'tariqul@example.com', 'House 12, Road 5', 'Sylhet', 1200.00, 0.00, 120.00, 1080.00, 'WELCOME10', 'inside', 'bkash', 'paid', 'delivered', NULL, '2026-10-18 20:00:00'),
(28, 'DN261016D028', 4, 'Md. Hasan', '01811000003', 'md.@example.com', 'House 12, Road 5', 'Chattogram', 835.00, 60.00, 0.00, 895.00, NULL, 'inside', 'cod', 'unpaid', 'confirmed', NULL, '2026-10-16 09:00:00'),
(29, 'DN261104D029', 8, 'Ahmed Karim', '01711000007', 'ahmed@example.com', 'House 12, Road 5', 'Dhaka', 780.00, 60.00, 0.00, 840.00, NULL, 'inside', 'nagad', 'paid', 'shipped', NULL, '2026-11-04 23:00:00'),
(30, 'DN261027D030', 8, 'Ahmed Karim', '01711000007', 'ahmed@example.com', 'House 12, Road 5', 'Dhaka', 465.00, 60.00, 0.00, 525.00, NULL, 'inside', 'cod', 'paid', 'delivered', NULL, '2026-10-27 12:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT 0,
  `variant_id` int(11) DEFAULT 0,
  `variant` varchar(80) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT 0.00,
  `qty` int(11) DEFAULT 1,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `variant_id`, `variant`, `name`, `price`, `cost`, `qty`, `image`) VALUES
(1, 1, 2, 5, '100 g', 'Dried Mango Bar 200g', 160.00, 105.00, 3, NULL),
(2, 1, 3, 9, '30 g', 'Dried Orange Slices 150g', 90.00, 59.00, 3, NULL),
(3, 1, 4, 12, '50 g', 'Dried Banana Chips 250g', 80.00, 50.00, 1, NULL),
(4, 2, 4, 12, '50 g', 'Dried Banana Chips 250g', 80.00, 50.00, 3, NULL),
(5, 3, 14, 40, '250 g', 'Dried Apricot 250g', 380.00, 270.00, 1, NULL),
(6, 3, 5, 13, '100 g', 'Dried Strawberry 100g', 450.00, 310.00, 2, NULL),
(7, 4, 2, 4, '200 g', 'Dried Mango Bar 200g', 290.00, 190.00, 3, NULL),
(8, 4, 7, 20, '125 g', 'Dried Pineapple Rings 250g', 230.00, 160.00, 2, NULL),
(9, 4, 16, 47, '250 g', 'Premium Almonds 500g', 340.00, 248.00, 3, NULL),
(10, 5, 10, 29, '500 g', 'Premium Dates 1kg', 250.00, 176.00, 3, NULL),
(11, 5, 17, 50, '100 g', 'Dried Cranberries 200g', 185.00, 127.00, 2, NULL),
(12, 5, 16, 46, '500 g', 'Premium Almonds 500g', 620.00, 450.00, 1, NULL),
(13, 6, 3, 7, '150 g', 'Dried Orange Slices 150g', 320.00, 210.00, 3, NULL),
(14, 7, 9, 26, '75 g', 'Dried Kiwi Slices 150g', 215.00, 148.00, 3, NULL),
(15, 8, 13, 39, '80 g', 'Mixed Dried Fruit Pack 400g', 155.00, 112.00, 2, NULL),
(16, 8, 12, 35, '250 g', 'Organic Raisins 500g', 230.00, 165.00, 1, NULL),
(17, 9, 8, 23, '125 g', 'Dried Papaya Cubes 250g', 200.00, 132.00, 1, NULL),
(18, 9, 13, 39, '80 g', 'Mixed Dried Fruit Pack 400g', 155.00, 112.00, 2, NULL),
(19, 9, 17, 50, '100 g', 'Dried Cranberries 200g', 185.00, 127.00, 3, NULL),
(20, 10, 13, 39, '80 g', 'Mixed Dried Fruit Pack 400g', 155.00, 112.00, 1, NULL),
(21, 10, 8, 24, '50 g', 'Dried Papaya Cubes 250g', 100.00, 67.00, 1, NULL),
(22, 10, 5, 13, '100 g', 'Dried Strawberry 100g', 450.00, 310.00, 1, NULL),
(23, 11, 14, 40, '250 g', 'Dried Apricot 250g', 380.00, 270.00, 2, NULL),
(24, 12, 18, 53, '150 g', 'Tropical Trail Mix 300g', 285.00, 198.00, 2, NULL),
(25, 12, 13, 38, '200 g', 'Mixed Dried Fruit Pack 400g', 310.00, 220.00, 1, NULL),
(26, 13, 4, 11, '125 g', 'Dried Banana Chips 250g', 155.00, 99.00, 3, NULL),
(27, 14, 1, 1, '250 g', 'Dried Mango Slices 250g', 380.00, 260.00, 2, NULL),
(28, 14, 3, 9, '30 g', 'Dried Orange Slices 150g', 90.00, 59.00, 3, NULL),
(29, 14, 7, 20, '125 g', 'Dried Pineapple Rings 250g', 230.00, 160.00, 2, NULL),
(30, 15, 16, 48, '100 g', 'Premium Almonds 500g', 175.00, 126.00, 1, NULL),
(31, 15, 10, 30, '200 g', 'Premium Dates 1kg', 125.00, 90.00, 3, NULL),
(32, 16, 12, 34, '500 g', 'Organic Raisins 500g', 420.00, 300.00, 2, NULL),
(33, 16, 5, 13, '100 g', 'Dried Strawberry 100g', 450.00, 310.00, 1, NULL),
(34, 16, 1, 1, '250 g', 'Dried Mango Slices 250g', 380.00, 260.00, 2, NULL),
(35, 17, 8, 23, '125 g', 'Dried Papaya Cubes 250g', 200.00, 132.00, 3, NULL),
(36, 17, 7, 21, '50 g', 'Dried Pineapple Rings 250g', 120.00, 81.00, 1, NULL),
(37, 17, 18, 52, '300 g', 'Tropical Trail Mix 300g', 520.00, 360.00, 2, NULL),
(38, 18, 7, 20, '125 g', 'Dried Pineapple Rings 250g', 230.00, 160.00, 3, NULL),
(39, 18, 12, 35, '250 g', 'Organic Raisins 500g', 230.00, 165.00, 2, NULL),
(40, 19, 1, 1, '250 g', 'Dried Mango Slices 250g', 380.00, 260.00, 2, NULL),
(41, 19, 16, 46, '500 g', 'Premium Almonds 500g', 620.00, 450.00, 1, NULL),
(42, 20, 11, 32, '125 g', 'Dried Figs (Anjeer) 250g', 285.00, 198.00, 2, NULL),
(43, 20, 3, 7, '150 g', 'Dried Orange Slices 150g', 320.00, 210.00, 1, NULL),
(44, 20, 13, 37, '400 g', 'Mixed Dried Fruit Pack 400g', 560.00, 400.00, 1, NULL),
(45, 21, 16, 46, '500 g', 'Premium Almonds 500g', 620.00, 450.00, 3, NULL),
(46, 21, 12, 36, '100 g', 'Organic Raisins 500g', 120.00, 84.00, 1, NULL),
(47, 21, 5, 15, '20 g', 'Dried Strawberry 100g', 125.00, 87.00, 3, NULL),
(48, 22, 9, 27, '30 g', 'Dried Kiwi Slices 150g', 110.00, 76.00, 2, NULL),
(49, 23, 2, 4, '200 g', 'Dried Mango Bar 200g', 290.00, 190.00, 2, NULL),
(50, 24, 5, 14, '50 g', 'Dried Strawberry 100g', 250.00, 170.00, 1, NULL),
(51, 24, 17, 49, '200 g', 'Dried Cranberries 200g', 340.00, 230.00, 1, NULL),
(52, 24, 1, 3, '50 g', 'Dried Mango Slices 250g', 105.00, 73.00, 1, NULL),
(53, 25, 17, 51, '40 g', 'Dried Cranberries 200g', 95.00, 64.00, 3, NULL),
(54, 25, 18, 54, '60 g', 'Tropical Trail Mix 300g', 145.00, 101.00, 1, NULL),
(55, 25, 16, 48, '100 g', 'Premium Almonds 500g', 175.00, 126.00, 2, NULL),
(56, 26, 17, 51, '40 g', 'Dried Cranberries 200g', 95.00, 64.00, 3, NULL),
(57, 26, 18, 53, '150 g', 'Tropical Trail Mix 300g', 285.00, 198.00, 2, NULL),
(58, 27, 7, 20, '125 g', 'Dried Pineapple Rings 250g', 230.00, 160.00, 1, NULL),
(59, 27, 15, 44, '250 g', 'Premium Cashew Nuts 500g', 360.00, 264.00, 2, NULL),
(60, 27, 5, 14, '50 g', 'Dried Strawberry 100g', 250.00, 170.00, 1, NULL),
(61, 28, 12, 36, '100 g', 'Organic Raisins 500g', 120.00, 84.00, 2, NULL),
(62, 28, 5, 15, '20 g', 'Dried Strawberry 100g', 125.00, 87.00, 3, NULL),
(63, 28, 9, 27, '30 g', 'Dried Kiwi Slices 150g', 110.00, 76.00, 2, NULL),
(64, 29, 14, 41, '125 g', 'Dried Apricot 250g', 210.00, 148.00, 2, NULL),
(65, 29, 7, 21, '50 g', 'Dried Pineapple Rings 250g', 120.00, 81.00, 3, NULL),
(66, 30, 17, 49, '200 g', 'Dried Cranberries 200g', 340.00, 230.00, 1, NULL),
(67, 30, 10, 30, '200 g', 'Premium Dates 1kg', 125.00, 90.00, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_log`
--

CREATE TABLE `order_log` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(20) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_log`
--

INSERT INTO `order_log` (`id`, `order_id`, `status`, `note`, `created_at`) VALUES
(1, 1, 'pending', 'Demo data', '2026-10-31 08:00:00'),
(2, 1, 'confirmed', 'Demo data', '2026-10-31 14:00:00'),
(3, 1, 'processing', 'Demo data', '2026-10-31 20:00:00'),
(4, 1, 'shipped', 'Demo data', '2026-11-01 02:00:00'),
(5, 1, 'delivered', 'Demo data', '2026-11-01 08:00:00'),
(6, 2, 'pending', 'Demo data', '2026-11-07 19:00:00'),
(7, 3, 'pending', 'Demo data', '2026-11-03 11:00:00'),
(8, 4, 'pending', 'Demo data', '2026-10-30 09:00:00'),
(9, 4, 'confirmed', 'Demo data', '2026-10-30 15:00:00'),
(10, 5, 'pending', 'Demo data', '2026-10-19 05:00:00'),
(11, 5, 'confirmed', 'Demo data', '2026-10-19 11:00:00'),
(12, 5, 'processing', 'Demo data', '2026-10-19 17:00:00'),
(13, 5, 'shipped', 'Demo data', '2026-10-19 23:00:00'),
(14, 5, 'delivered', 'Demo data', '2026-10-20 05:00:00'),
(15, 6, 'pending', 'Demo data', '2026-11-05 21:00:00'),
(16, 6, 'confirmed', 'Demo data', '2026-11-06 03:00:00'),
(17, 6, 'processing', 'Demo data', '2026-11-06 09:00:00'),
(18, 6, 'shipped', 'Demo data', '2026-11-06 15:00:00'),
(19, 7, 'pending', 'Demo data', '2026-10-22 22:00:00'),
(20, 7, 'confirmed', 'Demo data', '2026-10-23 04:00:00'),
(21, 7, 'processing', 'Demo data', '2026-10-23 10:00:00'),
(22, 7, 'shipped', 'Demo data', '2026-10-23 16:00:00'),
(23, 7, 'delivered', 'Demo data', '2026-10-23 22:00:00'),
(24, 8, 'pending', 'Demo data', '2026-10-20 18:00:00'),
(25, 8, 'cancelled', 'Demo data', '2026-10-21 00:00:00'),
(26, 9, 'pending', 'Demo data', '2026-10-17 03:00:00'),
(27, 9, 'confirmed', 'Demo data', '2026-10-17 09:00:00'),
(28, 9, 'processing', 'Demo data', '2026-10-17 15:00:00'),
(29, 9, 'shipped', 'Demo data', '2026-10-17 21:00:00'),
(30, 9, 'delivered', 'Demo data', '2026-10-18 03:00:00'),
(31, 10, 'pending', 'Demo data', '2026-10-18 23:00:00'),
(32, 10, 'confirmed', 'Demo data', '2026-10-19 05:00:00'),
(33, 10, 'processing', 'Demo data', '2026-10-19 11:00:00'),
(34, 10, 'shipped', 'Demo data', '2026-10-19 17:00:00'),
(35, 10, 'delivered', 'Demo data', '2026-10-19 23:00:00'),
(36, 11, 'pending', 'Demo data', '2026-11-02 03:00:00'),
(37, 11, 'cancelled', 'Demo data', '2026-11-02 09:00:00'),
(38, 12, 'pending', 'Demo data', '2026-10-18 20:00:00'),
(39, 12, 'confirmed', 'Demo data', '2026-10-19 02:00:00'),
(40, 13, 'pending', 'Demo data', '2026-11-08 06:00:00'),
(41, 14, 'pending', 'Demo data', '2026-10-24 09:00:00'),
(42, 14, 'cancelled', 'Demo data', '2026-10-24 15:00:00'),
(43, 15, 'pending', 'Demo data', '2026-10-13 21:00:00'),
(44, 15, 'confirmed', 'Demo data', '2026-10-14 03:00:00'),
(45, 15, 'processing', 'Demo data', '2026-10-14 09:00:00'),
(46, 15, 'shipped', 'Demo data', '2026-10-14 15:00:00'),
(47, 16, 'pending', 'Demo data', '2026-10-25 12:00:00'),
(48, 17, 'pending', 'Demo data', '2026-10-17 05:00:00'),
(49, 17, 'confirmed', 'Demo data', '2026-10-17 11:00:00'),
(50, 17, 'processing', 'Demo data', '2026-10-17 17:00:00'),
(51, 17, 'shipped', 'Demo data', '2026-10-17 23:00:00'),
(52, 18, 'pending', 'Demo data', '2026-10-16 04:00:00'),
(53, 18, 'confirmed', 'Demo data', '2026-10-16 10:00:00'),
(54, 18, 'processing', 'Demo data', '2026-10-16 16:00:00'),
(55, 18, 'shipped', 'Demo data', '2026-10-16 22:00:00'),
(56, 18, 'delivered', 'Demo data', '2026-10-17 04:00:00'),
(57, 19, 'pending', 'Demo data', '2026-11-04 02:00:00'),
(58, 19, 'confirmed', 'Demo data', '2026-11-04 08:00:00'),
(59, 19, 'processing', 'Demo data', '2026-11-04 14:00:00'),
(60, 19, 'shipped', 'Demo data', '2026-11-04 20:00:00'),
(61, 19, 'delivered', 'Demo data', '2026-11-05 02:00:00'),
(62, 20, 'pending', 'Demo data', '2026-10-26 07:00:00'),
(63, 20, 'confirmed', 'Demo data', '2026-10-26 13:00:00'),
(64, 20, 'processing', 'Demo data', '2026-10-26 19:00:00'),
(65, 20, 'shipped', 'Demo data', '2026-10-27 01:00:00'),
(66, 20, 'delivered', 'Demo data', '2026-10-27 07:00:00'),
(67, 21, 'pending', 'Demo data', '2026-10-15 16:00:00'),
(68, 21, 'confirmed', 'Demo data', '2026-10-15 22:00:00'),
(69, 21, 'processing', 'Demo data', '2026-10-16 04:00:00'),
(70, 21, 'shipped', 'Demo data', '2026-10-16 10:00:00'),
(71, 21, 'delivered', 'Demo data', '2026-10-16 16:00:00'),
(72, 22, 'pending', 'Demo data', '2026-10-28 06:00:00'),
(73, 23, 'pending', 'Demo data', '2026-11-01 19:00:00'),
(74, 23, 'confirmed', 'Demo data', '2026-11-02 01:00:00'),
(75, 24, 'pending', 'Demo data', '2026-10-14 20:00:00'),
(76, 24, 'confirmed', 'Demo data', '2026-10-15 02:00:00'),
(77, 24, 'processing', 'Demo data', '2026-10-15 08:00:00'),
(78, 24, 'shipped', 'Demo data', '2026-10-15 14:00:00'),
(79, 24, 'delivered', 'Demo data', '2026-10-15 20:00:00'),
(80, 25, 'pending', 'Demo data', '2026-10-24 11:00:00'),
(81, 25, 'confirmed', 'Demo data', '2026-10-24 17:00:00'),
(82, 25, 'processing', 'Demo data', '2026-10-24 23:00:00'),
(83, 25, 'shipped', 'Demo data', '2026-10-25 05:00:00'),
(84, 25, 'delivered', 'Demo data', '2026-10-25 11:00:00'),
(85, 26, 'pending', 'Demo data', '2026-10-17 10:00:00'),
(86, 27, 'pending', 'Demo data', '2026-10-18 20:00:00'),
(87, 27, 'confirmed', 'Demo data', '2026-10-19 02:00:00'),
(88, 27, 'processing', 'Demo data', '2026-10-19 08:00:00'),
(89, 27, 'shipped', 'Demo data', '2026-10-19 14:00:00'),
(90, 27, 'delivered', 'Demo data', '2026-10-19 20:00:00'),
(91, 28, 'pending', 'Demo data', '2026-10-16 09:00:00'),
(92, 28, 'confirmed', 'Demo data', '2026-10-16 15:00:00'),
(93, 29, 'pending', 'Demo data', '2026-11-04 23:00:00'),
(94, 29, 'confirmed', 'Demo data', '2026-11-05 05:00:00'),
(95, 29, 'processing', 'Demo data', '2026-11-05 11:00:00'),
(96, 29, 'shipped', 'Demo data', '2026-11-05 17:00:00'),
(97, 30, 'pending', 'Demo data', '2026-10-27 12:00:00'),
(98, 30, 'confirmed', 'Demo data', '2026-10-27 18:00:00'),
(99, 30, 'processing', 'Demo data', '2026-10-28 00:00:00'),
(100, 30, 'shipped', 'Demo data', '2026-10-28 06:00:00'),
(101, 30, 'delivered', 'Demo data', '2026-10-28 12:00:00'),
(102, 26, 'shipped', 'Updated by Super Admin', '2026-11-11 16:06:47');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `title_en` varchar(200) DEFAULT NULL,
  `title_bn` varchar(200) DEFAULT NULL,
  `body_en` mediumtext DEFAULT NULL,
  `body_bn` mediumtext DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `slug`, `title_en`, `title_bn`, `body_en`, `body_bn`, `status`) VALUES
(1, 'about', 'About Us', 'আমাদের সম্পর্কে', '<p>DryNutri is a healthy-food brand delivering premium nuts, dried fruits, seeds and superfoods across Bangladesh. Edit this page from Admin → Pages.</p>', '<p>ড্রাইনিউট্রি একটি স্বাস্থ্যকর খাদ্য ব্র্যান্ড। অ্যাডমিন প্যানেল থেকে এই পেজ এডিট করুন।</p>', 1),
(2, 'return-policy', 'Return Policy', 'রিটার্ন পলিসি', '<p>If you receive a damaged or incorrect item, contact us within 48 hours for a replacement or refund.</p>', '<p>ক্ষতিগ্রস্ত বা ভুল পণ্য পেলে ৪৮ ঘণ্টার মধ্যে যোগাযোগ করুন।</p>', 1),
(3, 'shipping', 'Shipping Info', 'শিপিং তথ্য', '<p>We deliver all over Bangladesh. Dhaka: 1-2 days, outside Dhaka: 2-4 days.</p>', '<p>সারা বাংলাদেশে ডেলিভারি। ঢাকায় ১-২ দিন, ঢাকার বাইরে ২-৪ দিন।</p>', 1),
(4, 'faq', 'FAQ', 'সাধারণ জিজ্ঞাসা', '<p><b>How can I pay?</b> Cash on delivery, bKash and Nagad.</p>', '<p><b>কীভাবে পেমেন্ট করব?</b> ক্যাশ অন ডেলিভারি, বিকাশ ও নগদ।</p>', 1),
(5, 'terms', 'Terms & Conditions', 'শর্তাবলী', '<p>By using this website you agree to our terms. Edit from Admin → Pages.</p>', '<p>এই ওয়েবসাইট ব্যবহার করে আপনি আমাদের শর্তাবলীতে সম্মত হচ্ছেন।</p>', 1),
(6, 'privacy', 'Privacy Policy', 'গোপনীয়তা নীতি', '<p>We respect your privacy and never sell your personal data.</p>', '<p>আমরা আপনার গোপনীয়তাকে সম্মান করি।</p>', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_requests`
--

CREATE TABLE `password_reset_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `token_hash` char(64) DEFAULT NULL,
  `request_ip` varchar(45) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_reset_requests`
--

INSERT INTO `password_reset_requests` (`id`, `user_id`, `token_hash`, `request_ip`, `created_at`, `expires_at`) VALUES
(1, NULL, NULL, '::1', '2026-11-11 12:53:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment_logos`
--

CREATE TABLE `payment_logos` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(120) NOT NULL,
  `image` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL DEFAULT 0,
  `name_en` varchar(200) NOT NULL,
  `name_bn` varchar(200) DEFAULT NULL,
  `slug` varchar(220) NOT NULL,
  `short_en` varchar(300) DEFAULT NULL,
  `short_bn` varchar(300) DEFAULT NULL,
  `desc_en` text DEFAULT NULL,
  `desc_bn` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `old_price` decimal(10,2) DEFAULT 0.00,
  `cost_price` decimal(10,2) DEFAULT 0.00,
  `stock` int(11) DEFAULT 0,
  `unit` varchar(30) DEFAULT NULL,
  `brand` varchar(80) DEFAULT NULL,
  `flag` varchar(20) DEFAULT '',
  `is_featured` tinyint(1) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `gallery` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `rating` decimal(3,2) DEFAULT 0.00,
  `rating_count` int(11) DEFAULT 0,
  `sold` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name_en`, `name_bn`, `slug`, `short_en`, `short_bn`, `desc_en`, `desc_bn`, `price`, `old_price`, `cost_price`, `stock`, `unit`, `brand`, `flag`, `is_featured`, `image`, `gallery`, `status`, `rating`, `rating_count`, `sold`, `created_at`) VALUES
(1, 9, 'Dried Mango Slices 250g', 'শুকনো আমের স্লাইস ২৫০গ্রাম', 'dried-mango-slices-250g', 'Golden, chewy and naturally sweet mango.', 'সোনালি, নরম ও প্রাকৃতিক মিষ্টি আম।', 'Golden, chewy and naturally sweet mango. Naturally dried, no preservatives, packed fresh.', 'সোনালি, নরম ও প্রাকৃতিক মিষ্টি আম। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।', 105.00, 125.00, 73.00, 150, '50 g', 'DryNutri', 'best', 1, NULL, NULL, 1, 4.90, 42, 327, '2026-11-11 10:58:20'),
(2, 10, 'Dried Mango Bar 200g', 'ম্যাঙ্গো বার ২০০গ্রাম', 'dried-mango-bar-200g', 'Pure mango pulp bar - a sweet healthy snack.', 'খাঁটি আমের পাল্পে তৈরি মিষ্টি স্ন্যাক।', 'Pure mango pulp bar - a sweet healthy snack. Naturally dried, no preservatives, packed fresh.', 'খাঁটি আমের পাল্পে তৈরি মিষ্টি স্ন্যাক। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।', 80.00, 95.00, 53.00, 120, '40 g', 'DryNutri', 'new', 0, NULL, NULL, 1, 4.70, 18, 118, '2026-11-11 10:58:20'),
(3, 11, 'Dried Orange Slices 150g', 'শুকনো কমলার স্লাইস ১৫০গ্রাম', 'dried-orange-slices-150g', 'Tangy citrus slices full of vitamin C.', 'ভিটামিন সি সমৃদ্ধ টক-মিষ্টি কমলার স্লাইস।', 'Tangy citrus slices full of vitamin C. Naturally dried, no preservatives, packed fresh.', 'ভিটামিন সি সমৃদ্ধ টক-মিষ্টি কমলার স্লাইস। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।', 90.00, 105.00, 59.00, 99, '30 g', 'DryNutri', 'new', 1, NULL, NULL, 1, 4.80, 25, 150, '2026-11-11 10:58:20'),
(4, 12, 'Dried Banana Chips 250g', 'শুকনো কলার চিপস ২৫০গ্রাম', 'dried-banana-chips-250g', 'Crunchy banana chips, no added sugar.', 'মচমচে কলার চিপস, চিনি ছাড়া।', 'Crunchy banana chips, no added sugar. Naturally dried, no preservatives, packed fresh.', 'মচমচে কলার চিপস, চিনি ছাড়া। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।', 80.00, 90.00, 50.00, 180, '50 g', 'DryNutri', 'best', 1, NULL, NULL, 1, 4.80, 36, 297, '2026-11-11 10:58:20'),
(5, 4, 'Dried Strawberry 100g', 'শুকনো স্ট্রবেরি ১০০গ্রাম', 'dried-strawberry-100g', 'Sweet-tart strawberries, freeze-dried crisp.', 'মিষ্টি-টক ক্রিস্পি স্ট্রবেরি।', 'Sweet-tart strawberries, freeze-dried crisp. Naturally dried, no preservatives, packed fresh.', 'মিষ্টি-টক ক্রিস্পি স্ট্রবেরি। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।', 125.00, 145.00, 87.00, 69, '20 g', 'DryNutri', 'new', 1, NULL, NULL, 1, 4.70, 15, 107, '2026-11-11 10:58:20'),
(6, 4, 'Dried Blueberry 150g', 'শুকনো ব্লুবেরি ১৫০গ্রাম', 'dried-blueberry-150g', 'Antioxidant-rich blueberries.', 'অ্যান্টিঅক্সিডেন্ট সমৃদ্ধ ব্লুবেরি।', 'Antioxidant-rich blueberries. Naturally dried, no preservatives, packed fresh.', 'অ্যান্টিঅক্সিডেন্ট সমৃদ্ধ ব্লুবেরি। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।', 155.00, 180.00, 109.00, 60, '30 g', 'DryNutri', 'organic', 1, NULL, NULL, 1, 4.80, 21, 80, '2026-11-11 10:58:20'),
(7, 5, 'Dried Pineapple Rings 250g', 'শুকনো আনারস রিং ২৫০গ্রাম', 'dried-pineapple-rings-250g', 'Tropical pineapple rings, naturally sweet.', 'প্রাকৃতিক মিষ্টি ট্রপিক্যাল আনারস রিং।', 'Tropical pineapple rings, naturally sweet. Naturally dried, no preservatives, packed fresh.', 'প্রাকৃতিক মিষ্টি ট্রপিক্যাল আনারস রিং। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।', 120.00, 135.00, 81.00, 108, '50 g', 'DryNutri', 'best', 1, NULL, NULL, 1, 4.80, 28, 212, '2026-11-11 10:58:20'),
(8, 5, 'Dried Papaya Cubes 250g', 'শুকনো পেঁপে কিউব ২৫০গ্রাম', 'dried-papaya-cubes-250g', 'Soft papaya cubes, great for digestion.', 'হজমে সহায়ক নরম পেঁপে কিউব।', 'Soft papaya cubes, great for digestion. Naturally dried, no preservatives, packed fresh.', 'হজমে সহায়ক নরম পেঁপে কিউব। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।', 100.00, 120.00, 67.00, 90, '50 g', 'DryNutri', '', 1, NULL, NULL, 1, 4.60, 12, 90, '2026-11-11 10:58:20'),
(9, 5, 'Dried Kiwi Slices 150g', 'শুকনো কিউই স্লাইস ১৫০গ্রাম', 'dried-kiwi-slices-150g', 'Tangy green kiwi slices.', 'টক-মিষ্টি সবুজ কিউই স্লাইস।', 'Tangy green kiwi slices. Naturally dried, no preservatives, packed fresh.', 'টক-মিষ্টি সবুজ কিউই স্লাইস। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।', 110.00, 125.00, 76.00, 63, '30 g', 'DryNutri', 'new', 0, NULL, NULL, 1, 4.60, 9, 67, '2026-11-11 10:58:20'),
(10, 6, 'Premium Dates 1kg', 'প্রিমিয়াম খেজুর ১কেজি', 'premium-dates-1kg', 'Soft, juicy and naturally sweet dates.', 'নরম, রসালো ও প্রাকৃতিক মিষ্টি খেজুর।', 'Soft, juicy and naturally sweet dates. Naturally dried, no preservatives, packed fresh.', 'নরম, রসালো ও প্রাকৃতিক মিষ্টি খেজুর। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।', 125.00, 155.00, 90.00, 198, '200 g', 'DryNutri', 'best', 1, NULL, NULL, 1, 4.90, 31, 317, '2026-11-11 10:58:20'),
(11, 6, 'Dried Figs (Anjeer) 250g', 'শুকনো ডুমুর (আঞ্জির) ২৫০গ্রাম', 'dried-figs-anjeer-250g', 'Honey-sweet figs rich in fibre.', 'ফাইবার সমৃদ্ধ মধুর মতো মিষ্টি ডুমুর।', 'Honey-sweet figs rich in fibre. Naturally dried, no preservatives, packed fresh.', 'ফাইবার সমৃদ্ধ মধুর মতো মিষ্টি ডুমুর। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।', 145.00, 170.00, 101.00, 78, '50 g', 'DryNutri', 'organic', 0, NULL, NULL, 1, 4.70, 14, 72, '2026-11-11 10:58:20'),
(12, 8, 'Organic Raisins 500g', 'অর্গানিক কিশমিশ ৫০০গ্রাম', 'organic-raisins-500g', 'Seedless organic raisins.', 'বীজহীন অর্গানিক কিশমিশ।', 'Seedless organic raisins. Naturally dried, no preservatives, packed fresh.', 'বীজহীন অর্গানিক কিশমিশ। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।', 120.00, 135.00, 84.00, 24, '100 g', 'DryNutri', 'organic', 0, NULL, NULL, 1, 4.40, 5, 48, '2026-11-11 10:58:20'),
(13, 8, 'Mixed Dried Fruit Pack 400g', 'মিক্সড শুকনো ফল ৪০০গ্রাম', 'mixed-dried-fruit-400g', 'Mango, orange, banana, pineapple & more.', 'আম, কমলা, কলা, আনারসসহ আরও অনেক ফল।', 'Mango, orange, banana, pineapple & more. Naturally dried, no preservatives, packed fresh.', 'আম, কমলা, কলা, আনারসসহ আরও অনেক ফল। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।', 155.00, 180.00, 112.00, 90, '80 g', 'DryNutri', 'best', 1, NULL, NULL, 1, 4.90, 33, 267, '2026-11-11 10:58:20'),
(14, 8, 'Dried Apricot 250g', 'শুকনো এপ্রিকট ২৫০গ্রাম', 'dried-apricot-250g', 'Sun-dried apricots with no added sugar.', 'চিনি ছাড়া রোদে শুকানো এপ্রিকট।', 'Sun-dried apricots with no added sugar. Naturally dried, no preservatives, packed fresh.', 'চিনি ছাড়া রোদে শুকানো এপ্রিকট। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।', 105.00, 125.00, 76.00, 78, '50 g', 'DryNutri', '', 0, NULL, NULL, 1, 4.50, 6, 55, '2026-11-11 10:58:20'),
(15, 13, 'Premium Cashew Nuts 500g', 'প্রিমিয়াম কাজুবাদাম ৫০০গ্রাম', 'premium-cashew-nuts-500g', 'Crunchy, creamy and naturally sweet.', 'মচমচে, ক্রিমি ও প্রাকৃতিক মিষ্টি।', 'Crunchy, creamy and naturally sweet. Naturally dried, no preservatives, packed fresh.', 'মচমচে, ক্রিমি ও প্রাকৃতিক মিষ্টি। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।', 180.00, 210.00, 134.00, 120, '100 g', 'DryNutri', 'best', 1, NULL, NULL, 1, 4.80, 25, 252, '2026-11-11 10:58:20'),
(16, 14, 'Premium Almonds 500g', 'প্রিমিয়াম কাঠবাদাম ৫০০গ্রাম', 'premium-almonds-500g', 'Fresh almonds rich in vitamin E.', 'ভিটামিন ই সমৃদ্ধ তাজা কাঠবাদাম।', 'Fresh almonds rich in vitamin E. Naturally dried, no preservatives, packed fresh.', 'ভিটামিন ই সমৃদ্ধ তাজা কাঠবাদাম। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।', 175.00, 195.00, 126.00, 90, '100 g', 'DryNutri', '', 1, NULL, NULL, 1, 4.80, 18, 191, '2026-11-11 10:58:20'),
(17, 4, 'Dried Cranberries 200g', 'শুকনো ক্র্যানবেরি ২০০গ্রাম', 'dried-cranberries-200g', 'Ruby-red cranberries for snacking and baking.', 'স্ন্যাক ও বেকিংয়ের জন্য লাল ক্র্যানবেরি।', 'Ruby-red cranberries for snacking and baking. Naturally dried, no preservatives, packed fresh.', 'স্ন্যাক ও বেকিংয়ের জন্য লাল ক্র্যানবেরি। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।', 95.00, 110.00, 64.00, 75, '40 g', 'DryNutri', '', 0, NULL, NULL, 1, 4.50, 8, 68, '2026-11-11 10:58:20'),
(18, 8, 'Tropical Trail Mix 300g', 'ট্রপিক্যাল ট্রেইল মিক্স ৩০০গ্রাম', 'tropical-trail-mix-300g', 'Dried fruit, nuts and seeds energy mix.', 'শুকনো ফল, বাদাম ও বীজের এনার্জি মিক্স।', 'Dried fruit, nuts and seeds energy mix. Naturally dried, no preservatives, packed fresh.', 'শুকনো ফল, বাদাম ও বীজের এনার্জি মিক্স। প্রাকৃতিকভাবে শুকানো, কোনো প্রিজারভেটিভ নেই।', 145.00, 170.00, 101.00, 54, '60 g', 'DryNutri', 'best', 0, '261111-22cca3de1e.png', '261111-0fe2a09db7.png,261111-ccbe7d8489.png,261111-f9317b1f94.png', 1, 4.80, 16, 137, '2026-11-11 10:58:20');

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `label_en` varchar(60) NOT NULL,
  `label_bn` varchar(60) DEFAULT NULL,
  `grams` int(11) DEFAULT 0,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `old_price` decimal(10,2) DEFAULT 0.00,
  `cost_price` decimal(10,2) DEFAULT 0.00,
  `stock` int(11) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `label_en`, `label_bn`, `grams`, `price`, `old_price`, `cost_price`, `stock`, `sort_order`, `status`) VALUES
(1, 1, '250 g', '২৫০ গ্রাম', 250, 380.00, 450.00, 260.00, 50, 1, 1),
(2, 1, '125 g', '১২৫ গ্রাম', 125, 210.00, 250.00, 143.00, 50, 2, 1),
(3, 1, '50 g', '৫০ গ্রাম', 50, 105.00, 125.00, 73.00, 50, 3, 1),
(4, 2, '200 g', '২০০ গ্রাম', 200, 290.00, 340.00, 190.00, 40, 1, 1),
(5, 2, '100 g', '১০০ গ্রাম', 100, 160.00, 185.00, 105.00, 40, 2, 1),
(6, 2, '40 g', '৪০ গ্রাম', 40, 80.00, 95.00, 53.00, 40, 3, 1),
(7, 3, '150 g', '১৫০ গ্রাম', 150, 320.00, 380.00, 210.00, 33, 1, 1),
(8, 3, '75 g', '৭৫ গ্রাম', 75, 175.00, 210.00, 116.00, 33, 2, 1),
(9, 3, '30 g', '৩০ গ্রাম', 30, 90.00, 105.00, 59.00, 33, 3, 1),
(10, 4, '250 g', '২৫০ গ্রাম', 250, 280.00, 330.00, 180.00, 60, 1, 1),
(11, 4, '125 g', '১২৫ গ্রাম', 125, 155.00, 180.00, 99.00, 60, 2, 1),
(12, 4, '50 g', '৫০ গ্রাম', 50, 80.00, 90.00, 50.00, 60, 3, 1),
(13, 5, '100 g', '১০০ গ্রাম', 100, 450.00, 520.00, 310.00, 23, 1, 1),
(14, 5, '50 g', '৫০ গ্রাম', 50, 250.00, 285.00, 170.00, 23, 2, 1),
(15, 5, '20 g', '২০ গ্রাম', 20, 125.00, 145.00, 87.00, 23, 3, 1),
(16, 6, '150 g', '১৫০ গ্রাম', 150, 560.00, 650.00, 390.00, 20, 1, 1),
(17, 6, '75 g', '৭৫ গ্রাম', 75, 310.00, 360.00, 215.00, 20, 2, 1),
(18, 6, '30 g', '৩০ গ্রাম', 30, 155.00, 180.00, 109.00, 20, 3, 1),
(19, 7, '250 g', '২৫০ গ্রাম', 250, 420.00, 480.00, 290.00, 36, 1, 1),
(20, 7, '125 g', '১২৫ গ্রাম', 125, 230.00, 265.00, 160.00, 36, 2, 1),
(21, 7, '50 g', '৫০ গ্রাম', 50, 120.00, 135.00, 81.00, 36, 3, 1),
(22, 8, '250 g', '২৫০ গ্রাম', 250, 360.00, 420.00, 240.00, 30, 1, 1),
(23, 8, '125 g', '১২৫ গ্রাম', 125, 200.00, 230.00, 132.00, 30, 2, 1),
(24, 8, '50 g', '৫০ গ্রাম', 50, 100.00, 120.00, 67.00, 30, 3, 1),
(25, 9, '150 g', '১৫০ গ্রাম', 150, 390.00, 450.00, 270.00, 21, 1, 1),
(26, 9, '75 g', '৭৫ গ্রাম', 75, 215.00, 250.00, 148.00, 21, 2, 1),
(27, 9, '30 g', '৩০ গ্রাম', 30, 110.00, 125.00, 76.00, 21, 3, 1),
(28, 10, '1 kg', '১ কেজি', 1000, 450.00, 550.00, 320.00, 66, 1, 1),
(29, 10, '500 g', '৫০০ গ্রাম', 500, 250.00, 300.00, 176.00, 66, 2, 1),
(30, 10, '200 g', '২০০ গ্রাম', 200, 125.00, 155.00, 90.00, 66, 3, 1),
(31, 11, '250 g', '২৫০ গ্রাম', 250, 520.00, 600.00, 360.00, 26, 1, 1),
(32, 11, '125 g', '১২৫ গ্রাম', 125, 285.00, 330.00, 198.00, 26, 2, 1),
(33, 11, '50 g', '৫০ গ্রাম', 50, 145.00, 170.00, 101.00, 26, 3, 1),
(34, 12, '500 g', '৫০০ গ্রাম', 500, 420.00, 480.00, 300.00, 8, 1, 1),
(35, 12, '250 g', '২৫০ গ্রাম', 250, 230.00, 265.00, 165.00, 8, 2, 1),
(36, 12, '100 g', '১০০ গ্রাম', 100, 120.00, 135.00, 84.00, 8, 3, 1),
(37, 13, '400 g', '৪০০ গ্রাম', 400, 560.00, 650.00, 400.00, 30, 1, 1),
(38, 13, '200 g', '২০০ গ্রাম', 200, 310.00, 360.00, 220.00, 30, 2, 1),
(39, 13, '80 g', '৮০ গ্রাম', 80, 155.00, 180.00, 112.00, 30, 3, 1),
(40, 14, '250 g', '২৫০ গ্রাম', 250, 380.00, 450.00, 270.00, 26, 1, 1),
(41, 14, '125 g', '১২৫ গ্রাম', 125, 210.00, 250.00, 148.00, 26, 2, 1),
(42, 14, '50 g', '৫০ গ্রাম', 50, 105.00, 125.00, 76.00, 26, 3, 1),
(43, 15, '500 g', '৫০০ গ্রাম', 500, 650.00, 750.00, 480.00, 40, 1, 1),
(44, 15, '250 g', '২৫০ গ্রাম', 250, 360.00, 415.00, 264.00, 40, 2, 1),
(45, 15, '100 g', '১০০ গ্রাম', 100, 180.00, 210.00, 134.00, 40, 3, 1),
(46, 16, '500 g', '৫০০ গ্রাম', 500, 620.00, 700.00, 450.00, 30, 1, 1),
(47, 16, '250 g', '২৫০ গ্রাম', 250, 340.00, 385.00, 248.00, 30, 2, 1),
(48, 16, '100 g', '১০০ গ্রাম', 100, 175.00, 195.00, 126.00, 30, 3, 1),
(49, 17, '200 g', '২০০ গ্রাম', 200, 340.00, 400.00, 230.00, 25, 1, 1),
(50, 17, '100 g', '১০০ গ্রাম', 100, 185.00, 220.00, 127.00, 25, 2, 1),
(51, 17, '40 g', '৪০ গ্রাম', 40, 95.00, 110.00, 64.00, 25, 3, 1),
(52, 18, '300 g', '৩০০ গ্রাম', 300, 520.00, 600.00, 360.00, 18, 1, 1),
(53, 18, '150 g', '১৫০ গ্রাম', 150, 285.00, 330.00, 198.00, 18, 2, 1),
(54, 18, '60 g', '৬০ গ্রাম', 60, 145.00, 170.00, 101.00, 18, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `push_subscriptions`
--

CREATE TABLE `push_subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `endpoint_hash` char(64) NOT NULL,
  `endpoint` text NOT NULL,
  `public_key` varchar(255) NOT NULL,
  `auth_token` varchar(255) NOT NULL,
  `content_encoding` varchar(12) NOT NULL DEFAULT 'aes128gcm',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT 0,
  `name` varchar(120) DEFAULT NULL,
  `rating` tinyint(4) DEFAULT 5,
  `body` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `name`, `rating`, `body`, `status`, `created_at`) VALUES
(1, 1, 0, 'Sabrina Rahman', 5, 'Best dried mango ever!', 1, '2026-11-11 10:58:20'),
(2, 10, 0, 'Ahmed Karim', 5, 'Best dates I have had.', 1, '2026-11-11 10:58:20'),
(3, 4, 0, 'Nusrat Jahan', 4, 'Kids love the banana chips.', 1, '2026-11-11 10:58:20');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `type` varchar(30) NOT NULL,
  `title_en` varchar(200) DEFAULT NULL,
  `title_bn` varchar(200) DEFAULT NULL,
  `subtitle_en` varchar(300) DEFAULT NULL,
  `subtitle_bn` varchar(300) DEFAULT NULL,
  `source` varchar(40) DEFAULT 'featured',
  `limit_n` int(11) DEFAULT 8,
  `layout` varchar(10) DEFAULT 'grid',
  `badge_en` varchar(100) DEFAULT NULL,
  `badge_bn` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `btn_en` varchar(60) DEFAULT NULL,
  `btn_bn` varchar(60) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `type`, `title_en`, `title_bn`, `subtitle_en`, `subtitle_bn`, `source`, `limit_n`, `layout`, `badge_en`, `badge_bn`, `image`, `image2`, `link`, `btn_en`, `btn_bn`, `sort_order`, `status`) VALUES
(1, 'hero', 'Hero Slider', 'হিরো স্লাইডার', '', '', '', 0, 'grid', '', '', '', NULL, '', '', '', 1, 1),
(2, 'categories', 'Shop by Category', 'ক্যাটাগরি অনুযায়ী কিনুন', 'Choose from our wide range of dried fruits', 'আমাদের শুকনো ফলের বিশাল সংগ্রহ থেকে বেছে নিন', '', 12, 'slider', '', '', '', NULL, 'shop.php', '', '', 2, 1),
(3, 'products', 'Fan Favorites', 'ভক্তদের পছন্দ', 'Loved by thousands of happy customers', 'হাজারো খুশি গ্রাহকের প্রিয়', 'featured', 8, 'grid', '', '', '', NULL, 'shop.php?featured=1', 'View All', 'সব দেখুন', 3, 1),
(4, 'banner', 'Healthy Choices for a Healthier Life', 'সুস্থ জীবনের জন্য স্বাস্থ্যকর পছন্দ', 'Get up to 30% OFF on selected products!', 'বাছাই করা পণ্যে ৩০% পর্যন্ত ছাড় পান!', '', 0, 'grid', 'UP TO|30%|OFF', 'ছাড়|৩০%|পর্যন্ত', '', '261111-668e48b007.png', 'shop.php?offer=1', 'Shop the Offer', 'অফারটি দেখুন', 4, 1),
(5, 'products', 'Best Selling Products', 'বেস্ট সেলিং পণ্য', 'Customer favourites you will love', 'গ্রাহকদের সবচেয়ে প্রিয় পণ্য', 'best', 8, 'slider', '', '', '', NULL, 'shop.php?flag=best', 'View All', 'সব দেখুন', 5, 1),
(6, 'products', 'Recent Uploads', 'সদ্য যোগ হওয়া', 'Fresh arrivals just added to our store', 'আমাদের দোকানে নতুন যোগ হয়েছে', 'recent', 4, 'grid', '', '', '', NULL, 'shop.php?sort=new', 'View All', 'সব দেখুন', 6, 1),
(7, 'products', 'All Products', 'সকল পণ্য', 'Fresh, natural and healthy', 'তাজা, প্রাকৃতিক ও স্বাস্থ্যকর', 'all', 8, 'grid', '', '', '', NULL, 'shop.php', 'View All Products', 'সব পণ্য দেখুন', 7, 1),
(8, 'features', 'Why Choose DryNutri?', 'কেন ড্রাইনিউট্রি বেছে নেবেন?', '', '', '', 0, 'grid', '', '', '', NULL, '', '', '', 8, 1),
(9, 'testimonials', 'What Our Customers Say', 'আমাদের গ্রাহকরা কী বলেন', '', '', '', 6, 'slider', '', '', '', NULL, '', '', '', 9, 1),
(10, 'newsletter', 'Subscribe to our Newsletter', 'আমাদের নিউজলেটারে সাবস্ক্রাইব করুন', 'Get new arrivals, offers and health tips - right in your inbox and on the site.', 'নতুন পণ্য, অফার ও স্বাস্থ্য টিপস পান - ইনবক্সে ও সাইটে।', '', 0, 'grid', '', '', '', NULL, '', 'Subscribe', 'সাবস্ক্রাইব', 10, 1),
(11, 'offer', NULL, NULL, NULL, NULL, 'featured', 8, 'grid', NULL, NULL, NULL, '261111-ba5687ea35.png', 'shop.php', 'Shop the Offer', 'অফার দেখুন', 1, 1),
(13, 'banner', '', '', '', '', 'featured', 8, 'grid', '', '', '', '261111-fb0e159e0b.jpg', '', '', '', 12, 1);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `skey` varchar(100) NOT NULL,
  `sval` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`skey`, `sval`) VALUES
('about_bn', 'ড্রাইনিউট্রি নিয়ে এসেছে প্রিমিয়াম শুকনো আম, কমলা, কলা ও আরও অনেক ফল - প্রাকৃতিক মিষ্টি, চিনি ছাড়া।'),
('about_en', 'DryNutri brings you premium sun-dried mango, orange, banana and more - naturally sweet, with no added sugar.'),
('accent_color', '#7ed957'),
('address_bn', '১২৩ হেলদি স্ট্রিট, ঢাকা, বাংলাদেশ'),
('address_en', '123 Healthy Street, Dhaka, Bangladesh'),
('admin_email', 'admin@drynutri.com'),
('copyright', '© DryNutri. All rights reserved. | Live Healthy. Live Natural.'),
('currency', '৳'),
('email', 'support@drynutri.com'),
('facebook', '#'),
('favicon', ''),
('float_email', 'support@drynutri.com'),
('float_enabled', '1'),
('float_messenger', ''),
('float_phone', '+8801234567890'),
('float_whatsapp', '8801234567890'),
('free_shipping_min', '1000'),
('instagram', '#'),
('linkedin', '#'),
('logo', ''),
('logo_dark', ''),
('logo_tagline', '1'),
('low_stock', '10'),
('mail_enabled', '0'),
('mail_from', ''),
('mail_from_name', 'DryNutri'),
('notify_email_auto', '0'),
('notify_offers', '1'),
('notify_products', '1'),
('order_mail_body', 'Hello {name},<br><br>Thank you for shopping with {site}! Your order <b>{order_no}</b> is now <b>{status}</b>.<br><br>{items}<br><b>Total: {total}</b><br><br>Track your order anytime: <a href=\"{track_url}\">{track_url}</a><br><br>With care,<br>{site} Team'),
('order_mail_subject', 'Your DryNutri order {order_no} is {status}'),
('order_sms_body', ''),
('payment_methods', 'cod,bkash,nagad'),
('payment_note_bn', 'আমাদের বিকাশ/নগদ নম্বরে টাকা পাঠিয়ে নোটে ট্রানজেকশন আইডি দিন।'),
('payment_note_en', 'Send payment to our bKash/Nagad number and enter the transaction ID in the note.'),
('phone', '+880 1234 567890'),
('pinterest', '#'),
('primary_color', '#2f7d32'),
('push_enabled', '1'),
('push_private_key', '8-HeDM3ZYAnfhoEos6cWrTL2OJqeHan9XELatNRcRH4'),
('push_public_key', 'BKF6PmHCQ_RTe3ewM8u5hbkh9P6LU2TaRk8Tkkwy1O12RtxZkU865UCYaBWxmzWgaDStRV1aopuEvu_5PzI77jE'),
('shipping_fee', '80'),
('shipping_inside', '60'),
('shipping_outside', '120'),
('site_name', 'DryNutri'),
('sms_api_url', ''),
('sms_api_user', ''),
('sms_enabled', '1'),
('sms_sender_id', ''),
('smtp_host', 'smtp.gmail.com'),
('smtp_pass', ''),
('smtp_port', '587'),
('smtp_secure', 'tls'),
('smtp_user', ''),
('tagline_bn', 'প্রতিটি কামড়ে খাঁটি পুষ্টি'),
('tagline_en', 'Pure Nutrition in Every Bite'),
('topbar_bn', '৳১,০০০ এর উপরে ফ্রি ডেলিভারি|১০০% প্রাকৃতিক শুকনো ফল|২৪/৭ গ্রাহক সহায়তা'),
('topbar_en', 'Free Shipping on Orders Over ৳1,000|100% Natural Dried Fruits|24/7 Customer Support'),
('visitor_tracking', '1'),
('whatsapp_order', '1'),
('youtube', '#');

-- --------------------------------------------------------

--
-- Table structure for table `sliders`
--

CREATE TABLE `sliders` (
  `id` int(11) NOT NULL,
  `title_en` varchar(200) DEFAULT NULL,
  `title_bn` varchar(200) DEFAULT NULL,
  `subtitle_en` varchar(300) DEFAULT NULL,
  `subtitle_bn` varchar(300) DEFAULT NULL,
  `badge_en` varchar(80) DEFAULT NULL,
  `badge_bn` varchar(80) DEFAULT NULL,
  `btn_en` varchar(60) DEFAULT NULL,
  `btn_bn` varchar(60) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `btn2_en` varchar(60) DEFAULT NULL,
  `btn2_bn` varchar(60) DEFAULT NULL,
  `link2` varchar(255) DEFAULT NULL,
  `image_mode` varchar(8) DEFAULT 'side',
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sliders`
--

INSERT INTO `sliders` (`id`, `title_en`, `title_bn`, `subtitle_en`, `subtitle_bn`, `badge_en`, `badge_bn`, `btn_en`, `btn_bn`, `link`, `btn2_en`, `btn2_bn`, `link2`, `image_mode`, `image`, `sort_order`, `status`) VALUES
(1, 'Taste the Sunshine.Naturally Dried.', 'সূর্যের স্বাদ নিন।প্রাকৃতিকভাবে শুকানো।', 'Premium dried mango, orange, banana and more - no added sugar, 100% natural.', 'প্রিমিয়াম শুকনো আম, কমলা, কলা ও আরও অনেক কিছু - চিনি ছাড়া, ১০০% প্রাকৃতিক।', 'Sun-Dried Goodness in Every Bite', 'প্রতিটি কামড়ে রোদে শুকানো স্বাদ', 'Shop Now', 'এখনই কিনুন', 'shop.php', 'Explore More', 'আরও দেখুন', 'shop.php?featured=1', 'side', '261111-7e085e0393.jpg', 1, 1),
(2, 'Mango, Mango,\nMango!', 'আম, আম,\nআর আম!', 'Juicy golden slices dried to perfection - sweet, chewy and irresistible.', 'রসালো সোনালি স্লাইস নিখুঁতভাবে শুকানো - মিষ্টি, নরম আর অপ্রতিরোধ্য।', 'Our Best Seller', 'আমাদের সেরা বিক্রিত', 'Shop Mango', 'আম কিনুন', 'shop.php?cat=dried-mango', 'All Products', 'সব পণ্য', 'shop.php', 'side', NULL, 2, 1),
(3, 'Citrus & Tropical\nDelights', 'সাইট্রাস ও\nট্রপিক্যাল স্বাদ', 'Orange, pineapple, kiwi and papaya - a sunny taste of the tropics.', 'কমলা, আনারস, কিউই ও পেঁপে - ট্রপিকসের রোদেলা স্বাদ।', 'New Arrivals', 'নতুন আগমন', 'Shop Tropical', 'ট্রপিক্যাল কিনুন', 'shop.php?cat=pineapple-tropical', 'Orange Slices', 'কমলার স্লাইস', 'shop.php?cat=dried-orange', 'side', NULL, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `body_en` text DEFAULT NULL,
  `body_bn` text DEFAULT NULL,
  `rating` tinyint(4) DEFAULT 5,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `name`, `body_en`, `body_bn`, `rating`, `image`, `sort_order`, `status`) VALUES
(1, 'Sabrina Rahman', 'The dried mango is unbelievably good - sweet, soft and no added sugar!', 'শুকনো আমটা অসাধারণ - মিষ্টি, নরম আর চিনি ছাড়া!', 5, NULL, 1, 1),
(2, 'Ahmed Karim', 'Fresh, natural and healthy. Fast delivery too. Highly recommended!', 'তাজা, প্রাকৃতিক ও স্বাস্থ্যকর। ডেলিভারিও দ্রুত। অত্যন্ত সুপারিশযোগ্য!', 5, NULL, 2, 1),
(3, 'Nusrat Jahan', 'My kids love the banana chips and orange slices. Great service!', 'আমার বাচ্চারা কলার চিপস আর কমলার স্লাইস খুব পছন্দ করে। দারুণ সার্ভিস!', 5, NULL, 3, 1),
(4, 'Rakib Hasan', 'Excellent quality and super fast delivery! DryNutri is now my favorite store.', 'চমৎকার মান এবং অসম্ভব দ্রুত ডেলিভারি! ড্রাইনিউট্রি এখন আমার প্রিয় দোকান।', 5, NULL, 4, 1),
(5, 'Sumaiya Akter', 'The mixed fruit pack is perfect for gifting.', 'মিক্সড ফ্রুট প্যাকটা উপহারের জন্য একদম পারফেক্ট।', 5, NULL, 5, 1),
(6, 'Tariq Rahman', 'Great products and amazing service.', 'দারুণ পণ্য এবং অসাধারণ সেবা।', 5, NULL, 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `email` varchar(160) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager','customer') NOT NULL DEFAULT 'customer',
  `perms` varchar(255) DEFAULT '',
  `address` text DEFAULT NULL,
  `city` varchar(80) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `is_guest` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `perms`, `address`, `city`, `avatar`, `is_guest`, `status`, `last_login`, `created_at`) VALUES
(1, 'Super Admin', 'admin@drynutri.com', '', '$2y$10$FL.nLMxQh/wBVjMFvin0juvWDbFalWbMY.zfJPhYdiNl1fAnyFpf.', 'admin', '', NULL, NULL, '261111-c3b001b969.jpg', 0, 1, '2026-11-11 15:57:53', '2026-11-11 10:58:20'),
(2, 'Rahim Ahmed', 'guest-2171ab8530ff@guest.local', '01711000001', '$2y$10$demoguestaccountcannotloginxxxxxxxxxxxxxxxxxxxxxxxxxxxx', 'customer', '', 'House 12, Road 5', 'Dhaka', NULL, 1, 1, NULL, '2026-10-11 12:00:00'),
(3, 'Fatema Akter', 'guest-9bda39f7e671@guest.local', '01711000002', '$2y$10$demoguestaccountcannotloginxxxxxxxxxxxxxxxxxxxxxxxxxxxx', 'customer', '', 'House 12, Road 5', 'Uttara', NULL, 1, 1, NULL, '2026-10-12 12:00:00'),
(4, 'Md. Hasan', 'guest-4b678c41df36@guest.local', '01811000003', '$2y$10$demoguestaccountcannotloginxxxxxxxxxxxxxxxxxxxxxxxxxxxx', 'customer', '', 'House 12, Road 5', 'Chattogram', NULL, 1, 1, NULL, '2026-10-13 12:00:00'),
(5, 'Nusrat Jahan', 'guest-1393053fbda8@guest.local', '01911000004', '$2y$10$demoguestaccountcannotloginxxxxxxxxxxxxxxxxxxxxxxxxxxxx', 'customer', '', 'House 12, Road 5', 'Dhaka', NULL, 1, 1, NULL, '2026-10-14 12:00:00'),
(6, 'Tariqul Islam', 'guest-ab88850e22a5@guest.local', '01611000005', '$2y$10$demoguestaccountcannotloginxxxxxxxxxxxxxxxxxxxxxxxxxxxx', 'customer', '', 'House 12, Road 5', 'Sylhet', NULL, 1, 1, NULL, '2026-10-15 12:00:00'),
(7, 'Sabrina Rahman', 'guest-c61220e50c95@guest.local', '01511000006', '$2y$10$demoguestaccountcannotloginxxxxxxxxxxxxxxxxxxxxxxxxxxxx', 'customer', '', 'House 12, Road 5', 'Gazipur', NULL, 1, 1, NULL, '2026-10-16 12:00:00'),
(8, 'Ahmed Karim', 'guest-881d64de00ea@guest.local', '01711000007', '$2y$10$demoguestaccountcannotloginxxxxxxxxxxxxxxxxxxxxxxxxxxxx', 'customer', '', 'House 12, Road 5', 'Dhaka', NULL, 1, 1, NULL, '2026-10-17 12:00:00'),
(9, 'Mitu Sarker', 'guest-c56a4b1f47c8@guest.local', '01811000008', '$2y$10$demoguestaccountcannotloginxxxxxxxxxxxxxxxxxxxxxxxxxxxx', 'customer', '', 'House 12, Road 5', 'Rajshahi', NULL, 1, 1, NULL, '2026-10-18 12:00:00'),
(10, 'Md. Jahidul Hakim', 'mdjhk20@gmail.com', '01837090666', '$2y$10$B.yM7tg73vaIlZOp5N2xveRwXqNuKjMqaA4XECf.yLSF2axnwvLQ2', 'customer', '', NULL, NULL, NULL, 0, 1, '2026-11-11 11:02:34', '2026-11-11 11:02:34'),
(11, 'Md. Jahidul Hakim', 'admin@abcit.com', '01837090666', '$2y$10$/.OL33dIEfzgjofN4.RoIuUWlCZEBBqYAWJtdaflmBq4.VFW3Q32i', 'customer', '', '', '', '261111-6c5b782909.jpg', 0, 1, '2026-11-11 12:55:20', '2026-11-11 12:55:20'),
(12, 'Moyna', 'mdjhk300@gmail.com', '01957288638', '$2y$10$F3QYw4lngQVhsFCB1KpmxeERvA7oiYQwyKsnHdBaKVW8bit7SF2Yy', 'manager', 'products,categories,orders,customers,content,finance,reports', NULL, NULL, NULL, 0, 1, '2026-11-11 13:39:34', '2026-11-11 13:39:13');

-- --------------------------------------------------------

--
-- Table structure for table `visitors`
--

CREATE TABLE `visitors` (
  `id` bigint(20) NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `page` varchar(255) DEFAULT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `device` varchar(12) DEFAULT NULL,
  `visit_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitors`
--

INSERT INTO `visitors` (`id`, `ip`, `page`, `referrer`, `user_agent`, `device`, `visit_date`, `created_at`) VALUES
(1, '103.21.194.34', '/checkout.php', 'https://www.google.com/', 'Mozilla/5.0 demo', 'tablet', '2026-10-13', '2026-10-13 05:49:00'),
(2, '103.36.147.127', '/cart.php', '', 'Mozilla/5.0 demo', 'desktop', '2026-11-05', '2026-11-05 17:42:00'),
(3, '103.31.230.19', '/product.php?slug=dried-banana-chips-250g', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-09', '2026-11-09 05:45:00'),
(4, '103.9.156.220', '/product.php?slug=dried-mango-slices-250g', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-16', '2026-10-16 03:07:00'),
(5, '103.33.3.87', '/track.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-14', '2026-10-14 07:51:00'),
(6, '103.6.135.182', '/product.php?slug=dried-mango-slices-250g', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-22', '2026-10-22 07:36:00'),
(7, '103.15.52.239', '/product.php?slug=dried-banana-chips-250g', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-02', '2026-11-02 10:17:00'),
(8, '103.36.173.46', '/product.php?slug=dried-banana-chips-250g', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-31', '2026-10-31 20:48:00'),
(9, '103.4.5.188', '/track.php', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-02', '2026-11-02 10:45:00'),
(10, '103.19.240.115', '/shop.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'tablet', '2026-10-24', '2026-10-24 19:48:00'),
(11, '103.36.79.177', '/product.php?slug=dried-mango-slices-250g', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-23', '2026-10-23 22:35:00'),
(12, '103.12.104.89', '/', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-03', '2026-11-03 14:18:00'),
(13, '103.51.226.66', '/checkout.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-07', '2026-11-07 14:40:00'),
(14, '103.57.98.223', '/track.php', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-07', '2026-11-07 13:18:00'),
(15, '103.6.118.48', '/shop.php?cat=dried-mango', 'https://www.google.com/', 'Mozilla/5.0 demo', 'tablet', '2026-10-19', '2026-10-19 02:00:00'),
(16, '103.27.247.85', '/track.php', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-10', '2026-11-10 03:01:00'),
(17, '103.17.92.47', '/', 'https://www.google.com/', 'Mozilla/5.0 demo', 'tablet', '2026-11-09', '2026-11-09 01:27:00'),
(18, '103.21.129.168', '/product.php?slug=dried-mango-slices-250g', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-07', '2026-11-07 19:48:00'),
(19, '103.56.23.37', '/checkout.php', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-08', '2026-11-08 02:59:00'),
(20, '103.23.78.162', '/product.php?slug=dried-mango-slices-250g', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-29', '2026-10-29 11:14:00'),
(21, '103.28.196.84', '/page.php?slug=about', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-19', '2026-10-19 15:39:00'),
(22, '103.45.38.12', '/track.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-17', '2026-10-17 14:53:00'),
(23, '103.52.130.146', '/', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-11', '2026-10-11 18:08:00'),
(24, '103.6.35.164', '/cart.php', '', 'Mozilla/5.0 demo', 'tablet', '2026-11-08', '2026-11-08 10:57:00'),
(25, '103.39.13.161', '/', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-14', '2026-10-14 20:36:00'),
(26, '103.4.117.205', '/shop.php', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-26', '2026-10-26 03:00:00'),
(27, '103.8.191.189', '/page.php?slug=about', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-19', '2026-10-19 18:03:00'),
(28, '103.19.187.194', '/product.php?slug=dried-mango-slices-250g', '', 'Mozilla/5.0 demo', 'tablet', '2026-10-14', '2026-10-14 02:57:00'),
(29, '103.8.123.234', '/product.php?slug=dried-banana-chips-250g', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-25', '2026-10-25 22:57:00'),
(30, '103.13.85.66', '/product.php?slug=dried-banana-chips-250g', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-07', '2026-11-07 15:32:00'),
(31, '103.7.125.69', '/shop.php', '', 'Mozilla/5.0 demo', 'tablet', '2026-11-09', '2026-11-09 19:33:00'),
(32, '103.22.119.120', '/page.php?slug=about', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-31', '2026-10-31 18:23:00'),
(33, '103.34.5.75', '/page.php?slug=about', '', 'Mozilla/5.0 demo', 'tablet', '2026-11-01', '2026-11-01 09:05:00'),
(34, '103.17.235.243', '/product.php?slug=dried-mango-slices-250g', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-01', '2026-11-01 22:48:00'),
(35, '103.20.244.93', '/shop.php?cat=dried-mango', 'https://instagram.com/', 'Mozilla/5.0 demo', 'desktop', '2026-11-05', '2026-11-05 18:07:00'),
(36, '103.49.94.60', '/page.php?slug=about', 'https://facebook.com/', 'Mozilla/5.0 demo', 'tablet', '2026-10-13', '2026-10-13 08:10:00'),
(37, '103.4.244.126', '/page.php?slug=about', 'https://facebook.com/', 'Mozilla/5.0 demo', 'desktop', '2026-11-10', '2026-11-10 06:35:00'),
(38, '103.30.89.97', '/cart.php', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-18', '2026-10-18 07:12:00'),
(39, '103.52.87.215', '/checkout.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-10', '2026-11-10 00:56:00'),
(40, '103.51.75.65', '/cart.php', '', 'Mozilla/5.0 demo', 'tablet', '2026-10-19', '2026-10-19 11:36:00'),
(41, '103.8.93.237', '/checkout.php', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-28', '2026-10-28 15:54:00'),
(42, '103.7.214.170', '/product.php?slug=dried-banana-chips-250g', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-02', '2026-11-02 08:32:00'),
(43, '103.36.81.49', '/cart.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-01', '2026-11-01 21:07:00'),
(44, '103.29.234.225', '/track.php', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-15', '2026-10-15 14:28:00'),
(45, '103.7.239.188', '/checkout.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-18', '2026-10-18 09:15:00'),
(46, '103.35.13.234', '/track.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-21', '2026-10-21 02:14:00'),
(47, '103.25.73.77', '/product.php?slug=dried-banana-chips-250g', 'https://www.google.com/', 'Mozilla/5.0 demo', 'tablet', '2026-10-25', '2026-10-25 21:51:00'),
(48, '103.23.124.143', '/checkout.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-21', '2026-10-21 03:52:00'),
(49, '103.8.54.129', '/page.php?slug=about', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-21', '2026-10-21 06:29:00'),
(50, '103.52.116.110', '/shop.php?cat=dried-mango', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-27', '2026-10-27 00:39:00'),
(51, '103.15.88.143', '/shop.php', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-03', '2026-11-03 08:55:00'),
(52, '103.55.146.52', '/', 'https://facebook.com/', 'Mozilla/5.0 demo', 'tablet', '2026-10-30', '2026-10-30 03:11:00'),
(53, '103.17.97.70', '/cart.php', '', 'Mozilla/5.0 demo', 'tablet', '2026-10-27', '2026-10-27 18:07:00'),
(54, '103.27.33.176', '/track.php', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-01', '2026-11-01 16:24:00'),
(55, '103.19.99.103', '/page.php?slug=about', 'https://facebook.com/', 'Mozilla/5.0 demo', 'desktop', '2026-11-08', '2026-11-08 02:45:00'),
(56, '103.12.9.109', '/page.php?slug=about', 'https://instagram.com/', 'Mozilla/5.0 demo', 'tablet', '2026-10-14', '2026-10-14 11:16:00'),
(57, '103.29.239.238', '/track.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'tablet', '2026-11-10', '2026-11-10 09:31:00'),
(58, '103.18.40.39', '/track.php', '', 'Mozilla/5.0 demo', 'tablet', '2026-11-03', '2026-11-03 08:17:00'),
(59, '103.53.11.1', '/shop.php?cat=dried-mango', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-07', '2026-11-07 17:11:00'),
(60, '103.12.161.65', '/track.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-21', '2026-10-21 01:38:00'),
(61, '103.23.135.242', '/product.php?slug=dried-mango-slices-250g', 'https://facebook.com/', 'Mozilla/5.0 demo', 'desktop', '2026-11-07', '2026-11-07 09:36:00'),
(62, '103.4.3.138', '/product.php?slug=dried-banana-chips-250g', 'https://facebook.com/', 'Mozilla/5.0 demo', 'desktop', '2026-11-02', '2026-11-02 15:30:00'),
(63, '103.57.227.63', '/page.php?slug=about', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-30', '2026-10-30 14:00:00'),
(64, '103.5.246.106', '/product.php?slug=dried-banana-chips-250g', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-24', '2026-10-24 03:35:00'),
(65, '103.60.173.166', '/checkout.php', '', 'Mozilla/5.0 demo', 'desktop', '2026-11-03', '2026-11-03 19:00:00'),
(66, '103.31.237.95', '/product.php?slug=dried-mango-slices-250g', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-02', '2026-11-02 13:14:00'),
(67, '103.49.108.93', '/checkout.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-19', '2026-10-19 00:28:00'),
(68, '103.51.217.130', '/shop.php', '', 'Mozilla/5.0 demo', 'tablet', '2026-10-16', '2026-10-16 02:02:00'),
(69, '103.53.210.50', '/product.php?slug=dried-mango-slices-250g', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-04', '2026-11-04 01:22:00'),
(70, '103.10.244.160', '/page.php?slug=about', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-02', '2026-11-02 01:56:00'),
(71, '103.35.107.234', '/', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-13', '2026-10-13 04:23:00'),
(72, '103.7.55.7', '/shop.php?cat=dried-mango', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-11', '2026-10-11 22:35:00'),
(73, '103.15.101.116', '/cart.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-19', '2026-10-19 09:57:00'),
(74, '103.25.49.48', '/track.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-12', '2026-10-12 06:21:00'),
(75, '103.50.97.215', '/cart.php', 'https://www.google.com/', 'Mozilla/5.0 demo', 'tablet', '2026-10-31', '2026-10-31 13:20:00'),
(76, '103.4.21.72', '/shop.php', 'https://www.google.com/', 'Mozilla/5.0 demo', 'tablet', '2026-11-05', '2026-11-05 08:17:00'),
(77, '103.39.247.195', '/product.php?slug=dried-mango-slices-250g', 'https://facebook.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-13', '2026-10-13 07:47:00'),
(78, '103.56.206.111', '/shop.php', '', 'Mozilla/5.0 demo', 'tablet', '2026-10-17', '2026-10-17 01:28:00'),
(79, '103.38.236.115', '/product.php?slug=dried-mango-slices-250g', 'https://www.google.com/', 'Mozilla/5.0 demo', 'desktop', '2026-11-03', '2026-11-03 23:17:00'),
(80, '103.5.162.106', '/product.php?slug=dried-mango-slices-250g', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-17', '2026-10-17 19:49:00'),
(81, '103.33.17.206', '/', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-29', '2026-10-29 10:49:00'),
(82, '103.42.87.93', '/product.php?slug=dried-banana-chips-250g', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-18', '2026-10-18 09:52:00'),
(83, '103.21.77.1', '/shop.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-02', '2026-11-02 01:12:00'),
(84, '103.49.245.120', '/checkout.php', 'https://www.google.com/', 'Mozilla/5.0 demo', 'tablet', '2026-11-06', '2026-11-06 19:47:00'),
(85, '103.12.238.128', '/shop.php?cat=dried-mango', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-14', '2026-10-14 19:10:00'),
(86, '103.42.61.84', '/cart.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-15', '2026-10-15 06:51:00'),
(87, '103.9.132.51', '/checkout.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-15', '2026-10-15 15:40:00'),
(88, '103.45.9.124', '/track.php', 'https://instagram.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-28', '2026-10-28 09:48:00'),
(89, '103.60.27.19', '/product.php?slug=dried-banana-chips-250g', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-04', '2026-11-04 21:27:00'),
(90, '103.30.128.182', '/page.php?slug=about', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-04', '2026-11-04 08:43:00'),
(91, '103.33.159.229', '/product.php?slug=dried-mango-slices-250g', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-05', '2026-11-05 21:47:00'),
(92, '103.22.72.146', '/product.php?slug=dried-banana-chips-250g', 'https://www.google.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-17', '2026-10-17 01:59:00'),
(93, '103.16.113.64', '/shop.php?cat=dried-mango', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-18', '2026-10-18 03:07:00'),
(94, '103.60.233.149', '/product.php?slug=dried-mango-slices-250g', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-06', '2026-11-06 02:24:00'),
(95, '103.19.130.135', '/product.php?slug=dried-mango-slices-250g', '', 'Mozilla/5.0 demo', 'tablet', '2026-10-29', '2026-10-29 03:25:00'),
(96, '103.4.122.227', '/product.php?slug=dried-mango-slices-250g', 'https://facebook.com/', 'Mozilla/5.0 demo', 'desktop', '2026-11-09', '2026-11-09 08:31:00'),
(97, '103.18.31.13', '/product.php?slug=dried-mango-slices-250g', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-09', '2026-11-09 01:59:00'),
(98, '103.27.132.222', '/shop.php?cat=dried-mango', 'https://facebook.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-12', '2026-10-12 09:27:00'),
(99, '103.4.28.164', '/cart.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-16', '2026-10-16 13:19:00'),
(100, '103.13.12.53', '/product.php?slug=dried-banana-chips-250g', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-30', '2026-10-30 00:24:00'),
(101, '103.56.84.105', '/cart.php', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-15', '2026-10-15 11:37:00'),
(102, '103.6.204.127', '/track.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-08', '2026-11-08 05:04:00'),
(103, '103.54.102.170', '/track.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-28', '2026-10-28 08:33:00'),
(104, '103.29.179.70', '/checkout.php', 'https://www.google.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-21', '2026-10-21 06:25:00'),
(105, '103.23.191.146', '/cart.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'tablet', '2026-10-28', '2026-10-28 10:15:00'),
(106, '103.45.51.101', '/checkout.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-09', '2026-11-09 23:35:00'),
(107, '103.31.30.211', '/shop.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-28', '2026-10-28 06:40:00'),
(108, '103.12.4.14', '/track.php', '', 'Mozilla/5.0 demo', 'tablet', '2026-10-27', '2026-10-27 06:28:00'),
(109, '103.43.238.95', '/track.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-07', '2026-11-07 16:27:00'),
(110, '103.14.134.44', '/shop.php', '', 'Mozilla/5.0 demo', 'tablet', '2026-10-30', '2026-10-30 02:20:00'),
(111, '103.23.33.215', '/', 'https://facebook.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-26', '2026-10-26 05:16:00'),
(112, '103.44.100.23', '/shop.php?cat=dried-mango', '', 'Mozilla/5.0 demo', 'tablet', '2026-11-08', '2026-11-08 15:16:00'),
(113, '103.57.122.47', '/product.php?slug=dried-mango-slices-250g', '', 'Mozilla/5.0 demo', 'tablet', '2026-10-22', '2026-10-22 05:19:00'),
(114, '103.28.92.32', '/shop.php?cat=dried-mango', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-25', '2026-10-25 06:40:00'),
(115, '103.57.194.173', '/', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-08', '2026-11-08 16:49:00'),
(116, '103.33.141.218', '/product.php?slug=dried-banana-chips-250g', 'https://facebook.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-28', '2026-10-28 15:33:00'),
(117, '103.31.100.169', '/cart.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'tablet', '2026-10-23', '2026-10-23 03:30:00'),
(118, '103.4.159.126', '/page.php?slug=about', '', 'Mozilla/5.0 demo', 'tablet', '2026-11-05', '2026-11-05 11:13:00'),
(119, '103.53.210.118', '/shop.php?cat=dried-mango', 'https://facebook.com/', 'Mozilla/5.0 demo', 'tablet', '2026-10-16', '2026-10-16 14:54:00'),
(120, '103.12.92.111', '/cart.php', '', 'Mozilla/5.0 demo', 'tablet', '2026-11-07', '2026-11-07 09:43:00'),
(121, '103.46.11.11', '/shop.php?cat=dried-mango', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-24', '2026-10-24 18:36:00'),
(122, '103.9.14.193', '/track.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-16', '2026-10-16 18:33:00'),
(123, '103.43.188.178', '/shop.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-10', '2026-11-10 09:45:00'),
(124, '103.22.245.208', '/shop.php?cat=dried-mango', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-12', '2026-10-12 19:13:00'),
(125, '103.43.194.65', '/shop.php?cat=dried-mango', 'https://www.google.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-15', '2026-10-15 00:02:00'),
(126, '103.13.66.129', '/page.php?slug=about', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-12', '2026-10-12 20:26:00'),
(127, '103.19.82.96', '/', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-21', '2026-10-21 18:44:00'),
(128, '103.44.240.72', '/cart.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-29', '2026-10-29 06:30:00'),
(129, '103.11.197.136', '/', 'https://www.google.com/', 'Mozilla/5.0 demo', 'tablet', '2026-10-16', '2026-10-16 02:59:00'),
(130, '103.41.177.226', '/shop.php', 'https://www.google.com/', 'Mozilla/5.0 demo', 'tablet', '2026-10-23', '2026-10-23 18:13:00'),
(131, '103.20.97.95', '/shop.php?cat=dried-mango', 'https://www.google.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-17', '2026-10-17 23:20:00'),
(132, '103.32.59.46', '/', 'https://www.google.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-17', '2026-10-17 09:14:00'),
(133, '103.59.150.238', '/cart.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-31', '2026-10-31 14:11:00'),
(134, '103.22.158.161', '/checkout.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'desktop', '2026-11-03', '2026-11-03 06:55:00'),
(135, '103.12.126.59', '/', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-13', '2026-10-13 10:23:00'),
(136, '103.26.78.28', '/track.php', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-09', '2026-11-09 16:39:00'),
(137, '103.23.151.35', '/product.php?slug=dried-mango-slices-250g', 'https://www.google.com/', 'Mozilla/5.0 demo', 'tablet', '2026-10-27', '2026-10-27 16:05:00'),
(138, '103.4.240.206', '/product.php?slug=dried-mango-slices-250g', '', 'Mozilla/5.0 demo', 'tablet', '2026-11-05', '2026-11-05 07:25:00'),
(139, '103.44.38.224', '/product.php?slug=dried-banana-chips-250g', 'https://facebook.com/', 'Mozilla/5.0 demo', 'desktop', '2026-11-07', '2026-11-07 09:50:00'),
(140, '103.45.211.144', '/cart.php', 'https://instagram.com/', 'Mozilla/5.0 demo', 'tablet', '2026-11-10', '2026-11-10 10:06:00'),
(141, '103.50.127.64', '/shop.php?cat=dried-mango', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-21', '2026-10-21 18:20:00'),
(142, '103.5.104.48', '/product.php?slug=dried-mango-slices-250g', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-08', '2026-11-08 17:52:00'),
(143, '103.4.157.142', '/product.php?slug=dried-mango-slices-250g', '', 'Mozilla/5.0 demo', 'tablet', '2026-10-12', '2026-10-12 08:26:00'),
(144, '103.42.165.130', '/checkout.php', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-03', '2026-11-03 18:19:00'),
(145, '103.8.77.161', '/', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-25', '2026-10-25 01:27:00'),
(146, '103.51.234.120', '/shop.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-28', '2026-10-28 21:06:00'),
(147, '103.20.60.165', '/', '', 'Mozilla/5.0 demo', 'desktop', '2026-11-03', '2026-11-03 08:25:00'),
(148, '103.49.14.69', '/track.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-13', '2026-10-13 03:01:00'),
(149, '103.17.22.226', '/track.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-31', '2026-10-31 14:06:00'),
(150, '103.57.191.52', '/shop.php?cat=dried-mango', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-02', '2026-11-02 03:57:00'),
(151, '103.25.154.62', '/checkout.php', 'https://instagram.com/', 'Mozilla/5.0 demo', 'tablet', '2026-10-12', '2026-10-12 22:44:00'),
(152, '103.48.2.220', '/', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-25', '2026-10-25 17:54:00'),
(153, '103.54.55.101', '/shop.php', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-23', '2026-10-23 01:30:00'),
(154, '103.5.29.28', '/shop.php?cat=dried-mango', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-06', '2026-11-06 10:53:00'),
(155, '103.5.11.36', '/', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-19', '2026-10-19 11:02:00'),
(156, '103.52.94.52', '/track.php', '', 'Mozilla/5.0 demo', 'tablet', '2026-11-07', '2026-11-07 15:51:00'),
(157, '103.17.53.29', '/', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-07', '2026-11-07 03:36:00'),
(158, '103.44.74.123', '/shop.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-14', '2026-10-14 14:27:00'),
(159, '103.17.76.82', '/cart.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-15', '2026-10-15 13:57:00'),
(160, '103.20.239.73', '/', 'https://www.google.com/', 'Mozilla/5.0 demo', 'desktop', '2026-11-10', '2026-11-10 00:02:00'),
(161, '103.36.122.218', '/product.php?slug=dried-banana-chips-250g', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-16', '2026-10-16 15:28:00'),
(162, '103.5.112.133', '/shop.php', 'https://www.google.com/', 'Mozilla/5.0 demo', 'tablet', '2026-10-15', '2026-10-15 21:55:00'),
(163, '103.38.145.56', '/shop.php', 'https://instagram.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-19', '2026-10-19 10:22:00'),
(164, '103.4.135.52', '/product.php?slug=dried-banana-chips-250g', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-04', '2026-11-04 21:07:00'),
(165, '103.10.126.178', '/shop.php?cat=dried-mango', 'https://facebook.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-29', '2026-10-29 19:15:00'),
(166, '103.20.148.242', '/shop.php?cat=dried-mango', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-14', '2026-10-14 18:25:00'),
(167, '103.35.43.29', '/shop.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-19', '2026-10-19 04:06:00'),
(168, '103.26.25.103', '/checkout.php', '', 'Mozilla/5.0 demo', 'tablet', '2026-10-21', '2026-10-21 00:52:00'),
(169, '103.5.96.53', '/product.php?slug=dried-banana-chips-250g', 'https://www.google.com/', 'Mozilla/5.0 demo', 'tablet', '2026-10-12', '2026-10-12 13:58:00'),
(170, '103.36.44.98', '/product.php?slug=dried-mango-slices-250g', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-12', '2026-10-12 17:24:00'),
(171, '103.52.177.193', '/', 'https://www.google.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-23', '2026-10-23 15:44:00'),
(172, '103.59.216.116', '/track.php', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-25', '2026-10-25 06:42:00'),
(173, '103.48.198.66', '/product.php?slug=dried-mango-slices-250g', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-26', '2026-10-26 21:02:00'),
(174, '103.60.179.61', '/track.php', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-26', '2026-10-26 14:04:00'),
(175, '103.13.186.40', '/product.php?slug=dried-mango-slices-250g', 'https://www.google.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-31', '2026-10-31 14:56:00'),
(176, '103.24.245.49', '/product.php?slug=dried-banana-chips-250g', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-05', '2026-11-05 03:57:00'),
(177, '103.16.99.39', '/shop.php?cat=dried-mango', 'https://www.google.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-20', '2026-10-20 08:32:00'),
(178, '103.16.28.164', '/shop.php', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-28', '2026-10-28 02:40:00'),
(179, '103.33.9.4', '/checkout.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-12', '2026-10-12 22:45:00'),
(180, '103.22.119.6', '/shop.php?cat=dried-mango', 'https://www.google.com/', 'Mozilla/5.0 demo', 'tablet', '2026-10-24', '2026-10-24 14:25:00'),
(181, '103.58.111.180', '/checkout.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-10', '2026-11-10 03:44:00'),
(182, '103.45.32.117', '/checkout.php', 'https://www.google.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-20', '2026-10-20 05:49:00'),
(183, '103.30.63.201', '/checkout.php', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-21', '2026-10-21 08:40:00'),
(184, '103.34.117.6', '/checkout.php', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-13', '2026-10-13 21:33:00'),
(185, '103.24.200.3', '/checkout.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-12', '2026-10-12 13:40:00'),
(186, '103.38.56.42', '/product.php?slug=dried-mango-slices-250g', 'https://instagram.com/', 'Mozilla/5.0 demo', 'desktop', '2026-11-09', '2026-11-09 03:26:00'),
(187, '103.33.139.53', '/page.php?slug=about', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-06', '2026-11-06 16:24:00'),
(188, '103.37.88.106', '/page.php?slug=about', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-20', '2026-10-20 23:23:00'),
(189, '103.52.239.32', '/cart.php', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-28', '2026-10-28 18:28:00'),
(190, '103.29.16.4', '/shop.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'tablet', '2026-11-01', '2026-11-01 22:58:00'),
(191, '103.26.149.68', '/shop.php', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-20', '2026-10-20 12:58:00'),
(192, '103.37.249.57', '/checkout.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-17', '2026-10-17 22:20:00'),
(193, '103.53.18.208', '/product.php?slug=dried-mango-slices-250g', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-05', '2026-11-05 07:36:00'),
(194, '103.26.171.164', '/checkout.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-15', '2026-10-15 07:01:00'),
(195, '103.45.33.200', '/page.php?slug=about', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-16', '2026-10-16 17:18:00'),
(196, '103.47.65.110', '/shop.php?cat=dried-mango', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-01', '2026-11-01 23:10:00'),
(197, '103.26.63.168', '/product.php?slug=dried-banana-chips-250g', 'https://www.google.com/', 'Mozilla/5.0 demo', 'tablet', '2026-10-16', '2026-10-16 02:25:00'),
(198, '103.43.164.22', '/cart.php', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-25', '2026-10-25 21:23:00'),
(199, '103.7.22.212', '/cart.php', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-13', '2026-10-13 22:52:00'),
(200, '103.4.169.3', '/product.php?slug=dried-mango-slices-250g', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-20', '2026-10-20 16:08:00'),
(201, '103.10.149.37', '/product.php?slug=dried-mango-slices-250g', '', 'Mozilla/5.0 demo', 'tablet', '2026-11-01', '2026-11-01 15:15:00'),
(202, '103.17.232.104', '/track.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-30', '2026-10-30 06:48:00'),
(203, '103.54.163.215', '/product.php?slug=dried-banana-chips-250g', '', 'Mozilla/5.0 demo', 'tablet', '2026-10-19', '2026-10-19 17:17:00'),
(204, '103.37.21.190', '/page.php?slug=about', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-19', '2026-10-19 04:44:00'),
(205, '103.18.212.36', '/page.php?slug=about', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-01', '2026-11-01 21:42:00'),
(206, '103.13.180.126', '/product.php?slug=dried-mango-slices-250g', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-25', '2026-10-25 20:04:00'),
(207, '103.59.189.2', '/shop.php?cat=dried-mango', 'https://www.google.com/', 'Mozilla/5.0 demo', 'tablet', '2026-10-23', '2026-10-23 15:33:00'),
(208, '103.35.171.76', '/page.php?slug=about', 'https://www.google.com/', 'Mozilla/5.0 demo', 'tablet', '2026-10-18', '2026-10-18 16:48:00'),
(209, '103.8.47.164', '/cart.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-27', '2026-10-27 12:56:00'),
(210, '103.47.189.239', '/cart.php', '', 'Mozilla/5.0 demo', 'tablet', '2026-10-22', '2026-10-22 10:27:00'),
(211, '103.6.55.184', '/checkout.php', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-26', '2026-10-26 07:05:00'),
(212, '103.27.88.122', '/track.php', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-06', '2026-11-06 13:31:00'),
(213, '103.25.109.65', '/track.php', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-31', '2026-10-31 21:09:00'),
(214, '103.56.127.104', '/cart.php', 'https://instagram.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-31', '2026-10-31 23:53:00'),
(215, '103.26.250.53', '/page.php?slug=about', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-13', '2026-10-13 18:43:00'),
(216, '103.49.77.33', '/shop.php', '', 'Mozilla/5.0 demo', 'tablet', '2026-11-04', '2026-11-04 01:11:00'),
(217, '103.60.104.140', '/', 'https://facebook.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-17', '2026-10-17 17:05:00'),
(218, '103.6.49.211', '/page.php?slug=about', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-07', '2026-11-07 11:48:00'),
(219, '103.38.157.97', '/shop.php?cat=dried-mango', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-15', '2026-10-15 18:55:00'),
(220, '103.46.163.118', '/shop.php?cat=dried-mango', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-04', '2026-11-04 10:40:00'),
(221, '103.30.199.26', '/', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-14', '2026-10-14 10:45:00'),
(222, '103.39.182.67', '/product.php?slug=dried-banana-chips-250g', '', 'Mozilla/5.0 demo', 'tablet', '2026-10-16', '2026-10-16 01:27:00'),
(223, '103.5.111.145', '/', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-09', '2026-11-09 01:08:00'),
(224, '103.53.208.108', '/checkout.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-15', '2026-10-15 07:57:00'),
(225, '103.28.153.152', '/shop.php?cat=dried-mango', 'https://facebook.com/', 'Mozilla/5.0 demo', 'tablet', '2026-11-09', '2026-11-09 12:48:00'),
(226, '103.9.165.121', '/product.php?slug=dried-mango-slices-250g', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-24', '2026-10-24 08:32:00'),
(227, '103.4.176.172', '/shop.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-28', '2026-10-28 11:51:00'),
(228, '103.12.121.5', '/product.php?slug=dried-banana-chips-250g', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-14', '2026-10-14 07:52:00'),
(229, '103.7.94.199', '/shop.php?cat=dried-mango', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-27', '2026-10-27 05:37:00'),
(230, '103.49.128.118', '/product.php?slug=dried-banana-chips-250g', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-20', '2026-10-20 16:59:00'),
(231, '103.4.227.167', '/shop.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'desktop', '2026-11-10', '2026-11-10 09:56:00'),
(232, '103.14.246.221', '/page.php?slug=about', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-31', '2026-10-31 15:31:00'),
(233, '103.40.187.113', '/page.php?slug=about', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-30', '2026-10-30 23:28:00'),
(234, '103.27.245.166', '/shop.php?cat=dried-mango', 'https://facebook.com/', 'Mozilla/5.0 demo', 'tablet', '2026-10-16', '2026-10-16 08:01:00'),
(235, '103.21.201.194', '/cart.php', 'https://www.google.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-28', '2026-10-28 20:33:00'),
(236, '103.45.181.206', '/cart.php', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-08', '2026-11-08 14:47:00'),
(237, '103.42.214.80', '/checkout.php', '', 'Mozilla/5.0 demo', 'tablet', '2026-10-15', '2026-10-15 06:51:00'),
(238, '103.42.198.230', '/product.php?slug=dried-mango-slices-250g', 'https://facebook.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-28', '2026-10-28 23:10:00'),
(239, '103.24.68.69', '/checkout.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-19', '2026-10-19 11:57:00'),
(240, '103.55.228.223', '/shop.php?cat=dried-mango', 'https://www.google.com/', 'Mozilla/5.0 demo', 'tablet', '2026-11-01', '2026-11-01 07:12:00'),
(241, '103.9.139.142', '/page.php?slug=about', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-29', '2026-10-29 17:46:00'),
(242, '103.23.156.15', '/checkout.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-16', '2026-10-16 04:01:00'),
(243, '103.41.193.3', '/checkout.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-12', '2026-10-12 03:19:00'),
(244, '103.53.17.60', '/checkout.php', 'https://instagram.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-23', '2026-10-23 23:53:00'),
(245, '103.24.123.130', '/product.php?slug=dried-mango-slices-250g', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-12', '2026-10-12 18:12:00'),
(246, '103.15.207.180', '/product.php?slug=dried-banana-chips-250g', 'https://www.google.com/', 'Mozilla/5.0 demo', 'desktop', '2026-11-04', '2026-11-04 08:52:00'),
(247, '103.58.39.64', '/', 'https://facebook.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-28', '2026-10-28 18:21:00'),
(248, '103.27.162.119', '/shop.php', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-14', '2026-10-14 08:23:00'),
(249, '103.26.72.133', '/', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-22', '2026-10-22 10:58:00'),
(250, '103.35.151.146', '/product.php?slug=dried-mango-slices-250g', 'https://www.google.com/', 'Mozilla/5.0 demo', 'desktop', '2026-11-03', '2026-11-03 16:42:00'),
(251, '103.32.197.152', '/shop.php?cat=dried-mango', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-28', '2026-10-28 08:42:00'),
(252, '103.15.97.22', '/', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-31', '2026-10-31 05:09:00'),
(253, '103.59.181.118', '/page.php?slug=about', '', 'Mozilla/5.0 demo', 'tablet', '2026-10-23', '2026-10-23 23:23:00'),
(254, '103.49.246.24', '/product.php?slug=dried-banana-chips-250g', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-12', '2026-10-12 07:55:00'),
(255, '103.46.130.101', '/shop.php?cat=dried-mango', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-21', '2026-10-21 08:57:00'),
(256, '103.50.57.45', '/', 'https://www.google.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-30', '2026-10-30 03:59:00'),
(257, '103.5.215.236', '/', 'https://www.google.com/', 'Mozilla/5.0 demo', 'tablet', '2026-11-08', '2026-11-08 17:08:00'),
(258, '103.13.82.194', '/', '', 'Mozilla/5.0 demo', 'desktop', '2026-11-09', '2026-11-09 08:34:00'),
(259, '103.32.195.168', '/shop.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-22', '2026-10-22 15:49:00'),
(260, '103.28.32.96', '/page.php?slug=about', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-30', '2026-10-30 03:14:00'),
(261, '103.55.37.235', '/', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-27', '2026-10-27 03:52:00'),
(262, '103.14.238.214', '/product.php?slug=dried-mango-slices-250g', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-16', '2026-10-16 10:47:00'),
(263, '103.53.115.246', '/shop.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-13', '2026-10-13 07:14:00'),
(264, '103.32.249.87', '/cart.php', '', 'Mozilla/5.0 demo', 'tablet', '2026-10-21', '2026-10-21 09:27:00'),
(265, '103.27.37.85', '/product.php?slug=dried-mango-slices-250g', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-06', '2026-11-06 14:34:00'),
(266, '103.39.228.38', '/page.php?slug=about', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-18', '2026-10-18 20:36:00'),
(267, '103.19.40.7', '/product.php?slug=dried-banana-chips-250g', 'https://instagram.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-27', '2026-10-27 21:57:00'),
(268, '103.20.126.28', '/cart.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'tablet', '2026-10-31', '2026-10-31 06:17:00'),
(269, '103.36.15.162', '/product.php?slug=dried-mango-slices-250g', 'https://instagram.com/', 'Mozilla/5.0 demo', 'tablet', '2026-11-07', '2026-11-07 06:46:00'),
(270, '103.11.66.194', '/product.php?slug=dried-mango-slices-250g', 'https://www.google.com/', 'Mozilla/5.0 demo', 'tablet', '2026-10-15', '2026-10-15 02:14:00'),
(271, '103.19.25.100', '/product.php?slug=dried-banana-chips-250g', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-02', '2026-11-02 03:52:00'),
(272, '103.13.164.5', '/page.php?slug=about', 'https://instagram.com/', 'Mozilla/5.0 demo', 'desktop', '2026-11-09', '2026-11-09 01:59:00'),
(273, '103.32.1.203', '/track.php', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-25', '2026-10-25 07:13:00'),
(274, '103.6.234.105', '/product.php?slug=dried-mango-slices-250g', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-29', '2026-10-29 21:09:00'),
(275, '103.37.198.59', '/shop.php?cat=dried-mango', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-06', '2026-11-06 05:52:00'),
(276, '103.60.156.188', '/page.php?slug=about', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-15', '2026-10-15 09:01:00'),
(277, '103.43.172.182', '/product.php?slug=dried-mango-slices-250g', 'https://instagram.com/', 'Mozilla/5.0 demo', 'desktop', '2026-11-04', '2026-11-04 07:20:00'),
(278, '103.8.178.188', '/track.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-04', '2026-11-04 11:40:00'),
(279, '103.25.73.216', '/page.php?slug=about', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-25', '2026-10-25 00:09:00'),
(280, '103.12.224.171', '/product.php?slug=dried-banana-chips-250g', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-27', '2026-10-27 19:44:00'),
(281, '103.6.42.180', '/cart.php', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-22', '2026-10-22 23:29:00'),
(282, '103.32.248.133', '/shop.php', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-29', '2026-10-29 18:16:00'),
(283, '103.56.213.222', '/cart.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-19', '2026-10-19 03:39:00'),
(284, '103.50.127.115', '/track.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-01', '2026-11-01 08:20:00'),
(285, '103.9.58.159', '/shop.php?cat=dried-mango', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-10', '2026-11-10 03:42:00'),
(286, '103.39.210.245', '/', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-01', '2026-11-01 03:28:00'),
(287, '103.20.5.215', '/page.php?slug=about', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-12', '2026-10-12 05:21:00'),
(288, '103.10.90.223', '/shop.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-18', '2026-10-18 20:51:00'),
(289, '103.33.127.150', '/track.php', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-02', '2026-11-02 07:48:00'),
(290, '103.29.227.36', '/track.php', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-07', '2026-11-07 07:52:00'),
(291, '103.13.172.147', '/page.php?slug=about', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-14', '2026-10-14 04:16:00'),
(292, '103.44.100.178', '/checkout.php', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-15', '2026-10-15 11:23:00'),
(293, '103.53.93.87', '/checkout.php', '', 'Mozilla/5.0 demo', 'desktop', '2026-10-29', '2026-10-29 10:14:00'),
(294, '103.57.145.206', '/cart.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-18', '2026-10-18 21:08:00'),
(295, '103.13.246.175', '/cart.php', '', 'Mozilla/5.0 demo', 'tablet', '2026-10-30', '2026-10-30 18:21:00'),
(296, '103.4.94.28', '/track.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-19', '2026-10-19 14:25:00'),
(297, '103.16.130.172', '/', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-30', '2026-10-30 21:14:00'),
(298, '103.53.240.117', '/', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-27', '2026-10-27 22:27:00'),
(299, '103.43.69.235', '/product.php?slug=dried-banana-chips-250g', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-13', '2026-10-13 14:07:00'),
(300, '103.20.32.134', '/', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-22', '2026-10-22 08:35:00'),
(301, '103.11.79.89', '/shop.php?cat=dried-mango', '', 'Mozilla/5.0 demo', 'mobile', '2026-11-09', '2026-11-09 02:12:00'),
(302, '103.21.22.120', '/track.php', '', 'Mozilla/5.0 demo', 'tablet', '2026-10-21', '2026-10-21 18:28:00'),
(303, '103.12.227.76', '/checkout.php', 'https://instagram.com/', 'Mozilla/5.0 demo', 'desktop', '2026-11-06', '2026-11-06 18:33:00'),
(304, '103.51.23.190', '/track.php', 'https://www.google.com/', 'Mozilla/5.0 demo', 'tablet', '2026-11-02', '2026-11-02 03:42:00'),
(305, '103.18.167.99', '/product.php?slug=dried-mango-slices-250g', 'https://instagram.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-21', '2026-10-21 16:33:00'),
(306, '103.23.157.123', '/page.php?slug=about', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-26', '2026-10-26 17:18:00'),
(307, '103.18.49.132', '/track.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'tablet', '2026-11-03', '2026-11-03 00:37:00'),
(308, '103.14.221.244', '/product.php?slug=dried-mango-slices-250g', 'https://www.google.com/', 'Mozilla/5.0 demo', 'desktop', '2026-11-09', '2026-11-09 23:58:00'),
(309, '103.22.225.56', '/product.php?slug=dried-banana-chips-250g', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-26', '2026-10-26 02:48:00'),
(310, '103.8.156.224', '/cart.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-04', '2026-11-04 17:12:00'),
(311, '103.57.113.91', '/shop.php', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-24', '2026-10-24 22:46:00'),
(312, '103.30.87.172', '/cart.php', '', 'Mozilla/5.0 demo', 'mobile', '2026-10-20', '2026-10-20 06:44:00'),
(313, '103.58.71.211', '/track.php', '', 'Mozilla/5.0 demo', 'tablet', '2026-10-21', '2026-10-21 15:10:00'),
(314, '103.49.162.235', '/shop.php?cat=dried-mango', 'https://facebook.com/', 'Mozilla/5.0 demo', 'mobile', '2026-11-01', '2026-11-01 14:29:00'),
(315, '103.53.141.150', '/shop.php', 'https://facebook.com/', 'Mozilla/5.0 demo', 'tablet', '2026-11-09', '2026-11-09 22:00:00'),
(316, '103.30.218.201', '/product.php?slug=dried-banana-chips-250g', 'https://instagram.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-23', '2026-10-23 06:54:00'),
(317, '103.48.118.74', '/cart.php', 'https://www.google.com/', 'Mozilla/5.0 demo', 'desktop', '2026-10-28', '2026-10-28 20:34:00'),
(318, '103.39.153.99', '/cart.php', '', 'Mozilla/5.0 demo', 'tablet', '2026-10-28', '2026-10-28 18:03:00'),
(319, '103.23.48.138', '/product.php?slug=dried-banana-chips-250g', '', 'Mozilla/5.0 demo', 'tablet', '2026-10-28', '2026-10-28 20:51:00'),
(320, '103.41.60.23', '/cart.php', 'https://www.google.com/', 'Mozilla/5.0 demo', 'mobile', '2026-10-22', '2026-10-22 23:08:00'),
(321, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:01:35'),
(322, '::1', '/drynutri/product.php?slug=premium-cashew-nuts-500g', 'http://localhost/drynutri/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:01:51'),
(323, '::1', '/drynutri/index.php', 'http://localhost/drynutri/product.php?slug=premium-cashew-nuts-500g', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:02:03'),
(324, '::1', '/drynutri/', 'http://localhost/drynutri/index.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:02:04'),
(325, '::1', '/drynutri/login.php', 'http://localhost/drynutri/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:02:11'),
(326, '::1', '/drynutri/register.php', 'http://localhost/drynutri/login.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:02:13'),
(327, '::1', '/drynutri/account.php', 'http://localhost/drynutri/register.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:02:34'),
(328, '::1', '/drynutri/account.php?tab=profile', 'http://localhost/drynutri/account.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:02:38'),
(329, '::1', '/drynutri/account.php?tab=orders', 'http://localhost/drynutri/account.php?tab=profile', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:02:42'),
(330, '::1', '/drynutri/account.php?tab=profile', 'http://localhost/drynutri/account.php?tab=orders', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:02:49'),
(331, '::1', '/drynutri/account.php?tab=orders', 'http://localhost/drynutri/account.php?tab=profile', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:02:55'),
(332, '::1', '/drynutri/account.php?tab=profile', 'http://localhost/drynutri/account.php?tab=orders', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:02:56'),
(333, '::1', '/drynutri/account.php?tab=orders', 'http://localhost/drynutri/account.php?tab=profile', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:02:56'),
(334, '::1', '/drynutri/account.php?tab=profile', 'http://localhost/drynutri/account.php?tab=orders', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:02:57'),
(335, '::1', '/drynutri/account.php?tab=profile', 'http://localhost/drynutri/account.php?tab=profile', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:02:57'),
(336, '::1', '/drynutri/account.php?tab=orders', 'http://localhost/drynutri/account.php?tab=profile', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:02:58'),
(337, '::1', '/drynutri/', 'http://localhost/drynutri/account.php?tab=orders', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:03:11'),
(338, '::1', '/drynutri/account.php', 'http://localhost/drynutri/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:06:46'),
(339, '::1', '/drynutri/index.php', 'http://localhost/drynutri/account.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:06:48'),
(340, '::1', '/drynutri/login.php', 'http://localhost/drynutri/index.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:06:50'),
(341, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9711', 'desktop', '2026-11-11', '2026-11-11 11:23:36'),
(342, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:23:41'),
(343, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:23:56'),
(344, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:24:41'),
(345, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:25:05'),
(346, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:29:21'),
(347, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:29:23'),
(348, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:29:23'),
(349, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:29:23'),
(350, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:29:24'),
(351, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:29:24'),
(352, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:29:24'),
(353, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:29:24'),
(354, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:29:24'),
(355, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:29:25'),
(356, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:29:25');
INSERT INTO `visitors` (`id`, `ip`, `page`, `referrer`, `user_agent`, `device`, `visit_date`, `created_at`) VALUES
(357, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:29:25'),
(358, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:29:25'),
(359, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:29:38'),
(360, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:30:03'),
(361, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:31:57'),
(362, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:31:57'),
(363, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:31:57'),
(364, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:31:58'),
(365, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:31:58'),
(366, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:31:58'),
(367, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:31:58'),
(368, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:31:59'),
(369, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:31:59'),
(370, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:31:59'),
(371, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:32:38'),
(372, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:41:22'),
(373, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:41:23'),
(374, '::1', '/drynutri/shop.php', 'http://localhost/drynutri/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:41:25'),
(375, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:41:26'),
(376, '::1', '/drynutri/shop.php?featured=1', 'http://localhost/drynutri/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:41:27'),
(377, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:41:27'),
(378, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:44:10'),
(379, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:44:13'),
(380, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:44:30'),
(381, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 11:47:36'),
(382, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:11:35'),
(383, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9711', 'desktop', '2026-11-11', '2026-11-11 12:11:40'),
(384, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:11:42'),
(385, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:11:42'),
(386, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:11:42'),
(387, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:11:43'),
(388, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:11:43'),
(389, '::1', '/drynutri/login.php', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:11:49'),
(390, '::1', '/drynutri/index.php', 'http://localhost/drynutri/login.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:12:44'),
(391, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9711', 'desktop', '2026-11-11', '2026-11-11 12:15:17'),
(392, '::1', '/drynutri/index.php', 'http://localhost/drynutri/login.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:17:52'),
(393, '::1', '/drynutri/index.php', 'http://localhost/drynutri/login.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:25:10'),
(394, '::1', '/drynutri/index.php', 'http://localhost/drynutri/login.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:45:34'),
(395, '::1', '/drynutri/index.php', 'http://localhost/drynutri/admin/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:47:29'),
(396, '::1', '/drynutri/login.php', 'http://localhost/drynutri/index.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:47:31'),
(397, '::1', '/drynutri/login.php', 'http://localhost/drynutri/admin/products.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:48:25'),
(398, '::1', '/drynutri/login.php', 'http://localhost/drynutri/admin/offers.php?a=edit&id=13', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:48:30'),
(399, '::1', '/drynutri/login.php', 'http://localhost/drynutri/admin/offers.php?a=edit&id=13', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:48:31'),
(400, '::1', '/drynutri/login.php', 'http://localhost/drynutri/admin/offers.php?a=edit&id=13', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:48:31'),
(401, '::1', '/drynutri/', 'http://localhost/drynutri/login.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:48:35'),
(402, '::1', '/drynutri/shop.php', 'http://localhost/drynutri/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:49:11'),
(403, '::1', '/drynutri/index.php', 'http://localhost/drynutri/shop.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:49:12'),
(404, '::1', '/drynutri/shop.php', 'http://localhost/drynutri/index.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:49:13'),
(405, '::1', '/drynutri/shop.php?cat=dried-mango', 'http://localhost/drynutri/shop.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:49:13'),
(406, '::1', '/drynutri/shop.php?cat=dried-orange', 'http://localhost/drynutri/shop.php?cat=dried-mango', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:49:14'),
(407, '::1', '/drynutri/shop.php', 'http://localhost/drynutri/shop.php?cat=dried-orange', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:49:19'),
(408, '::1', '/drynutri/login.php', 'http://localhost/drynutri/shop.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:49:21'),
(409, '::1', '/drynutri/login.php', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:53:05'),
(410, '::1', '/drynutri/login.php', 'http://localhost/drynutri/admin/products.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:53:10'),
(411, '::1', '/drynutri/forgot_password.php', 'http://localhost/drynutri/login.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:53:24'),
(412, '::1', '/drynutri/login.php', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:53:29'),
(413, '::1', '/drynutri/forgot_password.php', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:53:29'),
(414, '::1', '/drynutri/forgot_password.php', 'http://localhost/drynutri/forgot_password.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:53:31'),
(415, '::1', '/drynutri/login.php', 'http://localhost/drynutri/forgot_password.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:53:45'),
(416, '::1', '/drynutri/register.php', 'http://localhost/drynutri/login.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:55:05'),
(417, '::1', '/drynutri/account.php', 'http://localhost/drynutri/register.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:55:20'),
(418, '::1', '/drynutri/account.php?tab=profile', 'http://localhost/drynutri/account.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:55:25'),
(419, '::1', '/drynutri/account.php?tab=profile', 'http://localhost/drynutri/account.php?tab=profile', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:55:34'),
(420, '::1', '/drynutri/index.php', 'http://localhost/drynutri/account.php?tab=profile', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:55:37'),
(421, '::1', '/drynutri/index.php', 'http://localhost/drynutri/index.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:55:42'),
(422, '::1', '/drynutri/login.php', 'http://localhost/drynutri/index.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:55:44'),
(423, '::1', '/drynutri/login.php', 'http://localhost/drynutri/login.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:56:11'),
(424, '::1', '/drynutri/forgot_password.php?token=invalid', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 12:56:54'),
(425, '::1', '/drynutri/login.php', 'http://localhost/drynutri/forgot_password.php?token=invalid', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 13:12:27'),
(426, '::1', '/drynutri/index.php', 'http://localhost/drynutri/admin/profile.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 13:39:19'),
(427, '::1', '/drynutri/login.php', 'http://localhost/drynutri/index.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 13:39:21'),
(428, '::1', '/drynutri/index.php', 'http://localhost/drynutri/admin/profile.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 13:56:08'),
(429, '::1', '/drynutri/index.php', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 13:57:14'),
(430, '::1', '/drynutri/product.php?slug=tropical-trail-mix-300g', 'http://localhost/drynutri/index.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 13:59:06'),
(431, '::1', '/drynutri/index.php', 'http://localhost/drynutri/product.php?slug=tropical-trail-mix-300g', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.145.2 Chrome/150.0.7871.250 Electron/43.8.3 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 13:59:10'),
(432, '::1', '/drynutri/index.php', 'http://localhost/drynutri/admin/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 14:04:18'),
(433, '::1', '/drynutri/login.php', 'http://localhost/drynutri/index.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 14:04:19'),
(434, '::1', '/drynutri/index.php', 'http://localhost/drynutri/admin/orders.php?status=pending', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 14:05:42'),
(435, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:47:25'),
(436, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:49:36'),
(437, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:50:59'),
(438, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:53:10'),
(439, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:54:47'),
(440, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:54:48'),
(441, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:54:48'),
(442, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:54:55'),
(443, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:54:55'),
(444, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:54:55'),
(445, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:54:55'),
(446, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:54:56'),
(447, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:54:56'),
(448, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:54:56'),
(449, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:54:56'),
(450, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:54:56'),
(451, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:54:57'),
(452, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:54:57'),
(453, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:54:57'),
(454, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:54:57'),
(455, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:54:57'),
(456, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:54:58'),
(457, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:54:58'),
(458, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:54:58'),
(459, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:54:58'),
(460, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:54:59'),
(461, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:54:59'),
(462, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:54:59'),
(463, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:57:33'),
(464, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:57:33'),
(465, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:57:33'),
(466, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:57:34'),
(467, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:57:34'),
(468, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:57:34'),
(469, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:57:34'),
(470, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:57:34'),
(471, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:57:34'),
(472, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:57:35'),
(473, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:57:35'),
(474, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:57:35'),
(475, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:57:36'),
(476, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:57:36'),
(477, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:57:36'),
(478, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:57:36'),
(479, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:57:37'),
(480, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:57:37'),
(481, '::1', '/drynutri/login.php', 'http://localhost/drynutri/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 15:57:38'),
(482, '::1', '/drynutri/', '', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.9711', 'desktop', '2026-11-11', '2026-11-11 16:10:43'),
(483, '::1', '/drynutri/index.php', 'http://localhost/drynutri/admin/finance.php?tab=overview', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/157.0.0.0 Safari/537.36', 'desktop', '2026-11-11', '2026-11-11 16:18:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_notification_reads`
--
ALTER TABLE `admin_notification_reads`
  ADD PRIMARY KEY (`staff_id`,`item_type`,`item_id`),
  ADD KEY `read_at` (`read_at`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `checkout_visits`
--
ALTER TABLE `checkout_visits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sid` (`sid`),
  ADD KEY `status` (`status`),
  ADD KEY `phone` (`phone`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `features`
--
ALTER TABLE `features`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`ip`);

--
-- Indexes for table `mail_log`
--
ALTER TABLE `mail_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newsletter`
--
ALTER TABLE `newsletter`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_no` (`order_no`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `order_log`
--
ALTER TABLE `order_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `token_hash` (`token_hash`),
  ADD KEY `request_ip_created` (`request_ip`,`created_at`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payment_logos`
--
ALTER TABLE `payment_logos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `push_subscriptions`
--
ALTER TABLE `push_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `endpoint_hash` (`endpoint_hash`),
  ADD KEY `updated_at` (`updated_at`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`skey`);

--
-- Indexes for table `sliders`
--
ALTER TABLE `sliders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip` (`ip`),
  ADD KEY `visit_date` (`visit_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `checkout_visits`
--
ALTER TABLE `checkout_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `features`
--
ALTER TABLE `features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `mail_log`
--
ALTER TABLE `mail_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `newsletter`
--
ALTER TABLE `newsletter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `order_log`
--
ALTER TABLE `order_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_logos`
--
ALTER TABLE `payment_logos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `push_subscriptions`
--
ALTER TABLE `push_subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `sliders`
--
ALTER TABLE `sliders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `visitors`
--
ALTER TABLE `visitors`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=484;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
