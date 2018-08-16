<?php

namespace TFD;

abstract class CPT
{
    protected $id;
    protected $slug;
    protected $key;
    protected static $_instance = null;

    protected function __construct()
    {
    }

    final public static function getInstance()
    {
        static $instances = array();

        $calledClass = get_called_class();

        if (!isset($instances[$calledClass])) {
            $instances[$calledClass] = new $calledClass();
            $calledClass::init();
        }

        return $instances[$calledClass];
    }

    private function __clone()
    {
    }

    public function init()
    {
        add_action('init', array($this, 'register'));
        add_action('save_post_'.$this->id, array($this, 'on_save_post'));
    }

    public function register()
    {
        // $supports = [
        //     'title'
        //     'editor' (content)
        //     'author'
        //     'thumbnail' (featured image, current theme must also support post-thumbnails)
        //     'excerpt'
        //     'trackbacks'
        //     'custom-fields'
        //     'comments' (also will see comment count balloon on edit screen)
        //     'revisions' (will store revisions)
        //     'page-attributes' (menu order, hierarchical must be true to show Parent option)
        //     'post-formats' add post formats, see Post Formats
        // ]
    }

    public function on_save_post($post_id)
    {
        d_log($post_id);
        //TFD\Transient::delete_transients($this->key);
    }

    public function getID()
    {
        return $this->id;
    }

    protected function setId($value)
    {
        $this->id = $value;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    protected function setSlug($vlaue)
    {
        $this->slug = $value;
    }

    public function getKey()
    {
        return $this->key;
    }

    protected function setKey($value)
    {
        $this->key = $value;
    }
}
