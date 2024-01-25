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
class RecentGalleriesWidget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'recent_galleries_widget',
            'Ostatnio Dodane Galerie',
            array('description' => 'Wyświetla ostatnio dodane galerie.')
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];

        $title = apply_filters('widget_title', $instance['title']);
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        // Display recent galleries
        $recent_galleries = new WP_Query(array(
            'post_type'      => 'galeria_zdjec',
            'posts_per_page' => $instance['number'],
            'orderby'        => 'date',
            'order'          => 'DESC',
        ));

        if ($recent_galleries->have_posts()) {
            echo '<ul>';
            while ($recent_galleries->have_posts()) {
                $recent_galleries->the_post();
                echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
            }
            echo '</ul>';
            wp_reset_postdata();
        } else {
            echo '<p>Brak ostatnio dodanych galerii.</p>';
        }

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $number = isset($instance['number']) ? absint($instance['number']) : 5;

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Tytuł:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>">Liczba galerii do wyświetlenia:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" min="1" value="<?php echo $number; ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 5;

        return $instance;
    }
}
function register_recent_galleries_widget() {
    register_widget('RecentGalleriesWidget');
}