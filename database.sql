-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 20, 2024 at 08:30 PM
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
-- Database: `database`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `booking_status` enum('confirmed','pending','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `city_id` int(11) NOT NULL,
  `city_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`city_id`, `city_name`) VALUES
(10, 'Beliatta'),
(11, 'matara'),
(1001, 'Tangalle'),
(1002, 'Anuradhapuraya'),
(1003, 'Dambulla'),
(1004, 'Galle'),
(1005, 'Badulla'),
(1006, 'Mihintalaya'),
(1007, 'Polonnaruwa'),
(1009, 'Kandy'),
(1010, 'Colombo'),
(1011, 'Mount Lavinia'),
(1012, 'Kesbewa'),
(1013, 'Maharagama'),
(1014, 'Moratuwa'),
(1015, 'Ratnapura'),
(1016, 'Negombo'),
(1017, 'Sri Jayewardenepura Kotte'),
(1018, 'Mawanella'),
(1019, 'Kotmale'),
(1020, 'Kalmunai'),
(1021, 'Kilinochchi'),
(1022, 'Hikkaduwa'),
(1023, 'Trincomalee'),
(1024, 'Jaffna'),
(1025, 'Kalpitiya'),
(1026, 'Batticaloa'),
(1027, 'Athurugiriya'),
(1028, 'Tissamaharama'),
(1029, 'Mawatagama'),
(1030, 'Weligama'),
(1031, 'Tangalla'),
(1032, 'Kolonnawa'),
(1033, 'Akurana'),
(1034, 'Galgamuwa'),
(1035, 'Anuradhapura'),
(1036, 'Gampaha'),
(1037, 'Narammala'),
(1038, 'Dikwella South'),
(1039, 'Kalawana'),
(1040, 'Nikaweratiya'),
(1041, 'Puttalam'),
(1042, 'Bakamune'),
(1043, 'Sevanagala'),
(1044, 'Matale'),
(1045, 'Vavuniya'),
(1046, 'Gampola'),
(1047, 'Mullaittivu'),
(1048, 'Kalutara'),
(1049, 'Bentota'),
(1050, 'Mannar'),
(1051, 'Bandarawela'),
(1052, 'Point Pedro'),
(1053, 'Pothuhera'),
(1054, 'Kurunegala'),
(1055, 'Mabole'),
(1056, 'Hakmana'),
(1057, 'Nuwara Eliya'),
(1058, 'Galhinna'),
(1059, 'Kegalle'),
(1060, 'Hatton'),
(1061, 'Gandara West'),
(1062, 'Hambantota'),
(1063, 'Abasingammedda'),
(1064, 'Monaragala');

-- --------------------------------------------------------

--
-- Table structure for table `destinations`
--

CREATE TABLE `destinations` (
  `destination_id` int(11) NOT NULL,
  `desti_name` varchar(255) NOT NULL,
  `desti_description` text NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `destinations`
--

INSERT INTO `destinations` (`destination_id`, `desti_name`, `desti_description`, `image_url`, `city`) VALUES
(3, 'Ruwanweli Seya', 'Ruwanweli Maha Seya (Ruwanweliseya) is one of the most venerated Buddhist sites in Sri Lanka built by the great king Dutugamunu who reigned from 137 BCE to 119 BCE from Anuradhapura. Ruwanweli Maha Seya is not the largest nor the oldest of the stupas erected in Anuradhapura, but this is the most venerated by the Buddhists surpassing all other great stupas. It has the most imposing collection of relics of Gautama Buddha than was ever enshrined in any other dagoba on the island.\r\n\r\nere3e ', './uploads/4092564-about-mobile-ui-profile-ui-user-website_114033.png', 'Anuradhapuraya'),
(8, 'Avukana Buddha statue', 'The Avukana Buddha statue is a renowned ancient sculpture located in Sri Lanka, specifically in the district of Anuradhapura the cradle of our ancient civilization. It stands near the small town of Avukana, hence the name. It is carved out of a rock boulder and lies close to the serene Kala Weva reservoir built by King Dhatusena in the 5th century AD.', '', 'Anuradhapuraya'),
(17, 'Sri maha bodiya', 'After the introduction of Buddhism to Sri Lanka by Mahinda Thero in 250 BC Emperor Asoka in India sent his daughter Theri Sanghamitta to the island with a branch of the Sacred Bodhi obtained from the main stem of the bodhi tree in Bodh Gaya under which Buddha attained enlightenment. King Devanampiyatissa received this sapling and planted it at the present site in Mahameghavana Garden in 249 BC. Taking this information into account today (in 2023) the Sri Maha Bodhi tree is exactly 2273 years old. Thus this tree is considered the oldest living tree in world in the recorded history.', './uploads/search (2).png', 'Anuradhapuraya'),
(19, 'Mirisawetiya Dagaba', 'Mirisawetiya Dagaba was built by King Dutugamunu (161-137 BCE) and this belongs to the Mahavihara Complex. King Dutugamunu was the great king who defeated the Tamil invaders who ruled the country for 30 years and brought the country under one ruler in the 1st century BCE.', './uploads/search_locate_find_icon-icons.com_67287.png', 'Anuradhapuraya'),
(43, 'Beach', 'sea view \r\nnice for day out', '', 'matara'),
(44, 'Sigiriya', 'Sigiriya or Sinhagiri is an ancient rock fortress located in the northern Matale District near the town of Dambulla in the Central Province, Sri Lanka. It is a site of historical and archaeological significance that is dominated by a massive column of granite approximately 180 m high.', '', 'Dambulla'),
(45, 'Pidurangala', 'Pidurangala is a massive rock formation located a few kilometers north of Sigiriya in Sri Lanka. It has an interesting history closely related to that of the Sigiriya Rock Fortress. Climbing to the top of Pidurangala Rock is more strenuous than climbing Sigiriya. If you are fit and adventurous it is a climb worth making. It will take you about two hours. There is far less to see on this site than Sigiriya.', '', 'Dambulla'),
(53, 'Jethawanarama Sthupa', 'Although very similar to the Abhayagiri Stupa, the Jetavanarama Stupa in Anuradhapura was built in the 3rd century AD. It is considered to be the largest stupa in the country and the world, and was once the foremost place of worship for Mahayana Buddhists (a branch of Buddhism). In fact, King Mahasen’s reason to build the stupa was to propagate Mahayana Buddhism over Theravada Buddhism.', '', 'Anuradhapuraya'),
(54, 'Dambulla Royal Cave Temple and Golden Temple', 'Dambulla cave temple, also known as the Golden Temple of Dambulla, is a World Heritage Site in Sri Lanka, situated in the central part of the country. This site is situated 148 kilometres east of Colombo, 72 kilometres north of Kandy and 43 kilometres north of Matale.', '', 'Dambulla'),
(55, 'Unawatuna Beach', 'Unawatuna is one of the biggest tourist destinations in Sri Lanka and is the most “famous” beach in the country. It is a lovely banana-shaped beach of golden sand and turquoise water, surrounded by green palm trees! It was the first beach we visited in Sri Lanka. We chose not to stay at one of the fancy and expensive hotels along the beach of Unawatuna, but a few Kilometers away at a guesthouse. We walked to the Unawatuna Beach on two separate day trips.', '', 'Galle'),
(56, 'Ella Nine Arch Bridge', 'Ella is a small town in the Badulla District of Uva Province, Sri Lanka governed by an Urban Council. It is approximately 200 kilometres east of Colombo and is situated at an elevation of 1,041 metres above sea level. The area has a rich bio-diversity, dense with numerous varieties of flora and fauna.', '', 'Badulla');

-- --------------------------------------------------------

--
-- Table structure for table `destination_images`
--

CREATE TABLE `destination_images` (
  `id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `destination_images`
--

INSERT INTO `destination_images` (`id`, `destination_id`, `image_url`) VALUES
(11, 43, './uploads/roomd.jpg'),
(19, 44, './uploads/R (7).jpeg'),
(22, 17, 'uploads/Sri-Maha-Bodiya.jpg'),
(23, 19, 'uploads/OIP (7).jpeg'),
(24, 45, './uploads/R (8).jpeg'),
(25, 3, 'uploads/R (9).jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `hotel_id` int(11) NOT NULL,
  `hotel_name` varchar(100) NOT NULL,
  `total_rooms` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `description` varchar(20) NOT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city_id` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`hotel_id`, `hotel_name`, `total_rooms`, `user_id`, `description`, `location`, `city_id`) VALUES
(31, 'New Hotel', 5, 11, 'hi', 'matara', 11),
(35, 'Sigiriya Kingdom Resort ', 3, 18, 'swimming pools, Free', 'Dambulla', 1003),
(36, 'Gold Crown Residence', 3, 19, 'good', 'Dambulla', 1003),
(37, 'Eden Grand ', 3, 20, ' ', 'Dambulla', 1003);

-- --------------------------------------------------------

--
-- Table structure for table `hotel_destinations`
--

CREATE TABLE `hotel_destinations` (
  `id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotel_destinations`
--

INSERT INTO `hotel_destinations` (`id`, `hotel_id`, `destination_id`) VALUES
(9, 31, 43),
(10, 31, 54);

-- --------------------------------------------------------

--
-- Table structure for table `hotel_images`
--

CREATE TABLE `hotel_images` (
  `image_id` int(11) NOT NULL,
  `hotel_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotel_images`
--

INSERT INTO `hotel_images` (`image_id`, `hotel_id`, `image_path`) VALUES
(12, 35, 'uploads/2.jpg'),
(13, 36, 'uploads/trh.jpg'),
(14, 37, 'uploads/dfs.jpg'),
(16, 31, 'uploads/ac725cf3a09c452978e89844b0cccc76263c8c70.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `message` varchar(255) DEFAULT NULL,
  `status` enum('unread','read') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `hotel_id` int(11) DEFAULT NULL,
  `room_name` varchar(255) NOT NULL,
  `room_number` int(255) NOT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `max_adults` int(11) NOT NULL,
  `max_children` int(11) NOT NULL,
  `availability` varchar(255) NOT NULL,
  `hotel_name` varchar(255) DEFAULT NULL,
  `facilities` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `hotel_id`, `room_name`, `room_number`, `price_per_night`, `max_adults`, `max_children`, `availability`, `hotel_name`, `facilities`) VALUES
(15, 31, 'we', 27, 20.00, 2, 2, 'available', NULL, '0'),
(27, 31, 'no', 3, 3000.00, 2, 1, 'available', NULL, 'ac'),
(28, 31, '', 45, 4.00, 2, 1, 'Available', NULL, 'no'),
(29, 31, '', 4, 2.00, 1, 0, 'Available', NULL, '2'),
(33, 31, '22', 2, 4.00, 1, 1, 'available', NULL, '22\r\nhq th\r\nkj55'),
(34, 35, '', 1, 17000.00, 2, 0, 'Available', NULL, 'swimming pools\r\nFree WiFi\r\nRestaurant\r\nFree parking'),
(35, 35, '', 2, 21000.00, 2, 1, 'Available', NULL, 'swimming pools\r\nFree WiFi\r\nRestaurant\r\nFree parking'),
(36, 35, '', 3, 32000.00, 2, 3, 'Available', NULL, 'swimming pools\r\nFree WiFi\r\nRestaurant\r\nFree parking'),
(37, 36, '', 1, 9500.00, 2, 0, 'Available', NULL, 'Swimming pools\r\nFree WiFi\r\nRestaurant\r\nAirport shuttle'),
(38, 36, '', 2, 11700.00, 2, 1, 'Available', NULL, 'Swimming pools\r\nFree WiFi\r\nRestaurant\r\nAirport shuttle'),
(39, 36, '', 3, 18000.00, 2, 3, 'Available', NULL, 'Swimming pools\r\nFree WiFi\r\nRestaurant\r\nAirport shuttle'),
(40, 37, '', 1, 9250.00, 2, 0, 'Available', NULL, 'swimming pools\r\nFree WiFi\r\nRestaurant\r\nAirport shuttle\r\nFitness centrer'),
(41, 37, '', 2, 26700.00, 2, 1, 'Available', NULL, 'swimming pools\r\nFree WiFi\r\nRestaurant\r\nAirport shuttle\r\nFitness centrer'),
(42, 37, '', 3, 38560.00, 2, 3, 'Available', NULL, 'swimming pools\r\nFree WiFi\r\nRestaurant\r\nAirport shuttle\r\nFitness centrer');

--
-- Triggers `rooms`
--
DELIMITER $$
CREATE TRIGGER `update_total_rooms_after_delete` AFTER DELETE ON `rooms` FOR EACH ROW BEGIN
    -- Update the total_rooms in hotels table
    UPDATE hotels
    SET total_rooms = total_rooms - 1
    WHERE hotel_id = OLD.hotel_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_total_rooms_after_insert` AFTER INSERT ON `rooms` FOR EACH ROW BEGIN
    -- Update the total_rooms in hotels table
    UPDATE hotels
    SET total_rooms = total_rooms + 1
    WHERE hotel_id = NEW.hotel_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_total_rooms_after_update` AFTER UPDATE ON `rooms` FOR EACH ROW BEGIN
    -- If the hotel_id is changed, update the total rooms for both hotels
    IF OLD.hotel_id != NEW.hotel_id THEN
        -- Decrement from the old hotel
        UPDATE hotels
        SET total_rooms = total_rooms - 1
        WHERE hotel_id = OLD.hotel_id;
        
        -- Increment for the new hotel
        UPDATE hotels
        SET total_rooms = total_rooms + 1
        WHERE hotel_id = NEW.hotel_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `room_images`
--

CREATE TABLE `room_images` (
  `image_id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_images`
--

INSERT INTO `room_images` (`image_id`, `room_id`, `image_path`) VALUES
(2, NULL, 'uploads/Screenshot 2024-08-13 152159.png'),
(3, NULL, 'uploads/Screenshot 2024-08-13 152159.png'),
(5, NULL, 'uploads/Screenshot 2024-08-16 220034.png'),
(31, 27, 'room_27_66e514d86875f9.91549764.jpg'),
(33, 15, 'uploads/R (2).jpeg'),
(37, 15, 'uploads/room_27_image3.jpeg'),
(38, NULL, 'uploads/rooms/room__image1.jpg'),
(39, 29, 'uploads/rooms/room_4_image1.jpg'),
(43, 28, 'uploads/room_45_image2.jpg'),
(46, 33, 'uploads/rooms/room_2_image1.jpg'),
(47, 34, 'uploads/rooms/room_01_image1.jpg'),
(48, 34, 'uploads/rooms/room_01_image2.jpg'),
(49, 35, 'uploads/rooms/room_2_image1.jpg'),
(50, 35, 'uploads/rooms/room_2_image2.jpg'),
(51, 36, 'uploads/rooms/room_3_image1.jpg'),
(52, 36, 'uploads/rooms/room_3_image2.jpg'),
(53, 36, 'uploads/rooms/room_3_image3.jpg'),
(54, 37, 'uploads/rooms/room_01_image1.jpg'),
(55, 37, 'uploads/rooms/room_01_image2.jpg'),
(56, 37, 'uploads/rooms/room_01_image3.jpg'),
(57, 38, 'uploads/rooms/room_02_image1.jpg'),
(58, 38, 'uploads/rooms/room_02_image2.jpg'),
(59, 38, 'uploads/rooms/room_02_image3.jpg'),
(60, 39, 'uploads/rooms/room_03_image1.jpg'),
(61, 39, 'uploads/rooms/room_03_image2.jpg'),
(62, 39, 'uploads/rooms/room_03_image3.jpg'),
(63, 40, 'uploads/rooms/room_01_image1.jpg'),
(64, 40, 'uploads/rooms/room_01_image2.jpg'),
(65, 40, 'uploads/rooms/room_01_image3.jpg'),
(66, 41, 'uploads/rooms/room_02_image1.jpg'),
(67, 41, 'uploads/rooms/room_02_image2.jpg'),
(68, 41, 'uploads/rooms/room_02_image3.jpg'),
(69, 42, 'uploads/rooms/room_03_image1.jpg'),
(70, 42, 'uploads/rooms/room_03_image2.jpg'),
(71, 42, 'uploads/rooms/room_03_image3.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_type` varchar(20) NOT NULL,
  `description` varchar(20) NOT NULL,
  `hotel_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `telephone` varchar(15) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `created_at`, `user_type`, `description`, `hotel_id`, `name`, `telephone`, `profile_picture`) VALUES
(7, 'er', 'erer@d', '$2y$10$qPQFWjDYPAAOpKE.1.665ePJne03zdHARDK8MzOjmlVjjc2tXH1NO', '2024-08-12 07:15:18', 'hotel_admin', '', 16, NULL, NULL, NULL),
(9, 'Mihiru', 'mihiru09@gmail.com', '$2y$10$zHLJ7', '2024-08-12 08:06:15', 'admin', '', NULL, NULL, NULL, NULL),
(10, 'testuser', 'testuser@gmail.com', '$2y$10$b5JXTAYnKnVOMJN6rstbt.oKb.vqtks4eS1fl38g71Ml5aK68kdcC', '2024-08-13 07:26:15', 'user', '', NULL, 'User 01', '0701234567', 'bg11.png'),
(11, 'testhadmin', 'testhadmin@gmail.com', '$2y$10$azimQGMjUJrViRdmD/3z0ur8Hp5X0BZKy9hvLfhVZCPCVIIe6U0N2', '2024-08-13 07:27:26', 'hotel_admin', '', 31, 'hotel', '000000000000', 'travel-and-tourism-background-vector.jpg'),
(12, 'testadmin', 'testadmin@gmail.com', '$2y$10$DByNtJngGCUTNKEV9itPaOQaPeMSbzpyFKVZHEbz.98CiDo9PP0R.', '2024-08-13 07:28:12', 'admin', '', 33, 'Admin', '0123456789', '4092564-about-mobile-ui-profile-ui-user-website_114033.png'),
(14, 'ab', 'ab@a', '$2y$10$NEIrq', '2024-08-13 08:35:19', 'hotel_admin', '', 30, NULL, NULL, NULL),
(15, 'hadmin', 'ff@g', '$2y$10$x77Ls', '2024-10-16 08:15:24', 'user', '', NULL, NULL, NULL, NULL),
(16, 'mm', 'mm@m', '$2y$10$b5JXTAYnKnVOMJN6rstbt.oKb.vqtks4eS1fl38g71Ml5aK68kdcC', '2024-10-16 08:16:50', 'hotel_admin', '', 34, NULL, NULL, NULL),
(18, 'hotel1', 'hotel1@gmail.com', '$2y$10$5mBzQD4d5c1RH3bsfUjdSOgwHxvVDU19ITZunOksBPm.bcaApM/Fa', '2024-10-18 12:19:45', 'hotel_admin', '', 35, 'Hotel1', '0123456789', NULL),
(19, 'hotel2', 'hotel2@gmail.com', '$2y$10$b3lkMEsFn7mR1iDTTM9jx.W2FvAm.g3LTkMS3P22TIP7KWZDA.JLq', '2024-10-18 12:33:58', 'hotel_admin', '', 36, 'hotel2', '0123456789', NULL),
(20, 'Hotel3', 'hotel3@gmail.com', '$2y$10$ycESjBgF.njATrxwRmng6OCS.xBx3srSL8sngcdhUyJ72y.Y0nzUW', '2024-10-18 13:33:51', 'hotel_admin', '', 37, 'hotel3', '0123456789', NULL),
(21, 'mihiru md', 'mihirudahanayaka@gmail.com', '$2y$10$7M7ViqwD4.1OZNZ.u8HypuYyHbittfoBPL5U940fbndXW5gdmwPPO', '2024-10-20 05:35:39', 'user', '', NULL, 'Mihiru', '0704868401', 'DALL·E 2024-10-20 03.22.01 - A clean black-and-white sequence diagram without graphics, illustrating the flow for a hotel booking system website. The diagram should involve two ac.webp');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`city_name`),
  ADD UNIQUE KEY `city_name` (`city_name`),
  ADD UNIQUE KEY `city_id` (`city_id`);

--
-- Indexes for table `destinations`
--
ALTER TABLE `destinations`
  ADD PRIMARY KEY (`destination_id`) USING BTREE,
  ADD KEY `fk_city` (`city`);

--
-- Indexes for table `destination_images`
--
ALTER TABLE `destination_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `destination_id` (`destination_id`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`hotel_id`),
  ADD UNIQUE KEY `hotel_name` (`hotel_name`),
  ADD KEY `user_id` (`user_id`) USING BTREE,
  ADD KEY `fk_location` (`location`),
  ADD KEY `fk_hotel_city_id` (`city_id`);

--
-- Indexes for table `hotel_destinations`
--
ALTER TABLE `hotel_destinations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_id` (`hotel_id`),
  ADD KEY `destination_id` (`destination_id`);

--
-- Indexes for table `hotel_images`
--
ALTER TABLE `hotel_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `hotel_id` (`hotel_id`),
  ADD KEY `fk_hotel_name` (`hotel_name`);

--
-- Indexes for table `room_images`
--
ALTER TABLE `room_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `city_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1071;

--
-- AUTO_INCREMENT for table `destinations`
--
ALTER TABLE `destinations`
  MODIFY `destination_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `destination_images`
--
ALTER TABLE `destination_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `hotel_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `hotel_destinations`
--
ALTER TABLE `hotel_destinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `hotel_images`
--
ALTER TABLE `hotel_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `room_images`
--
ALTER TABLE `room_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE;

--
-- Constraints for table `destinations`
--
ALTER TABLE `destinations`
  ADD CONSTRAINT `fk_city` FOREIGN KEY (`city`) REFERENCES `cities` (`city_name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `destination_images`
--
ALTER TABLE `destination_images`
  ADD CONSTRAINT `destination_images_ibfk_1` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`destination_id`) ON DELETE CASCADE;

--
-- Constraints for table `hotels`
--
ALTER TABLE `hotels`
  ADD CONSTRAINT `fk_hotel_city_id` FOREIGN KEY (`city_id`) REFERENCES `cities` (`city_id`),
  ADD CONSTRAINT `fk_location` FOREIGN KEY (`location`) REFERENCES `cities` (`city_name`),
  ADD CONSTRAINT `hotels_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `hotel_destinations`
--
ALTER TABLE `hotel_destinations`
  ADD CONSTRAINT `hotel_destinations_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hotel_destinations_ibfk_2` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`destination_id`) ON DELETE CASCADE;

--
-- Constraints for table `hotel_images`
--
ALTER TABLE `hotel_images`
  ADD CONSTRAINT `hotel_images_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_4` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `fk_hotel_name` FOREIGN KEY (`hotel_name`) REFERENCES `hotels` (`hotel_name`) ON DELETE CASCADE,
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`hotel_id`) ON DELETE CASCADE;

--
-- Constraints for table `room_images`
--
ALTER TABLE `room_images`
  ADD CONSTRAINT `room_images_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
