<?php

use Security\Session;
use Ouzo\Utilities\Json;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Object\BikeProfile;
use Database\Repository\BikeProfile as RepositoryBikeProfile;

require_once __DIR__ . "/../../../app/autoload.php";

$return = [];
$continue = true;

$id = Strings::isBlank($_POST['id']) ? null : $_POST['id'];
$upn = Strings::isBlank($_POST['upn']) ? Session::get(SECURITY_SESSION_ISSIGNEDIN)['upn'] : $_POST['upn'];
$deleted = Strings::isBlank($_POST['deleted']) ? null : $_POST['deleted'];
$address_street = $_POST['address_street'];
$address_number = $_POST['address_number'];
$address_bus = $_POST['address_bus'];
$address_zipcode = $_POST['address_zipcode'];
$address_city = $_POST['address_city'];
$address_country = $_POST['address_country'];
$mainSchool = $_POST['mainSchool'];
$distance1 = $_POST['distance1'];
$distance2 = Strings::isBlank($_POST['distance2']) ? 0 : $_POST['distance2'];
$bankAccount = $_POST['bankAccount'];

if (Strings::isBlank($address_street)) {
    $continue = false;
    Arrays::setNestedValue($return, ['validation', 'address_street', 'state'], 'invalid');
    Arrays::setNestedValue($return, ['validation', 'address_street', 'feedback'], "Adres - Straat is verplicht!");
}

if (Strings::isBlank($address_number)) {
    $continue = false;
    Arrays::setNestedValue($return, ['validation', 'address_number', 'state'], 'invalid');
    Arrays::setNestedValue($return, ['validation', 'address_number', 'feedback'], "Adres - Nummer is verplicht!");
}

if (Strings::isBlank($address_zipcode)) {
    $continue = false;
    Arrays::setNestedValue($return, ['validation', 'address_zipcode', 'state'], 'invalid');
    Arrays::setNestedValue($return, ['validation', 'address_zipcode', 'feedback'], "Adres - Postcode is verplicht!");
}

if (Strings::isBlank($address_city)) {
    $continue = false;
    Arrays::setNestedValue($return, ['validation', 'address_city', 'state'], 'invalid');
    Arrays::setNestedValue($return, ['validation', 'address_city', 'feedback'], "Adres - Stad is verplicht!");
}

if (Strings::isBlank($address_country)) {
    $continue = false;
    Arrays::setNestedValue($return, ['validation', 'address_country', 'state'], 'invalid');
    Arrays::setNestedValue($return, ['validation', 'address_country', 'feedback'], "Adres - Land is verplicht!");
}

if (Strings::isBlank($distance1)) {
    $continue = false;
    Arrays::setNestedValue($return, ['validation', 'distance1', 'state'], 'invalid');
    Arrays::setNestedValue($return, ['validation', 'distance1', 'feedback'], "Afstand 1 is verplicht!");
}

if (Strings::isBlank($bankAccount)) {
    $continue = false;
    Arrays::setNestedValue($return, ['validation', 'bankAccount', 'state'], 'invalid');
    Arrays::setNestedValue($return, ['validation', 'bankAccount', 'feedback'], "Rekeningnummer is verplicht!");
}

if (Strings::isNotBlank($distance1) && (floatval($distance1) > 0 && floatval($distance1) < 1)) {
    $continue = false;
    Arrays::setNestedValue($return, ['validation', 'distance1', 'state'], 'invalid');
    Arrays::setNestedValue($return, ['validation', 'distance1', 'feedback'], "Afstanden kleiner dan 1km worden niet aanvaard!");
}

if (Strings::isNotBlank($distance2) && (floatval($distance2) > 0 && floatval($distance2) < 1)) {
    $continue = false;
    Arrays::setNestedValue($return, ['validation', 'distance2', 'state'], 'invalid');
    Arrays::setNestedValue($return, ['validation', 'distance2', 'feedback'], "Afstanden kleiner dan 1km worden niet aanvaard!");
}

if ($continue) {
    try {
        $bikeProfile = new BikeProfile([
            'id' => $id,
            'deleted' => $deleted,
            'upn' => $upn,
            'address_street' => $address_street,
            'address_number' => $address_number,
            'address_bus' => $address_bus,
            'address_zipcode' => $address_zipcode,
            'address_city' => $address_city,
            'address_country' => $address_country,
            'mainSchool' => $mainSchool,
            'distance1' => $distance1,
            'distance2' => $distance2,
            'bankAccount' => $bankAccount,
        ]);

        (new RepositoryBikeProfile)->set($bikeProfile);
        $return['message']['state'] = "success";
        $return['message']['content'] = "Profiel opgeslagen!";
        $return['message']['disappear'] = 5;
    } catch (\Exception $e) {
        $return['error'] = $e->getMessage();
    }
}

echo Json::safeEncode($return);
