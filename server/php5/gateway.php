<?php

$cwd = realpath( dirname( __FILE__ ) ).'/';
$paths = array( $cwd.'/core', $cwd.'/services' ini_get( 'includes_path' ) );
ini_set( 'includes_path', explode( PATH_SEPARATOR, $paths ) );

function __autoload( $class_name ){
	require $class_name.'.php';
}

// ------------------------------------------------------------------------------
// Instantiate XAPR gateway
// ------------------------------------------------------------------------------

$xapr = new XAPR_Gateway( $cwd.'/services' );
$xapr->setGzipCompression( 4 );
$xapr->service();

?>