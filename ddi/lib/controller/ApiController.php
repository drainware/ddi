<?php

class ApiController extends Controller {

    private static $month_events_limit = 500;
    private static $month_screenshoot_limit = 3;

    public function __construct($name = null) {
        parent::__construct($name);
    }

    /*
     * Depending on the passed action it runs a method.
     * It must to recieve the showAction method at least.
     */

    public function getFilterStatsAction() {

        $cloud_model = new CloudModel();
        $cloud_user = $cloud_model->getCloudUserByLicense($_SESSION['license']);

        $percent = $cloud_user['events']['monthly']['general'] * 100 / (self::$month_events_limit + $cloud_user['extra_events']);
        $percentage = $percent > 100 ? 100 : $percent;
        $month_events = array($percentage, $cloud_user['events']['monthly']['general'], (self::$month_events_limit  + $cloud_user['extra_events']));

        $group_model = new GroupModel();
        $groups = $group_model->countGroups() - 1;

        $stats = array(
            'month_events' => $month_events,
            'events' => $cloud_user['events'],
            'groups' => $groups,
        );

        $options = array(
            'data' => json_encode($stats),
        );

        $this->options = array_merge($this->options, $options);
    }

    public function getNetworkStatsAction() {

        $model = new InfoModel();
        $stats = array("nadminconnections" => $model->getAdminNConnections(),
            "nwebfilterconnections" => $model->getWebFilterConnections());
        $options = array(
            "data" => json_encode($stats),
        );

        $this->options = array_merge($this->options, $options);
    }

    public function getMemUsageAction() {

        $model = new InfoModel();

        $options = array(
            "data" => json_encode($model->getMemUsage()),
        );
        $this->options = array_merge($this->options, $options);
    }

    public function getSwapUsageAction() {

        $model = new InfoModel();

        $options = array(
            "data" => json_encode($model->getSwapUsage()),
        );
        $this->options = array_merge($this->options, $options);
    }

    public function getDiskUsageAction() {

        $model = new InfoModel();

        $options = array(
            "data" => json_encode($model->getDiskUsage()),
        );
        $this->options = array_merge($this->options, $options);
    }

    public function getCpuUsageAction() {

        $model = new InfoModel();

        $options = array(
            "data" => json_encode($model->getCpuUsage()),
        );


        $this->options = array_merge($this->options, $options);
    }

