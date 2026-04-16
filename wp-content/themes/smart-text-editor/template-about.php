<?php
/**
 * Template Name: About Page
 * Description: An about page with a hero banner, mission statement, team section placeholder, and content area.
 */
get_header();

while ( have_posts() ) : the_post();
    $title   = get_the_title();
    $content = get_the_content();
    $thumb   = get_the_post_thumbnail_url( get_the_ID(), 'full' );
    $excerpt = has_excerpt() ? get_the_excerpt() : '';
endwhile;
?>

<!-- About Hero -->
<section class="ste-about-hero">
    <div class="ste-container" style="max-width:860px;text-align:center;">
        <span class="ste-section-badge">About Us</span>
        <h1 style="font-size:clamp(28px,5vw,48px);font-weight:900;letter-spacing:-.5px;margin:16px 0 16px;"><?php echo esc_html( $title ); ?></h1>
        <?php if ( $excerpt ) : ?>
            <p style="font-size:18px;color:#666;line-height:1.7;max-width:640px;margin:0 auto;"><?php echo esc_html( $excerpt ); ?></p>
        <?php endif; ?>
    </div>
</section>

<?php if ( $thumb ) : ?>
<!-- Full-width Image -->
<section style="padding:0 0 60px;">
    <div class="ste-container" style="max-width:960px;">
        <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $title ); ?>" style="width:100%;height:auto;border-radius:16px;box-shadow:0 12px 40px rgba(0,0,0,.08);">
    </div>
</section>
<?php endif; ?>

<!-- Content -->
<section style="padding:0 0 80px;">
    <div class="ste-container" style="max-width:800px;">
        <div class="ste-output" style="font-size:17px;line-height:1.9;">
            <?php echo apply_filters( 'the_content', $content ); ?>
        </div>
    </div>
</section>

<!-- Stats Bar -->
<section style="padding:60px 0;background:#f8f7ff;">
    <div class="ste-container">
        <div class="ste-about-stats">
            <div class="ste-about-stat">
                <span class="ste-about-stat-num">50+</span>
                <span class="ste-about-stat-label">Google Fonts</span>
            </div>
            <div class="ste-about-stat">
                <span class="ste-about-stat-num">36</span>
                <span class="ste-about-stat-label">Style Presets</span>
            </div>
            <div class="ste-about-stat">
                <span class="ste-about-stat-num">11</span>
                <span class="ste-about-stat-label">Animations</span>
            </div>
            <div class="ste-about-stat">
                <span class="ste-about-stat-num">100%</span>
                <span class="ste-about-stat-label">Custom Built</span>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="ste-cta">
    <div class="ste-container" style="text-align:center;">
        <h2>Want to Try It Yourself?</h2>
        <p style="font-size:18px;opacity:.85;margin:16px 0 32px;">Experience the power of Smart Text Editor firsthand.</p>
        <a href="<?php echo esc_url( home_url( '/#editor' ) ); ?>" class="ste-btn ste-btn-primary" style="padding:16px 40px;font-size:16px;">Open Editor</a>
    </div>
</section>

<?php get_footer(); ?>
