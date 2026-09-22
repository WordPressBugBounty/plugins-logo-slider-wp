<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if (!defined('WPINC')) {
    die;
}

wp_enqueue_style( 'lgx-logo-slider-style');
wp_enqueue_script( 'logo-slider-wp-public');






//Section Background Settings
$lgx_section_width              = $lgx_shortcodes_meta['lgx_section_width'];
$lgx_section_container          = $lgx_shortcodes_meta['lgx_section_container'];
$lgx_section_bg_img_en          = $lgx_shortcodes_meta['lgx_section_bg_img_en'];
$lgx_section_bg_img             = $lgx_shortcodes_meta['lgx_section_bg_img'];
$lgx_section_bg_img_attachment  = $lgx_shortcodes_meta['lgx_section_bg_img_attachment'];
$lgx_section_bg_img_size        = $lgx_shortcodes_meta['lgx_section_bg_img_size'];
$lgx_section_bg_color_en        = $lgx_shortcodes_meta['lgx_section_bg_color_en'];
$lgx_section_bg_color           = lgx_ls_sanitize_css_color($lgx_shortcodes_meta['lgx_section_bg_color']);
$lgx_section_top_margin         = $lgx_shortcodes_meta['lgx_section_top_margin'];
$lgx_section_bottom_margin      = $lgx_shortcodes_meta['lgx_section_bottom_margin'];
$lgx_section_top_padding        = $lgx_shortcodes_meta['lgx_section_top_padding'];
$lgx_section_bottom_padding     = $lgx_shortcodes_meta['lgx_section_bottom_padding'];

//Header Settings
$lgx_header_title_font_size         = $lgx_shortcodes_meta['lgx_header_title_font_size'];
$lgx_header_title_color             = lgx_ls_sanitize_css_color($lgx_shortcodes_meta['lgx_header_title_color']);
$lgx_header_title_font_weight       = $lgx_shortcodes_meta['lgx_header_title_font_weight'];
$lgx_header_title_bottom_margin     = $lgx_shortcodes_meta['lgx_header_title_bottom_margin'];
$lgx_header_subtitle_font_size      = $lgx_shortcodes_meta['lgx_header_subtitle_font_size'];
$lgx_header_subtitle_color          = lgx_ls_sanitize_css_color($lgx_shortcodes_meta['lgx_header_subtitle_color']);
$lgx_header_subtitle_font_weight    = $lgx_shortcodes_meta['lgx_header_subtitle_font_weight'];
$lgx_header_subtitle_bottom_margin  = $lgx_shortcodes_meta['lgx_header_subtitle_bottom_margin'];



// Style

$lgx_item_brand_name_color          = lgx_ls_sanitize_css_color($lgx_shortcodes_meta['lgx_item_brand_name_color']);
$lgx_item_brand_name_font_size      = $lgx_shortcodes_meta['lgx_item_brand_name_font_size'];
$lgx_item_brand_name_font_weight    = $lgx_shortcodes_meta['lgx_item_brand_name_font_weight'];

$lgx_item_desc_font_size            = $lgx_shortcodes_meta['lgx_item_desc_font_size'];
$lgx_item_desc_color                = lgx_ls_sanitize_css_color($lgx_shortcodes_meta['lgx_item_desc_color']);
$lgx_item_desc_font_weight          = $lgx_shortcodes_meta['lgx_item_desc_font_weight'];

$lgx_img_border_color_en            = $lgx_shortcodes_meta['lgx_img_border_color_en'];
$lgx_img_border_color               = lgx_ls_sanitize_css_color($lgx_shortcodes_meta['lgx_img_border_color']);
$lgx_img_border_color_hover         = lgx_ls_sanitize_css_color($lgx_shortcodes_meta['lgx_img_border_color_hover']);
$lgx_img_border_width               = $lgx_shortcodes_meta['lgx_img_border_width'];
$lgx_img_border_radius              = $lgx_shortcodes_meta['lgx_img_border_radius'];


$lgx_border_color_en                = $lgx_shortcodes_meta['lgx_border_color_en'];
$lgx_item_border_color              = lgx_ls_sanitize_css_color($lgx_shortcodes_meta['lgx_item_border_color']);
$lgx_item_border_color_hover        = lgx_ls_sanitize_css_color($lgx_shortcodes_meta['lgx_item_border_color_hover']);
$lgx_item_border_width              = $lgx_shortcodes_meta['lgx_item_border_width'];
$lgx_item_border_radius             = $lgx_shortcodes_meta['lgx_item_border_radius'];

