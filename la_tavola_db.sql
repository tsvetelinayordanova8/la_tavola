-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 29, 2025 at 08:37 AM
-- Server version: 8.0.31
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `la_tavola_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
CREATE TABLE IF NOT EXISTS `cart_items` (
  `CartItemID` int NOT NULL AUTO_INCREMENT,
  `UserID` int NOT NULL,
  `ProductID` int NOT NULL,
  `Quantity` int NOT NULL DEFAULT '1',
  `DateAdded` datetime DEFAULT CURRENT_TIMESTAMP,
  `ProductType` enum('main','lunch') DEFAULT 'main',
  PRIMARY KEY (`CartItemID`),
  KEY `UserID` (`UserID`),
  KEY `ProductID` (`ProductID`)
) ENGINE=MyISAM AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`CartItemID`, `UserID`, `ProductID`, `Quantity`, `DateAdded`, `ProductType`) VALUES
(72, 10, 15, 1, '2025-06-11 20:22:59', 'lunch');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `CategoryID` int NOT NULL AUTO_INCREMENT,
  `CategoryName` varchar(100) NOT NULL,
  PRIMARY KEY (`CategoryID`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`CategoryID`, `CategoryName`) VALUES
(1, 'Салати'),
(2, 'Предястия'),
(3, 'Основни'),
(4, 'Пица'),
(5, 'Паста'),
(6, 'Питки'),
(7, 'Десерти'),
(8, 'Сезонни предложения');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

DROP TABLE IF EXISTS `favorites`;
CREATE TABLE IF NOT EXISTS `favorites` (
  `FavoriteID` int NOT NULL AUTO_INCREMENT,
  `UserID` int NOT NULL,
  `ProductID` int NOT NULL,
  `AddedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`FavoriteID`),
  UNIQUE KEY `UserID` (`UserID`,`ProductID`),
  KEY `FK_Favorites_Products` (`ProductID`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`FavoriteID`, `UserID`, `ProductID`, `AddedAt`) VALUES
(4, 7, 11, '2025-05-25 10:53:27'),
(5, 8, 12, '2025-05-25 10:53:41'),
(6, 8, 13, '2025-05-25 10:55:39'),
(13, 9, 51, '2025-06-03 16:00:43'),
(15, 9, 1, '2025-06-04 17:53:54'),
(24, 21, 41, '2025-06-07 13:08:41');

-- --------------------------------------------------------

--
-- Table structure for table `lunch_menu_products`
--

