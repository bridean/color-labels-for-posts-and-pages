<?php
/*
Plugin Name: Color Label Posts and Pages
Description: A plugin to color-label rows for Pages and Posts in the WordPress Dashboard.
Version: 1.1.1
Author: Brian Dean
Author URI: https://brian-dean.com
*/

// Add the 'Color Label' link to post and page row actions
add_filter('post_row_actions', 'add_color_label_link', 10, 2);
add_filter('page_row_actions', 'add_color_label_link', 10, 2);

function add_color_label_link($actions, $post) {
    $actions['color_label'] = '<a href="#" class="color-label-link" data-post-id="' . $post->ID . '">Color Label</a>';
    return $actions;
}

// Enqueue the JavaScript and CSS files
add_action('admin_enqueue_scripts', 'enqueue_color_label_scripts');

function enqueue_color_label_scripts() {
    wp_enqueue_script('color-label-script', plugin_dir_url(__FILE__) . 'js/color-labeling.js', array('jquery'), '1.0', true);
    wp_enqueue_style('color-label-style', plugin_dir_url(__FILE__) . 'css/color-labeling.css');
}

// Handle the AJAX request to save the color label
add_action('wp_ajax_save_color_label', 'save_color_label');

function save_color_label() {
    $post_id = intval($_POST['post_id']);
    $color = sanitize_hex_color($_POST['color']);
    
    if ($post_id && $color) {
        update_post_meta($post_id, '_color_label', $color);
        wp_send_json_success();
    } else {
        wp_send_json_error();
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
                $color = get_post_meta($post->ID, '_color_label', true);
                if ($color) {
                    echo 'tr#post-' . $post->ID . ' { background-color: ' . esc_attr($color) . '; }';
                }
            }
            echo '</style>';
        }
    }
}
