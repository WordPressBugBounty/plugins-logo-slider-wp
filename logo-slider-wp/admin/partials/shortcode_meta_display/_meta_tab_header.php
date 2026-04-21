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

$this->meta_form->switch(
    array(
        'yes_label' => __( 'Enabled', 'logo-slider-wp'),
        'no_label' => __( 'Disabled', 'logo-slider-wp'),
        'label'   => __( 'Section Header', 'logo-slider-wp'),
        'desc'    => __( 'Enable Header Section in your showcase.', 'logo-slider-wp'),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_header_en]',
        'id'      => 'lgx_header_en',
       // 'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default' => 'no'
    )
);

$this->meta_form->select(
    array(
        'label'     => __( 'Header Alignment', 'logo-slider-wp'),
        'desc'      => __( 'Section Header Alignment.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_header_align]',
        'id'        => 'lgx_header_align',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => 'center',
        'options'   => array(
            'center' => __( 'Center', 'logo-slider-wp'),
            'left' => __( 'Left', 'logo-slider-wp'),
            'right' => __( 'Right', 'logo-slider-wp')
        )
    )
);


$this->meta_form->text(
    array(
        'label'     => __( 'Title', 'logo-slider-wp'),
        'desc'      => __( 'Add your section header title.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_header_title]',
        'id'        => 'lgx_header_title',
//'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => ''
    )
);


$this->meta_form->text(
    array(
        'label'     => __( 'Title Font Size', 'logo-slider-wp'),
        'desc'      => __( 'Add Title Font Size.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_header_title_font_size]',
        'id'        => 'lgx_header_title_font_size',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '42px'
    )
);



$this->meta_form->color(
    array(
        'label'     => __( 'Title Color', 'logo-slider-wp'),
        'desc'      => __( 'Please select title color.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_header_title_color]',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'id'        => 'lgx_header_title_color',
        'default'   => '#010101',
    )
);


$this->meta_form->text(
    array(
        'label'     => __( 'Title Font Weight', 'logo-slider-wp'),
        'desc'      => __( 'Set Title Font Weight.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_header_title_font_weight]',
        'id'        => 'lgx_header_title_font_weight',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '500'
    )
);

$this->meta_form->text(
    array(
        'label'     => __( 'Title Bottom Margin', 'logo-slider-wp'),
        'desc'      => __( 'Add Title Font Size.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_header_title_bottom_margin]',
        'id'        => 'lgx_header_title_bottom_margin',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '10px'
    )
);


$this->meta_form->text(
    array(
        'label'     => __( 'Sub Title', 'logo-slider-wp'),
        'desc'      => __( 'Add your section header Sub title.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_header_subtitle]',
        'id'        => 'lgx_header_subtitle',
       // 'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => ''
    )
);


$this->meta_form->text(
    array(
        'label'     => __( 'Sub Title Font Size', 'logo-slider-wp'),
        'desc'      => __( 'Add Sub Title Font Size.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_header_subtitle_font_size]',
        'id'        => 'lgx_header_subtitle_font_size',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '16px'
    )
);



$this->meta_form->color(
    array(
        'label'     => __( 'Sub Title Color', 'logo-slider-wp'),
        'desc'      => __( 'Please select Sub title color.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_header_subtitle_color]',
        'id'        => 'lgx_header_subtitle_color',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '#888888',
    )
);


$this->meta_form->text(
    array(
        'label'     => __( 'Sub Title Font Weight', 'logo-slider-wp'),
        'desc'      => __( 'Set Sub Title Font Weight.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_header_subtitle_font_weight]',
        'id'        => 'lgx_header_subtitle_font_weight',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '400'
    )
);

$this->meta_form->text(
    array(
        'label'     => __( 'Sub Title Bottom Margin', 'logo-slider-wp'),
        'desc'      => __( 'Add Sub Title Font Size.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_header_subtitle_bottom_margin]',
        'id'        => 'lgx_header_subtitle_bottom_margin',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '35px'
    )
);

