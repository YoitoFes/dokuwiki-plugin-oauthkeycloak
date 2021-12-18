<?php

use dokuwiki\plugin\oauth\Adapter;
use dokuwiki\plugin\oauthkeycloak\Keycloak;

/**
 * Service Implementation for Keycloak authentication
 */
class action_plugin_oauthkeycloak extends Adapter
{
    /** @inheritdoc */
    public function registerServiceClass()
    {
        return Keycloak::class;
    }

    /**
     * @inheritdoc
     * @throws \OAuth\Common\Exception\Exception
     */
    public function logout()
    {
        /** @var Keycloak */
        $oauth = $this->getOAuthService();
        $oauth->logout();
    }

    /** * @inheritDoc */
    public function getUser()
    {
        $oauth = $this->getOAuthService();
        $data = array();

        $url = Keycloak::getEndpointUri(Keycloak::ENDPOINT_USERINFO);
        $raw = $oauth->request($url);

        if (!$raw) throw new OAuthException('Failed to fetch data from userinfo endpoint');
        $result = json_decode($raw, true);
        if (!$result) throw new OAuthException('Failed to parse data from userinfo endpoint');

        $data = array();
        $data['user'] = $result['preferred_username'];
        $data['name'] = $result['name'];
        $data['mail'] = $result['email'];
        $data['grps'] = $result['groups'];

        return $data;
    }

    /** @inheritdoc */
    public function getScopes()
    {
        return array(Keycloak::SCOPE_OPENID);
    }

    /** @inheritDoc */
    public function getColor()
    {
        return '#333333';
    }
}