$lgx_item_bg_color_en               = $lgx_shortcodes_meta['lgx_item_bg_color_en'];
$lgx_item_bg_color                  = lgx_ls_sanitize_css_color($lgx_shortcodes_meta['lgx_item_bg_color']);
$lgx_item_bg_color_hover            = lgx_ls_sanitize_css_color($lgx_shortcodes_meta['lgx_item_bg_color_hover']);

$lgx_item_padding                   = $lgx_shortcodes_meta['lgx_item_padding'];
$lgx_item_margin                    = $lgx_shortcodes_meta['lgx_item_margin'];


$lgx_item_top_margin_title          = (isset($lgx_shortcodes_meta['lgx_item_top_margin_title']) ? $lgx_shortcodes_meta['lgx_item_top_margin_title'] : '5px');
$lgx_item_bottom_margin_title       = $lgx_shortcodes_meta['lgx_item_bottom_margin_title'];


$lgx_item_top_margin_desc           = (isset($lgx_shortcodes_meta['lgx_item_top_margin_desc']) ? $lgx_shortcodes_meta['lgx_item_top_margin_desc'] : '0px');
$lgx_item_bottom_margin_desc        = $lgx_shortcodes_meta['lgx_item_bottom_margin_desc'];


//Carousel

$lgx_carousel_pagination_color              = lgx_ls_sanitize_css_color($lgx_shortcodes_meta['lgx_carousel_pagination_color']);
$lgx_carousel_pagination_color_active       = lgx_ls_sanitize_css_color($lgx_shortcodes_meta['lgx_carousel_pagination_color_active']);

$lgx_carousel_nav_position                  = $lgx_shortcodes_meta['lgx_carousel_nav_position'];
$lgx_carousel_nav_color                     = lgx_ls_sanitize_css_color($lgx_shortcodes_meta['lgx_carousel_nav_color']);
$lgx_carousel_nav_color_hover               = lgx_ls_sanitize_css_color($lgx_shortcodes_meta['lgx_carousel_nav_color_hover']);
$lgx_carousel_nav_bg_color                  = lgx_ls_sanitize_css_color($lgx_shortcodes_meta['lgx_carousel_nav_bg_color']);
$lgx_carousel_nav_bg_color_hover            = lgx_ls_sanitize_css_color($lgx_shortcodes_meta['lgx_carousel_nav_bg_color_hover']);
$lgx_carousel_nav_border_en                 = ( isset( $lgx_shortcodes_meta['lgx_carousel_nav_border_en'] ) ? $lgx_shortcodes_meta['lgx_carousel_nav_border_en'] : 'no' );
$lgx_carousel_nav_border_color              = lgx_ls_sanitize_css_color($lgx_shortcodes_meta['lgx_carousel_nav_border_color']);
$lgx_carousel_nav_border_color_hover        = lgx_ls_sanitize_css_color($lgx_shortcodes_meta['lgx_carousel_nav_border_color_hover']);
$lgx_carousel_nav_border_width              = $lgx_shortcodes_meta['lgx_carousel_nav_border_width'];
$lgx_carousel_nav_border_radius             = $lgx_shortcodes_meta['lgx_carousel_nav_border_radius'];

$lgx_carousel_nav_btn_margin         = (isset($lgx_shortcodes_meta['lgx_carousel_nav_btn_margin']) ? $lgx_shortcodes_meta['lgx_carousel_nav_btn_margin'] : '5px 0px');
$lgx_carousel_nav_ver_pos_bottom     = (isset($lgx_shortcodes_meta['lgx_carousel_nav_ver_pos_bottom']) ? $lgx_shortcodes_meta['lgx_carousel_nav_ver_pos_bottom'] : '50%');
$lgx_carousel_nav_ver_pos_left_right = (isset($lgx_shortcodes_meta['lgx_carousel_nav_ver_pos_left_right']) ? $lgx_shortcodes_meta['lgx_carousel_nav_ver_pos_left_right'] : '-55px');

