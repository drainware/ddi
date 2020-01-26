<?

class CommunicationModel extends Model {

    private $dwfilter_host;
    private $dwfilter_port;
    private $dwDLP_host;
    private $dwDLP_port;
    private $auth;

    public function __construct() {
        $this->dwfilter_host = $GLOBALS['conf']['ddi']['configuration']['icap_listener']['web-filter']['host']['value'];
        $this->dwfilter_port = $GLOBALS['conf']['ddi']['configuration']['icap_listener']['web-filter']['port']['value'];
        $this->dwDLP_host = $GLOBALS['conf']['ddi']['configuration']['icap_listener']['dlp']['host']['value'];
        $this->dwDLP_port = $GLOBALS['conf']['ddi']['configuration']['icap_listener']['dlp']['port']['value'];
        $this->auth = $GLOBALS['conf']['ddi']['configuration']['authentication']['value'];
    }

    public function sendUpdateAllUsersWF() {
        
        $data = '*';
        
        $fp_webfilter = fsockopen($this->dwfilter_host, $this->dwfilter_port, $errno, $errstr, 30);

        if (!$fp_webfilter) {
            //FIXME: register this error
            //echo "$errstr ($errno)<br />\n";
        } else {
            $out = "$data\r\n\r\n";
            fwrite($fp_webfilter, $out);
            //If we need to verify the host received the data
            //while (!feof($fp)) {
            //  echo fgets($fp, 128);
            //}
            fclose($fp_webfilter);
        }
    }

    public function sendUpdateAllUsersDLP() {
        
        $data = '*';
        
        $fp_DLP = fsockopen($this->dwDLP_host, $this->dwDLP_port, $errno, $errstr, 30);

        if (!$fp_DLP) {
            //FIXME: register this error
            //echo "$errstr ($errno)<br />\n";
        } else {
            $out = "$data\r\n\r\n";
            fwrite($fp_DLP, $out);
            //If we need to verify the host received the data
            //while (!feof($fp)) {
            //  echo fgets($fp, 128);
            //}
            fclose($fp_DLP);
        }
    }

    public function sendUpdateUsersWF($group) {

        $users = Array();

        //echo "$this->auth";

        if ($this->auth == "local") {
            $model = new UserModel();
            $users = $model->getMembersOfGroup($group);
        } elseif ($this->auth == "ldap") {
            $model = new LdapModel();
            $users = $model->getUsersOfGroup($group);
        }



        $data = '';
        foreach ($users as $user) {
            $data = $data . $user . "\n";
        }

        $data = $data . "\n";

        //$fp = fsockopen($this->host, $this->port, $errno, $errstr, 30);
        $fp_webfilter = fsockopen($this->dwfilter_host, $this->dwfilter_port, $errno, $errstr, 30);

        if (!$fp_webfilter) {
            //FIXME: register this error
            //echo "$errstr ($errno)<br />\n";
        } else {
            $out = "$data\r\n\r\n";
            fwrite($fp_webfilter, $out);
            //If we need to verify the host received the data
            //while (!feof($fp)) {
            //  echo fgets($fp, 128);
            //}
            fclose($fp_webfilter);
        }
    }

    public function sendUpdateUsersDLP($group) {

        $users = Array();

        //echo "$this->auth";

        if ($this->auth == "local") {
            $model = new UserModel();
            $users = $model->getMembersOfGroup($group);
        } elseif ($this->auth == "ldap") {
            $model = new LdapModel();
            $users = $model->getUsersOfGroup($group);
        }

        $data = '';
        foreach ($users as $user) {
            $data = $data . $user . "\n";
        }

        $data = $data . "\n";

        $fp_DLP = fsockopen($this->dwDLP_host, $this->dwDLP_port, $errno, $errstr, 30);

        if (!$fp_DLP) {
            //FIXME: register this error
            //echo "$errstr ($errno)<br />\n";
        } else {
            $out = "$data\r\n\r\n";
            fwrite($fp_DLP, $out);
            //If we need to verify the host received the data
            //while (!feof($fp)) {
            //  echo fgets($fp, 128);
            //}
            fclose($fp_DLP);
        }
    }

    public function sendUpdateOneUserWF($user) {

        $data = $user . "\n";

        //$data = $data . "\n";

        $fp_webfilter = fsockopen($this->dwfilter_host, $this->dwfilter_port, $errno, $errstr, 30);

        if (!$fp_webfilter) {
            //FIXME: register this error
            //echo "$errstr ($errno)<br />\n";
        } else {
            $out = "$data\r\n\r\n";
            fwrite($fp_webfilter, $out);
            //If we need to verify the host received the data
            //while (!feof($fp)) {
            //  echo fgets($fp, 128);
            //}
            fclose($fp_webfilter);
        }
    }

    public function sendUpdateOneUserDLP($user) {

        $data = $data . $user . "\n";

        $fp_DLP = fsockopen($this->dwDLP_host, $this->dwDLP_port, $errno, $errstr, 30);

        if (!$fp_DLP) {
            //FIXME: register this error
            //echo "$errstr ($errno)<br />\n";
        } else {
            $out = "$data\r\n\r\n";
            fwrite($fp_DLP, $out);
            //If we need to verify the host received the data
            //while (!feof($fp)) {
            //  echo fgets($fp, 128);
            //}
            fclose($fp_DLP);
        }
    }

    public function sendUpdateDefaultGroupWF() {
        
        $data = '#';
        
        $fp_webfilter = fsockopen($this->dwfilter_host, $this->dwfilter_port, $errno, $errstr, 30);

        if (!$fp_webfilter) {
            //FIXME: register this error
            //echo "$errstr ($errno)<br />\n";
        } else {
            $out = "$data\r\n\r\n";
            fwrite($fp_webfilter, $out);
            //If we need to verify the host received the data
            //while (!feof($fp)) {
            //  echo fgets($fp, 128);
            //}
            fclose($fp_webfilter);
        }
    }

    public function sendUpdateDefaultGroupDLP() {
        
        $data = '#';
        
        $fp_DLP = fsockopen($this->dwDLP_host, $this->dwDLP_port, $errno, $errstr, 30);

        if (!$fp_DLP) {
            //FIXME: register this error
            //echo "$errstr ($errno)<br />\n";
        } else {
            $out = "$data\r\n\r\n";
            fwrite($fp_DLP, $out);
            //If we need to verify the host received the data
            //while (!feof($fp)) {
            //  echo fgets($fp, 128);
            //}
            fclose($fp_DLP);
        }
    }

}

?>
