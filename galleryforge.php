<?php
/*
 * Plugin Name: GalleryForge
 * Description: Plugin to create and manage photo galleries
 * Version: 0.3.2
 * Author: <a href="https://t.ly/duzp9">SzumisKarp</a>
 */

// Register custom post type
function register_gallery_post_type() {
    $labels = array(
        'name'               => 'Galerie Zdjęć',
        'singular_name'      => 'Galeria Zdjęć',
        'menu_name'          => 'Galerie Zdjęć',
        'add_new'            => 'Dodaj nową galerię',
        'add_new_item'       => 'Dodaj nową galerię zdjęć',
        'edit_item'          => 'Edytuj galerię zdjęć',
        'new_item'           => 'Nowa galeria zdjęć',
        'view_item'          => 'Zobacz galerię zdjęć',
        'search_items'       => 'Szukaj galerii zdjęć',
        'not_found'          => 'Nie znaleziono żadnych galerii zdjęć',
        'not_found_in_trash' => 'Nie znaleziono żadnych galerii zdjęć w koszu',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'menu_icon'          => 'dashicons-format-gallery', // You can change the icon
        'supports'           => array( 'title', 'thumbnail', 'custom-fields' ),
        'rewrite'            => array( 'slug' => 'galeria-zdjec' ),
    );

    register_post_type( 'galeria_zdjec', $args );
}

// Hook into the 'init' action to register the custom post type
add_action( 'init', 'register_gallery_post_type' );

// Enqueue necessary scripts for media uploader
function enqueue_gallery_scripts() {
    wp_enqueue_media();
    wp_enqueue_script( 'gallery-scripts', plugin_dir_url( __FILE__ ) . 'gallery-scripts.js', array( 'jquery' ), '1.0', true );
}

// Hook into the 'admin_enqueue_scripts' action to enqueue scripts
add_action( 'admin_enqueue_scripts', 'enqueue_gallery_scripts' );

// Remove Custom Fields meta box
function remove_custom_fields_metabox() {
    remove_meta_box( 'postcustom', 'galeria_zdjec', 'normal' );
}

// Hook into the 'add_meta_boxes' action to remove the Custom Fields meta box
add_action( 'add_meta_boxes', 'remove_custom_fields_metabox' );

// Add custom meta box for managing images
function add_gallery_images_meta_box() {
    add_meta_box(
        'gallery_images_meta_box',
        'Dodane zdjęcia',
        'display_gallery_images_meta_box',
        'galeria_zdjec',
        'normal',
        'default'
    );
}

// Hook into the 'add_meta_boxes' action to add the custom meta box
add_action( 'add_meta_boxes', 'add_gallery_images_meta_box' );

// Display content of the custom meta box
function display_gallery_images_meta_box( $post ) {
    // Get previously saved images
    $gallery_images = get_post_meta( $post->ID, '_gallery_images', true );

    // Output HTML for displaying images
    echo '<style>';
    echo '#gallery-images-container { display: flex; flex-wrap: wrap; }';
    echo '#gallery-images-container div { width: 150px; height: 150px; margin-right: 10px; margin-bottom: 10px; overflow: hidden; border: 1px solid #ddd; border-radius: 4px; transition: 0.3s; }';
    echo '#gallery-images-container img { width: 100%; height: 100%; object-fit: cover; }';
    echo '#gallery-images-container div:hover { border: 1px solid #555; }';
    echo '#upload-gallery-images { background-color: #4CAF50; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; }';
    echo '#upload-gallery-images:hover { background-color: #45a049; }';
    echo '</style>';

    echo '<div id="gallery-images-container">';
    if ( ! empty( $gallery_images ) ) {
        foreach ( $gallery_images as $image ) {
            echo '<div style="width: 150px; height: 150px; margin-right: 10px; margin-bottom: 10px; overflow: hidden; border: 1px solid #ddd; border-radius: 4px; transition: 0.3s;">';
            echo '<img src="' . esc_url( $image ) . '" alt="Gallery Image" style="width: 100%; height: 100%; object-fit: cover;">';
            echo '</div>';
        }
    } else {
        echo '<p>Brak dodanych zdjęć</p>';
    }
    echo '</div>';

    // Add media uploader button
    echo '<p><button type="button" class="button" id="upload-gallery-images">Dodaj/Zarządzaj Zdjęciami</button></p>';

    // Add hidden input field to store image URLs
    echo '<input type="hidden" name="gallery_images" id="gallery_images" value="' . esc_attr( json_encode( $gallery_images ) ) . '" />';
}


