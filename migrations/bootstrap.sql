--liquibase formatted sql

-- DO NOT change the changeset names!
-- We WANT liquibase to complain about a checksum changing.

--changeset applicants:applicants
CREATE TABLE `applicants` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `nickname` VARCHAR(255) NOT NULL DEFAULT '',
  `contact_email` VARCHAR(255) NOT NULL DEFAULT '',
  `list_email` VARCHAR(255) NOT NULL DEFAULT '',
  `bio_reason` TEXT NOT NULL,
  `sponsor` VARCHAR(255) DEFAULT NULL,
  `second_sponsor` VARCHAR(255) DEFAULT NULL,
  `picture` VARCHAR(255) DEFAULT NULL,
  `twitter` VARCHAR(255) DEFAULT NULL,
  `facebook` VARCHAR(255) DEFAULT NULL,
  `heard_from` TEXT,
  `profile_hash` char(40) DEFAULT NULL COMMENT 'For profile image lookup',
  `website` VARCHAR(255) DEFAULT NULL,
  `token_type` VARCHAR(8) NOT NULL,
  `username` VARCHAR(255) NOT NULL,
  `completed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nickname` (`nickname`),
  KEY `profile_hash` (`profile_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
