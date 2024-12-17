-- MySQL dump 10.13  Distrib 8.0.35, for Win64 (x86_64)
--
-- Host: localhost    Database: workflow_db
-- ------------------------------------------------------
-- Server version	8.0.35

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
-- Table structure for table `audit_trail`
--

DROP TABLE IF EXISTS `audit_trail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_trail` (
  `audit_id` int NOT NULL AUTO_INCREMENT,
  `workflow_instance_id` int NOT NULL,
  `action_description` text NOT NULL,
  `action_timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`audit_id`),
  KEY `workflow_instance_id` (`workflow_instance_id`),
  CONSTRAINT `audit_trail_ibfk_1` FOREIGN KEY (`workflow_instance_id`) REFERENCES `workflow_instances` (`workflow_instance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_trail`
--

LOCK TABLES `audit_trail` WRITE;
/*!40000 ALTER TABLE `audit_trail` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_trail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `revoke_conditions`
--

DROP TABLE IF EXISTS `revoke_conditions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `revoke_conditions` (
  `revoke_condition_id` int NOT NULL AUTO_INCREMENT,
  `instance_step_id` int NOT NULL,
  `target_step_id` int NOT NULL,
  `resume_step_id` int NOT NULL,
  PRIMARY KEY (`revoke_condition_id`),
  KEY `instance_step_id` (`instance_step_id`),
  CONSTRAINT `revoke_conditions_ibfk_1` FOREIGN KEY (`instance_step_id`) REFERENCES `workflow_instance_steps` (`instance_step_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `revoke_conditions`
--

LOCK TABLES `revoke_conditions` WRITE;
/*!40000 ALTER TABLE `revoke_conditions` DISABLE KEYS */;
/*!40000 ALTER TABLE `revoke_conditions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `state_manager`
--

DROP TABLE IF EXISTS `state_manager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `state_manager` (
  `state_manager_id` int NOT NULL AUTO_INCREMENT,
  `workflow_instance_id` int NOT NULL,
  `current_state` int NOT NULL,
  `is_halted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`state_manager_id`),
  KEY `workflow_instance_id` (`workflow_instance_id`),
  CONSTRAINT `state_manager_ibfk_1` FOREIGN KEY (`workflow_instance_id`) REFERENCES `workflow_instances` (`workflow_instance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `state_manager`
--

LOCK TABLES `state_manager` WRITE;
/*!40000 ALTER TABLE `state_manager` DISABLE KEYS */;
/*!40000 ALTER TABLE `state_manager` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `steps`
--

DROP TABLE IF EXISTS `steps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `steps` (
  `step_id` int NOT NULL AUTO_INCREMENT,
  `workflow_id` int NOT NULL,
  `step_owner_role` varchar(255) NOT NULL,
  `step_position` int NOT NULL,
  `step_name` varchar(255) DEFAULT NULL,
  `step_description` text,
  `step_next_step_id` int DEFAULT NULL,
  `step_previous_step_id` int DEFAULT NULL,
  `step_on_success_step_id` int DEFAULT NULL,
  `step_on_failure_step_id` int DEFAULT NULL,
  PRIMARY KEY (`step_id`),
  KEY `workflow_id` (`workflow_id`),
  KEY `step_next_step_id` (`step_next_step_id`),
  KEY `step_previous_step_id` (`step_previous_step_id`),
  KEY `step_on_success_step_id` (`step_on_success_step_id`),
  KEY `step_on_failure_step_id` (`step_on_failure_step_id`),
  CONSTRAINT `steps_ibfk_1` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`workflow_id`),
  CONSTRAINT `steps_ibfk_2` FOREIGN KEY (`step_next_step_id`) REFERENCES `steps` (`step_id`),
  CONSTRAINT `steps_ibfk_3` FOREIGN KEY (`step_previous_step_id`) REFERENCES `steps` (`step_id`),
  CONSTRAINT `steps_ibfk_4` FOREIGN KEY (`step_on_success_step_id`) REFERENCES `steps` (`step_id`),
  CONSTRAINT `steps_ibfk_5` FOREIGN KEY (`step_on_failure_step_id`) REFERENCES `steps` (`step_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `steps`
--

LOCK TABLES `steps` WRITE;
/*!40000 ALTER TABLE `steps` DISABLE KEYS */;
/*!40000 ALTER TABLE `steps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workflow_instance_steps`
--

DROP TABLE IF EXISTS `workflow_instance_steps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `workflow_instance_steps` (
  `instance_step_id` int NOT NULL AUTO_INCREMENT,
  `workflow_instance_id` int NOT NULL,
  `step_id` int NOT NULL,
  `instance_step_owner_name` varchar(255) NOT NULL,
  `instance_step_description` text,
  `instance_step_on_success` int DEFAULT NULL,
  `instance_step_on_failure` int DEFAULT NULL,
  `instance_step_next_step_id` int DEFAULT NULL,
  `instance_step_previous_step_id` int DEFAULT NULL,
  PRIMARY KEY (`instance_step_id`),
  KEY `workflow_instance_id` (`workflow_instance_id`),
  KEY `step_id` (`step_id`),
  KEY `instance_step_on_success` (`instance_step_on_success`),
  KEY `instance_step_on_failure` (`instance_step_on_failure`),
  KEY `instance_step_next_step_id` (`instance_step_next_step_id`),
  KEY `instance_step_previous_step_id` (`instance_step_previous_step_id`),
  CONSTRAINT `workflow_instance_steps_ibfk_1` FOREIGN KEY (`workflow_instance_id`) REFERENCES `workflow_instances` (`workflow_instance_id`),
  CONSTRAINT `workflow_instance_steps_ibfk_2` FOREIGN KEY (`step_id`) REFERENCES `steps` (`step_id`),
  CONSTRAINT `workflow_instance_steps_ibfk_3` FOREIGN KEY (`instance_step_on_success`) REFERENCES `workflow_instance_steps` (`instance_step_id`),
  CONSTRAINT `workflow_instance_steps_ibfk_4` FOREIGN KEY (`instance_step_on_failure`) REFERENCES `workflow_instance_steps` (`instance_step_id`),
  CONSTRAINT `workflow_instance_steps_ibfk_5` FOREIGN KEY (`instance_step_next_step_id`) REFERENCES `workflow_instance_steps` (`instance_step_id`),
  CONSTRAINT `workflow_instance_steps_ibfk_6` FOREIGN KEY (`instance_step_previous_step_id`) REFERENCES `workflow_instance_steps` (`instance_step_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workflow_instance_steps`
--

LOCK TABLES `workflow_instance_steps` WRITE;
/*!40000 ALTER TABLE `workflow_instance_steps` DISABLE KEYS */;
/*!40000 ALTER TABLE `workflow_instance_steps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workflow_instances`
--

DROP TABLE IF EXISTS `workflow_instances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `workflow_instances` (
  `workflow_instance_id` int NOT NULL AUTO_INCREMENT,
  `workflow_id` int NOT NULL,
  `workflow_instance_name` varchar(255) NOT NULL,
  `workflow_instance_description` text,
  `workflow_instance_stage` int NOT NULL,
  `workflow_instance_user_id` int NOT NULL,
  `revoked_stage_id` int DEFAULT NULL,
  PRIMARY KEY (`workflow_instance_id`),
  KEY `workflow_id` (`workflow_id`),
  CONSTRAINT `workflow_instances_ibfk_1` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`workflow_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workflow_instances`
--

LOCK TABLES `workflow_instances` WRITE;
/*!40000 ALTER TABLE `workflow_instances` DISABLE KEYS */;
/*!40000 ALTER TABLE `workflow_instances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workflows`
--

DROP TABLE IF EXISTS `workflows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `workflows` (
  `workflow_id` int NOT NULL AUTO_INCREMENT,
  `workflow_name` varchar(255) NOT NULL,
  `workflow_description` text,
  PRIMARY KEY (`workflow_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workflows`
--

LOCK TABLES `workflows` WRITE;
/*!40000 ALTER TABLE `workflows` DISABLE KEYS */;
INSERT INTO `workflows` VALUES (1,'Asset Transfer Workflow','Workflow for asset transfer process');
/*!40000 ALTER TABLE `workflows` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-12-17 10:43:14
