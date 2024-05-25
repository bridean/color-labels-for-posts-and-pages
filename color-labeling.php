<?php
/*
Plugin Name: Color Labeling Posts and Pages
Description: A plugin to color-label rows for Pages and Posts in the WordPress Dashboard.
Version: 1.1.1
Author: Brian Dean
Author URI: https://brian-dean.com
*/

// Enqueue scripts and styles
function enqueue_color_label_scripts() {
    wp_enqueue_script('color-labeling-js', plugin_dir_url(__FILE__) . 'js/color-labeling.js', array('jquery'), '1.1.1', true);
    wp_localize_script('color-labeling-js', 'ajaxurl', admin_url('admin-ajax.php'));
}
add_action('admin_enqueue_scripts', 'enqueue_color_label_scripts');

// Add 'Color Label' link to each post row
function add_color_label_link($actions, $post) {
    $actions['color_label'] = '<a href="#" class="color-label-link" data-post-id="' . $post->ID . '">Color Label</a>';
    return $actions;
}
add_filter('post_row_actions', 'add_color_label_link', 10, 2);
add_filter('page_row_actions', 'add_color_label_link', 10, 2);

// Save the color label
function save_color_label() {
    $post_id = intval($_POST['post_id']);
    $color = sanitize_text_field($_POST['color']);
    if ($post_id && $color !== null) {
        if ($color === '') {
            delete_post_meta($post_id, '_color_label');
        } else {
            update_post_meta($post_id, '_color_label', $color);
        }
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
}
add_action('wp_ajax_save_color_label', 'save_color_label');

// Add inline style to apply the color labels
function add_color_labels_inline_style() {
    global $pagenow;
    if ($pagenow == 'edit.php' || $pagenow == 'edit.php?post_type=page') {
        $posts = get_posts(array('numberposts' => -1));
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
add_action('admin_head', 'add_color_labels_inline_style');