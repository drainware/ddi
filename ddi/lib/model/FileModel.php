<?php

class FileModel extends Model {

    private $coll;

    public function __construct() {
        $mongo_model = new MongoModel();
        $conn = $mongo_model->connect();
        $this->coll = $conn->dlp->files;
    }

    public function createFile($name, $sha1, $md5, $ssdeep) {
        $_id = new MongoId();
        $file_object = array(
            '_id' => $_id,
            'license' => $_SESSION['license'],
            'name' => $name,
            'sha1' => $sha1,
            'md5' => $md5,
            'ssdeep' => $ssdeep,
        );

        $this->coll->insert($file_object);
        return (string)$_id;
    }

    public function updateFile($file_id, $new_data) {
        $criteria = array(
            '_id' => new MongoID($file_id),
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

    public function deleteFile($file_id) {
        $criteria = array(
            '_id' => new MongoId($file_id),
            'license' => $_SESSION['license'],
        );

        $this->coll->remove($criteria);
    }

    public function getFile($file_id, $license) {
        
        if(!isset($license)){
            $license = $_SESSION['license'];
        }
        
        $criteria = array(
            '_id' => new MongoId($file_id),
            'license' => $license,
        );

        return $this->coll->findOne($criteria);
    }

    public function getFiles($license) {
        $query = array(
            'license' => $license
        );

        return $this->coll->find($query);
    }

    public function getPath() {
        return '/var/www/drainware/ddi/uploads/';
    }

}
?>

