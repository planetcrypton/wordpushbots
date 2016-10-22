<?php
/*
Plugin Name: WordPushBots
Description: Making Pushbots send push-notifications to apps when you're publishing posts
Version:     0.0.1
Author:      sejKo
Author URI:  http://sej-ko.dk
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/


defined( 'ABSPATH' ) or die( 'Busted!' );


if ( ! class_exists( 'WordPushBots' ) ) :

/**
 * Main WordPushBots Class.
 *
 * @class WordPushBots
 * @version 0.0.1
 */
final class WordPushBots {

    /**
     * WordPushBots version.
     *
     * @var string
     */
    public $version = '0.0.1';

    /**
     * Text-domain.
     *
     * @var string
     */
    public $text_domain = 'sejko';

    /**
     * The single instance of the class.
     *
     * @var WordPushBots
     */
    protected static $_instance = null;

    /**
     * The settings instance of class WPB_Settings.
     *
     * @var WPB_Settings
     */
    public $settings = null;

    /**
     * The settings instance of class WPB_Admin.
     *
     * @var WPB_Admin
     */
    public $admin = null;

    /**
     * Main WordPushBots Instance.
     *
     * Ensures only one instance of WordPushBots is loaded or can be loaded.
     *
     * @static
     * @return WordPushBots - Main instance.
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * WordPushBots Constructor.
     */
    public function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Define WPB Constants.
     */
    private function define_constants() {
        $this->define( 'WPB_PLUGIN_FILE', __FILE__ );
        $this->define( 'WPB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
        $this->define( 'WPB_VERSION', $this->version );
        $this->define( 'WPB_TXTDMN', $this->text_domain );
    }

    /**
     * Include files.
     */
    private function includes() {
        include_once( 'includes/wpb-template-functions.php' );
        include_once( 'includes/class-wpb-settings.php' );
        include_once( 'includes/class-wpb-post.php' );

        $this->settings = new WPB_Settings();
        $this->post = new WPB_Post();

        if ( $this->is_request( 'admin' ) ) {
            include_once( 'includes/admin/class-wpb-admin.php' );

            $this->admin = new WPB_Admin();
        }
    }

    /**
     * Hook into actions and filters.
     */
    private function init_hooks() {
        $this->settings->init_hooks();

        if( isset($this->admin) ) {
            $this->admin->init_hooks();
        }
    }

    /**
     * Define constant if not already set.
     *
     * @param  string $name
     * @param  string|bool $value
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

    /**
     * What type of request is this?
     *
     * @param  string $type admin, ajax, cron or frontend.
     * @return bool
     */
    private function is_request( $type ) {
        switch ( $type ) {
            case 'admin' :
                return is_admin();
            case 'ajax' :
                return defined( 'DOING_AJAX' );
            case 'cron' :
                return defined( 'DOING_CRON' );
            case 'frontend' :
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
    }
}

endif; // class_exists( 'WordPushBots' )

/**
 * Main instance of WordPushBots.
 *
 * Returns the main instance of WPB to prevent the need to use globals.
 *
 * @since  2.1
 * @return WordPushBots
 */
function WPB() {
    return WordPushBots::instance();
}

$wordpushbots = WPB();


