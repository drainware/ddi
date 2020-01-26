<?php

class ConfigModel extends Model {

    private $con;
    private $ddi_db;
    private $config_col;
    
    public function __construct() {
        
        $mongo_model = new MongoModel();
        $this->con = $mongo_model->connect();
        $this->ddi_db = $this->con->ddi;
        $this->config_col = $this->ddi_db->configuration;
    }

    public function getAdvancedConfig(){
        $criteria = array(
            "id" => "advanced"
        );
        return $this->config_col->findOne($criteria);
    }
    
    public function saveAdvancedConfig($request) {
        $config = $this->getAdvancedConfig();
        foreach ($config['config'] as $key => $value) {
            if (isset($value['value'])) {
                $config['config'][$key]['value'] = $this->getValueFromAdvancedConfigRequest($key, $request, $value['type'], $value['path']);
            } else {
                foreach ($value as $subkey => $subvalue) {
                    if (isset($subvalue['value'])) {
                        $config['config'][$key][$subkey]['value'] = $this->getValueFromAdvancedConfigRequest($key . "-" . $subkey, $request, $subvalue['type'], $subvalue['path']);
                    } else {
                        foreach ($subvalue as $subsubkey => $subsubvalue) {
                            $config['config'][$key][$subkey][$subsubkey]['value'] = $this->getValueFromAdvancedConfigRequest($key . "-" . $subkey . "-" . $subsubkey, $request, $subsubvalue['type'], $subsubvalue['path']);
                        }
                    }
                }
            }
        }
            
        $this->config_col->save($config);
        
    }
    
    public function renderAdvancedConfigForm() {

        $output = '';
        $config = $this->getAdvancedConfig();
        foreach ($config['config'] as $key => $value) {
            if (isset($value['value'])) {
                $output.=$this->renderAdvancedConfigType($key, $key, $value['value'], $value['type']);
            } else {
                $output.="<fieldset><legend> $key </legend>";
                foreach ($value as $subkey => $subvalue) {
                    if (isset($subvalue['value'])) {
                        $output.=$this->renderAdvancedConfigType($key . "-" . $subkey, $subkey, $subvalue['value'], $subvalue['type']);
                    } else {
                        $output.="<fieldset><legend>$subkey</legend>";
                        foreach ($subvalue as $subsubkey => $subsubvalue) {
                            $output.=$this->renderAdvancedConfigType($key . "-" . $subkey . "-" . $subsubkey, $subsubkey, $subsubvalue['value'], $subsubvalue['type']);
                        }
                        $output.="</fieldset>";
                    }
                }
                $output.="</fieldset>";
            }
        }
        return $output;
    }
    
    private function provideAdvancedConfigOptions($field) {
        $options = array();
        switch ($field) {
            case 'language':
                $languages_model = new LangModel();
                $options = $languages_model->getLanguages();
                break;
            case 'authentication':
                $auth_methods = Array('ldap', 'local');
                if ($_SERVER['SERVER_NAME'] == 'www.drainware.com'){
                    $auth_methods[] = 'cloud';
                }
                $options = $auth_methods;
                break;
            default:
                break;
        }
        return $options;
    }
    
    private function renderAdvancedConfigType($id, $name, $value, $type) {

        switch ($type) {
            case "password":
                $output = "<p> <span class='entitle_medium'>$name</span>
                                <input type='password' class='text medium required' name='$id' id='$id' value='$value'>
                           </p>";
                break;
            case "boolean":
                if ($value) {
                    $output = "<p><span class='entitle_medium'>$name</span> <input type='checkbox' checked name='$id' value='1'></p>";
                } else {
                    $output = "<p><span class='entitle_medium'>$name</span> <input type='checkbox' name='$id' value='1'></p>";
                }
                break;
            case "option":
                $options = $this->provideAdvancedConfigOptions($id);
                $output = "<p> <span class='entitle_medium'>$name</span> <select name='$id' id='$id'>";
                foreach ($options as $option) {
                    if ($option == $value)
                        $output.= "<option selected value='$option'> $option </option>";
                    else
                        $output.= "<option value='$option'> $option </option>";
                }
                $output.="</select></p>";
                break;
            case "ip":
                $groups = split("\.", $value);
                $output = "<p>
                            <span class='entitle_medium'>$name</span>
                            <input type='text' class='ipgroup text short required' max='255' name='$id-group1' maxlength='3' size='3' value='$groups[0]'>
                            <input type='text' class='ipgroup text short required' max='255' name='$id-group2' maxlength='3' size='3' value='$groups[1]'>
                            <input type='text' class='ipgroup text short required' max='255' name='$id-group3' maxlength='3' size='3' value='$groups[2]'> 
                            <input type='text' class='ipgroup text short required' max='255' name='$id-group4' maxlength='3' size='3' value='$groups[3]'>
      			  </p>";
                break;
            case "file":
                if ($_SERVER['SERVER_NAME'] != "localhost" || $_SERVER['SERVER_NAME'] == "127.0.0.1"){
                    $output = "<p><span class='entitle_medium'> $name </span> $value <input name='$id' id='$id'  type='file' value='$value' style='width: 230px;' /> </p>";
                } else{
                    $output = "<p><span class='entitle_medium'> $name </span> $value <span> You can\'t upload files from the server. </span> </p>";
                }
                break;
            default:
                $output = "<p><span class='entitle_medium'>$name</span><input type='text' class='text medium required' name='$id' id='$id' value='" . $value . "'></p>";
        }

        return $output;
    }
    
    private function getValueFromAdvancedConfigRequest($id, $request, $type, $path) {

        switch ($type) {
            case "boolean":
                if (isset($request[$id])) {
                    $value = True;
                } else {
                    $value = False;
                }
                break;
            case "ip":
                $value = $request[$id . "-group1"] . "." . $request[$id . "-group2"] . "." . $request[$id . "-group3"] . "." . $request[$id . "-group4"];
                break;
            case "file":               
                if ($_FILES[$id]['name'] != "") {
                    $extension = end(explode('.', $_FILES[$id]['name']));
                    switch ($extension) {
                        case "pem":
                            $target_path = $path . "/dw.pem";
                            if (!move_uploaded_file($_FILES[$id]['tmp_name'], $target_path)) {
                                echo "There was an error uploading the file, please try again!";
                            }
                            system('/opt/drainware/scripts/ddi/upload_cert.py > /dev/null &');
                            $value = $_FILES[$id]['name'];
                            break;
                        default:
                            echo "Incorrect file!";
                            $value = "";
                    }
                } else {
                    $value = "";
                }
                break;
            default:
                $value = $request[$id];
        }
        return $value;
    }
    
}

?>
