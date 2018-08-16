<?php

namespace TFD\Media_Sources;

function get_sources($media_query = null)
{
    $media_queries = isset($media_query) ? $media_query : MEDIA_QUERIES;
    $res = [];
    foreach ($media_queries as $key => $media_query) {
        $res[$key] = get_source($key, $media_query);
    }

    $res['default'] = [
        'src' => [
            'default' => '1x',
        ],
    ];

    return $res;
}

function get_source($key, $media_query)
{
    $min = array_key_exists('min', $media_query) ? ["(min-width: {$media_query['min']}px)"] : [];
    $max = array_key_exists('max', $media_query) ? ["(max-width: {$media_query['max']}px)"] : [];
    $media = implode(' and ', array_merge($min, $max));

    return [
        'media' => $media,
        'src' => [
            $key => '1x',
            "{$key}@2x" => '2x',
        ],
    ];
}
