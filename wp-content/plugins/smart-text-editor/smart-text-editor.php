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

/* ── Required dependency: FluentSMTP ── */
function ste_check_fluentsmtp() {
    if ( ! function_exists( 'is_plugin_active' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    return is_plugin_active( 'fluent-smtp/fluent-smtp.php' );
}

function ste_fluentsmtp_notice() {
    if ( ste_check_fluentsmtp() ) return;

    $install_url  = '';
    $action_label = '';

    if ( file_exists( WP_PLUGIN_DIR . '/fluent-smtp/fluent-smtp.php' ) ) {
        // Installed but not activated
        $activate_url = wp_nonce_url(
            admin_url( 'plugins.php?action=activate&plugin=fluent-smtp/fluent-smtp.php' ),
            'activate-plugin_fluent-smtp/fluent-smtp.php'
        );
        $action_label = '<a href="' . esc_url( $activate_url ) . '" class="button button-primary" style="margin-left:12px;vertical-align:middle;">Activate FluentSMTP</a>';
    } else {
        // Not installed
        $install_url = wp_nonce_url(
            admin_url( 'update.php?action=install-plugin&plugin=fluent-smtp' ),
            'install-plugin_fluent-smtp'
        );
        $action_label = '<a href="' . esc_url( $install_url ) . '" class="button button-primary" style="margin-left:12px;vertical-align:middle;">Install FluentSMTP</a>';
    }

    echo '<div class="notice notice-error" style="padding:14px 18px;border-left-color:#dc2626;">
        <p style="font-size:14px;margin:0;">
            <strong>Smart Text Editor</strong> requires the <strong>FluentSMTP</strong> plugin to send emails (license keys, order confirmations, etc.). Please install and activate it to use Smart Text Editor.
            ' . $action_label . '
        </p>
    </div>';
}
add_action( 'admin_notices', 'ste_fluentsmtp_notice' );

/* ── SMTP configuration notice ── */
function ste_smtp_config_notice() {
    if ( ! ste_check_fluentsmtp() ) return; // FluentSMTP notice already shown
    if ( ! class_exists( 'STE_Checkout' ) ) return;
    if ( STE_Checkout::is_smtp_configured() ) return;

    echo '<div class="notice notice-warning" style="padding:12px 18px;border-left-color:#f59e0b;">
        <p style="font-size:13px;margin:0;">
            <strong>Smart Text Editor:</strong> FluentSMTP is active but no sender email is configured. Purchases are blocked until email delivery is set up.
            <a href="' . esc_url( admin_url( 'options-general.php?page=fluent-mail#/' ) ) . '" style="margin-left:6px;">Configure FluentSMTP &rarr;</a>
        </p>
    </div>';
}
add_action( 'admin_notices', 'ste_smtp_config_notice' );

/* ── Stop loading if FluentSMTP is not active ── */
function ste_init() {
    // Load editor & core features regardless of FluentSMTP
    require_once STE_PLUGIN_DIR . 'includes/class-ste-license.php';
    require_once STE_PLUGIN_DIR . 'includes/class-ste-admin.php';
    require_once STE_PLUGIN_DIR . 'includes/class-ste-editor.php';
    require_once STE_PLUGIN_DIR . 'includes/class-ste-shortcode.php';
    require_once STE_PLUGIN_DIR . 'includes/class-ste-checkout.php';

    STE_License::init();
    STE_Admin::init();
    STE_Editor::init();
    STE_Shortcode::init();
    STE_Checkout::init();

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
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ste_deactivate' );

/* Uninstall — clean up all plugin data */
function ste_uninstall() {
    global $wpdb;
    $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ste_orders" );
    $options = array(
        'ste_active_plan', 'ste_license_key', 'ste_license_activated',
        'ste_license_expires', 'ste_billing_cycle', 'ste_trial_used',
        'ste_trial_started', 'ste_trial_expires', 'ste_cf_mode',
        'ste_cf_app_id', 'ste_cf_secret_key', 'ste_db_version',
        'ste_flush_rewrite', 'ste_license_salt',
    );
    foreach ( $options as $opt ) {
        delete_option( $opt );
    }
}
register_uninstall_hook( __FILE__, 'ste_uninstall' );

/* Plugin action link */
function ste_action_links( $links ) {
    array_unshift( $links, '<a href="' . esc_url( admin_url( 'post-new.php?post_type=page' ) ) . '">' . esc_html__( 'Create Page', 'smart-text-editor' ) . '</a>' );
    return $links;
}
add_filter( 'plugin_action_links_' . STE_PLUGIN_BASENAME, 'ste_action_links' );
