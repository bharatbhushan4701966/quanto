<?php
/**
 * @Packge     : Quanto
 * @Version    : 1.0
 * @Author     : Mirrortheme
 * @Author URI : https://mirrortheme.com/
 *
 */
 
    // Block direct access
    if( !defined( 'ABSPATH' ) ){
        exit();
    }

    if( !empty( quanto_pagination() ) ) :
?>
<!-- Post Pagination -->
<div class="row row-padding-top">
    <div class="col-12">
        <div class="blog-pagination">
            <nav aria-label="Page navigation example">

                <?php 
                    add_filter('next_posts_link_attributes', 'posts_link_attributes');
                    add_filter('previous_posts_link_attributes', 'posts_link_attributes');
                    function posts_link_attributes(){
                        return 'class="pagi-btn"';
                    };
                ?>

                <ul class="pagination align-items-center custom-ul">
                    <!-- Prev -->
                    <?php $prev 	= '<i class="fa-solid fa-arrow-left"></i>Prev';?>
                    <?php if ( $prev_link = get_previous_posts_link( $prev ) ) : ?>
                        <li class="page-item"><?php echo str_replace('<a', '<a class="page-link prev"', $prev_link); ?></li>
                    <?php endif; ?>

                    <?php 
                        echo quanto_pagination();
                    ?>

                    <!-- Next -->
                    <?php $next 	= 'Next<i class="fa-solid fa-arrow-right"></i>';?>
                    <?php if ( $next_link = get_next_posts_link( $next ) ) : ?>
                        <li class="page-item"><?php echo str_replace('<a', '<a class="page-link next"', $next_link); ?></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>
<!-- End of Post Pagination -->
<?php 
    endif;