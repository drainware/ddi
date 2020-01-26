<?

class StorageModel extends Model {

    private $files;

    public function __construct() {
        $mongo_model = new MongoModel();
        $conn = $mongo_model->connect();
        $db = $conn->storage;
        $this->files = $db->getGridFS();
    }

    public function getFile($file_id) {
        $file = $this->files->get(new MongoId($file_id));
        $file_content = $file->getBytes();
        return $file_content;
    }
    
    public function removeFile($file_id){
        $this->files->delete(new MongoId($file_id));
    }
    
}

?>