    public function getGroupsOfUserAction() {
        syslog(LOG_DEBUG, "getGroupsOfUserAction(): user = " . $_GET['user'] . " lic = "  . $_GET['license']);

        $group_names = array();
        if (isset($_GET['license']) && (isset($_GET['user']) || isset($_GET['email']))) {

            $main_model = new MainModel();
            $mode = $main_model->getConfValue("mode");

            $cloud_model = new CloudModel();
            $auth = $cloud_model->getClientUserAuth($_GET['license']);

            $group_model = new GroupModel();

            if ($mode == 'cloud') {

                switch ($auth) {
                    case 'local':
                        if (!empty($_GET['user'])) {
                            $user_model = new UserModel();
                            $user_object = $user_model->getUserByName($_GET['user'], $_GET['license']);
                            $group_model = new GroupModel();
                            if (isset($user_object)) {
                                foreach ($user_object['group'] as $gid) {
                                    $group_object = $group_model->getGroup($gid);
                                    $group_names[] = $group_object['name'];
                                }
                            } else {
                                $cloud_user = $cloud_model->getCloudUser($_GET['license']);

                                $users_cursor = $user_model->getUsers($_GET['license']);
                                

 
				syslog(LOG_DEBUG, "type == " . $cloud_user['type'] . " and nbr_users = " . $cloud_user['nbr_users'] . " / " . $users_cursor->count());
                                
                                if ($cloud_user['type'] == 'freemium' || $cloud_user['nbr_users'] > $users_cursor->count()) {
                                    $group_object = $group_model->getDefaultGroup($_GET['license']);
                                    $groups = array((string) $group_object['_id']);
                                    $user_model->createUser($_GET['user'], 'drainware', $groups, $_GET['license']);

				    //syslog(LOG_DEBUG, "reference_activated: license = " . $_GET['license'] );
                                    //Check friend reference
                                    if(!$cloud_user['reference_activated']){
					//syslog(LOG_DEBUG, "reference_activated: reference is not activated for " . $cloud_user['email'] );
                                        $referer= $cloud_model->getCustomerByID($cloud_user['referenced_by']);
                                        $referer_data = array(
                                            'extra_events' => ($referer['extra_events'] < 5000) ? $referer['extra_events'] + 100 : $cloud_user['extra_events'],
                                        );
					//syslog(LOG_DEBUG, "reference_activated: " . $referer['email'] . " tiene " . $referer['extra_events'] . " eventos");

				    	//syslog(LOG_DEBUG, "reference_activated:  " . $referer['email'] . " va a recibir " . $referer['extra_events'] . " eventos extra ");

                                        $cloud_model->updateCloudUser($referer['license'], $referer_data);
                                        
                                        $new_data = array(
                                            'reference_activated' => true,
                                        );
                                        $cloud_model->updateCloudUser($_GET['license'], $new_data);
                                        
                                        $subject = "You won 100 extra events";
                                        $vars = array(
                                            'email' => $cloud_user['email']
                                        );

                                        $mail_model = new MailModel();
                                        $mail_model->setSubject($subject);
                                        $mail_model->setVars($vars);
                                        $mail_model->setTemplate('extraEventsMessage');
                                        $mail_model->setDest($referer['email']);
                                        $mail_model->sendMail();
					//syslog(LOG_DEBUG, "reference_activated: correo mandado a  " . $cloud_user['email']);
                                        
				    }
                                } else {

                                    $group_names[] = 'exceeded-users';

                                    $mail_model = new MailModel();
                                    $subject = "The license " . $_GET['license'] . " exceeded the number of users";
                                    $vars = array(
                                        'license' => $_GET['license'],
                                        'user' => $_GET['user']);
                                    $mail_model->setVars($vars);
                                    $mail_model->setSubject($subject);
                                    $mail_model->setDest("info@drainware.com");
                                    $mail_model->setTemplate('usersLimitMessage');
                                    $mail_model->sendMail();
                                }


                                //Check customer deployed
                                if(!$cloud_user['deployed']){
                                    if($users_cursor->count() > 0){
                                        $cloud_model->updateCloudUser($_GET['license'], array('deployed' => true));
                                    }
                                }

                            }
                        }
                        break;
                    case 'ldap':
                        $ldap = new LdapModel($_GET['license']);
                        if (!empty($_GET['user'])) {
                            $groups_path = $ldap->getGroupsOfUser($_GET['user']);
                        } else if (!empty($_GET['email'])) {
                            $groups_path = $ldap->getGroupsOfUserByEmail($_GET['email']);
                        } else {
                            die();
                        }

                        $gids = array();
                        foreach ($groups_path as $gpath) {
                            $group_object = $group_model->getGroupByPath($gpath, $_GET['license']);
                            if (isset($group_object['name'])) {
                                $group_names[] = $group_object['name'];
                                $gids[] = (string) $group_object['_id'];
                            }
                        }
                        $user_model = new UserModel();
                        $user_object = $user_model->getUserByName($_GET['user']);

                        if (isset($user_object)) {
                            $user_object['group'] = $gids;
                            $user_model->saveUserObject($user_object);
                        } else {
                            $user_model->createUser($_GET['user'], 'drainware', $gids, $_GET['license']);
                            
                            //Check customer deployed
                            if (!$cloud_user['deployed']) {
                                $users_cursor = $user_model->getUsers($_GET['license']);
                                if ($users_cursor->count() > 0) {
                                    $cloud_model->updateCloudUser($_GET['license'], array('deployed' => true));
                                }
                            }
                            
                            //Check friend reference
                            if (!$cloud_user['reference_activated']) {
                                $friend = $cloud_model->getCustomerByID($cloud_user['referenced_by']);
                                $friend_data = array(
                                    'extra_events' => ($friend['extra_events'] < 5000) ? $friend['extra_events'] + 100 : $cloud_user['extra_events'],
                                );
                                $cloud_model->updateCloudUser($friend['license'], $friend_data);

                                $new_data = array(
                                    'reference_activated' => true,
                                );
                                $cloud_model->updateCloudUser($_GET['license'], $new_data);
                                
                                $subject = "You won 100 extra events";
                                $vars = array(
                                    'email' => $friend['email']
                                );

                                $mail_model = new MailModel();
                                $mail_model->setSubject($subject);
                                $mail_model->setVars($vars);
                                $mail_model->setTemplate('extraEventsMessage');
                                $mail_model->setDest($cloud_user['email']);
                                $mail_model->sendMail();
                            }
                        }
                        break;

                    default:
                        break;
                }

                if (empty($group_names)) {
                    $group_names[] = 'default';

                    $group_model = new GroupModel();
                    $group_object = $group_model->getDefaultGroup($_GET['license']);
                    $groups = array((string) $group_object['_id']);

                    $user_model = new UserModel();
                    $user_object = $user_model->getUserByName($_GET['user'], $_GET['license']);

                    if (isset($user_object)) {
                        $user_object['group'] = $groups;
                        $user_model->saveUserObject($user_object);
                    } else {
                        $user_model->createUser($_GET['user'], 'drainware', $groups, $_GET['license']);

                    }
                }
            } else if ($mode == 'private') {
                
            }
        } else {
            $group_names[] = 'default';
        }


        $json_object = json_encode($group_names);

        $options = array(
            "json_object" => $json_object,
        );
        $this->options = array_merge($this->options, $options);
    }

