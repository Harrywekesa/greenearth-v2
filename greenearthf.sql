-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: greenearth
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `badges`
--

DROP TABLE IF EXISTS `badges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `badges` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `icon` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `points_required` int DEFAULT '0',
  `criteria` text COLLATE utf8mb4_general_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `badges`
--

LOCK TABLES `badges` WRITE;
/*!40000 ALTER TABLE `badges` DISABLE KEYS */;
INSERT INTO `badges` VALUES (1,'First Tree','Planted your first tree',NULL,10,'{\"type\":\"tree_planted\",\"count\":1}',1,'2025-09-23 18:50:30','2025-09-23 18:50:30'),(2,'Tree Planter','Planted 10 trees',NULL,50,'{\"type\":\"tree_planted\",\"count\":10}',1,'2025-09-23 18:50:30','2025-09-23 18:50:30'),(3,'Eco Warrior','Attended 5 events',NULL,75,'{\"type\":\"event_attended\",\"count\":5}',1,'2025-09-23 18:50:30','2025-09-23 18:50:30'),(4,'Generous Donor','Made a donation',NULL,100,'{\"type\":\"donation\",\"count\":1}',1,'2025-09-23 18:50:30','2025-09-23 18:50:30'),(5,'Top Volunteer','Attended 10 events',NULL,150,'{\"type\":\"event_attended\",\"count\":10}',1,'2025-09-23 18:50:30','2025-09-23 18:50:30');
/*!40000 ALTER TABLE `badges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blog_posts`
--

DROP TABLE IF EXISTS `blog_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blog_posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `excerpt` text COLLATE utf8mb4_general_ci,
  `content` longtext COLLATE utf8mb4_general_ci,
  `featured_image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `author_id` int DEFAULT NULL,
  `meta_description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `meta_keywords` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `view_count` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `author_id` (`author_id`),
  CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blog_posts`
--

LOCK TABLES `blog_posts` WRITE;
/*!40000 ALTER TABLE `blog_posts` DISABLE KEYS */;
INSERT INTO `blog_posts` VALUES (1,'The Importance of Indigenous Trees in Kenya','importance-of-indigenous-trees','Discover why indigenous trees are crucial for Kenya\'s ecosystem and how you can help protect them.','<p>Indigenous trees play a vital role in maintaining Kenya\'s biodiversity and ecological balance...</p>',NULL,6,NULL,NULL,1,'2024-05-15 07:00:00',0,'2025-09-23 18:50:33','2025-09-24 08:30:37'),(2,'How to Start Your Own Tree Nursery','how-to-start-tree-nursery','A step-by-step guide to establishing a tree nursery in your community.','<p>Starting a tree nursery is a rewarding way to contribute to environmental conservation...</p>',NULL,7,NULL,NULL,1,'2024-05-20 11:30:00',0,'2025-09-23 18:50:33','2025-09-24 08:30:38'),(3,'Climate Change Impact on Kenyan Ecosystems','climate-change-kenya','Understanding how climate change is affecting Kenya\'s diverse ecosystems and what we can do about it.','<p>Kenya\'s varied climatic zones are experiencing significant changes due to global warming...</p>',NULL,8,NULL,NULL,1,'2024-05-25 06:15:00',0,'2025-09-23 18:50:33','2025-09-24 08:30:38');
/*!40000 ALTER TABLE `blog_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `climatic_zones`
--

DROP TABLE IF EXISTS `climatic_zones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `climatic_zones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `rainfall_pattern` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `soil_type` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `vegetation_type` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `climatic_zones`
--

