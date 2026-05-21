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

    if ( get_post_type() === 'product' && class_exists( 'WooCommerce' ) && wc_review_ratings_enabled() ) {
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
