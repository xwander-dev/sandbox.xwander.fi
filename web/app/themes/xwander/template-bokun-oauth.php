<?php
/* Template Name: Bokun OAuth */

get_header();

$access_token = get_option('bokun_access_token');

if ($access_token) {
    echo '<h2>The application is already authorized.</h2>';
    echo '<p>If you need to reauthorize, <a href="' . home_url('/bokun-oauth/?action=authorize') . '">click here</a>.</p>';
} elseif (isset($_GET['action']) && $_GET['action'] === 'authorize') {
    redirect_to_bokun_auth();
} elseif (isset($_GET['code'])) {
    echo '<h2>Authorization Code Received</h2>';
    echo '<p>Authorization Code: ' . esc_html($_GET['code']) . '</p>';
    handle_bokun_callback();
} else {
    echo '<h2>Invalid request.</h2>';
    echo '<p>Please <a href="' . home_url('/bokun-oauth/?action=authorize') . '">authorize the application</a>.</p>';
}

get_footer();

function redirect_to_bokun_auth() {
    $client_id = '46805';
    $redirect_uri = urlencode(home_url('/bokun-oauth/'));
    $auth_url = "https://bokun.io/oauth/authorize?client_id={$client_id}&response_type=code&redirect_uri={$redirect_uri}";

    wp_redirect($auth_url);
    exit;
}

function handle_bokun_callback() {
    $client_id = '46805';
    $client_secret = '998e4d95b709437ea13c03ae7da42ed5';
    $code = $_GET['code'];
    $redirect_uri = home_url('/bokun-oauth/');

    $response = wp_remote_post('https://bokun.io/oauth/token', array(
        'body' => array(
            'grant_type' => 'authorization_code',
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'code' => $code,
            'redirect_uri' => $redirect_uri
        )
    ));

    if (is_wp_error($response)) {
        echo '<h2>Error retrieving access token.</h2>';
        echo '<p>Error: ' . esc_html($response->get_error_message()) . '</p>';
        return;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (isset($data['access_token'])) {
        update_option('bokun_access_token', $data['access_token']);
        echo '<h2>Authorization successful!</h2>';
        echo '<p>Access Token: ' . esc_html($data['access_token']) . '</p>';
    } else {
        echo '<h2>Error retrieving access token.</h2>';
        echo '<p>Response: ' . esc_html($body) . '</p>';
    }
}
?>