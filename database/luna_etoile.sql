-- Celesteà Zy Database Schema Backup (Modular Component)
-- Target DB: luna_etoile

CREATE DATABASE IF NOT EXISTS `luna_etoile` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `luna_etoile`;

-- Drop tables in order of dependency to ensure clean rebuild
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `newsletter`;
DROP TABLE IF EXISTS `users`;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(191) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) DEFAULT 'customer',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Dumping data for table `users`
-- --------------------------------------------------------

-- admin -> admin123
-- customer@celesteazy.com -> password123
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'Celesteà Zy Admin', 'admin', '$2y$10$r8y96ybdebLXuo1Q3TURoO00Td1QPSbGxRnNEmmrCtV863i0I6H6.', 'admin'),
(2, 'Celestial Customer', 'customer@celesteazy.com', '$2y$10$HCpBTGoNxyGt5mGDdxbbbOTnbvF2vt1ndLf9KJOJz3NRjDLvI7MRi', 'customer');

-- --------------------------------------------------------
-- Table structure for table `products`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL UNIQUE,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `subtitle` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `top_notes` varchar(255) NOT NULL,
  `heart_notes` varchar(255) NOT NULL,
  `base_notes` varchar(255) NOT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_bestseller` tinyint(1) DEFAULT 0,
  `badge` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Dumping data for table `products`
-- --------------------------------------------------------

INSERT INTO `products` (`id`, `name`, `slug`, `price`, `image_url`, `subtitle`, `description`, `top_notes`, `heart_notes`, `base_notes`, `is_featured`, `is_bestseller`, `badge`) VALUES
(1, 'Noir Éclipse', 'noir-eclipse', '4800.00', 'https://images.unsplash.com/photo-1541643600914-78b084683601?q=80&w=1200&auto=format&fit=crop', 'Woody & Mysterious Oriental', 'An intense encounter between shadowed velvet fruits, raw warm amber, and aged premium smoky woods.', 'Spiced Black Cherry, Saffron', 'Midnight Orchid, Velvet Rose', 'Warm Ambergris, Smoky Cedarwood', 1, 0, NULL),
(2, 'Or Blanc', 'or-blanc', '5200.00', 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?q=80&w=1200&auto=format&fit=crop', 'Fresh Floral Aldehyde', 'A bright, radiant glow of white blossoms shimmering against crystal vanilla musk under stardust.', 'Aldehydic Accord, Calabrian Bergamot', 'White Jasmine Bloom, Lily of the Valley', 'Vanilla Pod, Soft White Musk', 1, 0, NULL),
(3, 'Soleil de Nuit', 'soleil-de-nuit', '4950.00', 'https://images.unsplash.com/photo-1617897903246-719242758050?q=80&w=1200&auto=format&fit=crop', 'Rich Warm Oud Saffron', 'A heavy sensual dream of gold-dusted spices, crimson rose petals, and midnight tobacco.', 'Pink Pepper, Cardamom Seed', 'Black Fig, Crimson Rose Petals', 'Sandalwood, Tobacco Accord', 1, 0, NULL),
(4, 'Celesteà Intense', 'celestea-intense', '5400.00', 'https://images.unsplash.com/photo-1523293182086-7651a899d37f?q=80&w=1200&auto=format&fit=crop', 'Midnight Rose & Intense Patchouli', 'An absolute masterwork capture of night-blooming dark roses, dry vetiver, and premium heavy patchouli resin.', 'Wild Raspberry, Cardamom', 'Grandiflorum Jasmine, Midnight Rose', 'Indonesian Patchouli, Tonka Bean', 0, 1, 'Signature'),
(5, 'Mystère de Nuit', 'mystere-de-nuit', '4990.00', 'https://images.unsplash.com/photo-1616949755610-8c9bbc08f138?q=80&w=1200&auto=format&fit=crop', 'Smoked Cardamom & Velvet Cashmere', 'A quiet whispers trace of dark vanilla pods, aged incense smoke, and cashmere woods wrapping the skin.', 'Olibanum incense, Pink Pepper', 'Soft Cashmere Woods, Cypriol oil', 'Madagascar Vanilla Pod, Vetiver', 0, 1, 'Trending'),
(6, 'Poudre de Satin', 'poudre-de-satin', '4600.00', 'https://images.unsplash.com/photo-1585218356057-dc0e8d3558bb?q=80&w=1200&auto=format&fit=crop', 'Creamy Powdered Vanilla & Musk', 'Satin sheets captured in olfactory beauty. Soft iris petals blended with warm roasted almonds and clean cedar.', 'Warm Almond, White Iris', 'Helicotrope, Mimosa Blossoms', 'Sandalwood, Clean Musk Accord', 0, 1, 'Vintage'),
(7, 'Rose Impériale', 'rose-imperiale', '5100.00', 'https://images.unsplash.com/photo-1547887537-6158d64c35b3?q=80&w=1200&auto=format&fit=crop', 'Deep Damascus Rose & Golden Amber', 'Royal amber streams flowing around majestic hand-pressed Damascus rose oil and dry agarwood dust.', 'Coriander, Honey Glaze', 'Damascus Rose Absolute, Cinnamon', 'Golden Baltic Amber, Somalian Myrrh', 0, 1, 'Rare Edition');

-- --------------------------------------------------------
-- Table structure for table `newsletter`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `newsletter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(191) NOT NULL UNIQUE,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `orders`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `shipping_price` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `order_items`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
