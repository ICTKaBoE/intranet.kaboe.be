<?php

namespace M365;

use Database\Repository\Setting;
use Microsoft\Graph\Core\Authentication\GraphPhpLeagueAccessTokenProvider;
use Microsoft\Graph\GraphServiceClient;
use Microsoft\Kiota\Authentication\Oauth\ClientCredentialContext;
use Ouzo\Utilities\Arrays;

class GraphHelper
{
    private static string $clientId = '';
    private static string $clientSecret = '';
    private static string $tenantId = '';

    private static ClientCredentialContext $tokenContext;
    public static GraphServiceClient $appClient;

    public static function initializeGraphForAppOnlyAuth(): void
    {
        $settings = new Setting;
        GraphHelper::$clientId = Arrays::first($settings->get("m365.client.id"))->value;
        GraphHelper::$clientSecret = Arrays::first($settings->get("m365.client.secret"))->value;
        GraphHelper::$tenantId = Arrays::first($settings->get("m365.tenant.id"))->value;

        GraphHelper::$tokenContext = new ClientCredentialContext(
            GraphHelper::$tenantId,
            GraphHelper::$clientId,
            GraphHelper::$clientSecret
        );

        GraphHelper::$appClient = new GraphServiceClient(
            GraphHelper::$tokenContext,
            ['https://graph.microsoft.com/.default']
        );
    }

    public static function getAppOnlyToken(): string
    {
        // Create an access token provider to get the token
        $tokenProvider = new GraphPhpLeagueAccessTokenProvider(GraphHelper::$tokenContext);
        return $tokenProvider
            ->getAuthorizationTokenAsync('https://graph.microsoft.com')
            ->wait();
    }
}