    public function getWebFilterConfigAction() {

        $method = $GLOBALS['conf']['ddi']['configuration']['authentication']['value'];

        //get groups of user
        //$user = $_GET['user'];

        $mmodel = new MainModel();
        $typeddi = $mmodel->getConfValue("ddisbmode");
        // http://www.drainware.com/ddi/?module=api&action=getWebFilterConfig&ip=192.168.0.105
        if (isset($_GET['ip'])) {
            $cmd = "grep " . $_GET['ip'] . " /etc/ppp/chap-secrets | awk '{ print \$1\$3 }'";
            //echo $cmd;
            $license = exec($cmd);
            //echo $license;
            $mongo_model = new MongoModel();
            $m = $mongo_model->connect();
            $db = $m->ddi;
            $col = $db->licenses;
            $lic_data = $col->findOne(array('lic' => $license), array('mail'));
            //$lic_data = $col->find();

            $group_name = $lic_data['mail'];
            //echo $group_name;

            $group_model = new GroupModel();

            $white_list = $group_model->getWhiteList($group_name);

            $black_list = $group_model->getBlackList($group_name);

            $categories = $group_model->getCategoriesBlocked($group_name);

            $extensions = $group_model->getExtensionsBlocked($group_name);

            $groups_user = array($group_name);
        } elseif ($typeddi == "unique") {

            $group_name = "default";


            $group_model = new GroupModel();

            $white_list = $group_model->getWhiteList($group_name);

            $black_list = $group_model->getBlackList($group_name);

            $categories = $group_model->getCategoriesBlocked($group_name);

            $extensions = $group_model->getExtensionsBlocked($group_name);

            $groups_user = array($group_name);
        } elseif ($method == "local") {

            //dirty code
            $um = new UserModel();
            $user = $_GET['user'];
            $group_name = $um->getGroupOfUser($user);

            //$group_name = "IT";//FIXME


            $group_model = new GroupModel();

            $white_list = $group_model->getWhiteList($group_name);

            $black_list = $group_model->getBlackList($group_name);

            $categories = $group_model->getCategoriesBlocked($group_name);

            $extensions = $group_model->getExtensionsBlocked($group_name);

            $groups_user = array($group_name);
        } elseif ($method == "ldap") {
            $user = $_GET['user'];

            $ldap = new LdapModel();
            $groups = $ldap->getGroupsOfUser($user);

            $group_model = new GroupModel();
            $categories = array();
            $extensions = array();
            $white_list = array();
            $black_list = array();

            $firstblacklist = false;
            $firstcat = false;
            $firstextension = false;

            $i = 0;
            $no_cats = false; //$no_cats --> Groups without blocked categories

            foreach ($groups as $group) {

                $group_name = $group_model->getNameByLdapPath($group);
                if ($group_model->exists($group_name)) {
                    $groups_user[] = $group_name;
                    $white_list_group = $group_model->getWhiteList($group_name);
                    if (!empty($white_list_group) && !empty($white_list_group[0]))
                        $white_list = array_merge($white_list_group, $white_list);

                    $black_list_group_aux = $group_model->getBlackList($group_name);
                    $black_list_group = array();
                    foreach ($black_list_group_aux as $elem)
                        $black_list_group[] = $elem;

                    if (!empty($black_list_group) && !empty($black_list_group[0])) {

                        if (!empty($black_list)) {
                            $black_list = array_intersect($black_list, $black_list_group);
                        } else if (!$firstblacklist) {
                            $black_list = $black_list_group;
                            $firstblacklist = true;
                        } else {
                            $black_list = array();
                        }
                    }

                    $categories_group = $group_model->getCategoriesBlocked($group_name);

                    if (!empty($categories_group) && $no_cats == false) {

                        if (!empty($categories)) {
                            $tmp_categories = array_intersect($categories, $categories_group);
                            // jpalanco: avoid include the index value in the array result of array_intersect
                            // that was the reason of the fails with the mlojo user
                            $categories = array();

                            foreach ($tmp_categories as $cat) {
                                $categories[] = $cat;
                            }
                        } else if (!$firstcat) {
                            $categories = $categories_group;
                            $firstcat = true;
                        } else {
                            $categories = array();
                        }
                    } else {
                        $no_cats = true;
                        $categories = array();
                    }


                    $extensions_group = $group_model->getExtensionsBlocked($group_name);
                    if (!empty($extensions_group)) {
                        if (!empty($extensions)) {
                            $extensions = array_intersect($extensions, $extensions_group);
                        } else if (!$firstextension) {
                            $extensions = $extensions_group;
                            $firstextension = true;
                        } else {
                            $extensions = array();
                        }
                    } else {
                        $extensions = array();
                    }
                }

                $i = $i + 1;
            }
        }

        if (count($groups_user) < 1) {

            $group_name = "default";
            $group_model = new GroupModel();
            $white_list = $group_model->getWhiteList($group_name);
            $black_list = $group_model->getBlackList($group_name);
            $categories = $group_model->getCategoriesBlocked($group_name);
            $extensions = $group_model->getExtensionsBlocked($group_name);
            $groups_user = array($group_name);
        }


        $object['groups_user'] = $groups_user;

        //Add to categories adv, malware o phishing depending of the module status
        $main_model = new MainModel();
        $advstatus = $main_model->getConfValue("ddiadv");
        $mlwstatus = $main_model->getConfValue("ddimlw");
        $pshstatus = $main_model->getConfValue("ddipsh");

        if ($advstatus == "on") {
            $categories[] = "adv";
        }

        if ($mlwstatus == "on") {
            $categories[] = "malware";
        }

        if ($pshstatus == "on") {
            $categories[] = "phishing";
        }

        $object['categories'] = $categories;

        foreach ($extensions as $extension) {
            $extensions_detailed[] = $group_model->getExtensionDetail($extension);
        }

        if (empty($extensions_detailed))
            $extension_detailed = array();

        $object['extensions'] = $extensions_detailed;
        $object['white_list'] = $white_list;
        $object['black_list'] = $black_list;


        $json_object = json_encode($object);


        $options = array(
            "json_object" => $json_object,
        );
        $this->options = array_merge($this->options, $options);
    }

