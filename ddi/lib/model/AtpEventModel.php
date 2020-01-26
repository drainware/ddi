<?

class AtpEventModel extends Model {

    private $events;
    private $events_col_name;
    
    public function __construct($license) {
        
        $mongo_model = new MongoModel();
        $this->conn = $mongo_model->connect();

        $events_collection = 'atp_events';

        if (isset($license)) {
            $license = 'LIC_' . preg_replace('/-/', '_', $license);
            $events_collection = $license . '_' . $events_collection;
        }

        $this->events_col_name = $events_collection;
        $this->events = $this->conn->drs->$events_collection;

    }

    
    public function insertEvent($event_object) {
        
        $this->events->insert($event_object);
        $event = $this->events->findOne($event_object);
        $event_id = $event['_id'];
        
        return $event_id;
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
