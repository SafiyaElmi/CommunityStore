-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 05, 2026 at 11:08 AM
-- Server version: 8.0.46
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `communitystore`
--

-- --------------------------------------------------------

--
-- Table structure for table `bulletin_post`
--

DROP TABLE IF EXISTS `bulletin_post`;
CREATE TABLE IF NOT EXISTS `bulletin_post` (
  `bulletinID` int NOT NULL AUTO_INCREMENT,
  `title` varchar(120) NOT NULL,
  `content` text NOT NULL,
  `postType` enum('Announcement','Event','Service') NOT NULL DEFAULT 'Announcement',
  `postDate` datetime NOT NULL,
  `userID` int NOT NULL,
  PRIMARY KEY (`bulletinID`),
  KEY `fk_bulletin_user` (`userID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bulletin_post`
--

INSERT INTO `bulletin_post` (`bulletinID`, `title`, `content`, `postType`, `postDate`, `userID`) VALUES
(1, 'Snack Pack Discount', 'Snack Packs will be R18 every last Friday of every month.', 'Announcement', '2026-10-05 07:47:47', 2);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `cartID` int NOT NULL AUTO_INCREMENT,
  `userID` int NOT NULL,
  PRIMARY KEY (`cartID`),
  UNIQUE KEY `userID` (`userID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cartID`, `userID`) VALUES
(1, 1),
(3, 2),
(2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `cartitem`
--

DROP TABLE IF EXISTS `cartitem`;
CREATE TABLE IF NOT EXISTS `cartitem` (
  `cartItemID` int NOT NULL AUTO_INCREMENT,
  `quantity` int NOT NULL DEFAULT '1',
  `cartID` int NOT NULL,
  `listingID` int NOT NULL,
  PRIMARY KEY (`cartItemID`),
  UNIQUE KEY `unique_cart_listing` (`cartID`,`listingID`),
  KEY `fk_cartitem_listing` (`listingID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `categoryID` int NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(50) NOT NULL,
  PRIMARY KEY (`categoryID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`categoryID`, `categoryName`) VALUES
(1, 'Electronics'),
(2, 'Books'),
(3, 'Clothing'),
(4, 'Furniture'),
(5, 'Food'),
(6, 'Services'),
(7, 'Accessories'),
(8, 'Other');

-- --------------------------------------------------------

--
-- Table structure for table `listing`
--

DROP TABLE IF EXISTS `listing`;
CREATE TABLE IF NOT EXISTS `listing` (
  `listingID` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `type` enum('New','Used') NOT NULL,
  `imageURL` varchar(255) DEFAULT NULL,
  `location` varchar(120) NOT NULL,
  `datePosted` datetime NOT NULL,
  `status` enum('Available','Reserved','Sold') NOT NULL DEFAULT 'Available',
  `userID` int NOT NULL,
  `categoryID` int NOT NULL,
  `salePrice` decimal(10,2) DEFAULT NULL,
  `saleStart` date DEFAULT NULL,
  `saleEnd` date DEFAULT NULL,
  `saleType` varchar(20) DEFAULT NULL,
  `recurringDay` varchar(20) DEFAULT NULL,
  `recurringWeek` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`listingID`),
  KEY `fk_listing_user` (`userID`),
  KEY `fk_listing_category` (`categoryID`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `listing`
--

INSERT INTO `listing` (`listingID`, `title`, `description`, `price`, `type`, `imageURL`, `location`, `datePosted`, `status`, `userID`, `categoryID`, `salePrice`, `saleStart`, `saleEnd`, `saleType`, `recurringDay`, `recurringWeek`) VALUES
(1, 'Wireless Headphones', 'Bluetooth headphones with good sound quality and a long-lasting battery.', 350.00, 'Used', '../uploads/listing_6ac337270b73a4.39777861.jpg', 'Observatory', '2026-10-05 05:35:35', 'Available', 1, 7, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Phone Case', 'Clear protective phone case suitable for most standard smartphones.', 120.00, 'New', '../uploads/listing_6ac33a9c105f37.84003996.jpg', 'Observatory', '2026-10-05 05:50:20', 'Available', 1, 7, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Smart Watch', 'Smart watch with fitness tracking, notifications and sleep monitoring.', 450.00, 'Used', '../uploads/listing_6ac33b0a41ab29.64644668.jpg', 'Observatory', '2026-10-05 05:52:10', 'Available', 1, 7, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'Silver Bracelet', 'Simple silver-coloured bracelet suitable for everyday wear.', 180.00, 'New', '../uploads/listing_6ac33b85ee5f14.74738701.jpg', 'Observatory', '2026-10-05 05:54:13', 'Reserved', 1, 7, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'Canvas Backpack', 'Spacious canvas backpack with multiple compartments for books and a laptop.', 300.00, 'New', '../uploads/listing_6ac33bc5c83237.43843083.jpg', 'Observatory', '2026-10-05 05:55:17', 'Available', 1, 7, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'Introduction to Programming Textbook', 'Useful programming textbook for students studying introductory computer programming.', 250.00, 'Used', '../uploads/listing_6ac33cf53ed393.56538272.jpg', 'District six', '2026-10-05 06:00:21', 'Available', 1, 2, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'Business Management Textbook', 'Business management textbook with useful notes and examples for students.', 200.00, 'Used', '../uploads/listing_6ac33d816ea539.98318184.jpg', 'District six', '2026-10-05 06:02:41', 'Available', 1, 2, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'Mathematics Textbook', 'Mathematics textbook covering common university-level topics and exercises.', 180.00, 'Used', '../uploads/listing_6ac33e1b876ad3.45945514.jpg', 'District six', '2026-10-05 06:05:15', 'Available', 1, 2, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'English Literature Book', 'Literature textbook with useful readings and analysis.', 150.00, 'Used', '../uploads/listing_6ac33eac52ee27.73832170.jpg', 'District six', '2026-10-05 06:07:40', 'Available', 1, 2, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'Computer Science Study Guide', 'Study guide covering important computer science concepts and revision material.', 220.00, 'Used', '../uploads/listing_6ac33f673489d8.68858447.jpg', 'District six', '2026-10-05 06:10:47', 'Available', 1, 2, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 'Homemade Cookies', 'Fresh homemade cookies, perfect as a quick snack between lectures.', 5.00, 'New', '../uploads/listing_6ac340e3e95c97.59727557.jpg', 'Bellville', '2026-10-05 06:17:07', 'Available', 2, 5, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 'Chocolate Brownies', 'Homemade chocolate brownies sold in a convenient student-sized pack.', 5.00, 'New', '../uploads/listing_6ac34152907486.79989274.jpg', 'Bellville', '2026-10-05 06:18:58', 'Available', 2, 5, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 'Cupcakes', 'Freshly baked cupcakes with assorted toppings.', 5.00, 'New', '../uploads/listing_6ac341ae7dce30.13554625.jpg', 'Bellville', '2026-10-05 06:20:30', 'Available', 2, 5, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 'Homemade Sandwich', 'Freshly prepared sandwich with a choice of simple fillings.', 8.00, 'New', '../uploads/listing_6ac3420ddf1263.05176892.jpg', 'Bellville', '2026-10-05 06:22:05', 'Available', 2, 5, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 'Snack Pack', 'Affordable snack pack containing a selection of popular treats.', 25.00, 'New', '../uploads/listing_6ac342da269810.53301822.jpg', 'Bellville', '2026-10-05 06:25:30', 'Available', 2, 5, 18.00, NULL, NULL, 'Recurring', 'Friday', 'Last'),
(16, 'Laptop', 'HP 15.6 Ryzen 5 8GB/256GB suitable for assignments, browsing, documents and university work.', 4500.00, 'Used', '../uploads/listing_6ac34557278c52.32562202.jpg', 'Salt River', '2026-10-05 06:36:07', 'Available', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'Bluetooth Speaker', 'Portable Bluetooth speaker with good battery life and clear sound.', 300.00, 'New', '../uploads/listing_6ac3468646c2e2.80762697.jpg', 'Salt River', '2026-10-05 06:41:10', 'Available', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'USB Flash Drive 64GB', '64GB USB flash drive useful for storing assignments and study files.', 120.00, 'New', '../uploads/listing_6ac346e192e209.73324750.jpg', 'Salt River', '2026-10-05 06:42:41', 'Available', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 'Wireless Mouse', 'Wireless computer mouse with comfortable design.', 180.00, 'New', '../uploads/listing_6ac34749753396.89285454.jpg', 'Salt River', '2026-10-05 06:44:25', 'Available', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 'Phone Charger', 'Fast-charging phone charger in excellent condition.', 100.00, 'New', '../uploads/listing_6ac347adaf2131.36241451.jpg', 'Salt River', '2026-10-05 06:46:05', 'Available', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 'Black Hoodie', 'Comfortable black hoodie perfect for cool mornings and evenings.', 100.00, 'New', '../uploads/listing_6ac34940269de9.31596682.jpg', 'Mowbray', '2026-10-05 06:52:48', 'Available', 1, 3, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 'Blue Denim Jacket', 'Classic blue denim jacket suitable for everyday casual wear.', 200.00, 'New', '../uploads/listing_6ac3498d8326f9.92506435.jpg', 'Mowbray', '2026-10-05 06:54:05', 'Available', 1, 3, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 'Graphic T-Shirt', 'Casual graphic T-shirt suitable for everyday wear.', 150.00, 'New', '../uploads/listing_6ac349faef8d10.24247310.jpg', 'Mowbray', '2026-10-05 06:55:54', 'Available', 1, 3, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 'Black Jeans', 'Comfortable black jeans with a simple regular fit.', 150.00, 'New', '../uploads/listing_6ac34a48eeef84.54054310.jpg', 'Mowbray', '2026-10-05 06:57:12', 'Available', 1, 3, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 'Puffer Jacket', 'Warm puffer jacket suitable for cold winter days and evenings.', 250.00, 'New', '../uploads/listing_6ac34ac3c15982.50063162.jpg', 'Mowbray', '2026-10-05 06:59:15', 'Available', 1, 3, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 'Study Desk', 'Compact study desk with enough space for a laptop, books and stationery.', 300.00, 'Used', '../uploads/listing_6ac34b5e4539b4.74315999.jpg', 'Woodstock', '2026-10-05 07:01:50', 'Available', 1, 4, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 'Office Chair', 'Comfortable office chair suitable for studying at home.', 250.00, 'Used', '../uploads/listing_6ac34bb7be9aa0.84716269.jpg', 'Woodstock', '2026-10-05 07:03:19', 'Available', 1, 4, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 'Bedside Table', 'Small bedside table with storage space, ideal for student accommodation.', 200.00, 'Used', '../uploads/listing_6ac34c76772709.45915782.jpg', 'Woodstock', '2026-10-05 07:06:30', 'Available', 1, 4, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 'Bookshelf', 'Wooden bookshelf with several shelves for books and study materials.', 300.00, 'Used', '../uploads/listing_6ac34ce20922c2.51112886.jpg', 'Woodstock', '2026-10-05 07:08:18', 'Available', 1, 4, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 'Desk Lamp', 'Compact desk lamp providing good lighting for studying at night.', 90.00, 'Used', '../uploads/listing_6ac34d44ce1f48.99491191.jpg', 'Woodstock', '2026-10-05 07:09:56', 'Available', 1, 4, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 'Board Game Set', 'Complete board game set, great for relaxing with friends.', 300.00, 'New', '../uploads/listing_6ac34ddd583dd5.06497894.jpg', 'Salt River', '2026-10-05 07:12:29', 'Reserved', 1, 8, NULL, NULL, NULL, NULL, NULL, NULL),
(32, 'Football', 'Standard football suitable for recreational games and training.', 150.00, 'New', '../uploads/listing_6ac34e3bd03b63.36424027.jpg', 'Salt River', '2026-10-05 07:14:03', 'Available', 1, 8, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 'CV Design', 'CV formatting and design service for students looking for internships or part-time work.', 200.00, 'Used', '../uploads/listing_6ac34ee5f34a20.32250643.jpg', 'District six', '2026-10-05 07:16:53', 'Available', 1, 6, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 'Website Design', 'Basic website design service for students and small businesses.', 500.00, 'Used', '../uploads/listing_6ac34fcc456a98.48612513.jpg', 'District six', '2026-10-05 07:20:44', 'Available', 1, 6, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 'Mathematics Tutoring', 'One-on-one mathematics tutoring for school and first-year university students.', 150.00, 'Used', '../uploads/listing_6ac350564cc697.06313773.jpg', 'Bellville', '2026-10-05 07:23:02', 'Available', 2, 6, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 'Photography Session', 'Affordable photography sessions for student events, portraits and special occasions.', 350.00, 'Used', '../uploads/listing_6ac3536343c2c0.46914021.jpg', 'Woodstock', '2026-10-05 07:36:03', 'Available', 3, 6, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

DROP TABLE IF EXISTS `notification`;
CREATE TABLE IF NOT EXISTS `notification` (
  `notificationID` int NOT NULL AUTO_INCREMENT,
  `message` text NOT NULL,
  `notificationDate` datetime NOT NULL,
  `userID` int NOT NULL,
  PRIMARY KEY (`notificationID`),
  KEY `fk_notification_user` (`userID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`notificationID`, `message`, `notificationDate`, `userID`) VALUES
(1, 'Your order #1 was placed and is pending payment.', '2026-10-05 07:29:40', 3),
(2, 'A new order #1 was placed for one of your listings.', '2026-10-05 07:29:40', 1),
(3, 'Your order #2 was placed and is pending payment.', '2026-10-05 07:37:56', 2),
(4, 'A new order #2 was placed for one of your listings.', '2026-10-05 07:37:56', 1);

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
CREATE TABLE IF NOT EXISTS `order` (
  `orderID` int NOT NULL AUTO_INCREMENT,
  `orderDate` datetime NOT NULL,
  `status` enum('Pending','Paid','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `userID` int NOT NULL,
  PRIMARY KEY (`orderID`),
  KEY `fk_order_user` (`userID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`orderID`, `orderDate`, `status`, `userID`) VALUES
(1, '2026-10-05 07:29:40', 'Pending', 3),
(2, '2026-10-05 07:37:56', 'Pending', 2);

-- --------------------------------------------------------

--
-- Table structure for table `orderitem`
--

DROP TABLE IF EXISTS `orderitem`;
CREATE TABLE IF NOT EXISTS `orderitem` (
  `orderItemID` int NOT NULL AUTO_INCREMENT,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `orderID` int NOT NULL,
  `listingID` int NOT NULL,
  PRIMARY KEY (`orderItemID`),
  KEY `fk_orderitem_order` (`orderID`),
  KEY `fk_orderitem_listing` (`listingID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orderitem`
--

INSERT INTO `orderitem` (`orderItemID`, `quantity`, `price`, `orderID`, `listingID`) VALUES
(1, 1, 180.00, 1, 4),
(2, 1, 300.00, 2, 31);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
CREATE TABLE IF NOT EXISTS `payment` (
  `paymentID` int NOT NULL AUTO_INCREMENT,
  `amount` decimal(10,2) NOT NULL,
  `paymentMethod` enum('Card','EFT','PayFast','SnapScan','Cash') NOT NULL,
  `paymentStatus` enum('Pending','Paid','Failed') NOT NULL DEFAULT 'Pending',
  `paymentDate` datetime NOT NULL,
  `orderID` int NOT NULL,
  PRIMARY KEY (`paymentID`),
  UNIQUE KEY `orderID` (`orderID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`paymentID`, `amount`, `paymentMethod`, `paymentStatus`, `paymentDate`, `orderID`) VALUES
(1, 180.00, 'Card', 'Pending', '2026-10-05 07:29:40', 1),
(2, 300.00, 'Card', 'Pending', '2026-10-05 07:37:56', 2);

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
CREATE TABLE IF NOT EXISTS `review` (
  `reviewID` int NOT NULL AUTO_INCREMENT,
  `rating` tinyint NOT NULL,
  `comment` text,
  `reviewDate` datetime NOT NULL,
  `userID` int NOT NULL,
  `listingID` int NOT NULL,
  PRIMARY KEY (`reviewID`),
  KEY `fk_review_user` (`userID`),
  KEY `fk_review_listing` (`listingID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `userID` int NOT NULL AUTO_INCREMENT,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','User') NOT NULL DEFAULT 'User',
  `userType` enum('Student','Vendor','Resident') NOT NULL DEFAULT 'Student',
  PRIMARY KEY (`userID`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userID`, `firstName`, `lastName`, `email`, `password`, `role`, `userType`) VALUES
(1, 'Jerry', 'Jack', '223344556@mycput.ac.za', '$2y$10$VsWM4hfW/eA/McGl6d/t2ODSycCask7V5PrJnbwB5/l8NNFQyaTWi', 'User', 'Vendor'),
(2, 'Jade', 'Gray', '234567890@mycput.ac.za', '$2y$10$nFMFjRr7Dmya3Km45NtdWepPtcy4NbMJn.hbyus4r7hm5SCPyTuIO', 'User', 'Student'),
(3, 'Andre', 'Louw', 'andrelouw5@gmail.com', '$2y$10$aO2U0VpOkInV.5Mln1fieeuCluBhb8W3TPyOBhqMB8lz96Pa4JSPu', 'User', 'Resident');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bulletin_post`
--
ALTER TABLE `bulletin_post`
  ADD CONSTRAINT `fk_bulletin_user` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `cartitem`
--
ALTER TABLE `cartitem`
  ADD CONSTRAINT `fk_cartitem_cart` FOREIGN KEY (`cartID`) REFERENCES `cart` (`cartID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cartitem_listing` FOREIGN KEY (`listingID`) REFERENCES `listing` (`listingID`) ON DELETE CASCADE;

--
-- Constraints for table `listing`
--
ALTER TABLE `listing`
  ADD CONSTRAINT `fk_listing_category` FOREIGN KEY (`categoryID`) REFERENCES `category` (`categoryID`),
  ADD CONSTRAINT `fk_listing_user` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `fk_notification_user` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `orderitem`
--
ALTER TABLE `orderitem`
  ADD CONSTRAINT `fk_orderitem_listing` FOREIGN KEY (`listingID`) REFERENCES `listing` (`listingID`),
  ADD CONSTRAINT `fk_orderitem_order` FOREIGN KEY (`orderID`) REFERENCES `order` (`orderID`) ON DELETE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `fk_payment_order` FOREIGN KEY (`orderID`) REFERENCES `order` (`orderID`) ON DELETE CASCADE;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `fk_review_listing` FOREIGN KEY (`listingID`) REFERENCES `listing` (`listingID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_review_user` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
