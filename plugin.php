<?php

/**
 * Plugin Name:         CBMDF OAuth2
 * Plugin URI:          https://github.com/CBMDF/cbmdf-oauth-plugin-wordpress
 * Description:         Plugin para autenticação no servidor OAuth do CBMDF.
 * Version:             1.0.0
 * Requires at last:    5.6
 * Requires PHP:        5.6
 * Text Domain:         cbmdf_oauth
 * Domain Path:         /languages
 * Author:              CBMDF
 * Author URI:          https://github.com/CBMDF/
 * License:             GPL v2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace CBMDF\WordPress\OAuth;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

require("vendor/autoload.php");

// Activation hook
register_activation_hook(__FILE__, array('\CBMDF\OAuth\Plugin', 'activate'));

Plugin::init();
