<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$this->meta_form->buy_pro(
    array(
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'link' => 'https://logichunt.com/product/wordpress-logo-slider/',
    )
);

$this->meta_form->switch(
    array(
        'label'   => __( 'Tooltip', 'logo-slider-wp'),
        'yes_label' => __( 'Enabled', 'logo-slider-wp'),
        'no_label' => __( 'Disabled', 'logo-slider-wp'),
        'desc'    => __( 'Enable Tooltip in your showcase.', 'logo-slider-wp'),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_tooltip_en]',
        'id'      => 'lgx_tooltip_en',
        'default' => 'no'
    )
);


$this->meta_form->select(
    array(
        'label'     => __( 'Content Type', 'logo-slider-wp'),
        'desc'      => __( 'Select which content you want to show in tooltip.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_content_type]',
        'id'        => 'lgx_tooltip_content_type',
        'default'   => 'brand_name',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'options'   => array(
            'brand_name' => __( 'Brand Name', 'logo-slider-wp'),
            'tooltip_text' => __( 'Tooltip Text', 'logo-slider-wp')
        )
    )
);


$this->meta_form->select(
    array(
        'label'     => __( 'Position', 'logo-slider-wp'),
        'desc'      => __( 'Select the position where you want to show tooltip.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_position]',
        'id'        => 'lgx_tooltip_position',
        'default'   => 'top',
     //  'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'options'   => array(
            'top' => __( 'Top', 'logo-slider-wp'),
            'bottom' => __( 'Bottom', 'logo-slider-wp'),
            'right' => __( 'Right', 'logo-slider-wp'),
            'left' => __( 'Left', 'logo-slider-wp'),
        )
    )
);


$this->meta_form->number(
    array(
        'label'     => __( 'Min Width', 'logo-slider-wp'),
        'desc'      => __( 'Set a minimum width for the tooltip. Default: 0 (auto width)', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_min_width]',
        'id'        => 'lgx_tooltip_min_width',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => 0
    )
);

$this->meta_form->number(
    array(
        'label'     => __( 'Max Width', 'logo-slider-wp'),
        'desc'      => __( 'Set a maximum width for the tooltip. Default: null (no max width)', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_max_width]',
        'id'        => 'lgx_tooltip_max_width',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => ''
    )
);


$this->meta_form->select(
    array(
        'label'     => __( 'Animation', 'logo-slider-wp'),
        'desc'      => __( 'Determines how the tooltip will animate in and out.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_anim]',
        'id'        => 'lgx_tooltip_anim',
        'default'   => 'fade',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'options'   => array(
            'fade'   => __( 'Fade', 'logo-slider-wp'),
            'grow' => __( 'Grow', 'logo-slider-wp'),
            'swing'  => __( 'Swing', 'logo-slider-wp'),
            'slide'  => __( 'Slide', 'logo-slider-wp'),
            'fall'  => __( 'Fall', 'logo-slider-wp'),
        )
    )
);


$this->meta_form->number(
    array(
        'label'     => __( 'Animation Duration', 'logo-slider-wp'),
        'desc'      => __( 'Sets the duration of the animation, in milliseconds.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_anim_duration]',
        'id'        => 'lgx_tooltip_anim_duration',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => 350
    )
);


$this->meta_form->number(
    array(
        'label'     => __( 'Animation Delay', 'logo-slider-wp'),
        'desc'      => __( 'Upon mouse interaction, this is the delay before the tooltip starts its opening and closing animations when the "hover" trigger is used.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_anim_delay]',
        'id'        => 'lgx_tooltip_anim_delay',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => 300
    )
);


$this->meta_form->select(
    array(
        'label'     => __( 'Trigger Type', 'logo-slider-wp'),
        'desc'      => __( 'Sets when the tooltip should open and close. If you want to use click trigger, you must be disabled the item company URL.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_trigger_type]',
        'id'        => 'lgx_tooltip_trigger_type',
        'default'   => 'hover',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'options'   => array(
            'hover' => __( 'Hover', 'logo-slider-wp'),
            'click' => __( 'Click', 'logo-slider-wp')
        )
    )
);


$this->meta_form->number(
    array(
        'label'     => __( 'Distance', 'logo-slider-wp'),
        'desc'      => __( 'The distance between the origin and the tooltip, in pixels. The value may be an integer or an array of integers (in the usual CSS syntax) if you wish to specify a different distance for each side.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_distance]',
        'id'        => 'lgx_tooltip_distance',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => 6
    )
);


$this->meta_form->color(
    array(
        'label'     => __( 'Text Color', 'logo-slider-wp'),
        'desc'      => __( 'Select tooltip text color', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_text_color]',
        'id'        => 'lgx_tooltip_text_color',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '#ffffff'
    )
);



$this->meta_form->text(
    array(
        'label'     => __( 'Padding', 'logo-slider-wp'),
        'desc'      => __( 'Add Tooltip inner padding.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_padding]',
        'id'        => 'lgx_tooltip_padding',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '8px'
    )
);

$this->meta_form->color(
    array(
        'label'     => __( 'Background Color', 'logo-slider-wp'),
        'desc'      => __( 'Select tooltip Background color', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_bg_color]',
        'id'        => 'lgx_tooltip_bg_color',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '#d3d3d3'
    )
);


$this->meta_form->color(
    array(
        'label'     => __( 'Border Color', 'logo-slider-wp'),
        'desc'      => __( 'Select tooltip border color', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_border_color]',
        'id'        => 'lgx_tooltip_border_color',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '#333333'
    )
);

$this->meta_form->text(
    array(
        'label'     => __( 'Border Width', 'logo-slider-wp'),
        'desc'      => __( 'Add Tooltip Width.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_border_width]',
        'id'        => 'lgx_tooltip_border_width',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '2px'
    )
);


$this->meta_form->text(
    array(
        'label'     => __( 'Border Radius', 'logo-slider-wp'),
        'desc'      => __( 'Add Tooltip Radius.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_border_radius]',
        'id'        => 'lgx_tooltip_border_radius',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '4px',
    )
);


$this->meta_form->number(
    array(
        'label'     => __( 'Minimum Intersection', 'logo-slider-wp'),
        'desc'      => __( 'Corresponds to the minimum distance to enforce between the center of the arrow and the edges of the tooltip. Mainly used to create an arrow bigger than those of the default themes.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_min_intersection]',
        'id'        => 'lgx_tooltip_min_intersection',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => 16
    )
);

$this->meta_form->number(
    array(
        'label'     => __( 'Timer', 'logo-slider-wp'),
        'desc'      => __( 'How long the tooltip should be allowed to live before hiding. Default: 0 (disabled)', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_timer]',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'id'        => 'lgx_tooltip_timer',
        'default'   => 0
    )
);


$this->meta_form->switch(
    array(
        'label'   => __( 'Arrow', 'logo-slider-wp'),
        'yes_label' => __( 'Enabled', 'logo-slider-wp'),
        'no_label' => __( 'Disabled', 'logo-slider-wp'),
        'desc'    => __( 'Add a speech bubble arrow to the tooltip.', 'logo-slider-wp'),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_tooltip_arrow_en]',
        'id'      => 'lgx_tooltip_arrow_en',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default' => 'yes'
    )
);


$this->meta_form->color(
    array(
        'label'     => __( 'Arrow Background Color', 'logo-slider-wp'),
        'desc'      => __( 'Select arrow background color', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_arrow_bg_color]',
        'id'        => 'lgx_tooltip_arrow_bg_color',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '#555555'
    )
);

$this->meta_form->color(
    array(
        'label'     => __( 'Arrow Border Color', 'logo-slider-wp'),
        'desc'      => __( 'Select arrow border color', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_tooltip_arrow_border_color]',
        'id'        => 'lgx_tooltip_arrow_border_color',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '#333333'
    )
);