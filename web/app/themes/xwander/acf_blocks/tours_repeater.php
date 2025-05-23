 <?php if( have_rows('tour_repeater') ): ?>
 	<div class="tours allignfull mt-4 tour-pagination">
			    <div class="tour-section">
			       

			            <?php $i=1; while( have_rows('tour_repeater') ): the_row(); ?>
			                <div class="wp-block-column m-0">
			                    <div class="wp-block-classic related-tour home-tour-wrapper align-item-center">
									<div class="home-tour-item related-tour-img">
			                        <?php
			                        $image = get_sub_field('tour_main_image');

			                        if( $image ) {
			                            //$url = $image['url']; ?>
			                            <div aria-hidden="true" style="width:100%;padding-bottom:75%"></div>
			                           <img src="<?php echo esc_url($image); ?>" alt="" width="100%" height="100%" />
			                        <?php } ?>
									</div>
									<div class="home-tour-item related-tour-content">
			                        <h2 class="heading-h2 sub_heading_<?php echo $i;?>">
			                            <?php the_sub_field('tour_day_title'); ?>
									</h2>
			                        <h3 class="heading-h3 m-0"><?php the_sub_field('tour_main_title'); ?></h3>
			                        <div class="mt-2 tour_main_description"><?php the_sub_field('tour_main_description'); ?>
										
										</div>
									<div class="mt-1">
									<?php 
									  $read_more_text = get_sub_field('tour_main_read_more_text');
									  if(!empty($read_more_text)){


									  	if (get_sub_field('tour_page_link')) $tour_link = get_sub_field('tour_page_link');
									  	else $tour_link = get_sub_field('tour_main_read_more');
									?>
			                        <a class="btn btn-primary mr-default" href="<?php echo $tour_link; ?>"><?php the_sub_field('tour_main_read_more_text'); ?></a>
									  <?php } ?>
									 
									<?php 
									  $book_now_text = get_sub_field('tour_main_book_now_text');
									  if(!empty($book_now_text)){
									?>
									<a class="btn btn-outline" href="#block-10"><?php the_sub_field('tour_main_book_now_text'); ?></a>
									<?php } ?>
									
									</div>
									</div>
			                    </div>
			                </div>
			            <?php $i++; endwhile; ?>


</div>
</div>

			        <?php endif; ?>