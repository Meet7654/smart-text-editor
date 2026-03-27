<?php
/**
 * Plugin Name: Smart Text Editor
 * Description: A fully custom rich text editor built from scratch — no Gutenberg, no TinyMCE. Features gradient text, 3D effects, glow, animations, 48 built-in style presets, and HTML/CSS export. Works on all post types.
 * Version: 1.3.0
 * Author: Meet Patel
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: smart-text-editor
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'STE_VERSION', '1.3.0' );
define( 'STE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'STE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'STE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once STE_PLUGIN_DIR . 'includes/class-ste-admin.php';
require_once STE_PLUGIN_DIR . 'includes/class-ste-editor.php';
require_once STE_PLUGIN_DIR . 'includes/class-ste-ajax.php';
require_once STE_PLUGIN_DIR . 'includes/class-ste-shortcode.php';

function ste_init() {
    STE_Admin::init();
    STE_Editor::init();
    STE_Ajax::init();
    STE_Shortcode::init();
}
add_action( 'plugins_loaded', 'ste_init' );

/* Activation */
function ste_activate() {
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ste_activate' );

/* Plugin action link */
function ste_action_links( $links ) {
    array_unshift( $links, '<a href="' . esc_url( admin_url( 'post-new.php?post_type=page' ) ) . '">' . esc_html__( 'Create Page', 'smart-text-editor' ) . '</a>' );
    return $links;
}
add_filter( 'plugin_action_links_' . STE_PLUGIN_BASENAME, 'ste_action_links' );
