<?php

abstract class TFD_Front_Page extends TFD_Model
{
    public $postType = 'page';

    public function __construct(array $insert = [])
    {
        $this->ID = Self::getID();
        parent::__construct($insert);
    }

    public static function getID()
    {
        return get_option('page_on_front');
    }

    // ----------------------------------------------------
	// FINDERS
	// ----------------------------------------------------
    /**
     * Find model by it's post ID
     *
     * @param  int $ID
     * @return Object|NULL
     */
    public static function find()
    {
        $ID = Self::getID();
        return parent::find($ID);
    }

    /**
     * Get model by ID without booting the model
     *
     * @param  int $ID
     * @return Object|NULL
     */
    public static function findBypassBoot()
    {
        $ID = Self::getID();
        return parent::findBypassBoot($ID);
    }
}