<div class="featured-tour-item">
	<div class="wm-image-container featured-tour-image" >
		<img src="<?php echo $img1x; ?>"
		     srcset="<?php echo $img1x; ?> 1x, <?php echo $img2x; ?> 2x"
		     alt="<?php echo $title; ?>"
		     />
	</div>
	<div class="featured-tour-content">
		<div class="tour-content-upper">
            <?php if ($badge): ?><span class="day-label"><?php echo $badge; ?></span><?php endif; ?>
            <h4><?php echo $title; ?></h4>
            <div class="featured-tour-excerpt"><?php  echo $excerpt; ?></div>
		</div>
		<?php if ($price): ?>
		<div class="tour-content-bottom">
            <div class="featured-tour-meta">
                <div class="tour-duration"><?php echo $duration; ?></div>
                <div class="tour-price-from">
                    <span class="price-from-title"><?php _e('from', 'xwander'); ?></span>
                    <span class="price-from-eur"><?php echo $price; ?> &euro;</span>
                    <span class="price-from-unit"><?php _e(' / person', 'xwander'); ?></span>
                </div>
            </div>
		</div>
		<?php endif; ?>
	</div>
	<a href="<?php the_permalink($post->ID); ?>"></a>   
</div>