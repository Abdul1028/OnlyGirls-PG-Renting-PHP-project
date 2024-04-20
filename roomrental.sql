-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2024 at 02:53 PM
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
-- Database: `roomrental`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `room_title` varchar(255) NOT NULL,
  `room_description` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `price` int(25) NOT NULL,
  `number_of_days` int(25) NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `username` varchar(255) NOT NULL,
  `Total` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`room_title`, `room_description`, `location`, `price`, `number_of_days`, `date_from`, `date_to`, `username`, `Total`) VALUES
('Sohail', '2323', 'Mumbai', 1212, 8, '2024-04-22', '2024-05-09', 'BBB', '9696'),
('Room123', 'ggdfgdgcgdhh', 'Mumbai', 1000, 20, '2024-04-24', '2024-05-04', 'Sohail', '20000'),
('Sohail', 'weeewe', 'Mumbai', 1212, 5, '2024-04-30', '2024-05-09', 'Sohail', '6060'),
('Room1234', 'dfdfdfdfdf', 'Mumbai', 12000, 7, '2024-05-08', '2024-05-11', 'abcd', '84000'),
('ABC123', 'A very good room', 'Mumbai', 1200, 1, '2024-05-08', '2024-05-08', 'abcd', '1200'),
('ABC123', 'A very good room', 'Mumbai', 1200, 13, '2024-05-08', '2024-05-08', 'hello1', '15600');

-- --------------------------------------------------------

--
-- Table structure for table `listing`
--

CREATE TABLE `listing` (
  `Username` varchar(255) NOT NULL,
  `Room_Title` varchar(255) NOT NULL,
  `Room_Description` varchar(255) NOT NULL,
  `Location` varchar(255) NOT NULL,
  `Price` int(25) NOT NULL,
  `Date_From` date NOT NULL,
  `Date_To` date NOT NULL,
  `Image` longblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `listing`
--

INSERT INTO `listing` (`Username`, `Room_Title`, `Room_Description`, `Location`, `Price`, `Date_From`, `Date_To`, `Image`) VALUES
('Sohail', 'Sohail', 'wewe', 'Mumbai', 1212, '2024-04-30', '2024-05-01', ''),
('Sohail', 'Sohail', 'wwe', 'Delhi', 1212, '2024-05-01', '2024-05-03', ''),
('Sohail', 'Ssdsd', 'wewe', 'Pune', 1212, '2024-04-30', '2024-05-08', 0x4172726179),
('Sohail', 'Sohail', '2323', 'Mumbai', 1212, '2024-04-22', '2024-05-09', ''),
('Sohail', 'sd', '232', 'Delhi', 2323, '2024-05-08', '2024-05-09', 0x4172726179),
('Sohail', 'Sohail', 'wwewessss', 'Pune', 1212, '2024-05-07', '2024-06-07', 0x4172726179),
('Sohail', 'Sohail', 'qqqq', 'Pune', 1111, '2024-05-08', '2024-05-11', 0x4172726179),
('Sohail', 'Sohail', 'weeewe', 'Mumbai', 1212, '2024-04-30', '2024-05-09', 0x4172726179),
('Sohail', 'Sohail', 'sdsd', 'Mumbai', 121212, '2024-05-09', '2024-04-19', 0x4172726179),
('', 'ABC', 'sfgsdhhfshf', 'Mumbai', 233, '2024-05-09', '2024-04-19', 0x4172726179),
('abc123', 'Room123', 'ggdfgdgcgdhh', 'Mumbai', 1000, '2024-04-24', '2024-05-04', 0x4172726179),
('hello', 'Room1234', 'dfdfdfdfdf', 'Mumbai', 12000, '2024-05-08', '2024-05-11', 0x4172726179),
('xyz', 'ABC123', 'A very good room', 'Mumbai', 1200, '2024-05-08', '2024-05-08', 0x4172726179);

-- --------------------------------------------------------

--
-- Table structure for table `login_details`
--

CREATE TABLE `login_details` (
  `Name` varchar(255) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Phone_Number` int(25) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Age` int(10) NOT NULL,
  `Drink` varchar(255) NOT NULL,
  `Smoke` varchar(255) NOT NULL,
  `Married` varchar(255) NOT NULL,
  `Home_Town` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_details`
--

INSERT INTO `login_details` (`Name`, `Username`, `Phone_Number`, `Email`, `Password`, `Age`, `Drink`, `Smoke`, `Married`, `Home_Town`) VALUES
('Sohail Shaikh', 'Sohail', 2147483647, 'sssohail2902@gmail.com', '12', 12, 'single', 'no', 'no', 'Mumbai'),
('Sohail Shaikh', 'SohailShaikh', 2147483647, 'sssohail2902@gmail.com', '12', 12, 'single', 'yes', 'yes', 'Mumbai'),
('Sohail Shaikh', 'SohailShaikh', 2147483647, 'sssohail2902@gmail.com', '12', 12, 'single', 'yes', 'yes', 'Mumbai'),
('ABC', 'abc', 2147483647, 'sssohail2902@gmail.com', '1212', 12, 'single', 'no', 'no', 'Mumbai'),
('Sohail Shaikh', 'BBB', 2147483647, 'sssohail2902@gmail.com', '12', 1221, 'married', 'yes', 'yes', 'Mumbai'),
('Abc', 'abc123', 2147483647, 'sssohail2902@gmail.com', '12', 67, 'married', 'no', 'no', 'Mumbai'),
('Hello', 'hello', 2147483647, 'sssohail2902@gmail.com', '12', 29, 'single', 'no', 'no', 'Mumbai'),
('Sohail Shaikh', 'abc', 2147483647, 'sssohail2902@gmail.com', '12', 12, 'single', 'yes', 'yes', 'Mumbai'),
('Sohail Shaikh', 'abcd', 2147483647, 'sssohail2902@gmail.com', '12', 12, 'single', 'yes', 'no', 'Mumbai'),
('Sohail Shaikh', 'XYZ', 2147483647, 'sssohail2902@gmail.com', '12', 45, 'married', 'no', 'yes', 'Mumbai'),
('Sohail Shaikh', 'hello1', 12, 'sssohail2902@gmail.com', '12', 12, 'single', 'yes', 'yes', 'Mumbai');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
