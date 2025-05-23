<?php 

global $post;

$booking_cta =  get_field('bokun_booking_widget', $post->ID );
$cta_text = get_field('custom_cta_text');

$is_gutenberg = wm_is_gutenberg_page(); 

if ($booking_cta || $is_gutenberg ):
	if ( $is_gutenberg ) $button = '<button class="bokunButton"> Booking and details </button>';
	else $button =  wm_strip_tags_content($booking_cta, '<button>');

	$button_text = ($cta_text) ? $cta_text : get_field('cta_button_text', 'option'); 
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

