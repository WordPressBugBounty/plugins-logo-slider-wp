<?php
if (!defined('WPINC')) {
    die;
}

/**
 *
 * Grab Logo Category for lgx_logo_item_category
 *
 */
$lgx_logo_slider_taxo = 'logosliderwpcat';
$lgx_logo_slider_terms = get_terms(
    array(
        'taxonomy' => $lgx_logo_slider_taxo,
        'orderby'  => 'id',
        'hide_empty'=> true,
    )

); // Get all terms of a taxonomy

$lgx_logo_slider_term_array = array(
    'all' => 'All'
);
if ($lgx_logo_slider_terms && !is_wp_error($lgx_logo_slider_terms)) {

    foreach ($lgx_logo_slider_terms as $term) {
        $lgx_logo_slider_term_array[$term->term_id] = $term->name;
    }

}

$this->meta_form->buy_pro(
    array(
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'link' => 'https://logichunt.com/product/wordpress-logo-slider/',
    )
);



$this->meta_form->switch(
    array(
        'label' => __( 'Brand Name', 'logo-slider-wp'),
        'desc' => __( 'Show brand name in your showcase.', 'logo-slider-wp'),
        'yes_label' => __( 'Show', 'logo-slider-wp'),
        'no_label' => __( 'Hide', 'logo-slider-wp'),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_brand_name_en]',
        'id' => 'lgx_brand_name_en',
        'default' => 'yes'

    )
);

$this->meta_form->switch(
    array(
        'label' => __( 'Brand Description', 'logo-slider-wp'),
        'yes_label' => __( 'Show', 'logo-slider-wp'),
        'no_label' => __( 'Hide', 'logo-slider-wp'),
        'desc' => __( 'Show Description in your showcase.', 'logo-slider-wp'),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_brand_desc_en]',
        'id' => 'lgx_brand_desc_en',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default' => 'no'
    )
);

$this->meta_form->switch(
    array(
        'label' => __( 'Company URL', 'logo-slider-wp'),
        'yes_label' => __( 'Enabled', 'logo-slider-wp'),
        'no_label' => __( 'Disabled', 'logo-slider-wp'),
        'desc' => __( 'Add Custom Company URL in your showcase.', 'logo-slider-wp'),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_company_url_en]',
        'id' => 'lgx_company_url_en',
        'default' => 'yes'
    )
);


