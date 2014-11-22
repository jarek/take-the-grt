/* this is the output of MySQL's SHOW CREATE TABLE and so is MySQL-specific, 
   but should be adaptable to other SQL databases easily
   (removing ENGINE=InnoDB would probably be the first step) */

CREATE TABLE `Service` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `sort` int(11) NOT NULL,
  `start` datetime NOT NULL,
  `stop` datetime NOT NULL,
  `weekday` tinyint(1) NOT NULL,
  `saturday` tinyint(1) NOT NULL,
  `sunday` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

CREATE TABLE `Route` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `short_name` varchar(8) NOT NULL,
  `long_name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `short_name` (`short_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

CREATE TABLE `RouteDirection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `route_id` int(11) NOT NULL,
  `direction` int(11) NOT NULL,
  `description` varchar(512) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `route_id` (`route_id`),
  CONSTRAINT `RouteDirection_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `Route` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

CREATE TABLE `Stop` (
  `code` int(11) NOT NULL,
  `latitude` float NOT NULL,
  `longitude` float NOT NULL,
  `name` varchar(256) DEFAULT NULL,
  `friendlyname` varchar(256) DEFAULT NULL,
  `direction` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

CREATE TABLE `RouteDirectionStop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `route_direction_id` int(11) NOT NULL,
  `stop_id` int(11) NOT NULL,
  `sequence` int(11) NOT NULL,
  `timepoint` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `route_direction_id` (`route_direction_id`,`stop_id`),
  KEY `stop_id` (`stop_id`),
  CONSTRAINT `RouteDirectionStop_ibfk_1` FOREIGN KEY (`route_direction_id`) REFERENCES `RouteDirection` (`id`),
  CONSTRAINT `RouteDirectionStop_ibfk_2` FOREIGN KEY (`stop_id`) REFERENCES `Stop` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

CREATE TABLE `Trip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `route_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `headsign` varchar(128) NOT NULL,
  `direction` set('0','1') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `service_id` (`service_id`),
  KEY `route_id` (`route_id`),
  CONSTRAINT `Trip_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `Route` (`id`),
  CONSTRAINT `Trip_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `Service` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

CREATE TABLE `StopTime` (
  `trip_id` int(11) NOT NULL,
  `stop_id` int(11) NOT NULL,
  `time` time NOT NULL,
  KEY `stop_id` (`stop_id`),
  KEY `trip_id` (`trip_id`),
  CONSTRAINT `StopTime_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `Trip` (`id`),
  CONSTRAINT `StopTime_ibfk_2` FOREIGN KEY (`stop_id`) REFERENCES `Stop` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
