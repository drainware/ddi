<?

class GroupCollection extends Collection
{


  private $groups;


  public function __construct(){


  //FIXME: WTF!!
  //$group_path = $GLOBALS['conf']['prefix']  ."etc/ddi/";

    //for($i=1;$i<10;$i++){
      //  $group_file = $group_path . "group-" . $i . ".conf";
        //if(file_exists( $group_file )){
            

   
            //$conf = new ConfigTool();
            //$conf->setConfigFromFile($group_file);

            //$group = new GroupModel();
            //$group->setConf($conf);
            //$this->groups[$i] = $group;
      //  }
    //}


  }


  public function getGroups()
  {
    return $this->groups;
  }

  public function getGroup($id){
      return $this->groups[$id];
  }



}
?>
