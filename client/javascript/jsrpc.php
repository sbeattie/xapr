<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>JSRPC Test</title>
<?php include_once 'JSRPC.inc.php'; ?>
<script>
/*
var db = new JSRPC_DataBridge( 'dbBridgeTest.php' );

db.call( "examples.firstTest.sayHello", "stephen" );
*/


// define on an object instance
var obj = {};

obj.__noSuchMethod__ = function(id, args)
{
  alert('Undefined method ' + id + 
      ' called with arguments (' + args.join(', ') + ')');
};

//obj.foo('bar');

// define on an object prototype
Object.prototype.__noSuchMethod__ = function(id, args)
{
  alert('Undefined method ' + id + 
      ' called with arguments (' + args.join(', ') + ')');
};

var date = new Date();

function trapError(msg, URI, ln)
{
	for( var prop in event.boundElements )
	{
		alert( prop +":"+event.boundElements[prop] );
	}
	return true;
}
window.onerror = trapError;

date.baz('foobar');

</script>
</head>

<body>
</body>
</html>
