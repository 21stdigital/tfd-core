<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

abstract class TFD_Template extends WP_Model
{
    /**
     * Returns the template name
     *
     * @return string
     *
     * @throws \Exception
     */
    public static function getTemplateName()
    {
        $model = Self::newWithoutConstructor();

        if (isset($model->templateName)) {
            return $model->postType;
        }

        throw new Exception('$postType not defined');
    }

    // -----------------------------------------------------
	// HELPER METHODS
	// -----------------------------------------------------
    /**
     * Check if the post exists by Post ID
     *
     * @param  string|int   $ID   Post ID
     * @param  bool 		$postTypeSafe Require post to be the same post type as the model
     * @return bool
     */
    public static function exists($ID, $postTypeSafe = true)
    {
        if ($postTypeSafe) {
            $template_name = get_post_meta($ID, '_wp_page_template', true);
            if ($template_name === Self::getTemplateName()) {
                return true;
            }
        }
        return parent::exists($ID, $postTypeSafe);
    }
}