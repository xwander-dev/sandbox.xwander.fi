<?php

$languages = get_field('guide_languages', $post->ID);
$adventure_type = get_field('adventure_type', $post->ID);
$min_age = get_field('suggested_min_age', $post->ID);
$duration_suffix = get_field('tour_duration_suffix', $post->ID );
$duration = get_field('tour_duration', $post->ID ).' '. __($duration_suffix, 'xwander');
$activity_level = get_field('tour_level', $post->ID );
$foods = get_field('food_and_beverage', $post->ID); ?>

<div class="sidebar-info">
<?php if ($languages) { ?>
    <div class="group lang">
        <h5>
            <?php echo __('Language of the Guides','xwander'); ?>:
        </h5>
        <p class="content">
            <?php if (is_array($languages)) {
                echo implode(', ', $languages);
            } else {
                echo $languages;
            } ?>
        </p>
    </div>
    <?php
}

if ($adventure_type) { ?>
    <div class="group type">
        <h5>
            <?php echo __('Adventure Type','xwander'); ?>:
        </h5>
        <p class="content">
            <?php if (is_array($adventure_type)) {
                echo implode(', ', $adventure_type);
            } else {
                echo $adventure_type;
            } ?>
        </p>
    </div>
    <?php
}

if ($min_age) { ?>
    <div class="group age">
        <h5>
            <?php echo __('Suggested Min. Age','xwander'); ?>:
        </h5>
        <p class="content">
            <?php echo $min_age; ?>
        </p>
    </div>
    <?php
}

if ($duration) { ?>
    <div class="group duration">
        <h5>
            <?php echo __('Adventure Duration','xwander'); ?>:
        </h5>
        <p class="content">
            <?php echo $duration; ?>
        </p>
    </div>
    <?php
}

if ($activity_level) { ?>
    <div class="group level">
        <h5>
            <?php echo __('Activity Level','xwander'); ?>:
        </h5>
        <p class="content">
            <?php echo $activity_level; ?>
        </p>
    </div>
    <?php
}

if ($foods) { ?>
    <div class="group food">
        <h5>
            <?php echo __('Food/Beverage Provided','xwander'); ?>:
        </h5>
        <p class="content">
            <?php  if (is_array($foods)) {
                foreach ($foods as $food) {
                    echo '<span>' . esc_html($food) . '</span>';
                }
            } else {
                echo '<span>' . esc_html($foods) . '</span>';
            } ?>
        </p>
    </div>
    <?php
} ?>
</div>