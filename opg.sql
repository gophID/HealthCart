-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 08, 2020 at 09:34 PM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 7.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `opg`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `kupac_id` int(11) DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_croatian_ci DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '11',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_croatian_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `kupac_id`, `address`, `status`, `created_at`) VALUES
(11, 12, 'Trg Ante Starčevića 4B, 31000 Osijek', 11, '2020-03-08 21:11:57'),
(12, 12, 'Trg Ante Starčevića 4B, 31000 Osijek', 1, '2020-03-08 21:15:57'),
(13, 15, 'Stara Lipa 39, 34000 Požega', 11, '2020-03-08 21:29:28');

-- --------------------------------------------------------

--
-- Table structure for table `order_products`
--

CREATE TABLE `order_products` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '11',
  `comment` text COLLATE utf8mb4_croatian_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_croatian_ci;

--
-- Dumping data for table `order_products`
--

INSERT INTO `order_products` (`id`, `order_id`, `product_id`, `amount`, `price`, `status`, `comment`, `created_at`) VALUES
(23, 11, 18, 1, '16.00', 11, NULL, '2020-03-08 21:11:57'),
(24, 11, 19, 1, '29.00', 11, NULL, '2020-03-08 21:11:57'),
(25, 11, 22, 4, '35.00', 1, 'Trenutno nemamo dovoljnu količinu proizvoda na stanju. ', '2020-03-08 21:11:57'),
(26, 11, 31, 2, '90.00', 11, NULL, '2020-03-08 21:11:58'),
(27, 11, 35, 5, '55.00', 11, NULL, '2020-03-08 21:11:58'),
(28, 11, 37, 1, '150.00', 11, NULL, '2020-03-08 21:11:58'),
(29, 12, 21, 1, '35.00', 1, 'Narudžba se potvrđuje, možete je očekivati u roku 5 radnih dana', '2020-03-08 21:15:58'),
(30, 12, 30, 3, '20.00', 1, 'Narudžba je uspješno zaprimljena. Stiže na adresu kroz 14 dana.', '2020-03-08 21:15:58'),
(31, 13, 15, 4, '35.00', 11, NULL, '2020-03-08 21:29:28'),
(32, 13, 16, 1, '40.00', 11, NULL, '2020-03-08 21:29:28'),
(33, 13, 19, 1, '29.00', 11, NULL, '2020-03-08 21:29:29'),
(34, 13, 22, 2, '35.00', 11, NULL, '2020-03-08 21:29:29'),
(35, 13, 25, 3, '45.00', 11, NULL, '2020-03-08 21:29:29'),
(36, 13, 29, 1, '30.00', 11, NULL, '2020-03-08 21:29:29'),
(37, 13, 32, 1, '17.00', 11, NULL, '2020-03-08 21:29:29'),
(38, 13, 34, 1, '39.00', 11, NULL, '2020-03-08 21:29:29'),
(39, 13, 37, 1, '150.00', 11, NULL, '2020-03-08 21:29:29');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `naziv` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `slika` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `proizvodac_id` int(11) NOT NULL,
  `cijena` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `naziv`, `slika`, `proizvodac_id`, `cijena`, `created_at`) VALUES
