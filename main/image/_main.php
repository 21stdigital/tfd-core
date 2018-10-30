<?php

namespace TFD;

use App;
use TFD\Media_Sources as Sources;

require_once dirname(__FILE__) . '/media_queries.php';

class Image
{
    protected static $_instance = null;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    protected function __clone()
    {
    }

    protected function __construct()
    {
    }


    public static function get_density_sizes($density, $name, $width, $height, $crop = true)
    {
        $res = [];
        for ($i = 1; $i <= $density; ++$i) {
            $suffix = ($i > 1) ? '@' . $i . 'x' : '';
            $res[] = [
                $name . $suffix,
                $width * $i,
                $height * $i,
                $crop,
            ];
        }

        return $res;
    }

    public static function add_size($name, $width = 0, $height = 0, $crop = true)
    {
        if (function_exists('fly_add_image_size')) {
            fly_add_image_size($name, $width, $height, $crop);
        } else {
            add_image_size($name, $width, $height, $crop);
        }
    }

    public static function add_sizes($sizes)
    {
        foreach ($sizes as $index => $size) {
            if (is_array($size) && count($size) == 4) {
                self::add_size($size[0], $size[1], $size[2], $size[3]);
            }
        }

    }


    /**
     * Get size information for all currently-registered image sizes.
     *
     * @global $_wp_additional_image_sizes
     * @uses   get_intermediate_image_sizes()
     * @return array $sizes Data for all currently-registered image sizes.
     */
    private static function get_image_sizes()
    {
        global $_wp_additional_image_sizes;

        $sizes = array();

        foreach (get_intermediate_image_sizes() as $_size) {
            if (in_array($_size, array('thumbnail', 'medium', 'medium_large', 'large'))) {
                $sizes[$_size]['width'] = get_option("{$_size}_size_w");
                $sizes[$_size]['height'] = get_option("{$_size}_size_h");
                $sizes[$_size]['crop'] = (bool)get_option("{$_size}_crop");
            } elseif (isset($_wp_additional_image_sizes[$_size])) {
                $sizes[$_size] = array(
                    'width' => $_wp_additional_image_sizes[$_size]['width'],
                    'height' => $_wp_additional_image_sizes[$_size]['height'],
                    'crop' => $_wp_additional_image_sizes[$_size]['crop'],
                );
            }
        }

        return $sizes;
    }

    /**
     * Get size information for a specific image size.
     *
     * @uses   get_image_sizes()
     * @param  string $size The image size for which to retrieve data.
     * @return bool|array $size Size data about an image size or false if the size doesn't exist.
     */
    private static function get_image_size($size)
    {
        $sizes = Self::get_image_sizes();

        if (isset($sizes[$size])) {
            return $sizes[$size];
        }

        return false;
    }

    /**
     * Get the width of a specific image size.
     *
     * @uses   get_image_size()
     * @param  string $size The image size for which to retrieve data.
     * @return bool|string $size Width of an image size or false if the size doesn't exist.
     */
    private static function get_image_width($size)
    {
        if (!$size = Self::get_image_size($size)) {
            return false;
        }

        if (isset($size['width'])) {
            return $size['width'];
        }

        return false;
    }

    /**
     * Get the height of a specific image size.
     *
     * @uses   get_image_size()
     * @param  string $size The image size for which to retrieve data.
     * @return bool|string $size Height of an image size or false if the size doesn't exist.
     */
    private static function get_image_height($size)
    {
        if (!$size = Self::get_image_size($size)) {
            return false;
        }

        if (isset($size['height'])) {
            return $size['height'];
        }

        return false;
    }

    private static function get_attachment_image_src($attachment_id, $size = 'thumbnail')
    {
        if (function_exists('fly_get_attachment_image_src')) {
            $image = fly_get_attachment_image_src($attachment_id, $size);
            if (isset($image) && !empty($image)) {
                return [
                    'src' => $image['src'],
                    'width' => $image['width'],
                    'height' => $image['height'],
                ];
            }
        } else {
            $image = wp_get_attachment_image_src($attachment_id, $size);
            if (isset($image) && !empty($image)) {
                return [
                    'src' => $image[0],
                    'width' => $image[1],
                    'height' => $image[2],
                ];
            }
        }

        return null;
    }

    private static function get_attachment_fields($attachment_id = null, $size = '', $class = ['ResponsiveImage'])
    {
        $attachment_id = isset($attachment_id) ? (int)$attachment_id : get_post_thumbnail_id();
        $attachment = isset($attachment_id) ? get_post($attachment_id) : null;

        if (!isset($attachment) || $attachment->post_type != 'attachment') {
            return null;
        }

        $width = wp_get_attachment_image_src($attachment_id, $size)[1];
        $height = wp_get_attachment_image_src($attachment_id, $size)[2];
        $orienation = 'landscape';
        if ($width < $height) {
            $orienation = 'portrait';
        } elseif ($width == $height) {
            $orienation = 'square';
        }

        return [
            'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
            'caption' => $attachment->post_excerpt,
            'description' => $attachment->post_content,
            'href' => get_permalink($attachment->ID),
            'src' => wp_get_attachment_image_src($attachment_id, $size)[0],
            'guid' => $attachment->guid,
            'title' => $attachment->post_title,
            'width' => $width,
            'height' => $height,
            'class' => $class ? implode(' ', $class) : 'Image',
            'caption_enabled' => true,
            'id' => $attachment->ID,
            'focal_point' => self::get_focal_point($attachment_id),
            'orientation' => $orienation,
        ];
    }

