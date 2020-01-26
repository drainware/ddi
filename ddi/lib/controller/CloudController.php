<?php

class CloudController extends Controller {

    public function __construct($name = null) {
        parent::__construct($name);
    }

    protected function showAction() {
        $this->registerUserAction();
    }

    public function registeredAction() {
    }

    public function registerUserAction() {

        if (isset($_POST['activate'])) {

            $cloud_model = new CloudModel();

            switch ($cloud_model->checkCloudUserExist($_POST['email'])) {
                case 0:
                    if($_POST['passwd'] == $_POST['passwd2']){
			//FIXME: never send data from users directly to models (potencial injection)
                        $cloud_user = $cloud_model->newCloudUser($_POST);
                        $mail_model = new MailModel();
                        $subject = "Activate your Drainware Account";
            			$vars = array(
            				'license' => $cloud_user['license'],
            				'email' => $cloud_user['email'],
            				'id' => $cloud_user['activation']);
            			$mail_model->setVars($vars);
            			$mail_model->setSubject($subject);
            			$mail_model->setDest($cloud_user['email']);
            			$mail_model->setTemplate('wellcomeMessage');
                                    $mail_model->sendMail();

                        header('Location: ?module=cloud&action=registered');
                    } else {
                        $msg = "The passwords do not match";
                    }
                    break;
                case -1:
                    $msg = "The email already exists.";
                    break;
                default:
                    $msg = "Unknown error.";
                    break;
            }
        }

        $refernced_by = isset($_GET['id']) ? $_GET['id'] : null;

        $options = array(
            "cloud_user" => $_POST,
            "referenced_by" => $refernced_by,
            "msg" => $msg,
        );

        $this->options = array_merge($this->options, $options);
    }

    public function validateUserAction() {
        $cloud_model = new CloudModel();
        $resp = $cloud_model->activateCloudUser($_GET['id']);

        $options = array(
            "resp" => $resp,
        );

        $this->options = array_merge($this->options, $options);
    }

    public function recoveryPasswordAction() {
        if (isset($_POST['email'])) {
            $cloud_model = new CloudModel();
            $cloud_user = $cloud_model->getCloudUserByEmail($_POST['email']);
            if (isset($cloud_user)) {

                $new_data = array(
                    'reset_password' => array(
                        'time' => new MongoDate(),
                        'token' => hash('sha256', new MongoId()),
                    ),
                );

                $cloud_model->updateCloudUser($cloud_user['license'], $new_data);

		$mail_model = new MailModel();
		$subject = "Drainware Account Recovery";
		$vars = array(
            		'email' => $cloud_user['email'],
            		'token' => $new_data['reset_password']['token']);
		$mail_model->setVars($vars);
		$mail_model->setDest($cloud_user['email']);
		$mail_model->setSubject($subject);
		$mail_model->setTemplate('resetPassword');
		$mail_model->sendMail();

                header('Location: ?module=cloud&action=sendRecoveryEmail');
            } else {
                $msg = "The email does not exists.";
            }
        }

        $options = array(
            "msg" => $msg,
        );

        $this->options = array_merge($this->options, $options);
    }

    public function sendRecoveryEmailAction() {
    }

