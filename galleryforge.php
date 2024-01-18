// functions.php lub plik wtyczki

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
}
add_action('admin_menu', 'custom_gallery_admin_menu');

function custom_gallery_admin_page() {
    // Kod HTML dla strony administracyjnej do zarządzania galeriami
    // Tutaj dodaj formularze i obsługę dodawania, edycji, usuwania galerii
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
