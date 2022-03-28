<?php

use dokuwiki\plugin\oauth\Adapter;
use dokuwiki\plugin\oauthazuread\AzureAD;

/**
 * Service Implementation for Azure AD authentication
 */
class action_plugin_oauthazuread extends Adapter
{
    /** @inheritdoc */
    public function registerServiceClass()
    {
        return AzureAD::class;
    }

    /**
     * @inheritdoc
     * @throws \OAuth\Common\Exception\Exception
     */
    public function logout()
    {
        /** @var AzureAD */
        $oauth = $this->getOAuthService();
        $oauth->logout();
    }

    /** * @inheritDoc */
    public function getUser()
    {
        /** @var AzureAD */
        $oauth = $this->getOAuthService();
        $data = array();

        $url = $oauth->getEndpoint(AzureAD::ENDPOINT_USERINFO);
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
        return array(AzureAD::SCOPE_OPENID);
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
