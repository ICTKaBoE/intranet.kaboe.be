<?php

namespace Informat;

use Database\Repository\Source;
use GuzzleHttp\Client;
use Helpers\CString;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;

abstract class Connection
{
    const RESPONSE_CODE_OK = 200;
    const SOURCE_ID = "informat";

    static public function init()
    {
        $sourceRepo = new Source;

        $tokenValue = self::GetTokenValue();
        $tokenType = self::GetTokenType();
        $tokenUntil = self::GetTokenUntil();

        $identityEndpoint = Strings::trimToNull(self::GetIdentityEndpoint());
        $identityGrantType = Strings::trimToNull(self::GetIdentityGrantType());
        $identityClientId = Strings::trimToNull(self::GetIdentityClientId());
        $identityClientSecret = Strings::trimToNull(self::GetIdentityClientSecret());
        $identityScopes = Strings::trimToNull(self::GetIdentityScope());
        $identityScopes = CString::noLines($identityScopes, " ");

        if (Strings::isBlank($tokenValue) || Strings::isBlank($tokenUntil) || Clock::now()->isAfter(Clock::at($tokenUntil))) {
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

                $source = $sourceRepo->getById(self::SOURCE_ID);
                $source->tokenValue = $body['access_token'];
                $source->tokenType = $body['token_type'];
                $source->tokenUntil = Clock::now()->plusSeconds($body['expires_in'])->format("Y-m-d H:i:s");
                $sourceRepo->set($source);
            } else {
                return false;
            }
        }

        return true;
    }

    static public function GetIdentityEndpoint()
    {
        return (new Source)->getById(self::SOURCE_ID)->host;
    }

    static public function GetIdentityGrantType()
    {
        return (new Source)->getById(self::SOURCE_ID)->identityGrantType;
    }

    static public function GetIdentityClientId()
    {
        return (new Source)->getById(self::SOURCE_ID)->identityClientId;
    }

    static public function GetIdentityClientSecret()
    {
        return (new Source)->getById(self::SOURCE_ID)->identityClientSecret;
    }

    static public function GetIdentityScope()
    {
        return (new Source)->getById(self::SOURCE_ID)->identityScope;
    }

    static public function GetTokenValue()
    {
        return (new Source)->getById(self::SOURCE_ID)->tokenValue;
    }

    static public function GetTokenType()
    {
        return (new Source)->getById(self::SOURCE_ID)->tokenType;
    }

    static public function GetTokenUntil()
    {
        return (new Source)->getById(self::SOURCE_ID)->tokenUntil;
    }
}
