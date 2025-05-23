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

		<?php do_action( 'neve_do_sidebar', 'single-post', 'left' ); ?>

		<article id="post-<?php echo esc_attr( get_the_ID() ); ?>"
				class="<?php echo $parent_slug;?> <?php echo esc_attr( join( ' ', get_post_class( 'nv-single-post-wrap col' ) ) ); ?> <?php echo $class_parent;?>">
			<?php
			
			do_action( 'neve_before_post_content' );


			if ( have_posts() ) {
				while ( have_posts() ) {
					the_post();
						
					the_content( );
		
				}
			}

			?>


			<?php 
			$parent_id = wp_get_post_parent_id($post->ID);
			$parent_slug = get_post_field( 'post_name',$parent_id );
			$parent_title = get_the_title($parent_id);
			$current_id = $post->ID;

			  
         if ($parent_id == 0):  
      		include('acf_blocks/tours_list.php');
         else:
      	?>
      	

			<div class="tours allignfull mt-4 tour-pagination">
		        
            <?php 

            

				    $all_posts = new WP_Query(array(
				    	  'wpse_include_parent' => true,
				    	  'post_parent'         => $parent_id,
				        'orderby' => 'menu_order',
				        'order' => 'ASC',
				        'posts_per_page' => -1,
				        'post_type' => 'tour'
				    ));

				    foreach($all_posts->posts as $key => $value) {
				        if($value->ID == $post->ID){
				            $next_id = $all_posts->posts[$key + 1]->ID;
				            //$prev_id = $all_posts->posts[$key - 1]->ID;
				            break;
				        }
				    }
					?>

               <div class="pagi_pre">
						<?php 
						echo "\t" . '<a rel="prev" href="' . get_permalink($parent_id)  . '" title="' . $parent_title. '" class=" ">← '. $parent_title . '</strong></a>' . "\n";
						?>
               </div>

               <div class="pagi_next">

						<?php 
						$next_post_title = get_the_title($next_id);
						$day_title = __('Day', 'xwander').' '.get_field('tour_day_number',$next_id);

						if($next_post_title && $next_id) {
						  
						   echo "\t" . '<a rel="next" href="' . get_permalink($next_id) . '" title="' . $next_post_title. '" class=" ">'.$day_title. ' →</strong></a>' . "\n";
						}?>
						
					</div>

				
				
	    </div>
	    <?php endif; ?>
	<?php
		// Fetch calendar id from main tour page
		 	$calendar_page_id = ($parent_id) ? $parent_id : $current_id;  
		 	$booking_calendar_id = get_field('fareharbor_calendar_id', $calendar_page_id);
			
			?>
		
	

		<?php
			
			do_action( 'neve_after_post_content' );
			?>
		
		</article>

			


		<?php do_action( 'neve_do_sidebar', 'single-post', 'right' ); ?>
	</div>

	<?php
		// Fetch calendar id from main tour page
		 	
			if( $booking_calendar_id ): 
		 	?>
        	<div class="booking-container">
        		<div id="booking"  class="booking-enquiry-sec">
		
	<script type="text/javascript" src="https://widgets.bokun.io/assets/javascripts/apps/build/BokunWidgetsLoader.js?bookingChannelUUID=7abc9ee0-e49f-4420-ab27-4131eebbec8f" async></script>
     
    <div class="bokunWidget" data-src="https://widgets.bokun.io/online-sales/7abc9ee0-e49f-4420-ab27-4131eebbec8f/experience-calendar/680252"></div>
    <noscript>Please enable javascript in your browser to book</noscript>


           	 	</div>
			</div>
			<?php 
			endif;
		 ?>
				
</div>
<?php
get_footer();