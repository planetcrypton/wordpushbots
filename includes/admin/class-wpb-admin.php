<?php

defined( 'ABSPATH' ) or die( 'Busted!' );


class WPB_Admin extends WPB_Plugin{

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
        // ..

        if( is_admin() ){
            // queue scripts etc for admin

            // add_action(..);
            add_filter( 'plugin_action_links_' . WPB_PLUGIN_BASENAME, array( __CLASS__, 'plugin_action_links' ) );
        }
    }

    /**
     * Show action links on the plugin screen.
     *
     * @param   mixed $links Plugin Action links
     * @return  array
     */
    public static function plugin_action_links( $links ) {
        $action_links = array(
            'settings' => '<a href="' . admin_url( 'options-general.php?page=wordpushbots' ) . '" title="' . esc_attr( __( 'View WordPushBots Settings', WPB_TXTDMN ) ) . '">' . __( 'Settings', WPB_TXTDMN ) . '</a>',
        );

        return array_merge( $action_links, $links );
    }

}
