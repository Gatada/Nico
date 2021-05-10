-- version 1.0
-- https://basberg.com/2021/05/08/online-diary/

CREATE TABLE `users` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `email` varchar(25) NOT NULL,
   `password` varchar(100) NOT NULL,
   `message` text,
   `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
   `isPublished` tinyint(4) NOT NULL DEFAULT '0',
   `isAdmin` tinyint(4) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`)
  ) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4