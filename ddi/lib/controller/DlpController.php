<?php

class DlpController extends Controller {

    public function __construct($name = null) {
        parent::__construct($name);
    }

    /*
     * Depending on the passed action it runs a method.
     * It must to recieve the showAction method at least.
     */

    protected function showAction() {

        $model = new PolicyModel();
        $policies = $model->getPolicies($_SESSION['license']);

        $options = array(
            'policies' => $policies,
        );
        $this->options = array_merge($this->options, $options);
    }

    protected function showRulesAction() {

        $model = new RuleModel();

        $rules = $model->getRules($_SESSION['license']);

        $options = array(
            'rules' => $rules,
        );
        $this->options = array_merge($this->options, $options);
    }

    protected function showFilesAction() {

        $file_model = new FileModel();
        $files = $file_model->getFiles($_SESSION['license']);

        $policy_model = new PolicyModel();
        $all_policies = $policy_model->getPolicies($_SESSION['license']);

        $file_policies = array();
        foreach ($files as $file_id => $file_object) {
            $policies = array();
            foreach ($all_policies as $policy_id => $policy_object) {
                $policies[$policy_id]['name'] = $policy_object['name'];
                $policies[$policy_id]['checked'] = in_array((string) $file_object['_id'], $policy_object['files']);
            }
            $file_policies[$file_id] = $policies;
        }
        
        $new_files = array();
        if (!empty($_SESSION['new_files'])) {
            $new_files = $_SESSION['new_files'];
            $_SESSION['new_files'] = array();
        }

        $options = array(
            'files' => $files,
            'new_files' => $new_files,
            'file_policies' => $file_policies,
            'policies' => $all_policies,
            'hostname' => $_SERVER['SERVER_NAME'],
        );
        $this->options = array_merge($this->options, $options);
    }

    protected function showNetworkPlacesAction() {

        $model = new NetworkPlaceModel();
        $network_places = $model->getNetworkPlaces($_SESSION['license']);

        $group_model = new GroupModel();
        $default_group = $group_model->getDefaultGroup();

        $enabled = false;
        if ($default_group != null) {
            $endpoint_modules = $default_group['endpoint_modules'];
            $enabled = in_array('network device src', $endpoint_modules);
        }

        $options = array(
            'enabled' => $enabled,
            'network_places' => $network_places,
        );
        $this->options = array_merge($this->options, $options);
    }

    protected function showApplicationsAction() {

        $application_model = new ApplicationModel();

        $applications = $application_model->getApplications();

        $options = array(
            'applications' => $applications,
        );
        $this->options = array_merge($this->options, $options);
    }

    protected function showAdvancedAction() {
        $all_enpoint_modules = array();
        $all_enpoint_modules['source']['network device src'] = 'Network Device';
        //$all_enpoint_modules['sink']['desktop'] = 'Desktop';
        $all_enpoint_modules['sink']['dropbox'] = 'Dropbox';
        $all_enpoint_modules['sink']['google drive'] = 'Google Drive';
        $all_enpoint_modules['sink']['network device dst'] = 'Network Device';
        $all_enpoint_modules['sink']['pendrive'] = 'Pendrive';
        $all_enpoint_modules['sink']['printer'] = 'Printer (experimental)';
        $all_enpoint_modules['sink']['skydrive'] = 'Skydrive';
        $all_enpoint_modules['pipe']['clipboard image'] = 'Clipboard Image* (experimental)';
        $all_enpoint_modules['pipe']['clipboard text'] = 'Clipboard Text';
        $all_enpoint_modules['pipe']['keylogger'] = 'Keylogger';

        $cloud_model = new CloudModel();
        $cloud_user = $cloud_model->getCloudUserByLicense($_SESSION['license']);

        $group_model = new GroupModel();
        $groups = $group_model->getGroups();
        $default_group = $group_model->getDefaultGroup();
        if ($default_group != null) {
            $screenshot_severity = $default_group['screenshot_severity'];
            $endpoint_modules = $default_group['endpoint_modules'];
        } else {
            $screenshot_severity = 'none';
            $endpoint_modules = array();
        }
        
        foreach ($groups as $key => $group_obj) {
            $groups_array[$key] = array('name' => $group_obj['name'], 'encrypt' => $group_obj['encrypted_files']);
        }

        $enpoint_modules_array = array();
        foreach ($all_enpoint_modules as $endpoint_module_key => $endpoint_module_group) {
            foreach ($endpoint_module_group as $endpoint_module => $endpoint_module_name) {
                if (in_array($endpoint_module, $endpoint_modules)) {
                    $enpoint_modules_array[$endpoint_module_key][$endpoint_module]['name'] = $endpoint_module_name;
                    $enpoint_modules_array[$endpoint_module_key][$endpoint_module]['check'] = 1;
                } else {
                    $enpoint_modules_array[$endpoint_module_key][$endpoint_module]['name'] = $endpoint_module_name;
                    $enpoint_modules_array[$endpoint_module_key][$endpoint_module]['check'] = 0;
                }
            }
        }

        $options = array(
            'groups' => $groups_array,
            'screenshot_severity' => $screenshot_severity,
            'endpoint_modules' => $enpoint_modules_array,
            'user_type' => $cloud_user['type'],
        );
        $this->options = array_merge($this->options, $options);
    }

