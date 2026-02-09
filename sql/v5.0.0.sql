ALTER TABLE tbl_school ADD street varchar(254) NULL;
ALTER TABLE tbl_school CHANGE street street varchar(254) NULL AFTER color;
ALTER TABLE tbl_school ADD `number` INT NULL;
ALTER TABLE tbl_school CHANGE `number` `number` INT NULL AFTER street;
ALTER TABLE tbl_school ADD bus varchar(8) NULL;
ALTER TABLE tbl_school CHANGE bus bus varchar(8) NULL AFTER `number`;
ALTER TABLE tbl_school ADD zipcode varchar(8) NULL;
ALTER TABLE tbl_school CHANGE zipcode zipcode varchar(8) NULL AFTER bus;
ALTER TABLE tbl_school ADD city varchar(254) NULL;
ALTER TABLE tbl_school CHANGE city city varchar(254) NULL AFTER zipcode;
ALTER TABLE tbl_school ADD countryId INT NULL;
ALTER TABLE tbl_school CHANGE countryId countryId INT NULL AFTER city;
ALTER TABLE tbl_school ADD phone varchar(32) NULL;
ALTER TABLE tbl_school CHANGE phone phone varchar(32) NULL AFTER countryId;
ALTER TABLE tbl_school ADD smartschoolSourceId varchar(16) NULL;
ALTER TABLE tbl_school CHANGE smartschoolSourceId smartschoolSourceId varchar(16) NULL AFTER dynamicTeam;

UPDATE tbl_school ts SET street = (SELECT street FROM tbl_school_address tsa WHERE tsa.schoolId = ts.id), number = (SELECT number FROM tbl_school_address tsa WHERE tsa.schoolId = ts.id), bus = (SELECT bus FROM tbl_school_address tsa WHERE tsa.schoolId = ts.id), zipcode = (SELECT zipcode FROM tbl_school_address tsa WHERE tsa.schoolId = ts.id), city = (SELECT city FROM tbl_school_address tsa WHERE tsa.schoolId = ts.id), countryId = (SELECT (SELECT id FROM tbl_general_country tgc WHERE tsa.country = tgc.name) FROM tbl_school_address tsa WHERE tsa.schoolId = ts.id), phone = (SELECT phone FROM tbl_school_address tsa WHERE tsa.schoolId = ts.id);
DROP TABLE tbl_school_address;
ALTER TABLE tbl_school_institute DROP COLUMN sourceId;

ALTER TABLE tbl_source MODIFY COLUMN identityEndpoint blob DEFAULT NULL NULL;
ALTER TABLE tbl_source MODIFY COLUMN identityGrantType varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL NULL;
ALTER TABLE tbl_source MODIFY COLUMN identityClientId varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL NULL;
ALTER TABLE tbl_source MODIFY COLUMN identityClientSecret varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL NULL;
ALTER TABLE tbl_source MODIFY COLUMN identityScope blob DEFAULT NULL NULL;

ALTER TABLE tbl_source CHANGE identityEndpoint host blob DEFAULT NULL NULL;
ALTER TABLE tbl_source CHANGE host host blob DEFAULT NULL NULL AFTER id;

ALTER TABLE tbl_source ADD password varchar(128) NULL;
ALTER TABLE tbl_source CHANGE password password varchar(128) NULL AFTER identityScope;

ALTER TABLE tbl_navigation ADD management tinyint(1) DEFAULT 0 NOT NULL;
ALTER TABLE tbl_navigation CHANGE management management tinyint(1) DEFAULT 0 NOT NULL AFTER `type`;
UPDATE tbl_navigation SET management = 1 WHERE link IN ('settings', 'export');

INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `link` = "schools"), 3, NULL, NULL, 'Importeren', 'formatted.icon.import', 0, 0, 20, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `link` = "schools"), 4, NULL, NULL, 'Synchroniseren', 'formatted.icon.sync', 0, 0, 20, 0, 0, NULL, NULL, 0);

CREATE TABLE `tbl_general_schoolyear` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `name` varchar(8) NOT NULL,
  `start` date DEFAULT NULL,
  `end` date DEFAULT NULL,
  `current` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tbl_general_schoolyear (guid, name, `start`, `end`, `current`) VALUES('DF0C8760-9C7D-42F8-8DB1-F5AD86F9A62C', '2023-24', '2023-08-01', '2024-07-31', 0);
INSERT INTO tbl_general_schoolyear (guid, name, `start`, `end`, `current`) VALUES('DD1EFEFD-47BA-4C06-8C46-17F1754818FC', '2024-25', '2024-08-01', '2025-07-31', 0);
INSERT INTO tbl_general_schoolyear (guid, name, `start`, `end`, `current`) VALUES('60F263D2-FC41-4462-B15E-6B5460A78F59', '2025-26', '2025-08-01', '2026-07-31', 1);
INSERT INTO tbl_general_schoolyear (guid, name, `start`, `end`, `current`) VALUES('6BE566F7-CE02-4AAE-BC87-AA9920E7FA9F', '2026-27', '2026-08-01', '2027-07-31', 0);
INSERT INTO tbl_general_schoolyear (guid, name, `start`, `end`, `current`) VALUES('38F3A74B-DE32-4AD8-BA6A-DE8DFA3048AE', '2027-28', '2027-08-01', '2028-07-31', 0);
INSERT INTO tbl_general_schoolyear (guid, name, `start`, `end`, `current`) VALUES('01B05EEB-DC38-430D-968B-7FFBD95FC67B', '2028-29', '2028-08-01', '2029-07-31', 0);
INSERT INTO tbl_general_schoolyear (guid, name, `start`, `end`, `current`) VALUES('7F322B78-0A25-41D8-89AD-C969C664CD22', '2029-30', '2029-08-01', '2030-07-31', 0);
INSERT INTO tbl_general_schoolyear (guid, name, `start`, `end`, `current`) VALUES('8665269A-F784-4413-ABA1-53B76F439C9A', '2030-31', '2030-08-01', '2031-07-31', 0);
INSERT INTO tbl_general_schoolyear (guid, name, `start`, `end`, `current`) VALUES('9F238A85-D0E9-4AC9-B485-D836BBD8762B', '2031-32', '2031-08-01', '2032-07-31', 0);
INSERT INTO tbl_general_schoolyear (guid, name, `start`, `end`, `current`) VALUES('4080ADE8-BE3E-4858-AB76-24362CB96B09', '2032-33', '2032-08-01', '2033-07-31', 0);

