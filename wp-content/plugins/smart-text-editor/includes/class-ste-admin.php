<?php
/**
 * Admin menu — About page only. The editor itself is on post/page screens.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class STE_Admin {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'add_menu' ) );
        add_action( 'admin_notices', array( __CLASS__, 'fluentsmtp_notice' ) );
        add_action( 'admin_notices', array( __CLASS__, 'smtp_config_notice' ) );
        add_filter( 'plugin_action_links_' . STE_PLUGIN_BASENAME, array( __CLASS__, 'action_links' ) );
    }

    /* ── FluentSMTP dependency notice ── */

    public static function check_fluentsmtp() {
        if ( ! function_exists( 'is_plugin_active' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        return is_plugin_active( 'fluent-smtp/fluent-smtp.php' );
    }

    public static function fluentsmtp_notice() {
        if ( self::check_fluentsmtp() ) return;

        if ( file_exists( WP_PLUGIN_DIR . '/fluent-smtp/fluent-smtp.php' ) ) {
            $btn_url   = wp_nonce_url(
                admin_url( 'plugins.php?action=activate&plugin=fluent-smtp/fluent-smtp.php' ),
                'activate-plugin_fluent-smtp/fluent-smtp.php'
            );
            $btn_label = __( 'Activate FluentSMTP', 'smart-text-editor' );
        } else {
            $btn_url   = wp_nonce_url(
                admin_url( 'update.php?action=install-plugin&plugin=fluent-smtp' ),
                'install-plugin_fluent-smtp'
            );
            $btn_label = __( 'Install FluentSMTP', 'smart-text-editor' );
        }
        ?>
        <div class="notice notice-error" style="padding:14px 18px;border-left-color:#dc2626;">
            <p style="font-size:14px;margin:0;">
                <?php
                printf(
                    /* translators: 1: opening strong, 2: closing strong, 3: button HTML */
                    wp_kses(
                        __( '%1$sSmart Text Editor%2$s requires %1$sFluentSMTP%2$s to send emails (license keys, order confirmations, etc.). Please install and activate it. %3$s', 'smart-text-editor' ),
                        array( 'strong' => array() )
                    ),
                    '<strong>',
                    '</strong>',
                    '<a href="' . esc_url( $btn_url ) . '" class="button button-primary" style="margin-left:12px;vertical-align:middle;">' . esc_html( $btn_label ) . '</a>'
                );
                ?>
            </p>
        </div>
        <?php
    }

    public static function smtp_config_notice() {
        if ( ! self::check_fluentsmtp() ) return;
        if ( ! class_exists( 'STE_Checkout' ) ) return;
        if ( STE_Checkout::is_smtp_configured() ) return;
        ?>
        <div class="notice notice-warning" style="padding:12px 18px;border-left-color:#f59e0b;">
            <p style="font-size:13px;margin:0;">
                <?php
                printf(
                    /* translators: 1: opening strong, 2: closing strong, 3: settings link */
                    wp_kses(
                        __( '%1$sSmart Text Editor:%2$s FluentSMTP is active but no sender email is configured. Purchases are blocked until email delivery is set up. %3$s', 'smart-text-editor' ),
                        array( 'strong' => array() )
                    ),
                    '<strong>',
                    '</strong>',
                    '<a href="' . esc_url( admin_url( 'options-general.php?page=fluent-mail#/' ) ) . '" style="margin-left:6px;">' . esc_html__( 'Configure FluentSMTP &rarr;', 'smart-text-editor' ) . '</a>'
                );
                ?>
            </p>
        </div>
        <?php
    }

    /* ── Plugin action link ── */

    public static function action_links( $links ) {
        array_unshift( $links, '<a href="' . esc_url( admin_url( 'post-new.php?post_type=page' ) ) . '">' . esc_html__( 'Create Page', 'smart-text-editor' ) . '</a>' );
        return $links;
    }

    public static function add_menu() {
        add_menu_page(
            __( 'Smart Text Editor', 'smart-text-editor' ),
            __( 'Smart Editor', 'smart-text-editor' ),
            'edit_posts',
            'smart-text-editor',
            array( __CLASS__, 'render_about' ),
            'dashicons-edit-page',
            26
        );
    }

    public static function render_about() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Smart Text Editor by Meet Patel', 'smart-text-editor' ); ?></h1>

            <div style="max-width:750px;margin-top:20px;display:flex;flex-direction:column;gap:20px;">

                <!-- How to Use -->
                <div style="padding:24px 28px;background:#fff;border:1px solid #e5e5e5;border-radius:8px;">
                    <h2 style="margin-top:0;font-size:17px;"><?php esc_html_e( 'Getting Started', 'smart-text-editor' ); ?></h2>
                    <p style="color:#555;">The Smart Text Editor replaces the default WordPress editor on <strong>all pages and posts</strong>.</p>
                    <ol style="color:#555;line-height:1.8;">
                        <li>Go to <strong>Pages &gt; Add New</strong> or <strong>Posts &gt; Add New</strong></li>
                        <li>You will see the custom editor with the formatting toolbar</li>
                        <li>Type your content, select text, and use the <strong>style controls</strong> below the editor</li>
                        <li>Click <strong>Publish</strong> or <strong>Update</strong> as usual</li>
                    </ol>
                </div>

                <!-- Rich Text Editing -->
                <div style="padding:24px 28px;background:#fff;border:1px solid #e5e5e5;border-radius:8px;">
                    <h2 style="margin-top:0;font-size:17px;"><?php esc_html_e( 'Rich Text Editing', 'smart-text-editor' ); ?></h2>
                    <ul style="list-style:disc;padding-left:20px;color:#555;line-height:1.9;">
                        <li>Custom contenteditable editor — no TinyMCE, no Gutenberg</li>
                        <li>Bold, italic, underline, strikethrough, subscript, superscript</li>
                        <li>Headings (H1–H6), paragraphs, preformatted text</li>
                        <li>Bullet lists, numbered lists, blockquotes</li>
                        <li>Text alignment — left, center, right, justify</li>
                        <li>Font family picker (50+ Google Fonts) and font size selector (8px–96px)</li>
                        <li>Text color and highlight/background color</li>
                        <li>Indent / outdent</li>
                        <li>Undo / redo</li>
                    </ul>
                </div>

                <!-- Insert & Media -->
                <div style="padding:24px 28px;background:#fff;border:1px solid #e5e5e5;border-radius:8px;">
                    <h2 style="margin-top:0;font-size:17px;"><?php esc_html_e( 'Insert & Media', 'smart-text-editor' ); ?></h2>
                    <ul style="list-style:disc;padding-left:20px;color:#555;line-height:1.9;">
                        <li>Insert links (Ctrl+K) and unlink</li>
                        <li>Insert images from WordPress Media Library or drag &amp; drop from desktop</li>
                        <li>Insert tables with customizable grid size, border style, padding, and header row</li>
                        <li>Table context toolbar — add/delete rows &amp; columns, merge/split cells, cell colors</li>
                        <li>Insert horizontal rule</li>
                        <li>Insert Read More tag</li>
                        <li>Special characters picker (65+ symbols)</li>
                    </ul>
                </div>

                <!-- Style Effects -->
                <div style="padding:24px 28px;background:#fff;border:1px solid #e5e5e5;border-radius:8px;">
                    <h2 style="margin-top:0;font-size:17px;"><?php esc_html_e( 'Style Effects', 'smart-text-editor' ); ?></h2>
                    <ul style="list-style:disc;padding-left:20px;color:#555;line-height:1.9;">
                        <li><strong>Text Shadow</strong> — adjustable X, Y, blur, and color</li>
                        <li><strong>Gradient Text</strong> — 3-color gradient with angle control</li>
                        <li><strong>3D Text</strong> — depth and color for layered shadow effect</li>
                        <li><strong>Glow</strong> — color and intensity for neon-style glow</li>
                        <li><strong>Copy / Paste Style</strong> — copy styles from one text and apply to another</li>
                        <li><strong>Clear Formatting</strong> — strip all formatting in one click</li>
                    </ul>
                </div>

                <!-- Animations -->
                <div style="padding:24px 28px;background:#fff;border:1px solid #e5e5e5;border-radius:8px;">
                    <h2 style="margin-top:0;font-size:17px;"><?php esc_html_e( 'Scroll Animations', 'smart-text-editor' ); ?></h2>
                    <p style="color:#555;">Apply scroll-triggered animations to any text. Elements animate in when they enter the viewport.</p>
                    <ul style="list-style:disc;padding-left:20px;color:#555;line-height:1.9;">
                        <li>11 animation types — Fade, Slide Up, Slide Down, Slide Left, Slide Right, Reveal, Bounce, Zoom In, Zoom Out, Flip, Typewriter</li>
                        <li>Adjustable duration (0.1s – 3s)</li>
                        <li>Preview button to test animations in the editor</li>
                        <li>Powered by IntersectionObserver for optimal performance</li>
                    </ul>
                </div>

                <!-- Presets -->
                <div style="padding:24px 28px;background:#fff;border:1px solid #e5e5e5;border-radius:8px;">
                    <h2 style="margin-top:0;font-size:17px;"><?php esc_html_e( 'Style Presets', 'smart-text-editor' ); ?></h2>
                    <p style="color:#555;">48 built-in presets organized by category, plus save your own custom presets.</p>
                    <ul style="list-style:disc;padding-left:20px;color:#555;line-height:1.9;">
                        <li><strong>Glow &amp; Neon</strong> — Neon Glow, Neon Pink, Neon Orange, Cyber Green, Frosted, Fire, Electric Blue</li>
                        <li><strong>Gradients</strong> — Gold Luxury, Ocean Wave, Sunset, Candy, Rainbow, Nature, Rose Gold, Aurora, Midnight, Peach, Berry, Chrome</li>
                        <li><strong>3D &amp; Shadow</strong> — Retro Pop, Deep Shadow, Long Shadow, Emboss, Letterpress, Comic 3D</li>
                        <li><strong>Outline &amp; Stroke</strong> — Outline, Outline Red, Outline Gold, Thick Outline</li>
                        <li><strong>Typography</strong> — Elegant Serif, Typewriter, Modern Clean, Bold Impact, Handwritten, Small Caps</li>
                        <li><strong>Colors</strong> — Blood Red, Royal Purple, Forest Green, Coral, Steel Blue</li>
                        <li><strong>Special</strong> — Highlight Yellow/Green/Blue, Tag Dark/Blue, Underline Accent, Strikethrough Red, Wavy Underline</li>
                    </ul>
                </div>

                <!-- Tools & Extras -->
                <div style="padding:24px 28px;background:#fff;border:1px solid #e5e5e5;border-radius:8px;">
                    <h2 style="margin-top:0;font-size:17px;"><?php esc_html_e( 'Tools & Extras', 'smart-text-editor' ); ?></h2>
                    <ul style="list-style:disc;padding-left:20px;color:#555;line-height:1.9;">
                        <li>Floating toolbar — appears on text selection for quick formatting</li>
                        <li>HTML source code view with syntax-highlighted dark editor</li>
                        <li>HTML/CSS export — copy clean markup for use outside WordPress</li>
                        <li>Paste as plain text toggle</li>
                        <li>Fullscreen distraction-free mode (F11)</li>
                        <li>Word and character count</li>
                        <li>Collapsible style effects bar</li>
                        <li><code>[ste_doc id="123"]</code> shortcode — embed another page's styled content</li>
                    </ul>
                </div>

                <!-- Keyboard Shortcuts -->
                <div style="padding:24px 28px;background:#fff;border:1px solid #e5e5e5;border-radius:8px;">
                    <h2 style="margin-top:0;font-size:17px;"><?php esc_html_e( 'Keyboard Shortcuts', 'smart-text-editor' ); ?></h2>
                    <table style="width:100%;border-collapse:collapse;font-size:13px;color:#555;">
                        <tr><td style="padding:5px 10px;border-bottom:1px solid #f0f0f0;width:180px;"><kbd style="background:#f5f5f5;border:1px solid #ddd;padding:1px 6px;border-radius:3px;font-family:monospace;font-size:12px;">Ctrl+B</kbd></td><td style="padding:5px 10px;border-bottom:1px solid #f0f0f0;">Bold</td></tr>
                        <tr><td style="padding:5px 10px;border-bottom:1px solid #f0f0f0;"><kbd style="background:#f5f5f5;border:1px solid #ddd;padding:1px 6px;border-radius:3px;font-family:monospace;font-size:12px;">Ctrl+I</kbd></td><td style="padding:5px 10px;border-bottom:1px solid #f0f0f0;">Italic</td></tr>
                        <tr><td style="padding:5px 10px;border-bottom:1px solid #f0f0f0;"><kbd style="background:#f5f5f5;border:1px solid #ddd;padding:1px 6px;border-radius:3px;font-family:monospace;font-size:12px;">Ctrl+U</kbd></td><td style="padding:5px 10px;border-bottom:1px solid #f0f0f0;">Underline</td></tr>
                        <tr><td style="padding:5px 10px;border-bottom:1px solid #f0f0f0;"><kbd style="background:#f5f5f5;border:1px solid #ddd;padding:1px 6px;border-radius:3px;font-family:monospace;font-size:12px;">Ctrl+K</kbd></td><td style="padding:5px 10px;border-bottom:1px solid #f0f0f0;">Insert Link</td></tr>
                        <tr><td style="padding:5px 10px;border-bottom:1px solid #f0f0f0;"><kbd style="background:#f5f5f5;border:1px solid #ddd;padding:1px 6px;border-radius:3px;font-family:monospace;font-size:12px;">Ctrl+S</kbd></td><td style="padding:5px 10px;border-bottom:1px solid #f0f0f0;">Save / Publish</td></tr>
                        <tr><td style="padding:5px 10px;border-bottom:1px solid #f0f0f0;"><kbd style="background:#f5f5f5;border:1px solid #ddd;padding:1px 6px;border-radius:3px;font-family:monospace;font-size:12px;">Ctrl+Z / Y</kbd></td><td style="padding:5px 10px;border-bottom:1px solid #f0f0f0;">Undo / Redo</td></tr>
                        <tr><td style="padding:5px 10px;border-bottom:1px solid #f0f0f0;"><kbd style="background:#f5f5f5;border:1px solid #ddd;padding:1px 6px;border-radius:3px;font-family:monospace;font-size:12px;">Ctrl+Shift+D</kbd></td><td style="padding:5px 10px;border-bottom:1px solid #f0f0f0;">Strikethrough</td></tr>
                        <tr><td style="padding:5px 10px;border-bottom:1px solid #f0f0f0;"><kbd style="background:#f5f5f5;border:1px solid #ddd;padding:1px 6px;border-radius:3px;font-family:monospace;font-size:12px;">Ctrl+Shift+X</kbd></td><td style="padding:5px 10px;border-bottom:1px solid #f0f0f0;">Clear Formatting</td></tr>
                        <tr><td style="padding:5px 10px;border-bottom:1px solid #f0f0f0;"><kbd style="background:#f5f5f5;border:1px solid #ddd;padding:1px 6px;border-radius:3px;font-family:monospace;font-size:12px;">Ctrl+Shift+7 / 8</kbd></td><td style="padding:5px 10px;border-bottom:1px solid #f0f0f0;">Ordered / Unordered List</td></tr>
                        <tr><td style="padding:5px 10px;border-bottom:1px solid #f0f0f0;"><kbd style="background:#f5f5f5;border:1px solid #ddd;padding:1px 6px;border-radius:3px;font-family:monospace;font-size:12px;">Tab / Shift+Tab</kbd></td><td style="padding:5px 10px;border-bottom:1px solid #f0f0f0;">Navigate table cells</td></tr>
                        <tr><td style="padding:5px 10px;border-bottom:1px solid #f0f0f0;"><kbd style="background:#f5f5f5;border:1px solid #ddd;padding:1px 6px;border-radius:3px;font-family:monospace;font-size:12px;">F11</kbd></td><td style="padding:5px 10px;border-bottom:1px solid #f0f0f0;">Fullscreen toggle</td></tr>
                        <tr><td style="padding:5px 10px;"><kbd style="background:#f5f5f5;border:1px solid #ddd;padding:1px 6px;border-radius:3px;font-family:monospace;font-size:12px;">Escape</kbd></td><td style="padding:5px 10px;">Close modal / dialog</td></tr>
                    </table>
                </div>

                <!-- Plugin Info -->
                <div style="padding:24px 28px;background:#fff;border:1px solid #e5e5e5;border-radius:8px;">
                    <p style="margin:0;"><strong>Version:</strong> <?php echo esc_html( STE_VERSION ); ?></p>
                    <p style="margin:8px 0 0;"><strong>Author:</strong> Meet Patel</p>
                    <p style="margin:8px 0 0;"><strong>License:</strong> GPL-2.0-or-later</p>
                    <p style="color:#999;font-size:12px;margin:16px 0 0;">Created by Meet Patel</p>
                </div>

            </div>
        </div>
        <?php
    }
}
