
class com.stephenbeattie.xapr.Deserializer
{
	public static function deserialize ( node:XMLNode )
	{
		switch ( node.nodeName )
		{
			case "b":  // Boolean ( represented as '0' or '1' )
				return Boolean( node.attributes['v'] );
			
			case "s":  // String
				return String( node.firstChild.nodeValue );

			case "u":  // Null
				return null;

			case "n":  // Number
				return Number( node.attributes['v'] );

			case "d":  // Date
				var d:Date = new Date()
				d.setTime( node.attributes['v']*1000 );
				return d;

			case "a":  // Array
				var a:Array = new Array();
				var subNode:XMLNode = node.firstChild;
				while( subNode != null )
				{	
					a[ Number( subNode.attributes['k'] ) ] = Deserializer.deserialize( subNode.firstChild );
					subNode = subNode.nextSibling;
				}
				return a;

			case "x":  // XML
				var x:XML = new XML();
				x.parseXML( node.firstChild.nodeValue );
				return x;

			case "o":  // Object/Class
				if ( node.attributes['c'] != undefined )
				{
					var className:String = node.attributes['c'];
					var oFunc:Function = Function( eval( className ) );
					var o = new oFunc();
				}
				else
				{	var o = new Object();
				}

				var subNode:XMLNode = node.firstChild;
				while( subNode != null )
				{	o[ subNode.attributes['p'] ] = Deserializer.deserialize( subNode.firstChild );
					subNode = subNode.nextSibling;
				}
				return o;

			default:
				return null;
		}
	}
}