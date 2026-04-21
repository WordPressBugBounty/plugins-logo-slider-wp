<?php

if (!defined('WPINC')) {
    die;
}

$this->meta_form->buy_pro(
    array(
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'link' => 'https://logichunt.com/product/wordpress-logo-slider/',
    )
);


$this->meta_form->select(
    array(
        'label' => __( 'Sliding  Effect', 'logo-slider-wp'),
        'desc'  => __( 'Slide transition effect will apply as selected.', 'logo-slider-wp'),
        'name'  => 'meta_lgx_lsp_shortcodes[lgx_carousel_transition_effect]',
        'id'    => 'lgx_carousel_transition_effect',
        'default'   => 'slide',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'options'   => array(
            'slide' => __( 'Slide', 'logo-slider-wp'),
            'coverflow' => __( 'Coverflow', 'logo-slider-wp')
        )
    )
);

$this->meta_form->switch(
    array(
        'label' => __( 'Ticker Mood', 'logo-slider-wp'),
        'yes_label' => __( 'Enabled', 'logo-slider-wp'),
        'no_label' => __( 'Disabled', 'logo-slider-wp'),
        'desc' => __( 'If the option is enabled, the carousel will get a smooth autoplay effect and the nav button will be invisible.', 'logo-slider-wp'),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_carousel_ticker_en]',
        'id' => 'lgx_carousel_ticker_en',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default' => 'no'

    )
);

$this->meta_form->switch(
    array(
        'label'   => __( 'Infinite Loop', 'logo-slider-wp'),
        'yes_label' => __( 'Enabled', 'logo-slider-wp'),
        'no_label' => __( 'Disabled', 'logo-slider-wp'),
        'desc'    => __( 'If the option is mark as checked, slider will acts like endless.', 'logo-slider-wp'),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_infinite_en]',
        'id'      => 'lgx_carousel_infinite_en',
        'default' => 'yes'
    )
);

$this->meta_form->switch(
    array(
        'label'   => __( 'Autoplay', 'logo-slider-wp'),
        'yes_label' => __( 'Enabled', 'logo-slider-wp'),
        'no_label' => __( 'Disabled', 'logo-slider-wp'),
        'desc'    => __( 'If the option is mark as checked, item will slide automatically.', 'logo-slider-wp'),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_autoplay_en]',
        'id'      => 'lgx_carousel_autoplay_en',
        'default' => 'yes'
    )
);


$this->meta_form->number(
    array(
        'label'   => __( 'Transition Speed', 'logo-slider-wp'),
        'desc'    => __( 'Duration of transition between slides (in ms). Default value is 450.', 'logo-slider-wp'),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_transition_speed]',
        'id'      => 'lgx_carousel_transition_speed',
        'default' => '450'
    )
);


$this->meta_form->number(
    array(
        'label'   => __( 'Autoplay Delay', 'logo-slider-wp'),
        'desc'    => __( 'The delay between transitions (in ms) during autoplay. Default value is 1500. <br> <span style="color: #e31919">Note: If the Ticker Mood is enabled, this option will be inactive. Adjust Transition Speed only.</span>', 'logo-slider-wp'),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_autoplay_delay]',
        'id'      => 'lgx_carousel_autoplay_delay',
        'default' => '1500'
    )
);



$this->meta_form->switch(
    array(
        'label'   => __( 'RTL Support', 'logo-slider-wp'),
        'yes_label' => __( 'Enabled', 'logo-slider-wp'),
        'no_label' => __( 'Disabled', 'logo-slider-wp'),
        'desc'    => __( 'If the option is mark as checked, then Right to Left (rtl) support will be enabled.', 'logo-slider-wp'),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_rtl_en]',
        'id'      => 'lgx_carousel_rtl_en',
        'default' => 'no'
    )
);