LOCK TABLES `climatic_zones` WRITE;
/*!40000 ALTER TABLE `climatic_zones` DISABLE KEYS */;
INSERT INTO `climatic_zones` VALUES (1,'Coastal Plain','coastal-plain','The coastal plain is characterized by hot and humid conditions with high rainfall throughout the year. Temperatures range from 25-30','Bimodal','Sandy','Coastal Forest','68d382d673f06.jpeg',1,'2025-09-23 18:50:30','2025-09-24 05:34:14'),(2,'Lowland','lowland','The lowland areas experience moderate temperatures with seasonal rainfall patterns. Annual rainfall ranges from 600-1200mm with temperatures between 20-28','Unimodal','Clay','Savanna Grassland',NULL,1,'2025-09-23 18:50:30','2025-09-23 18:50:30'),(3,'Highland','highland','Highland areas have cooler temperatures due to elevation, with bimodal rainfall patterns. Temperatures range from 15-25','Bimodal','Volcanic','Montane Forest',NULL,1,'2025-09-23 18:50:30','2025-09-23 18:50:30'),(4,'Arid and Semi-Arid Zones (Hot and Dry)','arid-and-semi-arid-zones-hot-and-dry','Location: Northern and eastern Kenya including Turkana, Marsabit, Garissa, and Mandera.\r\n\r\nTemperature: Very hot, often above 35','Semi-arid','Sandy, stony, and shallow soils, often with poor water retention','Sparse cover with thorn bushes, acacia trees, cactus, and seasonal grasses.','68d380fd85be1.jpeg',1,'2025-09-24 05:17:04','2025-09-24 05:26:21');
/*!40000 ALTER TABLE `climatic_zones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `event_date` datetime DEFAULT NULL,
  `location` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `initiative_id` int DEFAULT NULL,
  `max_volunteers` int DEFAULT '50',
  `current_volunteers` int DEFAULT '0',
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('upcoming','ongoing','completed') COLLATE utf8mb4_general_ci DEFAULT 'upcoming',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `initiative_id` (`initiative_id`),
  CONSTRAINT `events_ibfk_1` FOREIGN KEY (`initiative_id`) REFERENCES `initiatives` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (1,'Nairobi Tree Planting Day','nairobi-tree-planting-day','Join us for a community tree planting event in Uhuru Park. We will plant indigenous species to restore the urban forest.','2024-06-15 09:00:00','Uhuru Park, Nairobi',-1.29980000,36.81470000,1,100,45,NULL,'upcoming',1,'2025-09-23 18:50:30','2025-09-23 18:50:30'),(2,'Mangrove Nursery Workshop','mangrove-nursery-workshop','Learn how to propagate and care for mangrove seedlings. This workshop is open to coastal community members.','2024-06-22 10:00:00','Mombasa Marine Park',-4.04350000,39.66820000,2,50,25,NULL,'upcoming',1,'2025-09-23 18:50:30','2025-09-23 18:50:30'),(3,'Lake Victoria Cleanup','lake-victoria-cleanup','Community cleanup and tree planting event along the shores of Lake Victoria to protect the watershed.','2024-06-29 08:00:00','Kisumu Beach',-0.09170000,34.76800000,3,150,75,NULL,'upcoming',1,'2025-09-23 18:50:30','2025-09-23 18:50:30');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `initiatives`
--

DROP TABLE IF EXISTS `initiatives`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `initiatives` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `objectives` text COLLATE utf8mb4_general_ci,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `location` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `partner_id` int DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('upcoming','ongoing','completed') COLLATE utf8mb4_general_ci DEFAULT 'upcoming',
  `target_trees` int DEFAULT '0',
  `planted_trees` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `partner_id` (`partner_id`),
  CONSTRAINT `initiatives_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `initiatives`
--

LOCK TABLES `initiatives` WRITE;
/*!40000 ALTER TABLE `initiatives` DISABLE KEYS */;
INSERT INTO `initiatives` VALUES (1,'Nairobi Reforestation Project','nairobi-reforestation-project','A comprehensive tree planting initiative aimed at restoring green cover in Nairobi County through community participation and education.','Plant 10,000 indigenous trees, engage 500 community volunteers, establish 5 tree nurseries','2024-06-01','2024-12-31','Nairobi County',-1.29210000,36.82190000,1,NULL,'ongoing',10000,3500,1,'2025-09-23 18:50:30','2025-09-23 18:50:30'),(2,'Coastal Mangrove Restoration','coastal-mangrove-restoration','Restoring mangrove ecosystems along the Kenyan coast to protect against erosion and support marine biodiversity.','Restore 50 hectares of mangrove forest, engage coastal communities, monitor ecosystem health','2024-07-01','2025-06-30','Mombasa County',-4.04350000,39.66820000,2,NULL,'upcoming',25000,0,1,'2025-09-23 18:50:30','2025-09-23 18:50:30'),(3,'Lake Victoria Watershed Protection','lake-victoria-watershed-protection','Protecting the Lake Victoria watershed through riparian buffer planting and sustainable land management practices.','Establish 200km of riparian buffers, train 200 farmers, improve water quality','2024-08-01','2025-07-31','Kisumu County',-0.09170000,34.76800000,3,'68d38b614043b.jpeg','',50000,23,1,'2025-09-23 18:50:30','2025-09-24 06:10:41');
/*!40000 ALTER TABLE `initiatives` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marquee_images`
--

DROP TABLE IF EXISTS `marquee_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marquee_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `sort_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marquee_images`
--

LOCK TABLES `marquee_images` WRITE;
/*!40000 ALTER TABLE `marquee_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `marquee_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `newsletter_subscribers`
--

DROP TABLE IF EXISTS `newsletter_subscribers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `newsletter_subscribers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_subscribed` tinyint(1) DEFAULT '1',
  `subscribed_at` timestamp NULL DEFAULT NULL,
  `unsubscribed_at` timestamp NULL DEFAULT NULL,
  `subscription_token` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `subscription_token` (`subscription_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `newsletter_subscribers`
--

LOCK TABLES `newsletter_subscribers` WRITE;
/*!40000 ALTER TABLE `newsletter_subscribers` DISABLE KEYS */;
/*!40000 ALTER TABLE `newsletter_subscribers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,4,1,800.00,800.00,'2025-09-23 19:26:45','2025-09-23 19:26:45');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `order_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `payment_status` enum('pending','paid','failed') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `payment_method` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `transaction_id` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `shipping_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `shipping_phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `shipping_email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `shipping_county` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `shipping_subcounty` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `shipping_address` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,4,'ORD-68D2F4759AA1D',800.00,'processing','paid','mpesa','MPESA68D2F4A1BF7D2','0','+254741947264','harrisonwekesa09@gmail.com','Nairobi','Karen','foor','2025-09-23 19:26:45','2025-09-24 06:41:41');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `partners`
--

DROP TABLE IF EXISTS `partners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `partners` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `website` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `sponsorship_level` enum('bronze','silver','gold','platinum') COLLATE utf8mb4_general_ci DEFAULT 'bronze',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `partners`
--

LOCK TABLES `partners` WRITE;
/*!40000 ALTER TABLE `partners` DISABLE KEYS */;
INSERT INTO `partners` VALUES (1,'World Wildlife Fund Kenya','info@wwfkenya.org','+254 20 1234567','Westlands, Nairobi','https://www.wwfkenya.org',NULL,'WWF works to sustain the natural world for the benefit of people and wildlife, collaborating with local communities to conserve the richness of nature.','platinum',1,'2025-09-23 18:50:30','2025-09-23 18:50:30'),(2,'United Nations Environment Programme','unep@unep.org','+254 20 9876543','Nairobi','https://www.unep.org',NULL,'UNEP is the leading global environmental authority that sets the global environmental agenda and promotes the coherent implementation of the environmental dimension of sustainable development.','gold',1,'2025-09-23 18:50:30','2025-09-23 18:50:30'),(3,'Kenya Forest Research Institute','info@kefri.org','+254 20 4567890','Nairobi','https://www.kefri.org',NULL,'KEFRI is a semi-autonomous government research institution under the Ministry of Environment and Natural Resources.','gold',1,'2025-09-23 18:50:30','2025-09-23 18:50:30'),(4,'Kenya Agricultural Research and Livestock Organization (KARLO)','admin@KARLO.AC.KE','0725208128','21312','https://www.karlo.ac.ke','68d386ccd41cb.png','KALRO is the premier agricultural and livestock research organization in Kenya that provides leadership and demand-driven solutions to agricultural challenges. KALRO promotes, streamline, co-ordinate and regulate research in crops, livestock, genetic resources, biotechnology and animal diseases. It also expedite equitable access to research information, resources and technologies and promote the application of research findings and developed technologies in the field of agriculture and livestock.','platinum',1,'2025-09-24 05:51:08','2025-09-24 05:51:08');
/*!40000 ALTER TABLE `partners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int DEFAULT '0',
  `category` enum('seedlings','manure','pesticides','tools') COLLATE utf8mb4_general_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `planting_tips` text COLLATE utf8mb4_general_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Indigenous Seedlings Pack','indigenous-seedlings-pack','A pack of 10 indigenous tree seedlings including Acacia, Croton, and Markhamia species.',500.00,500,'seedlings',NULL,'Plant during the rainy season. Water regularly for the first 3 months. Protect from livestock.',1,'2025-09-23 18:50:30','2025-09-23 18:50:30'),(2,'Organic Manure','organic-manure','High-quality organic manure made from decomposed plant matter and animal waste.',300.00,200,'manure',NULL,'Apply 2kg per tree monthly during growing season. Mix with soil before planting.',1,'2025-09-23 18:50:30','2025-09-23 18:50:30'),(3,'Neem Tree Seedlings','neem-tree-seedlings','Neem (Azadirachta indica) seedlings known for their pest-repelling properties and drought resistance.',150.00,300,'seedlings','68d390bedc652.jpeg','Plant in well-drained soil. Requires minimal water once established. Prune regularly.',1,'2025-09-23 18:50:30','2025-09-24 06:33:34'),(4,'Eco-Friendly Pesticide','eco-friendly-pesticide','Natural pesticide made from neem oil and other plant extracts. Safe for beneficial insects.',800.00,99,'pesticides',NULL,'Apply early morning or late evening. Avoid spraying during flowering periods.',1,'2025-09-23 18:50:30','2025-09-23 19:26:45'),(5,'Bimunor&#039;s Oak','bimunor039s-oak','The Bimundors Oak (Quercus ',340.00,10000,'seedlings','68d390652fdfe.webp','o plant a &quot;Bimundors Oak,&quot; start by collecting and testing acorns to find viable ones that sink in water. Plant the acorns in pots with good drainage, simulating winter conditions (cold stratification) if the species requires it, and then grow the seedlings for several months. When the sapling is 1-2 feet tall, or outgrows its pot, transplant it to a permanent location, digging a hole twice as wide as the root ball. Ensure the top of the root ball is level with or slightly higher than the surrounding soil, water well, and protect the young tree from herbivores with protective caging.',1,'2025-09-24 06:32:05','2025-09-24 06:32:05');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `key` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `value` text COLLATE utf8mb4_general_ci,
  `type` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'string',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'site_name','GreenEarth','string','2025-09-23 18:50:30','2025-09-23 18:50:30'),(2,'site_description','Promoting environmental care and tree-planting initiatives','string','2025-09-23 18:50:30','2025-09-23 18:50:30'),(3,'contact_email','info@greenearth.org','string','2025-09-23 18:50:30','2025-09-23 18:50:30'),(4,'contact_phone','+254 700 000 000','string','2025-09-23 18:50:30','2025-09-23 18:50:30'),(5,'social_facebook','https://facebook.com/greenearth','string','2025-09-23 18:50:30','2025-09-23 18:50:30'),(6,'social_twitter','https://twitter.com/greenearth','string','2025-09-23 18:50:30','2025-09-23 18:50:30'),(7,'social_instagram','https://instagram.com/greenearth','string','2025-09-23 18:50:30','2025-09-23 18:50:30'),(8,'trees_planted','12500','string','2025-09-23 18:50:30','2025-09-23 18:50:30'),(9,'volunteers_count','850','string','2025-09-23 18:50:30','2025-09-23 18:50:30'),(10,'partners_count','24','string','2025-09-23 18:50:30','2025-09-23 18:50:30');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tree_plantings`
--

DROP TABLE IF EXISTS `tree_plantings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tree_plantings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `event_id` int DEFAULT NULL,
  `initiative_id` int DEFAULT NULL,
  `tree_type` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `planting_date` date DEFAULT NULL,
  `status` enum('planted','verified','dead') COLLATE utf8mb4_general_ci DEFAULT 'planted',
  `photo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `event_id` (`event_id`),
  KEY `initiative_id` (`initiative_id`),
  CONSTRAINT `tree_plantings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tree_plantings_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tree_plantings_ibfk_3` FOREIGN KEY (`initiative_id`) REFERENCES `initiatives` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tree_plantings`
--

LOCK TABLES `tree_plantings` WRITE;
/*!40000 ALTER TABLE `tree_plantings` DISABLE KEYS */;
/*!40000 ALTER TABLE `tree_plantings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_badges`
--

DROP TABLE IF EXISTS `user_badges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_badges` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `badge_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_badge` (`user_id`,`badge_id`),
  KEY `badge_id` (`badge_id`),
  CONSTRAINT `user_badges_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_badges_ibfk_2` FOREIGN KEY (`badge_id`) REFERENCES `badges` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_badges`
--

LOCK TABLES `user_badges` WRITE;
/*!40000 ALTER TABLE `user_badges` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_badges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `county` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subcounty` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` enum('admin','partner','user','author') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'user',
  `is_active` tinyint(1) DEFAULT '1',
  `email_verified` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (4,'Harrison Wekesa','harrisonwekesa09@gmail.com','+254741947264','$2y$10$RzvBwA62tlW3dmrO9oqPhessQrXLZCC8VJlvhh0B1n2ZC8qsRXu1u','Trans-Nzoia','kwanza','user',1,0,'2025-09-23 19:26:21','2025-09-23 19:26:21'),(5,'admin','admin@greenearth.com','+254741947265','$2y$10$23JMPAIpdsw.msC0SJB3suWmnKUEjT0nFTnhPsU4c9S8SVPWdeyyC','Trans-Nzoia','kwanza','admin',1,1,'2025-09-23 20:44:29','2025-09-23 20:47:45'),(6,'John Mwangi','john.mwangi@example.com','+254700000001','$2y$10$abcdefghijklmnopqrstuv','Nairobi','Westlands','author',1,1,'2025-09-24 08:29:17','2025-09-24 08:29:17'),(7,'Mary Wanjiku','mary.wanjiku@example.com','+254700000002','$2y$10$abcdefghijklmnopqrstuv','Kiambu','Ruiru','author',1,1,'2025-09-24 08:29:17','2025-09-24 08:29:17'),(8,'Peter Otieno','peter.otieno@example.com','+254700000003','$2y$10$abcdefghijklmnopqrstuv','Kisumu','Kisumu East','author',1,1,'2025-09-24 08:29:17','2025-09-24 08:29:17');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `volunteer_signups`
--

DROP TABLE IF EXISTS `volunteer_signups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `volunteer_signups` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `event_id` int DEFAULT NULL,
  `motivation` text COLLATE utf8mb4_general_ci,
  `status` enum('pending','confirmed','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_signup` (`user_id`,`event_id`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `volunteer_signups_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `volunteer_signups_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `volunteer_signups`
--

LOCK TABLES `volunteer_signups` WRITE;
/*!40000 ALTER TABLE `volunteer_signups` DISABLE KEYS */;
/*!40000 ALTER TABLE `volunteer_signups` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-24 11:33:03
