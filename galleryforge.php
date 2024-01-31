<?php
/*
 * Plugin Name: GalleryForge
 * Description: Plugin to create and manage photo galleries
 * Version: 0.3.3
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
        'register_meta_box_cb' => 'add_gallery_images_meta_box', // Added meta box registration
    );

    register_post_type( 'galeria_zdjec', $args );
}

// Hook into the 'init' action to register the custom post type
add_action( 'init', 'register_gallery_post_type' );

// Enqueue necessary scripts for media uploader
function enqueue_gallery_scripts() {
    wp_enqueue_media();
    wp_enqueue_script( 'gallery-scripts', plugin_dir_url( __FILE__ ) . 'gallery-scripts.js', array( 'jquery' ), '1.1', true );
}

// Hook into the 'admin_enqueue_scripts' action to enqueue scripts
add_action( 'admin_enqueue_scripts', 'enqueue_gallery_scripts' );

// Remove Custom Fields meta box
function remove_custom_fields_metabox() {
    remove_meta_box( 'postcustom', 'galeria_zdjec', 'normal' );
}

// Hook into the 'add_meta_boxes' action to remove the Custom Fields meta box
add_action( 'add_meta_boxes', 'remove_custom_fields_metabox' );

// Add custom meta box for managing images and settings
function add_gallery_images_meta_box() {
    add_meta_box(
        'gallery_images_meta_box',
        'Dodane zdjęcia',
        'display_gallery_images_meta_box',
        'galeria_zdjec',
        'normal',
        'default'
    );

    // Add meta box for gallery settings
    add_meta_box(
        'gallery_settings_meta_box',
        'Ustawienia Galerii',
        'display_gallery_settings_meta_box',
        'galeria_zdjec',
        'normal',
        'default'
    );
}

// Hook into the 'add_meta_boxes' action to add the custom meta boxes
add_action( 'add_meta_boxes', 'add_gallery_images_meta_box' );

// Display content of the custom meta box for managing images
function display_gallery_images_meta_box($post) {
    // Get previously saved images
    $gallery_images = get_post_meta($post->ID, '_gallery_images', true);

    // Output HTML for displaying images
    echo '<style>';
    echo '#gallery-images-container { display: flex; flex-wrap: wrap; }';
    echo '.gallery-image-container { width: calc(33.33% - 10px); box-sizing: border-box; margin-right: 10px; margin-bottom: 10px; overflow: hidden; border: 1px solid #ddd; border-radius: 4px; transition: 0.3s; position: relative; }';
    echo '.gallery-image-container img { width: 100%; height: 100%; object-fit: cover; }';
    echo '.gallery-image-container:hover { border: 1px solid #555; cursor: pointer; }';
    echo '#upload-gallery-images { background-color: #4CAF50; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; }';
    echo '#upload-gallery-images:hover { background-color: #45a049; }';
    echo '</style>';

    echo '<div id="gallery-images-container">';
    if (!empty($gallery_images)) {
        foreach ($gallery_images as $index => $image) {
            echo '<div class="gallery-image-container">';
            echo '<img src="' . esc_url($image) . '" alt="Gallery Image">';
            echo '</div>';

            if (($index + 1) % 3 === 0) {
                // Start a new row for every 3 images
                echo '<div style="width: 100%;"></div>';
            }
        }
    } else {
        echo '<p>Brak dodanych zdjęć</p>';
    }
    echo '</div>';

    // Add media uploader button
    echo '<p><button type="button" class="button" id="upload-gallery-images">Dodaj/Zarządzaj Zdjęciami</button></p>';

    // Add hidden input field to store image URLs
    echo '<input type="hidden" name="gallery_images" id="gallery_images" value="' . esc_attr(json_encode($gallery_images)) . '" />';

    // Add recent galleries widget
    echo '<div class="recent-galleries-widget">';
    echo '<h3>Ostatnio dodane galerie</h3>';
    the_widget('RecentGalleriesWidget');
    echo '</div>';
}

// Display content of the custom meta box for gallery settings
function display_gallery_settings_meta_box( $post ) {
    // Get previously saved gallery size
    $gallery_size = get_post_meta( $post->ID, '_gallery_size', true );

    // Output HTML for displaying settings
    echo '<label for="gallery_size_width">Szerokość Zdjęć:</label>';
    echo '<input type="text" name="gallery_size_width" id="gallery_size_width" value="' . esc_attr( isset( $gallery_size['width'] ) ? $gallery_size['width'] : '' ) . '" placeholder="Szerokość w pikselach" />';

    echo '<label for="gallery_size_height">Wysokość Zdjęć:</label>';
    echo '<input type="text" name="gallery_size_height" id="gallery_size_height" value="' . esc_attr( isset( $gallery_size['height'] ) ? $gallery_size['height'] : '' ) . '" placeholder="Wysokość w pikselach" />';
}

// Save gallery images and settings when the post is saved
function save_gallery_data( $post_id ) {
    if ( isset( $_POST['gallery_images'] ) ) {
        $gallery_images = json_decode( stripslashes( $_POST['gallery_images'] ) );

        // Sanitize and save the images
        $gallery_images = array_map( 'esc_url_raw', $gallery_images );
        update_post_meta( $post_id, '_gallery_images', $gallery_images );
    }

    if ( isset( $_POST['gallery_size_width'] ) && isset( $_POST['gallery_size_height'] ) ) {
        $gallery_size = array(
            'width'  => sanitize_text_field( $_POST['gallery_size_width'] ),
            'height' => sanitize_text_field( $_POST['gallery_size_height'] ),
        );
        update_post_meta( $post_id, '_gallery_size', $gallery_size );
    }
}

// Hook into the 'save_post' action to save gallery data
add_action( 'save_post', 'save_gallery_data' );

// Shortcode to display gallery based on the specified name
function galleryforge_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'name' => '',
        ),
        $atts,
        'gallery'
    );

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
    // Get the saved size for the gallery
    $gallery_size = get_post_meta($gallery_post->ID, '_gallery_size', true);

    // Output HTML for displaying images with a styled gallery
    $output = '<div class="galleryforge-gallery-container">'; // Added container class
    if (!empty($gallery_images)) {
        $output .= '<div class="gallery-row">'; // Start the first row
        foreach ($gallery_images as $index => $image) {
            $output .= '<div class="gallery-image-container"';

            // Apply custom size if available
            if (!empty($gallery_size) && isset($gallery_size['width']) && isset($gallery_size['height'])) {
                $output .= ' style="width: ' . esc_attr($gallery_size['width']) . 'px; height: ' . esc_attr($gallery_size['height']) . 'px;"';
            }

            $output .= '>';
            $output .= '<img src="' . esc_url($image) . '" alt="Gallery Image">';
            $output .= '</div>';

            if (($index + 1) % 3 === 0 && $index !== count($gallery_images) - 1) {
                // Start a new row for every 3 images, excluding the last image
                $output .= '</div><div class="gallery-row">';
            }
        }
        $output .= '</div>'; // Close the last row
    } else {
        $output .= '<p>Brak dodanych zdjęć</p>';
    }
    $output .= '</div>';

    // Apply additional styling to the gallery
    $output .= '<style>';
    $output .= '.galleryforge-gallery-container { display: flex; flex-wrap: wrap; justify-content: space-around; }';
    $output .= '.gallery-row { width: 100%; display: flex; justify-content: space-between; margin-bottom: 10px; }';
    $output .= '.gallery-image-container { overflow: hidden; border: 3px solid #ddd; border-radius: 4px; transition: 0.3s; position: relative; margin-bottom: 10px; }';
    $output .= '.gallery-image-container img { width: 100%; height: 100%; object-fit: cover; }';
    $output .= '.gallery-image-container:hover { border: 3px solid #555; cursor: pointer; }';
    $output .= '.galleryforge-lightbox { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); justify-content: center; align-items: center; }';
    $output .= '.galleryforge-lightbox img { max-width: 100%; max-height: 100%; }';
    $output .= '</style>';

    // Lightbox script
    $output .= '<script>';
    $output .= 'document.addEventListener("DOMContentLoaded", function() {';
    $output .= '  var images = document.querySelectorAll(".gallery-image-container img");';
    $output .= '  var lightbox = document.createElement("div");';
    $output .= '  lightbox.className = "galleryforge-lightbox";';
    $output .= '  document.body.appendChild(lightbox);';
    $output .= '  images.forEach(function(image) {';
    $output .= '    image.addEventListener("click", function() {';
    $output .= '      lightbox.innerHTML = \'<span class="galleryforge-close-btn" onclick="closeLightbox()">&times;</span><img src="\' + this.src + \'" alt="Gallery Image">\';';
    $output .= '      lightbox.style.display = "flex";';
    $output .= '    });';
    $output .= '  });';
    $output .= '  lightbox.addEventListener("click", function(e) {';
    $output .= '    if (e.target !== e.currentTarget) return;';
    $output .= '    closeLightbox();';
    $output .= '  });';
    $output .= '});';

    // Function to close the lightbox
    $output .= 'function closeLightbox() {';
    $output .= '  var lightbox = document.querySelector(".galleryforge-lightbox");';
    $output .= '  lightbox.style.display = "none";';
    $output .= '}';
    $output .= '</script>';

    return $output;
}

// Register the shortcode
add_shortcode('gallery', 'galleryforge_shortcode');

// Register the Recent Galleries Widget
function register_recent_galleries_widget() {
    register_widget('RecentGalleriesWidget');
}
add_action('widgets_init', 'register_recent_galleries_widget');

// Define the Recent Galleries Widget class
class RecentGalleriesWidget extends WP_Widget {
    // Widget setup
    public function __construct() {
        parent::__construct(
            'recent_galleries_widget', // Base ID
            'Recent Galleries Widget', // Widget name
            array('description' => 'Displays recently created galleries') // Widget description
        );
    }

    // Front-end display of the widget
    public function widget($args, $instance) {
        echo $args['before_widget'];

        $title = apply_filters('widget_title', $instance['title']);
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        $recent_galleries = $this->get_recent_galleries();
        if (!empty($recent_galleries)) {
            echo '<ul>';
            foreach ($recent_galleries as $gallery) {
                echo '<li><a href="' . esc_url(get_permalink($gallery->ID)) . '">' . esc_html($gallery->post_title) . '</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<p>No recent galleries found.</p>';
        }

        echo $args['after_widget'];
    }

    // Back-end widget form
    public function form($instance) {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <?php
    }

    // Updating widget replacing old instances with new
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }

    // Get the most recent galleries
    private function get_recent_galleries() {
        $args = array(
            'post_type' => 'galeria_zdjec',
            'posts_per_page' => 5, // Adjust the number of galleries to display
            'orderby' => 'date',
            'order' => 'DESC',
        );

        $recent_galleries = get_posts($args);
        return $recent_galleries;
    }
}
?>