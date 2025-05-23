<?php
// Check if individual tours selected
$selected_tours = get_field('tour_packages');

if( $selected_tours ): ?>

<div class="carousel-container">
	<div class="featured-tours-container">
	<?php foreach( $selected_tours as $post ):
		setup_postdata($post);
		$badge = get_field('tour_badge', $post->ID); 
		$title = get_the_title($post->ID);
		$excerpt = get_field( 'tour_main_description',$post->ID );
		$duration = get_field('tour_duration', $post->ID ).' '.get_field('tour_duration_suffix', $post->ID );
		$price = get_field('tour_price_starting_from', $post->ID );
		$image_id = ( get_field('hero_image',$post->ID) ) ? get_field('hero_image',$post->ID) : get_field('default_hero_image', 'option');
		$img1x = wp_get_attachment_image_url( $image_id, 'listing');
		$img2x = wp_get_attachment_image_url( $image_id, 'listing-2x');
		?>
		<?php include('content_tour-item.php'); ?>
	<?php endforeach; ?>

	<?php
		// Reset the global post object so that the rest of the page works correctly.
		wp_reset_postdata();
	 ?>
	</div>
</div>
<?php endif;


    