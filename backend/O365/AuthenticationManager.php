<?php

/**
 *  Copyright (c) Microsoft. All rights reserved. Licensed under the MIT license.
 *  See LICENSE in the project root for license information.
 * 
 *  PHP version 5
 *
 *  @category Code_Sample
 *  @package  O365-PHP-Microsoft-Graph-Connect
 *  @author   Ricardo Loo <ricardol@microsoft.com>
 *  @license  MIT License
 *  @link     http://GitHub.com/OfficeDev/O365-PHP-Microsoft-Graph-Connect
 */

/*! @header Office 365 PHP Connect sample using the Microsoft Graph
    @abstract A PHP project that shows how to use the Microsoft Graph 
 */

namespace O365;

use Database\Repository\Setting;
use Security\Session;
use O365\RequestManager;
use Router\Helpers;

// We use the session to store tokens and data about the user. 
Session::start();

/** 
 *  Provides methods to authenticate to Azure AD and 
 *  store tokens and user information 
 *
 *  @class    AuthenticationManager
 *  @category Code_Sample
 *  @package  O365-PHP-Microsoft-Graph-Connect
 *  @author   Ricardo Loo <ricardol@microsoft.com>
 *  @license  MIT License
 *  @link     http://GitHub.com/OfficeDev/O365-PHP-Microsoft-Graph-Connect
 */
class AuthenticationManager
{
    /**
     *  Starts the authentication flow. At the end, 
     *  the user should be redirected to callback.php 
     *
     *  @function connect
     *  @return   Nothing, redirects browser to authorize endpoint
     */
    public static function connect($username = null, $autoRedirect = true)
    {
        $settingRepo = new Setting;
        $devmode = $settingRepo->get("site.mode")[0]->value == "DEV";
        // Redirect the browser to the authorization endpoint. Auth endpoint is
        // https://login.microsoftonline.com/common/oauth2/authorize
        $redirect = "{{o365.url.authority}}{{o365.endpoint.authorize}}?response_type=code&client_id={{o365.client.id}}&redirect_uri={{o365.url.callback}}";
        if (!is_null($username)) $redirect .= "&login_hint=" . urlencode($username);

        foreach (array_reverse((new Setting)->get()) as $setting) {
            $redirect = str_replace('{{' . $setting->id . '}}', $setting->value, $redirect);
        }
        $redirect = str_replace("{{site:url}}", (Helpers::url()->getScheme() ?? 'http') . "://" . Helpers::url()->getHost(), $redirect);
        // $redirect = urlencode($redirect);

        if ($autoRedirect) {
            header("Location: {$redirect}");
            exit();
        } else return $redirect;
    }

