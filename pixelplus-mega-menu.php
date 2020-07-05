<?php
/**
 * PixelPlus Mega Menu Plugin.
 *
 * @package      PixelPlus
 * @copyright    Copyright (C) 2020, PixelPlus - contact@pixelplus.vn
 * @link         https://pixelplus.vn
 * @since        1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       PixelPlus Mega Menu
 * Version:           1.0.2
 * Plugin URI:        https://pixelplus.vn
 * Description:       Create Mega Menu for WordPress Theme.
 * Author:            Pixel+
 * Author URI:        https://pixelplus.vn
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pixelplus
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) || exit;

if ( defined( 'PMM_VERSION' ) ) {
	return;
}

define( 'PMM_VERSION', '1.0.2' );
define( 'PMM_PATH', plugin_dir_path( __FILE__ ) );
define( 'PMM_URL', plugin_dir_url( __FILE__ ) );

if ( ! class_exists( '\PixelPlus\Mega_Menu_Walker' ) ) {
	require_once PMM_PATH . 'inc/class-mega-menu-walker.php';
}

if ( ! class_exists( '\PixelPlus\Mega_Menu' ) ) {
	require_once PMM_PATH . 'inc/class-mega-menu.php';
}

if ( ! function_exists( 'pixelplus_nav_menu' ) ) {
	/**
	 * Pixelplus Nav Menu.
	 *
	 * @param  string $location [description].
	 * @return mixed [description]
	 */
	function pixelplus_nav_menu( $location ) {
		$location = $location ? $location : 'primary';
		wp_nav_menu(
			apply_filters(
				'primary_menu_args',
				array(
					'theme_location' => $location,
					'menu_id'        => $location . '-menu',
					'container'      => '',
				)
			)
		);
	}
}
