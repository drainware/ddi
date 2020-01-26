<?php

class Controller {

    protected $name;
    protected $view;
    protected $action; // = 'show';
    protected $state;
    protected $options;

    public function __construct($name = null) {
        $this->name = $name;
        $this->loadView();
        $this->state = Array();
        $this->state['error'] = false;

        $menu = Array();

        $main = new MainModel();
        $sbmode = $main->getConfValue('ddisbmode');

        //Only shown when we are in explicit mode
        if ($sbmode == 'user') {
            $menu[] = 'main';
            $menu[] = 'user';
            $menu[] = 'group';
        }

        $msg_notifications_bar = array();
        $notification_status = 'disabled';
        if (isset($_SESSION['license'])) {
            $cloud_model = new CloudModel();
            $cloud_user = $cloud_model->getCloudUser();
            $notification_status = $cloud_user['notifications']['status'];
            if(!$cloud_user['events']['availability'])
	            $msg_notifications_bar[] = new NotificationModel(NotificationModel::ERROR, 'Free Monthly Events Consumed');
            
            
        }


	// Data sent to interface for uservoice and internal management
	$cloud_model = new CloudModel();
	$cloud_user = $cloud_model->getCloudUserByLicense($_SESSION['license']);
	
	if($cloud_user['type'] == "premium"){
	    // Marketing stuff for uservoice
	    $churn_rate = 0.05; // We have to tune it
	    $gross_margin = 0.3;
	    $monthly_rate = $cloud_user['cost_user_per_month'] * $cloud_user['nbr_users'];
	    $ltv = ($monthly_rate * $gross_margin) / $churn_rate;
	}else{
	    $ltv = 0;
	    $monthly_rate = 0;
	}
        $this->options = array(
            'action' => $this->action,
            'module' => $this->name,
            'menu' => $menu,
            'state' => Array(),
            'return_to' => Array(),
            'translations' => $GLOBALS['lang'],
            'msg_notifications_bar' => $msg_notifications_bar,
            'notification_status' => $notification_status,
            'mode',
	    'client_type' => $cloud_user['type'],
	    'account_company' => $cloud_user['company'],
	    'account_monthly_rate' => $monthly_rate,
	    'account_timezone' => $cloud_user['timezone'],
	    'account_creation' => $cloud_user['registered']->sec,
	    'account_ltv' => $ltv,
	    'acccount_mail' => $cloud_user['email']
        );
    }

    protected function setState($state) {
        $this->state = $state;
    }

    protected function setReturnTo($return_to) {
        $this->return_to = $return_to;
    }

    public function load() {
        $method_name = $this->action . 'Action();';
        $code = '$this->' . $method_name;
        //@TODO: sanitice $this->action
        eval($code);

        //syslog(LOG_DEBUG, $code);

        $this->view->setOptions($this->options);
        $this->view->display();
    }

    protected function showAction() {
        echo 'Please, define showAction() method in ' . $this->name . 'Controller';
    }

    public function loadView() {
        $view_name = ucfirst($this->name) . 'View';
        $this->view = new $view_name($this->name);
        //$this->view->display();
    }

    public function setAction($action) {
        $this->action = $action;
        $this->options['action'] = $action;
    }

    public function getAction() {
        return $this->action;
    }

}

?>
