CREATE TABLE `tbl_supervision_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `userMainSchoolId` int(11) NOT NULL,
  `start` datetime NOT NULL DEFAULT curdate(),
  `end` datetime NOT NULL DEFAULT curdate(),
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, visible, deleted) VALUES('supervision', 'Middagtoezichten', 'device-cctv', 'azure', 'app', 2, NULL, 1, '1100', 1, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(14, 'fill', 'Invullen', 'pencil', 'view', 1, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(14, 'export', 'Exporteren', 'file-export', 'export', 2, 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(14, 'settings', 'Instellingen', 'settings', 'changeSettings', 3, 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(14, 'blockPast', '1', 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(14, 'blockFuture', '1', 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(14, 'lastPayDate', '2023-05-31', 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(14, 'blockPastType', 'm', 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(14, 'blockFutureType', 'd', 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(14, 'blockPastAmount', '2', 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(14, 'blockFutureAmount', '', 0);
INSERT INTO tbl_module_setting (moduleId, `key`, value, deleted) VALUES(14, 'blockPastOnLastPayDate', '1', 0);
INSERT INTO tbl_setting_override (settingId, moduleId, value, deleted) VALUES('page.default.tool', 14, 0x2F66696C6C, 0);

UPDATE tbl_module SET module='helpdesk', name='Helpdesk', icon='question-mark', iconBackgroundColor='orange', `scope`='app', `order`=3, redirect=NULL, assignUserRights=1, defaultRights='1000', visible=1, deleted=0 WHERE id=3;
UPDATE tbl_module SET module='notescreen', name='Meldingenscherm', icon='message-dots', iconBackgroundColor='yellow', `scope`='app/public', `order`=5, redirect=NULL, assignUserRights=0, defaultRights='1000', visible=1, deleted=0 WHERE id=6;
UPDATE tbl_module SET module='checklists', name='Controlelijsten', icon='check', iconBackgroundColor='green', `scope`='app', `order`=4, redirect=NULL, assignUserRights=1, defaultRights='0000', visible=1, deleted=0 WHERE id=7;
UPDATE tbl_module SET module='maintenance', name='Onderhoudswerken', icon='home-question', iconBackgroundColor='pink', `scope`='app', `order`=4, redirect=NULL, assignUserRights=0, defaultRights='0000', visible=1, deleted=1 WHERE id=10;
UPDATE tbl_module SET module='synchronisation', name='Synchronisatie', icon='refresh', iconBackgroundColor='cyan', `scope`='app', `order`=8, redirect=NULL, assignUserRights=0, defaultRights='0000', visible=1, deleted=0 WHERE id=11;
UPDATE tbl_module SET module='orders', name='Bestellingen', icon='shopping-cart', iconBackgroundColor='teal', `scope`='app', `order`=7, redirect=NULL, assignUserRights=0, defaultRights='0000', visible=1, deleted=0 WHERE id=12;
UPDATE tbl_module SET module='management', name='Beheer', icon='businessplan', iconBackgroundColor='lime', `scope`='app', `order`=6, redirect=NULL, assignUserRights=0, defaultRights='0000', visible=1, deleted=0 WHERE id=13;
UPDATE tbl_setting SET moduleId=1, settingTabId=1, name='Versie', `type`='input', `options`=NULL, value=0x322E39, `order`=2, deleted=0 WHERE id='site.version';