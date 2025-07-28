<?php
// tests/bootstrap.php

// Path to where your WP test library is installed.
$tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $tests_dir ) {
    exit( "WP_TESTS_DIR env var is not set.\n" );
}

// Load the WordPress tests functions
require_once $tests_dir . '/includes/functions.php';

// Start up the WP testing environment
tests_add_filter( 'muplugins_loaded', function() {
    require dirname( __DIR__ ) . '/workcity-client-projects.php';
});
require $tests_dir . '/includes/bootstrap.php';
