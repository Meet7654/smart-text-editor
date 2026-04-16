<?php
/**
 * Template Name: Privacy Policy
 * Description: Privacy Policy page styled to match the Smart Text Editor theme.
 */
get_header();
$site_name = get_bloginfo( 'name' );
$admin_email = get_option( 'admin_email' );
?>

<!-- Hero -->
<section class="ste-legal-hero">
    <div class="ste-legal-hero-bg">
        <div class="ste-legal-orb ste-legal-orb-1"></div>
        <div class="ste-legal-orb ste-legal-orb-2"></div>
    </div>
    <div class="ste-container" style="position:relative;text-align:center;max-width:760px;">
        <span class="ste-section-badge" data-ste-anim="fade" data-ste-anim-dur="0.4">Legal</span>
        <h1 class="ste-legal-hero-title" data-ste-anim="slide-up" data-ste-anim-dur="0.5">Privacy Policy</h1>
        <p class="ste-legal-hero-sub" data-ste-anim="fade" data-ste-anim-dur="0.6">
            Your privacy matters to us. This policy explains what data we collect, how we use it, and your rights.
        </p>
        <div data-ste-anim="slide-up" data-ste-anim-dur="0.6" style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap;">
            <span class="ste-legal-meta-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Last updated: <?php echo esc_html( date( 'F j, Y' ) ); ?>
            </span>
            <span class="ste-legal-meta-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                GDPR &amp; DPDP Compliant
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
                        <a href="#pp-overview">1. Overview</a>
                        <a href="#pp-collect">2. Data We Collect</a>
                        <a href="#pp-use">3. How We Use Data</a>
                        <a href="#pp-payments">4. Payments</a>
                        <a href="#pp-cookies">5. Cookies</a>
                        <a href="#pp-sharing">6. Data Sharing</a>
                        <a href="#pp-retention">7. Data Retention</a>
                        <a href="#pp-rights">8. Your Rights</a>
                        <a href="#pp-security">9. Security</a>
                        <a href="#pp-children">10. Children</a>
                        <a href="#pp-changes">11. Policy Changes</a>
                        <a href="#pp-contact">12. Contact Us</a>
                    </nav>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="ste-legal-content">

                <!-- 1. Overview -->
                <div id="pp-overview" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#eef2ff;color:#6366f1;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    </div>
                    <h2>1. Overview</h2>
                    <p>
                        Smart Text Editor ("we", "our", "us") is a WordPress plugin and web service developed by Meet Patel, based in India.
                        This Privacy Policy applies to all users of <strong><?php echo esc_html( home_url() ); ?></strong> and the Smart Text Editor plugin.
                    </p>
                    <p>
                        By using our website or plugin, you agree to the collection and use of information as described in this policy.
                        If you do not agree, please discontinue use of our services.
                    </p>
                    <div class="ste-legal-highlight">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <span>We do <strong>not</strong> sell your personal data to third parties. Ever.</span>
                    </div>
                </div>

                <!-- 2. Data We Collect -->
                <div id="pp-collect" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#ecfdf5;color:#059669;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                    </div>
                    <h2>2. Data We Collect</h2>
                    <p>We collect the following categories of information:</p>

                    <h3>2.1 Information You Provide</h3>
                    <ul>
                        <li><strong>Account &amp; Purchase Data:</strong> Name, email address, and billing details when you purchase a license or create an account.</li>
                        <li><strong>Contact Form Data:</strong> Name, email, and message content when you contact us via the contact page.</li>
                        <li><strong>License Key:</strong> The license key you enter to activate the plugin.</li>
                    </ul>

                    <h3>2.2 Automatically Collected Data</h3>
                    <ul>
                        <li><strong>Usage Data:</strong> Pages visited, time spent, browser type, operating system, and referring URLs.</li>
                        <li><strong>IP Address:</strong> Collected for security, fraud prevention, and analytics.</li>
                        <li><strong>Cookies &amp; Local Storage:</strong> Session data, preferences, and analytics identifiers (see Section 5).</li>
                    </ul>

                    <h3>2.3 Plugin Data</h3>
                    <ul>
                        <li>The Smart Text Editor plugin processes text content <strong>entirely within your browser</strong>. We do not receive or store the documents you create.</li>
                        <li>License validation requests send only your license key and your site's domain to our server.</li>
                    </ul>
                </div>

                <!-- 3. How We Use Data -->
                <div id="pp-use" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#fff7ed;color:#ea580c;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
                    </div>
                    <h2>3. How We Use Your Data</h2>
                    <p>We use collected data for the following purposes:</p>
                    <div class="ste-legal-table-wrap">
                        <table class="ste-legal-table">
                            <thead>
                                <tr><th>Purpose</th><th>Legal Basis</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>Process your purchase and deliver your license</td><td>Contract performance</td></tr>
                                <tr><td>Send license keys, receipts, and order confirmations</td><td>Contract performance</td></tr>
                                <tr><td>Validate license keys and prevent abuse</td><td>Legitimate interest</td></tr>
                                <tr><td>Respond to support and contact form enquiries</td><td>Legitimate interest</td></tr>
                                <tr><td>Improve website performance and user experience</td><td>Legitimate interest</td></tr>
                                <tr><td>Send product updates and important notices</td><td>Legitimate interest / Consent</td></tr>
                                <tr><td>Comply with legal obligations</td><td>Legal obligation</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 4. Payments -->
                <div id="pp-payments" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#fdf2f8;color:#db2777;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    </div>
                    <h2>4. Payments &amp; Billing</h2>
                    <p>
                        All payments are processed by <strong>Cashfree Payments</strong>, a PCI-DSS compliant payment gateway.
                        We do <strong>not</strong> store your card number, CVV, UPI ID, or any raw payment credentials on our servers.
                    </p>
                    <p>
                        Cashfree may collect and process your payment data in accordance with their own
                        <a href="https://www.cashfree.com/privacy-policy" target="_blank" rel="noopener noreferrer">Privacy Policy</a>.
                        We receive only a transaction confirmation and your billing name/email.
                    </p>
                    <div class="ste-legal-highlight ste-legal-highlight-green">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <span>Payments are secured with 256-bit SSL encryption via Cashfree's PCI-DSS Level 1 infrastructure.</span>
                    </div>
                </div>

                <!-- 5. Cookies -->
                <div id="pp-cookies" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#fffbeb;color:#d97706;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M8.56 2.75c4.37 6.03 6.02 9.42 8.03 17.72m2.54-15.38c-3.72 4.35-8.94 5.66-16.88 5.85m19.5 1.9c-3.5-.93-6.63-.82-8.94 0-2.58.92-5.01 2.86-7.44 6.32"/></svg>
                    </div>
                    <h2>5. Cookies &amp; Tracking</h2>
                    <p>We use the following types of cookies:</p>
                    <ul>
                        <li><strong>Essential Cookies:</strong> Required for the website and WordPress to function (session, login, security nonces). Cannot be disabled.</li>
                        <li><strong>Preference Cookies:</strong> Remember your settings such as editor preferences and font choices.</li>
                        <li><strong>Analytics Cookies:</strong> Help us understand how visitors use the site (e.g., page views, bounce rate). No personally identifiable information is stored.</li>
                    </ul>
                    <p>You can control cookies through your browser settings. Disabling essential cookies may affect site functionality.</p>
                </div>

                <!-- 6. Data Sharing -->
                <div id="pp-sharing" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#eef2ff;color:#6366f1;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                    </div>
                    <h2>6. Data Sharing &amp; Third Parties</h2>
                    <p>We share your data only in the following limited circumstances:</p>
                    <ul>
                        <li><strong>Cashfree Payments:</strong> To process transactions securely.</li>
                        <li><strong>Email Service Provider:</strong> To deliver transactional emails (license keys, receipts, support replies).</li>
                        <li><strong>Hosting Provider:</strong> Our web host may have access to server logs containing IP addresses.</li>
                        <li><strong>Legal Requirements:</strong> If required by law, court order, or to protect our rights and users' safety.</li>
                    </ul>
                    <p>We do <strong>not</strong> share, sell, rent, or trade your personal information with any third party for marketing purposes.</p>
                </div>

                <!-- 7. Data Retention -->
                <div id="pp-retention" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#ecfdf5;color:#059669;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <h2>7. Data Retention</h2>
                    <ul>
                        <li><strong>Purchase &amp; License Records:</strong> Retained for 7 years to comply with Indian tax and accounting laws.</li>
                        <li><strong>Contact Form Submissions:</strong> Retained for up to 2 years, then permanently deleted.</li>
                        <li><strong>Account Data:</strong> Retained while your account is active. Deleted within 30 days of an account deletion request.</li>
                        <li><strong>Server Logs:</strong> Automatically purged after 90 days.</li>
                    </ul>
                </div>

                <!-- 8. Your Rights -->
                <div id="pp-rights" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#fdf2f8;color:#db2777;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <h2>8. Your Rights</h2>
                    <p>Under GDPR and India's Digital Personal Data Protection (DPDP) Act, you have the following rights:</p>
                    <div class="ste-legal-rights-grid">
                        <div class="ste-legal-right-card">
                            <strong>Access</strong>
                            <p>Request a copy of the personal data we hold about you.</p>
                        </div>
                        <div class="ste-legal-right-card">
                            <strong>Correction</strong>
                            <p>Ask us to correct inaccurate or incomplete data.</p>
                        </div>
                        <div class="ste-legal-right-card">
                            <strong>Deletion</strong>
                            <p>Request deletion of your personal data ("right to be forgotten").</p>
                        </div>
                        <div class="ste-legal-right-card">
                            <strong>Portability</strong>
                            <p>Receive your data in a structured, machine-readable format.</p>
                        </div>
                        <div class="ste-legal-right-card">
                            <strong>Objection</strong>
                            <p>Object to processing based on legitimate interests.</p>
                        </div>
                        <div class="ste-legal-right-card">
                            <strong>Withdraw Consent</strong>
                            <p>Withdraw consent at any time where processing is consent-based.</p>
                        </div>
                    </div>
                    <p style="margin-top:20px;">To exercise any of these rights, email us at <a href="mailto:<?php echo esc_attr( $admin_email ); ?>"><?php echo esc_html( $admin_email ); ?></a>. We will respond within 30 days.</p>
                </div>

                <!-- 9. Security -->
                <div id="pp-security" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#eef2ff;color:#6366f1;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </div>
                    <h2>9. Security</h2>
                    <p>
                        We implement industry-standard security measures including SSL/TLS encryption, secure password hashing,
                        and regular security audits. However, no method of transmission over the internet is 100% secure.
                        We cannot guarantee absolute security but are committed to protecting your data.
                    </p>
                    <p>
                        In the event of a data breach that affects your rights and freedoms, we will notify you and the relevant
                        authorities within 72 hours as required by applicable law.
                    </p>
                </div>

                <!-- 10. Children -->
                <div id="pp-children" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#fff7ed;color:#ea580c;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    </div>
                    <h2>10. Children's Privacy</h2>
                    <p>
                        Our services are not directed to individuals under the age of 18. We do not knowingly collect personal
                        data from children. If you believe a child has provided us with personal information, please contact us
                        immediately and we will delete it.
                    </p>
                </div>

                <!-- 11. Changes -->
                <div id="pp-changes" class="ste-legal-section">
                    <div class="ste-legal-section-icon" style="background:#ecfdf5;color:#059669;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
                    </div>
                    <h2>11. Changes to This Policy</h2>
                    <p>
                        We may update this Privacy Policy from time to time. When we do, we will revise the "Last updated" date
                        at the top of this page. For significant changes, we will notify active users via email.
                        Continued use of our services after changes constitutes acceptance of the updated policy.
                    </p>
                </div>

                <!-- 12. Contact -->
                <div id="pp-contact" class="ste-legal-section ste-legal-section-last">
                    <div class="ste-legal-section-icon" style="background:#fdf2f8;color:#db2777;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <h2>12. Contact Us</h2>
                    <p>For any privacy-related questions, requests, or concerns, please reach out:</p>
                    <div class="ste-legal-contact-box">
                        <div class="ste-legal-contact-row">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <span><strong>Data Controller:</strong> Meet Patel</span>
                        </div>
                        <div class="ste-legal-contact-row">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <span><strong>Email:</strong> <a href="mailto:<?php echo esc_attr( $admin_email ); ?>"><?php echo esc_html( $admin_email ); ?></a></span>
                        </div>
                        <div class="ste-legal-contact-row">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <span><strong>Response Time:</strong> Within 30 days</span>
                        </div>
                        <div class="ste-legal-contact-row">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span><strong>Jurisdiction:</strong> India (Gujarat)</span>
                        </div>
                    </div>
                    <div style="margin-top:24px;">
                        <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="ste-btn ste-btn-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            Contact Us
                        </a>
                        <a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>" class="ste-btn ste-btn-outline" style="margin-left:12px;">View Terms &amp; Conditions</a>
                        <a href="<?php echo esc_url( home_url( '/refund-policy/' ) ); ?>" class="ste-btn ste-btn-outline" style="margin-left:12px;">Refund Policy</a>
                    </div>
                </div>

            </div><!-- /.ste-legal-content -->
        </div><!-- /.ste-legal-layout -->
    </div>
</section>

<?php get_footer(); ?>
