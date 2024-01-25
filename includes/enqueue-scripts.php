<?php
function enqueue_media_uploader_script() {
    if (is_admin()) {
        wp_enqueue_media();
    }
}