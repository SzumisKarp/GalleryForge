<?php
/*
 * Plugin Name: GalleryForge
 * Description: Plugin to create and manage photo galleries
 * Version: 0.1.0
 * Author: <a href="https://t.ly/duzp9">SzumisKarp</a>
 */
function custom_post_type_galeria_zdjec() {
    $args = array(
        'public' => true,
        'label' => 'Galeria Zdjęć',
        'supports' => array('title'),
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'galeria-zdjec'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'has_archive' => 'galeria_zdjec',
    );

    register_post_type('galeria_zdjec', $args);
}
add_action('init', 'custom_post_type_galeria_zdjec');

function add_gallery_menu_item() {
    add_menu_page(
        'Dodaj Galerię',
        'Dodaj Galerię',
        'manage_options',
        'add_gallery',
        'gallery_form_page',
        'dashicons-images-alt2',
        6
    );
}
add_action('admin_menu', 'add_gallery_menu_item');

function gallery_form_page() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && current_user_can('manage_options')) {
        $gallery_name = sanitize_text_field($_POST['gallery_name']);
        $image_files = $_FILES['gallery_images'];

        $gallery_folder = plugin_dir_path(__FILE__) . 'images/' . sanitize_title($gallery_name);
        if (!file_exists($gallery_folder)) {
            mkdir($gallery_folder, 0755, true);
        }

        foreach ($image_files['name'] as $key => $image_name) {
            $image_path = $gallery_folder . '/' . sanitize_file_name($image_name);
            move_uploaded_file($image_files['tmp_name'][$key], $image_path);
        }

        $gallery_post = array(
            'post_title' => $gallery_name,
            'post_type' => 'galeria_zdjec',
            'post_status' => 'publish',
        );

        $gallery_post_id = wp_insert_post($gallery_post);

        if ($gallery_post_id) {
            wp_redirect(admin_url('edit.php?post_type=galeria_zdjec&page=add_gallery&message=success'));
            exit;
        }
    }

    echo '<div class="wrap">';
    echo '<h2>Dodaj Galerię</h2>';
    echo '<form method="post" enctype="multipart/form-data">';
    echo '<label for="gallery_name">Nazwa Galerii:</label>';
    echo '<input type="text" name="gallery_name" required>';
    echo '<br>';
    echo '<label for="gallery_images">Zdjęcia Galerii:</label>';
    echo '<input type="file" name="gallery_images[]" accept="image/*" multiple required>';
    echo '<br>';
    echo '<label for="gallery_images">(przytrzymaj Ctrl jeżeli chcesz wybrać więcej niż jedno zdjęcie)</label>';
    echo '<br>';
    echo '<br>';
    echo '<input type="submit" value="Dodaj Galerię">';
    echo '</form>';
    echo '</div>';
}
function display_gallery_images($gallery_name) {
    $gallery_folder = plugin_dir_path(__FILE__) . 'images/' . sanitize_title($gallery_name);
    $image_files = glob($gallery_folder . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);

    if (!empty($image_files)) {
        echo '<div class="gallery-images">';
        foreach ($image_files as $image_file) {
            $image_url = esc_url(site_url('/wp-content/plugins/GalleryForge/images/' . sanitize_title($gallery_name) . '/' . basename($image_file)));
            
            echo '<div class="gallery-image">';
            echo '<img src="' . $image_url . '" alt="' . esc_attr(basename($image_file)) . '" style="width: 100%; height: auto;">';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p>No images found for this gallery.</p>';
    }
}



function custom_gallery_shortcode($atts) {
    $atts = shortcode_atts(array(
        'name' => '',
    ), $atts, 'gallery');

    if (empty($atts['name'])) {
        return 'Please provide a gallery name in the shortcode.';
    }

    ob_start();
    display_gallery_images($atts['name']);
    return ob_get_clean();
}

add_shortcode('gallery', 'custom_gallery_shortcode');