CREATE TABLE tbl_ehbo (
	id INT auto_increment NOT NULL,
	guid varchar(36) NOT NULL,
	creatorUserId INT NOT NULL,
	creationDateTime DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
	schoolId INT NOT NULL,
	place varchar(254) NOT NULL,
	description varchar(8) NOT NULL,
	descriptionOther varchar(254) NULL,
	firstHelpDateTime DATETIME NOT NULL,
	firstHelp varchar(8) NOT NULL,
	firstHelpOther varchar(254) NULL,
	victimType set('S', 'E') DEFAULT 'S' NOT NULL,
	victimId INT NOT NULL,
	witness varchar(254) NOT NULL,
	deleted TINYINT(1) DEFAULT 0 NOT NULL,
	CONSTRAINT tbl_ehbo_pk PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;

CREATE TABLE tbl_ehbo_description (
	id varchar(8) NOT NULL,
	name varchar(254) NOT NULL,
	CONSTRAINT tbl_ehbo_description_pk PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;

CREATE TABLE tbl_ehbo_firsthelp (
	id varchar(8) NOT NULL,
	name varchar(254) NOT NULL,
	CONSTRAINT tbl_ehbo_firsthelp_pk PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;

CREATE TABLE tbl_ehbo_victimtype (
	id varchar(8) NOT NULL,
	name varchar(254) NOT NULL,
	CONSTRAINT tbl_ebho_victimtype_pk PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;

CREATE TABLE tbl_violence (
	id INT auto_increment NOT NULL,
	guid varchar(36) NOT NULL,
	creationDateTime DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
	creatorUserId INT NOT NULL,
	schoolId INT NOT NULL,
	anonymous BOOL DEFAULT 1 NOT NULL,
	victimId INT NULL,
	factsDateTime DATETIME NOT NULL,
	identityParty varchar(254) NULL,
	ageParty INT NULL,
	workingHours BOOL DEFAULT 1 NOT NULL,
	form varchar(8) NOT NULL,
	formOther varchar(254) NULL,
	`out` varchar(8) NOT NULL,
	outOther varchar(254) NULL,
	intention varchar(8) NOT NULL,
	intentionOther varchar(254) NULL,
	consequence varchar(8) NULL,
	cause varchar(8) NULL,
	causeOther varchar(254) NULL,
	damage varchar(8) NULL,
	damageKind varchar(8) NULL,
	police BOOL DEFAULT 0 NOT NULL,
	actionsTaken TEXT NULL,
	proposalEmployer varchar(254) NULL,
	proposalConfidant varchar(254) NULL,
	proposalPapsy varchar(254) NULL,
	proposalHead varchar(254) NULL,
	deleted BOOL DEFAULT 0 NOT NULL,
	CONSTRAINT tbl_violence_pk PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;

CREATE TABLE tbl_violence_cause (
	id varchar(8) NOT NULL,
	name varchar(254) NOT NULL,
	CONSTRAINT tbl_violence_cause_pk PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;

CREATE TABLE tbl_violence_consequence (
	id varchar(8) NOT NULL,
	name varchar(254) NOT NULL,
	CONSTRAINT tbl_violence_consequence_pk PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;

CREATE TABLE tbl_violence_damage (
	id varchar(8) NOT NULL,
	name varchar(254) NOT NULL,
	CONSTRAINT tbl_violence_damage_pk PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;

CREATE TABLE tbl_violence_damage_kind (
	id varchar(8) NOT NULL,
	name varchar(254) NOT NULL,
	CONSTRAINT tbl_violence_damage_kind_pk PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;

CREATE TABLE tbl_violence_form (
	id varchar(8) NOT NULL,
	name varchar(254) NOT NULL,
	CONSTRAINT tbl_violence_form_pk PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;

CREATE TABLE tbl_violence_intention (
	id varchar(8) NOT NULL,
	name varchar(254) NOT NULL,
	CONSTRAINT tbl_violence_intention_pk PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;

CREATE TABLE tbl_violence_out (
	id varchar(8) NOT NULL,
	name varchar(254) NOT NULL,
	CONSTRAINT tbl_violence_out_pk PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;

CREATE TABLE tbl_iwe (
	id INT auto_increment NOT NULL,
	guid varchar(36) NOT NULL,
	creationDateTime DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
	creatorUserId INT NOT NULL,
	schoolId INT NOT NULL,
	buildingId INT NOT NULL,
	roomId INT NOT NULL,
	name varchar(254) NOT NULL,
	brand varchar(254) NOT NULL,
	model varchar(254) NOT NULL,
	amount INT NOT NULL,
	ownedBySchool BOOL DEFAULT 0 NOT NULL,
	schoolTakesOwnership BOOL DEFAULT 0 NOT NULL,
	deleted BOOL DEFAULT 0 NOT NULL,
	CONSTRAINT tbl_iwe_pk PRIMARY KEY (id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;

INSERT INTO tbl_ehbo_description (id, name) VALUES('P', 'Pijn: buik, hoofd, tand, ...');
INSERT INTO tbl_ehbo_description (id, name) VALUES('W', 'Wonde: schaafwonde, brandwonde, snijwonde, ...');
INSERT INTO tbl_ehbo_description (id, name) VALUES('L', 'Verlies bewustzijn');
INSERT INTO tbl_ehbo_description (id, name) VALUES('S', 'Verstuiking, kneuzing');
INSERT INTO tbl_ehbo_description (id, name) VALUES('H', 'Hyperventilatie');
INSERT INTO tbl_ehbo_description (id, name) VALUES('V', 'Braken');
INSERT INTO tbl_ehbo_description (id, name) VALUES('O', 'Andere');

INSERT INTO tbl_ehbo_firsthelp (id, name) VALUES('H', 'Hypo Fit zakje (i.p.v. Cola)');
INSERT INTO tbl_ehbo_firsthelp (id, name) VALUES('R', 'Rust op bedje secretariaat');
INSERT INTO tbl_ehbo_firsthelp (id, name) VALUES('C', 'Coldpack');
INSERT INTO tbl_ehbo_firsthelp (id, name) VALUES('W', 'Wondzorg');
INSERT INTO tbl_ehbo_firsthelp (id, name) VALUES('A', 'Alarmeren (112 via receptie)');
INSERT INTO tbl_ehbo_firsthelp (id, name) VALUES('S', 'Splinter verwijderen');
INSERT INTO tbl_ehbo_firsthelp (id, name) VALUES('O', 'Andere');

INSERT INTO tbl_ehbo_victimtype (id, name) VALUES('E', 'Personeelslid');
INSERT INTO tbl_ehbo_victimtype (id, name) VALUES('S', 'Leerling');

INSERT INTO tbl_violence_form (id, name) VALUES('V', 'Agressie/Geweld');
INSERT INTO tbl_violence_form (id, name) VALUES('P', 'Pesten');
INSERT INTO tbl_violence_form (id, name) VALUES('I', 'Ongewenst seksueel/relationeel gedrag');
INSERT INTO tbl_violence_form (id, name) VALUES('S', 'Stalking');
INSERT INTO tbl_violence_form (id, name) VALUES('R', 'Racisme');
INSERT INTO tbl_violence_form (id, name) VALUES('D', 'Discriminatie');
INSERT INTO tbl_violence_form (id, name) VALUES('M', 'Materiêle agressie');
INSERT INTO tbl_violence_form (id, name) VALUES('O', 'Andere');

INSERT INTO tbl_violence_out (id, name) VALUES('V', 'Verbaal (schelden, dreigen, druk uitoefenen)');
INSERT INTO tbl_violence_out (id, name) VALUES('B', 'Lichaamstaal');
INSERT INTO tbl_violence_out (id, name) VALUES('F', 'Fysiek (aanraken, slagen, verwonding)');
INSERT INTO tbl_violence_out (id, name) VALUES('N', 'Schriftelijk (sms, mail, bericht)');
INSERT INTO tbl_violence_out (id, name) VALUES('O', 'Andere');

INSERT INTO tbl_violence_intention (id, name) VALUES('I', 'Intentioneel (moedwillig)');
INSERT INTO tbl_violence_intention (id, name) VALUES('S', 'Volgens een ziektebeeld (verslaving, psychiatrie, dementie, ...)');
INSERT INTO tbl_violence_intention (id, name) VALUES('EU', 'Vanuit een hevige emotie na een begrijpbare frustratie');
INSERT INTO tbl_violence_intention (id, name) VALUES('ENU', 'Vanuit een hevige emotie die niet goed te begrijpen valt');
INSERT INTO tbl_violence_intention (id, name) VALUES('O', 'Andere');

INSERT INTO tbl_violence_consequence (id, name) VALUES('N', 'Geen');
INSERT INTO tbl_violence_consequence (id, name) VALUES('P', 'Pijn');
INSERT INTO tbl_violence_consequence (id, name) VALUES('W', 'Verwonding (blauwe plek, kneuzing, wonde, ...)');
INSERT INTO tbl_violence_consequence (id, name) VALUES('E', 'Emotioneel leed');
INSERT INTO tbl_violence_consequence (id, name) VALUES('M', 'Materiële schade');

INSERT INTO tbl_violence_cause (id, name) VALUES('L', 'Te lang moeten wachten');
INSERT INTO tbl_violence_cause (id, name) VALUES('S', 'Ontevredenheid over de dienstverlening');
INSERT INTO tbl_violence_cause (id, name) VALUES('H', 'Emotioneel ten gevolge van een gebeurtenis');
INSERT INTO tbl_violence_cause (id, name) VALUES('A', 'Onder invloed van alcohol of drugs');
INSERT INTO tbl_violence_cause (id, name) VALUES('N', 'Geen duidelijke aanleiding');
INSERT INTO tbl_violence_cause (id, name) VALUES('O', 'Andere');

INSERT INTO tbl_violence_damage_kind (id, name) VALUES('B', 'Lichamelijk');
INSERT INTO tbl_violence_damage_kind (id, name) VALUES('P', 'Psychisch');
INSERT INTO tbl_violence_damage_kind (id, name) VALUES('M', 'Materieel');

INSERT INTO tbl_violence_damage (id, name) VALUES('M', 'Tegen mij alleen');
INSERT INTO tbl_violence_damage (id, name) VALUES('C', 'Tegen mij en collega''s');
INSERT INTO tbl_violence_damage (id, name) VALUES('P', 'Tegen andere derden');

ALTER TABLE tbl_accident DROP COLUMN `number`;
ALTER TABLE tbl_helpdesk DROP COLUMN `number`;
ALTER TABLE tbl_order DROP COLUMN `number`;

DELETE FROM tbl_navigation_setting WHERE `key`="lastNumber";

UPDATE tbl_navigation SET parentId = folderId WHERE folderId <> 0;
UPDATE tbl_navigation SET link = NULL WHERE `type` = "F"
ALTER TABLE tbl_navigation DROP COLUMN folderId;

ALTER TABLE tbl_navigation ADD CONSTRAINT tbl_navigation_unique UNIQUE KEY (parentId,link);
ALTER TABLE tbl_navigation MODIFY COLUMN link varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL NULL;

INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, `order`, link, name, icon, color, deleted) VALUES('1', 84, 'M', 2, 'ehbo', 'EHBO Register', 'first-aid-kit', 'red', 0);
SET @ehboId = (SELECT id FROM tbl_navigation WHERE link="ehbo");
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, `order`, link, name, icon, color, deleted) VALUES('1', @ehboId, 'P', 1, 'mine', 'Mijn Registraties', 'user-scan', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, `order`, link, name, icon, color, deleted) VALUES('1', @ehboId, 'P', 2, 'all', 'Register', 'first-aid-kit', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, `order`, link, name, icon, color, deleted) VALUES('1', @ehboId, 'P', 3, 'export', 'Exporteren', 'file-export', 'blue', 0);
INSERT INTO tbl_navigation_setting (navigationId, `key`, value) VALUES(@ehboId, '_', 0x6D696E65);

INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @ehboId and link="mine"), 1, NULL, NULL, 'School', 'linked.school.formatted.badge.name', 0, 0, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @ehboId and link="mine"), 2, NULL, NULL, 'Aangegeven op', 'formatted.creationDateTime', 1, 1, 175, 1, 1, 1, 'desc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @ehboId and link="mine"), 3, NULL, NULL, 'Plaats', 'place', 1, 1, 200, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @ehboId and link="mine"), 4, NULL, NULL, 'Beschrijving', 'formatted.description', 1, 1, 200, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @ehboId and link="mine"), 5, NULL, NULL, 'Eerste hulp', 'formatted.firstHelpWithDateTime', 1, 1, 300, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @ehboId and link="mine"), 6, NULL, NULL, 'Slachtoffer', 'linked.victim.formatted.fullNameReversed', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @ehboId and link="mine"), 7, NULL, NULL, 'Getuige', 'witness', 1, 1, 200, 0, 0, NULL, NULL, 0);

INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @ehboId and link="all"), 1, NULL, NULL, 'School', 'linked.school.formatted.badge.name', 0, 0, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @ehboId and link="all"), 2, NULL, NULL, 'Aangegeven op', 'formatted.creationDateTime', 1, 1, 175, 1, 1, 1, 'desc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @ehboId and link="all"), 4, NULL, NULL, 'Plaats', 'place', 1, 1, 200, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @ehboId and link="all"), 5, NULL, NULL, 'Beschrijving', 'formatted.description', 1, 1, 200, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @ehboId and link="all"), 6, NULL, NULL, 'Eerste hulp', 'formatted.firstHelpWithDateTime', 1, 1, 300, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @ehboId and link="all"), 7, NULL, NULL, 'Slachtoffer', 'linked.victim.formatted.fullNameReversed', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @ehboId and link="all"), 8, NULL, NULL, 'Getuige', 'witness', 1, 1, 200, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @ehboId and link="all"), 3, NULL, NULL, 'Door', 'linked.creatorUser.formatted.fullNameReversed', 1, 1, 200, 0, 0, NULL, NULL, 0);

INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, `order`, link, name, icon, color, deleted) VALUES('1', 84, 'M', 3, 'violence', 'Melding van geweld, pesterijen of ongewenst gedrag op het werk', 'karate', 'red', 0);
SET @violenceId = (SELECT id FROM tbl_navigation WHERE link="violence");
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, `order`, link, name, icon, color, deleted) VALUES('1', @violenceId, 'P', 1, 'mine', 'Mijn Registraties', 'user-scan', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, `order`, link, name, icon, color, deleted) VALUES('1', @violenceId, 'P', 2, 'all', 'Register', 'karate', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, `order`, link, name, icon, color, deleted) VALUES('1', @violenceId, 'P', 3, 'export', 'Exporteren', 'file-export', 'blue', 0);
INSERT INTO tbl_navigation_setting (navigationId, `key`, value) VALUES(@violenceId, '_', 0x6D696E65);

INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @violenceId and link="mine"), 1, NULL, NULL, 'School', 'linked.school.formatted.badge.name', 0, 0, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @violenceId and link="mine"), 2, NULL, NULL, 'Aangegeven op', 'formatted.creationDateTime', 1, 1, 175, 1, 1, 1, 'desc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @violenceId and link="mine"), 3, NULL, NULL, 'Slachtoffer', 'formatted.victim', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @violenceId and link="mine"), 4, NULL, NULL, 'Datum/Uur feiten', 'formatted.factsDateTime', 1, 1, 175, 1, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @violenceId and link="mine"), 5, NULL, NULL, 'Derde', 'formatted.party', 1, 1, 300, 0, 0, NULL, NULL, 0);

INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @violenceId and link="all"), 1, NULL, NULL, 'School', 'linked.school.formatted.badge.name', 0, 0, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @violenceId and link="all"), 2, NULL, NULL, 'Aangegeven op', 'formatted.creationDateTime', 1, 1, 175, 1, 1, 1, 'desc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @violenceId and link="all"), 3, NULL, NULL, 'Slachtoffer', 'formatted.victim', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @violenceId and link="all"), 4, NULL, NULL, 'Datum/Uur feiten', 'formatted.factsDateTime', 1, 1, 175, 1, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @violenceId and link="all"), 5, NULL, NULL, 'Derde', 'formatted.party', 1, 1, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @violenceId and link="all"), 6, NULL, NULL, 'Werkuren', 'formatted.workingHours', 1, 1, 50, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @violenceId and link="all"), 7, NULL, NULL, 'Vorm', 'formatted.form', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @violenceId and link="all"), 8, NULL, NULL, 'Uiting', 'formatted.out', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @violenceId and link="all"), 9, NULL, NULL, 'Intentie van de derde', 'formatted.intention', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @violenceId and link="all"), 10, NULL, NULL, 'Gevolgen', 'linked.consequence.name', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @violenceId and link="all"), 11, NULL, NULL, 'Aanleiding', 'formatted.cause', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @violenceId and link="all"), 12, NULL, NULL, 'Schade of gevolg', 'formatted.damage', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @violenceId and link="all"), 13, NULL, NULL, 'Soort schade of gevolg', 'formatted.damageKind', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @violenceId and link="all"), 14, NULL, NULL, 'Aangifte politie', 'formatted.police', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @violenceId and link="all"), 15, NULL, NULL, 'Reeds genomen acties', 'actionsTaken', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @violenceId and link="all"), 16, NULL, NULL, 'Voorstellen aan werkgever', 'proposalEmployer', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @violenceId and link="all"), 17, NULL, NULL, 'Voorstellen aan vertrouwenspersoon', 'proposalConfidant', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @violenceId and link="all"), 19, NULL, NULL, 'Voorstellen voor papsy', 'proposalPapsy', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @violenceId and link="all"), 20, NULL, NULL, 'Voorstellen voor leidinggevend', 'proposalHead', 1, 1, 0, 0, 0, NULL, NULL, 0);

INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, `order`, link, name, icon, color, deleted) VALUES('1', 84, 'M', 3, 'iwe', 'Inventarisatie Arbeidsmiddelen', 'forklift', 'red', 0);
SET @iweId = (SELECT id FROM tbl_navigation WHERE link="iwe");
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, `order`, link, name, icon, color, deleted) VALUES('1', @iweId, 'P', 1, 'mine', 'Mijn Registraties', 'user-scan', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, `order`, link, name, icon, color, deleted) VALUES('1', @iweId, 'P', 2, 'all', 'Register', 'forklift', 'blue', 0);
INSERT INTO tbl_navigation (routeGroupId, parentId, `type`, `order`, link, name, icon, color, deleted) VALUES('1', @iweId, 'P', 3, 'export', 'Exporteren', 'file-export', 'blue', 0);
INSERT INTO tbl_navigation_setting (navigationId, `key`, value) VALUES(@iweId, '_', 0x6D696E65);

INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @iweId and link="mine"), 1, NULL, NULL, 'School', 'linked.school.formatted.badge.name', 0, 0, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @iweId and link="mine"), 2, NULL, NULL, 'Aangegeven op', 'formatted.creationDateTime', 1, 1, 175, 1, 1, 1, 'desc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @iweId and link="mine"), 3, NULL, NULL, 'Plaats', 'formatted.location', 1, 1, 200, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @iweId and link="mine"), 4, NULL, NULL, 'Aantal', 'amount', 1, 1, 50, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @iweId and link="mine"), 5, NULL, NULL, 'Naam', 'name', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @iweId and link="mine"), 6, NULL, NULL, 'Merk/Model', 'formatted.brandModel', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @iweId and link="mine"), 7, NULL, NULL, 'School is eigenaar', 'formatted.icon.ownedBySchool', 1, 1, 20, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @iweId and link="mine"), 8, NULL, NULL, 'Overname door school', 'formatted.icon.schoolTakesOwnership', 1, 1, 20, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @iweId and link="mine"), 9, NULL, NULL, 'Handleiding', 'formatted.manual', 1, 1, 20, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @iweId and link="mine"), 10, NULL, NULL, 'CE-kenteken', 'formatted.ce', 1, 1, 20, 0, 0, NULL, NULL, 0);

INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @iweId and link="all"), 1, NULL, NULL, 'School', 'linked.school.formatted.badge.name', 0, 0, 100, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @iweId and link="all"), 2, NULL, NULL, 'Aangegeven op', 'formatted.creationDateTime', 1, 1, 175, 1, 1, 1, 'desc', 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @iweId and link="all"), 3, NULL, NULL, 'Door', 'linked.creatorUser.formatted.fullNameReversed', 1, 1, 200, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @iweId and link="all"), 4, NULL, NULL, 'Plaats', 'formatted.location', 1, 1, 200, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @iweId and link="all"), 5, NULL, NULL, 'Aantal', 'amount', 1, 1, 50, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @iweId and link="all"), 6, NULL, NULL, 'Naam', 'name', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @iweId and link="all"), 7, NULL, NULL, 'Merk/Model', 'formatted.brandModel', 1, 1, 0, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @iweId and link="all"), 8, NULL, NULL, 'School is eigenaar', 'formatted.icon.ownedBySchool', 1, 1, 20, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @iweId and link="all"), 9, NULL, NULL, 'Overname door school', 'formatted.icon.schoolTakesOwnership', 1, 1, 20, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @iweId and link="all"), 10, NULL, NULL, 'Handleiding', 'formatted.manual', 1, 1, 20, 0, 0, NULL, NULL, 0);
INSERT INTO tbl_navigation_tabledef (navigationId, `order`, priority, `type`, title, `data`, orderable, searchable, width, render, defaultOrder, defaultOrderOrder, defaultOrderDirection, deleted) VALUES((select id from tbl_navigation where parentId = @iweId and link="all"), 11, NULL, NULL, 'CE-kenteken', 'formatted.ce', 1, 1, 20, 0, 0, NULL, NULL, 0);

