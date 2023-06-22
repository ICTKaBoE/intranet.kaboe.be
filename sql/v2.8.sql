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

CREATE TABLE `tbl_informat_staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `informatUID` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `masterNumber` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `firstName` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `birthPlace` varchar(254) COLLATE utf8_unicode_ci DEFAULT NULL,
  `birthDate` date DEFAULT NULL,
  `sex` set('M','F','X') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'X',
  `insz` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `diploma` varchar(254) COLLATE utf8_unicode_ci DEFAULT NULL,
  `homePhone` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobilePhone` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `privateEmail` varchar(254) COLLATE utf8_unicode_ci DEFAULT NULL,
  `schoolEmail` varchar(254) COLLATE utf8_unicode_ci DEFAULT NULL,
  `addressStreet` varchar(254) COLLATE utf8_unicode_ci DEFAULT NULL,
  `addressNumber` int(11) DEFAULT NULL,
  `addressBus` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `addressZipcode` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `addressCity` varchar(254) COLLATE utf8_unicode_ci DEFAULT NULL,
  `addressCountry` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bankAccount` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bankId` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` varchar(8) COLLATE utf8_unicode_ci DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_informat_staff_assignment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `informatUID` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `informatStaffUID` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `masterNumber` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `instituteNumber` int(11) NOT NULL,
  `start` date NOT NULL,
  `end` date DEFAULT '2999-12-31',
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_informat_student` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `informatUID` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `instituteId` int(11) NOT NULL,
  `name` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `firstName` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `insz` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_informat_student_extra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `informatUID` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `instituteId` int(11) NOT NULL,
  `name` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `firstName` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `nickname` varchar(254) COLLATE utf8_unicode_ci DEFAULT NULL,
  `masterNumber` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bisNumber` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_informat_student_subgroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `informatStudentUID` int(11) NOT NULL,
  `class` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_informat_student_subscription` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `informatUID` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `informatStudentUID` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `instituteId` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `start` date NOT NULL,
  `end` date DEFAULT NULL,
  `grade` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_sync_student` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `informatUID` int(11) NOT NULL,
  `instituteId` int(11) NOT NULL,
  `uid` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `firstName` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `displayName` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` text COLLATE utf8_unicode_ci NOT NULL,
  `class` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(254) COLLATE utf8_unicode_ci DEFAULT NULL,
  `companyName` varchar(254) COLLATE utf8_unicode_ci DEFAULT NULL,
  `memberOf` blob DEFAULT '{{ad:domain}}/Users',
  `samAccountName` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `ou` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `action` text COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `lastAdSyncSuccessAction` text COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `lastAdSyncTime` datetime DEFAULT NULL,
  `lastAdSyncError` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE tbl_school adSecGroupFolderName varchar(16) NOT NULL AFTER deviceNamePrefix;
ALTER TABLE tbl_school adSecGroupPostfix varchar(16) NOT NULL AFTER adSecGroupFolderName;
ALTER TABLE tbl_school adUserDescription varchar(16) NOT NULL AFTER adSecGroupPostfix;

INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(12, 'suppliers', 'Leveranciers', 'truck', 'edit', 99, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(12, 'mine', 'Door mij goed te keuren', 'user', 'edit', 3, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(11, 'overview', 'Overzicht', 'list', 'view', 1, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(11, 'teacher', 'Leerkrachten', 'school', 'view', 2, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(11, 'student', 'Leerlingen', 'user', 'view', 3, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(4, 'start', 'Start', 'layout-dashboard', 'view', 3, 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(11, 'syncToAdFrom', '1', 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(11, 'sendSyncMailTime', '06:00', 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(11, 'informatLastSyncTime', NULL, 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(11, 'teacherLastSyncTime', NULL, 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(11, 'studentLastSyncTime', NULL, 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(11, 'dictionary', NULL, 0);

INSERT INTO tbl_setting_override (settingId, moduleId, value, deleted) VALUES('page.default.tool', 11, 0x2F6F766572766965770D0A, 0);

UPDATE tbl_module SET module='synchronisation', name='Synchronisatie', icon='refresh', iconBackgroundColor='cyan', `scope`='app', `order`=7, redirect=NULL, assignUserRights=0, defaultRights='0000', visible=1, deleted=0 WHERE id=11;
UPDATE tbl_school SET name='De Meidoorn', color='#F9C606', logo='De Meidoorn.png', deviceNamePrefix='MEI', adSecGroupFolderName='meidoorn', adSecGroupPostfix='meidoorn', adUserDescription='Meidoorn', deleted=0 WHERE id=1;
UPDATE tbl_school SET name='De Wegel', color='#F57F29', logo='De Wegel.png', deviceNamePrefix='WEG', adSecGroupFolderName='wegel', adSecGroupPostfix='wegel', adUserDescription='Wegel', deleted=0 WHERE id=2;
UPDATE tbl_school SET name='Sint-Antonius', color='#ED1944', logo='Sint-Antonius.png', deviceNamePrefix='BSA', adSecGroupFolderName='St-Antonius', adSecGroupPostfix='stantonius', adUserDescription='Antonius', deleted=0 WHERE id=3;
UPDATE tbl_school SET name='Sint-Jozef', color='#77AE1B', logo='Sint-Jozef.png', deviceNamePrefix='STJ', adSecGroupFolderName='St-JOZEF', adSecGroupPostfix='stjozef', adUserDescription='Jozef', deleted=0 WHERE id=4;
UPDATE tbl_school SET name='KaBoE', color='#FFFFFF', logo=NULL, deviceNamePrefix='KABOE', adSecGroupFolderName='', adSecGroupPostfix='', adUserDescription=NULL, deleted=0 WHERE id=5;

UPDATE tbl_setting SET moduleId=1, settingTabId=1, name='Versie', `type`='input', `options`=NULL, value=0x322E38, `order`=2, deleted=0 WHERE id='site.version';
