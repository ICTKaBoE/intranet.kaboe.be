<?php

namespace M365;

use Microsoft\Graph\Core\Authentication\GraphPhpLeagueAccessTokenProvider;
use Microsoft\Graph\Generated\Models;
use Microsoft\Graph\Generated\Users\UsersRequestBuilderGetQueryParameters;
use Microsoft\Graph\Generated\Users\UsersRequestBuilderGetRequestConfiguration;
use Microsoft\Graph\GraphServiceClient;
use Microsoft\Kiota\Authentication\Oauth\ClientCredentialContext;

class GraphHelper
{
    private static string $clientId = '904777c1-08a6-4866-ac72-30ee26dd7724';
    private static string $clientSecret = 'vNb8Q~3QduKyCZBXAYbayU3pQGO8zwtLgNHPRbPw';
    private static string $tenantId = '360f4fe0-2089-4e49-9c9d-df601c5edef9';
    private static ClientCredentialContext $tokenContext;
    private static GraphServiceClient $appClient;

    public static function initializeGraphForAppOnlyAuth(): void
    {
        // GraphHelper::$clientId = $_ENV['CLIENT_ID'];
        // GraphHelper::$clientSecret = $_ENV['CLIENT_SECRET'];
        // GraphHelper::$tenantId = $_ENV['TENANT_ID'];

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
}
