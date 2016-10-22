<?php

defined( 'ABSPATH' ) or die( 'Busted!' );


class WPB_Admin{

    /**
     * Constructor.
     */
    public function __construct() {}

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
