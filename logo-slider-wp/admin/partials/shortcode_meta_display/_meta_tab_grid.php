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
        'label'     => __( 'Grid Style', 'logo-slider-wp'),
        'desc'      => __( 'Select grid style for showcase item.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_grid_style]',
        'id'        => 'lgx_grid_style',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => 'simple',
        'options'   => array(
            'simple' => __( 'Simple', 'logo-slider-wp'),
            'outline' => __( 'Outline', 'logo-slider-wp'),
            'inner_outline' => __( 'Inner Outline', 'logo-slider-wp'),
        )
    )
);

$this->meta_form->color(
    array(
        'label'     => __( 'Outline Color', 'logo-slider-wp'),
        'desc'      => __( 'Please select color for the outline or inner outline style.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_grid_outline_color]',
        'id'        => 'lgx_grid_outline_color',
        'default'   => '#e9e9e9',
    )
);

$this->meta_form->text(
    array(
        'label'     => __( 'Column Gap', 'logo-slider-wp'),
        'desc'      => __( 'Sets the gap between the columns.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_grid_column_gap]',
        'id'        => 'lgx_grid_column_gap',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '15px'
    )
);

$this->meta_form->text(
    array(
        'label'     => __( 'Row Gap', 'logo-slider-wp'),
        'desc'      => __( 'Sets the gap between the columns.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_grid_row_gap]',
        'id'        => 'lgx_grid_row_gap',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '15px'
    )
);

$this->meta_form->text(
    array(
        'label'     => __( 'Item Min Height', 'logo-slider-wp'),
        'desc'      => __( 'Set Minimum Height of the item. Default: 0 [Disabled]. Please add your desired height with unit.E.g. 100px/ 10rem / 10em. :  If you do not want this option, just add 0', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_grid_item_min_height]',
        'id'        => 'lgx_grid_item_min_height',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => 0
    )
);

$this->meta_form->select(
    array(
        'label' => __( 'Item Image Vertical Align', 'logo-slider-wp'),
        'desc' => __( 'Set Grid item image vertical alignment.<br> <span style="color: #e31919">Note: It helps if the size of the images are not equal.</span>', 'logo-slider-wp'),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_grid_image_vertical_align]',
        'id' => 'lgx_grid_image_vertical_align',
        'default'   => 'start',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'options'   => array(
            'start' => __( 'Top', 'logo-slider-wp'),
            'center' => __( 'Vertically Middle', 'logo-slider-wp'),
            'end' => __( 'Bottom', 'logo-slider-wp'),
        )
    )
);

