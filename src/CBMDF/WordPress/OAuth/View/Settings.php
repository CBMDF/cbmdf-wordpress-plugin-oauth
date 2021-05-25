<?php

namespace CBMDF\WordPress\OAuth\View;

use CBMDF\WordPress\OAuth\Options;

if (!defined('ABSPATH')) exit; // Finaliza a execução se o arquivo é acessado diretamente.

/**
 * Classe para construção da página de configurações do plugin.
 * 
 * @package CBMDF\WordPress\OAuth\View
 */
class Settings
{
    /**
     * @see https://fontawesome.com/icons?d=gallery&m=free
     */
    static $icons = [
        'fas fa-address-card' => '&#xf2bb',
        'fas fa-user-lock' => '&#xf502',
        'fas fa-user' => '&#xf007',
        'fas fa-id-card' => '&#xf2c2',
        'fas fa-id-card-alt' => '&#xf47f',
        'fas fa-id-badge' => '&#xf2c1',
        'fas fa-key' => '&#xf084',
        'fas fa-passport' => '&#xf5ab',
        'fas fa-unlock-alt' => '&#xf13e',
        'fas fa-sign-in-alt' => '&#xf2f6'
    ];

    /**
     * Retorna uma string contendo os ícones para o botão de autenticação que poderão ser utilizados no botão.
     * 
     * @return string 
     */
    public static function get_icons()
    {

        $options = Options::get_instance();
        $output = '';
        $button_icon  = $options->get('button_icon');
        foreach (self::$icons as $key => $value) {
            $selected = '';
            if ($key == $button_icon) {
                $selected = 'selected="selected"';
            }
            $output .= "<option class=\"fa\" value=\"{$key}\" {$selected}>{$value}</option>";
        }
        return $output;
    }

