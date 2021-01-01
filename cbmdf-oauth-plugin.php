<?php

namespace CBMDF\OAuth;

require("vendor/autoload.php");

class Plugin
{


    /**
     * Plugin Name:         CBMDF OAuth
     * Plugin URI:          https://github.com/CBMDF/wordpress_plugin_oauth
     * Description:         Autenticação no servidor OAuth do CBMDF.
     * Version:             1.0.0
     * Requires at last:    5.3
     * Requires PHP:        5.6
     * Text Domain:         cbmdf
     * Domain Path:         /public/lang
     * Author:              CBMDF
     * Author URI:          https://github.com/CBMDF/
     * License:             GPL v2 or later
     * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
     */

    /**
     * Realiza as funções de inicialização do plugin, como registro do widget e criação de menu.
     *
     * @return void 
     */



    public static function init()
    {
        // Inicializa a autenticação
        add_action('init', array("\CBMDF\OAuth\Plugin", 'cbmdf_oauth_authenticate'));


        add_shortcode('cbmdf_oauth', function ($atts = []) {
            require_once "src/views/button.php";
            return get_cbmdf_oauth_button($atts);
        });

        // Cria o menu administrativo        
        add_action('admin_menu', function () {
            add_menu_page(
                'CBMDF OAuth',
                'CBMDF OAuth',
                'manage_options',
                'cbmdf-oauth-options',
                array("\CBMDF\OAuth\Plugin", 'cbmdf_oauth_settings_page'),
                'dashicons-id',
                99
            );
        });

        // Registra o Widget
        add_action('widgets_init', function () {
            require("cbmdf-oauth-widget.php");
            register_widget('CBMDF\OAuth\Widget');
        });

        // Inclui estilos CSS e scripts no header.
        add_action('wp_enqueue_scripts', function () {
            $styles_path = plugin_dir_url(__FILE__) . "/assets/css/all.css";
            wp_register_style('font-awesome', $styles_path);
            wp_register_style('cbmdf-oauth', false);
            wp_enqueue_style('font-awesome');
            wp_enqueue_style('cbmdf-oauth');
            wp_add_inline_style('cbmdf-oauth', get_option('cbmdf_oauth_custom_css'));
        });
    }

    public static function cbmdf_oauth_settings_page()
    {
        require("src/views/settings.php");
    }

    public static function cbmdf_oauth_authenticate()
    {

        // Configuração do Provider
        require "src/provider-conf.php";

        if (isset($_GET['cbmdf-oauth-logout'])) {
            wp_logout();
            header('Location: ' . $cbmdf_oauth_logout_uri);
            exit;
        }

        if (isset($_POST['cbmdf-oauth-button'])) {

            $authorizationUrl = $provider->getAuthorizationUrl();

            // Get the state generated for you and store it to the session.
            $_SESSION['oauth2state'] = $provider->getState();

            // Redirect the user to the authorization URL.
            header('Location: ' . $authorizationUrl);
            exit;

            //wp_redirect($cbmdf_oauth_redirect_uri);
        } elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {
            if (isset($_SESSION['oauth2state'])) {
                unset($_SESSION['oauth2state']);
            }
            //wp_die('Invalid state');
        } else {

            try {

                $accessToken = $provider->getAccessToken('authorization_code', [
                    'code' => $_GET['code']
                ]);

                $resourceOwner = $provider->getResourceOwner($accessToken);

                $request = $provider->getAuthenticatedRequest(
                    'GET',
                    $cbmdf_oauth_resource_uri,
                    $accessToken
                );

                // Token Expirado
                if ($accessToken->hasExpired()) {
                    show_message("Token Expirado");

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

                        $current_user = get_user_by("login", $username);

                        $user_id = wp_insert_user($userdata);
                    }
                    echo $user_id;
                    wp_set_current_user($user_id);
                    wp_set_auth_cookie($user_id);
                    wp_redirect(site_url());
                    exit;
                }
            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                exit($e->getMessage());
            } catch (\BadMethodCallException $e) {
                wp_die($e->getMessage());
            }
        }
    }
}

Plugin::init();