    public function getDlpGroupConfigAction() {

        $license = $_GET['license'];
        $group_name = $_GET['group'];

        $dlp_config = $this->getDlpGroupConfig($license, $group_name);

        $json_object = json_encode($dlp_config);

        $options = array(
            "json_object" => $json_object,
        );
        $this->options = array_merge($this->options, $options);
    }

    //FIXME: should be getDlpGroupConfig instead of getDLPGroupConfig?
    public static function getDLPGroupConfig($license, $group_name) {

        $cloud_model = new CloudModel();
        $cloud_user = $cloud_model->getCloudUserByLicense($license);

        $dlp_config = array();
        $dlp_config["subconcepts"] = array();
        $dlp_config["rules"] = array();
        $dlp_config["files"] = array();
        $dlp_config["network_places"] = array();
        $dlp_config["applications"] = array();
        $dlp_config["screenshot_severity"] = null;
        $dlp_config["endpoint_modules"] = array();
        $dlp_config["mime_types"] = array();
        $dlp_config["block_encrypted"] = 0;
        $dlp_config['groups_user'] = isset($license) ? array($license . '_' . $group_name) : array($group_name);

        if ($cloud_user['events']['availability']) {
            $policy_model = new PolicyModel();
            $policies = $policy_model->getPoliciesByGroup($license, $group_name);

            $subconcept_model = new SubconceptModel();
            $rule_model = new RuleModel();
            $file_model = new FileModel();
            $network_place_model = new NetworkPlaceModel();
            $application_model = new ApplicationModel();

            foreach ($policies as $policy_id => $policy_object) {

                foreach ($policy_object['concepts'] as $concept_id) {
                    foreach ($subconcept_model->getSubConceptsByIdConcept($concept_id) as $subconcept_id => $subconcept_object) {
                        if (isset($dlp_config['subconcepts'][$subconcept_id])) {
                            $dlp_config['subconcepts'][$subconcept_id]['policies_id'][] = $policy_id;
                        } else {
                            $subconcept_object['policies_id'][] = $policy_id;
                            $dlp_config['subconcepts'][$subconcept_id] = $subconcept_object;
                        }
                    }
                }

                foreach ($policy_object['subconcepts'] as $subconcept_id) {
                    $subconcept_object = $subconcept_model->getSubconcept($subconcept_id);
                    if (isset($dlp_config['subconcepts'][$subconcept_id])) {
                        $dlp_config['subconcepts'][$subconcept_id]['policies_id'][] = $policy_id;
                    } else {
                        $subconcept_object['policies_id'][] = $policy_id;
                        $dlp_config['subconcepts'][$subconcept_id] = $subconcept_object;
                    }
                }

                foreach ($policy_object['rules'] as $rule_id) {
                    $rule_object = $rule_model->getRule($rule_id, $license);
                    if (isset($dlp_config['rules'][$rule_id])) {
                        $dlp_config['rules'][$rule_id]['policies_id'][] = $policy_id;
                    } else {
                        $rule_object['policies_id'][] = $policy_id;
                        $dlp_config['rules'][$rule_id] = $rule_object;
                    }
                }

                foreach ($policy_object['files'] as $file_id) {
                    $file_object = $file_model->getFile($file_id, $license);
                    if (isset($dlp_config['files'][$file_id])) {
                        $dlp_config['files'][$file_id]['policies_id'][] = $policy_id;
                    } else {
                        $file_object['policies_id'][] = $policy_id;
                        $dlp_config['files'][$file_id] = $file_object;
                    }
                }

                foreach ($policy_object['network_places'] as $network_place_id) {
                    $network_place_object = $network_place_model->getNetworkPlace($network_place_id, $license);
                    if (isset($dlp_config['network_places'][$network_place_id])) {
                        $dlp_config['network_places'][$network_place_id]['policies_id'][] = $policy_id;
                    } else {
                        $network_place_object['policies_id'][] = $policy_id;
                        $dlp_config['network_places'][$network_place_id] = $network_place_object;
                    }
                }

                foreach ($policy_object['applications'] as $application_id) {
                    $application_object = $application_model->getApplication($application_id, $license);
                    if (isset($dlp_config['applications'][$application_id])) {
                        $dlp_config['applications'][$application_id]['policies_id'][] = $policy_id;
                    } else {
                        $application_object['policies_id'][] = $policy_id;
                        $dlp_config['applications'][$application_id] = $application_object;
                    }
                }

                $dlp_config['policies'][$policy_id] = $policy_object['groups'];
            }

            $group_model = new GroupModel();

            $dlp_config['endpoint_modules'] = $group_model->getEndpointModules($license);
            $dlp_config['mime_types'] = $group_model->getMimeTypes($license);
            $dlp_config['block_encrypted'] = $group_model->getEncryptedFiles($license, $group_name);
            $dlp_config['screenshot_severity'] = $group_model->getScreenShotSeverity($license);
            $dlp_config['groups_user'] = isset($license) ? array($license . '_' . $group_name) : array($group_name);
        }

        return $dlp_config;
    }

    public function getAtpConfigAction() {

        $license = $_GET['license'];
        $atp_config = $this->getATPConfig($license);
        $json_object = json_encode($atp_config);
        $options = array(
            "json_object" => $json_object,
        );
        $this->options = array_merge($this->options, $options);
    }

