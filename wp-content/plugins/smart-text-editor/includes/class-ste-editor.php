<?php
/**
 * Replaces the default WordPress editor with the Smart Text Editor.
 * All TinyMCE features rebuilt from scratch + advanced style effects.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class STE_Editor {

    private static $post_types = array( 'post', 'page' );

    /**
     * Set to true only while STE is actively saving post content so that
     * allow_editor_html() only widens kses rules for STE's own save flow,
     * not for every wp_kses_post() call across the entire site.
     */
    private static $_saving_ste_post = false;

    public static function init() {
        add_filter( 'use_block_editor_for_post_type', array( __CLASS__, 'disable_gutenberg' ), 10, 2 );
        add_action( 'init', array( __CLASS__, 'remove_default_editor' ) );
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_editor_box' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
        add_action( 'admin_footer', array( __CLASS__, 'footer_credit' ) );
        add_filter( 'wp_kses_allowed_html', array( __CLASS__, 'allow_editor_html' ), 10, 2 );
        add_filter( 'safe_style_css', array( __CLASS__, 'allow_css_properties' ) );
        add_action( 'save_post', array( __CLASS__, 'enforce_plan_on_save' ), 10, 2 );
    }

    public static function disable_gutenberg( $use, $pt ) {
        return in_array( $pt, self::$post_types, true ) ? false : $use;
    }

    public static function remove_default_editor() {
        foreach ( self::$post_types as $pt ) remove_post_type_support( $pt, 'editor' );
    }

    public static function add_editor_box() {
        foreach ( self::$post_types as $pt ) {
            add_meta_box( 'ste_editor_box', __( 'Smart Text Editor', 'smart-text-editor' ), array( __CLASS__, 'render_editor' ), $pt, 'normal', 'high' );
        }
    }

    public static function enqueue_assets( $hook ) {
        if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) return;
        $screen = get_current_screen();
        if ( ! $screen || ! in_array( $screen->post_type, self::$post_types, true ) ) return;
        wp_enqueue_media(); // For Insert Image
        wp_enqueue_style( 'dashicons' );
        wp_enqueue_style( 'ste-google-fonts', STE_GOOGLE_FONTS_URL, array(), null );
        wp_enqueue_style( 'ste-editor', STE_PLUGIN_URL . 'assets/css/editor.css', array(), STE_VERSION );
        wp_enqueue_script( 'ste-editor', STE_PLUGIN_URL . 'assets/js/editor.js', array(), STE_VERSION, true );
        wp_localize_script( 'ste-editor', 'stePlan', STE_License::js_config() );
    }

    public static function allow_editor_html( $allowed, $context ) {
        // Only widen kses rules when STE is actively saving its own post content.
        // Without this guard the permissive rules apply to every wp_kses_post()
        // call site across the entire WordPress installation.
        if ( 'post' !== $context || ! self::$_saving_ste_post ) return $allowed;

        // Span — allow animation data attributes
        if ( ! isset( $allowed['span'] ) ) $allowed['span'] = array();
        $allowed['span'] = array_merge( $allowed['span'], array( 'class' => true, 'style' => true, 'data-ste-anim' => true, 'data-ste-anim-dur' => true ) );
        // Common tags: allow style + class + animation data attributes
        $tags = array( 'p','h1','h2','h3','h4','h5','h6','div','li','ul','ol','a','strong','em','b','i','u','s','blockquote','font','pre','sub','sup','hr' );
        foreach ( $tags as $tag ) {
            if ( ! isset( $allowed[ $tag ] ) ) $allowed[ $tag ] = array();
            $allowed[ $tag ]['style'] = true;
            $allowed[ $tag ]['class'] = true;
            $allowed[ $tag ]['data-ste-anim'] = true;
            $allowed[ $tag ]['data-ste-anim-dur'] = true;
        }
        // Anchor-specific
        if ( ! isset( $allowed['a'] ) ) $allowed['a'] = array();
        $allowed['a']['href']   = true;
        $allowed['a']['target'] = true;
        $allowed['a']['rel']    = true;
        $allowed['a']['title']  = true;
        // Image
        if ( ! isset( $allowed['img'] ) ) $allowed['img'] = array();
        $allowed['img'] = array_merge( $allowed['img'], array( 'src' => true, 'alt' => true, 'width' => true, 'height' => true, 'style' => true, 'class' => true ) );
        // Table
        $table_tags = array( 'table' => array('style','class','border'), 'thead' => array('style','class'), 'tbody' => array('style','class'), 'tr' => array('style','class'), 'td' => array('style','class','colspan','rowspan'), 'th' => array('style','class','colspan','rowspan') );
        foreach ( $table_tags as $tag => $attrs ) {
            if ( ! isset( $allowed[ $tag ] ) ) $allowed[ $tag ] = array();
            foreach ( $attrs as $a ) $allowed[ $tag ][ $a ] = true;
        }
        return $allowed;
    }

    public static function allow_css_properties( $styles ) {
        return array_merge( $styles, array(
            'text-shadow', 'background-clip', '-webkit-background-clip',
            '-webkit-text-fill-color', '-webkit-text-stroke',
            'display', 'perspective', 'transform', 'text-align',
            'animation', 'opacity', 'clip-path',
            'border-collapse', 'vertical-align',
        ) );
    }

    /* ══════════════════════════════════════
       RENDER
       ══════════════════════════════════════ */
    public static function render_editor( $post ) {
        $content = $post->post_content;
        // Convert <!--more--> to visual placeholder for editing
        $display_content = str_replace( '<!--more-->', '<hr class="ste-more-tag">', $content );
        $plan       = STE_License::get_plan();
        $plan_data  = STE_License::get_plan_data();
        $free_fonts = STE_License::get_free_fonts();
        ?>
        <div id="ste-wrap" data-ste-plan="<?php echo esc_attr( $plan ); ?>">

            <!-- ═══ ROW 1: Main Toolbar ═══ -->
            <?php include STE_PLUGIN_DIR . 'templates/editor-toolbar.php'; ?>

        <?php include STE_PLUGIN_DIR . 'templates/editor-modals.php'; ?>
        </div>
        <?php
    }

    public static function enforce_plan_on_save( $post_id, $post ) {
        if ( ! in_array( $post->post_type, self::$post_types, true ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;

        $plan_data = STE_License::get_plan_data();
        $content   = $post->post_content;
        if ( empty( $content ) ) return;

        $new     = self::strip_plan_restricted_content( $content, $plan_data );
        $changed = ( $new !== $content );

        if ( $changed ) {
            remove_action( 'save_post', array( __CLASS__, 'enforce_plan_on_save' ), 10 );
            self::$_saving_ste_post = true;
            wp_update_post( array( 'ID' => $post_id, 'post_content' => $new ) );
            self::$_saving_ste_post = false;
            add_action( 'save_post', array( __CLASS__, 'enforce_plan_on_save' ), 10, 2 );
        }
    }

    /**
     * Strip plan-restricted content using DOMDocument so multi-line styles,
     * encoded characters, and CSS comments cannot defeat the sanitiser.
     *
     * @param string $content   Raw post HTML.
     * @param array  $plan_data Plan feature flags from STE_License::get_plan_data().
     * @return string Cleaned HTML.
     */
    private static function strip_plan_restricted_content( $content, $plan_data ) {
        // Wrap in a root element so DOMDocument handles fragments correctly.
        // Use UTF-8 meta so multibyte characters are preserved.
        $wrapped = '<html><head><meta charset="UTF-8"></head><body>' . $content . '</body></html>';

        $dom = new DOMDocument();
        libxml_use_internal_errors( true );
        $dom->loadHTML( $wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
        libxml_clear_errors();

        $xpath   = new DOMXPath( $dom );
        $changed = false;

        // ── Strip animation attributes ──────────────────────────────────────
        if ( empty( $plan_data['animations'] ) ) {
            foreach ( $xpath->query( '//*[@data-ste-anim or @data-ste-anim-dur]' ) as $node ) {
                if ( $node->hasAttribute( 'data-ste-anim' ) ) {
                    $node->removeAttribute( 'data-ste-anim' );
                    $changed = true;
                }
                if ( $node->hasAttribute( 'data-ste-anim-dur' ) ) {
                    $node->removeAttribute( 'data-ste-anim-dur' );
                    $changed = true;
                }
            }
        }

        // ── Strip effect CSS properties from inline styles ──────────────────
        if ( empty( $plan_data['effects'] ) ) {
            $effect_props = array(
                'text-shadow',
                '-webkit-background-clip',
                'background-clip',
                '-webkit-text-fill-color',
            );
            foreach ( $xpath->query( '//*[@style]' ) as $node ) {
                $cleaned = self::remove_css_properties( $node->getAttribute( 'style' ), $effect_props );
                if ( $cleaned !== $node->getAttribute( 'style' ) ) {
                    if ( '' === trim( $cleaned ) ) {
                        $node->removeAttribute( 'style' );
                    } else {
                        $node->setAttribute( 'style', $cleaned );
                    }
                    $changed = true;
                }
            }
        }

        // ── Remove table elements entirely ──────────────────────────────────
        if ( empty( $plan_data['table_editor'] ) ) {
            // Collect first so we don't modify the NodeList while iterating.
            $tables = array();
            foreach ( $xpath->query( '//table' ) as $table ) {
                $tables[] = $table;
            }
            foreach ( $tables as $table ) {
                if ( $table->parentNode ) {
                    $table->parentNode->removeChild( $table );
                    $changed = true;
                }
            }
        }

        if ( ! $changed ) return $content;

        // Extract only the <body> inner HTML to return a fragment.
        $body   = $dom->getElementsByTagName( 'body' )->item( 0 );
        $result = '';
        foreach ( $body->childNodes as $child ) {
            $result .= $dom->saveHTML( $child );
        }
        return $result;
    }

    /**
     * Remove specific CSS property declarations from an inline style string.
     * Parses declaration by declaration so encoded chars and comments in
     * other properties cannot interfere.
     *
     * @param string   $style Raw inline style attribute value.
     * @param string[] $props CSS property names to remove (lowercase).
     * @return string Remaining declarations, re-joined with "; ".
     */
    private static function remove_css_properties( $style, array $props ) {
        $declarations = array_filter( array_map( 'trim', explode( ';', $style ) ) );
        $kept = array();
        foreach ( $declarations as $decl ) {
            $colon = strpos( $decl, ':' );
            if ( false === $colon ) {
                $kept[] = $decl;
                continue;
            }
            $prop = strtolower( trim( substr( $decl, 0, $colon ) ) );
            if ( ! in_array( $prop, $props, true ) ) {
                $kept[] = $decl;
            }
        }
        return implode( '; ', $kept );
    }

    public static function footer_credit() {
        $screen = get_current_screen();
        if ( $screen && in_array( $screen->post_type, self::$post_types, true ) && 'post' === $screen->base ) {
            /* translators: %s: plugin version number */
            echo '<div style="text-align:center;padding:8px;color:#999;font-size:11px;">' . sprintf( esc_html__( 'Smart Text Editor v%s — Created by Meet Patel', 'smart-text-editor' ), esc_html( STE_VERSION ) ) . '</div>';
        }
    }
}