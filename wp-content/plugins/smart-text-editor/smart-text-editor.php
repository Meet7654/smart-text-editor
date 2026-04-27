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
define( 'STE_GOOGLE_FONTS_URL', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700;800&family=Roboto:wght@300;400;500;700;900&family=Montserrat:wght@300;400;500;600;700;800;900&family=Open+Sans:wght@300;400;500;600;700;800&family=Lato:wght@300;400;700;900&family=Oswald:wght@300;400;500;600;700&family=Raleway:wght@300;400;500;600;700;800;900&family=Dancing+Script:wght@400;500;600;700&family=Pacifico&family=Lobster&family=Caveat:wght@400;500;600;700&family=Satisfy&family=Great+Vibes&family=Sacramento&family=Permanent+Marker&family=Abril+Fatface&family=Bebas+Neue&family=Righteous&family=Russo+One&family=Orbitron:wght@400;500;600;700;800;900&family=Press+Start+2P&family=Fira+Code:wght@300;400;500;600;700&family=Source+Code+Pro:wght@300;400;500;600;700&family=Merriweather:wght@300;400;700;900&family=Crimson+Text:wght@400;600;700&family=Libre+Baskerville:wght@400;700&family=Spectral:wght@300;400;500;600;700;800&family=Comfortaa:wght@300;400;500;600;700&family=Quicksand:wght@300;400;500;600;700&family=Nunito:wght@300;400;500;600;700;800;900&family=Work+Sans:wght@300;400;500;600;700;800;900&family=Josefin+Sans:wght@300;400;500;600;700&family=Barlow:wght@300;400;500;600;700;800;900&family=Rubik:wght@300;400;500;600;700;800;900&family=Archivo+Black&family=Anton&family=Titan+One&family=Bangers&family=Bungee&display=swap' );

/* ── Required dependency notices and plugin action link are
 * handled by STE_Admin::init() once the class is loaded.
 * ── */

function ste_init() {
    // Load editor & core features regardless of FluentSMTP
    require_once STE_PLUGIN_DIR . 'includes/class-ste-license.php';
    require_once STE_PLUGIN_DIR . 'includes/class-ste-admin.php';
    require_once STE_PLUGIN_DIR . 'includes/class-ste-editor.php';
    require_once STE_PLUGIN_DIR . 'includes/class-ste-shortcode.php';
    require_once STE_PLUGIN_DIR . 'includes/class-ste-checkout.php';
    require_once STE_PLUGIN_DIR . 'includes/class-ste-keyboard-api.php';

    STE_License::init();
    STE_Admin::init();
    STE_Editor::init();
    STE_Shortcode::init();
    STE_Checkout::init();
    STE_Keyboard_API::init();

    if ( get_option( 'ste_db_version', '0' ) !== STE_VERSION ) {
        STE_Checkout::create_table();
        update_option( 'ste_db_version', STE_VERSION );
    }
}
add_action( 'plugins_loaded', 'ste_init' );

/* ── Soft-block purchases (not plugin load) if FluentSMTP missing ──
 * The actual block happens inside ajax_create_cf_order() and the checkout
 * template via STE_Checkout::is_smtp_configured() checks.
 */

/* Activation */
function ste_activate() {
    require_once STE_PLUGIN_DIR . 'includes/class-ste-checkout.php';
    require_once STE_PLUGIN_DIR . 'includes/class-ste-license.php';
    STE_Checkout::create_table();
    STE_License::init_salt();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ste_activate' );

/* Deactivation */
function ste_deactivate() {
    wp_clear_scheduled_hook( 'ste_cleanup_pending_orders' );
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ste_deactivate' );

/* Uninstall — handled by uninstall.php (loaded directly by WordPress) */

/* Plugin action link — handled by STE_Admin::action_links() */
