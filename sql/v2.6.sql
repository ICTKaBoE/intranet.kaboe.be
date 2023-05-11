CREATE TABLE `tbl_helpdesk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` set('N','O','AC','AO','AR','C') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `priority` set('L','M','H') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'L',
  `creationDateTime` datetime NOT NULL DEFAULT current_timestamp(),
  `schoolId` int(11) NOT NULL DEFAULT 0,
  `creatorId` int(11) NOT NULL DEFAULT 0,
  `assignedToId` int(11) DEFAULT NULL,
  `type` set('L','D','P','B','O') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'O',
  `subtype` set('B','K','M','S','O') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'O',
  `deviceName` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `deviceLocation` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deviceBrand` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deviceType` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastActionDateTime` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_helpdesk_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `helpdeskId` int(11) NOT NULL,
  `creationDateTime` datetime NOT NULL DEFAULT current_timestamp(),
  `description` blob NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tbl_helpdesk_thread` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `helpdeskId` int(11) NOT NULL,
  `creationDateTime` datetime NOT NULL DEFAULT current_timestamp(),
  `creatorId` int(11) NOT NULL DEFAULT 0,
  `content` blob NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- db_intranet2.tbl_management_accesspoint definition

CREATE TABLE `tbl_management_accesspoint` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolId` int(11) NOT NULL DEFAULT 0,
  `buildingId` int(11) NOT NULL DEFAULT 0,
  `roomId` int(11) NOT NULL DEFAULT 0,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `brand` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `model` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `firmware` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `serialnumber` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `macaddress` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- db_intranet2.tbl_management_building definition

CREATE TABLE `tbl_management_building` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolId` int(11) NOT NULL DEFAULT 0,
  `name` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- db_intranet2.tbl_management_cabinet definition

CREATE TABLE `tbl_management_cabinet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolId` int(11) NOT NULL DEFAULT 0,
  `buildingId` int(11) NOT NULL DEFAULT 0,
  `roomId` int(11) NOT NULL DEFAULT 0,
  `name` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- db_intranet2.tbl_management_computer definition

CREATE TABLE `tbl_management_computer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolId` int(11) NOT NULL DEFAULT 0,
  `buildingId` int(11) DEFAULT 0,
  `roomId` int(11) DEFAULT 0,
  `type` set('L','D') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'D',
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `osType` set('W','L','C') COLLATE utf8_unicode_ci DEFAULT 'W',
  `osNumber` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `osBuild` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `osArchitecture` set('32','64') COLLATE utf8_unicode_ci DEFAULT '64',
  `systemManufacturer` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `systemModel` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `systemMemory` varchar(16) COLLATE utf8_unicode_ci DEFAULT '8GB',
  `systemProcessor` varchar(254) COLLATE utf8_unicode_ci DEFAULT NULL,
  `systemSerialnumber` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `systemBiosManufacturer` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `systemBiosVersion` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `systemDrive` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- db_intranet2.tbl_management_firewall definition

CREATE TABLE `tbl_management_firewall` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolId` int(11) NOT NULL DEFAULT 0,
  `buildingId` int(11) NOT NULL DEFAULT 0,
  `roomId` int(11) NOT NULL DEFAULT 0,
  `cabinetId` int(11) NOT NULL DEFAULT 0,
  `brand` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `model` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `hostname` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `serialnumber` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `macaddress` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `firmware` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `interface` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `username` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- db_intranet2.tbl_management_firewall_interface definition

CREATE TABLE `tbl_management_firewall_interface` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolId` int(11) NOT NULL,
  `firewallId` int(11) NOT NULL DEFAULT 0,
  `enabled` tinyint(1) NOT NULL DEFAULT 0,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `number` int(11) NOT NULL,
  `ip` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `netmask` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dhcpRange` blob DEFAULT NULL,
  `dhcpNetmask` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dhcpDns` blob DEFAULT NULL,
  `dhcpLeaseTime` int(11) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- db_intranet2.tbl_management_firewall_policy definition

CREATE TABLE `tbl_management_firewall_policy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolId` int(11) NOT NULL DEFAULT 0,
  `firewallId` int(11) NOT NULL DEFAULT 0,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `vlanInId` int(11) NOT NULL DEFAULT 0,
  `vlanOutId` int(11) NOT NULL DEFAULT 0,
  `action` set('A','D') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `nat` tinyint(1) NOT NULL DEFAULT 0,
  `log` tinyint(1) NOT NULL DEFAULT 1,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- db_intranet2.tbl_management_patchpanel definition