    public static function getATPConfig($license) {
        $cloud_model = new CloudModel();
        $cloud_user = $cloud_model->getCloudUserByLicense($license);

        $atp_obj = array();

        if ($cloud_user['events']['availability']) {
            $app_model = new AppModel();
            $apps = $app_model->getActiveApps($license);

            foreach ($apps as $app_id => $app_object) {
                $app_object = $app_model->getApp($app_id, $license);

                $desc_obj = array();
                $desc_obj['name'] = $app_object['name'];
                foreach ($app_object['variables'] as $key => $value) {
                    if ($key == "util_printf") {
                        $desc_obj["util.printf"] = $value;
                    } else {
                        $desc_obj[$key] = $value;
                    }
                }
                $desc_obj["ForceTermination"]['value'] = $app_object['force_termination'];
                $desc_obj["ResumeMonitoring"]['value'] = $app_object['resume_monitoring'];
                $desc_obj['extensions'] = $app_object['extensions'];

                $app_obj['app'] = $desc_obj;

                $atp_obj[] = $app_obj;
            }
        }

        $atp_config = array();
        $atp_config['atp'] = $atp_obj;

        return $atp_config;
    }

    private function checkEventsAvailability($cloud_user) {
        $events_availability = true;

        if ($cloud_user['type'] == 'freemium') {
            $events_availability = $cloud_user['events']['availability'];
            if ($events_availability) {

                if ($cloud_user['events']['screenshot']) {
                    if ($cloud_user['events']['monthly']['general'] >= self::$month_screenshoot_limit) {
                        $cloud_model = new CloudModel();
                        $cloud_user['events']['screenshot'] = false;
                        $cloud_model->saveCloudUser($cloud_user);

                        $group_model = new GroupModel();
                        $group_object = $group_model->getDefaultGroup($cloud_user['license']);
                        $group_object['screenshot_severity'] = 'none';
                        $group_model->saveGroup($group_object);

                        $group_objects = $group_model->getGroups($cloud_user['license']);
                        foreach ($group_objects as $group_object) {
                            $group_dlp_config = ApiController::getDLPGroupConfig($cloud_user['license'], $group_object['name']);

                            $amqp_model = new AMQPModel();
                            $amqp_model->setExchange('server');
                            $amqp_model->setRoutingKey($group_object['name'], $cloud_user['license']);
                            $amqp_model->setMessageModule('dlp');
                            $amqp_model->setMessageCommand('update');
                            $amqp_model->setMessageArgs($group_dlp_config);
                            $amqp_model->sendMessage();
                        }
                    }
                }

                if ($cloud_user['events']['monthly']['general'] >= (self::$month_events_limit + $cloud_user['extra_events'])) {
                    $events_availability = false;

                    $cloud_model = new CloudModel();
                    $cloud_user['events']['availability'] = $events_availability;
                    $cloud_model->saveCloudUser($cloud_user);

                    $this->sendEmailNotificationLimitOfEvent($cloud_user);

                    $group_model = new GroupModel();
                    $group_objects = $group_model->getGroups($cloud_user['license']);
                    foreach ($group_objects as $group_object) {
                        $group_dlp_config = ApiController::getDLPGroupConfig($cloud_user['license'], $group_object['name']);

                        $amqp_model = new AMQPModel();
                        $amqp_model->setExchange('server');
                        $amqp_model->setRoutingKey($group_object['name'], $cloud_user['license']);
                        $amqp_model->setMessageModule('dlp');
                        $amqp_model->setMessageCommand('update');
                        $amqp_model->setMessageArgs($group_dlp_config);
                        $amqp_model->sendMessage();
                    }

                    $atp_config = ApiController::getATPConfig($cloud_user['license']);

                    $amqp_model = new AMQPModel();
                    $amqp_model->setExchange('server');
                    $amqp_model->setRoutingKey('*');
                    $amqp_model->setMessageModule('atp');
                    $amqp_model->setMessageCommand('update');
                    $amqp_model->setMessageArgs($atp_config);
                    $amqp_model->sendMessage();
                }
            }
        }

        return $events_availability;
    }

    private function sendEmailNotificationLimitOfEvent($cloud_user) {
        $mail_model = new MailModel();
	$mail_model->setSubject("You have consumed all your free events");
	$mail_model->setDest($cloud_user['email']);
	$mail_model->setTemplate('eventsLimitMessage');
	$mail_model->sendMail();
    }

