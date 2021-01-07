<?php

namespace CBMDF\WordPress\OAuth;

use CBMDF\WordPress\OAuth\View\Button;

if (!defined('ABSPATH')) exit; // Finaliza a execução se o arquivo é acessado diretamente.

class Widget extends \WP_Widget
{

    function __construct()
    {
        $widget_ops = array(
            'classname' => 'cbmdf_oauth_widget',
            'description' => 'Widget de autenticação CBMDF OAuth',
        );
        parent::__construct('cbmdf_oauth_widget', 'CBMDF OAuth Widget', $widget_ops);
    }

    /**
     * Renderiza o conteúdo do widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance)
    {
        echo Button::render($args, $instance);
    }

    /**
     * Salva as configurações do Widget.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Novos valores que serão salvos.
     * @param array $old_instance Valores anteriorios em banco de dados.
     *
     * @return array Dados atualizados.
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['button_label'] = (!empty($new_instance['button_label'])) ? sanitize_text_field($new_instance['button_label']) : '';

        return $instance;
    }
}
