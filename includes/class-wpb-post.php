<?php

class WPB_Post{

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
     * Hook into actions and filters.
     */
    public function init_hooks() {
        add_action( 'transition_post_status', [this, 'post_unpublished'], 10, 3 );

        if( is_admin() ){}
    }
    /**
     * When a post unpublishes.
     *
     * @param string    $new_status
     * @param string    $old_status
     * @param WP_Post   $post
     */
    function post_unpublished( $new_status, $old_status, $post ) {
        if ( $old_status == 'publish'  &&  $new_status != 'publish' ) {
            // A function to perform actions when a post status changes from publish to any non-public status.
        }
    }
}
