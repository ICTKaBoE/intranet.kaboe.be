ALTER TABLE tbl_module ADD defaultRights varchar(8) DEFAULT 1000 NOT NULL AFTER assignUserRights;
ALTER TABLE tbl_check_student_relation_insz ADD informatStudentId int NULL AFTER id;
ALTER TABLE tbl_check_student_relation_insz ADD informatInstituteNumber int NULL AFTER informatStudentId;

INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, deleted ) VALUES ('checklists', 'Controlelijsten', 'check', 'green', 'app', 3, NULL, 1, '0000', 0);
INSERT INTO tbl_setting_tab (moduleId, name, icon, `order`, deleted) VALUES (1, 'Informat', NULL, 5, 0); 
INSERT INTO tbl_setting ( id, moduleId, settingTabId, name, `type`, `options`, value, deleted ) VALUES ( 'informat.username', 1, 5, 'Gebruikersnaam', 'input', NULL, 0x6A616E6F2E6C616D70616572744065656B6C6F2E6265, 0);
INSERT INTO tbl_setting ( id, moduleId, settingTabId, name, `type`, `options`, value, deleted ) VALUES ( 'informat.password', 1, 5, 'Wachtwoord', 'input', NULL, 0x5069616E6F6D616E50413132350D0A, 0);
INSERT INTO tbl_setting (id, moduleId, settingTabId, name, `type`, `options`, value, deleted) VALUES('informat.get.host', 1, 5, 'GET Host', 'input', NULL, 0x776562736572766963652E696E666F726D6174736F6674776172652E62652F7773496E666F726D61742E61736D782F, 0);

UPDATE tbl_module SET module = 'bike', name = 'Fietsvergoeding', icon = 'bike', iconBackgroundColor = 'blue', `scope` = 'app', `order` = 1, redirect = NULL, assignUserRights = 1, defaultRights = '1100', deleted = 0 WHERE id = 2;
UPDATE tbl_setting SET moduleId = 1, settingTabId = 1, name = 'Versie', `type` = 'input', `options` = NULL, value = 0x322E31, deleted = 0 WHERE id = 'site.version';

CREATE TABLE tbl_school_institute (
	id int auto_increment NOT NULL,
	schoolId int NOT NULL,
	instituteNumber int NOT NULL,
	deleted bool DEFAULT 0 NOT NULL,
	CONSTRAINT tbl_school_institute_pk PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;

INSERT INTO db_intranet2.tbl_school_institute (schoolId, instituteNumber, deleted) VALUES(1, 110064, 0);
INSERT INTO db_intranet2.tbl_school_institute (schoolId, instituteNumber, deleted) VALUES(1, 115915, 0);
INSERT INTO db_intranet2.tbl_school_institute (schoolId, instituteNumber, deleted) VALUES(2, 24836, 0);
INSERT INTO db_intranet2.tbl_school_institute (schoolId, instituteNumber, deleted) VALUES(2, 115923, 0);
INSERT INTO db_intranet2.tbl_school_institute (schoolId, instituteNumber, deleted) VALUES(3, 24811, 0);
INSERT INTO db_intranet2.tbl_school_institute (schoolId, instituteNumber, deleted) VALUES(4, 24802, 0);
