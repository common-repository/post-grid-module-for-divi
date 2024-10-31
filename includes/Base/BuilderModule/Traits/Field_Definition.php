<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Builder Module Helper Class which help to the all module class.
 *
 * @since       1.0.0
 * @package     post-grid-module-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */


namespace SquadPostGrid\Base\BuilderModule\Traits;

use ET_Global_Settings;

/**
 * Field Definition class.
 *
 * @since       1.0.0
 * @package     post-grid-module-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
trait Field_Definition {

	/**
	 * Add range fields for the module.
	 *
	 * @param string $label      The field label.
	 * @param array  $properties The additional properties for the current field.
	 * @param array  $conditions The additional conditions for the current field.
	 *
	 * @return array[]
	 */
	protected function squad_add_range_field( $label, $properties = array(), $conditions = array() ) {
		// Add icon width feature for button, By default is 16px.
		$field_options = array(
			'label'           => $label,
			'type'            => 'range',
			'range_settings'  => array(
				'min'       => '0',
				'min_limit' => '0',
				'max'       => '100',
				'step'      => '1',
			),
			// set slug for tab and toggle with category.
			'option_category' => 'layout',
			// include allowed values.
			'allow_empty'     => true,
			'allowed_units'   => et_builder_get_acceptable_css_string_values(),
			'allowed_values'  => et_builder_get_acceptable_css_string_values(),
			'validate_unit'   => true,

			'hover'           => 'tabs',
			'mobile_options'  => true,
			'responsive'      => true,
			'sticky'          => true,
		);

		// Merge all data with additional data.
		$field_options = wp_parse_args( $properties, $field_options );

		// Unset use_hover for this field.
		if ( isset( $conditions['use_hover'] ) && false === $conditions['use_hover'] ) {
			unset( $field_options['hover'] );
		}

		// Unset mobile_options for this field.
		if ( isset( $conditions['mobile_options'] ) && false === $conditions['mobile_options'] ) {
			unset( $field_options['mobile_options'] );
		}

		return $field_options;
	}

	/**
	 * Default fields for Heading toggles.
	 *
	 * @param string $label      The field label.
	 * @param array  $properties The additional properties for the current field.
	 *
	 * @return array
	 */
	protected function squad_add_font_field( $label, $properties = array() ) {
		// Default properties for Font field.
		$defaults = array(
			'label'       => $label,
			'font_weight' => array(
				'default' => '500',
			),
			'line_height' => array(
				'default' => '1.7',
			),
		);

		// Merge all data with additional data.
		return wp_parse_args( $properties, $defaults );
	}

	/**
	 * Add margin and padding fields for the module.
	 *
	 * @param string $label      The field label.
	 * @param array  $properties The additional properties for the current field.
	 *
	 * @return array[]
	 */
	protected function squad_add_margin_padding_field( $label, $properties = array() ) {
		// Default properties for Background field.
		$defaults = array(
			'label'           => $label,
			'description'     => esc_html__( 'Here you can define a custom padding size for the field.', 'post-grid-module-for-divi' ),
			'type'            => 'custom_margin',
			'option_category' => 'layout',
			'default_unit'    => 'px',
			'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
			'range_settings'  => array(
				'min'  => '1',
				'max'  => '50',
				'step' => '1',
			),
			// Advanced feature.
			'hover'           => 'tabs',
			'mobile_options'  => true,
			'responsive'      => true,
			'sticky'          => true,
		);

		// Merge all data with additional data.
		return wp_parse_args( $properties, $defaults );
	}

	/**
	 * Add background fields for module.
	 *
	 * @param string $label      The field label.
	 * @param array  $properties The additional properties for the current field.
	 *
	 * @return array
	 */
	protected function squad_add_background_field( $label, $properties = array() ) {
		// General variables.
		list( $base_name, $context, $tab_slug, $toggle_slug ) = $this->squad_get_background_field_options( $properties );

		// Definitions.
		$background_fields = array_merge_recursive(
			$this->generate_background_options( $base_name, 'color', $tab_slug, $toggle_slug, $context ),
			$this->generate_background_options( $base_name, 'gradient', $tab_slug, $toggle_slug, $context ),
			$this->generate_background_options( $base_name, 'image', $tab_slug, $toggle_slug, $context )
		);

		return $this->squad_add_background_fields( $label, $properties, $background_fields );
	}

	/**
	 * Add background field options for module.
	 *
	 * @param array $properties The additional properties for the current field.
	 *
	 * @return array
	 */
	protected function squad_get_background_field_options( $properties = array() ) {
		// General variables.
		$base_name   = isset( $properties['base_name'] ) ? $properties['base_name'] : '_background';
		$context     = isset( $properties['context'] ) ? $properties['context'] : '_background_color';
		$tab_slug    = isset( $properties['tab_slug'] ) ? $properties['tab_slug'] : 'advanced';
		$toggle_slug = isset( $properties['toggle_slug'] ) ? $properties['toggle_slug'] : 'wrapper';

		return array( $base_name, $context, $tab_slug, $toggle_slug );
	}

	/**
	 * Add all background fields for module.
	 *
	 * @param string $label      The field label.
	 * @param array  $properties The additional properties for the current field.
	 * @param array  $background_fields The background properties for the current field.
	 *
	 * @return array
	 */
	protected function squad_add_background_fields( $label, $properties = array(), $background_fields = array() ) {
		// General variables.
		list( $base_name, $context, $tab_slug, $toggle_slug ) = $this->squad_get_background_field_options( $properties );

		// Definitions.
		$default_bg_colors = ET_Global_Settings::get_value( 'all_buttons_bg_color' );
		$defaults          = array(
			'label'             => $label,
			'description'       => esc_html__( 'Adjust the background style of the current field by customizing the background color, gradient, and image.', 'post-grid-module-for-divi' ),
			'type'              => 'background-field',
			'base_name'         => $base_name,
			'context'           => $context,
			'option_category'   => 'button',
			'custom_color'      => true,
			'default'           => $default_bg_colors,
			'default_on_front'  => '',
			'tab_slug'          => $tab_slug,
			'toggle_slug'       => $toggle_slug,
			'background_fields' => $background_fields,
			'hover'             => 'tabs',
			'mobile_options'    => true,
			'sticky'            => true,
		);

		// Conditions.
		$conditions = wp_array_slice_assoc(
			$properties,
			array(
				'depends_show_if',
				'depends_show_if_not',
				'show_if',
				'show_if_not',
			)
		);

		// Properties for Background field.
		$background_options             = array();
		$background_options[ $context ] = array_merge_recursive( $conditions, $defaults );

		// Set default colors.
		$background_options[ $context ]['background_fields'][ $context ]['default'] = $default_bg_colors;

		return array_merge(
			$background_options,
			$this->generate_background_options( $base_name, 'skip', $tab_slug, $toggle_slug, $context )
		);
	}

	/**
	 * Add yes no fields for module.
	 *
	 * @param string $label      The field label.
	 * @param array  $properties The additional properties for the current field.
	 *
	 * @return array
	 */
	protected function squad_add_yes_no_field( $label, $properties = array() ) {
		// Default properties for Background field.
		$defaults = array(
			'label'            => $label,
			'type'             => 'yes_no_button',
			'option_category'  => 'configuration',
			'options'          => array(
				'off' => esc_html__( 'No', 'post-grid-module-for-divi' ),
				'on'  => esc_html__( 'Yes', 'post-grid-module-for-divi' ),
			),
			'default_on_front' => 'off',
		);

		// Merge all data with additional data.
		return wp_parse_args( $properties, $defaults );
	}

	/**
	 * Add color fields for module.
	 *
	 * @param string $label      The field label.
	 * @param array  $properties The additional properties for the current field.
	 *
	 * @return array
	 */
	protected function squad_add_color_field( $label, $properties = array() ) {
		// Default properties for Background field.
		$defaults = array(
			'label'           => $label,
			'type'            => 'color-alpha',
			'option_category' => 'configuration',
			'custom_color'    => true,

			'field_template'  => 'color',

			'mobile_options'  => true,
			'sticky'          => true,
			'hover'           => 'tabs',
		);

		// Merge all data with additional data.
		return wp_parse_args( $properties, $defaults );
	}

	/**
	 * Add select box fields for module.
	 *
	 * @param string $label      The field label.
	 * @param array  $properties The additional properties for the current field.
	 *
	 * @return array
	 */
	protected function squad_add_select_box_field( $label, $properties = array() ) {
		// Default properties for select field.
		$defaults = array(
			'label'            => $label,
			'description'      => esc_html__( 'Here you can choose where to place the icon.', 'post-grid-module-for-divi' ),
			'type'             => 'select',
			'option_category'  => 'layout',
			'options'          => array(
				'none' => esc_html__( 'Select one', 'post-grid-module-for-divi' ),
			),
			'default_on_front' => '',
		);

		// Merge all data with additional data.
		return wp_parse_args( $properties, $defaults );
	}

	/**
	 * Add alignment fields for module.
	 *
	 * @param string $label      The field label.
	 * @param array  $properties The additional properties for the current field.
	 *
	 * @return array
	 */
	protected function squad_add_placement_field( $label, $properties = array() ) {
		$child_image_icon_placement = array(
			'column'      => et_builder_i18n( 'Top' ),
			'row'         => et_builder_i18n( 'Left' ),
			'row-reverse' => et_builder_i18n( 'Right' ),
		);

		$child_default_placement = 'row';

		if ( is_rtl() ) {
			$child_default_placement = 'row-reverse';
		}

		// Default properties for alignment field.
		$defaults = array(
			'label'            => $label,
			'description'      => esc_html__( 'Here you can choose where to place the icon.', 'post-grid-module-for-divi' ),
			'type'             => 'select',
			'option_category'  => 'layout',
			'options'          => $child_image_icon_placement,
			'default_on_front' => $child_default_placement,
			'mobile_options'   => true,
			'sticky'           => true,
		);

		// Merge all data with additional data.
		return wp_parse_args( $properties, $defaults );
	}

	/**
	 * Add alignment fields for module.
	 *
	 * @param string $label      The field label.
	 * @param array  $properties The additional properties for the current field.
	 *
	 * @return array
	 */
	protected function squad_add_alignment_field( $label, $properties = array() ) {
		// Default properties for alignment field.
		$defaults = array(
			'label'           => $label,
			'description'     => esc_html__( 'Align icon to the left, right or center.', 'post-grid-module-for-divi' ),
			'type'            => 'align',
			'option_category' => 'layout',
			'options'         => et_builder_get_text_orientation_options( array( 'justified' ) ),
			'default'         => 'left',
			'mobile_options'  => true,
			'sticky'          => true,
		);

		// Merge all data with additional data.
		return wp_parse_args( $properties, $defaults );
	}
}
