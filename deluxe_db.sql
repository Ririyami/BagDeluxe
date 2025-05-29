-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2025 at 01:09 PM
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
-- Database: `deluxe_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(100) NOT NULL,
  `name` varchar(20) NOT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `password`) VALUES
(1, 'admin', '6216f8a75'),
(3, 'avril', '40bd00156'),
(4, 'Admin101', '01b307acb'),
(5, 'Admin111', '01b307acba4f54f55aafc33bb06bbbf6ca803e9a');

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
(11, 9, 1, 'urger', 56, 3, 'BACKPACKS.jpg'),
(13, 11, 5, 'MINI porosus crocodile', 105000, 1, 'MINI 16 in porosus crocodile - Amazone.jpg'),
(14, 11, 4, 'Celine HANDBAGS', 100000, 1, '16 - WOMEN HANDBAGS.jpg'),
(23, 12, 21, 'Chanel Holiday Collection', 107000, 1, 'Holiday Gift Collection - 583 For Sale at 1stDibs.jpg'),
(24, 13, 5, 'MINI porosus crocodile', 105000, 1, 'MINI 16 in porosus crocodile - Amazone.jpg'),
(25, 13, 6, 'Macadam Canvas Bag', 104000, 1, 'CELINE Macadam Canvas Hand Bag PVC Leather Brown Auth yk13056.jpg');

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
(4, 3, 'Calfskin Leather Bag', 'Female Bags', 'Black', 0, NULL),
(5, 4, 'Celine HANDBAGS', 'Female Bags', 'White', 290, NULL),
(6, 5, 'MINI porosus crocodile', 'Female Bags', 'Green', 297, NULL),
(7, 6, 'Macadam Canvas Bag', 'Female Bags', 'Brown', 298, NULL),
(8, 7, 'Celine Triomphe Bag', 'Female Bags', 'White', 300, NULL),
(9, 8, 'Classique satinated calfskin', 'Female Bags', 'Black', 299, NULL),
(10, 9, 'Celine bag', 'Female Bags', 'Nude', 299, NULL),
(11, 10, 'Celine 2024 bag', 'Female Bags', 'Black', 300, NULL),
(12, 11, 'Medium Quilted Caviar Leather Bag', 'Female Bags', 'Orange', 300, NULL),
(13, 16, 'Flap Bags  Fashion', 'Female Bags', 'White', 299, NULL),
(14, 17, 'Mini Rectangular Flap ', 'Female Bags', 'Black', 300, NULL),
(15, 18, 'Bnib Mini Bowling Bag', 'Female Bags', 'White', 299, NULL),
(16, 20, 'Fashion _ CHANEL', 'Female Bags', 'Green', 300, NULL),
(17, 21, 'Chanel Holiday Collection', 'Female Bags', 'Pink', 299, NULL),
(18, 22, 'Small Chloe C Carry Bag', 'Female Bags', 'Gray', 300, NULL),
(19, 23, 'Chloe Calfskin Crocodile Embossed Mini C Vanity Bag', 'Female Bags', 'Green', 300, NULL),
(20, 24, 'Chloé _ Luxury Fashion', 'Female Bags', 'Blue', 300, NULL),
(21, 26, 'Chloé Fashion Collections', 'Female Bags', 'Orange', 300, NULL),
(22, 27, 'Calfskin Crocodile Embossed Small Bag', 'Female Bags', 'Nude', 300, NULL),
(23, 30, 'Chloe brand new', 'Female Bags', 'Orange', 300, NULL),
(24, 31, 'Chloe Leather Paddington Medium Satchel', 'Female Bags', 'White', 300, NULL),
(25, 34, 'Medium Mily Shoulder Bag', 'Female Bags', 'Pink', 300, NULL),
(26, 36, 'MEDIUM DIOR CARO BAG', 'Female Bags', 'Blue', 299, NULL),
(27, 37, 'DIOR - Fashion and beauty', 'Female Bags', 'Brown', 300, NULL),
(28, 40, 'Medium Lady D-lite Bag Blue Toile De Jouy Embroidery', 'Female Bags', 'Blue', 300, NULL),
(29, 41, 'Dior Caro Large Bag', 'Female Bags', 'White', 300, NULL),
(31, 43, 'Mini Lady Dior Bag Cherry Red Patent Cannage Calfskin', 'Female Bags', 'Red', 300, NULL),
(32, 44, 'CDior Medium Cannage Lady D-Lite', 'Female Bags', 'Gray', 300, NULL),
(33, 46, 'Borsa Media Lady C Dior Pelle Di Pelle Cannage', 'Female Bags', 'White', 300, NULL),
(34, 47, 'Lambskin Cannage Mini Jolie Top Handle Bag', 'Female Bags', 'Pink', 300, NULL),
(35, 48, 'Fendi small bag', 'Female Bags', 'Blue', 300, NULL),
(36, 49, 'Fendi fashion bag', 'Female Bags', 'Orange', 300, NULL),
(37, 50, 'Bag fendi unique fashion', 'Female Bags', 'Pink', 300, NULL),
(38, 51, 'Elegant gray fendi', 'Female Bags', 'Gray', 300, NULL),
(39, 52, 'Quality design bag', 'Female Bags', 'Green', 300, NULL),
(40, 53, 'PEEKABOO ICONIC MINI _ Fendi', 'Female Bags', 'Blue', 300, NULL),
(41, 54, 'Fendi high quality brand bag', 'Female Bags', 'White', 300, NULL),
(42, 55, 'Fendigraphy Mini Leather', 'Female Bags', 'Gray', 300, NULL),
(43, 56, 'Trend-Proof and So Chic', 'Female Bags', 'Yellow', 300, NULL),
(44, 57, 'Gucci Diana small tote bag', 'Female Bags', 'Red', 300, NULL),
(45, 59, 'Chain Strap Bags', 'Female Bags', 'White', 300, NULL),
(46, 60, 'Women - Gucci Bamboo 1947', 'Female Bags', 'Blue', 300, NULL),
(47, 61, 'Gucci Bamboo Diva small top handle bag', 'Female Bags', 'Black', 300, NULL),
(48, 62, 'Ophidia Gg Small Beige Bag', 'Female Bags', 'White', 300, NULL),
(49, 63, 'Leather Ophidia Dome Small Shoulder Bag', 'Female Bags', 'Black', 300, NULL),
(50, 67, 'Gucci  Nylon Handbag ', 'Female Bags', 'Pink', 300, NULL),
(51, 69, 'Hermes Picnic Kelly', 'Female Bags', 'White', 300, NULL),
(52, 71, 'Hermes Perfect Spring Bag ', 'Female Bags', 'Orange', 300, NULL),
(53, 73, 'Vestiaire Collective', 'Female Bags', 'Gray', 300, NULL),
(54, 76, 'Hermes Mauve Sylvestre ', 'Female Bags', 'Pink', 300, NULL),
(55, 77, 'Constance 18 Vert Bosphore', 'Female Bags', 'Green', 300, NULL),
(56, 78, 'Hermes Kelly Retourne', 'Female Bags', 'Brown', 300, NULL),
(57, 79, 'Hermes Kelly Sellier Vert Criquet Epsom ', 'Female Bags', 'Green', 300, NULL),
(58, 80, 'Hermes COUTURE', 'Female Bags', 'Blue', 300, NULL),
(59, 81, 'Aerogram Keepall XS Leather Handbag', 'Female Bags', 'Orange', 300, NULL),
(60, 82, 'LV small bag', 'Female Bags', 'Pink', 300, NULL),
(61, 83, 'Monogram Very Handba', 'Female Bags', 'Red', 300, NULL),
(62, 84, 'LV fashion bag', 'Female Bags', 'Pink', 300, NULL),
(63, 85, 'GO-14 MM 2Way Bag', 'Female Bags', 'Black', 300, NULL),
(64, 86, 'LV unique bag', 'Female Bags', 'Brown', 300, NULL),
(65, 87, 'Montorgueil PM Shoulder Bag', 'Female Bags', 'Brown', 300, NULL),
(66, 89, 'New Wave Chain Bag MM', 'Female Bags', 'Pink', 300, NULL),
(67, 91, 'Luxury fashion Prada', 'Female Bags', 'Pink', 300, NULL),
(68, 92, 'New Designer bag Prada', 'Female Bags', 'Black', 300, NULL),
(69, 93, 'High quality prada ', 'Female Bags', 'Red', 300, NULL),
(70, 94, 'Promenade Vernice Saffiano Blue Leather ', 'Female Bags', 'Blue', 300, NULL),
(71, 95, 'Leather Medium Tote Bag', 'Female Bags', 'Black', 300, NULL),
(72, 96, 'Prada Re-Edition 2000 ', 'Female Bags', 'Yellow', 300, NULL),
(73, 97, 'Prada Mytheresa', 'Female Bags', 'White', 300, NULL),
(74, 98, 'PRADA Soft Calfskin Arque', 'Female Bags', 'Gray', 300, NULL),
(75, 99, 'Fashion Men bag', 'Male Bags', 'Black', 300, NULL),
(76, 100, 'Messenger Small Celine Bag', 'Male Bags', 'Black', 300, NULL),
(77, 102, 'Celine Unique Bag', 'Male Bags', 'Black', 300, NULL),
(78, 103, 'Men Fashion Bag ', 'Male Bags', 'Brown', 300, NULL),
(79, 104, 'Small Bag Celine', 'Male Bags', 'Black', 300, NULL),
(80, 105, 'Men Celine bag 7', 'Male Bags', 'Black', 300, NULL),
(81, 106, 'Designer Bag Celine', 'Male Bags', 'Brown', 300, NULL),
(82, 107, 'Chanel Men&#39;s bag 1', 'Male Bags', 'Black', 300, NULL),
(83, 108, 'Cool Chanel Men&#39;s Bag', 'Male Bags', 'Black', 300, NULL),
(84, 109, 'Big Channel Multi-purpose', 'Male Bags', 'Brown', 300, NULL),
(85, 110, 'Old Fashion ', 'Male Bags', 'Black', 300, NULL),
(86, 111, 'Dior bag 1', 'Male Bags', 'Black', 300, NULL),
(87, 112, 'Dior Monte', 'Male Bags', 'Black', 300, NULL),
(88, 113, 'Dior Small Bag', 'Male Bags', 'Black', 300, NULL),
(89, 114, 'Unique Brown Dior', 'Male Bags', 'Brown', 600, NULL),
(90, 115, 'Dior bag 5', 'Male Bags', 'Brown', 300, NULL),
(91, 116, 'School Bag ', 'Male Bags', 'Brown', 300, NULL),
(92, 117, 'Dior Men Sling Bag', 'Male Bags', 'Black', 300, NULL),
(93, 118, 'Dior Atelier', 'Male Bags', 'Black', 300, NULL),
(94, 119, 'Cool Eyes Bag', 'Male Bags', 'Black', 300, NULL),
(95, 120, 'Smooth Fendi Bag', 'Male Bags', 'Black', 300, NULL),
(96, 121, 'Macho Bag', 'Male Bags', 'Brown', 300, NULL),
(97, 123, 'Gray Leather Bag', 'Male Bags', 'Gray', 300, NULL),
(98, 124, 'Fendi Roma', 'Male Bags', 'Black', 300, NULL),
(99, 125, 'FF Fendi', 'Male Bags', 'Brown', 300, NULL),
(100, 126, 'Cool Camping Bag', 'Male Bags', 'Black', 300, NULL),
(101, 127, 'Fendi Racer', 'Male Bags', 'Black', 300, NULL),
(102, 128, 'Gucci Fashion S Bag', 'Male Bags', 'Black', 300, NULL),
(103, 129, 'Simple Small Bag', 'Male Bags', 'Black', 300, NULL),
(104, 130, 'Gucci bag 3', 'Male Bags', 'Black', 300, NULL),
(105, 131, 'Gucci bag 4', 'Male Bags', 'Black', 300, NULL),
(106, 132, 'Gucci bag 5', 'Male Bags', 'Black', 300, NULL),
(107, 133, 'Gucci messenger bag ', 'Male Bags', 'Black', 300, NULL),
(108, 134, 'Gucci men backpack', 'Male Bags', 'Black', 300, NULL),
(109, 135, 'Small gucci messenger bag', 'Male Bags', 'Black', 300, NULL),
(110, 136, 'Blue messenger bag', 'Male Bags', 'Blue', 300, NULL),
(111, 137, 'Black long bag', 'Male Bags', 'Black', 300, NULL),
(112, 138, 'Black leather bag', 'Male Bags', 'Black', 300, NULL),
(113, 139, 'Fashion Hermes bag', 'Male Bags', 'Black', 300, NULL),
(114, 140, 'Black LV Bag', 'Male Bags', 'Black', 300, NULL),
(115, 141, 'Nice Blue LV Backpack', 'Male Bags', 'Blue', 300, NULL),
(116, 142, 'Big LV Backpack', 'Male Bags', 'Black', 300, NULL),
(117, 143, 'Big Adventure Bag', 'Male Bags', 'Black', 300, NULL),
(118, 144, 'LV Leather Bag', 'Male Bags', 'Black', 300, NULL),
(119, 145, 'Multi-Purpose Bag', 'Male Bags', 'Black', 300, NULL),
(120, 146, 'Gray Men&#39;s Bag ', 'Male Bags', 'Gray', 300, NULL),
(121, 147, 'LV backpack', 'Male Bags', 'Black', 300, NULL),
(122, 148, 'Best Prada Bag', 'Male Bags', 'Black', 300, NULL),
(123, 149, 'Strong Prada Backpack', 'Male Bags', 'Black', 300, NULL),
(124, 150, 'Multi-purpose Black Prada ', 'Male Bags', 'Black', 300, NULL),
(125, 151, 'Prada Messenger Bag', 'Male Bags', 'Black', 300, NULL),
(126, 152, 'Prada Laptop Bag', 'Male Bags', 'Black', 300, NULL),
(127, 153, 'Old Messenger Bag', 'Male Bags', 'Black', 300, NULL),
(128, 154, 'Unique White Prada Bag', 'Male Bags', 'White', 300, NULL),
(129, 155, 'Gray Waterproof Leather Bag', 'Male Bags', 'Gray', 300, NULL),
(130, 156, 'Big SL Bag', 'Female Bags', 'Black', 300, NULL),
(131, 157, 'Fashion SL Bag', 'Female Bags', 'Black', 300, NULL),
(132, 158, 'SL Bag', 'Female Bags', 'Black', 300, NULL),
(133, 159, 'Blue Collection Bag', 'Female Bags', 'Blue', 300, NULL),
(134, 160, 'Big SL Black Bag', 'Male Bags', 'Black', 300, NULL),
(135, 161, 'Small Square Bag', 'Male Bags', 'Black', 300, NULL),
(136, 162, 'Cool Leather SL', 'Male Bags', 'Black', 300, NULL),
(137, 163, 'SL Big Bag', 'Male Bags', 'Black', 300, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `number` varchar(12) NOT NULL,
  `message` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `user_id`, `name`, `email`, `number`, `message`) VALUES
(1, 0, 'saf', 'ncxxec@gmail.com', '09563892678', 'BETTERRRRRR\r\n'),
(2, 0, 'rio', 'hakdog@gmail.com', '09563892678', 'OK!');

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
(18, 12, NULL, 'okii', '0956389267', 'hakdog@gmail.com', 'cash on delivery', '2, 3, Danao, Danao, Danao City, Cebu, Philippines - 0976', 'Celine HANDBAGS  (₱100000 x 1) - , Macadam Canvas Bag (₱104000 x 2) - , Classique satinated calfskin (₱101300 x 1) - , MINI porosus crocodile (₱105000 x 1) - , Bnib Mini Bowling Bag (₱111000 x 1) - , Flap Bags  Fashion (₱110000 x 1) - ', 750006, NULL, '2025-05-20', 'pending'),
(19, 12, NULL, 'okii', '0956389267', 'hakdog@gmail.com', 'cash on delivery', '2, 3, Danao, Danao, Danao City, Cebu, Philippines - 0976', 'MEDIUM DIOR CARO BAG (₱115000 x 1) - , Celine bag  (₱102300 x 1) - ', 221646, NULL, '2025-05-20', 'pending');

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
  `brand` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `price`, `image`, `bag_color`, `stock`, `brand`, `description`) VALUES
(3, 'Calfskin Leather Bag', 'Female Bags', 102000, 'Celine Black Shiny Calfskin Leather Claude Shoulder Bag.jpg', 'Black', 300, 'Celine', 'Best bag'),
(4, 'Celine HANDBAGS ', 'Female Bags', 100000, '16 - WOMEN HANDBAGS.jpg', 'White', 298, 'Celine', 'Best bag'),
(5, 'MINI porosus crocodile', 'Female Bags', 105000, 'MINI 16 in porosus crocodile - Amazone.jpg', 'Green', 297, 'Celine', 'Best bag'),
(6, 'Macadam Canvas Bag', 'Female Bags', 104000, 'CELINE Macadam Canvas Hand Bag PVC Leather Brown Auth yk13056.jpg', 'Brown', 298, 'Celine', 'Best bag'),
(7, 'Celine Triomphe Bag', 'Female Bags', 101000, 'Celine Triomphe Shoulder Bag White at Helenwray.jpg', 'White', 300, 'Celine', 'Best bag'),
(8, 'Classique satinated calfskin', 'Female Bags', 101300, 'Classique 16 Bag IN satinated calfskin.jpg', 'Black', 299, 'Celine', 'Best bag'),
(9, 'Celine bag ', 'Female Bags', 102300, 'OFFICIAL ONLINE STORE UNITED KINGDOM (1).jpg', 'Nude', 299, 'Celine', 'Best bag'),
(10, 'Celine 2024 bag', 'Female Bags', 103000, '新包 _ CELINE 上架2024秋冬新款漆皮手袋：回溯1960年代太空时尚.jpg', 'Black', 300, 'Celine', 'Best bag'),
(11, 'M. Quilted Caviar Lthr Bg', 'Female Bags', 105000, 'CHANEL Medium Coco Quilted Caviar Leather Top Handle Shoulder Bag Red.jpg', 'Orange', 300, 'Chanel', 'Best bag'),
(16, 'Flap Bags  Fashion', 'Female Bags', 110000, 'Flap Bags - Handbags — Fashion _ CHANEL.jpg', 'White', 299, 'Chanel', 'Best bag'),
(17, 'Mini Rectangular Flap ', 'Female Bags', 108000, 'Chanel Mini Rectangular Flap with Top Handle Black and Pink Lambskin Light Gold Hardware.jpg', 'Black', 300, 'Chanel', 'Best quality'),
(18, 'Bnib Mini Bowling Bag', 'Female Bags', 111000, 'Bnib Chanel 25p Mini Bowling Bag With Chain Lambskin Blanc_white.jpg', 'White', 299, 'Chanel', 'Best quality'),
(20, 'Fashion _ CHANEL', 'Female Bags', 108000, 'Handbags & Bags - Fashion _ CHANEL.jpg', 'Green', 300, 'Chanel', 'Best quality'),
(21, 'Chanel Holiday Collection', 'Female Bags', 107000, 'Holiday Gift Collection - 583 For Sale at 1stDibs.jpg', 'Pink', 299, 'Chanel', 'Best quality'),
(22, 'Small Chloe C Carry Bag', 'Female Bags', 105000, 'Small Chloe C Double Carry Bag Motty Grey.jpg', 'Gray', 300, 'Chloé', 'Best quality'),
(23, 'Chloe Calfskin Crocodile Embossed Mini C Vanity Bag', 'Female Bags', 112000, 'Chloe Calfskin Crocodile Embossed Mini C Vanity Bag (SHF-20712).jpg', 'Green', 300, 'Chloé', 'Best quality'),
(24, 'Chloé _ Luxury Fashion', 'Female Bags', 100000, 'Chloé _ US Official Site _ Luxury Fashion.jpg', 'Blue', 300, 'Chloé', 'Best quality'),
(26, 'Chloé Fashion Collections', 'Female Bags', 103000, 'Chloé Fashion Collections For Women _ Moda Operandi.jpg', 'Orange', 300, 'Chloé', 'Best quality'),
(27, 'Calfskin Crocodile Embossed Small Bag', 'Female Bags', 105000, 'CHLOE Calfskin Crocodile Embossed Small C Double Carry Autumnal Brown _ FASHIONPHILE.jpg', 'Nude', 300, 'Chloé', 'Best quality'),
(30, 'Chloe brand new', 'Female Bags', 109000, '11 Bag Brands That Are the Epitome of French Style.jpg', 'Orange', 300, 'Chloé', 'Best quality'),
(31, 'Chloe Leather Paddington Medium Satchel', 'Female Bags', 115000, 'Chloe Leather Paddington Medium Satchel (SHF-23646).jpg', 'White', 300, 'Chloé', 'Best quality'),
(34, 'Medium Mily Shoulder Bag', 'Female Bags', 112000, 'Medium Mily Shoulder Bag.jpg', 'Pink', 300, 'Chloé', 'Best quality'),
(36, 'MEDIUM DIOR CARO BAG', 'Female Bags', 115000, 'MEDIUM DIOR CARO BAG - Blue _ one size.jpg', 'Blue', 299, 'Christian_Dior', 'Best quality'),
(37, 'DIOR - Fashion and beauty', 'Female Bags', 108000, 'DIOR - US Official Online Boutique _ Fashion and beauty  _ DIOR.jpg', 'Brown', 300, 'Christian_Dior', 'Best quality'),
(40, 'Medium Lady D-lite Bag Blue Toile De Jouy Embroidery', 'Female Bags', 160000, 'Medium Lady D-lite Bag Blue Toile De Jouy Embroidery.jpg', 'Blue', 300, 'Christian_Dior', 'Best quality'),
(41, 'Dior Caro Large Bag', 'Female Bags', 133000, 'Dior Caro Large Bag - White _ M.jpg', 'White', 300, 'Christian_Dior', 'Best quality'),
(43, 'Mini Lady Dior Bag Cherry Red Patent Cannage Calfskin', 'Female Bags', 120000, 'Mini Lady Dior Bag Cherry Red Patent Cannage Calfskin _ DIOR.jpg', 'Red', 300, 'Christian_Dior', 'Best quality'),
(44, 'CDior Medium Cannage Lady D-Lite', 'Female Bags', 110000, 'Dior Medium Cannage Lady D-Lite (SHG-xP4ymr).jpg', 'Gray', 300, 'Christian_Dior', 'Best quality'),
(46, 'Borsa Media Lady C Dior Pelle Di Pelle Cannage', 'Female Bags', 118000, 'Borsa Media Lady Dior Pelle Di Pelle Cannage Color Latte.jpg', 'White', 300, 'Christian_Dior', 'Best quality'),
(47, 'Lambskin Cannage Mini Jolie Top Handle Bag', 'Female Bags', 108000, 'CHRISTIAN DIOR Lambskin Cannage Mini Jolie Top Handle Bag Powder Pink.jpg', 'Pink', 300, 'Christian_Dior', 'Best quality'),
(48, 'Fendi small bag', 'Female Bags', 111000, 'FENDI Official USA Online Store (1).jpg', 'Blue', 300, 'Fendi', 'Best quality'),
(49, 'Fendi fashion bag', 'Female Bags', 115000, 'Bags _ Fendi (1).jpg', 'Orange', 300, 'Fendi', 'Best quality'),
(50, 'Bag fendi unique fashion', 'Female Bags', 113000, 'FENDI _ Official Online Store (1).jpg', 'Pink', 300, 'Fendi', 'Best quality'),
(51, 'Elegant gray fendi', 'Female Bags', 119000, 'FENDI _ Official Online Store.jpg', 'Gray', 300, 'Fendi', 'Best quality'),
(52, 'Quality design bag', 'Female Bags', 117000, 'Bags _ Fendi.jpg', 'Green', 300, 'Fendi', 'Best quality'),
(53, 'PEEKABOO ICONIC MINI _ Fendi', 'Female Bags', 116000, 'Light blue ostrich bag - PEEKABOO ICONIC MINI _ Fendi.jpg', 'Blue', 300, 'Fendi', 'Best quality'),
(54, 'Fendi high quality brand bag', 'Female Bags', 119000, 'FENDI Official USA Online Store.jpg', 'White', 300, 'Fendi', 'Best quality'),
(55, 'Fendigraphy Mini Leather', 'Female Bags', 113000, 'Fendigraphy Mini Leather Gray _ Fendi.jpg', 'Gray', 300, 'Fendi', 'Best quality'),
(56, 'Trend-Proof and So Chic', 'Female Bags', 120000, 'Trend-Proof and So Chic—This Is the Designer Bag Every Editor Wants.jpg', 'Yellow', 300, 'Gucci', 'Best quality'),
(57, 'Gucci Diana small tote bag', 'Female Bags', 130000, 'Gucci - Gucci Diana small tote bag.jpg', 'Red', 300, 'Gucci', 'Best quality'),
(59, 'Chain Strap Bags', 'Female Bags', 118000, 'Designer Chain Shoulder Bags _ Chain Strap Bags  _ GUCCI® US.jpg', 'White', 300, 'Gucci', 'Best quality'),
(60, 'Women - Gucci Bamboo 1947', 'Female Bags', 121000, 'Women - Women - Gucci Bamboo 1947 _ GUCCI® US.jpg', 'Blue', 300, 'Gucci', 'Best bag'),
(61, 'Gucci Bamboo Diva small top handle bag', 'Female Bags', 124000, 'Gucci - Gucci Bamboo Diva small top handle bag.jpg', 'Black', 300, 'Gucci', 'Best bag'),
(62, 'Ophidia Gg Small Beige Bag', 'Female Bags', 115000, 'Gucci Ophidia Gg Small Beige Bag Men - Cream _ One size _ Men.jpg', 'White', 300, 'Gucci', 'Best quality'),
(63, 'Leather Ophidia Dome Small Shoulder Bag', 'Female Bags', 116000, 'Gucci Leather Ophidia Dome Small Shoulder Bag.jpg', 'Black', 300, 'Gucci', 'Best quality'),
(67, 'Gucci  Nylon Handbag ', 'Female Bags', 113000, 'Gucci  Nylon Handbag (Pre-Owned) - One Size _ pink.jpg', 'Pink', 300, 'Gucci', 'Best quality'),
(69, 'Hermes Picnic Kelly', 'Female Bags', 118000, 'Hermes Picnic Kelly 35 Nata Swift Palladium Hardware.jpg', 'White', 300, 'Hermès', 'Best quality'),
(71, 'Hermes Perfect Spring Bag ', 'Female Bags', 125000, 'Pick Up the Perfect Spring Bag from Chanel, Hermès and More at Christie’s - PurseBlog.jpg', 'Orange', 300, 'Hermès', 'Best quality'),
(73, 'Vestiaire Collective', 'Female Bags', 128000, 'Second hand store - Vestiaire Collective.jpg', 'Gray', 300, 'Hermès', 'Best quality'),
(76, 'Hermes Mauve Sylvestre ', 'Female Bags', 122000, 'Hermes Birkin 30 Mauve Sylvestre Clemence Palladium Hardware.jpg', 'Pink', 300, 'Hermès', 'Best quality'),
(77, 'Constance 18 Vert Bosphore', 'Female Bags', 111000, 'Hermes Constance 18 Vert Bosphore Evercolor Gold Hardware - Green _ New bag _ Evercolor.jpg', 'Green', 300, 'Hermès', 'Best quality'),
(78, 'Hermes Kelly Retourne', 'Female Bags', 118000, 'Hermes Kelly Retourne 28 Etoupe Togo Gold Hardware - Brown _ New _ Togo.jpg', 'Brown', 300, 'Hermès', 'Best quality'),
(79, 'Hermes Kelly Sellier Vert Criquet Epsom ', 'Female Bags', 122000, 'Hermes Kelly Sellier 25 Vert Criquet Epsom Gold Hardware - Green _ New or Never Worn _ Epsom.jpg', 'Green', 300, 'Hermès', 'Best quality'),
(80, 'Hermes COUTURE', 'Female Bags', 120000, 'HANDMADE COUTURE_ You can make this look too - Blue Hermes Designer Handbag_.jpg', 'Blue', 300, 'Hermès', 'Best quality'),
(81, 'Aerogram Keepall XS Leather Handbag', 'Female Bags', 120000, 'Louis Vuitton Aerogram Keepall XS Leather Handbag M81004 - Calfskin.jpg', 'Orange', 300, 'Louis_Vuitton', 'Best quality'),
(82, 'LV small bag', 'Female Bags', 112000, 'download (2).jpg', 'Pink', 300, 'Louis_Vuitton', 'Best quality'),
(83, 'Monogram Very Handba', 'Female Bags', 139000, 'Louis Vuitton Monogram Very Handbag M42905 - Red _ Patent Leather.jpg', 'Red', 300, 'Louis_Vuitton', 'Best quality'),
(84, 'LV fashion bag', 'Female Bags', 108000, 'download (1).jpg', 'Pink', 300, 'Louis_Vuitton', 'Best quality'),
(85, 'GO-14 MM 2Way Bag', 'Female Bags', 113000, 'Louis Vuitton GO-14 MM 2Way Bag - Black _ Lambskin.jpg', 'Black', 300, 'Louis_Vuitton', 'Best quality'),
(86, 'LV unique bag', 'Female Bags', 105000, 'Products by Louis Vuitton_ Oxford.jpg', 'Brown', 300, 'Louis_Vuitton', 'Best quality'),
(87, 'Montorgueil PM Shoulder Bag', 'Female Bags', 125000, 'Louis Vuitton Montorgueil PM Shoulder Bag Brown - Brown _ Coated_Waterproof canvas.jpg', 'Brown', 300, 'Louis_Vuitton', 'Best quality'),
(89, 'New Wave Chain Bag MM', 'Female Bags', 117000, 'Louis Vuitton New Wave Chain Bag MM Pink Leather - One Size _ pink.jpg', 'Pink', 300, 'Louis_Vuitton', 'Best quality'),
(91, 'Luxury fashion Prada', 'Female Bags', 125000, 'Luxury fashion & independent designers _ SSENSE.jpg', 'Pink', 300, 'Prada', 'Best quality'),
(92, 'New Designer bag Prada', 'Female Bags', 120000, 'New In _ Designer Fashion for Women.jpg', 'Black', 300, 'Prada', 'Best quality'),
(93, 'High quality prada ', 'Female Bags', 123000, 'Pradaaa.jpg', 'Red', 300, 'Prada', 'Best quality'),
(94, 'Promenade Vernice Saffiano Blue Leather ', 'Female Bags', 132000, 'Prada Promenade Vernice Saffiano Blue Leather Triangle Logo Top Handle Tote Bag.jpg', 'Blue', 300, 'Prada', 'Best quality'),
(95, 'Leather Medium Tote Bag', 'Female Bags', 123000, 'Prada Black Leather Medium Tote Bag Women - Black _ One size _ Women.jpg', 'Black', 300, 'Prada', 'Best quality'),
(96, 'Prada Re-Edition 2000 ', 'Female Bags', 128000, 'Prada Re-Edition 2000 Yellow Shoulder Bag in Re-Nylon, Silver hardware C2409-000940CH - Prada.jpg', 'Yellow', 300, 'Prada', 'Best quality'),
(97, 'Prada Mytheresa', 'Female Bags', 124000, 'Mytheresa - The Finest Edit in Luxury.jpg', 'White', 300, 'Prada', 'Best quality'),
(98, 'PRADA Soft Calfskin Arque', 'Female Bags', 119000, 'PRADA Soft Calfskin Arque Shoulder Bag Ardesia.jpg', 'Gray', 300, 'Prada', 'Best quality'),
(99, 'Fashion Men bag', 'Male Bags', 100000, 'c1.jpg', 'black', 300, 'Celine', 'Good bag'),
(100, 'Messenger Small Celine Bag', 'Male Bags', 110000, 'c2.jpg', 'black', 300, 'Celine', 'Good design'),
(102, 'Celine Unique Bag', 'Male Bags', 116000, 'c4.jpg', 'black', 300, 'Celine', 'Good design'),
(103, 'Men Fashion Bag ', 'Male Bags', 104000, 'c5.jpg', 'brown', 300, 'Celine', 'Good bag'),
(104, 'Small Bag Celine', 'Male Bags', 111000, 'c6.jpg', 'black', 300, 'Celine', 'Good product'),
(105, 'Men Celine bag 7', 'Male Bags', 112000, 'c7.jpg', 'black', 300, 'Celine', 'Good designer bag'),
(106, 'Designer Bag Celine', 'Male Bags', 118000, 'c8.jpg', 'brown', 300, 'Celine', 'Designer bag'),
(107, 'Chanel Men&#39;s bag 1', 'Male Bags', 105000, 'chan1.jpg', 'black', 300, 'Chanel', 'Good design'),
(108, 'Cool Chanel Men&#39;s Bag', 'Male Bags', 116000, 'chan2.jpg', 'black', 300, 'Chanel', 'Good Design'),
(109, 'Big Channel Multi-purpose', 'Male Bags', 120000, 'chan3.jpg', 'brown', 300, 'Chanel', 'Good bag'),
(110, 'Old Fashion ', 'Male Bags', 109000, 'chan4.jpg', 'black', 300, 'Chanel', 'Good design'),
(111, 'Dior bag 1', 'Male Bags', 103000, 'dior1.jpg', 'black', 300, 'Christian_Dior', 'Good design'),
(112, 'Dior Monte', 'Male Bags', 105000, 'dior2.jpg', 'black', 300, 'Christian_Dior', 'Good design'),
(113, 'Dior Small Bag', 'Male Bags', 108000, 'dior3.jpg', 'black', 300, 'Christian_Dior', 'Good design'),
(114, 'Unique Brown Dior', 'Male Bags', 113000, 'dior4.jpg', 'brown', 300, 'Christian_Dior', 'Good design'),
(115, 'Dior bag 5', 'Male Bags', 120000, 'dior5.jpg', 'brown', 300, 'Christian_Dior', 'Good design'),
(116, 'School Bag ', 'Male Bags', 117000, 'dior6.jpg', 'brown', 300, 'Christian_Dior', 'Good design'),
(117, 'Dior Men Sling Bag', 'Male Bags', 120000, 'dior7.jpg', 'black', 300, 'Christian_Dior', 'Good design'),
(118, 'Dior Atelier', 'Male Bags', 129000, 'dior8.jpg', 'black', 300, 'Christian_Dior', 'Good design'),
(119, 'Cool Eyes Bag', 'Male Bags', 102000, 'fendi4.jpg', 'black', 300, 'Fendi', 'Good bag'),
(120, 'Smooth Fendi Bag', 'Male Bags', 108000, 'fendi7.jpg', 'black', 300, 'Fendi', 'Good bag'),
(121, 'Macho Bag', 'Male Bags', 111000, 'fendi8.jpg', 'brown', 300, 'Fendi', 'Good design'),
(123, 'Gray Leather Bag', 'Male Bags', 101000, 'fendi2.webp', 'Gray', 300, 'Fendi', 'Good bag'),
(124, 'Fendi Roma', 'Male Bags', 108000, 'fendi3.webp', 'Black', 300, 'Fendi', 'Good bag'),
(125, 'FF Fendi', 'Male Bags', 114000, 'fendi5.webp', 'Brown', 300, 'Fendi', 'Good bag'),
(126, 'Cool Camping Bag', 'Male Bags', 106000, 'fendi6.webp', 'Black', 300, 'Fendi', 'Good bag'),
(127, 'Fendi Racer', 'Male Bags', 104000, 'fendi1.webp', 'Black', 300, 'Fendi', 'Good bag'),
(128, 'Gucci Fashion S Bag', 'Male Bags', 102000, 'gucci1.webp', 'black', 300, 'Gucci', 'Good bag'),
(129, 'Simple Small Bag', 'Male Bags', 105000, 'gucci2.webp', 'black', 300, 'Gucci', 'Good bag'),
(130, 'Gucci bag 3', 'Male Bags', 112000, 'gucci3.webp', 'black', 300, 'Gucci', 'Good bag'),
(131, 'Gucci bag 4', 'Male Bags', 108000, 'gucci4.webp', 'black', 300, 'Gucci', 'Good bag'),
(132, 'Gucci bag 5', 'Male Bags', 107000, 'gucci5.webp', 'black', 300, 'Gucci', 'Good bag'),
(133, 'Gucci messenger bag ', 'Male Bags', 109000, 'gucci6.webp', 'black', 300, 'Gucci', 'Good bag'),
(134, 'Gucci men backpack', 'Male Bags', 112, 'gucci7.webp', 'black', 300, 'Gucci', 'Good bag'),
(135, 'Small gucci messenger bag', 'Male Bags', 100000, 'gucci8 (2).webp', 'black', 300, 'Gucci', 'Good product'),
(136, 'Blue messenger bag', 'Male Bags', 112000, 'herm1.webp', 'Blue', 300, 'Hermès', 'Good bag'),
(137, 'Black long bag', 'Male Bags', 104000, 'herm3.webp', 'black', 300, 'Hermès', 'Good bag'),
(138, 'Black leather bag', 'Male Bags', 105000, 'herm5.jpg', 'black', 300, 'Hermès', 'Good bag'),
(139, 'Fashion Hermes bag', 'Male Bags', 112000, 'hermes4.webp', 'black', 300, 'Hermès', '111000'),
(140, 'Black LV Bag', 'Male Bags', 105000, 'lv1.jpg', 'black', 300, 'Louis_Vuitton', 'Good product'),
(141, 'Nice Blue LV Backpack', 'Male Bags', 123000, 'lv2.jpg', 'Blue', 300, 'Louis_Vuitton', 'Good bag'),
(142, 'Big LV Backpack', 'Male Bags', 120000, 'lv3.jpg', 'black', 300, 'Louis_Vuitton', 'Good bag'),
(143, 'Big Adventure Bag', 'Male Bags', 130000, 'lv4.jpg', 'black', 300, 'Louis_Vuitton', 'Good bag'),
(144, 'LV Leather Bag', 'Male Bags', 128000, 'lv5.jpg', 'black', 300, 'Louis_Vuitton', 'Good bag'),
(145, 'Multi-Purpose Bag', 'Male Bags', 115000, 'lv6.jpg', 'black', 300, 'Louis_Vuitton', 'Good bag'),
(146, 'Gray Men&#39;s Bag ', 'Male Bags', 112000, 'lv7.jpg', 'Gray', 300, 'Louis_Vuitton', 'Good bag'),
(147, 'LV backpack', 'Male Bags', 121000, 'lv8.jpg', 'black', 300, 'Louis_Vuitton', 'Good design'),
(148, 'Best Prada Bag', 'Male Bags', 130000, 'prada1.jpg', 'black', 300, 'Prada', 'Good bag'),
(149, 'Strong Prada Backpack', 'Male Bags', 122000, 'prada2.jpg', 'black', 300, 'Prada', 'Good bag'),
(150, 'Multi-purpose Black Prada ', 'Male Bags', 133000, 'prada3.jpg', 'black', 300, 'Prada', 'Good bag'),
(151, 'Prada Messenger Bag', 'Male Bags', 111000, 'prada4.jpg', 'black', 300, 'Prada', 'Good bag'),
(152, 'Prada Laptop Bag', 'Male Bags', 123000, 'prada5.jpg', 'black', 300, 'Prada', 'Good Bag'),
(153, 'Old Messenger Bag', 'Male Bags', 102000, 'prada6.jpg', 'black', 300, 'Prada', 'Good '),
(154, 'Unique White Prada Bag', 'Male Bags', 118000, 'prada7.jpg', 'white', 300, 'Prada', 'Good Bag'),
(155, 'Gray Waterproof Leather Bag', 'Male Bags', 123000, 'prada8.jpg', 'Gray', 300, 'Prada', 'Good bag'),
(156, 'Big SL Bag', 'Female Bags', 112000, 'st1.jpg', 'black', 300, 'Saint_Laurent', 'Good Bag'),
(157, 'Fashion SL Bag', 'Female Bags', 120000, 'st2.jpg', 'black', 300, 'Saint_Laurent', 'Good bag'),
(158, 'SL Bag', 'Female Bags', 114000, 'st3.jpg', 'black', 300, 'Saint_Laurent', 'Good Bag'),
(159, 'Blue Collection Bag', 'Female Bags', 126000, 'st4.jpg', 'Blue', 300, 'Saint_Laurent', 'Good Bag'),
(160, 'Big SL Black Bag', 'Male Bags', 112000, 'st5.jpg', 'black', 300, 'Saint_Laurent', 'Big and Good'),
(161, 'Small Square Bag', 'Male Bags', 102000, 'st6.jpg', 'black', 300, 'Saint_Laurent', 'Good Bag'),
(162, 'Cool Leather SL', 'Male Bags', 107000, 'st7.jpg', 'black', 300, 'Saint_Laurent', 'Good bag'),
(163, 'SL Big Bag', 'Male Bags', 123000, 'st8.jpg', 'black', 300, 'Saint_Laurent', 'Good bag');

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
(10, 'Princess', NULL, 'oliviaazcuna@gmail.com', '0987654321', '0ec09ef9836da03f1add21e3ef607627e687e790', '3056, N/A, Sambag, Liloan, Cebu, Liloan, Cebu, Cebu, Philippines - 6002', NULL),
(11, 'cess', NULL, 'cess@gmail.com', '0923451873', 'f7c3bc1d808e04732adf679965ccc34ca7ae3441', '', NULL),
(12, 'okii', NULL, 'hakdog@gmail.com', '0956389267', 'f7c3bc1d808e04732adf679965ccc34ca7ae3441', '2, 3, Danao, Danao, Danao City, Cebu, Philippines - 0976', NULL),
(13, 'mika', NULL, 'mika@gmail.com', '0923451873', 'bd5e5eb049f3907175f54f5a571ba6b9fdea36ab', '', NULL);

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
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

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

ALTER TABLE `users` ADD COLUMN `address` TEXT NOT NULL;

ALTER TABLE `inventory` ADD COLUMN `brand_name` VARCHAR (100);

ALTER TABLE `inventory` MODIFY image TEXT;
