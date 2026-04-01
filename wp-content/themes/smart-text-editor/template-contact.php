<?php
/**
 * Template Name: Contact Page
 * Description: A beautifully designed contact page with animated hero, info cards, built-in form, FAQ, and map placeholder.
 */
get_header();
$admin_email = get_option( 'admin_email' );
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
            <a href="#contact-form" class="ste-btn ste-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                Send a Message
            </a>
            <a href="#contact-info" class="ste-btn ste-btn-outline">View Contact Info</a>
        </div>
    </div>
</section>

<!-- Contact Info Cards -->
<section id="contact-info" class="ste-section" style="padding-top:80px;padding-bottom:40px;">
    <div class="ste-container" style="max-width:1000px;">
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

            <div class="ste-contact-info-card" data-ste-anim="slide-up" data-ste-anim-dur="0.7">
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

<!-- Contact Form + Sidebar -->
<section id="contact-form" class="ste-section" style="padding-top:40px;background:#f8f7ff;">
    <div class="ste-container" style="max-width:1000px;">
        <div class="ste-contact-main-grid">
            <!-- Left: Form -->
            <div class="ste-contact-form-card" data-ste-anim="slide-right" data-ste-anim-dur="0.6">
                <h2>Send Us a Message</h2>
                <p class="ste-contact-form-sub">Fill out the form and we'll get back to you as soon as possible.</p>

                <?php
                // If the page has content (e.g. a shortcode form), show that instead
                while ( have_posts() ) : the_post();
                    $page_content = get_the_content();
                endwhile;

                if ( trim( strip_tags( $page_content ) ) !== '' ) :
                    // Show page content (user placed a form shortcode here)
                ?>
                    <div class="ste-output"><?php echo apply_filters( 'the_content', $page_content ); ?></div>
                <?php else :
                    // Default built-in contact form
                ?>
                <form id="ste-contact-form" class="ste-cf" method="post" novalidate>
                    <div class="ste-cf-row">
                        <div class="ste-cf-group">
                            <label for="ste-cf-name">Full Name <span>*</span></label>
                            <input type="text" id="ste-cf-name" name="name" placeholder="Your name" required autocomplete="name">
                        </div>
                        <div class="ste-cf-group">
                            <label for="ste-cf-email">Email <span>*</span></label>
                            <input type="email" id="ste-cf-email" name="email" placeholder="you@example.com" required autocomplete="email">
                        </div>
                    </div>
                    <div class="ste-cf-group">
                        <label for="ste-cf-subject">Subject</label>
                        <input type="text" id="ste-cf-subject" name="subject" placeholder="How can we help?">
                    </div>
                    <div class="ste-cf-group">
                        <label for="ste-cf-message">Message <span>*</span></label>
                        <textarea id="ste-cf-message" name="message" rows="6" placeholder="Write your message here..." required></textarea>
                    </div>
                    <button type="submit" class="ste-btn ste-btn-primary" style="width:100%;justify-content:center;padding:14px 24px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        Send Message
                    </button>
                </form>
                <?php endif; ?>
            </div>

            <!-- Right: Sidebar -->
            <div class="ste-contact-sidebar">
                <!-- Quick Links -->
                <div class="ste-contact-sidebar-card" data-ste-anim="slide-left" data-ste-anim-dur="0.5">
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
                <div class="ste-contact-sidebar-card" data-ste-anim="slide-left" data-ste-anim-dur="0.6">
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
    </div>
</section>

<!-- Inline Animation Observer (same pattern as homepage) -->
<script>
(function(){
    if (window._steAnimInit) return;
    window._steAnimInit = true;
    var els = document.querySelectorAll('[data-ste-anim]');
    if (!els.length) return;
    if (!('IntersectionObserver' in window)) {
        els.forEach(function(el){ el.style.opacity = '1'; });
        return;
    }
    var io = new IntersectionObserver(function(entries){
        entries.forEach(function(e){
            if (e.isIntersecting) {
                var t = e.target, type = t.getAttribute('data-ste-anim'),
                    dur = parseFloat(t.getAttribute('data-ste-anim-dur')) || 0.6;
                t.style.animation = 'ste-' + type + ' ' + dur + 's ease both';
                t.addEventListener('animationend', function(){ t.style.opacity = '1'; }, {once:true});
                io.unobserve(t);
            }
        });
    }, {threshold: 0.15});
    els.forEach(function(el){ io.observe(el); });
})();
</script>

