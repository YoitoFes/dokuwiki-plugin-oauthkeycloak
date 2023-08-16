<?php

namespace dokuwiki\plugin\oauthkeycloak;

use dokuwiki\plugin\oauth\Service\AbstractOAuth2Base;
use OAuth\Common\Http\Uri\Uri;

/**
 * Custom Service for Keycloak oAuth
 */
class Keycloak extends AbstractOAuth2Base
{
    /**
     * Defined scopes are listed here:
     * @link https://www.keycloak.org/docs/latest/server_admin/#_client_scopes
     */
    const SCOPE_OPENID    = 'openid';

    /**
     * Endpoints are listed here:
     * @link https://www.iana.org/assignments/oauth-parameters/oauth-parameters.xhtml#authorization-server-metadata
     */
    const ENDPOINT_AUTH     = 'authorization_endpoint';
    const ENDPOINT_TOKEN    = 'token_endpoint';
    const ENDPOINT_USERINFO = 'userinfo_endpoint';
    /**
     * This endpoint is used for backchannel logout and documented here
     * @link https://www.keycloak.org/docs/latest/server_admin/#con-basic-settings_server_administration_guide
     */
    const ENDPOINT_LOGOUT   = 'end_session_endpoint';

    protected $discovery;

    /**
     * Return URI of discovered endpoint
     *
     * @return string
     */
    public function getEndpoint(string $endpoint)
    {
        if (!isset($this->discovery)) {
            $plugin = plugin_load('helper', 'oauthkeycloak');
            $json = file_get_contents($plugin->getConf('openidurl'));
            if (!$json) throw new \Exception('Failed accessing ' . $plugin->getConf('openidurl'));
            $this->discovery = json_decode($json, true);
        }
        if (!isset($this->discovery[$endpoint])) return '';
        return $this->discovery[$endpoint];
    }

    /** @inheritdoc */
    public function getAuthorizationEndpoint()
    {
        return new Uri($this->getEndpoint(self::ENDPOINT_AUTH));
    }

    /** @inheritdoc */
    public function getAccessTokenEndpoint()
    {
        return new Uri($this->getEndpoint(self::ENDPOINT_TOKEN));
    }

    /** @inheritdoc */
    protected function getAuthorizationMethod()
    {
        return static::AUTHORIZATION_METHOD_HEADER_BEARER;
    }

    /**
     * Logout from Keycloak
     *
     * @return void
     * @throws \OAuth\Common\Exception\Exception
     */
    public function logout()
    {
        $token = $this->getStorage()->retrieveAccessToken($this->service());
        $refreshToken = $token->getRefreshToken();

        if (!$refreshToken) {
            return;
        }

        $parameters = [
            'client_id' => $this->credentials->getConsumerId(),
            'client_secret' => $this->credentials->getConsumerSecret(),
            'refresh_token' => $refreshToken,
        ];

        $this->httpClient->retrieveResponse(
            new Uri($this->getEndpoint(self::ENDPOINT_LOGOUT)),
            $parameters,
            $this->getExtraOAuthHeaders()
        );
    }
}
