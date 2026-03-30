<?php
/**
 * Replaces the default WordPress editor with the Smart Text Editor.
 * All TinyMCE features rebuilt from scratch + advanced style effects.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class STE_Editor {

    private static $post_types = array( 'post', 'page' );

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
        wp_enqueue_style( 'ste-google-fonts',
            'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&family=Playfair+Display:wght@400;700;900&family=Poppins:wght@300;400;500;600;700;800&family=Roboto:wght@300;400;500;700;900&family=Montserrat:wght@300;400;500;600;700;800;900&family=Open+Sans:wght@300;400;500;600;700;800&family=Lato:wght@300;400;700;900&family=Oswald:wght@300;400;500;600;700&family=Raleway:wght@300;400;500;600;700;800;900&family=Dancing+Script:wght@400;500;600;700&family=Pacifico&family=Lobster&family=Caveat:wght@400;500;600;700&family=Satisfy&family=Great+Vibes&family=Sacramento&family=Permanent+Marker&family=Abril+Fatface&family=Bebas+Neue&family=Righteous&family=Russo+One&family=Orbitron:wght@400;500;600;700;800;900&family=Press+Start+2P&family=Fira+Code:wght@300;400;500;600;700&family=Source+Code+Pro:wght@300;400;500;600;700&family=Merriweather:wght@300;400;700;900&family=Crimson+Text:wght@400;600;700&family=Libre+Baskerville:wght@400;700&family=Spectral:wght@300;400;500;600;700;800&family=Comfortaa:wght@300;400;500;600;700&family=Quicksand:wght@300;400;500;600;700&family=Nunito:wght@300;400;500;600;700;800;900&family=Work+Sans:wght@300;400;500;600;700;800;900&family=Josefin+Sans:wght@300;400;500;600;700&family=Barlow:wght@300;400;500;600;700;800;900&family=Rubik:wght@300;400;500;600;700;800;900&family=Archivo+Black&family=Anton&family=Titan+One&family=Bangers&family=Bungee&display=swap',
            array(), null
        );
        wp_enqueue_style( 'ste-editor', STE_PLUGIN_URL . 'assets/css/editor.css', array(), STE_VERSION );
        wp_enqueue_script( 'ste-editor', STE_PLUGIN_URL . 'assets/js/editor.js', array(), STE_VERSION, true );
        wp_localize_script( 'ste-editor', 'stePlan', STE_License::js_config() );
    }

    public static function allow_editor_html( $allowed, $context ) {
        if ( 'post' !== $context ) return $allowed;
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
        $plan      = STE_License::get_plan();
        $plan_data = STE_License::get_plan_data();
        $free_fonts = STE_License::get_free_fonts();
        ?>
        <div id="ste-wrap" data-ste-plan="<?php echo esc_attr( $plan ); ?>">

            <!-- ═══ ROW 1: Main Toolbar ═══ -->
            <div id="ste-toolbar">
                <select id="ste-block-type" title="Block type">
                    <option value="p">Paragraph</option>
                    <option value="h1">Heading 1</option>
                    <option value="h2">Heading 2</option>
                    <option value="h3">Heading 3</option>
                    <option value="h4">Heading 4</option>
                    <option value="h5">Heading 5</option>
                    <option value="h6">Heading 6</option>
                    <option value="pre">Preformatted</option>
                </select>

                <select id="ste-font-family" title="Font family">
                    <option value="">Font Family</option>
                    <optgroup label="Sans Serif">
                        <option value="'Inter', sans-serif" style="font-family:'Inter'">Inter</option>
                        <option value="'Poppins', sans-serif" style="font-family:'Poppins'">Poppins</option>
                        <option value="'Roboto', sans-serif" style="font-family:'Roboto'">Roboto</option>
                        <option value="'Montserrat', sans-serif" style="font-family:'Montserrat'">Montserrat</option>
                        <option value="'Open Sans', sans-serif" style="font-family:'Open Sans'">Open Sans</option>
                        <option value="'Lato', sans-serif" style="font-family:'Lato'">Lato</option>
                        <option value="'Raleway', sans-serif" style="font-family:'Raleway'">Raleway</option>
                        <option value="'Nunito', sans-serif" style="font-family:'Nunito'">Nunito</option>
                        <option value="'Quicksand', sans-serif" style="font-family:'Quicksand'">Quicksand</option>
                        <option value="'Work Sans', sans-serif" style="font-family:'Work Sans'">Work Sans</option>
                        <option value="'Josefin Sans', sans-serif" style="font-family:'Josefin Sans'">Josefin Sans</option>
                        <option value="'Barlow', sans-serif" style="font-family:'Barlow'">Barlow</option>
                        <option value="'Rubik', sans-serif" style="font-family:'Rubik'">Rubik</option>
                        <option value="'Comfortaa', cursive" style="font-family:'Comfortaa'">Comfortaa</option>
                        <option value="'Oswald', sans-serif" style="font-family:'Oswald'">Oswald</option>
                        <option value="Arial, Helvetica, sans-serif" style="font-family:Arial">Arial</option>
                        <option value="'Segoe UI', sans-serif" style="font-family:'Segoe UI'">Segoe UI</option>
                        <option value="Verdana, Geneva, sans-serif" style="font-family:Verdana">Verdana</option>
                        <option value="'Helvetica Neue', Helvetica, Arial, sans-serif" style="font-family:Helvetica">Helvetica</option>
                        <option value="Tahoma, Geneva, sans-serif" style="font-family:Tahoma">Tahoma</option>
                        <option value="Trebuchet MS, sans-serif" style="font-family:'Trebuchet MS'">Trebuchet MS</option>
                    </optgroup>
                    <optgroup label="Serif">
                        <option value="'Playfair Display', serif" style="font-family:'Playfair Display'">Playfair Display</option>
                        <option value="'Merriweather', serif" style="font-family:'Merriweather'">Merriweather</option>
                        <option value="'Crimson Text', serif" style="font-family:'Crimson Text'">Crimson Text</option>
                        <option value="'Libre Baskerville', serif" style="font-family:'Libre Baskerville'">Libre Baskerville</option>
                        <option value="'Spectral', serif" style="font-family:'Spectral'">Spectral</option>
                        <option value="'Abril Fatface', serif" style="font-family:'Abril Fatface'">Abril Fatface</option>
                        <option value="Georgia, 'Times New Roman', serif" style="font-family:Georgia">Georgia</option>
                        <option value="'Times New Roman', Times, serif" style="font-family:'Times New Roman'">Times New Roman</option>
                        <option value="'Palatino Linotype', Palatino, serif" style="font-family:'Palatino Linotype'">Palatino</option>
                    </optgroup>
                    <optgroup label="Display">
                        <option value="'Bebas Neue', sans-serif" style="font-family:'Bebas Neue'">Bebas Neue</option>
                        <option value="'Righteous', sans-serif" style="font-family:'Righteous'">Righteous</option>
                        <option value="'Russo One', sans-serif" style="font-family:'Russo One'">Russo One</option>
                        <option value="'Archivo Black', sans-serif" style="font-family:'Archivo Black'">Archivo Black</option>
                        <option value="'Anton', sans-serif" style="font-family:'Anton'">Anton</option>
                        <option value="'Titan One', sans-serif" style="font-family:'Titan One'">Titan One</option>
                        <option value="'Bangers', cursive" style="font-family:'Bangers'">Bangers</option>
                        <option value="'Bungee', cursive" style="font-family:'Bungee'">Bungee</option>
                        <option value="'Permanent Marker', cursive" style="font-family:'Permanent Marker'">Permanent Marker</option>
                        <option value="Impact, Charcoal, sans-serif" style="font-family:Impact">Impact</option>
                    </optgroup>
                    <optgroup label="Script / Handwriting">
                        <option value="'Dancing Script', cursive" style="font-family:'Dancing Script'">Dancing Script</option>
                        <option value="'Pacifico', cursive" style="font-family:'Pacifico'">Pacifico</option>
                        <option value="'Lobster', cursive" style="font-family:'Lobster'">Lobster</option>
                        <option value="'Caveat', cursive" style="font-family:'Caveat'">Caveat</option>
                        <option value="'Satisfy', cursive" style="font-family:'Satisfy'">Satisfy</option>
                        <option value="'Great Vibes', cursive" style="font-family:'Great Vibes'">Great Vibes</option>
                        <option value="'Sacramento', cursive" style="font-family:'Sacramento'">Sacramento</option>
                        <option value="'Comic Sans MS', cursive" style="font-family:'Comic Sans MS'">Comic Sans</option>
                    </optgroup>
                    <optgroup label="Monospace">
                        <option value="'JetBrains Mono', monospace" style="font-family:'JetBrains Mono'">JetBrains Mono</option>
                        <option value="'Fira Code', monospace" style="font-family:'Fira Code'">Fira Code</option>
                        <option value="'Source Code Pro', monospace" style="font-family:'Source Code Pro'">Source Code Pro</option>
                        <option value="'Courier New', Courier, monospace" style="font-family:'Courier New'">Courier New</option>
                        <option value="'Lucida Console', Monaco, monospace" style="font-family:'Lucida Console'">Lucida Console</option>
                    </optgroup>
                    <optgroup label="Futuristic">
                        <option value="'Orbitron', sans-serif" style="font-family:'Orbitron'">Orbitron</option>
                        <option value="'Press Start 2P', monospace" style="font-family:'Press Start 2P'">Press Start 2P</option>
                    </optgroup>
                </select>

                <select id="ste-font-size" title="Font size">
                    <option value="">Size</option>
                    <option value="8px">8</option>
                    <option value="9px">9</option>
                    <option value="10px">10</option>
                    <option value="11px">11</option>
                    <option value="12px">12</option>
                    <option value="14px">14</option>
                    <option value="16px">16</option>
                    <option value="18px">18</option>
                    <option value="20px">20</option>
                    <option value="22px">22</option>
                    <option value="24px">24</option>
                    <option value="26px">26</option>
                    <option value="28px">28</option>
                    <option value="32px">32</option>
                    <option value="36px">36</option>
                    <option value="42px">42</option>
                    <option value="48px">48</option>
                    <option value="56px">56</option>
                    <option value="64px">64</option>
                    <option value="72px">72</option>
                    <option value="96px">96</option>
                </select>
                <div class="ste-sep"></div>

                <button type="button" data-cmd="bold" title="Bold (Ctrl+B)" class="ste-tb"><b>B</b></button>
                <button type="button" data-cmd="italic" title="Italic (Ctrl+I)" class="ste-tb"><i>I</i></button>
                <button type="button" data-cmd="underline" title="Underline (Ctrl+U)" class="ste-tb"><u>U</u></button>
                <button type="button" data-cmd="strikeThrough" title="Strikethrough" class="ste-tb"><s>S</s></button>
                <button type="button" data-cmd="subscript" title="Subscript" class="ste-tb"><sub>x₂</sub></button>
                <button type="button" data-cmd="superscript" title="Superscript" class="ste-tb"><sup>x²</sup></button>
                <div class="ste-sep"></div>

                <button type="button" data-cmd="insertUnorderedList" title="Bullet list" class="ste-tb"><span class="dashicons dashicons-editor-ul"></span></button>
                <button type="button" data-cmd="insertOrderedList" title="Numbered list" class="ste-tb"><span class="dashicons dashicons-editor-ol"></span></button>
                <button type="button" data-cmd="formatBlock" data-val="blockquote" title="Blockquote" class="ste-tb"><span class="dashicons dashicons-editor-quote"></span></button>
                <button type="button" id="ste-btn-hr" title="Horizontal Rule" class="ste-tb">―</button>
                <div class="ste-sep"></div>

                <button type="button" data-cmd="justifyLeft" title="Align Left" class="ste-tb"><span class="dashicons dashicons-editor-alignleft"></span></button>
                <button type="button" data-cmd="justifyCenter" title="Align Center" class="ste-tb"><span class="dashicons dashicons-editor-aligncenter"></span></button>
                <button type="button" data-cmd="justifyRight" title="Align Right" class="ste-tb"><span class="dashicons dashicons-editor-alignright"></span></button>
                <button type="button" data-cmd="justifyFull" title="Justify" class="ste-tb"><span class="dashicons dashicons-editor-justify"></span></button>
                <div class="ste-sep"></div>

                <button type="button" id="ste-btn-link" title="Insert Link (Ctrl+K)" class="ste-tb"><span class="dashicons dashicons-admin-links"></span></button>
                <button type="button" data-cmd="unlink" title="Remove Link" class="ste-tb"><span class="dashicons dashicons-editor-unlink"></span></button>
                <button type="button" id="ste-btn-image" title="Insert Image" class="ste-tb"><span class="dashicons dashicons-format-image"></span></button>
                <button type="button" id="ste-btn-table" title="Insert Table" class="ste-tb" data-ste-feature="tableEditor"><span class="dashicons dashicons-grid-view"></span></button>
                <div class="ste-sep"></div>

                <label class="ste-tb-clr" title="Text color">
                    <span class="ste-clr-ico">A</span>
                    <input type="color" id="ste-fg-color" value="#e0e0e0">
                </label>
                <label class="ste-tb-clr" title="Highlight">
                    <span class="ste-clr-ico ste-clr-hl">H</span>
                    <input type="color" id="ste-bg-color" value="#ffff00">
                </label>
                <div class="ste-sep"></div>

                <button type="button" class="ste-tb ste-tb-fx" id="ste-quick-gradient" title="Quick Gradient" data-ste-feature="effects"><span style="background:linear-gradient(90deg,#ff6b6b,#48dbfb);-webkit-background-clip:text;-webkit-text-fill-color:transparent;font-weight:800;font-size:14px;">G</span></button>
                <button type="button" class="ste-tb ste-tb-fx" id="ste-quick-shadow" title="Quick Shadow" data-ste-feature="effects"><span style="text-shadow:2px 2px 0 #666;font-weight:800;font-size:14px;">S</span></button>
                <button type="button" class="ste-tb ste-tb-fx" id="ste-quick-3d" title="Quick 3D" data-ste-feature="effects"><span style="text-shadow:1px 1px 0 #888,2px 2px 0 #777;font-weight:800;font-size:12px;">3D</span></button>
                <button type="button" class="ste-tb ste-tb-fx" id="ste-quick-glow" title="Quick Glow" data-ste-feature="effects"><span style="text-shadow:0 0 8px #00ffff;color:#00ffff;font-weight:800;font-size:14px;">G</span></button>
                <button type="button" class="ste-tb ste-tb-fx" id="ste-btn-presets" title="Style Presets"><span class="dashicons dashicons-art"></span></button>
                <div class="ste-sep"></div>

                <button type="button" data-cmd="indent" title="Increase Indent" class="ste-tb"><span class="dashicons dashicons-editor-indent"></span></button>
                <button type="button" data-cmd="outdent" title="Decrease Indent" class="ste-tb"><span class="dashicons dashicons-editor-outdent"></span></button>
                <div class="ste-sep"></div>

                <button type="button" data-cmd="undo" title="Undo (Ctrl+Z)" class="ste-tb"><span class="dashicons dashicons-undo"></span></button>
                <button type="button" data-cmd="redo" title="Redo (Ctrl+Y)" class="ste-tb"><span class="dashicons dashicons-redo"></span></button>
                <div class="ste-sep"></div>

                <button type="button" id="ste-btn-remove-format" title="Clear Formatting" class="ste-tb"><span class="dashicons dashicons-editor-removeformatting"></span></button>
                <button type="button" id="ste-btn-paste-text" title="Paste as Text" class="ste-tb"><span class="dashicons dashicons-editor-paste-text"></span></button>
                <button type="button" id="ste-btn-charmap" title="Special Character" class="ste-tb">Ω</button>
                <button type="button" id="ste-btn-more" title="Insert Read More" class="ste-tb ste-tb-txt">More</button>
                <div class="ste-sep"></div>

                <button type="button" id="ste-btn-copy-style" title="Copy Style" class="ste-tb">🎨</button>
                <button type="button" id="ste-btn-paste-style" title="Paste Style" class="ste-tb">📋</button>
                <button type="button" id="ste-btn-source" title="HTML Source" class="ste-tb ste-tb-txt">&lt;/&gt;</button>
                <button type="button" id="ste-btn-export" title="Export HTML/CSS" class="ste-tb ste-tb-txt" data-ste-feature="exportCss">Export</button>
                <div class="ste-sep"></div>

                <button type="button" id="ste-btn-toggle-toolbar" title="Toggle Style Bar" class="ste-tb"><span class="dashicons dashicons-menu"></span></button>
                <button type="button" id="ste-btn-daynight" title="Toggle Day/Night Mode" class="ste-tb ste-tb-daynight"><span class="ste-dn-icon">☀️</span></button>
                <button type="button" id="ste-btn-shortcuts" title="Keyboard Shortcuts" class="ste-tb"><span class="dashicons dashicons-editor-help"></span></button>
            </div>

            <!-- ═══ ROW 2: Style Effects Bar ═══ -->
            <div id="ste-style-bar">
                <div id="ste-style-tabs">
                    <button type="button" class="ste-st<?php echo $plan !== 'free' ? ' active' : ''; ?>" data-tab="shadow" data-ste-feature="effects">Shadow</button>
                    <button type="button" class="ste-st" data-tab="gradient" data-ste-feature="effects">Gradient</button>
                    <button type="button" class="ste-st" data-tab="threed" data-ste-feature="effects">3D</button>
                    <button type="button" class="ste-st" data-tab="glow" data-ste-feature="effects">Glow</button>
                    <button type="button" class="ste-st" data-tab="anim" data-ste-feature="animations">Animation</button>
                    <button type="button" class="ste-st<?php echo $plan === 'free' ? ' active' : ''; ?>" data-tab="presets">Presets</button>
                </div>
                <div class="ste-sp<?php echo $plan !== 'free' ? ' active' : ''; ?>" data-panel="shadow">
                    <label>X <span class="ste-rv" data-for="shadow-x">0</span>px<input type="range" id="ste-shadow-x" min="-50" max="50" value="0"></label>
                    <label>Y <span class="ste-rv" data-for="shadow-y">0</span>px<input type="range" id="ste-shadow-y" min="-50" max="50" value="0"></label>
                    <label>Blur <span class="ste-rv" data-for="shadow-blur">0</span>px<input type="range" id="ste-shadow-blur" min="0" max="80" value="0"></label>
                    <label>Color<input type="color" id="ste-shadow-color" value="#000000"></label>
                    <button type="button" class="ste-apply" data-apply="shadow">Apply Shadow</button>
                    <button type="button" class="ste-clear-fx" data-clear="shadow">Clear</button>
                </div>
                <div class="ste-sp" data-panel="gradient">
                    <label>Color 1<input type="color" id="ste-grad-c1" value="#ff6b6b"></label>
                    <label>Color 2<input type="color" id="ste-grad-c2" value="#48dbfb"></label>
                    <label>Color 3<input type="color" id="ste-grad-c3" value="#feca57"></label>
                    <label>Angle <span class="ste-rv" data-for="grad-angle">90</span>°<input type="range" id="ste-grad-angle" min="0" max="360" value="90"></label>
                    <button type="button" class="ste-apply" data-apply="gradient">Apply Gradient</button>
                    <button type="button" class="ste-clear-fx" data-clear="gradient">Clear</button>
                </div>
                <div class="ste-sp" data-panel="threed">
                    <label>Depth <span class="ste-rv" data-for="3d-depth">5</span><input type="range" id="ste-3d-depth" min="1" max="20" value="5"></label>
                    <label>Color<input type="color" id="ste-3d-color" value="#aaaaaa"></label>
                    <button type="button" class="ste-apply" data-apply="threed">Apply 3D</button>
                    <button type="button" class="ste-clear-fx" data-clear="threed">Clear</button>
                </div>
                <div class="ste-sp" data-panel="glow">
                    <label>Color<input type="color" id="ste-glow-color" value="#00ffff"></label>
                    <label>Intensity <span class="ste-rv" data-for="glow-int">10</span><input type="range" id="ste-glow-int" min="1" max="60" value="10"></label>
                    <button type="button" class="ste-apply" data-apply="glow">Apply Glow</button>
                    <button type="button" class="ste-clear-fx" data-clear="glow">Clear</button>
                </div>
                <div class="ste-sp" data-panel="anim">
                    <label>Type<select id="ste-anim-type"><option value="">None</option><option value="fade">Fade</option><option value="slide-up">Slide Up</option><option value="slide-down">Slide Down</option><option value="slide-left">Slide Left</option><option value="slide-right">Slide Right</option><option value="reveal">Reveal</option><option value="bounce">Bounce</option><option value="zoom-in">Zoom In</option><option value="zoom-out">Zoom Out</option><option value="flip">Flip</option><option value="typewriter">Typewriter</option></select></label>
                    <label>Duration <span class="ste-rv" data-for="anim-dur">0.6</span>s<input type="range" id="ste-anim-dur" min="0.1" max="3" step="0.1" value="0.6"></label>
                    <button type="button" class="ste-apply" data-apply="animation">Apply</button>
                    <button type="button" class="ste-clear-fx" id="ste-anim-preview">Preview</button>
                    <button type="button" class="ste-clear-fx" data-clear="animation">Clear</button>
                </div>
                <div class="ste-sp<?php echo $plan === 'free' ? ' active' : ''; ?>" data-panel="presets">
                    <div id="ste-presets-list"></div>
                    <div class="ste-preset-row"><input type="text" id="ste-preset-name" placeholder="Preset name…"><button type="button" class="ste-apply" id="ste-preset-save">Save</button></div>
                </div>
            </div>

            <!-- ═══ ROW 3: Editor Canvas ═══ -->
            <div id="ste-editor" contenteditable="true" spellcheck="true" data-placeholder="Start typing your content here…"><?php
                if ( $display_content ) echo wp_kses_post( $display_content );
            ?></div>

            <!-- Source code editor (hidden) -->
            <textarea id="ste-source-editor" spellcheck="false" style="display:none;"></textarea>

            <!-- Hidden textarea for WP save -->
            <textarea id="content" name="content" style="display:none;" cols="40" rows="1"><?php echo esc_textarea( $content ); ?></textarea>

            <div id="ste-editor-footer">
                <span id="ste-word-count">0 words</span>
                <span>Created by Meet Patel <span class="ste-plan-badge ste-plan-badge-<?php echo esc_attr( $plan ); ?>"><?php echo esc_html( $plan_data['label'] ); ?> Plan</span></span>
            </div>
        </div>

        <!-- Floating toolbar -->
        <div id="ste-float-toolbar" class="ste-hidden">
            <button type="button" data-cmd="bold" class="ste-ft"><b>B</b></button>
            <button type="button" data-cmd="italic" class="ste-ft"><i>I</i></button>
            <button type="button" data-cmd="underline" class="ste-ft"><u>U</u></button>
            <span class="ste-ft-sep"></span>
            <label class="ste-ft-clr"><span>A</span><input type="color" id="ste-ft-color" value="#333333"></label>
            <button type="button" id="ste-ft-shadow" class="ste-ft" title="Shadow">S</button>
            <button type="button" id="ste-ft-gradient" class="ste-ft" title="Gradient">G</button>
            <button type="button" id="ste-ft-link" class="ste-ft" title="Link">🔗</button>
        </div>

        <!-- Table picker popover -->
        <div id="ste-table-picker" class="ste-hidden">
            <div class="ste-tp-section">
                <div class="ste-tp-title">Insert Table</div>
                <div id="ste-table-grid"></div>
                <div id="ste-table-label">1 × 1</div>
            </div>
            <div class="ste-tp-section">
                <div class="ste-tp-title">Border Style</div>
                <div class="ste-tp-row">
                    <select id="ste-tbl-border-style">
                        <option value="solid">Solid</option>
                        <option value="dashed">Dashed</option>
                        <option value="dotted">Dotted</option>
                        <option value="double">Double</option>
                        <option value="none">None</option>
                    </select>
                    <input type="number" id="ste-tbl-border-width" value="1" min="0" max="10" title="Border width (px)">
                    <input type="color" id="ste-tbl-border-color" value="#dddddd" title="Border color">
                </div>
            </div>
            <div class="ste-tp-section">
                <div class="ste-tp-title">Cell Padding</div>
                <div class="ste-tp-row">
                    <input type="number" id="ste-tbl-padding" value="8" min="0" max="40" title="Padding (px)"> px
                </div>
            </div>
            <div class="ste-tp-section">
                <div class="ste-tp-title">Header Row</div>
                <div class="ste-tp-row">
                    <label class="ste-tp-check"><input type="checkbox" id="ste-tbl-header" checked> Include header row</label>
                </div>
            </div>
        </div>

        <!-- Table context toolbar (appears when cursor is in a table) -->
        <div id="ste-table-ctx" class="ste-hidden">
            <button type="button" class="ste-tc-btn" data-taction="row-above" title="Insert row above">↑ Row</button>
            <button type="button" class="ste-tc-btn" data-taction="row-below" title="Insert row below">↓ Row</button>
            <button type="button" class="ste-tc-btn" data-taction="col-left" title="Insert column left">← Col</button>
            <button type="button" class="ste-tc-btn" data-taction="col-right" title="Insert column right">→ Col</button>
            <span class="ste-tc-sep"></span>
            <button type="button" class="ste-tc-btn" data-taction="del-row" title="Delete row">✕ Row</button>
            <button type="button" class="ste-tc-btn" data-taction="del-col" title="Delete column">✕ Col</button>
            <span class="ste-tc-sep"></span>
            <button type="button" class="ste-tc-btn" data-taction="merge" title="Merge selected cells">Merge</button>
            <button type="button" class="ste-tc-btn" data-taction="split" title="Split cell">Split</button>
            <span class="ste-tc-sep"></span>
            <label class="ste-tc-clr" title="Cell background">
                <span>BG</span>
                <input type="color" id="ste-tc-bg" value="#ffffff">
            </label>
            <label class="ste-tc-clr" title="Border color">
                <span>Bd</span>
                <input type="color" id="ste-tc-border" value="#dddddd">
            </label>
            <span class="ste-tc-sep"></span>
            <button type="button" class="ste-tc-btn ste-tc-del" data-taction="del-table" title="Delete table">🗑 Table</button>
        </div>

        <!-- Charmap modal -->
        <div id="ste-charmap-modal" class="ste-modal ste-hidden">
            <div class="ste-modal-box" style="width:440px;">
                <div class="ste-modal-head"><h3>Special Characters</h3><button type="button" class="ste-modal-x">&times;</button></div>
                <div id="ste-charmap-grid"></div>
            </div>
        </div>

        <!-- Shortcuts modal -->
        <div id="ste-shortcuts-modal" class="ste-modal ste-hidden">
            <div class="ste-modal-box" style="width:480px;">
                <div class="ste-modal-head"><h3>Keyboard Shortcuts</h3><button type="button" class="ste-modal-x">&times;</button></div>
                <div style="padding:16px;max-height:50vh;overflow-y:auto;">
                    <table class="ste-shortcuts-table">
                        <tr><td><kbd>Ctrl+B</kbd></td><td>Bold</td></tr>
                        <tr><td><kbd>Ctrl+I</kbd></td><td>Italic</td></tr>
                        <tr><td><kbd>Ctrl+U</kbd></td><td>Underline</td></tr>
                        <tr><td><kbd>Ctrl+K</kbd></td><td>Insert Link</td></tr>
                        <tr><td><kbd>Ctrl+Z</kbd></td><td>Undo</td></tr>
                        <tr><td><kbd>Ctrl+Y</kbd></td><td>Redo</td></tr>
                        <tr><td><kbd>Ctrl+Shift+7</kbd></td><td>Ordered List</td></tr>
                        <tr><td><kbd>Ctrl+Shift+8</kbd></td><td>Unordered List</td></tr>
                        <tr><td><kbd>Ctrl+Shift+D</kbd></td><td>Strikethrough</td></tr>
                        <tr><td><kbd>Ctrl+Shift+X</kbd></td><td>Clear Formatting</td></tr>
                        <tr><td><kbd>Ctrl+S</kbd></td><td>Save</td></tr>
                        <tr><td><kbd>Tab</kbd></td><td>Next table cell</td></tr>
                        <tr><td><kbd>Shift+Tab</kbd></td><td>Previous table cell</td></tr>
                        <tr><td><kbd>Escape</kbd></td><td>Close modal / dialog</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Export modal -->
        <div id="ste-export-modal" class="ste-modal ste-hidden">
            <div class="ste-modal-box">
                <div class="ste-modal-head"><h3>Export HTML / CSS</h3><button type="button" class="ste-modal-x">&times;</button></div>
                <div class="ste-modal-tabs"><button type="button" class="ste-mt active" data-target="html">HTML</button><button type="button" class="ste-mt" data-target="css">CSS</button></div>
                <pre id="ste-export-html" class="ste-code active"></pre>
                <pre id="ste-export-css" class="ste-code"></pre>
                <div class="ste-modal-foot"><button type="button" id="ste-export-copy" class="ste-apply">Copy to Clipboard</button></div>
            </div>
        </div>

        <!-- Upgrade modal -->
        <div id="ste-upgrade-modal" class="ste-modal ste-hidden">
            <div class="ste-modal-box" style="width:440px;">
                <div class="ste-modal-head"><h3 id="ste-upgrade-title">Upgrade Required</h3><button type="button" class="ste-modal-x">&times;</button></div>
                <div style="padding:24px;text-align:center;">
                    <div style="font-size:48px;margin-bottom:12px;">&#128274;</div>
                    <p id="ste-upgrade-msg" style="color:#666;font-size:14px;line-height:1.6;margin:0 0 20px;">This feature is available on the <strong>Pro</strong> plan and above.</p>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=ste-license' ) ); ?>" class="ste-apply" style="display:inline-block;padding:10px 28px;text-decoration:none;font-size:14px;">Upgrade Now</a>
                    <p style="color:#999;font-size:12px;margin-top:12px;">Current plan: <strong><?php echo esc_html( $plan_data['label'] ); ?></strong></p>
                </div>
            </div>
        </div>
        <?php
    }

    public static function enforce_plan_on_save( $post_id, $post ) {
        if ( ! in_array( $post->post_type, self::$post_types, true ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;

        $plan_data = STE_License::get_plan_data();
        $content   = $post->post_content;
        $changed   = false;

        // Strip animations if not available
        if ( empty( $plan_data['animations'] ) ) {
            $new = preg_replace( '/\s*data-ste-anim="[^"]*"/', '', $content );
            $new = preg_replace( '/\s*data-ste-anim-dur="[^"]*"/', '', $new );
            if ( $new !== $content ) { $content = $new; $changed = true; }
        }

        // Strip gradient text styles if effects not available
        if ( empty( $plan_data['effects'] ) ) {
            // Remove background-clip gradient text effect
            $new = preg_replace( '/\s*-webkit-background-clip:\s*text;?/', '', $content );
            $new = preg_replace( '/\s*background-clip:\s*text;?/', '', $new );
            $new = preg_replace( '/\s*-webkit-text-fill-color:\s*[^;]+;?/', '', $new );
            // Remove text-shadow (used by shadow, 3D, glow)
            $new = preg_replace( '/\s*text-shadow:\s*[^;]+;?/', '', $new );
            if ( $new !== $content ) { $content = $new; $changed = true; }
        }

        // Strip tables if table editor not available
        if ( empty( $plan_data['table_editor'] ) ) {
            $new = preg_replace( '/<table[^>]*>[\s\S]*?<\/table>/i', '', $content );
            if ( $new !== $content ) { $content = $new; $changed = true; }
        }

        if ( $changed ) {
            remove_action( 'save_post', array( __CLASS__, 'enforce_plan_on_save' ), 10 );
            wp_update_post( array( 'ID' => $post_id, 'post_content' => $content ) );
            add_action( 'save_post', array( __CLASS__, 'enforce_plan_on_save' ), 10, 2 );
        }
    }

    public static function footer_credit() {
        $screen = get_current_screen();
        if ( $screen && in_array( $screen->post_type, self::$post_types, true ) && 'post' === $screen->base ) {
            /* translators: %s: plugin version number */
            echo '<div style="text-align:center;padding:8px;color:#999;font-size:11px;">' . sprintf( esc_html__( 'Smart Text Editor v%s — Created by Meet Patel', 'smart-text-editor' ), esc_html( STE_VERSION ) ) . '</div>';
        }
    }
}
