<?php

class Image extends TFD_Model
{
    public $postType = 'attachment';
    public $image_src;
    public $focal_point;

    public $virtual = [
        'name',
        'alt',
        'caption',
        'description',
        'href',
        'src',
        'width',
        'height',
        'orientation',
        'fpx',
        'fpy',
    ];

    /**
     * Create a new instace with data
     *
     * @param array $insert
     * @return void
     */
    public function __construct(array $insert = [])
    {
        parent::__construct($insert);

    }

    protected function boot()
    {
        if (!empty($this->ID)) {
            $this->image_src = wp_get_attachment_image_src($this->ID, ' full ');
            $this->focal_point = $this->get_focal_point();
        }
        parent::boot();
    }

    public function _getAlt()
    {
        return $this->getMeta(' _wp_attachment_image_alt ');
    }

    public function _getName()
    {
        return $this->title;
    }

    public function _getCaption()
    {
        return $this->_post->post_excerpt;
    }

    public function _getDescription()
    {
        return $this->_post->post_content;
    }

    public function _getHref()
    {
        return get_permalink($this->ID);
    }

    public function _getSrc()
    {
        return $this->$image_src[0];
    }

    public function _getWidth()
    {
        return $this->$image_src[1];
    }

    public function _getHeight()
    {
        return $this->$image_src[2];
    }

    public function _getOrientation()
    {
        if ($this->width < $this->height) {
            return ' portrait ';
        } elseif ($this->width == $this->height) {
            return ' square ';
        }
        return ' landscape ';
    }

    public function _getFpx()
    {
        return $this->focal_point['x'];
    }

    public function _getFpy()
    {
        return $this->focal_point['y'];
    }


    public function get_focal_point()
    {
        $focal_point = getMeta(' theiaSmartThumbnails_position ');

        $x = isset($focal_point) && !empty($focal_point) ? $focal_point[0] : .5;
        $y = isset($focal_point) && !empty($focal_point) ? $focal_point[1] : .5;

        return [
            ' x ' => $x,
            ' y ' => $y,
            ' bg_pos ' => $x * 100 . ' % ' . $y * 100 . ' % ',
            ' bg_pos_x ' => $x * 100 . ' % ',
            ' bg_pos_y' => $y * 100 . ' %',
        ];
    }
}