DROP TABLE IF EXISTS `lunch_menu_products`;
CREATE TABLE IF NOT EXISTS `lunch_menu_products` (
  `LunchProductID` int NOT NULL AUTO_INCREMENT,
  `ProductName` varchar(255) NOT NULL,
  `Description` text,
  `Weight` decimal(10,2) DEFAULT NULL,
  `Price` decimal(10,2) DEFAULT NULL,
  `CategoryID` int DEFAULT NULL,
  `IsActive` tinyint(1) DEFAULT '1',
  `PhotoSource` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`LunchProductID`),
  KEY `CategoryID` (`CategoryID`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lunch_menu_products`
--

INSERT INTO `lunch_menu_products` (`LunchProductID`, `ProductName`, `Description`, `Weight`, `Price`, `CategoryID`, `IsActive`, `PhotoSource`) VALUES
(15, 'Салата с рукола и пармезан', 'Рукола, чери домати, люспи от пармезан, кедрови ядки, лимонов дресинг', '280.00', '11.50', 1, 1, 'Sources/no_photo_sign.png'),
(14, 'Салта Капрезе', 'Пресни домати, моцарела ди буфала, пресен босилек, зехтин екстра върджин и балсамов оцет', '300.00', '12.40', 1, 1, 'Sources/no_photo_sign.png'),
(16, 'Средиземноморска салата', 'Айсберг, краставици, маслини каламата, червен лук, сирене фета, винегрет', '300.00', '9.80', 1, 1, 'Sources/no_photo_sign.png'),
(17, 'Пене арабиата', 'Пене в пикантен доматен сос с чесън и люта чушка', '250.00', '11.20', 5, 1, 'Sources/no_photo_sign.png'),
(18, 'Спагети Карбонара', 'Прясна паста, яйчен сос, бекон, пармезан и черен пипер.\n\n', '300.00', '10.50', 5, 1, 'Sources/no_photo_sign.png'),
(19, 'Талиатели с гъби и сметана', 'Прясна паста с гъби, сметана, чесън и мащерка', '260.00', '7.50', 5, 1, 'Sources/no_photo_sign.png'),
(20, 'Маргарита', 'Доматен сос, моцарела и босилек', '330.00', '7.80', 4, 1, 'Sources/no_photo_sign.png'),
(21, 'Капричоза', 'Доматен сос, моцарела, шунка, гъби, артишок, маслини', '330.00', '8.00', 4, 1, 'Sources/no_photo_sign.png'),
(22, 'Амстердам', 'Доматен сос, моцарела, прошуто крудо, рукола и пармезан', '330.00', '8.20', 4, 1, 'Sources/no_photo_sign.png'),
(23, 'Пиле Пармиджана', 'Панирано пилешко филе с доматен сос и моцарела, запечено на фурна. Сервира се с гарнитура от зеленчуци', '350.00', '12.20', 3, 1, 'Sources/no_photo_sign.png'),
(24, 'Телешко Осо Буко', 'Телешки джолан, бавно готвен в бяло вино и зеленчуци, поднесен с ризото Миланезе', '300.00', '15.40', 3, 1, 'Sources/no_photo_sign.png'),
(25, 'Тирамису', 'Класически италиански десерт с маскарпоне, кафе и бишкоти, поръсен с какао', '120.00', '5.70', 7, 1, 'Sources/no_photo_sign.png'),
(26, 'Панакота с ягодов сос', 'Кремообразна ванилова панакота, поднесена с домашен ягодов сос', '100.00', '7.50', 7, 1, 'Sources/no_photo_sign.png'),
(27, 'Каноли със сладък рикота крем', 'Хрупкави тръбички, пълнени с рикота, захар и капки шоколад', '120.00', '0.50', 7, 1, 'Sources/no_photo_sign.png');

-- --------------------------------------------------------

--
-- Table structure for table `orderitems`
--

DROP TABLE IF EXISTS `orderitems`;
CREATE TABLE IF NOT EXISTS `orderitems` (
  `OrderItemID` int NOT NULL AUTO_INCREMENT,
  `OrderID` int DEFAULT NULL,
  `ProductID` int DEFAULT NULL,
  `Quantity` int NOT NULL,
  `UnitPrice` decimal(10,2) NOT NULL,
  `ProductType` enum('main','lunch') NOT NULL DEFAULT 'main',
  PRIMARY KEY (`OrderItemID`),
  KEY `OrderID` (`OrderID`),
  KEY `ProductID` (`ProductID`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orderitems`
--

INSERT INTO `orderitems` (`OrderItemID`, `OrderID`, `ProductID`, `Quantity`, `UnitPrice`, `ProductType`) VALUES
(1, 1, 13, 2, '50.00', 'main'),
(2, 1, 4, 1, '60.00', 'main'),
(3, 2, 13, 1, '17.60', 'main'),
(4, 3, 1, 2, '15.00', 'main'),
(5, 4, 1, 1, '15.00', 'main'),
(21, 11, 23, 2, '29.00', 'main'),
(20, 10, 5, 15, '14.70', 'main'),
(30, 15, 13, 1, '50.00', 'lunch'),
(22, 11, 34, 2, '8.50', 'main'),
(34, 19, 14, 2, '15.00', 'main'),
(29, 14, 13, 1, '50.00', 'lunch'),
(35, 19, 29, 3, '19.50', 'main'),
(33, 18, 15, 1, '11.50', 'lunch'),
(32, 17, 5, 1, '14.70', 'main'),
(31, 16, 13, 1, '50.00', 'lunch'),
(36, 19, 34, 2, '8.50', 'main'),
(37, 19, 2, 1, '15.60', 'main');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `OrderID` int NOT NULL AUTO_INCREMENT,
  `CustomerID` int DEFAULT NULL,
  `OrderDate` datetime DEFAULT CURRENT_TIMESTAMP,
  `TotalAmount` decimal(10,2) DEFAULT NULL,
  `Status` enum('Pending','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
  `DeliveryAddress` text,
  `Phone` varchar(20) DEFAULT NULL,
  `Comment` text,
  PRIMARY KEY (`OrderID`),
  KEY `CustomerID` (`CustomerID`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`OrderID`, `CustomerID`, `OrderDate`, `TotalAmount`, `Status`, `DeliveryAddress`, `Phone`, `Comment`) VALUES
(1, 9, '2025-05-25 11:56:20', '160.00', 'Delivered', NULL, NULL, NULL),
(2, 9, '2025-06-01 10:05:09', '17.60', 'Pending', 'Хан телериг 2', '0897742085', 'test'),
(3, 9, '2025-06-01 10:06:15', '30.00', 'Shipped', 'Хан телериг 2', '0897742085', 'test2'),
(4, 9, '2025-06-01 12:33:38', '15.00', 'Pending', 'Хан телериг 2', '0897742085', 'тестттт'),
(10, 10, '2025-06-06 22:12:54', '220.50', 'Cancelled', 'Хан телериг 5', '0892325657', 'mnogo chesaaaaaan'),
(11, 10, '2025-06-06 22:17:24', '75.00', 'Shipped', 'Хан телериг 2', '0897742085', 'mnogo krenvirshiiiii'),
(14, 10, '2025-06-06 22:50:41', '50.00', 'Pending', '1', '1', '1'),
(15, 10, '2025-06-06 22:51:16', '50.00', 'Pending', '1', '1', '1'),
(16, 10, '2025-06-06 22:52:22', '50.00', 'Pending', '1', '1', '1'),
(17, 10, '2025-06-06 22:52:50', '14.70', 'Pending', '1', '1', '1'),
(18, 10, '2025-06-06 23:36:15', '11.50', 'Delivered', '2', '2', '2'),
(19, 21, '2025-06-07 16:00:11', '121.10', 'Pending', 'Kuklensko shose 11', '0899999999', '');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `ProductID` int NOT NULL AUTO_INCREMENT,
  `ProductName` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Weight` int DEFAULT NULL,
  `Price` decimal(10,2) DEFAULT NULL,
  `Description` text,
  `CategoryID` int DEFAULT NULL,
  `PhotoSource` text NOT NULL,
  PRIMARY KEY (`ProductID`),
  KEY `CategoryID` (`CategoryID`)
) ENGINE=MyISAM AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`ProductID`, `ProductName`, `Weight`, `Price`, `Description`, `CategoryID`, `PhotoSource`) VALUES
(1, 'Салата пиле и прошуто', 300, '15.00', 'Накъсано пилешко с авокадо, домат и микс от зелени салатни листа, поднесени със сос „Цезар“, пармезан и хрупкаво прошуто.', 1, 'Sources/Menu/Salads/1.jpg'),
(2, 'Салата със сьомга и авокадо', 300, '15.60', 'Филе от сьомга, поднесено върху прясно авокадо, малки домати „плам“, микс от салатни листа и завършено с класически сос „Цезар“.', 1, 'Sources/Menu/Salads/2.jpg'),
(3, 'Зука салата', 320, '17.50', 'Печена тиква, смесена с пресни листа, микс от зърнени култури, грах, кейл, тиквени семки, червен пипер и лютив хумус. Завършено с балсамов глейз.\r\n', 1, 'Sources/Menu/Salads/3.jpg'),
(4, 'Бурата капрезе', 250, '13.50', 'Цяла топка прясна буррата моцарела, с малки домати „плам“, босилек и балсамов сос.', 2, 'Sources/Menu/Starters/1.jpg'),
(5, 'Чеснови кюфтенца', 300, '14.70', 'Печени свински кюфтенца с нашия огнен сос „Ла Бомба“, с пушена моцарела.', 2, 'Sources/Menu/Starters/2.jpg'),
(6, 'Пилешки спедини', 240, '12.50', 'Пилешко филе с чесън и слънчеви домати, печено на шиш, поднесено със сос от лютив мед „Рокито“ и дики чеснов айоли. Не забравяйте да изцедите лимона!', 2, 'Sources/Menu/Starters/3.jpg'),
(7, 'Средиземноморски калмари', 150, '12.00', 'Хрупкави калмари, поднесени с чеснов айоли.', 2, 'Sources/Menu/Starters/4.jpg'),
(8, 'Поло Фрити', 280, '15.00', 'Панирани пилешки филета, поднесени с пармезан, лимон и чеснов айоли.\r\n6 Кралски скариди с чеснов сос - Кралски скариди, мариновани в хариса, печени на шиш с чеснов сос за заливане.\r\n', 2, 'Sources/Menu/Starters/5.jpg'),
(10, 'Пилешко Калабрезе', 320, '18.00', 'Пилешко филе, мариновано в хариса, печено с картофи във фурна в сос от пипер, домати и пикантна „ндуя“. Поднесено с маскарпоне и запален чили.', 3, 'Sources/Menu/Main_dishes/1.jpg'),
(11, 'Говежди брикет', 270, '22.50', 'Бавно готвено говеждо, приготвено с вино Кианти, малки лукчета и картофи. Всичко поднесено в нашата тестена купа с хрупкава скаморца и салвия.', 3, 'Sources/Menu/Main_dishes/2.jpg'),
(12, 'Пиле Миланезе', 300, '19.50', 'Панирано пилешко филе с хрупкави картофи, зелени бобчета, кейл и спанак.\r\n4 Средиземноморски морски костур - Поднесено с топла средиземноморска салата от зеленчуци с лентички тиквички, печен пипер, зелени бобчета, артишок, черни маслини, салатни картофи и слънчеви домати.', 3, 'Sources/Menu/Main_dishes/3.jpg'),
(14, 'Ризото с гъби', 250, '15.00', ' Богато и кремаво ризото с маскарпоне, сос от гъби Порто Белло и Порчини, завършено с хрупкава салвия и сирене Ризиерва.', 3, 'Sources/Menu/Main_dishes/5.jpg'),
(15, 'Ризото със сьомга', 290, '16.00', 'Нашето богато и кремаво ризото, поднесено с панфрид филе от сьомга, генуезе песто, пресен спанак и лентички тиквички. Завършено с изцеден лимон.\r\n\r\n', 3, 'Sources/Menu/Main_dishes/6.jpg'),
(16, 'Маргарита', 330, '16.00', 'Доматен сос, моцарела и пресен босилек', 4, 'Sources/Menu/Pizza/1.jpg'),
(13, 'Средиземноморски морски костур', 290, '17.60', 'Поднесено с топла средиземноморска салата от зеленчуци с лентички тиквички, печен пипер, зелени бобчета, артишок, черни маслини, салатни картофи и слънчеви домати.\r\n', 3, 'Sources/Menu/Main_dishes/4.jpg'),
(9, 'Кралски скариди с чеснов сос', 250, '16.50', ' Кралски скариди, мариновани в хариса, печени на шиш с чеснов сос за заливане.\r\n', 2, 'Sources/Menu/Starters/6.jpg'),
(17, ' Класическа пеперони Кампана', 330, '17.00', 'Доматен сос, пеперони, шунка, гъби, моцарела и розмарин\r\n', 4, 'Sources/Menu/Pizza/2.jpg'),
(18, 'Рустика', 330, '18.00', 'Доматен сос, Wagyu и говеждо, перли Roquito, захаросани зелени халапеньос, печен червен чили, хариса, моцарела и пикантен мед с Roquito. Завършено с пармезан и цяла топка свежа буррата.\r\n', 4, 'Sources/Menu/Pizza/3.jpg'),
(19, 'Дон Карлос', 330, '19.00', 'Доматен сос, пилешко филе с хариса, пеперони и разкъсано свинско скилидкови кюфтета. Завършено с моцарела, пикантни Roquito чили и розмарин\r\n', 4, 'Sources/Menu/Main_dishes/4.jpg'),
(20, 'Поло', 330, '18.00', 'Доматен сос, печено пилешко филе, слънчеви домати, пармезан и генуезе песто\r\n', 4, 'Sources/Menu/Pizza/5.jpg'),
(21, 'Капрезе', 330, '16.50', 'Нашият вариант на пица Капрезе с разкъсана моцарела Фиор ди Лате, червени и оранжеви малки домати „плам“, червен лук и балсамов дресинг.\r\n', 4, 'Sources/Menu/Pizza/6.jpg'),
(22, 'Пица с пикантни скариди с хариса', 330, '18.50', 'Доамтен сос, кралски скариди, перли от пипер Roquito, лентички тиквички и щипка пикантна хариса\r\n', 4, 'Sources/Menu/Pizza/7.jpg'),
(23, 'Калцоне Карне', 330, '29.00', 'Пълно с месни кюфтета от свинско и чесън, разкъсано пилешко филе с хариса, болонезе, моцарела, пикантни Roquito чили и гъби. С пушен доматен сос отстран', 4, 'Sources/Menu/Pizza/8.jpg'),
(24, 'Калцоне Поло', 330, '18.50', 'Пълно с разкъсано пилешко филе, прошуто, спанак, гъби и моцарела в кремав бешамел сос. С пушен доматен сос отстрани.\r\n', 4, 'Sources/Menu/Pizza/9.jpg'),
(25, 'Говеждо рагу Кианти', 300, '17.80', 'Дърпано говеждо в рагу от вино Кианти и печени домати, завършено с пармезан и хрупкава салвия\r\n', 5, 'Sources/Menu/Pasta/1.jpg'),
(26, 'Арабиата с кюфтенца', 300, '19.50', 'Месни кюфтета от Wagyu, говеждо и моцарела в нашия огнен сос \"Ла Бомба\", завършени с пармезан и чили', 5, 'Sources/Menu/Pasta/2.jpg'),
(27, 'Прошуто Карбонара', 290, '17.50', 'Нашата пет-сирийна карбонара със чедър, маскарпоне, пекорино, регато, Ризиерва и пушена панчета. Завършено с хрупкав прошуто, скаморца и пресни подправки.', 5, 'Sources/Menu/Pasta/3.jpg'),
(28, 'Сицилианска сьомга', 300, '18.90', 'Панфрид филе от сьомга, поднесено със сос от маслено-магданозено масло, свеж лимон и каперси. Завършено с хрупкав кейл.', 5, 'Sources/Menu/Pasta/4.jpg'),
(29, 'Кралски скариди', 300, '19.50', 'Пикантно мариновани кралски скариди, печени на шиш, с кремав сос хариса и малки домати „плам“. Завършено с маскарпоне, печени червени чили чушки и пресни подправки.', 5, 'Sources/Menu/Pasta/5.jpg'),
(30, 'Гъбена буррата', 300, '18.00', 'Гъби ПортоБелло и Порчини в сос от четири сирена, инфузиран с трюфелово масло. Завършено с Ризиерва сирене, хрупкава салвия, пресен босилек и цяла топка буррата.', 5, 'Sources/Menu/Pasta/6.jpg'),
(31, 'Чеснова питка с моцарела\r\n', 150, '6.00', 'Чеснова питка с моцарела\r\n', 6, 'Sources/Menu/Bread/1.jpg'),
(32, 'Питка с кашкавал и чили', 160, '6.20', 'Питка с кашкавал и чили', 6, 'Sources/Menu/Bread/2.jpg'),
(33, 'Питка с карамелизиран лук и чесън', 160, '6.50', 'Питка с карамелизиран лук и чесън\r\n', 6, 'Sources/Menu/Bread/3.jpg'),
(34, 'Моцарелена бомба', 230, '8.50', 'Прясно изпечена питка с чесън, която носи истински удар. Напълнена с разтопена моцарела. Покрита с пикантен мед.\r\n', 6, 'Sources/Menu/Bread/4.jpg'),
(35, 'Бомба Чоризо', 230, '8.50', 'Прясно изпечена питка с чесън, която носи истински удар. Напълнена с чоризо и моцарела. Покрита с пикантен мед .', 6, 'Sources/Menu/Bread/5.jpg'),
(36, 'Тирамису', 150, '7.50', 'Еспресо-наситен блат, слоен с маскарпоне.\r\n', 7, 'Sources/Menu/Desserts/1.jpg'),
(37, 'Брауни солен карамел', 200, '8.50', 'Топъл брауни със солена карамелова сърцевина, топка ванилов гелато и каничка горещ шоколад за заливане.\r\n', 7, 'Sources/Menu/Desserts/2.jpg'),
(38, 'Шоколадов мелт', 180, '8.20', 'Топъл шоколадов пудинг с разтопена сърцевина, поднесен с ванилово гелато.', 7, 'Sources/Menu/Desserts/3.jpg'),
(39, 'Шоколадово джелато', 120, '6.50', 'Три топки домашно шоколадово джелато.', 7, 'Sources/Menu/Desserts/4.jpg'),
(40, 'Ванилово джелато', 120, '6.50', 'Три топки ванилово джелато.', 7, 'Sources/Menu/Desserts/5.jpg'),
(41, 'Солен карамел джелато', 120, '6.50', 'Три топки джелато със солен карамел.', 7, 'Sources/Menu/Desserts/6.jpg'),
(57, 'Фондю от сирена', 150, '8.60', 'Топяща се смес от четири вида сирена, сервирана с нашите специални хлебчета за топене.', 1, 'Sources/Menu/Starters/Fonduta_Formaggi.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `seasonal_products`
--

DROP TABLE IF EXISTS `seasonal_products`;
CREATE TABLE IF NOT EXISTS `seasonal_products` (
  `SeasonalID` int NOT NULL AUTO_INCREMENT,
  `ProductID` int NOT NULL,
  `AddedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`SeasonalID`),
  KEY `FK_Seasonal_Product` (`ProductID`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `seasonal_products`
--

INSERT INTO `seasonal_products` (`SeasonalID`, `ProductID`, `AddedAt`) VALUES
(1, 2, '2025-05-25 16:29:01'),
(4, 5, '2025-05-25 16:36:55'),
(5, 29, '2025-06-06 19:34:42');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `UserID` int NOT NULL AUTO_INCREMENT,
  `Email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Usertype` varchar(50) DEFAULT NULL,
  `PersonName` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `Username` (`Email`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `Email`, `Password`, `Usertype`, `PersonName`) VALUES
(10, 'gabriela.gi0109@gmail.com', '$2y$10$q6qldRxl4UiE5LDlrQBhM.S7KrDSQsW6ZkELCZB1../zVa4tUZWXO', '2', 'Gabriela Ivanova'),
(9, 'admin@gmail.com', '$2y$10$8fjZcONZvxZ.vQsUbZPEluG.QF75aAvyM.tVGY59TVjyStuSN9.Oe', '2', 'Admin'),
(21, 'givanova@petkoangelov.bg', '$2y$10$qzBLJ6QIcNMgKl7WzwfNmeVR.685UkwPR4//qZEbKl7NJSMl6aooG', '1', 'Petko Angelov'),
(22, 'user@petkoangelov.bg', '$2y$10$dFCJ5dKFocUVTCAqAUg2T.hHrKZClXeh0brqNKg/8LqGwENryRbA.', '1', 'User');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
