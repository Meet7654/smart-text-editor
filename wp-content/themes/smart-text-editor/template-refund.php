<?php
/**
 * Template Name: Refund & Cancellation Policy
 * Description: Refund and Cancellation Policy page styled to match the Smart Text Editor theme.
 */
get_header();
$admin_email = get_option( 'admin_email' );
?>

<!-- Hero -->
<section class="ste-legal-hero">
    <div class="ste-legal-hero-bg">
        <div class="ste-legal-orb ste-legal-orb-1" style="background:#10b981;"></div>
        <div class="ste-legal-orb ste-legal-orb-2" style="background:#6366f1;"></div>
    </div>
    <div class="ste-container" style="position:relative;text-align:center;max-width:760px;">
        <span class="ste-section-badge" data-ste-anim="fade" data-ste-anim-dur="0.4">Legal</span>
        <h1 class="ste-legal-hero-title" data-ste-anim="slide-up" data-ste-anim-dur="0.5">Refund &amp; Cancellation Policy</h1>
        <p class="ste-legal-hero-sub" data-ste-anim="fade" data-ste-anim-dur="0.6">
            We stand behind our product with a 7-day money-back guarantee. Here's everything you need to know about refunds and cancellations.
        </p>
        <div data-ste-anim="slide-up" data-ste-anim-dur="0.6" style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap;">
            <span class="ste-legal-meta-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Last updated: <?php echo esc_html( date( 'F j, Y' ) ); ?>
            </span>
            <span class="ste-legal-meta-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                7-Day Money-Back Guarantee
            </span>
        </div>
    </div>
</section>