ALTER TABLE tbl_informat_student CHANGE isdn insz varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL NULL;

ALTER TABLE tbl_route ADD `order` INT DEFAULT 1 NOT NULL;
ALTER TABLE tbl_route CHANGE `order` `order` INT DEFAULT 1 NOT NULL AFTER apiNoAuth;

CREATE TABLE `tbl_mail_attachment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mailId` int(11) NOT NULL,
  `name` varchar(254) NOT NULL,
  `path` text NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE tbl_mail ADD fromEmail varchar(254) NULL;
ALTER TABLE tbl_mail CHANGE fromEmail fromEmail varchar(254) NULL AFTER id;
ALTER TABLE tbl_mail ADD fromName varchar(254) NULL;
ALTER TABLE tbl_mail CHANGE fromName fromName varchar(254) NULL AFTER fromEmail;

ALTER TABLE tbl_holliday MODIFY COLUMN `start` date NOT NULL;
ALTER TABLE tbl_holliday MODIFY COLUMN `end` date DEFAULT NULL NULL;

-- ACTUAL UPDATE
-- Informat
ALTER TABLE tbl_informat_student_address ADD domicile BOOL DEFAULT 0 NOT NULL;

-- School
ALTER TABLE tbl_school ADD import BOOL DEFAULT 1 NOT NULL;
ALTER TABLE tbl_school CHANGE import import BOOL DEFAULT 1 NOT NULL AFTER `phone`;
ALTER TABLE tbl_school ADD sync BOOL DEFAULT 1 NOT NULL;
ALTER TABLE tbl_school CHANGE sync sync BOOL DEFAULT 1 NOT NULL AFTER import;
ALTER TABLE tbl_school ADD syncEmployeeCompanyName varchar(254) NULL;
ALTER TABLE tbl_school CHANGE syncEmployeeCompanyName syncEmployeeCompanyName varchar(254) NULL AFTER sync;
ALTER TABLE tbl_school ADD syncStudentCompanyName varchar(254) NULL;
ALTER TABLE tbl_school CHANGE syncStudentCompanyName syncStudentCompanyName varchar(254) NULL AFTER syncEmployeeCompanyName;
ALTER TABLE tbl_school ADD syncEmployeeOU TEXT NULL;
ALTER TABLE tbl_school CHANGE syncEmployeeOU syncEmployeeOU TEXT NULL AFTER syncStudentCompanyName;
ALTER TABLE tbl_school ADD syncStudentOU TEXT NULL;
ALTER TABLE tbl_school CHANGE syncStudentOU syncStudentOU TEXT NULL AFTER syncEmployeeOU;

