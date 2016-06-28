<?php get_header(); ?>

<div id="main-content">
	<div class="container">
		<div id="content-area" class="clearfix">
			<div id="left-area">
		<?php
			$params = array(
'post_type' => 'town',
'posts_per_page' => -1,
'nopaging' => true,			
					);
			$query = new WP_Query( $params );
			
			
			if ( $query->have_posts() ) :
				while ( $query->have_posts() ) : $query->the_post();
				
					locate_template( array( 'town-list.php'), true, false );

					endwhile;
				else :
					get_template_part( 'includes/no-results', 'index' );
				endif;
			?>
			</div> <!-- #left-area -->

			<?php get_sidebar(); ?>
		</div> <!-- #content-area -->
	</div> <!-- .container -->
</div> <!-- #main-content -->

<?php get_footer(); ?>