<?php
/**
 * Template Name: Blog / Posts
 * Description: A blog listing page showing recent posts in a clean card grid layout.
 */
get_header(); ?>

<main class="ste-page-content" style="padding-top:100px;padding-bottom:80px;min-height:60vh;">
    <div class="ste-container" style="max-width:1040px;">

        <!-- Page Header -->
        <div style="text-align:center;margin-bottom:48px;">
            <span class="ste-section-badge">Blog</span>
            <h1 style="font-size:clamp(28px,5vw,44px);font-weight:800;letter-spacing:-.5px;margin:16px 0 12px;"><?php the_title(); ?></h1>
            <?php if ( has_excerpt() ) : ?>
                <p style="font-size:17px;color:#777;max-width:560px;margin:0 auto;"><?php echo esc_html( get_the_excerpt() ); ?></p>
            <?php endif; ?>
        </div>

        <?php
        $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
        $blog_query = new WP_Query( array(
            'post_type'      => 'post',
            'posts_per_page' => 9,
            'paged'          => $paged,
            'post_status'    => 'publish',
        ) );
        ?>

        <?php if ( $blog_query->have_posts() ) : ?>
            <div class="ste-blog-grid">
                <?php while ( $blog_query->have_posts() ) : $blog_query->the_post(); ?>
                    <article class="ste-blog-card">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <a href="<?php the_permalink(); ?>" class="ste-blog-card-img">
                                <?php the_post_thumbnail( 'medium_large', array( 'style' => 'width:100%;height:100%;object-fit:cover;display:block;' ) ); ?>
                            </a>
                        <?php else : ?>
                            <a href="<?php the_permalink(); ?>" class="ste-blog-card-img ste-blog-card-noimg">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                            </a>
                        <?php endif; ?>

                        <div class="ste-blog-card-body">
                            <div class="ste-blog-card-meta">
                                <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
                                <?php
                                $cats = get_the_category();
                                if ( ! empty( $cats ) ) :
                                ?>
                                    <span class="ste-blog-card-cat"><?php echo esc_html( $cats[0]->name ); ?></span>
                                <?php endif; ?>
                            </div>
                            <h2 class="ste-blog-card-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            <p class="ste-blog-card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
                            <a href="<?php the_permalink(); ?>" class="ste-blog-card-link">Read More &rarr;</a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <?php if ( $blog_query->max_num_pages > 1 ) : ?>
                <div class="ste-blog-pagination">
                    <?php
                    echo paginate_links( array(
                        'total'     => $blog_query->max_num_pages,
                        'current'   => $paged,
                        'prev_text' => '&laquo; Prev',
                        'next_text' => 'Next &raquo;',
                    ) );
                    ?>
                </div>
            <?php endif; ?>

            <?php wp_reset_postdata(); ?>

        <?php else : ?>
            <div style="text-align:center;padding:60px 0;">
                <p style="font-size:18px;color:#999;">No posts found yet. Start writing!</p>
                <?php if ( current_user_can( 'edit_posts' ) ) : ?>
                    <a href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>" class="ste-btn ste-btn-primary" style="margin-top:16px;">Create Your First Post</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</main>



<?php get_footer(); ?>
