<?php
/**
 * Original author:          Rajeev <rajeev.kushwaha@miritech.com>
 * Created on:      28/08/2018
 * Edited by nomadi: march 2022
 *
 * @package Neve
 */

// Custom code for tour listing page and tour inner page to display tours

$container_class = apply_filters( 'neve_container_class_filter', 'container', 'single-post' );

get_header();



?>


<div class="<?php echo esc_attr( $container_class ); ?> single-post-container">
	<div class="row">


		<article id="post-<?php echo esc_attr( get_the_ID() ); ?>"
				class="<?php echo $parent_slug;?> <?php echo esc_attr( join( ' ', get_post_class( 'nv-single-post-wrap col' ) ) ); ?> <?php echo $class_parent;?>">

			<?php
			
			
			
			// Normal loop
			if ( have_posts() ) {
				while ( have_posts() ) {
					the_post();
						
					the_content( );
		
				}
			}

			?>


			
			

		</article>

			


		
	</div>

	<?php
 	// Get BOKUN widget, strip away style tag and use script only once on page
	$booking_cta =  get_field('bokun_booking_widget', $post->ID );

	if( $booking_cta ): 
		$script =  wm_strip_tags_content($booking_cta, '<script>');
		$button =  wm_strip_tags_content($booking_cta, '<button>');
		$button_text = get_field('cta_button_text', 'option'); 
		if ( function_exists('wm_replace_button_text') ) $button = wm_replace_button_text($button, $button_text); 
			
		echo $script; 
	 	?>
    	<div class="booking-cta-container">
    		<div class="booking-cta-content">
			<h2><?php the_field('cta_heading', 'option'); ?></h2>
			<?php echo  $button; ?>

       	 	</div>
		</div>
		<?php 
	endif;
 	?>
				
</div>
<?php
get_footer();