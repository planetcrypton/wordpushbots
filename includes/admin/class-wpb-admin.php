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
        }
    }

}
