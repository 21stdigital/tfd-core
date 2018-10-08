
<?php

class Module_Repeater extends TFD_Field_Group
{
    public $attributes = [
        'items'
    ];



    public static function fields($config = [])
    {
        $class_name = get_called_class();

        $defaults = [
            'style' => 'seamless',
        ];

        $fields = new FieldsBuilder($class_name, array_merge($defaults, $config));
        $fields
            ->addFlexibleContent('modules')
            ->addRepeater('items', [
                'min' => 1,
                'button_label' => esc_html__('Add Module', 'tfd'),
                'layout' => 'block',
            ])
            ->addLayout(Text::fields())
            ->addLayout(TextImage::fields())
            ->endFlexibleContent();

        return $fields;
    }
}