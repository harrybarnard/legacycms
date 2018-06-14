--
-- Table structure for table `articles`
--

CREATE TABLE IF NOT EXISTS `articles` (
  `article_id` int(11) NOT NULL AUTO_INCREMENT,
  `article_category` tinyint(4) NOT NULL DEFAULT '1',
  `article_date` datetime DEFAULT NULL,
  `article_title` text NOT NULL,
  `article_user` int(11) NOT NULL,
  `article_intro` text,
  `article_content` text,
  `article_comments` varchar(1) NOT NULL DEFAULT 'Y',
  `article_moderate` varchar(1) NOT NULL DEFAULT 'N',
  `article_sticky` varchar(1) NOT NULL DEFAULT 'N',
  `article_edit` datetime NOT NULL,
  `article_published` datetime DEFAULT NULL,
  `article_status` varchar(12) NOT NULL DEFAULT 'draft',
  PRIMARY KEY (`article_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=68 ;

--
-- Table structure for table `articles_categories`
--

CREATE TABLE IF NOT EXISTS `articles_categories` (
  `acat_id` int(11) NOT NULL AUTO_INCREMENT,
  `acat_title` varchar(60) NOT NULL,
  PRIMARY KEY (`acat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci AUTO_INCREMENT=45 ;

--
-- Dumping data for table `articles_categories`
--

INSERT INTO `articles_categories` (`acat_id`, `acat_title`) VALUES
(1, 'Uncategorised');

--
-- Table structure for table `assets`
--

CREATE TABLE IF NOT EXISTS `assets` (
  `asset_id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_key` varchar(64) NOT NULL,
  `asset_folder` int(11) NOT NULL,
  `asset_file` varchar(256) NOT NULL,
  `asset_extension` varchar(12) NOT NULL,
  `asset_name` varchar(256) NOT NULL,
  `asset_mime` varchar(64) NOT NULL,
  `asset_size` int(11) NOT NULL,
  `asset_user` int(11) DEFAULT NULL,
  `asset_date` datetime NOT NULL,
  `asset_modified` datetime NOT NULL,
  PRIMARY KEY (`asset_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci AUTO_INCREMENT=218 ;

--
-- Table structure for table `assets_folders`
--

CREATE TABLE IF NOT EXISTS `assets_folders` (
  `folder_id` int(11) NOT NULL AUTO_INCREMENT,
  `folder_parent` int(11) NOT NULL DEFAULT '0',
  `folder_name` varchar(256) NOT NULL,
  `folder_date` datetime NOT NULL,
  `folder_modify` datetime NOT NULL,
  UNIQUE KEY `folder_id` (`folder_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci AUTO_INCREMENT=46 ;

--
-- Table structure for table `attachments`
--

CREATE TABLE IF NOT EXISTS `attachments` (
  `attach_id` int(11) NOT NULL AUTO_INCREMENT,
  `attach_type` varchar(1) NOT NULL,
  `attach_slave` int(11) NOT NULL,
  `attach_asset` varchar(64) NOT NULL,
  PRIMARY KEY (`attach_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci AUTO_INCREMENT=29 ;

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_type` varchar(1) NOT NULL DEFAULT 'A',
  `comment_slave` int(11) NOT NULL DEFAULT '0',
  `comment_user` mediumint(9) NOT NULL DEFAULT '0',
  `comment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_content` text NOT NULL,
  `comment_approved` varchar(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`comment_id`),
  FULLTEXT KEY `comment_subject` (`comment_content`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=86 ;

--
-- Table structure for table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_venue` int(11) NOT NULL,
  `event_category` int(11) NOT NULL,
  `event_title` varchar(255) NOT NULL,
  `event_date` datetime DEFAULT NULL,
  `event_end` datetime DEFAULT NULL,
  `event_description` text,
  `event_url` varchar(255) DEFAULT NULL,
  `event_tickets` varchar(255) DEFAULT NULL,
  `event_user` int(11) NOT NULL,
  `event_comments` varchar(1) NOT NULL DEFAULT 'Y',
  `event_moderate` varchar(1) NOT NULL DEFAULT 'N',
  `event_published` datetime DEFAULT NULL,
  `event_status` varchar(16) NOT NULL DEFAULT 'draft',
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci AUTO_INCREMENT=189 ;

--
-- Table structure for table `events_categories`
--

CREATE TABLE IF NOT EXISTS `events_categories` (
  `ecat_id` int(11) NOT NULL AUTO_INCREMENT,
  `ecat_title` varchar(255) NOT NULL,
  `ecat_content` text NOT NULL,
  `ecat_description` text,
  `ecat_default` text NOT NULL,
  `ecat_venue` int(11) DEFAULT NULL,
  `ecat_status` varchar(12) NOT NULL DEFAULT 'draft',
  PRIMARY KEY (`ecat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=28 ;

--
-- Table structure for table `events_venues`
--

CREATE TABLE IF NOT EXISTS `events_venues` (
  `venue_id` int(11) NOT NULL AUTO_INCREMENT,
  `venue_title` varchar(255) NOT NULL,
  `venue_address` text NOT NULL,
  `venue_city` varchar(255) NOT NULL,
  `venue_country` varchar(255) NOT NULL DEFAULT 'United Kingdom',
  `venue_latitude` float(10,8) NOT NULL,
  `venue_longitude` float(10,8) NOT NULL,
  `venue_description` text,
  `venue_email` varchar(255) DEFAULT NULL,
  `venue_url` varchar(255) DEFAULT NULL,
  `venue_phone` varchar(32) DEFAULT NULL,
  `venue_published` datetime DEFAULT NULL,
  `venue_status` varchar(12) NOT NULL DEFAULT 'draft',
  PRIMARY KEY (`venue_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci AUTO_INCREMENT=57 ;

--
-- Table structure for table `mail`
--

CREATE TABLE IF NOT EXISTS `mail` (
  `mail_id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_type` varchar(1) NOT NULL DEFAULT 'G',
  `mail_slave` int(11) NOT NULL,
  `mail_date` datetime NOT NULL,
  `mail_user` int(11) NOT NULL,
  `mail_subject` text NOT NULL,
  `mail_text` text,
  `mail_html` text,
  `mail_sent` datetime DEFAULT NULL,
  `mail_status` varchar(12) NOT NULL DEFAULT 'draft',
  PRIMARY KEY (`mail_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci AUTO_INCREMENT=21 ;

--
-- Table structure for table `mail_groups`
--

CREATE TABLE IF NOT EXISTS `mail_groups` (
  `mgroup_id` int(11) NOT NULL AUTO_INCREMENT,
  `mgroup_title` text NOT NULL,
  `mgroup_description` text NOT NULL,
  `mgroup_text` text,
  `mgroup_html` text,
  `mgroup_open` varchar(1) NOT NULL DEFAULT 'Y',
  `mgroup_default` varchar(1) NOT NULL DEFAULT 'N',
  `mgroup_status` varchar(12) NOT NULL DEFAULT 'draft',
  PRIMARY KEY (`mgroup_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci AUTO_INCREMENT=5 ;

--
-- Table structure for table `mail_subscriptions`
--

CREATE TABLE IF NOT EXISTS `mail_subscriptions` (
  `msub_id` int(11) NOT NULL AUTO_INCREMENT,
  `msub_group` int(11) NOT NULL,
  `msub_user` int(11) NOT NULL,
  PRIMARY KEY (`msub_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci ROW_FORMAT=FIXED AUTO_INCREMENT=175 ;

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_section` varchar(32) DEFAULT NULL,
  `page_slug` varchar(64) DEFAULT NULL,
  `page_title` varchar(256) NOT NULL,
  `page_content` text,
  `page_user` int(11) NOT NULL DEFAULT '0',
  `page_date` datetime NOT NULL,
  `page_edit` datetime NOT NULL,
  `page_published` datetime DEFAULT NULL,
  `page_status` varchar(16) NOT NULL DEFAULT 'draft',
  `page_protected` varchar(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`page_id`),
  UNIQUE KEY `page_slug` (`page_slug`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci AUTO_INCREMENT=70 ;

--
-- Table structure for table `resources`
--

CREATE TABLE IF NOT EXISTS `resources` (
  `resource_id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_type` int(11) NOT NULL DEFAULT '1',
  `resource_category` int(11) NOT NULL,
  `resource_date` datetime NOT NULL,
  `resource_title` text NOT NULL,
  `resource_description` text NOT NULL,
  `resource_content` text,
  `resource_asset` varchar(64) DEFAULT NULL,
  `resource_author` text,
  `resource_illustrator` text,
  `resource_ISBN` varchar(17) DEFAULT NULL,
  `resource_brand` int(11) DEFAULT NULL,
  `resource_user` int(11) NOT NULL DEFAULT '1',
  `resource_comments` varchar(1) NOT NULL DEFAULT 'Y',
  `resource_moderate` varchar(1) NOT NULL DEFAULT 'N',
  `resource_edit` datetime DEFAULT NULL,
  `resource_published` datetime DEFAULT NULL,
  `resource_status` varchar(16) NOT NULL DEFAULT 'draft',
  PRIMARY KEY (`resource_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci AUTO_INCREMENT=39 ;

--
-- Table structure for table `resources_brands`
--

CREATE TABLE IF NOT EXISTS `resources_brands` (
  `rbrand_id` int(11) NOT NULL AUTO_INCREMENT,
  `rbrand_title` text NOT NULL,
  `rbrand_link` varchar(255) DEFAULT NULL,
  `rbrand_phone` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`rbrand_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci AUTO_INCREMENT=10 ;

--
-- Dumping data for table `resources_brands`
--

INSERT INTO `resources_brands` (`rbrand_id`, `rbrand_title`, `rbrand_link`, `rbrand_phone`) VALUES
(1, 'Unknown', NULL, NULL);

--
-- Table structure for table `resources_categories`
--

CREATE TABLE IF NOT EXISTS `resources_categories` (
  `rcat_id` int(11) NOT NULL AUTO_INCREMENT,
  `rcat_asset` varchar(64) DEFAULT NULL,
  `rcat_title` text NOT NULL,
  `rcat_description` text NOT NULL,
  `rcat_content` text,
  `rcat_status` varchar(12) NOT NULL DEFAULT 'draft',
  PRIMARY KEY (`rcat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci AUTO_INCREMENT=9 ;

--
-- Table structure for table `resources_types`
--

CREATE TABLE IF NOT EXISTS `resources_types` (
  `rtype_id` int(11) NOT NULL AUTO_INCREMENT,
  `rtype_title` varchar(255) NOT NULL,
  `rtype_plural` varchar(24) NOT NULL,
  PRIMARY KEY (`rtype_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci AUTO_INCREMENT=3 ;

--
-- Table structure for table `rotators`
--

CREATE TABLE IF NOT EXISTS `rotators` (
  `rot_id` int(11) NOT NULL AUTO_INCREMENT,
  `rot_name` varchar(64) NOT NULL,
  `rot_width` int(11) NOT NULL,
  `rot_height` int(11) NOT NULL,
  `rot_paging` varchar(1) NOT NULL DEFAULT 'Y',
  `rot_delay` int(11) NOT NULL DEFAULT '5000',
  PRIMARY KEY (`rot_id`,`rot_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `rotators`
--

INSERT INTO `rotators` (`rot_id`, `rot_name`, `rot_width`, `rot_height`, `rot_paging`, `rot_delay`) VALUES
(1, 'SideBar', 298, 255, 'Y', 5000),
(2, 'HomePage', 916, 255, 'Y', 5000),
(3, 'ResourcesIndex', 606, 250, 'Y', 5000),
(4, 'EventsIndex', 606, 250, 'Y', 5000);

--
-- Table structure for table `rotators_slides`
--

CREATE TABLE IF NOT EXISTS `rotators_slides` (
  `rots_id` int(11) NOT NULL AUTO_INCREMENT,
  `rots_rotator` int(11) NOT NULL,
  `rots_asset` varchar(64) NOT NULL,
  `rots_title` varchar(256) NOT NULL,
  `rots_description` text NOT NULL,
  `rots_link` varchar(256) NOT NULL,
  `rots_order` int(11) NOT NULL,
  PRIMARY KEY (`rots_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci AUTO_INCREMENT=13 ;

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_type` varchar(1) NOT NULL DEFAULT 'A',
  `tag_tag` varchar(60) NOT NULL,
  `tag_slave` int(11) NOT NULL,
  PRIMARY KEY (`tag_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci AUTO_INCREMENT=107 ;

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_role` int(11) NOT NULL DEFAULT '1',
  `user_alias` varchar(64) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_salt` varchar(32) NOT NULL,
  `user_key` varchar(255) NOT NULL,
  `user_date` datetime NOT NULL,
  `user_notifications` varchar(1) NOT NULL DEFAULT 'Y',
  `user_mailformat` varchar(4) NOT NULL DEFAULT 'text',
  `user_member` varchar(1) NOT NULL DEFAULT 'N',
  `user_status` varchar(16) NOT NULL DEFAULT 'active',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci AUTO_INCREMENT=220 ;

--
-- Table structure for table `users_privileges`
--

CREATE TABLE IF NOT EXISTS `users_privileges` (
  `prv_id` int(11) NOT NULL AUTO_INCREMENT,
  `prv_resource` varchar(64) NOT NULL,
  `prv_role` int(11) NOT NULL,
  PRIMARY KEY (`prv_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci AUTO_INCREMENT=2412 ;

--
-- Dumping data for table `users_privileges`
--

INSERT INTO `users_privileges` (`prv_id`, `prv_resource`, `prv_role`) VALUES
(1, 'uroleedit', 3),
(2, 'uroledelete', 3),
(3, 'uusers', 3),
(4, 'ppagepublish', 3),
(5, 'ppagenew', 3),
(6, 'ppagelock', 3),
(7, 'ppageedit', 3),
(8, 'ppagedelete', 3),
(9, 'ppages', 3),
(10, 'evenues', 3),
(11, 'evenuepublish', 3),
(12, 'evenuenew', 3),
(13, 'evenueedit', 3),
(14, 'evenuedelete', 3),
(15, 'eevents', 3),
(16, 'eeventpublish', 3),
(17, 'eeventnew', 3),
(18, 'eeventedit', 3),
(19, 'eeventdelete', 3),
(20, 'ecategorypublish', 3),
(21, 'ecategorynew', 3),
(22, 'ecategoryedit', 3),
(23, 'ecategorydelete', 3),
(24, 'rresourcepublish', 3),
(25, 'rresourcenew', 3),
(26, 'rresourceedit', 3),
(27, 'rresourcedelete', 3),
(28, 'rresources', 3),
(29, 'rcategorypublish', 3),
(30, 'rcategorynew', 3),
(31, 'rcategoryedit', 3),
(32, 'rcategorydelete', 3),
(33, 'rbrands', 3),
(34, 'rbrandnew', 3),
(35, 'rbrandedit', 3),
(36, 'rbranddelete', 3),
(37, 'aarticles', 3),
(38, 'acategoryedit', 3),
(39, 'acategorynew', 3),
(40, 'rotrotatoredit', 3),
(41, 'mmail', 3),
(42, 'rotrotators', 3),
(43, 'uview', 3),
(44, 'ustatus', 3),
(45, 'upassword', 3),
(46, 'unewuser', 3),
(47, 'uedit', 3),
(48, 'urolenew', 3),
(49, 'acategorydelete', 3),
(50, 'aarticlepublish', 3),
(51, 'aarticlenew', 3),
(52, 'aarticleedit', 3),
(53, 'aarticledelete', 3),
(54, 'fassets', 3),
(55, 'ffoldernew', 3),
(56, 'ffolderedit', 3),
(57, 'ffolderdelete', 3),
(58, 'fassetupload', 3),
(59, 'fassetedit', 3),
(60, 'fassetdelete', 3),
(61, 'gcommentsstatus', 3),
(62, 'gcomments', 3),
(63, 'gcommentnew', 3),
(64, 'gcommentdelete', 3),
(65, 'gadmin', 3),
(66, 'mmailnew', 3),
(67, 'mmaildelete', 3),
(68, 'mlistnew', 3),
(69, 'mlistdelete', 3),
(70, 'mmailsend', 3),
(71, 'mlistedit', 3),
(72, 'mmailedit', 3),
(73, 'mlistpublish', 3);

--
-- Table structure for table `users_profiles`
--

CREATE TABLE IF NOT EXISTS `users_profiles` (
  `upro_id` int(11) NOT NULL AUTO_INCREMENT,
  `upro_userid` int(11) NOT NULL,
  `upro_name` text NOT NULL,
  `upro_first` text NOT NULL,
  `upro_last` text NOT NULL,
  `upro_gender` varchar(1) NOT NULL DEFAULT 'N',
  `upro_dob` date DEFAULT NULL,
  `upro_organisation` text,
  `upro_position` text,
  `upro_address` text,
  `upro_city` text,
  `upro_postcode` varchar(16) DEFAULT NULL,
  `upro_country` varchar(2) NOT NULL DEFAULT 'GB',
  `upro_phone` varchar(32) DEFAULT NULL,
  `upro_blurb` text,
  `upro_url` varchar(256) DEFAULT NULL,
  `upro_avatar` varchar(256) DEFAULT NULL,
  `upro_date` datetime NOT NULL,
  PRIMARY KEY (`upro_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci AUTO_INCREMENT=225 ;

--
-- Table structure for table `users_resources`
--

CREATE TABLE IF NOT EXISTS `users_resources` (
  `res_id` int(11) NOT NULL AUTO_INCREMENT,
  `res_resource` varchar(64) NOT NULL,
  `res_module` varchar(64) NOT NULL,
  `res_group` varchar(64) NOT NULL,
  `res_description` text NOT NULL,
  PRIMARY KEY (`res_id`),
  UNIQUE KEY `res_resource` (`res_resource`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci AUTO_INCREMENT=75 ;

--
-- Dumping data for table `users_resources`
--

INSERT INTO `users_resources` (`res_id`, `res_resource`, `res_module`, `res_group`, `res_description`) VALUES
(1, 'uusers', 'users', 'global', 'Access the Users Module (This will override other settings)'),
(2, 'uedit', 'users', 'users', 'Edit user details'),
(3, 'ustatus', 'users', 'users', 'Change user''s status'),
(4, 'uview', 'users', 'users', 'View user details'),
(5, 'unewuser', 'users', 'users', 'Add new users'),
(8, 'ppages', 'pages', 'global', 'Access the Pages Module (This will override other settings)'),
(7, 'uroleedit', 'users', 'roles', 'Edit roles'),
(9, 'ppageedit', 'pages', 'pages', 'Edit pages'),
(10, 'ppagenew', 'pages', 'pages', 'Add new page'),
(11, 'ppagepublish', 'pages', 'pages', 'Publish pages'),
(12, 'ppagelock', 'pages', 'pages', 'Lock pages'),
(13, 'urolenew', 'users', 'roles', 'Add new roles'),
(14, 'uroledelete', 'users', 'roles', 'Delete roles'),
(15, 'ppagedelete', 'pages', 'pages', 'Delete pages'),
(16, 'eevents', 'events', 'global', 'Access the Events Module (This will override other settings)'),
(17, 'eeventdelete', 'events', 'events', 'Delete events'),
(18, 'eeventedit', 'events', 'events', 'Edit events'),
(19, 'eeventpublish', 'events', 'events', 'Publish events'),
(20, 'eeventnew', 'events', 'events', 'Add new events'),
(21, 'evenuedelete', 'events', 'venues', 'Delete venues'),
(22, 'evenueedit', 'events', 'venues', 'Edit venues'),
(23, 'evenuepublish', 'events', 'venues', 'Publish venues'),
(24, 'evenuenew', 'events', 'venues', 'Add new venues'),
(25, 'ecategorydelete', 'events', 'categories', 'Delete categories'),
(26, 'ecategoryedit', 'events', 'categories', 'Edit categories'),
(27, 'ecategorypublish', 'events', 'categories', 'Publish categories'),
(28, 'ecategorynew', 'events', 'categories', 'Add new categories'),
(29, 'rresources', 'resources', 'global', 'Access the Resources Module (This will override other settings)'),
(30, 'rresourcedelete', 'resources', 'resources', 'Delete resources'),
(31, 'rresourceedit', 'resources', 'resources', 'Edit resources'),
(32, 'rresourcepublish', 'resources', 'resources', 'Publish resources'),
(33, 'rresourcenew', 'resources', 'resources', 'Add new resources'),
(34, 'rbranddelete', 'resources', 'brands', 'Delete brands'),
(35, 'rbrandedit', 'resources', 'brands', 'Edit brands'),
(36, 'rbrandnew', 'resources', 'brands', 'Add new brands'),
(37, 'rcategorydelete', 'resources', 'categories', 'Delete categories'),
(38, 'rcategoryedit', 'resources', 'categories', 'Edit categories'),
(39, 'rcategorypublish', 'resources', 'categories', 'Publish categories'),
(40, 'rcategorynew', 'resources', 'categories', 'Add new categories'),
(41, 'aarticles', 'articles', 'global', 'Access the Articles Module (This will override other settings)'),
(42, 'aarticledelete', 'articles', 'articles', 'Delete articles'),
(43, 'aarticleedit', 'articles', 'articles', 'Edit articles'),
(44, 'aarticlepublish', 'articles', 'articles', 'Publish articles'),
(45, 'aarticlenew', 'articles', 'articles', 'Add new articles'),
(46, 'acategorydelete', 'articles', 'categories', 'Delete categories'),
(47, 'acategoryedit', 'articles', 'categories', 'Edit categories'),
(48, 'acategorynew', 'articles', 'categories', 'Add new categories'),
(49, 'gcomments', 'global', 'comments', 'Access the Comments Module (This will override other settings)'),
(50, 'gcommentsstatus', 'global', 'comments', 'Change comments status'),
(51, 'gcommentdelete', 'global', 'comments', 'Delete comments'),
(52, 'gcommentnew', 'global', 'comments', 'Post comments'),
(53, 'gadmin', 'global', 'administration', 'Access the Control Panel (This will override other settings)'),
(54, 'upassword', 'users', 'users', 'Reset user''s password'),
(55, 'rbrands', 'resources', 'brands', 'View brands (This will override other settings)'),
(56, 'evenues', 'events', 'venues', 'View venues (This will override other settings)'),
(57, 'fassetupload', 'assets', 'assets', 'Upload assets'),
(58, 'ffoldernew', 'assets', 'folders', 'Add new folders'),
(59, 'fassetdelete', 'assets', 'assets', 'Delete assets'),
(60, 'ffolderdelete', 'assets', 'folders', 'Delete folders'),
(61, 'fassets', 'assets', 'global', 'Access the Assets Store (This will override other settings)'),
(62, 'fassetedit', 'assets', 'assets', 'Edit assets'),
(63, 'ffolderedit', 'assets', 'folders', 'Edit folders'),
(64, 'rotrotators', 'global', 'rotators', 'Access the Rotators Module (This will override other settings)'),
(65, 'rotrotatoredit', 'global', 'rotators', 'Edit rotators'),
(66, 'mmail', 'mail', 'global', 'Access the Mail Module (This will override other settings)'),
(67, 'mmailnew', 'mail', 'mail', 'Add new mail'),
(68, 'mmaildelete', 'mail', 'mail', 'Delete mail'),
(69, 'mlistnew', 'mail', 'mailing lists', 'Add new mailing lists'),
(70, 'mlistdelete', 'mail', 'mailing lists', 'Delete mailing lists'),
(71, 'mmailsend', 'mail', 'mail', 'Send mail'),
(72, 'mlistedit', 'mail', 'mailing lists', 'Edit mailing lists'),
(73, 'mmailedit', 'mail', 'mail', 'Edit mail'),
(74, 'mlistpublish', 'mail', 'mailing lists', 'Publish mailing lists');

--
-- Table structure for table `users_roles`
--

CREATE TABLE IF NOT EXISTS `users_roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_title` varchar(64) NOT NULL,
  `role_colour` varchar(7) NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci AUTO_INCREMENT=9 ;

--
-- Dumping data for table `users_roles`
--

INSERT INTO `users_roles` (`role_id`, `role_title`, `role_colour`) VALUES
(1, 'User', '#FFFFFF'),
(2, 'Guest', '#FFFFFF'),
(3, 'Administrator', '#FFFFDD');