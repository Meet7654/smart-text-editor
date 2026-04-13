<?php
/**
 * Frontend: enqueue styles/scripts for pages using styled content,
 * wrap output in .ste-output container, and provide [ste_doc] shortcode.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class STE_Shortcode {

    public static function init() {
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
        // Wrap post content in .ste-output and enqueue assets when needed
        add_filter( 'the_content', array( __CLASS__, 'wrap_content' ) );
        // Register the [ste_doc] shortcode
        add_shortcode( 'ste_doc', array( __CLASS__, 'render_shortcode' ) );
    }

    /**
     * Register assets, and on singular pages enqueue early so CSS is in <head>
     * before content renders (prevents flash of unstyled animated content).
     */
    public static function enqueue_assets() {
        wp_register_style( 'ste-google-fonts',
            'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700;800&family=Roboto:wght@300;400;500;700;900&family=Montserrat:wght@300;400;500;600;700;800;900&family=Open+Sans:wght@300;400;500;600;700;800&family=Lato:wght@300;400;700;900&family=Oswald:wght@300;400;500;600;700&family=Raleway:wght@300;400;500;600;700;800;900&family=Dancing+Script:wght@400;500;600;700&family=Pacifico&family=Lobster&family=Caveat:wght@400;500;600;700&family=Satisfy&family=Great+Vibes&family=Sacramento&family=Permanent+Marker&family=Abril+Fatface&family=Bebas+Neue&family=Righteous&family=Russo+One&family=Orbitron:wght@400;500;600;700;800;900&family=Press+Start+2P&family=Fira+Code:wght@300;400;500;600;700&family=Source+Code+Pro:wght@300;400;500;600;700&family=Merriweather:wght@300;400;700;900&family=Crimson+Text:wght@400;600;700&family=Libre+Baskerville:wght@400;700&family=Spectral:wght@300;400;500;600;700;800&family=Comfortaa:wght@300;400;500;600;700&family=Quicksand:wght@300;400;500;600;700&family=Nunito:wght@300;400;500;600;700;800;900&family=Work+Sans:wght@300;400;500;600;700;800;900&family=Josefin+Sans:wght@300;400;500;600;700&family=Barlow:wght@300;400;500;600;700;800;900&family=Rubik:wght@300;400;500;600;700;800;900&family=Archivo+Black&family=Anton&family=Titan+One&family=Bangers&family=Bungee&display=swap',
            array(), null
        );
        wp_register_style( 'ste-front', STE_PLUGIN_URL . 'assets/css/frontend.css', array( 'ste-google-fonts' ), STE_VERSION );
        wp_register_script( 'ste-front', STE_PLUGIN_URL . 'assets/js/frontend.js', array(), STE_VERSION, true );

        // Early enqueue on singular views — check post content before render
        if ( is_singular() ) {
            $post = get_queried_object();
            if ( $post && isset( $post->post_content ) ) {
                wp_enqueue_style( 'ste-front' );
                wp_enqueue_script( 'ste-front' );
            }
        }
    }

    /**
     * Wrap content in .ste-output only when the post was created with STE.
     * Detects STE content by presence of ste- classes or data-ste-anim attributes.
     */
    public static function wrap_content( $content ) {
        if ( empty( $content ) ) return $content;

        // Only wrap if content contains STE-specific markup
        if ( strpos( $content, 'data-ste-anim' ) === false
            && strpos( $content, 'ste-output' ) === false
            && strpos( $content, '-webkit-text-fill-color' ) === false
            && strpos( $content, 'text-shadow' ) === false ) {
            return $content;
        }

        wp_enqueue_style( 'ste-front' );
        wp_enqueue_script( 'ste-front' );

        return '<div class="ste-output">' . $content . '</div>';
    }

    /**
     * [ste_doc id="123"] — Embed another post/page's styled content inline.
     */
    public static function render_shortcode( $atts ) {
        $atts = shortcode_atts( array( 'id' => 0 ), $atts, 'ste_doc' );
        $post_id = absint( $atts['id'] );
        if ( ! $post_id ) return '';

        $post = get_post( $post_id );
        if ( ! $post || 'publish' !== $post->post_status ) return '';

        wp_enqueue_style( 'ste-front' );
        wp_enqueue_script( 'ste-front' );

        return '<div class="ste-output">' . wp_kses_post( $post->post_content ) . '</div>';
    }
}
