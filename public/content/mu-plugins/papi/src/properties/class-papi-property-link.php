<?php

/**
 * WordPress link manager property.
 */
class Papi_Property_Link extends Papi_Property {

	/**
	 * The convert type.
	 *
	 * @var string
	 */
	public $convert_type = 'object';

	/**
	 * The default value.
	 *
	 * @var array
	 */
	public $default_value = [];

	/**
	 * Link fields.
	 *
	 * @var array
	 */
	protected $link_fields = [
		'post_id',
		'url',
		'title',
		'target'
	];

	/**
	 * Delete value from the database.
	 *
	 * @param  string $slug
	 * @param  int    $post_id
	 * @param  string $type
	 *
	 * @return bool
	 */
	public function delete_value( $slug, $post_id, $type ) {
		$values = $this->load_value( null, $slug, $post_id );
		$result = true;

		foreach ( $values as $key => $val ) {
			$out    = papi_delete_property_meta_value( $post_id, $slug . '_' . $key );
			$result = $out ? $result : $out;
		}

		if ( $result ) {
			$result = papi_delete_property_meta_value( $post_id, $slug );
		}

		return $result;
	}

	/**
	 * Format the value of the property before it's returned
	 * to WordPress admin or the site.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return mixed
	 */
	public function format_value( $value, $slug, $post_id ) {
		return $this->load_value( $value, $slug, $post_id );
	}

	/**
	 * Get default settings.
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return [];
	}

	/**
	 * Load value from database.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return array
	 */
	public function load_value( $value, $slug, $post_id ) {
		$values = $this->link_fields;

		if ( is_array( $value ) || is_object( $value ) ) {
			$values = (array) $value;

			foreach ( $values as $key => $val ) {
				unset( $values[$key] );
				$key = preg_replace( '/^' . $slug . '\_/', '', $key );
				$values[$key] = $val;
			}
		} else {
			$type = $post_id === 0 ? 'option' : 'post';
			foreach ( $values as $index => $key ) {
				$values[$key] = papi_get_property_meta_value(
					$post_id,
					sprintf( '%s_%s', $slug, $key ),
					$type
				);
				unset( $values[$index] );
			}
		}

		$link = (object) $values;

		if ( isset( $link->$slug ) && is_numeric( $link->$slug ) ) {
			unset( $link->$slug );
		}

		return $this->prepare_link_array( $link );
	}

	/**
	 * Render property html.
	 */
	public function html() {
		$value = $this->get_value();
		$value = is_array( $value ) || is_object( $value ) ? $value : [];
		$value = (object) $value;
		?>
		<div class="papi-property-link" data-replace-slug="true" data-slug="<?php echo $this->html_name(); ?>">
			<input type="hidden" name="<?php echo $this->html_name(); ?>">

			<?php if ( ! empty( $value->url ) ): ?>
				<table class="papi-table link-table">
					<tbody>
						<tr>
							<td><?php _e( 'URL', 'papi' ); ?></td>
							<td>
								<a href="<?php echo $value->url; ?>" target="_blank"><?php echo $value->url; ?></a>
								<input type="hidden" value="<?php echo $value->title . ' - ' . $value->url; ?>" data-papi-rule="<?php echo $this->html_name(); ?>">
								<input class="wp-link-url" type="hidden" value="<?php echo $value->url; ?>" name="<?php echo $this->html_name(); ?>[url]">
							</td>
						</tr>
						<tr>
							<td><?php _e( 'Title', 'papi' ); ?></td>
							<td>
								<?php echo $value->title; ?>
								<input class="wp-link-text" type="hidden" value="<?php echo $value->title; ?>" name="<?php echo $this->html_name(); ?>[title]">
							</td>
						</tr>
						<tr>
							<td><?php _e( 'Target', 'papi' ); ?></td>
							<td>
								<?php echo $value->target === '_blank' ? __( 'New window', 'papi' ) : __( 'Same window', 'papi' ); ?>
								<input class="wp-link-target" type="hidden" value="<?php echo $value->target; ?>" name="<?php echo $this->html_name(); ?>[target]">
							</td>
						</tr>
					</tbody>
				</table>
			<?php endif; ?>

			<p class="papi-file-select">
				<span class="<?php echo empty( $value->url ) ? '' : 'papi-hide'; ?>">
					<?php _e( 'No link selected', 'papi' ); ?>
					<button class="button" data-link-action="add"><?php _e( 'Add link', 'papi' ); ?></button>
				</span>
				<span class="<?php echo empty( $value->url ) ? 'papi-hide' : ''; ?>">
					<button class="button" data-link-action="edit"><?php _e( 'Edit link', 'papi' ); ?></button>
					<button class="button" data-link-action="remove"><?php _e( 'Remove link', 'papi' ); ?></button>
				</span>
			</p>
		</div>
		<?php
	}

