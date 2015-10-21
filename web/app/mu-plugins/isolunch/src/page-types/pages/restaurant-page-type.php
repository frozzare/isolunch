<?php

namespace Isolunch\Page_Types\Pages;

class Restaurant_Page_Type extends \Papi_Page_Type {

	/**
	 * The page type meta data.
	 *
	 * @return array
	 */
	public function page_type() {
		return [
			'description' => 'Restaurant Page',
			'fill_labels' => true,
			'name'        => 'Restaurant Page',
			'post_type'   => 'restaurant',
			'template'    => 'templates/pages/restaurant.php'
		];
	}

	/**
	 * Register meta boxes with custom fields.
	 */
	public function register() {
		// Remove meta boxes we don't want.
		$this->remove( [
			'editor',
			'comments',
			'postimagediv' => 'side'
		] );

		$this->box( 'Restaurant content', [
			papi_property( [
				'slug'  => 'heading',
				'title' => 'Heading',
				'type'  => 'string'
			] ),
			papi_property( [
				'overwrite' => true,
				'slug'      => 'post_content',
				'title'     => 'Content',
				'type'      => 'editor'
			] )
		] );
	}
}
