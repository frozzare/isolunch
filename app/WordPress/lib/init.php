<?php

namespace App\WordPress\Lib;

/**
 * Removes admin menu items.
 */
add_action('admin_menu', function () {
    remove_menu_page('themes.php');
    remove_menu_page('edit.php?post_type=page');
});

add_action('init', function () {
    register_post_status('obsolete', [
        'label' => 'obsolete',
        'public' => false,
        'exclude_from_search' => true,
        'show_in_admin_all_list' => false,
        'show_in_admin_status_list' => false,
        'label_count' => _n_noop('Obsolete <span class="count">(%s)</span>',
            'Obsolete <span class="count">(%s)</span>'),
    ]);
});


add_action('save_post', function ($post_id) {
    $post = get_post($post_id);
    if ($post->post_status !== 'obsolete') {
        $obsolete = papi_get_field($post_id, 'obsolete', false);
        if ($obsolete) {
            $post->post_status = 'obsolete';
            wp_update_post($post);
        }
    }
});
