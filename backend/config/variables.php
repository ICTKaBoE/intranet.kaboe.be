<?php
define("LOCATION_ROOT", dirname(dirname(__DIR__)));
define("LOCATION_FRONTEND", LOCATION_ROOT . "/frontend");
define("LOCATION_BACKEND", LOCATION_ROOT . "/backend");
define("LOCATION_SQL", LOCATION_ROOT . "/sql");
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

define("ROUTER_DEFAULT_PREFIX", "/app");
define("ROUTER_DEFAULT_MIDDLEWARE", "\\Router\\Middleware\\DefaultMiddleware");
define("ROUTER_DEFAULT_NAMESPACE", "\\Controllers");
define("ROUTER_DEFAULT_CONTROLLER", "DefaultController");
define("ROUTER_DEFAULT_FUNCTION", "index");

define("SELECT_ALL_VALUES", "Alle");
define("INFORMAT_CURRENT_SCHOOLYEAR", "2022-23");
