<?php

use Security\Request;
use Security\Session;
use O365\AuthenticationManager;

require_once __DIR__ . "/../../../app/autoload.php";

// Get the authorization code and other parameters from the query string
// and store them in the session.
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['code'])) {
    Session::start();
    if (Request::parameter('admin_consent')) Session::set('admin_consent', Request::parameter('admin_consent'));
    if (Request::parameter('code')) Session::set('code', Request::parameter('code'));
    if (Request::parameter('session_state')) Session::set('session_state', Request::parameter('session_state'));
    if (Request::parameter('state')) Session::set('state', Request::parameter('state'));


    // With the authorization code, we can retrieve access tokens and other data.
    try {
        AuthenticationManager::acquireToken();

        Session::set(SECURITY_SESSION_ISSIGNEDIN, [
            'method' => 'o365',
            'oid' => Session::get("oid"),
            'upn' => Session::get("upn")
        ]);

        header('Location: ' . Request::host());
        exit();
    } catch (\RuntimeException $e) {
        echo 'Something went wrong, couldn\'t get tokens: ' . $e->getMessage();
    }
}
