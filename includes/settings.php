<?php
namespace OMGForms\Settings;

function setup() {
	add_action( 'admin_menu', __NAMESPACE__ . '\add_omg_forms_setting_menu' );
}

function add_omg_forms_setting_menu() {
	add_submenu_page(
		'options-general.php',
		esc_html__( 'OMG Form', 'omg-forms' ),
		esc_html__( 'OMG Form', 'omg-forms' ),
		'manage_options',
		'form_settings',
		__NAMESPACE__ . '\form_settings_page_display'
	);
}

function form_settings_page_display() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'OMG Form Settings', 'omg-forms' ); ?></h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'omg-forms-section' );
			do_settings_sections( 'form_settings' );
			submit_button();
			?>
		</form>
	<div>
	<?php
}