    /**
     *  Contacts the token endpoint to get OAuth tokens including an access token
     *  that can be used to send an authenticated request to the 
     *  Microsoft Graph.
     *  It also stores user information, like given name, in session variables. 
     *
     *  @function acquireToken
     *  @return   Nothing, stores tokens in session variables.
     */
    public static function acquireToken()
    {
        $settingRepo = new Setting;
        $devmode = $settingRepo->get("site.mode")[0]->value == "DEV";
        // $tokenEndpoint = $settingRepo->get("o365.url.authority")[0]->value . $settingRepo->get("o365.endpoint.token")[0]->value;
        $tokenEndpoint = "{{o365.url.authority}}{{o365.endpoint.token}}";
        $clientId = "{{o365.client.id}}";
        $clientSecret = "{{o365.client.secret}}";
        $redirectUri = "{{o365.url.callback}}";
        $resource = "{{o365.url.resource}}";

        foreach (array_reverse((new Setting)->get()) as $setting) {
            $tokenEndpoint = str_replace('{{' . $setting->id . '}}', $setting->value, $tokenEndpoint);
            $clientId = str_replace('{{' . $setting->id . '}}', $setting->value, $clientId);
            $clientSecret = str_replace('{{' . $setting->id . '}}', $setting->value, $clientSecret);
            $redirectUri = str_replace('{{' . $setting->id . '}}', $setting->value, $redirectUri);
            $resource = str_replace('{{' . $setting->id . '}}', $setting->value, $resource);
        }

        $tokenEndpoint = str_replace("{{site:url}}", (Helpers::url()->getScheme() ?? 'http') . "://" . Helpers::url()->getHost(), $tokenEndpoint);
        $clientId = str_replace("{{site:url}}", (Helpers::url()->getScheme() ?? 'http') . "://" . Helpers::url()->getHost(), $clientId);
        $clientSecret = str_replace("{{site:url}}", (Helpers::url()->getScheme() ?? 'http') . "://" . Helpers::url()->getHost(), $clientSecret);
        $redirectUri = str_replace("{{site:url}}", (Helpers::url()->getScheme() ?? 'http') . "://" . Helpers::url()->getHost(), $redirectUri);
        $resource = str_replace("{{site:url}}", (Helpers::url()->getScheme() ?? 'http') . "://" . Helpers::url()->getHost(), $resource);

        // Send a POST request to the token endpoint to retrieve tokens.
        // Token endpoint is:
        // https://login.microsoftonline.com/common/oauth2/token
        $response = RequestManager::sendPostRequest(
            $tokenEndpoint,
            array(),
            array(
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => Session::get('code'),
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUri,
                'resource' => $resource
            )
        );

        // Store the raw response in JSON format.
        $jsonResponse = json_decode($response, true);

        // The access token response has the following parameters:
        // access_token - The requested access token.
        // expires_in - How long the access token is valid.
        // expires_on - The time when the access token expires.
        // id_token - An unsigned JSON Web Token (JWT).
        // refresh_token - An OAuth 2.0 refresh token.
        // resource - The App ID URI of the web API (secured resource).
        // scope - Impersonation permissions granted to the client application.
        // token_type - Indicates the token type value.
        foreach ($jsonResponse as $key => $value) {
            Session::set($key, $value);
        }

        // The id token is a JWT token that contains information about the user
        // It's a base64 coded string that has a header and payload 
        $decodedAccessTokenPayload = base64_decode(
            explode('.', Session::get('id_token'))[1]
        );
        $jsonAccessTokenPayload = json_decode($decodedAccessTokenPayload, true);

        // The id token payload has the following parameters:
        // aud - Audience of the token.
        // exp - Expiration time.
        // family_name - User’s last name or surname.
        // given_name - User’s first name.
        // iat - Issued at time.
        // iss - Identifies the token issuer.
        // nbf - Not before time. The time when the token becomes effective.
        // oid - Object identifier (ID) of the user object 
        //       in Azure Active Directory (AD).
        // sub - Token subject identifier.
        // tid - Tenant identifier of the Azure AD tenant that issued the token.
        // unique_name - A unique identifier that can be displayed to the user.
        // upn - User principal name.
        // ver - Version.
        foreach ($jsonAccessTokenPayload as $key => $value) {
            Session::set($key, $value);
        }
    }

    /**
     *  Clear the session and redirect the browser to Azure logout endpoint. 
     *
     *  @function disconnect
     *  @return   Nothing, redirects browser to Connect.php page.
     */
    public static function disconnect()
    {
        // $settingRepo = new Setting;
        Session::stop();

        // $connectUrl = Helpers::url()->getScheme() . "://" . Helpers::url()->getHost();

        // Logout endpoint is in the form
        // https://login.microsoftonline.com/common/oauth2/logout
        // ?post_logout_redirect_uri=<full_url_of_your_start_page> 
        $redirect = "{{o365.url.authority}}{{o365.endpoint.logout}}?post_logout_redirect_uri={{site:url}}";

        foreach (array_reverse((new Setting)->get()) as $setting) {
            $redirect = str_replace('{{' . $setting->id . '}}', $setting->value, $redirect);
        }
        $redirect = str_replace(
            "{{site:url}}",
            (Helpers::url()->getScheme() ?? 'http') . "://" . Helpers::url()->getHost(),
            $redirect
        );

        // $redirect = urlencode($redirect);
        // $redirect = $settingRepo->get("o365.url.authority")[0]->value . $settingRepo->get("o365.endpoint.logout")[0]->value .
        //     '?post_logout_redirect_uri=' . urlencode($connectUrl);
        header("Location: " . $redirect);
        exit();
    }
}