// Save gallery images when the post is saved
function save_gallery_images( $post_id ) {
    if ( isset( $_POST['gallery_images'] ) ) {
        $gallery_images = json_decode( stripslashes( $_POST['gallery_images'] ) );

        // Sanitize and save the images
        $gallery_images = array_map( 'esc_url_raw', $gallery_images );
        update_post_meta( $post_id, '_gallery_images', $gallery_images );
    }
}

// Hook into the 'save_post' action to save gallery images
add_action( 'save_post', 'save_gallery_images' );

// Shortcode to display gallery based on the specified name
function galleryforge_shortcode($atts) {
    $atts = shortcode_atts(array(
        'name' => '',
    ), $atts, 'gallery');

    $gallery_name = sanitize_text_field($atts['name']);

    if (empty($gallery_name)) {
        return 'Gallery name not provided.';
    }

    // Query to get the gallery post
    $gallery_post = get_page_by_title($gallery_name, OBJECT, 'galeria_zdjec');

    if (!$gallery_post) {
        return 'Gallery not found.';
    }

    // Get the saved images for the gallery
    $gallery_images = get_post_meta($gallery_post->ID, '_gallery_images', true);

    // Output HTML for displaying images with a styled gallery
    $output = '<div class="galleryforge-gallery-container">'; // Added container class
    if (!empty($gallery_images)) {
        foreach ($gallery_images as $image) {
            $output .= '<div class="galleryforge-gallery-image">';
            $output .= '<img src="' . esc_url($image) . '" alt="Gallery Image" class="galleryforge-image">';
            $output .= '</div>';
        }
    } else {
        $output .= '<p>Brak dodanych zdjęć</p>';
    }
    $output .= '</div>';

    // Apply additional styling to the gallery
    $output .= '<style>';
    $output .= '.galleryforge-gallery-container { display: flex; flex-wrap: wrap; justify-content: space-around; }';
    $output .= '.galleryforge-gallery-image { width: 150px; height: 150px; margin: 10px; overflow: hidden; border: 2px solid #ddd; border-radius: 8px; transition: 0.3s; position: relative; }';
    $output .= '.galleryforge-gallery-image:hover { border-color: #4CAF50; cursor: pointer; }';
    $output .= '.galleryforge-image { width: 100%; height: 100%; object-fit: cover; }';
    $output .= '.galleryforge-lightbox { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); justify-content: center; align-items: center; }';
    $output .= '.galleryforge-lightbox img { max-width: 90%; max-height: 90%; }';
    $output .= '.gallery-row {display: flex;justify-content: space-between;margin-bottom: 10px; }';
    $output .= '.gallery-column {flex: 0 0 calc(33.33% - 10px); }';
    $output .= '</style>';

    // Lightbox script
    $output .= '<script>';
    $output .= 'document.addEventListener("DOMContentLoaded", function() {';
    $output .= '  var images = document.querySelectorAll(".galleryforge-gallery-image img");';
    $output .= '  var lightbox = document.createElement("div");';
    $output .= '  lightbox.className = "galleryforge-lightbox";';
    $output .= '  document.body.appendChild(lightbox);';
    $output .= '  images.forEach(function(image) {';
    $output .= '    image.addEventListener("click", function() {';
    $output .= '      lightbox.innerHTML = \'<span class="galleryforge-close-btn">&times;</span><img src="\' + this.src + \'" alt="Gallery Image">\';';
    $output .= '      lightbox.style.display = "flex";';
    $output .= '    });';
    $output .= '  });';
    $output .= '  lightbox.addEventListener("click", function(e) {';
    $output .= '    if (e.target !== e.currentTarget) return;';
    $output .= '    lightbox.style.display = "none";';
    $output .= '  });';
    $output .= '});';
    $output .= '</script>';

    return $output;
}

// Register the shortcode
add_shortcode('gallery', 'galleryforge_shortcode');

?>