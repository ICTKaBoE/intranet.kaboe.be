-- Strategic Dashboard
-- --TABLES
CREATE TABLE `tbl_strategicdashboard_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolId` int(11) NOT NULL,
  `typeId` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `minimum` int(11) NOT NULL DEFAULT 0,
  `target` int(11) NOT NULL DEFAULT 0,
  `canEditUserId` varchar(254) DEFAULT NULL,
  `width` int(11) NOT NULL DEFAULT 2,
  `order` int(11) NOT NULL DEFAULT 1,
  `info` blob DEFAULT NULL,
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

CREATE TABLE `tbl_strategicdashboard_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `name` varchar(254) NOT NULL,
  `image` tinyint(1) NOT NULL DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
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

-- --INSERTS
INSERT INTO tbl_strategicdashboard_category (guid, name, image, `order`, deleted) VALUES('E4E3B508-B282-4023-A0F1-013F942E7FE1', 'Breed onderwijsaanbod', 1, 1, 0);
INSERT INTO tbl_strategicdashboard_category (guid, name, image, `order`, deleted) VALUES('5CE13270-802C-441D-B3CE-856BDD63DB96', 'Kwaliteitsvol onderwijs', 1, 2, 0);
INSERT INTO tbl_strategicdashboard_category (guid, name, image, `order`, deleted) VALUES('27612A3A-88F6-465F-AB60-16A007578A00', 'Innovatie en digitalisering', 1, 3, 0);
INSERT INTO tbl_strategicdashboard_category (guid, name, image, `order`, deleted) VALUES('D6B92E19-C155-4985-A7BB-AACC21E6024E', 'Betrokken medewerkers', 1, 4, 0);
INSERT INTO tbl_strategicdashboard_category (guid, name, image, `order`, deleted) VALUES('7FB0054F-62F9-47E0-A457-A05DB612E6D4', 'Infrastructuur en Financieel', 1, 5, 0);

INSERT INTO tbl_strategicdashboard_item_type (short, name) VALUES('rating:circle', 'Rating: Cirkel');
INSERT INTO tbl_strategicdashboard_item_type (short, name) VALUES('chart:pie', 'Chart: Pie');
INSERT INTO tbl_strategicdashboard_item_type (short, name) VALUES('chart:bar', 'Chart: Bar');
INSERT INTO tbl_strategicdashboard_item_type (short, name) VALUES('number', 'Getal');

-- --NAVIGATION
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', 0, 'M', 0, 90, 'strategicDashboard', 'Strategisch Dashboard', 'dashboard', 'blue', 0);
SET @nId = (SELECT id FROM tbl_navigation WHERE link="strategicDashboard" AND `type` = "M");

INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 0, 1, 'dashboard', 'Dashboard', 'dashboard', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 1, 1, 'insert', 'Gegevens invoeren', 'cylinder-plus', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 1, 2, 'categories', 'Categorieën', 'list', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 1, 2, 'items', 'Items', 'list', 'blue', 0);

-- --NAVIGATION SETTING
INSERT INTO tbl_navigation_setting (navigationId, `key`, value) VALUES(@nId, '_', 0x64617368626F617264);

-- --NAVIGATION TABLE
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "items"), 1, NULL, NULL, 'School', 'linked.school.formatted.badge.name', 0, 0, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "items"), 2, NULL, NULL, 'Categorie', 'linked.category.name', 0, 0, 200, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "items"), 3, NULL, NULL, 'Naam', 'name', 1, 1, 0, 0, 1, NULL, 'asc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "items"), 4, NULL, NULL, 'Minimum', 'minimum', 1, 1, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "items"), 5, NULL, NULL, 'Streefdoel', 'target', 1, 1, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "items"), 6, NULL, NULL, 'Type', 'linked.type.name', 1, 1, 200, 0, 0, NULL, NULL, 0);

INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "categories"), 1, NULL, NULL, 'Naam', 'name', 1, 1, 0, 0, 1, NULL, 'asc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "categories"), 2, NULL, NULL, 'Volgorde', 'order', 1, 1, 100, 0, 1, NULL, 'asc', 0);

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

-- --INSERTS
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('5D71658A-B74A-4B9B-ADCE-0D7EB77B966E', 6, 1, 'Bedrijfswetenschappen', 'BWE', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('266C9C1F-E813-4A97-BC43-E2B438475BF1', 6, 1, 'Biotechnologishe STEM-wetenschappen', 'BIOWE', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('FE925B38-4E98-4AC9-AAFB-4929B736A0F2', 6, 1, 'Economische wetenschappen', 'ECWE', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('3DC748CC-68DB-4D04-B3FE-D9CE23991E13', 6, 1, 'Grieks-Latijn', 'GRLA', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('D33CC0C4-80D4-42E8-BA78-A2A2222E9E87', 6, 1, 'Humane wetenschappen', 'HUWE', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('A596E252-0A90-4357-9565-7120410966D3', 6, 1, 'Latijn', 'LA', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('511B5661-AE7D-45FA-AEC0-0ADE0AC6C85E', 6, 1, 'Maatschappij- en welzijnswetenschappen', 'MWWE', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('384F58B6-C754-421B-BD58-3680F8F7A3A1', 6, 1, 'Moderne talen', 'MT', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('BA131C47-0327-429B-B9D7-5EBB684D829E', 6, 1, 'Natuurwetenschappen', 'NATWE', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('8CA4FAD8-8451-4CCF-B055-81A06CA8E6E5', 6, 1, 'Sportwetenschappen', 'SPWE', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('AACDA7C0-9D20-4FAE-91D3-2C93C4744606', 6, 1, 'Economie - moderne talen', 'ECMT', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('B068E984-D8D5-4622-BF7C-61265C7B6A57', 6, 1, 'Economie - wiskunde', 'ECWI', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('B5CBBC00-A4D5-4850-B929-4C0CCED1836F', 6, 1, 'Grieks - Latijn', 'GRLA', 1);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('28DC9378-00D3-4BC9-BAFD-B475548C6C45', 6, 1, 'Grieks - wiskunde', 'GRWI', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('D382E2F0-03A0-4F2F-BC3D-116CA0DAABC5', 6, 1, 'Latijn - moderne talen', 'LAMT', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('0BAF6FA1-58A3-4CCA-B2A8-CEF626E07F46', 6, 1, 'Latijn - wetenschappen', 'LAWE', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('32185F54-5464-4D51-A7C4-D3F9C9F3FDB5', 6, 1, 'Latijn - wiskunde', 'LAWI', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('773E93BC-A612-4316-AEE2-D189A514E9FC', 6, 1, 'Moderne talen - wetenschappen', 'MTWE', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('2BC7F4A0-AA49-4ED0-86F4-9B38CC503539', 6, 1, 'Moderne talen', 'MT', 1);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('3BD83604-C5E5-4E13-B398-C5F456BD3D12', 6, 1, 'Wetenschappen- wiskunde (6u)', 'WEW6', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('E6A81E71-3C83-4027-B589-998A316E08B5', 6, 1, 'Wetenschappen- wiskunde (8u)', 'WEW8', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('8CF6BDAC-5EE3-41F1-AD54-91FDE501BC93', 6, 1, 'Welzijnswetenschappen', 'WWE', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('5D82D971-4089-47C0-A03B-4C913D9B9929', 6, 3, '1A basis Frans - basis wiskunde', 'A', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('D1D31EB8-8716-421A-B993-3A0B5431BAF9', 6, 3, '1A met Latijn', 'ALA', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('006AC001-A190-431D-AF08-80647E94651D', 6, 3, '1A met Latijn (CLIL Engels en Frans)', 'ALAC', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('E751C709-A4D1-447D-B940-2C2BDE8A49FE', 6, 3, '1A verdieping Frans - basis wiskunde', 'AF', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('FAE501FD-7CAB-475C-89DD-BDE71087FD77', 6, 3, '1A verdieping Frans - basis wiskunde (CLIL Engels en Frans)', 'AFC', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('7D625E38-DB36-42B0-A2FD-DF2972E31B9C', 6, 3, '1A basis Frans - verdieping wiskunde', 'AW', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('6E914C34-1C95-4B57-B7BE-2734ECA56130', 6, 3, '1A verdieping Frans - verdieping wiskunde', 'AWF', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('A401E8F3-7DA9-49B0-92B7-1CDAE37D919C', 6, 3, '1A verdieping Frans - verdieping wiskunde (CLIL Engels en Frans)', 'AWFC', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('F7899FED-B3DC-498A-9BA7-DE45B9268AC9', 6, 3, '1B', 'B', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('665C79A6-97D4-4F11-96A0-10A4DFCC7578', 6, 3, '2A Economie en organisatie - basis', 'AECOB', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('5AC81A98-501F-4D6D-9CE4-2ECFE69B6682', 6, 3, '2A Economie en organisatie - verdieping', 'AECOV', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('7074D955-32BB-4A5F-8007-2D4AFF932462', 6, 3, '2A Klassieke talen met Grieks-Latijn ', 'AKTGLA', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('A6992742-391B-43D6-848A-F20D062FFAD8', 6, 3, '2A Klassieke talen met Grieks-Latijn (CLIL Frans)', 'AKTGLAC', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('C4591CC4-E3D4-41E0-B4B7-87EA109A1D0A', 6, 3, '2A Klassieke talen met Latijn ', 'AKTLA', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('EBA48733-5541-4E96-9366-C7D91A7779C8', 6, 3, '2A Klassieke talen met Latijn (CLIL Frans)', 'AKTLAC', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('E388FB89-23B6-4EEA-9285-E5922D84ED50', 6, 3, '2A Klassieke talen met Latijn en STEM-project', 'AKTLAST', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('EA0F7494-E3E1-40E0-93CE-2B6087EE1A52', 6, 3, '2A Klassieke talen met Latijn en STEM-project (CLIL Engels en/of Frans)', 'AKTLASTC', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('3ADCF04C-060D-4101-8A73-37A26ED07542', 6, 3, '2A Maatschappij en welzijn - basis', 'AMWB', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('DB16EC11-B690-4D39-BB19-562628276B94', 6, 3, '2A Maatschappij en welzijn - verdieping', 'AMWV', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('122AEE13-ABA8-44A9-AC05-CB33515250EC', 6, 3, '2A Moderne talen en wetenschappen ', 'AMTWE', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('F4BB5226-78BB-4A53-8355-8025B1A38C06', 6, 3, '2A Moderne talen en wetenschappen (CLIL Engels en/of Frans)', 'AMTWEC', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('DA071D99-ECA8-4710-9B2C-668BDE31CA2D', 6, 3, '2A Moderne talen en wetenschappen en STEM-project (CLIL Engels en/of Frans)', 'AMTWEST', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('F40C2FEB-2A38-4147-AD81-C3BBF20774AD', 6, 3, '2A Sport - basis', 'ASPB', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('116F79C9-6E64-47DC-AC19-BE25BCEB1D7B', 6, 3, '2A Sport - verdieping', 'ASPV', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('654CF5A3-9275-46A9-9353-8DCED6DBD2F9', 6, 3, '2A STEM-technieken', 'ASTTE', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('DFBEF670-9188-4634-AEA4-BF5E72CFCB45', 6, 3, '2A STEM-wetenschappen - basis', 'ASTWEB', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('D61EB05F-22C0-492A-9170-96C9D5EB70D2', 6, 3, '2A STEM-wetenschappen - verdieping', 'ASTWEV', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('CD05C6B0-DF8B-4795-8658-EB014F32D8DF', 6, 3, '2A STEM-wetenschappen - verdieping (CLIL Engels)', 'ASTWEVC', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('7EF08C30-66FE-4462-A2D2-360B869E3229', 6, 3, '2B Economie en organisatie - Maatschappij en welzijn', 'BECOMV', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('3BB4ECEE-8D46-4A62-85D0-8D46BB877578', 6, 3, '2B Economie en organisatie - Voeding en horeca', 'BECOVH', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('33C0D670-0837-4D49-BAFA-22267D858F28', 6, 3, '2B Maatschappij en welzijn ', 'BMW', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('D89C29F6-F527-45E0-8B39-CD6D9ECAF9C3', 6, 3, '2B Sport - Maatschappij en welzijn', 'BMWSP', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('272507D7-FF32-4B1A-AB9B-077B5B4854DF', 6, 3, '2B STEM-technieken', 'BSTTE', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('5ECA4865-AED6-4696-BC80-D3F68F5645AA', 6, 2, 'Bedrijf en organisatie CLIL', 'BO', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('F100FB59-925F-41A3-8B55-C3A5EFEE0904', 6, 2, 'Biotechnieken', 'BIOT', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('EE3B1734-63E0-4C69-BA22-B0E0E893373D', 6, 2, 'Maatschappij en welzijn', 'MW', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('16347C53-0828-4115-AC00-250941D07EAE', 6, 2, 'Plant, dier- en milieutechnieken', 'PDMT', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('B2D612E8-1C4F-4AED-B980-91C3D2CB65D6', 6, 2, 'Sport', 'SP', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('1F8395AB-BEFC-4707-8A55-1154F190E9E8', 6, 2, 'Taal en communicatie CLIL', 'TC', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('4E88786B-74FE-4D7D-955A-A4D91A177A17', 6, 2, 'Wellness & lifestyle', 'WL', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('4D904190-58B7-40F1-A607-85878C1DFDAB', 6, 2, 'Agrotechnieken dier', 'AGRO', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('9554B9C4-5796-4909-8194-F15DBE89A73F', 6, 2, 'Applicatie- en databeheer', 'ADB', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('A9F9C01A-BF88-4B93-A4B2-899D8B89E7AD', 6, 2, 'Biotechnologische en chemische technieken', 'BIOCT', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('E3A95E63-B101-41F9-A0AF-1A1A2385D829', 6, 2, 'Bedrijfsorganisatie', 'BORG', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('592F869E-9C7E-49D3-87F1-8E13DD3C3709', 6, 2, 'Commerciële organisatie', 'CO', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('C9007734-0D21-4D95-9AB5-7A21C7D6B111', 6, 2, 'Gezondheidszorg', 'GZ', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('1BE575AF-3B0E-4138-9648-F8680076A724', 6, 2, 'Opvoeding en begeleiding', 'OBG', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('7365DD3D-B0BE-4654-AA7C-77C1BE780C60', 6, 2, 'Sportbegeleiding', 'SPBG', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('56C0632E-C9F6-42E5-B338-B1E3F82E7B90', 6, 2, 'Tuinaanleg en beheer', 'TAB', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('ADD06E46-0D48-431D-BD0C-1E57607AF824', 6, 2, 'Wellness en schoonheid', 'WS', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('0199E0FD-33FE-4039-B26D-989BA09B4212', 6, 4, 'Haar- en schoonheidsverzorging', 'HSV', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('6014A276-F048-461F-ABBD-5CDEC057A9C8', 6, 4, 'Organisatie en logistiek', 'ORL', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('D6B6FF79-AF5F-4A33-B290-6E45BE47F11D', 6, 4, 'Plant, dier en milieu', 'PDM', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('499E696C-3FC2-4FB5-952E-2CB753697040', 6, 4, 'Zorg en welzijn', 'ZW', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('85C61467-88E8-4CD2-B5F8-39D56BE9C9B0', 6, 4, 'Restaurant en keuken', 'RK', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('43738979-83FA-4E12-BED8-04741AB80F49', 6, 4, 'Assistentie in wonen, zorg en welzijn', 'AWZW', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('D28EF329-29E9-42E1-8817-5B7EA88127F1', 6, 4, 'Basiszorg en ondersteuning', 'BZ', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('76770FBF-A7B9-42F1-A81E-2EBE51562BB4', 6, 4, 'Dier en milieu', 'DM', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('C50D4E60-A9C6-4035-AD85-1464B2A048A5', 6, 4, 'Groenaanleg- en beheer', 'GB', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('6240D4F9-92DD-4A6E-9B65-B94D3EA8E2FB', 6, 4, 'Grootkeuken en catering', 'GC', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('9F15D925-9257-4EB6-A6BE-52140ECF18C5', 6, 4, 'Haarverzorging', 'HV', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('483E9214-3669-4AAB-9CCD-1C211B16B862', 6, 4, 'Logistiek', 'LOG', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('8D44281C-AD8D-42F5-B4DA-25825FFB094E', 6, 4, 'Onthaal, organisatie en sales', 'OOS', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('02DC7649-8FA5-42A8-8B6D-4F62774134ED', 6, 4, 'Schoonheidsverzorging (bso)', 'SV', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('64B301C6-5175-41B6-B176-01A1D76BBD2F', 6, 4, 'Haarstilist', 'HS', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('50679758-AAEF-44B7-8ED7-69D58A50B4F2', 6, 4, 'Kantooradministratie en gegevensbeheer (Business Support)', 'BS', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('E1ACA7B3-61CC-4806-AA63-64F69A953336', 6, 4, 'Kinderzorg', 'KZ', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('CA82195A-9B74-42F0-8360-1D55A67645FE', 6, 4, 'Thuis- en bejaardenzorg/Zorgkundige', 'TZBZ', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('DA0D094D-9DEA-4BCA-97DA-C4704DCD428A', 6, 4, 'Tuinaanleg en -onderhoud', 'TUIN', 0);
INSERT INTO tbl_school_direction (guid, schoolId, departmentId, name, code, deleted) VALUES('2D25F745-8662-44E1-B565-097703C13D64', 6, 4, 'Veehouderij en landbouwteelten', 'VH', 0);

-- -- --NAVIGATION
SET @nId = (SELECT id FROM tbl_navigation WHERE link="configuration" AND `type` = "M");
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 0, 5, 'direction', 'Richting', 'arrow-left', 'blue', 0);

-- -- --TABLE
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "direction"), 1, NULL, NULL, 'School', 'linked.school.formatted.badge.name', 0, 0, 20, 0, 0, null, null, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "direction"), 2, NULL, NULL, 'Afdeling', 'linked.department.name', 0, 0, 20, 0, 0, null, null, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "direction"), 3, NULL, NULL, 'Naam', 'name', 1, 1, 0, 0, 1, 1, 'asc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "direction"), 3, NULL, NULL, 'Code', 'code', 1, 1, 100, 0, 0, null, null, 0);

-- HR
-- --TABLES
CREATE TABLE `tbl_hr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `creatorUserId` int(11) NOT NULL,
  `insz` varchar(16) NOT NULL,
  `name` varchar(254) NOT NULL,
  `firstName` varchar(254) NOT NULL,
  `sex` set('M','F','X') NOT NULL DEFAULT 'X',
  `phone` varchar(16) DEFAULT NULL,
  `email` varchar(254) DEFAULT NULL,
  `statusId` int(11) NOT NULL,
  `informatId` int(11) DEFAULT NULL,
  `schoolEmail` varchar(254) DEFAULT NULL,
  `cv` varchar(64) DEFAULT NULL,
  `certificate` varchar(254) DEFAULT NULL,
  `proof` tinyint(1) NOT NULL DEFAULT 0,
  `start` date NOT NULL,
  `end` date DEFAULT NULL,
  `laptopUsage` tinyint(1) NOT NULL DEFAULT 0,
  `functionDescriptionReceived` tinyint(1) NOT NULL DEFAULT 0,
  `roleId` varchar(128) NOT NULL,
  `info` blob DEFAULT NULL,
  `lastActionUserId` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_hr_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hrId` int(11) NOT NULL,
  `action` set('CREATE','UPDATE','DELETE') NOT NULL DEFAULT 'CREATE',
  `data` blob DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `creatorUserId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_hr_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `schoolId` int(11) NOT NULL,
  `name` varchar(254) NOT NULL,
  `code` varchar(16) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_hr_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tbl_hr_status (name, deleted) VALUES('In Dienst', 0);
