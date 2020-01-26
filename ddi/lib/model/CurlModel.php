<?php


class CurlModel extends Model {

    private $ch;

    public function __construct($url) {
	
	$this->ch = curl_init($url);
    }

    // This function receives an array with the fields
    public function post($options){

	$fields = http_build_query($options);
	curl_setopt($this->ch, CURLOPT_POST, 1);
	curl_setopt($this->ch, CURLOPT_POSTFIELDS, $fields);
	curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
	return curl_exec($this->ch);
	
    }

}

?>
