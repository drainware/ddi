<?php

class RoomModel extends Model {

    private $conf;
    private $db;
    private $m;
    private $tag;

    public function __construct() {

        $mongo_model = new MongoModel();
        $this->m = $mongo_model->connect();
        $this->db = $this->m->ddi;
        $this->table = $this->db->rooms;
        $this->tag = "Bloqueados";
    }

    public function getRooms() {
        $rooms = $this->table->find();
        return $rooms;
    }

    public function newRoom($room) {

        $room['status'] = 1;
        $out = $this->table->insert($room);
        return $out;
    }

    public function deleteRoom($idRoom) {


        $criteria = array(
            '_id' => new MongoId($idRoom),
        );
        $r = $this->table->remove($criteria);
        return True;
    }

    public function updateRoom($room) {

        return $this->table->save($room);
    }

    public function getRoom($idRoom) {

        $criteria = array(
            '_id' => new MongoId($idRoom),
        );
        $r = $this->table->findOne($criteria);
        return $r;
    }

    public function getRoomStatus($room) {
        $rangeA = long2ip($room['rangea']);
        $rangeB = long2ip($room['rangeb']);
        $tag = $this->tag;
        //iptables call
        exec("/sbin/iptables -L $tag", $output);

        $pattern = "/\d+\.\d+\.\d+\.\d+\-\d+\.\d+\.\d+\.\d+/";
        preg_match_all($pattern, implode(' ', $output), $ranges);
        foreach ($ranges[0] as $range) {
            if ($rangeA . "-" . $rangeB == $range) {
                return "blocked";
            }
        }
        return "unbloqued";
    }

    public function translateToStatus($status) {
        if ($status == "1")
            return "blocked";
        else
            return "unbloqued";
    }

    public function blockRoom($idRoom) {

        $room = $this->getRoom($idRoom);
        $rangeA = long2ip($room['rangea']);
        $rangeB = long2ip($room['rangeb']);
        $range = $rangeA . "-" . $rangeB;
        $tag = $this->tag;

        $output = exec("/sbin/iptables -A $tag -m iprange --src-range $range -j REJECT");
        $this->table->update(array("_id" => new MongoId($idRoom)), array('$set' => array('status' => 0)));
    }

    public function unblockRoom($idRoom) {

        $room = $this->getRoom($idRoom);
        $rangeA = long2ip($room['rangea']);
        $rangeB = long2ip($room['rangeb']);
        $range = $rangeA . "-" . $rangeB;
        $tag = $this->tag;

        $output = exec("/sbin/iptables -D $tag -m iprange --src-range $range -j REJECT");
        $this->table->update(array("_id" => new MongoId($idRoom)), array('$set' => array('status' => 1)));
    }

}
?>

