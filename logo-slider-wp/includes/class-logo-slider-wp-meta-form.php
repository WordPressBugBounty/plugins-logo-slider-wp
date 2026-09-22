<?php
if (! defined('ABSPATH')) {
    exit;
}

/*************************************
 * 
 * Meta Fields By LogicHunt
 * Developed By: Vaskar Jewel
 * Update Date: 17.04.26
 * Version:1.0.0
 * 
 **************************************/


if (!class_exists('Lgx_Meta_Form')) {
    class Lgx_Meta_Form
    {



    /**
     * @param array $args
     *
     *  id, name, label, value, default
     */

    public function text(array $args)
    {
        global $post;

        if (empty($args['id']) || empty($args['name']) || empty($args['label'])) {
            return;
        }

        $status   = $args['status'] ?? '';
        $is_pro_lock = class_exists('Lgx_Pro_Locking') ? Lgx_Pro_Locking::is_field_locked($args['id'], $status) : false;
        $disabled = ($status === 'disabled') ? 'disabled' : '';
        $pro_input_attr = $is_pro_lock ? 'data-pro-locked-input="true"' : '';

        $is_pro_label = $is_pro_lock
            ? '<span class="lgx-pro-locked-indicator dashicons dashicons-lock" aria-hidden="true" title="' . esc_attr__('Available in Logo Slider Pro', 'logo-slider-wp') . '"></span>'
            : '';

        $pro_css_class = $is_pro_lock ? ' lgx-pro-locked-field' : '';

        $meta = get_post_meta(absint($post->ID), '_lgx_lsp_shortcodes_meta', true);

        // Get value safely
        $raw_value = $meta[$args['id']] ?? ($args['default'] ?? '');

        // Escape for input field
        $value_safe = esc_attr($raw_value);

        $is_modern = (isset($args['layout']) && $args['layout'] === 'modern');

        // Output start
        if ($is_modern) {
            $output_safe  = '<div class="lgx-field lgx-field--row' . $pro_css_class . '">';
            $output_safe .= '<div class="lgx-field__label">';
            $output_safe .= '<label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label>';
            if (!empty($args['desc'])) {
                $output_safe .= '<button type="button" class="dashicons dashicons-info lgx-builder-help-icon" aria-label="' . esc_attr__( 'More information', 'logo-slider-wp' ) . '" aria-expanded="false" aria-controls="lgx-help-popover" data-help="' . esc_attr( $args['desc'] ) . '"></button>';
            }
            $output_safe .= '</div>';
            $output_safe .= '<div class="lgx-field__control">';
            $output_safe .= '<input type="text" value="' . $value_safe . '" placeholder="' . $value_safe . '" id="' . esc_attr($args['id']) . '" class="lgx-input lgx_app_meta_filed" name="' . esc_attr($args['name']) . '" ' . ($is_pro_lock ? 'readonly' : $disabled) . ' ' . $pro_input_attr . '>';
            $output_safe .= $is_pro_label;
            $output_safe .= '</div></div>';
        } else {
            $output_safe  = '<tr class="' . esc_attr(trim($pro_css_class)) . '">';
            $output_safe .= '<th scope="row">';
            $output_safe .= '<h4 class="lgx_app_meta_label">';
            $output_safe .= '<label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label>';
            $output_safe .= '</h4>';
            $output_safe .= '<p class="lgx_input_desc lgx_app_meta_desc">' . esc_html($args['desc'] ?? '') . '</p>';
            $output_safe .= '</th>';
            $output_safe .= '<td>';
            $output_safe .= '<input type="text"
                    value="' . $value_safe . '"
                    placeholder="' . $value_safe . '"
                    id="' . esc_attr($args['id']) . '"
                    class="lgx_input_width lgx_app_meta_filed lgx_app_meta_text"
                    name="' . esc_attr($args['name']) . '"
                    ' . ($is_pro_lock ? 'readonly' : $disabled) . ' ' . $pro_input_attr . '>';
            $output_safe .= $is_pro_label;
            $output_safe .= '</td>';
            $output_safe .= '</tr>';
        }

        // Safe output (NO wp_kses, NO force_balance_tags)
        echo $output_safe;
    }


    public function number(array $args)
    {
        global $post;

        if (empty($args['id']) || empty($args['name']) || empty($args['label'])) {
            return;
        }

        $status   = $args['status'] ?? '';
        $is_pro_lock = class_exists('Lgx_Pro_Locking') ? Lgx_Pro_Locking::is_field_locked($args['id'], $status) : false;
        $disabled = ($status === 'disabled') ? 'disabled' : '';
        $pro_input_attr = $is_pro_lock ? 'data-pro-locked-input="true"' : '';

        $is_pro_label = $is_pro_lock
            ? '<span class="lgx-pro-locked-indicator dashicons dashicons-lock" aria-hidden="true" title="' . esc_attr__('Available in Logo Slider Pro', 'logo-slider-wp') . '"></span>'
            : '';

        $pro_css_class = $is_pro_lock ? ' lgx-pro-locked-field' : '';

        $meta = get_post_meta(absint($post->ID), '_lgx_lsp_shortcodes_meta', true);

        $raw_value = $meta[$args['id']] ?? ($args['default'] ?? '');
        $value     = is_numeric($raw_value) ? $raw_value : '';
        $value_safe = esc_attr($value);

        $is_modern = (isset($args['layout']) && $args['layout'] === 'modern');

        if ($is_modern) {
            $output_safe  = '<div class="lgx-field lgx-field--row' . $pro_css_class . '">';
            $output_safe .= '<div class="lgx-field__label">';
            $output_safe .= '<label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label>';
            if (!empty($args['desc'])) {
                $output_safe .= '<button type="button" class="dashicons dashicons-info lgx-builder-help-icon" aria-label="' . esc_attr__( 'More information', 'logo-slider-wp' ) . '" aria-expanded="false" aria-controls="lgx-help-popover" data-help="' . esc_attr( $args['desc'] ) . '"></button>';
            }
            $output_safe .= '</div>';
            $output_safe .= '<div class="lgx-field__control">';
            $output_safe .= '<input type="number" value="' . $value_safe . '" id="' . esc_attr($args['id']) . '" class="lgx-input lgx_app_meta_filed" name="' . esc_attr($args['name']) . '" ' . ($is_pro_lock ? 'readonly' : $disabled) . ' ' . $pro_input_attr . '>';
            $output_safe .= $is_pro_label;
            $output_safe .= '</div></div>';
        } else {
            $output_safe  = '<tr class="' . esc_attr(trim($pro_css_class)) . '">';
            $output_safe .= '<th scope="row">';
            $output_safe .= '<h4 class="lgx_app_meta_label"><label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label></h4>';
            $output_safe .= '<p class="lgx_input_desc lgx_app_meta_desc">' . esc_html($args['desc'] ?? '') . '</p>';
            $output_safe .= '</th>';
            $output_safe .= '<td>';
            $output_safe .= '<input type="number" value="' . $value_safe . '" id="' . esc_attr($args['id']) . '" class="lgx_input_width lgx_app_meta_filed lgx_app_meta_number" name="' . esc_attr($args['name']) . '" ' . ($is_pro_lock ? 'readonly' : $disabled) . ' ' . $pro_input_attr . '>';
            $output_safe .= $is_pro_label;
            $output_safe .= '</td></tr>';
        }

        echo $output_safe;
    }



    /**
     * @param array $args
     */


    public function url(array $args)
    {
        global $post;

        if (empty($args['id']) || empty($args['name']) || empty($args['label'])) {
            return;
        }

        $status   = $args['status'] ?? '';
        $is_pro_lock = class_exists('Lgx_Pro_Locking') ? Lgx_Pro_Locking::is_field_locked($args['id'], $status) : false;
        $disabled = ($status === 'disabled') ? 'disabled' : '';
        $pro_input_attr = $is_pro_lock ? 'data-pro-locked-input="true"' : '';

        $is_pro_label = $is_pro_lock
            ? '<span class="lgx-pro-locked-indicator dashicons dashicons-lock" aria-hidden="true" title="' . esc_attr__('Available in Logo Slider Pro', 'logo-slider-wp') . '"></span>'
            : '';

        $pro_css_class = $is_pro_lock ? ' lgx-pro-locked-field' : '';

        $meta = get_post_meta(absint($post->ID), '_lgx_lsp_shortcodes_meta', true);

        $raw_value = $meta[$args['id']] ?? ($args['default'] ?? '');
        $value_safe = esc_url($raw_value);

        $is_modern = (isset($args['layout']) && $args['layout'] === 'modern');

        if ($is_modern) {
            $output_safe  = '<div class="lgx-field lgx-field--row' . $pro_css_class . '">';
            $output_safe .= '<div class="lgx-field__label">';
            $output_safe .= '<label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label>';
            if (!empty($args['desc'])) {
                $output_safe .= '<button type="button" class="dashicons dashicons-info lgx-builder-help-icon" aria-label="' . esc_attr__( 'More information', 'logo-slider-wp' ) . '" aria-expanded="false" aria-controls="lgx-help-popover" data-help="' . esc_attr( $args['desc'] ) . '"></button>';
            }
            $output_safe .= '</div>';
            $output_safe .= '<div class="lgx-field__control">';
            $output_safe .= '<input type="url" value="' . $value_safe . '" id="' . esc_attr($args['id']) . '" class="lgx-input lgx_app_meta_filed" name="' . esc_attr($args['name']) . '" ' . ($is_pro_lock ? 'readonly' : $disabled) . ' ' . $pro_input_attr . '>';
            $output_safe .= $is_pro_label;
            $output_safe .= '</div></div>';
        } else {
            $output_safe  = '<tr class="' . esc_attr(trim($pro_css_class)) . '">';
            $output_safe .= '<th scope="row">';
            $output_safe .= '<h4 class="lgx_app_meta_label"><label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label></h4>';
            $output_safe .= '<p class="lgx_input_desc lgx_app_meta_desc">' . esc_html($args['desc'] ?? '') . '</p>';
            $output_safe .= '</th>';
            $output_safe .= '<td>';
            $output_safe .= '<input type="url" value="' . $value_safe . '" id="' . esc_attr($args['id']) . '" class="lgx_input_width lgx_app_meta_filed lgx_app_meta_url" name="' . esc_attr($args['name']) . '" ' . ($is_pro_lock ? 'readonly' : $disabled) . ' ' . $pro_input_attr . '>';
            $output_safe .= $is_pro_label;
            $output_safe .= '</td></tr>';
        }

        echo $output_safe;
    }




    public function textarea(array $args)
    {
        global $post;

        if (empty($args['id']) || empty($args['name']) || empty($args['label'])) {
            return;
        }

        $status   = $args['status'] ?? '';
        $is_pro_lock = class_exists('Lgx_Pro_Locking') ? Lgx_Pro_Locking::is_field_locked($args['id'], $status) : false;
        $disabled = ($status === 'disabled') ? 'disabled' : '';
        $placeholder = esc_attr($args['placeholder'] ?? '');
        $pro_input_attr = $is_pro_lock ? 'data-pro-locked-input="true"' : '';

        $is_pro_label = $is_pro_lock
            ? '<span class="lgx-pro-locked-indicator dashicons dashicons-lock" aria-hidden="true" title="' . esc_attr__('Available in Logo Slider Pro', 'logo-slider-wp') . '"></span>'
            : (($status === 'disabled') ? '<span class="lgx_meta_field_mark_pro_wrap"><span class="lgx_meta_field_mark">' . esc_html__('Pro', 'logo-slider-wp') . '</span></span>' : '');

        $pro_css_class = $is_pro_lock ? ' lgx-pro-locked-field' : '';

        $meta = get_post_meta(absint($post->ID), '_lgx_lsp_shortcodes_meta', true);

        $raw_value  = $meta[$args['id']] ?? ($args['default'] ?? '');
        $value_safe = esc_textarea($raw_value);


        $is_modern = (isset($args['layout']) && $args['layout'] === 'modern');

        if ($is_modern) {
            $output_safe  = '<div class="lgx-field' . $pro_css_class . '">';
            $output_safe .= '<div class="lgx-field__label">';
            $output_safe .= '<label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label>';
            if (!empty($args['desc'])) {
                $output_safe .= '<button type="button" class="dashicons dashicons-info lgx-builder-help-icon" aria-label="' . esc_attr__( 'More information', 'logo-slider-wp' ) . '" aria-expanded="false" aria-controls="lgx-help-popover" data-help="' . esc_attr( $args['desc'] ) . '"></button>';
            }
            $output_safe .= '</div>';
            $output_safe .= '<div class="lgx-field__control">';
            $output_safe .= '<textarea id="' . esc_attr($args['id']) . '" class="lgx-input lgx_app_meta_filed" name="' . esc_attr($args['name']) . '" placeholder="' . $placeholder . '" ' . ($is_pro_lock ? 'readonly' : $disabled) . ' ' . $pro_input_attr . '>' . $value_safe . '</textarea>';
            $output_safe .= $is_pro_label;
            $output_safe .= '</div></div>';
        } else {
            $output_safe  = '<tr class="' . esc_attr(trim($pro_css_class)) . '">';
            $output_safe .= '<th scope="row">';
            $output_safe .= '<h4 class="lgx_app_meta_label"><label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label></h4>';
            $output_safe .= '<p class="lgx_input_desc lgx_app_meta_desc">' . esc_html($args['desc'] ?? '') . '</p>';
            $output_safe .= '</th>';
            $output_safe .= '<td>';
            $output_safe .= '<textarea 
        id="' . esc_attr($args['id']) . '" 
        class="lgx_input_width lgx_app_meta_filed lgx_app_meta_textarea" 
        name="' . esc_attr($args['name']) . '" 
        placeholder="' . $placeholder . '" 
        ' . ($is_pro_lock ? 'readonly' : $disabled) . ' ' . $pro_input_attr . '>' . $value_safe . '</textarea>';
            $output_safe .= $is_pro_label;
            $output_safe .= '</td></tr>';
        }

        echo $output_safe;
    }


    public function checkbox(array $args)
    {
        global $post;

        if (empty($args['id']) || empty($args['name']) || empty($args['label'])) {
            return;
        }

        $status   = $args['status'] ?? '';
        $is_pro_lock = class_exists('Lgx_Pro_Locking') ? Lgx_Pro_Locking::is_field_locked($args['id'], $status) : false;
        $disabled = ($status === 'disabled') ? 'disabled' : '';
        $pro_input_attr = $is_pro_lock ? 'data-pro-locked-input="true"' : '';

        $is_pro_label = $is_pro_lock
            ? '<span class="lgx-pro-locked-indicator dashicons dashicons-lock" aria-hidden="true" title="' . esc_attr__('Available in Logo Slider Pro', 'logo-slider-wp') . '"></span>'
            : '';

        $pro_css_class = $is_pro_lock ? ' lgx-pro-locked-field' : '';

        $meta = get_post_meta(absint($post->ID), '_lgx_lsp_shortcodes_meta', true);
        $value = $meta[$args['id']] ?? ($args['default'] ?? '');

        $is_modern = (isset($args['layout']) && $args['layout'] === 'modern');

        if ($is_modern) {
            $output_safe  = '<div class="lgx-field lgx-field--inline' . $pro_css_class . '">';
            $output_safe .= '<div class="lgx-field__control">';
            $output_safe .= '<input type="checkbox" value="yes" id="' . esc_attr($args['id']) . '" class="lgx_app_meta_filed" name="' . esc_attr($args['name']) . '" ' . checked($value, 'yes', false) . ' ' . $disabled . ' ' . $pro_input_attr . '>';
            $output_safe .= '</div>';
            $output_safe .= '<div class="lgx-field__label">';
            $output_safe .= '<label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label>';
            if (!empty($args['desc'])) {
                $output_safe .= '<button type="button" class="dashicons dashicons-info lgx-builder-help-icon" aria-label="' . esc_attr__( 'More information', 'logo-slider-wp' ) . '" aria-expanded="false" aria-controls="lgx-help-popover" data-help="' . esc_attr( $args['desc'] ) . '"></button>';
            }
            $output_safe .= $is_pro_label;
            $output_safe .= '</div></div>';
        } else {
            $output_safe  = '<tr class="' . esc_attr(trim($pro_css_class)) . '">';
            $output_safe .= '<th scope="row">';
            $output_safe .= '<h4 class="lgx_app_meta_label"><label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label></h4>';
            $output_safe .= '<p class="lgx_input_desc lgx_app_meta_desc">' . esc_html($args['desc'] ?? '') . '</p>';
            $output_safe .= '</th>';
            $output_safe .= '<td>';
            $output_safe .= '<input type="checkbox" value="yes" id="' . esc_attr($args['id']) . '" class="lgx_app_meta_filed lgx_app_meta_checkbox" name="' . esc_attr($args['name']) . '" ' . checked($value, 'yes', false) . ' ' . $disabled . ' ' . $pro_input_attr . '>';
            $output_safe .= $is_pro_label;
            $output_safe .= '</td></tr>';
        }

        echo $output_safe;
    }


    /****  Select */


    public function select(array $args)
    {
        global $post;

        if (empty($args['id']) || empty($args['name']) || empty($args['label'])) {
            return;
        }

        if (empty($args['options']) || ! is_array($args['options'])) {
            return;
        }

        $status   = $args['status'] ?? '';
        $is_pro   = ($status === 'disabled') ? 'disabled' : '';
        $is_pro_lock = class_exists('Lgx_Pro_Locking') ? Lgx_Pro_Locking::is_field_locked($args['id'], $status) : false;
        // select() renders its Pro treatment through option-level data-pro-locked-option
        // only — never the whole-field lock/wrapper — so Model A's exempt option stays
        // clickable and the dropdown itself is never blocked (§3.2). B60: the field-level
        // lock icon below deliberately reuses only .lgx-pro-locked-indicator (icon styling),
        // never the .lgx-pro-locked-field wrapper class the other 10 field types use — that
        // wrapper dims the whole control to 60% opacity and is what initProFieldLocking()'s
        // JS matches to intercept clicks, both wrong here since the dropdown stays genuinely
        // usable under Model A, not inert.
        $pro_input_attr = '';
        $is_pro_label = $is_pro_lock
            ? '<span class="lgx-pro-locked-indicator dashicons dashicons-lock" aria-hidden="true" title="' . esc_attr__('Available in Logo Slider Pro', 'logo-slider-wp') . '"></span>'
            : '';
        $pro_css_class = '';

        $meta = get_post_meta(absint($post->ID), '_lgx_lsp_shortcodes_meta', true);

        $default_value = $args['default'] ?? '';
        $meta_value    = $meta[$args['id']] ?? $default_value;

        $is_modern = (isset($args['layout']) && $args['layout'] === 'modern');

        // Model A "exempt value" fallback (§3.3): if the saved value doesn't match any
        // real option key, fall back to exempting the default instead — never let a
        // stale/corrupt saved value lock every option with no escape.
        $exempt_value = array_key_exists($meta_value, $args['options']) ? $meta_value : $default_value;

        // Options
        $options_safe = '';
        foreach ($args['options'] as $option_value => $option_text) {
            $selected = selected($option_value, $meta_value, false);

            $is_option_pro = class_exists('Lgx_Pro_Locking')
                ? Lgx_Pro_Locking::is_option_locked($args['id'], $option_value, $status, $exempt_value, false, $args['free_options'] ?? array())
                : false;

            $disabled_attr = $is_option_pro ? 'disabled="disabled" data-pro-locked-option="true"' : '';
            $label = esc_html($option_text);
            $options_safe .= '<option value="' . esc_attr($option_value) . '" ' . $selected . ' ' . $disabled_attr . '>' . $label . '</option>';
        }

        $is_multiple = (isset($args['multiple']) && $args['multiple'] === 'yes') ? 'multiple' : '';

        if ($is_modern) {
            $output_safe  = '<div class="lgx-field lgx-field--row' . $pro_css_class . '">';
            $output_safe .= '<div class="lgx-field__label">';
            $output_safe .= '<label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label>';
            if (!empty($args['desc'])) {
                $output_safe .= '<button type="button" class="dashicons dashicons-info lgx-builder-help-icon" aria-label="' . esc_attr__( 'More information', 'logo-slider-wp' ) . '" aria-expanded="false" aria-controls="lgx-help-popover" data-help="' . esc_attr( $args['desc'] ) . '"></button>';
            }
            $output_safe .= '</div>';
            $output_safe .= '<div class="lgx-field__control">';
            $output_safe .= '<select name="' . esc_attr($args['name']) . '" class="lgx-input lgx_app_meta_filed" id="' . esc_attr($args['id']) . '" ' . $is_multiple . ' ' . $pro_input_attr . '>';
            $output_safe .= $options_safe;
            $output_safe .= '</select>';
            $output_safe .= $is_pro_label;
            $output_safe .= '</div></div>';
        } else {
            $output_safe  = '<tr class="' . esc_attr(trim($pro_css_class)) . '">';
            $output_safe .= '<th scope="row">';
            $output_safe .= '<h4 class="lgx_app_meta_label"><label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label></h4>';
            $output_safe .= '<p class="lgx_input_desc lgx_app_meta_desc">' . esc_html($args['desc'] ?? '') . '</p>';
            $output_safe .= '</th>';
            $output_safe .= '<td>';
            $output_safe .= '<select name="' . esc_attr($args['name']) . '" class="lgx_input_width lgx_app_meta_filed lgx_app_meta_select ' . ($is_pro === 'disabled' ? 'lgx_app_meta_select_disabled' : '') . '" id="' . esc_attr($args['id']) . '" ' . $is_multiple . ' ' . $pro_input_attr . '>';
            $output_safe .= $options_safe;
            $output_safe .= '</select>';
            $output_safe .= '</td></tr>';
        }

        echo $output_safe;
    }

    /**
     * @param array $args
     *
     *  id, name, label, value, default, status
     */

    public function switch(array $args)
    {
        global $post;

        if (! isset($args['id'], $args['name'], $args['label'])) {
            return;
        }

        $yes_label = $args['yes_label'] ?? __('ON', 'logo-slider-wp');
        $no_label  = $args['no_label'] ?? __('OFF', 'logo-slider-wp');
        $status    = $args['status'] ?? '';
        $is_pro_lock = class_exists('Lgx_Pro_Locking') ? Lgx_Pro_Locking::is_field_locked($args['id'], $status) : false;
        $disabled  = ($status === 'disabled') ? 'disabled' : '';
        $pro_input_attr = $is_pro_lock ? 'data-pro-locked-input="true"' : '';
        
        $pro_class = ($status === 'disabled' || $is_pro_lock) ? 'lgx_switch_pro' : '';

        $is_pro_label = $is_pro_lock
            ? '<span class="lgx-pro-locked-indicator dashicons dashicons-lock" aria-hidden="true" title="' . esc_attr__('Available in Logo Slider Pro', 'logo-slider-wp') . '"></span>'
            : '';
            
        $pro_css_class = $is_pro_lock ? ' lgx-pro-locked-field' : '';

        $meta = get_post_meta(absint($post->ID), '_lgx_lsp_shortcodes_meta', true);
        $meta_value = $meta[$args['id']] ?? ($args['default'] ?? '');

        //$is_checked = ( $meta_value == 'yes') ? 'checked' : '';


        $is_modern = (isset($args['layout']) && $args['layout'] === 'modern');

        if ($is_modern) {
            $aria_checked = ($meta_value === 'yes') ? 'true' : 'false';

            $output_safe  = '<div class="lgx-field lgx-field--row lgx-field--inline' . $pro_css_class . '">';

            $output_safe .= '<div class="lgx-field__label">';
            $output_safe .= '<label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label>';
            if (!empty($args['desc'])) {
                $output_safe .= '<button type="button" class="dashicons dashicons-info lgx-builder-help-icon" aria-label="' . esc_attr__( 'More information', 'logo-slider-wp' ) . '" aria-expanded="false" aria-controls="lgx-help-popover" data-help="' . esc_attr( $args['desc'] ) . '"></button>';
            }
            $output_safe .= '</div>';

            $output_safe .= '<div class="lgx-field__control">';
            $output_safe .= '<label class="lgx_switch ' . esc_attr($pro_class) . '">';
            $output_safe .= '<input type="checkbox" role="switch" aria-checked="' . $aria_checked . '" value="yes" id="' . esc_attr($args['id']) . '" class="lgx_app_meta_filed lgx_app_meta_checkbox" name="' . esc_attr($args['name']) . '" ' . checked($meta_value, 'yes', false) . ' ' . $disabled . ' ' . $pro_input_attr . '>';
            $output_safe .= '<div class="lgx_switch_slider lgx_switch_round">';
            $output_safe .= '<span class="lgx_switch_on">' . esc_html($yes_label) . '</span>';
            $output_safe .= '<span class="lgx_switch_off">' . esc_html($no_label) . '</span>';
            $output_safe .= '</div></label>';
            $output_safe .= $is_pro_label;
            $output_safe .= '</div></div>';
        } else {
            $output_safe = '<tr class="' . esc_attr(trim($pro_css_class)) . '">';
            $output_safe .= '<th scope="row">';
            $output_safe .= '<h4 class="lgx_app_meta_label"><label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label></h4>';
            $output_safe .= '<p class="lgx_input_desc lgx_app_meta_desc">' . esc_html($args['desc'] ?? '') . '</p>';
            $output_safe .= '</th>';

            $output_safe .= '<td>';
            $output_safe .= '<label class="lgx_switch ' . esc_attr($pro_class) . '">';

            $output_safe .= '<input type="checkbox" value="yes"
            id="' . esc_attr($args['id']) . '"
            class="lgx_app_meta_filed lgx_app_meta_checkbox" 
            name="' . esc_attr($args['name']) . '"
            ' . checked($meta_value, 'yes', false) . '
            ' . $disabled . ' ' . $pro_input_attr . '>';

            $output_safe .= ' <div class="lgx_switch_slider lgx_switch_round">';
            $output_safe .= '<span class="lgx_switch_on">' . esc_html($yes_label) . '</span>';
            $output_safe .= '<span class="lgx_switch_off">' . esc_html($no_label) . '</span>';
            $output_safe .= '</div>';
            $output_safe .= '</label>';
            $output_safe .= $is_pro_label;
            $output_safe .= '</td>';

            $output_safe .= '</tr>';
        }

        echo $output_safe;
    }




    public function upload(array $args)
    {

        global $post;

        if (empty($args['id']) || empty($args['name']) || empty($args['label'])) {
            return;
        }

        $status   = $args['status'] ?? '';
        $is_pro_lock = class_exists('Lgx_Pro_Locking') ? Lgx_Pro_Locking::is_field_locked($args['id'], $status) : false;
        $is_pro   = ($status === 'disabled') ? 'disabled' : '';
        $pro_input_attr = $is_pro_lock ? 'data-pro-locked-input="true"' : '';

        $is_pro_label = $is_pro_lock
            ? '<span class="lgx-pro-locked-indicator dashicons dashicons-lock" aria-hidden="true" title="' . esc_attr__('Available in Logo Slider Pro', 'logo-slider-wp') . '"></span>'
            : '';

        $pro_css_class = $is_pro_lock ? ' lgx-pro-locked-field' : '';

        $meta = get_post_meta(absint($post->ID), '_lgx_lsp_shortcodes_meta', true);

        $default_value = $args['default'] ?? '';
        $raw_value     = $meta[$args['id']] ?? $default_value;

        // ✅ IMPORTANT: URL must be escaped properly
        $value_safe = esc_url($raw_value);

        $is_modern = (isset($args['layout']) && $args['layout'] === 'modern');

        if ($is_modern) {
            $output_safe  = '<div class="lgx-field lgx-field--row' . $pro_css_class . '">';
            $output_safe .= '<div class="lgx-field__label">';
            $output_safe .= '<label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label>';
            if (!empty($args['desc'])) {
                $output_safe .= '<button type="button" class="dashicons dashicons-info lgx-builder-help-icon" aria-label="' . esc_attr__( 'More information', 'logo-slider-wp' ) . '" aria-expanded="false" aria-controls="lgx-help-popover" data-help="' . esc_attr( $args['desc'] ) . '"></button>';
            }
            $output_safe .= '</div>';
            $has_value = ! empty( $raw_value );
            $output_safe .= '<div class="lgx-field__control lgx-field__control--media">';
            $output_safe .= '<button type="button" class="button lgx-btn lgx-btn-secondary lgx_icon_image_button" ' . $pro_input_attr . ' data-icon-field-name="' . esc_attr($args['name']) . '" data-icon-img-id="' . esc_attr($args['id']) . '"><i class="lgxicon lgx-icon-file-photo-o"></i> ' . esc_html__('Select Icon', 'logo-slider-wp') . '</button>';
            // Visual Consistency Pass: a fixed-size, object-fit preview box
            // replaces the old bare <img style="width:24px"> (which squished
            // any non-square image) and the separate "Remove Icon" text
            // button. The box - not just the <img> - is hidden when there is
            // no value, and the <img> has no src attribute at all in that
            // case, so an empty field never renders a literal <img src="">
            // (which browsers resolve to the current page's own URL and try
            // to load as a broken image).
            $img_src_attr = $has_value ? ' src="' . $value_safe . '"' : '';
            $output_safe .= '<div class="lgx-upload-preview"' . ( $has_value ? '' : ' style="display:none;"' ) . '>';
            $output_safe .= '<img' . $img_src_attr . ' alt="" id="' . esc_attr($args['id']) . '" class="lgx-upload-preview__img">';
            $output_safe .= '<button type="button" class="lgx-upload-preview__remove lgx_icon_image_button_clear" aria-label="' . esc_attr__( 'Remove image', 'logo-slider-wp' ) . '" data-icon-field-name="' . esc_attr($args['name']) . '" data-icon-img-id="' . esc_attr($args['id']) . '">&times;</button>';
            $output_safe .= '</div>';
            $output_safe .= '<input type="hidden" value="' . $value_safe . '" name="' . esc_attr($args['name']) . '" ' . $is_pro . '>';
            $output_safe .= $is_pro_label;
            $output_safe .= '</div></div>';
        } else {
            $output_safe  = '<tr class="' . esc_attr(trim($pro_css_class)) . '">';
            $output_safe .= '<th scope="row">';
            $output_safe .= '<h4 class="lgx_app_meta_label"><label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label></h4>';
            $output_safe .= '<p class="lgx_input_desc lgx_app_meta_desc">' . esc_html($args['desc'] ?? '') . '</p>';
            $output_safe .= '</th>';

            $output_safe .= '<td>';
            $output_safe .= '<button type="button" class="button button-large lgx_icon_image_button" ' . $pro_input_attr . ' data-icon-field-name="' . esc_attr($args['name']) . '" data-icon-img-id="' . esc_attr($args['id']) . '"><i class="lgxicon lgx-icon-file-photo-o"></i> ' . esc_html__('Select Icon', 'logo-slider-wp') . '</button>';
            $output_safe .= '&nbsp;&nbsp;&nbsp;';
            $output_safe .= '<button type="button" class="button button-large lgx_icon_image_button_clear" data-icon-field-name="' . esc_attr($args['name']) . '" data-icon-img-id="' . esc_attr($args['id']) . '"><i class="lgxicon lgx-icon-remove"></i> ' . esc_html__('Remove Icon', 'logo-slider-wp') . '</button>';
            $output_safe .= '&nbsp;&nbsp;&nbsp;';
            $output_safe .= '<img src="' . $value_safe . '" alt="" id="' . esc_attr($args['id']) . '" style="width: 24px; margin-top: 5px;">';
            $output_safe .= '<input type="hidden" value="' . $value_safe . '" name="' . esc_attr($args['name']) . '" ' . $is_pro . '>';
            $output_safe .= $is_pro_label;
            $output_safe .= '</td>';
            $output_safe .= '</tr>';
        }

        echo $output_safe;
    }


    public function group2SelectText(array $args)
    {
        global $post;

        if (empty($args['id']) || empty($args['name']) || empty($args['label'])) {
            return;
        }

        if (empty($args['options_select']) || ! is_array($args['options_select'])) {
            return;
        }

        $status   = $args['status'] ?? '';
        $is_pro_lock = class_exists('Lgx_Pro_Locking') ? Lgx_Pro_Locking::is_field_locked($args['id'] ?? $args['id_select'], $status) : false;
        $is_pro   = ($status === 'disabled') ? 'disabled' : '';
        // B60/Model A convention (matches select()): 'pro' status on this
        // field type means "the Properties dropdown has some Pro-gated
        // options," never "the whole control is locked" -- neither control
        // gets a data-pro-locked-input attribute or readonly treatment, and
        // $pro_css_class stays empty, even though $is_pro_lock (still
        // computed the same way) is used below only to decide whether to
        // show the small lock icon. Per-option protection (data-pro-locked-
        // option below) is what actually guards Width's Pro-only options.
        $is_pro_label = $is_pro_lock
            ? '<span class="lgx-pro-locked-indicator dashicons dashicons-lock" aria-hidden="true" title="' . esc_attr__('Available in Logo Slider Pro', 'logo-slider-wp') . '"></span>'
            : '';

        $pro_css_class = '';

        $meta = get_post_meta(absint($post->ID), '_lgx_lsp_shortcodes_meta', true);

        /*
     * SELECT VALUE
     */
        $default_select = $args['default_select'] ?? '';
        $meta_value     = $meta[$args['id_select']] ?? $default_select;

        /*
     * TEXT VALUE
     */
        $default_text     = $args['default_text'] ?? '';
        $raw_text_value   = $meta[$args['id_text']] ?? $default_text;
        $text_value_safe  = esc_attr($raw_text_value); // correct for input

        // Model A exempt-value fallback (mirrors select()'s own pattern).
        $exempt_value = array_key_exists($meta_value, $args['options_select']) ? $meta_value : $default_select;

        $is_modern = (isset($args['layout']) && $args['layout'] === 'modern');

        /*
     * OUTPUT START
     */
        if ($is_modern) {
            $output_safe  = '<fieldset class="lgx-field' . $pro_css_class . '">';
            $output_safe .= '<legend class="lgx-field__label">';
            $output_safe .= esc_html($args['label']);
            if (!empty($args['desc'])) {
                $output_safe .= '<button type="button" class="dashicons dashicons-info lgx-builder-help-icon" aria-label="' . esc_attr__( 'More information', 'logo-slider-wp' ) . '" aria-expanded="false" aria-controls="lgx-help-popover" data-help="' . esc_attr( $args['desc'] ) . '"></button>';
            }
            $output_safe .= '</legend>';
            $output_safe .= '<div class="lgx-field__control">';
            $output_safe .= '<div class="lgx_group_field_wrap">';
        } else {
            $output_safe  = '<tr class="' . esc_attr(trim($pro_css_class)) . '">';
            $output_safe .= '<th scope="row">';
            $output_safe .= '<h4 class="lgx_app_meta_label"><label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label></h4>';
            $output_safe .= '<p class="lgx_input_desc lgx_app_meta_desc">' . esc_html($args['desc'] ?? '') . '</p>';
            $output_safe .= '</th>';
            $output_safe .= '<td>';
            $output_safe .= '<div class="lgx_group_field_wrap">';
        }

        /*
     * SELECT FIELD
     */
        $output_safe .= '<div class="lgx_group_field_item">';
        $output_safe .= '<div class="lgx_group_field_label">' . esc_html($args['label_select'] ?? '') . '</div>';

        $options_safe = '';

        foreach ($args['options_select'] as $option_value => $option_text) {

            $selected = selected($option_value, $meta_value, false);

            $disabled_attr = ($is_pro === 'disabled' && $option_value !== $meta_value)
                ? 'disabled="disabled"'
                : '';

            // Model A per-option gating -- same mechanism select() already
            // uses via is_option_locked()/free_options. Only engages when
            // $status is 'pro' and the option isn't already disabled by the
            // 'disabled' status branch above (mutually exclusive statuses).
            if ('' === $disabled_attr && class_exists('Lgx_Pro_Locking')) {
                $is_option_pro = Lgx_Pro_Locking::is_option_locked(
                    $args['id_select'],
                    $option_value,
                    $status,
                    $exempt_value,
                    false,
                    $args['free_options'] ?? array()
                );
                if ($is_option_pro) {
                    $disabled_attr = 'disabled="disabled" data-pro-locked-option="true"';
                }
            }

            $label = esc_html($option_text);

            $options_safe .= '<option value="' . esc_attr($option_value) . '" ' . $selected . ' ' . $disabled_attr . '>' . $label . '</option>';
        }

        $is_multiple = (isset($args['multiple']) && $args['multiple'] === 'yes') ? 'multiple' : '';

        $output_safe .= '<select name="' . esc_attr($args['name_select']) . '" class="lgx_input_width lgx_app_meta_filed lgx_app_meta_select ' . ($is_pro === 'disabled' ? 'lgx_app_meta_select_disabled' : '') . '" id="' . esc_attr($args['id_select']) . '" ' . $is_multiple . '>';

        $output_safe .= $options_safe;
        $output_safe .= '</select>';
        // Icon sits with the Properties dropdown now, not the Value field --
        // it's the dropdown that carries Pro-gated options (Width) or none
        // at all (Height), never the Value field, which is always Free for
        // both fields under this merge.
        $output_safe .= $is_pro_label;
        $output_safe .= '</div>'; // item 1

        /*
     * TEXT FIELD
     */
        $output_safe .= '<div class="lgx_group_field_item">';
        $output_safe .= '<div class="lgx_group_field_label">' . esc_html($args['label_text'] ?? '') . '</div>';

        $placeholder = esc_attr($args['placeholder_text'] ?? $raw_text_value);

        $output_safe .= '<input type="text"
        value="' . $text_value_safe . '"
        placeholder="' . $placeholder . '"
        id="' . esc_attr($args['id_text']) . '"
        class="lgx_input_width lgx_app_meta_filed lgx_app_meta_text"
        name="' . esc_attr($args['name_text']) . '"
        ' . $is_pro . '>';

        $output_safe .= '</div>'; // item 2

        $output_safe .= '</div>'; // wrap

        if ($is_modern) {
            $output_safe .= '</div></fieldset>';
        } else {
            $output_safe .= '</td></tr>';
        }

        echo $output_safe;
    }



    public function buy_pro(array $args)
{
    // Only show when disabled
    if (($args['status'] ?? '') !== 'disabled') {
        return;
    }

    $text       = $args['text']       ?? '✨ To unlock all premium options and enjoy all exclusive features, please';
    $link       = $args['link']       ?? 'https://logichunt.com/';
    $link_label = $args['link_label'] ?? __('Upgrade To Pro', 'logo-slider-wp');

    // Trailing dot only for default flow, not when custom link_label is provided
    $trailing   = isset($args['link_label']) ? '' : ' .';

    $is_modern = (isset($args['layout']) && $args['layout'] === 'modern');

    if ($is_modern) {
        $output_safe  = '<div class="lgx-field" style="margin-bottom: 0;">';
        $output_safe .= '<div class="lgx_app_meta_buy_pro_wrap" style="width: 100%;">';
        if (! empty($args['title'])) {
            $output_safe .= '<h3 class="lgx_app_meta_buy_pro_title">' . esc_html($args['title']) . '</h3>';
        }
        $output_safe .= '<p class="lgx_input_desc lgx_app_meta_buy_pro_desc">'
            . esc_html($text) . ' <a href="' . esc_url($link) . '" target="_blank" rel="noopener noreferrer">'
            . esc_html($link_label) . '</a>' . $trailing . '</p>';
        $output_safe .= '</div></div>';
    } else {
        $output_safe  = '<tr>';
        $output_safe .= '<td class="lgx_app_meta_buy_pro_td" colspan="2"><div class="lgx_app_meta_buy_pro_wrap">';

        if (! empty($args['title'])) {
            $output_safe .= '<h3 class="lgx_app_meta_buy_pro_title">' . esc_html($args['title']) . '</h3>';
        }

        $output_safe .= '<p class="lgx_input_desc lgx_app_meta_buy_pro_desc">'
            . esc_html($text) . ' <a href="' . esc_url($link) . '" target="_blank" rel="noopener noreferrer">'
            . esc_html($link_label) . '</a>' . $trailing . '</p>';

        $output_safe .= '</div></td>';
        $output_safe .= '</tr>';
    }

    echo $output_safe;
}


    public function buy_pro_legacy(array $args)
    {

        // Only show when disabled
        if (($args['status'] ?? '') !== 'disabled') {
            return;
        }

        $text = $args['text'] ?? 'To unlock all premium options and enjoy all exclusive features, please ';
        $link = $args['link'] ?? 'https://logichunt.com/';

        $output_safe  = '<tr>';
        $output_safe .= '<td class="lgx_app_meta_buy_pro_td" colspan="2"><div class="lgx_app_meta_buy_pro_wrap">';

        if (! empty($args['title'])) {
            $output_safe .= '<h3 class="lgx_app_meta_buy_pro_title">' . esc_html($args['title']) . '</h3>';
        }

        $output_safe .= '<p class="lgx_input_desc lgx_app_meta_buy_pro_desc">'
            . esc_html($text) . ' <a href="' . esc_url($link) . '" target="_blank" rel="noopener noreferrer">'
            . esc_html__('Upgrade To Pro', 'logo-slider-wp') . '</a> .</p>';

        $output_safe .= '</div></td>';
        $output_safe .= '</tr>';

        echo $output_safe;
    }

public function header_spacer(array $args)
{
    if (empty($args['label'])) {
        return;
    }

    $label   = $args['label']       ?? '';
    $message = $args['message']     ?? '';
    $type    = $args['type']        ?? 'info'; // info | warning | success

    $allowed_html = array(
        'a'      => array('href' => array(), 'target' => array(), 'rel' => array(), 'style' => array(), 'class' => array()),
        'strong' => array('style' => array(), 'class' => array()),
        'em'     => array('style' => array(), 'class' => array()),
        'b'      => array('style' => array(), 'class' => array()),
        'span'   => array('style' => array(), 'class' => array()),
        'br'     => array(),
        'i'      => array('style' => array(), 'class' => array()),
    );

    $is_modern = (isset($args['layout']) && $args['layout'] === 'modern');

    if ($is_modern) {
        $output_safe  = '<div class="lgx-field" style="margin-top: 15px; margin-bottom: 5px;">';
        $output_safe .= '<div class="lgx_app_meta_header_spacer" style="width: 100%; border-bottom: 1px solid #ddd; padding-bottom: 5px;">';
        $output_safe .= '<h3>' . wp_kses($label, $allowed_html) . '</h3>';
        if (! empty($message)) {
            $output_safe .= '<p class="lgx_app_meta_spacer_notice lgx_app_meta_spacer_notice--' . esc_attr($type) . '" style="margin-top: -10px; margin-bottom: 10px;">'
                . wp_kses($message, $allowed_html)
                . '</p>';
        }
        $output_safe .= '</div></div>';
    } else {
        $output_safe  = '<tr>';
        $output_safe .= '<td colspan="2">';
        $output_safe .= '<div class="lgx_app_meta_header_spacer">';

        // Label — supports HTML if passed as raw, falls back to esc_html for plain string
        $output_safe .= '<h3>' . wp_kses($label, $allowed_html) . '</h3>';

        if (! empty($message)) {
            $output_safe .= '<p class="lgx_app_meta_spacer_notice lgx_app_meta_spacer_notice--' . esc_attr($type) . '" style="margin-top: -10px; margin-bottom: 10px;">'
                . wp_kses($message, $allowed_html)
                . '</p>';
        }

        $output_safe .= '</div>';
        $output_safe .= '</td>';
        $output_safe .= '</tr>';
    }

    echo $output_safe;
}

    public function header_spacer_legacy(array $args)
    {

        if (empty($args['label'])) {
            return;
        }

        $output_safe  = '<tr>';
        $output_safe .= '<td colspan="2">';
        $output_safe .= '<div class="lgx_app_meta_header_spacer"><h3>' . esc_html($args['label']) . '</h3></div>';
        $output_safe .= '</td>';
        $output_safe .= '</tr>';

        echo $output_safe;
    }



    public function color(array $args)
    {
        global $post;

        if (empty($args['id']) || empty($args['name']) || empty($args['label'])) {
            return;
        }

        $status   = $args['status'] ?? '';
        $is_pro_lock = class_exists('Lgx_Pro_Locking') ? Lgx_Pro_Locking::is_field_locked($args['id'], $status) : false;
        $is_pro   = ($status === 'disabled') ? 'disabled' : '';
        $pro_input_attr = $is_pro_lock ? 'data-pro-locked-input="true"' : '';

        $is_pro_label = $is_pro_lock
            ? '<span class="lgx-pro-locked-indicator dashicons dashicons-lock" aria-hidden="true" title="' . esc_attr__('Available in Logo Slider Pro', 'logo-slider-wp') . '"></span>'
            : '';

        $pro_css_class = $is_pro_lock ? ' lgx-pro-locked-field' : '';

        $meta = get_post_meta(absint($post->ID), '_lgx_lsp_shortcodes_meta', true);

        $default_value = $args['default'] ?? '';
        $raw_value     = $meta[$args['id']] ?? $default_value;

        // Correct for input + data attribute
        $value_safe = esc_attr($raw_value);

        $is_modern = (isset($args['layout']) && $args['layout'] === 'modern');

        if ($is_modern) {
            $output_safe  = '<div class="lgx-field lgx-field--row' . $pro_css_class . '">';
            $output_safe .= '<div class="lgx-field__label">';
            $output_safe .= '<label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label>';
            if (!empty($args['desc'])) {
                $output_safe .= '<button type="button" class="dashicons dashicons-info lgx-builder-help-icon" aria-label="' . esc_attr__( 'More information', 'logo-slider-wp' ) . '" aria-expanded="false" aria-controls="lgx-help-popover" data-help="' . esc_attr( $args['desc'] ) . '"></button>';
            }
            $output_safe .= '</div>';
            $output_safe .= '<div class="lgx-field__control">';
            $output_safe .= '<input type="text" value="' . $value_safe . '" id="' . esc_attr($args['id']) . '" class="lgx-input lgx_app_meta_filed lgx_color_picker" name="' . esc_attr($args['name']) . '" data-default-color="' . $value_safe . '" data-alpha-enabled="true" ' . ($is_pro_lock ? 'readonly' : $is_pro) . ' ' . $pro_input_attr . '>';
            $output_safe .= $is_pro_label;
            $output_safe .= '</div></div>';
        } else {
            $output_safe  = '<tr class="' . esc_attr(trim($pro_css_class)) . '">';
            $output_safe .= '<th scope="row">';
            $output_safe .= '<h4 class="lgx_app_meta_label"><label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label></h4>';
            $output_safe .= '<p class="lgx_input_desc lgx_app_meta_desc">' . esc_html($args['desc'] ?? '') . '</p>';
            $output_safe .= '</th>';

            $output_safe .= '<td>';
            $output_safe .= '<input type="text"
            value="' . $value_safe . '"
            id="' . esc_attr($args['id']) . '"
            class="lgx_app_meta_filed lgx_color_picker"
            name="' . esc_attr($args['name']) . '"
            data-default-color="' . $value_safe . '"
            data-alpha-enabled="true"
            ' . ($is_pro_lock ? 'readonly' : $is_pro) . ' ' . $pro_input_attr . '>';

            $output_safe .= $is_pro_label;
            $output_safe .= '</td>';
            $output_safe .= '</tr>';
        }

        echo $output_safe;
    }

    public function card_select(array $args)
    {
        global $post;

        if (empty($args['id']) || empty($args['name']) || empty($args['label']) || empty($args['options'])) {
            return;
        }

        $meta = get_post_meta(absint($post->ID), '_lgx_lsp_shortcodes_meta', true);
        $default_value = $args['default'] ?? '';
        $meta_value    = $meta[$args['id']] ?? $default_value;

        $output_safe  = '<fieldset class="lgx-field lgx-field--card-select">';
        $output_safe .= '<legend class="lgx-field__label">';
        $output_safe .= esc_html($args['label']);
        if (!empty($args['desc'])) {
            $output_safe .= '<p class="lgx-field__description">' . esc_html($args['desc']) . '</p>';
        }
        $output_safe .= '</legend>';
        $output_safe .= '<div class="lgx-field__control" style="display: flex; gap: 15px; flex-wrap: wrap;">';

        foreach ($args['options'] as $option_value => $option_data) {
            $checked = checked($option_value, $meta_value, false);
            $icon = $option_data['icon'] ?? '';
            $title = $option_data['title'] ?? '';
            $desc = $option_data['desc'] ?? '';
            
            $output_safe .= '<label class="lgx-card-select-label" style="flex: 1; min-width: 150px; border: 1px solid #c3c4c7; padding: 15px; border-radius: 4px; cursor: pointer; text-align: center; position: relative;">';
            $output_safe .= '<input type="radio" name="' . esc_attr($args['name']) . '" value="' . esc_attr($option_value) . '" ' . $checked . ' style="position: absolute; opacity: 0; pointer-events: none;">';
            if ($icon) {
                $output_safe .= '<div class="lgx-card-select-icon" style="font-size: 24px; color: #50575e; margin-bottom: 10px;"><i class="dashicons ' . esc_attr($icon) . '"></i></div>';
            }
            if ($title) {
                $output_safe .= '<div class="lgx-card-select-title" style="font-weight: 600; color: #1d2327;">' . esc_html($title) . '</div>';
            }
            if ($desc) {
                $output_safe .= '<div class="lgx-card-select-desc" style="font-size: 12px; color: #50575e; margin-top: 5px;">' . esc_html($desc) . '</div>';
            }
            $output_safe .= '</label>';
        }

        $output_safe .= '</div></fieldset>';

        echo $output_safe;
    }

    public function showcase_type_selector(array $args)
    {
        if (empty($args['options'])) {
            $args['options'] = [
                'carousel' => __('Carousel', 'logo-slider-wp'),
                'grid'     => __('Grid', 'logo-slider-wp'),
                'flexbox'  => __('Flexbox', 'logo-slider-wp')
            ];
        }
        $this->select($args);
    }

    /**
     * B53 — multi-value checkbox-list field, backed by a single hidden input
     * that carries the real field name/value as a comma-joined string (kept
     * in sync client-side by initCheckboxListSync() in showcase-builder.js).
     * The visible checkboxes carry no 'name' of their own and are never
     * submitted directly — this avoids the array-POST shape a native
     * <select multiple> would require, so the save handler needs no changes.
     *
     * Pro-lock treatment mirrors text()/switch(): field-level only
     * (Lgx_Pro_Locking::is_field_locked()), no per-option Model A exemption
     * — this field has no free_options carve-out, matching ADR-012.
     *
     * @param array $args id, name, label, desc, options (value => label),
     *                     default, status
     */
    public function checkbox_list(array $args)
    {
        global $post;

        if (empty($args['id']) || empty($args['name']) || empty($args['label'])) {
            return;
        }

        if (empty($args['options']) || ! is_array($args['options'])) {
            return;
        }

        $status   = $args['status'] ?? '';
        $is_pro_lock = class_exists('Lgx_Pro_Locking') ? Lgx_Pro_Locking::is_field_locked($args['id'], $status) : false;
        $pro_input_attr = $is_pro_lock ? 'data-pro-locked-input="true"' : '';

        $is_pro_label = $is_pro_lock
            ? '<span class="lgx-pro-locked-indicator dashicons dashicons-lock" aria-hidden="true" title="' . esc_attr__('Available in Logo Slider Pro', 'logo-slider-wp') . '"></span>'
            : '';

        $pro_css_class = $is_pro_lock ? ' lgx-pro-locked-field' : '';

        $meta = get_post_meta(absint($post->ID), '_lgx_lsp_shortcodes_meta', true);
        $default_value = $args['default'] ?? '';
        $meta_value    = $meta[$args['id']] ?? $default_value;

        // 'all' / empty means no filter — the explicit "All" checkbox below
        // is checked (2026-09-07: previously this meant "nothing rendered
        // checked" since 'all' had no checkbox at all). Anything else is a
        // comma-joined list of option keys (a bare single ID, legacy
        // pre-multi-select data, explodes to a one-element array here too).
        // Stale/deleted term IDs simply never match a real option and are
        // dropped from the pre-checked set — they cannot be represented by
        // a checkbox that no longer has a matching option. This interpretation
        // is unchanged for old saved data: an unset/empty/'all' stored value
        // reads identically before and after this change, it just now renders
        // its own visible checkbox instead of an implicit blank state.
        $selected_values = array();
        if ( ! empty( $meta_value ) && 'all' !== $meta_value ) {
            $selected_values = array_map( 'trim', explode( ',', $meta_value ) );
        }

        $hidden_value = ! empty( $selected_values ) ? implode( ',', $selected_values ) : 'all';

        $output_safe  = '<fieldset class="lgx-field lgx-field--checkbox-list' . $pro_css_class . '">';
        $output_safe .= '<legend class="lgx-field__label">';
        $output_safe .= '<span>' . esc_html($args['label']) . '</span>';
        if (!empty($args['desc'])) {
            $output_safe .= '<button type="button" class="dashicons dashicons-info lgx-builder-help-icon" aria-label="' . esc_attr__( 'More information', 'logo-slider-wp' ) . '" aria-expanded="false" aria-controls="lgx-help-popover" data-help="' . esc_attr( $args['desc'] ) . '"></button>';
        }
        // Lock indicator sits inline with the label (like every other field
        // type) rather than trailing after the checkbox block — this field's
        // .lgx-field__control is a column flex container (a list of
        // checkboxes, not one inline control), so appending it after the
        // list would leave it stranded on its own line below everything.
        $output_safe .= $is_pro_label;
        $output_safe .= '</legend>';
        $output_safe .= '<div class="lgx-field__control">';
        $output_safe .= '<div class="lgx-checkbox-list" data-checkbox-list-for="' . esc_attr($args['id']) . '">';

        foreach ($args['options'] as $option_value => $option_label) {
            $is_all_option = ( 'all' === (string) $option_value );
            // 'all' is now a real, explicit checkbox rather than an implicit
            // "nothing selected" state (2026-09-07) — checked exactly when no
            // individual category is selected, mirroring $hidden_value's own
            // fallback below so the two can never disagree. Marked with
            // data-checkbox-list-all so JS can target it without depending on
            // (translatable) label text or array key order.
            $checked = $is_all_option
                ? ( empty( $selected_values ) ? 'checked' : '' )
                : ( in_array( (string) $option_value, $selected_values, true ) ? 'checked' : '' );
            $item_class = 'lgx-checkbox-list__item' . ( $is_all_option ? ' lgx-checkbox-list__item--all' : '' );
            $all_attr   = $is_all_option ? 'data-checkbox-list-all="true"' : '';
            $output_safe .= '<label class="' . esc_attr( $item_class ) . '">';
            $output_safe .= '<input type="checkbox" class="lgx-checkbox-list__input" value="' . esc_attr($option_value) . '" ' . $checked . ' ' . $pro_input_attr . ' ' . $all_attr . '>';
            $output_safe .= '<span>' . esc_html($option_label) . '</span>';
            $output_safe .= '</label>';
        }

        $output_safe .= '</div>';
        $output_safe .= '<input type="hidden" id="' . esc_attr($args['id']) . '" class="lgx-input lgx_app_meta_filed" name="' . esc_attr($args['name']) . '" value="' . esc_attr($hidden_value) . '" ' . $pro_input_attr . '>';
        $output_safe .= '</div></fieldset>';

        echo $output_safe;
    }
}
}