$this->meta_form->switch(
    array(
        'label'   => __( 'Pause on Hover', 'logo-slider-wp'),
        'yes_label' => __( 'Enabled', 'logo-slider-wp'),
        'no_label' => __( 'Disabled', 'logo-slider-wp'),
        'desc'    => __( 'When enabled autoplay will be paused on mouse enter over Swiper container. ', 'logo-slider-wp'),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_pause_mouse_enter_en]',
        'id'      => 'lgx_carousel_pause_mouse_enter_en',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default' => 'no'
    )
);

$this->meta_form->number(
    array(
        'label'     => __( 'Space Between', 'logo-slider-wp'),
        'desc'      => __( 'The value must be a number. The number of pixel will be set between slider item. Default value is 10.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_space_between]',
        'id'        => 'lgx_carousel_space_between',
        'default'   => 10
    )
);


$this->meta_form->switch(
    array(
        'label' => __( 'Lazy load', 'logo-slider-wp'),
        'yes_label' => __( 'Enabled', 'logo-slider-wp'),
        'no_label' => __( 'Disabled', 'logo-slider-wp'),
        'desc' => __( 'If the option is enabled, images lazy loading will be enabled with default settings', 'logo-slider-wp'),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_carousel_lazy_load_en]',
        'id' => 'lgx_carousel_lazy_load_en',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default' => 'no'

    )
);

$this->meta_form->select(
    array(
        'label' => __( 'Lazy Spinner Type', 'logo-slider-wp'),
        'desc' => __( 'Select Lazy Load Spinner color type for animated preloader spinner', 'logo-slider-wp'),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_carousel_lazy_spinner_color]',
        'id' => 'lgx_carousel_lazy_spinner_color',
        'default'   => 'white',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'options'   => array(
            'white' => __( 'White', 'logo-slider-wp'),
            'blue' => __( 'Blue', 'logo-slider-wp'),
        )
    )
);

$this->meta_form->buy_pro(
    array(
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'link' => 'https://logichunt.com/product/wordpress-logo-slider/',
    )
);


/********************************************************************************/
$this->meta_form->header_spacer(
    array(
        'label'     => __( 'Navigation Settings', 'logo-slider-wp'),
    )
);
/********************************************************************************/

$this->meta_form->switch(
    array(
        'yes_label' => __( 'Show', 'logo-slider-wp'),
        'no_label' => __( 'Hide', 'logo-slider-wp'),
        'label'   => __( 'Navigation', 'logo-slider-wp'),
        'desc'    => __( 'If the options is mark as checked then navigation will show', 'logo-slider-wp'),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_en]',
        'id'      => 'lgx_carousel_nav_en',
        'default' => 'yes'
    )
);

$this->meta_form->switch(
    array(
        'yes_label' => __( 'Yes', 'logo-slider-wp'),
        'no_label' => __( 'No', 'logo-slider-wp'),
        'label'   => __( 'Visible On Over', 'logo-slider-wp'),
        'desc'    => __( 'If the options is mark as checked then navigation will show only on hover.', 'logo-slider-wp'),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_hover_en]',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'id'      => 'lgx_carousel_nav_hover_en',
        'default' => 'no'
    )
);

$this->meta_form->select(
    array(
        'label' => __( 'Navigation Position', 'logo-slider-wp'),
        'desc' => __( 'On which position navigation will display.', 'logo-slider-wp'),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_position]',
        'id' => 'lgx_carousel_nav_position',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => 'top_right',
        'options'   => array(
            'top_right' => __( 'Top Right', 'logo-slider-wp'),
            'top_center' => __( 'Top Center', 'logo-slider-wp'),
            'top_left' => __( 'Top Left', 'logo-slider-wp'),
            'vertical_center' => __( 'Vertical Middle', 'logo-slider-wp'),
            'bottom_center' => __( 'Bottom Center', 'logo-slider-wp'),
            'bottom_left' => __( 'Bottom Left', 'logo-slider-wp'),
            'bottom_right' => __( 'Bottom Right', 'logo-slider-wp'),
        )
    )
);

