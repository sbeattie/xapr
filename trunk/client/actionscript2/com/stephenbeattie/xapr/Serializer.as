
import com.stephenbeattie.util.Class;

class com.stephenbeattie.xapr.Serializer
{
	public static function serialize ( item, doc:XML ): XMLNode
	{
		switch ( typeof item )
		{
			case "boolean":
			
				var node:XMLNode = doc.createElement( 'b' );
				node.attributes['v'] = Boolean( item );
				return node;

				
			case "string":

				var node:XMLNode = doc.createElement( 's' );
				var textNode:XMLNode = doc.createTextNode( item );
				node.appendChild( textNode );
				return node;


			case "number":

				var node:XMLNode = doc.createElement( 'n' );
				node.attributes['v'] = item;
				return node;


			case "null":
			case "undefined":

				var node:XMLNode = doc.createElement( 'u' ); 
				return node;


			case "object":

				if ( item instanceof Date )
				{
					var node:XMLNode = doc.createElement( 'd' );
					node.attributes['v'] = Math.round(item.getTime()/1000);
					return node;
				}
				else if ( item instanceof XML )
				{
					if ( item.toString() == "" )
					{
						var node:XMLNode = doc.createElement( 'u' ); 
						return node;
					}
					else
					{
						var node:XMLNode = doc.createElement( 'x' );
						var textNode:XMLNode = doc.createTextNode( item.toString() );
						node.appendChild( textNode );
						return node;
					}
				}
				else
				{
					var className:String = Class.getClass( item, true );
					var objIsArray = ( item instanceof Array || className == 'Array' );
					if ( objIsArray ) 
					{
						var node:XMLNode = doc.createElement( 'a' );
						
						if ( item.length == 0 )
						{
							return node;
						}
						else
						{
							for ( var i in item )
							{
								if ( !( item[i] instanceof Function ) )
								{
									var elemNode = doc.createElement( 'e' );
									elemNode.attributes['k'] = i;
									var val = item[i];
									elemNode.appendChild( Serializer.serialize( val, doc ) );
									node.appendChild( elemNode );
								}
							};
							
							return node;
						}
					}
					else
					{
						var node:XMLNode = doc.createElement( 'o' );
					}
					if( className != undefined && !objIsArray )
					{
						node.attributes['c'] = className;
					}
					
					for ( var i in item )
					{
						if ( !( item[i] instanceof Function ) )
						{
							var elemNode = doc.createElement( 'p' );
							elemNode.attributes['n'] = i;
							var val = item[i];
							elemNode.appendChild( Serializer.serialize( val, doc ) );
							node.appendChild( elemNode );
						}
					};
					
					return node;
				}


			default:
			
				var node:XMLNode = doc.createElement( 'u' ); 
				return node;
		}
	}
}