    private function registerDLPEvents($event_post) {
        $event_model = new DlpEventModel($event_post['license']);

        $valid_types = array("subconcept", "rule", "file", "network_place", "encrypted");

        $datetime = split(' ', $event_post['datetime']);
        $date = str_replace('-', '.', $datetime[0]);

        $cloud_model = new CloudModel();
        $auth_method = $cloud_model->getClientUserAuth($event_post['license']);

        $group_names = array();
        switch ($auth_method) {
            case "local":
                $user_model = new UserModel();
                $user_object = $user_model->getUserByName($event_post['user'], $event_post['license']);
                $group_model = new GroupModel();
                if (isset($user_object)) {
                    foreach ($user_object['group'] as $gid) {
                        $group_object = $group_model->getGroup($gid);
                        $group_names[] = $group_object['name'];
                    }
                }
                break;
            case "ldap":
                $ldap = new LdapModel($event_post['license']);
                $groups_path = $ldap->getGroupsOfUser($event_post['user']);
                
                $group_model = new GroupModel();
                foreach ($groups_path as $gpath) {
                    $group_object = $group_model->getGroupByPath($gpath, $event_post['license']);
                    if (isset($group_object['name'])) {
                        $group_names[] = $group_object['name'];
                    }
                }
                break;
            default:
                break;
        }

        if (empty($group_names)) {
            $group_names[] = 'default';
        }

        $global_info_event = array();
        $global_info_event['timetime'] = new MongoDate(strtotime($event_post['datetime']));
        $global_info_event['date'] = $date;
        if (isset($event_post['license'])) {
            $host = "www.drainware.com";
            $global_info_event['license'] = $event_post['license'];
        }
        $global_info_event['ip'] = $event_post['ip'];
        $global_info_event['user'] = $event_post['user'];
        $global_info_event['groups'] = $group_names;
        $global_info_event['origin'] = $event_post['origin'];
        $global_info_event['scid'] = $event_post['scid'];
        $global_info_event['geodata'] = json_decode($event_post['geodata']);
        
        $json_object = ApiController::object_to_array(json_decode($event_post['json']));

        $nro_events = 0;

        $info_event['endpoint_module'] = $json_object['Source'];
        
        $info_event['app'] = array();
        $info_event['app']['process'] = isset($json_object['Application']) ? $json_object['Application']['process_name']: "explorer.exe";
        $info_event['app']['description'] = isset($json_object['Application']) ? trim($json_object['Application']['description']): "Windows Explorer";
        $info_event['app']['details'] = isset($json_object['Application']) ? preg_replace("/[^a-zA-Z0-9\s\-_.,:]/", " ", $json_object['Application']['details']) : "";

        $send_to = array();

        foreach ($json_object['Results'] as $result) {
            
            if (isset($result['FileName'])) {
                $info_event['filename'] = $result['FileName'];
            }

            foreach ($result['Coincidences'] as $coincidence) {
                $info_event['id'] = $coincidence['Id'];
                $info_event['action'] = $coincidence['Action'];
                $info_event['severity'] = $coincidence['Severity'];
                $info_event['type'] = $coincidence['Type'];
		// UGLY HACK: instead of use array_unique() the endpoint should be fixed
                $info_event['policies'] = array_unique($coincidence['Policies']);
                $info_event['policies_name'] = $event_model->getPoliciesName($info_event['policies']);

                if (in_array($info_event['type'], $valid_types)) {
                    if ($info_event['type'] == "subconcept") {
                        $concept = $event_model->getConcept($info_event['id']);
                        $info_event['concept'] = (string) $concept['_id'];
                        $info_event['concept_name'] = $concept['concept'];
                    }
                    $info_event['identifier'] = $event_model->getIdentifier($info_event['id'], $info_event['type']);
                } else {
                    $info_event['type'] = "unknow";
                    $info_event['identifier'] = "unknow";
                }

                $events_id = array();
                if (is_array($coincidence['Matches'])) {
                    foreach ($coincidence['Matches'] as $match) {
                        $info_event['match'] = trim($match['match']);
                        $info_event['context'] = trim($match['context']);
                        $event_id = $event_model->insertEvent(array_merge($global_info_event, $info_event));
                        $nro_events += 1;
                        if ($info_event['action'] == 'alert') {
                            $events_id[] = $event_id;
                        }
                    }
                } else {
                    $info_event['match'] = trim($coincidence['Matches']);
                    $event_id = $event_model->insertEvent(array_merge($global_info_event, $info_event));
                    $nro_events += 1;

                    if ($info_event['action'] == 'alert') {
                        $events_id[] = $event_id;
                    }
                }

                $emails = $event_model->getEmailsByPolicies($event_post['license'], $info_event['policies']);
                foreach ($emails as $email) {
                    foreach ($events_id as $event_id) {
                        $send_to[$email][] = $event_id;
                    }
                }
            }
        }
        
        if(!empty($send_to)){
            $client = $cloud_model->getCloudUser($event_post['license']);
            $mail_datetime = new DateTime($event_post['datetime'], new DateTimeZone($client['timezone']));
            $mail_date = date('Y-m-d', $mail_datetime->format('U'));
            $this->sendAlertEventAction($host, $mail_date, $info_event['severity'], $send_to);            
        }

        return $nro_events;
    }

    private function registerAtpEvents($event_post) {
        $atp_event_model = new AtpEventModel($event_post['license']);

        $datetime = split(' ', $event_post['datetime']);
        $date = str_replace('-', '.', $datetime[0]);

        $cloud_model = new CloudModel();
        $auth_method = $cloud_model->getClientUserAuth($event_post['license']);

        $group_names = array();
        switch ($auth_method) {
            case "local":
                $user_model = new UserModel();
                $user_object = $user_model->getUserByName($event_post['user'], $event_post['license']);
                $group_model = new GroupModel();
                $group_object = $group_model->getGroup($user_object['group']);
                if (isset($group_object['name'])) {
                    $group_names[] = $group_object['name'];
                }
            case "ldap":
                $ldap = new LdapModel($event_post['license']);
                $groups_path = $ldap->getGroupsOfUser($event_post['user']);
                
                $group_model = new GroupModel();
                foreach ($groups_path as $gpath) {
                    $group_object = $group_model->getGroupByPath($gpath, $event_post['license']);
                    if (isset($group_object['name'])) {
                        $group_names[] = $group_object['name'];
                    }
                }
                break;
            default:
                break;
        }
        
        if (empty($group_names)) {
            $group_names[] = 'default';
        }

        $atp_event = array();
        $atp_event['timetime'] = new MongoDate(strtotime($event_post['datetime']));
        $atp_event['date'] = $date;
        if (isset($event_post['license'])) {
            $atp_event['license'] = $event_post['license'];
        }
        $atp_event['ip'] = $event_post['ip'];
        $atp_event['groups'] = $group_names;
        $atp_event['user'] = $event_post['user'];
        $atp_event['origin'] = $event_post['origin'];
        $atp_event['processname'] = $event_post['processname'];

        $atp_object_vars = get_object_vars(json_decode($event_post['variables']));

        foreach ($atp_object_vars as $key => $value) {
            $atp_event[$key] = $value;
        }

        $atp_event['scid'] = $event_post['scid'];
        $atp_event['geodata'] = json_decode($event_post['geodata']);

        $atp_event_model->insertEvent($atp_event);
    }

