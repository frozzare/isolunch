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
