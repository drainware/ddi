<?php
class ListModel {

private $file;
private static $white = '#listcategory: "Lista Blanca General"';
private static $black = '#listcategory: "Lista Negra General"';
private $urls;
private $type;

    public function __construct($type = null){

      $this->type = $type;


      switch ($this->type) {
          case "white":
              $filename = "generalWhiteList";
              break;
          case "black":
              $filename = "generalBlackList";
              break;
          default:
              $filename = "generalWhiteList";
              break;
      }


      $this->file = $GLOBALS['conf']['prefix']  ."etc/dansguardian/" . $filename ;
      $data = file_get_contents($this->file);
      if($this->type == "white"){
        $this->urls = str_replace('#listcategory: "Lista Blanca General"', "", $data);
      }else{
        $this->urls = str_replace('#listcategory: "Lista Negra General"', "", $data);
      }


    }

    public function getAll(){
        //echo "<h1>lista $this->type -> $this->urls</h1>";
        return $this->urls;
    }

    public function modify($data){
        $this->urls = $data;
    }

    public function save(){

    $file = $GLOBALS['conf']['prefix'] . "etc/dansguardian/" . $filename;
    $data = "";
    if($this->type == "white"){
      $data = '#listcategory: "Lista Blanca General"';
    }else{
      $data = '#listcategory: "Lista Negra General"';
    }

    $urlListSeparators=", \n\t";
    $tmplist="";

    $tok = strtok($this->urls, $urlListSeparators);

    while ($tok !== false) {
        $tmplist.=($this->trimURL($tok)."\n");
        $tok = strtok($urlListSeparators);
    }

    $this->urls = $tmplist;

    if (substr($this->urls, strlen($this->urls), 1 != "\n"))
        $this->urls.="\n";

    file_put_contents($this->file, $data . "\n" .$this->urls);

    $ret['message'] = "Success";
    $ret['error'] = false;

    return $ret;
  }

    private function trimURL($urlToTrim){
        $trimStarts[]="https://www.";
        $trimStarts[]="http://www.";
        $trimStarts[]="https://";
        $trimStarts[]="http://";
        $trimStarts[]="www.";
        foreach ($trimStarts as $trimString)
        {
            if (strtolower(substr($urlToTrim, 0, strlen($trimString)))
                    == $trimString)
                $urlToTrim = substr($urlToTrim, strlen($trimString),
                    strlen($urlToTrim) - strlen($trimString));
        }
        return $urlToTrim;
    }

}

?>
