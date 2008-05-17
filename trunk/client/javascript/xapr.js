/*** Global Script Wrapper ***/
var jscripts = document.getElementsByTagName("script");	
var jscriptsPath = '';

for( var i=0; i<jscripts.length; i++ )
{
	var sSrc = jscripts[i].src.toLowerCase();
	if( sSrc.indexOf("xapr.js") != -1 ) 
	{
		jscriptsPath = jscripts[i].src.split("xapr.js").join("");
	}
}

document.write('<script type="text/javascript" src="' + jscriptsPath + 'sarissa.js"></script>');

document.write('<script type="text/javascript" src="' + jscriptsPath + 'XAPR_AsyncRequest.js"></script>');
document.write('<script type="text/javascript" src="' + jscriptsPath + 'XAPR_DataBridge.js"></script>');
document.write('<script type="text/javascript" src="' + jscriptsPath + 'XAPR_Deserializer.js"></script>');
document.write('<script type="text/javascript" src="' + jscriptsPath + 'XAPR_ErrorPacket.js"></script>');
document.write('<script type="text/javascript" src="' + jscriptsPath + 'XAPR_RequestPacket.js"></script>');
document.write('<script type="text/javascript" src="' + jscriptsPath + 'XAPR_ResponsePacket.js"></script>');
document.write('<script type="text/javascript" src="' + jscriptsPath + 'XAPR_Serializer.js"></script>');
document.write('<script type="text/javascript" src="' + jscriptsPath + 'XAPR_XMLDocument.js"></script>');
document.write('<script type="text/javascript" src="' + jscriptsPath + 'XAPR_XMLHttpRequest.js"></script>');