DROP TABLE tbl_order_supplier;
DROP TABLE tbl_order;
DROP TABLE tbl_order_line;
DROP TABLE tbl_user_start;

CREATE TABLE `tbl_supplier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `street` varchar(128) DEFAULT NULL,
  `number` int(11) DEFAULT NULL,
  `bus` varchar(8) DEFAULT NULL,
  `zipcode` varchar(8) DEFAULT NULL,
  `city` varchar(128) DEFAULT NULL,
  `country` varchar(64) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_supplier_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplierId` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `firstName` varchar(128) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `phone` varchar(128) DEFAULT NULL,
  `isMainContact` tinyint(1) NOT NULL DEFAULT 1,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolId` int(11) NOT NULL DEFAULT 0,
  `creatorId` int(11) NOT NULL DEFAULT 0,
  `acceptorId` int(11) DEFAULT NULL,
  `supplierId` int(11) NOT NULL DEFAULT 0,
  `status` set('N','QR','QO','W','PA','A','D','O','S','PR','R','C') NOT NULL DEFAULT 'N',
  `description` longtext DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_order_line` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderId` int(11) NOT NULL DEFAULT 0,
  `for` set('L','D','B','P','O') NOT NULL DEFAULT 'O',
  `assetId` int(11) DEFAULT NULL,
  `what` varchar(254) NOT NULL,
  `reason` varchar(254) DEFAULT NULL,
  `amount` double DEFAULT 0,
  `quotationPrice` double DEFAULT 0,
  `quotationVatIncluded` tinyint(1) NOT NULL DEFAULT 1,
  `warenty` tinyint(1) NOT NULL DEFAULT 0,
  `accepted` tinyint(1) DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` set('W','E','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'I',
  `creationDateTime` datetime NOT NULL DEFAULT current_timestamp(),
  `userId` int(11) NOT NULL DEFAULT 0,
  `route` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_library` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `isdn` varchar(32) NOT NULL,
  `author` varchar(128) NOT NULL,
  `title` varchar(64) NOT NULL,
  `schoolId` int(11) NOT NULL DEFAULT 0,
  `numberOfCopies` int(11) NOT NULL DEFAULT 1,
  `category` set('S','I','D','PO','LU','LE','PR','V','T') DEFAULT NULL,
  `lastActionDateTime` datetime NOT NULL DEFAULT current_timestamp(),
  `numberOfAvailableCopies` int(11) NOT NULL DEFAULT 1,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_library_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bookId` int(11) NOT NULL,
  `creationDateTime` datetime NOT NULL DEFAULT current_timestamp(),
  `description` blob NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_informat_staff_freefield` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `informatStaffId` int(11) NOT NULL,
  `description` varchar(128) NOT NULL,
  `value` varchar(128) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_management_cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolId` int(11) NOT NULL DEFAULT 0,
  `name` varchar(32) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `type` set('L','I') NOT NULL DEFAULT 'L',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_management_ipad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolId` int(11) NOT NULL DEFAULT 0,
  `cartId` int(11) DEFAULT 0,
  `type` set('I') NOT NULL DEFAULT 'I',
  `udId` varchar(64) NOT NULL,
  `serialNumber` varchar(16) NOT NULL,
  `modelName` varchar(32) NOT NULL,
  `osPrefix` varchar(16) NOT NULL,
  `osVersion` varchar(16) NOT NULL,
  `deviceName` varchar(32) NOT NULL,
  `batteryLevel` double DEFAULT 0,
  `totalCapacity` double DEFAULT 0,
  `availableCapacity` double DEFAULT 0,
  `lastCheckin` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tbl_management_ipad_id_IDX` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_reservation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `schoolId` int(11) NOT NULL,
  `start` datetime NOT NULL DEFAULT curdate(),
  `end` datetime NOT NULL DEFAULT curdate(),
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `type` set('R','L','D','I','LK','IK') NOT NULL,
  `assetId` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_school_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolId` int(11) NOT NULL,
  `name` varchar(16) NOT NULL,
  `teacher` varchar(254) DEFAULT NULL,
  `grade` int(11) NOT NULL DEFAULT 0,
  `year` int(11) NOT NULL DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_temperature_registration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolId` int(11) NOT NULL,
  `creationDateTime` datetime NOT NULL DEFAULT current_timestamp(),
  `person` varchar(64) NOT NULL,
  `description` blob DEFAULT NULL,
  `soupTemp` double DEFAULT NULL,
  `potatoRicePastaTemp` double DEFAULT NULL,
  `vegetablesTemp` double DEFAULT NULL,
  `meatFishTemp` double DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

TRUNCATE TABLE tbl_module;
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('settings', 'Instellingen', 'settings', 'red', 'app', 100, NULL, 0, '1000', 1, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('bike', 'Fietsvergoedingen', 'bike', 'blue', 'app', 1, NULL, 1, '1100', 1, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('helpdesk', 'Helpdesk', 'question-mark', 'orange', 'app', 3, NULL, 1, '1000', 1, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('user', 'Gebruiker', NULL, NULL, 'app', 999, NULL, 1, '1000', 0, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('selectModule', 'Module Selecteren', NULL, NULL, 'app/public', 998, NULL, 0, '1000', 0, 1);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('notescreen', 'Meldingenscherm', 'message-dots', 'yellow', 'app/public', 5, NULL, 0, '1000', 1, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('checklists', 'Controlelijsten', 'check', 'green', 'app', 4, NULL, 1, '0000', 1, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('knoladgbase', 'Kennisbank KaBoE', 'bulb', 'green', 'app', 996, 'https://collegetendoorn.sharepoint.com/sites/KaBoE-Public/Gedeelde documenten/Forms/AllItems.aspx?RootFolder=%2Fsites%2FKaBoE%2DPublic%2FGedeelde%20documenten%2FGeneral%2FKaBoE%2DLKR%2Dlezen&FolderCTID=0x012000AD901E71FE9F2A46B4F3E7E30D7E122C', 1, '1000', 1, 1);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('ictplatform', 'ICT-Platform', 'affiliate', 'cyan', 'app', 997, 'https://ict-platform.be/', 1, '1000', 1, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('maintenance', 'Onderhoudswerken', 'home-question', 'pink', 'app', 4, NULL, 0, '0000', 1, 1);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('synchronisation', 'Synchronisatie', 'refresh', 'cyan', 'app', 99, NULL, 0, '0000', 1, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('orders', 'Bestellingen', 'shopping-cart', 'teal', 'app', 10, NULL, 0, '0000', 1, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('management', 'Beheer', 'businessplan', 'lime', 'app', 8, NULL, 0, '0000', 1, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('supervision', 'Middagtoezichten', 'device-cctv', 'azure', 'app', 2, NULL, 1, '1100', 1, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('supplier', 'Leveranciers', 'truck', 'dark', 'app', 9, NULL, 0, '0000', 1, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('schoollibrary', 'Schoolbibliotheek', 'book', 'pink', 'app', 6, NULL, 0, '1000', 1, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('reservation', 'Reservatie', 'calendar-event', 'indigo', 'app', 7, NULL, 0, '1000', 1, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('forms', 'Formulier', 'forms', 'muted', 'public', 98, NULL, 0, '0000', 1, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('temperatureregistration', 'Temperatuurregistratie maaltijden', 'tools-kitchen-2', 'purple', 'app', 11, NULL, 0, '1000', 1, 0);

TRUNCATE TABLE tbl_module_navigation;
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(2, 'home-work', 'Woon - Werk', 'home', 'edit', 1, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(2, 'export', 'Exporteren', 'file-export', 'export', 99, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(2, 'distances', 'Afstanden', 'arrows-horizontal', 'edit', 98, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(2, 'work-work', 'Werk-Werk', 'building', 'edit', 2, 1);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(4, 'profile', 'Profiel', 'user', 'view', 1, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(4, 'address', 'Adres', 'building', 'view', 2, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(2, 'settings', 'Instellingen', 'settings', 'changeSettings', 100, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(7, 'dashboard', 'Dashboard', 'dashboard', 'view', 1, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(7, 'checkStudentRelationInsz', 'Rijksregister leerling/ouders', 'pencil', 'edit', 2, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(1, 'general', 'Algemeen', 'adjustments', 'changeSettings', 1, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(1, 'rights', 'Gebruikersrechten', 'fingerprint', 'changeSettings', 2, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(6, 'pages', 'Pagina''s', 'book', 'edit', 1, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(6, 'articles', 'Artikelen', 'file', 'edit', 2, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(10, 'requests', 'Aanvragen', 'clipboard-list', 'edit', 2, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(3, 'open', 'Open', 'lock-open', 'edit', 3, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(3, 'mine', 'Mijn tickets', 'list', 'view', 2, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(3, 'assignedToMe', 'Toegewezen tickets', 'list', 'edit', 4, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(3, 'closed', 'Gesloten', 'lock', 'edit', 5, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(3, 'settings', 'Instellingen', 'settings', 'changeSettings', 100, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(3, 'details', 'Details', NULL, 'view', 0, 1);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(12, 'dashboard', 'Dashboard', 'dashboard', 'view', 1, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(12, 'overview', 'Overzicht', 'list', 'export', 2, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'dashboard', 'Dashboard', 'dashboard', 'view', 1, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'computer', 'Computer', 'devices-pc', 'edit', 10, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'peripheral', 'Randapparatuur', 'keyboard', 'edit', 14, 1);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'printer', 'Printer', 'printer', 'edit', 13, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'firewall', 'Firewall', 'wall', 'edit', 6, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'switch', 'Switches', 'topology-ring', 'edit', 7, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'accesspoint', 'Access Point', 'wifi', 'edit', 8, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'rooms', 'Lokalen', 'door', 'edit', 3, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'cabinet', 'Netwerkkast', 'server', 'edit', 4, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'cable', 'Kabels', 'plug-connected', 'edit', 15, 1);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'patchpanel', 'Patchpanelen', 'plug-connected', 'edit', 5, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'building', 'Gebouwen', 'building', 'edit', 2, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'beamer', 'Beamer', 'presentation', 'edit', 12, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(12, 'suppliers', 'Leveranciers', 'truck', 'changeSettings', 99, 1);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(12, 'mine', 'Door mij goed te keuren', 'user', 'edit', 3, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(11, 'overview', 'Overzicht', 'list', 'view', 1, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(11, 'teacher', 'Leerkrachten', 'school', 'edit', 2, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(11, 'student', 'Leerlingen', 'user', 'edit', 3, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(4, 'start', 'Start', 'layout-dashboard', 'view', 3, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(11, 'settings', 'Instellingen', 'settings', 'changeSettings', 4, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(14, 'fill', 'Invullen', 'pencil', 'view', 1, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(14, 'export', 'Exporteren', 'file-export', 'export', 2, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(14, 'settings', 'Instellingen', 'settings', 'changeSettings', 3, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(12, 'settings', 'Instellingen', 'settings', 'changeSettings', 100, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(15, 'overview', 'Overzicht', 'list', 'edit', 1, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(15, 'contact', 'Contacten', 'users', 'edit', 2, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'ipad', 'Ipad', 'device-ipad', 'edit', 11, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(16, 'library', 'Bibliotheek', 'book', 'edit', 2, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(16, 'lend', 'Uitlenen', 'book-download', 'edit', 3, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(16, 'return', 'Terugbrengen', 'book-upload', 'edit', 4, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(17, 'dashboard', 'Dashboard', 'dashboard', 'view', 1, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(17, 'mine', 'Mijn reservaties', 'list', 'view', 2, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(17, 'all', 'Alle reservaties', 'list', 'edit', 3, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(1, 'logs', 'Logboeken', 'list', 'changeSettings', 10, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(18, 'checkStudentRelationInsz', 'Rijksregisternummer leerling/ouders', NULL, 'view', 1, 1);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(16, 'dashboard', 'Dashboard', 'dashboard', 'view', 1, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(3, 'dashboard', 'Dashboard', 'dashboard', 'view', 1, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'cart', 'Kar', 'box', 'edit', 9, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(19, 'dashboard', 'Dashboard', 'dashboard', 'view', 1, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(19, 'export', 'Exporteren', 'file-export', 'export', 3, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(18, 'temperatureRegistration', 'Temperatuurregistratie Maaltijden', NULL, 'view', 1, 1);

INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(12, 'mailReplyEmail', 0x6963742E6B61626F6540636F6C74642E6265, 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(12, 'mailReplyName', 0x494354204B61426F45, 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(12, 'acceptorIds', 0x31363B32, 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(12, 'format', 0x592D232323232323, 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(17, 'blockPast', 0x31, 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(17, 'blockFuture', 0x31, 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(17, 'blockPastType', 0x64, 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(17, 'blockFutureType', 0x6D, 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(17, 'blockPastAmount', 0x30, 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(17, 'blockFutureAmount', 0x32, 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(19, 'names1', 0x, 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(19, 'names2', 0x, 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(19, 'names3', 0x, 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(19, 'names4', 0x, 0);

ALTER TABLE tbl_management_computer ADD COLUMN `cartId` int(11) DEFAULT 0 AFTER `systemDrive`;
ALTER TABLE tbl_sync_student ADD COLUMN `active` tinyint(1) DEFAULT 1 AFTER `deleted`;

UPDATE tbl_setting SET moduleId=1, settingTabId=2, name='Na aanmelding', `type`='input', `options`=NULL, value=0x2F6170702F696E646578, `order`=3, deleted=0 WHERE id='page.default.afterLogin';
UPDATE tbl_setting SET moduleId=1, settingTabId=2, name='Publieke eerste pagina', `type`='input', `options`=NULL, value=0x2F7075626C69632F696E646578, `order`=1, deleted=0 WHERE id='page.default.public';
UPDATE tbl_setting SET moduleId=1, settingTabId=2, name='Bij module', `type`='input', `options`=NULL, value=0x2F64617368626F617264, `order`=4, deleted=0 WHERE id='page.default.tool';
UPDATE tbl_setting SET moduleId=1, settingTabId=2, name='Aanmelding', `type`='input', `options`=NULL, value=0x2F6170702F757365722F6C6F67696E, `order`=2, deleted=0 WHERE id='page.login';
UPDATE tbl_setting SET moduleId=1, settingTabId=1, name='Titel', `type`='input', `options`=NULL, value=0x4B61426F4520496E7472616E6574, `order`=1, deleted=0 WHERE id='site.title';

UPDATE tbl_module_setting SET moduleId=3, `key`='format', value=0x232323232323, deleted=0 WHERE id=15;

INSERT INTO tbl_setting (id, moduleId, settingTabId, name, `type`, `options`, value, `order`, deleted) VALUES('api.version', 1, 1, 'API Versie', 'input', NULL, 0x32, 10, 0);
INSERT INTO tbl_setting (id, moduleId, settingTabId, name, `type`, `options`, value, `order`, deleted) VALUES('jamf.endpoint', 1, 7, 'Endpoint', 'input', NULL, 0x68747470733A2F2F7363686F6C656E67726F65706B61626F652E6A616D66636C6F75642E636F6D2F6170692F, 1, 0);
INSERT INTO tbl_setting (id, moduleId, settingTabId, name, `type`, `options`, value, `order`, deleted) VALUES('jamf.key', 1, 7, 'Key', 'password', NULL, 0x4654344C5442594337424B51574E57464232445747384757334E4C4E48584A35, 2, 0);
INSERT INTO tbl_setting (id, moduleId, settingTabId, name, `type`, `options`, value, `order`, deleted) VALUES('jamf.networkid', 1, 7, 'Netwerk ID', 'input', NULL, 0x3130303339393732, 6, 0);
INSERT INTO tbl_setting (id, moduleId, settingTabId, name, `type`, `options`, value, `order`, deleted) VALUES('jamf.password', 1, 7, 'Wachtwoord', 'password', NULL, 0x5069616E6F6D616E5041313235, 4, 0);
INSERT INTO tbl_setting (id, moduleId, settingTabId, name, `type`, `options`, value, `order`, deleted) VALUES('jamf.username', 1, 7, 'Gebruikersnaam', 'input', NULL, 0x6963742E6B61626F6540636F6C74642E6265, 3, 0);
INSERT INTO tbl_setting (id, moduleId, settingTabId, name, `type`, `options`, value, `order`, deleted) VALUES('jamf.version', 1, 7, 'Versie', 'input', NULL, 0x, 5, 0);
INSERT INTO tbl_setting (id, moduleId, settingTabId, name, `type`, `options`, value, `order`, deleted) VALUES('site.title.withVersion', 1, 1, 'Titel met versie', 'input', NULL, 0x7B7B736974652E7469746C657D7D20767B7B736974652E76657273696F6E7D7D, 2, 0);

INSERT INTO tbl_setting_override (settingId, moduleId, value, deleted) VALUES('page.default.tool', 15, 0x2F6F76657276696577, 0);
INSERT INTO tbl_setting_override (settingId, moduleId, value, deleted) VALUES('page.default.tool', 16, 0x2F6C6962726172790D, 0);
INSERT INTO tbl_setting_override (settingId, moduleId, value, deleted) VALUES('page.default.tool', 17, 0x2F6D696E65, 0);

UPDATE tbl_setting SET moduleId=1, settingTabId=1, name='Versie', `type`='input', `options`=NULL, value=0x332E302E30, `order`=2, deleted=0 WHERE id='site.version';