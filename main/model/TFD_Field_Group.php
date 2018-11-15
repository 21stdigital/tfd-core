<?php

abstract class TFD_Field_Group implements JsonSerializable
{
    public $model;

    public $attributes = [];
    public $prefix = '';

    public $default = [];
    public $virtual = [];
    public $filter = [];
    public $protected = [];
    public $serialize = [];

    public function __construct(TFD_Model $model, $prefix = '')
    {
        $this->model = $model;
        $this->prefix = $prefix;
        $this->boot();
    }


    /**
     * Initalize the model, load in any addional data
     *
     * @return void
     */
    protected function boot()
    {
        if (!empty($this->model)) {
            $this->new = false;
            $this->_post = get_post($this->ID);

            foreach ($this->attributes as $attribute) {
                $meta = $this->getMeta($attribute);
                if (empty($meta) && isset($this->default[$attribute])) {
                    $this->set($attribute, $this->default[$attribute]);
                } else {
                    $this->set($attribute, $meta);
                }
            }
        }

        $this->booted = true;
        $this->triggerEvent('booted');
    }




	// -----------------------------------------------------
	// UTILITY METHODS
	// -----------------------------------------------------
    /**
     * Create a new model without calling the constructor.
     *
     * @return object
     */
    protected static function newWithoutConstructor()
    {
        $class = get_called_class();
        $reflection = new ReflectionClass($class);
        return $reflection->newInstanceWithoutConstructor();
    }

    public function isArrayOfModels($array)
    {
        if (!is_array($array)) {
            return false;
        }

        $types = array_unique(array_map('gettype', $array));
        return (count($types) === 1 && $types[0] === "object" && $array[0] instanceof TFD_Field_Group);
    }

    public static function getKey()
    {
        return get_called_class();
    }

