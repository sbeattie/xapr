// -------------------------------------------------------------------------------------------
// XAPR ErrorPacket
// -------------------------------------------------------------------------------------------

function XAPR_ErrorPacket( id, code, msg , type )
{
	this._xml = Sarissa.getDomDocument();

	var rootNode = this._xml.createElement( 'error ');
	rootNode.setAttribute( 'id', id );
	rootNode.setAttribute('code', code );
	rootNode.setAttribute('msg', msg );
	rootNode.setAttribute('type', type );
	this._xml.appendChild( rootNode );

	this._detailNode = this._xml.createElement( 'detail' );
	rootNode.appendChild( this._detailNode );
}

XAPR_ErrorPacket.prototype.setDetail = function( detail )
{
	var detNode = XAPR_Serializer.serialize( detail, this._xml );
	this._detailNode.appendChild( detNode );
}

XAPR_ErrorPacket.prototype.getXML = function()
{
	var serializer = new XMLSerializer();
	return serializer.serializeToString(this._xml);
}
