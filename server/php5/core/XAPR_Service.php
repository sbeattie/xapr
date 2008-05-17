<?php

// XAPR services should extend this class

class XAPR_Service
{
	protected $summary = ''; 		// the default summary description for the service
	protected $help = ''; 			// the default help page URL for the service
	
	public function __construct()
	{
		// empty
	}

	public final function describe()
	{
		$service = get_class($this);
	
		$protocol = ( isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ) ? 'https://' : 'http://';
		$address = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
		
		$xml = new DOMDocument( '1.0', 'utf-8' );
		$rootNode = $xml->createElement( 'service' );
		$rootNode->setAttribute( 'name', $service );
		$rootNode->setAttribute( 'address', $address );
		$rootNode->setAttribute( 'version', XAPR::VERSION );
		$xml->appendChild( $rootNode );
		
		if( $this->summary != '' )
		{
			$summaryNode = $xml->createElement( 'summary' );
			$summaryNode->appendChild( $xml->createCDATASection( $this->summary ) );
			$rootNode->appendChild( $summaryNode );
		}
		
		if( $this->help != '' )
		{
			$helpNode = $xml->createElement( 'help' );
			$helpNode->appendChild( $xml->createCDATASection( $this->help ) );
			$rootNode->appendChild( $helpNode );		
		}
		
		$refl = new ReflectionClass( $service );
		
		$procsNode = $xml->createElement( 'procedures' );
		$rootNode->appendChild( $procsNode );
		
		foreach( $refl->getMethods() as $method )
		{
			if( $method->isPublic() && $method->getDeclaringClass() == $refl && $method->getName() != '__construct' )
			{
				$procNode = $xml->createElement( 'procedure' );
				$procNode->setAttribute( 'name', $method->getName() );
			
				foreach( $method->getParameters() as $param )
				{
					$paramNode = $xml->createElement( 'param' );
					$paramNode->setAttribute( 'name', $param->getName() );
					if ( $param->getClass() )
					{
						$paramNode->setAttribute( 'type', $param->getClass()->getName() );
					}
					else
					{
						$paramNode->setAttribute( 'type', 'any' );
					}
					
					$procNode->appendChild( $paramNode );
				}
						
				$procsNode->appendChild( $procNode );
			}
		}
		
		return $xml->saveXML();
	}
}

?>