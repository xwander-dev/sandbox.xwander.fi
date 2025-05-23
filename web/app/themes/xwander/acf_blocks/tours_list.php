  <?php
global $post;
$parent_id = $post->ID;

$loop = new WP_Query(array(
	  
	  'post_parent'         => $parent_id,
    'orderby' => 'menu_order',
    'order' => 'ASC',
    'posts_per_page' => -1,
    'post_type' => 'tour'
));


if ( $loop->have_posts() ):
?>

<div class="tours allignfull mt-4 tour-pagination">
<div class="tour-section">

<?php 


$i = 1;
while ( $loop->have_posts() ) : $loop->the_post(); 


	$p_id = get_the_ID();
	$otsikko = get_the_title();
	
	
	?>
			            
    <div class="wp-block-column m-0">
        <div class="wp-block-classic related-tour home-tour-wrapper align-item-center">
			
			<div class="home-tour-item related-tour-img">
            <?php
            $image_id = get_field('hero_image', $p_id);
            if( !$image_id ) $image_id = get_field('default_hero_image', 'option');

            $title = get_field('tour_main_title', $p_id);
            $day_title = __('Day', 'xwander').' '.get_field('tour_day_number',$p_id);

            $img1x = wp_get_attachment_image_url( $image_id, 'listing');
            $img2x = wp_get_attachment_image_url( $image_id, 'listing-2x');
            
            	
            ?>
                <div aria-hidden="true" style="width:100%;padding-bottom:75%"></div>
                
				<img src="<?php echo $img1x; ?>"
				     srcset="<?php echo $img1x; ?> 1x, <?php echo $img2x; ?> 2x"
				     
				     alt="<?php echo $title; ?>"
				     />
               
			</div>

			<div class="home-tour-item related-tour-content">

                <h2 class="heading-h2 sub_heading_<?php echo $i;?>">
                    <?php echo $day_title; ?>
				</h2>
                <h3 class="heading-h3 m-0"><?php the_field('tour_main_title',$p_id); ?></h3>
            	<div class="mt-2 tour_main_description">
            		<?php the_field('tour_main_description',$p_id); ?>
				</div>
				<div class="mt-1">
					<?php  $tour_link = get_the_permalink($p_id);	?>
	                
	                <a class="btn btn-primary mr-default" href="<?php echo $tour_link; ?>"><?php _e('Read more', 'xwander'); ?></a>
					  
					<a class="btn btn-outline" href="#booking"><?php _e('Book now', 'xwander'); ?></a>
				</div>

			</div>

        </div>
    </div>
    <?php
     $i++;
 endwhile; ?>


</div>
</div>

<?php endif; ?>