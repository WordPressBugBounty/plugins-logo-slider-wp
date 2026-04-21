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
        'label'     => __( 'Hover Effect', 'logo-slider-wp'),
        'desc'      => __( 'Select hover effect for showcase item', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_hover_effect]',
        'id'        => 'lgx_item_hover_effect',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => 'none',
        'options'   => array(
            'none' => __( 'None', 'logo-slider-wp'),
            'gray_hover' => __( 'Grayscale On Over', 'logo-slider-wp'),
            'gray_remove' => __( 'Grayscale Remove On Over', 'logo-slider-wp'),
            'gray_always' => __( 'Grayscale Always', 'logo-slider-wp'),
            'box_shadow' => __( 'Box Shadow', 'logo-slider-wp'),
            'box_shadow_always' => __( 'Box Shadow Always', 'logo-slider-wp'),
            'overlay_link' => __( 'Hover Overlay Link', 'logo-slider-wp'),
            'overlay_title' => __( 'Hover Overlay Link with Title', 'logo-slider-wp'),
        )
    )
);


$this->meta_form->select(
    array(
        'label'     => __( 'Hover Animation', 'logo-slider-wp'),
        'desc'      => __( 'Select hover animation for showcase logo image.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_hover_anim]',
        'status'    => LGX_LS_PLUGIN_META_FIELD_PRO,
        'id'        => 'lgx_item_hover_anim',
        'default'   => 'default',
        'options'   => array(
            'default'       => __( 'Default', 'logo-slider-wp'),
            'none'          => __( 'None', 'logo-slider-wp'),
            'scaleup'       => __( 'Scale Up', 'logo-slider-wp'),
            'bounce'        => __( 'Bounce', 'logo-slider-wp'),
            'flash'         => __( 'Flash', 'logo-slider-wp'),
            'pulse'         => __( 'Pulse', 'logo-slider-wp'),
            'rubberBand'    => __( 'Rubber Band', 'logo-slider-wp'),
            'shakeX'        => __( 'ShakeX', 'logo-slider-wp'),
            'shakeY'        => __( 'ShakeY', 'logo-slider-wp'),
            'headShake'     => __( 'Head Shake', 'logo-slider-wp'),
            'swing'         => __( 'Swing', 'logo-slider-wp'),
            'tada'          => __( 'Tada', 'logo-slider-wp'),
            'wobble'        => __( 'Wobble', 'logo-slider-wp'),
            'jello'         => __( 'Jello', 'logo-slider-wp'),
            'heartBeat'     => __( 'Heart Beat', 'logo-slider-wp'),
            'backInDown'    => __( 'Back In Down', 'logo-slider-wp'),
            'backInLeft'    => __( 'Back In Left', 'logo-slider-wp'),
            'backInRight'   => __( 'Back In Right', 'logo-slider-wp'),
            'backInUp'      => __( 'Back In Up ', 'logo-slider-wp'),
            'bounceIn'      => __( 'Bounce In ', 'logo-slider-wp'),
            'bounceInDown'  => __( 'Bounce In Down ', 'logo-slider-wp'),
            'bounceInLeft'  => __( 'Bounce In Left ', 'logo-slider-wp'),
            'bounceInRight' => __( 'Bounce In Right ', 'logo-slider-wp'),
            'bounceInUp'    => __( 'Bounce In Up ', 'logo-slider-wp'),
            'fadeIn'        => __( 'Fade In ', 'logo-slider-wp'),
            'fadeInDown'    => __( 'Fade In Down ', 'logo-slider-wp'),
            'fadeInDownBig' => __( 'Fade In Down Big ', 'logo-slider-wp'),
            'fadeInLeft'    => __( 'Fade In Left ', 'logo-slider-wp'),
            'fadeInLeftBig' => __( 'Fade In Left Big ', 'logo-slider-wp'),
            'fadeInRight'   => __( 'Fade In Right ', 'logo-slider-wp'),
            'fadeInRightBig'=> __( 'Fade In Right Big ', 'logo-slider-wp'),
            'fadeInUp'      => __( 'Fade In Up ', 'logo-slider-wp'),
            'fadeInUpBig'   => __( 'Fade In Up Big ', 'logo-slider-wp'),
            'fadeInTopLeft' => __( 'Fade In Top Left ', 'logo-slider-wp'),
            'fadeInTopRight'=> __( 'Fade In Top Right ', 'logo-slider-wp'),
            'fadeInBottomLeft'  => __( 'Fade In Bottom Left ', 'logo-slider-wp'),
            'fadeInBottomRight' => __( 'Fade In Bottom Right ', 'logo-slider-wp'),
            'flip'              => __( 'Flip', 'logo-slider-wp'),
            'flipInX'           => __( 'Flip InX', 'logo-slider-wp'),
            'lightSpeedInRight' => __( 'Light Speed In Right', 'logo-slider-wp'),
            'lightSpeedInLeft'  => __( 'Light Speed In Left', 'logo-slider-wp'),
            'rotateIn'          => __( 'Rotate In', 'logo-slider-wp'),
            'rotateInDownLeft'  => __( 'Rotate In Down Left', 'logo-slider-wp'),
            'rotateInDownRight' => __( 'Rotate In Down Right', 'logo-slider-wp'),
            'rotateInUpLeft'    => __( 'Rotate In Up Left', 'logo-slider-wp'),
            'rotateInUpRight'   => __( 'Rotate In Up Right', 'logo-slider-wp'),
            'hinge'             => __( 'Hinge', 'logo-slider-wp'),
            'jackInTheBox'      => __( 'Jack In TheBox', 'logo-slider-wp'),
            'rollIn'            => __( 'Roll In', 'logo-slider-wp'),
            'zoomIn'            => __( 'Zoom In', 'logo-slider-wp'),
            'zoomInDown'        => __( 'Zoom In Down', 'logo-slider-wp'),
            'zoomInLeft'        => __( 'Zoom In Left', 'logo-slider-wp'),
            'zoomInRight'       => __( 'Zoom In Right', 'logo-slider-wp'),
            'zoomInUp'          => __( 'Zoom In Up', 'logo-slider-wp'),
            'slideInDown'       => __( 'Slide In Down', 'logo-slider-wp'),
            'slideInLeft'       => __( 'Slide In Left', 'logo-slider-wp'),
            'slideInRight'      => __( 'Slide In Right', 'logo-slider-wp'),
            'slideInUp'         => __( 'Slide In Up', 'logo-slider-wp')
        )
    )
);



/********************************************************************************/
$this->meta_form->header_spacer(
    array(
        'label'     => __( 'Item Title  Settings', 'logo-slider-wp'),
    )
);
/********************************************************************************/

$this->meta_form->buy_pro(
    array(
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'link' => 'https://logichunt.com/product/wordpress-logo-slider/',
    )
);


$this->meta_form->color(
    array(
        'label'     => __( 'Title Color', 'logo-slider-wp'),
        'desc'      => __( 'Please select Item Brand Name Color.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_brand_name_color]',
        'id'        => 'lgx_item_brand_name_color',
        'default'   => '#111111',
    )
);

$this->meta_form->text(
    array(
        'label'     => __( 'Font Size', 'logo-slider-wp'),
        'desc'      => __( 'Add Item Brand Name Font Size.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_brand_name_font_size]',
        'id'        => 'lgx_item_brand_name_font_size',
        'default'   => '20px'
    )
);

$this->meta_form->text(
    array(
        'label'     => __( 'Font Weight', 'logo-slider-wp'),
        'desc'      => __( 'Set Item Brand Name Font Weight.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_brand_name_font_weight]',
        'id'        => 'lgx_item_brand_name_font_weight',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '600'
    )
);

$this->meta_form->text(
    array(
        'label'     => __( 'Bottom Margin', 'logo-slider-wp'),
        'desc'      => __( 'Set bottom margin for showcase title.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_bottom_margin_title]',
        'id'        => 'lgx_item_bottom_margin_title',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '0px'
    )
);


/********************************************************************************/
$this->meta_form->header_spacer(
    array(
        'label'     => __( 'Item Description  Settings', 'logo-slider-wp'),
    )
);
/********************************************************************************/
$this->meta_form->buy_pro(
    array(
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'link' => 'https://logichunt.com/product/wordpress-logo-slider/',
    )
);



$this->meta_form->color(
    array(
        'label'     => __( 'Description Color', 'logo-slider-wp'),
        'desc'      => __( 'Please select Item Description Color.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_desc_color]',
        'id'        => 'lgx_item_desc_color',
        'default'   => '#555555',
    )
);


$this->meta_form->text(
    array(
        'label'     => __( 'Description Font Size', 'logo-slider-wp'),
        'desc'      => __( 'Add Item Description Font Size.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_desc_font_size]',
        'id'        => 'lgx_item_desc_font_size',
        'default'   => '20px'
    )
);

$this->meta_form->text(
    array(
        'label'     => __( 'Description Font Weight', 'logo-slider-wp'),
        'desc'      => __( 'Set Item Description Font Weight.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_desc_font_weight]',
        'id'        => 'lgx_item_desc_font_weight',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '400'
    )
);

$this->meta_form->text(
    array(
        'label'     => __( 'Description Bottom Margin', 'logo-slider-wp'),
        'desc'      => __( 'Set bottom margin for showcase description.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_bottom_margin_desc]',
        'id'        => 'lgx_item_bottom_margin_desc',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '0px'
    )
);


/********************************************************************************/
$this->meta_form->header_spacer(
    array(
        'label'     => __( 'Logo Image Border Settings', 'logo-slider-wp'),
    )
);
/********************************************************************************/


$this->meta_form->switch(
    array(
        'yes_label' => __( 'Enabled', 'logo-slider-wp'),
        'no_label' => __( 'Disabled', 'logo-slider-wp'),
        'label'     => __( 'Image Border', 'logo-slider-wp'),
        'desc'      => __( 'Enable Border for all logo Image.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_img_border_color_en]',
        'id'        => 'lgx_img_border_color_en',
        'default'   => 'no'
    )
);


$this->meta_form->color(
    array(
        'label'     => __( 'Image Border Color', 'logo-slider-wp'),
        'desc'      => __( 'Choose border color for logo image.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_img_border_color]',
        'id'        => 'lgx_img_border_color',
        'default'   => '#FF5151',
    )
);


$this->meta_form->color(
    array(
        'label'     => __( 'Image Border Hover Color', 'logo-slider-wp'),
        'desc'      => __( 'Choose on hover. border color for logo Image', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_img_border_color_hover]',
        'id'        => 'lgx_img_border_color_hover',
        'default'   => '#FF9B6A',
    )
);

$this->meta_form->text(
    array(
        'label'     => __( 'Image Border Width', 'logo-slider-wp'),
        'desc'      => __( 'Set Border Width for Showcase Logo Image.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_img_border_width]',
        'id'        => 'lgx_img_border_width',
        //'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '1px'
    )
);


$this->meta_form->text(
    array(
        'label'     => __( 'Image Border Radius', 'logo-slider-wp'),
        'desc'      => __( 'Set Border Radius for showcase logo Image.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_img_border_radius]',
        'id'        => 'lgx_img_border_radius',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '4px'
    )
);



/********************************************************************************/
$this->meta_form->header_spacer(
    array(
        'label'     => __( 'Item  Border Settings', 'logo-slider-wp'),
    )
);
/********************************************************************************/
$this->meta_form->buy_pro(
    array(
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'link' => 'https://logichunt.com/product/wordpress-logo-slider/',
    )
);


$this->meta_form->switch(
    array(
        'label'     => __( 'Item Border', 'logo-slider-wp'),
        'yes_label' => __( 'Enabled', 'logo-slider-wp'),
        'no_label' => __( 'Disabled', 'logo-slider-wp'),
        'desc'      => __( 'Enable Border for all logo item.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_border_color_en]',
        'id'        => 'lgx_border_color_en',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => 'no'
    )
);


$this->meta_form->color(
    array(
        'label'     => __( 'Item Border Color', 'logo-slider-wp'),
        'desc'      => __( 'Choose border color for logo item.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_border_color]',
        'id'        => 'lgx_border_color',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '#161E54',
    )
);

$this->meta_form->color(
    array(
        'label'     => __( 'Item Hover Border Color', 'logo-slider-wp'),
        'desc'      => __( 'Choose on hover. border color for full item area', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_border_color_hover]',
        'id'        => 'lgx_item_border_color_hover',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '#161E54',
    )
);


$this->meta_form->text(
    array(
        'label'     => __( 'Item Border Width', 'logo-slider-wp'),
        'desc'      => __( 'Set Border Width for showcase logo item.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_border_width]',
        'id'        => 'lgx_item_border_width',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '1px'
    )
);


$this->meta_form->text(
    array(
        'label'     => __( 'Item Border Radius', 'logo-slider-wp'),
        'desc'      => __( 'Set Border Radius for showcase logo item.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_border_radius]',
        'id'        => 'lgx_item_border_radius',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '4px'
    )
);



/********************************************************************************/
$this->meta_form->header_spacer(
    array(
        'label'     => __( 'Item Background Settings', 'logo-slider-wp'),
    )
);
/********************************************************************************/

$this->meta_form->switch(
    array(
        'label'     => __( 'Item Background Color', 'logo-slider-wp'),
        'yes_label' => __( 'Enabled', 'logo-slider-wp'),
        'no_label' => __( 'Disabled', 'logo-slider-wp'),
        'desc'      => __( 'Enable Background Color for all logo item.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_bg_color_en]',
        'id'        => 'lgx_item_bg_color_en',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => 'no'
    )
);




$this->meta_form->color(
    array(
        'label'     => __( 'Item Background Color', 'logo-slider-wp'),
        'desc'      => __( 'Please select logo item background color.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_bg_color]',
        'id'        => 'lgx_item_bg_color',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '#f1f1f1',
    )
);

$this->meta_form->color(
    array(
        'label'     => __( 'Item Hover Background Color', 'logo-slider-wp'),
        'desc'      => __( 'Please select logo item background color on hover.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_bg_color_hover]',
        'id'        => 'lgx_item_bg_color_hover',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '#f1f1f1',
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

$this->meta_form->text(
    array(
        'label'     => __( 'Logo Item padding', 'logo-slider-wp'),
        'desc'      => __( 'Set item inner padding for showcase logo item.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_padding]',
        'id'        => 'lgx_item_padding',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '0px'
    )
);



$this->meta_form->text(
    array(
        'label'     => __( 'Logo Item Margin', 'logo-slider-wp'),
        'desc'      => __( 'Set Margin for showcase logo item.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_margin]',
        'id'        => 'lgx_item_margin',
        //'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '0px'
    )
);
