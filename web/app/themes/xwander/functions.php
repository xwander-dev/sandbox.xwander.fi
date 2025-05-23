<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CHILD_THEME_VERSION', '2.21' );

// Custom ACF code and registered Blocks
include('functions/wm_acf.php'); 

// Register custom post types
include('functions/wm_custom_post_types.php'); 

// Sitewide helper functions
include('functions/wm_helpers.php');

// Bokun data management
require_once get_stylesheet_directory() . '/class-bokun-data-manager.php';



// Set textdomain for translations
load_theme_textdomain( 'xwander', get_stylesheet_directory_uri() . '/languages' );

function wm_child_load_css() 
{

	/**
	 * IMPORTANT:  this removes style sheet from parent theme to avoid conflicts. Original coder had made changes directly there. 
	 */
    wp_dequeue_style( 'neve-style' );
	wp_deregister_style( 'neve-style' );

    wp_enqueue_style( 'neve-child-style', trailingslashit( get_stylesheet_directory_uri() ) . 'style.min.css', array(), null );
    wp_register_style( 'neve-style', trailingslashit( get_stylesheet_directory_uri() ) . 'style-main-new.min.css', array(), null );

    wp_enqueue_style( 'neve-style' );

	// Ensure jQuery is loaded
	wp_enqueue_script('jquery');
	
	wp_enqueue_script( 'xwander-js', get_stylesheet_directory_uri() . '/assets/js/scripts.js', array('jquery'), null, true );

	wp_enqueue_style( 'lightbox-style',  get_stylesheet_directory_uri()  . '/assets/js/lightbox/css/lightbox.min.css' );

	wp_enqueue_script( 'lightbox-js', get_stylesheet_directory_uri() . '/assets/js/lightbox/js/lightbox.min.js', array('jquery'), null, true );
	
}

// Child theme styles and scripts
add_action( 'wp_enqueue_scripts', 'wm_child_load_css', 20 );


/*
? pondering
3840 × 2160   4k
3072 x 1920   16inch mac pro
2880 × 1800  common retina laptop
2560 x 1600 
1920 x 1080 fullhd non retina
*/


// Add custom image sizes
add_image_size( 'hero-mobile-2x', 840, 1400, true );
add_image_size( 'hero-mobile', 420, 700, true );
add_image_size( 'hero-2x', 2880, 1800, false );
add_image_size( 'hero', 1440, 900, false );
add_image_size( 'listing-2x', 1200, 900, true);
add_image_size( 'listing', 600, 450, true);


// Remove unncecessary image sizes
add_action('intermediate_image_sizes', 'wm_disable_image_sizes', 10, 1);

function wm_disable_image_sizes($sizes) 
{

  	$targets = ['medium_large', 'thumbnail',  '1536x1536', '2048x2048', 'neve-blog'];

	foreach($sizes as $size_index=>$size) 
	{
		if(in_array($size, $targets)) 
		{
	  		unset($sizes[$size_index]);
		}
	}
  
	return $sizes;
}

add_action('wp_enqueue_scripts', 'hubspot_tracking_script');

function hubspot_tracking_script() {
    wp_enqueue_script(
        'hs-script-loader',
        '//js.hs-scripts.com/8208470.js',
        array(),
        null,
        true
    );
    add_filter('script_loader_tag', 'customize_hubspot_script', 10, 3);
}

function customize_hubspot_script($tag, $handle, $src) {
    if ($handle === 'hs-script-loader') {
        $tag = str_replace('<script ', '<script type="text/javascript" id="hs-script-loader" async defer ', $tag);
        $tag = str_replace('"async"', 'async', $tag);
        $tag = str_replace('"defer"', 'defer', $tag);
    }
    return $tag;
}

add_action('wp_enqueue_scripts', 'browser_detect_script');

function browser_detect_script() {
    wp_enqueue_script( 'browser-detect', get_stylesheet_directory_uri() . '/assets/js/browser-detect.js','jquery', null );
}

