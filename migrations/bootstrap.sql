--liquibase formatted sql

-- DO NOT change the changeset names!
-- We WANT liquibase to complain about a checksum changing.

--changeset notifications:notifications
CREATE TABLE `applicants` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `nickname` varchar(255) NOT NULL DEFAULT '',
  `contact_email` varchar(255) NOT NULL DEFAULT '',
  `list_email` varchar(255) NOT NULL DEFAULT '',
  `bio_reason` text NOT NULL,
  `sponsor` varchar(255) DEFAULT NULL,
  `second_sponsor` varchar(255) DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `heard_from` text,
  `profile_hash` char(40) DEFAULT NULL COMMENT 'For profile image lookup',
  `website` varchar(255) DEFAULT NULL,
  `token_type` varchar(8) NOT NULL,
  `username` varchar(255) NOT NULL,
  `completed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nickname` (`nickname`),
  KEY `profile_hash` (`profile_hash`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
