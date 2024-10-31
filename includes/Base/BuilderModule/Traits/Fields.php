<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * Builder Module Helper Class which help to the all module class
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
 * Fields class.
 *
 * @since       1.0.0
 * @package     post-grid-module-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
trait Fields {

	/**
	 * Get HTML tag elements for text item.
	 *
	 * @return string[][]
	 */
	public static function squad_get_html_tag_elements() {
		return array(
			'h1'   => esc_html__( 'H1 tag', 'post-grid-module-for-divi' ),
			'h2'   => esc_html__( 'H2 tag', 'post-grid-module-for-divi' ),
			'h3'   => esc_html__( 'H3 tag', 'post-grid-module-for-divi' ),
			'h4'   => esc_html__( 'H4 tag', 'post-grid-module-for-divi' ),
			'h5'   => esc_html__( 'H5 tag', 'post-grid-module-for-divi' ),
			'h6'   => esc_html__( 'H6 tag', 'post-grid-module-for-divi' ),
			'p'    => esc_html__( 'P tag', 'post-grid-module-for-divi' ),
			'span' => esc_html__( 'SPAN tag', 'post-grid-module-for-divi' ),
			'div'  => esc_html__( 'DIV tag', 'post-grid-module-for-divi' ),
		);
	}

	/**
	 *  Add button fields.
	 *
	 * @param array $options The options for button fields.
	 *
	 * @return array
	 */
	protected function squad_get_button_fields( $options = array() ) {
		$defaults = array(
			'title_prefix'                 => '',
			'base_attr_name'               => 'button',
			'button_icon'                  => '&#x4e;||divi||400',
			'button_image'                 => '',
			'fields_after_text'            => array(),
			'fields_after_image'           => array(),
			'fields_after_background'      => array(),
			'fields_after_colors'          => array(),
			'fields_before_margin'         => array(),
			'fields_before_icon_placement' => array(),
			'tab_slug'                     => 'general',
			'toggle_slug'                  => 'button_element',
			'sub_toggle'                   => null,
			'priority'                     => 30,
		);

		$config             = wp_parse_args( $options, $defaults );
		$base_name          = $config['base_attr_name'];
		$fields_after_text  = $config['fields_after_text'];
		$fields_after_image = $config['fields_after_image'];

		// Conditions.
		$conditions = wp_array_slice_assoc(
			$options,
			array(
				'depends_show_if',
				'depends_show_if_not',
				'show_if',
				'show_if_not',
			)
		);

		// Button fields definitions.
		$button_text_field  = array_merge_recursive(
			$conditions,
			array(
				"{$base_name}_text" => array(
					'label'           => esc_html__( 'Button', 'post-grid-module-for-divi' ),
					'description'     => esc_html__( 'The text of your button will appear in with the module.', 'post-grid-module-for-divi' ),
					'type'            => 'text',
					'option_category' => 'basic_option',
					'tab_slug'        => 'general',
					'toggle_slug'     => $config['toggle_slug'],
					'dynamic_content' => 'text',
					'hover'           => 'tabs',
					'mobile_options'  => true,
				),
			)
		);
		$button_icon_fields = array(
			"{$base_name}_icon_type" => array_merge_recursive(
				$conditions,
				$this->squad_add_select_box_field(
					esc_html__( 'Button Icon Type', 'post-grid-module-for-divi' ),
					array(
						'description'      => esc_html__( 'Choose an icon type to display with your button.', 'post-grid-module-for-divi' ),
						'options'          => array(
							'icon'  => esc_html__( 'Icon', 'post-grid-module-for-divi' ),
							'image' => et_builder_i18n( 'Image' ),
							'none'  => esc_html__( 'None', 'post-grid-module-for-divi' ),
						),
						'default_on_front' => 'icon',
						'affects'          => array(
							"{$base_name}_icon",
							"{$base_name}_image",
							"{$base_name}_icon_color",
							"{$base_name}_icon_size",
							"{$base_name}_image_width",
							"{$base_name}_image_height",
							"{$base_name}_icon_gap",
							"{$base_name}_icon_on_hover",
							"{$base_name}_icon_placement",
							"{$base_name}_icon_margin",
						),
						'tab_slug'         => 'general',
						'toggle_slug'      => $config['toggle_slug'],
					)
				)
			),
			"{$base_name}_icon"      => array(
				'label'            => esc_html__( 'Choose an icon', 'post-grid-module-for-divi' ),
				'description'      => esc_html__( 'Choose an icon to display with your button.', 'post-grid-module-for-divi' ),
				'type'             => 'select_icon',
				'option_category'  => 'basic_option',
				'class'            => array( 'et-pb-font-icon' ),
				'default_on_front' => ! empty( $config['button_icon'] ) ? '&#x4e;||divi||400' : '',
				'depends_show_if'  => 'icon',
				'tab_slug'         => 'general',
				'toggle_slug'      => $config['toggle_slug'],
				'hover'            => 'tabs',
				'mobile_options'   => true,
			),
			"{$base_name}_image"     => array(
				'label'              => et_builder_i18n( 'Image' ),
				'description'        => esc_html__( 'Upload an image to display at the top of your button.', 'post-grid-module-for-divi' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => et_builder_i18n( 'Upload an image' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'post-grid-module-for-divi' ),
				'update_text'        => esc_attr__( 'Set As Image', 'post-grid-module-for-divi' ),
				'depends_show_if'    => 'image',
				'tab_slug'           => 'general',
				'toggle_slug'        => $config['toggle_slug'],
				'hover'              => 'tabs',
				'dynamic_content'    => 'image',
				'mobile_options'     => true,
			),
		);
		$button_fields      = array_merge(
			$button_text_field,
			$fields_after_text,
			$button_icon_fields,
			$fields_after_image
		);

		return array_merge(
			$button_fields,
			$this->squad_get_button_associated_fields( $config )
		);
	}

	/**
	 *  Add button associated fields.
	 *
	 * @param array $options The options for button fields.
	 *
	 * @return array
	 */
	protected function squad_get_button_associated_fields( $options = array() ) {
		$defaults = array(
			'title_prefix'                 => '',
			'base_attr_name'               => 'button',
			'button_icon'                  => '&#x4e;||divi||400',
			'button_image'                 => '',
			'fields_after_text'            => array(),
			'fields_after_image'           => array(),
			'fields_after_background'      => array(),
			'fields_after_colors'          => array(),
			'fields_before_icon_placement' => array(),
			'fields_before_margin'         => array(),
			'tab_slug'                     => 'general',
			'toggle_slug'                  => 'button_element',
			'sub_toggle'                   => null,
			'priority'                     => 30,
		);

		$config    = wp_parse_args( $options, $defaults );
		$base_name = $config['base_attr_name'];

		// Conditions.
		$conditions = wp_array_slice_assoc(
			$options,
			array(
				'depends_show_if',
				'depends_show_if_not',
				'show_if',
				'show_if_not',
			)
		);

		$button_hover_effects = array(
			"{$base_name}_hover_animation__enable" => array_merge_recursive(
				$conditions,
				$this->squad_add_yes_no_field(
					esc_html__( 'Enable Hover Animation', 'post-grid-module-for-divi' ),
					array(
						'description'      => esc_html__(
							'By default, the button element will be not get any hover animation. If you would like get hover animation for the button, then you can enable this option.',
							'post-grid-module-for-divi'
						),
						'default_on_front' => 'off',
						'affects'          => array(
							"{$base_name}_hover_animation_type",
						),
						'tab_slug'         => 'advanced',
						'toggle_slug'      => $config['toggle_slug'],
					)
				)
			),
			"{$base_name}_hover_animation_type"    => $this->squad_add_select_box_field(
				esc_html__( 'Animation Type', 'post-grid-module-for-divi' ),
				array(
					'description'      => esc_html__( 'Choose an animation type to display with your button.', 'post-grid-module-for-divi' ),
					'options'          => array(
						'fill'   => esc_html__( 'fill', 'post-grid-module-for-divi' ),
						'pulse'  => esc_html__( 'pulse', 'post-grid-module-for-divi' ),
						'close'  => esc_html__( 'close', 'post-grid-module-for-divi' ),
						'raise'  => esc_html__( 'raise', 'post-grid-module-for-divi' ),
						'up'     => esc_html__( 'up', 'post-grid-module-for-divi' ),
						'slide'  => esc_html__( 'slide', 'post-grid-module-for-divi' ),
						'offset' => esc_html__( 'offset', 'post-grid-module-for-divi' ),
					),
					'default_on_front' => 'icon',
					'depends_show_if'  => 'on',
					'tab_slug'         => 'advanced',
					'toggle_slug'      => $config['toggle_slug'],
				)
			),
		);

		$background          = array();
		$default_colors      = ET_Global_Settings::get_value( 'all_buttons_bg_color' );
		$background_defaults = array(
			/* translators: 1. Field Name. */
			'label'             => sprintf( esc_html__( '%1$s Background', 'post-grid-module-for-divi' ), $config['title_prefix'] ),
			'description'       => esc_html__( 'Adjust the background style of the button by customizing the background color, gradient, and image.', 'post-grid-module-for-divi' ),
			'type'              => 'background-field',
			'base_name'         => "{$base_name}_background",
			'context'           => "{$base_name}_background_color",
			'option_category'   => 'button',
			'custom_color'      => true,
			'default'           => $default_colors,
			'default_on_front'  => '',
			'tab_slug'          => 'advanced',
			'toggle_slug'       => $config['toggle_slug'],
			'background_fields' => array_merge_recursive(
				$this->generate_background_options(
					"{$base_name}_background",
					'color',
					'advanced',
					$config['toggle_slug'],
					"{$base_name}_background_color"
				),
				$this->generate_background_options(
					"{$base_name}_background",
					'gradient',
					'advanced',
					$config['toggle_slug'],
					"{$base_name}_background_color"
				),
				$this->generate_background_options(
					"{$base_name}_background",
					'image',
					'advanced',
					$config['toggle_slug'],
					"{$base_name}_background_color"
				)
			),
			'hover'             => 'tabs',
			'mobile_options'    => true,
			'sticky'            => true,
		);

		$background[ "{$base_name}_background_color" ] = array_merge_recursive(
			$conditions,
			$background_defaults
		);

		$background[ "{$base_name}_background_color" ]['background_fields'][ "{$base_name}_background_color" ]['default'] = $default_colors;

		$background = array_merge(
			$background,
			$this->generate_background_options(
				"{$base_name}_background",
				'skip',
				'advanced',
				$config['toggle_slug'],
				"{$base_name}_background_color"
			)
		);

		// Button fields definitions.
		return array_merge(
			$background,
			$config['fields_after_background'],
			array(
				"{$base_name}_icon_color" => $this->squad_add_color_field(
					esc_html__( 'Icon Color', 'post-grid-module-for-divi' ),
					array(
						'description'     => esc_html__( 'Here you can define a custom color for your button icon.', 'post-grid-module-for-divi' ),
						'depends_show_if' => 'icon',
						'tab_slug'        => 'advanced',
						'toggle_slug'     => $config['toggle_slug'],
					)
				),
			),
			$config['fields_after_colors'],
			array(
				"{$base_name}_icon_size"    => $this->squad_add_range_field(
					esc_html__( 'Icon Size', 'post-grid-module-for-divi' ),
					array(
						'description'     => esc_html__( 'Here you can choose icon size.', 'post-grid-module-for-divi' ),
						'range_settings'  => array(
							'min'  => '1',
							'max'  => '200',
							'step' => '1',
						),
						'default'         => '16px',
						'default_unit'    => 'px',
						'tab_slug'        => 'advanced',
						'toggle_slug'     => $config['toggle_slug'],
						'depends_show_if' => 'icon',
					)
				),
				"{$base_name}_image_width"  => $this->squad_add_range_field(
					esc_html__( 'Image Width', 'post-grid-module-for-divi' ),
					array(
						'description'     => esc_html__( 'Here you can choose image width.', 'post-grid-module-for-divi' ),
						'range_settings'  => array(
							'min'  => '1',
							'max'  => '200',
							'step' => '1',
						),
						'default'         => '16px',
						'tab_slug'        => 'advanced',
						'toggle_slug'     => $config['toggle_slug'],
						'depends_show_if' => 'image',
					)
				),
				"{$base_name}_image_height" => $this->squad_add_range_field(
					esc_html__( 'Image Height', 'post-grid-module-for-divi' ),
					array(
						'description'     => esc_html__( 'Here you can choose image height.', 'post-grid-module-for-divi' ),
						'range_settings'  => array(
							'min'  => '1',
							'max'  => '200',
							'step' => '1',
						),
						'default'         => '16px',
						'depends_show_if' => 'image',
						'tab_slug'        => 'advanced',
						'toggle_slug'     => $config['toggle_slug'],
					)
				),
				"{$base_name}_icon_gap"     => $this->squad_add_range_field(
					esc_html__( 'Gap Between Icon/Image and Text', 'post-grid-module-for-divi' ),
					array(
						'description'         => esc_html__( 'Here you can choose gap between icon and text.', 'post-grid-module-for-divi' ),
						'range_settings'      => array(
							'min'  => '1',
							'max'  => '200',
							'step' => '1',
						),
						'default'             => '10px',
						'default_unit'        => 'px',
						'depends_show_if_not' => array( 'none' ),
						'tab_slug'            => 'advanced',
						'toggle_slug'         => $config['toggle_slug'],
						'mobile_options'      => true,
					),
					array( 'use_hover' => false )
				),
			),
			$config['fields_before_icon_placement'],
			array(
				"{$base_name}_icon_placement"       => $this->squad_add_placement_field(
					esc_html__( 'Icon Placement', 'post-grid-module-for-divi' ),
					array(
						'description'         => esc_html__( 'Here you can choose where to place the icon.', 'post-grid-module-for-divi' ),
						'options'             => array(
							'row-reverse' => et_builder_i18n( 'Left' ),
							'row'         => et_builder_i18n( 'Right' ),
						),
						'default_on_front'    => 'row',
						'depends_show_if_not' => array( 'none' ),
						'tab_slug'            => 'advanced',
						'toggle_slug'         => $config['toggle_slug'],
					)
				),
				"{$base_name}_icon_on_hover"        => $this->squad_add_yes_no_field(
					esc_html__( 'Show Icon On Hover', 'post-grid-module-for-divi' ),
					array(
						'description'         => esc_html__( 'By default, button icon to always be displayed. If you would like button icon are displayed on hover, then you can enable this option.', 'post-grid-module-for-divi' ),
						'default_on_front'    => 'off',
						'depends_show_if_not' => array( 'none' ),
						'affects'             => array(
							"{$base_name}_icon_hover_move_icon",
						),
						'tab_slug'            => 'advanced',
						'toggle_slug'         => $config['toggle_slug'],
					)
				),
				"{$base_name}_icon_hover_move_icon" => $this->squad_add_yes_no_field(
					esc_html__( 'Move Icon On Hover Only', 'post-grid-module-for-divi' ),
					array(
						'description'      => esc_html__( 'By default, icon and text are both move on hover. If you would like button icon move on hover, then you can enable this option.', 'post-grid-module-for-divi' ),
						'default_on_front' => 'off',
						'depends_show_if'  => 'on',
						'tab_slug'         => 'advanced',
						'toggle_slug'      => $config['toggle_slug'],
					)
				),
			),
			$button_hover_effects,
			array(
				"{$base_name}_custom_width"       => $this->squad_add_yes_no_field(
					esc_html__( 'Resize Button', 'post-grid-module-for-divi' ),
					array(
						'description'      => esc_html__( 'By default, the button element will be get default width. If you would like resize the button, then you can enable this option.', 'post-grid-module-for-divi' ),
						'default_on_front' => 'off',
						'affects'          => array(
							"{$base_name}_width",
							"{$base_name}_elements_alignment",
						),
						'tab_slug'         => 'advanced',
						'toggle_slug'      => $config['toggle_slug'],
					)
				),
				"{$base_name}_width"              => $this->squad_add_range_field(
					esc_html__( 'Button Width', 'post-grid-module-for-divi' ),
					array(
						'description'     => esc_html__( 'Adjust the width of the content within the button.', 'post-grid-module-for-divi' ),
						'range_settings'  => array(
							'min'  => '0',
							'max'  => '1100',
							'step' => '1',
						),
						'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
						'allow_empty'     => true,
						'default_unit'    => 'px',
						'depends_show_if' => 'on',
						'tab_slug'        => 'advanced',
						'toggle_slug'     => $config['toggle_slug'],
					)
				),
				"{$base_name}_elements_alignment" => $this->squad_add_alignment_field(
					esc_html__( 'Button Elements Alignment', 'post-grid-module-for-divi' ),
					array(
						'description'      => esc_html__( 'Align icon to the left, right or center.', 'post-grid-module-for-divi' ),
						'type'             => 'text_align',
						'default_on_front' => 'left',
						'depends_show_if'  => 'on',
						'tab_slug'         => 'advanced',
						'toggle_slug'      => $config['toggle_slug'],
					)
				),
			),
			$config['fields_before_margin'],
			array(
				"{$base_name}_icon_margin" => $this->squad_add_margin_padding_field(
					esc_html__( 'Icon/Image Margin', 'post-grid-module-for-divi' ),
					array(
						'description'         => esc_html__(
							'Here you can define a custom padding size for the icon.',
							'post-grid-module-for-divi'
						),
						'type'                => 'custom_margin',
						'depends_show_if_not' => array( 'none' ),
						'tab_slug'            => 'advanced',
						'toggle_slug'         => $config['toggle_slug'],
					)
				),
				"{$base_name}_margin"      => $this->squad_add_margin_padding_field(
					esc_html__( 'Button Margin', 'post-grid-module-for-divi' ),
					array(
						'description' => esc_html__(
							'Here you can define a custom margin size for the button.',
							'post-grid-module-for-divi'
						),
						'type'        => 'custom_margin',
						'tab_slug'    => 'advanced',
						'toggle_slug' => $config['toggle_slug'],
					)
				),
				"{$base_name}_padding"     => $this->squad_add_margin_padding_field(
					esc_html__( 'Button Padding', 'post-grid-module-for-divi' ),
					array(
						'description' => esc_html__(
							'Here you can define a custom padding size for the button.',
							'post-grid-module-for-divi'
						),
						'type'        => 'custom_padding',
						'tab_slug'    => 'advanced',
						'toggle_slug' => $config['toggle_slug'],
					)
				),
			)
		);
	}

	/**
	 *  Get general fields.
	 *
	 * @return array[]
	 */
	public static function squad_get_general_fields() {
		return array(
			'admin_label'  => array(
				'label'           => et_builder_i18n( 'Admin Label' ),
				'description'     => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'post-grid-module-for-divi' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'toggle_slug'     => 'admin_label',
			),
			'module_id'    => array(
				'label'           => esc_html__( 'CSS ID', 'post-grid-module-for-divi' ),
				'description'     => esc_html__( "Assign a unique CSS ID to the element which can be used to assign custom CSS styles from within your child theme or from within Divi's custom CSS inputs.", 'post-grid-module-for-divi' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'post-grid-module-for-divi' ),
				'description'     => esc_html__( "Assign any number of CSS Classes to the element, separated by spaces, which can be used to assign custom CSS styles from within your child theme or from within Divi's custom CSS inputs.", 'post-grid-module-for-divi' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
		);
	}
}
