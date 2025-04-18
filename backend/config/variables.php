<?php

// Security group permissions: read, create, update, delete, export, changeSettings
define("VERSION_DB", "4.2.0");
define("URL_MAIN", "kaboe.be");

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
define("LOCATION_DOWNLOAD", LOCATION_ROOT . "/downloads");
define("LOCATION_UPLOAD", LOCATION_ROOT . "/uploads");

define("SECURITY_SESSION_ISSIGNEDIN", sha1("isSignedIn"));

define("SECURITY_SESSION_SIGNINMETHOD_LOCAL", "local");
define("SECURITY_SESSION_SIGNINMETHOD_M365", "m365");

if (str_starts_with($_SERVER["HTTP_HOST"], "dev")) {
	define("DB_SERVER", "localhost");
	define("DB_DATABASE", "db_intranet_v4");
	define("DB_USERNAME", "root");
	define("DB_PASSWORD", "");
	define("DB_CHARSET", "utf8mb4");
} else {
	define("DB_SERVER", "ID459940_kaboebe.db.webhosting.be");
	define("DB_DATABASE", "ID459940_kaboebe");
	define("DB_USERNAME", "ID459940_kaboebe");
	define("DB_PASSWORD", "kaboebe2025");
	define("DB_CHARSET", "utf8mb4");
}

define("ROUTER_DEFAULT_PREFIX", "/");
define("ROUTER_DEFAULT_MIDDLEWARE", "\\Router\\Middleware\\DefaultMiddleware");
define("ROUTER_DEFAULT_CONTROLLER", "\\Controllers\\DefaultController");
define("ROUTER_DEFAULT_FUNCTION", "index");

define("SELECT_ALL_VALUES", "Alle");
define("SELECT_OTHER_ID", "O");
define("SELECT_OTHER_VALUE", "Andere");
define("SELECT_ALL_ID", 0);
define("SELECT_ALL_VALUE", "Alle");

define("INFORMAT_CURRENT_SCHOOLYEAR", date('n') <= 8 ? (date("Y") - 1) . "-" . date("y") : date("Y") . "-" . (date("y") + 1));
// define("INFORMAT_CURRENT_SCHOOLYEAR", "2023-24");
define("INFORMAT_REFERENCE_DATE", "2024-01-01");

define("EMAIL_SUFFIX", "coltd.be");
define("EMAIL_SUFFIX_STUDENT", "student.coltd.be");
