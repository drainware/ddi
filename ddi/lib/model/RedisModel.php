<?php


class RedisModel extends Model {

    private $redis;

    public function __construct() {

	Predis\Autoloader::register();

	$single_server = array(
	    'host'     => 'redis',
	    'port'     => 6379,
	    'database' => 15
	);

	$this->redis = new Predis\Client($single_server);

    }

    public function setPersistentVariable($name, $data){
        $this->redis->set( $name, $data);
    }


    public function setVariable($name, $data, $expire=300){
      $this->redis->set( $name, $data);
      $this->redis->expireat($name, time() + $expire);
      $ttl = $this->redis->ttl($name);
    }


    public function getVariable($name){
      $data = $this->redis->get($name); 
      return $data;
    }

    public function incrby($name, $inc){
      $this->redis->incrby($name, $inc);
    }

    public function incr($name){
      $this->redis->incr($name);
    }

    public function decrby($name, $dec){
      $this->redis->decrby($name, $dec);
    }

}

?>
