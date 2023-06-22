CREATE TABLE `tbl_sync_student` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `informatUID` int(11) NOT NULL,
  `instituteId` int(11) NOT NULL,
  `uid` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `firstName` varchar(254) COLLATE utf8_unicode_ci NOT NULL,
  `displayName` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` text COLLATE utf8_unicode_ci NOT NULL,
  `class` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(254) COLLATE utf8_unicode_ci DEFAULT NULL,
  `companyName` varchar(254) COLLATE utf8_unicode_ci DEFAULT NULL,
  `memberOf` blob DEFAULT "{{ad:domain}}/Users",
  `samAccountName` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `ou` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `action` text COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `lastAdSyncSuccessAction` text COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `lastAdSyncTime` datetime DEFAULT NULL,
  `lastAdSyncError` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE tbl_school adSecGroupFolderName varchar(16) NOT NULL AFTER deviceNamePrefix;
ALTER TABLE tbl_school adSecGroupPostfix varchar(16) NOT NULL AFTER adSecGroupFolderName;
ALTER TABLE tbl_school adUserDescription varchar(16) NOT NULL AFTER adSecGroupPostfix;
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(12, 'suppliers', 'Leveranciers', 'truck', 'edit', 99, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(12, 'mine', 'Door mij goed te keuren', 'user', 'edit', 3, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(11, 'overview', 'Overzicht', 'list', 'view', 1, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(11, 'teacher', 'Leerkrachten', 'school', 'view', 2, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(11, 'student', 'Leerlingen', 'user', 'view', 3, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(4, 'start', 'Start', 'layout-dashboard', 'view', 3, 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(11, 'syncToAdFrom', '1', 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(11, 'sendSyncMailTime', '06:00', 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(11, 'informatLastSyncTime', NULL, 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(11, 'teacherLastSyncTime', NULL, 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(11, 'studentLastSyncTime', NULL, 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(11, 'dictionary', NULL, 0);
INSERT INTO tbl_setting_override (settingId, moduleId, value, deleted) VALUES('page.default.tool', 11, 0x2F6F766572766965770D0A, 0);
UPDATE tbl_module SET module='synchronisation', name='Synchronisatie', icon='refresh', iconBackgroundColor='cyan', `scope`='app', `order`=7, redirect=NULL, assignUserRights=0, defaultRights='0000', visible=1, deleted=0 WHERE id=11;
UPDATE tbl_school SET name='De Meidoorn', color='#F9C606', logo='De Meidoorn.png', deviceNamePrefix='MEI', adSecGroupFolderName='meidoorn', adSecGroupPostfix='meidoorn', adUserDescription='Meidoorn', deleted=0 WHERE id=1;
UPDATE tbl_school SET name='De Wegel', color='#F57F29', logo='De Wegel.png', deviceNamePrefix='WEG', adSecGroupFolderName='wegel', adSecGroupPostfix='wegel', adUserDescription='Wegel', deleted=0 WHERE id=2;
UPDATE tbl_school SET name='Sint-Antonius', color='#ED1944', logo='Sint-Antonius.png', deviceNamePrefix='BSA', adSecGroupFolderName='St-Antonius', adSecGroupPostfix='stantonius', adUserDescription='Antonius', deleted=0 WHERE id=3;
UPDATE tbl_school SET name='Sint-Jozef', color='#77AE1B', logo='Sint-Jozef.png', deviceNamePrefix='STJ', adSecGroupFolderName='St-JOZEF', adSecGroupPostfix='stjozef', adUserDescription='Jozef', deleted=0 WHERE id=4;
UPDATE tbl_school SET name='KaBoE', color='#FFFFFF', logo=NULL, deviceNamePrefix='KABOE', adSecGroupFolderName='', adSecGroupPostfix='', adUserDescription=NULL, deleted=0 WHERE id=5;
UPDATE tbl_setting SET moduleId=1, settingTabId=1, name='Versie', `type`='input', `options`=NULL, value=0x322E38, `order`=2, deleted=0 WHERE id='site.version';
