<?php

class NetworkPlaceModel extends Model {

    private $coll;

    public function __construct() {
        $mongo_model = new MongoModel();
        $conn = $mongo_model->connect();
        $this->coll = $conn->dlp->network_places;
    }

    public function createNetworkPlace($network_uri, $description) {
        $id = new MongoId();

        $network_place_object = array(
            '_id' => $id,
            'license' => $_SESSION['license'],
            'network_uri' => $network_uri,
            'description' => $description,
        );

        $this->coll->insert($network_place_object);

        return (string) $id;
    }

    public function updateNetworkPlace($network_place_id, $new_data) {
        $criteria = array(
            '_id' => new MongoID($network_place_id),
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

    public function deleteNetworkPlace($network_place_id) {
        $criteria = array(
            '_id' => new MongoId($network_place_id),
            'license' => $_SESSION['license'],
        );

        $this->coll->remove($criteria);
    }

    public function getNetworkPlace($network_place_id, $license = null) {
        
        if(!isset($license)){
            $license = $_SESSION['license'];
        }
        
        $criteria = array(
            '_id' => new MongoId($network_place_id),
            'license' => $license,
        );

        return $this->coll->findOne($criteria);
    }

    public function getNetworkPlaces($license) {
        $query = array(
            'license' => $license
        );

        return $this->coll->find($query);
    }

}

?>