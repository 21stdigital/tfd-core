
<?php

class ModuleRepeater extends TFD_Field_Group
{
    public $attributes = [
        'modules'
    ];

    public $virtual = [
        'items'
    ];

    public function _filterItems($items)
    {
        $modules = $this->modules;

        $items = [];
        foreach ($modules as $index => $module) {
            $moduleClass = __NAMESPACE__ . '\\' . $module;
            if (class_exists($moduleClass)) {
                $prefix = Self::keyWithPrefix('modules', [$this->prefix, $index]);
                $items[] = new $moduleClass($this->model, $prefix);
            } else {
                error_log('MODULE CLASS NOT EXISTS: ' . $moduleClass);
            }
        }
        return $items;
    }

    public static function fields($namespace = null)
    {
        $globalKey = Self::keyWithPrefix(Self::getKey(), $namespace);
        $fieldsBuilder = self::fieldsBuilder();

        $fieldsBuilder
            ->addGroup($globalKey)

            ->addFlexibleContent('modules', [
                'button_label' => esc_html__('Add Content', 'tfd'),
                'layout' => 'block',
            ])
            ->addLayout(Text::fields())
            ->addLayout(TextImage::fields())

            ->endFlexibleContent()

            ->endGroup();

        return $fieldsBuilder;
    }
}