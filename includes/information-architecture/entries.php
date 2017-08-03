<?php
namespace OMGForms\IA;

function register_entries_cpt() {
	$labels = array(
		'name'               => esc_html__( 'Entries', 'omg-forms' ),
		'singular_name'      => esc_html__( 'Entry', 'omg-forms' ),
		'add_new'            => esc_html__( 'Add New', 'omg-forms' ),
		'add_new_item'       => esc_html__( 'Add New Entry', 'omg-forms' ),
		'edit_item'          => esc_html__( 'Edit Entry', 'omg-forms' ),
		'new_item'           => esc_html__( 'New Entry', 'omg-forms' ),
		'all_items'          => esc_html__( 'All Entries', 'omg-forms' ),
		'view_item'          => esc_html__( 'View Entry', 'omg-forms' ),
		'search_items'       => esc_html__( 'Search Entries', 'omg-forms' ),
		'not_found'          => esc_html__( 'No Entries found.', 'omg-forms' ),
		'menu_name'          => esc_html__( 'Entries', 'omg-forms' ),
		'not_found_in_trash' => esc_html__( 'No Entries found in Trash', 'omg-forms' ),
	);

	$args = array(
		'labels'                => $labels,
		'description'           => esc_html__( 'Entries', 'omg-forms' ),
		'public'                => true,
		'menu_icon'             => 'dashicons-list-view',
		'hierarchical'          => false,
		'has_archive'           => false,
		'menu_position'         => 12,
		'rewrite'               => array(
			'slug'              => 'entries'
		),
		'supports' => array(
			'title',
		),
		'show_in_rest'          => true,
		'rest_base'             => 'entries',
		'rest_controller_class' => '\OMGForms\API\OMG_Entries_Controller'
	);

	register_post_type( get_type_entries(), $args );
}
add_action( 'init', __NAMESPACE__ . '\register_entries_cpt' );
