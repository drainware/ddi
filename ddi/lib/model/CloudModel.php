<?php

class CloudModel extends Model {

    private $db;
    private $customers_col;
    private $premium_upgrade_col;
    private $premium_singup_subscription_col;
    private $premium_cancel_subscription_col;
    private $premium_unique_payment_col;
    private $premium_monthly_payment_col;

    public function __construct() {
        $mongo_model = new MongoModel();
        $conn = $mongo_model->connect();
        $this->db = $conn->cloud;
        $this->customers_col = $conn->cloud->customers;
        $this->premium_upgrade_col = $conn->cloud->premium_upgrade;
        $this->premium_singup_subscription_col = $conn->cloud->premium_singup_subscription;
        $this->premium_cancel_subscription_col = $conn->cloud->premium_cancel_subscription;
        $this->premium_unique_payment_col = $conn->cloud->premium_unique_payment;
        $this->premium_monthly_payment_col = $conn->cloud->premium_monthly_payment;
    }

    public function newCloudUser($user_data) {
        $_id = new MongoId();
        
        $long_url = "https://www.drainware.com/ddi/?module=cloud&action=registerUser&id=" . $_id;
        $long_url = urlencode($long_url);
        $request_url = "http://api.bitly.com/v3/shorten?format=txt&login=drainware&apiKey=R_530a251a0116de0e96a9209f64377982&longUrl=" . $long_url;
        $referral_url = trim(file_get_contents($request_url));
        
        $cloud_user = array(
            '_id' => $_id,
            'license' => $this->generateCloudLicense(),
            'email' => $user_data['email'],
            'password' => md5($user_data['passwd']),
            'type' => isset($user_data['type']) ? $user_data['type'] : "freemium",
            'country' => $user_data['country_name'],
            'company' => empty($user_data['company']) ? null: $user_data['company'] ,
            'cif' => empty($user_data['cif']) ? null : $user_data['cif'],
            'nbr_employees' => isset($user_data['nbr_employees']) ? $user_data['nbr_employees'] : 0,
            'auth' => 'local',
            'nbr_users' => 0,
            'activate' => false,
            'deployed' => false,
            '1stpolicy' => false,            
            'activation' => hash('sha256', new MongoId()),
            'referral_url' => $referral_url,
            'referenced_by' => empty($user_data['referenced_by']) ? null : $user_data['referenced_by'],
            'reference_activated' => false,
            'events' => array(
                'availability' => true,
                'monthly' => array(
                    'general' => 0,
                    'dlp' => 0,
                    'atp' => 0,
                    'forensics' => 0,
                ),
                'global' => array(
                    'general' => 0,
                    'dlp' => 0,
                    'atp' => 0,
                    'forensics' => 0,
                ),
                'screenshot' => true,
            ),
            'extra_events' => 0,
            'notifications' => array(
                'status' => 'disabled',
                'when' => array(),
            ),
            'registered' => new MongoDate(),
            '1st_policy' => false,
            'timezone' => 'UTC',
        );

        $this->customers_col->insert($cloud_user);

        return $cloud_user;
    }

    public function updateCloudUser($license, $new_data) {
        $cloud_user = $this->getCloudUserByLicense($license);
        $criteria = array(
            '_id' => $cloud_user['_id'],
        );
        $data = array(
            '$set' => $new_data,
        );
        $option = array(
            'upsert' => true,
        );

        return $this->customers_col->update($criteria, $data, $option);
    }

    public function saveCloudUser($cloud_user) {
        $this->customers_col->save($cloud_user);
    }

    public function checkCloudUserExist($email) {
        $criteria = array(
            'email' => $email,
        );

        $cloud_user = $this->customers_col->find($criteria);

        if ($cloud_user->count() == 0) {
            return 0;
        } else {
            return -1;
        }
    }

    public function loginCloudUser($email, $password) {
        $criteria = array(
            'email' => $email,
        );

        $cloud_user = $this->customers_col->findOne($criteria);

        if ($cloud_user != null) {
            if ($cloud_user['activate']) {
                if ($cloud_user['password'] == md5($password)) {
                    return 0;
                } else {
                    return 1;
                }
            } else {
                return 2;
            }
        } else {
            return -1;
        }
    }

    public function resetPassword($user_id, $password) {
        $criteria = array(
            '_id' => new MongoId($user_id),
        );
        $data = array(
            '$set' => array(
                'password' => md5($password),
            )
        );
        $option = array(
            'upsert' => true,
        );

        $this->customers_col->update($criteria, $data, $option);
    }

    public function changePassword($email, $password) {
        $criteria_find = array(
            'license' => $_SESSION['license'],
            'email' => $email,
        );
        
        $cloud_user = $this->customers_col->findOne($criteria_find);
        
        $criteria = array(
            '_id' => $cloud_user['_id'],
        );
        $data = array(
            '$set' => array(
                'password' => md5($password),
            )
        );
        $option = array(
            'upsert' => true,
        );

        $this->customers_col->update($criteria, $data, $option);
    }

