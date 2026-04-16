<?php
/**
 * Template Name: Contact Page
 * Description: A beautifully designed contact page with animated hero, info cards, FAQ, and map placeholder.
 */
get_header();
?>

<!-- Hero -->
<section class="ste-contact-hero">
    <div class="ste-contact-hero-bg">
        <div class="ste-contact-orb ste-contact-orb-1"></div>
        <div class="ste-contact-orb ste-contact-orb-2"></div>
        <div class="ste-contact-orb ste-contact-orb-3"></div>
    </div>
    <div class="ste-container" style="position:relative;text-align:center;max-width:720px;">
        <span class="ste-section-badge" data-ste-anim="fade" data-ste-anim-dur="0.4">Get in Touch</span>
        <h1 class="ste-contact-hero-title" data-ste-anim="slide-up" data-ste-anim-dur="0.6">
            <?php the_title(); ?>
        </h1>
        <?php if ( has_excerpt() ) : ?>
            <p class="ste-contact-hero-sub" data-ste-anim="fade" data-ste-anim-dur="0.7"><?php echo esc_html( get_the_excerpt() ); ?></p>
        <?php else : ?>
            <p class="ste-contact-hero-sub" data-ste-anim="fade" data-ste-anim-dur="0.7">Have a question, suggestion, or just want to say hello? We'd love to hear from you.</p>
        <?php endif; ?>
        <div data-ste-anim="slide-up" data-ste-anim-dur="0.6" style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap;">
            <a href="#contact-info" class="ste-btn ste-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                Contact Info
            </a>
            <a href="#faq" class="ste-btn ste-btn-outline">View FAQ</a>
        </div>
    </div>
</section>

<!-- Contact Info Cards -->
<section id="contact-info" class="ste-section" style="padding-top:80px;padding-bottom:80px;">
    <div class="ste-container" style="max-width:1000px;">
        <div class="ste-section-header" data-ste-anim="fade" data-ste-anim-dur="0.4">
            <span class="ste-section-badge">Reach Out</span>
            <h2 class="ste-section-title">How to Contact Us</h2>
            <p class="ste-section-sub">Choose the way that works best for you.</p>
        </div>
        <div class="ste-contact-info-grid">
            <div class="ste-contact-info-card" data-ste-anim="slide-up" data-ste-anim-dur="0.4">
                <div class="ste-ci-icon ste-ci-icon-email">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                </div>
                <div class="ste-ci-text">
                    <h3>Email Us</h3>
                    <p>pmeet4771@gmail.com</p>
                    <a href="mailto:pmeet4771@gmail.com">Send an email &rarr;</a>
                </div>
            </div>
            <div class="ste-contact-info-card" data-ste-anim="slide-up" data-ste-anim-dur="0.5">
                <div class="ste-ci-icon ste-ci-icon-phone">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                </div>
                <div class="ste-ci-text">
                    <h3>Call Us</h3>
                    <p>+91 9106948211</p>
                    <span>Mon - Sat, 10AM - 7PM IST</span>
                </div>
            </div>
            <div class="ste-contact-info-card" data-ste-anim="slide-up" data-ste-anim-dur="0.6">
                <div class="ste-ci-icon ste-ci-icon-clock">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="ste-ci-text">
                    <h3>Response Time</h3>
                    <p>Within 24 hours</p>
                    <span>Usually much faster</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Links + FAQ -->
<section id="faq" class="ste-section" style="padding-top:0;padding-bottom:80px;">
    <div class="ste-container" style="max-width:1000px;">
        <div class="ste-contact-bottom-grid">
            <!-- Quick Links -->
            <div class="ste-contact-sidebar-card" data-ste-anim="slide-up" data-ste-anim-dur="0.4">
                <h3>Quick Links</h3>
                <ul class="ste-contact-links">
                    <li>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <a href="<?php echo esc_url( home_url( '/#features' ) ); ?>">View Features</a>
                    </li>
                    <li>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        <a href="<?php echo esc_url( home_url( '/#pricing' ) ); ?>">Pricing Plans</a>
                    </li>
                    <li>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                        <a href="<?php echo esc_url( home_url( '/#editor' ) ); ?>">Try the Editor</a>
                    </li>
                    <?php if ( is_user_logged_in() ) : ?>
                    <li>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=ste-license' ) ); ?>">Manage License</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- FAQ -->
            <div class="ste-contact-sidebar-card" data-ste-anim="slide-up" data-ste-anim-dur="0.5">
                <h3>Frequently Asked</h3>
                <div class="ste-contact-faq">
                    <details class="ste-faq-item">
                        <summary>How do I activate my license?</summary>
                        <p>Go to WordPress Admin &rarr; Smart Editor &rarr; Plan &amp; License, paste your key and click Activate.</p>
                    </details>
                    <details class="ste-faq-item">
                        <summary>Can I try before buying?</summary>
                        <p>Yes! We offer a 7-day free trial with all Pro features. No payment required.</p>
                    </details>
                    <details class="ste-faq-item">
                        <summary>Which payment methods are accepted?</summary>
                        <p>We accept UPI, Cards, Netbanking &amp; Wallets via Cashfree payment gateway.</p>
                    </details>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
