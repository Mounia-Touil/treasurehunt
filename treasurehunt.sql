-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 30, 2025 at 04:42 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `treasurehunt`
--

-- --------------------------------------------------------

--
-- Table structure for table `Bids`
--

CREATE TABLE `Bids` (
  `BidID` int(11) NOT NULL,
  `ItemID` int(11) DEFAULT NULL,
  `BidderID` int(11) DEFAULT NULL,
  `BidAmount` decimal(10,2) DEFAULT NULL,
  `BidTime` datetime DEFAULT current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `Categories`
--

CREATE TABLE `Categories` (
  `CategoryID` int(11) NOT NULL,
  `CategoryName` varchar(100) NOT NULL,
  `ParentCategoryID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Categories`
--

INSERT INTO `Categories` (`CategoryID`, `CategoryName`, `ParentCategoryID`) VALUES
(1, 'Electronics', NULL),
(2, 'Fashion', NULL),
(3, 'Home & Garden', NULL),
(4, 'Jewelry & Watches', NULL),
(5, 'Automobiles', NULL),
(6, 'Collectibles', NULL),
(7, 'Books', NULL),
(8, 'Toys & Hobbies', NULL),
(9, 'Sports Equipment', NULL),
(10, 'Health & Beauty', NULL),
(11, 'Music & Instruments', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Items`
--

CREATE TABLE `Items` (
  `ItemID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL,
  `ImageURL` varchar(255) DEFAULT NULL,
  `CategoryID` int(11) DEFAULT NULL,
  `SellerID` int(11) DEFAULT NULL,
  `StartTime` datetime DEFAULT NULL,
  `EndTime` datetime DEFAULT NULL,
  `StartingPrice` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Items`
--

INSERT INTO `Items` (`ItemID`, `Name`, `Description`, `ImageURL`, `CategoryID`, `SellerID`, `StartTime`, `EndTime`, `StartingPrice`) VALUES
(1, 'Rolex oyster', 'qwer', '../uploads/Rolex oyster.webp', 4, NULL, NULL, NULL, 12.00),
(3, 'Rolex oyster', '325435', '../uploads/IMG_1547.jpg', 2, 1, '2025-06-29 21:31:32', '2025-06-30 00:31:32', 123.00),
(4, 'Rolex oyster 21', '121212121221', '../uploads/Screenshot 2025-06-24 at 8.46.11 PM.png', 4, 1, '2025-06-29 21:32:37', '2025-06-30 18:32:37', 121212.00);

-- --------------------------------------------------------

--
-- Table structure for table `Purchases`
--

CREATE TABLE `Purchases` (
  `PurchaseID` int(11) NOT NULL,
  `ItemID` int(11) DEFAULT NULL,
  `BuyerID` int(11) DEFAULT NULL,
  `SoldPrice` decimal(10,2) DEFAULT NULL,
  `SoldDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Reports`
--

CREATE TABLE `Reports` (
  `ID` int(11) NOT NULL,
  `AdminID` int(11) DEFAULT NULL,
  `Action` varchar(255) DEFAULT NULL,
  `Timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `FullName` varchar(100) DEFAULT NULL,
  `ShippingAddress` text DEFAULT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `CreditCardInfo` varchar(100) DEFAULT NULL,
  `IsAdmin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`UserID`, `Username`, `Password`, `FullName`, `ShippingAddress`, `PhoneNumber`, `Email`, `CreditCardInfo`, `IsAdmin`) VALUES
(1, '169825715911qq11', '$2y$10$7.KJARHy2C/xroKvkGlGwu59eIlVCAOSjCkgf1W5kVwTlTD9iUDsy', 'Mounia Touil', '100 Demo St', '5550100001', 'demo.user@example.com', '1324123412341234', 0),
(10, 'mtouil12', '$2y$10$pBhiJiZgIY1ssKdkdzQG2umbtFbp5UnD8tQYbQQHrSVyTksBO0y1S', NULL, '100 Demo St', '1231234567', 'demo.user2@example.com', NULL, 0),
(11, 'MouniaTouil', '$2y$10$smo7j1Xq5wvkOkYl/b2ihOTKHMbr3.b5w1bP.FJuNeWMXr4ol5i3q', NULL, '200 Sample Rd', '5550100001', 'demo.user2@example.com', NULL, 0),
(12, 'mtouil122', '$2y$10$CYR6N8U0bV81OgXFnKWN3uZnXh5wW82BLURpzmMkT./dApmlWMYsW', 'moroho', '100 Demo St', '4332524367', 'demo.user@example.com', '1324123412341234', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Bids`
--
ALTER TABLE `Bids`
  ADD PRIMARY KEY (`BidID`),
  ADD KEY `ItemID` (`ItemID`),
  ADD KEY `BidderID` (`BidderID`);

--
-- Indexes for table `Categories`
--
ALTER TABLE `Categories`
  ADD PRIMARY KEY (`CategoryID`),
  ADD KEY `ParentCategoryID` (`ParentCategoryID`);

--
-- Indexes for table `Items`
--
ALTER TABLE `Items`
  ADD PRIMARY KEY (`ItemID`),
  ADD KEY `CategoryID` (`CategoryID`),
  ADD KEY `SellerID` (`SellerID`);

--
-- Indexes for table `Purchases`
--
ALTER TABLE `Purchases`
  ADD PRIMARY KEY (`PurchaseID`),
  ADD UNIQUE KEY `ItemID` (`ItemID`),
  ADD KEY `BuyerID` (`BuyerID`);

--
-- Indexes for table `Reports`
--
ALTER TABLE `Reports`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `AdminID` (`AdminID`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Bids`
--
ALTER TABLE `Bids`
  MODIFY `BidID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Categories`
--
ALTER TABLE `Categories`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `Items`
--
ALTER TABLE `Items`
  MODIFY `ItemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `Purchases`
--
ALTER TABLE `Purchases`
  MODIFY `PurchaseID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Reports`
--
ALTER TABLE `Reports`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Bids`
--
ALTER TABLE `Bids`
  ADD CONSTRAINT `bids_ibfk_1` FOREIGN KEY (`ItemID`) REFERENCES `Items` (`ItemID`),
  ADD CONSTRAINT `bids_ibfk_2` FOREIGN KEY (`BidderID`) REFERENCES `Users` (`UserID`);

--
-- Constraints for table `Categories`
--
ALTER TABLE `Categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`ParentCategoryID`) REFERENCES `Categories` (`CategoryID`);

--
-- Constraints for table `Items`
--
ALTER TABLE `Items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `Categories` (`CategoryID`),
  ADD CONSTRAINT `items_ibfk_2` FOREIGN KEY (`SellerID`) REFERENCES `Users` (`UserID`);

--
-- Constraints for table `Purchases`
--
ALTER TABLE `Purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`ItemID`) REFERENCES `Items` (`ItemID`),
  ADD CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`BuyerID`) REFERENCES `Users` (`UserID`);

--
-- Constraints for table `Reports`
--
ALTER TABLE `Reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`AdminID`) REFERENCES `Users` (`UserID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
