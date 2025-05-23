jQuery( document ).ready( function( $ ) 
{
	$html = '';
	$button_text = $('.header .builder-item .button').first().text();
	$bokun_button = $('.bokunButton').first().clone();
	$bokun_button.text($button_text);

	if ($bokun_button) {
		$html = $bokun_button;
		$('.header .builder-item .button').show();
	}

	$('.header .builder-item .button').parent().html($html);


});