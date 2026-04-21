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
        'label' => __( 'Enable Preloader', 'logo-slider-wp'),
        'yes_label' => __( 'Enabled', 'logo-slider-wp'),
        'no_label' => __( 'Disabled', 'logo-slider-wp'),
        'desc' => __( 'The showcase will be invisible until the page load complete.', 'logo-slider-wp'),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_preloader_en]',
        'id' => 'lgx_preloader_en',
        'default' => 'yes'
    )
);

$this->meta_form->upload(
    array(
        'label'   => __( 'Preloader Icon', 'logo-slider-wp'),
        'desc'    => __( 'Upload Background Icon for Preloader.', 'logo-slider-wp'),
        'name'    => 'meta_lgx_lsp_shortcodes[lgx_preloader_icon]',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'id'      => 'lgx_preloader_icon',
    )
);

$this->meta_form->color(
    array(
        'label'     => __( 'Preloader Background', 'logo-slider-wp'),
        'desc'      => __( 'Please select background color for Preloader.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_preloader_bg_color]',
        'id'        => 'lgx_preloader_bg_color',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '#ffffff',
    )
);

