<?php
/**
 * Live Preview — minimal HTML shell (Task 010 / ADR-009 Decision 5).
 *
 * No theme header, footer, sidebar, or wp-admin bar — shortcode output only.
 * wp_head()/wp_footer() are what actually PRINT the styles/scripts that
 * enqueue_styles()/enqueue_scripts()/display_lgx_logo_slider() registered and
 * enqueued in Logo_Slider_WP_Public::maybe_render_preview() just before this
 * file was included — a bare wp_die() with only the shortcode HTML would omit
 * all carousel CSS/JS and render broken.
 *
 * Expects $preview_html (already-rendered shortcode output) in scope, set by
 * the including method (PHP shares local scope with include()).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php esc_html_e( 'Logo Slider WP — Live Preview', 'logo-slider-wp' ); ?></title>
<?php wp_head(); ?>
<style>
/* Preview-only layout. Vertically centres the showcase when the iframe is
   taller than the content — which happens whenever the rendered showcase is
   shorter than .lgx-live-preview-iframe's min-height floor. Without this the
   whole surplus falls below the showcase (normal document flow), so a short
   carousel/grid/flexbox sat hard against the top of the frame.

   html/body must fill the iframe for justify-content to have any free space to
   distribute; the body is otherwise only as tall as its own content and there
   is nothing to centre within.

   Deliberately inline here rather than in public/assets/css: that stylesheet
   ships to real front-end pages, and `html { height: 100% }` cannot be scoped
   to a body class without :has(). These rules must never leave the preview
   document. This file is already a preview-only shell (see the docblock above),
   so it is the correct home.

   Paired with resizePreviewIframe() in showcase-builder.js, which measures
   .lgx-preview-canvas rather than documentElement — once html fills the iframe,
   documentElement.scrollHeight reports the viewport, not the content, and the
   auto-sizing measurement would become circular. */
html,
body.lgx-preview-body {
	height: 100%;
}

body.lgx-preview-body {
	margin: 0;
	display: flex;
	flex-direction: column;
	justify-content: center;
}

/* Full width so carousel/grid/flexbox each resolve their own widths against
   the frame exactly as before — this wrapper adds no horizontal constraint. */
.lgx-preview-canvas {
	width: 100%;
}
</style>
</head>
<body class="lgx-preview-body">
<?php
/*
 * The showcase output gets its own wrapper so resizePreviewIframe() has a
 * single, stable element to measure. It cannot measure the body's children
 * directly: wp_head()/wp_footer() inject ~19 <style>/<script>/<link> nodes
 * around this markup, so body.firstElementChild is a zero-height <script>,
 * not the showcase. Measuring this wrapper instead of the renderer's own
 * .lgx_logo_slider_app also keeps the JS decoupled from renderer class names.
 */
?>
<div class="lgx-preview-canvas">
<?php echo $preview_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already-rendered, trusted shortcode HTML from display_lgx_logo_slider(), not raw user input. ?>
</div>
<?php wp_footer(); ?>
</body>
</html>
