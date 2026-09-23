-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: investment_portfolio
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bond_capital_gains`
--

DROP TABLE IF EXISTS `bond_capital_gains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bond_capital_gains` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `bond_id` int(10) unsigned NOT NULL,
  `exit_transaction_id` int(10) unsigned NOT NULL,
  `buy_transaction_id` int(10) unsigned NOT NULL,
  `buy_date` date NOT NULL,
  `exit_date` date NOT NULL,
  `holding_days` int(10) unsigned NOT NULL,
  `quantity_matched` decimal(12,4) NOT NULL,
  `buy_price` decimal(12,2) NOT NULL,
  `exit_price` decimal(12,2) NOT NULL,
  `realized_gain` decimal(12,2) NOT NULL,
  `gain_type` enum('STCG','LTCG','EXEMPT_SGB_MATURITY') NOT NULL DEFAULT 'LTCG',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bond_capital_gains_user_id_foreign` (`user_id`),
  KEY `bond_capital_gains_bond_id_foreign` (`bond_id`),
  KEY `bond_capital_gains_exit_transaction_id_foreign` (`exit_transaction_id`),
  KEY `bond_capital_gains_buy_transaction_id_foreign` (`buy_transaction_id`),
  CONSTRAINT `bond_capital_gains_bond_id_foreign` FOREIGN KEY (`bond_id`) REFERENCES `bonds` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `bond_capital_gains_buy_transaction_id_foreign` FOREIGN KEY (`buy_transaction_id`) REFERENCES `bond_transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `bond_capital_gains_exit_transaction_id_foreign` FOREIGN KEY (`exit_transaction_id`) REFERENCES `bond_transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `bond_capital_gains_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bond_interest_payouts`
--

