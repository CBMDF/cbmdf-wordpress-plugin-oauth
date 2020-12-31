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
        require "src/provider-conf.php";
        require_once "src/views/button.php";

        echo get_cbmdf_oauth_button();
    }
}
