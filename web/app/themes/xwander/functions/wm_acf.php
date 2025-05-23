<?php

if( function_exists('acf_add_options_page') ) 
{
    // Create site specific options page
    acf_add_options_page(array(
        'page_title'    => 'Xwander Options',
        'menu_title'    => 'Xwander Options',
        'menu_slug'     => 'xwander-general-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));

    acf_add_options_page(array(
        'page_title'    => 'Xwander Tour Blocks',
        'menu_title'    => 'Tour Blocks',
        'menu_slug'     => 'tour-blocks',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));
}

add_action( 'acf/include_fields', function() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group( array(
        'key' => 'group_65b3c76c5f46f',
        'title' => 'Accommodation Images',
        'fields' => array(
            array(
                'key' => 'field_65b3c80e08fec',
                'label' => 'Image 1',
                'name' => 'accommodation_image_1',
                'aria-label' => '',
                'type' => 'image',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'return_format' => 'id',
                'library' => 'all',
                'min_width' => '',
                'min_height' => '',
                'min_size' => '',
                'max_width' => '',
                'max_height' => '',
                'max_size' => '',
                'mime_types' => '',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_65b3c83708fed',
                'label' => 'Image 2',
                'name' => 'accommodation_image_2',
                'aria-label' => '',
                'type' => 'image',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'return_format' => 'id',
                'library' => 'all',
                'min_width' => '',
                'min_height' => '',
                'min_size' => '',
                'max_width' => '',
                'max_height' => '',
                'max_size' => '',
                'mime_types' => '',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_65b3c83e08fee',
                'label' => 'Image 3',
                'name' => 'accommodation_image_3',
                'aria-label' => '',
                'type' => 'image',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'return_format' => 'id',
                'library' => 'all',
                'min_width' => '',
                'min_height' => '',
                'min_size' => '',
                'max_width' => '',
                'max_height' => '',
                'max_size' => '',
                'mime_types' => '',
                'preview_size' => 'medium',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'tour-blocks',
                ),
            ),
        ),
        'menu_order' => 0
    ) );
} );






// Register custom Blocks 
add_action('acf/init', 'wm_acf_init_block_types');

