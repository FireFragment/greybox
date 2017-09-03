CREATE TABLE `badge` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(30) COLLATE 'utf8_czech_ci' NOT NULL,
  `requiredIB` smallint NOT NULL
) ENGINE='InnoDB' COLLATE 'utf8_czech_ci';

AlTER TABLE `badge`
ADD UNIQUE `requiredIB` (`requiredIB`);

ALTER TABLE `clovek`
ADD `badge` int(11) NULL AFTER `dostal`,
ADD `received_badge` bit NULL DEFAULT 1 AFTER `badge`,
ADD FOREIGN KEY (`badge`) REFERENCES `badge` (`id`);