INSERT INTO tbl_hr_status (name, deleted) VALUES('Uit Dienst', 0);

-- --NAVIGATION
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', 0, 'M', 0, 92, 'hr', 'HR', 'users-group', 'blue', 0);
SET @nId = (SELECT id FROM tbl_navigation WHERE link="hr" AND `type` = "M");
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 0, 1, 'overview', 'Overzicht', 'users-group', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 1, 1, 'role', 'Functies', 'list', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 1, 2, 'flow', 'Flow', 'arrow-guide', 'blue', 0);

-- --NAVIGATION SETTING
INSERT INTO tbl_navigation_setting (navigationId, `key`, value) VALUES(@nId, '_', 0x6F76657276696577);
INSERT INTO tbl_navigation_setting (navigationId, `key`, value) VALUES(@nId, 'sex', 0x463A56726F75770D0A4D3A4D616E0D0A583A4765656E206765736C61636874206F706765676576656E);
INSERT INTO tbl_navigation_setting (navigationId, `key`, value) VALUES(@nId, 'laptopUsage', 0x303A50726F66657373696F6E65656C2C313A50726F66657373696F6E65656C202B2050726976C3A9);

-- --ROUTE
INSERT INTO tbl_route (routeGroupId, `method`, route, controller, callback, apiNoAuth, deleted) VALUES(2, 'ANY', '{view}/hr/{what?}/{id?}', '\\Controllers\\API\\HRController', 'any', 0, 0);

