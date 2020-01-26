<?php

class MongoModel extends Mongo {

    private $conexion;
    private $collection;
    
    public function __construct() {
        //$mongo_ip = $GLOBALS['conf']['mongo']['configuration']['server']['host'];
	$mongo_ip = 'mongodb://mongo';

        $this->conexion = new Mongo($mongo_ip);
        //$this->collection = $this->conexion->admin;
        //$this->collection->authenticate('drainware', 'JVxLZr9UmSMVobGGk0OKL172OsyHOQ6QophYpUu7B3DQhOvbcDZLYAHgNSnNXZTPXjd0OQ93kruR0aY2hLT26t14tnAedGhI9RiYCbCyc9JFw9srrRiLr3fRuiOFu0Jb');
    }
    
    public function connect(){
        return $this->conexion;
    }
    
}

?>
