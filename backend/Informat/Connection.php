<?php

namespace Informat;

use Database\Repository\Setting\Setting;
use Database\Repository\Source;
use GuzzleHttp\Client;
use Helpers\CString;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Clock;
use Ouzo\Utilities\Strings;

abstract class Connection
{
    const RESPONSE_CODE_OK = 200;

    static public function init($sourceId = null)
    {
        $sourceRepo = new Source;

        $tokenValue = self::GetTokenValue($sourceId);
        $tokenType = self::GetTokenType($sourceId);
        $tokenUntil = self::GetTokenUntil($sourceId);

        $identityEndpoint = Strings::trimToNull(self::GetIdentityEndpoint($sourceId));
        $identityGrantType = Strings::trimToNull(self::GetIdentityGrantType($sourceId));
        $identityClientId = Strings::trimToNull(self::GetIdentityClientId($sourceId));
        $identityClientSecret = Strings::trimToNull(self::GetIdentityClientSecret($sourceId));
        $identityScopes = Strings::trimToNull(self::GetIdentityScope($sourceId));
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

                $source = $sourceRepo->getById($sourceId);
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

    static public function GetIdentityEndpoint($sourceId)
    {
        return (new Source)->getById($sourceId)->identityEndpoint;
    }

    static public function GetIdentityGrantType($sourceId)
    {
        return (new Source)->getById($sourceId)->identityGrantType;
    }

    static public function GetIdentityClientId($sourceId)
    {
        return (new Source)->getById($sourceId)->identityClientId;
    }

    static public function GetIdentityClientSecret($sourceId)
    {
        return (new Source)->getById($sourceId)->identityClientSecret;
    }

    static public function GetIdentityScope($sourceId)
    {
        return (new Source)->getById($sourceId)->identityScope;
    }

    static public function GetTokenValue($sourceId)
    {
        return (new Source)->getById($sourceId)->tokenValue;
    }

    static public function GetTokenType($sourceId)
    {
        return (new Source)->getById($sourceId)->tokenType;
    }

    static public function GetTokenUntil($sourceId)
    {
        return (new Source)->getById($sourceId)->tokenUntil;
    }
}
