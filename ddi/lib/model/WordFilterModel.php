<?php

class WordFilterModel extends Model {

    private $concidence;
    
    public function __construct(){
        $this->coincidence = '';
    }
    
    private function onlyChars($s) {
        $result = preg_replace("/[^a-zA-Z0-9áéíóúñ ]+/", "", $s);
        return $result;
    }

    public function configureDescription($element, $key, $options) {
        $where_apply = $options['where_apply'];

        if ($key == 'coincidence') {
            $this->concidence = $element;
        }
        
        if ($key == $where_apply) {
            //$element = $this->onlyChars($element);

	    // Convert '<', '>' and other chars which are in conflict with html
	    $element = htmlentities($element);

            $replace_term = "<br />";
            $element = preg_replace("/\r\n/", $replace_term, $element);            
            $element = preg_replace("/\n/", $replace_term, $element);

            $replace_term = "&nbsp;&nbsp;&nbsp;&nbsp;";
            $element = preg_replace("/\t/", $replace_term, $element);

            $term = "/" . $this->concidence . "/";
            $replace_term = "<b>" . $this->concidence . "</b>";
            $element = preg_replace($term, $replace_term, $element);
            //$element = substr($element, 0, 50);
            
        }
    }

}

?>
