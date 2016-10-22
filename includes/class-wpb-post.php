<?php

defined( 'ABSPATH' ) or die( 'Busted!' );


/**
 * Inherit this class for other post-types
 */
class WPB_Post extends WPB_Plugin{

    /**
     * Post-type.
     *
     * @var string
     */
    public $post_type = 'post';

    /**
     * Constructor.
     */
    public function __construct() {}
    /**
     * Include files.
     */
    public function includes() {}
    /**
     * Hook into actions and filters.
     */
    public function init_hooks() {
        add_action( 'transition_post_status', [$this, 'post_published'], 10, 3);

        if( is_admin() ){}
    }
    /**
     * When a post unpublishes.
     *
     * Read documentation for values of
     * params $new_status and $old_status
     * here: https://codex.wordpress.org/Post_Status_Transitions
     *
     * @param string    $new_status
     * @param string    $old_status
     * @param WP_Post   $post
     */
    public function post_published( $new_status, $old_status, $post ) {
        echo "POST STATUS CHANGED";
        // exit;
        if ( $old_status != 'publish'  &&  $new_status == 'publish' ) {
            if( $post->post_type == $this->post_type ) {
                $this->push( $post );
            }
        }
    }
    /**
     * Prepares and creates a push notification of this post
     * by posting it to PushBots
     *
     * But first for testing's sake we'll just send an email to the author :)
     *
     * @param WP_Post   $post
     */
    public function push( $post ) {
        echo "The ID of the new post is: $post->ID";
        print_r($post);

        // exit;

        // $author = $post->post_author; /* Post author ID. */
        // $name = get_the_author_meta( 'display_name', $author );
        // $email = get_the_author_meta( 'user_email', $author );
        // $title = $post->post_title;
        // $permalink = get_permalink( $ID );
        // $edit = get_edit_post_link( $ID, '' );
        // $to[] = sprintf( '%s <%s>', $name, $email );
        // $subject = sprintf( 'Published: %s', $title );
        // $message = sprintf ('Congratulations, %s! Your article “%s” has been published.' . "\n\n", $name, $title );
        // $message .= sprintf( 'View: %s', $permalink );
        // $headers[] = '';
        // wp_mail( $to, $subject, $message, $headers );
    }
}
