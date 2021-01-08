<?php

namespace CBMDF\WordPress\OAuth\View;

use CBMDF\WordPress\OAuth\Options;

if (!defined('ABSPATH')) exit; // Finaliza a execução se o arquivo é acessado diretamente.

class Button
{

    public static function render($args = array(), $instance = null)
    {

        if (!empty($instance['button_label'])) {
            $label = esc_html__($instance['button_label'], 'text_domain');;
        } elseif (isset($args['button_label'])) {
            $label = $args['button_label'];
        } else {
            $label = 'CBMDF OAuth';
        }
        $logout_url = site_url() . '/?cbmdf-oauth-logout';
        $current_user = wp_get_current_user();

        $options = Options::get_instance();

        $custom_class = $options->get('custom_class');
        $button_icon =  $options->get('button_icon');

        $btn_cbmdf_oauth = <<<OUTPUT
            <form action="" method="post">
                <button type="submit" name="cbmdf-oauth-button" class="{$custom_class}" value="authenticate"><i class="{$button_icon}"></i>&nbsp;{$label}</button>
            </form>
            OUTPUT;

        $lnk_cbmdf_oauth = <<<OUTPUT
            <a href="{$logout_url}" >Logout de <strong>{$current_user->user_login}</strong>.</a>
            OUTPUT;

        if (is_user_logged_in()) return $lnk_cbmdf_oauth;
        return $btn_cbmdf_oauth;
    }
}
