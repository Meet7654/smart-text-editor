<?php
/**
 * Template Name: Contact Page
 * Description: A contact page with info cards (email, phone, address) and a contact form area for your content.
 */
get_header(); ?>

<main class="ste-page-content" style="padding-top:100px;padding-bottom:80px;min-height:60vh;">
    <div class="ste-container" style="max-width:960px;">

        <!-- Page Header -->
        <div style="text-align:center;margin-bottom:48px;">
            <span class="ste-section-badge">Contact</span>
            <h1 style="font-size:clamp(28px,5vw,44px);font-weight:800;letter-spacing:-.5px;margin:16px 0 12px;"><?php the_title(); ?></h1>
            <?php if ( has_excerpt() ) : ?>
                <p style="font-size:17px;color:#777;max-width:560px;margin:0 auto;"><?php echo esc_html( get_the_excerpt() ); ?></p>
            <?php else : ?>
                <p style="font-size:17px;color:#777;max-width:560px;margin:0 auto;">We'd love to hear from you. Reach out using any of the methods below.</p>
            <?php endif; ?>
        </div>

        <!-- Contact Cards -->
        <div class="ste-contact-cards">
            <div class="ste-contact-card">
                <div class="ste-contact-card-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                </div>
                <h3>Email Us</h3>
                <p><?php echo esc_html( get_option( 'admin_email' ) ); ?></p>
            </div>
            <div class="ste-contact-card">
                <div class="ste-contact-card-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                </div>
                <h3>Call Us</h3>
                <p>+91 98765 43210</p>
            </div>
            <div class="ste-contact-card">
                <div class="ste-contact-card-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </div>
                <h3>Visit Us</h3>
                <p>Gujarat, India</p>
            </div>
        </div>

        <!-- Page Content (Contact Form goes here via editor/shortcode) -->
        <div class="ste-contact-form-area">
            <?php while ( have_posts() ) : the_post(); ?>
                <div class="ste-output"><?php the_content(); ?></div>
            <?php endwhile; ?>
        </div>

    </div>
</main>

<style>
.ste-contact-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 48px;
}
.ste-contact-card {
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 12px;
    padding: 28px 24px;
    text-align: center;
    transition: all .2s;
}
.ste-contact-card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,.06);
    border-color: #c7d2fe;
}
.ste-contact-card-icon {
    width: 52px;
    height: 52px;
    margin: 0 auto 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #eef2ff;
    border-radius: 12px;
}
.ste-contact-card h3 {
    font-size: 16px;
    font-weight: 700;
    margin: 0 0 6px;
}
.ste-contact-card p {
    font-size: 14px;
    color: #666;
    margin: 0;
}
.ste-contact-form-area {
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 12px;
    padding: 40px;
}
@media (max-width: 768px) {
    .ste-contact-cards { grid-template-columns: 1fr; }
    .ste-contact-form-area { padding: 24px; }
}
</style>

<?php get_footer(); ?>
