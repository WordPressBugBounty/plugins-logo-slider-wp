<?php
/**
 * Showcase Builder Generic Renderer
 *
 * This class translates the declarative config array into semantic HTML
 * by routing field definitions to the Lgx_Meta_Form class.
 *
 * @package    Logo_Slider_WP
 * @subpackage Logo_Slider_WP/admin/modules/showcase-builder
 * @since      6.0.0
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

class Lgx_Showcase_Renderer {

    /**
     * Iterate over the groups of a tab and render each one.
     *
     * @param array $tab_data The tab configuration array from the registry.
     */
    public static function render_tab_groups( $tab_data, $tab_key = '' ) {
        if ( empty( $tab_data['groups'] ) ) {
            return;
        }

        $tab_id = ! empty( $tab_key ) ? $tab_key : ( isset( $tab_data['id'] ) ? $tab_data['id'] : '' );

        foreach ( $tab_data['groups'] as $group_id => $group ) {
            self::render_group( $group_id, $group, $tab_id );
        }
    }

    /**
     * Render a single accordion group and its fields.
     *
     * @param string $group_id The group ID.
     * @param array  $group    The group configuration array.
     * @param string $tab_id   Optional tab identifier for unique anchor IDs.
     */
    public static function render_group( $group_id, $group, $tab_id = '' ) {
        if ( empty( $group['fields'] ) ) {
            return;
        }

        // Option 2: All settings groups are permanently open and visible
        $is_expanded = 'true';
        $open_class  = ' is-open';
        $dom_id      = ! empty( $tab_id ) ? "lgx-group-{$tab_id}-{$group_id}" : "lgx-group-{$group_id}";
        ?>
        <div id="<?php echo esc_attr( $dom_id ); ?>" class="lgx-ui-panelbody<?php echo esc_attr( $open_class ); ?>">
            <div class="lgx-ui-panelbody-header" aria-expanded="true">
                <span class="lgx-ui-panelbody-title"><?php echo esc_html( $group['label'] ); ?></span>
            </div>
            <div class="lgx-ui-panelbody-content">
                <?php if ( ! empty( $group['description'] ) ) : ?>
                    <p class="description" style="margin-bottom: 16px;"><?php echo esc_html( $group['description'] ); ?></p>
                <?php endif; ?>
                
                <div class="lgx_form_table">
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <?php
                        foreach ( $group['fields'] as $field ) {
                            self::render_field( $field );
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render a single field by invoking Lgx_Meta_Form dynamically.
     *
     * @param array $field_config The field configuration array.
     */
    public static function render_field( $field_config ) {
        if ( empty( $field_config['type'] ) ) {
            return;
        }

        global $post; // Ensure global $post is available for Lgx_Meta_Form context
        
        static $meta_form = null;
        if ( is_null( $meta_form ) ) {
            if ( ! class_exists( 'Lgx_Meta_Form' ) ) {
                $meta_form_path = plugin_dir_path( dirname( dirname( dirname( __FILE__ ) ) ) ) . 'includes/class-logo-slider-wp-meta-form.php';
                if ( file_exists( $meta_form_path ) ) {
                    require_once $meta_form_path;
                }
            }
            if ( class_exists( 'Lgx_Meta_Form' ) ) {
                $meta_form = new Lgx_Meta_Form();
            }
        }

        // Failsafe if Lgx_Meta_Form cannot be loaded
        if ( ! $meta_form ) {
            echo '<div class="lgx-builder-error"><p>' . esc_html__( 'Error: Lgx_Meta_Form class not found.', 'logo-slider-wp' ) . '</p></div>';
            return;
        }

        $method = $field_config['type'];

        if ( method_exists( $meta_form, $method ) ) {
            // Guarantee name matches ID for backwards compatibility with $_POST handlers
            if ( empty( $field_config['name'] ) && ! empty( $field_config['id'] ) ) {
                $field_config['name'] = 'meta_lgx_lsp_shortcodes[' . $field_config['id'] . ']';
            }
            
            // Enforce modern HTML layout block
            $field_config['layout'] = 'modern';
            
            // Dynamically invoke the method
            $meta_form->$method( $field_config );
        } else {
            // Graceful fallback for unknown field types
            echo '<div class="lgx-builder-error" style="padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 4px;">';
            echo '<p style="margin: 0;">' . sprintf( esc_html__( 'Unsupported field type: %s', 'logo-slider-wp' ), esc_html( $method ) ) . '</p>';
            echo '</div>';
        }
    }
}
