<?php

namespace App\WordPress\PageTypes;

class PostPageType extends \Papi_Page_Type
{

    /**
     * The page type meta data.
     *
     * @return array
     */
    public function page_type()
    {
        return [
            'name' => 'Posts',
            'post_type' => 'post',
        ];
    }

    /**
     * Register custom fields and meta boxes.
     */
    public function register()
    {

        $this->box('Position', [

            papi_property([
                'slug' => 'lat',
                'title' => __('Latitude', ''),
                'type' => 'string'
            ]),

            papi_property([
                'slug' => 'lng',
                'title' => __('Longitude', ''),
                'type' => 'string'
            ]),

            papi_property([
                'slug' => 'street_adress',
                'title' => __('Street adress', ''),
                'type' => 'string'
            ])

        ]);

        $this->box('Information', [

            papi_property([
                'slug' => 'website',
                'title' => __('Website', ''),
                'type' => 'string'
            ]),

            papi_property([
                'slug' => 'menu',
                'title' => __('Menu', ''),
                'type' => 'string'
            ]),

            papi_property([
                'title' => 'Image',
                'slug' => 'image',
                'type' => 'image'
            ])

        ]);

    }
}
