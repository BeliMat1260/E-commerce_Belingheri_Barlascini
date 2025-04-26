-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Apr 26, 2025 alle 12:53
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `my_mattiabelingheri`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address_name` varchar(100) NOT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `country` varchar(100) NOT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `address_name`, `address_line1`, `address_line2`, `city`, `state`, `postal_code`, `country`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 1, 'ejhty', 'Dwerfve', 'vewrverw', 'htreh', 'verv', 'jyturjtrj', 'Canada', 1, '2025-04-26 09:40:20', '2025-04-26 09:48:11');

-- --------------------------------------------------------

--
-- Struttura della tabella `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Pool Balls', 'High-quality pool balls for professional and recreational use', '2025-04-14 07:41:59'),
(2, 'Water Balls', 'Fun and colorful water balls for pool games and activities', '2025-04-14 07:41:59');

-- --------------------------------------------------------

--
-- Struttura della tabella `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `shipping_address` text NOT NULL,
  `billing_address` text NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` enum('pending','completed','failed') DEFAULT 'pending',
  `tracking_number` varchar(100) DEFAULT NULL,
  `estimated_delivery` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `shipping_address`, `billing_address`, `payment_method`, `payment_status`, `tracking_number`, `estimated_delivery`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 64.95, 'cancelled', 'Mkmfqwefrwqeg3erq', 'etrherwthrwth', 'credit_card', 'pending', NULL, NULL, '', '2025-04-26 08:38:13', '2025-04-26 10:04:47'),
(2, 1, 19.99, 'cancelled', 'Dwerfve\r\nvewrverw\r\nhtreh, verv jyturjtrj\r\nCanada', 'Dwerfve\r\nvewrverw\r\nhtreh, verv jyturjtrj\r\nCanada', 'credit_card', 'pending', NULL, NULL, '', '2025-04-26 10:49:25', '2025-04-26 10:51:51'),
(3, 1, 49.99, 'pending', 'Dwerfve\r\nvewrverw\r\nhtreh, verv jyturjtrj\r\nCanada', 'Dwerfve\r\nvewrverw\r\nhtreh, verv jyturjtrj\r\nCanada', 'credit_card', 'pending', NULL, NULL, '', '2025-04-26 10:49:53', '2025-04-26 10:49:53');

-- --------------------------------------------------------

--
-- Struttura della tabella `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `created_at`) VALUES
(1, 1, 4, 5, 12.99, '2025-04-26 08:38:13'),
(2, 2, 3, 1, 19.99, '2025-04-26 10:49:25'),
(3, 3, 1, 1, 49.99, '2025-04-26 10:49:53');

-- --------------------------------------------------------

--
-- Struttura della tabella `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `sku` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `material` varchar(100) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `weight` decimal(10,2) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `rating` decimal(3,2) DEFAULT 0.00,
  `review_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category_id` int(11) DEFAULT NULL,
  `capacity` varchar(50) DEFAULT NULL,
  `dimensions` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `products`
--

INSERT INTO `products` (`id`, `sku`, `name`, `description`, `material`, `size`, `weight`, `color`, `price`, `discount_price`, `stock_quantity`, `featured`, `is_active`, `rating`, `review_count`, `created_at`, `updated_at`, `category_id`, `capacity`, `dimensions`) VALUES
(1, 'PB0001', 'Professional Pool Ball Set', 'Complete set of 16 professional pool balls, including cue ball', 'Phenolic Resin', '57.15 mm', 170.00, NULL, 49.99, NULL, 49, 1, 1, 0.00, 0, '2025-04-14 07:35:04', '2025-04-26 10:49:53', 1, NULL, NULL),
(2, 'PB0002', 'Water Exercise Ball', 'Durable water exercise ball for pool workouts', 'Phenolic Resin', '57.15 mm', 170.00, NULL, 29.99, NULL, 30, 1, 1, 0.00, 0, '2025-04-14 07:35:04', '2025-04-26 08:31:59', 1, NULL, NULL),
(3, 'PB0003', 'Kids Pool Ball Set', 'Colorful set of 6 pool balls for children', 'Phenolic Resin', '57.15 mm', 170.00, NULL, 19.99, NULL, 40, 0, 1, 0.00, 0, '2025-04-14 07:35:04', '2025-04-26 10:51:51', 1, NULL, NULL),
(4, 'PB0004', 'Floating Water Ball', 'Fun floating ball for pool games', 'Phenolic Resin', '57.15 mm', 170.00, NULL, 12.99, NULL, 60, 0, 1, 0.00, 0, '2025-04-14 07:35:04', '2025-04-26 10:04:47', 1, NULL, NULL),
(5, 'PB0005', 'Tournament Pool Balls', 'High-quality tournament-grade pool balls', 'Phenolic Resin', '57.15 mm', 170.00, NULL, 79.99, NULL, 25, 1, 1, 0.00, 0, '2025-04-14 07:35:04', '2025-04-26 08:31:59', 1, NULL, NULL),
(6, 'PB0006', 'Water Polo Ball', 'Official size water polo ball', 'Phenolic Resin', '57.15 mm', 170.00, NULL, 34.99, NULL, 20, 1, 1, 0.00, 0, '2025-04-14 07:35:04', '2025-04-26 08:31:59', 1, NULL, NULL);

