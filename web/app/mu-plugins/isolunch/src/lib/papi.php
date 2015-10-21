<?php

namespace Isolunch\Lib\Papi;

/**
 * Register page types directory with Papi.
 *
 * @return string
 */
add_filter( 'papi/settings/directories', function () {
	return __DIR__ . '/../page-types';
} );