    public function resetPasswordAction() {

        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $cloud_model = new CloudModel();
            $cloud_user = $cloud_model->getCloudUserByToken($_GET['id']);

            $now = new MongoDate();
            $time = $cloud_user['reset_password']['time'];

            $diff = $now->sec - $time->sec;
            $resp = ($diff / 3600) >= 72 ? 0 : 1;

            $options = array(
                "id" => $_GET['id'],
                "email" => $cloud_user['email'],
                "resp" => $resp,
            );

            $this->options = array_merge($this->options, $options);
        } else {
            header('Location: ?module=main');
        }
    }

    public function newPasswordAction() {
        if ($_POST['new_passwd'] == $_POST['rep_passwd']) {
            $cloud_model = new CloudModel();
            $cloud_user = $cloud_model->getCloudUserByToken($_POST['id']);

            if(isset($cloud_user)){
                $cloud_model->resetPassword($cloud_user['_id'], $_POST['new_passwd']);
                $msg = "Hooray! Your password has been changed!";
                $url = "?module=main&action=login";
                $notification = "We will redirect you to the login form. Please, wait a few seconds.";
            } else{
                $msg = "User does not exists";
                $url = "?module=cloud&action=recoveryPassword";
                $notification = "We will redirect you to the recovery password form. Please, wait a few seconds.";
            }
        }else {
            $msg = "The passwords do not match";
            $url = "?module=cloud&action=resetPassword&id=" . $_POST['id'];
            $notification = "We will redirect you to the reset password form. Please, wait a few seconds.";
        }

        $options = array(
            "msg" => $msg,
            "url" => $url,
            "notification" => $notification,
        );

        $this->options = array_merge($this->options, $options);
    }

    public function savePremiumUpgradeAction() {
        $upgrade_object = array();

        foreach ($_POST as $key => $value) {
            $upgrade_object[$key] = $value;
        }
        
        $upgrade_object['datetime'] = new MongoDate();
        $upgrade_object['nbr_users'] = (int)$upgrade_object['nbr_users'];
        $upgrade_object['extra_users'] = (int)$upgrade_object['extra_users'];
        $upgrade_object['period'] = (int)$upgrade_object['period'];
        
        $cloud_model = new CloudModel();

        $users = $upgrade_object['nbr_users'] + $upgrade_object['extra_users'];
        switch ($users) {
            case $users < 50:
                $cost_user = 6.99;
                break;
            case $users <= 100:
                $cost_user = 5.99;
                break;
            default:
                $cost_user = 4.99;
                break;
        }

        $months = $upgrade_object['period'];
        $discount = ((int)($months / 12)) / 10;

        if($upgrade_object['extra_users'] != 0){
            $discount = 0;
            $users = $upgrade_object['extra_users'];
        }

        $total_amount = $users * $cost_user * $months * (1 - $discount);
        $final_amount = (float) sprintf("%0.2f", $total_amount);

        $premium_upgrade_id = 0;
        if($final_amount == $upgrade_object['total_amount']){
            $premium_upgrade_id = $cloud_model->registerPremiumUpgrade($upgrade_object);
        }

        $options = array(
            "premium_upgrade_id" => $premium_upgrade_id,
        );

        $this->options = array_merge($this->options, $options);
    }

    public function paymentNotificationAction() {
        $cloud_model = new CloudModel();
        $cloud_model->registerPaymentNotification($_POST);

        switch ($_POST['txn_type']) {
            case 'subscr_signup':
                break;
            case 'subscr_cancel':
                break;
            case 'subscr_payment':
            case 'web_accept':
                $premium_upgrade = $cloud_model->getPremiumUpgrade($_POST['item_number']);
                
                if ($premium_upgrade['total_amount'] == $_POST['mc_gross']) {

                    $upgrade_data = array();
                    
                    if ($premium_upgrade['extra_users'] == 0) {
                        $upgrade_data['type'] = 'premium';
                        $upgrade_data['events.availability'] = true;
                        $upgrade_data['events.screenshot'] = true;

                        $upgrade_data['period'] = $premium_upgrade['period'];
                        $upgrade_data['expiry'] = new MongoDate(strtotime('+' . $premium_upgrade['period'] . ' month', strtotime(date('Y-m-d'))));
                    }
                    
                    $upgrade_data['nbr_users'] = $premium_upgrade['nbr_users'] + $premium_upgrade['extra_users'];
                    $upgrade_data['cost_user_per_month'] = (float)$premium_upgrade['cost_user_per_month'];
                    
                    $upgrade_data['payment_id'] = $_POST['item_number'];
                    
                    $cloud_model->updateCloudUser($premium_upgrade['license'], $upgrade_data);
                    $group_model = new GroupModel();
                    $groups = $group_model->getGroups($premium_upgrade['license']);

                    foreach ($groups as $group) {
                        $group_dlp_config = ApiController::getDLPGroupConfig($premium_upgrade['license'], $group['name']);

                        $amqp_model = new AMQPModel();
                        $amqp_model->setExchange('server');
                        $amqp_model->setRoutingKey($group['name'], $premium_upgrade['license']);
                        $amqp_model->setMessageModule('dlp');
                        $amqp_model->setMessageCommand('update');
                        $amqp_model->setMessageArgs($group_dlp_config);
                        $amqp_model->sendMessage();
                    }

                    $atp_config = ApiController::getATPConfig($premium_upgrade['license']);

                    $amqp_model = new AMQPModel();
                    $amqp_model->setExchange('server');
                    $amqp_model->setRoutingKey('*');
                    $amqp_model->setMessageModule('atp');
                    $amqp_model->setMessageCommand('update');
                    $amqp_model->setMessageArgs($atp_config);
                    $amqp_model->sendMessage();
                }
                break;
            default:
                break;
        }
    }

    public function checkPaymentAction() {
        $cloud_model = new CloudModel();
        $cloud_user = $cloud_model->getCloudUser();

        $code = -1;
        if($cloud_user['payment_id'] == $_POST['payment_id']){
            $code = 0;
        }

        $options = array(
            "code" => $code,
        );

        $this->options = array_merge($this->options, $options);
    }

    public function validateLicenseAction() {
        $url = "";
        $server = "";
        $language="es";
        $port = 0;
        $code = 1;

        if (isset($_POST['license']) & !empty($_POST['license'])) {
            $cloud_model = new CloudModel();
            $resp = $cloud_model->validateLicense($_POST['license']);
            if ($resp) {
                $code = 0;
                $url = "www.google.com";
		//Always use *.drainware.com because of proxy authentication bypass of drainware.com
                $server = "rabbot.drainware.com";
                $port = 443;
            }
        }
	

        $response = json_encode(
                array(
                    'code' => $code,
                    'language' => $language,
                    'server' => $server,
                    'port' => $port,
                    'url' => $url
                )
        );

        $options = array(
            "response" => $response,
        );
	
	syslog(LOG_DEBUG, 'validateLicenseAction => ' . $response);

        $this->options = array_merge($this->options, $options);
    }

    public function downloadEndpointAction() {
        $arch = $_GET['arch'];

        $dse = file_get_contents("http://update.drainware.com/latest-dse/index.php?mode=cloud&arch=" . $arch);
        $filearray = explode("\n", $dse);
        $url = $filearray[1];

        header('Location: ' . $url);
    }

}

?>
