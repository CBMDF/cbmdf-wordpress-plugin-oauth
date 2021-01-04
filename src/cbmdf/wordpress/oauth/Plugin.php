<?php

namespace CBMDF\WordPress\OAuth;

use CBMDF\WordPress\OAuth\Options;
use CBMDF\WordPress\OAuth\View\Settings;
use CBMDF\WordPress\OAuth\View\Button;
use CBMDF\WordPress\OAuth\Provider;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Plugin
{

    public static function init()
    {

        // Realiza as configurações de inicialização, login, logout e a inclusão de estilos CSS e scripts no header.
        add_action('init', function () {
            self::logout();
            self::login();

            $styles_path = plugin_dir_url(__FILE__) . "/public/css/all.css";
            wp_register_style('font-awesome', $styles_path);
            wp_register_style('cbmdf-oauth', false);
            wp_enqueue_style('font-awesome');
            wp_enqueue_style('cbmdf-oauth');
            wp_add_inline_style('cbmdf-oauth', Options::get_instance()->get('custom_css'));
        });



        //Registra do menu administrativo
        add_action('admin_menu', function () {

            add_menu_page(
                'CBMDF OAuth',
                'CBMDF OAuth',
                'manage_options',
                'cbmdf-oauth-options',
                function () {
                    echo Settings::render();
                },
                'dashicons-id'
            );
        });

        // Registra o Shortcode
        add_shortcode('cbmdf_oauth', function ($atts = []) {
            return Button::render($atts);
        });

        // Registra o Widget
        add_action('widgets_init', function () {
            register_widget('CBMDF\WordPress\OAuth\Widget');
        });
    }

    /**
     * 
     * @return void 
     */
    public static function activate()
    {
        add_option('cbmdf_oauth_activation', array('CBMDF\WordPress\OAuth\Activator', 'activate'));
    }

    public static function login()
    {
        $options = Options::get_instance();
        $provider = Provider::get_provider();

        // Recupera o state gerado e grava na sessão.
        $_SESSION['oauth2state'] = $provider->getState();

        //echo '<pre>';
        //var_dump($provider->getState());

        if (isset($_POST['cbmdf-oauth-button'])) {

            $authorizationUrl = $provider->getAuthorizationUrl();
            $_SESSION['oauth2state'] = $provider->getState();

            // Redireciona o usuário para a URI de autorização.
            header('Location: ' . $authorizationUrl);
            exit;
        } elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {
            if (isset($_SESSION['oauth2state'])) {
                unset($_SESSION['oauth2state']);
            }
        } else {

            try {

                $accessToken = $provider->getAccessToken('authorization_code', [
                    'code' => $_GET['code']
                ]);

                $resourceOwner = $provider->getResourceOwner($accessToken);

                $request = $provider->getAuthenticatedRequest(
                    'GET',
                    $options->get('resource_uri'),
                    $accessToken
                );

                // Token Expirado
                if ($accessToken->hasExpired()) {
                    show_message("<div class='notice notice-error inline'><p>Token expirado!</p></div>");

                    // Verificar se o usuário existe
                } else {

                    $username = $resourceOwner->toArray()['num_cpf_pessoa'];
                    $email = $resourceOwner->toArray()['dsc_email'];
                    $user_id = username_exists($username);
                    if (!$user_id) {

                        $userdata = array(
                            'user_email'            => $email,   //(string) The user email address.
                            'user_pass'             => wp_generate_password(),
                            'user_login'            => $username,
                            'show_admin_bar_front'  => "false",
                        );

                        $user_id = wp_insert_user($userdata);
                    }

                    wp_set_current_user($user_id);
                    wp_set_auth_cookie($user_id);
                    wp_redirect($options->get('redirect_uri'));
                    exit;
                }
            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                exit($e->getMessage());
            } catch (\BadMethodCallException $e) {
                wp_die($e->getMessage());
            }
        }
    }


    public static function logout()
    {
        $options = Options::get_instance();
        if (isset($_GET['cbmdf-oauth-logout'])) {
            wp_logout();
            header('Location: ' . $options->get('logout_uri'));
            exit;
        }
    }
}
