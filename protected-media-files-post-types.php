<?php
//P Posts
add_action( 'init', 'mos_p_file_init' );
function mos_p_file_init() {
	$labels = array(
		'name'               => _x( 'Protected Posts', 'post type general name', 'excavator-template' ),
		'singular_name'      => _x( 'Protected Post', 'post type singular name', 'excavator-template' ),
		'menu_name'          => _x( 'Protected Posts', 'admin menu', 'excavator-template' ),
		'name_admin_bar'     => _x( 'Protected Post', 'add new on admin bar', 'excavator-template' ),
		'add_new'            => _x( 'Add New', 'p_file', 'excavator-template' ),
		'add_new_item'       => __( 'Add New P Post', 'excavator-template' ),
		'new_item'           => __( 'New Protected Post', 'excavator-template' ),
		'edit_item'          => __( 'Edit Protected Post', 'excavator-template' ),
		'view_item'          => __( 'View Protected Post', 'excavator-template' ),
		'all_items'          => __( 'All Protected Posts', 'excavator-template' ),
		'search_items'       => __( 'Search Protected Posts', 'excavator-template' ),
		'parent_item_colon'  => __( 'Parent Protected Posts:', 'excavator-template' ),
		'not_found'          => __( 'No Protected Posts found.', 'excavator-template' ),
		'not_found_in_trash' => __( 'No Protected Posts found in Trash.', 'excavator-template' )
	);

	$args = array(
		'labels'             => $labels,
        'description'        => __( 'Description.', 'excavator-template' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'p_file' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 7,
		'menu_icon' => 'dashicons-shield',
		'supports'           => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
	);

	register_post_type( 'p_file', $args );
}


add_action( 'after_switch_theme', 'flush_rewrite_rules' );
