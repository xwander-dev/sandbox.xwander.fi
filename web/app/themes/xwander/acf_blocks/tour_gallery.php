<?php 

global $post;

$booking_cta =  get_field('bokun_booking_widget', $post->ID );


$is_gutenberg = wm_is_gutenberg_page(); 

$hide_cta = get_field('hide_cta_button');

if (!$hide_cta && ($booking_cta || $is_gutenberg) ):
	if ( $is_gutenberg ) $button = '<button class="bokunButton"> Booking and details </button>';
	else $button =  wm_strip_tags_content($booking_cta, '<button>');

	$button_text = get_field('cta_button_text', 'option'); 
	if ( function_exists('wm_replace_button_text') ) $button = wm_replace_button_text($button, $button_text); 

?>
<section class="content-booking-cta">
	<div class="content-booking-cta-content">
        	<?php echo  $button; ?>
        </div>
</section>

<?php 
endif;
 ?>

<?php 
$images = get_field('tour_gallery_images');

if( $images ): 
	$count = sizeof($images);
	$size = 'large'; 
	?>
	<figure class="wp-container-2 wp-block-gallery-1 wp-block-gallery has-nested-images columns-default is-cropped">
		<?php foreach( $images as $image_id ): ?>
            <figure class="wp-block-image size-large" >
            	<a href="<?php echo wp_get_attachment_image_src( $image_id, 'full')[0]; ?>" data-lightbox="tour-images">
            	<?php echo wp_get_attachment_image( $image_id, $size ); ?>
            	</a>
            </figure>  
                
            
        <?php endforeach; ?>

	</figure>
    
      
    
<?php 
endif; 