    public static function keyWithPrefix($key, $prefix = null)
    {
        if (!$prefix) return $key;
        if (is_array($prefix)) {
            $prefix = array_reduce($prefix, function ($carry, $item) {
                $carry = $carry . $item . '_';
                return $carry;
            });
        } else {
            $prefix = $prefix ? $prefix . '_' : '';
        }
        return $prefix . $key;
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

    /**
     * Returns TRUE if $attribute is in the $virtual array
     * and has a corresponding vitaul property method
     *
     * @param  string $attribute
     * @return bool
     */
    public function isVirtualProperty($attribute)
    {
        return (isset($this->virtual) &&
            in_array($attribute, $this->virtual) &&
            method_exists($this, ('_get' . ucfirst($attribute))));
    }

    /**
     * Calls virtual property method
     *
     * @param  string $attribute
     * @return mixed
     */
    public function getVirtualProperty($attribute)
    {
        return call_user_func([$this, ('_get' . ucfirst($attribute))]);
    }

    /**
     * Returns TRUE if $attribute is in the $filter array
     * and has a corresponding filter property method
     * OR
     * Returns TRUE if $attribute is in the $filter array
     * and the $filter array is an asoc array (:318)
     * and the value corresponding to the key ($attribute) has is the name of an exiting function.
     *
     * @param  string $attribute
     * @return bool
     */
    public function isFilterProperty($attribute)
    {
        return ((isset($this->filter) &&
            is_array($this->filter) &&
            in_array($attribute, $this->filter) &&
            method_exists($this, ('_filter' . ucfirst($attribute)))) || (isset($this->filter) &&
            is_array($this->filter) &&
            count(array_filter(array_keys($this->filter), 'is_string')) > 0 &&
            in_array($attribute, array_keys($this->filter)) &&
            isset($this->filter[$attribute]) && (function_exists($this->filter[$attribute]) ||
            $this->filter[$attribute] === 'the_content' ||
            class_exists($this->filter[$attribute]))));
    }

    /**
     * Calls filter property method
     *
     * @param  string $attribute
     * @return mixed
     */
    public function getFilterProperty($attribute)
    {
        if (count(array_filter(array_keys($this->filter), 'is_string')) > 0 &&
            isset($this->filter[$attribute])) {

            if ($this->filter[$attribute] === 'the_content') {
                return apply_filters('the_content', $this->get($attribute));
            } elseif (function_exists($this->filter[$attribute])) {
                return ($this->filter[$attribute]($this->get($attribute)));
            } elseif (class_exists($this->filter[$attribute])) {

                $className = $this->filter[$attribute];
                if (is_array($this->get($attribute))) {
                    if ($this->isArrayOfModels($this->get($attribute))) {
                        return $this->get($attribute);
                    }

                    $return = [];
                    foreach ($this->get($attribute) as $model) {
                        if ($className::exists($model)) {
                            $return[] = $className::find($model);
                        }
                    }

                    return $this->{$attribute} = &$return;
                } else {
                    if (is_object($this->get($attribute))) {
                        return $this->get($attribute);
                    }
                    return $this->{$attribute} = $className::find($this->get($attribute));
                }
            }

            return null;
        }

        return call_user_func_array([$this, ('_filter' . ucfirst($attribute))], [$this->get($attribute)]);
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
	// GETTERS & SETTERS
	// -----------------------------------------------------
    /**
     * Get property of model or $default
     *
     * @param  property $attribute [description]
     * @param  property $default
     * @return mixed
     *
     * @todo  investagte this method
     */
    public function get($attribute, $default = null)
    {
        switch ($attribute) {
            default:
                if (isset($this->data[$attribute])) {
                    return $this->data[$attribute];
                } else {
                    return $default;
                }
                break;
        }
    }

    /**
     * Set propert of the model
     *
     * @param string $attribute
     * @param string $value
     * @return void
     */
    public function set($attribute, $value)
    {
        if (in_array($attribute, $this->getAttributes())) {
            $this->data[$attribute] = $value;
        }
    }

	// -----------------------------------------------------
	// MAGIC METHODS
	// -----------------------------------------------------
    /**
     * @return void
     */
    public function __set($attribute, $value)
    {
        if ($this->booted) {
            $this->dirty = true;
        }

        if (in_array($attribute, $this->getAttributes())) {
            $this->set($attribute, $value);
        }
    }

    /**
     * @return void
     */
    public function __get($attribute)
    {
        if (in_array($attribute, $this->getAttributes())) {
            if ($this->isFilterProperty($attribute)) {
                return $this->getFilterProperty($attribute);
            }

            return $this->get($attribute);
        } else if ($this->isVirtualProperty($attribute)) {
            return $this->getVirtualProperty($attribute);
        } else if ($attribute === 'HostID') {
            return $this->model->ID;
        } else if ($attribute === 'key') {
            return Self::getKey();
        } else if ($attribute === 'prefix') {
            return $this->prefix;
        }
    }



    /**
     * Returns an asoc array representaion of the model
     *
     * @return array
     */
    public function toArray()
    {
        $model = [];

        foreach ($this->attributes as $key => $attribute) {
            if (!empty($this->protected) && !in_array($attribute, $this->protected)) {
				// Do not add to $model
            } elseif ($this->attribute instanceof TFD_Field_Group) {
                $model[$attribute] = $this->$attribute->toArray();
            } else {
                $model[$attribute] = $this->$attribute;
            }
        }

        if (!empty($this->serialize)) {
            foreach ($this->serialize as $key => $attribute) {
                if (!empty($this->protected) && !in_array($attribute, $this->protected)) {
					// Do not add to $model
                } elseif ($this->attribute instanceof TFD_Field_Group) {
                    $model[$attribute] = $this->$attribute->toArray();
                } else {
                    $model[$attribute] = $this->$attribute;
                }
            }
        }

        $model['HostID'] = $this->HostID;
        $model['key'] = $this->key;
        $model['prefix'] = $this->content;

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



	// -----------------------------------------------------
	// Custom Field Handling
    // -----------------------------------------------------
    public function repeater($key, $call)
    {
        $count = (int)$this->getMeta($key);
        if (!$count) return null;

        $repeater = [];
        for ($i = 0; $i < $count; $i++) {
            $repeater[] = $call();
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



	// -----------------------------------------------------
	// Custom Fields
    // -----------------------------------------------------
    public static function fieldsBuilder($config = [])
    {
        $defaults = [
            'style' => 'seamless',
        ];
        $config = array_merge($defaults, $config);

        return new FieldsBuilder(Self::getKey(), $config);
    }

    public static function fields($namespace = null)
    {
        $globalKey = Self::keyWithPrefix(Self::getKey(), $namespace);
        $fieldsBuilder = Self::fieldsBuilder();

        return $fieldsBuilder;
    }
}

