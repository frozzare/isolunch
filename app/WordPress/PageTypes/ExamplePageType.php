<?php

namespace App\WordPress\PageTypes;

class ExamplePageType extends \Papi_Page_Type
{
	public function meta()
	{
		return [
			'name' => 'Example page type'
		];
	}

	public function register()
	{
		$this->box('Content', [
			papi_property([
				'title' => 'Name',
				'type'  => 'string'
			])
		]);
	}
}
