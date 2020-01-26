<?php

class MainController extends Controller {

    public function __construct($name = null) {
        parent::__construct($name);
    }

    /*
     * Depending on the passed action it runs a method.
     * It must to recieve the showAction method at least.
     */

    protected function logoutAction() {

        $main_model = new MainModel();
        $mode =  $main_model->getConfValue('mode');

        $_SESSION['authorized'] = 0;
        session_destroy();

        $options = array(
            "mode" => $mode,
            "software_version" => $GLOBALS['conf']['version'],
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function rebootAction() {
        system('/opt/drainware/scripts/ddi/reboot.py &');
    }

    public function registerAction() {

        if (isset($_POST['register'])) {

            // Are all the fields filled?

            $u = $_POST['email'];
            $p = $_POST['passwd'];
            $p2 = $_POST['passwd2'];
            $lic = $_POST['lic'];

            // Is license correct? Lookup in mongodb for the license number

            $mongo_model = new MongoModel();
            $m = $mongo_model->connect();
            $db = $m->ddi;
            $col = $db->licenses;
            $license_info = $col->findOne(array('lic' => $lic));


            if (!isset($license_info[lic])) {
                echo "La licencia no es valida";
                return;
            }

            // Is the license already used (has an email associated)
            if (isset($license_info[mail])) {
                echo "La licencia ya se ha utilizado";
                return;
            }


            // Is the license expired?
            //if(isset($license_info[ends])){
            //  echo "La licencia ya se habia dado de alta";
            //  return;
            //}

            $expires = date('Y-m-d H:i:s', strtotime('+1 year'));
            $end = new MongoDate(strtotime($expires));
            $lic_data = array('lic' => $lic, 'ends' => $end, 'email' => $u);
            $col->update(array("lic" => $lic), $lic_data);


            // Are all the fields valid?

            $passwdmodel = new PasswdAuth('', '', $GLOBALS['conf']['prefix'] . "/etc/ddi/passwd");

            if ($passwdmodel->checkUser($u)) {
                echo "User Already in db";
                return;
            }

            if ($p != $p2) {
                echo "Las claves no coinciden";
                return;
            } else {
                $passwdmodel->addUser($u, $p);
            }
            $group = new GroupModel();
            if (!$group->exists($u)) {
                $group->setName($u);
                $response = 1;
                $group->save();
            } else {
                echo "El grupo para ese usuario ya existe";
                $response = -1;
                rerturn;
            }


            echo "Se ha registrado correctamente";
        } else {

            $lic = $_GET['lic'];
            $options = array(
                "lic" => $lic
            );

            $this->options = array_merge($this->options, $options);
        }
    }

    protected function loginAction() {

        $passwdmodel = new PasswdAuth('', '', $GLOBALS['conf']['prefix'] . "/etc/ddi/passwd");

        $lang = $GLOBALS['conf']['ddi']['configuration']['language']['value'];

        if (!empty($_POST)) {

            $redirect_to = $_POST['redirect_to'];

            if (isset($_POST['name']) && isset($_POST['passwd'])) {
                $_SESSION['license'] = null;

                $main_model = new MainModel();
                $mode = $main_model->getConfValue('mode');

                switch ($mode) {
                    case 'cloud':
                        $cloud_model = new CloudModel();
                        $resp = $cloud_model->loginCloudUser($_POST['name'], $_POST['passwd']);
                        switch ($resp) {
                            case 0:
                                $_SESSION['authorized'] = 1;

                                $user_cloud = $cloud_model->getCloudUserByEmail($_POST['name']);

                                $_SESSION['username'] = $user_cloud['company'];
                                $_SESSION['license'] = $user_cloud['license'];

                                if ($_POST['language'] != $lang) {
                                    $_SESSION['language'] = $_POST['language'];
                                }

                                if (strstr($redirect_to, "action=login")) {
                                    $redirect_to = "/ddi/";
                                }

                                header("location:" . $redirect_to);

                                break;
                            case 1:
                                $msg = "User and/or password are not corrects";
                                break;
                            case 2:
                                $msg = "E-mail not activated";
                                break;
                            case -1:
                                $msg = "User and/or password are not corrects";
                                break;
                            default:
                                break;
                        }

                        break;

                    default:
                        if ($passwdmodel->checkPasswd($_POST['name'], $_POST['passwd'])) {

                            $_SESSION['authorized'] = 1;
                            $_SESSION['username'] = $_POST['name'];
                            if ($_POST['language'] != $lang) {
                                $_SESSION['language'] = $_POST['language'];
                            }

                            if (strstr($redirect_to, "action=login")) {
                                $redirect_to = "/ddi/";
                            }

                            header("location:" . $redirect_to);
                        } else {
                            $msg = "User and/or password are not corrects";
                        }
                        break;
                }
            } else {
                $msg = "User and/or password are not corrects";
            }
        } else {
            //firsttime
        }

        $languages_model = new LangModel();
        $languages = $languages_model->getLanguages();

        $redirect_to = $_SERVER['REQUEST_URI'];
        if (isset($_POST['redirect_to'])){
            $redirect_to = $_POST['redirect_to'];
        }

        $options = array(
            "msg" => $msg,
            "software_version" => $GLOBALS['conf']['version'],
            "languages" => $languages,
            "lang" => $lang,
            "redirect_to" => $redirect_to
        );


        $this->options = array_merge($this->options, $options);
    }

    protected function showAction() {

        $main_model = new MainModel();
        $mode =  $main_model->getConfValue('mode');

        $wfstatus = $main_model->getConfValue('ddiwebfilter');
        $avstatus = $main_model->getConfValue('ddiav');
        $advstatus = $main_model->getConfValue('ddiadv');
        $dlpstatus = $main_model->getConfValue('ddidlp');
        $pshstatus = $main_model->getConfValue('ddipsh');
        $mlwstatus = $main_model->getConfValue('ddimlw');

        $active_module['webfilter'] = in_array('webfilter', $GLOBALS['conf']['ddi']['modules']);
        $active_module['dlp'] = in_array('dlp', $GLOBALS['conf']['ddi']['modules']);
        $active_module['atp'] = in_array('atp', $GLOBALS['conf']['ddi']['modules']);
        $active_module['forensics'] = in_array('forensics', $GLOBALS['conf']['ddi']['modules']);

        $event_date = array();
        $event_date['first'] = date('Y.m.d', strtotime('first day of this month'));
        $event_date['last'] = date('Y.m.d', strtotime('last day of this month'));

        $options = array(
            'software_version' => $GLOBALS['conf']['version'],
            'mode' => $mode,
            'license'=> $_SESSION['license'],
            'active_module' => $active_module,
            'wfstatus' => $wfstatus,
            'avstatus' => $avstatus,
            'advstatus' => $advstatus,
            'dlpstatus' => $dlpstatus,
            'pshstatus' => $pshstatus,
            'mlwstatus' => $mlwstatus,
            'event_date' => $event_date
        );
        $this->options = array_merge($this->options, $options);
    }

    protected function showModulesAction() {

        $main_model = new MainModel();
        $mode =  $main_model->getConfValue('mode');

        $wfstatus = $main_model->getConfValue("ddiwebfilter");
        $avstatus = $main_model->getConfValue("ddiav");
        $advstatus = $main_model->getConfValue("ddiadv");
        $dlpstatus = $main_model->getConfValue("ddidlp");
        $pshstatus = $main_model->getConfValue("ddipsh");
        $mlwstatus = $main_model->getConfValue("ddimlw");

        $options = array(
            "mode" => $mode,
            "software_version" => $GLOBALS['conf']['version'],
            "wfstatus" => $wfstatus,
            "avstatus" => $avstatus,
            "advstatus" => $advstatus,
            "dlpstatus" => $dlpstatus,
            "pshstatus" => $pshstatus,
            "mlwstatus" => $mlwstatus
        );
        $this->options = array_merge($this->options, $options);
    }

    protected function showTypeFilterAction() {

        $main_model = new MainModel();
        $mode =  $main_model->getConfValue('mode');
        $sbmode = $main_model->getConfValue("ddisbmode");

        $options = array(
            "mode" => $mode,
            "software_version" => $GLOBALS['conf']['version'],
            "sbmode" => $sbmode
        );
        $this->options = array_merge($this->options, $options);
    }

    protected function showAdvancedAction() {

        $main_model = new MainModel();
        $mode =  $main_model->getConfValue('mode');

        $config = new ConfigModel();
        //$config_dev = print_r($config->getConfig(),true);
        //$config_dev = $config->renderForm();
        $config_dev = $config->renderAdvancedConfigForm();

        $options = array(
            "mode" => $mode,
            "software_version" => $GLOBALS['conf']['version'],
            "config_dev" => $config_dev,
        );
        $this->options = array_merge($this->options, $options);
    }

    protected function showLicenseAction() {

        $main_model = new MainModel();
        $mode =  $main_model->getConfValue('mode');

        $license = array();
        $license['registered'] = $GLOBALS['conf']['ddi']['registered'];
        $license['licensed_to'] = $GLOBALS['conf']['ddi']['licensed_to'];
        $license['modules']['webfilter'] = in_array('webfilter', $GLOBALS['conf']['ddi']['modules']);
        $license['modules']['dlp'] = in_array('dlp', $GLOBALS['conf']['ddi']['modules']);
        $license['modules']['atp'] = in_array('atp', $GLOBALS['conf']['ddi']['modules']);
        $license['modules']['forensics'] = in_array('forensics', $GLOBALS['conf']['ddi']['modules']);
        $license['max_groups'] = $GLOBALS['conf']['ddi']['max_groups'];
        $license['expiry'] = date("F j, Y, g:i a", $GLOBALS['conf']['ddi']['expiry']);

        $options = array(
            "mode" => $mode,
            "software_version" => $GLOBALS['conf']['version'],
            "license" => $license,
            "hostname" => $_SERVER['SERVER_NAME']
        );
        $this->options = array_merge($this->options, $options);
    }

    protected function showInviteFriendAction() {
        $main_model = new MainModel();
        $mode =  $main_model->getConfValue('mode');

        $cloud_model = new CloudModel();
        $cloud_user = $cloud_model->getCloudUser();
        $referral_url = $cloud_user['referral_url'];

        $options = array(
            'mode' => $mode,
            'software_version' => $GLOBALS['conf']['version'],
            'referral_url' =>$referral_url,
        );
        $this->options = array_merge($this->options, $options);
    }

    protected function showCredentialsAction() {

        $main_model = new MainModel();
        $mode =  $main_model->getConfValue('mode');

        if ($mode != 'cloud') {
            $user = 'admin';
        } else {
            $cloud_model = new CloudModel();
            $user_object = $cloud_model->getCloudUser();
            $user = $user_object['email'];
        }

        $options = array(
            'mode' => $mode,
            'software_version' => $GLOBALS['conf']['version'],
            'user' => $user,
        );
        $this->options = array_merge($this->options, $options);
    }

    protected function showCloudConfigAction() {
        $main_model = new MainModel();
        $mode =  $main_model->getConfValue('mode');

        $cloud_model = new CloudModel();
        $client = $cloud_model->getCloudUser();

        $client['expiry'] = date('Y-m-j',$client['expiry']->sec);

        $options = array(
            "mode" => $mode,
            "software_version" => $GLOBALS['conf']['version'],
            "client" => $client
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function showWireTransferAction(){
        $main_model = new MainModel();
        $mode =  $main_model->getConfValue('mode');

        $options = array(
            "mode" => $mode,
            "software_version" => $GLOBALS['conf']['version'],
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function showUserAuthAction(){
        $main_model = new MainModel();
        $mode =  $main_model->getConfValue('mode');

        $cloud_model = new CloudModel();
        $client = $cloud_model->getCloudUserByLicense($_SESSION['license']);

        $group_model = new GroupModel();
        $local_groups = $group_model->getLocalGroups();

        $options = array(
            "mode" => $mode,
            "software_version" => $GLOBALS['conf']['version'],
            "user_auth" => $client['auth'],
            "ldap_conf" => $client['ldap'],
            "local_groups" => $local_groups,
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function showNotificationsAction(){
        $main_model = new MainModel();
        $mode =  $main_model->getConfValue('mode');

        $cloud_model = new CloudModel();
        $client = $cloud_model->getCloudUser();

        $options = array(
            "mode" => $mode,
            "software_version" => $GLOBALS['conf']['version'],
            "notifications" => $client['notifications']
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function showTimeZoneAction(){
        $main_model = new MainModel();
        $mode =  $main_model->getConfValue('mode');

        $cloud_model = new CloudModel();
        $client = $cloud_model->getCloudUserByLicense($_SESSION['license']);

        $tz = $client['timezone'];
        $tz_list = DateTimeZone::listIdentifiers();
        foreach ($tz_list as $key => $value) {
            $tz_list[$key] = array(
                'text' => preg_replace('/_/', ' ', $value),
                'value' => $value
            );
        }

        $options = array(
            "mode" => $mode,
            "software_version" => $GLOBALS['conf']['version'],
            'utz' => $tz,
            'tz_list' => $tz_list
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function saveCloudConfigAction() {

    }

    protected function saveWireTransferAction() {
        if(sha1($_POST['password']) == 'ee66bcf34d165f171074ed60e54ad99888978601') {

            if(!isset($_POST['extra_users'])){
                $data = array(
                    'type' => $_POST['type'],
                    'nbr_users' => (int) $_POST['nbr_users'],
                    'cost_user_per_month' => sprintf('%01.2f', $_POST['cost_user_month']),
                    'period' => ApiController::getMonths(date('Y-m-d'), $_POST['expiry']),
                    'events.availability' => true,
                    'events.screenshot' => true,
                    //'total_amount' => sprintf('%01.2f', $_POST['total_amount']),
                    'expiry' => new MongoDate(strtotime($_POST['expiry']))
                );
            } else{
                 $data = array(
                     'nbr_users' => (int)$_POST['nbr_users'] +  (int)$_POST['extra_users'],
                 );
            }
            
            $cloud_model = new CloudModel();
            $cloud_model->updateCloudUser($_POST['license'], $data);

            $group_model = new GroupModel();
            $groups = $group_model->getGroups($_POST['license']);

            foreach ($groups as $group) {
                $group_dlp_config = ApiController::getDLPGroupConfig($_POST['license'], $group['name']);

                $amqp_model = new AMQPModel();
                $amqp_model->setExchange('server');
                $amqp_model->setRoutingKey($group['name'], $_POST['license']);
                $amqp_model->setMessageModule('dlp');
                $amqp_model->setMessageCommand('update');
                $amqp_model->setMessageArgs($group_dlp_config);
                $amqp_model->sendMessage();
            }

            $atp_config = ApiController::getATPConfig($_POST['license']);

            $amqp_model = new AMQPModel();
            $amqp_model->setExchange('server');
            $amqp_model->setRoutingKey('*');
            $amqp_model->setMessageModule('atp');
            $amqp_model->setMessageCommand('update');
            $amqp_model->setMessageArgs($atp_config);
            $amqp_model->sendMessage();

        }
        header('Location: ?module=main&action=showWireTransfer');
    }

    protected function testLDAPConnectionAction() {
        $code = LdapModel::testConnection($_POST);

        $options = array(
            "code" => $code,
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function saveUserAuthAction() {
        $cloud_model = new CloudModel();

        //FIXME: Validate data.
        $new_data = array();
        $new_data['auth'] = $_POST['auth'];
        if (isset($_POST['ldap'])) {
            $new_data['ldap'] = array(
                'ssl' => isset($_POST['ldap']['ssl']) ? true : false,
                'version' => (int)$_POST['ldap']['version'],
                'host' => $_POST['ldap']['host'],
                'port' => (int)$_POST['ldap']['port'],
                'dn' => $_POST['ldap']['dn'],
                'base' => $_POST['ldap']['base'],
                'username_attr' => $_POST['ldap']['username_attr'],
                'user' => $_POST['ldap']['user'],
                'password' => $_POST['ldap']['password'],
                'recursive_groups' => isset($_POST['ldap']['recursive_groups']) ? true : false,
            );
        }

        $cloud_model->updateCloudUser($_SESSION['license'], $new_data);

        UserController::updateLDAPGroups();

        header('Location: ?module=main&action=showUserAuth');
    }

    protected function saveNotificationsAction() {
        $cloud_model = new CloudModel();

        $new_data = array();
        $new_data['notifications'] = array(
            'status' => $_POST['status'],
            'when' => $_POST['when']
        );

        $cloud_model->updateCloudUser($_SESSION['license'], $new_data);

        header('Location: ?module=main&action=showNotifications');
    }

    protected function saveTimeZoneAction() {
        $cloud_model = new CloudModel();

        $new_data = array();
        $new_data['timezone'] = $_POST['timezone'];

        $cloud_model->updateCloudUser($_SESSION['license'], $new_data);

        header('Location: ?module=main&action=showTimeZone');
    }

    protected function sendInvitationsAction(){
        $email_list = empty($_POST['emails']) ? null : $_POST['emails'];

        $cloud_model = new CloudModel();
        $client = $cloud_model->getCloudUser();

        $subject = $client['email'] . " invites you to check out Drainware";
        $vars = array(
            'email' => $client['email'],
            'referral_url' => $client['referral_url']
        );

        $mail_model = new MailModel();
        $mail_model->setSubject($subject);
        $mail_model->setVars($vars);
        $mail_model->setTemplate('invitationMessage');

        $data = array(
            'guests' => array(),
            'clients' => array()
        );

        foreach ($email_list as $email) {
            $friend = $cloud_model->getCloudUserByEmail($email);
            if(!isset($friend)){
                $data['guests'][] = $email;
                $mail_model->setDest($email);
                $mail_model->sendMail();
            } else{
                $data['clients'][] = $email;
            }
        }

        $options = Array(
            'response' => json_encode($data)
        );

        $this->options = array_merge($this->options, $options);

    }

    protected function editAdvancedAction() {
        $configuration = $_POST;
        $configuration['ldap-base'] = ereg_replace("([ ]+)", "", $configuration['ldap-base']);

        $config = new ConfigModel();
        system('/opt/drainware/scripts/ddi/ldap_icap.py leave > /dev/null &');
        $config->saveAdvancedConfig($configuration);
        system('/opt/drainware/scripts/ddi/ldap_icap.py join > /dev/null &');

        $return_to = Array('module' => 'main', 'action' => 'show');
        $options = Array(
            "return_to" => $return_to
        );
        $this->options = array_merge($this->options, $options);
    }

    protected function uploadLicenseAction() {
        if ($_FILES['license_file']['name'] != "") {
            $extension = end(explode('.', $_FILES['license_file']['name']));
            if ($extension == "lic") {
                $target_path = "/var/www/drainware/" . $_FILES['license_file']['name'];
                if (!move_uploaded_file($_FILES['license_file']['tmp_name'], $target_path)) {
                    echo "There was an error uploading the file, please try again!";
                }
            }
        }
    }

    protected function changeCredentialsAction() {
        if ($_POST['new_passwd'] == $_POST['rep_passwd']) {

            $main_model = new MainModel();
            $mode =  $main_model->getConfValue('mode');

            if ($mode != 'cloud') {
                $passwdmodel = new PasswdAuth('', '', $GLOBALS['conf']['prefix'] . "/etc/ddi/passwd");
                if ($passwdmodel->checkPasswd($_POST['user'], $_POST['old_passwd'])) {
                    $handle = fopen($GLOBALS['conf']['prefix'] . "etc/ddi/passwd", "w");
                    $passwd_line = "admin:" . $_POST['new_passwd'] . "\n";

                    if (fwrite($handle, $passwd_line) === false) {
                        $this->state['error'] = true;
                        $this->state['messages'][] = "Cannot write to password file";
                    }
                    fclose($handle);
                } else {
                    $this->state['error'] = true;
                    $this->state['messages'][] = "Old password is not correct";
                }
            } else {
                $cloud_model = new CloudModel();
                if ($cloud_model->loginCloudUser($_POST['user'], $_POST['old_passwd']) == 0) {

                    $cloud_model->changePassword($_POST['user'], $_POST['new_passwd']);
                } else {
                    $this->state['error'] = true;
                    $this->state['messages'][] = "Old password is not correct";
                }
            }
        } else {
            $this->state['error'] = true;
            $this->state['messages'][] = "The passwords do not match";
        }

        $return_to = Array('module' => 'main', 'action' => 'show');
        $options = array(
            "return_to" => $return_to,
            "action" => $this->action,
            "state" => $this->state
        );
        $this->options = array_merge($this->options, $options);
    }

    protected function modifyAction() {

        $model = new MainModel();
        $currentAction = 'default';

        if (isset($_POST['ddimodules'])) {
            $currentAction = 'ddimodules';
        } elseif (isset($_POST['dditype'])) {
            $currentAction = 'dditype';
        } elseif (isset($_POST['ddiproto_thisisahack'])) {
            $currentAction = 'update_firewall';
        } elseif (isset($_POST['black']) || isset($_POST['white'])) {
            $currentAction = 'update_lists';
        }

        switch ($currentAction) {
            case 'ddimodules':
                $main_model = new MainModel();
                $group_model = new GroupModel();
                /*
                  $groupsname = $group_model->getGroupsNames();
                  foreach ($groupsname as $gname){
                  $categories = $group_model->getCategoriesBlocked($gname);
                  echo "<h1> ".$name.": ".print_r($categories)."</h1>";
                  }
                 */

                if ($_POST['ddiwebfilter'] == 'on') {
                    $output = system('/opt/drainware/scripts/ddi/webfilter.py set on > /dev/null');
                    $main_model->updateKey("ddiwebfilter", $_POST['ddiwebfilter']);
                } else {
                    $output = system('/opt/drainware/scripts/ddi/webfilter.py set off > /dev/null');
                    $main_model->updateKey("ddiwebfilter", $_POST['ddiwebfilter']);
                    //$_POST['ddiav'] = $_POST['ddiwebfilter'];
                }

                if ($_POST['ddidlp'] == 'on') {
                    $output = system('/opt/drainware/scripts/ddi/dlp.py set on > /dev/null');
                    $main_model->updateKey("ddidlp", $_POST['ddidlp']);
                } else {
                    $output = system('/opt/drainware/scripts/ddi/dlp.py set off > /dev/null');
                    $main_model->updateKey("ddidlp", $_POST['ddidlp']);
                }

                if ($_POST['ddiav'] == 'on') {
                    $output = system('/opt/drainware/scripts/ddi/clamav.py set on > /dev/null');
                    $main_model->updateKey("ddiav", $_POST['ddiav']);
                } else {
                    $output = system('/opt/drainware/scripts/ddi/clamav.py set off > /dev/null');
                    $main_model->updateKey("ddiav", $_POST['ddiav']);
                }

                if ($_POST['ddiadv'] == 'on') {
                    $main_model->updateKey("ddiadv", $_POST['ddiadv']);
                } else {
                    $main_model->updateKey("ddiadv", $_POST['ddiadv']);
                }

                if ($_POST['ddipsh'] == 'on') {
                    $main_model->updateKey("ddipsh", $_POST['ddipsh']);
                } else {
                    $main_model->updateKey("ddipsh", $_POST['ddipsh']);
                }

                if ($_POST['ddimlw'] == 'on') {
                    $main_model->updateKey("ddimlw", $_POST['ddimlw']);
                } else {
                    $main_model->updateKey("ddimlw", $_POST['ddimlw']);
                }

                $output = system('/opt/drainware/scripts/ddi/reloadsquid3.py > /dev/null');
                $com_model = new CommunicationModel();
                $com_model->sendUpdateAllUsersWF();
                $com_model->sendUpdateAllUsersDLP();
                $main_model->save();
                break;

            case 'dditype':
                $main_model = new MainModel();
                $main_model->updateKey("ddisbmode", $_POST['ddisbmode']);
                $main_model->save();
                if ($_POST['ddisbmode'] == 'unique') {
                    $output = system('/opt/drainware/scripts/ddi/typefilter.py set unique > /dev/null');
                } else {
                    $output = system('/opt/drainware/scripts/ddi/typefilter.py set groups > /dev/null');
                }
                $com_model = new CommunicationModel();
                $com_model->sendUpdateAllUsersWF();
                $com_model->sendUpdateAllUsersDLP();
                break;

            case 'update_firewall':
                /* $wl = new FirewallModel
                  $wl->modify($_POST['white']);
                  $wl->save();
                  $bl = new ListModel('black');
                  $bl->modify($_POST['black']);
                  $bl->save();
                 */
                //@TODO: This must be changed. For now groups are fixed to 5.
                /*
                 * The following excecutions must NOT be sent to background.

                 */

                $fwmodel->save();

                break;
            case 'update_lists':
                $wl = new ListModel('white');
                $wl->modify($_POST['white']);
                $wl->save();
                $bl = new ListModel('black');
                $bl->modify($_POST['black']);
                $bl->save();
                //@TODO: This must be changed. For now groups are fixed to 5.
                /*
                 * The following excecutions must NOT be sent to background.
                 */
                for ($i = 0; $i < 5; $i++)
                    exec("" . $GLOBALS['conf']['prefix']
                            . "usr/share/drainware/scripts/wfControl Update $i");
                break;
            default:
                $statusSave = false;
                $saveRequiered = array();
                foreach ($_POST as $element => $value) {
                    if ($value != $model->getConfValue($element)) {
                        $model->updateKey($element, $value);
                        $statusSave = True;
                        $saveRequiered[$element] = $value;
                    }
                }

                if ($statusSave) {
                    $model->save();
                    foreach ($saveRequiered as $element => $value) {
                        switch ($element) {
                            case "ddiwebfilter":
                                $cmd = "" . $GLOBALS['conf']['prefix'];
                                $cmd.= "usr/share/drainware/scripts/wfControl " . $value;
                                $cmd.= "> /dev/null 2>&1 &";
                                exec($cmd);
                                break;
                            case "ddiav":
                                $AVgroups = new GroupCollection();

                                foreach ($AVgroups->getGroups() as $AVgroup) {
                                    /*
                                     * This key DISABLES the content scan, so "on" means
                                     * AV disabled an "off" AV enabled.
                                     */
                                    $AVgroup->updateKey("disablecontentscan", $value == "on" ? "off" : "on");
                                    $AVgroup->save();
                                }
                                //@TODO: Create an external script to made this call.
                                exec("dansguardian -g > /dev/null 2>&1 &");
                                break;
                            case "ddisbmode";
                                $cmd = "" . $GLOBALS['conf']['prefix'];
                                $cmd.="usr/share/drainware/scripts/generalControl UpdateFilterType> /dev/null 2>&1 &";
                                exec($cmd);
                                break;
                        }
                    }
                }
        }
    }

}

?>
