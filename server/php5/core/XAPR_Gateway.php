<?php

class XAPR_Gateway
{
	protected $services_dir;
	protected $gzip_compression_level;

	public function __construct( $path='' )
	{
		if( $path == '' ){
			$path = realpath( dirname(__FILE__).'../' ).'/services';
		}
	
		$this->services_dir = $path;
		$this->gzip_compression_level = 0; // gzip disabled by default
	}
	
	public final function setGzipCompression( $level=6 )
	{
		$this->gzip_compression_level = (int) $level;
	}
	
	public final function service()
	{
		// get raw POST body
        $requestXML = file_get_contents( 'php://input' );
		
		// if no request XML, return error
		if( $requestXML == '' )
		{
			// cannot find requested procedure
			$e = new XAPRException( 'No request XML was sent', 404 );
			$this->send_error( -1, $e, $requestXML );
		}
		
		// load XML into DOM
		$node = new DOMDocument( '1.0', 'utf-8' );
	    $node->loadXML( $requestXML ); // need error checking here !!
		$node->preserveWhiteSpace = false;

		// reference XML
		$rootNode = $node->documentElement;
		
		// result payload
		$result = true;
		
		if( !$rootNode->hasAttribute('id') || !$rootNode->hasAttribute('method') )
		{
			// invalid XAPR request!
			$e = new XAPRException( 'Badly formatted request', 400 );
			$this->send_error( -1, $e, $requestXML );
		}
		
		// get request ID
		$id = $rootNode->getAttribute('id');
		
		$rpcParts = explode('.', $rootNode->getAttribute('method'));
		
		// check service and method are defined			
		if( count($rpcParts) != 2 )
		{
			// version mismatch error
			$e = new XAPRException( 'Bad service path.  Must be in the form Service.method', 404 );
			$this->send_error( $id, $e, $requestXML );
		}
		
		list($service, $method) = $rpcParts;
		
		// attempt to load service class
		if( $this->services_dir != '' )
		{
			$serviceFile = $this->services_dir.DIRECTORY_SEPARATOR.$service.'.php';
			if( file_exists( $serviceFile ) )
			{
				include $serviceFile;
			}
		}
					
		if( !class_exists( $service ) )
		{
			// version mismatch error
			$e = new XAPRException( 'Unknown Service', 404 );
			$this->send_error( $id, $e, $requestXML );
		}
		
		// get request params
		$args = array();
		if( $rootNode->hasChildNodes() && $rootNode->firstChild->nodeName == 'params' )
		{
			$paramsNode = $rootNode->firstChild;
			$args = XAPR::deserialize( $paramsNode->firstChild );
		}
		
		$xapr_service_class = 'XAPR_Service';			
		if ( $service instanceof $xapr_service_class )
		{
			// version mismatch error
			$e = new XAPRException( 'The requested service is not a valid XAPR Service', 404 );
			$this->send_error( $id, $e, $requestXML );
		}
		
		// check if we can service the request		
		if( !method_exists( $service, $method) )
		{
			// cannot find requested procedure
			$e = new XAPRException( 'Cannot find requested procedure', 404 );
			$this->send_error( $id, $e, $requestXML );
		}
		
		try
		{			
			// avoid call_user_func_array() if possible for speed
			if( count( $args ) < 4 )
			{
				$instance = new $service();
				
				switch( count( $args ) )
				{
					case 0:
						$result = $instance->$method();
						break;
					case 1:
						$result = $instance->$method( current($args) );
						break;
					case 2:
						$result = $instance->$method( current($args), next($args) );
						break;
					case 3:
						$result = $instance->$method( current($args), next($args), next($args) );
						break;
					default:
						break;
				}
			}
			else
			{
				$result = call_user_func_array( array($service, $method), $args );
			}
		}
		catch( Exception $e )
		{
			// PHP error
			$this->send_error( $id, $e, $requestXML );
		}
		
		// set headers and print response
		if( $this->gzip_compression_level > 0 )
		{
			ini_set( 'zlib.output_compression_level', $this->gzip_compression_level );
		}
		
		// if client accepts gzipped content
		if( isset($_SERVER['HTTP_ACCEPT_ENCODING']) && substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') )
		{
			ob_start( 'ob_gzhandler' );
		}
		else
		{
			 ob_start();
		}
		
		echo ( $method == 'describe' ) ? $result : XAPR::response( $id, $result );
		$reponse = ob_get_contents();
		ob_end_clean();

		header( 'Content-type: application/xml' );
		header( 'Content-length: '.strlen($reponse) );
		
		echo $reponse;
	  	exit();
    }
	
	public function send_error( $id, Exception $e, $requestXML='', $format=false )
	{
		// set headers and print response
		// gzip output settings ignored to ensure IE 5.x and 6.x receive a response
		header( 'Content-type: application/xml' );
		$error = XAPR::error( $id, $e, $requestXML, $format );
		header( 'Content-length: '.strlen($error) );
		
		echo $error;		
		exit();
	}
}