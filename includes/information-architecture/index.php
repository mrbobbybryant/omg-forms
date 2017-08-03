<?php
namespace OMGForms\IA;

function setup() {
	require_once 'types.php';
	require_once 'forms.php';
	require_once 'forms-model.php';
	require_once 'entries.php';
}

function get_meta_key_value( $keys, $key ) {
	return ( $key ) ? $keys[$key]['key'] : $keys;
}
