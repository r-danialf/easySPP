-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: spp
-- ------------------------------------------------------
-- Server version	8.0.30

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
-- Table structure for table `info_spp_jurusan`
--

DROP TABLE IF EXISTS `info_spp_jurusan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `info_spp_jurusan` (
  `jurusan` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `total_kelas1` int NOT NULL,
  `total_kelas2` int NOT NULL,
  `total_kelas3` int NOT NULL,
  `total_kelas4` int NOT NULL,
  `lingkup_bulan` int NOT NULL,
  PRIMARY KEY (`jurusan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `info_spp_jurusan`
--

LOCK TABLES `info_spp_jurusan` WRITE;
/*!40000 ALTER TABLE `info_spp_jurusan` DISABLE KEYS */;
INSERT INTO `info_spp_jurusan` VALUES ('GP',70000,140000,200000,100000,5),('RPL',70000,140000,200000,0,5),('TP',70000,140000,200000,0,5);
/*!40000 ALTER TABLE `info_spp_jurusan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `siswa`
--

DROP TABLE IF EXISTS `siswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `siswa` (
  `nisn` varchar(10) NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `kelas` enum('X','XI','XII','XIII') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `jurusan` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `bagian` enum('1','2','3','4') NOT NULL DEFAULT '1',
  `absen` int NOT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `no_telp` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`nisn`),
  UNIQUE KEY `nama` (`nama`),
  UNIQUE KEY `kelas` (`kelas`),
  UNIQUE KEY `bagian` (`bagian`),
  KEY `jurusan` (`jurusan`),
  CONSTRAINT `siswa_ibfk_1` FOREIGN KEY (`jurusan`) REFERENCES `info_spp_jurusan` (`jurusan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `siswa`
--

LOCK TABLES `siswa` WRITE;
/*!40000 ALTER TABLE `siswa` DISABLE KEYS */;
INSERT INTO `siswa` VALUES ('3068827613','Yugoslavika','XII','RPL','2',12,'JL. CYKA BLYAT','629928370102');
/*!40000 ALTER TABLE `siswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spp_siswa`
--

DROP TABLE IF EXISTS `spp_siswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `spp_siswa` (
  `nisn` varchar(10) NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `kelas` enum('X','XI','XII','XIII') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `jurusan` varchar(8) NOT NULL,
  `bagian` enum('1','2','3','4') NOT NULL DEFAULT '1',
  `status` enum('BELUM','LUNAS') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `terbayarkan` int NOT NULL,
  `hiraubayar` varchar(4) NOT NULL,
  UNIQUE KEY `bagian` (`bagian`),
  KEY `nisn` (`nisn`),
  KEY `nama` (`nama`),
  KEY `kelas` (`kelas`),
  KEY `jurusan` (`jurusan`),
  CONSTRAINT `spp_siswa_ibfk_2` FOREIGN KEY (`jurusan`) REFERENCES `info_spp_jurusan` (`jurusan`),
  CONSTRAINT `spp_siswa_ibfk_3` FOREIGN KEY (`nama`) REFERENCES `siswa` (`nama`),
  CONSTRAINT `spp_siswa_ibfk_4` FOREIGN KEY (`kelas`) REFERENCES `siswa` (`kelas`),
  CONSTRAINT `spp_siswa_ibfk_5` FOREIGN KEY (`nisn`) REFERENCES `siswa` (`nisn`),
  CONSTRAINT `spp_siswa_ibfk_6` FOREIGN KEY (`bagian`) REFERENCES `siswa` (`bagian`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spp_siswa`
--

LOCK TABLES `spp_siswa` WRITE;
/*!40000 ALTER TABLE `spp_siswa` DISABLE KEYS */;
INSERT INTO `spp_siswa` VALUES ('3068827613','Yugoslavika','XII','RPL','2','BELUM',0,'');
/*!40000 ALTER TABLE `spp_siswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_tu`
--

DROP TABLE IF EXISTS `user_tu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_tu` (
  `user` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `pass` varchar(64) NOT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nuptk` varchar(16) NOT NULL,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_tu`
--

LOCK TABLES `user_tu` WRITE;
/*!40000 ALTER TABLE `user_tu` DISABLE KEYS */;
INSERT INTO `user_tu` VALUES ('albens','','Albert Einstein','2358927103265909');
/*!40000 ALTER TABLE `user_tu` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-05-26 17:15:53
