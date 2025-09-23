CREATE TABLE `tbl_accident_document` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `alias` varchar(254) NOT NULL,
  `name` text NOT NULL,
  `ext` varchar(8) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_accident_location` (
  `id` varchar(8) NOT NULL,
  `categoryId` varchar(8) DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_accident_party` (
  `id` varchar(8) NOT NULL,
  `name` varchar(254) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_accident_status` (
  `id` varchar(8) NOT NULL,
  `name` varchar(128) NOT NULL,
  `color` varchar(128) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_bike_distance_type` (
  `id` varchar(8) NOT NULL,
  `name` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_general_language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `code` varchar(8) NOT NULL,
  `name` varchar(254) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_general_message_type` (
  `id` varchar(8) NOT NULL,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_general_nationality` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `code` varchar(8) NOT NULL,
  `name` varchar(254) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_helpdesk_category` (
  `id` varchar(8) NOT NULL,
  `categoryId` varchar(8) DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_helpdesk_priority` (
  `id` varchar(8) NOT NULL,
  `name` varchar(128) NOT NULL,
  `color` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_helpdesk_status` (
  `id` varchar(8) NOT NULL,
  `name` varchar(128) NOT NULL,
  `color` varchar(128) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_library_type` (
  `id` varchar(8) NOT NULL,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_management_treshhold` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(128) NOT NULL,
  `min` double NOT NULL,
  `max` double NOT NULL,
  `color` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_navigation_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `navigationId` int(11) NOT NULL,
  `key` varchar(254) NOT NULL,
  `value` longblob DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_navigation_tabledef` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `navigationId` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `priority` int(11) DEFAULT NULL,
  `type` varchar(128) DEFAULT NULL,
  `title` varchar(254) DEFAULT NULL,
  `data` varchar(254) NOT NULL,
  `orderable` tinyint(1) NOT NULL DEFAULT 1,
  `searchable` tinyint(1) NOT NULL DEFAULT 1,
  `width` int(11) NOT NULL DEFAULT 20,
  `render` tinyint(1) NOT NULL DEFAULT 0,
  `defaultOrder` tinyint(1) NOT NULL DEFAULT 0,
  `defaultOrderOrder` int(11) DEFAULT NULL,
  `defaultOrderDirection` set('asc','desc') DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `showtime` datetime NOT NULL,
  `type` set('normal','valid','invalid') NOT NULL DEFAULT 'normal',
  `link` text DEFAULT NULL,
  `delay` int(11) NOT NULL DEFAULT 10000,
  `message` text NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_order_category` (
  `id` varchar(8) NOT NULL,
  `categoryId` varchar(8) DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_order_status` (
  `id` varchar(8) NOT NULL,
  `name` varchar(128) NOT NULL,
  `color` varchar(128) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_source` (
  `id` varchar(64) NOT NULL,
  `tokenType` varchar(64) DEFAULT NULL,
  `tokenUntil` datetime DEFAULT NULL,
  `tokenValue` blob DEFAULT NULL,
  `identityEndpoint` blob NOT NULL,
  `identityGrantType` varchar(128) NOT NULL,
  `identityClientId` varchar(254) NOT NULL,
  `identityClientSecret` varchar(254) NOT NULL,
  `identityScope` blob NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_security_group_navigation` (
  `securityGroupId` int(11) NOT NULL,
  `navigationId` int(11) NOT NULL,
  PRIMARY KEY (`securityGroupId`,`navigationId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_sync_action` (
  `id` varchar(8) NOT NULL,
  `name` varchar(64) NOT NULL,
  `color` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_tempreg_treshhold` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `min` double NOT NULL,
  `max` double NOT NULL,
  `color` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

RENAME TABLE tbl_country TO tbl_general_country;
RENAME TABLE tbl_helpdesk_ticket TO tbl_helpdesk;

INSERT INTO tbl_route (routeGroupId, `method`, route, controller, callback, apiNoAuth, deleted) VALUES(2, 'ANY', '{view}/registration/{what?}/{id?}', '\Controllers\API\RegistrationController', 'any', 0, 0);
INSERT INTO tbl_route (routeGroupId, `method`, route, controller, callback, apiNoAuth, deleted) VALUES(2, 'ANY', '{view}/notification/{what?}/{id?}', '\Controllers\API\NotificationController', 'any', 0, 0);
UPDATE tbl_route SET routeGroupId=2, `method`='ANY', route='{view}/general/{what?}/{id?}', controller='\Controllers\API\GeneralController', callback='any', apiNoAuth=0, deleted=0 WHERE id=45;

INSERT INTO tbl_source (id, identityEndpoint, identityGrantType, identityClientId, identityClientSecret, identityScope, deleted) VALUES('kaboe', 0x68747470733A2F2F7777772E6964656E746974797365727665722E62652F636F6E6E6563742F746F6B656E, 'client_credentials', 'informat_customer_KaBoe', 'mxl0onpr4KPlAOI2fn6ma4gy_9qfis0BLXt5NHB6lx4', 0x6170695F696E666F726D61745F7361735F6C6565726C696E67656E2E766F6F72696E73636872696A76696E67656E2E3032343830320D0A6170695F696E666F726D61745F7361735F6C6565726C696E67656E2E766F6F72696E73636872696A76696E67656E2E3032343831310D0A6170695F696E666F726D61745F7361735F6C6565726C696E67656E2E766F6F72696E73636872696A76696E67656E2E3032343833360D0A6170695F696E666F726D61745F7361735F6C6565726C696E67656E2E766F6F72696E73636872696A76696E67656E2E3131303036340D0A6170695F696E666F726D61745F7361735F6C6565726C696E67656E2E766F6F72696E73636872696A76696E67656E2E3131353931350D0A6170695F696E666F726D61745F7361735F6C6565726C696E67656E2E766F6F72696E73636872696A76696E67656E2E3131353932330D0A6170695F696E666F726D61745F7361735F6C6565726C696E67656E2E6C6565726C696E67656E2E3032343830320D0A6170695F696E666F726D61745F7361735F6C6565726C696E67656E2E6C6565726C696E67656E2E3032343831310D0A6170695F696E666F726D61745F7361735F6C6565726C696E67656E2E6C6565726C696E67656E2E3032343833360D0A6170695F696E666F726D61745F7361735F6C6565726C696E67656E2E6C6565726C696E67656E2E3131303036340D0A6170695F696E666F726D61745F7361735F6C6565726C696E67656E2E6C6565726C696E67656E2E3131353931350D0A6170695F696E666F726D61745F7361735F6C6565726C696E67656E2E6C6565726C696E67656E2E3131353932330D0A6170695F696E666F726D61745F7361735F706572736F6E65656C2E706572736F6E65656C2E3032343830320D0A6170695F696E666F726D61745F7361735F706572736F6E65656C2E706572736F6E65656C2E3032343831310D0A6170695F696E666F726D61745F7361735F706572736F6E65656C2E706572736F6E65656C2E3032343833360D0A6170695F696E666F726D61745F7361735F706572736F6E65656C2E706572736F6E65656C2E3131303036340D0A6170695F696E666F726D61745F7361735F706572736F6E65656C2E706572736F6E65656C2E3131353931350D0A6170695F696E666F726D61745F7361735F706572736F6E65656C2E706572736F6E65656C2E313135393233, 0);

ALTER TABLE tbl_helpdesk ADD subject varchar(254) NULL;
ALTER TABLE tbl_helpdesk CHANGE subject subject varchar(254) NULL AFTER category;

ALTER TABLE tbl_navigation DROP COLUMN minimumRights;

ALTER TABLE tbl_user ADD entraCompany varchar(64) NOT NULL;
ALTER TABLE tbl_user CHANGE entraCompany entraCompany varchar(64) NOT NULL AFTER entraId;

ALTER TABLE tbl_school ADD virtual BOOL DEFAULT 0 NOT NULL;
ALTER TABLE tbl_school CHANGE virtual virtual BOOL DEFAULT 0 NOT NULL AFTER id;
ALTER TABLE tbl_school ADD parentSchoolId INT DEFAULT 0 NOT NULL;
ALTER TABLE tbl_school CHANGE parentSchoolId parentSchoolId INT DEFAULT 0 NOT NULL AFTER virtual;

ALTER TABLE tbl_school_institute ADD sourceId varchar(8) DEFAULT '' NOT NULL;
ALTER TABLE tbl_school CHANGE sourceId sourceId VARCHAR(8) DEFAULT '' NOT NULL AFTER dynamicTeam;

ALTER TABLE tbl_security_group DROP COLUMN permission;

ALTER TABLE tbl_general_country CHANGE translatedName `name` VARCHAR(254) NOT NULL AFTER officialName;

ALTER TABLE tbl_informat_classgroup ADD administrativeGroupCode varchar(8) NOT NULL;
ALTER TABLE tbl_informat_classgroup CHANGE administrativeGroupCode administrativeGroupCode varchar(8) NOT NULL AFTER schoolyear;

DROP TABLE tbl_mapping;

UPDATE tbl_setting SET settingTabId=1, name='DB Versie', `type`='input', `options`=NULL, value=0x342E352E30, readonly=1, `order`=99, deleted=0 WHERE id='db.version';
UPDATE tbl_setting SET settingTabId=1, name='Versie', `type`='input', `options`=NULL, value=0x342E352E30, readonly=0, `order`=3, deleted=0 WHERE id='site.version';