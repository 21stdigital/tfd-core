<?php

namespace TFD;

function camelize($input, $separator = '_')
{
    return str_replace($separator, '', ucwords($input, $separator));
}

function email_url($email, $subject = null, $body = null)
{
    if (!is_email($email)) {
        return '';
    }

    $email = antispambot($email);
    $email_link = 'mailto:' . $email;

    $params = [];
    if ($subject) {
        $params['subject'] = $subject;
    }
    if ($body) {
        $params['body'] = $body;
    }

    $query = '';
    if (count($params)) {
        $index = 1;
        foreach ($params as $key => $value) {
            $query .= $index == 1 ? '?' : '&';
            $query .= $key . '=' . $value;
            ++$index;
        }
    }

    if ($query) {
        $email_link = $email_link . $query;
    }

    return esc_url($email_link, array('mailto'));
}

function email_link($email, $class = '')
{
    if (!is_email($email)) {
        return '';
    }

    $email = antispambot($email);
    $email_link = 'mailto:' . $email;

    $res = '<a href="' . esc_url($email_link, array('mailto')) . '" class="Email ' . $class . '" itemprop="email">' . esc_html($email) . '</a>';

    return $res;
}

function phone_link($phone, $class = '')
{
    $phone_link = 'tel:' . str_replace('-', '', str_replace(' ', '', $phone));

    $res = '<a href="' . esc_url($phone_link, array('tel')) . '" class="Phone ' . $class . '" itemprop="telephone">' . esc_html($phone) . '</a>';

    return $res;
}

function google_map_url($map)
{
    return 'https://www.google.com/maps/search/?api=1&query=' . urlencode($map['address']);
}

function get_weekday_label($weekday)
{
    switch ($weekday) {
        case 1:
            return __('Montag', 'tfd');
        case 2:
            return __('Dienstag', 'tfd');
        case 3:
            return __('Mittwoch', 'tfd');
        case 4:
            return __('Donnerstag', 'tfd');
        case 5:
            return __('Freitag', 'tfd');
        case 6:
            return __('Samstag', 'tfd');
        case 7:
            return __('Sonntag', 'tfd');
    }
}

/**
 * Calculates the great-circle distance between two points, with
 * the Haversine formula.
 *
 * @param float $latitudeFrom  Latitude of start point in [deg decimal]
 * @param float $longitudeFrom Longitude of start point in [deg decimal]
 * @param float $latitudeTo    Latitude of target point in [deg decimal]
 * @param float $longitudeTo   Longitude of target point in [deg decimal]
 * @param float $earthRadius   Mean earth radius in [m]
 *
 * @return float Distance between points in [m] (same as earthRadius)
 */
function haversineGreatCircleDistance(
    $latitudeFrom,
    $longitudeFrom,
    $latitudeTo,
    $longitudeTo,
    $earthRadius = 6371000
) {
    // convert from degrees to radians
    $latFrom = deg2rad($latitudeFrom);
    $lonFrom = deg2rad($longitudeFrom);
    $latTo = deg2rad($latitudeTo);
    $lonTo = deg2rad($longitudeTo);

    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

    return $angle * $earthRadius;
}
