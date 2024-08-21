<?php
/*
Plugin Name: WP Urdu
Plugin URI: https://github.com/etarbiyat/wp-urdu
Description: Adds support for Urdu language with Urdu font, including a Gutenberg block style and phonetic keyboard input.
Version: 1.0.0
Author: Etarbiyat
Author URI: https://etarbiyat.com/
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 5.0
Tested up to: 6.3
Requires PHP: 7.0
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
        filemtime( plugin_dir_path( __FILE__ ) . 'wp-urdu-editor.js' ),
        true
    );

    // Localize the script with plugin settings
    $wp_urdu_settings = get_option('wp_urdu_settings', array(
        'wp_urdu_shortcut' => 't',
        'wp_urdu_title_option' => 'no',
    ));
    wp_localize_script('wp-urdu-editor-script', 'wp_urdu_settings', $wp_urdu_settings);

    // Add custom styles for block editor to apply RTL and font
    wp_add_inline_style( 'wp-urdu-font', '
        .is-style-wp-urdu {
            font-family: "Noto Nastaliq Urdu", serif !important;
            direction: rtl !important;
            text-align: right !important;
            word-spacing: 0.15em;
            line-height: 2.3;
        }
    ' );
}
add_action( 'enqueue_block_editor_assets', 'wp_urdu_enqueue_assets' );

function wp_urdu_register_block_styles() {
    register_block_style(
        'core/paragraph',
        array(
            'name'  => 'wp-urdu',
            'label' => 'WP Urdu',
        )
    );
}
add_action( 'init', 'wp_urdu_register_block_styles' );

function wp_urdu_add_admin_menu() {
    add_options_page(
        'WP Urdu Settings',
        'WP Urdu',
        'manage_options',
        'wp-urdu',
        'wp_urdu_settings_page'
    );
}
add_action('admin_menu', 'wp_urdu_add_admin_menu');

function wp_urdu_settings_page() {
    ?>
    <div class="wrap">
        <h1>WP Urdu Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('wp_urdu_settings_group');
            do_settings_sections('wp-urdu');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function wp_urdu_settings_init() {
    register_setting('wp_urdu_settings_group', 'wp_urdu_settings');

    add_settings_section(
        'wp_urdu_settings_section',
        'WP Urdu Settings',
        null,
        'wp-urdu'
    );

    add_settings_field(
        'wp_urdu_shortcut',
        'Toggle Shortcut',
        'wp_urdu_shortcut_render',
        'wp-urdu',
        'wp_urdu_settings_section'
    );

    add_settings_field(
        'wp_urdu_title_option',
        'Apply Urdu to Titles',
        'wp_urdu_title_option_render',
        'wp-urdu',
        'wp_urdu_settings_section'
    );

    add_settings_field(
        'wp_urdu_block_style_shortcut',
        'Block Style Shortcut',
        'wp_urdu_block_style_shortcut_render',
        'wp-urdu',
        'wp_urdu_settings_section'
    );
}
add_action('admin_init', 'wp_urdu_settings_init');

function wp_urdu_shortcut_render() {
    $options = get_option('wp_urdu_settings');
    $shortcut = isset($options['wp_urdu_shortcut']) ? $options['wp_urdu_shortcut'] : 't';
    ?>
    <input type="text" name="wp_urdu_settings[wp_urdu_shortcut]" value="<?php echo esc_attr($shortcut); ?>" />
    <p class="description">Enter the key you want to use for toggling Urdu typing (e.g., 't').</p>
    <?php
}

function wp_urdu_title_option_render() {
    $options = get_option('wp_urdu_settings');
    $apply_to_titles = isset($options['wp_urdu_title_option']) ? $options['wp_urdu_title_option'] : 'no';
    ?>
    <select name="wp_urdu_settings[wp_urdu_title_option]">
        <option value="yes" <?php selected($apply_to_titles, 'yes'); ?>>Yes</option>
        <option value="no" <?php selected($apply_to_titles, 'no'); ?>>No</option>
    </select>
    <p class="description">Choose whether to apply Urdu styling to all post titles by default.</p>
    <?php
}

function wp_urdu_block_style_shortcut_render() {
    $options = get_option('wp_urdu_settings');
    $block_style_shortcut = isset($options['wp_urdu_block_style_shortcut']) ? $options['wp_urdu_block_style_shortcut'] : 'b';
    ?>
    <input type="text" name="wp_urdu_settings[wp_urdu_block_style_shortcut]" value="<?php echo esc_attr($block_style_shortcut); ?>" />
    <p class="description">Enter the key you want to use for applying Urdu style to selected blocks (e.g., 'b').</p>
    <?php
}