// Logo Height/Width: moved here from pro-style.php so these 4 real fields'
// CSS renders in both editions (previously Pro-only via pro-style.php's
// file-level `if ( lgx_ls_is_pro() )` gate in view-controller.php -- that
// gate predates all of this session's Height/Width work and was never a
// deliberate decision specific to these 4 fields; see docs/architecture/
// invariants.md #6 and the Height/Width task's ADR entry). isset()-guarded
// fallbacks unchanged from pro-style.php's own (see BACKLOG.md B105 for the
// pre-existing 'auto' vs corrected 'none' staleness, out of scope here).
$lgx_logo_height = isset( $lgx_shortcodes_meta['lgx_logo_height'] ) ? $lgx_shortcodes_meta['lgx_logo_height'] : 'auto';
$lgx_logo_width  = isset( $lgx_shortcodes_meta['lgx_logo_width'] ) ? $lgx_shortcodes_meta['lgx_logo_width'] : '100%';
$lgx_logo_height_property = (isset($lgx_shortcodes_meta['lgx_logo_height_property']) ? $lgx_shortcodes_meta['lgx_logo_height_property'] : 'max-height');
$lgx_logo_width_property = (isset($lgx_shortcodes_meta['lgx_logo_width_property']) ? $lgx_shortcodes_meta['lgx_logo_width_property'] : 'max-width');

// Downgrade-safe: 'width'/'min-width' are Pro-only options (Model A -- Max
// Width stays Free; see config.php's free_options on lgx_logo_width_property
// and Lgx_Pro_Locking::is_option_locked()). A showcase saved while Pro, then
// downgraded, keeps its stored value forever (Invariant 9 -- "a rejected
// value retains the previously stored value"). Now that this block always
// renders regardless of edition, a render-time fallback to the Free default
// is required to stop that stored Pro-only choice from taking visible effect
// once the site is no longer entitled to it. Height has no equivalent gating
// (fully Free, all 3 options), so no analogous check applies there.
if ( ! lgx_ls_is_pro() && in_array( $lgx_logo_width_property, array( 'width', 'min-width' ), true ) ) {
    $lgx_logo_width_property = 'max-width';
}





//Style Settings

$lgx_lsw_dynamic_style_general = '';
$lgx_lsw_dynamic_style_general .= '#lgx_logo_slider_app_'. $lgx_app_id.' .lgx_logo_slider{
    '. (('yes' == $lgx_section_bg_img_en) ? 'background: url('.$lgx_section_bg_img.') no-repeat center top;' : '').'
        background-attachment: '. $lgx_section_bg_img_attachment.';
        background-size: '. $lgx_section_bg_img_size.';
        width:'.$lgx_section_width.';
    }';

$lgx_lsw_dynamic_style_general .= ' #lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_inner {
    '. (('yes' == $lgx_section_bg_color_en) ? lgx_ls_css_decl('background-color', $lgx_section_bg_color) : '').'
        padding: '. $lgx_section_top_padding.' 0 '. $lgx_section_bottom_padding.';
        margin: '. $lgx_section_top_margin.' 0 '. $lgx_section_bottom_margin.';
    }';

$lgx_lsw_dynamic_style_general .= '#lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_header_title{
        font-size: '. $lgx_header_title_font_size.';
        '. lgx_ls_css_decl('color', $lgx_header_title_color) .'
        font-weight: '. $lgx_header_title_font_weight.';
        margin:0px;
        margin-bottom: '. $lgx_header_title_bottom_margin.';
    }';

$lgx_lsw_dynamic_style_general .= ' #lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_header_subtitle{
        font-size: '. $lgx_header_subtitle_font_size.';
        '. lgx_ls_css_decl('color', $lgx_header_subtitle_color) .'
        font-weight: '. $lgx_header_subtitle_font_weight.';
        margin-bottom: '. $lgx_header_subtitle_bottom_margin.';
    }';

$lgx_lsw_dynamic_style_general .= '#lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_item .lgx_app_item_title  {
        '. lgx_ls_css_decl('color', $lgx_item_brand_name_color) .'
        font-size: '. $lgx_item_brand_name_font_size.';
        font-weight: '. $lgx_item_brand_name_font_weight.';
        margin-top:'. $lgx_item_top_margin_title .';
        margin-bottom: '. $lgx_item_bottom_margin_title .';
    }';
