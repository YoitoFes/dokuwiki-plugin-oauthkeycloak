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
        /** @var Keycloak */
        $oauth = $this->getOAuthService();
        $data = array();

        $url = $oauth->getEndpoint(Keycloak::ENDPOINT_USERINFO);
        $raw = $oauth->request($url);

        if (!$raw) throw new OAuthException('Failed to fetch data from userinfo endpoint');
        $result = json_decode($raw, true);
        if (!$result) throw new OAuthException('Failed to parse data from userinfo endpoint');

        $data = array();
        $data['user'] = $result['preferred_username'];
        $data['name'] = $result['name'];
        $data['mail'] = $result['email'];
        if (array_key_exists('groups', $result)) {
            $data['grps'] = $result['groups'];
        } else {
            $data['grps'] = [];
        }

        return $data;
    }

    /** @inheritdoc */
    public function getScopes()
    {
        return array(Keycloak::SCOPE_OPENID);
    }

    /** @inheritDoc */
    public function getLabel()
    {
        return $this->getConf('label');
    }

    /** @inheritDoc */
    public function getColor()
    {
        return $this->getConf('color');
    }
}
