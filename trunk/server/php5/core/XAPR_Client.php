<?php

class XAPR_Client
{
	protected $username = '';
	protected $password = '';
	protected $gatewayUrl;
	protected $serviceName;
	
	public function __construct( $gatewayUrl, $serviceName )
	{
		$this->gatewayUrl = $gatewayUrl;
		$this->serviceName = $serviceName;
	}
	
	public function setCredentials( $username, $password )
	{
		$this->username = $username;
		$this->password = $password;
	}
	
	public function __call( $method, $args )
	{
		$reqXML = XAPR::request( $this->serviceName.'.'.$method, $args );
		
		$ch = curl_init();	
		curl_setopt( $ch, CURLOPT_URL, $this->gatewayUrl );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_USERAGENT, 'XAPR PHP Client' );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $reqXML );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
			"Content-Type: application/xml; charset=UTF-8",
			"Content-Length: ".strlen( $reqXML )
		)); 
		
		if( !empty($this->username) || !empty($this->password) )
		{
			curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ); 
			curl_setopt( $ch, CURLOPT_USERPWD, $this->username.':'.$this->password );
		}

		$data = curl_exec( $ch );

		if ( $data ) 
		{
			$response = $data;
		} 
		else
		{
			$response = curl_error( $ch );
		}
		
		curl_close( $ch );
		
		return $this->handleResponse( $response );	
	}
	
	private function handleResponse( $xmlStr )
	{
		$xml = new DOMDocument( '1.0', 'utf-8' );
		$xml->loadXML( $xmlStr );
		$xml->preserveWhiteSpace = false;

		$rootNode = $xml->documentElement;
		$id = intval( $rootNode->getAttribute('id') );
		
		$result = false;
		
		switch ( $rootNode->nodeName )
		{
			case 'response':

				$resultNode = $rootNode->firstChild;
				$result = XAPR::deserialize( $resultNode->firstChild );
				
				break;

			case 'error':

				$code = intval( $rootNode->getAttribute('code') );
				$type = $rootNode->getAttribute('type');
				$detail = $rootNode->firstChild->nodeValue;				
				$message = $rootNode->getAttributes('msg');
				
				throw new XAPRException( $message, $code );		

				break;
		}
		
		return $result;
	}	
}