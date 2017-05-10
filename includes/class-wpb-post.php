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
    protected $post_type = 'post';

    /**
     * Post object.
     *
     * @var WP_Post
     */
    protected $post;

    /**
     * Constant; max no of characters apush alert may contain
     *
     * @var ALERT_MAX_CHAR
     */
    const ALERT_MAX_CHAR = 140;

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
        /*
add_action('transition_comment_status', 'my_approve_comment_callback', 10, 3);
function my_approve_comment_callback($new_status, $old_status, $comment) {
    if($old_status != $new_status) {
        if($new_status == 'approved') {
            // Your code here
        }
    }
}
        */
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
                $this->post = $post;
                $this->push();
            }
        }
    }
    /**
     * Prepares and creates a push notification of this post
     * by posting it to PushBots
     *
     * But first for testing's sake we'll just send an email to the author :)
     */
    public function push() {
        // if( !defined('WPB_ARRAY_OPTIONS_KEY') ) {
        //     return;
        // }
        // $options = get_option( WPB_ARRAY_OPTIONS_KEY );
        $options_account        = get_option( 'wpb_account' );
        $options_notification   = get_option( 'wpb_notification' );
        $options_target         = get_option( 'wpb_target' );
        #$options_payload        = get_option( 'wpb_payload' );

        // var_dump($options_target);
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

        // account info must be set
        if( !isset($options_account['wpb_field_account_app_id']) OR empty($options_account['wpb_field_account_app_id'] )
        OR  !isset($options_account['wpb_field_account_app_secret']) OR empty($options_account['wpb_field_account_app_secret']) ) {
            return;
        }
        $app_id = $options_account['wpb_field_account_app_id'];
        $app_secret = $options_account['wpb_field_account_app_secret'];


        $pb = new PushBots();
        $pb->App($app_id, $app_secret);
        $pb->Platform( ["0","1"] );

        // alert message
        $pb->Alert( $this->getAlertMessage() );

        // badge
        // if( isset($options['wpb_field_notification_bagde']) && $options['wpb_field_notification_bagde']=== 1 ){
        //     $pb->Badge("+1");
        // }

        // payload
        $payload = $this->getPayload();
        if( count($payload) > 0 ) {
            $pb->Payload($payload);
        }

        // target
        if( !empty($options_target['wpb_field_target_with_alias']) ) {
            $pb->Alias( $options_target['wpb_field_target_with_alias'] );
        }
        $tags = $this->getTags();
        if( count($tags) > 0 ) {
            $pb->Tags( $tags );
        }

        $pb->Push();
    }
    /**
     * Retrieves the text for the alert-message to be sent
     * with the alert to the device
     *
     * @return string
     */
    protected function getAlertMessage() {
        $options = get_option( 'wpb_notification' );
        if( $options['wpb_field_notification_post_alert_content'] === 'custom-content' && !empty($options['wpb_field_notification_post_custom_alert']) ) {
            return preg_replace(
                ['/[\n\r]/', '/\$title/', '/\$author/'],
                [' ', $this->post->post_title, get_the_author_meta('display_name', $this->post->post_author)],
                $options['wpb_field_notification_post_custom_alert']
            );
        }else
        if( $options['wpb_field_notification_post_alert_content'] === 'post-content' ) {
            return $this->getPostContentAlert();
        }else{
            return $this->post->post_title;
        }
    }
    /**
     * Retrieves the payload to be sent
     * with the alert to the device
     *
     * @return array
     */
    protected function getPayload() {
        $options = get_option( 'wpb_payload' );
        $payload = [];
        if( !empty($options['wpb_field_payload_post_item_key']) ) {
            // add post to payload (which key, which value)
            $post_item_key = $options['wpb_field_payload_post_item_key'];
            $post_item_val = $options['wpb_field_payload_post_item_value'];
            $payload[ $post_item_key ] = $post_item_val == 'id' ? $this->post->ID : $this->post->post_name;
        }
        if( !empty($options['wpb_field_payload_categories_key']) ) {
            // add post-categories to payload (which key, which value)
            $categories_key = $options['wpb_field_payload_categories_key'];
            $categories_val = $options['wpb_field_payload_categories_value'];
            if( $categories_val == 'id' ) {
                $payload[ $categories_key ] = wp_get_post_categories($this->post->ID, ['fields' => 'ids'] );
            }else
            if(  $categories_val == 'slug' ) {
                $payload[ $categories_key ] = wp_get_post_categories($this->post->ID, ['fields' => 'slugs'] );
            }
        }
        if( !empty($options['wpb_field_payload_tags_key']) ) {
            // add post-tags to payload (which key, which value)
            $tags_key = $options['wpb_field_payload_tags_key'];
            $tags_val = $options['wpb_field_payload_tags_value'];
            if( $tags_val == 'id' ) {
                $payload[ $tags_key ] = wp_get_post_tags($this->post->ID, ['fields' => 'ids'] );
            }else
            if(  $tags_val == 'slug' ) {
                $payload[ $tags_key ] = wp_get_post_tags($this->post->ID, ['fields' => 'slugs'] );
            }
        }
        return $payload;
    }
    /**
     * Retrieves the tags to be set
     * at PushBots
     *
     * @return array
     */
    protected function getTags() {
        $options = get_option( 'wpb_target' );
        $tags = [];
        if( !empty($options['wpb_field_target_taggedwith_post_categories']) ) {
            $tags = array_merge($tags, wp_get_post_categories($this->post->ID, ['fields' => 'slugs'] ));
        }
        if( !empty($options['wpb_field_target_taggedwith_post_tags']) ) {
            $tags = array_merge($tags, wp_get_post_tags($this->post->ID, ['fields' => 'slugs'] ));
        }
        if( !empty($options['wpb_field_target_taggedwith_input']) ) {
            $inputTags = $options['wpb_field_target_taggedwith_input'];
            $inputTags = preg_replace('/\s+/', '', $inputTags);
            $tags = array_merge($tags,  explode(',', $inputTags));
        }
        return $tags;
    }
    /**
     * Returns the content of a post
     * trimmed down to the maximum
     * allowed number of characters
     * a push notifcation alert may contain.
     *
     * @return string
     */
    protected function getPostContentAlert() {
        $excerpt = strip_tags(strip_shortcodes($this->post->post_content));
        $charlength = static::ALERT_MAX_CHAR + 1;

        if ( mb_strlen( $excerpt ) > $charlength ) {
            $cutExcerpt = '';
            $subex = mb_substr( $excerpt, 0, $charlength - 5 );
            $exwords = explode( ' ', $subex );
            $excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
            if ( $excut < 0 ) {
                $cutExcerpt .= mb_substr( $subex, 0, $excut );
            } else {
                $cutExcerpt .= $subex;
            }
            $cutExcerpt .= '[...]';
            return $cutExcerpt;
        } else {
            return $excerpt;
        }
    }
}
