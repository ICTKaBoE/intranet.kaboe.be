--REMEDY
ALTER TABLE tbl_remedy_type DROP COLUMN closeRegistrationAt;
ALTER TABLE tbl_remedy_moment ADD closeRegistrationAt INT DEFAULT 0 NOT NULL;
ALTER TABLE tbl_remedy_moment CHANGE closeRegistrationAt closeRegistrationAt INT DEFAULT 0 NOT NULL AFTER hourId;

ALTER TABLE tbl_remedy_type ADD allowMultipleRegistrationsForThisTypeOnSameDayAmount INT DEFAULT 1 NOT NULL;
ALTER TABLE tbl_remedy_type CHANGE allowMultipleRegistrationsForThisTypeOnSameDayAmount allowMultipleRegistrationsForThisTypeOnSameDayAmount INT DEFAULT 1 NOT NULL AFTER allowMultipleRegistrationsForSameCourseOnSameDay;

ALTER TABLE tbl_remedy_type ADD allowMultipleRegistrationsForSameCourseOnSameDayAmount INT DEFAULT 1 NOT NULL;
ALTER TABLE tbl_remedy_type CHANGE allowMultipleRegistrationsForSameCourseOnSameDayAmount allowMultipleRegistrationsForSameCourseOnSameDayAmount INT DEFAULT 1 NOT NULL AFTER allowMultipleRegistrationsForSameCourseOnSameDay;

ALTER TABLE tbl_remedy_type MODIFY COLUMN departmentId varchar(64) NOT NULL;
ALTER TABLE tbl_remedy_moment MODIFY COLUMN departmentId varchar(64) NOT NULL;

--VERSION CONTROL
UPDATE tbl_setting SET settingTabId=1, name='DB Versie', `type`='input', `options`=NULL, value=0x352E322E33, readonly=1, `order`=99, deleted=0 WHERE id='db.version';
UPDATE tbl_setting SET settingTabId=1, name='Versie', `type`='input', `options`=NULL, value=0x352E322E33, readonly=0, `order`=3, deleted=0 WHERE id='site.version';