    private function sendAlertEventAction($host, $msg_date, $msg_severiy, $send_to) {
        //FIXME: send date from POST, send groups, send match of regex and the name of the regex
        if (!isset($host)) {
            $host = exec("cat /etc/network/interfaces | grep address | awk '{ print \$2 }' ");
        }
        foreach ($send_to as $dst_email => $events_id) {
            $mail_model = new MailModel();
            $subject = "Drainware Alert: " . $msg_severiy . " severity"; 
            $vars = array(
                   'date' => $msg_date,
                   'events' => $events_id);
            $mail_model->setVars($vars);
            $mail_model->setSubject($subject);
            $mail_model->setDest($dst_email);
            $mail_model->setTemplate('eventAlertMessage');
            $mail_model->sendMail();


        }
    }

    private function registerForensicsEvent($fevent) {

        $remote_query_model = new ForensicsModel();

        if (empty($fevent['license'])) {
            $fevent['license'] = null;
        }
        
        foreach ($fevent as $key => $value) {
            $fevent[$key] = $value == "None" ? null : $value;          
        }
        
        if(isset($fevent['datetime'])){
            $fevent['timetime'] = new MongoDate(strtotime($fevent['datetime']));
            $fevent['datetime'] = date('F j, Y, H:i:s a', $fevent['timetime']->sec);
        }
        $fevent['payload'] = json_decode($fevent['payload']);
        $fevent['geodata'] = json_decode($fevent['geodata']);
        $fevent['registered'] = new MongoDate();
        
        if (isset($fevent['command'])) {
            switch ($fevent['command']) {
                case 'search':
                case 'geodata':
                    $remote_query_model->registerResultSearch($fevent);
                    break;
                case 'ping':
                    //$remote_query_model->registerEndpointAgent($search_result_oject);
                    break;
                default:
                    $remote_query_model->registerResultObject($fevent);
                    break;
            }
        } else {
            $remote_query_model->registerResultSearch($fevent);
        }
    }

    public function registerJsonEventsAction() {
        $cloud_model = new CloudModel();
        $cloud_user = $cloud_model->getCloudUserByLicense($_POST['license']);
        if ($this->checkEventsAvailability($cloud_user)) {
            $nro_events = $this->registerDLPEvents($_POST);

	    //REDIS 
	    $redis = new RedisModel();
	    $redis->incrby('GlobalEvents', $nro_events);
	    //syslog(LOG_DEBUG, "GlobalEvents + " . $nro_events); 


            $cloud_user['events']['monthly']['general'] += $nro_events;
            $cloud_user['events']['monthly']['dlp'] += $nro_events;
            $cloud_user['events']['global']['general'] += $nro_events;
            $cloud_user['events']['global']['dlp'] += $nro_events;
            $cloud_model->saveCloudUser($cloud_user);
        }
    }

    public function registerAtpEventsAction() {
        $cloud_model = new CloudModel();
        $cloud_user = $cloud_model->getCloudUserByLicense($_POST['license']);
        if ($this->checkEventsAvailability($cloud_user)) {
            $this->registerAtpEvents($_POST);

	    //REDIS
            $redis = new RedisModel();
            $redis->incr('GlobalEvents');

            $cloud_user['events']['monthly']['general']++;
            $cloud_user['events']['monthly']['atp']++;
            $cloud_user['events']['global']['general']++;
            $cloud_user['events']['global']['atp']++;
            $cloud_model->saveCloudUser($cloud_user);
        }
    }

    public function registerRemoteQueryResultsAction() {
        $cloud_model = new CloudModel();
        $cloud_user = $cloud_model->getCloudUserByLicense($_POST['license']);
        
        switch ($_POST['command']) {
            case 'ping':
            case 'geodata':
                $this->registerForensicsEvent($_POST);
                break;
            default:
                if ($this->checkEventsAvailability($cloud_user)) {
                    $this->registerForensicsEvent($_POST);

            	    //REDIS
        	    $redis = new RedisModel();
	            $redis->incr('GlobalEvents');

                    $cloud_user['events']['monthly']['general']++;
                    $cloud_user['events']['monthly']['forensics']++;
                    $cloud_user['events']['global']['general']++;
                    $cloud_user['events']['global']['forensics']++;
                    $cloud_model->saveCloudUser($cloud_user);
                }
                break;
        }
    }

