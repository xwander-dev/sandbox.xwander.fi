

<?php 
global $post;

$parent_id = wp_get_post_parent_id($post->ID);
$parent_slug = get_post_field( 'post_name',$parent_id );


if($parent_id != 0)
{
	$parent_title =  get_the_title( $parent_id );
	$main_tour_page_id = $parent_id;
} 
else
{
	$parent_title =  get_the_title($post->ID);
	$main_tour_page_id = $post->ID;
}


if($parent_id != 0):

  $args = array(
    'post_type'      => 'tour',
    'posts_per_page' => -1,
    'post_parent'    => $parent_id,
    'order'          => 'ASC',
    'orderby'        => 'menu_order'
 );


$parent = new WP_Query( $args );
	

if ( $parent->have_posts() ) : 

$shown_title = __('Day', 'xwander').' '.get_field('tour_day_number',$p_id);  

	?>
  <select name="sources" id="sources" class="custom-select sources" placeholder="<?php echo $shown_title; ?>">
  	<?php while ( $parent->have_posts() ) : $parent->the_post(); 
$p_id = get_the_ID();
?>

<?php
  
            
                
    $day_title = __('Day', 'xwander').' '.get_field('tour_day_number',$p_id);
 

 	$tour_link = get_the_permalink($p_id);
?>
	<option value="<?php echo $tour_link; ?>"><?php echo $day_title; ?></option>

  <?php endwhile; ?>
</select>
 <?php endif; 

  ?>

<script type="text/javascript">

   jQuery(".custom-select").each(function() {
	  var classes = jQuery(this).attr("class"),
	      id      = jQuery(this).attr("id"),
	      name    = jQuery(this).attr("name");
	  var slug = '<?php echo get_post_field( 'post_name', $parent_id ); ?>';
	  var template =  '<div class="' + classes + '">';
	      template += '<span class="custom-select-trigger"> <?php echo $shown_title; ?> </span>';
	      template += '<div class="custom-options">';
	      template += '<div class="custom-options-title"><?php echo $parent_title;?></div>';
	      jQuery(this).find("option").each(function() {
	      	var data_value = jQuery(this).attr("value");
	        template += '<a href="'+data_value+'"  class="custom-option ' + jQuery(this).attr("class") + '" data-value="' + jQuery(this).attr("value") + '">' + jQuery(this).html() + '</a>';
	      });
	  template += '</div></div>';
	  
	  jQuery(this).wrap('<div class="custom-select-wrapper"></div>');
	  jQuery(this).hide();
	  jQuery(this).after(template);
	});
	jQuery(".custom-option:first-of-type").hover(function() {
	  jQuery(this).parents(".custom-options").addClass("option-hover");
	}, function() {
	  jQuery(this).parents(".custom-options").removeClass("option-hover");
	});
	jQuery(".custom-select-trigger").on("click", function() {
	  jQuery('html').one('click',function() {
	    jQuery(".custom-select").removeClass("opened");
	  });
	  jQuery(this).parents(".custom-select").toggleClass("opened");
	  event.stopPropagation();
	});
	jQuery(".custom-option").on("click", function() {
	  jQuery(this).parents(".custom-select-wrapper").find("select").val(jQuery(this).data("value"));
	  jQuery(this).parents(".custom-options").find(".custom-option").removeClass("selection");
	  jQuery(this).addClass("selection");
	  jQuery(this).parents(".custom-select").removeClass("opened");
	  jQuery(this).parents(".custom-select").find(".custom-select-trigger").text(jQuery(this).text());
	});

	
</script>

<?php 
endif; // end if parent ?>