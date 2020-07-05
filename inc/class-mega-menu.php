<?php
/**
 * PixelPlus Mega Menu Plugin.
 *
 * @package PixelPlus
 */

namespace PixelPlus;

defined( 'ABSPATH' ) || exit;

/**
 * Mega_Menu
 */
class Mega_Menu {

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_filter( 'wp_edit_nav_menu_walker', array( $this, 'mega_menu_filter_walker' ), 99 );
			add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'mega_menu_fields' ), 10, 4 );
			add_action( 'wp_update_nav_menu_item', array( $this, 'save_mega_menu' ), 10, 3 );

			global $pagenow;
			if ( 'nav-menus.php' === $pagenow ) {

				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 11 );

			}
		}

		add_filter( 'primary_menu_args', array( $this, 'menu_args' ) );
	}

	public function enqueue_scripts() {
		wp_enqueue_media();

		wp_enqueue_style( 'pixelplus-admin-mega-menu', PMM_URL . 'assets/css/admin-menu.css', array(), '1.0.0' );
		wp_enqueue_script( 'pixelplus-admin-mega-menu', PMM_URL . 'assets/js/admin-menu.js', array( 'jquery' ), '1.0.0', true );

		wp_localize_script(
			'pixelplus-admin-mega-menu',
			'pamm',
			array(
				'media_title'  => __( 'Insert a media', 'pixelplus' ),
				'media_button' => __( 'Insert Media', 'pixelplus' ),
			)
		);
	}

	public function mega_menu_filter_walker( $walker ) {

		$new_walker = 'Pixelplus_Walker_Nav_Menu_Edit';

		return class_exists( $new_walker ) ? $new_walker : $walker;
	}

	public function mega_menu_fields( $id, $item, $depth, $args ) {
		$mega_menu_data = get_post_meta( $item->ID, '_menu_data', true );

		foreach ( $this->get_menu_fields() as $field ) :
			$field = wp_parse_args(
				$field,
				array(
					'default'     => false,
					'level0_only' => false,
					'sub_only'    => false,
				)
			);

			$field_id    = sprintf( 'edit-menu-item-%s-%s', $field['id'], $item->ID );
			$field_name  = sprintf( 'menu-item-%s[%s]', $field['id'], $item->ID );
			$meta_key    = '_' . str_replace( '-', '_', $field['id'] );
			$field_value = isset( $mega_menu_data[ $meta_key ] ) ? $mega_menu_data[ $meta_key ] : $field['default'];

			switch ( $field['type'] ) {
				case 'checkbox':
					$field_html = sprintf(
						'<label for="%s"><input type="%s" id="%s" name="%s" value="%s" %s />%s</label>',
						$field_id,
						$field['type'],
						$field_id,
						$field_name,
						$field_value ? 'yes' : 'no',
						checked( $field_value, 'yes', false ),
						$field['label']
					);

					break;

				case 'select':
					$options_html = '';

					foreach ( $field['options'] as $option_key => $option_label ) {
						$options_html .= sprintf(
							'<option value="%s" %s>%s</option>',
							$option_key,
							$option_key == $field_value ? 'selected' : '',
							$option_label
						);
					}

					$field_html = sprintf(
						'<label for="%s">%s</label><select class="" id="%s" name="%s">%s</select>',
						$field_id,
						$field['label'],
						$field_id,
						$field_name,
						$options_html
					);

					break;

				case 'image':
					$field_html = sprintf(
						'<button type="button" class="menu-img%s dashicons dashicons-admin-media" style="background-image:url(%s)"><span class="dashicons dashicons-dismiss"></span></button><input type="hidden" id="%s" name="%s" value="%s" />',
						! empty( $field_value ) ? ' has-img' : '',
						$field_value,
						$field_id,
						$field_name,
						$field_value
					);

					break;

				default:
					$field_html = sprintf(
						'<label for="%s">%s</label><input type="%s" class="" id="%s" name="%s" value="%s" />',
						$field_id,
						$field['label'],
						$field['type'],
						$field_id,
						$field_name,
						$field_value
					);
					break;
			}

			$class = ' hidden-field';

			$class = $field['sub_only'] ? ' level1-field' : '';
			$class = $field['level0_only'] ? ' level0-field' : $class;

			printf(
				'<p class="description description-wide mm-field field-%s%s">%s</p>',
				$field['id'],
				$class,
				$field_html,
			);

		endforeach;
	}

	public function get_menu_fields() {
		return array(
			array(
				'id'          => 'mega_menu',
				'type'        => 'checkbox',
				'level0_only' => true,
				'label'       => __( 'Enable Mega menu?', 'pixelplus' ),
			),
			array(
				'id'          => 'column',
				'type'        => 'select',
				'default'     => 4,
				'level0_only' => true,
				'options'     => array(
					1 => __( '1', 'pixelplus' ),
					2 => __( '2', 'pixelplus' ),
					3 => __( '3', 'pixelplus' ),
					4 => __( '4', 'pixelplus' ),
					5 => __( '5', 'pixelplus' ),
					6 => __( '6', 'pixelplus' ),
				),
				'label'       => __( 'Columns', 'pixelplus' ),
			),
			array(
				'id'       => 'is_heading_column',
				'type'     => 'checkbox',
				'sub_only' => true,
				'label'    => __( 'Column Title?', 'pixelplus' ),
			),
			array(
				'id'       => 'image',
				'type'     => 'image',
				'sub_only' => true,
				'label'    => __( 'Image', 'pixelplus' ),
			),
		);
	}

	public function save_mega_menu( $menu_id, $menu_item_db_id, $menu_item_args ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

		$fields = $this->get_menu_fields();

		$meta_value = get_post_meta( $menu_item_db_id, '_menu_data', true );

		$meta_value = ! empty( $meta_value ) ? $meta_value : array();

		foreach ( $fields as $field ) :
			$key      = sprintf( 'menu-item-%s', $field['id'] );
			$data_key = '_' . str_replace( '-', '_', $field['id'] );

			// Sanitize.
			if ( isset( $_POST[ $key ][ $menu_item_db_id ] ) ) {
				$value = sanitize_text_field( wp_unslash( $_POST[ $key ][ $menu_item_db_id ] ) );
				// Do some checks here...
				$meta_value[ $data_key ] = $value;
			} else {
				if ( 'checkbox' === $field['type'] ) {
					$meta_value[ $data_key ] = 'no';
				}
			}

			// Update.
		endforeach;

		if ( ! empty( $meta_value ) ) {
			update_post_meta( $menu_item_db_id, '_menu_data', $meta_value );
		} else {
			delete_post_meta( $menu_item_db_id, '_menu_data' );
		}
	}

	public function menu_args( $args ) {
		$args['walker'] = new Mega_Menu_Walker();

		return $args;
	}
}

new Mega_Menu();
