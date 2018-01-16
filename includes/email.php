<?php
namespace OMGForms\Email;

function send( $form, $data ) {
	$to      = $form['email_to'];
	$headers = array(
		'From: ' . get_bloginfo( 'admin_email' ),
	);
	$subject = sprintf( '%s Submission Notification.', \OMGForms\Helpers\get_form_name( $form['name'] ) );

	$message  = 'Hello' . "\r\n\r\n";
	$message .= 'We have received a new form submission on ' . site_url() . ".\r\n\r\n";
	$message .= 'Please login to view this form submission.';

	$subject = apply_filters( 'omg_form_submitted_subject', $subject, $form, $data );
	$headers = apply_filters( 'omg_form_submitted_headers', $headers, $form, $data );
	$message = apply_filters( 'omg_form_submitted_message', $message, $form, $data );

	$sent = wp_mail( $to, $subject, $message, $headers );

	if ( false === $sent ) {
		error_log( 'Email failed to send for form ' . $form['name'] );
	}
}
