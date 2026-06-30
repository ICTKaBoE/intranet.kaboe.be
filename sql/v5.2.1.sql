-- INFORMAT
CREATE TABLE `tbl_informat_classgroup_teacher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `informatClassgroupId` int(11) NOT NULL,
  `informatEmployeeId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE tbl_informat_registration ADD virtualStart DATE NULL;
ALTER TABLE tbl_informat_registration CHANGE virtualStart virtualStart DATE NULL AFTER `start`;
ALTER TABLE tbl_informat_registration ADD virtualEnd DATE NULL;
ALTER TABLE tbl_informat_registration CHANGE virtualEnd virtualEnd DATE NULL AFTER `end`;
ALTER TABLE tbl_informat_registration_class ADD virtualStart DATE NULL;
ALTER TABLE tbl_informat_registration_class CHANGE virtualStart virtualStart DATE NULL AFTER `start`;
ALTER TABLE tbl_informat_registration_class ADD virtualEnd DATE NULL;
ALTER TABLE tbl_informat_registration_class CHANGE virtualEnd virtualEnd DATE NULL AFTER `end`;


-- STUDENT
-- --NAVIGATION
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', 0, 'M', 0, 93, 'student', 'Leerlingenbeheer', 'users-group', 'blue', 0);
SET @nId = (SELECT id FROM tbl_navigation WHERE link="student" AND `type` = "M");

INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 0, 1, 'overview', 'Overzicht', 'users-group', 'blue', 0);

-- --NAVIGATION SETTING
INSERT INTO tbl_navigation_setting (navigationId, `key`, value) VALUES(@nId, '_', 0x6F76657276696577);

-- --ROUTING
INSERT INTO tbl_route (routeGroupId, `method`, route, controller, callback, apiNoAuth, `order`, deleted) VALUES(2, 'ANY', '{view}/student/{what?}/{id?}', '\\Controllers\\API\\StudentController', 'any', 0, 1, 0);

-- SCHOOL
ALTER TABLE tbl_school CHANGE sync syncEmployee tinyint(1) DEFAULT 1 NOT NULL;
ALTER TABLE tbl_school ADD syncStudent BOOL DEFAULT 1 NOT NULL;
ALTER TABLE tbl_school CHANGE syncStudent syncStudent BOOL DEFAULT 1 NOT NULL AFTER syncEmployee;
ALTER TABLE tbl_school ADD syncStudentDefaultMemberOf blob DEFAULT NULL NULL;
ALTER TABLE tbl_school CHANGE syncStudentDefaultMemberOf syncStudentDefaultMemberOf blob DEFAULT NULL NULL AFTER hrEmail;
ALTER TABLE tbl_school ADD syncEmployeeDefaultMemberOf BLOB NULL;
ALTER TABLE tbl_school CHANGE syncEmployeeDefaultMemberOf syncEmployeeDefaultMemberOf BLOB NULL AFTER syncStudentDefaultMemberOf;
ALTER TABLE tbl_school DROP COLUMN hrEmail;

ALTER TABLE tbl_school CHANGE syncEmployeeCompanyName syncEmployeeCompanyName varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL NULL AFTER syncEmployee;
ALTER TABLE tbl_school CHANGE syncEmployeeOU syncEmployeeOU text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL NULL AFTER syncEmployeeCompanyName;
ALTER TABLE tbl_school CHANGE syncEmployeeDefaultMemberOf syncEmployeeDefaultMemberOf blob DEFAULT NULL NULL AFTER syncEmployeeOU;
ALTER TABLE tbl_school CHANGE syncStudentDefaultMemberOf syncStudentDefaultMemberOf blob DEFAULT NULL NULL AFTER syncStudentOU;
ALTER TABLE tbl_school CHANGE syncUpdateMail syncUpdateMail text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL NULL AFTER syncStudentDefaultMemberOf;

ALTER TABLE tbl_school CHANGE eetjemeeKey eetjemeeKeyStudents varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL NULL;
ALTER TABLE tbl_school CHANGE eetjemeeSmartschoolGroup eetjemeeStudentSmartschoolGroup varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL NULL;
ALTER TABLE tbl_school ADD eetjemeeEmployeeSmartschoolGroup varchar(254) NULL;
ALTER TABLE tbl_school CHANGE eetjemeeEmployeeSmartschoolGroup eetjemeeEmployeeSmartschoolGroup varchar(254) NULL AFTER eetjemeeStudentSmartschoolGroup;

ALTER TABLE tbl_school CHANGE eetjemeeStudentSmartschoolGroup smsGroupStudents varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL NULL;
ALTER TABLE tbl_school CHANGE eetjemeeEmployeeSmartschoolGroup smsGroupEmployee varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL NULL;
ALTER TABLE tbl_school CHANGE smsGroupEmployee smsGroupEmployee varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL NULL AFTER smsGroupStudents;

ALTER TABLE tbl_school ADD smsSyncClassTeachers BOOL DEFAULT 0 NOT NULL;
ALTER TABLE tbl_school CHANGE smsSyncClassTeachers smsSyncClassTeachers BOOL DEFAULT 0 NOT NULL AFTER eetjemeeKeyStudents;
ALTER TABLE tbl_school CHANGE smsGroupStudents smsGroupStudents varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL NULL AFTER smsSyncClassTeachers;
ALTER TABLE tbl_school ADD eetjemeeKeyEmployee varchar(254) NULL;
ALTER TABLE tbl_school CHANGE eetjemeeKeyEmployee eetjemeeKeyEmployee varchar(254) NULL AFTER eetjemeeKeyStudents;

-- SYNC
UPDATE tbl_navigation SET deleted = 1 WHERE link='sync' AND `type`='M';

-- DEFAULT
UPDATE tbl_setting SET settingTabId=1, name='DB Versie', `type`='input', `options`=NULL, value=0x352E322E31, readonly=1, `order`=99, deleted=0 WHERE id='db.version';
UPDATE tbl_setting SET settingTabId=1, name='Versie', `type`='input', `options`=NULL, value=0x352E322E31, readonly=0, `order`=3, deleted=0 WHERE id='site.version';