<?php


class MemcacheModel extends Model {

    private $memcache;

    public function __construct() {
      $this->memcache = new Memcached();
      $this->memcache->addServer('localhost', 11211);
    }


    public function setVariable($name, $data, $expire=300){
      $this->memcache->set( $name, $data, $expire);
    }


    public function getVariable($name){
      $data = $this->memcache->get($name); 
      return $data;
    }

}

?>
