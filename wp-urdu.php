<?php
/*
Plugin Name: WP Urdu
Plugin URI: https://github.com/etarbiyat/wp-urdu
Description: Adds support for Urdu language with Urdu font, including a Gutenberg block style.
Version: 1.0
Author: Etarbiyat.com
Author URI: https://etarbiyat.com/
*/

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly.
}

function wp_urdu_enqueue_assets() {
    // Enqueue the Noto Nastaliq Urdu font
    wp_enqueue_style( 'wp-urdu-font', plugin_dir_url( __FILE__ ) . 'fonts/notonastaliqurdu.css' );

    // Enqueue block editor styles and scripts
    wp_enqueue_script(
        'wp-urdu-editor-script',
        plugin_dir_url( __FILE__ ) . 'wp-urdu-editor.js',
        array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post' ),
        filemtime( plugin_dir_path( __FILE__ ) . 'wp-urdu-editor.js' )
    );

    // Add custom styles for block editor to apply RTL and font
    wp_add_inline_style( 'wp-urdu-font', '
        .is-style-wp-urdu {
            font-family: "Noto Nastaliq Urdu", serif !important;
            direction: rtl !important;
            text-align: right !important;
            word-spacing: 0.15em;
            line-height: 2.3;
        }
        .is-style-wp-urdu .wp-block {
            direction: rtl !important;
            text-align: right !important;
        }
    ' );
}
add_action( 'enqueue_block_editor_assets', 'wp_urdu_enqueue_assets' );

function wp_urdu_enqueue_frontend_styles() {
    // Apply styles to frontend as well
    wp_enqueue_style( 'wp-urdu-frontend', plugin_dir_url( __FILE__ ) . 'fonts/notonastaliqurdu.css' );

    // Inline styles for RTL and font-family on the frontend
    wp_add_inline_style( 'wp-urdu-frontend', '
        .is-style-wp-urdu {
            font-family: "Noto Nastaliq Urdu", serif !important;
            direction: rtl !important;
            text-align: right !important;
            word-spacing: 0.15em;
            line-height: 2.3;
        }
    ' );
}
add_action( 'wp_enqueue_scripts', 'wp_urdu_enqueue_frontend_styles' );