<?php

class PolicyModel extends Model {

    private $conn;
    private $db;
    private $policy_col;

    public function __construct() {

        $mongo_model = new MongoModel();
        $this->conn = $mongo_model->connect();
        $this->db = $this->conn->dlp;
        $this->policy_col = $this->db->policies;
    }

    public function getPolicies($license) {
        $query = array(
            'license' => $license
        );
        
        $policies = $this->policy_col->find($query);
        return $policies;
    }
  
    public function getPoliciesByGroup($license, $group) {
        $query = array(
            'license' => $license,
            'groups.' . $group . ".group_id" => $group
        );
        $policies = $this->policy_col->find($query);
        return $policies;
    }

    public function gePoliciesByRule($rule_id){
        $query = array(
            'license' => $_SESSION['license'],
            'rules' => $rule_id,
        );
        $policies = $this->policy_col->find($query);
        return $policies;
    }

    public function gePoliciesByFile($file_id){
        $query = array(
            'license' => $_SESSION['license'],
            'files' => $file_id,
        );
        $policies = $this->policy_col->find($query);
        return $policies;
    }    
    
    public function gePoliciesByNetworkPlace($network_place_id){
        $query = array(
            'license' => $_SESSION['license'],
            'network_places' => $network_place_id,
        );
        $policies = $this->policy_col->find($query);
        return $policies;
    }        
    
    public function gePoliciesByApplication($application_id){
        $query = array(
            'license' => $_SESSION['license'],
            'applications' => $application_id,
        );
        $policies = $this->policy_col->find($query);
        return $policies;
    }    
    
    public function newPolicy($policy) {
        $out = $this->policy_col->insert($policy);
        return $out;
    }

    public function deletePolicy($idPolicy) {
        $criteria = array(
            '_id' => new MongoId($idPolicy),
            'license' => $_SESSION['license'],
        );
        $r = $this->policy_col->remove($criteria);
        return $r;
    }

    public function updatePolicy($policy) {
        return $this->policy_col->save($policy);
    }

    public function getPolicy($policy_id) {
        $criteria = array(
            '_id' => new MongoId($policy_id),
            'license' => $_SESSION['license'],
        );
        $r = $this->policy_col->findOne($criteria);
        return $r;
    }

    public function getGroup($policy, $idGroup) {
        $groups = $policy['groups'];
        foreach ($groups as $group) {
            if ($group['group_id'] == $idGroup)
                return $group;
        }
        return null;
    }
    
    public function updateGroups($policy_id, $groups){
        $criteria = array(
            '_id' => new MongoID($policy_id),
            'license' => $_SESSION['license'],
        );
        $data = array(
            '$set' => array(
                'groups' => $groups
            )
        );
        $option = array (
            'upsert' => true
        );
        
        $this->policy_col->update($criteria, $data, $option);   
    }
    
    public function updateRules($policy_id, $rules){
        $criteria = array(
            '_id' => new MongoID($policy_id),
            'license' => $_SESSION['license'],
        );
        $data = array(
            '$set' => array(
                'rules' => $rules
            )
        );
        $option = array (
            'upsert' => true
        );
        
        $this->policy_col->update($criteria, $data, $option);   
    }
    
    public function updateFiles($policy_id, $files){
        $criteria = array(
            '_id' => new MongoID($policy_id),
            'license' => $_SESSION['license'],
        );
        $data = array(
            '$set' => array(
                'files' => $files
            )
        );
        $option = array (
            'upsert' => true
        );
        
        $this->policy_col->update($criteria, $data, $option);   
    }

    public function updateNetworkPlaces($policy_id, $network_places){
        $criteria = array(
            '_id' => new MongoID($policy_id),
            'license' => $_SESSION['license'],
        );
        $data = array(
            '$set' => array(
                'network_places' => $network_places
            )
        );
        $option = array (
            'upsert' => true
        );
        
        $this->policy_col->update($criteria, $data, $option);   
    }
    
    public function updateApplications($policy_id, $applications){
        $criteria = array(
            '_id' => new MongoID($policy_id),
            'license' => $_SESSION['license'],  
        );
        $data = array(
            '$set' => array(
                'applications' => $applications
            )
        );
        $option = array (
            'upsert' => true
        );
        
        $this->policy_col->update($criteria, $data, $option);   
    }
        
}

?>