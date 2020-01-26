<?PHP

class NetworkController extends Controller {

    public function __construct($name = null) {
        parent::__construct($name);
    }

    /*
     * Depending on the passed action it runs a method.
     * It must to recieve the showAction method at least.
     */

    protected function reloadAction() {
	//jpalanco: maybe we can get the ip address from the previus forms
	$ip = exec("cat /etc/network/interfaces | grep address | awk '{ print \$2 }' ");
	system('/opt/drainware/scripts/ddi/reloadnetwork.py > /dev/null &');

        $options = array(
            "ip" => $ip,
        );

	$this->options = array_merge($this->options, $options);



    }
    
    protected function showAction() { 
     
        $options = array(
            "msg" => "Save ok",
        );
        $this->options = array_merge($this->options, $options);
    }

    protected function saveAction() {

       $model = new NetworkModel();
       $config = array();
       if ($_POST['step1'] != "dhcp"){
       $config['dhcp'] = 0;
       $config['static'] = $_POST['group1a'] . "." . $_POST['group2a'] . "." . $_POST['group3a'] . "." . $_POST['group4a'];
       $config['mask']  = $_POST['group1b'] . "." . $_POST['group2b'] . "." . $_POST['group3b'] . "." . $_POST['group4b'];
       $config['gateway'] = $_POST['group1c'] . "." . $_POST['group2c'] . "." . $_POST['group3c'] . "." . $_POST['group4c'];
       $config['dns'] = $_POST['dnsgroup1a'] . "." . $_POST['dnsgroup2a'] . "." . $_POST['dnsgroup3a'] . "." . $_POST['dnsgroup4a'];
       $config['dns2'] = $_POST['dnsgroup1b'] . "." . $_POST['dnsgroup2b'] . "." . $_POST['dnsgroup3b'] . "." . $_POST['dnsgroup4b'];
       } else {
       $config['dhcp'] = 1;
       }
       $model->SaveConf($config); //We get all the rooms and their statuses
        $options = array(
            "msg" => "Save ok",
        );
        $this->options = array_merge($this->options, $options);
	//commented by jpalanco
        //exec("reboot");
    }
   
    /*room*/

   protected function showRoomAction() {

       $model = new RoomModel();
       $rooms = $model->getRooms(); //We get all the rooms and their statuses
        $options = array(
            "rooms" => $rooms,
        );
        $this->options = array_merge($this->options, $options);
    }
    protected function blockRoomAction() {

        $model = new RoomModel(); 
        $model->blockRoom($_GET['idRoom']); 
        $rooms = $model->getRooms(); //We get all the rooms and their statuses 
        $options = array(
            "rooms" => $rooms,
            "return_to" => Array('module' => 'network', 'action'=> 'showRoom')
        );

        $this->options = array_merge($this->options, $options);

    }
    protected function unblockRoomAction() {
        $model = new RoomModel();
        $model->unblockRoom($_GET['idRoom']);                                                 
        $rooms = $model->getRooms(); //We get all the rooms and their statuses       
        $options = array(
            "rooms" => $rooms,
            "return_to" => Array('module' => 'network', 'action'=> 'showRoom')
        );
        $this->options = array_merge($this->options, $options);

    }

