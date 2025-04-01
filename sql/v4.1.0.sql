--CREATE;
CREATE TABLE IF NOT EXISTS `tbl_management_cctv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `schoolId` int(11) NOT NULL,
  `buildingId` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `serialnumber` varchar(64) DEFAULT NULL,
  `macaddress` varchar(24) NOT NULL,
  `manufacturer` varchar(128) NOT NULL,
  `model` varchar(128) NOT NULL,
  `ip` varchar(24) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--RENAME;
RENAME TABLE tbl_order_purchase TO tbl_order;
RENAME TABLE tbl_order_purchase_line TO tbl_order_line;

--ALTER;
ALTER TABLE tbl_order_line CHANGE purchaseId orderId int(11) NOT NULL;
ALTER TABLE tbl_order ADD quoteLink TEXT NULL;
ALTER TABLE tbl_order CHANGE quoteLink quoteLink TEXT NULL AFTER supplierId;
ALTER TABLE tbl_order ADD quoteFile varchar(40) NULL;
ALTER TABLE tbl_order CHANGE quoteFile quoteFile varchar(40) NULL AFTER quoteLink;

--INSERT;
INSERT INTO tbl_navigation (routeGroupId, parentId, `order`, redirect, link, name, icon, color, minimumRights, settings, deleted) VALUES('1', 26, 13, 0, 'cctv', 'CCTV-Camera', 'device-cctv', 'blue', '0000001', NULL, 0);
INSERT INTO tbl_setting (id, settingTabId, name, `type`, `options`, value, readonly, `order`, deleted) VALUES('db.version', 1, 'DB Versie', 'input', NULL, 0x342E312E30, 1, 99, 0);

--UPDATE;
UPDATE tbl_navigation SET `order`=14 WHERE id=39;
UPDATE tbl_navigation SET link='order' WHERE id=45;

--DELETE;