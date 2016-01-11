<?php

/**
 * Plugin Name:  Elda Loader.
 * Description:  Load WordPress site plugin.
 * Version:      1.0.0
 * Author:       Fredrik Forsmo
 * Author URI:   https://frozzare.com/
 * License:      MIT License
 */

use Frozzare\Elda\Elda;

// Configuration.
$path     = __DIR__ . '/../../../app/WordPress';
$path     = realpath( $path );
$lib_path = $path . '/' . ( defined( 'ELDA_LIB_DIR' ) ? ELDA_LIB_DIR : 'lib' );

// Load all library files.
$files    = glob( $lib_path . '/*', GLOB_NOSORT );
$files    = array_map( function ( $file ) use( $path ) {
	$file = str_replace( $path, '', $file );
	$file = ltrim( $file, '/' );

	return $file;
}, $files );

/**
 * Bootstrap plugin with Elda.
 */
Elda::boot( $path, [
	'domain'    => ELDA_DOMAIN,
  	'files'     => $files,
  	'lang_path' => __DIR__ . '../languages',
  	'namespace' => defined( 'ELDA_NAMESPACE' ) ? ELDA_NAMESPACE : 'App\\WordPress\\',
	'src_dir'   => ''
] );
