DROP TABLE tbl_maintenance;
UPDATE tbl_module SET module='maintenance', name='Onderhoudswerken', icon='home-question', iconBackgroundColor='pink', `scope`='app', `order`=4, redirect=NULL, assignUserRights=0, defaultRights='0000', deleted=1 WHERE id=10;

INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, deleted) VALUES('synchronisation', 'Synchronisatie', 'refresh', 'cyan', 'app', 5, NULL, 0, '0000', 0);
