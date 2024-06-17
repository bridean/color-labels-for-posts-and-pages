<?php
/*
Plugin Name: Color Labels for Posts and Pages
Description: A plugin to color-label rows for Pages and Posts in the WordPress Dashboard to ease navigation.
Version: 1.1.5
Author: Brian Dean
Author URI: https://brian-dean.com
*/

// Add the 'Color Label' link to post and page row actions
add_filter('post_row_actions', 'add_color_label_link', 10, 2);
add_filter('page_row_actions', 'add_color_label_link', 10, 2);

function add_color_label_link($actions, $post) {
    $nonce = wp_create_nonce('color_label_nonce');
    $actions['color_label'] = '<a href="#" class="color-label-link" data-post-id="' . esc_attr($post->ID) . '" data-nonce="' . esc_attr($nonce) . '">Color Label</a>';
    $actions['color_swatch'] = '<div class="color-swatch-container" id="color-swatch-' . esc_attr($post->ID) . '">
                                    <div class="color-swatch" data-color="#FFCDD2" style="background-color: #FFCDD2;"></div>
                                    <div class="color-swatch" data-color="#FFE0B2" style="background-color: #FFE0B2;"></div>
                                    <div class="color-swatch" data-color="#FFF9C4" style="background-color: #FFF9C4;"></div>
                                    <div class="color-swatch" data-color="#C8E6C9" style="background-color: #C8E6C9;"></div>
                                    <div class="color-swatch" data-color="#BBDEFB" style="background-color: #BBDEFB;"></div>
                                    <div class="color-swatch" data-color="#E1BEE7" style="background-color: #E1BEE7;"></div>
                                    <div class="color-swatch" data-color="#f6f7f7" style="background-color: #f6f7f7;"></div>
                                    <div class="color-swatch" data-color="#ffffff" style="background-color: #ffffff;"></div>
                                </div>';
    return $actions;
}

// Enqueue the JavaScript and CSS files
add_action('admin_enqueue_scripts', 'enqueue_color_label_scripts');

function enqueue_color_label_scripts() {
    wp_enqueue_script('color-label-script', plugin_dir_url(__FILE__) . 'js/color-labels.js', array('jquery'), '1.0', true);
    wp_enqueue_style('color-label-style', plugin_dir_url(__FILE__) . 'css/color-labels.css');
}

// Handle the AJAX request to save the color label
add_action('wp_ajax_save_color_label', 'save_color_label');

function save_color_label() {
    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'color_label_nonce')) {
        wp_send_json_error('Invalid nonce');
        return;
    }
    
    // Sanitize post_id and color inputs
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $color = isset($_POST['color']) ? sanitize_hex_color($_POST['color']) : '';

    // Validate inputs and update post meta
    if ($post_id && $color) {
        update_post_meta($post_id, '_color_label', $color);
        wp_send_json_success();
    } else {
        wp_send_json_error('Invalid post ID or color');
    }
}


// Apply the saved color labels to the post/page rows
add_action('admin_head', 'apply_color_labels');

function apply_color_labels() {
    global $typenow;
    if ($typenow == 'post' || $typenow == 'page') {
        $posts = get_posts(array(
            'post_type' => $typenow,
            'posts_per_page' => -1,
            'meta_key' => '_color_label'
        ));
        if ($posts) {
            echo '<style type="text/css">';
            foreach ($posts as $post) {
                $color = esc_html(get_post_meta($post->ID, '_color_label', true));
                if ($color) {
                    echo 'tr#post-' . esc_html($post->ID) . ' { background-color: ' . $color . '; }';
                }
            }
            echo '</style>';
        }
    }
}

?>