<?php
define("LOCATION_ROOT", dirname(dirname(__DIR__)));
define("LOCATION_FRONTEND", LOCATION_ROOT . "/frontend");
define("LOCATION_BACKEND", LOCATION_ROOT . "/backend");
define("LOCATION_SHARED", LOCATION_FRONTEND . "/shared");
define("LOCATION_APP", LOCATION_FRONTEND . "/app");
define("LOCATION_PUBLIC", LOCATION_FRONTEND . "/public");
define("LOCATION_ICON", LOCATION_SHARED . "/ui/icons/");
define("LOCATION_DOWNLOAD", LOCATION_ROOT . "/downloads");

define("SECURITY_SESSION_ISSIGNEDIN", sha1("isSignedIn"));
define("SECURITY_SESSION_PAGEERROR", sha1("pageerror"));
define("SECURITY_DEFAULT_GROUP", 2);

define("SECURITY_SESSION_SIGNINMETHOD_LOCAL", "local");
define("SECURITY_SESSION_SIGNINMETHOD_O365", "o365");

define("DB_SERVER", "localhost");
define("DB_DATABASE", "db_intranet2");
define("DB_USERNAME", "application");
define("DB_PASSWORD", "PianomanPA");

define("ROUTER_DEFAULT_PREFIX", "/app");
define("ROUTER_DEFAULT_MIDDLEWARE", "\\Router\\Middleware\\DefaultMiddleware");
define("ROUTER_DEFAULT_NAMESPACE", "\\Controllers");
define("ROUTER_DEFAULT_CONTROLLER", "DefaultController");
define("ROUTER_DEFAULT_FUNCTION", "index");
