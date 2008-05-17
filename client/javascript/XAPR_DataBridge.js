// -------------------------------------------------------------------------------------------
// XAPR DataBridge
// -------------------------------------------------------------------------------------------

function XAPR_DataBridge( url )
{
	this._lastRequest = null;
	this._lastResponse = null;
	this._lastError = null;
	
	this._requestID = 1;
	this._handlers = new Array();
	this._gatewayURL = url;
	
	this.username = null;
	this.password = null;
}

XAPR_DataBridge.prototype.setCredentials = function( user, pass )
{
	this.username = user;
	this.password = pass;
}

// Sending Information

XAPR_DataBridge.prototype.__send = function ( msg )
{
	this._xmlhttp = new XAPR_XMLHttpRequest();
	this._xmlhttp.setCredentials( this.username, this.password );
	this._xmlhttp.call( "POST", this._gatewayURL, msg );
}

XAPR_DataBridge.prototype.sendRequest = function ()
{
	var id = this._requestID;
	var method = arguments[0];
	var params = arguments.slice(2);

	var packet = new RequestPacket( id, method );
	for( var i=0; i<params.length; i++ )
	{
		packet.addParameter( params[i] );
	}

	// store last request ( for debugging )
	var msg = packet.getXML();
	this._lastRequest = msg;

	this.__send( msg );
	this._handlers[ id ] = arguments[1];
	this._requestID++;
}

XAPR_DataBridge.prototype.sendResponse = function ( id, obj )
{
	var packet = new ResponsePacket( id );
	packet.setResult( obj );

	// store last response ( for debugging )
	var msg = packet.getXML();
	this._lastResponse = msg;

	this.__send( msg );
}

XAPR_DataBridge.prototype.sendError = function ( id, code, message, detail, type )
{
	var packet = new ErrorPacket( id, code, message, type );
	packet.setDetail( detail );

	// store last error ( for debugging )
	var msg = packet.getXML();
	this._lastError = msg;

	this.__send( msg );
}


// Receiving Information


XAPR_DataBridge.prototype.onResult = function ( xmlStr )
{
	alert( 'RECEIVED: ' + "\n" + xmlStr + "\n" );
	var node = new XAPR_XMLDocument();
		node.parseXML( xmlStr );

	var rootNode = node.firstChild;

	switch ( rootNode.nodeName )
	{
		case "request":

			var id = Number( rootNode.getAttribute('id') );
			var paramsNode = rootNode.firstChild;
			var params = XAPR_Deserializer.deserialize( paramsNode.firstChild );
			
			var func = eval( rootNode.getAttribute('method') );

			if ( typeof( func ) != 'function' )
			{
				// method not found - return error ( with full request XML )
				this.sendError( id, 1, 'Method not found', xmlStr, 'MethodNotFoundError' );
			}
			else
			{
				// make call to local service and capture result
				var returnData = func.apply( null, params );
				this.sendResponse.apply( null, [id, returnData] );
			}

			break;

		case "response":

			var id = Number( rootNode.getAttribute('id') );
			var resultNode = rootNode.firstChild;
			var resultObj = XAPR_Deserializer.deserialize( resultNode.firstChild );
			
			var scope = _handlers[ id ];
			scope.onResponse.apply( null, [resultObj] );

			break;

		case "error":

			var id = Number( rootNode.attributes['id'] );

			var code = rootNode.getAttribute('code');
			var msg = rootNode.getAttribute('msg');
			var detail = XAPR_Deserializer.deserialize( rootNode.firstChild );
			var type = rootNode.attributes['type'];

			// 'static' hack to overcome scoping issues
			var f = new Fault( code, msg, detail, type );
			var e = new FaultEvent( id, f );
			var scope = _handlers[ id ];
			scope.onError.apply( null, [e] );

			break;
	}
}

XAPR_DataBridge.prototype.onStatus = function ( xmlStr )
{
	//
}


// IMPLICIT Remote Method Resolution (Mozilla only - not used)

XAPR_DataBridge.prototype.__noSuchMethod__ = function( method )
{
	var f = function()
	{
		if ( arguments[0] != null )
		{
			if ( typeof( arguments[0].onResponse ) != "function" )
			{
				trace( "INFO: No 'onResponse' method defined in responder for call to " + method + "()" );
				return null;
			}

			if ( typeof( arguments[0].onError ) != "function" )
			{
				trace( "INFO: No 'onError' method defined in responder for call to " + method + "()" );
				return null;
			}
		}
		arguments.unshift( method );
		this.sendRequest( arguments );
	}
	
	this[method] = f;

	return f;
}

// EXPLICIT Remote Method Resolution (Mozilla only)
XAPR_DataBridge.prototype.callRemote = function( method )
{
	this.sendRequest( arguments );
}

// Debug Information
	
XAPR_DataBridge.prototype.getLastRequest = function()
{
	return this._lastRequest;
}

XAPR_DataBridge.prototype.getLastResponse = function()
{
	return this._lastResponse;
}

XAPR_DataBridge.prototype.getLastError = function()
{
	return this._lastError;
}
