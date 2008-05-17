// -------------------------------------------------------------------------------------------
// XAPR ResponsePacket
// -------------------------------------------------------------------------------------------

function XAPR_ResponsePacket( id )
{
	this._xml = Sarissa.getDomDocument();

	var rootNode = this._xml.createElement( 'response' );
	rootNode.setAttribute( 'id', id );
	this._xml.appendChild( rootNode );

	this._resultNode = this._xml.createElement( 'result' );
	rootNode.appendChild( this._resultNode );
}

XAPR_ResponsePacket.prototype.setResult = function( result )
{
	var resNode = XAPR_Serializer.serialize( result, this._xml );
	this._resultNode.appendChild( resNode );
}

XAPR_ResponsePacket.prototype.getXML = function()
{
	var serializer = new XMLSerializer();
	return serializer.serializeToString(this._xml);
}
