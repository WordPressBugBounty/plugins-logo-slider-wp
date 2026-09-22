<?php
if (!defined('WPINC')) {
    die;
}


//Tooltip

$lgx_tooltip_arrow_bg_color               = lgx_ls_sanitize_css_color($lgx_shortcodes_meta['lgx_tooltip_arrow_bg_color']);
$lgx_tooltip_arrow_border_color            = lgx_ls_sanitize_css_color($lgx_shortcodes_meta['lgx_tooltip_arrow_border_color']);
$lgx_tooltip_bg_color                      = lgx_ls_sanitize_css_color($lgx_shortcodes_meta['lgx_tooltip_bg_color']);
$lgx_tooltip_border_color                  = lgx_ls_sanitize_css_color($lgx_shortcodes_meta['lgx_tooltip_border_color']);
$lgx_tooltip_text_color                    = lgx_ls_sanitize_css_color($lgx_shortcodes_meta['lgx_tooltip_text_color']);


// Image Align
$lgx_grid_image_vertical_align = (isset($lgx_shortcodes_meta['lgx_grid_image_vertical_align']) ? $lgx_shortcodes_meta['lgx_grid_image_vertical_align'] : 'start');

// Logo Height/Width: moved to general-style.php (which always renders, in
// both editions) so these 4 real fields' CSS is genuinely edition-agnostic --
// see general-style.php for the variable computation, the emission rule, and
// the downgrade-safe guard on lgx_logo_width_property. Nothing left here.

$lgx_lsw_dynamic_style_pro = '';


$lgx_lsw_dynamic_style_pro_hide_dot = '@media (min-width: 767px) {
        #lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_layout_carousel .lgx_lsw_pagination {
         display:none;
        }
    }';

$lgx_lsw_dynamic_style_pro_hide_nav = '@media (min-width: 767px) {
        #lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_layout_carousel .lgx_lsw_nav_wrap {
         display:none;
        }
    }';



$lgx_lsw_dynamic_style_pro .=(($lgx_shortcodes_meta['lgx_carousel_pagi_mobile_en'] == 'yes') ?  $lgx_lsw_dynamic_style_pro_hide_dot : '');
$lgx_lsw_dynamic_style_pro .= (($lgx_shortcodes_meta['lgx_carousel_nav_mobile_en'] == 'yes') ?  $lgx_lsw_dynamic_style_pro_hide_nav : '');


// Nav hover-color (B104): moved to general-style.php (which always renders,
// in both editions) so these 3 real fields' CSS is genuinely edition-agnostic
// -- see general-style.php for the emission rule. Nothing left here.

$lgx_lsw_dynamic_style_pro .= '.tooltipster-sidetip.lgx_lsw_tooltipster_'. $lgx_app_id.' .tooltipster-box {
        '. lgx_ls_css_decl('background', $lgx_tooltip_bg_color) .'
        '. lgx_ls_css_decl('border', $lgx_shortcodes_meta['lgx_tooltip_border_width'].' solid '.$lgx_tooltip_border_color, $lgx_tooltip_border_color) .'
        border-radius: '. $lgx_shortcodes_meta['lgx_tooltip_border_radius'].';
        box-shadow: 5px 5px 2px 0 rgba(0,0,0,0.4);
    }';

$lgx_lsw_dynamic_style_pro .= '.tooltipster-sidetip.lgx_lsw_tooltipster_'. $lgx_app_id.' .tooltipster-content {
        '. lgx_ls_css_decl('color', $lgx_tooltip_text_color) .'
        padding: '. $lgx_shortcodes_meta['lgx_tooltip_padding'].';
    }';

$lgx_lsw_dynamic_style_pro .= ' .tooltipster-sidetip.lgx_lsw_tooltipster_'. $lgx_app_id.'.tooltipster-bottom .tooltipster-arrow-border {
        '. lgx_ls_css_decl('border-bottom-color', $lgx_tooltip_arrow_border_color) .'
    }';

$lgx_lsw_dynamic_style_pro .= '.tooltipster-sidetip.lgx_lsw_tooltipster_'. $lgx_app_id.'.tooltipster-right .tooltipster-arrow-border {
        '. lgx_ls_css_decl('border-right-color', $lgx_tooltip_arrow_border_color) .'
    }';

$lgx_lsw_dynamic_style_pro .= '.tooltipster-sidetip.lgx_lsw_tooltipster_'. $lgx_app_id.'.tooltipster-left .tooltipster-arrow-border {
        '. lgx_ls_css_decl('border-left-color', $lgx_tooltip_arrow_border_color) .'
    }';

$lgx_lsw_dynamic_style_pro .= '.tooltipster-sidetip.lgx_lsw_tooltipster_'. $lgx_app_id.'.tooltipster-top .tooltipster-arrow-border {
        '. lgx_ls_css_decl('border-top-color', $lgx_tooltip_arrow_border_color) .'
    }';

$lgx_lsw_dynamic_style_pro .= '.tooltipster-sidetip.lgx_lsw_tooltipster_'. $lgx_app_id.'.tooltipster-bottom .tooltipster-arrow-background {
        '. lgx_ls_css_decl('border-bottom-color', $lgx_tooltip_arrow_bg_color) .'
    }';

$lgx_lsw_dynamic_style_pro .= '.tooltipster-sidetip.lgx_lsw_tooltipster_'. $lgx_app_id.'.tooltipster-right .tooltipster-arrow-background  {
        '. lgx_ls_css_decl('border-right-color', $lgx_tooltip_arrow_bg_color) .'
    }';

$lgx_lsw_dynamic_style_pro .= '.tooltipster-sidetip.lgx_lsw_tooltipster_'. $lgx_app_id.'.tooltipster-left .tooltipster-arrow-background  {
        '. lgx_ls_css_decl('border-left-color', $lgx_tooltip_arrow_bg_color) .'
    }';
    
$lgx_lsw_dynamic_style_pro .= '.tooltipster-sidetip.lgx_lsw_tooltipster_'. $lgx_app_id.'.tooltipster-top .tooltipster-arrow-background  {
        '. lgx_ls_css_decl('border-top-color', $lgx_tooltip_arrow_bg_color) .'
    }';

    //Grid
    $lgx_lsw_dynamic_style_pro .= '#lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_layout_grid .lgx_app_item_figure{
        display: inline-flex;
        justify-content: center;
        align-items: '. $lgx_grid_image_vertical_align .';
    }';


/**
 *  Inline Style
 */

echo '<style>' . wp_strip_all_tags( $lgx_lsw_dynamic_style_pro ) . '</style>';