    protected function deleteRoomAction() {
        $model = new RoomModel();
        $model->deleteRoom($_GET['idRoom']);
        $rooms = $model->getRooms(); //We get all the rooms and their statuses
        $return_to = Array('module' => 'network', 'action'=> 'showRoom');
        $options = array(
            "rooms" => $rooms,
            "return_to" => $return_to,
            "state" => $this->state
         );

        $this->options = array_merge($this->options, $options);

    }
    protected function createRoomAction() {
        $model = new RoomModel();
        $rangea = $_GET['group1a'] . "." . $_GET['group2a'] . "." . $_GET['group3a'] . "." . $_GET['group4a'];
        $rangeb= $_GET['group1b'] . "." . $_GET['group2b'] . "." . $_GET['group3b'] . "." . $_GET['group4b']; 
        $room['rangea'] = sprintf('%u',ip2long($rangea));
        $room['rangeb'] = sprintf('%u',ip2long($rangeb)); 
	    $room['name'] = $_GET['name'];
        $room['desc'] = $_GET['desc']; 
        $model->newRoom($room);
        $rooms = $model->getRooms(); //We get all the rooms and their statuses 
        $return_to = Array('module' => 'network', 'action'=> 'showRoom');
        $options = array(
            "rooms" => $rooms,
            "return_to" => $return_to,
            "state" => $this->state
         );

        $this->options = array_merge($this->options, $options);



    }
   protected function editRoomAction() {
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
 
    protected function updateRoomAction() {
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
        $return_to = Array('module' => 'network', 'action'=> 'showRoom');
        $options = array(
            "rooms" => $rooms,
            "return_to" => $return_to,
            "state" => $this->state
         );


        $this->options = array_merge($this->options, $options);


    }

    protected function newRoomAction() {
        $model = new RoomModel();
        $options = array(
            "room" => $room,
        );

        $this->options = array_merge($this->options, $options);

   }

   /*route*/

    protected function showRouteAction() {
        $model = new RouteModel(); 
        benchmark("before load routes");
        $routes = $model->getRoutes();
        $options = array(
            "routes" => $routes,
        );
        $this->options = array_merge($this->options, $options);
        benchmark("before render and after execute controller"); 
    }

   

    protected function deleteRouteAction() {
        $model = new RouteModel(); 
        $route = array();
        $route['network'] = $_POST['group1a'] . "." . $_POST['group2a'] . "." . $_POST['group3a'] . "." . $_POST['group4a'];
        $route['mask'] = $_POST['mask'];
        $route['gateway'] = $_POST['group1b'] . "." . $_POST['group2b'] . "." . $_POST['group3b'] . "." . $_POST['group4b']; 
        $model->deleteRoute($route);
        $routes = $model->getRoutes();
        $options = array(
            "routes" => $routes,
        );
        $this->options = array_merge($this->options, $options);

    }

    protected function createRouteAction() {

       
        $model = new RouteModel(); 
        $route = array();
        $route['network'] = $_POST['group1a'] . "." . $_POST['group2a'] . "." . $_POST['group3a'] . "." . $_POST['group4a'];
        $route['mask'] = $_POST['mask'];
        $route['gateway'] = $_POST['group1b'] . "." . $_POST['group2b'] . "." . $_POST['group3b'] . "." . $_POST['group4b']; 
        $model->newRoute($route);
        $routes = $model->getRoutes(); 
        $options = array(
            "routes" => $routes,
        );

        $this->options = array_merge($this->options, $options);


    }
 

    protected function newRouteAction() {

        $options = array(
            "room" => $room,
        );

        $this->options = array_merge($this->options, $options);
	}
	

   /*host white list*/

    protected function showHostAction() {
        $model = new HostWhiteListModel(); 
        $hosts = $model->getList();
        $options = array(
            "hosts" => $hosts,
        );
        $this->options = array_merge($this->options, $options);
    }

   

    protected function deleteHostAction() {
        $model = new HostWhiteListModel(); 
        $model->deleteHost($_POST['id']);
        $options = array(
            "return_to" => Array('module' => 'network', 'action'=> 'showHost')
        );
        $this->options = array_merge($this->options, $options);

    }

    protected function createHostAction() {

       
        $model = new HostWhiteListModel(); 
        $host = array();

        if ($_POST['type']=="range") {

          $host['host'] = array($_POST['group1a'] . "." . $_POST['group2a'] . "." . $_POST['group3a'] . "." . $_POST['group4a'], $_POST['group1b'] . "." . $_POST['group2b'] . "." . $_POST['group3b'] . "." . $_POST['group4b']);
          $host['type'] = "range";
        } else if ($_POST['type']=="ip")  {

          $host['host'] = $_POST['group1'] . "." . $_POST['group2'] . "." . $_POST['group3'] . "." . $_POST['group4'];  
          $host['type'] = "ip";

        }

        $model->newHost($host);
        $options = array(
            "return_to" => Array('module' => 'network', 'action'=> 'showHost')
        );
        $this->options = array_merge($this->options, $options);


    }
 

    protected function newHostAction() {

        $options = array();
        $this->options = array_merge($this->options, $options);

	}
	
	protected function firewallAction() {
	
	    $model = new FirewallModel();
	    $cat = $model->getCategories();
	    $options = array("firewall_categories" => $cat, "tst" => print_r($cat, true));
        $this->options = array_merge($this->options, $options);
	
	}
	
	protected function firewallModifyAction() {
		$model = new FirewallModel();
		$model->blockProtocols($_POST['blocked_protocols']);
        $options = array(
            "return_to" => Array('module' => 'network', 'action'=> 'firewall')
        );
        $this->options = array_merge($this->options, $options);

	}

}
?>
