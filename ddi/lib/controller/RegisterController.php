<?PHP

class RegisterController extends Controller {

    public function __construct($name = null) {
        parent::__construct($name);
    }

    /*
     * Depending on the passed action it runs a method.
     * It must to recieve the showAction method at least.
    */

    protected function showAction() {




        $options = array(
                "software_version" => $GLOBALS['conf']['version']
        );

        $this->options = array_merge($this->options, $options);
    }


    protected function submitAction() {

        if(isset ($_POST['organization_name'])){
            $organization = $_POST['organization_name'];
        }else{
              $this->state['error'] = true;
              $this->state['messages'][] = "Please, specify an organization name.";
        }

        if(isset ($_POST['email'])){
            $email = $_POST['email'];
        }else{
              $this->state['error'] = true;
              $this->state['messages'][] = "Please, specify an email.";
        }

        if(isset ($_POST['phone'])){
            $phone = $_POST['phone'];
        }else{
              $this->state['error'] = true;
              $this->state['messages'][] = "Please, specify a phone number.";
        }

        if(isset ($_POST['address'])){
            $address = $_POST['address'];
        }else{
              $this->state['error'] = true;
              $this->state['messages'][] = "Please, specify an address.";
        }

        if(isset ($_POST['city'])){
            $city = $_POST['city'];
        }else{
              $this->state['error'] = true;
              $this->state['messages'][] = "Please, specify a city.";
        }

        if(isset ($_POST['state'])){
            $state = $_POST['state'];
        }else{
              $this->state['error'] = true;
              $this->state['messages'][] = "Please, specify an state.";
        }

        if(isset ($_POST['zip'])){
            $country = $_POST['zip'];
        }else{
              $this->state['error'] = true;
              $this->state['messages'][] = "Please, specify a postal code.";
        }

        if(isset ($_POST['country'])){
            $country = $_POST['country'];
        }else{
              $this->state['error'] = true;
              $this->state['messages'][] = "Please, specify a country.";
        }

        if(isset ($_POST['installation_id'])){
            $installation = $_POST['installation_id'];
        }else{
              $this->state['error'] = true;
              $this->state['messages'][] = "Please, specify a valid installation id.";
        }

        if(isset ($_POST['passwd'])){
            $pass1 = $_POST['passwd'];
        }else{
              $this->state['error'] = true;
              $this->state['messages'][] = "Please, specify a password.";
        }

        if(isset ($_POST['passwd2'])){
            $pass2 = $_POST['passwd2'];
        }else{
              $this->state['error'] = true;
              $this->state['messages'][] = "Please, specify the password for twice.";
        }

        if($_POST['passwd'] != $_POST['passwd2']){
            $pass2 = $_POST['passwd2'];
        }else{
              $this->state['error'] = true;
              $this->state['messages'][] = "Password does not match.";
        }

        //se de de alta en el server

    }
}

?>
