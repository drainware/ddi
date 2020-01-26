<?php

class AtpController extends Controller {

    private $variables = array(
        "NOPSledLengthMin" => "NOPSledLengthMin",
        "PrivateUsageMax" => "PrivateUsageMax",
        "GenericPreAllocate" => "GenericPreAllocate",
        "SearchString" => "SearchString",
        "SearchMode" => "SearchMode",
        "NullPagePreallocate" => "NullPagePreallocate",
        "Verbose" => "Verbose",
        "util_printf" => "util.printf",
    );

    public function __construct($name = null) {
        parent::__construct($name);
    }

    /*
     * Depending on the passed action it runs a method.
     * It must to recieve the showAction method at least.
     */

    protected function showAction() {
        $app_model = new AppModel();
        $user_apps = $app_model->getApps();
        $predefined_apps = $app_model->getPredefinedApps();

        $tmp_user_apps = array();
        foreach ($user_apps as $user_app_id => $user_app_object) {
            if (isset($user_app_object['editable'])) {
                $predefined_app = $app_model->getPredefinedApp($user_app_object['predefined_app']);
                $user_app_object['name'] = $predefined_app['name'];
                $user_app_object['description'] = $predefined_app['description'];
            }
            $tmp_user_apps[$user_app_id] = $user_app_object;
        }

        $apps = array_merge($tmp_user_apps, iterator_to_array($predefined_apps));

        $options = array(
            "apps" => $apps,
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function newAppAction() {

        $options = array(
            "atp_variables" => $this->variables,
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function createAppAction() {

        $variables = array();
        foreach ($_POST['app_vars'] as $key => $value) {
            switch ($key) {
                case "SearchString":
                    $variables[$key]['type'] = "hex"; //$value['type'];
                    $variables[$key] = $value;
                    break;
                default:
                    $variables[$key]['type'] = "dword"; //$value['type'];
                    $variables[$key]['value'] = isset($value['value']) ? hexdec($value['value']) : 0;
                    break;
            }
        }

        $app_object = array(
            "name" => $_POST['app_name'],
            "description" => $_POST['app_description'],
            "variables" => $variables,
            "force_termination" => (int)$_POST['app_force_termination'],
            "resume_monitoring" => (int)$_POST['app_resume_monitoring'],
            "extensions" => explode(",", str_replace(" ", "", $_POST['app_extensions'])),
            "status" => 0
        );

        $app_model = new AppModel();
        $app_model->newApp($app_object);

        header("location:/ddi/?module=atp&action=show");
    }

    protected function editAppAction() {
        $app_id = $_GET['app_id'];
        $app_model = new AppModel();
        $app = $app_model->getApp($app_id);

        if (!isset($app)) {
            $predefined_app = $app_model->getPredefinedApp($app_id);
            $tmp_app = $app_model->getAppByPredefinedApp($app_id);
            if (isset($predefined_app) && !isset($tmp_app)) {
                $app_id = $app_model->newPredefinedApp($app_id);
                $app = $app_model->getApp($app_id);
            } else {
                $app = $app_model->getApp((string) $tmp_app['_id']);
            }
        }

        foreach ($app['variables'] as $var_name => $var_object) {
            if ($var_object['type'] == 'dword') {
                $app['variables'][$var_name]['value'] = dechex($var_object['value']);
            }
        }
        $app['extensions'] = implode(", ", $app['extensions']);
        if(isset($app['other_extensions'])){
            $app['other_extensions'] = implode(", ", $app['other_extensions']);
        }
        
        $options = array(
            "atp_variables" => $this->variables,
            "app" => $app
        );

        $this->options = array_merge($this->options, $options);
    }

    protected function updateAppAction() {
        $app_data = array();
        
        $app_model = new AppModel();
        $app_object = $app_model->getApp($_POST['app_id']);
        
        $variables = array();
        foreach ($_POST['app_vars'] as $key => $value) {
            switch ($key) {
                case "SearchString":
                    $variables[$key]['type'] = "hex"; //$value['type'];
                    $variables[$key] = $value;
                    break;
                default:
                    $variables[$key]['type'] = "dword"; //$value['type'];
                    $variables[$key]['value'] = isset($value['value']) ? hexdec($value['value']) : 0;
                    break;
            }
        }
        
        $extensions = trim($_POST['app_extensions']);
        
        if(isset($app_object['predefined_app'])){
            if($_POST['app_editable']){
                $app_data['variables'] = $variables;
            }
            $app_data['force_termination'] = (int)$_POST['app_force_termination'];
            $app_data['resume_monitoring'] = (int)$_POST['app_resume_monitoring'];
            $app_data['extensions'] = empty($extensions) ? array() : explode(",", str_replace(" ", "", $extensions));
            $app_data['editable'] = (int)$_POST['app_editable'];
        } else {
            $app_data['name'] = $_POST['app_name'];
            $app_data['variables'] = $variables;
            $app_data['force_termination'] = (int)$_POST['app_force_termination'];
            $app_data['resume_monitoring'] = (int)$_POST['app_resume_monitoring'];
            $app_data['extensions'] = empty($extensions) ? array() : explode(",", str_replace(" ", "", $extensions));
        }

        $app_model->updateApp($_POST['app_id'], $app_data);

        $atp_config = ApiController::getATPConfig($_SESSION['license']);

        $amqp_model = new AMQPModel();
        $amqp_model->setExchange('server');
        $amqp_model->setRoutingKey('*');
        $amqp_model->setMessageModule('atp');
        $amqp_model->setMessageCommand('update');
        $amqp_model->setMessageArgs($atp_config);
        $amqp_model->sendMessage();

        header("location:/ddi/?module=atp&action=show");
    }

    protected function changeAppStatusAction() {
        $app_id = $_GET['app_id'];
        $app_status = (int) $_GET['app_status'];
        $app_model = new AppModel();

        $app_object = $app_model->getApp($app_id);
        if (!isset($app_object)) {
            $predefined_app = $app_model->getPredefinedApp($app_id);
            $tmp_app = $app_model->getAppByPredefinedApp($app_id);
            if (isset($predefined_app) && !isset($tmp_app)) {
                $app_id = $app_model->newPredefinedApp($app_id);
            } else {
                $app_id = (string) $tmp_app['_id'];
            }
        }

        $app_model->updateAppStatus($app_id, $app_status);

        $atp_config = ApiController::getATPConfig($_SESSION['license']);

        $amqp_model = new AMQPModel();
        $amqp_model->setExchange('server');
        $amqp_model->setRoutingKey('*');
        $amqp_model->setMessageModule('atp');
        $amqp_model->setMessageCommand('update');
        $amqp_model->setMessageArgs($atp_config);
        $amqp_model->sendMessage();

        header("location:/ddi/?module=atp&action=show");
    }

    protected function deleteAppAction() {
        $app_id = $_GET['app_id'];
        $app_model = new AppModel();
        $app = $app_model->deleteApp($app_id);

        $atp_config = ApiController::getATPConfig($_SESSION['license']);

        $amqp_model = new AMQPModel();
        $amqp_model->setExchange('server');
        $amqp_model->setRoutingKey('*');
        $amqp_model->setMessageModule('atp');
        $amqp_model->setMessageCommand('update');
        $amqp_model->setMessageArgs($atp_config);
        $amqp_model->sendMessage();        
        
        $options = array(
            "app" => $app,
        );

        $this->options = array_merge($this->options, $options);
        header("location:/ddi/?module=atp&action=show");
    }

}

?>
