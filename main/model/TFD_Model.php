<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

abstract class TFD_Model extends WP_Model
{
    public $field_groups = [];



    /**
     * Register the post type using the propery $postType as the post type
     *
     * @param  array  $args  see: register_post_type()
     * @return void
     */
    public static function register($args = [])
    {
        $postType = Self::getPostType();

        $defaults = [
            'public' => true,
            'label' => ucfirst($postType)
        ];

        register_extended_post_type($postType, array_merge($defaults, $args));

        Self::addHooks();
    }

    public static function fields($config = [])
    {
        $class_name = get_called_class();

        $defaults = [
            'title' => 'Fields',
            'style' => 'seamless',
            'hide_on_screen' => [
                'permalink',
                'the_content',
                'excerpt',
                'discussion',
                'comments',
                'revisions',
                'slug',
                'author',
                'format',
                'page_attributes',
                'featured_image',
                'categories',
                'tags',
                'send-trackbacks',
            ]
        ];

        $fields = new FieldsBuilder($class_name, array_merge($defaults, $config));

        return $fields;
    }


    public static function registerFields()
    {
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group(Self::fields()->build());
        }
    }
}