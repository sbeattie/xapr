<?php

class XAPRException extends Exception
{
   public function __construct( $message, $code=0 ) 
   {
       parent::__construct( $message, $code );
   }

   public function __toString() 
   {
       return 'XAPRException: '.$this->message;
   }
}