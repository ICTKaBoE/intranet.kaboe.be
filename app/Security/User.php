<?php

namespace Security;

use Database\Repository\User as RepositoryUser;
use O365\Objects\User as ObjectsUser;
use Ouzo\Utilities\Strings;

abstract class User {
    static public function isSignedIn() {
        return !is_null(Session::get(SECURITY_SESSION_ISSIGNEDIN));
    }

    static public function signInMethod() {
        return Session::get(SECURITY_SESSION_ISSIGNEDIN)['method'];
    }

    static public function get() {
        Session::start();
        
        if (Strings::equalsIgnoreCase(self::signInMethod(), 'o365')) return (new ObjectsUser)->get(Session::get(SECURITY_SESSION_ISSIGNEDIN)['oid']);
        if (Strings::equalsIgnoreCase(self::signInMethod(), 'local')) return (new RepositoryUser)->get(Session::get(SECURITY_SESSION_ISSIGNEDIN)['oid'], false)[0];
        return false;
    }
}