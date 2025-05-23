<?php 
add_action('init', 'wm_register_tour_post_type' );

function wm_register_tour_post_type () 
{

    $singular = 'Tour';
    $plural = 'Tour';
    $object = 'tour';
    $labels = array(
        'name'                  => $plural, 
        'singular_name'         => $singular,
        'add_name'              => 'Add new',
        'add_new_item'          => 'Add new ' . $singular,
        'edit'                  => 'Edit',
        'edit_item'             => 'Edit ' . $object,
        'new_item'              => 'New ' .$singular,
        'view'                  => 'Show ' . $singular,
        'view_item'             => 'Show ' . $singular,
        'search_term'           => 'Search ' . $object,
        'not_found'             => 'Could not find that ' . $object,  
        'not_found_in_trash'    => 'Could not find that ' . $object . ' from trash'

    );

    $args = array(
        'labels'    => $labels,
        'public'    => true,
        'publicly_queryable' => true,
        'exclude_from_search'=> false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_admin_bar'  => true,
        'menu_position'      => 20,
        'show_in_rest'       => true,
        'menu_icon'          => 'dashicons-admin-site',
        'can_export'         => true,
        'delete_with_user'   => false,
        'hierarchical'       => true,
        'has_archive'        => true, 
        'query_var'          => true,
        'capability_type'    => 'page',
        'map_meta_cap'       => true,
        'rewrite'            => array( 
                'slug'          => 'tour', 
                'with_front'    => true,
                'pages'         => true, 
                'feeds'         => false 
            ),
        'supports'           => array( 
                'title',
                'editor',
                'author',
               // 'thumbnail',
             //   'excerpt',
               'custom-fields',
            //    'comments',
                'page-attributes' 
        )
    );


    register_post_type('tour', $args );

    register_taxonomy(
        'tour-category',  
        'tour',           
        array(
            'hierarchical' => true,
            'label' => 'Tour category', 
            'query_var' => true,
               'show_in_rest' => true,
        'show_ui' => true,
            'rewrite' => array(
                'slug' => 'tour',    
                'with_front' => false 
            )
        )
    );
}


 ?>