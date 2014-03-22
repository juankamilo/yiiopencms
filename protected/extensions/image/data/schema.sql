/*
	Image database schematic.
	Author Christoffer Niska <ChristofferNiska@gmail.com>
	Copyright (c) 2012, Christoffer Niska
 */

CREATE TABLE `image` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`ownerId` int(10) unsigned NOT NULL,
	`owner` varchar(255) NOT NULL,
	`name` varchar(255) NOT NULL,
	`path` varchar(255) NOT NULL,
	`extension` varchar(255) NOT NULL,
	`filename` varchar(255) NOT NULL,
	`byteSize` int(10) unsigned NOT NULL,
	`mimeType` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;