-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2026 at 12:02 PM
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
-- Database: `sharon_store_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_audit_logs`
--

CREATE TABLE `tbl_audit_logs` (
  `LogID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `Action_Performed` text NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_audit_logs`
--

INSERT INTO `tbl_audit_logs` (`LogID`, `UserID`, `Action_Performed`, `Timestamp`) VALUES
(1, 1, 'User logged in', '2026-05-02 09:55:41'),
(2, 1, 'User logged out', '2026-05-02 09:57:10'),
(3, 1, 'User logged in', '2026-05-02 09:59:04'),
(4, 1, 'User logged out', '2026-05-02 09:59:41'),
(5, 2, 'User logged in', '2026-05-02 09:59:45');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_forecasts`
--

CREATE TABLE `tbl_forecasts` (
  `ForecastID` int(11) NOT NULL,
  `ProductID` int(11) DEFAULT NULL,
  `Recommended_Restock_Qty` int(11) NOT NULL,
  `Forecast_Date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_inventory`
--

CREATE TABLE `tbl_inventory` (
  `ProductID` int(11) NOT NULL,
  `Barcode_ID` varchar(100) DEFAULT NULL,
  `Item_Name` varchar(150) NOT NULL,
  `Category` varchar(50) DEFAULT NULL,
  `Selling_Price` decimal(10,2) NOT NULL,
  `Stock_Quantity` int(11) NOT NULL DEFAULT 0,
  `Expiration_Date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_transactions`
--

CREATE TABLE `tbl_transactions` (
  `TransactionID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `Transaction_Date` timestamp NOT NULL DEFAULT current_timestamp(),
  `Total_Amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_transaction_details`
--

CREATE TABLE `tbl_transaction_details` (
  `DetailID` int(11) NOT NULL,
  `TransactionID` int(11) DEFAULT NULL,
  `ProductID` int(11) DEFAULT NULL,
  `Quantity_Sold` int(11) NOT NULL,
  `Sub_Total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Role` enum('Admin','Cashier') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`UserID`, `Username`, `Password`, `Role`) VALUES
(1, 'cuns', '$2y$10$39LGb4T.uGtD0cHk9a26YujjQAzLFzzqubfr8RNSMtd/2ibbbWMe.', 'Admin'),
(2, 'jam', '$2y$10$K/pZFyChlNqJHgkNgU5mZuOX1W9LeGBHX51l2LatwHPPOhddCgmf2', 'Cashier');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_audit_logs`
--
ALTER TABLE `tbl_audit_logs`
  ADD PRIMARY KEY (`LogID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `tbl_forecasts`
--
ALTER TABLE `tbl_forecasts`
  ADD PRIMARY KEY (`ForecastID`),
  ADD KEY `ProductID` (`ProductID`);

--
-- Indexes for table `tbl_inventory`
--
ALTER TABLE `tbl_inventory`
  ADD PRIMARY KEY (`ProductID`),
  ADD UNIQUE KEY `Barcode_ID` (`Barcode_ID`);

--
-- Indexes for table `tbl_transactions`
--
ALTER TABLE `tbl_transactions`
  ADD PRIMARY KEY (`TransactionID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `tbl_transaction_details`
--
ALTER TABLE `tbl_transaction_details`
  ADD PRIMARY KEY (`DetailID`),
  ADD KEY `TransactionID` (`TransactionID`),
  ADD KEY `ProductID` (`ProductID`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_audit_logs`
--
ALTER TABLE `tbl_audit_logs`
  MODIFY `LogID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_forecasts`
--
ALTER TABLE `tbl_forecasts`
  MODIFY `ForecastID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_inventory`
--
ALTER TABLE `tbl_inventory`
  MODIFY `ProductID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_transactions`
--
ALTER TABLE `tbl_transactions`
  MODIFY `TransactionID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_transaction_details`
--
ALTER TABLE `tbl_transaction_details`
  MODIFY `DetailID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_audit_logs`
--
ALTER TABLE `tbl_audit_logs`
  ADD CONSTRAINT `tbl_audit_logs_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `tbl_users` (`UserID`) ON DELETE SET NULL;

--
-- Constraints for table `tbl_forecasts`
--
ALTER TABLE `tbl_forecasts`
  ADD CONSTRAINT `tbl_forecasts_ibfk_1` FOREIGN KEY (`ProductID`) REFERENCES `tbl_inventory` (`ProductID`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_transactions`
--
ALTER TABLE `tbl_transactions`
  ADD CONSTRAINT `tbl_transactions_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `tbl_users` (`UserID`) ON DELETE SET NULL;

--
-- Constraints for table `tbl_transaction_details`
--
ALTER TABLE `tbl_transaction_details`
  ADD CONSTRAINT `tbl_transaction_details_ibfk_1` FOREIGN KEY (`TransactionID`) REFERENCES `tbl_transactions` (`TransactionID`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_transaction_details_ibfk_2` FOREIGN KEY (`ProductID`) REFERENCES `tbl_inventory` (`ProductID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
