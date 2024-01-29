document.addEventListener('DOMContentLoaded', function() {
    var frame;

    // Triggered when the "Dodaj/Zarządzaj Zdjęciami" button is clicked
    document.getElementById('upload-gallery-images').addEventListener('click', function(e) {
        e.preventDefault();

        // If the media frame already exists, reopen it.
        if (frame) {
            frame.open();
            return;
        }

        // Create a new media frame
        frame = wp.media({
            title: 'Dodaj/Zarządzaj Zdjęciami',
            button: {
                text: 'Wybierz Zdjęcia'
            },
            multiple: true
        });

        // When an image is selected in the media frame, run a callback
        frame.on('select', function() {
            var attachment = frame.state().get('selection').toJSON();
            var galleryImages = [];

            // Collect URLs of selected images
            attachment.forEach(function(value) {
                galleryImages.push(value.url);
            });

            // Update the hidden input field and display the images
            document.getElementById('gallery_images').value = JSON.stringify(galleryImages);
            displayGalleryImages(galleryImages);
        });

        // Open the media frame
        frame.open();
    });

    // Display images in the custom meta box
    function displayGalleryImages(images) {
        var container = document.getElementById('gallery-images-container');
        container.innerHTML = '';

        if (images.length > 0) {
            images.forEach(function(url) {
                var img = document.createElement('img');
                img.src = url;
                img.alt = 'Gallery Image';
                img.style.maxWidth = '100px';
                img.style.maxHeight = '100px';
                img.style.marginRight = '10px';
                container.appendChild(img);
            });
        } else {
            container.innerHTML = '<p>Brak dodanych zdjęć</p>';
        }
    }
});