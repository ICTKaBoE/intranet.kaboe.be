--CONFIGURATION - DEPARTMENT
SET @configurationId = (SELECT id FROM tbl_navigation WHERE link="configuration");
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @configurationId and link="department"), 4, NULL, NULL, 'Lokalen', 'formatted.managementRooms', 0, 0, 300, 0, 0, NULL, NULL, 0);

--MANAGEMENT - ROOM
SET @managementId = (SELECT id FROM tbl_navigation WHERE link="management");
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @managementId and link="room"), 5, NULL, NULL, 'Alias', 'alias', 1, 1, 100, 0, 0, NULL, NULL, 0);
UPDATE tbl_navigation_tabledef SET `order`=6 WHERE id=(select id from tbl_navigation where parentId = @managementId and link="room") AND `data`='formatted.full';

--INFORMAT - CONFIG
ALTER TABLE tbl_informat_student_config ADD schoolId INT DEFAULT 0 NOT NULL;
ALTER TABLE tbl_informat_student_config CHANGE schoolId schoolId INT DEFAULT 0 NOT NULL AFTER informatStudentId;
ALTER TABLE tbl_informat_student_config MODIFY COLUMN registrationId int(11) DEFAULT 0 NOT NULL;
ALTER TABLE tbl_informat_student_config MODIFY COLUMN classgroupId int(11) DEFAULT 0 NOT NULL;
ALTER TABLE tbl_informat_student_config ADD `rank` INT NULL;
ALTER TABLE tbl_informat_student_config CHANGE `rank` `rank` INT NULL AFTER classgroupId;

--STUDENT - DOCUMENT
CREATE TABLE `tbl_student_document` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `alias` varchar(254) NOT NULL,
  `name` text NOT NULL,
  `ext` varchar(8) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--STUDENT - NAVIGATION
SET @studentId = (SELECT id FROM tbl_navigation WHERE link="student" AND type="M");
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @studentId, 'P', 1, 99, 'documents', 'Documenten', 'file', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @studentId, 'P', 1, 100, 'settings', 'Instellingen', 'settings', 'blue', 0);

--STUDENT - OVERVIEW - TABLEDEF
DELETE FROM tbl_navigation_tabledef WHERE navigationId = (SELECT id FROM tbl_navigation WHERE parentId = @studentId AND link="overview");
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @studentId and link="overview"), 1, NULL, NULL, 'School', 'linked.school.formatted.badge.name', 0, 0, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @studentId and link="overview"), 3, NULL, NULL, 'Naam', 'linked.informatStudent.name', 1, 1, 0, 0, 1, 2, 'asc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @studentId and link="overview"), 4, NULL, NULL, 'Voornaam', 'linked.informatStudent.firstName', 1, 1, 0, 0, 1, 3, 'asc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @studentId and link="overview"), 2, NULL, NULL, 'Klas', 'linked.classgroup.code', 1, 1, 100, 0, 1, 1, 'asc', 0);

--STUDENT - DOCUMENT - TABLEDEF
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @studentId and link="documents"), 1, NULL, '', 'Alias', 'alias', 1, 1, 0, 0, 1, 1, 'asc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @studentId and link="documents"), 2, NULL, '', 'Naam', 'name', 1, 1, 350, 0, 0, NULL, '', 0);

--EXAMSCHEDULE
-- INSERT INTO tbl_route (routeGroupId, `method`, route, controller, callback, apiNoAuth, `order`, deleted) VALUES(2, 'ANY', '{view}/examschedule/{what?}/{id?}', '\\Controllers\\API\\ExamScheduleController', 'any', 0, 1, 0);

-- INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', 0, 'M', 0, 82, 'examschedule', 'Examenrooster', 'calendar-week', 'teal', 0);
-- SET @examscheduleId = (SELECT id FROM tbl_navigation WHERE link="examschedule" AND type="M");
-- INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @examscheduleId, 'P', 0, 1, 'build', 'Rooster bouwen', 'calendar-week', 'blue', 0);
-- INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @examscheduleId, 'P', 1, 1, 'assign', 'Uren toewijzen', 'clock', 'blue', 0);
-- INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @examscheduleId, 'P', 1, 2, 'period', 'Periode', 'calendar-time', 'blue', 0);
-- INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @examscheduleId, 'P', 1, 100, 'settings', 'Instellingen', 'settings', 'blue', 0);
-- INSERT INTO tbl_navigation_setting (navigationId, `key`, value) VALUES(@examscheduleId, '_', 0x6275696C64);

-- INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @examscheduleId and link="assign"), 1, NULL, NULL, 'Leerkracht', 'formatted.fullNameReversed', 1, 1, 0, 0, 1, 1, 'asc', 0);
-- INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @examscheduleId and link="assign"), 2, NULL, NULL, 'Gepresteerde uren', 'hours.workedHours', 0, 0, 100, 0, 0, NULL, NULL, 0);
-- INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @examscheduleId and link="assign"), 3, NULL, NULL, 'Uren examen', 'hours.examHours', 0, 0, 100, 0, 0, NULL, NULL, 0);
-- INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @examscheduleId and link="assign"), 4, NULL, NULL, 'Geen examen', 'hours.noExamHours', 0, 0, 100, 0, 0, NULL, NULL, 0);
-- INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @examscheduleId and link="assign"), 5, NULL, NULL, 'Uren toezicht', 'hours.formatted.supervisionHoursToDo', 0, 0, 100, 0, 0, NULL, NULL, 0);
-- INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @examscheduleId and link="assign"), 6, NULL, NULL, 'Uren toezicht effectief', 'hours.supervisionHours', 0, 0, 100, 0, 0, NULL, NULL, 0);
-- INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @examscheduleId and link="assign"), 7, NULL, NULL, 'Balans', 'hours.formatted.balans', 0, 0, 100, 0, 0, NULL, NULL, 0);

--CRON
CREATE TABLE `tbl_cron` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `expression` varchar(254) DEFAULT NULL,
  `name` varchar(254) DEFAULT NULL,
  `class` varchar(254) DEFAULT NULL,
  `function` varchar(254) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `lastRun` datetime DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tbl_cron (expression, name, class, `function`, active, lastRun, enabled) VALUES('0 * * * *', 'Informat - Import', 'informat', 'import', 0, NULL, 1);
INSERT INTO tbl_cron (expression, name, class, `function`, active, lastRun, enabled) VALUES('0 * * * *', 'M365 - Import Users', 'm365', 'importUsers', 0, NULL, 1);
INSERT INTO tbl_cron (expression, name, class, `function`, active, lastRun, enabled) VALUES('15 * * * *', 'Local - Prepare', 'local', 'prepare', 0, NULL, 1);
INSERT INTO tbl_cron (expression, name, class, `function`, active, lastRun, enabled) VALUES('30 * * * *', 'Sync - Prepare', 'sync', 'prepare', 0, NULL, 1);
INSERT INTO tbl_cron (expression, name, class, `function`, active, lastRun, enabled) VALUES('0 * * * *', 'EetJeMee - Import', 'eetjemee', 'import', 0, NULL, 1);
INSERT INTO tbl_cron (expression, name, class, `function`, active, lastRun, enabled) VALUES('0 * * * *', 'Smartschool - Sync Group', 'smartschool', 'syncGroups', 0, NULL, 1);
INSERT INTO tbl_cron (expression, name, class, `function`, active, lastRun, enabled) VALUES('* * * * *', 'Mail - Send', 'mail', 'send', 0, NULL, 1);
INSERT INTO tbl_cron (expression, name, class, `function`, active, lastRun, enabled) VALUES('* * * * *', 'Smartschool - Send', 'smartschool', 'send', 0, NULL, 1);
INSERT INTO tbl_cron (expression, name, class, `function`, active, lastRun, enabled) VALUES('59 7 * * *', 'M365 - Warn User Password Expiration', 'm365', 'warnUserPasswordExpiration', 0, NULL, 1);
INSERT INTO tbl_cron (expression, name, class, `function`, active, lastRun, enabled) VALUES('0 * * * *', 'M365 - Import Computers', 'm365', 'importComputers', 0, NULL, 1);
INSERT INTO tbl_cron (expression, name, class, `function`, active, lastRun, enabled) VALUES('0 * * * *', 'JAMF - Import', 'jamf', 'import', 0, NULL, 1);
INSERT INTO tbl_cron (expression, name, class, `function`, active, lastRun, enabled) VALUES('35 * * * *', 'Smartschool - Sync Class Teachers', 'smartschool', 'syncClassTeachers', 0, NULL, 1);
INSERT INTO tbl_cron (expression, name, class, `function`, active, lastRun, enabled) VALUES('45 * * * *', 'Smartschool - Import Courses', 'smartschool', 'importCourses', 0, NULL, 1);
INSERT INTO tbl_cron (expression, name, class, `function`, active, lastRun, enabled) VALUES('* * * * *', 'RingRing - Import', 'ringring', 'import', 0, NULL, 1);
INSERT INTO tbl_cron (expression, name, class, `function`, active, lastRun, enabled) VALUES('0 * * * *', 'Smartschool - Sync User Data', 'smartschool', 'syncUserData', 0, NULL, 1);