--
-- Trigger `products`
--
DELIMITER $$
CREATE TRIGGER `after_product_delete` AFTER DELETE ON `products` FOR EACH ROW BEGIN
    DELETE FROM cart WHERE product_id = OLD.id;
    DELETE FROM cart_items WHERE product_id = OLD.id;
    DELETE FROM cart_sessions WHERE product_id = OLD.id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image_order` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_url`, `created_at`, `image_order`) VALUES
(1, 1, 'assets/images/pool-balls-set.jpg', '2025-04-26 08:31:59', 1),
(2, 2, 'assets/images/water-exercise-ball.jpg', '2025-04-26 08:31:59', 1),
(3, 3, 'assets/images/kids-pool-balls.jpg', '2025-04-26 08:31:59', 1),
(4, 4, 'assets/images/floating-ball.jpg', '2025-04-26 08:31:59', 1),
(5, 5, 'assets/images/tournament-balls.jpg', '2025-04-26 08:31:59', 1),
(6, 6, 'assets/images/water-polo-ball.jpg', '2025-04-26 08:31:59', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Trigger `product_reviews`
--
DELIMITER $$
CREATE TRIGGER `after_review_delete` AFTER DELETE ON `product_reviews` FOR EACH ROW BEGIN
    UPDATE products 
    SET rating = (SELECT AVG(rating) FROM product_reviews WHERE product_id = OLD.product_id),
        review_count = (SELECT COUNT(*) FROM product_reviews WHERE product_id = OLD.product_id)
    WHERE id = OLD.product_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_review_insert` AFTER INSERT ON `product_reviews` FOR EACH ROW BEGIN
    UPDATE products 
    SET rating = (SELECT AVG(rating) FROM product_reviews WHERE product_id = NEW.product_id),
        review_count = (SELECT COUNT(*) FROM product_reviews WHERE product_id = NEW.product_id)
    WHERE id = NEW.product_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `first_name`, `last_name`, `address`, `city`, `state`, `zip_code`, `country`, `phone`, `role`, `last_login`, `is_active`, `created_at`, `updated_at`, `profile_image`) VALUES
(1, 'Belinga', 'ssssafwgfher@jnjn.bhb', '$2y$10$iIsOEfBCyDwmubsJQXoi8OOXZHSYnH8WulC.HcjAv8wue8YzxfqYC', 'Mattia', 'Belingheri', NULL, NULL, NULL, NULL, NULL, NULL, 'user', NULL, 1, '2025-04-26 08:37:34', '2025-04-26 08:37:34', NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(3, 1, 2, '2025-04-26 10:18:22');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_default_address` (`user_id`,`is_default`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_default` (`is_default`);

--
-- Indici per le tabelle `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_product` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_product_id` (`product_id`);

--
-- Indici per le tabelle `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indici per le tabelle `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_price` (`price`);

--
-- Indici per le tabelle `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_featured` (`featured`),
  ADD KEY `idx_stock` (`stock_quantity`);

--
-- Indici per le tabelle `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_product_images_order` (`product_id`,`image_order`);

--
-- Indici per le tabelle `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indici per le tabelle `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist_item` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT per la tabella `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT per la tabella `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT per la tabella `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT per la tabella `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Limiti per la tabella `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Limiti per la tabella `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Limiti per la tabella `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_image_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Limiti per la tabella `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `fk_review_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_review_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Limiti per la tabella `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `fk_wishlist_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
