
import com.stephenbeattie.xapr.Serializer;
import com.stephenbeattie.xapr.Deserializer;
import com.stephenbeattie.xapr.RequestPacket;
import com.stephenbeattie.util.NumberPrototypes;
import mx.utils.Delegate;
import RSA.Base64;

dynamic class com.stephenbeattie.xapr.Service
{
	//==============================================================================
	// Member variables
	//==============================================================================
	
	private var _lastRequest:String
	private var _lastResponse:String;
	private var _lastError:String;
	
	private var _username:String = '';
	private var _password:String = '';
	
	private var _requestID:Number;
	private var _handlers:Array;
	private var _serviceUrl:String;
	private var _serviceName:String;
	
	private var _id:Number;

	//==============================================================================
	// Constructor
	//==============================================================================

	function Service( serviceUrl:String, serviceName:String )
	{
		_requestID = 1;
		_handlers = new Array();
		_serviceUrl = ( _serviceUrl == undefined ) ? 'localhost' : serviceUrl;
		_serviceName = serviceName;
	}
	
	public function getServiceName():String
	{
		return _serviceName;
	}
	
	//==============================================================================
	// Authentication
	//==============================================================================	
	
	public function setCredentials( username:String, password:String ): Void
	{
		_username = username;
		_password = password;
	}
	
	private function getCredentials():String
	{
		return Base64.encode( _username + ':' + _password );
	}
	
	//==============================================================================
	// Sending Information
	//==============================================================================
	
	private function generateRequestId()
	{
		var d = new Date();
		var hours:String = NumberPrototypes.force2Digits( d.getHours() );
		var mins:String = NumberPrototypes.force2Digits( d.getMinutes() );
		var secs:String = NumberPrototypes.force2Digits( d.getSeconds() );
		var ms:String = d.getMilliseconds().toString();
		var msLength:Number = ms.length;
		
		var msString:String = '';
		for( var i:Number=0; i<(4-msLength); i++ )
		{
			msString += '0';
		}
		msString += ms;
		
		return hours + mins + secs + msString;		
	}
	
	private function send( msg:String ): Void
	{
		var receiveXML = new XML();
		receiveXML.ignoreWhite = true;
		receiveXML.scope = this;
		receiveXML.onLoad = function( success )
		{
			this.scope._onLoad( this.toString() );
		};
		
		var sendXML:XML = new XML( msg );		
		if( _username != '' ){
			sendXML.addRequestHeader( 'Authorization', getCredentials() );
		}		
		sendXML.addRequestHeader( 'Content-Type', 'application/xml' );
		sendXML.addRequestHeader( 'Accept-Encoding', 'compress, gzip' );
		sendXML.sendAndLoad( _serviceUrl, receiveXML );
	}

	public function sendRequest (): Void
	{
		_id = generateRequestId();
		var method:String = arguments[0];
		var params:Array = arguments.slice(2);

		var packet:RequestPacket = new RequestPacket( _id, _serviceName+'.'+method );
		for( var i:Number=0; i<params.length; i++ )
		{
			packet.addParameter( params[i] );
		}

		// store last request ( for debugging )
		var msg:String = packet.getXML();
		_lastRequest = msg;
		
		_handlers[ _id ] = arguments[1];
		
		send( msg );
	}


	//==============================================================================
	// Receiving Information
	//==============================================================================

	public function _onLoad ( xmlStr:String ): Void
	{
		var node:XML = new XML();
		    node.ignoreWhite = true;
		    node.parseXML( xmlStr );

		var rootNode:XMLNode = node.firstChild;
		
		switch ( rootNode.nodeName )
		{
			case "response":

				var id = rootNode.attributes['id'];
				var resultNode:XMLNode = rootNode.firstChild;
				var resultObj:Object = Deserializer.deserialize( resultNode.firstChild );
				
				var scope:Object = _handlers[ id ];
				scope.onSuccess.apply( null, [resultObj] );

				break;

			case "error":

				var id:Number = Number( rootNode.attributes['id'] );
				var code:String = rootNode.attributes['code'];
				var description:String = rootNode.attributes['msg'];
				var detail:String = rootNode.firstChild.nodeValue;				
				var type:String = rootNode.attributes['type'];
			
				var scope:Object = _handlers[ id ];
				scope.onError.apply( null, [code, description, detail, type] );

				break;
		}
	}

	//==============================================================================
	// Remote Method Resolution
	//==============================================================================

	private function __resolve( method:String ): Function
	{
		_root.args = arguments;
	
		var f:Function = function()
		{
			if ( arguments[0] != null )
			{
				if ( typeof( arguments[0].onSuccess ) != "function" )
				{
					trace( "WARNING: No 'onSuccess' method defined in responder for call to " 
						   + _serviceName + '.' +method + "()" );
					return null;
				}

				if ( typeof( arguments[0].onError ) != "function" )
				{
					trace( "WARNING: No 'onError' method defined in responder for call to " 
					       + _serviceName + '.' +method + "()" );
					return null;
				}
			}
			arguments.unshift( method );
			sendRequest.apply( this, arguments );
		}
		
		this[method] = f;

		return f;
	}

	//==============================================================================
	// Debug Information
	//==============================================================================
	
	public function getLastRequest(): String
	{
		return _lastRequest;
	}

	public function getLastResponse(): String
	{
		return _lastResponse;
	}

	public function getLastError(): String
	{
		return _lastError;
	}

}