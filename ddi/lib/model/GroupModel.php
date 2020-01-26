<?

class GroupModel extends Model {

    private $name;
    private $path;
    private $mail;
    private $db;
    private $col;

    public function __construct() {

        $mongo_model = new MongoModel();
        $conn = $mongo_model->connect();

        $this->db = $conn->ddi;
        $this->col = $this->db->groups;
    }
    
    public function createDefaultGroup($license) {

        $cloud_model = new CloudModel();
        $cloud_user = $cloud_model->getCloudUserByLicense($license);

        $default_group = array(
            'license' => $license,
            'name' => 'default',
            'screenshot_severity' => $cloud_user['type'] == 'freemium' ? 'low' : 'none',
            'endpoint_modules' => array(
                'clipboard', 'keylogger', 'screenshot',
                'pendrive', 'network device src', 'network device dst',
                'google drive', 'dropbox', 'skydrive',
            ),
            'mime_types' => array(
                'application/msword', 'application/vnd.ms-powerpoint', 'application/vnd.ms-excel',
                'application/vnd.ms-office', 'application/vnd.ms-word-2007+', 'application/vnd.ms-powerpoint-2007+',
                'application/vnd.ms-excel-2007+', 'application/pdf', 'application/x-7z-compressed', 'application/zip',
                'application/x-tar', 'application/x-bzip2', 'application/x-gzip', 'application/octet-stream',
                'application/x-iso9660-image', 'text/troff', 'text/x-pascal', 'text/x-java', 'text/html', 'text/text',
            ),
            'created_time' => new MongoDate(),
        );

        $this->col->insert($default_group);
    }

    public function existGroup($license, $name) {
        $query = array(
            'name' => $name,
            'license' => $license,
        );
        $out = $this->col->findOne($query);
        if ($out == null) {
            return false;
        }
        return true;
    }

    public function createGroup($type, $name, $path = null) {
        $id = new MongoID();
        $group_obj = array(
            '_id' => $id,
            'license' => $_SESSION['license'],
            'type' => $type,
            'name' => $name,
            'path' => $path,
            'created_time' => new MongoDate(),
        );

        $this->col->insert($group_obj);
        return $id;
    }

    public function saveGroup($group_object) {
        $this->col->save($group_object);
    }

    public function updateGroup($gid, $new_data){
        $criteria = array(
            '_id' => new MongoID($gid),
        );
        $data = array(
            '$set' => $new_data,
        );
        $option = array(
            'upsert' => true,
        );
        
        $this->col->update($criteria, $data, $option);
    }
    
    public function removeGroup($gid) {
        $criteria = array(
            '_id' => new MongoID($gid),
        );
        return $this->col->remove($criteria);
    }

    public function getGroups($license = null) {
        $cloud_model = new CloudModel();
        $user_auth = $cloud_model->getClientUserAuth();

        $license = !isset($license) ? $_SESSION['license'] : $license;

        $query = array();
        $query['license'] = $license;

        if ($user_auth == 'ldap') {
            $query['$or'] = array(
                array('type' => 'ldap'),
                array('name' => 'default')
            );
        }

        return $this->col->find($query)->sort(array('_id' => 1));
    }
    
    public function getLocalGroups($license = null){
        $license = !isset($license) ? $_SESSION['license'] : $license;
        
        $criteria = array(
            'license' => $license,
            'type' => 'local',
        );
        return $this->col->find($criteria);
    }

    public function getDefaultGroup($license = null) {
        $license = !isset($license) ? $_SESSION['license'] : $license;

        $criteria = array(
            'license' => $license,
            'name' => 'default',
        );
        return $this->col->findOne($criteria);
    }

    public function getGroup($group_id) {
        $criteria = array(
            '_id' => new MongoID($group_id),
        );
        return $this->col->findOne($criteria);
    }

    public function getGroupByPath($path, $license = null) {
        $license = !isset($license) ? $_SESSION['license'] : $license;
        
        $criteria = array(
            'license' => $license,
            'path' => $path,
        );
        return $this->col->findOne($criteria);
    }
    
