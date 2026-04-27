<?php
/**
 * Email template: License key delivery.
 *
 * Available variables (all pre-sanitised by send_license_email()):
 *   string $customer_name
 *   string $plan_label
 *   string $license_key
 *   string $order_number
 *   float  $amount
 *   string $expires_at   — Y-m-d H:i:s or empty string
 *   string $site_url
 *   string $site_host
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
</head>
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
        <p style="font-size:16px;color:#333;margin:0 0 20px;">
            Hi <strong><?php echo esc_html( $customer_name ); ?></strong>,
        </p>
        <p style="font-size:15px;color:#555;margin:0 0 24px;line-height:1.6;">
            Thank you for purchasing the <strong><?php echo esc_html( $plan_label ); ?></strong> plan!
            Your payment has been confirmed and your license key is ready to use.
        </p>

        <!-- License Key Box -->
        <div style="background:#f8f7ff;border:2px solid #6366f1;border-radius:10px;padding:24px;text-align:center;margin:0 0 28px;">
            <p style="font-size:12px;text-transform:uppercase;letter-spacing:1px;color:#6366f1;margin:0 0 10px;font-weight:600;">Your License Key</p>
            <p style="font-size:22px;font-weight:700;color:#1a1a2e;margin:0;font-family:monospace;letter-spacing:2px;">
                <?php echo esc_html( $license_key ); ?>
            </p>
        </div>

        <!-- Order Details -->
        <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 28px;font-size:14px;color:#555;">
            <tr>
                <td style="padding:8px 0;border-bottom:1px solid #eee;"><strong>Order Number</strong></td>
                <td style="padding:8px 0;border-bottom:1px solid #eee;text-align:right;"><?php echo esc_html( $order_number ); ?></td>
            </tr>
            <tr>
                <td style="padding:8px 0;border-bottom:1px solid #eee;"><strong>Plan</strong></td>
                <td style="padding:8px 0;border-bottom:1px solid #eee;text-align:right;"><?php echo esc_html( $plan_label ); ?></td>
            </tr>
            <tr>
                <td style="padding:8px 0;border-bottom:1px solid #eee;"><strong>Amount Paid</strong></td>
                <td style="padding:8px 0;border-bottom:1px solid #eee;text-align:right;">
                    &#8377;<?php echo esc_html( number_format( (float) $amount, 2 ) ); ?> INR
                </td>
            </tr>
            <tr>
                <td style="padding:8px 0;border-bottom:1px solid #eee;"><strong>Valid Until</strong></td>
                <td style="padding:8px 0;border-bottom:1px solid #eee;text-align:right;">
                    <?php echo $expires_at ? esc_html( date_i18n( 'F j, Y', strtotime( $expires_at ) ) ) : 'N/A'; ?>
                </td>
            </tr>
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
            Keep this email safe &mdash; it contains your license key.
            If you have any questions, reply to this email and we&rsquo;ll be happy to help.
        </p>
    </td></tr>

    <!-- Footer -->
    <tr><td style="background:#f9fafb;padding:24px 40px;text-align:center;border-top:1px solid #eee;">
        <p style="font-size:12px;color:#999;margin:0;">
            &copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> Smart Text Editor &bull;
            <a href="<?php echo esc_url( $site_url ); ?>" style="color:#6366f1;text-decoration:none;">
                <?php echo esc_html( $site_host ); ?>
            </a>
        </p>
    </td></tr>

</table>
</td></tr>
</table>
</body>
</html>
