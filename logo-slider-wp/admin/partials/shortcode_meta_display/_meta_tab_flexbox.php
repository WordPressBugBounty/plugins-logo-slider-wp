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

$this->meta_form->text(
    array(
        'label'     => __( 'Column Gap', 'logo-slider-wp'),
        'desc'      => __( 'Sets the gap between the columns. Add your desired value with suitable unit. E.g. 15px or 1.5rem.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_flexbox_column_gap]',
        'id'        => 'lgx_flexbox_column_gap',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '15px'
    )
);

$this->meta_form->text(
    array(
        'label'     => __( 'Row Gap', 'logo-slider-wp'),
        'desc'      => __( 'Sets the gap between the row. Add your desired value with suitable unit. E.g. 15px or 1.5rem', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_flexbox_row_gap]',
        'id'        => 'lgx_flexbox_row_gap',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '15px'
    )
);

$this->meta_form->select(
    array(
        'label' => __( 'Vertical Alignment', 'logo-slider-wp'),
        'desc' => __( 'Set flexible items vertical alignment ( Align).<br> <span style="color: #e31919">Note: It helps if the size of the flexible items are not equal.</span>', 'logo-slider-wp'),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_flexbox_align_items]',
        'id' => 'lgx_flexbox_align_items',
        'default'   => 'flex-start',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'options'   => array(
            'flex-start' => __( 'Top', 'logo-slider-wp'),
            'center' => __( 'Vertically Middle', 'logo-slider-wp'),
            'flex-end' => __( 'Bottom', 'logo-slider-wp'),
        )
    )
);

$this->meta_form->select(
    array(
        'label' => __( 'Horizontal Alignment', 'logo-slider-wp'),
        'desc' => __( 'Set flexible items horizontal alignment ( Justify ).<br> <span style="color: #e31919">Note: It helps to align row items horizontally.</span>', 'logo-slider-wp'),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_flexbox_justify_content]',
        'id' => 'lgx_flexbox_justify_content',
        'default'   => 'flex-start',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'options'   => array(
            'flex-start' => __( 'Left', 'logo-slider-wp'),
            'center' => __( ' Center', 'logo-slider-wp'),
            'flex-end' => __( 'Right', 'logo-slider-wp'),
        )
    )
);


$this->meta_form->select(
    array(
        'label' => __( 'Flex Wrap', 'logo-slider-wp'),
        'desc' => __( 'Make the flexible items single or multi-line.', 'logo-slider-wp'),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_flexbox_wrap]',
        'id' => 'lgx_flexbox_wrap',
        'default'   => 'wrap',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'options'   => array(
            'wrap' => __( 'Wrap', 'logo-slider-wp'),
            'wrap-reverse' => __( 'Wrap Reverse', 'logo-slider-wp'),
            'nowrap' => __( 'No wrap', 'logo-slider-wp'),
        )
    )
);



$this->meta_form->select(
    array(
        'label' => __( 'Flex Direction', 'logo-slider-wp'),
        'desc' => __( 'Set the direction of the flexible items.', 'logo-slider-wp'),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_flexbox_direction]',
        'id' => 'lgx_flexbox_direction',
        'default'   => 'row',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'options'   => array(
            'row' => __( 'Row', 'logo-slider-wp'),
            'row-reverse' => __( 'Row Reverse', 'logo-slider-wp'),
            'column' => __( 'Column', 'logo-slider-wp'),
            'column-reverse' => __( 'Column Reverse', 'logo-slider-wp'),
        )
    )
);


