-- MySQL dump 10.13  Distrib 5.7.19, for Linux (x86_64)
--
-- Host: localhost    Database: cb_cbosdevel
-- ------------------------------------------------------
-- Server version	5.7.19-0ubuntu0.16.04.1

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
-- Table structure for table `berli_crmtogo_config`
--

DROP TABLE IF EXISTS `berli_crmtogo_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `berli_crmtogo_config` (
  `crmtogouser` int(19) NOT NULL,
  `navi_limit` int(3) NOT NULL,
  `theme_color` varchar(1) NOT NULL,
  `compact_cal` int(1) NOT NULL,
  PRIMARY KEY (`crmtogouser`),
  CONSTRAINT `fk_1_berli_crmtogo_config` FOREIGN KEY (`crmtogouser`) REFERENCES `vtiger_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `berli_crmtogo_config`
--

LOCK TABLES `berli_crmtogo_config` WRITE;
/*!40000 ALTER TABLE `berli_crmtogo_config` DISABLE KEYS */;
INSERT INTO `berli_crmtogo_config` VALUES (1,25,'b',1);
/*!40000 ALTER TABLE `berli_crmtogo_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `berli_crmtogo_defaults`
--

DROP TABLE IF EXISTS `berli_crmtogo_defaults`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `berli_crmtogo_defaults` (
  `fetch_limit` int(3) NOT NULL,
  `crmtogo_lang` varchar(5) NOT NULL,
  `defaulttheme` varchar(1) NOT NULL,
  `crm_version` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `berli_crmtogo_defaults`
--

LOCK TABLES `berli_crmtogo_defaults` WRITE;
/*!40000 ALTER TABLE `berli_crmtogo_defaults` DISABLE KEYS */;
INSERT INTO `berli_crmtogo_defaults` VALUES (99,'en_us','b','6.3');
/*!40000 ALTER TABLE `berli_crmtogo_defaults` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `berli_crmtogo_modules`
--

DROP TABLE IF EXISTS `berli_crmtogo_modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `berli_crmtogo_modules` (
  `crmtogo_user` int(19) NOT NULL,
  `crmtogo_module` varchar(50) NOT NULL,
  `crmtogo_active` int(1) NOT NULL DEFAULT '1',
  `order_num` int(3) NOT NULL,
  KEY `fk_1_berli_crmtogo_modules` (`crmtogo_user`),
  CONSTRAINT `fk_1_berli_crmtogo_modules` FOREIGN KEY (`crmtogo_user`) REFERENCES `vtiger_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `berli_crmtogo_modules`
--

LOCK TABLES `berli_crmtogo_modules` WRITE;
/*!40000 ALTER TABLE `berli_crmtogo_modules` DISABLE KEYS */;
INSERT INTO `berli_crmtogo_modules` VALUES (1,'Contacts',1,0),(1,'Accounts',1,1),(1,'Leads',1,2),(1,'Calendar',1,3),(1,'Potentials',1,4),(1,'HelpDesk',1,5),(1,'Vendors',1,6),(1,'Assets',1,7),(1,'Faq',1,8),(1,'Documents',1,9),(1,'Quotes',1,10),(1,'SalesOrder',1,11),(1,'Invoice',1,12),(1,'Products',1,13),(1,'Project',1,14),(1,'ProjectMilestone',1,15),(1,'ProjectTask',1,16),(1,'Events',1,17);
/*!40000 ALTER TABLE `berli_crmtogo_modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cb_messagequeue`
--

DROP TABLE IF EXISTS `cb_messagequeue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cb_messagequeue` (
  `idx` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `channel` varchar(200) NOT NULL,
  `producer` varchar(200) NOT NULL,
  `consumer` varchar(200) NOT NULL,
  `type` varchar(20) NOT NULL,
  `share` varchar(20) NOT NULL,
  `sequence` int(11) NOT NULL,
  `senton` datetime NOT NULL,
  `deliverafter` datetime DEFAULT NULL,
  `expires` datetime NOT NULL,
  `version` varchar(20) NOT NULL,
  `invalid` tinyint(4) NOT NULL,
  `invalidreason` varchar(500) NOT NULL,
  `userid` int(11) NOT NULL,
  `information` mediumtext NOT NULL,
  PRIMARY KEY (`idx`),
  KEY `cbmqchannelseq` (`channel`),
  KEY `cbmqproducer` (`producer`),
  KEY `cbmqconsumer` (`consumer`),
  KEY `cbmqexpires` (`expires`),
  KEY `cbmquserid` (`userid`),
  KEY `cbmqchannel` (`channel`,`sequence`),
  KEY `cbmqdeliver` (`deliverafter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cb_messagequeue`
--

LOCK TABLES `cb_messagequeue` WRITE;
/*!40000 ALTER TABLE `cb_messagequeue` DISABLE KEYS */;
/*!40000 ALTER TABLE `cb_messagequeue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cb_mqsubscriptions`
--

DROP TABLE IF EXISTS `cb_mqsubscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cb_mqsubscriptions` (
  `md5idx` char(32) NOT NULL,
  `channel` varchar(200) NOT NULL,
  `producer` varchar(200) NOT NULL,
  `consumer` varchar(200) NOT NULL,
  `callback` varchar(500) NOT NULL,
  PRIMARY KEY (`md5idx`),
  KEY `cbmqchannelseq` (`channel`),
  KEY `cbmqproducer` (`producer`),
  KEY `cbmqconsumer` (`consumer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cb_mqsubscriptions`
--

LOCK TABLES `cb_mqsubscriptions` WRITE;
/*!40000 ALTER TABLE `cb_mqsubscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `cb_mqsubscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cb_settings`
--

DROP TABLE IF EXISTS `cb_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cb_settings` (
  `setting_key` varchar(200) NOT NULL,
  `setting_value` varchar(1000) NOT NULL,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cb_settings`
--

LOCK TABLES `cb_settings` WRITE;
/*!40000 ALTER TABLE `cb_settings` DISABLE KEYS */;
INSERT INTO `cb_settings` VALUES ('cbmqtm_classfile','include/cbmqtm/cbmqtm_dbdistributor.php'),('cbmqtm_classname','cbmqtm_dbdistributor');
/*!40000 ALTER TABLE `cb_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `com_vtiger_workflow_activatedonce`
--

DROP TABLE IF EXISTS `com_vtiger_workflow_activatedonce`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `com_vtiger_workflow_activatedonce` (
  `workflow_id` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  PRIMARY KEY (`workflow_id`,`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `com_vtiger_workflow_activatedonce`
--

LOCK TABLES `com_vtiger_workflow_activatedonce` WRITE;
/*!40000 ALTER TABLE `com_vtiger_workflow_activatedonce` DISABLE KEYS */;
/*!40000 ALTER TABLE `com_vtiger_workflow_activatedonce` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `com_vtiger_workflow_tasktypes`
--

DROP TABLE IF EXISTS `com_vtiger_workflow_tasktypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `com_vtiger_workflow_tasktypes` (
  `id` int(11) NOT NULL,
  `tasktypename` varchar(255) NOT NULL,
  `label` varchar(255) DEFAULT NULL,
  `classname` varchar(255) DEFAULT NULL,
  `classpath` varchar(255) DEFAULT NULL,
  `templatepath` varchar(255) DEFAULT NULL,
  `modules` varchar(500) DEFAULT NULL,
  `sourcemodule` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tasktypename` (`tasktypename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `com_vtiger_workflow_tasktypes`
--

LOCK TABLES `com_vtiger_workflow_tasktypes` WRITE;
/*!40000 ALTER TABLE `com_vtiger_workflow_tasktypes` DISABLE KEYS */;
INSERT INTO `com_vtiger_workflow_tasktypes` VALUES (1,'VTEmailTask','Send Mail','VTEmailTask','modules/com_vtiger_workflow/tasks/VTEmailTask.inc','com_vtiger_workflow/taskforms/VTEmailTask.tpl','{\"include\":[],\"exclude\":[]}',''),(2,'VTEntityMethodTask','Invoke Custom Function','VTEntityMethodTask','modules/com_vtiger_workflow/tasks/VTEntityMethodTask.inc','com_vtiger_workflow/taskforms/VTEntityMethodTask.tpl','{\"include\":[],\"exclude\":[]}',''),(3,'VTCreateTodoTask','Create Todo','VTCreateTodoTask','modules/com_vtiger_workflow/tasks/VTCreateTodoTask.inc','com_vtiger_workflow/taskforms/VTCreateTodoTask.tpl','{\"include\":[\"Leads\",\"Accounts\",\"Potentials\",\"Contacts\",\"HelpDesk\",\"Campaigns\",\"Quotes\",\"PurchaseOrder\",\"SalesOrder\",\"Invoice\"],\"exclude\":[\"Calendar\",\"FAQ\",\"Events\"]}',''),(4,'VTCreateEventTask','Create Event','VTCreateEventTask','modules/com_vtiger_workflow/tasks/VTCreateEventTask.inc','com_vtiger_workflow/taskforms/VTCreateEventTask.tpl','{\"include\":[\"Leads\",\"Accounts\",\"Potentials\",\"Contacts\",\"HelpDesk\",\"Campaigns\"],\"exclude\":[\"Calendar\",\"FAQ\",\"Events\"]}',''),(5,'VTUpdateFieldsTask','Update Fields','VTUpdateFieldsTask','modules/com_vtiger_workflow/tasks/VTUpdateFieldsTask.inc','com_vtiger_workflow/taskforms/VTUpdateFieldsTask.tpl','{\"include\":[],\"exclude\":[]}',''),(6,'VTCreateEntityTask','Create Entity','VTCreateEntityTask','modules/com_vtiger_workflow/tasks/VTCreateEntityTask.inc','com_vtiger_workflow/taskforms/VTCreateEntityTask.tpl','{\"include\":[],\"exclude\":[]}',''),(7,'VTSMSTask','SMS Task','VTSMSTask','modules/com_vtiger_workflow/tasks/VTSMSTask.inc','com_vtiger_workflow/taskforms/VTSMSTask.tpl','{\"include\":[],\"exclude\":[]}','SMSNotifier'),(8,'CBTagTask','CBTagTask','CBTagTask','modules/com_vtiger_workflow/tasks/CBTagTask.inc','com_vtiger_workflow/taskforms/CBTagTask.tpl','{\"include\":[],\"exclude\":[]}',''),(9,'CBRelateSales','CBRelateSales','CBRelateSales','modules/com_vtiger_workflow/tasks/CBRelateSales.inc','com_vtiger_workflow/taskforms/CBRelateSales.tpl','{\"include\":[\"Quotes\",\"SalesOrder\",\"Invoice\",\"PurchaseOrder\"],\"exclude\":[]}',''),(10,'CBDeleteRelatedTask','CBDeleteRelatedTask','CBDeleteRelatedTask','modules/com_vtiger_workflow/tasks/CBDeleteRelatedTask.inc','com_vtiger_workflow/taskforms/CBDeleteRelated.tpl','{\"include\":[],\"exclude\":[]}',''),(11,'CBSelectcbMap','CBSelectcbMap','CBSelectcbMap','modules/com_vtiger_workflow/tasks/CBSelectcbMap.inc','com_vtiger_workflow/taskforms/CBSelectcbMap.tpl','{\"include\":[],\"exclude\":[]}',''),(12,'CBAssignRelatedTask','CBAssignRelatedTask','CBAssignRelatedTask','modules/com_vtiger_workflow/tasks/CBAssignRelatedTask.inc','com_vtiger_workflow/taskforms/CBAssignRelated.tpl','{\"include\":[],\"exclude\":[]}',''),(13,'DuplicateRecords','DuplicateRecords','DuplicateRecords','modules/com_vtiger_workflow/tasks/DuplicateRecords.inc','com_vtiger_workflow/taskforms/DuplicateRecords.tpl','{\"include\":[],\"exclude\":[]}',''),(14,'ConvertInventoryModule','ConvertInventoryModule','ConvertInventoryModule','modules/com_vtiger_workflow/tasks/convert_inventorymodule.inc','com_vtiger_workflow/taskforms/convert_inventorymodule.tpl','{\"include\":[\"Invoice\",\"Quotes\",\"PurchaseOrder\",\"SalesOrder\",\"Issuecards\"],\"exclude\":[]}','');
/*!40000 ALTER TABLE `com_vtiger_workflow_tasktypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `com_vtiger_workflow_tasktypes_seq`
--

DROP TABLE IF EXISTS `com_vtiger_workflow_tasktypes_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `com_vtiger_workflow_tasktypes_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `com_vtiger_workflow_tasktypes_seq`
--

LOCK TABLES `com_vtiger_workflow_tasktypes_seq` WRITE;
/*!40000 ALTER TABLE `com_vtiger_workflow_tasktypes_seq` DISABLE KEYS */;
INSERT INTO `com_vtiger_workflow_tasktypes_seq` VALUES (14);
/*!40000 ALTER TABLE `com_vtiger_workflow_tasktypes_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `com_vtiger_workflows`
--

DROP TABLE IF EXISTS `com_vtiger_workflows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `com_vtiger_workflows` (
  `workflow_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(100) DEFAULT NULL,
  `summary` varchar(400) NOT NULL,
  `test` text,
  `execution_condition` int(11) NOT NULL,
  `defaultworkflow` int(1) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `schtypeid` int(10) DEFAULT NULL,
  `schtime` time DEFAULT NULL,
  `schdayofmonth` varchar(200) DEFAULT NULL,
  `schdayofweek` varchar(200) DEFAULT NULL,
  `schannualdates` varchar(200) DEFAULT NULL,
  `nexttrigger_time` datetime DEFAULT NULL,
  `schminuteinterval` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`workflow_id`),
  KEY `module_name` (`module_name`,`execution_condition`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `com_vtiger_workflows`
--

LOCK TABLES `com_vtiger_workflows` WRITE;
/*!40000 ALTER TABLE `com_vtiger_workflows` DISABLE KEYS */;
INSERT INTO `com_vtiger_workflows` VALUES (1,'Invoice','UpdateInventoryProducts On Every Save','[{\"fieldname\":\"subject\",\"operation\":\"does not contain\",\"value\":\"`!`\"}]',3,1,'basic',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,'Accounts','Send Email to user when Notifyowner is True','[{\"fieldname\":\"notify_owner\",\"operation\":\"is\",\"value\":\"true:boolean\"}]',2,1,'basic',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,'Contacts','Send Email to user when Notifyowner is True','[{\"fieldname\":\"notify_owner\",\"operation\":\"is\",\"value\":\"true:boolean\"}]',2,1,'basic',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,'Contacts','Send Email to user when Portal User is True','[{\"fieldname\":\"portal\",\"operation\":\"is\",\"value\":\"true:boolean\"}]',2,1,'basic',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,'Potentials','Send Email to users on Potential creation',NULL,1,1,'basic',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,'Contacts','Workflow for Contact Creation or Modification','',3,1,'basic',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,'HelpDesk','Workflow for Ticket Created from Portal','[{\"fieldname\":\"from_portal\",\"operation\":\"is\",\"value\":\"true:boolean\"}]',1,1,'basic',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,'HelpDesk','Workflow for Ticket Updated from Portal','[{\"fieldname\":\"from_portal\",\"operation\":\"is\",\"value\":\"true:boolean\"}]',4,1,'basic',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,'HelpDesk','Workflow for Ticket Change, not from the Portal','[{\"fieldname\":\"from_portal\",\"operation\":\"is\",\"value\":\"false:boolean\"}]',3,1,'basic',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,'Events','Workflow for Events when Send Notification is True','[{\"fieldname\":\"sendnotification\",\"operation\":\"is\",\"value\":\"true:boolean\"}]',3,1,'basic',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,'Calendar','Workflow for Calendar Todos when Send Notification is True','[{\"fieldname\":\"sendnotification\",\"operation\":\"is\",\"value\":\"true:boolean\"}]',3,1,'basic',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,'Potentials','Calculate or Update forecast amount','',3,1,'basic',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,'InventoryDetails','Line Completed','[{\"fieldname\":\"units_delivered_received\",\"operation\":\"equal to\",\"value\":\"quantity\",\"valuetype\":\"fieldname\",\"joincondition\":\"and\",\"groupid\":\"0\"}]',3,1,'basic',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,'PurchaseOrder','UpdateInventoryProducts On Every Save','[{\"fieldname\":\"subject\",\"operation\":\"does not contain\",\"value\":\"`!`\"}]',3,1,'basic',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15,'Events','Send Reminder Template','',7,1,'basic',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,'Calendar','Notify when a task is delayed beyond 24 hrs','[{\"fieldname\":\"taskstatus\",\"operation\":\"is not\",\"value\":\"Completed\",\"valuetype\":\"rawtext\",\"joincondition\":\"and\",\"groupid\":\"0\"},{\"fieldname\":\"date_start\",\"operation\":\"less than days ago\",\"value\":\"1\",\"valuetype\":\"expression\",\"joincondition\":\"and\",\"groupid\":\"0\"}]',6,0,'basic',2,'02:15:00',NULL,'','','2017-01-07 02:15:00','5'),(17,'Products','Product Support Starting','[{\"fieldname\":\"start_date\",\"operation\":\"is\",\"value\":\"get_date(\'today\')\",\"valuetype\":\"expression\",\"joincondition\":\"and\",\"groupid\":\"0\"}]',6,0,'basic',2,'01:15:00',NULL,'','','2017-01-07 01:15:00','5'),(18,'Products','Product Support Ended','[{\"fieldname\":\"expiry_date\",\"operation\":\"is\",\"value\":\"get_date(\'today\')\",\"valuetype\":\"expression\",\"joincondition\":\"and\",\"groupid\":\"0\"}]',6,0,'basic',2,'01:25:00',NULL,'','','2017-01-07 01:25:00','5'),(19,'Contacts','Client End Support Notification 1 month','[{\"fieldname\":\"support_end_date\",\"operation\":\"is\",\"value\":\"add_days(get_date(\'today\'), 30)\",\"valuetype\":\"expression\",\"joincondition\":\"and\",\"groupid\":\"0\"}]',6,0,'basic',2,'05:15:00',NULL,'','','2017-01-07 05:15:00','5'),(20,'Contacts','Client End Support Notification 1 week','[{\"fieldname\":\"support_end_date\",\"operation\":\"is\",\"value\":\"add_days(get_date(\'today\'), 7)\",\"valuetype\":\"expression\",\"joincondition\":\"and\",\"groupid\":\"0\"}]',6,0,'basic',2,'05:25:00',NULL,'','','2017-01-07 05:25:00','5'),(21,'cbCalendar','Workflow for Calendar when Send Notification is True','[{\"fieldname\":\"sendnotification\",\"operation\":\"is\",\"value\":\"true:boolean\"}]',3,1,'basic',0,'00:00:00','','','','2017-09-02 09:22:46',''),(22,'cbCalendar','Create Calendar Follow Up on change','[{\"fieldname\":\"followupcreate\",\"operation\":\"has changed to\",\"value\":\"true:boolean\",\"valuetype\":\"rawtext\",\"joincondition\":\"and\",\"groupid\":\"0\"}]',4,1,'basic',0,'00:00:00','','','','2017-09-02 09:22:46',''),(23,'cbCalendar','Create Calendar Follow Up on create','[{\"fieldname\":\"followupcreate\",\"operation\":\"is\",\"value\":\"true:boolean\",\"valuetype\":\"rawtext\",\"joincondition\":\"and\",\"groupid\":\"0\"}]',1,1,'basic',0,'00:00:00','','','','2017-09-02 09:22:46',''),(24,'cbCalendar','Notify when a task is delayed beyond 24 hrs','[{\"fieldname\":\"date_start\",\"operation\":\"less than days ago\",\"value\":\"1\",\"valuetype\":\"expression\",\"joincondition\":\"and\",\"groupid\":\"0\"},{\"fieldname\":\"activitytype\",\"operation\":\"is\",\"value\":\"Task\",\"valuetype\":\"rawtext\",\"joincondition\":\"and\",\"groupid\":\"0\"},{\"fieldname\":\"eventstatus\",\"operation\":\"is not\",\"value\":\"Held\",\"valuetype\":\"rawtext\",\"joincondition\":\"and\",\"groupid\":\"0\"},{\"fieldname\":\"eventstatus\",\"operation\":\"is not\",\"value\":\"Completed\",\"valuetype\":\"rawtext\",\"joincondition\":\"and\",\"groupid\":\"0\"}]',6,0,'basic',2,'02:15:00','','','',NULL,'5'),(25,'cbCalendar','Workflow for Events when Send Notification is True','[{\"fieldname\":\"sendnotification\",\"operation\":\"is\",\"value\":\"true:boolean\"}]',3,1,'basic',0,'00:00:00','','','',NULL,''),(26,'cbCalendar','Send Reminder Template','',7,1,'basic',0,'00:00:00','','','',NULL,'');
/*!40000 ALTER TABLE `com_vtiger_workflows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `com_vtiger_workflows_expfunctions`
--

DROP TABLE IF EXISTS `com_vtiger_workflows_expfunctions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `com_vtiger_workflows_expfunctions` (
  `expname` varchar(180) NOT NULL,
  `expinfo` varchar(250) NOT NULL,
  `funcname` varchar(180) NOT NULL,
  `funcfile` varchar(250) NOT NULL,
  PRIMARY KEY (`expname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `com_vtiger_workflows_expfunctions`
--

LOCK TABLES `com_vtiger_workflows_expfunctions` WRITE;
/*!40000 ALTER TABLE `com_vtiger_workflows_expfunctions` DISABLE KEYS */;
/*!40000 ALTER TABLE `com_vtiger_workflows_expfunctions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `com_vtiger_workflows_seq`
--

DROP TABLE IF EXISTS `com_vtiger_workflows_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `com_vtiger_workflows_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `com_vtiger_workflows_seq`
--

LOCK TABLES `com_vtiger_workflows_seq` WRITE;
/*!40000 ALTER TABLE `com_vtiger_workflows_seq` DISABLE KEYS */;
INSERT INTO `com_vtiger_workflows_seq` VALUES (26);
/*!40000 ALTER TABLE `com_vtiger_workflows_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `com_vtiger_workflowtask_queue`
--

DROP TABLE IF EXISTS `com_vtiger_workflowtask_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `com_vtiger_workflowtask_queue` (
  `task_id` int(11) DEFAULT NULL,
  `entity_id` varchar(100) DEFAULT NULL,
  `do_after` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `com_vtiger_workflowtask_queue`
--

LOCK TABLES `com_vtiger_workflowtask_queue` WRITE;
/*!40000 ALTER TABLE `com_vtiger_workflowtask_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `com_vtiger_workflowtask_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `com_vtiger_workflowtasks`
--

DROP TABLE IF EXISTS `com_vtiger_workflowtasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `com_vtiger_workflowtasks` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` int(11) DEFAULT NULL,
  `summary` varchar(400) NOT NULL,
  `task` text,
  `executionorder` int(10) DEFAULT NULL,
  PRIMARY KEY (`task_id`),
  KEY `com_vtiger_workflowtasks_workflowidx` (`workflow_id`),
  KEY `executionorder` (`executionorder`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `com_vtiger_workflowtasks`
--

LOCK TABLES `com_vtiger_workflowtasks` WRITE;
/*!40000 ALTER TABLE `com_vtiger_workflowtasks` DISABLE KEYS */;
INSERT INTO `com_vtiger_workflowtasks` VALUES (1,1,'','O:18:\"VTEntityMethodTask\":6:{s:18:\"executeImmediately\";b:1;s:10:\"workflowId\";i:1;s:7:\"summary\";s:0:\"\";s:6:\"active\";b:1;s:10:\"methodName\";s:15:\"UpdateInventory\";s:2:\"id\";i:1;}',1),(2,2,'An account has been created ','O:11:\"VTEmailTask\":9:{s:18:\"executeImmediately\";b:0;s:10:\"workflowId\";i:2;s:7:\"summary\";s:28:\"An account has been created \";s:6:\"active\";b:1;s:10:\"methodName\";s:11:\"NotifyOwner\";s:9:\"recepient\";s:36:\"$(assigned_user_id : (Users) email1)\";s:7:\"subject\";s:26:\"Regarding Account Creation\";s:7:\"content\";s:301:\"An Account has been assigned to you on vtigerCRM<br>Details of account are :<br><br>AccountId:<b>$account_no</b><br>AccountName:<b>$accountname</b><br>Rating:<b>$rating</b><br>Industry:<b>$industry</b><br>AccountType:<b>$accounttype</b><br>Description:<b>$description</b><br><br><br>Thank You<br>Admin\";s:2:\"id\";i:2;}',1),(3,3,'An contact has been created ','O:11:\"VTEmailTask\":9:{s:18:\"executeImmediately\";b:0;s:10:\"workflowId\";i:3;s:7:\"summary\";s:28:\"An contact has been created \";s:6:\"active\";b:1;s:10:\"methodName\";s:11:\"NotifyOwner\";s:9:\"recepient\";s:36:\"$(assigned_user_id : (Users) email1)\";s:7:\"subject\";s:26:\"Regarding Contact Creation\";s:7:\"content\";s:305:\"An Contact has been assigned to you on vtigerCRM<br>Details of Contact are :<br><br>Contact Id:<b>$contact_no</b><br>LastName:<b>$lastname</b><br>FirstName:<b>$firstname</b><br>Lead Source:<b>$leadsource</b><br>Department:<b>$department</b><br>Description:<b>$description</b><br><br><br>Thank You<br>Admin\";s:2:\"id\";i:3;}',1),(4,4,'An contact has been created ','O:11:\"VTEmailTask\":9:{s:18:\"executeImmediately\";b:0;s:10:\"workflowId\";i:4;s:7:\"summary\";s:28:\"An contact has been created \";s:6:\"active\";b:1;s:10:\"methodName\";s:11:\"NotifyOwner\";s:9:\"recepient\";s:36:\"$(assigned_user_id : (Users) email1)\";s:7:\"subject\";s:28:\"Regarding Contact Assignment\";s:7:\"content\";s:384:\"An Contact has been assigned to you on vtigerCRM<br>Details of Contact are :<br><br>Contact Id:<b>$contact_no</b><br>LastName:<b>$lastname</b><br>FirstName:<b>$firstname</b><br>Lead Source:<b>$leadsource</b><br>Department:<b>$department</b><br>Description:<b>$description</b><br><br><br>And <b>CustomerPortal Login Details</b> is sent to the EmailID :-$email<br><br>Thank You<br>Admin\";s:2:\"id\";i:4;}',1),(5,5,'An Potential has been created ','O:11:\"VTEmailTask\":8:{s:18:\"executeImmediately\";b:0;s:10:\"workflowId\";i:5;s:7:\"summary\";s:30:\"An Potential has been created \";s:6:\"active\";b:1;s:9:\"recepient\";s:36:\"$(assigned_user_id : (Users) email1)\";s:7:\"subject\";s:30:\"Regarding Potential Assignment\";s:7:\"content\";s:325:\"An Potential has been assigned to you on vtigerCRM<br>Details of Potential are :<br><br>Potential No:<b>$potential_no</b><br>Potential Name:<b>$potentialname</b><br>Amount:<b>$amount</b><br>Expected Close Date:<b>$closingdate</b><br>Type:<b>$opportunity_type</b><br><br><br>Description :$description<br><br>Thank You<br>Admin\";s:2:\"id\";i:5;}',1),(6,6,'Email Customer Portal Login Details','O:18:\"VTEntityMethodTask\":6:{s:18:\"executeImmediately\";b:1;s:10:\"workflowId\";i:6;s:7:\"summary\";s:35:\"Email Customer Portal Login Details\";s:6:\"active\";b:1;s:10:\"methodName\";s:22:\"SendPortalLoginDetails\";s:2:\"id\";i:6;}',1),(7,7,'Notify Record Owner and the Related Contact when Ticket is created from Portal','O:18:\"VTEntityMethodTask\":6:{s:18:\"executeImmediately\";b:1;s:10:\"workflowId\";i:7;s:7:\"summary\";s:78:\"Notify Record Owner and the Related Contact when Ticket is created from Portal\";s:6:\"active\";b:1;s:10:\"methodName\";s:28:\"NotifyOnPortalTicketCreation\";s:2:\"id\";i:7;}',1),(8,8,'Notify Record Owner when Comment is added to a Ticket from Customer Portal','O:18:\"VTEntityMethodTask\":6:{s:18:\"executeImmediately\";b:1;s:10:\"workflowId\";i:8;s:7:\"summary\";s:74:\"Notify Record Owner when Comment is added to a Ticket from Customer Portal\";s:6:\"active\";b:1;s:10:\"methodName\";s:27:\"NotifyOnPortalTicketComment\";s:2:\"id\";i:8;}',1),(9,9,'Notify Record Owner on Ticket Change, which is not done from Portal','O:18:\"VTEntityMethodTask\":6:{s:18:\"executeImmediately\";b:1;s:10:\"workflowId\";i:9;s:7:\"summary\";s:67:\"Notify Record Owner on Ticket Change, which is not done from Portal\";s:6:\"active\";b:1;s:10:\"methodName\";s:25:\"NotifyOwnerOnTicketChange\";s:2:\"id\";i:9;}',1),(10,9,'Notify Related Customer on Ticket Change, which is not done from Portal','O:18:\"VTEntityMethodTask\":6:{s:18:\"executeImmediately\";b:1;s:10:\"workflowId\";i:9;s:7:\"summary\";s:71:\"Notify Related Customer on Ticket Change, which is not done from Portal\";s:6:\"active\";b:1;s:10:\"methodName\";s:26:\"NotifyParentOnTicketChange\";s:2:\"id\";i:10;}',2),(11,10,'Send Notification Email to Record Owner','O:11:\"VTEmailTask\":8:{s:18:\"executeImmediately\";b:0;s:10:\"workflowId\";i:10;s:7:\"summary\";s:39:\"Send Notification Email to Record Owner\";s:6:\"active\";b:1;s:9:\"recepient\";s:36:\"$(assigned_user_id : (Users) email1)\";s:7:\"subject\";s:17:\"Event :  $subject\";s:7:\"content\";s:817:\"$(assigned_user_id : (Users) first_name) $(assigned_user_id : (Users) last_name) ,<br/><b>Activity Notification Details:</b><br/>Subject             : $subject<br/>Start date and time : $date_start  $time_start ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>End date and time   : $due_date  $time_end ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>Status              : $eventstatus <br/>Priority            : $taskpriority <br/>Related To          : $(parent_id : (Leads) lastname) $(parent_id : (Leads) firstname) $(parent_id : (Accounts) accountname) $(parent_id : (Potentials) potentialname) $(parent_id : (HelpDesk) ticket_title) <br/>Contacts List       : $(contact_id : (Contacts) lastname) $(contact_id : (Contacts) firstname) <br/>Location            : $location <br/>Description         : $description\";s:2:\"id\";i:11;}',1),(12,11,'Send Notification Email to Record Owner','O:11:\"VTEmailTask\":8:{s:18:\"executeImmediately\";b:0;s:10:\"workflowId\";i:11;s:7:\"summary\";s:39:\"Send Notification Email to Record Owner\";s:6:\"active\";b:1;s:9:\"recepient\";s:36:\"$(assigned_user_id : (Users) email1)\";s:7:\"subject\";s:16:\"Task :  $subject\";s:7:\"content\";s:797:\"$(assigned_user_id : (Users) first_name) $(assigned_user_id : (Users) last_name) ,<br/><b>Task Notification Details:</b><br/>Subject : $subject<br/>Start date and time : $date_start  $time_start ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>End date and time   : $due_date ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>Status              : $taskstatus <br/>Priority            : $taskpriority <br/>Related To          : $(parent_id : (Leads) lastname) $(parent_id : (Leads) firstname) $(parent_id : (Accounts) accountname) $(parent_id         : (Potentials) potentialname) $(parent_id : (HelpDesk) ticket_title) <br/>Contacts List       : $(contact_id : (Contacts) lastname) $(contact_id : (Contacts) firstname) <br/>Location            : $location <br/>Description         : $description\";s:2:\"id\";i:12;}',1),(13,12,'update forecast amount','O:18:\"VTUpdateFieldsTask\":6:{s:18:\"executeImmediately\";b:1;s:10:\"workflowId\";i:12;s:7:\"summary\";s:22:\"update forecast amount\";s:6:\"active\";b:1;s:19:\"field_value_mapping\";s:95:\"[{\"fieldname\":\"forecast_amount\",\"valuetype\":\"expression\",\"value\":\"amount * probability / 100\"}]\";s:2:\"id\";i:13;}',1),(14,13,'Mark as Line Completed','O:18:\"VTUpdateFieldsTask\":6:{s:18:\"executeImmediately\";b:1;s:10:\"workflowId\";i:13;s:7:\"summary\";s:22:\"Mark as Line Completed\";s:6:\"active\";b:1;s:19:\"field_value_mapping\";s:77:\"[{\"fieldname\":\"line_completed\",\"valuetype\":\"rawtext\",\"value\":\"true:boolean\"}]\";s:2:\"id\";i:14;}',1),(15,14,'Update product stock','O:18:\"VTEntityMethodTask\":6:{s:18:\"executeImmediately\";b:1;s:10:\"workflowId\";i:14;s:7:\"summary\";s:20:\"Update product stock\";s:6:\"active\";b:1;s:10:\"methodName\";s:15:\"UpdateInventory\";s:2:\"id\";i:15;}',1),(16,15,'Send Reminder Template','O:11:\"VTEmailTask\":13:{s:18:\"executeImmediately\";b:0;s:10:\"workflowId\";i:15;s:7:\"summary\";s:22:\"Send Reminder Template\";s:6:\"active\";b:1;s:7:\"subject\";s:111:\"[Reminder: @ $date_start $time_start] ($(general : (__VtigerMeta__) dbtimezone)) Activity Reminder Notification\";s:7:\"content\";s:285:\"This is a reminder notification for the Activity\n\n Subject : $subject\n Date & Time : $date_start $time_start&nbsp;($(general : (__VtigerMeta__) dbtimezone))\n\n Kindly visit the link for more details on the activity <a href=\'$(general : (__VtigerMeta__) crmdetailviewurl)\'>Click here</a>\";s:8:\"fromname\";s:0:\"\";s:9:\"fromemail\";s:0:\"\";s:7:\"replyto\";s:0:\"\";s:9:\"recepient\";s:87:\"$(assigned_user_id : (Users) email1),$(general : (__VtigerMeta__) Events_Users_Invited)\";s:7:\"emailcc\";s:0:\"\";s:8:\"emailbcc\";s:0:\"\";s:2:\"id\";i:16;}',1),(17,16,'Delayed Task Notification','O:11:\"VTEmailTask\":13:{s:18:\"executeImmediately\";b:0;s:10:\"workflowId\";i:16;s:7:\"summary\";s:25:\"Delayed Task Notification\";s:6:\"active\";b:1;s:7:\"subject\";s:29:\"Task Not completed : $subject\";s:7:\"content\";s:322:\"Dear Admin,<br><br> Please note that there are certain tasks in the system which have not been completed even after 24hours of their existence<br> Subject:: Task Not completed : $subject<br> Assigned To: $(assigned_user_id : (Users) first_name) $(assigned_user_id : (Users) last_name)<br><br>Thank You<br>HelpDesk Team<br>\";s:8:\"fromname\";s:0:\"\";s:9:\"fromemail\";s:0:\"\";s:7:\"replyto\";s:0:\"\";s:9:\"recepient\";s:36:\"$(assigned_user_id : (Users) email1)\";s:7:\"emailcc\";s:0:\"\";s:8:\"emailbcc\";s:0:\"\";s:2:\"id\";i:17;}',1),(18,17,'Product Support Starting','O:11:\"VTEmailTask\":13:{s:18:\"executeImmediately\";b:0;s:10:\"workflowId\";i:17;s:7:\"summary\";s:24:\"Product Support Starting\";s:6:\"active\";b:1;s:7:\"subject\";s:16:\"Support starting\";s:7:\"content\";s:87:\"Hello! Support Starts for $productname\n Congratulations! Your support starts from today\";s:8:\"fromname\";s:0:\"\";s:9:\"fromemail\";s:0:\"\";s:7:\"replyto\";s:0:\"\";s:9:\"recepient\";s:36:\"$(assigned_user_id : (Users) email1)\";s:7:\"emailcc\";s:0:\"\";s:8:\"emailbcc\";s:0:\"\";s:2:\"id\";i:18;}',1),(19,18,'Product Support Ended','O:11:\"VTEmailTask\":13:{s:18:\"executeImmediately\";b:0;s:10:\"workflowId\";i:18;s:7:\"summary\";s:21:\"Product Support Ended\";s:6:\"active\";b:1;s:7:\"subject\";s:19:\"Reg: Support Ending\";s:7:\"content\";s:193:\"Dear Admin,<br><br> This is to bring to your notice that Support Date for the product <b> $productname\n </b> ends shortly. Kindly renew your support please.<br><br>Regards,<br>HelpDesk Team<br>\";s:8:\"fromname\";s:0:\"\";s:9:\"fromemail\";s:0:\"\";s:7:\"replyto\";s:0:\"\";s:9:\"recepient\";s:36:\"$(assigned_user_id : (Users) email1)\";s:7:\"emailcc\";s:0:\"\";s:8:\"emailbcc\";s:0:\"\";s:2:\"id\";i:19;}',1),(20,19,'Client End Support Notification 1 month','O:11:\"VTEmailTask\":13:{s:18:\"executeImmediately\";b:0;s:10:\"workflowId\";i:19;s:7:\"summary\";s:39:\"Client End Support Notification 1 month\";s:6:\"active\";b:0;s:7:\"subject\";s:27:\"End of Support Notification\";s:7:\"content\";s:249:\"Dear $firstname $lastname,<br>This is just a notification mail regarding your support end.<br />\n					<span style=\"font-weight: bold;\">Priority:</span> Normal<br />\n					Your Support is going to expire next month.<br />\n					Please contact support.\";s:8:\"fromname\";s:0:\"\";s:9:\"fromemail\";s:0:\"\";s:7:\"replyto\";s:0:\"\";s:9:\"recepient\";s:6:\"$email\";s:7:\"emailcc\";s:36:\"$(assigned_user_id : (Users) email1)\";s:8:\"emailbcc\";s:0:\"\";s:2:\"id\";i:20;}',1),(21,20,'Client End Support Notification 1 week','O:11:\"VTEmailTask\":13:{s:18:\"executeImmediately\";b:0;s:10:\"workflowId\";i:20;s:7:\"summary\";s:38:\"Client End Support Notification 1 week\";s:6:\"active\";b:0;s:7:\"subject\";s:27:\"End of Support Notification\";s:7:\"content\";s:248:\"Dear $firstname $lastname,<br>This is just a notification mail regarding your support end.<br />\n					<span style=\"font-weight: bold;\">Priority:</span> Urgent<br />\n					Your Support is going to expire next week.<br />\n					Please contact support.\";s:8:\"fromname\";s:0:\"\";s:9:\"fromemail\";s:0:\"\";s:7:\"replyto\";s:0:\"\";s:9:\"recepient\";s:6:\"$email\";s:7:\"emailcc\";s:36:\"$(assigned_user_id : (Users) email1)\";s:8:\"emailbcc\";s:0:\"\";s:2:\"id\";i:21;}',1),(22,21,'Send Task Notification Email to Record Owner','O:11:\"VTEmailTask\":16:{s:18:\"executeImmediately\";b:0;s:15:\"attachmentsinfo\";a:0:{}s:10:\"workflowId\";i:21;s:7:\"summary\";s:44:\"Send Task Notification Email to Record Owner\";s:6:\"active\";b:1;s:8:\"fromname\";s:0:\"\";s:9:\"fromemail\";s:0:\"\";s:7:\"replyto\";s:0:\"\";s:7:\"subject\";s:20:\"Calendar :  $subject\";s:7:\"content\";s:812:\"$(assigned_user_id : (Users) last_name) $(assigned_user_id : (Users) first_name) ,<br/><b>Task Notification Details:</b><br/>Subject : $subject<br/>Start date and time : $dtstart ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>End date and time   : $dtend ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>Event type          : $activitytype <br/>Status              : $eventstatus <br/>Priority            : $taskpriority <br/>Related To          : $(parent_id : (Leads) lastname) $(parent_id : (Leads) firstname) $(parent_id : (Accounts) accountname) $(parent_id : (Potentials) potentialname) $(parent_id : (HelpDesk) ticket_title) <br/>Contact             : $(contact_id : (Contacts) lastname) $(contact_id : (Contacts) firstname) <br/>Location            : $location <br/>Description         : $description\";s:9:\"recepient\";s:36:\"$(assigned_user_id : (Users) email1)\";s:7:\"emailcc\";s:0:\"\";s:8:\"emailbcc\";s:0:\"\";s:13:\"attfieldnames\";s:0:\"\";s:13:\"attachmentids\";s:0:\"\";s:2:\"id\";i:22;}',1),(23,22,'Create Calendar Follow Up','O:18:\"VTCreateEntityTask\":10:{s:18:\"executeImmediately\";b:1;s:10:\"workflowId\";i:22;s:7:\"summary\";s:25:\"Create Calendar Follow Up\";s:6:\"active\";b:1;s:11:\"entity_type\";s:10:\"cbCalendar\";s:15:\"reference_field\";s:11:\"relatedwith\";s:19:\"field_value_mapping\";s:1962:\"[{\"fieldname\":\"subject\",\"modulename\":\"cbCalendar\",\"valuetype\":\"expression\",\"value\":\"concat(\'Follow up: \',subject )\"},{\"fieldname\":\"assigned_user_id\",\"modulename\":\"cbCalendar\",\"valuetype\":\"fieldname\",\"value\":\"assigned_user_id \"},{\"fieldname\":\"dtstart\",\"modulename\":\"cbCalendar\",\"valuetype\":\"fieldname\",\"value\":\"followupdt  \"},{\"fieldname\":\"dtend\",\"modulename\":\"cbCalendar\",\"valuetype\":\"fieldname\",\"value\":\"followupdt \"},{\"fieldname\":\"eventstatus\",\"modulename\":\"cbCalendar\",\"valuetype\":\"rawtext\",\"value\":\"Planned\"},{\"fieldname\":\"taskpriority\",\"modulename\":\"cbCalendar\",\"valuetype\":\"rawtext\",\"value\":\"Medium\"},{\"fieldname\":\"sendnotification\",\"modulename\":\"cbCalendar\",\"valuetype\":\"rawtext\",\"value\":\"true:boolean\"},{\"fieldname\":\"activitytype\",\"modulename\":\"cbCalendar\",\"valuetype\":\"fieldname\",\"value\":\"followuptype \"},{\"fieldname\":\"visibility\",\"modulename\":\"cbCalendar\",\"valuetype\":\"rawtext\",\"value\":\"Private\"},{\"fieldname\":\"duration_hours\",\"modulename\":\"cbCalendar\",\"valuetype\":\"fieldname\",\"value\":\"duration_hours \"},{\"fieldname\":\"duration_minutes\",\"modulename\":\"cbCalendar\",\"valuetype\":\"rawtext\",\"value\":\"15\"},{\"fieldname\":\"location\",\"modulename\":\"cbCalendar\",\"valuetype\":\"fieldname\",\"value\":\"location \"},{\"fieldname\":\"reminder_time\",\"modulename\":\"cbCalendar\",\"valuetype\":\"rawtext\",\"value\":\"0\"},{\"fieldname\":\"recurringtype\",\"modulename\":\"cbCalendar\",\"valuetype\":\"rawtext\",\"value\":\"--None--\"},{\"fieldname\":\"description\",\"modulename\":\"cbCalendar\",\"valuetype\":\"fieldname\",\"value\":\"description \"},{\"fieldname\":\"followupcreate\",\"modulename\":\"cbCalendar\",\"valuetype\":\"rawtext\",\"value\":\"false:boolean\"},{\"fieldname\":\"date_start\",\"modulename\":\"cbCalendar\",\"valuetype\":\"fieldname\",\"value\":\"dtstart \"},{\"fieldname\":\"time_start\",\"modulename\":\"cbCalendar\",\"valuetype\":\"rawtext\",\"value\":\"00:00\"},{\"fieldname\":\"due_date\",\"modulename\":\"cbCalendar\",\"valuetype\":\"fieldname\",\"value\":\"dtend \"},{\"fieldname\":\"time_end\",\"modulename\":\"cbCalendar\",\"valuetype\":\"rawtext\",\"value\":\"00:00\"}]\";s:4:\"test\";s:0:\"\";s:10:\"reevaluate\";i:0;s:2:\"id\";i:23;}',1),(24,23,'Create Calendar Follow Up','O:18:\"VTCreateEntityTask\":10:{s:18:\"executeImmediately\";b:1;s:10:\"workflowId\";i:23;s:7:\"summary\";s:25:\"Create Calendar Follow Up\";s:6:\"active\";b:1;s:11:\"entity_type\";s:10:\"cbCalendar\";s:15:\"reference_field\";s:11:\"relatedwith\";s:19:\"field_value_mapping\";s:1962:\"[{\"fieldname\":\"subject\",\"modulename\":\"cbCalendar\",\"valuetype\":\"expression\",\"value\":\"concat(\'Follow up: \',subject )\"},{\"fieldname\":\"assigned_user_id\",\"modulename\":\"cbCalendar\",\"valuetype\":\"fieldname\",\"value\":\"assigned_user_id \"},{\"fieldname\":\"dtstart\",\"modulename\":\"cbCalendar\",\"valuetype\":\"fieldname\",\"value\":\"followupdt  \"},{\"fieldname\":\"dtend\",\"modulename\":\"cbCalendar\",\"valuetype\":\"fieldname\",\"value\":\"followupdt \"},{\"fieldname\":\"eventstatus\",\"modulename\":\"cbCalendar\",\"valuetype\":\"rawtext\",\"value\":\"Planned\"},{\"fieldname\":\"taskpriority\",\"modulename\":\"cbCalendar\",\"valuetype\":\"rawtext\",\"value\":\"Medium\"},{\"fieldname\":\"sendnotification\",\"modulename\":\"cbCalendar\",\"valuetype\":\"rawtext\",\"value\":\"true:boolean\"},{\"fieldname\":\"activitytype\",\"modulename\":\"cbCalendar\",\"valuetype\":\"fieldname\",\"value\":\"followuptype \"},{\"fieldname\":\"visibility\",\"modulename\":\"cbCalendar\",\"valuetype\":\"rawtext\",\"value\":\"Private\"},{\"fieldname\":\"duration_hours\",\"modulename\":\"cbCalendar\",\"valuetype\":\"fieldname\",\"value\":\"duration_hours \"},{\"fieldname\":\"duration_minutes\",\"modulename\":\"cbCalendar\",\"valuetype\":\"rawtext\",\"value\":\"15\"},{\"fieldname\":\"location\",\"modulename\":\"cbCalendar\",\"valuetype\":\"fieldname\",\"value\":\"location \"},{\"fieldname\":\"reminder_time\",\"modulename\":\"cbCalendar\",\"valuetype\":\"rawtext\",\"value\":\"0\"},{\"fieldname\":\"recurringtype\",\"modulename\":\"cbCalendar\",\"valuetype\":\"rawtext\",\"value\":\"--None--\"},{\"fieldname\":\"description\",\"modulename\":\"cbCalendar\",\"valuetype\":\"fieldname\",\"value\":\"description \"},{\"fieldname\":\"followupcreate\",\"modulename\":\"cbCalendar\",\"valuetype\":\"rawtext\",\"value\":\"false:boolean\"},{\"fieldname\":\"date_start\",\"modulename\":\"cbCalendar\",\"valuetype\":\"fieldname\",\"value\":\"dtstart \"},{\"fieldname\":\"time_start\",\"modulename\":\"cbCalendar\",\"valuetype\":\"rawtext\",\"value\":\"00:00\"},{\"fieldname\":\"due_date\",\"modulename\":\"cbCalendar\",\"valuetype\":\"fieldname\",\"value\":\"dtend \"},{\"fieldname\":\"time_end\",\"modulename\":\"cbCalendar\",\"valuetype\":\"rawtext\",\"value\":\"00:00\"}]\";s:4:\"test\";s:0:\"\";s:10:\"reevaluate\";i:0;s:2:\"id\";i:24;}',1),(25,24,'Delayed Task Notification','O:11:\"VTEmailTask\":14:{s:18:\"executeImmediately\";s:0:\"\";s:15:\"attachmentsinfo\";a:0:{}s:10:\"workflowId\";s:2:\"24\";s:7:\"summary\";s:25:\"Delayed Task Notification\";s:6:\"active\";s:1:\"1\";s:7:\"subject\";s:29:\"Task Not completed : $subject\";s:7:\"content\";s:322:\"Dear Admin,<br><br> Please note that there are certain tasks in the system which have not been completed even after 24hours of their existence<br> Subject:: Task Not completed : $subject<br> Assigned To: $(assigned_user_id : (Users) first_name) $(assigned_user_id : (Users) last_name)<br><br>Thank You<br>HelpDesk Team<br>\";s:8:\"fromname\";s:0:\"\";s:9:\"fromemail\";s:0:\"\";s:7:\"replyto\";s:0:\"\";s:9:\"recepient\";s:36:\"$(assigned_user_id : (Users) email1)\";s:7:\"emailcc\";s:0:\"\";s:8:\"emailbcc\";s:0:\"\";s:2:\"id\";s:2:\"25\";}',1),(26,25,'Send Notification Email to Record Owner','O:11:\"VTEmailTask\":9:{s:18:\"executeImmediately\";s:0:\"\";s:15:\"attachmentsinfo\";a:0:{}s:10:\"workflowId\";s:2:\"25\";s:7:\"summary\";s:39:\"Send Notification Email to Record Owner\";s:6:\"active\";s:1:\"1\";s:9:\"recepient\";s:36:\"$(assigned_user_id : (Users) email1)\";s:7:\"subject\";s:17:\"Event :  $subject\";s:7:\"content\";s:794:\"$(assigned_user_id : (Users) first_name) $(assigned_user_id : (Users) last_name) ,<br/><b>Activity Notification Details:</b><br/>Subject             : $subject<br/>Start date and time : $date_start  $time_start ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>End date and time   : $due_date  $time_end ( $(general : (__VtigerMeta__) dbtimezone) ) <br/>Status              : $eventstatus <br/>Priority            : $taskpriority <br/>Related To          : $(rel_id : (Leads) lastname) $(rel_id : (Leads) firstname) $(rel_id : (Accounts) accountname) $(rel_id : (Potentials) potentialname) $(rel_id : (HelpDesk) ticket_title) <br/>Contacts List       : $(cto_id : (Contacts) lastname) $(cto_id : (Contacts) firstname) <br/>Location            : $location <br/>Description         : $description\";s:2:\"id\";s:2:\"26\";}',1),(27,26,'Send Reminder Template','O:11:\"VTEmailTask\":14:{s:18:\"executeImmediately\";s:0:\"\";s:15:\"attachmentsinfo\";a:0:{}s:10:\"workflowId\";s:2:\"26\";s:7:\"summary\";s:22:\"Send Reminder Template\";s:6:\"active\";s:1:\"1\";s:7:\"subject\";s:111:\"[Reminder: @ $date_start $time_start] ($(general : (__VtigerMeta__) dbtimezone)) Activity Reminder Notification\";s:7:\"content\";s:285:\"This is a reminder notification for the Activity\n\n Subject : $subject\n Date & Time : $date_start $time_start&nbsp;($(general : (__VtigerMeta__) dbtimezone))\n\n Kindly visit the link for more details on the activity <a href=\'$(general : (__VtigerMeta__) crmdetailviewurl)\'>Click here</a>\";s:8:\"fromname\";s:0:\"\";s:9:\"fromemail\";s:0:\"\";s:7:\"replyto\";s:0:\"\";s:9:\"recepient\";s:87:\"$(assigned_user_id : (Users) email1),$(general : (__VtigerMeta__) Events_Users_Invited)\";s:7:\"emailcc\";s:0:\"\";s:8:\"emailbcc\";s:0:\"\";s:2:\"id\";s:2:\"27\";}',1);
/*!40000 ALTER TABLE `com_vtiger_workflowtasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `com_vtiger_workflowtasks_entitymethod`
--

DROP TABLE IF EXISTS `com_vtiger_workflowtasks_entitymethod`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `com_vtiger_workflowtasks_entitymethod` (
  `workflowtasks_entitymethod_id` int(11) NOT NULL,
  `module_name` varchar(100) DEFAULT NULL,
  `method_name` varchar(100) DEFAULT NULL,
  `function_path` varchar(400) DEFAULT NULL,
  `function_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`workflowtasks_entitymethod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `com_vtiger_workflowtasks_entitymethod`
--

LOCK TABLES `com_vtiger_workflowtasks_entitymethod` WRITE;
/*!40000 ALTER TABLE `com_vtiger_workflowtasks_entitymethod` DISABLE KEYS */;
INSERT INTO `com_vtiger_workflowtasks_entitymethod` VALUES (1,'SalesOrder','UpdateInventory','include/InventoryHandler.php','handleInventoryProductRel'),(2,'Invoice','UpdateInventory','include/InventoryHandler.php','handleInventoryProductRel'),(3,'Contacts','SendPortalLoginDetails','modules/Contacts/ContactsHandler.php','Contacts_sendCustomerPortalLoginDetails'),(4,'HelpDesk','NotifyOnPortalTicketCreation','modules/HelpDesk/HelpDeskHandler.php','HelpDesk_nofifyOnPortalTicketCreation'),(5,'HelpDesk','NotifyOnPortalTicketComment','modules/HelpDesk/HelpDeskHandler.php','HelpDesk_notifyOnPortalTicketComment'),(6,'HelpDesk','NotifyOwnerOnTicketChange','modules/HelpDesk/HelpDeskHandler.php','HelpDesk_notifyOwnerOnTicketChange'),(7,'HelpDesk','NotifyParentOnTicketChange','modules/HelpDesk/HelpDeskHandler.php','HelpDesk_notifyParentOnTicketChange'),(8,'Accounts','Update Contact Assigned To','include/wfMethods/updateContactAssignedTo.php','updateContactAssignedTo'),(9,'PurchaseOrder','UpdateInventory','include/InventoryHandler.php','handleInventoryProductRel'),(10,'Contacts','Update Contact Assigned To','include/wfMethods/updateContactAssignedTo.php','updateContactAssignedTo');
/*!40000 ALTER TABLE `com_vtiger_workflowtasks_entitymethod` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `com_vtiger_workflowtasks_entitymethod_seq`
--

DROP TABLE IF EXISTS `com_vtiger_workflowtasks_entitymethod_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `com_vtiger_workflowtasks_entitymethod_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `com_vtiger_workflowtasks_entitymethod_seq`
--

LOCK TABLES `com_vtiger_workflowtasks_entitymethod_seq` WRITE;
/*!40000 ALTER TABLE `com_vtiger_workflowtasks_entitymethod_seq` DISABLE KEYS */;
INSERT INTO `com_vtiger_workflowtasks_entitymethod_seq` VALUES (10);
/*!40000 ALTER TABLE `com_vtiger_workflowtasks_entitymethod_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `com_vtiger_workflowtasks_seq`
--

DROP TABLE IF EXISTS `com_vtiger_workflowtasks_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `com_vtiger_workflowtasks_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `com_vtiger_workflowtasks_seq`
--

LOCK TABLES `com_vtiger_workflowtasks_seq` WRITE;
/*!40000 ALTER TABLE `com_vtiger_workflowtasks_seq` DISABLE KEYS */;
INSERT INTO `com_vtiger_workflowtasks_seq` VALUES (27);
/*!40000 ALTER TABLE `com_vtiger_workflowtasks_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `com_vtiger_workflowtemplates`
--

DROP TABLE IF EXISTS `com_vtiger_workflowtemplates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `com_vtiger_workflowtemplates` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(100) DEFAULT NULL,
  `title` varchar(400) DEFAULT NULL,
  `template` text,
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `com_vtiger_workflowtemplates`
--

LOCK TABLES `com_vtiger_workflowtemplates` WRITE;
/*!40000 ALTER TABLE `com_vtiger_workflowtemplates` DISABLE KEYS */;
/*!40000 ALTER TABLE `com_vtiger_workflowtemplates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `its4you_calendar4you_colors`
--

DROP TABLE IF EXISTS `its4you_calendar4you_colors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `its4you_calendar4you_colors` (
  `userid` int(11) NOT NULL,
  `mode` varchar(100) NOT NULL,
  `entity` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `color` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `its4you_calendar4you_colors`
--

LOCK TABLES `its4you_calendar4you_colors` WRITE;
/*!40000 ALTER TABLE `its4you_calendar4you_colors` DISABLE KEYS */;
/*!40000 ALTER TABLE `its4you_calendar4you_colors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `its4you_calendar4you_event_fields`
--

DROP TABLE IF EXISTS `its4you_calendar4you_event_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `its4you_calendar4you_event_fields` (
  `efid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `event` varchar(200) NOT NULL,
  `type` int(2) NOT NULL,
  `view` varchar(50) NOT NULL,
  `fieldname` varchar(200) NOT NULL,
  PRIMARY KEY (`efid`),
  KEY `userid` (`userid`,`view`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `its4you_calendar4you_event_fields`
--

LOCK TABLES `its4you_calendar4you_event_fields` WRITE;
/*!40000 ALTER TABLE `its4you_calendar4you_event_fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `its4you_calendar4you_event_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `its4you_calendar4you_settings`
--

DROP TABLE IF EXISTS `its4you_calendar4you_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `its4you_calendar4you_settings` (
  `userid` int(11) NOT NULL,
  `dayoftheweek` varchar(100) NOT NULL,
  `show_weekends` int(2) DEFAULT NULL,
  `user_view` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `its4you_calendar4you_settings`
--

LOCK TABLES `its4you_calendar4you_settings` WRITE;
/*!40000 ALTER TABLE `its4you_calendar4you_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `its4you_calendar4you_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `its4you_calendar4you_view`
--

DROP TABLE IF EXISTS `its4you_calendar4you_view`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `its4you_calendar4you_view` (
  `userid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `parent` varchar(255) NOT NULL,
  PRIMARY KEY (`userid`,`type`,`parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `its4you_calendar4you_view`
--

LOCK TABLES `its4you_calendar4you_view` WRITE;
/*!40000 ALTER TABLE `its4you_calendar4you_view` DISABLE KEYS */;
/*!40000 ALTER TABLE `its4you_calendar4you_view` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `its4you_calendar_modulefields`
--

DROP TABLE IF EXISTS `its4you_calendar_modulefields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `its4you_calendar_modulefields` (
  `calmodfields` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(19) NOT NULL,
  `module` varchar(50) NOT NULL,
  `start_field` varchar(30) NOT NULL,
  `start_time` varchar(30) NOT NULL,
  `end_field` varchar(30) NOT NULL,
  `end_time` varchar(30) NOT NULL,
  `subject_fields` varchar(250) NOT NULL,
  `color` varchar(50) NOT NULL,
  PRIMARY KEY (`calmodfields`),
  KEY `userid` (`userid`,`module`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `its4you_calendar_modulefields`
--

LOCK TABLES `its4you_calendar_modulefields` WRITE;
/*!40000 ALTER TABLE `its4you_calendar_modulefields` DISABLE KEYS */;
INSERT INTO `its4you_calendar_modulefields` VALUES (1,1,'Project','startdate','','targetenddate','','progress,projecttype',''),(2,1,'ProjectTask','startdate','','enddate','','Project.projectname,projecttaskname,projecttaskprogress',''),(3,1,'SalesOrder','duedate','','duedate','','hdnGrandTotal',''),(4,1,'cbupdater','execdate','','execdate','','classname,execstate',''),(5,1,'Campaigns','closingdate','','closingdate','','campaignstatus',''),(6,1,'CobroPago','duedate','','duedate','','amount',''),(7,1,'Contacts','birthday','','birthday','','firstname',''),(8,1,'ServiceContracts','start_date','','end_date','','used_units,total_units',''),(9,1,'Invoice','duedate','','duedate','','hdnGrandTotal',''),(10,1,'ProjectMilestone','projectmilestonedate','','projectmilestonedate','','Project.projectname',''),(11,1,'Potentials','closingdate','','closingdate','','sales_stage',''),(12,1,'PurchaseOrder','duedate','','duedate','','hdnGrandTotal',''),(13,1,'Quotes','validtill','','validtill','','hdnGrandTotal',''),(14,1,'Products','sales_start_date','','sales_end_date','','productcode',''),(15,1,'Services','sales_start_date','','sales_end_date','','',''),(16,1,'Assets','dateinservice','','dateinservice','','serialnumber',''),(17,1,'Timecontrol','date_start','time_start','date_end','time_end','totaltime','');
/*!40000 ALTER TABLE `its4you_calendar_modulefields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `its4you_calendar_modulestatus`
--

DROP TABLE IF EXISTS `its4you_calendar_modulestatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `its4you_calendar_modulestatus` (
  `calmodstatus` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(50) NOT NULL,
  `status` varchar(10) NOT NULL,
  `field` varchar(50) NOT NULL,
  `value` varchar(250) NOT NULL,
  `operator` varchar(10) NOT NULL,
  `glue` varchar(3) NOT NULL,
  PRIMARY KEY (`calmodstatus`),
  KEY `module` (`module`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `its4you_calendar_modulestatus`
--

LOCK TABLES `its4you_calendar_modulestatus` WRITE;
/*!40000 ALTER TABLE `its4you_calendar_modulestatus` DISABLE KEYS */;
INSERT INTO `its4you_calendar_modulestatus` VALUES (1,'Project','Planned','projectstatus','completed','n',''),(2,'Project','Planned','projectstatus','delivered','n','AND'),(3,'Project','Planned','projectstatus','in progress','n','AND'),(4,'Project','Held','projectstatus','completed','e',''),(5,'Project','Held','projectstatus','delivered','e','OR'),(6,'Project','Not Held','projectstatus','in progress','e',''),(7,'ProjectTask','Planned','projecttaskprogress','--none--','e',''),(8,'ProjectTask','Held','projecttaskprogress','100%','e',''),(9,'ProjectTask','Not Held','projecttaskprogress','100%','n',''),(10,'ProjectTask','Not Held','projecttaskprogress','--none--','n','AND'),(11,'cbupdater','Planned','execstate','Pending','e',''),(12,'cbupdater','Held','execstate','Executed','e',''),(13,'cbupdater','Not Held','execstate','Pending','e',''),(14,'Campaigns','Held','campaignstatus','Completed','e',''),(15,'Campaigns','Held','campaignstatus','Cancelled','e','OR'),(16,'Campaigns','Not Held','campaignstatus','Active','e',''),(17,'Campaigns','Not Held','campaignstatus','Inactive','e','OR'),(18,'Campaigns','Planned','campaignstatus','--None--','e',''),(19,'Campaigns','Planned','campaignstatus','Planning','e','OR'),(20,'CobroPago','Planned','paid','0','e',''),(21,'CobroPago','Not Held','paid','0','e',''),(22,'CobroPago','Held','paid','1','e',''),(23,'ServiceContracts','Planned','contract_status','In Planning','e',''),(24,'ServiceContracts','Held','contract_status','Complete','e',''),(25,'ServiceContracts','Held','contract_status','Archived','e','OR'),(26,'ServiceContracts','Not Held','contract_status','In Progress','e',''),(27,'ServiceContracts','Not Held','contract_status','On Hold','e','OR'),(28,'Invoice','Planned','invoicestatus','Approved','e',''),(29,'Invoice','Planned','invoicestatus','Sent','e','OR'),(30,'Invoice','Not Held','invoicestatus','AutoCreated','e',''),(31,'Invoice','Not Held','invoicestatus','Created','e','OR'),(32,'Invoice','Held','invoicestatus','Paid','e',''),(33,'Potentials','Not Held','sales_stage','Closed Won','n',''),(34,'Potentials','Not Held','sales_stage','Closed Lost','n','AND'),(35,'Potentials','Planned','sales_stage','Closed Won','n',''),(36,'Potentials','Planned','sales_stage','Closed Lost','n','AND'),(37,'Potentials','Held','sales_stage','Closed Won','e',''),(38,'Potentials','Held','sales_stage','Closed Lost','e','OR'),(39,'PurchaseOrder','Planned','postatus','Created','e',''),(40,'PurchaseOrder','Planned','postatus','Approved','e','OR'),(41,'PurchaseOrder','Held','postatus','Delivered','e',''),(42,'PurchaseOrder','Held','postatus','Received Shipment','e','OR'),(43,'PurchaseOrder','Not Held','postatus','Cancelled','e',''),(44,'SalesOrder','Planned','sostatus','Created','e',''),(45,'SalesOrder','Planned','sostatus','Approved','e','OR'),(46,'SalesOrder','Held','sostatus','Delivered','e',''),(47,'SalesOrder','Not Held','sostatus','Cancelled','e',''),(48,'Quotes','Held','quotestage','Delivered','e',''),(49,'Quotes','Not Held','quotestage','Rejected','e',''),(50,'Quotes','Planned','quotestage','Created','e',''),(51,'Quotes','Planned','quotestage','Reviewed','e','OR'),(52,'Quotes','Planned','quotestage','Accepted','e','OR'),(53,'Products','Planned','discontinued','1','e',''),(54,'Products','Held','discontinued','0','e',''),(55,'Services','Planned','discontinued','1','e',''),(56,'Services','Held','discontinued','0','e',''),(57,'Assets','Held','assetstatus','Out-of-service','e',''),(58,'Assets','Not Held','assetstatus','In Service','e',''),(59,'Timecontrol','Held','date_end','','n',''),(60,'Timecontrol','Not Held','date_end','','e',''),(61,'HelpDesk','Planned','ticketstatus','Closed','n',''),(62,'HelpDesk','Held','ticketstatus','Closed','e',''),(63,'HelpDesk','Not Held','ticketstatus','Closed','n','');
/*!40000 ALTER TABLE `its4you_calendar_modulestatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `its4you_googlesync4you_access`
--

DROP TABLE IF EXISTS `its4you_googlesync4you_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `its4you_googlesync4you_access` (
  `userid` int(11) NOT NULL,
  `google_login` varchar(255) DEFAULT NULL,
  `google_password` varchar(255) DEFAULT NULL,
  `google_apikey` varchar(250) DEFAULT NULL,
  `google_keyfile` varchar(250) DEFAULT NULL,
  `google_clientid` varchar(250) DEFAULT NULL,
  `refresh_token` varchar(250) DEFAULT NULL,
  `synctoken` varchar(250) NOT NULL,
  `googleinsert` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `its4you_googlesync4you_access`
--

LOCK TABLES `its4you_googlesync4you_access` WRITE;
/*!40000 ALTER TABLE `its4you_googlesync4you_access` DISABLE KEYS */;
/*!40000 ALTER TABLE `its4you_googlesync4you_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `its4you_googlesync4you_calendar`
--

DROP TABLE IF EXISTS `its4you_googlesync4you_calendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `its4you_googlesync4you_calendar` (
  `userid` int(11) NOT NULL,
  `event` varchar(200) NOT NULL,
  `calendar` varchar(255) NOT NULL,
  `type` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `its4you_googlesync4you_calendar`
--

LOCK TABLES `its4you_googlesync4you_calendar` WRITE;
/*!40000 ALTER TABLE `its4you_googlesync4you_calendar` DISABLE KEYS */;
/*!40000 ALTER TABLE `its4you_googlesync4you_calendar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `its4you_googlesync4you_dis`
--

DROP TABLE IF EXISTS `its4you_googlesync4you_dis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `its4you_googlesync4you_dis` (
  `userid` int(11) NOT NULL,
  `event` varchar(200) NOT NULL,
  `type` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `its4you_googlesync4you_dis`
--

LOCK TABLES `its4you_googlesync4you_dis` WRITE;
/*!40000 ALTER TABLE `its4you_googlesync4you_dis` DISABLE KEYS */;
/*!40000 ALTER TABLE `its4you_googlesync4you_dis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `its4you_googlesync4you_events`
--

DROP TABLE IF EXISTS `its4you_googlesync4you_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `its4you_googlesync4you_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `crmid` int(11) NOT NULL,
  `geventid` text NOT NULL,
  `userid` int(11) NOT NULL,
  `eventtype` varchar(255) NOT NULL,
  `lastmodified` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `its4you_googlesync4you_events`
--

LOCK TABLES `its4you_googlesync4you_events` WRITE;
/*!40000 ALTER TABLE `its4you_googlesync4you_events` DISABLE KEYS */;
/*!40000 ALTER TABLE `its4you_googlesync4you_events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_account`
--

DROP TABLE IF EXISTS `vtiger_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_account` (
  `accountid` int(19) NOT NULL DEFAULT '0',
  `account_no` varchar(100) NOT NULL,
  `accountname` varchar(100) NOT NULL,
  `parentid` int(19) DEFAULT '0',
  `account_type` varchar(200) DEFAULT NULL,
  `industry` varchar(200) DEFAULT NULL,
  `annualrevenue` decimal(25,6) DEFAULT NULL,
  `rating` varchar(200) DEFAULT NULL,
  `ownership` varchar(50) DEFAULT NULL,
  `siccode` varchar(50) DEFAULT NULL,
  `tickersymbol` varchar(30) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `otherphone` varchar(30) DEFAULT NULL,
  `email1` varchar(100) DEFAULT NULL,
  `email2` varchar(100) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  `fax` varchar(30) DEFAULT NULL,
  `employees` int(10) DEFAULT '0',
  `emailoptout` varchar(3) DEFAULT '0',
  `notify_owner` varchar(3) DEFAULT '0',
  `isconvertedfromlead` varchar(3) DEFAULT NULL,
  `convertedfromlead` int(11) DEFAULT NULL,
  PRIMARY KEY (`accountid`),
  KEY `account_account_type_idx` (`account_type`),
  KEY `email_idx` (`email1`,`email2`),
  CONSTRAINT `fk_1_vtiger_account` FOREIGN KEY (`accountid`) REFERENCES `vtiger_crmentity` (`crmid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_account`
--

LOCK TABLES `vtiger_account` WRITE;
/*!40000 ALTER TABLE `vtiger_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_accountbillads`
--

DROP TABLE IF EXISTS `vtiger_accountbillads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_accountbillads` (
  `accountaddressid` int(19) NOT NULL DEFAULT '0',
  `bill_city` varchar(30) DEFAULT NULL,
  `bill_code` varchar(30) DEFAULT NULL,
  `bill_country` varchar(30) DEFAULT NULL,
  `bill_state` varchar(30) DEFAULT NULL,
  `bill_street` varchar(250) DEFAULT NULL,
  `bill_pobox` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`accountaddressid`),
  CONSTRAINT `fk_1_vtiger_accountbillads` FOREIGN KEY (`accountaddressid`) REFERENCES `vtiger_account` (`accountid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_accountbillads`
--

LOCK TABLES `vtiger_accountbillads` WRITE;
/*!40000 ALTER TABLE `vtiger_accountbillads` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_accountbillads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_accountrating`
--

DROP TABLE IF EXISTS `vtiger_accountrating`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_accountrating` (
  `accountratingid` int(19) NOT NULL AUTO_INCREMENT,
  `rating` varchar(200) NOT NULL,
  `sortorderid` int(19) NOT NULL DEFAULT '0',
  `presence` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`accountratingid`),
  UNIQUE KEY `accountrating_rating_idx` (`rating`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_accountrating`
--

LOCK TABLES `vtiger_accountrating` WRITE;
/*!40000 ALTER TABLE `vtiger_accountrating` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_accountrating` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_accountscf`
--

DROP TABLE IF EXISTS `vtiger_accountscf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_accountscf` (
  `accountid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`accountid`),
  CONSTRAINT `fk_1_vtiger_accountscf` FOREIGN KEY (`accountid`) REFERENCES `vtiger_account` (`accountid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_accountscf`
--

LOCK TABLES `vtiger_accountscf` WRITE;
/*!40000 ALTER TABLE `vtiger_accountscf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_accountscf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_accountshipads`
--

DROP TABLE IF EXISTS `vtiger_accountshipads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_accountshipads` (
  `accountaddressid` int(19) NOT NULL DEFAULT '0',
  `ship_city` varchar(30) DEFAULT NULL,
  `ship_code` varchar(30) DEFAULT NULL,
  `ship_country` varchar(30) DEFAULT NULL,
  `ship_state` varchar(30) DEFAULT NULL,
  `ship_pobox` varchar(30) DEFAULT NULL,
  `ship_street` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`accountaddressid`),
  CONSTRAINT `fk_1_vtiger_accountshipads` FOREIGN KEY (`accountaddressid`) REFERENCES `vtiger_account` (`accountid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_accountshipads`
--

LOCK TABLES `vtiger_accountshipads` WRITE;
/*!40000 ALTER TABLE `vtiger_accountshipads` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_accountshipads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_accounttype`
--

DROP TABLE IF EXISTS `vtiger_accounttype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_accounttype` (
  `accounttypeid` int(19) NOT NULL AUTO_INCREMENT,
  `accounttype` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`accounttypeid`),
  UNIQUE KEY `accounttype_accounttype_idx` (`accounttype`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_accounttype`
--

LOCK TABLES `vtiger_accounttype` WRITE;
/*!40000 ALTER TABLE `vtiger_accounttype` DISABLE KEYS */;
INSERT INTO `vtiger_accounttype` VALUES (1,'--None--',1,1),(2,'Analyst',1,2),(3,'Competitor',1,3),(4,'Customer',1,4),(5,'Integrator',1,5),(6,'Investor',1,6),(7,'Partner',1,7),(8,'Press',1,8),(9,'Prospect',1,9),(10,'Reseller',1,10),(11,'Other',1,11);
/*!40000 ALTER TABLE `vtiger_accounttype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_accounttype_seq`
--

DROP TABLE IF EXISTS `vtiger_accounttype_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_accounttype_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_accounttype_seq`
--

LOCK TABLES `vtiger_accounttype_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_accounttype_seq` DISABLE KEYS */;
INSERT INTO `vtiger_accounttype_seq` VALUES (11);
/*!40000 ALTER TABLE `vtiger_accounttype_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_actionmapping`
--

DROP TABLE IF EXISTS `vtiger_actionmapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_actionmapping` (
  `actionid` int(19) NOT NULL,
  `actionname` varchar(200) NOT NULL,
  `securitycheck` int(19) DEFAULT NULL,
  PRIMARY KEY (`actionid`,`actionname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_actionmapping`
--

LOCK TABLES `vtiger_actionmapping` WRITE;
/*!40000 ALTER TABLE `vtiger_actionmapping` DISABLE KEYS */;
INSERT INTO `vtiger_actionmapping` VALUES (0,'Save',0),(0,'SavePriceBook',1),(0,'SaveVendor',1),(1,'DetailViewAjax',1),(1,'EditView',0),(1,'PriceBookEditView',1),(1,'QuickCreate',1),(1,'VendorEditView',1),(2,'Delete',0),(2,'DeletePriceBook',1),(2,'DeleteVendor',1),(3,'index',0),(3,'Popup',1),(4,'DetailView',0),(4,'PriceBookDetailView',1),(4,'TagCloud',1),(4,'VendorDetailView',1),(5,'Import',0),(6,'Export',0),(7,'CreateView',0),(8,'Merge',0),(9,'ConvertLead',0),(10,'DuplicatesHandling',0);
/*!40000 ALTER TABLE `vtiger_actionmapping` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_activity`
--

DROP TABLE IF EXISTS `vtiger_activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_activity` (
  `activityid` int(19) NOT NULL DEFAULT '0',
  `subject` varchar(255) DEFAULT NULL,
  `semodule` varchar(20) DEFAULT NULL,
  `activitytype` varchar(200) NOT NULL,
  `date_start` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `time_start` varchar(50) DEFAULT NULL,
  `time_end` varchar(50) DEFAULT NULL,
  `sendnotification` varchar(3) NOT NULL DEFAULT '0',
  `duration_hours` varchar(200) DEFAULT NULL,
  `duration_minutes` varchar(200) DEFAULT NULL,
  `status` varchar(200) DEFAULT NULL,
  `eventstatus` varchar(200) DEFAULT NULL,
  `priority` varchar(200) DEFAULT NULL,
  `location` varchar(150) DEFAULT NULL,
  `notime` varchar(3) NOT NULL DEFAULT '0',
  `visibility` varchar(50) NOT NULL DEFAULT 'all',
  `recurringtype` varchar(200) DEFAULT NULL,
  `rel_id` int(19) DEFAULT '0',
  `cto_id` int(19) DEFAULT '0',
  `dtstart` datetime DEFAULT NULL,
  `dtend` datetime DEFAULT NULL,
  `followupdt` datetime DEFAULT NULL,
  `followuptype` varchar(150) DEFAULT NULL,
  `followupcreate` varchar(3) DEFAULT NULL,
  `relatedwith` int(11) DEFAULT NULL,
  PRIMARY KEY (`activityid`),
  KEY `activity_activityid_subject_idx` (`activityid`,`subject`),
  KEY `activity_activitytype_date_start_idx` (`activitytype`,`date_start`),
  KEY `activity_date_start_due_date_idx` (`date_start`,`due_date`),
  KEY `activity_date_start_time_start_idx` (`date_start`,`time_start`),
  KEY `activity_eventstatus_idx` (`eventstatus`),
  KEY `activity_status_idx` (`status`),
  KEY `activity_activitytype_idx` (`activitytype`),
  CONSTRAINT `fk_1_vtiger_activity` FOREIGN KEY (`activityid`) REFERENCES `vtiger_crmentity` (`crmid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_activity`
--

LOCK TABLES `vtiger_activity` WRITE;
/*!40000 ALTER TABLE `vtiger_activity` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_activity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_activity_reminder`
--

DROP TABLE IF EXISTS `vtiger_activity_reminder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_activity_reminder` (
  `activity_id` int(11) NOT NULL,
  `reminder_time` int(11) NOT NULL,
  `reminder_sent` int(2) NOT NULL,
  `recurringid` int(19) NOT NULL,
  PRIMARY KEY (`activity_id`,`recurringid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_activity_reminder`
--

LOCK TABLES `vtiger_activity_reminder` WRITE;
/*!40000 ALTER TABLE `vtiger_activity_reminder` DISABLE KEYS */;
INSERT INTO `vtiger_activity_reminder` VALUES (145,0,0,0);
/*!40000 ALTER TABLE `vtiger_activity_reminder` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_activity_reminder_popup`
--

DROP TABLE IF EXISTS `vtiger_activity_reminder_popup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_activity_reminder_popup` (
  `reminderid` int(19) NOT NULL AUTO_INCREMENT,
  `semodule` varchar(100) NOT NULL,
  `recordid` int(19) NOT NULL,
  `date_start` date NOT NULL,
  `time_start` varchar(100) NOT NULL,
  `status` int(2) NOT NULL,
  PRIMARY KEY (`reminderid`),
  KEY `activity_reminder_popup_recordid_idx` (`recordid`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_activity_reminder_popup`
--

LOCK TABLES `vtiger_activity_reminder_popup` WRITE;
/*!40000 ALTER TABLE `vtiger_activity_reminder_popup` DISABLE KEYS */;
INSERT INTO `vtiger_activity_reminder_popup` VALUES (1,'Calendar',116,'2006-07-11','18:20',1),(2,'Calendar',117,'2006-06-12','11:42',1),(3,'Calendar',118,'2006-04-09','09:07',1),(4,'Calendar',119,'2006-04-11','23:19',1),(5,'Calendar',120,'2006-01-12','15:58',1),(6,'Calendar',121,'2006-04-13','21:10',1),(7,'Calendar',145,'2014-10-08','16:25:00',0);
/*!40000 ALTER TABLE `vtiger_activity_reminder_popup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_activity_view`
--

DROP TABLE IF EXISTS `vtiger_activity_view`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_activity_view` (
  `activity_viewid` int(19) NOT NULL AUTO_INCREMENT,
  `activity_view` varchar(200) NOT NULL,
  `sortorderid` int(19) NOT NULL DEFAULT '0',
  `presence` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`activity_viewid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_activity_view`
--

LOCK TABLES `vtiger_activity_view` WRITE;
/*!40000 ALTER TABLE `vtiger_activity_view` DISABLE KEYS */;
INSERT INTO `vtiger_activity_view` VALUES (1,'Today',0,1),(2,'This Week',1,1),(3,'This Month',2,1),(4,'This Year',3,1);
/*!40000 ALTER TABLE `vtiger_activity_view` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_activity_view_seq`
--

DROP TABLE IF EXISTS `vtiger_activity_view_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_activity_view_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_activity_view_seq`
--

LOCK TABLES `vtiger_activity_view_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_activity_view_seq` DISABLE KEYS */;
INSERT INTO `vtiger_activity_view_seq` VALUES (4);
/*!40000 ALTER TABLE `vtiger_activity_view_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_activitycf`
--

DROP TABLE IF EXISTS `vtiger_activitycf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_activitycf` (
  `activityid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`activityid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_activitycf`
--

LOCK TABLES `vtiger_activitycf` WRITE;
/*!40000 ALTER TABLE `vtiger_activitycf` DISABLE KEYS */;
INSERT INTO `vtiger_activitycf` VALUES (116),(117),(118),(119),(120),(121),(145);
/*!40000 ALTER TABLE `vtiger_activitycf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_activityproductrel`
--

DROP TABLE IF EXISTS `vtiger_activityproductrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_activityproductrel` (
  `activityid` int(19) NOT NULL DEFAULT '0',
  `productid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`activityid`,`productid`),
  KEY `activityproductrel_activityid_idx` (`activityid`),
  KEY `activityproductrel_productid_idx` (`productid`),
  CONSTRAINT `fk_2_vtiger_activityproductrel` FOREIGN KEY (`productid`) REFERENCES `vtiger_products` (`productid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_activityproductrel`
--

LOCK TABLES `vtiger_activityproductrel` WRITE;
/*!40000 ALTER TABLE `vtiger_activityproductrel` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_activityproductrel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_activitytype`
--

DROP TABLE IF EXISTS `vtiger_activitytype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_activitytype` (
  `activitytypeid` int(19) NOT NULL AUTO_INCREMENT,
  `activitytype` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`activitytypeid`),
  UNIQUE KEY `activitytype_activitytype_idx` (`activitytype`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_activitytype`
--

LOCK TABLES `vtiger_activitytype` WRITE;
/*!40000 ALTER TABLE `vtiger_activitytype` DISABLE KEYS */;
INSERT INTO `vtiger_activitytype` VALUES (1,'Call',0,12),(2,'Meeting',0,13),(3,'Emails',1,534),(4,'Task',1,535);
/*!40000 ALTER TABLE `vtiger_activitytype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_activitytype_seq`
--

DROP TABLE IF EXISTS `vtiger_activitytype_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_activitytype_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_activitytype_seq`
--

LOCK TABLES `vtiger_activitytype_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_activitytype_seq` DISABLE KEYS */;
INSERT INTO `vtiger_activitytype_seq` VALUES (4);
/*!40000 ALTER TABLE `vtiger_activitytype_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_assets`
--

DROP TABLE IF EXISTS `vtiger_assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_assets` (
  `assetsid` int(11) NOT NULL,
  `asset_no` varchar(30) NOT NULL,
  `account` int(19) DEFAULT NULL,
  `product` int(19) DEFAULT NULL,
  `serialnumber` varchar(200) DEFAULT NULL,
  `datesold` date DEFAULT NULL,
  `dateinservice` date DEFAULT NULL,
  `assetstatus` varchar(200) DEFAULT 'In Service',
  `tagnumber` varchar(300) DEFAULT NULL,
  `invoiceid` int(19) DEFAULT NULL,
  `shippingmethod` varchar(200) DEFAULT NULL,
  `shippingtrackingnumber` varchar(200) DEFAULT NULL,
  `assetname` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`assetsid`),
  KEY `account` (`account`),
  CONSTRAINT `fk_1_vtiger_assets` FOREIGN KEY (`assetsid`) REFERENCES `vtiger_crmentity` (`crmid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_assets`
--

LOCK TABLES `vtiger_assets` WRITE;
/*!40000 ALTER TABLE `vtiger_assets` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_assets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_assetscf`
--

DROP TABLE IF EXISTS `vtiger_assetscf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_assetscf` (
  `assetsid` int(19) NOT NULL,
  PRIMARY KEY (`assetsid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_assetscf`
--

LOCK TABLES `vtiger_assetscf` WRITE;
/*!40000 ALTER TABLE `vtiger_assetscf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_assetscf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_assetstatus`
--

DROP TABLE IF EXISTS `vtiger_assetstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_assetstatus` (
  `assetstatusid` int(11) NOT NULL AUTO_INCREMENT,
  `assetstatus` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`assetstatusid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_assetstatus`
--

LOCK TABLES `vtiger_assetstatus` WRITE;
/*!40000 ALTER TABLE `vtiger_assetstatus` DISABLE KEYS */;
INSERT INTO `vtiger_assetstatus` VALUES (1,'In Service',1,245),(2,'Out-of-service',1,246);
/*!40000 ALTER TABLE `vtiger_assetstatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_assetstatus_seq`
--

DROP TABLE IF EXISTS `vtiger_assetstatus_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_assetstatus_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_assetstatus_seq`
--

LOCK TABLES `vtiger_assetstatus_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_assetstatus_seq` DISABLE KEYS */;
INSERT INTO `vtiger_assetstatus_seq` VALUES (2);
/*!40000 ALTER TABLE `vtiger_assetstatus_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_asterisk`
--

DROP TABLE IF EXISTS `vtiger_asterisk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_asterisk` (
  `server` varchar(30) DEFAULT NULL,
  `port` varchar(30) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `version` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_asterisk`
--

LOCK TABLES `vtiger_asterisk` WRITE;
/*!40000 ALTER TABLE `vtiger_asterisk` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_asterisk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_asteriskextensions`
--

DROP TABLE IF EXISTS `vtiger_asteriskextensions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_asteriskextensions` (
  `userid` int(11) DEFAULT NULL,
  `asterisk_extension` varchar(50) DEFAULT NULL,
  `use_asterisk` varchar(3) DEFAULT NULL,
  UNIQUE KEY `asteriskextensions_userid_uniq` (`userid`),
  KEY `asteriskextensions_userid_idx` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_asteriskextensions`
--

LOCK TABLES `vtiger_asteriskextensions` WRITE;
/*!40000 ALTER TABLE `vtiger_asteriskextensions` DISABLE KEYS */;
INSERT INTO `vtiger_asteriskextensions` VALUES (1,NULL,NULL);
/*!40000 ALTER TABLE `vtiger_asteriskextensions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_asteriskincomingcalls`
--

DROP TABLE IF EXISTS `vtiger_asteriskincomingcalls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_asteriskincomingcalls` (
  `from_number` varchar(50) DEFAULT NULL,
  `from_name` varchar(50) DEFAULT NULL,
  `to_number` varchar(50) DEFAULT NULL,
  `callertype` varchar(30) DEFAULT NULL,
  `flag` int(19) DEFAULT NULL,
  `timer` int(19) DEFAULT NULL,
  `refuid` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_asteriskincomingcalls`
--

LOCK TABLES `vtiger_asteriskincomingcalls` WRITE;
/*!40000 ALTER TABLE `vtiger_asteriskincomingcalls` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_asteriskincomingcalls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_asteriskincomingevents`
--

DROP TABLE IF EXISTS `vtiger_asteriskincomingevents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_asteriskincomingevents` (
  `uid` varchar(255) NOT NULL,
  `channel` varchar(100) DEFAULT NULL,
  `from_number` bigint(20) DEFAULT NULL,
  `from_name` varchar(100) DEFAULT NULL,
  `to_number` bigint(20) DEFAULT NULL,
  `callertype` varchar(100) DEFAULT NULL,
  `timer` int(20) DEFAULT NULL,
  `flag` varchar(3) DEFAULT NULL,
  `pbxrecordid` int(19) DEFAULT NULL,
  `relcrmid` int(19) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `asteriskincomingevents_relcrmid_idx` (`relcrmid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_asteriskincomingevents`
--

LOCK TABLES `vtiger_asteriskincomingevents` WRITE;
/*!40000 ALTER TABLE `vtiger_asteriskincomingevents` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_asteriskincomingevents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_attachments`
--

DROP TABLE IF EXISTS `vtiger_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_attachments` (
  `attachmentsid` int(19) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `type` varchar(100) DEFAULT NULL,
  `path` varchar(550) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`attachmentsid`),
  CONSTRAINT `fk_1_vtiger_attachments` FOREIGN KEY (`attachmentsid`) REFERENCES `vtiger_crmentity` (`crmid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_attachments`
--

LOCK TABLES `vtiger_attachments` WRITE;
/*!40000 ALTER TABLE `vtiger_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_attachmentsfolder`
--

DROP TABLE IF EXISTS `vtiger_attachmentsfolder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_attachmentsfolder` (
  `folderid` int(19) NOT NULL AUTO_INCREMENT,
  `foldername` varchar(200) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `createdby` int(19) NOT NULL,
  `sequence` int(19) DEFAULT NULL,
  PRIMARY KEY (`folderid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_attachmentsfolder`
--

LOCK TABLES `vtiger_attachmentsfolder` WRITE;
/*!40000 ALTER TABLE `vtiger_attachmentsfolder` DISABLE KEYS */;
INSERT INTO `vtiger_attachmentsfolder` VALUES (1,'Default','This is a Default Folder',1,1);
/*!40000 ALTER TABLE `vtiger_attachmentsfolder` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_attachmentsfolder_seq`
--

DROP TABLE IF EXISTS `vtiger_attachmentsfolder_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_attachmentsfolder_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_attachmentsfolder_seq`
--

LOCK TABLES `vtiger_attachmentsfolder_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_attachmentsfolder_seq` DISABLE KEYS */;
INSERT INTO `vtiger_attachmentsfolder_seq` VALUES (1);
/*!40000 ALTER TABLE `vtiger_attachmentsfolder_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_audit_trial`
--

DROP TABLE IF EXISTS `vtiger_audit_trial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_audit_trial` (
  `auditid` int(19) NOT NULL,
  `userid` int(19) DEFAULT NULL,
  `module` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `recordid` varchar(20) DEFAULT NULL,
  `actiondate` datetime DEFAULT NULL,
  PRIMARY KEY (`auditid`),
  KEY `audit_trial_userid_idx` (`userid`),
  KEY `actiondate` (`actiondate`),
  KEY `module` (`module`),
  KEY `recordid` (`recordid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_audit_trial`
--

LOCK TABLES `vtiger_audit_trial` WRITE;
/*!40000 ALTER TABLE `vtiger_audit_trial` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_audit_trial` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_blocks`
--

DROP TABLE IF EXISTS `vtiger_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_blocks` (
  `blockid` int(19) NOT NULL,
  `tabid` int(19) NOT NULL,
  `blocklabel` varchar(100) NOT NULL,
  `sequence` int(10) DEFAULT NULL,
  `show_title` int(2) DEFAULT NULL,
  `visible` int(2) NOT NULL DEFAULT '0',
  `create_view` int(2) NOT NULL DEFAULT '0',
  `edit_view` int(2) NOT NULL DEFAULT '0',
  `detail_view` int(2) NOT NULL DEFAULT '0',
  `display_status` int(1) NOT NULL DEFAULT '1',
  `iscustom` int(1) NOT NULL DEFAULT '0',
  `isrelatedlist` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`blockid`),
  KEY `block_tabid_idx` (`tabid`),
  KEY `sequence` (`sequence`),
  CONSTRAINT `fk_1_vtiger_blocks` FOREIGN KEY (`tabid`) REFERENCES `vtiger_tab` (`tabid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_blocks`
--

LOCK TABLES `vtiger_blocks` WRITE;
/*!40000 ALTER TABLE `vtiger_blocks` DISABLE KEYS */;
INSERT INTO `vtiger_blocks` VALUES (1,2,'LBL_OPPORTUNITY_INFORMATION',1,0,0,0,0,0,1,0,0),(2,2,'LBL_CUSTOM_INFORMATION',2,0,0,0,0,0,1,0,0),(3,2,'LBL_DESCRIPTION_INFORMATION',3,0,0,0,0,0,1,0,0),(4,4,'LBL_CONTACT_INFORMATION',1,0,0,0,0,0,1,0,0),(5,4,'LBL_CUSTOM_INFORMATION',2,0,0,0,0,0,1,0,0),(6,4,'LBL_CUSTOMER_PORTAL_INFORMATION',3,0,0,0,0,0,1,0,0),(7,4,'LBL_ADDRESS_INFORMATION',4,0,0,0,0,0,1,0,0),(8,4,'LBL_DESCRIPTION_INFORMATION',5,0,0,0,0,0,1,0,0),(9,6,'LBL_ACCOUNT_INFORMATION',1,0,0,0,0,0,1,0,0),(10,6,'LBL_CUSTOM_INFORMATION',2,0,0,0,0,0,1,0,0),(11,6,'LBL_ADDRESS_INFORMATION',3,0,0,0,0,0,1,0,0),(12,6,'LBL_DESCRIPTION_INFORMATION',4,0,0,0,0,0,1,0,0),(13,7,'LBL_LEAD_INFORMATION',1,0,0,0,0,0,1,0,0),(14,7,'LBL_CUSTOM_INFORMATION',2,0,0,0,0,0,1,0,0),(15,7,'LBL_ADDRESS_INFORMATION',3,0,0,0,0,0,1,0,0),(16,7,'LBL_DESCRIPTION_INFORMATION',4,0,0,0,0,0,1,0,0),(17,8,'LBL_NOTE_INFORMATION',1,0,0,0,0,0,1,0,0),(18,8,'LBL_FILE_INFORMATION',3,1,0,0,0,0,1,0,0),(19,9,'LBL_TASK_INFORMATION',1,0,0,0,0,0,1,0,0),(20,9,'',2,1,0,0,0,0,1,0,0),(21,10,'LBL_EMAIL_INFORMATION',1,0,0,0,0,0,1,0,0),(22,10,'',2,1,0,0,0,0,1,0,0),(23,10,'',3,1,0,0,0,0,1,0,0),(24,10,'',4,1,0,0,0,0,1,0,0),(25,13,'LBL_TICKET_INFORMATION',1,0,0,0,0,0,1,0,0),(26,13,'',2,1,0,0,0,0,1,0,0),(27,13,'LBL_CUSTOM_INFORMATION',3,0,0,0,0,0,1,0,0),(28,13,'LBL_DESCRIPTION_INFORMATION',4,0,0,0,0,0,1,0,0),(29,13,'LBL_TICKET_RESOLUTION',5,0,0,0,0,0,1,0,0),(30,13,'LBL_COMMENTS',6,0,0,1,0,0,1,0,0),(31,14,'LBL_PRODUCT_INFORMATION',1,0,0,0,0,0,1,0,0),(32,14,'LBL_PRICING_INFORMATION',2,0,0,0,0,0,1,0,0),(33,14,'LBL_STOCK_INFORMATION',3,0,0,0,0,0,1,0,0),(34,14,'LBL_CUSTOM_INFORMATION',4,0,0,0,0,0,1,0,0),(35,14,'LBL_IMAGE_INFORMATION',5,0,0,0,0,0,1,0,0),(36,14,'LBL_DESCRIPTION_INFORMATION',6,0,0,0,0,0,1,0,0),(37,15,'LBL_FAQ_INFORMATION',1,0,0,0,0,0,1,0,0),(38,15,'LBL_COMMENT_INFORMATION',4,0,0,1,0,0,1,0,0),(39,16,'LBL_EVENT_INFORMATION',1,0,0,0,0,0,1,0,0),(40,16,'',2,1,0,0,0,0,1,0,0),(41,16,'',3,1,0,0,0,0,1,0,0),(42,18,'LBL_VENDOR_INFORMATION',1,0,0,0,0,0,1,0,0),(43,18,'LBL_CUSTOM_INFORMATION',2,0,0,0,0,0,1,0,0),(44,18,'LBL_VENDOR_ADDRESS_INFORMATION',3,0,0,0,0,0,1,0,0),(45,18,'LBL_DESCRIPTION_INFORMATION',4,0,0,0,0,0,1,0,0),(46,19,'LBL_PRICEBOOK_INFORMATION',1,0,0,0,0,0,1,0,0),(47,19,'LBL_CUSTOM_INFORMATION',2,0,0,0,0,0,1,0,0),(48,19,'LBL_DESCRIPTION_INFORMATION',3,0,0,0,0,0,1,0,0),(49,20,'LBL_QUOTE_INFORMATION',1,0,0,0,0,0,1,0,0),(50,20,'LBL_CUSTOM_INFORMATION',2,0,0,0,0,0,1,0,0),(51,20,'LBL_ADDRESS_INFORMATION',3,0,0,0,0,0,1,0,0),(52,20,'LBL_RELATED_PRODUCTS',4,0,0,0,0,0,1,0,0),(53,20,'LBL_TERMS_INFORMATION',5,0,0,0,0,0,1,0,0),(54,20,'LBL_DESCRIPTION_INFORMATION',6,0,0,0,0,0,1,0,0),(55,21,'LBL_PO_INFORMATION',1,0,0,0,0,0,1,0,0),(56,21,'LBL_CUSTOM_INFORMATION',2,0,0,0,0,0,1,0,0),(57,21,'LBL_ADDRESS_INFORMATION',3,0,0,0,0,0,1,0,0),(58,21,'LBL_RELATED_PRODUCTS',4,0,0,0,0,0,1,0,0),(59,21,'LBL_TERMS_INFORMATION',5,0,0,0,0,0,1,0,0),(60,21,'LBL_DESCRIPTION_INFORMATION',6,0,0,0,0,0,1,0,0),(61,22,'LBL_SO_INFORMATION',1,0,0,0,0,0,1,0,0),(62,22,'LBL_CUSTOM_INFORMATION',3,0,0,0,0,0,1,0,0),(63,22,'LBL_ADDRESS_INFORMATION',4,0,0,0,0,0,1,0,0),(64,22,'LBL_RELATED_PRODUCTS',5,0,0,0,0,0,1,0,0),(65,22,'LBL_TERMS_INFORMATION',6,0,0,0,0,0,1,0,0),(66,22,'LBL_DESCRIPTION_INFORMATION',7,0,0,0,0,0,1,0,0),(67,23,'LBL_INVOICE_INFORMATION',1,0,0,0,0,0,1,0,0),(68,23,'LBL_CUSTOM_INFORMATION',2,0,0,0,0,0,1,0,0),(69,23,'LBL_ADDRESS_INFORMATION',3,0,0,0,0,0,1,0,0),(70,23,'LBL_RELATED_PRODUCTS',4,0,0,0,0,0,1,0,0),(71,23,'LBL_TERMS_INFORMATION',5,0,0,0,0,0,1,0,0),(72,23,'LBL_DESCRIPTION_INFORMATION',6,0,0,0,0,0,1,0,0),(73,4,'LBL_IMAGE_INFORMATION',6,0,0,0,0,0,1,0,0),(74,26,'LBL_CAMPAIGN_INFORMATION',1,0,0,0,0,0,1,0,0),(75,26,'LBL_CUSTOM_INFORMATION',2,0,0,0,0,0,1,0,0),(76,26,'LBL_EXPECTATIONS_AND_ACTUALS',3,0,0,0,0,0,1,0,0),(77,29,'LBL_USERLOGIN_ROLE',1,0,0,0,0,0,1,0,0),(78,29,'LBL_CURRENCY_CONFIGURATION',2,0,0,0,0,0,1,0,0),(79,29,'LBL_MORE_INFORMATION',3,0,0,0,0,0,1,0,0),(80,29,'LBL_ADDRESS_INFORMATION',4,0,0,0,0,0,1,0,0),(81,26,'LBL_DESCRIPTION_INFORMATION',4,0,0,0,0,0,1,0,0),(82,29,'LBL_USER_IMAGE_INFORMATION',4,0,0,0,0,0,1,0,0),(83,29,'LBL_USER_ADV_OPTIONS',5,0,0,0,0,0,1,0,0),(84,8,'LBL_DESCRIPTION',2,0,0,0,0,0,1,0,0),(85,22,'Recurring Invoice Information',2,0,0,0,0,0,1,0,0),(86,9,'LBL_CUSTOM_INFORMATION',3,0,0,0,0,0,1,0,0),(87,16,'LBL_CUSTOM_INFORMATION',4,0,0,0,0,0,1,0,0),(88,36,'LBL_CALL_INFORMATION',1,0,0,0,0,0,1,0,0),(89,36,'LBL_CUSTOM_INFORMATION',2,0,0,0,0,0,1,0,0),(90,29,'Asterisk Configuration',6,0,0,0,0,0,1,0,0),(91,37,'LBL_SERVICE_CONTRACT_INFORMATION',1,0,0,0,0,0,1,0,0),(92,37,'LBL_CUSTOM_INFORMATION',2,0,0,0,0,0,1,0,0),(93,38,'LBL_SERVICE_INFORMATION',1,0,0,0,0,0,1,0,0),(94,38,'LBL_PRICING_INFORMATION',2,0,0,0,0,0,1,0,0),(95,38,'LBL_CUSTOM_INFORMATION',3,0,0,0,0,0,1,0,0),(96,38,'LBL_DESCRIPTION_INFORMATION',4,0,0,0,0,0,1,0,0),(97,41,'LBL_cbupdater_INFORMATION',1,0,0,0,0,0,1,0,0),(98,41,'LBL_CUSTOM_INFORMATION',2,0,0,0,0,0,1,0,0),(99,41,'LBL_DESCRIPTION_INFORMATION',3,0,0,0,0,0,1,0,0),(100,42,'LBL_COBROPAGO_INFORMATION',1,0,0,0,0,0,1,0,0),(101,42,'LBL_CUSTOM_INFORMATION',2,0,0,0,0,0,1,0,0),(102,42,'LBL_DESCRIPTION_INFORMATION',3,0,0,0,0,0,1,0,0),(103,43,'LBL_ASSET_INFORMATION',1,0,0,0,0,0,1,0,0),(104,43,'LBL_CUSTOM_INFORMATION',2,0,0,0,0,0,1,0,0),(105,43,'LBL_DESCRIPTION_INFORMATION',3,0,0,0,0,0,1,0,0),(106,47,'LBL_MODCOMMENTS_INFORMATION',1,0,0,0,0,0,1,0,0),(107,47,'LBL_OTHER_INFORMATION',2,0,0,0,0,0,1,0,0),(108,47,'LBL_CUSTOM_INFORMATION',3,0,0,0,0,0,1,0,0),(109,48,'LBL_PROJECT_MILESTONE_INFORMATION',1,0,0,0,0,0,1,0,0),(110,48,'LBL_CUSTOM_INFORMATION',2,0,0,0,0,0,1,0,0),(111,48,'LBL_DESCRIPTION_INFORMATION',3,0,0,0,0,0,1,0,0),(112,49,'LBL_PROJECT_TASK_INFORMATION',1,0,0,0,0,0,1,0,0),(113,49,'LBL_CUSTOM_INFORMATION',2,0,0,0,0,0,1,0,0),(114,49,'LBL_DESCRIPTION_INFORMATION',3,0,0,0,0,0,1,0,0),(115,50,'LBL_PROJECT_INFORMATION',1,0,0,0,0,0,1,0,0),(116,50,'LBL_CUSTOM_INFORMATION',2,0,0,0,0,0,1,0,0),(117,50,'LBL_DESCRIPTION_INFORMATION',3,0,0,0,0,0,1,0,0),(118,52,'LBL_SMSNOTIFIER_INFORMATION',1,0,0,0,0,0,1,0,0),(119,52,'LBL_CUSTOM_INFORMATION',2,0,0,0,0,0,1,0,0),(120,52,'StatusInformation',3,0,0,0,0,0,1,0,0),(121,56,'LBL_GLOBAL_VARIABLE_INFORMATION',1,0,0,0,0,0,1,0,0),(122,56,'LBL_DESCRIPTION_INFORMATION',2,0,0,0,0,0,1,0,0),(123,57,'LBL_INVENTORYDETAILS_INFORMATION',1,0,0,0,0,0,1,0,0),(124,57,'LBL_CUSTOM_INFORMATION',2,0,0,0,0,0,1,0,0),(125,57,'LBL_DESCRIPTION_INFORMATION',3,0,0,0,0,0,1,0,0),(126,29,'LBL_CALENDAR_SETTINGS',2,0,0,0,0,0,1,0,0),(127,58,'LBL_MAP_INFORMATION',1,0,0,0,0,0,1,0,0),(128,58,'LBL_CUSTOM_INFORMATION',2,0,0,0,0,0,1,0,0),(129,58,'LBL_DESCRIPTION_INFORMATION',3,0,0,0,0,0,1,0,0),(130,56,'GVarDefinitions',3,0,0,0,0,0,0,0,0),(131,62,'LBL_CBTERMCONDITIONS_INFORMATION',1,0,0,0,0,0,1,0,0),(132,62,'LBL_TERMANDCONDITIONS',2,0,0,0,0,0,1,0,0),(133,62,'LBL_DESCRIPTION_INFORMATION',3,0,0,0,0,0,1,0,0),(134,63,'LBL_TASK_INFORMATION',1,0,0,0,0,0,1,0,0),(135,63,'',2,0,0,0,0,0,1,0,0),(136,63,'LBL_CUSTOM_INFORMATION',3,0,0,0,0,0,1,0,0),(137,63,'LBL_FOLLOWUP_INFORMATION',4,0,0,0,0,0,1,0,0),(138,63,'EventAdvancedOptions',5,0,0,0,0,0,1,0,0),(140,64,'LBL_CBTRANSLATION_INFORMATION',1,0,0,0,0,0,1,0,0),(141,64,'LBL_CUSTOM_INFORMATION',2,0,0,0,0,0,1,0,0);
/*!40000 ALTER TABLE `vtiger_blocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_blocks_seq`
--

DROP TABLE IF EXISTS `vtiger_blocks_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_blocks_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_blocks_seq`
--

LOCK TABLES `vtiger_blocks_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_blocks_seq` DISABLE KEYS */;
INSERT INTO `vtiger_blocks_seq` VALUES (141);
/*!40000 ALTER TABLE `vtiger_blocks_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_campaign`
--

DROP TABLE IF EXISTS `vtiger_campaign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_campaign` (
  `campaign_no` varchar(100) NOT NULL,
  `campaignname` varchar(255) DEFAULT NULL,
  `campaigntype` varchar(200) DEFAULT NULL,
  `campaignstatus` varchar(200) DEFAULT NULL,
  `expectedrevenue` decimal(28,6) DEFAULT NULL,
  `budgetcost` decimal(28,6) DEFAULT NULL,
  `actualcost` decimal(28,6) DEFAULT NULL,
  `expectedresponse` varchar(200) DEFAULT NULL,
  `numsent` decimal(11,0) DEFAULT NULL,
  `product_id` int(19) DEFAULT NULL,
  `sponsor` varchar(255) DEFAULT NULL,
  `targetaudience` varchar(255) DEFAULT NULL,
  `targetsize` int(19) DEFAULT NULL,
  `expectedresponsecount` int(19) DEFAULT NULL,
  `expectedsalescount` int(19) DEFAULT NULL,
  `expectedroi` decimal(28,6) DEFAULT NULL,
  `actualresponsecount` int(19) DEFAULT NULL,
  `actualsalescount` int(19) DEFAULT NULL,
  `actualroi` decimal(28,6) DEFAULT NULL,
  `campaignid` int(19) NOT NULL,
  `closingdate` date DEFAULT NULL,
  PRIMARY KEY (`campaignid`),
  KEY `campaign_campaignstatus_idx` (`campaignstatus`),
  KEY `campaign_campaignname_idx` (`campaignname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_campaign`
--

LOCK TABLES `vtiger_campaign` WRITE;
/*!40000 ALTER TABLE `vtiger_campaign` DISABLE KEYS */;
INSERT INTO `vtiger_campaign` VALUES ('CAM1','User Conference','Conference','Planning',250000.000000,25000.000000,23500.000000,'',2000,0,'Finace','Managers',210000,2500,25000,23.000000,250,1250,21.000000,122,'2003-01-02'),('CAM2','International Electrical Engineers Association Trade Show','Trade Show','Planning',750000.000000,50000.000000,45000.000000,'',2500,0,'Marketing','CEOs',13390,7500,50000,45.000000,750,5200,14.000000,123,'2004-02-03'),('CAM3','DM Campaign to Top Customers','Direct Mail','Completed',500000.000000,90000.000000,80000.000000,'',3000,0,'Sales','Rookies',187424,5000,90000,82.000000,1500,2390,12.000000,124,'2005-04-12');
/*!40000 ALTER TABLE `vtiger_campaign` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_campaignaccountrel`
--

DROP TABLE IF EXISTS `vtiger_campaignaccountrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_campaignaccountrel` (
  `campaignid` int(19) DEFAULT NULL,
  `accountid` int(19) DEFAULT NULL,
  `campaignrelstatusid` int(19) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_campaignaccountrel`
--

LOCK TABLES `vtiger_campaignaccountrel` WRITE;
/*!40000 ALTER TABLE `vtiger_campaignaccountrel` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_campaignaccountrel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_campaigncontrel`
--

DROP TABLE IF EXISTS `vtiger_campaigncontrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_campaigncontrel` (
  `campaignid` int(19) NOT NULL DEFAULT '0',
  `contactid` int(19) NOT NULL DEFAULT '0',
  `campaignrelstatusid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`campaignid`,`contactid`,`campaignrelstatusid`),
  KEY `campaigncontrel_contractid_idx` (`contactid`),
  CONSTRAINT `fk_2_vtiger_campaigncontrel` FOREIGN KEY (`contactid`) REFERENCES `vtiger_contactdetails` (`contactid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_campaigncontrel`
--

LOCK TABLES `vtiger_campaigncontrel` WRITE;
/*!40000 ALTER TABLE `vtiger_campaigncontrel` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_campaigncontrel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_campaignleadrel`
--

DROP TABLE IF EXISTS `vtiger_campaignleadrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_campaignleadrel` (
  `campaignid` int(19) NOT NULL DEFAULT '0',
  `leadid` int(19) NOT NULL DEFAULT '0',
  `campaignrelstatusid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`campaignid`,`leadid`,`campaignrelstatusid`),
  KEY `campaignleadrel_leadid_campaignid_idx` (`leadid`,`campaignid`),
  CONSTRAINT `fk_2_vtiger_campaignleadrel` FOREIGN KEY (`leadid`) REFERENCES `vtiger_leaddetails` (`leadid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_campaignleadrel`
--

LOCK TABLES `vtiger_campaignleadrel` WRITE;
/*!40000 ALTER TABLE `vtiger_campaignleadrel` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_campaignleadrel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_campaignrelstatus`
--

DROP TABLE IF EXISTS `vtiger_campaignrelstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_campaignrelstatus` (
  `campaignrelstatusid` int(19) DEFAULT NULL,
  `campaignrelstatus` varchar(256) DEFAULT NULL,
  `sortorderid` int(19) DEFAULT NULL,
  `presence` int(19) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_campaignrelstatus`
--

LOCK TABLES `vtiger_campaignrelstatus` WRITE;
/*!40000 ALTER TABLE `vtiger_campaignrelstatus` DISABLE KEYS */;
INSERT INTO `vtiger_campaignrelstatus` VALUES (1,'--None--',0,1),(2,'Contacted - Successful',1,1),(3,'Contacted - Unsuccessful',2,1),(4,'Contacted - Never Contact Again',3,1);
/*!40000 ALTER TABLE `vtiger_campaignrelstatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_campaignrelstatus_seq`
--

DROP TABLE IF EXISTS `vtiger_campaignrelstatus_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_campaignrelstatus_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_campaignrelstatus_seq`
--

LOCK TABLES `vtiger_campaignrelstatus_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_campaignrelstatus_seq` DISABLE KEYS */;
INSERT INTO `vtiger_campaignrelstatus_seq` VALUES (4);
/*!40000 ALTER TABLE `vtiger_campaignrelstatus_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_campaignscf`
--

DROP TABLE IF EXISTS `vtiger_campaignscf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_campaignscf` (
  `campaignid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`campaignid`),
  CONSTRAINT `fk_1_vtiger_campaignscf` FOREIGN KEY (`campaignid`) REFERENCES `vtiger_campaign` (`campaignid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_campaignscf`
--

LOCK TABLES `vtiger_campaignscf` WRITE;
/*!40000 ALTER TABLE `vtiger_campaignscf` DISABLE KEYS */;
INSERT INTO `vtiger_campaignscf` VALUES (122),(123),(124);
/*!40000 ALTER TABLE `vtiger_campaignscf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_campaignstatus`
--

DROP TABLE IF EXISTS `vtiger_campaignstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_campaignstatus` (
  `campaignstatusid` int(19) NOT NULL AUTO_INCREMENT,
  `campaignstatus` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`campaignstatusid`),
  KEY `campaignstatus_campaignstatus_idx` (`campaignstatus`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_campaignstatus`
--

LOCK TABLES `vtiger_campaignstatus` WRITE;
/*!40000 ALTER TABLE `vtiger_campaignstatus` DISABLE KEYS */;
INSERT INTO `vtiger_campaignstatus` VALUES (1,'--None--',1,14),(2,'Planning',1,15),(3,'Active',1,16),(4,'Inactive',1,17),(5,'Completed',1,18),(6,'Cancelled',1,19);
/*!40000 ALTER TABLE `vtiger_campaignstatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_campaignstatus_seq`
--

DROP TABLE IF EXISTS `vtiger_campaignstatus_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_campaignstatus_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_campaignstatus_seq`
--

LOCK TABLES `vtiger_campaignstatus_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_campaignstatus_seq` DISABLE KEYS */;
INSERT INTO `vtiger_campaignstatus_seq` VALUES (6);
/*!40000 ALTER TABLE `vtiger_campaignstatus_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_campaigntype`
--

DROP TABLE IF EXISTS `vtiger_campaigntype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_campaigntype` (
  `campaigntypeid` int(19) NOT NULL AUTO_INCREMENT,
  `campaigntype` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`campaigntypeid`),
  UNIQUE KEY `campaigntype_campaigntype_idx` (`campaigntype`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_campaigntype`
--

LOCK TABLES `vtiger_campaigntype` WRITE;
/*!40000 ALTER TABLE `vtiger_campaigntype` DISABLE KEYS */;
INSERT INTO `vtiger_campaigntype` VALUES (1,'--None--',1,20),(2,'Conference',1,21),(3,'Webinar',1,22),(4,'Trade Show',1,23),(5,'Public Relations',1,24),(6,'Partners',1,25),(7,'Referral Program',1,26),(8,'Advertisement',1,27),(9,'Banner Ads',1,28),(10,'Direct Mail',1,29),(11,'Email',1,30),(12,'Telemarketing',1,31),(13,'Others',1,32);
/*!40000 ALTER TABLE `vtiger_campaigntype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_campaigntype_seq`
--

DROP TABLE IF EXISTS `vtiger_campaigntype_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_campaigntype_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_campaigntype_seq`
--

LOCK TABLES `vtiger_campaigntype_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_campaigntype_seq` DISABLE KEYS */;
INSERT INTO `vtiger_campaigntype_seq` VALUES (13);
/*!40000 ALTER TABLE `vtiger_campaigntype_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_carrier`
--

DROP TABLE IF EXISTS `vtiger_carrier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_carrier` (
  `carrierid` int(19) NOT NULL AUTO_INCREMENT,
  `carrier` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`carrierid`),
  UNIQUE KEY `carrier_carrier_idx` (`carrier`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_carrier`
--

LOCK TABLES `vtiger_carrier` WRITE;
/*!40000 ALTER TABLE `vtiger_carrier` DISABLE KEYS */;
INSERT INTO `vtiger_carrier` VALUES (1,'FedEx',1,33),(2,'UPS',1,34),(3,'USPS',1,35),(4,'DHL',1,36),(5,'BlueDart',1,37);
/*!40000 ALTER TABLE `vtiger_carrier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_carrier_seq`
--

DROP TABLE IF EXISTS `vtiger_carrier_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_carrier_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_carrier_seq`
--

LOCK TABLES `vtiger_carrier_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_carrier_seq` DISABLE KEYS */;
INSERT INTO `vtiger_carrier_seq` VALUES (5);
/*!40000 ALTER TABLE `vtiger_carrier_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_category`
--

DROP TABLE IF EXISTS `vtiger_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_category` (
  `categoryid` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`categoryid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_category`
--

LOCK TABLES `vtiger_category` WRITE;
/*!40000 ALTER TABLE `vtiger_category` DISABLE KEYS */;
INSERT INTO `vtiger_category` VALUES (1,'System',1,329),(2,'User Interface',1,330),(3,'Performance',1,331),(4,'Module Functionality',1,332),(5,'Other',1,333);
/*!40000 ALTER TABLE `vtiger_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_category_seq`
--

DROP TABLE IF EXISTS `vtiger_category_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_category_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_category_seq`
--

LOCK TABLES `vtiger_category_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_category_seq` DISABLE KEYS */;
INSERT INTO `vtiger_category_seq` VALUES (5);
/*!40000 ALTER TABLE `vtiger_category_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_cbmap`
--

DROP TABLE IF EXISTS `vtiger_cbmap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_cbmap` (
  `cbmapid` int(11) NOT NULL DEFAULT '0',
  `mapname` varchar(255) DEFAULT NULL,
  `mapnumber` varchar(100) DEFAULT NULL,
  `maptype` varchar(100) DEFAULT NULL,
  `targetname` varchar(255) NOT NULL,
  `content` text,
  PRIMARY KEY (`cbmapid`),
  KEY `mapname` (`mapname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_cbmap`
--

LOCK TABLES `vtiger_cbmap` WRITE;
/*!40000 ALTER TABLE `vtiger_cbmap` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_cbmap` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_cbmapcf`
--

DROP TABLE IF EXISTS `vtiger_cbmapcf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_cbmapcf` (
  `cbmapid` int(11) NOT NULL,
  PRIMARY KEY (`cbmapid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_cbmapcf`
--

LOCK TABLES `vtiger_cbmapcf` WRITE;
/*!40000 ALTER TABLE `vtiger_cbmapcf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_cbmapcf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_cbtandc`
--

DROP TABLE IF EXISTS `vtiger_cbtandc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_cbtandc` (
  `cbtandcid` int(11) NOT NULL,
  `cbtandcno` varchar(100) DEFAULT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `formodule` varchar(20) DEFAULT NULL,
  `isdefault` varchar(3) DEFAULT NULL,
  `tandc` text,
  PRIMARY KEY (`cbtandcid`),
  KEY `cbtandcid` (`cbtandcid`),
  KEY `cbtandcno` (`cbtandcno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_cbtandc`
--

LOCK TABLES `vtiger_cbtandc` WRITE;
/*!40000 ALTER TABLE `vtiger_cbtandc` DISABLE KEYS */;
INSERT INTO `vtiger_cbtandc` VALUES (277,'cbTermConditions-0000001','Default T&C','Invoice','1','\n - Unless otherwise agreed in writing by the supplier all invoices are payable within thirty (30) days of the date of invoice, in the currency of the invoice, drawn on a bank based in India or by such other method as is agreed in advance by the Supplier.\n\n - All prices are not inclusive of VAT which shall be payable in addition by the Customer at the applicable rate.'),(278,'cbTermConditions-0000002','Default T&C','SalesOrder','1','\n - Unless otherwise agreed in writing by the supplier all invoices are payable within thirty (30) days of the date of invoice, in the currency of the invoice, drawn on a bank based in India or by such other method as is agreed in advance by the Supplier.\n\n - All prices are not inclusive of VAT which shall be payable in addition by the Customer at the applicable rate.'),(279,'cbTermConditions-0000003','Default T&C','Quotes','1','\n - Unless otherwise agreed in writing by the supplier all invoices are payable within thirty (30) days of the date of invoice, in the currency of the invoice, drawn on a bank based in India or by such other method as is agreed in advance by the Supplier.\n\n - All prices are not inclusive of VAT which shall be payable in addition by the Customer at the applicable rate.'),(280,'cbTermConditions-0000004','Default T&C','PurchaseOrder','1','\n - Unless otherwise agreed in writing by the supplier all invoices are payable within thirty (30) days of the date of invoice, in the currency of the invoice, drawn on a bank based in India or by such other method as is agreed in advance by the Supplier.\n\n - All prices are not inclusive of VAT which shall be payable in addition by the Customer at the applicable rate.');
/*!40000 ALTER TABLE `vtiger_cbtandc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_cbtandccf`
--

DROP TABLE IF EXISTS `vtiger_cbtandccf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_cbtandccf` (
  `cbtandcid` int(11) NOT NULL,
  PRIMARY KEY (`cbtandcid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_cbtandccf`
--

LOCK TABLES `vtiger_cbtandccf` WRITE;
/*!40000 ALTER TABLE `vtiger_cbtandccf` DISABLE KEYS */;
INSERT INTO `vtiger_cbtandccf` VALUES (277),(278),(279),(280);
/*!40000 ALTER TABLE `vtiger_cbtandccf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_cbtranslation`
--

DROP TABLE IF EXISTS `vtiger_cbtranslation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_cbtranslation` (
  `cbtranslationid` int(11) NOT NULL,
  `autonum` varchar(100) DEFAULT NULL,
  `locale` varchar(10) DEFAULT NULL,
  `translation_module` varchar(150) DEFAULT NULL,
  `i18n` text,
  `proofread` varchar(3) DEFAULT NULL,
  `translates` int(11) DEFAULT NULL,
  `forfield` varchar(130) DEFAULT NULL,
  `forpicklist` varchar(130) DEFAULT NULL,
  `translation_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`cbtranslationid`),
  KEY `cbtrankey` (`locale`,`translation_key`),
  KEY `cbtranmod` (`locale`,`translation_module`,`translation_key`),
  KEY `cbtranrecfield` (`locale`,`translates`,`forfield`),
  KEY `cbtranreckey` (`locale`,`translates`,`translation_key`),
  KEY `cbtranpl` (`locale`,`forpicklist`),
  KEY `cbtranplkey` (`locale`,`forpicklist`,`translation_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_cbtranslation`
--

LOCK TABLES `vtiger_cbtranslation` WRITE;
/*!40000 ALTER TABLE `vtiger_cbtranslation` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_cbtranslation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_cbtranslationcf`
--

DROP TABLE IF EXISTS `vtiger_cbtranslationcf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_cbtranslationcf` (
  `cbtranslationid` int(11) NOT NULL,
  PRIMARY KEY (`cbtranslationid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_cbtranslationcf`
--

LOCK TABLES `vtiger_cbtranslationcf` WRITE;
/*!40000 ALTER TABLE `vtiger_cbtranslationcf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_cbtranslationcf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_cbupdater`
--

DROP TABLE IF EXISTS `vtiger_cbupdater`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_cbupdater` (
  `cbupdaterid` int(11) NOT NULL DEFAULT '0',
  `cbupd_no` varchar(26) DEFAULT NULL,
  `author` varchar(83) DEFAULT NULL,
  `filename` varchar(218) DEFAULT NULL,
  `pathfilename` varchar(218) DEFAULT NULL,
  `classname` varchar(183) DEFAULT NULL,
  `execstate` varchar(56) DEFAULT NULL,
  `systemupdate` varchar(3) DEFAULT NULL,
  `blocked` varchar(3) DEFAULT NULL,
  `perspective` varchar(3) DEFAULT NULL,
  `execdate` date DEFAULT NULL,
  `execorder` int(11) DEFAULT NULL,
  PRIMARY KEY (`cbupdaterid`),
  UNIQUE KEY `findupdate` (`filename`,`classname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_cbupdater`
--

LOCK TABLES `vtiger_cbupdater` WRITE;
/*!40000 ALTER TABLE `vtiger_cbupdater` DISABLE KEYS */;
INSERT INTO `vtiger_cbupdater` VALUES (125,'cbupd-0000001','joebordes','vt54_cb54','build/changeSets/vt54_cb54.php','vt54_cb54','Executed','1','0','0','2014-10-07',1),(126,'cbupd-0000002','joebordes','coreboscp_rest','build/changeSets/coreboscp_rest.php','coreboscp_rest','Continuous','1','0','0','2017-09-02',2),(127,'cbupd-0000003','joebordes','DocumentRelatedList','build/changeSets/DocumentRelatedList.php','document_relatedlist','Executed','0','0','0','2014-10-07',3),(128,'cbupd-0000004','joebordes','departmentfieldlimit_177','build/changeSets/departmentfieldlimit_177.php','departmentfieldlimit','Executed','1','0','0','2014-10-07',4),(129,'cbupd-0000005','joebordes','EmailReports','build/changeSets/EmailReports.php','EmailReports','Executed','0','0','0','2014-10-07',5),(130,'cbupd-0000006','joebordes','PotentialForecastAmount','build/changeSets/PotentialForecastAmount.php','PotentialForecastAmount','Executed','0','0','0','2014-10-07',6),(131,'cbupd-0000007','joebordes','cleanoptimizedatabase_140','build/changeSets/cleanoptimizedatabase_140.php','cleandatabase_140','Executed','1','0','0','2014-10-07',7),(132,'cbupd-0000008','joebordes','cb54_cb55','build/changeSets/cb54_cb55.php','cb54_cb55','Executed','1','0','0','2014-10-07',8),(133,'cbupd-0000009','joebordes','create_workflow_taskstype','build/changeSets/create_workflow_taskstype.php','create_workflow_taskstype','Executed','1','0','0','2014-10-07',9),(134,'cbupd-0000010','joebordes','add_workflow_tags','build/changeSets/add_workflow_tags.php','add_workflow_tags','Executed','0','0','0','2014-10-07',10),(135,'cbupd-0000011','joebordes','workflow_contactassignedto','build/changeSets/workflow_contactassignedto.php','workflow_contactassignedto','Executed','0','0','0','2014-10-07',11),(136,'cbupd-0000012','joebordes','cffaq','build/changeSets/cffaq.php','cffaq','Executed','1','0','0','2014-10-07',12),(137,'cbupd-0000013','joebordes','installcyp','build/changeSets/installcyp.php','installcyp','Executed','0','0','0','2014-10-07',13),(138,'cbupd-0000014','joebordes','cbupdater_blockperspective','build/changeSets/cbupdater_blockperspective.php','cbupdater_blockperspective','Executed','1','0','0','2014-10-07',14),(139,'cbupd-0000015','joebordes','ticket191_wfrelatepdosrv','build/changeSets/ticket191_wfrelatepdosrv.php','ticket191','Executed','0','0','0','2014-10-07',15),(140,'cbupd-0000016','joebordes','directemailPPTHP','build/changeSets/directemailPPTHP.php','directemailPPTHP','Executed','1','0','0','2014-10-07',16),(141,'cbupd-0000017','joebordes','email_rest_support','build/changeSets/email_rest_support.php','email_rest_support','Executed','1','0','0','2014-10-07',17),(142,'cbupd-0000018','joebordes','user_showtagas','build/changeSets/user_showtagas.php','user_showtagas','Executed','1','0','0','2014-10-07',18),(143,'cbupd-0000019','omar','installc4y','build/changeSets/installc4y.php','installc4y','Executed','0','0','0','2014-10-07',19),(144,'cbupd-0000020','joebordes','convertVendorPhoneToCorrectType','build/changeSets/convertVendorPhoneToCorrectType.php','convertVendorPhoneToCorrectType','Executed','1','0','0','2014-10-07',20),(146,'cbupd-0000021','mslokhat','addvend_activities','build/changeSets/addvend_activities.php','addVendorActivities','Executed','0','0','0','2014-10-07',21),(147,'cbupd-0000022','joebordes','UserSendEmailToSender','build/changeSets/UserSendEmailToSender.php','UserSendEmailToSender','Executed','1','0','0','2016-04-24',22),(148,'cbupd-0000023','kikojover','correct_datetime','build/changeSets/correct_datetime.php','solve_dt','Executed','1','0','0','2016-04-24',23),(149,'cbupd-0000024','joebordes','moveDVBtoEnd','build/changeSets/moveDVBtoEnd.php','moveDVBtoEnd','Executed','1','0','0','2016-04-24',24),(150,'cbupd-0000025','joebordes','correctProjectTargetBudgetType','build/changeSets/correctProjectTargetBudgetType.php','correctProjectTargetBudgetType','Executed','1','0','0','2016-04-24',25),(151,'cbupd-0000026','loridacito','addfieldstocal','build/changeSets/addfieldstocal.php','addfieldstocal','Executed','1','0','0','2016-04-24',26),(152,'cbupd-0000027','joebordes','SendEmailFromAddress','build/changeSets/SendEmailFromAddress.php','SendEmailFromAddress','Executed','1','0','0','2016-04-24',27),(153,'cbupd-0000028','joebordes','MassUploadImageOnProduct','build/changeSets/MassUploadImageOnProduct.php','MassUploadImageOnProduct','Executed','0','0','0','2016-04-24',28),(154,'cbupd-0000029','joebordes','PdoSrvPBActiveDefaultValue','build/changeSets/PdoSrvPBActiveDefaultValue.php','PdoSrvPBActiveDefaultValue','Executed','1','0','0','2016-04-24',29),(155,'cbupd-0000030','loridacito','addfieldstocaltwo','build/changeSets/addfieldstocaltwo.php','addfieldstocaltwo','Executed','1','0','0','2016-04-24',30),(156,'cbupd-0000031','joebordes','addvendorrelatedlist2contact','build/changeSets/addvendorrelatedlist2contact.php','addvendorrelatedlist2contact','Executed','0','0','0','2016-04-24',31),(157,'cbupd-0000032','joebordes','cleanoptimizedatabase_610','build/changeSets/cleanoptimizedatabase_610.php','cleandatabase_610','Executed','1','0','0','2016-04-24',32),(158,'cbupd-0000033','omarllorens','fix_assigneduserid_in_service','build/changeSets/fix_assigneduserid_in_service.php','fix_assigneduserid_in_service','Executed','1','0','0','2016-04-24',33),(159,'cbupd-0000034','joebordes','undo_wsreferencetype31insert','build/changeSets/undo_wsreferencetype31insert.php','undo_wsreferencetype31insert','Executed','1','0','0','2016-04-24',34),(160,'cbupd-0000035','omarllorens','fixRefresTokenInC4Y','build/changeSets/fixRefresTokenInC4Y.php','fixRefresTokenInC4Y','Executed','1','0','0','2016-04-24',35),(161,'cbupd-0000036','joebordes','installglobalvars','build/changeSets/installglobalvars.php','installglobalvars','Executed','1','0','0','2016-04-24',36),(162,'cbupd-0000037','omarllorens','changeUitypes23To5','build/changeSets/changeUitypes23To5.php','changeUitypes23To5','Executed','1','0','0','2016-04-24',37),(163,'cbupd-0000038','joebordes','create_workflow_onschedule','build/changeSets/create_workflow_onschedule.php','create_workflow_onschedule','Executed','1','0','0','2016-04-24',38),(164,'cbupd-0000039','joebordes','importexportdedup_campaigns','build/changeSets/importexportdedup_campaigns.php','importexportdedup_campaigns','Executed','1','0','0','2016-04-24',39),(165,'cbupd-0000040','joebordes','gvServiceOrProduct','build/changeSets/gvServiceOrProduct.php','gvServiceOrProduct','Executed','1','0','0','2016-04-24',40),(166,'cbupd-0000041','omarllorens','addIconToAddpaymentAction','build/changeSets/addIconToAddpaymentAction.php','addIconToAddpaymentAction','Executed','1','0','0','2016-04-24',41),(167,'cbupd-0000042','joebordes','modcommentsassignedtoemail','build/changeSets/modcommentsassignedtoemail.php','modcommentsassignedtoemail','Executed','1','0','0','2016-04-24',42),(168,'cbupd-0000043','joebordes','UpdateExchangeRate','build/changeSets/UpdateExchangeRate.php','UpdateExchangeRateCron','Executed','0','0','0','2016-04-24',43),(169,'cbupd-0000044','joebordes','DefineGlobalVariables','build/changeSets/DefineGlobalVariables.php','DefineGlobalVariables','Continuous','1','0','0','2017-09-02',44),(170,'cbupd-0000045','omarllorens','installinventorydetails','build/changeSets/installinventorydetails.php','installinventorydetails','Executed','0','0','0','2016-04-24',45),(171,'cbupd-0000046','joebordes','ModulesOnCalendar','build/changeSets/ModulesOnCalendar.php','ModulesOnCalendar','Executed','1','0','0','2016-04-24',46),(172,'cbupd-0000047','joebordes','delCustomActionTable','build/changeSets/delCustomActionTable.php','delCustomActionTable','Executed','1','0','0','2016-04-24',47),(173,'cbupd-0000048','joebordes','milestonedatetodate','build/changeSets/milestonedatetodate.php','milestonedatetodate','Executed','1','0','0','2016-04-24',48),(174,'cbupd-0000049','joebordes','DeleteSystemServerInfoTab','build/changeSets/DeleteSystemServerInfoTab.php','DeleteSystemServerInfoTab','Executed','1','0','0','2016-04-24',49),(175,'cbupd-0000050','joebordes','cbupdater_importxml','build/changeSets/cbupdater_importxml.php','cbupdater_importxml','Executed','0','0','0','2016-04-24',50),(176,'cbupd-0000051','joebordes','inventoryproductstockcontrol','build/changeSets/inventoryproductstockcontrol.php','inventoryproductstockcontrol','Executed','1','0','0','2016-04-24',51),(177,'cbupd-0000052','joebordes','importexport_inventorymodules','build/changeSets/importexport_inventorymodules.php','importexport_inventorymodules','Executed','1','0','0','2016-04-24',52),(178,'cbupd-0000053','joebordes','makeVendorShareable','build/changeSets/makeVendorShareable.php','makeVendorShareable','Executed','1','0','0','2016-04-24',53),(179,'cbupd-0000054','joebordes','fixUIType4WebserviceFieldType','build/changeSets/fixUIType4WebserviceFieldType.php','fixUIType4WebserviceFieldType','Executed','1','0','0','2016-04-24',54),(180,'cbupd-0000055','joebordes','addUIType1613and3313WebserviceFieldType','build/changeSets/addUIType1613and3313WebserviceFieldType.php','addUIType1613and3313WebserviceFieldType','Executed','1','0','0','2016-04-24',55),(181,'cbupd-0000056','joebordes','fix_add_FieldDataTypes','build/changeSets/fix_add_FieldDataTypes.php','fix_add_FieldDataTypes','Executed','1','0','0','2016-04-24',56),(182,'cbupd-0000057','joebordes','cronbackup','build/changeSets/cronbackup.php','cbcronbackup','Executed','1','0','0','2016-04-24',57),(183,'cbupd-0000058','joebordes','GlobalVarUITypeModuleListFixEntityID','build/changeSets/GlobalVarUITypeModuleListFixEntityID.php','GlobalVarUITypeModuleListFixEntityID','Executed','1','0','0','2016-04-24',58),(184,'cbupd-0000059','kikojover','addFieldsToCyP','build/changeSets/addFieldsToCyP.php','addFieldsToCyP','Executed','1','0','0','2016-04-24',59),(185,'cbupd-0000060','joebordes','ModTrackerRestoreRecord','build/changeSets/ModTrackerRestoreRecord.php','ModTrackerRestoreRecord','Executed','1','0','0','2016-04-24',60),(186,'cbupd-0000061','joebordes','UserHourStartFields','build/changeSets/UserHourStartFields.php','UserHourStartFields','Executed','1','0','0','2016-04-24',61),(187,'cbupd-0000062','joebordes','UserHourStartFieldsList','build/changeSets/UserHourStartFieldsList.php','UserHourStartFieldsList','Executed','1','0','0','2016-04-24',62),(188,'cbupd-0000063','joebordes','UserHourStartFieldsPL16','build/changeSets/UserHourStartFieldsPL16.php','UserHourStartFieldsPL16','Executed','1','0','0','2016-04-24',63),(189,'cbupd-0000064','joebordes','delSettingsModTrackerProxy','build/changeSets/delSettingsModTrackerProxy.php','delSettingsModTrackerProxy','Executed','0','0','0','2016-04-24',64),(190,'cbupd-0000065','kikojover','assignto_mailscanner','build/changeSets/assignto_mailscanner.php','assignto_mailscanner','Executed','1','0','0','2016-04-24',65),(191,'cbupd-0000066','joebordes','PBXManagerAfterSaveCreateActivity','build/changeSets/PBXManagerAfterSaveCreateActivity.php','PBXManagerAfterSaveCreateActivity','Executed','1','0','0','2016-04-24',66),(192,'cbupd-0000067','joebordes','contacts_hierarchylink','build/changeSets/contacts_hierarchylink.php','contacts_hierarchylink','Executed','0','0','0','2016-04-24',67),(193,'cbupd-0000068','joebordes','HelpDeskStatusOnCalendar','build/changeSets/HelpDeskStatusOnCalendar.php','HelpDeskStatusOnCalendar','Executed','1','0','0','2016-04-24',68),(194,'cbupd-0000069','joebordes','fixProjectRelatedLists','build/changeSets/fixProjectRelatedLists.php','fixProjectRelatedLists','Executed','1','0','0','2016-04-24',69),(195,'cbupd-0000070','joebordes','CalendarEnhanceFieldInfoRelatedModules','build/changeSets/CalendarEnhanceFieldInfoRelatedModules.php','CalendarEnhanceFieldInfoRelatedModules','Executed','1','0','0','2016-04-24',70),(196,'cbupd-0000071','joebordes','fixAddNotesURLToEstablishLink','build/changeSets/fixAddNotesURLToEstablishLink.php','fixAddNotesURLToEstablishLink','Executed','1','0','0','2016-04-24',71),(197,'cbupd-0000072','joebordes','PdoDiscontinuedToCheckbox','build/changeSets/PdoDiscontinuedToCheckbox.php','PdoDiscontinuedToCheckbox','Executed','1','0','0','2016-04-24',72),(198,'cbupd-0000073','joebordes','add_workflow_delrelated','build/changeSets/add_workflow_delrelated.php','add_workflow_delrelated','Executed','1','0','0','2016-04-24',73),(199,'cbupd-0000074','joebordes','sortworkflowtasks','build/changeSets/sortworkflowtasks.php','sortworkflowtasks','Executed','1','0','0','2016-04-24',74),(200,'cbupd-0000075','omarllorens','addCostPrice','build/changeSets/addCostPrice.php','addCostPrice','Executed','1','0','0','2016-04-24',75),(201,'cbupd-0000076','joebordes','addNumberDecimalPlaces','build/changeSets/addNumberDecimalPlaces.php','addNumberDecimalPlaces','Executed','1','0','0','2016-04-24',76),(202,'cbupd-0000077','joebordes','addfixCurrencies01','build/changeSets/addfixCurrencies01.php','addfixCurrencies01','Executed','1','0','0','2016-04-24',77),(203,'cbupd-0000078','joebordes','installcbmap','build/changeSets/installcbmap.php','installcbmap','Executed','1','0','0','2016-04-24',78),(204,'cbupd-0000079','joebordes','cbMapTargetModuleField','build/changeSets/cbMapTargetModuleField.php','cbMapTargetModuleField','Executed','1','0','0','2016-04-24',79),(205,'cbupd-0000080','joebordes','cbMapAddMapTypes','build/changeSets/cbMapAddMapTypes.php','cbMapAddMapTypes','Continuous','1','0','0','2017-09-02',80),(206,'cbupd-0000081','joebordes','add_workflow_selectcbmap','build/changeSets/add_workflow_selectcbmap.php','add_workflow_selectcbmap','Executed','1','0','0','2016-04-24',81),(207,'cbupd-0000082','kikojover','add_halfyear_recurring_invoice','build/changeSets/add_halfyear_recurring_invoice.php','add_halfyear_recurring_invoice','Executed','1','0','0','2016-04-24',82),(208,'cbupd-0000083','joebordes','fixCalendarStartEndDateOnReports','build/changeSets/fixCalendarStartEndDateOnReports.php','fixCalendarStartEndDateOnReports','Executed','1','0','0','2016-06-28',83),(209,'cbupd-0000084','joebordes','separateCreateAndEditPermissions','build/changeSets/separateCreateAndEditPermissions.php','separateCreateAndEditPermissions','Executed','1','0','0','2016-06-28',84),(210,'cbupd-0000085','joebordes','workflow_contactassignedtoaccount','build/changeSets/workflow_contactassignedtoaccount.php','workflow_contactassignedtoaccount','Executed','0','0','0','2016-06-28',85),(211,'cbupd-0000086','omarllorens','updateMobileModuleToCrmNow','build/changeSets/updateMobileModuleToCrmNow.php','updateMobileModuleToCrmNow','Executed','1','0','0','2016-06-28',86),(212,'cbupd-0000087','joebordes','UserFailedLoginAttempts','build/changeSets/UserFailedLoginAttempts.php','UserFailedLoginAttempts','Executed','1','0','0','2016-06-28',87),(213,'cbupd-0000088','joebordes','CalendarDeletejQueryLinks','build/changeSets/CalendarDeletejQueryLinks.php','CalendarDeletejQueryLinks','Executed','1','0','0','2016-06-28',88),(214,'cbupd-0000089','kikojover','addRecurringSO','build/changeSets/addRecurringSO.php','addRecurringSO','Executed','1','0','0','2016-06-28',89),(215,'cbupd-0000090','joebordes','cronExpirePasswordAfterDays','build/changeSets/cronExpirePasswordAfterDays.php','cronExpirePasswordAfterDays','Executed','1','0','0','2016-06-28',90),(216,'cbupd-0000091','joebordes','permissions_fixesfoundwhiletesting','build/changeSets/permissions_fixesfoundwhiletesting.php','permissions_fixesfoundwhiletesting','Executed','1','0','0','2016-06-28',91),(217,'cbupd-0000092','erimatraku','minuteintervalwfsched','build/changeSets/minuteintervalwfsched.php','minuteintervalwfsched','Executed','1','0','0','2016-06-28',92),(218,'cbupd-0000093','omarllorens','changeHoursAndUse_UnitsFields','build/changeSets/changeHoursAndUse_UnitsFields.php','changeHoursAndUse_UnitsFields','Executed','1','0','0','2016-06-28',93),(219,'cbupd-0000094','joebordes','fixConvertLeadWebserviceParameters','build/changeSets/fixConvertLeadWebserviceParameters.php','fixConvertLeadWebserviceParameters','Executed','1','0','0','2016-06-28',94),(220,'cbupd-0000095','joebordes','setDefaultValuesForUserFields','build/changeSets/setDefaultValuesForUserFields.php','setDefaultValuesForUserFields','Executed','1','0','0','2016-06-28',95),(221,'cbupd-0000096','joebordes','makeEmailsShareable','build/changeSets/makeEmailsShareable.php','makeEmailsShareable','Executed','1','0','0','2016-10-26',96),(222,'cbupd-0000097','joebordes','cleanoptimizedatabase_150','build/changeSets/cleanoptimizedatabase_150.php','cleanoptimizedatabase_150','Executed','1','0','0','2016-10-26',97),(223,'cbupd-0000098','omarllorens','fixEventHandlerPathForConvertTZ','build/changeSets/fixEventHandlerPathForConvertTZ.php','fixEventHandlerPathForConvertTZ','Executed','1','0','0','2016-10-26',98),(224,'cbupd-0000099','omarllorens','cleanActivityTypesSpecialChars','build/changeSets/cleanActivityTypesSpecialChars.php','cleanActivityTypesSpecialChars','Executed','1','0','0','2016-10-26',99),(225,'cbupd-0000100','joebordes','cronWatcher','build/changeSets/cronWatcher.php','cronWatcherService','Executed','1','0','0','2016-10-26',100),(226,'cbupd-0000101','joebordes','UserDeleteUserHashColumn','build/changeSets/UserDeleteUserHashColumn.php','UserDeleteUserHashColumn','Executed','1','0','0','2016-10-26',101),(227,'cbupd-0000102','kevinduqi','add_workflow_assignrelated','build/changeSets/add_workflow_assignrelated.php','add_workflow_assignrelated','Executed','0','0','0','2016-10-26',102),(228,'cbupd-0000103','joebordes','installGlobalVarDefinitions','build/changeSets/installGlobalVarDefinitions.php','installGlobalVarDefinitions','Executed','1','0','0','2016-10-26',103),(229,'cbupd-0000104','joebordes','addCostPriceID','build/changeSets/addCostPriceID.php','addCostPriceID','Executed','1','0','0','2016-10-26',104),(230,'cbupd-0000105','kevinduqi','add_workflow_duplicaterecords','build/changeSets/add_workflow_duplicaterecords.php','add_workflow_duplicaterecords','Executed','0','0','0','2016-10-26',105),(231,'cbupd-0000106','joebordes','cleanoptimizedatabase_160','build/changeSets/cleanoptimizedatabase_160.php','cleanoptimizedatabase_160','Executed','1','0','0','2016-10-26',106),(232,'cbupd-0000107','joebordes','addCurrencyPosition','build/changeSets/addCurrencyPosition.php','addCurrencyPosition','Executed','1','0','0','2016-10-26',107),(233,'cbupd-0000108','kevinduqi','mass_document_download','build/changeSets/mass_document_download.php','mass_document_download','Executed','0','0','0','2016-10-26',108),(234,'cbupd-0000109','joebordes','addStatus2ProjectTask','build/changeSets/addStatus2ProjectTask.php','addStatus2ProjectTask','Executed','0','0','0','2016-10-26',109),(235,'cbupd-0000110','joebordes','addLeadEmailOptOutAndConversionRelatedFields','build/changeSets/addLeadEmailOptOutAndConversionRelatedFields.php','addLeadEmailOptOutAndConversionRelatedFields','Executed','1','0','0','2016-10-26',110),(236,'cbupd-0000111','joebordes','addCommentAdded2HelpDesk','build/changeSets/addCommentAdded2HelpDesk.php','addCommentAdded2HelpDesk','Executed','1','0','0','2016-10-26',111),(237,'cbupd-0000112','kikojover','setDocRelVendor','build/changeSets/setDocRelVendor.php','setDocRelVendor','Executed','1','0','0','2016-10-26',112),(238,'cbupd-0000113','joebordes','changeEMailAccessCountToInteger','build/changeSets/changeEMailAccessCountToInteger.php','changeEMailAccessCountToInteger','Executed','1','0','0','2016-10-26',113),(239,'cbupd-0000114','omarllorens','changeTypeOfDataUitype9','build/changeSets/changeTypeOfDataUitype9.php','changeTypeOfDataUitype9','Executed','1','0','0','2016-10-26',114),(240,'cbupd-0000115','joebordes','mysqlstrictNO_ZERO_IN_DATE','build/changeSets/mysqlstrictNO_ZERO_IN_DATE.php','mysqlstrictNO_ZERO_IN_DATE','Executed','1','0','0','2016-10-26',115),(241,'cbupd-0000116','joebordes','changeTypeOfDataUitype9FIX','build/changeSets/changeTypeOfDataUitype9FIX.php','changeTypeOfDataUitype9FIX','Executed','1','0','0','2016-10-26',116),(242,'cbupd-0000117','omarllorens','mysqlstrictCobroPagoUpdateLog','build/changeSets/mysqlstrictCobroPagoUpdateLog.php','mysqlstrictCobroPagoUpdateLog','Executed','1','0','0','2016-10-26',117),(243,'cbupd-0000118','omarllorens','mysqlstrict_Uitype101NotAcceptEmptyValue','build/changeSets/mysqlstrict_Uitype101NotAcceptEmptyValue.php','mysqlstrict_Uitype101NotAcceptEmptyValue','Executed','1','0','0','2016-10-26',118),(244,'cbupd-0000119','joebordes','delSettingsBackupAnnouncements','build/changeSets/delSettingsBackupAnnouncements.php','delSettingsBackupAnnouncements','Executed','1','0','0','2016-10-26',119),(245,'cbupd-0000120','joebordes','delSettingsCPortalWebform','build/changeSets/delSettingsCPortalWebform.php','delSettingsCPortalWebform','Executed','1','0','0','2016-10-26',120),(246,'cbupd-0000121','omarllorens','updateIssuecardsModule','build/changeSets/updateIssuecardsModule.php','updateIssuecardsModule','Executed','1','0','0','2016-10-26',121),(247,'cbupd-0000122','joebordes','delSettingsSinglePaneViewConfigEditor','build/changeSets/delSettingsSinglePaneViewConfigEditor.php','delSettingsSinglePaneViewConfigEditor','Executed','1','0','0','2016-10-26',122),(248,'cbupd-0000123','MajorLabel','add_workflow_convert_inventorymodule','build/changeSets/add_workflow_convert_inventorymodule.php','add_workflow_convert_inventorymodule','Executed','0','0','0','2016-10-26',123),(249,'cbupd-0000124','joebordes','changeUitype58To10','build/changeSets/changeUitype58To10.php','changeUitype58To10','Executed','1','0','0','2017-01-07',124),(250,'cbupd-0000125','joebordes','activateMassEditOnDocumentFields','build/changeSets/activateMassEditOnDocumentFields.php','activateMassEditOnDocumentFields','Executed','1','0','0','2017-01-07',125),(251,'cbupd-0000126','joebordes','WorkflowOnDelete','build/changeSets/WorkflowOnDelete.php','WorkflowOnDelete','Executed','1','0','0','2017-01-07',126),(252,'cbupd-0000127','joebordes','addIsRelatedListBlock','build/changeSets/addIsRelatedListBlock.php','addIsRelatedListBlock','Executed','1','0','0','2017-01-07',127),(253,'cbupd-0000128','omarllorens','fixIssuecardsCurrencyFieldsBlock','build/changeSets/fixIssuecardsCurrencyFieldsBlock.php','fixIssuecardsCurrencyFieldsBlock','Executed','1','0','0','2017-01-07',128),(254,'cbupd-0000129','joebordes','addPdoSrvDivisible','build/changeSets/addPdoSrvDivisible.php','addPdoSrvDivisible','Executed','1','0','0','2017-01-07',129),(255,'cbupd-0000130','joebordes','delAdministration','build/changeSets/delAdministration.php','delAdministration','Executed','1','0','0','2017-01-07',130),(256,'cbupd-0000131','joebordes','delSettingsNotifications','build/changeSets/delSettingsNotifications.php','delSettingsNotifications','Executed','1','0','0','2017-01-07',131),(257,'cbupd-0000132','joebordes','delvtChatTables','build/changeSets/delvtChatTables.php','delvtChatTables','Executed','1','0','0','2017-01-07',132),(258,'cbupd-0000133','kikojover','add_Indexes_InvDetails','build/changeSets/add_Indexes_InvDetails.php','add_Indexes_InvDetails','Executed','1','0','0','2017-01-07',133),(259,'cbupd-0000134','joebordes','vtlibLinkLoadOnlyOnMyModule','build/changeSets/vtlibLinkLoadOnlyOnMyModule.php','vtlibLinkLoadOnlyOnMyModule','Executed','1','0','0','2017-01-07',134),(260,'cbupd-0000135','joebordes','dropzonecssandjs','build/changeSets/dropzonecssandjs.php','dropzonecssandjs','Executed','1','0','0','2017-01-07',135),(261,'cbupd-0000136','joebordes','mysqlstrictMailManager','build/changeSets/mysqlstrictMailManager.php','mysqlstrictMailManager','Executed','1','0','0','2017-01-07',136),(262,'cbupd-0000137','omarllorens','addUIType1024WebserviceFieldType','build/changeSets/addUIType1024WebserviceFieldType.php','addUIType1024WebserviceFieldType','Executed','1','0','0','2017-01-07',137),(263,'cbupd-0000138','joebordes','fixCalendarStartEndDateOnReportsFilters','build/changeSets/fixCalendarStartEndDateOnReportsFilters.php','fixCalendarStartEndDateOnReportsFilters','Executed','1','0','0','2017-01-07',138),(264,'cbupd-0000139','joebordes','addCreatedByField','build/changeSets/addCreatedByField.php','addCreatedByField','Executed','1','0','0','2017-01-07',139),(265,'cbupd-0000140','omarllorens','updateMobileModuleToCrmNow2','build/changeSets/updateMobileModuleToCrmNow2.php','updateMobileModuleToCrmNow2','Executed','1','0','0','2017-01-07',140),(266,'cbupd-0000141','joebordes','makeModCommentsShareable','build/changeSets/makeModCommentsShareable.php','makeModCommentsShareable','Executed','1','0','0','2017-01-07',141),(267,'cbupd-0000142','loridacito','addlinksforcalendar','build/changeSets/addlinksforcalendar.php','addlinksforcalendar','Executed','1','0','0','2017-01-07',142),(268,'cbupd-0000143','kikojover','grow_activity_subject','build/changeSets/grow_activity_subject.php','grow_activity_subject','Executed','1','0','0','2017-01-07',143),(269,'cbupd-0000144','shpend','ldMenu','build/changeSets/ldMenu.php','ldMenu','Executed','1','0','0','2017-01-07',144),(270,'cbupd-0000145','joebordes','delSettingsAuditLogin','build/changeSets/delSettingsAuditLogin.php','delSettingsAuditLogin','Executed','1','0','0','2017-01-07',145),(271,'cbupd-0000146','kevinduqi','installcbTermConditions','build/changeSets/installcbTermConditions.php','installcbTermConditions','Executed','1','0','0','2017-01-07',146),(272,'cbupd-0000147','albertxhani','RemoveMomentjs','build/changeSets/RemoveMomentjs.php','RemoveMomentLink','Executed','1','0','0','2017-01-07',147),(273,'cbupd-0000148','joebordes','FeatureSeeConvertedLeads','build/changeSets/FeatureSeeConvertedLeads.php','FeatureSeeConvertedLeads','Executed','0','0','0','2017-01-07',148),(274,'cbupd-0000149','joebordes','cleanoptimizedatabase_170','build/changeSets/cleanoptimizedatabase_170.php','cleanoptimizedatabase_170','Executed','1','0','0','2017-01-07',149),(275,'cbupd-0000150','joebordes','GlobalVarUITypeModuleList3314','build/changeSets/2017/GlobalVarUITypeModuleList3314.php','GlobalVarUITypeModuleList3314','Executed','1','0','0','2017-01-07',150),(276,'cbupd-0000151','joebordes','cleanoptimizedatabase_180','build/changeSets/2017/cleanoptimizedatabase_180.php','cleanoptimizedatabase_180','Executed','1','0','0','2017-01-07',151),(281,'cbupd-0000152','joebordes','fixcbTermConditionsField','build/changeSets/2017/fixcbTermConditionsField.php','fixcbTermConditionsField','Executed','1','0','0','2017-02-01',152),(282,'cbupd-0000153','joebordes','addMoreInfoFieldToReports','build/changeSets/2017/addMoreInfoFieldToReports.php','addMoreInfoFieldToReports','Executed','1','0','0','2017-02-01',153),(283,'cbupd-0000154','joebordes','PBXManagerAfterSaveCreateActivity','build/changeSets/PBXManagerAfterSaveCreateActivity.php','cbupdPBXManagerAfterSaveCreateActivity','Executed','1','0','0','2017-09-02',154),(284,'cbupd-0000155','joebordes','migratebiurl2moreinfo','build/changeSets/2017/migratebiurl2moreinfo.php','migratebiurl2moreinfo','Executed','1','0','0','2017-09-02',155),(285,'cbupd-0000156','omarllorens','changeUitype68To10','build/changeSets/2017/changeUitype68To10.php','changeUitype68To10','Executed','1','0','0','2017-09-02',156),(286,'cbupd-0000157','omarllorens','fixWordProcessorMergeTable','build/changeSets/2017/fixWordProcessorMergeTable.php','fixWordProcessorMergeTable','Executed','1','0','0','2017-09-02',157),(287,'cbupd-0000158','loridacito','add_dailyfield_incron','build/changeSets/2017/add_dailyfield_incron.php','add_dailyfield_incron','Executed','1','0','0','2017-09-02',158),(288,'cbupd-0000159','joebordes','uitype50DateTimeinWebservice','build/changeSets/2017/uitype50DateTimeinWebservice.php','uitype50DateTimeinWebservice','Executed','1','0','0','2017-09-02',159),(289,'cbupd-0000160','joebordes','cleanoptimizedatabase_190','build/changeSets/2017/cleanoptimizedatabase_190.php','cleanoptimizedatabase_190','Executed','1','0','0','2017-09-02',160),(290,'cbupd-0000161','joebordes','fixAsteriskLabelOnUsersModule','build/changeSets/2017/fixAsteriskLabelOnUsersModule.php','fixAsteriskLabelOnUsersModule','Executed','1','0','0','2017-09-02',161),(291,'cbupd-0000162','joebordes','addDatabaseIndex2PBXManager','build/changeSets/2017/addDatabaseIndex2PBXManager.php','addDatabaseIndex2PBXManager','Executed','1','0','0','2017-09-02',162),(292,'cbupd-0000163','joebordes','fixPBXManagerUITypes','build/changeSets/2017/fixPBXManagerUITypes.php','fixPBXManagerUITypes','Executed','1','0','0','2017-09-02',163),(293,'cbupd-0000164','joebordes','delIntegration','build/changeSets/2017/delIntegration.php','delIntegration','Executed','1','0','0','2017-09-02',164),(294,'cbupd-0000165','joebordes','uitype50DateTimeinWebservice2','build/changeSets/2017/uitype50DateTimeinWebservice2.php','uitype50DateTimeinWebservice2','Executed','1','0','0','2017-09-02',165),(295,'cbupd-0000166','joebordes','addWebdomainWebFormscb','build/changeSets/2017/addWebdomainWebFormscb.php','addWebdomainWebFormscb','Executed','1','0','0','2017-09-02',166),(296,'cbupd-0000167','joebordes','cbmqtmdbdistributortables','build/changeSets/2017/cbmqtmdbdistributortables.php','cbmqtmdbdistributortables','Executed','1','0','0','2017-09-02',167),(297,'cbupd-0000168','joebordes','corebossettingstable','build/changeSets/2017/corebossettingstable.php','corebossettingstable','Executed','1','0','0','2017-09-02',168),(298,'cbupd-0000169','omarllorens','mysqlstrictAssets','build/changeSets/2017/mysqlstrictAssets.php','mysqlstrictAssets','Executed','1','0','0','2017-09-02',169),(299,'cbupd-0000170','joebordes','ModCommentsImportExport','build/changeSets/2017/ModCommentsImportExport.php','ModCommentsImportExport','Executed','1','0','0','2017-09-02',170),(300,'cbupd-0000171','joebordes','mysqlstrictGoogleSync','build/changeSets/2017/mysqlstrictGoogleSync.php','mysqlstrictGoogleSync','Executed','1','0','0','2017-09-02',171),(301,'cbupd-0000172','joebordes','installcbcalendar','build/changeSets/2017/installcbcalendar.php','installcbcalendar','Executed','1','0','0','2017-09-02',172),(302,'cbupd-0000173','joebordes','supportForImageFieldsonProducts','build/changeSets/2017/supportForImageFieldsonProducts.php','supportForImageFieldsonProducts','Executed','1','0','0','2017-09-02',173),(303,'cbupd-0000174','joebordes','delConfigPerformance','build/changeSets/2017/delConfigPerformance.php','delConfigPerformance','Executed','1','0','0','2017-09-02',174),(304,'cbupd-0000175','joebordes','workflowexpressionfunctions','build/changeSets/2017/workflowexpressionfunctions.php','workflowexpressionfunctions','Executed','1','0','0','2017-09-02',175),(305,'cbupd-0000176','joebordes','changeTypeOfCampaignNumSent','build/changeSets/2017/changeTypeOfCampaignNumSent.php','changeTypeOfCampaignNumSent','Executed','1','0','0','2017-09-02',176),(306,'cbupd-0000177','joebordes','dropdocumentwrite','build/changeSets/2017/dropdocumentwrite.php','dropdocumentwrite','Executed','1','0','0','2017-09-02',177),(307,'cbupd-0000178','joebordes','fixMassEditableOnuitype4','build/changeSets/2017/fixMassEditableOnuitype4.php','fixMassEditableOnuitype4','Executed','1','0','0','2017-09-02',178),(308,'cbupd-0000179','joebordes','changeActivityRelatedListTocbCalendar','build/changeSets/2017/changeActivityRelatedListTocbCalendar.php','changeActivityRelatedListTocbCalendar','Executed','1','0','0','2017-09-02',179),(309,'cbupd-0000180','joebordes','addUIType1614and1615WebserviceFieldType','build/changeSets/2017/addUIType1614and1615WebserviceFieldType.php','addUIType1614and1615WebserviceFieldType','Executed','1','0','0','2017-09-02',180),(310,'cbupd-0000181','joebordes','cbmqtmdbdistributordeliverafter','build/changeSets/2017/cbmqtmdbdistributordeliverafter.php','cbmqtmdbdistributordeliverafter','Executed','1','0','0','2017-09-02',181),(311,'cbupd-0000182','joebordes','installcbtranslation','build/changeSets/2017/installcbtranslation.php','installcbtranslation','Executed','1','0','0','2017-09-02',182),(312,'cbupd-0000183','joebordes','fixCalendarPresenceValue','build/changeSets/2017/fixCalendarPresenceValue.php','fixCalendarPresenceValue','Executed','1','0','0','2017-09-02',183),(313,'cbupd-0000184','joebordes','fixCalendarLocationDisplayTypeValue','build/changeSets/2017/fixCalendarLocationDisplayTypeValue.php','fixCalendarLocationDisplayTypeValue','Executed','1','0','0','2017-09-02',184),(314,'cbupd-0000185','joebordes','fixCalendarRelations','build/changeSets/2017/fixCalendarRelations.php','fixCalendarRelations','Executed','1','0','0','2017-09-02',185),(315,'cbupd-0000186','omarllorens','syncSeactivityRelWithAllRelIdOnCbCalendar','build/changeSets/2017/syncSeactivityRelWithAllRelIdOnCbCalendar.php','syncSeactivityRelWithAllRelIdOnCbCalendar','Executed','1','0','0','2017-09-02',186),(316,'cbupd-0000187','AlbanaCelepija','addUIType1025WebserviceFieldType','build/changeSets/addUIType1025WebserviceFieldType.php','addUIType1025WebserviceFieldType','Executed','1','0','0','2017-09-02',187),(317,'cbupd-0000188','joebordes','delEmail104uitype','build/changeSets/2017/delEmail104uitype.php','delEmail104uitype','Executed','1','0','0','2017-09-02',188),(318,'cbupd-0000189','joebordes','delFieldFormula','build/changeSets/2017/delFieldFormula.php','delFieldFormula','Executed','1','0','0','2017-09-02',189),(319,'cbupd-0000190','joebordes','cleanoptimizedatabase_200','build/changeSets/2017/cleanoptimizedatabase_200.php','cleanoptimizedatabase_200','Executed','1','0','0','2017-09-02',190);
/*!40000 ALTER TABLE `vtiger_cbupdater` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_cbupdatercf`
--

DROP TABLE IF EXISTS `vtiger_cbupdatercf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_cbupdatercf` (
  `cbupdaterid` int(11) NOT NULL,
  PRIMARY KEY (`cbupdaterid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_cbupdatercf`
--

LOCK TABLES `vtiger_cbupdatercf` WRITE;
/*!40000 ALTER TABLE `vtiger_cbupdatercf` DISABLE KEYS */;
INSERT INTO `vtiger_cbupdatercf` VALUES (125),(126),(127),(128),(129),(130),(131),(132),(133),(134),(135),(136),(137),(138),(139),(140),(141),(142),(143),(144),(146),(147),(148),(149),(150),(151),(152),(153),(154),(155),(156),(157),(158),(159),(160),(161),(162),(163),(164),(165),(166),(167),(168),(169),(170),(171),(172),(173),(174),(175),(176),(177),(178),(179),(180),(181),(182),(183),(184),(185),(186),(187),(188),(189),(190),(191),(192),(193),(194),(195),(196),(197),(198),(199),(200),(201),(202),(203),(204),(205),(206),(207),(208),(209),(210),(211),(212),(213),(214),(215),(216),(217),(218),(219),(220),(221),(222),(223),(224),(225),(226),(227),(228),(229),(230),(231),(232),(233),(234),(235),(236),(237),(238),(239),(240),(241),(242),(243),(244),(245),(246),(247),(248),(249),(250),(251),(252),(253),(254),(255),(256),(257),(258),(259),(260),(261),(262),(263),(264),(265),(266),(267),(268),(269),(270),(271),(272),(273),(274),(275),(276),(281),(282),(283),(284),(285),(286),(287),(288),(289),(290),(291),(292),(293),(294),(295),(296),(297),(298),(299),(300),(301),(302),(303),(304),(305),(306),(307),(308),(309),(310),(311),(312),(313),(314),(315),(316),(317),(318),(319);
/*!40000 ALTER TABLE `vtiger_cbupdatercf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_cntactivityrel`
--

DROP TABLE IF EXISTS `vtiger_cntactivityrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_cntactivityrel` (
  `contactid` int(19) NOT NULL DEFAULT '0',
  `activityid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`contactid`,`activityid`),
  KEY `cntactivityrel_contactid_idx` (`contactid`),
  KEY `cntactivityrel_activityid_idx` (`activityid`),
  CONSTRAINT `fk_2_vtiger_cntactivityrel` FOREIGN KEY (`contactid`) REFERENCES `vtiger_contactdetails` (`contactid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_cntactivityrel`
--

LOCK TABLES `vtiger_cntactivityrel` WRITE;
/*!40000 ALTER TABLE `vtiger_cntactivityrel` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_cntactivityrel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_cobropago`
--

DROP TABLE IF EXISTS `vtiger_cobropago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_cobropago` (
  `cobropagoid` int(11) NOT NULL DEFAULT '0',
  `reference` varchar(256) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `related_id` int(11) DEFAULT NULL,
  `register` date DEFAULT NULL,
  `duedate` date DEFAULT NULL,
  `paid` varchar(3) DEFAULT NULL,
  `credit` varchar(3) DEFAULT NULL,
  `paymentmode` varchar(256) DEFAULT NULL,
  `paymentcategory` varchar(256) DEFAULT NULL,
  `amount` decimal(14,2) DEFAULT NULL,
  `cost` decimal(14,2) DEFAULT NULL,
  `benefit` decimal(14,2) DEFAULT NULL,
  `comercialid` int(19) DEFAULT NULL,
  `update_log` text,
  `cyp_no` varchar(50) DEFAULT NULL,
  `paymentdate` date DEFAULT NULL,
  PRIMARY KEY (`cobropagoid`),
  KEY `parent_id` (`parent_id`),
  KEY `related_id` (`related_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_cobropago`
--

LOCK TABLES `vtiger_cobropago` WRITE;
/*!40000 ALTER TABLE `vtiger_cobropago` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_cobropago` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_cobropagocf`
--

DROP TABLE IF EXISTS `vtiger_cobropagocf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_cobropagocf` (
  `cobropagoid` int(11) NOT NULL,
  PRIMARY KEY (`cobropagoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_cobropagocf`
--

LOCK TABLES `vtiger_cobropagocf` WRITE;
/*!40000 ALTER TABLE `vtiger_cobropagocf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_cobropagocf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_cobropagoconfig`
--

DROP TABLE IF EXISTS `vtiger_cobropagoconfig`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_cobropagoconfig` (
  `bluepay_accountid` varchar(200) DEFAULT NULL,
  `bluepay_secretkey` varchar(150) DEFAULT NULL,
  `bluepay_mode` varchar(150) DEFAULT NULL,
  `block_paid` varchar(3) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_cobropagoconfig`
--

LOCK TABLES `vtiger_cobropagoconfig` WRITE;
/*!40000 ALTER TABLE `vtiger_cobropagoconfig` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_cobropagoconfig` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_contactaddress`
--

DROP TABLE IF EXISTS `vtiger_contactaddress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_contactaddress` (
  `contactaddressid` int(19) NOT NULL DEFAULT '0',
  `mailingcity` varchar(40) DEFAULT NULL,
  `mailingstreet` varchar(250) DEFAULT NULL,
  `mailingcountry` varchar(40) DEFAULT NULL,
  `othercountry` varchar(30) DEFAULT NULL,
  `mailingstate` varchar(30) DEFAULT NULL,
  `mailingpobox` varchar(30) DEFAULT NULL,
  `othercity` varchar(40) DEFAULT NULL,
  `otherstate` varchar(50) DEFAULT NULL,
  `mailingzip` varchar(30) DEFAULT NULL,
  `otherzip` varchar(30) DEFAULT NULL,
  `otherstreet` varchar(250) DEFAULT NULL,
  `otherpobox` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`contactaddressid`),
  CONSTRAINT `fk_1_vtiger_contactaddress` FOREIGN KEY (`contactaddressid`) REFERENCES `vtiger_contactdetails` (`contactid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_contactaddress`
--

LOCK TABLES `vtiger_contactaddress` WRITE;
/*!40000 ALTER TABLE `vtiger_contactaddress` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_contactaddress` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_contactdetails`
--

DROP TABLE IF EXISTS `vtiger_contactdetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_contactdetails` (
  `contactid` int(19) NOT NULL DEFAULT '0',
  `contact_no` varchar(100) NOT NULL,
  `accountid` int(19) DEFAULT NULL,
  `salutation` varchar(200) DEFAULT NULL,
  `firstname` varchar(40) DEFAULT NULL,
  `lastname` varchar(80) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `department` varchar(230) DEFAULT NULL,
  `fax` varchar(50) DEFAULT NULL,
  `reportsto` int(11) DEFAULT NULL,
  `training` varchar(50) DEFAULT NULL,
  `otheremail` varchar(100) DEFAULT NULL,
  `secondaryemail` varchar(100) DEFAULT NULL,
  `donotcall` varchar(3) DEFAULT NULL,
  `emailoptout` varchar(3) DEFAULT '0',
  `imagename` varchar(150) DEFAULT NULL,
  `reference` varchar(3) DEFAULT NULL,
  `notify_owner` varchar(3) DEFAULT '0',
  `isconvertedfromlead` varchar(3) DEFAULT NULL,
  `convertedfromlead` int(11) DEFAULT NULL,
  PRIMARY KEY (`contactid`),
  KEY `contactdetails_accountid_idx` (`accountid`),
  KEY `email_idx` (`email`),
  CONSTRAINT `fk_1_vtiger_contactdetails` FOREIGN KEY (`contactid`) REFERENCES `vtiger_crmentity` (`crmid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_contactdetails`
--

LOCK TABLES `vtiger_contactdetails` WRITE;
/*!40000 ALTER TABLE `vtiger_contactdetails` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_contactdetails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_contactscf`
--

DROP TABLE IF EXISTS `vtiger_contactscf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_contactscf` (
  `contactid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`contactid`),
  CONSTRAINT `fk_1_vtiger_contactscf` FOREIGN KEY (`contactid`) REFERENCES `vtiger_contactdetails` (`contactid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_contactscf`
--

LOCK TABLES `vtiger_contactscf` WRITE;
/*!40000 ALTER TABLE `vtiger_contactscf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_contactscf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_contactsubdetails`
--

DROP TABLE IF EXISTS `vtiger_contactsubdetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_contactsubdetails` (
  `contactsubscriptionid` int(19) NOT NULL DEFAULT '0',
  `homephone` varchar(50) DEFAULT NULL,
  `otherphone` varchar(50) DEFAULT NULL,
  `assistant` varchar(30) DEFAULT NULL,
  `assistantphone` varchar(50) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `laststayintouchrequest` int(30) DEFAULT '0',
  `laststayintouchsavedate` int(19) DEFAULT '0',
  `leadsource` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`contactsubscriptionid`),
  CONSTRAINT `fk_1_vtiger_contactsubdetails` FOREIGN KEY (`contactsubscriptionid`) REFERENCES `vtiger_contactdetails` (`contactid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_contactsubdetails`
--

LOCK TABLES `vtiger_contactsubdetails` WRITE;
/*!40000 ALTER TABLE `vtiger_contactsubdetails` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_contactsubdetails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_contpotentialrel`
--

DROP TABLE IF EXISTS `vtiger_contpotentialrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_contpotentialrel` (
  `contactid` int(19) NOT NULL DEFAULT '0',
  `potentialid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`contactid`,`potentialid`),
  KEY `contpotentialrel_potentialid_idx` (`potentialid`),
  KEY `contpotentialrel_contactid_idx` (`contactid`),
  CONSTRAINT `fk_2_vtiger_contpotentialrel` FOREIGN KEY (`potentialid`) REFERENCES `vtiger_potential` (`potentialid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_contpotentialrel`
--

LOCK TABLES `vtiger_contpotentialrel` WRITE;
/*!40000 ALTER TABLE `vtiger_contpotentialrel` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_contpotentialrel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_contract_priority`
--

DROP TABLE IF EXISTS `vtiger_contract_priority`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_contract_priority` (
  `contract_priorityid` int(11) NOT NULL AUTO_INCREMENT,
  `contract_priority` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`contract_priorityid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_contract_priority`
--

LOCK TABLES `vtiger_contract_priority` WRITE;
/*!40000 ALTER TABLE `vtiger_contract_priority` DISABLE KEYS */;
INSERT INTO `vtiger_contract_priority` VALUES (1,'Low',1,220),(2,'Normal',1,221),(3,'High',1,222);
/*!40000 ALTER TABLE `vtiger_contract_priority` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_contract_priority_seq`
--

DROP TABLE IF EXISTS `vtiger_contract_priority_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_contract_priority_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_contract_priority_seq`
--

LOCK TABLES `vtiger_contract_priority_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_contract_priority_seq` DISABLE KEYS */;
INSERT INTO `vtiger_contract_priority_seq` VALUES (3);
/*!40000 ALTER TABLE `vtiger_contract_priority_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_contract_status`
--

DROP TABLE IF EXISTS `vtiger_contract_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_contract_status` (
  `contract_statusid` int(11) NOT NULL AUTO_INCREMENT,
  `contract_status` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`contract_statusid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_contract_status`
--

LOCK TABLES `vtiger_contract_status` WRITE;
/*!40000 ALTER TABLE `vtiger_contract_status` DISABLE KEYS */;
INSERT INTO `vtiger_contract_status` VALUES (1,'Undefined',1,214),(2,'In Planning',1,215),(3,'In Progress',1,216),(4,'On Hold',1,217),(5,'Complete',0,218),(6,'Archived',1,219);
/*!40000 ALTER TABLE `vtiger_contract_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_contract_status_seq`
--

DROP TABLE IF EXISTS `vtiger_contract_status_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_contract_status_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_contract_status_seq`
--

LOCK TABLES `vtiger_contract_status_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_contract_status_seq` DISABLE KEYS */;
INSERT INTO `vtiger_contract_status_seq` VALUES (6);
/*!40000 ALTER TABLE `vtiger_contract_status_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_contract_type`
--

DROP TABLE IF EXISTS `vtiger_contract_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_contract_type` (
  `contract_typeid` int(11) NOT NULL AUTO_INCREMENT,
  `contract_type` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`contract_typeid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_contract_type`
--

LOCK TABLES `vtiger_contract_type` WRITE;
/*!40000 ALTER TABLE `vtiger_contract_type` DISABLE KEYS */;
INSERT INTO `vtiger_contract_type` VALUES (1,'Support',1,223),(2,'Services',1,224),(3,'Administrative',1,225);
/*!40000 ALTER TABLE `vtiger_contract_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_contract_type_seq`
--

DROP TABLE IF EXISTS `vtiger_contract_type_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_contract_type_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_contract_type_seq`
--

LOCK TABLES `vtiger_contract_type_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_contract_type_seq` DISABLE KEYS */;
INSERT INTO `vtiger_contract_type_seq` VALUES (3);
/*!40000 ALTER TABLE `vtiger_contract_type_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_convertleadmapping`
--

DROP TABLE IF EXISTS `vtiger_convertleadmapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_convertleadmapping` (
  `cfmid` int(19) NOT NULL AUTO_INCREMENT,
  `leadfid` int(19) NOT NULL,
  `accountfid` int(19) DEFAULT NULL,
  `contactfid` int(19) DEFAULT NULL,
  `potentialfid` int(19) DEFAULT NULL,
  `editable` int(19) DEFAULT '1',
  PRIMARY KEY (`cfmid`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_convertleadmapping`
--

LOCK TABLES `vtiger_convertleadmapping` WRITE;
/*!40000 ALTER TABLE `vtiger_convertleadmapping` DISABLE KEYS */;
INSERT INTO `vtiger_convertleadmapping` VALUES (1,43,1,0,110,0),(2,49,14,0,0,1),(3,40,3,69,0,NULL),(4,0,0,0,0,NULL),(5,44,5,77,0,1),(6,52,13,0,0,1),(7,46,9,80,0,0),(8,48,4,0,0,1),(9,61,26,98,0,1),(10,60,30,0,0,1),(11,62,32,104,0,1),(12,63,28,100,0,1),(13,59,24,96,0,1),(14,64,34,106,0,1),(15,61,27,0,0,1),(16,60,31,0,0,1),(17,62,33,0,0,1),(18,63,29,0,0,1),(19,59,25,0,0,1),(20,64,35,0,0,1),(21,65,36,109,125,1),(22,37,0,66,0,1),(23,38,0,67,0,0),(24,41,0,70,0,0),(25,42,0,71,0,1),(26,45,0,76,0,1),(27,55,0,83,0,1),(28,47,0,74,117,1),(29,50,0,0,0,1),(30,53,10,0,0,1),(31,51,17,0,0,1);
/*!40000 ALTER TABLE `vtiger_convertleadmapping` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_crmentity`
--

DROP TABLE IF EXISTS `vtiger_crmentity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_crmentity` (
  `crmid` int(19) NOT NULL,
  `smcreatorid` int(19) NOT NULL DEFAULT '0',
  `smownerid` int(19) NOT NULL DEFAULT '0',
  `modifiedby` int(19) NOT NULL DEFAULT '0',
  `setype` varchar(100) DEFAULT NULL,
  `description` mediumtext,
  `createdtime` datetime NOT NULL,
  `modifiedtime` datetime NOT NULL,
  `viewedtime` datetime DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `version` int(19) NOT NULL DEFAULT '0',
  `presence` int(1) DEFAULT '1',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`crmid`),
  KEY `crmentity_smcreatorid_idx` (`smcreatorid`),
  KEY `crmentity_modifiedby_idx` (`modifiedby`),
  KEY `crmentity_deleted_idx` (`deleted`),
  KEY `crm_ownerid_del_setype_idx` (`smownerid`,`deleted`,`setype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_crmentity`
--

LOCK TABLES `vtiger_crmentity` WRITE;
/*!40000 ALTER TABLE `vtiger_crmentity` DISABLE KEYS */;
INSERT INTO `vtiger_crmentity` VALUES (125,1,1,1,'cbupdater','Upgrade from vtigerCRM5.4 to coreBOS5.4','2014-10-07 15:53:55','2014-10-07 15:53:55',NULL,NULL,0,1,0),(126,1,1,1,'cbupdater','Apply coreBOS Customer Portal REST enhancements','2014-10-07 15:53:56','2014-10-07 15:53:56',NULL,NULL,0,1,0),(127,1,1,1,'cbupdater','Document Related List enhancements','2014-10-07 15:53:56','2014-10-07 15:53:56',NULL,NULL,0,1,0),(128,1,1,1,'cbupdater','Department field size limit #177','2014-10-07 15:53:56','2014-10-07 15:53:56',NULL,NULL,0,1,0),(129,1,1,1,'cbupdater','Email Reports','2014-10-07 15:53:57','2014-10-07 15:53:57',NULL,NULL,0,1,0),(130,1,1,1,'cbupdater','Potential Forecast field and workflow','2014-10-07 15:53:57','2014-10-07 15:53:57',NULL,NULL,0,1,0),(131,1,1,1,'cbupdater','Clean database of unused fields and apply SQL optimizations: #140','2014-10-07 15:53:57','2014-10-07 15:53:57',NULL,NULL,0,1,0),(132,1,1,1,'cbupdater','Upgrade from coreBOS5.4 to coreBOS5.5','2014-10-07 15:53:57','2014-10-07 15:53:57',NULL,NULL,0,1,0),(133,1,1,1,'cbupdater','Move workflow tasks to database to make easier adding new ones','2014-10-07 15:53:58','2014-10-07 15:53:58',NULL,NULL,0,1,0),(134,1,1,1,'cbupdater','Add/Delete Tags workflow task','2014-10-07 15:53:58','2014-10-07 15:53:58',NULL,NULL,0,1,0),(135,1,1,1,'cbupdater','Workflow custom method to update assigned to of contacts on account save','2014-10-07 15:53:58','2014-10-07 15:53:58',NULL,NULL,0,1,0),(136,1,1,1,'cbupdater','Custom field support on FAQ module','2014-10-07 15:53:58','2014-10-07 15:53:58',NULL,NULL,0,1,0),(137,1,1,1,'cbupdater','Install Payment Control module','2014-10-07 15:53:59','2014-10-07 15:53:59',NULL,NULL,0,1,0),(138,1,1,1,'cbupdater','Add Block, Perspective and Continuous','2014-10-07 15:54:00','2014-10-07 15:54:00',NULL,NULL,0,1,0),(139,1,1,1,'cbupdater','Workflow custom method to relate products/services with accounts/contacts/vendors on sales','2014-10-07 15:54:00','2014-10-07 15:54:00',NULL,NULL,0,1,0),(140,1,1,1,'cbupdater','Add direct Email support on Project/ProjectTask/HelpDesk/Potentials','2014-10-07 15:54:00','2014-10-07 15:54:00',NULL,NULL,0,1,0),(141,1,1,1,'cbupdater','Add support for Email management via REST','2014-10-07 15:54:01','2014-10-07 15:54:01',NULL,NULL,0,1,0),(142,1,1,1,'cbupdater','Add support for user based tag visualization','2014-10-07 15:54:01','2014-10-07 15:54:01',NULL,NULL,0,1,0),(143,1,1,1,'cbupdater','Instalo Calendar4You','2014-10-07 15:54:01','2014-10-07 15:54:01',NULL,NULL,0,1,0),(144,1,1,1,'cbupdater','Convert Vendor phone field to correct phone type to support calls','2014-10-07 15:54:01','2014-10-07 15:54:01','2014-10-07 16:26:21',NULL,0,1,0),(146,1,1,1,'cbupdater','Add Calendar Events to Vendors','2014-10-07 16:26:11','2014-10-07 16:26:11','2014-10-07 16:26:25',NULL,0,1,0),(147,1,1,1,'cbupdater','Add SendEmailToSender field on Users','2016-04-24 10:32:19','2016-04-24 10:32:19',NULL,NULL,0,1,0),(148,1,1,1,'cbupdater','Set created and modified Time to DateTime Field type','2016-04-24 10:32:19','2016-04-24 10:32:19',NULL,NULL,0,1,0),(149,1,1,1,'cbupdater','Move detail view widget blocks to end of blocks to maintain current visibility and structure','2016-04-24 10:32:20','2016-04-24 10:32:20',NULL,NULL,0,1,0),(150,1,1,1,'cbupdater','Correct Project TargetBudget Type: convert to number','2016-04-24 10:32:20','2016-04-24 10:32:20',NULL,NULL,0,1,0),(151,1,1,1,'cbupdater','Add fields in calendar4you','2016-04-24 10:32:20','2016-04-24 10:32:20',NULL,NULL,0,1,0),(152,1,1,1,'cbupdater','Send Email from support','2016-04-24 10:32:21','2016-04-24 10:32:21',NULL,NULL,0,1,0),(153,1,1,1,'cbupdater','Mass Upload Image On Product','2016-04-24 10:32:21','2016-04-24 10:32:21',NULL,NULL,0,1,0),(154,1,1,1,'cbupdater','Product/Service and PB active default value: move to database instead of code','2016-04-24 10:32:21','2016-04-24 10:32:21',NULL,NULL,0,1,0),(155,1,1,1,'cbupdater','Add fields in calendar4you 2','2016-04-24 10:32:21','2016-04-24 10:32:21',NULL,NULL,0,1,0),(156,1,1,1,'cbupdater','Vendor Related List on Contacts','2016-04-24 10:32:22','2016-04-24 10:32:22',NULL,NULL,0,1,0),(157,1,1,1,'cbupdater','Optimize database with interesting changes from vtiger crm 6.1','2016-04-24 10:32:22','2016-04-24 10:32:22',NULL,NULL,0,1,0),(158,1,1,1,'cbupdater','Update assigned_user_id field in Services to V~M, to no obtain an error in quick create ','2016-04-24 10:32:22','2016-04-24 10:32:22',NULL,NULL,0,1,0),(159,1,1,1,'cbupdater','Undo insert into vtiger_ws_referencetype which was incorrectly added','2016-04-24 10:32:22','2016-04-24 10:32:22',NULL,NULL,0,1,0),(160,1,1,1,'cbupdater','Put refrestoken in calendar4you google access table to null','2016-04-24 10:32:23','2016-04-24 10:32:23',NULL,NULL,0,1,0),(161,1,1,1,'cbupdater','Install Globals Variable module','2016-04-24 10:32:23','2016-04-24 10:32:23',NULL,NULL,0,1,0),(162,1,1,1,'cbupdater','Change some uitype 23 fields to 5, to be possible to edit from DetailView','2016-04-24 10:32:23','2016-04-24 10:32:23',NULL,NULL,0,1,0),(163,1,1,1,'cbupdater','Install On Schedule Workflows','2016-04-24 10:32:24','2016-04-24 10:32:24',NULL,NULL,0,1,0),(164,1,1,1,'cbupdater','Import/Export/Dedup Campaigns','2016-04-24 10:32:25','2016-04-24 10:32:25',NULL,NULL,0,1,0),(165,1,1,1,'cbupdater','Set Service or Product by default in ProductLines','2016-04-24 10:32:25','2016-04-24 10:32:25',NULL,NULL,0,1,0),(166,1,1,1,'cbupdater','Add icaon to Add Payment link','2016-04-24 10:32:25','2016-04-24 10:32:25',NULL,NULL,0,1,0),(167,1,1,1,'cbupdater','ModComments: add related assignedto email for workflows','2016-04-24 10:32:25','2016-04-24 10:32:25',NULL,NULL,0,1,0),(168,1,1,1,'cbupdater','Update currency exchange rates cron.','2016-04-24 10:32:26','2016-04-24 10:32:26',NULL,NULL,0,1,0),(169,1,1,1,'cbupdater','Define new global variables','2016-04-24 10:32:26','2016-04-24 10:32:26',NULL,NULL,0,1,0),(170,1,1,1,'cbupdater','Install InventoryDetails module','2016-04-24 10:32:26','2016-04-24 10:32:26',NULL,NULL,0,1,0),(171,1,1,1,'cbupdater','Create configuration tables for Modules on Calendar','2016-04-24 10:32:27','2016-04-24 10:32:27',NULL,NULL,0,1,0),(172,1,1,1,'cbupdater','Eliminate CustomAction Table','2016-04-24 10:32:27','2016-04-24 10:32:27',NULL,NULL,0,1,0),(173,1,1,1,'cbupdater','Change milestonedate to date','2016-04-24 10:32:27','2016-04-24 10:32:27',NULL,NULL,0,1,0),(174,1,1,1,'cbupdater','phpsysinfo tab not in use anymore: cleanup code and database','2016-04-24 10:32:27','2016-04-24 10:32:27',NULL,NULL,0,1,0),(175,1,1,1,'cbupdater','Import coreBOS Updater changeset','2016-04-24 10:32:28','2016-04-24 10:32:28',NULL,NULL,0,1,0),(176,1,1,1,'cbupdater','Product stock control functionality','2016-04-24 10:32:28','2016-04-24 10:32:28',NULL,NULL,0,1,0),(177,1,1,1,'cbupdater','Import/Export support for inventory modules','2016-04-24 10:32:29','2016-04-24 10:32:29',NULL,NULL,0,1,0),(178,1,1,1,'cbupdater','Make Vendors Shareable','2016-04-24 10:32:29','2016-04-24 10:32:29',NULL,NULL,0,1,0),(179,1,1,1,'cbupdater','fix UIType 4 Webservice Field Type','2016-04-24 10:32:29','2016-04-24 10:32:29',NULL,NULL,0,1,0),(180,1,1,1,'cbupdater','Add UIType 1613 and 3313 Webservice Field Type','2016-04-24 10:32:29','2016-04-24 10:32:29',NULL,NULL,0,1,0),(181,1,1,1,'cbupdater','Add UIType 14 Webservice Field Type, fix duration type and fix uitype 3-4','2016-04-24 10:32:30','2016-04-24 10:32:30',NULL,NULL,0,1,0),(182,1,1,1,'cbupdater','Cron Backup services','2016-04-24 10:32:30','2016-04-24 10:32:30',NULL,NULL,0,1,0),(183,1,1,1,'cbupdater','Global Variables UIType ModuleList to 3313 and Fix EntityID','2016-04-24 10:32:30','2016-04-24 10:32:30',NULL,NULL,0,1,0),(184,1,1,1,'cbupdater','Add CyP No and PaymentDate de Payments (CobroPago)','2016-04-24 10:32:30','2016-04-24 10:32:30',NULL,NULL,0,1,0),(185,1,1,1,'cbupdater','ModTracker: restore record change','2016-04-24 10:32:31','2016-04-24 10:32:31',NULL,NULL,0,1,0),(186,1,1,1,'cbupdater','User fields: add hour_format and start_hour for calendar','2016-04-24 10:32:31','2016-04-24 10:32:31',NULL,NULL,0,1,0),(187,1,1,1,'cbupdater','User fields: add hour_format and start_hour for calendar (missing lists)','2016-04-24 10:32:32','2016-04-24 10:32:32',NULL,NULL,0,1,0),(188,1,1,1,'cbupdater','User fields: add hour_format and start_hour for calendar (correct uityp16)','2016-04-24 10:32:32','2016-04-24 10:32:32',NULL,NULL,0,1,0),(189,1,1,1,'cbupdater','Delete ModTracker and Proxy from Settings','2016-04-24 10:32:32','2016-04-24 10:32:32',NULL,NULL,0,1,0),(190,1,1,1,'cbupdater','Add from_mailscanner field to HelpDesk and assign_to field to mailscanner rules','2016-04-24 10:32:33','2016-04-24 10:32:33',NULL,NULL,0,1,0),(191,1,1,1,'cbupdater','Create and relate call activity when creating new record from call','2016-04-24 10:32:33','2016-04-24 10:32:33',NULL,NULL,0,1,0),(192,1,1,1,'cbupdater','Add Contact Hierarchy Action Link','2016-04-24 10:32:33','2016-04-24 10:32:33',NULL,NULL,0,1,0),(193,1,1,1,'cbupdater','HelpDesk Status on Calendar','2016-04-24 10:32:34','2016-04-24 10:32:34',NULL,NULL,0,1,0),(194,1,1,1,'cbupdater','Fix Project Related Lists','2016-04-24 10:32:34','2016-04-24 10:32:34',NULL,NULL,0,1,0),(195,1,1,1,'cbupdater','Enhance calendar field information to support related module information','2016-04-24 10:32:34','2016-04-24 10:32:34',NULL,NULL,0,1,0),(196,1,1,1,'cbupdater','Add Notes action is missing parameter to support establishing link between records','2016-04-24 10:32:35','2016-04-24 10:32:35',NULL,NULL,0,1,0),(197,1,1,1,'cbupdater','Change product active checkbox type','2016-04-24 10:32:36','2016-04-24 10:32:36',NULL,NULL,0,1,0),(198,1,1,1,'cbupdater','Delete Related records task','2016-04-24 10:32:36','2016-04-24 10:32:36',NULL,NULL,0,1,0),(199,1,1,1,'cbupdater','Add sort column to sort workflow tasks','2016-04-24 10:32:36','2016-04-24 10:32:36','2016-04-24 10:34:00',NULL,0,1,0),(200,1,1,1,'cbupdater','Add cost price to Products and Services','2016-04-24 10:32:37','2016-04-24 10:32:37',NULL,NULL,0,1,0),(201,1,1,1,'cbupdater','Maximum 6 decimal precision on currency','2016-04-24 10:32:37','2016-04-24 10:32:37',NULL,NULL,0,1,0),(202,1,1,1,'cbupdater','Add and fix Currencies','2016-04-24 10:32:37','2016-04-24 10:32:37',NULL,NULL,0,1,0),(203,1,1,1,'cbupdater','Install Business Mapping and Conditions','2016-04-24 10:32:38','2016-04-24 10:32:38',NULL,NULL,0,1,0),(204,1,1,1,'cbupdater','Business Mapping and Conditions Target Module field','2016-04-24 10:32:38','2016-04-24 10:32:38',NULL,NULL,0,1,0),(205,1,1,1,'cbupdater','Define new Business Map types','2016-04-24 10:32:38','2016-04-24 10:32:38',NULL,NULL,0,1,0),(206,1,1,1,'cbupdater','Select Business Rules workflow task','2016-04-24 10:32:39','2016-04-24 10:32:39',NULL,NULL,0,1,0),(207,1,1,1,'cbupdater','add half-year recurring frequency to recurring Invoices from Salesorder','2016-04-24 10:32:39','2016-04-24 10:32:39',NULL,NULL,0,1,0),(208,1,1,1,'cbupdater','Fix Calendar Start and End Date On Reports, error with ampersand','2016-06-28 19:06:38','2016-06-28 19:06:38',NULL,NULL,0,1,0),(209,1,1,1,'cbupdater','Separate Create and Edit Permissions','2016-06-28 19:06:38','2016-06-28 19:06:38',NULL,NULL,0,1,0),(210,1,1,1,'cbupdater','Workflow custom method to update assigned to of contact on contact save','2016-06-28 19:06:39','2016-06-28 19:06:39',NULL,NULL,0,1,0),(211,1,1,1,'cbupdater','Update Mobile Module','2016-06-28 19:06:39','2016-06-28 19:06:39',NULL,NULL,0,1,0),(212,1,1,1,'cbupdater','User Failed Login Attempts','2016-06-28 19:06:39','2016-06-28 19:06:39',NULL,NULL,0,1,0),(213,1,1,1,'cbupdater','Eliminate Calendar jquery loading. Use application jquery','2016-06-28 19:06:39','2016-06-28 19:06:39',NULL,NULL,0,1,0),(214,1,1,1,'cbupdater','Add recurring periods to SalesOrder','2016-06-28 19:06:40','2016-06-28 19:06:40',NULL,NULL,0,1,0),(215,1,1,1,'cbupdater','cron Expire User Password After Days','2016-06-28 19:06:40','2016-06-28 19:06:40',NULL,NULL,0,1,0),(216,1,1,1,'cbupdater','Permissions: fixes found while testing','2016-06-28 19:06:40','2016-06-28 19:06:40',NULL,NULL,0,1,0),(217,1,1,1,'cbupdater','Add Minute Interval for wf shceduling','2016-06-28 19:06:40','2016-06-28 19:06:40',NULL,NULL,0,1,0),(218,1,1,1,'cbupdater','Fix types for hours and units in HelpDesk and ServiceContracts','2016-06-28 19:06:41','2016-06-28 19:06:41',NULL,NULL,0,1,0),(219,1,1,1,'cbupdater','WebService: correct Convert Lead parameters','2016-06-28 19:06:41','2016-06-28 19:06:41',NULL,NULL,0,1,0),(220,1,1,1,'cbupdater','Users: set Default Values For User Fields','2016-06-28 21:36:44','2016-06-28 21:36:44',NULL,NULL,0,1,0),(221,1,1,1,'cbupdater','Emails: make shareble by profile settings','2016-10-25 22:23:45','2016-10-25 22:23:45',NULL,NULL,0,1,0),(222,1,1,1,'cbupdater','Apply SQL optimizations','2016-10-25 22:23:45','2016-10-25 22:23:45',NULL,NULL,0,1,0),(223,1,1,1,'cbupdater','Fix event handler if timecontrol is installed','2016-10-25 22:23:46','2016-10-25 22:23:46',NULL,NULL,0,1,0),(224,1,1,1,'cbupdater','Fix activity types with values with special chars','2016-10-25 22:23:46','2016-10-25 22:23:46',NULL,NULL,0,1,0),(225,1,1,1,'cbupdater','cron Watcher Service','2016-10-25 22:23:47','2016-10-25 22:23:47',NULL,NULL,0,1,0),(226,1,1,1,'cbupdater','Empty user_hash column which was a security issue and not being used.','2016-10-25 22:23:47','2016-10-25 22:23:47',NULL,NULL,0,1,0),(227,1,1,1,'cbupdater','Assign Related records workflow task','2016-10-25 22:23:47','2016-10-25 22:23:47',NULL,NULL,0,1,0),(228,1,1,1,'cbupdater','Install Global Variable Definitions infrastructure','2016-10-25 22:23:48','2016-10-25 22:23:48',NULL,NULL,0,1,0),(229,1,1,1,'cbupdater','Add cost price and total to Inventory Details','2016-10-25 22:23:48','2016-10-25 22:23:48',NULL,NULL,0,1,0),(230,1,1,1,'cbupdater','Duplicate Records workflow task','2016-10-25 22:23:48','2016-10-25 22:23:48',NULL,NULL,0,1,0),(231,1,1,1,'cbupdater','Add decimals to status history logs and apply SQL optimizations','2016-10-25 22:23:49','2016-10-25 22:23:49',NULL,NULL,0,1,0),(232,1,1,1,'cbupdater','Add Currency Position to currency settings','2016-10-25 22:23:49','2016-10-25 22:23:49',NULL,NULL,0,1,0),(233,1,1,1,'cbupdater','Mass Document Download action','2016-10-25 22:23:49','2016-10-25 22:23:49',NULL,NULL,0,1,0),(234,1,1,1,'cbupdater','Add Status picklist to Project Task module','2016-10-25 22:23:50','2016-10-25 22:23:50',NULL,NULL,0,1,0),(235,1,1,1,'cbupdater','Add EmailOptOut and Conversion Related Fields to Leads module','2016-10-25 22:23:50','2016-10-25 22:23:50',NULL,NULL,0,1,0),(236,1,1,1,'cbupdater','Add CommentAdded field to HelpDesk module','2016-10-25 22:23:50','2016-10-25 22:23:50',NULL,NULL,0,1,0),(237,1,1,1,'cbupdater','Add relation between Vendors and Documents','2016-10-25 22:23:50','2016-10-25 22:23:50',NULL,NULL,0,1,0),(238,1,1,1,'cbupdater','Change EMail access_count to integer','2016-10-25 22:23:51','2016-10-25 22:23:51',NULL,NULL,0,1,0),(239,1,1,1,'cbupdater','Set number of decimals to uitype 9, percent values','2016-10-25 22:23:51','2016-10-25 22:23:51',NULL,NULL,0,1,0),(240,1,1,1,'cbupdater','Database changes for mysql strict NO_ZERO_IN_DATE','2016-10-25 22:23:51','2016-10-25 22:23:51',NULL,NULL,0,1,0),(241,1,1,1,'cbupdater','FIX:: Set number of decimals to uitype 9, percent values','2016-10-25 22:23:52','2016-10-25 22:23:52',NULL,NULL,0,1,0),(242,1,1,1,'cbupdater','Database changes for mysql strict. Fix update_log for cobropago. Not permit null and we save with null value','2016-10-25 22:23:52','2016-10-25 22:23:52',NULL,NULL,0,1,0),(243,1,1,1,'cbupdater','Database changes for mysql strict. Uitype 101 need a number by default, not accpet empty value','2016-10-25 22:23:52','2016-10-25 22:23:52',NULL,NULL,0,1,0),(244,1,1,1,'cbupdater','Delete Settings Backup and Announcements','2016-10-25 22:23:53','2016-10-25 22:23:53',NULL,NULL,0,1,0),(245,1,1,1,'cbupdater','Delete Customer Portal and Webforms from Settings','2016-10-25 22:23:53','2016-10-25 22:23:53',NULL,NULL,0,1,0),(246,1,1,1,'cbupdater','Update Packing Slip module if is installed ','2016-10-25 22:23:54','2016-10-25 22:23:54',NULL,NULL,0,1,0),(247,1,1,1,'cbupdater','Delete Single Pane View and Configuration Editor from Settings (global variables)','2016-10-25 22:23:54','2016-10-25 22:23:54',NULL,NULL,0,1,0),(248,1,1,1,'cbupdater','Adds a workflow that allows for converting inventory modules to inventory modules','2016-10-25 22:23:54','2016-10-25 22:23:54',NULL,NULL,0,1,0),(249,1,1,1,'cbupdater','Change uitype 58 (campaign) to 10','2017-01-06 23:15:39','2017-01-06 23:15:39',NULL,NULL,0,1,0),(250,1,1,1,'cbupdater','Activate Document fields for mass edit','2017-01-06 23:15:40','2017-01-06 23:15:40',NULL,NULL,0,1,0),(251,1,1,1,'cbupdater','On Delete event in workflows','2017-01-06 23:15:40','2017-01-06 23:15:40',NULL,NULL,0,1,0),(252,1,1,1,'cbupdater','Is Related List Block','2017-01-06 23:15:40','2017-01-06 23:15:40',NULL,NULL,0,1,0),(253,1,1,1,'cbupdater','Fix Packing Slip module, currency fields are on incorrect block and for this not appear on filter columns ','2017-01-06 23:15:40','2017-01-06 23:15:40',NULL,NULL,0,1,0),(254,1,1,1,'cbupdater','Add Divisible checkbox to product and service','2017-01-06 23:15:40','2017-01-06 23:15:40',NULL,NULL,0,1,0),(255,1,1,1,'cbupdater','Delete Administration directory (obsolete code)','2017-01-06 23:15:40','2017-01-06 23:15:40',NULL,NULL,0,1,0),(256,1,1,1,'cbupdater','Delete Settings Notifications (moved to workflow)','2017-01-06 23:15:41','2017-01-06 23:15:41',NULL,NULL,0,1,0),(257,1,1,1,'cbupdater','Delete chat functionality tables','2017-01-06 23:15:41','2017-01-06 23:15:41',NULL,NULL,0,1,0),(258,1,1,1,'cbupdater','Add Indexes on Inventorydetail table','2017-01-06 23:15:41','2017-01-06 23:15:41',NULL,NULL,0,1,0),(259,1,1,1,'cbupdater','vtlib Header Links Load Only On their Module','2017-01-06 23:15:41','2017-01-06 23:15:41',NULL,NULL,0,1,0),(260,1,1,1,'cbupdater','Header Dropzone CSS and javascript','2017-01-06 23:15:41','2017-01-06 23:15:41',NULL,NULL,0,1,0),(261,1,1,1,'cbupdater','mysql strict Mail Manager record table','2017-01-06 23:15:41','2017-01-06 23:15:41',NULL,NULL,0,1,0),(262,1,1,1,'cbupdater','Add UIType 1024 Webservice Field Type','2017-01-06 23:15:41','2017-01-06 23:15:41',NULL,NULL,0,1,0),(263,1,1,1,'cbupdater','Fix Calendar Start and End Date On Reports Filters, error with ampersand','2017-01-06 23:15:41','2017-01-06 23:15:41',NULL,NULL,0,1,0),(264,1,1,1,'cbupdater','Add Created-By Field to all modules','2017-01-06 23:15:42','2017-01-06 23:15:42',NULL,NULL,0,1,0),(265,1,1,1,'cbupdater','Update Mobile Module','2017-01-06 23:15:42','2017-01-06 23:15:42',NULL,NULL,0,1,0),(266,1,1,1,'cbupdater','Make ModComments Shareable','2017-01-06 23:15:42','2017-01-06 23:15:42',NULL,NULL,0,1,0),(267,1,1,1,'cbupdater','Add files on header for calendar4you','2017-01-06 23:15:42','2017-01-06 23:15:42',NULL,NULL,0,1,0),(268,1,1,1,'cbupdater','Set Activity subject field to 250 chars','2017-01-06 23:15:42','2017-01-06 23:15:42',NULL,NULL,0,1,0),(269,1,1,1,'cbupdater','Lightning design evvtmenu','2017-01-06 23:15:42','2017-01-06 23:15:42',NULL,NULL,0,1,0),(270,1,1,1,'cbupdater','Delete Audit Trail and Login History from Settings: use cbAuditTrail and cbLoginHistory','2017-01-06 23:15:42','2017-01-06 23:15:42',NULL,NULL,0,1,0),(271,1,1,1,'cbupdater','Install Terms and Conditions Module','2017-01-06 23:15:42','2017-01-06 23:15:42',NULL,NULL,0,1,0),(272,1,1,1,'cbupdater','Remove Calendar4You Link for moment.mn.js','2017-01-06 23:15:43','2017-01-06 23:15:43',NULL,NULL,0,1,0),(273,1,1,1,'cbupdater','Feature: See Converted Leads','2017-01-06 23:15:43','2017-01-06 23:15:43',NULL,NULL,0,1,0),(274,1,1,1,'cbupdater','Increment module Name/Label size and add missing indexes on SMS','2017-01-06 23:15:43','2017-01-06 23:15:43',NULL,NULL,0,1,0),(275,1,1,1,'cbupdater','Change GlobalVariable Module-List UIType to 3314','2017-01-06 23:15:43','2017-01-06 23:15:43',NULL,NULL,0,1,0),(276,1,1,1,'cbupdater','Add database indexes while studying MySQL queries with no index for a few hours','2017-01-06 23:15:43','2017-01-06 23:15:43',NULL,NULL,0,1,0),(277,1,1,1,'cbTermConditions','','2017-01-06 23:18:01','2017-01-06 23:18:01',NULL,NULL,0,1,0),(278,1,1,1,'cbTermConditions','','2017-01-06 23:18:03','2017-01-06 23:18:03',NULL,NULL,0,1,0),(279,1,1,1,'cbTermConditions','','2017-01-06 23:18:03','2017-01-06 23:18:03',NULL,NULL,0,1,0),(280,1,1,1,'cbTermConditions','','2017-01-06 23:18:05','2017-01-06 23:18:05',NULL,NULL,0,1,0),(281,1,1,1,'cbupdater','Fix Term and Conditions Field table on inventory modules','2017-02-01 11:30:32','2017-02-01 11:30:32',NULL,NULL,0,1,0),(282,1,1,1,'cbupdater','Add moreinfo field to Reports for External URLs, Direct SQL and other future enhancements','2017-02-01 11:30:33','2017-02-01 11:30:33',NULL,NULL,0,1,0),(283,1,1,1,'cbupdater','Create and relate call activity when creating new record from call','2017-09-02 09:21:39','2017-09-02 09:21:39',NULL,NULL,0,1,0),(284,1,1,1,'cbupdater','Migrate biurl to moreinfo','2017-09-02 09:21:39','2017-09-02 09:21:39',NULL,NULL,0,1,0),(285,1,1,1,'cbupdater','Change uitype 68 (helpdesk) to 10','2017-09-02 09:21:39','2017-09-02 09:21:39',NULL,NULL,0,1,0),(286,1,1,1,'cbupdater','fix WordProcessor Merge Table for MySQL strict mode','2017-09-02 09:21:39','2017-09-02 09:21:39',NULL,NULL,0,1,0),(287,1,1,1,'cbupdater','add daily field in cron table','2017-09-02 09:21:39','2017-09-02 09:21:39',NULL,NULL,0,1,0),(288,1,1,1,'cbupdater','Set uitype 50 to DateTime in Webservice','2017-09-02 09:21:39','2017-09-02 09:21:39',NULL,NULL,0,1,0),(289,1,1,1,'cbupdater','Database optimizations and cleanup','2017-09-02 09:21:39','2017-09-02 09:21:39',NULL,NULL,0,1,0),(290,1,1,1,'cbupdater','fix Asterisk Label on Users Module','2017-09-02 09:21:40','2017-09-02 09:21:40',NULL,NULL,0,1,0),(291,1,1,1,'cbupdater','Add database index to PBXManager','2017-09-02 09:21:40','2017-09-02 09:21:40',NULL,NULL,0,1,0),(292,1,1,1,'cbupdater','set PBXManager timeofcall to uiyype 50 so it can be searched on filters and reports','2017-09-02 09:21:40','2017-09-02 09:21:40',NULL,NULL,0,1,0),(293,1,1,1,'cbupdater','Delete Integration extension','2017-09-02 09:21:40','2017-09-02 09:21:40',NULL,NULL,0,1,0),(294,1,1,1,'cbupdater','Delete uitype 50 as reference from Webservice','2017-09-02 09:21:40','2017-09-02 09:21:40',NULL,NULL,0,1,0),(295,1,1,1,'cbupdater','Add WebForm Domain validation field','2017-09-02 09:21:40','2017-09-02 09:21:40',NULL,NULL,0,1,0),(296,1,1,1,'cbupdater','coreBOS Message Queue and Task Manager database tables','2017-09-02 09:21:40','2017-09-02 09:21:40',NULL,NULL,0,1,0),(297,1,1,1,'cbupdater','Generic coreBOS Settings and Configuration database table','2017-09-02 09:21:40','2017-09-02 09:21:40',NULL,NULL,0,1,0),(298,1,1,1,'cbupdater','mysqlStrict support for product field in Assets module','2017-09-02 09:21:40','2017-09-02 09:21:40',NULL,NULL,0,1,0),(299,1,1,1,'cbupdater','Support for Import, Export and Deduplication on ModComments','2017-09-02 09:21:40','2017-09-02 09:21:40',NULL,NULL,0,1,0),(300,1,1,1,'cbupdater','mysqlStrict support for google calendar and contacts authorization','2017-09-02 09:21:41','2017-09-02 09:21:41',NULL,NULL,0,1,0),(301,1,1,1,'cbupdater','Install Calendar remake module','2017-09-02 09:21:41','2017-09-02 09:21:41',NULL,NULL,0,1,0),(302,1,1,1,'cbupdater','Image fields supported on all modules: products and contacts. first step towards multiimage field which is only working now on Products','2017-09-02 09:21:41','2017-09-02 09:21:41',NULL,NULL,0,1,0),(303,1,1,1,'cbupdater','Migrate config.performance.php variables to Global Variables module.','2017-09-02 09:21:41','2017-09-02 09:21:41',NULL,NULL,0,1,0),(304,1,1,1,'cbupdater','Database table for dynamic workflow expression functions.','2017-09-02 09:21:41','2017-09-02 09:21:41',NULL,NULL,0,1,0),(305,1,1,1,'cbupdater','Change TypeOf Campaign NumSent which is percentage and should be number.','2017-09-02 09:21:41','2017-09-02 09:21:41',NULL,NULL,0,1,0),(306,1,1,1,'cbupdater','Load javascript from HTML not document.write.','2017-09-02 09:21:41','2017-09-02 09:21:41',NULL,NULL,0,1,0),(307,1,1,1,'cbupdater','Set MassEditable to 0 for uitype 4 fields','2017-09-02 09:21:42','2017-09-02 09:21:42',NULL,NULL,0,1,0),(308,1,1,1,'cbupdater','changeActivityRelatedListTocbCalendar','2017-09-02 09:21:42','2017-09-02 09:21:42',NULL,NULL,0,1,0),(309,1,1,1,'cbupdater','Add UIType 1614 and 1615 to Webservice Field Type','2017-09-02 09:21:42','2017-09-02 09:21:42',NULL,NULL,0,1,0),(310,1,1,1,'cbupdater','coreBOS Message Queue and Task Manager: Deliver After','2017-09-02 09:21:42','2017-09-02 09:21:42',NULL,NULL,0,1,0),(311,1,1,1,'cbupdater','Install Translation module','2017-09-02 09:21:42','2017-09-02 09:21:42',NULL,NULL,0,1,0),(312,1,1,1,'cbupdater','Fix Calendar Field Presence Value','2017-09-02 09:21:42','2017-09-02 09:21:42',NULL,NULL,0,1,0),(313,1,1,1,'cbupdater','Fix Calendar displaytype Value for location field','2017-09-02 09:21:42','2017-09-02 09:21:42',NULL,NULL,0,1,0),(314,1,1,1,'cbupdater','Fix Calendar relations with missing modules','2017-09-02 09:21:42','2017-09-02 09:21:42',NULL,NULL,0,1,0),(315,1,1,1,'cbupdater','Update new events created with the new module cbCalendar with vtiger_seactivityrel','2017-09-02 09:21:42','2017-09-02 09:21:42',NULL,NULL,0,1,0),(316,1,1,1,'cbupdater','Add uitype 1025 MultiValued Field Webservice Type','2017-09-02 09:21:42','2017-09-02 09:21:42',NULL,NULL,0,1,0),(317,1,1,1,'cbupdater','Delete Email uitype 104 (mostly for users). Use uitype 13.','2017-09-02 09:21:43','2017-09-02 09:21:43',NULL,NULL,0,1,0),(318,1,1,1,'cbupdater','Delete FieldFormula module: Use workflows.','2017-09-02 09:21:43','2017-09-02 09:21:43',NULL,NULL,0,1,0),(319,1,1,1,'cbupdater','Database optimizations and support for mass edit on solution and customer portal fields','2017-09-02 09:21:43','2017-09-02 09:21:43',NULL,NULL,0,1,0),(320,1,1,1,'GlobalVariable','','2017-09-02 09:22:49','2017-09-02 09:22:49',NULL,NULL,0,1,0);
/*!40000 ALTER TABLE `vtiger_crmentity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_crmentity_seq`
--

DROP TABLE IF EXISTS `vtiger_crmentity_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_crmentity_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_crmentity_seq`
--

LOCK TABLES `vtiger_crmentity_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_crmentity_seq` DISABLE KEYS */;
INSERT INTO `vtiger_crmentity_seq` VALUES (320);
/*!40000 ALTER TABLE `vtiger_crmentity_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_crmentityrel`
--

DROP TABLE IF EXISTS `vtiger_crmentityrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_crmentityrel` (
  `crmid` int(11) NOT NULL,
  `module` varchar(100) NOT NULL,
  `relcrmid` int(11) NOT NULL,
  `relmodule` varchar(100) NOT NULL,
  KEY `crmentityrel_crmid_relcrmid_idx` (`crmid`,`relcrmid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_crmentityrel`
--

LOCK TABLES `vtiger_crmentityrel` WRITE;
/*!40000 ALTER TABLE `vtiger_crmentityrel` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_crmentityrel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_cron_task`
--

DROP TABLE IF EXISTS `vtiger_cron_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_cron_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `handler_file` varchar(100) DEFAULT NULL,
  `frequency` int(11) DEFAULT NULL,
  `laststart` int(11) unsigned DEFAULT NULL,
  `lastend` int(11) unsigned DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `module` varchar(100) DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `description` text,
  `daily` varchar(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `handler_file` (`handler_file`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_cron_task`
--

LOCK TABLES `vtiger_cron_task` WRITE;
/*!40000 ALTER TABLE `vtiger_cron_task` DISABLE KEYS */;
INSERT INTO `vtiger_cron_task` VALUES (1,'Workflow','cron/modules/com_vtiger_workflow/com_vtiger_workflow.service',900,NULL,NULL,1,'com_vtiger_workflow',1,'Recommended frequency for Workflow is 15 mins','0'),(2,'RecurringInvoice','cron/modules/SalesOrder/RecurringInvoice.service',43200,NULL,NULL,1,'SalesOrder',2,'Recommended frequency for RecurringInvoice is 12 hours','0'),(3,'SendReminder','cron/SendReminder.service',900,NULL,NULL,1,'Calendar',3,'Recommended frequency for SendReminder is 15 mins','0'),(4,'ScheduleReports','cron/modules/Reports/ScheduleReports.service',900,NULL,NULL,1,'Reports',4,'Recommended frequency for ScheduleReports is 15 mins','0'),(5,'MailScanner','cron/MailScanner.service',900,NULL,NULL,1,'Settings',5,'Recommended frequency for MailScanner is 15 mins','0'),(6,'Scheduled Import','cron/modules/Import/ScheduledImport.service',900,NULL,NULL,0,'Import',6,'','0'),(7,'Calendar4You - GoogleSync','modules/Calendar4You/cron/UpdateEvents.service',900,NULL,NULL,0,'Calendar4You',7,'','0'),(8,'Calendar4You - GoogleSync Insert','modules/Calendar4You/cron/InsertEvents.service',60,1421927705,1421927730,1,'Calendar4You',7,'','0'),(9,'UpdateExchangeRate','cron/UpdateExchangeRate.service',43200,NULL,NULL,1,'Home',8,'Update currency exchange rates.','0'),(10,'Native Backup','cron/modules/VtigerBackup/VtigerBackup.service',86400,0,0,0,'VtigerBackup',7,'Backup with no external tools. Can easily run into memory limitations and really slow down the server. Good for smaller sets of information.','0'),(11,'External Backup','cron/modules/VtigerBackup/ExternalBackup.service',86400,0,0,0,'VtigerBackup',7,'Backup with external tools. mysqldump and zip must be available on server. Fast and good for big sets of information.','0'),(12,'ExpirePasswordAfterDays','cron/ExpirePasswordAfterDays.service',86400,NULL,NULL,0,'Home',9,'Expire users passwords after Application_ExpirePasswordAfterDays days.','0'),(13,'cronWatcherService','modules/CronTasks/cronWatcher.service',1800,NULL,NULL,1,'CronTasks',10,'Send out warning email for long running cron tasks','0');
/*!40000 ALTER TABLE `vtiger_cron_task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_currencies`
--

DROP TABLE IF EXISTS `vtiger_currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_currencies` (
  `currencyid` int(19) NOT NULL AUTO_INCREMENT,
  `currency_name` varchar(200) DEFAULT NULL,
  `currency_code` varchar(50) DEFAULT NULL,
  `currency_symbol` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`currencyid`)
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_currencies`
--

LOCK TABLES `vtiger_currencies` WRITE;
/*!40000 ALTER TABLE `vtiger_currencies` DISABLE KEYS */;
INSERT INTO `vtiger_currencies` VALUES (1,'Albania, Leke','ALL','Lek'),(2,'Argentina, Pesos','ARS','$'),(3,'Aruba, Guilders','AWG','ƒ'),(4,'Australia, Dollars','AUD','$'),(5,'Azerbaijan, New Manats','AZN','ман'),(6,'Bahamas, Dollars','BSD','$'),(7,'Bahrain, Dinar','BHD','BD'),(8,'Barbados, Dollars','BBD','$'),(9,'Belarus, Rubles','BYR','p.'),(10,'Belize, Dollars','BZD','BZ$'),(11,'Bermuda, Dollars','BMD','$'),(12,'Bolivia, Bolivianos','BOB','$b'),(13,'China, Yuan Renminbi','CNY','¥'),(14,'Convertible Marka','BAM','KM'),(15,'Botswana, Pulas','BWP','P'),(16,'Bulgaria, Leva','BGN','лв'),(17,'Brazil, Reais','BRL','R$'),(18,'Great Britain Pounds','GBP','£'),(19,'Brunei Darussalam, Dollars','BND','$'),(20,'Canada, Dollars','CAD','$'),(21,'Cayman Islands, Dollars','KYD','$'),(22,'Chile, Pesos','CLP','$'),(23,'Colombia, Pesos','COP','$'),(24,'Costa Rica, Colón','CRC','₡'),(25,'Croatia, Kuna','HRK','kn'),(26,'Cuba, Pesos','CUP','₱'),(27,'Czech Republic, Koruny','CZK','Kč'),(28,'Cyprus, Pounds','CYP','£'),(29,'Denmark, Kroner','DKK','kr'),(30,'Dominican Republic, Pesos','DOP','RD$'),(31,'East Caribbean, Dollars','XCD','$'),(32,'Egypt, Pounds','EGP','E£'),(33,'El Salvador, Colón','SVC','₡'),(34,'England, Pounds','GBP','£'),(35,'Estonia, Krooni','EEK','kr'),(36,'Euro','EUR','€'),(37,'Falkland Islands, Pounds','FKP','£'),(38,'Fiji, Dollars','FJD','$'),(39,'Ghana, Cedis','GHC','¢'),(40,'Gibraltar, Pounds','GIP','£'),(41,'Guatemala, Quetzales','GTQ','Q'),(42,'Guernsey, Pounds','GGP','£'),(43,'Guyana, Dollars','GYD','$'),(44,'Honduras, Lempiras','HNL','L'),(45,'Hong Kong, Dollars','HKD','HK$'),(46,'Hungary, Forint','HUF','Ft'),(47,'Iceland, Krona','ISK','kr'),(48,'India, Rupees','INR','₹'),(49,'Indonesia, Rupiahs','IDR','Rp'),(50,'Iran, Rials','IRR','﷼'),(51,'Isle of Man, Pounds','IMP','£'),(52,'Israel, New Shekels','ILS','₪'),(53,'Jamaica, Dollars','JMD','J$'),(54,'Japan, Yen','JPY','¥'),(55,'Jersey, Pounds','JEP','£'),(56,'Jordan, Dinar','JOD','JOD'),(57,'Kazakhstan, Tenge','KZT','〒'),(58,'Kenya, Shilling','KES','KES'),(59,'Korea (North), Won','KPW','₩'),(60,'Korea (South), Won','KRW','₩'),(61,'Kuwait, Dinar','KWD','KWD'),(62,'Kyrgyzstan, Soms','KGS','лв'),(63,'Laos, Kips','LAK','₭'),(64,'Latvia, Lati','LVL','Ls'),(65,'Lebanon, Pounds','LBP','£'),(66,'Liberia, Dollars','LRD','$'),(67,'Switzerland Francs','CHF','CHF'),(68,'Lithuania, Litai','LTL','Lt'),(69,'MADAGASCAR, Malagasy Ariary','MGA','MGA'),(70,'Macedonia, Denars','MKD','ден'),(71,'Malaysia, Ringgits','MYR','RM'),(72,'Malta, Liri','MTL','₤'),(73,'Mauritius, Rupees','MUR','₨'),(74,'Mexico, Pesos','MXN','$'),(75,'Mongolia, Tugriks','MNT','₮'),(76,'Mozambique, Meticais','MZN','MT'),(77,'Namibia, Dollars','NAD','$'),(78,'Nepal, Rupees','NPR','₨'),(79,'Netherlands Antilles, Guilders','ANG','ƒ'),(80,'New Zealand, Dollars','NZD','$'),(81,'Nicaragua, Cordobas','NIO','C$'),(82,'Nigeria, Nairas','NGN','₦'),(83,'North Korea, Won','KPW','₩'),(84,'Norway, Krone','NOK','kr'),(85,'Oman, Rials','OMR','﷼'),(86,'Pakistan, Rupees','PKR','₨'),(87,'Panama, Balboa','PAB','B/.'),(88,'Paraguay, Guarani','PYG','Gs'),(89,'Peru, Nuevos Soles','PEN','S/.'),(90,'Philippines, Pesos','PHP','Php'),(91,'Poland, Zlotych','PLN','zł'),(92,'Qatar, Rials','QAR','﷼'),(93,'Romania, New Lei','RON','lei'),(94,'Russia, Rubles','RUB','руб'),(95,'Saint Helena, Pounds','SHP','£'),(96,'Saudi Arabia, Riyals','SAR','﷼'),(97,'Serbia, Dinars','RSD','Дин.'),(98,'Seychelles, Rupees','SCR','₨'),(99,'Singapore, Dollars','SGD','$'),(100,'Solomon Islands, Dollars','SBD','$'),(101,'Somalia, Shillings','SOS','S'),(102,'South Africa, Rand','ZAR','R'),(103,'South Korea, Won','KRW','₩'),(104,'Sri Lanka, Rupees','LKR','₨'),(105,'Sweden, Kronor','SEK','kr'),(106,'Switzerland, Francs','CHF','CHF'),(107,'Suriname, Dollars','SRD','$'),(108,'Syria, Pounds','SYP','£'),(109,'Taiwan, New Dollars','TWD','NT$'),(110,'Thailand, Baht','THB','฿'),(111,'Trinidad and Tobago, Dollars','TTD','TT$'),(112,'Turkey, New Lira','TRY','YTL'),(113,'Turkey, Liras','TRL','₤'),(114,'Tuvalu, Dollars','TVD','$'),(115,'Ukraine, Hryvnia','UAH','₴'),(116,'United Arab Emirates, Dirham','AED','AED'),(117,'United Kingdom, Pounds','GBP','£'),(118,'United Republic of Tanzania, Shilling','TZS','TZS'),(119,'USA, Dollars','USD','$'),(120,'Uruguay, Pesos','UYU','$U'),(121,'Uzbekistan, Sums','UZS','лв'),(122,'Venezuela, Bolivares Fuertes','VEF','Bs'),(123,'Vietnam, Dong','VND','₫'),(124,'Zambia, Kwacha','ZMK','ZMK'),(125,'Yemen, Rials','YER','﷼'),(126,'Zimbabwe Dollars','ZWD','Z$'),(127,'Malawi, Kwacha','MWK','MK'),(128,'Tunisian, Dinar','TD','TD'),(129,'Moroccan, Dirham','MAD','DH'),(130,'Sudanese Pound','SDG','£'),(131,'CFA Franc BCEAO','XOF','CFA'),(132,'CFA Franc BEAC','XAF','CFA'),(133,'Haiti, Gourde','HTG','G'),(134,'Libya, Dinar','LYD','LYD'),(135,'Iraqi Dinar','IQD','ID'),(136,'Maldivian Ruffiya','MVR','MVR'),(137,'Ugandan Shilling','UGX','Sh'),(138,'CFP Franc','XPF','F');
/*!40000 ALTER TABLE `vtiger_currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_currencies_seq`
--

DROP TABLE IF EXISTS `vtiger_currencies_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_currencies_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_currencies_seq`
--

LOCK TABLES `vtiger_currencies_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_currencies_seq` DISABLE KEYS */;
INSERT INTO `vtiger_currencies_seq` VALUES (138);
/*!40000 ALTER TABLE `vtiger_currencies_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_currency`
--

DROP TABLE IF EXISTS `vtiger_currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_currency` (
  `currencyid` int(19) NOT NULL AUTO_INCREMENT,
  `currency` varchar(200) NOT NULL,
  `sortorderid` int(19) NOT NULL DEFAULT '0',
  `presence` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`currencyid`),
  UNIQUE KEY `currency_currency_idx` (`currency`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_currency`
--

LOCK TABLES `vtiger_currency` WRITE;
/*!40000 ALTER TABLE `vtiger_currency` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_currency` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_currency_decimal_separator`
--

DROP TABLE IF EXISTS `vtiger_currency_decimal_separator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_currency_decimal_separator` (
  `currency_decimal_separatorid` int(19) NOT NULL AUTO_INCREMENT,
  `currency_decimal_separator` varchar(2) NOT NULL,
  `sortorderid` int(19) NOT NULL DEFAULT '0',
  `presence` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`currency_decimal_separatorid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_currency_decimal_separator`
--

LOCK TABLES `vtiger_currency_decimal_separator` WRITE;
/*!40000 ALTER TABLE `vtiger_currency_decimal_separator` DISABLE KEYS */;
INSERT INTO `vtiger_currency_decimal_separator` VALUES (1,'.',0,1),(2,',',1,1),(3,'\'',2,1),(4,' ',3,1),(5,'$',4,1);
/*!40000 ALTER TABLE `vtiger_currency_decimal_separator` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_currency_decimal_separator_seq`
--

DROP TABLE IF EXISTS `vtiger_currency_decimal_separator_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_currency_decimal_separator_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_currency_decimal_separator_seq`
--

LOCK TABLES `vtiger_currency_decimal_separator_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_currency_decimal_separator_seq` DISABLE KEYS */;
INSERT INTO `vtiger_currency_decimal_separator_seq` VALUES (5);
/*!40000 ALTER TABLE `vtiger_currency_decimal_separator_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_currency_grouping_pattern`
--

DROP TABLE IF EXISTS `vtiger_currency_grouping_pattern`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_currency_grouping_pattern` (
  `currency_grouping_patternid` int(19) NOT NULL AUTO_INCREMENT,
  `currency_grouping_pattern` varchar(200) NOT NULL,
  `sortorderid` int(19) NOT NULL DEFAULT '0',
  `presence` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`currency_grouping_patternid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_currency_grouping_pattern`
--

LOCK TABLES `vtiger_currency_grouping_pattern` WRITE;
/*!40000 ALTER TABLE `vtiger_currency_grouping_pattern` DISABLE KEYS */;
INSERT INTO `vtiger_currency_grouping_pattern` VALUES (1,'123,456,789',0,1),(2,'123456789',1,1),(3,'123456,789',2,1),(4,'12,34,56,789',3,1);
/*!40000 ALTER TABLE `vtiger_currency_grouping_pattern` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_currency_grouping_pattern_seq`
--

DROP TABLE IF EXISTS `vtiger_currency_grouping_pattern_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_currency_grouping_pattern_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_currency_grouping_pattern_seq`
--

LOCK TABLES `vtiger_currency_grouping_pattern_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_currency_grouping_pattern_seq` DISABLE KEYS */;
INSERT INTO `vtiger_currency_grouping_pattern_seq` VALUES (4);
/*!40000 ALTER TABLE `vtiger_currency_grouping_pattern_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_currency_grouping_separator`
--

DROP TABLE IF EXISTS `vtiger_currency_grouping_separator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_currency_grouping_separator` (
  `currency_grouping_separatorid` int(19) NOT NULL AUTO_INCREMENT,
  `currency_grouping_separator` varchar(2) NOT NULL,
  `sortorderid` int(19) NOT NULL DEFAULT '0',
  `presence` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`currency_grouping_separatorid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_currency_grouping_separator`
--

LOCK TABLES `vtiger_currency_grouping_separator` WRITE;
/*!40000 ALTER TABLE `vtiger_currency_grouping_separator` DISABLE KEYS */;
INSERT INTO `vtiger_currency_grouping_separator` VALUES (1,'.',0,1),(2,',',1,1),(3,'\'',2,1),(4,' ',3,1),(5,'$',4,1);
/*!40000 ALTER TABLE `vtiger_currency_grouping_separator` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_currency_grouping_separator_seq`
--

DROP TABLE IF EXISTS `vtiger_currency_grouping_separator_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_currency_grouping_separator_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_currency_grouping_separator_seq`
--

LOCK TABLES `vtiger_currency_grouping_separator_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_currency_grouping_separator_seq` DISABLE KEYS */;
INSERT INTO `vtiger_currency_grouping_separator_seq` VALUES (5);
/*!40000 ALTER TABLE `vtiger_currency_grouping_separator_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_currency_info`
--

DROP TABLE IF EXISTS `vtiger_currency_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_currency_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_name` varchar(100) DEFAULT NULL,
  `currency_code` varchar(100) DEFAULT NULL,
  `currency_symbol` varchar(30) DEFAULT NULL,
  `conversion_rate` decimal(12,6) DEFAULT NULL,
  `currency_status` varchar(25) DEFAULT NULL,
  `defaultid` varchar(10) NOT NULL DEFAULT '0',
  `deleted` int(1) NOT NULL DEFAULT '0',
  `currency_position` char(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_currency_info`
--

LOCK TABLES `vtiger_currency_info` WRITE;
/*!40000 ALTER TABLE `vtiger_currency_info` DISABLE KEYS */;
INSERT INTO `vtiger_currency_info` VALUES (1,'USA, Dollars','USD','$',1.000000,'Active','-11',0,'$1.0');
/*!40000 ALTER TABLE `vtiger_currency_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_currency_info_seq`
--

DROP TABLE IF EXISTS `vtiger_currency_info_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_currency_info_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_currency_info_seq`
--

LOCK TABLES `vtiger_currency_info_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_currency_info_seq` DISABLE KEYS */;
INSERT INTO `vtiger_currency_info_seq` VALUES (1);
/*!40000 ALTER TABLE `vtiger_currency_info_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_currency_symbol_placement`
--

DROP TABLE IF EXISTS `vtiger_currency_symbol_placement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_currency_symbol_placement` (
  `currency_symbol_placementid` int(19) NOT NULL AUTO_INCREMENT,
  `currency_symbol_placement` varchar(30) NOT NULL,
  `sortorderid` int(19) NOT NULL DEFAULT '0',
  `presence` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`currency_symbol_placementid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_currency_symbol_placement`
--

LOCK TABLES `vtiger_currency_symbol_placement` WRITE;
/*!40000 ALTER TABLE `vtiger_currency_symbol_placement` DISABLE KEYS */;
INSERT INTO `vtiger_currency_symbol_placement` VALUES (1,'$1.0',0,1),(2,'1.0$',1,1);
/*!40000 ALTER TABLE `vtiger_currency_symbol_placement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_currency_symbol_placement_seq`
--

DROP TABLE IF EXISTS `vtiger_currency_symbol_placement_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_currency_symbol_placement_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_currency_symbol_placement_seq`
--

LOCK TABLES `vtiger_currency_symbol_placement_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_currency_symbol_placement_seq` DISABLE KEYS */;
INSERT INTO `vtiger_currency_symbol_placement_seq` VALUES (2);
/*!40000 ALTER TABLE `vtiger_currency_symbol_placement_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_customerdetails`
--

DROP TABLE IF EXISTS `vtiger_customerdetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_customerdetails` (
  `customerid` int(19) NOT NULL,
  `portal` varchar(3) DEFAULT NULL,
  `support_start_date` date DEFAULT NULL,
  `support_end_date` date DEFAULT NULL,
  PRIMARY KEY (`customerid`),
  CONSTRAINT `fk_1_vtiger_customerdetails` FOREIGN KEY (`customerid`) REFERENCES `vtiger_contactdetails` (`contactid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_customerdetails`
--

LOCK TABLES `vtiger_customerdetails` WRITE;
/*!40000 ALTER TABLE `vtiger_customerdetails` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_customerdetails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_customerportal_fields`
--

DROP TABLE IF EXISTS `vtiger_customerportal_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_customerportal_fields` (
  `tabid` int(19) NOT NULL,
  `fieldid` int(19) DEFAULT NULL,
  `visible` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_customerportal_fields`
--

LOCK TABLES `vtiger_customerportal_fields` WRITE;
/*!40000 ALTER TABLE `vtiger_customerportal_fields` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_customerportal_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_customerportal_prefs`
--

DROP TABLE IF EXISTS `vtiger_customerportal_prefs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_customerportal_prefs` (
  `tabid` int(19) NOT NULL,
  `prefkey` varchar(100) NOT NULL,
  `prefvalue` int(20) DEFAULT NULL,
  PRIMARY KEY (`tabid`,`prefkey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_customerportal_prefs`
--

LOCK TABLES `vtiger_customerportal_prefs` WRITE;
/*!40000 ALTER TABLE `vtiger_customerportal_prefs` DISABLE KEYS */;
INSERT INTO `vtiger_customerportal_prefs` VALUES (0,'defaultassignee',1),(0,'userid',1),(4,'showrelatedinfo',1),(6,'showrelatedinfo',1),(8,'showrelatedinfo',1),(13,'showrelatedinfo',1),(14,'showrelatedinfo',1),(15,'showrelatedinfo',1),(20,'showrelatedinfo',1),(23,'showrelatedinfo',1),(38,'showrelatedinfo',1),(43,'showrelatedinfo',1);
/*!40000 ALTER TABLE `vtiger_customerportal_prefs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_customerportal_tabs`
--

DROP TABLE IF EXISTS `vtiger_customerportal_tabs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_customerportal_tabs` (
  `tabid` int(19) NOT NULL,
  `visible` int(1) DEFAULT '1',
  `sequence` int(1) DEFAULT NULL,
  PRIMARY KEY (`tabid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_customerportal_tabs`
--

LOCK TABLES `vtiger_customerportal_tabs` WRITE;
/*!40000 ALTER TABLE `vtiger_customerportal_tabs` DISABLE KEYS */;
INSERT INTO `vtiger_customerportal_tabs` VALUES (4,1,9),(6,1,10),(8,1,8),(13,1,2),(14,1,6),(15,1,3),(20,1,5),(23,1,4),(38,1,7),(43,1,11);
/*!40000 ALTER TABLE `vtiger_customerportal_tabs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_customview`
--

DROP TABLE IF EXISTS `vtiger_customview`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_customview` (
  `cvid` int(19) NOT NULL,
  `viewname` varchar(100) NOT NULL,
  `setdefault` int(1) DEFAULT '0',
  `setmetrics` int(1) DEFAULT '0',
  `entitytype` varchar(100) DEFAULT NULL,
  `status` int(1) DEFAULT '1',
  `userid` int(19) DEFAULT '1',
  PRIMARY KEY (`cvid`),
  KEY `customview_entitytype_idx` (`entitytype`),
  KEY `userid_idx` (`userid`),
  KEY `setmetrics` (`setmetrics`),
  CONSTRAINT `fk_1_vtiger_customview` FOREIGN KEY (`entitytype`) REFERENCES `vtiger_tab` (`name`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_customview`
--

LOCK TABLES `vtiger_customview` WRITE;
/*!40000 ALTER TABLE `vtiger_customview` DISABLE KEYS */;
INSERT INTO `vtiger_customview` VALUES (1,'All',1,0,'Leads',0,1),(2,'Hot Leads',0,1,'Leads',3,1),(3,'This Month Leads',0,0,'Leads',3,1),(4,'All',1,0,'Accounts',0,1),(5,'Prospect Accounts',0,1,'Accounts',3,1),(6,'New This Week',0,0,'Accounts',3,1),(7,'All',1,0,'Contacts',0,1),(8,'Contacts Address',0,0,'Contacts',3,1),(9,'Todays Birthday',0,0,'Contacts',3,1),(10,'All',1,0,'Potentials',0,1),(11,'Potentials Won',0,1,'Potentials',3,1),(12,'Prospecting',0,0,'Potentials',3,1),(13,'All',1,0,'HelpDesk',0,1),(14,'Open Tickets',0,1,'HelpDesk',3,1),(15,'High Prioriy Tickets',0,0,'HelpDesk',3,1),(16,'All',1,0,'Quotes',0,1),(17,'Open Quotes',0,1,'Quotes',3,1),(18,'Rejected Quotes',0,0,'Quotes',3,1),(19,'All',1,0,'Calendar',0,1),(20,'All',1,0,'Emails',0,1),(21,'All',1,0,'Invoice',0,1),(22,'All',1,0,'Documents',0,1),(23,'All',1,0,'PriceBooks',0,1),(24,'All',1,0,'Products',0,1),(25,'All',1,0,'PurchaseOrder',0,1),(26,'All',1,0,'SalesOrder',0,1),(27,'All',1,0,'Vendors',0,1),(28,'All',1,0,'Faq',0,1),(29,'All',1,0,'Campaigns',0,1),(30,'All',1,0,'Webmails',0,1),(31,'Drafted FAQ',0,0,'Faq',3,1),(32,'Published FAQ',0,0,'Faq',3,1),(33,'Open Purchase Orders',0,0,'PurchaseOrder',3,1),(34,'Received Purchase Orders',0,0,'PurchaseOrder',3,1),(35,'Open Invoices',0,0,'Invoice',3,1),(36,'Paid Invoices',0,0,'Invoice',3,1),(37,'Pending Sales Orders',0,0,'SalesOrder',3,1),(38,'All',1,0,'PBXManager',0,1),(39,'Missed',0,0,'PBXManager',3,1),(40,'Dialed',0,0,'PBXManager',3,1),(41,'Received',0,0,'PBXManager',3,1),(42,'All',1,0,'ServiceContracts',0,1),(43,'All',1,0,'Services',0,1),(44,'All',0,0,'cbupdater',0,1),(45,'Applied',0,0,'cbupdater',3,1),(46,'Pending',0,0,'cbupdater',3,1),(47,'Error',0,0,'cbupdater',3,1),(48,'Continuous',0,0,'cbupdater',3,1),(49,'blocked',0,0,'cbupdater',3,1),(50,'perspective',0,0,'cbupdater',3,1),(51,'All',0,0,'CobroPago',0,1),(52,'payview',1,1,'CobroPago',3,1),(53,'All',1,0,'Assets',0,1),(54,'All',0,0,'ModComments',0,1),(55,'All',1,0,'ProjectMilestone',0,1),(56,'All',1,0,'ProjectTask',0,1),(57,'All',1,0,'Project',0,1),(58,'All',0,0,'SMSNotifier',0,1),(59,'All',1,0,'GlobalVariable',0,1),(60,'All',1,0,'InventoryDetails',0,1),(61,'All',1,0,'cbMap',0,1),(62,'All',0,0,'cbTermConditions',0,1),(63,'All',1,0,'cbCalendar',0,1),(64,'All',1,0,'cbtranslation',0,1);
/*!40000 ALTER TABLE `vtiger_customview` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_customview_seq`
--

DROP TABLE IF EXISTS `vtiger_customview_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_customview_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_customview_seq`
--

LOCK TABLES `vtiger_customview_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_customview_seq` DISABLE KEYS */;
INSERT INTO `vtiger_customview_seq` VALUES (64);
/*!40000 ALTER TABLE `vtiger_customview_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_cvadvfilter`
--

DROP TABLE IF EXISTS `vtiger_cvadvfilter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_cvadvfilter` (
  `cvid` int(19) NOT NULL,
  `columnindex` int(11) NOT NULL,
  `columnname` varchar(250) DEFAULT '',
  `comparator` varchar(20) DEFAULT NULL,
  `value` varchar(512) DEFAULT NULL,
  `groupid` int(11) DEFAULT '1',
  `column_condition` varchar(255) DEFAULT 'and',
  PRIMARY KEY (`cvid`,`columnindex`),
  KEY `cvadvfilter_cvid_idx` (`cvid`),
  CONSTRAINT `fk_1_vtiger_cvadvfilter` FOREIGN KEY (`cvid`) REFERENCES `vtiger_customview` (`cvid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_cvadvfilter`
--

LOCK TABLES `vtiger_cvadvfilter` WRITE;
/*!40000 ALTER TABLE `vtiger_cvadvfilter` DISABLE KEYS */;
INSERT INTO `vtiger_cvadvfilter` VALUES (2,0,'vtiger_leaddetails:leadstatus:leadstatus:Leads_Lead_Status:V','e','Hot',1,'and'),(5,0,'vtiger_account:account_type:accounttype:Accounts_Type:V','e','Prospect',1,'and'),(11,0,'vtiger_potential:sales_stage:sales_stage:Potentials_Sales_Stage:V','e','Closed Won',1,'and'),(12,0,'vtiger_potential:sales_stage:sales_stage:Potentials_Sales_Stage:V','e','Prospecting',1,'and'),(14,0,'vtiger_troubletickets:status:ticketstatus:HelpDesk_Status:V','n','Closed',1,'and'),(15,0,'vtiger_troubletickets:priority:ticketpriorities:HelpDesk_Priority:V','e','High',1,'and'),(17,0,'vtiger_quotes:quotestage:quotestage:Quotes_Quote_Stage:V','n','Accepted',1,'and'),(17,1,'vtiger_quotes:quotestage:quotestage:Quotes_Quote_Stage:V','n','Rejected',1,'and'),(18,0,'vtiger_quotes:quotestage:quotestage:Quotes_Quote_Stage:V','e','Rejected',1,'and'),(31,0,'vtiger_faq:status:faqstatus:Faq_Status:V','e','Draft',1,'and'),(32,0,'vtiger_faq:status:faqstatus:Faq_Status:V','e','Published',1,'and'),(33,0,'vtiger_purchaseorder:postatus:postatus:PurchaseOrder_Status:V','e','Created, Approved, Delivered',1,'and'),(34,0,'vtiger_purchaseorder:postatus:postatus:PurchaseOrder_Status:V','e','Received Shipment',1,'and'),(35,0,'vtiger_invoice:invoicestatus:invoicestatus:Invoice_Status:V','e','Created, Approved, Sent',1,'and'),(36,0,'vtiger_invoice:invoicestatus:invoicestatus:Invoice_Status:V','e','Paid',1,'and'),(37,0,'vtiger_salesorder:sostatus:sostatus:SalesOrder_Status:V','e','Created, Approved',1,'and'),(45,0,'vtiger_cbupdater:execstate:execstate:cbupdater_execstate:V','e','Executed',1,'and'),(46,0,'vtiger_cbupdater:execstate:execstate:cbupdater_execstate:V','e','Pending',1,''),(47,0,'vtiger_cbupdater:execstate:execstate:cbupdater_execstate:V','e','Error',1,'and'),(48,0,'vtiger_cbupdater:execstate:execstate:cbupdater_execstate:V','e','Continuous',1,'and'),(49,0,'vtiger_cbupdater:blocked:blocked:cbupdater_blocked:C','e','1',1,'and'),(50,0,'vtiger_cbupdater:perspective:perspective:cbupdater_perspective:C','e','1',1,'and');
/*!40000 ALTER TABLE `vtiger_cvadvfilter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_cvadvfilter_grouping`
--

DROP TABLE IF EXISTS `vtiger_cvadvfilter_grouping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_cvadvfilter_grouping` (
  `groupid` int(11) NOT NULL,
  `cvid` int(19) NOT NULL,
  `group_condition` varchar(255) DEFAULT NULL,
  `condition_expression` text,
  PRIMARY KEY (`groupid`,`cvid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_cvadvfilter_grouping`
--

LOCK TABLES `vtiger_cvadvfilter_grouping` WRITE;
/*!40000 ALTER TABLE `vtiger_cvadvfilter_grouping` DISABLE KEYS */;
INSERT INTO `vtiger_cvadvfilter_grouping` VALUES (1,2,'',''),(1,5,'',''),(1,11,'',''),(1,12,'',''),(1,14,'',''),(1,15,'',''),(1,17,'',''),(1,18,'',''),(1,31,'',''),(1,32,'',''),(1,33,'',''),(1,34,'',''),(1,35,'',''),(1,36,'',''),(1,37,'',''),(1,45,'and',''),(1,46,'',''),(1,47,'and',''),(1,48,'and',''),(1,49,'and',''),(1,50,'and','');
/*!40000 ALTER TABLE `vtiger_cvadvfilter_grouping` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_cvcolumnlist`
--

DROP TABLE IF EXISTS `vtiger_cvcolumnlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_cvcolumnlist` (
  `cvid` int(19) NOT NULL,
  `columnindex` int(11) NOT NULL,
  `columnname` varchar(250) DEFAULT '',
  PRIMARY KEY (`cvid`,`columnindex`),
  KEY `cvcolumnlist_columnindex_idx` (`columnindex`),
  KEY `cvcolumnlist_cvid_idx` (`cvid`),
  CONSTRAINT `fk_1_vtiger_cvcolumnlist` FOREIGN KEY (`cvid`) REFERENCES `vtiger_customview` (`cvid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_cvcolumnlist`
--

LOCK TABLES `vtiger_cvcolumnlist` WRITE;
/*!40000 ALTER TABLE `vtiger_cvcolumnlist` DISABLE KEYS */;
INSERT INTO `vtiger_cvcolumnlist` VALUES (1,0,'vtiger_leaddetails:lead_no:lead_no:Leads_Lead_No:V'),(1,1,'vtiger_leaddetails:lastname:lastname:Leads_Last_Name:V'),(1,2,'vtiger_leaddetails:firstname:firstname:Leads_First_Name:V'),(1,3,'vtiger_leaddetails:company:company:Leads_Company:V'),(1,4,'vtiger_leadaddress:phone:phone:Leads_Phone:V'),(1,5,'vtiger_leadsubdetails:website:website:Leads_Website:V'),(1,6,'vtiger_leaddetails:email:email:Leads_Email:E'),(1,7,'vtiger_crmentity:smownerid:assigned_user_id:Leads_Assigned_To:V'),(2,0,'vtiger_leaddetails:firstname:firstname:Leads_First_Name:V'),(2,1,'vtiger_leaddetails:lastname:lastname:Leads_Last_Name:V'),(2,2,'vtiger_leaddetails:company:company:Leads_Company:V'),(2,3,'vtiger_leaddetails:leadsource:leadsource:Leads_Lead_Source:V'),(2,4,'vtiger_leadsubdetails:website:website:Leads_Website:V'),(2,5,'vtiger_leaddetails:email:email:Leads_Email:E'),(3,0,'vtiger_leaddetails:firstname:firstname:Leads_First_Name:V'),(3,1,'vtiger_leaddetails:lastname:lastname:Leads_Last_Name:V'),(3,2,'vtiger_leaddetails:company:company:Leads_Company:V'),(3,3,'vtiger_leaddetails:leadsource:leadsource:Leads_Lead_Source:V'),(3,4,'vtiger_leadsubdetails:website:website:Leads_Website:V'),(3,5,'vtiger_leaddetails:email:email:Leads_Email:E'),(4,0,'vtiger_account:account_no:account_no:Accounts_Account_No:V'),(4,1,'vtiger_account:accountname:accountname:Accounts_Account_Name:V'),(4,2,'vtiger_accountbillads:bill_city:bill_city:Accounts_City:V'),(4,3,'vtiger_account:website:website:Accounts_Website:V'),(4,4,'vtiger_account:phone:phone:Accounts_Phone:V'),(4,5,'vtiger_crmentity:smownerid:assigned_user_id:Accounts_Assigned_To:V'),(5,0,'vtiger_account:accountname:accountname:Accounts_Account_Name:V'),(5,1,'vtiger_account:phone:phone:Accounts_Phone:V'),(5,2,'vtiger_account:website:website:Accounts_Website:V'),(5,3,'vtiger_account:rating:rating:Accounts_Rating:V'),(5,4,'vtiger_crmentity:smownerid:assigned_user_id:Accounts_Assigned_To:V'),(6,0,'vtiger_account:accountname:accountname:Accounts_Account_Name:V'),(6,1,'vtiger_account:phone:phone:Accounts_Phone:V'),(6,2,'vtiger_account:website:website:Accounts_Website:V'),(6,3,'vtiger_accountbillads:bill_city:bill_city:Accounts_City:V'),(6,4,'vtiger_crmentity:smownerid:assigned_user_id:Accounts_Assigned_To:V'),(7,0,'vtiger_contactdetails:contact_no:contact_no:Contacts_Contact_Id:V'),(7,1,'vtiger_contactdetails:firstname:firstname:Contacts_First_Name:V'),(7,2,'vtiger_contactdetails:lastname:lastname:Contacts_Last_Name:V'),(7,3,'vtiger_contactdetails:title:title:Contacts_Title:V'),(7,4,'vtiger_contactdetails:accountid:account_id:Contacts_Account_Name:I'),(7,5,'vtiger_contactdetails:email:email:Contacts_Email:E'),(7,6,'vtiger_contactdetails:phone:phone:Contacts_Office_Phone:V'),(7,7,'vtiger_crmentity:smownerid:assigned_user_id:Contacts_Assigned_To:V'),(8,0,'vtiger_contactdetails:firstname:firstname:Contacts_First_Name:V'),(8,1,'vtiger_contactdetails:lastname:lastname:Contacts_Last_Name:V'),(8,2,'vtiger_contactaddress:mailingstreet:mailingstreet:Contacts_Mailing_Street:V'),(8,3,'vtiger_contactaddress:mailingcity:mailingcity:Contacts_Mailing_City:V'),(8,4,'vtiger_contactaddress:mailingstate:mailingstate:Contacts_Mailing_State:V'),(8,5,'vtiger_contactaddress:mailingzip:mailingzip:Contacts_Mailing_Zip:V'),(8,6,'vtiger_contactaddress:mailingcountry:mailingcountry:Contacts_Mailing_Country:V'),(9,0,'vtiger_contactdetails:firstname:firstname:Contacts_First_Name:V'),(9,1,'vtiger_contactdetails:lastname:lastname:Contacts_Last_Name:V'),(9,2,'vtiger_contactdetails:title:title:Contacts_Title:V'),(9,3,'vtiger_contactdetails:accountid:account_id:Contacts_Account_Name:I'),(9,4,'vtiger_contactdetails:email:email:Contacts_Email:E'),(9,5,'vtiger_contactsubdetails:otherphone:otherphone:Contacts_Phone:V'),(9,6,'vtiger_crmentity:smownerid:assigned_user_id:Contacts_Assigned_To:V'),(10,0,'vtiger_potential:potential_no:potential_no:Potentials_Potential_No:V'),(10,1,'vtiger_potential:potentialname:potentialname:Potentials_Potential_Name:V'),(10,2,'vtiger_potential:related_to:related_to:Potentials_Related_To:V'),(10,3,'vtiger_potential:sales_stage:sales_stage:Potentials_Sales_Stage:V'),(10,4,'vtiger_potential:leadsource:leadsource:Potentials_Lead_Source:V'),(10,5,'vtiger_potential:closingdate:closingdate:Potentials_Expected_Close_Date:D'),(10,6,'vtiger_crmentity:smownerid:assigned_user_id:Potentials_Assigned_To:V'),(11,0,'vtiger_potential:potentialname:potentialname:Potentials_Potential_Name:V'),(11,1,'vtiger_potential:related_to:related_to:Potentials_Related_To:V'),(11,2,'vtiger_potential:amount:amount:Potentials_Amount:N'),(11,3,'vtiger_potential:closingdate:closingdate:Potentials_Expected_Close_Date:D'),(11,4,'vtiger_crmentity:smownerid:assigned_user_id:Potentials_Assigned_To:V'),(12,0,'vtiger_potential:potentialname:potentialname:Potentials_Potential_Name:V'),(12,1,'vtiger_potential:related_to:related_to:Potentials_Related_To:V'),(12,2,'vtiger_potential:amount:amount:Potentials_Amount:N'),(12,3,'vtiger_potential:leadsource:leadsource:Potentials_Lead_Source:V'),(12,4,'vtiger_potential:closingdate:closingdate:Potentials_Expected_Close_Date:D'),(12,5,'vtiger_crmentity:smownerid:assigned_user_id:Potentials_Assigned_To:V'),(13,0,'vtiger_troubletickets:ticket_no:ticket_no:HelpDesk_Ticket_No:V'),(13,1,'vtiger_troubletickets:title:ticket_title:HelpDesk_Title:V'),(13,2,'vtiger_troubletickets:parent_id:parent_id:HelpDesk_Related_To:I'),(13,3,'vtiger_troubletickets:status:ticketstatus:HelpDesk_Status:V'),(13,4,'vtiger_troubletickets:priority:ticketpriorities:HelpDesk_Priority:V'),(13,5,'vtiger_crmentity:smownerid:assigned_user_id:HelpDesk_Assigned_To:V'),(14,0,'vtiger_troubletickets:title:ticket_title:HelpDesk_Title:V'),(14,1,'vtiger_troubletickets:parent_id:parent_id:HelpDesk_Related_To:I'),(14,2,'vtiger_troubletickets:priority:ticketpriorities:HelpDesk_Priority:V'),(14,3,'vtiger_troubletickets:product_id:product_id:HelpDesk_Product_Name:I'),(14,4,'vtiger_crmentity:smownerid:assigned_user_id:HelpDesk_Assigned_To:V'),(15,0,'vtiger_troubletickets:title:ticket_title:HelpDesk_Title:V'),(15,1,'vtiger_troubletickets:parent_id:parent_id:HelpDesk_Related_To:I'),(15,2,'vtiger_troubletickets:status:ticketstatus:HelpDesk_Status:V'),(15,3,'vtiger_troubletickets:product_id:product_id:HelpDesk_Product_Name:I'),(15,4,'vtiger_crmentity:smownerid:assigned_user_id:HelpDesk_Assigned_To:V'),(16,0,'vtiger_quotes:quote_no:quote_no:Quotes_Quote_No:V'),(16,1,'vtiger_quotes:subject:subject:Quotes_Subject:V'),(16,2,'vtiger_quotes:quotestage:quotestage:Quotes_Quote_Stage:V'),(16,3,'vtiger_quotes:potentialid:potential_id:Quotes_Potential_Name:I'),(16,4,'vtiger_quotes:accountid:account_id:Quotes_Account_Name:I'),(16,5,'vtiger_quotes:total:hdnGrandTotal:Quotes_Total:I'),(16,6,'vtiger_crmentity:smownerid:assigned_user_id:Quotes_Assigned_To:V'),(17,0,'vtiger_quotes:subject:subject:Quotes_Subject:V'),(17,1,'vtiger_quotes:quotestage:quotestage:Quotes_Quote_Stage:V'),(17,2,'vtiger_quotes:potentialid:potential_id:Quotes_Potential_Name:I'),(17,3,'vtiger_quotes:accountid:account_id:Quotes_Account_Name:I'),(17,4,'vtiger_quotes:validtill:validtill:Quotes_Valid_Till:D'),(17,5,'vtiger_crmentity:smownerid:assigned_user_id:Quotes_Assigned_To:V'),(18,0,'vtiger_quotes:subject:subject:Quotes_Subject:V'),(18,1,'vtiger_quotes:potentialid:potential_id:Quotes_Potential_Name:I'),(18,2,'vtiger_quotes:accountid:account_id:Quotes_Account_Name:I'),(18,3,'vtiger_quotes:validtill:validtill:Quotes_Valid_Till:D'),(18,4,'vtiger_crmentity:smownerid:assigned_user_id:Quotes_Assigned_To:V'),(19,0,'vtiger_activity:status:taskstatus:cbCalendar_Status:V'),(19,1,'vtiger_activity:activitytype:activitytype:cbCalendar_Type:V'),(19,2,'vtiger_activity:subject:subject:cbCalendar_Subject:V'),(19,3,'vtiger_seactivityrel:crmid:parent_id:cbCalendar_Related_to:V'),(19,4,'vtiger_activity:date_start:date_start:cbCalendar_Start_Date:D'),(19,5,'vtiger_activity:due_date:due_date:cbCalendar_End_Date:D'),(19,6,'vtiger_crmentity:smownerid:assigned_user_id:cbCalendar_Assigned_To:V'),(20,0,'vtiger_activity:subject:subject:Emails_Subject:V'),(20,1,'vtiger_emaildetails:to_email:saved_toid:Emails_To:V'),(20,2,'vtiger_activity:date_start:date_start:Emails_Date_Sent:D'),(21,0,'vtiger_invoice:invoice_no:invoice_no:Invoice_Invoice_No:V'),(21,1,'vtiger_invoice:subject:subject:Invoice_Subject:V'),(21,2,'vtiger_invoice:salesorderid:salesorder_id:Invoice_Sales_Order:I'),(21,3,'vtiger_invoice:invoicestatus:invoicestatus:Invoice_Status:V'),(21,4,'vtiger_invoice:total:hdnGrandTotal:Invoice_Total:I'),(21,5,'vtiger_crmentity:smownerid:assigned_user_id:Invoice_Assigned_To:V'),(22,0,'vtiger_notes:note_no:note_no:Notes_Note_No:V'),(22,1,'vtiger_notes:title:notes_title:Notes_Title:V'),(22,2,'vtiger_notes:filename:filename:Notes_File:V'),(22,3,'vtiger_crmentity:modifiedtime:modifiedtime:Notes_Modified_Time:DT'),(22,4,'vtiger_crmentity:smownerid:assigned_user_id:Notes_Assigned_To:V'),(23,0,'vtiger_pricebook:pricebook_no:pricebook_no:PriceBooks_PriceBook_No:V'),(23,1,'vtiger_pricebook:bookname:bookname:PriceBooks_Price_Book_Name:V'),(23,2,'vtiger_pricebook:active:active:PriceBooks_Active:V'),(23,3,'vtiger_pricebook:currency_id:currency_id:PriceBooks_Currency:I'),(24,0,'vtiger_products:product_no:product_no:Products_Product_No:V'),(24,1,'vtiger_products:productname:productname:Products_Product_Name:V'),(24,2,'vtiger_products:productcode:productcode:Products_Part_Number:V'),(24,3,'vtiger_products:commissionrate:commissionrate:Products_Commission_Rate:V'),(24,4,'vtiger_products:qtyinstock:qtyinstock:Products_Quantity_In_Stock:V'),(24,5,'vtiger_products:qty_per_unit:qty_per_unit:Products_Qty/Unit:V'),(24,6,'vtiger_products:unit_price:unit_price:Products_Unit_Price:V'),(25,0,'vtiger_purchaseorder:purchaseorder_no:purchaseorder_no:PurchaseOrder_PurchaseOrder_No:V'),(25,1,'vtiger_purchaseorder:subject:subject:PurchaseOrder_Subject:V'),(25,2,'vtiger_purchaseorder:vendorid:vendor_id:PurchaseOrder_Vendor_Name:I'),(25,3,'vtiger_purchaseorder:tracking_no:tracking_no:PurchaseOrder_Tracking_Number:V'),(25,4,'vtiger_purchaseorder:total:hdnGrandTotal:PurchaseOrder_Total:V'),(25,5,'vtiger_crmentity:smownerid:assigned_user_id:PurchaseOrder_Assigned_To:V'),(26,0,'vtiger_salesorder:salesorder_no:salesorder_no:SalesOrder_SalesOrder_No:V'),(26,1,'vtiger_salesorder:subject:subject:SalesOrder_Subject:V'),(26,2,'vtiger_salesorder:accountid:account_id:SalesOrder_Account_Name:I'),(26,3,'vtiger_salesorder:quoteid:quote_id:SalesOrder_Quote_Name:I'),(26,4,'vtiger_salesorder:total:hdnGrandTotal:SalesOrder_Total:V'),(26,5,'vtiger_crmentity:smownerid:assigned_user_id:SalesOrder_Assigned_To:V'),(27,0,'vtiger_vendor:vendor_no:vendor_no:Vendors_Vendor_No:V'),(27,1,'vtiger_vendor:vendorname:vendorname:Vendors_Vendor_Name:V'),(27,2,'vtiger_vendor:phone:phone:Vendors_Phone:V'),(27,3,'vtiger_vendor:email:email:Vendors_Email:E'),(27,4,'vtiger_vendor:category:category:Vendors_Category:V'),(28,0,'vtiger_faq:faq_no:faq_no:Faq_Faq_No:V'),(28,1,'vtiger_faq:question:question:Faq_Question:V'),(28,2,'vtiger_faq:category:faqcategories:Faq_Category:V'),(28,3,'vtiger_faq:product_id:product_id:Faq_Product_Name:I'),(28,4,'vtiger_crmentity:createdtime:createdtime:Faq_Created_Time:DT'),(28,5,'vtiger_crmentity:modifiedtime:modifiedtime:Faq_Modified_Time:DT'),(29,0,'vtiger_campaign:campaign_no:campaign_no:Campaigns_Campaign_No:V'),(29,1,'vtiger_campaign:campaignname:campaignname:Campaigns_Campaign_Name:V'),(29,2,'vtiger_campaign:campaigntype:campaigntype:Campaigns_Campaign_Type:N'),(29,3,'vtiger_campaign:campaignstatus:campaignstatus:Campaigns_Campaign_Status:N'),(29,4,'vtiger_campaign:expectedrevenue:expectedrevenue:Campaigns_Expected_Revenue:V'),(29,5,'vtiger_campaign:closingdate:closingdate:Campaigns_Expected_Close_Date:D'),(29,6,'vtiger_crmentity:smownerid:assigned_user_id:Campaigns_Assigned_To:V'),(30,0,'subject:subject:subject:Subject:V'),(30,1,'from:fromname:fromname:From:N'),(30,2,'to:tpname:toname:To:N'),(30,3,'body:body:body:Body:V'),(31,0,'vtiger_faq:question:question:Faq_Question:V'),(31,1,'vtiger_faq:status:faqstatus:Faq_Status:V'),(31,2,'vtiger_faq:product_id:product_id:Faq_Product_Name:I'),(31,3,'vtiger_faq:category:faqcategories:Faq_Category:V'),(31,4,'vtiger_crmentity:createdtime:createdtime:Faq_Created_Time:DT'),(32,0,'vtiger_faq:question:question:Faq_Question:V'),(32,1,'vtiger_faq:answer:faq_answer:Faq_Answer:V'),(32,2,'vtiger_faq:status:faqstatus:Faq_Status:V'),(32,3,'vtiger_faq:product_id:product_id:Faq_Product_Name:I'),(32,4,'vtiger_faq:category:faqcategories:Faq_Category:V'),(32,5,'vtiger_crmentity:createdtime:createdtime:Faq_Created_Time:DT'),(33,0,'vtiger_purchaseorder:subject:subject:PurchaseOrder_Subject:V'),(33,1,'vtiger_purchaseorder:postatus:postatus:PurchaseOrder_Status:V'),(33,2,'vtiger_purchaseorder:vendorid:vendor_id:PurchaseOrder_Vendor_Name:I'),(33,3,'vtiger_crmentity:smownerid:assigned_user_id:PurchaseOrder_Assigned_To:V'),(33,4,'vtiger_purchaseorder:duedate:duedate:PurchaseOrder_Due_Date:V'),(34,0,'vtiger_purchaseorder:subject:subject:PurchaseOrder_Subject:V'),(34,1,'vtiger_purchaseorder:vendorid:vendor_id:PurchaseOrder_Vendor_Name:I'),(34,2,'vtiger_crmentity:smownerid:assigned_user_id:PurchaseOrder_Assigned_To:V'),(34,3,'vtiger_purchaseorder:postatus:postatus:PurchaseOrder_Status:V'),(34,4,'vtiger_purchaseorder:carrier:carrier:PurchaseOrder_Carrier:V'),(34,5,'vtiger_poshipads:ship_street:ship_street:PurchaseOrder_Shipping_Address:V'),(35,0,'vtiger_invoice:invoice_no:invoice_no:Invoice_Invoice_No:V'),(35,1,'vtiger_invoice:subject:subject:Invoice_Subject:V'),(35,2,'vtiger_invoice:accountid:account_id:Invoice_Account_Name:I'),(35,3,'vtiger_invoice:salesorderid:salesorder_id:Invoice_Sales_Order:I'),(35,4,'vtiger_invoice:invoicestatus:invoicestatus:Invoice_Status:V'),(35,5,'vtiger_crmentity:smownerid:assigned_user_id:Invoice_Assigned_To:V'),(35,6,'vtiger_crmentity:createdtime:createdtime:Invoice_Created_Time:DT'),(36,0,'vtiger_invoice:invoice_no:invoice_no:Invoice_Invoice_No:V'),(36,1,'vtiger_invoice:subject:subject:Invoice_Subject:V'),(36,2,'vtiger_invoice:accountid:account_id:Invoice_Account_Name:I'),(36,3,'vtiger_invoice:salesorderid:salesorder_id:Invoice_Sales_Order:I'),(36,4,'vtiger_invoice:invoicestatus:invoicestatus:Invoice_Status:V'),(36,5,'vtiger_invoiceshipads:ship_street:ship_street:Invoice_Shipping_Address:V'),(36,6,'vtiger_crmentity:smownerid:assigned_user_id:Invoice_Assigned_To:V'),(37,0,'vtiger_salesorder:subject:subject:SalesOrder_Subject:V'),(37,1,'vtiger_salesorder:accountid:account_id:SalesOrder_Account_Name:I'),(37,2,'vtiger_salesorder:sostatus:sostatus:SalesOrder_Status:V'),(37,3,'vtiger_crmentity:smownerid:assigned_user_id:SalesOrder_Assigned_To:V'),(37,4,'vtiger_soshipads:ship_street:ship_street:SalesOrder_Shipping_Address:V'),(37,5,'vtiger_salesorder:carrier:carrier:SalesOrder_Carrier:V'),(38,0,'vtiger_pbxmanager:callfrom:callfrom:PBXManager_Call_From:V'),(38,1,'vtiger_pbxmanager:callto:callto:PBXManager_Call_To:V'),(38,2,'vtiger_pbxmanager:timeofcall:timeofcall:PBXManager_Time_Of_Call:V'),(38,3,'vtiger_pbxmanager:status:status:PBXManager_Status:V'),(39,0,'vtiger_pbxmanager:callfrom:callfrom:PBXManager_Call_From:V'),(39,1,'vtiger_pbxmanager:callto:callto:PBXManager_Call_To:V'),(39,2,'vtiger_pbxmanager:timeofcall:timeofcall:PBXManager_Time_Of_Call:V'),(39,3,'vtiger_pbxmanager:status:status:PBXManager_Status:V'),(40,0,'vtiger_pbxmanager:callfrom:callfrom:PBXManager_Call_From:V'),(40,1,'vtiger_pbxmanager:callto:callto:PBXManager_Call_To:V'),(40,2,'vtiger_pbxmanager:timeofcall:timeofcall:PBXManager_Time_Of_Call:V'),(40,3,'vtiger_pbxmanager:status:status:PBXManager_Status:V'),(41,0,'vtiger_pbxmanager:callfrom:callfrom:PBXManager_Call_From:V'),(41,1,'vtiger_pbxmanager:callto:callto:PBXManager_Call_To:V'),(41,2,'vtiger_pbxmanager:timeofcall:timeofcall:PBXManager_Time_Of_Call:V'),(41,3,'vtiger_pbxmanager:status:status:PBXManager_Status:V'),(42,0,'vtiger_servicecontracts:contract_no:contract_no:ServiceContracts_Contract_No:V'),(42,1,'vtiger_servicecontracts:subject:subject:ServiceContracts_Subject:V'),(42,2,'vtiger_servicecontracts:sc_related_to:sc_related_to:ServiceContracts_Related_to:V'),(42,3,'vtiger_crmentity:smownerid:assigned_user_id:ServiceContracts_Assigned_To:V'),(42,4,'vtiger_servicecontracts:start_date:start_date:ServiceContracts_Start_Date:D'),(42,5,'vtiger_servicecontracts:due_date:due_date:ServiceContracts_Due_date:D'),(42,7,'vtiger_servicecontracts:progress:progress:ServiceContracts_Progress:N'),(42,8,'vtiger_servicecontracts:contract_status:contract_status:ServiceContracts_Status:V'),(43,0,'vtiger_service:service_no:service_no:Services_Service_No:V'),(43,1,'vtiger_service:servicename:servicename:Services_Service_Name:V'),(43,2,'vtiger_service:service_usageunit:service_usageunit:Services_Usage_Unit:V'),(43,3,'vtiger_service:unit_price:unit_price:Services_Price:N'),(43,4,'vtiger_service:qty_per_unit:qty_per_unit:Services_No_of_Units:N'),(43,5,'vtiger_service:servicecategory:servicecategory:Services_Service_Category:V'),(43,6,'vtiger_crmentity:smownerid:assigned_user_id:Services_Owner:I'),(44,0,'vtiger_cbupdater:cbupd_no:cbupd_no:cbupdater_cbupd_no:V'),(44,1,'vtiger_cbupdater:execdate:execdate:cbupdater_execdate:D'),(44,2,'vtiger_cbupdater:author:author:cbupdater_author:V'),(44,3,'vtiger_cbupdater:filename:filename:cbupdater_filename:V'),(44,4,'vtiger_cbupdater:execstate:execstate:cbupdater_execstate:V'),(44,5,'vtiger_cbupdater:systemupdate:systemupdate:cbupdater_systemupdate:C'),(44,6,'vtiger_crmentity:smownerid:assigned_user_id:cbupdater_Assigned_To:V'),(45,0,'vtiger_cbupdater:cbupd_no:cbupd_no:cbupdater_cbupd_no:V'),(45,1,'vtiger_cbupdater:execdate:execdate:cbupdater_execdate:D'),(45,2,'vtiger_cbupdater:author:author:cbupdater_author:V'),(45,3,'vtiger_cbupdater:filename:filename:cbupdater_filename:V'),(45,4,'vtiger_cbupdater:execstate:execstate:cbupdater_execstate:V'),(45,5,'vtiger_cbupdater:systemupdate:systemupdate:cbupdater_systemupdate:C'),(45,6,'vtiger_crmentity:smownerid:assigned_user_id:cbupdater_Assigned_To:V'),(46,0,'vtiger_cbupdater:cbupd_no:cbupd_no:cbupdater_cbupd_no:V'),(46,1,'vtiger_cbupdater:execdate:execdate:cbupdater_execdate:D'),(46,2,'vtiger_cbupdater:author:author:cbupdater_author:V'),(46,3,'vtiger_cbupdater:filename:filename:cbupdater_filename:V'),(46,4,'vtiger_cbupdater:execstate:execstate:cbupdater_execstate:V'),(46,5,'vtiger_cbupdater:systemupdate:systemupdate:cbupdater_systemupdate:C'),(46,6,'vtiger_crmentity:smownerid:assigned_user_id:cbupdater_Assigned_To:V'),(47,0,'vtiger_cbupdater:cbupd_no:cbupd_no:cbupdater_cbupd_no:V'),(47,1,'vtiger_cbupdater:execdate:execdate:cbupdater_execdate:D'),(47,2,'vtiger_cbupdater:author:author:cbupdater_author:V'),(47,3,'vtiger_cbupdater:filename:filename:cbupdater_filename:V'),(47,4,'vtiger_cbupdater:execstate:execstate:cbupdater_execstate:V'),(47,5,'vtiger_cbupdater:systemupdate:systemupdate:cbupdater_systemupdate:C'),(47,6,'vtiger_crmentity:smownerid:assigned_user_id:cbupdater_Assigned_To:V'),(48,0,'vtiger_cbupdater:cbupd_no:cbupd_no:cbupdater_cbupd_no:V'),(48,1,'vtiger_cbupdater:execdate:execdate:cbupdater_execdate:D'),(48,2,'vtiger_cbupdater:author:author:cbupdater_author:V'),(48,3,'vtiger_cbupdater:filename:filename:cbupdater_filename:V'),(48,4,'vtiger_cbupdater:execstate:execstate:cbupdater_execstate:V'),(48,5,'vtiger_cbupdater:systemupdate:systemupdate:cbupdater_systemupdate:C'),(48,6,'vtiger_crmentity:smownerid:assigned_user_id:cbupdater_Assigned_To:V'),(49,0,'vtiger_cbupdater:cbupd_no:cbupd_no:cbupdater_cbupd_no:V'),(49,1,'vtiger_cbupdater:execdate:execdate:cbupdater_execdate:D'),(49,2,'vtiger_cbupdater:author:author:cbupdater_author:V'),(49,3,'vtiger_cbupdater:filename:filename:cbupdater_filename:V'),(49,4,'vtiger_cbupdater:execstate:execstate:cbupdater_execstate:V'),(49,5,'vtiger_cbupdater:systemupdate:systemupdate:cbupdater_systemupdate:C'),(49,6,'vtiger_crmentity:smownerid:assigned_user_id:cbupdater_Assigned_To:V'),(50,0,'vtiger_cbupdater:cbupd_no:cbupd_no:cbupdater_cbupd_no:V'),(50,1,'vtiger_cbupdater:execdate:execdate:cbupdater_execdate:D'),(50,2,'vtiger_cbupdater:author:author:cbupdater_author:V'),(50,3,'vtiger_cbupdater:filename:filename:cbupdater_filename:V'),(50,4,'vtiger_cbupdater:execstate:execstate:cbupdater_execstate:V'),(50,5,'vtiger_cbupdater:systemupdate:systemupdate:cbupdater_systemupdate:C'),(50,6,'vtiger_crmentity:smownerid:assigned_user_id:cbupdater_Assigned_To:V'),(51,0,'vtiger_cobropago:reference:reference:CobroPago_Reference:V'),(51,1,'vtiger_cobropago:duedate:duedate:CobroPago_DueDate:D'),(51,2,'vtiger_cobropago:parent_id:parent_id:CobroPago_Parent:V'),(51,3,'vtiger_cobropago:related_id:related_id:CobroPago_RelatedTo:V'),(51,4,'vtiger_cobropago:amount:amount:CobroPago_Amount:N'),(51,5,'vtiger_crmentity:smownerid:assigned_user_id:CobroPago_Assigned_To:V'),(52,0,'vtiger_cobropago:reference:reference:CobroPago_Reference:V'),(52,1,'vtiger_cobropago:duedate:duedate:CobroPago_DueDate:D'),(52,2,'vtiger_cobropago:amount:amount:CobroPago_Amount:N'),(52,3,'vtiger_cobropago:cost:cost:CobroPago_Cost:N'),(52,4,'vtiger_cobropago:benefit:benefit:CobroPago_Benefit:N'),(52,5,'vtiger_cobropago:paid:paid:CobroPago_Paid:C'),(52,6,'vtiger_crmentity:smownerid:assigned_user_id:CobroPago_Assigned_To:V'),(53,0,'vtiger_assets:asset_no:asset_no:Assets_Asset_No:V'),(53,1,'vtiger_assets:assetname:assetname:Assets_Asset_Name:V'),(53,2,'vtiger_assets:account:account:Assets_Customer_Name:V'),(53,3,'vtiger_assets:product:product:Assets_Product_Name:V'),(54,0,'vtiger_modcomments:commentcontent:commentcontent:ModComments_Comment:V'),(54,1,'vtiger_modcomments:related_to:related_to:ModComments_Related_To:V'),(54,2,'vtiger_crmentity:modifiedtime:modifiedtime:ModComments_Modified_Time:T'),(54,3,'vtiger_crmentity:smownerid:assigned_user_id:ModComments_Assigned_To:V'),(55,0,'vtiger_projectmilestone:projectmilestonename:projectmilestonename:ProjectMilestone_Project_Milestone_Name:V'),(55,1,'vtiger_projectmilestone:projectmilestonedate:projectmilestonedate:ProjectMilestone_Milestone_Date:D'),(55,3,'vtiger_crmentity:description:description:ProjectMilestone_description:V'),(55,4,'vtiger_crmentity:createdtime:createdtime:ProjectMilestone_Created_Time:T'),(55,5,'vtiger_crmentity:modifiedtime:modifiedtime:ProjectMilestone_Modified_Time:T'),(56,2,'vtiger_projecttask:projecttaskname:projecttaskname:ProjectTask_Project_Task_Name:V'),(56,3,'vtiger_projecttask:projectid:projectid:ProjectTask_Related_to:V'),(56,4,'vtiger_projecttask:projecttaskpriority:projecttaskpriority:ProjectTask_Priority:V'),(56,5,'vtiger_projecttask:projecttaskprogress:projecttaskprogress:ProjectTask_Progress:V'),(56,6,'vtiger_projecttask:projecttaskhours:projecttaskhours:ProjectTask_Worked_Hours:V'),(56,7,'vtiger_projecttask:startdate:startdate:ProjectTask_Start_Date:D'),(56,8,'vtiger_projecttask:enddate:enddate:ProjectTask_End_Date:D'),(56,9,'vtiger_crmentity:smownerid:assigned_user_id:ProjectTask_Assigned_To:V'),(57,0,'vtiger_project:projectname:projectname:Project_Project_Name:V'),(57,1,'vtiger_project:linktoaccountscontacts:linktoaccountscontacts:Project_Related_to:V'),(57,2,'vtiger_project:startdate:startdate:Project_Start_Date:D'),(57,3,'vtiger_project:targetenddate:targetenddate:Project_Target_End_Date:D'),(57,4,'vtiger_project:actualenddate:actualenddate:Project_Actual_End_Date:D'),(57,5,'vtiger_project:targetbudget:targetbudget:Project_Target_Budget:V'),(57,6,'vtiger_project:progress:progress:Project_Progress:V'),(57,7,'vtiger_project:projectstatus:projectstatus:Project_Status:V'),(57,8,'vtiger_crmentity:smownerid:assigned_user_id:Project_Assigned_To:V'),(58,0,'vtiger_smsnotifier:message:message:SMSNotifier_message:V'),(58,2,'vtiger_crmentity:smownerid:assigned_user_id:SMSNotifier_Assigned_To:V'),(58,3,'vtiger_crmentity:createdtime:createdtime:SMSNotifier_Created_Time:T'),(58,4,'vtiger_crmentity:modifiedtime:modifiedtime:SMSNotifier_Modified_Time:T'),(59,0,'vtiger_globalvariable:globalno:globalno:GlobalVariable_Globalno:V'),(59,1,'vtiger_globalvariable:gvname:gvname:GlobalVariable_Name:V'),(59,2,'vtiger_globalvariable:value:value:GlobalVariable_Value:V'),(59,3,'vtiger_crmentity:smownerid:assigned_user_id:GlobalVariable_User:V'),(59,4,'vtiger_globalvariable:default_check:default_check:GlobalVariable_Default:C'),(59,5,'vtiger_globalvariable:mandatory:mandatory:GlobalVariable_Mandatory:C'),(60,0,'vtiger_inventorydetails:inventorydetails_no:inventorydetails_no:InventoryDetails_Inventory_Details_No:V'),(60,1,'vtiger_inventorydetails:productid:productid:InventoryDetails_Products:V'),(60,2,'vtiger_inventorydetails:related_to:related_to:InventoryDetails_Related_To:I'),(60,3,'vtiger_inventorydetails:account_id:account_id:InventoryDetails_Accounts:I'),(60,4,'vtiger_inventorydetails:contact_id:contact_id:InventoryDetails_Contacts:I'),(60,5,'vtiger_inventorydetails:vendor_id:vendor_id:InventoryDetails_Vendors:I'),(60,6,'vtiger_inventorydetails:quantity:quantity:InventoryDetails_Quantity:N'),(60,7,'vtiger_inventorydetails:listprice:listprice:InventoryDetails_Listprice:N'),(60,8,'vtiger_inventorydetails:linetotal:linetotal:InventoryDetails_Line_Total:N'),(61,0,'vtiger_cbmap:mapname:mapname:cbMap_Map_Name:V'),(61,1,'vtiger_cbmap:maptype:maptype:cbMap_Map_Type:V'),(61,2,'vtiger_cbmap:targetname:targetname:cbMap_Target_Module:V'),(61,3,'vtiger_cbmap:mapnumber:mapnumber:cbMap_Map_Number:V'),(61,4,'vtiger_crmentity:smownerid:assigned_user_id:cbMap_Assigned_To:V'),(62,0,'vtiger_cbtandc:cbtandcno:cbtandcno:cbTermConditions_TandC_No:V'),(62,1,'vtiger_cbtandc:reference:reference:cbTermConditions_Reference:V'),(62,2,'vtiger_cbtandc:formodule:formodule:cbTermConditions_formodule:V'),(62,3,'vtiger_cbtandc:isdefault:isdefault:cbTermConditions_Is_Default:C'),(63,0,'vtiger_activity:eventstatus:eventstatus:cbCalendar_Status:V'),(63,1,'vtiger_activity:activitytype:activitytype:cbCalendar_Activity_Type:V'),(63,2,'vtiger_activity:subject:subject:cbCalendar_Subject:V'),(63,3,'vtiger_activity:rel_id:rel_id:cbCalendar_Related_To:I'),(63,4,'vtiger_activity:cto_id:cto_id:cbCalendar_Contact_Name:I'),(63,5,'vtiger_activity:dtstart:dtstart:cbCalendar_Start_Date_Time:DT'),(63,6,'vtiger_activity:dtend:dtend:cbCalendar_Due_Date:D'),(63,7,'vtiger_crmentity:smownerid:assigned_user_id:cbCalendar_Assigned_To:V'),(64,0,'vtiger_cbtranslation:autonum:autonum:cbtranslation_cbtranslation_No:V'),(64,1,'vtiger_cbtranslation:translation_module:translation_module:cbtranslation_Module:V'),(64,2,'vtiger_cbtranslation:translation_key:translation_key:cbtranslation_Key:V'),(64,3,'vtiger_cbtranslation:i18n:i18n:cbtranslation_i18n:V'),(64,4,'vtiger_cbtranslation:proofread:proofread:cbtranslation_Proof_Read:C'),(64,5,'vtiger_cbtranslation:locale:locale:cbtranslation_Locale:V');
/*!40000 ALTER TABLE `vtiger_cvcolumnlist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_cvstdfilter`
--

DROP TABLE IF EXISTS `vtiger_cvstdfilter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_cvstdfilter` (
  `cvid` int(19) NOT NULL,
  `columnname` varchar(250) DEFAULT '',
  `stdfilter` varchar(250) DEFAULT '',
  `startdate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  PRIMARY KEY (`cvid`),
  CONSTRAINT `fk_1_vtiger_cvstdfilter` FOREIGN KEY (`cvid`) REFERENCES `vtiger_customview` (`cvid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_cvstdfilter`
--

LOCK TABLES `vtiger_cvstdfilter` WRITE;
/*!40000 ALTER TABLE `vtiger_cvstdfilter` DISABLE KEYS */;
INSERT INTO `vtiger_cvstdfilter` VALUES (3,'vtiger_crmentity:modifiedtime:modifiedtime:Leads_Modified_Time','thismonth','2005-06-01','2005-06-30'),(6,'vtiger_crmentity:createdtime:createdtime:Accounts_Created_Time','thisweek','2005-06-19','2005-06-25'),(9,'vtiger_contactsubdetails:birthday:birthday:Contacts_Birthdate','today','2005-06-25','2005-06-25');
/*!40000 ALTER TABLE `vtiger_cvstdfilter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_datashare_grp2grp`
--

DROP TABLE IF EXISTS `vtiger_datashare_grp2grp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_datashare_grp2grp` (
  `shareid` int(19) NOT NULL,
  `share_groupid` int(19) DEFAULT NULL,
  `to_groupid` int(19) DEFAULT NULL,
  `permission` int(19) DEFAULT NULL,
  PRIMARY KEY (`shareid`),
  KEY `datashare_grp2grp_share_groupid_idx` (`share_groupid`),
  KEY `datashare_grp2grp_to_groupid_idx` (`to_groupid`),
  CONSTRAINT `fk_3_vtiger_datashare_grp2grp` FOREIGN KEY (`to_groupid`) REFERENCES `vtiger_groups` (`groupid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_datashare_grp2grp`
--

LOCK TABLES `vtiger_datashare_grp2grp` WRITE;
/*!40000 ALTER TABLE `vtiger_datashare_grp2grp` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_datashare_grp2grp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_datashare_grp2role`
--

DROP TABLE IF EXISTS `vtiger_datashare_grp2role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_datashare_grp2role` (
  `shareid` int(19) NOT NULL,
  `share_groupid` int(19) DEFAULT NULL,
  `to_roleid` varchar(255) DEFAULT NULL,
  `permission` int(19) DEFAULT NULL,
  PRIMARY KEY (`shareid`),
  KEY `idx_datashare_grp2role_share_groupid` (`share_groupid`),
  KEY `idx_datashare_grp2role_to_roleid` (`to_roleid`),
  CONSTRAINT `fk_3_vtiger_datashare_grp2role` FOREIGN KEY (`to_roleid`) REFERENCES `vtiger_role` (`roleid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_datashare_grp2role`
--

LOCK TABLES `vtiger_datashare_grp2role` WRITE;
/*!40000 ALTER TABLE `vtiger_datashare_grp2role` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_datashare_grp2role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_datashare_grp2rs`
--

DROP TABLE IF EXISTS `vtiger_datashare_grp2rs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_datashare_grp2rs` (
  `shareid` int(19) NOT NULL,
  `share_groupid` int(19) DEFAULT NULL,
  `to_roleandsubid` varchar(255) DEFAULT NULL,
  `permission` int(19) DEFAULT NULL,
  PRIMARY KEY (`shareid`),
  KEY `datashare_grp2rs_share_groupid_idx` (`share_groupid`),
  KEY `datashare_grp2rs_to_roleandsubid_idx` (`to_roleandsubid`),
  CONSTRAINT `fk_3_vtiger_datashare_grp2rs` FOREIGN KEY (`to_roleandsubid`) REFERENCES `vtiger_role` (`roleid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_datashare_grp2rs`
--

LOCK TABLES `vtiger_datashare_grp2rs` WRITE;
/*!40000 ALTER TABLE `vtiger_datashare_grp2rs` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_datashare_grp2rs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_datashare_module_rel`
--

DROP TABLE IF EXISTS `vtiger_datashare_module_rel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_datashare_module_rel` (
  `shareid` int(19) NOT NULL,
  `tabid` int(19) NOT NULL,
  `relationtype` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`shareid`),
  KEY `idx_datashare_module_rel_tabid` (`tabid`),
  CONSTRAINT `fk_1_vtiger_datashare_module_rel` FOREIGN KEY (`tabid`) REFERENCES `vtiger_tab` (`tabid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_datashare_module_rel`
--

LOCK TABLES `vtiger_datashare_module_rel` WRITE;
/*!40000 ALTER TABLE `vtiger_datashare_module_rel` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_datashare_module_rel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_datashare_relatedmodule_permission`
--

DROP TABLE IF EXISTS `vtiger_datashare_relatedmodule_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_datashare_relatedmodule_permission` (
  `shareid` int(19) NOT NULL,
  `datashare_relatedmodule_id` int(19) NOT NULL,
  `permission` int(19) DEFAULT NULL,
  PRIMARY KEY (`shareid`,`datashare_relatedmodule_id`),
  KEY `datashare_relatedmodule_permission_shareid_permissions_idx` (`shareid`,`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_datashare_relatedmodule_permission`
--

LOCK TABLES `vtiger_datashare_relatedmodule_permission` WRITE;
/*!40000 ALTER TABLE `vtiger_datashare_relatedmodule_permission` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_datashare_relatedmodule_permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_datashare_relatedmodules`
--

DROP TABLE IF EXISTS `vtiger_datashare_relatedmodules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_datashare_relatedmodules` (
  `datashare_relatedmodule_id` int(19) NOT NULL,
  `tabid` int(19) DEFAULT NULL,
  `relatedto_tabid` int(19) DEFAULT NULL,
  PRIMARY KEY (`datashare_relatedmodule_id`),
  KEY `datashare_relatedmodules_tabid_idx` (`tabid`),
  KEY `datashare_relatedmodules_relatedto_tabid_idx` (`relatedto_tabid`),
  CONSTRAINT `fk_2_vtiger_datashare_relatedmodules` FOREIGN KEY (`tabid`) REFERENCES `vtiger_tab` (`tabid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_datashare_relatedmodules`
--

LOCK TABLES `vtiger_datashare_relatedmodules` WRITE;
/*!40000 ALTER TABLE `vtiger_datashare_relatedmodules` DISABLE KEYS */;
INSERT INTO `vtiger_datashare_relatedmodules` VALUES (1,6,2),(2,6,13),(3,6,20),(4,6,22),(5,6,23),(6,2,20),(7,2,22),(8,20,22),(9,22,23);
/*!40000 ALTER TABLE `vtiger_datashare_relatedmodules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_datashare_relatedmodules_seq`
--

DROP TABLE IF EXISTS `vtiger_datashare_relatedmodules_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_datashare_relatedmodules_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_datashare_relatedmodules_seq`
--

LOCK TABLES `vtiger_datashare_relatedmodules_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_datashare_relatedmodules_seq` DISABLE KEYS */;
INSERT INTO `vtiger_datashare_relatedmodules_seq` VALUES (9);
/*!40000 ALTER TABLE `vtiger_datashare_relatedmodules_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_datashare_role2group`
--

DROP TABLE IF EXISTS `vtiger_datashare_role2group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_datashare_role2group` (
  `shareid` int(19) NOT NULL,
  `share_roleid` varchar(255) DEFAULT NULL,
  `to_groupid` int(19) DEFAULT NULL,
  `permission` int(19) DEFAULT NULL,
  PRIMARY KEY (`shareid`),
  KEY `idx_datashare_role2group_share_roleid` (`share_roleid`),
  KEY `idx_datashare_role2group_to_groupid` (`to_groupid`),
  CONSTRAINT `fk_3_vtiger_datashare_role2group` FOREIGN KEY (`share_roleid`) REFERENCES `vtiger_role` (`roleid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_datashare_role2group`
--

LOCK TABLES `vtiger_datashare_role2group` WRITE;
/*!40000 ALTER TABLE `vtiger_datashare_role2group` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_datashare_role2group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_datashare_role2role`
--

DROP TABLE IF EXISTS `vtiger_datashare_role2role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_datashare_role2role` (
  `shareid` int(19) NOT NULL,
  `share_roleid` varchar(255) DEFAULT NULL,
  `to_roleid` varchar(255) DEFAULT NULL,
  `permission` int(19) DEFAULT NULL,
  PRIMARY KEY (`shareid`),
  KEY `datashare_role2role_share_roleid_idx` (`share_roleid`),
  KEY `datashare_role2role_to_roleid_idx` (`to_roleid`),
  CONSTRAINT `fk_3_vtiger_datashare_role2role` FOREIGN KEY (`to_roleid`) REFERENCES `vtiger_role` (`roleid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_datashare_role2role`
--

LOCK TABLES `vtiger_datashare_role2role` WRITE;
/*!40000 ALTER TABLE `vtiger_datashare_role2role` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_datashare_role2role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_datashare_role2rs`
--

DROP TABLE IF EXISTS `vtiger_datashare_role2rs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_datashare_role2rs` (
  `shareid` int(19) NOT NULL,
  `share_roleid` varchar(255) DEFAULT NULL,
  `to_roleandsubid` varchar(255) DEFAULT NULL,
  `permission` int(19) DEFAULT NULL,
  PRIMARY KEY (`shareid`),
  KEY `datashare_role2s_share_roleid_idx` (`share_roleid`),
  KEY `datashare_role2s_to_roleandsubid_idx` (`to_roleandsubid`),
  CONSTRAINT `fk_3_vtiger_datashare_role2rs` FOREIGN KEY (`to_roleandsubid`) REFERENCES `vtiger_role` (`roleid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_datashare_role2rs`
--

LOCK TABLES `vtiger_datashare_role2rs` WRITE;
/*!40000 ALTER TABLE `vtiger_datashare_role2rs` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_datashare_role2rs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_datashare_rs2grp`
--

DROP TABLE IF EXISTS `vtiger_datashare_rs2grp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_datashare_rs2grp` (
  `shareid` int(19) NOT NULL,
  `share_roleandsubid` varchar(255) DEFAULT NULL,
  `to_groupid` int(19) DEFAULT NULL,
  `permission` int(19) DEFAULT NULL,
  PRIMARY KEY (`shareid`),
  KEY `datashare_rs2grp_share_roleandsubid_idx` (`share_roleandsubid`),
  KEY `datashare_rs2grp_to_groupid_idx` (`to_groupid`),
  CONSTRAINT `fk_3_vtiger_datashare_rs2grp` FOREIGN KEY (`share_roleandsubid`) REFERENCES `vtiger_role` (`roleid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_datashare_rs2grp`
--

LOCK TABLES `vtiger_datashare_rs2grp` WRITE;
/*!40000 ALTER TABLE `vtiger_datashare_rs2grp` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_datashare_rs2grp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_datashare_rs2role`
--

DROP TABLE IF EXISTS `vtiger_datashare_rs2role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_datashare_rs2role` (
  `shareid` int(19) NOT NULL,
  `share_roleandsubid` varchar(255) DEFAULT NULL,
  `to_roleid` varchar(255) DEFAULT NULL,
  `permission` int(19) DEFAULT NULL,
  PRIMARY KEY (`shareid`),
  KEY `datashare_rs2role_share_roleandsubid_idx` (`share_roleandsubid`),
  KEY `datashare_rs2role_to_roleid_idx` (`to_roleid`),
  CONSTRAINT `fk_3_vtiger_datashare_rs2role` FOREIGN KEY (`to_roleid`) REFERENCES `vtiger_role` (`roleid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_datashare_rs2role`
--

LOCK TABLES `vtiger_datashare_rs2role` WRITE;
/*!40000 ALTER TABLE `vtiger_datashare_rs2role` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_datashare_rs2role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_datashare_rs2rs`
--

DROP TABLE IF EXISTS `vtiger_datashare_rs2rs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_datashare_rs2rs` (
  `shareid` int(19) NOT NULL,
  `share_roleandsubid` varchar(255) DEFAULT NULL,
  `to_roleandsubid` varchar(255) DEFAULT NULL,
  `permission` int(19) DEFAULT NULL,
  PRIMARY KEY (`shareid`),
  KEY `datashare_rs2rs_share_roleandsubid_idx` (`share_roleandsubid`),
  KEY `idx_datashare_rs2rs_to_roleandsubid_idx` (`to_roleandsubid`),
  CONSTRAINT `fk_3_vtiger_datashare_rs2rs` FOREIGN KEY (`to_roleandsubid`) REFERENCES `vtiger_role` (`roleid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_datashare_rs2rs`
--

LOCK TABLES `vtiger_datashare_rs2rs` WRITE;
/*!40000 ALTER TABLE `vtiger_datashare_rs2rs` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_datashare_rs2rs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_date_format`
--

DROP TABLE IF EXISTS `vtiger_date_format`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_date_format` (
  `date_formatid` int(19) NOT NULL AUTO_INCREMENT,
  `date_format` varchar(200) NOT NULL,
  `sortorderid` int(19) NOT NULL DEFAULT '0',
  `presence` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`date_formatid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_date_format`
--

LOCK TABLES `vtiger_date_format` WRITE;
/*!40000 ALTER TABLE `vtiger_date_format` DISABLE KEYS */;
INSERT INTO `vtiger_date_format` VALUES (1,'dd-mm-yyyy',0,1),(2,'mm-dd-yyyy',1,1),(3,'yyyy-mm-dd',2,1);
/*!40000 ALTER TABLE `vtiger_date_format` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_date_format_seq`
--

DROP TABLE IF EXISTS `vtiger_date_format_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_date_format_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_date_format_seq`
--

LOCK TABLES `vtiger_date_format_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_date_format_seq` DISABLE KEYS */;
INSERT INTO `vtiger_date_format_seq` VALUES (3);
/*!40000 ALTER TABLE `vtiger_date_format_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_def_org_field`
--

DROP TABLE IF EXISTS `vtiger_def_org_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_def_org_field` (
  `tabid` int(10) DEFAULT NULL,
  `fieldid` int(19) NOT NULL,
  `visible` int(19) DEFAULT NULL,
  `readonly` int(19) DEFAULT NULL,
  PRIMARY KEY (`fieldid`),
  KEY `def_org_field_tabid_fieldid_idx` (`tabid`,`fieldid`),
  KEY `def_org_field_tabid_idx` (`tabid`),
  KEY `def_org_field_visible_fieldid_idx` (`visible`,`fieldid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_def_org_field`
--

LOCK TABLES `vtiger_def_org_field` WRITE;
/*!40000 ALTER TABLE `vtiger_def_org_field` DISABLE KEYS */;
INSERT INTO `vtiger_def_org_field` VALUES (6,1,0,0),(6,2,0,0),(6,3,0,0),(6,4,0,0),(6,5,0,0),(6,6,0,0),(6,7,0,0),(6,8,0,0),(6,9,0,0),(6,10,0,0),(6,11,0,0),(6,12,0,0),(6,13,0,0),(6,14,0,0),(6,15,0,0),(6,16,0,0),(6,17,0,0),(6,18,0,0),(6,19,0,0),(6,20,0,0),(6,21,0,0),(6,22,0,0),(6,23,0,0),(6,24,0,0),(6,25,0,0),(6,26,0,0),(6,27,0,0),(6,28,0,0),(6,29,0,0),(6,30,0,0),(6,31,0,0),(6,32,0,0),(6,33,0,0),(6,34,0,0),(6,35,0,0),(6,36,0,0),(7,37,0,0),(7,38,0,0),(7,39,0,0),(7,40,0,0),(7,41,0,0),(7,42,0,0),(7,43,0,0),(7,44,0,0),(7,45,0,0),(7,46,0,0),(7,47,0,0),(7,48,0,0),(7,49,0,0),(7,50,0,0),(7,51,0,0),(7,52,0,0),(7,53,0,0),(7,54,0,0),(7,55,0,0),(7,56,0,0),(7,57,0,0),(7,58,0,0),(7,59,0,0),(7,60,0,0),(7,61,0,0),(7,62,0,0),(7,63,0,0),(7,64,0,0),(7,65,0,0),(4,66,0,0),(4,67,0,0),(4,68,0,0),(4,69,0,0),(4,70,0,0),(4,71,0,0),(4,72,0,0),(4,73,0,0),(4,74,0,0),(4,75,0,0),(4,76,0,0),(4,77,0,0),(4,78,0,0),(4,79,0,0),(4,80,0,0),(4,81,0,0),(4,82,0,0),(4,83,0,0),(4,84,0,0),(4,85,0,0),(4,86,0,0),(4,87,0,0),(4,88,0,0),(4,89,0,0),(4,90,0,0),(4,91,0,0),(4,92,0,0),(4,93,0,0),(4,94,0,0),(4,95,0,0),(4,96,0,0),(4,97,0,0),(4,98,0,0),(4,99,0,0),(4,100,0,0),(4,101,0,0),(4,102,0,0),(4,103,0,0),(4,104,0,0),(4,105,0,0),(4,106,0,0),(4,107,0,0),(4,108,0,0),(4,109,0,0),(2,110,0,0),(2,111,0,0),(2,112,0,0),(2,113,0,0),(2,114,0,0),(2,115,0,0),(2,116,0,0),(2,117,0,0),(2,118,0,0),(2,119,0,0),(2,120,0,0),(2,121,0,0),(2,122,0,0),(2,123,0,0),(2,124,0,0),(2,125,0,0),(26,126,0,0),(26,127,0,0),(26,128,0,0),(26,129,0,0),(26,130,0,0),(26,131,0,0),(26,132,0,0),(26,133,0,0),(26,134,0,0),(26,135,0,0),(26,136,0,0),(26,137,0,0),(26,138,0,0),(26,139,0,0),(26,140,0,0),(26,141,0,0),(26,142,0,0),(26,143,0,0),(26,144,0,0),(26,145,0,0),(26,146,0,0),(26,147,0,0),(26,148,0,0),(26,149,0,0),(26,150,0,0),(4,151,0,0),(6,152,0,0),(7,153,0,0),(26,154,0,0),(13,155,0,0),(13,156,0,0),(13,157,0,0),(13,158,0,0),(13,159,0,0),(13,160,0,0),(13,161,0,0),(13,162,0,0),(13,163,0,0),(13,164,0,0),(13,165,0,0),(13,166,0,0),(13,167,0,0),(13,168,0,0),(13,169,0,0),(13,170,0,0),(13,171,0,0),(13,172,0,0),(13,173,0,0),(14,174,0,0),(14,175,0,0),(14,176,0,0),(14,177,0,0),(14,178,0,0),(14,179,0,0),(14,180,0,0),(14,181,0,0),(14,182,0,0),(14,183,0,0),(14,184,0,0),(14,185,0,0),(14,186,0,0),(14,187,0,0),(14,188,0,0),(14,189,0,0),(14,190,0,0),(14,191,0,0),(14,192,0,0),(14,193,0,0),(14,194,0,0),(14,195,0,0),(14,196,0,0),(14,197,0,0),(14,198,0,0),(14,199,0,0),(14,200,0,0),(14,201,0,0),(14,202,0,0),(14,203,0,0),(14,204,0,0),(8,205,0,0),(8,206,0,0),(8,207,0,0),(8,208,0,0),(8,209,0,0),(8,210,0,0),(8,211,0,0),(8,212,0,0),(8,213,0,0),(8,214,0,0),(8,215,0,0),(8,216,0,0),(8,217,0,0),(8,218,0,0),(8,219,0,0),(10,220,0,0),(10,221,0,0),(10,222,0,0),(10,223,0,0),(10,224,0,0),(10,225,0,0),(10,226,0,0),(10,227,0,0),(10,228,0,0),(10,229,0,0),(10,230,0,0),(10,231,0,0),(9,232,0,0),(9,233,0,0),(9,234,0,0),(9,235,0,0),(9,236,0,0),(9,237,0,0),(9,238,0,0),(9,239,0,0),(9,240,0,0),(9,241,0,0),(9,242,0,0),(9,243,0,0),(9,244,0,0),(9,245,0,0),(9,246,0,0),(9,247,0,0),(9,248,0,0),(9,249,0,0),(9,250,0,0),(9,251,0,0),(9,252,0,0),(9,253,0,0),(9,254,0,0),(9,255,0,0),(16,256,0,0),(16,257,0,0),(16,258,0,0),(16,259,0,0),(16,260,0,0),(16,261,0,0),(16,262,0,0),(16,263,0,0),(16,264,0,0),(16,265,0,0),(16,266,0,0),(16,267,0,0),(16,268,0,0),(16,269,0,0),(16,270,0,0),(16,271,0,0),(16,272,0,0),(16,273,0,0),(16,274,0,0),(16,275,0,0),(16,276,0,0),(16,277,0,0),(16,278,0,0),(15,279,0,0),(15,280,0,0),(15,281,0,0),(15,282,0,0),(15,283,0,0),(15,284,0,0),(15,285,0,0),(15,286,0,0),(15,287,0,0),(15,288,0,0),(18,289,0,0),(18,290,0,0),(18,291,0,0),(18,292,0,0),(18,293,0,0),(18,294,0,0),(18,295,0,0),(18,296,0,0),(18,297,0,0),(18,298,0,0),(18,299,0,0),(18,300,0,0),(18,301,0,0),(18,302,0,0),(18,303,0,0),(18,304,0,0),(18,305,0,0),(19,306,0,0),(19,307,0,0),(19,308,0,0),(19,309,0,0),(19,310,0,0),(19,311,0,0),(19,312,0,0),(19,313,0,0),(20,314,0,0),(20,315,0,0),(20,316,0,0),(20,317,0,0),(20,318,0,0),(20,319,0,0),(20,320,0,0),(20,321,0,0),(20,322,0,0),(20,323,0,0),(20,324,0,0),(20,325,0,0),(20,326,0,0),(20,327,0,0),(20,328,0,0),(20,329,0,0),(20,330,0,0),(20,331,0,0),(20,332,0,0),(20,333,0,0),(20,334,0,0),(20,335,0,0),(20,336,0,0),(20,337,0,0),(20,338,0,0),(20,339,0,0),(20,340,0,0),(20,341,0,0),(20,342,0,0),(20,343,0,0),(20,344,0,0),(20,345,0,0),(20,346,0,0),(20,347,0,0),(20,348,0,0),(20,349,0,0),(20,350,0,0),(21,351,0,0),(21,352,0,0),(21,353,0,0),(21,354,0,0),(21,355,0,0),(21,356,0,0),(21,357,0,0),(21,358,0,0),(21,359,0,0),(21,360,0,0),(21,361,0,0),(21,362,0,0),(21,363,0,0),(21,364,0,0),(21,365,0,0),(21,366,0,0),(21,367,0,0),(21,368,0,0),(21,369,0,0),(21,370,0,0),(21,371,0,0),(21,372,0,0),(21,373,0,0),(21,374,0,0),(21,375,0,0),(21,376,0,0),(21,377,0,0),(21,378,0,0),(21,379,0,0),(21,380,0,0),(21,381,0,0),(21,382,0,0),(21,383,0,0),(21,384,0,0),(21,385,0,0),(21,386,0,0),(21,387,0,0),(21,388,0,0),(22,389,0,0),(22,390,0,0),(22,391,0,0),(22,392,0,0),(22,393,0,0),(22,394,0,0),(22,395,0,0),(22,396,0,0),(22,397,0,0),(22,398,0,0),(22,399,0,0),(22,400,0,0),(22,401,0,0),(22,402,0,0),(22,403,0,0),(22,404,0,0),(22,405,0,0),(22,406,0,0),(22,407,0,0),(22,408,0,0),(22,409,0,0),(22,410,0,0),(22,411,0,0),(22,412,0,0),(22,413,0,0),(22,414,0,0),(22,415,0,0),(22,416,0,0),(22,417,0,0),(22,418,0,0),(22,419,0,0),(22,420,0,0),(22,421,0,0),(22,422,0,0),(22,423,0,0),(22,424,0,0),(22,425,0,0),(22,426,0,0),(22,427,0,0),(22,428,0,0),(22,429,0,0),(22,430,0,0),(22,431,0,0),(22,432,0,0),(22,433,0,0),(22,434,0,0),(22,435,0,0),(23,436,0,0),(23,437,0,0),(23,438,0,0),(23,439,0,0),(23,440,0,0),(23,441,0,0),(23,442,0,0),(23,443,0,0),(23,444,0,0),(23,445,0,0),(23,446,0,0),(23,447,0,0),(23,448,0,0),(23,449,0,0),(23,450,0,0),(23,451,0,0),(23,452,0,0),(23,453,0,0),(23,454,0,0),(23,455,0,0),(23,456,0,0),(23,457,0,0),(23,458,0,0),(23,459,0,0),(23,460,0,0),(23,461,0,0),(23,462,0,0),(23,463,0,0),(23,464,0,0),(23,465,0,0),(23,466,0,0),(23,467,0,0),(23,468,0,0),(23,469,0,0),(23,470,0,0),(23,471,0,0),(23,472,0,0),(23,473,0,0),(23,474,0,0),(10,519,0,0),(10,520,0,0),(10,521,0,0),(10,522,0,0),(10,523,0,0),(10,524,0,0),(36,525,0,0),(36,526,0,0),(36,527,0,0),(36,528,0,0),(37,531,0,0),(37,532,0,0),(37,533,0,0),(37,534,0,0),(37,535,0,0),(37,536,0,0),(37,537,0,0),(37,538,0,0),(37,539,0,0),(37,540,0,0),(37,541,0,0),(37,542,0,0),(37,543,0,0),(37,544,0,0),(37,545,0,0),(37,546,0,0),(37,547,0,0),(37,548,0,0),(37,549,0,0),(38,550,0,0),(38,551,0,0),(38,552,0,0),(38,553,0,0),(38,554,0,0),(38,555,0,0),(38,556,0,0),(38,557,0,0),(38,558,0,0),(38,559,0,0),(38,560,0,0),(38,561,0,0),(38,562,0,0),(38,563,0,0),(38,564,0,0),(38,565,0,0),(38,566,0,0),(38,567,0,0),(38,568,0,0),(41,569,0,0),(41,570,0,0),(41,571,0,0),(41,572,0,0),(41,573,0,0),(41,574,0,0),(41,575,0,0),(41,576,0,0),(41,577,0,0),(41,578,0,0),(41,579,0,0),(41,580,0,0),(41,581,0,0),(41,582,0,0),(42,583,0,0),(42,584,0,0),(42,585,0,0),(42,586,0,0),(42,587,0,0),(42,588,0,0),(42,589,0,0),(42,590,0,0),(42,591,0,0),(42,592,0,0),(42,593,0,0),(42,594,0,0),(42,595,0,0),(42,596,0,0),(42,597,0,0),(42,598,0,0),(42,599,0,0),(43,600,0,0),(43,601,0,0),(43,602,0,0),(43,603,0,0),(43,604,0,0),(43,605,0,0),(43,606,0,0),(43,607,0,0),(43,608,0,0),(43,609,0,0),(43,610,0,0),(43,611,0,0),(43,612,0,0),(43,613,0,0),(43,614,0,0),(43,615,0,0),(43,616,0,0),(47,617,0,0),(47,618,0,0),(47,619,0,0),(47,620,0,0),(47,621,0,0),(47,622,0,0),(47,623,0,0),(48,624,0,0),(48,625,0,0),(48,626,0,0),(48,627,0,0),(48,628,0,0),(48,629,0,0),(48,630,0,0),(48,631,0,0),(48,632,0,0),(48,633,0,0),(49,634,0,0),(49,635,0,0),(49,636,0,0),(49,637,0,0),(49,638,0,0),(49,639,0,0),(49,640,0,0),(49,641,0,0),(49,642,0,0),(49,643,0,0),(49,644,0,0),(49,645,0,0),(49,646,0,0),(49,647,0,0),(49,648,0,0),(50,649,0,0),(50,650,0,0),(50,651,0,0),(50,652,0,0),(50,653,0,0),(50,654,0,0),(50,655,0,0),(50,656,0,0),(50,657,0,0),(50,658,0,0),(50,659,0,0),(50,660,0,0),(50,661,0,0),(50,662,0,0),(50,663,0,0),(50,664,0,0),(50,665,0,0),(52,666,0,0),(52,667,0,0),(52,668,0,0),(52,669,0,0),(52,670,0,0),(2,671,0,0),(50,672,0,0),(49,673,0,0),(2,674,0,0),(13,675,0,0),(29,676,0,0),(56,677,0,0),(56,678,0,0),(56,679,0,0),(56,680,0,0),(56,681,0,0),(56,682,0,0),(56,683,0,0),(56,684,0,0),(56,685,0,0),(56,686,0,0),(56,687,0,0),(56,688,0,0),(56,689,0,0),(47,690,0,0),(57,691,0,0),(57,692,0,0),(57,693,0,0),(57,694,0,0),(57,695,0,0),(57,696,0,0),(57,697,0,0),(57,698,0,0),(57,699,0,0),(57,700,0,0),(57,701,0,0),(57,702,0,0),(57,703,0,0),(57,704,0,0),(57,705,0,0),(57,706,0,0),(57,707,0,0),(57,708,0,0),(57,709,0,0),(57,710,0,0),(57,711,0,0),(57,712,0,0),(57,713,0,0),(18,714,0,0),(42,715,0,0),(42,716,0,0),(13,717,0,0),(14,718,0,0),(38,719,0,0),(29,720,0,0),(58,721,0,0),(58,722,0,0),(58,723,0,0),(58,724,0,0),(58,725,0,0),(58,726,0,0),(58,727,0,0),(58,728,0,0),(58,729,0,0),(56,730,0,0),(29,731,0,0),(57,732,0,0),(57,733,0,0),(49,734,0,0),(7,735,0,0),(6,736,0,0),(6,737,0,0),(4,738,0,0),(4,739,0,0),(2,740,0,0),(2,741,0,0),(13,742,0,0),(14,743,0,0),(38,744,0,0),(57,745,0,0),(2,746,0,0),(4,747,0,0),(6,748,0,0),(7,749,0,0),(8,750,0,0),(13,751,0,0),(14,752,0,0),(15,753,0,0),(18,754,0,0),(19,755,0,0),(20,756,0,0),(21,757,0,0),(22,758,0,0),(23,759,0,0),(26,760,0,0),(37,761,0,0),(38,762,0,0),(41,763,0,0),(42,764,0,0),(43,765,0,0),(48,766,0,0),(49,767,0,0),(50,768,0,0),(52,769,0,0),(56,770,0,0),(57,771,0,0),(58,772,0,0),(62,773,0,0),(62,774,0,0),(62,775,0,0),(62,776,0,0),(62,777,0,0),(62,778,0,0),(62,779,0,0),(62,780,0,0),(62,781,0,0),(23,782,0,0),(22,783,0,0),(20,784,0,0),(21,785,0,0),(63,786,0,0),(63,787,0,0),(63,788,0,0),(63,789,0,0),(63,790,0,0),(63,791,0,0),(63,792,0,0),(63,793,0,0),(63,794,0,0),(63,795,0,0),(63,796,0,0),(63,797,0,0),(63,798,0,0),(63,799,0,0),(63,800,0,0),(63,801,0,0),(63,802,0,0),(63,803,0,0),(63,804,0,0),(63,805,0,0),(63,806,0,0),(63,807,0,0),(63,808,0,0),(63,809,0,0),(63,810,0,0),(63,811,0,0),(63,812,0,0),(63,813,0,0),(63,814,0,0),(63,815,0,0),(64,816,0,0),(64,817,0,0),(64,818,0,0),(64,819,0,0),(64,820,0,0),(64,821,0,0),(64,822,0,0),(64,823,0,0),(64,824,0,0),(64,825,0,0),(64,826,0,0),(64,827,0,0),(64,828,0,0);
/*!40000 ALTER TABLE `vtiger_def_org_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_def_org_share`
--

DROP TABLE IF EXISTS `vtiger_def_org_share`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_def_org_share` (
  `ruleid` int(11) NOT NULL AUTO_INCREMENT,
  `tabid` int(11) NOT NULL,
  `permission` int(19) DEFAULT NULL,
  `editstatus` int(19) DEFAULT NULL,
  PRIMARY KEY (`ruleid`),
  KEY `fk_1_vtiger_def_org_share` (`permission`),
  CONSTRAINT `fk_1_vtiger_def_org_share` FOREIGN KEY (`permission`) REFERENCES `vtiger_org_share_action_mapping` (`share_action_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_def_org_share`
--

LOCK TABLES `vtiger_def_org_share` WRITE;
/*!40000 ALTER TABLE `vtiger_def_org_share` DISABLE KEYS */;
INSERT INTO `vtiger_def_org_share` VALUES (1,2,2,0),(2,4,2,2),(3,6,2,0),(4,7,2,0),(5,9,3,1),(6,13,2,0),(7,16,3,2),(8,20,2,0),(9,21,2,0),(10,22,2,0),(11,23,2,0),(12,26,2,0),(13,8,2,0),(14,14,2,0),(15,36,3,0),(16,37,2,0),(17,38,2,0),(18,41,3,0),(19,42,3,0),(20,43,2,0),(21,47,0,0),(22,48,2,0),(23,49,2,0),(24,50,2,0),(25,52,2,0),(26,55,3,0),(27,56,3,0),(28,57,2,0),(29,18,2,0),(30,58,3,0),(31,10,3,0),(32,60,3,0),(33,61,3,0),(34,62,2,0),(35,63,3,0),(36,64,3,0);
/*!40000 ALTER TABLE `vtiger_def_org_share` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_def_org_share_seq`
--

DROP TABLE IF EXISTS `vtiger_def_org_share_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_def_org_share_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_def_org_share_seq`
--

LOCK TABLES `vtiger_def_org_share_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_def_org_share_seq` DISABLE KEYS */;
INSERT INTO `vtiger_def_org_share_seq` VALUES (36);
/*!40000 ALTER TABLE `vtiger_def_org_share_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_defaultcv`
--

DROP TABLE IF EXISTS `vtiger_defaultcv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_defaultcv` (
  `tabid` int(19) NOT NULL,
  `defaultviewname` varchar(50) NOT NULL,
  `query` text,
  PRIMARY KEY (`tabid`),
  CONSTRAINT `fk_1_vtiger_defaultcv` FOREIGN KEY (`tabid`) REFERENCES `vtiger_tab` (`tabid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_defaultcv`
--

LOCK TABLES `vtiger_defaultcv` WRITE;
/*!40000 ALTER TABLE `vtiger_defaultcv` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_defaultcv` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_duration_minutes`
--

DROP TABLE IF EXISTS `vtiger_duration_minutes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_duration_minutes` (
  `minutesid` int(19) NOT NULL AUTO_INCREMENT,
  `duration_minutes` varchar(200) NOT NULL,
  `sortorderid` int(19) NOT NULL DEFAULT '0',
  `presence` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`minutesid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_duration_minutes`
--

LOCK TABLES `vtiger_duration_minutes` WRITE;
/*!40000 ALTER TABLE `vtiger_duration_minutes` DISABLE KEYS */;
INSERT INTO `vtiger_duration_minutes` VALUES (1,'00',0,1),(2,'15',1,1),(3,'30',2,1),(4,'45',3,1);
/*!40000 ALTER TABLE `vtiger_duration_minutes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_duration_minutes_seq`
--

DROP TABLE IF EXISTS `vtiger_duration_minutes_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_duration_minutes_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_duration_minutes_seq`
--

LOCK TABLES `vtiger_duration_minutes_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_duration_minutes_seq` DISABLE KEYS */;
INSERT INTO `vtiger_duration_minutes_seq` VALUES (4);
/*!40000 ALTER TABLE `vtiger_duration_minutes_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_durationhrs`
--

DROP TABLE IF EXISTS `vtiger_durationhrs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_durationhrs` (
  `hrsid` int(19) NOT NULL AUTO_INCREMENT,
  `hrs` varchar(50) DEFAULT NULL,
  `sortorderid` int(19) NOT NULL DEFAULT '0',
  `presence` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`hrsid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_durationhrs`
--

LOCK TABLES `vtiger_durationhrs` WRITE;
/*!40000 ALTER TABLE `vtiger_durationhrs` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_durationhrs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_durationmins`
--

DROP TABLE IF EXISTS `vtiger_durationmins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_durationmins` (
  `minsid` int(19) NOT NULL AUTO_INCREMENT,
  `mins` varchar(50) DEFAULT NULL,
  `sortorderid` int(19) NOT NULL DEFAULT '0',
  `presence` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`minsid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_durationmins`
--

LOCK TABLES `vtiger_durationmins` WRITE;
/*!40000 ALTER TABLE `vtiger_durationmins` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_durationmins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_email_access`
--

DROP TABLE IF EXISTS `vtiger_email_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_email_access` (
  `crmid` int(11) DEFAULT NULL,
  `mailid` int(11) DEFAULT NULL,
  `accessdate` date DEFAULT NULL,
  `accesstime` time DEFAULT NULL,
  KEY `crmid` (`crmid`,`mailid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_email_access`
--

LOCK TABLES `vtiger_email_access` WRITE;
/*!40000 ALTER TABLE `vtiger_email_access` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_email_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_email_track`
--

DROP TABLE IF EXISTS `vtiger_email_track`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_email_track` (
  `crmid` int(11) DEFAULT NULL,
  `mailid` int(11) DEFAULT NULL,
  `access_count` int(11) DEFAULT NULL,
  UNIQUE KEY `link_tabidtype_idx` (`crmid`,`mailid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_email_track`
--

LOCK TABLES `vtiger_email_track` WRITE;
/*!40000 ALTER TABLE `vtiger_email_track` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_email_track` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_emaildetails`
--

DROP TABLE IF EXISTS `vtiger_emaildetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_emaildetails` (
  `emailid` int(19) NOT NULL,
  `from_email` varchar(50) NOT NULL DEFAULT '',
  `to_email` text,
  `cc_email` text,
  `bcc_email` text,
  `assigned_user_email` varchar(50) NOT NULL DEFAULT '',
  `idlists` text,
  `email_flag` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`emailid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_emaildetails`
--

LOCK TABLES `vtiger_emaildetails` WRITE;
/*!40000 ALTER TABLE `vtiger_emaildetails` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_emaildetails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_emailtemplates`
--

DROP TABLE IF EXISTS `vtiger_emailtemplates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_emailtemplates` (
  `foldername` varchar(100) DEFAULT NULL,
  `templatename` varchar(100) DEFAULT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `description` text,
  `body` text,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `templateid` int(19) NOT NULL AUTO_INCREMENT,
  `sendemailfrom` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`templateid`),
  KEY `emailtemplates_foldernamd_templatename_subject_idx` (`foldername`,`templatename`,`subject`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_emailtemplates`
--

LOCK TABLES `vtiger_emailtemplates` WRITE;
/*!40000 ALTER TABLE `vtiger_emailtemplates` DISABLE KEYS */;
INSERT INTO `vtiger_emailtemplates` VALUES ('Public','Pending Invoices','Invoices Pending','Payment Due','name <br />\nstreet, <br />\ncity, <br />\nstate, <br />\n zip) <br />\n  <br />\n Dear <br />\n <br />\n Please check the following invoices that are yet to be paid by you: <br />\n <br />\n No. Date      Amount <br />\n 1   1/1/01    $4000 <br />\n 2   2/2//01   $5000 <br />\n 3   3/3/01    $10000 <br />\n 4   7/4/01    $23560 <br />\n <br />\n Kindly let us know if there are any issues that you feel are pending to be discussed. <br />\n We will be more than happy to give you a call. <br />\n We would like to continue our business with you.',0,2,NULL),('Public','Acceptance Proposal','Acceptance Proposal','Acceptance of Proposal',' Dear <br />\n <br />\nYour proposal on the project XYZW has been reviewed by us <br />\nand is acceptable in its entirety. <br />\n <br />\nWe are eagerly looking forward to this project <br />\nand are pleased about having the opportunity to work <br />\ntogether. We look forward to a long standing relationship <br />\nwith your esteemed firm. <br />\n<br />\nI would like to take this opportunity to invite you <br />\nto a game of golf on Wednesday morning 9am at the <br />\nCuff Links Ground. We will be waiting for you in the <br />\nExecutive Lounge. <br />\n<br />\nLooking forward to seeing you there.',0,3,NULL),('Public','Goods received acknowledgement','Goods received acknowledgement','Acknowledged Receipt of Goods',' The undersigned hereby acknowledges receipt and delivery of the goods. <br />\nThe undersigned will release the payment subject to the goods being discovered not satisfactory. <br />\n<br />\nSigned under seal this <date>',0,4,NULL),('Public','Accept Order','Accept Order','Acknowledgement/Acceptance of Order',' Dear <br />\n         We are in receipt of your order as contained in the <br />\n   purchase order form.We consider this to be final and binding on both sides. <br />\nIf there be any exceptions noted, we shall consider them <br />\nonly if the objection is received within ten days of receipt of <br />\nthis notice. <br />\n <br />\nThank you for your patronage.',0,5,NULL),('Public','Address Change','Change of Address','Address Change','Dear <br />\n <br />\nWe are relocating our office to <br />\n11111,XYZDEF Cross, <br />\nUVWWX Circle <br />\nThe telephone number for this new location is (101) 1212-1328. <br />\n<br />\nOur Manufacturing Division will continue operations <br />\nat 3250 Lovedale Square Avenue, in Frankfurt. <br />\n<br />\nWe hope to keep in touch with you all. <br />\nPlease update your addressbooks.',0,6,NULL),('Public','Follow Up','Follow Up','Follow Up of meeting','Dear <br />\n<br />\nThank you for extending us the opportunity to meet with <br />\nyou and members of your staff. <br />\n<br />\nI know that John Doe serviced your account <br />\nfor many years and made many friends at your firm. He has personally <br />\ndiscussed with me the deep relationship that he had with your firm. <br />\nWhile his presence will be missed, I can promise that we will <br />\ncontinue to provide the fine service that was accorded by <br />\nJohn to your firm. <br />\n<br />\nI was genuinely touched to receive such fine hospitality. <br />\n<br />\nThank you once again.',0,7,NULL),('Public','Target Crossed!','Target Crossed!','Fantastic Sales Spree!','Congratulations! <br />\n<br />\nThe numbers are in and I am proud to inform you that our <br />\ntotal sales for the previous quarter <br />\namounts to $100,000,00.00!. This is the first time <br />\nwe have exceeded the target by almost 30%. <br />\nWe have also beat the previous quarter record by a <br />\nwhopping 75%! <br />\n<br />\nLet us meet at Smoking Joe for a drink in the evening! <br />\n\nC you all there guys!',0,8,NULL),('Public','Thanks Note','Thanks Note','Note of thanks','Dear <br />\n<br />\nThank you for your confidence in our ability to serve you. <br />\nWe are glad to be given the chance to serve you.I look <br />\nforward to establishing a long term partnership with you. <br />\nConsider me as a friend. <br />\nShould any need arise,please do give us a call.',0,9,NULL),('Public','Customer Login Details','Customer Portal Login Details','Send Portal login details to customer','<table width=\"700\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; text-decoration: none; background-color: rgb(122, 122, 254);\">\n        <tr>\n            <td> </td>\n            <td> </td>\n            <td> </td>\n        </tr>\n        <tr>\n            <td> </td>\n            <td> </td>\n            <td> </td>\n        </tr>\n        <tr>\n            <td> </td>\n            <td> </td>\n            <td> </td>\n        </tr>\n        <tr>\n            <td width=\"50\"> </td>\n            <td>\n            <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n                    <tr>\n                        <td>\n                        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"background-color: rgb(27, 77, 140); font-family: Arial,Helvetica,sans-serif; font-size: 14px; color: rgb(255, 255, 255); font-weight: normal; line-height: 25px;\">\n                                <tr>\n                                    <td align=\"center\" rowspan=\"4\">$logo$</td>\n                                    <td align=\"center\"> </td>\n                                </tr>\n                                <tr>\n                                    <td align=\"left\" style=\"background-color: rgb(27, 77, 140); font-family: Arial,Helvetica,sans-serif; font-size: 24px; color: rgb(255, 255, 255); font-weight: bolder; line-height: 35px;\">vtiger CRM<br /> </td>\n                                </tr>\n                                <tr>\n                                    <td align=\"right\" style=\"padding-right: 100px;\">The honest Open Source CRM </td>\n                                </tr>\n                                <tr>\n                                    <td> </td>\n                                </tr>\n                        </table>\n                        </td>\n                    </tr>\n                    <tr>\n                        <td>\n                        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);\">\n                                <tr>\n                                    <td valign=\"top\">\n                                    <table width=\"100%\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\">\n                                            <tr>\n                                                <td align=\"right\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);\"> </td>\n                                            </tr>\n                                            <tr>\n                                                <td> </td>\n                                            </tr>\n                                            <tr>\n                                                <td style=\"font-family: Arial,Helvetica,sans-serif; font-size: 14px; color: rgb(22, 72, 134); font-weight: bolder; line-height: 15px;\">Dear $contact_name$, </td>\n                                            </tr>\n                                            <tr>\n                                                <td style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; text-align: justify; line-height: 20px;\"> Thank you very much for subscribing to the vtiger CRM - annual support service.<br />Here is your self service portal login details:</td>\n                                            </tr>\n                                            <tr>\n                                                <td align=\"center\">\n                                                <table width=\"75%\" cellspacing=\"0\" cellpadding=\"10\" border=\"0\" style=\"border: 2px solid rgb(180, 180, 179); background-color: rgb(226, 226, 225); font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal;\">\n                                                        <tr>\n                                                            <td><br />User ID     : <font color=\"#990000\"><strong> $login_name$</strong></font> </td>\n                                                        </tr>\n                                                        <tr>\n                                                            <td>Password: <font color=\"#990000\"><strong> $password$</strong></font> </td>\n                                                        </tr>\n                                                        <tr>\n                                                            <td align=\"center\"> <strong>  $URL$<br /> </strong> </td>\n                                                        </tr>\n                                                </table>\n                                                </td>\n                                            </tr>\n                                            <tr>\n                                                <td style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; text-align: justify; line-height: 20px;\"><strong>NOTE:</strong> We suggest you to change your password after logging in first time. <br /><br /> <strong><u>Help Documentation</u></strong><br />  <br /> After logging in to vtiger Self-service Portal first time, you can access the vtiger CRM documents from the <strong>Documents</strong> tab. Following documents are available for your reference:<br />\n                                                <ul>\n                                                    <li>Installation Manual (Windows &amp; Linux OS)<br /> </li>\n                                                    <li>User &amp; Administrator Manual<br /> </li>\n                                                    <li>vtiger Customer Portal - User Manual<br /> </li>\n                                                    <li>vtiger Outlook Plugin - User Manual<br /> </li>\n                                                    <li>vtiger Office Plug-in - User Manual<br /> </li>\n                                                    <li>vtiger Thunderbird Extension - User Manual<br /> </li>\n                                                    <li>vtiger Web Forms - User Manual<br /> </li>\n                                                    <li>vtiger Firefox Tool bar - User Manual<br /> </li>\n                                                </ul>\n                                                <br />  <br /> <strong><u>Knowledge Base</u></strong><br /> <br /> Periodically we update frequently asked question based on our customer experiences. You can access the latest articles from the <strong>FAQ</strong> tab.<br /> <br /> <strong><u>vtiger CRM - Details</u></strong><br /> <br /> Kindly let us know your current vtiger CRM version and system specification so that we can provide you necessary guidelines to enhance your vtiger CRM system performance. Based on your system specification we alert you about the latest security &amp; upgrade patches.<br />  <br />			 Thank you once again and wish you a wonderful experience with vtiger CRM.<br /> </td>\n                                            </tr>\n                                            <tr>\n                                                <td align=\"right\"><strong style=\"padding: 2px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: bold;\"><br /><br />Best Regards</strong></td>\n                                            </tr>\n                                            <tr>\n                                                <td align=\"right\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; line-height: 20px;\">$support_team$ </td>\n                                            </tr>\n                                            <tr>\n                                                <td align=\"right\"><a style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);\" href=\"http://www.vtiger.com\">www.vtiger.com</a></td>\n                                            </tr>\n                                            <tr>\n                                                <td> </td>\n                                            </tr>\n                                    </table>\n                                    </td>\n                                    <td width=\"1%\" valign=\"top\"> </td>\n                                </tr>\n                        </table>\n                        </td>\n                    </tr>\n                    <tr>\n                        <td>\n                        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(255, 255, 255); font-weight: normal; line-height: 15px; background-color: rgb(51, 51, 51);\">\n                                <tr>\n                                    <td align=\"center\">Shree Narayana Complex, No 11 Sarathy Nagar, Vijaya Nagar , Velachery, Chennai - 600 042 India </td>\n                                </tr>\n                                <tr>\n                                    <td align=\"center\">Telephone No: +91 - 44 - 4202 - 1990     Toll Free No: +1 877 788 4437</td>\n                                </tr>\n                                <tr>\n                                    <td align=\"center\">Email Id: <a style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(255, 255, 255);\" href=\"mailto:support@vtiger.com\">support@vtiger.com</a></td>\n                                </tr>\n                        </table>\n                        </td>\n                    </tr>\n            </table>\n            </td>\n            <td width=\"50\"> </td>\n        </tr>\n        <tr>\n            <td> </td>\n            <td> </td>\n            <td> </td>\n        </tr>\n        <tr>\n            <td> </td>\n            <td> </td>\n            <td> </td>\n        </tr>\n        <tr>\n            <td> </td>\n            <td> </td>\n            <td> </td>\n        </tr>\n</table>',0,10,NULL),('Public','Support end notification before a week','VtigerCRM Support Notification','Send Notification mail to customer before a week of support end date','<table width=\"700\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; text-decoration: none; background-color: rgb(122, 122, 254);\">\n        <tr>\n            <td> </td>\n            <td> </td>\n            <td> </td>\n        </tr>\n        <tr>\n            <td> </td>\n            <td> </td>\n            <td> </td>\n        </tr>\n        <tr>\n            <td> </td>\n            <td> </td>\n            <td> </td>\n        </tr>\n        <tr>\n            <td width=\"50\"> </td>\n            <td>\n            <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n                    <tr>\n                        <td>\n                        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"background-color: rgb(27, 77, 140); font-family: Arial,Helvetica,sans-serif; font-size: 14px; color: rgb(255, 255, 255); font-weight: normal; line-height: 25px;\">\n                                <tr>\n                                    <td align=\"center\" rowspan=\"4\">$logo$</td>\n                                    <td align=\"center\"> </td>\n                                </tr>\n                                <tr>\n                                    <td align=\"left\" style=\"background-color: rgb(27, 77, 140); font-family: Arial,Helvetica,sans-serif; font-size: 24px; color: rgb(255, 255, 255); font-weight: bolder; line-height: 35px;\">vtiger CRM </td>\n                                </tr>\n                                <tr>\n                                    <td align=\"right\" style=\"padding-right: 100px;\">The honest Open Source CRM </td>\n                                </tr>\n                                <tr>\n                                    <td> </td>\n                                </tr>\n                        </table>\n                        </td>\n                    </tr>\n                    <tr>\n                        <td>\n                        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);\">\n                                <tr>\n                                    <td valign=\"top\">\n                                    <table width=\"100%\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\">\n                                            <tr>\n                                                <td align=\"right\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);\"> </td>\n                                            </tr>\n                                            <tr>\n                                                <td> </td>\n                                            </tr>\n                                            <tr>\n                                                <td style=\"font-family: Arial,Helvetica,sans-serif; font-size: 14px; color: rgb(22, 72, 134); font-weight: bolder; line-height: 15px;\">Dear $contacts-lastname$, </td>\n                                            </tr>\n                                            <tr>\n                                                <td style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; text-align: justify; line-height: 20px;\">This is just a notification mail regarding your support end.<br /><span style=\"font-weight: bold;\">Priority:</span> Urgent<br />Your Support is going to expire on next week<br />Please contact support@vtiger.com.<br /><br /><br /></td>\n                                            </tr>\n                                            <tr>\n                                                <td align=\"center\"><br /></td>\n                                            </tr>\n                                            <tr>\n                                                <td align=\"right\"><strong style=\"padding: 2px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: bold;\"><br /><br />Sincerly</strong></td>\n                                            </tr>\n                                            <tr>\n                                                <td align=\"right\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; line-height: 20px;\">Support Team </td>\n                                            </tr>\n                                            <tr>\n                                                <td align=\"right\"><a style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);\" href=\"http://www.vtiger.com\">www.vtiger.com</a></td>\n                                            </tr>\n                                            <tr>\n                                                <td> </td>\n                                            </tr>\n                                    </table>\n                                    </td>\n                                    <td width=\"1%\" valign=\"top\"> </td>\n                                </tr>\n                        </table>\n                        </td>\n                    </tr>\n                    <tr>\n                        <td>\n                        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(255, 255, 255); font-weight: normal; line-height: 15px; background-color: rgb(51, 51, 51);\">\n                                <tr>\n                                    <td align=\"center\">Shree Narayana Complex, No 11 Sarathy Nagar, Vijaya Nagar , Velachery, Chennai - 600 042 India </td>\n                                </tr>\n                                <tr>\n                                    <td align=\"center\">Telephone No: +91 - 44 - 4202 - 1990     Toll Free No: +1 877 788 4437</td>\n                                </tr>\n                                <tr>\n                                    <td align=\"center\">Email Id: <a style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(255, 255, 255);\" href=\"mailto:info@vtiger.com\">info@vtiger.com</a></td>\n                                </tr>\n                        </table>\n                        </td>\n                    </tr>\n            </table>\n            </td>\n            <td width=\"50\"> </td>\n        </tr>\n        <tr>\n            <td> </td>\n            <td> </td>\n            <td> </td>\n        </tr>\n        <tr>\n            <td> </td>\n            <td> </td>\n            <td> </td>\n        </tr>\n        <tr>\n            <td> </td>\n            <td> </td>\n            <td> </td>\n        </tr>\n</table>',0,11,NULL),('Public','Support end notification before a month','VtigerCRM Support Notification','Send Notification mail to customer before a month of support end date','<table width=\"700\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; text-decoration: none; background-color: rgb(122, 122, 254);\">\n        <tr>\n            <td> </td>\n            <td> </td>\n            <td> </td>\n        </tr>\n        <tr>\n            <td> </td>\n            <td> </td>\n            <td> </td>\n        </tr>\n        <tr>\n            <td> </td>\n            <td> </td>\n            <td> </td>\n        </tr>\n        <tr>\n            <td width=\"50\"> </td>\n            <td>\n            <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n                    <tr>\n                        <td>\n                        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"background-color: rgb(27, 77, 140); font-family: Arial,Helvetica,sans-serif; font-size: 14px; color: rgb(255, 255, 255); font-weight: normal; line-height: 25px;\">\n                                <tr>\n                                    <td align=\"center\" rowspan=\"4\">$logo$</td>\n                                    <td align=\"center\"> </td>\n                                </tr>\n                                <tr>\n                                    <td align=\"left\" style=\"background-color: rgb(27, 77, 140); font-family: Arial,Helvetica,sans-serif; font-size: 24px; color: rgb(255, 255, 255); font-weight: bolder; line-height: 35px;\">vtiger CRM </td>\n                                </tr>\n                                <tr>\n                                    <td align=\"right\" style=\"padding-right: 100px;\">The honest Open Source CRM </td>\n                                </tr>\n                                <tr>\n                                    <td> </td>\n                                </tr>\n                        </table>\n                        </td>\n                    </tr>\n                    <tr>\n                        <td>\n                        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: normal; color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);\">\n                                <tr>\n                                    <td valign=\"top\">\n                                    <table width=\"100%\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\">\n                                            <tr>\n                                                <td align=\"right\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);\"> </td>\n                                            </tr>\n                                            <tr>\n                                                <td> </td>\n                                            </tr>\n                                            <tr>\n                                                <td style=\"font-family: Arial,Helvetica,sans-serif; font-size: 14px; color: rgb(22, 72, 134); font-weight: bolder; line-height: 15px;\">Dear $contacts-lastname$, </td>\n                                            </tr>\n                                            <tr>\n                                                <td style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; text-align: justify; line-height: 20px;\">This is just a notification mail regarding your support end.<br /><span style=\"font-weight: bold;\">Priority:</span> Normal<br />Your Support is going to expire on next month.<br />Please contact support@vtiger.com<br /><br /><br /></td>\n                                            </tr>\n                                            <tr>\n                                                <td align=\"center\"><br /></td>\n                                            </tr>\n                                            <tr>\n                                                <td align=\"right\"><strong style=\"padding: 2px; font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: bold;\"><br /><br />Sincerly</strong></td>\n                                            </tr>\n                                            <tr>\n                                                <td align=\"right\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(0, 0, 0); font-weight: normal; line-height: 20px;\">Support Team </td>\n                                            </tr>\n                                            <tr>\n                                                <td align=\"right\"><a href=\"http://www.vtiger.com\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(66, 66, 253);\">www.vtiger.com</a></td>\n                                            </tr>\n                                            <tr>\n                                                <td> </td>\n                                            </tr>\n                                    </table>\n                                    </td>\n                                    <td width=\"1%\" valign=\"top\"> </td>\n                                </tr>\n                        </table>\n                        </td>\n                    </tr>\n                    <tr>\n                        <td>\n                        <table width=\"100%\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; color: rgb(255, 255, 255); font-weight: normal; line-height: 15px; background-color: rgb(51, 51, 51);\">\n                                <tr>\n                                    <td align=\"center\">Shree Narayana Complex, No 11 Sarathy Nagar, Vijaya Nagar , Velachery, Chennai - 600 042 India </td>\n                                </tr>\n                                <tr>\n                                    <td align=\"center\">Telephone No: +91 - 44 - 4202 - 1990     Toll Free No: +1 877 788 4437</td>\n                                </tr>\n                                <tr>\n                                    <td align=\"center\">Email Id: <a href=\"mailto:info@vtiger.com\" style=\"font-family: Arial,Helvetica,sans-serif; font-size: 12px; font-weight: bolder; text-decoration: none; color: rgb(255, 255, 255);\">info@vtiger.com</a></td>\n                                </tr>\n                        </table>\n                        </td>\n                    </tr>\n            </table>\n            </td>\n            <td width=\"50\"> </td>\n        </tr>\n        <tr>\n            <td> </td>\n            <td> </td>\n            <td> </td>\n        </tr>\n        <tr>\n            <td> </td>\n            <td> </td>\n            <td> </td>\n        </tr>\n        <tr>\n            <td> </td>\n            <td> </td>\n            <td> </td>\n        </tr>\n</table>',0,12,NULL);
/*!40000 ALTER TABLE `vtiger_emailtemplates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_emailtemplates_seq`
--

DROP TABLE IF EXISTS `vtiger_emailtemplates_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_emailtemplates_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_emailtemplates_seq`
--

LOCK TABLES `vtiger_emailtemplates_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_emailtemplates_seq` DISABLE KEYS */;
INSERT INTO `vtiger_emailtemplates_seq` VALUES (12);
/*!40000 ALTER TABLE `vtiger_emailtemplates_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_entityname`
--

DROP TABLE IF EXISTS `vtiger_entityname`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_entityname` (
  `tabid` int(19) NOT NULL DEFAULT '0',
  `modulename` varchar(50) NOT NULL,
  `tablename` varchar(100) NOT NULL,
  `fieldname` varchar(150) NOT NULL,
  `entityidfield` varchar(150) NOT NULL,
  `entityidcolumn` varchar(150) NOT NULL,
  PRIMARY KEY (`tabid`),
  KEY `modulename` (`modulename`),
  CONSTRAINT `fk_1_vtiger_entityname` FOREIGN KEY (`tabid`) REFERENCES `vtiger_tab` (`tabid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_entityname`
--

LOCK TABLES `vtiger_entityname` WRITE;
/*!40000 ALTER TABLE `vtiger_entityname` DISABLE KEYS */;
INSERT INTO `vtiger_entityname` VALUES (2,'Potentials','vtiger_potential','potentialname','potentialid','potential_id'),(4,'Contacts','vtiger_contactdetails','firstname,lastname','contactid','contact_id'),(6,'Accounts','vtiger_account','accountname','accountid','account_id'),(7,'Leads','vtiger_leaddetails','firstname,lastname','leadid','leadid'),(8,'Documents','vtiger_notes','title','notesid','notesid'),(9,'Calendar','vtiger_activity','subject','activityid','activityid'),(10,'Emails','vtiger_activity','subject','activityid','activityid'),(13,'HelpDesk','vtiger_troubletickets','title','ticketid','ticketid'),(14,'Products','vtiger_products','productname','productid','product_id'),(15,'Faq','vtiger_faq','question','id','id'),(18,'Vendors','vtiger_vendor','vendorname','vendorid','vendor_id'),(19,'PriceBooks','vtiger_pricebook','bookname','pricebookid','pricebookid'),(20,'Quotes','vtiger_quotes','subject','quoteid','quote_id'),(21,'PurchaseOrder','vtiger_purchaseorder','subject','purchaseorderid','purchaseorderid'),(22,'SalesOrder','vtiger_salesorder','subject','salesorderid','salesorder_id'),(23,'Invoice','vtiger_invoice','subject','invoiceid','invoiceid'),(26,'Campaigns','vtiger_campaign','campaignname','campaignid','campaignid'),(29,'Users','vtiger_users','first_name,last_name','id','id'),(36,'PBXManager','vtiger_pbxmanager','callfrom','pbxmanagerid','pbxmanagerid'),(37,'ServiceContracts','vtiger_servicecontracts','subject','servicecontractsid','servicecontractsid'),(38,'Services','vtiger_service','servicename','serviceid','serviceid'),(41,'cbupdater','vtiger_cbupdater','cbupd_no','cbupdaterid','cbupdaterid'),(42,'CobroPago','vtiger_cobropago','reference,cyp_no','cobropagoid','cobropagoid'),(43,'Assets','vtiger_assets','assetname','assetsid','assetsid'),(47,'ModComments','vtiger_modcomments','commentcontent','modcommentsid','modcommentsid'),(48,'ProjectMilestone','vtiger_projectmilestone','projectmilestonename','projectmilestoneid','projectmilestoneid'),(49,'ProjectTask','vtiger_projecttask','projecttaskname','projecttaskid','projecttaskid'),(50,'Project','vtiger_project','projectname','projectid','projectid'),(52,'SMSNotifier','vtiger_smsnotifier','message','smsnotifierid','smsnotifierid'),(56,'GlobalVariable','vtiger_globalvariable','globalno','globalvariableid','globalvariableid'),(57,'InventoryDetails','vtiger_inventorydetails','inventorydetails_no','inventorydetailsid','inventorydetailsid'),(58,'cbMap','vtiger_cbmap','mapname','cbmapid','cbmapid'),(62,'cbTermConditions','vtiger_cbtandc','cbtandcno','cbtandcid','cbtandcid'),(63,'cbCalendar','vtiger_activity','subject','activityid','activityid'),(64,'cbtranslation','vtiger_cbtranslation','autonum','cbtranslationid','cbtranslationid');
/*!40000 ALTER TABLE `vtiger_entityname` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_eventhandler_module`
--

DROP TABLE IF EXISTS `vtiger_eventhandler_module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_eventhandler_module` (
  `eventhandler_module_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(100) DEFAULT NULL,
  `handler_class` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`eventhandler_module_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_eventhandler_module`
--

LOCK TABLES `vtiger_eventhandler_module` WRITE;
/*!40000 ALTER TABLE `vtiger_eventhandler_module` DISABLE KEYS */;
INSERT INTO `vtiger_eventhandler_module` VALUES (1,'ModTracker','ModTrackerHandler'),(2,'ServiceContracts','ServiceContractsHandler'),(4,NULL,'GoogleSync4YouHandler');
/*!40000 ALTER TABLE `vtiger_eventhandler_module` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_eventhandler_module_seq`
--

DROP TABLE IF EXISTS `vtiger_eventhandler_module_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_eventhandler_module_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_eventhandler_module_seq`
--

LOCK TABLES `vtiger_eventhandler_module_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_eventhandler_module_seq` DISABLE KEYS */;
INSERT INTO `vtiger_eventhandler_module_seq` VALUES (4);
/*!40000 ALTER TABLE `vtiger_eventhandler_module_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_eventhandlers`
--

DROP TABLE IF EXISTS `vtiger_eventhandlers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_eventhandlers` (
  `eventhandler_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_name` varchar(100) NOT NULL,
  `handler_path` varchar(400) NOT NULL,
  `handler_class` varchar(100) NOT NULL,
  `cond` text,
  `is_active` int(1) NOT NULL,
  `dependent_on` varchar(255) DEFAULT '[]',
  PRIMARY KEY (`eventhandler_id`,`event_name`,`handler_class`),
  UNIQUE KEY `eventhandler_idx` (`eventhandler_id`),
  KEY `event_name` (`event_name`,`is_active`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_eventhandlers`
--

LOCK TABLES `vtiger_eventhandlers` WRITE;
/*!40000 ALTER TABLE `vtiger_eventhandlers` DISABLE KEYS */;
INSERT INTO `vtiger_eventhandlers` VALUES (1,'vtiger.entity.aftersave','modules/SalesOrder/RecurringInvoiceHandler.php','RecurringInvoiceHandler','',1,'[]'),(2,'vtiger.entity.beforesave','data/VTEntityDelta.php','VTEntityDelta','',1,'[]'),(3,'vtiger.entity.aftersave','data/VTEntityDelta.php','VTEntityDelta','',1,'[]'),(4,'vtiger.entity.aftersave','modules/com_vtiger_workflow/VTEventHandler.inc','VTWorkflowEventHandler','',1,'[\"VTEntityDelta\"]'),(5,'vtiger.entity.afterrestore','modules/com_vtiger_workflow/VTEventHandler.inc','VTWorkflowEventHandler','',1,'[]'),(6,'vtiger.entity.aftersave.final','modules/HelpDesk/HelpDeskHandler.php','HelpDeskHandler','',1,'[]'),(7,'vtiger.entity.aftersave.final','modules/ModTracker/ModTrackerHandler.php','ModTrackerHandler','',1,'[]'),(8,'vtiger.entity.beforedelete','modules/ModTracker/ModTrackerHandler.php','ModTrackerHandler','',1,'[]'),(9,'vtiger.entity.beforesave','modules/ServiceContracts/ServiceContractsHandler.php','ServiceContractsHandler','',1,'[]'),(10,'vtiger.entity.aftersave','modules/ServiceContracts/ServiceContractsHandler.php','ServiceContractsHandler','',1,'[]'),(11,'vtiger.entity.aftersave','modules/WSAPP/WorkFlowHandlers/WSAPPAssignToTracker.php','WSAPPAssignToTracker','',1,'[\"VTEntityDelta\"]'),(13,'vtiger.entity.aftersave','modules/Emails/evcbrcHandler.php','evcbrcHandler','',1,'[]'),(14,'vtiger.entity.aftersave','modules/Calendar4You/GoogleSync4YouHandler.php','GoogleSync4YouHandler','',1,'[]'),(15,'vtiger.entity.beforedelete','modules/Calendar4You/GoogleSync4YouHandler.php','GoogleSync4YouHandler','',1,'[]'),(16,'vtiger.entity.afterrestore','modules/ModTracker/ModTrackerHandler.php','ModTrackerHandler','',1,'[]'),(17,'vtiger.entity.aftersave','modules/PBXManager/PBXManagerHandler.php','PBXManagerAfterSaveCreateActivity','',1,'[]'),(18,'vtiger.entity.beforedelete','modules/com_vtiger_workflow/VTEventHandler.inc','VTWorkflowEventHandler','',1,'[]'),(19,'corebos.permissions.accessquery','modules/cbCalendar/PublicInvitePermission.php','PublicInvitePermissionHandler','',1,'[]');
/*!40000 ALTER TABLE `vtiger_eventhandlers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_eventhandlers_seq`
--

DROP TABLE IF EXISTS `vtiger_eventhandlers_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_eventhandlers_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_eventhandlers_seq`
--

LOCK TABLES `vtiger_eventhandlers_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_eventhandlers_seq` DISABLE KEYS */;
INSERT INTO `vtiger_eventhandlers_seq` VALUES (19);
/*!40000 ALTER TABLE `vtiger_eventhandlers_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_eventstatus`
--

DROP TABLE IF EXISTS `vtiger_eventstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_eventstatus` (
  `eventstatusid` int(19) NOT NULL AUTO_INCREMENT,
  `eventstatus` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`eventstatusid`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_eventstatus`
--

LOCK TABLES `vtiger_eventstatus` WRITE;
/*!40000 ALTER TABLE `vtiger_eventstatus` DISABLE KEYS */;
INSERT INTO `vtiger_eventstatus` VALUES (1,'Planned',0,38),(2,'Held',0,39),(3,'Not Held',0,40),(4,'Not Started',1,529),(5,'In Progress',1,530),(6,'Completed',1,531),(7,'Pending Input',1,532),(8,'Deferred',1,533);
/*!40000 ALTER TABLE `vtiger_eventstatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_eventstatus_seq`
--

DROP TABLE IF EXISTS `vtiger_eventstatus_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_eventstatus_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_eventstatus_seq`
--

LOCK TABLES `vtiger_eventstatus_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_eventstatus_seq` DISABLE KEYS */;
INSERT INTO `vtiger_eventstatus_seq` VALUES (8);
/*!40000 ALTER TABLE `vtiger_eventstatus_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_evvtmenu`
--

DROP TABLE IF EXISTS `vtiger_evvtmenu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_evvtmenu` (
  `evvtmenuid` int(11) NOT NULL AUTO_INCREMENT,
  `mtype` varchar(25) NOT NULL,
  `mvalue` varchar(200) NOT NULL,
  `mlabel` varchar(200) NOT NULL,
  `mparent` int(11) NOT NULL,
  `mseq` smallint(6) NOT NULL,
  `mvisible` tinyint(4) NOT NULL,
  `mpermission` varchar(250) NOT NULL,
  PRIMARY KEY (`evvtmenuid`),
  KEY `mparent` (`mparent`),
  KEY `mlabel` (`mlabel`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_evvtmenu`
--

LOCK TABLES `vtiger_evvtmenu` WRITE;
/*!40000 ALTER TABLE `vtiger_evvtmenu` DISABLE KEYS */;
INSERT INTO `vtiger_evvtmenu` VALUES (1,'menu','','My Home Page',0,1,1,''),(2,'module','Home','Home',1,1,1,''),(3,'module','Calendar','Calendar',1,2,1,''),(4,'module','Webmails','Webmails',1,3,1,''),(5,'menu','','Marketing',0,2,1,''),(6,'module','Campaigns','Campaigns',5,1,1,''),(7,'module','Accounts','Accounts',5,2,1,''),(8,'module','Contacts','Contacts',5,3,1,''),(9,'module','Webmails','Webmails',5,4,1,''),(10,'module','Leads','Leads',5,5,1,''),(11,'module','Calendar','Calendar',5,6,1,''),(12,'module','Documents','Documents',5,7,1,''),(13,'menu','','Sales',0,3,1,''),(14,'module','Leads','Leads',13,1,1,''),(15,'module','Accounts','Accounts',13,2,1,''),(16,'module','Contacts','Contacts',13,3,1,''),(17,'module','Potentials','Potentials',13,4,1,''),(18,'module','Quotes','Quotes',13,5,1,''),(19,'module','SalesOrder','SalesOrder',13,6,1,''),(20,'module','Invoice','Invoice',13,7,1,''),(21,'module','PriceBooks','PriceBooks',13,8,1,''),(22,'module','Documents','Documents',13,9,1,''),(23,'module','Calendar','Calendar',13,10,1,''),(24,'menu','','Support',0,4,1,''),(25,'module','HelpDesk','HelpDesk',24,1,1,''),(26,'module','Faq','Faq',24,2,1,''),(27,'module','Accounts','Accounts',24,3,1,''),(28,'module','Contacts','Contacts',24,4,1,''),(29,'module','Documents','Documents',24,5,1,''),(30,'module','Webmails','Webmails',24,6,1,''),(31,'module','Calendar','Calendar',24,7,1,''),(32,'module','ServiceContracts','ServiceContracts',24,8,1,''),(33,'module','ProjectMilestone','ProjectMilestone',24,9,1,''),(34,'module','ProjectTask','ProjectTask',24,10,1,''),(35,'module','Project','Project',24,11,1,''),(36,'menu','','Analytics',0,5,1,''),(37,'module','Reports','Reports',36,1,1,''),(38,'module','Dashboard','Dashboard',36,2,1,''),(39,'menu','','Inventory',0,6,1,''),(40,'module','Products','Products',39,1,1,''),(41,'module','Vendors','Vendors',39,2,1,''),(42,'module','PriceBooks','PriceBooks',39,3,1,''),(43,'module','PurchaseOrder','PurchaseOrder',39,4,1,''),(44,'module','SalesOrder','SalesOrder',39,5,1,''),(45,'module','Quotes','Quotes',39,6,1,''),(46,'module','Invoice','Invoice',39,7,1,''),(47,'module','Services','Services',39,8,1,''),(48,'module','CobroPago','CobroPago',39,9,1,''),(49,'module','Assets','Assets',39,10,1,''),(50,'module','InventoryDetails','InventoryDetails',39,11,1,''),(51,'menu','','Tools',0,7,1,''),(52,'module','Rss','Rss',51,1,1,''),(53,'module','Portal','Portal',51,2,1,''),(54,'module','Documents','Documents',51,3,1,''),(56,'module','MailManager','MailManager',51,5,1,''),(57,'module','PBXManager','PBXManager',51,6,1,''),(58,'module','ModComments','ModComments',51,7,1,''),(59,'module','RecycleBin','RecycleBin',51,8,1,''),(60,'module','SMSNotifier','SMSNotifier',51,9,1,''),(61,'module','Calendar4You','Calendar4You',51,10,1,''),(62,'module','GlobalVariable','GlobalVariable',51,11,1,''),(63,'module','cbMap','cbMap',51,12,1,''),(64,'menu','','Settings',0,8,1,''),(65,'module','cbupdater','cbupdater',64,1,1,''),(66,'module','evvtMenu','evvtMenu',64,2,1,''),(67,'module','cbCalendar','cbCalendar',1,4,1,''),(68,'module','cbtranslation','cbtranslation',64,3,1,'');
/*!40000 ALTER TABLE `vtiger_evvtmenu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_execstate`
--

DROP TABLE IF EXISTS `vtiger_execstate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_execstate` (
  `execstateid` int(11) NOT NULL AUTO_INCREMENT,
  `execstate` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`execstateid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_execstate`
--

LOCK TABLES `vtiger_execstate` WRITE;
/*!40000 ALTER TABLE `vtiger_execstate` DISABLE KEYS */;
INSERT INTO `vtiger_execstate` VALUES (1,'Pending',1,235),(2,'Executed',1,236),(3,'Continuous',1,237),(4,'Error',1,238);
/*!40000 ALTER TABLE `vtiger_execstate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_execstate_seq`
--

DROP TABLE IF EXISTS `vtiger_execstate_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_execstate_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_execstate_seq`
--

LOCK TABLES `vtiger_execstate_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_execstate_seq` DISABLE KEYS */;
INSERT INTO `vtiger_execstate_seq` VALUES (4);
/*!40000 ALTER TABLE `vtiger_execstate_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_expectedresponse`
--

DROP TABLE IF EXISTS `vtiger_expectedresponse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_expectedresponse` (
  `expectedresponseid` int(19) NOT NULL AUTO_INCREMENT,
  `expectedresponse` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`expectedresponseid`),
  UNIQUE KEY `CampaignExpRes_UK01` (`expectedresponse`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_expectedresponse`
--

LOCK TABLES `vtiger_expectedresponse` WRITE;
/*!40000 ALTER TABLE `vtiger_expectedresponse` DISABLE KEYS */;
INSERT INTO `vtiger_expectedresponse` VALUES (1,'--None--',1,41),(2,'Excellent',1,42),(3,'Good',1,43),(4,'Average',1,44),(5,'Poor',1,45);
/*!40000 ALTER TABLE `vtiger_expectedresponse` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_expectedresponse_seq`
--

DROP TABLE IF EXISTS `vtiger_expectedresponse_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_expectedresponse_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_expectedresponse_seq`
--

LOCK TABLES `vtiger_expectedresponse_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_expectedresponse_seq` DISABLE KEYS */;
INSERT INTO `vtiger_expectedresponse_seq` VALUES (5);
/*!40000 ALTER TABLE `vtiger_expectedresponse_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_faq`
--

DROP TABLE IF EXISTS `vtiger_faq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faq_no` varchar(100) NOT NULL,
  `product_id` varchar(100) DEFAULT NULL,
  `question` text,
  `answer` text,
  `category` varchar(200) NOT NULL,
  `status` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `faq_id_idx` (`id`),
  CONSTRAINT `fk_1_vtiger_faq` FOREIGN KEY (`id`) REFERENCES `vtiger_crmentity` (`crmid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_faq`
--

LOCK TABLES `vtiger_faq` WRITE;
/*!40000 ALTER TABLE `vtiger_faq` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_faq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_faqcategories`
--

DROP TABLE IF EXISTS `vtiger_faqcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_faqcategories` (
  `faqcategories_id` int(19) NOT NULL AUTO_INCREMENT,
  `faqcategories` varchar(200) DEFAULT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`faqcategories_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_faqcategories`
--

LOCK TABLES `vtiger_faqcategories` WRITE;
/*!40000 ALTER TABLE `vtiger_faqcategories` DISABLE KEYS */;
INSERT INTO `vtiger_faqcategories` VALUES (1,'General',1,46);
/*!40000 ALTER TABLE `vtiger_faqcategories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_faqcategories_seq`
--

DROP TABLE IF EXISTS `vtiger_faqcategories_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_faqcategories_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_faqcategories_seq`
--

LOCK TABLES `vtiger_faqcategories_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_faqcategories_seq` DISABLE KEYS */;
INSERT INTO `vtiger_faqcategories_seq` VALUES (1);
/*!40000 ALTER TABLE `vtiger_faqcategories_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_faqcf`
--

DROP TABLE IF EXISTS `vtiger_faqcf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_faqcf` (
  `faqid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`faqid`),
  CONSTRAINT `fk_1_vtiger_faqcf` FOREIGN KEY (`faqid`) REFERENCES `vtiger_faq` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_faqcf`
--

LOCK TABLES `vtiger_faqcf` WRITE;
/*!40000 ALTER TABLE `vtiger_faqcf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_faqcf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_faqcomments`
--

DROP TABLE IF EXISTS `vtiger_faqcomments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_faqcomments` (
  `commentid` int(19) NOT NULL AUTO_INCREMENT,
  `faqid` int(19) DEFAULT NULL,
  `comments` text,
  `createdtime` datetime NOT NULL,
  PRIMARY KEY (`commentid`),
  KEY `faqcomments_faqid_idx` (`faqid`),
  CONSTRAINT `fk_1_vtiger_faqcomments` FOREIGN KEY (`faqid`) REFERENCES `vtiger_faq` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_faqcomments`
--

LOCK TABLES `vtiger_faqcomments` WRITE;
/*!40000 ALTER TABLE `vtiger_faqcomments` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_faqcomments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_faqstatus`
--

DROP TABLE IF EXISTS `vtiger_faqstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_faqstatus` (
  `faqstatus_id` int(19) NOT NULL AUTO_INCREMENT,
  `faqstatus` varchar(200) DEFAULT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`faqstatus_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_faqstatus`
--

LOCK TABLES `vtiger_faqstatus` WRITE;
/*!40000 ALTER TABLE `vtiger_faqstatus` DISABLE KEYS */;
INSERT INTO `vtiger_faqstatus` VALUES (1,'Draft',0,47),(2,'Reviewed',0,48),(3,'Published',0,49),(4,'Obsolete',0,50);
/*!40000 ALTER TABLE `vtiger_faqstatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_faqstatus_seq`
--

DROP TABLE IF EXISTS `vtiger_faqstatus_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_faqstatus_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_faqstatus_seq`
--

LOCK TABLES `vtiger_faqstatus_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_faqstatus_seq` DISABLE KEYS */;
INSERT INTO `vtiger_faqstatus_seq` VALUES (4);
/*!40000 ALTER TABLE `vtiger_faqstatus_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_field`
--

DROP TABLE IF EXISTS `vtiger_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_field` (
  `tabid` int(19) NOT NULL,
  `fieldid` int(19) NOT NULL AUTO_INCREMENT,
  `columnname` varchar(30) NOT NULL,
  `tablename` varchar(100) DEFAULT NULL,
  `generatedtype` int(19) NOT NULL DEFAULT '0',
  `uitype` varchar(30) NOT NULL,
  `fieldname` varchar(50) NOT NULL,
  `fieldlabel` varchar(50) NOT NULL,
  `readonly` int(1) NOT NULL,
  `presence` int(19) NOT NULL DEFAULT '1',
  `defaultvalue` text,
  `maximumlength` int(19) DEFAULT NULL,
  `sequence` int(19) DEFAULT NULL,
  `block` int(19) DEFAULT NULL,
  `displaytype` int(19) DEFAULT NULL,
  `typeofdata` varchar(100) DEFAULT NULL,
  `quickcreate` int(10) NOT NULL DEFAULT '1',
  `quickcreatesequence` int(19) DEFAULT NULL,
  `info_type` varchar(20) DEFAULT NULL,
  `masseditable` int(10) NOT NULL DEFAULT '1',
  `helpinfo` text,
  PRIMARY KEY (`fieldid`),
  KEY `field_tabid_idx` (`tabid`),
  KEY `field_fieldname_idx` (`fieldname`),
  KEY `field_block_idx` (`block`),
  KEY `field_displaytype_idx` (`displaytype`),
  CONSTRAINT `fk_1_vtiger_field` FOREIGN KEY (`tabid`) REFERENCES `vtiger_tab` (`tabid`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=829 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_field`
--

LOCK TABLES `vtiger_field` WRITE;
/*!40000 ALTER TABLE `vtiger_field` DISABLE KEYS */;
INSERT INTO `vtiger_field` VALUES (6,1,'accountname','vtiger_account',1,'2','accountname','Account Name',1,0,'',100,1,9,1,'V~M',0,1,'BAS',0,NULL),(6,2,'account_no','vtiger_account',1,'4','account_no','Account No',1,0,'',100,2,9,1,'V~O',3,NULL,'BAS',0,NULL),(6,3,'phone','vtiger_account',1,'11','phone','Phone',1,2,'',100,4,9,1,'V~O',2,2,'BAS',1,NULL),(6,4,'website','vtiger_account',1,'17','website','Website',1,2,'',100,3,9,1,'V~O',2,3,'BAS',1,NULL),(6,5,'fax','vtiger_account',1,'11','fax','Fax',1,2,'',100,6,9,1,'V~O',1,NULL,'BAS',1,NULL),(6,6,'tickersymbol','vtiger_account',1,'1','tickersymbol','Ticker Symbol',1,2,'',100,5,9,1,'V~O',1,NULL,'BAS',1,NULL),(6,7,'otherphone','vtiger_account',1,'11','otherphone','Other Phone',1,2,'',100,8,9,1,'V~O',1,NULL,'ADV',1,NULL),(6,8,'parentid','vtiger_account',1,'51','account_id','Member Of',1,2,'',100,7,9,1,'I~O',1,NULL,'BAS',0,NULL),(6,9,'email1','vtiger_account',1,'13','email1','Email',1,2,'',100,10,9,1,'E~O',1,NULL,'BAS',1,NULL),(6,10,'employees','vtiger_account',1,'7','employees','Employees',1,2,'',100,9,9,1,'I~O',1,NULL,'ADV',1,NULL),(6,11,'email2','vtiger_account',1,'13','email2','Other Email',1,2,'',100,11,9,1,'E~O',1,NULL,'ADV',1,NULL),(6,12,'ownership','vtiger_account',1,'1','ownership','Ownership',1,2,'',100,12,9,1,'V~O',1,NULL,'ADV',1,NULL),(6,13,'rating','vtiger_account',1,'15','rating','Rating',1,2,'',100,14,9,1,'V~O',1,NULL,'ADV',1,NULL),(6,14,'industry','vtiger_account',1,'15','industry','industry',1,2,'',100,13,9,1,'V~O',1,NULL,'ADV',1,NULL),(6,15,'siccode','vtiger_account',1,'1','siccode','SIC Code',1,2,'',100,16,9,1,'V~O',1,NULL,'ADV',1,NULL),(6,16,'account_type','vtiger_account',1,'15','accounttype','Type',1,2,'',100,15,9,1,'V~O',1,NULL,'ADV',1,NULL),(6,17,'annualrevenue','vtiger_account',1,'71','annual_revenue','Annual Revenue',1,2,'',100,18,9,1,'N~O',1,NULL,'ADV',1,NULL),(6,18,'emailoptout','vtiger_account',1,'56','emailoptout','Email Opt Out',1,2,'',100,17,9,1,'C~O',1,NULL,'ADV',1,NULL),(6,19,'notify_owner','vtiger_account',1,'56','notify_owner','Notify Owner',1,2,'',10,20,9,1,'C~O',1,NULL,'ADV',1,NULL),(6,20,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,0,'',100,19,9,1,'V~M',0,4,'BAS',1,NULL),(6,21,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,22,9,2,'DT~O',3,NULL,'BAS',0,NULL),(6,22,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,21,9,2,'DT~O',3,NULL,'BAS',0,NULL),(6,23,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,23,9,3,'V~O',3,NULL,'BAS',0,NULL),(6,24,'bill_street','vtiger_accountbillads',1,'21','bill_street','Billing Address',1,2,'',100,1,11,1,'V~O',1,NULL,'BAS',1,NULL),(6,25,'ship_street','vtiger_accountshipads',1,'21','ship_street','Shipping Address',1,2,'',100,2,11,1,'V~O',1,NULL,'BAS',1,NULL),(6,26,'bill_city','vtiger_accountbillads',1,'1','bill_city','Billing City',1,2,'',100,5,11,1,'V~O',1,NULL,'BAS',1,NULL),(6,27,'ship_city','vtiger_accountshipads',1,'1','ship_city','Shipping City',1,2,'',100,6,11,1,'V~O',1,NULL,'BAS',1,NULL),(6,28,'bill_state','vtiger_accountbillads',1,'1','bill_state','Billing State',1,2,'',100,7,11,1,'V~O',1,NULL,'BAS',1,NULL),(6,29,'ship_state','vtiger_accountshipads',1,'1','ship_state','Shipping State',1,2,'',100,8,11,1,'V~O',1,NULL,'BAS',1,NULL),(6,30,'bill_code','vtiger_accountbillads',1,'1','bill_code','Billing Code',1,2,'',100,9,11,1,'V~O',1,NULL,'BAS',1,NULL),(6,31,'ship_code','vtiger_accountshipads',1,'1','ship_code','Shipping Code',1,2,'',100,10,11,1,'V~O',1,NULL,'BAS',1,NULL),(6,32,'bill_country','vtiger_accountbillads',1,'1','bill_country','Billing Country',1,2,'',100,11,11,1,'V~O',1,NULL,'BAS',1,NULL),(6,33,'ship_country','vtiger_accountshipads',1,'1','ship_country','Shipping Country',1,2,'',100,12,11,1,'V~O',1,NULL,'BAS',1,NULL),(6,34,'bill_pobox','vtiger_accountbillads',1,'1','bill_pobox','Billing Po Box',1,2,'',100,3,11,1,'V~O',1,NULL,'BAS',1,NULL),(6,35,'ship_pobox','vtiger_accountshipads',1,'1','ship_pobox','Shipping Po Box',1,2,'',100,4,11,1,'V~O',1,NULL,'BAS',1,NULL),(6,36,'description','vtiger_crmentity',1,'19','description','Description',1,2,'',100,1,12,1,'V~O',1,NULL,'BAS',1,NULL),(7,37,'salutation','vtiger_leaddetails',1,'55','salutationtype','Salutation',1,0,'',100,1,13,3,'V~O',1,NULL,'BAS',1,NULL),(7,38,'firstname','vtiger_leaddetails',1,'55','firstname','First Name',1,0,'',100,2,13,1,'V~O',2,1,'BAS',1,NULL),(7,39,'lead_no','vtiger_leaddetails',1,'4','lead_no','Lead No',1,0,'',100,3,13,1,'V~O',3,NULL,'BAS',0,NULL),(7,40,'phone','vtiger_leadaddress',1,'11','phone','Phone',1,2,'',100,5,13,1,'V~O',2,4,'BAS',1,NULL),(7,41,'lastname','vtiger_leaddetails',1,'255','lastname','Last Name',1,0,'',100,4,13,1,'V~M',0,2,'BAS',1,NULL),(7,42,'mobile','vtiger_leadaddress',1,'11','mobile','Mobile',1,2,'',100,7,13,1,'V~O',1,NULL,'BAS',1,NULL),(7,43,'company','vtiger_leaddetails',1,'2','company','Company',1,2,'',100,6,13,1,'V~M',2,3,'BAS',1,NULL),(7,44,'fax','vtiger_leadaddress',1,'11','fax','Fax',1,2,'',100,9,13,1,'V~O',1,NULL,'BAS',1,NULL),(7,45,'designation','vtiger_leaddetails',1,'1','designation','Designation',1,2,'',100,8,13,1,'V~O',1,NULL,'BAS',1,NULL),(7,46,'email','vtiger_leaddetails',1,'13','email','Email',1,2,'',100,11,13,1,'E~O',2,5,'BAS',1,NULL),(7,47,'leadsource','vtiger_leaddetails',1,'15','leadsource','Lead Source',1,2,'',100,10,13,1,'V~O',1,NULL,'BAS',1,NULL),(7,48,'website','vtiger_leadsubdetails',1,'17','website','Website',1,2,'',100,13,13,1,'V~O',1,NULL,'ADV',1,NULL),(7,49,'industry','vtiger_leaddetails',1,'15','industry','Industry',1,2,'',100,12,13,1,'V~O',1,NULL,'ADV',1,NULL),(7,50,'leadstatus','vtiger_leaddetails',1,'15','leadstatus','Lead Status',1,2,'',100,15,13,1,'V~O',1,NULL,'BAS',1,NULL),(7,51,'annualrevenue','vtiger_leaddetails',1,'71','annualrevenue','Annual Revenue',1,2,'',100,14,13,1,'N~O',1,NULL,'ADV',1,NULL),(7,52,'rating','vtiger_leaddetails',1,'15','rating','Rating',1,2,'',100,17,13,1,'V~O',1,NULL,'ADV',1,NULL),(7,53,'noofemployees','vtiger_leaddetails',1,'1','noofemployees','No Of Employees',1,2,'',100,16,13,1,'I~O',1,NULL,'ADV',1,NULL),(7,54,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,0,'',100,19,13,1,'V~M',0,6,'BAS',1,NULL),(7,55,'secondaryemail','vtiger_leaddetails',1,'13','secondaryemail','Secondary Email',1,2,'',100,18,13,1,'E~O',1,NULL,'ADV',1,NULL),(7,56,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,21,13,2,'DT~O',3,NULL,'BAS',0,NULL),(7,57,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,20,13,2,'DT~O',3,NULL,'BAS',0,NULL),(7,58,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,23,13,3,'V~O',3,NULL,'BAS',0,NULL),(7,59,'lane','vtiger_leadaddress',1,'21','lane','Street',1,2,'',100,1,15,1,'V~O',1,NULL,'BAS',1,NULL),(7,60,'code','vtiger_leadaddress',1,'1','code','Postal Code',1,2,'',100,3,15,1,'V~O',1,NULL,'BAS',1,NULL),(7,61,'city','vtiger_leadaddress',1,'1','city','City',1,2,'',100,4,15,1,'V~O',1,NULL,'BAS',1,NULL),(7,62,'country','vtiger_leadaddress',1,'1','country','Country',1,2,'',100,5,15,1,'V~O',1,NULL,'BAS',1,NULL),(7,63,'state','vtiger_leadaddress',1,'1','state','State',1,2,'',100,6,15,1,'V~O',1,NULL,'BAS',1,NULL),(7,64,'pobox','vtiger_leadaddress',1,'1','pobox','Po Box',1,2,'',100,2,15,1,'V~O',1,NULL,'BAS',1,NULL),(7,65,'description','vtiger_crmentity',1,'19','description','Description',1,2,'',100,1,16,1,'V~O',1,NULL,'BAS',1,NULL),(4,66,'salutation','vtiger_contactdetails',1,'55','salutationtype','Salutation',1,0,'',100,1,4,3,'V~O',1,NULL,'BAS',1,NULL),(4,67,'firstname','vtiger_contactdetails',1,'55','firstname','First Name',1,0,'',100,2,4,1,'V~O',2,1,'BAS',1,NULL),(4,68,'contact_no','vtiger_contactdetails',1,'4','contact_no','Contact Id',1,0,'',100,3,4,1,'V~O',3,NULL,'BAS',0,NULL),(4,69,'phone','vtiger_contactdetails',1,'11','phone','Office Phone',1,2,'',100,5,4,1,'V~O',2,4,'BAS',1,NULL),(4,70,'lastname','vtiger_contactdetails',1,'255','lastname','Last Name',1,0,'',100,4,4,1,'V~M',0,2,'BAS',1,NULL),(4,71,'mobile','vtiger_contactdetails',1,'11','mobile','Mobile',1,2,'',100,7,4,1,'V~O',1,NULL,'BAS',1,NULL),(4,72,'accountid','vtiger_contactdetails',1,'51','account_id','Account Name',1,0,'',100,6,4,1,'I~O',2,3,'BAS',1,NULL),(4,73,'homephone','vtiger_contactsubdetails',1,'11','homephone','Home Phone',1,2,'',100,9,4,1,'V~O',1,NULL,'ADV',1,NULL),(4,74,'leadsource','vtiger_contactsubdetails',1,'15','leadsource','Lead Source',1,2,'',100,8,4,1,'V~O',1,NULL,'BAS',1,NULL),(4,75,'otherphone','vtiger_contactsubdetails',1,'11','otherphone','Other Phone',1,2,'',100,11,4,1,'V~O',1,NULL,'ADV',1,NULL),(4,76,'title','vtiger_contactdetails',1,'1','title','Title',1,2,'',100,10,4,1,'V~O',1,NULL,'BAS',1,NULL),(4,77,'fax','vtiger_contactdetails',1,'11','fax','Fax',1,2,'',100,13,4,1,'V~O',1,NULL,'BAS',1,NULL),(4,78,'department','vtiger_contactdetails',1,'1','department','Department',1,2,'',100,12,4,1,'V~O',1,NULL,'ADV',1,NULL),(4,79,'birthday','vtiger_contactsubdetails',1,'5','birthday','Birthdate',1,2,'',100,16,4,1,'D~O',1,NULL,'ADV',1,NULL),(4,80,'email','vtiger_contactdetails',1,'13','email','Email',1,2,'',100,15,4,1,'E~O',2,5,'BAS',1,NULL),(4,81,'reportsto','vtiger_contactdetails',1,'57','contact_id','Reports To',1,2,'',100,18,4,1,'V~O',1,NULL,'ADV',0,NULL),(4,82,'assistant','vtiger_contactsubdetails',1,'1','assistant','Assistant',1,2,'',100,17,4,1,'V~O',1,NULL,'ADV',1,NULL),(4,83,'secondaryemail','vtiger_contactdetails',1,'13','secondaryemail','Secondary Email',1,2,'',100,20,4,1,'E~O',1,NULL,'ADV',1,NULL),(4,84,'assistantphone','vtiger_contactsubdetails',1,'11','assistantphone','Assistant Phone',1,2,'',100,19,4,1,'V~O',1,NULL,'ADV',1,NULL),(4,85,'donotcall','vtiger_contactdetails',1,'56','donotcall','Do Not Call',1,2,'',100,22,4,1,'C~O',1,NULL,'ADV',1,NULL),(4,86,'emailoptout','vtiger_contactdetails',1,'56','emailoptout','Email Opt Out',1,2,'',100,21,4,1,'C~O',1,NULL,'ADV',1,NULL),(4,87,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,0,'',100,24,4,1,'V~M',0,6,'BAS',1,NULL),(4,88,'reference','vtiger_contactdetails',1,'56','reference','Reference',1,2,'',10,23,4,1,'C~O',1,NULL,'ADV',1,NULL),(4,89,'notify_owner','vtiger_contactdetails',1,'56','notify_owner','Notify Owner',1,2,'',10,26,4,1,'C~O',1,NULL,'ADV',1,NULL),(4,90,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,25,4,2,'DT~O',3,NULL,'BAS',0,NULL),(4,91,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,27,4,2,'DT~O',3,NULL,'BAS',0,NULL),(4,92,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,28,4,3,'V~O',3,NULL,'BAS',0,NULL),(4,93,'portal','vtiger_customerdetails',1,'56','portal','Portal User',1,2,'',100,1,6,1,'C~O',1,NULL,'ADV',2,NULL),(4,94,'support_start_date','vtiger_customerdetails',1,'5','support_start_date','Support Start Date',1,2,'',100,2,6,1,'D~O',1,NULL,'ADV',1,NULL),(4,95,'support_end_date','vtiger_customerdetails',1,'5','support_end_date','Support End Date',1,2,'',100,3,6,1,'D~O~OTH~GE~support_start_date~Support Start Date',1,NULL,'ADV',1,NULL),(4,96,'mailingstreet','vtiger_contactaddress',1,'21','mailingstreet','Mailing Street',1,2,'',100,1,7,1,'V~O',1,NULL,'BAS',1,NULL),(4,97,'otherstreet','vtiger_contactaddress',1,'21','otherstreet','Other Street',1,2,'',100,2,7,1,'V~O',1,NULL,'BAS',1,NULL),(4,98,'mailingcity','vtiger_contactaddress',1,'1','mailingcity','Mailing City',1,2,'',100,5,7,1,'V~O',1,NULL,'BAS',1,NULL),(4,99,'othercity','vtiger_contactaddress',1,'1','othercity','Other City',1,2,'',100,6,7,1,'V~O',1,NULL,'BAS',1,NULL),(4,100,'mailingstate','vtiger_contactaddress',1,'1','mailingstate','Mailing State',1,2,'',100,7,7,1,'V~O',1,NULL,'BAS',1,NULL),(4,101,'otherstate','vtiger_contactaddress',1,'1','otherstate','Other State',1,2,'',100,8,7,1,'V~O',1,NULL,'BAS',1,NULL),(4,102,'mailingzip','vtiger_contactaddress',1,'1','mailingzip','Mailing Zip',1,2,'',100,9,7,1,'V~O',1,NULL,'BAS',1,NULL),(4,103,'otherzip','vtiger_contactaddress',1,'1','otherzip','Other Zip',1,2,'',100,10,7,1,'V~O',1,NULL,'BAS',1,NULL),(4,104,'mailingcountry','vtiger_contactaddress',1,'1','mailingcountry','Mailing Country',1,2,'',100,11,7,1,'V~O',1,NULL,'BAS',1,NULL),(4,105,'othercountry','vtiger_contactaddress',1,'1','othercountry','Other Country',1,2,'',100,12,7,1,'V~O',1,NULL,'BAS',1,NULL),(4,106,'mailingpobox','vtiger_contactaddress',1,'1','mailingpobox','Mailing Po Box',1,2,'',100,3,7,1,'V~O',1,NULL,'BAS',1,NULL),(4,107,'otherpobox','vtiger_contactaddress',1,'1','otherpobox','Other Po Box',1,2,'',100,4,7,1,'V~O',1,NULL,'BAS',1,NULL),(4,108,'imagename','vtiger_contactdetails',1,'69','imagename','Contact Image',1,2,'',100,1,73,1,'V~O',3,NULL,'ADV',0,NULL),(4,109,'description','vtiger_crmentity',1,'19','description','Description',1,2,'',100,1,8,1,'V~O',1,NULL,'BAS',1,NULL),(2,110,'potentialname','vtiger_potential',1,'2','potentialname','Potential Name',1,0,'',100,1,1,1,'V~M',0,1,'BAS',1,NULL),(2,111,'potential_no','vtiger_potential',1,'4','potential_no','Potential No',1,0,'',100,2,1,1,'V~O',3,NULL,'BAS',0,NULL),(2,112,'amount','vtiger_potential',1,'71','amount','Amount',1,2,'',100,4,1,1,'N~O',2,5,'BAS',1,NULL),(2,113,'related_to','vtiger_potential',1,'10','related_to','Related To',1,0,'',100,3,1,1,'V~M',0,2,'BAS',1,NULL),(2,114,'closingdate','vtiger_potential',1,'5','closingdate','Expected Close Date',1,2,'',100,7,1,1,'D~M',2,3,'BAS',1,NULL),(2,115,'potentialtype','vtiger_potential',1,'15','opportunity_type','Type',1,2,'',100,6,1,1,'V~O',1,NULL,'BAS',1,NULL),(2,116,'nextstep','vtiger_potential',1,'1','nextstep','Next Step',1,2,'',100,9,1,1,'V~O',1,NULL,'BAS',1,NULL),(2,117,'leadsource','vtiger_potential',1,'15','leadsource','Lead Source',1,2,'',100,8,1,1,'V~O',1,NULL,'BAS',1,NULL),(2,118,'sales_stage','vtiger_potential',1,'15','sales_stage','Sales Stage',1,2,'',100,11,1,1,'V~M',2,4,'BAS',1,NULL),(2,119,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,2,'',100,10,1,1,'V~M',0,6,'BAS',1,NULL),(2,120,'probability','vtiger_potential',1,'9','probability','Probability',1,2,'',100,13,1,1,'N~O~3,2',1,NULL,'BAS',1,NULL),(2,121,'campaignid','vtiger_potential',1,'10','campaignid','Campaign Source',1,2,'',100,12,1,1,'N~O',1,NULL,'BAS',1,NULL),(2,122,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,15,1,2,'DT~O',3,NULL,'BAS',0,NULL),(2,123,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,14,1,2,'DT~O',3,NULL,'BAS',0,NULL),(2,124,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,16,1,3,'V~O',3,NULL,'BAS',0,NULL),(2,125,'description','vtiger_crmentity',1,'19','description','Description',1,2,'',100,1,3,1,'V~O',1,NULL,'BAS',1,NULL),(26,126,'campaignname','vtiger_campaign',1,'2','campaignname','Campaign Name',1,0,'',100,1,74,1,'V~M',0,1,'BAS',1,NULL),(26,127,'campaign_no','vtiger_campaign',1,'4','campaign_no','Campaign No',1,0,'',100,2,74,1,'V~O',3,NULL,'BAS',0,NULL),(26,128,'campaigntype','vtiger_campaign',1,'15','campaigntype','Campaign Type',1,2,'',100,5,74,1,'V~O',2,3,'BAS',1,NULL),(26,129,'product_id','vtiger_campaign',1,'59','product_id','Product',1,2,'',100,6,74,1,'I~O',2,5,'BAS',1,NULL),(26,130,'campaignstatus','vtiger_campaign',1,'15','campaignstatus','Campaign Status',1,2,'',100,4,74,1,'V~O',2,6,'BAS',1,NULL),(26,131,'closingdate','vtiger_campaign',1,'5','closingdate','Expected Close Date',1,2,'',100,8,74,1,'D~M',2,2,'BAS',1,NULL),(26,132,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,0,'',100,3,74,1,'V~M',0,7,'BAS',1,NULL),(26,133,'numsent','vtiger_campaign',1,'7','numsent','Num Sent',1,2,'',100,12,74,1,'N~O',1,NULL,'BAS',1,NULL),(26,134,'sponsor','vtiger_campaign',1,'1','sponsor','Sponsor',1,2,'',100,9,74,1,'V~O',1,NULL,'BAS',1,NULL),(26,135,'targetaudience','vtiger_campaign',1,'1','targetaudience','Target Audience',1,2,'',100,7,74,1,'V~O',1,NULL,'BAS',1,NULL),(26,136,'targetsize','vtiger_campaign',1,'1','targetsize','TargetSize',1,2,'',100,10,74,1,'I~O',1,NULL,'BAS',1,NULL),(26,137,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,11,74,2,'DT~O',3,NULL,'BAS',0,NULL),(26,138,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,13,74,2,'DT~O',3,NULL,'BAS',0,NULL),(26,139,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,16,74,3,'V~O',3,NULL,'BAS',0,NULL),(26,140,'expectedresponse','vtiger_campaign',1,'15','expectedresponse','Expected Response',1,2,'',100,3,76,1,'V~O',2,4,'BAS',1,NULL),(26,141,'expectedrevenue','vtiger_campaign',1,'71','expectedrevenue','Expected Revenue',1,2,'',100,4,76,1,'N~O',1,NULL,'BAS',1,NULL),(26,142,'budgetcost','vtiger_campaign',1,'71','budgetcost','Budget Cost',1,2,'',100,1,76,1,'N~O',1,NULL,'BAS',1,NULL),(26,143,'actualcost','vtiger_campaign',1,'71','actualcost','Actual Cost',1,2,'',100,2,76,1,'N~O',1,NULL,'BAS',1,NULL),(26,144,'expectedresponsecount','vtiger_campaign',1,'1','expectedresponsecount','Expected Response Count',1,2,'',100,7,76,1,'I~O',1,NULL,'BAS',1,NULL),(26,145,'expectedsalescount','vtiger_campaign',1,'1','expectedsalescount','Expected Sales Count',1,2,'',100,5,76,1,'I~O',1,NULL,'BAS',1,NULL),(26,146,'expectedroi','vtiger_campaign',1,'71','expectedroi','Expected ROI',1,2,'',100,9,76,1,'N~O',1,NULL,'BAS',1,NULL),(26,147,'actualresponsecount','vtiger_campaign',1,'1','actualresponsecount','Actual Response Count',1,2,'',100,8,76,1,'I~O',1,NULL,'BAS',1,NULL),(26,148,'actualsalescount','vtiger_campaign',1,'1','actualsalescount','Actual Sales Count',1,2,'',100,6,76,1,'I~O',1,NULL,'BAS',1,NULL),(26,149,'actualroi','vtiger_campaign',1,'71','actualroi','Actual ROI',1,2,'',100,10,76,1,'N~O',1,NULL,'BAS',1,NULL),(26,150,'description','vtiger_crmentity',1,'19','description','Description',1,2,'',100,1,81,1,'V~O',1,NULL,'BAS',1,NULL),(4,151,'campaignrelstatus','vtiger_campaignrelstatus',1,'16','campaignrelstatus','Status',1,0,'0',100,1,NULL,1,'V~O',1,NULL,'BAS',0,NULL),(6,152,'campaignrelstatus','vtiger_campaignrelstatus',1,'16','campaignrelstatus','Status',1,0,'0',100,1,NULL,1,'V~O',1,NULL,'BAS',0,NULL),(7,153,'campaignrelstatus','vtiger_campaignrelstatus',1,'16','campaignrelstatus','Status',1,0,'0',100,1,NULL,1,'V~O',1,NULL,'BAS',0,NULL),(26,154,'campaignrelstatus','vtiger_campaignrelstatus',1,'16','campaignrelstatus','Status',1,0,'0',100,1,NULL,1,'V~O',1,NULL,'BAS',0,NULL),(13,155,'ticket_no','vtiger_troubletickets',1,'4','ticket_no','Ticket No',1,0,'',100,13,25,1,'V~O',3,NULL,'BAS',0,NULL),(13,156,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,0,'',100,4,25,1,'V~M',0,4,'BAS',1,NULL),(13,157,'parent_id','vtiger_troubletickets',1,'10','parent_id','Related To',1,0,'',100,2,25,1,'I~O',1,NULL,'BAS',1,NULL),(13,158,'priority','vtiger_troubletickets',1,'15','ticketpriorities','Priority',1,2,'',100,6,25,1,'V~O',2,3,'BAS',1,NULL),(13,159,'product_id','vtiger_troubletickets',1,'59','product_id','Product Name',1,2,'',100,5,25,1,'I~O',1,NULL,'BAS',1,NULL),(13,160,'severity','vtiger_troubletickets',1,'15','ticketseverities','Severity',1,2,'',100,8,25,1,'V~O',1,NULL,'BAS',1,NULL),(13,161,'status','vtiger_troubletickets',1,'15','ticketstatus','Status',1,2,'',100,7,25,1,'V~M',1,2,'BAS',1,NULL),(13,162,'category','vtiger_troubletickets',1,'15','ticketcategories','Category',1,2,'',100,10,25,1,'V~O',1,NULL,'BAS',1,NULL),(13,163,'update_log','vtiger_troubletickets',1,'19','update_log','Update History',1,0,'',100,11,25,3,'V~O',1,NULL,'BAS',0,NULL),(13,164,'hours','vtiger_troubletickets',1,'7','hours','Hours',1,2,'',100,9,25,1,'N~O',1,NULL,'BAS',1,'This gives the estimated hours for the Ticket.<br>When the same ticket is added to a Service Contract,based on the Tracking Unit of the Service Contract,Used units is updated whenever a ticket is Closed.'),(13,165,'days','vtiger_troubletickets',1,'1','days','Days',1,2,'',100,10,25,1,'I~O',1,NULL,'BAS',1,'This gives the estimated days for the Ticket.<br>When the same ticket is added to a Service Contract,based on the Tracking Unit of the Service Contract,Used units is updated whenever a ticket is Closed.'),(13,166,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,9,25,2,'DT~O',3,NULL,'BAS',0,NULL),(13,167,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,12,25,2,'DT~O',3,NULL,'BAS',0,NULL),(13,168,'from_portal','vtiger_troubletickets',1,'56','from_portal','From Portal',1,0,'',100,13,25,3,'C~O',3,NULL,'BAS',0,NULL),(13,169,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,16,25,3,'V~O',3,NULL,'BAS',0,NULL),(13,170,'title','vtiger_troubletickets',1,'22','ticket_title','Title',1,0,'',100,1,25,1,'V~M',0,1,'BAS',1,NULL),(13,171,'description','vtiger_crmentity',1,'19','description','Description',1,2,'',100,1,28,1,'V~O',2,4,'BAS',1,NULL),(13,172,'solution','vtiger_troubletickets',1,'19','solution','Solution',1,0,'',100,1,29,1,'V~O',3,NULL,'BAS',2,NULL),(13,173,'comments','vtiger_ticketcomments',1,'19','comments','Add Comment',1,0,'',100,1,30,1,'V~O',3,NULL,'BAS',0,NULL),(14,174,'productname','vtiger_products',1,'2','productname','Product Name',1,0,'',100,1,31,1,'V~M',0,1,'BAS',1,NULL),(14,175,'product_no','vtiger_products',1,'4','product_no','Product No',1,0,'',100,2,31,1,'V~O',3,NULL,'BAS',0,NULL),(14,176,'productcode','vtiger_products',1,'1','productcode','Part Number',1,2,'',100,4,31,1,'V~O',1,NULL,'BAS',1,NULL),(14,177,'discontinued','vtiger_products',1,'56','discontinued','Product Active',1,2,'1',100,3,31,1,'C~O',2,2,'BAS',1,NULL),(14,178,'manufacturer','vtiger_products',1,'15','manufacturer','Manufacturer',1,2,'',100,6,31,1,'V~O',1,NULL,'BAS',1,NULL),(14,179,'productcategory','vtiger_products',1,'15','productcategory','Product Category',1,2,'',100,6,31,1,'V~O',1,NULL,'BAS',1,NULL),(14,180,'sales_start_date','vtiger_products',1,'5','sales_start_date','Sales Start Date',1,2,'',100,5,31,1,'D~O',1,NULL,'BAS',1,NULL),(14,181,'sales_end_date','vtiger_products',1,'5','sales_end_date','Sales End Date',1,2,'',100,8,31,1,'D~O~OTH~GE~sales_start_date~Sales Start Date',1,NULL,'BAS',1,NULL),(14,182,'start_date','vtiger_products',1,'5','start_date','Support Start Date',1,2,'',100,7,31,1,'D~O',1,NULL,'BAS',1,NULL),(14,183,'expiry_date','vtiger_products',1,'5','expiry_date','Support Expiry Date',1,2,'',100,10,31,1,'D~O~OTH~GE~start_date~Start Date',1,NULL,'BAS',1,NULL),(14,184,'website','vtiger_products',1,'17','website','Website',1,2,'',100,14,31,1,'V~O',1,NULL,'BAS',1,NULL),(14,185,'vendor_id','vtiger_products',1,'75','vendor_id','Vendor Name',1,2,'',100,13,31,1,'I~O',1,NULL,'BAS',1,NULL),(14,186,'mfr_part_no','vtiger_products',1,'1','mfr_part_no','Mfr PartNo',1,2,'',100,16,31,1,'V~O',1,NULL,'BAS',1,NULL),(14,187,'vendor_part_no','vtiger_products',1,'1','vendor_part_no','Vendor PartNo',1,2,'',100,15,31,1,'V~O',1,NULL,'BAS',1,NULL),(14,188,'serialno','vtiger_products',1,'1','serial_no','Serial No',1,2,'',100,18,31,1,'V~O',1,NULL,'BAS',1,NULL),(14,189,'productsheet','vtiger_products',1,'1','productsheet','Product Sheet',1,2,'',100,17,31,1,'V~O',1,NULL,'BAS',1,NULL),(14,190,'glacct','vtiger_products',1,'15','glacct','GL Account',1,2,'',100,20,31,1,'V~O',1,NULL,'BAS',1,NULL),(14,191,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,19,31,2,'DT~O',3,NULL,'BAS',0,NULL),(14,192,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,21,31,2,'DT~O',3,NULL,'BAS',0,NULL),(14,193,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,22,31,3,'V~O',3,NULL,'BAS',0,NULL),(14,194,'unit_price','vtiger_products',1,'72','unit_price','Unit Price',1,2,'',100,1,32,1,'N~O',2,3,'BAS',2,NULL),(14,195,'commissionrate','vtiger_products',1,'9','commissionrate','Commission Rate',1,2,'',100,2,32,1,'N~O~3,2',1,NULL,'BAS',1,NULL),(14,196,'taxclass','vtiger_products',1,'83','taxclass','Tax Class',1,2,'',100,4,32,1,'V~O',3,NULL,'BAS',1,NULL),(14,197,'usageunit','vtiger_products',1,'15','usageunit','Usage Unit',1,2,'',100,1,33,1,'V~O',1,NULL,'ADV',1,NULL),(14,198,'qty_per_unit','vtiger_products',1,'1','qty_per_unit','Qty/Unit',1,2,'',100,2,33,1,'N~O',1,NULL,'ADV',1,NULL),(14,199,'qtyinstock','vtiger_products',1,'1','qtyinstock','Qty In Stock',1,2,'',100,3,33,1,'NN~O',0,4,'ADV',1,NULL),(14,200,'reorderlevel','vtiger_products',1,'1','reorderlevel','Reorder Level',1,2,'',100,4,33,1,'I~O',1,NULL,'ADV',1,NULL),(14,201,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Handler',1,0,'',100,5,33,1,'V~M',0,5,'BAS',1,NULL),(14,202,'qtyindemand','vtiger_products',1,'1','qtyindemand','Qty In Demand',1,2,'',100,6,33,1,'I~O',1,NULL,'ADV',1,NULL),(14,203,'imagename','vtiger_products',1,'69m','imagename','Product Image',1,2,'',100,1,35,1,'V~O',3,NULL,'ADV',0,NULL),(14,204,'description','vtiger_crmentity',1,'19','description','Description',1,2,'',100,1,36,1,'V~O',1,NULL,'BAS',1,NULL),(8,205,'title','vtiger_notes',1,'2','notes_title','Title',1,0,'',100,1,17,1,'V~M',0,1,'BAS',1,NULL),(8,206,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,5,17,2,'DT~O',3,NULL,'BAS',0,NULL),(8,207,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,6,17,2,'DT~O',3,NULL,'BAS',0,NULL),(8,208,'filename','vtiger_notes',1,'28','filename','File Name',1,2,'',100,3,18,1,'V~O',3,NULL,'BAS',0,NULL),(8,209,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,0,'',100,4,17,1,'V~M',0,3,'BAS',1,NULL),(8,210,'notecontent','vtiger_notes',1,'19','notecontent','Note',1,2,'',100,1,84,1,'V~O',1,NULL,'BAS',1,NULL),(8,211,'filetype','vtiger_notes',1,'1','filetype','File Type',1,2,'',100,5,18,2,'V~O',3,0,'BAS',0,NULL),(8,212,'filesize','vtiger_notes',1,'1','filesize','File Size',1,2,'',100,4,18,2,'I~O',3,0,'BAS',0,NULL),(8,213,'filelocationtype','vtiger_notes',1,'27','filelocationtype','Download Type',1,0,'',100,1,18,1,'V~O',3,0,'BAS',0,NULL),(8,214,'fileversion','vtiger_notes',1,'1','fileversion','Version',1,2,'',100,6,18,1,'V~O',1,0,'BAS',0,NULL),(8,215,'filestatus','vtiger_notes',1,'56','filestatus','Active',1,2,'1',100,2,18,1,'V~O',1,0,'BAS',0,NULL),(8,216,'filedownloadcount','vtiger_notes',1,'1','filedownloadcount','Download Count',1,2,'',100,7,18,2,'I~O',3,0,'BAS',0,NULL),(8,217,'folderid','vtiger_notes',1,'26','folderid','Folder Name',1,2,'',100,2,17,1,'V~O',2,2,'BAS',1,NULL),(8,218,'note_no','vtiger_notes',1,'4','note_no','Document No',1,0,'',100,3,17,1,'V~O',3,NULL,'BAS',0,NULL),(8,219,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,12,17,3,'V~O',3,NULL,'BAS',0,NULL),(10,220,'date_start','vtiger_activity',1,'6','date_start','Date & Time Sent',1,0,'',100,1,21,1,'DT~M~time_start~Time Start',1,NULL,'BAS',1,NULL),(10,221,'semodule','vtiger_activity',1,'2','parent_type','Sales Enity Module',1,0,'',100,2,21,3,'',1,NULL,'BAS',1,NULL),(10,222,'activitytype','vtiger_activity',1,'2','activitytype','Activtiy Type',1,0,'',100,3,21,3,'V~O',1,NULL,'BAS',1,NULL),(10,223,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,0,'',100,5,21,1,'V~M',1,NULL,'BAS',1,NULL),(10,224,'subject','vtiger_activity',1,'2','subject','Subject',1,0,'',100,1,23,1,'V~M',1,NULL,'BAS',1,NULL),(10,225,'name','vtiger_attachments',1,'61','filename','Attachment',1,0,'',100,2,23,1,'V~O',1,NULL,'BAS',1,NULL),(10,226,'description','vtiger_crmentity',1,'19','description','Description',1,0,'',100,1,24,1,'V~O',1,NULL,'BAS',1,NULL),(10,227,'time_start','vtiger_activity',1,'2','time_start','Time Start',1,0,'',100,9,23,1,'T~O',1,NULL,'BAS',1,NULL),(10,228,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,10,22,1,'DT~O',3,NULL,'BAS',0,NULL),(10,229,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,11,21,2,'DT~O',3,NULL,'BAS',0,NULL),(10,230,'access_count','vtiger_email_track',1,'7','access_count','Access Count',1,0,'0',100,6,21,3,'I~O',1,NULL,'BAS',0,NULL),(10,231,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,12,21,3,'V~O',3,NULL,'BAS',0,NULL),(9,232,'subject','vtiger_activity',1,'2','subject','Subject',1,0,'',100,1,19,1,'V~M',0,1,'BAS',1,NULL),(9,233,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,0,'',100,2,19,1,'V~M',0,4,'BAS',1,NULL),(9,234,'date_start','vtiger_activity',1,'6','date_start','Start Date & Time',1,0,'',100,3,19,1,'DT~M~time_start',0,2,'BAS',1,NULL),(9,235,'time_start','vtiger_activity',1,'2','time_start','Time Start',1,0,'',100,4,19,3,'T~O',1,NULL,'BAS',1,NULL),(9,236,'time_end','vtiger_activity',1,'2','time_end','End Time',1,0,'',100,4,19,3,'T~O',1,NULL,'BAS',1,NULL),(9,237,'due_date','vtiger_activity',1,'23','due_date','Due Date',1,0,'',100,5,19,1,'D~M~OTH~GE~date_start~Start Date & Time',1,NULL,'BAS',1,NULL),(9,238,'crmid','vtiger_seactivityrel',1,'66','parent_id','Related To',1,0,'',100,7,19,1,'I~O',1,NULL,'BAS',1,NULL),(9,239,'contactid','vtiger_cntactivityrel',1,'57','contact_id','Contact Name',1,0,'',100,8,19,1,'I~O',1,NULL,'BAS',1,NULL),(9,240,'status','vtiger_activity',1,'15','taskstatus','Status',1,0,'',100,8,19,1,'V~M',0,3,'BAS',1,NULL),(9,241,'eventstatus','vtiger_activity',1,'15','eventstatus','Status',1,0,'',100,9,19,3,'V~O',1,NULL,'BAS',1,NULL),(9,242,'priority','vtiger_activity',1,'15','taskpriority','Priority',1,0,'',100,10,19,1,'V~O',1,NULL,'BAS',1,NULL),(9,243,'sendnotification','vtiger_activity',1,'56','sendnotification','Send Notification',1,0,'',100,11,19,1,'C~O',1,NULL,'BAS',1,NULL),(9,244,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,14,19,2,'DT~O',3,NULL,'BAS',0,NULL),(9,245,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,15,19,2,'DT~O',3,NULL,'BAS',0,NULL),(9,246,'activitytype','vtiger_activity',1,'15','activitytype','Activity Type',1,0,'',100,16,19,3,'V~O',1,NULL,'BAS',1,NULL),(9,247,'visibility','vtiger_activity',1,'16','visibility','Visibility',1,0,'',100,17,19,3,'V~O',1,NULL,'BAS',1,NULL),(9,248,'description','vtiger_crmentity',1,'19','description','Description',1,0,'',100,1,20,1,'V~O',1,NULL,'BAS',1,NULL),(9,249,'duration_hours','vtiger_activity',1,'63','duration_hours','Duration',1,0,'',100,17,19,3,'I~O',1,NULL,'BAS',1,NULL),(9,250,'duration_minutes','vtiger_activity',1,'16','duration_minutes','Duration Minutes',1,0,'',100,18,19,3,'I~O',1,NULL,'BAS',1,NULL),(9,251,'location','vtiger_activity',1,'1','location','Location',1,0,'',100,19,19,3,'V~O',1,NULL,'BAS',1,NULL),(9,252,'reminder_time','vtiger_activity_reminder',1,'30','reminder_time','Send Reminder',1,0,'',100,1,19,3,'I~O',1,NULL,'BAS',1,NULL),(9,253,'recurringtype','vtiger_activity',1,'16','recurringtype','Recurrence',1,0,'',100,6,19,3,'O~O',1,NULL,'BAS',1,NULL),(9,254,'notime','vtiger_activity',1,'56','notime','No Time',1,0,'',100,20,19,3,'C~O',1,NULL,'BAS',1,NULL),(9,255,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,22,19,3,'V~O',3,NULL,'BAS',0,NULL),(16,256,'subject','vtiger_activity',1,'2','subject','Subject',1,0,'',100,1,41,1,'V~M',0,1,'BAS',1,NULL),(16,257,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,0,'',100,2,41,1,'V~M',0,6,'BAS',1,NULL),(16,258,'date_start','vtiger_activity',1,'6','date_start','Start Date & Time',1,0,'',100,3,41,1,'DT~M~time_start',0,2,'BAS',1,NULL),(16,259,'time_start','vtiger_activity',1,'2','time_start','Time Start',1,0,'',100,4,41,3,'T~M',1,NULL,'BAS',1,NULL),(16,260,'due_date','vtiger_activity',1,'23','due_date','End Date',1,0,'',100,5,41,1,'D~M~OTH~GE~date_start~Start Date & Time',0,5,'BAS',1,NULL),(16,261,'time_end','vtiger_activity',1,'2','time_end','End Time',1,0,'',100,5,41,3,'T~M',1,NULL,'BAS',1,NULL),(16,262,'recurringtype','vtiger_activity',1,'16','recurringtype','Recurrence',1,0,'',100,6,41,1,'O~O',1,NULL,'BAS',1,NULL),(16,263,'duration_hours','vtiger_activity',1,'63','duration_hours','Duration',1,0,'',100,7,41,1,'I~O',1,NULL,'BAS',1,NULL),(16,264,'duration_minutes','vtiger_activity',1,'16','duration_minutes','Duration Minutes',1,0,'',100,8,41,3,'I~O',1,NULL,'BAS',1,NULL),(16,265,'crmid','vtiger_seactivityrel',1,'66','parent_id','Related To',1,0,'',100,9,41,1,'I~O',1,NULL,'BAS',1,NULL),(16,266,'eventstatus','vtiger_activity',1,'15','eventstatus','Status',1,0,'',100,10,41,1,'V~M',0,3,'BAS',1,NULL),(16,267,'sendnotification','vtiger_activity',1,'56','sendnotification','Send Notification',1,0,'',100,11,41,1,'C~O',1,NULL,'BAS',1,NULL),(16,268,'activitytype','vtiger_activity',1,'15','activitytype','Activity Type',1,0,'',100,12,41,1,'V~M',0,4,'BAS',1,NULL),(16,269,'location','vtiger_activity',1,'1','location','Location',1,0,'',100,13,41,1,'V~O',1,NULL,'BAS',1,NULL),(16,270,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,14,41,2,'DT~O',3,NULL,'BAS',0,NULL),(16,271,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,15,41,2,'DT~O',3,NULL,'BAS',0,NULL),(16,272,'priority','vtiger_activity',1,'15','taskpriority','Priority',1,0,'',100,16,41,1,'V~O',1,NULL,'BAS',1,NULL),(16,273,'notime','vtiger_activity',1,'56','notime','No Time',1,0,'',100,17,41,1,'C~O',1,NULL,'BAS',1,NULL),(16,274,'visibility','vtiger_activity',1,'16','visibility','Visibility',1,0,'',100,18,41,1,'V~O',1,NULL,'BAS',1,NULL),(16,275,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,22,41,3,'V~O',3,NULL,'BAS',0,NULL),(16,276,'description','vtiger_crmentity',1,'19','description','Description',1,0,'',100,1,41,1,'V~O',1,NULL,'BAS',1,NULL),(16,277,'reminder_time','vtiger_activity_reminder',1,'30','reminder_time','Send Reminder',1,0,'',100,1,40,1,'I~O',1,NULL,'BAS',1,NULL),(16,278,'contactid','vtiger_cntactivityrel',1,'57','contact_id','Contact Name',1,0,'',100,1,19,1,'I~O',1,NULL,'BAS',1,NULL),(15,279,'product_id','vtiger_faq',1,'59','product_id','Product Name',1,2,'',100,1,37,1,'I~O',3,NULL,'BAS',1,NULL),(15,280,'faq_no','vtiger_faq',1,'4','faq_no','Faq No',1,0,'',100,2,37,1,'V~O',3,NULL,'BAS',0,NULL),(15,281,'category','vtiger_faq',1,'15','faqcategories','Category',1,2,'',100,4,37,1,'V~O',3,NULL,'BAS',1,NULL),(15,282,'status','vtiger_faq',1,'15','faqstatus','Status',1,2,'',100,3,37,1,'V~M',3,NULL,'BAS',1,NULL),(15,283,'question','vtiger_faq',1,'20','question','Question',1,2,'',100,7,37,1,'V~M',3,NULL,'BAS',1,NULL),(15,284,'answer','vtiger_faq',1,'20','faq_answer','Answer',1,2,'',100,8,37,1,'V~M',3,NULL,'BAS',1,NULL),(15,285,'comments','vtiger_faqcomments',1,'19','comments','Add Comment',1,0,'',100,1,38,1,'V~O',3,NULL,'BAS',0,NULL),(15,286,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,5,37,2,'DT~O',3,NULL,'BAS',0,NULL),(15,287,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,6,37,2,'DT~O',3,NULL,'BAS',0,NULL),(15,288,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,7,37,3,'V~O',3,NULL,'BAS',0,NULL),(18,289,'vendorname','vtiger_vendor',1,'2','vendorname','Vendor Name',1,0,'',100,1,42,1,'V~M',0,1,'BAS',1,NULL),(18,290,'vendor_no','vtiger_vendor',1,'4','vendor_no','Vendor No',1,0,'',100,2,42,1,'V~O',3,NULL,'BAS',0,NULL),(18,291,'phone','vtiger_vendor',1,'11','phone','Phone',1,2,'',100,4,42,1,'V~O',2,2,'BAS',1,NULL),(18,292,'email','vtiger_vendor',1,'13','email','Email',1,2,'',100,3,42,1,'E~O',2,3,'BAS',1,NULL),(18,293,'website','vtiger_vendor',1,'17','website','Website',1,2,'',100,6,42,1,'V~O',1,NULL,'BAS',1,NULL),(18,294,'glacct','vtiger_vendor',1,'15','glacct','GL Account',1,2,'',100,5,42,1,'V~O',1,NULL,'BAS',1,NULL),(18,295,'category','vtiger_vendor',1,'1','category','Category',1,2,'',100,8,42,1,'V~O',1,NULL,'BAS',1,NULL),(18,296,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,7,42,2,'DT~O',3,NULL,'BAS',0,NULL),(18,297,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,9,42,2,'DT~O',3,NULL,'BAS',0,NULL),(18,298,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,12,42,3,'V~O',3,NULL,'BAS',0,NULL),(18,299,'street','vtiger_vendor',1,'21','street','Street',1,2,'',100,1,44,1,'V~O',1,NULL,'ADV',1,NULL),(18,300,'pobox','vtiger_vendor',1,'1','pobox','Po Box',1,2,'',100,2,44,1,'V~O',1,NULL,'ADV',1,NULL),(18,301,'city','vtiger_vendor',1,'1','city','City',1,2,'',100,3,44,1,'V~O',1,NULL,'ADV',1,NULL),(18,302,'state','vtiger_vendor',1,'1','state','State',1,2,'',100,4,44,1,'V~O',1,NULL,'ADV',1,NULL),(18,303,'postalcode','vtiger_vendor',1,'1','postalcode','Postal Code',1,2,'',100,5,44,1,'V~O',1,NULL,'ADV',1,NULL),(18,304,'country','vtiger_vendor',1,'1','country','Country',1,2,'',100,6,44,1,'V~O',1,NULL,'ADV',1,NULL),(18,305,'description','vtiger_crmentity',1,'19','description','Description',1,2,'',100,1,45,1,'V~O',1,NULL,'ADV',1,NULL),(19,306,'bookname','vtiger_pricebook',1,'2','bookname','Price Book Name',1,0,'',100,1,46,1,'V~M',0,1,'BAS',1,NULL),(19,307,'pricebook_no','vtiger_pricebook',1,'4','pricebook_no','PriceBook No',1,0,'',100,3,46,1,'V~O',3,NULL,'BAS',0,NULL),(19,308,'active','vtiger_pricebook',1,'56','active','Active',1,2,'1',100,2,46,1,'C~O',2,2,'BAS',1,NULL),(19,309,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,4,46,2,'DT~O',3,NULL,'BAS',0,NULL),(19,310,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,5,46,2,'DT~O',3,NULL,'BAS',0,NULL),(19,311,'currency_id','vtiger_pricebook',1,'117','currency_id','Currency',1,0,'',100,5,46,1,'I~M',0,3,'BAS',0,NULL),(19,312,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,7,46,3,'V~O',3,NULL,'BAS',0,NULL),(19,313,'description','vtiger_crmentity',1,'19','description','Description',1,2,'',100,1,48,1,'V~O',1,NULL,'BAS',1,NULL),(20,314,'quote_no','vtiger_quotes',1,'4','quote_no','Quote No',1,0,'',100,3,49,1,'V~O',3,NULL,'BAS',0,NULL),(20,315,'subject','vtiger_quotes',1,'2','subject','Subject',1,0,'',100,1,49,1,'V~M',1,NULL,'BAS',1,NULL),(20,316,'potentialid','vtiger_quotes',1,'76','potential_id','Potential Name',1,2,'',100,2,49,1,'I~O',3,NULL,'BAS',1,NULL),(20,317,'quotestage','vtiger_quotes',1,'15','quotestage','Quote Stage',1,2,'',100,4,49,1,'V~M',3,NULL,'BAS',1,NULL),(20,318,'validtill','vtiger_quotes',1,'5','validtill','Valid Till',1,2,'',100,5,49,1,'D~O',3,NULL,'BAS',1,NULL),(20,319,'contactid','vtiger_quotes',1,'57','contact_id','Contact Name',1,2,'',100,6,49,1,'V~O',3,NULL,'BAS',1,NULL),(20,320,'carrier','vtiger_quotes',1,'15','carrier','Carrier',1,2,'',100,8,49,1,'V~O',3,NULL,'BAS',1,NULL),(20,321,'subtotal','vtiger_quotes',1,'72','hdnSubTotal','Sub Total',1,2,'',100,9,49,3,'N~O',3,NULL,'BAS',1,NULL),(20,322,'shipping','vtiger_quotes',1,'1','shipping','Shipping',1,2,'',100,10,49,1,'V~O',3,NULL,'BAS',1,NULL),(20,323,'inventorymanager','vtiger_quotes',1,'77','assigned_user_id1','Inventory Manager',1,2,'',100,11,49,1,'I~O',3,NULL,'BAS',1,NULL),(20,324,'adjustment','vtiger_quotes',1,'72','txtAdjustment','Adjustment',1,2,'',100,20,49,3,'NN~O',3,NULL,'BAS',1,NULL),(20,325,'total','vtiger_quotes',1,'72','hdnGrandTotal','Total',1,2,'',100,14,49,3,'N~O',3,NULL,'BAS',1,NULL),(20,326,'taxtype','vtiger_quotes',1,'16','hdnTaxType','Tax Type',1,2,'',100,14,49,3,'V~O',3,NULL,'BAS',1,NULL),(20,327,'discount_percent','vtiger_quotes',1,'1','hdnDiscountPercent','Discount Percent',1,2,'',100,14,49,3,'N~O',3,NULL,'BAS',1,NULL),(20,328,'discount_amount','vtiger_quotes',1,'72','hdnDiscountAmount','Discount Amount',1,2,'',100,14,49,3,'N~O',3,NULL,'BAS',1,NULL),(20,329,'s_h_amount','vtiger_quotes',1,'72','hdnS_H_Amount','S&H Amount',1,2,'',100,14,49,3,'N~O',3,NULL,'BAS',1,NULL),(20,330,'accountid','vtiger_quotes',1,'73','account_id','Account Name',1,2,'',100,16,49,1,'I~M',3,NULL,'BAS',1,NULL),(20,331,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,0,'',100,17,49,1,'V~M',3,NULL,'BAS',1,NULL),(20,332,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,18,49,2,'DT~O',3,NULL,'BAS',0,NULL),(20,333,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,19,49,2,'DT~O',3,NULL,'BAS',0,NULL),(20,334,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,22,49,3,'V~O',3,NULL,'BAS',0,NULL),(20,335,'currency_id','vtiger_quotes',1,'117','currency_id','Currency',1,2,'1',100,20,49,3,'I~O',3,NULL,'BAS',1,NULL),(20,336,'conversion_rate','vtiger_quotes',1,'1','conversion_rate','Conversion Rate',1,2,'1',100,21,49,3,'N~O',3,NULL,'BAS',1,NULL),(20,337,'bill_street','vtiger_quotesbillads',1,'24','bill_street','Billing Address',1,2,'',100,1,51,1,'V~M',3,NULL,'BAS',1,NULL),(20,338,'ship_street','vtiger_quotesshipads',1,'24','ship_street','Shipping Address',1,2,'',100,2,51,1,'V~M',3,NULL,'BAS',1,NULL),(20,339,'bill_city','vtiger_quotesbillads',1,'1','bill_city','Billing City',1,2,'',100,5,51,1,'V~O',3,NULL,'BAS',1,NULL),(20,340,'ship_city','vtiger_quotesshipads',1,'1','ship_city','Shipping City',1,2,'',100,6,51,1,'V~O',3,NULL,'BAS',1,NULL),(20,341,'bill_state','vtiger_quotesbillads',1,'1','bill_state','Billing State',1,2,'',100,7,51,1,'V~O',3,NULL,'BAS',1,NULL),(20,342,'ship_state','vtiger_quotesshipads',1,'1','ship_state','Shipping State',1,2,'',100,8,51,1,'V~O',3,NULL,'BAS',1,NULL),(20,343,'bill_code','vtiger_quotesbillads',1,'1','bill_code','Billing Code',1,2,'',100,9,51,1,'V~O',3,NULL,'BAS',1,NULL),(20,344,'ship_code','vtiger_quotesshipads',1,'1','ship_code','Shipping Code',1,2,'',100,10,51,1,'V~O',3,NULL,'BAS',1,NULL),(20,345,'bill_country','vtiger_quotesbillads',1,'1','bill_country','Billing Country',1,2,'',100,11,51,1,'V~O',3,NULL,'BAS',1,NULL),(20,346,'ship_country','vtiger_quotesshipads',1,'1','ship_country','Shipping Country',1,2,'',100,12,51,1,'V~O',3,NULL,'BAS',1,NULL),(20,347,'bill_pobox','vtiger_quotesbillads',1,'1','bill_pobox','Billing Po Box',1,2,'',100,3,51,1,'V~O',3,NULL,'BAS',1,NULL),(20,348,'ship_pobox','vtiger_quotesshipads',1,'1','ship_pobox','Shipping Po Box',1,2,'',100,4,51,1,'V~O',3,NULL,'BAS',1,NULL),(20,349,'description','vtiger_crmentity',1,'19','description','Description',1,2,'',100,1,54,1,'V~O',3,NULL,'ADV',1,NULL),(20,350,'terms_conditions','vtiger_quotes',1,'19','terms_conditions','Terms & Conditions',1,2,'',100,1,53,1,'V~O',3,NULL,'ADV',1,NULL),(21,351,'purchaseorder_no','vtiger_purchaseorder',1,'4','purchaseorder_no','PurchaseOrder No',1,0,'',100,2,55,1,'V~O',3,NULL,'BAS',0,NULL),(21,352,'subject','vtiger_purchaseorder',1,'2','subject','Subject',1,0,'',100,1,55,1,'V~M',3,NULL,'BAS',1,NULL),(21,353,'vendorid','vtiger_purchaseorder',1,'81','vendor_id','Vendor Name',1,0,'',100,3,55,1,'I~M',3,NULL,'BAS',1,NULL),(21,354,'requisition_no','vtiger_purchaseorder',1,'1','requisition_no','Requisition No',1,2,'',100,4,55,1,'V~O',3,NULL,'BAS',1,NULL),(21,355,'tracking_no','vtiger_purchaseorder',1,'1','tracking_no','Tracking Number',1,2,'',100,5,55,1,'V~O',3,NULL,'BAS',1,NULL),(21,356,'contactid','vtiger_purchaseorder',1,'57','contact_id','Contact Name',1,2,'',100,6,55,1,'I~O',3,NULL,'BAS',1,NULL),(21,357,'duedate','vtiger_purchaseorder',1,'5','duedate','Due Date',1,2,'',100,7,55,1,'D~O',3,NULL,'BAS',1,NULL),(21,358,'carrier','vtiger_purchaseorder',1,'15','carrier','Carrier',1,2,'',100,8,55,1,'V~O',3,NULL,'BAS',1,NULL),(21,359,'adjustment','vtiger_purchaseorder',1,'72','txtAdjustment','Adjustment',1,2,'',100,10,55,3,'NN~O',3,NULL,'BAS',1,NULL),(21,360,'salescommission','vtiger_purchaseorder',1,'1','salescommission','Sales Commission',1,2,'',100,11,55,1,'N~O',3,NULL,'BAS',1,NULL),(21,361,'exciseduty','vtiger_purchaseorder',1,'1','exciseduty','Excise Duty',1,2,'',100,12,55,1,'N~O',3,NULL,'BAS',1,NULL),(21,362,'total','vtiger_purchaseorder',1,'72','hdnGrandTotal','Total',1,2,'',100,13,55,3,'N~O',3,NULL,'BAS',1,NULL),(21,363,'subtotal','vtiger_purchaseorder',1,'72','hdnSubTotal','Sub Total',1,2,'',100,14,55,3,'N~O',3,NULL,'BAS',1,NULL),(21,364,'taxtype','vtiger_purchaseorder',1,'16','hdnTaxType','Tax Type',1,2,'',100,14,55,3,'V~O',3,NULL,'BAS',1,NULL),(21,365,'discount_percent','vtiger_purchaseorder',1,'1','hdnDiscountPercent','Discount Percent',1,2,'',100,14,55,3,'N~O',3,NULL,'BAS',1,NULL),(21,366,'discount_amount','vtiger_purchaseorder',1,'72','hdnDiscountAmount','Discount Amount',1,0,'',100,14,55,3,'N~O',3,NULL,'BAS',1,NULL),(21,367,'s_h_amount','vtiger_purchaseorder',1,'72','hdnS_H_Amount','S&H Amount',1,2,'',100,14,55,3,'N~O',3,NULL,'BAS',1,NULL),(21,368,'postatus','vtiger_purchaseorder',1,'15','postatus','Status',1,2,'',100,15,55,1,'V~M',3,NULL,'BAS',1,NULL),(21,369,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,0,'',100,16,55,1,'V~M',3,NULL,'BAS',1,NULL),(21,370,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,17,55,2,'DT~O',3,NULL,'BAS',0,NULL),(21,371,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,18,55,2,'DT~O',3,NULL,'BAS',0,NULL),(21,372,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,22,55,3,'V~O',3,NULL,'BAS',0,NULL),(21,373,'currency_id','vtiger_purchaseorder',1,'117','currency_id','Currency',1,2,'1',100,19,55,3,'I~O',3,NULL,'BAS',1,NULL),(21,374,'conversion_rate','vtiger_purchaseorder',1,'1','conversion_rate','Conversion Rate',1,2,'1',100,20,55,3,'N~O',3,NULL,'BAS',1,NULL),(21,375,'bill_street','vtiger_pobillads',1,'24','bill_street','Billing Address',1,2,'',100,1,57,1,'V~M',3,NULL,'BAS',1,NULL),(21,376,'ship_street','vtiger_poshipads',1,'24','ship_street','Shipping Address',1,2,'',100,2,57,1,'V~M',3,NULL,'BAS',1,NULL),(21,377,'bill_city','vtiger_pobillads',1,'1','bill_city','Billing City',1,2,'',100,5,57,1,'V~O',3,NULL,'BAS',1,NULL),(21,378,'ship_city','vtiger_poshipads',1,'1','ship_city','Shipping City',1,2,'',100,6,57,1,'V~O',3,NULL,'BAS',1,NULL),(21,379,'bill_state','vtiger_pobillads',1,'1','bill_state','Billing State',1,2,'',100,7,57,1,'V~O',3,NULL,'BAS',1,NULL),(21,380,'ship_state','vtiger_poshipads',1,'1','ship_state','Shipping State',1,2,'',100,8,57,1,'V~O',3,NULL,'BAS',1,NULL),(21,381,'bill_code','vtiger_pobillads',1,'1','bill_code','Billing Code',1,2,'',100,9,57,1,'V~O',3,NULL,'BAS',1,NULL),(21,382,'ship_code','vtiger_poshipads',1,'1','ship_code','Shipping Code',1,2,'',100,10,57,1,'V~O',3,NULL,'BAS',1,NULL),(21,383,'bill_country','vtiger_pobillads',1,'1','bill_country','Billing Country',1,2,'',100,11,57,1,'V~O',3,NULL,'BAS',1,NULL),(21,384,'ship_country','vtiger_poshipads',1,'1','ship_country','Shipping Country',1,2,'',100,12,57,1,'V~O',3,NULL,'BAS',1,NULL),(21,385,'bill_pobox','vtiger_pobillads',1,'1','bill_pobox','Billing Po Box',1,2,'',100,3,57,1,'V~O',3,NULL,'BAS',1,NULL),(21,386,'ship_pobox','vtiger_poshipads',1,'1','ship_pobox','Shipping Po Box',1,2,'',100,4,57,1,'V~O',3,NULL,'BAS',1,NULL),(21,387,'description','vtiger_crmentity',1,'19','description','Description',1,2,'',100,1,60,1,'V~O',3,NULL,'ADV',1,NULL),(21,388,'terms_conditions','vtiger_purchaseorder',1,'19','terms_conditions','Terms & Conditions',1,2,'',100,1,59,1,'V~O',3,NULL,'ADV',1,NULL),(22,389,'salesorder_no','vtiger_salesorder',1,'4','salesorder_no','SalesOrder No',1,0,'',100,4,61,1,'V~O',3,NULL,'BAS',0,NULL),(22,390,'subject','vtiger_salesorder',1,'2','subject','Subject',1,0,'',100,1,61,1,'V~M',3,NULL,'BAS',1,NULL),(22,391,'potentialid','vtiger_salesorder',1,'76','potential_id','Potential Name',1,2,'',100,2,61,1,'I~O',3,NULL,'BAS',1,NULL),(22,392,'customerno','vtiger_salesorder',1,'1','customerno','Customer No',1,2,'',100,3,61,1,'V~O',3,NULL,'BAS',1,NULL),(22,393,'quoteid','vtiger_salesorder',1,'78','quote_id','Quote Name',1,2,'',100,5,61,1,'I~O',3,NULL,'BAS',0,NULL),(22,394,'purchaseorder','vtiger_salesorder',1,'1','vtiger_purchaseorder','Purchase Order',1,2,'',100,5,61,1,'V~O',3,NULL,'BAS',1,NULL),(22,395,'contactid','vtiger_salesorder',1,'57','contact_id','Contact Name',1,2,'',100,6,61,1,'I~O',3,NULL,'BAS',1,NULL),(22,396,'duedate','vtiger_salesorder',1,'5','duedate','Due Date',1,2,'',100,8,61,1,'D~O',3,NULL,'BAS',1,NULL),(22,397,'carrier','vtiger_salesorder',1,'15','carrier','Carrier',1,2,'',100,9,61,1,'V~O',3,NULL,'BAS',1,NULL),(22,398,'pending','vtiger_salesorder',1,'1','pending','Pending',1,2,'',100,10,61,1,'V~O',3,NULL,'BAS',1,NULL),(22,399,'sostatus','vtiger_salesorder',1,'15','sostatus','Status',1,2,'',100,11,61,1,'V~M',3,NULL,'BAS',1,NULL),(22,400,'adjustment','vtiger_salesorder',1,'72','txtAdjustment','Adjustment',1,2,'',100,12,61,3,'NN~O',3,NULL,'BAS',1,NULL),(22,401,'salescommission','vtiger_salesorder',1,'1','salescommission','Sales Commission',1,2,'',100,13,61,1,'N~O',3,NULL,'BAS',1,NULL),(22,402,'exciseduty','vtiger_salesorder',1,'1','exciseduty','Excise Duty',1,2,'',100,13,61,1,'N~O',3,NULL,'BAS',1,NULL),(22,403,'total','vtiger_salesorder',1,'72','hdnGrandTotal','Total',1,2,'',100,14,61,3,'N~O',3,NULL,'BAS',1,NULL),(22,404,'subtotal','vtiger_salesorder',1,'72','hdnSubTotal','Sub Total',1,2,'',100,15,61,3,'N~O',3,NULL,'BAS',1,NULL),(22,405,'taxtype','vtiger_salesorder',1,'16','hdnTaxType','Tax Type',1,2,'',100,15,61,3,'V~O',3,NULL,'BAS',1,NULL),(22,406,'discount_percent','vtiger_salesorder',1,'1','hdnDiscountPercent','Discount Percent',1,2,'',100,15,61,3,'N~O',3,NULL,'BAS',1,NULL),(22,407,'discount_amount','vtiger_salesorder',1,'72','hdnDiscountAmount','Discount Amount',1,0,'',100,15,61,3,'N~O',3,NULL,'BAS',1,NULL),(22,408,'s_h_amount','vtiger_salesorder',1,'72','hdnS_H_Amount','S&H Amount',1,2,'',100,15,61,3,'N~O',3,NULL,'BAS',1,NULL),(22,409,'accountid','vtiger_salesorder',1,'73','account_id','Account Name',1,2,'',100,16,61,1,'I~M',3,NULL,'BAS',1,NULL),(22,410,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,0,'',100,17,61,1,'V~M',3,NULL,'BAS',1,NULL),(22,411,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,18,61,2,'DT~O',3,NULL,'BAS',0,NULL),(22,412,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,19,61,2,'DT~O',3,NULL,'BAS',0,NULL),(22,413,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,22,61,3,'V~O',3,NULL,'BAS',0,NULL),(22,414,'currency_id','vtiger_salesorder',1,'117','currency_id','Currency',1,2,'1',100,20,61,3,'I~O',3,NULL,'BAS',1,NULL),(22,415,'conversion_rate','vtiger_salesorder',1,'1','conversion_rate','Conversion Rate',1,2,'1',100,21,61,3,'N~O',3,NULL,'BAS',1,NULL),(22,416,'bill_street','vtiger_sobillads',1,'24','bill_street','Billing Address',1,2,'',100,1,63,1,'V~M',3,NULL,'BAS',1,NULL),(22,417,'ship_street','vtiger_soshipads',1,'24','ship_street','Shipping Address',1,2,'',100,2,63,1,'V~M',3,NULL,'BAS',1,NULL),(22,418,'bill_city','vtiger_sobillads',1,'1','bill_city','Billing City',1,2,'',100,5,63,1,'V~O',3,NULL,'BAS',1,NULL),(22,419,'ship_city','vtiger_soshipads',1,'1','ship_city','Shipping City',1,2,'',100,6,63,1,'V~O',3,NULL,'BAS',1,NULL),(22,420,'bill_state','vtiger_sobillads',1,'1','bill_state','Billing State',1,2,'',100,7,63,1,'V~O',3,NULL,'BAS',1,NULL),(22,421,'ship_state','vtiger_soshipads',1,'1','ship_state','Shipping State',1,2,'',100,8,63,1,'V~O',3,NULL,'BAS',1,NULL),(22,422,'bill_code','vtiger_sobillads',1,'1','bill_code','Billing Code',1,2,'',100,9,63,1,'V~O',3,NULL,'BAS',1,NULL),(22,423,'ship_code','vtiger_soshipads',1,'1','ship_code','Shipping Code',1,2,'',100,10,63,1,'V~O',3,NULL,'BAS',1,NULL),(22,424,'bill_country','vtiger_sobillads',1,'1','bill_country','Billing Country',1,2,'',100,11,63,1,'V~O',3,NULL,'BAS',1,NULL),(22,425,'ship_country','vtiger_soshipads',1,'1','ship_country','Shipping Country',1,2,'',100,12,63,1,'V~O',3,NULL,'BAS',1,NULL),(22,426,'bill_pobox','vtiger_sobillads',1,'1','bill_pobox','Billing Po Box',1,2,'',100,3,63,1,'V~O',3,NULL,'BAS',1,NULL),(22,427,'ship_pobox','vtiger_soshipads',1,'1','ship_pobox','Shipping Po Box',1,2,'',100,4,63,1,'V~O',3,NULL,'BAS',1,NULL),(22,428,'description','vtiger_crmentity',1,'19','description','Description',1,2,'',100,1,66,1,'V~O',3,NULL,'ADV',1,NULL),(22,429,'terms_conditions','vtiger_salesorder',1,'19','terms_conditions','Terms & Conditions',1,2,'',100,1,65,1,'V~O',3,NULL,'ADV',1,NULL),(22,430,'enable_recurring','vtiger_salesorder',1,'56','enable_recurring','Enable Recurring',1,0,'',100,1,85,1,'C~O',3,NULL,'BAS',0,NULL),(22,431,'recurring_frequency','vtiger_invoice_recurring_info',1,'16','recurring_frequency','Frequency',1,0,'',100,2,85,1,'V~O',3,NULL,'BAS',0,NULL),(22,432,'start_period','vtiger_invoice_recurring_info',1,'5','start_period','Start Period',1,0,'',100,3,85,1,'D~O',3,NULL,'BAS',0,NULL),(22,433,'end_period','vtiger_invoice_recurring_info',1,'5','end_period','End Period',1,0,'',100,4,85,1,'D~O~OTH~G~start_period~Start Period',3,NULL,'BAS',0,NULL),(22,434,'payment_duration','vtiger_invoice_recurring_info',1,'16','payment_duration','Payment Duration',1,0,'',100,5,85,1,'V~O',3,NULL,'BAS',0,NULL),(22,435,'invoice_status','vtiger_invoice_recurring_info',1,'15','invoicestatus','Invoice Status',1,0,'',100,6,85,1,'V~M',3,NULL,'BAS',0,NULL),(23,436,'subject','vtiger_invoice',1,'2','subject','Subject',1,0,'',100,1,67,1,'V~M',3,NULL,'BAS',1,NULL),(23,437,'salesorderid','vtiger_invoice',1,'80','salesorder_id','Sales Order',1,2,'',100,2,67,1,'I~O',3,NULL,'BAS',0,NULL),(23,438,'customerno','vtiger_invoice',1,'1','customerno','Customer No',1,2,'',100,3,67,1,'V~O',3,NULL,'BAS',1,NULL),(23,439,'contactid','vtiger_invoice',1,'57','contact_id','Contact Name',1,2,'',100,4,67,1,'I~O',3,NULL,'BAS',1,NULL),(23,440,'invoicedate','vtiger_invoice',1,'5','invoicedate','Invoice Date',1,2,'',100,5,67,1,'D~O',3,NULL,'BAS',1,NULL),(23,441,'duedate','vtiger_invoice',1,'5','duedate','Due Date',1,2,'',100,6,67,1,'D~O',3,NULL,'BAS',1,NULL),(23,442,'purchaseorder','vtiger_invoice',1,'1','vtiger_purchaseorder','Purchase Order',1,2,'',100,8,67,1,'V~O',3,NULL,'BAS',1,NULL),(23,443,'adjustment','vtiger_invoice',1,'72','txtAdjustment','Adjustment',1,2,'',100,9,67,3,'NN~O',3,NULL,'BAS',1,NULL),(23,444,'salescommission','vtiger_invoice',1,'1','salescommission','Sales Commission',1,2,'',10,13,67,1,'N~O',3,NULL,'BAS',1,NULL),(23,445,'exciseduty','vtiger_invoice',1,'1','exciseduty','Excise Duty',1,2,'',100,11,67,1,'N~O',3,NULL,'BAS',1,NULL),(23,446,'subtotal','vtiger_invoice',1,'72','hdnSubTotal','Sub Total',1,2,'',100,12,67,3,'N~O',3,NULL,'BAS',1,NULL),(23,447,'total','vtiger_invoice',1,'72','hdnGrandTotal','Total',1,2,'',100,13,67,3,'N~O',3,NULL,'BAS',1,NULL),(23,448,'taxtype','vtiger_invoice',1,'16','hdnTaxType','Tax Type',1,2,'',100,13,67,3,'V~O',3,NULL,'BAS',1,NULL),(23,449,'discount_percent','vtiger_invoice',1,'1','hdnDiscountPercent','Discount Percent',1,2,'',100,13,67,3,'N~O',3,NULL,'BAS',1,NULL),(23,450,'discount_amount','vtiger_invoice',1,'72','hdnDiscountAmount','Discount Amount',1,2,'',100,13,67,3,'N~O',3,NULL,'BAS',1,NULL),(23,451,'s_h_amount','vtiger_invoice',1,'72','hdnS_H_Amount','S&H Amount',1,2,'',100,14,67,3,'N~O',3,NULL,'BAS',1,NULL),(23,452,'accountid','vtiger_invoice',1,'73','account_id','Account Name',1,2,'',100,14,67,1,'I~M',3,NULL,'BAS',1,NULL),(23,453,'invoicestatus','vtiger_invoice',1,'15','invoicestatus','Status',1,2,'',100,15,67,1,'V~O',3,NULL,'BAS',1,NULL),(23,454,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,0,'',100,16,67,1,'V~M',3,NULL,'BAS',1,NULL),(23,455,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,17,67,2,'DT~O',3,NULL,'BAS',0,NULL),(23,456,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,18,67,2,'DT~O',3,NULL,'BAS',0,NULL),(23,457,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,22,67,3,'V~O',3,NULL,'BAS',0,NULL),(23,458,'currency_id','vtiger_invoice',1,'117','currency_id','Currency',1,2,'1',100,19,67,3,'I~O',3,NULL,'BAS',1,NULL),(23,459,'conversion_rate','vtiger_invoice',1,'1','conversion_rate','Conversion Rate',1,2,'1',100,20,67,3,'N~O',3,NULL,'BAS',1,NULL),(23,460,'bill_street','vtiger_invoicebillads',1,'24','bill_street','Billing Address',1,2,'',100,1,69,1,'V~M',3,NULL,'BAS',1,NULL),(23,461,'ship_street','vtiger_invoiceshipads',1,'24','ship_street','Shipping Address',1,2,'',100,2,69,1,'V~M',3,NULL,'BAS',1,NULL),(23,462,'bill_city','vtiger_invoicebillads',1,'1','bill_city','Billing City',1,2,'',100,5,69,1,'V~O',3,NULL,'BAS',1,NULL),(23,463,'ship_city','vtiger_invoiceshipads',1,'1','ship_city','Shipping City',1,2,'',100,6,69,1,'V~O',3,NULL,'BAS',1,NULL),(23,464,'bill_state','vtiger_invoicebillads',1,'1','bill_state','Billing State',1,2,'',100,7,69,1,'V~O',3,NULL,'BAS',1,NULL),(23,465,'ship_state','vtiger_invoiceshipads',1,'1','ship_state','Shipping State',1,2,'',100,8,69,1,'V~O',3,NULL,'BAS',1,NULL),(23,466,'bill_code','vtiger_invoicebillads',1,'1','bill_code','Billing Code',1,2,'',100,9,69,1,'V~O',3,NULL,'BAS',1,NULL),(23,467,'ship_code','vtiger_invoiceshipads',1,'1','ship_code','Shipping Code',1,2,'',100,10,69,1,'V~O',3,NULL,'BAS',1,NULL),(23,468,'bill_country','vtiger_invoicebillads',1,'1','bill_country','Billing Country',1,2,'',100,11,69,1,'V~O',3,NULL,'BAS',1,NULL),(23,469,'ship_country','vtiger_invoiceshipads',1,'1','ship_country','Shipping Country',1,2,'',100,12,69,1,'V~O',3,NULL,'BAS',1,NULL),(23,470,'bill_pobox','vtiger_invoicebillads',1,'1','bill_pobox','Billing Po Box',1,2,'',100,3,69,1,'V~O',3,NULL,'BAS',1,NULL),(23,471,'ship_pobox','vtiger_invoiceshipads',1,'1','ship_pobox','Shipping Po Box',1,2,'',100,4,69,1,'V~O',3,NULL,'BAS',1,NULL),(23,472,'description','vtiger_crmentity',1,'19','description','Description',1,2,'',100,1,72,1,'V~O',3,NULL,'ADV',1,NULL),(23,473,'terms_conditions','vtiger_invoice',1,'19','terms_conditions','Terms & Conditions',1,2,'',100,1,71,1,'V~O',3,NULL,'ADV',1,NULL),(23,474,'invoice_no','vtiger_invoice',1,'4','invoice_no','Invoice No',1,0,'',100,3,67,1,'V~O',3,NULL,'BAS',0,NULL),(29,475,'user_name','vtiger_users',1,'106','user_name','User Name',1,0,'',11,1,77,1,'V~M',1,NULL,'BAS',1,NULL),(29,476,'is_admin','vtiger_users',1,'156','is_admin','Admin',1,0,'',3,2,77,1,'V~O',1,NULL,'BAS',1,NULL),(29,477,'user_password','vtiger_users',1,'99','user_password','Password',1,0,'',30,3,77,4,'P~M',1,NULL,'BAS',1,NULL),(29,478,'confirm_password','vtiger_users',1,'99','confirm_password','Confirm Password',1,0,'',30,5,77,4,'P~M',1,NULL,'BAS',1,NULL),(29,479,'first_name','vtiger_users',1,'1','first_name','First Name',1,0,'',30,7,77,1,'V~O',1,NULL,'BAS',1,NULL),(29,480,'last_name','vtiger_users',1,'2','last_name','Last Name',1,0,'',30,9,77,1,'V~M',1,NULL,'BAS',1,NULL),(29,481,'roleid','vtiger_user2role',1,'98','roleid','Role',1,0,'',200,11,77,1,'V~M',1,NULL,'BAS',1,NULL),(29,482,'email1','vtiger_users',1,'13','email1','Email',1,0,'',100,4,77,1,'E~M',1,NULL,'BAS',1,NULL),(29,483,'status','vtiger_users',1,'115','status','Status',1,0,'',100,6,77,1,'V~O',1,NULL,'BAS',1,NULL),(29,484,'activity_view','vtiger_users',1,'16','activity_view','Default Activity View',1,0,'',100,12,77,1,'V~O',1,NULL,'BAS',1,NULL),(29,485,'lead_view','vtiger_users',1,'16','lead_view','Default Lead View',1,0,'',100,10,77,1,'V~O',1,NULL,'BAS',1,NULL),(29,486,'hour_format','vtiger_users',1,'16','hour_format','Calendar Hour Format',1,2,'',100,13,77,3,'V~O',1,NULL,'BAS',1,NULL),(29,487,'end_hour','vtiger_users',1,'116','end_hour','Day ends at',1,0,'',100,15,77,3,'V~O',1,NULL,'BAS',1,NULL),(29,488,'start_hour','vtiger_users',1,'16','start_hour','Day starts at',1,2,'',100,14,77,3,'V~O',1,NULL,'BAS',1,NULL),(29,489,'title','vtiger_users',1,'1','title','Title',1,0,'',50,1,79,1,'V~O',1,NULL,'BAS',1,NULL),(29,490,'phone_work','vtiger_users',1,'11','phone_work','Office Phone',1,0,'',50,5,79,1,'V~O',1,NULL,'BAS',1,NULL),(29,491,'department','vtiger_users',1,'1','department','Department',1,0,'',50,3,79,1,'V~O',1,NULL,'BAS',1,NULL),(29,492,'phone_mobile','vtiger_users',1,'11','phone_mobile','Mobile',1,0,'',50,7,79,1,'V~O',1,NULL,'BAS',1,NULL),(29,493,'reports_to_id','vtiger_users',1,'101','reports_to_id','Reports To',1,0,'0',50,8,79,1,'V~O',1,NULL,'BAS',1,NULL),(29,494,'phone_other','vtiger_users',1,'11','phone_other','Other Phone',1,0,'',50,11,79,1,'V~O',1,NULL,'BAS',1,NULL),(29,495,'email2','vtiger_users',1,'13','email2','Other Email',1,0,'',100,4,79,1,'E~O',1,NULL,'BAS',1,NULL),(29,496,'phone_fax','vtiger_users',1,'11','phone_fax','Fax',1,0,'',50,2,79,1,'V~O',1,NULL,'BAS',1,NULL),(29,497,'secondaryemail','vtiger_users',1,'13','secondaryemail','Secondary Email',1,0,'',100,6,79,1,'E~O',1,NULL,'BAS',1,NULL),(29,498,'phone_home','vtiger_users',1,'11','phone_home','Home Phone',1,0,'',50,9,79,1,'V~O',1,NULL,'BAS',1,NULL),(29,499,'date_format','vtiger_users',1,'16','date_format','Date Format',1,0,'',30,12,79,1,'V~O',1,NULL,'BAS',1,NULL),(29,500,'signature','vtiger_users',1,'21','signature','Signature',1,0,'',250,13,79,1,'V~O',1,NULL,'BAS',1,NULL),(29,501,'description','vtiger_users',1,'21','description','Documents',1,0,'',250,14,79,1,'V~O',1,NULL,'BAS',1,NULL),(29,502,'address_street','vtiger_users',1,'21','address_street','Street Address',1,0,'',250,1,80,1,'V~O',1,NULL,'BAS',1,NULL),(29,503,'address_city','vtiger_users',1,'1','address_city','City',1,0,'',100,3,80,1,'V~O',1,NULL,'BAS',1,NULL),(29,504,'address_state','vtiger_users',1,'1','address_state','State',1,0,'',100,5,80,1,'V~O',1,NULL,'BAS',1,NULL),(29,505,'address_postalcode','vtiger_users',1,'1','address_postalcode','Postal Code',1,0,'',100,4,80,1,'V~O',1,NULL,'BAS',1,NULL),(29,506,'address_country','vtiger_users',1,'1','address_country','Country',1,0,'',100,2,80,1,'V~O',1,NULL,'BAS',1,NULL),(29,507,'accesskey','vtiger_users',1,'3','accesskey','Webservice Access Key',1,0,'',100,2,83,2,'V~O',1,NULL,'BAS',1,NULL),(29,508,'time_zone','vtiger_users',1,'16','time_zone','Time Zone',1,0,'',200,15,79,1,'V~O',1,NULL,'BAS',1,NULL),(29,509,'currency_id','vtiger_users',1,'117','currency_id','Currency',1,0,'',100,1,78,1,'I~O',1,NULL,'BAS',1,NULL),(29,510,'currency_grouping_pattern','vtiger_users',1,'16','currency_grouping_pattern','Digit Grouping Pattern',1,0,'',100,2,78,1,'V~O',1,NULL,'BAS',1,'<b>Currency - Digit Grouping Pattern</b> <br/><br/>This pattern specifies the format in which the currency separator will be placed.'),(29,511,'currency_decimal_separator','vtiger_users',1,'16','currency_decimal_separator','Decimal Separator',1,0,'.',2,3,78,1,'V~O',1,NULL,'BAS',1,'<b>Currency - Decimal Separator</b> <br/><br/>Decimal separator specifies the separator to be used to separate the fractional values from the whole number part. <br/><b>Eg:</b> <br/>. => 123.45 <br/>, => 123,45 <br/>\' => 123\'45 <br/>  => 123 45 <br/>$ => 123$45 <br/>'),(29,512,'currency_grouping_separator','vtiger_users',1,'16','currency_grouping_separator','Digit Grouping Separator',1,0,'',2,4,78,1,'V~O',1,NULL,'BAS',1,'<b>Currency - Grouping Separator</b> <br/><br/>Grouping separator specifies the separator to be used to group the whole number part into hundreds, thousands etc. <br/><b>Eg:</b> <br/>. => 123.456.789 <br/>, => 123,456,789 <br/>\' => 123\'456\'789 <br/>  => 123 456 789 <br/>$ => 123$456$789 <br/>'),(29,513,'currency_symbol_placement','vtiger_users',1,'16','currency_symbol_placement','Symbol Placement',1,0,'',20,5,78,1,'V~O',1,NULL,'BAS',1,'<b>Currency - Symbol Placement</b> <br/><br/>Symbol Placement allows you to configure the position of the currency symbol with respect to the currency value.<br/><b>Eg:</b> <br/>$1.0 => $123,456,789.50 <br/>1.0$ => 123,456,789.50$ <br/>'),(29,514,'imagename','vtiger_users',1,'105','imagename','User Image',1,0,'',250,10,82,1,'V~O',1,NULL,'BAS',1,NULL),(29,515,'internal_mailer','vtiger_users',1,'56','internal_mailer','INTERNAL_MAIL_COMPOSER',1,0,'1',50,15,79,1,'V~O',1,NULL,'BAS',1,NULL),(29,516,'theme','vtiger_users',1,'31','theme','Theme',1,0,'softed',100,16,79,1,'V~O',1,NULL,'BAS',1,NULL),(29,517,'language','vtiger_users',1,'32','language','Language',1,0,'',100,17,79,1,'V~O',1,NULL,'BAS',1,NULL),(29,518,'reminder_interval','vtiger_users',1,'16','reminder_interval','Reminder Interval',1,0,'',100,1,83,1,'V~O',1,NULL,'BAS',1,NULL),(10,519,'from_email','vtiger_emaildetails',1,'12','from_email','From',1,2,'',100,1,21,3,'V~M',3,NULL,'BAS',0,NULL),(10,520,'to_email','vtiger_emaildetails',1,'8','saved_toid','To',1,2,'',100,2,21,1,'V~M',3,NULL,'BAS',0,NULL),(10,521,'cc_email','vtiger_emaildetails',1,'8','ccmail','CC',1,2,'',1000,3,21,1,'V~O',3,NULL,'BAS',0,NULL),(10,522,'bcc_email','vtiger_emaildetails',1,'8','bccmail','BCC',1,2,'',1000,4,21,1,'V~O',3,NULL,'BAS',0,NULL),(10,523,'idlists','vtiger_emaildetails',1,'357','parent_id','Parent ID',1,2,'',1000,5,21,1,'V~O',3,NULL,'BAS',0,NULL),(10,524,'email_flag','vtiger_emaildetails',1,'16','email_flag','Email Flag',1,2,'',1000,6,21,3,'V~O',3,NULL,'BAS',0,NULL),(36,525,'callfrom','vtiger_pbxmanager',1,'2','callfrom','Call From',1,0,'',100,1,88,1,'V~M',1,NULL,'BAS',1,''),(36,526,'callto','vtiger_pbxmanager',1,'2','callto','Call To',1,0,'',100,2,88,1,'V~M',1,NULL,'BAS',1,''),(36,527,'timeofcall','vtiger_pbxmanager',1,'50','timeofcall','Time Of Call',1,0,'',100,3,88,1,'V~O',1,NULL,'BAS',1,''),(36,528,'status','vtiger_pbxmanager',1,'16','status','Status',1,0,'',100,4,88,1,'V~O',1,NULL,'BAS',1,''),(29,529,'asterisk_extension','vtiger_asteriskextensions',1,'1','asterisk_extension','Asterisk Extension',1,0,'',30,1,90,1,'V~O',1,NULL,'BAS',1,NULL),(29,530,'use_asterisk','vtiger_asteriskextensions',1,'56','use_asterisk','Receive Incoming Calls',1,2,'',30,2,90,1,'C~O',1,NULL,'BAS',1,NULL),(37,531,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,2,'',100,4,91,1,'V~M',2,NULL,'BAS',1,''),(37,532,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,17,91,2,'DT~O',3,NULL,'BAS',0,''),(37,533,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,18,91,2,'DT~O',3,NULL,'BAS',0,''),(37,534,'start_date','vtiger_servicecontracts',1,'5','start_date','Start Date',1,2,'',100,7,91,1,'D~O',2,NULL,'BAS',1,''),(37,535,'end_date','vtiger_servicecontracts',1,'5','end_date','End Date',1,2,'',100,11,91,2,'D~O',3,NULL,'BAS',0,''),(37,536,'sc_related_to','vtiger_servicecontracts',1,'10','sc_related_to','Related to',1,2,'',100,3,91,1,'V~O',2,NULL,'BAS',1,''),(37,537,'tracking_unit','vtiger_servicecontracts',1,'15','tracking_unit','Tracking Unit',1,2,'',100,6,91,1,'V~O',2,NULL,'BAS',1,''),(37,538,'total_units','vtiger_servicecontracts',1,'7','total_units','Total Units',1,2,'',100,8,91,1,'N~O',2,NULL,'BAS',1,''),(37,539,'used_units','vtiger_servicecontracts',1,'7','used_units','Used Units',1,2,'',100,10,91,1,'N~O',2,NULL,'BAS',1,''),(37,540,'subject','vtiger_servicecontracts',1,'1','subject','Subject',1,0,'',100,1,91,1,'V~M',1,NULL,'BAS',1,''),(37,541,'due_date','vtiger_servicecontracts',1,'5','due_date','Due date',1,2,'',100,9,91,1,'D~O',2,NULL,'BAS',1,''),(37,542,'planned_duration','vtiger_servicecontracts',1,'1','planned_duration','Planned Duration',1,2,'',100,13,91,2,'V~O',3,NULL,'BAS',0,''),(37,543,'actual_duration','vtiger_servicecontracts',1,'1','actual_duration','Actual Duration',1,2,'',100,15,91,2,'V~O',3,NULL,'BAS',0,''),(37,544,'contract_status','vtiger_servicecontracts',1,'15','contract_status','Status',1,2,'',100,12,91,1,'V~O',1,NULL,'BAS',1,''),(37,545,'priority','vtiger_servicecontracts',1,'15','contract_priority','Priority',1,2,'',100,14,91,1,'V~O',1,NULL,'BAS',1,''),(37,546,'contract_type','vtiger_servicecontracts',1,'15','contract_type','Type',1,2,'',100,5,91,1,'V~O',1,NULL,'BAS',1,''),(37,547,'progress','vtiger_servicecontracts',1,'9','progress','Progress',1,2,'',100,16,91,2,'N~O~2~2',3,NULL,'BAS',0,''),(37,548,'contract_no','vtiger_servicecontracts',1,'4','contract_no','Contract No',1,0,'',100,2,91,1,'V~O',3,NULL,'BAS',0,''),(37,549,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,17,91,3,'V~O',3,NULL,'BAS',0,''),(38,550,'servicename','vtiger_service',1,'2','servicename','Service Name',1,0,'',100,1,93,1,'V~M',1,NULL,'BAS',1,''),(38,551,'service_no','vtiger_service',1,'4','service_no','Service No',1,0,'',100,2,93,1,'V~O',3,NULL,'BAS',0,''),(38,552,'discontinued','vtiger_service',1,'56','discontinued','Service Active',1,2,'1',100,4,93,1,'V~O',2,NULL,'BAS',1,''),(38,553,'sales_start_date','vtiger_service',1,'5','sales_start_date','Sales Start Date',1,2,'',100,9,93,1,'D~O',1,NULL,'BAS',1,''),(38,554,'sales_end_date','vtiger_service',1,'5','sales_end_date','Sales End Date',1,2,'',100,10,93,1,'D~O~OTH~GE~sales_start_date~Sales Start Date',1,NULL,'BAS',1,''),(38,555,'start_date','vtiger_service',1,'5','start_date','Support Start Date',1,2,'',100,11,93,1,'D~O',1,NULL,'BAS',1,''),(38,556,'expiry_date','vtiger_service',1,'5','expiry_date','Support Expiry Date',1,2,'',100,12,93,1,'D~O~OTH~GE~start_date~Start Date',1,NULL,'BAS',1,''),(38,557,'website','vtiger_service',1,'17','website','Website',1,2,'',100,6,93,1,'V~O',1,NULL,'BAS',1,''),(38,558,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,13,93,2,'DT~O',3,NULL,'BAS',0,''),(38,559,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,14,93,2,'DT~O',3,NULL,'BAS',0,''),(38,560,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,16,93,3,'V~O',3,NULL,'BAS',0,''),(38,561,'service_usageunit','vtiger_service',1,'15','service_usageunit','Usage Unit',1,2,'',100,3,93,1,'V~O',1,NULL,'BAS',1,''),(38,562,'qty_per_unit','vtiger_service',1,'1','qty_per_unit','No of Units',1,2,'',100,5,93,1,'N~O',1,NULL,'BAS',1,''),(38,563,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Owner',1,0,'',100,8,93,1,'V~M',1,NULL,'BAS',1,''),(38,564,'servicecategory','vtiger_service',1,'15','servicecategory','Service Category',1,2,'',100,7,93,1,'V~O',1,NULL,'BAS',1,''),(38,565,'unit_price','vtiger_service',1,'72','unit_price','Price',1,2,'',100,1,94,1,'N~O',2,NULL,'BAS',0,''),(38,566,'taxclass','vtiger_service',1,'83','taxclass','Tax Class',1,2,'',100,4,94,1,'V~O',1,NULL,'BAS',1,''),(38,567,'commissionrate','vtiger_service',1,'9','commissionrate','Commission Rate',1,2,'',100,2,94,1,'N~O~3,2',1,NULL,'BAS',1,''),(38,568,'description','vtiger_crmentity',1,'19','description','Description',1,2,'',100,1,96,1,'V~O',1,NULL,'BAS',1,''),(41,569,'cbupd_no','vtiger_cbupdater',1,'4','cbupd_no','cbupd_no',1,0,'',100,1,97,1,'V~M',1,NULL,'BAS',0,''),(41,570,'author','vtiger_cbupdater',1,'1','author','author',1,0,'',100,2,97,1,'V~O',1,NULL,'BAS',1,''),(41,571,'filename','vtiger_cbupdater',1,'1','filename','filename',1,0,'',100,3,97,1,'V~O',1,NULL,'BAS',1,''),(41,572,'classname','vtiger_cbupdater',1,'1','classname','classname',1,0,'',100,5,97,1,'V~O',1,NULL,'BAS',1,''),(41,573,'execstate','vtiger_cbupdater',1,'15','execstate','execstate',1,2,'',100,4,97,1,'V~O',1,NULL,'BAS',1,''),(41,574,'execdate','vtiger_cbupdater',1,'5','execdate','execdate',1,2,'',100,6,97,1,'D~O',1,NULL,'BAS',1,''),(41,575,'systemupdate','vtiger_cbupdater',1,'56','systemupdate','systemupdate',1,2,'',100,7,97,1,'C~O',1,NULL,'BAS',1,''),(41,576,'execorder','vtiger_cbupdater',1,'7','execorder','execorder',1,2,'',100,8,97,1,'I~O',1,NULL,'BAS',1,''),(41,577,'perspective','vtiger_cbupdater',1,'56','perspective','perspective',1,2,'',100,9,97,1,'C~O',1,NULL,'BAS',1,''),(41,578,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,0,'',100,10,97,1,'V~M',1,NULL,'BAS',1,''),(41,579,'blocked','vtiger_cbupdater',1,'56','blocked','blocked',1,2,'',100,11,97,1,'C~O',1,NULL,'BAS',1,''),(41,580,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,12,97,2,'DT~O',3,NULL,'BAS',1,''),(41,581,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,13,97,2,'DT~O',3,NULL,'BAS',1,''),(41,582,'description','vtiger_crmentity',1,'19','description','Description',1,2,'',100,1,99,1,'V~O',1,NULL,'BAS',1,''),(42,583,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,0,'',100,1,100,1,'V~M',1,NULL,'BAS',1,''),(42,584,'reference','vtiger_cobropago',1,'1','reference','Reference',1,0,'',100,3,100,1,'V~O',1,NULL,'BAS',1,''),(42,585,'parent_id','vtiger_cobropago',1,'10','parent_id','Parent',1,0,'',100,4,100,1,'V~O',1,NULL,'BAS',1,''),(42,586,'related_id','vtiger_cobropago',1,'10','related_id','RelatedTo',1,0,'',100,5,100,1,'V~O',1,NULL,'BAS',1,''),(42,587,'register','vtiger_cobropago',1,'5','register','Register',1,2,'',100,6,100,1,'D~O',1,NULL,'BAS',1,''),(42,588,'duedate','vtiger_cobropago',1,'5','duedate','DueDate',1,2,'',100,7,100,1,'D~O',1,NULL,'BAS',1,''),(42,589,'paid','vtiger_cobropago',1,'56','paid','Paid',1,2,'',100,9,100,1,'C~O',1,NULL,'BAS',1,''),(42,590,'credit','vtiger_cobropago',1,'56','credit','Credit',1,0,'',100,10,100,1,'C~O',1,NULL,'BAS',1,''),(42,591,'paymentmode','vtiger_cobropago',1,'15','paymentmode','PaymentMode',1,2,'',100,11,100,1,'V~O',1,NULL,'BAS',1,''),(42,592,'paymentcategory','vtiger_cobropago',1,'15','paymentcategory','Category',1,2,'',100,12,100,1,'V~O',1,NULL,'BAS',1,''),(42,593,'amount','vtiger_cobropago',1,'7','amount','Amount',1,0,'',100,13,100,1,'N~O',1,NULL,'BAS',1,''),(42,594,'cost','vtiger_cobropago',1,'7','cost','Cost',1,0,'',100,14,100,1,'N~O',1,NULL,'BAS',1,''),(42,595,'benefit','vtiger_cobropago',1,'1','benefit','Benefit',1,2,'',100,15,100,2,'N~O',1,NULL,'BAS',1,''),(42,596,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,16,100,2,'DT~O',3,NULL,'BAS',1,''),(42,597,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,17,100,2,'DT~O',3,NULL,'BAS',1,''),(42,598,'comercialid','vtiger_cobropago',1,'101','reports_to_id','Comercial',1,2,'0',100,18,100,1,'V~O',1,NULL,'BAS',1,''),(42,599,'description','vtiger_crmentity',1,'19','description','Description',1,2,'',100,1,102,1,'V~O',1,NULL,'BAS',1,''),(43,600,'asset_no','vtiger_assets',1,'4','asset_no','Asset No',1,0,'',100,2,103,1,'V~O',3,NULL,'BAS',0,''),(43,601,'product','vtiger_assets',1,'10','product','Product Name',1,2,'',100,3,103,1,'V~M',1,NULL,'BAS',1,''),(43,602,'serialnumber','vtiger_assets',1,'2','serialnumber','Serial Number',1,2,'',100,4,103,1,'V~M',1,NULL,'BAS',1,''),(43,603,'datesold','vtiger_assets',1,'5','datesold','Date Sold',1,2,'',100,5,103,1,'D~M~OTH~GE~datesold~Date Sold',1,NULL,'BAS',1,''),(43,604,'dateinservice','vtiger_assets',1,'5','dateinservice','Date in Service',1,2,'',100,6,103,1,'D~M~OTH~GE~dateinservice~Date in Service',1,NULL,'BAS',1,''),(43,605,'assetstatus','vtiger_assets',1,'15','assetstatus','Status',1,2,'',100,7,103,1,'V~M',1,NULL,'BAS',1,''),(43,606,'tagnumber','vtiger_assets',1,'2','tagnumber','Tag Number',1,2,'',100,8,103,1,'V~O',1,NULL,'BAS',1,''),(43,607,'invoiceid','vtiger_assets',1,'10','invoiceid','Invoice Name',1,2,'',100,9,103,1,'V~O',1,NULL,'BAS',1,''),(43,608,'shippingmethod','vtiger_assets',1,'2','shippingmethod','Shipping Method',1,2,'',100,10,103,1,'V~O',1,NULL,'BAS',1,''),(43,609,'shippingtrackingnumber','vtiger_assets',1,'2','shippingtrackingnumber','Shipping Tracking Number',1,2,'',100,11,103,1,'V~O',1,NULL,'BAS',1,''),(43,610,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,2,'',100,4,103,1,'V~M',1,NULL,'BAS',1,''),(43,611,'assetname','vtiger_assets',1,'1','assetname','Asset Name',1,0,'',100,12,103,1,'V~M',1,NULL,'BAS',1,''),(43,612,'account','vtiger_assets',1,'10','account','Customer Name',1,2,'',100,13,103,1,'V~M',1,NULL,'BAS',1,''),(43,613,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,14,103,2,'DT~O',3,NULL,'BAS',0,''),(43,614,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,15,103,2,'DT~O',3,NULL,'BAS',0,''),(43,615,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,16,103,3,'V~O',3,NULL,'BAS',0,''),(43,616,'description','vtiger_crmentity',1,'19','description','Notes',1,2,'',100,1,105,1,'V~O',1,NULL,'BAS',1,''),(47,617,'commentcontent','vtiger_modcomments',1,'19','commentcontent','Comment',1,0,'',100,4,106,1,'V~M',1,NULL,'BAS',2,''),(47,618,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,0,'',100,1,107,1,'V~M',1,NULL,'BAS',2,''),(47,619,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,5,107,2,'DT~O',1,NULL,'BAS',0,''),(47,620,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,6,107,2,'DT~O',1,NULL,'BAS',0,''),(47,621,'related_to','vtiger_modcomments',1,'10','related_to','Related To',1,2,'',100,2,107,1,'V~M',2,NULL,'BAS',2,''),(47,622,'smcreatorid','vtiger_crmentity',1,'52','creator','Creator',1,2,'',100,4,107,2,'V~O',1,NULL,'BAS',1,''),(47,623,'parent_comments','vtiger_modcomments',1,'10','parent_comments','Related To Comments',1,2,'',100,7,107,1,'V~O',1,NULL,'BAS',1,''),(48,624,'projectmilestonename','vtiger_projectmilestone',1,'2','projectmilestonename','Project Milestone Name',1,2,'',100,1,109,1,'V~M',1,NULL,'BAS',1,''),(48,625,'projectmilestonedate','vtiger_projectmilestone',1,'5','projectmilestonedate','Milestone Date',1,2,'',100,5,109,1,'D~O',1,NULL,'BAS',1,''),(48,626,'projectid','vtiger_projectmilestone',1,'10','projectid','Related to',1,0,'',100,4,109,1,'V~M',1,NULL,'BAS',1,''),(48,627,'projectmilestonetype','vtiger_projectmilestone',1,'15','projectmilestonetype','Type',1,2,'',100,7,109,1,'V~O',1,NULL,'BAS',1,''),(48,628,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,2,'',100,6,109,1,'V~M',1,NULL,'BAS',1,''),(48,629,'projectmilestone_no','vtiger_projectmilestone',2,'4','projectmilestone_no','Project Milestone No',1,0,'',100,2,109,1,'V~O',3,NULL,'BAS',0,''),(48,630,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,2,'',100,1,110,2,'DT~O',1,NULL,'BAS',1,''),(48,631,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,2,'',100,2,110,2,'DT~O',1,NULL,'BAS',1,''),(48,632,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,3,110,3,'V~O',3,NULL,'BAS',0,''),(48,633,'description','vtiger_crmentity',1,'19','description','Description',1,2,'',100,1,111,1,'V~O',1,NULL,'BAS',1,''),(49,634,'projecttaskname','vtiger_projecttask',1,'2','projecttaskname','Project Task Name',1,2,'',100,1,112,1,'V~M',1,NULL,'BAS',1,''),(49,635,'projecttasktype','vtiger_projecttask',1,'15','projecttasktype','Type',1,2,'',100,4,112,1,'V~O',1,NULL,'BAS',1,''),(49,636,'projecttaskpriority','vtiger_projecttask',1,'15','projecttaskpriority','Priority',1,2,'',100,3,112,1,'V~O',1,NULL,'BAS',1,''),(49,637,'projectid','vtiger_projecttask',1,'10','projectid','Related to',1,0,'',100,6,112,1,'V~M',1,NULL,'BAS',1,''),(49,638,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,2,'',100,7,112,1,'V~M',1,NULL,'BAS',1,''),(49,639,'projecttasknumber','vtiger_projecttask',1,'7','projecttasknumber','Project Task Number',1,2,'',100,5,112,1,'I~O',1,NULL,'BAS',1,''),(49,640,'projecttask_no','vtiger_projecttask',2,'4','projecttask_no','Project Task No',1,0,'',100,2,112,1,'V~O',3,NULL,'BAS',0,''),(49,641,'projecttaskprogress','vtiger_projecttask',1,'15','projecttaskprogress','Progress',1,2,'',100,1,113,1,'V~O',1,NULL,'BAS',1,''),(49,642,'projecttaskhours','vtiger_projecttask',1,'7','projecttaskhours','Worked Hours',1,2,'',100,2,113,1,'V~O',1,NULL,'BAS',1,''),(49,643,'startdate','vtiger_projecttask',1,'5','startdate','Start Date',1,2,'',100,3,113,1,'D~O',1,NULL,'BAS',1,''),(49,644,'enddate','vtiger_projecttask',1,'5','enddate','End Date',1,2,'',100,4,113,1,'D~O~OTH~GE~startdate~Start Date',1,NULL,'BAS',1,''),(49,645,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,2,'',100,5,113,2,'DT~O',1,NULL,'BAS',1,''),(49,646,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,2,'',100,6,113,2,'DT~O',1,NULL,'BAS',1,''),(49,647,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,7,113,3,'V~O',3,NULL,'BAS',0,''),(49,648,'description','vtiger_crmentity',1,'19','description','Description',1,2,'',100,1,114,1,'V~O',1,NULL,'BAS',1,''),(50,649,'projectname','vtiger_project',1,'2','projectname','Project Name',1,2,'',100,1,115,1,'V~M',1,NULL,'BAS',1,''),(50,650,'startdate','vtiger_project',1,'5','startdate','Start Date',1,2,'',100,3,115,1,'D~O',1,NULL,'BAS',1,''),(50,651,'targetenddate','vtiger_project',1,'5','targetenddate','Target End Date',1,2,'',100,5,115,1,'D~O~OTH~GE~startdate~Start Date',1,NULL,'BAS',1,''),(50,652,'actualenddate','vtiger_project',1,'5','actualenddate','Actual End Date',1,2,'',100,6,115,1,'D~O~OTH~GE~startdate~Start Date',1,NULL,'BAS',1,''),(50,653,'projectstatus','vtiger_project',1,'15','projectstatus','Status',1,2,'',100,7,115,1,'V~O',1,NULL,'BAS',1,''),(50,654,'projecttype','vtiger_project',1,'15','projecttype','Type',1,2,'',100,8,115,1,'V~O',1,NULL,'BAS',1,''),(50,655,'linktoaccountscontacts','vtiger_project',1,'10','linktoaccountscontacts','Related to',1,2,'',100,9,115,1,'V~O',1,NULL,'BAS',1,''),(50,656,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,2,'',100,4,115,1,'V~M',1,NULL,'BAS',1,''),(50,657,'project_no','vtiger_project',2,'4','project_no','Project No',1,0,'',100,2,115,1,'V~O',3,NULL,'BAS',0,''),(50,658,'targetbudget','vtiger_project',1,'7','targetbudget','Target Budget',1,2,'',100,1,116,1,'N~O',1,NULL,'BAS',1,''),(50,659,'projecturl','vtiger_project',1,'17','projecturl','Project Url',1,2,'',100,2,116,1,'V~O',1,NULL,'BAS',1,''),(50,660,'projectpriority','vtiger_project',1,'15','projectpriority','Priority',1,2,'',100,3,116,1,'V~O',1,NULL,'BAS',1,''),(50,661,'progress','vtiger_project',1,'15','progress','Progress',1,2,'',100,4,116,1,'V~O',1,NULL,'BAS',1,''),(50,662,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,2,'',100,5,116,2,'DT~O',1,NULL,'BAS',1,''),(50,663,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,2,'',100,6,116,2,'DT~O',1,NULL,'BAS',1,''),(50,664,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,7,116,3,'V~O',3,NULL,'BAS',0,''),(50,665,'description','vtiger_crmentity',1,'19','description','Description',1,2,'',100,1,117,1,'V~O',1,NULL,'BAS',1,''),(52,666,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,0,'',100,2,118,1,'V~M',1,NULL,'BAS',1,''),(52,667,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,5,118,2,'DT~O',1,NULL,'BAS',0,''),(52,668,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,6,118,2,'DT~O',1,NULL,'BAS',0,''),(52,669,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,0,'',100,7,118,3,'V~O',3,NULL,'BAS',0,''),(52,670,'message','vtiger_smsnotifier',1,'21','message','message',1,0,'',100,1,118,1,'V~M',1,NULL,'BAS',1,''),(2,671,'forecast_amount','vtiger_potential',1,'71','forecast_amount','Forecast Amount',1,2,'',100,17,1,1,'N~O',1,NULL,'BAS',0,''),(50,672,'email','vtiger_project',1,'13','email','Email',1,2,'',100,10,115,2,'E~O',1,NULL,'BAS',1,''),(49,673,'email','vtiger_projecttask',1,'13','email','Email',1,2,'',100,8,112,2,'E~O',1,NULL,'BAS',1,''),(2,674,'email','vtiger_potential',1,'13','email','Email',1,2,'',100,18,1,2,'E~O',1,NULL,'BAS',1,''),(13,675,'email','vtiger_troubletickets',1,'13','email','Email',1,2,'',100,17,25,2,'E~O',1,NULL,'BAS',1,''),(29,676,'send_email_to_sender','vtiger_users',1,'56','send_email_to_sender','LBL_SEND_EMAIL_TO_SENDER',1,2,'1',100,18,79,1,'C~O',1,NULL,'BAS',0,''),(56,677,'gvname','vtiger_globalvariable',1,'15','gvname','Name',1,2,'',150,1,121,1,'V~M',1,NULL,'BAS',1,''),(56,678,'default_check','vtiger_globalvariable',1,'56','default_check','Default',1,2,'',3,2,121,1,'C~O',1,NULL,'BAS',1,''),(56,679,'value','vtiger_globalvariable',1,'1','value','Value',1,2,'',250,3,121,1,'V~O',1,NULL,'BAS',1,''),(56,680,'mandatory','vtiger_globalvariable',1,'56','mandatory','Mandatory',1,2,'',3,4,121,1,'C~O',1,NULL,'BAS',1,''),(56,681,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','User',1,2,'',100,5,121,1,'V~M',1,NULL,'BAS',1,''),(56,682,'blocked','vtiger_globalvariable',1,'56','blocked','Blocked',1,2,'Blocked',3,6,121,1,'V~O',1,NULL,'BAS',1,''),(56,683,'module_list','vtiger_globalvariable',1,'3314','module_list','Module List',1,2,'',0,7,121,1,'V~O',1,NULL,'BAS',1,''),(56,684,'category','vtiger_globalvariable',1,'15','category','Category',1,2,'',150,8,121,1,'V~O',1,NULL,'BAS',1,''),(56,685,'in_module_list','vtiger_globalvariable',1,'56','in_module_list','In Module List',1,2,'1',3,9,121,1,'C~O',1,NULL,'BAS',1,''),(56,686,'globalno','vtiger_globalvariable',1,'4','globalno','Globalno',2,2,'',150,10,121,1,'V~O',1,NULL,'BAS',0,''),(56,687,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,2,'',100,11,121,2,'DT~O',1,NULL,'BAS',1,''),(56,688,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,2,'',100,12,121,2,'DT~O',1,NULL,'BAS',1,''),(56,689,'description','vtiger_crmentity',1,'19','description','Description',1,2,'',100,1,122,1,'V~O',1,NULL,'BAS',1,''),(47,690,'relatedassignedemail','vtiger_modcomments',1,'13','relatedassignedemail','Related Assigned Email',1,2,'',100,8,107,2,'E~O',1,NULL,'BAS',0,''),(57,691,'inventorydetails_no','vtiger_inventorydetails',1,'4','inventorydetails_no','Inventory Details No',1,0,'',100,1,123,1,'V~O',1,NULL,'BAS',0,''),(57,692,'productid','vtiger_inventorydetails',1,'10','productid','Products',1,0,'',100,2,123,1,'V~O',1,NULL,'BAS',1,''),(57,693,'related_to','vtiger_inventorydetails',1,'10','related_to','Related To',1,0,'',100,3,123,1,'I~O',1,NULL,'BAS',1,''),(57,694,'account_id','vtiger_inventorydetails',1,'10','account_id','Accounts',1,0,'',100,4,123,1,'I~O',1,NULL,'BAS',1,''),(57,695,'contact_id','vtiger_inventorydetails',1,'10','contact_id','Contacts',1,0,'',100,5,123,1,'I~O',1,NULL,'BAS',1,''),(57,696,'vendor_id','vtiger_inventorydetails',1,'10','vendor_id','Vendors',1,0,'',100,6,123,1,'I~O',1,NULL,'BAS',1,''),(57,697,'sequence_no','vtiger_inventorydetails',1,'1','sequence_no','Sequence No',1,0,'',100,7,123,1,'I~O',1,NULL,'BAS',1,''),(57,698,'lineitem_id','vtiger_inventorydetails',1,'1','lineitem_id','Line Item ID',1,0,'',100,8,123,1,'I~O',1,NULL,'BAS',1,''),(57,699,'quantity','vtiger_inventorydetails',1,'1','quantity','Quantity',1,0,'',100,9,123,1,'N~O',1,NULL,'BAS',1,''),(57,700,'listprice','vtiger_inventorydetails',1,'71','listprice','Listprice',1,0,'',100,10,123,1,'N~O',1,NULL,'BAS',1,''),(57,701,'tax_percent','vtiger_inventorydetails',1,'9','tax_percent','Tax Percent',1,0,'',100,11,123,1,'N~O~3,2',1,NULL,'BAS',1,''),(57,702,'extgross','vtiger_inventorydetails',1,'71','extgross','Extgross',1,0,'',100,12,123,1,'N~O',1,NULL,'BAS',1,''),(57,703,'discount_percent','vtiger_inventorydetails',1,'9','discount_percent','Discount Percent',1,0,'',100,13,123,1,'N~O~3,2',1,NULL,'BAS',1,''),(57,704,'discount_amount','vtiger_inventorydetails',1,'71','discount_amount','Discount Amount',1,0,'',100,14,123,1,'N~O',1,NULL,'BAS',1,''),(57,705,'extnet','vtiger_inventorydetails',1,'71','extnet','Extnet',1,0,'',100,15,123,1,'N~O',1,NULL,'BAS',1,''),(57,706,'linetax','vtiger_inventorydetails',1,'71','linetax','Line Tax',1,0,'',100,16,123,1,'N~O',1,NULL,'BAS',1,''),(57,707,'linetotal','vtiger_inventorydetails',1,'71','linetotal','Line Total',1,0,'',100,17,123,1,'N~O',1,NULL,'BAS',1,''),(57,708,'units_delivered_received','vtiger_inventorydetails',1,'7','units_delivered_received','Units Delivered Received',1,0,'',100,18,123,1,'N~O',1,NULL,'BAS',1,''),(57,709,'line_completed','vtiger_inventorydetails',1,'56','line_completed','Line Completed',1,0,'',100,19,123,1,'C~O',1,NULL,'BAS',1,''),(57,710,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,0,'',100,20,123,1,'V~M',1,NULL,'BAS',1,''),(57,711,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,21,123,2,'DT~O',1,NULL,'BAS',1,''),(57,712,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,22,123,2,'DT~O',1,NULL,'BAS',1,''),(57,713,'description','vtiger_crmentity',1,'19','description','Description',1,0,'',100,1,125,1,'V~O',1,NULL,'BAS',1,''),(18,714,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,2,'',100,13,42,1,'V~M',0,NULL,'BAS',1,''),(42,715,'cyp_no','vtiger_cobropago',1,'4','cyp_no','CyP No',1,0,'',100,2,100,1,'V~M',1,NULL,'BAS',0,''),(42,716,'paymentdate','vtiger_cobropago',1,'5','paymentdate','PaymentDate',1,0,'',100,8,100,1,'D~O',1,NULL,'BAS',1,''),(13,717,'from_mailscanner','vtiger_troubletickets',1,'56','from_mailscanner','From mailscanner',1,0,'',100,1,25,3,'C~O',1,NULL,'BAS',1,''),(14,718,'cost_price','vtiger_products',1,'71','cost_price','Cost Price',1,0,'',100,5,32,1,'N~O',1,NULL,'BAS',1,''),(38,719,'cost_price','vtiger_service',1,'71','cost_price','Cost Price',1,0,'',100,5,94,1,'N~O',1,NULL,'BAS',1,''),(29,720,'no_of_currency_decimals','vtiger_users',1,'16','no_of_currency_decimals','Number Of Currency Decimals',1,2,'2',100,6,78,1,'V~O',1,NULL,'BAS',1,'<b>Currency - Number of Decimal places</b> <br/><br/>Number of decimal places specifies how many number of decimals will be shown after decimal separator.<br/><b>Eg:</b> 123.99'),(58,721,'mapname','vtiger_cbmap',1,'2','mapname','Map Name',1,2,'',100,1,127,1,'V~M',1,NULL,'BAS',1,''),(58,722,'mapnumber','vtiger_cbmap',1,'4','mapnumber','Map Number',1,2,'',100,2,127,1,'V~O',1,NULL,'BAS',0,''),(58,723,'maptype','vtiger_cbmap',1,'15','maptype','Map Type',1,2,'',100,3,127,1,'V~O',1,NULL,'BAS',1,''),(58,724,'targetname','vtiger_cbmap',1,'1613','targetname','Target Module',1,2,'',0,4,127,1,'V~O',1,NULL,'BAS',1,''),(58,725,'content','vtiger_cbmap',1,'19','content','Content',1,2,'',100,5,127,1,'V~0',1,NULL,'BAS',1,''),(58,726,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,0,'',100,6,127,1,'V~M',1,NULL,'BAS',1,''),(58,727,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,7,127,2,'DT~O',1,NULL,'BAS',1,''),(58,728,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,2,'',100,8,127,2,'DT~O',1,NULL,'BAS',1,''),(58,729,'description','vtiger_crmentity',1,'19','description','Description',1,2,'',100,1,129,1,'V~O',1,NULL,'BAS',1,''),(56,730,'bmapid','vtiger_globalvariable',1,'10','bmapid','cbMap',1,0,'',100,13,121,1,'V~O',1,NULL,'BAS',1,''),(29,731,'failed_login_attempts','vtiger_users',1,'7','failed_login_attempts','LBL_FAILED_LOGIN_ATTEMPTS',1,2,'',100,3,83,1,'I~O',1,NULL,'BAS',0,''),(57,732,'cost_price','vtiger_inventorydetails',1,'71','cost_price','Cost Price',1,0,'',100,23,123,1,'N~O',1,NULL,'BAS',1,''),(57,733,'cost_gross','vtiger_inventorydetails',1,'71','cost_gross','Cost Total',1,0,'',100,24,123,1,'N~O',1,NULL,'BAS',1,''),(49,734,'projecttaskstatus','vtiger_projecttask',1,'15','projecttaskstatus','Status',1,2,'',100,9,112,1,'V~O',0,1,'BAS',1,''),(7,735,'emailoptout','vtiger_leaddetails',1,'56','emailoptout','Email Opt Out',1,2,'',100,24,13,1,'C~O',0,7,'BAS',1,''),(6,736,'isconvertedfromlead','vtiger_account',1,'56','isconvertedfromlead','Is Converted From Lead',1,2,'',100,24,9,2,'C~O',1,NULL,'BAS',0,''),(6,737,'convertedfromlead','vtiger_account',1,'10','convertedfromlead','Converted From Lead',1,2,'',100,25,9,3,'V~O',1,NULL,'BAS',0,''),(4,738,'isconvertedfromlead','vtiger_contactdetails',1,'56','isconvertedfromlead','Is Converted From Lead',1,2,'',100,29,4,2,'C~O',1,NULL,'BAS',0,''),(4,739,'convertedfromlead','vtiger_contactdetails',1,'10','convertedfromlead','Converted From Lead',1,2,'',100,30,4,3,'V~O',1,NULL,'BAS',0,''),(2,740,'isconvertedfromlead','vtiger_potential',1,'56','isconvertedfromlead','Is Converted From Lead',1,2,'',100,19,1,2,'C~O',1,NULL,'BAS',0,''),(2,741,'convertedfromlead','vtiger_potential',1,'10','convertedfromlead','Converted From Lead',1,2,'',100,20,1,3,'V~O',1,NULL,'BAS',0,''),(13,742,'commentadded','vtiger_troubletickets',1,'56','commentadded','Comment Added',1,2,'',100,18,25,3,'C~O',0,5,'BAS',1,''),(14,743,'divisible','vtiger_products',1,'56','divisible','Divisible',1,0,'',100,7,33,1,'C~O',1,NULL,'BAS',1,''),(38,744,'divisible','vtiger_service',1,'56','divisible','Divisible',1,0,'',100,6,94,1,'C~O',1,NULL,'BAS',1,''),(57,745,'total_stock','vtiger_inventorydetails',1,'7','total_stock','Total Stock',1,0,'',100,25,123,2,'N~O',1,NULL,'BAS',1,''),(2,746,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,21,1,2,'V~O',3,7,'BAS',0,''),(4,747,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,31,4,2,'V~O',3,7,'BAS',0,''),(6,748,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,26,9,2,'V~O',3,5,'BAS',0,''),(7,749,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,25,13,2,'V~O',3,8,'BAS',0,''),(8,750,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,13,17,2,'V~O',3,4,'BAS',0,''),(13,751,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,19,25,2,'V~O',3,6,'BAS',0,''),(14,752,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,23,31,2,'V~O',3,6,'BAS',0,''),(15,753,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,9,37,2,'V~O',3,1,'BAS',0,''),(18,754,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,14,42,2,'V~O',3,4,'BAS',0,''),(19,755,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,8,46,2,'V~O',3,4,'BAS',0,''),(20,756,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,23,49,2,'V~O',3,1,'BAS',0,''),(21,757,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,23,55,2,'V~O',3,1,'BAS',0,''),(22,758,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,23,61,2,'V~O',3,1,'BAS',0,''),(23,759,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,23,67,2,'V~O',3,1,'BAS',0,''),(26,760,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,17,74,2,'V~O',3,8,'BAS',0,''),(37,761,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,19,91,2,'V~O',3,1,'BAS',0,''),(38,762,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,17,93,2,'V~O',3,1,'BAS',0,''),(41,763,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,14,97,2,'V~O',3,1,'BAS',0,''),(42,764,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,19,100,2,'V~O',3,1,'BAS',0,''),(43,765,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,17,103,2,'V~O',3,1,'BAS',0,''),(48,766,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,8,109,2,'V~O',3,1,'BAS',0,''),(49,767,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,10,112,2,'V~O',3,2,'BAS',0,''),(50,768,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,11,115,2,'V~O',3,1,'BAS',0,''),(52,769,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,8,118,2,'V~O',3,1,'BAS',0,''),(56,770,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,14,121,2,'V~O',3,1,'BAS',0,''),(57,771,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,26,123,2,'V~O',3,1,'BAS',0,''),(58,772,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,9,127,2,'V~O',3,1,'BAS',0,''),(62,773,'cbtandcno','vtiger_cbtandc',1,'4','cbtandcno','TandC No',1,0,'',25,1,131,1,'V~M',1,NULL,'BAS',0,''),(62,774,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,0,'',100,2,131,1,'V~M',1,NULL,'BAS',1,''),(62,775,'reference','vtiger_cbtandc',1,'1','reference','Reference',1,2,'',100,3,131,1,'V~O',1,NULL,'BAS',1,''),(62,776,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,0,'',100,4,131,2,'T~O',3,NULL,'BAS',1,''),(62,777,'isdefault','vtiger_cbtandc',2,'56','isdefault','Is Default',0,2,'',100,5,131,1,'C~O',1,NULL,'BAS',1,''),(62,778,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,0,'',100,6,131,2,'T~O',3,NULL,'BAS',1,''),(62,779,'formodule','vtiger_cbtandc',1,'15','formodule','formodule',1,0,'',20,7,131,1,'V~O',3,NULL,'BAS',1,''),(62,780,'tandc','vtiger_cbtandc',1,'19','tandc','Terms and Conditions',1,2,'',100,1,132,1,'V~O',1,NULL,'BAS',1,''),(62,781,'description','vtiger_crmentity',1,'19','description','Description',1,2,'',100,1,133,1,'V~O',1,NULL,'BAS',1,''),(23,782,'tandc','vtiger_invoice',1,'10','tandc','Terms and Conditions',1,2,'',100,2,71,1,'I~O',1,NULL,'BAS',0,''),(22,783,'tandc','vtiger_salesorder',1,'10','tandc','Terms and Conditions',1,2,'',100,2,65,1,'I~O',1,NULL,'BAS',0,''),(20,784,'tandc','vtiger_quotes',1,'10','tandc','Terms and Conditions',1,2,'',100,2,53,1,'I~O',1,NULL,'BAS',0,''),(21,785,'tandc','vtiger_purchaseorder',1,'10','tandc','Terms and Conditions',1,2,'',100,2,59,1,'I~O',1,NULL,'BAS',0,''),(63,786,'subject','vtiger_activity',1,'2','subject','Subject',1,2,'',100,1,134,1,'V~M',1,NULL,'BAS',1,''),(63,787,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,2,'',100,2,134,1,'V~M',1,NULL,'BAS',1,''),(63,788,'dtstart','vtiger_activity',1,'50','dtstart','Start Date Time',1,2,'',100,3,134,1,'DT~M',1,NULL,'BAS',1,''),(63,789,'dtend','vtiger_activity',1,'50','dtend','Due Date',1,2,'',100,5,134,1,'D~M',1,NULL,'BAS',1,''),(63,790,'rel_id','vtiger_activity',1,'10','rel_id','Related To',1,2,'',100,7,134,1,'I~O',1,NULL,'BAS',1,''),(63,791,'cto_id','vtiger_activity',1,'10','cto_id','Contact Name',1,2,'',100,8,134,1,'I~O',1,NULL,'BAS',1,''),(63,792,'eventstatus','vtiger_activity',1,'15','eventstatus','Status',1,2,'',100,9,134,1,'V~O',1,NULL,'BAS',1,''),(63,793,'priority','vtiger_activity',1,'15','taskpriority','Priority',1,2,'',100,10,134,1,'V~O',1,NULL,'BAS',1,''),(63,794,'sendnotification','vtiger_activity',1,'56','sendnotification','Send Notification',1,2,'',100,11,134,1,'C~O',1,NULL,'BAS',1,''),(63,795,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,2,'',100,14,134,2,'DT~O',3,NULL,'BAS',0,''),(63,796,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,2,'',100,15,134,2,'DT~O',3,NULL,'BAS',0,''),(63,797,'activitytype','vtiger_activity',1,'15','activitytype','Activity Type',1,2,'',100,16,134,1,'V~O',1,NULL,'BAS',1,''),(63,798,'visibility','vtiger_activity',1,'16','visibility','Visibility',1,2,'',100,17,134,1,'V~O',1,NULL,'BAS',1,''),(63,799,'duration_hours','vtiger_activity',1,'63','duration_hours','Duration',1,2,'',100,17,134,3,'I~O',1,NULL,'BAS',1,''),(63,800,'duration_minutes','vtiger_activity',1,'16','duration_minutes','Duration Minutes',1,2,'',100,18,134,3,'I~O',1,NULL,'BAS',1,''),(63,801,'location','vtiger_activity',1,'1','location','Location',1,2,'',100,19,134,1,'V~O',1,NULL,'BAS',1,''),(63,802,'reminder_time','vtiger_activity_reminder',1,'30','reminder_time','Send Reminder',1,2,'',100,1,134,3,'I~O',1,NULL,'BAS',1,''),(63,803,'recurringtype','vtiger_activity',1,'16','recurringtype','Recurrence',1,2,'',100,6,134,3,'O~O',1,NULL,'BAS',1,''),(63,804,'notime','vtiger_activity',1,'56','notime','No Time',1,2,'',100,20,134,3,'C~O',1,NULL,'BAS',1,''),(63,805,'relatedwith','vtiger_activity',1,'10','relatedwith','Related with',1,2,'',100,21,134,1,'I~O',1,NULL,'BAS',1,''),(63,806,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,22,134,2,'V~O',3,NULL,'BAS',0,''),(63,807,'modifiedby','vtiger_crmentity',1,'52','modifiedby','Last Modified By',1,2,'',100,23,134,3,'V~O',3,NULL,'BAS',0,''),(63,808,'description','vtiger_crmentity',1,'19','description','Description',1,2,'',100,1,135,1,'V~O',1,NULL,'BAS',1,''),(63,809,'followupdt','vtiger_activity',2,'50','followupdt','Fecha Seguimiento',1,2,'',100,1,137,1,'DT~O',1,NULL,'BAS',1,''),(63,810,'followuptype','vtiger_activity',1,'15','followuptype','Tipo Seguimiento',1,2,'',100,2,137,1,'V~O',1,NULL,'BAS',1,''),(63,811,'followupcreate','vtiger_activity',1,'56','followupcreate','Crear Seguimiento',1,2,'',100,4,137,1,'C~O',1,NULL,'BAS',1,''),(63,812,'date_start','vtiger_activity',1,'6','date_start','Start Date & Time',1,0,'',100,3,134,3,'DT~M~time_start',0,1,'BAS',1,''),(63,813,'time_start','vtiger_activity',1,'2','time_start','Time Start',1,0,'',100,4,134,3,'T~M',1,NULL,'BAS',1,''),(63,814,'due_date','vtiger_activity',1,'23','due_date','End Date',1,0,'',100,5,134,3,'D~M~OTH~GE~date_start~Start Date &amp; Time',0,2,'BAS',1,''),(63,815,'time_end','vtiger_activity',1,'2','time_end','End Time',1,0,'',100,5,134,3,'T~M',1,NULL,'BAS',1,''),(64,816,'autonum','vtiger_cbtranslation',1,'4','autonum','cbtranslation No',1,2,'',100,1,140,1,'V~M',1,NULL,'BAS',0,''),(64,817,'locale','vtiger_cbtranslation',1,'32','locale','Locale',1,2,'',100,2,140,1,'V~M',1,NULL,'BAS',1,''),(64,818,'translation_module','vtiger_cbtranslation',1,'1614','translation_module','Module',1,2,'',100,3,140,1,'V~M',1,NULL,'BAS',1,''),(64,819,'translation_key','vtiger_cbtranslation',1,'1','translation_key','Key',1,2,'',100,4,140,1,'V~M',1,NULL,'BAS',1,''),(64,820,'i18n','vtiger_cbtranslation',1,'19','i18n','i18n',1,2,'',100,5,140,1,'V~0',1,NULL,'BAS',1,''),(64,821,'translates','vtiger_cbtranslation',1,'10','translates','Translates',1,2,'',100,6,140,1,'V~0',1,NULL,'BAS',1,''),(64,822,'forpicklist','vtiger_cbtranslation',1,'1615','forpicklist','Picklist',1,2,'',100,7,140,1,'V~0',1,NULL,'BAS',1,''),(64,823,'forfield','vtiger_cbtranslation',1,'1','forfield','Field',1,2,'',100,8,140,1,'V~0',1,NULL,'BAS',1,''),(64,824,'proofread','vtiger_cbtranslation',1,'56','proofread','Proof Read',1,2,'',100,9,140,1,'C~0',1,NULL,'BAS',1,''),(64,825,'smownerid','vtiger_crmentity',1,'53','assigned_user_id','Assigned To',1,2,'',100,10,140,1,'V~M',1,NULL,'BAS',1,''),(64,826,'createdtime','vtiger_crmentity',1,'70','createdtime','Created Time',1,2,'',100,11,140,2,'T~O',1,NULL,'BAS',1,''),(64,827,'modifiedtime','vtiger_crmentity',1,'70','modifiedtime','Modified Time',1,2,'',100,12,140,2,'T~O',1,NULL,'BAS',1,''),(64,828,'smcreatorid','vtiger_crmentity',1,'52','created_user_id','Created By',1,2,'',100,13,140,2,'V~O',3,NULL,'BAS',0,'');
/*!40000 ALTER TABLE `vtiger_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_field_seq`
--

DROP TABLE IF EXISTS `vtiger_field_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_field_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_field_seq`
--

LOCK TABLES `vtiger_field_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_field_seq` DISABLE KEYS */;
INSERT INTO `vtiger_field_seq` VALUES (828);
/*!40000 ALTER TABLE `vtiger_field_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_fieldmodulerel`
--

DROP TABLE IF EXISTS `vtiger_fieldmodulerel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_fieldmodulerel` (
  `fieldid` int(11) NOT NULL,
  `module` varchar(100) NOT NULL,
  `relmodule` varchar(100) NOT NULL,
  `status` varchar(10) DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  PRIMARY KEY (`fieldid`,`module`,`relmodule`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_fieldmodulerel`
--

LOCK TABLES `vtiger_fieldmodulerel` WRITE;
/*!40000 ALTER TABLE `vtiger_fieldmodulerel` DISABLE KEYS */;
INSERT INTO `vtiger_fieldmodulerel` VALUES (113,'Potentials','Accounts',NULL,0),(113,'Potentials','Contacts',NULL,1),(121,'Potentials','Campaigns',NULL,0),(157,'HelpDesk','Accounts',NULL,1),(157,'HelpDesk','Contacts',NULL,2),(536,'ServiceContracts','Accounts',NULL,2),(536,'ServiceContracts','Contacts',NULL,1),(585,'CobroPago','Accounts',NULL,1),(585,'CobroPago','Contacts',NULL,3),(585,'CobroPago','Leads',NULL,4),(585,'CobroPago','Vendors',NULL,2),(586,'CobroPago','Assets',NULL,11),(586,'CobroPago','Campaigns',NULL,5),(586,'CobroPago','HelpDesk',NULL,7),(586,'CobroPago','Invoice',NULL,1),(586,'CobroPago','Potentials',NULL,6),(586,'CobroPago','Products',NULL,12),(586,'CobroPago','Project',NULL,8),(586,'CobroPago','ProjectMilestone',NULL,9),(586,'CobroPago','ProjectTask',NULL,10),(586,'CobroPago','PurchaseOrder',NULL,2),(586,'CobroPago','Quotes',NULL,4),(586,'CobroPago','SalesOrder',NULL,3),(586,'CobroPago','ServiceContracts',NULL,14),(586,'CobroPago','Services',NULL,13),(601,'Assets','Products',NULL,1),(607,'Assets','Invoice',NULL,1),(612,'Assets','Accounts',NULL,1),(621,'ModComments','Accounts',NULL,3),(621,'ModComments','Contacts',NULL,2),(621,'ModComments','Leads',NULL,1),(621,'ModComments','Potentials',NULL,1),(621,'ModComments','Project',NULL,1),(621,'ModComments','ProjectTask',NULL,1),(623,'ModComments','ModComments',NULL,1),(626,'ProjectMilestone','Project',NULL,1),(637,'ProjectTask','Project',NULL,1),(655,'Project','Accounts',NULL,1),(655,'Project','Contacts',NULL,2),(692,'InventoryDetails','Products',NULL,1),(692,'InventoryDetails','Services',NULL,2),(693,'InventoryDetails','Invoice',NULL,3),(693,'InventoryDetails','PurchaseOrder',NULL,4),(693,'InventoryDetails','Quotes',NULL,1),(693,'InventoryDetails','SalesOrder',NULL,2),(694,'InventoryDetails','Accounts',NULL,1),(695,'InventoryDetails','Contacts',NULL,1),(696,'InventoryDetails','Vendors',NULL,1),(730,'GlobalVariable','cbMap',NULL,1),(737,'Accounts','Leads',NULL,1),(739,'Contacts','Leads',NULL,1),(741,'Potentials','Leads',NULL,1),(782,'Invoice','cbTermConditions',NULL,1),(783,'SalesOrder','cbTermConditions',NULL,1),(784,'Quotes','cbTermConditions',NULL,1),(785,'PurchaseOrder','cbTermConditions',NULL,1),(790,'cbCalendar','Accounts',NULL,2),(790,'cbCalendar','Campaigns',NULL,5),(790,'cbCalendar','CobroPago',NULL,11),(790,'cbCalendar','HelpDesk',NULL,4),(790,'cbCalendar','Invoice',NULL,10),(790,'cbCalendar','Leads',NULL,1),(790,'cbCalendar','Potentials',NULL,3),(790,'cbCalendar','PurchaseOrder',NULL,8),(790,'cbCalendar','Quotes',NULL,7),(790,'cbCalendar','SalesOrder',NULL,9),(790,'cbCalendar','Vendors',NULL,6),(791,'cbCalendar','Contacts',NULL,1),(805,'cbCalendar','cbCalendar',NULL,1),(821,'cbtranslation','Accounts',NULL,3),(821,'cbtranslation','Calendar',NULL,6),(821,'cbtranslation','Campaigns',NULL,17),(821,'cbtranslation','cbMap',NULL,25),(821,'cbtranslation','cbTermConditions',NULL,26),(821,'cbtranslation','cbupdater',NULL,21),(821,'cbtranslation','Contacts',NULL,2),(821,'cbtranslation','Documents',NULL,5),(821,'cbtranslation','Emails',NULL,7),(821,'cbtranslation','Faq',NULL,10),(821,'cbtranslation','GlobalVariable',NULL,23),(821,'cbtranslation','HelpDesk',NULL,8),(821,'cbtranslation','InventoryDetails',NULL,24),(821,'cbtranslation','Invoice',NULL,16),(821,'cbtranslation','Leads',NULL,4),(821,'cbtranslation','ModComments',NULL,22),(821,'cbtranslation','PBXManager',NULL,18),(821,'cbtranslation','Potentials',NULL,1),(821,'cbtranslation','PriceBooks',NULL,12),(821,'cbtranslation','Products',NULL,9),(821,'cbtranslation','PurchaseOrder',NULL,14),(821,'cbtranslation','Quotes',NULL,13),(821,'cbtranslation','SalesOrder',NULL,15),(821,'cbtranslation','ServiceContracts',NULL,19),(821,'cbtranslation','Services',NULL,20),(821,'cbtranslation','Vendors',NULL,11);
/*!40000 ALTER TABLE `vtiger_fieldmodulerel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_followuptype`
--

DROP TABLE IF EXISTS `vtiger_followuptype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_followuptype` (
  `followuptypeid` int(11) NOT NULL AUTO_INCREMENT,
  `followuptype` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`followuptypeid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_followuptype`
--

LOCK TABLES `vtiger_followuptype` WRITE;
/*!40000 ALTER TABLE `vtiger_followuptype` DISABLE KEYS */;
INSERT INTO `vtiger_followuptype` VALUES (1,'Call',1,536),(2,'Meeting',1,537),(3,'Emails',1,538),(4,'Task',1,539);
/*!40000 ALTER TABLE `vtiger_followuptype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_followuptype_seq`
--

DROP TABLE IF EXISTS `vtiger_followuptype_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_followuptype_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_followuptype_seq`
--

LOCK TABLES `vtiger_followuptype_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_followuptype_seq` DISABLE KEYS */;
INSERT INTO `vtiger_followuptype_seq` VALUES (4);
/*!40000 ALTER TABLE `vtiger_followuptype_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_formodule`
--

DROP TABLE IF EXISTS `vtiger_formodule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_formodule` (
  `formoduleid` int(11) NOT NULL AUTO_INCREMENT,
  `formodule` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`formoduleid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_formodule`
--

LOCK TABLES `vtiger_formodule` WRITE;
/*!40000 ALTER TABLE `vtiger_formodule` DISABLE KEYS */;
INSERT INTO `vtiger_formodule` VALUES (1,'Quotes',1,486),(2,'SalesOrder',1,487),(3,'PurchaseOrder',1,488),(4,'Invoice',1,489);
/*!40000 ALTER TABLE `vtiger_formodule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_formodule_seq`
--

DROP TABLE IF EXISTS `vtiger_formodule_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_formodule_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_formodule_seq`
--

LOCK TABLES `vtiger_formodule_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_formodule_seq` DISABLE KEYS */;
INSERT INTO `vtiger_formodule_seq` VALUES (4);
/*!40000 ALTER TABLE `vtiger_formodule_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_freetagged_objects`
--

DROP TABLE IF EXISTS `vtiger_freetagged_objects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_freetagged_objects` (
  `tag_id` int(20) NOT NULL DEFAULT '0',
  `tagger_id` int(20) NOT NULL DEFAULT '0',
  `object_id` int(20) NOT NULL DEFAULT '0',
  `tagged_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `module` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`tag_id`,`tagger_id`,`object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_freetagged_objects`
--

LOCK TABLES `vtiger_freetagged_objects` WRITE;
/*!40000 ALTER TABLE `vtiger_freetagged_objects` DISABLE KEYS */;
INSERT INTO `vtiger_freetagged_objects` VALUES (2,1,10,'2014-10-07 13:51:12','Accounts'),(3,1,12,'2014-10-07 13:51:13','Accounts'),(4,1,14,'2014-10-07 13:51:14','Accounts'),(5,1,16,'2014-10-07 13:51:15','Accounts'),(6,1,18,'2014-10-07 13:51:16','Accounts'),(7,1,20,'2014-10-07 13:51:16','Accounts'),(8,1,31,'2014-10-07 13:51:23','Contacts'),(9,1,93,'2014-10-07 13:51:56','Invoice'),(10,1,115,'2014-10-07 13:52:04','HelpDesk');
/*!40000 ALTER TABLE `vtiger_freetagged_objects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_freetags`
--

DROP TABLE IF EXISTS `vtiger_freetags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_freetags` (
  `id` int(19) NOT NULL,
  `tag` varchar(50) NOT NULL DEFAULT '',
  `raw_tag` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `tag_idx` (`tag`),
  KEY `multi_idx` (`id`,`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_freetags`
--

LOCK TABLES `vtiger_freetags` WRITE;
/*!40000 ALTER TABLE `vtiger_freetags` DISABLE KEYS */;
INSERT INTO `vtiger_freetags` VALUES (2,'X-CEED','X-CEED'),(3,'X-CEED','X-CEED'),(4,'X-CEED','X-CEED'),(5,'X-CEED','X-CEED'),(6,'X-CEED','X-CEED'),(7,'X-CEED','X-CEED'),(8,'X-CEED','X-CEED'),(9,'SO_vendtl','SO_vendtl'),(10,'vtiger_50usr','vtiger_50usr');
/*!40000 ALTER TABLE `vtiger_freetags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_freetags_seq`
--

DROP TABLE IF EXISTS `vtiger_freetags_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_freetags_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_freetags_seq`
--

LOCK TABLES `vtiger_freetags_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_freetags_seq` DISABLE KEYS */;
INSERT INTO `vtiger_freetags_seq` VALUES (10);
/*!40000 ALTER TABLE `vtiger_freetags_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_glacct`
--

DROP TABLE IF EXISTS `vtiger_glacct`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_glacct` (
  `glacctid` int(19) NOT NULL AUTO_INCREMENT,
  `glacct` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`glacctid`),
  UNIQUE KEY `glacct_glacct_idx` (`glacct`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_glacct`
--

LOCK TABLES `vtiger_glacct` WRITE;
/*!40000 ALTER TABLE `vtiger_glacct` DISABLE KEYS */;
INSERT INTO `vtiger_glacct` VALUES (1,'300-Sales-Software',1,51),(2,'301-Sales-Hardware',1,52),(3,'302-Rental-Income',1,53),(4,'303-Interest-Income',1,54),(5,'304-Sales-Software-Support',1,55),(6,'305-Sales Other',1,56),(7,'306-Internet Sales',1,57),(8,'307-Service-Hardware Labor',1,58),(9,'308-Sales-Books',1,59);
/*!40000 ALTER TABLE `vtiger_glacct` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_glacct_seq`
--

DROP TABLE IF EXISTS `vtiger_glacct_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_glacct_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_glacct_seq`
--

LOCK TABLES `vtiger_glacct_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_glacct_seq` DISABLE KEYS */;
INSERT INTO `vtiger_glacct_seq` VALUES (9);
/*!40000 ALTER TABLE `vtiger_glacct_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_globalvariable`
--

DROP TABLE IF EXISTS `vtiger_globalvariable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_globalvariable` (
  `globalvariableid` int(11) NOT NULL DEFAULT '0',
  `gvname` varchar(150) NOT NULL,
  `globalno` varchar(150) DEFAULT NULL,
  `default_check` varchar(3) DEFAULT NULL,
  `value` varchar(500) DEFAULT NULL,
  `mandatory` varchar(3) DEFAULT NULL,
  `blocked` varchar(3) DEFAULT NULL,
  `module_list` text,
  `category` varchar(150) DEFAULT NULL,
  `in_module_list` varchar(3) DEFAULT NULL,
  `bmapid` int(11) DEFAULT NULL,
  PRIMARY KEY (`globalvariableid`),
  KEY `gvname` (`gvname`),
  KEY `gvname_2` (`gvname`,`mandatory`),
  KEY `gvname_3` (`gvname`,`default_check`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_globalvariable`
--

LOCK TABLES `vtiger_globalvariable` WRITE;
/*!40000 ALTER TABLE `vtiger_globalvariable` DISABLE KEYS */;
INSERT INTO `vtiger_globalvariable` VALUES (320,'Application_ListView_Compute_Page_Count','glb-0000001','1','1','0','0','','Application','0',0);
/*!40000 ALTER TABLE `vtiger_globalvariable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_globalvariablecf`
--

DROP TABLE IF EXISTS `vtiger_globalvariablecf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_globalvariablecf` (
  `globalvariableid` int(11) NOT NULL,
  PRIMARY KEY (`globalvariableid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_globalvariablecf`
--

LOCK TABLES `vtiger_globalvariablecf` WRITE;
/*!40000 ALTER TABLE `vtiger_globalvariablecf` DISABLE KEYS */;
INSERT INTO `vtiger_globalvariablecf` VALUES (320);
/*!40000 ALTER TABLE `vtiger_globalvariablecf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_group2grouprel`
--

DROP TABLE IF EXISTS `vtiger_group2grouprel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_group2grouprel` (
  `groupid` int(19) NOT NULL,
  `containsgroupid` int(19) NOT NULL,
  PRIMARY KEY (`groupid`,`containsgroupid`),
  CONSTRAINT `fk_2_vtiger_group2grouprel` FOREIGN KEY (`groupid`) REFERENCES `vtiger_groups` (`groupid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_group2grouprel`
--

LOCK TABLES `vtiger_group2grouprel` WRITE;
/*!40000 ALTER TABLE `vtiger_group2grouprel` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_group2grouprel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_group2role`
--

DROP TABLE IF EXISTS `vtiger_group2role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_group2role` (
  `groupid` int(19) NOT NULL,
  `roleid` varchar(255) NOT NULL,
  PRIMARY KEY (`groupid`,`roleid`),
  KEY `fk_2_vtiger_group2role` (`roleid`),
  CONSTRAINT `fk_2_vtiger_group2role` FOREIGN KEY (`roleid`) REFERENCES `vtiger_role` (`roleid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_group2role`
--

LOCK TABLES `vtiger_group2role` WRITE;
/*!40000 ALTER TABLE `vtiger_group2role` DISABLE KEYS */;
INSERT INTO `vtiger_group2role` VALUES (3,'H2'),(4,'H3'),(2,'H4');
/*!40000 ALTER TABLE `vtiger_group2role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_group2rs`
--

DROP TABLE IF EXISTS `vtiger_group2rs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_group2rs` (
  `groupid` int(19) NOT NULL,
  `roleandsubid` varchar(255) NOT NULL,
  PRIMARY KEY (`groupid`,`roleandsubid`),
  KEY `fk_2_vtiger_group2rs` (`roleandsubid`),
  CONSTRAINT `fk_2_vtiger_group2rs` FOREIGN KEY (`roleandsubid`) REFERENCES `vtiger_role` (`roleid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_group2rs`
--

LOCK TABLES `vtiger_group2rs` WRITE;
/*!40000 ALTER TABLE `vtiger_group2rs` DISABLE KEYS */;
INSERT INTO `vtiger_group2rs` VALUES (3,'H3'),(4,'H3'),(2,'H5');
/*!40000 ALTER TABLE `vtiger_group2rs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_groups`
--

DROP TABLE IF EXISTS `vtiger_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_groups` (
  `groupid` int(19) NOT NULL,
  `groupname` varchar(100) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`groupid`),
  UNIQUE KEY `groups_groupname_idx` (`groupname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_groups`
--

LOCK TABLES `vtiger_groups` WRITE;
/*!40000 ALTER TABLE `vtiger_groups` DISABLE KEYS */;
INSERT INTO `vtiger_groups` VALUES (2,'Team Selling','Group Related to Sales'),(3,'Marketing Group','Group Related to Marketing Activities'),(4,'Support Group','Group Related to providing Support to Customers');
/*!40000 ALTER TABLE `vtiger_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_gvname`
--

DROP TABLE IF EXISTS `vtiger_gvname`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_gvname` (
  `gvnameid` int(11) NOT NULL AUTO_INCREMENT,
  `gvname` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`gvnameid`)
) ENGINE=InnoDB AUTO_INCREMENT=174 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_gvname`
--

LOCK TABLES `vtiger_gvname` WRITE;
/*!40000 ALTER TABLE `vtiger_gvname` DISABLE KEYS */;
INSERT INTO `vtiger_gvname` VALUES (1,'--none--',1,298),(30,'Application_JSCalendar_Load',1,327),(32,'Inventory_ProductService_Default',1,334),(33,'Debug_Record_Not_Found',1,335),(34,'Debug_Report_Query',1,336),(35,'Inventory_Product_Default_Units',1,337),(36,'Inventory_Service_Default_Units',1,338),(37,'Workflow_Maximum_Scheduled',1,339),(38,'Application_Billing_Address_Checked',1,340),(39,'Application_Shipping_Address_Checked',1,341),(40,'Inventory_Tax_Type_Default',1,342),(41,'Calendar_call_default_duration',1,343),(42,'Calendar_other_default_duration',1,344),(43,'Calendar_sort_users_by',1,345),(44,'Debug_Send_VtigerCron_Error',1,346),(45,'Import_Full_CSV',1,347),(46,'Lead_Convert_TransferToAccount',1,348),(47,'Application_Show_Copy_Address',1,349),(48,'SalesOrder_StatusOnInvoiceSave',1,350),(49,'Quote_StatusOnSalesOrderSave',1,351),(50,'GoogleCalendarSync_BaseUpdateMonths',1,352),(51,'GoogleCalendarSync_BaseCreateMonths',1,353),(52,'Report_Excel_Export_RowHeight',1,354),(53,'Calendar_Modules_Panel_Visible',1,355),(54,'Calendar_Default_Reminder_Minutes',1,356),(55,'Application_Global_Search_Binary',1,357),(56,'Calendar_Slot_Minutes',1,358),(57,'Users_ReplyTo_SecondEmail',1,359),(58,'Workflow_Send_Email_ToCCBCC',1,360),(59,'BusinessMapping_SalesOrder2Invoice',1,361),(60,'Calendar_Show_Inactive_Users',1,362),(61,'Campaign_CreatePotentialOnAccountRelation',1,363),(62,'Campaign_CreatePotentialOnContactRelation',1,364),(63,'BusinessMapping_PotentialOnCampaignRelation',1,365),(64,'Application_Global_Search_SelectedModules',1,366),(65,'Debug_ListView_Query',1,399),(66,'Application_Storage_Directory',1,400),(67,'Application_Storage_SaveStrategy',1,401),(68,'Application_OpenRecordInNewXOnRelatedList',1,402),(69,'Application_OpenRecordInNewXOnListView',1,403),(70,'Application_MaxFailedLoginAttempts',1,404),(71,'Application_ExpirePasswordAfterDays',1,405),(72,'Application_ListView_MaxColumns',1,406),(73,'Users_Default_Send_Email_Template',1,407),(74,'Accounts_BlockDuplicateName',1,408),(75,'Product_Copy_Bundle_OnDuplicate',1,409),(76,'Product_Show_Subproducts_Popup',1,410),(77,'Product_Permit_Relate_Bundle_Parent',1,411),(78,'Product_Permit_Subproduct_Be_Parent',1,412),(79,'Product_Maximum_Number_Images',1,413),(80,'Report_Send_Scheduled_ifEmpty',1,414),(81,'Debug_Popup_Query',1,416),(82,'Debug_Send_AdminLoginIPAuth_Error',1,417),(83,'Debug_Calculate_Response_Time',1,418),(84,'Application_AdminLoginIPs',1,419),(85,'Application_DetailView_ActionPanel_Open',1,420),(86,'Application_ListView_SearchPanel_Open',1,421),(87,'Application_TrackerMaxHistory',1,422),(88,'Application_Announcement',1,423),(89,'Application_Display_World_Clock',1,424),(90,'Application_Display_Calculator',1,425),(91,'Application_Display_Mini_Calendar',1,426),(92,'Application_Use_RTE',1,427),(93,'Application_Default_Action',1,428),(94,'Application_Default_Module',1,429),(95,'Application_Allow_Exports',1,430),(96,'Application_ListView_Max_Text_Length',1,431),(97,'Application_ListView_PageSize',1,432),(98,'Application_Upload_MaxSize',1,433),(99,'Application_Single_Pane_View',1,434),(100,'Application_Minimum_Cron_Frequency',1,435),(101,'Application_Customer_Portal_URL',1,436),(102,'Application_Help_URL',1,437),(103,'Application_UI_Name',1,438),(104,'Application_UI_Version',1,439),(105,'Application_UI_URL',1,440),(106,'Calendar_Show_Group_Events',1,441),(107,'CronTasks_cronWatcher_mailto',1,442),(108,'Webservice_showUserAdvancedBlock',1,443),(109,'Webservice_CORS_Enabled_Domains',1,444),(110,'Webservice_Enabled',1,445),(111,'WebService_Session_Life_Span',1,446),(112,'WebService_Session_Idle_Time',1,447),(114,'SOAP_CustomerPortal_Enabled',1,449),(115,'Import_Batch_Limit',1,450),(116,'Import_Scheduled_Limit',1,451),(117,'Workflow_GeoDistance_Country_Default',1,452),(118,'ModComments_DefaultCriteria',1,453),(119,'ModComments_DefaultBlockStatus',1,454),(120,'EMail_OpenTrackingEnabled',1,455),(121,'ToolTip_MaxFieldValueLength',1,456),(122,'HelpDesk_Support_EMail',1,457),(123,'HelpDesk_Support_Name',1,458),(124,'HelpDesk_Support_Reply_EMail',1,459),(126,'Application_Group_Selection_Permitted',1,475),(127,'Application_B2B',1,476),(128,'BusinessMapping_Quotes2Invoice',1,477),(129,'BusinessMapping_Quotes2SalesOrder',1,478),(130,'Mobile_Module_by_default',1,479),(131,'PBX_Get_Line_Prefix',1,480),(132,'Email_Attachments_Folder',1,481),(133,'Document_Folder_View',1,482),(134,'Report_ListView_PageSize',1,483),(135,'Report_MaxRows_OnScreen',1,484),(136,'Inventory_ListPrice_ReadOnly',1,485),(137,'User_AuthenticationType',1,490),(138,'Debug_Send_UserLoginIPAuth_Error',1,491),(139,'Application_Global_Search_TopModules',1,492),(140,'Application_Global_Search_Active',1,493),(141,'Application_UserLoginIPs',1,494),(142,'Application_DetailView_Inline_Edit',1,495),(143,'Application_DetailView_Record_Navigation',1,496),(144,'Application_ListView_Default_Sort_Order',1,497),(145,'Application_ListView_Record_Change_Indicator',1,498),(146,'Application_ListView_Default_Sorting',1,499),(147,'Application_ListView_Compute_Page_Count',1,500),(148,'Application_FirstTimeLogin_Template',1,501),(149,'Application_Permit_Assign_Up',1,502),(150,'Application_Permit_Assign_SameRole',1,503),(151,'Calendar_Slot_Event_Overlap',1,504),(152,'Calendar_Push_End_On_Start_Change',1,505),(153,'Mobile_Related_Modules',1,506),(154,'Users_Select_Inactive',1,507),(155,'User_2FAAuthentication',1,508),(156,'User_2FAAuthentication_SendMethod',1,509),(157,'Export_Field_Separator_Symbol',1,510),(158,'Export_RelatedField_GetValueFrom',1,511),(159,'Export_RelatedField_NameForSearch',1,512),(160,'Lead_Convert_OpportunitySelected',1,513),(161,'PBX_Unknown_CallerID',1,514),(162,'Workflow_GeoDistance_ServerIP',1,515),(163,'Workflow_GeoDistance_Email',1,516),(164,'EMail_Maximum_Number_Attachments',1,517),(165,'HelpDesk_Notify_Owner_EMail',1,518),(166,'HomePage_Widget_Group_Size',1,519),(167,'Report_MaxRelated_Modules',1,520),(168,'GContacts_Max_Results',1,521),(169,'CustomerPortal_PDF',1,522),(170,'CustomerPortal_PDFTemplate_Quote',1,523),(171,'CustomerPortal_PDFTemplate_SalesOrder',1,524),(172,'CustomerPortal_PDFTemplate_Invoice',1,525),(173,'CustomerPortal_PDFTemplate_PurchaseOrder',1,526);
/*!40000 ALTER TABLE `vtiger_gvname` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_gvname_seq`
--

DROP TABLE IF EXISTS `vtiger_gvname_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_gvname_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_gvname_seq`
--

LOCK TABLES `vtiger_gvname_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_gvname_seq` DISABLE KEYS */;
INSERT INTO `vtiger_gvname_seq` VALUES (173);
/*!40000 ALTER TABLE `vtiger_gvname_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_home_layout`
--

DROP TABLE IF EXISTS `vtiger_home_layout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_home_layout` (
  `userid` int(19) NOT NULL,
  `layout` int(19) NOT NULL DEFAULT '4',
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_home_layout`
--

LOCK TABLES `vtiger_home_layout` WRITE;
/*!40000 ALTER TABLE `vtiger_home_layout` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_home_layout` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_homedashbd`
--

DROP TABLE IF EXISTS `vtiger_homedashbd`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_homedashbd` (
  `stuffid` int(19) NOT NULL DEFAULT '0',
  `dashbdname` varchar(100) DEFAULT NULL,
  `dashbdtype` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`stuffid`),
  CONSTRAINT `fk_1_vtiger_homedashbd` FOREIGN KEY (`stuffid`) REFERENCES `vtiger_homestuff` (`stuffid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_homedashbd`
--

LOCK TABLES `vtiger_homedashbd` WRITE;
/*!40000 ALTER TABLE `vtiger_homedashbd` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_homedashbd` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_homedefault`
--

DROP TABLE IF EXISTS `vtiger_homedefault`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_homedefault` (
  `stuffid` int(19) NOT NULL DEFAULT '0',
  `hometype` varchar(30) NOT NULL,
  `maxentries` int(19) DEFAULT NULL,
  `setype` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`stuffid`),
  CONSTRAINT `fk_1_vtiger_homedefault` FOREIGN KEY (`stuffid`) REFERENCES `vtiger_homestuff` (`stuffid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_homedefault`
--

LOCK TABLES `vtiger_homedefault` WRITE;
/*!40000 ALTER TABLE `vtiger_homedefault` DISABLE KEYS */;
INSERT INTO `vtiger_homedefault` VALUES (1,'ALVT',5,'Accounts'),(2,'HDB',5,'Dashboard'),(3,'PLVT',5,'Potentials'),(4,'QLTQ',5,'Quotes'),(5,'CVLVT',5,'NULL'),(6,'HLT',5,'HelpDesk'),(7,'UA',5,'Calendar'),(8,'GRT',5,'NULL'),(9,'OLTSO',5,'SalesOrder'),(10,'ILTI',5,'Invoice'),(11,'MNL',5,'Leads'),(12,'OLTPO',5,'PurchaseOrder'),(13,'PA',5,'Calendar'),(14,'LTFAQ',5,'Faq');
/*!40000 ALTER TABLE `vtiger_homedefault` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_homemodule`
--

DROP TABLE IF EXISTS `vtiger_homemodule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_homemodule` (
  `stuffid` int(19) NOT NULL,
  `modulename` varchar(100) DEFAULT NULL,
  `maxentries` int(19) NOT NULL,
  `customviewid` int(19) NOT NULL,
  `setype` varchar(30) NOT NULL,
  PRIMARY KEY (`stuffid`),
  KEY `homemodule_customviewid_idx` (`customviewid`),
  CONSTRAINT `fk_1_vtiger_homemodule` FOREIGN KEY (`stuffid`) REFERENCES `vtiger_homestuff` (`stuffid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_homemodule`
--

LOCK TABLES `vtiger_homemodule` WRITE;
/*!40000 ALTER TABLE `vtiger_homemodule` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_homemodule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_homemoduleflds`
--

DROP TABLE IF EXISTS `vtiger_homemoduleflds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_homemoduleflds` (
  `stuffid` int(19) DEFAULT NULL,
  `fieldname` varchar(100) DEFAULT NULL,
  KEY `stuff_stuffid_idx` (`stuffid`),
  CONSTRAINT `fk_1_vtiger_homemoduleflds` FOREIGN KEY (`stuffid`) REFERENCES `vtiger_homemodule` (`stuffid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_homemoduleflds`
--

LOCK TABLES `vtiger_homemoduleflds` WRITE;
/*!40000 ALTER TABLE `vtiger_homemoduleflds` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_homemoduleflds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_homereportchart`
--

DROP TABLE IF EXISTS `vtiger_homereportchart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_homereportchart` (
  `stuffid` int(11) NOT NULL,
  `reportid` int(19) DEFAULT NULL,
  `reportcharttype` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`stuffid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_homereportchart`
--

LOCK TABLES `vtiger_homereportchart` WRITE;
/*!40000 ALTER TABLE `vtiger_homereportchart` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_homereportchart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_homerss`
--

DROP TABLE IF EXISTS `vtiger_homerss`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_homerss` (
  `stuffid` int(19) NOT NULL DEFAULT '0',
  `url` varchar(100) DEFAULT NULL,
  `maxentries` int(19) NOT NULL,
  PRIMARY KEY (`stuffid`),
  CONSTRAINT `fk_1_vtiger_homerss` FOREIGN KEY (`stuffid`) REFERENCES `vtiger_homestuff` (`stuffid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_homerss`
--

LOCK TABLES `vtiger_homerss` WRITE;
/*!40000 ALTER TABLE `vtiger_homerss` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_homerss` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_homestuff`
--

DROP TABLE IF EXISTS `vtiger_homestuff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_homestuff` (
  `stuffid` int(19) NOT NULL DEFAULT '0',
  `stuffsequence` int(19) NOT NULL DEFAULT '0',
  `stufftype` varchar(100) DEFAULT NULL,
  `userid` int(19) NOT NULL,
  `visible` int(10) NOT NULL DEFAULT '0',
  `stufftitle` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`stuffid`),
  KEY `fk_1_vtiger_homestuff` (`userid`),
  CONSTRAINT `fk_1_vtiger_homestuff` FOREIGN KEY (`userid`) REFERENCES `vtiger_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_homestuff`
--

LOCK TABLES `vtiger_homestuff` WRITE;
/*!40000 ALTER TABLE `vtiger_homestuff` DISABLE KEYS */;
INSERT INTO `vtiger_homestuff` VALUES (1,1,'Default',1,0,'Top Accounts'),(2,2,'Default',1,0,'Home Page Dashboard'),(3,3,'Default',1,0,'Top Potentials'),(4,4,'Default',1,0,'Top Quotes'),(5,5,'Default',1,0,'Key Metrics'),(6,6,'Default',1,0,'Top Trouble Tickets'),(7,7,'Default',1,0,'Upcoming Activities'),(8,8,'Default',1,0,'My Group Allocation'),(9,9,'Default',1,0,'Top Sales Orders'),(10,10,'Default',1,0,'Top Invoices'),(11,11,'Default',1,0,'My New Leads'),(12,12,'Default',1,0,'Top Purchase Orders'),(13,13,'Default',1,0,'Pending Activities'),(14,14,'Default',1,0,'My Recent FAQs'),(15,15,'Tag Cloud',1,1,'Tag Cloud');
/*!40000 ALTER TABLE `vtiger_homestuff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_homestuff_seq`
--

DROP TABLE IF EXISTS `vtiger_homestuff_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_homestuff_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_homestuff_seq`
--

LOCK TABLES `vtiger_homestuff_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_homestuff_seq` DISABLE KEYS */;
INSERT INTO `vtiger_homestuff_seq` VALUES (15);
/*!40000 ALTER TABLE `vtiger_homestuff_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_hour_format`
--

DROP TABLE IF EXISTS `vtiger_hour_format`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_hour_format` (
  `hour_formatid` int(11) NOT NULL AUTO_INCREMENT,
  `hour_format` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`hour_formatid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_hour_format`
--

LOCK TABLES `vtiger_hour_format` WRITE;
/*!40000 ALTER TABLE `vtiger_hour_format` DISABLE KEYS */;
INSERT INTO `vtiger_hour_format` VALUES (1,'12',1,368),(2,'24',1,369);
/*!40000 ALTER TABLE `vtiger_hour_format` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_hour_format_seq`
--

DROP TABLE IF EXISTS `vtiger_hour_format_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_hour_format_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_hour_format_seq`
--

LOCK TABLES `vtiger_hour_format_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_hour_format_seq` DISABLE KEYS */;
INSERT INTO `vtiger_hour_format_seq` VALUES (3);
/*!40000 ALTER TABLE `vtiger_hour_format_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_import_locks`
--

DROP TABLE IF EXISTS `vtiger_import_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_import_locks` (
  `vtiger_import_lock_id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `tabid` int(11) NOT NULL,
  `importid` int(11) NOT NULL,
  `locked_since` datetime DEFAULT NULL,
  PRIMARY KEY (`vtiger_import_lock_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_import_locks`
--

LOCK TABLES `vtiger_import_locks` WRITE;
/*!40000 ALTER TABLE `vtiger_import_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_import_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_import_maps`
--

DROP TABLE IF EXISTS `vtiger_import_maps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_import_maps` (
  `id` int(19) NOT NULL AUTO_INCREMENT,
  `name` varchar(36) NOT NULL,
  `module` varchar(36) NOT NULL,
  `content` longblob,
  `has_header` int(1) NOT NULL DEFAULT '1',
  `deleted` int(1) NOT NULL DEFAULT '0',
  `date_entered` datetime NOT NULL,
  `date_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `assigned_user_id` varchar(36) DEFAULT NULL,
  `is_published` varchar(3) NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`),
  KEY `import_maps_assigned_user_id_module_name_deleted_idx` (`assigned_user_id`,`module`,`name`,`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_import_maps`
--

LOCK TABLES `vtiger_import_maps` WRITE;
/*!40000 ALTER TABLE `vtiger_import_maps` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_import_maps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_import_queue`
--

DROP TABLE IF EXISTS `vtiger_import_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_import_queue` (
  `importid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `tabid` int(11) NOT NULL,
  `field_mapping` text,
  `default_values` text,
  `merge_type` int(11) DEFAULT NULL,
  `merge_fields` text,
  `status` int(11) DEFAULT '0',
  PRIMARY KEY (`importid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_import_queue`
--

LOCK TABLES `vtiger_import_queue` WRITE;
/*!40000 ALTER TABLE `vtiger_import_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_import_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_industry`
--

DROP TABLE IF EXISTS `vtiger_industry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_industry` (
  `industryid` int(19) NOT NULL AUTO_INCREMENT,
  `industry` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`industryid`),
  UNIQUE KEY `industry_industry_idx` (`industry`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_industry`
--

LOCK TABLES `vtiger_industry` WRITE;
/*!40000 ALTER TABLE `vtiger_industry` DISABLE KEYS */;
INSERT INTO `vtiger_industry` VALUES (1,'--None--',1,60),(2,'Apparel',1,61),(3,'Banking',1,62),(4,'Biotechnology',1,63),(5,'Chemicals',1,64),(6,'Communications',1,65),(7,'Construction',1,66),(8,'Consulting',1,67),(9,'Education',1,68),(10,'Electronics',1,69),(11,'Energy',1,70),(12,'Engineering',1,71),(13,'Entertainment',1,72),(14,'Environmental',1,73),(15,'Finance',1,74),(16,'Food & Beverage',1,75),(17,'Government',1,76),(18,'Healthcare',1,77),(19,'Hospitality',1,78),(20,'Insurance',1,79),(21,'Machinery',1,80),(22,'Manufacturing',1,81),(23,'Media',1,82),(24,'Not For Profit',1,83),(25,'Recreation',1,84),(26,'Retail',1,85),(27,'Shipping',1,86),(28,'Technology',1,87),(29,'Telecommunications',1,88),(30,'Transportation',1,89),(31,'Utilities',1,90),(32,'Other',1,91);
/*!40000 ALTER TABLE `vtiger_industry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_industry_seq`
--

DROP TABLE IF EXISTS `vtiger_industry_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_industry_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_industry_seq`
--

LOCK TABLES `vtiger_industry_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_industry_seq` DISABLE KEYS */;
INSERT INTO `vtiger_industry_seq` VALUES (32);
/*!40000 ALTER TABLE `vtiger_industry_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_inventorydetails`
--

DROP TABLE IF EXISTS `vtiger_inventorydetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_inventorydetails` (
  `inventorydetailsid` int(11) NOT NULL,
  `inventorydetails_no` varchar(50) DEFAULT NULL,
  `productid` varchar(150) DEFAULT NULL,
  `related_to` int(11) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `contact_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `sequence_no` int(4) DEFAULT NULL,
  `lineitem_id` int(19) DEFAULT NULL,
  `quantity` decimal(25,3) DEFAULT NULL,
  `listprice` decimal(28,6) DEFAULT NULL,
  `tax_percent` decimal(7,3) DEFAULT NULL,
  `extgross` decimal(28,6) DEFAULT NULL,
  `discount_percent` decimal(25,3) DEFAULT NULL,
  `discount_amount` decimal(28,6) DEFAULT NULL,
  `extnet` decimal(28,6) DEFAULT NULL,
  `linetax` decimal(28,6) DEFAULT NULL,
  `linetotal` decimal(28,6) DEFAULT NULL,
  `units_delivered_received` decimal(25,3) DEFAULT NULL,
  `line_completed` varchar(3) DEFAULT NULL,
  `cost_price` decimal(28,6) DEFAULT NULL,
  `cost_gross` decimal(28,6) DEFAULT NULL,
  `total_stock` decimal(28,6) DEFAULT NULL,
  PRIMARY KEY (`inventorydetailsid`),
  KEY `productid` (`productid`),
  KEY `related_to` (`related_to`),
  KEY `account_id` (`account_id`),
  KEY `contact_id` (`contact_id`),
  KEY `vendor_id` (`vendor_id`),
  KEY `lineitem_id` (`lineitem_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_inventorydetails`
--

LOCK TABLES `vtiger_inventorydetails` WRITE;
/*!40000 ALTER TABLE `vtiger_inventorydetails` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_inventorydetails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_inventorydetailscf`
--

DROP TABLE IF EXISTS `vtiger_inventorydetailscf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_inventorydetailscf` (
  `inventorydetailsid` int(11) NOT NULL,
  PRIMARY KEY (`inventorydetailsid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_inventorydetailscf`
--

LOCK TABLES `vtiger_inventorydetailscf` WRITE;
/*!40000 ALTER TABLE `vtiger_inventorydetailscf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_inventorydetailscf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_inventoryproductrel`
--

DROP TABLE IF EXISTS `vtiger_inventoryproductrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_inventoryproductrel` (
  `id` int(19) DEFAULT NULL,
  `productid` int(19) DEFAULT NULL,
  `sequence_no` int(4) DEFAULT NULL,
  `quantity` decimal(25,3) DEFAULT NULL,
  `listprice` decimal(28,6) DEFAULT NULL,
  `discount_percent` decimal(7,3) DEFAULT NULL,
  `discount_amount` decimal(28,6) DEFAULT NULL,
  `comment` text,
  `description` text,
  `incrementondel` int(11) NOT NULL DEFAULT '0',
  `lineitem_id` int(11) NOT NULL AUTO_INCREMENT,
  `tax1` decimal(7,3) DEFAULT NULL,
  `tax2` decimal(7,3) DEFAULT NULL,
  `tax3` decimal(7,3) DEFAULT NULL,
  PRIMARY KEY (`lineitem_id`),
  KEY `inventoryproductrel_id_idx` (`id`),
  KEY `inventoryproductrel_productid_idx` (`productid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_inventoryproductrel`
--

LOCK TABLES `vtiger_inventoryproductrel` WRITE;
/*!40000 ALTER TABLE `vtiger_inventoryproductrel` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_inventoryproductrel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_inventoryproductrel_seq`
--

DROP TABLE IF EXISTS `vtiger_inventoryproductrel_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_inventoryproductrel_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_inventoryproductrel_seq`
--

LOCK TABLES `vtiger_inventoryproductrel_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_inventoryproductrel_seq` DISABLE KEYS */;
INSERT INTO `vtiger_inventoryproductrel_seq` VALUES (39);
/*!40000 ALTER TABLE `vtiger_inventoryproductrel_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_inventoryshippingrel`
--

DROP TABLE IF EXISTS `vtiger_inventoryshippingrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_inventoryshippingrel` (
  `id` int(19) DEFAULT NULL,
  `shtax1` decimal(7,3) DEFAULT NULL,
  `shtax2` decimal(7,3) DEFAULT NULL,
  `shtax3` decimal(7,3) DEFAULT NULL,
  KEY `inventoryishippingrel_id_idx` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_inventoryshippingrel`
--

LOCK TABLES `vtiger_inventoryshippingrel` WRITE;
/*!40000 ALTER TABLE `vtiger_inventoryshippingrel` DISABLE KEYS */;
INSERT INTO `vtiger_inventoryshippingrel` VALUES (74,NULL,NULL,NULL),(74,NULL,NULL,NULL),(75,NULL,NULL,NULL),(75,NULL,NULL,NULL),(76,NULL,NULL,NULL),(76,NULL,NULL,NULL),(77,NULL,NULL,NULL),(77,NULL,NULL,NULL),(78,NULL,NULL,NULL),(78,NULL,NULL,NULL),(79,NULL,NULL,NULL),(79,NULL,NULL,NULL),(80,NULL,NULL,NULL),(80,NULL,NULL,NULL),(81,NULL,NULL,NULL),(81,NULL,NULL,NULL),(82,NULL,NULL,NULL),(82,NULL,NULL,NULL),(83,NULL,NULL,NULL),(83,NULL,NULL,NULL),(84,NULL,NULL,NULL),(84,NULL,NULL,NULL),(85,NULL,NULL,NULL),(85,NULL,NULL,NULL),(86,NULL,NULL,NULL),(86,NULL,NULL,NULL),(87,NULL,NULL,NULL),(87,NULL,NULL,NULL),(88,NULL,NULL,NULL),(88,NULL,NULL,NULL),(89,NULL,NULL,NULL),(89,NULL,NULL,NULL),(90,NULL,NULL,NULL),(90,NULL,NULL,NULL),(91,NULL,NULL,NULL),(91,NULL,NULL,NULL),(92,NULL,NULL,NULL),(92,NULL,NULL,NULL),(93,NULL,NULL,NULL),(93,NULL,NULL,NULL);
/*!40000 ALTER TABLE `vtiger_inventoryshippingrel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_inventorysubproductrel`
--

DROP TABLE IF EXISTS `vtiger_inventorysubproductrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_inventorysubproductrel` (
  `id` int(19) NOT NULL,
  `sequence_no` int(10) NOT NULL,
  `productid` int(19) NOT NULL,
  KEY `inventorysubproductrel_productid_idx` (`productid`),
  KEY `id` (`id`,`sequence_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_inventorysubproductrel`
--

LOCK TABLES `vtiger_inventorysubproductrel` WRITE;
/*!40000 ALTER TABLE `vtiger_inventorysubproductrel` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_inventorysubproductrel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_inventorytaxinfo`
--

DROP TABLE IF EXISTS `vtiger_inventorytaxinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_inventorytaxinfo` (
  `taxid` int(3) NOT NULL,
  `taxname` varchar(50) DEFAULT NULL,
  `taxlabel` varchar(50) DEFAULT NULL,
  `percentage` decimal(7,3) DEFAULT NULL,
  `deleted` int(1) DEFAULT NULL,
  PRIMARY KEY (`taxid`),
  KEY `inventorytaxinfo_taxname_idx` (`taxname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_inventorytaxinfo`
--

LOCK TABLES `vtiger_inventorytaxinfo` WRITE;
/*!40000 ALTER TABLE `vtiger_inventorytaxinfo` DISABLE KEYS */;
INSERT INTO `vtiger_inventorytaxinfo` VALUES (1,'tax1','VAT',4.500,0),(2,'tax2','Sales',10.000,0),(3,'tax3','Service',12.500,0);
/*!40000 ALTER TABLE `vtiger_inventorytaxinfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_inventorytaxinfo_seq`
--

DROP TABLE IF EXISTS `vtiger_inventorytaxinfo_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_inventorytaxinfo_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_inventorytaxinfo_seq`
--

LOCK TABLES `vtiger_inventorytaxinfo_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_inventorytaxinfo_seq` DISABLE KEYS */;
INSERT INTO `vtiger_inventorytaxinfo_seq` VALUES (3);
/*!40000 ALTER TABLE `vtiger_inventorytaxinfo_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_invitees`
--

DROP TABLE IF EXISTS `vtiger_invitees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_invitees` (
  `activityid` int(19) NOT NULL,
  `inviteeid` int(19) NOT NULL,
  PRIMARY KEY (`activityid`,`inviteeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_invitees`
--

LOCK TABLES `vtiger_invitees` WRITE;
/*!40000 ALTER TABLE `vtiger_invitees` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_invitees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_invoice`
--

DROP TABLE IF EXISTS `vtiger_invoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_invoice` (
  `invoiceid` int(19) NOT NULL DEFAULT '0',
  `subject` varchar(100) DEFAULT NULL,
  `salesorderid` int(19) DEFAULT NULL,
  `customerno` varchar(100) DEFAULT NULL,
  `contactid` int(19) DEFAULT NULL,
  `notes` varchar(100) DEFAULT NULL,
  `invoicedate` date DEFAULT NULL,
  `duedate` date DEFAULT NULL,
  `invoiceterms` varchar(100) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `adjustment` decimal(28,6) DEFAULT NULL,
  `salescommission` decimal(25,3) DEFAULT NULL,
  `exciseduty` decimal(25,3) DEFAULT NULL,
  `subtotal` decimal(28,6) DEFAULT NULL,
  `total` decimal(28,6) DEFAULT NULL,
  `taxtype` varchar(25) DEFAULT NULL,
  `discount_percent` decimal(25,3) DEFAULT NULL,
  `discount_amount` decimal(28,6) DEFAULT NULL,
  `s_h_amount` decimal(28,6) DEFAULT NULL,
  `shipping` varchar(100) DEFAULT NULL,
  `accountid` int(19) DEFAULT NULL,
  `terms_conditions` text,
  `purchaseorder` varchar(200) DEFAULT NULL,
  `invoicestatus` varchar(200) DEFAULT NULL,
  `invoice_no` varchar(100) DEFAULT NULL,
  `currency_id` int(19) NOT NULL DEFAULT '1',
  `conversion_rate` decimal(10,3) NOT NULL DEFAULT '1.000',
  `tandc` int(11) DEFAULT NULL,
  PRIMARY KEY (`invoiceid`),
  KEY `fk_2_vtiger_invoice` (`salesorderid`),
  KEY `invoice_contactid_idx` (`contactid`),
  KEY `invoice_accountid_idx` (`accountid`),
  CONSTRAINT `fk_2_vtiger_invoice` FOREIGN KEY (`salesorderid`) REFERENCES `vtiger_salesorder` (`salesorderid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_invoice`
--

LOCK TABLES `vtiger_invoice` WRITE;
/*!40000 ALTER TABLE `vtiger_invoice` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_invoice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_invoice_recurring_info`
--

DROP TABLE IF EXISTS `vtiger_invoice_recurring_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_invoice_recurring_info` (
  `salesorderid` int(11) NOT NULL DEFAULT '0',
  `recurring_frequency` varchar(200) DEFAULT NULL,
  `start_period` date DEFAULT NULL,
  `end_period` date DEFAULT NULL,
  `last_recurring_date` date DEFAULT NULL,
  `payment_duration` varchar(200) DEFAULT NULL,
  `invoice_status` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`salesorderid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_invoice_recurring_info`
--

LOCK TABLES `vtiger_invoice_recurring_info` WRITE;
/*!40000 ALTER TABLE `vtiger_invoice_recurring_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_invoice_recurring_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_invoicebillads`
--

DROP TABLE IF EXISTS `vtiger_invoicebillads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_invoicebillads` (
  `invoicebilladdressid` int(19) NOT NULL DEFAULT '0',
  `bill_city` varchar(30) DEFAULT NULL,
  `bill_code` varchar(30) DEFAULT NULL,
  `bill_country` varchar(30) DEFAULT NULL,
  `bill_state` varchar(30) DEFAULT NULL,
  `bill_street` varchar(250) DEFAULT NULL,
  `bill_pobox` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`invoicebilladdressid`),
  CONSTRAINT `fk_1_vtiger_invoicebillads` FOREIGN KEY (`invoicebilladdressid`) REFERENCES `vtiger_invoice` (`invoiceid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_invoicebillads`
--

LOCK TABLES `vtiger_invoicebillads` WRITE;
/*!40000 ALTER TABLE `vtiger_invoicebillads` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_invoicebillads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_invoicecf`
--

DROP TABLE IF EXISTS `vtiger_invoicecf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_invoicecf` (
  `invoiceid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`invoiceid`),
  CONSTRAINT `fk_1_vtiger_invoicecf` FOREIGN KEY (`invoiceid`) REFERENCES `vtiger_invoice` (`invoiceid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_invoicecf`
--

LOCK TABLES `vtiger_invoicecf` WRITE;
/*!40000 ALTER TABLE `vtiger_invoicecf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_invoicecf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_invoiceshipads`
--

DROP TABLE IF EXISTS `vtiger_invoiceshipads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_invoiceshipads` (
  `invoiceshipaddressid` int(19) NOT NULL DEFAULT '0',
  `ship_city` varchar(30) DEFAULT NULL,
  `ship_code` varchar(30) DEFAULT NULL,
  `ship_country` varchar(30) DEFAULT NULL,
  `ship_state` varchar(30) DEFAULT NULL,
  `ship_street` varchar(250) DEFAULT NULL,
  `ship_pobox` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`invoiceshipaddressid`),
  CONSTRAINT `fk_1_vtiger_invoiceshipads` FOREIGN KEY (`invoiceshipaddressid`) REFERENCES `vtiger_invoice` (`invoiceid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_invoiceshipads`
--

LOCK TABLES `vtiger_invoiceshipads` WRITE;
/*!40000 ALTER TABLE `vtiger_invoiceshipads` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_invoiceshipads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_invoicestatus`
--

DROP TABLE IF EXISTS `vtiger_invoicestatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_invoicestatus` (
  `invoicestatusid` int(19) NOT NULL AUTO_INCREMENT,
  `invoicestatus` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`invoicestatusid`),
  UNIQUE KEY `invoicestatus_invoiestatus_idx` (`invoicestatus`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_invoicestatus`
--

LOCK TABLES `vtiger_invoicestatus` WRITE;
/*!40000 ALTER TABLE `vtiger_invoicestatus` DISABLE KEYS */;
INSERT INTO `vtiger_invoicestatus` VALUES (1,'AutoCreated',0,92),(2,'Created',0,93),(3,'Approved',0,94),(4,'Sent',0,95),(5,'Credit Invoice',0,96),(6,'Paid',0,97),(7,'Cancel',1,367);
/*!40000 ALTER TABLE `vtiger_invoicestatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_invoicestatus_seq`
--

DROP TABLE IF EXISTS `vtiger_invoicestatus_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_invoicestatus_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_invoicestatus_seq`
--

LOCK TABLES `vtiger_invoicestatus_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_invoicestatus_seq` DISABLE KEYS */;
INSERT INTO `vtiger_invoicestatus_seq` VALUES (7);
/*!40000 ALTER TABLE `vtiger_invoicestatus_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_invoicestatushistory`
--

DROP TABLE IF EXISTS `vtiger_invoicestatushistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_invoicestatushistory` (
  `historyid` int(19) NOT NULL AUTO_INCREMENT,
  `invoiceid` int(19) NOT NULL,
  `accountname` varchar(100) DEFAULT NULL,
  `total` decimal(28,6) DEFAULT NULL,
  `invoicestatus` varchar(200) DEFAULT NULL,
  `lastmodified` datetime DEFAULT NULL,
  PRIMARY KEY (`historyid`),
  KEY `invoicestatushistory_invoiceid_idx` (`invoiceid`),
  CONSTRAINT `fk_1_vtiger_invoicestatushistory` FOREIGN KEY (`invoiceid`) REFERENCES `vtiger_invoice` (`invoiceid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_invoicestatushistory`
--

LOCK TABLES `vtiger_invoicestatushistory` WRITE;
/*!40000 ALTER TABLE `vtiger_invoicestatushistory` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_invoicestatushistory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_language`
--

DROP TABLE IF EXISTS `vtiger_language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `prefix` varchar(10) DEFAULT NULL,
  `label` varchar(30) DEFAULT NULL,
  `lastupdated` datetime DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `isdefault` int(1) DEFAULT NULL,
  `active` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_language`
--

LOCK TABLES `vtiger_language` WRITE;
/*!40000 ALTER TABLE `vtiger_language` DISABLE KEYS */;
INSERT INTO `vtiger_language` VALUES (1,'English','en_us','US English','2014-10-07 15:43:32',NULL,1,1),(2,'Brazilian','pt_br','PT Brasil','2017-01-06 23:28:52',NULL,0,0),(3,'BritishEnglish','en_gb','British English','2017-01-06 23:28:52',NULL,0,0),(4,'Deutsch','de_de','DE Deutsch','2017-01-06 23:28:53',NULL,0,0),(5,'Dutch','nl_nl','NL-Dutch','2017-01-06 23:28:53',NULL,0,0),(6,'French','fr_fr','Francais','2017-01-06 23:28:53',NULL,0,0),(7,'Hungarian','hu_hu','HU Magyar','2017-01-06 23:28:53',NULL,0,0),(8,'Mexican Spanish','es_mx','ES Mexico','2017-01-06 23:28:53',NULL,0,0),(9,'Spanish','es_es','ES Spanish','2017-01-06 23:28:53',NULL,0,0),(10,'Italian','it_it','IT Italian','2017-01-06 23:28:52',NULL,0,0);
/*!40000 ALTER TABLE `vtiger_language` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_language_seq`
--

DROP TABLE IF EXISTS `vtiger_language_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_language_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_language_seq`
--

LOCK TABLES `vtiger_language_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_language_seq` DISABLE KEYS */;
INSERT INTO `vtiger_language_seq` VALUES (10);
/*!40000 ALTER TABLE `vtiger_language_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_lead_view`
--

DROP TABLE IF EXISTS `vtiger_lead_view`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_lead_view` (
  `lead_viewid` int(19) NOT NULL AUTO_INCREMENT,
  `lead_view` varchar(200) NOT NULL,
  `sortorderid` int(19) NOT NULL DEFAULT '0',
  `presence` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`lead_viewid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_lead_view`
--

LOCK TABLES `vtiger_lead_view` WRITE;
/*!40000 ALTER TABLE `vtiger_lead_view` DISABLE KEYS */;
INSERT INTO `vtiger_lead_view` VALUES (1,'Today',0,1),(2,'Last 2 Days',1,1),(3,'Last Week',2,1);
/*!40000 ALTER TABLE `vtiger_lead_view` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_lead_view_seq`
--

DROP TABLE IF EXISTS `vtiger_lead_view_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_lead_view_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_lead_view_seq`
--

LOCK TABLES `vtiger_lead_view_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_lead_view_seq` DISABLE KEYS */;
INSERT INTO `vtiger_lead_view_seq` VALUES (3);
/*!40000 ALTER TABLE `vtiger_lead_view_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_leadaddress`
--

DROP TABLE IF EXISTS `vtiger_leadaddress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_leadaddress` (
  `leadaddressid` int(19) NOT NULL DEFAULT '0',
  `city` varchar(30) DEFAULT NULL,
  `code` varchar(30) DEFAULT NULL,
  `state` varchar(30) DEFAULT NULL,
  `pobox` varchar(30) DEFAULT NULL,
  `country` varchar(30) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `fax` varchar(50) DEFAULT NULL,
  `lane` varchar(250) DEFAULT NULL,
  `leadaddresstype` varchar(30) DEFAULT 'Billing',
  PRIMARY KEY (`leadaddressid`),
  CONSTRAINT `fk_1_vtiger_leadaddress` FOREIGN KEY (`leadaddressid`) REFERENCES `vtiger_leaddetails` (`leadid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_leadaddress`
--

LOCK TABLES `vtiger_leadaddress` WRITE;
/*!40000 ALTER TABLE `vtiger_leadaddress` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_leadaddress` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_leaddetails`
--

DROP TABLE IF EXISTS `vtiger_leaddetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_leaddetails` (
  `leadid` int(19) NOT NULL,
  `lead_no` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `interest` varchar(50) DEFAULT NULL,
  `firstname` varchar(40) DEFAULT NULL,
  `salutation` varchar(200) DEFAULT NULL,
  `lastname` varchar(80) NOT NULL,
  `company` varchar(100) NOT NULL,
  `annualrevenue` decimal(25,6) DEFAULT NULL,
  `industry` varchar(200) DEFAULT NULL,
  `campaign` varchar(30) DEFAULT NULL,
  `rating` varchar(200) DEFAULT NULL,
  `leadstatus` varchar(200) DEFAULT NULL,
  `leadsource` varchar(200) DEFAULT NULL,
  `converted` int(1) DEFAULT '0',
  `designation` varchar(50) DEFAULT 'SalesMan',
  `space` varchar(250) DEFAULT NULL,
  `comments` text,
  `priority` varchar(50) DEFAULT NULL,
  `demorequest` varchar(50) DEFAULT NULL,
  `partnercontact` varchar(50) DEFAULT NULL,
  `productversion` varchar(20) DEFAULT NULL,
  `product` varchar(50) DEFAULT NULL,
  `maildate` date DEFAULT NULL,
  `nextstepdate` date DEFAULT NULL,
  `fundingsituation` varchar(50) DEFAULT NULL,
  `transferdate` date DEFAULT NULL,
  `noofemployees` int(50) DEFAULT NULL,
  `secondaryemail` varchar(100) DEFAULT NULL,
  `assignleadchk` int(1) DEFAULT '0',
  `emailoptout` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`leadid`),
  KEY `leaddetails_converted_leadstatus_idx` (`converted`,`leadstatus`),
  KEY `email_idx` (`email`),
  CONSTRAINT `fk_1_vtiger_leaddetails` FOREIGN KEY (`leadid`) REFERENCES `vtiger_crmentity` (`crmid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_leaddetails`
--

LOCK TABLES `vtiger_leaddetails` WRITE;
/*!40000 ALTER TABLE `vtiger_leaddetails` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_leaddetails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_leadscf`
--

DROP TABLE IF EXISTS `vtiger_leadscf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_leadscf` (
  `leadid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`leadid`),
  CONSTRAINT `fk_1_vtiger_leadscf` FOREIGN KEY (`leadid`) REFERENCES `vtiger_leaddetails` (`leadid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_leadscf`
--

LOCK TABLES `vtiger_leadscf` WRITE;
/*!40000 ALTER TABLE `vtiger_leadscf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_leadscf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_leadsource`
--

DROP TABLE IF EXISTS `vtiger_leadsource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_leadsource` (
  `leadsourceid` int(19) NOT NULL AUTO_INCREMENT,
  `leadsource` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`leadsourceid`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_leadsource`
--

LOCK TABLES `vtiger_leadsource` WRITE;
/*!40000 ALTER TABLE `vtiger_leadsource` DISABLE KEYS */;
INSERT INTO `vtiger_leadsource` VALUES (1,'--None--',1,98),(2,'Cold Call',1,99),(3,'Existing Customer',1,100),(4,'Self Generated',1,101),(5,'Employee',1,102),(6,'Partner',1,103),(7,'Public Relations',1,104),(8,'Direct Mail',1,105),(9,'Conference',1,106),(10,'Trade Show',1,107),(11,'Web Site',1,108),(12,'Word of mouth',1,109),(13,'Other',1,110);
/*!40000 ALTER TABLE `vtiger_leadsource` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_leadsource_seq`
--

DROP TABLE IF EXISTS `vtiger_leadsource_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_leadsource_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_leadsource_seq`
--

LOCK TABLES `vtiger_leadsource_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_leadsource_seq` DISABLE KEYS */;
INSERT INTO `vtiger_leadsource_seq` VALUES (13);
/*!40000 ALTER TABLE `vtiger_leadsource_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_leadstage`
--

DROP TABLE IF EXISTS `vtiger_leadstage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_leadstage` (
  `leadstageid` int(19) NOT NULL AUTO_INCREMENT,
  `stage` varchar(200) NOT NULL,
  `sortorderid` int(19) NOT NULL DEFAULT '0',
  `presence` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`leadstageid`),
  UNIQUE KEY `leadstage_stage_idx` (`stage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_leadstage`
--

LOCK TABLES `vtiger_leadstage` WRITE;
/*!40000 ALTER TABLE `vtiger_leadstage` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_leadstage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_leadstatus`
--

DROP TABLE IF EXISTS `vtiger_leadstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_leadstatus` (
  `leadstatusid` int(19) NOT NULL AUTO_INCREMENT,
  `leadstatus` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`leadstatusid`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_leadstatus`
--

LOCK TABLES `vtiger_leadstatus` WRITE;
/*!40000 ALTER TABLE `vtiger_leadstatus` DISABLE KEYS */;
INSERT INTO `vtiger_leadstatus` VALUES (1,'--None--',1,111),(2,'Attempted to Contact',1,112),(3,'Cold',1,113),(4,'Contact in Future',1,114),(5,'Contacted',1,115),(6,'Hot',1,116),(7,'Junk Lead',1,117),(8,'Lost Lead',1,118),(9,'Not Contacted',1,119),(10,'Pre Qualified',1,120),(11,'Qualified',1,121),(12,'Warm',1,122);
/*!40000 ALTER TABLE `vtiger_leadstatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_leadstatus_seq`
--

DROP TABLE IF EXISTS `vtiger_leadstatus_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_leadstatus_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_leadstatus_seq`
--

LOCK TABLES `vtiger_leadstatus_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_leadstatus_seq` DISABLE KEYS */;
INSERT INTO `vtiger_leadstatus_seq` VALUES (12);
/*!40000 ALTER TABLE `vtiger_leadstatus_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_leadsubdetails`
--

DROP TABLE IF EXISTS `vtiger_leadsubdetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_leadsubdetails` (
  `leadsubscriptionid` int(19) NOT NULL DEFAULT '0',
  `website` varchar(255) DEFAULT NULL,
  `callornot` int(1) DEFAULT '0',
  `readornot` int(1) DEFAULT '0',
  `empct` int(10) DEFAULT '0',
  PRIMARY KEY (`leadsubscriptionid`),
  CONSTRAINT `fk_1_vtiger_leadsubdetails` FOREIGN KEY (`leadsubscriptionid`) REFERENCES `vtiger_leaddetails` (`leadid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_leadsubdetails`
--

LOCK TABLES `vtiger_leadsubdetails` WRITE;
/*!40000 ALTER TABLE `vtiger_leadsubdetails` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_leadsubdetails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_links`
--

DROP TABLE IF EXISTS `vtiger_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_links` (
  `linkid` int(11) NOT NULL,
  `tabid` int(11) DEFAULT NULL,
  `linktype` varchar(50) DEFAULT NULL,
  `linklabel` varchar(50) DEFAULT NULL,
  `linkurl` varchar(512) DEFAULT NULL,
  `linkicon` varchar(100) DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `handler_path` varchar(128) DEFAULT NULL,
  `handler_class` varchar(50) DEFAULT NULL,
  `handler` varchar(50) DEFAULT NULL,
  `onlyonmymodule` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`linkid`),
  KEY `link_tabidtype_idx` (`tabid`,`linktype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_links`
--

LOCK TABLES `vtiger_links` WRITE;
/*!40000 ALTER TABLE `vtiger_links` DISABLE KEYS */;
INSERT INTO `vtiger_links` VALUES (1,6,'DETAILVIEWBASIC','LBL_ADD_NOTE','index.php?module=Documents&action=EditView&return_module=$MODULE$&return_action=DetailView&return_id=$RECORD$&parent_id=$RECORD$&createmode=link','themes/images/bookMark.gif',0,NULL,NULL,NULL,0),(2,6,'DETAILVIEWBASIC','LBL_SHOW_ACCOUNT_HIERARCHY','index.php?module=Accounts&action=AccountHierarchy&accountid=$RECORD$','themes/images/hierarchy_color16.png',0,NULL,NULL,NULL,0),(3,7,'DETAILVIEWBASIC','LBL_ADD_NOTE','index.php?module=Documents&action=EditView&return_module=$MODULE$&return_action=DetailView&return_id=$RECORD$&parent_id=$RECORD$&createmode=link','themes/images/bookMark.gif',0,NULL,NULL,NULL,0),(4,4,'DETAILVIEWBASIC','LBL_ADD_NOTE','index.php?module=Documents&action=EditView&return_module=$MODULE$&return_action=DetailView&return_id=$RECORD$&parent_id=$RECORD$&createmode=link','themes/images/bookMark.gif',0,NULL,NULL,NULL,0),(5,35,'HEADERSCRIPT','ModTrackerCommon_JS','modules/ModTracker/ModTrackerCommon.js','',0,NULL,NULL,NULL,0),(6,41,'DETAILVIEWWIDGET','Execute','module=cbupdater&action=cbupdaterAjax&file=cbupdaterWidget&record=$RECORD$','',0,NULL,NULL,NULL,0),(7,41,'LISTVIEWBASIC','GetUpdates','javascript:gotourl(\'index.php?module=cbupdater&action=getupdates\')','',0,NULL,NULL,NULL,0),(8,41,'LISTVIEWBASIC','Apply','javascript:cbupdater.applyselected()','',1,NULL,NULL,NULL,0),(9,41,'LISTVIEWBASIC','ApplyAll','javascript:gotourl(\'index.php?module=cbupdater&action=dowork&idstring=all\')','',2,NULL,NULL,NULL,0),(10,41,'LISTVIEWBASIC','Undo','javascript:cbupdater.undoselected()','',3,NULL,NULL,NULL,0),(11,41,'LISTVIEWBASIC','ExportXML','javascript:cbupdater.exportselected()','',4,NULL,NULL,NULL,0),(12,23,'DETAILVIEWBASIC','Add Payment','index.php?module=CobroPago&action=EditView&related_id=$RECORD$&return_module=Invoice&return_id=$RECORD$&return_action=DetailView','themes/images/actionGenerateInvoice.gif',0,NULL,NULL,NULL,0),(13,22,'DETAILVIEWBASIC','Add Payment','index.php?module=CobroPago&action=EditView&related_id=$RECORD$&return_module=SalesOrder&return_id=$RECORD$&return_action=DetailView','themes/images/actionGenerateInvoice.gif',0,NULL,NULL,NULL,0),(14,21,'DETAILVIEWBASIC','Add Payment','index.php?module=CobroPago&action=EditView&related_id=$RECORD$&return_module=PurchaseOrder&return_id=$RECORD$&return_action=DetailView','themes/images/actionGenerateInvoice.gif',0,NULL,NULL,NULL,0),(15,20,'DETAILVIEWBASIC','Add Payment','index.php?module=CobroPago&action=EditView&related_id=$RECORD$&return_module=Quotes&return_id=$RECORD$&return_action=DetailView','themes/images/actionGenerateInvoice.gif',0,NULL,NULL,NULL,0),(16,47,'HEADERSCRIPT','ModCommentsCommonHeaderScript','modules/ModComments/ModCommentsCommon.js','',0,NULL,NULL,NULL,0),(17,7,'DETAILVIEWWIDGET','DetailViewBlockCommentWidget','block://ModComments:modules/ModComments/ModComments.php','',5,NULL,NULL,NULL,0),(18,4,'DETAILVIEWWIDGET','DetailViewBlockCommentWidget','block://ModComments:modules/ModComments/ModComments.php','',7,NULL,NULL,NULL,0),(19,6,'DETAILVIEWWIDGET','DetailViewBlockCommentWidget','block://ModComments:modules/ModComments/ModComments.php','',5,NULL,NULL,NULL,0),(20,2,'DETAILVIEWWIDGET','DetailViewBlockCommentWidget','block://ModComments:modules/ModComments/ModComments.php','',4,NULL,NULL,NULL,0),(21,49,'DETAILVIEWBASIC','Add Note','index.php?module=Documents&action=EditView&return_module=ProjectTask&return_action=DetailView&return_id=$RECORD$&parent_id=$RECORD$&createmode=link','',0,NULL,NULL,NULL,0),(22,49,'DETAILVIEWWIDGET','DetailViewBlockCommentWidget','block://ModComments:modules/ModComments/ModComments.php','',4,NULL,NULL,NULL,0),(23,50,'DETAILVIEWBASIC','Add Project Task','index.php?module=ProjectTask&action=EditView&projectid=$RECORD$&return_module=Project&return_action=DetailView&return_id=$RECORD$','',0,NULL,NULL,NULL,0),(24,50,'DETAILVIEWBASIC','Add Note','index.php?module=Documents&action=EditView&return_module=Project&return_action=DetailView&return_id=$RECORD$&parent_id=$RECORD$&createmode=link','',1,NULL,NULL,NULL,0),(25,50,'DETAILVIEWWIDGET','DetailViewBlockCommentWidget','block://ModComments:modules/ModComments/ModComments.php','',4,NULL,NULL,NULL,0),(26,52,'DETAILVIEWBASIC','LBL_CHECK_STATUS','javascript:SMSNotifier.checkstatus($RECORD$)','themes/images/reload.gif',0,NULL,NULL,NULL,0),(28,52,'HEADERSCRIPT','SMSNotifierCommonJS','modules/SMSNotifier/SMSNotifierCommon.js','',0,NULL,NULL,NULL,0),(29,7,'LISTVIEWBASIC','Send SMS','SMSNotifierCommon.displaySelectWizard(this, \'$MODULE$\');','',0,NULL,NULL,NULL,0),(30,7,'DETAILVIEWBASIC','Send SMS','javascript:SMSNotifierCommon.displaySelectWizard_DetailView(\'$MODULE$\', \'$RECORD$\');','',0,NULL,NULL,NULL,0),(31,4,'LISTVIEWBASIC','Send SMS','SMSNotifierCommon.displaySelectWizard(this, \'$MODULE$\');','',0,NULL,NULL,NULL,0),(32,4,'DETAILVIEWBASIC','Send SMS','javascript:SMSNotifierCommon.displaySelectWizard_DetailView(\'$MODULE$\', \'$RECORD$\');','',0,NULL,NULL,NULL,0),(33,6,'LISTVIEWBASIC','Send SMS','SMSNotifierCommon.displaySelectWizard(this, \'$MODULE$\');','',0,NULL,NULL,NULL,0),(34,6,'DETAILVIEWBASIC','Send SMS','javascript:SMSNotifierCommon.displaySelectWizard_DetailView(\'$MODULE$\', \'$RECORD$\');','',0,NULL,NULL,NULL,0),(35,53,'HEADERSCRIPT','ToolTip_HeaderScript','modules/Tooltip/TooltipHeaderScript.js','',0,NULL,NULL,NULL,0),(36,55,'HEADERCSS','Calendar4You_HeaderStyle1','modules/Calendar4You/fullcalendar/fullcalendar.css','',0,NULL,NULL,NULL,1),(39,55,'HEADERSCRIPT','Calendar4You_HeaderScript3','modules/Calendar4You/fullcalendar/fullcalendar.js','',2,NULL,NULL,NULL,1),(40,55,'HEADERSCRIPT','Calendar4You_HeaderScript4','modules/Calendar4You/Calendar4You.js','',3,NULL,NULL,NULL,1),(41,14,'DETAILVIEWWIDGET','Upload Images','module=Products&action=ProductsAjax&file=MassUploadImage&record=$RECORD$','',0,NULL,NULL,NULL,0),(42,56,'DETAILVIEWBASIC','Test','javascript:gotourl(\'index.php?module=GlobalVariable&action=TestGlobalVar&parenttab=Tools\')','',0,NULL,NULL,NULL,0),(43,56,'LISTVIEWBASIC','Test','javascript:gotourl(\'index.php?module=GlobalVariable&action=TestGlobalVar&parenttab=Tools\')','',0,NULL,NULL,NULL,0),(44,41,'LISTVIEWBASIC','ImportXML','javascript:gotourl(\'index.php?module=cbupdater&action=importxml\')','',5,NULL,NULL,NULL,0),(45,4,'DETAILVIEWBASIC','LBL_SHOW_CONTACT_HIERARCHY','index.php?module=Contacts&action=ContactHierarchy&contactid=$RECORD$','themes/images/hierarchy_color16.png',5,NULL,NULL,NULL,0),(46,58,'DETAILVIEWBASIC','Generate Map','javascript:showMapWindow($RECORD$);','',0,NULL,NULL,NULL,0),(47,56,'LISTVIEWBASIC','Definitions','javascript:gotourl(\'index.php?module=GlobalVariable&action=GlobalVariableDefinitions&parenttab=Tools\')','',4,NULL,NULL,NULL,0),(48,8,'LISTVIEWBASIC','LNK_DOWNLOAD','massDownload();','',0,NULL,NULL,NULL,0),(49,14,'HEADERCSS','ProductDropzoneCSS','include/dropzone/dropzone.css','0',0,NULL,NULL,NULL,1),(50,14,'HEADERCSS','ProductDropzoneCustomCSS','include/dropzone/custom.css','1',0,NULL,NULL,NULL,1),(51,14,'HEADERSCRIPT','ProductDropzoneJS','include/dropzone/dropzone.js','',2,NULL,NULL,NULL,1),(52,33,'HEADERCSS','MailManagerDropzoneCSS','include/dropzone/dropzone.css','0',0,NULL,NULL,NULL,1),(53,33,'HEADERCSS','MailManagerDropzoneCustomCSS','include/dropzone/custom.css','1',0,NULL,NULL,NULL,1),(54,33,'HEADERSCRIPT','MailManagerDropzoneJS','include/dropzone/dropzone.js','',2,NULL,NULL,NULL,1),(56,55,'HEADERCSS','Calendar4You_HeaderStyle2','modules/Calendar4You/fullcalendar/themes/cupertino/jquery-ui.min.css','',1,NULL,NULL,NULL,1),(57,7,'LISTVIEWBASIC','LBL_TGL_HISTORICOS','javascript: toggle_converted();','',0,NULL,NULL,NULL,0),(58,6,'HEADERSCRIPT','InventoryJS','include/js/Inventory.js','',1,NULL,NULL,NULL,1),(59,20,'HEADERSCRIPT','InventoryJS','include/js/Inventory.js','',1,NULL,NULL,NULL,1),(60,22,'HEADERSCRIPT','InventoryJS','include/js/Inventory.js','',1,NULL,NULL,NULL,1),(61,21,'HEADERSCRIPT','InventoryJS','include/js/Inventory.js','',1,NULL,NULL,NULL,1),(62,23,'HEADERSCRIPT','InventoryJS','include/js/Inventory.js','',1,NULL,NULL,NULL,1),(63,6,'HEADERSCRIPT','MailJS','include/js/Mail.js','',1,NULL,NULL,NULL,1),(64,4,'HEADERSCRIPT','MailJS','include/js/Mail.js','',1,NULL,NULL,NULL,1),(65,13,'HEADERSCRIPT','MailJS','include/js/Mail.js','',1,NULL,NULL,NULL,1),(66,7,'HEADERSCRIPT','MailJS','include/js/Mail.js','',1,NULL,NULL,NULL,1),(67,2,'HEADERSCRIPT','MailJS','include/js/Mail.js','',1,NULL,NULL,NULL,1),(68,50,'HEADERSCRIPT','MailJS','include/js/Mail.js','',1,NULL,NULL,NULL,1),(69,49,'HEADERSCRIPT','MailJS','include/js/Mail.js','',1,NULL,NULL,NULL,1),(70,29,'HEADERSCRIPT','MailJS','include/js/Mail.js','',1,NULL,NULL,NULL,1),(71,18,'HEADERSCRIPT','MailJS','include/js/Mail.js','',1,NULL,NULL,NULL,1),(72,14,'HEADERSCRIPT','MultifileJS','modules/Products/multifile.js','',1,NULL,NULL,NULL,1),(73,64,'LISTVIEWBASIC','Export CSV','javascript:exportLanguageCSV()','',0,'','','',0),(74,64,'LISTVIEWBASIC','Export JSON','javascript:exportLanguageJSON()','',0,'','','',0);
/*!40000 ALTER TABLE `vtiger_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_links_seq`
--

DROP TABLE IF EXISTS `vtiger_links_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_links_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_links_seq`
--

LOCK TABLES `vtiger_links_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_links_seq` DISABLE KEYS */;
INSERT INTO `vtiger_links_seq` VALUES (74);
/*!40000 ALTER TABLE `vtiger_links_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_loginhistory`
--

DROP TABLE IF EXISTS `vtiger_loginhistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_loginhistory` (
  `login_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) NOT NULL,
  `user_ip` varchar(25) NOT NULL,
  `logout_time` timestamp NULL DEFAULT NULL,
  `login_time` timestamp NULL DEFAULT NULL,
  `status` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`login_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_loginhistory`
--

LOCK TABLES `vtiger_loginhistory` WRITE;
/*!40000 ALTER TABLE `vtiger_loginhistory` DISABLE KEYS */;
INSERT INTO `vtiger_loginhistory` VALUES (1,'admin','127.0.0.1','2014-10-07 19:55:14','2014-10-07 13:52:20','Signed off'),(2,'admin','127.0.0.1',NULL,'2014-11-29 23:01:25','Signed in'),(3,'admin','127.0.0.1','2016-04-24 08:42:38','2016-04-24 08:30:31','Signed off'),(4,'admin','127.0.0.1','2016-06-28 20:30:17','2016-06-28 16:26:08','Signed off'),(5,'admin','172.17.0.1','2016-10-25 20:25:25','2016-10-25 20:23:34','Signed off'),(6,'admin','::1','2017-01-06 22:18:20','2017-01-06 22:15:37','Signed off'),(7,'admin','::1','2017-01-06 22:29:56','2017-01-06 22:29:08','Signed off'),(8,'admin','127.0.0.1','2017-01-10 20:21:41','2017-01-10 20:15:18','Signed off'),(9,'admin','127.0.0.1','2017-02-01 10:30:52','2017-02-01 10:29:48','Signed off'),(10,'admin','::1','2017-09-02 08:00:20','2017-09-02 07:21:31','Signed off');
/*!40000 ALTER TABLE `vtiger_loginhistory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_mail_accounts`
--

DROP TABLE IF EXISTS `vtiger_mail_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_mail_accounts` (
  `account_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `display_name` varchar(50) DEFAULT NULL,
  `mail_id` varchar(50) DEFAULT NULL,
  `account_name` varchar(50) DEFAULT NULL,
  `mail_protocol` varchar(20) DEFAULT NULL,
  `mail_username` varchar(50) NOT NULL,
  `mail_password` varchar(250) NOT NULL,
  `mail_servername` varchar(50) DEFAULT NULL,
  `box_refresh` int(10) DEFAULT NULL,
  `mails_per_page` int(10) DEFAULT NULL,
  `ssltype` varchar(50) DEFAULT NULL,
  `sslmeth` varchar(50) DEFAULT NULL,
  `int_mailer` int(1) DEFAULT '0',
  `status` varchar(10) DEFAULT NULL,
  `set_default` int(2) DEFAULT NULL,
  PRIMARY KEY (`account_id`),
  KEY `userid_idx` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_mail_accounts`
--

LOCK TABLES `vtiger_mail_accounts` WRITE;
/*!40000 ALTER TABLE `vtiger_mail_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_mail_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_mailmanager_mailattachments`
--

DROP TABLE IF EXISTS `vtiger_mailmanager_mailattachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_mailmanager_mailattachments` (
  `userid` int(11) DEFAULT NULL,
  `muid` int(11) DEFAULT NULL,
  `aname` varchar(100) DEFAULT NULL,
  `lastsavedtime` int(11) DEFAULT NULL,
  `attachid` int(19) NOT NULL,
  `path` varchar(200) NOT NULL,
  KEY `userid_muid_idx` (`userid`,`muid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_mailmanager_mailattachments`
--

LOCK TABLES `vtiger_mailmanager_mailattachments` WRITE;
/*!40000 ALTER TABLE `vtiger_mailmanager_mailattachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_mailmanager_mailattachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_mailmanager_mailrecord`
--

DROP TABLE IF EXISTS `vtiger_mailmanager_mailrecord`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_mailmanager_mailrecord` (
  `userid` int(11) DEFAULT NULL,
  `mfrom` varchar(255) DEFAULT NULL,
  `mto` varchar(255) DEFAULT NULL,
  `mcc` varchar(500) DEFAULT NULL,
  `mbcc` varchar(500) DEFAULT NULL,
  `mdate` varchar(20) DEFAULT NULL,
  `msubject` varchar(500) DEFAULT NULL,
  `mbody` text,
  `mcharset` varchar(10) DEFAULT NULL,
  `misbodyhtml` int(1) DEFAULT NULL,
  `mplainmessage` text,
  `mhtmlmessage` text,
  `muniqueid` varchar(500) DEFAULT NULL,
  `mbodyparsed` int(1) DEFAULT NULL,
  `muid` int(11) DEFAULT NULL,
  `lastsavedtime` int(11) DEFAULT NULL,
  KEY `userid_lastsavedtime_idx` (`userid`,`lastsavedtime`),
  KEY `userid_muid_idx` (`userid`,`muid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_mailmanager_mailrecord`
--

LOCK TABLES `vtiger_mailmanager_mailrecord` WRITE;
/*!40000 ALTER TABLE `vtiger_mailmanager_mailrecord` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_mailmanager_mailrecord` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_mailmanager_mailrel`
--

DROP TABLE IF EXISTS `vtiger_mailmanager_mailrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_mailmanager_mailrel` (
  `mailuid` varchar(999) DEFAULT NULL,
  `crmid` int(11) DEFAULT NULL,
  `emailid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_mailmanager_mailrel`
--

LOCK TABLES `vtiger_mailmanager_mailrel` WRITE;
/*!40000 ALTER TABLE `vtiger_mailmanager_mailrel` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_mailmanager_mailrel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_mailscanner`
--

DROP TABLE IF EXISTS `vtiger_mailscanner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_mailscanner` (
  `scannerid` int(11) NOT NULL AUTO_INCREMENT,
  `scannername` varchar(30) DEFAULT NULL,
  `server` varchar(100) DEFAULT NULL,
  `protocol` varchar(10) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `ssltype` varchar(10) DEFAULT NULL,
  `sslmethod` varchar(30) DEFAULT NULL,
  `connecturl` varchar(255) DEFAULT NULL,
  `searchfor` varchar(10) DEFAULT NULL,
  `markas` varchar(10) DEFAULT NULL,
  `isvalid` int(1) DEFAULT NULL,
  PRIMARY KEY (`scannerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_mailscanner`
--

LOCK TABLES `vtiger_mailscanner` WRITE;
/*!40000 ALTER TABLE `vtiger_mailscanner` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_mailscanner` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_mailscanner_actions`
--

DROP TABLE IF EXISTS `vtiger_mailscanner_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_mailscanner_actions` (
  `actionid` int(11) NOT NULL AUTO_INCREMENT,
  `scannerid` int(11) DEFAULT NULL,
  `actiontype` varchar(10) DEFAULT NULL,
  `module` varchar(30) DEFAULT NULL,
  `lookup` varchar(30) DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  PRIMARY KEY (`actionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_mailscanner_actions`
--

LOCK TABLES `vtiger_mailscanner_actions` WRITE;
/*!40000 ALTER TABLE `vtiger_mailscanner_actions` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_mailscanner_actions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_mailscanner_folders`
--

DROP TABLE IF EXISTS `vtiger_mailscanner_folders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_mailscanner_folders` (
  `folderid` int(11) NOT NULL AUTO_INCREMENT,
  `scannerid` int(11) DEFAULT NULL,
  `foldername` varchar(255) DEFAULT NULL,
  `lastscan` varchar(30) DEFAULT NULL,
  `rescan` int(1) DEFAULT NULL,
  `enabled` int(1) DEFAULT NULL,
  PRIMARY KEY (`folderid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_mailscanner_folders`
--

LOCK TABLES `vtiger_mailscanner_folders` WRITE;
/*!40000 ALTER TABLE `vtiger_mailscanner_folders` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_mailscanner_folders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_mailscanner_ids`
--

DROP TABLE IF EXISTS `vtiger_mailscanner_ids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_mailscanner_ids` (
  `scannerid` int(11) DEFAULT NULL,
  `messageid` varchar(512) DEFAULT NULL,
  `crmid` int(11) DEFAULT NULL,
  KEY `scanner_message_ids_idx` (`scannerid`,`messageid`(255)),
  KEY `messageids_crmid_idx` (`crmid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_mailscanner_ids`
--

LOCK TABLES `vtiger_mailscanner_ids` WRITE;
/*!40000 ALTER TABLE `vtiger_mailscanner_ids` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_mailscanner_ids` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_mailscanner_ruleactions`
--

DROP TABLE IF EXISTS `vtiger_mailscanner_ruleactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_mailscanner_ruleactions` (
  `ruleid` int(11) DEFAULT NULL,
  `actionid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_mailscanner_ruleactions`
--

LOCK TABLES `vtiger_mailscanner_ruleactions` WRITE;
/*!40000 ALTER TABLE `vtiger_mailscanner_ruleactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_mailscanner_ruleactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_mailscanner_rules`
--

DROP TABLE IF EXISTS `vtiger_mailscanner_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_mailscanner_rules` (
  `ruleid` int(11) NOT NULL AUTO_INCREMENT,
  `scannerid` int(11) DEFAULT NULL,
  `fromaddress` varchar(255) DEFAULT NULL,
  `toaddress` varchar(255) DEFAULT NULL,
  `subjectop` varchar(20) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `bodyop` varchar(20) DEFAULT NULL,
  `body` varchar(255) DEFAULT NULL,
  `matchusing` varchar(5) DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  `assign_to` int(11) DEFAULT NULL,
  PRIMARY KEY (`ruleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_mailscanner_rules`
--

LOCK TABLES `vtiger_mailscanner_rules` WRITE;
/*!40000 ALTER TABLE `vtiger_mailscanner_rules` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_mailscanner_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_manufacturer`
--

DROP TABLE IF EXISTS `vtiger_manufacturer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_manufacturer` (
  `manufacturerid` int(19) NOT NULL AUTO_INCREMENT,
  `manufacturer` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`manufacturerid`),
  UNIQUE KEY `manufacturer_manufacturer_idx` (`manufacturer`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_manufacturer`
--

LOCK TABLES `vtiger_manufacturer` WRITE;
/*!40000 ALTER TABLE `vtiger_manufacturer` DISABLE KEYS */;
INSERT INTO `vtiger_manufacturer` VALUES (1,'--None--',1,123),(2,'AltvetPet Inc.',1,124),(3,'LexPon Inc.',1,125),(4,'MetBeat Corp',1,126);
/*!40000 ALTER TABLE `vtiger_manufacturer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_manufacturer_seq`
--

DROP TABLE IF EXISTS `vtiger_manufacturer_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_manufacturer_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_manufacturer_seq`
--

LOCK TABLES `vtiger_manufacturer_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_manufacturer_seq` DISABLE KEYS */;
INSERT INTO `vtiger_manufacturer_seq` VALUES (4);
/*!40000 ALTER TABLE `vtiger_manufacturer_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_maptype`
--

DROP TABLE IF EXISTS `vtiger_maptype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_maptype` (
  `maptypeid` int(11) NOT NULL AUTO_INCREMENT,
  `maptype` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`maptypeid`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_maptype`
--

LOCK TABLES `vtiger_maptype` WRITE;
/*!40000 ALTER TABLE `vtiger_maptype` DISABLE KEYS */;
INSERT INTO `vtiger_maptype` VALUES (1,'Condition Expression',1,394),(2,'Condition Query',1,395),(3,'Mapping',1,396),(4,'Record Access Control',1,397),(5,'Record Set Mapping',1,398),(6,'ListColumns',1,415),(7,'Module Set Mapping',1,461),(8,'DuplicateRelations',1,462),(9,'MasterDetailLayout',1,463),(10,'IOMap',1,464),(11,'FieldDependency',1,465),(12,'Validations',1,466),(13,'Import',1,467),(14,'RelatedPanes',1,468),(15,'FieldInfo',1,527),(16,'GlobalSearchAutocomplete',1,528);
/*!40000 ALTER TABLE `vtiger_maptype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_maptype_seq`
--

DROP TABLE IF EXISTS `vtiger_maptype_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_maptype_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_maptype_seq`
--

LOCK TABLES `vtiger_maptype_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_maptype_seq` DISABLE KEYS */;
INSERT INTO `vtiger_maptype_seq` VALUES (16);
/*!40000 ALTER TABLE `vtiger_maptype_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_modcomments`
--

DROP TABLE IF EXISTS `vtiger_modcomments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_modcomments` (
  `modcommentsid` int(11) NOT NULL,
  `commentcontent` text,
  `related_to` int(19) DEFAULT NULL,
  `parent_comments` int(19) DEFAULT '0',
  `relatedassignedemail` varchar(254) DEFAULT NULL,
  PRIMARY KEY (`modcommentsid`),
  KEY `modcomments_related_to_idx` (`related_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_modcomments`
--

LOCK TABLES `vtiger_modcomments` WRITE;
/*!40000 ALTER TABLE `vtiger_modcomments` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_modcomments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_modcommentscf`
--

DROP TABLE IF EXISTS `vtiger_modcommentscf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_modcommentscf` (
  `modcommentsid` int(11) NOT NULL,
  PRIMARY KEY (`modcommentsid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_modcommentscf`
--

LOCK TABLES `vtiger_modcommentscf` WRITE;
/*!40000 ALTER TABLE `vtiger_modcommentscf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_modcommentscf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_modentity_num`
--

DROP TABLE IF EXISTS `vtiger_modentity_num`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_modentity_num` (
  `num_id` int(19) NOT NULL,
  `semodule` varchar(50) NOT NULL,
  `prefix` varchar(50) NOT NULL DEFAULT '',
  `start_id` varchar(50) NOT NULL,
  `cur_id` varchar(50) NOT NULL,
  `active` varchar(2) NOT NULL,
  PRIMARY KEY (`num_id`),
  UNIQUE KEY `num_idx` (`num_id`),
  KEY `semodule_active_idx` (`semodule`,`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_modentity_num`
--

LOCK TABLES `vtiger_modentity_num` WRITE;
/*!40000 ALTER TABLE `vtiger_modentity_num` DISABLE KEYS */;
INSERT INTO `vtiger_modentity_num` VALUES (1,'Leads','LEA','1','11','1'),(2,'Accounts','ACC','1','11','1'),(3,'Campaigns','CAM','1','4','1'),(4,'Contacts','CON','1','11','1'),(5,'Potentials','POT','1','11','1'),(6,'HelpDesk','TT','1','6','1'),(7,'Quotes','QUO','1','6','1'),(8,'SalesOrder','SO','1','6','1'),(9,'PurchaseOrder','PO','1','6','1'),(10,'Invoice','INV','1','6','1'),(11,'Products','PRO','1','11','1'),(12,'Vendors','VEN','1','11','1'),(13,'PriceBooks','PB','1','13','1'),(14,'Faq','FAQ','1','13','1'),(15,'Documents','DOC','1','1','1'),(16,'ServiceContracts','SERCON','1','1','1'),(17,'Services','SER','1','1','1'),(18,'cbupdater','cbupd-','0000001','0000191','1'),(19,'GlobalVariable','glb-','0000001','0000002','1'),(20,'InventoryDetails','','000000001','000000001','1'),(21,'CobroPago','PAY-','0000001','0000001','1'),(22,'cbMap','BMAP-','0000001','0000001','1'),(23,'cbTermConditions','cbTermConditions-','0000001','0000005','1'),(24,'cbtranslation','cbtr-','0000001','0000001','1');
/*!40000 ALTER TABLE `vtiger_modentity_num` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_modentity_num_seq`
--

DROP TABLE IF EXISTS `vtiger_modentity_num_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_modentity_num_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_modentity_num_seq`
--

LOCK TABLES `vtiger_modentity_num_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_modentity_num_seq` DISABLE KEYS */;
INSERT INTO `vtiger_modentity_num_seq` VALUES (24);
/*!40000 ALTER TABLE `vtiger_modentity_num_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_modtracker_basic`
--

DROP TABLE IF EXISTS `vtiger_modtracker_basic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_modtracker_basic` (
  `id` int(20) NOT NULL,
  `crmid` int(20) DEFAULT NULL,
  `module` varchar(50) DEFAULT NULL,
  `whodid` int(20) DEFAULT NULL,
  `changedon` datetime DEFAULT NULL,
  `status` int(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `crmidx` (`crmid`),
  KEY `modtracker_basic_crmid_idx` (`crmid`),
  KEY `modtracker_basic_whodid_idx` (`whodid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_modtracker_basic`
--

LOCK TABLES `vtiger_modtracker_basic` WRITE;
/*!40000 ALTER TABLE `vtiger_modtracker_basic` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_modtracker_basic` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_modtracker_basic_seq`
--

DROP TABLE IF EXISTS `vtiger_modtracker_basic_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_modtracker_basic_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_modtracker_basic_seq`
--

LOCK TABLES `vtiger_modtracker_basic_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_modtracker_basic_seq` DISABLE KEYS */;
INSERT INTO `vtiger_modtracker_basic_seq` VALUES (0);
/*!40000 ALTER TABLE `vtiger_modtracker_basic_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_modtracker_detail`
--

DROP TABLE IF EXISTS `vtiger_modtracker_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_modtracker_detail` (
  `id` int(11) DEFAULT NULL,
  `fieldname` varchar(100) DEFAULT NULL,
  `prevalue` text,
  `postvalue` text,
  KEY `idx` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_modtracker_detail`
--

LOCK TABLES `vtiger_modtracker_detail` WRITE;
/*!40000 ALTER TABLE `vtiger_modtracker_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_modtracker_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_modtracker_tabs`
--

DROP TABLE IF EXISTS `vtiger_modtracker_tabs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_modtracker_tabs` (
  `tabid` int(11) NOT NULL,
  `visible` int(11) DEFAULT '0',
  PRIMARY KEY (`tabid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_modtracker_tabs`
--

LOCK TABLES `vtiger_modtracker_tabs` WRITE;
/*!40000 ALTER TABLE `vtiger_modtracker_tabs` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_modtracker_tabs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_no_of_currency_decimals`
--

DROP TABLE IF EXISTS `vtiger_no_of_currency_decimals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_no_of_currency_decimals` (
  `no_of_currency_decimalsid` int(11) NOT NULL AUTO_INCREMENT,
  `no_of_currency_decimals` varchar(200) NOT NULL,
  `sortorderid` int(11) DEFAULT NULL,
  `presence` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`no_of_currency_decimalsid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_no_of_currency_decimals`
--

LOCK TABLES `vtiger_no_of_currency_decimals` WRITE;
/*!40000 ALTER TABLE `vtiger_no_of_currency_decimals` DISABLE KEYS */;
INSERT INTO `vtiger_no_of_currency_decimals` VALUES (1,'2',1,1),(2,'3',2,1),(3,'4',3,1),(4,'5',4,1),(5,'6',5,1);
/*!40000 ALTER TABLE `vtiger_no_of_currency_decimals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_no_of_currency_decimals_seq`
--

DROP TABLE IF EXISTS `vtiger_no_of_currency_decimals_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_no_of_currency_decimals_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_no_of_currency_decimals_seq`
--

LOCK TABLES `vtiger_no_of_currency_decimals_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_no_of_currency_decimals_seq` DISABLE KEYS */;
INSERT INTO `vtiger_no_of_currency_decimals_seq` VALUES (5);
/*!40000 ALTER TABLE `vtiger_no_of_currency_decimals_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_notebook_contents`
--

DROP TABLE IF EXISTS `vtiger_notebook_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_notebook_contents` (
  `userid` int(19) NOT NULL,
  `notebookid` int(19) NOT NULL,
  `contents` text,
  KEY `notebook_contents_userid_idx` (`userid`),
  KEY `notebook_contents_userid_notebookid_idx` (`userid`,`notebookid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_notebook_contents`
--

LOCK TABLES `vtiger_notebook_contents` WRITE;
/*!40000 ALTER TABLE `vtiger_notebook_contents` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_notebook_contents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_notes`
--

DROP TABLE IF EXISTS `vtiger_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_notes` (
  `notesid` int(19) NOT NULL DEFAULT '0',
  `note_no` varchar(100) NOT NULL,
  `title` varchar(50) NOT NULL,
  `filename` varchar(200) DEFAULT NULL,
  `notecontent` text,
  `folderid` int(19) NOT NULL DEFAULT '1',
  `filetype` varchar(50) DEFAULT NULL,
  `filelocationtype` varchar(5) DEFAULT NULL,
  `filedownloadcount` int(19) DEFAULT NULL,
  `filestatus` int(19) DEFAULT NULL,
  `filesize` int(19) NOT NULL DEFAULT '0',
  `fileversion` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`notesid`),
  KEY `notes_title_idx` (`title`),
  KEY `folderid` (`folderid`),
  CONSTRAINT `fk_1_vtiger_notes` FOREIGN KEY (`notesid`) REFERENCES `vtiger_crmentity` (`crmid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_notes`
--

LOCK TABLES `vtiger_notes` WRITE;
/*!40000 ALTER TABLE `vtiger_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_notescf`
--

DROP TABLE IF EXISTS `vtiger_notescf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_notescf` (
  `notesid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`notesid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_notescf`
--

LOCK TABLES `vtiger_notescf` WRITE;
/*!40000 ALTER TABLE `vtiger_notescf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_notescf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_opportunity_type`
--

DROP TABLE IF EXISTS `vtiger_opportunity_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_opportunity_type` (
  `opptypeid` int(19) NOT NULL AUTO_INCREMENT,
  `opportunity_type` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`opptypeid`),
  UNIQUE KEY `opportunity_type_opportunity_type_idx` (`opportunity_type`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_opportunity_type`
--

LOCK TABLES `vtiger_opportunity_type` WRITE;
/*!40000 ALTER TABLE `vtiger_opportunity_type` DISABLE KEYS */;
INSERT INTO `vtiger_opportunity_type` VALUES (1,'--None--',1,127),(2,'Existing Business',1,128),(3,'New Business',1,129);
/*!40000 ALTER TABLE `vtiger_opportunity_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_opportunity_type_seq`
--

DROP TABLE IF EXISTS `vtiger_opportunity_type_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_opportunity_type_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_opportunity_type_seq`
--

LOCK TABLES `vtiger_opportunity_type_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_opportunity_type_seq` DISABLE KEYS */;
INSERT INTO `vtiger_opportunity_type_seq` VALUES (3);
/*!40000 ALTER TABLE `vtiger_opportunity_type_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_opportunitystage`
--

DROP TABLE IF EXISTS `vtiger_opportunitystage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_opportunitystage` (
  `potstageid` int(19) NOT NULL AUTO_INCREMENT,
  `stage` varchar(200) NOT NULL,
  `sortorderid` int(19) NOT NULL DEFAULT '0',
  `presence` int(1) NOT NULL DEFAULT '1',
  `probability` decimal(3,2) DEFAULT '0.00',
  PRIMARY KEY (`potstageid`),
  UNIQUE KEY `opportunitystage_stage_idx` (`stage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_opportunitystage`
--

LOCK TABLES `vtiger_opportunitystage` WRITE;
/*!40000 ALTER TABLE `vtiger_opportunitystage` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_opportunitystage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_org_share_action2tab`
--

DROP TABLE IF EXISTS `vtiger_org_share_action2tab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_org_share_action2tab` (
  `share_action_id` int(19) NOT NULL,
  `tabid` int(19) NOT NULL,
  PRIMARY KEY (`share_action_id`,`tabid`),
  KEY `fk_2_vtiger_org_share_action2tab` (`tabid`),
  CONSTRAINT `fk_2_vtiger_org_share_action2tab` FOREIGN KEY (`tabid`) REFERENCES `vtiger_tab` (`tabid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_org_share_action2tab`
--

LOCK TABLES `vtiger_org_share_action2tab` WRITE;
/*!40000 ALTER TABLE `vtiger_org_share_action2tab` DISABLE KEYS */;
INSERT INTO `vtiger_org_share_action2tab` VALUES (0,2),(1,2),(2,2),(3,2),(0,4),(1,4),(2,4),(3,4),(0,6),(1,6),(2,6),(3,6),(0,7),(1,7),(2,7),(3,7),(0,8),(1,8),(2,8),(3,8),(0,9),(1,9),(2,9),(3,9),(0,10),(1,10),(2,10),(3,10),(0,13),(1,13),(2,13),(3,13),(0,14),(1,14),(2,14),(3,14),(0,16),(1,16),(2,16),(3,16),(0,18),(1,18),(2,18),(3,18),(0,20),(1,20),(2,20),(3,20),(0,21),(1,21),(2,21),(3,21),(0,22),(1,22),(2,22),(3,22),(0,23),(1,23),(2,23),(3,23),(0,26),(1,26),(2,26),(3,26),(0,36),(1,36),(2,36),(3,36),(0,37),(1,37),(2,37),(3,37),(0,38),(1,38),(2,38),(3,38),(0,41),(1,41),(2,41),(3,41),(0,42),(1,42),(2,42),(3,42),(0,43),(1,43),(2,43),(3,43),(0,47),(1,47),(2,47),(3,47),(0,48),(1,48),(2,48),(3,48),(0,49),(1,49),(2,49),(3,49),(0,50),(1,50),(2,50),(3,50),(0,52),(1,52),(2,52),(3,52),(0,55),(1,55),(2,55),(3,55),(0,56),(1,56),(2,56),(3,56),(0,57),(1,57),(2,57),(3,57),(0,58),(1,58),(2,58),(3,58),(0,62),(1,62),(2,62),(3,62),(0,63),(1,63),(2,63),(3,63),(0,64),(1,64),(2,64),(3,64);
/*!40000 ALTER TABLE `vtiger_org_share_action2tab` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_org_share_action_mapping`
--

DROP TABLE IF EXISTS `vtiger_org_share_action_mapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_org_share_action_mapping` (
  `share_action_id` int(19) NOT NULL,
  `share_action_name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`share_action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_org_share_action_mapping`
--

LOCK TABLES `vtiger_org_share_action_mapping` WRITE;
/*!40000 ALTER TABLE `vtiger_org_share_action_mapping` DISABLE KEYS */;
INSERT INTO `vtiger_org_share_action_mapping` VALUES (0,'Public: Read Only'),(1,'Public: Read, Create/Edit'),(2,'Public: Read, Create/Edit, Delete'),(3,'Private'),(4,'Hide Details'),(5,'Hide Details and Add Events'),(6,'Show Details'),(7,'Show Details and Add Events');
/*!40000 ALTER TABLE `vtiger_org_share_action_mapping` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_organizationdetails`
--

DROP TABLE IF EXISTS `vtiger_organizationdetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_organizationdetails` (
  `organization_id` int(11) NOT NULL,
  `organizationname` varchar(60) DEFAULT NULL,
  `address` varchar(150) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `code` varchar(30) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `fax` varchar(30) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  `logoname` varchar(50) DEFAULT NULL,
  `logo` text,
  `frontlogo` text,
  `faviconlogo` text,
  PRIMARY KEY (`organization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_organizationdetails`
--

LOCK TABLES `vtiger_organizationdetails` WRITE;
/*!40000 ALTER TABLE `vtiger_organizationdetails` DISABLE KEYS */;
INSERT INTO `vtiger_organizationdetails` VALUES (1,'Your Company',' Your Address','Your City','Your State','Your Country','ZIP CODE','+99-98-7654-3210','+99-98-7654-3210','www.your-company.tld','app-logo.png',NULL,NULL,NULL);
/*!40000 ALTER TABLE `vtiger_organizationdetails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_organizationdetails_seq`
--

DROP TABLE IF EXISTS `vtiger_organizationdetails_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_organizationdetails_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_organizationdetails_seq`
--

LOCK TABLES `vtiger_organizationdetails_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_organizationdetails_seq` DISABLE KEYS */;
INSERT INTO `vtiger_organizationdetails_seq` VALUES (1);
/*!40000 ALTER TABLE `vtiger_organizationdetails_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ownernotify`
--

DROP TABLE IF EXISTS `vtiger_ownernotify`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ownernotify` (
  `crmid` int(19) DEFAULT NULL,
  `smownerid` int(19) DEFAULT NULL,
  `flag` int(3) DEFAULT NULL,
  KEY `ownernotify_crmid_flag_idx` (`crmid`,`flag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ownernotify`
--

LOCK TABLES `vtiger_ownernotify` WRITE;
/*!40000 ALTER TABLE `vtiger_ownernotify` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_ownernotify` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_parenttab`
--

DROP TABLE IF EXISTS `vtiger_parenttab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_parenttab` (
  `parenttabid` int(19) NOT NULL,
  `parenttab_label` varchar(100) NOT NULL,
  `sequence` int(10) NOT NULL,
  `visible` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`parenttabid`),
  KEY `parenttab_parenttabid_parenttabl_label_visible_idx` (`parenttabid`,`parenttab_label`,`visible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_parenttab`
--

LOCK TABLES `vtiger_parenttab` WRITE;
/*!40000 ALTER TABLE `vtiger_parenttab` DISABLE KEYS */;
INSERT INTO `vtiger_parenttab` VALUES (1,'My Home Page',1,0),(2,'Marketing',2,0),(3,'Sales',3,0),(4,'Support',4,0),(5,'Analytics',5,0),(6,'Inventory',6,0),(7,'Tools',7,0),(8,'Settings',8,0);
/*!40000 ALTER TABLE `vtiger_parenttab` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_parenttabrel`
--

DROP TABLE IF EXISTS `vtiger_parenttabrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_parenttabrel` (
  `parenttabid` int(19) NOT NULL,
  `tabid` int(19) NOT NULL,
  `sequence` int(3) NOT NULL,
  KEY `parenttabrel_tabid_parenttabid_idx` (`tabid`,`parenttabid`),
  KEY `fk_2_vtiger_parenttabrel` (`parenttabid`),
  CONSTRAINT `fk_1_vtiger_parenttabrel` FOREIGN KEY (`tabid`) REFERENCES `vtiger_tab` (`tabid`) ON DELETE CASCADE,
  CONSTRAINT `fk_2_vtiger_parenttabrel` FOREIGN KEY (`parenttabid`) REFERENCES `vtiger_parenttab` (`parenttabid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_parenttabrel`
--

LOCK TABLES `vtiger_parenttabrel` WRITE;
/*!40000 ALTER TABLE `vtiger_parenttabrel` DISABLE KEYS */;
INSERT INTO `vtiger_parenttabrel` VALUES (1,9,2),(1,28,4),(1,3,1),(3,7,1),(3,6,2),(3,4,3),(3,2,4),(3,20,5),(3,22,6),(3,23,7),(3,19,8),(3,8,9),(4,13,1),(4,15,2),(4,6,3),(4,4,4),(4,8,5),(5,1,2),(5,25,1),(6,14,1),(6,18,2),(6,19,3),(6,21,4),(6,22,5),(6,20,6),(6,23,7),(7,24,1),(7,27,2),(7,8,3),(2,26,1),(2,6,2),(2,4,3),(2,28,4),(4,28,7),(2,7,5),(2,9,6),(4,9,8),(2,8,8),(3,9,11),(7,33,5),(7,36,6),(4,37,9),(6,38,8),(8,41,1),(6,42,9),(6,43,10),(7,47,7),(4,48,10),(4,49,11),(4,50,12),(7,51,8),(7,52,9),(7,55,10),(7,56,11),(6,57,11),(7,58,12),(8,59,2),(8,60,3),(8,61,4),(3,62,12),(1,63,5),(8,64,5);
/*!40000 ALTER TABLE `vtiger_parenttabrel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_payment_duration`
--

DROP TABLE IF EXISTS `vtiger_payment_duration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_payment_duration` (
  `payment_duration_id` int(11) DEFAULT NULL,
  `payment_duration` varchar(200) DEFAULT NULL,
  `sortorderid` int(11) DEFAULT NULL,
  `presence` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_payment_duration`
--

LOCK TABLES `vtiger_payment_duration` WRITE;
/*!40000 ALTER TABLE `vtiger_payment_duration` DISABLE KEYS */;
INSERT INTO `vtiger_payment_duration` VALUES (1,'Net 30 days',0,1),(2,'Net 45 days',1,1),(3,'Net 60 days',2,1);
/*!40000 ALTER TABLE `vtiger_payment_duration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_payment_duration_seq`
--

DROP TABLE IF EXISTS `vtiger_payment_duration_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_payment_duration_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_payment_duration_seq`
--

LOCK TABLES `vtiger_payment_duration_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_payment_duration_seq` DISABLE KEYS */;
INSERT INTO `vtiger_payment_duration_seq` VALUES (3);
/*!40000 ALTER TABLE `vtiger_payment_duration_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_paymentcategory`
--

DROP TABLE IF EXISTS `vtiger_paymentcategory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_paymentcategory` (
  `paymentcategoryid` int(11) NOT NULL AUTO_INCREMENT,
  `paymentcategory` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`paymentcategoryid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_paymentcategory`
--

LOCK TABLES `vtiger_paymentcategory` WRITE;
/*!40000 ALTER TABLE `vtiger_paymentcategory` DISABLE KEYS */;
INSERT INTO `vtiger_paymentcategory` VALUES (1,'Infrastructure',1,242),(2,'Stock',1,243),(3,'Sale',1,244);
/*!40000 ALTER TABLE `vtiger_paymentcategory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_paymentcategory_seq`
--

DROP TABLE IF EXISTS `vtiger_paymentcategory_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_paymentcategory_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_paymentcategory_seq`
--

LOCK TABLES `vtiger_paymentcategory_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_paymentcategory_seq` DISABLE KEYS */;
INSERT INTO `vtiger_paymentcategory_seq` VALUES (3);
/*!40000 ALTER TABLE `vtiger_paymentcategory_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_paymentmode`
--

DROP TABLE IF EXISTS `vtiger_paymentmode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_paymentmode` (
  `paymentmodeid` int(11) NOT NULL AUTO_INCREMENT,
  `paymentmode` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`paymentmodeid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_paymentmode`
--

LOCK TABLES `vtiger_paymentmode` WRITE;
/*!40000 ALTER TABLE `vtiger_paymentmode` DISABLE KEYS */;
INSERT INTO `vtiger_paymentmode` VALUES (1,'Cash',1,239),(2,'Check',1,240),(3,'Credit card',1,241);
/*!40000 ALTER TABLE `vtiger_paymentmode` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_paymentmode_seq`
--

DROP TABLE IF EXISTS `vtiger_paymentmode_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_paymentmode_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_paymentmode_seq`
--

LOCK TABLES `vtiger_paymentmode_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_paymentmode_seq` DISABLE KEYS */;
INSERT INTO `vtiger_paymentmode_seq` VALUES (3);
/*!40000 ALTER TABLE `vtiger_paymentmode_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_pbxmanager`
--

DROP TABLE IF EXISTS `vtiger_pbxmanager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_pbxmanager` (
  `pbxmanagerid` int(11) NOT NULL,
  `callfrom` varchar(255) DEFAULT NULL,
  `callto` varchar(255) DEFAULT NULL,
  `timeofcall` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pbxmanagerid`),
  KEY `callto` (`callto`),
  KEY `callfrom` (`callfrom`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_pbxmanager`
--

LOCK TABLES `vtiger_pbxmanager` WRITE;
/*!40000 ALTER TABLE `vtiger_pbxmanager` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_pbxmanager` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_picklist`
--

DROP TABLE IF EXISTS `vtiger_picklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_picklist` (
  `picklistid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  PRIMARY KEY (`picklistid`),
  UNIQUE KEY `picklist_name_idx` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_picklist`
--

LOCK TABLES `vtiger_picklist` WRITE;
/*!40000 ALTER TABLE `vtiger_picklist` DISABLE KEYS */;
INSERT INTO `vtiger_picklist` VALUES (1,'accounttype'),(2,'activitytype'),(40,'assetstatus'),(3,'campaignstatus'),(4,'campaigntype'),(5,'carrier'),(50,'category'),(33,'contract_priority'),(32,'contract_status'),(34,'contract_type'),(6,'eventstatus'),(37,'execstate'),(7,'expectedresponse'),(8,'faqcategories'),(9,'faqstatus'),(56,'followuptype'),(55,'formodule'),(10,'glacct'),(49,'gvname'),(11,'industry'),(12,'invoicestatus'),(13,'leadsource'),(14,'leadstatus'),(15,'manufacturer'),(53,'maptype'),(16,'opportunity_type'),(39,'paymentcategory'),(38,'paymentmode'),(17,'postatus'),(18,'productcategory'),(48,'progress'),(41,'projectmilestonetype'),(47,'projectpriority'),(45,'projectstatus'),(43,'projecttaskpriority'),(44,'projecttaskprogress'),(54,'projecttaskstatus'),(42,'projecttasktype'),(46,'projecttype'),(19,'quotestage'),(20,'rating'),(21,'sales_stage'),(22,'salutationtype'),(36,'servicecategory'),(35,'service_usageunit'),(23,'sostatus'),(24,'taskpriority'),(25,'taskstatus'),(26,'ticketcategories'),(27,'ticketpriorities'),(28,'ticketseverities'),(29,'ticketstatus'),(31,'tracking_unit'),(30,'usageunit');
/*!40000 ALTER TABLE `vtiger_picklist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_picklist_dependency`
--

DROP TABLE IF EXISTS `vtiger_picklist_dependency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_picklist_dependency` (
  `id` int(11) NOT NULL,
  `tabid` int(19) NOT NULL,
  `sourcefield` varchar(255) DEFAULT NULL,
  `targetfield` varchar(255) DEFAULT NULL,
  `sourcevalue` varchar(100) DEFAULT NULL,
  `targetvalues` text,
  `criteria` text,
  PRIMARY KEY (`id`),
  KEY `picklist_dependency_tabid_idx` (`tabid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_picklist_dependency`
--

LOCK TABLES `vtiger_picklist_dependency` WRITE;
/*!40000 ALTER TABLE `vtiger_picklist_dependency` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_picklist_dependency` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_picklist_seq`
--

DROP TABLE IF EXISTS `vtiger_picklist_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_picklist_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_picklist_seq`
--

LOCK TABLES `vtiger_picklist_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_picklist_seq` DISABLE KEYS */;
INSERT INTO `vtiger_picklist_seq` VALUES (56);
/*!40000 ALTER TABLE `vtiger_picklist_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_picklistvalues_seq`
--

DROP TABLE IF EXISTS `vtiger_picklistvalues_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_picklistvalues_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_picklistvalues_seq`
--

LOCK TABLES `vtiger_picklistvalues_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_picklistvalues_seq` DISABLE KEYS */;
INSERT INTO `vtiger_picklistvalues_seq` VALUES (539);
/*!40000 ALTER TABLE `vtiger_picklistvalues_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_pobillads`
--

DROP TABLE IF EXISTS `vtiger_pobillads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_pobillads` (
  `pobilladdressid` int(19) NOT NULL DEFAULT '0',
  `bill_city` varchar(30) DEFAULT NULL,
  `bill_code` varchar(30) DEFAULT NULL,
  `bill_country` varchar(30) DEFAULT NULL,
  `bill_state` varchar(30) DEFAULT NULL,
  `bill_street` varchar(250) DEFAULT NULL,
  `bill_pobox` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`pobilladdressid`),
  CONSTRAINT `fk_1_vtiger_pobillads` FOREIGN KEY (`pobilladdressid`) REFERENCES `vtiger_purchaseorder` (`purchaseorderid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_pobillads`
--

LOCK TABLES `vtiger_pobillads` WRITE;
/*!40000 ALTER TABLE `vtiger_pobillads` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_pobillads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_portal`
--

DROP TABLE IF EXISTS `vtiger_portal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_portal` (
  `portalid` int(19) NOT NULL,
  `portalname` varchar(200) NOT NULL,
  `portalurl` varchar(255) NOT NULL,
  `sequence` int(3) NOT NULL,
  `setdefault` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`portalid`),
  KEY `portal_portalname_idx` (`portalname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_portal`
--

LOCK TABLES `vtiger_portal` WRITE;
/*!40000 ALTER TABLE `vtiger_portal` DISABLE KEYS */;
INSERT INTO `vtiger_portal` VALUES (3,'coreBOS Forums','http://discussions.corebos.org/',0,1),(5,'coreBOS Documentation','http://corebos.org/documentation',0,0),(8,'coreBOS','http://corebos.org',0,0);
/*!40000 ALTER TABLE `vtiger_portal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_portal_seq`
--

DROP TABLE IF EXISTS `vtiger_portal_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_portal_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_portal_seq`
--

LOCK TABLES `vtiger_portal_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_portal_seq` DISABLE KEYS */;
INSERT INTO `vtiger_portal_seq` VALUES (8);
/*!40000 ALTER TABLE `vtiger_portal_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_portalinfo`
--

DROP TABLE IF EXISTS `vtiger_portalinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_portalinfo` (
  `id` int(11) NOT NULL,
  `user_name` varchar(50) DEFAULT NULL,
  `user_password` varchar(255) DEFAULT NULL,
  `type` varchar(5) DEFAULT NULL,
  `last_login_time` datetime DEFAULT NULL,
  `login_time` datetime DEFAULT NULL,
  `logout_time` datetime DEFAULT NULL,
  `isactive` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_1_vtiger_portalinfo` FOREIGN KEY (`id`) REFERENCES `vtiger_contactdetails` (`contactid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_portalinfo`
--

LOCK TABLES `vtiger_portalinfo` WRITE;
/*!40000 ALTER TABLE `vtiger_portalinfo` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_portalinfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_poshipads`
--

DROP TABLE IF EXISTS `vtiger_poshipads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_poshipads` (
  `poshipaddressid` int(19) NOT NULL DEFAULT '0',
  `ship_city` varchar(30) DEFAULT NULL,
  `ship_code` varchar(30) DEFAULT NULL,
  `ship_country` varchar(30) DEFAULT NULL,
  `ship_state` varchar(30) DEFAULT NULL,
  `ship_street` varchar(250) DEFAULT NULL,
  `ship_pobox` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`poshipaddressid`),
  CONSTRAINT `fk_1_vtiger_poshipads` FOREIGN KEY (`poshipaddressid`) REFERENCES `vtiger_purchaseorder` (`purchaseorderid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_poshipads`
--

LOCK TABLES `vtiger_poshipads` WRITE;
/*!40000 ALTER TABLE `vtiger_poshipads` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_poshipads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_postatus`
--

DROP TABLE IF EXISTS `vtiger_postatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_postatus` (
  `postatusid` int(19) NOT NULL AUTO_INCREMENT,
  `postatus` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`postatusid`),
  UNIQUE KEY `postatus_postatus_idx` (`postatus`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_postatus`
--

LOCK TABLES `vtiger_postatus` WRITE;
/*!40000 ALTER TABLE `vtiger_postatus` DISABLE KEYS */;
INSERT INTO `vtiger_postatus` VALUES (1,'Created',0,130),(2,'Approved',0,131),(3,'Delivered',0,132),(4,'Cancelled',0,133),(5,'Received Shipment',0,134);
/*!40000 ALTER TABLE `vtiger_postatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_postatus_seq`
--

DROP TABLE IF EXISTS `vtiger_postatus_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_postatus_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_postatus_seq`
--

LOCK TABLES `vtiger_postatus_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_postatus_seq` DISABLE KEYS */;
INSERT INTO `vtiger_postatus_seq` VALUES (5);
/*!40000 ALTER TABLE `vtiger_postatus_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_postatushistory`
--

DROP TABLE IF EXISTS `vtiger_postatushistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_postatushistory` (
  `historyid` int(19) NOT NULL AUTO_INCREMENT,
  `purchaseorderid` int(19) NOT NULL,
  `vendorname` varchar(100) DEFAULT NULL,
  `total` decimal(28,6) DEFAULT NULL,
  `postatus` varchar(200) DEFAULT NULL,
  `lastmodified` datetime DEFAULT NULL,
  PRIMARY KEY (`historyid`),
  KEY `postatushistory_purchaseorderid_idx` (`purchaseorderid`),
  CONSTRAINT `fk_1_vtiger_postatushistory` FOREIGN KEY (`purchaseorderid`) REFERENCES `vtiger_purchaseorder` (`purchaseorderid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_postatushistory`
--

LOCK TABLES `vtiger_postatushistory` WRITE;
/*!40000 ALTER TABLE `vtiger_postatushistory` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_postatushistory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_potential`
--

DROP TABLE IF EXISTS `vtiger_potential`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_potential` (
  `potentialid` int(19) NOT NULL DEFAULT '0',
  `potential_no` varchar(100) NOT NULL,
  `related_to` int(19) DEFAULT NULL,
  `potentialname` varchar(120) NOT NULL,
  `amount` decimal(18,6) DEFAULT NULL,
  `currency` varchar(20) DEFAULT NULL,
  `closingdate` date DEFAULT NULL,
  `typeofrevenue` varchar(50) DEFAULT NULL,
  `nextstep` varchar(100) DEFAULT NULL,
  `private` int(1) DEFAULT '0',
  `probability` decimal(7,3) DEFAULT '0.000',
  `campaignid` int(19) DEFAULT NULL,
  `sales_stage` varchar(200) DEFAULT NULL,
  `potentialtype` varchar(200) DEFAULT NULL,
  `leadsource` varchar(200) DEFAULT NULL,
  `productid` int(50) DEFAULT NULL,
  `productversion` varchar(50) DEFAULT NULL,
  `quotationref` varchar(50) DEFAULT NULL,
  `partnercontact` varchar(50) DEFAULT NULL,
  `remarks` varchar(50) DEFAULT NULL,
  `runtimefee` int(19) DEFAULT '0',
  `followupdate` date DEFAULT NULL,
  `description` text,
  `forecastcategory` int(19) DEFAULT '0',
  `outcomeanalysis` int(19) DEFAULT '0',
  `forecast_amount` decimal(27,6) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `isconvertedfromlead` varchar(3) DEFAULT NULL,
  `convertedfromlead` int(11) DEFAULT NULL,
  PRIMARY KEY (`potentialid`),
  KEY `potential_relatedto_idx` (`related_to`),
  KEY `potentail_sales_stage_idx` (`sales_stage`),
  KEY `potentail_sales_stage_amount_idx` (`amount`,`sales_stage`),
  CONSTRAINT `fk_1_vtiger_potential` FOREIGN KEY (`potentialid`) REFERENCES `vtiger_crmentity` (`crmid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_potential`
--

LOCK TABLES `vtiger_potential` WRITE;
/*!40000 ALTER TABLE `vtiger_potential` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_potential` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_potentialscf`
--

DROP TABLE IF EXISTS `vtiger_potentialscf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_potentialscf` (
  `potentialid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`potentialid`),
  CONSTRAINT `fk_1_vtiger_potentialscf` FOREIGN KEY (`potentialid`) REFERENCES `vtiger_potential` (`potentialid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_potentialscf`
--

LOCK TABLES `vtiger_potentialscf` WRITE;
/*!40000 ALTER TABLE `vtiger_potentialscf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_potentialscf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_potstagehistory`
--

DROP TABLE IF EXISTS `vtiger_potstagehistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_potstagehistory` (
  `historyid` int(19) NOT NULL AUTO_INCREMENT,
  `potentialid` int(19) NOT NULL,
  `amount` decimal(28,6) DEFAULT NULL,
  `stage` varchar(100) DEFAULT NULL,
  `probability` decimal(7,3) DEFAULT NULL,
  `expectedrevenue` decimal(28,6) DEFAULT NULL,
  `closedate` date DEFAULT NULL,
  `lastmodified` datetime DEFAULT NULL,
  PRIMARY KEY (`historyid`),
  KEY `potstagehistory_potentialid_idx` (`potentialid`),
  CONSTRAINT `fk_1_vtiger_potstagehistory` FOREIGN KEY (`potentialid`) REFERENCES `vtiger_potential` (`potentialid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_potstagehistory`
--

LOCK TABLES `vtiger_potstagehistory` WRITE;
/*!40000 ALTER TABLE `vtiger_potstagehistory` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_potstagehistory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_pricebook`
--

DROP TABLE IF EXISTS `vtiger_pricebook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_pricebook` (
  `pricebookid` int(19) NOT NULL DEFAULT '0',
  `pricebook_no` varchar(100) NOT NULL,
  `bookname` varchar(100) DEFAULT NULL,
  `active` int(1) DEFAULT NULL,
  `currency_id` int(19) NOT NULL DEFAULT '1',
  PRIMARY KEY (`pricebookid`),
  CONSTRAINT `fk_1_vtiger_pricebook` FOREIGN KEY (`pricebookid`) REFERENCES `vtiger_crmentity` (`crmid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_pricebook`
--

LOCK TABLES `vtiger_pricebook` WRITE;
/*!40000 ALTER TABLE `vtiger_pricebook` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_pricebook` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_pricebookcf`
--

DROP TABLE IF EXISTS `vtiger_pricebookcf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_pricebookcf` (
  `pricebookid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pricebookid`),
  CONSTRAINT `fk_1_vtiger_pricebookcf` FOREIGN KEY (`pricebookid`) REFERENCES `vtiger_pricebook` (`pricebookid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_pricebookcf`
--

LOCK TABLES `vtiger_pricebookcf` WRITE;
/*!40000 ALTER TABLE `vtiger_pricebookcf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_pricebookcf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_pricebookproductrel`
--

DROP TABLE IF EXISTS `vtiger_pricebookproductrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_pricebookproductrel` (
  `pricebookid` int(19) NOT NULL,
  `productid` int(19) NOT NULL,
  `listprice` decimal(28,6) DEFAULT NULL,
  `usedcurrency` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`pricebookid`,`productid`),
  KEY `pricebookproductrel_pricebookid_idx` (`pricebookid`),
  KEY `pricebookproductrel_productid_idx` (`productid`),
  CONSTRAINT `fk_1_vtiger_pricebookproductrel` FOREIGN KEY (`pricebookid`) REFERENCES `vtiger_pricebook` (`pricebookid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_pricebookproductrel`
--

LOCK TABLES `vtiger_pricebookproductrel` WRITE;
/*!40000 ALTER TABLE `vtiger_pricebookproductrel` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_pricebookproductrel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_priority`
--

DROP TABLE IF EXISTS `vtiger_priority`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_priority` (
  `priorityid` int(19) NOT NULL AUTO_INCREMENT,
  `priority` varchar(200) NOT NULL,
  `sortorderid` int(19) NOT NULL DEFAULT '0',
  `presence` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`priorityid`),
  UNIQUE KEY `priority_priority_idx` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_priority`
--

LOCK TABLES `vtiger_priority` WRITE;
/*!40000 ALTER TABLE `vtiger_priority` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_priority` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_productcategory`
--

DROP TABLE IF EXISTS `vtiger_productcategory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_productcategory` (
  `productcategoryid` int(19) NOT NULL AUTO_INCREMENT,
  `productcategory` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`productcategoryid`),
  UNIQUE KEY `productcategory_productcategory_idx` (`productcategory`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_productcategory`
--

LOCK TABLES `vtiger_productcategory` WRITE;
/*!40000 ALTER TABLE `vtiger_productcategory` DISABLE KEYS */;
INSERT INTO `vtiger_productcategory` VALUES (1,'--None--',1,135),(2,'Hardware',1,136),(3,'Software',1,137),(4,'CRM Applications',1,138);
/*!40000 ALTER TABLE `vtiger_productcategory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_productcategory_seq`
--

DROP TABLE IF EXISTS `vtiger_productcategory_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_productcategory_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_productcategory_seq`
--

LOCK TABLES `vtiger_productcategory_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_productcategory_seq` DISABLE KEYS */;
INSERT INTO `vtiger_productcategory_seq` VALUES (4);
/*!40000 ALTER TABLE `vtiger_productcategory_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_productcf`
--

DROP TABLE IF EXISTS `vtiger_productcf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_productcf` (
  `productid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`productid`),
  CONSTRAINT `fk_1_vtiger_productcf` FOREIGN KEY (`productid`) REFERENCES `vtiger_products` (`productid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_productcf`
--

LOCK TABLES `vtiger_productcf` WRITE;
/*!40000 ALTER TABLE `vtiger_productcf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_productcf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_productcurrencyrel`
--

DROP TABLE IF EXISTS `vtiger_productcurrencyrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_productcurrencyrel` (
  `productid` int(11) NOT NULL,
  `currencyid` int(11) NOT NULL,
  `converted_price` decimal(28,6) DEFAULT NULL,
  `actual_price` decimal(28,6) DEFAULT NULL,
  KEY `productid` (`productid`,`currencyid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_productcurrencyrel`
--

LOCK TABLES `vtiger_productcurrencyrel` WRITE;
/*!40000 ALTER TABLE `vtiger_productcurrencyrel` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_productcurrencyrel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_products`
--

DROP TABLE IF EXISTS `vtiger_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_products` (
  `productid` int(11) NOT NULL,
  `product_no` varchar(100) NOT NULL,
  `productname` varchar(100) DEFAULT NULL,
  `productcode` varchar(40) DEFAULT NULL,
  `productcategory` varchar(200) DEFAULT NULL,
  `manufacturer` varchar(200) DEFAULT NULL,
  `qty_per_unit` decimal(11,2) DEFAULT '0.00',
  `unit_price` decimal(29,6) DEFAULT NULL,
  `weight` decimal(11,3) DEFAULT NULL,
  `pack_size` int(11) DEFAULT NULL,
  `sales_start_date` date DEFAULT NULL,
  `sales_end_date` date DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `cost_factor` int(11) DEFAULT NULL,
  `commissionrate` decimal(7,3) DEFAULT NULL,
  `commissionmethod` varchar(50) DEFAULT NULL,
  `discontinued` int(1) NOT NULL DEFAULT '0',
  `usageunit` varchar(200) DEFAULT NULL,
  `reorderlevel` int(11) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  `taxclass` varchar(200) DEFAULT NULL,
  `mfr_part_no` varchar(200) DEFAULT NULL,
  `vendor_part_no` varchar(200) DEFAULT NULL,
  `serialno` varchar(200) DEFAULT NULL,
  `qtyinstock` decimal(25,3) DEFAULT NULL,
  `productsheet` varchar(200) DEFAULT NULL,
  `qtyindemand` int(11) DEFAULT NULL,
  `glacct` varchar(200) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `imagename` text,
  `currency_id` int(19) NOT NULL DEFAULT '1',
  `cost_price` decimal(28,6) DEFAULT NULL,
  `divisible` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`productid`),
  KEY `productname` (`productname`),
  CONSTRAINT `fk_1_vtiger_products` FOREIGN KEY (`productid`) REFERENCES `vtiger_crmentity` (`crmid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_products`
--

LOCK TABLES `vtiger_products` WRITE;
/*!40000 ALTER TABLE `vtiger_products` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_producttaxrel`
--

DROP TABLE IF EXISTS `vtiger_producttaxrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_producttaxrel` (
  `productid` int(11) NOT NULL,
  `taxid` int(3) NOT NULL,
  `taxpercentage` decimal(7,3) DEFAULT NULL,
  KEY `producttaxrel_productid_idx` (`productid`),
  KEY `producttaxrel_taxid_idx` (`taxid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_producttaxrel`
--

LOCK TABLES `vtiger_producttaxrel` WRITE;
/*!40000 ALTER TABLE `vtiger_producttaxrel` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_producttaxrel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_profile`
--

DROP TABLE IF EXISTS `vtiger_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_profile` (
  `profileid` int(10) NOT NULL AUTO_INCREMENT,
  `profilename` varchar(50) NOT NULL,
  `description` text,
  PRIMARY KEY (`profileid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_profile`
--

LOCK TABLES `vtiger_profile` WRITE;
/*!40000 ALTER TABLE `vtiger_profile` DISABLE KEYS */;
INSERT INTO `vtiger_profile` VALUES (1,'Administrator','Admin Profile'),(2,'Sales Profile','Profile Related to Sales'),(3,'Support Profile','Profile Related to Support'),(4,'Guest Profile','Guest Profile for Test Users');
/*!40000 ALTER TABLE `vtiger_profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_profile2field`
--

DROP TABLE IF EXISTS `vtiger_profile2field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_profile2field` (
  `profileid` int(11) NOT NULL,
  `tabid` int(10) DEFAULT NULL,
  `fieldid` int(19) NOT NULL,
  `visible` int(19) DEFAULT NULL,
  `readonly` int(19) DEFAULT NULL,
  PRIMARY KEY (`profileid`,`fieldid`),
  KEY `profile2field_profileid_tabid_fieldname_idx` (`profileid`,`tabid`),
  KEY `profile2field_tabid_profileid_idx` (`tabid`,`profileid`),
  KEY `profile2field_visible_profileid_idx` (`visible`,`profileid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_profile2field`
--

LOCK TABLES `vtiger_profile2field` WRITE;
/*!40000 ALTER TABLE `vtiger_profile2field` DISABLE KEYS */;
INSERT INTO `vtiger_profile2field` VALUES (1,6,1,0,0),(1,6,2,0,0),(1,6,3,0,0),(1,6,4,0,0),(1,6,5,0,0),(1,6,6,0,0),(1,6,7,0,0),(1,6,8,0,0),(1,6,9,0,0),(1,6,10,0,0),(1,6,11,0,0),(1,6,12,0,0),(1,6,13,0,0),(1,6,14,0,0),(1,6,15,0,0),(1,6,16,0,0),(1,6,17,0,0),(1,6,18,0,0),(1,6,19,0,0),(1,6,20,0,0),(1,6,21,0,0),(1,6,22,0,0),(1,6,23,0,0),(1,6,24,0,0),(1,6,25,0,0),(1,6,26,0,0),(1,6,27,0,0),(1,6,28,0,0),(1,6,29,0,0),(1,6,30,0,0),(1,6,31,0,0),(1,6,32,0,0),(1,6,33,0,0),(1,6,34,0,0),(1,6,35,0,0),(1,6,36,0,0),(1,7,37,0,0),(1,7,38,0,0),(1,7,39,0,0),(1,7,40,0,0),(1,7,41,0,0),(1,7,42,0,0),(1,7,43,0,0),(1,7,44,0,0),(1,7,45,0,0),(1,7,46,0,0),(1,7,47,0,0),(1,7,48,0,0),(1,7,49,0,0),(1,7,50,0,0),(1,7,51,0,0),(1,7,52,0,0),(1,7,53,0,0),(1,7,54,0,0),(1,7,55,0,0),(1,7,56,0,0),(1,7,57,0,0),(1,7,58,0,0),(1,7,59,0,0),(1,7,60,0,0),(1,7,61,0,0),(1,7,62,0,0),(1,7,63,0,0),(1,7,64,0,0),(1,7,65,0,0),(1,4,66,0,0),(1,4,67,0,0),(1,4,68,0,0),(1,4,69,0,0),(1,4,70,0,0),(1,4,71,0,0),(1,4,72,0,0),(1,4,73,0,0),(1,4,74,0,0),(1,4,75,0,0),(1,4,76,0,0),(1,4,77,0,0),(1,4,78,0,0),(1,4,79,0,0),(1,4,80,0,0),(1,4,81,0,0),(1,4,82,0,0),(1,4,83,0,0),(1,4,84,0,0),(1,4,85,0,0),(1,4,86,0,0),(1,4,87,0,0),(1,4,88,0,0),(1,4,89,0,0),(1,4,90,0,0),(1,4,91,0,0),(1,4,92,0,0),(1,4,93,0,0),(1,4,94,0,0),(1,4,95,0,0),(1,4,96,0,0),(1,4,97,0,0),(1,4,98,0,0),(1,4,99,0,0),(1,4,100,0,0),(1,4,101,0,0),(1,4,102,0,0),(1,4,103,0,0),(1,4,104,0,0),(1,4,105,0,0),(1,4,106,0,0),(1,4,107,0,0),(1,4,108,0,0),(1,4,109,0,0),(1,2,110,0,0),(1,2,111,0,0),(1,2,112,0,0),(1,2,113,0,0),(1,2,114,0,0),(1,2,115,0,0),(1,2,116,0,0),(1,2,117,0,0),(1,2,118,0,0),(1,2,119,0,0),(1,2,120,0,0),(1,2,121,0,0),(1,2,122,0,0),(1,2,123,0,0),(1,2,124,0,0),(1,2,125,0,0),(1,26,126,0,0),(1,26,127,0,0),(1,26,128,0,0),(1,26,129,0,0),(1,26,130,0,0),(1,26,131,0,0),(1,26,132,0,0),(1,26,133,0,0),(1,26,134,0,0),(1,26,135,0,0),(1,26,136,0,0),(1,26,137,0,0),(1,26,138,0,0),(1,26,139,0,0),(1,26,140,0,0),(1,26,141,0,0),(1,26,142,0,0),(1,26,143,0,0),(1,26,144,0,0),(1,26,145,0,0),(1,26,146,0,0),(1,26,147,0,0),(1,26,148,0,0),(1,26,149,0,0),(1,26,150,0,0),(1,4,151,0,0),(1,6,152,0,0),(1,7,153,0,0),(1,26,154,0,0),(1,13,155,0,0),(1,13,156,0,0),(1,13,157,0,0),(1,13,158,0,0),(1,13,159,0,0),(1,13,160,0,0),(1,13,161,0,0),(1,13,162,0,0),(1,13,163,0,0),(1,13,164,0,0),(1,13,165,0,0),(1,13,166,0,0),(1,13,167,0,0),(1,13,168,0,0),(1,13,169,0,0),(1,13,170,0,0),(1,13,171,0,0),(1,13,172,0,0),(1,13,173,0,0),(1,14,174,0,0),(1,14,175,0,0),(1,14,176,0,0),(1,14,177,0,0),(1,14,178,0,0),(1,14,179,0,0),(1,14,180,0,0),(1,14,181,0,0),(1,14,182,0,0),(1,14,183,0,0),(1,14,184,0,0),(1,14,185,0,0),(1,14,186,0,0),(1,14,187,0,0),(1,14,188,0,0),(1,14,189,0,0),(1,14,190,0,0),(1,14,191,0,0),(1,14,192,0,0),(1,14,193,0,0),(1,14,194,0,0),(1,14,195,0,0),(1,14,196,0,0),(1,14,197,0,0),(1,14,198,0,0),(1,14,199,0,0),(1,14,200,0,0),(1,14,201,0,0),(1,14,202,0,0),(1,14,203,0,0),(1,14,204,0,0),(1,8,205,0,0),(1,8,206,0,0),(1,8,207,0,0),(1,8,208,0,0),(1,8,209,0,0),(1,8,210,0,0),(1,8,211,0,0),(1,8,212,0,0),(1,8,213,0,0),(1,8,214,0,0),(1,8,215,0,0),(1,8,216,0,0),(1,8,217,0,0),(1,8,218,0,0),(1,8,219,0,0),(1,10,220,0,0),(1,10,221,0,0),(1,10,222,0,0),(1,10,223,0,0),(1,10,224,0,0),(1,10,225,0,0),(1,10,226,0,0),(1,10,227,0,0),(1,10,228,0,0),(1,10,229,0,0),(1,10,230,0,0),(1,10,231,0,0),(1,9,232,0,0),(1,9,233,0,0),(1,9,234,0,0),(1,9,235,0,0),(1,9,236,0,0),(1,9,237,0,0),(1,9,238,0,0),(1,9,239,0,0),(1,9,240,0,0),(1,9,241,0,0),(1,9,242,0,0),(1,9,243,0,0),(1,9,244,0,0),(1,9,245,0,0),(1,9,246,0,0),(1,9,247,0,0),(1,9,248,0,0),(1,9,249,0,0),(1,9,250,0,0),(1,9,251,0,0),(1,9,252,0,0),(1,9,253,0,0),(1,9,254,0,0),(1,9,255,0,0),(1,16,256,0,0),(1,16,257,0,0),(1,16,258,0,0),(1,16,259,0,0),(1,16,260,0,0),(1,16,261,0,0),(1,16,262,0,0),(1,16,263,0,0),(1,16,264,0,0),(1,16,265,0,0),(1,16,266,0,0),(1,16,267,0,0),(1,16,268,0,0),(1,16,269,0,0),(1,16,270,0,0),(1,16,271,0,0),(1,16,272,0,0),(1,16,273,0,0),(1,16,274,0,0),(1,16,275,0,0),(1,16,276,0,0),(1,16,277,0,0),(1,16,278,0,0),(1,15,279,0,0),(1,15,280,0,0),(1,15,281,0,0),(1,15,282,0,0),(1,15,283,0,0),(1,15,284,0,0),(1,15,285,0,0),(1,15,286,0,0),(1,15,287,0,0),(1,15,288,0,0),(1,18,289,0,0),(1,18,290,0,0),(1,18,291,0,0),(1,18,292,0,0),(1,18,293,0,0),(1,18,294,0,0),(1,18,295,0,0),(1,18,296,0,0),(1,18,297,0,0),(1,18,298,0,0),(1,18,299,0,0),(1,18,300,0,0),(1,18,301,0,0),(1,18,302,0,0),(1,18,303,0,0),(1,18,304,0,0),(1,18,305,0,0),(1,19,306,0,0),(1,19,307,0,0),(1,19,308,0,0),(1,19,309,0,0),(1,19,310,0,0),(1,19,311,0,0),(1,19,312,0,0),(1,19,313,0,0),(1,20,314,0,0),(1,20,315,0,0),(1,20,316,0,0),(1,20,317,0,0),(1,20,318,0,0),(1,20,319,0,0),(1,20,320,0,0),(1,20,321,0,0),(1,20,322,0,0),(1,20,323,0,0),(1,20,324,0,0),(1,20,325,0,0),(1,20,326,0,0),(1,20,327,0,0),(1,20,328,0,0),(1,20,329,0,0),(1,20,330,0,0),(1,20,331,0,0),(1,20,332,0,0),(1,20,333,0,0),(1,20,334,0,0),(1,20,335,0,0),(1,20,336,0,0),(1,20,337,0,0),(1,20,338,0,0),(1,20,339,0,0),(1,20,340,0,0),(1,20,341,0,0),(1,20,342,0,0),(1,20,343,0,0),(1,20,344,0,0),(1,20,345,0,0),(1,20,346,0,0),(1,20,347,0,0),(1,20,348,0,0),(1,20,349,0,0),(1,20,350,0,0),(1,21,351,0,0),(1,21,352,0,0),(1,21,353,0,0),(1,21,354,0,0),(1,21,355,0,0),(1,21,356,0,0),(1,21,357,0,0),(1,21,358,0,0),(1,21,359,0,0),(1,21,360,0,0),(1,21,361,0,0),(1,21,362,0,0),(1,21,363,0,0),(1,21,364,0,0),(1,21,365,0,0),(1,21,366,0,0),(1,21,367,0,0),(1,21,368,0,0),(1,21,369,0,0),(1,21,370,0,0),(1,21,371,0,0),(1,21,372,0,0),(1,21,373,0,0),(1,21,374,0,0),(1,21,375,0,0),(1,21,376,0,0),(1,21,377,0,0),(1,21,378,0,0),(1,21,379,0,0),(1,21,380,0,0),(1,21,381,0,0),(1,21,382,0,0),(1,21,383,0,0),(1,21,384,0,0),(1,21,385,0,0),(1,21,386,0,0),(1,21,387,0,0),(1,21,388,0,0),(1,22,389,0,0),(1,22,390,0,0),(1,22,391,0,0),(1,22,392,0,0),(1,22,393,0,0),(1,22,394,0,0),(1,22,395,0,0),(1,22,396,0,0),(1,22,397,0,0),(1,22,398,0,0),(1,22,399,0,0),(1,22,400,0,0),(1,22,401,0,0),(1,22,402,0,0),(1,22,403,0,0),(1,22,404,0,0),(1,22,405,0,0),(1,22,406,0,0),(1,22,407,0,0),(1,22,408,0,0),(1,22,409,0,0),(1,22,410,0,0),(1,22,411,0,0),(1,22,412,0,0),(1,22,413,0,0),(1,22,414,0,0),(1,22,415,0,0),(1,22,416,0,0),(1,22,417,0,0),(1,22,418,0,0),(1,22,419,0,0),(1,22,420,0,0),(1,22,421,0,0),(1,22,422,0,0),(1,22,423,0,0),(1,22,424,0,0),(1,22,425,0,0),(1,22,426,0,0),(1,22,427,0,0),(1,22,428,0,0),(1,22,429,0,0),(1,22,430,0,0),(1,22,431,0,0),(1,22,432,0,0),(1,22,433,0,0),(1,22,434,0,0),(1,22,435,0,0),(1,23,436,0,0),(1,23,437,0,0),(1,23,438,0,0),(1,23,439,0,0),(1,23,440,0,0),(1,23,441,0,0),(1,23,442,0,0),(1,23,443,0,0),(1,23,444,0,0),(1,23,445,0,0),(1,23,446,0,0),(1,23,447,0,0),(1,23,448,0,0),(1,23,449,0,0),(1,23,450,0,0),(1,23,451,0,0),(1,23,452,0,0),(1,23,453,0,0),(1,23,454,0,0),(1,23,455,0,0),(1,23,456,0,0),(1,23,457,0,0),(1,23,458,0,0),(1,23,459,0,0),(1,23,460,0,0),(1,23,461,0,0),(1,23,462,0,0),(1,23,463,0,0),(1,23,464,0,0),(1,23,465,0,0),(1,23,466,0,0),(1,23,467,0,0),(1,23,468,0,0),(1,23,469,0,0),(1,23,470,0,0),(1,23,471,0,0),(1,23,472,0,0),(1,23,473,0,0),(1,23,474,0,0),(1,10,519,0,0),(1,10,520,0,0),(1,10,521,0,0),(1,10,522,0,0),(1,10,523,0,0),(1,10,524,0,0),(1,36,525,0,0),(1,36,526,0,0),(1,36,527,0,0),(1,36,528,0,0),(1,37,531,0,0),(1,37,532,0,0),(1,37,533,0,0),(1,37,534,0,0),(1,37,535,0,0),(1,37,536,0,0),(1,37,537,0,0),(1,37,538,0,0),(1,37,539,0,0),(1,37,540,0,0),(1,37,541,0,0),(1,37,542,0,0),(1,37,543,0,0),(1,37,544,0,0),(1,37,545,0,0),(1,37,546,0,0),(1,37,547,0,0),(1,37,548,0,0),(1,37,549,0,0),(1,38,550,0,0),(1,38,551,0,0),(1,38,552,0,0),(1,38,553,0,0),(1,38,554,0,0),(1,38,555,0,0),(1,38,556,0,0),(1,38,557,0,0),(1,38,558,0,0),(1,38,559,0,0),(1,38,560,0,0),(1,38,561,0,0),(1,38,562,0,0),(1,38,563,0,0),(1,38,564,0,0),(1,38,565,0,0),(1,38,566,0,0),(1,38,567,0,0),(1,38,568,0,0),(1,41,569,0,0),(1,41,570,0,0),(1,41,571,0,0),(1,41,572,0,0),(1,41,573,0,0),(1,41,574,0,0),(1,41,575,0,0),(1,41,576,0,0),(1,41,577,0,0),(1,41,578,0,0),(1,41,579,0,0),(1,41,580,0,0),(1,41,581,0,0),(1,41,582,0,0),(1,42,583,0,0),(1,42,584,0,0),(1,42,585,0,0),(1,42,586,0,0),(1,42,587,0,0),(1,42,588,0,0),(1,42,589,0,0),(1,42,590,0,0),(1,42,591,0,0),(1,42,592,0,0),(1,42,593,0,0),(1,42,594,0,0),(1,42,595,0,0),(1,42,596,0,0),(1,42,597,0,0),(1,42,598,0,0),(1,42,599,0,0),(1,43,600,0,0),(1,43,601,0,0),(1,43,602,0,0),(1,43,603,0,0),(1,43,604,0,0),(1,43,605,0,0),(1,43,606,0,0),(1,43,607,0,0),(1,43,608,0,0),(1,43,609,0,0),(1,43,610,0,0),(1,43,611,0,0),(1,43,612,0,0),(1,43,613,0,0),(1,43,614,0,0),(1,43,615,0,0),(1,43,616,0,0),(1,47,617,0,0),(1,47,618,0,0),(1,47,619,0,0),(1,47,620,0,0),(1,47,621,0,0),(1,47,622,0,0),(1,47,623,0,0),(1,48,624,0,0),(1,48,625,0,0),(1,48,626,0,0),(1,48,627,0,0),(1,48,628,0,0),(1,48,629,0,0),(1,48,630,0,0),(1,48,631,0,0),(1,48,632,0,0),(1,48,633,0,0),(1,49,634,0,0),(1,49,635,0,0),(1,49,636,0,0),(1,49,637,0,0),(1,49,638,0,0),(1,49,639,0,0),(1,49,640,0,0),(1,49,641,0,0),(1,49,642,0,0),(1,49,643,0,0),(1,49,644,0,0),(1,49,645,0,0),(1,49,646,0,0),(1,49,647,0,0),(1,49,648,0,0),(1,50,649,0,0),(1,50,650,0,0),(1,50,651,0,0),(1,50,652,0,0),(1,50,653,0,0),(1,50,654,0,0),(1,50,655,0,0),(1,50,656,0,0),(1,50,657,0,0),(1,50,658,0,0),(1,50,659,0,0),(1,50,660,0,0),(1,50,661,0,0),(1,50,662,0,0),(1,50,663,0,0),(1,50,664,0,0),(1,50,665,0,0),(1,52,666,0,0),(1,52,667,0,0),(1,52,668,0,0),(1,52,669,0,0),(1,52,670,0,0),(1,2,671,0,0),(1,50,672,0,0),(1,49,673,0,0),(1,2,674,0,0),(1,13,675,0,0),(1,29,676,0,0),(1,56,677,0,0),(1,56,678,0,0),(1,56,679,0,0),(1,56,680,0,0),(1,56,681,0,0),(1,56,682,0,0),(1,56,683,0,0),(1,56,684,0,0),(1,56,685,0,0),(1,56,686,0,0),(1,56,687,0,0),(1,56,688,0,0),(1,56,689,0,0),(1,47,690,0,0),(1,57,691,0,0),(1,57,692,0,0),(1,57,693,0,0),(1,57,694,0,0),(1,57,695,0,0),(1,57,696,0,0),(1,57,697,0,0),(1,57,698,0,0),(1,57,699,0,0),(1,57,700,0,0),(1,57,701,0,0),(1,57,702,0,0),(1,57,703,0,0),(1,57,704,0,0),(1,57,705,0,0),(1,57,706,0,0),(1,57,707,0,0),(1,57,708,0,0),(1,57,709,0,0),(1,57,710,0,0),(1,57,711,0,0),(1,57,712,0,0),(1,57,713,0,0),(1,18,714,0,0),(1,42,715,0,0),(1,42,716,0,0),(1,13,717,0,0),(1,14,718,0,0),(1,38,719,0,0),(1,29,720,0,0),(1,58,721,0,0),(1,58,722,0,0),(1,58,723,0,0),(1,58,724,0,0),(1,58,725,0,0),(1,58,726,0,0),(1,58,727,0,0),(1,58,728,0,0),(1,58,729,0,0),(1,56,730,0,0),(1,29,731,0,0),(1,57,732,0,0),(1,57,733,0,0),(1,49,734,0,0),(1,7,735,0,0),(1,6,736,0,0),(1,6,737,0,0),(1,4,738,0,0),(1,4,739,0,0),(1,2,740,0,0),(1,2,741,0,0),(1,13,742,0,0),(1,14,743,0,0),(1,38,744,0,0),(1,57,745,0,0),(1,2,746,0,0),(1,4,747,0,0),(1,6,748,0,0),(1,7,749,0,0),(1,8,750,0,0),(1,13,751,0,0),(1,14,752,0,0),(1,15,753,0,0),(1,18,754,0,0),(1,19,755,0,0),(1,20,756,0,0),(1,21,757,0,0),(1,22,758,0,0),(1,23,759,0,0),(1,26,760,0,0),(1,37,761,0,0),(1,38,762,0,0),(1,41,763,0,0),(1,42,764,0,0),(1,43,765,0,0),(1,48,766,0,0),(1,49,767,0,0),(1,50,768,0,0),(1,52,769,0,0),(1,56,770,0,0),(1,57,771,0,0),(1,58,772,0,0),(1,62,773,0,0),(1,62,774,0,0),(1,62,775,0,0),(1,62,776,0,0),(1,62,777,0,0),(1,62,778,0,0),(1,62,779,0,0),(1,62,780,0,0),(1,62,781,0,0),(1,23,782,0,0),(1,22,783,0,0),(1,20,784,0,0),(1,21,785,0,0),(1,63,786,0,0),(1,63,787,0,0),(1,63,788,0,0),(1,63,789,0,0),(1,63,790,0,0),(1,63,791,0,0),(1,63,792,0,0),(1,63,793,0,0),(1,63,794,0,0),(1,63,795,0,0),(1,63,796,0,0),(1,63,797,0,0),(1,63,798,0,0),(1,63,799,0,0),(1,63,800,0,0),(1,63,801,0,0),(1,63,802,0,0),(1,63,803,0,0),(1,63,804,0,0),(1,63,805,0,0),(1,63,806,0,0),(1,63,807,0,0),(1,63,808,0,0),(1,63,809,0,0),(1,63,810,0,0),(1,63,811,0,0),(1,63,812,0,0),(1,63,813,0,0),(1,63,814,0,0),(1,63,815,0,0),(1,64,816,0,0),(1,64,817,0,0),(1,64,818,0,0),(1,64,819,0,0),(1,64,820,0,0),(1,64,821,0,0),(1,64,822,0,0),(1,64,823,0,0),(1,64,824,0,0),(1,64,825,0,0),(1,64,826,0,0),(1,64,827,0,0),(1,64,828,0,0),(2,6,1,0,0),(2,6,2,0,0),(2,6,3,0,0),(2,6,4,0,0),(2,6,5,0,0),(2,6,6,0,0),(2,6,7,0,0),(2,6,8,0,0),(2,6,9,0,0),(2,6,10,0,0),(2,6,11,0,0),(2,6,12,0,0),(2,6,13,0,0),(2,6,14,0,0),(2,6,15,0,0),(2,6,16,0,0),(2,6,17,0,0),(2,6,18,0,0),(2,6,19,0,0),(2,6,20,0,0),(2,6,21,0,0),(2,6,22,0,0),(2,6,23,0,0),(2,6,24,0,0),(2,6,25,0,0),(2,6,26,0,0),(2,6,27,0,0),(2,6,28,0,0),(2,6,29,0,0),(2,6,30,0,0),(2,6,31,0,0),(2,6,32,0,0),(2,6,33,0,0),(2,6,34,0,0),(2,6,35,0,0),(2,6,36,0,0),(2,7,37,0,0),(2,7,38,0,0),(2,7,39,0,0),(2,7,40,0,0),(2,7,41,0,0),(2,7,42,0,0),(2,7,43,0,0),(2,7,44,0,0),(2,7,45,0,0),(2,7,46,0,0),(2,7,47,0,0),(2,7,48,0,0),(2,7,49,0,0),(2,7,50,0,0),(2,7,51,0,0),(2,7,52,0,0),(2,7,53,0,0),(2,7,54,0,0),(2,7,55,0,0),(2,7,56,0,0),(2,7,57,0,0),(2,7,58,0,0),(2,7,59,0,0),(2,7,60,0,0),(2,7,61,0,0),(2,7,62,0,0),(2,7,63,0,0),(2,7,64,0,0),(2,7,65,0,0),(2,4,66,0,0),(2,4,67,0,0),(2,4,68,0,0),(2,4,69,0,0),(2,4,70,0,0),(2,4,71,0,0),(2,4,72,0,0),(2,4,73,0,0),(2,4,74,0,0),(2,4,75,0,0),(2,4,76,0,0),(2,4,77,0,0),(2,4,78,0,0),(2,4,79,0,0),(2,4,80,0,0),(2,4,81,0,0),(2,4,82,0,0),(2,4,83,0,0),(2,4,84,0,0),(2,4,85,0,0),(2,4,86,0,0),(2,4,87,0,0),(2,4,88,0,0),(2,4,89,0,0),(2,4,90,0,0),(2,4,91,0,0),(2,4,92,0,0),(2,4,93,0,0),(2,4,94,0,0),(2,4,95,0,0),(2,4,96,0,0),(2,4,97,0,0),(2,4,98,0,0),(2,4,99,0,0),(2,4,100,0,0),(2,4,101,0,0),(2,4,102,0,0),(2,4,103,0,0),(2,4,104,0,0),(2,4,105,0,0),(2,4,106,0,0),(2,4,107,0,0),(2,4,108,0,0),(2,4,109,0,0),(2,2,110,0,0),(2,2,111,0,0),(2,2,112,0,0),(2,2,113,0,0),(2,2,114,0,0),(2,2,115,0,0),(2,2,116,0,0),(2,2,117,0,0),(2,2,118,0,0),(2,2,119,0,0),(2,2,120,0,0),(2,2,121,0,0),(2,2,122,0,0),(2,2,123,0,0),(2,2,124,0,0),(2,2,125,0,0),(2,26,126,0,0),(2,26,127,0,0),(2,26,128,0,0),(2,26,129,0,0),(2,26,130,0,0),(2,26,131,0,0),(2,26,132,0,0),(2,26,133,0,0),(2,26,134,0,0),(2,26,135,0,0),(2,26,136,0,0),(2,26,137,0,0),(2,26,138,0,0),(2,26,139,0,0),(2,26,140,0,0),(2,26,141,0,0),(2,26,142,0,0),(2,26,143,0,0),(2,26,144,0,0),(2,26,145,0,0),(2,26,146,0,0),(2,26,147,0,0),(2,26,148,0,0),(2,26,149,0,0),(2,26,150,0,0),(2,4,151,0,0),(2,6,152,0,0),(2,7,153,0,0),(2,26,154,0,0),(2,13,155,0,0),(2,13,156,0,0),(2,13,157,0,0),(2,13,158,0,0),(2,13,159,0,0),(2,13,160,0,0),(2,13,161,0,0),(2,13,162,0,0),(2,13,163,0,0),(2,13,164,0,0),(2,13,165,0,0),(2,13,166,0,0),(2,13,167,0,0),(2,13,168,0,0),(2,13,169,0,0),(2,13,170,0,0),(2,13,171,0,0),(2,13,172,0,0),(2,13,173,0,0),(2,14,174,0,0),(2,14,175,0,0),(2,14,176,0,0),(2,14,177,0,0),(2,14,178,0,0),(2,14,179,0,0),(2,14,180,0,0),(2,14,181,0,0),(2,14,182,0,0),(2,14,183,0,0),(2,14,184,0,0),(2,14,185,0,0),(2,14,186,0,0),(2,14,187,0,0),(2,14,188,0,0),(2,14,189,0,0),(2,14,190,0,0),(2,14,191,0,0),(2,14,192,0,0),(2,14,193,0,0),(2,14,194,0,0),(2,14,195,0,0),(2,14,196,0,0),(2,14,197,0,0),(2,14,198,0,0),(2,14,199,0,0),(2,14,200,0,0),(2,14,201,0,0),(2,14,202,0,0),(2,14,203,0,0),(2,14,204,0,0),(2,8,205,0,0),(2,8,206,0,0),(2,8,207,0,0),(2,8,208,0,0),(2,8,209,0,0),(2,8,210,0,0),(2,8,211,0,0),(2,8,212,0,0),(2,8,213,0,0),(2,8,214,0,0),(2,8,215,0,0),(2,8,216,0,0),(2,8,217,0,0),(2,8,218,0,0),(2,8,219,0,0),(2,10,220,0,0),(2,10,221,0,0),(2,10,222,0,0),(2,10,223,0,0),(2,10,224,0,0),(2,10,225,0,0),(2,10,226,0,0),(2,10,227,0,0),(2,10,228,0,0),(2,10,229,0,0),(2,10,230,0,0),(2,10,231,0,0),(2,9,232,0,0),(2,9,233,0,0),(2,9,234,0,0),(2,9,235,0,0),(2,9,236,0,0),(2,9,237,0,0),(2,9,238,0,0),(2,9,239,0,0),(2,9,240,0,0),(2,9,241,0,0),(2,9,242,0,0),(2,9,243,0,0),(2,9,244,0,0),(2,9,245,0,0),(2,9,246,0,0),(2,9,247,0,0),(2,9,248,0,0),(2,9,249,0,0),(2,9,250,0,0),(2,9,251,0,0),(2,9,252,0,0),(2,9,253,0,0),(2,9,254,0,0),(2,9,255,0,0),(2,16,256,0,0),(2,16,257,0,0),(2,16,258,0,0),(2,16,259,0,0),(2,16,260,0,0),(2,16,261,0,0),(2,16,262,0,0),(2,16,263,0,0),(2,16,264,0,0),(2,16,265,0,0),(2,16,266,0,0),(2,16,267,0,0),(2,16,268,0,0),(2,16,269,0,0),(2,16,270,0,0),(2,16,271,0,0),(2,16,272,0,0),(2,16,273,0,0),(2,16,274,0,0),(2,16,275,0,0),(2,16,276,0,0),(2,16,277,0,0),(2,16,278,0,0),(2,15,279,0,0),(2,15,280,0,0),(2,15,281,0,0),(2,15,282,0,0),(2,15,283,0,0),(2,15,284,0,0),(2,15,285,0,0),(2,15,286,0,0),(2,15,287,0,0),(2,15,288,0,0),(2,18,289,0,0),(2,18,290,0,0),(2,18,291,0,0),(2,18,292,0,0),(2,18,293,0,0),(2,18,294,0,0),(2,18,295,0,0),(2,18,296,0,0),(2,18,297,0,0),(2,18,298,0,0),(2,18,299,0,0),(2,18,300,0,0),(2,18,301,0,0),(2,18,302,0,0),(2,18,303,0,0),(2,18,304,0,0),(2,18,305,0,0),(2,19,306,0,0),(2,19,307,0,0),(2,19,308,0,0),(2,19,309,0,0),(2,19,310,0,0),(2,19,311,0,0),(2,19,312,0,0),(2,19,313,0,0),(2,20,314,0,0),(2,20,315,0,0),(2,20,316,0,0),(2,20,317,0,0),(2,20,318,0,0),(2,20,319,0,0),(2,20,320,0,0),(2,20,321,0,0),(2,20,322,0,0),(2,20,323,0,0),(2,20,324,0,0),(2,20,325,0,0),(2,20,326,0,0),(2,20,327,0,0),(2,20,328,0,0),(2,20,329,0,0),(2,20,330,0,0),(2,20,331,0,0),(2,20,332,0,0),(2,20,333,0,0),(2,20,334,0,0),(2,20,335,0,0),(2,20,336,0,0),(2,20,337,0,0),(2,20,338,0,0),(2,20,339,0,0),(2,20,340,0,0),(2,20,341,0,0),(2,20,342,0,0),(2,20,343,0,0),(2,20,344,0,0),(2,20,345,0,0),(2,20,346,0,0),(2,20,347,0,0),(2,20,348,0,0),(2,20,349,0,0),(2,20,350,0,0),(2,21,351,0,0),(2,21,352,0,0),(2,21,353,0,0),(2,21,354,0,0),(2,21,355,0,0),(2,21,356,0,0),(2,21,357,0,0),(2,21,358,0,0),(2,21,359,0,0),(2,21,360,0,0),(2,21,361,0,0),(2,21,362,0,0),(2,21,363,0,0),(2,21,364,0,0),(2,21,365,0,0),(2,21,366,0,0),(2,21,367,0,0),(2,21,368,0,0),(2,21,369,0,0),(2,21,370,0,0),(2,21,371,0,0),(2,21,372,0,0),(2,21,373,0,0),(2,21,374,0,0),(2,21,375,0,0),(2,21,376,0,0),(2,21,377,0,0),(2,21,378,0,0),(2,21,379,0,0),(2,21,380,0,0),(2,21,381,0,0),(2,21,382,0,0),(2,21,383,0,0),(2,21,384,0,0),(2,21,385,0,0),(2,21,386,0,0),(2,21,387,0,0),(2,21,388,0,0),(2,22,389,0,0),(2,22,390,0,0),(2,22,391,0,0),(2,22,392,0,0),(2,22,393,0,0),(2,22,394,0,0),(2,22,395,0,0),(2,22,396,0,0),(2,22,397,0,0),(2,22,398,0,0),(2,22,399,0,0),(2,22,400,0,0),(2,22,401,0,0),(2,22,402,0,0),(2,22,403,0,0),(2,22,404,0,0),(2,22,405,0,0),(2,22,406,0,0),(2,22,407,0,0),(2,22,408,0,0),(2,22,409,0,0),(2,22,410,0,0),(2,22,411,0,0),(2,22,412,0,0),(2,22,413,0,0),(2,22,414,0,0),(2,22,415,0,0),(2,22,416,0,0),(2,22,417,0,0),(2,22,418,0,0),(2,22,419,0,0),(2,22,420,0,0),(2,22,421,0,0),(2,22,422,0,0),(2,22,423,0,0),(2,22,424,0,0),(2,22,425,0,0),(2,22,426,0,0),(2,22,427,0,0),(2,22,428,0,0),(2,22,429,0,0),(2,22,430,0,0),(2,22,431,0,0),(2,22,432,0,0),(2,22,433,0,0),(2,22,434,0,0),(2,22,435,0,0),(2,23,436,0,0),(2,23,437,0,0),(2,23,438,0,0),(2,23,439,0,0),(2,23,440,0,0),(2,23,441,0,0),(2,23,442,0,0),(2,23,443,0,0),(2,23,444,0,0),(2,23,445,0,0),(2,23,446,0,0),(2,23,447,0,0),(2,23,448,0,0),(2,23,449,0,0),(2,23,450,0,0),(2,23,451,0,0),(2,23,452,0,0),(2,23,453,0,0),(2,23,454,0,0),(2,23,455,0,0),(2,23,456,0,0),(2,23,457,0,0),(2,23,458,0,0),(2,23,459,0,0),(2,23,460,0,0),(2,23,461,0,0),(2,23,462,0,0),(2,23,463,0,0),(2,23,464,0,0),(2,23,465,0,0),(2,23,466,0,0),(2,23,467,0,0),(2,23,468,0,0),(2,23,469,0,0),(2,23,470,0,0),(2,23,471,0,0),(2,23,472,0,0),(2,23,473,0,0),(2,23,474,0,0),(2,10,519,0,0),(2,10,520,0,0),(2,10,521,0,0),(2,10,522,0,0),(2,10,523,0,0),(2,10,524,0,0),(2,36,525,0,0),(2,36,526,0,0),(2,36,527,0,0),(2,36,528,0,0),(2,37,531,0,0),(2,37,532,0,0),(2,37,533,0,0),(2,37,534,0,0),(2,37,535,0,0),(2,37,536,0,0),(2,37,537,0,0),(2,37,538,0,0),(2,37,539,0,0),(2,37,540,0,0),(2,37,541,0,0),(2,37,542,0,0),(2,37,543,0,0),(2,37,544,0,0),(2,37,545,0,0),(2,37,546,0,0),(2,37,547,0,0),(2,37,548,0,0),(2,37,549,0,0),(2,38,550,0,0),(2,38,551,0,0),(2,38,552,0,0),(2,38,553,0,0),(2,38,554,0,0),(2,38,555,0,0),(2,38,556,0,0),(2,38,557,0,0),(2,38,558,0,0),(2,38,559,0,0),(2,38,560,0,0),(2,38,561,0,0),(2,38,562,0,0),(2,38,563,0,0),(2,38,564,0,0),(2,38,565,0,0),(2,38,566,0,0),(2,38,567,0,0),(2,38,568,0,0),(2,41,569,0,0),(2,41,570,0,0),(2,41,571,0,0),(2,41,572,0,0),(2,41,573,0,0),(2,41,574,0,0),(2,41,575,0,0),(2,41,576,0,0),(2,41,577,0,0),(2,41,578,0,0),(2,41,579,0,0),(2,41,580,0,0),(2,41,581,0,0),(2,41,582,0,0),(2,42,583,0,0),(2,42,584,0,0),(2,42,585,0,0),(2,42,586,0,0),(2,42,587,0,0),(2,42,588,0,0),(2,42,589,0,0),(2,42,590,0,0),(2,42,591,0,0),(2,42,592,0,0),(2,42,593,0,0),(2,42,594,0,0),(2,42,595,0,0),(2,42,596,0,0),(2,42,597,0,0),(2,42,598,0,0),(2,42,599,0,0),(2,43,600,0,0),(2,43,601,0,0),(2,43,602,0,0),(2,43,603,0,0),(2,43,604,0,0),(2,43,605,0,0),(2,43,606,0,0),(2,43,607,0,0),(2,43,608,0,0),(2,43,609,0,0),(2,43,610,0,0),(2,43,611,0,0),(2,43,612,0,0),(2,43,613,0,0),(2,43,614,0,0),(2,43,615,0,0),(2,43,616,0,0),(2,47,617,0,0),(2,47,618,0,0),(2,47,619,0,0),(2,47,620,0,0),(2,47,621,0,0),(2,47,622,0,0),(2,47,623,0,0),(2,48,624,0,0),(2,48,625,0,0),(2,48,626,0,0),(2,48,627,0,0),(2,48,628,0,0),(2,48,629,0,0),(2,48,630,0,0),(2,48,631,0,0),(2,48,632,0,0),(2,48,633,0,0),(2,49,634,0,0),(2,49,635,0,0),(2,49,636,0,0),(2,49,637,0,0),(2,49,638,0,0),(2,49,639,0,0),(2,49,640,0,0),(2,49,641,0,0),(2,49,642,0,0),(2,49,643,0,0),(2,49,644,0,0),(2,49,645,0,0),(2,49,646,0,0),(2,49,647,0,0),(2,49,648,0,0),(2,50,649,0,0),(2,50,650,0,0),(2,50,651,0,0),(2,50,652,0,0),(2,50,653,0,0),(2,50,654,0,0),(2,50,655,0,0),(2,50,656,0,0),(2,50,657,0,0),(2,50,658,0,0),(2,50,659,0,0),(2,50,660,0,0),(2,50,661,0,0),(2,50,662,0,0),(2,50,663,0,0),(2,50,664,0,0),(2,50,665,0,0),(2,52,666,0,0),(2,52,667,0,0),(2,52,668,0,0),(2,52,669,0,0),(2,52,670,0,0),(2,2,671,0,0),(2,50,672,0,0),(2,49,673,0,0),(2,2,674,0,0),(2,13,675,0,0),(2,29,676,0,0),(2,56,677,0,0),(2,56,678,0,0),(2,56,679,0,0),(2,56,680,0,0),(2,56,681,0,0),(2,56,682,0,0),(2,56,683,0,0),(2,56,684,0,0),(2,56,685,0,0),(2,56,686,0,0),(2,56,687,0,0),(2,56,688,0,0),(2,56,689,0,0),(2,47,690,0,0),(2,57,691,0,0),(2,57,692,0,0),(2,57,693,0,0),(2,57,694,0,0),(2,57,695,0,0),(2,57,696,0,0),(2,57,697,0,0),(2,57,698,0,0),(2,57,699,0,0),(2,57,700,0,0),(2,57,701,0,0),(2,57,702,0,0),(2,57,703,0,0),(2,57,704,0,0),(2,57,705,0,0),(2,57,706,0,0),(2,57,707,0,0),(2,57,708,0,0),(2,57,709,0,0),(2,57,710,0,0),(2,57,711,0,0),(2,57,712,0,0),(2,57,713,0,0),(2,18,714,0,0),(2,42,715,0,0),(2,42,716,0,0),(2,13,717,0,0),(2,14,718,0,0),(2,38,719,0,0),(2,29,720,0,0),(2,58,721,0,0),(2,58,722,0,0),(2,58,723,0,0),(2,58,724,0,0),(2,58,725,0,0),(2,58,726,0,0),(2,58,727,0,0),(2,58,728,0,0),(2,58,729,0,0),(2,56,730,0,0),(2,29,731,0,0),(2,57,732,0,0),(2,57,733,0,0),(2,49,734,0,0),(2,7,735,0,0),(2,6,736,0,0),(2,6,737,0,0),(2,4,738,0,0),(2,4,739,0,0),(2,2,740,0,0),(2,2,741,0,0),(2,13,742,0,0),(2,14,743,0,0),(2,38,744,0,0),(2,57,745,0,0),(2,2,746,0,0),(2,4,747,0,0),(2,6,748,0,0),(2,7,749,0,0),(2,8,750,0,0),(2,13,751,0,0),(2,14,752,0,0),(2,15,753,0,0),(2,18,754,0,0),(2,19,755,0,0),(2,20,756,0,0),(2,21,757,0,0),(2,22,758,0,0),(2,23,759,0,0),(2,26,760,0,0),(2,37,761,0,0),(2,38,762,0,0),(2,41,763,0,0),(2,42,764,0,0),(2,43,765,0,0),(2,48,766,0,0),(2,49,767,0,0),(2,50,768,0,0),(2,52,769,0,0),(2,56,770,0,0),(2,57,771,0,0),(2,58,772,0,0),(2,62,773,0,0),(2,62,774,0,0),(2,62,775,0,0),(2,62,776,0,0),(2,62,777,0,0),(2,62,778,0,0),(2,62,779,0,0),(2,62,780,0,0),(2,62,781,0,0),(2,23,782,0,0),(2,22,783,0,0),(2,20,784,0,0),(2,21,785,0,0),(2,63,786,0,0),(2,63,787,0,0),(2,63,788,0,0),(2,63,789,0,0),(2,63,790,0,0),(2,63,791,0,0),(2,63,792,0,0),(2,63,793,0,0),(2,63,794,0,0),(2,63,795,0,0),(2,63,796,0,0),(2,63,797,0,0),(2,63,798,0,0),(2,63,799,0,0),(2,63,800,0,0),(2,63,801,0,0),(2,63,802,0,0),(2,63,803,0,0),(2,63,804,0,0),(2,63,805,0,0),(2,63,806,0,0),(2,63,807,0,0),(2,63,808,0,0),(2,63,809,0,0),(2,63,810,0,0),(2,63,811,0,0),(2,63,812,0,0),(2,63,813,0,0),(2,63,814,0,0),(2,63,815,0,0),(2,64,816,0,0),(2,64,817,0,0),(2,64,818,0,0),(2,64,819,0,0),(2,64,820,0,0),(2,64,821,0,0),(2,64,822,0,0),(2,64,823,0,0),(2,64,824,0,0),(2,64,825,0,0),(2,64,826,0,0),(2,64,827,0,0),(2,64,828,0,0),(3,6,1,0,0),(3,6,2,0,0),(3,6,3,0,0),(3,6,4,0,0),(3,6,5,0,0),(3,6,6,0,0),(3,6,7,0,0),(3,6,8,0,0),(3,6,9,0,0),(3,6,10,0,0),(3,6,11,0,0),(3,6,12,0,0),(3,6,13,0,0),(3,6,14,0,0),(3,6,15,0,0),(3,6,16,0,0),(3,6,17,0,0),(3,6,18,0,0),(3,6,19,0,0),(3,6,20,0,0),(3,6,21,0,0),(3,6,22,0,0),(3,6,23,0,0),(3,6,24,0,0),(3,6,25,0,0),(3,6,26,0,0),(3,6,27,0,0),(3,6,28,0,0),(3,6,29,0,0),(3,6,30,0,0),(3,6,31,0,0),(3,6,32,0,0),(3,6,33,0,0),(3,6,34,0,0),(3,6,35,0,0),(3,6,36,0,0),(3,7,37,0,0),(3,7,38,0,0),(3,7,39,0,0),(3,7,40,0,0),(3,7,41,0,0),(3,7,42,0,0),(3,7,43,0,0),(3,7,44,0,0),(3,7,45,0,0),(3,7,46,0,0),(3,7,47,0,0),(3,7,48,0,0),(3,7,49,0,0),(3,7,50,0,0),(3,7,51,0,0),(3,7,52,0,0),(3,7,53,0,0),(3,7,54,0,0),(3,7,55,0,0),(3,7,56,0,0),(3,7,57,0,0),(3,7,58,0,0),(3,7,59,0,0),(3,7,60,0,0),(3,7,61,0,0),(3,7,62,0,0),(3,7,63,0,0),(3,7,64,0,0),(3,7,65,0,0),(3,4,66,0,0),(3,4,67,0,0),(3,4,68,0,0),(3,4,69,0,0),(3,4,70,0,0),(3,4,71,0,0),(3,4,72,0,0),(3,4,73,0,0),(3,4,74,0,0),(3,4,75,0,0),(3,4,76,0,0),(3,4,77,0,0),(3,4,78,0,0),(3,4,79,0,0),(3,4,80,0,0),(3,4,81,0,0),(3,4,82,0,0),(3,4,83,0,0),(3,4,84,0,0),(3,4,85,0,0),(3,4,86,0,0),(3,4,87,0,0),(3,4,88,0,0),(3,4,89,0,0),(3,4,90,0,0),(3,4,91,0,0),(3,4,92,0,0),(3,4,93,0,0),(3,4,94,0,0),(3,4,95,0,0),(3,4,96,0,0),(3,4,97,0,0),(3,4,98,0,0),(3,4,99,0,0),(3,4,100,0,0),(3,4,101,0,0),(3,4,102,0,0),(3,4,103,0,0),(3,4,104,0,0),(3,4,105,0,0),(3,4,106,0,0),(3,4,107,0,0),(3,4,108,0,0),(3,4,109,0,0),(3,2,110,0,0),(3,2,111,0,0),(3,2,112,0,0),(3,2,113,0,0),(3,2,114,0,0),(3,2,115,0,0),(3,2,116,0,0),(3,2,117,0,0),(3,2,118,0,0),(3,2,119,0,0),(3,2,120,0,0),(3,2,121,0,0),(3,2,122,0,0),(3,2,123,0,0),(3,2,124,0,0),(3,2,125,0,0),(3,26,126,0,0),(3,26,127,0,0),(3,26,128,0,0),(3,26,129,0,0),(3,26,130,0,0),(3,26,131,0,0),(3,26,132,0,0),(3,26,133,0,0),(3,26,134,0,0),(3,26,135,0,0),(3,26,136,0,0),(3,26,137,0,0),(3,26,138,0,0),(3,26,139,0,0),(3,26,140,0,0),(3,26,141,0,0),(3,26,142,0,0),(3,26,143,0,0),(3,26,144,0,0),(3,26,145,0,0),(3,26,146,0,0),(3,26,147,0,0),(3,26,148,0,0),(3,26,149,0,0),(3,26,150,0,0),(3,4,151,0,0),(3,6,152,0,0),(3,7,153,0,0),(3,26,154,0,0),(3,13,155,0,0),(3,13,156,0,0),(3,13,157,0,0),(3,13,158,0,0),(3,13,159,0,0),(3,13,160,0,0),(3,13,161,0,0),(3,13,162,0,0),(3,13,163,0,0),(3,13,164,0,0),(3,13,165,0,0),(3,13,166,0,0),(3,13,167,0,0),(3,13,168,0,0),(3,13,169,0,0),(3,13,170,0,0),(3,13,171,0,0),(3,13,172,0,0),(3,13,173,0,0),(3,14,174,0,0),(3,14,175,0,0),(3,14,176,0,0),(3,14,177,0,0),(3,14,178,0,0),(3,14,179,0,0),(3,14,180,0,0),(3,14,181,0,0),(3,14,182,0,0),(3,14,183,0,0),(3,14,184,0,0),(3,14,185,0,0),(3,14,186,0,0),(3,14,187,0,0),(3,14,188,0,0),(3,14,189,0,0),(3,14,190,0,0),(3,14,191,0,0),(3,14,192,0,0),(3,14,193,0,0),(3,14,194,0,0),(3,14,195,0,0),(3,14,196,0,0),(3,14,197,0,0),(3,14,198,0,0),(3,14,199,0,0),(3,14,200,0,0),(3,14,201,0,0),(3,14,202,0,0),(3,14,203,0,0),(3,14,204,0,0),(3,8,205,0,0),(3,8,206,0,0),(3,8,207,0,0),(3,8,208,0,0),(3,8,209,0,0),(3,8,210,0,0),(3,8,211,0,0),(3,8,212,0,0),(3,8,213,0,0),(3,8,214,0,0),(3,8,215,0,0),(3,8,216,0,0),(3,8,217,0,0),(3,8,218,0,0),(3,8,219,0,0),(3,10,220,0,0),(3,10,221,0,0),(3,10,222,0,0),(3,10,223,0,0),(3,10,224,0,0),(3,10,225,0,0),(3,10,226,0,0),(3,10,227,0,0),(3,10,228,0,0),(3,10,229,0,0),(3,10,230,0,0),(3,10,231,0,0),(3,9,232,0,0),(3,9,233,0,0),(3,9,234,0,0),(3,9,235,0,0),(3,9,236,0,0),(3,9,237,0,0),(3,9,238,0,0),(3,9,239,0,0),(3,9,240,0,0),(3,9,241,0,0),(3,9,242,0,0),(3,9,243,0,0),(3,9,244,0,0),(3,9,245,0,0),(3,9,246,0,0),(3,9,247,0,0),(3,9,248,0,0),(3,9,249,0,0),(3,9,250,0,0),(3,9,251,0,0),(3,9,252,0,0),(3,9,253,0,0),(3,9,254,0,0),(3,9,255,0,0),(3,16,256,0,0),(3,16,257,0,0),(3,16,258,0,0),(3,16,259,0,0),(3,16,260,0,0),(3,16,261,0,0),(3,16,262,0,0),(3,16,263,0,0),(3,16,264,0,0),(3,16,265,0,0),(3,16,266,0,0),(3,16,267,0,0),(3,16,268,0,0),(3,16,269,0,0),(3,16,270,0,0),(3,16,271,0,0),(3,16,272,0,0),(3,16,273,0,0),(3,16,274,0,0),(3,16,275,0,0),(3,16,276,0,0),(3,16,277,0,0),(3,16,278,0,0),(3,15,279,0,0),(3,15,280,0,0),(3,15,281,0,0),(3,15,282,0,0),(3,15,283,0,0),(3,15,284,0,0),(3,15,285,0,0),(3,15,286,0,0),(3,15,287,0,0),(3,15,288,0,0),(3,18,289,0,0),(3,18,290,0,0),(3,18,291,0,0),(3,18,292,0,0),(3,18,293,0,0),(3,18,294,0,0),(3,18,295,0,0),(3,18,296,0,0),(3,18,297,0,0),(3,18,298,0,0),(3,18,299,0,0),(3,18,300,0,0),(3,18,301,0,0),(3,18,302,0,0),(3,18,303,0,0),(3,18,304,0,0),(3,18,305,0,0),(3,19,306,0,0),(3,19,307,0,0),(3,19,308,0,0),(3,19,309,0,0),(3,19,310,0,0),(3,19,311,0,0),(3,19,312,0,0),(3,19,313,0,0),(3,20,314,0,0),(3,20,315,0,0),(3,20,316,0,0),(3,20,317,0,0),(3,20,318,0,0),(3,20,319,0,0),(3,20,320,0,0),(3,20,321,0,0),(3,20,322,0,0),(3,20,323,0,0),(3,20,324,0,0),(3,20,325,0,0),(3,20,326,0,0),(3,20,327,0,0),(3,20,328,0,0),(3,20,329,0,0),(3,20,330,0,0),(3,20,331,0,0),(3,20,332,0,0),(3,20,333,0,0),(3,20,334,0,0),(3,20,335,0,0),(3,20,336,0,0),(3,20,337,0,0),(3,20,338,0,0),(3,20,339,0,0),(3,20,340,0,0),(3,20,341,0,0),(3,20,342,0,0),(3,20,343,0,0),(3,20,344,0,0),(3,20,345,0,0),(3,20,346,0,0),(3,20,347,0,0),(3,20,348,0,0),(3,20,349,0,0),(3,20,350,0,0),(3,21,351,0,0),(3,21,352,0,0),(3,21,353,0,0),(3,21,354,0,0),(3,21,355,0,0),(3,21,356,0,0),(3,21,357,0,0),(3,21,358,0,0),(3,21,359,0,0),(3,21,360,0,0),(3,21,361,0,0),(3,21,362,0,0),(3,21,363,0,0),(3,21,364,0,0),(3,21,365,0,0),(3,21,366,0,0),(3,21,367,0,0),(3,21,368,0,0),(3,21,369,0,0),(3,21,370,0,0),(3,21,371,0,0),(3,21,372,0,0),(3,21,373,0,0),(3,21,374,0,0),(3,21,375,0,0),(3,21,376,0,0),(3,21,377,0,0),(3,21,378,0,0),(3,21,379,0,0),(3,21,380,0,0),(3,21,381,0,0),(3,21,382,0,0),(3,21,383,0,0),(3,21,384,0,0),(3,21,385,0,0),(3,21,386,0,0),(3,21,387,0,0),(3,21,388,0,0),(3,22,389,0,0),(3,22,390,0,0),(3,22,391,0,0),(3,22,392,0,0),(3,22,393,0,0),(3,22,394,0,0),(3,22,395,0,0),(3,22,396,0,0),(3,22,397,0,0),(3,22,398,0,0),(3,22,399,0,0),(3,22,400,0,0),(3,22,401,0,0),(3,22,402,0,0),(3,22,403,0,0),(3,22,404,0,0),(3,22,405,0,0),(3,22,406,0,0),(3,22,407,0,0),(3,22,408,0,0),(3,22,409,0,0),(3,22,410,0,0),(3,22,411,0,0),(3,22,412,0,0),(3,22,413,0,0),(3,22,414,0,0),(3,22,415,0,0),(3,22,416,0,0),(3,22,417,0,0),(3,22,418,0,0),(3,22,419,0,0),(3,22,420,0,0),(3,22,421,0,0),(3,22,422,0,0),(3,22,423,0,0),(3,22,424,0,0),(3,22,425,0,0),(3,22,426,0,0),(3,22,427,0,0),(3,22,428,0,0),(3,22,429,0,0),(3,22,430,0,0),(3,22,431,0,0),(3,22,432,0,0),(3,22,433,0,0),(3,22,434,0,0),(3,22,435,0,0),(3,23,436,0,0),(3,23,437,0,0),(3,23,438,0,0),(3,23,439,0,0),(3,23,440,0,0),(3,23,441,0,0),(3,23,442,0,0),(3,23,443,0,0),(3,23,444,0,0),(3,23,445,0,0),(3,23,446,0,0),(3,23,447,0,0),(3,23,448,0,0),(3,23,449,0,0),(3,23,450,0,0),(3,23,451,0,0),(3,23,452,0,0),(3,23,453,0,0),(3,23,454,0,0),(3,23,455,0,0),(3,23,456,0,0),(3,23,457,0,0),(3,23,458,0,0),(3,23,459,0,0),(3,23,460,0,0),(3,23,461,0,0),(3,23,462,0,0),(3,23,463,0,0),(3,23,464,0,0),(3,23,465,0,0),(3,23,466,0,0),(3,23,467,0,0),(3,23,468,0,0),(3,23,469,0,0),(3,23,470,0,0),(3,23,471,0,0),(3,23,472,0,0),(3,23,473,0,0),(3,23,474,0,0),(3,10,519,0,0),(3,10,520,0,0),(3,10,521,0,0),(3,10,522,0,0),(3,10,523,0,0),(3,10,524,0,0),(3,36,525,0,0),(3,36,526,0,0),(3,36,527,0,0),(3,36,528,0,0),(3,37,531,0,0),(3,37,532,0,0),(3,37,533,0,0),(3,37,534,0,0),(3,37,535,0,0),(3,37,536,0,0),(3,37,537,0,0),(3,37,538,0,0),(3,37,539,0,0),(3,37,540,0,0),(3,37,541,0,0),(3,37,542,0,0),(3,37,543,0,0),(3,37,544,0,0),(3,37,545,0,0),(3,37,546,0,0),(3,37,547,0,0),(3,37,548,0,0),(3,37,549,0,0),(3,38,550,0,0),(3,38,551,0,0),(3,38,552,0,0),(3,38,553,0,0),(3,38,554,0,0),(3,38,555,0,0),(3,38,556,0,0),(3,38,557,0,0),(3,38,558,0,0),(3,38,559,0,0),(3,38,560,0,0),(3,38,561,0,0),(3,38,562,0,0),(3,38,563,0,0),(3,38,564,0,0),(3,38,565,0,0),(3,38,566,0,0),(3,38,567,0,0),(3,38,568,0,0),(3,41,569,0,0),(3,41,570,0,0),(3,41,571,0,0),(3,41,572,0,0),(3,41,573,0,0),(3,41,574,0,0),(3,41,575,0,0),(3,41,576,0,0),(3,41,577,0,0),(3,41,578,0,0),(3,41,579,0,0),(3,41,580,0,0),(3,41,581,0,0),(3,41,582,0,0),(3,42,583,0,0),(3,42,584,0,0),(3,42,585,0,0),(3,42,586,0,0),(3,42,587,0,0),(3,42,588,0,0),(3,42,589,0,0),(3,42,590,0,0),(3,42,591,0,0),(3,42,592,0,0),(3,42,593,0,0),(3,42,594,0,0),(3,42,595,0,0),(3,42,596,0,0),(3,42,597,0,0),(3,42,598,0,0),(3,42,599,0,0),(3,43,600,0,0),(3,43,601,0,0),(3,43,602,0,0),(3,43,603,0,0),(3,43,604,0,0),(3,43,605,0,0),(3,43,606,0,0),(3,43,607,0,0),(3,43,608,0,0),(3,43,609,0,0),(3,43,610,0,0),(3,43,611,0,0),(3,43,612,0,0),(3,43,613,0,0),(3,43,614,0,0),(3,43,615,0,0),(3,43,616,0,0),(3,47,617,0,0),(3,47,618,0,0),(3,47,619,0,0),(3,47,620,0,0),(3,47,621,0,0),(3,47,622,0,0),(3,47,623,0,0),(3,48,624,0,0),(3,48,625,0,0),(3,48,626,0,0),(3,48,627,0,0),(3,48,628,0,0),(3,48,629,0,0),(3,48,630,0,0),(3,48,631,0,0),(3,48,632,0,0),(3,48,633,0,0),(3,49,634,0,0),(3,49,635,0,0),(3,49,636,0,0),(3,49,637,0,0),(3,49,638,0,0),(3,49,639,0,0),(3,49,640,0,0),(3,49,641,0,0),(3,49,642,0,0),(3,49,643,0,0),(3,49,644,0,0),(3,49,645,0,0),(3,49,646,0,0),(3,49,647,0,0),(3,49,648,0,0),(3,50,649,0,0),(3,50,650,0,0),(3,50,651,0,0),(3,50,652,0,0),(3,50,653,0,0),(3,50,654,0,0),(3,50,655,0,0),(3,50,656,0,0),(3,50,657,0,0),(3,50,658,0,0),(3,50,659,0,0),(3,50,660,0,0),(3,50,661,0,0),(3,50,662,0,0),(3,50,663,0,0),(3,50,664,0,0),(3,50,665,0,0),(3,52,666,0,0),(3,52,667,0,0),(3,52,668,0,0),(3,52,669,0,0),(3,52,670,0,0),(3,2,671,0,0),(3,50,672,0,0),(3,49,673,0,0),(3,2,674,0,0),(3,13,675,0,0),(3,29,676,0,0),(3,56,677,0,0),(3,56,678,0,0),(3,56,679,0,0),(3,56,680,0,0),(3,56,681,0,0),(3,56,682,0,0),(3,56,683,0,0),(3,56,684,0,0),(3,56,685,0,0),(3,56,686,0,0),(3,56,687,0,0),(3,56,688,0,0),(3,56,689,0,0),(3,47,690,0,0),(3,57,691,0,0),(3,57,692,0,0),(3,57,693,0,0),(3,57,694,0,0),(3,57,695,0,0),(3,57,696,0,0),(3,57,697,0,0),(3,57,698,0,0),(3,57,699,0,0),(3,57,700,0,0),(3,57,701,0,0),(3,57,702,0,0),(3,57,703,0,0),(3,57,704,0,0),(3,57,705,0,0),(3,57,706,0,0),(3,57,707,0,0),(3,57,708,0,0),(3,57,709,0,0),(3,57,710,0,0),(3,57,711,0,0),(3,57,712,0,0),(3,57,713,0,0),(3,18,714,0,0),(3,42,715,0,0),(3,42,716,0,0),(3,13,717,0,0),(3,14,718,0,0),(3,38,719,0,0),(3,29,720,0,0),(3,58,721,0,0),(3,58,722,0,0),(3,58,723,0,0),(3,58,724,0,0),(3,58,725,0,0),(3,58,726,0,0),(3,58,727,0,0),(3,58,728,0,0),(3,58,729,0,0),(3,56,730,0,0),(3,29,731,0,0),(3,57,732,0,0),(3,57,733,0,0),(3,49,734,0,0),(3,7,735,0,0),(3,6,736,0,0),(3,6,737,0,0),(3,4,738,0,0),(3,4,739,0,0),(3,2,740,0,0),(3,2,741,0,0),(3,13,742,0,0),(3,14,743,0,0),(3,38,744,0,0),(3,57,745,0,0),(3,2,746,0,0),(3,4,747,0,0),(3,6,748,0,0),(3,7,749,0,0),(3,8,750,0,0),(3,13,751,0,0),(3,14,752,0,0),(3,15,753,0,0),(3,18,754,0,0),(3,19,755,0,0),(3,20,756,0,0),(3,21,757,0,0),(3,22,758,0,0),(3,23,759,0,0),(3,26,760,0,0),(3,37,761,0,0),(3,38,762,0,0),(3,41,763,0,0),(3,42,764,0,0),(3,43,765,0,0),(3,48,766,0,0),(3,49,767,0,0),(3,50,768,0,0),(3,52,769,0,0),(3,56,770,0,0),(3,57,771,0,0),(3,58,772,0,0),(3,62,773,0,0),(3,62,774,0,0),(3,62,775,0,0),(3,62,776,0,0),(3,62,777,0,0),(3,62,778,0,0),(3,62,779,0,0),(3,62,780,0,0),(3,62,781,0,0),(3,23,782,0,0),(3,22,783,0,0),(3,20,784,0,0),(3,21,785,0,0),(3,63,786,0,0),(3,63,787,0,0),(3,63,788,0,0),(3,63,789,0,0),(3,63,790,0,0),(3,63,791,0,0),(3,63,792,0,0),(3,63,793,0,0),(3,63,794,0,0),(3,63,795,0,0),(3,63,796,0,0),(3,63,797,0,0),(3,63,798,0,0),(3,63,799,0,0),(3,63,800,0,0),(3,63,801,0,0),(3,63,802,0,0),(3,63,803,0,0),(3,63,804,0,0),(3,63,805,0,0),(3,63,806,0,0),(3,63,807,0,0),(3,63,808,0,0),(3,63,809,0,0),(3,63,810,0,0),(3,63,811,0,0),(3,63,812,0,0),(3,63,813,0,0),(3,63,814,0,0),(3,63,815,0,0),(3,64,816,0,0),(3,64,817,0,0),(3,64,818,0,0),(3,64,819,0,0),(3,64,820,0,0),(3,64,821,0,0),(3,64,822,0,0),(3,64,823,0,0),(3,64,824,0,0),(3,64,825,0,0),(3,64,826,0,0),(3,64,827,0,0),(3,64,828,0,0),(4,6,1,0,0),(4,6,2,0,0),(4,6,3,0,0),(4,6,4,0,0),(4,6,5,0,0),(4,6,6,0,0),(4,6,7,0,0),(4,6,8,0,0),(4,6,9,0,0),(4,6,10,0,0),(4,6,11,0,0),(4,6,12,0,0),(4,6,13,0,0),(4,6,14,0,0),(4,6,15,0,0),(4,6,16,0,0),(4,6,17,0,0),(4,6,18,0,0),(4,6,19,0,0),(4,6,20,0,0),(4,6,21,0,0),(4,6,22,0,0),(4,6,23,0,0),(4,6,24,0,0),(4,6,25,0,0),(4,6,26,0,0),(4,6,27,0,0),(4,6,28,0,0),(4,6,29,0,0),(4,6,30,0,0),(4,6,31,0,0),(4,6,32,0,0),(4,6,33,0,0),(4,6,34,0,0),(4,6,35,0,0),(4,6,36,0,0),(4,7,37,0,0),(4,7,38,0,0),(4,7,39,0,0),(4,7,40,0,0),(4,7,41,0,0),(4,7,42,0,0),(4,7,43,0,0),(4,7,44,0,0),(4,7,45,0,0),(4,7,46,0,0),(4,7,47,0,0),(4,7,48,0,0),(4,7,49,0,0),(4,7,50,0,0),(4,7,51,0,0),(4,7,52,0,0),(4,7,53,0,0),(4,7,54,0,0),(4,7,55,0,0),(4,7,56,0,0),(4,7,57,0,0),(4,7,58,0,0),(4,7,59,0,0),(4,7,60,0,0),(4,7,61,0,0),(4,7,62,0,0),(4,7,63,0,0),(4,7,64,0,0),(4,7,65,0,0),(4,4,66,0,0),(4,4,67,0,0),(4,4,68,0,0),(4,4,69,0,0),(4,4,70,0,0),(4,4,71,0,0),(4,4,72,0,0),(4,4,73,0,0),(4,4,74,0,0),(4,4,75,0,0),(4,4,76,0,0),(4,4,77,0,0),(4,4,78,0,0),(4,4,79,0,0),(4,4,80,0,0),(4,4,81,0,0),(4,4,82,0,0),(4,4,83,0,0),(4,4,84,0,0),(4,4,85,0,0),(4,4,86,0,0),(4,4,87,0,0),(4,4,88,0,0),(4,4,89,0,0),(4,4,90,0,0),(4,4,91,0,0),(4,4,92,0,0),(4,4,93,0,0),(4,4,94,0,0),(4,4,95,0,0),(4,4,96,0,0),(4,4,97,0,0),(4,4,98,0,0),(4,4,99,0,0),(4,4,100,0,0),(4,4,101,0,0),(4,4,102,0,0),(4,4,103,0,0),(4,4,104,0,0),(4,4,105,0,0),(4,4,106,0,0),(4,4,107,0,0),(4,4,108,0,0),(4,4,109,0,0),(4,2,110,0,0),(4,2,111,0,0),(4,2,112,0,0),(4,2,113,0,0),(4,2,114,0,0),(4,2,115,0,0),(4,2,116,0,0),(4,2,117,0,0),(4,2,118,0,0),(4,2,119,0,0),(4,2,120,0,0),(4,2,121,0,0),(4,2,122,0,0),(4,2,123,0,0),(4,2,124,0,0),(4,2,125,0,0),(4,26,126,0,0),(4,26,127,0,0),(4,26,128,0,0),(4,26,129,0,0),(4,26,130,0,0),(4,26,131,0,0),(4,26,132,0,0),(4,26,133,0,0),(4,26,134,0,0),(4,26,135,0,0),(4,26,136,0,0),(4,26,137,0,0),(4,26,138,0,0),(4,26,139,0,0),(4,26,140,0,0),(4,26,141,0,0),(4,26,142,0,0),(4,26,143,0,0),(4,26,144,0,0),(4,26,145,0,0),(4,26,146,0,0),(4,26,147,0,0),(4,26,148,0,0),(4,26,149,0,0),(4,26,150,0,0),(4,4,151,0,0),(4,6,152,0,0),(4,7,153,0,0),(4,26,154,0,0),(4,13,155,0,0),(4,13,156,0,0),(4,13,157,0,0),(4,13,158,0,0),(4,13,159,0,0),(4,13,160,0,0),(4,13,161,0,0),(4,13,162,0,0),(4,13,163,0,0),(4,13,164,0,0),(4,13,165,0,0),(4,13,166,0,0),(4,13,167,0,0),(4,13,168,0,0),(4,13,169,0,0),(4,13,170,0,0),(4,13,171,0,0),(4,13,172,0,0),(4,13,173,0,0),(4,14,174,0,0),(4,14,175,0,0),(4,14,176,0,0),(4,14,177,0,0),(4,14,178,0,0),(4,14,179,0,0),(4,14,180,0,0),(4,14,181,0,0),(4,14,182,0,0),(4,14,183,0,0),(4,14,184,0,0),(4,14,185,0,0),(4,14,186,0,0),(4,14,187,0,0),(4,14,188,0,0),(4,14,189,0,0),(4,14,190,0,0),(4,14,191,0,0),(4,14,192,0,0),(4,14,193,0,0),(4,14,194,0,0),(4,14,195,0,0),(4,14,196,0,0),(4,14,197,0,0),(4,14,198,0,0),(4,14,199,0,0),(4,14,200,0,0),(4,14,201,0,0),(4,14,202,0,0),(4,14,203,0,0),(4,14,204,0,0),(4,8,205,0,0),(4,8,206,0,0),(4,8,207,0,0),(4,8,208,0,0),(4,8,209,0,0),(4,8,210,0,0),(4,8,211,0,0),(4,8,212,0,0),(4,8,213,0,0),(4,8,214,0,0),(4,8,215,0,0),(4,8,216,0,0),(4,8,217,0,0),(4,8,218,0,0),(4,8,219,0,0),(4,10,220,0,0),(4,10,221,0,0),(4,10,222,0,0),(4,10,223,0,0),(4,10,224,0,0),(4,10,225,0,0),(4,10,226,0,0),(4,10,227,0,0),(4,10,228,0,0),(4,10,229,0,0),(4,10,230,0,0),(4,10,231,0,0),(4,9,232,0,0),(4,9,233,0,0),(4,9,234,0,0),(4,9,235,0,0),(4,9,236,0,0),(4,9,237,0,0),(4,9,238,0,0),(4,9,239,0,0),(4,9,240,0,0),(4,9,241,0,0),(4,9,242,0,0),(4,9,243,0,0),(4,9,244,0,0),(4,9,245,0,0),(4,9,246,0,0),(4,9,247,0,0),(4,9,248,0,0),(4,9,249,0,0),(4,9,250,0,0),(4,9,251,0,0),(4,9,252,0,0),(4,9,253,0,0),(4,9,254,0,0),(4,9,255,0,0),(4,16,256,0,0),(4,16,257,0,0),(4,16,258,0,0),(4,16,259,0,0),(4,16,260,0,0),(4,16,261,0,0),(4,16,262,0,0),(4,16,263,0,0),(4,16,264,0,0),(4,16,265,0,0),(4,16,266,0,0),(4,16,267,0,0),(4,16,268,0,0),(4,16,269,0,0),(4,16,270,0,0),(4,16,271,0,0),(4,16,272,0,0),(4,16,273,0,0),(4,16,274,0,0),(4,16,275,0,0),(4,16,276,0,0),(4,16,277,0,0),(4,16,278,0,0),(4,15,279,0,0),(4,15,280,0,0),(4,15,281,0,0),(4,15,282,0,0),(4,15,283,0,0),(4,15,284,0,0),(4,15,285,0,0),(4,15,286,0,0),(4,15,287,0,0),(4,15,288,0,0),(4,18,289,0,0),(4,18,290,0,0),(4,18,291,0,0),(4,18,292,0,0),(4,18,293,0,0),(4,18,294,0,0),(4,18,295,0,0),(4,18,296,0,0),(4,18,297,0,0),(4,18,298,0,0),(4,18,299,0,0),(4,18,300,0,0),(4,18,301,0,0),(4,18,302,0,0),(4,18,303,0,0),(4,18,304,0,0),(4,18,305,0,0),(4,19,306,0,0),(4,19,307,0,0),(4,19,308,0,0),(4,19,309,0,0),(4,19,310,0,0),(4,19,311,0,0),(4,19,312,0,0),(4,19,313,0,0),(4,20,314,0,0),(4,20,315,0,0),(4,20,316,0,0),(4,20,317,0,0),(4,20,318,0,0),(4,20,319,0,0),(4,20,320,0,0),(4,20,321,0,0),(4,20,322,0,0),(4,20,323,0,0),(4,20,324,0,0),(4,20,325,0,0),(4,20,326,0,0),(4,20,327,0,0),(4,20,328,0,0),(4,20,329,0,0),(4,20,330,0,0),(4,20,331,0,0),(4,20,332,0,0),(4,20,333,0,0),(4,20,334,0,0),(4,20,335,0,0),(4,20,336,0,0),(4,20,337,0,0),(4,20,338,0,0),(4,20,339,0,0),(4,20,340,0,0),(4,20,341,0,0),(4,20,342,0,0),(4,20,343,0,0),(4,20,344,0,0),(4,20,345,0,0),(4,20,346,0,0),(4,20,347,0,0),(4,20,348,0,0),(4,20,349,0,0),(4,20,350,0,0),(4,21,351,0,0),(4,21,352,0,0),(4,21,353,0,0),(4,21,354,0,0),(4,21,355,0,0),(4,21,356,0,0),(4,21,357,0,0),(4,21,358,0,0),(4,21,359,0,0),(4,21,360,0,0),(4,21,361,0,0),(4,21,362,0,0),(4,21,363,0,0),(4,21,364,0,0),(4,21,365,0,0),(4,21,366,0,0),(4,21,367,0,0),(4,21,368,0,0),(4,21,369,0,0),(4,21,370,0,0),(4,21,371,0,0),(4,21,372,0,0),(4,21,373,0,0),(4,21,374,0,0),(4,21,375,0,0),(4,21,376,0,0),(4,21,377,0,0),(4,21,378,0,0),(4,21,379,0,0),(4,21,380,0,0),(4,21,381,0,0),(4,21,382,0,0),(4,21,383,0,0),(4,21,384,0,0),(4,21,385,0,0),(4,21,386,0,0),(4,21,387,0,0),(4,21,388,0,0),(4,22,389,0,0),(4,22,390,0,0),(4,22,391,0,0),(4,22,392,0,0),(4,22,393,0,0),(4,22,394,0,0),(4,22,395,0,0),(4,22,396,0,0),(4,22,397,0,0),(4,22,398,0,0),(4,22,399,0,0),(4,22,400,0,0),(4,22,401,0,0),(4,22,402,0,0),(4,22,403,0,0),(4,22,404,0,0),(4,22,405,0,0),(4,22,406,0,0),(4,22,407,0,0),(4,22,408,0,0),(4,22,409,0,0),(4,22,410,0,0),(4,22,411,0,0),(4,22,412,0,0),(4,22,413,0,0),(4,22,414,0,0),(4,22,415,0,0),(4,22,416,0,0),(4,22,417,0,0),(4,22,418,0,0),(4,22,419,0,0),(4,22,420,0,0),(4,22,421,0,0),(4,22,422,0,0),(4,22,423,0,0),(4,22,424,0,0),(4,22,425,0,0),(4,22,426,0,0),(4,22,427,0,0),(4,22,428,0,0),(4,22,429,0,0),(4,22,430,0,0),(4,22,431,0,0),(4,22,432,0,0),(4,22,433,0,0),(4,22,434,0,0),(4,22,435,0,0),(4,23,436,0,0),(4,23,437,0,0),(4,23,438,0,0),(4,23,439,0,0),(4,23,440,0,0),(4,23,441,0,0),(4,23,442,0,0),(4,23,443,0,0),(4,23,444,0,0),(4,23,445,0,0),(4,23,446,0,0),(4,23,447,0,0),(4,23,448,0,0),(4,23,449,0,0),(4,23,450,0,0),(4,23,451,0,0),(4,23,452,0,0),(4,23,453,0,0),(4,23,454,0,0),(4,23,455,0,0),(4,23,456,0,0),(4,23,457,0,0),(4,23,458,0,0),(4,23,459,0,0),(4,23,460,0,0),(4,23,461,0,0),(4,23,462,0,0),(4,23,463,0,0),(4,23,464,0,0),(4,23,465,0,0),(4,23,466,0,0),(4,23,467,0,0),(4,23,468,0,0),(4,23,469,0,0),(4,23,470,0,0),(4,23,471,0,0),(4,23,472,0,0),(4,23,473,0,0),(4,23,474,0,0),(4,10,519,0,0),(4,10,520,0,0),(4,10,521,0,0),(4,10,522,0,0),(4,10,523,0,0),(4,10,524,0,0),(4,36,525,0,0),(4,36,526,0,0),(4,36,527,0,0),(4,36,528,0,0),(4,37,531,0,0),(4,37,532,0,0),(4,37,533,0,0),(4,37,534,0,0),(4,37,535,0,0),(4,37,536,0,0),(4,37,537,0,0),(4,37,538,0,0),(4,37,539,0,0),(4,37,540,0,0),(4,37,541,0,0),(4,37,542,0,0),(4,37,543,0,0),(4,37,544,0,0),(4,37,545,0,0),(4,37,546,0,0),(4,37,547,0,0),(4,37,548,0,0),(4,37,549,0,0),(4,38,550,0,0),(4,38,551,0,0),(4,38,552,0,0),(4,38,553,0,0),(4,38,554,0,0),(4,38,555,0,0),(4,38,556,0,0),(4,38,557,0,0),(4,38,558,0,0),(4,38,559,0,0),(4,38,560,0,0),(4,38,561,0,0),(4,38,562,0,0),(4,38,563,0,0),(4,38,564,0,0),(4,38,565,0,0),(4,38,566,0,0),(4,38,567,0,0),(4,38,568,0,0),(4,41,569,0,0),(4,41,570,0,0),(4,41,571,0,0),(4,41,572,0,0),(4,41,573,0,0),(4,41,574,0,0),(4,41,575,0,0),(4,41,576,0,0),(4,41,577,0,0),(4,41,578,0,0),(4,41,579,0,0),(4,41,580,0,0),(4,41,581,0,0),(4,41,582,0,0),(4,42,583,0,0),(4,42,584,0,0),(4,42,585,0,0),(4,42,586,0,0),(4,42,587,0,0),(4,42,588,0,0),(4,42,589,0,0),(4,42,590,0,0),(4,42,591,0,0),(4,42,592,0,0),(4,42,593,0,0),(4,42,594,0,0),(4,42,595,0,0),(4,42,596,0,0),(4,42,597,0,0),(4,42,598,0,0),(4,42,599,0,0),(4,43,600,0,0),(4,43,601,0,0),(4,43,602,0,0),(4,43,603,0,0),(4,43,604,0,0),(4,43,605,0,0),(4,43,606,0,0),(4,43,607,0,0),(4,43,608,0,0),(4,43,609,0,0),(4,43,610,0,0),(4,43,611,0,0),(4,43,612,0,0),(4,43,613,0,0),(4,43,614,0,0),(4,43,615,0,0),(4,43,616,0,0),(4,47,617,0,0),(4,47,618,0,0),(4,47,619,0,0),(4,47,620,0,0),(4,47,621,0,0),(4,47,622,0,0),(4,47,623,0,0),(4,48,624,0,0),(4,48,625,0,0),(4,48,626,0,0),(4,48,627,0,0),(4,48,628,0,0),(4,48,629,0,0),(4,48,630,0,0),(4,48,631,0,0),(4,48,632,0,0),(4,48,633,0,0),(4,49,634,0,0),(4,49,635,0,0),(4,49,636,0,0),(4,49,637,0,0),(4,49,638,0,0),(4,49,639,0,0),(4,49,640,0,0),(4,49,641,0,0),(4,49,642,0,0),(4,49,643,0,0),(4,49,644,0,0),(4,49,645,0,0),(4,49,646,0,0),(4,49,647,0,0),(4,49,648,0,0),(4,50,649,0,0),(4,50,650,0,0),(4,50,651,0,0),(4,50,652,0,0),(4,50,653,0,0),(4,50,654,0,0),(4,50,655,0,0),(4,50,656,0,0),(4,50,657,0,0),(4,50,658,0,0),(4,50,659,0,0),(4,50,660,0,0),(4,50,661,0,0),(4,50,662,0,0),(4,50,663,0,0),(4,50,664,0,0),(4,50,665,0,0),(4,52,666,0,0),(4,52,667,0,0),(4,52,668,0,0),(4,52,669,0,0),(4,52,670,0,0),(4,2,671,0,0),(4,50,672,0,0),(4,49,673,0,0),(4,2,674,0,0),(4,13,675,0,0),(4,29,676,0,0),(4,56,677,0,0),(4,56,678,0,0),(4,56,679,0,0),(4,56,680,0,0),(4,56,681,0,0),(4,56,682,0,0),(4,56,683,0,0),(4,56,684,0,0),(4,56,685,0,0),(4,56,686,0,0),(4,56,687,0,0),(4,56,688,0,0),(4,56,689,0,0),(4,47,690,0,0),(4,57,691,0,0),(4,57,692,0,0),(4,57,693,0,0),(4,57,694,0,0),(4,57,695,0,0),(4,57,696,0,0),(4,57,697,0,0),(4,57,698,0,0),(4,57,699,0,0),(4,57,700,0,0),(4,57,701,0,0),(4,57,702,0,0),(4,57,703,0,0),(4,57,704,0,0),(4,57,705,0,0),(4,57,706,0,0),(4,57,707,0,0),(4,57,708,0,0),(4,57,709,0,0),(4,57,710,0,0),(4,57,711,0,0),(4,57,712,0,0),(4,57,713,0,0),(4,18,714,0,0),(4,42,715,0,0),(4,42,716,0,0),(4,13,717,0,0),(4,14,718,0,0),(4,38,719,0,0),(4,29,720,0,0),(4,58,721,0,0),(4,58,722,0,0),(4,58,723,0,0),(4,58,724,0,0),(4,58,725,0,0),(4,58,726,0,0),(4,58,727,0,0),(4,58,728,0,0),(4,58,729,0,0),(4,56,730,0,0),(4,29,731,0,0),(4,57,732,0,0),(4,57,733,0,0),(4,49,734,0,0),(4,7,735,0,0),(4,6,736,0,0),(4,6,737,0,0),(4,4,738,0,0),(4,4,739,0,0),(4,2,740,0,0),(4,2,741,0,0),(4,13,742,0,0),(4,14,743,0,0),(4,38,744,0,0),(4,57,745,0,0),(4,2,746,0,0),(4,4,747,0,0),(4,6,748,0,0),(4,7,749,0,0),(4,8,750,0,0),(4,13,751,0,0),(4,14,752,0,0),(4,15,753,0,0),(4,18,754,0,0),(4,19,755,0,0),(4,20,756,0,0),(4,21,757,0,0),(4,22,758,0,0),(4,23,759,0,0),(4,26,760,0,0),(4,37,761,0,0),(4,38,762,0,0),(4,41,763,0,0),(4,42,764,0,0),(4,43,765,0,0),(4,48,766,0,0),(4,49,767,0,0),(4,50,768,0,0),(4,52,769,0,0),(4,56,770,0,0),(4,57,771,0,0),(4,58,772,0,0),(4,62,773,0,0),(4,62,774,0,0),(4,62,775,0,0),(4,62,776,0,0),(4,62,777,0,0),(4,62,778,0,0),(4,62,779,0,0),(4,62,780,0,0),(4,62,781,0,0),(4,23,782,0,0),(4,22,783,0,0),(4,20,784,0,0),(4,21,785,0,0),(4,63,786,0,0),(4,63,787,0,0),(4,63,788,0,0),(4,63,789,0,0),(4,63,790,0,0),(4,63,791,0,0),(4,63,792,0,0),(4,63,793,0,0),(4,63,794,0,0),(4,63,795,0,0),(4,63,796,0,0),(4,63,797,0,0),(4,63,798,0,0),(4,63,799,0,0),(4,63,800,0,0),(4,63,801,0,0),(4,63,802,0,0),(4,63,803,0,0),(4,63,804,0,0),(4,63,805,0,0),(4,63,806,0,0),(4,63,807,0,0),(4,63,808,0,0),(4,63,809,0,0),(4,63,810,0,0),(4,63,811,0,0),(4,63,812,0,0),(4,63,813,0,0),(4,63,814,0,0),(4,63,815,0,0),(4,64,816,0,0),(4,64,817,0,0),(4,64,818,0,0),(4,64,819,0,0),(4,64,820,0,0),(4,64,821,0,0),(4,64,822,0,0),(4,64,823,0,0),(4,64,824,0,0),(4,64,825,0,0),(4,64,826,0,0),(4,64,827,0,0),(4,64,828,0,0);
/*!40000 ALTER TABLE `vtiger_profile2field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_profile2globalpermissions`
--

DROP TABLE IF EXISTS `vtiger_profile2globalpermissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_profile2globalpermissions` (
  `profileid` int(19) NOT NULL,
  `globalactionid` int(19) NOT NULL,
  `globalactionpermission` int(19) DEFAULT NULL,
  PRIMARY KEY (`profileid`,`globalactionid`),
  CONSTRAINT `fk_1_vtiger_profile2globalpermissions` FOREIGN KEY (`profileid`) REFERENCES `vtiger_profile` (`profileid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_profile2globalpermissions`
--

LOCK TABLES `vtiger_profile2globalpermissions` WRITE;
/*!40000 ALTER TABLE `vtiger_profile2globalpermissions` DISABLE KEYS */;
INSERT INTO `vtiger_profile2globalpermissions` VALUES (1,1,0),(1,2,0),(2,1,1),(2,2,1),(3,1,1),(3,2,1),(4,1,1),(4,2,1);
/*!40000 ALTER TABLE `vtiger_profile2globalpermissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_profile2standardpermissions`
--

DROP TABLE IF EXISTS `vtiger_profile2standardpermissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_profile2standardpermissions` (
  `profileid` int(11) NOT NULL,
  `tabid` int(10) NOT NULL,
  `operation` int(10) NOT NULL,
  `permissions` int(1) DEFAULT NULL,
  PRIMARY KEY (`profileid`,`tabid`,`operation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_profile2standardpermissions`
--

LOCK TABLES `vtiger_profile2standardpermissions` WRITE;
/*!40000 ALTER TABLE `vtiger_profile2standardpermissions` DISABLE KEYS */;
INSERT INTO `vtiger_profile2standardpermissions` VALUES (1,2,0,0),(1,2,1,0),(1,2,2,0),(1,2,3,0),(1,2,4,0),(1,2,7,0),(1,4,0,0),(1,4,1,0),(1,4,2,0),(1,4,3,0),(1,4,4,0),(1,4,7,0),(1,6,0,0),(1,6,1,0),(1,6,2,0),(1,6,3,0),(1,6,4,0),(1,6,7,0),(1,7,0,0),(1,7,1,0),(1,7,2,0),(1,7,3,0),(1,7,4,0),(1,7,7,0),(1,8,0,0),(1,8,1,0),(1,8,2,0),(1,8,3,0),(1,8,4,0),(1,8,7,0),(1,9,0,0),(1,9,1,0),(1,9,2,0),(1,9,3,0),(1,9,4,0),(1,9,7,0),(1,10,0,0),(1,10,1,0),(1,10,2,0),(1,10,3,0),(1,10,4,0),(1,10,7,0),(1,13,0,0),(1,13,1,0),(1,13,2,0),(1,13,3,0),(1,13,4,0),(1,13,7,0),(1,14,0,0),(1,14,1,0),(1,14,2,0),(1,14,3,0),(1,14,4,0),(1,14,7,0),(1,15,0,0),(1,15,1,0),(1,15,2,0),(1,15,3,0),(1,15,4,0),(1,15,7,0),(1,16,0,0),(1,16,1,0),(1,16,2,0),(1,16,3,0),(1,16,4,0),(1,16,7,0),(1,18,0,0),(1,18,1,0),(1,18,2,0),(1,18,3,0),(1,18,4,0),(1,18,7,0),(1,19,0,0),(1,19,1,0),(1,19,2,0),(1,19,3,0),(1,19,4,0),(1,19,7,0),(1,20,0,0),(1,20,1,0),(1,20,2,0),(1,20,3,0),(1,20,4,0),(1,20,7,0),(1,21,0,0),(1,21,1,0),(1,21,2,0),(1,21,3,0),(1,21,4,0),(1,21,7,0),(1,22,0,0),(1,22,1,0),(1,22,2,0),(1,22,3,0),(1,22,4,0),(1,22,7,0),(1,23,0,0),(1,23,1,0),(1,23,2,0),(1,23,3,0),(1,23,4,0),(1,23,7,0),(1,26,0,0),(1,26,1,0),(1,26,2,0),(1,26,3,0),(1,26,4,0),(1,26,7,0),(1,36,0,0),(1,36,1,0),(1,36,2,0),(1,36,3,0),(1,36,4,0),(1,36,7,0),(1,37,0,0),(1,37,1,0),(1,37,2,0),(1,37,3,0),(1,37,4,0),(1,37,7,0),(1,38,0,0),(1,38,1,0),(1,38,2,0),(1,38,3,0),(1,38,4,0),(1,38,7,0),(1,41,0,0),(1,41,1,0),(1,41,2,0),(1,41,3,0),(1,41,4,0),(1,41,7,0),(1,42,0,0),(1,42,1,0),(1,42,2,0),(1,42,3,0),(1,42,4,0),(1,42,7,0),(1,43,0,0),(1,43,1,0),(1,43,2,0),(1,43,3,0),(1,43,4,0),(1,43,7,0),(1,47,0,0),(1,47,1,0),(1,47,2,0),(1,47,3,0),(1,47,4,0),(1,47,7,0),(1,48,0,0),(1,48,1,0),(1,48,2,0),(1,48,3,0),(1,48,4,0),(1,48,7,0),(1,49,0,0),(1,49,1,0),(1,49,2,0),(1,49,3,0),(1,49,4,0),(1,49,7,0),(1,50,0,0),(1,50,1,0),(1,50,2,0),(1,50,3,0),(1,50,4,0),(1,50,7,0),(1,52,0,0),(1,52,1,0),(1,52,2,0),(1,52,3,0),(1,52,4,0),(1,52,7,0),(1,56,0,0),(1,56,1,0),(1,56,2,0),(1,56,3,0),(1,56,4,0),(1,56,7,0),(1,57,0,0),(1,57,1,0),(1,57,2,0),(1,57,3,0),(1,57,4,0),(1,57,7,0),(1,58,0,0),(1,58,1,0),(1,58,2,0),(1,58,3,0),(1,58,4,0),(1,58,7,0),(1,62,0,0),(1,62,1,0),(1,62,2,0),(1,62,3,0),(1,62,4,0),(1,62,7,0),(1,63,0,0),(1,63,1,0),(1,63,2,0),(1,63,3,0),(1,63,4,0),(1,63,7,0),(1,64,0,0),(1,64,1,0),(1,64,2,0),(1,64,3,0),(1,64,4,0),(1,64,7,0),(2,2,0,0),(2,2,1,0),(2,2,2,0),(2,2,3,0),(2,2,4,0),(2,2,7,0),(2,4,0,0),(2,4,1,0),(2,4,2,0),(2,4,3,0),(2,4,4,0),(2,4,7,0),(2,6,0,0),(2,6,1,0),(2,6,2,0),(2,6,3,0),(2,6,4,0),(2,6,7,0),(2,7,0,0),(2,7,1,0),(2,7,2,0),(2,7,3,0),(2,7,4,0),(2,7,7,0),(2,8,0,0),(2,8,1,0),(2,8,2,0),(2,8,3,0),(2,8,4,0),(2,8,7,0),(2,9,0,0),(2,9,1,0),(2,9,2,0),(2,9,3,0),(2,9,4,0),(2,9,7,0),(2,10,0,0),(2,10,1,0),(2,10,2,0),(2,10,3,0),(2,10,4,0),(2,10,7,0),(2,13,0,1),(2,13,1,1),(2,13,2,1),(2,13,3,0),(2,13,4,0),(2,13,7,1),(2,14,0,0),(2,14,1,0),(2,14,2,0),(2,14,3,0),(2,14,4,0),(2,14,7,0),(2,15,0,0),(2,15,1,0),(2,15,2,0),(2,15,3,0),(2,15,4,0),(2,15,7,0),(2,16,0,0),(2,16,1,0),(2,16,2,0),(2,16,3,0),(2,16,4,0),(2,16,7,0),(2,18,0,0),(2,18,1,0),(2,18,2,0),(2,18,3,0),(2,18,4,0),(2,18,7,0),(2,19,0,0),(2,19,1,0),(2,19,2,0),(2,19,3,0),(2,19,4,0),(2,19,7,0),(2,20,0,0),(2,20,1,0),(2,20,2,0),(2,20,3,0),(2,20,4,0),(2,20,7,0),(2,21,0,0),(2,21,1,0),(2,21,2,0),(2,21,3,0),(2,21,4,0),(2,21,7,0),(2,22,0,0),(2,22,1,0),(2,22,2,0),(2,22,3,0),(2,22,4,0),(2,22,7,0),(2,23,0,0),(2,23,1,0),(2,23,2,0),(2,23,3,0),(2,23,4,0),(2,23,7,0),(2,26,0,0),(2,26,1,0),(2,26,2,0),(2,26,3,0),(2,26,4,0),(2,26,7,0),(2,36,0,0),(2,36,1,0),(2,36,2,0),(2,36,3,0),(2,36,4,0),(2,36,7,0),(2,37,0,0),(2,37,1,0),(2,37,2,0),(2,37,3,0),(2,37,4,0),(2,37,7,0),(2,38,0,0),(2,38,1,0),(2,38,2,0),(2,38,3,0),(2,38,4,0),(2,38,7,0),(2,41,0,0),(2,41,1,0),(2,41,2,0),(2,41,3,0),(2,41,4,0),(2,41,7,0),(2,42,0,0),(2,42,1,0),(2,42,2,0),(2,42,3,0),(2,42,4,0),(2,42,7,0),(2,43,0,0),(2,43,1,0),(2,43,2,0),(2,43,3,0),(2,43,4,0),(2,43,7,0),(2,47,0,0),(2,47,1,0),(2,47,2,0),(2,47,3,0),(2,47,4,0),(2,47,7,0),(2,48,0,0),(2,48,1,0),(2,48,2,0),(2,48,3,0),(2,48,4,0),(2,48,7,0),(2,49,0,0),(2,49,1,0),(2,49,2,0),(2,49,3,0),(2,49,4,0),(2,49,7,0),(2,50,0,0),(2,50,1,0),(2,50,2,0),(2,50,3,0),(2,50,4,0),(2,50,7,0),(2,52,0,0),(2,52,1,0),(2,52,2,0),(2,52,3,0),(2,52,4,0),(2,52,7,0),(2,56,0,0),(2,56,1,0),(2,56,2,0),(2,56,3,0),(2,56,4,0),(2,56,7,0),(2,57,0,0),(2,57,1,0),(2,57,2,0),(2,57,3,0),(2,57,4,0),(2,57,7,0),(2,58,0,0),(2,58,1,0),(2,58,2,0),(2,58,3,0),(2,58,4,0),(2,58,7,0),(2,62,0,0),(2,62,1,0),(2,62,2,0),(2,62,3,0),(2,62,4,0),(2,62,7,0),(2,63,0,0),(2,63,1,0),(2,63,2,0),(2,63,3,0),(2,63,4,0),(2,63,7,0),(2,64,0,0),(2,64,1,0),(2,64,2,0),(2,64,3,0),(2,64,4,0),(2,64,7,0),(3,2,0,1),(3,2,1,1),(3,2,2,1),(3,2,3,0),(3,2,4,0),(3,2,7,1),(3,4,0,0),(3,4,1,0),(3,4,2,0),(3,4,3,0),(3,4,4,0),(3,4,7,0),(3,6,0,0),(3,6,1,0),(3,6,2,0),(3,6,3,0),(3,6,4,0),(3,6,7,0),(3,7,0,0),(3,7,1,0),(3,7,2,0),(3,7,3,0),(3,7,4,0),(3,7,7,0),(3,8,0,0),(3,8,1,0),(3,8,2,0),(3,8,3,0),(3,8,4,0),(3,8,7,0),(3,9,0,0),(3,9,1,0),(3,9,2,0),(3,9,3,0),(3,9,4,0),(3,9,7,0),(3,10,0,0),(3,10,1,0),(3,10,2,0),(3,10,3,0),(3,10,4,0),(3,10,7,0),(3,13,0,0),(3,13,1,0),(3,13,2,0),(3,13,3,0),(3,13,4,0),(3,13,7,0),(3,14,0,0),(3,14,1,0),(3,14,2,0),(3,14,3,0),(3,14,4,0),(3,14,7,0),(3,15,0,0),(3,15,1,0),(3,15,2,0),(3,15,3,0),(3,15,4,0),(3,15,7,0),(3,16,0,0),(3,16,1,0),(3,16,2,0),(3,16,3,0),(3,16,4,0),(3,16,7,0),(3,18,0,0),(3,18,1,0),(3,18,2,0),(3,18,3,0),(3,18,4,0),(3,18,7,0),(3,19,0,0),(3,19,1,0),(3,19,2,0),(3,19,3,0),(3,19,4,0),(3,19,7,0),(3,20,0,0),(3,20,1,0),(3,20,2,0),(3,20,3,0),(3,20,4,0),(3,20,7,0),(3,21,0,0),(3,21,1,0),(3,21,2,0),(3,21,3,0),(3,21,4,0),(3,21,7,0),(3,22,0,0),(3,22,1,0),(3,22,2,0),(3,22,3,0),(3,22,4,0),(3,22,7,0),(3,23,0,0),(3,23,1,0),(3,23,2,0),(3,23,3,0),(3,23,4,0),(3,23,7,0),(3,26,0,0),(3,26,1,0),(3,26,2,0),(3,26,3,0),(3,26,4,0),(3,26,7,0),(3,36,0,0),(3,36,1,0),(3,36,2,0),(3,36,3,0),(3,36,4,0),(3,36,7,0),(3,37,0,0),(3,37,1,0),(3,37,2,0),(3,37,3,0),(3,37,4,0),(3,37,7,0),(3,38,0,0),(3,38,1,0),(3,38,2,0),(3,38,3,0),(3,38,4,0),(3,38,7,0),(3,41,0,0),(3,41,1,0),(3,41,2,0),(3,41,3,0),(3,41,4,0),(3,41,7,0),(3,42,0,0),(3,42,1,0),(3,42,2,0),(3,42,3,0),(3,42,4,0),(3,42,7,0),(3,43,0,0),(3,43,1,0),(3,43,2,0),(3,43,3,0),(3,43,4,0),(3,43,7,0),(3,47,0,0),(3,47,1,0),(3,47,2,0),(3,47,3,0),(3,47,4,0),(3,47,7,0),(3,48,0,0),(3,48,1,0),(3,48,2,0),(3,48,3,0),(3,48,4,0),(3,48,7,0),(3,49,0,0),(3,49,1,0),(3,49,2,0),(3,49,3,0),(3,49,4,0),(3,49,7,0),(3,50,0,0),(3,50,1,0),(3,50,2,0),(3,50,3,0),(3,50,4,0),(3,50,7,0),(3,52,0,0),(3,52,1,0),(3,52,2,0),(3,52,3,0),(3,52,4,0),(3,52,7,0),(3,56,0,0),(3,56,1,0),(3,56,2,0),(3,56,3,0),(3,56,4,0),(3,56,7,0),(3,57,0,0),(3,57,1,0),(3,57,2,0),(3,57,3,0),(3,57,4,0),(3,57,7,0),(3,58,0,0),(3,58,1,0),(3,58,2,0),(3,58,3,0),(3,58,4,0),(3,58,7,0),(3,62,0,0),(3,62,1,0),(3,62,2,0),(3,62,3,0),(3,62,4,0),(3,62,7,0),(3,63,0,0),(3,63,1,0),(3,63,2,0),(3,63,3,0),(3,63,4,0),(3,63,7,0),(3,64,0,0),(3,64,1,0),(3,64,2,0),(3,64,3,0),(3,64,4,0),(3,64,7,0),(4,2,0,1),(4,2,1,1),(4,2,2,1),(4,2,3,0),(4,2,4,0),(4,2,7,1),(4,4,0,1),(4,4,1,1),(4,4,2,1),(4,4,3,0),(4,4,4,0),(4,4,7,1),(4,6,0,1),(4,6,1,1),(4,6,2,1),(4,6,3,0),(4,6,4,0),(4,6,7,1),(4,7,0,1),(4,7,1,1),(4,7,2,1),(4,7,3,0),(4,7,4,0),(4,7,7,1),(4,8,0,1),(4,8,1,1),(4,8,2,1),(4,8,3,0),(4,8,4,0),(4,8,7,1),(4,9,0,1),(4,9,1,1),(4,9,2,1),(4,9,3,0),(4,9,4,0),(4,9,7,1),(4,10,0,0),(4,10,1,0),(4,10,2,0),(4,10,3,0),(4,10,4,0),(4,10,7,0),(4,13,0,1),(4,13,1,1),(4,13,2,1),(4,13,3,0),(4,13,4,0),(4,13,7,1),(4,14,0,1),(4,14,1,1),(4,14,2,1),(4,14,3,0),(4,14,4,0),(4,14,7,1),(4,15,0,1),(4,15,1,1),(4,15,2,1),(4,15,3,0),(4,15,4,0),(4,15,7,1),(4,16,0,1),(4,16,1,1),(4,16,2,1),(4,16,3,0),(4,16,4,0),(4,16,7,1),(4,18,0,1),(4,18,1,1),(4,18,2,1),(4,18,3,0),(4,18,4,0),(4,18,7,1),(4,19,0,1),(4,19,1,1),(4,19,2,1),(4,19,3,0),(4,19,4,0),(4,19,7,1),(4,20,0,1),(4,20,1,1),(4,20,2,1),(4,20,3,0),(4,20,4,0),(4,20,7,1),(4,21,0,1),(4,21,1,1),(4,21,2,1),(4,21,3,0),(4,21,4,0),(4,21,7,1),(4,22,0,1),(4,22,1,1),(4,22,2,1),(4,22,3,0),(4,22,4,0),(4,22,7,1),(4,23,0,1),(4,23,1,1),(4,23,2,1),(4,23,3,0),(4,23,4,0),(4,23,7,1),(4,26,0,1),(4,26,1,1),(4,26,2,1),(4,26,3,0),(4,26,4,0),(4,26,7,1),(4,36,0,0),(4,36,1,0),(4,36,2,0),(4,36,3,0),(4,36,4,0),(4,36,7,0),(4,37,0,0),(4,37,1,0),(4,37,2,0),(4,37,3,0),(4,37,4,0),(4,37,7,0),(4,38,0,0),(4,38,1,0),(4,38,2,0),(4,38,3,0),(4,38,4,0),(4,38,7,0),(4,41,0,0),(4,41,1,0),(4,41,2,0),(4,41,3,0),(4,41,4,0),(4,41,7,0),(4,42,0,0),(4,42,1,0),(4,42,2,0),(4,42,3,0),(4,42,4,0),(4,42,7,0),(4,43,0,0),(4,43,1,0),(4,43,2,0),(4,43,3,0),(4,43,4,0),(4,43,7,0),(4,47,0,0),(4,47,1,0),(4,47,2,0),(4,47,3,0),(4,47,4,0),(4,47,7,0),(4,48,0,0),(4,48,1,0),(4,48,2,0),(4,48,3,0),(4,48,4,0),(4,48,7,0),(4,49,0,0),(4,49,1,0),(4,49,2,0),(4,49,3,0),(4,49,4,0),(4,49,7,0),(4,50,0,0),(4,50,1,0),(4,50,2,0),(4,50,3,0),(4,50,4,0),(4,50,7,0),(4,52,0,0),(4,52,1,0),(4,52,2,0),(4,52,3,0),(4,52,4,0),(4,52,7,0),(4,56,0,0),(4,56,1,0),(4,56,2,0),(4,56,3,0),(4,56,4,0),(4,56,7,0),(4,57,0,0),(4,57,1,0),(4,57,2,0),(4,57,3,0),(4,57,4,0),(4,57,7,0),(4,58,0,0),(4,58,1,0),(4,58,2,0),(4,58,3,0),(4,58,4,0),(4,58,7,0),(4,62,0,0),(4,62,1,0),(4,62,2,0),(4,62,3,0),(4,62,4,0),(4,62,7,0),(4,63,0,0),(4,63,1,0),(4,63,2,0),(4,63,3,0),(4,63,4,0),(4,63,7,0),(4,64,0,0),(4,64,1,0),(4,64,2,0),(4,64,3,0),(4,64,4,0),(4,64,7,0);
/*!40000 ALTER TABLE `vtiger_profile2standardpermissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_profile2tab`
--

DROP TABLE IF EXISTS `vtiger_profile2tab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_profile2tab` (
  `profileid` int(11) DEFAULT NULL,
  `tabid` int(10) DEFAULT NULL,
  `permissions` int(10) NOT NULL DEFAULT '0',
  KEY `profile2tab_profileid_tabid_idx` (`profileid`,`tabid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_profile2tab`
--

LOCK TABLES `vtiger_profile2tab` WRITE;
/*!40000 ALTER TABLE `vtiger_profile2tab` DISABLE KEYS */;
INSERT INTO `vtiger_profile2tab` VALUES (1,1,0),(1,2,0),(1,3,0),(1,4,0),(1,6,0),(1,7,0),(1,8,0),(1,9,0),(1,10,0),(1,13,0),(1,14,0),(1,15,0),(1,16,0),(1,18,0),(1,19,0),(1,20,0),(1,21,0),(1,22,0),(1,23,0),(1,24,0),(1,25,0),(1,26,0),(1,27,0),(2,1,0),(2,2,0),(2,3,0),(2,4,0),(2,6,0),(2,7,0),(2,8,0),(2,9,0),(2,10,0),(2,13,0),(2,14,0),(2,15,0),(2,16,0),(2,18,0),(2,19,0),(2,20,0),(2,21,0),(2,22,0),(2,23,0),(2,24,0),(2,25,0),(2,26,0),(2,27,0),(3,1,0),(3,2,0),(3,3,0),(3,4,0),(3,6,0),(3,7,0),(3,8,0),(3,9,0),(3,10,0),(3,13,0),(3,14,0),(3,15,0),(3,16,0),(3,18,0),(3,19,0),(3,20,0),(3,21,0),(3,22,0),(3,23,0),(3,24,0),(3,25,0),(3,26,0),(3,27,0),(4,1,0),(4,2,0),(4,3,0),(4,4,0),(4,6,0),(4,7,0),(4,8,0),(4,9,0),(4,10,0),(4,13,0),(4,14,0),(4,15,0),(4,16,0),(4,18,0),(4,19,0),(4,20,0),(4,21,0),(4,22,0),(4,23,0),(4,24,0),(4,25,0),(4,26,0),(4,27,0),(1,30,0),(2,30,0),(3,30,0),(4,30,0),(1,31,0),(2,31,0),(3,31,0),(4,31,0),(1,33,0),(2,33,0),(3,33,0),(4,33,0),(1,34,0),(2,34,0),(3,34,0),(4,34,0),(1,35,0),(2,35,0),(3,35,0),(4,35,0),(1,36,0),(2,36,0),(3,36,0),(4,36,0),(1,37,0),(2,37,0),(3,37,0),(4,37,0),(1,38,0),(2,38,0),(3,38,0),(4,38,0),(1,39,0),(2,39,0),(3,39,0),(4,39,0),(1,40,0),(2,40,0),(3,40,0),(4,40,0),(1,41,0),(2,41,0),(3,41,0),(4,41,0),(1,42,0),(2,42,0),(3,42,0),(4,42,0),(1,43,0),(2,43,0),(3,43,0),(4,43,0),(1,44,0),(2,44,0),(3,44,0),(4,44,0),(1,45,0),(2,45,0),(3,45,0),(4,45,0),(1,47,0),(2,47,0),(3,47,0),(4,47,0),(1,48,0),(2,48,0),(3,48,0),(4,48,0),(1,49,0),(2,49,0),(3,49,0),(4,49,0),(1,50,0),(2,50,0),(3,50,0),(4,50,0),(1,51,0),(2,51,0),(3,51,0),(4,51,0),(1,52,0),(2,52,0),(3,52,0),(4,52,0),(1,53,0),(2,53,0),(3,53,0),(4,53,0),(1,54,0),(2,54,0),(3,54,0),(4,54,0),(1,55,0),(2,55,0),(3,55,0),(4,55,0),(1,56,0),(2,56,0),(3,56,0),(4,56,0),(1,57,0),(2,57,0),(3,57,0),(4,57,0),(1,58,0),(2,58,0),(3,58,0),(4,58,0),(1,10,0),(2,10,0),(3,10,0),(4,10,0),(1,59,0),(2,59,0),(3,59,0),(4,59,0),(1,60,0),(2,60,0),(3,60,0),(4,60,0),(1,61,0),(2,61,0),(3,61,0),(4,61,0),(1,62,0),(2,62,0),(3,62,0),(4,62,0),(1,63,0),(2,63,0),(3,63,0),(4,63,0),(1,64,0),(2,64,0),(3,64,0),(4,64,0);
/*!40000 ALTER TABLE `vtiger_profile2tab` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_profile2utility`
--

DROP TABLE IF EXISTS `vtiger_profile2utility`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_profile2utility` (
  `profileid` int(11) NOT NULL,
  `tabid` int(11) NOT NULL,
  `activityid` int(11) NOT NULL,
  `permission` int(1) DEFAULT NULL,
  PRIMARY KEY (`profileid`,`tabid`,`activityid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_profile2utility`
--

LOCK TABLES `vtiger_profile2utility` WRITE;
/*!40000 ALTER TABLE `vtiger_profile2utility` DISABLE KEYS */;
INSERT INTO `vtiger_profile2utility` VALUES (1,2,5,0),(1,2,6,0),(1,2,10,0),(1,4,5,0),(1,4,6,0),(1,4,8,0),(1,4,10,0),(1,6,5,0),(1,6,6,0),(1,6,8,0),(1,6,10,0),(1,7,5,0),(1,7,6,0),(1,7,8,0),(1,7,9,0),(1,7,10,0),(1,8,6,0),(1,13,5,0),(1,13,6,0),(1,13,8,0),(1,13,10,0),(1,14,5,0),(1,14,6,0),(1,14,10,0),(1,18,5,0),(1,18,6,0),(1,18,10,0),(1,19,5,0),(1,19,6,0),(1,20,5,0),(1,20,6,0),(1,21,5,0),(1,21,6,0),(1,22,5,0),(1,22,6,0),(1,23,5,0),(1,23,6,0),(1,26,5,0),(1,26,6,0),(1,26,8,1),(1,26,10,0),(1,36,5,1),(1,36,6,1),(1,36,8,1),(1,37,5,0),(1,37,6,0),(1,37,10,0),(1,38,5,0),(1,38,6,0),(1,38,10,0),(1,41,5,1),(1,41,8,1),(1,42,5,0),(1,42,6,0),(1,42,8,0),(1,43,5,0),(1,43,6,0),(1,43,10,0),(1,47,5,0),(1,47,6,0),(1,47,10,0),(1,48,5,0),(1,48,6,0),(1,48,10,0),(1,49,5,0),(1,49,6,0),(1,49,10,0),(1,50,5,0),(1,50,6,0),(1,50,10,0),(1,56,5,0),(1,56,6,0),(1,56,8,0),(1,57,5,0),(1,57,6,0),(1,57,8,0),(1,58,5,0),(1,58,6,0),(1,58,8,0),(1,60,5,1),(1,60,6,1),(1,60,8,1),(1,61,5,1),(1,61,6,1),(1,61,8,1),(1,62,5,0),(1,62,6,0),(1,62,8,0),(1,64,5,0),(1,64,6,0),(1,64,8,0),(2,2,5,1),(2,2,6,1),(2,2,10,0),(2,4,5,1),(2,4,6,1),(2,4,8,0),(2,4,10,0),(2,6,5,1),(2,6,6,1),(2,6,8,0),(2,6,10,0),(2,7,5,1),(2,7,6,1),(2,7,8,0),(2,7,9,0),(2,7,10,0),(2,8,6,1),(2,13,5,1),(2,13,6,1),(2,13,8,0),(2,13,10,0),(2,14,5,1),(2,14,6,1),(2,14,10,0),(2,18,5,1),(2,18,6,1),(2,18,10,0),(2,19,5,0),(2,19,6,0),(2,20,5,0),(2,20,6,0),(2,21,5,0),(2,21,6,0),(2,22,5,0),(2,22,6,0),(2,23,5,0),(2,23,6,0),(2,26,5,0),(2,26,6,0),(2,26,8,1),(2,26,10,0),(2,36,5,1),(2,36,6,1),(2,36,8,1),(2,37,5,0),(2,37,6,0),(2,37,10,0),(2,38,5,0),(2,38,6,0),(2,38,10,0),(2,41,5,1),(2,41,8,1),(2,42,5,0),(2,42,6,0),(2,42,8,0),(2,43,5,0),(2,43,6,0),(2,43,10,0),(2,47,5,0),(2,47,6,0),(2,47,10,0),(2,48,5,0),(2,48,6,0),(2,48,10,0),(2,49,5,0),(2,49,6,0),(2,49,10,0),(2,50,5,0),(2,50,6,0),(2,50,10,0),(2,56,5,0),(2,56,6,0),(2,56,8,0),(2,57,5,0),(2,57,6,0),(2,57,8,0),(2,58,5,0),(2,58,6,0),(2,58,8,0),(2,60,5,1),(2,60,6,1),(2,60,8,1),(2,61,5,1),(2,61,6,1),(2,61,8,1),(2,62,5,0),(2,62,6,0),(2,62,8,0),(2,64,5,0),(2,64,6,0),(2,64,8,0),(3,2,5,1),(3,2,6,1),(3,2,10,0),(3,4,5,1),(3,4,6,1),(3,4,8,0),(3,4,10,0),(3,6,5,1),(3,6,6,1),(3,6,8,0),(3,6,10,0),(3,7,5,1),(3,7,6,1),(3,7,8,0),(3,7,9,0),(3,7,10,0),(3,8,6,1),(3,13,5,1),(3,13,6,1),(3,13,8,0),(3,13,10,0),(3,14,5,1),(3,14,6,1),(3,14,10,0),(3,18,5,1),(3,18,6,1),(3,18,10,0),(3,19,5,0),(3,19,6,0),(3,20,5,0),(3,20,6,0),(3,21,5,0),(3,21,6,0),(3,22,5,0),(3,22,6,0),(3,23,5,0),(3,23,6,0),(3,26,5,0),(3,26,6,0),(3,26,8,1),(3,26,10,0),(3,36,5,1),(3,36,6,1),(3,36,8,1),(3,37,5,0),(3,37,6,0),(3,37,10,0),(3,38,5,0),(3,38,6,0),(3,38,10,0),(3,41,5,1),(3,41,8,1),(3,42,5,0),(3,42,6,0),(3,42,8,0),(3,43,5,0),(3,43,6,0),(3,43,10,0),(3,47,5,0),(3,47,6,0),(3,47,10,0),(3,48,5,0),(3,48,6,0),(3,48,10,0),(3,49,5,0),(3,49,6,0),(3,49,10,0),(3,50,5,0),(3,50,6,0),(3,50,10,0),(3,56,5,0),(3,56,6,0),(3,56,8,0),(3,57,5,0),(3,57,6,0),(3,57,8,0),(3,58,5,0),(3,58,6,0),(3,58,8,0),(3,60,5,1),(3,60,6,1),(3,60,8,1),(3,61,5,1),(3,61,6,1),(3,61,8,1),(3,62,5,0),(3,62,6,0),(3,62,8,0),(3,64,5,0),(3,64,6,0),(3,64,8,0),(4,2,5,1),(4,2,6,1),(4,2,10,0),(4,4,5,1),(4,4,6,1),(4,4,8,1),(4,4,10,0),(4,6,5,1),(4,6,6,1),(4,6,8,1),(4,6,10,0),(4,7,5,1),(4,7,6,1),(4,7,8,1),(4,7,9,0),(4,7,10,0),(4,8,6,1),(4,13,5,1),(4,13,6,1),(4,13,8,1),(4,13,10,0),(4,14,5,1),(4,14,6,1),(4,14,10,0),(4,18,5,1),(4,18,6,1),(4,18,10,0),(4,19,5,0),(4,19,6,0),(4,20,5,0),(4,20,6,0),(4,21,5,0),(4,21,6,0),(4,22,5,0),(4,22,6,0),(4,23,5,0),(4,23,6,0),(4,26,5,0),(4,26,6,0),(4,26,8,1),(4,26,10,0),(4,36,5,1),(4,36,6,1),(4,36,8,1),(4,37,5,0),(4,37,6,0),(4,37,10,0),(4,38,5,0),(4,38,6,0),(4,38,10,0),(4,41,5,1),(4,41,8,1),(4,42,5,0),(4,42,6,0),(4,42,8,0),(4,43,5,0),(4,43,6,0),(4,43,10,0),(4,47,5,0),(4,47,6,0),(4,47,10,0),(4,48,5,0),(4,48,6,0),(4,48,10,0),(4,49,5,0),(4,49,6,0),(4,49,10,0),(4,50,5,0),(4,50,6,0),(4,50,10,0),(4,56,5,0),(4,56,6,0),(4,56,8,0),(4,57,5,0),(4,57,6,0),(4,57,8,0),(4,58,5,0),(4,58,6,0),(4,58,8,0),(4,60,5,1),(4,60,6,1),(4,60,8,1),(4,61,5,1),(4,61,6,1),(4,61,8,1),(4,62,5,0),(4,62,6,0),(4,62,8,0),(4,64,5,0),(4,64,6,0),(4,64,8,0);
/*!40000 ALTER TABLE `vtiger_profile2utility` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_profile_seq`
--

DROP TABLE IF EXISTS `vtiger_profile_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_profile_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_profile_seq`
--

LOCK TABLES `vtiger_profile_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_profile_seq` DISABLE KEYS */;
INSERT INTO `vtiger_profile_seq` VALUES (4);
/*!40000 ALTER TABLE `vtiger_profile_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_progress`
--

DROP TABLE IF EXISTS `vtiger_progress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_progress` (
  `progressid` int(11) NOT NULL AUTO_INCREMENT,
  `progress` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`progressid`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_progress`
--

LOCK TABLES `vtiger_progress` WRITE;
/*!40000 ALTER TABLE `vtiger_progress` DISABLE KEYS */;
INSERT INTO `vtiger_progress` VALUES (1,'--none--',1,287),(2,'10%',1,288),(3,'20%',1,289),(4,'30%',1,290),(5,'40%',1,291),(6,'50%',1,292),(7,'60%',1,293),(8,'70%',1,294),(9,'80%',1,295),(10,'90%',1,296),(11,'100%',1,297);
/*!40000 ALTER TABLE `vtiger_progress` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_progress_seq`
--

DROP TABLE IF EXISTS `vtiger_progress_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_progress_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_progress_seq`
--

LOCK TABLES `vtiger_progress_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_progress_seq` DISABLE KEYS */;
INSERT INTO `vtiger_progress_seq` VALUES (11);
/*!40000 ALTER TABLE `vtiger_progress_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_project`
--

DROP TABLE IF EXISTS `vtiger_project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_project` (
  `projectid` int(11) NOT NULL DEFAULT '0',
  `projectname` varchar(255) DEFAULT NULL,
  `project_no` varchar(100) DEFAULT NULL,
  `startdate` date DEFAULT NULL,
  `targetenddate` date DEFAULT NULL,
  `actualenddate` date DEFAULT NULL,
  `targetbudget` varchar(255) DEFAULT NULL,
  `projecturl` varchar(255) DEFAULT NULL,
  `projectstatus` varchar(100) DEFAULT NULL,
  `projectpriority` varchar(100) DEFAULT NULL,
  `projecttype` varchar(100) DEFAULT NULL,
  `progress` varchar(100) DEFAULT NULL,
  `linktoaccountscontacts` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`projectid`),
  KEY `linktoaccountscontacts` (`linktoaccountscontacts`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_project`
--

LOCK TABLES `vtiger_project` WRITE;
/*!40000 ALTER TABLE `vtiger_project` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_project` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_projectcf`
--

DROP TABLE IF EXISTS `vtiger_projectcf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_projectcf` (
  `projectid` int(11) NOT NULL,
  PRIMARY KEY (`projectid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_projectcf`
--

LOCK TABLES `vtiger_projectcf` WRITE;
/*!40000 ALTER TABLE `vtiger_projectcf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_projectcf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_projectmilestone`
--

DROP TABLE IF EXISTS `vtiger_projectmilestone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_projectmilestone` (
  `projectmilestoneid` int(11) NOT NULL,
  `projectmilestonename` varchar(255) DEFAULT NULL,
  `projectmilestone_no` varchar(100) DEFAULT NULL,
  `projectmilestonedate` date DEFAULT NULL,
  `projectid` varchar(100) DEFAULT NULL,
  `projectmilestonetype` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`projectmilestoneid`),
  KEY `projectmilestone_projectid_idx` (`projectid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_projectmilestone`
--

LOCK TABLES `vtiger_projectmilestone` WRITE;
/*!40000 ALTER TABLE `vtiger_projectmilestone` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_projectmilestone` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_projectmilestonecf`
--

DROP TABLE IF EXISTS `vtiger_projectmilestonecf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_projectmilestonecf` (
  `projectmilestoneid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`projectmilestoneid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_projectmilestonecf`
--

LOCK TABLES `vtiger_projectmilestonecf` WRITE;
/*!40000 ALTER TABLE `vtiger_projectmilestonecf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_projectmilestonecf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_projectmilestonetype`
--

DROP TABLE IF EXISTS `vtiger_projectmilestonetype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_projectmilestonetype` (
  `projectmilestonetypeid` int(11) NOT NULL AUTO_INCREMENT,
  `projectmilestonetype` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`projectmilestonetypeid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_projectmilestonetype`
--

LOCK TABLES `vtiger_projectmilestonetype` WRITE;
/*!40000 ALTER TABLE `vtiger_projectmilestonetype` DISABLE KEYS */;
INSERT INTO `vtiger_projectmilestonetype` VALUES (1,'--none--',1,247),(2,'administrative',1,248),(3,'operative',1,249),(4,'other',1,250);
/*!40000 ALTER TABLE `vtiger_projectmilestonetype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_projectmilestonetype_seq`
--

DROP TABLE IF EXISTS `vtiger_projectmilestonetype_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_projectmilestonetype_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_projectmilestonetype_seq`
--

LOCK TABLES `vtiger_projectmilestonetype_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_projectmilestonetype_seq` DISABLE KEYS */;
INSERT INTO `vtiger_projectmilestonetype_seq` VALUES (4);
/*!40000 ALTER TABLE `vtiger_projectmilestonetype_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_projectpriority`
--

DROP TABLE IF EXISTS `vtiger_projectpriority`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_projectpriority` (
  `projectpriorityid` int(11) NOT NULL AUTO_INCREMENT,
  `projectpriority` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`projectpriorityid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_projectpriority`
--

LOCK TABLES `vtiger_projectpriority` WRITE;
/*!40000 ALTER TABLE `vtiger_projectpriority` DISABLE KEYS */;
INSERT INTO `vtiger_projectpriority` VALUES (1,'--none--',1,283),(2,'low',1,284),(3,'normal',1,285),(4,'high',1,286);
/*!40000 ALTER TABLE `vtiger_projectpriority` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_projectpriority_seq`
--

DROP TABLE IF EXISTS `vtiger_projectpriority_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_projectpriority_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_projectpriority_seq`
--

LOCK TABLES `vtiger_projectpriority_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_projectpriority_seq` DISABLE KEYS */;
INSERT INTO `vtiger_projectpriority_seq` VALUES (4);
/*!40000 ALTER TABLE `vtiger_projectpriority_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_projectstatus`
--

DROP TABLE IF EXISTS `vtiger_projectstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_projectstatus` (
  `projectstatusid` int(11) NOT NULL AUTO_INCREMENT,
  `projectstatus` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`projectstatusid`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_projectstatus`
--

LOCK TABLES `vtiger_projectstatus` WRITE;
/*!40000 ALTER TABLE `vtiger_projectstatus` DISABLE KEYS */;
INSERT INTO `vtiger_projectstatus` VALUES (1,'--none--',1,270),(2,'prospecting',1,271),(3,'initiated',1,272),(4,'in progress',1,273),(5,'waiting for feedback',1,274),(6,'on hold',1,275),(7,'completed',1,276),(8,'delivered',1,277),(9,'archived',1,278);
/*!40000 ALTER TABLE `vtiger_projectstatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_projectstatus_seq`
--

DROP TABLE IF EXISTS `vtiger_projectstatus_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_projectstatus_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_projectstatus_seq`
--

LOCK TABLES `vtiger_projectstatus_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_projectstatus_seq` DISABLE KEYS */;
INSERT INTO `vtiger_projectstatus_seq` VALUES (9);
/*!40000 ALTER TABLE `vtiger_projectstatus_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_projecttask`
--

DROP TABLE IF EXISTS `vtiger_projecttask`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_projecttask` (
  `projecttaskid` int(11) NOT NULL,
  `projecttaskname` varchar(255) DEFAULT NULL,
  `projecttask_no` varchar(100) DEFAULT NULL,
  `projecttasktype` varchar(100) DEFAULT NULL,
  `projecttaskpriority` varchar(100) DEFAULT NULL,
  `projecttaskprogress` varchar(100) DEFAULT NULL,
  `projecttaskhours` varchar(255) DEFAULT NULL,
  `startdate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  `projectid` varchar(100) DEFAULT NULL,
  `projecttasknumber` int(11) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `projecttaskstatus` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`projecttaskid`),
  KEY `projecttask_projectid_idx` (`projectid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_projecttask`
--

LOCK TABLES `vtiger_projecttask` WRITE;
/*!40000 ALTER TABLE `vtiger_projecttask` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_projecttask` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_projecttaskcf`
--

DROP TABLE IF EXISTS `vtiger_projecttaskcf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_projecttaskcf` (
  `projecttaskid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`projecttaskid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_projecttaskcf`
--

LOCK TABLES `vtiger_projecttaskcf` WRITE;
/*!40000 ALTER TABLE `vtiger_projecttaskcf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_projecttaskcf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_projecttaskpriority`
--

DROP TABLE IF EXISTS `vtiger_projecttaskpriority`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_projecttaskpriority` (
  `projecttaskpriorityid` int(11) NOT NULL AUTO_INCREMENT,
  `projecttaskpriority` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`projecttaskpriorityid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_projecttaskpriority`
--

LOCK TABLES `vtiger_projecttaskpriority` WRITE;
/*!40000 ALTER TABLE `vtiger_projecttaskpriority` DISABLE KEYS */;
INSERT INTO `vtiger_projecttaskpriority` VALUES (1,'--none--',1,255),(2,'low',1,256),(3,'normal',1,257),(4,'high',1,258);
/*!40000 ALTER TABLE `vtiger_projecttaskpriority` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_projecttaskpriority_seq`
--

DROP TABLE IF EXISTS `vtiger_projecttaskpriority_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_projecttaskpriority_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_projecttaskpriority_seq`
--

LOCK TABLES `vtiger_projecttaskpriority_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_projecttaskpriority_seq` DISABLE KEYS */;
INSERT INTO `vtiger_projecttaskpriority_seq` VALUES (4);
/*!40000 ALTER TABLE `vtiger_projecttaskpriority_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_projecttaskprogress`
--

DROP TABLE IF EXISTS `vtiger_projecttaskprogress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_projecttaskprogress` (
  `projecttaskprogressid` int(11) NOT NULL AUTO_INCREMENT,
  `projecttaskprogress` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`projecttaskprogressid`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_projecttaskprogress`
--

LOCK TABLES `vtiger_projecttaskprogress` WRITE;
/*!40000 ALTER TABLE `vtiger_projecttaskprogress` DISABLE KEYS */;
INSERT INTO `vtiger_projecttaskprogress` VALUES (1,'--none--',1,259),(2,'10%',1,260),(3,'20%',1,261),(4,'30%',1,262),(5,'40%',1,263),(6,'50%',1,264),(7,'60%',1,265),(8,'70%',1,266),(9,'80%',1,267),(10,'90%',1,268),(11,'100%',1,269);
/*!40000 ALTER TABLE `vtiger_projecttaskprogress` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_projecttaskprogress_seq`
--

DROP TABLE IF EXISTS `vtiger_projecttaskprogress_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_projecttaskprogress_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_projecttaskprogress_seq`
--

LOCK TABLES `vtiger_projecttaskprogress_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_projecttaskprogress_seq` DISABLE KEYS */;
INSERT INTO `vtiger_projecttaskprogress_seq` VALUES (11);
/*!40000 ALTER TABLE `vtiger_projecttaskprogress_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_projecttaskstatus`
--

DROP TABLE IF EXISTS `vtiger_projecttaskstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_projecttaskstatus` (
  `projecttaskstatusid` int(11) NOT NULL AUTO_INCREMENT,
  `projecttaskstatus` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`projecttaskstatusid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_projecttaskstatus`
--

LOCK TABLES `vtiger_projecttaskstatus` WRITE;
/*!40000 ALTER TABLE `vtiger_projecttaskstatus` DISABLE KEYS */;
INSERT INTO `vtiger_projecttaskstatus` VALUES (1,'--None--',1,469),(2,'Open',1,470),(3,'In Progress',1,471),(4,'Completed',1,472),(5,'Deferred',1,473),(6,'Cancelled',1,474);
/*!40000 ALTER TABLE `vtiger_projecttaskstatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_projecttaskstatus_seq`
--

DROP TABLE IF EXISTS `vtiger_projecttaskstatus_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_projecttaskstatus_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_projecttaskstatus_seq`
--

LOCK TABLES `vtiger_projecttaskstatus_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_projecttaskstatus_seq` DISABLE KEYS */;
INSERT INTO `vtiger_projecttaskstatus_seq` VALUES (6);
/*!40000 ALTER TABLE `vtiger_projecttaskstatus_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_projecttasktype`
--

DROP TABLE IF EXISTS `vtiger_projecttasktype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_projecttasktype` (
  `projecttasktypeid` int(11) NOT NULL AUTO_INCREMENT,
  `projecttasktype` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`projecttasktypeid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_projecttasktype`
--

LOCK TABLES `vtiger_projecttasktype` WRITE;
/*!40000 ALTER TABLE `vtiger_projecttasktype` DISABLE KEYS */;
INSERT INTO `vtiger_projecttasktype` VALUES (1,'--none--',1,251),(2,'administrative',1,252),(3,'operative',1,253),(4,'other',1,254);
/*!40000 ALTER TABLE `vtiger_projecttasktype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_projecttasktype_seq`
--

DROP TABLE IF EXISTS `vtiger_projecttasktype_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_projecttasktype_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_projecttasktype_seq`
--

LOCK TABLES `vtiger_projecttasktype_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_projecttasktype_seq` DISABLE KEYS */;
INSERT INTO `vtiger_projecttasktype_seq` VALUES (4);
/*!40000 ALTER TABLE `vtiger_projecttasktype_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_projecttype`
--

DROP TABLE IF EXISTS `vtiger_projecttype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_projecttype` (
  `projecttypeid` int(11) NOT NULL AUTO_INCREMENT,
  `projecttype` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`projecttypeid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_projecttype`
--

LOCK TABLES `vtiger_projecttype` WRITE;
/*!40000 ALTER TABLE `vtiger_projecttype` DISABLE KEYS */;
INSERT INTO `vtiger_projecttype` VALUES (1,'--none--',1,279),(2,'administrative',1,280),(3,'operative',1,281),(4,'other',1,282);
/*!40000 ALTER TABLE `vtiger_projecttype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_projecttype_seq`
--

DROP TABLE IF EXISTS `vtiger_projecttype_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_projecttype_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_projecttype_seq`
--

LOCK TABLES `vtiger_projecttype_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_projecttype_seq` DISABLE KEYS */;
INSERT INTO `vtiger_projecttype_seq` VALUES (4);
/*!40000 ALTER TABLE `vtiger_projecttype_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_purchaseorder`
--

DROP TABLE IF EXISTS `vtiger_purchaseorder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_purchaseorder` (
  `purchaseorderid` int(19) NOT NULL DEFAULT '0',
  `subject` varchar(100) DEFAULT NULL,
  `quoteid` int(19) DEFAULT NULL,
  `vendorid` int(19) DEFAULT NULL,
  `requisition_no` varchar(100) DEFAULT NULL,
  `purchaseorder_no` varchar(100) DEFAULT NULL,
  `tracking_no` varchar(100) DEFAULT NULL,
  `contactid` int(19) DEFAULT NULL,
  `duedate` date DEFAULT NULL,
  `carrier` varchar(200) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `adjustment` decimal(28,6) DEFAULT NULL,
  `salescommission` decimal(25,3) DEFAULT NULL,
  `exciseduty` decimal(25,3) DEFAULT NULL,
  `total` decimal(28,6) DEFAULT NULL,
  `subtotal` decimal(28,6) DEFAULT NULL,
  `taxtype` varchar(25) DEFAULT NULL,
  `discount_percent` decimal(25,3) DEFAULT NULL,
  `discount_amount` decimal(28,6) DEFAULT NULL,
  `s_h_amount` decimal(28,6) DEFAULT NULL,
  `terms_conditions` text,
  `postatus` varchar(200) DEFAULT NULL,
  `currency_id` int(19) NOT NULL DEFAULT '1',
  `conversion_rate` decimal(10,3) NOT NULL DEFAULT '1.000',
  `tandc` int(11) NOT NULL,
  PRIMARY KEY (`purchaseorderid`),
  KEY `purchaseorder_vendorid_idx` (`vendorid`),
  KEY `purchaseorder_quoteid_idx` (`quoteid`),
  KEY `purchaseorder_contactid_idx` (`contactid`),
  CONSTRAINT `fk_4_vtiger_purchaseorder` FOREIGN KEY (`vendorid`) REFERENCES `vtiger_vendor` (`vendorid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_purchaseorder`
--

LOCK TABLES `vtiger_purchaseorder` WRITE;
/*!40000 ALTER TABLE `vtiger_purchaseorder` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_purchaseorder` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_purchaseordercf`
--

DROP TABLE IF EXISTS `vtiger_purchaseordercf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_purchaseordercf` (
  `purchaseorderid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`purchaseorderid`),
  CONSTRAINT `fk_1_vtiger_purchaseordercf` FOREIGN KEY (`purchaseorderid`) REFERENCES `vtiger_purchaseorder` (`purchaseorderid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_purchaseordercf`
--

LOCK TABLES `vtiger_purchaseordercf` WRITE;
/*!40000 ALTER TABLE `vtiger_purchaseordercf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_purchaseordercf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_quickview`
--

DROP TABLE IF EXISTS `vtiger_quickview`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_quickview` (
  `fieldid` int(19) NOT NULL,
  `related_fieldid` int(19) NOT NULL,
  `sequence` int(19) NOT NULL,
  `currentview` int(19) NOT NULL,
  KEY `fk_1_vtiger_quickview` (`fieldid`),
  CONSTRAINT `fk_1_vtiger_quickview` FOREIGN KEY (`fieldid`) REFERENCES `vtiger_field` (`fieldid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_quickview`
--

LOCK TABLES `vtiger_quickview` WRITE;
/*!40000 ALTER TABLE `vtiger_quickview` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_quickview` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_quotes`
--

DROP TABLE IF EXISTS `vtiger_quotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_quotes` (
  `quoteid` int(19) NOT NULL DEFAULT '0',
  `subject` varchar(100) DEFAULT NULL,
  `potentialid` int(19) DEFAULT NULL,
  `quotestage` varchar(200) DEFAULT NULL,
  `validtill` date DEFAULT NULL,
  `contactid` int(19) DEFAULT NULL,
  `quote_no` varchar(100) DEFAULT NULL,
  `subtotal` decimal(28,6) DEFAULT NULL,
  `carrier` varchar(200) DEFAULT NULL,
  `shipping` varchar(100) DEFAULT NULL,
  `inventorymanager` int(19) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `adjustment` decimal(28,6) DEFAULT NULL,
  `total` decimal(28,6) DEFAULT NULL,
  `taxtype` varchar(25) DEFAULT NULL,
  `discount_percent` decimal(25,3) DEFAULT NULL,
  `discount_amount` decimal(28,6) DEFAULT NULL,
  `s_h_amount` decimal(28,6) DEFAULT NULL,
  `accountid` int(19) DEFAULT NULL,
  `terms_conditions` text,
  `currency_id` int(19) NOT NULL DEFAULT '1',
  `conversion_rate` decimal(10,3) NOT NULL DEFAULT '1.000',
  `tandc` int(11) NOT NULL,
  PRIMARY KEY (`quoteid`),
  KEY `quote_quotestage_idx` (`quotestage`),
  KEY `quotes_potentialid_idx` (`potentialid`),
  KEY `quotes_contactid_idx` (`contactid`),
  KEY `quotes_accountid_idx` (`accountid`),
  KEY `quotes_currencyid_idx` (`currency_id`),
  CONSTRAINT `fk_3_vtiger_quotes` FOREIGN KEY (`potentialid`) REFERENCES `vtiger_potential` (`potentialid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_quotes`
--

LOCK TABLES `vtiger_quotes` WRITE;
/*!40000 ALTER TABLE `vtiger_quotes` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_quotes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_quotesbillads`
--

DROP TABLE IF EXISTS `vtiger_quotesbillads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_quotesbillads` (
  `quotebilladdressid` int(19) NOT NULL DEFAULT '0',
  `bill_city` varchar(30) DEFAULT NULL,
  `bill_code` varchar(30) DEFAULT NULL,
  `bill_country` varchar(30) DEFAULT NULL,
  `bill_state` varchar(30) DEFAULT NULL,
  `bill_street` varchar(250) DEFAULT NULL,
  `bill_pobox` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`quotebilladdressid`),
  CONSTRAINT `fk_1_vtiger_quotesbillads` FOREIGN KEY (`quotebilladdressid`) REFERENCES `vtiger_quotes` (`quoteid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_quotesbillads`
--

LOCK TABLES `vtiger_quotesbillads` WRITE;
/*!40000 ALTER TABLE `vtiger_quotesbillads` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_quotesbillads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_quotescf`
--

DROP TABLE IF EXISTS `vtiger_quotescf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_quotescf` (
  `quoteid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`quoteid`),
  CONSTRAINT `fk_1_vtiger_quotescf` FOREIGN KEY (`quoteid`) REFERENCES `vtiger_quotes` (`quoteid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_quotescf`
--

LOCK TABLES `vtiger_quotescf` WRITE;
/*!40000 ALTER TABLE `vtiger_quotescf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_quotescf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_quotesshipads`
--

DROP TABLE IF EXISTS `vtiger_quotesshipads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_quotesshipads` (
  `quoteshipaddressid` int(19) NOT NULL DEFAULT '0',
  `ship_city` varchar(30) DEFAULT NULL,
  `ship_code` varchar(30) DEFAULT NULL,
  `ship_country` varchar(30) DEFAULT NULL,
  `ship_state` varchar(30) DEFAULT NULL,
  `ship_street` varchar(250) DEFAULT NULL,
  `ship_pobox` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`quoteshipaddressid`),
  CONSTRAINT `fk_1_vtiger_quotesshipads` FOREIGN KEY (`quoteshipaddressid`) REFERENCES `vtiger_quotes` (`quoteid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_quotesshipads`
--

LOCK TABLES `vtiger_quotesshipads` WRITE;
/*!40000 ALTER TABLE `vtiger_quotesshipads` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_quotesshipads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_quotestage`
--

DROP TABLE IF EXISTS `vtiger_quotestage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_quotestage` (
  `quotestageid` int(19) NOT NULL AUTO_INCREMENT,
  `quotestage` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`quotestageid`),
  UNIQUE KEY `quotestage_quotestage_idx` (`quotestage`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_quotestage`
--

LOCK TABLES `vtiger_quotestage` WRITE;
/*!40000 ALTER TABLE `vtiger_quotestage` DISABLE KEYS */;
INSERT INTO `vtiger_quotestage` VALUES (1,'Created',0,139),(2,'Delivered',0,140),(3,'Reviewed',0,141),(4,'Accepted',0,142),(5,'Rejected',0,143);
/*!40000 ALTER TABLE `vtiger_quotestage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_quotestage_seq`
--

DROP TABLE IF EXISTS `vtiger_quotestage_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_quotestage_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_quotestage_seq`
--

LOCK TABLES `vtiger_quotestage_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_quotestage_seq` DISABLE KEYS */;
INSERT INTO `vtiger_quotestage_seq` VALUES (5);
/*!40000 ALTER TABLE `vtiger_quotestage_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_quotestagehistory`
--

DROP TABLE IF EXISTS `vtiger_quotestagehistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_quotestagehistory` (
  `historyid` int(19) NOT NULL AUTO_INCREMENT,
  `quoteid` int(19) NOT NULL,
  `accountname` varchar(100) DEFAULT NULL,
  `total` decimal(28,6) DEFAULT NULL,
  `quotestage` varchar(200) DEFAULT NULL,
  `lastmodified` datetime DEFAULT NULL,
  PRIMARY KEY (`historyid`),
  KEY `quotestagehistory_quoteid_idx` (`quoteid`),
  CONSTRAINT `fk_1_vtiger_quotestagehistory` FOREIGN KEY (`quoteid`) REFERENCES `vtiger_quotes` (`quoteid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_quotestagehistory`
--

LOCK TABLES `vtiger_quotestagehistory` WRITE;
/*!40000 ALTER TABLE `vtiger_quotestagehistory` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_quotestagehistory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_rating`
--

DROP TABLE IF EXISTS `vtiger_rating`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_rating` (
  `rating_id` int(19) NOT NULL AUTO_INCREMENT,
  `rating` varchar(200) DEFAULT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rating_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_rating`
--

LOCK TABLES `vtiger_rating` WRITE;
/*!40000 ALTER TABLE `vtiger_rating` DISABLE KEYS */;
INSERT INTO `vtiger_rating` VALUES (1,'--None--',1,144),(2,'Acquired',1,145),(3,'Active',1,146),(4,'Market Failed',1,147),(5,'Project Cancelled',1,148),(6,'Shutdown',1,149);
/*!40000 ALTER TABLE `vtiger_rating` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_rating_seq`
--

DROP TABLE IF EXISTS `vtiger_rating_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_rating_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_rating_seq`
--

LOCK TABLES `vtiger_rating_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_rating_seq` DISABLE KEYS */;
INSERT INTO `vtiger_rating_seq` VALUES (6);
/*!40000 ALTER TABLE `vtiger_rating_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_recurring_frequency`
--

DROP TABLE IF EXISTS `vtiger_recurring_frequency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_recurring_frequency` (
  `recurring_frequency_id` int(11) DEFAULT NULL,
  `recurring_frequency` varchar(200) DEFAULT NULL,
  `sortorderid` int(11) DEFAULT NULL,
  `presence` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_recurring_frequency`
--

LOCK TABLES `vtiger_recurring_frequency` WRITE;
/*!40000 ALTER TABLE `vtiger_recurring_frequency` DISABLE KEYS */;
INSERT INTO `vtiger_recurring_frequency` VALUES (1,'--None--',0,1),(2,'Daily',1,1),(3,'Weekly',2,1),(4,'Monthly',3,1),(5,'Quarterly',4,1),(6,'Yearly',6,1),(7,'half-year',5,1),(8,'2years',7,1),(9,'3years',8,1),(10,'4years',9,1),(11,'5years',10,1);
/*!40000 ALTER TABLE `vtiger_recurring_frequency` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_recurring_frequency_seq`
--

DROP TABLE IF EXISTS `vtiger_recurring_frequency_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_recurring_frequency_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_recurring_frequency_seq`
--

LOCK TABLES `vtiger_recurring_frequency_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_recurring_frequency_seq` DISABLE KEYS */;
INSERT INTO `vtiger_recurring_frequency_seq` VALUES (11);
/*!40000 ALTER TABLE `vtiger_recurring_frequency_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_recurringevents`
--

DROP TABLE IF EXISTS `vtiger_recurringevents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_recurringevents` (
  `recurringid` int(19) NOT NULL AUTO_INCREMENT,
  `activityid` int(19) NOT NULL,
  `recurringdate` date DEFAULT NULL,
  `recurringtype` varchar(30) DEFAULT NULL,
  `recurringfreq` int(19) DEFAULT NULL,
  `recurringinfo` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`recurringid`),
  KEY `fk_1_vtiger_recurringevents` (`activityid`),
  CONSTRAINT `fk_1_vtiger_recurringevents` FOREIGN KEY (`activityid`) REFERENCES `vtiger_activity` (`activityid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_recurringevents`
--

LOCK TABLES `vtiger_recurringevents` WRITE;
/*!40000 ALTER TABLE `vtiger_recurringevents` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_recurringevents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_recurringtype`
--

DROP TABLE IF EXISTS `vtiger_recurringtype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_recurringtype` (
  `recurringeventid` int(19) NOT NULL AUTO_INCREMENT,
  `recurringtype` varchar(200) NOT NULL,
  `sortorderid` int(19) NOT NULL DEFAULT '0',
  `presence` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`recurringeventid`),
  UNIQUE KEY `recurringtype_status_idx` (`recurringtype`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_recurringtype`
--

LOCK TABLES `vtiger_recurringtype` WRITE;
/*!40000 ALTER TABLE `vtiger_recurringtype` DISABLE KEYS */;
INSERT INTO `vtiger_recurringtype` VALUES (1,'--None--',0,1),(2,'Daily',1,1),(3,'Weekly',2,1),(4,'Monthly',3,1),(5,'Yearly',4,1);
/*!40000 ALTER TABLE `vtiger_recurringtype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_recurringtype_seq`
--

DROP TABLE IF EXISTS `vtiger_recurringtype_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_recurringtype_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_recurringtype_seq`
--

LOCK TABLES `vtiger_recurringtype_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_recurringtype_seq` DISABLE KEYS */;
INSERT INTO `vtiger_recurringtype_seq` VALUES (5);
/*!40000 ALTER TABLE `vtiger_recurringtype_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_relatedlists`
--

DROP TABLE IF EXISTS `vtiger_relatedlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_relatedlists` (
  `relation_id` int(19) NOT NULL,
  `tabid` int(10) DEFAULT NULL,
  `related_tabid` int(10) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `sequence` int(10) DEFAULT NULL,
  `label` varchar(100) DEFAULT NULL,
  `presence` int(10) NOT NULL DEFAULT '0',
  `actions` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`relation_id`),
  KEY `relatedlists_tabid_idx` (`tabid`),
  KEY `relatedlists_related_tabid_idx` (`related_tabid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_relatedlists`
--

LOCK TABLES `vtiger_relatedlists` WRITE;
/*!40000 ALTER TABLE `vtiger_relatedlists` DISABLE KEYS */;
INSERT INTO `vtiger_relatedlists` VALUES (1,6,4,'get_contacts',1,'Contacts',0,'add'),(2,6,2,'get_opportunities',2,'Potentials',0,'add'),(3,6,20,'get_quotes',3,'Quotes',0,'add'),(4,6,22,'get_salesorder',4,'Sales Order',0,'add'),(5,6,23,'get_invoices',5,'Invoice',0,'add'),(6,6,64,'get_activities',6,'Activities',0,'add'),(7,6,10,'get_emails',7,'Emails',0,'add'),(9,6,8,'get_attachments',9,'Documents',0,'add,select'),(10,6,13,'get_tickets',10,'HelpDesk',0,'add'),(11,6,14,'get_products',11,'Products',0,'select'),(12,7,64,'get_activities',1,'Activities',0,'add'),(13,7,10,'get_emails',2,'Emails',0,'add'),(15,7,8,'get_attachments',4,'Documents',0,'add,select'),(16,7,14,'get_products',5,'Products',0,'select'),(17,7,26,'get_campaigns',6,'Campaigns',0,'select'),(18,4,2,'get_opportunities',1,'Potentials',0,'add'),(19,4,64,'get_activities',2,'Activities',0,'add'),(20,4,10,'get_emails',3,'Emails',0,'add'),(21,4,13,'get_tickets',4,'HelpDesk',0,'add'),(22,4,20,'get_quotes',5,'Quotes',0,'add'),(23,4,21,'get_purchase_orders',6,'Purchase Order',0,'add'),(24,4,22,'get_salesorder',7,'Sales Order',0,'add'),(25,4,14,'get_products',8,'Products',0,'select'),(27,4,8,'get_attachments',10,'Documents',0,'add,select'),(28,4,26,'get_campaigns',11,'Campaigns',0,'select'),(29,4,23,'get_invoices',12,'Invoice',0,'add'),(30,2,64,'get_activities',1,'Activities',0,'add'),(31,2,4,'get_contacts',2,'Contacts',0,'select'),(32,2,14,'get_products',3,'Products',0,'select'),(33,2,0,'get_stage_history',4,'Sales Stage History',0,''),(34,2,8,'get_attachments',5,'Documents',0,'add,select'),(35,2,20,'get_Quotes',6,'Quotes',0,'add'),(36,2,22,'get_salesorder',7,'Sales Order',0,'add'),(38,14,13,'get_tickets',1,'HelpDesk',0,'add'),(39,14,8,'get_attachments',3,'Documents',0,'add,select'),(40,14,20,'get_quotes',4,'Quotes',0,'add'),(41,14,21,'get_purchase_orders',5,'Purchase Order',0,'add'),(42,14,22,'get_salesorder',6,'Sales Order',0,'add'),(43,14,23,'get_invoices',7,'Invoice',0,'add'),(44,14,19,'get_product_pricebooks',8,'PriceBooks',0,'add'),(45,14,7,'get_leads',9,'Leads',0,'select'),(46,14,6,'get_accounts',10,'Accounts',0,'select'),(47,14,4,'get_contacts',11,'Contacts',0,'select'),(48,14,2,'get_opportunities',12,'Potentials',0,'select'),(49,14,14,'get_products',13,'Product Bundles',0,'add,select'),(50,14,14,'get_parent_products',14,'Parent Product',0,''),(51,10,4,'get_contacts',1,'Contacts',0,'select,bulkmail'),(52,10,0,'get_users',2,'Users',0,''),(53,10,8,'get_attachments',3,'Documents',0,'add,select'),(54,13,64,'get_activities',1,'Activities',0,'add,select'),(55,13,8,'get_attachments',2,'Documents',0,'add,select'),(56,13,0,'get_ticket_history',3,'Ticket History',0,''),(58,19,14,'get_pricebook_products',2,'Products',0,'select'),(59,18,14,'get_products',1,'Products',0,'add,select'),(60,18,21,'get_purchase_orders',2,'Purchase Order',0,'add'),(61,18,4,'get_contacts',3,'Contacts',0,'select'),(62,18,10,'get_emails',4,'Emails',0,'add'),(63,20,22,'get_salesorder',1,'Sales Order',0,''),(64,20,64,'get_activities',2,'Activities',0,'add'),(65,20,8,'get_attachments',3,'Documents',0,'add,select'),(67,20,0,'get_quotestagehistory',5,'Quote Stage History',0,''),(68,21,64,'get_activities',1,'Activities',0,'add'),(69,21,8,'get_attachments',2,'Documents',0,'add,select'),(71,21,0,'get_postatushistory',4,'PurchaseOrder Status History',0,''),(72,22,64,'get_activities',1,'Activities',0,'add'),(73,22,8,'get_attachments',2,'Documents',0,'add,select'),(74,22,23,'get_invoices',3,'Invoice',0,''),(76,22,0,'get_sostatushistory',5,'SalesOrder Status History',0,''),(77,23,64,'get_activities',1,'Activities',0,'add'),(78,23,8,'get_attachments',2,'Documents',0,'add,select'),(80,23,0,'get_invoicestatushistory',4,'Invoice Status History',0,''),(81,9,0,'get_users',1,'Users',0,''),(82,9,4,'get_contacts',2,'Contacts',0,''),(83,26,4,'get_contacts',1,'Contacts',0,'add,select'),(84,26,7,'get_leads',2,'Leads',0,'add,select'),(85,26,2,'get_dependents_list',3,'Potentials',0,'add'),(86,26,64,'get_activities',4,'Activities',0,'add'),(87,6,26,'get_campaigns',13,'Campaigns',0,'select'),(88,26,6,'get_accounts',5,'Accounts',0,'add,select'),(89,15,8,'get_attachments',1,'Documents',0,'add,select'),(90,37,13,'get_related_list',1,'Service Requests',0,'ADD,SELECT'),(91,37,8,'get_attachments',2,'Documents',0,'ADD,SELECT'),(92,6,37,'get_dependents_list',14,'Service Contracts',0,'ADD'),(93,4,37,'get_dependents_list',13,'Service Contracts',0,'ADD'),(94,13,37,'get_related_list',5,'Service Contracts',0,'ADD,SELECT'),(95,38,13,'get_related_list',1,'HelpDesk',0,'ADD,SELECT'),(96,38,20,'get_quotes',2,'Quotes',0,'ADD'),(97,38,21,'get_purchase_orders',3,'Purchase Order',0,'ADD'),(98,38,22,'get_salesorder',4,'Sales Order',0,'ADD'),(99,38,23,'get_invoices',5,'Invoice',0,'ADD'),(100,38,19,'get_service_pricebooks',6,'PriceBooks',0,'ADD'),(101,38,7,'get_related_list',7,'Leads',0,'SELECT'),(102,38,6,'get_related_list',8,'Accounts',0,'SELECT'),(103,38,4,'get_related_list',9,'Contacts',0,'SELECT'),(104,38,2,'get_related_list',10,'Potentials',0,'SELECT'),(105,38,8,'get_attachments',11,'Documents',0,'ADD,SELECT'),(106,13,38,'get_related_list',6,'Services',0,'SELECT'),(107,7,38,'get_related_list',7,'Services',0,'SELECT'),(108,6,38,'get_related_list',15,'Services',0,'SELECT'),(109,4,38,'get_related_list',14,'Services',0,'SELECT'),(110,2,38,'get_related_list',9,'Services',0,'SELECT'),(111,19,38,'get_pricebook_services',3,'Services',0,'SELECT'),(112,42,64,'get_activities',1,'Activities',0,'ADD,SELECT'),(114,42,8,'get_attachments',3,'Documents',0,'ADD,SELECT'),(115,42,42,'get_history_cobropago',4,'Historico de Cobros y Pagos',0,'ADD'),(116,6,42,'get_dependents_list',16,'CobroPago',0,'ADD'),(117,4,42,'get_dependents_list',15,'CobroPago',0,'ADD'),(118,18,42,'get_dependents_list',5,'CobroPago',0,'ADD'),(119,23,42,'get_dependents_list',5,'CobroPago',0,'ADD'),(120,22,42,'get_dependents_list',6,'CobroPago',0,'ADD'),(121,21,42,'get_dependents_list',5,'CobroPago',0,'ADD'),(122,20,42,'get_dependents_list',6,'CobroPago',0,'ADD'),(123,26,42,'get_dependents_list',6,'CobroPago',0,'ADD'),(124,2,42,'get_dependents_list',10,'CobroPago',0,'ADD'),(125,13,42,'get_dependents_list',7,'CobroPago',0,'ADD'),(126,43,13,'get_related_list',1,'HelpDesk',0,'ADD,SELECT'),(127,43,8,'get_attachments',2,'Documents',0,'ADD,SELECT'),(128,6,43,'get_dependents_list',17,'Assets',0,'ADD'),(129,14,43,'get_dependents_list',15,'Assets',0,'ADD'),(130,23,43,'get_dependents_list',6,'Assets',0,'ADD'),(131,49,8,'get_attachments',1,'Documents',0,'ADD,SELECT'),(132,50,49,'get_dependents_list',1,'Project Tasks',0,'ADD,SELECT'),(133,50,48,'get_dependents_list',2,'Project Milestones',0,'ADD,SELECT'),(134,50,8,'get_attachments',3,'Documents',0,'ADD,SELECT'),(135,50,13,'get_related_list',4,'Trouble Tickets',0,'ADD,SELECT'),(136,50,0,'get_gantt_chart',5,'Charts',0,''),(137,6,50,'get_dependents_list',18,'Projects',0,'ADD,SELECT'),(138,4,50,'get_dependents_list',16,'Projects',0,'ADD,SELECT'),(139,13,50,'get_related_list',8,'Projects',0,'SELECT'),(140,52,6,'get_related_list',1,'Accounts',0,' '),(141,52,4,'get_related_list',2,'Contacts',0,' '),(142,52,7,'get_related_list',3,'Leads',0,' '),(143,8,0,'getEntities',1,'Related To',0,'SELECT'),(144,18,38,'get_related_list',6,'Services',0,'SELECT'),(145,38,18,'get_related_list',12,'Vendors',0,'SELECT'),(146,50,10,'get_emails',6,'Emails',0,'ADD'),(147,49,10,'get_emails',2,'Emails',0,'ADD'),(148,2,10,'get_emails',11,'Emails',0,'ADD'),(149,13,10,'get_emails',9,'Emails',0,'ADD'),(150,16,8,'get_attachments',1,'Documents',0,'ADD,SELECT'),(151,55,8,'get_attachments',1,'Documents',0,'ADD,SELECT'),(152,18,64,'get_activities',7,'Activities',0,'ADD'),(153,18,9,'get_history',8,'Activities History',0,'ADD'),(154,4,18,'get_vendors',17,'Vendors',0,'SELECT'),(155,6,57,'get_dependents_list',19,'InventoryDetails',0,''),(156,4,57,'get_dependents_list',18,'InventoryDetails',0,''),(157,18,57,'get_dependents_list',9,'InventoryDetails',0,''),(158,23,57,'get_dependents_list',7,'InventoryDetails',0,''),(159,22,57,'get_dependents_list',7,'InventoryDetails',0,''),(160,21,57,'get_dependents_list',6,'InventoryDetails',0,''),(161,20,57,'get_dependents_list',7,'InventoryDetails',0,''),(162,14,57,'get_dependents_list',16,'InventoryDetails',0,''),(163,38,57,'get_dependents_list',13,'InventoryDetails',0,''),(164,58,56,'get_dependents_list',1,'GlobalVariable',0,'ADD'),(165,18,8,'get_attachments',10,'Documents',0,'ADD,SELECT'),(166,62,23,'get_dependents_list',1,'Invoice',0,'ADD'),(167,62,22,'get_dependents_list',2,'SalesOrder',0,'ADD'),(168,62,20,'get_dependents_list',3,'Quotes',0,'ADD'),(169,62,21,'get_dependents_list',4,'PurchaseOrder',0,'ADD'),(170,63,4,'get_contacts',1,'Contacts',0,'ADD,SELECT'),(171,63,63,'get_dependents_list',2,'cbCalendar',0,'ADD'),(172,63,8,'get_attachments',3,'Documents',0,'ADD,SELECT');
/*!40000 ALTER TABLE `vtiger_relatedlists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_relatedlists_rb`
--

DROP TABLE IF EXISTS `vtiger_relatedlists_rb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_relatedlists_rb` (
  `entityid` int(19) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `rel_table` varchar(200) DEFAULT NULL,
  `rel_column` varchar(200) DEFAULT NULL,
  `ref_column` varchar(200) DEFAULT NULL,
  `related_crm_ids` text,
  KEY `relatedlists_rb_entityid_idx` (`entityid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_relatedlists_rb`
--

LOCK TABLES `vtiger_relatedlists_rb` WRITE;
/*!40000 ALTER TABLE `vtiger_relatedlists_rb` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_relatedlists_rb` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_relatedlists_seq`
--

DROP TABLE IF EXISTS `vtiger_relatedlists_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_relatedlists_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_relatedlists_seq`
--

LOCK TABLES `vtiger_relatedlists_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_relatedlists_seq` DISABLE KEYS */;
INSERT INTO `vtiger_relatedlists_seq` VALUES (172);
/*!40000 ALTER TABLE `vtiger_relatedlists_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_relcriteria`
--

DROP TABLE IF EXISTS `vtiger_relcriteria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_relcriteria` (
  `queryid` int(19) NOT NULL,
  `columnindex` int(11) NOT NULL,
  `columnname` varchar(250) DEFAULT '',
  `comparator` varchar(20) DEFAULT NULL,
  `value` varchar(512) DEFAULT NULL,
  `groupid` int(11) DEFAULT '1',
  `column_condition` varchar(256) DEFAULT 'and',
  PRIMARY KEY (`queryid`,`columnindex`),
  KEY `relcriteria_queryid_idx` (`queryid`),
  CONSTRAINT `fk_1_vtiger_relcriteria` FOREIGN KEY (`queryid`) REFERENCES `vtiger_selectquery` (`queryid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_relcriteria`
--

LOCK TABLES `vtiger_relcriteria` WRITE;
/*!40000 ALTER TABLE `vtiger_relcriteria` DISABLE KEYS */;
INSERT INTO `vtiger_relcriteria` VALUES (1,0,'vtiger_contactdetails:accountid:Contacts_Account_Name:account_id:V','n','',1,'and'),(2,0,'vtiger_contactdetails:accountid:Contacts_Account_Name:account_id:V','e','',1,'and'),(3,0,'vtiger_potential:potentialname:Potentials_Potential_Name:potentialname:V','n','',1,'and'),(7,0,'vtiger_potential:sales_stage:Potentials_Sales_Stage:sales_stage:V','e','Closed Won',1,'and'),(12,0,'vtiger_troubletickets:status:HelpDesk_Status:ticketstatus:V','n','Closed',1,'and'),(15,0,'vtiger_quotes:quotestage:Quotes_Quote_Stage:quotestage:V','n','Accepted',1,'and'),(15,1,'vtiger_quotes:quotestage:Quotes_Quote_Stage:quotestage:V','n','Rejected',1,'and'),(22,0,'vtiger_email_track:access_count:Emails_Access_Count:access_count:I','n','',1,'and'),(23,0,'vtiger_email_track:access_count:Emails_Access_Count:access_count:I','n','',1,'and'),(24,0,'vtiger_email_track:access_count:Emails_Access_Count:access_count:I','n','',1,'and'),(25,0,'vtiger_email_track:access_count:Emails_Access_Count:access_count:I','n','',1,'and');
/*!40000 ALTER TABLE `vtiger_relcriteria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_relcriteria_grouping`
--

DROP TABLE IF EXISTS `vtiger_relcriteria_grouping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_relcriteria_grouping` (
  `groupid` int(11) NOT NULL,
  `queryid` int(19) NOT NULL,
  `group_condition` varchar(256) DEFAULT NULL,
  `condition_expression` text,
  PRIMARY KEY (`groupid`,`queryid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_relcriteria_grouping`
--

LOCK TABLES `vtiger_relcriteria_grouping` WRITE;
/*!40000 ALTER TABLE `vtiger_relcriteria_grouping` DISABLE KEYS */;
INSERT INTO `vtiger_relcriteria_grouping` VALUES (1,1,'','0'),(1,2,'','0'),(1,3,'','0'),(1,7,'','0'),(1,12,'','0'),(1,15,'','0 and 1'),(1,22,'','0'),(1,23,'','0'),(1,24,'','0'),(1,25,'','0');
/*!40000 ALTER TABLE `vtiger_relcriteria_grouping` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_reminder_interval`
--

DROP TABLE IF EXISTS `vtiger_reminder_interval`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_reminder_interval` (
  `reminder_intervalid` int(19) NOT NULL AUTO_INCREMENT,
  `reminder_interval` varchar(200) NOT NULL,
  `sortorderid` int(19) NOT NULL,
  `presence` int(1) NOT NULL,
  PRIMARY KEY (`reminder_intervalid`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_reminder_interval`
--

LOCK TABLES `vtiger_reminder_interval` WRITE;
/*!40000 ALTER TABLE `vtiger_reminder_interval` DISABLE KEYS */;
INSERT INTO `vtiger_reminder_interval` VALUES (1,'None',0,1),(2,'1 Minute',1,1),(3,'5 Minutes',2,1),(4,'15 Minutes',3,1),(5,'30 Minutes',4,1),(6,'45 Minutes',5,1),(7,'1 Hour',6,1),(8,'1 Day',7,1);
/*!40000 ALTER TABLE `vtiger_reminder_interval` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_reminder_interval_seq`
--

DROP TABLE IF EXISTS `vtiger_reminder_interval_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_reminder_interval_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_reminder_interval_seq`
--

LOCK TABLES `vtiger_reminder_interval_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_reminder_interval_seq` DISABLE KEYS */;
INSERT INTO `vtiger_reminder_interval_seq` VALUES (8);
/*!40000 ALTER TABLE `vtiger_reminder_interval_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_report`
--

DROP TABLE IF EXISTS `vtiger_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_report` (
  `reportid` int(19) NOT NULL,
  `folderid` int(19) NOT NULL,
  `reportname` varchar(100) DEFAULT '',
  `description` varchar(250) DEFAULT '',
  `reporttype` varchar(50) DEFAULT '',
  `queryid` int(19) NOT NULL DEFAULT '0',
  `state` varchar(50) DEFAULT 'SAVED',
  `customizable` int(1) DEFAULT '1',
  `category` int(11) DEFAULT '1',
  `owner` int(11) DEFAULT '1',
  `sharingtype` varchar(200) DEFAULT 'Private',
  `moreinfo` text,
  PRIMARY KEY (`reportid`),
  KEY `report_queryid_idx` (`queryid`),
  KEY `report_folderid_idx` (`folderid`),
  KEY `report_owner_idx` (`owner`),
  KEY `report_sharingtype_idx` (`sharingtype`),
  CONSTRAINT `fk_2_vtiger_report` FOREIGN KEY (`queryid`) REFERENCES `vtiger_selectquery` (`queryid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_report`
--

LOCK TABLES `vtiger_report` WRITE;
/*!40000 ALTER TABLE `vtiger_report` DISABLE KEYS */;
INSERT INTO `vtiger_report` VALUES (1,1,'Contacts by Accounts','Contacts related to Accounts','tabular',1,'SAVED',1,1,1,'Public',NULL),(2,1,'Contacts without Accounts','Contacts not related to Accounts','tabular',2,'SAVED',1,1,1,'Public',NULL),(3,1,'Contacts by Potentials','Contacts related to Potentials','tabular',3,'SAVED',1,1,1,'Public',NULL),(4,2,'Lead by Source','Lead by Source','summary',4,'SAVED',1,1,1,'Public',NULL),(5,2,'Lead Status Report','Lead Status Report','summary',5,'SAVED',1,1,1,'Public',NULL),(6,3,'Potential Pipeline','Potential Pipeline','summary',6,'SAVED',1,1,1,'Public',NULL),(7,3,'Closed Potentials','Potential that have Won','tabular',7,'SAVED',1,1,1,'Public',NULL),(8,4,'Last Month Activities','Last Month Activities','tabular',8,'SAVED',1,1,1,'Public',NULL),(9,4,'This Month Activities','This Month Activities','tabular',9,'SAVED',1,1,1,'Public',NULL),(10,5,'Tickets by Products','Tickets related to Products','tabular',10,'SAVED',1,1,1,'Public',NULL),(11,5,'Tickets by Priority','Tickets by Priority','summary',11,'SAVED',1,1,1,'Public',NULL),(12,5,'Open Tickets','Tickets that are Open','tabular',12,'SAVED',1,1,1,'Public',NULL),(13,6,'Product Details','Product Detailed Report','tabular',13,'SAVED',1,1,1,'Public',NULL),(14,6,'Products by Contacts','Products related to Contacts','tabular',14,'SAVED',1,1,1,'Public',NULL),(15,7,'Open Quotes','Quotes that are Open','tabular',15,'SAVED',1,1,1,'Public',NULL),(16,7,'Quotes Detailed Report','Quotes Detailed Report','tabular',16,'SAVED',1,1,1,'Public',NULL),(17,8,'PurchaseOrder by Contacts','PurchaseOrder related to Contacts','tabular',17,'SAVED',1,1,1,'Public',NULL),(18,8,'PurchaseOrder Detailed Report','PurchaseOrder Detailed Report','tabular',18,'SAVED',1,1,1,'Public',NULL),(19,9,'Invoice Detailed Report','Invoice Detailed Report','tabular',19,'SAVED',1,1,1,'Public',NULL),(20,10,'SalesOrder Detailed Report','SalesOrder Detailed Report','tabular',20,'SAVED',1,1,1,'Public',NULL),(21,11,'Campaign Expectations and Actuals','Campaign Expectations and Actuals','tabular',21,'SAVED',1,1,1,'Public',NULL),(22,12,'Contacts Email Report','Emails sent to Contacts','tabular',22,'SAVED',1,1,1,'Public',NULL),(23,12,'Accounts Email Report','Emails sent to Organizations','tabular',23,'SAVED',1,1,1,'Public',NULL),(24,12,'Leads Email Report','Emails sent to Leads','tabular',24,'SAVED',1,1,1,'Public',NULL),(25,12,'Vendors Email Report','Emails sent to Vendors','tabular',25,'SAVED',1,1,1,'Public',NULL);
/*!40000 ALTER TABLE `vtiger_report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_reportdatefilter`
--

DROP TABLE IF EXISTS `vtiger_reportdatefilter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_reportdatefilter` (
  `datefilterid` int(19) NOT NULL,
  `datecolumnname` varchar(250) DEFAULT '',
  `datefilter` varchar(250) DEFAULT '',
  `startdate` date DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  PRIMARY KEY (`datefilterid`),
  KEY `reportdatefilter_datefilterid_idx` (`datefilterid`),
  CONSTRAINT `fk_1_vtiger_reportdatefilter` FOREIGN KEY (`datefilterid`) REFERENCES `vtiger_report` (`reportid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_reportdatefilter`
--

LOCK TABLES `vtiger_reportdatefilter` WRITE;
/*!40000 ALTER TABLE `vtiger_reportdatefilter` DISABLE KEYS */;
INSERT INTO `vtiger_reportdatefilter` VALUES (8,'vtiger_crmentity:modifiedtime:modifiedtime:Calendar_Modified_Time','lastmonth','2005-05-01','2005-05-31'),(9,'vtiger_crmentity:modifiedtime:modifiedtime:Calendar_Modified_Time','thismonth','2005-06-01','2005-06-30');
/*!40000 ALTER TABLE `vtiger_reportdatefilter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_reportfilters`
--

DROP TABLE IF EXISTS `vtiger_reportfilters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_reportfilters` (
  `filterid` int(19) NOT NULL,
  `name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_reportfilters`
--

LOCK TABLES `vtiger_reportfilters` WRITE;
/*!40000 ALTER TABLE `vtiger_reportfilters` DISABLE KEYS */;
INSERT INTO `vtiger_reportfilters` VALUES (1,'Private'),(2,'Public'),(3,'Shared');
/*!40000 ALTER TABLE `vtiger_reportfilters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_reportfolder`
--

DROP TABLE IF EXISTS `vtiger_reportfolder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_reportfolder` (
  `folderid` int(19) NOT NULL AUTO_INCREMENT,
  `foldername` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(250) DEFAULT '',
  `state` varchar(50) DEFAULT 'SAVED',
  PRIMARY KEY (`folderid`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_reportfolder`
--

LOCK TABLES `vtiger_reportfolder` WRITE;
/*!40000 ALTER TABLE `vtiger_reportfolder` DISABLE KEYS */;
INSERT INTO `vtiger_reportfolder` VALUES (1,'Account and Contact Reports','Account and Contact Reports','SAVED'),(2,'Lead Reports','Lead Reports','SAVED'),(3,'Potential Reports','Potential Reports','SAVED'),(4,'Activity Reports','Activity Reports','SAVED'),(5,'HelpDesk Reports','HelpDesk Reports','SAVED'),(6,'Product Reports','Product Reports','SAVED'),(7,'Quote Reports','Quote Reports','SAVED'),(8,'PurchaseOrder Reports','PurchaseOrder Reports','SAVED'),(9,'Invoice Reports','Invoice Reports','SAVED'),(10,'SalesOrder Reports','SalesOrder Reports','SAVED'),(11,'Campaign Reports','Campaign Reports','SAVED'),(12,'Email Reports','Email Reports','SAVED');
/*!40000 ALTER TABLE `vtiger_reportfolder` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_reportgroupbycolumn`
--

DROP TABLE IF EXISTS `vtiger_reportgroupbycolumn`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_reportgroupbycolumn` (
  `reportid` int(19) DEFAULT NULL,
  `sortid` int(19) DEFAULT NULL,
  `sortcolname` varchar(250) DEFAULT NULL,
  `dategroupbycriteria` varchar(250) DEFAULT NULL,
  KEY `fk_1_vtiger_reportgroupbycolumn` (`reportid`),
  CONSTRAINT `fk_1_vtiger_reportgroupbycolumn` FOREIGN KEY (`reportid`) REFERENCES `vtiger_report` (`reportid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_reportgroupbycolumn`
--

LOCK TABLES `vtiger_reportgroupbycolumn` WRITE;
/*!40000 ALTER TABLE `vtiger_reportgroupbycolumn` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_reportgroupbycolumn` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_reportmodules`
--

DROP TABLE IF EXISTS `vtiger_reportmodules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_reportmodules` (
  `reportmodulesid` int(19) NOT NULL,
  `primarymodule` varchar(50) NOT NULL DEFAULT '',
  `secondarymodules` varchar(250) DEFAULT '',
  PRIMARY KEY (`reportmodulesid`),
  CONSTRAINT `fk_1_vtiger_reportmodules` FOREIGN KEY (`reportmodulesid`) REFERENCES `vtiger_report` (`reportid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_reportmodules`
--

LOCK TABLES `vtiger_reportmodules` WRITE;
/*!40000 ALTER TABLE `vtiger_reportmodules` DISABLE KEYS */;
INSERT INTO `vtiger_reportmodules` VALUES (1,'Contacts','Accounts'),(2,'Contacts','Accounts'),(3,'Contacts','Potentials'),(4,'Leads',''),(5,'Leads',''),(6,'Potentials',''),(7,'Potentials',''),(8,'Calendar',''),(9,'Calendar',''),(10,'HelpDesk','Products'),(11,'HelpDesk',''),(12,'HelpDesk',''),(13,'Products',''),(14,'Products','Contacts'),(15,'Quotes',''),(16,'Quotes',''),(17,'PurchaseOrder','Contacts'),(18,'PurchaseOrder',''),(19,'Invoice',''),(20,'SalesOrder',''),(21,'Campaigns',''),(22,'Contacts','Emails'),(23,'Accounts','Emails'),(24,'Leads','Emails'),(25,'Vendors','Emails');
/*!40000 ALTER TABLE `vtiger_reportmodules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_reportsharing`
--

DROP TABLE IF EXISTS `vtiger_reportsharing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_reportsharing` (
  `reportid` int(19) NOT NULL,
  `shareid` int(19) NOT NULL,
  `setype` varchar(200) NOT NULL,
  KEY `reportsharing_reportid_idx` (`reportid`),
  KEY `reportsharing_shareid_idx` (`shareid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_reportsharing`
--

LOCK TABLES `vtiger_reportsharing` WRITE;
/*!40000 ALTER TABLE `vtiger_reportsharing` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_reportsharing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_reportsortcol`
--

DROP TABLE IF EXISTS `vtiger_reportsortcol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_reportsortcol` (
  `sortcolid` int(19) NOT NULL,
  `reportid` int(19) NOT NULL,
  `columnname` varchar(250) DEFAULT '',
  `sortorder` varchar(250) DEFAULT 'Asc',
  PRIMARY KEY (`sortcolid`,`reportid`),
  KEY `fk_1_vtiger_reportsortcol` (`reportid`),
  CONSTRAINT `fk_1_vtiger_reportsortcol` FOREIGN KEY (`reportid`) REFERENCES `vtiger_report` (`reportid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_reportsortcol`
--

LOCK TABLES `vtiger_reportsortcol` WRITE;
/*!40000 ALTER TABLE `vtiger_reportsortcol` DISABLE KEYS */;
INSERT INTO `vtiger_reportsortcol` VALUES (1,4,'vtiger_leaddetails:leadsource:Leads_Lead_Source:leadsource:V','Ascending'),(1,5,'vtiger_leaddetails:leadstatus:Leads_Lead_Status:leadstatus:V','Ascending'),(1,6,'vtiger_potential:sales_stage:Potentials_Sales_Stage:sales_stage:V','Ascending'),(1,11,'vtiger_troubletickets:priority:HelpDesk_Priority:ticketpriorities:V','Ascending');
/*!40000 ALTER TABLE `vtiger_reportsortcol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_reportsummary`
--

DROP TABLE IF EXISTS `vtiger_reportsummary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_reportsummary` (
  `reportsummaryid` int(19) NOT NULL,
  `summarytype` int(19) NOT NULL,
  `columnname` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`reportsummaryid`,`summarytype`,`columnname`),
  KEY `reportsummary_reportsummaryid_idx` (`reportsummaryid`),
  CONSTRAINT `fk_1_vtiger_reportsummary` FOREIGN KEY (`reportsummaryid`) REFERENCES `vtiger_report` (`reportid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_reportsummary`
--

LOCK TABLES `vtiger_reportsummary` WRITE;
/*!40000 ALTER TABLE `vtiger_reportsummary` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_reportsummary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_role`
--

DROP TABLE IF EXISTS `vtiger_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_role` (
  `roleid` varchar(255) NOT NULL,
  `rolename` varchar(200) DEFAULT NULL,
  `parentrole` varchar(255) DEFAULT NULL,
  `depth` int(19) DEFAULT NULL,
  PRIMARY KEY (`roleid`),
  KEY `parent` (`parentrole`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_role`
--

LOCK TABLES `vtiger_role` WRITE;
/*!40000 ALTER TABLE `vtiger_role` DISABLE KEYS */;
INSERT INTO `vtiger_role` VALUES ('H1','Organisation','H1',0),('H2','CEO','H1::H2',1),('H3','Vice President','H1::H2::H3',2),('H4','Sales Manager','H1::H2::H3::H4',3),('H5','Sales Man','H1::H2::H3::H4::H5',4);
/*!40000 ALTER TABLE `vtiger_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_role2picklist`
--

DROP TABLE IF EXISTS `vtiger_role2picklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_role2picklist` (
  `roleid` varchar(255) NOT NULL,
  `picklistvalueid` int(11) NOT NULL,
  `picklistid` int(11) NOT NULL,
  `sortid` int(11) DEFAULT NULL,
  PRIMARY KEY (`roleid`,`picklistvalueid`,`picklistid`),
  KEY `fk_2_vtiger_role2picklist` (`picklistid`),
  KEY `role2picklist_roleid_picklistid_idx` (`roleid`,`picklistid`,`picklistvalueid`,`sortid`),
  KEY `picklistvalueid` (`picklistvalueid`),
  CONSTRAINT `fk_1_vtiger_role2picklist` FOREIGN KEY (`roleid`) REFERENCES `vtiger_role` (`roleid`) ON DELETE CASCADE,
  CONSTRAINT `fk_2_vtiger_role2picklist` FOREIGN KEY (`picklistid`) REFERENCES `vtiger_picklist` (`picklistid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_role2picklist`
--

LOCK TABLES `vtiger_role2picklist` WRITE;
/*!40000 ALTER TABLE `vtiger_role2picklist` DISABLE KEYS */;
INSERT INTO `vtiger_role2picklist` VALUES ('H1',1,1,0),('H1',2,1,1),('H1',3,1,2),('H1',4,1,3),('H1',5,1,4),('H1',6,1,5),('H1',7,1,6),('H1',8,1,7),('H1',9,1,8),('H1',10,1,9),('H1',11,1,10),('H1',12,2,0),('H1',13,2,1),('H1',534,2,1),('H1',535,2,2),('H1',14,3,0),('H1',15,3,1),('H1',16,3,2),('H1',17,3,3),('H1',18,3,4),('H1',19,3,5),('H1',20,4,0),('H1',21,4,1),('H1',22,4,2),('H1',23,4,3),('H1',24,4,4),('H1',25,4,5),('H1',26,4,6),('H1',27,4,7),('H1',28,4,8),('H1',29,4,9),('H1',30,4,10),('H1',31,4,11),('H1',32,4,12),('H1',33,5,0),('H1',34,5,1),('H1',35,5,2),('H1',36,5,3),('H1',37,5,4),('H1',38,6,0),('H1',39,6,1),('H1',40,6,2),('H1',529,6,1),('H1',530,6,2),('H1',531,6,3),('H1',532,6,4),('H1',533,6,5),('H1',41,7,0),('H1',42,7,1),('H1',43,7,2),('H1',44,7,3),('H1',45,7,4),('H1',46,8,0),('H1',47,9,0),('H1',48,9,1),('H1',49,9,2),('H1',50,9,3),('H1',51,10,0),('H1',52,10,1),('H1',53,10,2),('H1',54,10,3),('H1',55,10,4),('H1',56,10,5),('H1',57,10,6),('H1',58,10,7),('H1',59,10,8),('H1',60,11,0),('H1',61,11,1),('H1',62,11,2),('H1',63,11,3),('H1',64,11,4),('H1',65,11,5),('H1',66,11,6),('H1',67,11,7),('H1',68,11,8),('H1',69,11,9),('H1',70,11,10),('H1',71,11,11),('H1',72,11,12),('H1',73,11,13),('H1',74,11,14),('H1',75,11,15),('H1',76,11,16),('H1',77,11,17),('H1',78,11,18),('H1',79,11,19),('H1',80,11,20),('H1',81,11,21),('H1',82,11,22),('H1',83,11,23),('H1',84,11,24),('H1',85,11,25),('H1',86,11,26),('H1',87,11,27),('H1',88,11,28),('H1',89,11,29),('H1',90,11,30),('H1',91,11,31),('H1',92,12,0),('H1',93,12,1),('H1',94,12,2),('H1',95,12,3),('H1',96,12,4),('H1',97,12,5),('H1',367,12,1),('H1',98,13,0),('H1',99,13,1),('H1',100,13,2),('H1',101,13,3),('H1',102,13,4),('H1',103,13,5),('H1',104,13,6),('H1',105,13,7),('H1',106,13,8),('H1',107,13,9),('H1',108,13,10),('H1',109,13,11),('H1',110,13,12),('H1',111,14,0),('H1',112,14,1),('H1',113,14,2),('H1',114,14,3),('H1',115,14,4),('H1',116,14,5),('H1',117,14,6),('H1',118,14,7),('H1',119,14,8),('H1',120,14,9),('H1',121,14,10),('H1',122,14,11),('H1',123,15,0),('H1',124,15,1),('H1',125,15,2),('H1',126,15,3),('H1',127,16,0),('H1',128,16,1),('H1',129,16,2),('H1',130,17,0),('H1',131,17,1),('H1',132,17,2),('H1',133,17,3),('H1',134,17,4),('H1',135,18,0),('H1',136,18,1),('H1',137,18,2),('H1',138,18,3),('H1',139,19,0),('H1',140,19,1),('H1',141,19,2),('H1',142,19,3),('H1',143,19,4),('H1',144,20,0),('H1',145,20,1),('H1',146,20,2),('H1',147,20,3),('H1',148,20,4),('H1',149,20,5),('H1',150,21,0),('H1',151,21,1),('H1',152,21,2),('H1',153,21,3),('H1',154,21,4),('H1',155,21,5),('H1',156,21,6),('H1',157,21,7),('H1',158,21,8),('H1',159,21,9),('H1',160,22,0),('H1',161,22,1),('H1',162,22,2),('H1',163,22,3),('H1',164,22,4),('H1',165,22,5),('H1',166,23,0),('H1',167,23,1),('H1',168,23,2),('H1',169,23,3),('H1',170,24,0),('H1',171,24,1),('H1',172,24,2),('H1',173,25,0),('H1',174,25,1),('H1',175,25,2),('H1',176,25,3),('H1',177,25,4),('H1',178,25,5),('H1',179,26,0),('H1',180,26,1),('H1',181,26,2),('H1',182,27,0),('H1',183,27,1),('H1',184,27,2),('H1',185,27,3),('H1',186,28,0),('H1',187,28,1),('H1',188,28,2),('H1',189,28,3),('H1',190,29,0),('H1',191,29,1),('H1',192,29,2),('H1',193,29,3),('H1',194,30,0),('H1',195,30,1),('H1',196,30,2),('H1',197,30,3),('H1',198,30,4),('H1',199,30,5),('H1',200,30,6),('H1',201,30,7),('H1',202,30,8),('H1',203,30,9),('H1',204,30,10),('H1',205,30,11),('H1',206,30,12),('H1',207,30,13),('H1',208,30,14),('H1',209,30,15),('H1',210,31,1),('H1',211,31,2),('H1',212,31,3),('H1',213,31,4),('H1',214,32,1),('H1',215,32,2),('H1',216,32,3),('H1',217,32,4),('H1',218,32,5),('H1',219,32,6),('H1',220,33,1),('H1',221,33,2),('H1',222,33,3),('H1',223,34,1),('H1',224,34,2),('H1',225,34,3),('H1',226,35,1),('H1',227,35,2),('H1',228,35,3),('H1',229,36,1),('H1',230,36,2),('H1',231,36,3),('H1',232,36,4),('H1',233,36,5),('H1',234,36,6),('H1',235,37,1),('H1',236,37,2),('H1',237,37,3),('H1',238,37,4),('H1',239,38,1),('H1',240,38,2),('H1',241,38,3),('H1',242,39,1),('H1',243,39,2),('H1',244,39,3),('H1',245,40,1),('H1',246,40,2),('H1',247,41,1),('H1',248,41,2),('H1',249,41,3),('H1',250,41,4),('H1',251,42,1),('H1',252,42,2),('H1',253,42,3),('H1',254,42,4),('H1',255,43,1),('H1',256,43,2),('H1',257,43,3),('H1',258,43,4),('H1',259,44,1),('H1',260,44,2),('H1',261,44,3),('H1',262,44,4),('H1',263,44,5),('H1',264,44,6),('H1',265,44,7),('H1',266,44,8),('H1',267,44,9),('H1',268,44,10),('H1',269,44,11),('H1',270,45,1),('H1',271,45,2),('H1',272,45,3),('H1',273,45,4),('H1',274,45,5),('H1',275,45,6),('H1',276,45,7),('H1',277,45,8),('H1',278,45,9),('H1',279,46,1),('H1',280,46,2),('H1',281,46,3),('H1',282,46,4),('H1',283,47,1),('H1',284,47,2),('H1',285,47,3),('H1',286,47,4),('H1',287,48,1),('H1',288,48,2),('H1',289,48,3),('H1',290,48,4),('H1',291,48,5),('H1',292,48,6),('H1',293,48,7),('H1',294,48,8),('H1',295,48,9),('H1',296,48,10),('H1',297,48,11),('H1',298,49,1),('H1',327,49,30),('H1',334,49,1),('H1',335,49,1),('H1',336,49,2),('H1',337,49,3),('H1',338,49,4),('H1',339,49,5),('H1',340,49,6),('H1',341,49,7),('H1',342,49,8),('H1',343,49,9),('H1',344,49,10),('H1',345,49,11),('H1',346,49,12),('H1',347,49,13),('H1',348,49,14),('H1',349,49,15),('H1',350,49,16),('H1',351,49,17),('H1',352,49,18),('H1',353,49,19),('H1',354,49,20),('H1',355,49,21),('H1',356,49,22),('H1',357,49,23),('H1',358,49,24),('H1',359,49,25),('H1',360,49,26),('H1',361,49,27),('H1',362,49,28),('H1',363,49,29),('H1',364,49,30),('H1',365,49,31),('H1',366,49,32),('H1',399,49,1),('H1',400,49,2),('H1',401,49,3),('H1',402,49,4),('H1',403,49,5),('H1',404,49,6),('H1',405,49,7),('H1',406,49,8),('H1',407,49,9),('H1',408,49,10),('H1',409,49,11),('H1',410,49,12),('H1',411,49,13),('H1',412,49,14),('H1',413,49,15),('H1',414,49,16),('H1',416,49,1),('H1',417,49,2),('H1',418,49,3),('H1',419,49,4),('H1',420,49,5),('H1',421,49,6),('H1',422,49,7),('H1',423,49,8),('H1',424,49,9),('H1',425,49,10),('H1',426,49,11),('H1',427,49,12),('H1',428,49,13),('H1',429,49,14),('H1',430,49,15),('H1',431,49,16),('H1',432,49,17),('H1',433,49,18),('H1',434,49,19),('H1',435,49,20),('H1',436,49,21),('H1',437,49,22),('H1',438,49,23),('H1',439,49,24),('H1',440,49,25),('H1',441,49,26),('H1',442,49,27),('H1',443,49,28),('H1',444,49,29),('H1',445,49,30),('H1',446,49,31),('H1',447,49,32),('H1',449,49,34),('H1',450,49,35),('H1',451,49,36),('H1',452,49,37),('H1',453,49,38),('H1',454,49,39),('H1',455,49,40),('H1',456,49,41),('H1',457,49,42),('H1',458,49,43),('H1',459,49,44),('H1',475,49,1),('H1',476,49,2),('H1',477,49,3),('H1',478,49,4),('H1',479,49,5),('H1',480,49,6),('H1',481,49,7),('H1',482,49,8),('H1',483,49,9),('H1',484,49,10),('H1',485,49,11),('H1',490,49,1),('H1',491,49,1),('H1',492,49,2),('H1',493,49,3),('H1',494,49,4),('H1',495,49,5),('H1',496,49,6),('H1',497,49,7),('H1',498,49,8),('H1',499,49,9),('H1',500,49,10),('H1',501,49,11),('H1',502,49,12),('H1',503,49,13),('H1',504,49,14),('H1',505,49,15),('H1',506,49,16),('H1',507,49,17),('H1',508,49,18),('H1',509,49,19),('H1',510,49,20),('H1',511,49,21),('H1',512,49,22),('H1',513,49,23),('H1',514,49,24),('H1',515,49,25),('H1',516,49,26),('H1',517,49,27),('H1',518,49,28),('H1',519,49,29),('H1',520,49,30),('H1',521,49,31),('H1',522,49,32),('H1',523,49,33),('H1',524,49,34),('H1',525,49,35),('H1',526,49,36),('H1',329,50,1),('H1',330,50,2),('H1',331,50,3),('H1',332,50,4),('H1',333,50,5),('H1',394,53,1),('H1',395,53,2),('H1',396,53,3),('H1',397,53,1),('H1',398,53,2),('H1',415,53,1),('H1',461,53,1),('H1',462,53,2),('H1',463,53,3),('H1',464,53,4),('H1',465,53,5),('H1',466,53,6),('H1',467,53,7),('H1',468,53,8),('H1',527,53,1),('H1',528,53,2),('H1',469,54,1),('H1',470,54,2),('H1',471,54,3),('H1',472,54,4),('H1',473,54,5),('H1',474,54,6),('H1',486,55,1),('H1',487,55,2),('H1',488,55,3),('H1',489,55,4),('H1',536,56,1),('H1',537,56,2),('H1',538,56,3),('H1',539,56,4),('H2',1,1,0),('H2',2,1,1),('H2',3,1,2),('H2',4,1,3),('H2',5,1,4),('H2',6,1,5),('H2',7,1,6),('H2',8,1,7),('H2',9,1,8),('H2',10,1,9),('H2',11,1,10),('H2',12,2,0),('H2',13,2,1),('H2',534,2,1),('H2',535,2,2),('H2',14,3,0),('H2',15,3,1),('H2',16,3,2),('H2',17,3,3),('H2',18,3,4),('H2',19,3,5),('H2',20,4,0),('H2',21,4,1),('H2',22,4,2),('H2',23,4,3),('H2',24,4,4),('H2',25,4,5),('H2',26,4,6),('H2',27,4,7),('H2',28,4,8),('H2',29,4,9),('H2',30,4,10),('H2',31,4,11),('H2',32,4,12),('H2',33,5,0),('H2',34,5,1),('H2',35,5,2),('H2',36,5,3),('H2',37,5,4),('H2',38,6,0),('H2',39,6,1),('H2',40,6,2),('H2',529,6,1),('H2',530,6,2),('H2',531,6,3),('H2',532,6,4),('H2',533,6,5),('H2',41,7,0),('H2',42,7,1),('H2',43,7,2),('H2',44,7,3),('H2',45,7,4),('H2',46,8,0),('H2',47,9,0),('H2',48,9,1),('H2',49,9,2),('H2',50,9,3),('H2',51,10,0),('H2',52,10,1),('H2',53,10,2),('H2',54,10,3),('H2',55,10,4),('H2',56,10,5),('H2',57,10,6),('H2',58,10,7),('H2',59,10,8),('H2',60,11,0),('H2',61,11,1),('H2',62,11,2),('H2',63,11,3),('H2',64,11,4),('H2',65,11,5),('H2',66,11,6),('H2',67,11,7),('H2',68,11,8),('H2',69,11,9),('H2',70,11,10),('H2',71,11,11),('H2',72,11,12),('H2',73,11,13),('H2',74,11,14),('H2',75,11,15),('H2',76,11,16),('H2',77,11,17),('H2',78,11,18),('H2',79,11,19),('H2',80,11,20),('H2',81,11,21),('H2',82,11,22),('H2',83,11,23),('H2',84,11,24),('H2',85,11,25),('H2',86,11,26),('H2',87,11,27),('H2',88,11,28),('H2',89,11,29),('H2',90,11,30),('H2',91,11,31),('H2',92,12,0),('H2',93,12,1),('H2',94,12,2),('H2',95,12,3),('H2',96,12,4),('H2',97,12,5),('H2',367,12,1),('H2',98,13,0),('H2',99,13,1),('H2',100,13,2),('H2',101,13,3),('H2',102,13,4),('H2',103,13,5),('H2',104,13,6),('H2',105,13,7),('H2',106,13,8),('H2',107,13,9),('H2',108,13,10),('H2',109,13,11),('H2',110,13,12),('H2',111,14,0),('H2',112,14,1),('H2',113,14,2),('H2',114,14,3),('H2',115,14,4),('H2',116,14,5),('H2',117,14,6),('H2',118,14,7),('H2',119,14,8),('H2',120,14,9),('H2',121,14,10),('H2',122,14,11),('H2',123,15,0),('H2',124,15,1),('H2',125,15,2),('H2',126,15,3),('H2',127,16,0),('H2',128,16,1),('H2',129,16,2),('H2',130,17,0),('H2',131,17,1),('H2',132,17,2),('H2',133,17,3),('H2',134,17,4),('H2',135,18,0),('H2',136,18,1),('H2',137,18,2),('H2',138,18,3),('H2',139,19,0),('H2',140,19,1),('H2',141,19,2),('H2',142,19,3),('H2',143,19,4),('H2',144,20,0),('H2',145,20,1),('H2',146,20,2),('H2',147,20,3),('H2',148,20,4),('H2',149,20,5),('H2',150,21,0),('H2',151,21,1),('H2',152,21,2),('H2',153,21,3),('H2',154,21,4),('H2',155,21,5),('H2',156,21,6),('H2',157,21,7),('H2',158,21,8),('H2',159,21,9),('H2',160,22,0),('H2',161,22,1),('H2',162,22,2),('H2',163,22,3),('H2',164,22,4),('H2',165,22,5),('H2',166,23,0),('H2',167,23,1),('H2',168,23,2),('H2',169,23,3),('H2',170,24,0),('H2',171,24,1),('H2',172,24,2),('H2',173,25,0),('H2',174,25,1),('H2',175,25,2),('H2',176,25,3),('H2',177,25,4),('H2',178,25,5),('H2',179,26,0),('H2',180,26,1),('H2',181,26,2),('H2',182,27,0),('H2',183,27,1),('H2',184,27,2),('H2',185,27,3),('H2',186,28,0),('H2',187,28,1),('H2',188,28,2),('H2',189,28,3),('H2',190,29,0),('H2',191,29,1),('H2',192,29,2),('H2',193,29,3),('H2',194,30,0),('H2',195,30,1),('H2',196,30,2),('H2',197,30,3),('H2',198,30,4),('H2',199,30,5),('H2',200,30,6),('H2',201,30,7),('H2',202,30,8),('H2',203,30,9),('H2',204,30,10),('H2',205,30,11),('H2',206,30,12),('H2',207,30,13),('H2',208,30,14),('H2',209,30,15),('H2',210,31,1),('H2',211,31,2),('H2',212,31,3),('H2',213,31,4),('H2',214,32,1),('H2',215,32,2),('H2',216,32,3),('H2',217,32,4),('H2',218,32,5),('H2',219,32,6),('H2',220,33,1),('H2',221,33,2),('H2',222,33,3),('H2',223,34,1),('H2',224,34,2),('H2',225,34,3),('H2',226,35,1),('H2',227,35,2),('H2',228,35,3),('H2',229,36,1),('H2',230,36,2),('H2',231,36,3),('H2',232,36,4),('H2',233,36,5),('H2',234,36,6),('H2',235,37,1),('H2',236,37,2),('H2',237,37,3),('H2',238,37,4),('H2',239,38,1),('H2',240,38,2),('H2',241,38,3),('H2',242,39,1),('H2',243,39,2),('H2',244,39,3),('H2',245,40,1),('H2',246,40,2),('H2',247,41,1),('H2',248,41,2),('H2',249,41,3),('H2',250,41,4),('H2',251,42,1),('H2',252,42,2),('H2',253,42,3),('H2',254,42,4),('H2',255,43,1),('H2',256,43,2),('H2',257,43,3),('H2',258,43,4),('H2',259,44,1),('H2',260,44,2),('H2',261,44,3),('H2',262,44,4),('H2',263,44,5),('H2',264,44,6),('H2',265,44,7),('H2',266,44,8),('H2',267,44,9),('H2',268,44,10),('H2',269,44,11),('H2',270,45,1),('H2',271,45,2),('H2',272,45,3),('H2',273,45,4),('H2',274,45,5),('H2',275,45,6),('H2',276,45,7),('H2',277,45,8),('H2',278,45,9),('H2',279,46,1),('H2',280,46,2),('H2',281,46,3),('H2',282,46,4),('H2',283,47,1),('H2',284,47,2),('H2',285,47,3),('H2',286,47,4),('H2',287,48,1),('H2',288,48,2),('H2',289,48,3),('H2',290,48,4),('H2',291,48,5),('H2',292,48,6),('H2',293,48,7),('H2',294,48,8),('H2',295,48,9),('H2',296,48,10),('H2',297,48,11),('H2',298,49,1),('H2',327,49,30),('H2',334,49,1),('H2',335,49,1),('H2',336,49,2),('H2',337,49,3),('H2',338,49,4),('H2',339,49,5),('H2',340,49,6),('H2',341,49,7),('H2',342,49,8),('H2',343,49,9),('H2',344,49,10),('H2',345,49,11),('H2',346,49,12),('H2',347,49,13),('H2',348,49,14),('H2',349,49,15),('H2',350,49,16),('H2',351,49,17),('H2',352,49,18),('H2',353,49,19),('H2',354,49,20),('H2',355,49,21),('H2',356,49,22),('H2',357,49,23),('H2',358,49,24),('H2',359,49,25),('H2',360,49,26),('H2',361,49,27),('H2',362,49,28),('H2',363,49,29),('H2',364,49,30),('H2',365,49,31),('H2',366,49,32),('H2',399,49,1),('H2',400,49,2),('H2',401,49,3),('H2',402,49,4),('H2',403,49,5),('H2',404,49,6),('H2',405,49,7),('H2',406,49,8),('H2',407,49,9),('H2',408,49,10),('H2',409,49,11),('H2',410,49,12),('H2',411,49,13),('H2',412,49,14),('H2',413,49,15),('H2',414,49,16),('H2',416,49,1),('H2',417,49,2),('H2',418,49,3),('H2',419,49,4),('H2',420,49,5),('H2',421,49,6),('H2',422,49,7),('H2',423,49,8),('H2',424,49,9),('H2',425,49,10),('H2',426,49,11),('H2',427,49,12),('H2',428,49,13),('H2',429,49,14),('H2',430,49,15),('H2',431,49,16),('H2',432,49,17),('H2',433,49,18),('H2',434,49,19),('H2',435,49,20),('H2',436,49,21),('H2',437,49,22),('H2',438,49,23),('H2',439,49,24),('H2',440,49,25),('H2',441,49,26),('H2',442,49,27),('H2',443,49,28),('H2',444,49,29),('H2',445,49,30),('H2',446,49,31),('H2',447,49,32),('H2',449,49,34),('H2',450,49,35),('H2',451,49,36),('H2',452,49,37),('H2',453,49,38),('H2',454,49,39),('H2',455,49,40),('H2',456,49,41),('H2',457,49,42),('H2',458,49,43),('H2',459,49,44),('H2',475,49,1),('H2',476,49,2),('H2',477,49,3),('H2',478,49,4),('H2',479,49,5),('H2',480,49,6),('H2',481,49,7),('H2',482,49,8),('H2',483,49,9),('H2',484,49,10),('H2',485,49,11),('H2',490,49,1),('H2',491,49,1),('H2',492,49,2),('H2',493,49,3),('H2',494,49,4),('H2',495,49,5),('H2',496,49,6),('H2',497,49,7),('H2',498,49,8),('H2',499,49,9),('H2',500,49,10),('H2',501,49,11),('H2',502,49,12),('H2',503,49,13),('H2',504,49,14),('H2',505,49,15),('H2',506,49,16),('H2',507,49,17),('H2',508,49,18),('H2',509,49,19),('H2',510,49,20),('H2',511,49,21),('H2',512,49,22),('H2',513,49,23),('H2',514,49,24),('H2',515,49,25),('H2',516,49,26),('H2',517,49,27),('H2',518,49,28),('H2',519,49,29),('H2',520,49,30),('H2',521,49,31),('H2',522,49,32),('H2',523,49,33),('H2',524,49,34),('H2',525,49,35),('H2',526,49,36),('H2',329,50,1),('H2',330,50,2),('H2',331,50,3),('H2',332,50,4),('H2',333,50,5),('H2',394,53,1),('H2',395,53,2),('H2',396,53,3),('H2',397,53,1),('H2',398,53,2),('H2',415,53,1),('H2',461,53,1),('H2',462,53,2),('H2',463,53,3),('H2',464,53,4),('H2',465,53,5),('H2',466,53,6),('H2',467,53,7),('H2',468,53,8),('H2',527,53,1),('H2',528,53,2),('H2',469,54,1),('H2',470,54,2),('H2',471,54,3),('H2',472,54,4),('H2',473,54,5),('H2',474,54,6),('H2',486,55,1),('H2',487,55,2),('H2',488,55,3),('H2',489,55,4),('H2',536,56,1),('H2',537,56,2),('H2',538,56,3),('H2',539,56,4),('H3',1,1,0),('H3',2,1,1),('H3',3,1,2),('H3',4,1,3),('H3',5,1,4),('H3',6,1,5),('H3',7,1,6),('H3',8,1,7),('H3',9,1,8),('H3',10,1,9),('H3',11,1,10),('H3',12,2,0),('H3',13,2,1),('H3',534,2,1),('H3',535,2,2),('H3',14,3,0),('H3',15,3,1),('H3',16,3,2),('H3',17,3,3),('H3',18,3,4),('H3',19,3,5),('H3',20,4,0),('H3',21,4,1),('H3',22,4,2),('H3',23,4,3),('H3',24,4,4),('H3',25,4,5),('H3',26,4,6),('H3',27,4,7),('H3',28,4,8),('H3',29,4,9),('H3',30,4,10),('H3',31,4,11),('H3',32,4,12),('H3',33,5,0),('H3',34,5,1),('H3',35,5,2),('H3',36,5,3),('H3',37,5,4),('H3',38,6,0),('H3',39,6,1),('H3',40,6,2),('H3',529,6,1),('H3',530,6,2),('H3',531,6,3),('H3',532,6,4),('H3',533,6,5),('H3',41,7,0),('H3',42,7,1),('H3',43,7,2),('H3',44,7,3),('H3',45,7,4),('H3',46,8,0),('H3',47,9,0),('H3',48,9,1),('H3',49,9,2),('H3',50,9,3),('H3',51,10,0),('H3',52,10,1),('H3',53,10,2),('H3',54,10,3),('H3',55,10,4),('H3',56,10,5),('H3',57,10,6),('H3',58,10,7),('H3',59,10,8),('H3',60,11,0),('H3',61,11,1),('H3',62,11,2),('H3',63,11,3),('H3',64,11,4),('H3',65,11,5),('H3',66,11,6),('H3',67,11,7),('H3',68,11,8),('H3',69,11,9),('H3',70,11,10),('H3',71,11,11),('H3',72,11,12),('H3',73,11,13),('H3',74,11,14),('H3',75,11,15),('H3',76,11,16),('H3',77,11,17),('H3',78,11,18),('H3',79,11,19),('H3',80,11,20),('H3',81,11,21),('H3',82,11,22),('H3',83,11,23),('H3',84,11,24),('H3',85,11,25),('H3',86,11,26),('H3',87,11,27),('H3',88,11,28),('H3',89,11,29),('H3',90,11,30),('H3',91,11,31),('H3',92,12,0),('H3',93,12,1),('H3',94,12,2),('H3',95,12,3),('H3',96,12,4),('H3',97,12,5),('H3',367,12,1),('H3',98,13,0),('H3',99,13,1),('H3',100,13,2),('H3',101,13,3),('H3',102,13,4),('H3',103,13,5),('H3',104,13,6),('H3',105,13,7),('H3',106,13,8),('H3',107,13,9),('H3',108,13,10),('H3',109,13,11),('H3',110,13,12),('H3',111,14,0),('H3',112,14,1),('H3',113,14,2),('H3',114,14,3),('H3',115,14,4),('H3',116,14,5),('H3',117,14,6),('H3',118,14,7),('H3',119,14,8),('H3',120,14,9),('H3',121,14,10),('H3',122,14,11),('H3',123,15,0),('H3',124,15,1),('H3',125,15,2),('H3',126,15,3),('H3',127,16,0),('H3',128,16,1),('H3',129,16,2),('H3',130,17,0),('H3',131,17,1),('H3',132,17,2),('H3',133,17,3),('H3',134,17,4),('H3',135,18,0),('H3',136,18,1),('H3',137,18,2),('H3',138,18,3),('H3',139,19,0),('H3',140,19,1),('H3',141,19,2),('H3',142,19,3),('H3',143,19,4),('H3',144,20,0),('H3',145,20,1),('H3',146,20,2),('H3',147,20,3),('H3',148,20,4),('H3',149,20,5),('H3',150,21,0),('H3',151,21,1),('H3',152,21,2),('H3',153,21,3),('H3',154,21,4),('H3',155,21,5),('H3',156,21,6),('H3',157,21,7),('H3',158,21,8),('H3',159,21,9),('H3',160,22,0),('H3',161,22,1),('H3',162,22,2),('H3',163,22,3),('H3',164,22,4),('H3',165,22,5),('H3',166,23,0),('H3',167,23,1),('H3',168,23,2),('H3',169,23,3),('H3',170,24,0),('H3',171,24,1),('H3',172,24,2),('H3',173,25,0),('H3',174,25,1),('H3',175,25,2),('H3',176,25,3),('H3',177,25,4),('H3',178,25,5),('H3',179,26,0),('H3',180,26,1),('H3',181,26,2),('H3',182,27,0),('H3',183,27,1),('H3',184,27,2),('H3',185,27,3),('H3',186,28,0),('H3',187,28,1),('H3',188,28,2),('H3',189,28,3),('H3',190,29,0),('H3',191,29,1),('H3',192,29,2),('H3',193,29,3),('H3',194,30,0),('H3',195,30,1),('H3',196,30,2),('H3',197,30,3),('H3',198,30,4),('H3',199,30,5),('H3',200,30,6),('H3',201,30,7),('H3',202,30,8),('H3',203,30,9),('H3',204,30,10),('H3',205,30,11),('H3',206,30,12),('H3',207,30,13),('H3',208,30,14),('H3',209,30,15),('H3',210,31,1),('H3',211,31,2),('H3',212,31,3),('H3',213,31,4),('H3',214,32,1),('H3',215,32,2),('H3',216,32,3),('H3',217,32,4),('H3',218,32,5),('H3',219,32,6),('H3',220,33,1),('H3',221,33,2),('H3',222,33,3),('H3',223,34,1),('H3',224,34,2),('H3',225,34,3),('H3',226,35,1),('H3',227,35,2),('H3',228,35,3),('H3',229,36,1),('H3',230,36,2),('H3',231,36,3),('H3',232,36,4),('H3',233,36,5),('H3',234,36,6),('H3',235,37,1),('H3',236,37,2),('H3',237,37,3),('H3',238,37,4),('H3',239,38,1),('H3',240,38,2),('H3',241,38,3),('H3',242,39,1),('H3',243,39,2),('H3',244,39,3),('H3',245,40,1),('H3',246,40,2),('H3',247,41,1),('H3',248,41,2),('H3',249,41,3),('H3',250,41,4),('H3',251,42,1),('H3',252,42,2),('H3',253,42,3),('H3',254,42,4),('H3',255,43,1),('H3',256,43,2),('H3',257,43,3),('H3',258,43,4),('H3',259,44,1),('H3',260,44,2),('H3',261,44,3),('H3',262,44,4),('H3',263,44,5),('H3',264,44,6),('H3',265,44,7),('H3',266,44,8),('H3',267,44,9),('H3',268,44,10),('H3',269,44,11),('H3',270,45,1),('H3',271,45,2),('H3',272,45,3),('H3',273,45,4),('H3',274,45,5),('H3',275,45,6),('H3',276,45,7),('H3',277,45,8),('H3',278,45,9),('H3',279,46,1),('H3',280,46,2),('H3',281,46,3),('H3',282,46,4),('H3',283,47,1),('H3',284,47,2),('H3',285,47,3),('H3',286,47,4),('H3',287,48,1),('H3',288,48,2),('H3',289,48,3),('H3',290,48,4),('H3',291,48,5),('H3',292,48,6),('H3',293,48,7),('H3',294,48,8),('H3',295,48,9),('H3',296,48,10),('H3',297,48,11),('H3',298,49,1),('H3',327,49,30),('H3',334,49,1),('H3',335,49,1),('H3',336,49,2),('H3',337,49,3),('H3',338,49,4),('H3',339,49,5),('H3',340,49,6),('H3',341,49,7),('H3',342,49,8),('H3',343,49,9),('H3',344,49,10),('H3',345,49,11),('H3',346,49,12),('H3',347,49,13),('H3',348,49,14),('H3',349,49,15),('H3',350,49,16),('H3',351,49,17),('H3',352,49,18),('H3',353,49,19),('H3',354,49,20),('H3',355,49,21),('H3',356,49,22),('H3',357,49,23),('H3',358,49,24),('H3',359,49,25),('H3',360,49,26),('H3',361,49,27),('H3',362,49,28),('H3',363,49,29),('H3',364,49,30),('H3',365,49,31),('H3',366,49,32),('H3',399,49,1),('H3',400,49,2),('H3',401,49,3),('H3',402,49,4),('H3',403,49,5),('H3',404,49,6),('H3',405,49,7),('H3',406,49,8),('H3',407,49,9),('H3',408,49,10),('H3',409,49,11),('H3',410,49,12),('H3',411,49,13),('H3',412,49,14),('H3',413,49,15),('H3',414,49,16),('H3',416,49,1),('H3',417,49,2),('H3',418,49,3),('H3',419,49,4),('H3',420,49,5),('H3',421,49,6),('H3',422,49,7),('H3',423,49,8),('H3',424,49,9),('H3',425,49,10),('H3',426,49,11),('H3',427,49,12),('H3',428,49,13),('H3',429,49,14),('H3',430,49,15),('H3',431,49,16),('H3',432,49,17),('H3',433,49,18),('H3',434,49,19),('H3',435,49,20),('H3',436,49,21),('H3',437,49,22),('H3',438,49,23),('H3',439,49,24),('H3',440,49,25),('H3',441,49,26),('H3',442,49,27),('H3',443,49,28),('H3',444,49,29),('H3',445,49,30),('H3',446,49,31),('H3',447,49,32),('H3',449,49,34),('H3',450,49,35),('H3',451,49,36),('H3',452,49,37),('H3',453,49,38),('H3',454,49,39),('H3',455,49,40),('H3',456,49,41),('H3',457,49,42),('H3',458,49,43),('H3',459,49,44),('H3',475,49,1),('H3',476,49,2),('H3',477,49,3),('H3',478,49,4),('H3',479,49,5),('H3',480,49,6),('H3',481,49,7),('H3',482,49,8),('H3',483,49,9),('H3',484,49,10),('H3',485,49,11),('H3',490,49,1),('H3',491,49,1),('H3',492,49,2),('H3',493,49,3),('H3',494,49,4),('H3',495,49,5),('H3',496,49,6),('H3',497,49,7),('H3',498,49,8),('H3',499,49,9),('H3',500,49,10),('H3',501,49,11),('H3',502,49,12),('H3',503,49,13),('H3',504,49,14),('H3',505,49,15),('H3',506,49,16),('H3',507,49,17),('H3',508,49,18),('H3',509,49,19),('H3',510,49,20),('H3',511,49,21),('H3',512,49,22),('H3',513,49,23),('H3',514,49,24),('H3',515,49,25),('H3',516,49,26),('H3',517,49,27),('H3',518,49,28),('H3',519,49,29),('H3',520,49,30),('H3',521,49,31),('H3',522,49,32),('H3',523,49,33),('H3',524,49,34),('H3',525,49,35),('H3',526,49,36),('H3',329,50,1),('H3',330,50,2),('H3',331,50,3),('H3',332,50,4),('H3',333,50,5),('H3',394,53,1),('H3',395,53,2),('H3',396,53,3),('H3',397,53,1),('H3',398,53,2),('H3',415,53,1),('H3',461,53,1),('H3',462,53,2),('H3',463,53,3),('H3',464,53,4),('H3',465,53,5),('H3',466,53,6),('H3',467,53,7),('H3',468,53,8),('H3',527,53,1),('H3',528,53,2),('H3',469,54,1),('H3',470,54,2),('H3',471,54,3),('H3',472,54,4),('H3',473,54,5),('H3',474,54,6),('H3',486,55,1),('H3',487,55,2),('H3',488,55,3),('H3',489,55,4),('H3',536,56,1),('H3',537,56,2),('H3',538,56,3),('H3',539,56,4),('H4',1,1,0),('H4',2,1,1),('H4',3,1,2),('H4',4,1,3),('H4',5,1,4),('H4',6,1,5),('H4',7,1,6),('H4',8,1,7),('H4',9,1,8),('H4',10,1,9),('H4',11,1,10),('H4',12,2,0),('H4',13,2,1),('H4',534,2,1),('H4',535,2,2),('H4',14,3,0),('H4',15,3,1),('H4',16,3,2),('H4',17,3,3),('H4',18,3,4),('H4',19,3,5),('H4',20,4,0),('H4',21,4,1),('H4',22,4,2),('H4',23,4,3),('H4',24,4,4),('H4',25,4,5),('H4',26,4,6),('H4',27,4,7),('H4',28,4,8),('H4',29,4,9),('H4',30,4,10),('H4',31,4,11),('H4',32,4,12),('H4',33,5,0),('H4',34,5,1),('H4',35,5,2),('H4',36,5,3),('H4',37,5,4),('H4',38,6,0),('H4',39,6,1),('H4',40,6,2),('H4',529,6,1),('H4',530,6,2),('H4',531,6,3),('H4',532,6,4),('H4',533,6,5),('H4',41,7,0),('H4',42,7,1),('H4',43,7,2),('H4',44,7,3),('H4',45,7,4),('H4',46,8,0),('H4',47,9,0),('H4',48,9,1),('H4',49,9,2),('H4',50,9,3),('H4',51,10,0),('H4',52,10,1),('H4',53,10,2),('H4',54,10,3),('H4',55,10,4),('H4',56,10,5),('H4',57,10,6),('H4',58,10,7),('H4',59,10,8),('H4',60,11,0),('H4',61,11,1),('H4',62,11,2),('H4',63,11,3),('H4',64,11,4),('H4',65,11,5),('H4',66,11,6),('H4',67,11,7),('H4',68,11,8),('H4',69,11,9),('H4',70,11,10),('H4',71,11,11),('H4',72,11,12),('H4',73,11,13),('H4',74,11,14),('H4',75,11,15),('H4',76,11,16),('H4',77,11,17),('H4',78,11,18),('H4',79,11,19),('H4',80,11,20),('H4',81,11,21),('H4',82,11,22),('H4',83,11,23),('H4',84,11,24),('H4',85,11,25),('H4',86,11,26),('H4',87,11,27),('H4',88,11,28),('H4',89,11,29),('H4',90,11,30),('H4',91,11,31),('H4',92,12,0),('H4',93,12,1),('H4',94,12,2),('H4',95,12,3),('H4',96,12,4),('H4',97,12,5),('H4',367,12,1),('H4',98,13,0),('H4',99,13,1),('H4',100,13,2),('H4',101,13,3),('H4',102,13,4),('H4',103,13,5),('H4',104,13,6),('H4',105,13,7),('H4',106,13,8),('H4',107,13,9),('H4',108,13,10),('H4',109,13,11),('H4',110,13,12),('H4',111,14,0),('H4',112,14,1),('H4',113,14,2),('H4',114,14,3),('H4',115,14,4),('H4',116,14,5),('H4',117,14,6),('H4',118,14,7),('H4',119,14,8),('H4',120,14,9),('H4',121,14,10),('H4',122,14,11),('H4',123,15,0),('H4',124,15,1),('H4',125,15,2),('H4',126,15,3),('H4',127,16,0),('H4',128,16,1),('H4',129,16,2),('H4',130,17,0),('H4',131,17,1),('H4',132,17,2),('H4',133,17,3),('H4',134,17,4),('H4',135,18,0),('H4',136,18,1),('H4',137,18,2),('H4',138,18,3),('H4',139,19,0),('H4',140,19,1),('H4',141,19,2),('H4',142,19,3),('H4',143,19,4),('H4',144,20,0),('H4',145,20,1),('H4',146,20,2),('H4',147,20,3),('H4',148,20,4),('H4',149,20,5),('H4',150,21,0),('H4',151,21,1),('H4',152,21,2),('H4',153,21,3),('H4',154,21,4),('H4',155,21,5),('H4',156,21,6),('H4',157,21,7),('H4',158,21,8),('H4',159,21,9),('H4',160,22,0),('H4',161,22,1),('H4',162,22,2),('H4',163,22,3),('H4',164,22,4),('H4',165,22,5),('H4',166,23,0),('H4',167,23,1),('H4',168,23,2),('H4',169,23,3),('H4',170,24,0),('H4',171,24,1),('H4',172,24,2),('H4',173,25,0),('H4',174,25,1),('H4',175,25,2),('H4',176,25,3),('H4',177,25,4),('H4',178,25,5),('H4',179,26,0),('H4',180,26,1),('H4',181,26,2),('H4',182,27,0),('H4',183,27,1),('H4',184,27,2),('H4',185,27,3),('H4',186,28,0),('H4',187,28,1),('H4',188,28,2),('H4',189,28,3),('H4',190,29,0),('H4',191,29,1),('H4',192,29,2),('H4',193,29,3),('H4',194,30,0),('H4',195,30,1),('H4',196,30,2),('H4',197,30,3),('H4',198,30,4),('H4',199,30,5),('H4',200,30,6),('H4',201,30,7),('H4',202,30,8),('H4',203,30,9),('H4',204,30,10),('H4',205,30,11),('H4',206,30,12),('H4',207,30,13),('H4',208,30,14),('H4',209,30,15),('H4',210,31,1),('H4',211,31,2),('H4',212,31,3),('H4',213,31,4),('H4',214,32,1),('H4',215,32,2),('H4',216,32,3),('H4',217,32,4),('H4',218,32,5),('H4',219,32,6),('H4',220,33,1),('H4',221,33,2),('H4',222,33,3),('H4',223,34,1),('H4',224,34,2),('H4',225,34,3),('H4',226,35,1),('H4',227,35,2),('H4',228,35,3),('H4',229,36,1),('H4',230,36,2),('H4',231,36,3),('H4',232,36,4),('H4',233,36,5),('H4',234,36,6),('H4',235,37,1),('H4',236,37,2),('H4',237,37,3),('H4',238,37,4),('H4',239,38,1),('H4',240,38,2),('H4',241,38,3),('H4',242,39,1),('H4',243,39,2),('H4',244,39,3),('H4',245,40,1),('H4',246,40,2),('H4',247,41,1),('H4',248,41,2),('H4',249,41,3),('H4',250,41,4),('H4',251,42,1),('H4',252,42,2),('H4',253,42,3),('H4',254,42,4),('H4',255,43,1),('H4',256,43,2),('H4',257,43,3),('H4',258,43,4),('H4',259,44,1),('H4',260,44,2),('H4',261,44,3),('H4',262,44,4),('H4',263,44,5),('H4',264,44,6),('H4',265,44,7),('H4',266,44,8),('H4',267,44,9),('H4',268,44,10),('H4',269,44,11),('H4',270,45,1),('H4',271,45,2),('H4',272,45,3),('H4',273,45,4),('H4',274,45,5),('H4',275,45,6),('H4',276,45,7),('H4',277,45,8),('H4',278,45,9),('H4',279,46,1),('H4',280,46,2),('H4',281,46,3),('H4',282,46,4),('H4',283,47,1),('H4',284,47,2),('H4',285,47,3),('H4',286,47,4),('H4',287,48,1),('H4',288,48,2),('H4',289,48,3),('H4',290,48,4),('H4',291,48,5),('H4',292,48,6),('H4',293,48,7),('H4',294,48,8),('H4',295,48,9),('H4',296,48,10),('H4',297,48,11),('H4',298,49,1),('H4',327,49,30),('H4',334,49,1),('H4',335,49,1),('H4',336,49,2),('H4',337,49,3),('H4',338,49,4),('H4',339,49,5),('H4',340,49,6),('H4',341,49,7),('H4',342,49,8),('H4',343,49,9),('H4',344,49,10),('H4',345,49,11),('H4',346,49,12),('H4',347,49,13),('H4',348,49,14),('H4',349,49,15),('H4',350,49,16),('H4',351,49,17),('H4',352,49,18),('H4',353,49,19),('H4',354,49,20),('H4',355,49,21),('H4',356,49,22),('H4',357,49,23),('H4',358,49,24),('H4',359,49,25),('H4',360,49,26),('H4',361,49,27),('H4',362,49,28),('H4',363,49,29),('H4',364,49,30),('H4',365,49,31),('H4',366,49,32),('H4',399,49,1),('H4',400,49,2),('H4',401,49,3),('H4',402,49,4),('H4',403,49,5),('H4',404,49,6),('H4',405,49,7),('H4',406,49,8),('H4',407,49,9),('H4',408,49,10),('H4',409,49,11),('H4',410,49,12),('H4',411,49,13),('H4',412,49,14),('H4',413,49,15),('H4',414,49,16),('H4',416,49,1),('H4',417,49,2),('H4',418,49,3),('H4',419,49,4),('H4',420,49,5),('H4',421,49,6),('H4',422,49,7),('H4',423,49,8),('H4',424,49,9),('H4',425,49,10),('H4',426,49,11),('H4',427,49,12),('H4',428,49,13),('H4',429,49,14),('H4',430,49,15),('H4',431,49,16),('H4',432,49,17),('H4',433,49,18),('H4',434,49,19),('H4',435,49,20),('H4',436,49,21),('H4',437,49,22),('H4',438,49,23),('H4',439,49,24),('H4',440,49,25),('H4',441,49,26),('H4',442,49,27),('H4',443,49,28),('H4',444,49,29),('H4',445,49,30),('H4',446,49,31),('H4',447,49,32),('H4',449,49,34),('H4',450,49,35),('H4',451,49,36),('H4',452,49,37),('H4',453,49,38),('H4',454,49,39),('H4',455,49,40),('H4',456,49,41),('H4',457,49,42),('H4',458,49,43),('H4',459,49,44),('H4',475,49,1),('H4',476,49,2),('H4',477,49,3),('H4',478,49,4),('H4',479,49,5),('H4',480,49,6),('H4',481,49,7),('H4',482,49,8),('H4',483,49,9),('H4',484,49,10),('H4',485,49,11),('H4',490,49,1),('H4',491,49,1),('H4',492,49,2),('H4',493,49,3),('H4',494,49,4),('H4',495,49,5),('H4',496,49,6),('H4',497,49,7),('H4',498,49,8),('H4',499,49,9),('H4',500,49,10),('H4',501,49,11),('H4',502,49,12),('H4',503,49,13),('H4',504,49,14),('H4',505,49,15),('H4',506,49,16),('H4',507,49,17),('H4',508,49,18),('H4',509,49,19),('H4',510,49,20),('H4',511,49,21),('H4',512,49,22),('H4',513,49,23),('H4',514,49,24),('H4',515,49,25),('H4',516,49,26),('H4',517,49,27),('H4',518,49,28),('H4',519,49,29),('H4',520,49,30),('H4',521,49,31),('H4',522,49,32),('H4',523,49,33),('H4',524,49,34),('H4',525,49,35),('H4',526,49,36),('H4',329,50,1),('H4',330,50,2),('H4',331,50,3),('H4',332,50,4),('H4',333,50,5),('H4',394,53,1),('H4',395,53,2),('H4',396,53,3),('H4',397,53,1),('H4',398,53,2),('H4',415,53,1),('H4',461,53,1),('H4',462,53,2),('H4',463,53,3),('H4',464,53,4),('H4',465,53,5),('H4',466,53,6),('H4',467,53,7),('H4',468,53,8),('H4',527,53,1),('H4',528,53,2),('H4',469,54,1),('H4',470,54,2),('H4',471,54,3),('H4',472,54,4),('H4',473,54,5),('H4',474,54,6),('H4',486,55,1),('H4',487,55,2),('H4',488,55,3),('H4',489,55,4),('H4',536,56,1),('H4',537,56,2),('H4',538,56,3),('H4',539,56,4),('H5',1,1,0),('H5',2,1,1),('H5',3,1,2),('H5',4,1,3),('H5',5,1,4),('H5',6,1,5),('H5',7,1,6),('H5',8,1,7),('H5',9,1,8),('H5',10,1,9),('H5',11,1,10),('H5',12,2,0),('H5',13,2,1),('H5',534,2,1),('H5',535,2,2),('H5',14,3,0),('H5',15,3,1),('H5',16,3,2),('H5',17,3,3),('H5',18,3,4),('H5',19,3,5),('H5',20,4,0),('H5',21,4,1),('H5',22,4,2),('H5',23,4,3),('H5',24,4,4),('H5',25,4,5),('H5',26,4,6),('H5',27,4,7),('H5',28,4,8),('H5',29,4,9),('H5',30,4,10),('H5',31,4,11),('H5',32,4,12),('H5',33,5,0),('H5',34,5,1),('H5',35,5,2),('H5',36,5,3),('H5',37,5,4),('H5',38,6,0),('H5',39,6,1),('H5',40,6,2),('H5',529,6,1),('H5',530,6,2),('H5',531,6,3),('H5',532,6,4),('H5',533,6,5),('H5',41,7,0),('H5',42,7,1),('H5',43,7,2),('H5',44,7,3),('H5',45,7,4),('H5',46,8,0),('H5',47,9,0),('H5',48,9,1),('H5',49,9,2),('H5',50,9,3),('H5',51,10,0),('H5',52,10,1),('H5',53,10,2),('H5',54,10,3),('H5',55,10,4),('H5',56,10,5),('H5',57,10,6),('H5',58,10,7),('H5',59,10,8),('H5',60,11,0),('H5',61,11,1),('H5',62,11,2),('H5',63,11,3),('H5',64,11,4),('H5',65,11,5),('H5',66,11,6),('H5',67,11,7),('H5',68,11,8),('H5',69,11,9),('H5',70,11,10),('H5',71,11,11),('H5',72,11,12),('H5',73,11,13),('H5',74,11,14),('H5',75,11,15),('H5',76,11,16),('H5',77,11,17),('H5',78,11,18),('H5',79,11,19),('H5',80,11,20),('H5',81,11,21),('H5',82,11,22),('H5',83,11,23),('H5',84,11,24),('H5',85,11,25),('H5',86,11,26),('H5',87,11,27),('H5',88,11,28),('H5',89,11,29),('H5',90,11,30),('H5',91,11,31),('H5',92,12,0),('H5',93,12,1),('H5',94,12,2),('H5',95,12,3),('H5',96,12,4),('H5',97,12,5),('H5',367,12,1),('H5',98,13,0),('H5',99,13,1),('H5',100,13,2),('H5',101,13,3),('H5',102,13,4),('H5',103,13,5),('H5',104,13,6),('H5',105,13,7),('H5',106,13,8),('H5',107,13,9),('H5',108,13,10),('H5',109,13,11),('H5',110,13,12),('H5',111,14,0),('H5',112,14,1),('H5',113,14,2),('H5',114,14,3),('H5',115,14,4),('H5',116,14,5),('H5',117,14,6),('H5',118,14,7),('H5',119,14,8),('H5',120,14,9),('H5',121,14,10),('H5',122,14,11),('H5',123,15,0),('H5',124,15,1),('H5',125,15,2),('H5',126,15,3),('H5',127,16,0),('H5',128,16,1),('H5',129,16,2),('H5',130,17,0),('H5',131,17,1),('H5',132,17,2),('H5',133,17,3),('H5',134,17,4),('H5',135,18,0),('H5',136,18,1),('H5',137,18,2),('H5',138,18,3),('H5',139,19,0),('H5',140,19,1),('H5',141,19,2),('H5',142,19,3),('H5',143,19,4),('H5',144,20,0),('H5',145,20,1),('H5',146,20,2),('H5',147,20,3),('H5',148,20,4),('H5',149,20,5),('H5',150,21,0),('H5',151,21,1),('H5',152,21,2),('H5',153,21,3),('H5',154,21,4),('H5',155,21,5),('H5',156,21,6),('H5',157,21,7),('H5',158,21,8),('H5',159,21,9),('H5',160,22,0),('H5',161,22,1),('H5',162,22,2),('H5',163,22,3),('H5',164,22,4),('H5',165,22,5),('H5',166,23,0),('H5',167,23,1),('H5',168,23,2),('H5',169,23,3),('H5',170,24,0),('H5',171,24,1),('H5',172,24,2),('H5',173,25,0),('H5',174,25,1),('H5',175,25,2),('H5',176,25,3),('H5',177,25,4),('H5',178,25,5),('H5',179,26,0),('H5',180,26,1),('H5',181,26,2),('H5',182,27,0),('H5',183,27,1),('H5',184,27,2),('H5',185,27,3),('H5',186,28,0),('H5',187,28,1),('H5',188,28,2),('H5',189,28,3),('H5',190,29,0),('H5',191,29,1),('H5',192,29,2),('H5',193,29,3),('H5',194,30,0),('H5',195,30,1),('H5',196,30,2),('H5',197,30,3),('H5',198,30,4),('H5',199,30,5),('H5',200,30,6),('H5',201,30,7),('H5',202,30,8),('H5',203,30,9),('H5',204,30,10),('H5',205,30,11),('H5',206,30,12),('H5',207,30,13),('H5',208,30,14),('H5',209,30,15),('H5',210,31,1),('H5',211,31,2),('H5',212,31,3),('H5',213,31,4),('H5',214,32,1),('H5',215,32,2),('H5',216,32,3),('H5',217,32,4),('H5',218,32,5),('H5',219,32,6),('H5',220,33,1),('H5',221,33,2),('H5',222,33,3),('H5',223,34,1),('H5',224,34,2),('H5',225,34,3),('H5',226,35,1),('H5',227,35,2),('H5',228,35,3),('H5',229,36,1),('H5',230,36,2),('H5',231,36,3),('H5',232,36,4),('H5',233,36,5),('H5',234,36,6),('H5',235,37,1),('H5',236,37,2),('H5',237,37,3),('H5',238,37,4),('H5',239,38,1),('H5',240,38,2),('H5',241,38,3),('H5',242,39,1),('H5',243,39,2),('H5',244,39,3),('H5',245,40,1),('H5',246,40,2),('H5',247,41,1),('H5',248,41,2),('H5',249,41,3),('H5',250,41,4),('H5',251,42,1),('H5',252,42,2),('H5',253,42,3),('H5',254,42,4),('H5',255,43,1),('H5',256,43,2),('H5',257,43,3),('H5',258,43,4),('H5',259,44,1),('H5',260,44,2),('H5',261,44,3),('H5',262,44,4),('H5',263,44,5),('H5',264,44,6),('H5',265,44,7),('H5',266,44,8),('H5',267,44,9),('H5',268,44,10),('H5',269,44,11),('H5',270,45,1),('H5',271,45,2),('H5',272,45,3),('H5',273,45,4),('H5',274,45,5),('H5',275,45,6),('H5',276,45,7),('H5',277,45,8),('H5',278,45,9),('H5',279,46,1),('H5',280,46,2),('H5',281,46,3),('H5',282,46,4),('H5',283,47,1),('H5',284,47,2),('H5',285,47,3),('H5',286,47,4),('H5',287,48,1),('H5',288,48,2),('H5',289,48,3),('H5',290,48,4),('H5',291,48,5),('H5',292,48,6),('H5',293,48,7),('H5',294,48,8),('H5',295,48,9),('H5',296,48,10),('H5',297,48,11),('H5',298,49,1),('H5',327,49,30),('H5',334,49,1),('H5',335,49,1),('H5',336,49,2),('H5',337,49,3),('H5',338,49,4),('H5',339,49,5),('H5',340,49,6),('H5',341,49,7),('H5',342,49,8),('H5',343,49,9),('H5',344,49,10),('H5',345,49,11),('H5',346,49,12),('H5',347,49,13),('H5',348,49,14),('H5',349,49,15),('H5',350,49,16),('H5',351,49,17),('H5',352,49,18),('H5',353,49,19),('H5',354,49,20),('H5',355,49,21),('H5',356,49,22),('H5',357,49,23),('H5',358,49,24),('H5',359,49,25),('H5',360,49,26),('H5',361,49,27),('H5',362,49,28),('H5',363,49,29),('H5',364,49,30),('H5',365,49,31),('H5',366,49,32),('H5',399,49,1),('H5',400,49,2),('H5',401,49,3),('H5',402,49,4),('H5',403,49,5),('H5',404,49,6),('H5',405,49,7),('H5',406,49,8),('H5',407,49,9),('H5',408,49,10),('H5',409,49,11),('H5',410,49,12),('H5',411,49,13),('H5',412,49,14),('H5',413,49,15),('H5',414,49,16),('H5',416,49,1),('H5',417,49,2),('H5',418,49,3),('H5',419,49,4),('H5',420,49,5),('H5',421,49,6),('H5',422,49,7),('H5',423,49,8),('H5',424,49,9),('H5',425,49,10),('H5',426,49,11),('H5',427,49,12),('H5',428,49,13),('H5',429,49,14),('H5',430,49,15),('H5',431,49,16),('H5',432,49,17),('H5',433,49,18),('H5',434,49,19),('H5',435,49,20),('H5',436,49,21),('H5',437,49,22),('H5',438,49,23),('H5',439,49,24),('H5',440,49,25),('H5',441,49,26),('H5',442,49,27),('H5',443,49,28),('H5',444,49,29),('H5',445,49,30),('H5',446,49,31),('H5',447,49,32),('H5',449,49,34),('H5',450,49,35),('H5',451,49,36),('H5',452,49,37),('H5',453,49,38),('H5',454,49,39),('H5',455,49,40),('H5',456,49,41),('H5',457,49,42),('H5',458,49,43),('H5',459,49,44),('H5',475,49,1),('H5',476,49,2),('H5',477,49,3),('H5',478,49,4),('H5',479,49,5),('H5',480,49,6),('H5',481,49,7),('H5',482,49,8),('H5',483,49,9),('H5',484,49,10),('H5',485,49,11),('H5',490,49,1),('H5',491,49,1),('H5',492,49,2),('H5',493,49,3),('H5',494,49,4),('H5',495,49,5),('H5',496,49,6),('H5',497,49,7),('H5',498,49,8),('H5',499,49,9),('H5',500,49,10),('H5',501,49,11),('H5',502,49,12),('H5',503,49,13),('H5',504,49,14),('H5',505,49,15),('H5',506,49,16),('H5',507,49,17),('H5',508,49,18),('H5',509,49,19),('H5',510,49,20),('H5',511,49,21),('H5',512,49,22),('H5',513,49,23),('H5',514,49,24),('H5',515,49,25),('H5',516,49,26),('H5',517,49,27),('H5',518,49,28),('H5',519,49,29),('H5',520,49,30),('H5',521,49,31),('H5',522,49,32),('H5',523,49,33),('H5',524,49,34),('H5',525,49,35),('H5',526,49,36),('H5',329,50,1),('H5',330,50,2),('H5',331,50,3),('H5',332,50,4),('H5',333,50,5),('H5',394,53,1),('H5',395,53,2),('H5',396,53,3),('H5',397,53,1),('H5',398,53,2),('H5',415,53,1),('H5',461,53,1),('H5',462,53,2),('H5',463,53,3),('H5',464,53,4),('H5',465,53,5),('H5',466,53,6),('H5',467,53,7),('H5',468,53,8),('H5',527,53,1),('H5',528,53,2),('H5',469,54,1),('H5',470,54,2),('H5',471,54,3),('H5',472,54,4),('H5',473,54,5),('H5',474,54,6),('H5',486,55,1),('H5',487,55,2),('H5',488,55,3),('H5',489,55,4),('H5',536,56,1),('H5',537,56,2),('H5',538,56,3),('H5',539,56,4);
/*!40000 ALTER TABLE `vtiger_role2picklist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_role2profile`
--

DROP TABLE IF EXISTS `vtiger_role2profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_role2profile` (
  `roleid` varchar(255) NOT NULL,
  `profileid` int(11) NOT NULL,
  PRIMARY KEY (`roleid`,`profileid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_role2profile`
--

LOCK TABLES `vtiger_role2profile` WRITE;
/*!40000 ALTER TABLE `vtiger_role2profile` DISABLE KEYS */;
INSERT INTO `vtiger_role2profile` VALUES ('H2',1),('H3',2),('H4',2),('H5',2);
/*!40000 ALTER TABLE `vtiger_role2profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_role_seq`
--

DROP TABLE IF EXISTS `vtiger_role_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_role_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_role_seq`
--

LOCK TABLES `vtiger_role_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_role_seq` DISABLE KEYS */;
INSERT INTO `vtiger_role_seq` VALUES (5);
/*!40000 ALTER TABLE `vtiger_role_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_rss`
--

DROP TABLE IF EXISTS `vtiger_rss`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_rss` (
  `rssid` int(19) NOT NULL,
  `rssurl` varchar(200) NOT NULL DEFAULT '',
  `rsstitle` varchar(200) DEFAULT NULL,
  `rsstype` int(10) DEFAULT '0',
  `starred` int(1) DEFAULT '0',
  PRIMARY KEY (`rssid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_rss`
--

LOCK TABLES `vtiger_rss` WRITE;
/*!40000 ALTER TABLE `vtiger_rss` DISABLE KEYS */;
INSERT INTO `vtiger_rss` VALUES (1,'http://forums.vtiger.com/rss.php?name=forums&file=rss','vtiger - Forums',0,0),(2,'http://trac.vtiger.com/cgi-bin/trac.cgi/report/8?format=rss&USER=anonymous','vtiger development - Active Tickets',0,0);
/*!40000 ALTER TABLE `vtiger_rss` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_rss_seq`
--

DROP TABLE IF EXISTS `vtiger_rss_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_rss_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_rss_seq`
--

LOCK TABLES `vtiger_rss_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_rss_seq` DISABLE KEYS */;
INSERT INTO `vtiger_rss_seq` VALUES (2);
/*!40000 ALTER TABLE `vtiger_rss_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_sales_stage`
--

DROP TABLE IF EXISTS `vtiger_sales_stage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_sales_stage` (
  `sales_stage_id` int(19) NOT NULL AUTO_INCREMENT,
  `sales_stage` varchar(200) DEFAULT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sales_stage_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_sales_stage`
--

LOCK TABLES `vtiger_sales_stage` WRITE;
/*!40000 ALTER TABLE `vtiger_sales_stage` DISABLE KEYS */;
INSERT INTO `vtiger_sales_stage` VALUES (1,'Prospecting',1,150),(2,'Qualification',1,151),(3,'Needs Analysis',1,152),(4,'Value Proposition',1,153),(5,'Id. Decision Makers',1,154),(6,'Perception Analysis',1,155),(7,'Proposal/Price Quote',1,156),(8,'Negotiation/Review',1,157),(9,'Closed Won',0,158),(10,'Closed Lost',0,159);
/*!40000 ALTER TABLE `vtiger_sales_stage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_sales_stage_seq`
--

DROP TABLE IF EXISTS `vtiger_sales_stage_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_sales_stage_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_sales_stage_seq`
--

LOCK TABLES `vtiger_sales_stage_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_sales_stage_seq` DISABLE KEYS */;
INSERT INTO `vtiger_sales_stage_seq` VALUES (10);
/*!40000 ALTER TABLE `vtiger_sales_stage_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_salesmanactivityrel`
--

DROP TABLE IF EXISTS `vtiger_salesmanactivityrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_salesmanactivityrel` (
  `smid` int(19) NOT NULL DEFAULT '0',
  `activityid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`smid`,`activityid`),
  KEY `salesmanactivityrel_activityid_idx` (`activityid`),
  KEY `salesmanactivityrel_smid_idx` (`smid`),
  CONSTRAINT `fk_2_vtiger_salesmanactivityrel` FOREIGN KEY (`smid`) REFERENCES `vtiger_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_salesmanactivityrel`
--

LOCK TABLES `vtiger_salesmanactivityrel` WRITE;
/*!40000 ALTER TABLE `vtiger_salesmanactivityrel` DISABLE KEYS */;
INSERT INTO `vtiger_salesmanactivityrel` VALUES (1,116),(1,117),(1,118),(1,119),(1,120),(1,121),(1,145);
/*!40000 ALTER TABLE `vtiger_salesmanactivityrel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_salesmanattachmentsrel`
--

DROP TABLE IF EXISTS `vtiger_salesmanattachmentsrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_salesmanattachmentsrel` (
  `smid` int(19) NOT NULL DEFAULT '0',
  `attachmentsid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`smid`,`attachmentsid`),
  KEY `salesmanattachmentsrel_smid_idx` (`smid`),
  KEY `salesmanattachmentsrel_attachmentsid_idx` (`attachmentsid`),
  CONSTRAINT `fk_2_vtiger_salesmanattachmentsrel` FOREIGN KEY (`attachmentsid`) REFERENCES `vtiger_attachments` (`attachmentsid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_salesmanattachmentsrel`
--

LOCK TABLES `vtiger_salesmanattachmentsrel` WRITE;
/*!40000 ALTER TABLE `vtiger_salesmanattachmentsrel` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_salesmanattachmentsrel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_salesorder`
--

DROP TABLE IF EXISTS `vtiger_salesorder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_salesorder` (
  `salesorderid` int(19) NOT NULL DEFAULT '0',
  `subject` varchar(100) DEFAULT NULL,
  `potentialid` int(19) DEFAULT NULL,
  `customerno` varchar(100) DEFAULT NULL,
  `salesorder_no` varchar(100) DEFAULT NULL,
  `quoteid` int(19) DEFAULT NULL,
  `vendorterms` varchar(100) DEFAULT NULL,
  `contactid` int(19) DEFAULT NULL,
  `vendorid` int(19) DEFAULT NULL,
  `duedate` date DEFAULT NULL,
  `carrier` varchar(200) DEFAULT NULL,
  `pending` varchar(200) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `adjustment` decimal(28,6) DEFAULT NULL,
  `salescommission` decimal(25,3) DEFAULT NULL,
  `exciseduty` decimal(25,3) DEFAULT NULL,
  `total` decimal(28,6) DEFAULT NULL,
  `subtotal` decimal(28,6) DEFAULT NULL,
  `taxtype` varchar(25) DEFAULT NULL,
  `discount_percent` decimal(25,3) DEFAULT NULL,
  `discount_amount` decimal(28,6) DEFAULT NULL,
  `s_h_amount` decimal(28,6) DEFAULT NULL,
  `accountid` int(19) DEFAULT NULL,
  `terms_conditions` text,
  `purchaseorder` varchar(200) DEFAULT NULL,
  `sostatus` varchar(200) DEFAULT NULL,
  `currency_id` int(19) NOT NULL DEFAULT '1',
  `conversion_rate` decimal(10,3) NOT NULL DEFAULT '1.000',
  `enable_recurring` int(11) DEFAULT '0',
  `tandc` int(11) NOT NULL,
  PRIMARY KEY (`salesorderid`),
  KEY `salesorder_vendorid_idx` (`vendorid`),
  KEY `salesorder_contactid_idx` (`contactid`),
  KEY `salesorder_accountid_idx` (`accountid`),
  KEY `salesorder_quoteid_idx` (`quoteid`),
  KEY `salesorder_potentialid_idx` (`potentialid`),
  KEY `salesorder_subject_idx` (`subject`),
  CONSTRAINT `fk_3_vtiger_salesorder` FOREIGN KEY (`vendorid`) REFERENCES `vtiger_vendor` (`vendorid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_salesorder`
--

LOCK TABLES `vtiger_salesorder` WRITE;
/*!40000 ALTER TABLE `vtiger_salesorder` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_salesorder` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_salesordercf`
--

DROP TABLE IF EXISTS `vtiger_salesordercf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_salesordercf` (
  `salesorderid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`salesorderid`),
  CONSTRAINT `fk_1_vtiger_salesordercf` FOREIGN KEY (`salesorderid`) REFERENCES `vtiger_salesorder` (`salesorderid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_salesordercf`
--

LOCK TABLES `vtiger_salesordercf` WRITE;
/*!40000 ALTER TABLE `vtiger_salesordercf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_salesordercf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_salutationtype`
--

DROP TABLE IF EXISTS `vtiger_salutationtype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_salutationtype` (
  `salutationid` int(19) NOT NULL AUTO_INCREMENT,
  `salutationtype` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`salutationid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_salutationtype`
--

LOCK TABLES `vtiger_salutationtype` WRITE;
/*!40000 ALTER TABLE `vtiger_salutationtype` DISABLE KEYS */;
INSERT INTO `vtiger_salutationtype` VALUES (1,'--None--',1,160),(2,'Mr.',1,161),(3,'Ms.',1,162),(4,'Mrs.',1,163),(5,'Dr.',1,164),(6,'Prof.',1,165);
/*!40000 ALTER TABLE `vtiger_salutationtype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_salutationtype_seq`
--

DROP TABLE IF EXISTS `vtiger_salutationtype_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_salutationtype_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_salutationtype_seq`
--

LOCK TABLES `vtiger_salutationtype_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_salutationtype_seq` DISABLE KEYS */;
INSERT INTO `vtiger_salutationtype_seq` VALUES (6);
/*!40000 ALTER TABLE `vtiger_salutationtype_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_scheduled_reports`
--

DROP TABLE IF EXISTS `vtiger_scheduled_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_scheduled_reports` (
  `reportid` int(11) NOT NULL,
  `recipients` text,
  `schedule` text,
  `format` varchar(10) DEFAULT NULL,
  `next_trigger_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`reportid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_scheduled_reports`
--

LOCK TABLES `vtiger_scheduled_reports` WRITE;
/*!40000 ALTER TABLE `vtiger_scheduled_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_scheduled_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_seactivityrel`
--

DROP TABLE IF EXISTS `vtiger_seactivityrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_seactivityrel` (
  `crmid` int(19) NOT NULL,
  `activityid` int(19) NOT NULL,
  PRIMARY KEY (`crmid`,`activityid`),
  KEY `seactivityrel_activityid_idx` (`activityid`),
  KEY `seactivityrel_crmid_idx` (`crmid`),
  CONSTRAINT `fk_2_vtiger_seactivityrel` FOREIGN KEY (`crmid`) REFERENCES `vtiger_crmentity` (`crmid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_seactivityrel`
--

LOCK TABLES `vtiger_seactivityrel` WRITE;
/*!40000 ALTER TABLE `vtiger_seactivityrel` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_seactivityrel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_seactivityrel_seq`
--

DROP TABLE IF EXISTS `vtiger_seactivityrel_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_seactivityrel_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_seactivityrel_seq`
--

LOCK TABLES `vtiger_seactivityrel_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_seactivityrel_seq` DISABLE KEYS */;
INSERT INTO `vtiger_seactivityrel_seq` VALUES (1);
/*!40000 ALTER TABLE `vtiger_seactivityrel_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_seattachmentsrel`
--

DROP TABLE IF EXISTS `vtiger_seattachmentsrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_seattachmentsrel` (
  `crmid` int(19) NOT NULL DEFAULT '0',
  `attachmentsid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`crmid`,`attachmentsid`),
  KEY `seattachmentsrel_attachmentsid_idx` (`attachmentsid`),
  KEY `seattachmentsrel_crmid_idx` (`crmid`),
  KEY `seattachmentsrel_attachmentsid_crmid_idx` (`attachmentsid`,`crmid`),
  CONSTRAINT `fk_2_vtiger_seattachmentsrel` FOREIGN KEY (`crmid`) REFERENCES `vtiger_crmentity` (`crmid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_seattachmentsrel`
--

LOCK TABLES `vtiger_seattachmentsrel` WRITE;
/*!40000 ALTER TABLE `vtiger_seattachmentsrel` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_seattachmentsrel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_selectcolumn`
--

DROP TABLE IF EXISTS `vtiger_selectcolumn`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_selectcolumn` (
  `queryid` int(19) NOT NULL,
  `columnindex` int(11) NOT NULL DEFAULT '0',
  `columnname` varchar(250) DEFAULT '',
  PRIMARY KEY (`queryid`,`columnindex`),
  KEY `selectcolumn_queryid_idx` (`queryid`),
  CONSTRAINT `fk_1_vtiger_selectcolumn` FOREIGN KEY (`queryid`) REFERENCES `vtiger_selectquery` (`queryid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_selectcolumn`
--

LOCK TABLES `vtiger_selectcolumn` WRITE;
/*!40000 ALTER TABLE `vtiger_selectcolumn` DISABLE KEYS */;
INSERT INTO `vtiger_selectcolumn` VALUES (1,0,'vtiger_contactdetails:firstname:Contacts_First_Name:firstname:V'),(1,1,'vtiger_contactdetails:lastname:Contacts_Last_Name:lastname:V'),(1,2,'vtiger_contactsubdetails:leadsource:Contacts_Lead_Source:leadsource:V'),(1,3,'vtiger_contactdetails:accountid:Contacts_Account_Name:account_id:V'),(1,4,'vtiger_account:industry:Accounts_industry:industry:V'),(1,5,'vtiger_contactdetails:email:Contacts_Email:email:E'),(2,0,'vtiger_contactdetails:firstname:Contacts_First_Name:firstname:V'),(2,1,'vtiger_contactdetails:lastname:Contacts_Last_Name:lastname:V'),(2,2,'vtiger_contactsubdetails:leadsource:Contacts_Lead_Source:leadsource:V'),(2,3,'vtiger_contactdetails:accountid:Contacts_Account_Name:account_id:V'),(2,4,'vtiger_account:industry:Accounts_industry:industry:V'),(2,5,'vtiger_contactdetails:email:Contacts_Email:email:E'),(3,0,'vtiger_contactdetails:firstname:Contacts_First_Name:firstname:V'),(3,1,'vtiger_contactdetails:lastname:Contacts_Last_Name:lastname:V'),(3,2,'vtiger_contactdetails:accountid:Contacts_Account_Name:account_id:V'),(3,3,'vtiger_contactdetails:email:Contacts_Email:email:E'),(3,4,'vtiger_potential:potentialname:Potentials_Potential_Name:potentialname:V'),(3,5,'vtiger_potential:sales_stage:Potentials_Sales_Stage:sales_stage:V'),(4,0,'vtiger_leaddetails:firstname:Leads_First_Name:firstname:V'),(4,1,'vtiger_leaddetails:lastname:Leads_Last_Name:lastname:V'),(4,2,'vtiger_leaddetails:company:Leads_Company:company:V'),(4,3,'vtiger_leaddetails:email:Leads_Email:email:E'),(4,4,'vtiger_leaddetails:leadsource:Leads_Lead_Source:leadsource:V'),(5,0,'vtiger_leaddetails:firstname:Leads_First_Name:firstname:V'),(5,1,'vtiger_leaddetails:lastname:Leads_Last_Name:lastname:V'),(5,2,'vtiger_leaddetails:company:Leads_Company:company:V'),(5,3,'vtiger_leaddetails:email:Leads_Email:email:E'),(5,4,'vtiger_leaddetails:leadsource:Leads_Lead_Source:leadsource:V'),(5,5,'vtiger_leaddetails:leadstatus:Leads_Lead_Status:leadstatus:V'),(6,0,'vtiger_potential:potentialname:Potentials_Potential_Name:potentialname:V'),(6,1,'vtiger_potential:amount:Potentials_Amount:amount:N'),(6,2,'vtiger_potential:potentialtype:Potentials_Type:opportunity_type:V'),(6,3,'vtiger_potential:leadsource:Potentials_Lead_Source:leadsource:V'),(6,4,'vtiger_potential:sales_stage:Potentials_Sales_Stage:sales_stage:V'),(7,0,'vtiger_potential:potentialname:Potentials_Potential_Name:potentialname:V'),(7,1,'vtiger_potential:amount:Potentials_Amount:amount:N'),(7,2,'vtiger_potential:potentialtype:Potentials_Type:opportunity_type:V'),(7,3,'vtiger_potential:leadsource:Potentials_Lead_Source:leadsource:V'),(7,4,'vtiger_potential:sales_stage:Potentials_Sales_Stage:sales_stage:V'),(8,0,'vtiger_activity:subject:Calendar_Subject:subject:V'),(8,1,'vtiger_contactdetailsCalendar:lastname:Calendar_Contact_Name:contact_id:I'),(8,2,'vtiger_activity:status:Calendar_Status:taskstatus:V'),(8,3,'vtiger_activity:priority:Calendar_Priority:taskpriority:V'),(8,4,'vtiger_usersCalendar:user_name:Calendar_Assigned_To:assigned_user_id:V'),(9,0,'vtiger_activity:subject:Calendar_Subject:subject:V'),(9,1,'vtiger_contactdetailsCalendar:lastname:Calendar_Contact_Name:contact_id:I'),(9,2,'vtiger_activity:status:Calendar_Status:taskstatus:V'),(9,3,'vtiger_activity:priority:Calendar_Priority:taskpriority:V'),(9,4,'vtiger_usersCalendar:user_name:Calendar_Assigned_To:assigned_user_id:V'),(10,0,'vtiger_troubletickets:title:HelpDesk_Title:ticket_title:V'),(10,1,'vtiger_troubletickets:status:HelpDesk_Status:ticketstatus:V'),(10,2,'vtiger_products:productname:Products_Product_Name:productname:V'),(10,3,'vtiger_products:discontinued:Products_Product_Active:discontinued:V'),(10,4,'vtiger_products:productcategory:Products_Product_Category:productcategory:V'),(10,5,'vtiger_products:manufacturer:Products_Manufacturer:manufacturer:V'),(11,0,'vtiger_troubletickets:title:HelpDesk_Title:ticket_title:V'),(11,1,'vtiger_troubletickets:priority:HelpDesk_Priority:ticketpriorities:V'),(11,2,'vtiger_troubletickets:severity:HelpDesk_Severity:ticketseverities:V'),(11,3,'vtiger_troubletickets:status:HelpDesk_Status:ticketstatus:V'),(11,4,'vtiger_troubletickets:category:HelpDesk_Category:ticketcategories:V'),(11,5,'vtiger_usersHelpDesk:user_name:HelpDesk_Assigned_To:assigned_user_id:V'),(12,0,'vtiger_troubletickets:title:HelpDesk_Title:ticket_title:V'),(12,1,'vtiger_troubletickets:priority:HelpDesk_Priority:ticketpriorities:V'),(12,2,'vtiger_troubletickets:severity:HelpDesk_Severity:ticketseverities:V'),(12,3,'vtiger_troubletickets:status:HelpDesk_Status:ticketstatus:V'),(12,4,'vtiger_troubletickets:category:HelpDesk_Category:ticketcategories:V'),(12,5,'vtiger_usersHelpDesk:user_name:HelpDesk_Assigned_To:assigned_user_id:V'),(13,0,'vtiger_products:productname:Products_Product_Name:productname:V'),(13,1,'vtiger_products:productcode:Products_Product_Code:productcode:V'),(13,2,'vtiger_products:discontinued:Products_Product_Active:discontinued:V'),(13,3,'vtiger_products:productcategory:Products_Product_Category:productcategory:V'),(13,4,'vtiger_products:website:Products_Website:website:V'),(13,5,'vtiger_vendorRelProducts:vendorname:Products_Vendor_Name:vendor_id:I'),(13,6,'vtiger_products:mfr_part_no:Products_Mfr_PartNo:mfr_part_no:V'),(14,0,'vtiger_products:productname:Products_Product_Name:productname:V'),(14,1,'vtiger_products:manufacturer:Products_Manufacturer:manufacturer:V'),(14,2,'vtiger_products:productcategory:Products_Product_Category:productcategory:V'),(14,3,'vtiger_contactdetails:firstname:Contacts_First_Name:firstname:V'),(14,4,'vtiger_contactdetails:lastname:Contacts_Last_Name:lastname:V'),(14,5,'vtiger_contactsubdetails:leadsource:Contacts_Lead_Source:leadsource:V'),(15,0,'vtiger_quotes:subject:Quotes_Subject:subject:V'),(15,1,'vtiger_potentialRelQuotes:potentialname:Quotes_Potential_Name:potential_id:I'),(15,2,'vtiger_quotes:quotestage:Quotes_Quote_Stage:quotestage:V'),(15,3,'vtiger_quotes:contactid:Quotes_Contact_Name:contact_id:V'),(15,4,'vtiger_usersRel1:user_name:Quotes_Inventory_Manager:assigned_user_id1:I'),(15,5,'vtiger_accountQuotes:accountname:Quotes_Account_Name:account_id:I'),(16,0,'vtiger_quotes:subject:Quotes_Subject:subject:V'),(16,1,'vtiger_potentialRelQuotes:potentialname:Quotes_Potential_Name:potential_id:I'),(16,2,'vtiger_quotes:quotestage:Quotes_Quote_Stage:quotestage:V'),(16,3,'vtiger_quotes:contactid:Quotes_Contact_Name:contact_id:V'),(16,4,'vtiger_usersRel1:user_name:Quotes_Inventory_Manager:assigned_user_id1:I'),(16,5,'vtiger_accountQuotes:accountname:Quotes_Account_Name:account_id:I'),(16,6,'vtiger_quotes:carrier:Quotes_Carrier:carrier:V'),(16,7,'vtiger_quotes:shipping:Quotes_Shipping:shipping:V'),(17,0,'vtiger_purchaseorder:subject:PurchaseOrder_Subject:subject:V'),(17,1,'vtiger_vendorRelPurchaseOrder:vendorname:PurchaseOrder_Vendor_Name:vendor_id:I'),(17,2,'vtiger_purchaseorder:tracking_no:PurchaseOrder_Tracking_Number:tracking_no:V'),(17,3,'vtiger_contactdetails:firstname:Contacts_First_Name:firstname:V'),(17,4,'vtiger_contactdetails:lastname:Contacts_Last_Name:lastname:V'),(17,5,'vtiger_contactsubdetails:leadsource:Contacts_Lead_Source:leadsource:V'),(17,6,'vtiger_contactdetails:email:Contacts_Email:email:E'),(18,0,'vtiger_purchaseorder:subject:PurchaseOrder_Subject:subject:V'),(18,1,'vtiger_vendorRelPurchaseOrder:vendorname:PurchaseOrder_Vendor_Name:vendor_id:I'),(18,2,'vtiger_purchaseorder:requisition_no:PurchaseOrder_Requisition_No:requisition_no:V'),(18,3,'vtiger_purchaseorder:tracking_no:PurchaseOrder_Tracking_Number:tracking_no:V'),(18,4,'vtiger_contactdetailsPurchaseOrder:lastname:PurchaseOrder_Contact_Name:contact_id:I'),(18,5,'vtiger_purchaseorder:carrier:PurchaseOrder_Carrier:carrier:V'),(18,6,'vtiger_purchaseorder:salescommission:PurchaseOrder_Sales_Commission:salescommission:N'),(18,7,'vtiger_purchaseorder:exciseduty:PurchaseOrder_Excise_Duty:exciseduty:N'),(18,8,'vtiger_usersPurchaseOrder:user_name:PurchaseOrder_Assigned_To:assigned_user_id:V'),(19,0,'vtiger_invoice:subject:Invoice_Subject:subject:V'),(19,1,'vtiger_invoice:salesorderid:Invoice_Sales_Order:salesorder_id:I'),(19,2,'vtiger_invoice:customerno:Invoice_Customer_No:customerno:V'),(19,3,'vtiger_invoice:exciseduty:Invoice_Excise_Duty:exciseduty:N'),(19,4,'vtiger_invoice:salescommission:Invoice_Sales_Commission:salescommission:N'),(19,5,'vtiger_accountInvoice:accountname:Invoice_Account_Name:account_id:I'),(20,0,'vtiger_salesorder:subject:SalesOrder_Subject:subject:V'),(20,1,'vtiger_quotesSalesOrder:subject:SalesOrder_Quote_Name:quote_id:I'),(20,2,'vtiger_contactdetailsSalesOrder:lastname:SalesOrder_Contact_Name:contact_id:I'),(20,3,'vtiger_salesorder:duedate:SalesOrder_Due_Date:duedate:D'),(20,4,'vtiger_salesorder:carrier:SalesOrder_Carrier:carrier:V'),(20,5,'vtiger_salesorder:sostatus:SalesOrder_Status:sostatus:V'),(20,6,'vtiger_accountSalesOrder:accountname:SalesOrder_Account_Name:account_id:I'),(20,7,'vtiger_salesorder:salescommission:SalesOrder_Sales_Commission:salescommission:N'),(20,8,'vtiger_salesorder:exciseduty:SalesOrder_Excise_Duty:exciseduty:N'),(20,9,'vtiger_usersSalesOrder:user_name:SalesOrder_Assigned_To:assigned_user_id:V'),(21,0,'vtiger_campaign:campaignname:Campaigns_Campaign_Name:campaignname:V'),(21,1,'vtiger_campaign:campaigntype:Campaigns_Campaign_Type:campaigntype:V'),(21,2,'vtiger_campaign:targetaudience:Campaigns_Target_Audience:targetaudience:V'),(21,3,'vtiger_campaign:budgetcost:Campaigns_Budget_Cost:budgetcost:I'),(21,4,'vtiger_campaign:actualcost:Campaigns_Actual_Cost:actualcost:I'),(21,5,'vtiger_campaign:expectedrevenue:Campaigns_Expected_Revenue:expectedrevenue:I'),(21,6,'vtiger_campaign:expectedsalescount:Campaigns_Expected_Sales_Count:expectedsalescount:N'),(21,7,'vtiger_campaign:actualsalescount:Campaigns_Actual_Sales_Count:actualsalescount:N'),(21,8,'vtiger_usersCampaigns:user_name:Campaigns_Assigned_To:assigned_user_id:V'),(22,0,'vtiger_contactdetails:lastname:Contacts_Last_Name:lastname:V'),(22,1,'vtiger_contactdetails:email:Contacts_Email:email:E'),(22,2,'vtiger_activity:subject:Emails_Subject:subject:V'),(22,3,'vtiger_email_track:access_count:Emails_Access_Count:access_count:I'),(23,0,'vtiger_account:accountname:Accounts_Account_Name:accountname:V'),(23,1,'vtiger_account:phone:Accounts_Phone:phone:V'),(23,2,'vtiger_account:email1:Accounts_Email:email1:E'),(23,3,'vtiger_activity:subject:Emails_Subject:subject:V'),(23,4,'vtiger_email_track:access_count:Emails_Access_Count:access_count:I'),(24,0,'vtiger_leaddetails:lastname:Leads_Last_Name:lastname:V'),(24,1,'vtiger_leaddetails:company:Leads_Company:company:V'),(24,2,'vtiger_leaddetails:email:Leads_Email:email:E'),(24,3,'vtiger_activity:subject:Emails_Subject:subject:V'),(24,4,'vtiger_email_track:access_count:Emails_Access_Count:access_count:I'),(25,0,'vtiger_vendor:vendorname:Vendors_Vendor_Name:vendorname:V'),(25,1,'vtiger_vendor:glacct:Vendors_GL_Account:glacct:V'),(25,2,'vtiger_vendor:email:Vendors_Email:email:E'),(25,3,'vtiger_activity:subject:Emails_Subject:subject:V'),(25,4,'vtiger_email_track:access_count:Emails_Access_Count:access_count:I');
/*!40000 ALTER TABLE `vtiger_selectcolumn` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_selectquery`
--

DROP TABLE IF EXISTS `vtiger_selectquery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_selectquery` (
  `queryid` int(19) NOT NULL,
  `startindex` int(19) DEFAULT '0',
  `numofobjects` int(19) DEFAULT '0',
  PRIMARY KEY (`queryid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_selectquery`
--

LOCK TABLES `vtiger_selectquery` WRITE;
/*!40000 ALTER TABLE `vtiger_selectquery` DISABLE KEYS */;
INSERT INTO `vtiger_selectquery` VALUES (1,0,0),(2,0,0),(3,0,0),(4,0,0),(5,0,0),(6,0,0),(7,0,0),(8,0,0),(9,0,0),(10,0,0),(11,0,0),(12,0,0),(13,0,0),(14,0,0),(15,0,0),(16,0,0),(17,0,0),(18,0,0),(19,0,0),(20,0,0),(21,0,0),(22,0,0),(23,0,0),(24,0,0),(25,0,0);
/*!40000 ALTER TABLE `vtiger_selectquery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_selectquery_seq`
--

DROP TABLE IF EXISTS `vtiger_selectquery_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_selectquery_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_selectquery_seq`
--

LOCK TABLES `vtiger_selectquery_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_selectquery_seq` DISABLE KEYS */;
INSERT INTO `vtiger_selectquery_seq` VALUES (25);
/*!40000 ALTER TABLE `vtiger_selectquery_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_senotesrel`
--

DROP TABLE IF EXISTS `vtiger_senotesrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_senotesrel` (
  `crmid` int(19) NOT NULL DEFAULT '0',
  `notesid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`crmid`,`notesid`),
  KEY `senotesrel_notesid_idx` (`notesid`),
  KEY `senotesrel_crmid_idx` (`crmid`),
  CONSTRAINT `fk_2_vtiger_senotesrel` FOREIGN KEY (`notesid`) REFERENCES `vtiger_notes` (`notesid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_senotesrel`
--

LOCK TABLES `vtiger_senotesrel` WRITE;
/*!40000 ALTER TABLE `vtiger_senotesrel` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_senotesrel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_seproductsrel`
--

DROP TABLE IF EXISTS `vtiger_seproductsrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_seproductsrel` (
  `crmid` int(19) NOT NULL DEFAULT '0',
  `productid` int(19) NOT NULL DEFAULT '0',
  `setype` varchar(30) NOT NULL,
  PRIMARY KEY (`crmid`,`productid`),
  KEY `seproductsrel_productid_idx` (`productid`),
  KEY `seproductrel_crmid_idx` (`crmid`),
  CONSTRAINT `fk_2_vtiger_seproductsrel` FOREIGN KEY (`productid`) REFERENCES `vtiger_products` (`productid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_seproductsrel`
--

LOCK TABLES `vtiger_seproductsrel` WRITE;
/*!40000 ALTER TABLE `vtiger_seproductsrel` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_seproductsrel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_service`
--

DROP TABLE IF EXISTS `vtiger_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_service` (
  `serviceid` int(11) NOT NULL,
  `service_no` varchar(100) NOT NULL,
  `servicename` varchar(50) NOT NULL,
  `servicecategory` varchar(200) DEFAULT NULL,
  `qty_per_unit` decimal(11,2) DEFAULT '0.00',
  `unit_price` decimal(29,6) DEFAULT NULL,
  `sales_start_date` date DEFAULT NULL,
  `sales_end_date` date DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `discontinued` int(1) NOT NULL DEFAULT '0',
  `service_usageunit` varchar(200) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  `taxclass` varchar(200) DEFAULT NULL,
  `currency_id` int(19) NOT NULL DEFAULT '1',
  `commissionrate` decimal(7,3) DEFAULT NULL,
  `cost_price` decimal(28,6) DEFAULT NULL,
  `divisible` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`serviceid`),
  CONSTRAINT `fk_1_vtiger_service` FOREIGN KEY (`serviceid`) REFERENCES `vtiger_crmentity` (`crmid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_service`
--

LOCK TABLES `vtiger_service` WRITE;
/*!40000 ALTER TABLE `vtiger_service` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_service_usageunit`
--

DROP TABLE IF EXISTS `vtiger_service_usageunit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_service_usageunit` (
  `service_usageunitid` int(11) NOT NULL AUTO_INCREMENT,
  `service_usageunit` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`service_usageunitid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_service_usageunit`
--

LOCK TABLES `vtiger_service_usageunit` WRITE;
/*!40000 ALTER TABLE `vtiger_service_usageunit` DISABLE KEYS */;
INSERT INTO `vtiger_service_usageunit` VALUES (1,'Hours',1,226),(2,'Days',1,227),(3,'Incidents',1,228);
/*!40000 ALTER TABLE `vtiger_service_usageunit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_service_usageunit_seq`
--

DROP TABLE IF EXISTS `vtiger_service_usageunit_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_service_usageunit_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_service_usageunit_seq`
--

LOCK TABLES `vtiger_service_usageunit_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_service_usageunit_seq` DISABLE KEYS */;
INSERT INTO `vtiger_service_usageunit_seq` VALUES (3);
/*!40000 ALTER TABLE `vtiger_service_usageunit_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_servicecategory`
--

DROP TABLE IF EXISTS `vtiger_servicecategory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_servicecategory` (
  `servicecategoryid` int(11) NOT NULL AUTO_INCREMENT,
  `servicecategory` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`servicecategoryid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_servicecategory`
--

LOCK TABLES `vtiger_servicecategory` WRITE;
/*!40000 ALTER TABLE `vtiger_servicecategory` DISABLE KEYS */;
INSERT INTO `vtiger_servicecategory` VALUES (1,'--None--',1,229),(2,'Support',1,230),(3,'Installation',1,231),(4,'Migration',1,232),(5,'Customization',1,233),(6,'Training',1,234);
/*!40000 ALTER TABLE `vtiger_servicecategory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_servicecategory_seq`
--

DROP TABLE IF EXISTS `vtiger_servicecategory_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_servicecategory_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_servicecategory_seq`
--

LOCK TABLES `vtiger_servicecategory_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_servicecategory_seq` DISABLE KEYS */;
INSERT INTO `vtiger_servicecategory_seq` VALUES (6);
/*!40000 ALTER TABLE `vtiger_servicecategory_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_servicecf`
--

DROP TABLE IF EXISTS `vtiger_servicecf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_servicecf` (
  `serviceid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`serviceid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_servicecf`
--

LOCK TABLES `vtiger_servicecf` WRITE;
/*!40000 ALTER TABLE `vtiger_servicecf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_servicecf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_servicecontracts`
--

DROP TABLE IF EXISTS `vtiger_servicecontracts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_servicecontracts` (
  `servicecontractsid` int(11) NOT NULL DEFAULT '0',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `sc_related_to` int(11) DEFAULT NULL,
  `tracking_unit` varchar(100) DEFAULT NULL,
  `total_units` decimal(28,6) DEFAULT NULL,
  `used_units` decimal(28,6) DEFAULT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `planned_duration` varchar(256) DEFAULT NULL,
  `actual_duration` varchar(256) DEFAULT NULL,
  `contract_status` varchar(200) DEFAULT NULL,
  `priority` varchar(200) DEFAULT NULL,
  `contract_type` varchar(200) DEFAULT NULL,
  `progress` decimal(5,2) DEFAULT NULL,
  `contract_no` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`servicecontractsid`),
  KEY `sc_related_to_idx` (`sc_related_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_servicecontracts`
--

LOCK TABLES `vtiger_servicecontracts` WRITE;
/*!40000 ALTER TABLE `vtiger_servicecontracts` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_servicecontracts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_servicecontractscf`
--

DROP TABLE IF EXISTS `vtiger_servicecontractscf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_servicecontractscf` (
  `servicecontractsid` int(11) NOT NULL,
  PRIMARY KEY (`servicecontractsid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_servicecontractscf`
--

LOCK TABLES `vtiger_servicecontractscf` WRITE;
/*!40000 ALTER TABLE `vtiger_servicecontractscf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_servicecontractscf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_seticketsrel`
--

DROP TABLE IF EXISTS `vtiger_seticketsrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_seticketsrel` (
  `crmid` int(19) NOT NULL DEFAULT '0',
  `ticketid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`crmid`,`ticketid`),
  KEY `seticketsrel_crmid_idx` (`crmid`),
  KEY `seticketsrel_ticketid_idx` (`ticketid`),
  CONSTRAINT `fk_2_vtiger_seticketsrel` FOREIGN KEY (`ticketid`) REFERENCES `vtiger_troubletickets` (`ticketid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_seticketsrel`
--

LOCK TABLES `vtiger_seticketsrel` WRITE;
/*!40000 ALTER TABLE `vtiger_seticketsrel` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_seticketsrel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_settings_blocks`
--

DROP TABLE IF EXISTS `vtiger_settings_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_settings_blocks` (
  `blockid` int(19) NOT NULL,
  `label` varchar(250) DEFAULT NULL,
  `sequence` int(19) DEFAULT NULL,
  PRIMARY KEY (`blockid`),
  KEY `label` (`label`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_settings_blocks`
--

LOCK TABLES `vtiger_settings_blocks` WRITE;
/*!40000 ALTER TABLE `vtiger_settings_blocks` DISABLE KEYS */;
INSERT INTO `vtiger_settings_blocks` VALUES (1,'LBL_MODULE_MANAGER',1),(2,'LBL_USER_MANAGEMENT',2),(3,'LBL_STUDIO',3),(4,'LBL_COMMUNICATION_TEMPLATES',4),(5,'LBL_OTHER_SETTINGS',5);
/*!40000 ALTER TABLE `vtiger_settings_blocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_settings_blocks_seq`
--

DROP TABLE IF EXISTS `vtiger_settings_blocks_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_settings_blocks_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_settings_blocks_seq`
--

LOCK TABLES `vtiger_settings_blocks_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_settings_blocks_seq` DISABLE KEYS */;
INSERT INTO `vtiger_settings_blocks_seq` VALUES (5);
/*!40000 ALTER TABLE `vtiger_settings_blocks_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_settings_field`
--

DROP TABLE IF EXISTS `vtiger_settings_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_settings_field` (
  `fieldid` int(19) NOT NULL,
  `blockid` int(19) DEFAULT NULL,
  `name` varchar(250) DEFAULT NULL,
  `iconpath` varchar(300) DEFAULT NULL,
  `description` text,
  `linkto` text,
  `sequence` int(19) DEFAULT NULL,
  `active` int(19) DEFAULT '0',
  PRIMARY KEY (`fieldid`),
  KEY `fk_1_vtiger_settings_field` (`blockid`),
  CONSTRAINT `fk_1_vtiger_settings_field` FOREIGN KEY (`blockid`) REFERENCES `vtiger_settings_blocks` (`blockid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_settings_field`
--

LOCK TABLES `vtiger_settings_field` WRITE;
/*!40000 ALTER TABLE `vtiger_settings_field` DISABLE KEYS */;
INSERT INTO `vtiger_settings_field` VALUES (1,2,'LBL_USERS','ico-users.gif','LBL_USER_DESCRIPTION','index.php?module=Users&action=index&parenttab=Settings',1,0),(2,2,'LBL_ROLES','ico-roles.gif','LBL_ROLE_DESCRIPTION','index.php?module=Settings&action=listroles&parenttab=Settings',2,0),(3,2,'LBL_PROFILES','ico-profile.gif','LBL_PROFILE_DESCRIPTION','index.php?module=Settings&action=ListProfiles&parenttab=Settings',3,0),(4,2,'USERGROUPLIST','ico-groups.gif','LBL_GROUP_DESCRIPTION','index.php?module=Settings&action=listgroups&parenttab=Settings',4,0),(5,2,'LBL_SHARING_ACCESS','shareaccess.gif','LBL_SHARING_ACCESS_DESCRIPTION','index.php?module=Settings&action=OrgSharingDetailView&parenttab=Settings',5,0),(6,2,'LBL_FIELDS_ACCESS','orgshar.gif','LBL_SHARING_FIELDS_DESCRIPTION','index.php?module=Settings&action=DefaultFieldPermissions&parenttab=Settings',6,0),(9,3,'VTLIB_LBL_MODULE_MANAGER','vtlib_modmng.gif','VTLIB_LBL_MODULE_MANAGER_DESCRIPTION','index.php?module=Settings&action=ModuleManager&parenttab=Settings',1,0),(10,3,'LBL_PICKLIST_EDITOR','picklist.gif','LBL_PICKLIST_DESCRIPTION','index.php?module=PickList&action=PickList&parenttab=Settings',2,0),(11,3,'LBL_PICKLIST_DEPENDENCY_SETUP','picklistdependency.gif','LBL_PICKLIST_DEPENDENCY_DESCRIPTION','index.php?module=PickList&action=PickListDependencySetup&parenttab=Settings',3,0),(12,4,'EMAILTEMPLATES','ViewTemplate.gif','LBL_EMAIL_TEMPLATE_DESCRIPTION','index.php?module=Settings&action=listemailtemplates&parenttab=Settings',4,0),(13,4,'LBL_MAIL_MERGE','mailmarge.gif','LBL_MAIL_MERGE_DESCRIPTION','index.php?module=Settings&action=listwordtemplates&parenttab=Settings',1,0),(16,4,'LBL_COMPANY_DETAILS','company.gif','LBL_COMPANY_DESCRIPTION','index.php?module=Settings&action=OrganizationConfig&parenttab=Settings',4,0),(17,5,'LBL_MAIL_SERVER_SETTINGS','ogmailserver.gif','LBL_MAIL_SERVER_DESCRIPTION','index.php?module=Settings&action=EmailConfig&parenttab=Settings',5,0),(19,5,'LBL_CURRENCY_SETTINGS','currency.gif','LBL_CURRENCY_DESCRIPTION','index.php?module=Settings&action=CurrencyListView&parenttab=Settings',1,0),(20,5,'LBL_TAX_SETTINGS','taxConfiguration.gif','LBL_TAX_DESCRIPTION','index.php?module=Settings&action=TaxConfig&parenttab=Settings',2,0),(22,5,'LBL_PROXY_SETTINGS','proxy.gif','LBL_PROXY_DESCRIPTION','index.php?module=Settings&action=ProxyServerConfig&parenttab=Settings',4,1),(24,5,'LBL_DEFAULT_MODULE_VIEW','set-IcoTwoTabConfig.gif','LBL_DEFAULT_MODULE_VIEW_DESC','index.php?module=Settings&action=DefModuleView&parenttab=Settings',6,1),(26,5,'LBL_CUSTOMIZE_MODENT_NUMBER','settingsInvNumber.gif','LBL_CUSTOMIZE_MODENT_NUMBER_DESCRIPTION','index.php?module=Settings&action=CustomModEntityNo&parenttab=Settings',8,0),(27,5,'LBL_MAIL_SCANNER','mailScanner.gif','LBL_MAIL_SCANNER_DESCRIPTION','index.php?module=Settings&action=MailScanner&parenttab=Settings',9,0),(28,5,'LBL_LIST_WORKFLOWS','settingsWorkflow.png','LBL_LIST_WORKFLOWS_DESCRIPTION','index.php?module=com_vtiger_workflow&action=workflowlist&parenttab=Settings',10,0),(29,3,'LBL_MENU_EDITOR','menueditor.png','LBL_MENU_DESC','index.php?module=Settings&action=MenuEditor&parenttab=Settings',11,1),(30,1,'LBL_WORKFLOW_LIST','settingsWorkflow.png','LBL_AVAILABLE_WORKLIST_LIST','index.php?module=com_vtiger_workflow&action=workflowlist',1,0),(31,5,'Configuration Editor','migrate.gif','Update configuration file of the application','index.php?module=ConfigEditor&action=index',11,1),(32,5,'ModTracker','set-IcoLoginHistory.gif','LBL_MODTRACKER_DESCRIPTION','index.php?module=ModTracker&action=BasicSettings&parenttab=Settings&formodule=ModTracker',12,1),(33,5,'Scheduler','Cron.png','Allows you to Configure Cron Task','index.php?module=CronTasks&action=ListCronJobs&parenttab=Settings',13,0),(35,1,'LBL_FIELDFORMULAS','modules/FieldFormulas/resources/FieldFormulas.png','LBL_FIELDFORMULAS_DESCRIPTION','index.php?module=FieldFormulas&action=index&parenttab=Settings',15,0),(36,1,'LBL_TOOLTIP_MANAGEMENT','quickview.png','LBL_TOOLTIP_MANAGEMENT_DESCRIPTION','index.php?module=Tooltip&action=QuickView&parenttab=Settings',NULL,0);
/*!40000 ALTER TABLE `vtiger_settings_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_settings_field_seq`
--

DROP TABLE IF EXISTS `vtiger_settings_field_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_settings_field_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_settings_field_seq`
--

LOCK TABLES `vtiger_settings_field_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_settings_field_seq` DISABLE KEYS */;
INSERT INTO `vtiger_settings_field_seq` VALUES (37);
/*!40000 ALTER TABLE `vtiger_settings_field_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_sharedcalendar`
--

DROP TABLE IF EXISTS `vtiger_sharedcalendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_sharedcalendar` (
  `userid` int(19) NOT NULL,
  `sharedid` int(19) NOT NULL,
  PRIMARY KEY (`userid`,`sharedid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_sharedcalendar`
--

LOCK TABLES `vtiger_sharedcalendar` WRITE;
/*!40000 ALTER TABLE `vtiger_sharedcalendar` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_sharedcalendar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_shippingtaxinfo`
--

DROP TABLE IF EXISTS `vtiger_shippingtaxinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_shippingtaxinfo` (
  `taxid` int(3) NOT NULL,
  `taxname` varchar(50) DEFAULT NULL,
  `taxlabel` varchar(50) DEFAULT NULL,
  `percentage` decimal(7,3) DEFAULT NULL,
  `deleted` int(1) DEFAULT NULL,
  PRIMARY KEY (`taxid`),
  KEY `shippingtaxinfo_taxname_idx` (`taxname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_shippingtaxinfo`
--

LOCK TABLES `vtiger_shippingtaxinfo` WRITE;
/*!40000 ALTER TABLE `vtiger_shippingtaxinfo` DISABLE KEYS */;
INSERT INTO `vtiger_shippingtaxinfo` VALUES (1,'shtax1','VAT',4.500,0),(2,'shtax2','Sales',10.000,0),(3,'shtax3','Service',12.500,0);
/*!40000 ALTER TABLE `vtiger_shippingtaxinfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_shippingtaxinfo_seq`
--

DROP TABLE IF EXISTS `vtiger_shippingtaxinfo_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_shippingtaxinfo_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_shippingtaxinfo_seq`
--

LOCK TABLES `vtiger_shippingtaxinfo_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_shippingtaxinfo_seq` DISABLE KEYS */;
INSERT INTO `vtiger_shippingtaxinfo_seq` VALUES (3);
/*!40000 ALTER TABLE `vtiger_shippingtaxinfo_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_smsnotifier`
--

DROP TABLE IF EXISTS `vtiger_smsnotifier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_smsnotifier` (
  `smsnotifierid` int(11) NOT NULL,
  `message` text,
  `status` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`smsnotifierid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_smsnotifier`
--

LOCK TABLES `vtiger_smsnotifier` WRITE;
/*!40000 ALTER TABLE `vtiger_smsnotifier` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_smsnotifier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_smsnotifier_servers`
--

DROP TABLE IF EXISTS `vtiger_smsnotifier_servers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_smsnotifier_servers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `password` varchar(255) DEFAULT NULL,
  `isactive` int(1) DEFAULT NULL,
  `providertype` varchar(50) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `parameters` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_smsnotifier_servers`
--

LOCK TABLES `vtiger_smsnotifier_servers` WRITE;
/*!40000 ALTER TABLE `vtiger_smsnotifier_servers` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_smsnotifier_servers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_smsnotifier_status`
--

DROP TABLE IF EXISTS `vtiger_smsnotifier_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_smsnotifier_status` (
  `smsnotifierid` int(11) DEFAULT NULL,
  `tonumber` varchar(20) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `smsmessageid` varchar(50) DEFAULT NULL,
  `needlookup` int(1) DEFAULT '1',
  `statusid` int(11) NOT NULL AUTO_INCREMENT,
  `statusmessage` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`statusid`),
  KEY `smsnotifierid` (`smsnotifierid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_smsnotifier_status`
--

LOCK TABLES `vtiger_smsnotifier_status` WRITE;
/*!40000 ALTER TABLE `vtiger_smsnotifier_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_smsnotifier_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_smsnotifiercf`
--

DROP TABLE IF EXISTS `vtiger_smsnotifiercf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_smsnotifiercf` (
  `smsnotifierid` int(11) NOT NULL,
  PRIMARY KEY (`smsnotifierid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_smsnotifiercf`
--

LOCK TABLES `vtiger_smsnotifiercf` WRITE;
/*!40000 ALTER TABLE `vtiger_smsnotifiercf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_smsnotifiercf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_soapservice`
--

DROP TABLE IF EXISTS `vtiger_soapservice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_soapservice` (
  `id` int(19) DEFAULT NULL,
  `type` varchar(25) DEFAULT NULL,
  `sessionid` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_soapservice`
--

LOCK TABLES `vtiger_soapservice` WRITE;
/*!40000 ALTER TABLE `vtiger_soapservice` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_soapservice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_sobillads`
--

DROP TABLE IF EXISTS `vtiger_sobillads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_sobillads` (
  `sobilladdressid` int(19) NOT NULL DEFAULT '0',
  `bill_city` varchar(30) DEFAULT NULL,
  `bill_code` varchar(30) DEFAULT NULL,
  `bill_country` varchar(30) DEFAULT NULL,
  `bill_state` varchar(30) DEFAULT NULL,
  `bill_street` varchar(250) DEFAULT NULL,
  `bill_pobox` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`sobilladdressid`),
  CONSTRAINT `fk_1_vtiger_sobillads` FOREIGN KEY (`sobilladdressid`) REFERENCES `vtiger_salesorder` (`salesorderid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_sobillads`
--

LOCK TABLES `vtiger_sobillads` WRITE;
/*!40000 ALTER TABLE `vtiger_sobillads` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_sobillads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_soshipads`
--

DROP TABLE IF EXISTS `vtiger_soshipads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_soshipads` (
  `soshipaddressid` int(19) NOT NULL DEFAULT '0',
  `ship_city` varchar(30) DEFAULT NULL,
  `ship_code` varchar(30) DEFAULT NULL,
  `ship_country` varchar(30) DEFAULT NULL,
  `ship_state` varchar(30) DEFAULT NULL,
  `ship_street` varchar(250) DEFAULT NULL,
  `ship_pobox` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`soshipaddressid`),
  CONSTRAINT `fk_1_vtiger_soshipads` FOREIGN KEY (`soshipaddressid`) REFERENCES `vtiger_salesorder` (`salesorderid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_soshipads`
--

LOCK TABLES `vtiger_soshipads` WRITE;
/*!40000 ALTER TABLE `vtiger_soshipads` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_soshipads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_sostatus`
--

DROP TABLE IF EXISTS `vtiger_sostatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_sostatus` (
  `sostatusid` int(19) NOT NULL AUTO_INCREMENT,
  `sostatus` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sostatusid`),
  UNIQUE KEY `sostatus_sostatus_idx` (`sostatus`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_sostatus`
--

LOCK TABLES `vtiger_sostatus` WRITE;
/*!40000 ALTER TABLE `vtiger_sostatus` DISABLE KEYS */;
INSERT INTO `vtiger_sostatus` VALUES (1,'Created',0,166),(2,'Approved',0,167),(3,'Delivered',0,168),(4,'Cancelled',0,169);
/*!40000 ALTER TABLE `vtiger_sostatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_sostatus_seq`
--

DROP TABLE IF EXISTS `vtiger_sostatus_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_sostatus_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_sostatus_seq`
--

LOCK TABLES `vtiger_sostatus_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_sostatus_seq` DISABLE KEYS */;
INSERT INTO `vtiger_sostatus_seq` VALUES (4);
/*!40000 ALTER TABLE `vtiger_sostatus_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_sostatushistory`
--

DROP TABLE IF EXISTS `vtiger_sostatushistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_sostatushistory` (
  `historyid` int(19) NOT NULL AUTO_INCREMENT,
  `salesorderid` int(19) NOT NULL,
  `accountname` varchar(100) DEFAULT NULL,
  `total` decimal(28,6) DEFAULT NULL,
  `sostatus` varchar(200) DEFAULT NULL,
  `lastmodified` datetime DEFAULT NULL,
  PRIMARY KEY (`historyid`),
  KEY `sostatushistory_salesorderid_idx` (`salesorderid`),
  CONSTRAINT `fk_1_vtiger_sostatushistory` FOREIGN KEY (`salesorderid`) REFERENCES `vtiger_salesorder` (`salesorderid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_sostatushistory`
--

LOCK TABLES `vtiger_sostatushistory` WRITE;
/*!40000 ALTER TABLE `vtiger_sostatushistory` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_sostatushistory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_start_hour`
--

DROP TABLE IF EXISTS `vtiger_start_hour`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_start_hour` (
  `start_hourid` int(11) NOT NULL AUTO_INCREMENT,
  `start_hour` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`start_hourid`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_start_hour`
--

LOCK TABLES `vtiger_start_hour` WRITE;
/*!40000 ALTER TABLE `vtiger_start_hour` DISABLE KEYS */;
INSERT INTO `vtiger_start_hour` VALUES (1,'00:00',1,370),(2,'01:00',1,371),(3,'02:00',1,372),(4,'03:00',1,373),(5,'04:00',1,374),(6,'05:00',1,375),(7,'06:00',1,376),(8,'07:00',1,377),(9,'08:00',1,378),(10,'09:00',1,379),(11,'10:00',1,380),(12,'11:00',1,381),(13,'12:00',1,382),(14,'13:00',1,383),(15,'14:00',1,384),(16,'15:00',1,385),(17,'16:00',1,386),(18,'17:00',1,387),(19,'18:00',1,388),(20,'19:00',1,389),(21,'20:00',1,390),(22,'21:00',1,391),(23,'22:00',1,392),(24,'23:00',1,393);
/*!40000 ALTER TABLE `vtiger_start_hour` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_start_hour_seq`
--

DROP TABLE IF EXISTS `vtiger_start_hour_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_start_hour_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_start_hour_seq`
--

LOCK TABLES `vtiger_start_hour_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_start_hour_seq` DISABLE KEYS */;
INSERT INTO `vtiger_start_hour_seq` VALUES (24);
/*!40000 ALTER TABLE `vtiger_start_hour_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_status`
--

DROP TABLE IF EXISTS `vtiger_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_status` (
  `statusid` int(19) NOT NULL AUTO_INCREMENT,
  `status` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`statusid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_status`
--

LOCK TABLES `vtiger_status` WRITE;
/*!40000 ALTER TABLE `vtiger_status` DISABLE KEYS */;
INSERT INTO `vtiger_status` VALUES (1,'Active',0,1),(2,'Inactive',1,1);
/*!40000 ALTER TABLE `vtiger_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_status_seq`
--

DROP TABLE IF EXISTS `vtiger_status_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_status_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_status_seq`
--

LOCK TABLES `vtiger_status_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_status_seq` DISABLE KEYS */;
INSERT INTO `vtiger_status_seq` VALUES (2);
/*!40000 ALTER TABLE `vtiger_status_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_systems`
--

DROP TABLE IF EXISTS `vtiger_systems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_systems` (
  `id` int(19) NOT NULL,
  `server` varchar(100) DEFAULT NULL,
  `server_port` int(19) DEFAULT NULL,
  `server_username` varchar(100) DEFAULT NULL,
  `server_password` varchar(100) DEFAULT NULL,
  `server_type` varchar(20) DEFAULT NULL,
  `smtp_auth` varchar(5) DEFAULT NULL,
  `server_path` varchar(256) DEFAULT NULL,
  `from_email_field` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_systems`
--

LOCK TABLES `vtiger_systems` WRITE;
/*!40000 ALTER TABLE `vtiger_systems` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_systems` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_tab`
--

DROP TABLE IF EXISTS `vtiger_tab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_tab` (
  `tabid` int(19) NOT NULL DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
  `presence` int(19) NOT NULL DEFAULT '1',
  `tabsequence` int(10) DEFAULT NULL,
  `tablabel` varchar(100) DEFAULT NULL,
  `modifiedby` int(19) DEFAULT NULL,
  `modifiedtime` int(19) DEFAULT NULL,
  `customized` int(19) DEFAULT NULL,
  `ownedby` int(19) DEFAULT NULL,
  `isentitytype` int(11) NOT NULL DEFAULT '1',
  `version` varchar(10) DEFAULT NULL,
  `parent` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`tabid`),
  UNIQUE KEY `tab_name_idx` (`name`),
  KEY `tab_modifiedby_idx` (`modifiedby`),
  KEY `name` (`name`,`presence`,`isentitytype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_tab`
--

LOCK TABLES `vtiger_tab` WRITE;
/*!40000 ALTER TABLE `vtiger_tab` DISABLE KEYS */;
INSERT INTO `vtiger_tab` VALUES (1,'Dashboard',0,12,'Dashboards',NULL,NULL,0,1,0,NULL,'Analytics'),(2,'Potentials',0,7,'Potentials',NULL,NULL,0,0,1,NULL,'Sales'),(3,'Home',0,1,'Home',NULL,NULL,0,1,0,NULL,NULL),(4,'Contacts',0,6,'Contacts',NULL,NULL,0,0,1,NULL,'Sales'),(6,'Accounts',0,5,'Accounts',NULL,NULL,0,0,1,NULL,'Sales'),(7,'Leads',0,4,'Leads',NULL,NULL,0,0,1,NULL,'Sales'),(8,'Documents',0,9,'Documents',NULL,NULL,0,0,1,NULL,'Tools'),(9,'Calendar',0,3,'Calendar',NULL,NULL,0,0,1,NULL,'Tools'),(10,'Emails',0,10,'Emails',NULL,NULL,0,0,1,NULL,'Tools'),(13,'HelpDesk',0,11,'HelpDesk',NULL,NULL,0,0,1,NULL,'Support'),(14,'Products',0,8,'Products',NULL,NULL,0,0,1,NULL,'Inventory'),(15,'Faq',0,-1,'Faq',NULL,NULL,0,1,1,NULL,'Support'),(16,'Events',2,-1,'Events',NULL,NULL,0,0,1,NULL,NULL),(18,'Vendors',0,-1,'Vendors',NULL,NULL,0,0,1,NULL,'Inventory'),(19,'PriceBooks',0,-1,'PriceBooks',NULL,NULL,0,1,1,NULL,'Inventory'),(20,'Quotes',0,-1,'Quotes',NULL,NULL,0,0,1,NULL,'Sales'),(21,'PurchaseOrder',0,-1,'PurchaseOrder',NULL,NULL,0,0,1,NULL,'Inventory'),(22,'SalesOrder',0,-1,'SalesOrder',NULL,NULL,0,0,1,NULL,'Sales'),(23,'Invoice',0,-1,'Invoice',NULL,NULL,0,0,1,NULL,'Sales'),(24,'Rss',0,-1,'Rss',NULL,NULL,0,1,0,NULL,'Tools'),(25,'Reports',0,-1,'Reports',NULL,NULL,0,1,0,NULL,'Analytics'),(26,'Campaigns',0,-1,'Campaigns',NULL,NULL,0,0,1,NULL,'Marketing'),(27,'Portal',0,-1,'Portal',NULL,NULL,0,1,0,NULL,'Tools'),(28,'Webmails',0,-1,'Webmails',NULL,NULL,0,1,1,NULL,NULL),(29,'Users',0,-1,'Users',NULL,NULL,0,1,0,NULL,NULL),(30,'ConfigEditor',0,-1,'ConfigEditor',NULL,NULL,1,0,0,'1.9',''),(31,'Import',0,-1,'Import',NULL,NULL,1,0,0,'1.2',''),(33,'MailManager',0,-1,'MailManager',NULL,NULL,1,0,0,'1.4','Tools'),(34,'Mobile',0,-1,'Mobile',NULL,NULL,1,0,0,'2.1',''),(35,'ModTracker',0,-1,'ModTracker',NULL,NULL,0,0,0,'1.0',''),(36,'PBXManager',0,-1,'PBXManager',NULL,NULL,0,0,1,'1.7','Tools'),(37,'ServiceContracts',0,-1,'Service Contracts',NULL,NULL,0,0,1,'2.1','Support'),(38,'Services',0,-1,'Services',NULL,NULL,0,0,1,'2.2','Inventory'),(39,'VtigerBackup',0,-1,'Vtiger Backup',NULL,NULL,0,0,0,'1.2',''),(40,'WSAPP',0,-1,'WSAPP',NULL,NULL,1,0,0,'3.4.4',''),(41,'cbupdater',0,-1,'cbupdater',NULL,NULL,1,0,1,'5.4','Settings'),(42,'CobroPago',1,-1,'CobroPago',NULL,NULL,1,0,1,'5.4','Inventory'),(43,'Assets',1,-1,'Assets',NULL,NULL,0,0,1,'1.6','Inventory'),(44,'CronTasks',0,-1,'CronTasks',NULL,NULL,1,0,0,'1.1',''),(45,'CustomerPortal',1,-1,'CustomerPortal',NULL,NULL,0,0,0,'1.4',''),(47,'ModComments',0,-1,'Comments',NULL,NULL,0,0,1,'1.9','Tools'),(48,'ProjectMilestone',1,-1,'ProjectMilestone',NULL,NULL,0,0,1,'2.7','Support'),(49,'ProjectTask',1,-1,'ProjectTask',NULL,NULL,0,0,1,'2.7','Support'),(50,'Project',1,-1,'Project',NULL,NULL,0,0,1,'2.7','Support'),(51,'RecycleBin',0,-1,'Recycle Bin',NULL,NULL,0,0,0,'1.5','Tools'),(52,'SMSNotifier',1,-1,'SMSNotifier',NULL,NULL,0,0,1,'1.8','Tools'),(53,'Tooltip',1,-1,'Tool Tip',NULL,NULL,0,0,0,'1.3',''),(54,'Webforms',1,-1,'Webforms',NULL,NULL,0,0,0,'1.2',''),(55,'Calendar4You',0,-1,'Calendar4You',NULL,NULL,1,0,0,'540.3','Tools'),(56,'GlobalVariable',0,-1,'GlobalVariable',NULL,NULL,1,0,1,'1.0','Tools'),(57,'InventoryDetails',0,-1,'InventoryDetails',NULL,NULL,1,0,1,'0','Inventory'),(58,'cbMap',0,-1,'cbMap',NULL,NULL,1,0,1,'0','Tools'),(59,'evvtMenu',0,-1,'vtMenu',NULL,NULL,1,0,0,'1.0','Settings'),(60,'cbAuditTrail',0,-1,'cbAuditTrail',NULL,NULL,1,0,0,'1.0','Settings'),(61,'cbLoginHistory',0,-1,'cbLoginHistory',NULL,NULL,1,0,0,'1.0','Settings'),(62,'cbTermConditions',0,-1,'cbTermConditions',NULL,NULL,1,0,1,'1.0','Sales'),(63,'cbCalendar',0,-1,'cbCalendar',NULL,NULL,1,0,1,'0','My Home Page'),(64,'cbtranslation',0,-1,'cbtranslation',NULL,NULL,1,0,1,'0','Settings');
/*!40000 ALTER TABLE `vtiger_tab` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_tab_info`
--

DROP TABLE IF EXISTS `vtiger_tab_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_tab_info` (
  `tabid` int(19) DEFAULT NULL,
  `prefname` varchar(256) DEFAULT NULL,
  `prefvalue` varchar(256) DEFAULT NULL,
  KEY `fk_1_vtiger_tab_info` (`tabid`),
  CONSTRAINT `fk_1_vtiger_tab_info` FOREIGN KEY (`tabid`) REFERENCES `vtiger_tab` (`tabid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_tab_info`
--

LOCK TABLES `vtiger_tab_info` WRITE;
/*!40000 ALTER TABLE `vtiger_tab_info` DISABLE KEYS */;
INSERT INTO `vtiger_tab_info` VALUES (30,'vtiger_min_version','5.1.0'),(30,'vtiger_max_version','5.*'),(31,'vtiger_min_version','5.3.0'),(31,'vtiger_max_version','5.*'),(33,'vtiger_min_version','5.2.0'),(34,'vtiger_min_version','5.3.4'),(35,'vtiger_min_version','5.1.0'),(36,'vtiger_min_version','5.1.0'),(36,'vtiger_max_version','5.*'),(37,'vtiger_min_version','5.1.0'),(37,'vtiger_max_version','5.*'),(38,'vtiger_min_version','5.1.0'),(38,'vtiger_max_version','5.*'),(39,'vtiger_min_version','5.1.0'),(40,'vtiger_min_version','5.1.0'),(41,'vtiger_min_version','5.4.0'),(41,'vtiger_max_version','5.*'),(42,'vtiger_min_version','5.4.0'),(42,'vtiger_max_version','5.*'),(43,'vtiger_min_version','5.1.0'),(43,'vtiger_max_version','5.*'),(44,'vtiger_min_version','5.3.1'),(44,'vtiger_max_version','5.*'),(45,'vtiger_min_version','5.1.0'),(45,'vtiger_max_version','5.*'),(47,'vtiger_min_version','5.1.0'),(47,'vtiger_max_version','5.*'),(48,'vtiger_min_version','5.2.0'),(49,'vtiger_min_version','5.2.0'),(50,'vtiger_min_version','5.2.0'),(51,'vtiger_min_version','5.1.0'),(51,'vtiger_max_version','5.*'),(52,'vtiger_min_version','5.1.0'),(52,'vtiger_max_version','5.*'),(53,'vtiger_min_version','5.1.0'),(53,'vtiger_max_version','5.*'),(54,'vtiger_min_version','5.3.0'),(54,'vtiger_max_version','5.*'),(55,'vtiger_min_version','5.4.0'),(55,'vtiger_max_version','5.*'),(56,'vtiger_min_version','5.4.0'),(56,'vtiger_max_version','5.*'),(57,'vtiger_min_version','5.4.0'),(57,'vtiger_max_version','5.*'),(58,'vtiger_min_version','5.5.0'),(59,'vtiger_min_version','5.3.0'),(60,'vtiger_min_version','5.4.0'),(60,'vtiger_max_version','5.*'),(61,'vtiger_min_version','5.4.0'),(61,'vtiger_max_version','5.*'),(62,'vtiger_min_version','5.4.0'),(62,'vtiger_max_version','5.*'),(63,'vtiger_min_version','5.5.0'),(64,'vtiger_min_version','5.5.0');
/*!40000 ALTER TABLE `vtiger_tab_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_taskpriority`
--

DROP TABLE IF EXISTS `vtiger_taskpriority`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_taskpriority` (
  `taskpriorityid` int(19) NOT NULL AUTO_INCREMENT,
  `taskpriority` varchar(200) DEFAULT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`taskpriorityid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_taskpriority`
--

LOCK TABLES `vtiger_taskpriority` WRITE;
/*!40000 ALTER TABLE `vtiger_taskpriority` DISABLE KEYS */;
INSERT INTO `vtiger_taskpriority` VALUES (1,'High',1,170),(2,'Medium',1,171),(3,'Low',1,172);
/*!40000 ALTER TABLE `vtiger_taskpriority` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_taskpriority_seq`
--

DROP TABLE IF EXISTS `vtiger_taskpriority_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_taskpriority_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_taskpriority_seq`
--

LOCK TABLES `vtiger_taskpriority_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_taskpriority_seq` DISABLE KEYS */;
INSERT INTO `vtiger_taskpriority_seq` VALUES (3);
/*!40000 ALTER TABLE `vtiger_taskpriority_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_taskstatus`
--

DROP TABLE IF EXISTS `vtiger_taskstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_taskstatus` (
  `taskstatusid` int(19) NOT NULL AUTO_INCREMENT,
  `taskstatus` varchar(200) DEFAULT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`taskstatusid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_taskstatus`
--

LOCK TABLES `vtiger_taskstatus` WRITE;
/*!40000 ALTER TABLE `vtiger_taskstatus` DISABLE KEYS */;
INSERT INTO `vtiger_taskstatus` VALUES (1,'Not Started',0,173),(2,'In Progress',0,174),(3,'Completed',0,175),(4,'Pending Input',0,176),(5,'Deferred',0,177),(6,'Planned',0,178);
/*!40000 ALTER TABLE `vtiger_taskstatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_taskstatus_seq`
--

DROP TABLE IF EXISTS `vtiger_taskstatus_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_taskstatus_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_taskstatus_seq`
--

LOCK TABLES `vtiger_taskstatus_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_taskstatus_seq` DISABLE KEYS */;
INSERT INTO `vtiger_taskstatus_seq` VALUES (6);
/*!40000 ALTER TABLE `vtiger_taskstatus_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_taxclass`
--

DROP TABLE IF EXISTS `vtiger_taxclass`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_taxclass` (
  `taxclassid` int(19) NOT NULL AUTO_INCREMENT,
  `taxclass` varchar(200) NOT NULL,
  `sortorderid` int(19) NOT NULL DEFAULT '0',
  `presence` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`taxclassid`),
  UNIQUE KEY `taxclass_carrier_idx` (`taxclass`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_taxclass`
--

LOCK TABLES `vtiger_taxclass` WRITE;
/*!40000 ALTER TABLE `vtiger_taxclass` DISABLE KEYS */;
INSERT INTO `vtiger_taxclass` VALUES (1,'SalesTax',0,1),(2,'Vat',1,1);
/*!40000 ALTER TABLE `vtiger_taxclass` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_taxclass_seq`
--

DROP TABLE IF EXISTS `vtiger_taxclass_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_taxclass_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_taxclass_seq`
--

LOCK TABLES `vtiger_taxclass_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_taxclass_seq` DISABLE KEYS */;
INSERT INTO `vtiger_taxclass_seq` VALUES (2);
/*!40000 ALTER TABLE `vtiger_taxclass_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ticketcategories`
--

DROP TABLE IF EXISTS `vtiger_ticketcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ticketcategories` (
  `ticketcategories_id` int(19) NOT NULL AUTO_INCREMENT,
  `ticketcategories` varchar(200) DEFAULT NULL,
  `presence` int(1) NOT NULL DEFAULT '0',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ticketcategories_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ticketcategories`
--

LOCK TABLES `vtiger_ticketcategories` WRITE;
/*!40000 ALTER TABLE `vtiger_ticketcategories` DISABLE KEYS */;
INSERT INTO `vtiger_ticketcategories` VALUES (1,'Big Problem',1,179),(2,'Small Problem',1,180),(3,'Other Problem',1,181);
/*!40000 ALTER TABLE `vtiger_ticketcategories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ticketcategories_seq`
--

DROP TABLE IF EXISTS `vtiger_ticketcategories_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ticketcategories_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ticketcategories_seq`
--

LOCK TABLES `vtiger_ticketcategories_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_ticketcategories_seq` DISABLE KEYS */;
INSERT INTO `vtiger_ticketcategories_seq` VALUES (3);
/*!40000 ALTER TABLE `vtiger_ticketcategories_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ticketcf`
--

DROP TABLE IF EXISTS `vtiger_ticketcf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ticketcf` (
  `ticketid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ticketid`),
  CONSTRAINT `fk_1_vtiger_ticketcf` FOREIGN KEY (`ticketid`) REFERENCES `vtiger_troubletickets` (`ticketid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ticketcf`
--

LOCK TABLES `vtiger_ticketcf` WRITE;
/*!40000 ALTER TABLE `vtiger_ticketcf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_ticketcf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ticketcomments`
--

DROP TABLE IF EXISTS `vtiger_ticketcomments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ticketcomments` (
  `commentid` int(19) NOT NULL AUTO_INCREMENT,
  `ticketid` int(19) DEFAULT NULL,
  `comments` text,
  `ownerid` int(19) NOT NULL DEFAULT '0',
  `ownertype` varchar(10) DEFAULT NULL,
  `createdtime` datetime NOT NULL,
  PRIMARY KEY (`commentid`),
  KEY `ticketcomments_ticketid_idx` (`ticketid`),
  KEY `ticketcomments_ownerid_idx` (`ownerid`),
  CONSTRAINT `fk_1_vtiger_ticketcomments` FOREIGN KEY (`ticketid`) REFERENCES `vtiger_troubletickets` (`ticketid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ticketcomments`
--

LOCK TABLES `vtiger_ticketcomments` WRITE;
/*!40000 ALTER TABLE `vtiger_ticketcomments` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_ticketcomments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ticketpriorities`
--

DROP TABLE IF EXISTS `vtiger_ticketpriorities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ticketpriorities` (
  `ticketpriorities_id` int(19) NOT NULL AUTO_INCREMENT,
  `ticketpriorities` varchar(200) DEFAULT NULL,
  `presence` int(1) NOT NULL DEFAULT '0',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ticketpriorities_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ticketpriorities`
--

LOCK TABLES `vtiger_ticketpriorities` WRITE;
/*!40000 ALTER TABLE `vtiger_ticketpriorities` DISABLE KEYS */;
INSERT INTO `vtiger_ticketpriorities` VALUES (1,'Low',1,182),(2,'Normal',1,183),(3,'High',1,184),(4,'Urgent',1,185);
/*!40000 ALTER TABLE `vtiger_ticketpriorities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ticketpriorities_seq`
--

DROP TABLE IF EXISTS `vtiger_ticketpriorities_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ticketpriorities_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ticketpriorities_seq`
--

LOCK TABLES `vtiger_ticketpriorities_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_ticketpriorities_seq` DISABLE KEYS */;
INSERT INTO `vtiger_ticketpriorities_seq` VALUES (4);
/*!40000 ALTER TABLE `vtiger_ticketpriorities_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ticketseverities`
--

DROP TABLE IF EXISTS `vtiger_ticketseverities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ticketseverities` (
  `ticketseverities_id` int(19) NOT NULL AUTO_INCREMENT,
  `ticketseverities` varchar(200) DEFAULT NULL,
  `presence` int(1) NOT NULL DEFAULT '0',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ticketseverities_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ticketseverities`
--

LOCK TABLES `vtiger_ticketseverities` WRITE;
/*!40000 ALTER TABLE `vtiger_ticketseverities` DISABLE KEYS */;
INSERT INTO `vtiger_ticketseverities` VALUES (1,'Minor',1,186),(2,'Major',1,187),(3,'Feature',1,188),(4,'Critical',1,189);
/*!40000 ALTER TABLE `vtiger_ticketseverities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ticketseverities_seq`
--

DROP TABLE IF EXISTS `vtiger_ticketseverities_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ticketseverities_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ticketseverities_seq`
--

LOCK TABLES `vtiger_ticketseverities_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_ticketseverities_seq` DISABLE KEYS */;
INSERT INTO `vtiger_ticketseverities_seq` VALUES (4);
/*!40000 ALTER TABLE `vtiger_ticketseverities_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ticketstatus`
--

DROP TABLE IF EXISTS `vtiger_ticketstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ticketstatus` (
  `ticketstatus_id` int(19) NOT NULL AUTO_INCREMENT,
  `ticketstatus` varchar(200) DEFAULT NULL,
  `presence` int(1) NOT NULL DEFAULT '0',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ticketstatus_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ticketstatus`
--

LOCK TABLES `vtiger_ticketstatus` WRITE;
/*!40000 ALTER TABLE `vtiger_ticketstatus` DISABLE KEYS */;
INSERT INTO `vtiger_ticketstatus` VALUES (1,'Open',0,190),(2,'In Progress',0,191),(3,'Wait For Response',0,192),(4,'Closed',0,193);
/*!40000 ALTER TABLE `vtiger_ticketstatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ticketstatus_seq`
--

DROP TABLE IF EXISTS `vtiger_ticketstatus_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ticketstatus_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ticketstatus_seq`
--

LOCK TABLES `vtiger_ticketstatus_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_ticketstatus_seq` DISABLE KEYS */;
INSERT INTO `vtiger_ticketstatus_seq` VALUES (4);
/*!40000 ALTER TABLE `vtiger_ticketstatus_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_time_zone`
--

DROP TABLE IF EXISTS `vtiger_time_zone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_time_zone` (
  `time_zoneid` int(19) NOT NULL AUTO_INCREMENT,
  `time_zone` varchar(200) NOT NULL,
  `sortorderid` int(19) NOT NULL DEFAULT '0',
  `presence` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`time_zoneid`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_time_zone`
--

LOCK TABLES `vtiger_time_zone` WRITE;
/*!40000 ALTER TABLE `vtiger_time_zone` DISABLE KEYS */;
INSERT INTO `vtiger_time_zone` VALUES (1,'Pacific/Midway',0,1),(2,'Pacific/Samoa',1,1),(3,'Pacific/Honolulu',2,1),(4,'America/Anchorage',3,1),(5,'America/Los_Angeles',4,1),(6,'America/Tijuana',5,1),(7,'America/Denver',6,1),(8,'America/Chihuahua',7,1),(9,'America/Mazatlan',8,1),(10,'America/Phoenix',9,1),(11,'America/Regina',10,1),(12,'America/Tegucigalpa',11,1),(13,'America/Chicago',12,1),(14,'America/Mexico_City',13,1),(15,'America/Monterrey',14,1),(16,'America/New_York',15,1),(17,'America/Bogota',16,1),(18,'America/Lima',17,1),(19,'America/Rio_Branco',18,1),(20,'America/Indiana/Indianapolis',19,1),(21,'America/Caracas',20,1),(22,'America/Halifax',21,1),(23,'America/Manaus',22,1),(24,'America/Santiago',23,1),(25,'America/La_Paz',24,1),(26,'America/Cuiaba',25,1),(27,'America/Asuncion',26,1),(28,'America/St_Johns',27,1),(29,'America/Argentina/Buenos_Aires',28,1),(30,'America/Sao_Paulo',29,1),(31,'America/Godthab',30,1),(32,'America/Montevideo',31,1),(33,'Atlantic/South_Georgia',32,1),(34,'Atlantic/Azores',33,1),(35,'Atlantic/Cape_Verde',34,1),(36,'Europe/London',35,1),(37,'UTC',36,1),(38,'Africa/Monrovia',37,1),(39,'Africa/Casablanca',38,1),(40,'Europe/Belgrade',39,1),(41,'Europe/Sarajevo',40,1),(42,'Europe/Brussels',41,1),(43,'Africa/Algiers',42,1),(44,'Europe/Amsterdam',43,1),(45,'Europe/Minsk',44,1),(46,'Africa/Cairo',45,1),(47,'Europe/Helsinki',46,1),(48,'Europe/Athens',47,1),(49,'Europe/Istanbul',48,1),(50,'Asia/Jerusalem',49,1),(51,'Asia/Amman',50,1),(52,'Asia/Beirut',51,1),(53,'Africa/Windhoek',52,1),(54,'Africa/Harare',53,1),(55,'Asia/Kuwait',54,1),(56,'Asia/Baghdad',55,1),(57,'Africa/Nairobi',56,1),(58,'Asia/Tehran',57,1),(59,'Asia/Tbilisi',58,1),(60,'Europe/Moscow',59,1),(61,'Asia/Muscat',60,1),(62,'Asia/Baku',61,1),(63,'Asia/Yerevan',62,1),(64,'Asia/Karachi',63,1),(65,'Asia/Tashkent',64,1),(66,'Asia/Kolkata',65,1),(67,'Asia/Colombo',66,1),(68,'Asia/Katmandu',67,1),(69,'Asia/Dhaka',68,1),(70,'Asia/Almaty',69,1),(71,'Asia/Yekaterinburg',70,1),(72,'Asia/Rangoon',71,1),(73,'Asia/Novosibirsk',72,1),(74,'Asia/Bangkok',73,1),(75,'Asia/Brunei',74,1),(76,'Asia/Krasnoyarsk',75,1),(77,'Asia/Ulaanbaatar',76,1),(78,'Asia/Kuala_Lumpur',77,1),(79,'Asia/Taipei',78,1),(80,'Australia/Perth',79,1),(81,'Asia/Irkutsk',80,1),(82,'Asia/Seoul',81,1),(83,'Asia/Tokyo',82,1),(84,'Australia/Darwin',83,1),(85,'Australia/Adelaide',84,1),(86,'Australia/Canberra',85,1),(87,'Australia/Brisbane',86,1),(88,'Australia/Hobart',87,1),(89,'Asia/Vladivostok',88,1),(90,'Pacific/Guam',89,1),(91,'Asia/Yakutsk',90,1),(92,'Pacific/Fiji',91,1),(93,'Asia/Kamchatka',92,1),(94,'Pacific/Auckland',93,1),(95,'Asia/Magadan',94,1),(96,'Pacific/Tongatapu',95,1);
/*!40000 ALTER TABLE `vtiger_time_zone` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_time_zone_seq`
--

DROP TABLE IF EXISTS `vtiger_time_zone_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_time_zone_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_time_zone_seq`
--

LOCK TABLES `vtiger_time_zone_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_time_zone_seq` DISABLE KEYS */;
INSERT INTO `vtiger_time_zone_seq` VALUES (96);
/*!40000 ALTER TABLE `vtiger_time_zone_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_tmp_read_group_rel_sharing_per`
--

DROP TABLE IF EXISTS `vtiger_tmp_read_group_rel_sharing_per`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_tmp_read_group_rel_sharing_per` (
  `userid` int(11) NOT NULL,
  `tabid` int(11) NOT NULL,
  `relatedtabid` int(11) NOT NULL,
  `sharedgroupid` int(11) NOT NULL,
  PRIMARY KEY (`userid`,`tabid`,`relatedtabid`,`sharedgroupid`),
  KEY `tmp_read_group_rel_sharing_per_userid_sharedgroupid_tabid` (`userid`,`sharedgroupid`,`tabid`),
  CONSTRAINT `fk_4_vtiger_tmp_read_group_rel_sharing_per` FOREIGN KEY (`userid`) REFERENCES `vtiger_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_tmp_read_group_rel_sharing_per`
--

LOCK TABLES `vtiger_tmp_read_group_rel_sharing_per` WRITE;
/*!40000 ALTER TABLE `vtiger_tmp_read_group_rel_sharing_per` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_tmp_read_group_rel_sharing_per` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_tmp_read_group_sharing_per`
--

DROP TABLE IF EXISTS `vtiger_tmp_read_group_sharing_per`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_tmp_read_group_sharing_per` (
  `userid` int(11) NOT NULL,
  `tabid` int(11) NOT NULL,
  `sharedgroupid` int(11) NOT NULL,
  PRIMARY KEY (`userid`,`tabid`,`sharedgroupid`),
  KEY `tmp_read_group_sharing_per_userid_sharedgroupid_idx` (`userid`,`sharedgroupid`),
  CONSTRAINT `fk_3_vtiger_tmp_read_group_sharing_per` FOREIGN KEY (`userid`) REFERENCES `vtiger_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_tmp_read_group_sharing_per`
--

LOCK TABLES `vtiger_tmp_read_group_sharing_per` WRITE;
/*!40000 ALTER TABLE `vtiger_tmp_read_group_sharing_per` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_tmp_read_group_sharing_per` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_tmp_read_user_rel_sharing_per`
--

DROP TABLE IF EXISTS `vtiger_tmp_read_user_rel_sharing_per`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_tmp_read_user_rel_sharing_per` (
  `userid` int(11) NOT NULL,
  `tabid` int(11) NOT NULL,
  `relatedtabid` int(11) NOT NULL,
  `shareduserid` int(11) NOT NULL,
  PRIMARY KEY (`userid`,`tabid`,`relatedtabid`,`shareduserid`),
  KEY `tmp_read_user_rel_sharing_per_userid_shared_reltabid_idx` (`userid`,`shareduserid`,`relatedtabid`),
  CONSTRAINT `fk_4_vtiger_tmp_read_user_rel_sharing_per` FOREIGN KEY (`userid`) REFERENCES `vtiger_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_tmp_read_user_rel_sharing_per`
--

LOCK TABLES `vtiger_tmp_read_user_rel_sharing_per` WRITE;
/*!40000 ALTER TABLE `vtiger_tmp_read_user_rel_sharing_per` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_tmp_read_user_rel_sharing_per` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_tmp_read_user_sharing_per`
--

DROP TABLE IF EXISTS `vtiger_tmp_read_user_sharing_per`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_tmp_read_user_sharing_per` (
  `userid` int(11) NOT NULL,
  `tabid` int(11) NOT NULL,
  `shareduserid` int(11) NOT NULL,
  PRIMARY KEY (`userid`,`tabid`,`shareduserid`),
  KEY `tmp_read_user_sharing_per_userid_shareduserid_idx` (`userid`,`shareduserid`),
  CONSTRAINT `fk_3_vtiger_tmp_read_user_sharing_per` FOREIGN KEY (`userid`) REFERENCES `vtiger_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_tmp_read_user_sharing_per`
--

LOCK TABLES `vtiger_tmp_read_user_sharing_per` WRITE;
/*!40000 ALTER TABLE `vtiger_tmp_read_user_sharing_per` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_tmp_read_user_sharing_per` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_tmp_write_group_rel_sharing_per`
--

DROP TABLE IF EXISTS `vtiger_tmp_write_group_rel_sharing_per`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_tmp_write_group_rel_sharing_per` (
  `userid` int(11) NOT NULL,
  `tabid` int(11) NOT NULL,
  `relatedtabid` int(11) NOT NULL,
  `sharedgroupid` int(11) NOT NULL,
  PRIMARY KEY (`userid`,`tabid`,`relatedtabid`,`sharedgroupid`),
  KEY `tmp_write_group_rel_sharing_per_userid_sharedgroupid_tabid_idx` (`userid`,`sharedgroupid`,`tabid`),
  CONSTRAINT `fk_4_vtiger_tmp_write_group_rel_sharing_per` FOREIGN KEY (`userid`) REFERENCES `vtiger_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_tmp_write_group_rel_sharing_per`
--

LOCK TABLES `vtiger_tmp_write_group_rel_sharing_per` WRITE;
/*!40000 ALTER TABLE `vtiger_tmp_write_group_rel_sharing_per` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_tmp_write_group_rel_sharing_per` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_tmp_write_group_sharing_per`
--

DROP TABLE IF EXISTS `vtiger_tmp_write_group_sharing_per`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_tmp_write_group_sharing_per` (
  `userid` int(11) NOT NULL,
  `tabid` int(11) NOT NULL,
  `sharedgroupid` int(11) NOT NULL,
  PRIMARY KEY (`userid`,`tabid`,`sharedgroupid`),
  KEY `tmp_write_group_sharing_per_UK1` (`userid`,`sharedgroupid`),
  CONSTRAINT `fk_3_vtiger_tmp_write_group_sharing_per` FOREIGN KEY (`userid`) REFERENCES `vtiger_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_tmp_write_group_sharing_per`
--

LOCK TABLES `vtiger_tmp_write_group_sharing_per` WRITE;
/*!40000 ALTER TABLE `vtiger_tmp_write_group_sharing_per` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_tmp_write_group_sharing_per` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_tmp_write_user_rel_sharing_per`
--

DROP TABLE IF EXISTS `vtiger_tmp_write_user_rel_sharing_per`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_tmp_write_user_rel_sharing_per` (
  `userid` int(11) NOT NULL,
  `tabid` int(11) NOT NULL,
  `relatedtabid` int(11) NOT NULL,
  `shareduserid` int(11) NOT NULL,
  PRIMARY KEY (`userid`,`tabid`,`relatedtabid`,`shareduserid`),
  KEY `tmp_write_user_rel_sharing_per_userid_sharduserid_tabid_idx` (`userid`,`shareduserid`,`tabid`),
  CONSTRAINT `fk_4_vtiger_tmp_write_user_rel_sharing_per` FOREIGN KEY (`userid`) REFERENCES `vtiger_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_tmp_write_user_rel_sharing_per`
--

LOCK TABLES `vtiger_tmp_write_user_rel_sharing_per` WRITE;
/*!40000 ALTER TABLE `vtiger_tmp_write_user_rel_sharing_per` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_tmp_write_user_rel_sharing_per` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_tmp_write_user_sharing_per`
--

DROP TABLE IF EXISTS `vtiger_tmp_write_user_sharing_per`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_tmp_write_user_sharing_per` (
  `userid` int(11) NOT NULL,
  `tabid` int(11) NOT NULL,
  `shareduserid` int(11) NOT NULL,
  PRIMARY KEY (`userid`,`tabid`,`shareduserid`),
  KEY `tmp_write_user_sharing_per_userid_shareduserid_idx` (`userid`,`shareduserid`),
  CONSTRAINT `fk_3_vtiger_tmp_write_user_sharing_per` FOREIGN KEY (`userid`) REFERENCES `vtiger_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_tmp_write_user_sharing_per`
--

LOCK TABLES `vtiger_tmp_write_user_sharing_per` WRITE;
/*!40000 ALTER TABLE `vtiger_tmp_write_user_sharing_per` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_tmp_write_user_sharing_per` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_tracker`
--

DROP TABLE IF EXISTS `vtiger_tracker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_tracker` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(36) DEFAULT NULL,
  `module_name` varchar(25) DEFAULT NULL,
  `item_id` varchar(36) DEFAULT NULL,
  `item_summary` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tracker_multi_idx` (`user_id`,`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_tracker`
--

LOCK TABLES `vtiger_tracker` WRITE;
/*!40000 ALTER TABLE `vtiger_tracker` DISABLE KEYS */;
INSERT INTO `vtiger_tracker` VALUES (7,'1','cbupdater','146','cbupd-0000021'),(8,'1','Vendors','45','Barbara'),(9,'1','Vendors','46','Elizabeth'),(12,'1','Calendar','145','vnd'),(13,'1','cbupdater','199','cbupd-0000074'),(14,'1','Users','1',' Administrator');
/*!40000 ALTER TABLE `vtiger_tracker` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_tracking_unit`
--

DROP TABLE IF EXISTS `vtiger_tracking_unit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_tracking_unit` (
  `tracking_unitid` int(11) NOT NULL AUTO_INCREMENT,
  `tracking_unit` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tracking_unitid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_tracking_unit`
--

LOCK TABLES `vtiger_tracking_unit` WRITE;
/*!40000 ALTER TABLE `vtiger_tracking_unit` DISABLE KEYS */;
INSERT INTO `vtiger_tracking_unit` VALUES (1,'None',1,210),(2,'Hours',1,211),(3,'Days',1,212),(4,'Incidents',1,213);
/*!40000 ALTER TABLE `vtiger_tracking_unit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_tracking_unit_seq`
--

DROP TABLE IF EXISTS `vtiger_tracking_unit_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_tracking_unit_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_tracking_unit_seq`
--

LOCK TABLES `vtiger_tracking_unit_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_tracking_unit_seq` DISABLE KEYS */;
INSERT INTO `vtiger_tracking_unit_seq` VALUES (4);
/*!40000 ALTER TABLE `vtiger_tracking_unit_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_troubletickets`
--

DROP TABLE IF EXISTS `vtiger_troubletickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_troubletickets` (
  `ticketid` int(19) NOT NULL,
  `ticket_no` varchar(100) NOT NULL,
  `groupname` varchar(100) DEFAULT NULL,
  `parent_id` int(19) DEFAULT NULL,
  `product_id` int(19) DEFAULT NULL,
  `priority` varchar(200) DEFAULT NULL,
  `severity` varchar(200) DEFAULT NULL,
  `status` varchar(200) DEFAULT NULL,
  `category` varchar(200) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `solution` text,
  `update_log` text,
  `version_id` int(11) DEFAULT NULL,
  `hours` decimal(28,6) DEFAULT NULL,
  `days` varchar(200) DEFAULT NULL,
  `from_portal` varchar(3) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `from_mailscanner` varchar(3) DEFAULT NULL,
  `commentadded` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`ticketid`),
  KEY `troubletickets_status_idx` (`status`),
  KEY `parentid_idx` (`parent_id`),
  KEY `productid_idx` (`product_id`),
  CONSTRAINT `fk_1_vtiger_troubletickets` FOREIGN KEY (`ticketid`) REFERENCES `vtiger_crmentity` (`crmid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_troubletickets`
--

LOCK TABLES `vtiger_troubletickets` WRITE;
/*!40000 ALTER TABLE `vtiger_troubletickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_troubletickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_usageunit`
--

DROP TABLE IF EXISTS `vtiger_usageunit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_usageunit` (
  `usageunitid` int(19) NOT NULL AUTO_INCREMENT,
  `usageunit` varchar(200) NOT NULL,
  `presence` int(1) NOT NULL DEFAULT '1',
  `picklist_valueid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`usageunitid`),
  UNIQUE KEY `usageunit_usageunit_idx` (`usageunit`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_usageunit`
--

LOCK TABLES `vtiger_usageunit` WRITE;
/*!40000 ALTER TABLE `vtiger_usageunit` DISABLE KEYS */;
INSERT INTO `vtiger_usageunit` VALUES (1,'Box',1,194),(2,'Carton',1,195),(3,'Dozen',1,196),(4,'Each',1,197),(5,'Hours',1,198),(6,'Impressions',1,199),(7,'Lb',1,200),(8,'M',1,201),(9,'Pack',1,202),(10,'Pages',1,203),(11,'Pieces',1,204),(12,'Quantity',1,205),(13,'Reams',1,206),(14,'Sheet',1,207),(15,'Spiral Binder',1,208),(16,'Sq Ft',1,209);
/*!40000 ALTER TABLE `vtiger_usageunit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_usageunit_seq`
--

DROP TABLE IF EXISTS `vtiger_usageunit_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_usageunit_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_usageunit_seq`
--

LOCK TABLES `vtiger_usageunit_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_usageunit_seq` DISABLE KEYS */;
INSERT INTO `vtiger_usageunit_seq` VALUES (16);
/*!40000 ALTER TABLE `vtiger_usageunit_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_user2mergefields`
--

DROP TABLE IF EXISTS `vtiger_user2mergefields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_user2mergefields` (
  `userid` int(11) DEFAULT NULL,
  `tabid` int(19) DEFAULT NULL,
  `fieldid` int(19) DEFAULT NULL,
  `visible` int(2) DEFAULT NULL,
  KEY `userid_tabid_idx` (`userid`,`tabid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_user2mergefields`
--

LOCK TABLES `vtiger_user2mergefields` WRITE;
/*!40000 ALTER TABLE `vtiger_user2mergefields` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_user2mergefields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_user2role`
--

DROP TABLE IF EXISTS `vtiger_user2role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_user2role` (
  `userid` int(11) NOT NULL,
  `roleid` varchar(255) NOT NULL,
  PRIMARY KEY (`userid`),
  KEY `user2role_roleid_idx` (`roleid`),
  CONSTRAINT `fk_2_vtiger_user2role` FOREIGN KEY (`userid`) REFERENCES `vtiger_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_user2role`
--

LOCK TABLES `vtiger_user2role` WRITE;
/*!40000 ALTER TABLE `vtiger_user2role` DISABLE KEYS */;
INSERT INTO `vtiger_user2role` VALUES (1,'H2');
/*!40000 ALTER TABLE `vtiger_user2role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_user_module_preferences`
--

DROP TABLE IF EXISTS `vtiger_user_module_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_user_module_preferences` (
  `userid` int(19) NOT NULL,
  `tabid` int(19) NOT NULL,
  `default_cvid` int(19) NOT NULL,
  PRIMARY KEY (`userid`,`tabid`),
  KEY `fk_2_vtiger_user_module_preferences` (`tabid`),
  CONSTRAINT `fk_2_vtiger_user_module_preferences` FOREIGN KEY (`tabid`) REFERENCES `vtiger_tab` (`tabid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_user_module_preferences`
--

LOCK TABLES `vtiger_user_module_preferences` WRITE;
/*!40000 ALTER TABLE `vtiger_user_module_preferences` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_user_module_preferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_users`
--

DROP TABLE IF EXISTS `vtiger_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) DEFAULT NULL,
  `user_password` varchar(200) DEFAULT NULL,
  `user_hash` varchar(32) DEFAULT NULL,
  `cal_color` varchar(25) DEFAULT '#E6FAD8',
  `first_name` varchar(30) DEFAULT NULL,
  `last_name` varchar(30) DEFAULT NULL,
  `reports_to_id` varchar(36) DEFAULT NULL,
  `is_admin` varchar(3) DEFAULT '0',
  `currency_id` int(19) NOT NULL DEFAULT '1',
  `description` text,
  `date_entered` datetime NOT NULL,
  `date_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_user_id` varchar(36) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `department` varchar(150) DEFAULT NULL,
  `phone_home` varchar(50) DEFAULT NULL,
  `phone_mobile` varchar(50) DEFAULT NULL,
  `phone_work` varchar(50) DEFAULT NULL,
  `phone_other` varchar(50) DEFAULT NULL,
  `phone_fax` varchar(50) DEFAULT NULL,
  `email1` varchar(100) DEFAULT NULL,
  `email2` varchar(100) DEFAULT NULL,
  `secondaryemail` varchar(100) DEFAULT NULL,
  `status` varchar(25) DEFAULT NULL,
  `signature` text,
  `address_street` varchar(150) DEFAULT NULL,
  `address_city` varchar(100) DEFAULT NULL,
  `address_state` varchar(100) DEFAULT NULL,
  `address_country` varchar(25) DEFAULT NULL,
  `address_postalcode` varchar(9) DEFAULT NULL,
  `user_preferences` text,
  `tz` varchar(30) DEFAULT NULL,
  `holidays` varchar(60) DEFAULT NULL,
  `namedays` varchar(60) DEFAULT NULL,
  `workdays` varchar(30) DEFAULT NULL,
  `weekstart` int(11) DEFAULT NULL,
  `date_format` varchar(200) DEFAULT NULL,
  `hour_format` varchar(30) DEFAULT 'am/pm',
  `start_hour` varchar(30) DEFAULT '10:00',
  `end_hour` varchar(30) DEFAULT '23:00',
  `activity_view` varchar(200) DEFAULT 'Today',
  `lead_view` varchar(200) DEFAULT 'Today',
  `imagename` varchar(250) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `confirm_password` varchar(300) DEFAULT NULL,
  `internal_mailer` varchar(3) NOT NULL DEFAULT '1',
  `reminder_interval` varchar(100) DEFAULT NULL,
  `reminder_next_time` varchar(100) DEFAULT NULL,
  `crypt_type` varchar(20) NOT NULL DEFAULT 'MD5',
  `accesskey` varchar(36) DEFAULT NULL,
  `theme` varchar(100) DEFAULT NULL,
  `language` varchar(36) DEFAULT NULL,
  `time_zone` varchar(200) DEFAULT NULL,
  `currency_grouping_pattern` varchar(100) DEFAULT NULL,
  `currency_decimal_separator` varchar(2) DEFAULT NULL,
  `currency_grouping_separator` varchar(2) DEFAULT NULL,
  `currency_symbol_placement` varchar(20) DEFAULT NULL,
  `showtagas` varchar(20) NOT NULL DEFAULT 'hring',
  `send_email_to_sender` varchar(3) DEFAULT NULL,
  `no_of_currency_decimals` varchar(2) DEFAULT NULL,
  `change_password` tinyint(1) NOT NULL DEFAULT '0',
  `last_password_reset_date` date DEFAULT NULL,
  `failed_login_attempts` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_user_name_idx` (`user_name`),
  KEY `user_user_password_idx` (`user_password`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_users`
--

LOCK TABLES `vtiger_users` WRITE;
/*!40000 ALTER TABLE `vtiger_users` DISABLE KEYS */;
INSERT INTO `vtiger_users` VALUES (1,'admin','$1$ad000000$hzXFXvL3XVlnUE/X.1n9t/','','#E6FAD8','','Administrator','','on',1,'','2016-10-26 00:24:30','2017-01-06 23:28:50',NULL,'','','','','','','','joe@tsolucio.com','','','Active','','','','','','',NULL,NULL,NULL,NULL,NULL,NULL,'yyyy-mm-dd','am/pm','08:00','23:00','This Week','Today','',0,'$1$ad000000$hzXFXvL3XVlnUE/X.1n9t/','1','1 Minute',NULL,'PHP5.3MD5','bpkAgeBNInBqt4MT','softed','en_us','UTC','','','','','hring','1','2',0,'2017-01-07',0);
/*!40000 ALTER TABLE `vtiger_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_users2group`
--

DROP TABLE IF EXISTS `vtiger_users2group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_users2group` (
  `groupid` int(19) NOT NULL,
  `userid` int(19) NOT NULL,
  PRIMARY KEY (`groupid`,`userid`),
  KEY `fk_2_vtiger_users2group` (`userid`),
  CONSTRAINT `fk_2_vtiger_users2group` FOREIGN KEY (`userid`) REFERENCES `vtiger_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_users2group`
--

LOCK TABLES `vtiger_users2group` WRITE;
/*!40000 ALTER TABLE `vtiger_users2group` DISABLE KEYS */;
INSERT INTO `vtiger_users2group` VALUES (3,1);
/*!40000 ALTER TABLE `vtiger_users2group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_users_last_import`
--

DROP TABLE IF EXISTS `vtiger_users_last_import`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_users_last_import` (
  `id` int(36) NOT NULL AUTO_INCREMENT,
  `assigned_user_id` varchar(36) DEFAULT NULL,
  `bean_type` varchar(36) DEFAULT NULL,
  `bean_id` varchar(36) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`assigned_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_users_last_import`
--

LOCK TABLES `vtiger_users_last_import` WRITE;
/*!40000 ALTER TABLE `vtiger_users_last_import` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_users_last_import` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_users_seq`
--

DROP TABLE IF EXISTS `vtiger_users_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_users_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_users_seq`
--

LOCK TABLES `vtiger_users_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_users_seq` DISABLE KEYS */;
INSERT INTO `vtiger_users_seq` VALUES (4);
/*!40000 ALTER TABLE `vtiger_users_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_vendor`
--

DROP TABLE IF EXISTS `vtiger_vendor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_vendor` (
  `vendorid` int(19) NOT NULL DEFAULT '0',
  `vendor_no` varchar(100) NOT NULL,
  `vendorname` varchar(100) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  `glacct` varchar(200) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `street` text,
  `city` varchar(30) DEFAULT NULL,
  `state` varchar(30) DEFAULT NULL,
  `pobox` varchar(30) DEFAULT NULL,
  `postalcode` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`vendorid`),
  CONSTRAINT `fk_1_vtiger_vendor` FOREIGN KEY (`vendorid`) REFERENCES `vtiger_crmentity` (`crmid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_vendor`
--

LOCK TABLES `vtiger_vendor` WRITE;
/*!40000 ALTER TABLE `vtiger_vendor` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_vendor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_vendorcf`
--

DROP TABLE IF EXISTS `vtiger_vendorcf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_vendorcf` (
  `vendorid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`vendorid`),
  CONSTRAINT `fk_1_vtiger_vendorcf` FOREIGN KEY (`vendorid`) REFERENCES `vtiger_vendor` (`vendorid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_vendorcf`
--

LOCK TABLES `vtiger_vendorcf` WRITE;
/*!40000 ALTER TABLE `vtiger_vendorcf` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_vendorcf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_vendorcontactrel`
--

DROP TABLE IF EXISTS `vtiger_vendorcontactrel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_vendorcontactrel` (
  `vendorid` int(19) NOT NULL DEFAULT '0',
  `contactid` int(19) NOT NULL DEFAULT '0',
  PRIMARY KEY (`vendorid`,`contactid`),
  KEY `vendorcontactrel_vendorid_idx` (`vendorid`),
  KEY `vendorcontactrel_contact_idx` (`contactid`),
  CONSTRAINT `fk_2_vtiger_vendorcontactrel` FOREIGN KEY (`vendorid`) REFERENCES `vtiger_vendor` (`vendorid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_vendorcontactrel`
--

LOCK TABLES `vtiger_vendorcontactrel` WRITE;
/*!40000 ALTER TABLE `vtiger_vendorcontactrel` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_vendorcontactrel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_version`
--

DROP TABLE IF EXISTS `vtiger_version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `old_version` varchar(30) DEFAULT NULL,
  `current_version` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_version`
--

LOCK TABLES `vtiger_version` WRITE;
/*!40000 ALTER TABLE `vtiger_version` DISABLE KEYS */;
INSERT INTO `vtiger_version` VALUES (1,'5.5.0','5.5.0');
/*!40000 ALTER TABLE `vtiger_version` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_version_seq`
--

DROP TABLE IF EXISTS `vtiger_version_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_version_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_version_seq`
--

LOCK TABLES `vtiger_version_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_version_seq` DISABLE KEYS */;
INSERT INTO `vtiger_version_seq` VALUES (1);
/*!40000 ALTER TABLE `vtiger_version_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_visibility`
--

DROP TABLE IF EXISTS `vtiger_visibility`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_visibility` (
  `visibilityid` int(19) NOT NULL AUTO_INCREMENT,
  `visibility` varchar(200) NOT NULL,
  `sortorderid` int(19) NOT NULL DEFAULT '0',
  `presence` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`visibilityid`),
  UNIQUE KEY `visibility_visibility_idx` (`visibility`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_visibility`
--

LOCK TABLES `vtiger_visibility` WRITE;
/*!40000 ALTER TABLE `vtiger_visibility` DISABLE KEYS */;
INSERT INTO `vtiger_visibility` VALUES (1,'Private',0,1),(2,'Public',1,1);
/*!40000 ALTER TABLE `vtiger_visibility` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_visibility_seq`
--

DROP TABLE IF EXISTS `vtiger_visibility_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_visibility_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_visibility_seq`
--

LOCK TABLES `vtiger_visibility_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_visibility_seq` DISABLE KEYS */;
INSERT INTO `vtiger_visibility_seq` VALUES (2);
/*!40000 ALTER TABLE `vtiger_visibility_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_webforms`
--

DROP TABLE IF EXISTS `vtiger_webforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_webforms` (
  `id` int(19) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `publicid` varchar(100) NOT NULL,
  `enabled` int(1) NOT NULL DEFAULT '1',
  `targetmodule` varchar(50) NOT NULL,
  `description` text,
  `ownerid` int(19) NOT NULL,
  `returnurl` varchar(250) DEFAULT NULL,
  `web_domain` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `webformname` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_webforms`
--

LOCK TABLES `vtiger_webforms` WRITE;
/*!40000 ALTER TABLE `vtiger_webforms` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_webforms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_webforms_field`
--

DROP TABLE IF EXISTS `vtiger_webforms_field`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_webforms_field` (
  `id` int(19) NOT NULL AUTO_INCREMENT,
  `webformid` int(19) NOT NULL,
  `fieldname` varchar(50) NOT NULL,
  `neutralizedfield` varchar(50) NOT NULL,
  `defaultvalue` varchar(200) DEFAULT NULL,
  `required` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_1_vtiger_webforms_field` (`webformid`),
  KEY `fk_2_vtiger_webforms_field` (`fieldname`),
  CONSTRAINT `fk_1_vtiger_webforms_field` FOREIGN KEY (`webformid`) REFERENCES `vtiger_webforms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_3_vtiger_webforms_field` FOREIGN KEY (`fieldname`) REFERENCES `vtiger_field` (`fieldname`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_webforms_field`
--

LOCK TABLES `vtiger_webforms_field` WRITE;
/*!40000 ALTER TABLE `vtiger_webforms_field` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_webforms_field` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_wordtemplates`
--

DROP TABLE IF EXISTS `vtiger_wordtemplates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_wordtemplates` (
  `templateid` int(19) NOT NULL,
  `filename` varchar(100) NOT NULL,
  `module` varchar(30) NOT NULL,
  `date_entered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `parent_type` varchar(50) NOT NULL,
  `data` longblob,
  `description` text,
  `filesize` varchar(50) NOT NULL,
  `filetype` varchar(50) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`templateid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_wordtemplates`
--

LOCK TABLES `vtiger_wordtemplates` WRITE;
/*!40000 ALTER TABLE `vtiger_wordtemplates` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_wordtemplates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ws_entity`
--

DROP TABLE IF EXISTS `vtiger_ws_entity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ws_entity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `handler_path` varchar(255) NOT NULL,
  `handler_class` varchar(64) NOT NULL,
  `ismodule` int(3) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ws_entity`
--

LOCK TABLES `vtiger_ws_entity` WRITE;
/*!40000 ALTER TABLE `vtiger_ws_entity` DISABLE KEYS */;
INSERT INTO `vtiger_ws_entity` VALUES (1,'Campaigns','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(2,'Vendors','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(3,'Faq','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(4,'Quotes','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(5,'PurchaseOrder','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(6,'SalesOrder','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(7,'Invoice','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(8,'PriceBooks','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(9,'Calendar','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(10,'Leads','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(11,'Accounts','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(12,'Contacts','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(13,'Potentials','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(14,'Products','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(15,'Documents','include/Webservices/VtigerDocumentOperation.php','VtigerDocumentOperation',1),(16,'Emails','include/Webservices/VtigerEmailOperation.php','VtigerEmailOperation',1),(17,'HelpDesk','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(18,'Events','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(19,'Users','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(20,'Groups','include/Webservices/VtigerActorOperation.php','VtigerActorOperation',0),(21,'Currency','include/Webservices/VtigerActorOperation.php','VtigerActorOperation',0),(22,'DocumentFolders','include/Webservices/VtigerActorOperation.php','VtigerActorOperation',0),(23,'CompanyDetails','include/Webservices/VtigerCompanyDetails.php','VtigerCompanyDetails',0),(24,'PBXManager','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(25,'ServiceContracts','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(26,'Services','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(27,'cbupdater','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(28,'CobroPago','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(29,'Assets','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(30,'ModComments','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(31,'ProjectMilestone','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(32,'ProjectTask','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(33,'Project','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(34,'SMSNotifier','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(35,'GlobalVariable','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(36,'InventoryDetails','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(37,'cbMap','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(38,'cbTermConditions','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(39,'cbCalendar','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1),(40,'cbtranslation','include/Webservices/VtigerModuleOperation.php','VtigerModuleOperation',1);
/*!40000 ALTER TABLE `vtiger_ws_entity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ws_entity_fieldtype`
--

DROP TABLE IF EXISTS `vtiger_ws_entity_fieldtype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ws_entity_fieldtype` (
  `fieldtypeid` int(19) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(50) NOT NULL,
  `field_name` varchar(50) NOT NULL,
  `fieldtype` varchar(200) NOT NULL,
  PRIMARY KEY (`fieldtypeid`),
  UNIQUE KEY `vtiger_idx_1_tablename_fieldname` (`table_name`,`field_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ws_entity_fieldtype`
--

LOCK TABLES `vtiger_ws_entity_fieldtype` WRITE;
/*!40000 ALTER TABLE `vtiger_ws_entity_fieldtype` DISABLE KEYS */;
INSERT INTO `vtiger_ws_entity_fieldtype` VALUES (1,'vtiger_attachmentsfolder','createdby','reference'),(2,'vtiger_organizationdetails','logoname','file'),(3,'vtiger_organizationdetails','phone','phone'),(4,'vtiger_organizationdetails','fax','phone'),(5,'vtiger_organizationdetails','website','url');
/*!40000 ALTER TABLE `vtiger_ws_entity_fieldtype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ws_entity_fieldtype_seq`
--

DROP TABLE IF EXISTS `vtiger_ws_entity_fieldtype_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ws_entity_fieldtype_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ws_entity_fieldtype_seq`
--

LOCK TABLES `vtiger_ws_entity_fieldtype_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_ws_entity_fieldtype_seq` DISABLE KEYS */;
INSERT INTO `vtiger_ws_entity_fieldtype_seq` VALUES (5);
/*!40000 ALTER TABLE `vtiger_ws_entity_fieldtype_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ws_entity_name`
--

DROP TABLE IF EXISTS `vtiger_ws_entity_name`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ws_entity_name` (
  `entity_id` int(11) NOT NULL,
  `name_fields` varchar(50) NOT NULL,
  `index_field` varchar(50) NOT NULL,
  `table_name` varchar(50) NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ws_entity_name`
--

LOCK TABLES `vtiger_ws_entity_name` WRITE;
/*!40000 ALTER TABLE `vtiger_ws_entity_name` DISABLE KEYS */;
INSERT INTO `vtiger_ws_entity_name` VALUES (20,'groupname','groupid','vtiger_groups'),(21,'currency_name','id','vtiger_currency_info'),(22,'foldername','folderid','vtiger_attachmentsfolder'),(23,'organizationname','groupid','vtiger_organizationdetails');
/*!40000 ALTER TABLE `vtiger_ws_entity_name` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ws_entity_referencetype`
--

DROP TABLE IF EXISTS `vtiger_ws_entity_referencetype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ws_entity_referencetype` (
  `fieldtypeid` int(19) NOT NULL,
  `type` varchar(25) NOT NULL,
  PRIMARY KEY (`fieldtypeid`,`type`),
  CONSTRAINT `vtiger_fk_1_actors_referencetype` FOREIGN KEY (`fieldtypeid`) REFERENCES `vtiger_ws_entity_fieldtype` (`fieldtypeid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ws_entity_referencetype`
--

LOCK TABLES `vtiger_ws_entity_referencetype` WRITE;
/*!40000 ALTER TABLE `vtiger_ws_entity_referencetype` DISABLE KEYS */;
INSERT INTO `vtiger_ws_entity_referencetype` VALUES (5,'Users');
/*!40000 ALTER TABLE `vtiger_ws_entity_referencetype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ws_entity_seq`
--

DROP TABLE IF EXISTS `vtiger_ws_entity_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ws_entity_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ws_entity_seq`
--

LOCK TABLES `vtiger_ws_entity_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_ws_entity_seq` DISABLE KEYS */;
INSERT INTO `vtiger_ws_entity_seq` VALUES (40);
/*!40000 ALTER TABLE `vtiger_ws_entity_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ws_entity_tables`
--

DROP TABLE IF EXISTS `vtiger_ws_entity_tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ws_entity_tables` (
  `webservice_entity_id` int(11) NOT NULL,
  `table_name` varchar(50) NOT NULL,
  PRIMARY KEY (`webservice_entity_id`,`table_name`),
  CONSTRAINT `fk_1_vtiger_ws_actor_tables` FOREIGN KEY (`webservice_entity_id`) REFERENCES `vtiger_ws_entity` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ws_entity_tables`
--

LOCK TABLES `vtiger_ws_entity_tables` WRITE;
/*!40000 ALTER TABLE `vtiger_ws_entity_tables` DISABLE KEYS */;
INSERT INTO `vtiger_ws_entity_tables` VALUES (20,'vtiger_groups'),(21,'vtiger_currency_info'),(22,'vtiger_attachmentsfolder'),(23,'vtiger_organizationdetails');
/*!40000 ALTER TABLE `vtiger_ws_entity_tables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ws_fieldinfo`
--

DROP TABLE IF EXISTS `vtiger_ws_fieldinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ws_fieldinfo` (
  `id` varchar(64) NOT NULL,
  `property_name` varchar(32) DEFAULT NULL,
  `property_value` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ws_fieldinfo`
--

LOCK TABLES `vtiger_ws_fieldinfo` WRITE;
/*!40000 ALTER TABLE `vtiger_ws_fieldinfo` DISABLE KEYS */;
INSERT INTO `vtiger_ws_fieldinfo` VALUES ('vtiger_organizationdetails.organization_id','upload.path','1');
/*!40000 ALTER TABLE `vtiger_ws_fieldinfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ws_fieldtype`
--

DROP TABLE IF EXISTS `vtiger_ws_fieldtype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ws_fieldtype` (
  `fieldtypeid` int(19) NOT NULL AUTO_INCREMENT,
  `uitype` varchar(30) NOT NULL,
  `fieldtype` varchar(200) NOT NULL,
  PRIMARY KEY (`fieldtypeid`),
  UNIQUE KEY `uitype_idx` (`uitype`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ws_fieldtype`
--

LOCK TABLES `vtiger_ws_fieldtype` WRITE;
/*!40000 ALTER TABLE `vtiger_ws_fieldtype` DISABLE KEYS */;
INSERT INTO `vtiger_ws_fieldtype` VALUES (1,'15','picklist'),(2,'16','picklist'),(3,'19','text'),(4,'20','text'),(5,'21','text'),(6,'24','text'),(7,'3','autogenerated'),(8,'11','phone'),(9,'33','multipicklist'),(10,'17','url'),(11,'85','skype'),(12,'56','boolean'),(13,'156','boolean'),(14,'53','owner'),(15,'61','file'),(16,'28','file'),(17,'13','email'),(18,'71','currency'),(19,'72','currency'),(20,'50','datetime'),(21,'51','reference'),(22,'57','reference'),(23,'58','reference'),(24,'73','reference'),(25,'75','reference'),(26,'76','reference'),(27,'78','reference'),(28,'80','reference'),(29,'81','reference'),(30,'101','reference'),(31,'52','reference'),(32,'357','reference'),(33,'59','reference'),(34,'66','reference'),(35,'77','reference'),(36,'68','reference'),(37,'117','reference'),(38,'26','reference'),(39,'10','reference'),(40,'3313','multipicklist'),(41,'1613','picklist'),(42,'14','time'),(43,'1024','picklist'),(44,'1614','picklist'),(45,'1615','picklist'),(46,'1025','multireference');
/*!40000 ALTER TABLE `vtiger_ws_fieldtype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ws_operation`
--

DROP TABLE IF EXISTS `vtiger_ws_operation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ws_operation` (
  `operationid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `handler_path` varchar(255) NOT NULL,
  `handler_method` varchar(64) NOT NULL,
  `type` varchar(8) NOT NULL,
  `prelogin` int(3) NOT NULL,
  PRIMARY KEY (`operationid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ws_operation`
--

LOCK TABLES `vtiger_ws_operation` WRITE;
/*!40000 ALTER TABLE `vtiger_ws_operation` DISABLE KEYS */;
INSERT INTO `vtiger_ws_operation` VALUES (1,'login','include/Webservices/Login.php','vtws_login','POST',1),(2,'retrieve','include/Webservices/Retrieve.php','vtws_retrieve','GET',0),(3,'create','include/Webservices/Create.php','vtws_create','POST',0),(4,'update','include/Webservices/Update.php','vtws_update','POST',0),(5,'delete','include/Webservices/Delete.php','vtws_delete','POST',0),(6,'sync','include/Webservices/GetUpdates.php','vtws_sync','GET',0),(7,'query','include/Webservices/Query.php','vtws_query','GET',0),(8,'logout','include/Webservices/Logout.php','vtws_logout','POST',0),(9,'listtypes','include/Webservices/ModuleTypes.php','vtws_listtypes','GET',0),(10,'getchallenge','include/Webservices/AuthToken.php','vtws_getchallenge','GET',1),(11,'describe','include/Webservices/DescribeObject.php','vtws_describe','GET',0),(12,'extendsession','include/Webservices/ExtendSession.php','vtws_extendSession','POST',1),(13,'convertlead','include/Webservices/ConvertLead.php','vtws_convertlead','POST',0),(14,'revise','include/Webservices/Revise.php','vtws_revise','POST',0),(15,'changePassword','include/Webservices/ChangePassword.php','vtws_changePassword','POST',0),(16,'deleteUser','include/Webservices/DeleteUser.php','vtws_deleteUser','POST',0),(17,'mobile.fetchallalerts','modules/Mobile/api/wsapi.php','mobile_ws_fetchAllAlerts','POST',0),(18,'mobile.alertdetailswithmessage','modules/Mobile/api/wsapi.php','mobile_ws_alertDetailsWithMessage','POST',0),(19,'mobile.fetchmodulefilters','modules/Mobile/api/wsapi.php','mobile_ws_fetchModuleFilters','POST',0),(20,'mobile.fetchrecord','modules/Mobile/api/wsapi.php','mobile_ws_fetchRecord','POST',0),(21,'mobile.fetchrecordwithgrouping','modules/Mobile/api/wsapi.php','mobile_ws_fetchRecordWithGrouping','POST',0),(22,'mobile.filterdetailswithcount','modules/Mobile/api/wsapi.php','mobile_ws_filterDetailsWithCount','POST',0),(23,'mobile.listmodulerecords','modules/Mobile/api/wsapi.php','mobile_ws_listModuleRecords','POST',0),(24,'mobile.saverecord','modules/Mobile/api/wsapi.php','mobile_ws_saveRecord','POST',0),(25,'mobile.syncModuleRecords','modules/Mobile/api/wsapi.php','mobile_ws_syncModuleRecords','POST',0),(26,'mobile.query','modules/Mobile/api/wsapi.php','mobile_ws_query','POST',0),(27,'mobile.querywithgrouping','modules/Mobile/api/wsapi.php','mobile_ws_queryWithGrouping','POST',0),(28,'wsapp_register','modules/WSAPP/api/ws/Register.php','wsapp_register','POST',0),(29,'wsapp_deregister','modules/WSAPP/api/ws/DeRegister.php','wsapp_deregister','POST',0),(30,'wsapp_get','modules/WSAPP/api/ws/Get.php','wsapp_get','POST',0),(31,'wsapp_put','modules/WSAPP/api/ws/Put.php','wsapp_put','POST',0),(32,'wsapp_map','modules/WSAPP/api/ws/Map.php','wsapp_map','POST',0),(33,'authenticateContact','include/Webservices/CustomerPortalWS.php','vtws_AuthenticateContact','POST',0),(34,'changePortalUserPassword','include/Webservices/CustomerPortalWS.php','vtws_changePortalUserPassword','POST',0),(35,'getPortalUserDateFormat','include/Webservices/CustomerPortalWS.php','vtws_getPortalUserDateFormat','POST',0),(36,'getPortalUserInfo','include/Webservices/CustomerPortalWS.php','vtws_getPortalUserInfo','POST',0),(37,'vtyiicpng_getWSEntityId','include/Webservices/CustomerPortalWS.php','vtyiicpng_getWSEntityId','POST',0),(38,'getReferenceValue','include/Webservices/CustomerPortalWS.php','vtws_getReferenceValue','POST',0),(39,'getSearchResults','include/Webservices/CustomerPortalWS.php','vtws_getSearchResults','POST',0),(40,'loginPortal','include/Webservices/LoginPortal.php','vtws_loginportal','GET',1),(41,'addTicketFaqComment','include/Webservices/addTicketFaqComment.php','vtws_addTicketFaqComment','POST',0),(42,'findByPortalUserName','include/Webservices/CustomerPortalWS.php','vtws_findByPortalUserName','POST',0),(43,'getfilterfields','include/Webservices/GetFilterFields.php','vtws_getfilterfields','POST',0),(44,'getPicklistValues','include/Webservices/CustomerPortalWS.php','vtws_getPicklistValues','POST',0),(45,'gettranslation','include/Webservices/GetTranslation.php','vtws_gettranslation','POST',0),(46,'getUItype','include/Webservices/CustomerPortalWS.php','vtws_getUItype','POST',0),(47,'getUsersInSameGroup','include/Webservices/CustomerPortalWS.php','vtws_getUsersInTheSameGroup','POST',0),(48,'getEntityNum','include/Webservices/getentitynum.php','vtws_get_entitynum','POST',0),(49,'sendRecoverPassword','include/Webservices/CustomerPortalWS.php','vtws_sendRecoverPassword','POST',0),(50,'retrievedocattachment','include/Webservices/RetrieveDocAttachment.php','vtws_retrievedocattachment','POST',0),(51,'getpdfdata','include/Webservices/GetPDFData.php','cbws_getpdfdata','POST',0),(52,'getRelatedRecords','include/Webservices/GetRelatedRecords.php','getRelatedRecords','POST',0),(53,'SetRelation','include/Webservices/SetRelation.php','vtws_setrelation','POST',0),(54,'getAssignedUserList','include/Webservices/CustomerPortalWS.php','vtws_getAssignedUserList','GET',0),(55,'getFieldAutocomplete','include/Webservices/CustomerPortalWS.php','getFieldAutocomplete','GET',0),(56,'getMaxLoadSize','include/Webservices/getmaxloadsize.php','get_maxloadsize','POST',0),(57,'getReferenceAutocomplete','include/Webservices/CustomerPortalWS.php','getReferenceAutocomplete','GET',0),(58,'SearchGlobalVar','modules/GlobalVariable/SearchGlobalVarws.php','cbws_SearchGlobalVar','GET',0),(59,'getProductImages','include/Webservices/getProductImages.php','cbws_getproductimageinfo','GET',0),(60,'getRecordImages','include/Webservices/getRecordImages.php','cbws_getrecordimageinfo','GET',0),(61,'addProductImages','include/Webservices/UploadProductImages.php','cbws_uploadProductImages','POST',0);
/*!40000 ALTER TABLE `vtiger_ws_operation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ws_operation_parameters`
--

DROP TABLE IF EXISTS `vtiger_ws_operation_parameters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ws_operation_parameters` (
  `operationid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `type` varchar(64) NOT NULL,
  `sequence` int(11) NOT NULL,
  PRIMARY KEY (`operationid`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ws_operation_parameters`
--

LOCK TABLES `vtiger_ws_operation_parameters` WRITE;
/*!40000 ALTER TABLE `vtiger_ws_operation_parameters` DISABLE KEYS */;
INSERT INTO `vtiger_ws_operation_parameters` VALUES (1,'accessKey','String',2),(1,'username','String',1),(2,'id','String',1),(3,'element','encoded',2),(3,'elementType','String',1),(4,'element','encoded',1),(5,'id','String',1),(6,'elementType','String',2),(6,'modifiedTime','DateTime',1),(7,'query','String',1),(8,'sessionName','String',1),(9,'fieldTypeList','encoded',1),(10,'username','String',1),(11,'elementType','String',1),(13,'element','encoded',1),(14,'element','Encoded',1),(15,'confirmPassword','String',4),(15,'id','String',1),(15,'newPassword','String',3),(15,'oldPassword','String',2),(16,'id','String',1),(16,'newOwnerId','String',2),(18,'alertid','string',1),(19,'module','string',1),(20,'record','string',1),(21,'record','string',1),(22,'filterid','string',1),(23,'elements','encoded',1),(24,'module','string',1),(24,'record','string',2),(24,'values','encoded',3),(25,'module','string',1),(25,'page','string',3),(25,'syncToken','string',2),(26,'module','string',1),(26,'page','string',3),(26,'query','string',2),(27,'module','string',1),(27,'page','string',3),(27,'query','string',2),(28,'synctype','string',2),(28,'type','string',1),(29,'key','string',2),(29,'type','string',1),(30,'key','string',1),(30,'module','string',2),(30,'token','string',3),(31,'element','encoded',2),(31,'key','string',1),(32,'element','encoded',2),(32,'key','string',1),(33,'email','string',1),(33,'password','string',2),(34,'email','String',1),(34,'password','String',2),(37,'entityName','String',1),(38,'id','String',1),(39,'query','string',1),(39,'restrictionids','encoded',3),(39,'search_onlyin','string',2),(40,'password','string',2),(40,'username','string',1),(41,'id','string',1),(41,'values','encoded',2),(42,'username','string',1),(43,'module','string',1),(44,'module','string',1),(45,'language','string',2),(45,'module','string',3),(45,'totranslate','encoded',1),(46,'module','string',1),(47,'id','string',1),(49,'username','string',1),(50,'id','string',1),(50,'returnfile','string',2),(51,'id','String',1),(52,'id','String',1),(52,'module','String',2),(52,'queryParameters','encoded',4),(52,'relatedModule','String',3),(53,'relate_this_id','String',1),(53,'with_these_ids','encoded',2),(54,'module','string',1),(55,'fields','String',4),(55,'filter','String',2),(55,'limit','String',6),(55,'returnfields','String',5),(55,'searchinmodule','String',3),(55,'term','String',1),(57,'filter','String',2),(57,'limit','String',4),(57,'searchinmodules','String',3),(57,'term','String',1),(58,'defaultvalue','string',2),(58,'gvmodule','string',3),(58,'gvname','string',1),(59,'id','String',1),(60,'id','String',1),(61,'files','encoded',2),(61,'id','String',1);
/*!40000 ALTER TABLE `vtiger_ws_operation_parameters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ws_operation_seq`
--

DROP TABLE IF EXISTS `vtiger_ws_operation_seq`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ws_operation_seq` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ws_operation_seq`
--

LOCK TABLES `vtiger_ws_operation_seq` WRITE;
/*!40000 ALTER TABLE `vtiger_ws_operation_seq` DISABLE KEYS */;
INSERT INTO `vtiger_ws_operation_seq` VALUES (61);
/*!40000 ALTER TABLE `vtiger_ws_operation_seq` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ws_referencetype`
--

DROP TABLE IF EXISTS `vtiger_ws_referencetype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ws_referencetype` (
  `fieldtypeid` int(19) NOT NULL,
  `type` varchar(25) NOT NULL,
  PRIMARY KEY (`fieldtypeid`,`type`),
  CONSTRAINT `fk_1_vtiger_referencetype` FOREIGN KEY (`fieldtypeid`) REFERENCES `vtiger_ws_fieldtype` (`fieldtypeid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ws_referencetype`
--

LOCK TABLES `vtiger_ws_referencetype` WRITE;
/*!40000 ALTER TABLE `vtiger_ws_referencetype` DISABLE KEYS */;
INSERT INTO `vtiger_ws_referencetype` VALUES (21,'Accounts'),(22,'Contacts'),(23,'Campaigns'),(24,'Accounts'),(25,'Vendors'),(26,'Potentials'),(27,'Quotes'),(28,'SalesOrder'),(29,'Vendors'),(30,'Users'),(31,'Users'),(32,'Accounts'),(32,'Contacts'),(32,'Leads'),(32,'Users'),(32,'Vendors'),(33,'Products'),(34,'Accounts'),(34,'Campaigns'),(34,'HelpDesk'),(34,'Leads'),(34,'Potentials'),(34,'Vendors'),(35,'Users'),(36,'Accounts'),(36,'Contacts'),(37,'Currency'),(38,'DocumentFolders');
/*!40000 ALTER TABLE `vtiger_ws_referencetype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_ws_userauthtoken`
--

DROP TABLE IF EXISTS `vtiger_ws_userauthtoken`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_ws_userauthtoken` (
  `userid` int(19) NOT NULL,
  `token` varchar(36) NOT NULL,
  `expiretime` int(19) NOT NULL,
  PRIMARY KEY (`userid`,`expiretime`),
  UNIQUE KEY `userid_idx` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_ws_userauthtoken`
--

LOCK TABLES `vtiger_ws_userauthtoken` WRITE;
/*!40000 ALTER TABLE `vtiger_ws_userauthtoken` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_ws_userauthtoken` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_wsapp`
--

DROP TABLE IF EXISTS `vtiger_wsapp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_wsapp` (
  `appid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `appkey` varchar(255) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`appid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_wsapp`
--

LOCK TABLES `vtiger_wsapp` WRITE;
/*!40000 ALTER TABLE `vtiger_wsapp` DISABLE KEYS */;
INSERT INTO `vtiger_wsapp` VALUES (1,'vtigerCRM','54340b87980fb','user');
/*!40000 ALTER TABLE `vtiger_wsapp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_wsapp_handlerdetails`
--

DROP TABLE IF EXISTS `vtiger_wsapp_handlerdetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_wsapp_handlerdetails` (
  `type` varchar(200) NOT NULL,
  `handlerclass` varchar(100) DEFAULT NULL,
  `handlerpath` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_wsapp_handlerdetails`
--

LOCK TABLES `vtiger_wsapp_handlerdetails` WRITE;
/*!40000 ALTER TABLE `vtiger_wsapp_handlerdetails` DISABLE KEYS */;
INSERT INTO `vtiger_wsapp_handlerdetails` VALUES ('Outlook','OutlookHandler','modules/WSAPP/Handlers/OutlookHandler.php'),('vtigerCRM','vtigerCRMHandler','modules/WSAPP/Handlers/vtigerCRMHandler.php');
/*!40000 ALTER TABLE `vtiger_wsapp_handlerdetails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_wsapp_queuerecords`
--

DROP TABLE IF EXISTS `vtiger_wsapp_queuerecords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_wsapp_queuerecords` (
  `syncserverid` int(19) DEFAULT NULL,
  `details` varchar(300) DEFAULT NULL,
  `flag` varchar(100) DEFAULT NULL,
  `appid` int(19) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_wsapp_queuerecords`
--

LOCK TABLES `vtiger_wsapp_queuerecords` WRITE;
/*!40000 ALTER TABLE `vtiger_wsapp_queuerecords` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_wsapp_queuerecords` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_wsapp_recordmapping`
--

DROP TABLE IF EXISTS `vtiger_wsapp_recordmapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_wsapp_recordmapping` (
  `id` int(19) NOT NULL AUTO_INCREMENT,
  `serverid` varchar(10) DEFAULT NULL,
  `clientid` varchar(255) DEFAULT NULL,
  `clientmodifiedtime` datetime DEFAULT NULL,
  `appid` int(11) DEFAULT NULL,
  `servermodifiedtime` datetime DEFAULT NULL,
  `serverappid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wsapp_recordmapping_appid_idx` (`appid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_wsapp_recordmapping`
--

LOCK TABLES `vtiger_wsapp_recordmapping` WRITE;
/*!40000 ALTER TABLE `vtiger_wsapp_recordmapping` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_wsapp_recordmapping` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vtiger_wsapp_sync_state`
--

DROP TABLE IF EXISTS `vtiger_wsapp_sync_state`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vtiger_wsapp_sync_state` (
  `id` int(19) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `stateencodedvalues` varchar(300) NOT NULL,
  `userid` int(19) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wsapp_sync_state_userid_idx` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vtiger_wsapp_sync_state`
--

LOCK TABLES `vtiger_wsapp_sync_state` WRITE;
/*!40000 ALTER TABLE `vtiger_wsapp_sync_state` DISABLE KEYS */;
/*!40000 ALTER TABLE `vtiger_wsapp_sync_state` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-09-02 12:01:36