DELETE FROM tbl_setting WHERE id='cron.country.import.active';
DELETE FROM tbl_setting WHERE id='cron.eetjemee.import.active';
DELETE FROM tbl_setting WHERE id='cron.informat.import.active';
DELETE FROM tbl_setting WHERE id='cron.jamf.import.active';
DELETE FROM tbl_setting WHERE id='cron.local.prepare.active';
DELETE FROM tbl_setting WHERE id='cron.m365.importComputers.active';
DELETE FROM tbl_setting WHERE id='cron.m365.importSignInTimes.active';
DELETE FROM tbl_setting WHERE id='cron.m365.importUsers.active';
DELETE FROM tbl_setting WHERE id='cron.m365.warnUserPasswordExpiration.active';
DELETE FROM tbl_setting WHERE id='cron.mail.send.active';
DELETE FROM tbl_setting WHERE id='cron.ringring.import.active';
DELETE FROM tbl_setting WHERE id='cron.smartschool.importCourses.active';
DELETE FROM tbl_setting WHERE id='cron.smartschool.send.active';
DELETE FROM tbl_setting WHERE id='cron.smartschool.syncClassTeachers.active';
DELETE FROM tbl_setting WHERE id='cron.smartschool.syncGroups.active';
DELETE FROM tbl_setting WHERE id='cron.sync.prepare.active';

--SOURCE
ALTER TABLE tbl_source ADD url varchar(254) NULL;
ALTER TABLE tbl_source CHANGE url url varchar(254) NULL AFTER id;

--SMARTSCHOOL
CREATE TABLE `tbl_smartschool_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `internalId` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `name` varchar(254) NOT NULL,
  `firstName` varchar(254) NOT NULL,
  `username` text NOT NULL,
  `rank` int(11) DEFAULT NULL,
  `scannableCode` varchar(254) NOT NULL,
  `lastLogin` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_smartschool_user_coaccount` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `index` int(11) DEFAULT NULL,
  `name` varchar(254) DEFAULT NULL,
  `firstName` varchar(254) DEFAULT NULL,
  `email` text DEFAULT NULL,
  `emailVerified` tinyint(1) DEFAULT NULL,
  `phone` varchar(64) DEFAULT NULL,
  `mobile` varchar(64) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `authenticatorApp` tinyint(1) DEFAULT NULL,
  `yubikey` tinyint(1) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `responsible` tinyint(1) DEFAULT NULL,
  `lastLogin` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--LAST STEP
UPDATE tbl_setting SET settingTabId=1, name='DB Versie', `type`='input', `options`=NULL, value=0x352E332E30, readonly=1, `order`=99, deleted=0 WHERE id='db.version';
UPDATE tbl_setting SET settingTabId=1, name='Versie', `type`='input', `options`=NULL, value=0x352E332E30, readonly=0, `order`=3, deleted=0 WHERE id='site.version';