    public function getCloudUsers() {

        $cloud_users = $this->customers_col->find();
        return $cloud_users;
    }

    public function getCloudUser($license = null) {
        $license = !isset($license) ? $_SESSION['license'] : $license;
        
        $criteria = array(
            'license' => $license,
        );

        return $this->customers_col->findOne($criteria);;
    }

    public function getCustomerByID($cloud_user_id) {

        $criteria = array(
            '_id' => new MongoId($cloud_user_id),
        );

        $cloud_user = $this->customers_col->findOne($criteria);
        return $cloud_user;
    }

    public function getCloudUserByLicense($cloud_user_license) {

        $criteria = array(
            'license' => $cloud_user_license,
        );

        $cloud_user = $this->customers_col->findOne($criteria);
        return $cloud_user;
    }

    public function getCloudUserByEmail($cloud_user_email) {

        $criteria = array(
            'email' => $cloud_user_email,
        );

        return $this->customers_col->findOne($criteria);
    }

    public function getCloudUserByToken($cloud_user_token) {

        $criteria = array(
            'reset_password.token' => $cloud_user_token,
        );

        return $this->customers_col->findOne($criteria);
    }

    public function getClientUserAuth($license = null){
        $client = $this->getCloudUser($license);
        return $client['auth'];
    }


    public function activateCloudUser($cloud_user_token) {
        $criteria_find = array(
            'activation' => $cloud_user_token,
        );

        $customer = $this->customers_col->findOne($criteria_find);

        $active = 1;
        if ($customer != null) {
            $active = 2;
            if (!$customer['activate']) {
                $criteria = array(
                    '_id' => $customer['_id'],
                );
                $data = array(
                    '$set' => array(
                        'activate' => true,
                    )
                );
                $option = array(
                    'upsert' => true,
                );
                $this->customers_col->update($criteria, $data, $option);

                $mail_model = new MailModel();
		$mail_model->setDest($customer['email']);
		$mail_model->setSubject('You are ready to deploy drainware');
		$mail_model->setTemplate('firstStepMessage');
		$mail_model->sendMail();
                
                $group_model = new GroupModel();
                $group_model->createDefaultGroup($customer['license']);

                $dlp_event_model = new DlpEventModel($customer['license']);
                $dlp_event_model->setShardCollection();

                $atp_event_model = new AtpEventModel($customer['license']);
                $atp_event_model->setShardCollection();

                $active = 0;
            }
        }
        return $active;
    }

    private function generateCloudLicense() {

        do {
            $sernum = '';
            for ($i = 0; $i < 16; $i++) {
                switch (rand(0, 1)) {
                    case 0: $sernum .= chr(rand(65, 90));
                        break;
                    case 1: $sernum .= rand(0, 9);
                        break;
                }
            }
            $license = implode("-", str_split($sernum, 4));

            $criteria = array(
                'license' => $license,
            );

            $out = $this->customers_col->find($criteria);
        } while ($out->count() != 0);

        return $license;
    }

    public function registerPremiumUpgrade($premium_user_payment) {
        $payment_id = new MongoId();
        $premium_user_payment['_id'] = $payment_id;
        $this->premium_upgrade_col->insert($premium_user_payment);
        return $payment_id;
    }

    public function getPremiumUpgrade($premium_user_payment_id) {
        $criteria = array(
            '_id' => new MongoId($premium_user_payment_id),
        );
        return $this->premium_upgrade_col->findOne($criteria);
    }

    public function registerPaymentNotification($notification) {
        switch ($notification['txn_type']) {
            case 'subscr_signup':
                $this->premium_singup_subscription_col->insert($notification);
                break;
            case 'subscr_cancel':
                $this->premium_cancel_subscription_col->insert($notification);
                break;
            case 'subscr_payment':
                $this->premium_monthly_payment_col->insert($notification);
                break;
            case 'web_accept':
                $this->premium_unique_payment_col->insert($notification);
                break;            
            default:
                break;
        }
    }

    public function validateLicense($license) {
        $valid = false;
        $criteria = array(
            'license' => $license,
        );
        $out = $this->customers_col->findOne($criteria);
        if ($out != null) {
            $valid = true;
        }
        return $valid;
    }

    /*
    public function getGlobalEvents() {
        
        $map = new MongoCode(
            'function () {
                emit("global_events", this.events.global.general);
            }'
        );
        
        $reduce = new MongoCode(
            'function (key, values) {
                var result = 0;
                values.forEach(
                    function (value) {
                        result += value;
                    }
                );
                return result;
            }'
        );
        
        $this->db->command(
            array(
                "mapreduce" => "customers", 
                "map" => $map,
                "reduce" => $reduce,
                "out" => array("merge" => "global_events")
            )
        );
        
        $global_events = $this->db->global_events->findOne();
        
        return $global_events['value'];
    }
    */
    
}

?>
