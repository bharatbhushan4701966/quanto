<?php
/**
 * @Packge     : Quanto
 * @Version    : 1.0
 * @Author     : Mirrortheme
 * @Author URI : https://mirrortheme.com/
 *
 */

// Block direct access
if ( ! defined('ABSPATH') ) {
    exit;
}

get_header();
$query = get_search_query();
?>

<style>
.cmr-search-page {
    text-align: center;
    padding: 80px 20px;
    background: #fff;
    min-height: 70vh;
}
.cmr-search-title {
    font-size: 48px;
    font-weight: 700;
    margin-bottom: 40px;
    color: #1a1a1a;
    font-family: inherit;
}
.cmr-search-form-wrapper {
    max-width: 700px;
    margin: 0 auto 60px auto;
    position: relative;
}
.cmr-search-form-wrapper form {
    display: flex;
    align-items: center;
    border: 1px solid #7c3aed; /* Purple border */
    border-radius: 50px;
    padding: 6px;
    background: #fff;
    box-shadow: 0 4px 20px rgba(124, 58, 237, 0.05);
}
.cmr-search-form-wrapper .cat-icon {
    padding-left: 20px;
    color: #475569;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    font-size: 15px;
    white-space: nowrap;
}
.cmr-search-form-wrapper .cat-icon i {
    color: #7c3aed;
    font-size: 18px;
}
.cmr-search-form-wrapper input {
    border: none;
    outline: none;
    flex-grow: 1;
    padding: 15px 20px;
    font-size: 16px;
    background: transparent;
    box-shadow: none;
    color: #333;
}
.cmr-search-form-wrapper input:focus {
    box-shadow: none;
    outline: none;
}
.cmr-search-form-wrapper button {
    background: #7c3aed;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 20px;
    transition: all 0.3s ease;
}
.cmr-search-form-wrapper button:hover {
    background: #6d28d9;
    transform: scale(1.05);
}
.cmr-search-empty-state {
    max-width: 600px;
    margin: 0 auto;
}
.cmr-search-illustration {
    margin-bottom: 30px;
    opacity: 0.8;
}
.cmr-search-empty-title {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 15px;
    color: #0f172a;
}
.cmr-search-empty-text {
    font-size: 16px;
    color: #64748b;
    margin-bottom: 50px;
    line-height: 1.6;
}
.cmr-search-empty-text strong {
    color: #1e293b;
}
.cmr-popular-topics-title {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #94a3b8;
    margin-bottom: 25px;
    font-weight: 600;
}
.cmr-popular-topics {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}
.cmr-topic-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border: 1px solid #e2e8f0;
    border-radius: 30px;
    color: #475569;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    background: #fff;
}
.cmr-topic-btn i {
    font-size: 18px;
    color: #94a3b8;
    transition: all 0.3s ease;
}
.cmr-topic-btn:hover {
    border-color: #7c3aed;
    color: #7c3aed;
    box-shadow: 0 4px 12px rgba(124, 58, 237, 0.1);
}
.cmr-topic-btn:hover i {
    color: #7c3aed;
}

/* Results Grid Styles */
.cmr-search-results-grid {
    text-align: left;
    margin-top: 20px;
}
</style>

<div class="cmr-search-page">
    <div class="container">
        
        <?php if ($query) : ?>
            <h1 class="cmr-search-title">&ldquo;<?php echo esc_html($query); ?>&rdquo;</h1>
        <?php endif; ?>
        
        <div class="cmr-search-form-wrapper">
            <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <div class="cat-icon">
                    <i class="ri-instance-line"></i> Consumer tech
                </div>
                <input type="search" name="s" value="<?php echo esc_attr($query); ?>" placeholder="Search...">
                <button type="submit"><i class="ri-search-line"></i></button>
            </form>
        </div>

        <?php if ( have_posts() ) : ?>
            <div class="cmr-search-results-grid">
                <div class="row gx-4 gy-5">
                    <?php while ( have_posts() ) : the_post(); ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="quanto-blog-box fade-anim" data-delay="0.30" data-direction="right">
                                <?php get_template_part('templates/content', get_post_format()); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                <div class="cmr-pagination mt-5 text-center">
                    <?php 
                    the_posts_pagination( array(
                        'prev_text' => '<i class="ri-arrow-left-s-line"></i>',
                        'next_text' => '<i class="ri-arrow-right-s-line"></i>',
                    ) ); 
                    ?>
                </div>
            </div>
        <?php else : ?>
            <div class="cmr-search-empty-state">
                <div class="cmr-search-illustration">
                    <!-- Clean minimal magnifying glass illustration -->
                    <svg width="220" height="160" viewBox="0 0 220 160" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Background abstract shapes (mountains/clouds) -->
                        <path d="M20 130 Q 50 80 80 120 T 150 100 T 200 130" fill="#f1f5f9" opacity="0.6"/>
                        <path d="M40 140 Q 90 90 120 130 T 210 120" fill="#e2e8f0" opacity="0.4"/>
                        <!-- Magnifying Glass -->
                        <circle cx="110" cy="65" r="35" stroke="#cbd5e1" stroke-width="12" fill="#f8fafc"/>
                        <circle cx="102" cy="57" r="15" fill="#e2e8f0" opacity="0.5"/>
                        <path d="M135 90 L165 120" stroke="#cbd5e1" stroke-width="14" stroke-linecap="round"/>
                        <!-- Small decorative elements -->
                        <rect x="50" y="110" width="8" height="12" rx="2" fill="#cbd5e1"/>
                        <rect x="150" y="140" width="16" height="20" rx="2" fill="#e2e8f0"/>
                        <ellipse cx="110" cy="145" rx="15" ry="4" fill="#cbd5e1" opacity="0.3"/>
                    </svg>
                </div>
                <h2 class="cmr-search-empty-title">No results found</h2>
                <p class="cmr-search-empty-text">
                    We couldn't find anything matching <strong>&ldquo;<?php echo esc_html($query); ?>&rdquo;</strong>.<br>
                    It might be a very specialized niche, or there's a typo in the query.
                </p>
                
                <div class="cmr-popular-topics-title">EXPLORE POPULAR TOPICS</div>
                <div class="cmr-popular-topics">
                    <a href="<?php echo home_url('/automotive'); ?>" class="cmr-topic-btn"><i class="ri-car-line"></i> Automotive</a>
                    <a href="<?php echo home_url('/consumer-tech'); ?>" class="cmr-topic-btn"><i class="ri-smartphone-line"></i> Consumer Tech</a>
                    <a href="<?php echo home_url('/it-telecom'); ?>" class="cmr-topic-btn"><i class="ri-base-station-line"></i> IT & Telecom</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>