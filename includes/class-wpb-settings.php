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
     * WPB settings.
     */
    public function settings_init() {
        // register a new setting for "wporg" page
        register_setting('wpb', WPB_ARRAY_OPTIONS_KEY);

        // register a new section in the "wporg" page
        add_settings_section(
            'wpb_section_payload',
            __('Payload', WPB_TXTDMN),
            'wpb_settings_section_payload_cb',
            'wpb'
        );

        // register fields in the "wpb_section_payload" section, inside the "wpb" page
        add_settings_field(
            'wpb_field_payload_post_item_key',
            __('Post Item Key', WPB_TXTDMN),
            'wpb_settings_textinput_field_cb',
            'wpb',
            'wpb_section_payload',
            [
                'label_for'         => 'wpb_field_payload_post_item_key',// use to populate the id inside the callback
                'class'             => 'wpb_row',
                // 'wpb_custom_data' => 'custom',
            ]
        );
        add_settings_field(
            'wpb_field_payload_post_item_value',
            __('Post Item Value', WPB_TXTDMN),
            'wpb_settings_select_field_cb',
            'wpb',
            'wpb_section_payload',
            [
                'label_for'         => 'wpb_field_payload_post_item_value',
                'class'             => 'wpb_row',
                'wpb_options'       => [
                    ['id', _('ID', WPB_TXTDMN)],
                    ['slug', _('Slug', WPB_TXTDMN)],
                ],
                'wpb_description' => __('Populate the Payload with an array containing IDs or Slugs'),
            ]
        );
        // add_settings_field(
        //     'wpb_field_payload_categories_key',
        //     __('Categories Key', WPB_TXTDMN),
        //     'wpb_settings_textinput_field_cb',
        //     'wpb',
        //     'wpb_section_payload',
        //     [
        //         'label_for'         => 'wpb_field_payload_categories_key_key',
        //         'class'             => 'wpb_row',
        //     ]
        // );
    }
}
