with open('comments.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

new_lines = []
skip = False

# We want to replace the whole blog-comments container and the comment form container.
# It's safer to just replace from "<!-- Comments -->" to the end of the file.

in_comments_block = False

for i, line in enumerate(lines):
    if line.strip() == "<!-- Comments -->":
        in_comments_block = True
        
        replacement = """<!-- Comments -->
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
    
    <div class="d-flex align-items-center mt-4">
        <span class="me-4" style="font-weight: 700; font-size: 14px; font-family: 'Instrument Sans', sans-serif;">View all <?php echo esc_html(get_comments_number()); ?> reviews</span>
        <button id="cmr-open-review-modal" class="btn btn-link p-0 text-decoration-none" style="color: #6366f1; font-weight: 600; font-size: 14px; font-family: 'Instrument Sans', sans-serif;">Add a Review <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 12px; margin-left: 4px;"></i></button>
    </div>
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
    }
"""
        new_lines.append(replacement)
        
    if in_comments_block:
        # We just skip everything until the end of the file since we are replacing all of it.
        pass
    else:
        new_lines.append(line)

with open('comments.php', 'w', encoding='utf-8') as f:
    f.writelines(new_lines)
print("Done comment footer and modal replace")
