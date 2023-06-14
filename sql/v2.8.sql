CREATE TABLE `tbl_notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL DEFAULT 0,
  `creationDateTime` datetime NOT NULL DEFAULT current_timestamp(),
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `read` tinyint(1) NOT NULL DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolId` int(11) NOT NULL DEFAULT 0,
  `creatorId` int(11) NOT NULL DEFAULT 0,
  `acceptorId` int(11) DEFAULT NULL,
  `supplierId` int(11) NOT NULL DEFAULT 0,
  `status` set('N','QR','QO','W','PA','A','D','O','S','PR','R','C') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_order_line` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderId` int(11) NOT NULL DEFAULT 0,
  `for` set('L','D','B','P','O') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'O',
  `assetId` int(11) DEFAULT NULL,
  `what` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `reason` varchar(254) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` double DEFAULT 0,
  `quotationPrice` double DEFAULT 0,
  `quotationVatIncluded` tinyint(1) NOT NULL DEFAULT 1,
  `accepted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_order_supplier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `contact` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_user_start` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `type` set('I','IN','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'IN',
  `url` text COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `icon` text COLLATE utf8_unicode_ci NOT NULL,
  `width` int(11) NOT NULL DEFAULT 1,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(12, 'suppliers', 'Leveranciers', 'truck', 'edit', 99, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(12, 'mine', 'Door mij goed te keuren', 'user', 'edit', 3, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(11, 'overview', 'Overzicht', 'list', 'changeSettings', 1, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(11, 'teacher', 'Leerkrachten', 'school', 'changeSettings', 2, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(11, 'student', 'Leerlingen', 'user', 'changeSettings', 3, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(4, 'start', 'Start', 'layout-dashboard', 'view', 3, 0);

INSERT INTO tbl_setting_override (settingId, moduleId, value, deleted) VALUES('page.default.tool', 11, 0x2F6F766572766965770D0A, 0);

UPDATE tbl_module SET module='synchronisation', name='Synchronisatie', icon='refresh', iconBackgroundColor='cyan', `scope`='app', `order`=7, redirect=NULL, assignUserRights=0, defaultRights='0000', visible=1, deleted=0 WHERE id=11;

UPDATE tbl_setting SET moduleId=1, settingTabId=1, name='Versie', `type`='input', `options`=NULL, value=0x322E38, `order`=2, deleted=0 WHERE id='site.version';
