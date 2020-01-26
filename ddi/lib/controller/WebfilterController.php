<?

class WebfilterController extends Controller {

    //protected $options;
    public function __construct($name = null) {
        parent::__construct($name);
    }

    protected function getGroupsAction() {
        $auth = $GLOBALS['conf']['ddi']['configuration']['authentication']['value'];

        if ($auth == "ldap") {

            $ldap = new LdapModel();
            $options = array(
                "groups_json" => json_encode($ldap->getGroups())
            );

            $this->options = array_merge($this->options, $options);
        }
    }

    protected function showAction() {
        $main_model = new MainModel();
        $sbmode = $main_model->getConfValue("ddisbmode");

        $group_model = new GroupModel();
        if ($sbmode == "unique") {
            $groups[]= $group_model->getDefaultGroup();
        } else {
            $groups_array = $group_model->getGroups();
            foreach ($groups_array as $group) {
                $groups[] = $group;
            }
        }

        $options = array(
            "groups" => $groups,
        );


        $this->options = array_merge($this->options, $options);
    }

    protected function createAction() {

        $group = new GroupModel();
        if (!$group->exists($_POST['name'])) {
            $group->setName($_POST['name']);
            $group->setPath($_POST['path']);
            //$group = new GroupModel("warepax");
            $response = 1;
            $group->save();
        } else {
            $response = -1;
        }

        $options = array(
            "response" => $response,
            "gid" => $gid,
            "return_to" => $return_to
        );

        $com_model = new CommunicationModel();
        $com_model->sendUpdateAllUsersWF();
        $com_model->sendUpdateAllUsersDLP();
        $this->options = array_merge($this->options, $options);
    }

    protected function removeAction() {

        $error = false;
        $options = Array();

        $model = new GroupModel();

        //for local auth support
        //we remove all users belonging a removed group
        //FIXME: it would be better move to another group
        $umodel = new UserModel();
        $users = $umodel->getMembersOfGroup();

        foreach ($users as $user) {
            $umodel->removeUser($user);
            $umodel->save();
        }
        $name = $_POST['name'];

        $com_model = new CommunicationModel();
        $com_model->sendUpdateUsersWF($name);
        $com_model->sendUpdateUsersDLP($name);

        $return_to = Array('module' => 'group', 'action' => 'show');
        $model->remove($name);

        $options = array(
            "return_to" => $return_to,
            "action" => $this->action,
            "state" => $this->state
        );


        $this->options = array_merge($this->options, $options);
    }

