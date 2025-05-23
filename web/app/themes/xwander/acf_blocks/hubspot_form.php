<?php 
global $post;

$hubspot_contact_form_id = get_field( 'hubspot_id');



//if(!empty($booking_calendar_id)){
 echo '<div id="booking"  class="booking-enquiry-sec">';
echo '<script charset="utf-8" type="text/javascript" src="//js.hsforms.net/forms/shell.js"></script>';
echo '<script>
    hbspt.forms.create({
    region: "na1",
    portalId: "8208470",
    formId: "'.$hubspot_contact_form_id.'"
  });
</script>';
echo '</div>';

 // }
