<?php
function create_photo_gallery_post_type() {
    $labels = array(
        'name'               => 'Galerie Zdjęć',
        'singular_name'      => 'Galeria Zdjęć',
        'menu_name'          => 'Galerie Zdjęć',
        'all_items'          => 'Wszystkie Galerie',
        'add_new'            => 'Dodaj nową galerię',
        'add_new_item'       => 'Dodaj nową galerię',
        'edit_item'          => 'Edytuj galerię',
        'new_item'           => 'Nowa galeria',
        'view_item'          => 'Zobacz galerię',
        'search_items'       => 'Szukaj galerii',
        'not_found'          => 'Nie znaleziono galerii',
        'not_found_in_trash' => 'Nie znaleziono galerii w koszu',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'galeria-zdjec'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
    );

    register_post_type('galeria_zdjec', $args);
}
function display_gallery_shortcode($atts) {
    $atts = shortcode_atts(array(
        'name' => '', // Default gallery name
    ), $atts, 'gallery');

    $gallery_name = sanitize_text_field($atts['name']);
    $gallery_query = new WP_Query(array(
        'post_type' => 'galeria_zdjec',
        'name' => $gallery_name,
    ));

    ob_start();

    if ($gallery_query->have_posts()) {
        while ($gallery_query->have_posts()) {
            $gallery_query->the_post();
            $attached_images = get_post_meta(get_the_ID(), '_attached_images', true);

            if (!empty($attached_images)) {
                // Set a fixed size for the images
                $image_size = 'custom-gallery-image-size';

                echo '<div class="gallery-container">';

                foreach ($attached_images as $image_id) {
                    $image_url = wp_get_attachment_url($image_id);
                    echo '<img src="' . esc_url($image_url) . '" alt="" class="' . esc_attr($image_size) . '">';
                }

                // Add custom style for the fixed size and centering
                echo '<style>';
                echo '.gallery-container {';
                echo '  display: flex;';
                echo '  justify-content: center;';
                echo '  align-items: center;';
                echo '  flex-wrap: wrap;'; // Allow images to wrap to the next line
                echo '}';
                echo '.gallery-container img.' . esc_attr($image_size) . ' {';
                echo '  display: block;';
                echo '  margin: 0 10px 10px 0;'; // Optional space between images
                echo '  width: 300px;'; // Set your desired width
                echo '  height: 200px;'; // Set your desired height
                echo '}';
                echo '</style>';

                echo '</div>';
            } else {
                echo '<p>Brak zdjęć w galerii.</p>';
            }
        }
    } else {
        echo '<p>Brak galerii o podanej nazwie.</p>';
    }

    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('gallery', 'display_gallery_shortcode');
add_action('init', 'create_photo_gallery_post_type');