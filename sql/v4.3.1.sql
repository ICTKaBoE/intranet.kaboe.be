ALTER TABLE db_intranet_v4.tbl_navigation ADD `type` set('M','F','P','L') DEFAULT 'M' NOT NULL;
ALTER TABLE db_intranet_v4.tbl_navigation CHANGE `type` `type` set('M','F','P','L') DEFAULT 'M' NOT NULL AFTER parentId;

UPDATE tbl_navigation SET type = "P" WHERE parentId > 0;
UPDATE tbl_navigation SET type = "L" WHERE redirect = 1;
ALTER TABLE db_intranet_v4.tbl_navigation DROP COLUMN redirect;

UPDATE tbl_setting SET settingTabId=1, name='DB Versie', `type`='input', `options`=NULL, value=0x342E332E31, readonly=1, `order`=99, deleted=0 WHERE id='db.version';
UPDATE tbl_setting SET settingTabId=1, name='Versie', `type`='input', `options`=NULL, value=0x342E332E31, readonly=0, `order`=3, deleted=0 WHERE id='site.version';