    protected function detailAction() {


        $gid = $_GET['gid'];
        $name = $_GET['name'];

        $model = new GroupModel();
        $categories = $model->getCategories($name);
        $extensions = $model->getExtensions($name);
        $black_list = $model->getBlackList($name);
        $white_list = $model->getWhiteList($name);
        $ldap_path = $model->getLdapPath($name);

        /*
          foreach($categorias as $categoria){
          $groupname=preg_replace('/_(\w+)/i','',$categoria);
          //echo "<h1>" . $GLOBALS['lang'][preg_replace('/(\w+)_/i','',$categoria)] . " vale " . $gmodel->getConfValue("ddicat_".$categoria) . "</h1>";
          $categories[$groupname][] = array('type'=>'check','value'=>($model->getConfValue("ddicat_".$categoria)=="on") ? 'checked' : '', 'display'=>  $GLOBALS['lang'][preg_replace('/(\w+)_/i','',$categoria)] ,  'property'=>"ddicat_".$categoria);
          }

          $extensions = Array();


          foreach($extensiones as $extension){
          $extensions[] = array('type'=>'check','value'=>($model->getConfValue("ddiext_".$extension)=="on") ? 'checked' : '', 'display'=>  $GLOBALS['lang'][preg_replace('/(\w+)_/i','',$extension)] ,  'property'=>"ddiext_".$extension);
          }


          $configuration = array(
          array('type'=>'string','value' =>$model->getConfValue("groupname"), 'display'=>"Group name", 'property'=>"groupname"),
          array('type'=>'string','value'=>$model->getConfValue("groupdescription"), 'display'=>"Group description", 'property'=>"groupdescription")/*,
          array('type'=>'boolean','value'=>($model->getConfValue("guinddownloadblock")=="on") ? 'checked' : '', 'display'=>"Block downloads", 'property'=>"guinddownloadblock"),
          array('type'=>'boolean','value'=>($model->getConfValue("guindintelliscan")=="on") ? 'checked' : '', 'display'=>"Smart scan", 'property'=>"guindintelliscan"),
          array('type'=>'boolean','value'=>($model->getConfValue("guindavsupport")=="on") ? 'checked' : '', 'display'=>"Antivirus support", 'property'=>"guindavsupport"),
          array('type'=>'boolean','value'=>($model->getConfValue("ddibypassav")=="on") ? 'checked' : '', 'display'=>"Bypass antivirus in files unable to scan", 'property'=>"ddibypassav")
          );
         */

        //$black_list = array("http://www.dd.com", "http://adfadf.com");
        //$white_list = array("http://www.dd.com", "http://adfadf.com");

        $options = array(
            "auth" => $GLOBALS['conf']['ddi']['configuration']['authentication']['value'],
            "group_categories" => $categories,
            "group_extensions" => $extensions,
            "white_list" => implode("\n", $white_list),
            "black_list" => implode("\n", $black_list),
            "conf" => $configuration,
            "group" => $model,
            "gid" => $gid,
            "group_name" => $name,
            "ldap_path" => $ldap_path
        );


        $this->options = array_merge($this->options, $options);
    }

    protected function modifyAction() {

        $main_model = new MainModel();

        $model = new GroupModel();
        $name = $_POST['name'];
        $path = $_POST['path'];


        $auth = $GLOBALS['conf']['ddi']['configuration']['authentication']['value'];


        //if($auth == "ldap" && isset($path))
        if (isset($path))
            $model->chageLdapPath($name, $path);
        //benchmark("before checking name");

        if ($name == "default") {
            //if($device_mode == "unique"){
            $return_to = Array('module' => 'main', 'action' => 'show');
        } else {
            $return_to = Array('module' => 'webfilter', 'action' => 'detail', 'name' => $name);
        }

        //benchmark("after checking name");
        //benchmark("before group model cats");

        if (isset($_POST['ddicat_thisisahack'])) {
            if ($_POST['blocked_categories']) {
                $categories = $_POST['blocked_categories'];
            } else {
                $categories = array();
            }
            $model = new GroupModel();
            $model->setCategories($name, $categories);
        }
        //benchmark("after group model cats");
        //benchmark("before group model exts");
        if (isset($_POST['ddiext_thisisahack'])) {
            if ($_POST['blocked_extensions']) {
                $extensions = $_POST['blocked_extensions'];
            } else {
                $extensions = array();
            }
            $model = new GroupModel();
            $model->setExtensions($name, $extensions);
        }

        //benchmark("after group model exts");
        //benchmark("before group model white");
        if (isset($_POST['white_list'])) {
            $white_list = $_POST['white_list'];
            $model = new GroupModel();
            $model->setWhiteList($name, preg_split('/\n/', $white_list));
        }
        //benchmark("after group model white");
        //benchmark("before group model black");

        if (isset($_POST['black_list'])) {
            $black_list = $_POST['black_list'];
            $model = new GroupModel();
            $model->setBlackList($name, preg_split('/\n/', $black_list));
        }

        //benchmark("after group model black");


        $options = array(
            //"return_to" => Array('module' => 'group', 'action'=> 'detail', 'gid'=> $gid ),
            "return_to" => $return_to,
            "state" => $this->state
        );

        $this->options = array_merge($this->options, $options);

        //benchmark("before group update icap");
        $com_model = new CommunicationModel();
        if ($name != "default") {
            $com_model->sendUpdateUsersWF($name);
        } else {
            $com_model->sendUpdateDefaultGroupWF();
        }
        //benchmark("after group update icap");
    }

}

?>
