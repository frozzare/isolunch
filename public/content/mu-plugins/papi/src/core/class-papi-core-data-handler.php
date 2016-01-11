<?php

/**
 * Core class that handle core post data.
 */
class Papi_Core_Data_Handler {

	/**
	 * The fields that should be overwritten.
	 *
	 * @var array
	 */
	protected $overwrite = [];

	/**
	 * Decode property.
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @return mixed
	 */
	protected function decode_property( $key, $value ) {
		if ( papi_is_property_type_key( $key ) && is_string( $value ) ) {
			$value = base64_decode( $value );
			$value = papi_maybe_json_decode( $value );
		}

		return $value;
	}

	/**
	 * Get post data.
	 *
	 * @param  string $pattern
	 *
	 * @return array
	 */
	protected function get_post_data( $pattern = '/^papi\_.*/' ) {
		$data = [];
		$keys = preg_grep( $pattern, array_keys( $_POST ) );

		foreach ( $keys as $key ) {
			// Fix for input fields that should be true on `on` value.
			if ( $_POST[$key] === 'on' ) {
				$data[$key] = true;
			} else {
                $value = $this->decode_property( $key, $_POST[$key] );
                $data[$key] = $this->prepare_post_data( $value );
                $data[$key] = $this->santize_data( $data[$key] );
			}
		}

		// Don't wont to save meta nonce field.
		if ( isset( $data['papi_meta_nonce'] ) ) {
			unset( $data['papi_meta_nonce'] );
		}

		return $data;
	}

	/**
	 * Get pre data that should be saved before all properties data.
	 */
	protected function get_pre_data() {
		return $this->get_post_data( '/^\_papi\_.*/' );
	}

	/**
	 * Pre get deep keys and value.
	 *
	 * Used for saving pre data when properties are in a flexible or repeater.
	 *
	 * @param  array $arr
	 *
	 * @return array
	 */
	protected function get_pre_deep_keys_value( array $arr ) {
		$keys  = [];
		$value = null;

		foreach ( $arr as $key => $v ) {
			if ( is_array( $v ) ) {
				$keys[] = $key;
				list( $ks, $val ) = $this->get_pre_deep_keys_value( $v );
				$keys   = array_merge( $keys, $ks );
				$value  = $val;
			} else {
				$keys[] = $key;
				$value  = $v;
			}
		}

		return [$keys, $value];
	}

	/**
	 * Prepare post data.
	 * Will decode property options recursive.
	 *
	 * @param  mixed $data
	 *
	 * @return mixed
	 */
	protected function prepare_post_data( $data ) {
		if ( ! is_array( $data ) ) {
			return $data;
		}

		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				$data[$key] = $this->prepare_post_data( $value );
			} else {
				$data[$key] = $this->decode_property( $key, $value );
			}
		}

		return $data;
	}

	/**
	 * Prepare properties data for saving.
	 *
	 * @param  array $data
	 * @param  int   $post_id
	 *
	 * @return array
	 */
	protected function prepare_properties_data( array $data = [], $post_id = 0 ) {
		// Since we are storing witch property it is in the $data array
		// we need to remove that and set the property type to the property
		// and make a array of the property type and the value.
		foreach ( $data as $key => $value ) {
			$property_type_key = papi_get_property_type_key();

			if ( strpos( $key, $property_type_key ) === false ) {
				continue;
			}

			$property_key = str_replace( $property_type_key, '', $key );

			// Check if value exists.
			if ( isset( $data[$property_key] ) ) {
				$data[$property_key] = [
					'type'  => $value,
					'value' => $data[$property_key]
				];
			}

			unset( $data[$key] );
		}

		foreach ( $data as $key => $item ) {
			$property = papi_get_property_type( $item['type'] );

			unset( $data[ $key ] );

			if ( papi_is_property( $property ) ) {
				// Run `update_value` method on the property class.
				$data[$key] = $property->update_value(
					$item['value'],
					papi_remove_papi( $key ),
					$post_id
				);

				// Apply `update_value` filter so this can be changed from the theme for specified property type.
				$data[$key] = papi_filter_update_value(
					$item['type']->type,
					$data[$key],
					papi_remove_papi( $key ),
					$post_id
				);

				if ( $item['type']->overwrite ) {
					$slug = papi_remove_papi( $key );
					$this->overwrite[$slug] = $data[$key];
				}
			}
		}

		return $data;
	}

	/**
	 * Sanitize data before saving it.
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	private function santize_data( $value ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $k => $v ) {
				if ( is_string( $v ) ) {
					$value[$k] = $this->santize_data( $v );
				}
			}
		} else if ( is_string( $value ) ) {
			$value = papi_remove_trailing_quotes( $value );
		}

		return $value;
	}
}
