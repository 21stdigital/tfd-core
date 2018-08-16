<?php

namespace TFD;

class ACFUtility
{
    public static function get_address($id = null, $prefix = '')
    {
        $data = [
            'street' => ACF::get_field('address_street', $id, $prefix),
            'zipcode' => ACF::get_field('address_zipcode', $id, $prefix),
            'city' => ACF::get_field('address_city', $id, $prefix),
        ];

        return $data;
    }
}
