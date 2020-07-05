<?php
/**
 * Define function.
 *
 * @package PixelPlus
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'pixelplus_mega_menu' ) ) {
	/**
	 * Pixelplus Nav Menu.
	 *
	 * @param  string $location [description].
	 * @return mixed [description]
	 */
	function pixelplus_mega_menu( $location = '' ) {
		if ( empty( $location ) ) {
			$location = 'primary';
		}

		wp_nav_menu(
			apply_filters(
				'primary_mega_menu_args',
				array(
					'theme_location' => $location,
					'menu_id'        => $location . '-menu',
					'container'      => '',
					'walker'         => new \PixelPlus\Mega_Menu_Walker(),
				)
			)
		);
	}
}
