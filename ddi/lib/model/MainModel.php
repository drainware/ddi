<?

class MainModel extends Model
{

  private $conf;

  public function __construct(){
    $this->conf = new ConfigTool();   
    $this->conf->setConfigFromFile($GLOBALS['conf']['prefix'] . "/etc/webfilter.conf");
    //print_r($this->conf);

  }

  public function getConfValue($value){
    return $this->conf->get($value);
  }

  
  public function addKey($keyname, $value){
    $this->conf->addKeyValue("$keyname","$value");
  }


  public function deleteKey($keyname){
    $this->conf->deleteKey($keyname);
  }


  public function updateKey($keyname, $value){
      if(is_string($value))
      {
          if($value == "off" || $value == "on"){            
          }else{
              $value = "'" . $value . "'";
          }
      }
      $this->conf->updateKeyValue($keyname, $value);
      //echo "<h1>$keyname = $value</h1>";
      //print_r($this->conf);
  }

  public function save() {
    $this->conf->saveToFile();
  }




}
?>
