<?php

function get_cbmdf_oauth_button()
{

    $logout_url = site_url() . "/?cbmdf-oauth-logout";
    $current_user = wp_get_current_user();
    $cbmdf_oauth_custom_class = get_option("cbmdf_oauth_custom_class");
    $cbmdf_oauth_button_icon = get_option("cbmdf_oauth_button_icon");

    $btn_cbmdf_oauth = <<<EOF
    <form action="" method="post">
        <button type="submit" name="cbmdf-oauth-button" class="{$cbmdf_oauth_custom_class}" value="authenticate"><i class="{$cbmdf_oauth_button_icon}"></i> CBMDF OAuth</button>
    </form>
    EOF;

    $lnk_cbmdf_oauth = <<<EOF
        <a href="{$logout_url}" >Logout de <strong>{$current_user->user_login}</strong>.</a>
    EOF;

    if (is_user_logged_in()) return $lnk_cbmdf_oauth;
    return $btn_cbmdf_oauth;
}
