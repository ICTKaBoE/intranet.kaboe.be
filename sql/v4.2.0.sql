--CREATE
CREATE TABLE `tbl_signage_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `schoolId` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_signage_media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `schoolId` int(11) NOT NULL,
  `type` set('I','V','L') NOT NULL,
  `alias` varchar(254) NOT NULL,
  `link` longtext DEFAULT NULL,
  `size` double DEFAULT NULL,
  `length` varchar(128) DEFAULT NULL,
  `duration` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_signage_playlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `schoolId` int(11) NOT NULL,
  `name` varchar(254) NOT NULL,
  `assignedTo` set('S','G') NOT NULL DEFAULT 'S',
  `assignedToId` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_signage_playlist_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `playlistId` int(11) NOT NULL,
  `mediaId` int(11) NOT NULL,
  `duration` int(11) NOT NULL,
  `order` int(11) NOT NULL DEFAULT 1,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tbl_signage_screen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `schoolId` int(11) NOT NULL,
  `code` varchar(16) NOT NULL,
  `name` varchar(254) NOT NULL,
  `groupId` int(11) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--RENAME
--ALTER
--INSERT
INSERT INTO tbl_navigation (routeGroupId, parentId, `order`, redirect, link, name, icon, color, minimumRights, settings, deleted) VALUES('1', 17, 3, 0, 'group', 'Groepen', 'device-tv-old', 'blue', '0000001', NULL, 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `order`, redirect, link, name, icon, color, minimumRights, settings, deleted) VALUES('1', 17, 4, 0, 'media', 'Media', 'player-eject', 'blue', '0000001', NULL, 0);
INSERT INTO tbl_route (routeGroupId, `method`, route, controller, callback, apiNoAuth, deleted) VALUES(2, 'ANY', '{view}/signage/{what?}/{id?}', '\Controllers\API\SignageController', 'any', 1, 0);

--UPDATE
UPDATE tbl_navigation SET `order`=1 WHERE id=2;
UPDATE tbl_navigation SET `order`=2 WHERE id=7;
UPDATE tbl_navigation SET `order`=3 WHERE id=11;
UPDATE tbl_navigation SET `order`=5 WHERE id=20;
UPDATE tbl_navigation SET `order`=6 WHERE id=26;
UPDATE tbl_navigation SET `order`=7 WHERE id=44;
UPDATE tbl_navigation SET `order`=9 WHERE id=48;
UPDATE tbl_navigation SET `order`=10 WHERE id=52;
UPDATE tbl_navigation SET `order`=4 WHERE id=68;
UPDATE tbl_navigation SET `order`=98 WHERE id=72;
UPDATE tbl_navigation SET `order`=99 WHERE id=77;
UPDATE tbl_navigation SET `order`=8, link='signage', name='Digital Signage', color='teal', minimumRights='0000001', deleted=0 WHERE id=17;

UPDATE tbl_navigation SET routeGroupId='1', parentId=17, `order`=1, redirect=0, link='playlist', name='Playlists', icon='playlist', color='blue', minimumRights='0000001', settings=NULL, deleted=0 WHERE id=18;
UPDATE tbl_navigation SET routeGroupId='1', parentId=17, `order`=2, redirect=0, link='screen', name='Schermen', icon='device-tv', color='blue', minimumRights='0000001', settings=NULL, deleted=0 WHERE id=19;

UPDATE tbl_setting SET value=0x342E322E30 WHERE id='db.version';
UPDATE tbl_setting SET value=0x342E322E30 WHERE id='site.version';

--DELETE