with open('comments.php', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Fix the "Add a Review" button location
old_footer = """    <div class="d-flex align-items-center mt-4">
        <span class="me-4" style="font-weight: 700; font-size: 14px; font-family: 'Instrument Sans', sans-serif;">View all <?php echo esc_html(get_comments_number()); ?> reviews</span>
        <button id="cmr-open-review-modal" class="btn btn-link p-0 text-decoration-none" style="color: #6366f1; font-weight: 600; font-size: 14px; font-family: 'Instrument Sans', sans-serif;">Add a Review <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 12px; margin-left: 4px;"></i></button>
    </div>
</div>
<!-- End of Comments -->
<?php
    endif;
?>"""

new_footer = """</div>
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
"""
if old_footer in content:
    content = content.replace(old_footer, new_footer)
else:
    print("Warning: Could not find old_footer")

# 2. Rewrite the modal and comment_form arguments
old_modal = """    if ( ( get_post_type() === 'product' && class_exists( 'WooCommerce' ) && wc_review_ratings_enabled() ) || get_post_type() === 'cmr_news' ) {
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
        echo '<!-- Comment Form Modal -->';
        ?>
        <div id="cmr-review-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
            <div class="cmr-review-modal-content" style="background: #fff; padding: 40px; border-radius: 12px; width: 90%; max-width: 600px; position: relative; max-height: 90vh; overflow-y: auto;">
                <button id="cmr-close-review-modal" style="position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
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
        });
        </script>
        <?php
        echo '<!-- End of Comment Form Modal -->';
    }"""

# New modal structure and styling
new_modal = """    // Custom form for Modal
    $comment_field = '<div class="row g-3"><div class="col-12"><div class="mb-2">
        <label style="font-family: \\'Instrument Sans\\', sans-serif; font-weight: 700; font-size: 16px; color: #111; margin-bottom: 15px; display:block;">What insights were most valuable?</label>
        <textarea class="form-control" name="comment" placeholder="Please write product review here." style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; font-family: \\'Instrument Sans\\', sans-serif; min-height: 120px;" '.esc_attr( $aria_req ).'></textarea>
    </div></div></div>';
    
    if ( get_post_type() === 'cmr_news' ) {
        $custom_rating = '
        <div class="cmr-star-rating-widget mb-4">
            <label style="font-family: \\'Instrument Sans\\', sans-serif; font-weight: 700; font-size: 16px; color: #111; margin-bottom: 10px; display:block;">How would you rate this report?</label>
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
    	'submit_button'         => '<div style="font-size: 12px; color: #4b5563; font-family: \\'Instrument Sans\\', sans-serif; margin-bottom: 20px;">By submitting, you agree to <a href="#" style="color: #111; text-decoration: underline;">Terms of Use</a> and <a href="#" style="color: #111; text-decoration: underline;">Privacy Policy</a></div>
    	                            <button type="submit" name="%1$s" id="%2$s" class="%3$s" style="border-radius: 30px; font-weight: 600; font-family: \\'Instrument Sans\\', sans-serif; font-size: 16px; background: #111;">Submit</button>',
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
    }"""

if old_modal in content:
    content = content.replace(old_modal, new_modal)
else:
    print("Warning: Could not find old_modal")

with open('comments.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Done")