	/**
	 * Import value to the property.
	 *
	 * @param  mixed  $value
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return mixed
	 */
	public function import_value( $value, $slug, $post_id ) {
		if ( is_array( $value ) || is_object( $value ) ) {
			return $this->update_value( (array) $value, $slug, $post_id );
		}
	}

	/**
	 * Prepare link array with post id. If it gets a post id
	 * bigger then zero it will use the permalink as url.
	 *
	 * @param array|object $link
	 *
	 * @return array|object
	 */
	protected function prepare_link_array( $link ) {
		$array  = is_array( $link );
		$link = (object) $link;

		// Don't continue without a url.
		if ( ! isset( $link->url ) || empty( $link->url ) ) {
			return $array ? (array) $link : $link;
		}

		// Don't overwrite existing post id.
		if ( ! isset( $link->post_id ) || empty( $link->post_id ) ) {
			$link->post_id = url_to_postid( $link->url );
		}

		// Only replace url when post id is not zero.
		if ( $link->post_id > 0 ) {
			$link->url = get_permalink( $link->post_id );
		}

		// If empty target set `_self` as default target.
		if ( empty( $link->target ) ) {
			$link->target = '_self';
		}

		return $array ? (array) $link : $link;
	}

	/**
	 * Render link template.
	 */
	public function render_link_template() {
		?>
		<script type="text/template" id="tmpl-papi-property-link">
			<table class="papi-table link-table">
				<tbody>
					<tr>
						<td>
							<?php _e( 'URL', 'papi' ); ?>
						</td>
						<td>
							<%= link %>
							<input type="hidden" value="<%= title %> - <%= href %>" data-papi-rule="<%= slug %>">
							<input class="wp-link-url" type="hidden" value="<%= href %>" name="<%= slug %>[url]">
						</td>
					</tr>
					<tr>
						<td>
							<?php _e( 'Title', 'papi' ); ?>
						</td>
						<td>
							<%= title %>
							<input class="wp-link-text" type="hidden" value="<%= title %>" name="<%= slug %>[title]">
						</td>
					</tr>
					<tr>
						<td>
							<?php _e( 'Target', 'papi' ); ?>
						</td>
						<td>
							<input class="wp-link-target" type="hidden" value="<%= target %>" name="<%= slug %>[target]">
							<%= target === '_blank' ? '<?php _e( 'New window', 'papi' ) ?>' : '<?php _e( 'Same window', 'papi' ); ?>' %>
						</td>
					</tr>
				</tbody>
			</table>
		</script>
		<?php
	}

	/**
	 * Prepare value for the database.
	 *
	 * @param  mixed  $values
	 * @param  string $slug
	 * @param  int    $post_id
	 *
	 * @return array
	 */
	public function update_value( $values, $slug, $post_id ) {
		if ( ! isset( $values['url'] ) ) {
			$values = $this->link_fields;

			foreach ( $values as $index => $key ) {
				$values[sprintf( '%s_%s', $slug, $key )] = '';
				unset( $values[$index] );
			}

			// Delete the required field.
			$values[$slug] = '';

			return $values;
		}

		// If a url exists we can continue making the meta fields
		// that should be saved.
		$values = $this->prepare_link_array( $values );

		foreach ( $values as $key => $val ) {
			$values[sprintf( '%s_%s', $slug, $key )] = $val;
			unset( $values[$key] );
		}

		// Required field so Papi can load a value from the
		// original property slug.
		$values[$slug] = 1;

		return $values;
	}

	/**
	 * Setup actions.
	 */
	protected function setup_actions() {
		add_action( 'admin_head', [$this, 'render_link_template'] );
	}
}
