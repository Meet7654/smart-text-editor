<?php
/**
 * Template Name: Landing Page
 * Description: A marketing landing page with hero section, features grid, CTA, and testimonials pulled from page content.
 */
get_header();

while ( have_posts() ) : the_post();
    $title   = get_the_title();
    $content = get_the_content();
    $thumb   = get_the_post_thumbnail_url( get_the_ID(), 'full' );
    $excerpt = has_excerpt() ? get_the_excerpt() : '';
endwhile;
?>

<!-- Hero Section -->
<section class="ste-landing-hero">
    <div class="ste-container">
        <div class="ste-landing-hero-inner">
            <div class="ste-landing-hero-text">
                <h1 class="ste-landing-title"><?php echo esc_html( $title ); ?></h1>
                <?php if ( $excerpt ) : ?>
                    <p class="ste-landing-subtitle"><?php echo esc_html( $excerpt ); ?></p>
                <?php endif; ?>
                <div class="ste-landing-actions">
                    <a href="#content" class="ste-btn ste-btn-primary" style="padding:14px 32px;font-size:16px;">Learn More</a>
                    <a href="<?php echo esc_url( home_url( '/#pricing' ) ); ?>" class="ste-btn ste-btn-outline" style="padding:14px 32px;font-size:16px;">View Pricing</a>
                </div>
            </div>
            <?php if ( $thumb ) : ?>
                <div class="ste-landing-hero-img">
                    <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $title ); ?>">
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Content Section -->
<section id="content" class="ste-section">
    <div class="ste-container" style="max-width:860px;">
        <div class="ste-output" style="font-size:17px;line-height:1.9;">
            <?php echo apply_filters( 'the_content', $content ); ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="ste-cta">
    <div class="ste-container">
        <div class="ste-cta-content">
            <h2>Ready to Get Started?</h2>
            <p style="font-size:18px;opacity:.85;margin:16px 0 32px;">Take the next step and explore everything we have to offer.</p>
            <a href="<?php echo esc_url( home_url( '/#editor' ) ); ?>" class="ste-btn ste-btn-primary" style="padding:16px 40px;font-size:16px;">Get Started Now</a>
        </div>
    </div>
</section>

<style>
.ste-landing-hero {
    padding: 140px 0 80px;
    background: linear-gradient(135deg, #f8f7ff 0%, #eef2ff 50%, #f0f9ff 100%);
}
.ste-landing-hero-inner {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
}
.ste-landing-title {
    font-size: clamp(32px, 5vw, 52px);
    font-weight: 900;
    letter-spacing: -1px;
    line-height: 1.1;
    margin-bottom: 20px;
    background: linear-gradient(135deg, #1a1a2e, #6366f1);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.ste-landing-subtitle {
    font-size: 18px;
    color: #666;
    line-height: 1.7;
    margin-bottom: 32px;
}
.ste-landing-actions { display: flex; gap: 14px; flex-wrap: wrap; }
.ste-landing-hero-img img {
    width: 100%;
    height: auto;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(99,102,241,.15);
}
@media (max-width: 768px) {
    .ste-landing-hero-inner { grid-template-columns: 1fr; text-align: center; }
    .ste-landing-actions { justify-content: center; }
    .ste-landing-hero { padding: 120px 0 60px; }
}
</style>

<?php get_footer(); ?>
