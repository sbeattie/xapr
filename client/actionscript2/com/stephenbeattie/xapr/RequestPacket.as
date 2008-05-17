
import com.stephenbeattie.xapr.Serializer;
 
class com.stephenbeattie.xapr.RequestPacket
{
	private var _xml:XML;
	private var _paramsNode:XMLNode;
	private var _paramsCount:Number;

	function RequestPacket( id:Number, method:String )
	{
		_xml = new XML();

		var rootNode:XMLNode = _xml.createElement( 'request');
		rootNode.attributes['id'] = id;
		rootNode.attributes['method'] = method;
		rootNode.attributes['version'] = "0.1";
		_xml.appendChild( rootNode );

		_paramsCount = 0;

		var pNode:XMLNode = _xml.createElement( 'params' );
		rootNode.appendChild( pNode );
		_paramsNode = _xml.createElement( 'a' );
		pNode.appendChild( _paramsNode );
	}

	function addParameter( arg ): Void
	{
		var elemNode:XMLNode = _xml.createElement( 'e' );
		elemNode.attributes['k'] = _paramsCount;
		var argNode:XMLNode = Serializer.serialize( arg, _xml );
		elemNode.appendChild( argNode );

		_paramsNode.appendChild( elemNode );
		_paramsCount++;
	}

	function getXML(): String
	{
		return _xml.toString();
	}
}