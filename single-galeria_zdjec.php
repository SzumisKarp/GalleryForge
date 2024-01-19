<?php
/**
 * Template Name: Single Galeria Zdjęć
 */

get_header();

while (have_posts()) : the_post();
    echo '<div id="post-' . get_the_ID() . '" ' . post_class() . '>';
    echo '<h1 class="entry-title">' . get_the_title() . '</h1>';
    echo '<div class="entry-content">';
    the_content();

    // Display gallery images
    display_gallery_images(get_the_title());

    echo '</div>';
    echo '</div>';

endwhile;

get_footer();
?>
