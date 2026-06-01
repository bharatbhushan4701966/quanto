<?php
/**
 * @Packge     : Quanto
 * @Version    : 1.0
 * @Author     : Mirrortheme
 * @Author URI : https://mirrortheme.com/
 *
 */

    // Block direct access
    if( ! defined( 'ABSPATH' ) ){
        exit();
    }

    if ( post_password_required() ) {
        return;
    }


    if ( get_post_type() === 'cmr_news' ) {
        // Compute ratings
        $all_comments = get_comments( array( 'post_id' => get_the_ID(), 'status' => 'approve', 'type' => 'comment' ) );
        $rating_counts = array( 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0 );
        $total_rating = 0;
        $rating_count = 0;
        foreach ( $all_comments as $c ) {
            $r = get_comment_meta( $c->comment_ID, 'rating', true );
            if ( $r && $r >= 1 && $r <= 5 ) {
                $rating_counts[$r]++;
                $total_rating += $r;
                $rating_count++;
            }
        }
        $avg_rating = $rating_count > 0 ? number_format( $total_rating / $rating_count, 1 ) : '0.0';
        
        ?>
        <div class="cmr-ratings-summary mt-5 mb-5">
            <h2 style="font-size: 32px; font-weight: 700; margin-bottom: 30px;">Rating</h2>
            <div class="d-flex align-items-center">
                <div class="rating-left me-5" style="min-width: 120px;">
                    <div style="font-size: 64px; font-weight: 700; line-height: 1;"><?php echo esc_html($avg_rating); ?></div>
                    <div class="rating-stars text-warning my-2" style="font-size: 16px;">
                        <?php
                        $full_stars = floor($avg_rating);
                        $half_star = ($avg_rating - $full_stars) >= 0.5 ? 1 : 0;
                        $empty_stars = 5 - $full_stars - $half_star;
                        echo str_repeat('<i class="fa-solid fa-star"></i>', $full_stars);
                        if ($half_star) echo '<i class="fa-solid fa-star-half-stroke"></i>';
                        echo str_repeat('<i class="fa-regular fa-star"></i>', $empty_stars);
                        ?>
                    </div>
                    <div class="text-muted" style="font-size: 14px;"><?php echo esc_html($rating_count); ?> Reviews</div>
                </div>
                <div class="rating-right flex-grow-1" style="max-width: 300px;">
                    <?php 
                    $colors = array( 5 => '#22c55e', 4 => '#22c55e', 3 => '#facc15', 2 => '#fbbf24', 1 => '#ea580c' );
                    for ($i = 5; $i >= 1; $i--): 
                        $pct = $rating_count > 0 ? ($rating_counts[$i] / $rating_count) * 100 : 0;
                    ?>
                    <div class="d-flex align-items-center mb-2">
                        <span class="me-3 fw-bold" style="width: 15px; font-size: 14px;"><?php echo $i; ?></span>
                        <div class="progress flex-grow-1" style="height: 8px; border-radius: 4px; background-color: #f8f9fa;">
                            <div class="progress-bar" role="progressbar" style="width: <?php echo esc_attr($pct); ?>%; border-radius: 4px; background-color: <?php echo esc_attr($colors[$i]); ?>;" aria-valuenow="<?php echo esc_attr($pct); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
        <?php
    }

    if( have_comments() ) :
?>
<!-- Comments -->
<div class="blog-comments row-margin-top">
    <h4>
        <?php printf( _nx( '1 Comment ', ' %1$s Comments', get_comments_number(), 'comments title', 'quanto' ), number_format_i18n( get_comments_number() ) ); ?>
    </h4>
    <ul class="custom-ul">
        <?php
            the_comments_navigation();
                wp_list_comments( array(
                    'style'       => 'ul',
                    'short_ping'  => true,
                    'avatar_size' => 100,
                    'callback'    => 'quanto_comment_callback'
                ) );
            the_comments_navigation();
        ?>
    </ul>
</div>

<!-- End of Comments -->
<?php
    endif;
?>

