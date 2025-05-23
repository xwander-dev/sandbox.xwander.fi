<?php

$cat_id = get_field('tour_selected_category', $post->ID );	

$taxonomies = get_terms( array(
	'taxonomy' => 'tour-category',
	'hide_empty' => false
) );

$terms = array();

foreach ($taxonomies as $key => $value) 
{	
	$terms[$value->term_id] = $value->name;
}



if ($cat_id) :
	
	$args = array(
		'numberposts'	=> -1,
		'orderby' => 'menu_order', //'tour_cat',
		'post_type'		=> 'tour',
		'meta_query'	=> array(
			
			array(
				'key'	 	=> 'tour_cat',
				'value'	  	=> $cat_id,
				'compare' 	=> 'IN',
			)
		),
	);

	$tour_query = new WP_Query($args);

	$selected_tours = $tour_query->posts;
?>


<div>

	
	
<?php 
$current_cat = '';

foreach( $selected_tours as $post ): 
    setup_postdata($post); 

   
    $badge = get_field('tour_badge', $post->ID); 
    $title = get_the_title($post->ID); 
    $excerpt = get_field( 'tour_main_description',$post->ID );
    $duration_suffix = get_field('tour_duration_suffix', $post->ID ); 
    $duration = get_field('tour_duration', $post->ID ).' '. __($duration_suffix, 'xwander');
    $price = get_field('tour_price_starting_from', $post->ID );

    $image_id = ( get_field('hero_image',$post->ID) ) ? get_field('hero_image',$post->ID) : get_field('default_hero_image', 'option');

  

	$img1x = wp_get_attachment_image_url( $image_id, 'listing');
    $img2x = wp_get_attachment_image_url( $image_id, 'listing-2x');

    $cat_id = get_field('tour_cat', $post->ID);
    $cat_desc = strip_tags( category_description($cat_id) );
    
    $main_cat = $terms[$cat_id];
	
	
	if ($main_cat != $current_cat)
	{
		echo '</div><h2 class="featured">'.$main_cat.'</h2><p class="tour-cat-desc">'.$cat_desc.'</p>';
		echo '<div class="featured-tours-container">';		
		$current_cat = $main_cat;
	} 
           	
    include('content_tour-item.php');

 endforeach; ?>

<?php 
    // Reset the global post object so that the rest of the page works correctly.
    wp_reset_postdata();
 ?>
</div>
<?php endif;


    