<?php

namespace TFD;

class ACF
{
    public static function get_prefix_with_index($key = null, $index = null, $prefix = null)
    {
        if (empty($key) && empty($prefix) && !isset($index)) {
            return '';
        }

        $key = preg_replace('/_$/', '', $key);
        $prefix = preg_replace('/_$/', '', $prefix);

        if (!empty($key)) {
            $key = $key.'_';
            if (isset($index)) {
                $key = $key.$index.'_';
            }
        }

        if (!empty($prefix)) {
            $prefix = $prefix.'_';
        }

        return $key.$prefix;
    }

    public static function get_prefix($key = null, $prefix = null)
    {
        return self::get_prefix_with_index($key, null, $prefix);
    }

    public static function get_field($field, $id = null, $prefix = null)
    {
        if (!post_password_required()) {
            $prefix = self::prefix($prefix);
            if ($prefix) {
                $prefix = $prefix.'_';
            }

            $id = $id ?: get_the_id();

            if ($id === 'option') {
                $prefix = 'options_'.$prefix;
                $value = get_option($prefix.$field);
            } else {
                $value = get_post_meta($id, $prefix.$field, true);
            }

            return $value;
        }

        return null;
    }

    public static function prefix($prefix = null, $separator = false)
    {
        if (is_null($prefix) || $prefix == '') {
            return '';
        }

        if (!is_array($prefix) && $prefix) {
            $prefix = [$prefix];
        }

        $empty_prefix_removed = [];
        foreach ($prefix as $p) {
            if (!is_null($p) && $p !== '') {
                $empty_prefix_removed[] = $p;
            }
        }
        $prefix = $empty_prefix_removed;

        $prefix = empty($prefix) ? '' : implode('_', $prefix);
        $prefix .= ($separator) ? '_' : '';

        return $prefix;
    }

    public static function get_value($field_key, $fields, $prefix = '')
    {
        if ($prefix && substr($prefix, -1) !== '_') {
            $prefix = $prefix.'_';
        }

        return array_key_exists($prefix.$field_key, $fields) ? $fields[$prefix.$field_key] : null;
    }

    public static function get_fields($id = null)
    {
        $id = $id ?: get_the_id();

        $all_fields = get_post_meta($id);
        $fields = [];
        foreach ($all_fields as $key => $value) {
            if (substr($key, 0, 1) !== '_') {
                $fields[$key] = is_array($value) ? $value[0] : $value;
            }
        }

        return ($id === 'option') ? wp_load_alloptions() : $fields;
    }

    public static function get_field_with_prefix($field, $prefix = null, $id = null)
    {
        $field = get_key_with_prefix($field, $prefix);

        return get_field($field, $id);
    }

    public static function get_key_with_prefix($key, $prefix)
    {
        return $prefix ? get_prefix(null, $prefix).$key : $key;
    }

    public static function get_repeater_args($key, $id = null)
    {
        $id = $id ?: get_the_id();
        $count = self::get_field($key, $id);
        if ($count) {
            for ($i = 0; $i < $count; ++$i) {
            }
        }
    }
}

if (defined('GOOGLE_API_KEY')) {
    add_action('acf/init', function () {
        acf_update_setting('google_api_key', GOOGLE_API_KEY);
    });
}