CREATE TABLE tbl_school_department (
	id INT auto_increment NOT NULL,
	guid varchar(36) NOT NULL,
  schoolId int(11) NOT NULL,
	name varchar(254) NOT NULL,
	deleted BOOL DEFAULT 0 NOT NULL,
	CONSTRAINT tbl_school_department_pk PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_school_course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `name` varchar(254) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_school_course_informat_employee_classgroup` (
  `schoolCourseId` int(11) NOT NULL,
  `informatEmployeeId` int(11) NOT NULL,
  `informatClassgroupId` int(11) NOT NULL,
  PRIMARY KEY (`schoolCourseId`,`informatEmployeeId`,`informatClassgroupId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_school_course_informat_student` (
  `schoolCourseId` int(11) NOT NULL,
  `informatStudentId` int(11) NOT NULL,
  PRIMARY KEY (`schoolCourseId`,`informatStudentId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_school_hour` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `schoolId` int(11) NOT NULL,
  `start` time NOT NULL,
  `end` time NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Informat
ALTER TABLE tbl_informat_employee ADD instituteId int(11) DEFAULT 1 NOT NULL;
ALTER TABLE tbl_informat_student ADD instituteId int(11) DEFAULT 1 NOT NULL;

-- Configuration
SET @nId = (SELECT id FROM tbl_navigation WHERE link="configuration" AND `type` = "M");
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 0, 3, 'department', 'Afdelingen', 'window', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 0, 4, 'course', 'Vakken', 'book', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 0, 5, 'hours', 'Lesblokken', 'clock', 'blue', 0);

INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "department"), 1, NULL, NULL, 'School', 'linked.school.formatted.badge.name', 0, 0, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "department"), 2, NULL, NULL, 'Naam', 'name', 1, 1, 0, 0, 1, 1, 'asc', 0);

INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "hours"), 1, NULL, NULL, 'School', 'linked.school.formatted.badge.name', 0, 0, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "hours"), 2, NULL, NULL, 'Start', 'start', 1, 1, 0, 0, 1, 1, 'asc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "hours"), 3, NULL, NULL, 'Eind', 'end', 1, 1, 0, 0, 0, NULL, NULL, 0);

-- Accident
SET @nId = (SELECT id FROM tbl_navigation WHERE link="accident" AND `type` = "M");

ALTER TABLE tbl_accident MODIFY COLUMN supervision tinyint(1) DEFAULT 0 NOT NULL;
ALTER TABLE tbl_accident MODIFY COLUMN `status` varchar(8) DEFAULT 'S' NOT NULL;
ALTER TABLE tbl_accident ADD visibleDescription BLOB NULL;
ALTER TABLE tbl_accident CHANGE visibleDescription visibleDescription BLOB NULL AFTER description;

ALTER TABLE tbl_accident ADD witness BOOL DEFAULT 0 NOT NULL;
ALTER TABLE tbl_accident CHANGE witness witness BOOL DEFAULT 0 NOT NULL AFTER policePVNumber;
ALTER TABLE tbl_accident ADD witnessAfter BOOL DEFAULT 0 NOT NULL;
ALTER TABLE tbl_accident CHANGE witnessAfter witnessAfter BOOL DEFAULT 0 NOT NULL AFTER witnessId;
ALTER TABLE tbl_accident CHANGE witnessId witnessInfo varchar(254) NULL;
ALTER TABLE tbl_accident MODIFY COLUMN witnessInfo varchar(254) NULL;
ALTER TABLE tbl_accident ADD witnessAfterInfo varchar(254) NULL;
ALTER TABLE tbl_accident CHANGE witnessAfterInfo witnessAfterInfo varchar(254) NULL AFTER witnessAfter;
ALTER TABLE tbl_accident ADD whenAndWho TEXT NULL;
ALTER TABLE tbl_accident CHANGE whenAndWho whenAndWho TEXT NULL AFTER witnessAfterInfo;
ALTER TABLE tbl_accident ADD materialDamage BOOL DEFAULT 0 NOT NULL;
ALTER TABLE tbl_accident CHANGE materialDamage materialDamage BOOL DEFAULT 0 NOT NULL AFTER visibleDescription;
ALTER TABLE tbl_accident ADD physicalDamage BOOL DEFAULT 0 NOT NULL;
ALTER TABLE tbl_accident CHANGE physicalDamage physicalDamage BOOL DEFAULT 0 NOT NULL AFTER materialDamage;

-- Remedy
CREATE TABLE `tbl_remedy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `creatorUserId` int(11) NOT NULL,
  `typeId` int(11) NOT NULL,
  `schoolId` int(11) NOT NULL,
  `departmentId` int(11) NOT NULL,
  `courseId` int(11) NOT NULL,
  `momentId` int(11) DEFAULT NULL,
  `informatStudentId` int(11) NOT NULL,
  `classgroupId` int(11) NOT NULL,
  `remark` blob DEFAULT NULL,
  `assignedDate` date DEFAULT NULL,
  `present` tinyint(1) DEFAULT NULL,
  `computerType` varchar(8) DEFAULT NULL,
  `computerTypeOther` varchar(254) DEFAULT NULL,
  `computerPassword` varchar(254) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_remedy_computer_type` (
  `id` varchar(8) NOT NULL,
  `name` varchar(254) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_remedy_moment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `schoolyearId` int(11) DEFAULT NULL,
  `schoolId` int(11) NOT NULL,
  `departmentId` int(11) NOT NULL,
  `buildingId` int(11) NOT NULL,
  `roomId` int(11) NOT NULL,
  `typeId` int(11) NOT NULL,
  `description` varchar(254) NOT NULL,
  `seats` int(11) DEFAULT NULL,
  `full` tinyint(1) NOT NULL DEFAULT 0,
  `dayOfWeek` int(11) NOT NULL DEFAULT 0,
  `date` date DEFAULT NULL,
  `hourId` int(11) DEFAULT NULL,
  `courseId` int(11) DEFAULT NULL,
  `informatEmployeeId` int(11) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_remedy_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `schoolId` int(11) NOT NULL,
  `departmentId` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `color` varchar(64) NOT NULL DEFAULT 'red',
  `from` date DEFAULT NULL,
  `until` date DEFAULT NULL,
  `courseDependsOnSkore` tinyint(1) NOT NULL DEFAULT 0,
  `closeRegistrationAt` int(11) NOT NULL DEFAULT 0,
  `manualAssignDate` tinyint(1) NOT NULL DEFAULT 0,
  `onComputer` tinyint(1) NOT NULL DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tbl_route (routeGroupId, `method`, route, controller, callback, apiNoAuth, deleted) VALUES(2, 'ANY', '{view}/remedy/{what?}/{id?}', '\\Controllers\\API\\RemedyController', 'any', 0, 0);

-- Remedy Navigation
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', 0, 'M', 0, 81, 'remedy', 'Remediëring', 'rotate-2', 'indigo', 0);
SET @nId = (SELECT id FROM tbl_navigation WHERE link="remedy" AND `type` = "M");

INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 0, 2, 'assign', 'Toewijzen', 'calendar-user', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 0, 5, 'export', 'Exporteren', 'file-export', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 0, 1, 'mine', 'Mijn Registraties', 'user-scan', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 1, 2, 'moment', 'Inhaalmomenten', 'calendar', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 0, 4, 'overview', 'Overzicht', 'list', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 0, 3, 'presence', 'Aanwezigheden', 'user-check', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 1, 1, 'type', 'Type', 'list', 'blue', 0);

-- Remedy Navigation Setting
INSERT INTO tbl_navigation_setting (navigationId, `key`, value) VALUES(@nId, '_', 0x6D696E65);

-- Remedy Navigation TableDef
-- Type
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "type"), 1, NULL, NULL, 'Afdeling', 'linked.department.name', 0, 0, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "type"), 2, NULL, NULL, 'Naam', 'name', 1, 1, 0, 0, 1, 1, 'asc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "type"), 3, NULL, NULL, 'Van', 'formatted.from', 1, 1, 100, 1, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "type"), 4, NULL, NULL, 'Tot', 'formatted.until', 1, 1, 100, 1, 0, NULL, NULL, 0);

-- Moment
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "moment"), 1, NULL, NULL, 'Schooljaar', 'linked.schoolyear.name', 1, 1, 125, 0, 1, 1, 'asc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "moment"), 2, NULL, NULL, 'Afdeling', 'linked.department.name', 1, 1, 125, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "moment"), 3, NULL, NULL, 'Lokaal', 'formatted.buildingRoom', 1, 1, 125, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "moment"), 4, NULL, NULL, 'Type', 'linked.type.name', 1, 1, 125, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "moment"), 5, NULL, NULL, 'Datum', 'formatted.date', 1, 1, 125, 1, 1, 2, 'asc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "moment"), 6, NULL, NULL, 'Lesuur', 'linked.hour.formatted.startEnd', 1, 1, 125, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "moment"), 7, NULL, NULL, 'Vak', 'linked.course.name', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "moment"), 8, NULL, NULL, 'Beschrijving', 'description', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "moment"), 10, NULL, NULL, 'Plaatsen', 'formatted.seats', 0, 1, 50, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "moment"), 9, NULL, NULL, 'Leerkracht', 'linked.informatEmployee.formatted.fullNameReversed', 1, 1, 175, 0, 0, NULL, NULL, 0);

-- Mine
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "mine"), 2, NULL, NULL, 'Type', 'linked.type.name', 0, 0, 125, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "mine"), 7, NULL, NULL, 'Leerling', 'linked.informatStudent.formatted.fullNameReversed', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "mine"), 5, NULL, NULL, 'Vak', 'linked.course.name', 1, 1, 200, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "mine"), 1, NULL, NULL, 'School', 'linked.school.formatted.badge.name', 0, 0, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "mine"), 6, NULL, NULL, 'Klas', 'linked.classgroup.name', 1, 1, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "mine"), 3, NULL, NULL, 'Datum', 'formatted.date', 1, 1, 125, 1, 1, 1, 'asc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "mine"), 4, NULL, NULL, 'Lesuur', 'linked.moment.linked.hour.formatted.startEnd', 1, 1, 125, 0, 1, 2, 'asc', 0);

-- Presence
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "presence"), 2, NULL, NULL, 'Type', 'linked.type.name', 0, 0, 125, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "presence"), 5, NULL, NULL, 'Vak', 'linked.course.name', 1, 1, 200, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "presence"), 1, NULL, NULL, 'School', 'linked.school.formatted.badge.name', 0, 0, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "presence"), 3, NULL, NULL, 'Datum', 'formatted.date', 1, 1, 125, 1, 1, 1, 'asc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "presence"), 4, NULL, NULL, 'Lesuur', 'linked.moment.linked.hour.formatted.startEnd', 1, 1, 125, 0, 1, 2, 'asc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "presence"), 6, NULL, NULL, 'Lokaal', 'linked.moment.linked.room.formatted.buildingRoom', 1, 1, 125, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "presence"), 7, NULL, NULL, 'Omschrijving', 'linked.moment.description', 0, 1, 0, 0, 0, NULL, NULL, 0);

-- Overview
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "overview"), 2, NULL, '', 'Type', 'linked.type.name', 0, 0, 125, 0, 0, NULL, '', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "overview"), 5, NULL, '', 'Vak', 'linked.course.name', 1, 1, 200, 0, 0, NULL, '', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "overview"), 1, NULL, '', 'School', 'linked.school.formatted.badge.name', 0, 0, 100, 0, 0, NULL, '', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "overview"), 3, NULL, '', 'Datum', 'formatted.date', 1, 1, 125, 1, 1, 1, 'asc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "overview"), 4, NULL, '', 'Lesuur', 'linked.moment.linked.hour.formatted.startEnd', 1, 1, 125, 0, 1, 2, 'asc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "overview"), 6, NULL, '', 'Lokaal', 'linked.moment.linked.room.formatted.buildingRoom', 1, 1, 125, 0, 0, NULL, '', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((SELECT id FROM tbl_navigation WHERE `parentId` = @nId AND `link` = "overview"), 7, NULL, '', 'Omschrijving', 'linked.moment.description', 0, 1, 0, 0, 0, NULL, '', 0);

-- Smartschool
CREATE TABLE `tbl_smartschool_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sourceId` varchar(32) NOT NULL,
  `subject` varchar(254) NOT NULL,
  `body` blob NOT NULL,
  `sendAfterDateTime` datetime NOT NULL DEFAULT current_timestamp(),
  `sentDateTime` datetime DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_smartschool_message_receiver` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `messageId` int(11) NOT NULL,
  `username` varchar(254) NOT NULL,
  `account` int(11) NOT NULL DEFAULT 0,
  `copyToLvs` tinyint(1) NOT NULL DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sync
ALTER TABLE tbl_sync ADD `badgeId` varchar(64) DEFAULT NULL;
ALTER TABLE tbl_sync CHANGE `badgeId` `badgeId` varchar(64) DEFAULT NULL AFTER `jobTitle`;

-- Settings
INSERT INTO tbl_setting (id, settingTabId, name, `type`, `options`, value, readonly, `order`, deleted) VALUES('cron.country.import.active', 1, 'cron.country.import.active', 'input', NULL, 0x30, 1, NULL, 0);
INSERT INTO tbl_setting (id, settingTabId, name, `type`, `options`, value, readonly, `order`, deleted) VALUES('cron.informat.import.active', 1, 'cron.informat.import.active', 'input', NULL, 0x30, 1, NULL, 0);
INSERT INTO tbl_setting (id, settingTabId, name, `type`, `options`, value, readonly, `order`, deleted) VALUES('cron.jamf.import.active', 1, 'cron.jamf.import.active', 'input', NULL, 0x30, 1, NULL, 0);
INSERT INTO tbl_setting (id, settingTabId, name, `type`, `options`, value, readonly, `order`, deleted) VALUES('cron.local.prepare.active', 1, 'cron.local.prepare.active', 'input', NULL, 0x30, 1, NULL, 0);
INSERT INTO tbl_setting (id, settingTabId, name, `type`, `options`, value, readonly, `order`, deleted) VALUES('cron.m365.importUsers.active', 1, 'cron.m365.importUsers.active', 'input', NULL, 0x30, 1, NULL, 0);
INSERT INTO tbl_setting (id, settingTabId, name, `type`, `options`, value, readonly, `order`, deleted) VALUES('cron.m365.importComputers.active', 1, 'cron.m365.importComputers.active', 'input', NULL, 0x30, 1, NULL, 0);
INSERT INTO tbl_setting (id, settingTabId, name, `type`, `options`, value, readonly, `order`, deleted) VALUES('cron.m365.importSignInTimes.active', 1, 'cron.m365.importSignInTimes.active', 'input', NULL, 0x30, 1, NULL, 0);
INSERT INTO tbl_setting (id, settingTabId, name, `type`, `options`, value, readonly, `order`, deleted) VALUES('cron.m365.warnUserPasswordExpiration.active', 1, 'cron.m365.warnUserPasswordExpiration.active', 'input', NULL, 0x30, 1, NULL, 0);
INSERT INTO tbl_setting (id, settingTabId, name, `type`, `options`, value, readonly, `order`, deleted) VALUES('cron.mail.send.active', 1, 'cron.mail.send.active', 'input', NULL, 0x30, 1, NULL, 0);
INSERT INTO tbl_setting (id, settingTabId, name, `type`, `options`, value, readonly, `order`, deleted) VALUES('cron.smartschool.send.active', 1, 'cron.smartschool.send.active', 'input', NULL, 0x30, 1, NULL, 0);
INSERT INTO tbl_setting (id, settingTabId, name, `type`, `options`, value, readonly, `order`, deleted) VALUES('cron.smartschool.importCourses.active', 1, 'cron.smartschool.importCourses.active', 'input', NULL, 0x30, 1, NULL, 0);
INSERT INTO tbl_setting (id, settingTabId, name, `type`, `options`, value, readonly, `order`, deleted) VALUES('cron.sync.prepare.active', 1, 'cron.sync.prepare.active', 'input', NULL, 0x30, 1, NULL, 0);

-- helpdesk - status
ALTER TABLE tbl_helpdesk_status ADD _short varchar(100) NULL;
UPDATE tbl_helpdesk_status SET _short = id;
ALTER TABLE tbl_helpdesk_status DROP PRIMARY KEY;
UPDATE tbl_helpdesk_status SET id = 0;
ALTER TABLE tbl_helpdesk_status MODIFY COLUMN id int(11) DEFAULT NULL auto_increment NOT NULL PRIMARY KEY;

ALTER TABLE tbl_helpdesk MODIFY COLUMN status varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'N' NOT NULL;
UPDATE tbl_helpdesk SET status = (SELECT id FROM tbl_helpdesk_status WHERE _short = status);
ALTER TABLE tbl_helpdesk MODIFY COLUMN status int NOT NULL;
ALTER TABLE tbl_helpdesk_status DROP COLUMN _short;
ALTER TABLE tbl_helpdesk_status ADD closed BOOL DEFAULT 0 NOT NULL;

-- helpdesk - priority
ALTER TABLE tbl_helpdesk_priority ADD _short varchar(100) NULL;
UPDATE tbl_helpdesk_priority SET _short = id;
ALTER TABLE tbl_helpdesk_priority DROP PRIMARY KEY;
UPDATE tbl_helpdesk_priority SET id = 0;
ALTER TABLE tbl_helpdesk_priority MODIFY COLUMN id int(11) DEFAULT NULL auto_increment NOT NULL PRIMARY KEY;

ALTER TABLE tbl_helpdesk MODIFY COLUMN priority varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'N' NOT NULL;
UPDATE tbl_helpdesk SET priority = (SELECT id FROM tbl_helpdesk_priority WHERE _short = priority);
ALTER TABLE tbl_helpdesk MODIFY COLUMN priority int NOT NULL;
ALTER TABLE tbl_helpdesk_priority DROP COLUMN _short;

-- helpdesk - category
ALTER TABLE tbl_helpdesk_category ADD _short varchar(100) NULL;
ALTER TABLE tbl_helpdesk_category ADD _subshort varchar(100) NULL;
UPDATE tbl_helpdesk_category SET _short = id;
UPDATE tbl_helpdesk_category SET _subshort = categoryId WHERE categoryId IS NOT NULL;
UPDATE tbl_helpdesk_category SET id = 0;
UPDATE tbl_helpdesk_category SET categoryId = 0 WHERE categoryId IS NOT NULL;
ALTER TABLE tbl_helpdesk_category MODIFY COLUMN id int(11) DEFAULT NULL auto_increment NOT NULL PRIMARY KEY;
UPDATE tbl_helpdesk_category c SET c.categoryId = (SELECT id FROM tbl_helpdesk_category WHERE _short = c._subshort LIMIT 1) WHERE c._subshort IS NOT NULL;
ALTER TABLE tbl_helpdesk_category MODIFY COLUMN categoryId int(11) DEFAULT NULL;

ALTER TABLE tbl_helpdesk_category ADD managementType varchar(8) NULL;
ALTER TABLE tbl_helpdesk_category CHANGE managementType managementType varchar(8) NULL AFTER name;
UPDATE tbl_helpdesk_category SET managementType = _short WHERE _subshort IS NULL;

UPDATE tbl_helpdesk th SET category = IFNULL((SELECT id FROM tbl_helpdesk_category WHERE _subshort = substring_index(th.category, "-", 1) AND _short = substring_index(th.category, "-", -1)), 0);
ALTER TABLE tbl_helpdesk MODIFY COLUMN category int NOT NULL;
ALTER TABLE tbl_helpdesk_category DROP COLUMN _short;
ALTER TABLE tbl_helpdesk_category DROP COLUMN _subshort;

-- accident - status
ALTER TABLE tbl_accident_status ADD _short varchar(100) NULL;
UPDATE tbl_accident_status SET _short = id;
ALTER TABLE tbl_accident_status DROP PRIMARY KEY;
UPDATE tbl_accident_status SET id = 0;
ALTER TABLE tbl_accident_status MODIFY COLUMN id int(11) DEFAULT NULL auto_increment NOT NULL PRIMARY KEY;

ALTER TABLE tbl_accident MODIFY COLUMN status varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'N' NOT NULL;
UPDATE tbl_accident SET status = (SELECT id FROM tbl_accident_status WHERE _short = status);
ALTER TABLE tbl_accident MODIFY COLUMN status int NOT NULL;
ALTER TABLE tbl_accident_status DROP COLUMN _short;
ALTER TABLE tbl_accident_status ADD `default` BOOL DEFAULT 0 NOT NULL;
ALTER TABLE tbl_accident_status ADD whenInsuranceIsMailed BOOL DEFAULT 0 NOT NULL;
ALTER TABLE tbl_accident_status ADD closed BOOL DEFAULT 0 NOT NULL;

-- accident - party
ALTER TABLE tbl_accident_party ADD _short varchar(100) NULL;
UPDATE tbl_accident_party SET _short = id;
ALTER TABLE tbl_accident_party DROP PRIMARY KEY;
UPDATE tbl_accident_party SET id = 0;
ALTER TABLE tbl_accident_party MODIFY COLUMN id int(11) DEFAULT NULL auto_increment NOT NULL PRIMARY KEY;

ALTER TABLE tbl_accident MODIFY COLUMN party varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'N' NOT NULL;
UPDATE tbl_accident SET party = (SELECT id FROM tbl_accident_party WHERE _short = party);
ALTER TABLE tbl_accident MODIFY COLUMN party int NOT NULL;
ALTER TABLE tbl_accident_party DROP COLUMN _short;

-- accident - location
ALTER TABLE tbl_accident_location ADD _short varchar(100) NULL;
ALTER TABLE tbl_accident_location ADD _subshort varchar(100) NULL;
UPDATE tbl_accident_location SET _short = id;
UPDATE tbl_accident_location SET _subshort = categoryId WHERE categoryId IS NOT NULL;
UPDATE tbl_accident_location SET id = 0;
UPDATE tbl_accident_location SET categoryId = 0 WHERE categoryId IS NOT NULL;
ALTER TABLE tbl_accident_location MODIFY COLUMN id int(11) DEFAULT NULL auto_increment NOT NULL PRIMARY KEY;
UPDATE tbl_accident_location c SET c.categoryId = (SELECT id FROM tbl_accident_location WHERE _short = c._subshort) WHERE c._subshort IS NOT NULL;
ALTER TABLE tbl_accident_location MODIFY COLUMN categoryId int(11) DEFAULT NULL;

UPDATE tbl_accident th SET location = IFNULL((SELECT id FROM tbl_accident_location WHERE _subshort = substring_index(th.location, "-", 1) AND _short = substring_index(th.location, "-", -1)), 0);
ALTER TABLE tbl_accident MODIFY COLUMN location int NOT NULL;
ALTER TABLE tbl_accident_location DROP COLUMN _short;
ALTER TABLE tbl_accident_location DROP COLUMN _subshort;

ALTER TABLE tbl_accident_location ADD extendedOptions BOOL DEFAULT 0 NOT NULL;
ALTER TABLE tbl_accident_location CHANGE extendedOptions extendedOptions BOOL DEFAULT 0 NOT NULL AFTER name;

ALTER TABLE tbl_accident_party ADD extendedOptions varchar(8) DEFAULT NULL;
ALTER TABLE tbl_accident_party CHANGE extendedOptions extendedOptions varchar(8) DEFAULT NULL AFTER name;

-- ehbo - description
ALTER TABLE tbl_ehbo_description ADD _short varchar(100) NULL;
UPDATE tbl_ehbo_description SET _short = id;
ALTER TABLE tbl_ehbo_description DROP PRIMARY KEY;
UPDATE tbl_ehbo_description SET id = 0;
ALTER TABLE tbl_ehbo_description MODIFY COLUMN id int(11) DEFAULT NULL auto_increment NOT NULL PRIMARY KEY;

ALTER TABLE tbl_ehbo MODIFY COLUMN description varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'N' NOT NULL;
UPDATE tbl_ehbo SET description = (SELECT id FROM tbl_ehbo_description WHERE _short = description);
ALTER TABLE tbl_ehbo MODIFY COLUMN description int NOT NULL;
ALTER TABLE tbl_ehbo_description DROP COLUMN _short;

-- ehbo - firsthelp
ALTER TABLE tbl_ehbo_firsthelp ADD _short varchar(100) NULL;
UPDATE tbl_ehbo_firsthelp SET _short = id;
ALTER TABLE tbl_ehbo_firsthelp DROP PRIMARY KEY;
UPDATE tbl_ehbo_firsthelp SET id = 0;
ALTER TABLE tbl_ehbo_firsthelp MODIFY COLUMN id int(11) DEFAULT NULL auto_increment NOT NULL PRIMARY KEY;

ALTER TABLE tbl_ehbo MODIFY COLUMN firstHelp varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'N' NOT NULL;
UPDATE tbl_ehbo SET firstHelp = (SELECT id FROM tbl_ehbo_firsthelp WHERE _short = firstHelp);
ALTER TABLE tbl_ehbo MODIFY COLUMN firstHelp int NOT NULL;
ALTER TABLE tbl_ehbo_firsthelp DROP COLUMN _short;

-- ehbo - victimtype
ALTER TABLE tbl_ehbo_victimtype ADD _short varchar(100) NULL;
UPDATE tbl_ehbo_victimtype SET _short = id;
ALTER TABLE tbl_ehbo_victimtype DROP PRIMARY KEY;
UPDATE tbl_ehbo_victimtype SET id = 0;
ALTER TABLE tbl_ehbo_victimtype MODIFY COLUMN id int(11) DEFAULT NULL auto_increment NOT NULL PRIMARY KEY;

ALTER TABLE tbl_ehbo MODIFY COLUMN victimType varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'N' NOT NULL;
UPDATE tbl_ehbo SET victimType = (SELECT id FROM tbl_ehbo_victimtype WHERE _short = victimType);
ALTER TABLE tbl_ehbo MODIFY COLUMN victimType int NOT NULL;
ALTER TABLE tbl_ehbo_victimtype DROP COLUMN _short;
ALTER TABLE tbl_ehbo_victimtype ADD `type` varchar(8) NOT NULL;

-- Violence - Cause
ALTER TABLE tbl_violence_cause ADD _short varchar(100) NULL;
UPDATE tbl_violence_cause SET _short = id;
ALTER TABLE tbl_violence_cause DROP PRIMARY KEY;
UPDATE tbl_violence_cause SET id = 0;
ALTER TABLE tbl_violence_cause MODIFY COLUMN id int(11) DEFAULT NULL auto_increment NOT NULL PRIMARY KEY;

ALTER TABLE tbl_violence MODIFY COLUMN cause varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;
UPDATE tbl_violence SET cause = (SELECT id FROM tbl_violence_cause WHERE _short = cause);
ALTER TABLE tbl_violence_cause DROP COLUMN _short;

-- Violence - Consequence
ALTER TABLE tbl_violence_consequence ADD _short varchar(100) NULL;
UPDATE tbl_violence_consequence SET _short = id;
ALTER TABLE tbl_violence_consequence DROP PRIMARY KEY;
UPDATE tbl_violence_consequence SET id = 0;
ALTER TABLE tbl_violence_consequence MODIFY COLUMN id int(11) DEFAULT NULL auto_increment NOT NULL PRIMARY KEY;

ALTER TABLE tbl_violence MODIFY COLUMN consequence varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;
UPDATE tbl_violence SET consequence = (SELECT id FROM tbl_violence_consequence WHERE _short = consequence);
ALTER TABLE tbl_violence_consequence DROP COLUMN _short;

-- Violence - Damage
ALTER TABLE tbl_violence_damage ADD _short varchar(100) NULL;
UPDATE tbl_violence_damage SET _short = id;
ALTER TABLE tbl_violence_damage DROP PRIMARY KEY;
UPDATE tbl_violence_damage SET id = 0;
ALTER TABLE tbl_violence_damage MODIFY COLUMN id int(11) DEFAULT NULL auto_increment NOT NULL PRIMARY KEY;

ALTER TABLE tbl_violence MODIFY COLUMN damage varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;
UPDATE tbl_violence SET damage = (SELECT id FROM tbl_violence_damage WHERE _short = damage);
ALTER TABLE tbl_violence_damage DROP COLUMN _short;

-- Violence - DamageKind
ALTER TABLE tbl_violence_damage_kind ADD _short varchar(100) NULL;
UPDATE tbl_violence_damage_kind SET _short = id;
ALTER TABLE tbl_violence_damage_kind DROP PRIMARY KEY;
UPDATE tbl_violence_damage_kind SET id = 0;
ALTER TABLE tbl_violence_damage_kind MODIFY COLUMN id int(11) DEFAULT NULL auto_increment NOT NULL PRIMARY KEY;

ALTER TABLE tbl_violence MODIFY COLUMN damageKind varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;
UPDATE tbl_violence SET damageKind = (SELECT id FROM tbl_violence_damage_kind WHERE _short = damageKind);
ALTER TABLE tbl_violence_damage_kind DROP COLUMN _short;

-- Violence - Form
ALTER TABLE tbl_violence_form ADD _short varchar(100) NULL;
UPDATE tbl_violence_form SET _short = id;
ALTER TABLE tbl_violence_form DROP PRIMARY KEY;
UPDATE tbl_violence_form SET id = 0;
ALTER TABLE tbl_violence_form MODIFY COLUMN id int(11) DEFAULT NULL auto_increment NOT NULL PRIMARY KEY;

ALTER TABLE tbl_violence MODIFY COLUMN form varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
UPDATE tbl_violence SET form = (SELECT id FROM tbl_violence_form WHERE _short = form);
ALTER TABLE tbl_violence_form DROP COLUMN _short;

-- Violence - Out
ALTER TABLE tbl_violence_out ADD _short varchar(100) NULL;
UPDATE tbl_violence_out SET _short = id;
ALTER TABLE tbl_violence_out DROP PRIMARY KEY;
UPDATE tbl_violence_out SET id = 0;
ALTER TABLE tbl_violence_out MODIFY COLUMN id int(11) DEFAULT NULL auto_increment NOT NULL PRIMARY KEY;

ALTER TABLE tbl_violence MODIFY COLUMN `out` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
UPDATE tbl_violence SET `out` = (SELECT id FROM tbl_violence_out WHERE _short = `out`);
ALTER TABLE tbl_violence_out DROP COLUMN _short;

-- absent - note
ALTER TABLE tbl_absent_note ADD _short varchar(100) NULL;
UPDATE tbl_absent_note SET _short = id;
ALTER TABLE tbl_absent_note DROP PRIMARY KEY;
UPDATE tbl_absent_note SET id = 0;
ALTER TABLE tbl_absent_note MODIFY COLUMN id int(11) DEFAULT NULL auto_increment NOT NULL PRIMARY KEY;

ALTER TABLE tbl_absent MODIFY COLUMN absentNoteReceived varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'N' NOT NULL;
UPDATE tbl_absent SET absentNoteReceived = (SELECT id FROM tbl_absent_note WHERE _short = absentNoteReceived);
ALTER TABLE tbl_absent MODIFY COLUMN absentNoteReceived int NOT NULL;
ALTER TABLE tbl_absent_note DROP COLUMN _short;

-- absent - payment
ALTER TABLE tbl_absent_payment ADD _short varchar(100) NULL;
UPDATE tbl_absent_payment SET _short = id;
ALTER TABLE tbl_absent_payment DROP PRIMARY KEY;
UPDATE tbl_absent_payment SET id = 0;
ALTER TABLE tbl_absent_payment MODIFY COLUMN id int(11) DEFAULT NULL auto_increment NOT NULL PRIMARY KEY;

ALTER TABLE tbl_absent MODIFY COLUMN paymentOfSubstitute varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'N' NOT NULL;
UPDATE tbl_absent SET paymentOfSubstitute = (SELECT id FROM tbl_absent_payment WHERE _short = paymentOfSubstitute);
ALTER TABLE tbl_absent MODIFY COLUMN paymentOfSubstitute int NOT NULL;
ALTER TABLE tbl_absent_payment DROP COLUMN _short;

-- absent - substitute
ALTER TABLE tbl_absent_substitute ADD _short varchar(100) NULL;
UPDATE tbl_absent_substitute SET _short = id;
ALTER TABLE tbl_absent_substitute DROP PRIMARY KEY;
UPDATE tbl_absent_substitute SET id = 0;
ALTER TABLE tbl_absent_substitute MODIFY COLUMN id int(11) DEFAULT NULL auto_increment NOT NULL PRIMARY KEY;

ALTER TABLE tbl_absent MODIFY COLUMN substituteBy varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'N' NOT NULL;
UPDATE tbl_absent SET substituteBy = (SELECT id FROM tbl_absent_substitute WHERE _short = substituteBy);
ALTER TABLE tbl_absent MODIFY COLUMN substituteBy int NOT NULL;
ALTER TABLE tbl_absent_substitute DROP COLUMN _short;

-- order - status
ALTER TABLE tbl_order_status ADD _short varchar(100) NULL;
UPDATE tbl_order_status SET _short = id;
ALTER TABLE tbl_order_status DROP PRIMARY KEY;
UPDATE tbl_order_status SET id = 0;
ALTER TABLE tbl_order_status MODIFY COLUMN id int(11) DEFAULT NULL auto_increment NOT NULL PRIMARY KEY;

ALTER TABLE tbl_order MODIFY COLUMN status varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'N' NOT NULL;
UPDATE tbl_order SET status = (SELECT id FROM tbl_order_status WHERE _short = status);
ALTER TABLE tbl_order MODIFY COLUMN status int NOT NULL;
ALTER TABLE tbl_order_status DROP COLUMN _short;
ALTER TABLE tbl_order_status ADD mailQuote BOOL DEFAULT 0 NOT NULL;
ALTER TABLE tbl_order_status ADD mailAccept BOOL DEFAULT 0 NOT NULL;
ALTER TABLE tbl_order_status ADD mailStatus BOOL DEFAULT 0 NOT NULL;
ALTER TABLE tbl_order_status ADD mailOrder BOOL DEFAULT 0 NOT NULL;
ALTER TABLE tbl_order_status ADD closed BOOL DEFAULT 0 NOT NULL;

-- order - category
ALTER TABLE tbl_order_category ADD _short varchar(100) NULL;
ALTER TABLE tbl_order_category ADD _subshort varchar(100) NULL;
UPDATE tbl_order_category SET _short = id;
UPDATE tbl_order_category SET _subshort = categoryId WHERE categoryId IS NOT NULL;
UPDATE tbl_order_category SET id = 0;
UPDATE tbl_order_category SET categoryId = 0 WHERE categoryId IS NOT NULL;
ALTER TABLE tbl_order_category MODIFY COLUMN id int(11) DEFAULT NULL auto_increment NOT NULL PRIMARY KEY;
UPDATE tbl_order_category c SET c.categoryId = (SELECT id FROM tbl_order_category WHERE _short = c._subshort LIMIT 1) WHERE c._subshort IS NOT NULL;
ALTER TABLE tbl_order_category MODIFY COLUMN categoryId int(11) DEFAULT NULL;

ALTER TABLE tbl_order_category ADD managementType varchar(8) NULL;
ALTER TABLE tbl_order_category CHANGE managementType managementType varchar(8) NULL AFTER name;
UPDATE tbl_order_category SET managementType = _short WHERE _subshort IS NULL;

UPDATE tbl_order_line th SET category = IFNULL((SELECT id FROM tbl_order_category WHERE _subshort = substring_index(th.category, "-", 1) AND _short = substring_index(th.category, "-", -1)), 0);
ALTER TABLE tbl_order_line MODIFY COLUMN category int NOT NULL;
ALTER TABLE tbl_order_category DROP COLUMN _short;
ALTER TABLE tbl_order_category DROP COLUMN _subshort;

-- SETTINGS
UPDATE tbl_setting SET settingTabId=1, name='DB Versie', `type`='input', `options`=NULL, value=0x352E302E30, readonly=1, `order`=99, deleted=0 WHERE id='db.version';
UPDATE tbl_setting SET settingTabId=1, name='Versie', `type`='input', `options`=NULL, value=0x352E302E30, readonly=1, `order`=3, deleted=0 WHERE id='site.version';