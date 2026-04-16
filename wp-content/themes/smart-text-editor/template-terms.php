<?php
/**
 * Template Name: Terms and Conditions
 * Description: Terms and Conditions page styled to match the Smart Text Editor theme.
 */
get_header();
$admin_email = get_option( 'admin_email' );
?>

<!-- Hero -->
<section class="ste-legal-hero">
    <div class="ste-legal-hero-bg">
        <div class="ste-legal-orb ste-legal-orb-1" style="background:#a855f7;"></div>
        <div class="ste-legal-orb ste-legal-orb-2" style="background:#06b6d4;"></div>
    </div>
    <div class="ste-container" style="position:relative;text-align:center;max-width:760px;">
        <span class="ste-section-badge" data-ste-anim="fade" data-ste-anim-dur="0.4">Legal</span>
        <h1 class="ste-legal-hero-title" data-ste-anim="slide-up" data-ste-anim-dur="0.5">Terms &amp; Conditions</h1>
        <p class="ste-legal-hero-sub" data-ste-anim="fade" data-ste-anim-dur="0.6">
            Please read these terms carefully before purchasing or using Smart Text Editor.
        </p>
        <div data-ste-anim="slide-up" data-ste-anim-dur="0.6" style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap;">
            <span class="ste-legal-meta-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Last updated: <?php echo esc_html( date( 'F j, Y' ) ); ?>
            </span>
            <span class="ste-legal-meta-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Governing Law: India
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
                        <a href="#tc-acceptance">1. Acceptance</a>
                        <a href="#tc-license">2. License Grant</a>
                        <a href="#tc-restrictions">3. Restrictions</a>
                        <a href="#tc-plans">4. Plans &amp; Pricing</a>
                        <a href="#tc-trial">5. Free Trial</a>
                        <a href="#tc-payments">6. Payments</a>
                        <a href="#tc-refunds">7. Refund Policy</a>
                        <a href="#tc-ip">8. Intellectual Property</a>
                        <a href="#tc-disclaimer">9. Disclaimer</a>
                        <a href="#tc-liability">10. Limitation of Liability</a>
                        <a href="#tc-termination">11. Termination</a>
                        <a href="#tc-governing">12. Governing Law</a>
                        <a href="#tc-changes">13. Changes to Terms</a>
                        <a href="#tc-contact">14. Contact</a>
                    </nav>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="ste-legal-content">

                <!-- 1. Acceptance -->
                <div id="tc-acceptance" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#eef2ff;color:#6366f1;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <h2>1. Acceptance of Terms</h2>
                    <p>
                        By accessing <strong><?php echo esc_html( home_url() ); ?></strong>, installing the Smart Text Editor plugin,
                        or purchasing a license, you ("User") agree to be bound by these Terms and Conditions.
                        If you do not agree, do not use our services.
                    </p>
                    <p>
                        These Terms constitute a legally binding agreement between you and Meet Patel ("Developer", "we", "us"),
                        the creator of Smart Text Editor.
                    </p>
                    <div class="ste-legal-highlight">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>By completing a purchase or activating a license key, you confirm you have read and accepted these terms.</span>
                    </div>
                </div>

                <!-- 2. License Grant -->
                <div id="tc-license" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#ecfdf5;color:#059669;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </div>
                    <h2>2. License Grant</h2>
                    <p>Subject to these Terms, we grant you a <strong>non-exclusive, non-transferable, revocable</strong> license to:</p>
                    <ul>
                        <li>Install and use the Smart Text Editor plugin on the number of WordPress sites permitted by your purchased plan.</li>
                        <li>Access all features included in your chosen plan tier (Free, Pro Monthly, or Pro Yearly).</li>
                        <li>Receive updates and improvements released during your active subscription period.</li>
                    </ul>
                    <div class="ste-legal-table-wrap">
                        <table class="ste-legal-table">
                            <thead>
                                <tr><th>Plan</th><th>Sites Allowed</th><th>License Type</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>Free</td><td>1 site</td><td>Perpetual (limited features)</td></tr>
                                <tr><td>Pro Monthly</td><td>1 site</td><td>Active while subscription is paid</td></tr>
                                <tr><td>Pro Yearly</td><td>1 site</td><td>Active for 12 months from purchase</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. Restrictions -->
                <div id="tc-restrictions" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#fff7ed;color:#ea580c;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                    </div>
                    <h2>3. Restrictions</h2>
                    <p>You may <strong>not</strong>:</p>
                    <ul>
                        <li>Redistribute, resell, sublicense, or share your license key with others.</li>
                        <li>Use a single license on more sites than your plan permits.</li>
                        <li>Reverse-engineer, decompile, or attempt to extract the source code of the plugin beyond what is publicly available.</li>
                        <li>Remove or alter any copyright, trademark, or proprietary notices in the plugin.</li>
                        <li>Use the plugin for any unlawful purpose or in violation of any applicable laws.</li>
                        <li>Attempt to circumvent license validation or activation mechanisms.</li>
                    </ul>
                    <p>Violation of these restrictions will result in immediate license termination without refund.</p>
                </div>

                <!-- 4. Plans & Pricing -->
                <div id="tc-plans" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#fdf2f8;color:#db2777;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    </div>
                    <h2>4. Plans &amp; Pricing</h2>
                    <p>Smart Text Editor is offered under the following plans:</p>
                    <div class="ste-legal-plans-grid">
                        <div class="ste-legal-plan-card">
                            <div class="ste-legal-plan-name">Free</div>
                            <div class="ste-legal-plan-price">₹0</div>
                            <ul>
                                <li>Basic text editing</li>
                                <li>Limited font selection</li>
                                <li>Standard export</li>
                            </ul>
                        </div>
                        <div class="ste-legal-plan-card ste-legal-plan-featured">
                            <div class="ste-legal-plan-badge">Most Popular</div>
                            <div class="ste-legal-plan-name">Pro Monthly</div>
                            <div class="ste-legal-plan-price">As listed on pricing page</div>
                            <ul>
                                <li>All Pro features</li>
                                <li>50+ Google Fonts</li>
                                <li>All style effects &amp; animations</li>
                                <li>Priority support</li>
                            </ul>
                        </div>
                        <div class="ste-legal-plan-card">
                            <div class="ste-legal-plan-name">Pro Yearly</div>
                            <div class="ste-legal-plan-price">As listed on pricing page</div>
                            <ul>
                                <li>All Pro features</li>
                                <li>Best value (save vs monthly)</li>
                                <li>Priority support</li>
                            </ul>
                        </div>
                    </div>
                    <p style="margin-top:16px;">All prices are in Indian Rupees (INR) and inclusive of applicable taxes unless stated otherwise. We reserve the right to change pricing with 30 days' notice to existing subscribers.</p>
                </div>

                <!-- 5. Free Trial -->
                <div id="tc-trial" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#ecfdf5;color:#059669;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <h2>5. Free Trial</h2>
                    <p>We offer a <strong>7-day free trial</strong> of the Pro plan with the following conditions:</p>
                    <ul>
                        <li>No payment information is required to start the trial.</li>
                        <li>The trial provides access to all Pro features for 7 calendar days from activation.</li>
                        <li>Each user is entitled to one free trial per email address.</li>
                        <li>At the end of the trial, access to Pro features will be revoked unless a paid plan is purchased.</li>
                        <li>We reserve the right to modify or discontinue the free trial offer at any time.</li>
                    </ul>
                </div>

                <!-- 6. Payments -->
                <div id="tc-payments" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#eef2ff;color:#6366f1;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    </div>
                    <h2>6. Payments &amp; Billing</h2>
                    <ul>
                        <li>All payments are processed securely through <strong>Cashfree Payments</strong>. We accept UPI, Credit/Debit Cards, Net Banking, and Wallets.</li>
                        <li>For monthly plans, billing recurs automatically on the same date each month unless cancelled.</li>
                        <li>For yearly plans, billing recurs annually unless cancelled before the renewal date.</li>
                        <li>You will receive an email receipt for every successful payment.</li>
                        <li>Failed payments may result in temporary suspension of Pro features until payment is resolved.</li>
                        <li>It is your responsibility to ensure your payment method remains valid.</li>
                    </ul>
                </div>

                <!-- 7. Refund Policy -->
                <div id="tc-refunds" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#fff7ed;color:#ea580c;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
                    </div>
                    <h2>7. Refund Policy</h2>
                    <div class="ste-legal-highlight ste-legal-highlight-green">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>We offer a <strong>7-day money-back guarantee</strong> on all paid plans.</span>
                    </div>
                    <p>To be eligible for a refund:</p>
                    <ul>
                        <li>Your refund request must be submitted within <strong>7 days</strong> of the original purchase date.</li>
                        <li>Contact us at <a href="mailto:<?php echo esc_attr( $admin_email ); ?>"><?php echo esc_html( $admin_email ); ?></a> with your order ID and reason for the refund.</li>
                        <li>Refunds are processed within 5–10 business days to the original payment method.</li>
                    </ul>
                    <p><strong>Refunds will not be issued for:</strong></p>
                    <ul>
                        <li>Requests made after the 7-day window.</li>
                        <li>Renewal charges where the renewal was not cancelled before the billing date.</li>
                        <li>Accounts found to have violated these Terms.</li>
                        <li>Partial months or unused days of a subscription period.</li>
                    </ul>
                </div>

                <!-- 8. Intellectual Property -->
                <div id="tc-ip" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#fdf2f8;color:#db2777;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>
                    <h2>8. Intellectual Property</h2>
                    <p>
                        Smart Text Editor, including its code, design, branding, logos, and documentation, is the exclusive
                        intellectual property of Meet Patel. All rights are reserved.
                    </p>
                    <p>
                        Your license grants you the right to <em>use</em> the plugin — it does not transfer any ownership
                        or intellectual property rights to you.
                    </p>
                    <p>
                        Content you create using the editor remains entirely your own. We claim no rights over documents,
                        text, or designs you produce with Smart Text Editor.
                    </p>
                </div>

                <!-- 9. Disclaimer -->
                <div id="tc-disclaimer" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#fffbeb;color:#d97706;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>
                    <h2>9. Disclaimer of Warranties</h2>
                    <p>
                        Smart Text Editor is provided <strong>"as is"</strong> and <strong>"as available"</strong> without warranties of any kind,
                        either express or implied, including but not limited to warranties of merchantability,
                        fitness for a particular purpose, or non-infringement.
                    </p>
                    <p>
                        We do not warrant that the plugin will be error-free, uninterrupted, or compatible with all
                        WordPress versions or hosting environments. It is your responsibility to maintain backups of your data.
                    </p>
                </div>

                <!-- 10. Limitation of Liability -->
                <div id="tc-liability" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#eef2ff;color:#6366f1;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <h2>10. Limitation of Liability</h2>
                    <p>
                        To the maximum extent permitted by applicable law, Meet Patel shall not be liable for any indirect,
                        incidental, special, consequential, or punitive damages, including but not limited to loss of profits,
                        data, or goodwill, arising from your use of or inability to use Smart Text Editor.
                    </p>
                    <p>
                        Our total liability to you for any claim arising from these Terms or your use of the plugin shall
                        not exceed the amount you paid us in the <strong>3 months preceding the claim</strong>.
                    </p>
                </div>

                <!-- 11. Termination -->
                <div id="tc-termination" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#fff7ed;color:#ea580c;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    </div>
                    <h2>11. Termination</h2>
                    <p>
                        You may cancel your subscription at any time through your account dashboard or by contacting us.
                        Cancellation takes effect at the end of the current billing period.
                    </p>
                    <p>
                        We reserve the right to suspend or terminate your license immediately, without notice or refund, if you:
                    </p>
                    <ul>
                        <li>Violate any provision of these Terms.</li>
                        <li>Engage in fraudulent, abusive, or illegal activity.</li>
                        <li>Attempt to circumvent license validation or payment systems.</li>
                    </ul>
                    <p>Upon termination, your right to use the plugin ceases immediately.</p>
                </div>

                <!-- 12. Governing Law -->
                <div id="tc-governing" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#ecfdf5;color:#059669;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <h2>12. Governing Law &amp; Disputes</h2>
                    <p>
                        These Terms are governed by and construed in accordance with the laws of <strong>India</strong>.
                        Any disputes arising from these Terms shall be subject to the exclusive jurisdiction of the courts
                        located in <strong>Gujarat, India</strong>.
                    </p>
                    <p>
                        Before initiating any legal proceedings, both parties agree to attempt to resolve disputes
                        amicably through direct communication for a period of at least 30 days.
                    </p>
                </div>

                <!-- 13. Changes -->
                <div id="tc-changes" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#fdf2f8;color:#db2777;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </div>
                    <h2>13. Changes to Terms</h2>
                    <p>
                        We reserve the right to modify these Terms at any time. When we make material changes, we will
                        update the "Last updated" date and notify active subscribers via email at least 14 days before
                        the changes take effect.
                    </p>
                    <p>
                        Continued use of Smart Text Editor after the effective date of changes constitutes your acceptance
                        of the revised Terms.
                    </p>
                </div>

                <!-- 14. Contact -->
                <div id="tc-contact" class="ste-legal-section ste-legal-section-last">
                    <div class="ste-legal-section-icon" style="background:#eef2ff;color:#6366f1;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <h2>14. Contact</h2>
                    <p>For questions about these Terms, please contact:</p>
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
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span><strong>Jurisdiction:</strong> Gujarat, India</span>
                        </div>
                    </div>
                    <div style="margin-top:24px;">
                        <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="ste-btn ste-btn-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            Contact Us
                        </a>
                        <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>" class="ste-btn ste-btn-outline" style="margin-left:12px;">View Privacy Policy</a>
                        <a href="<?php echo esc_url( home_url( '/refund-policy/' ) ); ?>" class="ste-btn ste-btn-outline" style="margin-left:12px;">Refund Policy</a>
                    </div>
                </div>

            </div><!-- /.ste-legal-content -->
        </div><!-- /.ste-legal-layout -->
    </div>
</section>

<?php get_footer(); ?>
