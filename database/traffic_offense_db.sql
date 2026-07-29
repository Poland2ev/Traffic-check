-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 19, 2021 at 10:14 AM
-- Server version: 10.4.19-MariaDB
-- PHP Version: 8.0.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `traffic_offense_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `drivers_list`
--

CREATE TABLE `drivers_list` (
  `id` int(30) NOT NULL,
  `license_id_no` varchar(100) NOT NULL,
  `name` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = active, 2 = suspended, 3 = banned',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `drivers_list`
--

INSERT INTO `drivers_list` (`id`, `license_id_no`, `name`, `status`, `date_created`, `date_updated`) VALUES
(1, 'CDM-062314', 'Smith Johnny D', 1, '2021-08-19 10:45:48', '2021-08-19 10:53:02'),
(4, 'GBN-10140715', 'Blake Claire C', 1, '2021-08-19 14:56:09', NULL),
(5, 'CDM-062314', 'Smith Johnny D', 1, '2021-08-19 15:10:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `drivers_meta`
--

CREATE TABLE `drivers_meta` (
  `driver_id` int(30) DEFAULT NULL,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL,
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `drivers_meta`
--

INSERT INTO `drivers_meta` (`driver_id`, `meta_field`, `meta_value`, `date_updated`) VALUES
(1, 'license_id_no', 'CDM-062314', '2021-08-19 10:53:02'),
(1, 'lastname', 'Smith', '2021-08-19 10:53:02'),
(1, 'firstname', 'Johnny', '2021-08-19 10:53:02'),
(1, 'middlename', 'D', '2021-08-19 10:53:02'),
(1, 'dob', '1997-06-23', '2021-08-19 10:53:02'),
(1, 'present_address', 'Sample Address', '2021-08-19 10:53:02'),
(1, 'permanent_address', 'Sample Address', '2021-08-19 10:53:02'),
(1, 'civil_status', 'Married', '2021-08-19 10:53:02'),
(1, 'nationality', 'Filipino', '2021-08-19 10:53:02'),
(1, 'contact', '09123456789', '2021-08-19 10:53:02'),
(1, 'cccd_no', '001097000001', '2021-08-19 10:53:02'),
(1, 'plate_no', '30A-123.45', '2021-08-19 10:53:02'),
(1, 'vehicle_type', 'Ô tô', '2021-08-19 10:53:02'),
(1, 'vehicle_brand', 'Toyota Vios', '2021-08-19 10:53:02'),
(1, 'vehicle_color', 'Trắng', '2021-08-19 10:53:02'),
(1, 'user_id', '9', '2021-08-19 10:53:02'),
(1, 'license_type', 'Professional', '2021-08-19 10:53:02'),
(1, 'image_path', 'uploads/drivers/1.jpg', '2021-08-19 10:53:02'),
(1, 'driver_id', '1', '2021-08-19 10:53:02'),
(4, 'license_id_no', 'GBN-10140715', '2021-08-19 14:56:09'),
(4, 'lastname', 'Blake', '2021-08-19 14:56:09'),
(4, 'firstname', 'Claire', '2021-08-19 14:56:09'),
(4, 'middlename', 'C', '2021-08-19 14:56:09'),
(4, 'dob', '1992-10-14', '2021-08-19 14:56:09'),
(4, 'present_address', 'Sample Address 123', '2021-08-19 14:56:09'),
(4, 'permanent_address', 'Sample Address 123', '2021-08-19 14:56:09'),
(4, 'civil_status', 'Married', '2021-08-19 14:56:09'),
(4, 'nationality', 'Filipino', '2021-08-19 14:56:09'),
(4, 'contact', '09123789456', '2021-08-19 14:56:09'),
(4, 'cccd_no', '001092000004', '2021-08-19 14:56:09'),
(4, 'plate_no', '29B-456.78', '2021-08-19 14:56:09'),
(4, 'vehicle_type', 'Xe máy', '2021-08-19 14:56:09'),
(4, 'vehicle_brand', 'Honda Air Blade', '2021-08-19 14:56:09'),
(4, 'vehicle_color', 'Đen', '2021-08-19 14:56:09'),
(4, 'license_type', 'Non-Professional', '2021-08-19 14:56:09'),
(4, 'image_path', '', '2021-08-19 14:56:09'),
(4, 'driver_id', '4', '2021-08-19 14:56:09'),
(4, 'image_path', 'uploads/drivers/4.jpg', '2021-08-19 14:56:09'),
(5, 'license_id_no', 'CDM-062314', '2021-08-19 15:10:00'),
(5, 'lastname', 'Smith', '2021-08-19 15:10:00'),
(5, 'firstname', 'Johnny D', '2021-08-19 15:10:00'),
(5, 'present_address', 'Sample Address', '2021-08-19 15:10:00'),
(5, 'permanent_address', 'Sample Address', '2021-08-19 15:10:00'),
(5, 'contact', '09123456789', '2021-08-19 15:10:00'),
(5, 'cccd_no', '001097000001', '2021-08-19 15:10:00'),
(5, 'plate_no', '30F-999.99', '2021-08-19 15:10:00'),
(5, 'vehicle_type', 'Ô tô', '2021-08-19 15:10:00'),
(5, 'vehicle_brand', 'Mazda 3', '2021-08-19 15:10:00'),
(5, 'vehicle_color', 'Đỏ', '2021-08-19 15:10:00'),
(5, 'user_id', '9', '2021-08-19 15:10:00'),
(5, 'driver_id', '5', '2021-08-19 15:10:00');

-- --------------------------------------------------------

--
-- Table structure for table `offenses`
--

CREATE TABLE `offenses` (
  `id` int(30) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `fine` float NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0=Inactive, 1=Active',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `offenses`
--

INSERT INTO `offenses` (`id`, `code`, `name`, `description`, `fine`, `status`, `date_created`, `date_updated`) VALUES
(1, 'OT-1001', 'Driving without License', 'This is a traffic offense for driving without License', 650000, 1, '2021-08-19 09:14:43', '2021-08-19 09:17:50'),
(2, 'TO-1002', 'Running Over Speed Limit', '&lt;p&gt;Sample Traffic offense or violation for over speed limit.&lt;/p&gt;', 1000000, 1, '2021-08-19 13:54:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `offense_items`
--

CREATE TABLE `offense_items` (
  `driver_offense_id` int(30) NOT NULL,
  `offense_id` int(30) DEFAULT NULL,
  `fine` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=pending, 1=paid',
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `offense_items`
--

INSERT INTO `offense_items` (`driver_offense_id`, `offense_id`, `fine`, `status`, `date_created`) VALUES
(1, 1, 650000, 1, '2021-08-18 15:00:00'),
(1, 2, 1000000, 1, '2021-08-18 15:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `offense_list`
--

CREATE TABLE `offense_list` (
  `id` int(30) NOT NULL,
  `driver_id` int(30) NOT NULL,
  `officer_name` text NOT NULL,
  `officer_id` text NOT NULL,
  `ticket_no` text NOT NULL,
  `location` text DEFAULT NULL,
  `total_amount` float NOT NULL,
  `due_date` date DEFAULT NULL,
  `remarks` text NOT NULL,
  `evidence_path` text DEFAULT NULL,
  `evidence_type` varchar(20) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=pending, 1=paid',
  `payment_method` varchar(100) DEFAULT NULL,
  `payment_reference` varchar(150) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `offense_list`
--

INSERT INTO `offense_list` (`id`, `driver_id`, `officer_name`, `officer_id`, `ticket_no`, `location`, `total_amount`, `due_date`, `remarks`, `evidence_path`, `evidence_type`, `status`, `payment_method`, `payment_reference`, `date_created`, `date_updated`) VALUES
(1, 1, 'George Wilson', 'OFC-789456123', '123456789', 'Nút giao Nguyễn Trãi - Khuất Duy Tiến', 1650000, '2021-09-17', 'Sample Traffic Offense Record Only.', NULL, NULL, 1, 'Chuyển khoản ngân hàng', 'VPGT-123456789', '2021-08-18 15:00:00', '2021-08-19 14:20:59');

-- --------------------------------------------------------

--
-- Table structure for table `system_info`
--

CREATE TABLE `system_info` (
  `id` int(30) NOT NULL,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `system_info`
--

INSERT INTO `system_info` (`id`, `meta_field`, `meta_value`) VALUES
(1, 'name', 'Cổng tra cứu vi phạm giao thông'),
(6, 'short_name', 'Tra cứu VPGT'),
(11, 'logo', 'http://localhost/traffic_offense/uploads/1629334140_traffic_light_logo.png'),
(13, 'user_avatar', 'uploads/user_avatar.jpg'),
(14, 'cover', 'http://localhost/traffic_offense/uploads/1629334140_traffic_bg.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(50) NOT NULL,
  `firstname` varchar(250) NOT NULL,
  `lastname` varchar(250) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `avatar` text DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `username`, `password`, `avatar`, `last_login`, `type`, `date_added`, `date_updated`) VALUES
(1, 'Adminstrator', 'Admin', 'admin', '0192023a7bbd73250516f069df18b500', 'uploads/1624240500_avatar.png', NULL, 1, '2021-01-20 14:02:37', '2021-06-21 09:55:07'),
(9, 'John', 'Smith', 'jsmith', '1254737c076cf867dc53d60a0364f38e', 'uploads/1629336240_avatar.jpg', NULL, 2, '2021-08-19 09:24:25', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `drivers_list`
--
ALTER TABLE `drivers_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `drivers_meta`
--
ALTER TABLE `drivers_meta`
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `offenses`
--
ALTER TABLE `offenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offense_items`
--
ALTER TABLE `offense_items`
  ADD KEY `driver_offense_id` (`driver_offense_id`),
  ADD KEY `offense_id` (`offense_id`);

--
-- Indexes for table `offense_list`
--
ALTER TABLE `offense_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `system_info`
--
ALTER TABLE `system_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `drivers_list`
--
ALTER TABLE `drivers_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `offenses`
--
ALTER TABLE `offenses`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `offense_list`
--
ALTER TABLE `offense_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `system_info`
--
ALTER TABLE `system_info`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `drivers_meta`
--
ALTER TABLE `drivers_meta`
  ADD CONSTRAINT `drivers_meta_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `drivers_list` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `offense_items`
--
ALTER TABLE `offense_items`
  ADD CONSTRAINT `offense_items_ibfk_1` FOREIGN KEY (`driver_offense_id`) REFERENCES `offense_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `offense_items_ibfk_2` FOREIGN KEY (`offense_id`) REFERENCES `offenses` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `offense_list`
--
ALTER TABLE `offense_list`
  ADD CONSTRAINT `offense_list_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `drivers_list` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