    public function getScreenshotAction() {
        $fid = $_GET['id'];
        $storage_model = new StorageModel();
        $image = $storage_model->getFile($fid);
        header('Content-type: image/jpeg');
        echo $image;
    }

    public function getGlobalEventsAction(){

	$redis = new RedisModel();
	$global_events = $redis->getVariable('GlobalEvents');

	if (empty($global_events)) {
	    $global_events = 83814;
	    $redis->setPersistentVariable('GlobalEvents', 83814);
	}

	/*
        $cloud_model = new CloudModel();
        $global_events = $cloud_model->getGlobalEvents();
	*/

        $options = array(
            "global_events" => $global_events,
        );
        $this->options = array_merge($this->options, $options);
    }
    
    public static function object_to_array($obj) {
        $arr = array();
        $_arr = is_object($obj) ? get_object_vars($obj) : $obj;
        foreach ($_arr as $key => $val) {
                $val = (is_array($val) || is_object($val)) ? ApiController::object_to_array($val) : $val;
                $arr[$key] = $val;
        }
        return $arr;
    }
    
    public function getEventsNotificationAction(){
        $_id = empty($_POST['id']) ? null : $_POST['id'] ;
        
        $criteria = null;
        if (isset($_id)) {
            $cloud_model = new CloudModel();
            $client = $cloud_model->getCloudUser();

            $criteria = null;
            if ($client['notifications']['status'] == 'enabled') {
                $criteria = array(
                    '_id' => array(
                        '$gt' => new MongoId($_id)
                    ),
                    'action' => array(
                        '$in' => array_values($client['notifications']['when']['action'])
                    ),
                    'severity' => array(
                        '$in' => array_values($client['notifications']['when']['severity'])
                    )
                );
            }
        }
        
        $reporter_model = new ReporterModel();
        $events = $reporter_model->getLastDLPEvents($criteria);
        
        $last_id = null;
        $notifications_object = array();
        foreach ($events as $eid => $event) {
            $last_id = $eid;
            $notifications_object[] = array(
                'title' => 'Drainware ' . $event['action'],
                'body' => ucfirst($event['severity']) . ': user ' . $event['user'] . ', Policy ' . $event['identifier']
            );  
        }
        
        $notifications = isset($criteria) ? $notifications_object : array();
        
        $response = json_encode(array(
            'last_id' => $last_id,
            'notifications' => $notifications
        ));
        
        $options = array(
            'response' => $response
        );
        
        $this->options = array_merge($this->options, $options);
        
    }
    
    public function getClientUsersAction(){
        
        $code = -1;
        if (sha1($_POST['password']) == 'ee66bcf34d165f171074ed60e54ad99888978601') {
            $cloud_model = new CloudModel();
            $client = $cloud_model->getCloudUser($_POST['license']);

            $code = isset($client) ? 1 : 0;
            $users = $client['nbr_users'];
	    $company = $client['company'];
        }
        $data = array(
            'company' => $company,
            'code' => $code,
            'users' => $users
        );
        
        $options = array(
            'response' => json_encode($data)
        );
        
        $this->options = array_merge($this->options, $options);
    }
    
    public function sendEmailAction(){
        if (isset($_POST['id']) && isset($_POST['type'])) {
            $cloud_model = new CloudModel();
            $customer = $cloud_model->getCustomerByID($_POST['id']);

            $mail_model = new MailModel();
            switch ($_POST['type']) {
                case '1st_policy':
                    $mail_model->setDest($customer['email']);
                    $mail_model->setSubject('We miss you');
                    $mail_model->setTemplate('1st_policy');
                    $mail_model->sendMail();
                    break;                
                case 'miss_you':
                    $mail_model->setDest($customer['email']);
                    $mail_model->setSubject('We miss you');
                    $mail_model->setTemplate('weMissYouMessage');
                    $mail_model->sendMail();
                    break;
                case 'pending_customer':
                    $mail_model->setDest('info@drainware.com');
                    $mail_model->setSubject('Pending customer');
                    $mail_model->setVars(array(
                        'email' => $customer['email'],
                        'license' => $customer['license'],
                        'company' => $customer['company'])
                    );
                    $mail_model->setTemplate('pendingCustomerMessage');
                    $mail_model->sendMail();
                default:
                    break;
            }
        }
    }
    
    public function testPHPCodeAction() {
        $code = "if(true){}else{" . $_POST['code'] . "}";
        
        $return = eval($code);
        
        $response = array(
            'code' => 1,
            'message' => 'Valid code'
        );

        if ($return === FALSE) {
            $response['code'] = 0;
            $response['message'] = "Syntax error";
        }
        
        $options = array(
            'response' => json_encode($response)
        );

        $this->options = array_merge($this->options, $options);
    }

    public static function getMonths($start, $end) {
        $startParsed = date_parse_from_format('Y-m-d', $start);
        $startMonth = $startParsed['month'];
        $startYear = $startParsed['year'];
        $startDay = $startParsed['day'];

        $endParsed = date_parse_from_format('Y-m-d', $end);
        $endDay = $endParsed['day'];
        $endMonth = $endParsed['month'];
        $endYear = $endParsed['year'];

        $nmonths = (($endYear - $startYear) * 12) + ($endMonth - $startMonth);
        if (($endDay - $startDay) > 10) {
            $nmonths += 1;
        }
        return $nmonths;
    }


}

?>