    /**
     * Constrói a saída HTML para o formulário de configurações do plugin.
     * 
     * @return string 
     */
    public static function render()
    {
        $options = Options::get_instance();

        add_action( 'admin_menu', 'my_admin_plugin' );

        if (isset($_POST['btn-save-cbmdf-oauth-settings'])) {
            $options->save($_POST);
            show_message("<div class='notice notice-success inline'><p>Configurações salvas com sucesso!</p></div>");
        }

        $site_url = site_url();

        /**
         * Função que permite a chamada de outra função dentro de um bloco heredoc.
         */
        $call_function = function ($name) {
            return $name;
        };

        $selected_true = selected( $options->get('ignore_certificate_errors'), 1 , false);        
        $selected_false = selected( $options->get('ignore_certificate_errors'), 0 , false);
        
        $selected_login_true = selected( $options->get('complement_login'), 1 , false);
        $selected_login_false = selected( $options->get('complement_login'), 0 , false);

        if(!empty($options->get('name_grupo_registered')))
            $checked = "checked='checked'";
        else
            $checked = "";

        return <<<OUTPUT
            <script>
                jQuery(function(){
                    jQuery("#complement_login").change(function (){
                        var complemento = jQuery(this).val();
                        if(complemento == 0){
                            jQuery(".hidden-complemento").hide();
                        }
                        else{
                            jQuery(".hidden-complemento").show();
                        }
                    });
                });
            </script>
            <div class="wrap">

                <h1>Configurações de Autenticação OAuth</h1>

                <p>O protocolo <a href="https://www.oauth.com/" target="_blank">OAuth2</a> permite que o usuário
                    realize uma autenticação em um servidor externo de confiança de modo que não seja necessário armazenar a senha
                    ou outras informações. OAuth também é utilizado para prover recurso de Autenticação Única (Single Sign-On).</p>
                <p>Esse plugin suporta o uso de shortcodes. Exemplo: <strong><samp>[cbmdf_oauth button_label="Autenticar com App"]</samp></strong></p>

                <hr />

                <h2>Parâmetros de autenticação do cliente</h2>

                <form method="post" action="admin.php?page=cbmdf-oauth-options" novalidate="novalidate">
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row"><label for="client_id">Client ID</label></th>
                                <td><input name="client_id" type="text" id="client_id" value="{$options->get('client_id')}" class="regular-text">
                                    <p class="description">O Client ID é um <strong>identificador público</strong> da sua aplicação.</p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><label for="client_secret">Client Secret</label></th>
                                <td><input name="client_secret" type="text" id="client_secret" value="{$options->get('client_secret')}" class="regular-text">
                                    <p class="description">O Client Secret é um código de segurança conhecido apenas pela a aplicação e o servidor de autorizção.</p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><label for="authorize_uri">Authorize URI</label></th>
                                <td><input name="authorize_uri" type="text" id="authorize_uri" value="{$options->get('authorize_uri')}" placeholder="e.g. https://sistemas.cbm.df.gov.br/oauth/authorize" class=" large-text">
                                    <p class="description">Endereço para o qual o usuário será redirecionado para realizar a autenticação e autorização.</p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><label for="token_uri">Token URI</label></th>
                                <td><input name="token_uri" type="text" id="token_uri" value="{$options->get('token_uri')}" placeholder="e.g. https://sistemas.cbm.df.gov.br/oauth/token" class="large-text">
                                    <p class="description">Endereço para obter o token de acesso.</p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><label for="resource_uri">Resource URI</label></th>
                                <td><input name="resource_uri" type="text" id="resource_uri" value="{$options->get('resource_uri')}" placeholder="e.g. https://sistemas.cbm.df.gov.br/oauth/resource" class=" large-text">
                                    <p class="description">Endereço para obter informações sobre o usuário.</p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><label for="logout_uri">Logout URI</label></th>
                                <td><input name="logout_uri" type="text" id="logout_uri" value="{$options->get('logout_uri')}" placeholder="e.g. https://sistemas.cbm.df.gov.br/oauth/logout" class=" large-text">
                                    <p class="description">Aqui você deve informar a URL para efetuar o logout no servidor OAuth. Alguns servidores implementam o recurso para redirecionar
                                        após o logout, se este for o caso você deve especificar manualmente o endereço para o qual deseja retornar.</p>
                                    <p>Exemplo:<code>https://sistemas.cbm.df.gov.br/oauth/logout/<strong>?redirect_to=</strong>http://www.cbm.df.gov.br</code></p>


                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><label for="redirect_uri">Redirect URI</label></th>
                                <td><input name="redirect_uri" type="text" id="redirect_uri" placeholder="{$site_url}" value="{$options->get('redirect_uri')}" class=" large-text">
                                    <p class="description">Endereço de redirecionamento após obter o token de acesso.</p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><label for="button_icon">Ignorar erros de certificado</label></th>
                                <td> 
                                    <select name="ignore_certificate_errors">                          
                                    <option value="0" {$selected_false}>Não</option>
                                    <option value="1" {$selected_true}>Sim</option>                                
                                    </select>
                                    <p class="description">Ignorar erros de certificado tais como expirados ou auto-assinados.</p>
                                </td>
                            </tr>

                        </tbody>
                    </table>

                    <hr />

                    <h2>Complemento de Login</h2>
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row"><label for="button_icon">Usar Complemento de Login</label></th>
                                <td> 
                                    <select name="complement_login" id="complement_login">                          
                                        <option value="0" {$selected_login_false}>Não</option>
                                        <option value="1" {$selected_login_true}>Sim</option>                                
                                    </select>
                                    <p class="description">Complemento de login permite associar usuários a grupos de acordo com os seus perfis advindos de uma API externa. É <strong>necessário</strong> ter o plugin Groups da itthinx <strong>instalado</strong> e <strong>ativado</strong> em seu site.</p>
                                </td>
                            </tr>
                            
                            <tr class="hidden-complemento">
                                <th scope="row"><label for="locate_plugin">Localização do Plugin de Groups no servidor</label></th>
                                <td><input name="locate_plugin" type="text" id="locate_plugin" placeholder="groups/groups.php" value="{$options->get('locate_plugin')}" class=" large-text">
                                    <p class="description">Local do plugin de groups. Ex.: groups/groups.php.</p>
                                </td>
                            </tr>

                            <tr class="hidden-complemento">
                                <th scope="row"><label for="groups">Site do Plugin de Groups</label></th>
                                <td><input name="groups" type="text" id="groups" placeholder="https://wordpress.org/plugins/groups" value="{$options->get('groups')}" class=" large-text">
                                    <p class="description">Site do plugin de groups. Ex.: https://wordpress.org/plugins/groups.</p>
                                </td>
                            </tr>

                            <tr class="hidden-complemento">
                                <th scope="row"><label for="list_other_redirect">Perfis que serão redirecionado para outro lugar</label></th>
                                <td><input name="list_other_redirect" type="text" id="list_other_redirect" placeholder="PERFIL_1; PERFIL_X" value="{$options->get('list_other_redirect')}" class=" large-text">
                                    <p class="description">Lista de perfis que serão redirecionados para outro local separados por ponto e vírgula (;). Ex: PERFIL_1; PERFIL_X</p>
                                </td>
                            </tr>

                            <tr class="hidden-complemento">
                                <th scope="row"><label for="page_redirect">URL de Redirecionamento para os perfis da lista acima</label></th>
                                <td><input name="page_redirect" type="text" id="page_redirect" placeholder="" value="{$options->get('page_redirect')}" class=" large-text">
                                    <p class="description">Endereço de redirecionamento para os perfis escolhidos na lista acima.</p>
                                </td>
                            </tr>
                            
                            <tr class="hidden-complemento">
                                <th scope="row"><label for="external_api_perfis">Link da API</label></th>
                                <td><input name="external_api_perfis" type="text" id="external_api_perfis" placeholder="{$site_url}" value="{$options->get('external_api_perfis')}" class=" large-text">
                                    <p class="description">Link da API externa para busca de perfis. O retorno da API deve ser um JSON com a lista de perfis. Lembrando que o token gerado pelo o OAuth2 é passado no corpo da requisição da API.</p>
                                </td>
                            </tr>

                            <tr class="hidden-complemento">
                                <th scope="row"><label for="button_icon">Remover do grupo Registrado</label></th>
                                <td>                                    
                                    <input type="checkbox" id="cbmdf-oauth-toggle-aditional" $checked>
                                    <input class="cbmdf-oauth-group-registered regular-text" type="text" 
                                    name="name_grupo_registered" value="{$options->get('name_grupo_registered')}" id="cbmdf-oauth-group-registered" />
                                    <p class="description">O usuário será removido do grupo <strong>Registrado</strong>. Digite o nome do grupo Registrado do seu site, geralmente é Registered.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <hr />

                    <h2>Aparência do Widget</h2>

                    <table class="form-table" role="presentation">
                        <tbody>

                            <tr>
                                <th scope="row"><label for="button_icon">Ícone do Botão</label></th>
                                <td>
                                    <select class="fa" name="button_icon" style="font-size:200%;">
                                        {$call_function(self::get_icons())}
                                    </select>

                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><label for="custom_class">Classes Adicionais</label></th>
                                <td><input type="text" name="custom_class" class="regular-text" type="text" id="custom_class" value="{$options->get('custom_class')}">
                                    <p class="description">Informar as classes que serão associadas ao atributo <strong>class</strong> do botão. Separar por espaço.
                                    </p>
                                    <p>Exemplo:
                                        <strong><code>
                                                button-secondary oauth-button
                                            </code></strong>
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><label for="custom_css">CSS Personalizado</label></th>
                                <td><textarea cols="80" rows="10" name="custom_css" type="text" id="custom-css">{$options->get('custom_css')}</textarea>
                                    <p class="description">O bloco de código CSS será incluído no <strong>header</strong> da página.</p>
                                    <p>Exemplo:
                                        <strong><code>
                                                .oauth-button{ padding: 4px 10px !important; border-radius: 5px !important; font-size: 90%; line-height: 20px; text-transform: none !important;}
                                            </code></strong>
                                    </p>

                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <hr />

                    <h2>Criação do Usuário no WordPress</h2>

                    <p >Nesta seção você pode especificar qual propriedade e/ou valor devem ser retornadas pelo servidor OAuth para o usuário.</p>

                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row"><label for="button_icon">Exigir propriedade adicional</label></th>
                                <td>                                    
                                    <input type="checkbox" id="cbmdf-oauth-toggle-aditional">
                                    <input class="cbmdf-oauth-aditional-property regular-text" type="text" 
                                    name="aditional_property" id="cbmdf-oauth-aditional-property" />
                                    <p class="description">O usuário só será criado no WordPress caso as informações do usuário retornadas pelo servidor OAuth contenham a <strong>propriedade</strong> especificada.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="button_icon">Combinação dos critérios</label></th>
                                <td>                                    
                                   <select name="aditional_value_criteria" >
                                   <option value="and">Exigir ambos</option>
                                   <option value="or">Um ou outro</option>
                                   </select>                                    

                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="button_icon">Exigir valor adicional</label></th>
                                <td>                                    
                                    <input type="checkbox" id="cbmdf-oauth-toggle-aditional">
                                    <input class="cbmdf-oauth-aditional-value regular-text" type="text" 
                                    name="aditional_value" id="cbmdf-oauth-aditional-value" />
                                    <p class="description">O usuário só será criado no WordPress caso as informações do usuário retornadas pelo servidor OAuth contenham o <strong>valor</strong> especificado.</p>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <input type="submit" class="button button-primary" name="btn-save-cbmdf-oauth-settings" value="Salvar alterações" />
                </form>
            </div>
        OUTPUT;
    }
}
