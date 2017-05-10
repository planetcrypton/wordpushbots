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
            add_action('admin_enqueue_scripts', [$this, 'assets']);
        }
    }

    /**
     * Assets for WPB options pages
     */
    public function assets( $hook ) {
        if( $hook === 'settings_page_wordpushbots' ) {
            wp_enqueue_style( 'wpb_admin_css', plugin_dir_url( WPB_PLUGIN_BASENAME ) . '/assets/css/style.css' );
            #wp_enqueue_script( 'wpb_admin_js', plugin_dir_url( WPB_PLUGIN_BASENAME ) . '/assets/js/script.js' );
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
        $avail_tabs = ['account', 'notification', 'target', 'payload'];
        $tabs_data = [
            'account' => ['title'=>__('Account', WPB_TXTDMN)],
            'notification' => ['title'=>__('Notification', WPB_TXTDMN)],
            'target' => ['title'=>__('Target', WPB_TXTDMN)],
            'payload' => ['title'=>__('Payload', WPB_TXTDMN)],
        ];
        if( in_array($_GET['tab'], $avail_tabs) ) {
            $tab = $_GET['tab'];
        }else{
            $tab = $avail_tabs[0];
        }
        ?>
        <div class="wrap">
            <h1><?= esc_html(get_admin_page_title()); ?></h1>
            <form id="settings-form" action="options.php" method="post">
                <h2 class="nav-tab-wrapper">
                <?php foreach ($tabs_data as $key => $tab_data):
                    $title = $tab_data['title'];
                    $activeClass = $tab===$key ? 'nav-tab-active' : '';
                ?>
                    <a class="nav-tab <?= $activeClass ?>" href="?page=wordpushbots&tab=<?= $key; ?>">
                        <?= $title; ?>
                    </a>
                <?php endforeach; ?>
                </h2>
                <section>
                    <?php do_settings_sections('wpb_' . $tab); ?>
                </section>
                <?php
                // output security fields for the registered setting "wpb_options"
                settings_fields('wpb_' . $tab);

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
        // register section "Account" in the "wpb" page
        register_setting('wpb_account', 'wpb_account');
        add_settings_section(
            'wpb_section_account',
            __('Account', WPB_TXTDMN),
            [$this, 'section_account_cb'],
            'wpb_account'
        );

        add_settings_field(
            'wpb_field_account_app_id',
            __('App ID', WPB_TXTDMN),
            'wpb_settings_textinput_field_cb',
            'wpb_account',
            'wpb_section_account',
            [
                'label_for'         => 'wpb_field_account_app_id',
                'page'              => 'wpb_account',
            ]
        );

        add_settings_field(
            'wpb_field_account_app_secret',
            __('App secret', WPB_TXTDMN),
            'wpb_settings_textinput_field_cb',
            'wpb_account',#'wordpushbots',
            'wpb_section_account',
            [
                'label_for'         => 'wpb_field_account_app_secret',
                'page'              => 'wpb_account',
            ]
        );



        // register section "Notification" in the "wpb" page
        register_setting('wpb_notification', 'wpb_notification');
        add_settings_section(
            'wpb_section_notification',
            __('Notification', WPB_TXTDMN),
            [$this, 'section_notification_cb'],
            'wpb_notification'
        );
        add_settings_field(
            'wpb_field_notification_post_alert_content',
            __('Alert message', WPB_TXTDMN),
            'wpb_settings_select_field_cb',
            'wpb_notification',
            'wpb_section_notification',
            [
                'label_for'         => 'wpb_field_notification_post_alert_content',
                'page'              => 'wpb_notification',
                'wpb_options'       => [
                    ['post-title', _('Post title', WPB_TXTDMN)],
                    ['post-content', _('Post content (140 chars)', WPB_TXTDMN)],
                    ['custom-content', _('Custom', WPB_TXTDMN)],
                ],
                'wpb_description'   => __('For custom use field below', WPB_TXTDMN),
            ]
        );
        add_settings_field(
            'wpb_field_notification_post_custom_alert',
            __("Custom alert", WPB_TXTDMN),
            'wpb_settings_textarea_field_cb',
            'wpb_notification',
            'wpb_section_notification',
            [
                'label_for'         => 'wpb_field_notification_post_custom_alert',
                'page'              => 'wpb_notification',
                'rows'              => 4,
                'wpb_description'   => __('Available variables: $author, $title', WPB_TXTDMN),
            ]
        );
        // add_settings_field(
        //     'wpb_field_notification_bagde',
        //     __('Badge (iOS only)', WPB_TXTDMN),
        //     'wpb_settings_checkbox_field_cb',
        //     'wpb_notification',
        //     'wpb_section_notification',
        //     [
        //         'wpb_id'    => 'wpb_field_notification_bagde',
        //         'wpb_label' => __('Increase app-bagde'),
        //     ]
        // );

        // register section "Target" in the "wpb" page
        register_setting('wpb_target', 'wpb_target');
        add_settings_section(
            'wpb_section_target',
            __('Target', WPB_TXTDMN),
            [$this, 'section_target_cb'],
            'wpb_target'
        );
        add_settings_field(
            'wpb_field_target_with_alias',
            __("With alias", WPB_TXTDMN),
            'wpb_settings_textinput_field_cb',
            'wpb_target',
            'wpb_section_target',
            [
                'label_for'         => 'wpb_field_target_with_alias',
                'page'              => 'wpb_target',
            ]
        );
        add_settings_field(
            'wpb_field_target_taggedwith_post_categories',
            __("Tagged with one of post's categories (slugs)", WPB_TXTDMN),
            'wpb_settings_checkbox_field_cb',
            'wpb_target',
            'wpb_section_target',
            [
                'label_for'         => 'wpb_field_target_taggedwith_post_categories',
                'page'              => 'wpb_target',
            ]
        );
        add_settings_field(
            'wpb_field_target_taggedwith_post_tags',
            __("Tagged with one of post's tags (slugs)", WPB_TXTDMN),
            'wpb_settings_checkbox_field_cb',
            'wpb_target',
            'wpb_section_target',
            [
                'label_for'         => 'wpb_field_target_taggedwith_post_tags',
                'page'              => 'wpb_target',
            ]
        );
        add_settings_field(
            'wpb_field_target_taggedwith_input',
            __("Tagged with", WPB_TXTDMN),
            'wpb_settings_textinput_field_cb',
            'wpb_target',
            'wpb_section_target',
            [
                'label_for'         => 'wpb_field_target_taggedwith_input',
                'page'              => 'wpb_target',
                'wpb_description'   => __('Comma separated', WPB_TXTDMN),
            ]
        );

        // register section "Payload" in the "wpb" page
        register_setting('wpb_payload', 'wpb_payload');
        add_settings_section(
            'wpb_section_payload',
            __('Payload', WPB_TXTDMN),
            [$this, 'section_payload_cb'],
            'wpb_payload'
        );
        add_settings_field(
            'wpb_field_payload_post_item_key',
            __('Post Item Key', WPB_TXTDMN),
            'wpb_settings_textinput_field_cb',
            'wpb_payload',
            'wpb_section_payload',
            [
                'label_for'         => 'wpb_field_payload_post_item_key',
                'page'              => 'wpb_payload',
            ]
        );
        add_settings_field(
            'wpb_field_payload_post_item_value',
            __('Post Item Value', WPB_TXTDMN),
            'wpb_settings_select_field_cb',
            'wpb_payload',
            'wpb_section_payload',
            [
                'label_for'         => 'wpb_field_payload_post_item_value',
                'page'              => 'wpb_payload',
                'wpb_options'       => [
                    ['id', _('ID', WPB_TXTDMN)],
                    ['slug', _('Slug', WPB_TXTDMN)],
                ],
                'wpb_description'   => __('Populate the Payload with ID or Slug', WPB_TXTDMN),
            ]
        );
        add_settings_field(
            'wpb_field_payload_categories_key',
            __('Categories Key', WPB_TXTDMN),
            'wpb_settings_textinput_field_cb',
            'wpb_payload',
            'wpb_section_payload',
            [
                'label_for'         => 'wpb_field_payload_categories_key',
                'page'              => 'wpb_payload',
            ]
        );
        add_settings_field(
            'wpb_field_payload_categories_value',
            __('Categories Value', WPB_TXTDMN),
            'wpb_settings_select_field_cb',
            'wpb_payload',
            'wpb_section_payload',
            [
                'label_for'         => 'wpb_field_payload_categories_value',
                'page'              => 'wpb_payload',
                'wpb_options'       => [
                    ['id', _('ID', WPB_TXTDMN)],
                    ['slug', _('Slug', WPB_TXTDMN)],
                ],
                'wpb_description'   => __('Populate the Payload with an array containing IDs or Slugs', WPB_TXTDMN),
            ]
        );
        add_settings_field(
            'wpb_field_payload_tags_key',
            __('Tags Key', WPB_TXTDMN),
            'wpb_settings_textinput_field_cb',
            'wpb_payload',
            'wpb_section_payload',
            [
                'label_for'         => 'wpb_field_payload_tags_key',
                'page'              => 'wpb_payload',
            ]
        );
        add_settings_field(
            'wpb_field_payload_tags_value',
            __('Tags Value', WPB_TXTDMN),
            'wpb_settings_select_field_cb',
            'wpb_payload',
            'wpb_section_payload',
            [
                'label_for'         => 'wpb_field_payload_tags_value',
                'page'              => 'wpb_payload',
                'wpb_options'       => [
                    ['id', _('ID', WPB_TXTDMN)],
                    ['slug', _('Slug', WPB_TXTDMN)],
                ],
                'wpb_description'   => __('Populate the Payload with an array containing IDs or Slugs', WPB_TXTDMN),
            ]
        );
    }

    /**
     * Description Account section
     * @param array $args
     */
    public function section_account_cb( $args ) {
        $content = esc_html__("Enter your PushBots account-data.", WPB_TXTDMN);
        wpb_settings_section_description_cb($content, $args);
    }
    /**
     * Description Notification section
     * @param array $args
     */
    public function section_notification_cb( $args ) {
        $content = esc_html__("What kind of notification must be received?", WPB_TXTDMN);
        wpb_settings_section_description_cb($content, $args);
    }
    /**
     * Description Target section
     * @param array $args
     */
    public function section_target_cb( $args ) {
        $content = esc_html__("Narrow down your audience: Push notification are received by everyone...", WPB_TXTDMN);
        wpb_settings_section_description_cb($content, $args);
    }
    /**
     * Description Payload section
     * @param array $args
     */
    public function section_payload_cb( $args ) {
        $content = esc_html__("Configure the payload to be sent to the apps. Leave blank if not added to payload.", WPB_TXTDMN);
        wpb_settings_section_description_cb($content, $args);
    }
}
