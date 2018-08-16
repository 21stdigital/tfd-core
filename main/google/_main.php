<?php

namespace TFD;

function gtm_enabled()
{
    if (defined('GTM_ID') && !is_user_logged_in()) {
        return true;
    }

    return false;
}

add_action('wp_head', function () {
    if (gtm_enabled()) :
        ?>
        <!-- Google Tag Manager -->
        <script>(function (w, d, s, l, i) {
            w[l] = w[l] || []; w[l].push({
                'gtm.start':
                new Date().getTime(), event: 'gtm.js'
            }); var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', '<?= GTM_ID; ?>');</script>
        <!-- End Google Tag Manager -->
        <?php
    endif;
});

add_action('after_body_open_tag', function () {
    if (gtm_enabled()) :
        ?>
        <!-- Google Tag Manager (noscript) -->
        <noscript>
            <iframe src="https://www.googletagmanager.com/ns.html?id=<?= GTM_ID; ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe>
        </noscript>
        <!-- End Google Tag Manager (noscript) -->
        <?php
    endif;
});
