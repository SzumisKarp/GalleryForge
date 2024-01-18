<?php
/*
 * Plugin Name: GalleryForge
 * Description: Plugin to create and manage photo galleries
 * Version: 0.1.0
 * Author: <a href="https://t.ly/duzp9">SzumisKarp</a>
*/

function create_gallery_post_type() {
    register_post_type('gallery', array(
        'labels' => array(
            'name' => __('Galerie Zdjęć'),
            'singular_name' => __('Galeria Zdjęć'),
            'menu_name' => __('Galerie Zdjęć'),
            'all_items' => __('Wszystkie Galerie Zdjęć'),
            'add_new' => __('Dodaj nową galerię'),
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'galeria-zdjec'),
        'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'capability_type' => 'post'
    ));
}

add_action('init', 'create_gallery_post_type');

// Dodaj interfejs administracyjny do zarządzania galeriami
function add_gallery_admin_menu() {
    add_menu_page(
        __('Zarządzaj Galeriami'),
        __('Galerie Zdjęć'),
        'manage_options',
        'manage-galleries',
        'manage_galleries_page',
        'dashicons-format-gallery',
        20
    );

    add_submenu_page(
        'manage-galleries',
        __('Dodaj Nową Galerię'),
        __('Dodaj Nową'),
        'manage_options',
        'add-new-gallery',
        'add_new_gallery_page'
    );
}

add_action('admin_menu', 'add_gallery_admin_menu');

// Strona główna zarządzania galeriami
function manage_galleries_page() {
    echo '<div class="wrap">';
    echo '<h2>Zarządzaj Galeriami</h2>';
    // Dodaj kod do wyświetlania istniejących galerii
    echo '</div>';
}

// Strona dodawania nowej galerii
function add_new_gallery_page() {
    echo '<div class="wrap">';
    echo '<h2>Dodaj Nową Galerię</h2>';
    // Dodaj kod do formularza dodawania nowej galerii
    echo '</div>';
}