(15, 'Trajni sok od mandarine 1l', 'IMG_20191129_014759-4-600x805.jpg', 9, '35.00', '2020-03-08 20:01:36'),
(16, 'Svježi sok od miksa agruma 0,33l', 'Sok-miks-agruma-50-600x842.jpg', 9, '40.00', '2020-03-08 20:02:34'),
(17, 'Svježi sok od šipka/nara 0,33l', 'sipak_smanjen2-1-600x876.jpg', 9, '33.00', '2020-03-08 20:04:36'),
(18, 'Sok od aronije 1l', 'maticni-sok-od-aronije-075-l-opg-lagator-slatki-plodovi-dakovstine-2.jpg', 9, '16.00', '2020-03-08 20:09:24'),
(19, 'Sok od bazge 1l', 'domaci-sok-od-bazge-GLAVNA.jpg', 9, '29.00', '2020-03-08 20:12:52'),
(20, 'Sok od cikle, mrkve i jabuke 1l', 'a98a644518b4c5e49c7cefe5cd493d6a_header.jpg', 9, '40.00', '2020-03-08 20:13:54'),
(21, 'Sok od jabuke 3l', 'images.jpg', 9, '35.00', '2020-03-08 20:16:11'),
(22, 'Cvjetni med 900g', 'cvjetni_med_prilagodjeno.jpg', 10, '35.00', '2020-03-08 20:22:04'),
(23, 'Šumski med 900g', 'sumski_med_prilagodjeno.jpg', 10, '35.00', '2020-03-08 20:22:51'),
(24, 'Bagremov med 450g', 'Galerija-1-12.jpg', 10, '30.00', '2020-03-08 20:24:07'),
(25, 'Med od uljane repice 900g', 'med_uljane_repice_prilagodjeno.jpg', 10, '45.00', '2020-03-08 20:24:59'),
(26, 'Med od lipe 900g', 'med_od_lipe_prilagodjeno.jpg', 10, '35.00', '2020-03-08 20:25:41'),
(27, 'Bronhial s anisom i propolisom 750g', 'bronhial_prilagodjeno.jpg', 10, '55.00', '2020-03-08 20:26:58'),
(28, 'Đumbir med limun', 'umbir-e1536324396846.jpg', 10, '50.00', '2020-03-08 20:27:41'),
(29, 'Cimetni med 250g', 'cimetni-mali.jpg', 10, '30.00', '2020-03-08 20:28:30'),
(30, 'Propolis', 'propolis_prilagodjeno.jpg', 10, '20.00', '2020-03-08 20:30:50'),
(31, 'Svježa matična mliječ', 'Untitledqyq_InPixio.png', 10, '90.00', '2020-03-08 20:32:28'),
(32, 'Balzam za usne', 'balzam_za_usne_prilagodjeno.jpg', 10, '17.00', '2020-03-08 20:34:19'),
(33, 'Maslinovo ulje', 'EKSTRA-DJEVIČANSKO-MASLINOVO-ULJE-BIO-ORGULA-500-ml-HRVATSKO.jpg', 11, '65.00', '2020-03-08 20:57:47'),
(34, 'Bučino ulje', 'bučino-ulje-OPG-Petrović-Mr.EKO_.jpg', 11, '39.00', '2020-03-08 21:00:12'),
(35, 'Kokosovo ulje', 'kokosovo-ulje-gljivice.jpg', 11, '55.00', '2020-03-08 21:01:22'),
(36, 'Mast od divljeg kestena i gaveza', 'imagessss.jpg', 11, '56.00', '2020-03-08 21:03:21'),
(37, 'Lavandino ulje', '106700.jpg', 11, '150.00', '2020-03-08 21:07:05');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `name`) VALUES
(1, 'Proizvođač'),
(2, 'Kupac');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) NOT NULL,
  `username` varchar(50) CHARACTER SET utf8 NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 NOT NULL,
  `address` varchar(255) CHARACTER SET utf8 NOT NULL,
  `role_id` int(10) UNSIGNED DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `address`, `role_id`, `created_at`) VALUES
(9, 'pperic', 'peroperic@opgperic.com', '$2y$10$Q6BFDTLx5y6lpHHP.l2Ic.Wq97ajy5wEgel/M4BhECl0vJnb2jYr6', 'Baštijanova 1A, 10000 Zagreb', 1, '2020-03-08 19:58:31'),
(10, 'hveber', 'hveber@pcelarstvoveber.hr', '$2y$10$ldcDB.gvEWYPAofkUJksnOd35nxUGFjiXBM2NqxpGaLJBimhYX3lK', 'Kvarnerska 17, 35000 Slavonski Brod', 1, '2020-03-08 20:19:56'),
(11, 'vbasic', 'vbasic@opgbasic.hr', '$2y$10$7O62EQOdVPbiL/L2C6cNDuWvhlIGz6u7SlhQB8Mmf0tXK6JcGlN/i', 'Obala Maršala Tita 80A, 52440 Poreč', 1, '2020-03-08 20:52:38'),
(12, 'mmikic45', 'mmikic@inet.hr', '$2y$10$0E1.UsmAESzw21JeLscYee7JL/.1/4zk2by0XcKkIzR1DUe8KyxX.', 'Trg Ante Starčevića 4B, 31000 Osijek', 2, '2020-03-08 21:10:30'),
(14, 'mmaric4', 'mmaric@gmail.com', '$2y$10$o1qzy5dh5Y7FRJcBT5AlsuZtG1E3aJOIVf5RWY0KbSTRAf3Crltye', 'Vukovarska ulica 154, 21000 Split', 1, '2020-03-08 21:26:32'),
(15, 'imarcetic', 'imarcetic@gmail.com', '$2y$10$mDIf/pUGUPIpBnit65tJoOvd3lCy4phdNcyAIDgssdznyJOn0caMW', 'Stara Lipa 39, 34000 Požega', 2, '2020-03-08 21:28:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kupac_id` (`kupac_id`);

--
-- Indexes for table `order_products`
--
ALTER TABLE `order_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proizvodac_id` (`proizvodac_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `order_products`
--
ALTER TABLE `order_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`kupac_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_products`
--
ALTER TABLE `order_products`
  ADD CONSTRAINT `order_products_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`proizvodac_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`proizvodac_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `products_ibfk_3` FOREIGN KEY (`proizvodac_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
