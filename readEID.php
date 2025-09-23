<?php

include_once "./backend/autoload.php";

use Jumbojett\OpenIDConnectClient;
use Router\Helpers;
use Security\Session;

// we need the session to store OpenID Connect protocol data
Session::start();

if (!Session::get('referer')) {
    Session::set('referer', Helpers::request()->getReferer());
}

if (!Session::get("oidc")) {
    $oidc = new OpenIDConnectClient("https://www.e-contract.be/eid-idp/oidc/ident/");
    // store the OpenID Connect client within the HTTP session
    Session::set("oidc", $oidc);
    // next really seems to be required
    $oidc->setClientName("KaBoE Intranet");
}

$oidc = Session::get("oidc");

if (!Helpers::url()->hasParam("code")) {
    // OpenID Connect Dynamic Client Registration
    $oidc->register();
}

// optionally add other OpenID Connect scopes
$oidc->addScope(["address", "photo"]);

// enable PKCE
$oidc->setCodeChallengeMethod("S256");

$oidc->authenticate();
$userinfo = $oidc->requestUserInfo();
Session::set('eidData', $userinfo);

// important to check for identification or authentication flow
if ($userinfo->acr !== "urn:be:e-contract:idp:oidc:acr:ident") {
    exit("Invalid ACR.");
}

Session::remove("oidc");
$referer = Session::get("referer");
Session::remove('referer');
Helpers::redirect($referer);
