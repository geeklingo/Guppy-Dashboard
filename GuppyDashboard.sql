-- MySQL dump 10.13  Distrib 5.7.17, for macos10.12 (x86_64)
--
-- Host: miniwhales.net    Database: PT_DATA
-- ------------------------------------------------------
-- Server version	5.7.21-0ubuntu0.16.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `binance_holdings`
--

DROP TABLE IF EXISTS `binance_holdings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `binance_holdings` (
  `coin` varchar(45) CHARACTER SET latin1 NOT NULL,
  `name` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `btc_value` decimal(15,8) DEFAULT NULL,
  PRIMARY KEY (`coin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `portfolio_history`
--

DROP TABLE IF EXISTS `portfolio_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `portfolio_history` (
  `checked` datetime NOT NULL,
  `value` decimal(15,8) DEFAULT NULL,
  `freeBTC` decimal(15,8) NOT NULL,
  `usedBTC` decimal(15,8) NOT NULL,
  `ethValue` decimal(15,8) NOT NULL,
  `exchange` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`checked`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pt_dcaLogData`
--

DROP TABLE IF EXISTS `pt_dcaLogData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pt_dcaLogData` (
  `boughtTimes` int(11) NOT NULL,
  `buyProfit` float NOT NULL,
  `market` text NOT NULL,
  `profit` float NOT NULL,
  `avg_totalAmount` float NOT NULL,
  `avg_totalAmountWithSold` float NOT NULL,
  `avg_avgPrice` float NOT NULL,
  `avg_avgCost` float NOT NULL,
  `avg_firstBoughtDate` datetime NOT NULL,
  `age` int(11) NOT NULL,
  `avg_fee` float NOT NULL,
  `currentPrice` float NOT NULL,
  `sellStrategy` text NOT NULL,
  `buyStrategy` text NOT NULL,
  `volume` float NOT NULL,
  `triggerValue` float NOT NULL,
  `percChange` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pt_sellLogData`
--

DROP TABLE IF EXISTS `pt_sellLogData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pt_sellLogData` (
  `soldAmount` float NOT NULL,
  `soldDate` datetime NOT NULL,
  `boughtTimes` int(11) NOT NULL,
  `market` text NOT NULL,
  `profit` float NOT NULL,
  `avg_totalCost` float NOT NULL,
  `avg_totalAmount` float NOT NULL,
  `avg_totalAmountWithSold` float NOT NULL,
  `avg_avgPrice` float NOT NULL,
  `avg_avgCost` float NOT NULL,
  `avg_firstBoughtDate` datetime NOT NULL,
  `avg_totalWeightedPrice` float NOT NULL,
  `avg_orderNumber` float NOT NULL,
  `avg_fee` float NOT NULL,
  `currentPrice` float NOT NULL,
  `sellStrategy` text NOT NULL,
  `volume` float NOT NULL,
  `triggerValue` float NOT NULL,
  `percChange` float NOT NULL,
  PRIMARY KEY (`soldDate`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-02-15 22:53:32
