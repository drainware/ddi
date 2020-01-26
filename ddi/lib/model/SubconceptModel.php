<?php

class SubconceptModel extends Model {

    private $conn;
    private $db;
    private $subconcept_col;

    public function __construct() {

        $mongo_model = new MongoModel();
        $this->conn = $mongo_model->connect();
        $this->db = $this->conn->dlp;
        $this->subconcept_col = $this->db->subconcepts;
    }

    public function getSubconcepts() {
        $subconcepts = $this->subconcept_col->find();
        return $subconcepts;
    }

    public function newSubconcept($subconcept) {

        $out = $this->subconcept_col->insert($subconcept);
        return $out;
    }

    public function deleteSubconcept($idSubconcept) {

        $criteria = array(
            '_id' => new MongoId($idSubconcept),
        );
        $r = $this->subconcept_col->remove($criteria);
        return $r;
    }

    public function updateSubconcept($subconcept) {

        return $this->subconcept_col->save($subconcept);
    }

    public function getSubconcept($idSubconcept) {

        $criteria = array(
            '_id' => new MongoId($idSubconcept),
        );
        $r = $this->subconcept_col->findOne($criteria);
        return $r;
    }

    public function getSubConceptsByIdConcept($idConcept) {
        $query = Array(
            'concept' => $idConcept,
        );
        $subconcepts = $this->subconcept_col->find($query);
        return $subconcepts;
    }

    public function getIdConcept($subconcept) {
        $idConcept = $subconcept['concept'];
        return $idConcept;
    }

    public function getGroup($subconcept, $idGroup) {
        $groups = $subconcept['groups'];
        foreach ($groups as $group) {
            if ($group['group_id'] == $idGroup)
                return $group;
        }
        return NULL;
    }

}

?>