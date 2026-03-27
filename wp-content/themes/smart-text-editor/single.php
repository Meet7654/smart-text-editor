<?php get_header(); ?>
<main class="ste-page-content" style="padding-top:100px;min-height:60vh;">
    <div class="ste-container" style="max-width:800px;">
        <?php while ( have_posts() ) : the_post(); ?>
            <article>
                <h1 style="font-size:36px;font-weight:800;margin-bottom:8px;"><?php the_title(); ?></h1>
                <p style="font-size:14px;color:#888;margin-bottom:32px;">Published on <?php the_date(); ?></p>
                <div class="ste-output" style="font-size:17px;line-height:1.8;"><?php the_content(); ?></div>
            </article>
        <?php endwhile; ?>
    </div>
</main>
<?php get_footer(); ?>
