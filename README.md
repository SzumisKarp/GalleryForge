# GalleryForge WordPress Plugin

**Version: 0.1.0**

## Overview

GalleryForge is a powerful WordPress plugin crafted by [SzumisKarp](https://t.ly/duzp9) for seamlessly creating and managing stunning photo galleries on your WordPress site.

## Installation

1. **Download:**
   - [Download the latest release](#) as a zip file.

2. **Upload & Activate:**
   - Navigate to your WordPress admin panel.
   - Go to Plugins > Add New > Upload Plugin.
   - Upload the downloaded zip file and activate the plugin.

## Features

### 1. Photo Gallery Custom Post Type

Introduces a custom post type named 'galeria_zdjec' for effortlessly organizing and managing photo galleries with a user-friendly interface.

### 2. New Gallery Submenu Page

Adds a dedicated submenu page under 'Galeria Zdjęć' for creating new galleries, enhancing the overall user experience.

### 3. Gallery Creation Form

A sleek form on the 'Nowa Galeria' page allows users to create new galleries by providing a name and easily adding images using the integrated media uploader.

### 4. Media Uploader Integration

Leverages the WordPress media uploader for a seamless image selection and attachment process, making gallery creation a breeze.

### 5. Gallery Display Shortcode

Integrates a customizable `[gallery]` shortcode for effortlessly displaying galleries on the front end. Customize the display by specifying the gallery name as an attribute.

### 6. Submenu Page Access Control

Hides the 'Add New' submenu page under 'galeria_zdjec' for improved access control and a streamlined gallery creation workflow.

## Usage

1. **Navigate:**
   - Visit 'Galerie Zdjęć' in the WordPress admin panel.

2. **Create Gallery:**
   - Click on 'Nowa Galeria' to access the gallery creation form.
   - Provide a name and use the media uploader to add images.
   - Submit the form to create your gallery.

3. **Display Gallery:**
   - Use the `[gallery]` shortcode with the desired gallery name as an attribute to showcase your gallery on the front end.

## Customization

- Adjust the image size and styling in the `display_gallery_shortcode()` function.
- Modify the custom post type and form labels in the `create_photo_gallery_post_type()` function.

## Contributors

- [SzumisKarp](https://t.ly/duzp9) - Plugin Author

## License

This plugin is licensed under the [GPL-2.0-or-later](https://www.gnu.org/licenses/gpl-2.0.html) license.
