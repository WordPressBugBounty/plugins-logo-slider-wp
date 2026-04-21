<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * weDevs Settings API wrapper class
 *
 * @version 1.2 (18-Oct-2015)
 *
 * @author Tareq Hasan <tareq@weDevs.com>
 * @link http://tareq.weDevs.com Tareq's Planet
 * @example src/settings-api.php How to use the class
 * Update By Vaskar on 10.04.2026
 */


if ( ! class_exists( 'Lgx_Carousel_Settings_API' ) ) :

class Lgx_Carousel_Settings_API {

    protected $settings_sections = array();
    protected $settings_fields = array();

    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
    }

    public function admin_enqueue_scripts() {
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_media();
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script( 'jquery' );
    }

    public function set_sections( $sections ) {
        $this->settings_sections = $sections;
        return $this;
    }

    public function add_section( $section ) {
        $this->settings_sections[] = $section;
        return $this;
    }

    public function set_fields( $fields ) {
        $this->settings_fields = $fields;
        return $this;
    }

    public function add_field( $section, $field ) {

        $defaults = array(
            'name'  => '',
            'label' => '',
            'desc'  => '',
            'type'  => 'text'
        );

        $arg = wp_parse_args( $field, $defaults );
        $this->settings_fields[ $section ][] = $arg;

        return $this;
    }

    public function admin_init() {

        foreach ( $this->settings_sections as $section ) {

            if ( false === get_option( $section['id'] ) ) {
                add_option( $section['id'] );
            }

            $callback = null;

            if ( ! empty( $section['desc'] ) ) {

                $desc = wp_kses_post( $section['desc'] );

                $callback = function() use ( $desc ) {
                    echo wp_kses_post(
                        force_balance_tags(
                            '<div class="inside">' . $desc . '</div>'
                        )
                    );
                };

            } elseif ( isset( $section['callback'] ) ) {
                $callback = $section['callback'];
            }

            add_settings_section(
                $section['id'],
                esc_html( $section['title'] ),
                $callback,
                $section['id']
            );
        }

        foreach ( $this->settings_fields as $section => $field ) {

            foreach ( $field as $option ) {

                $type = isset( $option['type'] ) ? sanitize_key( $option['type'] ) : 'text';

                $args = array(
                    'id'                => sanitize_key( $option['name'] ),
                    'label_for'         => "{$section}[{$option['name']}]",
                    'desc'              => isset( $option['desc'] ) ? wp_kses_post( $option['desc'] ) : '',
                    'name'              => sanitize_text_field( $option['label'] ),
                    'section'           => sanitize_key( $section ),
                    'size'              => isset( $option['size'] ) ? sanitize_text_field( $option['size'] ) : '',
                    'options'           => isset( $option['options'] ) ? $option['options'] : array(),
                    'std'               => isset( $option['default'] ) ? $option['default'] : '',
                    'sanitize_callback' => isset( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : '',
                    'type'              => $type,
                );

                add_settings_field(
                    $section . '[' . $option['name'] . ']',
                    esc_html( $option['label'] ),
                    array( $this, 'callback_' . $type ),
                    $section,
                    $section,
                    $args
                );
            }
        }

        foreach ( $this->settings_sections as $section ) {
            register_setting( $section['id'], $section['id'], array( $this, 'sanitize_options' ) );
        }
    }

    public function get_field_description( $args ) {

        if ( empty( $args['desc'] ) ) {
            return '';
        }

        return wp_kses_post(
            sprintf( '<p class="description">%s</p>', $args['desc'] )
        );
    }

    public function callback_text( $args ) {

        $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );

        $size = ! empty( $args['size'] ) ? esc_attr( $args['size'] ) : 'regular';
        $type = ! empty( $args['type'] ) ? esc_attr( $args['type'] ) : 'text';

        $html = sprintf(
            '<input type="%1$s" class="%2$s-text" id="%3$s[%4$s]" name="%3$s[%4$s]" value="%5$s"/>',
            $type,
            $size,
            esc_attr( $args['section'] ),
            esc_attr( $args['id'] ),
            $value
        );

        echo wp_kses_post( $html . $this->get_field_description( $args ) );
    }

    public function callback_textimg( $args ) {

        $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );

        $size = ! empty( $args['size'] ) ? esc_attr( $args['size'] ) : 'regular';
        $placeholder = ! empty( $args['placeholder'] ) ? esc_attr( $args['placeholder'] ) : '';

        $html = sprintf(
            '<input type="text" class="logosliderwpbg %1$s-text" value="%2$s" placeholder="%3$s" />',
            $size,
            $value,
            $placeholder
        );

        echo wp_kses_post( $html . $this->get_field_description( $args ) );
    }

    public function callback_checkbox( $args ) {

        $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );

        $html  = '<fieldset>';
        $html .= '<input type="hidden" name="' . esc_attr( $args['section'] ) . '[' . esc_attr( $args['id'] ) . ']" value="off" />';

        $html .= sprintf(
            '<input type="checkbox" value="on" %s /> %s',
            checked( $value, 'on', false ),
            wp_kses_post( $args['desc'] )
        );

        $html .= '</fieldset>';

        echo wp_kses_post( $html );
    }

    public function callback_select( $args ) {

        $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );

        $html = '<select name="' . esc_attr( $args['section'] ) . '[' . esc_attr( $args['id'] ) . ']">';

        foreach ( (array) $args['options'] as $key => $label ) {
            $html .= sprintf(
                '<option value="%s"%s>%s</option>',
                esc_attr( $key ),
                selected( $value, $key, false ),
                esc_html( $label )
            );
        }

        $html .= '</select>';

        echo wp_kses_post( $html . $this->get_field_description( $args ) );
    }

    public function callback_textarea( $args ) {

        $value = esc_textarea( $this->get_option( $args['id'], $args['section'], $args['std'] ) );

        $html = sprintf(
            '<textarea rows="5">%s</textarea>',
            $value
        );

        echo wp_kses_post( $html . $this->get_field_description( $args ) );
    }

    public function callback_html( $args ) {
        echo wp_kses_post( $this->get_field_description( $args ) );
    }

    public function callback_wysiwyg( $args ) {

        $value = $this->get_option( $args['id'], $args['section'], $args['std'] );

        $editor_settings = array(
            'teeny'         => true,
            'textarea_name' => $args['section'] . '[' . $args['id'] . ']',
            'textarea_rows' => 10,
        );

        echo '<div style="max-width:500px;">';

        wp_editor(
            $value,
            esc_attr( $args['section'] . '-' . $args['id'] ),
            $editor_settings
        );

        echo '</div>';

        echo wp_kses_post( $this->get_field_description( $args ) );
    }

    public function sanitize_options( $options ) {

        foreach ( (array) $options as $key => $value ) {

            if ( is_callable( $value ) ) {
                continue;
            }

            $options[ $key ] = sanitize_text_field( $value );
        }

        return $options;
    }

    public function get_option( $option, $section, $default = '' ) {

        $options = get_option( $section );

        return isset( $options[ $option ] ) ? $options[ $option ] : $default;
    }

    public function show_navigation() {

        $html = '<h2 class="nav-tab-wrapper">';

        foreach ( $this->settings_sections as $tab ) {
            $html .= sprintf(
                '<a href="#%s">%s</a>',
                esc_attr( $tab['id'] ),
                esc_html( $tab['title'] )
            );
        }

        $html .= '</h2>';

        echo wp_kses_post( $html );
    }

    public function show_forms() {
        ?>
        <div class="metabox-holder">
            <?php foreach ( $this->settings_sections as $form ) : ?>
                <div id="<?php echo esc_attr( $form['id'] ); ?>" class="group">
                    <form method="post" action="options.php">
                        <?php
                        settings_fields( $form['id'] );
                        do_settings_sections( $form['id'] );
                        submit_button();
                        ?>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
}

endif;


