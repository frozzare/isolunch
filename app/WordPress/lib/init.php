<?php

namespace App\WordPress\Lib;

/**
 * Removes admin menu items if envoirment is not development.
 */
add_action('admin_menu', function () {
    remove_menu_page('themes.php');
    remove_menu_page('edit.php?post_type=page');
});
