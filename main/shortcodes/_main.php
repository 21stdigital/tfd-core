<?php

namespace TFD;

/*
 * Hide email from Spam Bots using a shortcode.
 *
 * @param array  $atts    Shortcode attributes. Not used.
 * @param string $content The shortcode content. Should be an email address.
 *
 * @return string The obfuscated email address.
 */
add_shortcode('email', function ($atts, $content = null) {
    if (!is_email($content)) {
        return;
    }

    $class = 'Email';
    if ($atts && is_array($atts) && array_key_exists('class', $atts)) {
        $class .= ' '.$atts['class'];
    }

    $email = antispambot($content);
    $email_link = 'mailto:'.$email;

    return sprintf('<a href="%1$s" class="%2$s">%3$s</a>', esc_url($email_link, array('mailto')), esc_attr($class), esc_html($email));
});