UPDATE tbl_navigation SET link='all' WHERE `type`="P" and link="order";
UPDATE tbl_navigation_setting SET value="all" WHERE navigationId=(SELECT id FROM tbl_navigation WHERE link="order");

INSERT INTO tbl_route (routeGroupId, `method`, route, controller, callback, apiNoAuth, deleted) VALUES(2, 'ANY', '{view}/ehbo/{what?}/{id?}', '\\Controllers\\API\\EHBOController', 'any', 0, 0);
INSERT INTO tbl_route (routeGroupId, `method`, route, controller, callback, apiNoAuth, deleted) VALUES(2, 'ANY', '{view}/violence/{what?}/{id?}', '\\Controllers\\API\\ViolenceController', 'any', 0, 0);
INSERT INTO tbl_route (routeGroupId, `method`, route, controller, callback, apiNoAuth, deleted) VALUES(2, 'ANY', '{view}/iwe/{what?}/{id?}', '\\Controllers\\API\\IWEController', 'any', 0, 0);

UPDATE tbl_setting SET settingTabId=1, name='DB Versie', `type`='input', `options`=NULL, value=0x342E372E30, readonly=1, `order`=99, deleted=0 WHERE id='db.version';
UPDATE tbl_setting SET settingTabId=1, name='Versie', `type`='input', `options`=NULL, value=0x342E372E30, readonly=0, `order`=3, deleted=0 WHERE id='site.version';