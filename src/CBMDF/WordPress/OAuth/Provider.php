<?php

namespace CBMDF\WordPress\OAuth;

if (!defined('ABSPATH')) exit; // Finaliza a execução se o arquivo é acessado diretamente.

class Provider
{

    public static function get_provider()
    {
        try {
            $options = Options::get_instance();           

            $provider = new \League\OAuth2\Client\Provider\GenericProvider([
                'clientId'                => $options->get('client_id'),
                'clientSecret'            => $options->get('client_secret'),
                'redirectUri'            => $options->get('redirect_uri'),
                'urlAuthorize'            => $options->get('authorize_uri'),
                'urlAccessToken'          => $options->get('token_uri'),
                'urlResourceOwnerDetails' => $options->get('resource_uri')
            ]);

            if($options->get('ignore_certificate_errors')){

                $guzzyClient = new \GuzzleHttp\Client([
                    'defaults' => [
                        \GuzzleHttp\RequestOptions::CONNECT_TIMEOUT => 5,
                        \GuzzleHttp\RequestOptions::ALLOW_REDIRECTS => true],
                        \GuzzleHttp\RequestOptions::VERIFY => false,
                ]);

                $provider->setHttpClient($guzzyClient);
            }
        } catch (Exception $e) {
            wp_die($e->getMessage());        
        }

        return $provider;
    }
}
