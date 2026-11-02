<?php
if (!defined('WPINC')) {
    die;
}

// Flex Value

$lgx_flexbox_column_gap         = (isset($lgx_shortcodes_meta['lgx_flexbox_column_gap']) ? $lgx_shortcodes_meta['lgx_flexbox_column_gap'] : '15px');
$lgx_flexbox_row_gap            = (isset($lgx_shortcodes_meta['lgx_flexbox_row_gap']) ? $lgx_shortcodes_meta['lgx_flexbox_row_gap'] : '15px');
$lgx_flexbox_align_items        = (isset($lgx_shortcodes_meta['lgx_flexbox_align_items']) ? $lgx_shortcodes_meta['lgx_flexbox_align_items'] : 'flex-start');
$lgx_flexbox_justify_content    = (isset($lgx_shortcodes_meta['lgx_flexbox_justify_content']) ? $lgx_shortcodes_meta['lgx_flexbox_justify_content'] : 'flex-start');
$lgx_flexbox_wrap               = (isset($lgx_shortcodes_meta['lgx_flexbox_wrap']) ? $lgx_shortcodes_meta['lgx_flexbox_wrap'] : 'wrap');
$lgx_flexbox_direction          = (isset($lgx_shortcodes_meta['lgx_flexbox_direction']) ? $lgx_shortcodes_meta['lgx_flexbox_direction'] : 'row');


// Value
$lgx_grid_column_gap     = $lgx_shortcodes_meta['lgx_grid_column_gap'];
$lgx_grid_row_gap        = $lgx_shortcodes_meta['lgx_grid_row_gap'];

// T-292 (B118): bcdiv() throws DivisionByZeroError on PHP 8 for 0. A missing, empty, non-numeric, 0 or negative
// column count falls back, for the flexbox division only, to the default the save handler uses (5/4/3/2).
$lgx_flex_large_desktop_item = (intval($lgx_large_desktop_item) >= 1) ? intval($lgx_large_desktop_item) : 5;
$lgx_flex_desktop_item       = (intval($lgx_desktop_item) >= 1) ? intval($lgx_desktop_item) : 4;
$lgx_flex_tablet_item        = (intval($lgx_tablet_item) >= 1) ? intval($lgx_tablet_item) : 3;
$lgx_flex_mobile_item        = (intval($lgx_mobile_item) >= 1) ? intval($lgx_mobile_item) : 2;

$lgx_lsw_dynamic_style_grid = '';
$lgx_lsw_dynamic_style_grid .= '#lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_layout_flexbox .lgx_app_item_row{
                        -ms-flex-wrap: '.$lgx_flexbox_wrap .';
                        flex-wrap: '.$lgx_flexbox_wrap .';
                        gap: '.$lgx_flexbox_row_gap .' '.$lgx_flexbox_column_gap .';
                        align-items: '.$lgx_flexbox_align_items .'; 
                        justify-content: '.$lgx_flexbox_justify_content .';
                        flex-direction: '.$lgx_flexbox_direction.';
                        }';

    $lgx_lsw_dynamic_style_grid .= '#lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_layout_flexbox .lgx_app_item {
        -ms-flex: 0 0 calc('.bcdiv('100', $lgx_flex_large_desktop_item, 8).'% - '.$lgx_flexbox_column_gap .');
        flex: 0 0 calc('.bcdiv('100', $lgx_flex_large_desktop_item, 8).'% - '.$lgx_flexbox_column_gap .');
        width: '.bcdiv('100', $lgx_flex_large_desktop_item, 8).'%;
    }';


$lgx_lsw_dynamic_style_grid .= '@media (max-width: 767px) {
        #lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_layout_flexbox .lgx_app_item {
            -ms-flex: 0 0 calc('.bcdiv('100', $lgx_flex_mobile_item, 8).'% - '.$lgx_flexbox_column_gap .');
            flex: 0 0 calc('.bcdiv('100', $lgx_flex_mobile_item, 8).'% - '.$lgx_flexbox_column_gap .');
            width: '.bcdiv('100', $lgx_flex_mobile_item, 8).'%;
        }
        
    }';
$lgx_lsw_dynamic_style_grid .= '@media (min-width: 768px) {

        #lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_layout_flexbox .lgx_app_item {
            -ms-flex: 0 0 calc('.bcdiv('100', $lgx_flex_tablet_item, 8).'% - '.$lgx_flexbox_column_gap .');
            flex: 0 0 calc('.bcdiv('100', $lgx_flex_tablet_item, 8).'% - '.$lgx_flexbox_column_gap .');
            width: '.bcdiv('100', $lgx_flex_tablet_item, 8).'%;
        }
    }';
$lgx_lsw_dynamic_style_grid .= '@media (min-width: 992px) {
        #lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_layout_flexbox .lgx_app_item {
            -ms-flex: 0 0 calc('.bcdiv('100', $lgx_flex_desktop_item, 8).'% - '.$lgx_flexbox_column_gap .');
            flex: 0 0 calc('.bcdiv('100', $lgx_flex_desktop_item, 8).'% - '.$lgx_flexbox_column_gap .');
            width: '.bcdiv('100', $lgx_flex_desktop_item, 8).'%;
        }
    }';
$lgx_lsw_dynamic_style_grid .= '@media (min-width: 1200px) {
        #lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_layout_flexbox .lgx_app_item {
            -ms-flex: 0 0 calc('.bcdiv('100', $lgx_flex_large_desktop_item, 8).'% - '.$lgx_flexbox_column_gap .');
            flex: 0 0 calc('.bcdiv('100', $lgx_flex_large_desktop_item, 8).'% - '.$lgx_flexbox_column_gap .');
            width: '.bcdiv('100', $lgx_flex_large_desktop_item, 8).'%;
        }
    }';



/**
 *  Inline Style
 */

echo '<style>' . wp_strip_all_tags( $lgx_lsw_dynamic_style_grid ) . '</style>';