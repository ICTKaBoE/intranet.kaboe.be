<?php
define("LOCATION_ROOT", dirname(dirname(__DIR__)));
define("LOCATION_APP", LOCATION_ROOT . "/app");
define("LOCATION_PUBLIC", LOCATION_ROOT . "/public");
define("LOCATION_ICON", LOCATION_PUBLIC . "/ui/icons/");
define("LOCATION_PUBLIC_DOWNLOADS", LOCATION_PUBLIC . "/downloads/");

define("SECURITY_SESSION_ISSIGNEDIN", sha1("isSignedIn"));
define("SECURITY_SESSION_PAGEERROR", sha1("pageerror"));

define("SECURITY_SESSION_SIGNINMETHOD_LOCAL", "local");
define("SECURITY_SESSION_SIGNINMETHOD_O365", "o365");

define("DB_SERVER", "localhost");
define("DB_DATABASE", "db_intranet2");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "");
