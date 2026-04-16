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
