CREATE TABLE IF NOT EXISTS `#__ablog_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `level` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `access` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `path` varchar(255) NOT NULL DEFAULT '',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL,
  `published` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_left_right` (`lft`,`rgt`)
) DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__ablog_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_date` datetime NOT NULL,
  `email_adress` varchar(120) NOT NULL,
  `creator` varchar(100) NOT NULL,
  `post_creator` int(11) NOT NULL,
  `content` text NOT NULL,
  `comment_answer_id` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `#__ablog_comment_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `creator` varchar(100) NOT NULL,
  `email_adress` varchar(100) NOT NULL,
  `created_date` datetime NOT NULL,
  `published` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `#__ablog_posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_date` datetime DEFAULT NULL,
  `title` varchar(50) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL,
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `creator` varchar(100) NOT NULL,
  `creator_username` varchar(100) NOT NULL,
  `creator_id` int(11) NOT NULL,
  `categorie_id` int(11) NOT NULL,
  `trashed` int(11) NOT NULL,
  `publish_start` datetime NOT NULL,
  `publish_stop`datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


INSERT INTO `#__ablog_categories`(`id`, `parent_id`, `lft`, `rgt`, `level`, `title`, `alias`,`access`,`path`) 
VALUES(1,0,0,1,0,'root','root',0,'root');
