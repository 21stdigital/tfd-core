<?php

namespace TFD;

class Transient
{
    public static function get_expiration()
    {
        return defined('TRANSIENT_EXPIRATION') ? TRANSIENT_EXPIRATION : 43200;
    }

    public static function is_active()
    {
        $transient = defined('TRANSIENT') ? TRANSIENT : false;
        if (!$transient) {
            return false;
        }

        if (is_user_logged_in()) {
            return defined('TRANSIENT_ADMIN') ? TRANSIENT_ADMIN : false;
        }

        return true;
    }

    private static function make_key($key)
    {
        return 'TFD_'.$key;
    }

    public static function get_value($key, $func, $expiration = null)
    {
        $key = self::make_key($key);
        $expiration = $expiration ?: self::get_expiration();
        // get the transient
        $value = self::is_active() ? get_transient($key) : null;

        if (is_null($value)) {//|| is_user_logged_in()) {
            $value = $func();
            self::is_active() ? set_transient($key, $value, $expiration) : delete_transient($key);
        }

        return $value;
    }

    public static function get_buffered_value($key, $func, $expiration = null)
    {
        $key = self::make_key($key);
        $expiration = $expiration ?: self::get_expiration();
        // get the transient
        $value = self::is_active() ? get_transient($key) : null;

        if (is_null($value)) {//|| is_user_logged_in()) {
            ob_start();
            $func();
            $value = ob_get_contents();
            ob_end_clean();
            self::is_active() ? set_transient($key, $value, $expiration) : delete_transient($key);
        }

        return $value;
    }

    public static function get_keys($stack)
    {
        return get_transient($stack);
    }

    public static function add_key($key, $stack)
    {
        $keys = get_transient($stack) ?: array();
        $keys = is_array($keys) ? $keys : array();
        if (!in_array($key, $keys)) {
            $keys[] = $key;
            $key = self::make_key($stack);
            set_transient($stack, $keys);
        }
    }

    public static function delete_key_stack($stack)
    {
        delete_transient($stack);
    }

    public static function delete_transients($keys)
    {
        $keys = is_array($keys) ? $keys : array($keys);
        foreach ($keys as $key) {
            $key = self::make_key($key);
            delete_transient($key);
        }
    }
}
