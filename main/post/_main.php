<?php

namespace TFD;

class Post
{
    const ID = 'post';
    const SLUG = 'post';
    const KEY = 'post';

    public static function get_the_content($post_id = null)
    {
        $content_post = get_post($post_id);
        if (!$content_post) {
            return null;
        }
        $content = $content_post->post_content;
        $content = apply_filters('the_content', $content);
        $content = str_replace(']]>', ']]&gt;', $content);

        return $content;
    }

    public static function get_all($posts_per_page = -1, $paged = 1)
    {
        return Transient::get_value(self::KEY, function () use ($posts_per_page, $paged) {
            $args = [
                        'post_type' => self::SLUG,
                        'post_status' => 'publish',
                        'posts_per_page' => $posts_per_page,
                        'paged' => $paged,
                    ];

            return get_posts($args);
        });
    }

    public static function get_by_cat($cat_ids, $posts_per_page = -1, $paged = 1)
    {
        $cat_ids = (is_array($cat_ids)) ? $cat_ids : [$cat_ids];
        //return Transient::get_value(self::KEY, function () use ($cat_ids, $posts_per_page, $paged) {
        $args = [
                    'post_type' => self::SLUG,
                    'post_status' => 'publish',
                    'posts_per_page' => $posts_per_page,
                    'category__in' => $cat_ids,
                    'paged' => $paged,
                ];

        return get_posts($args);
        //});
    }

    public static function get_author($id = null)
    {
        $id = $id ?: get_the_id();
        $author_id = get_post_field('post_author', $id);

        return [
            'name' => get_the_author_meta('display_name', $author_id),
            'url' => esc_url(get_author_posts_url($author_id)),
        ];
    }

    public static function get_categories($id = null)
    {
        $id = $id ?: get_the_id();
        $category_objects = get_the_category($id);

        $res = [];
        foreach ($category_objects as $index => $category_object) {
            if ($category_object->cat_ID != 1) {
                $category = [
                    'name' => $category_object->cat_name,
                    'url' => esc_url(get_category_link($category_object->cat_ID)),
                ];

                $res[] = $category;
            }
        }

        return $res;
    }
}

add_action('save_post_'.Post::ID, function ($post_id) {
    d_log('Delete Posts Transients');
    Transient::delete_transients(Post::KEY);
});
