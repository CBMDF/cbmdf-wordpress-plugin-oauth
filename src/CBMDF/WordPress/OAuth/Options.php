<?php

namespace CBMDF\WordPress\OAuth;

if (!defined('ABSPATH')) exit; // Finaliza a execução se o arquivo é acessado diretamente.

/**
 * Classe para estuturar e persistir os dados de configuração do plugin.
 * 
 * @package CBMDF\WordPress\OAuth
 */
class Options
{
    /**
     * Contem a instância da classe.
     * @var CBMDF\WordPress\OAuth\Options
     */
    public static $instance;

    /**
     * Array contendo as opções de configuração do plugin. 
     * @var array
     */
    protected $options;

    /**
     * Opções de configuração válidas para salvar em banco de dados.

     */
    const VALID_OPTIONS = [
        "client_id",
        "client_secret",
        "authorize_uri",
        "token_uri",
        "resource_uri",
        "logout_uri",
        "redirect_uri",
        "button_icon",
        "custom_class",
        "custom_css",
        "aditional_property",
        "aditional_value"
    ];

    private function __construct()
    {
        // get_option retornará false se a o valor não existir.
        $options = get_option("cbmdf_oauth_options");
        if ($options != false) {
            $this->set_options($options);
        }
    }

    /**
     * Retorna uma instância da classe Options
     * 
     * @return CBMDF\WordPress\OAuth\Options
     */
    public static function get_instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Options;
        }
        return self::$instance;
    }

    /**
     * Retorna uma opção de configuração.
     * 
     * @param string $option 
     * @return string 
     */
    public function get($option)
    {
        return isset($this->options[$option]) ? esc_html($this->options[$option]) : null;
    }

    /**
     * Atualiza o valor de uma option válida na classe.
     * 
     * @param array $data
     * @return void 
     */
    private function set_options($data)
    {
        foreach ($data as $option => $value) {
            if (in_array($option, Options::VALID_OPTIONS)) {
                $this->options[$option] = stripslashes(sanitize_text_field($value));
            }
        }
    }

    /**
     * Retorna o array de opções de configuração do plugin.
     * 
     * @return array
     */
    public function get_options()
    {
        return $this->options;
    }

    /**
     * Persiste as opções de configuração do plugin no banco de dados do Wordpress.
     * 
     * @return void 
     */
    public function save($data)
    {
        $this->set_options($data);
        return update_option("cbmdf_oauth_options", $this->options);
    }
}
