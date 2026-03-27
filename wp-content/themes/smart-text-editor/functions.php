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

/* ── Enqueue assets ── */
function ste_theme_assets() {
    // Google Fonts
    wp_enqueue_style( 'ste-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700;800&family=Roboto:wght@300;400;500;700;900&family=Montserrat:wght@300;400;500;600;700;800;900&family=Open+Sans:wght@300;400;500;600;700;800&family=Lato:wght@300;400;700;900&family=Oswald:wght@300;400;500;600;700&family=Raleway:wght@300;400;500;600;700;800;900&family=Dancing+Script:wght@400;500;600;700&family=Pacifico&family=Lobster&family=Caveat:wght@400;500;600;700&family=Satisfy&family=Great+Vibes&family=Sacramento&family=Permanent+Marker&family=Abril+Fatface&family=Bebas+Neue&family=Righteous&family=Russo+One&family=Orbitron:wght@400;500;600;700;800;900&family=Press+Start+2P&family=Fira+Code:wght@300;400;500;600;700&family=Source+Code+Pro:wght@300;400;500;600;700&family=Merriweather:wght@300;400;700;900&family=Crimson+Text:wght@400;600;700&family=Libre+Baskerville:wght@400;700&family=Spectral:wght@300;400;500;600;700;800&family=Comfortaa:wght@300;400;500;600;700&family=Quicksand:wght@300;400;500;600;700&family=Nunito:wght@300;400;500;600;700;800;900&family=Work+Sans:wght@300;400;500;600;700;800;900&family=Josefin+Sans:wght@300;400;500;600;700&family=Barlow:wght@300;400;500;600;700;800;900&family=Rubik:wght@300;400;500;600;700;800;900&family=Archivo+Black&family=Anton&family=Titan+One&family=Bangers&family=Bungee&display=swap',
        array(), null
    );

    wp_enqueue_style( 'ste-theme-style', STE_THEME_URI . '/assets/css/theme.css', array(), STE_THEME_VERSION );
    wp_enqueue_script( 'ste-theme-editor', STE_THEME_URI . '/assets/js/editor.js', array(), STE_THEME_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'ste_theme_assets' );

/* ── Custom page template ── */
function ste_theme_page_templates( $templates ) {
    $templates['template-editor.php'] = __( 'Smart Text Editor', 'smart-text-editor-theme' );
    return $templates;
}
add_filter( 'theme_page_templates', 'ste_theme_page_templates' );
