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
        <div class="cmr-ratings-summary mt-5 mb-5" style="background: transparent; box-shadow: none; padding: 0;">
            <h2 style="font-size: 28px; font-weight: 700; margin-bottom: 25px; color: #000; font-family: 'Instrument Sans', sans-serif;">Rating</h2>
            <div class="d-flex align-items-start">
                <div class="rating-left me-5" style="min-width: 100px;">
                    <div style="font-size: 48px; font-weight: 700; line-height: 1; color: #222; font-family: 'Instrument Sans', sans-serif;"><?php echo esc_html($avg_rating); ?></div>
                    <div class="rating-stars my-2" style="font-size: 14px; color: #fbbf24;">
                        <?php
                        $full_stars = floor($avg_rating);
                        $half_star = ($avg_rating - $full_stars) >= 0.5 ? 1 : 0;
                        $empty_stars = 5 - $full_stars - $half_star;
                        echo str_repeat('<i class="fa-solid fa-star"></i>', $full_stars);
                        if ($half_star) echo '<i class="fa-solid fa-star-half-stroke"></i>';
                        echo str_repeat('<i class="fa-regular fa-star" style="color: #e5e7eb;"></i>', $empty_stars);
                        ?>
                    </div>
                    <div style="font-size: 13px; color: #6b7280; font-family: 'Instrument Sans', sans-serif;"><?php echo esc_html($rating_count); ?> Reviews</div>
                </div>
                <div class="rating-right flex-grow-1" style="max-width: 250px; padding-top: 5px;">
                    <?php 
                    $colors = array( 5 => '#22c55e', 4 => '#22c55e', 3 => '#facc15', 2 => '#fbbf24', 1 => '#ea580c' );
                    for ($i = 5; $i >= 1; $i--): 
                        $pct = $rating_count > 0 ? ($rating_counts[$i] / $rating_count) * 100 : 0;
                    ?>
                    <div class="d-flex align-items-center mb-2" style="gap: 10px;">
                        <span class="fw-bold" style="font-size: 13px; color: #222; width: 10px; font-family: 'Instrument Sans', sans-serif;"><?php echo $i; ?></span>
                        <div class="progress flex-grow-1" style="height: 6px; border-radius: 3px; background-color: #f3f4f6; overflow: hidden;">
                            <div class="progress-bar" role="progressbar" style="width: <?php echo esc_attr($pct); ?>%; border-radius: 3px; background-color: <?php echo esc_attr($colors[$i]); ?>;" aria-valuenow="<?php echo esc_attr($pct); ?>" aria-valuemin="0" aria-valuemax="100"></div>
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
<div class="blog-comments row-margin-top" style="border-top: 1px solid #f3f4f6; padding-top: 40px;">
    <ul class="custom-ul m-0 p-0">
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
<div class="blog-comments-footer d-flex align-items-center mt-4">
    <?php if (have_comments()): ?>
    <span class="me-4" style="font-weight: 700; font-size: 14px; font-family: 'Instrument Sans', sans-serif; color: #111;">View all <?php echo esc_html(get_comments_number()); ?> reviews</span>
    <?php endif; ?>
    <button id="cmr-open-review-modal" class="btn btn-link p-0 text-decoration-none" style="color: #6366f1; font-weight: 600; font-size: 14px; font-family: 'Instrument Sans', sans-serif;">Add a Review <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 12px; margin-left: 4px;"></i></button>