-- --TABLE
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "overview"), 1, NULL, NULL, 'Verzonden op', 'formatted.datetime', 1, 0, 200, 1, 1, 1, 'desc', 0);

-- SCHOOL
ALTER TABLE tbl_school ADD hrEmail varchar(254) NULL;
ALTER TABLE tbl_school CHANGE hrEmail hrEmail varchar(254) NULL AFTER eetjemeeSmartschoolGroup;

-- INFORMAT
-- --STUDENT EMAIL
ALTER TABLE tbl_informat_student_email ADD communication tinyint(1) DEFAULT 1 NOT NULL;

-- SCHOOL DEPARTMENT
-- --TABLE
ALTER TABLE tbl_school_department ADD informatClassId BLOB NULL;
ALTER TABLE tbl_school_department CHANGE informatClassId informatClassId BLOB NULL AFTER name;

-- REMEDY
RENAME TABLE tbl_school_course_informat_employee_classgroup TO tbl_school_course_informat_employee_student_classgroup;
ALTER TABLE tbl_school_course_informat_employee_student_classgroup DROP PRIMARY KEY;
ALTER TABLE tbl_school_course_informat_employee_student_classgroup ADD informatStudentId INT NOT NULL;
ALTER TABLE tbl_school_course_informat_employee_student_classgroup CHANGE informatStudentId informatStudentId INT NOT NULL AFTER informatEmployeeId;
ALTER TABLE tbl_school_course_informat_employee_student_classgroup ADD CONSTRAINT tbl_school_course_informat_employee_student_classgroup_pk PRIMARY KEY (schoolCourseId,informatEmployeeId,informatStudentId,informatClassgroupId);


