--CONFIGURATION - DEPARTMENT
SET @configurationId = (SELECT id FROM tbl_navigation WHERE link="configuration");
ALTER TABLE tbl_school_department ADD managementRoomId BLOB NULL;
ALTER TABLE tbl_school_department CHANGE managementRoomId managementRoomId BLOB NULL AFTER name;

INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @configurationId and link="department"), 4, NULL, NULL, 'Lokalen', 'formatted.managementRooms', 0, 0, 300, 0, 0, NULL, NULL, 0);

--MANAGEMENT - ROOM
SET @managementId = (SELECT id FROM tbl_navigation WHERE link="management");
ALTER TABLE tbl_management_room ADD alias varchar(128) NULL;
ALTER TABLE tbl_management_room CHANGE alias alias varchar(128) NULL AFTER `number`;

INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @managementId and link="room"), 5, NULL, NULL, 'Alias', 'alias', 1, 1, 100, 0, 0, NULL, NULL, 0);
UPDATE tbl_navigation_tabledef SET `order`=6 WHERE id=(select id from tbl_navigation where parentId = @managementId and link="room") AND `data`='formatted.full';

--EXAMSCHEDULE
INSERT INTO tbl_route (routeGroupId, `method`, route, controller, callback, apiNoAuth, `order`, deleted) VALUES(2, 'ANY', '{view}/examschedule/{what?}/{id?}', '\\Controllers\\API\\ExamScheduleController', 'any', 0, 1, 0);

INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', 0, 'M', 0, 82, 'examschedule', 'Examenrooster', 'calendar-week', 'teal', 0);
SET @examscheduleId = (SELECT id FROM tbl_navigation WHERE link="examschedule");
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @examscheduleId, 'P', 0, 1, 'build', 'Rooster bouwen', 'calendar-week', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @examscheduleId, 'P', 1, 1, 'assign', 'Uren toewijzen', 'clock', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @examscheduleId, 'P', 1, 2, 'period', 'Periode', 'calendar-time', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, management, `order`, link, name, icon, color, deleted) VALUES('1', @examscheduleId, 'P', 1, 100, 'settings', 'Instellingen', 'settings', 'blue', 0);
INSERT INTO tbl_navigation_setting (navigationId, `key`, value) VALUES(@examscheduleId, '_', 0x6275696C64);

INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @examscheduleId and link="assign"), 1, NULL, NULL, 'Leerkracht', 'formatted.fullNameReversed', 1, 1, 0, 0, 1, 1, 'asc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @examscheduleId and link="assign"), 2, NULL, NULL, 'Gepresteerde uren', 'hours.workedHours', 0, 0, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @examscheduleId and link="assign"), 3, NULL, NULL, 'Uren examen', 'hours.examHours', 0, 0, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @examscheduleId and link="assign"), 4, NULL, NULL, 'Geen examen', 'hours.noExamHours', 0, 0, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @examscheduleId and link="assign"), 5, NULL, NULL, 'Uren toezicht', 'hours.formatted.supervisionHoursToDo', 0, 0, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @examscheduleId and link="assign"), 6, NULL, NULL, 'Uren toezicht effectief', 'hours.supervisionHours', 0, 0, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @examscheduleId and link="assign"), 7, NULL, NULL, 'Balans', 'hours.formatted.balans', 0, 0, 100, 0, 0, NULL, NULL, 0);

--LAST STEP
UPDATE tbl_setting SET settingTabId=1, name='DB Versie', `type`='input', `options`=NULL, value=0x352E332E30, readonly=1, `order`=99, deleted=0 WHERE id='db.version';
UPDATE tbl_setting SET settingTabId=1, name='Versie', `type`='input', `options`=NULL, value=0x352E332E30, readonly=0, `order`=3, deleted=0 WHERE id='site.version';