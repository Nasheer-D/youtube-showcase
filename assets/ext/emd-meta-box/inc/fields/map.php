<?php
// Prevent loading this file directly
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'EMD_MB_Map_Field' ) )
{
	class EMD_MB_Map_Field extends EMD_MB_Field
	{
		/**
		 * Enqueue scripts and styles
		 *
		 * @return void
		 */
		static function admin_enqueue_scripts()
		{
			wp_enqueue_script( 'googlemap', 'https://maps.google.com/maps/api/js?sensor=false', array(), '', true );
			wp_enqueue_script( 'emd-mb-map', EMD_MB_JS_URL . 'map.js', array( 'jquery', 'jquery-ui-autocomplete', 'googlemap' ), EMD_MB_VER, true );
		}

		/**
		 * Get field HTML
		 *
		 * @param mixed  $meta
		 * @param array  $field
		 *
		 * @return string
		 */
		static function html( $meta, $field )
		{
			$address = isset( $field['address_field'] ) ? $field['address_field'] : false;

			$html = '<div class="emd-mb-map-field">';

			$html .= sprintf(
				'<div class="emd-mb-map-canvas" style="%s"%s></div>
				<input type="hidden" name="%s" class="emd-mb-map-coordinate" value="%s">',
				isset( $field['style'] ) ? $field['style'] : '',
				isset( $field['std'] ) ? " data-default-loc=\"{$field['std']}\"" : '',
				$field['field_name'],
				$meta
			);

			if ( $address )
			{
				$html .= sprintf(
					'<button class="button emd-mb-map-goto-address-button" value="%s">%s</button>',
					is_array( $address ) ? implode( ',', $address ) : $address,
					__( 'Find Address', 'emd-plugins' )
				);
			}

			$html .= '</div>';

			return $html;
		}
	}
}
