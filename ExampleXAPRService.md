#Example XAPR Service

# Sample XAPR Service Class #

XAPR services are regular PHP classes that extend the 'XAPR\_Service' class.  Any method marked as 'public' will be accessible from a XAPR client.


```
<?php

class TestService extends XAPR_Service
{
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
}

?>
```