DROP TABLE IF EXISTS `bond_interest_payouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bond_interest_payouts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `bond_id` int(10) unsigned NOT NULL,
  `payout_date` date NOT NULL,
  `coupon_rate` decimal(5,2) NOT NULL,
  `gross_interest` decimal(12,2) NOT NULL,
  `tds_deducted` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_interest` decimal(12,2) NOT NULL,
  `period_description` varchar(100) DEFAULT NULL,
  `financial_year` varchar(15) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bond_interest_payouts_bond_id_foreign` (`bond_id`),
  KEY `user_id_bond_id_payout_date` (`user_id`,`bond_id`,`payout_date`),
  KEY `idx_bond_int_perf` (`user_id`,`bond_id`,`payout_date`),
  CONSTRAINT `bond_interest_payouts_bond_id_foreign` FOREIGN KEY (`bond_id`) REFERENCES `bonds` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `bond_interest_payouts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bond_transactions`
--

DROP TABLE IF EXISTS `bond_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bond_transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `bond_id` int(10) unsigned NOT NULL,
  `transaction_type` enum('BUY','SELL','REDEMPTION') NOT NULL DEFAULT 'BUY',
  `transaction_date` date NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `remaining_quantity` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `price` decimal(12,2) NOT NULL,
  `brokerage_charges` decimal(10,2) NOT NULL DEFAULT 0.00,
  `accrued_interest` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(14,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bond_transactions_bond_id_foreign` (`bond_id`),
  KEY `user_id_bond_id_transaction_date` (`user_id`,`bond_id`,`transaction_date`),
  KEY `idx_bond_tx_perf` (`user_id`,`bond_id`,`transaction_date`),
  CONSTRAINT `bond_transactions_bond_id_foreign` FOREIGN KEY (`bond_id`) REFERENCES `bonds` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `bond_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bonds`
--

DROP TABLE IF EXISTS `bonds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bonds` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `bond_name` varchar(150) NOT NULL,
  `isin` varchar(20) NOT NULL,
  `bond_symbol` varchar(30) DEFAULT NULL,
  `category` enum('SGB','GOVT_SECURITY','CORPORATE_NCD','TAX_FREE') NOT NULL DEFAULT 'SGB',
  `issuer` varchar(100) NOT NULL,
  `face_value` decimal(12,2) NOT NULL DEFAULT 1000.00,
  `coupon_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `interest_frequency` enum('MONTHLY','QUARTERLY','SEMI_ANNUAL','ANNUAL','CUMULATIVE') NOT NULL DEFAULT 'SEMI_ANNUAL',
  `issue_date` date DEFAULT NULL,
  `maturity_date` date NOT NULL,
  `current_market_price` decimal(12,2) NOT NULL DEFAULT 1000.00,
  `cmp_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_isin` (`user_id`,`isin`),
  CONSTRAINT `bonds_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `corporate_actions`
--

DROP TABLE IF EXISTS `corporate_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `corporate_actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `module` enum('EQUITY','ETF') NOT NULL,
  `security_id` int(11) NOT NULL,
  `action_type` enum('SPLIT','BONUS','RIGHTS','MERGER','DEMERGER') NOT NULL,
  `record_date` date NOT NULL,
  `ratio_old` decimal(10,4) NOT NULL,
  `ratio_new` decimal(10,4) NOT NULL,
  `offer_price` decimal(15,4) DEFAULT NULL,
  `cost_apportionment_ratio` decimal(6,4) DEFAULT NULL,
  `target_security_id` int(11) DEFAULT NULL,
  `shares_before` decimal(15,4) NOT NULL,
  `shares_after` decimal(15,4) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_security` (`user_id`,`module`,`security_id`),
  KEY `idx_user_record_date` (`user_id`,`record_date`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `equities`
--

DROP TABLE IF EXISTS `equities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `equities` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `symbol` varchar(20) NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `exchange` enum('NSE','BSE') NOT NULL DEFAULT 'NSE',
  `isin` varchar(20) DEFAULT NULL,
  `sector` varchar(60) DEFAULT NULL,
  `current_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `previous_close` decimal(12,2) NOT NULL DEFAULT 0.00,
  `price_updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_symbol_exchange` (`user_id`,`symbol`,`exchange`),
  KEY `user_id` (`user_id`),
  KEY `symbol` (`symbol`),
  CONSTRAINT `equities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `equity_capital_gains`
--

DROP TABLE IF EXISTS `equity_capital_gains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `equity_capital_gains` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `equity_id` int(11) unsigned NOT NULL,
  `sell_transaction_id` int(11) unsigned NOT NULL,
  `buy_transaction_id` int(11) unsigned NOT NULL,
  `quantity_matched` int(11) unsigned NOT NULL,
  `buy_date` date NOT NULL,
  `buy_price` decimal(12,2) NOT NULL,
  `sell_date` date NOT NULL,
  `sell_price` decimal(12,2) NOT NULL,
  `holding_days` int(11) unsigned NOT NULL,
  `gain_type` enum('STCG','LTCG') NOT NULL,
  `realized_gain` decimal(12,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `equity_capital_gains_buy_transaction_id_foreign` (`buy_transaction_id`),
  KEY `user_id` (`user_id`),
  KEY `equity_id` (`equity_id`),
  KEY `sell_transaction_id` (`sell_transaction_id`),
  CONSTRAINT `equity_capital_gains_buy_transaction_id_foreign` FOREIGN KEY (`buy_transaction_id`) REFERENCES `equity_transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `equity_capital_gains_equity_id_foreign` FOREIGN KEY (`equity_id`) REFERENCES `equities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `equity_capital_gains_sell_transaction_id_foreign` FOREIGN KEY (`sell_transaction_id`) REFERENCES `equity_transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `equity_capital_gains_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `equity_dividends`
--

DROP TABLE IF EXISTS `equity_dividends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `equity_dividends` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `equity_id` int(11) unsigned NOT NULL,
  `dividend_date` date NOT NULL,
  `amount_per_share` decimal(10,2) DEFAULT NULL,
  `shares_held` int(11) unsigned DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `tds_deducted` decimal(10,2) NOT NULL DEFAULT 0.00,
  `dividend_type` enum('Interim','Final','Special') NOT NULL DEFAULT 'Interim',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `equity_id` (`equity_id`),
  KEY `dividend_date` (`dividend_date`),
  KEY `idx_eq_div_perf` (`user_id`,`equity_id`,`dividend_date`),
  CONSTRAINT `equity_dividends_equity_id_foreign` FOREIGN KEY (`equity_id`) REFERENCES `equities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `equity_dividends_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `equity_sectors`
--

DROP TABLE IF EXISTS `equity_sectors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `equity_sectors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `equity_transactions`
--

DROP TABLE IF EXISTS `equity_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `equity_transactions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `equity_id` int(11) unsigned NOT NULL,
  `transaction_type` enum('BUY','SELL') NOT NULL,
  `transaction_date` date NOT NULL,
  `quantity` int(11) unsigned NOT NULL,
  `remaining_quantity` int(11) unsigned NOT NULL DEFAULT 0,
  `price` decimal(12,2) NOT NULL,
  `brokerage` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stt_taxes` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(14,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `equity_id` (`equity_id`),
  KEY `transaction_date` (`transaction_date`),
  KEY `idx_eq_tx_perf` (`user_id`,`equity_id`,`transaction_date`),
  CONSTRAINT `equity_transactions_equity_id_foreign` FOREIGN KEY (`equity_id`) REFERENCES `equities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `equity_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `etf_capital_gains`
--

DROP TABLE IF EXISTS `etf_capital_gains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `etf_capital_gains` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `etf_id` int(11) unsigned NOT NULL,
  `sell_transaction_id` int(11) unsigned NOT NULL,
  `buy_transaction_id` int(11) unsigned NOT NULL,
  `quantity_matched` int(11) unsigned NOT NULL,
  `buy_date` date NOT NULL,
  `buy_price` decimal(12,2) NOT NULL,
  `sell_date` date NOT NULL,
  `sell_price` decimal(12,2) NOT NULL,
  `holding_days` int(11) unsigned NOT NULL,
  `gain_type` enum('STCG','LTCG') NOT NULL,
  `realized_gain` decimal(12,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `etf_capital_gains_buy_transaction_id_foreign` (`buy_transaction_id`),
  KEY `user_id` (`user_id`),
  KEY `etf_id` (`etf_id`),
  KEY `sell_transaction_id` (`sell_transaction_id`),
  CONSTRAINT `etf_capital_gains_buy_transaction_id_foreign` FOREIGN KEY (`buy_transaction_id`) REFERENCES `etf_transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `etf_capital_gains_etf_id_foreign` FOREIGN KEY (`etf_id`) REFERENCES `etfs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `etf_capital_gains_sell_transaction_id_foreign` FOREIGN KEY (`sell_transaction_id`) REFERENCES `etf_transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `etf_capital_gains_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `etf_transactions`
--

DROP TABLE IF EXISTS `etf_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `etf_transactions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `etf_id` int(11) unsigned NOT NULL,
  `transaction_type` enum('BUY','SELL') NOT NULL,
  `transaction_date` date NOT NULL,
  `quantity` int(11) unsigned NOT NULL,
  `remaining_quantity` int(11) unsigned NOT NULL DEFAULT 0,
  `price` decimal(12,2) NOT NULL,
  `brokerage` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stt_taxes` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(14,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `etf_id` (`etf_id`),
  KEY `transaction_date` (`transaction_date`),
  KEY `idx_etf_tx_perf` (`user_id`,`etf_id`,`transaction_date`),
  CONSTRAINT `etf_transactions_etf_id_foreign` FOREIGN KEY (`etf_id`) REFERENCES `etfs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `etf_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `etfs`
--

DROP TABLE IF EXISTS `etfs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `etfs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `symbol` varchar(20) NOT NULL,
  `etf_name` varchar(150) NOT NULL,
  `category` enum('Index','Commodity - Gold','Commodity - Silver','Sectoral','Global','Debt') NOT NULL DEFAULT 'Index',
  `amc_name` varchar(100) DEFAULT NULL,
  `exchange` enum('NSE','BSE') NOT NULL DEFAULT 'NSE',
  `isin` varchar(20) DEFAULT NULL,
  `current_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `previous_close` decimal(12,2) NOT NULL DEFAULT 0.00,
  `price_updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_symbol_exchange` (`user_id`,`symbol`,`exchange`),
  KEY `user_id` (`user_id`),
  KEY `symbol` (`symbol`),
  CONSTRAINT `etfs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_fund_capital_gains`
--

DROP TABLE IF EXISTS `mutual_fund_capital_gains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_fund_capital_gains` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `mutual_fund_id` int(11) unsigned NOT NULL,
  `redeem_transaction_id` int(11) unsigned NOT NULL,
  `buy_transaction_id` int(11) unsigned NOT NULL,
  `units_matched` decimal(14,4) NOT NULL,
  `buy_date` date NOT NULL,
  `buy_nav` decimal(12,4) NOT NULL,
  `redeem_date` date NOT NULL,
  `redeem_nav` decimal(12,4) NOT NULL,
  `holding_days` int(11) unsigned NOT NULL,
  `gain_type` enum('STCG','LTCG') NOT NULL,
  `realized_gain` decimal(12,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mutual_fund_capital_gains_buy_transaction_id_foreign` (`buy_transaction_id`),
  KEY `user_id` (`user_id`),
  KEY `mutual_fund_id` (`mutual_fund_id`),
  KEY `redeem_transaction_id` (`redeem_transaction_id`),
  CONSTRAINT `mutual_fund_capital_gains_buy_transaction_id_foreign` FOREIGN KEY (`buy_transaction_id`) REFERENCES `mutual_fund_transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mutual_fund_capital_gains_mutual_fund_id_foreign` FOREIGN KEY (`mutual_fund_id`) REFERENCES `mutual_funds` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mutual_fund_capital_gains_redeem_transaction_id_foreign` FOREIGN KEY (`redeem_transaction_id`) REFERENCES `mutual_fund_transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mutual_fund_capital_gains_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_fund_transactions`
--

DROP TABLE IF EXISTS `mutual_fund_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_fund_transactions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `mutual_fund_id` int(11) unsigned NOT NULL,
  `transaction_type` enum('BUY_SIP','BUY_LUMPSUM','REDEEM') NOT NULL,
  `transaction_date` date NOT NULL,
  `units` decimal(14,4) NOT NULL,
  `remaining_units` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `nav` decimal(12,4) NOT NULL,
  `amount` decimal(14,2) NOT NULL,
  `charges` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(14,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `mutual_fund_id` (`mutual_fund_id`),
  KEY `transaction_date` (`transaction_date`),
  KEY `idx_mf_tx_perf` (`user_id`,`mutual_fund_id`,`transaction_date`),
  CONSTRAINT `mutual_fund_transactions_mutual_fund_id_foreign` FOREIGN KEY (`mutual_fund_id`) REFERENCES `mutual_funds` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mutual_fund_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mutual_funds`
--

DROP TABLE IF EXISTS `mutual_funds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mutual_funds` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `amfi_code` varchar(20) NOT NULL,
  `scheme_name` varchar(180) NOT NULL,
  `folio_number` varchar(50) NOT NULL,
  `category` varchar(80) NOT NULL,
  `fund_house` varchar(100) DEFAULT NULL,
  `current_nav` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `nav_date` date DEFAULT NULL,
  `isin` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_amfi_code_folio_number` (`user_id`,`amfi_code`,`folio_number`),
  KEY `user_id` (`user_id`),
  KEY `amfi_code` (`amfi_code`),
  CONSTRAINT `mutual_funds_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nps_accounts`
--

DROP TABLE IF EXISTS `nps_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nps_accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `pran` varchar(20) NOT NULL,
  `subscriber_name` varchar(100) NOT NULL,
  `pfm_name` varchar(100) NOT NULL,
  `investment_choice` enum('ACTIVE','AUTO') NOT NULL DEFAULT 'ACTIVE',
  `alloc_equity` decimal(5,2) NOT NULL DEFAULT 50.00,
  `alloc_corporate_debt` decimal(5,2) NOT NULL DEFAULT 30.00,
  `alloc_govt_bonds` decimal(5,2) NOT NULL DEFAULT 20.00,
  `alloc_alternative` decimal(5,2) NOT NULL DEFAULT 0.00,
  `scheme_code_equity` varchar(20) DEFAULT NULL,
  `scheme_code_corporate_debt` varchar(20) DEFAULT NULL,
  `scheme_code_govt_bonds` varchar(20) DEFAULT NULL,
  `scheme_code_alternative` varchar(20) DEFAULT NULL,
  `nav_last_updated` date DEFAULT NULL,
  `status` enum('ACTIVE','CLOSED') NOT NULL DEFAULT 'ACTIVE',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_pran` (`user_id`,`pran`),
  CONSTRAINT `nps_accounts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nps_scheme_units`
--

DROP TABLE IF EXISTS `nps_scheme_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nps_scheme_units` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `nps_account_id` int(10) unsigned NOT NULL,
  `nps_transaction_id` int(10) unsigned NOT NULL,
  `scheme_type` enum('SCHEME_E','SCHEME_C','SCHEME_G','SCHEME_A') NOT NULL,
  `scheme_name` varchar(120) NOT NULL,
  `transaction_type` enum('CONTRIBUTION','UNIT_DEDUCTION','WITHDRAWAL') NOT NULL DEFAULT 'CONTRIBUTION',
  `transaction_date` date NOT NULL,
  `allocated_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `nav` decimal(12,4) NOT NULL,
  `units` decimal(14,4) NOT NULL,
  `remaining_units` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `current_nav` decimal(12,4) NOT NULL DEFAULT 10.0000,
  `nav_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nps_scheme_units_user_id_foreign` (`user_id`),
  KEY `nps_scheme_units_nps_transaction_id_foreign` (`nps_transaction_id`),
  KEY `nps_account_id_scheme_type` (`nps_account_id`,`scheme_type`),
  CONSTRAINT `nps_scheme_units_nps_account_id_foreign` FOREIGN KEY (`nps_account_id`) REFERENCES `nps_accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `nps_scheme_units_nps_transaction_id_foreign` FOREIGN KEY (`nps_transaction_id`) REFERENCES `nps_transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `nps_scheme_units_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nps_transactions`
--

DROP TABLE IF EXISTS `nps_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nps_transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `nps_account_id` int(10) unsigned NOT NULL,
  `transaction_type` enum('CONTRIBUTION','UNIT_DEDUCTION','WITHDRAWAL') NOT NULL DEFAULT 'CONTRIBUTION',
  `contribution_type` enum('VOLUNTARY','EMPLOYEE','EMPLOYER') NOT NULL DEFAULT 'VOLUNTARY',
  `transaction_date` date NOT NULL,
  `acknowledgement_no` varchar(60) DEFAULT NULL,
  `gross_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `optional_cash_charges` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `financial_year` varchar(15) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nps_transactions_nps_account_id_foreign` (`nps_account_id`),
  KEY `user_id_transaction_date` (`user_id`,`transaction_date`),
  KEY `idx_nps_tx_perf` (`user_id`,`nps_account_id`,`transaction_date`),
  CONSTRAINT `nps_transactions_nps_account_id_foreign` FOREIGN KEY (`nps_account_id`) REFERENCES `nps_accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `nps_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reit_invit_capital_gains`
--

DROP TABLE IF EXISTS `reit_invit_capital_gains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reit_invit_capital_gains` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `trust_id` int(10) unsigned NOT NULL,
  `sell_transaction_id` int(10) unsigned NOT NULL,
  `buy_transaction_id` int(10) unsigned NOT NULL,
  `buy_date` date NOT NULL,
  `sell_date` date NOT NULL,
  `holding_days` int(10) unsigned NOT NULL,
  `quantity_matched` decimal(12,4) NOT NULL,
  `buy_price` decimal(12,2) NOT NULL,
  `sell_price` decimal(12,2) NOT NULL,
  `realized_gain` decimal(12,2) NOT NULL,
  `gain_type` enum('STCG','LTCG') NOT NULL DEFAULT 'LTCG',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reit_invit_capital_gains_user_id_foreign` (`user_id`),
  KEY `reit_invit_capital_gains_trust_id_foreign` (`trust_id`),
  KEY `reit_invit_capital_gains_sell_transaction_id_foreign` (`sell_transaction_id`),
  KEY `reit_invit_capital_gains_buy_transaction_id_foreign` (`buy_transaction_id`),
  CONSTRAINT `reit_invit_capital_gains_buy_transaction_id_foreign` FOREIGN KEY (`buy_transaction_id`) REFERENCES `reit_invit_transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reit_invit_capital_gains_sell_transaction_id_foreign` FOREIGN KEY (`sell_transaction_id`) REFERENCES `reit_invit_transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reit_invit_capital_gains_trust_id_foreign` FOREIGN KEY (`trust_id`) REFERENCES `reits_invits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reit_invit_capital_gains_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reit_invit_distributions`
--

DROP TABLE IF EXISTS `reit_invit_distributions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reit_invit_distributions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `trust_id` int(10) unsigned NOT NULL,
  `record_date` date DEFAULT NULL,
  `eligible_units` decimal(12,4) DEFAULT 0.0000,
  `dpu` decimal(10,4) DEFAULT 0.0000,
  `payout_date` date NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `interest_component` decimal(12,2) NOT NULL DEFAULT 0.00,
  `dividend_component` decimal(12,2) NOT NULL DEFAULT 0.00,
  `return_of_capital` decimal(12,2) NOT NULL DEFAULT 0.00,
  `other_income` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tds_deducted` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_received` decimal(12,2) NOT NULL,
  `quarter_description` varchar(100) DEFAULT NULL,
  `financial_year` varchar(15) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reit_invit_distributions_trust_id_foreign` (`trust_id`),
  KEY `user_id_trust_id_payout_date` (`user_id`,`trust_id`,`payout_date`),
  KEY `idx_reit_dist_perf` (`user_id`,`trust_id`,`record_date`),
  CONSTRAINT `reit_invit_distributions_trust_id_foreign` FOREIGN KEY (`trust_id`) REFERENCES `reits_invits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reit_invit_distributions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reit_invit_transactions`
--

DROP TABLE IF EXISTS `reit_invit_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reit_invit_transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `trust_id` int(10) unsigned NOT NULL,
  `transaction_type` enum('BUY','SELL') NOT NULL DEFAULT 'BUY',
  `transaction_date` date NOT NULL,
  `quantity` decimal(12,4) NOT NULL,
  `remaining_quantity` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `price` decimal(12,2) NOT NULL,
  `brokerage` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stt_taxes` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(14,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reit_invit_transactions_trust_id_foreign` (`trust_id`),
  KEY `user_id_trust_id_transaction_date` (`user_id`,`trust_id`,`transaction_date`),
  KEY `idx_reit_tx_perf` (`user_id`,`trust_id`,`transaction_date`),
  CONSTRAINT `reit_invit_transactions_trust_id_foreign` FOREIGN KEY (`trust_id`) REFERENCES `reits_invits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reit_invit_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reits_invits`
--

DROP TABLE IF EXISTS `reits_invits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reits_invits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `trust_name` varchar(150) NOT NULL,
  `symbol` varchar(30) NOT NULL,
  `isin` varchar(20) NOT NULL,
  `trust_type` enum('REIT','INVIT') NOT NULL DEFAULT 'REIT',
  `exchange` varchar(10) NOT NULL DEFAULT 'NSE',
  `sponsor` varchar(100) DEFAULT NULL,
  `current_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `prev_close` decimal(12,2) NOT NULL DEFAULT 0.00,
  `last_price_update` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_symbol_exchange` (`user_id`,`symbol`,`exchange`),
  CONSTRAINT `reits_invits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `fy_start_month` tinyint(2) unsigned DEFAULT 4,
  `records_per_page` int(11) NOT NULL DEFAULT 20,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expenses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `expense_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `expense_type` enum('BROKERAGE','STT_TAXES','PLATFORM_MISC') NOT NULL DEFAULT 'BROKERAGE',
  `module` enum('equity','etf','bond','reit_invit','mutual_fund') NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_user_id_foreign` (`user_id`),
  KEY `idx_expenses_user_date` (`user_id`, `expense_date`),
  KEY `idx_expenses_user_module` (`user_id`, `module`),
  CONSTRAINT `expenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-21  7:10:10

--
-- Seed Data for table `equity_sectors`
--
INSERT IGNORE INTO `equity_sectors` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1,'Information Technology (IT)','Software, Services, Hardware & IT Consulting',NOW(),NOW()),
(2,'Banking & Financial Services','Commercial Banks, NBFCs, Housing Finance & Insurance',NOW(),NOW()),
(3,'Energy, Oil & Gas','Petroleum, Refining, Natural Gas & Renewable Energy',NOW(),NOW()),
(4,'Automobile & Auto Components','Passenger Vehicles, Commercial Vehicles & Auto Ancillaries',NOW(),NOW()),
(5,'Pharmaceuticals & Healthcare','Drug Manufacturing, Formulations, Hospitals & Diagnostics',NOW(),NOW()),
(6,'Fast Moving Consumer Goods (FMCG)','Consumer Non-Durables, Food, Beverages & Personal Care',NOW(),NOW()),
(7,'Metals & Mining','Steel, Aluminum, Copper, Zinc & Mining operations',NOW(),NOW()),
(8,'Infrastructure & Construction','Civil Construction, Roads, Ports & Engineering',NOW(),NOW()),
(9,'Telecommunication','Telecom Service Providers & Communication Infrastructure',NOW(),NOW()),
(10,'Power & Utilities','Generation, Transmission, Distribution & Clean Energy',NOW(),NOW()),
(11,'Chemicals & Petrochemicals','Specialty Chemicals, Agro-chemicals & Fertilizers',NOW(),NOW()),
(12,'Consumer Durables','Electronics, Appliances, Furniture & Home Improvement',NOW(),NOW()),
(13,'Real Estate','Residential & Commercial Real Estate Developers',NOW(),NOW()),
(14,'Capital Goods & Industrial','Heavy Electrical, Machinery, Engines & Equipment',NOW(),NOW()),
(15,'Services & Retail','E-commerce, Logistics, Hotels & Retail Chains',NOW(),NOW()),
(16,'Others','Diversified & Conglomerate Businesses',NOW(),NOW()),
(17,'Information Technology','Software and Technology',NOW(),NOW()),
(18,'Energy & Petrochemicals','Refining and power',NOW(),NOW());
