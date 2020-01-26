<?php

class NotificationModel extends Model {

    private $type;
    private $message;
    const ERROR   = 'nerror';
    const WARNING = 'nwarning';
    const INFO    = 'ninfo';        
    const SUCCESS = 'nsuccess';
    
    public function __construct($type, $message) {
    
    	$this->type = $type;
    	$this->message = $message;
    
    }
    
    public function getType(){
	    return $this->type;
    }
    
    public function getMessage(){
	    return $this->message;
    }
    

   
}

?>