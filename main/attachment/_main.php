<?php

namespace TFD;

class Attachment
{
    public static function get_data($id)
    {
        $attachment = get_post($id);

        $data = null;
        if ($attachment && $attachment->post_type === 'attachment') {
            $data = [
                'id' => $attachment->ID,
                'title' => $attachment->post_title,
                'mime_type' => $attachment->post_mime_type,
                'description' => $attachment->post_content,
                'caption' => $attachment->post_excerpt,
                'date' => date_i18n('j. F Y', strtotime(get_the_date('d-m-Y', $attachment->id))),
                'href' => get_permalink($attachment->ID),
                'url' => wp_get_attachment_url($attachment->ID),
                'path' => get_attached_file($attachment->ID),
                'size' => size_format(filesize(get_attached_file($attachment->ID))),
            ];

            $data = array_merge(pathinfo($data['path']), $data);
        }

        return $data;
    }
}
