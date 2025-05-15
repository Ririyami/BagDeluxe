-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 15, 2025 at 04:47 PM
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
-- Database: `bag_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `password`) VALUES
(1, 'admin', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2'),
(3, 'avril', '40bd001563085fc35165329ea1ff5c5ecbdbbeef');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `pid` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(10) NOT NULL,
  `quantity` int(10) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `pid`, `name`, `price`, `quantity`, `image`) VALUES
(4, 1, 1, 'urger', 56, 2, 'BACKPACKS.jpg'),
(11, 9, 1, 'urger', 56, 3, 'BACKPACKS.jpg');

--
-- Triggers `cart`
--
DELIMITER $$
CREATE TRIGGER `prevent_negative_stock` BEFORE INSERT ON `cart` FOR EACH ROW BEGIN
    IF (SELECT stock_quantity FROM inventory WHERE product_id = NEW.pid) < NEW.quantity THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Not enough stock available';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `reduce_inventory_after_add_to_cart` AFTER INSERT ON `cart` FOR EACH ROW BEGIN
    UPDATE inventory 
    SET stock_quantity = stock_quantity - NEW.quantity
    WHERE product_id = NEW.pid;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `restore_inventory_after_remove_from_cart` AFTER DELETE ON `cart` FOR EACH ROW BEGIN
    UPDATE inventory 
    SET stock_quantity = stock_quantity + OLD.quantity
    WHERE product_id = OLD.pid;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `completed_deliveries`
--

CREATE TABLE `completed_deliveries` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deliveries`
--