$lgx_lsw_dynamic_style_general .= '#lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_item .lgx_app_item_desc  {
        font-size: '. $lgx_item_desc_font_size.';
        '. lgx_ls_css_decl('color', $lgx_item_desc_color) .'
        font-weight: '. $lgx_item_desc_font_weight.';
        margin-top:'. $lgx_item_top_margin_desc .';
         margin-bottom: '. $lgx_item_bottom_margin_desc .';
    }';
$lgx_lsw_dynamic_style_general .= '#lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_item .lgx_app_item_figure .lgx_app_item_img {
    '. (('yes' == $lgx_img_border_color_en) ? lgx_ls_css_decl('border', $lgx_img_border_width.' solid '.$lgx_img_border_color, $lgx_img_border_color) : '').'
    '. (('yes' == $lgx_img_border_color_en) ? 'border-radius:'.$lgx_img_border_radius.';' : '').'
    }';
$lgx_lsw_dynamic_style_general .= '#lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_item .lgx_app_item_inner:hover .lgx_app_item_figure .lgx_app_item_img {
        transition: background-color 0.5s ease;
    '. (('yes' == $lgx_img_border_color_en) ? lgx_ls_css_decl('border', $lgx_img_border_width.' solid '.$lgx_img_border_color_hover, $lgx_img_border_color_hover) : '').'
    }';
// lgx_logo_max_height / lgx_logo_max_width removed entirely before 6.0.0
// shipped -- introduced mid-development (5427caf), never present in any real
// shipped release, so no output block or backward-compat guard needed here.
// See docs/architecture/invariants.md #6.
//
// The 4 real fields' CSS (moved here from pro-style.php so it renders in
// both editions -- see the variable computation and downgrade-safe guard
// above): identical rule to what pro-style.php always emitted, unconditional
// on edition. $lgx_logo_width_property is already edition-corrected above,
// so this needs no lgx_ls_is_pro() check of its own.
$lgx_lsw_dynamic_style_general .= '#lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_item .lgx_app_item_img  {
        '.(($lgx_logo_width != 0) ? trim($lgx_logo_width_property ).':'. $lgx_logo_width.';' : '' ).'
        '.(($lgx_logo_height != 0) ? trim($lgx_logo_height_property ).':'. $lgx_logo_height.';' : '' ).'
        object-fit: scale-down;
    }';

 /*  $lgx_lsw_dynamic_style_general .= '#lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_item {
            padding: '. $lgx_item_padding.';
        }';*/
$lgx_lsw_dynamic_style_general .= '#lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_item .lgx_app_item_inner  {
    '. (('yes' == $lgx_border_color_en) ? lgx_ls_css_decl('border', $lgx_item_border_width.' solid '.$lgx_item_border_color, $lgx_item_border_color) : '').'
    '. (('yes' == $lgx_border_color_en) ? 'border-radius:'.$lgx_item_border_radius.';' : '').'
        margin: '. $lgx_item_margin.';
        padding: '. $lgx_item_padding.';
       '.(('yes' == $lgx_item_bg_color_en) ? lgx_ls_css_decl('background-color', $lgx_item_bg_color) : '').'
    }';
$lgx_lsw_dynamic_style_general .= ' #lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_item .lgx_app_item_inner:hover  {
    '. (('yes' == $lgx_border_color_en) ? lgx_ls_css_decl('border-color', $lgx_item_border_color_hover) : '').'
    '. (('yes' == $lgx_item_bg_color_en) ? lgx_ls_css_decl('background-color', $lgx_item_bg_color_hover) : '').'

    }';
$lgx_lsw_dynamic_style_general .= ' '.(($lgx_shortcodes_meta['lgx_carousel_pagination_en'] == 'yes') ? '#lgx_logo_slider_app_'.$lgx_app_id.' .lgx_logo_carousel  {padding-bottom: 45px;}' : '').'
';
$lgx_lsw_dynamic_style_general .= '#lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_layout_carousel .lgx_lsw_pagination .swiper-pagination-bullet{
        '. lgx_ls_css_decl('background', $lgx_carousel_pagination_color) .'
        opacity: 1;
    }';
$lgx_lsw_dynamic_style_general .= ' #lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_layout_carousel .lgx_lsw_pagination .swiper-pagination-bullet-active{
        '. lgx_ls_css_decl('background', $lgx_carousel_pagination_color_active) .'
    }';
