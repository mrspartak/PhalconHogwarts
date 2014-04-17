CREATE TABLE `work_done` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `student_from` int(10) unsigned NOT NULL,
  `student_to` int(10) unsigned NOT NULL,
  `work` smallint(5) unsigned NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `student_from` (`student_from`,`student_to`,`work`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8