CREATE TABLE `tbl_management_patchpanel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolId` int(11) NOT NULL DEFAULT 0,
  `buildingId` int(11) NOT NULL DEFAULT 0,
  `roomId` int(11) NOT NULL DEFAULT 0,
  `cabinetId` int(11) NOT NULL DEFAULT 0,
  `name` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `ports` int(11) NOT NULL DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- db_intranet2.tbl_management_room definition

CREATE TABLE `tbl_management_room` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolId` int(11) NOT NULL DEFAULT 0,
  `buildingId` int(11) NOT NULL DEFAULT 0,
  `floor` int(11) NOT NULL DEFAULT 0,
  `number` int(11) NOT NULL DEFAULT 1,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- db_intranet2.tbl_management_switch definition

CREATE TABLE `tbl_management_switch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolId` int(11) NOT NULL DEFAULT 0,
  `buildingId` int(11) NOT NULL DEFAULT 0,
  `roomId` int(11) NOT NULL DEFAULT 0,
  `cabinetId` int(11) NOT NULL DEFAULT 0,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `brand` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `serialnumber` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `macaddress` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `ports` int(11) NOT NULL DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE tbl_school ADD deviceNamePrefix varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL AFTER logo;
ALTER TABLE tbl_module ADD visible tinyint(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 1 AFTER defaultRights;

TRUNCATE TABLE tbl_module;
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('settings', 'Instellingen', 'settings', 'red', 'app', 100, NULL, 0, '1000', 1, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('bike', 'Fietsvergoeding', 'bike', 'blue', 'app', 1, NULL, 1, '1100', 1, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('helpdesk', 'Helpdesk', 'question-mark', 'orange', 'app', 2, NULL, 1, '1000', 1, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('user', 'Gebruiker', NULL, NULL, 'app', 999, NULL, 1, '1000', 0, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('selectModule', 'Module Selecteren', NULL, NULL, 'app/public', 998, NULL, 0, '1000', 0, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('notescreen', 'Meldingenscherm', 'message-dots', 'yellow', 'app/public', 3, NULL, 0, '1000', 1, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('checklists', 'Controlelijsten', 'check', 'green', 'app', 3, NULL, 1, '0000', 1, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('knoladgbase', 'Kennisbank KaBoE', 'bulb', 'green', 'app', 996, 'https://collegetendoorn.sharepoint.com/sites/KaBoE-Public/Gedeelde documenten/Forms/AllItems.aspx?RootFolder=%2Fsites%2FKaBoE%2DPublic%2FGedeelde%20documenten%2FGeneral%2FKaBoE%2DLKR%2Dlezen&FolderCTID=0x012000AD901E71FE9F2A46B4F3E7E30D7E122C', 1, '1000', 1, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('ictplatform', 'ICT-Platform', 'affiliate', 'cyan', 'app', 997, 'https://ict-platform.be/', 1, '1000', 1, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('maintenance', 'Onderhoudswerken', 'home-question', 'pink', 'app', 4, NULL, 0, '0000', 1, 1);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('synchronisation', 'Synchronisatie', 'refresh', 'cyan', 'app', 7, '[NULL]', 0, '0000', 1, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('orders', 'Bestellingen', 'shopping-cart', 'teal', 'app', 6, NULL, 0, '0000', 1, 0);
INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('management', 'Beheer', 'businessplan', 'lime', 'app', 5, NULL, 0, '0000', 1, 0);

TRUNCATE TABLE tbl_module_navigation;
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(2, 'home-work', 'Woon-Werk', 'home', 'edit', 1, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(2, 'export', 'Exporteren', 'file-export', 'export', 98, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(2, 'distances', 'Afstanden', 'arrows-horizontal', 'edit', 99, 0);
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
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(3, 'open', 'Open', 'lock-open', 'edit', 2, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(3, 'mine', 'Mijn tickets', 'list', 'view', 1, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(3, 'assignedToMe', 'Toegewezen tickets', 'list', 'edit', 3, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(3, 'closed', 'Gesloten', 'lock', 'edit', 4, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(3, 'settings', 'Instellingen', 'settings', 'changeSettings', 100, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(3, 'details', 'Details', NULL, 'view', 0, 1);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(12, 'dashboard', 'Dashboard', 'dashboard', 'view', 1, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(12, 'all', 'Alle', 'list', 'edit', 2, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'dashboard', 'Dashboard', 'dashboard', 'view', 1, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'computer', 'Computer', 'devices-pc', 'edit', 9, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'peripheral', 'Randapparatuur', 'keyboard', 'edit', 12, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'printer', 'Printer', 'printer', 'edit', 11, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'firewall', 'Firewall', 'wall', 'edit', 6, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'switch', 'Switches', 'topology-ring', 'edit', 7, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'accesspoint', 'Access Point', 'wifi', 'edit', 8, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'rooms', 'Lokalen', 'door', 'edit', 3, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'cabinet', 'Netwerkkast', 'server', 'edit', 4, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'cable', 'Kabels', 'plug-connected', 'edit', 13, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'patchpanel', 'Patchpanelen', 'plug-connected', 'edit', 5, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'building', 'Gebouwen', 'building', 'edit', 2, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(13, 'beamer', 'Beamer', 'presentation', 'edit', 10, 0);

INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(3, 'format', 0x542D53542D232323232323, 0);

UPDATE tbl_school SET name='De Meidoorn', color='#F9C606', logo='De Meidoorn.png', deviceNamePrefix='MEI', deleted=0 WHERE id=1;
UPDATE tbl_school SET name='De Wegel', color='#F57F29', logo='De Wegel.png', deviceNamePrefix='WEG', deleted=0 WHERE id=2;
UPDATE tbl_school SET name='Sint-Antonius', color='#ED1944', logo='Sint-Antonius.png', deviceNamePrefix='BSA', deleted=0 WHERE id=3;
UPDATE tbl_school SET name='Sint-Jozef', color='#77AE1B', logo='Sint-Jozef.png', deviceNamePrefix='STJ', deleted=0 WHERE id=4;
INSERT INTO tbl_school (name, color, logo, deviceNamePrefix, deleted) VALUES('KaBoE', '#FFFFFF', NULL, 'KABOE', 0);

TRUNCATE TABLE tbl_setting_override;
INSERT INTO tbl_setting_override (settingId, moduleId, value, deleted) VALUES('page.default.tool', 2, 0x2F686F6D652D776F726B, 0);
INSERT INTO tbl_setting_override (settingId, moduleId, value, deleted) VALUES('page.default.tool', 1, 0x2F67656E6572616C, 0);
INSERT INTO tbl_setting_override (settingId, moduleId, value, deleted) VALUES('page.default.tool', 6, 0x2F7061676573, 0);
INSERT INTO tbl_setting_override (settingId, moduleId, value, deleted) VALUES('page.default.tool', 10, 0x2F7265717565737473, 0);
INSERT INTO tbl_setting_override (settingId, moduleId, value, deleted) VALUES('page.default.tool', 3, 0x2F6D696E65, 0);
INSERT INTO tbl_setting_override (settingId, moduleId, value, deleted) VALUES('page.default.tool', 12, 0x2F616C6C, 0);


INSERT INTO tbl_setting_tab (moduleId, name, icon, `order`, deleted) VALUES(1, 'Mailer', NULL, 5, 0);
INSERT INTO tbl_setting (id, moduleId, settingTabId, name, `type`, `options`, value, `order`, deleted) VALUES('mailer.from.email', 1, 6, 'Van (E-mail)', 'input', NULL, 0x696E7472616E65742E6B61626F652E6D61696C657240636F6C74642E6265, 3, 0);
INSERT INTO tbl_setting (id, moduleId, settingTabId, name, `type`, `options`, value, `order`, deleted) VALUES('mailer.from.name', 1, 6, 'Van (Naam)', 'input', NULL, 0x496E7472616E6574204B61426F45, 5, 0);
INSERT INTO tbl_setting (id, moduleId, settingTabId, name, `type`, `options`, value, `order`, deleted) VALUES('mailer.from.password', 1, 6, 'Wachtwoord', 'password', NULL, 0x696E7472616E6574313233, 4, 0);
INSERT INTO tbl_setting (id, moduleId, settingTabId, name, `type`, `options`, value, `order`, deleted) VALUES('mailer.host', 1, 6, 'Host', 'input', NULL, 0x736D74702E6F66666963653336352E636F6D, 1, 0);
INSERT INTO tbl_setting (id, moduleId, settingTabId, name, `type`, `options`, value, `order`, deleted) VALUES('mailer.port', 1, 6, 'Poort', 'input', NULL, 0x353837, 2, 0);

UPDATE tbl_setting SET moduleId=1, settingTabId=1, name='Versie', `type`='input', `options`=NULL, value=0x322E36, `order`=2, deleted=0 WHERE id='site.version';