<style>
/* ═══ Contact Hero ═══ */
.ste-contact-hero {
    position: relative; padding: 150px 0 80px; overflow: hidden;
    background: linear-gradient(180deg, #f8f7ff 0%, #eef2ff 50%, #f0f9ff 100%);
}
.ste-contact-hero-bg { position: absolute; inset: 0; pointer-events: none; }
.ste-contact-orb { position: absolute; border-radius: 50%; filter: blur(80px); opacity: .35; }
.ste-contact-orb-1 { width: 450px; height: 450px; background: #6366f1; top: -150px; right: -80px; animation: float1 20s ease-in-out infinite; }
.ste-contact-orb-2 { width: 350px; height: 350px; background: #ec4899; bottom: -100px; left: -60px; animation: float2 25s ease-in-out infinite; }
.ste-contact-orb-3 { width: 250px; height: 250px; background: #06b6d4; top: 50%; left: 60%; animation: float3 18s ease-in-out infinite; }
@keyframes float1 { 0%,100% { transform: translate(0,0); } 50% { transform: translate(-30px,30px); } }
@keyframes float2 { 0%,100% { transform: translate(0,0); } 50% { transform: translate(25px,-25px); } }
@keyframes float3 { 0%,100% { transform: translate(0,0); } 50% { transform: translate(-15px,15px); } }
.ste-contact-hero-title {
    font-size: clamp(32px, 5vw, 52px); font-weight: 900; letter-spacing: -1px; line-height: 1.1;
    margin: 16px 0 16px;
    background: linear-gradient(135deg, #1e1b4b, #6366f1, #a855f7);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
}
.ste-contact-hero-sub { font-size: 18px; color: #666; line-height: 1.7; margin: 0 0 28px; }

/* ═══ Info Cards ═══ */
.ste-contact-info-grid {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;
}
.ste-contact-info-card {
    display: flex; align-items: flex-start; gap: 16px;
    background: #fff; border: 1px solid #f0f0f0; border-radius: 14px;
    padding: 24px 20px; transition: all .3s;
}
.ste-contact-info-card:hover {
    transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,.06);
    border-color: #c7d2fe;
}
.ste-ci-icon {
    width: 52px; height: 52px; flex-shrink: 0; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
}
.ste-ci-icon-email { background: #eef2ff; color: #6366f1; }
.ste-ci-icon-phone { background: #ecfdf5; color: #059669; }
.ste-ci-icon-location { background: #fff7ed; color: #ea580c; }
.ste-ci-icon-clock { background: #fdf2f8; color: #db2777; }
.ste-ci-text h3 { font-size: 14px; font-weight: 700; margin: 0 0 4px; color: #111; }
.ste-ci-text p { font-size: 14px; color: #444; margin: 0 0 4px; font-weight: 500; }
.ste-ci-text a { font-size: 12px; color: #6366f1; text-decoration: none; font-weight: 600; }
.ste-ci-text a:hover { text-decoration: underline; }
.ste-ci-text span { font-size: 11px; color: #999; }

/* ═══ Form + Sidebar Grid ═══ */
.ste-contact-main-grid {
    display: grid; grid-template-columns: 1.4fr 1fr; gap: 28px; align-items: start;
}
.ste-contact-form-card {
    background: #fff; border: 1px solid #e5e5e5; border-radius: 16px;
    padding: 40px 36px; box-shadow: 0 4px 24px rgba(0,0,0,.04);
}
.ste-contact-form-card h2 { font-size: 22px; font-weight: 800; margin: 0 0 6px; }
.ste-contact-form-sub { font-size: 14px; color: #888; margin: 0 0 28px; }

/* Built-in Form */
.ste-cf { display: flex; flex-direction: column; gap: 18px; }
.ste-cf-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.ste-cf-group { display: flex; flex-direction: column; gap: 5px; }
.ste-cf-group label { font-size: 13px; font-weight: 600; color: #333; }
.ste-cf-group label span { color: #ef4444; }
.ste-cf-group input,
.ste-cf-group textarea {
    padding: 11px 14px; font-size: 14px; font-family: 'Inter', sans-serif;
    border: 1.5px solid #e0e0e0; border-radius: 10px; background: #fafafa;
    transition: all .2s; color: #333; resize: vertical;
}
.ste-cf-group input:focus,
.ste-cf-group textarea:focus {
    outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.1);
    background: #fff;
}
.ste-cf-group input::placeholder,
.ste-cf-group textarea::placeholder { color: #bbb; }

/* Sidebar */
.ste-contact-sidebar { display: flex; flex-direction: column; gap: 20px; }
.ste-contact-sidebar-card {
    background: #fff; border: 1px solid #e5e5e5; border-radius: 14px;
    padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,.03);
}
.ste-contact-sidebar-card h3 {
    font-size: 15px; font-weight: 700; margin: 0 0 16px; color: #111;
    padding-bottom: 12px; border-bottom: 1px solid #f0f0f0;
}

/* Quick Links */
.ste-contact-links { list-style: none; padding: 0; margin: 0; }
.ste-contact-links li {
    display: flex; align-items: center; gap: 10px;
    padding: 8px 0; border-bottom: 1px solid #f5f5f5;
}
.ste-contact-links li:last-child { border-bottom: none; }
.ste-contact-links a {
    font-size: 14px; color: #444; text-decoration: none; font-weight: 500;
    transition: color .2s;
}
.ste-contact-links a:hover { color: #6366f1; }

/* FAQ */
.ste-contact-faq { display: flex; flex-direction: column; gap: 0; }
.ste-faq-item {
    border-bottom: 1px solid #f0f0f0; padding: 0;
}
.ste-faq-item:last-child { border-bottom: none; }
.ste-faq-item summary {
    padding: 12px 0; font-size: 13px; font-weight: 600; color: #333;
    cursor: pointer; list-style: none; display: flex; align-items: center;
    justify-content: space-between; transition: color .2s;
}
.ste-faq-item summary:hover { color: #6366f1; }
.ste-faq-item summary::-webkit-details-marker { display: none; }
.ste-faq-item summary::after {
    content: '+'; font-size: 18px; font-weight: 400; color: #999;
    transition: transform .2s;
}
.ste-faq-item[open] summary::after { content: '-'; }
.ste-faq-item p {
    font-size: 13px; color: #666; line-height: 1.6;
    padding: 0 0 12px; margin: 0;
}

/* Social Links */
.ste-contact-socials { display: flex; gap: 10px; }
.ste-social-link {
    width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;
    border-radius: 10px; background: #f3f4f6; color: #555; transition: all .2s;
}
.ste-social-link:hover { background: #6366f1; color: #fff; transform: translateY(-2px); }

/* ═══ Map Section ═══ */
.ste-contact-map-section { padding: 0 0 80px; }
.ste-contact-map-placeholder {
    background: linear-gradient(135deg, #f8f7ff, #eef2ff);
    border: 2px dashed #c7d2fe; border-radius: 16px;
    padding: 60px 40px; text-align: center;
}
.ste-contact-map-placeholder h3 { font-size: 18px; font-weight: 700; margin: 16px 0 8px; color: #111; }
.ste-contact-map-placeholder p { font-size: 14px; color: #888; margin: 0; }

/* ═══ Responsive ═══ */
@media (max-width: 900px) {
    .ste-contact-info-grid { grid-template-columns: repeat(2, 1fr); }
    .ste-contact-main-grid { grid-template-columns: 1fr; }
}
@media (max-width: 600px) {
    .ste-contact-info-grid { grid-template-columns: 1fr; }
    .ste-contact-hero { padding: 120px 0 60px; }
    .ste-contact-form-card { padding: 28px 20px; }
    .ste-cf-row { grid-template-columns: 1fr; }
}

/* ═══ Animation display overrides (same as theme.css pattern) ═══ */
h1[data-ste-anim], h2[data-ste-anim], h3[data-ste-anim], p[data-ste-anim],
div[data-ste-anim], section[data-ste-anim] { display: block !important; }
span.ste-section-badge[data-ste-anim] { display: inline-block !important; }
.ste-contact-info-card[data-ste-anim] { display: flex !important; }
.ste-contact-form-card[data-ste-anim] { display: block !important; }
.ste-contact-sidebar-card[data-ste-anim] { display: block !important; }
</style>

<?php get_footer(); ?>
