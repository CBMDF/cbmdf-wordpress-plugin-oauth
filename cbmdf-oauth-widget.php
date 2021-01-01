<?php

namespace CBMDF\OAuth;

require("vendor/autoload.php");


class Widget
extends \WP_Widget
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

        echo get_cbmdf_oauth_button($args, $instance);
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    public function form($instance)
    {
        $button_label = !empty($instance['button_label']) ? $instance['button_label'] : esc_html__('Fazer login com CBMDF', 'text_domain');
?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('button_label')); ?>"><?php esc_attr_e('Texto do Botão:', 'text_domain'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('button_label')); ?>" name="<?php echo esc_attr($this->get_field_name('button_label')); ?>" type="text" value="<?php echo esc_attr($button_label); ?>">
        </p>
<?php
    }


    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['button_label'] = (!empty($new_instance['button_label'])) ? sanitize_text_field($new_instance['button_label']) : '';

        return $instance;
    }
}