CREATE TABLE `deliveries` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `driver_name` varchar(255) NOT NULL,
  `driver_contact` varchar(15) NOT NULL,
  `delivery_status` enum('pending','in_progress','completed') DEFAULT 'pending',
  `delivery_date` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `number` varchar(255) NOT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `drivers_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`id`, `name`, `number`, `gender`, `address`, `password`, `drivers_picture`) VALUES
(1, 'admin', '232323', NULL, NULL, '$2y$10$HbvWKfzYE.RN8H94FrO9POD7W46Wz9CuMp4yWxu0EBIldYPSLLUfe', NULL),
(3, 'Kathleen', '0987654321', NULL, NULL, '$2y$10$tG9P.O/WMR7D71lpTI1LxeDNhxxJWuB4eHqYMiaz0X/msc3ppDTJq', NULL),
(4, 'avril', '098765431', NULL, NULL, '$2y$10$l0AVdpuq9EOWtmvW9rqMkuGpirrr8L8.eKqLaCxXjPjMoUR2z411.', NULL),
(5, 'avril', '09876543211', 'Female', 'Sambag San-Vicente Liloan, Cebu', '$2y$10$Z0pYQ.nuPn0QBnmz3ZSf/OQIrO2SQffNIMNI3s69SKga6uYguLaLC', 'about me.png');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `variant_color` varchar(100) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `product_id`, `product_name`, `category`, `variant_color`, `stock_quantity`, `admin_id`) VALUES
(1, 1, 'urger', 'Male Bags', 'pink', 4, NULL),
(2, 2, 'bag', 'Male Bags', 'pink', 6, NULL),
(4, 3, 'rhinnon', 'Male Bags', 'blue', 10, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `name` varchar(20) NOT NULL,
  `number` varchar(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `method` varchar(50) NOT NULL,
  `address` varchar(500) NOT NULL,
  `total_products` varchar(1000) NOT NULL,
  `total_price` int(100) NOT NULL,
  `qrcode` varchar(255) DEFAULT NULL,
  `placed_on` date NOT NULL DEFAULT current_timestamp(),
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `driver_id`, `name`, `number`, `email`, `method`, `address`, `total_products`, `total_price`, `qrcode`, `placed_on`, `payment_status`) VALUES
(1, 1, 4, 'dad', '09554687', 'op@gmail.com', 'cash on delivery', 'hhhjh, uyth, ghfyutg, fguih, gfch, yht, hygtg - 7009', 'urger (56 x 1) - ', 56, '', '2025-03-27', ''),
(2, 1, 5, 'dad', '09554687', 'op@gmail.com', 'cash on delivery', 'hhhjh, uyth, ghfyutg, fguih, gfch, yht, hygtg - 7009', 'urger (₱56 x 2) - ', 123, '', '2025-03-28', 'completed'),
(3, 1, 5, 'dad', '09554687', 'op@gmail.com', 'cash on delivery', 'hhhjh, uyth, ghfyutg, fguih, gfch, yht, hygtg - 7009', 'urger (₱56 x 2) - ', 123, '', '2025-03-28', 'completed'),
(4, 1, NULL, 'dad', '09554687', 'op@gmail.com', 'cash on delivery', 'hhhjh, uyth, ghfyutg, fguih, gfch, yht, hygtg - 7009', 'urger (₱56 x 2) - ', 123, '', '2025-03-28', 'pending'),
(5, 1, NULL, 'dad', '09554687', 'op@gmail.com', 'cash on delivery', 'hhhjh, uyth, ghfyutg, fguih, gfch, yht, hygtg - 7009', 'urger (₱56 x 2) - ', 123, '', '2025-03-28', 'pending'),
(6, 1, NULL, 'dad', '09554687', 'op@gmail.com', 'cash on delivery', 'hhhjh, uyth, ghfyutg, fguih, gfch, yht, hygtg - 7009', 'urger (₱56 x 2) - ', 123, '', '2025-03-28', 'pending'),
(7, 2, NULL, 'admin', '232323', 'lol@gmail.com', 'cash on delivery', 'adad, adad, adadad, adad, adada, dadad, adadad - 4444', 'urger (₱56 x 2) - ', 123, '', '2025-03-28', 'completed'),
(8, 2, 5, 'admin', '232323', 'lol@gmail.com', 'cash on delivery', 'adad, adad, adadad, adad, adada, dadad, adadad - 4444', 'urger (₱56 x 2) - , bag (₱34 x 3) - ', 235, '', '2025-03-29', 'pending'),
(9, 2, NULL, 'admin', '232323', 'lol@gmail.com', 'cash on delivery', 'adad, adad, adadad, adad, adada, dadad, adadad - 4444', 'urger (₱56 x 2) - , bag (₱34 x 3) - ', 235, '', '2025-03-29', 'pending'),
(10, 2, NULL, 'admin', '232323', 'lol@gmail.com', 'cash on delivery', 'adad, adad, adadad, adad, adada, dadad, adadad - 4444', 'urger (₱56 x 2) - , bag (₱34 x 3) - ', 235, '', '2025-03-29', 'pending'),
(11, 2, NULL, 'admin', '232323', 'lol@gmail.com', 'cash on delivery', 'adad, adad, adadad, adad, adada, dadad, adadad - 4444', 'urger (₱56 x 2) - , bag (₱34 x 3) - ', 235, '', '2025-03-29', 'pending'),
(12, 2, NULL, 'admin', '232323', 'lol@gmail.com', 'cash on delivery', 'adad, adad, adadad, adad, adada, dadad, adadad - 4444', 'urger (₱56 x 2) - , bag (₱34 x 3) - ', 235, '', '2025-03-29', 'pending'),
(13, 2, NULL, 'admin', '232323', 'lol@gmail.com', 'cash on delivery', 'adad, adad, adadad, adad, adada, dadad, adadad - 4444', 'urger (₱56 x 3) - ', 185, '', '2025-03-29', 'pending'),
(14, 2, 4, 'admin', '232323', 'lol@gmail.com', 'cash on delivery', 'adad, adad, adadad, adad, adada, dadad, adadad - 4444', 'urger (₱56 x 3) - ', 185, '', '2025-04-04', 'pending'),
(15, 8, 5, 'av', '445', 'b@gmail.com', 'cash on delivery', 'dada, dadad, adadada, adada, adada, adada, adada - 2222', 'bag (₱34 x 4) - ', 150, '', '2025-04-11', 'completed'),
(16, 8, 5, 'av', '445', 'b@gmail.com', 'cash on delivery', 'dada, dadad, adadada, adada, adada, adada, adada - 2222', 'urger (₱56 x 7) - ', 431, '', '2025-04-11', 'completed'),
(17, 10, 5, 'Princess', '0987654321', 'oliviaazcuna@gmail.com', 'cash on delivery', '3056, N/A, Sambag, Liloan, Cebu, Liloan, Cebu, Cebu, Philippines - 6002', 'urger (₱56 x 3) - ', 185, '', '2025-05-03', 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) GENERATED ALWAYS AS (`quantity` * `price`) STORED,
  `cart_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `price` int(10) NOT NULL,
  `image` varchar(100) NOT NULL,
  `bag_color` varchar(100) DEFAULT NULL,
  `stock` int(11) NOT NULL,
  `brand` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `price`, `image`, `bag_color`, `stock`, `brand`) VALUES
