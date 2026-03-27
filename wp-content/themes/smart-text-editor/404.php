<?php get_header(); ?>
<main style="padding:160px 0 100px;text-align:center;min-height:60vh;">
    <div class="ste-container">
        <h1 style="font-size:72px;font-weight:900;background:linear-gradient(135deg,#6366f1,#a855f7);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">404</h1>
        <p style="font-size:20px;color:#666;margin:16px 0 32px;">Page not found. The page you're looking for doesn't exist.</p>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ste-btn ste-btn-primary">Back to Home</a>
    </div>
</main>
<?php get_footer(); ?>
