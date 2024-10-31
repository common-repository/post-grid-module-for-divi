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

namespace SquadPostGrid\Base\BuilderModule;

use ET_Builder_Module;

/**
 * Builder Module class
 *
 * @since       1.0.0
 * @package     post-grid-module-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */
#[\AllowDynamicProperties]
abstract class Squad_Builder_Module extends ET_Builder_Module {

	use Traits\Element_Divider;
	use Traits\Field_Compatibility;
	use Traits\Field_Definition;
	use Traits\Field_Processor;
	use Traits\Fields;

	/**
	 * Module credits.
	 *
	 * @var string[]
	 * @since 1.0.0
	 */
	protected $module_credits = array(
		'module_uri' => '',
		'author'     => 'Divi Squad',
		'author_uri' => 'https://squadmodules.com/?utm_campaign=wporg&utm_source=module_modal&utm_medium=module_author_link',
	);

	/**
	 * The icon for module.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $icon = '';

	/**
	 * The icon path for module.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected $icon_path = '';
}
