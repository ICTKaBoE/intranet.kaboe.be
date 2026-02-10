<?php

define("VERSION_DB", "5.1.0");
define("URL_MAIN", "kaboe.be");
define("DEV_MODE", (PHP_SAPI !== "cli" ? str_starts_with($_SERVER["HTTP_HOST"], "dev") : array_search("mode=dev", $argv)));
define("DEV_CONTACT", "jano.lampaert@coltd.be");

define("LOCATION_ROOT", dirname(dirname(__DIR__)));
define("LOCATION_FRONTEND", LOCATION_ROOT . "/frontend");
define("LOCATION_FRONTEND_PAGES", LOCATION_FRONTEND . "/pages");
define("LOCATION_BACKEND", LOCATION_ROOT . "/backend");
define("LOCATION_SQL", LOCATION_ROOT . "/sql");
define("LOCATION_LOGS", LOCATION_ROOT . "/logs");
define("LOCATION_SHARED", LOCATION_FRONTEND . "/shared");
define("LOCATION_APP", LOCATION_FRONTEND . "/app");
define("LOCATION_PUBLIC", LOCATION_FRONTEND . "/public");
define("LOCATION_ICON", LOCATION_SHARED . "/ui/icons/");
define("LOCATION_IMAGE", LOCATION_SHARED . "/default/images/");
define("LOCATION_FILES", LOCATION_ROOT . "/files");
define("LOCATION_DOWNLOAD", LOCATION_ROOT . "/files/downloads");
define("LOCATION_UPLOAD", LOCATION_ROOT . "/files/uploads");

define("SECURITY_SESSION_ISSIGNEDIN", sha1("isSignedIn"));

define("SECURITY_SESSION_SIGNINMETHOD_LOCAL", "local");
define("SECURITY_SESSION_SIGNINMETHOD_M365", "m365");

if (DEV_MODE) {
	define("DB_SERVER", "localhost");
	define("DB_DATABASE", "db_intranet");
	define("DB_USERNAME", "root");
	define("DB_PASSWORD", "");
	define("DB_CHARSET", "utf8mb4");
} else {
	define("DB_SERVER", "ID459940_kaboebe.db.webhosting.be");
	define("DB_DATABASE", "ID459940_kaboebe");
	define("DB_USERNAME", "ID459940_kaboebe");
	define("DB_PASSWORD", "PianomanPA125");
	define("DB_CHARSET", "utf8mb4");
}

define("ROUTER_DEFAULT_PREFIX", "/");
define("ROUTER_DEFAULT_MIDDLEWARE", "\\Router\\Middleware\\DefaultMiddleware");
define("ROUTER_DEFAULT_CONTROLLER", "\\Controllers\\DefaultController");
define("ROUTER_DEFAULT_FUNCTION", "index");

define("SELECT_ALL_VALUES", "Alle");
define("SELECT_OTHER_ID", 0);
define("SELECT_OTHER_VALUE", "Andere");
define("SELECT_OTHER", ["id" => SELECT_OTHER_ID, "name" => SELECT_OTHER_VALUE]);
define("SELECT_ALL_ID", 0);
define("SELECT_ALL_VALUE", "Alle");

define("EMAIL_SUFFIX", "coltd.be");
define("EMAIL_SUFFIX_STUDENT", "student.coltd.be");

define("WEEK_DAYS", [
	"nl" => [
		1 => "maandag",
		2 => "dinsdag",
		3 => "woensdag",
		4 => "donderdag",
		5 => "vrijdag",
		6 => "zaterdag",
		7 => "zondag"
	],
	"en" => [
		1 => "monday",
		2 => "tuesday",
		3 => "wednesday",
		4 => "thursday",
		5 => "friday",
		6 => "saturday",
		7 => "sunday"
	]
]);