    public function getCreatedTime($name, $license = null) {
        $license = !isset($license) ? $_SESSION['license'] : $license;

        $criteria = array(
            'license' => $license,
            'name' => $name,
        );

        $group = $this->col->findOne($criteria);

        return $group['created_time'];
    }

    public function getEncryptedFiles($license, $name) {
        $criteria = array(
            'license' => $license,
            'name' => $name,
        );

        $group = $this->col->findOne($criteria);

        $encryptedValue = 0;
        if (isset($group)) {
            $encryptedValue = $group['encrypted_files'];
        }

        return $encryptedValue;
    }

    public function setEncryptedFiles($license, $name, $value) {

        $find_criteria = array(
            'license' => $license,
            'name' => $name,
        );

        $group_object = $this->col->findOne($find_criteria);

        $criteria = array(
            '_id' => $group_object['_id'],
        );

        $data = array(
            '$set' => array(
                'encrypted_files' => $value,
            )
        );
        $option = array(
            'upsert' => true,
        );

        $this->col->update($criteria, $data, $option);
    }

    public function getScreenShotSeverity($license) {

        $criteria = array(
            'license' => $license,
            'name' => 'default',
        );

        $group = $this->col->findOne($criteria);

        $scs_value = 'none';
        if (isset($group)) {
            $scs_value = $group['screenshot_severity'];
        }

        return $scs_value;
    }

    public function setScreenShotSeverity($license, $value) {

        $find_criteria = array(
            'license' => $license,
            'name' => 'default',
        );

        $default_group = $this->col->findOne($find_criteria);

        $criteria = array(
            '_id' => $default_group['_id'],
        );
        $data = array(
            '$set' => array(
                'screenshot_severity' => $value,
            )
        );
        $option = array(
            'upsert' => true,
        );

        $this->col->update($criteria, $data, $option);
    }

    public function getEndpointModules($license) {

        $criteria = array(
            'license' => $license,
            'name' => 'default',
        );

        $group = $this->col->findOne($criteria);

        $endpoint_modules = array();
        if (isset($group)) {
            $endpoint_modules = $group['endpoint_modules'];
        }

        return $endpoint_modules;
    }

    public function setEndpointModules($license, $value) {

        $find_criteria = array(
            'license' => $license,
            'name' => 'default',
        );

        $default_group = $this->col->findOne($find_criteria);

        $criteria = array(
            '_id' => $default_group['_id'],
        );
        $data = array(
            '$set' => array(
                'endpoint_modules' => $value,
            )
        );
        $option = array(
            'upsert' => true,
        );

        $this->col->update($criteria, $data, $option);
    }

    public function getMimeTypes($license) {

        $criteria = array(
            'license' => $license,
            'name' => 'default',
        );

        $group = $this->col->findOne($criteria);

        $mime_types = array();

        if (isset($group)) {
            $mime_types = $group['mime_types'];
        }

        return $mime_types;
    }

    public function setMimeTyoes($license, $value) {

        $find_criteria = array(
            'license' => $license,
            'name' => 'default'
        );

        $default_group = $this->col->findOne($find_criteria);

        $criteria = array(
            '_id' => $default_group['_id'],
        );
        $data = array(
            '$set' => array(
                'mime_types' => $value,
            )
        );
        $option = array(
            'upsert' => true,
        );

        $this->col->update($criteria, $data, $option);
    }

    public function countGroups() {
        $criteria = array(
            'license' => $_SESSION['license'],
        );
        $count = $this->col->count($criteria);
        return $count;
    }
    
    ##################################################################################################
    
        public function getUniqueDefaultGroup() {
        $query = array(
            'license' => null,
            'name' => 'default',
        );
        return $this->col->find($query);
    }

    public function exists($name) {

        $group = $this->col->findOne(array("name" => $name));
        if (!empty($group)) {
            return True;
        } else {
            return False;
        }
    }

    public function setName($name) {

        $this->name = $name;
    }

    //jpalanco: set the path during the import process
    public function setPath($path) {

        $this->path = $path;
    }

    public function getName() {

        return $this->name;
    }

