<?php
/*
Plugin Name: WP Urdu
Plugin URI: https://github.com/etarbiyat/wp-urdu
Description: Adds support for Urdu language with Urdu font, including a Gutenberg block style and phonetic keyboard input.
Version: 1.1.0
Author: Etarbiyat
Author URI: https://etarbiyat.com/
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 5.0
Tested up to: 6.3
Requires PHP: 7.0
Text Domain: wp-urdu
Domain Path: /languages
*/

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly.
}

// Define constants
define( 'WP_URDU_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WP_URDU_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Load text domain for translations
function wp_urdu_load_textdomain() {
    load_plugin_textdomain( 'wp-urdu', false, WP_URDU_PLUGIN_DIR . '/languages' );
}
add_action( 'init', 'wp_urdu_load_textdomain' );

// Enqueue block editor assets
function wp_urdu_enqueue_editor_assets() {
    // Enqueue the Noto Nastaliq Urdu font
    wp_enqueue_style( 'wp-urdu-font', WP_URDU_PLUGIN_URL . 'assets/css/notonastaliqurdu.css' );

    // Enqueue block editor styles and scripts
    wp_enqueue_script(
        'wp-urdu-editor-script',
        WP_URDU_PLUGIN_URL . 'assets/js/wp-urdu-editor.js',
        array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post' ),
        filemtime( WP_URDU_PLUGIN_DIR . 'assets/js/wp-urdu-editor.js' )
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
add_action( 'enqueue_block_editor_assets', 'wp_urdu_enqueue_editor_assets' );

// Enqueue frontend styles
function wp_urdu_enqueue_frontend_styles() {
    // Apply styles to frontend as well
    wp_enqueue_style( 'wp-urdu-frontend', WP_URDU_PLUGIN_URL . 'assets/css/notonastaliqurdu.css' );

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

// Add a settings page
function wp_urdu_add_settings_page() {
    add_options_page(
        __('WP Urdu Settings', 'wp-urdu'),
        __('WP Urdu', 'wp-urdu'),
        'manage_options',
        'wp-urdu',
        'wp_urdu_render_settings_page'
    );
}
add_action( 'admin_menu', 'wp_urdu_add_settings_page' );

function wp_urdu_render_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('WP Urdu Settings', 'wp-urdu'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'wp_urdu_settings_group' );
            do_settings_sections( 'wp-urdu' );
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register settings
function wp_urdu_register_settings() {
    register_setting( 'wp_urdu_settings_group', 'wp_urdu_typing_mode' );

    add_settings_section(
        'wp_urdu_general_section',
        __('General Settings', 'wp-urdu'),
        null,
        'wp-urdu'
    );

    add_settings_field(
        'wp_urdu_typing_mode',
        __('Default Typing Mode', 'wp-urdu'),
        'wp_urdu_typing_mode_callback',
        'wp-urdu',
        'wp_urdu_general_section'
    );
}
add_action( 'admin_init', 'wp_urdu_register_settings' );

function wp_urdu_typing_mode_callback() {
    $typing_mode = get_option('wp_urdu_typing_mode', 'urdu');
    ?>
    <select name="wp_urdu_typing_mode" id="wp_urdu_typing_mode">
        <option value="urdu" <?php selected( $typing_mode, 'urdu' ); ?>><?php _e('Urdu', 'wp-urdu'); ?></option>
        <option value="english" <?php selected( $typing_mode, 'english' ); ?>><?php _e('English', 'wp-urdu'); ?></option>
    </select>
    <p class="description"><?php _e('Select the default typing mode for Urdu blocks.', 'wp-urdu'); ?></p>
    <?php
}
