--REMEDY
ALTER TABLE tbl_remedy_type ADD allowMultipleRegistrationsForThisTypeOnSameDay BOOL DEFAULT 0 NOT NULL;
ALTER TABLE tbl_remedy_type ADD allowMultipleRegistrationsForSameCourseOnSameDay BOOL DEFAULT 0 NOT NULL;
ALTER TABLE tbl_remedy_type ADD allowWithoutDate BOOL DEFAULT 0 NOT NULL;
ALTER TABLE tbl_remedy_type CHANGE deleted deleted tinyint(1) DEFAULT 0 NOT NULL AFTER allowMultipleRegistrationsForSameCourseOnSameDay;

--VERSION CONTROL
UPDATE tbl_setting SET value=0x352E322E32 WHERE id='db.version';
UPDATE tbl_setting SET value=0x352E322E32 WHERE id='site.version';