--
-- Table structure for table `staff_session`
--

CREATE TABLE IF NOT EXISTS `staff_session` (
  `token` char(40) COLLATE ascii_bin NOT NULL,
  `staff_id` int(10) unsigned NOT NULL,
  `remote_ip` varchar(255) COLLATE ascii_bin NOT NULL,
  `user_agent` varchar(255) COLLATE ascii_bin NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`token`),
  KEY `staff_id` (`staff_id`),
  KEY `expires` (`expires`,`is_active`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii COLLATE=ascii_bin;
