<?php 




$post = get_page_by_path('404-page-not-found');

$post_id = apply_filters( 'wpml_object_id', $post->ID, 'page' );

if( get_field('hero_image',$post_id) ) 
{
	$image_id = get_field('hero_image' );
} 
else 
{
	$image_id = get_field('default_hero_image', 'option');
}

if( get_field('hero_image_mobile',$post_id) ) 
{
	$mobile_id = get_field('hero_image_mobile',$post_id);
}
else
{
	$mobile_id = $image_id;
}


$hero_title = (get_field( "hero_title",$post_id )) ? get_field( "hero_title",$post_id ) : get_the_title( $post_id );
$hero_excerpt = (get_field( "hero_description",$post_id )) ?  get_field( "hero_description",$post_id ) : null;


?>


<div class="hero-section ">

<div class=" wm-image-container hero-image-container" >
	
		<div class="hero-image-wrapper">
		
		<div aria-hidden="true" style="width:100%;padding-bottom:66.66666666666667%"></div>
	    <picture >

			<source 
				media="(max-width: 420px)"
				srcset="<?php echo wp_get_attachment_image_url( $mobile_id, 'hero-mobile') ?> 1x, <?php echo wp_get_attachment_image_url( $mobile_id, 'hero-mobile-2x') ?> 2x">

			<source 
				media="(max-width: 1024px)"
				srcset="<?php echo wp_get_attachment_image_url( $image_id, 'large') ?> 1x">	
	  
			<source 
				media="(max-width: 1440px)" 
				srcset="<?php echo wp_get_attachment_image_url( $image_id, 'hero') ?> 1x, <?php echo wp_get_attachment_image_url( $image_id, 'hero-2x') ?> 2x">
			
			<img 
				src="<?php echo wp_get_attachment_image_url( $image_id, 'full') ?>"
		        alt="<?php get_post_meta( $image_id, '_wp_attachment_image_alt', true) ?>"
		        class="wm-image-cover hero-image-cover">

		</picture>
	</div>
	

	</div>
	<div class="hero-top-overlay"></div>

	<div class="hero-content-container">
		<div class="hero-content">
	 		<div class="home-banner-text">
				
             <h1 class="has-text-align-center has-text-color">
             	<?php if($hero_title)  echo $hero_title; ?>
             </h1>

             <?php if($hero_excerpt): ?>
                 <h2 class="home-banner-subhead has-text-align-center has-text-color"><?php echo $hero_excerpt; ?></h2>
             <?php endif; ?>		            
            
       
	      </div>

		</div>
	</div>


    <div class="home-banner-arrow"><img src="https://xwanderfidev.wpengine.com/wp-content/uploads/2021/11/vector3.png"></div>
</div>
                
        
