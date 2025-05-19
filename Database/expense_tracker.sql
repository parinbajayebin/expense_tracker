-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 15, 2025 at 10:31 PM
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
-- Database: `expense_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `expense`
--

CREATE TABLE `expense` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,0) NOT NULL,
  `cat_types` enum('food','transport','education','entertainment','miscellaneous') NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `description` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expense`
--

INSERT INTO `expense` (`id`, `user_id`, `amount`, `cat_types`, `date`, `description`) VALUES
(2, 8, 3233, 'transport', '2025-02-15 09:25:06', 'ada'),
(3, 8, 3233, 'transport', '2025-02-15 09:29:22', 'ada'),
(4, 8, 3233, 'transport', '2025-02-15 09:30:13', 'ada'),
(5, 8, 2323, 'entertainment', '2025-02-15 09:32:25', 'adfds'),
(6, 8, 2323, 'entertainment', '2025-02-15 09:38:04', 'adfds'),
(7, 8, 111, 'transport', '2025-02-15 10:54:07', 'daa'),
(8, 8, 500, 'transport', '2025-02-15 11:06:55', 'wfwf'),
(9, 8, 888, 'transport', '2025-02-15 11:07:53', 'ddada');

-- --------------------------------------------------------

--
-- Table structure for table `financial_resources`
--

CREATE TABLE `financial_resources` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `resource_type` enum('article','video') NOT NULL,
  `link` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `financial_resources`
--

INSERT INTO `financial_resources` (`id`, `title`, `content`, `resource_type`, `link`) VALUES
(11, 'How to Save Money Efficiently', 'Learn practical ways to manage your savings better.', 'article', 'https://www.investopedia.com/articles/personal-finance/011216/10-simple-ways-save-money.asp'),
(12, 'Investment Strategies for Beginners', 'Understand the best strategies for investing your money wisely.', 'article', 'https://www.nerdwallet.com/article/investing/investing-for-beginners'),
(13, 'Understanding Credit Scores', 'Your credit score impacts financial decisions. Here’s what you need to know.', 'article', 'https://www.experian.com/blogs/news/what-is-a-good-credit-score/'),
(14, 'Debt Management Tips', 'Reduce your debts and manage payments effectively.', 'article', 'https://www.moneyunder30.com/debt-payoff-strategies'),
(15, 'Emergency Fund Planning', 'How much should you save for emergencies? Read more.', 'article', 'https://www.ramseysolutions.com/budgeting/emergency-fund'),
(16, 'Budgeting Basics for Students', 'A step-by-step guide on making a budget and sticking to it.', 'video', 'https://www.youtube.com/embed/7p6g4xEfaWw'),
(17, 'Stock Market for Beginners', 'Learn the fundamentals of investing in stocks.', 'video', 'https://www.youtube.com/embed/kh9uWZzwhCw'),
(18, 'How to Retire Early', 'Financial independence and early retirement strategies.', 'video', 'https://www.youtube.com/embed/gpWde7co6G0'),
(19, 'Saving vs Investing', 'Understanding when to save and when to invest.', 'video', 'https://www.youtube.com/embed/xH2UcvKaXZY'),
(20, 'Passive Income Ideas', 'Different ways to earn passive income.', 'video', 'https://www.youtube.com/embed/HlkVwEv8O6M');

-- --------------------------------------------------------

--
-- Table structure for table `income`
--

CREATE TABLE `income` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,0) NOT NULL,
  `source` enum('job','gift','allowance','extra') NOT NULL,
  `date` date NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `income`
--

INSERT INTO `income` (`id`, `user_id`, `amount`, `source`, `date`, `description`) VALUES
(1, 8, 5000, 'gift', '2025-02-12', 'bua ne di'),
(2, 8, 1000, 'gift', '2025-02-19', 'ddsv'),
(3, 8, 3000, 'gift', '2025-02-16', 'rgbb');

-- --------------------------------------------------------

--
-- Table structure for table `recurring_expense`
--

