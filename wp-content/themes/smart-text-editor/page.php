<?php get_header(); ?>
<main class="ste-page-content" style="padding-top:100px;min-height:60vh;">
    <div class="ste-container">
        <?php while ( have_posts() ) : the_post(); ?>
            <article>
                <h1 style="font-size:36px;font-weight:800;margin-bottom:20px;"><?php the_title(); ?></h1>
                <div class="ste-output"><?php the_content(); ?></div>
            </article>
        <?php endwhile; ?>
    </div>
</main>
<?php get_footer(); ?>