<!-- Content -->
<section class="ste-legal-body">
    <div class="ste-container" style="max-width:1040px;">
        <div class="ste-legal-layout">

            <!-- Sticky TOC -->
            <aside class="ste-legal-toc">
                <div class="ste-legal-toc-inner">
                    <h4>Contents</h4>
                    <nav>
                        <a href="#rc-overview">1. Overview</a>
                        <a href="#rc-eligibility">2. Refund Eligibility</a>
                        <a href="#rc-ineligible">3. Non-Refundable Cases</a>
                        <a href="#rc-process">4. How to Request</a>
                        <a href="#rc-timeline">5. Processing Timeline</a>
                        <a href="#rc-cancellation">6. Cancellations</a>
                        <a href="#rc-renewals">7. Renewal Charges</a>
                        <a href="#rc-partial">8. Partial Refunds</a>
                        <a href="#rc-disputes">9. Disputes</a>
                        <a href="#rc-contact">10. Contact Us</a>
                    </nav>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="ste-legal-content">

                <!-- 1. Overview -->
                <div id="rc-overview" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#ecfdf5;color:#059669;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <h2>1. Overview</h2>
                    <p>
                        At Smart Text Editor, we are committed to your satisfaction. We offer a <strong>7-day money-back guarantee</strong>
                        on all paid plans — no questions asked, as long as your request falls within the eligibility criteria below.
                    </p>
                    <p>
                        This policy applies to all purchases made through <strong><?php echo esc_html( home_url() ); ?></strong>
                        and covers the Pro and Business plans (monthly and yearly billing cycles).
                    </p>
                    <div class="ste-legal-highlight ste-legal-highlight-green">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>If you are not satisfied within the first 7 days of purchase, we will issue a full refund — no hassle.</span>
                    </div>
                </div>

                <!-- 2. Refund Eligibility -->
                <div id="rc-eligibility" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#eef2ff;color:#6366f1;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <h2>2. Refund Eligibility</h2>
                    <p>You are eligible for a full refund if <strong>all</strong> of the following conditions are met:</p>
                    <ul>
                        <li>Your refund request is submitted within <strong>7 calendar days</strong> of the original purchase date.</li>
                        <li>The request is for the <strong>first purchase</strong> of a plan (not a renewal).</li>
                        <li>Your account has not been found to be in violation of our <a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>">Terms &amp; Conditions</a>.</li>
                        <li>The license key has not been used to activate the plugin on more sites than your plan permits.</li>
                    </ul>
                    <div class="ste-legal-table-wrap">
                        <table class="ste-legal-table">
                            <thead>
                                <tr><th>Plan</th><th>Refund Window</th><th>Refund Amount</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>Pro Monthly</td><td>7 days from purchase</td><td>100% of amount paid</td></tr>
                                <tr><td>Pro Yearly</td><td>7 days from purchase</td><td>100% of amount paid</td></tr>
                                <tr><td>Business Monthly</td><td>7 days from purchase</td><td>100% of amount paid</td></tr>
                                <tr><td>Business Yearly</td><td>7 days from purchase</td><td>100% of amount paid</td></tr>
                                <tr><td>Free Plan</td><td>N/A</td><td>N/A (no charge)</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. Non-Refundable Cases -->
                <div id="rc-ineligible" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#fff7ed;color:#ea580c;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                    </div>
                    <h2>3. Non-Refundable Cases</h2>
                    <p>Refunds will <strong>not</strong> be issued in the following circumstances:</p>
                    <ul>
                        <li>The refund request is submitted <strong>after 7 days</strong> from the original purchase date.</li>
                        <li>The request is for a <strong>subscription renewal</strong> charge (monthly or yearly auto-renewal).</li>
                        <li>The account has been <strong>suspended or terminated</strong> due to a violation of our Terms &amp; Conditions.</li>
                        <li>The license key was used to circumvent activation limits or shared with unauthorised users.</li>
                        <li>The purchase was made during a <strong>promotional or discounted period</strong> where the offer explicitly stated no refunds.</li>
                        <li>Refund requests citing incompatibility with third-party plugins or themes not listed in our compatibility documentation.</li>
                        <li>Requests for <strong>partial refunds</strong> for unused days within a billing period (see Section 8).</li>
                    </ul>
                    <div class="ste-legal-highlight">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>We strongly recommend using the <strong>7-day free trial</strong> before purchasing to ensure the plugin meets your needs.</span>
                    </div>
                </div>

                <!-- 4. How to Request -->
                <div id="rc-process" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#fdf2f8;color:#db2777;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <h2>4. How to Request a Refund</h2>
                    <p>To initiate a refund, follow these steps:</p>
                    <ul>
                        <li><strong>Step 1:</strong> Email us at <a href="mailto:<?php echo esc_attr( $admin_email ); ?>"><?php echo esc_html( $admin_email ); ?></a> with the subject line: <em>"Refund Request — [Your Order ID]"</em>.</li>
                        <li><strong>Step 2:</strong> Include your <strong>Order ID</strong> (found in your purchase confirmation email), the <strong>email address</strong> used at checkout, and a brief reason for the refund.</li>
                        <li><strong>Step 3:</strong> We will review your request and respond within <strong>2 business days</strong>.</li>
                        <li><strong>Step 4:</strong> If approved, the refund will be initiated to your original payment method within <strong>5–10 business days</strong>.</li>
                    </ul>
                    <div class="ste-legal-contact-box">
                        <div class="ste-legal-contact-row">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <span><strong>Refund Email:</strong> <a href="mailto:<?php echo esc_attr( $admin_email ); ?>"><?php echo esc_html( $admin_email ); ?></a></span>
                        </div>
                        <div class="ste-legal-contact-row">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <span><strong>Subject Line:</strong> Refund Request — [Your Order ID]</span>
                        </div>
                        <div class="ste-legal-contact-row">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <span><strong>Response Time:</strong> Within 2 business days</span>
                        </div>
                    </div>
                </div>

                <!-- 5. Processing Timeline -->
                <div id="rc-timeline" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#ecfdf5;color:#059669;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <h2>5. Processing Timeline</h2>
                    <p>Once a refund is approved, here is what to expect:</p>
                    <div class="ste-legal-table-wrap">
                        <table class="ste-legal-table">
                            <thead>
                                <tr><th>Stage</th><th>Timeframe</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>Refund request reviewed &amp; approved</td><td>Within 2 business days of your email</td></tr>
                                <tr><td>Refund initiated to Cashfree</td><td>Within 1 business day of approval</td></tr>
                                <tr><td>Cashfree processes the refund</td><td>3–5 business days</td></tr>
                                <tr><td>Credit appears in your account</td><td>5–10 business days total (bank-dependent)</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <p style="margin-top:16px;">
                        Refund timelines may vary depending on your bank or payment provider. UPI refunds are typically faster (1–3 days),
                        while credit/debit card refunds may take up to 10 business days to reflect on your statement.
                    </p>
                    <p>
                        Once a refund is initiated, your license key will be <strong>deactivated immediately</strong> and access to Pro features will be revoked.
                    </p>
                </div>

                <!-- 6. Cancellations -->
                <div id="rc-cancellation" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#eef2ff;color:#6366f1;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    </div>
                    <h2>6. Cancellations</h2>
                    <p>
                        You may cancel your subscription at any time. Cancellation stops future billing but does <strong>not</strong> automatically
                        trigger a refund for the current billing period.
                    </p>
                    <h3>How to Cancel</h3>
                    <ul>
                        <li>Go to your WordPress Admin → <strong>Smart Editor → Plan &amp; License</strong>.</li>
                        <li>Click <strong>Deactivate License</strong> to deactivate your current plan.</li>
                        <li>Email us at <a href="mailto:<?php echo esc_attr( $admin_email ); ?>"><?php echo esc_html( $admin_email ); ?></a> to cancel future auto-renewal charges.</li>
                    </ul>
                    <h3>What Happens After Cancellation</h3>
                    <ul>
                        <li>Your Pro or Business features remain active until the <strong>end of the current paid period</strong>.</li>
                        <li>After the period ends, your account reverts to the <strong>Free plan</strong> automatically.</li>
                        <li>Your content and posts are preserved — only the advanced styling features are restricted.</li>
                        <li>You can re-subscribe at any time to regain access to paid features.</li>
                    </ul>
                    <div class="ste-legal-highlight">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>To avoid being charged for the next billing cycle, cancel <strong>at least 24 hours before</strong> your renewal date.</span>
                    </div>
                </div>

                <!-- 7. Renewal Charges -->
                <div id="rc-renewals" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#fffbeb;color:#d97706;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
                    </div>
                    <h2>7. Renewal Charges</h2>
                    <p>
                        Subscriptions renew automatically at the end of each billing cycle (monthly or yearly).
                        You will receive a <strong>reminder email 7 days before renewal</strong> so you have time to cancel if needed.
                    </p>
                    <ul>
                        <li>Renewal charges are <strong>non-refundable</strong> once processed, as the new billing period has already begun.</li>
                        <li>If you did not intend to renew and contact us <strong>within 24 hours</strong> of the renewal charge, we will review your case on a goodwill basis.</li>
                        <li>To prevent renewal, cancel your subscription at least 24 hours before the renewal date (see Section 6).</li>
                    </ul>
                </div>

                <!-- 8. Partial Refunds -->
                <div id="rc-partial" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#fff7ed;color:#ea580c;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <h2>8. Partial Refunds</h2>
                    <p>
                        We do <strong>not</strong> offer pro-rated or partial refunds for unused days within a billing period.
                        For example, if you cancel a monthly plan on day 15, you will not receive a refund for the remaining 15 days.
                    </p>
                    <p>
                        The only exception is within the 7-day money-back guarantee window on the <strong>first purchase</strong>,
                        where a full refund is issued regardless of usage.
                    </p>
                </div>

                <!-- 9. Disputes -->
                <div id="rc-disputes" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#fdf2f8;color:#db2777;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>
                    <h2>9. Disputes &amp; Chargebacks</h2>
                    <p>
                        If you believe a charge was made in error, please <strong>contact us first</strong> before initiating a chargeback
                        with your bank or payment provider. We are committed to resolving issues quickly and fairly.
                    </p>
                    <p>
                        Initiating a chargeback without first contacting us may result in immediate suspension of your account
                        and license. Fraudulent chargebacks will be disputed with supporting transaction evidence.
                    </p>
                    <p>
                        All disputes are governed by the laws of <strong>India</strong> and subject to the jurisdiction of courts in <strong>Gujarat, India</strong>.
                        See our <a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>">Terms &amp; Conditions</a> for full details.
                    </p>
                </div>

                <!-- 10. Contact -->
                <div id="rc-contact" class="ste-legal-section ste-legal-section-last">
                    <div class="ste-legal-section-icon" style="background:#eef2ff;color:#6366f1;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <h2>10. Contact Us</h2>
                    <p>For any refund or cancellation queries, please reach out directly:</p>
                    <div class="ste-legal-contact-box">
                        <div class="ste-legal-contact-row">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <span><strong>Developer:</strong> Meet Patel</span>
                        </div>
                        <div class="ste-legal-contact-row">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <span><strong>Email:</strong> <a href="mailto:<?php echo esc_attr( $admin_email ); ?>"><?php echo esc_html( $admin_email ); ?></a></span>
                        </div>
                        <div class="ste-legal-contact-row">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <span><strong>Response Time:</strong> Within 2 business days</span>
                        </div>
                        <div class="ste-legal-contact-row">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span><strong>Jurisdiction:</strong> Gujarat, India</span>
                        </div>
                    </div>
                    <div style="margin-top:24px;">
                        <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="ste-btn ste-btn-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            Contact Us
                        </a>
                        <a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>" class="ste-btn ste-btn-outline" style="margin-left:12px;">View Terms &amp; Conditions</a>
                    </div>
                </div>

            </div><!-- /.ste-legal-content -->
        </div><!-- /.ste-legal-layout -->
    </div>
</section>

<?php get_footer(); ?>
