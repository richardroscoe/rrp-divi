
<div class="post-content post-content-box">
<div class="post-content-content">
			<?php 
				$town_data = get_post_meta( get_the_ID(), '_town', true );
				$town_name = ( empty( $town_data['town_name'] ) ) ? '' : $town_data['town_name'];
			?>

				<article id="post-<?php the_ID(); ?>" <?php post_class( 'town' ); ?>>
					<div class="entry-content">
						<p class="town-client-name"><a href="<?php echo get_permalink(get_the_ID()); ?>"><?php echo $town_name; ?></a></p>
					</div>
				</article>
</div>
</div>

