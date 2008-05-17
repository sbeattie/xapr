// -------------------------------------------------------------------------------------------
// XAPR Deserializer
// -------------------------------------------------------------------------------------------

function XAPR_Deserializer()
{
}

XAPR_Deserializer.prototype.deserialize = function( node )
{
	switch ( node.nodeName )
	{
		case "b":  // Boolean ( represented as '0' or '1' )
			return Boolean( node.getAttribute('v') );
		
		case "s":  // String in <![CDATA[]]> node - !!! not possible
			return String( node.firstChild.nodeValue );

		case "u":  // Null
			return null;

		case "i":  // Integer
			return Number( node.getAttribute('v') );

		case "f":  // Float
			return Number( node.getAttribute('v') );

		case "d":  // Date
			var d = new Date()
			d.setTime( node.getAttribute('v') );
			return d;

		case "a":  // Array
			var a = new Array();
			var subNode = node.firstChild;
			while( subNode != null )
			{	
				// read key/values
				a[ Number( subNode.getAttribute('k') ) ] = XAPR_Deserializer.deserialize( subNode.firstChild );
				subNode = subNode.nextSibling;
			}
			return a;

		case "o":  // Object/Class
			if ( node.getAttribute('c') != undefined )
			{
				var className = node.getAttribute('c');
				var o = new ( eval(className) )();
			}
			else
			{	var o = new Object();
			}

			var subNode = node.firstChild;
			while( subNode != null )
			{
				// read properties and their values
				o[ subNode.getAttribute('p') ] = XAPR_Deserializer.deserialize( subNode.firstChild );
				subNode = subNode.nextSibling;
			}
			return o;

		default:
			return null;
	}
}

// instantiate XAPR_Deserializer singleton
XAPR_Deserializer = new XAPR_Deserializer();
