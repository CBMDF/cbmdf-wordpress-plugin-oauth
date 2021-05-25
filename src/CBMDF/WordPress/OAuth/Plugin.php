<?php

namespace CBMDF\WordPress\OAuth;

//use CBMDF\WordPress\OAuth\Options;
use CBMDF\WordPress\OAuth\View\Settings;
use CBMDF\WordPress\OAuth\View\Button;
use CBMDF\WordPress\OAuth\Provider;

//include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Plugin
{
    public static function init()
    {
        // Realiza as configurações de inicialização, login, logout e a inclusão de estilos CSS e scripts no header.
        add_action('init', function () {
            // Inicializa a sessão
            if (!session_id()) {
                session_start();
            }

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
        try {
            $options = Options::get_instance();
            $provider = Provider::get_provider();

            // Recupera o state gerado e grava na sessão
            $_SESSION['oauth2state'] = $provider->getState();

            if (isset($_POST['cbmdf-oauth-button'])) {
                $authorizationUrl = $provider->getAuthorizationUrl();
                $_SESSION['oauth2state'] = $provider->getState();

                // Redireciona o usuário para a URI de autorização
                header('Location: ' . $authorizationUrl);
                exit;
            } elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {
                if (isset($_SESSION['oauth2state'])) {
                    unset($_SESSION['oauth2state']);
                }
            } else {
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
                // Verificar se o usuário existe no WP
                } else {          
                    $username = $resourceOwner->toArray()['num_cpf_pessoa'];
                    $email = $resourceOwner->toArray()['dsc_email'];
                    $user_id = username_exists($username);
                    
                    // Se o usuário não existe no WP, cria um novo.
                    if (!$user_id) {
                        $userdata = array(
                            'user_email'            => strtolower($email),   //(string) The user email address.
                            'user_pass'             => wp_generate_password(),
                            'user_login'            => $username,
                            'show_admin_bar_front'  => "false",
                        );
                        $user_id = wp_insert_user($userdata);
                    }

                    $pageRedirect = false;

                    if($options->get('complement_login')){

                        if(!in_array($options->get('locate_plugin'), 
                        (array) get_option( 'active_plugins', array()
                        ))){ 
                            wp_die("<p>Você já está autenticado no servidor pelo plugin CBMDF OAuth2.<br/>
                            <br/>Porém para usar o Complemento de Login você precisar ter o Plugin Groups instalado e ativado.<br/>
                            <br/><a href=" . $options->get('groups') . ">Clique aqui</a> e baixe o plugin.<br/>
                            <br/><a href=" . admin_url( '/plugin-install.php' ) . ">Clique aqui</a> e acesse a seção de instalação de plugins do menu de administração do seu site.</p>");
                        }

                        $data = wp_remote_post($options->get('external_api_perfis'), array(
                            'body'        => array('token'=>$accessToken->getToken()),
                            'method'      => 'POST',
                            'data_format' => 'body',
                            'sslverify' => false
                        ));

                        $obj = json_decode($data['body']);

                        foreach ( $obj as $perfil )
                        {
                            if($group = \Groups_Group::read_by_name( $perfil->nom_perfil )){
                                //Se existir um grupo com o nome do perfil, adiciona o usuário logado no grupo
                                \Groups_User_Group::create( array( 'user_id' => $user_id, 'group_id' => $group->group_id ) );
                                //Remove o usuário do grupo Registered
                                if(!empty($options->get('name_grupo_registered'))){
                                    $groupRemove = \Groups_Group::read_by_name($options->get('name_grupo_registered'));
                                    \Groups_User_Group::delete( $user_id, $groupRemove->group_id);
                                }
                            }
            
                            $perfisRedirect = $options->get('list_other_redirect');
	                        $perfisRedirect = explode(";" , $perfisRedirect);
                            
                            foreach ($perfisRedirect as $perfis){
                                if($perfil->nom_perfil == $perfis)
                                    $pageRedirect = true;
                            }
                        }
                    }

                    wp_set_current_user( $user_id );
                    wp_set_auth_cookie( $user_id );

                    if($pageRedirect){
                        wp_redirect( $options->get('page_redirect') );
                        exit;
                    }
                    
                    wp_redirect( $options->get('redirect_uri') );
                    exit;
                }
            }
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            exit($e->getMessage());
        } catch (\BadMethodCallException $e) {
            wp_die($e->getMessage());
        } catch (\Throwable $t) {
            echo json_last_error_msg();
            die($t->getMessage());
            wp_die("<p>Ocorreu um erro de autenticação no plugin CBMDF OAuth.<br/>Você pode tentar ativar a opção 'Ignorar erros de certificado' nas configurações do plugin.</p>");
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
