<?php

/**
 * Plugin Name: Isolunch
 * Description: What's for lunch today?
 * Author: Isolunch contributors
 * Author URI: http://isotop.se
 * Version: 1.0.0
 * Textdomain: isolunch
 */

use Frozzare\Elda\Elda;

/**
 * Bootstrap Isolunch plugin with Elda.
 * Visit https://github.com/frozzare/elda for more info.
 */
Elda::boot( __FILE__, [
	'files'     => [
		'lib/cpt.php',
		'lib/taxos.php',
		'lib/papi.php'
	],
	'namespace' => 'Isolunch\\'
] );
