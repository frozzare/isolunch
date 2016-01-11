<?php

namespace App\WordPress\PageTypes;

class ExamplePageType extends \Papi_Page_Type
{
    /**
     * Return page type meta.
     *
     * @return array
     */
    public function meta()
    {
        return [
            'name' => 'Example page type'
        ];
    }

    /**
     * Register meta boxes.
     */
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
