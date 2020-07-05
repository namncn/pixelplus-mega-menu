<?php
/**
 * Loader file for plugins and composer.
 *
 * @package PixelPlus
 */

defined( 'ABSPATH' ) || exit;

if ( defined( 'PMM_VERSION' ) ) {
	return;
}

define( 'PMM_VERSION', '1.0.5' );
define( 'PMM_PATH', get_template_directory() . '/vendor/namncn/pixelplus-mega-menu/' );
define( 'PMM_URL', get_template_directory_uri() . '/vendor/namncn/pixelplus-mega-menu/' );

if ( ! class_exists( '\PixelPlus\Mega_Menu_Walker' ) ) {
	require_once PMM_PATH . 'inc/class-mega-menu-walker.php';
}

if ( ! class_exists( '\PixelPlus\Mega_Menu' ) ) {
	require_once PMM_PATH . 'inc/class-mega-menu.php';
}

require_once PMM_PATH . 'inc/utils.php';