function wm_acf_init_block_types() 
{

    
$xwander_icon = '<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="clock" class="svg-inline--fa fa-clock fa-w-16 clockIcon" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
    <path fill="#444444" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.5 0-200-89.5-200-200S145.5 56 256 56s200 89.5 200 200-89.5 200-200 200zm61.8-104.4l-84.9-61.7c-3.1-2.3-4.9-5.9-4.9-9.7V116c0-6.6 5.4-12 12-12h32c6.6 0 12 5.4 12 12v141.7l66.8 48.6c5.4 3.9 6.5 11.4 2.6 16.8L334.6 349c-3.9 5.3-11.4 6.5-16.8 2.6z"></path>
</svg>';


    if( function_exists('acf_register_block_type') ) 
    {

        acf_register_block_type(array(
            'name'              => 'xwander_calendar',
            'title'             => __('Xwander Calendar'),
            'description'       => __('xwander calendar block. Just add fareharbor product ID such as 341817. '),
            'render_template'   => 'acf_blocks/fareharbor_calendar.php',
            'category'          => 'xwander-blocks',
            'mode'              => 'edit',
            'icon'              => $xwander_icon,
            'keywords'          => array( 'calendar', 'kalenteri', 'booking' ),
        ));

         acf_register_block_type(array(
            'name'              => 'xwander_hubspot',
            'title'             => __('Xwander hubspot'),
            'description'       => __('xwander hubspot block. Just add hubspot form ID. '),
            'render_template'   => 'acf_blocks/hubspot_form.php',
            'category'          => 'xwander-blocks',
            'mode'              => 'edit',
            'icon'              => $xwander_icon,
            'keywords'          => array( 'hubspot', 'booking' ),
        ));
/*
         acf_register_block_type(array(
            'name'              => 'xwander_tours_repeater',
            'title'             => __('Xwander tours repeater'),
            'description'       => __('xwander tours repeater block. Lists tour days. '),
            'render_template'   => 'acf_blocks/tours_repeater.php',
            'category'          => 'xwander-blocks',
            'mode'              => 'edit',
            'icon'              => $xwander_icon,
            'keywords'          => array( 'tours_repeater', 'tours', 'tour' ),
        ));

        acf_register_block_type(array(
            'name'              => 'xwander_list_tour_days',
            'title'             => __('Xwander list tour days'),
            'description'       => __('xwander list tour days block. '),
            'render_template'   => 'acf_blocks/tours_list.php',
            'category'          => 'xwander-blocks',
            'mode'              => 'preview',
            'icon'              => $xwander_icon,
            'keywords'          => array( 'tours_repeater', 'tours', 'tour' ),
        ));
        */

        acf_register_block_type(array(
            'name'              => 'xwander_list_by_category',
            'title'             => __('Xwander tours by category'),
            'description'       => __('List tours by categories. '),
            'render_template'   => 'acf_blocks/tour_list_by_category.php',
            'category'          => 'xwander-blocks',
            'mode'              => 'edit',
            'icon'              => $xwander_icon,
            'keywords'          => array( 'packages', 'tours', 'tour' ),
        ));


        acf_register_block_type(array(
            'name'              => 'xwander_list_tour_packages',
            'title'             => __('Xwander display tours'),
            'description'       => __('xwander list tours block. '),
            'render_template'   => 'acf_blocks/tour_packages.php',
            'category'          => 'xwander-blocks',
            'mode'              => 'edit',
            'icon'              => $xwander_icon,
            'keywords'          => array( 'packages', 'tours', 'tour' ),
        ));

        acf_register_block_type(array(
            'name'              => 'xwander_footer',
            'title'             => __('Xwander footer'),
            'description'       => __('xwander footer block. '),
            'render_template'   => 'acf_blocks/footer.php',
            'category'          => 'xwander-blocks',
            'mode'              => 'preview',
            'icon'              => $xwander_icon,
            'keywords'          => array( 'footer', 'contact' ),
        ));

        acf_register_block_type(array(
            'name'              => 'xwander_partners',
            'title'             => __('Xwander partners'),
            'description'       => __('xwander partners block. '),
            'render_template'   => 'acf_blocks/partners.php',
            'category'          => 'xwander-blocks',
            'mode'              => 'preview',
            'icon'              => $xwander_icon,
            'keywords'          => array( 'partners' ),
        ));
        
         acf_register_block_type(array(
            'name'              => 'xwander_tour_gallery',
            'title'             => __('Xwander tour gallery'),
            'description'       => __('xwander tour gallery block. '),
            'render_template'   => 'acf_blocks/tour_gallery.php',
            'category'          => 'xwander-blocks',
            'mode'              => 'edit',
            'icon'              => $xwander_icon,
            'keywords'          => array( 'gallery', 'tour' ),
        ));

          acf_register_block_type(array(
            'name'              => 'xwander_tour_cta',
            'title'             => __('Xwander tour CTA'),
            'description'       => __('xwander tour CTA block. '),
            'render_template'   => 'acf_blocks/tour_cta.php',
            'category'          => 'xwander-blocks',
            'mode'              => 'edit',
            'icon'              => $xwander_icon,
            'keywords'          => array( 'CTA', 'tour' ),
        ));

       

    }
}


 // Add Custom Blocks Panel in Gutenberg
function wm_block_categories( $categories, $post )
{
    return array_merge( $categories, array( array(
        'slug'  => 'xwander-blocks',
        'title' => 'xwander Blocks',
    ) ) );
}

add_filter(
    'block_categories',
    'wm_block_categories',
    10,
    2
);



// Add custom styles to ACF editor fields
add_action( 'admin_enqueue_scripts', 'wm_block_admin_style' );

function wm_block_admin_style() 
{
        wp_register_style( 'wm_wp_admin_css', get_stylesheet_directory_uri() . '/admin/admin-style.css', false, '1.0.0' );
        wp_enqueue_style( 'wm_wp_admin_css' );
}

// Add custom javascript to ACF fields
add_action('acf/input/admin_enqueue_scripts', 'wm_admin_enqueue_scripts');

function wm_admin_enqueue_scripts() 
{
    wp_enqueue_script( 'wm-admin-acf-js', get_stylesheet_directory_uri() . '/admin/custom_acf.js', array(), '1.0.0', true );

}

