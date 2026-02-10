-- Strategic Dashboard
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', 0, 'M', 0, 90, 'strategicDashboard', 'Strategisch Dashboard', 'dashboard', 'blue', 0);
SET @nId = (SELECT id FROM tbl_navigation WHERE link="strategicDashboard" AND `type` = "M");

INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @nId, 'P', 0, 2, 'dashboard', 'Dashboard', 'dashboard', 'blue', 0);

-- --NAV SETTING
INSERT INTO tbl_navigation_setting (navigationId, `key`, value) VALUES(@nId, '_', 0x64617368626F617264);

-- --ROUTE
INSERT INTO tbl_route (routeGroupId, `method`, route, controller, callback, apiNoAuth, deleted) VALUES(2, 'ANY', '{view}/strategicDashboard/{what?}/{id?}', '\\Controllers\\API\\StrategicDashboardController', 'any', 0, 0);

UPDATE tbl_setting SET settingTabId=1, name='DB Versie', `type`='input', `options`=NULL, value=0x352E312E30, readonly=1, `order`=99, deleted=0 WHERE id='db.version';
UPDATE tbl_setting SET settingTabId=1, name='Versie', `type`='input', `options`=NULL, value=0x352E312E30, readonly=0, `order`=3, deleted=0 WHERE id='site.version';