<?php
/*
 * Plugin Name: GalleryForge
 * Description: Plugin to create and manage photo galleries
 * Version: 0.1.0
 * Author: <a href="https://t.ly/duzp9">SzumisKarp</a>
 */
function enqueue_media_uploader_script() {
    if (is_admin()) {
        wp_enqueue_media();
    }
}
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
function add_photo_gallery_submenu_page() {
    add_submenu_page(
        'edit.php?post_type=galeria_zdjec',
        'Nowa Galeria',
        'Nowa Galeria',
        'manage_options',
        'create-photo-gallery',
        'photo_gallery_submenu_page_form'
    );
}
function photo_gallery_submenu_page_form() {
    if (isset($_POST['create_gallery'])) {
        // Handle form submission and create gallery

        $gallery_name = sanitize_text_field($_POST['gallery_name']);

        // Create gallery post
        $gallery_post_id = wp_insert_post(array(
            'post_title'   => $gallery_name,
            'post_content' => '',  // You can customize the content as needed
            'post_type'    => 'galeria_zdjec',
            'post_status'  => 'publish',
        ));

        // Save gallery shortcode
        $gallery_shortcode = "[Gallery name={$gallery_name}]";
        update_post_meta($gallery_post_id, '_gallery_shortcode', $gallery_shortcode);

        // Attach selected images to the gallery
        if (!empty($_POST['attached_images'])) {
            $attached_images = array_map('absint', explode(',', $_POST['attached_images']));

            // Save all images
            update_post_meta($gallery_post_id, '_attached_images', $attached_images);

            // Display notification about the number of added images
            $notification_message = sprintf(
                'Została dodana następująca ilość zdjęć do Galerii "%s": %d',
                $gallery_name,
                count($attached_images)
            );
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($notification_message) . '</p></div>';
        }
    }
    ?>
    <div class="wrap">
        <h2>Tworzenie Nowej Galerii Zdjęć</h2>
        <form method="post" action="">
            <label for="gallery_name">Nazwa Galerii:</label>
            <input type="text" name="gallery_name" id="gallery_name" required>

            <!-- Media uploader button -->
            <input type="button" value="Dodaj Zdjęcia" class="button" id="upload-btn">

            <input type="hidden" name="attached_images" id="attached_images">

            <input type="submit" name="create_gallery" value="Utwórz Galerię" class="button">
        </form>

        <script>
            jQuery(document).ready(function ($) {
                // Media uploader
                $('#upload-btn').click(function (e) {
                    e.preventDefault();
                    var frame = wp.media({
                        title: 'Dodaj Zdjęcia do Galerii',
                        multiple: true,
                        library: { type: 'image' },
                    });

                    frame.on('select', function () {
                        var attachment = frame.state().get('selection').toJSON();

                        // Clear the existing images container
                        $('#selected-images-container').html('');

                        // Update the hidden input with comma-separated image IDs
                        $('#attached_images').val('');

                        // Append selected images to the container and update the hidden input
                        if (attachment.length > 0) {
                            var uploadedImages = attachment.map(function (image) {
                                $('#selected-images-container').append('<img src="' + image.url + '" alt="' + image.alt + '" class="preview-image">');
                                return image.id;
                            });

                            // Update the hidden input with comma-separated image IDs
                            $('#attached_images').val(uploadedImages.join(','));

                            // Display notification about the number of selected images
                            var notificationMessage = 'Została dodana następująca ilość zdjęć: ' + uploadedImages.length;
                            alert(notificationMessage);
                        }
                    });

                    frame.open();
                });
            });
        </script>
    </div>
    <?php
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
add_action('admin_menu', 'add_photo_gallery_submenu_page');
add_action('init', 'create_photo_gallery_post_type');
add_action('admin_enqueue_scripts', 'enqueue_media_uploader_script');