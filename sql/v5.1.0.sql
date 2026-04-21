-- Strategic Dashboard
-- --TABLES
CREATE TABLE `tbl_strategicdashboard_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolId` int(11) NOT NULL,
  `typeId` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `minimum` int(11) NOT NULL DEFAULT 0,
  `target` int(11) NOT NULL DEFAULT 0,
  `canEditUserId` varchar(254) DEFAULT NULL,
  `width` int(11) NOT NULL DEFAULT 2,
  `order` int(11) NOT NULL DEFAULT 1,
  `valueTemplate` blob DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_strategicdashboard_item_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `short` varchar(32) NOT NULL,
  `name` varchar(254) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_strategicdashboard_item_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemId` int(11) NOT NULL,
  `value` blob DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `editedByUserId` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --NAVIGATION
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', 0, 'M', 0, 90, 'strategicDashboard', 'Strategisch Dashboard', 'dashboard', 'blue', 0);
SET @nId = (SELECT id FROM tbl_navigation WHERE link="strategicDashboard" AND `type` = "M");

INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 0, 1, 'dashboard', 'Dashboard', 'dashboard', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 1, 1, 'insert', 'Gegevens invoeren', 'cylinder-plus', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 1, 2, 'items', 'Items', 'list', 'blue', 0);

-- --NAVIGATION SETTING
INSERT INTO tbl_navigation_setting (navigationId, `key`, value) VALUES(@nId, '_', 0x64617368626F617264);

-- --NAVIGATION TABLE
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "items"), 1, NULL, NULL, 'School', 'linked.school.formatted.badge.name', 0, 0, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "items"), 2, NULL, NULL, 'Naam', 'name', 1, 1, 0, 0, 1, NULL, 'asc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "items"), 3, NULL, NULL, 'Minimum', 'minimum', 1, 1, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "items"), 4, NULL, NULL, 'Streefdoel', 'target', 1, 1, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "items"), 5, NULL, NULL, 'Type', 'linked.type.name', 1, 1, 200, 0, 0, NULL, NULL, 0);

-- --ROUTE
INSERT INTO tbl_route (routeGroupId, `method`, route, controller, callback, apiNoAuth, deleted) VALUES(2, 'ANY', '{view}/strategicDashboard/{what?}/{id?}', '\\Controllers\\API\\StrategicDashboardController', 'any', 0, 0);

-- COLTD Alert
-- --TABLES
CREATE TABLE `tbl_coltdalert` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `from` int(11) NOT NULL,
  `groupId` varchar(254) NOT NULL,
  `content` varchar(160) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_coltdalert_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `name` varchar(254) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_coltdalert_group_member` (
  `groupId` int(11) NOT NULL,
  `informatEmployeeNumberId` int(11) NOT NULL,
  PRIMARY KEY (`groupId`,`informatEmployeeNumberId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_coltdalert_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `coltdalertId` int(11) NOT NULL,
  `ringringGuid` varchar(36) NOT NULL,
  `statusCode` int(11) DEFAULT NULL,
  `statusDescription` varchar(254) DEFAULT NULL,
  `to` varchar(32) NOT NULL,
  `timeScheduled` datetime DEFAULT NULL,
  `timeDelivered` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_coltdalert_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `name` varchar(254) NOT NULL,
  `content` varchar(160) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --NAVIGATION
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', 0, 'M', 0, 91, 'coltdAlert', 'COLTD Alert', 'alert-triangle', 'red', 0);
SET @nId = (SELECT id FROM tbl_navigation WHERE link="coltdAlert" AND `type` = "M");
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 0, 1, 'send', 'Versturen', 'send', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 1, 1, 'logs', 'Logboek', 'logs', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 1, 2, 'templates', 'Templates', 'template', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 1, 3, 'groups', 'Groepen', 'users-group', 'blue', 0);

-- --NAVIGATION SETTING
INSERT INTO tbl_navigation_setting (navigationId, `key`, value) VALUES(@nId, '_', 0x73656E64);

-- --ROUTE
INSERT INTO tbl_route (routeGroupId, `method`, route, controller, callback, apiNoAuth, deleted) VALUES(2, 'ANY', '{view}/coltdAlert/{what?}/{id?}', '\\Controllers\\API\\ColtdAlertController', 'any', 0, 0);

-- --TABLE
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "logs"), 1, NULL, NULL, 'Verzonden op', 'formatted.datetime', 1, 0, 200, 1, 1, 1, 'desc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "logs"), 2, NULL, NULL, 'Vanaf', 'from', 1, 1, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "logs"), 3, NULL, NULL, 'Inhoud', 'content', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "logs"), 4, NULL, NULL, 'Groepen', 'formatted.group', 0, 1, 300, 0, 0, NULL, NULL, 0);

INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "templates"), 1, NULL, NULL, 'Naam', 'name', 1, 1, 250, 0, 1, 1, 'asc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "templates"), 2, NULL, NULL, 'Inhoud', 'content', 0, 1, 0, 0, 0, NULL, NULL, 0);

INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "groups"), 1, NULL, NULL, 'Naam', 'name', 1, 1, 0, 0, 1, 1, 'asc', 0);

-- School
ALTER TABLE tbl_school ADD eetjemeeKey varchar(254) NULL;
ALTER TABLE tbl_school CHANGE eetjemeeKey eetjemeeKey varchar(254) NULL AFTER smartschoolSourceId;
ALTER TABLE tbl_school ADD eetjemeeSmartschoolGroup varchar(254) NULL;
ALTER TABLE tbl_school CHANGE eetjemeeSmartschoolGroup eetjemeeSmartschoolGroup varchar(254) NULL AFTER eetjemeeKey;

-- --DIRECTION
CREATE TABLE `tbl_school_direction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `schoolId` int(11) NOT NULL,
  `departmentId` int(11) NOT NULL,
  `name` varchar(254) NOT NULL,
  `code` varchar(254) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -- --NAVIGATION
SET @nId = (SELECT id FROM tbl_navigation WHERE link="configuration" AND `type` = "M");
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 0, 5, 'direction', 'Richting', 'arrow-left', 'blue', 0);

-- -- --TABLE
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "direction"), 1, NULL, NULL, 'School', 'linked.school.formatted.badge.name', 0, 0, 20, 0, 0, null, null, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "direction"), 2, NULL, NULL, 'Afdeling', 'linked.department.name', 0, 0, 20, 0, 0, null, null, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "direction"), 3, NULL, NULL, 'Naam', 'name', 1, 1, 0, 0, 1, 1, 'asc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "direction"), 3, NULL, NULL, 'Code', 'code', 1, 1, 100, 0, 0, null, null, 0);

