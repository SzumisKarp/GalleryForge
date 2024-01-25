<?php
/*
 * Plugin Name: GalleryForge
 * Description: Plugin to create and manage photo galleries
 * Version: 0.2.0
 * Author: <a href="https://t.ly/duzp9">SzumisKarp</a>
 */

// Include enqueue scripts file
require_once plugin_dir_path(__FILE__) . 'includes/enqueue-scripts.php';

// Include gallery functions file
require_once plugin_dir_path(__FILE__) . 'includes/gallery-functions.php';

// Hooks
add_action('admin_menu', 'hide_add_gallery_submenu_page');
add_shortcode('gallery', 'display_gallery_shortcode');
add_action('admin_menu', 'add_photo_gallery_submenu_page');
add_action('init', 'create_photo_gallery_post_type');
add_action('admin_enqueue_scripts', 'enqueue_media_uploader_script');
add_action('widgets_init', 'register_recent_galleries_widget');
