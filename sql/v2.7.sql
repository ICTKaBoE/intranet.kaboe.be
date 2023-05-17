CREATE TABLE `tbl_management_beamer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolId` int(11) NOT NULL DEFAULT 0,
  `buildingId` int(11) NOT NULL DEFAULT 0,
  `roomId` int(11) NOT NULL DEFAULT 0,
  `brand` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `serialnumber` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `tbl_management_printer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schoolId` int(11) NOT NULL DEFAULT 0,
  `buildingId` int(11) NOT NULL DEFAULT 0,
  `roomId` int(11) NOT NULL DEFAULT 0,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `brand` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `serialnumber` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `colormode` set('B','YMCB') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'B',
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;