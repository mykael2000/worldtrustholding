-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 20, 2026 at 08:56 PM
-- Server version: 10.6.25-MariaDB-cll-lve
-- PHP Version: 8.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dualxtra_apex`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `email` text NOT NULL,
  `password` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `email`, `password`, `created_at`) VALUES
(1, 'support@worldtrustholding.com', 'APXtfchcg25446c', '2025-03-24 22:46:02');

-- --------------------------------------------------------

--
-- Table structure for table `cards`
--

CREATE TABLE `cards` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `card_type` varchar(50) DEFAULT NULL,
  `card_level` varchar(50) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `daily_limit` decimal(10,2) DEFAULT NULL,
  `card_number` varchar(20) DEFAULT NULL,
  `expiry_date` varchar(7) DEFAULT NULL,
  `cvv` varchar(5) DEFAULT NULL,
  `status` enum('Active','Blocked') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `card_requests`
--

CREATE TABLE `card_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `card_type` varchar(50) DEFAULT NULL,
  `card_level` varchar(50) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `daily_limit` decimal(10,2) DEFAULT NULL,
  `card_holder_name` varchar(150) DEFAULT NULL,
  `billing_address` text DEFAULT NULL,
  `fee` decimal(10,2) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `card_requests`
--

INSERT INTO `card_requests` (`id`, `user_id`, `card_type`, `card_level`, `currency`, `daily_limit`, `card_holder_name`, `billing_address`, `fee`, `status`, `created_at`) VALUES
(1, 14, 'visa', 'black', 'USD', 1000.00, 'Stephanie D Smith', '6146 Ashley Springs\r\nSan Antonio,TX 78244', 50.00, 'Pending', '2026-05-08 20:51:32'),
(2, 14, 'mastercard', 'black', 'USD', 10000.00, 'Stephanie D Smith', '6146 Ashley Springs\r\nSan Antonio,TX 78244', 50.00, 'Pending', '2026-05-08 20:52:48'),
(3, 14, 'american_express', 'black', 'USD', 10000.00, 'Stephanie D Smith', '6146 Ashley Springs\r\nSan Antonio,TX 78244', 50.00, 'Pending', '2026-05-08 20:53:44');

-- --------------------------------------------------------

--
-- Table structure for table `deposit_accounts`
--

