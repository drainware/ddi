<?php

class RouteModel extends Model
{


  private $conf_file;


  public function __construct(){
    //FIXME: this tag should be part of configuration files
    $this->conf_file = "/etc/network/interfaces";
  }


  public function newRoute($route) {
  $route = "up route add -net " . $route['network'] . "/" . $route['mask'] . " gw " . $route['gateway'] . " dev br0\n";
  $handle = fopen($this->conf_file, 'a') or die("can't open file");
  fwrite($handle, $route);
  fclose($handle);
  exec("/etc/init.d/networking restart &");
  }


  public function deleteRoute($route) {

  //reading file
  $route = "up route add -net " . $route['network'] . "/" . $route['mask'] . " gw " . $route['gateway'] . " dev br0\n";
  $handle = @fopen($this->conf_file, "r");
  $contents = fread($handle, filesize($this->conf_file));
  fclose($handle);
 
  //writting file
  $handle = fopen($this->conf_file, 'w') or die("can't open file");
  $contents = str_replace($route,'',$contents);
  fwrite($handle, $contents);
  fclose($handle);
  exec("/etc/init.d/networking restart &");
  }


  public function getRoutes() {
  
  $routes = array();
  $pattern = "/up route add -net (.+)\/(.+) gw (.+) dev br0/";


  //reading the file

   $handle = @fopen($this->conf_file, "r");
   $contents = fread($handle, filesize($this->conf_file));
   
  //reading info
   $routes = array();
   preg_match_all($pattern,$contents,$matches,PREG_PATTERN_ORDER);
   for ($i=0;$i<count($matches[1]);$i=$i=$i+1) {
   
       $route['network'] = explode(".",$matches[1][$i]);
       $route['mask'] = $matches[2][$i];
       $route['gateway'] = explode(".",$matches[3][$i]);
       $routes[] = $route;

   }


  return $routes;

  }


}
?>

