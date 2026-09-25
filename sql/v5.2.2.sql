--INFORMAT
CREATE TABLE `tbl_informat_student_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolyearId` int(11) NOT NULL,
  `informatStudentId` int(11) NOT NULL,
  `registrationId` int(11) NOT NULL,
  `classgroupId` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--REMEDY
ALTER TABLE tbl_remedy_type ADD allowMultipleRegistrationsForThisTypeOnSameDay BOOL DEFAULT 0 NOT NULL;
ALTER TABLE tbl_remedy_type ADD allowMultipleRegistrationsForSameCourseOnSameDay BOOL DEFAULT 0 NOT NULL;
ALTER TABLE tbl_remedy_type ADD allowWithoutDate BOOL DEFAULT 0 NOT NULL;
ALTER TABLE tbl_remedy_type CHANGE deleted deleted tinyint(1) DEFAULT 0 NOT NULL AFTER allowMultipleRegistrationsForSameCourseOnSameDay;

--STRATEGIC DASHBOARD
ALTER TABLE tbl_strategicdashboard_item ADD minimumRemark TEXT NULL;
ALTER TABLE tbl_strategicdashboard_item CHANGE minimumRemark minimumRemark TEXT NULL AFTER minimum;
ALTER TABLE tbl_strategicdashboard_item ADD targetRemark TEXT NULL;
ALTER TABLE tbl_strategicdashboard_item CHANGE targetRemark targetRemark TEXT NULL AFTER `target`;

ALTER TABLE tbl_strategicdashboard_item ADD valueRound INT DEFAULT 0 NULL;
ALTER TABLE tbl_strategicdashboard_item ADD valuePrefix varchar(8) NULL;
ALTER TABLE tbl_strategicdashboard_item ADD valueSuffix varchar(8) NULL;
ALTER TABLE tbl_strategicdashboard_item CHANGE deleted deleted tinyint(1) DEFAULT 0 NOT NULL AFTER valueSuffix;

--VERSION CONTROL
UPDATE tbl_setting SET value=0x352E322E32 WHERE id='db.version';
UPDATE tbl_setting SET value=0x352E322E32 WHERE id='site.version';