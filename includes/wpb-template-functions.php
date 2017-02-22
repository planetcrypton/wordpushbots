<?php

defined( 'ABSPATH' ) or die( 'Busted!' );


// test..
function wpb_admin_section_html($func) {
    ?>
    <fieldset>
    <?php $func(); ?>
    </fieldset>
    <?php
}

/**
 * Outputs settings section for payload
 *
 * @param array $args
 */
function wpb_settings_section_description_cb($content, $args)
{
    ?>
    <p id="<?= esc_attr($args['id']); ?>"><?= $content; ?></p>
    <?php
}
/**
 * Outputs settings field description
 *
 * @param string $content
 */
function wpb_settings_field_description($content)
{
    ?>
    <p class="description">
        <?= $content; ?>
    </p>
    <?php
}
/**
 * Outputs settings text-input field
 *
 * @param array $args
 */
function wpb_settings_textinput_field_cb($args)
{
    // get the value of the setting we've registered with register_setting()
    $options = get_option( WPB_ARRAY_OPTIONS_KEY );
    // var_dump($args['label_for']);
    // print_r($options);
    // var_dump($options[$args['label_for']]);

    // output the field
    ?>
    <input type="text"
        class="regular-text"
        id="<?= esc_attr($args['label_for']); ?>"
        name="<?= WPB_ARRAY_OPTIONS_KEY; ?>[<?= esc_attr($args['label_for']); ?>]"
        value="<?= isset($options[$args['label_for']]) ? esc_attr($options[$args['label_for']]) : ''; ?>">
    <?php

    if( isset($args['wpb_description']) ) {
        wpb_settings_field_description($args['wpb_description']);
    }
}
/**
 * Outputs settings text-area field
 *
 * @param array $args
 */
function wpb_settings_textarea_field_cb($args)
{
    // get the value of the setting we've registered with register_setting()
    $options = get_option( WPB_ARRAY_OPTIONS_KEY );

    // output the field
    ?>
    <textarea
        class="regular-text"
        id="<?= esc_attr($args['label_for']); ?>"
        name="<?= WPB_ARRAY_OPTIONS_KEY; ?>[<?= esc_attr($args['label_for']); ?>]"
        <?= isset($args['rows']) ? 'rows="'.esc_attr($args['rows']).'"' : ''; ?>
    ><?= isset($options[$args['label_for']]) ? esc_attr($options[$args['label_for']]) : ''; ?></textarea>
    <?php

    if( isset($args['wpb_description']) ) {
        wpb_settings_field_description($args['wpb_description']);
    }
}
/**
 * Outputs settings select field
 *
 * @param array $args
 */
function wpb_settings_select_field_cb($args)
{
    // get the value of the setting we've registered with register_setting()
    $options = get_option( WPB_ARRAY_OPTIONS_KEY );

    // output the field
    ?>
    <select id="<?= esc_attr($args['label_for']); ?>"
            <?php if(isset($args['wpb_custom_data'])): ?>
            data-custom="<?= esc_attr($args['wpb_custom_data']); ?>"
            <?php endif; ?>
            name="<?= WPB_ARRAY_OPTIONS_KEY; ?>[<?= esc_attr($args['label_for']); ?>]"
    >
    <?php foreach ($args['wpb_options'] as $option): ?>
        <option value="<?= $option[0]; ?>" <?= isset($options[$args['label_for']]) ? (selected($options[$args['label_for']], $option[0], false)) : (''); ?>>
            <?= $option[1]; ?>
        </option>
    <?php endforeach; ?>
    </select>
    <?php

    if( isset($args['wpb_description']) ) {
        wpb_settings_field_description($args['wpb_description']);
    }
}
/**
 * Outputs settings checkbox field
 *
 * @param array $args
 */
function wpb_settings_checkbox_field_cb($args)
{
    // get the value of the setting we've registered with register_setting()
    $options = get_option( WPB_ARRAY_OPTIONS_KEY );

    // output the field
    ?>
    <input type="checkbox"
        id="<?= esc_attr($args['label_for']); ?>"
        <?php if(isset($args['wpb_custom_data'])): ?>
        data-custom="<?= esc_attr($args['wpb_custom_data']); ?>"
        <?php endif; ?>
        name="<?= WPB_ARRAY_OPTIONS_KEY; ?>[<?= esc_attr($args['label_for']); ?>]"
        value="1"
        <?= isset($options[$args['label_for']]) ? (checked(1, $options[$args['label_for']], false)) : (''); ?>
    >
    <label for="<?= esc_attr($args['label_for']); ?>"><?= $args['wpb_label']; ?></label>
    <?php

    if( isset($args['wpb_description']) ) {
        wpb_settings_field_description($args['wpb_description']);
    }
}
