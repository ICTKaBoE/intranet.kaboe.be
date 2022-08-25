<?php
define("LOCATION_ROOT", dirname(dirname(__DIR__)));
define("LOCATION_APP", LOCATION_ROOT . "/app");
define("LOCATION_PUBLIC", LOCATION_ROOT . "/public");
define("LOCATION_ICON", LOCATION_PUBLIC . "/ui/icons/");
define("LOCATION_PUBLIC_DOWNLOADS", LOCATION_PUBLIC . "/downloads/");

define("REQUEST_ROUTE_PARAMETER_TOOL", "t");
define("REQUEST_ROUTE_PARAMETER_PAGE", "p");
define("REQUEST_ROUTE_PARAMETER_ID", "id");

define("SECURITY_SESSION_ISSIGNEDIN", sha1("isSignedIn"));
define("SECURITY_SESSION_PAGEERROR", sha1("pageerror"));
