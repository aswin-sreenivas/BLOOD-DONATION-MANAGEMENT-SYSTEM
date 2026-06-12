-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 14, 2026 at 04:07 PM
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
-- Database: `blood_donation_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `Admin_ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`Admin_ID`, `Name`, `Email`, `Password`, `Created_At`) VALUES
(1, 'Super Admin', 'admin@bloodbank.local', '$2y$12$4WoDtlA19WhiqwEF3pBSreK1dmWFX3OfVIz/6Tl0FSDZWBVYyesJu', '2026-03-23 04:12:59');

-- --------------------------------------------------------

--
-- Table structure for table `bloodissue`
--

CREATE TABLE `bloodissue` (
  `Issue_ID` int(11) NOT NULL,
  `Hospital_ID` int(11) NOT NULL,
  `Request_ID` int(11) NOT NULL,
  `Unit_ID` varchar(50) NOT NULL,
  `Issued_By_Staff_ID` int(11) DEFAULT NULL,
  `Issue_Date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bloodunit`
--

CREATE TABLE `bloodunit` (
  `Unit_ID` varchar(50) NOT NULL,
  `Donation_ID` int(11) NOT NULL,
  `Inventory_ID` int(11) NOT NULL,
  `Collection_Date` date NOT NULL,
  `Expiry_Date` date NOT NULL,
  `Status` enum('Available','Issued','Discarded','Expired') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blood_inventory`
--

CREATE TABLE `blood_inventory` (
  `Inventory_ID` int(11) NOT NULL,
  `Hospital_ID` int(11) NOT NULL,
  `Blood_Group` varchar(5) NOT NULL,
  `Units_Available` int(11) DEFAULT 0,
  `Last_Updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blood_inventory`
--

INSERT INTO `blood_inventory` (`Inventory_ID`, `Hospital_ID`, `Blood_Group`, `Units_Available`, `Last_Updated`) VALUES
(1, 2, 'B+', 12, '2026-03-23 04:35:00');

-- --------------------------------------------------------

--
-- Table structure for table `blood_request`
--

CREATE TABLE `blood_request` (
  `Request_ID` int(11) NOT NULL,
  `Recipient_ID` int(11) NOT NULL,
  `Blood_Group` varchar(5) NOT NULL,
  `Quantity` int(11) NOT NULL DEFAULT 1,
  `District` varchar(50) DEFAULT NULL,
  `Emergency_Status` enum('Yes','No') DEFAULT 'No',
  `Request_Date` timestamp NOT NULL DEFAULT current_timestamp(),
  `Status` enum('Pending','Matched','Fulfilled','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blood_request`
--

INSERT INTO `blood_request` (`Request_ID`, `Recipient_ID`, `Blood_Group`, `Quantity`, `District`, `Emergency_Status`, `Request_Date`, `Status`) VALUES
(1, 3, 'A+', 1, 'Wayanad', '', '2026-03-25 05:51:17', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `donation`
--

CREATE TABLE `donation` (
  `Donation_ID` int(11) NOT NULL,
  `Donor_ID` int(11) NOT NULL,
  `Request_ID` int(11) DEFAULT NULL,
  `Hospital_ID` int(11) DEFAULT NULL,
  `Donation_Date` date NOT NULL,
  `Units_Donated` int(11) DEFAULT 1,
  `Donation_Status` enum('Scheduled','Completed','Cancelled','Pending Verification') DEFAULT 'Pending Verification'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donor`
--

CREATE TABLE `donor` (
  `Donor_ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Age` int(11) DEFAULT NULL,
  `Gender` enum('Male','Female','Other') DEFAULT NULL,
  `Blood_Group` varchar(5) NOT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Email` varchar(100) NOT NULL,
  `District` varchar(50) DEFAULT NULL,
  `Availability_Status` enum('Available','Unavailable') DEFAULT 'Available',
  `Last_Donation_Date` date DEFAULT NULL,
  `Password` varchar(255) NOT NULL,
  `Status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donor`
--

INSERT INTO `donor` (`Donor_ID`, `Name`, `Age`, `Gender`, `Blood_Group`, `Phone`, `Email`, `District`, `Availability_Status`, `Last_Donation_Date`, `Password`, `Status`, `Created_At`) VALUES
(12, 'jyothi', 19, 'Male', 'B+', '6238445571', 'jyothi@gmail.com', 'Kozhikode', 'Available', NULL, '$2y$10$hbb/kn2bUX8HS9K8GitoROKtQb.Ab3bbNMdcGjDaxN8L8xNOSdAbC', 'Approved', '2026-05-13 04:13:40'),
(13, 'GOPIKA MK', 20, 'Female', 'A+', '9567445081', 'gopika@gmail.com', 'Kozhikode', 'Available', NULL, '$2y$10$5io8Y36Vm8TpGPTa0TRp2OkVX3aTMCfy8GPMlNdxEDhk6QSWBROWW', 'Approved', '2026-05-13 05:54:46'),
(14, 'sk', 21, 'Male', 'O+', '8590916423', 'sarang@gmail.com', 'Wayanad', 'Available', NULL, '$2y$10$Uy2LKxCIGzsTGBlFpEJP2.lXIZxW1BLyS8XcdqVxHpUmZpmEaw1tm', 'Approved', '2026-05-13 06:13:08'),
(15, 'surin ks', 21, 'Male', 'AB+', '6238445571', 'surin@gmail.com', 'Wayanad', 'Available', NULL, '$2y$10$HHCSIgS7QhSek.eH71IWnOarpQzuIfAHsvk7L2LRmmUklsmIBA11i', 'Approved', '2026-05-13 06:28:46'),
(16, 'gopika mk', 20, 'Female', 'A+', '9567445081', 'gopi@gmail.com', 'Wayanad', 'Unavailable', NULL, '$2y$10$eXwWVfcPgep8EO3CJqnDo.onRiM1jNaHJ8RzwIY7e79av5Ntt.8Ei', 'Approved', '2026-05-13 07:11:56'),
(17, 'deviday', 20, 'Female', 'B-', '1234567890', 'devu@gmail.com', 'Wayanad', 'Available', NULL, '$2y$10$56qwctF/LWkwvP.vkZL.eelZQKbS4TQLhGnptaGlDWeB5CfOgi8oG', 'Approved', '2026-05-13 07:16:57'),
(18, 'kithu', 20, 'Male', 'O+', '1234567890', 'kithu@123', 'Wayanad', 'Available', NULL, '$2y$10$Zb90KbLYFraG2Zn.BDjLfu.xJunsYKv7DZJXNPbyRRme5/UoQu/C6', 'Rejected', '2026-05-14 04:22:00');

-- --------------------------------------------------------

--
-- Table structure for table `hospital`
--

CREATE TABLE `hospital` (
  `Hospital_ID` int(11) NOT NULL,
  `Hospital_Name` varchar(150) NOT NULL,
  `Location` varchar(255) DEFAULT NULL,
  `Contact_Number` varchar(20) DEFAULT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospital`
--

INSERT INTO `hospital` (`Hospital_ID`, `Hospital_Name`, `Location`, `Contact_Number`, `Email`, `Password`, `Status`, `Created_At`) VALUES
(1, 'GOV MANANTHAVADY', 'wayanad', '123456789', 'mndy@gmail.com', '$2y$10$2y1FvAf5s6BCSVl3c1o2luK33EK9T11dPQ4MkTmO7JgImfoILO16i', 'Rejected', '2026-03-23 04:28:38'),
(2, 'GOV MANANTHAVADY', 'wayanad', '7306257449', 'gov@gmail.com', '$2y$10$kpy55aGBd6mxjQgPGuzg0eGAdHG1W392Dgt2y/O56pISozGwRUkkW', 'Approved', '2026-03-23 04:34:24'),
(3, 'HOSPITAL', 'MANNARKAD', '7306257449', 'hospital@demo.com', '$2y$10$WC.LEhn80FkciJgN/T64D.UzB9MLah3TQOpjxJGhbahZ3/04OrljW', 'Approved', '2026-05-10 16:08:50'),
(4, 'medical college mndy', 'mananthavady', '8590916423', 'mndyclg@gmail.com', '$2y$10$8IbiJj4BoX8ApusUy0nyK./qyflT/UseHOtr0eU0CzjtuuvAm9Vgi', 'Approved', '2026-05-13 06:34:24'),
(5, 'gfg', 'vgg', '6238445571', 'cl@gmail.com', '$2y$10$AG0HjuT.Tv.mWXSgN09O0uHMqxh7wgb806Xtw/ICReBSsyt5OCJW2', 'Approved', '2026-05-13 06:40:05'),
(6, 'poly', 'Wayanad', '1234567890', 'poly@gmail.com', '$2y$10$41t4c8IhlKR/pWIvuVODkePEztwT5jGIoFtCSMib7b1WXxFHL.DU2', 'Approved', '2026-05-14 06:28:51');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `Notification_ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  `User_Type` enum('Admin','Donor','Recipient','Hospital','Staff') NOT NULL,
  `Message` text NOT NULL,
  `Is_Read` tinyint(1) DEFAULT 0,
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recipient`
--

CREATE TABLE `recipient` (
  `Recipient_ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Blood_Group` varchar(5) NOT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Email` varchar(100) NOT NULL,
  `District` varchar(50) DEFAULT NULL,
  `Emergency_Flag` enum('Yes','No') DEFAULT 'No',
  `Password` varchar(255) NOT NULL,
  `Status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipient`
--

INSERT INTO `recipient` (`Recipient_ID`, `Name`, `Blood_Group`, `Phone`, `Email`, `District`, `Emergency_Flag`, `Password`, `Status`, `Created_At`) VALUES
(1, 'surin', 'A+', '07306257449', 'surin@gmail.com', 'wayanad', 'Yes', '$2y$10$nhuMy5qGqCvAsS8BUKk7hODSHZApkPwhGoq4RYGZVmgSsIn/Z7zJq', 'Approved', '2026-03-23 04:28:01'),
(3, 'recipient', 'A+', '9947139167', 'rec@gmail.com', 'Wayanad', 'Yes', '$2y$10$lHQe.caOwJXKDh/DVgKM8OxXj6ng52ajYAgpBL59cFqB9OhoqtaxS', 'Approved', '2026-03-25 05:46:45'),
(4, 'Aswin sreenivas', 'A+', '07306257449', 'aswin.sreenivas005@gmail.com', 'wayanad', 'No', '$2y$10$l83KZ5QlLdfkevjo6k4i.e9fzOiPEoG4chOX8l/HQKVjgqelhFVpW', 'Rejected', '2026-04-26 15:18:32'),
(6, 'SHAJI MK', 'A-', '787878787878', 'shaji@gmail.com', 'Kozhikode', 'Yes', '$2y$10$rV8Q2vLzhgyYVY6y771VKeNrNE.OdT6gwLwZFh6ZGnU6xncYVb4bi', 'Rejected', '2026-05-10 16:06:19');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `Staff_ID` int(11) NOT NULL,
  `Hospital_ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Role` varchar(50) DEFAULT 'Nurse',
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Status` enum('Active','Inactive') DEFAULT 'Active',
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`Staff_ID`, `Hospital_ID`, `Name`, `Role`, `Email`, `Password`, `Status`, `Created_At`) VALUES
(1, 1, 'gopi', 'HEAD OF DEP', 'gopika@gmail.com', 'gopika123', 'Active', '2026-03-23 04:29:14'),
(2, 2, 'suril', 'HEAD OF DEP', 'aswin.sreenivas005@gmail.com', '$2y$10$QbWIUSiL7ay9yrRivbW6SOVUEbzfrxZIgRbHdecTlCvHSUUUHVfTC', 'Active', '2026-03-23 04:36:10');

-- --------------------------------------------------------

--
-- Table structure for table `staff_leave`
--

CREATE TABLE `staff_leave` (
  `Leave_ID` int(11) NOT NULL,
  `Staff_ID` int(11) NOT NULL,
  `From_Date` date NOT NULL,
  `To_Date` date NOT NULL,
  `Reason` text NOT NULL,
  `Status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `Requested_At` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`Admin_ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `bloodissue`
--
ALTER TABLE `bloodissue`
  ADD PRIMARY KEY (`Issue_ID`),
  ADD KEY `Hospital_ID` (`Hospital_ID`),
  ADD KEY `Request_ID` (`Request_ID`),
  ADD KEY `Unit_ID` (`Unit_ID`),
  ADD KEY `Issued_By_Staff_ID` (`Issued_By_Staff_ID`);

--
-- Indexes for table `bloodunit`
--
ALTER TABLE `bloodunit`
  ADD PRIMARY KEY (`Unit_ID`),
  ADD KEY `Donation_ID` (`Donation_ID`),
  ADD KEY `Inventory_ID` (`Inventory_ID`);

--
-- Indexes for table `blood_inventory`
--
ALTER TABLE `blood_inventory`
  ADD PRIMARY KEY (`Inventory_ID`),
  ADD KEY `Hospital_ID` (`Hospital_ID`);

--
-- Indexes for table `blood_request`
--
ALTER TABLE `blood_request`
  ADD PRIMARY KEY (`Request_ID`),
  ADD KEY `Recipient_ID` (`Recipient_ID`);

--
-- Indexes for table `donation`
--
ALTER TABLE `donation`
  ADD PRIMARY KEY (`Donation_ID`),
  ADD KEY `Donor_ID` (`Donor_ID`),
  ADD KEY `Request_ID` (`Request_ID`),
  ADD KEY `Hospital_ID` (`Hospital_ID`);

--
-- Indexes for table `donor`
--
ALTER TABLE `donor`
  ADD PRIMARY KEY (`Donor_ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `hospital`
--
ALTER TABLE `hospital`
  ADD PRIMARY KEY (`Hospital_ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`Notification_ID`);

--
-- Indexes for table `recipient`
--
ALTER TABLE `recipient`
  ADD PRIMARY KEY (`Recipient_ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`Staff_ID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `Hospital_ID` (`Hospital_ID`);

--
-- Indexes for table `staff_leave`
--
ALTER TABLE `staff_leave`
  ADD PRIMARY KEY (`Leave_ID`),
  ADD KEY `Staff_ID` (`Staff_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `Admin_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bloodissue`
--
ALTER TABLE `bloodissue`
  MODIFY `Issue_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blood_inventory`
--
ALTER TABLE `blood_inventory`
  MODIFY `Inventory_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `blood_request`
--
ALTER TABLE `blood_request`
  MODIFY `Request_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `donation`
--
ALTER TABLE `donation`
  MODIFY `Donation_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donor`
--
ALTER TABLE `donor`
  MODIFY `Donor_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `hospital`
--
ALTER TABLE `hospital`
  MODIFY `Hospital_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `Notification_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recipient`
--
ALTER TABLE `recipient`
  MODIFY `Recipient_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `Staff_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `staff_leave`
--
ALTER TABLE `staff_leave`
  MODIFY `Leave_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bloodissue`
--
ALTER TABLE `bloodissue`
  ADD CONSTRAINT `bloodissue_ibfk_1` FOREIGN KEY (`Hospital_ID`) REFERENCES `hospital` (`Hospital_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `bloodissue_ibfk_2` FOREIGN KEY (`Request_ID`) REFERENCES `blood_request` (`Request_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `bloodissue_ibfk_3` FOREIGN KEY (`Unit_ID`) REFERENCES `bloodunit` (`Unit_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `bloodissue_ibfk_4` FOREIGN KEY (`Issued_By_Staff_ID`) REFERENCES `staff` (`Staff_ID`) ON DELETE SET NULL;

--
-- Constraints for table `bloodunit`
--
ALTER TABLE `bloodunit`
  ADD CONSTRAINT `bloodunit_ibfk_1` FOREIGN KEY (`Donation_ID`) REFERENCES `donation` (`Donation_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `bloodunit_ibfk_2` FOREIGN KEY (`Inventory_ID`) REFERENCES `blood_inventory` (`Inventory_ID`) ON DELETE CASCADE;

--
-- Constraints for table `blood_inventory`
--
ALTER TABLE `blood_inventory`
  ADD CONSTRAINT `blood_inventory_ibfk_1` FOREIGN KEY (`Hospital_ID`) REFERENCES `hospital` (`Hospital_ID`) ON DELETE CASCADE;

--
-- Constraints for table `blood_request`
--
ALTER TABLE `blood_request`
  ADD CONSTRAINT `blood_request_ibfk_1` FOREIGN KEY (`Recipient_ID`) REFERENCES `recipient` (`Recipient_ID`) ON DELETE CASCADE;

--
-- Constraints for table `donation`
--
ALTER TABLE `donation`
  ADD CONSTRAINT `donation_ibfk_1` FOREIGN KEY (`Donor_ID`) REFERENCES `donor` (`Donor_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `donation_ibfk_2` FOREIGN KEY (`Request_ID`) REFERENCES `blood_request` (`Request_ID`) ON DELETE SET NULL,
  ADD CONSTRAINT `donation_ibfk_3` FOREIGN KEY (`Hospital_ID`) REFERENCES `hospital` (`Hospital_ID`) ON DELETE SET NULL;

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`Hospital_ID`) REFERENCES `hospital` (`Hospital_ID`) ON DELETE CASCADE;

--
-- Constraints for table `staff_leave`
--
ALTER TABLE `staff_leave`
  ADD CONSTRAINT `staff_leave_ibfk_1` FOREIGN KEY (`Staff_ID`) REFERENCES `staff` (`Staff_ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
