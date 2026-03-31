<?php
/**
 * Template Name: Blank Canvas
 * Description: A completely blank page — no header, no footer. Only your content with wp_head/wp_footer for scripts.
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
    <style>
        body { margin: 0; padding: 0; font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; color: #333; }
        .ste-blank-content { max-width: 1200px; margin: 0 auto; padding: 40px 24px; }
    </style>
</head>
<body <?php body_class( 'ste-blank-page' ); ?>>
<?php wp_body_open(); ?>

<div class="ste-blank-content">
    <?php while ( have_posts() ) : the_post(); ?>
        <div class="ste-output"><?php the_content(); ?></div>
    <?php endwhile; ?>
</div>

<?php wp_footer(); ?>
</body>
</html>
