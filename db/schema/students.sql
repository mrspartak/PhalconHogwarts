CREATE TABLE `students` (
  `uid` int(10) unsigned NOT NULL,
  `faculty` tinyint(3) DEFAULT NULL,
  `fn` varchar(20) NOT NULL,
  `ln` varchar(20) NOT NULL,
  `userpic` varchar(256) NOT NULL,
  `admin` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `faculty` (`faculty`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8