    public function getMail() {

        return $this->mail;
    }

    public function save() {

        $obj = array("name" => $this->name, "path" => $this->path, "created_time" => new MongoDate(), "encrypted_files" => 0, "categories" => array(), "extensions" => array(), "black_list" => array(), "white_list" => array());
        $this->col->save($obj);
    }

    //jpalanco: recover path saved while the importing process
    public function getLdapPath($name) {

        $group = $this->col->findOne(array("name" => $name));
        if (!empty($group)) {
            $path = $group['path'];
        } else {
            $path = "unknown";
        }
        return $path;
    }

    public function getCategoriesBlocked($name) {


        $group = $this->col->findOne(array("name" => $name));

        if (!empty($group)) {
            $blocked_categories = $group['categories'];
        } else {
            $blocked_categories = array();
        }
        return $blocked_categories;
    }

    // jpalanco: for map groups in getDlpConfigAction and getWebFilterConfigAction
    public function getNameByLdapPath($path) {
        $group = $this->col->findOne(array("path" => $path));
        if (!empty($group)) {
            $name = $group['name'];
        } else {
            $name = null;
        }
        return $name;
    }

    public function getCategories($name) {
        $group = $this->col->findOne(array("name" => $name));

        if (!empty($group)) {
            $blocked_categories = $group['categories'];
        } else {
            $blocked_categories = array();
        }

        $categories = $this->db->category->find();

        foreach ($categories as $category) {

            $subcategories = $this->db->subcategory->find(array("category" => $category['category']));
            foreach ($subcategories as $subcategory) {
                $element = array("display" => $subcategory['subcategory']);
                if (in_array($subcategory['subcategory'], $blocked_categories)) {
                    $element['value'] = true;
                }
                $result[$category['category']][] = $element;
            }
        }
        return $result;
    }

    public function getExtensionsBlocked($name) {
        $group = $this->col->findOne(array("name" => $name));
        if (!empty($group)) {
            $blocked_extensions = $group['extensions'];
        } else {
            $blocked_extensions = array();
        }
        return $blocked_extensions;
    }

    public function getExtensionDetail($extension) {

        return $this->db->extension->findOne(array("name" => $extension), array("_id" => 0));
    }

    public function getExtensions($name) {
        $group = $this->col->findOne(array("name" => $name));
        if (!empty($group)) {
            $blocked_extensions = $group['extensions'];
        } else {
            $blocked_extensions = array();
        }

        $extensions = $this->db->extension->find();

        foreach ($extensions as $extension) {

            $element = array("display" => $extension['name']);
            if (in_array($extension['name'], $blocked_extensions)) {
                $element['value'] = true;
            }
            $result[] = $element;
        }

        return $result;
    }

    public function setCategories($name, $categories) {

        $data = array('$set' => array("categories" => $categories));
        $this->col->update(array("name" => $name), $data, array("upsert" => true));
    }

    public function setExtensions($name, $extensions) {
        $data = array('$set' => array("extensions" => $extensions));
        $this->col->update(array("name" => $name), $data);
    }

    public function setBlackList($name, $black_list) {
        foreach ($black_list as $elem)
            $black_list_aux[] = trim($elem);
        $data = array('$set' => array("black_list" => $black_list_aux));
        $this->col->update(array("name" => $name), $data);
    }

    public function setWhiteList($name, $white_list) {
        foreach ($white_list as $elem)
            $white_list_aux[] = trim($elem);
        $data = array('$set' => array("white_list" => $white_list_aux));
        $this->col->update(array("name" => $name), $data);
    }

    public function getBlackList($name) {

        $group = $this->col->findOne(array("name" => $name));
        return $group['black_list'];
    }

    public function getWhiteList($name) {

        $group = $this->col->findOne(array("name" => $name));
        return $group['white_list'];
    }

    public function remove($name) {
        $this->col->remove(array("name" => $name));
        return true;
    }

    public function chageLdapPath($name, $path) {
        $this->col->update(array("name" => $name), array("path" => $path, "name" => $name));
        return true;
    }
    
}

?>
