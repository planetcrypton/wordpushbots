<?php

defined( 'ABSPATH' ) or die( 'Busted!' );


abstract class WPB_Plugin {

    abstract public function includes();
    abstract public function init_hooks();


    /**
     * Define constant if not already set.
     *
     * @param  string $name
     * @param  string|bool $value
     */
    protected function define( $name, $value ) {
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
    protected function is_request( $type ) {
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