$this->meta_form->select(
    array(
        'label'     => __( 'Link Target Type', 'logo-slider-wp'),
        'desc'      => __( 'Specifies where to open the link.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_target_type]',
        'id'        => 'lgx_target_type',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => '_self',
        'options'   => array(
            '_self' => __( 'Same Tab', 'logo-slider-wp'),
            '_blank' => __( 'New Tab', 'logo-slider-wp')
        )
    )
);


$this->meta_form->group2SelectText(
    array(
        'label'       => __( 'Logo Image Height', 'logo-slider-wp'),
        'desc'        => __( 'Set Height of the Logo image. Default: 100% . You can add your desired height with suitable unit. E.g. 100px or 10rem.', 'logo-slider-wp'),
        'id'          => 'lgx_logo_height_dimension',
        'name'        => 'meta_lgx_lsp_shortcodes[lgx_logo_height_dimension]',
        //'status'      => LGX_LS_PLUGIN_META_FIELD_PRO,

        'label_select' => 'Properties',
        'name_select' => 'meta_lgx_lsp_shortcodes[lgx_logo_height_property]',
        'id_select'   => 'lgx_logo_height_property',
        'default_select'   => 'max-height',
        'options_select'   => array(
            'max-height' => __( 'Max Height', 'logo-slider-wp'),
            'height_'   => __( 'Height (Pro)', 'logo-slider-wp'),
            'min-height_' => __( 'Min Height (Pro)', 'logo-slider-wp')
        ),

        'label_text' => 'Value',
        'name_text'  => 'meta_lgx_lsp_shortcodes[lgx_logo_height]',
        'id_text'    => 'lgx_logo_height',
        'default_text' => '100%'
    )
);


$this->meta_form->group2SelectText(
    array(
        'label'       => __( 'Logo Image Width', 'logo-slider-wp'),
        'desc'        => __( 'Set Width of the Logo image. Default: 100% . You can add your desired Width with suitable unit. E.g. 100px or 10rem.', 'logo-slider-wp'),
        'id'          => 'lgx_logo_width_dimension',
        'name'        => 'meta_lgx_lsp_shortcodes[lgx_logo_width_dimension]',
        //'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,

        'label_select' => 'Properties',
        'name_select' => 'meta_lgx_lsp_shortcodes[lgx_logo_width_property]',
        'id_select'   => 'lgx_logo_width_property',
        'default_select'   => 'max-width',
        'options_select'   => array(
            'max-width' => __( 'Max Width', 'logo-slider-wp'),
            'width_'   => __( 'Width (Pro)', 'logo-slider-wp'),
            'min-width_' => __( 'Min Width (Pro)', 'logo-slider-wp')
        ),

        'label_text' => 'Value',
        'name_text'  => 'meta_lgx_lsp_shortcodes[lgx_logo_width]',
        'id_text'    => 'lgx_logo_width',
        'default_text' => '100%'
    )
);


$this->meta_form->select(
    array(
        'label'     => __( 'Logo Position', 'logo-slider-wp'),
        'desc'      => __( 'Set Logo Image Position.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_logo_position]',
        'id'        => 'lgx_logo_position',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => 'top',
        'options'   => array(
            'top' => __( 'Top', 'logo-slider-wp'),
            'left' => __( 'Left', 'logo-slider-wp'),
            'right' => __( 'Right', 'logo-slider-wp'),
            'bottom' => __( 'Bottom', 'logo-slider-wp')
        )
    )
);// Added from v3.5.0

$this->meta_form->select(
    array(
        'label'     => __( 'Logo Info Align', 'logo-slider-wp'),
        'desc'      => __( 'Set Logo Title and description Alignment.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_logo_info_align]',
        'id'        => 'lgx_logo_info_align',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => 'center',
        'options'   => array(
            'center' => __( 'Center', 'logo-slider-wp'),
            'left' => __( 'Left', 'logo-slider-wp'),
            'right' => __( 'Right', 'logo-slider-wp')
        )
    )
);// Added from v3.5.0



$this->meta_form->switch(
    array(
        'label' => __( 'Add Nofollow to Link', 'logo-slider-wp'),
        'yes_label' => __( 'Yes', 'logo-slider-wp'),
        'no_label' => __( 'No', 'logo-slider-wp'),
        'desc' => __( 'Basically this is important for site SEO. "nofollow" is used by Google, to specify that the Google search spider should not follow that link.', 'logo-slider-wp'),
        'name' => 'meta_lgx_lsp_shortcodes[lgx_nofollow_en]',
        'id' => 'lgx_nofollow_en',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default' => 'no'
    )
);




/********************************************************************************/
$this->meta_form->header_spacer(
    array(
        'label'     => __( 'Query Settings', 'logo-slider-wp'),
    )
);
/********************************************************************************/


$this->meta_form->buy_pro(
    array(
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'link' => 'https://logichunt.com/product/wordpress-logo-slider/',
    )
);


$this->meta_form->select(
    array(
        'label'     => __( 'Select Logo Category', 'logo-slider-wp'),
        'desc'      => __( 'Filter Logo item by category.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_from_category]',
        'id'        => 'lgx_from_category',
        'status'  => LGX_LS_PLUGIN_META_FIELD_PRO,
        'default'   => 'all',
        'options'   => $lgx_logo_slider_term_array
    )
);

$this->meta_form->number(
    array(
        'label'     => __( 'Item Limit', 'logo-slider-wp'),
        'desc'      => __( 'Number of total logo item to show. Default: 0 ( all ). The free version allows displaying maximum of 20 images.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_limit]',
        'id'        => 'lgx_item_limit',
        'default'   => 0
    )
);

$this->meta_form->select(
    array(
        'label'     => __( 'Order By', 'logo-slider-wp'),
        'desc'      => __( 'Sort retrieved items by parameter.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_sort_order_by]',
        'id'        => 'lgx_item_sort_order_by',
        'default'   => 'menu_order',
        'options'   => array(
            'menu_order' => __( 'Drag & Drop', 'logo-slider-wp'),
            'title' => __( 'Title', 'logo-slider-wp'),
            'date' => __( 'Date', 'logo-slider-wp'),
            'rand_free' => __( 'Random (Pro)', 'logo-slider-wp')
        )
    )
);// Added from v3.2.0



$this->meta_form->select(
    array(
        'label'     => __( 'Order', 'logo-slider-wp'),
        'desc'      => __( 'Designates the ascending or descending order of the "orderby" parameter.', 'logo-slider-wp'),
        'name'      => 'meta_lgx_lsp_shortcodes[lgx_item_sort_order]',
        'id'        => 'lgx_item_sort_order',
        'default'   => 'ASC',
        'options'   => array(
            'ASC' => __( 'Ascending ', 'logo-slider-wp'),
            'DESC' => __( 'Descending  ', 'logo-slider-wp')
        )
    )
); // Added from v3.2.0