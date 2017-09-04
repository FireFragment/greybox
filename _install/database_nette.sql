CREATE TABLE `badge` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(30) COLLATE 'utf8_czech_ci' NOT NULL,
  `requiredIB` smallint NOT NULL
) ENGINE='InnoDB' COLLATE 'utf8_czech_ci';

AlTER TABLE `badge`
ADD UNIQUE `requiredIB` (`requiredIB`);

CREATE TABLE `role` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(30) COLLATE 'utf8_czech_ci' NOT NULL,
  `value` tinyint NOT NULL
) ENGINE='InnoDB' COLLATE 'utf8_czech_ci';

AlTER TABLE `role`
ADD UNIQUE `value` (`value`);

ALTER TABLE `clovek`
ADD `badge` int(11) NULL AFTER `dostal`,
ADD `received_badge` bit NULL DEFAULT 1 AFTER `badge`,
ADD `role` int(11) NULL AFTER `received_badge`,
ADD FOREIGN KEY (`badge`) REFERENCES `badge` (`id`),
ADD FOREIGN KEY (`role`) REFERENCES `role` (`id`);