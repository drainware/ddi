<?

class DlpEventDetailsModel extends Model {

    private $event;
    private $previous_event;
    private $next_event;

    public function __construct($event_id) {

        $cloud_model = new CloudModel();
        $client = $cloud_model->getCloudUser();
        date_default_timezone_set($client['timezone']);
        
        $mongo_model = new MongoModel();
        $con = $mongo_model->connect();
        $db = $con->drs;
        
        $events_collection = 'dlp_events';
        if(isset($_SESSION['license'])){
            $license = 'LIC_' . preg_replace('/-/', '_', $_SESSION['license']);
            $events_collection = $license . '_' . $events_collection;
        }
        
        $events = $db->$events_collection;

        $criteria = array(
            '_id' => new MongoId($event_id),
            'license' => $_SESSION['license']
        );
        
        $this->event = $events->findOne($criteria);
        
        $id_position = array_search($event_id, $_SESSION['QUERY_IDS']);
        $this->previous_event = $_SESSION['QUERY_IDS'][$id_position + 1];
        $this->next_event = $_SESSION['QUERY_IDS'][$id_position - 1];
    }

    public function getDate() {
        $date = $this->event['timetime']->sec;
        return $date;
    }

    public function getIP() {
        return $this->event['ip'];
    }
    
    public function getUser() {
        return $this->event['user'];
    }

    public function getGroups() {
        return $this->event['groups'];
    }

    public function getOrigin() {
        return $this->event['origin'];
    }

    public function getScreenshot() {
        $ssid = $this->event['scid'];
        $image = '/ddi/images/drainware_screen.png';
        if ($ssid != null) {
            $image = '/ddi/?module=api&action=getScreenshot&id=' . $ssid;
        }
        return $image;
    }
    
    public function getPolciesName() {
        return $this->event['policies_name'];
    }

    public function getConceptName() {
        return ucwords(preg_replace("/_/", " ", $this->event['concept_name']));
    }
    
    public function getType() {
        return ucwords(preg_replace("/_/", " ", $this->event['type']));
    }
    
    public function getIdentifier() {
        return $this->event['identifier'];
    }
    
    public function getMatch() {
        return htmlentities($this->event['match']);
    }
    
    public function  checkContext(){
        return ($this->event['type'] == "subconcept" || $this->event['type'] == "subconcept") && $this->event['context'] != "";
    }

    public function getContext() {
        $term = "/" . $this->event['match'] . "/";
        $replace_term = "<b>" . $this->event['match'] . "</b>";
        return preg_replace($term, $replace_term, htmlentities($this->event['context']));
    }
    
    public function getAction() {
        return $this->event['action'];
    }
    
    public function getSeverity() {
        return $this->event['severity'];
    }
    
    public function getFilename() {
        return $this->event['filename'];
    }
    
    public function getApp() {
        return $this->event['app']['description'];
    }
    
    public function getTextOrigin() {
        return ucwords($this->event['endpoint_module']);
    }
    
    public function getLat() {
        return $this->event['geodata']['location']['lat'];
    }
    
    public function getLng() {
        return $this->event['geodata']['location']['lng'];
    }
    
    public function getAccuracy() {
        return $this->event['geodata']['accuracy'];
    }

    public function getPreviousEvent() {
        return $this->previous_event;
        //return $this->previous_event['_id'];
    }

    public function getNextEvent() {
        return $this->next_event;
        //return $this->next_event['_id'];
    }
}

?>
