/*
SQLyog Community v12.12 (64 bit)
MySQL - 5.7.14 : Database - event_registration
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`event_registration` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `event_registration`;

/*Table structure for table `all_users` */

DROP TABLE IF EXISTS `all_users`;

CREATE TABLE `all_users` (
  `U_ID` int(10) NOT NULL,
  `U_EMAIL` varchar(25) NOT NULL,
  `U_PASSWD` varchar(25) DEFAULT NULL,
  `U_MFA` varchar(25) DEFAULT NULL,
  `U_SecurityQ` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`U_ID`,`U_EMAIL`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `all_users` */

/*Table structure for table `events` */

DROP TABLE IF EXISTS `events`;

CREATE TABLE `events` (
  `event_id` bigint(15) NOT NULL AUTO_INCREMENT,
  `user_id` int(15) NOT NULL,
  `event_title` varchar(100) NOT NULL,
  `event_description` text NOT NULL,
  `event_type` varchar(50) NOT NULL,
  `event_image` varchar(100) NOT NULL,
  `event_contact` bigint(15) NOT NULL,
  `event_starttime` datetime NOT NULL,
  `event_endtime` datetime NOT NULL,
  `event_status` tinyint(1) NOT NULL DEFAULT '1',
  `event_created` datetime NOT NULL,
  `event_modified` datetime NOT NULL,
  `event_address` varchar(100) DEFAULT NULL,
  `event_city` varchar(50) DEFAULT NULL,
  `event_state` varchar(50) DEFAULT NULL,
  `event_zipcode` bigint(20) DEFAULT NULL,
  `event_privacy` varchar(10) DEFAULT 'Public',
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `events` */

insert  into `events`(`event_id`,`user_id`,`event_title`,`event_description`,`event_type`,`event_image`,`event_contact`,`event_starttime`,`event_endtime`,`event_status`,`event_created`,`event_modified`,`event_address`,`event_city`,`event_state`,`event_zipcode`,`event_privacy`) values (1,0,'Birthday Celebration','Celebrating birthday for Vinodkumar','Birthday','photo3.jpg',9894946054,'2017-07-13 00:00:16','2017-07-13 10:00:33',0,'2017-07-12 17:37:51','2017-07-12 17:37:51',NULL,NULL,NULL,NULL,'Public'),(2,0,'Birthday','21st birthday celebrations','Birthday','HeroHonda-Bike-RC-Book-2.jpg',98949460564,'2017-07-20 22:36:54','2017-07-21 01:00:58',0,'2017-07-20 17:07:16','2017-07-20 17:07:16',NULL,NULL,NULL,NULL,'Public'),(3,0,'asdf','asdfasd','2','Truck-Towing-51.jpg',0,'2017-07-25 00:00:57','2017-07-25 01:00:59',1,'2017-07-24 18:42:27','2017-07-24 18:42:27','This is Online event','','',0,'Public'),(4,0,'asd','asdfas','15','ace-banner-03.jpg',0,'2017-07-25 02:00:19','2017-07-25 03:00:22',1,'2017-07-24 18:43:28','2017-07-24 18:43:28','This is Online event','','',0,'Private'),(5,0,'asdf','asdf ','16','Photo1962_20131201T182426-100.jpg',0,'2017-07-25 01:00:00','2017-07-25 04:00:02',1,'2017-07-24 18:44:12','2017-07-24 18:44:12','This is Online event','','',0,'Public');

/*Table structure for table `user_details` */

DROP TABLE IF EXISTS `user_details`;

CREATE TABLE `user_details` (
  `profile_id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) DEFAULT NULL,
  `first_name` varchar(30) DEFAULT NULL,
  `last_name` varchar(30) DEFAULT NULL,
  `gender` varchar(10) NOT NULL,
  `address` varchar(200) DEFAULT NULL,
  `country` varchar(30) DEFAULT NULL,
  `state` varchar(25) DEFAULT NULL,
  `city` varchar(20) DEFAULT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`profile_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `user_details` */

insert  into `user_details`(`profile_id`,`uid`,`first_name`,`last_name`,`gender`,`address`,`country`,`state`,`city`,`zipcode`,`phone`,`created`,`modified`) values (1,1,'Vinodkumar','SC','Male','No.23, Rajaji Street, Walajapet','India','TamilNadu','Vellore','632513','9894946054','2017-07-12 05:13:31','2017-07-12 05:13:31');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `status` enum('1','0') COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`email`,`password`,`created`,`modified`,`status`) values (1,'scvinodkumar.php@gmail.com','e6e061838856bf47e1de730719fb2609','2017-07-19 22:24:00','2017-07-12 05:13:31','1');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
