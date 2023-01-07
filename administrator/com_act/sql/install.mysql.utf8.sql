CREATE TABLE IF NOT EXISTS `#__act_route` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`name` VARCHAR(64)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`settergrade` VARCHAR(255)  NOT NULL ,
`color` TEXT NOT NULL ,
`line` TEXT NOT NULL ,
`setter` TEXT NOT NULL ,
`createdate` DATE NOT NULL ,
`info` TEXT NOT NULL ,
`infoadmin` TEXT NOT NULL ,
`sponsor` TEXT NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified` DATETIME NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`hit` VARCHAR(11)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__act_comment` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`state` TINYINT(1)  NOT NULL ,
`route` TEXT NOT NULL ,
`review_yn` VARCHAR(255)  NOT NULL ,
`stars` VARCHAR(255)  NOT NULL ,
`myroutegrade` VARCHAR(255)  NOT NULL ,
`comment` TEXT NOT NULL ,
`ticklist_yn` VARCHAR(255)  NOT NULL ,
`ascent` VARCHAR(255)  NOT NULL ,
`tries` DOUBLE,
`climbdate` DATETIME NOT NULL ,
`tick_comment` TEXT NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL ,
`modified` DATETIME NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__act_setter` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`category` TEXT NOT NULL ,
`lastname` VARCHAR(20)  NOT NULL ,
`firstname` VARCHAR(20)  NOT NULL ,
`settername` VARCHAR(64)  NOT NULL ,
`email` VARCHAR(255)  NOT NULL ,
`phone` VARCHAR(255)  NOT NULL ,
`image` VARCHAR(255)  NOT NULL ,
`info` TEXT NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`ordering` INT(11)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__act_color` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`color` VARCHAR(20)  NOT NULL ,
`rgbcode` VARCHAR(7)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__act_line` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`line` VARCHAR(3)  NOT NULL ,
`sector` TEXT NOT NULL ,
`building` VARCHAR(255)  NOT NULL ,
`inorout` VARCHAR(255)  NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__act_grade` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`uiaa` VARCHAR(3)  NOT NULL ,
`franzoesisch` VARCHAR(4)  NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__act_sponsor` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`name` VARCHAR(255)  NOT NULL ,
`media` VARCHAR(255)  NOT NULL ,
`url` VARCHAR(255)  NOT NULL ,
`txt` TEXT NOT NULL ,
`contact` VARCHAR(255)  NOT NULL ,
`email` VARCHAR(255)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`info` TEXT NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8mb4_unicode_ci;

