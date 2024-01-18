<?php
/*
 * Plugin Name: GalleryForge
 * Description: Plugin to creating photo galleries
 * Version: 0.1.0
 * Author: <a href="https://t.ly/duzp9">SzumisKarp</a>
*/
// Dodaj nowy post_type "Galeria Zdjęć"
function create_gallery_post_type() {
    register_post_type('gallery', array(
        'labels' => array(
            'name' => __('Galerie Zdjęć'),
            'singular_name' => __('Galeria Zdjęć')
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'galeria-zdjec'),
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields')
    ));
}
add_action('init', 'create_gallery_post_type');