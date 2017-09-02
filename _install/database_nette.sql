CREATE TABLE `badge` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(30) COLLATE 'utf8_czech_ci' NOT NULL,
  `requiredIB` smallint NOT NULL
) ENGINE='InnoDB' COLLATE 'utf8_czech_ci';

AlTER TABLE `badge`
ADD UNIQUE `requiredIB` (`requiredIB`);