$this->meta_form->text(
    array(
        'label'     => __( 'Vertical Nav Bottom Position', 'logo-slider-wp'),
        'desc'      => __( 'Set top bottom position for Vertical Middle button.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_ver_pos_bottom]',
        'id'        => 'lgx_carousel_nav_ver_pos_bottom',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '50%'
    )
);


$this->meta_form->text(
    array(
        'label'     => __( 'Vertical Nav Left Right Position', 'logo-slider-wp'),
        'desc'      => __( 'Set left and right side position for Vertical Middle button.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_ver_pos_left_right]',
        'id'        => 'lgx_carousel_nav_ver_pos_left_right',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '-55px'
    )
);



$this->meta_form->color(
    array(
        'label'     => __( 'Nav Color', 'logo-slider-wp'),
        'desc'      => __( 'Please select  carousel navigation color for icon or text.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_color]',
        'id'        => 'lgx_carousel_nav_color',
        'default'   => '#ffffff',
    )
);

$this->meta_form->color(
    array(
        'label'     => __( 'Nav Hover Color', 'logo-slider-wp'),
        'desc'      => __( 'Please select carousel navigation hover color for icon or text.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_color_hover]',
        'id'        => 'lgx_carousel_nav_color_hover',
        'default'   => '#ffffff',
    )
);

$this->meta_form->color(
    array(
        'label'     => __( 'Background Color', 'logo-slider-wp'),
        'desc'      => __( 'Please select carousel navigation background color.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_bg_color]',
        'id'        => 'lgx_carousel_nav_bg_color',
        'default'   => '#222b30',
    )
);

$this->meta_form->color(
    array(
        'label'     => __( 'Background Hover Color', 'logo-slider-wp'),
        'desc'      => __( 'Please select carousel navigation hover background color.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_bg_color_hover]',
        'id'        => 'lgx_carousel_nav_bg_color_hover',
        'default'   => '#222b30',
    )
);

$this->meta_form->text(
    array(
        'label'     => __( 'Nav Button Font Size', 'logo-slider-wp'),
        'desc'      => __( 'Set Font Size for navigation button.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_btn_font_size]',
        'id'        => 'lgx_carousel_nav_btn_font_size',
        'status'   => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '22px'
    )
);


$this->meta_form->text(
    array(
        'label'     => __( 'Nav Button Width', 'logo-slider-wp'),
        'desc'      => __( 'Set Width for navigation button. You can also set the Width as: auto', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_btn_width]',
        'id'        => 'lgx_carousel_nav_btn_width',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '30px'
    )
);

$this->meta_form->text(
    array(
        'label'     => __( 'Nav Button Height', 'logo-slider-wp'),
        'desc'      => __( 'Set Height for navigation button. You can also set the Height as: auto', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_btn_height]',
        'id'        => 'lgx_carousel_nav_btn_height',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '30px'
    )
);


$this->meta_form->text(
    array(
        'label'     => __( 'Nav Button Margin', 'logo-slider-wp'),
        'desc'      => __( 'Set Margin for navigation button.<br> <span style="color: #e31919"> You can define the margin by shorthand method as: 10px 0px 5px 0px</span>', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_btn_margin]',
        'id'        => 'lgx_carousel_nav_btn_margin',
        'default'   => '5px 0px'
    )
);

$this->meta_form->text(
    array(
        'label'     => __( 'Nav Button Padding', 'logo-slider-wp'),
        'desc'      => __( 'Set padding for navigation button.<br> <span style="color: #e31919"> For auto Height & Width you can define the top-bottom padding by shorthand method as: 3px 12px</span>', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_btn_padding]',
        'id'        => 'lgx_carousel_nav_btn_padding',
        'status'    => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '0px'
    )
);


$this->meta_form->switch(
    array(
        'yes_label' => __( 'Enabled', 'logo-slider-wp'),
        'no_label' => __( 'Disabled', 'logo-slider-wp'),
        'label'     => __( 'Nav Border', 'logo-slider-wp'),
        'desc'      => __( 'Enable Border for carousel navigation', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_border_en]',
        'id'        => 'lgx_carousel_nav_border_en',
        'default'   => 'no'
    )
);


$this->meta_form->color(
    array(
        'label'     => __( 'Border Color', 'logo-slider-wp'),
        'desc'      => __( 'Choose border color carousel navigation.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_border_color]',
        'id'        => 'lgx_carousel_nav_border_color',
        'default'   => '#161E54',
    )
);


$this->meta_form->color(
    array(
        'label'     => __( 'Border Hover Color', 'logo-slider-wp'),
        'desc'      => __( 'Choose border hover color carousel navigation.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_border_color_hover]',
        'id'        => 'lgx_carousel_nav_border_color_hover',
        'default'   => '#88E0EF',
    )
);

$this->meta_form->text(
    array(
        'label'     => __( 'Border Width', 'logo-slider-wp'),
        'desc'      => __( 'Set Border Width for carousel navigation', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_border_width]',
        'id'        => 'lgx_carousel_nav_border_width',
        'default'   => '1px'
    )
);

$this->meta_form->select(
    array(
        'label' => __( 'Border Style', 'logo-slider-wp'),
        'desc' => __( 'Select carousel navigation button border style.', 'logo-slider-wp'),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_border_style]',
        'id' => 'lgx_carousel_nav_border_style',
        'default'   => 'solid',
        'options'   => array(
            'solid' => __( 'Solid', 'logo-slider-wp'),
            'dotted' => __( 'Dotted', 'logo-slider-wp'),
            'dashed' => __( 'Dashed', 'logo-slider-wp'),
            'double' => __( 'Double', 'logo-slider-wp'),
            'groove' => __( 'Groove', 'logo-slider-wp'),
            'ridge' => __( 'Ridge', 'logo-slider-wp'),
            'inset' => __( 'Inset', 'logo-slider-wp'),
            'outset' => __( 'Outset', 'logo-slider-wp'),
        )
    )
);


$this->meta_form->text(
    array(
        'label'     => __( 'Border Radius', 'logo-slider-wp'),
        'desc'      => __( 'Set carousel Border Radius for navigation button. <br> <span style="color: #e31919">Set 50% to get the circular button.</span>', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_border_radius]',
        'id'        => 'lgx_carousel_nav_border_radius',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '4px'
    )
);


$this->meta_form->select(
    array(
        'label' => __( 'Nav Button Type', 'logo-slider-wp'),
        'desc' => __( 'Select content type for navigation button.', 'logo-slider-wp'),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_btn_type]',
        'id' => 'lgx_carousel_nav_btn_type',
        'default'   => 'icon',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'options'   => array(
            'icon' => __( 'Icon', 'logo-slider-wp'),
            'text' => __( 'Text', 'logo-slider-wp'),
        )
    )
);


$this->meta_form->select(
    array(
        'label' => __( 'Select Icon', 'logo-slider-wp'),
        'desc' => __( 'Select content type for navigation button.', 'logo-slider-wp'),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_icon]',
        'id' => 'lgx_carousel_nav_icon',
        'default'   => 'angle',
        'options'   => array(
            'angle' => __( 'Angle', 'logo-slider-wp'),
            'angle-double' => __( 'Angle Double', 'logo-slider-wp'),
            'arrow' => __( 'Arrow', 'logo-slider-wp'),
            'arrow-circle-o' => __( 'Arrow Circle - O', 'logo-slider-wp'),
            'arrow-circle' => __( 'Arrow Circle', 'logo-slider-wp'),
            'long-arrow' => __( 'Long  Arrow', 'logo-slider-wp'),
            'caret' => __( 'Caret', 'logo-slider-wp'),
            'chevron' => __( 'Chevron', 'logo-slider-wp'),
            'chevron-circle' => __( 'Chevron Circle', 'logo-slider-wp'),
        )
    )
);


$this->meta_form->text(
    array(
        'label'     => __( 'Left Nav Text', 'logo-slider-wp'),
        'desc'      => __( 'Add content type for previous navigation button.<br> <span style="color: #e31919">Note: To use this option you must select Content Type as Text.</span>', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_btn_text_prev]',
        'id'        => 'lgx_carousel_nav_btn_text_prev',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => 'Prev'
    )
);

$this->meta_form->text(
    array(
        'label'     => __( 'Right Nav Text', 'logo-slider-wp'),
        'desc'      => __( 'Add content type for previous navigation button.<br> <span style="color: #e31919">Note: To use this option you must select Content Type as Text.</span>', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_btn_text_next]',
        'id'        => 'lgx_carousel_nav_btn_text_next',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => 'Next'
    )
);

$this->meta_form->switch(
    array(
        'label'   => __( 'Nav Hide on Mobile', 'logo-slider-wp'),
        'yes_label' => __( 'Yes', 'logo-slider-wp'),
        'no_label' => __( 'No', 'logo-slider-wp'),
        'desc'    => __( 'If the options is mark as checked then navigation will hide on mobile view (767px).', 'logo-slider-wp'),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_nav_mobile_en]',
        'id'      => 'lgx_carousel_nav_mobile_en',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default' => 'no'
    )
);

$this->meta_form->buy_pro(
    array(
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'link' => 'https://logichunt.com/product/wordpress-logo-slider/',
    )
);



/********************************************************************************/
$this->meta_form->header_spacer(
    array(
        'label'     => __( 'Pagination ( Dot ) Settings', 'logo-slider-wp'),
    )
);
/********************************************************************************/

$this->meta_form->switch(
    array(
        'yes_label' => __( 'Show', 'logo-slider-wp'),
        'no_label' => __( 'Hide', 'logo-slider-wp'),
        'label'   => __( 'Pagination', 'logo-slider-wp'),
        'desc'    => __( 'If the options is mark as checked then pagination will show.', 'logo-slider-wp'),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_pagination_en]',
        'id'      => 'lgx_carousel_pagination_en',
        'default' => 'yes'
    )
);

$this->meta_form->color(
    array(
        'label'     => __( 'Pagination Color', 'logo-slider-wp'),
        'desc'      => __( 'Please select carousel pagination dot Color.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_pagination_color]',
        'id'        => 'lgx_carousel_pagination_color',
        'default'   => '#869791',
    )
);


$this->meta_form->color(
    array(
        'label'     => __( 'Active Color', 'logo-slider-wp'),
        'desc'      => __( 'Please select carousel pagination active dot Color..', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_pagination_color_active]',
        'id'        => 'lgx_carousel_pagination_color_active',
        'default'   => '#222b30',
    )
);

$this->meta_form->switch(
    array(
        'label'   => __( 'Dynamic Bullets', 'logo-slider-wp'),
        'yes_label' => __( 'Enabled', 'logo-slider-wp'),
        'no_label' => __( 'Disabled', 'logo-slider-wp'),
        'desc'    => __( 'Good to enable if you use bullets pagination with a lot of slides. So it will keep only few bullets visible at the same time.', 'logo-slider-wp'),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_dynamic_bullets_en]',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'id'      => 'lgx_carousel_dynamic_bullets_en',
        'default' => 'no'
    )
);

$this->meta_form->text(
    array(
        'label'     => __( 'Main Dynamic Bullets No', 'logo-slider-wp'),
        'desc'      => __( 'The number of main bullets visible when Dynamic Bullets enabled.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_carousel_dynamic_bullets_no]',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'id'        => 'lgx_carousel_dynamic_bullets_no',
        'default'   => '1'
    )
);

$this->meta_form->switch(
    array(
        'label'   => __( 'Pagination Hide on Mobile', 'logo-slider-wp'),
        'yes_label' => __( 'Yes', 'logo-slider-wp'),
        'no_label' => __( 'No', 'logo-slider-wp'),
        'desc'    => __( 'If the options is mark as checked then pagination will hide on mobile view (767px).', 'logo-slider-wp'),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_pagi_mobile_en]',
        'id'      => 'lgx_carousel_pagi_mobile_en',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default' => 'no'
    )
);

$this->meta_form->buy_pro(
    array(
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'link' => 'https://logichunt.com/product/wordpress-logo-slider/',
    )
);


/********************************************************************************/
$this->meta_form->header_spacer(
    array(
        'label'     => __( 'More Settings', 'logo-slider-wp'),
    )
);
/********************************************************************************/

$this->meta_form->switch(
    array(
        'yes_label' => __( 'Enabled', 'logo-slider-wp'),
        'no_label' => __( 'Disabled', 'logo-slider-wp'),
        'label'   => __( 'Auto Height', 'logo-slider-wp'),
        'desc'    => __( 'If the option is mark as checked, then auto height support will be enabled.', 'logo-slider-wp'),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_auto_height_en]',
        'id'      => 'lgx_carousel_auto_height_en',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default' => 'no'
    )
);

$this->meta_form->switch(
    array(
        'yes_label' => __( 'Enabled', 'logo-slider-wp'),
        'no_label' => __( 'Disabled', 'logo-slider-wp'),
        'label'   => __( 'Touch Swipe', 'logo-slider-wp'),
        'desc'    => __( 'If the option is mark as checked, then the slider touch move facilities will be available.', 'logo-slider-wp'),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_allow_touch_move_en]',
        'id'      => 'lgx_carousel_allow_touch_move_en',
        'default' => 'yes'
    )
);

$this->meta_form->switch(
    array(
        'yes_label' => __( 'Enabled', 'logo-slider-wp'),
        'no_label' => __( 'Disabled', 'logo-slider-wp'),
        'label'   => __( 'Mouse Wheel', 'logo-slider-wp'),
        'desc'    => __( 'Enables navigation through slides using mouse wheel.', 'logo-slider-wp'),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_mouse_wheel_en]',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'id'      => 'lgx_carousel_mouse_wheel_en',
        'default' => 'no'
    )
);

$this->meta_form->switch(
    array(
        'yes_label' => __( 'Enabled', 'logo-slider-wp'),
        'no_label' => __( 'Disabled', 'logo-slider-wp'),
        'label'   => __( 'Mouse Draggable', 'logo-slider-wp'),
        'desc'    => __( 'If the option is mark as checked, swiper will accept mouse events like touch events (click and drag to change slides).', 'logo-slider-wp'),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_simulate_touch_en]',
        'id'      => 'lgx_carousel_simulate_touch_en',
        'default' => 'yes'
    )
);


$this->meta_form->switch(
    array(
        'yes_label' => __( 'Enabled', 'logo-slider-wp'),
        'no_label' => __( 'Disabled', 'logo-slider-wp'),
        'label'   => __( 'Grab Cursor', 'logo-slider-wp'),
        'desc'    => __( 'If the option is mark as checked, then cursor style change to hand tool when cursor hover on item.', 'logo-slider-wp'),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_carousel_grab_cursor_en]',
        'id'      => 'lgx_carousel_grab_cursor_en',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default' => 'no'
    )
);

$this->meta_form->select(
    array(
        'label' => __( 'Item Image Vertical Align', 'logo-slider-wp'),
        'desc' => __( 'Set carousel item vertical alignment.<br> <span style="color: #e31919">Note: It helps if the size of the images are not equal.</span>', 'logo-slider-wp'),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_carousel_item_vertical_align]',
        'id' => 'lgx_carousel_item_vertical_align',
        'default'   => 'top',
       // 'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'options'   => array(
            'top' => __( 'Top', 'logo-slider-wp'),
            'mid' => __( 'Vertically Middle', 'logo-slider-wp'),
        )
    )
);