<?php

namespace Informat\REST;

use Database\Repository\Setting;
use GuzzleHttp\Client;
use Helpers\CString;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;

abstract class Connection
{
    const RESPONSE_CODE_OK = 200;

    static public function init()
    {
        $settingRepo = new Setting;

        $tokenValue = Arrays::firstOrNull($settingRepo->get('informat.token.value'));
        $tokenType = Arrays::firstOrNull($settingRepo->get('informat.token.type'));
        $tokenUntil = Arrays::firstOrNull($settingRepo->get('informat.token.until'));

        $identityEndpoint = Strings::trimToNull(self::GetIdentityEndpoint());
        $identityGrantType = Strings::trimToNull(self::GetIdentityGrantType());
        $identityClientId = Strings::trimToNull(self::GetIdentityClientId());
        $identityClientSecret = Strings::trimToNull(self::GetIdentityClientSecret());
        $identityScopes = Strings::trimToNull(self::GetIdentityScope());
        $identityScopes = CString::noLines($identityScopes, " ");

        if (Strings::isBlank($tokenValue->value) || Strings::isBlank($tokenUntil->value) || Clock::now()->isAfter(Clock::at($tokenUntil?->value))) {
            $client = new Client;

            $response = $client->request('POST', $identityEndpoint, [
                'form_params' => [
                    'grant_type' => $identityGrantType,
                    'client_id' => $identityClientId,
                    'client_secret' => $identityClientSecret,
                    'scope' => $identityScopes
                ]
            ]);

            if ($response->getStatusCode() == self::RESPONSE_CODE_OK) {
                $body = $response->getBody()->getContents();
                $body = json_decode($body, true);

                $tokenValue->value = $body['access_token'];
                $tokenType->value = $body['token_type'];
                $tokenUntil->value = Clock::now()->plusSeconds($body['expires_in'])->format("Y-m-d H:i:s");

                $settingRepo->set($tokenValue);
                $settingRepo->set($tokenType);
                $settingRepo->set($tokenUntil);
            } else {
                return false;
            }
        }

        return true;
    }

    static public function GetIdentityEndpoint()
    {
        return Arrays::first((new Setting)->get("informat.identity.endpoint"))->value;
    }

    static public function GetIdentityGrantType()
    {
        return Arrays::first((new Setting)->get("informat.identity.grantType"))->value;
    }

    static public function GetIdentityClientId()
    {
        return Arrays::first((new Setting)->get("informat.identity.clientId"))->value;
    }

    static public function GetIdentityClientSecret()
    {
        return Arrays::first((new Setting)->get("informat.identity.clientSecret"))->value;
    }

    static public function GetIdentityScope()
    {
        return Arrays::first((new Setting)->get("informat.identity.scope"))->value;
    }

    static public function GetTokenValue()
    {
        return Arrays::first((new Setting)->get("informat.token.value"))->value;
    }

    static public function GetTokenType()
    {
        return Arrays::first((new Setting)->get("informat.token.type"))->value;
    }

    static public function GetTokenUntil()
    {
        return Arrays::first((new Setting)->get("informat.token.until"))->value;
    }
}
