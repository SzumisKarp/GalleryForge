<?php
/*
 * Plugin Name: GalleryForge
 * Description: Plugin to creating photo galleries
 * Version: 0.1.0
 * Author: <a href="https://t.ly/duzp9">SzumisKarp</a>
*/
// Tworzenie nowego post_type "Galeria Zdjęć"
function custom_gallery_post_type() {
    register_post_type('gallery',
        array(
            'labels' => array(
                'name' => __('Galerie Zdjęć'),
                'singular_name' => __('Galeria Zdjęć')
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail'),
        )
    );
}
add_action('init', 'custom_gallery_post_type');

// Interfejs administracyjny
function custom_gallery_admin_menu() {
    add_menu_page('Galerie Zdjęć', 'Galerie Zdjęć', 'manage_options', 'custom_gallery_admin', 'custom_gallery_admin_page');
    
    // Dodaj podstronę do menu
    add_submenu_page('custom_gallery_admin', 'Dodaj Nową Galerię', 'Dodaj Nową Galerię', 'manage_options', 'add_new_gallery', 'add_new_gallery_page');
}
add_action('admin_menu', 'custom_gallery_admin_menu');

function add_new_gallery_page() {
    echo '<div class="wrap">';
    echo '<h1>' . __('Dodaj Nową Galerię', 'text_domain') . '</h1>';
    
    // Dodaj formularz do dodawania nowej galerii
    echo '<form method="post" action="">';
    echo '<label for="gallery_title">' . __('Gallery Title:', 'text_domain') . '</label>';
    echo '<input type="text" name="gallery_title" id="gallery_title" required>';
    echo '<input type="submit" name="add_gallery" class="button button-primary" value="' . __('Add Gallery', 'text_domain') . '">';
    echo '</form>';

    echo '</div>';
}


function save_gallery_function($gallery_title) {
    // Create a new gallery post
    $gallery_post = array(
        'post_title'    => $gallery_title,
        'post_type'     => 'gallery',
        'post_status'   => 'publish',
    );

    // Insert the post into the database
    $gallery_id = wp_insert_post($gallery_post);

    // Check if the gallery was successfully added
    if ($gallery_id) {
        echo '<div class="notice notice-success is-dismissible">';
        echo '<p>' . __('Gallery added successfully!', 'text_domain') . '</p>';
        echo '</div>';
    } else {
        echo '<div class="notice notice-error is-dismissible">';
        echo '<p>' . __('Error adding gallery. Please try again.', 'text_domain') . '</p>';
        echo '</div>';
    }
}

function custom_gallery_admin_page() {
    echo '<div class="wrap">';
    echo '<h1>' . __('Manage Galerie Zdjęć', 'text_domain') . '</h1>';

    // Form for adding a new gallery
    echo '<form method="post" action="">';
    echo '<label for="gallery_title">' . __('Gallery Title:', 'text_domain') . '</label>';
    echo '<input type="text" name="gallery_title" id="gallery_title" required>';
    echo '<input type="submit" name="add_gallery" class="button button-primary" value="' . __('Add Gallery', 'text_domain') . '">';
    echo '</form>';

    // Process form submission for adding a new gallery
    if (isset($_POST['add_gallery'])) {
        $gallery_title = sanitize_text_field($_POST['gallery_title']);
        save_gallery_function($gallery_title);
    }

    // Display existing galleries
    echo '<h2>' . __('Existing Galleries', 'text_domain') . '</h2>';
    echo '<ul>';

    // Query and display existing galleries
    $gallery_query = new WP_Query(array(
        'post_type' => 'gallery',
        'posts_per_page' => -1,
    ));

    while ($gallery_query->have_posts()) : $gallery_query->the_post();
        echo '<li>';
        echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
        echo '</li>';
    endwhile;

    // Restore global post data
    wp_reset_postdata();

    echo '</ul>';
    echo '</div>';
}


// Dodawanie i zarządzanie zdjęciami
// W kodzie strony edycji galerii dodaj obsługę dodawania i zarządzania zdjęciami (załącznikami)

// Widżet Ostatnich Galerii
class Custom_Gallery_Widget extends WP_Widget {
    function __construct() {
        parent::__construct(
            'custom_gallery_widget',
            __('Ostatnie Galerie', 'text_domain'),
            array('description' => __('Wyświetla ostatnio dodane galerie', 'text_domain'))
        );
    }

    public function widget($args, $instance) {
        // Kod do wyświetlenia ostatnich galerii
    }

    public function form($instance) {
        // Kod do obsługi formularza w panelu administracyjnym
    }

    public function update($new_instance, $old_instance) {
        // Kod do zapisywania ustawień widgetu
    }
}

function register_custom_gallery_widget() {
    register_widget('Custom_Gallery_Widget');
}
add_action('widgets_init', 'register_custom_gallery_widget');

// Shortcode do Wyświetlania Galerii
function custom_gallery_shortcode($atts) {
    // Kod do obsługi shortcode, np. pobieranie galerii z bazy danych
}
add_shortcode('custom_gallery', 'custom_gallery_shortcode');