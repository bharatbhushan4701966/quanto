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
        margin: 120px auto 0;
        padding: 0 24px 120px;
        font-family: 'Instrument Sans', sans-serif;
    }

    /* Category Header */
    .cmr-cat-header {
        padding-bottom: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .cmr-cat-breadcrumb {
        font-size: 13px;
        color: #999;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
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
        font-weight: 600;
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

    /* Search Bar */
    .cmr-cat-search-form {
        position: relative;
        max-width: 800px;
        margin: 24px auto 30px auto;
        width: 100%;
    }

    .cmr-cat-search-field {
        width: 100%;
        height: 60px;
        padding: 0 70px;
        border: 1px solid #6B3FA0;
        border-radius: 40px;
        font-size: 16px;
        color: #333;
        background: #fff;
        box-sizing: border-box;
        outline: none;
    }

    .cmr-cat-search-field::placeholder {
        color: #aaa;
    }

    .cmr-cat-search-icon-left {
        position: absolute;
        left: 25px;
        top: 50%;
        transform: translateY(-50%);
        color: #6B3FA0;
        display: flex;
    }

    .cmr-cat-search-submit {
        position: absolute;
        right: 8px;
        top: 8px;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #6B3FA0;
        border: none;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .cmr-cat-search-submit:hover {
        background: #502e7a;
    }


    /* 3-column grid */
    .cmr-cat-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 40px;
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
        .cmr-category-page-wrap {
            margin-top: 40px;
        }
    }

    /* Article Card - Intel style */
    .cmr-cat-card {
        background: #fff;
        border: none;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .cmr-cat-card-img {
        width: 100%;
        aspect-ratio: 16/9;
        overflow: hidden;
        background: #f4f4f4;
        margin-bottom: 18px;
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
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    /* Meta row: dash + category + read time */
    .cmr-cat-card-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
        font-size: 13px;
        color: #888;
        font-weight: 500;
    }

    .cmr-cat-card-meta-dash {
        width: 24px;
        height: 1px;
        background: #ccc;
        flex-shrink: 0;
    }

    .cmr-cat-card-tag {
        font-size: 13px;
        font-weight: 500;
        color: #555;
        letter-spacing: 0;
    }

    .cmr-cat-card-meta-sep {
        color: #ddd;
    }

    .cmr-cat-card-read-time {
        font-size: 13px;
        color: #888;
        margin-left: auto;
    }

    .cmr-cat-card-title {
        font-size: 18px;
        font-weight: 600;
        color: #111;
        line-height: 1.4;
        letter-spacing: -0.3px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        margin: 0px 0px 15px;
        overflow: hidden;
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
        color: #555;
        line-height: 1.65;
        margin: 0 0 20px 0;
        flex: 1;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .cmr-cat-read-more {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 14px;
        font-weight: 600;
        color: #111;
        text-decoration: none;
        border-bottom: 1.5px solid #111;
        padding-bottom: 2px;
        width: fit-content;
        transition: color 0.2s, border-color 0.2s, gap 0.2s;
        margin-top: auto;
    }

    .cmr-cat-read-more:hover {
        color: #6A35FF;
        border-color: #6A35FF;
        gap: 10px;
    }

    .cmr-cat-read-more svg {
        width: 13px;
        height: 13px;
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
        margin-bottom: 80px;
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
        border-radius: 50%;
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
        <span style="font-size: 13px; font-weight: 700; text-transform: uppercase; color: #888; letter-spacing: 1.5px; margin-bottom: 8px;">Category</span>
        <h1 class="cmr-cat-title">&quot;<?php echo esc_html( $cat_name ); ?>&quot;</h1>
        <form role="search" method="get" class="cmr-cat-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <div class="cmr-cat-search-icon-left">
                <img src="https://qai8358l95-staging.onrocket.site/wp-content/uploads/2026/06/cmrlogo-with-oly-c.svg" alt="CMR Logo" style="width: 24px; height: auto;">
            </div>
            <input type="search" class="cmr-cat-search-field" placeholder="Search..." value="<?php echo get_search_query(); ?>" name="s" />
            <?php if ( $current_cat ) : ?>
                <input type="hidden" name="cat" value="<?php echo esc_attr( $current_cat->term_id ); ?>" />
            <?php endif; ?>
            <button type="submit" class="cmr-cat-search-submit">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </button>
        </form>
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
                        <span class="cmr-cat-card-meta-dash"></span>
                        <span class="cmr-cat-card-tag"><?php echo esc_html( $tag_name ); ?></span>
                        <span class="cmr-cat-card-read-time"><?php echo esc_html( $read_time ); ?> min read</span>
                    </div>
                    <h3 class="cmr-cat-card-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    <p class="cmr-cat-card-excerpt"><?php echo esc_html( $excerpt ); ?></p>
                    <a href="<?php the_permalink(); ?>" class="cmr-cat-read-more">
                        More Details
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </a>
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
