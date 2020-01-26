<?PHP

class RouteController extends Controller {

    public function __construct($name = null) {
        parent::__construct($name);
    }

    /*
     * Depending on the passed action it runs a method.
     * It must to recieve the showAction method at least.
     */


    protected function showAction() {
        $model = new RouteModel(); 
        benchmark("before load routes");
        $routes = $model->getRoutes();
        $options = array(
            "routes" => $routes,
        );
        $this->options = array_merge($this->options, $options);
        benchmark("before render and after execute controller"); 
    }

   

    protected function deleteAction() {
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

    protected function createAction() {

       
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
 


    protected function newAction() {

        $options = array(
            "room" => $room,
        );

        $this->options = array_merge($this->options, $options);
	}

}
?>
