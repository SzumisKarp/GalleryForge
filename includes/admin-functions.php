<?php
function enqueue_media_uploader_script() {
    if (is_admin()) {
        wp_enqueue_media();
    }
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
function hide_add_gallery_submenu_page() {
    global $submenu;
    // Sprawdź, czy podstrona o nazwie "Dodaj nową galerię" istnieje
    if (isset($submenu['edit.php?post_type=galeria_zdjec'])) {
        // Szukaj indeksu podstrony o nazwie "Dodaj nową galerię" i usuń ją z submenu
        foreach ($submenu['edit.php?post_type=galeria_zdjec'] as $index => $subpage) {
            if (isset($subpage[0]) && $subpage[0] === 'Dodaj nową galerię') {
                unset($submenu['edit.php?post_type=galeria_zdjec'][$index]);
                break;
            }
        }
    }
}
add_action('admin_menu', 'hide_add_gallery_submenu_page');
add_action('admin_menu', 'add_photo_gallery_submenu_page');
add_action('admin_enqueue_scripts', 'enqueue_media_uploader_script');