    protected function deletePolicyAction() {

        $model = new PolicyModel();
        $policy = $model->getPolicy($_GET['idPolicy']);
        $model->deletePolicy($_GET['idPolicy']);

        $group_model = new GroupModel();
        foreach (array_keys($policy['groups']) as $group) {
            $now = new MongoDate();
            $created_time = $group_model->getCreatedTime($group);
            $last_seconds = 300 - ($now->sec - $created_time->sec);
            if ($last_seconds < 0) {
                $last_seconds = 0;
            }

            $group_dlp_config = ApiController::getDLPGroupConfig($_SESSION['license'], $group);

            $amqp_model = new AMQPModel();
            $amqp_model->setExchange('server');
            $amqp_model->setRoutingKey($group);
            $amqp_model->setMessageModule('dlp');
            $amqp_model->setMessageCommand('update');
            $amqp_model->setMessageArgs($group_dlp_config);
            $amqp_model->setMessageDelay($last_seconds);
            $amqp_model->sendMessage();
        }

        header('location:/ddi/?module=dlp&action=show');
    }

    protected function deleteRuleAction() {
        $rule_id = $_GET['idRule'];

        $rule_model = new RuleModel();
        $rule_model->deleteRule($rule_id);

        $policy_model = new PolicyModel();
        $policies = $policy_model->gePoliciesByRule($rule_id);

        $groups = array();
        foreach ($policies as $policy_object) {
            foreach ($policy_object['groups'] as $group) {
                $groups[] = $group['group_id'];
            }
            $policy_object['rules'] = array_diff($policy_object['rules'], array($rule_id));
            $policy_model->updatePolicy($policy_object);
        }

        foreach (array_unique($groups) as $group) {
            $group_dlp_config = ApiController::getDLPGroupConfig($_SESSION['license'], $group);

            $amqp_model = new AMQPModel();
            $amqp_model->setExchange('server');
            $amqp_model->setRoutingKey($group);
            $amqp_model->setMessageModule('dlp');
            $amqp_model->setMessageCommand('update');
            $amqp_model->setMessageArgs($group_dlp_config);
            $amqp_model->sendMessage();
        }

        header('location:/ddi/?module=dlp&action=showRules');
    }

    protected function deleteFileAction() {
        $file_id = $_GET['idFile'];

        $file_model = new FileModel();
        $file_model->deleteFile($file_id);

        $policy_model = new PolicyModel();
        $policies = $policy_model->gePoliciesByFile($file_id);

        $groups = array();
        foreach ($policies as $policy_object) {
            foreach ($policy_object['groups'] as $group) {
                $groups[] = $group['group_id'];
            }
            $policy_object['files'] = array_diff($policy_object['files'], array($file_id));
            $policy_model->updatePolicy($policy_object);
        }

        foreach (array_unique($groups) as $group) {
            $group_dlp_config = ApiController::getDLPGroupConfig($_SESSION['license'], $group);

            $amqp_model = new AMQPModel();
            $amqp_model->setExchange('server');
            $amqp_model->setRoutingKey($group);
            $amqp_model->setMessageModule('dlp');
            $amqp_model->setMessageCommand('update');
            $amqp_model->setMessageArgs($group_dlp_config);
            $amqp_model->sendMessage();
        }

        header('location:/ddi/?module=dlp&action=showFiles');
    }

    protected function deleteNetworkPlaceAction() {
        $network_place_id = $_GET['idNetworkPlace'];

        $network_place_model = new NetworkPlaceModel();
        $network_place_model->deleteNetworkPlace($network_place_id);

        $policy_model = new PolicyModel();
        $policies = $policy_model->gePoliciesByNetworkPlace($network_place_id);

        $groups = array();
        foreach ($policies as $policy_object) {
            foreach ($policy_object['groups'] as $group) {
                $groups[] = $group['group_id'];
            }
            $policy_object['network_places'] = array_diff($policy_object['network_places'], array($network_place_id));
            $policy_model->updatePolicy($policy_object);
        }

        foreach (array_unique($groups) as $group) {
            $group_dlp_config = ApiController::getDLPGroupConfig($_SESSION['license'], $group);

            $amqp_model = new AMQPModel();
            $amqp_model->setExchange('server');
            $amqp_model->setRoutingKey($group);
            $amqp_model->setMessageModule('dlp');
            $amqp_model->setMessageCommand('update');
            $amqp_model->setMessageArgs($group_dlp_config);
            $amqp_model->sendMessage();
        }

        header('location:/ddi/?module=dlp&action=showNetworkPlaces');
    }

    protected function deleteApplicationAction() {
        $application_id = $_GET['id'];

        $application_model = new ApplicationModel();
        $application_model->deleteApplication($application_id);

        $policy_model = new PolicyModel();
        $policies = $policy_model->gePoliciesByApplication($application_id);

        $groups = array();
        foreach ($policies as $policy_object) {
            foreach ($policy_object['groups'] as $group) {
                $groups[] = $group['group_id'];
            }
            $policy_object['applications'] = array_diff($policy_object['applications'], array($application_id));
            $policy_model->updatePolicy($policy_object);
        }

        foreach (array_unique($groups) as $group) {
            $group_dlp_config = ApiController::getDLPGroupConfig($_SESSION['license'], $group);

            $amqp_model = new AMQPModel();
            $amqp_model->setExchange('server');
            $amqp_model->setRoutingKey($group);
            $amqp_model->setMessageModule('dlp');
            $amqp_model->setMessageCommand('update');
            $amqp_model->setMessageArgs($group_dlp_config);
            $amqp_model->sendMessage();
        }

        header('location:/ddi/?module=dlp&action=showApplications');
    }