// Blog scripts
function get_total_posts_count($tag = 'all') {
    global $sitepress;

    $args = [
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'suppress_filters' => false
    ];

    if ($tag !== 'all') {
        $args['tag'] = $tag;
    }

    if (isset($sitepress)) {
        $args['lang'] = $sitepress->get_current_language();
    }

    $post_query = new WP_Query($args);
    return $post_query->found_posts;
}

function load_posts_script() {
    wp_enqueue_script('load-more', get_stylesheet_directory_uri() . '/assets/js/load-more.js', array('jquery'), '1.0', true);
    wp_localize_script('load-more', 'load_more_params', array(
        'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php',
        'security' => wp_create_nonce('load_more_posts'),
    ));
}

add_action('wp_enqueue_scripts', 'load_posts_script');
add_action('wp_ajax_nopriv_load_posts_by_ajax', 'load_posts_by_ajax_callback');
add_action('wp_ajax_load_posts_by_ajax', 'load_posts_by_ajax_callback');

function load_posts_by_ajax_callback() {
    check_ajax_referer('load_more_posts', 'security');

    $paged = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $tag = $_POST['tag'];
    $posts_per_page = get_option('posts_per_page');

    $args = [
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => $posts_per_page,
        'paged' => $paged,
        'tag' => $tag == 'all' ? '' : $tag,
        'suppress_filters' => false
    ];

    $post_query = new WP_Query($args);

    ob_start();

    if ($post_query->have_posts()) :
        while ($post_query->have_posts()) : $post_query->the_post();
            get_template_part('template-parts/content', get_post_type());
        endwhile;
    endif;

    $html_content = ob_get_clean();

    $total_posts = get_total_posts_count($tag);
    $posts_displayed = $paged * $posts_per_page;
    $posts_remaining = $total_posts - $posts_displayed;
    $show_load_more = $posts_remaining > 0;

    if (ob_get_length()) {
        ob_clean();
    }

    wp_die(json_encode(array(
        'html' => $html_content,
        'total_posts' => $total_posts,
        'posts_remaining' => $posts_remaining,
        'show_load_more' => $show_load_more
    )));
}

add_action('wp_ajax_nopriv_check_posts_remaining', 'check_posts_remaining_callback');
add_action('wp_ajax_check_posts_remaining', 'check_posts_remaining_callback');

function check_posts_remaining_callback() {
    check_ajax_referer('load_more_posts', 'security');

    $tag = isset($_POST['tag']) ? $_POST['tag'] : 'all';
    $posts_per_page = get_option('posts_per_page');
    $paged = isset($_POST['page']) ? intval($_POST['page']) : 1;

    $total_posts = get_total_posts_count($tag);

    $posts_displayed = ($paged - 1) * $posts_per_page;
    $posts_remaining = $total_posts > $posts_displayed ? $total_posts - $posts_displayed : 0;

    wp_die(json_encode(array(
        'posts_remaining' => $posts_remaining,
    )));
}

function enqueue_custom_fonts() {
    $font_path_font1 = get_stylesheet_directory_uri() . '/assets/fonts/signifier-medium.woff2';
    $font_path_font2 = get_stylesheet_directory_uri() . '/assets/fonts/soehne-kraftig.woff2';
    $font_path_font3 = get_stylesheet_directory_uri() . '/assets/fonts/soehne-buch.woff2';
    ?>
    <style type="text/css">
        @font-face {
            font-family: 'Signifier Medium';
            src: url(<?php echo esc_url($font_path_font1); ?>) format('woff2');
        }
        @font-face {
            font-family: 'Söehne Kraftig';
            src: url(<?php echo esc_url($font_path_font2); ?>) format('woff2');
        }
        @font-face {
            font-family: 'Söehne Buch';
            src: url(<?php echo esc_url($font_path_font3); ?>) format('woff2');
        }
    </style>
    <?php
}
add_action('wp_head', 'enqueue_custom_fonts');
add_action('customize_controls_print_styles', 'enqueue_custom_fonts');

function add_custom_fonts_to_customizer($localized_data) {
    $localized_data['fonts']['Custom'][] = 'Signifier Medium';
    $localized_data['fonts']['Custom'][] = 'Söehne Kraftig';
    $localized_data['fonts']['Custom'][] = 'Söehne Buch';
    return $localized_data;
}
add_filter('neve_react_controls_localization', 'add_custom_fonts_to_customizer');

