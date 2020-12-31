<?php
$cbmdf_oauth_client_id = get_option("cbmdf_oauth_client_id");
$cbmdf_oauth_client_secret  = get_option("cbmdf_oauth_client_secret");
$cbmdf_oauth_redirect_uri  = get_option("cbmdf_oauth_redirect_uri");
$cbmdf_oauth_authorize_uri  = get_option("cbmdf_oauth_authorize_uri");
$cbmdf_oauth_token_uri  = get_option("cbmdf_oauth_token_uri");
$cbmdf_oauth_resource_uri  = get_option("cbmdf_oauth_resource_uri");
$cbmdf_oauth_logout_uri  = get_option("cbmdf_oauth_logout_uri");



$provider = new \League\OAuth2\Client\Provider\GenericProvider([
    'clientId'                => $cbmdf_oauth_client_id,    // The client ID assigned to you by the provider
    'clientSecret'            => $cbmdf_oauth_client_secret,    // The client password assigned to you by the provider
    'redirectUri'             => $cbmdf_oauth_redirect_uri,
    'urlAuthorize'            => $cbmdf_oauth_authorize_uri,
    'urlAccessToken'          => $cbmdf_oauth_token_uri,
    'urlResourceOwnerDetails' => $cbmdf_oauth_resource_uri
]);
