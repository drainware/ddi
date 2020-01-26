<?

class UserModel extends Model {
    
    private $coll;

    public function __construct() {
        $mongo_model = new MongoModel();
        $conn = $mongo_model->connect();
        $this->coll = $conn->ddi->users;
    }

    public function createUser($name, $password, $groups, $license = null) {


        $license = !isset($license) ? $_SESSION['license'] : $license;

	// trak.io analytics
        $curl = new CurlModel("https://api.trak.io/v1/track");
        //$curl = new CurlModel("http://127.0.0.1:31337/");
        $data['distinct_id'] = $license;
        $data['event'] = "User Aded";
        $vars['token'] = "571464b2396e32e0de8f2e87a18afea91b438c6a";
        $vars['data'] = json_encode($data);
        $curl->post($vars);


        $criteria = array(
            'license' => $license,
            'name' => $name
        );

        $out = false;
        if ($this->coll->findOne($criteria) == null) {
            $user_object = array(
                'license' => $license,
                'name' => $name,
                'password' => md5($password),
                'group' => $groups
            );
            $out = $this->coll->insert($user_object);
        }

        return $out;
    }

    public function updateUser($uid, $new_data) {
        $criteria = array(
            '_id' => new MongoID($uid)
        );
        $data = array(
            '$set' => $new_data
        );
        $option = array(
            'upsert' => true
        );

        $this->coll->update($criteria, $data, $option);
    }

    public function updateUserPassword($uid, $password) {
        $criteria = array(
            '_id' => new MongoID($uid)
        );
        $data = array(
            '$set' => array(
                'password' => md5($password)
            )
        );
        $option = array(
            'upsert' => true
        );

        return $this->coll->update($criteria, $data, $option);
    }

    public function saveUserObject($user_object) {
        return $this->coll->save($user_object);
    }

    public function removeUser($user_id) {
        $criteria = array(
            '_id' => new MongoID($user_id)
        );
        return $this->coll->remove($criteria);
    }

    public function getUsers($license = null) {
        if (!isset($license)) {
            $license = $_SESSION['license'];
        }
        $criteria = array(
            'license' => $license,
        );
        return $this->coll->find($criteria);
    }

    public function getUserById($user_id) {
        $criteria = array(
            '_id' => new MongoID($user_id),
        );
        return $this->coll->findOne($criteria);
    }

    public function getUserByName($name, $license = null) {
        $license = !isset($license) ? $_SESSION['license'] : $license;

        $criteria = array(
            'license' => $license,
            'name' => $name
        );
        return $this->coll->findOne($criteria);
    }

    public function getUsersByGroupId($group_id) {
        $criteria = array(
            'license' => $_SESSION['license'],
            'group' => $group_id
        );
        return $this->coll->find($criteria);
    }

}
?>
