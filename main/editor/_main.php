<?php

add_filter('tiny_mce_before_init', function ($settings) {
    //d_log($settings);
    $settings['block_formats'] = 'Paragraph=p;Heading 2=h2;Heading 3=h3;';
    $settings['toolbar1'] = 'formatselect,bold,italic,bullist,numlist,alignleft,aligncenter,alignright,link,spellchecker,dfw,wp_adv';
    $settings['toolbar2'] = 'pastetext,removeformat,charmap,undo,redo,wp_help';

    return $settings;
});