$lgx_lsw_dynamic_style_general .= '#lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_layout_carousel .lgx_lsw_pagination .swiper-pagination-bullet-active-main{
        '. lgx_ls_css_decl('background', $lgx_carousel_pagination_color_active) .'
    }';
$lgx_lsw_dynamic_style_general .= '#lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_layout_carousel  .lgx_lsw_nav_button{
        '. lgx_ls_css_decl('color', $lgx_carousel_nav_color) .'
        '. lgx_ls_css_decl('background-color', $lgx_carousel_nav_bg_color) .'
        font-size: '. $lgx_shortcodes_meta['lgx_carousel_nav_btn_font_size'].';
        width: '. $lgx_shortcodes_meta['lgx_carousel_nav_btn_width'].';
        height: '. $lgx_shortcodes_meta['lgx_carousel_nav_btn_height'].';
        padding: '. $lgx_shortcodes_meta['lgx_carousel_nav_btn_padding'].';
    '.(('yes' === $lgx_carousel_nav_border_en) ? lgx_ls_css_decl('border', $lgx_shortcodes_meta['lgx_carousel_nav_border_width'].' '.$lgx_shortcodes_meta['lgx_carousel_nav_border_style'].' '.$lgx_carousel_nav_border_color, $lgx_carousel_nav_border_color) : '').'
        border-radius: '. $lgx_shortcodes_meta['lgx_carousel_nav_border_radius'].';
    }
    ';
    // Nav hover-color (B104): moved here from pro-style.php so these 3 real
    // fields' CSS is genuinely edition-agnostic -- same pattern as the Logo
    // Height/Width move above. The 3 fields carry no 'status' key in
    // config.php (fully Free), but their only CSS emission previously lived
    // in the Pro-gated pro-style.php, so a Free user could save them with no
    // visible effect. See BACKLOG.md B104.
    $lgx_lsw_dynamic_style_general .= '#lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_layout_carousel .lgx_lsw_nav_button:hover{
        '. lgx_ls_css_decl('color', $lgx_carousel_nav_color_hover) .'
        '. lgx_ls_css_decl('background-color', $lgx_carousel_nav_bg_color_hover) .'
        '.(('yes' === $lgx_carousel_nav_border_en) ? lgx_ls_css_decl('border-color', $lgx_carousel_nav_border_color_hover) : '').'
    }';
    $lgx_lsw_dynamic_style_general .= '#lgx_logo_slider_app_'. $lgx_app_id.' .lgx_app_layout_carousel  .lgx_lsw_nav_wrap{
        margin:'. $lgx_carousel_nav_btn_margin.';
    }
    ';

    $lgx_lsw_dynamic_style_general .= '#lgx_logo_slider_app_'. $lgx_app_id.' .lgx_lsw_nav_vertical_center .lgx_lsw_nav_button{
        bottom:'. $lgx_carousel_nav_ver_pos_bottom.';
    }
    ';

    $lgx_lsw_dynamic_style_general .= '#lgx_logo_slider_app_'. $lgx_app_id.' .lgx_lsw_nav_vertical_center .lgx_lsw_nav_button_prev{
        left:'.$lgx_carousel_nav_ver_pos_left_right.';
    }';


    $lgx_lsw_dynamic_style_general .= '#lgx_logo_slider_app_'. $lgx_app_id.' .lgx_lsw_nav_vertical_center .lgx_lsw_nav_button_next{
        right:'.$lgx_carousel_nav_ver_pos_left_right.';
    }';

    $lgx_lsw_dynamic_style_general .= '#lgx_logo_slider_app_'. $lgx_app_id.' .lgx_lsw_nav_vertical_center .lgx_lsw_nav_button_next_rtl {
        left:'.$lgx_carousel_nav_ver_pos_left_right.';
        right: auto;
    }';

    $lgx_lsw_dynamic_style_general .= '#lgx_logo_slider_app_'. $lgx_app_id.' .lgx_lsw_nav_vertical_center .lgx_lsw_nav_button_prev_rtl {
        right:'.$lgx_carousel_nav_ver_pos_left_right.';
        left: auto;
    }';


/**
 *  Inline Style
 */

echo '<style>' . wp_strip_all_tags( $lgx_lsw_dynamic_style_general ) . '</style>';