<?php

class XAPR
{
	const VERSION = '0.1'; 	// XAPR specification version

	public static function serialize( $item, $doc )
	{
		if( is_bool($item) )
		{
			$node = $doc->createElement( 'b' );
			$node->setAttribute( 'v', (bool) $item );
			return $node;
		}
		else if( is_string($item) )
		{
			$node = $doc->createElement( 's' );
			$textNode = $doc->createCDATASection( (string) $item );
			$node->appendChild( $textNode );
			return $node;
		}
		else if( is_int($item) )
		{
			$node = $doc->createElement( 'n' );
			$node->setAttribute( 'v', (int) $item );
			return $node;
		}
		else if( is_float($item) )
		{
			$node = $doc->createElement( 'n' );
			$node->setAttribute( 'v', (float) $item );
			return $node;
		}
		else if ( is_null($item) )
		{
			$node = $doc->createElement( 'u' ); 
			return $node;
		}
		else if ( is_array($item) )
		{
			$node = $doc->createElement( 'a' );
			foreach( $item as $key => $value )
			{
				$elemNode = $doc->createElement( 'e' );
				$elemNode->setAttribute( 'k', $key );
				$elemNode->appendChild( XAPR::serialize( $value, $doc ) );
				$node->appendChild( $elemNode );
			}
			return $node;
		}		
		else if ( is_object($item) )
		{
			if( get_class($item) == 'DateTime' )
			{
				$node = $doc->createElement( 'd' );
				$node->setAttribute( 'v', $item->format('U') );
				return $node;
			}
			else if ( get_class($item) == 'DOMDocument' )
			{
				$node = $doc->createElement( 'x' );
				$textNode = $doc->createCDATASection( $item->saveXML() );
				$node->appendChild( $textNode );
				return $node;			
			}
			else
			{
				$node = $doc->createElement( 'o' );
				if( get_class($item) != 'stdClass' )
				{
					$node->setAttribute( 'c', get_class($item) );
				}
				
				$props = get_object_vars( $item );
				foreach( $props as $name => $value )
				{
					$elemNode = $doc->createElement( 'p' );
					$elemNode->setAttribute( 'n', $name );
					$elemNode->appendChild( XAPR::serialize( $value, $doc ) );
					$node->appendChild( $elemNode );
				};
				return $node;
			}			
		}
		else
		{
			$node = $doc->createElement( 'u' ); 
			return $node;
		}
	}

	public static function deserialize( $node )
	{
		switch ( $node->nodeName )
		{
			case "b":  // Boolean ( represented as '0' or '1' )
				return (bool) $node->getAttribute( 'v' );
			
			case "s":  // String in <![CDATA[]]> node
				return (string) $node->firstChild->nodeValue;

			case "u":  // Null
				return null;

			case "n":  // Number
				return intval( $node->getAttribute( 'v' ) );

			case "d":  // Date
				return new DateTime( gmdate( 'Y-m-d H:i:s', (int) $node->getAttribute('v') ) );

			case "a":  // Array
				$a = array();
				$subNode = $node->firstChild;
				while( $subNode != null )
				{	
					$key = $subNode->getAttribute('k');
					if( ctype_digit($key) )
					{
						$a[intval($key)] = XAPR::deserialize( $subNode->firstChild );
					}
					else
					{					
						$a[$key] = XAPR::deserialize( $subNode->firstChild );
					}
					$subNode = $subNode->nextSibling;
				}
				return $a;

			case "x":  // XML Data in <![CDATA[]]> node
				$x = new DOMDocument( '1.0', 'utf-8' );
				$x->loadXML( $node->firstChild->nodeValue );
				return $x;

			case "o":  // Object/Class
				if ( $node->getAttribute('c') )
				{
					$className = $node->getAttribute('c');
					$o = new $className();
				}
				else
				{	
					$o = new stdClass();
				}

				$subNode = $node->firstChild;
				while( $subNode != null )
				{
					$propName = $subNode->getAttribute('n');
					$o->$propName = XAPR::deserialize( $subNode->firstChild );
					$subNode = $subNode->nextSibling;
				}
				return $o;

			default:
				return null;
		}
	}
	
	/**
	* creates an 8-digit time signature unique to a 24hr period
	* eg. 090802 for 9:08am and 2ms
	*/	
	public function generate_id()
	{
		$timeParts = explode( ' ', microtime() );
		$ms = intval( 100*$timeParts[0] );
		
		return date('His').str_pad( (string) $ms, 2,'0', STR_PAD_LEFT );	
	}
	
	public static function request( $method, $params=array(), $format=false )
	{
		$xml = new DOMDocument( '1.0', 'utf-8' );

		$rootNode = $xml->createElement( 'request' );
		$rootNode->setAttribute( 'id', XAPR::generate_id() );
		$rootNode->setAttribute( 'version', XAPR::VERSION );
		$rootNode->setAttribute( 'method', $method );
		$xml->appendChild( $rootNode );

		$pNode = $xml->createElement( 'params' );
		$rootNode->appendChild( $pNode );
		$paramsNode = $xml->createElement( 'a' );
		$pNode->appendChild( $paramsNode );
		
		$count = 0;
		foreach( $params as $param )
		{
			$elemNode = $xml->createElement( 'e' );
			$elemNode->setAttribute( 'k', $count );
			
			$argNode = XAPR::serialize( $param, $xml );
			$elemNode->appendChild( $argNode );
			
			$paramsNode->appendChild( $elemNode );
			
			$count++;
		}
				
		if( $format )
		{
			$xml->formatOutput = $format;
		}
		
		return $xml->saveXML();
	}
	
	public static function response( $id, $obj, $format=false )
	{
		$xml = new DOMDocument( '1.0', 'utf-8' );

		$rootNode = $xml->createElement( 'response' );
		$rootNode->setAttribute( 'id', $id );
		$rootNode->setAttribute( 'version', XAPR::VERSION );
		$xml->appendChild( $rootNode );

		$resultNode = $xml->createElement( 'result' );
		$rootNode->appendChild( $resultNode );
		
		$resNode = XAPR::serialize( $obj, $xml );
		$resultNode->appendChild( $resNode );
	
		if( $format )
		{
			$xml->formatOutput = $format;
		}
		
		return $xml->saveXML();
	}
	
	public static function error( $id, Exception $e, $requestXML='', $format=false )
	{
		$xml = new DOMDocument( '1.0', 'utf-8' );

		$rootNode = $xml->createElement( 'error' );
		$rootNode->setAttribute( 'id', $id );
		$rootNode->setAttribute( 'code', $e->getCode() );
		$rootNode->setAttribute( 'type', get_class( $e ) );
		$rootNode->setAttribute( 'msg', $e->getMessage() );
		$xml->appendChild( $rootNode );
		
		$detailNode = $xml->createElement( 'detail' );
		$detailNode->appendChild( $xml->createCDATASection( $e->__toString() ) );
		$rootNode->appendChild( $detailNode );
		
		if( $requestXML != '' )
		{
			$reqNode = $xml->createElement( 'request' );
			$reqNode->appendChild( $xml->createCDATASection( $requestXML ) );
			$rootNode->appendChild( $reqNode );		
		}
			
		if( $format )
		{
			$xml->formatOutput = $format;
		}
		
		return $xml->saveXML();
	}
}
