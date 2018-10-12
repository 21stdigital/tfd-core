
<?php

class Text extends TFD_Field_Group
{
    public $attributes = [
        'text'
    ];

    public static function fields($namespace = null)
    {
        $globalKey = Self::prefixForKey(Self::getKey(), $namespace);
        $fieldsBuilder = self::fieldsBuilder();

        $fieldsBuilder
            ->addGroup($globalKey)

            ->addWysiwyg('text')
            ->setRequired()

            ->endGroup();

        return $fieldsBuilder;
    }
}