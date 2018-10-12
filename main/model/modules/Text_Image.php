<?php

class TextImage extends TFD_Field_Group
{
    public $attributes = [
        'text',
        'image',
        'image_position'
    ];


    public static function fields($namespace = null)
    {
        $globalKey = Self::prefixForKey(Self::getKey(), $namespace);
        $fieldsBuilder = self::fieldsBuilder();

        $fieldsBuilder
            ->addGroup($globalKey)

            ->addWysiwyg('text')
            ->setRequired()

            ->addImage('image')
            ->setRequired()


            ->addRadio('image_alignment')
            ->addChoice('left', 'Left')
            ->addChoice('right', 'Right')
            ->setDefaultValue('left')
            ->setRequired()

            ->endGroup();

        return $fieldsBuilder;
    }
}