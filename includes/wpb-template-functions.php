<?php

defined( 'ABSPATH' ) or die( 'Busted!' );



/**
 * Outputs settings section for payload
 *
 * @param array $args
 */

function wpb_settings_section_payload_cb($args)
{
    ?>
    <p id="<?= esc_attr($args['id']); ?>"><?= esc_html__('Configure the payload to be sent to the apps.', WPB_TXTDMN); ?></p>
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

    // output the field
    ?>
    <input type="text"
        id="<?= esc_attr($args['label_for']); ?>"
        name="<?= WPB_ARRAY_OPTIONS_KEY; ?>[<?= esc_attr($args['label_for']); ?>]"
        value="<?= isset($options[$args['label_for']]) ? esc_attr($options[$args['label_for']]) : ''; ?>">
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
