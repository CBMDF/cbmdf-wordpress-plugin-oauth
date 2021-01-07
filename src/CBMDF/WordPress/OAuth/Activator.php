<?php

namespace CBMDF\WordPress\OAuth;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * 
 * @package CBMDF\OAuth
 * @todo Criar menu para acesso a página de configurações do plugin.
 */
class Activator
{
    /**
     * Método que realiza as configurações necessárias durante a ativação do plugin.
     * 
     * Este método é responsável por incluir o menu de navegação e as permissões de
     * gerenciamento do plugin para o grupo de administradores.
     * 
     * @return void 
     */
    public static function activate()
    {
        $role = get_role('administrator');
        if (!empty($role)) {
            $role->add_cap('cbmdf_oauth_manage');
        }
    }
}
