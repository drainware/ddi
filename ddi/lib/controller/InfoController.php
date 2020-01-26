<?
class InfoController extends Controller
{

  public function __construct($name = null){
    parent::__construct($name);
  } 
  
  protected function showAction(){
    $model = new InfoModel();
    $options = array(
      "conf" => $configuration,
      "users" => $users,
      "groups" => $groups
    );
    $this->options = array_merge($this->options, $options);
  } 
}
?>