// Make sure WP gets our custom mobile image size in srcset
/*
add_filter('wp_calculate_image_srcset','wm_img_sources',10,5);

function wm_img_sources( $sources, $size_array, $image_src, $image_meta, $attachment_id )
{
	
	$explode = explode( '.', $image_src );
	$image_type = end( $explode );
	
	
	if( $image_type === "svg" )
	{
		return "";
	}

	
	if (!$attachment_id) return $sources;

	
 
	$upload_dir = wp_upload_dir();
 
	$img_url = $upload_dir['baseurl'] . '/' . str_replace( basename( $image_meta['file'] ), $image_meta['sizes'][$image_size_name]['file'], $image_meta['file'] );
 
	$sources[ $breakpoint ] = array(
		'url'        => $img_url,
		'descriptor' => 'w',
		'value'      => $breakpoint,
	);
	
	return $sources;
}


// Remove responsive sizes from svg images
add_filter( 'wp_calculate_image_sizes', 'remove_svgt_responsive_image_attr', 10, 3 );

function remove_svgt_responsive_image_attr( string $sizes, array $size, $image_src = null )  
{	
	$explode = explode( '.', $image_src );
	$image_type = end( $explode );
	
	if( $image_type === "svg" )
	{
		$sizes = "";
	}

	return $sizes;
}
 
*/

// Add support of site styles in gutenberg
add_action('after_setup_theme', 'generate_child_setup');

function modify_language_switcher_titles($languages) {

    foreach ($languages as $lang_code => &$language) {
        if ($language['default_locale'] === 'fr_FR') {
            $language['translated_name'] = 'French';
        }
        if ($language['default_locale'] === 'en_US') {
            $language['translated_name'] = 'English';
        }
        if ($language['default_locale'] === 'es_ES') {
            $language['translated_name'] = 'Spanish';
        }
        if ($language['default_locale'] === 'fi') {
            $language['translated_name'] = 'Finnish';
        }
    }

    return $languages;
}

add_filter('icl_ls_languages', 'modify_language_switcher_titles', 20);

function generate_child_setup() 
{
    add_theme_support('editor-styles');
    
}


add_action( 'enqueue_block_editor_assets', function() 
{
    wp_enqueue_style( 'xwander-child', get_stylesheet_directory_uri() . "/style.css", false, '1.0', 'all' );

   // wp_enqueue_style( 'xwander-neve-style', trailingslashit( get_stylesheet_directory_uri() ) . 'style-main-new.min.css' );
} );




// Move Yoast to bottom of edit page
add_filter( 'wpseo_metabox_prio', 'wm_yoasttobottom');

function wm_yoasttobottom() 
{
	return 'low';
}

function mobile_navigation_menu() {
    ?>
    <script>
        jQuery(document).ready(function($) {
            var resizeTimeout;
            var scriptInitialized = false;

            function isTouchDevice() {
                return 'ontouchstart' in window || navigator.maxTouchPoints;
            }

            function isMobileView() {
                return $(window).width() < 960;
            }

            function initializeScript() {
                if (!scriptInitialized) {
                    $('ul.menu-mobile > li.menu-item').each(function() {
                        var $menuItem = $(this);

                        if (!$menuItem.hasClass('menu-item-type-custom')) {
                            $menuItem.children('.wrap').find('> a').on('click', function(e) {
                                e.preventDefault();
                            });
                        }

                        $menuItem.on('click', function() {
                            $menuItem.toggleClass('mb-open');
                            $menuItem.find('.sub-menu, .wrap button').toggleClass('dropdown-open');
                        });
                    });
                    scriptInitialized = true;
                }
            }

            if (isTouchDevice() || isMobileView()) {
                initializeScript();
            }

            $(window).on('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(function() {
                    if (isTouchDevice() || isMobileView()) {
                        initializeScript();
                    }
                }, 50);
            });
        });
    </script>
    <?php
}
add_action('wp_head', 'mobile_navigation_menu');