-- SETTINGS
INSERT INTO tbl_setting_tab (name, icon, `order`, `default`, deleted) VALUES('RingRing', NULL, 12, 0, 0);
INSERT INTO tbl_setting (id, settingTabId, name, `type`, `options`, value, readonly, `order`, deleted) VALUES('ringring.key', (SELECT id FROM tbl_setting_tab WHERE `name`="RingRing"), 'Key', 'input', NULL, 0x44363636354631462D323846432D343030322D424446322D364233313132443645434230, 0, 1, 0);

INSERT INTO tbl_setting (id, settingTabId, name, `type`, `options`, value, readonly, `order`, deleted) VALUES('cron.ringring.import.active', 1, 'cron.ringring.import.active', 'input', NULL, 0x30, 1, NULL, 0);
INSERT INTO tbl_setting (id, settingTabId, name, `type`, `options`, value, readonly, `order`, deleted) VALUES('cron.eetjemee.import.active', 1, 'cron.eetjemee.import.active', 'input', NULL, 0x30, 1, NULL, 0);

UPDATE tbl_setting SET settingTabId=1, name='DB Versie', `type`='input', `options`=NULL, value=0x352E312E30, readonly=1, `order`=99, deleted=0 WHERE id='db.version';
UPDATE tbl_setting SET settingTabId=1, name='Versie', `type`='input', `options`=NULL, value=0x352E312E30, readonly=0, `order`=3, deleted=0 WHERE id='site.version';