<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName, WordPress.Files.FileName.NotHyphenatedLowercase

/**
 * String Helper helper.
 *
 * @since       1.2.3
 * @package     post-grid-module-for-divi
 * @author      WP Squad <wp@thewpsquad.com>
 * @copyright   2023 WP Squad
 * @license     GPL-3.0-only
 */

namespace SquadPostGrid\Utils\Polyfills;

/**
 * String Helper class.
 *
 * @since       1.2.3
 * @package     post-grid-module-for-divi
 */
class Str {

	/**
	 * Polyfill for `str_word_count()` function.
	 *
	 * Performs a case-sensitive check indicating if a needle is contained in a haystack.
	 *
	 * @param string  $string_content The string.
	 * @param int     $format         Specify the return value of this function, options are: 0, 1, 2.
	 * @param ?string $characters     The substring to search for in the `$haystack`.
	 *
	 * @return array|int True if `$needle` is in `$haystack`, otherwise false.
	 */
	public static function word_count( $string_content, $format = 0, $characters = null ) {
		/*
		 * The current supported values are:
		 * <ul>
		 *  <li>0: returns the number of words found</li>
		 *  <li>1: returns an array containing all the words found inside the string</li>
		 *  <li>2: returns an associative array, where the key is the numeric position of the word inside the string and the value is the actual word itself</li>
		 * </ul>
		 */

		if ( function_exists( '\str_word_count' ) ) {
			return \str_word_count( $string_content, $format, $characters );
		}

		// Split string into words.
		$break_words = preg_split( '~[^\p{L}\p{N}\']+~u', $string_content );

		return 0 === $format ? count( $break_words ) : $break_words;
	}
}