<?php
    $commenter = wp_get_current_commenter();
	$req = get_option( 'require_name_email' );
    $aria_req = ( $req ? "required" : '' );

    $consent = empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"';
    
	$fields =  array(
	  'author'  => '<div class="row g-3"><div class="col-md-6"><div class="mb-2"><input class="form-control" type="text" name="author" placeholder="'. esc_attr__( 'Your Name *', 'quanto' ) .'" value="'. esc_attr( $commenter['comment_author'] ).'" '.esc_attr( $aria_req ).'></div></div>',
	  'email'   => '<div class="col-md-6"><div class="mb-2"><input class="form-control" type="email" name="email"  value="' . esc_attr(  $commenter['comment_author_email'] ) .'" placeholder="'. esc_attr__( 'Enter your e-mail address', 'quanto' ) .'" '.esc_attr( $aria_req ).'></div></div></div>',
      'url'     => '',
      'cookies' => '<div class="row g-3"><div class="col-12"><div class="quanto-check notice"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . esc_attr( $consent ) . ' />' . '<label for="wp-comment-cookies-consent">'  . esc_html__( ' Save my name, email, and website in this browser for the next time I comment.','quanto' ) .  '<span class="checkmark"></span> </label> </div></div></div>'
    );

    $comment_field = '<div class="row g-3"><div class="col-12"><div class="mb-2"><textarea class="form-control" name="comment" placeholder="'. esc_attr__( 'Write your comment...', 'quanto' ) .'" '.esc_attr( $aria_req ).'></textarea></div></div></div>';
    $title_reply = esc_html__( 'Leave a reply', 'quanto' );

    if ( ( get_post_type() === 'product' && class_exists( 'WooCommerce' ) && wc_review_ratings_enabled() ) || get_post_type() === 'cmr_news' ) {
        $comment_field = '<div class="comment-form-rating mb-3">
            <label for="rating" class="form-label">' . esc_html__( 'Your rating', 'quanto' ) . ( wc_review_ratings_required() ? ' <span class="required">*</span>' : '' ) . '</label>
            <select name="rating" id="rating" required class="form-select">
                <option value="">' . esc_html__( 'Rate&hellip;', 'quanto' ) . '</option>
                <option value="5">' . esc_html__( 'Perfect', 'quanto' ) . '</option>
                <option value="4">' . esc_html__( 'Good', 'quanto' ) . '</option>
                <option value="3">' . esc_html__( 'Average', 'quanto' ) . '</option>
                <option value="2">' . esc_html__( 'Not that bad', 'quanto' ) . '</option>
                <option value="1">' . esc_html__( 'Very poor', 'quanto' ) . '</option>
            </select>
        </div>' . $comment_field;
        $title_reply = esc_html__( 'Leave a review', 'quanto' );
    }

	$args = array(
        'fields'                => $fields,
    	'comment_field'         => $comment_field,
        'class_form'            => 'quanto-cform',
    	'title_reply'           => $title_reply,
    	'title_reply_before'    => '<h4>',
        'title_reply_after'     => '</h4>',
        'comment_notes_before'  => '<p class="comment-notes">'.esc_html__('Your email address will not be published. Required fields are marked *','quanto').'</p>',
        'logged_in_as'          => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>','quanto' ), admin_url( 'profile.php' ), esc_attr( $user_identity ), wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ) . '</p>',
        'class_submit'          => 'quanto-link-btn btn-pill mt-2',
        'submit_field'          => '<div class="row g-3"><div class="col-12 mt-4">%1$s %2$s</div></div>',
    	'submit_button'         => '<button type="submit" name="%1$s" id="%2$s" class="%3$s">
                                        '.esc_html__('Submit Now','quanto').'
                                            <span>
                                                <i class="fa-solid fa-arrow-right arry1"></i>
                                                <i class="fa-solid fa-arrow-right arry2"></i>
                                            </span>
                                    </button>',
    	
	);

    if ( comments_open() ) {
        echo '<!-- Comment Form -->';
        echo '<div class="blog-contact-form row-margin-top">';
            comment_form( $args );
        echo '</div>';
        echo '<!-- End of Comment Form -->';
    }