</div>


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

    // Custom form for Modal
    $comment_field = '<div class="row g-3"><div class="col-12"><div class="mb-2">
        <label style="font-family: \'Instrument Sans\', sans-serif; font-weight: 700; font-size: 16px; color: #111; margin-bottom: 15px; display:block;">What insights were most valuable?</label>
        <textarea class="form-control" name="comment" placeholder="Please write product review here." style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; font-family: \'Instrument Sans\', sans-serif; min-height: 120px;" '.esc_attr( $aria_req ).'></textarea>
    </div></div></div>';
    
    if ( get_post_type() === 'cmr_news' ) {
        $custom_rating = '
        <div class="cmr-star-rating-widget mb-4">
            <label style="font-family: \'Instrument Sans\', sans-serif; font-weight: 700; font-size: 16px; color: #111; margin-bottom: 10px; display:block;">How would you rate this report?</label>
            <div class="cmr-stars" style="display: flex; gap: 8px; color: #fbbf24; font-size: 32px; cursor: pointer;">
                <i class="fa-regular fa-star cmr-star" data-val="1"></i>
                <i class="fa-regular fa-star cmr-star" data-val="2"></i>
                <i class="fa-regular fa-star cmr-star" data-val="3"></i>
                <i class="fa-regular fa-star cmr-star" data-val="4"></i>
                <i class="fa-regular fa-star cmr-star" data-val="5"></i>
            </div>
            <select name="rating" id="rating" required style="display:none;">
                <option value="">Rate...</option>
                <option value="5">5</option>
                <option value="4">4</option>
                <option value="3">3</option>
                <option value="2">2</option>
                <option value="1">1</option>
            </select>
        </div>';
        $comment_field = $custom_rating . $comment_field;
    }

	$args = array(
        'fields'                => $fields,
    	'comment_field'         => $comment_field,
        'class_form'            => 'quanto-cform',
    	'title_reply'           => '',
    	'title_reply_before'    => '',
        'title_reply_after'     => '',
        'comment_notes_before'  => '',
        'logged_in_as'          => '',
        'class_submit'          => 'btn btn-dark w-100 py-3',
        'submit_field'          => '<div class="row g-3"><div class="col-12 mt-4">%1$s %2$s</div></div>',
    	'submit_button'         => '<div style="font-size: 12px; color: #4b5563; font-family: \'Instrument Sans\', sans-serif; margin-bottom: 20px;">By submitting, you agree to <a href="#" style="color: #111; text-decoration: underline;">Terms of Use</a> and <a href="#" style="color: #111; text-decoration: underline;">Privacy Policy</a></div>
    	                            <button type="submit" name="%1$s" id="%2$s" class="%3$s" style="border-radius: 30px; font-weight: 600; font-family: \'Instrument Sans\', sans-serif; font-size: 16px; background: #111;">Submit</button>',
	);

    if ( comments_open() ) {
        echo '<!-- Comment Form Modal -->';
        ?>
        <div id="cmr-review-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(243, 244, 246, 0.95); z-index: 9999; align-items: center; justify-content: center;">
            <div class="cmr-review-modal-content" style="background: #fff; padding: 40px; width: 90%; max-width: 650px; position: relative; max-height: 95vh; overflow-y: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.05);">
                <button id="cmr-close-review-modal" style="position: absolute; top: 30px; right: 30px; background: none; border: none; font-size: 16px; color: #6b7280; font-family: 'Instrument Sans', sans-serif; cursor: pointer; display: flex; align-items: center; gap: 8px;">Close <i class="fa-solid fa-xmark"></i></button>
                
                <div class="cmr-modal-header d-flex mb-4 pb-4" style="border-bottom: 1px solid #f3f4f6;">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="me-4" style="width: 100px; flex-shrink: 0;">
                            <?php the_post_thumbnail('thumbnail', array('class' => 'img-fluid', 'style' => 'border: 1px solid #e5e7eb; padding: 2px;')); ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h3 style="font-family: 'Instrument Sans', sans-serif; font-size: 20px; font-weight: 700; color: #111; margin-bottom: 10px; line-height: 1.4;"><?php the_title(); ?></h3>
                        <p style="font-family: 'Instrument Sans', sans-serif; font-size: 16px; color: #6b7280; margin-bottom: 10px;">CyberMedia Research (CMR)</p>
                        <p style="font-family: 'Instrument Sans', sans-serif; font-size: 14px; color: #6b7280; margin-bottom: 0;">SKU: <span style="font-weight: 700; color: #111;">CMR-ADAS-<?php the_ID(); ?></span></p>
                    </div>
                </div>

                <div class="blog-contact-form">
                    <?php comment_form( $args ); ?>
                </div>
            </div>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.getElementById('cmr-review-modal');
            var btn = document.getElementById('cmr-open-review-modal');
            var span = document.getElementById('cmr-close-review-modal');
            var stars = document.querySelectorAll('.cmr-star');
            var ratingSelect = document.getElementById('rating');

            if(btn && modal && span) {
                btn.onclick = function(e) {
                    e.preventDefault();
                    modal.style.display = 'flex';
                }
                span.onclick = function() {
                    modal.style.display = 'none';
                }
                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = 'none';
                    }
                }
            }

            if(stars.length > 0 && ratingSelect) {
                stars.forEach(function(star, index) {
                    star.addEventListener('click', function() {
                        var val = this.getAttribute('data-val');
                        ratingSelect.value = val;
                        
                        // Update visual stars
                        stars.forEach(function(s, i) {
                            if(i < val) {
                                s.classList.remove('fa-regular');
                                s.classList.add('fa-solid');
                            } else {
                                s.classList.remove('fa-solid');
                                s.classList.add('fa-regular');
                            }
                        });
                    });
                });
            }
        });
        </script>
        <?php
        echo '<!-- End of Comment Form Modal -->';
    }
