-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 18, 2025 at 05:05 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `carrental`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `UserName` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `updationDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `UserName`, `Password`, `updationDate`) VALUES
(1, 'admin', '5c428d8875d2948607f3e3fe134d71b4', '2024-05-01 12:22:38');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `date_range` text NOT NULL,
  `slot_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`slot_details`)),
  `total_price` decimal(10,2) NOT NULL,
  `reference_image` varchar(255) DEFAULT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `event_id`, `event_name`, `user_email`, `date_range`, `slot_details`, `total_price`, `reference_image`, `booking_date`, `status`) VALUES
(13, 10, 'School/College Events', 'nandhiniv.22msc@kongu.edu', '2025-04-29,2025-04-30', '{\"2025-04-29\":{\"slot_type\":\"Full-Day\",\"start_time\":\"\",\"end_time\":\"\"},\"2025-04-30\":{\"slot_type\":\"Half-Day\",\"start_time\":\"\",\"end_time\":\"\"}}', 45000.00, NULL, '2025-04-11 15:39:22', 'confirmed'),
(14, 8, 'Birthday Parties', 'balachandrankk.22msc@kongu.edu', '2025-04-16,2025-04-17', '{\"2025-04-16\":{\"slot_type\":\"Full-Day\",\"start_time\":\"\",\"end_time\":\"\"},\"2025-04-17\":{\"slot_type\":\"Full-Day\",\"start_time\":\"\",\"end_time\":\"\"}}', 30000.00, NULL, '2025-04-12 06:04:47', 'cancelled'),
(15, 6, 'Weddings', 'balachandrankk.22msc@kongu.edu', '2025-05-19', '{\"2025-05-19\":{\"slot_type\":\"Full-Day\",\"start_time\":\"\",\"end_time\":\"\"}}', 100000.00, NULL, '2025-04-12 06:06:12', 'confirmed'),
(16, 7, 'Corporate Events', 'sabarishv.22msc@kongu.edu', '2025-05-16,2025-05-17', '{\"2025-05-16\":{\"slot_type\":\"Half-Day\",\"start_time\":\"\",\"end_time\":\"\"},\"2025-05-17\":{\"slot_type\":\"Half-Day\",\"start_time\":\"\",\"end_time\":\"\"}}', 50000.00, NULL, '2025-04-12 06:16:15', 'pending'),
(17, 6, 'Weddings', 'sabarishv.22msc@kongu.edu', '2025-04-29', '{\"2025-04-29\":{\"slot_type\":\"Full-Day\",\"start_time\":\"\",\"end_time\":\"\"}}', 100000.00, NULL, '2025-04-12 06:40:09', 'confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `camera_booking_saved_report`
--

CREATE TABLE `camera_booking_saved_report` (
  `id` int(11) NOT NULL,
  `report_name` varchar(255) NOT NULL,
  `filters` text NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `saved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `camera_booking_saved_report`
--

INSERT INTO `camera_booking_saved_report` (`id`, `report_name`, `filters`, `from_date`, `to_date`, `saved_at`) VALUES
(1, 'Camera_Report_20250410_033150', '{\"user\":\"\",\"brand\":\"\",\"status\":\"\",\"date_type\":\"PostingDate\",\"quick_range\":\"7days\",\"fromdate\":\"2025-04-04\",\"todate\":\"2025-04-10\",\"generate\":\"\"}', '2025-04-04', '2025-04-10', '2025-04-10 01:31:57'),
(2, 'Camera_Report_20250410_033249', '{\"user\":\"\",\"brand\":\"\",\"status\":\"\",\"date_type\":\"PostingDate\",\"quick_range\":\"month\",\"fromdate\":\"2025-03-31\",\"todate\":\"2025-04-10\",\"generate\":\"\"}', '2025-03-31', '2025-04-10', '2025-04-10 01:32:56'),
(3, 'Camera_Report_20250410_034156', '{\"user\":\"\",\"brand\":\"\",\"status\":\"\",\"date_type\":\"PostingDate\",\"quick_range\":\"\",\"fromdate\":\"\",\"todate\":\"\",\"generate\":\"\"}', '2025-04-01', '2025-04-10', '2025-04-10 01:42:01');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `event_name` varchar(255) DEFAULT NULL,
  `base_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `event_name`, `base_price`) VALUES
(6, 'Weddings', 100000.00),
(7, 'Corporate Events', 50000.00),
(8, 'Birthday Parties', 15000.00),
(9, 'Baby Showers', 20000.00),
(10, 'School/College Events', 30000.00),
(11, 'Workshops', 5000.00);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `service_description` text DEFAULT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_name`, `service_description`, `image`) VALUES
(2, 'Wedding Photography', 'Capture your most cherished moments with our premium Wedding Photography Services, designed to make your big day unforgettable', 'wedd.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `tblbooking`
--

CREATE TABLE `tblbooking` (
  `id` int(11) NOT NULL,
  `BookingNumber` bigint(12) DEFAULT NULL,
  `userEmail` varchar(100) DEFAULT NULL,
  `VehicleId` int(11) DEFAULT NULL,
  `FromDate` date NOT NULL,
  `ToDate` date NOT NULL,
  `message` varchar(255) DEFAULT NULL,
  `status` enum('Pending Approval','Awaiting Payment','Paid and Confirmed','Cancelled') DEFAULT 'Pending Approval',
  `payment_id` varchar(255) DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `LastUpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `totalPrice` decimal(10,2) DEFAULT NULL,
  `razorpay_order_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblbooking`
--

INSERT INTO `tblbooking` (`id`, `BookingNumber`, `userEmail`, `VehicleId`, `FromDate`, `ToDate`, `message`, `status`, `payment_id`, `amount_paid`, `PostingDate`, `LastUpdationDate`, `totalPrice`, `razorpay_order_id`) VALUES
(24, 688617739, 'nandhiniv.22msc@kongu.edu', 13, '2025-04-29', '2025-05-03', 'i want', 'Paid and Confirmed', 'pay_QKRakrPJyOrIp1', 10000.00, '2025-04-18 07:49:02', '2025-04-18 07:50:40', 10000.00, 'order_QKRaBBeaKOlTRp'),
(27, 982138208, 'nandhiniv.22msc@kongu.edu', 14, '2025-05-08', '2025-05-10', 'i want', 'Cancelled', NULL, NULL, '2025-04-18 09:44:36', '2025-04-18 09:45:05', 5400.00, NULL),
(28, 857797863, 'nandhiniv.22msc@kongu.edu', 13, '2025-05-09', '2025-05-10', 'i want', 'Cancelled', NULL, NULL, '2025-04-18 10:36:04', '2025-04-18 11:15:35', 4000.00, NULL),
(29, 131622909, 'nandhiniv.22msc@kongu.edu', 15, '2025-05-01', '2025-05-10', 'i want', 'Paid and Confirmed', 'pay_QKWlpqUgOlsiIn', 25000.00, '2025-04-18 11:16:47', '2025-04-18 12:54:38', 25000.00, 'order_QKWjqyeYHRAAQ3');

-- --------------------------------------------------------

--
-- Table structure for table `tblbrands`
--

CREATE TABLE `tblbrands` (
  `id` int(11) NOT NULL,
  `BrandName` varchar(120) NOT NULL,
  `CreationDate` timestamp NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblbrands`
--

INSERT INTO `tblbrands` (`id`, `BrandName`, `CreationDate`, `UpdationDate`) VALUES
(10, 'Canon', '2025-03-27 15:39:23', '2025-03-28 14:53:51'),
(12, 'Nikon', '2025-04-11 14:08:53', NULL),
(13, 'Sony', '2025-04-11 14:08:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblcameras`
--

CREATE TABLE `tblcameras` (
  `id` int(11) NOT NULL,
  `VehiclesTitle` varchar(150) DEFAULT NULL,
  `VehiclesBrand` int(11) DEFAULT NULL,
  `VehiclesOverview` longtext DEFAULT NULL,
  `PricePerDay` int(11) DEFAULT NULL,
  `FuelType` varchar(100) DEFAULT NULL,
  `ModelYear` int(6) DEFAULT NULL,
  `SeatingCapacity` int(11) DEFAULT NULL,
  `Vimage1` varchar(120) DEFAULT NULL,
  `Vimage2` varchar(120) DEFAULT NULL,
  `Vimage3` varchar(120) DEFAULT NULL,
  `Vimage4` varchar(120) DEFAULT NULL,
  `Vimage5` varchar(120) DEFAULT NULL,
  `AirConditioner` int(11) DEFAULT NULL,
  `PowerDoorLocks` int(11) DEFAULT NULL,
  `AntiLockBrakingSystem` int(11) DEFAULT NULL,
  `BrakeAssist` int(11) DEFAULT NULL,
  `PowerSteering` int(11) DEFAULT NULL,
  `DriverAirbag` int(11) DEFAULT NULL,
  `PassengerAirbag` int(11) DEFAULT NULL,
  `PowerWindows` int(11) DEFAULT NULL,
  `CDPlayer` int(11) DEFAULT NULL,
  `CentralLocking` int(11) DEFAULT NULL,
  `CrashSensor` int(11) DEFAULT NULL,
  `LeatherSeats` int(11) DEFAULT NULL,
  `RegDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblcameras`
--

INSERT INTO `tblcameras` (`id`, `VehiclesTitle`, `VehiclesBrand`, `VehiclesOverview`, `PricePerDay`, `FuelType`, `ModelYear`, `SeatingCapacity`, `Vimage1`, `Vimage2`, `Vimage3`, `Vimage4`, `Vimage5`, `AirConditioner`, `PowerDoorLocks`, `AntiLockBrakingSystem`, `BrakeAssist`, `PowerSteering`, `DriverAirbag`, `PassengerAirbag`, `PowerWindows`, `CDPlayer`, `CentralLocking`, `CrashSensor`, `LeatherSeats`, `RegDate`, `UpdationDate`) VALUES
(13, ' Canon EOS R5', 10, 'The Canon EOS R5 is a high-resolution full-frame mirrorless camera with cutting-edge autofocus and 8K video recording capabilities. It’s designed for professional photographers and videographers.', 2000, 'RF lens', 2023, 45, 'canon1.jpg', 'canon2.jpg', 'canon3.jpg', NULL, NULL, NULL, 1, 1, NULL, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, '2025-03-27 17:13:10', '2025-04-11 14:31:30'),
(14, 'Nikon Z6 II', 12, 'The Nikon Z6 II is a versatile full-frame mirrorless camera built for both photo and video creators. It features dual EXPEED 6 processors for faster performance, improved autofocus, and 4K video capabilities. It\'s well-suited for weddings, wildlife, portraits, and events.', 1800, 'Z-mount lens', 2021, 25, 'n1.jpg', 'n2.jpg', 'n3.jpg', 'n4.jpg', 'n5.jpg', 1, 1, 1, 1, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, '2025-04-11 14:19:00', '2025-04-11 14:30:55'),
(15, 'Sony Alpha A7 III', 13, 'The Sony A7 III is a powerful and highly capable full-frame mirrorless camera designed for both enthusiasts and professionals. It delivers excellent image quality, low-light performance, and 4K video recording. Ideal for weddings, street photography, portraits, and filmmaking.', 2500, 'EF lens', 2018, 24, 's1.jpg', 's2.jpg', 's3.jpg', 's4.jpg', 's5.jpg', 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-11 14:38:19', '2025-04-11 14:39:05');

-- --------------------------------------------------------

--
-- Table structure for table `tblcontactusinfo`
--

CREATE TABLE `tblcontactusinfo` (
  `id` int(11) NOT NULL,
  `Address` tinytext DEFAULT NULL,
  `EmailId` varchar(255) DEFAULT NULL,
  `ContactNo` char(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblcontactusinfo`
--

INSERT INTO `tblcontactusinfo` (`id`, `Address`, `EmailId`, `ContactNo`) VALUES
(1, '174,1st floor,\r\n Ramarajyam complex,\r\nCSB Bank upstairs,\r\n Kunnathur Rd,\r\nPerundurai ,Erode, \r\nTamil Nadu 638052', 'snappyboys052@gmail.com', '8248722752');

-- --------------------------------------------------------

--
-- Table structure for table `tblcontactusquery`
--

CREATE TABLE `tblcontactusquery` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `EmailId` varchar(120) DEFAULT NULL,
  `ContactNumber` char(11) DEFAULT NULL,
  `Message` longtext DEFAULT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblcontactusquery`
--

INSERT INTO `tblcontactusquery` (`id`, `name`, `EmailId`, `ContactNumber`, `Message`, `PostingDate`, `status`) VALUES
(6, 'Sabarish', 'sabarishv.22msc@kongu.edu', '7373731298', 'i have queries in event booking', '2025-04-12 06:26:21', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblpages`
--

CREATE TABLE `tblpages` (
  `id` int(11) NOT NULL,
  `PageName` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT '',
  `detail` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblpages`
--

INSERT INTO `tblpages` (`id`, `PageName`, `type`, `detail`) VALUES
(1, 'Terms and Conditions', 'terms', '<P align=justify><FONT size=2><STRONG><FONT color=#990000>(1) ACCEPTANCE OF TERMS</FONT><BR><BR></STRONG>Welcome to Yahoo! India. 1Yahoo Web Services India Private Limited Yahoo\", \"we\" or \"us\" as the case may be) provides the Service (defined below) to you, subject to the following Terms of Service (\"TOS\"), which may be updated by us from time to time without notice to you. You can review the most current version of the TOS at any time at: <A href=\"http://in.docs.yahoo.com/info/terms/\">http://in.docs.yahoo.com/info/terms/</A>. In addition, when using particular Yahoo services or third party services, you and Yahoo shall be subject to any posted guidelines or rules applicable to such services which may be posted from time to time. All such guidelines or rules, which maybe subject to change, are hereby incorporated by reference into the TOS. In most cases the guides and rules are specific to a particular part of the Service and will assist you in applying the TOS to that part, but to the extent of any inconsistency between the TOS and any guide or rule, the TOS will prevail. We may also offer other services from time to time that are governed by different Terms of Services, in which case the TOS do not apply to such other services if and to the extent expressly excluded by such different Terms of Services. Yahoo also may offer other services from time to time that are governed by different Terms of Services. These TOS do not apply to such other services that are governed by different Terms of Service. </FONT></P>\r\n<P align=justify><FONT size=2>Welcome to Yahoo! India. Yahoo Web Services India Private Limited Yahoo\", \"we\" or \"us\" as the case may be) provides the Service (defined below) to you, subject to the following Terms of Service (\"TOS\"), which may be updated by us from time to time without notice to you. You can review the most current version of the TOS at any time at: </FONT><A href=\"http://in.docs.yahoo.com/info/terms/\"><FONT size=2>http://in.docs.yahoo.com/info/terms/</FONT></A><FONT size=2>. In addition, when using particular Yahoo services or third party services, you and Yahoo shall be subject to any posted guidelines or rules applicable to such services which may be posted from time to time. All such guidelines or rules, which maybe subject to change, are hereby incorporated by reference into the TOS. In most cases the guides and rules are specific to a particular part of the Service and will assist you in applying the TOS to that part, but to the extent of any inconsistency between the TOS and any guide or rule, the TOS will prevail. We may also offer other services from time to time that are governed by different Terms of Services, in which case the TOS do not apply to such other services if and to the extent expressly excluded by such different Terms of Services. Yahoo also may offer other services from time to time that are governed by different Terms of Services. These TOS do not apply to such other services that are governed by different Terms of Service. </FONT></P>\r\n<P align=justify><FONT size=2>Welcome to Yahoo! India. Yahoo Web Services India Private Limited Yahoo\", \"we\" or \"us\" as the case may be) provides the Service (defined below) to you, subject to the following Terms of Service (\"TOS\"), which may be updated by us from time to time without notice to you. You can review the most current version of the TOS at any time at: </FONT><A href=\"http://in.docs.yahoo.com/info/terms/\"><FONT size=2>http://in.docs.yahoo.com/info/terms/</FONT></A><FONT size=2>. In addition, when using particular Yahoo services or third party services, you and Yahoo shall be subject to any posted guidelines or rules applicable to such services which may be posted from time to time. All such guidelines or rules, which maybe subject to change, are hereby incorporated by reference into the TOS. In most cases the guides and rules are specific to a particular part of the Service and will assist you in applying the TOS to that part, but to the extent of any inconsistency between the TOS and any guide or rule, the TOS will prevail. We may also offer other services from time to time that are governed by different Terms of Services, in which case the TOS do not apply to such other services if and to the extent expressly excluded by such different Terms of Services. Yahoo also may offer other services from time to time that are governed by different Terms of Services. These TOS do not apply to such other services that are governed by different Terms of Service. </FONT></P>'),
(2, 'Privacy Policy', 'privacy', '<span style=\"color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; text-align: justify;\">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat</span>'),
(3, 'About Us ', 'aboutus', '<span style=\"color: rgb(51, 51, 51); font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 13.3333px;\">We offer a varied fleet of cars, ranging from the compact. All our vehicles have air conditioning, &nbsp;power steering, electric windows. All our vehicles are bought and maintained at official dealerships only. Automatic transmission cars are available in every booking class.&nbsp;</span><span style=\"color: rgb(52, 52, 52); font-family: Arial, Helvetica, sans-serif;\">As we are not affiliated with any specific automaker, we are able to provide a variety of vehicle makes and models for customers to rent.</span><div><span style=\"color: rgb(62, 62, 62); font-family: &quot;Lucida Sans Unicode&quot;, &quot;Lucida Grande&quot;, sans-serif; font-size: 11px;\">ur mission is to be recognised as the global leader in Car Rental for companies and the public and private sector by partnering with our clients to provide the best and most efficient Cab Rental solutions and to achieve service excellence.</span><span style=\"color: rgb(52, 52, 52); font-family: Arial, Helvetica, sans-serif;\"><br></span></div>'),
(11, 'FAQs', 'faqs', '																														<span style=\"color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; text-align: justify;\">Address------Test &nbsp; &nbsp;dsfdsfds</span>');

-- --------------------------------------------------------

--
-- Table structure for table `tbltestimonial`
--

CREATE TABLE `tbltestimonial` (
  `id` int(11) NOT NULL,
  `UserEmail` varchar(100) NOT NULL,
  `Testimonial` mediumtext NOT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbltestimonial`
--

INSERT INTO `tbltestimonial` (`id`, `UserEmail`, `Testimonial`, `PostingDate`, `status`) VALUES
(2, 'nandhiniv.22msc@kongu.edu', '?????\r\n“Snappy Boys exceeded all our expectations!”\r\nWorking with Snappy Boys was an absolute delight! Their creativity, professionalism, and attention to detail truly made a difference. From the very first conversation to the final delivery, they were proactive, responsive, and incredibly easy to work with. Our project wouldn’t have been the same without their fresh ideas and snappy execution.', '2025-04-11 15:06:03', NULL),
(3, 'nandhiniv.22msc@kongu.edu', '\r\n“Snappy Boys exceeded all our expectations!”\r\nWorking with Snappy Boys was an absolute delight! Their creativity, professionalism, and attention to detail truly made a difference. From the very first conversation to the final delivery, they were proactive, responsive, and incredibly easy to work with. Our project wouldn’t have been the same without their fresh ideas and snappy execution.', '2025-04-11 15:06:35', 1),
(4, 'balachandrankk.22msc@kongu.edu', '\"Snappy Boys Photography made our special day truly unforgettable! Their team was professional, friendly, and incredibly talented. Every moment — from candid smiles to emotional glances — was captured beautifully. The edits were stunning, and the turnaround time was quicker than expected. \"', '2025-04-12 06:09:35', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblusers`
--

CREATE TABLE `tblusers` (
  `id` int(11) NOT NULL,
  `FullName` varchar(120) DEFAULT NULL,
  `EmailId` varchar(100) DEFAULT NULL,
  `Password` varchar(100) DEFAULT NULL,
  `ContactNo` char(11) DEFAULT NULL,
  `dob` varchar(100) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `Country` varchar(100) DEFAULT NULL,
  `RegDate` timestamp NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblusers`
--

INSERT INTO `tblusers` (`id`, `FullName`, `EmailId`, `Password`, `ContactNo`, `dob`, `Address`, `City`, `Country`, `RegDate`, `UpdationDate`) VALUES
(11, 'Nandhini V', 'nandhiniv.22msc@kongu.edu', 'e807f1fcf82d132f9bb018ca6738a19f', '7200852922', NULL, NULL, NULL, NULL, '2025-04-11 15:03:29', NULL),
(12, 'Balachandran', 'balachandrankk.22msc@kongu.edu', '167194e2555537468b42aa5814b74eb7', '9865716893', NULL, NULL, NULL, NULL, '2025-04-12 06:03:16', NULL),
(13, 'Sabarish', 'sabarishv.22msc@kongu.edu', 'e807f1fcf82d132f9bb018ca6738a19f', '7373731298', '12/12/2004', '2,palamettupudur,perundurai,erode', 'Erode', 'India', '2025-04-12 06:13:38', '2025-04-12 06:28:09'),
(14, 'Asvita AT', 'asvitaat.22msc@kongu.edu', 'e807f1fcf82d132f9bb018ca6738a19f', '9832162801', NULL, NULL, NULL, NULL, '2025-04-17 10:49:49', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `camera_booking_saved_report`
--
ALTER TABLE `camera_booking_saved_report`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblbooking`
--
ALTER TABLE `tblbooking`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblbrands`
--
ALTER TABLE `tblbrands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblcameras`
--
ALTER TABLE `tblcameras`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblcontactusinfo`
--
ALTER TABLE `tblcontactusinfo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblcontactusquery`
--
ALTER TABLE `tblcontactusquery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblpages`
--
ALTER TABLE `tblpages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbltestimonial`
--
ALTER TABLE `tbltestimonial`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblusers`
--
ALTER TABLE `tblusers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `EmailId` (`EmailId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `camera_booking_saved_report`
--
ALTER TABLE `camera_booking_saved_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tblbooking`
--
ALTER TABLE `tblbooking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `tblbrands`
--
ALTER TABLE `tblbrands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tblcameras`
--
ALTER TABLE `tblcameras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tblcontactusinfo`
--
ALTER TABLE `tblcontactusinfo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblcontactusquery`
--
ALTER TABLE `tblcontactusquery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tblpages`
--
ALTER TABLE `tblpages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tbltestimonial`
--
ALTER TABLE `tbltestimonial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tblusers`
--
ALTER TABLE `tblusers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