    public static function get_featured_image($size = '', $post_id = null, $class = ['ResponsiveImage'])
    {
        $post_thumbnail_id = get_post_thumbnail_id($post_id);

        return $post_thumbnail_id ? self::get_attachment($post_thumbnail_id, $size, $class) : null;
    }

    public static function get_focal_point($attachment_id)
    {
        $focal_point = get_post_meta($attachment_id, 'theiaSmartThumbnails_position', true);

        $x = isset($focal_point) && !empty($focal_point) ? $focal_point[0] : .5;
        $y = isset($focal_point) && !empty($focal_point) ? $focal_point[1] : .5;

        return [
            'x' => $x,
            'y' => $y,
            'bg_pos' => $x * 100 . '% ' . $y * 100 . '%',
            'bg_pos_x' => $x * 100 . '%',
            'bg_pos_y' => $y * 100 . '%',
        ];
    }

    public static function get_attachment($attachment_id, $size = '', $class = ['ResponsiveImage'])
    {
        $image = self::get_attachment_fields($attachment_id, $size, $class);

        if (!$image) {
            return null;
        }
        if ($size) {
            $media_sources = Sources\get_sources();
            $sources = array();
            $index = 0;

            $size_classes = App\sanatize_image_sizes();

            $metadata = wp_get_attachment_metadata($attachment_id);

            if (false && isset($metadata['cloudinary_data']) && isset($metadata['cloudinary_data']['public_id'])) {
                foreach ($media_sources as $key => $media_source) {
                    if ($key == 'default') {
                        $image_size_class = $size . '_default';
                        d_log($image_size_class);
                        if (isset($size_classes[$image_size_class])) {
                            $size_class = $size_classes[$image_size_class];

                            $image['src'] = "https://res.cloudinary.com/tfd/image/upload/c_fill,g_auto,w_" . $size_class->width . ",h_" . $size_class->height . ",f_auto/v1540307228/" . $metadata['cloudinary_data']['public_id'];
                            $image['width'] = $size_class->width;
                            $image['height'] = $size_class->height;
                        }
                    } else {
                        $media_query = $media_source['media'];
                        $srcset = '';

                        $lastElement = end($media_source['src']);
                        foreach ($media_source['src'] as $srcset_postfix => $density) {
                            $image_size_class = $size . '_' . $srcset_postfix;
                            d_log($image_size_class);
                            if (isset($size_classes[$image_size_class])) {
                                $size_class = $size_classes[$image_size_class];
                                if (isset($size_class)) {
                                    $srcset .= "https://res.cloudinary.com/tfd/image/upload/c_fill,g_auto,w_" . $size_class->width . ",h_" . $size_class->height . ",f_auto/v1540307228/" . $metadata['cloudinary_data']['public_id'] . ' ' . $density;
                                    $srcset .= ($lastElement === $density) ? '' : ', ';
                                }
                            }
                        }

                        if ($srcset) {
                            $sources[] = array(
                                'srcset' => $srcset,
                                'media' => $media_query,
                            );
                        }
                    }
                }
                d_log($sources, 'SOURCES');
            } else {
                foreach ($media_sources as $key => $media_source) {
                    if ($key == 'default') {
                        $image_data = self::get_attachment_image_src($attachment_id, $size . '_default');
                        if ($image_data && is_array($image_data) && count($image_data) && array_key_exists('src', $image_data)) {
                            $image['src'] = $image_data['src'];
                            $image['width'] = $image_data['width'];
                            $image['height'] = $image_data['height'];
                        }
                    } else {
                        $media_query = $media_source['media'];
                        $srcset = '';

                        $lastElement = end($media_source['src']);
                        foreach ($media_source['src'] as $srcset_postfix => $density) {
                            $image_data = self::get_attachment_image_src($attachment_id, $size . '_' . $srcset_postfix);

                            if (isset($image_data) && count($image_data) && array_key_exists('src', $image_data)) {
                                $srcset .= $image_data['src'] . ' ' . $density;
                                $srcset .= ($lastElement === $density) ? '' : ', ';
                            }
                        }

                        if ($srcset) {
                            $sources[] = array(
                                'srcset' => $srcset,
                                'media' => $media_query,
                            );
                        }
                    }

                    ++$index;
                }
            }

            $image['sources'] = $sources;
        }

        return array_key_exists('src', $image) && $image['src'] ? $image : null;
    }
}
$image = Image::getInstance();