    protected function editPolicyAction() {

        $main_model = new MainModel();
        $mode = $main_model->getConfValue('mode');

        $policy_id = $_GET['idPolicy'];
        $policy_model = new PolicyModel();
        $policy = $policy_model->getPolicy($policy_id);

        $group_model = new GroupModel();
        $groups = $group_model->getGroups();
        $edit_groups = Array();
        foreach ($groups as $group_obj) {
            if (in_array($group_obj['name'], array_keys($policy['groups']))) {
                $group_obj['checked'] = true;
                $group_obj['action'] = $policy['groups'][$group_obj['name']]['action'];
                $group_obj['severity'] = $policy['groups'][$group_obj['name']]['severity'];
            }
            $edit_groups[] = $group_obj;
        }

        $concept_model = new ConceptModel();
        $concepts = $concept_model->getConcepts();

        $subconcept_model = new SubconceptModel();
        foreach ($concepts as $id_concept => $concept_obj) {
            if (isset($policy['concepts'])) {
                if (in_array($id_concept, $policy['concepts'])) {
                    $concept_obj['checked'] = true;
                }
            }

            $subconcepts = $subconcept_model->getSubConceptsByIdConcept($id_concept);
            $edit_subconcepts = Array();
            foreach ($subconcepts as $id_subconcept => $subconcept_obj) {
                if (in_array($id_subconcept, $policy['subconcepts'])) {
                    $subconcept_obj['checked'] = true;
                }
                $edit_subconcepts[$id_subconcept] = $subconcept_obj;
            }
            $concepts_subconcepts[$id_concept] = Array("concept" => $concept_obj, "subconcepts" => $edit_subconcepts);
        }

        $rule_model = new RuleModel();
        $rules = $rule_model->getRules($_SESSION['license']);
        $edit_rules = Array();
        foreach ($rules as $id_rule => $rule_obj) {
            if (in_array($id_rule, $policy['rules'])) {
                $rule_obj['checked'] = true;
            }
            $edit_rules[$id_rule] = $rule_obj;
        }

        $file_model = new FileModel();
        $files = $file_model->getFiles($_SESSION['license']);
        $edit_files = Array();
        foreach ($files as $id_file => $file_obj) {
            if (in_array($id_file, $policy['files'])) {
                $file_obj['checked'] = true;
            }
            $edit_files[$id_file] = $file_obj;
        }

        $network_places = null;
        $default_group = $group_model->getDefaultGroup();
        if ($default_group != null) {
            $endpoint_modules = $default_group['endpoint_modules'];
            if (in_array('network device src', $endpoint_modules)) {
                $network_place_model = new NetworkPlaceModel();
                $network_places = $network_place_model->getNetworkPlaces($_SESSION['license']);
                $edit_network_places = Array();
                foreach ($network_places as $id_network_place => $network_place_obj) {
                    if (in_array($id_network_place, $policy['network_places'])) {
                        $network_place_obj['checked'] = true;
                    }
                    $edit_network_places[$id_network_place] = $network_place_obj;
                }
            }
        }

        $application_model = new ApplicationModel();
        $applications = $application_model->getApplications();
        $edit_applications = Array();
        foreach ($applications as $application_id => $application_obj) {
            if (in_array($application_id, $policy['applications'])) {
                $application_obj['checked'] = true;
            }
            $edit_applications[$application_id] = $application_obj;
        }

        $options = array(
            'mode' => $mode,
            'step' => $_GET['id'] < 0 || $_GET['id'] > 5 ? 0 : $_GET['id'],
            'concepts_subconcepts' => $concepts_subconcepts,
            'rules' => $edit_rules,
            'nbr_rules' => sizeof($edit_rules),
            'files' => $edit_files,
            'nbr_files' => sizeof($edit_files),
            'network_places' => $edit_network_places,
            'nbr_network_places' => sizeof($edit_network_places),
            'applications' => $edit_applications,
            'groups' => $edit_groups,
            'ngroups' => count($groups),
            'policy' => $policy,
            'output' => print_r($policy, TRUE)
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function editRuleAction() {
        $rule_model = new RuleModel();
        $rule_object = $rule_model->getRule($_GET['idRule']);

        $policy_model = new PolicyModel();
        $all_policies = $policy_model->getPolicies($_SESSION['license']);
        $rule_policies = $policy_model->gePoliciesByRule($_GET['idRule']);

        $rule_policies_id = array();
        foreach ($rule_policies as $policy_object) {
            $rule_policies_id[] = (string) $policy_object['_id'];
        }

        $policies = array();
        foreach ($all_policies as $id => $policy) {
            $policies[$id]['name'] = $policy['name'];
            $policies[$id]['checked'] = in_array($id, $rule_policies_id);
        }

        $options = array(
            'ruleid' => $rule_object['_id'],
            'rule' => $rule_object['rule'],
            'description' => $rule_object['description'],
            'verify' => base64_decode($rule_object['verify']),
            'policies' => $policies,
            'nbr_policies' => count($policies),
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function editFileAction() {
        $file_model = new FileModel();
        $file_object = $file_model->getFile($_GET['idFile']);

        $policy_model = new PolicyModel();
        $all_policies = $policy_model->getPolicies($_SESSION['license']);
        $file_policies = $policy_model->gePoliciesByFile($_GET['idFile']);

        $file_policies_id = array();
        foreach ($file_policies as $policy_object) {
            $file_policies_id[] = (string) $policy_object['_id'];
        }

        $policies = array();
        foreach ($all_policies as $id => $policy) {
            $policies[$id]['name'] = $policy['name'];
            $policies[$id]['checked'] = in_array($id, $file_policies_id);
        }

        $options = array(
            'file' => $file_object,
            'policies' => $policies,
            'nbr_policies' => count($policies),
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function editNetworkPlaceAction() {
        $network_place_model = new NetworkPlaceModel();
        $network_place_object = $network_place_model->getNetworkPlace($_GET['idNetworkPlace']);

        $policy_model = new PolicyModel();
        $all_policies = $policy_model->getPolicies($_SESSION['license']);
        $network_place_policies = $policy_model->gePoliciesByNetworkPlace($_GET['idNetworkPlace']);

        $network_place_policies_id = array();
        foreach ($network_place_policies as $policy_object) {
            $network_place_policies_id[] = (string) $policy_object['_id'];
        }

        $policies = array();
        foreach ($all_policies as $id => $policy) {
            $policies[$id]['name'] = $policy['name'];
            $policies[$id]['checked'] = in_array($id, $network_place_policies_id);
        }

        $options = array(
            'network_place' => $network_place_object,
            'policies' => $policies,
            'nbr_policies' => count($policies),
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function editApplicationAction() {
        $application_model = new ApplicationModel();
        $application_oject = $application_model->getApplication($_GET['id']);

        $policy_model = new PolicyModel();
        $all_policies = $policy_model->getPolicies($_SESSION['license']);
        $application_policies = $policy_model->gePoliciesByApplication($_GET['id']);

        $application_policies_id = array();
        foreach ($application_policies as $policy_object) {
            $application_policies_id[] = (string) $policy_object['_id'];
        }

        $policies = array();
        foreach ($all_policies as $id => $policy) {
            $policies[$id]['name'] = $policy['name'];
            $policies[$id]['checked'] = in_array($id, $application_policies_id);
        }

        $options = array(
            'application' => $application_oject,
            'policies' => $policies,
            'nbr_policies' => count($policies),
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function updatePolicyAction() {

        foreach ($_POST['groups'] as $group) {
            $groups[$group] = Array("group_id" => $group, "action" => $_POST['actions'][$group], "severity" => $_POST['severities'][$group]);
        }

        $policy = Array();
        $policy['_id'] = new MongoId($_POST['idPolicy']);
        $policy['license'] = $_SESSION['license'];
        $policy['name'] = $_POST['policy_name'];
        $policy['description'] = $_POST['policy_description'];
        $policy['enviroment'] = $_POST['policy_enviroment'];
        $policy['concepts'] = $_POST['policy_concepts'];
        $policy['subconcepts'] = $_POST['policy_subconcepts'];
        $policy['rules'] = $_POST['policy_rules'];
        $policy['files'] = $_POST['policy_files'];
        $policy['network_places'] = $_POST['policy_network_places'];
        $policy['applications'] = $_POST['policy_applications'];
        $policy['groups'] = $groups;
        $policy['email'] = $_POST['policy_email'];

        $policy_model = new PolicyModel();
        $old_policy = $policy_model->getPolicy($_POST['idPolicy']);
        $output = $policy_model->updatePolicy($policy);
        $new_policy = $policy_model->getPolicy($_POST['idPolicy']);

        $final_groups = array();

        if ($old_policy['groups'] != null) {
            foreach (array_keys($old_policy['groups']) as $value) {
                $final_groups[] = $value;
            }
        }

        if ($new_policy['groups'] != null) {
            foreach (array_keys($new_policy['groups']) as $value) {
                $final_groups[] = $value;
            }
        }

        $group_model = new GroupModel();
        foreach (array_unique($final_groups) as $group) {

            $now = new MongoDate();
            $created_time = $group_model->getCreatedTime($group);
            $last_seconds = 300 - ($now->sec - $created_time->sec);
            if ($last_seconds < 0) {
                $last_seconds = 0;
            }

            $group_dlp_config = ApiController::getDLPGroupConfig($_SESSION['license'], $group);

            $amqp_model = new AMQPModel();
            $amqp_model->setExchange('server');
            $amqp_model->setRoutingKey($group);
            $amqp_model->setMessageModule('dlp');
            $amqp_model->setMessageCommand('update');
            $amqp_model->setMessageArgs($group_dlp_config);
            $amqp_model->setMessageDelay($last_seconds);
            $amqp_model->sendMessage();
        }

        $options = array(
            'msg' => $output
        );

        $this->options = array_merge($this->options, $options);
        header('location:/ddi/?module=dlp&action=show');
    }

    protected function updateRuleAction() {

        $new_data = array(
            'rule' => $_GET['rule'],
            'verify' => base64_encode($_GET['verify'])
        );

        $rule_model = new RuleModel();
        $rule_model->updateRule($_GET['id'], $new_data);

        $policy_model = new PolicyModel();
        $all_policies = $policy_model->getPolicies($_SESSION['license']);

        if ($all_policies->count() > 0) {

            $groups = array();

            $policies = $policy_model->gePoliciesByRule($_GET['id']);
            $new_policies = isset($_GET['policies']) ? $_GET['policies'] : array();

            foreach ($policies as $policy_object) {
                foreach ($policy_object['groups'] as $group) {
                    $groups[] = $group['group_id'];
                }
            }

            foreach ($all_policies as $policy_id => $policy_object) {
                if (in_array($policy_id, $new_policies)) {
                    $policy_object['rules'][] = $_GET['id'];
                    foreach ($policy_object['groups'] as $group) {
                        $groups[] = $group['group_id'];
                    }
                } elseif (in_array($_GET['id'], $policy_object['rules'])) {
                    $policy_object['rules'] = array_diff($policy_object['rules'], array($_GET['id']));
                }
                $policy_model->updatePolicy($policy_object);
            }

            foreach (array_unique($groups) as $group) {
                $group_dlp_config = ApiController::getDLPGroupConfig($_SESSION['license'], $group);

                $amqp_model = new AMQPModel();
                $amqp_model->setExchange('server');
                $amqp_model->setRoutingKey($group);
                $amqp_model->setMessageModule('dlp');
                $amqp_model->setMessageCommand('update');
                $amqp_model->setMessageArgs($group_dlp_config);
                $amqp_model->sendMessage();
            }
        }

        header('location:/ddi/?module=dlp&action=showRules');
    }

    protected function updateFileAction() {

        $new_data = array(
            'name' => $_GET['name']
        );

        $file_model = new FileModel();
        $file_model->updateFile($_GET['id'], $new_data);

        $policy_model = new PolicyModel();
        $all_policies = $policy_model->getPolicies($_SESSION['license']);

        if ($all_policies->count() > 0) {

            $groups = array();

            $policies = $policy_model->gePoliciesByFile($_GET['id']);
            $new_policies = isset($_GET['policies']) ? $_GET['policies'] : array();

            foreach ($policies as $policy_object) {
                foreach ($policy_object['groups'] as $group) {
                    $groups[] = $group['group_id'];
                }
            }

            foreach ($all_policies as $policy_id => $policy_object) {
                if (in_array($policy_id, $new_policies)) {
                    $policy_object['files'][] = $_GET['id'];
                    foreach ($policy_object['groups'] as $group) {
                        $groups[] = $group['group_id'];
                    }
                } elseif (in_array($_GET['id'], $policy_object['files'])) {
                    $policy_object['files'] = array_diff($policy_object['files'], array($_GET['id']));
                }
                $policy_model->updatePolicy($policy_object);
            }

            foreach (array_unique($groups) as $group) {
                $group_dlp_config = ApiController::getDLPGroupConfig($_SESSION['license'], $group);

                $amqp_model = new AMQPModel();
                $amqp_model->setExchange('server');
                $amqp_model->setRoutingKey($group);
                $amqp_model->setMessageModule('dlp');
                $amqp_model->setMessageCommand('update');
                $amqp_model->setMessageArgs($group_dlp_config);
                $amqp_model->sendMessage();
            }
        }

        header('location:/ddi/?module=dlp&action=showFiles');
    }

    protected function updateNetworkPlaceAction() {

        $new_data = array(
            'network_uri' => $_GET['network_uri'],
        );

        $network_place_model = new NetworkPlaceModel();
        $network_place_model->updateNetworkPlace($_GET['id'], $new_data);

        $policy_model = new PolicyModel();
        $all_policies = $policy_model->getPolicies($_SESSION['license']);

        if ($all_policies->count() > 0) {

            $groups = array();

            $policies = $policy_model->gePoliciesByNetworkPlace($_GET['id']);
            $new_policies = isset($_GET['policies']) ? $_GET['policies'] : array();

            foreach ($policies as $policy_object) {
                foreach ($policy_object['groups'] as $group) {
                    $groups[] = $group['group_id'];
                }
            }

            foreach ($all_policies as $policy_id => $policy_object) {
                if (in_array($policy_id, $new_policies)) {
                    $policy_object['network_places'][] = $_GET['id'];
                    foreach ($policy_object['groups'] as $group) {
                        $groups[] = $group['group_id'];
                    }
                } elseif (in_array($_GET['id'], $policy_object['network_places'])) {
                    $policy_object['network_places'] = array_diff($policy_object['network_places'], array($_GET['id']));
                }
                $policy_model->updatePolicy($policy_object);
            }

            foreach (array_unique($groups) as $group) {
                $group_dlp_config = ApiController::getDLPGroupConfig($_SESSION['license'], $group);

                $amqp_model = new AMQPModel();
                $amqp_model->setExchange('server');
                $amqp_model->setRoutingKey($group);
                $amqp_model->setMessageModule('dlp');
                $amqp_model->setMessageCommand('update');
                $amqp_model->setMessageArgs($group_dlp_config);
                $amqp_model->sendMessage();
            }
        }

        header('location:/ddi/?module=dlp&action=showNetworkPlaces');
    }

    protected function updateApplicationAction() {
        if (isset($_POST['id']) && isset($_POST['application']) && isset($_POST['description'])) {
            $new_data = array(
                'application' => $_POST['application']
            );

            $application_model = new ApplicationModel();
            $application_model->updateApplication($_POST['id'], $new_data);

            $policy_model = new PolicyModel();
            $all_policies = $policy_model->getPolicies($_SESSION['license']);

            if ($all_policies->count() > 0) {

                $groups = array();

                $policies = $policy_model->gePoliciesByApplication($_POST['id']);
                $new_policies = isset($_POST['policies']) ? $_POST['policies'] : array();

                foreach ($policies as $policy_object) {
                    foreach ($policy_object['groups'] as $group) {
                        $groups[] = $group['group_id'];
                    }
                }

                foreach ($all_policies as $policy_id => $policy_object) {
                    if (in_array($policy_id, $new_policies)) {
                        $policy_object['applications'][] = $_POST['id'];
                        foreach ($policy_object['groups'] as $group) {
                            $groups[] = $group['group_id'];
                        }
                    } elseif (in_array($_POST['id'], $policy_object['applications'])) {
                        $policy_object['applications'] = array_diff($policy_object['applications'], array($_POST['id']));
                    }
                    $policy_model->updatePolicy($policy_object);
                }

                foreach (array_unique($groups) as $group) {
                    $group_dlp_config = ApiController::getDLPGroupConfig($_SESSION['license'], $group);

                    $amqp_model = new AMQPModel();
                    $amqp_model->setExchange('server');
                    $amqp_model->setRoutingKey($group);
                    $amqp_model->setMessageModule('dlp');
                    $amqp_model->setMessageCommand('update');
                    $amqp_model->setMessageArgs($group_dlp_config);
                    $amqp_model->sendMessage();
                }
            }
        }

        header('location:/ddi/?module=dlp&action=showApplications');
    }

    protected function updateAdvancedAction() {

        $groups = $_GET['groups'];
        $encrypted_groups = $_GET['encrypted_files'];
        $screenshot_severity = $_GET['screenshot_severity'];
        $endpoint_modules = $_GET['endpoint_modules'];

        $group_model = new GroupModel();

        if (isset($encrypted_groups)) {
            foreach ($groups as $group) {
                if (in_array($group, $encrypted_groups)) {
                    $group_model->setEncryptedFiles($_SESSION['license'], $group, 1);
                } else {
                    $group_model->setEncryptedFiles($_SESSION['license'], $group, 0);
                }
            }
        } else {
            foreach ($groups as $group) {
                $group_model->setEncryptedFiles($_SESSION['license'], $group, 0);
            }
        }

        if (isset($screenshot_severity)) {
            $group_model->setScreenShotSeverity($_SESSION['license'], $screenshot_severity);
        }

        if (isset($endpoint_modules)) {
            $group_model->setEndpointModules($_SESSION['license'], $endpoint_modules);
        }

        foreach ($groups as $group) {
            $group_dlp_config = ApiController::getDLPGroupConfig($_SESSION['license'], $group);

            $amqp_model = new AMQPModel();
            $amqp_model->setExchange('server');
            $amqp_model->setRoutingKey($group);
            $amqp_model->setMessageModule('dlp');
            $amqp_model->setMessageCommand('update');
            $amqp_model->setMessageArgs($group_dlp_config);
            $amqp_model->sendMessage();
        }

        header('location:/ddi/?module=dlp&action=showAdvanced');
    }

    protected function createPolicyAction() {
        foreach ($_POST['groups'] as $group) {
            $groups[$group] = array(
                'group_id' => $group,
                'action' => $_POST['actions'][$group],
                'severity' => $_POST['severities'][$group],
            );
        }

        $policy = Array();
        $policy['license'] = $_SESSION['license'];
        $policy['name'] = $_POST['policy_name'];
        $policy['description'] = $_POST['policy_description'];
        $policy['enviroment'] = isset($_POST['policy_enviroment']) ? $_POST['policy_enviroment'] : array();
        $policy['concepts'] = isset($_POST['policy_concepts']) ? $_POST['policy_concepts'] : array();
        $policy['subconcepts'] = isset($_POST['policy_subconcepts']) ? $_POST['policy_subconcepts'] : array();
        $policy['rules'] = isset($_POST['policy_rules']) ? $_POST['policy_rules'] : array();
        $policy['files'] = isset($_POST['policy_files']) ? $_POST['policy_files'] : array();
        $policy['applications'] = isset($_POST['policy_applications']) ? $_POST['policy_applications'] : array();
        $policy['network_places'] = isset($_POST['policy_network_places']) ? $_POST['policy_network_places'] : array();
        $policy['groups'] = $groups;
        $policy['email'] = $_POST['policy_email'];

        $policy_model = new PolicyModel();


        $policy_model->newPolicy($policy);

        $policies_cursor = $policy_model->getPolicies($_SESSION['license']);

        $cloud_model = new CloudModel();
        $cloud_user = $cloud_model->getCloudUser($_SESSION['license']);

        if(!$cloud_user['1st_policy']){
            if($policies_cursor->count() > 0){
                $cloud_model->updateCloudUser($_SESSION['license'], array('1stpolicy' => true));
            }
        }



        $group_model = new GroupModel();
        foreach ($_POST['groups'] as $group) {

            $now = new MongoDate();
            $created_time = $group_model->getCreatedTime($group);
            $last_seconds = 300 - ($now->sec - $created_time->sec);
            if ($last_seconds < 0) {
                $last_seconds = 0;
            }

            $group_dlp_config = ApiController::getDLPGroupConfig($_SESSION['license'], $group);

            $amqp_model = new AMQPModel();
            $amqp_model->setExchange('server');
            $amqp_model->setRoutingKey($group);
            $amqp_model->setMessageModule('dlp');
            $amqp_model->setMessageCommand('update');
            $amqp_model->setMessageArgs($group_dlp_config);
            $amqp_model->setMessageDelay($last_seconds);
            $amqp_model->sendMessage();
        }

        header('location:/ddi/?module=dlp&action=show');
    }

    protected function createRuleAction() {

        $rule_model = new RuleModel();
        $rule_id = $rule_model->createRule($_GET['rule'], $_GET['description'], base64_encode($_GET['verify']));

        if (isset($_GET['policies']) && !empty($_GET['policies'])) {
            $policy_model = new PolicyModel();
            $groups = array();
            foreach ($_GET['policies'] as $policy_id) {
                $policy_object = $policy_model->getPolicy($policy_id);
                $policy_object['rules'][] = $rule_id;
                foreach ($policy_object['groups'] as $group) {
                    $groups[] = $group['group_id'];
                }
                $policy_model->updatePolicy($policy_object);
            }

            foreach (array_unique($groups) as $group) {
                $group_dlp_config = ApiController::getDLPGroupConfig($_SESSION['license'], $group);

                $amqp_model = new AMQPModel();
                $amqp_model->setExchange('server');
                $amqp_model->setRoutingKey($group);
                $amqp_model->setMessageModule('dlp');
                $amqp_model->setMessageCommand('update');
                $amqp_model->setMessageArgs($group_dlp_config);
                $amqp_model->sendMessage();
            }
        }

        header('location:/ddi/?module=dlp&action=showRules');
    }

    protected function createFileAction() {

        /**
         * &XDEBUG_SESSION_START=netbeans-xdebug
         */
        $targetDir = "/tmp/uploads";

        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 5 * 3600; // Temp file age in seconds
        // 5 minutes execution time
        @set_time_limit(5 * 60);

        // Uncomment this one to fake upload time
        // usleep(5000);
        // Get parameters
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
        //$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
        // Clean the fileName for security reasons
        $fileName = preg_replace('/[^\w\._]+/', '_', isset($_REQUEST["name"]) ? $_REQUEST["name"] : '');

        // Make sure the fileName is unique but only if chunking is disabled
        if ($chunks < 2 && file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName)) {
            $ext = strrpos($fileName, '.');
            $fileName_a = substr($fileName, 0, $ext);
            $fileName_b = substr($fileName, $ext);

            $count = 1;
            while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
                $count++;

            $fileName = $fileName_a . '_' . $count . $fileName_b;
        }

        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

        // Create target dir
        if (!file_exists($targetDir))
            @mkdir($targetDir);

        // Remove old temp files	
        if ($cleanupTargetDir && is_dir($targetDir) && ($dir = opendir($targetDir))) {
            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

                // Remove temp file if it is older than the max age and is not the current file
                if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
                    @unlink($tmpfilePath);
                }
            }

            closedir($dir);
        } else
            die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');


        // Look for the content type header
        if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
            $contentType = $_SERVER["HTTP_CONTENT_TYPE"];

        if (isset($_SERVER["CONTENT_TYPE"]))
            $contentType = $_SERVER["CONTENT_TYPE"];

        // Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
        if (strpos($contentType, "multipart") !== false) {
            if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
                // Open temp file
                $out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
                if ($out) {
                    // Read binary input stream and append it to temp file
                    $in = fopen($_FILES['file']['tmp_name'], "rb");

                    if ($in) {
                        while ($buff = fread($in, 4096))
                            fwrite($out, $buff);
                    } else
                        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                    fclose($in);
                    fclose($out);
                    @unlink($_FILES['file']['tmp_name']);
                } else
                    die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            } else
                die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
        } else {
            // Open temp file
            $out = fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
            if ($out) {
                // Read binary input stream and append it to temp file
                $in = fopen("php://input", "rb");

                if ($in) {
                    while ($buff = fread($in, 4096))
                        fwrite($out, $buff);
                } else
                    die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

                fclose($in);
                fclose($out);
            } else
                die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
        }

        // Check if file has been uploaded
        if (!$chunks || $chunk == $chunks - 1) {
            rename("{$filePath}.part", $filePath);

            $sha1 = sha1_file($filePath);
            $md5 = md5_file($filePath);
            $ssdeep = generateSsdeep($filePath);

            unlink($filePath);

            $file_model = new FileModel();
            $file_id = $file_model->createFile($_REQUEST['dwFileName'], $sha1, $md5, $ssdeep);

            $file_object = array();
            $file_object['name'] = $_REQUEST['dwFileName'];
            
            $_SESSION['new_files'][$file_id] = $file_object;
        }

        // Return JSON-RPC response
        die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
    }

    protected function createNetworkPlaceAction() {

        $network_place_model = new NetworkPlaceModel();
        $network_place_id = $network_place_model->createNetworkPlace($_GET['network_uri'], $_GET['description']);

        if (isset($_GET['policies']) && !empty($_GET['policies'])) {
            $policy_model = new PolicyModel();
            $groups = array();
            foreach ($_GET['policies'] as $policy_id) {
                $policy_object = $policy_model->getPolicy($policy_id);
                $policy_object['network_places'][] = $network_place_id;
                foreach ($policy_object['groups'] as $group) {
                    $groups[] = $group['group_id'];
                }
                $policy_model->updatePolicy($policy_object);
            }

            foreach (array_unique($groups) as $group) {
                $group_dlp_config = ApiController::getDLPGroupConfig($_SESSION['license'], $group);

                $amqp_model = new AMQPModel();
                $amqp_model->setExchange('server');
                $amqp_model->setRoutingKey($group);
                $amqp_model->setMessageModule('dlp');
                $amqp_model->setMessageCommand('update');
                $amqp_model->setMessageArgs($group_dlp_config);
                $amqp_model->sendMessage();
            }
        }

        header('location:/ddi/?module=dlp&action=showNetworkPlaces');
    }

    protected function createApplicationAction() {
        if (isset($_POST['application']) && isset($_POST['description'])) {
            $application_model = new ApplicationModel();
            $application_id = $application_model->createApplication($_POST['application'], $_POST['description']);

            if (isset($_POST['policies'])) {
                $policy_model = new PolicyModel();
                $groups = array();
                foreach ($_POST['policies'] as $policy_id) {
                    $policy_object = $policy_model->getPolicy($policy_id);
                    $policy_object['applications'][] = $application_id;
                    foreach ($policy_object['groups'] as $group) {
                        $groups[] = $group['group_id'];
                    }
                    $policy_model->updatePolicy($policy_object);
                }

                foreach (array_unique($groups) as $group) {
                    $group_dlp_config = ApiController::getDLPGroupConfig($_SESSION['license'], $group);

                    $amqp_model = new AMQPModel();
                    $amqp_model->setExchange('server');
                    $amqp_model->setRoutingKey($group);
                    $amqp_model->setMessageModule('dlp');
                    $amqp_model->setMessageCommand('update');
                    $amqp_model->setMessageArgs($group_dlp_config);
                    $amqp_model->sendMessage();
                }
            }
        }

        header('location:/ddi/?module=dlp&action=showApplications');
    }

    protected function newPolicyAction() {

        $main_model = new MainModel();
        $sbmode = $main_model->getConfValue('ddisbmode');
        $mode = $main_model->getConfValue('mode');

        $group_model = new GroupModel();
        if ($sbmode == 'unique') {
            $groups = $group_model->getUniqueDefaultGroup();
        } else {
            $groups = $group_model->getGroups();
        }

        $concept_model = new ConceptModel();
        $concepts = $concept_model->getConcepts();

        $subconcept_model = new SubconceptModel();
        foreach ($concepts as $id_concept => $concept_obj) {
            $subconcepts = $subconcept_model->getSubConceptsByIdConcept($id_concept);
            $concepts_subconcepts[$id_concept] = Array('concept' => $concept_obj, 'subconcepts' => $subconcepts);
        }

        $rule_model = new RuleModel();
        $rules_cursor = $rule_model->getRules($_SESSION['license']);
        $rules = iterator_to_array($rules_cursor);

        $file_model = new FileModel();
        $files_cursor = $file_model->getFiles($_SESSION['license']);
        $files = iterator_to_array($files_cursor);

        $network_places = null;
        $default_group = $group_model->getDefaultGroup();
        if ($default_group != null) {
            $endpoint_modules = $default_group['endpoint_modules'];
            if (in_array('network device src', $endpoint_modules)) {
                $network_place_model = new NetworkPlaceModel();
                $network_places_cursor = $network_place_model->getNetworkPlaces($_SESSION['license']);
                $network_places = iterator_to_array($network_places_cursor);
            }
        }

        $application_model = new ApplicationModel();
        $applications = $application_model->getApplications();

        $options = array(
            'mode' => $mode,
            'concepts_subconcepts' => $concepts_subconcepts,
            'rules' => $rules,
            'nbr_rules' => sizeof($rules),
            'files' => $files,
            'nbr_files' => sizeof($files),
            'network_places' => $network_places,
            'nbr_network_places' => sizeof($network_places),
            'applications' => $applications,
            'groups' => $groups,
            'ngroups' => count($groups));

        $this->options = array_merge($this->options, $options);
    }

    protected function newRuleAction() {
        $policy_model = new PolicyModel();
        $policies = $policy_model->getPolicies($_SESSION['license']);

        $options = array(
            'policies' => $policies,
            'nbr_policies' => $policies->count(),
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function newFileAction() {
        
    }

    protected function newNetworkPlaceAction() {
        $policy_model = new PolicyModel();
        $policies = $policy_model->getPolicies($_SESSION['license']);

        $options = array(
            'policies' => $policies,
            'nbr_policies' => $policies->count(),
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function newApplicationAction() {
        $policy_model = new PolicyModel();
        $policies = $policy_model->getPolicies($_SESSION['license']);

        $options = array(
            'policies' => $policies,
            'nbr_policies' => $policies->count(),
        );

        $this->options = array_merge($this->options, $options);
    }

}

?>