CREATE TABLE `recurring_expense` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `expense_name` enum('housing and rent','food and beverages','transportation','education','health','entertainment') NOT NULL,
  `expense_sub_name` varchar(255) NOT NULL,
  `amount` decimal(10,0) NOT NULL,
  `due_date` date NOT NULL,
  `frequency` enum('weekly','monthly','quarterly','yearly') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recurring_expense`
--

INSERT INTO `recurring_expense` (`id`, `user_id`, `expense_name`, `expense_sub_name`, `amount`, `due_date`, `frequency`) VALUES
(1, 8, 'entertainment', 'netflix', 10000, '2025-02-19', 'weekly'),
(2, 8, 'entertainment', 'netflix', 10000, '2025-02-19', 'weekly');

-- --------------------------------------------------------

--
-- Table structure for table `saving`
--

CREATE TABLE `saving` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `goal_name` varchar(100) NOT NULL,
  `target_amount` decimal(10,0) NOT NULL,
  `current_saving` decimal(10,0) NOT NULL,
  `goal_deadline` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `saving`
--

INSERT INTO `saving` (`id`, `user_id`, `goal_name`, `target_amount`, `current_saving`, `goal_deadline`) VALUES
(4, 8, 'parrot', 50000, 1000, '2025-02-11'),
(5, 8, 'parrot', 50000, 1000, '2025-02-11');

-- --------------------------------------------------------

--
-- Table structure for table `upi_transaction`
--

CREATE TABLE `upi_transaction` (
  `id` int(11) NOT NULL,
  `transaction_id` varchar(300) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,0) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` longtext NOT NULL,
  `mobileno` text NOT NULL,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `passkey` enum('fav_teacher','fav_movie','fav_food') NOT NULL,
  `passkeyval` varchar(200) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `password`, `mobileno`, `creation_date`, `passkey`, `passkeyval`, `username`, `email`) VALUES
(4, 'jay prajapati', '$2y$10$iw76U1bkmUOccPTs0xfA7.fWgQJ2Yex0Aiinmh3RBg60RB9vN6SWS', '0986328845', '2025-02-15 12:17:33', '', 'ee', 'admin', ''),
(5, 'Parin Dhavalbhai Makwana', '$2y$10$MVgQd5DqSgZTUQEglDZKq.ZVrmzoQupMhq0zMrZxiig1LMUMpJUCG', '0888888877', '2025-02-15 12:19:14', '', 'harry', 'parin_210', ''),
(7, 'ee', '$2y$10$ceroSZCWRLjH8PR5JKIX6e.BAj.ZK43WI.fWqnoTFeK3V5v3zZbya', '1222222222', '2025-02-15 12:21:54', '', 'rr', 'bhavna_22', 'parin@gmail.com'),
(8, 'Manav', '$2y$10$tBSRdYpuQI8rM2Igbs8tSuBWtQfoSXi8/H31EhrllhrEVJymjHvAS', '0635384420', '2025-02-15 12:23:58', 'fav_teacher', 'mm', 'maitry_123', 'manav@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `expense`
--
ALTER TABLE `expense`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_iddddd` (`user_id`);

--
-- Indexes for table `financial_resources`
--
ALTER TABLE `financial_resources`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `income`
--
ALTER TABLE `income`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_idddd` (`user_id`);

--
-- Indexes for table `recurring_expense`
--
ALTER TABLE `recurring_expense`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_iddd` (`user_id`);

--
-- Indexes for table `saving`
--
ALTER TABLE `saving`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_idd` (`user_id`);

--
-- Indexes for table `upi_transaction`
--
ALTER TABLE `upi_transaction`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `expense`
--
ALTER TABLE `expense`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `financial_resources`
--
ALTER TABLE `financial_resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `income`
--
ALTER TABLE `income`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `recurring_expense`
--
ALTER TABLE `recurring_expense`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `saving`
--
ALTER TABLE `saving`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `upi_transaction`
--
ALTER TABLE `upi_transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `expense`
--
ALTER TABLE `expense`
  ADD CONSTRAINT `user_iddddd` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `income`
--
ALTER TABLE `income`
  ADD CONSTRAINT `user_idddd` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `recurring_expense`
--
ALTER TABLE `recurring_expense`
  ADD CONSTRAINT `user_iddd` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `saving`
--
ALTER TABLE `saving`
  ADD CONSTRAINT `user_idd` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `upi_transaction`
--
ALTER TABLE `upi_transaction`
  ADD CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
