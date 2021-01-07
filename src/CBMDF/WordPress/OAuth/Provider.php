<?php

namespace CBMDF\WordPress\OAuth;

if (!defined('ABSPATH')) exit; // Finaliza a execução se o arquivo é acessado diretamente.

class Provider
{

    public static function get_provider()
    {
        $options = Options::get_instance();

        $provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId'                => $options->get('client_id'),
            'clientSecret'            => $options->get('client_secret'),
            'redirectUri'            => $options->get('redirect_uri'),
            'urlAuthorize'            => $options->get('authorize_uri'),
            'urlAccessToken'          => $options->get('token_uri'),
            'urlResourceOwnerDetails' => $options->get('resource_uri')
        ]);

        return $provider;
    }
}
