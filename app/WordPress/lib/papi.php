<?php

namespace App\WordPress\Lib;

/**
 * Register Papi types directory with Papi.
 *
 * @return string
 */
add_filter('papi/settings/directories', function () {
    return __DIR__ . '/../PageTypes';
});

/**
 * Make `post-page-type.php`the only page type
 * that `post` post type can use.
 */
add_filter('papi/settings/only_page_type_post', function () {
    return 'PostPageType';
});
