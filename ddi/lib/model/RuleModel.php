<?php

class RuleModel extends Model {

    private $coll;

    public function __construct() {
        $mongo_model = new MongoModel();
        $conn = $mongo_model->connect();
        $this->coll = $conn->dlp->rules;
    }

    public function createRule($rule, $description, $verify) {
        $id = new MongoId();

        $rule_object = array(
            '_id' => $id,
            'license' => $_SESSION['license'],
            'rule' => $rule,
            'description' => $description,
            'verify' => $verify
        );

        $this->coll->insert($rule_object);

        return (string) $id;
    }

    public function updateRule($rule_id, $new_data) {
        $criteria = array(
            '_id' => new MongoID($rule_id),
            'license' => $_SESSION['license'],
        );
        $data = array(
            '$set' => $new_data
        );
        $option = array(
            'upsert' => true
        );

        return $this->coll->update($criteria, $data, $option);
    }

    public function deleteRule($rule_id) {
        $criteria = array(
            '_id' => new MongoId($rule_id),
            'license' => $_SESSION['license'],
        );

        $this->coll->remove($criteria);
    }

    public function getRule($rule_id, $license = null) {
        
        if(!isset($license)){
            $license = $_SESSION['license'];
        }
        
        $criteria = array(
            '_id' => new MongoId($rule_id),
            'license' => $license,
        );

        return $this->coll->findOne($criteria);
    }

    public function getRules($license) {
        $query = array(
            'license' => $license
        );

        return $this->coll->find($query);
    }    
    
}

?>