<?php
define("LOCATION_ROOT", dirname(dirname(__DIR__)));
define("LOCATION_FRONTEND", LOCATION_ROOT . "/frontend");
define("LOCATION_BACKEND", LOCATION_ROOT . "/backend");
define("LOCATION_SQL", LOCATION_ROOT . "/sql");
define("LOCATION_LOGS", LOCATION_ROOT . "/logs");
define("LOCATION_SHARED", LOCATION_FRONTEND . "/shared");
define("LOCATION_APP", LOCATION_FRONTEND . "/app");
define("LOCATION_PUBLIC", LOCATION_FRONTEND . "/public");
define("LOCATION_ICON", LOCATION_SHARED . "/ui/icons/");
define("LOCATION_IMAGE", LOCATION_SHARED . "/ui/img/");
define("LOCATION_DOWNLOAD", LOCATION_ROOT . "/downloads");

define("SECURITY_SESSION_ISSIGNEDIN", sha1("isSignedIn"));
define("SECURITY_SESSION_PAGEERROR", sha1("pageerror"));
define("SECURITY_DEFAULT_GROUP", 2);

define("SECURITY_SESSION_SIGNINMETHOD_LOCAL", "local");
define("SECURITY_SESSION_SIGNINMETHOD_O365", "o365");

if (str_starts_with($_SERVER["HTTP_HOST"], "dev")) {
	define("DB_SERVER", "localhost");
	define("DB_DATABASE", "db_intranet2");
	define("DB_USERNAME", "application");
	define("DB_PASSWORD", "PianomanPA");
	define("DB_CHARSET", "utf8mb4");
} else {
	define("DB_SERVER", "ID75803_intranet.db.webhosting.be");
	define("DB_DATABASE", "ID75803_intranet");
	define("DB_USERNAME", "ID75803_intranet");
	define("DB_PASSWORD", "PianomanPA125");
	define("DB_CHARSET", "utf8mb4");
}

define("ROUTER_DEFAULT_PREFIX", "/public");
define("ROUTER_DEFAULT_MIDDLEWARE", "\\Router\\Middleware\\DefaultMiddleware");
define("ROUTER_DEFAULT_CONTROLLER", "\\Controllers\\DefaultController");
define("ROUTER_DEFAULT_FUNCTION", "index");

define("SELECT_ALL_VALUES", "Alle");
define("INFORMAT_CURRENT_SCHOOLYEAR", date('n') <= 8 ? (date("Y") - 1) . "-" . date("y") : date("Y") . "-" . (date("y") + 1));

define("EMAIL_SUFFIX", "coltd.be");

define("SYNC_DEFAULT_COMPANY_NAME", "Basisscholen");
define("SYNC_DEFAULT_MEMBEROF_STUDENT", "secur.leerlingen.{{school:adSecGroupPostfix}}");
define("SYNC_DEFAULT_OU_STUDENT", "OU={{school:adUserDescription}},OU=Leerlingen,OU=BASISSCHOLEN,OU=UsersCOLTD,OU=COLTD,DC=coltd,DC=be");

define("CLEAN_DATES", [
	"bikeEvent" => [
		"date" => "8-31",
		"year" => 1
	],
	"helpdesk" => [
		"month" => 1
	]
]);

define("MANAGEMENT_DEFAULT_PASS", "@KaBoE123");
if (str_starts_with($_SERVER["HTTP_HOST"], "dev")) {
	define("MANAGEMENT_URL", "dev.helpdesk.kaboe.be");
	define("MANAGEMENT_USER_TOKEN", "WY5HzMHFOcAFY3aGmE9kAgZcqFRztFOUc9L8khlc");
	define("MANAGEMENT_APP_TOKEN", "npTH2S1Vzs8leop72qZyumbx4qNGTPsQuDSsBzse");
} else {
	define("MANAGEMENT_URL", "beheer.kaboe.be");
	define("MANAGEMENT_USER_TOKEN", "9WgXSR6zayV1dmxNE2dZqFWpfhISJ7J4RTc27FXc");
	define("MANAGEMENT_APP_TOKEN", "PDUDL265pDsqvR8y0N779JNMQgog8DVOVd8Mrcr6");
}
