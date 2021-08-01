<?php


namespace Lisfinity\Menus;

use Walker_Nav_Menu;

if ( ! class_exists( 'Walker_Main_Menu' ) ) {
	class Walker_Main_Menu extends Walker_Nav_Menu {

		/**
		 * Starts the list before the elements are added.
		 *
		 * @param string $output Passed by reference. Used to append additional content.
		 * @param int $depth Depth of menu item. Used for padding.
		 * @param array $args An array of wp_nav_menu() arguments.
		 *
		 * @see Walker::start_lvl()
		 *
		 * @since 3.0.0
		 *
		 */
		public function start_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat( "\t", $depth );
			$output .= "\n$indent<div class=\"sub-menu relative hidden lg:absolute\" aria-labelledby=\"subMenu\"><ul class=\"sub-menu-wrapper bg-white rounded\">\n";
		}

		/**
		 * Starts the element output.
		 *
		 * @param string $output Used to append additional content (passed by reference).
		 * @param WP_Post $item Menu item data object.
		 * @param int $depth Depth of menu item. Used for padding.
		 * @param stdClass $args An object of wp_nav_menu() arguments.
		 * @param int $id Current item ID.
		 *
		 * @see Walker::start_el()
		 *
		 * @since 3.0.0
		 * @since 4.4.0 The {@see 'nav_menu_item_args'} filter was added.
		 *
		 */
		function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

			$class_names = $value = '';

			$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'relative menu-item-' . $item->ID . ' px-16';
			$svg       = '';
			if ( $this->has_children ) {
				$classes[] = 'has-dropdown group';
				$svg       = '<svg version="1.1" class="ml-4 w-12 h-12 fill-white" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 64 64" style="enable-background:new 0 0 64 64;" xml:space="preserve"><g><path d="M32,48.1c-1.3,0-2.4-0.5-3.5-1.3L0.8,20.7c-1.1-1.1-1.1-2.7,0-3.7c1.1-1.1,2.7-1.1,3.7,0L32,42.8l27.5-26.1c1.1-1.1,2.7-1.1,3.7,0c1.1,1.1,1.1,2.7,0,3.7L35.5,46.5C34.4,47.9,33.3,48.1,32,48.1z"/></g></svg>';
			}
			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
			$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args );
			$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

			$output .= $indent . '<li' . $id . $value . $class_names . '>';

			$atts           = array();
			$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
			$atts['target'] = ! empty( $item->target ) ? $item->target : '';
			$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
			$atts['href']   = ! empty( $item->url ) ? $item->url : '';

			$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value      = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}

			$item_output = '';
			$item_label  = get_post_meta( $item->ID, 'menu-item-menu-label', true );
			$item_label  = isset( $item_label ) ? $item_label : '';
			if ( ! empty( $item_label ) ) {
				$item_output .= '<span class="menu-label">' . esc_html( $item_label ) . '</span>';
			}
			$a_class     = $this->has_children ? 'has-sub' : '';
			$item_output .= '<a class="flex items-center text-lg font-semibold text-white ' . $a_class . ' " ' . $attributes . '>';
			$item_output .= apply_filters( 'the_title', $item->title, $item->ID );
			if ( $this->has_children ) {
				$item_output .= $svg;
			}
			$item_output .= '</a>';
			$output      .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}

		/**
		 * Ends the element output, if needed.
		 *
		 * @param string $output Used to append additional content (passed by reference).
		 * @param WP_Post $item Page data object. Not used.
		 * @param int $depth Depth of page. Not Used.
		 * @param stdClass $args An object of wp_nav_menu() arguments.
		 *
		 * @since 3.0.0
		 *
		 * @see Walker::end_el()
		 *
		 */
		public function end_el( &$output, $item, $depth = 0, $args = array() ) {
			if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
				$t = '';
				$n = '';
			} else {
				$t = "\t";
				$n = "\n";
			}
			$output .= "</li>{$n}";

		}

	}
}
