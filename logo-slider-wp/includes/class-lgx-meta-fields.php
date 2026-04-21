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


class LogoSLiderWpMetaForm
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
        $disabled = ($status === 'disabled') ? 'disabled' : '';

        $is_pro_label = ($status === 'disabled')
            ? '<span class="lgx_meta_field_mark_pro_wrap"><span class="lgx_meta_field_mark">'
            . esc_html__('Pro', 'logo-slider-wp') .
            '</span></span>'
            : '';

        $meta = get_post_meta(absint($post->ID), '_lgx_lsp_shortcodes_meta', true);

        // Get value safely
        $raw_value = $meta[$args['id']] ?? ($args['default'] ?? '');

        // Escape for input field
        $value_safe = esc_attr($raw_value);

        // Output start
        $output_safe  = '<tr>';

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
                ' . $disabled . '>';

        $output_safe .= $is_pro_label;

        $output_safe .= '</td>';
        $output_safe .= '</tr>';

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
        $disabled = ($status === 'disabled') ? 'disabled' : '';

        $is_pro_label = ($status === 'disabled')
            ? '<span class="lgx_meta_field_mark_pro_wrap"><span class="lgx_meta_field_mark">'
            . esc_html__('Pro', 'logo-slider-wp') .
            '</span></span>'
            : '';

        $meta = get_post_meta(absint($post->ID), '_lgx_lsp_shortcodes_meta', true);

        $raw_value = $meta[$args['id']] ?? ($args['default'] ?? '');
        $value     = is_numeric($raw_value) ? $raw_value : '';
        $value_safe = esc_attr($value);

        $output_safe  = '<tr>';
        $output_safe .= '<th scope="row">';
        $output_safe .= '<h4 class="lgx_app_meta_label"><label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label></h4>';
        $output_safe .= '<p class="lgx_input_desc lgx_app_meta_desc">' . esc_html($args['desc'] ?? '') . '</p>';
        $output_safe .= '</th>';

        $output_safe .= '<td>';
        $output_safe .= '<input type="number" value="' . $value_safe . '" id="' . esc_attr($args['id']) . '" class="lgx_input_width lgx_app_meta_filed lgx_app_meta_number" name="' . esc_attr($args['name']) . '" ' . $disabled . '>';
        $output_safe .= $is_pro_label;
        $output_safe .= '</td></tr>';

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
        $disabled = ($status === 'disabled') ? 'disabled' : '';

        $is_pro_label = ($status === 'disabled')
            ? '<span class="lgx_meta_field_mark_pro_wrap"><span class="lgx_meta_field_mark">'
            . esc_html__('Pro', 'logo-slider-wp') .
            '</span></span>'
            : '';

        $meta = get_post_meta(absint($post->ID), '_lgx_lsp_shortcodes_meta', true);

        $raw_value = $meta[$args['id']] ?? ($args['default'] ?? '');
        $value_safe = esc_url($raw_value);

        $output_safe  = '<tr>';
        $output_safe .= '<th scope="row">';
        $output_safe .= '<h4 class="lgx_app_meta_label"><label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label></h4>';
        $output_safe .= '<p class="lgx_input_desc lgx_app_meta_desc">' . esc_html($args['desc'] ?? '') . '</p>';
        $output_safe .= '</th>';

        $output_safe .= '<td>';
        $output_safe .= '<input type="url" value="' . $value_safe . '" id="' . esc_attr($args['id']) . '" class="lgx_input_width lgx_app_meta_filed lgx_app_meta_url" name="' . esc_attr($args['name']) . '" ' . $disabled . '>';
        $output_safe .= $is_pro_label;
        $output_safe .= '</td></tr>';

        echo $output_safe;
    }




    public function textarea(array $args)
    {
        global $post;

        if (empty($args['id']) || empty($args['name']) || empty($args['label'])) {
            return;
        }

        $status   = $args['status'] ?? '';
        $disabled = ($status === 'disabled') ? 'disabled' : '';
        $placeholder = esc_attr($args['placeholder'] ?? '');

        $is_pro_label = ($status === 'disabled')
            ? '<span class="lgx_meta_field_mark_pro_wrap"><span class="lgx_meta_field_mark">'
            . esc_html__('Pro', 'logo-slider-wp') .
            '</span></span>'
            : '';


        $meta = get_post_meta(absint($post->ID), '_lgx_lsp_shortcodes_meta', true);

        $raw_value  = $meta[$args['id']] ?? ($args['default'] ?? '');
        $value_safe = esc_textarea($raw_value);


        $output_safe  = '<tr>';
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
    ' . $disabled . '>' . $value_safe . '</textarea>';
        $output_safe .= $is_pro_label;
        $output_safe .= '</td></tr>';

        echo $output_safe;
    }


    public function checkbox(array $args)
    {
        global $post;

        if (empty($args['id']) || empty($args['name']) || empty($args['label'])) {
            return;
        }

        $status   = $args['status'] ?? '';
        $disabled = ($status === 'disabled') ? 'disabled' : '';

        $is_pro_label = ($status === 'disabled')
            ? '<span class="lgx_meta_field_mark_pro_wrap"><span class="lgx_meta_field_mark">'
            . esc_html__('Pro', 'logo-slider-wp') .
            '</span></span>'
            : '';

        $meta = get_post_meta(absint($post->ID), '_lgx_lsp_shortcodes_meta', true);
        $value = $meta[$args['id']] ?? ($args['default'] ?? '');

        $output_safe  = '<tr>';
        $output_safe .= '<th scope="row">';
        $output_safe .= '<h4 class="lgx_app_meta_label"><label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label></h4>';
        $output_safe .= '<p class="lgx_input_desc lgx_app_meta_desc">' . esc_html($args['desc'] ?? '') . '</p>';
        $output_safe .= '</th>';

        $output_safe .= '<td>';
        $output_safe .= '<input type="checkbox" value="yes" id="' . esc_attr($args['id']) . '" class="lgx_app_meta_filed lgx_app_meta_checkbox" name="' . esc_attr($args['name']) . '" ' . checked($value, 'yes', false) . ' ' . $disabled . '>';
        $output_safe .= $is_pro_label;
        $output_safe .= '</td></tr>';

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

        $is_pro_label = ($status === 'disabled')
            ? '<span class="lgx_meta_field_mark_pro_wrap"><span class="lgx_meta_field_mark">'
            . esc_html__('Pro', 'logo-slider-wp') .
            '</span></span>'
            : '';

        $meta = get_post_meta(absint($post->ID), '_lgx_lsp_shortcodes_meta', true);

        $default_value = $args['default'] ?? '';
        $meta_value    = $meta[$args['id']] ?? $default_value;

        $output_safe  = '<tr>';

        $output_safe .= '<th scope="row">';
        $output_safe .= '<h4 class="lgx_app_meta_label"><label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label></h4>';
        $output_safe .= '<p class="lgx_input_desc lgx_app_meta_desc">' . esc_html($args['desc'] ?? '') . '</p>';
        $output_safe .= '</th>';

        $output_safe .= '<td>';

        // Options
        $options_safe = '';

        foreach ($args['options'] as $option_value => $option_text) {

            $selected = selected($option_value, $meta_value, false);

            $disabled_attr = ($is_pro === 'disabled' && $option_value !== $meta_value)
                ? 'disabled="disabled"'
                : '';

            $label = esc_html($option_text);

            if ($is_pro === 'disabled' && $option_value !== $meta_value) {
                $label .= ' ( Pro )';
            }

            $options_safe .= '<option value="' . esc_attr($option_value) . '" ' . $selected . ' ' . $disabled_attr . '>' . $label . '</option>';
        }

        $is_multiple = (isset($args['multiple']) && $args['multiple'] === 'yes') ? 'multiple' : '';

        $output_safe .= '<select name="' . esc_attr($args['name']) . '" class="lgx_input_width lgx_app_meta_filed lgx_app_meta_select ' . ($is_pro === 'disabled' ? 'lgx_app_meta_select_disabled' : '') . '" id="' . esc_attr($args['id']) . '" ' . $is_multiple . '>';

        $output_safe .= $options_safe;

        $output_safe .= '</select>';

        // (kept optional, like your original)
        // $output_safe .= $is_pro_label;

        $output_safe .= '</td>';
        $output_safe .= '</tr>';

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

        $disabled  = ($status === 'disabled') ? 'disabled' : '';
        $pro_class = ($status === 'disabled') ? 'lgx_switch_pro' : '';

        $is_pro_label = ($status === 'disabled')
            ? '<span class="lgx_meta_field_mark_pro_wrap"><span class="lgx_meta_field_mark">'
            . esc_html__('Pro', 'logo-slider-wp') .
            '</span></span>'
            : '';

        $meta = get_post_meta(absint($post->ID), '_lgx_lsp_shortcodes_meta', true);
        $meta_value = $meta[$args['id']] ?? ($args['default'] ?? '');

        //$is_checked = ( $meta_value == 'yes') ? 'checked' : '';


        //* Safe Output Start
        $output_safe = '<tr>';

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
        ' . $disabled . '>';

        $output_safe .= ' <div class="lgx_switch_slider lgx_switch_round">';
        $output_safe .= '<span class="lgx_switch_on">' . esc_html($yes_label) . '</span>';
        $output_safe .= '<span class="lgx_switch_off">' . esc_html($no_label) . '</span>';
        $output_safe .= '</div>';
        $output_safe .= '</label>';
        $output_safe .= $is_pro_label;
        $output_safe .= '</td>';

        $output_safe .= '</tr>';

        echo $output_safe;
    }




    public function upload(array $args)
    {

        global $post;

        if (empty($args['id']) || empty($args['name']) || empty($args['label'])) {
            return;
        }

        $status   = $args['status'] ?? '';
        $is_pro   = ($status === 'disabled') ? 'disabled' : '';

        $is_pro_label = ($status === 'disabled')
            ? '<span class="lgx_meta_field_mark_pro_wrap"><span class="lgx_meta_field_mark">'
            . esc_html__('Pro', 'logo-slider-wp') .
            '</span></span>'
            : '';

        $meta = get_post_meta(absint($post->ID), '_lgx_lsp_shortcodes_meta', true);

        $default_value = $args['default'] ?? '';
        $raw_value     = $meta[$args['id']] ?? $default_value;

        // ✅ IMPORTANT: URL must be escaped properly
        $value_safe = esc_url($raw_value);

        $output_safe  = '<tr>';

        $output_safe .= '<th scope="row">';
        $output_safe .= '<h4 class="lgx_app_meta_label"><label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label></h4>';
        $output_safe .= '<p class="lgx_input_desc lgx_app_meta_desc">' . esc_html($args['desc'] ?? '') . '</p>';
        $output_safe .= '</th>';

        $output_safe .= '<td>';

        // Select button
        $output_safe .= '<button type="button" class="button button-large lgx_icon_image_button" data-icon-field-name="' . esc_attr($args['name']) . '" data-icon-img-id="' . esc_attr($args['id']) . '"><i class="lgxicon lgx-icon-file-photo-o"></i> ' . esc_html__('Select Icon', 'logo-slider-wp') . '</button>';

        // spacing
        $output_safe .= '&nbsp;&nbsp;&nbsp;';

        // Remove button
        $output_safe .= '<button type="button" class="button button-large lgx_icon_image_button_clear" data-icon-field-name="' . esc_attr($args['name']) . '" data-icon-img-id="' . esc_attr($args['id']) . '"><i class="lgxicon lgx-icon-remove"></i> ' . esc_html__('Remove Icon', 'logo-slider-wp') . '</button>';

        // spacing
        $output_safe .= '&nbsp;&nbsp;&nbsp;';

        // Preview image
        $output_safe .= '<img src="' . $value_safe . '" alt="" id="' . esc_attr($args['id']) . '" style="width: 24px; margin-top: 5px;">';

        // Hidden input
        $output_safe .= '<input type="hidden" value="' . $value_safe . '" name="' . esc_attr($args['name']) . '" ' . $is_pro . '>';

        $output_safe .= $is_pro_label;

        $output_safe .= '</td>';
        $output_safe .= '</tr>';

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
        $is_pro   = ($status === 'disabled') ? 'disabled' : '';

        $is_pro_label = ($status === 'disabled')
            ? '<span class="lgx_meta_field_mark_pro_wrap"><span class="lgx_meta_field_mark">'
            . esc_html__('Pro', 'logo-slider-wp') .
            '</span></span>'
            : '';

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

        /*
     * OUTPUT START
     */
        $output_safe  = '<tr>';

        $output_safe .= '<th scope="row">';
        $output_safe .= '<h4 class="lgx_app_meta_label"><label for="' . esc_attr($args['id']) . '">' . esc_html($args['label']) . '</label></h4>';
        $output_safe .= '<p class="lgx_input_desc lgx_app_meta_desc">' . esc_html($args['desc'] ?? '') . '</p>';
        $output_safe .= '</th>';

        $output_safe .= '<td>';
        $output_safe .= '<div class="lgx_group_field_wrap">';

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

            $label = esc_html($option_text);

            if ($is_pro === 'disabled' && $option_value !== $meta_value) {
                $label .= ' ( Pro )';
            }

            $options_safe .= '<option value="' . esc_attr($option_value) . '" ' . $selected . ' ' . $disabled_attr . '>' . $label . '</option>';
        }

        $is_multiple = (isset($args['multiple']) && $args['multiple'] === 'yes') ? 'multiple' : '';

        $output_safe .= '<select name="' . esc_attr($args['name_select']) . '" class="lgx_input_width lgx_app_meta_filed lgx_app_meta_select ' . ($is_pro === 'disabled' ? 'lgx_app_meta_select_disabled' : '') . '" id="' . esc_attr($args['id_select']) . '" ' . $is_multiple . '>';

        $output_safe .= $options_safe;
        $output_safe .= '</select>';
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

        $output_safe .= $is_pro_label;

        $output_safe .= '</div>'; // item 2

        $output_safe .= '</div>'; // wrap
        $output_safe .= '</td>';

        $output_safe .= '</tr>';

        echo $output_safe;
    }





    public function buy_pro(array $args)
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
        $is_pro   = ($status === 'disabled') ? 'disabled' : '';

        $is_pro_label = ($status === 'disabled')
            ? '<span class="lgx_meta_field_mark_pro_wrap"><span class="lgx_meta_field_mark">'
            . esc_html__('Pro', 'logo-slider-wp') .
            '</span></span>'
            : '';

        $meta = get_post_meta(absint($post->ID), '_lgx_lsp_shortcodes_meta', true);

        $default_value = $args['default'] ?? '';
        $raw_value     = $meta[$args['id']] ?? $default_value;

        // Correct for input + data attribute
        $value_safe = esc_attr($raw_value);

        $output_safe  = '<tr>';

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
        ' . $is_pro . '>';

        $output_safe .= $is_pro_label;

        $output_safe .= '</td>';
        $output_safe .= '</tr>';

        echo $output_safe;
    }
}
