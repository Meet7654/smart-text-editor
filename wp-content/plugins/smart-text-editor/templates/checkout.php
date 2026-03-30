<?php
/**
 * Checkout page template — Cashfree Payment Gateway.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

$plan_key = isset( $_GET['plan'] ) ? sanitize_text_field( wp_unslash( $_GET['plan'] ) ) : 'pro';
if ( ! in_array( $plan_key, array( 'pro', 'business' ), true ) ) $plan_key = 'pro';

$plans = STE_License::get_all_plans();
$plan  = $plans[ $plan_key ];

// Check if returning from Cashfree payment
$return_order_id = isset( $_GET['order_id'] ) ? sanitize_text_field( wp_unslash( $_GET['order_id'] ) ) : '';
$return_result   = null;

if ( $return_order_id ) {
    $return_result = STE_Checkout::handle_return( $return_order_id, $plan_key );
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — Smart Text Editor <?php echo esc_html( $plan['label'] ); ?></title>
    <?php wp_head(); ?>
</head>
<body class="ste-checkout-body">

<div class="ste-checkout-page">
    <!-- Header -->
    <div class="ste-checkout-header">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ste-checkout-logo">
            <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                <rect width="32" height="32" rx="8" fill="url(#clogo)"/>
                <path d="M8 10h16M8 16h12M8 22h14" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/>
                <defs><linearGradient id="clogo" x1="0" y1="0" x2="32" y2="32"><stop stop-color="#6366f1"/><stop offset="1" stop-color="#a855f7"/></linearGradient></defs>
            </svg>
            <span>Smart<strong>TextEditor</strong></span>
        </a>
        <div class="ste-checkout-secure">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Secure Checkout
        </div>
    </div>

    <div class="ste-checkout-grid">
        <!-- Left Column -->
        <div>
            <?php if ( $return_result && $return_result['success'] ) : ?>
                <!-- ═══ SUCCESS STATE ═══ -->
                <div class="ste-checkout-success" id="ste-checkout-success">
                    <div class="ste-success-icon">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="16 8 10 16 7 13"/></svg>
                    </div>
                    <h1>Payment Successful!</h1>
                    <p class="ste-success-sub">Your <strong><?php echo esc_html( $return_result['plan_label'] ); ?></strong> plan license key is ready.</p>

                    <div class="ste-license-key-box">
                        <label>Your License Key</label>
                        <div class="ste-key-display">
                            <code id="ste-success-key"><?php echo esc_html( $return_result['license_key'] ); ?></code>
                            <button type="button" id="ste-copy-key" class="ste-copy-btn" title="Copy to clipboard">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="ste-success-info">
                        <p>A confirmation email has been sent to <strong><?php echo esc_html( $return_result['email'] ); ?></strong>.</p>
                    </div>

                    <div class="ste-success-steps">
                        <h3>How to activate:</h3>
                        <ol>
                            <li>Go to your WordPress Admin</li>
                            <li>Navigate to <strong>Smart Editor &rarr; Plan &amp; License</strong></li>
                            <li>Paste your license key and click <strong>Activate License</strong></li>
                        </ol>
                    </div>

                    <div class="ste-success-actions">
                        <?php if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) : ?>
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=ste-license' ) ); ?>" class="ste-checkout-btn">Activate Now</a>
                        <?php endif; ?>
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ste-back-link">Back to Homepage</a>
                    </div>
                </div>

            <?php elseif ( $return_order_id && ( ! $return_result || ! $return_result['success'] ) ) : ?>
                <!-- ═══ PAYMENT FAILED STATE ═══ -->
                <div class="ste-checkout-success" style="border-color: #fecaca;">
                    <div class="ste-success-icon">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    </div>
                    <h1 style="color:#ef4444;">Payment Not Completed</h1>
                    <p class="ste-success-sub"><?php echo esc_html( $return_result ? $return_result['message'] : 'Payment was not completed.' ); ?></p>
                    <div class="ste-success-actions">
                        <a href="<?php echo esc_url( home_url( '/checkout/?plan=' . $plan_key ) ); ?>" class="ste-checkout-btn">Try Again</a>
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ste-back-link">Back to Homepage</a>
                    </div>
                </div>

            <?php else : ?>
                <!-- ═══ CHECKOUT FORM ═══ -->
                <div class="ste-checkout-form-wrap" id="ste-checkout-form-wrap">
                    <h1>Complete your purchase</h1>

                    <!-- Plan switcher -->
                    <div class="ste-plan-switcher">
                        <a href="<?php echo esc_url( home_url( '/checkout/?plan=pro' ) ); ?>"
                           class="ste-plan-switch <?php echo 'pro' === $plan_key ? 'active' : ''; ?>">
                            Pro — $5/mo
                        </a>
                        <a href="<?php echo esc_url( home_url( '/checkout/?plan=business' ) ); ?>"
                           class="ste-plan-switch <?php echo 'business' === $plan_key ? 'active' : ''; ?>">
                            Business — $15/mo
                        </a>
                    </div>

                    <?php if ( ! STE_Checkout::is_cf_configured() ) : ?>
                        <div class="ste-checkout-error" style="display:block;">
                            Payment gateway is not configured yet. Please ask the site administrator to set up Cashfree credentials under <strong>Smart Editor &rarr; Payment Settings</strong>.
                        </div>
                    <?php else : ?>

                    <form id="ste-checkout-form" autocomplete="on" novalidate>
                        <input type="hidden" name="plan" value="<?php echo esc_attr( $plan_key ); ?>">

                        <div class="ste-form-section">
                            <h2>Your Details</h2>
                            <div class="ste-form-row">
                                <div class="ste-form-group">
                                    <label for="ste-name">Full Name</label>
                                    <input type="text" id="ste-name" name="name" placeholder="John Doe" required autocomplete="name">
                                </div>
                                <div class="ste-form-group">
                                    <label for="ste-email">Email Address</label>
                                    <input type="email" id="ste-email" name="email" placeholder="john@example.com" required autocomplete="email">
                                </div>
                            </div>
                            <div class="ste-form-group">
                                <label for="ste-phone">Phone Number</label>
                                <input type="tel" id="ste-phone" name="phone" placeholder="9876543210" required autocomplete="tel" maxlength="15">
                            </div>
                        </div>

                        <!-- Error message -->
                        <div id="ste-checkout-error" class="ste-checkout-error" style="display:none;"></div>

                        <!-- Submit -->
                        <button type="submit" id="ste-checkout-submit" class="ste-checkout-btn">
                            <span class="ste-btn-text">Pay $<?php echo esc_html( $plan['price_num'] ); ?>.00</span>
                            <span class="ste-btn-loading" style="display:none;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4m0 12v4m-7.07-3.93l2.83-2.83m8.48-8.48l2.83-2.83M2 12h4m12 0h4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83"/></svg>
                                Processing...
                            </span>
                        </button>

                        <p class="ste-checkout-terms">
                            You'll be redirected to Cashfree's secure payment page to complete your purchase. Supports UPI, Cards, Netbanking &amp; Wallets.
                        </p>
                    </form>

                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right: Order Summary -->
        <div class="ste-checkout-summary">
            <h2>Order Summary</h2>
            <div class="ste-summary-plan">
                <div class="ste-summary-plan-icon">
                    <svg width="24" height="24" viewBox="0 0 32 32" fill="none">
                        <rect width="32" height="32" rx="8" fill="url(#slogo)"/>
                        <path d="M8 10h16M8 16h12M8 22h14" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/>
                        <defs><linearGradient id="slogo" x1="0" y1="0" x2="32" y2="32"><stop stop-color="#6366f1"/><stop offset="1" stop-color="#a855f7"/></linearGradient></defs>
                    </svg>
                </div>
                <div>
                    <strong>Smart Text Editor — <?php echo esc_html( $plan['label'] ); ?></strong>
                    <span>Monthly subscription</span>
                </div>
            </div>

            <ul class="ste-summary-features">
                <?php if ( 'pro' === $plan_key ) : ?>
                    <li>50+ Google Fonts</li>
                    <li>All 36 Style Presets</li>
                    <li>Gradient, 3D &amp; Glow Effects</li>
                    <li>11 Scroll Animations</li>
                    <li>Table Editor</li>
                    <li>HTML &amp; CSS Export</li>
                    <li>Email Support</li>
                <?php else : ?>
                    <li>Everything in Pro</li>
                    <li>Unlimited Custom Presets</li>
                    <li>Custom Font Uploads</li>
                    <li>White-label Branding</li>
                    <li>Up to 10 Team Members</li>
                    <li>Multisite License</li>
                    <li>Priority Support</li>
                <?php endif; ?>
            </ul>

            <div class="ste-summary-line">
                <span>Subtotal</span>
                <span>$<?php echo esc_html( $plan['price_num'] ); ?>.00</span>
            </div>
            <div class="ste-summary-line ste-summary-total">
                <span>Total due today</span>
                <span>$<?php echo esc_html( $plan['price_num'] ); ?>.00 <small>USD</small></span>
            </div>

            <div class="ste-summary-guarantee">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                30-day money-back guarantee
            </div>

            <div class="ste-payment-methods" style="margin-top:18px;padding-top:16px;border-top:1px solid #f0f0f0;text-align:center;">
                <p style="font-size:11px;color:#999;margin:0 0 8px;">Powered by</p>
                <span style="font-size:14px;font-weight:700;color:#6366f1;">Cashfree</span>
                <p style="font-size:11px;color:#bbb;margin:8px 0 0;">UPI &bull; Cards &bull; Netbanking &bull; Wallets</p>
            </div>
        </div>
    </div>
</div>

<?php wp_footer(); ?>
</body>
</html>
