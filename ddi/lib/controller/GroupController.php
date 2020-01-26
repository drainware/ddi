<?

class GroupController extends Controller {

    //protected $options;
    public function __construct($name = null) {
        parent::__construct($name);
    }

    protected function showAction() {
        //FIXME: Devolver los grupos correspondientes en modolo local o ldap, no enviar auth
        $main_model = new MainModel();
        $ddi_mode = $main_model->getConfValue('ddisbmode');
        $mode = $main_model->getConfValue('mode');

        if ($mode == 'cloud') {
            $group_model = new GroupModel();
            $groups = $group_model->getGroups();
            $nro_groups = $groups->count();
        } else if ($mode == 'private') {
            
        }

        $nro_max_groups = $GLOBALS['conf']['ddi']['max_groups'] + 1;

        $cloud_model = new CloudModel();
        $user_auth = $cloud_model->getClientUserAuth();

        $policy_model = new PolicyModel();
        $policies = $policy_model->getPolicies($_SESSION['license']);

        $groups_object = array();
        foreach ($groups as $group) {
            foreach ($policies as $policy_id => $policy_object) {
                if (isset($policy_object['groups'][$group['name']])) {
                    $group['policies'][$policy_id]['name'] = $policy_object['name'];
                    $group['policies'][$policy_id]['checked'] = true;
                    $group['policies'][$policy_id]['action'] = $policy_object['groups'][$group['name']]['action'];
                    $group['policies'][$policy_id]['severity'] = $policy_object['groups'][$group['name']]['severity'];
                } else {
                    $group['policies'][$policy_id]['name'] = $policy_object['name'];
                    $group['policies'][$policy_id]['checked'] = false;
                    $group['policies'][$policy_id]['action'] = 'log';
                    $group['policies'][$policy_id]['severity'] = 'action';
                }
            }

            $groups_object[(string) $group['_id']] = $group;
        }

        $options = array(
            'ddi_mode' => $ddi_mode,
            'ddi_auth' => $user_auth,
            'nro_max_groups' => $nro_max_groups,
            'nro_groups' => $nro_groups,
            'groups' => $groups_object,
            'policies' => $policies,
            'nbr_policies' => $policies->count(),
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function createAction() {

        $cloud_model = new CloudModel();
        $client = $cloud_model->getCloudUserByLicense($_SESSION['license']);
        $user_auth = $client['auth'];

        $group_model = new GroupModel();
        if (!$group_model->existGroup($_SESSION['license'], $_POST['name'])) {

            $gpath = $user_auth == "local" ? null : $_POST['path'];

            $response = $group_model->createGroup($user_auth, $_POST['name'], $gpath);

            if($user_auth == "ldap"){
                $ldap_model = new LdapModel();
                $users = $ldap_model->getUsersOfGroup((string)$response);
                
                $dgroup = $group_model->getDefaultGroup();
                $dgid = $dgroup['_id'];
                
                $remove_users = array();
                
                $user_model = new UserModel();
                foreach ($users as $uname) {
                    $user_object = $user_model->getUserByName($uname);
                    
                    if(!in_array($dgid, $user_object['group'])){
                        $user_object['group'][] = (string)$response;
                    } else {
                        $user_object['group'] = array((string)$response);
                        $remove_users[] = $uname;
                    }
                    
                    $user_model->saveUserObject($user_object);
                } 
                
                $amqp_model = new AMQPModel();
                $amqp_model->setExchange('server');
                $amqp_model->setRoutingKey("*");
                $amqp_model->setMessageModule('group');

                $message_object = array();
                
                if(!empty($remove_users)){
                    $message_object['group'] = isset($_SESSION['license']) ? $_SESSION['license'] . '_' . $dgroup['name'] : $dgroup['name'];
                    $message_object['users'] = $remove_users;
                    
                    $amqp_model->setMessageCommand("remove");
                    $amqp_model->setMessageArgs($message_object);
                    $amqp_model->sendMessage();
                }
                
                $message_object['group'] = isset($_SESSION['license']) ? $_SESSION['license'] . '_' . $_POST['name'] : $_POST['name'];
                $message_object['users'] = $users;

                $amqp_model->setMessageCommand("add");
                $amqp_model->setMessageArgs($message_object);
                $amqp_model->sendMessage();
            }
        } else {
            $response = -1;
        }

        $options = array(
            "response" => $response,
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function removeAction() {
        $this->remove($_POST['id']);

        $return_to = Array(
            'module' => 'group',
            'action' => 'show',
        );

        $options = array(
            "return_to" => $return_to,
            "action" => $this->action,
            "state" => $this->state,
        );

        $this->options = array_merge($this->options, $options);
    }

    private function remove($gid) {

        $group_model = new GroupModel();
        $user_model = new UserModel();

        $group_object = $group_model->getGroup($gid);
        $group = $group_object['name'];
        $group_model->removeGroup($gid);
        
        $users = $user_model->getUsersByGroupId($gid);

        $group_message = array();
        $group_message['group'] = isset($_SESSION['license']) ? $_SESSION['license'] . '_' . $group : $group;
        $group_message['user'] = array();

        $amqp_model = new AMQPModel();
        $amqp_model->setExchange('server');
        $amqp_model->setRoutingKey($group);
        $amqp_model->setMessageModule('group');
        $amqp_model->setMessageCommand("delete");
        $amqp_model->setMessageArgs($group_message);
        $amqp_model->sendMessage();

        if (isset($users)) {
            
            $defaul_group = $group_model->getDefaultGroup();
            
            $usernames = array();
            foreach ($users as $user) {
                $user['group'] = array_diff($user['group'], array($gid));
                
                if (empty($user['group'])){
                    $user['group'][] = $defaul_group['_id'];
                    $usernames[] = $user['name'];
                }
                
                $user_model->saveUserObject($user);
            }

            $amqp_model = new AMQPModel();
            $amqp_model->setExchange('server');

            $amqp_model->setRoutingKey('*');
            $amqp_model->setMessageModule('group');
            $amqp_model->setMessageCommand("add");
            $group_message = array();
            $group_message['group'] = isset($_SESSION['license']) ? $_SESSION['license'] . '_' . $defaul_group['name'] : $defaul_group['name'];
            $group_message['users'] = $usernames;
            $amqp_model->setMessageArgs($group_message);
            $amqp_model->sendMessage();

            $amqp_model->setRoutingKey($defaul_group['name']);
            $amqp_model->setMessageModule('group');
            $amqp_model->setMessageCommand("update");
            $group_dlp_config = ApiController::getDLPGroupConfig($_SESSION['license'], $defaul_group['name']);
            $amqp_model->setMessageArgs($group_dlp_config);
            $amqp_model->setMessageDelay(5);
            $amqp_model->sendMessage();
        }

        $policy_model = new PolicyModel();
        $policies = $policy_model->getPoliciesByGroup($_SESSION['license'], $group);

        $groups = array();
        if ($policies->count() != 0) {
            foreach ($policies as $policy) {
                unset($policy['groups'][$group]);
                $policy_model->updatePolicy($policy);
                foreach ($policy["groups"] as $igroup) {
                    $groups[] = $igroup['group_id'];
                }
            }

            foreach (array_unique($groups) as $group) {
                $group_dlp_config = ApiController::getDLPGroupConfig($_SESSION['license'], $group);
                $amqp_model = new AMQPModel();
                $amqp_model->setExchange('server');
                $amqp_model->setRoutingKey($group);
                $amqp_model->setMessageModule('group');
                $amqp_model->setMessageCommand("update");
                $amqp_model->setMessageArgs($group_dlp_config);
                $amqp_model->sendMessage();
            }
        }
    }

    protected function importLocalToLDAPAction() {
        syslog(LOG_DEBUG, '########## Import Local to LDAP ########');
        $group_model = new GroupModel();
        
        $local_groups = empty($_POST['local_groups']) ? array() : $_POST['local_groups'];
        
        foreach ($local_groups as $gid) {
            $data = array(
                'type' => 'ldap',
            );
            $group_model->updateGroup($gid, $data);
        }

        foreach (array_diff($_POST['groups'], $local_groups) as $gid) {
            $this->remove($gid);
        }
        syslog(LOG_DEBUG, '########## End Import Local to LDAP ########');
    }

    protected function updatePathAction() {

        $group_model = new GroupModel();

        $data = array(
            'path' => $_POST['path'],
        );

        $group_model->updateGroup($_POST['id'], $data);
        
        UserController::updateLDAPGroups();
    }

    protected function updatePoliciesAction() {

        $policy_model = new PolicyModel();
        $policies = $policy_model->getPolicies($_SESSION['license']);

        foreach ($policies as $policy_id => $policy_object) {

            $groups = $policy_object['groups'];

            if (in_array($policy_id, $_POST['policies'])) {
                $new_group_object = array();
                $new_group_object['group_id'] = $_POST['group'];
                $new_group_object['action'] = $_POST['actions'][$policy_id];
                $new_group_object['severity'] = $_POST['severities'][$policy_id];
                $groups[$_POST['group']] = $new_group_object;
            } else {
                unset($groups[$_POST['group']]);
            }

            $policy_model->updateGroups($policy_id, $groups);
        }

        $group_model = new GroupModel();
        $now = new MongoDate();
        $created_time = $group_model->getCreatedTime($_POST['group']);
        $last_seconds = 300 - ($now->sec - $created_time->sec);
        if ($last_seconds < 0) {
            $last_seconds = 0;
        }

        $group_dlp_config = ApiController::getDLPGroupConfig($_SESSION['license'], $_POST['group']);

        $amqp_model = new AMQPModel();
        $amqp_model->setExchange('server');
        $amqp_model->setRoutingKey($_POST['group']);
        $amqp_model->setMessageModule('group');
        $amqp_model->setMessageCommand("update");
        $amqp_model->setMessageArgs($group_dlp_config);
        $amqp_model->setMessageDelay($last_seconds);
        $amqp_model->sendMessage();

        header("location: ?module=group");
    }

    protected function getGroupsAction() {

        $ldap_model = new LdapModel();
        $groups = json_encode($ldap_model->getGroups());

        $options = array(
            "groups_json" => $groups,
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function detailAction() {

        $name = $_GET['name'];

        $group_model = new GroupModel();
        $categories = $group_model->getCategories($name);
        $extensions = $group_model->getExtensions($name);
        $black_list = $group_model->getBlackList($name);
        $white_list = $group_model->getWhiteList($name);
        $ldap_path = $group_model->getLdapPath($name);

        $options = array(
            "auth" => $GLOBALS['conf']['ddi']['configuration']['authentication']['value'],
            "group_categories" => $categories,
            "group_extensions" => $extensions,
            "white_list" => implode("\n", $white_list),
            "black_list" => implode("\n", $black_list),
            "group" => $group_model,
            "group_name" => $name,
            "ldap_path" => $ldap_path,
        );


        $this->options = array_merge($this->options, $options);
    }

    protected function modifyAction() {

        $group_model = new GroupModel();
        $name = $_POST['name'];
        $path = $_POST['path'];

        if (isset($path))
            $group_model->chageLdapPath($name, $path);

        if ($name == "default") {
            $return_to = Array('module' => 'main', 'action' => 'show');
        } else {
            $return_to = Array('module' => 'group', 'action' => 'detail', 'name' => $name);
        }

        if (isset($_POST['ddicat_thisisahack'])) {
            if ($_POST['blocked_categories']) {
                $categories = $_POST['blocked_categories'];
            } else {
                $categories = array();
            }
            $group_model = new GroupModel();
            $group_model->setCategories($name, $categories);
        }

        if (isset($_POST['ddiext_thisisahack'])) {
            if ($_POST['blocked_extensions']) {
                $extensions = $_POST['blocked_extensions'];
            } else {
                $extensions = array();
            }
            $group_model = new GroupModel();
            $group_model->setExtensions($name, $extensions);
        }

        if (isset($_POST['white_list'])) {
            $white_list = $_POST['white_list'];
            $group_model = new GroupModel();
            $group_model->setWhiteList($name, preg_split('/\n/', $white_list));
        }

        if (isset($_POST['black_list'])) {
            $black_list = $_POST['black_list'];
            $group_model = new GroupModel();
            $group_model->setBlackList($name, preg_split('/\n/', $black_list));
        }

        $options = array(
            "return_to" => $return_to,
            "state" => $this->state
        );

        $this->options = array_merge($this->options, $options);

        $com_model = new CommunicationModel();
        if ($name != "default") {
            $com_model->sendUpdateUsersWF($name);
        } else {
            $com_model->sendUpdateDefaultGroupWF();
        }
    }

}

?>
