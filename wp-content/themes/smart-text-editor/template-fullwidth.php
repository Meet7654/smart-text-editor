<?php
/**
 * Template Name: Full Width (No Sidebar)
 * Description: A full-width page layout with no sidebar. Content stretches across the full container.
 */
get_header(); ?>

<main class="ste-page-content" style="padding-top:100px;padding-bottom:80px;min-height:60vh;">
    <div class="ste-container" style="max-width:960px;">
        <?php while ( have_posts() ) : the_post(); ?>
            <article class="ste-fullwidth-article">
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="ste-page-hero-img" style="margin-bottom:36px;border-radius:12px;overflow:hidden;">
                        <?php the_post_thumbnail( 'full', array( 'style' => 'width:100%;height:auto;display:block;' ) ); ?>
                    </div>
                <?php endif; ?>

                <h1 style="font-size:clamp(28px,5vw,44px);font-weight:800;margin-bottom:20px;letter-spacing:-.5px;"><?php the_title(); ?></h1>
                <div class="ste-output" style="font-size:17px;line-height:1.8;"><?php the_content(); ?></div>
            </article>
        <?php endwhile; ?>
    </div>
</main>

<?php get_footer(); ?>
