// -------------------------------------------------------------------------------------------
// XAPR AsyncRequest
// -------------------------------------------------------------------------------------------

function XAPR_AsyncRequest()
{
    this._xmlhttp = new XAPR_XMLHttpRequestObject();
    this.username = null;
    this.password = null;
	this.timeOut = 5000; // default 5secs timeout on each request
	this.timeOutCallback = null;
}

XAPR_AsyncRequest.prototype.setCredentials = function( user, pass )
{
	this.username = user;
	this.password = pass;
}

XAPR_AsyncRequest.prototype.setTimeOut = function( ms )
{
	this.timeOut = ms;
}

XAPR_AsyncRequest.prototype.call = function( action, url, data )
{
    var instance = this;

    this._xmlhttp.open( action, url, true, this.username, this.password );
    
	this.onOpen( this._xmlhttp );

	this._xmlhttp.onreadystatechange = function()
	{
        switch( instance._xmlhttp.readyState )
		{
			case 1:
				// instance.onLoading();
				break;
			case 2:
				// instance.onLoaded();
				break;
			case 3:
				// instance.onInteract();
				break;
			case 4:
				if( instance.onResult )
				{
					var statusCode = instance._xmlhttp.status;

					if( statusCode == 0 || ( statusCode>= 200 && statusCode < 300 ) )
					{
							instance.onResult( instance._xmlhttp.responseXML );
					}
					else
					{
							instance.onStatus(
								statusCode,
								instance._xmlhttp.statusText,
								instance._xmlhttp.responseText,
								instance._xmlhttp.responseXML
							);
					}
					clearTimeout( this.timeOutCallback );
				}
				break;
			default:
				break;
        }
    }
    try
	{
		this._xmlhttp.send( data );

		// start timer
		var d = new Date();
		this.timeOutCallback = setInterval( this, "abortRequest", d.getTime() + this.timeOut );
    }
    catch( e )
	{
		// do nothing
    }
}

XAPR_AsyncRequest.prototype.abortRequest = function()
{
	this._xmlhttp.abort();
	this.onTimeOut();
}

XAPR_AsyncRequest.prototype.get = function( url )
{
    this.call( "GET", url, null );
}

XAPR_AsyncRequest.prototype.post = function( url, mimetype, datalength, data )
{
	this.onOpen = function( xmlhttp )
	{
        xmlhttp.setRequestHeader( "Content-Type", mimetype );
        xmlhttp.setRequestHeader( "Content-Length", datalength );
    }

    this.call( "POST", url, data );
}

XAPR_AsyncRequest.prototype.onOpen = function(){ /* hook method ( used internally ) */ }


// -------------------------------------------------------------------------------------------
// public methods to override
// -------------------------------------------------------------------------------------------

XAPR_AsyncRequest.prototype.onTimeOut = function()
{
	alert( "onTimeOut" );
}

XAPR_AsyncRequest.prototype.onResult = function( responseXML )
{
	alert( responseXML );
}

XAPR_AsyncRequest.prototype.onStatus = function( status, statusText, responseText, responseXML )
{
	alert( status + ": " + statusText );
}