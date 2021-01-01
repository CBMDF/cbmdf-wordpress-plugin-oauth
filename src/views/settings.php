 <?php

    if (isset($_POST["btn-save-cbmdf-oauth-settings"])) {
        // If the option does not exist, it will be created.
        update_option("cbmdf_oauth_client_id", sanitize_text_field($_POST['client-id']));
        update_option("cbmdf_oauth_client_secret", sanitize_text_field($_POST['client-secret']));
        update_option("cbmdf_oauth_authorize_uri", stripslashes(sanitize_text_field($_POST['authorize-uri'])));
        update_option("cbmdf_oauth_token_uri", stripslashes(sanitize_text_field($_POST['token-uri'])));
        update_option("cbmdf_oauth_resource_uri", stripslashes(sanitize_text_field($_POST['resource-uri'])));
        update_option("cbmdf_oauth_logout_uri", stripslashes(sanitize_text_field($_POST['logout-uri'])));
        update_option("cbmdf_oauth_redirect_uri", site_url());
        update_option("cbmdf_oauth_custom_css", stripslashes(sanitize_text_field($_POST['custom-css'])));
        update_option("cbmdf_oauth_custom_class", stripslashes(sanitize_text_field($_POST['custom-class'])));
        update_option("cbmdf_oauth_button_icon", stripslashes(sanitize_text_field($_POST['button-icon'])));


        show_message("Configurações salvas!");
    }


    $cbmdf_oauth_client_id = get_option("cbmdf_oauth_client_id");
    $cbmdf_oauth_client_secret  = get_option("cbmdf_oauth_client_secret");
    $cbmdf_oauth_authorize_uri  = get_option("cbmdf_oauth_authorize_uri");
    $cbmdf_oauth_token_uri  = get_option("cbmdf_oauth_token_uri");
    $cbmdf_oauth_resource_uri  = get_option("cbmdf_oauth_resource_uri");
    $cbmdf_oauth_logout_uri  = get_option("cbmdf_oauth_logout_uri");
    $cbmdf_oauth_redirect_uri  = get_option("cbmdf_oauth_redirect_uri");
    $cbmdf_oauth_custom_css = get_option("cbmdf_oauth_custom_css");
    $cbmdf_oauth_custom_class = get_option("cbmdf_oauth_custom_class");
    $cbmdf_oauth_button_icon = get_option("cbmdf_oauth_button_icon");

    ?>

 <div class="wrap">

     <h1>Configurações de Autenticação OAuth</h1>

     <h2>Parâmetros de autenticação do cliente</h2>

     <p class="description">O protocolo <a href="https://www.oauth.com/" target="_blank">OAuth2</a> permite que o usuário
         realize uma autenticação em um servidor externo de confiança de modo que não seja necessário armazenar a senha
         ou outras informações. OAuth também é utilizado para prover recurso de Autenticação Única (Single Sign-On).</p>

     <p class="description">Esse plugin suporta o uso de shortcodes. Exemplo: <strong><samp>[cbmdf_oauth button_label="Autenticar com App"]</samp></strong></p>

     <form method="post" action="admin.php?page=cbmdf-oauth-options" novalidate="novalidate">
         <table class="form-table" role="presentation">
             <tbody>
                 <tr>
                     <th scope="row"><label for="client-id">Client ID</label></th>
                     <td><input name="client-id" type="text" id="client-id" value="<?php echo $cbmdf_oauth_client_id; ?>" class="regular-text">
                         <p class="description">O Client ID é um <strong>identificador público</strong> da sua aplicação.</p>
                     </td>
                 </tr>

                 <tr>
                     <th scope="row"><label for="client-secret">Client Secret</label></th>
                     <td><input name="client-secret" type="text" id="client-secret" value="<?php echo $cbmdf_oauth_client_secret; ?>" class="regular-text">
                         <p class="description">O Client Secret é um código de segurança conhecido apenas pela a aplicação e o servidor de autorizção.</p>
                     </td>
                 </tr>

                 <tr>
                     <th scope="row"><label for="authorize-uri">Authorize URI</label></th>
                     <td><input name="authorize-uri" type="text" id="authorize-uri" value="<?php echo $cbmdf_oauth_authorize_uri; ?>" placeholder="e.g. https://sistemas.cbm.df.gov.br/sistemas/cerberusAuth/public/oauth/authorize" class=" large-text">
                         <p class="description">Endereço para o qual o usuário será redirecionado para realizar a autenticação e autorização.</p>
                     </td>
                 </tr>

                 <tr>
                     <th scope="row"><label for="token-uri">Token URI</label></th>
                     <td><input name="token-uri" type="text" id="token-uri" value="<?php echo $cbmdf_oauth_token_uri; ?>" placeholder="e.g. https://sistemas.cbm.df.gov.br/sistemas/cerberusAuth/public/oauth/token" class="large-text">
                         <p class="description">Endereço para obter o token de acesso.</p>
                     </td>
                 </tr>

                 <tr>
                     <th scope="row"><label for="resource-uri">Resource URI</label></th>
                     <td><input name="resource-uri" type="text" id="resource-uri" value="<?php echo $cbmdf_oauth_resource_uri; ?>" placeholder="e.g. https://sistemas.cbm.df.gov.br/sistemas/cerberusAuth/public/oauth/resource" class=" large-text">
                         <p class="description">Endereço para obter informações sobre o usuário.</p>
                     </td>
                 </tr>


                 <tr>
                     <th scope="row"><label for="logout-uri">Logout URI</label></th>
                     <td><input name="logout-uri" type="text" id="logout-uri" value="<?php echo $cbmdf_oauth_logout_uri; ?>" placeholder="e.g. https://sistemas.cbm.df.gov.br/sistemas/cerberusAuth/public/index/sair" class=" large-text">
                         <p class="description">Aqui você deve informar a URL para efetuar o logout no servidor OAuth. Alguns servidores implementam o recurso para redirecionar
                             após o logout, se este for o caso você deve especificar manualmente o endereço para o qual deseja retornar.</p>
                         <p>Exemplo:<code>https://sistemas.cbm.df.gov.br/logout/<strong>?redirect_to=</strong>http://www.cbm.df.gov.br</code></p>


                     </td>
                 </tr>

                 <tr>
                     <th scope="row"><label for="redirect-uri">Redirect URI</label></th>
                     <td><input name="redirect-uri" type="text" readonly="readonly" id="redirect-uri" value="<?= $cbmdf_oauth_redirect_uri ?>" class=" large-text">
                         <p class="description">Endereço de redirecionamento após obter o token de acesso.</p>
                     </td>
                 </tr>




             </tbody>
         </table>

         <hr />

         <h2>Aparência do Widget</h2>

         <table class="form-table" role="presentation">
             <tbody>

                 <tr>
                     <th scope="row"><label for="custom-class">Classes Adicionais</label></th>
                     <td><input type="text" name="custom-class" class="regular-text" type="text" id="custom-class" value="<?php echo $cbmdf_oauth_custom_class ?>">
                         <p class="description">Informar as classes que serão associadas ao atributo <i><strong>class</strong></i> do botão. Separar por espaço.
                         </p>
                         <p>Exemplo:
                             <strong><code>
                                     button-secondary oauth-button
                                 </code></strong>
                         </p>
                     </td>
                 </tr>

                 <tr>
                     <th scope="row"><label for="custom-css">CSS Personalizado</label></th>
                     <td><textarea cols="80" rows="10" name="custom-css" type="text" id="custom-css"><?php echo $cbmdf_oauth_custom_css ?></textarea>
                         <p class="description">Informar apenas o código CSS que será incluido no atributo <strong>style</strong> do botão.</p>
                         <p>Exemplo:
                             <strong><code>
                                     .oauth-button{ padding: 4px 10px !important; border-radius: 5px !important; font-size: 90%; line-height: 20px; text-transform: none !important;}
                                 </code></strong>
                         </p>

                     </td>
                 </tr>


                 <tr>
                     <th scope="row"><label for="button-icon">Ícone do Font Awesome (Free) </label></th>
                     <td><input type="text" class="regular-text" name="button-icon" type="text" id="button-icon" value="<?php echo $cbmdf_oauth_button_icon ?>">
                         <p class="description">Consulte os ícones disponíveis em:
                             <a href="https://fontawesome.com/icons?d=gallery&m=free" target="_blank"><strong>Font Awesome</strong></a>.
                             <p>Exemplo:
                                 <strong><code>fas fa-address-card</code></strong>
                             </p>

                     </td>
                 </tr>
             </tbody>
         </table>

         <?php submit_button('Salvar alterações', 'primary', 'btn-save-cbmdf-oauth-settings'); ?>
     </form>
 </div>