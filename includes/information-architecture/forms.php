<?php
namespace OMGForms\Plugin\IA;

function register_forms_taxonomy() {
	$labels = [
		'name'                       => esc_html__( 'Forms', 'omg-forms' ),
		'singular_name'              => esc_html__( 'Form', 'omg-forms' ),
		'search_items'               => esc_html__( 'Search Forms', 'omg-forms' ),
		'popular_items'              => esc_html__( 'Popular Forms', 'omg-forms' ),
		'all_items'                  => esc_html__( 'All Forms', 'omg-forms' ),
		'edit_item'                  => esc_html__( 'Edit Form', 'omg-forms' ),
		'update_item'                => esc_html__( 'Update Form', 'omg-forms' ),
		'add_new_item'               => esc_html__( 'Add New Form', 'omg-forms' ),
		'new_item_name'              => esc_html__( 'New Form Name', 'omg-forms' ),
		'separate_items_with_commas' => esc_html__( 'Separate Forms with commas', 'omg-forms' ),
		'add_or_remove_items'        => esc_html__( 'Add or remove Forms', 'omg-forms' ),
		'choose_from_most_used'      => esc_html__( 'Choose from the most used Forms', 'omg-forms' ),
		'not_found'                  => esc_html__( 'No Forms found', 'omg-forms' ),
		'menu_name'                  => esc_html__( 'Forms', 'omg-forms' )
	];

	$args = array(
		'hierarchical'          => true,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => [ 'slug' => 'forms' ],
	);
	register_taxonomy( get_tax_forms(), get_type_entries(), $args );
}
add_action( 'init', __NAMESPACE__ . '\register_forms_taxonomy' );
