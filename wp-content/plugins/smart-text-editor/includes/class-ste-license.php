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
            'price'             => '₹0',
            'price_num'         => 0,
            'price_yearly'      => '₹0',
            'price_yearly_num'  => 0,
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
            'price'             => '₹449/mo',
            'price_num'         => 449,
            'price_yearly'      => '₹4,490/yr',
            'price_yearly_num'  => 4490,
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
            'price'             => '₹1,199/mo',
            'price_num'         => 1199,
            'price_yearly'      => '₹11,990/yr',
            'price_yearly_num'  => 11990,
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
        add_action( 'admin_post_ste_start_trial', array( __CLASS__, 'handle_start_trial' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );
    }

    /* ── Getters ── */

    public static function get_plan() {
        $plan = get_option( 'ste_active_plan', 'free' );
        if ( ! isset( self::$plans[ $plan ] ) ) return 'free';
        // For paid plans, verify license key still exists and hasn't expired
        if ( 'free' !== $plan ) {
            $key = get_option( 'ste_license_key', '' );
            if ( empty( $key ) || ! self::validate_key( $key, $plan ) ) {
                update_option( 'ste_active_plan', 'free' );
                return 'free';
            }
            // Check expiration
            $expires = get_option( 'ste_license_expires', '' );
            if ( $expires && current_time( 'timestamp' ) > strtotime( $expires ) ) {
                update_option( 'ste_active_plan', 'free' );
                delete_option( 'ste_license_key' );
                delete_option( 'ste_license_activated' );
                delete_option( 'ste_license_expires' );
                delete_option( 'ste_billing_cycle' );
                return 'free';
            }
        }

        // If still on free, check for active trial
        if ( 'free' === $plan && self::is_trial_active() ) {
            return 'pro';
        }

        return $plan;
    }

    /* ── Trial helpers ── */

    public static function is_trial_active() {
        $trial_expires = get_option( 'ste_trial_expires', '' );
        if ( empty( $trial_expires ) ) return false;
        return current_time( 'timestamp' ) < strtotime( $trial_expires );
    }

    public static function is_trial_used() {
        return (bool) get_option( 'ste_trial_used', false );
    }

    public static function get_trial_expires() {
        return get_option( 'ste_trial_expires', '' );
    }

    public static function can_start_trial() {
        // Can start if never used AND not on a paid plan already
        return ! self::is_trial_used() && 'free' === get_option( 'ste_active_plan', 'free' );
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
        $plan     = self::get_plan();
        $data     = self::get_plan_data();
        $on_trial = self::is_trial_active() && 'free' === get_option( 'ste_active_plan', 'free' );

        $config = array(
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
            'isTrial'       => $on_trial,
            'sourceView'    => true,
        );

        // Trial restrictions: no HTML source, no export, limited presets
        if ( $on_trial ) {
            $config['exportCss']   = false;
            $config['sourceView']  = false;
            $config['maxPresets']  = 5;
            $config['customPresets'] = false;
        }

        return $config;
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

        // Fetch expiry from the purchase order
        $expires_at    = '';
        $billing_cycle = 'monthly';
        if ( class_exists( 'STE_Checkout' ) ) {
            $expires_at    = STE_Checkout::get_expiry_by_license_key( $key );
            $billing_cycle = STE_Checkout::get_billing_cycle_by_license_key( $key );
            if ( ! $billing_cycle ) $billing_cycle = 'monthly';
        }
        // Fallback if no order found
        if ( empty( $expires_at ) ) {
            $period     = ( 'yearly' === $billing_cycle ) ? '+365 days' : '+30 days';
            $expires_at = date( 'Y-m-d H:i:s', strtotime( $period, current_time( 'timestamp' ) ) );
        }
        update_option( 'ste_license_expires', $expires_at );
        update_option( 'ste_billing_cycle', $billing_cycle );

        wp_redirect( add_query_arg( 'ste_msg', 'activated', admin_url( 'admin.php?page=ste-license' ) ) );
        exit;
    }

    /* ── Handle deactivation ── */

    public static function handle_deactivate() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
        check_admin_referer( 'ste_license_action' );

        delete_option( 'ste_license_key' );
        delete_option( 'ste_license_activated' );
        delete_option( 'ste_license_expires' );
        delete_option( 'ste_billing_cycle' );
        update_option( 'ste_active_plan', 'free' );

        wp_redirect( add_query_arg( 'ste_msg', 'deactivated', admin_url( 'admin.php?page=ste-license' ) ) );
        exit;
    }

    /* ── Handle start trial ── */

    public static function handle_start_trial() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
        check_admin_referer( 'ste_license_action' );

        if ( ! self::can_start_trial() ) {
            wp_redirect( add_query_arg( 'ste_msg', 'trial_unavailable', admin_url( 'admin.php?page=ste-license' ) ) );
            exit;
        }

        $trial_expires = date( 'Y-m-d H:i:s', strtotime( '+7 days', current_time( 'timestamp' ) ) );
        update_option( 'ste_trial_used', true );
        update_option( 'ste_trial_started', current_time( 'mysql' ) );
        update_option( 'ste_trial_expires', $trial_expires );

        wp_redirect( add_query_arg( 'ste_msg', 'trial_started', admin_url( 'admin.php?page=ste-license' ) ) );
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
        $expires_at    = get_option( 'ste_license_expires', '' );
        $billing_cycle = get_option( 'ste_billing_cycle', 'monthly' );
        $msg           = isset( $_GET['ste_msg'] ) ? sanitize_text_field( wp_unslash( $_GET['ste_msg'] ) ) : '';
        ?>
        <div class="wrap ste-license-wrap">
            <h1><?php esc_html_e( 'Smart Text Editor — Plan & License', 'smart-text-editor' ); ?></h1>

            <?php if ( $msg ) : ?>
                <div class="notice <?php echo in_array( $msg, array( 'activated', 'deactivated', 'trial_started' ), true ) ? 'notice-success' : 'notice-error'; ?> is-dismissible" style="margin-top:16px;">
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
                        case 'trial_started':
                            esc_html_e( '7-day free trial activated! Enjoy all Pro features.', 'smart-text-editor' );
                            break;
                        case 'trial_unavailable':
                            esc_html_e( 'Free trial is not available. You may have already used your trial.', 'smart-text-editor' );
                            break;
                    }
                    ?>
                    </p>
                </div>
            <?php endif; ?>

            <!-- Current Plan Status -->
            <?php
            $is_on_trial     = self::is_trial_active();
            $trial_exp       = self::get_trial_expires();
            $base_plan       = get_option( 'ste_active_plan', 'free' ); // actual plan without trial override
            ?>
            <div class="ste-license-status <?php echo $is_on_trial ? 'ste-license-status-trial' : ''; ?>">
                <div class="ste-license-status-inner">
                    <?php if ( $is_on_trial ) : ?>
                        <div class="ste-license-plan-badge ste-badge-trial">
                            Pro Plan <span class="ste-trial-tag">7-Day Trial</span>
                        </div>
                        <?php
                        $trial_started = get_option( 'ste_trial_started', '' );
                        $trial_diff    = strtotime( $trial_exp ) - current_time( 'timestamp' );
                        $trial_days    = max( 0, intval( $trial_diff / DAY_IN_SECONDS ) );
                        $trial_hours   = max( 0, intval( ( $trial_diff % DAY_IN_SECONDS ) / HOUR_IN_SECONDS ) );
                        $trial_total   = $trial_started ? strtotime( $trial_exp ) - strtotime( $trial_started ) : 1;
                        $trial_elapsed = $trial_started ? current_time( 'timestamp' ) - strtotime( $trial_started ) : 0;
                        $trial_pct     = $trial_total > 0 ? min( 100, max( 0, 100 - ( $trial_elapsed / $trial_total ) * 100 ) ) : 0;
                        $trial_warning = $trial_days <= 2;
                        ?>
                        <div class="ste-license-validity">
                            <div class="ste-validity-row">
                                <span class="ste-validity-label">Trial Started</span>
                                <span class="ste-validity-value"><?php echo esc_html( $trial_started ? date_i18n( get_option( 'date_format' ), strtotime( $trial_started ) ) : 'N/A' ); ?></span>
                            </div>
                            <div class="ste-validity-row">
                                <span class="ste-validity-label">Trial Expires</span>
                                <span class="ste-validity-value"><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $trial_exp ) ) ); ?></span>
                            </div>
                            <div class="ste-validity-row">
                                <span class="ste-validity-label">Time Remaining</span>
                                <span class="ste-validity-value <?php echo $trial_warning ? 'ste-validity-warning' : 'ste-validity-ok'; ?>">
                                    <?php
                                    if ( $trial_days > 0 ) {
                                        echo esc_html( $trial_days . ' day' . ( $trial_days !== 1 ? 's' : '' ) . ', ' . $trial_hours . ' hour' . ( $trial_hours !== 1 ? 's' : '' ) );
                                    } else {
                                        echo esc_html( $trial_hours . ' hour' . ( $trial_hours !== 1 ? 's' : '' ) );
                                    }
                                    ?>
                                </span>
                            </div>
                            <div class="ste-validity-bar-wrap">
                                <div class="ste-validity-bar">
                                    <div class="ste-validity-bar-fill <?php echo $trial_warning ? 'ste-bar-warning' : 'ste-bar-trial'; ?>" style="width:<?php echo esc_attr( round( $trial_pct ) ); ?>%;"></div>
                                </div>
                                <span class="ste-validity-bar-label"><?php echo esc_html( round( $trial_pct ) ); ?>% remaining</span>
                            </div>
                            <p class="ste-trial-upgrade-hint">Upgrade to a paid plan before your trial ends to keep Pro features.</p>
                        </div>

                    <?php elseif ( 'free' !== $current && $license_key ) :
                        $days_remaining = $expires_at ? max( 0, intval( ( strtotime( $expires_at ) - current_time( 'timestamp' ) ) / DAY_IN_SECONDS ) ) : 0;
                        $is_expiring_soon = $days_remaining <= 7 && $days_remaining > 0;
                    ?>
                        <div class="ste-license-plan-badge ste-badge-<?php echo esc_attr( $current ); ?>">
                            <?php echo esc_html( $current_data['label'] ); ?> Plan
                        </div>
                        <div class="ste-license-key-info">
                            <span class="ste-license-key-display"><?php echo esc_html( substr( $license_key, 0, 12 ) . '****-****' ); ?></span>
                            <span class="ste-license-date">Activated: <?php echo esc_html( $activated_at ? date_i18n( get_option( 'date_format' ), strtotime( $activated_at ) ) : 'N/A' ); ?></span>
                            <span class="ste-license-cycle-badge"><?php echo esc_html( ucfirst( $billing_cycle ) ); ?></span>
                        </div>
                        <div class="ste-license-validity">
                            <div class="ste-validity-row">
                                <span class="ste-validity-label">Expires On</span>
                                <span class="ste-validity-value"><?php echo esc_html( $expires_at ? date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $expires_at ) ) : 'N/A' ); ?></span>
                            </div>
                            <div class="ste-validity-row">
                                <span class="ste-validity-label">Time Remaining</span>
                                <span class="ste-validity-value <?php echo $is_expiring_soon ? 'ste-validity-warning' : 'ste-validity-ok'; ?>">
                                    <?php
                                    if ( $expires_at ) {
                                        $diff = strtotime( $expires_at ) - current_time( 'timestamp' );
                                        if ( $diff <= 0 ) {
                                            echo 'Expired';
                                        } else {
                                            $d = intval( $diff / DAY_IN_SECONDS );
                                            $h = intval( ( $diff % DAY_IN_SECONDS ) / HOUR_IN_SECONDS );
                                            if ( $d > 0 ) {
                                                echo esc_html( $d . ' day' . ( $d !== 1 ? 's' : '' ) . ', ' . $h . ' hour' . ( $h !== 1 ? 's' : '' ) );
                                            } else {
                                                echo esc_html( $h . ' hour' . ( $h !== 1 ? 's' : '' ) );
                                            }
                                        }
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </span>
                            </div>
                            <div class="ste-validity-bar-wrap">
                                <?php
                                $total_seconds = $activated_at && $expires_at ? strtotime( $expires_at ) - strtotime( $activated_at ) : 1;
                                $elapsed       = $activated_at ? current_time( 'timestamp' ) - strtotime( $activated_at ) : 0;
                                $percent_used  = $total_seconds > 0 ? min( 100, max( 0, ( $elapsed / $total_seconds ) * 100 ) ) : 0;
                                $percent_left  = 100 - $percent_used;
                                ?>
                                <div class="ste-validity-bar">
                                    <div class="ste-validity-bar-fill <?php echo $is_expiring_soon ? 'ste-bar-warning' : 'ste-bar-ok'; ?>" style="width:<?php echo esc_attr( $percent_left ); ?>%;"></div>
                                </div>
                                <span class="ste-validity-bar-label"><?php echo esc_html( round( $percent_left ) ); ?>% remaining</span>
                            </div>
                        </div>

                    <?php else : ?>
                        <div class="ste-license-plan-badge ste-badge-free">Free Plan</div>
                        <div class="ste-license-validity" style="margin-top:12px;">
                            <span class="ste-validity-free">Free plan — no expiration</span>
                            <?php if ( self::can_start_trial() ) : ?>
                                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:14px;">
                                    <input type="hidden" name="action" value="ste_start_trial">
                                    <?php wp_nonce_field( 'ste_license_action' ); ?>
                                    <button type="submit" class="ste-trial-btn">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                        Start 7-Day Free Trial
                                    </button>
                                    <p class="ste-trial-hint">Get all Pro features free for 7 days. No payment required.</p>
                                </form>
                            <?php elseif ( self::is_trial_used() && ! self::is_trial_active() ) : ?>
                                <p class="ste-trial-expired-hint">Your free trial has ended. Purchase a plan to continue using Pro features.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Billing Cycle Toggle -->
            <div class="ste-billing-toggle-wrap">
                <span class="ste-billing-label" data-cycle="monthly">Monthly</span>
                <label class="ste-billing-switch">
                    <input type="checkbox" id="ste-billing-toggle">
                    <span class="ste-billing-slider"></span>
                </label>
                <span class="ste-billing-label" data-cycle="yearly">Yearly</span>
                <span class="ste-billing-save-badge">Save up to 17%</span>
            </div>

            <!-- Plan Comparison -->
            <div class="ste-plans-grid">
                <?php foreach ( $plans as $key => $p ) :
                    $active     = ( $key === $current );
                    $is_upgrade = ! $active && array_search( $key, array_keys( $plans ) ) > array_search( $current, array_keys( $plans ) );
                    $monthly_equiv = $p['price_yearly_num'] > 0 ? round( $p['price_yearly_num'] / 12 ) : 0;
                ?>
                <div class="ste-plan-card <?php echo $active ? 'ste-plan-active' : ''; ?> <?php echo 'pro' === $key ? 'ste-plan-popular' : ''; ?>">
                    <?php if ( $active ) : ?>
                        <span class="ste-plan-tag ste-tag-active">Current Plan</span>
                    <?php elseif ( 'pro' === $key ) : ?>
                        <span class="ste-plan-tag ste-tag-popular">Most Popular</span>
                    <?php endif; ?>

                    <h2><?php echo esc_html( $p['label'] ); ?></h2>
                    <div class="ste-plan-price" data-monthly="<?php echo esc_attr( $p['price_num'] ); ?>" data-yearly="<?php echo esc_attr( $p['price_yearly_num'] ); ?>">
                        <span class="ste-plan-amount ste-price-monthly"><?php echo esc_html( '₹' . number_format( $p['price_num'] ) ); ?></span>
                        <span class="ste-plan-amount ste-price-yearly" style="display:none;"><?php echo esc_html( '₹' . number_format( $p['price_yearly_num'] ) ); ?></span>
                        <?php if ( $p['price_num'] > 0 ) : ?>
                            <span class="ste-plan-period ste-period-monthly">/month</span>
                            <span class="ste-plan-period ste-period-yearly" style="display:none;">/year</span>
                        <?php else : ?>
                            <span class="ste-plan-period">forever</span>
                        <?php endif; ?>
                    </div>
                    <?php if ( $monthly_equiv > 0 ) : ?>
                        <div class="ste-plan-yearly-equiv" style="display:none;">₹<?php echo esc_html( number_format( $monthly_equiv ) ); ?>/mo · Save ₹<?php echo esc_html( number_format( ( $p['price_num'] * 12 ) - $p['price_yearly_num'] ) ); ?></div>
                    <?php endif; ?>

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
                        <a href="<?php echo esc_url( home_url( '/checkout/?plan=' . $key . '&billing=monthly' ) ); ?>" class="ste-plan-btn ste-plan-btn-upgrade ste-purchase-link" data-plan="<?php echo esc_attr( $key ); ?>" data-price-monthly="<?php echo esc_attr( $p['price'] ); ?>" data-price-yearly="<?php echo esc_attr( $p['price_yearly'] ); ?>">
                            Purchase <?php echo esc_html( $p['label'] ); ?> — <span class="ste-btn-price-text"><?php echo esc_html( $p['price'] ); ?></span>
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
                        <p class="ste-purchase-price ste-purchase-price-monthly">₹449<span>/month</span></p>
                        <p class="ste-purchase-price ste-purchase-price-yearly" style="display:none;">₹4,490<span>/year</span></p>
                        <p class="ste-purchase-desc">50+ fonts, all effects, animations, table editor, HTML/CSS export</p>
                        <a href="<?php echo esc_url( home_url( '/checkout/?plan=pro&billing=monthly' ) ); ?>" target="_blank" rel="noopener" class="ste-purchase-btn ste-purchase-btn-pro ste-purchase-link" data-plan="pro">Buy Pro License</a>
                    </div>
                    <div class="ste-purchase-card">
                        <h3>Business Plan</h3>
                        <p class="ste-purchase-price ste-purchase-price-monthly">₹1,199<span>/month</span></p>
                        <p class="ste-purchase-price ste-purchase-price-yearly" style="display:none;">₹11,990<span>/year</span></p>
                        <p class="ste-purchase-desc">Everything in Pro + custom presets, font uploads, white-label, multisite</p>
                        <a href="<?php echo esc_url( home_url( '/checkout/?plan=business&billing=monthly' ) ); ?>" target="_blank" rel="noopener" class="ste-purchase-btn ste-purchase-btn-biz ste-purchase-link" data-plan="business">Buy Business License</a>
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
        <script>
        (function(){
            var toggle = document.getElementById('ste-billing-toggle');
            if (!toggle) return;
            var homeUrl = <?php echo wp_json_encode( home_url( '/checkout/' ) ); ?>;

            function update(yearly) {
                document.querySelectorAll('.ste-price-monthly, .ste-period-monthly, .ste-purchase-price-monthly').forEach(function(el){ el.style.display = yearly ? 'none' : ''; });
                document.querySelectorAll('.ste-price-yearly, .ste-period-yearly, .ste-purchase-price-yearly').forEach(function(el){ el.style.display = yearly ? '' : 'none'; });
                document.querySelectorAll('.ste-plan-yearly-equiv').forEach(function(el){ el.style.display = yearly ? 'block' : 'none'; });
                document.querySelectorAll('.ste-purchase-link').forEach(function(el){
                    var plan = el.getAttribute('data-plan');
                    var href = homeUrl + '?plan=' + plan + '&billing=' + (yearly ? 'yearly' : 'monthly');
                    el.setAttribute('href', href);
                });
                document.querySelectorAll('.ste-plan-btn-upgrade.ste-purchase-link').forEach(function(el){
                    var span = el.querySelector('.ste-btn-price-text');
                    if (span) {
                        span.textContent = yearly ? el.getAttribute('data-price-yearly') : el.getAttribute('data-price-monthly');
                    }
                });
                document.querySelectorAll('.ste-billing-label').forEach(function(el){
                    el.classList.toggle('active', (yearly && el.dataset.cycle === 'yearly') || (!yearly && el.dataset.cycle === 'monthly'));
                });
            }

            toggle.addEventListener('change', function(){ update(this.checked); });
            update(false);
        })();
        </script>
        <?php
    }
}
