<?php 


$booking_calendar_id = get_field( 'fareharbor_id');

$calendar_style = get_field('calendar_style');


if ($calendar_style == 'full')
{ 
?>	
<script src="https://fareharbor.com/embeds/script/items/xwandernordic/?full-items=yes&fallback=simple&flow=646900"></script>
<?php 
} 
else if (!empty($booking_calendar_id))
{

    echo '<div id="booking"  class="booking-enquiry-sec">';
     echo '<script src="https://fareharbor.com/embeds/script/calendar/xwandernordic/items/'.$booking_calendar_id.'/?fallback=simple&full-items=yes&flow=646900"></script>';
    echo '</div>';

}




if ( defined( 'ICL_LANGUAGE_CODE' ) ) {


	switch (ICL_LANGUAGE_CODE) {
		case 'en':
			# code...
			break;
		case 'fi':
			# code...
			break;
		case 'de':
			# code...
			break;
		case 'es':
			# code...
			break;
		
		default:
			# code...
			break;
	}
    
}

