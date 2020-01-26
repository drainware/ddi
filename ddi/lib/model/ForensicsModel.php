<?php

class ForensicsModel extends Model {

    private $conn;
    private $db_forensics;
    private $db_fs;

    public function __construct() {

        $mongo_model = new MongoModel();
        $this->conn = $mongo_model->connect();
        $this->db_forensics = $this->conn->forensics;
        $this->db_fs = $this->conn->storage;
        $this->remote_query_col = $this->db_forensics->remote_query;
        $this->result_object_col = $this->db_forensics->result_object;
        $this->remote_query_list_col = $this->db_forensics->remote_query_list;
        $this->remote_files = $this->db_fs->getGridFS();
    }

    public function newRemoteQueryList($name, $list) {

        $timetime = new MongoDate();
        $query_list_object = array(
            'name' => $name,
            'list' => $list,
            'timetime' => $timetime,
            'datetime' => date('Y.m.d h:i:s', $timetime->sec)
        );
        $this->remote_query_list_col->insert($query_list_object);
    }

    public function removeRemoteQueryList($query_list_id) {
        $criteria = array(
            '_id' => new MongoId($query_list_id)
        );
        return $this->remote_query_list_col->remove($criteria);
    }
    
    public function getRemoteQueryList($query_list_id) {
        $query_list_object = array(
            '_id' => new MongoId($query_list_id)
        );
        return $this->remote_query_list_col->findOne($query_list_object);
    }

    public function getRemoteQueryLists($query, $limit) {
        return $this->remote_query_list_col->find($query)->sort(array('_id' => -1))->limit($limit);
    }

    public function registerResultSearch($result_object){
        return $this->result_object_col->insert($result_object);
    }

    public function registerResultObject($result_object){
        return $this->result_object_col->insert($result_object);
    }

    public function removeObjectsResults($id, $command){
        $criteria = array(
            'id' => $id,
            'command' => $command,
        );

        return $this->result_object_col->remove($criteria);
    }
    
    public function getObjectsResults($id, $command){
        $criteria = array(
            'id' => $id,
            'command' => $command,
        );

        return $this->result_object_col->find($criteria)->sort(array('_id' => 1));
    }

    public function getLastSearchResults($query_id, $last_id){

        $search_object = array(
            'id' => $query_id,
            'command' => 'search',
        );

        if (!empty($last_id)){
            $search_object['_id'] =  array(
                '$gt' => new MongoId($last_id)
            );
        }
        
        return $this->result_object_col->find($search_object)->sort(array('_id' => 1));
    }

    public function getLastObjectResult($command_id){
        $result_object =  array(
            'id' => $command_id
        );
        return $this->result_object_col->findOne($result_object);
    }

    public function getRemoteFile($file_id){
        $storage_model = new StorageModel();
        $file_content = $storage_model->getFile($file_id);
        $storage_model->removeFile($file_id);
        return $file_content;
    }
    
}

?>
