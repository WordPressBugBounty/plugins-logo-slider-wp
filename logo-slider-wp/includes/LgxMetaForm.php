<?php
/**
 * Legacy shim for LgxMetaForm to ensure backward compatibility.
 * This file and class are deprecated. Use Lgx_Meta_Form instead.
 *
 * @package Logo_Slider_WP_Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('LgxMetaForm')) {
    /**
     * @deprecated Use Lgx_Meta_Form instead.
     */
    class LgxMetaForm extends Lgx_Meta_Form
    {
        public function __construct()
        {
            if (function_exists('_deprecated_constructor')) {
                _deprecated_constructor(__CLASS__, 'Logo Slider WP Pro 4.0.0', 'Lgx_Meta_Form');
            }
        }
    }
}
