<?php
/**
 * Checkout system for Smart Text Editor — powered by Cashfree.
 *
 * Flow:
 * 1. User clicks Buy on pricing → /checkout/?plan=pro
 * 2. User fills name & email, clicks Pay
 * 3. AJAX creates a Cashfree order via API → returns payment_session_id
 * 4. Cashfree JS SDK opens payment form (cards, UPI, netbanking, etc.)
 * 5. On success, user redirected to /checkout/?plan=X&order_id=Y
 * 6. Server verifies payment via Cashfree API
 * 7. If paid → generate license key, store order, show success
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class STE_Checkout {

    private static $table_name;

    public static function init() {
        global $wpdb;
        self::$table_name = $wpdb->prefix . 'ste_orders';

        add_action( 'init', array( __CLASS__, 'register_checkout_page' ) );
        add_action( 'wp_ajax_ste_create_cf_order', array( __CLASS__, 'ajax_create_cf_order' ) );
        add_action( 'wp_ajax_nopriv_ste_create_cf_order', array( __CLASS__, 'ajax_create_cf_order' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_checkout_assets' ) );
        add_action( 'admin_menu', array( __CLASS__, 'add_submenus' ), 25 );
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
        add_filter( 'template_include', array( __CLASS__, 'load_checkout_template' ) );
        add_filter( 'query_vars', array( __CLASS__, 'add_query_vars' ) );

        // Webhook endpoint
        add_action( 'rest_api_init', array( __CLASS__, 'register_webhook' ) );

    }

    /* ══════════════════════════════════════
       CASHFREE SETTINGS
       ══════════════════════════════════════ */

    public static function get_cf_mode() {
        return get_option( 'ste_cf_mode', 'sandbox' );
    }

    public static function get_cf_app_id() {
        return get_option( 'ste_cf_app_id', '' );
    }

    public static function get_cf_secret_key() {
        return get_option( 'ste_cf_secret_key', '' );
    }

    public static function get_cf_api_base() {
        return 'production' === self::get_cf_mode()
            ? 'https://api.cashfree.com/pg'
            : 'https://sandbox.cashfree.com/pg';
    }

    public static function get_cf_js_url() {
        return 'production' === self::get_cf_mode()
            ? 'https://sdk.cashfree.com/js/v3/cashfree.js'
            : 'https://sdk.cashfree.com/js/v3/cashfree.js';
    }

    public static function is_cf_configured() {
        return ! empty( self::get_cf_app_id() ) && ! empty( self::get_cf_secret_key() );
    }

    /**
     * Check if FluentSMTP has at least one configured email connection.
     */
    public static function is_smtp_configured() {
        $settings = get_option( 'fluentmail-settings', array() );
        if ( empty( $settings ) || ! is_array( $settings ) ) return false;
        if ( empty( $settings['connections'] ) || ! is_array( $settings['connections'] ) ) return false;

        foreach ( $settings['connections'] as $conn ) {
            if ( ! empty( $conn['provider_settings']['sender_email'] ) ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the configured sender email from FluentSMTP.
     */
    public static function get_smtp_sender_email() {
        $settings = get_option( 'fluentmail-settings', array() );
        if ( empty( $settings['connections'] ) || ! is_array( $settings['connections'] ) ) return '';

        foreach ( $settings['connections'] as $conn ) {
            if ( ! empty( $conn['provider_settings']['sender_email'] ) ) {
                return $conn['provider_settings']['sender_email'];
            }
        }
        return '';
    }

    public static function register_settings() {
        // Cashfree settings
        register_setting( 'ste_cashfree_settings', 'ste_cf_mode', array(
            'type' => 'string', 'default' => 'sandbox',
            'sanitize_callback' => function( $v ) { return in_array( $v, array( 'sandbox', 'production' ) ) ? $v : 'sandbox'; },
        ) );
        register_setting( 'ste_cashfree_settings', 'ste_cf_app_id', array(
            'type' => 'string', 'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'ste_cashfree_settings', 'ste_cf_secret_key', array(
            'type' => 'string', 'sanitize_callback' => 'sanitize_text_field',
        ) );

    }

    /* ══════════════════════════════════════
       DATABASE
       ══════════════════════════════════════ */

    public static function create_table() {
        global $wpdb;
        $table   = $wpdb->prefix . 'ste_orders';
        $charset = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            order_number VARCHAR(30) NOT NULL,
            cf_order_id VARCHAR(50) DEFAULT '',
            plan VARCHAR(20) NOT NULL DEFAULT 'pro',
            customer_name VARCHAR(100) NOT NULL,
            customer_email VARCHAR(100) NOT NULL,
            customer_phone VARCHAR(20) DEFAULT '',
            amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            currency VARCHAR(3) NOT NULL DEFAULT 'INR',
            billing_cycle VARCHAR(10) NOT NULL DEFAULT 'monthly',
            payment_method VARCHAR(50) DEFAULT '',
            license_key VARCHAR(50) DEFAULT '',
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            cf_payment_id VARCHAR(50) DEFAULT '',
            expires_at DATETIME DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY plan (plan),
            KEY status (status),
            KEY cf_order_id (cf_order_id),
            KEY customer_email (customer_email)
        ) {$charset};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    public static function generate_order_number() {
        return 'STE-' . strtoupper( substr( uniqid(), -8 ) );
    }

    /**
     * Look up the expiry date for a license key from the orders table.
     */
    public static function get_expiry_by_license_key( $license_key ) {
        global $wpdb;
        $table = $wpdb->prefix . 'ste_orders';
        return $wpdb->get_var( $wpdb->prepare(
            "SELECT expires_at FROM {$table} WHERE license_key = %s AND status = 'completed' LIMIT 1",
            $license_key
        ) );
    }

    /**
     * Look up the billing cycle for a license key from the orders table.
     */
    public static function get_billing_cycle_by_license_key( $license_key ) {
        global $wpdb;
        $table = $wpdb->prefix . 'ste_orders';
        return $wpdb->get_var( $wpdb->prepare(
            "SELECT billing_cycle FROM {$table} WHERE license_key = %s AND status = 'completed' LIMIT 1",
            $license_key
        ) );
    }

    /* ══════════════════════════════════════
       CHECKOUT PAGE ROUTING
       ══════════════════════════════════════ */

    public static function register_checkout_page() {
        add_rewrite_rule( '^checkout/?$', 'index.php?ste_checkout=1', 'top' );
    }

    public static function add_query_vars( $vars ) {
        $vars[] = 'ste_checkout';
        return $vars;
    }

    public static function load_checkout_template( $template ) {
        if ( get_query_var( 'ste_checkout' ) ) {
            $checkout_template = STE_PLUGIN_DIR . 'templates/checkout.php';
            if ( file_exists( $checkout_template ) ) {
                return $checkout_template;
            }
        }
        return $template;
    }

    public static function enqueue_checkout_assets() {
        if ( ! get_query_var( 'ste_checkout' ) ) return;

        wp_enqueue_style( 'ste-google-fonts',
            'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap',
            array(), null
        );
        wp_enqueue_style( 'ste-checkout', STE_PLUGIN_URL . 'assets/css/checkout.css', array(), STE_VERSION );

        // Cashfree JS SDK
        wp_enqueue_script( 'cashfree-sdk', self::get_cf_js_url(), array(), null, true );

        wp_enqueue_script( 'ste-checkout', STE_PLUGIN_URL . 'assets/js/checkout.js', array( 'cashfree-sdk' ), STE_VERSION, true );
        wp_localize_script( 'ste-checkout', 'steCheckout', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'ste_checkout_nonce' ),
            'mode'    => self::get_cf_mode(),
            'returnUrl' => home_url( '/checkout/' ),
        ) );
    }

    /* ══════════════════════════════════════
       STEP 1: CREATE CASHFREE ORDER (AJAX)
       ══════════════════════════════════════ */

    public static function ajax_create_cf_order() {
        check_ajax_referer( 'ste_checkout_nonce', 'nonce' );

        $plan    = isset( $_POST['plan'] ) ? sanitize_text_field( wp_unslash( $_POST['plan'] ) ) : '';
        $billing = isset( $_POST['billing'] ) ? sanitize_text_field( wp_unslash( $_POST['billing'] ) ) : 'monthly';
        $name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
        $email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
        $phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';

        if ( ! in_array( $billing, array( 'monthly', 'yearly' ), true ) ) $billing = 'monthly';

        // Validate
        if ( ! in_array( $plan, array( 'pro', 'business' ), true ) ) {
            wp_send_json_error( array( 'message' => 'Invalid plan selected.' ) );
        }
        if ( empty( $name ) || strlen( $name ) < 2 ) {
            wp_send_json_error( array( 'message' => 'Please enter your full name.' ) );
        }
        if ( ! is_email( $email ) ) {
            wp_send_json_error( array( 'message' => 'Please enter a valid email address.' ) );
        }
        if ( empty( $phone ) || strlen( preg_replace( '/\D/', '', $phone ) ) < 10 ) {
            wp_send_json_error( array( 'message' => 'Please enter a valid phone number.' ) );
        }

        if ( ! self::is_cf_configured() ) {
            wp_send_json_error( array( 'message' => 'Payment gateway is not configured. Please contact the site administrator.' ) );
        }

        if ( ! self::is_smtp_configured() ) {
            wp_send_json_error( array( 'message' => 'Email delivery (FluentSMTP) is not configured. License keys cannot be sent. Please contact the site administrator.' ) );
        }

        // Determine amount
        $plans  = STE_License::get_all_plans();
        $is_yearly = ( 'yearly' === $billing );
        $price_key = $is_yearly ? 'price_yearly_num' : 'price_num';
        $amount = isset( $plans[ $plan ][ $price_key ] ) ? floatval( $plans[ $plan ][ $price_key ] ) : 0;
        $label  = isset( $plans[ $plan ]['label'] ) ? $plans[ $plan ]['label'] : $plan;

        // Create local order first
        $order_number = self::generate_order_number();

        global $wpdb;
        $wpdb->insert( $wpdb->prefix . 'ste_orders', array(
            'order_number'   => $order_number,
            'plan'           => $plan,
            'billing_cycle'  => $billing,
            'customer_name'  => $name,
            'customer_email' => $email,
            'customer_phone' => $phone,
            'amount'         => $amount,
            'currency'       => 'INR',
            'status'         => 'pending',
            'created_at'     => current_time( 'mysql' ),
        ), array( '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%s', '%s', '%s' ) );

        // Create Cashfree order
        $cf_payload = array(
            'order_id'       => $order_number,
            'order_amount'   => $amount,
            'order_currency' => 'INR',
            'customer_details' => array(
                'customer_id'    => 'cust_' . substr( md5( $email ), 0, 12 ),
                'customer_name'  => $name,
                'customer_email' => $email,
                'customer_phone' => preg_replace( '/\D/', '', $phone ),
            ),
            'order_meta' => array(
                'return_url' => add_query_arg( array(
                    'plan'     => $plan,
                    'billing'  => $billing,
                    'order_id' => $order_number,
                ), home_url( '/checkout/' ) ),
            ),
        );

        $response = wp_remote_post( self::get_cf_api_base() . '/orders', array(
            'headers' => array(
                'Content-Type'    => 'application/json',
                'x-client-id'    => self::get_cf_app_id(),
                'x-client-secret' => self::get_cf_secret_key(),
                'x-api-version'  => '2023-08-01',
            ),
            'body'    => wp_json_encode( $cf_payload ),
            'timeout' => 30,
        ) );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( array( 'message' => 'Unable to connect to payment gateway. Please try again.' ) );
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        $code = wp_remote_retrieve_response_code( $response );

        if ( $code !== 200 || empty( $body['payment_session_id'] ) ) {
            $err_msg = isset( $body['message'] ) ? $body['message'] : 'Failed to create payment order.';
            wp_send_json_error( array( 'message' => $err_msg ) );
        }

        // Store Cashfree order ID
        $cf_order_id = isset( $body['cf_order_id'] ) ? $body['cf_order_id'] : '';
        $wpdb->update(
            $wpdb->prefix . 'ste_orders',
            array( 'cf_order_id' => $cf_order_id ),
            array( 'order_number' => $order_number ),
            array( '%s' ),
            array( '%s' )
        );

        wp_send_json_success( array(
            'payment_session_id' => $body['payment_session_id'],
            'order_id'           => $order_number,
            'cf_order_id'        => $cf_order_id,
            'plan'               => $plan,
            'plan_label'         => $label,
            'amount'             => number_format( $amount, 2 ),
        ) );
    }

    /* ══════════════════════════════════════
       STEP 2: VERIFY PAYMENT (called on return)
       ══════════════════════════════════════ */

    public static function verify_payment( $order_number ) {
        if ( ! self::is_cf_configured() || empty( $order_number ) ) return false;

        // Fetch payment status from Cashfree
        $response = wp_remote_get( self::get_cf_api_base() . '/orders/' . $order_number, array(
            'headers' => array(
                'x-client-id'    => self::get_cf_app_id(),
                'x-client-secret' => self::get_cf_secret_key(),
                'x-api-version'  => '2023-08-01',
            ),
            'timeout' => 30,
        ) );

        if ( is_wp_error( $response ) ) return false;

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( empty( $body['order_status'] ) ) return false;

        return $body;
    }

    /**
     * Process the return from Cashfree — verify and activate.
     * Called from the checkout template when order_id is in the URL.
     */
    public static function handle_return( $order_number, $plan ) {
        global $wpdb;
        $table = $wpdb->prefix . 'ste_orders';

        // Get local order
        $order = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE order_number = %s", $order_number
        ) );

        if ( ! $order ) return array( 'success' => false, 'message' => 'Order not found.' );

        // Already completed?
        if ( 'completed' === $order->status && ! empty( $order->license_key ) ) {
            return array(
                'success'     => true,
                'license_key' => $order->license_key,
                'plan'        => $order->plan,
                'plan_label'  => ucfirst( $order->plan ),
                'email'       => $order->customer_email,
                'amount'      => number_format( $order->amount, 2 ),
                'expires_at'  => isset( $order->expires_at ) ? $order->expires_at : '',
            );
        }

        // Verify with Cashfree
        $cf_data = self::verify_payment( $order_number );

        if ( ! $cf_data || 'PAID' !== strtoupper( $cf_data['order_status'] ) ) {
            $status = isset( $cf_data['order_status'] ) ? $cf_data['order_status'] : 'unknown';
            $wpdb->update( $table,
                array( 'status' => strtolower( $status ) ),
                array( 'order_number' => $order_number ),
                array( '%s' ), array( '%s' )
            );
            return array( 'success' => false, 'message' => 'Payment not completed. Status: ' . $status );
        }

        // Payment confirmed — generate license key
        $license_key    = STE_License::generate_key( $order->plan );
        $payment_method = '';
        $cf_payment_id  = '';

        // Get payment details
        $pay_response = wp_remote_get( self::get_cf_api_base() . '/orders/' . $order_number . '/payments', array(
            'headers' => array(
                'x-client-id'    => self::get_cf_app_id(),
                'x-client-secret' => self::get_cf_secret_key(),
                'x-api-version'  => '2023-08-01',
            ),
            'timeout' => 15,
        ) );
        if ( ! is_wp_error( $pay_response ) ) {
            $payments = json_decode( wp_remote_retrieve_body( $pay_response ), true );
            if ( is_array( $payments ) && ! empty( $payments[0] ) ) {
                $cf_payment_id  = isset( $payments[0]['cf_payment_id'] ) ? $payments[0]['cf_payment_id'] : '';
                $payment_method = isset( $payments[0]['payment_group'] ) ? $payments[0]['payment_group'] : '';
            }
        }

        // Calculate expiry based on billing cycle
        $billing_cycle = isset( $order->billing_cycle ) && 'yearly' === $order->billing_cycle ? 'yearly' : 'monthly';
        $expiry_period = ( 'yearly' === $billing_cycle ) ? '+365 days' : '+30 days';
        $expires_at    = date( 'Y-m-d H:i:s', strtotime( $expiry_period, current_time( 'timestamp' ) ) );

        // Update order
        $wpdb->update( $table, array(
            'status'         => 'completed',
            'license_key'    => $license_key,
            'cf_payment_id'  => $cf_payment_id,
            'payment_method' => $payment_method,
            'expires_at'     => $expires_at,
        ), array( 'order_number' => $order_number ),
        array( '%s', '%s', '%s', '%s', '%s' ), array( '%s' ) );

        // Send license key email to customer
        $plans = STE_License::get_all_plans();
        $label = isset( $plans[ $order->plan ]['label'] ) ? $plans[ $order->plan ]['label'] : $order->plan;

        self::send_license_email( $order->customer_email, $order->customer_name, $label, $license_key, $order_number, $order->amount, $expires_at );

        return array(
            'success'     => true,
            'license_key' => $license_key,
            'plan'        => $order->plan,
            'plan_label'  => $label,
            'email'       => $order->customer_email,
            'amount'      => number_format( $order->amount, 2 ),
            'expires_at'  => $expires_at,
        );
    }

    /* ══════════════════════════════════════
       SEND LICENSE KEY EMAIL
       ══════════════════════════════════════ */

    public static function send_license_email( $to_email, $customer_name, $plan_label, $license_key, $order_number, $amount, $expires_at = '' ) {
        $subject = sprintf( 'Your Smart Text Editor %s License Key', $plan_label );
        $site_url = home_url( '/' );

        $body = '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f4f4f7;font-family:Inter,Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f7;padding:40px 20px;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.06);">

    <!-- Header -->
    <tr><td style="background:linear-gradient(135deg,#6366f1,#a855f7);padding:32px 40px;text-align:center;">
        <h1 style="color:#ffffff;margin:0;font-size:22px;font-weight:700;">Smart Text Editor</h1>
        <p style="color:rgba(255,255,255,0.85);margin:8px 0 0;font-size:14px;">Payment Confirmation</p>
    </td></tr>

    <!-- Body -->
    <tr><td style="padding:40px;">
        <p style="font-size:16px;color:#333;margin:0 0 20px;">Hi <strong>' . esc_html( $customer_name ) . '</strong>,</p>
        <p style="font-size:15px;color:#555;margin:0 0 24px;line-height:1.6;">
            Thank you for purchasing the <strong>' . esc_html( $plan_label ) . '</strong> plan! Your payment has been confirmed and your license key is ready to use.
        </p>

        <!-- License Key Box -->
        <div style="background:#f8f7ff;border:2px solid #6366f1;border-radius:10px;padding:24px;text-align:center;margin:0 0 28px;">
            <p style="font-size:12px;text-transform:uppercase;letter-spacing:1px;color:#6366f1;margin:0 0 10px;font-weight:600;">Your License Key</p>
            <p style="font-size:22px;font-weight:700;color:#1a1a2e;margin:0;font-family:monospace;letter-spacing:2px;">' . esc_html( $license_key ) . '</p>
        </div>

        <!-- Order Details -->
        <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 28px;font-size:14px;color:#555;">
            <tr><td style="padding:8px 0;border-bottom:1px solid #eee;"><strong>Order Number</strong></td><td style="padding:8px 0;border-bottom:1px solid #eee;text-align:right;">' . esc_html( $order_number ) . '</td></tr>
            <tr><td style="padding:8px 0;border-bottom:1px solid #eee;"><strong>Plan</strong></td><td style="padding:8px 0;border-bottom:1px solid #eee;text-align:right;">' . esc_html( $plan_label ) . '</td></tr>
            <tr><td style="padding:8px 0;border-bottom:1px solid #eee;"><strong>Amount Paid</strong></td><td style="padding:8px 0;border-bottom:1px solid #eee;text-align:right;">₹' . esc_html( number_format( $amount, 2 ) ) . ' INR</td></tr>
            <tr><td style="padding:8px 0;border-bottom:1px solid #eee;"><strong>Valid Until</strong></td><td style="padding:8px 0;border-bottom:1px solid #eee;text-align:right;">' . ( $expires_at ? esc_html( date_i18n( 'F j, Y', strtotime( $expires_at ) ) ) : 'N/A' ) . '</td></tr>
        </table>

        <!-- Activation Steps -->
        <div style="background:#f0fdf4;border-radius:8px;padding:20px 24px;margin:0 0 28px;">
            <p style="font-size:14px;font-weight:600;color:#065f46;margin:0 0 12px;">How to Activate:</p>
            <ol style="margin:0;padding:0 0 0 20px;font-size:14px;color:#333;line-height:1.8;">
                <li>Go to your WordPress Admin Dashboard</li>
                <li>Navigate to <strong>Smart Editor &rarr; Plan &amp; License</strong></li>
                <li>Paste your license key and click <strong>Activate License</strong></li>
            </ol>
        </div>

        <p style="font-size:13px;color:#999;margin:0;line-height:1.6;">
            Keep this email safe — it contains your license key. If you have any questions, reply to this email and we\'ll be happy to help.
        </p>
    </td></tr>

    <!-- Footer -->
    <tr><td style="background:#f9fafb;padding:24px 40px;text-align:center;border-top:1px solid #eee;">
        <p style="font-size:12px;color:#999;margin:0;">
            &copy; ' . gmdate( 'Y' ) . ' Smart Text Editor &bull; <a href="' . esc_url( $site_url ) . '" style="color:#6366f1;text-decoration:none;">' . esc_html( parse_url( $site_url, PHP_URL_HOST ) ) . '</a>
        </p>
    </td></tr>

</table>
</td></tr>
</table>
</body>
</html>';

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: Smart Text Editor <no-reply@' . parse_url( $site_url, PHP_URL_HOST ) . '>',
            'Reply-To: ' . get_option( 'admin_email' ),
        );

        $sent = wp_mail( $to_email, $subject, $body, $headers );

        if ( ! $sent ) {
            // Email failed silently — order is stored, admin can resend from Orders page
        }
    }

    /* ══════════════════════════════════════
       WEBHOOK (Cashfree server notification)
       ══════════════════════════════════════ */

    public static function register_webhook() {
        register_rest_route( 'ste/v1', '/cashfree-webhook', array(
            'methods'             => 'POST',
            'callback'            => array( __CLASS__, 'handle_webhook' ),
            'permission_callback' => '__return_true',
        ) );
    }

    public static function handle_webhook( $request ) {
        $body = $request->get_json_params();

        if ( empty( $body['data']['order']['order_id'] ) ) {
            return new WP_REST_Response( array( 'status' => 'ignored' ), 200 );
        }

        $order_number = sanitize_text_field( $body['data']['order']['order_id'] );
        $event_type   = isset( $body['type'] ) ? $body['type'] : '';

        // Only process payment success events
        if ( 'PAYMENT_SUCCESS_WEBHOOK' !== $event_type ) {
            return new WP_REST_Response( array( 'status' => 'ignored' ), 200 );
        }

        // Verify & process (same as return handler)
        global $wpdb;
        $table = $wpdb->prefix . 'ste_orders';
        $order = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE order_number = %s", $order_number
        ) );

        if ( $order && 'completed' !== $order->status ) {
            self::handle_return( $order_number, $order->plan );
        }

        return new WP_REST_Response( array( 'status' => 'ok' ), 200 );
    }

    /* ══════════════════════════════════════
       ADMIN — SUBMENUS
       ══════════════════════════════════════ */

    public static function add_submenus() {
        add_submenu_page( 'smart-text-editor',
            __( 'Orders', 'smart-text-editor' ),
            __( 'Orders', 'smart-text-editor' ),
            'manage_options', 'ste-orders',
            array( __CLASS__, 'render_orders_page' )
        );
        add_submenu_page( 'smart-text-editor',
            __( 'Payment Settings', 'smart-text-editor' ),
            __( 'Payment Settings', 'smart-text-editor' ),
            'manage_options', 'ste-payment-settings',
            array( __CLASS__, 'render_settings_page' )
        );
    }

    /* ── Payment Settings Page ── */

    public static function render_settings_page() {
        $mode   = self::get_cf_mode();
        $app_id = self::get_cf_app_id();
        $secret = self::get_cf_secret_key();
        $webhook_url    = rest_url( 'ste/v1/cashfree-webhook' );
        $smtp_ready     = self::is_smtp_configured();
        $smtp_email     = self::get_smtp_sender_email();

        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Payment Settings — Cashfree', 'smart-text-editor' ); ?></h1>

            <!-- Email Delivery Status -->
            <div style="max-width:640px;margin-top:20px;padding:18px 22px;border-radius:8px;border:1px solid <?php echo $smtp_ready ? '#a7f3d0' : '#fca5a5'; ?>;background:<?php echo $smtp_ready ? '#ecfdf5' : '#fef2f2'; ?>;">
                <h2 style="margin:0 0 8px;font-size:15px;color:<?php echo $smtp_ready ? '#065f46' : '#991b1b'; ?>;">
                    <?php echo $smtp_ready ? '&#10003; Email Delivery Ready' : '&#10007; Email Delivery Not Configured'; ?>
                </h2>
                <?php if ( $smtp_ready ) : ?>
                    <p style="margin:0;font-size:13px;color:#065f46;">
                        FluentSMTP is configured. Emails will be sent from <strong><?php echo esc_html( $smtp_email ); ?></strong>.
                    </p>
                <?php else : ?>
                    <p style="margin:0 0 10px;font-size:13px;color:#991b1b;">
                        FluentSMTP is not configured with a sender email. License keys and order confirmations <strong>cannot be delivered</strong>. Purchases are blocked until this is resolved.
                    </p>
                    <a href="<?php echo esc_url( admin_url( 'options-general.php?page=fluent-mail#/' ) ); ?>" class="button button-primary" style="background:#dc2626;border-color:#dc2626;">
                        Configure FluentSMTP Now
                    </a>
                <?php endif; ?>
            </div>

            <form method="post" action="options.php" style="max-width:640px;margin-top:20px;">
                <?php settings_fields( 'ste_cashfree_settings' ); ?>

                <div style="background:#fff;border:1px solid #e5e5e5;border-radius:8px;padding:28px;">
                    <h2 style="margin-top:0;font-size:17px;">Cashfree API Credentials</h2>
                    <p style="color:#777;font-size:13px;margin-top:0;">
                        Get your credentials from
                        <a href="https://merchant.cashfree.com/merchants/login" target="_blank" rel="noopener">Cashfree Merchant Dashboard</a>
                        &rarr; Developers &rarr; API Keys.
                    </p>

                    <table class="form-table" style="margin:0;">
                        <tr>
                            <th scope="row"><label for="ste_cf_mode">Environment</label></th>
                            <td>
                                <select name="ste_cf_mode" id="ste_cf_mode">
                                    <option value="sandbox" <?php selected( $mode, 'sandbox' ); ?>>Sandbox (Testing)</option>
                                    <option value="production" <?php selected( $mode, 'production' ); ?>>Production (Live)</option>
                                </select>
                                <p class="description">Use <strong>Sandbox</strong> for testing. Switch to <strong>Production</strong> when ready to accept real payments.</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ste_cf_app_id">App ID</label></th>
                            <td>
                                <input type="text" name="ste_cf_app_id" id="ste_cf_app_id" value="<?php echo esc_attr( $app_id ); ?>" class="regular-text" placeholder="e.g. 123456abc789def012345">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="ste_cf_secret_key">Secret Key</label></th>
                            <td>
                                <input type="password" name="ste_cf_secret_key" id="ste_cf_secret_key" value="<?php echo esc_attr( $secret ); ?>" class="regular-text" placeholder="e.g. cfsk_ma_prod_xxxxx">
                                <p class="description">Keep this secret. Never share it publicly.</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div style="background:#fff;border:1px solid #e5e5e5;border-radius:8px;padding:28px;margin-top:16px;">
                    <h2 style="margin-top:0;font-size:17px;">Webhook URL</h2>
                    <p style="color:#777;font-size:13px;margin-top:0;">
                        Add this URL in your Cashfree Dashboard under <strong>Developers &rarr; Webhooks</strong>:
                    </p>
                    <code style="display:block;background:#f5f5f5;padding:10px 14px;border-radius:6px;font-size:13px;word-break:break-all;">
                        <?php echo esc_html( $webhook_url ); ?>
                    </code>
                    <p class="description" style="margin-top:8px;">Select the event: <strong>PAYMENT_SUCCESS_WEBHOOK</strong></p>
                </div>

                <?php
                if ( self::is_cf_configured() ) :
                    $env_label = 'production' === $mode ? 'LIVE' : 'SANDBOX';
                    $env_color = 'production' === $mode ? '#065f46;background:#d1fae5' : '#92400e;background:#fef3c7';
                ?>
                <div style="margin-top:16px;padding:12px 16px;border-radius:8px;border:1px solid #e5e5e5;background:#fff;">
                    <span style="display:inline-block;padding:2px 10px;border-radius:50px;font-size:11px;font-weight:700;color:<?php echo $env_color; ?>;">
                        <?php echo esc_html( $env_label ); ?>
                    </span>
                    &nbsp; Cashfree is configured and ready.
                </div>
                <?php endif; ?>

                <?php submit_button( __( 'Save Settings', 'smart-text-editor' ) ); ?>
            </form>
        </div>
        <?php
    }

    /* ── Orders Page ── */

    public static function render_orders_page() {
        global $wpdb;
        $table  = $wpdb->prefix . 'ste_orders';
        $orders = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `{$table}` ORDER BY created_at DESC LIMIT %d", 100 ) );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Smart Text Editor — Orders', 'smart-text-editor' ); ?></h1>

            <?php if ( empty( $orders ) ) : ?>
                <div style="margin-top:24px;padding:40px;background:#fff;border:1px solid #e5e5e5;border-radius:8px;text-align:center;">
                    <p style="font-size:16px;color:#999;margin:0;">No orders yet.</p>
                </div>
            <?php else : ?>
                <table class="wp-list-table widefat fixed striped" style="margin-top:16px;">
                    <thead>
                        <tr>
                            <th style="width:130px;">Order #</th>
                            <th style="width:80px;">Plan</th>
                            <th style="width:70px;">Billing</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th style="width:90px;">Amount</th>
                            <th style="width:90px;">Method</th>
                            <th>License Key</th>
                            <th style="width:80px;">Status</th>
                            <th style="width:120px;">Expires</th>
                            <th style="width:140px;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $orders as $order ) :
                            $order_billing = isset( $order->billing_cycle ) ? $order->billing_cycle : 'monthly';
                            $order_expires = isset( $order->expires_at ) ? $order->expires_at : '';
                        ?>
                        <tr>
                            <td><strong><?php echo esc_html( $order->order_number ); ?></strong></td>
                            <td>
                                <span style="display:inline-block;padding:2px 10px;border-radius:50px;font-size:11px;font-weight:700;text-transform:uppercase;
                                    <?php echo 'business' === $order->plan
                                        ? 'background:rgba(234,179,8,.1);color:#ca8a04;'
                                        : 'background:rgba(99,102,241,.1);color:#6366f1;'; ?>">
                                    <?php echo esc_html( $order->plan ); ?>
                                </span>
                            </td>
                            <td>
                                <span style="display:inline-block;padding:2px 8px;border-radius:50px;font-size:10px;font-weight:600;text-transform:uppercase;
                                    <?php echo 'yearly' === $order_billing
                                        ? 'background:#ecfdf5;color:#059669;'
                                        : 'background:#f0f9ff;color:#0284c7;'; ?>">
                                    <?php echo esc_html( $order_billing ); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html( $order->customer_name ); ?></td>
                            <td><a href="mailto:<?php echo esc_attr( $order->customer_email ); ?>"><?php echo esc_html( $order->customer_email ); ?></a></td>
                            <td>₹<?php echo esc_html( number_format( $order->amount, 2 ) ); ?></td>
                            <td><?php echo esc_html( $order->payment_method ?: '—' ); ?></td>
                            <td>
                                <?php if ( $order->license_key ) : ?>
                                    <code style="font-size:11px;background:#f5f5f5;padding:2px 6px;border-radius:3px;"><?php echo esc_html( $order->license_key ); ?></code>
                                <?php else : ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="display:inline-block;padding:2px 10px;border-radius:50px;font-size:11px;font-weight:600;
                                    <?php
                                    switch ( $order->status ) {
                                        case 'completed': echo 'background:#d1fae5;color:#065f46;'; break;
                                        case 'pending': echo 'background:#fef3c7;color:#92400e;'; break;
                                        default: echo 'background:#fee2e2;color:#991b1b;'; break;
                                    }
                                    ?>">
                                    <?php echo esc_html( $order->status ); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ( $order_expires && 'completed' === $order->status ) :
                                    $is_expired = current_time( 'timestamp' ) > strtotime( $order_expires );
                                ?>
                                    <span style="font-size:12px;<?php echo $is_expired ? 'color:#dc2626;' : 'color:#333;'; ?>">
                                        <?php echo esc_html( date_i18n( 'M j, Y', strtotime( $order_expires ) ) ); ?>
                                        <?php if ( $is_expired ) echo '<br><small style="color:#dc2626;">Expired</small>'; ?>
                                    </span>
                                <?php else : ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html( date_i18n( 'M j, Y g:i a', strtotime( $order->created_at ) ) ); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p style="margin-top:12px;color:#999;font-size:12px;">Showing last 100 orders.</p>
            <?php endif; ?>
        </div>
        <?php
    }
}
