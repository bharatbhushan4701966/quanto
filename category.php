<?php
/**
 * Category Template - Full Width Elementor Style, 3-Column Grid
 * Quanto Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<style>
    /* Force full width - hide sidebar, remove constraints */
    .cmr-category-page-wrap {
        max-width: 1280px;
        margin: 0 auto;
        padding: 60px 24px 80px;
        font-family: 'Instrument Sans', sans-serif;
    }

    /* Category Header */
    .cmr-cat-header {
        margin-bottom: 50px;
        padding-bottom: 30px;
        border-bottom: 1px solid #eaeaea;
    }

    .cmr-cat-breadcrumb {
        font-size: 13px;
        color: #999;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .cmr-cat-breadcrumb a {
        color: #999;
        text-decoration: none;
        transition: color 0.2s;
    }

    .cmr-cat-breadcrumb a:hover {
        color: #6A35FF;
    }

    .cmr-cat-title {
        font-size: 48px;
        font-weight: 700;
        color: #111;
        margin: 0 0 14px 0;
        letter-spacing: -1px;
        line-height: 1.1;
    }

    .cmr-cat-description {
        font-size: 16px;
        color: #555;
        margin: 0;
        max-width: 700px;
        line-height: 1.6;
    }

    .cmr-cat-count {
        display: inline-block;
        background: #f4f0ff;
        color: #6A35FF;
        font-size: 13px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 40px;
        margin-bottom: 16px;
    }

    /* 3-column grid */
    .cmr-cat-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 32px;
    }

    @media (max-width: 1024px) {
        .cmr-cat-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .cmr-cat-grid {
            grid-template-columns: 1fr;
        }
        .cmr-cat-title {
            font-size: 32px;
        }
    }

    /* Article Card */
    .cmr-cat-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #f0f0f0;
        overflow: hidden;
        transition: box-shadow 0.3s ease, transform 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .cmr-cat-card:hover {
        box-shadow: 0 12px 40px rgba(0,0,0,0.10);
        transform: translateY(-4px);
    }

    .cmr-cat-card-img {
        aspect-ratio: 16/9;
        overflow: hidden;
        background: #f4f4f4;
    }

    .cmr-cat-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
        display: block;
    }

    .cmr-cat-card:hover .cmr-cat-card-img img {
        transform: scale(1.04);
    }

    .cmr-cat-card-body {
        padding: 24px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .cmr-cat-card-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }

    .cmr-cat-card-tag {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #6A35FF;
        background: #f4f0ff;
        padding: 3px 10px;
        border-radius: 40px;
    }

    .cmr-cat-card-date {
        font-size: 12px;
        color: #aaa;
    }

    .cmr-cat-card-title {
        font-size: 18px;
        font-weight: 600;
        color: #111;
        line-height: 1.4;
        margin: 0 0 12px 0;
        letter-spacing: -0.3px;
    }

    .cmr-cat-card-title a {
        text-decoration: none;
        color: inherit;
        transition: color 0.2s;
    }

    .cmr-cat-card-title a:hover {
        color: #6A35FF;
    }

    .cmr-cat-card-excerpt {
        font-size: 14px;
        color: #666;
        line-height: 1.6;
        margin: 0 0 20px 0;
        flex: 1;
    }

    .cmr-cat-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: auto;
        padding-top: 16px;
        border-top: 1px solid #f0f0f0;
    }

    .cmr-cat-read-time {
        font-size: 12px;
        color: #aaa;
    }

    .cmr-cat-read-more {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #6A35FF;
        text-decoration: none;
        transition: gap 0.2s;
    }

    .cmr-cat-read-more:hover {
        gap: 10px;
    }

    .cmr-cat-read-more svg {
        width: 14px;
        height: 14px;
    }

    /* No posts */
    .cmr-cat-empty {
        text-align: center;
        padding: 80px 20px;
        color: #555;
    }

    .cmr-cat-empty h2 {
        font-size: 24px;
        color: #111;
        margin-bottom: 12px;
    }

    /* Pagination */
    .cmr-cat-pagination {
        margin-top: 60px;
        display: flex;
        justify-content: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .cmr-cat-pagination .page-numbers {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        border: 1px solid #eaeaea;
        font-size: 14px;
        font-weight: 500;
        color: #555;
        text-decoration: none;
        transition: all 0.2s;
    }

    .cmr-cat-pagination .page-numbers:hover,
    .cmr-cat-pagination .page-numbers.current {
        background: #6A35FF;
        border-color: #6A35FF;
        color: #fff;
    }

    .cmr-cat-pagination .page-numbers.dots {
        border: none;
        background: transparent;
    }

    /* Override theme sidebar/container constraints on category pages */
    body.category .site-content,
    body.category .content-area,
    body.category #primary,
    body.category .container {
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
    }

    body.category #secondary,
    body.category .widget-area,
    body.category aside {
        display: none !important;
    }
</style>

<div class="cmr-category-page-wrap">

    <?php
    $current_cat = get_queried_object();
    $cat_name    = $current_cat ? $current_cat->name : '';
    $cat_desc    = $current_cat ? $current_cat->description : '';
    $cat_count   = $current_cat ? $current_cat->count : 0;
    ?>

    <!-- Category Header -->
    <div class="cmr-cat-header">
        <div class="cmr-cat-breadcrumb">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
            <svg width="6" height="10" viewBox="0 0 6 10" fill="none"><path d="M1 1L5 5L1 9" stroke="#ccc" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span><?php echo esc_html( $cat_name ); ?></span>
        </div>
        <span class="cmr-cat-count"><?php echo esc_html( $cat_count ); ?> Articles</span>
        <h1 class="cmr-cat-title"><?php echo esc_html( $cat_name ); ?></h1>
        <?php if ( $cat_desc ) : ?>
            <p class="cmr-cat-description"><?php echo esc_html( $cat_desc ); ?></p>
        <?php endif; ?>
    </div>

    <!-- Article Grid -->
    <?php if ( have_posts() ) : ?>
        <div class="cmr-cat-grid">
            <?php while ( have_posts() ) : the_post();
                $post_id       = get_the_ID();
                $thumbnail_url = get_the_post_thumbnail_url( $post_id, 'large' );
                $placeholder   = 'https://via.placeholder.com/800x450?text=' . rawurlencode( get_the_title() );
                $img           = $thumbnail_url ?: $placeholder;
                $excerpt       = wp_trim_words( get_the_excerpt() ?: get_the_content(), 18, '...' );
                $word_count    = str_word_count( strip_tags( get_the_content() ) );
                $read_time     = max( 1, ceil( $word_count / 200 ) );
                $terms         = get_the_terms( $post_id, 'category' );
                $tag_name      = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : $cat_name;
            ?>
            <article class="cmr-cat-card">
                <div class="cmr-cat-card-img">
                    <a href="<?php the_permalink(); ?>">
                        <img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy" />
                    </a>
                </div>
                <div class="cmr-cat-card-body">
                    <div class="cmr-cat-card-meta">
                        <span class="cmr-cat-card-tag"><?php echo esc_html( $tag_name ); ?></span>
                        <span class="cmr-cat-card-date"><?php echo get_the_date( 'M j, Y' ); ?></span>
                    </div>
                    <h2 class="cmr-cat-card-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>
                    <p class="cmr-cat-card-excerpt"><?php echo esc_html( $excerpt ); ?></p>
                    <div class="cmr-cat-card-footer">
                        <span class="cmr-cat-read-time"><?php echo esc_html( $read_time ); ?> min read</span>
                        <a href="<?php the_permalink(); ?>" class="cmr-cat-read-more">
                            Read more
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="7" y1="17" x2="17" y2="7"></line>
                                <polyline points="7 7 17 7 17 17"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>
            </article>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <div class="cmr-cat-pagination">
            <?php
            echo paginate_links( array(
                'prev_text' => '<svg width="8" height="14" viewBox="0 0 8 14" fill="none"><path d="M7 1L1 7L7 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
                'next_text' => '<svg width="8" height="14" viewBox="0 0 8 14" fill="none"><path d="M1 1L7 7L1 13" stroke="#6A35FF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            ) );
            ?>
        </div>

        <?php wp_reset_postdata(); ?>

    <?php else : ?>
        <div class="cmr-cat-empty">
            <h2>No articles found</h2>
            <p>There are currently no articles in this category. Please check back later.</p>
        </div>
    <?php endif; ?>

</div>

<?php get_footer(); ?>
