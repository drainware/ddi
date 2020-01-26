<?php

class ConceptModel extends Model {

    private $conn;
    private $db;
    private $concept_col;

    public function __construct() {

        $mongo_model = new MongoModel();
        $this->conn = $mongo_model->connect();
        $this->db = $this->conn->dlp;
        $this->concept_col = $this->db->concepts;
    }

    public function getConcepts() {
        $concepts = $this->concept_col->find();
        return $concepts;
    }

    public function newConcept($concept) {
        $out = $this->concept_col->insert($concept);
        return $out;
    }

    public function deleteConcept($idConcept) {
        $criteria = array(
            '_id' => new MongoId($idConcept),
        );
        $r = $this->concept_col->remove($criteria);
        return $r;
    }

    public function updateConcept($concept) {
        return $this->concept_col->save($concept);
    }

    public function getConcept($idConcept) {
        $criteria = array(
            '_id' => new MongoId($idConcept),
        );
        $r = $this->concept_col->findOne($criteria);
        return $r;
    }

    public function getGroup($concept, $idGroup) {
        $groups = $concept['groups'];
        foreach ($groups as $group) {
            if ($group['group_id'] == $idGroup)
                return $group;
        }
        return NULL;
    }

}

?>