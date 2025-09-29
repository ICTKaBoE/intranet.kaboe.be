CREATE TABLE `tbl_absent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `creatorUserId` int(11) NOT NULL,
  `creationDateTime` datetime NOT NULL DEFAULT current_timestamp(),
  `schoolId` int(11) NOT NULL,
  `absentUserId` int(11) NOT NULL,
  `volume` varchar(8) NOT NULL,
  `substituteBy` varchar(8) NOT NULL DEFAULT 'S',
  `substituteByOther` varchar(254) DEFAULT NULL,
  `start` date NOT NULL,
  `end` date DEFAULT NULL,
  `paymentOfSubstitute` varchar(8) DEFAULT 'O',
  `paymentOfSubstituteOther` varchar(254) DEFAULT NULL,
  `absentNoteReceived` varchar(8) NOT NULL DEFAULT 'N',
  `notes` text DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_absent_substitute` (
  `id` varchar(8) NOT NULL,
  `name` varchar(254) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_absent_payment` (
  `id` varchar(8) NOT NULL,
  `name` varchar(254) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_absent_note` (
  `id` varchar(8) NOT NULL,
  `name` varchar(254) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tbl_absent_substitute (id, name) VALUES('S', 'Vervanger');
INSERT INTO tbl_absent_substitute (id, name) VALUES('P', 'Lerarenplatform');
INSERT INTO tbl_absent_substitute (id, name) VALUES('O', 'Andere');

INSERT INTO tbl_absent_payment (id, name) VALUES('S10', 'Ziekte personeelslid (langer dan 10 dagen afwezig)');
INSERT INTO tbl_absent_payment (id, name) VALUES('P', 'Lerarenplatform');
INSERT INTO tbl_absent_payment (id, name) VALUES('S', 'Korte vervanging');
INSERT INTO tbl_absent_payment (id, name) VALUES('O', 'Andere');

INSERT INTO tbl_absent_note (id, name) VALUES('R', 'Ontvangen en doorgegeven');
INSERT INTO tbl_absent_note (id, name) VALUES('F', 'Nog niet ontvangen en op te volgen');
INSERT INTO tbl_absent_note (id, name) VALUES('D', '1 dag en geen briefje nodig');

INSERT INTO tbl_route (routeGroupId, `method`, route, controller, callback, apiNoAuth, deleted) VALUES(2, 'ANY', '{view}/absent/{what?}/{id?}', '\\Controllers\\API\\AbsentController', 'any', 0, 0);

INSERT INTO tbl_navigation (routeGroupId, parentId, folderId, `type`, `order`, link, name, icon, color, deleted) VALUES('1', 0, 0, 'M', 10, 'absent', 'Afwezigheid Personeel', 'thermometer', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, folderId, `type`, `order`, link, name, icon, color, deleted) VALUES('1', 108, 0, 'P', 1, 'mine', 'Mijn Aangiftes', 'user-scan', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, folderId, `type`, `order`, link, name, icon, color, deleted) VALUES('1', 108, 0, 'P', 2, 'all', 'Aangiftes', 'temperature', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, folderId, `type`, `order`, link, name, icon, color, deleted) VALUES('1', 108, 0, 'P', 3, 'export', 'Exporteren', 'file-export', 'blue', 0);

INSERT INTO tbl_navigation_setting (navigationId, `key`, value) VALUES(108, '_', 0x6D696E65);

INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES(109, 1, NULL, NULL, 'School', 'linked.school.formatted.badge.name', 0, 0, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES(109, 2, NULL, NULL, 'Aangegeven op', 'formatted.creationDateTime', 1, 1, 175, 1, 1, 1, 'desc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES(109, 3, NULL, NULL, 'Afwezige', 'linked.absentUser.formatted.fullNameReversed', 1, 1, 200, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES(109, 6, NULL, NULL, 'Start', 'formatted.start', 1, 1, 100, 1, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES(109, 7, NULL, NULL, 'Einde', 'formatted.end', 1, 1, 100, 1, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES(109, 8, NULL, NULL, 'Betaling vervanger', 'formatted.paymentOfSubstitute', 0, 1, 250, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES(109, 9, NULL, NULL, 'Ziektebrief ontvangen', 'formatted.absentNoteReceived', 0, 1, 200, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES(109, 5, NULL, NULL, 'Wordt vervangen door', 'formatted.substituteBy', 0, 1, 200, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES(109, 4, NULL, NULL, 'Volume', 'volume', 1, 1, 50, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES(109, 10, NULL, NULL, 'Opmerking', 'notes', 0, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES(110, 1, NULL, NULL, 'School', 'linked.school.formatted.badge.name', 0, 0, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES(110, 2, NULL, NULL, 'Aangegeven op', 'formatted.creationDateTime', 1, 1, 175, 1, 1, 1, 'desc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES(110, 4, NULL, NULL, 'Afwezige', 'linked.absentUser.formatted.fullNameReversed', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES(110, 7, NULL, NULL, 'Start', 'formatted.start', 1, 1, 100, 1, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES(110, 8, NULL, NULL, 'Einde', 'formatted.end', 1, 1, 100, 1, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES(110, 6, NULL, NULL, 'Wordt vervangen door', 'formatted.substituteBy', 0, 1, 200, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES(110, 5, NULL, NULL, 'Volume', 'volume', 1, 1, 50, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES(110, 3, NULL, NULL, 'Door', 'linked.creatorUser.formatted.fullNameReversed', 1, 1, 200, 0, 0, NULL, NULL, 0);

DELETE FROM tbl_setting WHERE id='informat.identity.clientId';
DELETE FROM tbl_setting WHERE id='informat.identity.clientSecret';
DELETE FROM tbl_setting WHERE id='informat.identity.endpoint';
DELETE FROM tbl_setting WHERE id='informat.identity.grantType';
DELETE FROM tbl_setting WHERE id='informat.identity.scope';
DELETE FROM tbl_setting WHERE id='informat.token.type';
DELETE FROM tbl_setting WHERE id='informat.token.until';
DELETE FROM tbl_setting WHERE id='informat.token.value';

ALTER TABLE tbl_navigation DROP COLUMN settings;

UPDATE tbl_setting SET settingTabId=1, name='DB Versie', `type`='input', `options`=NULL, value=0x342E362E30, readonly=1, `order`=99, deleted=0 WHERE id='db.version';
UPDATE tbl_setting SET settingTabId=1, name='Versie', `type`='input', `options`=NULL, value=0x342E362E30, readonly=0, `order`=3, deleted=0 WHERE id='site.version';