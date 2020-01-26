<?

class AppModel extends Model {

    private $coll;

    public function __construct() {
        $mongo_model = new MongoModel();
        $conn = $mongo_model->connect();
        $this->coll = $conn->atp->applications;
    }

    public function newApp($app_object) {
        $app_object['license'] = $_SESSION['license'];
        return $this->coll->insert($app_object);
    }

    public function newPredefinedApp($predefined_app_id){
        $_id = new MongoId();
        $app_object = array(
            "_id" => $_id,
            "license" => $_SESSION['license'],
            "predefined_app" => $predefined_app_id,
            "force_termination" => 0,
            "resume_monitoring" => 0,
            "extensions" => array(),
            "editable" => 0,
            "status" => 0,
        );
        $this->coll->insert($app_object);
        return $_id;
    }

    public function saveApp($app_object) {
        $app_object['license'] = $_SESSION['license'];
        return $this->coll->save($app_object);
    }

    public function updateApp($app_id, $app_data) {
        $criteria = array(
            '_id' => new MongoId($app_id),
            'license' => $_SESSION['license'],
        );
        $data = array(
            '$set' => $app_data
        );
        $option = array(
            'upsert' => true
        );

        return $this->coll->update($criteria, $data, $option);
        ;
    }
    
    public function updateAppStatus($app_id, $app_status) {
        $criteria = array(
            '_id' => new MongoId($app_id),
            'license' => $_SESSION['license'],
        );
        $data = array(
            '$set' => array(
                "status" => $app_status
            )
        );
        $option = array(
            'upsert' => true
        );

        return $this->coll->update($criteria, $data, $option);
        ;
    }

    public function deleteApp($app_id) {
        $criteria = array(
            '_id' => new MongoId($app_id),
            'license' => $_SESSION['license'],
        );

        return $this->coll->remove($criteria);
    }

    public function getApp($app_id, $license = null) {
        $license = !isset($license) ? $_SESSION['license'] : $license;
        $criteria = array(
            '_id' => new MongoId($app_id),
            'license' => $license,
        );

        $app_object = $this->coll->findOne($criteria);
        
        if(isset($app_object['predefined_app'])){
            $predefined_app = $this->getPredefinedApp($app_object['predefined_app']);
            $app_object['name'] = $predefined_app['name'];
            $app_object['description'] = $predefined_app['description'];
            if(!$app_object['editable']){
                $app_object['variables'] = $predefined_app['variables'];
            }
            $app_object['other_extensions'] = $app_object['extensions'];
            $app_object['extensions'] = $predefined_app['extensions'];
        }
        
        return $app_object;
    }

    public function getPredefinedApp($app_id) {
        $criteria = array(
            '_id' => new MongoId($app_id),
            'general' => 1,
        );
        return $this->coll->findOne($criteria);
    }
    
    public function getAppByPredefinedApp($predefined_app_id){
        $criteria = array(
            'license' => $_SESSION['license'],
            'predefined_app' => $predefined_app_id,
        );

        return $this->coll->findOne($criteria);
    }


    public function getActiveApps($license = null) {
        $license = !isset($license) ? $_SESSION['license'] : $license;
        $criteria = array(
            'license' => $license,
            'status' => 1,
        );

        return $this->coll->find($criteria);
    }

    public function getApps() {
        $criteria = array(
            'license' => $_SESSION['license'],
        );
        return $this->coll->find($criteria);
    }
    
    public function getPredefinedApps() {

        $apps_id = array();
        foreach ($this->getApps() as $app_object) {
            $apps_id[] = new MongoId($app_object['predefined_app']);
        }
        
        $criteria = array(
            '_id' => array (
                '$nin' => $apps_id,
            ),
            'general' => 1,
        );
        return $this->coll->find($criteria);
    }

}

?>
