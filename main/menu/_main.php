<?php

namespace TFD;

function breadcrumb($theme_location = 'breadcrumb')
{
    $items = wp_get_nav_menu_items($theme_location);
    if (!$items) {
        return [];
    }
    _wp_menu_item_classes_by_context($items); // Set up the class variables, including current-classes
    $crumbs = array();

    $res = [];
    foreach ($items as $item) {
        if ($item->current_item_ancestor || $item->current) {
            $anchor_id = get_field('anchor_id', $item->ID);
            if (!$anchor_id) {
                $crumbs[] = [
                    'id' => $item->ID,
                    'raw_url' => $item->url,
                    'url' => $anchor_id ? $item->url.'#'.$anchor_id : $item->url,
                    'title' => $item->title,
                    'current' => $item->current,
                    'current_item_ancestor' => $item->current_item_ancestor,
                    'current_item_parent' => $item->current_item_parent,
                    'is_anchor' => $anchor_id ? true : false,
                    'anchor_id' => get_field('anchor_id', $item->ID),
                ];
            }
        }
    }

    return $crumbs;
}