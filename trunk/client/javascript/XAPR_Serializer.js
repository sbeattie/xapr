// -------------------------------------------------------------------------------------------
// XAPR Serializer
// -------------------------------------------------------------------------------------------

function XAPR_Serializer()
{
}

XAPR_Serializer.prototype.getClassName = function( obj )
{
	var funcText = obj.constructor.toString();
	var matches = funcText.match( /function ([^\s(]+)/ );

	return matches[1];
}

XAPR_Serializer.prototype.serialize = function( item, doc )
{
	switch ( typeof item )
	{
		case "boolean":		
			var node = doc.createElement( 'b' );
			node.setAttribute('v', Boolean( item ) );
			return node;
			
		case "string":
			var node = doc.createElement( 's' );
			var textNode = doc.createTextNode( item );
			node.appendChild( textNode );
			return node;

		case "number":
			var node = doc.createElement( 'n' );
			node.setAttribute('v', item);
			return node;

		case "object":

			if( item == null )
			{
				var node = doc.createElement( 'u' ); 
				return node;
			}

			if( item instanceof Date )
			{
				var node = doc.createElement( 'd' );
				node.setAttribute('v', Math.round(item.getTime()/1000) );
				return node;
			}

			else if ( item instanceof Array )
			{
				if ( item.length == 0 ) return;

				var node = doc.createElement( 'a' );
				for ( var i in item )
				{
					var elemNode = doc.createElement( 'e' );
					elemNode.setAttribute('k', i);
					var val = item[i];
					elemNode.appendChild( XAPR_Serializer.serialize( val, doc ) );
					node.appendChild( elemNode );
				};
				return node;
			}

			else
			{
				var node = doc.createElement( 'o' );
				node.setAttribute( 'c', this.getClassName( item ) );
				for ( var i in item )
				{
					if ( !( item[i] instanceof Function ) )
					{
						var elemNode = doc.createElement( 'p' );
						elemNode.setAttribute('n', i);
						var val = item[i];
						elemNode.appendChild( XAPR_Serializer.serialize( val, doc ) );
						node.appendChild( elemNode );
					}
				};
				return node;
			}

		default:		
			var node = doc.createElement( 'u' ); 
			return node;
	}
}

// instantiate XAPR_Serializer singleton
XAPR_Serializer = new XAPR_Serializer();