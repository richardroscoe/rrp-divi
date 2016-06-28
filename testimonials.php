<?php

/**
 * Add the testimonials widget
 */
//include( dirname( __FILE__ ) . '/widgets/widget-testimonials.php' );



add_action( 'init', 'testimonials_post_type' );
/**
 * Creating the custom post type
 *
 * This functions is attached to the 'init' action hook.
 */
function testimonials_post_type() {
	$labels = array(
		'name' => 'Testimonials',
		'singular_name' => 'Testimonial',
		'add_new' => 'Add New',
		'add_new_item' => 'Add New Testimonial',
		'edit_item' => 'Edit Testimonial',
		'new_item' => 'New Testimonial',
		'view_item' => 'View Testimonial',
		'search_items' => 'Search Testimonials',
		'not_found' =>  'No Testimonials found',
		'not_found_in_trash' => 'No Testimonials in the trash',
		'parent_item_colon' => '',
	);

	register_post_type( 'testimonials', array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'exclude_from_search' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'testimonials', 'with_front' => false ),
		'capability_type' => 'post',
		'has_archive' => true,
		'hierarchical' => false,
		'menu_position' => 10,
		'supports' => array( 'editor', 'thumbnail' ),
		'register_meta_box_cb' => 'testimonials_meta_boxes',
	) );
}

/**
 * Adding the necessary metabox
 *
 * This functions is attached to the 'testimonials_post_type()' meta box callback.
 */
function testimonials_meta_boxes() {
	add_meta_box( 'testimonials_form', 'Testimonial Details', 'testimonials_form', 'testimonials', 'normal', 'high' );
}

/**
 * Adding the necessary metabox
 *
 * This functions is attached to the 'add_meta_box()' callback.
 */
function testimonials_form() {
	$post_id = get_the_ID();
	$testimonial_data = get_post_meta( $post_id, '_testimonial', true );
	$client_name = ( empty( $testimonial_data['client_name'] ) ) ? '' : $testimonial_data['client_name'];
	$client_town = ( empty( $testimonial_data['client_town'] ) ) ? '' : $testimonial_data['client_town'];

	wp_nonce_field( 'testimonials', 'testimonials' );
	?>
	<p>
		<label>Client's Name (optional)</label><br />
		<input type="text" value="<?php echo $client_name; ?>" name="testimonial[client_name]" size="40" />
	</p>
	<p>
		<label>Client's Home Town (optional)</label><br />
		<input type="text" value="<?php echo $client_town; ?>" name="testimonial[client_town]" size="40" />
	</p>
	<?php
}

add_action( 'save_post', 'testimonials_save_post' );
/**
 * Data validation and saving
 *
 * This functions is attached to the 'save_post' action hook.
 */
function testimonials_save_post( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;

	if ( ! empty( $_POST['testimonials'] ) && ! wp_verify_nonce( $_POST['testimonials'], 'testimonials' ) )
		return;

	if ( ! empty( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) )
			return;
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return;
	}

	if ( ! wp_is_post_revision( $post_id ) && 'testimonials' == get_post_type( $post_id ) ) {
		remove_action( 'save_post', 'testimonials_save_post' );

		wp_update_post( array(
			'ID' => $post_id,
			'post_title' => 'Testimonial - ' . $post_id
		) );

		add_action( 'save_post', 'testimonials_save_post' );
	}

	if ( ! empty( $_POST['testimonial'] ) ) {
		$testimonial_data['client_name'] = ( empty( $_POST['testimonial']['client_name'] ) ) ? '' : sanitize_text_field( $_POST['testimonial']['client_name'] );
		$testimonial_data['client_town'] = ( empty( $_POST['testimonial']['client_town'] ) ) ? '' : sanitize_text_field( $_POST['testimonial']['client_town'] );

		update_post_meta( $post_id, '_testimonial', $testimonial_data );
	} else {
		delete_post_meta( $post_id, '_testimonial' );
	}
}

add_filter( 'manage_edit-testimonials_columns', 'testimonials_edit_columns' );
/**
 * Modifying the list view columns
 *
 * This functions is attached to the 'manage_edit-testimonials_columns' filter hook.
 */
function testimonials_edit_columns( $columns ) {
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => 'Title',
		'testimonial' => 'Testimonial',
		'testimonial-client-name' => 'Client\'s Name',
		'testimonial-client_town' => 'Business/Site',
		'author' => 'Posted by',
		'date' => 'Date'
	);

	return $columns;
}

add_action( 'manage_posts_custom_column', 'testimonials_columns', 10, 2 );
/**
 * Customizing the list view columns
 *
 * This functions is attached to the 'manage_posts_custom_column' action hook.
 */
function testimonials_columns( $column, $post_id ) {
	$testimonial_data = get_post_meta( $post_id, '_testimonial', true );
	switch ( $column ) {
		case 'testimonial':
			the_excerpt();
			break;
		case 'testimonial-client-name':
			if ( ! empty( $testimonial_data['client_name'] ) )
				echo $testimonial_data['client_name'];
			break;
		case 'testimonial-client_town':
			if ( ! empty( $testimonial_data['client_town'] ) )
				echo $testimonial_data['client_town'];
			break;
	}
}

/**
 * Display a testimonial
 *
 * @param	int $post_per_page  The number of testimonials you want to display
 * @param	string $orderby  The order by setting  https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters
 * @param	array $testimonial_id  The ID or IDs of the testimonial(s), comma separated
 *
 * @return	string  Formatted HTML
 */
function get_testimonial( $posts_per_page = 1, $orderby = 'none', $testimonial_id = null ) {
	$args = array(
		'posts_per_page' => (int) $posts_per_page,
		'post_type' => 'testimonials',
		'orderby' => $orderby,
		'no_found_rows' => true,
	);
	if ( $testimonial_id )
		$args['post__in'] = array( $testimonial_id );

	$query = new WP_Query( $args  );

	$testimonials = '';
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) : $query->the_post();
			$post_id = get_the_ID();
			$testimonial_data = get_post_meta( $post_id, '_testimonial', true );
			$client_name = ( empty( $testimonial_data['client_name'] ) ) ? '' : $testimonial_data['client_name'];
			$client_town = ( empty( $testimonial_data['client_town'] ) ) ? '' : ' - ' . $testimonial_data['client_town'];
			$cite = $client_name . $client_town;

			$testimonials .= '<aside class="testimonial">';
			$testimonials .= '<span class="quote">&ldquo;</span>';
			$testimonials .= '<div class="entry-content">';
			$testimonials .= '<p class="testimonial-text">' . get_the_content() . '<span></span></p>';
			$testimonials .= '<p class="testimonial-client-name"><cite>' . $cite . '</cite>';
			$testimonials .= '</div>';
			$testimonials .= '</aside>';

		endwhile;
		wp_reset_postdata();
	}

	return $testimonials;
}

add_shortcode( 'testimonial', 'testimonial_shortcode' );
/**
 * Shortcode to display testimonials
 *
 * This functions is attached to the 'testimonial' action hook.
 *
 * [testimonial posts_per_page="1" orderby="none" testimonial_id=""]
 */
function testimonial_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'posts_per_page' => '1',
		'orderby' => 'none',
		'id' => '',
		'height' => '',
		'width' => '',
	), $atts ) );

	return get_testimonial( $posts_per_page, $orderby, $id, $height, $width );
}