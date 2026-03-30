<?php
/**
 * Plan / License management for Smart Text Editor.
 * Free plan is always available. Pro and Business require a valid license key
 * obtained through purchase. Keys are validated and stored in wp_options.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class STE_License {

    /* ── Plan definitions ── */
    private static $plans = array(
        'free' => array(
            'label'             => 'Free',
            'price'             => '$0',
            'price_num'         => 0,
            'max_fonts'         => 10,
            'max_presets'       => 5,
            'effects'           => false,
            'animations'        => false,
            'table_editor'      => false,
            'export_css'        => false,
            'custom_presets'    => false,
            'custom_fonts'      => false,
            'whitelabel'        => false,
        ),
        'pro' => array(
            'label'             => 'Pro',
            'price'             => '$5/mo',
            'price_num'         => 5,
            'max_fonts'         => 999,
            'max_presets'       => 36,
            'effects'           => true,
            'animations'        => true,
            'table_editor'      => true,
            'export_css'        => true,
            'custom_presets'    => false,
            'custom_fonts'      => false,
            'whitelabel'        => false,
        ),
        'business' => array(
            'label'             => 'Business',
            'price'             => '$15/mo',
            'price_num'         => 15,
            'max_fonts'         => 999,
            'max_presets'       => 999,
            'effects'           => true,
            'animations'        => true,
            'table_editor'      => true,
            'export_css'        => true,
            'custom_presets'    => true,
            'custom_fonts'      => true,
            'whitelabel'        => true,
        ),
    );

    /* Free-tier font whitelist (first 10 popular fonts) */
    private static $free_fonts = array(
        "'Inter', sans-serif",
        "'Poppins', sans-serif",
        "'Roboto', sans-serif",
        "'Open Sans', sans-serif",
        "'Lato', sans-serif",
        "'Playfair Display', serif",
        "Georgia, 'Times New Roman', serif",
        "Arial, Helvetica, sans-serif",
        "'Courier New', Courier, monospace",
        "'Montserrat', sans-serif",
    );

    /**
     * Valid license key prefixes per plan.
     * In production, these would be validated against a remote API.
     * For now we use a local prefix + hash check.
     */
    private static $key_prefixes = array(
        'pro'      => 'STE-PRO-',
        'business' => 'STE-BIZ-',
    );

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'add_submenu' ), 20 );
        add_action( 'admin_post_ste_activate_license', array( __CLASS__, 'handle_activate' ) );
        add_action( 'admin_post_ste_deactivate_license', array( __CLASS__, 'handle_deactivate' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );
    }

    /* ── Getters ── */

    public static function get_plan() {
        $plan = get_option( 'ste_active_plan', 'free' );
        if ( ! isset( self::$plans[ $plan ] ) ) return 'free';
        // For paid plans, verify license key still exists
        if ( 'free' !== $plan ) {
            $key = get_option( 'ste_license_key', '' );
            if ( empty( $key ) || ! self::validate_key( $key, $plan ) ) {
                update_option( 'ste_active_plan', 'free' );
                return 'free';
            }
        }
        return $plan;
    }

    public static function get_plan_data( $plan = null ) {
        if ( null === $plan ) $plan = self::get_plan();
        return isset( self::$plans[ $plan ] ) ? self::$plans[ $plan ] : self::$plans['free'];
    }

    public static function get_all_plans() {
        return self::$plans;
    }

    public static function can( $feature ) {
        $data = self::get_plan_data();
        return ! empty( $data[ $feature ] );
    }

    public static function get_limit( $key ) {
        $data = self::get_plan_data();
        return isset( $data[ $key ] ) ? $data[ $key ] : 0;
    }

    public static function get_free_fonts() {
        return self::$free_fonts;
    }

    public static function js_config() {
        $plan = self::get_plan();
        $data = self::get_plan_data();
        return array(
            'plan'          => $plan,
            'label'         => $data['label'],
            'maxFonts'      => $data['max_fonts'],
            'maxPresets'    => $data['max_presets'],
            'effects'       => $data['effects'],
            'animations'    => $data['animations'],
            'tableEditor'   => $data['table_editor'],
            'exportCss'     => $data['export_css'],
            'customPresets' => $data['custom_presets'],
            'freeFonts'     => self::$free_fonts,
        );
    }

    /* ── License key validation ── */

    /**
     * Validate a license key for a given plan.
     * Format: STE-PRO-XXXX-XXXX-XXXX or STE-BIZ-XXXX-XXXX-XXXX
     * The key must start with the correct prefix and have a valid checksum.
     */
    public static function validate_key( $key, $plan = null ) {
        $key = strtoupper( trim( $key ) );
        if ( empty( $key ) ) return false;

        // Determine plan from key prefix if not provided
        if ( null === $plan ) {
            $plan = self::plan_from_key( $key );
        }

        if ( ! $plan || 'free' === $plan ) return false;
        if ( ! isset( self::$key_prefixes[ $plan ] ) ) return false;

        $prefix = self::$key_prefixes[ $plan ];
        if ( strpos( $key, $prefix ) !== 0 ) return false;

        // Key format: PREFIX + 3 groups of 4 alphanumeric chars separated by dashes
        $pattern = '/^' . preg_quote( $prefix, '/' ) . '[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/';
        if ( ! preg_match( $pattern, $key ) ) return false;

        // Checksum: last 4 chars must match a hash derived from the rest
        $body     = substr( $key, 0, -4 );
        $expected = strtoupper( substr( md5( $body . 'ste_salt_2024' ), 0, 4 ) );
        $actual   = substr( $key, -4 );

        return $actual === $expected;
    }

    /**
     * Determine plan type from a license key prefix.
     */
    public static function plan_from_key( $key ) {
        $key = strtoupper( trim( $key ) );
        foreach ( self::$key_prefixes as $plan => $prefix ) {
            if ( strpos( $key, $prefix ) === 0 ) return $plan;
        }
        return null;
    }

    /**
     * Generate a valid license key for a given plan (used for purchase).
     */
    public static function generate_key( $plan ) {
        if ( ! isset( self::$key_prefixes[ $plan ] ) ) return '';
        $prefix = self::$key_prefixes[ $plan ];
        $seg1   = strtoupper( substr( bin2hex( random_bytes( 2 ) ), 0, 4 ) );
        $seg2   = strtoupper( substr( bin2hex( random_bytes( 2 ) ), 0, 4 ) );
        $body   = $prefix . $seg1 . '-' . $seg2 . '-';
        $check  = strtoupper( substr( md5( $body . 'ste_salt_2024' ), 0, 4 ) );
        return $body . $check;
    }

    /* ── Handle activation ── */

    public static function handle_activate() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
        check_admin_referer( 'ste_license_action' );

        $key = isset( $_POST['ste_license_key'] ) ? sanitize_text_field( wp_unslash( $_POST['ste_license_key'] ) ) : '';
        $key = strtoupper( trim( $key ) );

        if ( empty( $key ) ) {
            wp_redirect( add_query_arg( 'ste_msg', 'empty_key', admin_url( 'admin.php?page=ste-license' ) ) );
            exit;
        }

        $plan = self::plan_from_key( $key );

        if ( ! $plan || ! self::validate_key( $key, $plan ) ) {
            wp_redirect( add_query_arg( 'ste_msg', 'invalid_key', admin_url( 'admin.php?page=ste-license' ) ) );
            exit;
        }

        update_option( 'ste_active_plan', $plan );
        update_option( 'ste_license_key', $key );
        update_option( 'ste_license_activated', current_time( 'mysql' ) );

        wp_redirect( add_query_arg( 'ste_msg', 'activated', admin_url( 'admin.php?page=ste-license' ) ) );
        exit;
    }

    /* ── Handle deactivation ── */

    public static function handle_deactivate() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
        check_admin_referer( 'ste_license_action' );

        delete_option( 'ste_license_key' );
        delete_option( 'ste_license_activated' );
        update_option( 'ste_active_plan', 'free' );

        wp_redirect( add_query_arg( 'ste_msg', 'deactivated', admin_url( 'admin.php?page=ste-license' ) ) );
        exit;
    }

    /* ── Admin assets ── */

    public static function enqueue_admin_assets( $hook ) {
        if ( 'smart-editor_page_ste-license' !== $hook ) return;
        wp_enqueue_style( 'ste-license-admin', STE_PLUGIN_URL . 'assets/css/license-admin.css', array(), STE_VERSION );
    }

    /* ── Submenu ── */

    public static function add_submenu() {
        add_submenu_page(
            'smart-text-editor',
            __( 'Plan & License', 'smart-text-editor' ),
            __( 'Plan & License', 'smart-text-editor' ),
            'manage_options',
            'ste-license',
            array( __CLASS__, 'render_page' )
        );
    }

    /* ── Render admin page ── */

    public static function render_page() {
        $current      = self::get_plan();
        $current_data = self::get_plan_data();
        $plans        = self::$plans;
        $license_key  = get_option( 'ste_license_key', '' );
        $activated_at = get_option( 'ste_license_activated', '' );
        $msg          = isset( $_GET['ste_msg'] ) ? sanitize_text_field( wp_unslash( $_GET['ste_msg'] ) ) : '';
        ?>
        <div class="wrap ste-license-wrap">
            <h1><?php esc_html_e( 'Smart Text Editor — Plan & License', 'smart-text-editor' ); ?></h1>

            <?php if ( $msg ) : ?>
                <div class="notice <?php echo in_array( $msg, array( 'activated', 'deactivated' ), true ) ? 'notice-success' : 'notice-error'; ?> is-dismissible" style="margin-top:16px;">
                    <p>
                    <?php
                    switch ( $msg ) {
                        case 'activated':
                            esc_html_e( 'License activated successfully! Your plan has been upgraded.', 'smart-text-editor' );
                            break;
                        case 'deactivated':
                            esc_html_e( 'License deactivated. You are now on the Free plan.', 'smart-text-editor' );
                            break;
                        case 'invalid_key':
                            esc_html_e( 'Invalid license key. Please check your key and try again.', 'smart-text-editor' );
                            break;
                        case 'empty_key':
                            esc_html_e( 'Please enter a license key.', 'smart-text-editor' );
                            break;
                    }
                    ?>
                    </p>
                </div>
            <?php endif; ?>

            <!-- Current Plan Status -->
            <div class="ste-license-status">
                <div class="ste-license-status-inner">
                    <div class="ste-license-plan-badge ste-badge-<?php echo esc_attr( $current ); ?>">
                        <?php echo esc_html( $current_data['label'] ); ?> Plan
                    </div>
                    <?php if ( 'free' !== $current && $license_key ) : ?>
                        <div class="ste-license-key-info">
                            <span class="ste-license-key-display"><?php echo esc_html( substr( $license_key, 0, 12 ) . '****-****' ); ?></span>
                            <span class="ste-license-date">Activated: <?php echo esc_html( $activated_at ? date_i18n( get_option( 'date_format' ), strtotime( $activated_at ) ) : 'N/A' ); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Plan Comparison -->
            <div class="ste-plans-grid">
                <?php foreach ( $plans as $key => $p ) :
                    $active     = ( $key === $current );
                    $is_upgrade = ! $active && array_search( $key, array_keys( $plans ) ) > array_search( $current, array_keys( $plans ) );
                ?>
                <div class="ste-plan-card <?php echo $active ? 'ste-plan-active' : ''; ?> <?php echo 'pro' === $key ? 'ste-plan-popular' : ''; ?>">
                    <?php if ( $active ) : ?>
                        <span class="ste-plan-tag ste-tag-active">Current Plan</span>
                    <?php elseif ( 'pro' === $key ) : ?>
                        <span class="ste-plan-tag ste-tag-popular">Most Popular</span>
                    <?php endif; ?>

                    <h2><?php echo esc_html( $p['label'] ); ?></h2>
                    <div class="ste-plan-price">
                        <span class="ste-plan-amount"><?php echo esc_html( '$' . $p['price_num'] ); ?></span>
                        <?php if ( $p['price_num'] > 0 ) : ?>
                            <span class="ste-plan-period">/month</span>
                        <?php else : ?>
                            <span class="ste-plan-period">forever</span>
                        <?php endif; ?>
                    </div>

                    <ul class="ste-plan-features">
                        <li class="<?php echo $p['max_fonts'] >= 999 ? 'ste-feat-yes' : ''; ?>"><?php echo $p['max_fonts'] >= 999 ? '50+ fonts' : $p['max_fonts'] . ' fonts'; ?></li>
                        <li class="<?php echo $p['max_presets'] >= 999 ? 'ste-feat-yes' : ''; ?>"><?php echo $p['max_presets'] >= 999 ? 'Unlimited presets' : $p['max_presets'] . ' presets'; ?></li>
                        <li class="<?php echo $p['effects'] ? 'ste-feat-yes' : 'ste-feat-no'; ?>">Style effects</li>
                        <li class="<?php echo $p['animations'] ? 'ste-feat-yes' : 'ste-feat-no'; ?>">Scroll animations</li>
                        <li class="<?php echo $p['table_editor'] ? 'ste-feat-yes' : 'ste-feat-no'; ?>">Table editor</li>
                        <li class="<?php echo $p['export_css'] ? 'ste-feat-yes' : 'ste-feat-no'; ?>">CSS export</li>
                        <li class="<?php echo $p['custom_presets'] ? 'ste-feat-yes' : 'ste-feat-no'; ?>">Custom presets</li>
                        <li class="<?php echo $p['custom_fonts'] ? 'ste-feat-yes' : 'ste-feat-no'; ?>">Custom font uploads</li>
                        <li class="<?php echo $p['whitelabel'] ? 'ste-feat-yes' : 'ste-feat-no'; ?>">White-label</li>
                    </ul>

                    <?php if ( $active ) : ?>
                        <span class="ste-plan-btn ste-plan-btn-current">Active</span>
                    <?php elseif ( 'free' === $key ) : ?>
                        <?php if ( 'free' !== $current ) : ?>
                            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin:0;">
                                <input type="hidden" name="action" value="ste_deactivate_license">
                                <?php wp_nonce_field( 'ste_license_action' ); ?>
                                <button type="submit" class="ste-plan-btn ste-plan-btn-downgrade" onclick="return confirm('This will deactivate your license and downgrade to the Free plan. Continue?');">Downgrade to Free</button>
                            </form>
                        <?php else : ?>
                            <span class="ste-plan-btn ste-plan-btn-current">Active</span>
                        <?php endif; ?>
                    <?php else : ?>
                        <a href="#ste-activate-section" class="ste-plan-btn ste-plan-btn-upgrade" data-plan="<?php echo esc_attr( $key ); ?>">
                            Purchase <?php echo esc_html( $p['label'] ); ?> — <?php echo esc_html( $p['price'] ); ?>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Purchase & Activate Section -->
            <div id="ste-activate-section" class="ste-activate-section">
                <h2><?php esc_html_e( 'Activate License Key', 'smart-text-editor' ); ?></h2>
                <p class="ste-activate-desc">
                    <?php esc_html_e( 'Purchase a Pro or Business plan to receive a license key. Enter it below to activate your plan.', 'smart-text-editor' ); ?>
                </p>

                <div class="ste-purchase-links">
                    <div class="ste-purchase-card">
                        <h3>Pro Plan</h3>
                        <p class="ste-purchase-price">$5<span>/month</span></p>
                        <p class="ste-purchase-desc">50+ fonts, all effects, animations, table editor, HTML/CSS export</p>
                        <a href="<?php echo esc_url( home_url( '/checkout/?plan=pro' ) ); ?>" target="_blank" rel="noopener" class="ste-purchase-btn ste-purchase-btn-pro">Buy Pro License</a>
                    </div>
                    <div class="ste-purchase-card">
                        <h3>Business Plan</h3>
                        <p class="ste-purchase-price">$15<span>/month</span></p>
                        <p class="ste-purchase-desc">Everything in Pro + custom presets, font uploads, white-label, multisite</p>
                        <a href="<?php echo esc_url( home_url( '/checkout/?plan=business' ) ); ?>" target="_blank" rel="noopener" class="ste-purchase-btn ste-purchase-btn-biz">Buy Business License</a>
                    </div>
                </div>

                <div class="ste-activate-form-wrap">
                    <h3><?php esc_html_e( 'Already have a license key?', 'smart-text-editor' ); ?></h3>
                    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="ste-activate-form">
                        <input type="hidden" name="action" value="ste_activate_license">
                        <?php wp_nonce_field( 'ste_license_action' ); ?>
                        <div class="ste-activate-input-row">
                            <input type="text" name="ste_license_key" placeholder="STE-PRO-XXXX-XXXX-XXXX" class="ste-license-input" spellcheck="false" autocomplete="off" required>
                            <button type="submit" class="button button-primary ste-activate-btn">Activate License</button>
                        </div>
                        <p class="ste-activate-hint">Enter your license key exactly as received after purchase.</p>
                    </form>
                </div>

                <?php if ( 'free' !== $current && $license_key ) : ?>
                    <div class="ste-deactivate-section">
                        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                            <input type="hidden" name="action" value="ste_deactivate_license">
                            <?php wp_nonce_field( 'ste_license_action' ); ?>
                            <button type="submit" class="ste-deactivate-btn" onclick="return confirm('This will deactivate your license and revert to the Free plan. Continue?');">Deactivate License</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}
