<?php

include_once "./backend/autoload.php";

use Router\Helpers;
use Helpers\CString;
use Security\Session;
use Jumbojett\OpenIDConnectClient;
use Database\Repository\General\Nationality;

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

// important to check for identification or authentication flow
if ($userinfo->acr !== "urn:be:e-contract:idp:oidc:acr:ident") {
    exit("Invalid ACR.");
}

Session::remove("oidc");
$referer = Session::get("referer");
Session::remove('referer');

// Session::set('eidData', $userinfo);
$nationalityRepo = new Nationality;
$params = [
    'cardNumber' => $userinfo->beid_card_number,
    'insz' => $userinfo->sub,
    'birthDate' => $userinfo->birthdate,
    'addressCity' => $userinfo->address->locality,
    'addressStreet' => CString::getStreetFromAddress($userinfo->address->street_address),
    'addressNumber' => preg_replace('/[^0-9]/', '', CString::getHouseNumberFromAddress($userinfo->address->street_address)),
    'addressBus' => preg_replace('/[^a-zA-Z]/', '', CString::getHouseNumberFromAddress($userinfo->address->street_address)),
    'addressZipcode' => $userinfo->address->postal_code,
    'sex' => strtoupper(substr($userinfo->gender, 0, 1)),
    'chipNumber' => $userinfo->beid_chip_number,
    'photo' => $userinfo->photo,
    'type' => $userinfo->beid_document_type,
    'firstName' => $userinfo->given_name,
    'middleName' => $userinfo->middle_name,
    'birthPlace' => $userinfo->place_of_birth->locality,
    'cardValidUntil' => $userinfo->beid_card_validity_end,
    'cardDeliveredAt' => $userinfo->beid_card_delivery_municipality,
    'fullName' => $userinfo->name,
    'nationalityId' => $nationalityRepo->getByName($userinfo->beid_nationality)->id,
    'name' => $userinfo->family_name,
    'age' => $userinfo->age,
    'cardValidFrom' => $userinfo->beid_card_validity_begin
];

Helpers::redirect($referer . "?" . http_build_query($params));