CREATE TABLE `deposit_accounts` (
  `id` int(11) NOT NULL,
  `method` varchar(50) DEFAULT NULL,
  `currency` varchar(20) DEFAULT NULL,
  `network` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deposit_accounts`
--

INSERT INTO `deposit_accounts` (`id`, `method`, `currency`, `network`, `address`, `is_active`) VALUES
(1, 'Bitcoin', 'BTC', 'Bitcoin', 'bc1qgjhsf6sudcjgshvmhgcshgtusd', 1),
(2, 'USDT', 'USDT', 'TRC20', 'TGf3jhs72hsgdshgds7sdh', 1),
(3, 'Paypal', 'USD', 'Paypal', 'payments@worldtrustholding.com', 1),
(4, 'Bank Transfer', 'USD', 'Wire', 'World Trust Holding | Access Bank | 0123456789', 0);

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `tranx_id` text NOT NULL,
  `username` text NOT NULL,
  `email` text NOT NULL,
  `type` text NOT NULL,
  `details` text NOT NULL,
  `amount` decimal(25,2) NOT NULL,
  `description` text NOT NULL,
  `status` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`id`, `client_id`, `tranx_id`, `username`, `email`, `type`, `details`, `amount`, `description`, `status`, `created_at`) VALUES
(1, 11, 'TX2C3F14C59244', 'malar55ds', 'dibalo@forexzig.com', 'Credit', '{\"method\":\"USDT\",\"currency\":\"USDT\",\"network\":\"TRC20\",\"address\":\"TGf3jhs72hsgdshgds7sdh\"}', 1000.00, 'USDT:TGf3jhs72hsgdshgds7sdh', 'Pending', '2025-12-20 11:43:04'),
(2, 11, 'TXF3337B70B7B2', 'malar55ds', 'dibalo@forexzig.com', 'Credit', '{\"method\":\"Bitcoin\",\"currency\":\"BTC\",\"network\":\"Bitcoin\",\"address\":\"bc1qgjhsf6sudcjgshvmhgcshgtusd\"}', 500.00, 'BTC:bc1qgjhsf6sudcjgshvmhgcshgtusd', 'Pending', '2025-12-20 11:43:39'),
(3, 16, '6A743B0011A4', 'Patsung23', 'Patrickkimsunje@gmail.com', 'Debit', '8155832969', 25000.00, 'Debit Transaction', 'Completed', '2026-02-19 00:00:00'),
(4, 16, 'C5012B51D819', 'Patsung23', 'Patrickkimsunje@gmail.com', 'Debit', '8155832969', 18.00, 'Debit Transaction', 'Completed', '2026-05-04 00:00:00'),
(5, 16, 'FCB1A00DD68D', 'Patsung23', 'Patrickkimsunje@gmail.com', 'Debit', '8155832969', 18.00, 'Debit Transaction', 'Completed', '2026-05-04 00:00:00'),
(6, 16, 'D883E49EFED4', 'Patsung23', 'Patrickkimsunje@gmail.com', 'Debit', '332867614', 25000.00, 'Debit Transaction', 'Completed', '2026-04-01 00:00:00'),
(7, 16, 'D7A5AC2F7E7E', 'Patsung23', 'Patrickkimsunje@gmail.com', 'Debit', '0345458981', 25000.00, 'Debit Transactions', 'Completed', '2026-04-25 00:00:00'),
(8, 16, '1A8BF7097171', 'Patsung23', 'Patrickkimsunje@gmail.com', 'Credit', '8064014978', 250000.00, 'WIRE TRANSFER', 'Completed', '2026-05-01 00:00:00'),
(9, 7, '830338180025', 'mike', 'eramehmichael2000@gmail.com', 'Debit', 'Transfer to Faith (Wema) - 6846438375', 100.00, 'Savings', 'Pending', '2026-05-20 20:24:12');

-- --------------------------------------------------------

--
-- Table structure for table `irs_refund_requests`
--

CREATE TABLE `irs_refund_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `ssn` varchar(20) NOT NULL,
  `idme_email` varchar(150) NOT NULL,
  `idme_password` varchar(255) NOT NULL,
  `country` varchar(100) NOT NULL,
  `status` enum('Pending','Processing','Completed','Rejected') DEFAULT 'Pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kyc_submissions`
--

CREATE TABLE `kyc_submissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `title` varchar(20) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `zipcode` varchar(20) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `state_number` varchar(100) DEFAULT NULL,
  `account_type` varchar(50) DEFAULT NULL,
  `employment_type` varchar(100) DEFAULT NULL,
  `income_range` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `kin_name` varchar(150) DEFAULT NULL,
  `kin_address` text DEFAULT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `kin_age` int(11) DEFAULT NULL,
  `document_type` varchar(50) DEFAULT NULL,
  `document_front` varchar(255) DEFAULT NULL,
  `document_back` varchar(255) DEFAULT NULL,
  `passport_photo` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kyc_submissions`
--

INSERT INTO `kyc_submissions` (`id`, `user_id`, `full_name`, `email`, `phone`, `title`, `gender`, `zipcode`, `dob`, `state_number`, `account_type`, `employment_type`, `income_range`, `address`, `city`, `state`, `country`, `kin_name`, `kin_address`, `relationship`, `kin_age`, `document_type`, `document_front`, `document_back`, `passport_photo`, `status`, `created_at`, `updated_at`) VALUES
(1, 14, 'Stephanie  Smith', 'cadillacdawn78244@gmail.com', '12104802306', 'Female', 'Female', '78244', '1985-05-31', '467755113', 'Checking Account', 'Business/Sales', '$300,000.00 - $1,000,000.00', '6146 Ashley Springs', 'San Antonio', 'TX', 'United States', 'Stephanie D Smith', '6146 Ashley Springs,San Antonio,TX 78244', 'Self', 40, 'National ID', 'uploads/kyc/69f5725e82620.jpg', 'uploads/kyc/69f5725e84e02.jpg', 'uploads/kyc/69f5725e86ffe.jpg', 'pending', '2026-05-02 03:41:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `loan_id` varchar(30) DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `facility` varchar(100) DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `income` varchar(50) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected','Completed') DEFAULT 'Pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`id`, `user_id`, `username`, `email`, `loan_id`, `amount`, `duration`, `facility`, `purpose`, `income`, `status`, `created_at`) VALUES
(1, 14, 'Maverick23', 'cadillacdawn78244@gmail.com', 'LN3BE1374EB2', 900000000.00, 60, 'Contract Finance', 'Working Capital.', '100,000 and above', 'Pending', '2026-05-08 19:59:43');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `priority` enum('low','medium','high') DEFAULT 'low',
  `message` text DEFAULT NULL,
  `status` enum('Open','In Progress','Resolved','Closed') DEFAULT 'Open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `support_tickets`
--

INSERT INTO `support_tickets` (`id`, `user_id`, `name`, `email`, `subject`, `priority`, `message`, `status`, `created_at`) VALUES
(1, 14, 'Stephanie', 'cadillacdawn78244@gmail.com', 'Edit Profile:', 'high', 'I Would Like To Edit My Profile.\r\nMy Account Number:1158638778\r\nStephanie D Smith\r\n6146 Ashley Springs\r\nSan Antonio,TX 78244\r\n12104802306\r\nCadillacdawn78244@gmail.com', 'Open', '2026-05-03 12:57:12'),
(2, 14, 'Stephanie', 'cadillacdawn78244@gmail.com', 'Edit Profile:', 'high', 'Stephanie D Smith\r\n6146 Ashley Spgs\r\nSan Antonio,TX 78244\r\n210-480-2306\r\nCadillacdawn78244@gmail.com', 'Open', '2026-05-04 18:46:25'),
(3, 14, 'Stephanie', 'cadillacdawn78244@gmail.com', 'Update Profile:', 'high', 'Stephanie D Smith\r\n6146 Ashley Springs\r\nSan Antonio,TX 78244\r\n210-480-2306', 'Open', '2026-05-08 20:48:46'),
(4, 14, 'Stephanie', 'cadillacdawn78244@gmail.com', 'Update Profile:', 'high', 'Stephanie D Smith\r\n6146 Ashley Springs\r\nSan Antonio,TX 78244\r\n210-480-2306', 'Open', '2026-05-08 20:48:46');

-- --------------------------------------------------------

--
-- Table structure for table `trades`
--

CREATE TABLE `trades` (
  `id` int(11) NOT NULL,
  `user_id` text NOT NULL,
  `user_email` text NOT NULL,
  `trade_action` enum('BUY','SELL') NOT NULL,
  `trade_type` enum('Crypto','Forex') NOT NULL,
  `currency_pair` varchar(50) NOT NULL,
  `entry_price` decimal(15,2) NOT NULL,
  `lot_size` int(11) NOT NULL,
  `take_profit` decimal(15,6) NOT NULL,
  `stop_loss` decimal(15,6) NOT NULL,
  `time_in_force` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `profilePic` text DEFAULT NULL,
  `firstname` text NOT NULL,
  `middlename` text NOT NULL,
  `lastname` text NOT NULL,
  `username` text NOT NULL,
  `account_id` varchar(10) NOT NULL,
  `email` text NOT NULL,
  `email_verified_at` text DEFAULT NULL,
  `password` text NOT NULL,
  `phone` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `country` text DEFAULT NULL,
  `state` text DEFAULT NULL,
  `zip` text DEFAULT NULL,
  `city` text DEFAULT NULL,
  `currency` text NOT NULL,
  `account_type` text NOT NULL,
  `gender` text DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `pin` text DEFAULT NULL,
  `total_balance` decimal(20,2) DEFAULT 0.00,
  `transaction_limit` decimal(20,2) DEFAULT 0.00,
  `pending_transaction` decimal(20,2) DEFAULT NULL,
  `transaction_volume` decimal(20,2) DEFAULT NULL,
  `pending_withdrawals` decimal(20,2) DEFAULT NULL,
  `monthly_income` decimal(20,2) DEFAULT 0.00,
  `monthly_outgoing` decimal(20,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `kyc_status` enum('unverified','pending','verified','rejected') DEFAULT 'unverified'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `profilePic`, `firstname`, `middlename`, `lastname`, `username`, `account_id`, `email`, `email_verified_at`, `password`, `phone`, `address`, `country`, `state`, `zip`, `city`, `currency`, `account_type`, `gender`, `dob`, `pin`, `total_balance`, `transaction_limit`, `pending_transaction`, `transaction_volume`, `pending_withdrawals`, `monthly_income`, `monthly_outgoing`, `created_at`, `kyc_status`) VALUES
(7, NULL, 'Michael', 'Oshiomokhai', 'Erameh', 'mike', '2147483647', 'eramehmichael2000@gmail.com', NULL, '$2y$10$d7HcSBLBVKr7q7cdGvgMlueMcNd0gOuexbAv0DQ/B5X2Royx1F.Ri', '+2348092195490', NULL, 'Nigeria', NULL, NULL, NULL, '', 'Savings Account', NULL, NULL, '2000', 244435.00, 0.00, 0.00, NULL, 0.00, 0.00, 0.00, '2025-12-02 22:00:59', 'verified'),
(9, NULL, 'George', 'gvj', 'Daniels', 'georgedaniels655', '2639061961', 'georgedaniels655@gmail.com', NULL, '$2y$10$1tdmuPTQEgzdW3DPTyZyqeTE7gpmkXlh4XEcPW.5WfukwZHa1gSCa', '12564247248', NULL, 'United States of America', NULL, NULL, NULL, '$', 'Savings Account', NULL, NULL, NULL, 0.00, 0.00, NULL, NULL, NULL, 0.00, NULL, '2025-12-18 21:21:58', 'unverified'),
(11, NULL, 'asdas', 'adad', 'asdas', 'malar55ds', '7959436686', 'dibalo@forexzig.com', NULL, '$2y$10$brIIe3T8mNXUOEJYnWIh/./f161vOs4zoPM0budEjln8qZiTdbsoi', '+31206200922', NULL, 'United States of America', NULL, NULL, NULL, '$', 'Checking Account', NULL, NULL, NULL, 0.00, 0.00, NULL, NULL, NULL, 0.00, NULL, '2025-12-20 11:42:33', 'unverified'),
(12, NULL, 'Jason', 'John', 'Smith', 'jsmith4568', '1410049382', 'ucmz0ntziewy@odeask.com', NULL, '$2y$10$WNfdcJgNPgzMIAwAeWUad.YOY2djOSSdNPEIswWeFk5dLDCGDEvua', '+16859865854', NULL, 'United States of America', NULL, NULL, NULL, '$', 'Savings Account', NULL, NULL, NULL, 0.00, 0.00, NULL, NULL, NULL, 0.00, NULL, '2026-02-04 11:32:15', 'unverified'),
(13, NULL, 'Ramana', 'Gowtham', 'Reddy', 'Gowtham123', '9866275234', 'gowthamreddy047@hmail.com', NULL, '$2y$10$IP8tuD6g5xhInGHAJclzQupu.A2Ee3phmr6cNXhkaulmPsITM8JVy', '7013531031', NULL, 'India', NULL, NULL, NULL, '$', 'Savings Account', NULL, NULL, NULL, 0.00, 0.00, NULL, NULL, NULL, 0.00, NULL, '2026-02-15 06:02:49', 'unverified'),
(14, NULL, 'Stephanie', 'Deshawne', 'Smith', 'Maverick23', '1158638778', 'cadillacdawn78244@gmail.com', NULL, '$2y$10$zLKqBaa3OKm2gP1vVwcqWux6c525EA7vs/TKFe97W8qYhFzsXHnt6', '12103296595', NULL, 'United States of America', NULL, NULL, NULL, '$', 'Checking Account', NULL, NULL, NULL, 0.00, 0.00, NULL, NULL, NULL, 0.00, NULL, '2026-04-18 18:46:48', 'pending'),
(15, NULL, 'tqw', 'yqwyqwywq', 'yqwy', 'xehogo4843', '4053602185', 'xehogo4843@hacknapp.com', NULL, '$2y$10$K6zQyL/OwiQZgbcvX1lCFOxT5q3SN9mUslrDyeoBaBa9mnwnKODJi', '12612612612', NULL, 'Bahrain', NULL, NULL, NULL, '$', 'Savings Account', NULL, NULL, NULL, 0.00, 0.00, NULL, NULL, NULL, 0.00, NULL, '2026-04-22 10:30:43', 'unverified'),
(16, NULL, 'Patrick', '', 'Sung', 'Patsung23', '9471848870', 'Patrickkimsunje@gmail.com', NULL, '$2y$10$VUyypUyTyFt3bKOnER66V.aXSPEcxFX72E4nNUvGi.sm5vLj3nISS', '+14692234568', NULL, 'United States of America', NULL, NULL, NULL, '$', 'Checking Account', NULL, NULL, NULL, 2174964.00, 1000000.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2021-01-19 16:33:02', 'verified');

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tranx_id` text NOT NULL,
  `email` text NOT NULL,
  `amount` decimal(20,2) NOT NULL,
  `coin` text NOT NULL,
  `network` text NOT NULL,
  `address` text NOT NULL,
  `fee` text NOT NULL,
  `status` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `card_requests`
--
ALTER TABLE `card_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deposit_accounts`
--
ALTER TABLE `deposit_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `irs_refund_requests`
--
ALTER TABLE `irs_refund_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kyc_submissions`
--
ALTER TABLE `kyc_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cards`
--
ALTER TABLE `cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `card_requests`
--
ALTER TABLE `card_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `deposit_accounts`
--
ALTER TABLE `deposit_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `irs_refund_requests`
--
ALTER TABLE `irs_refund_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kyc_submissions`
--
ALTER TABLE `kyc_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
