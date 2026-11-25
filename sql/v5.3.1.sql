ALTER TABLE tbl_remedy_type ADD sendMessage BOOL DEFAULT 1 NOT NULL;
ALTER TABLE tbl_remedy_type CHANGE sendMessage sendMessage BOOL DEFAULT 1 NOT NULL AFTER allowWithoutDate;

UPDATE tbl_setting SET value=0x352E332E31 WHERE id='db.version';
UPDATE tbl_setting SET value=0x352E332E31 WHERE id='site.version';