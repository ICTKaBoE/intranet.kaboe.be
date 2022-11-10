<?php

use Security\Session;
use Ouzo\Utilities\Json;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Database\Repository\User;

require_once __DIR__ . "/../../../app/autoload.php";

$return = [];
$continue = true;

$email = $_POST['email'];
$password = $_POST['password'];

if (Strings::isBlank($email)) {
    $continue = false;
    Arrays::setNestedValue($return, ['validation', 'email', 'state'], 'invalid');
    Arrays::setNestedValue($return, ['validation', 'email', 'feedback'], "Email is verplicht!");
}

if (!Strings::contains($email, "@") && Strings::isBlank($password)) {
    $continue = false;
    Arrays::setNestedValue($return, ['validation', 'password', 'state'], 'invalid');
    Arrays::setNestedValue($return, ['validation', 'password', 'feedback'], "Wachtwoord is verplicht!");
}

if ($continue) {
    if (Strings::contains($email, "@")) {
        Arrays::setNestedValue($return, ['redirect'], O365\AuthenticationManager::connect($email, false));
    } else {
        $userRepo = new User;
        $usersByUsername = $userRepo->getByUsername($email);
        $userWithPassword = false;

        foreach ($usersByUsername as $user) {
            if (Strings::equal($user->password, sha1($password))) {
                $userWithPassword = $user;
                break;
            }
        }

        if ($userWithPassword) {
            Session::set(SECURITY_SESSION_ISSIGNEDIN, [
                'method' => 'local',
                'oid' => $userWithPassword->id,
                'upn' => $email
            ]);
            Arrays::setNestedValue($return, ['reload'], true);
        }
    }
}

echo Json::safeEncode($return);
