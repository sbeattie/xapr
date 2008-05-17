<?php

class TestService extends XAPR_Service
{
	public function __construct()
	{
		parent::__construct();
	}

	public function addNumbers( $num1, $num2 )
	{
		return $num1 + $num2;
	}
	
	public function echoString( $str )
	{
		return 'You sent: '.$str;
	}
	
	public function echoArbitraryArray()
	{
		return func_get_args();
	}
	
	public function echoDate( $d )
	{
		return $d;
	}
	
	public function 
}

?>