UPDATE tbl_module_navigation SET moduleId=2, page='home-work', name='Woon - Werk', icon='home', minimumRights='edit', `order`=1, deleted=0 WHERE id=1;

UPDATE tbl_setting SET moduleId=1, settingTabId=1, name='Versie', `type`='input', `options`=NULL, value=0x332E302E30, `order`=2, deleted=0 WHERE id='site.version';