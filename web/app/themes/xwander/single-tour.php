<?php
/**
 * Original author:          Rajeev <rajeev.kushwaha@miritech.com>
 * Created on:      28/08/2018
 * Edited by nomadi: march 2022
 *
 * @package Neve
 */

/*
Template Name: Single Tour
*/

// Custom code for tour listing page and tour inner page to display tours

$container_class = apply_filters( 'neve_container_class_filter', 'container', 'single-post' );


$bokun_manager = null;
$details = null;

$experience_id = get_post_meta(get_the_ID(), 'bokun_experience_id', true);

if ($experience_id) {
    $language = strtoupper(ICL_LANGUAGE_CODE);
    $bokun_manager = Bokun_Data_Manager::get_instance();
    $details = $bokun_manager->init($experience_id, $language);
}

$price_data = get_field('price_before_form');
$prices = is_array($price_data) ? $price_data : [['price' => $price_data]];
$prices = array_filter($prices, function($price) {
    return !empty($price['price']);
});
$wp_price = !empty($prices) ? $prices[0]['price'] : 0;

get_header();
remove_filter('acf_the_content', 'wpautop');
?>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css" integrity="sha512-17EgCFERpgZKcm0j0fEq1YCJuyAWdz9KUtv1EjVuaOz8pDnh/0nZxmU6BBXwaaxqoi9PQXnRWqlcDB027hgv9A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <div class="tour-single-section">
        <?php
        $parent_id = wp_get_post_parent_id($post->ID);
        $parent_slug = get_post_field( 'post_name',$parent_id );
        ?>
        <article id="post-<?php echo esc_attr( get_the_ID() ); ?>"
                 class="<?php echo $parent_slug;?> <?php echo esc_attr( join( ' ', get_post_class( 'nv-single-post-wrap col' ) ) ); ?>">
            <div class="tour-anchor-links-pad"></div>
            <div class="tour-anchor-links">
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <ul>
                                <li class="active-default">
                                    <a href="#tour-detail"><?php echo __('Detail','xwander'); ?></a>
                                </li>
                                <?php
                                $what_to_expect = get_field('what_to_expect');
                                if($what_to_expect){
                                    ?>
                                    <li>
                                        <a href="#tour-what-to-expect"><?php echo __('What to Expect','xwander'); ?></a>
                                    </li>
                                    <?php
                                }
                                $accommodation_enabled = get_field('accommodation_enabled', $post->ID);
                                if($accommodation_enabled){
                                    ?>
                                    <li>
                                        <a href="#tour-accommodation"><?php echo __('Accommodation','xwander'); ?></a>
                                    </li>
                                    <?php
                                }

                                $itinerary = get_field('itinerary');
                                if($itinerary && is_array($itinerary)){
                                    ?>
                                    <li>
                                        <a href="#tour-itinerary"><?php echo __('Itinerary','xwander'); ?></a>
                                    </li>
                                    <?php
                                }
                                $map = get_field('map');
                                if($map){
                                    ?>
                                    <li>
                                        <a href="#tour-map"><?php echo __('Map','xwander'); ?></a>
                                    </li>
                                    <?php
                                } ?>
                                <li>
                                    <a href="#tour-faq"><?php echo __('FAQ','xwander'); ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col large-8">
                        <section id="tour-detail">
                            <?php

                            if ($experience_id && $details) {
                                global $post;
                                $has_content = !empty($post->post_content);

                                if ($has_content) {
                                    display_post_content();
                                } else if (!empty($details['description'])) {
                                    echo wp_kses_post($details['description']);
                                }

                                $price = $details['price'] ?? $wp_price ?? 0;

                                $offer_schema = [
                                    "@context" => "https://schema.org",
                                    "@type" => "Offer",
                                    "priceCurrency" => $details['currency'],
                                    "price" => $price,
                                    "availability" => "https://schema.org/InStock",
                                    "url" => get_permalink(),
                                    "validFrom" => $details['lastPublished'],
                                    "eligibleQuantity" => [
                                        "@type" => "QuantitativeValue",
                                        "minValue" => $details['minPerBooking'],
                                        "maxValue" => $details['maxPerBooking'],
                                    ],
                                    "seller" => [
                                        "@type" => "Organization",
                                        "name" => "Xwander Nordic",
                                        "url" => "https://xwander.fi"
                                    ]
                                ];

                                echo '<script type="application/ld+json">' . json_encode($offer_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
                            } else {
                                display_post_content();
                            }

                            function display_post_content() {
                                if (have_posts()) {
                                    while (have_posts()) {
                                        the_post();
                                        the_content();
                                    }
                                }
                            }
                            ?>
                        </section>
                        <div class="mob-tour-info-hide">
                            <?php
                            $what_to_expect = get_field('what_to_expect');
                            if($what_to_expect){
                                ?>
                                <section id="tour-what-to-expect">
                                    <h3><?php echo __('What to Expect','xwander'); ?></h3>
                                    <div class="tour-section-content">
                                        <?php
                                        echo $what_to_expect;
                                        ?>
                                    </div>
                                </section>
                                <?php
                            }

                            $gallery_images = get_field('gallery_images');
                            if($gallery_images) {
                                $image_count = count($gallery_images);
                                ?>
                                <section id="tour-gallery">
                                    <h3><?php echo __('Gallery','xwander'); ?></h3>
                                    <div class="tour-section-content">
                                        <div class="wp-block-gallery">
                                            <div class="gallery-grid" data-images="<?php echo esc_attr($image_count); ?>">
                                                <?php foreach($gallery_images as $image) {
                                                    $img = $image['gallery_image'];
                                                    if($img) { ?>
                                                        <figure class="gallery-item">
                                                            <a href="<?php echo esc_url($img['url']); ?>" data-lightbox="tour-gallery">
                                                                <img src="<?php echo esc_url($img['sizes']['large']); ?>"
                                                                     alt="<?php echo esc_attr($img['alt']); ?>"
                                                                     class="wp-image">
                                                            </a>
                                                        </figure>
                                                    <?php }
                                                } ?>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            <?php }

                            $bokun_itinerary = $bokun_manager ? $bokun_manager->get_agenda_items() : null;
                            $itinerary = get_field('itinerary');

                            if (($bokun_itinerary && is_array($bokun_itinerary)) || ($itinerary && is_array($itinerary))) { ?>
                                <section id="tour-itinerary">
                                    <div class="itinerary-flex">
                                        <h3><?php echo __('Itinerary','xwander'); ?></h3>
                                    </div>
                                    <div class="tour-section-content">
                                        <?php
                                        $counter = 0;
                                        if ($itinerary && is_array($itinerary) && !empty($itinerary)) {
                                            foreach($itinerary as $data) {
                                                ?>
                                                <div class="acc-tour-line">
                                                    <h4 class="accordion-tour <?php if($counter == 0){ echo 'active'; } ?>"><?php echo $data['title']; ?></h4>
                                                    <div class="acc-panel">
                                                        <?php echo $data['description']; ?>
                                                        <?php
                                                        if(!empty($data['itinerary_accommodation']) && is_array($data['itinerary_accommodation'])) {
                                                            ?>
                                                            <div class="tour-accommodation">
                                                                <div class="tour-accommodation-list">
                                                                    <?php
                                                                    foreach($data['itinerary_accommodation'] as $data_img) {
                                                                        ?>
                                                                        <a href="<?php echo $data_img['image']['url']; ?>" data-lightbox="roadtrip-<?php echo $counter; ?>">
                                                                            <img src="<?php echo $data_img['image']['sizes']['medium']; ?>" alt="Xwander">
                                                                        </a>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <?php
                                                ++$counter;
                                            }
                                        } elseif ($bokun_itinerary && is_array($bokun_itinerary)) {
                                            foreach($bokun_itinerary as $data) {
                                                ?>
                                                <div class="acc-tour-line">
                                                    <h4 class="accordion-tour <?php if($counter == 0){ echo 'active'; } ?>">
                                                        <?php echo esc_html($data['title']); ?>
                                                    </h4>
                                                    <div class="acc-panel">
                                                        <?php echo wp_kses_post($bokun_manager->strip_inline_styles($data['body'])); ?>
                                                        <?php if (!empty($data['location']) && !empty($data['location']['latitude']) && !empty($data['location']['longitude'])) { ?>
                                                            <div>
                                                                <a href="#" class="show-map-link"
                                                                   data-lat="<?php echo esc_attr($data['location']['latitude']); ?>"
                                                                   data-lng="<?php echo esc_attr($data['location']['longitude']); ?>"
                                                                   data-title="<?php echo esc_attr($data['title']); ?>">
                                                                    <?php echo __('Show Location', 'xwander'); ?>
                                                                </a>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <?php
                                                ++$counter;
                                            }
                                        }
                                        ?>
                                    </div>
                                </section>
                                <?php
                            }

                            $map = get_field('map');
                            if($map){ ?>
                                <section id="tour-map">
                                    <h3><?php echo __('Map','xwander'); ?></h3>
                                    <div class="tour-section-content">
                                        <?php
                                        echo $map;
                                        ?>
                                    </div>
                                </section>
                                <?php
                            }

                            $faq = get_field('faq');
                            $faq_or_details_data = !empty($faq) || !empty($details);

                            if ($faq_or_details_data) { ?>
                                <section id="tour-faq">
                                    <h3><?php echo __('FAQ','xwander'); ?></h3>
                                    <div class="tour-section-content">
                                        <?php
                                        $faq = get_field('faq');
                                        $counter = 0;

                                        $requirements = $attention = [];

                                        if (!empty($details)) {
                                            if (!empty($details['requirements'])) {
                                                $requirements = $bokun_manager->extract_list_items($details['requirements']);
                                            }

                                            if (!empty($details['attention'])) {
                                                $attention = $bokun_manager->extract_list_items($details['attention']);
                                            }

                                            if (!empty($details['minAge'])) {
                                                $attention[] = __('Minimum age of the passengers is', 'xwander') . " " . esc_html($details['minAge']) . " " . __('years.', 'xwander');
                                            }

                                            $know_before_you_go_map = [
                                                'STROLLER_OR_PRAM_ACCESSIBLE' => __('Stroller or pram accessible.', 'xwander'),
                                                'ANIMALS_OR_PETS_ALLOWED' => __('Animals or pets are allowed.', 'xwander'),
                                                'PUBLIC_TRANSPORTATION_NEARBY' => __('Public transportation is nearby.', 'xwander'),
                                                'INFANT_SEATS_AVAILABLE' => __('Infant seats are available.', 'xwander'),
                                                'WHEELCHAIR_ACCESSIBLE' => __('Wheelchair accessible.', 'xwander'),
                                                'LIMITED_SIGHT_ACCESSIBLE' => __('Accessible for limited sight.', 'xwander'),
                                                'LIMITED_MOBILITY_ACCESSIBLE' => __('Accessible for limited mobility.', 'xwander'),
                                                'INFANTS_MUST_SIT_ON_LAPS' => __('Infants must sit on laps.', 'xwander'),
                                                'PASSPORT_REQUIRED' => __('Passport is required.', 'xwander'),
                                                'DRESS_CODE' => __('Dress code applies.', 'xwander'),
                                            ];

                                            if (!empty($details['knowBeforeYouGoItems']) && is_array($details['knowBeforeYouGoItems'])) {
                                                foreach ($details['knowBeforeYouGoItems'] as $item) {
                                                    if (isset($know_before_you_go_map[$item])) {
                                                        $attention[] = $know_before_you_go_map[$item];
                                                    }
                                                }
                                            }
                                        }

                                        if (is_array($faq)) {
                                            foreach ($faq as $data) {
                                                ?>
                                                <div class="acc-tour-line">
                                                    <h4 class="accordion-tour <?php if($counter == 0){ echo 'active'; } ?>"><?php echo esc_html($data['question']); ?></h4>
                                                    <div class="acc-panel">
                                                        <?php echo wp_kses_post($data['answer']); ?>
                                                    </div>
                                                </div>
                                                <?php
                                                ++$counter;
                                            }
                                        }

                                        if (!empty($requirements)) {
                                            ?>
                                            <div class="acc-tour-line">
                                                <h4 class="accordion-tour <?php if($counter == 0){ echo 'active'; } ?>"><?php echo __('What do I need to bring?', 'xwander'); ?></h4>
                                                <div class="acc-panel">
                                                    <ul>
                                                        <?php
                                                        foreach($requirements as $requirement) {
                                                            echo '<li><span>' . esc_html($requirement) . '</span></li>';
                                                        }
                                                        ?>
                                                    </ul>
                                                </div>
                                            </div>
                                            <?php
                                            ++$counter;
                                        }

                                        if (!empty($details['cancellation'])) {
                                            ?>
                                            <div class="acc-tour-line">
                                                <h4 class="accordion-tour <?php if($counter == 0){ echo 'active'; } ?>"><?php echo __('What is the cancellation policy?', 'xwander'); ?></h4>
                                                <div class="acc-panel">
                                                    <ul>
                                                        <?php
                                                        foreach($details['cancellation'] as $policy) {
                                                            if ($policy['type'] === 'fully_refundable') {
                                                                echo '<li><span>' . __('Bookings are fully refundable up to the time of the event.', 'xwander') . '</span></li>';
                                                            } elseif ($policy['type'] === 'non_refundable') {
                                                                echo '<li><span>' . __('Bookings are not refundable, all sales are final.', 'xwander') . '</span></li>';
                                                            } elseif ($policy['type'] === 'penalty') {
                                                                $condition = '';
                                                                if ($policy['days'] > 0 && $policy['hours'] > 0) {
                                                                    $condition = sprintf(__('%d days and %d hours', 'xwander'), $policy['days'], $policy['hours']);
                                                                } elseif ($policy['days'] > 0) {
                                                                    $condition = sprintf(__('%d days', 'xwander'), $policy['days']);
                                                                } elseif ($policy['hours'] > 0) {
                                                                    $condition = sprintf(__('%d hours', 'xwander'), $policy['hours']);
                                                                }

                                                                if ($condition) {
                                                                    echo '<li><span>' . sprintf(__('Cancellation fee of %d%% is charged if cancelled %s or less before the event.', 'xwander'), $policy['percentage'], $condition) . '</span></li>';
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                    </ul>
                                                </div>
                                            </div>
                                            <?php
                                            ++$counter;
                                        }

                                        if (!empty($attention)) {
                                            ?>
                                            <div class="acc-tour-line">
                                                <h4 class="accordion-tour <?php if($counter == 0){ echo 'active'; } ?>"><?php echo __('Please note', 'xwander'); ?></h4>
                                                <div class="acc-panel">
                                                    <ul>
                                                        <?php
                                                        foreach($attention as $item) {
                                                            echo '<li><span>' . esc_html($item) . '</span></li>';
                                                        }
                                                        ?>
                                                    </ul>
                                                </div>
                                            </div>
                                            <?php
                                            ++$counter;
                                        }
                                        ?>
                                    </div>
                                    <h4><?php printf(__('Find Full FAQ from <a href="%s" target="_blank">here</a>','xwander'), esc_url(home_url('/faq'))); ?></h4>
                                </section>
                                <?php
                            } ?>
                        </div>
                        <div id="map-modal" class="map-modal">
                            <div class="map-modal-content">
                                <span class="close-map">&times;</span>
                                <div id="location-map"></div>
                            </div>
                        </div>

                        <div class="tour-after">
                            <section id="book-with-confidence">
                                <h3><?php echo __('Book With Confidence', 'xwander'); ?></h3>
                                <ul>
                                    <li>
                                        <span><?php echo __('Local family owned business', 'xwander'); ?></span>
                                    </li>
                                    <li>
                                        <span><?php echo __('Personalized service', 'xwander'); ?></span>
                                    </li>
                                    <li>
                                        <span><?php echo __('Flexible itineraries', 'xwander'); ?></span>
                                    </li>
                                    <li>
                                        <span><?php echo __('Small group sizes', 'xwander'); ?></span>
                                    </li>
                                </ul>
                            </section>
                            <?php
                            $sections = [
                                'included' => ['acf_field' => 'price_includes', 'section_id' => 'tour-price-includes', 'ul_class' => 'tour-price-includes__inner__list', 'title' => __('Price Includes', 'xwander')],
                                'excluded' => ['acf_field' => 'price_excludes', 'section_id' => 'tour-price-excludes', 'ul_class' => 'tour-price__inner__list', 'title' => __('Price Excludes', 'xwander')]
                            ];

                            foreach ($sections as $key => $settings) {
                                $items = [];

                                if ($details && isset($details[$key])) {
                                    $items = $bokun_manager->extract_list_items($details[$key]);
                                }

                                if (empty($items)) {
                                    $acf_items = get_field($settings['acf_field']);

                                    if ($acf_items && is_array($acf_items)) {
                                        $items = array_map(function($acf_item) {
                                            return $acf_item['list_item'];
                                        }, $acf_items);
                                    }
                                }

                                if (!empty($items)) {
                                    ?>
                                    <section id="<?php echo esc_attr($settings['section_id']); ?>">
                                        <h3><?php echo esc_html($settings['title']); ?></h3>
                                        <ul class="<?php echo esc_attr($settings['ul_class']); ?>">
                                            <?php
                                            foreach($items as $item) {
                                                echo '<li><span>' . esc_html($item) . '</span></li>';
                                            }
                                            ?>
                                        </ul>
                                    </section>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col large-4">
                        <div class="mob-tour-info-hide">
                            <?php // include('template_parts/sidebar-info.php');?>
                            <?php include('template_parts/tripadvisor-badge.php'); ?>
                            <div class="sidebar-info">
                                <?php
                                $duration_suffix = get_field('tour_duration_suffix', $post->ID );
                                $duration = get_field('tour_duration', $post->ID ).' '. __($duration_suffix, 'xwander');
                                $activity_level = get_field('tour_level', $post->ID );
                                $months = get_field('months'); ?>
                                <div class="info-row">
                                    <?php
                                    $duration_text = $details['durationText'] ?? null;

                                    if ($duration_text || $duration): ?>
                                        <div class="group duration">
                                            <h5>
                                                <?php echo __('Adventure Duration','xwander'); ?>:
                                            </h5>
                                            <p class="content">
                                                <?php
                                                if ($duration_text) {
                                                    echo esc_html($duration_text);
                                                } else {
                                                    echo esc_html($duration);
                                                }
                                                ?>
                                            </p>
                                        </div>
                                    <?php endif;
                                    if ($months): ?>
                                        <div class="group available">
                                            <h5>
                                                <?= __('Available', 'xwander'); ?>:
                                            </h5>
                                            <p class="content">
                                                <?php echo $months; ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                                $price_data = get_field('price_before_form');

                                if ($price_data) {
                                    $prices = is_array($price_data) ? $price_data : [['price' => $price_data]];
                                    $prices = array_filter($prices, function($price) {
                                        return !empty($price['price']);
                                    });

                                    $bokun_price = $details['price'] ?? null;
                                    $price_to_display = $bokun_price !== null ? $bokun_price : $wp_price;

                                    if ($price_to_display): ?>
                                        <div class="price-box">
                                            <div class="price-info">
                                                <h5><?= __('Price starting at', 'xwander'); ?></h5>
                                                <div class="group">
                                                    <span>Adult: <?= esc_html($price_to_display) . ' €'; ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif;
                                }
                                ?>
                            </div>
                            <div class="booking-form-widget">
                                <?php
                                $booking_cta = get_field('bokun_booking_widget', $post->ID);
                                $calendar_or_html = get_field('calendar_or_html');
                                $custom_html = get_field('sidebar_custom_html');

                                if ($calendar_or_html === false && !empty($custom_html)) {
                                    echo $custom_html;
                                } elseif ($booking_cta) {
                                    $book_image_id = get_field('booking_background_image', 'option');
                                    $book_image_src = wp_get_attachment_image_url($book_image_id, 'full'); ?>
                                    <div class="booking-container" style="background-image:url('<?php echo esc_url($book_image_src); ?>');">
                                        <?php echo $booking_cta; ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
<?php include('acf_blocks/tripadvisor.php');?>
<?php if ($accommodation_enabled == 'enabled') {
    $img1 = get_field('accommodation_image_1', 'option');
    $img2 = get_field('accommodation_image_2', 'option');
    $img3 = get_field('accommodation_image_3', 'option'); ?>
    <div class="accommodation-container" id="tour-accommodation">
        <h2><?php echo __('Accommodation','xwander'); ?></h2>
        <div class="thin-container">
            <p><?php echo __('You will be staying at the Xwander Suites, located in a modern building in the center of Ivalo. All the apartments are fully furnished, and they have free Wifi, heating and air conditioning.', 'xwander'); ?></p>
            <p><?php echo __("Xwander's rental equipment storage is in the same building, and you can use all equipment for free during your stay.", 'xwander'); ?></p>
        </div>
        <div class="images">
            <?php if ($img1) {
                echo wp_get_attachment_image($img1, 'medium');
            }

            if ($img2) {
                echo wp_get_attachment_image($img2, 'medium');
            }

            if ($img3) {
                echo wp_get_attachment_image($img3, 'medium');
            } ?>
        </div>
    </div>
    <?php
} ?>
    </article>
    </div>

    <script>
        var elementPosition = jQuery('.tour-anchor-links').offset();

        // Cache selectors
        var lastId,
            topMenu = jQuery(".tour-anchor-links"),
            topMenuHeight = topMenu.outerHeight()+15,
            // All list items
            menuItems = topMenu.find("a"),
            // Anchors corresponding to menu items
            scrollItems = menuItems.map(function(){
                var item = jQuery(jQuery(this).attr("href"));
                if (item.length) { return item; }
            });

        // Bind click handler to menu items
        // so we can get a fancy scroll animation
        menuItems.click(function(e){
            var href = jQuery(this).attr("href"),
                offsetTop = href === "#" ? 0 : jQuery(href).offset().top-topMenuHeight+1;
            jQuery('html, body').stop().animate({
                scrollTop: offsetTop
            }, 300);
            e.preventDefault();
        });

        // Bind to scroll
        jQuery(window).scroll(function(){
            if(jQuery(window).scrollTop() > elementPosition.top){
                jQuery('.tour-anchor-links').css('position','fixed').css('top','0');
                jQuery('.tour-anchor-links-pad').addClass('active');
            } else {
                jQuery('.tour-anchor-links').css('position','static');
                jQuery('.tour-anchor-links-pad').removeClass('active');
            }
            // Get container scroll position
            var fromTop = jQuery(this).scrollTop()+topMenuHeight;

            // Get id of current scroll item
            var cur = scrollItems.map(function(){
                if (jQuery(this).offset().top < fromTop)
                    return this;
            });
            // Get the id of the current element
            cur = cur[cur.length-1];
            var id = cur && cur.length ? cur[0].id : "";

            if (lastId !== id) {
                lastId = id;
                // Set/remove active class
                menuItems
                    .parent().removeClass("active")
                    .end().filter("[href='#"+id+"']").parent().addClass("active");
            }
        });

        var acc = document.getElementsByClassName("accordion-tour");
        var i;

        for (i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function() {
                this.classList.toggle("active");
            });
        }
        jQuery('.btn-read-more-tour').click(function(){
            jQuery('.mob-tour-info-hide').show();
            jQuery('.btn-read-more-tour').hide();
        });
    </script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script type="text/javascript">
        if (jQuery(window).width() < 960) {
            jQuery('.tour-accommodation-list').slick({
                dots: true,
                infinite: false,
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: false,
            });
        }

    </script>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            //const initialTop = parseInt($('.info-row').css('top'), 10);
            //const moveSpeed = 1;

            var textToCopy = $('.tour-days-nights').text();

            $('#tour-itinerary h3').after('<span>' + textToCopy + '</span>');

            /*$(window).scroll(function() {
                const scrollTop = $(window).scrollTop();
                $('.info-row').css('top', initialTop + scrollTop * moveSpeed + 'px');
            });*/
        });
    </script>
    <script>
        let map = null;
        let marker = null;

        function initGoogleMaps() {
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=<?php echo esc_js(GOOGLE_MAPS_API); ?>&callback=initMapCallback&loading=async`;
            script.defer = true;
            document.head.appendChild(script);
        }

        function initMapCallback() {
            const modal = document.getElementById('map-modal');
            const closeBtn = document.getElementsByClassName('close-map')[0];
            const mapLinks = document.getElementsByClassName('show-map-link');

            function initMap(lat, lng, title) {
                if (!map) {
                    map = new google.maps.Map(document.getElementById('location-map'), {
                        zoom: 12,
                        center: { lat: parseFloat(lat), lng: parseFloat(lng) }
                    });
                }

                const position = { lat: parseFloat(lat), lng: parseFloat(lng) };
                map.setCenter(position);

                if (marker) {
                    marker.setMap(null);
                }

                marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    title: title
                });
            }

            Array.from(mapLinks).forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const lat = this.getAttribute('data-lat');
                    const lng = this.getAttribute('data-lng');
                    const title = this.getAttribute('data-title');
                    modal.classList.add('active');
                    initMap(lat, lng, title);
                });
            });

            closeBtn.onclick = function() {
                modal.classList.remove('active');
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.classList.remove('active');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', initGoogleMaps);
    </script>
<?php
get_footer();
