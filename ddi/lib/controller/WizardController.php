<?PHP

class WizardController extends Controller {

    public function __construct($name = null) {
        parent::__construct($name);
    }

    /*
     * Depending on the passed action it runs a method.
     * It must to recieve the showAction method at least.
     */

    protected function showAction() { 
        $_SESSION['configured'] = 1;
        $options = array(
            "msg" => "Save ok",
        );
        $this->options = array_merge($this->options, $options);
    }



    protected function saveStep1Action() {

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
       $model->SaveConf($config); 
        $options = array(
            "msg" => "Save ok",
        );
        $this->options = array_merge($this->options, $options);
    }
   
    protected function step2Action() {
        $model = new RouteModel(); 
        $routes = $model->getRoutes();
        $options = array(
            "routes" => $routes,
        );
        $this->options = array_merge($this->options, $options);
    }

   

    protected function deleteStep2Action() {
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

    protected function createStep2Action() {

       
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

     protected function rebootAction() {

       
        exec("reboot");
        $options = array(
            "routes" => $routes,
        );

        $this->options = array_merge($this->options, $options);


    }
}
?>
