CREATE TABLE IF NOT EXISTS `instances` (
  `db_id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` varchar(10) NOT NULL,
  `hostname` varchar(50) NOT NULL,
  `refreshed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`db_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=388 ;


CREATE TABLE IF NOT EXISTS `notification_fra_assigement` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `server_id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`notification_id`,`server_id`),
  KEY `server_id` (`server_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;


CREATE TABLE IF NOT EXISTS `notification_switchover` (
  `notificaton_id` int(11) NOT NULL AUTO_INCREMENT,
  `db_id_rz1` int(11) NOT NULL,
  `db_id_rz2` int(11) NOT NULL,
  `so_time` varchar(20) NOT NULL,
  PRIMARY KEY (`notificaton_id`,`db_id_rz1`,`db_id_rz2`),
  KEY `db_id_rz1` (`db_id_rz1`),
  KEY `db_id_rz2` (`db_id_rz2`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;


CREATE TABLE IF NOT EXISTS `parameter` (
  `parameter_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(36) DEFAULT NULL,
  `type` int(1) DEFAULT NULL,
  `description` varchar(121) DEFAULT NULL,
  `default_value` varchar(189) DEFAULT NULL,
  `skip_last_parameter_overview` varchar(10) NOT NULL DEFAULT 'false',
  PRIMARY KEY (`parameter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `server` (
  `server_id` int(11) NOT NULL AUTO_INCREMENT,
  `hostname` varchar(100) NOT NULL,
  `test_system` varchar(10) NOT NULL DEFAULT 'false',
  `refreshed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`server_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=111 ;

CREATE TABLE IF NOT EXISTS `settings` (
  `key` varchar(50) NOT NULL,
  `value` varchar(50) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `system_parameter` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `server_id` int(11) NOT NULL,
  `parameter_name` varchar(200) NOT NULL,
  `value_string` varchar(1000) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`,`server_id`),
  KEY `server_id` (`server_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=266749 ;


CREATE TABLE IF NOT EXISTS `user` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT 'not defined',
  `displayname` varchar(100) NOT NULL DEFAULT 'not defined',
  `Location` varchar(10) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;



CREATE TABLE IF NOT EXISTS `values` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `db_id` int(11) NOT NULL,
  `parameter_id` int(11) NOT NULL,
  `value_string` varchar(2000) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dba_notice` varchar(1000) NOT NULL,
  PRIMARY KEY (`ID`,`db_id`,`parameter_id`),
  KEY `parameter_id` (`parameter_id`),
  KEY `db_id` (`db_id`,`parameter_id`,`time`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=847776 ;


ALTER TABLE `notification_fra_assigement`
  ADD CONSTRAINT `notification_fra_assigement_ibfk_1` FOREIGN KEY (`server_id`) REFERENCES `server` (`server_id`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `notification_switchover`
  ADD CONSTRAINT `notification_switchover_ibfk_1` FOREIGN KEY (`db_id_rz1`) REFERENCES `instances` (`db_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notification_switchover_ibfk_2` FOREIGN KEY (`db_id_rz2`) REFERENCES `instances` (`db_id`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `system_parameter`
  ADD CONSTRAINT `system_parameter_ibfk_1` FOREIGN KEY (`server_id`) REFERENCES `server` (`server_id`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `values`
  ADD CONSTRAINT `values_ibfk_1` FOREIGN KEY (`db_id`) REFERENCES `instances` (`db_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `values_ibfk_2` FOREIGN KEY (`parameter_id`) REFERENCES `parameter` (`parameter_id`) ON DELETE CASCADE ON UPDATE CASCADE;
