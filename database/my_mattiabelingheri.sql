-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Creato il: Mag 05, 2025 alle 12:39
-- Versione del server: 10.4.28-MariaDB
-- Versione PHP: 8.2.4

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
  `is_default` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `address_name`, `address_line1`, `address_line2`, `city`, `state`, `postal_code`, `country`, `is_default`) VALUES
(1, 1, 'ejhty', 'Dwerfve', 'vewrv', 'htreh', 'verv', 'jyturjtrj', 'Canada', 1),
(11, 2, 'asf', 'asf', 'asfasfaf', 'asf', 'asf', 'asf', 'afs', 1),
(12, 2, 'niggatest', 'niggatest', 'niggatest', 'niggatest', 'niggatest', '23017', 'niggatest', 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`) VALUES
(14, 2, 2, 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Pool Balls', 'High-quality pool balls for professional and recreational use'),
(2, 'Water Bottles', 'Premium water bottles for your hydration needs.');

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
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `shipping_address`, `billing_address`, `payment_method`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 64.95, 'cancelled', 'Mkmfqwefrwqeg3erq', 'etrherwthrwth', 'credit_card', '', '2025-04-26 08:38:13', '2025-04-26 10:04:47'),
(2, 1, 19.99, 'cancelled', 'Dwerfve\r\nvewrverw\r\nhtreh, verv jyturjtrj\r\nCanada', 'Dwerfve\r\nvewrverw\r\nhtreh, verv jyturjtrj\r\nCanada', 'credit_card', '', '2025-04-26 10:49:25', '2025-04-26 10:51:51'),
(3, 1, 49.99, 'pending', 'Dwerfve\r\nvewrverw\r\nhtreh, verv jyturjtrj\r\nCanada', 'Dwerfve\r\nvewrverw\r\nhtreh, verv jyturjtrj\r\nCanada', 'credit_card', '', '2025-04-26 10:49:53', '2025-04-26 10:49:53'),
(4, 1, 12.99, 'cancelled', 'Dwerfve\r\nvewrverw\r\nhtreh, verv jyturjtrj\r\nCanada', 'Dwerfve\r\nvewrverw\r\nhtreh, verv jyturjtrj\r\nCanada', 'paypal', '', '2025-05-03 08:08:46', '2025-05-03 08:12:56'),
(5, 1, 119.94, 'delivered', 'Dwerfve\r\nvewrverw\r\nhtreh, verv jyturjtrj\r\nCanada', 'Dwerfve\r\nvewrverw\r\nhtreh, verv jyturjtrj\r\nCanada', 'paypal', '', '2025-05-03 08:13:22', '2025-05-03 08:14:24'),
(6, 1, 249.95, 'cancelled', 'Dwerfve\r\nvewrverw\r\nhtreh, verv jyturjtrj\r\nCanada', 'Dwerfve\r\nvewrverw\r\nhtreh, verv jyturjtrj\r\nCanada', 'paypal', '', '2025-05-03 12:49:01', '2025-05-03 13:07:56');

-- --------------------------------------------------------

--
-- Struttura della tabella `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 4, 5, 12.99),
(2, 2, 3, 1, 19.99),
(3, 3, 1, 1, 49.99),
(4, 4, 4, 1, 12.99),
(5, 5, 3, 6, 19.99),
(6, 6, 1, 5, 49.99);

-- --------------------------------------------------------

--
-- Struttura della tabella `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `weight` decimal(10,2) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `size`, `weight`, `price`, `stock_quantity`, `category_id`) VALUES
(1, 'Professional Pool Ball Set', 'Complete set of 16 professional pool balls, including cue ball', '57.15 mm', 170.00, 34.99, 0, 1),
(2, 'Amateur Pool Ball Set', 'Amateur pool ball set for your everyday games', '57.15 mm', 170.00, 19.99, 30, 1),
(3, 'Kids Pool Ball Set', 'Colorful set of 6 pool balls for children', '57.15 mm', 170.00, 12.99, -6, 1),
(4, 'World Cup Pool Ball Set', 'World Cup grade pool ball set', '57.15 mm', 170.00, 79.99, 0, 1),
(5, 'Tournament Pool Balls', 'High-quality tournament-grade pool balls', '57.15 mm', 170.00, 49.99, 25, 1),
(6, 'High Quality Pool Ball Set', 'High quality pool ball set to elevate your games', '57.15 mm', 170.00, 29.99, 20, 1),
(7, 'Saratoga Bottled Water', 'A premium bottled water for your Ashton Hall like morning routine', '28 cm', 1200.00, 12.99, 100, 2);

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
  `image_order` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_url`, `image_order`) VALUES
(1, 1, 'assets/images/professional-pool-balls-set.jpg', 1),
(2, 2, 'assets/images/amateur-pool-ball-set.jpg', 1),
(3, 3, 'assets/images/kids-pool-ball-set.jpg', 1),
(4, 4, 'assets/images/world-cup-pool-ball-set.jpg', 1),
(5, 5, 'assets/images/tournament-pool-ball-set.jpg', 1),
(6, 6, 'assets/images/high-quality-pool-ball-set.jpg', 1),
(7, 7, 'assets/images/saratoga-bottled-water.jpg', 1),
(8, 7, 'assets/images/saratoga-bottled-water2.jpg', 2);

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
  `country` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `first_name`, `last_name`, `address`, `city`, `state`, `country`, `phone`) VALUES
(1, 'Belinga', 'ssssafwgfher@jnjn.bhb', '$2y$10$iIsOEfBCyDwmubsJQXoi8OOXZHSYnH8WulC.HcjAv8wue8YzxfqYC', 'Mattia', 'Belingheri', NULL, NULL, NULL, NULL, NULL),
(2, 'nigga', 'nigga@niggamail.com', '$2y$10$TiS322uyuvfMGwLJOI8GfOhXNt2jAxOxTzSKMQeiorDyc39zaV.XS', 'nigga', 'nigga', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
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
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_stock` (`stock_quantity`);

--
-- Indici per le tabelle `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_product_images_order` (`product_id`,`image_order`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT per la tabella `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT per la tabella `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT per la tabella `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT per la tabella `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT per la tabella `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT per la tabella `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- Limiti per la tabella `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `fk_wishlist_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
