<?php
/**
 * Plugin Name:     Color Labels for Posts and Pages
 * Plugin URI:      https://github.com/bridean/color-labels-for-posts-and-pages
 * Description:     Color-label rows in the WP Dashboard.
 * Version:         1.2.0
 * Author:          Brian Dean
 * Author URI:      https://brian-dean.com
 * License:         GPLv2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:     color-labels-for-posts-and-pages
 * Requires at least: 4.7
 * Requires PHP:    7.4
 * Tested up to:    7.0
 * Copyright: (c) 2024 Brian Dean. All rights reserved.
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'CLPP_VERSION', '1.2.0' );

// Enqueue assets only on the posts/pages list screens
add_action( 'admin_enqueue_scripts', 'clpp_enqueue_assets' );
function clpp_enqueue_assets( $hook_suffix ) {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    $post_type = null;
    $base = null;
    if ( $screen ) {
        $post_type = isset( $screen->post_type ) ? $screen->post_type : null;
        $base = isset( $screen->base ) ? $screen->base : null;
    }
    if ( ! $post_type ) {
        // Fallback for edge cases
        global $typenow;
        if ( ! empty( $typenow ) ) {
            $post_type = $typenow;
        }
    }
    if ( $base && 'edit' !== $base ) {
        return;
    }
    if ( ! $post_type || ! in_array( $post_type, array( 'post', 'page' ), true ) ) {
        return;
    }
    wp_enqueue_style(
        'clpp-style',
        plugin_dir_url( __FILE__ ) . 'css/color-labels.css',
        array(),
        CLPP_VERSION
    );
    wp_enqueue_script(
        'clpp-script',
        plugin_dir_url( __FILE__ ) . 'js/color-labels.js',
        array( 'jquery' ),
        CLPP_VERSION,
        true
    );
}


// Add the 'Color Label' link to post and page row actions
add_filter('post_row_actions', 'clpp_add_color_label_link', 10, 2);
add_filter('page_row_actions', 'clpp_add_color_label_link', 10, 2);

function clpp_add_color_label_link($actions, $post) {
    $nonce = wp_create_nonce('clpp_color_label_nonce');
    $actions['color_label'] = '<a href="#" class="color-label-link" data-post-id="' . esc_attr($post->ID) . '" data-nonce="' . esc_attr($nonce) . '">Color Label</a>'
        . '<div class="color-swatch-container" id="color-swatch-' . esc_attr($post->ID) . '">'
        . '<div class="color-swatch" data-color="#FFCDD2" style="background-color: #FFCDD2;"></div>'
        . '<div class="color-swatch" data-color="#FFE0B2" style="background-color: #FFE0B2;"></div>'
        . '<div class="color-swatch" data-color="#FFF9C4" style="background-color: #FFF9C4;"></div>'
        . '<div class="color-swatch" data-color="#C8E6C9" style="background-color: #C8E6C9;"></div>'
        . '<div class="color-swatch" data-color="#BBDEFB" style="background-color: #BBDEFB;"></div>'
        . '<div class="color-swatch" data-color="#E1BEE7" style="background-color: #E1BEE7;"></div>'
        . '<div class="color-swatch" data-color="#f6f7f7" style="background-color: #f6f7f7;"></div>'
        . '<div class="color-swatch" data-color="#ffffff" style="background-color: #ffffff;"></div>'
        . '</div>';
    return $actions;
}

// Remove duplicate global enqueues; assets are loaded conditionally above.

// Handle the AJAX request to save the color label
add_action( 'wp_ajax_clpp_save_color_label', 'clpp_save_color_label' );

function clpp_save_color_label() {
    // Nonce and capability checks
    $raw_nonce = isset( $_POST['nonce'] ) ? wp_unslash( $_POST['nonce'] ) : '';
    $nonce     = sanitize_text_field( $raw_nonce );

    if ( ! wp_verify_nonce( $nonce, 'clpp_color_label_nonce' ) ) {
        wp_send_json_error( 'Invalid nonce' );
    }

    $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
    if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
        wp_send_json_error( 'Permission denied' );
    }

    // Unsplash and sanitize the color
    $raw_color = isset( $_POST['color'] ) ? wp_unslash( $_POST['color'] ) : '';
    $color     = sanitize_hex_color( $raw_color );

    if ( $color ) {
        update_post_meta( $post_id, '_clpp_color_label', $color );
        wp_send_json_success();
    }

    wp_send_json_error( 'Invalid color' );
}

// On initial load of the list table, append saved row colors to the enqueued stylesheet
add_action( 'admin_enqueue_scripts', 'clpp_render_saved_row_colors', 20 );
function clpp_render_saved_row_colors() {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || 'edit' !== $screen->base || empty( $screen->post_type ) || ! in_array( $screen->post_type, array( 'post', 'page' ), true ) ) {
        return;
    }

    global $wp_query;
    if ( ! $wp_query || empty( $wp_query->posts ) ) {
        return;
    }

    $rules = array();
    foreach ( (array) $wp_query->posts as $p ) {
        $color = get_post_meta( $p->ID, '_clpp_color_label', true );
        if ( $color && is_string( $color ) ) {
            $san = sanitize_hex_color( $color );
            if ( $san ) {
                $rules[] = sprintf( 'tr#post-%d{background-color:%s !important;}', (int) $p->ID, esc_attr( $san ) );
            }
        }
    }

    if ( $rules ) {
        wp_add_inline_style( 'clpp-style', implode( '', $rules ) );
    }
}

