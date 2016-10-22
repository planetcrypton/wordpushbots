<?php

defined( 'ABSPATH' ) or die( 'Busted!' );


class WPB_Settings extends WPB_Plugin{

    /**
     * Single option key.
     * Only useful when using one single setting
     *
     * @var string
     */
    public $single_option_key = 'wpb_option';
    /**
     * Array option key.
     * When using one global option key for all settings,
     * all saved into this array
     *
     * @var string
     */
    public $array_option_key = 'wpb_options';


    /**
     * WPB_Settings Constructor.
     */
    public function __construct() {
        $this->define_constants();
    }
    /**
     * Define WPB Constants.
     */
    private function define_constants() {
        $this->define( 'WPB_SINGLE_OPTION_KEY', $this->single_option_key );
        $this->define( 'WPB_ARRAY_OPTIONS_KEY', $this->array_option_key );
    }
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
            add_action('admin_menu', [$this, 'options_pages']);
            add_action('admin_init', [$this, 'settings_init']);
        }
    }

    /**
     * WPB options page.
     */
    public function options_pages() {
        // add_menu_page(
        add_submenu_page(
            'options-general.php',
            __('WordPushBots', WPB_TXTDMN),
            __('WordPushBots', WPB_TXTDMN),
            'manage_options',
            'wordpushbots',
            [$this, 'options_page_html']
            // ,plugin_dir_url(__FILE__) . 'images/icon_wporg.png',
            // 20
        );
    }

    /**
     * WPB options page HTML.
     */
    public function options_page_html() {
        // check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?= esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                // output security fields for the registered setting "wpb_options"
                settings_fields('wpb');
                // output setting sections and their fields
                // (sections are registered for "wpb", each field is registered to a specific section)
                do_settings_sections('wpb');
                // output save settings button
                submit_button('Save Settings');
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * WPB settings page.
     */
    public function settings_init() {
        // register a new setting for "wpb" page
        register_setting('wpb', WPB_ARRAY_OPTIONS_KEY);

        // register section "Account" in the "wpb" page
        add_settings_section(
            'wpb_section_account',
            __('Account', WPB_TXTDMN),
            [$this, 'section_account_cb'],
            'wpb'
        );
        add_settings_field(
            'wpb_field_account_app_id',
            __('App ID', WPB_TXTDMN),
            'wpb_settings_textinput_field_cb',
            'wpb',
            'wpb_section_account',
            [
                'label_for'         => 'wpb_field_account_app_id',
            ]
        );
        add_settings_field(
            'wpb_field_account_app_secret',
            __('App secret', WPB_TXTDMN),
            'wpb_settings_textinput_field_cb',
            'wpb',
            'wpb_section_account',
            [
                'label_for'         => 'wpb_field_account_app_secret',
            ]
        );
    }

    /**
     * Description account section
     * @param array $args
     */
    public function section_account_cb( $args ) {
        $content = esc_html__('Enter your PushBots account-data.', WPB_TXTDMN);
        wpb_settings_section_description_cb($content, $args);
    }
    /**
     * Description account section
     * @param array $args
     */
    public function section_payload_cb( $args ) {
        $content = esc_html__('Configure the payload to be sent to the apps.', WPB_TXTDMN);
        wpb_settings_section_description_cb($content, $args);
    }
}
