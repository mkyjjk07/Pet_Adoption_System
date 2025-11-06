-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Sep 07, 2025 at 11:50 AM
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
-- Database: `pet_adoption`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','staff') DEFAULT 'staff',
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mobile_no` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`, `role`, `first_name`, `last_name`, `email`, `mobile_no`) VALUES
(1, 'superadmin', '$2y$10$uG3BoPwsEWmO/Ve6v2UqAOe0Db.smA1MlqKEsMuT2ClM.i.QDvvwa', 'super_admin', 'Super', 'Admin', 'admin001@petnest.com', '9876543210'),
(2, 'amit', '$2y$10$5AhOQZI2WcQP6xMHRVbK/.1HlyKwwdar7e/FNfGfBefrC/Fo1McV.', 'staff', 'amit', 'shah', 'amit@petnest.com', '1122334455');

-- --------------------------------------------------------

--
-- Table structure for table `adoption_requests`
--

CREATE TABLE `adoption_requests` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `pet_id` int(11) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `message` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `requested_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `experience` text DEFAULT NULL,
  `reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adoption_requests`
--

INSERT INTO `adoption_requests` (`request_id`, `user_id`, `pet_id`, `full_name`, `message`, `status`, `requested_on`, `email`, `phone`, `address`, `experience`, `reason`) VALUES
(1, 1, 4, 'Mita Kavalkar', NULL, 'pending', '2025-09-07 09:43:25', 'mita12@gmail.com', '9978098765', 'a-119,,XXX building,ABC road,Surat', 'No', 'I like pets. Adopting a pet  makes me happier and less stressed.');

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `donation_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `donated_on` timestamp NOT NULL DEFAULT current_timestamp(),
  `donation_type` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pets`
--

CREATE TABLE `pets` (
  `pet_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `breed` varchar(100) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'available',
  `added_on` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pets`
--

INSERT INTO `pets` (`pet_id`, `name`, `type`, `breed`, `age`, `gender`, `price`, `description`, `image`, `status`, `added_on`) VALUES
(1, 'Rocky', 'Dog', 'Labrador', 2, 'Male', 15000.00, 'Friendly and playful.', 'rocky.jpeg', 'available', '2025-08-08 04:55:15'),
(2, 'Max', 'Dog', 'Golden Retriever', 2, 'Male', 16000.00, 'Friendly and playful dog, good with kids.', 'max.jpeg', 'available', '2025-08-24 18:05:58'),
(3, 'Misty', 'Cat', 'Himalayan', 3, 'Female', 22000.00, 'Fluffy and calm Himalayan cat.', 'Misty.jpeg', 'available', '2025-08-25 09:38:11'),
(4, 'Peter', 'Rabbit', 'Angora Rabbit', 8, 'male', 15000.00, 'Peter is a fluffy white Angora rabbit with long, silky fur that requires regular grooming. He is calm, friendly, and enjoys being cuddled, making him a lovely pet for families.', 'peter.jpeg', 'available', '2025-09-07 06:24:56'),
(5, 'Tommy', 'Dog', 'Indian Pariah', 2, 'Male', 1500.00, 'Friendly Indian Pariah dog, healthy and active.', 'dog1.jpg', 'available', '2025-09-06 16:51:13'),
(6, 'Lucy', 'Dog', 'Labrador Retriever', 3, 'Female', 3500.00, 'Playful Labrador, vaccinated and dewormed.', 'dog2.jpg', 'available', '2025-09-06 16:51:13'),
(7, 'Milo', 'Cat', 'Domestic Shorthair', 1, 'Male', 1000.00, 'Cute Indian cat, litter trained and affectionate.', 'cat1.jpg', 'available', '2025-09-06 16:51:13'),
(8, 'Snowy', 'Rabbit', 'White Rabbit', 2, 'Female', 800.00, 'Adorable white rabbit, friendly with kids.', 'rabbit1.jpg', 'available', '2025-09-06 16:51:13'),
(9, 'Mitthu', 'Bird', 'Indian Ringneck Parrot', 4, 'Male', 1200.00, 'Talking parrot, healthy and active.', 'bird1.jpeg', 'available', '2025-09-06 16:51:13'),
(10, 'Bruno', 'Dog', 'German Shepherd', 3, 'Male', 4000.00, 'Loyal and protective, great family dog.', 'dog3.jpg', 'available', '2025-09-06 16:58:13'),
(11, 'Roxy', 'Dog', 'Beagle', 2, 'Female', 3200.00, 'Energetic and friendly beagle, loves kids.', 'dog4.jpg', 'available', '2025-09-06 16:58:13'),
(12, 'Robin', 'Dog', 'Golden Retriever', 4, 'Male', 4500.00, 'Gentle and playful golden retriever.', 'dog5.jpg', 'available', '2025-09-06 16:58:13'),
(13, 'Chiku', 'Dog', 'Pomeranian', 1, 'Female', 2800.00, 'Small fluffy companion, perfect for apartments.', 'dog6.jpg', 'available', '2025-09-06 16:58:13'),
(14, 'Tiger', 'Dog', 'Indie Mix', 2, 'Male', 1200.00, 'Adopted from street, vaccinated and trained.', 'dog7.jpg', 'available', '2025-09-06 16:58:13'),
(15, 'Simba', 'Dog', 'Dalmatian', 3, 'Male', 3800.00, 'Spotted dalmatian, playful and unique.', 'dog8.jpg', 'available', '2025-09-06 16:58:13'),
(16, 'jenny', 'Dog', 'Shih Tzu', 2, 'Female', 5000.00, 'Cute toy breed, well-groomed and healthy.', 'dog9.jpg', 'available', '2025-09-06 16:58:13'),
(17, 'Mark', 'Dog', 'Rottweiler', 3, 'Male', 4200.00, 'Strong and loyal Rottweiler, needs experienced owner.', 'dog10.jpg', 'available', '2025-09-06 16:58:13'),
(18, 'Kitty', 'Cat', 'Persian', 2, 'Female', 2500.00, 'Fluffy Persian cat, calm and friendly.', 'cat2.jpg', 'available', '2025-09-06 16:58:13'),
(19, 'Leo', 'Cat', 'Siamese', 1, 'Male', 2200.00, 'Vocal Siamese cat with striking blue eyes.', 'cat3.jpg', 'available', '2025-09-06 16:58:13'),
(20, 'Misty', 'Cat', 'Maine Coon', 3, 'Female', 2700.00, 'Large and gentle Maine Coon cat.', 'cat4.jpg', 'available', '2025-09-06 16:58:13'),
(21, 'Shadow', 'Cat', 'Bombay', 2, 'Male', 1900.00, 'Glossy black Bombay cat, affectionate.', 'cat5.jpg', 'available', '2025-09-06 16:58:13'),
(22, 'Nikki', 'Cat', 'Indian Billi', 1, 'Female', 800.00, 'Cute Indian domestic cat, very playful.', 'cat6.jpg', 'available', '2025-09-06 16:58:13'),
(23, 'Coco', 'Cat', 'British Shorthair', 2, 'Female', 2600.00, 'Calm and friendly British Shorthair.', 'cat7.jpg', 'available', '2025-09-06 16:58:13'),
(24, 'Oreo', 'Cat', 'Ragdoll', 2, 'Male', 3000.00, 'Soft Ragdoll cat, loves cuddles.', 'cat8.jpg', 'available', '2025-09-06 16:58:13'),
(25, 'Bella', 'Cat', 'Himalayan', 3, 'Female', 2400.00, 'Beautiful Himalayan cat, calm nature.', 'cat9.jpg', 'available', '2025-09-06 16:58:13'),
(26, 'Bunny', 'Rabbit', 'Angora', 2, 'Male', 900.00, 'Fluffy Angora rabbit, easy to care for.', 'rabbit2.jpg', 'available', '2025-09-06 16:58:13'),
(27, 'Lilly', 'Rabbit', 'Lop Rabbit', 1, 'Female', 850.00, 'Cute lop-eared rabbit, very friendly.', 'rabbit3.jpg', 'available', '2025-09-06 16:58:13'),
(28, 'Fluffy', 'Rabbit', 'Netherland Dwarf', 2, 'Male', 950.00, 'Tiny dwarf rabbit, adorable and active.', 'rabbit4.jpg', 'available', '2025-09-06 16:58:13'),
(29, 'Snowbell', 'Rabbit', 'Rex Rabbit', 3, 'Female', 1000.00, 'Soft-furred Rex rabbit, gentle with kids.', 'rabbit5.jpg', 'available', '2025-09-06 16:58:13'),
(30, 'Mithu', 'Bird', 'Budgerigar', 1, 'Male', 400.00, 'Colorful budgie, very active.', 'bird1.jpg', 'available', '2025-09-06 16:58:13'),
(31, 'Pinky', 'Bird', 'Lovebird', 2, 'Female', 500.00, 'Pair-loving Lovebird, playful nature.', 'bird2.jpg', 'available', '2025-09-06 16:58:13'),
(32, 'Sunny', 'Bird', 'Cockatiel', 3, 'Male', 800.00, 'Yellow cockatiel, can whistle tunes.', 'bird3.jpg', 'available', '2025-09-06 16:58:13'),
(33, 'Raja', 'Bird', 'African Grey Parrot', 5, 'Male', 2000.00, 'Highly intelligent parrot, talks well.', 'bird4.jpg', 'available', '2025-09-06 16:58:13'),
(34, 'Meena', 'Bird', 'Finch', 1, 'Female', 300.00, 'Tiny finch bird, chirpy and lively.', 'bird5.jpg', 'available', '2025-09-06 16:58:13'),
(35, 'Shyam', 'Bird', 'Macaw', 4, 'Male', 3500.00, 'Bright macaw parrot, rare adoption.', 'bird6.jpg', 'available', '2025-09-06 16:58:13'),
(36, 'Chutki', 'Dog', 'Cocker Spaniel', 2, 'Female', 3200.00, 'Beautiful spaniel, loving nature.', 'dog11.jpg', 'available', '2025-09-06 16:58:13'),
(37, 'Bullet', 'Dog', 'Doberman', 3, 'Male', 3700.00, 'Alert and strong Doberman, protective.', 'dog12.jpg', 'available', '2025-09-06 16:58:13'),
(38, 'Daisy', 'Dog', 'Indie Mix', 1, 'Female', 1000.00, 'Street rescued Indie puppy, playful.', 'dog13.jpg', 'available', '2025-09-06 16:58:13'),
(39, 'Tiger', 'Dog', 'Siberian Husky', 2, 'Male', 6000.00, 'Blue-eyed Husky, active and friendly.', 'dog14.jpg', 'available', '2025-09-06 16:58:13'),
(40, 'Piku', 'Cat', 'Indian Billi', 1, 'Male', 700.00, 'Sweet Indian street cat, calm nature.', 'cat10.jpg', 'available', '2025-09-06 16:58:13'),
(41, 'Angel', 'Cat', 'Persian', 2, 'Female', 2500.00, 'Snow-white Persian, very affectionate.', 'cat11.jpg', 'available', '2025-09-06 16:58:13'),
(42, 'Smokey', 'Cat', 'Russian Blue', 3, 'Male', 2800.00, 'Elegant Russian Blue cat, calm and sweet.', 'cat12.jpg', 'available', '2025-09-06 16:58:13'),
(43, 'Snow', 'Rabbit', 'White Rabbit', 1, 'Male', 750.00, 'Cute little white rabbit, very active.', 'rabbit6.jpg', 'available', '2025-09-06 16:58:13'),
(44, 'Peachy', 'Rabbit', 'Lionhead Rabbit', 2, 'Female', 950.00, 'Fluffy mane rabbit, playful.', 'rabbit7.jpg', 'available', '2025-09-06 16:58:13'),
(45, 'Golu', 'Bird', 'Indian Ringneck Parrot', 2, 'Male', 1000.00, 'Cheerful talking parrot, easy to train.', 'bird7.jpg', 'available', '2025-09-06 16:58:13');
INSERT INTO `pets` (`pet_id`, `name`, `type`, `breed`, `age`, `gender`, `price`, `description`, `image`, `status`, `added_on`) VALUES
(46, 'Chirpy', 'Bird', 'Budgerigar', 1, 'Female', 350.00, 'Tiny budgie bird, colorful and lively.', 'bird8.jpg', 'available', '2025-09-06 16:58:13'),
(47, 'Buddy', 'Dog', 'Indie Mix', 2, 'Male', 1100.00, 'Adorable street mix dog, healthy.', 'dog15.jpg', 'available', '2025-09-06 16:58:13'),
(48, 'Ruby', 'Dog', 'Pug', 2, 'Female', 3000.00, 'Cute pug with wrinkled face, friendly.', 'dog16.jpg', 'available', '2025-09-06 16:58:13'),
(49, 'Jack', 'Dog', 'Boxer', 3, 'Male', 3300.00, 'Strong and playful Boxer dog.', 'dog17.jpg', 'available', '2025-09-06 16:58:13'),
(50, 'Tara', 'Cat', 'Siamese', 2, 'Female', 2100.00, 'Graceful Siamese, loving pet.', 'cat13.jpg', 'available', '2025-09-06 16:58:13'),
(51, 'Oscar', 'Cat', 'Indian Billi', 1, 'Male', 800.00, 'Cute kitten from India, litter trained.', 'cat14.jpg', 'available', '2025-09-06 16:58:13'),
(52, 'Kiki', 'Rabbit', 'Dutch Rabbit', 2, 'Female', 880.00, 'Friendly black and white rabbit.', 'rabbit8.jpg', 'available', '2025-09-06 16:58:13'),
(53, 'Bholu', 'Bird', 'Lovebird', 1, 'Male', 600.00, 'Pair-loving bird, colorful.', 'bird9.jpg', 'available', '2025-09-06 16:58:13'),
(54, 'Titu', 'Bird', 'Cockatoo', 5, 'Male', 4000.00, 'Rare cockatoo parrot, very talkative.', 'bird10.jpg', 'available', '2025-09-06 16:58:13'),
(55, 'Sandy', 'Dog', 'Golden Retriever', 3, 'Female', 4600.00, 'Playful retriever, very friendly.', 'dog18.jpg', 'available', '2025-09-06 16:58:13'),
(56, 'Blacky', 'Dog', 'Labrador', 2, 'Male', 3400.00, 'Black Labrador, loves water.', 'dog19.jpg', 'available', '2025-09-06 16:58:13'),
(57, 'Whiskers', 'Cat', 'Maine Coon', 3, 'Male', 2800.00, 'Big fluffy Maine Coon, cuddly.', 'cat15.jpg', 'available', '2025-09-06 16:58:13'),
(58, 'Luna', 'Cat', 'Persian', 2, 'Female', 2600.00, 'Gray Persian with long hair.', 'cat16.jpg', 'available', '2025-09-06 16:58:13'),
(59, 'Cinnamon', 'Rabbit', 'Mini Rex', 1, 'Female', 950.00, 'Small and soft rabbit.', 'rabbit9.jpg', 'available', '2025-09-06 16:58:13'),
(60, 'Mango', 'Bird', 'Budgerigar', 1, 'Male', 400.00, 'Yellow budgie, chirpy and lively.', 'bird11.jpg', 'available', '2025-09-06 16:58:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `role` enum('adopter','guest','volunteer') DEFAULT 'adopter',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `phone`, `city`, `role`, `created_at`) VALUES
(1, 'Mita', 'mita12@gmail.com', '$2y$10$WosWi6V0rEBSCLafScGhfuf1rZ7tgzMxFe6gnAAjM22Cu4GZcc2ly', '9978098765', 'Surat', 'adopter', '2025-09-06 12:03:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `adoption_requests`
--
ALTER TABLE `adoption_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `pet_id` (`pet_id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`donation_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`pet_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `adoption_requests`
--
ALTER TABLE `adoption_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `donation_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pets`
--
ALTER TABLE `pets`
  MODIFY `pet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adoption_requests`
--
ALTER TABLE `adoption_requests`
  ADD CONSTRAINT `adoption_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `adoption_requests_ibfk_2` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`pet_id`) ON DELETE CASCADE;

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `donations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
