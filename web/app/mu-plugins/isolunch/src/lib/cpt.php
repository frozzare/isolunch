<?php

namespace Isolunch\Lib\CPT;

/**
 * Add custom post types on `init` action.
 */
add_action( 'init', function () {

	/**
	 * Register `restaurant` custom post type.
	 */
	register_extended_post_type( 'restaurant', [

	], [
		'singular' => 'Restaurant',
		'plural'   => 'Restaurants',
		'slug'     => 'restaurants'
	] );

} );