(1, 'urger', 'Male Bags', 56, 'BACKPACKS.jpg', NULL, 4, ''),
(2, 'bag', 'Male Bags', 34, 'bags marc by marc jacobs.jpg', 'pink', 15, ''),
(3, 'rhinnon', 'Male Bags', 23, 'BACKPACKS.jpg', 'blue', 20, '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `number` varchar(10) NOT NULL,
  `password` varchar(50) NOT NULL,
  `address` varchar(500) NOT NULL,
  `admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `profile_picture`, `email`, `number`, `password`, `address`, `admin_id`) VALUES
(1, 'dad', NULL, 'op@gmail.com', '09554687', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'hhhjh, uyth, ghfyutg, fguih, gfch, yht, hygtg - 7009', NULL),
(2, 'admin', NULL, 'lol@gmail.com', '232323', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'adad, adad, adadad, adad, adada, dadad, adadad - 4444', NULL),
(3, 'admin', NULL, 'ad@gmail.com', '091234567', '$2y$10$eIC/vCjTg8bXyXs20PbmP.d631hdGYfbB7tEFG24CTR', '', NULL),
(4, 'avril', 'default-profile.png', 'avi@gmail.com', '', '$2y$10$D6.STRYFY4uC3yrymq2VfOU0.92.Xnk/yRC1h8nPWtY', '', NULL),
(5, 'avril', 'C:\\xampp\\htdocs\\BagDeluxe(Latest)\\BagDeluxe/uploaded_img/kath.png', 'avi@gmail.com', '', '$2y$10$VXQHyU7Kz34qOiaBt6cMpeX/jlM3osGbxlAZJLFR40/', '', NULL),
(6, 'avril', 'C:\\xampp\\htdocs\\BagDeluxe(Latest)\\BagDeluxe/uploaded_img/home-img-2.png', 'avi@gmail.com', '', '$2y$10$wq/T3pe5c.S5yjsgyMC99uYMwUFBt0TPl9TrUcpuGvi', '', NULL),
(7, 'avril', NULL, 'av@gmail.com', '09124567', '$2y$10$Vdenh.80QAQrCHpkCSjE3.7kU.fdWe/C5T.mlyXxu6A', '', NULL),
(8, 'av', NULL, 'b@gmail.com', '445', '$2y$10$3yVhwmwKrfXUw.29ggj/dOfloBDCrOnyGMAV1dIItXs', 'dada, dadad, adadada, adada, adada, adada, adada - 2222', NULL),
(9, 'Riri', NULL, 'avrilainahazcuna@gmail.com', '0987654321', '17ba0791499db908433b80f37c5fbc89b870084b', '', NULL),
(10, 'Princess', NULL, 'oliviaazcuna@gmail.com', '0987654321', '0ec09ef9836da03f1add21e3ef607627e687e790', '3056, N/A, Sambag, Liloan, Cebu, Liloan, Cebu, Cebu, Philippines - 6002', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_app`
--

CREATE TABLE `user_app` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `last_login` datetime DEFAULT current_timestamp(),
  `status` enum('online','offline') DEFAULT 'offline'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_app`
--

INSERT INTO `user_app` (`id`, `user_id`, `driver_id`, `last_login`, `status`) VALUES
(1, 7, NULL, '2025-04-11 19:01:17', ''),
(2, 3, NULL, '2025-04-11 19:02:10', ''),
(3, 8, NULL, '2025-04-11 19:05:31', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `completed_deliveries`
--
ALTER TABLE `completed_deliveries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_completed_deliveries` (`order_id`);

--
-- Indexes for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `number` (`number`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_inventory_admin` (`admin_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_order_items_cart` (`cart_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_users_admin` (`admin_id`);

--
-- Indexes for table `user_app`
--
ALTER TABLE `user_app`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `completed_deliveries`
--
ALTER TABLE `completed_deliveries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deliveries`
--
ALTER TABLE `deliveries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_app`
--
ALTER TABLE `user_app`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `completed_deliveries`
--
ALTER TABLE `completed_deliveries`
  ADD CONSTRAINT `fk_completed_deliveries` FOREIGN KEY (`order_id`) REFERENCES `deliveries` (`order_id`);

--
-- Constraints for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD CONSTRAINT `deliveries_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `fk_inventory_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`),
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_cart` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`id`),
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`);

--
-- Constraints for table `user_app`
--
ALTER TABLE `user_app`
  ADD CONSTRAINT `user_app_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_app_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
