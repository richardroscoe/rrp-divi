<?php

add_action( 'init', 'town_post_type' );

/**
 * Creating the custom post type
 *
 * This functions is attached to the 'init' action hook.
 */
function town_post_type() {
	$labels = array(
		'name' => 'Towns',
		'singular_name' => 'Town',
		'add_new' => 'Add New',
		'add_new_item' => 'Add New Town',
		'edit_item' => 'Edit Town',
		'new_item' => 'New Town',
		'view_item' => 'View Town',
		'search_items' => 'Search Towns',
		'not_found' =>  'No Towns found',
		'not_found_in_trash' => 'No Towns in the trash',
		'parent_item_colon' => '',
	);

	register_post_type( 'town', array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'exclude_from_search' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'town', 'with_front' => false ),
		'capability_type' => 'post',
		'has_archive' => true,
		'hierarchical' => false,
		'menu_position' => 10,
		'supports' => array( '' ),
		'register_meta_box_cb' => 'town_meta_boxes',
	) );
}

/**
 * Adding the necessary metabox
 *
 * This functions is attached to the 'town_post_type()' meta box callback.
 */
function town_meta_boxes() {
	add_meta_box( 'town_form', 'Town Details', 'town_form', 'town', 'normal', 'high' );
}

/**
 * Adding the necessary metabox
 *
 * This functions is attached to the 'add_meta_box()' callback.
 */
function town_form() {
	$post_id = get_the_ID();
	$town_data = get_post_meta( $post_id, '_town', true );
	$town_name = ( empty( $town_data['town_name'] ) ) ? '' : $town_data['town_name'];

	wp_nonce_field( 'townnonce', 'town' );
	?>
	<p>
		<label>Town's Name</label><br />
		<input type="text" value="<?php echo $town_name; ?>" name="town[town_name]" size="40" />
	</p>
	<?php
}

add_action( 'save_post', 'town_save_post' );
/**
 * Data validation and saving
 *
 * This functions is attached to the 'save_post' action hook.
 */
function town_save_post( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;
//echo "<pre>"; print_r($_POST); echo "</pre>";
//echo "<pre>nonce returns:". wp_verify_nonce( $_POST['townnonce'], 'town' ) ."</pre>";
	if ( ! empty( $_POST['townnonce'] ) && ! wp_verify_nonce( $_POST['townnonce'], 'town' ) )
		return;
//echo "<pre>Thru nonce</pre>";
	if ( ! empty( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) )
			return;
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return;
	}
//echo "<pre>Thru sec</pre>";
	if ( ! empty( $_POST['town'] ) ) {
		$town_data['town_name'] = ( empty( $_POST['town']['town_name'] ) ) ? '' : sanitize_text_field( $_POST['town']['town_name'] );
	}

	if ( ! wp_is_post_revision( $post_id ) && 'town' == get_post_type( $post_id ) ) {
		remove_action( 'save_post', 'town_save_post' );

		$post_title = (! empty( $_POST['town'] ) && $town_data['town_name'] != '') ?  $town_data['town_name'] : 'Town - ' . $post_id;
		$post_name = sanitize_title($post_title);
//echo "<pre>post_tile = $post_title</pre>";
		wp_update_post( array(
			'ID' => $post_id,
			'post_title' => $post_title,
			'post_name' => $post_name
		) );

		add_action( 'save_post', 'town_save_post' );
	}

	if ( ! empty( $_POST['town'] ) ) {
//		$town_data['town_name'] = ( empty( $_POST['town']['town_name'] ) ) ? '' : sanitize_text_field( $_POST['town']['town_name'] );
		update_post_meta( $post_id, '_town', $town_data );
	} else {
		delete_post_meta( $post_id, '_town' );
	}
}

add_filter( 'manage_edit-town_columns', 'town_edit_columns' );
/**
 * Modifying the list view columns
 *
 * This functions is attached to the 'manage_edit-town_columns' filter hook.
 */
function town_edit_columns( $columns ) {
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => 'Title',
		'town-name' => 'Towns\'s Name',
		'author' => 'Posted by',
		'date' => 'Date'
	);

	return $columns;
}

add_action( 'manage_posts_custom_column', 'town_columns', 10, 2 );
/**
 * Customizing the list view columns
 *
 * This functions is attached to the 'manage_posts_custom_column' action hook.
 */
function town_columns( $column, $post_id ) {
	$town_data = get_post_meta( $post_id, '_town', true );
	switch ( $column ) {
		case 'town-name':
			if ( ! empty( $town_data['town_name'] ) )
				echo $town_data['town_name'];
			break;
	}
}

/**
 * Display a town
 *
 * @param	int $post_per_page  The number of town you want to display
 * @param	string $orderby  The order by setting  https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters
 * @param	array $town_id  The ID or IDs of the town(s), comma separated
 *
 * @return	string  Formatted HTML
 */
function get_town( $posts_per_page = 1, $orderby = 'none', $town_id = null ) {
	$args = array(
		'posts_per_page' => (int) $posts_per_page,
		'post_type' => 'town',
		'orderby' => $orderby,
		'no_found_rows' => true,
	);
	if ( $town_id )
		$args['post__in'] = array( $town_id );

	$query = new WP_Query( $args  );

	$town = '';
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) : $query->the_post();
			$post_id = get_the_ID();
			$town_data = get_post_meta( $post_id, '_town', true );
			$town_name = ( empty( $town_data['town_name'] ) ) ? '' : $town_data['town_name'];

			$town .= '<p class="town-name"><cite>' . $town_name . '</cite>';
		endwhile;
		wp_reset_postdata();
	}

	return $town;
}

add_shortcode( 'town', 'town_shortcode' );
/**
 * Shortcode to display town
 *
 * This functions is attached to the 'town' action hook.
 *
 * [town posts_per_page="1" orderby="none" town_id=""]
 */
function town_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'posts_per_page' => '1',
		'orderby' => 'none',
		'town_id' => '',
	), $atts ) );

	return get_town( $posts_per_page, $orderby, $town_id );
}
?>