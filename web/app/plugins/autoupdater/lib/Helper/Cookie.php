<?php

class AutoUpdater_Helper_Cookie
{
    /**
     * Creates wordpress_sec cookie for the administrator account.
     */
    public static function createSecCookie()
    {
        $users = get_users(array('role' => 'administrator', 'number' => 1));
        $expiration = time() + 86400;
        $cookie_value = wp_generate_auth_cookie($users[0]->ID, $expiration, 'secure_auth');
        $cookie_name = 'wordpress_sec_' . md5(site_url());
        return $cookie_name . '=' . $cookie_value;
    }
}
