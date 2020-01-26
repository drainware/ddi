<?

class ApplicationModel extends Model {

    private $coll;

    public function __construct() {
        $mongo_model = new MongoModel();
        $conn = $mongo_model->connect();
        $this->coll = $conn->dlp->applications;
    }

    public function createApplication($name, $description) {
        $id = new MongoId();
        $application_object = array(
            '_id' => $id,
            'license' => $_SESSION['license'],
            'application' => $name,
            'description' => $description,
            'editable' => true,
        );
        
        $this->coll->insert($application_object);
        
        return (string)$id;
    }

    public function updateApplication($application_id, $new_data) {
        $criteria = array(
            '_id' => new MongoID($application_id),
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

    public function deleteApplication($id) {
        $criteria = array(
            '_id' => new MongoId($id),
            'license' => $_SESSION['license'],
        );

        return $this->coll->remove($criteria);
    }

    public function getApplication($id, $license) {
        if(!isset($license)){
            $license = $_SESSION['license'];
        }
        
        $criteria = array(
            '$or' => array(
                array('_id' => new MongoId($id), 'license' => $license),
                array('_id' => new MongoId($id), 'license' => null)
            )
        );

        return $this->coll->findOne($criteria);
        ;
    }

    public function getApplications() {
        $criteria = array(
            '$or' => array(
                array('license' => $_SESSION['license']),
                array('license' => null)
            )
        );
        return $this->coll->find($criteria);
    }

}

?>
