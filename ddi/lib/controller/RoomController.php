<?PHP

class RoomController extends Controller {

    public function __construct($name = null) {
        parent::__construct($name);
    }

    /*
     * Depending on the passed action it runs a method.
     * It must to recieve the showAction method at least.
     */


    protected function showAction() {

       $model = new RoomModel();
       $rooms = $model->getRooms(); //We get all the rooms and their statuses
        $options = array(
            "rooms" => $rooms,
        );
        $this->options = array_merge($this->options, $options);
    }
    protected function blockAction() {

        $model = new RoomModel(); 
        $model->blockRoom($_GET['idRoom']); 
        $rooms = $model->getRooms(); //We get all the rooms and their statuses 
        $options = array(
            "rooms" => $rooms,
            "return_to" => Array('module' => 'room', 'action'=> 'show')
        );

        $this->options = array_merge($this->options, $options);

    }
    protected function unblockAction() {
        $model = new RoomModel();
        $model->unblockRoom($_GET['idRoom']);                                                 
        $rooms = $model->getRooms(); //We get all the rooms and their statuses       
        $options = array(
            "rooms" => $rooms,
            "return_to" => Array('module' => 'room', 'action'=> 'show')
        );
        $this->options = array_merge($this->options, $options);

    }

    protected function deleteAction() {
        $model = new RoomModel();
        $model->deleteRoom($_GET['idRoom']);
        $rooms = $model->getRooms(); //We get all the rooms and their statuses
        $return_to = Array('module' => 'room', 'action'=> 'show');
        $options = array(
            "rooms" => $rooms,
            "return_to" => $return_to,
            "state" => $this->state
         );

        $this->options = array_merge($this->options, $options);

    }
    protected function createAction() {
        $model = new RoomModel();
        $rangea = $_GET['group1a'] . "." . $_GET['group2a'] . "." . $_GET['group3a'] . "." . $_GET['group4a'];
        $rangeb= $_GET['group1b'] . "." . $_GET['group2b'] . "." . $_GET['group3b'] . "." . $_GET['group4b']; 
        $room['rangea'] = sprintf('%u',ip2long($rangea));
        $room['rangeb'] = sprintf('%u',ip2long($rangeb)); 
	    $room['name'] = $_GET['name'];
        $room['desc'] = $_GET['desc']; 
        $model->newRoom($room);
        $rooms = $model->getRooms(); //We get all the rooms and their statuses 
        $return_to = Array('module' => 'room', 'action'=> 'show');
        $options = array(
            "rooms" => $rooms,
            "return_to" => $return_to,
            "state" => $this->state
         );

        $this->options = array_merge($this->options, $options);



    }
   protected function editAction() {
        $model = new RoomModel();
        $room = $model->getRoom($_GET['idRoom']);
        $ipa = explode('.',long2ip($room['rangea']));
        $ipb = explode('.',long2ip($room['rangeb']));
        $options = array(
            "room" => $room,
            "ipa" => $ipa,
            "ipb" => $ipb,
        );

        $this->options = array_merge($this->options, $options);


    }
 
    protected function updateAction() {
        $model = new RoomModel();
        $rangea = $_GET['group1a'] . "." . $_GET['group2a'] . "." . $_GET['group3a'] . "." . $_GET['group4a'];
        $rangeb= $_GET['group1b'] . "." . $_GET['group2b'] . "." . $_GET['group3b'] . "." . $_GET['group4b'];
        $room['id'] = new MongoId($_GET['idRoom']); 
        $room['rangea'] = sprintf('%u',ip2long($rangea));
        $room['rangeb'] = sprintf('%u',ip2long($rangeb));
        $room['name'] = $_GET['name'];
        $room['desc'] = $_GET['desc'];
        $room['status'] = 1;	
        $model->updateRoom($room);
        $rooms = $model->getRooms(); //We get all the rooms and their statuses
        $return_to = Array('module' => 'room', 'action'=> 'show');
        $options = array(
            "rooms" => $rooms,
            "return_to" => $return_to,
            "state" => $this->state
         );


        $this->options = array_merge($this->options, $options);


    }

    protected function newAction() {
        $model = new RoomModel();
        $options = array(
            "room" => $room,
        );

        $this->options = array_merge($this->options, $options);

}
}
?>
