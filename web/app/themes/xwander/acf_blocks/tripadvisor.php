<?php
$tripadvisor = get_field('tripadvisor', 'option');
$image_id = get_field('tripadvisor_image', 'option');
$image_src = wp_get_attachment_image_url($image_id, 'full');
$image_srcset = wp_get_attachment_image_srcset($image_id, 'full');
$image_sizes = wp_get_attachment_image_sizes($image_id, 'full');
$image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);

if ($tripadvisor): ?>
<div class="tripadvisor-container">
    <img src="<?php echo esc_url($image_src); ?>"
         srcset="<?php echo esc_attr($image_srcset); ?>"
         sizes="<?php echo esc_attr($image_sizes); ?>"
         alt="<?php echo esc_attr($image_alt); ?>">
    <div class="cols">
        <?php foreach ($tripadvisor as $testimonial): ?>
            <div class="col">
                <h3>"<?php echo esc_html($testimonial['testimonial_title']); ?>"</h3>
                <div class="date"><?php echo esc_html($testimonial['testimonial_date']); ?></div>
                <div class="content">
                    <?php echo wp_kses_post($testimonial['testimonial_content']); ?>
                </div>
                <div class="logo"></div>
            </div>
        <?php endforeach; ?>
        <a href="https://www.tripadvisor.com/Attraction_Review-g189918-d16194057-Reviews-Xwander_Nordic-Ivalo_Lapland.html" target="_blank" class="link" rel="noopener">
            <?php echo __('Read more at Tripadvisor!','xwander'); ?>
        </a>
    </div>
</div>
<?php endif; ?>