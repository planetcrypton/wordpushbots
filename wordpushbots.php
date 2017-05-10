<?php
/*
Plugin Name: WordPushBots
Description: Making Pushbots send push-notifications to apps when you're publishing posts
Version:     1.0.0-beta.0
Author:      sejKo
Author URI:  http://sej-ko.dk
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/


defined( 'ABSPATH' ) or die( 'Busted!' );


if ( ! class_exists( 'WordPushBots' ) ) :

    require_once('includes/class-wpb-plugin.php');

/**
 * Main WordPushBots Class.
 *
 * @class WordPushBots
 * @version 0.0.1
 */
final class WordPushBots extends WPB_Plugin{

    /**
     * WordPushBots version.
     *
     * @var string
     */
    public $version = '1.0.0-beta.0';

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
    public function includes() {
        include_once( 'includes/wpb-template-functions.php' );
        include_once( 'includes/class-wpb-settings.php' );
        include_once( 'includes/class-wpb-post.php' );

        $this->settings = new WPB_Settings();
        $this->settings->includes();

        $this->post = new WPB_Post();
        $this->post->includes();

        if ( $this->is_request( 'admin' ) ) {
            include_once( 'includes/admin/class-wpb-admin.php' );

            $this->admin = new WPB_Admin();
            $this->admin->includes();
        }
    }

    /**
     * Hook into actions and filters.
     */
    public function init_hooks() {
        $this->settings->init_hooks();
        $this->post->init_hooks();

        if( isset($this->admin) ) {
            $this->admin->init_hooks();
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


