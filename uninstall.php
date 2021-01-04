<?php
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

$role = get_role('administrator');
if (!empty($role)) {
    $role->remove_cap('cbmdf_oauth_manage');
    delete_option('cbmdf_oauth_options');
}
