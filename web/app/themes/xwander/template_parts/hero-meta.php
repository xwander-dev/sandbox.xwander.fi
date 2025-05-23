<?php
global $post;

$bokun_manager = Bokun_Data_Manager::get_instance();
$bokun_duration = $bokun_manager->get_duration();

if (!empty($bokun_duration)) {
    preg_match('/(\d+)\s*(?:days?|jours?|días?|Tage?|päivää?)/', $bokun_duration, $matches);

    if (!empty($matches[1])) {
        $days = (int)$matches[1];
        $nights = $days - 1;

        $lang = strtoupper(ICL_LANGUAGE_CODE);
        switch ($lang) {
            case 'FI':
                $duration = $days . ' päivää / ' . $nights . ' yötä';
                break;
            case 'ES':
                $duration = $days . ' días / ' . $nights . ' noches';
                break;
            case 'DE':
                $duration = $days . ' Tage / ' . $nights . ' Nächte';
                break;
            case 'FR':
                $duration = $days . ' jours / ' . $nights . ' nuits';
                break;
            default:
                $duration = $days . ' days / ' . $nights . ' night' . ($nights > 1 ? 's' : '');
                break;
        }
    } else {
        $duration = $bokun_duration;
    }
} else {
    $duration_suffix = get_field('tour_duration_suffix', $post->ID);
    $duration = get_field('tour_duration', $post->ID) . ' ' . __($duration_suffix, 'xwander');
}

$days_nights = $duration;

$level = get_field('tour_level', $post->ID);
$level_class = $level;
$level = __($level, 'xwander');

$months = get_field('months');

$level_values_fi = array();
?>

<div class="single-tour-meta single-tour-meta-header">
    <?php if ($days_nights): ?>
        <div class="tour-days-nights">
			<svg width="28px" height="28px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path fill-rule="evenodd" clip-rule="evenodd" d="M4 12C4 7.58172 7.58172 4 12 4C12.5523 4 13 3.55228 13 3C13 2.44772 12.5523 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22C14.7611 22 17.2625 20.8796 19.0711 19.0711C19.4616 18.6805 19.4616 18.0474 19.0711 17.6569C18.6805 17.2663 18.0474 17.2663 17.6569 17.6569C16.208 19.1057 14.2094 20 12 20C7.58172 20 4 16.4183 4 12ZM13 6C13 5.44772 12.5523 5 12 5C11.4477 5 11 5.44772 11 6V12C11 12.2652 11.1054 12.5196 11.2929 12.7071L14.2929 15.7071C14.6834 16.0976 15.3166 16.0976 15.7071 15.7071C16.0976 15.3166 16.0976 14.6834 15.7071 14.2929L13 11.5858V6ZM21.7483 15.1674C21.535 15.824 20.8298 16.1833 20.1732 15.97C19.5167 15.7566 19.1574 15.0514 19.3707 14.3949C19.584 13.7383 20.2892 13.379 20.9458 13.5923C21.6023 13.8057 21.9617 14.5108 21.7483 15.1674ZM21.0847 11.8267C21.7666 11.7187 22.2318 11.0784 22.1238 10.3966C22.0158 9.71471 21.3755 9.2495 20.6937 9.3575C20.0118 9.46549 19.5466 10.1058 19.6546 10.7877C19.7626 11.4695 20.4029 11.9347 21.0847 11.8267ZM20.2924 5.97522C20.6982 6.53373 20.5744 7.31544 20.0159 7.72122C19.4574 8.127 18.6757 8.00319 18.2699 7.44468C17.8641 6.88617 17.9879 6.10446 18.5464 5.69868C19.1049 5.2929 19.8867 5.41671 20.2924 5.97522ZM17.1997 4.54844C17.5131 3.93333 17.2685 3.18061 16.6534 2.86719C16.0383 2.55378 15.2856 2.79835 14.9722 3.41346C14.6588 4.02858 14.9033 4.78129 15.5185 5.09471C16.1336 5.40812 16.8863 5.16355 17.1997 4.54844Z" fill="#FFFFFF"/>
			</svg>
            <?php echo $days_nights; ?>
        </div>
    <?php endif; ?>

    <?php if ($months): ?>
        <div class="tour-months">
			<svg width="28px" height="28px" viewBox="0 0 512 512" id="icons" xmlns="http://www.w3.org/2000/svg"><rect x="48" y="80" width="416" height="384" rx="48" fill="none" stroke="#FFFFFF" stroke-linejoin="round" stroke-width="32"/><line x1="128" y1="48" x2="128" y2="80" fill="none" stroke="#FFFFFF" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><line x1="384" y1="48" x2="384" y2="80" fill="none" stroke="#FFFFFF" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><line x1="464" y1="160" x2="48" y2="160" fill="none" stroke="#FFFFFF" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><polyline points="304 260 347.42 228 352 228 352 396" fill="none" stroke="#FFFFFF" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><path d="M191.87,306.63c9.11,0,25.79-4.28,36.72-15.47a37.9,37.9,0,0,0,11.13-27.26c0-26.12-22.59-39.9-47.89-39.9-21.4,0-33.52,11.61-37.85,18.93" fill="none" stroke="#FFFFFF" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><path d="M149,374.16c4.88,8.27,19.71,25.84,43.88,25.84,28.59,0,52.12-15.94,52.12-43.82,0-12.62-3.66-24-11.58-32.07-12.36-12.64-31.25-17.48-41.55-17.48" fill="none" stroke="#FFFFFF" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/></svg>
            <?php echo $months; ?>
        </div>
    <?php endif; ?>
</div>    
                 

