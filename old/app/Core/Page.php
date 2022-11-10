<?php

namespace Core;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Security\Request;

abstract class Page
{
    static public function exists()
    {
        return file_exists(LOCATION_PUBLIC . self::folder());
    }

    static public function route()
    {
        global $isSignedIn;

        $route = "";
        $tool = Config::get("page/login");
        $page = "";

        if (Arrays::contains([4, 5], substr(http_response_code(), 0, 1))) {
            $tool = "error/" . http_response_code();
        } else if ($isSignedIn) {
            $tool = Request::parameter(REQUEST_ROUTE_PARAMETER_TOOL) ? Request::parameter(REQUEST_ROUTE_PARAMETER_TOOL) : Config::get("page/default/afterLogin");

            if (!Strings::equal($tool, Config::get("page/default/afterLogin"))) {
                $page = Request::parameter(REQUEST_ROUTE_PARAMETER_PAGE) ? Request::parameter(REQUEST_ROUTE_PARAMETER_PAGE) : Config::get("page/default/tool");
            }
        }

        $route = $tool . (Strings::isNotBlank($page) ? "/{$page}" : "");
        return $route;
    }

    static public function header()
    {
        global $isSignedIn;

        if ($isSignedIn) {
            ob_start();
            require_once LOCATION_PUBLIC . "/components/header/header.php";
            return ob_get_clean();
        }

        return false;
    }

    static public function navbar()
    {
        global $isSignedIn;

        if ($isSignedIn && !Strings::equal(self::route(), Config::get("page/default/afterLogin"))) {
            ob_start();
            require_once LOCATION_PUBLIC . "/components/navbar/navbar.php";
            return ob_get_clean();
        }

        return false;
    }

    static public function pagetitle()
    {
        global $isSignedIn;

        if ($isSignedIn && !Strings::equal(self::route(), Config::get("page/default/afterLogin"))) {
            ob_start();
            require_once LOCATION_PUBLIC . "/components/pagetitle/pagetitle.php";
            return ob_get_clean();
        }

        return false;
    }

    static public function modals()
    {
        ob_start();
        require_once LOCATION_PUBLIC . "/components/modals/modals.php";
        return ob_get_clean();
    }

    static public function content()
    {
        ob_start();
        require_once LOCATION_PUBLIC . self::folder() . "/" . self::filename() . ".php";
        return ob_get_clean();
    }

    static public function javascript()
    {
        $fileCheck = LOCATION_PUBLIC . self::folder() . "/" . self::filename() . ".js";
        $file = "./public" . self::folder() . "/" . self::filename() . ".js";
        if (file_exists($fileCheck)) return "<script src=\"$file\"></script>";
        return false;
    }

    static public function stylesheet()
    {
        $fileCheck = LOCATION_PUBLIC . self::folder() . "/" . self::filename() . ".css";
        $file = "./public" . self::folder() . "/" . self::filename() . ".css";
        if (file_exists($fileCheck)) return "<link rel=\"stylesheet\" href=\"$file\">";
        return false;
    }

    static public function footer()
    {
        global $isSignedIn;

        if ($isSignedIn) {
            ob_start();
            require_once LOCATION_PUBLIC . "/components/footer/footer.php";
            return ob_get_clean();
        }

        return false;
    }

    static public function formAction($method, $appendix = false)
    {
        $method = strtoupper($method);
        $route = explode("/", self::route());
        $appendix = ($appendix == false ? "" : ucfirst($appendix));

        $formFile = array_shift($route);
        foreach ($route as $r) $formFile .= ucfirst($r);

        return "./app/scripts/{$method}/{$formFile}{$appendix}.php";
    }

    static public function id($prefix, $suffix = false)
    {
        $route = explode("/", self::route());
        $id = "";
        foreach ($route as $r) $id .= ucfirst($r);

        return $prefix . $id . ($suffix ? ucfirst($suffix) : "");
    }

    static private function folder()
    {
        return "/pages/" . self::route();
    }

    static private function filename()
    {
        return Arrays::last(explode("/", self::route()));
    }
}
