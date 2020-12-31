<?php
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
delete_option("cbmdf_oauth_client_id");
delete_option("cbmdf_oauth_client_secret");
delete_option("cbmdf_oauth_authorize_uri");
delete_option("cbmdf_oauth_token_uri");
delete_option("cbmdf_oauth_resource_uri");
delete_option("cbmdf_oauth_logout_uri");
delete_option("cbmdf_oauth_redirect_uri");