-- Settings
INSERT INTO tbl_setting_tab (name, icon, `order`, `default`, deleted) VALUES('RingRing', NULL, 12, 0, 0);
INSERT INTO tbl_setting (id, settingTabId, name, `type`, `options`, value, readonly, `order`, deleted) VALUES('ringring.key', (SELECT id FROM tbl_setting_tab WHERE `name`="RingRing"), 'Key', 'input', NULL, 0x44363636354631462D323846432D343030322D424446322D364233313132443645434230, 0, 1, 0);

INSERT INTO tbl_setting (id, settingTabId, name, `type`, `options`, value, readonly, `order`, deleted) VALUES('cron.ringring.import.active', 1, 'cron.ringring.import.active', 'input', NULL, 0x30, 1, NULL, 0);
INSERT INTO tbl_setting (id, settingTabId, name, `type`, `options`, value, readonly, `order`, deleted) VALUES('cron.eetjemee.import.active', 1, 'cron.eetjemee.import.active', 'input', NULL, 0x30, 1, NULL, 0);

UPDATE tbl_setting SET settingTabId=1, name='DB Versie', `type`='input', `options`=NULL, value=0x352E312E30, readonly=1, `order`=99, deleted=0 WHERE id='db.version';
UPDATE tbl_setting SET settingTabId=1, name='Versie', `type`='input', `options`=NULL, value=0x352E312E30, readonly=0, `order`=3, deleted=0 WHERE id='site.version';