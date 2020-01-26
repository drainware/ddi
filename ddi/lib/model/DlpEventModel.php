<?

class DlpEventModel extends Model {

    private $conn;
    private $events;
    private $policies;
    private $concepts;
    private $subconcepts;
    private $rules;
    private $files;
    private $events_col_name;

    public function __construct($license) {

        $mongo_model = new MongoModel();
        $this->conn = $mongo_model->connect();

        $this->policies = $this->conn->dlp->policies;
        $this->concepts = $this->conn->dlp->concepts;
        $this->subconcepts = $this->conn->dlp->subconcepts;
        $this->rules = $this->conn->dlp->rules;
        $this->files = $this->conn->dlp->files;
        $this->networkplaces = $this->conn->dlp->network_places;

        $events_collection = 'dlp_events';
        if (isset($license)) {
            $license = 'LIC_' . preg_replace('/-/', '_', $license);
            $events_collection = $license . '_' . $events_collection;
        }
        
        $this->events_col_name = $events_collection;
        $this->events = $this->conn->drs->$events_collection;
        
        
    }

    public function getPoliciesName($policies) {
        $policies_name = array();
        foreach ($policies as $policy) {
            $policy_id = new MongoID($policy);
            $result = $this->policies->findOne(array('_id' => $policy_id));
            $policies_name[] = $result["name"];
        }
        return $policies_name;
    }

    public function getConcept($id) {
        $subconcept_id = new MongoID($id);
        $subconcept = $this->subconcepts->findOne(array('_id' => $subconcept_id));
        $concept_id = new MongoID($subconcept['concept']);
        $concept = $this->concepts->findOne(array('_id' => $concept_id));
        //$concept_name = $concept['concept'];
        return $concept;
    }

    public function getIdentifier($id, $type) {
        $mongo_id = new MongoID($id);
        if ($type == "subconcept") {
            $subconcept = $this->subconcepts->findOne(array('_id' => $mongo_id));
            $identifier = $subconcept['description'];
        } elseif ($type == "rule") {
            $rule = $this->rules->findOne(array('_id' => $mongo_id));
            $identifier = $rule['description'];
        } elseif ($type == "file") {
            $file = $this->files->findOne(array('_id' => $mongo_id));
            $identifier = $file['name'];
        } elseif ($type == "network_place") {
            $networkplace = $this->networkplaces->findOne(array('_id' => $mongo_id));
            $identifier = $networkplace['description'];
        } elseif ($type == "encrypted") {
            $identifier = "Encrypted File";
        } else {
            $identifier = $id;
        }
        return $identifier;
    }

    public function insertEvent($event) {
        $this->events->insert($event);
        $event = $this->events->findOne($event);
        $event_id = $event['_id'];
        return $event_id;
    }

    public function getEmailsByPolicies($license, $policies) {
        $cloud_model = new CloudModel();
        $cloud_user = $cloud_model->getCloudUserByLicense($license);
        $user_email = $cloud_user['email'];

        $policies_email = array();
        foreach ($policies as $policy) {
            $redis = new RedisModel();
            $email = $redis->getVariable($policy);
            if ($email) {
                $policies_email[] = $email;
            } else {
                $policy_id = new MongoID($policy);
                $result = $this->policies->findOne(array('_id' => $policy_id));
                $email = $result["email"];
                if (empty($email)) {
                    $email = isset($license) ? $user_email : $GLOBALS['conf']['ddi']['configuration']['notifications']['e-mail']['value'];
                }
                $policies_email[] = $email;
                $redis->setVariable($policy, $email);
            }
        }
        return array_unique($policies_email);
    }

    public function setShardCollection() {
        
        $this->events->ensureIndex('_id');
        
        $command = array(
            'shardcollection' => 'drs.' . $this->events_col_name,
            'key' => array(
                '_id' => 1
            )
        );
        
        $this->conn->admin->command($command);
        
    }

}

?>
