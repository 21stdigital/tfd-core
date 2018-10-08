<?php

abstract class TFD_Field_Group implements JsonSerializable
{
    public $host_ID;
    public $model;

    public $attributes = [];
    public $prefix = '';

    public function __construct(TFD_Model $model)
    {
        $this->model = $model;
    }

    /**
     * Returns an array representaion of the model for serialization
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }



	// -----------------------------------------------------
	// Meta
    // -----------------------------------------------------
    public function getMetaKey($key)
    {
        return $this->prefix . $key;
    }

    /**
     * Returns meta value for a meta key
     *
     * @param  string meta_key
     * @return string
     */
    public function getMeta($key)
    {
        return get_post_meta($this->Host_ID, $this->getMetaKey($key), true);
    }

    /**
     * Delete meta's meta
     *
     * @param  string meta_key
     * @return void
     */
    public function deleteMeta($key)
    {
        delete_post_meta($this->Host_ID, ($this->prefix . $key));
    }




	// -----------------------------------------------------
	// Fields
    // -----------------------------------------------------
    public function repeater($key, $call)
    {
        $count = (int)$this->getMeta($key);
        if (!$count) return null;

        $repeater = [];
        for ($i = 0; $i < $count; $i++) {
            $repeater[] = $call()
        }
        return $repeater;
    }

    public function flexibleContent($key, $call)
    {
        $items = (int)$this->getMeta($key);
        if (!$items) return null;

        $res = array_map(function ($item) use ($call) {
            $call($item);
        }, $items);
    }



    /**
     * Returns an asoc array representaion of the model
     *
     * @return array
     */
    public function toArray()
    {
        $model = [];

        $model['host_ID'] = $this->host_ID;

        return $model;
    }


	// ----------------------------------------------------
	// FINDERS
	// ----------------------------------------------------
    /**
     * Find model by it's host ID
     *
     * @param  int $ID
     * @return Object|NULL
     */
    public static function find($ID)
    {
        if (Self::exists($ID)) {
            $class = Self::newInstance();
            $class->ID = $ID;
            $class->boot();
            return $class;
        }

        return null;
    }
}

