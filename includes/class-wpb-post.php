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
    public function includes() {
        require_once("class-pushbots.php");
    }
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
        if( !defined('WPB_ARRAY_OPTIONS_KEY') ) {
            return;
        }
        $options = get_option( WPB_ARRAY_OPTIONS_KEY );
        print_r($options);

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


        // Send to Pushbots
        $app_id = $options['wpb_field_account_app_id'];
        $app_secret = $options['wpb_field_account_app_secret'];

        $alert_message = $post->post_title;

        $pb = new PushBots();
        $pb->App($app_id, $app_secret);
        $pb->Platform( ["0","1"] );
        $pb->Alert( $alert_message );

        // badge
        if( $options['wpb_field_notification_bagde'] === 1 ){
            $pb->Badge("+1");
        }
        // payload
        $payload = [];
        if( !empty($options['wpb_field_payload_post_item_key']) ) {
            echo "Do add post_item";
            $post_item_key = $options['wpb_field_payload_post_item_key'];
            $post_item_val = $options['wpb_field_payload_post_item_value'];
            $payload[ $post_item_key ] = $post_item_val == 'id' ? $post->ID : $post->post_name;
        }
        if( !empty($options['wpb_field_payload_categories_key']) ) {
            echo "Do add categories";
            $categories_key = $options['wpb_field_payload_categories_key'];
            $categories_val = $options['wpb_field_payload_categories_value'];
            if( $categories_val == 'id' ) {
                $payload[ $categories_key ] = wp_get_post_categories($post->ID, ['fields' => 'ids'] );
            }else
            if(  $categories_val == 'slug' ) {
                $payload[ $categories_key ] = wp_get_post_categories($post->ID, ['fields' => 'slugs'] );
            }
        }
        if( !empty($options['wpb_field_payload_tags_key']) ) {
            echo "Do add tags";
            $tags_key = $options['wpb_field_payload_tags_key'];
            $tags_val = $options['wpb_field_payload_tags_value'];
            if( $tags_val == 'id' ) {
                $payload[ $tags_key ] = wp_get_post_tags( $post->ID, ['fields' => 'ids'] );
            }else
            if(  $categories_val == 'slug' ) {
                $payload[ $tags_key ] = wp_get_post_tags($post->ID, ['fields' => 'slugs'] );
            }
        }
        if( count($payload) > 0 ) {
            $pb->Payload($payload);
        }

        // tags
        $pb->Tags( wp_get_post_categories($post->ID, ['fields' => 'slugs'] ) );
        $pb->Push();
    }
}
