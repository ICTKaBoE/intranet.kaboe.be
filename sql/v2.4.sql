INSERT INTO tbl_module (module, name, icon, iconBackgroundColor, `scope`, `order`, redirect, assignUserRights, defaultRights, deleted) VALUES('maintenance', 'Onderhoudswerken', 'home-question', 'pink', 'app', 4, NULL, 0, '0000', 0);
INSERT INTO tbl_module_navigation (moduleId, page, name, icon, minimumRights, `order`, deleted) VALUES(10, 'requests', 'Aanvragen', 'clipboard-list', 'edit', 2, 0);
INSERT INTO tbl_setting_override (settingId, moduleId, value, deleted) VALUES('page.default.tool', 10, 0x2F7265717565737473, 0);

CREATE TABLE `tbl_maintenance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolId` int(11) NOT NULL DEFAULT 0,
  `creationDate` datetime NOT NULL,
  `lastActionDateTime` datetime DEFAULT NULL,
  `finishedByDate` date DEFAULT NULL,
  `priority` set('low','medium','high') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'low',
  `status` set('todo','inprogress','waiting','completed') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'todo',
  `location` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `details` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `executeBy` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;