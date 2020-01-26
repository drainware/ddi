<?

class UserController extends Controller {

    public function __construct($name = null) {
        parent::__construct($name);
    }

    protected function showAction() {

        $main_model = new MainModel();
        $ddi_mode = $main_model->getConfValue("ddisbmode");

        $cloud_model = new CloudModel();
        $auth = $cloud_model->getClientUserAuth();

        $group_model = new GroupModel();
        $groups = $group_model->getGroups();

        $user_model = new UserModel();
        $users = $user_model->getUsers();
        $nro_users = $users->count();
        
        $edit_users = array();
        foreach ($users as $uid => $user) {
            foreach ($groups as $gid => $group) {
                if(in_array($gid, $user['group'])){
                    $user['groups'][$gid]['checked'] = true;
                } else{
                    $user['groups'][$gid]['checked'] = false;
                }
                $user['groups'][$gid]['name'] = $group['name'];
            }
            $edit_users[$uid] = $user;  
        }
        
        $options = array(
            'ddi_mode' => $ddi_mode,
            'auth' => $auth,
            'groups' => $groups,
            'users' => $edit_users,
            'nro_users' => $nro_users
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function createAction() {

        $user_model = new UserModel();

        if (isset($_POST['name']) && isset($_POST['group']) && isset($_POST['passwd'])) {

            if ($_POST['passwd'] == $_POST['passwd2']) {

                $out = $user_model->createUser($_POST['name'], $_POST['passwd'], $_POST['group']);

                if ($out) {
                    $this->state['error'] = false;
                    $this->state['messages'][] = "Success";
                } else {
                    $this->state['error'] = true;
                    $this->state['messages'][] = "User already exists";
                }
            } else {
                $this->state['error'] = true;
                $this->state['messages'][] = "Passwords given are different";
            }
        } else {
            $this->state['error'] = true;
            $this->state['messages'][] = "You must fill all the fields";
        }

        $options = array(
            'return_to' => array(
                'module' => 'user',
                'action' => 'show'
            ),
            'state' => $this->state
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function modifyAction() {

        $group_model = new GroupModel();
        
        $user_model = new UserModel();
        $user_object = $user_model->getUserById($_POST['id']);

        if(empty($_POST['groups'])){
            $default_group = $group_model->getDefaultGroup();
            $new_groups = array((string) $default_group['_id']);
        }else{
            $new_groups = $_POST['groups'];
        }
        $old_groups = $user_object['group'];
        
        $remove_groups = array_diff($old_groups, $new_groups);
        
        $user_object['group'] = $new_groups;
        $user_model->saveUserObject($user_object);

        $amqp_model = new AMQPModel();
        $amqp_model->setExchange('server');

        foreach ($remove_groups as $gid) {
            $group_object = $group_model->getGroup($gid);

            $remove_message = array();
            $remove_message['group'] = isset($_SESSION['license']) ? $_SESSION['license'] . '_' . $group_object['name'] : $group_object['name'];
            $remove_message['user'] = array ($user_object['name']);
            $amqp_model->setRoutingKey($group_object['name']);
            $amqp_model->setMessageModule('user');
            $amqp_model->setMessageCommand('delete');
            $amqp_model->setMessageArgs($remove_message);
            $amqp_model->sendMessage();
        }

        foreach ($new_groups as $gid) {
            if (!in_array($gid, $old_groups)) {
                $group_object = $group_model->getGroup($gid);

                $add_message = array();
                $add_message['group'] = isset($_SESSION['license']) ? $_SESSION['license'] . '_' . $group_object['name'] : $group_object['name'];
                $add_message['users'] = array($user_object['name']);
                $amqp_model->setRoutingKey('*');
                $amqp_model->setMessageModule('user');
                $amqp_model->setMessageCommand('add');
                $amqp_model->setMessageArgs($add_message);
                $amqp_model->sendMessage();

                $update_message = ApiController::getDLPGroupConfig($_SESSION['license'], $group_object['name']);
                $amqp_model->setRoutingKey($group_object['name']);
                $amqp_model->setMessageModule('user');
                $amqp_model->setMessageCommand('update');
                $amqp_model->setMessageArgs($update_message);
                $amqp_model->setMessageDelay(5);
                $amqp_model->sendMessage();
            }
        }
    }

    protected function removeAction() {

        if (isset($_GET['id'])) {
            $model = new UserModel();

            $out = $model->removeUser($_GET['id']);

            if ($out) {
                $this->state['error'] = false;
                $this->state['messages'][] = "Success";
            } else {
                $this->state['error'] = true;
                $this->state['messages'][] = "The user could not be modified";
            }
        } else {
            $this->state['error'] = true;
            $this->state['messages'][] = "You must choose one user";
        }

        $options = array(
            'return_to' => array(
                'module' => 'user',
                'action' => 'show'
            ),
            'state' => $this->state
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function change_passwdAction() {

        if (isset($_POST['passwd']) && isset($_POST['passwd2']) && isset($_POST['user'])) {
            if ($_POST['passwd'] == $_POST['passwd2']) {
                $user_model = new UserModel();

                $out = $user_model->updateUserPassword($_POST['user'], $_POST['passwd']);

                if ($out) {
                    $this->state['error'] = false;
                    $this->state['messages'][] = "Success";
                } else {
                    $this->state['error'] = true;
                    $this->state['messages'][] = "The user password could not be changed";
                }
            } else {
                $this->state['error'] = true;
                $this->state['messages'][] = "Passwords given are different";
            }
        } else {
            $this->state['error'] = true;
            $this->state['messages'][] = "You must choose one user";
        }

        $options = array(
            'return_to' => array(
                'module' => 'user',
                'action' => 'show'
            ),
            'state' => $this->state
        );

        $this->options = array_merge($this->options, $options);
    }
    
    public static function updateLDAPGroups(){
        $cloud_model = new CloudModel();
        if ($cloud_model->getClientUserAuth() == "ldap") {
            syslog(LOG_DEBUG, '########## User Local to LDAP ########');
            $user_model = new UserModel();
            $users = $user_model->getUsers();

            $group_model = new GroupModel();
            $dgroup = $group_model->getDefaultGroup();

            $ldap_model = new LdapModel();
            foreach ($users as $uid => $user) {
                $groups_path = $ldap_model->getGroupsOfUser($user['name']);
                $old_groups = $user['group'];

                $new_groups = array();
                foreach ($groups_path as $gpath) {
                    $group_object = $group_model->getGroupByPath($gpath);
                    if (isset($group_object['name'])) {
                        $new_groups[] = (string) $group_object['_id'];
                    }
                }

                if (empty($new_groups)) {
                    $new_groups[] = (string) $dgroup['_id'];
                }

                $new_data = array(
                    'group' => $new_groups
                );

                $user_model->updateUser($uid, $new_data);

                $remove_groups = array_diff($old_groups, $new_groups);

                $amqp_model = new AMQPModel();
                $amqp_model->setExchange('server');

                foreach ($remove_groups as $gid) {
                    $group_object = $group_model->getGroup($gid);

                    $remove_message = array();
                    $remove_message['group'] = isset($_SESSION['license']) ? $_SESSION['license'] . '_' . $group_object['name'] : $group_object['name'];
                    $remove_message['user'] = array($user['name']);
                    $amqp_model->setRoutingKey($group_object['name']);
                    $amqp_model->setMessageModule('user');
                    $amqp_model->setMessageCommand('delete');
                    $amqp_model->setMessageArgs($remove_message);
                    $amqp_model->sendMessage();
                }

                foreach ($new_groups as $gid) {
                    if (!in_array($gid, $old_groups)) {
                        $group_object = $group_model->getGroup($gid);

                        $add_message = array();
                        $add_message['group'] = isset($_SESSION['license']) ? $_SESSION['license'] . '_' . $group_object['name'] : $group_object['name'];
                        $add_message['users'] = array($user['name']);
                        $amqp_model->setRoutingKey('*');
                        $amqp_model->setMessageModule('user');
                        $amqp_model->setMessageCommand('add');
                        $amqp_model->setMessageArgs($add_message);
                        $amqp_model->sendMessage();

                        $update_message = ApiController::getDLPGroupConfig($_SESSION['license'], $group_object['name']);
                        $amqp_model->setRoutingKey($group_object['name']);
                        $amqp_model->setMessageModule('user');
                        $amqp_model->setMessageCommand('update');
                        $amqp_model->setMessageArgs($update_message);
                        $amqp_model->setMessageDelay(5);
                        $amqp_model->sendMessage();
                    }
                }
            }
            syslog(LOG_DEBUG, '########## End User Local to LDAP ########');
        }
    }
            

}

?>