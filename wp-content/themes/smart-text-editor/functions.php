<?php
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'STE_THEME_VERSION', '1.0.0' );
define( 'STE_THEME_URI', get_template_directory_uri() );

/* ── Theme setup ── */
function ste_theme_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'gallery', 'caption', 'style', 'script' ) );

    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'smart-text-editor-theme' ),
    ) );
}
add_action( 'after_setup_theme', 'ste_theme_setup' );

/* ── Favicon ── */
function ste_theme_favicon() {
    echo '<link rel="icon" href="' . esc_url( STE_THEME_URI . '/assets/images/favicon.svg' ) . '" type="image/svg+xml">';
}
add_action( 'wp_head', 'ste_theme_favicon' );

/* ── Enqueue assets ── */
function ste_enqueue_frontend_anim() {
    // The plugin's STE_Shortcode::enqueue_assets() already handles singular pages
    // and pages with STE content. Only enqueue here for theme-specific templates
    // that are NOT singular posts/pages (e.g. the homepage landing template).
    if ( is_singular() ) return; // plugin handles these
    if ( is_front_page() || is_home() ) {
        if ( defined( 'STE_PLUGIN_URL' ) && defined( 'STE_VERSION' ) ) {
            wp_enqueue_style( 'ste-front', STE_PLUGIN_URL . 'assets/css/frontend.css', array(), STE_VERSION );
            wp_enqueue_script( 'ste-front', STE_PLUGIN_URL . 'assets/js/frontend.js', array(), STE_VERSION, true );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'ste_enqueue_frontend_anim', 20 );

function ste_theme_assets() {
    // Google Fonts
    wp_enqueue_style( 'ste-google-fonts', defined( 'STE_GOOGLE_FONTS_URL' ) ? STE_GOOGLE_FONTS_URL : '', array(), null );

    wp_enqueue_style( 'ste-theme-style', STE_THEME_URI . '/assets/css/theme.css', array(), STE_THEME_VERSION );
    wp_enqueue_script( 'ste-theme-editor', STE_THEME_URI . '/assets/js/editor.js', array(), STE_THEME_VERSION, true );
    wp_localize_script( 'ste-theme-editor', 'steTheme', array(
        'checkoutUrl' => home_url( '/checkout/' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'ste_theme_assets', 30 );

/* ── Custom page templates ── */
function ste_theme_page_templates( $templates ) {
    $templates['template-fullwidth.php'] = __( 'Full Width (No Sidebar)', 'smart-text-editor-theme' );
    $templates['template-blank.php']     = __( 'Blank Canvas', 'smart-text-editor-theme' );
    $templates['template-landing.php']   = __( 'Landing Page', 'smart-text-editor-theme' );
    $templates['template-contact.php']   = __( 'Contact Page', 'smart-text-editor-theme' );
    $templates['template-about.php']     = __( 'About Page', 'smart-text-editor-theme' );
    $templates['template-blog.php']      = __( 'Blog / Posts', 'smart-text-editor-theme' );
    $templates['template-privacy.php']   = __( 'Privacy Policy', 'smart-text-editor-theme' );
    $templates['template-refund.php']    = __( 'Refund & Cancellation Policy', 'smart-text-editor-theme' );
    $templates['template-terms.php']     = __( 'Terms & Conditions', 'smart-text-editor-theme' );
    return $templates;
}
add_filter( 'theme_page_templates', 'ste_theme_page_templates' );
