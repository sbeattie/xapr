// -------------------------------------------------------------------------------------------
// XAPR XMLDocument
// -------------------------------------------------------------------------------------------

function XAPR_XMLDocument() 
{
	var xmlDoc;
	// code for IE
	if ( window.ActiveXObject )
	{
		var msdoms = new Array(
			"MSXML5.DOMDocument",
			"MSXML4.DOMDocument", 
            		"MSXML3.DOMDocument",
            		"MSXML2.DOMDocument", 
            		"MSXML.DOMDocument",
            		"Microsoft.XMLDOM"
		);

        for ( var i=0; i<msdoms.length; i++ )
	{
            try
            {
                return new ActiveXObject(msdoms[i]);
            }
	    catch (e)
	    {
		// do nothing
            }
        }
		
		throw new Error( "Could not instantiate XAPR_XMLDocument" );
	}
	// code for Mozilla, Firefox, Opera, etc.
	else if ( document.implementation && document.implementation.createDocument )
	{
		xmlDoc = document.implementation.createDocument( "", "", null );
	}
	else
	{
		throw new Error( "Could not instantiate XAPR_XMLDocument" );
	}
	return xmlDoc;
}
