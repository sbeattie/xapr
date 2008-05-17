// -------------------------------------------------------------------------------------------
// XAPR RequestPacket
// -------------------------------------------------------------------------------------------

function XAPR_RequestPacket( id, method )
{
	this._xml = Sarissa.getDomDocument();

	var rootNode = this._xml.createElement( 'request' );
	rootNode.setAttribute('id', id );
	rootNode.setAttribute('method', method );
	this._xml.appendChild( rootNode );

	this._paramsCount = 0;

	var pNode = this._xml.createElement( 'params' );
	rootNode.appendChild( pNode );
	this._paramsNode = this._xml.createElement( 'a' );
	pNode.appendChild( this._paramsNode );
}

XAPR_RequestPacket.prototype.addParameter = function( arg )
{
	var elemNode = this._xml.createElement( 'e' );
	elemNode.setAttribute('k', this._paramsCount );
	var argNode = XAPR_Serializer.serialize( arg, this._xml );
	elemNode.appendChild( argNode );

	this._paramsNode.appendChild( elemNode );
	this._paramsCount++;
}

XAPR_RequestPacket.prototype.getXML = function()
{
	var serializer = new XMLSerializer();
	return serializer.serializeToString(this._xml);
}
