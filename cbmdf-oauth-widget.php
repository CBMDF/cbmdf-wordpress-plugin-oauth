<?php

namespace CBMDF\OAuth;

require("vendor/autoload.php");


class Widget
extends \WP_Widget
{

    function __construct()
    {
        parent::__construct('cbmdf_oauth_widget', 'CBMDF OAuth Widget');
    }


    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance)
    {

        // Configuração do Provider
        require "provider-conf.php";

        // Verificar se o usuário já está autenticado
        $logout_url = site_url() . "/?cbmdf-oauth-logout";
        $current_user = wp_get_current_user();
        if (is_user_logged_in()) {
            echo <<<EOF
                <a href="{$logout_url}" >Logout de <strong>{$current_user->user_login}</strong>.</a>
            EOF;
        } else {
            //Usuário não autenticado.
            echo <<<EOF
                <form action="" method="post"><button type="submit" name="cbmdf-oauth-button" value="authenticate" style="padding:4px 20px;line-height:20px;">CBMDF OAuth</button></form>
            EOF;
        }
    }
}
