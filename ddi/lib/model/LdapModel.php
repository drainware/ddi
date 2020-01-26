<?

class LdapModel extends Model {

    private $groups;
    private $groups_members = Array();
    private $groups_parents = Array();
    private $ldap_conf;

    public function __construct($license = null) {

        if (!isset($license)) {
            $license = $_SESSION['license'];
        }

        $cloud_model = new CloudModel();
        $client = $cloud_model->getCloudUserByLicense($license);

        $this->ldap_conf = array();
        $this->ldap_conf = $client['ldap'];

        $conn = ldap_connect($this->ldap_conf['host'], $this->ldap_conf['port']);
        ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, $this->ldap_conf['version']);
        ldap_bind($conn, $this->ldap_conf['dn'], $this->ldap_conf['password']);

        $query = ldap_search($conn, $this->ldap_conf['base'], "(objectclass=Group)");
        $data = ldap_get_entries($conn, $query);

        foreach ($data as $element) {
            if (!empty($element['name'][0])) {
                $this->groups[] = utf8_encode($element['dn']);
            }
        }
    }

    public function getUsersOfGroup($gid) {

        $conn = ldap_connect($this->ldap_conf['host'], $this->ldap_conf['port']);
        ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, $this->ldap_conf['version']);
        ldap_bind($conn, $this->ldap_conf['dn'], $this->ldap_conf['password']);

        $group_model = new GroupModel();
        $group_object = $group_model->getGroup($gid);
        $gpath = $group_object['path'];
        
        $users = array();
        if ($this->ldap_conf['recursive_groups']) {
            $this->findGroupMembersOfGroup($gpath);
            $this->groups_members[] = $gpath;
            foreach ($this->groups_members as $group) {
                foreach ($this->getUserMembersOfGroup($group) as $user) {
                    $user = $this->getUserNameOfUser($user);
                    if (!in_array($user, $users)) {
                        $users[] = $user;
                    }
                }
            }
        } else {
            foreach ($this->getUserMembersOfGroup($gpath) as $user) {
                $user = $this->getUserNameOfUser($user);
                if (!in_array($user, $users)) {
                    $users[] = $user;
                }
            }    
        }
        
        return $users;
    }

    public function getGroupsOfUser($uname) {

        $redis = new RedisModel();
        $groups_name = $redis->getVariable('groupsOf' . $uname);

        if (empty($groups_name)) {

            $conn = ldap_connect($this->ldap_conf['host'], $this->ldap_conf['port']);
            ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, $this->ldap_conf['version']);
            ldap_bind($conn, $this->ldap_conf['dn'], $this->ldap_conf['password']);

            $query = ldap_search($conn, $this->ldap_conf['base'], "(samaccountname=$uname)"); //get reesource id
            $data = ldap_get_entries($conn, $query); // get information of user
            
            $groups_name = Array();
            
            if ($data['count'] > 0) {
                $groups = $data[0]['memberof']; //get groups of user

                for ($i = 0; $i < $groups['count']; $i++) {
                    $groups_name[] = $groups[$i];
                }

                if ($this->ldap_conf['recursive_groups']) {
                    foreach ($groups_name as $group) {
                        $this->findGroupParentsOfGroup($group);
                        $this->groups_parents[] = $group;
                    }

                    $groups_name = $this->groups_parents;
                }
                
                $redis->setVariable('groupsOf' . $uname, $groups_name);
            }
             
        }

        return $groups_name;
    }

    public function getGroupMembersOfGroup() {
        return $this->groups_members;
    }

    public function getGroupParentsOfGroup() {
        return $this->groups_parents;
    }

    public function findGroupMembersOfGroup($gpath) {

        $conn = ldap_connect($this->ldap_conf['host'], $this->ldap_conf['port']);
        ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, $this->ldap_conf['version']);
        ldap_bind($conn, $this->ldap_conf['dn'], $this->ldap_conf['password']);

        $query = ldap_search($conn, $this->ldap_conf['base'], "(&(objectclass=Group)(memberof=" . $gpath . "))");
        $data = ldap_get_entries($conn, $query);

        $groups = Array();
        foreach ($data as $elem) {
            if (!empty($elem['name'][0])) {
                $groups[] = $elem['dn'];
            }
        }

        foreach ($groups as $group) {
            if (in_array($group, $this->groups_members)) {
                continue;
            } else {
                $this->groups_members[] = $group;
                $this->findGroupMembersOfGroup($group);
            }
        }
    }

    public function findGroupParentsOfGroup($groupname) {

        $conn = ldap_connect($this->ldap_conf['host'], $this->ldap_conf['port']);
        ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, $this->ldap_conf['version']);
        ldap_bind($conn, $this->ldap_conf['dn'], $this->ldap_conf['password']);

        $query = ldap_search($conn, $this->ldap_conf['base'], "(&(objectclass=Group)(member=" . $groupname . "))");
        $data = ldap_get_entries($conn, $query);

        $groups = Array();
        foreach ($data as $elem) {
            if (!empty($elem['name'][0])) {
                $groups[] = $elem['dn'];
            }
        }

        foreach ($groups as $group) {
            if (in_array($group, $this->groups_parents)) {
                continue;
            } else {
                $this->groups_parents[] = $group;
                $this->findGroupParentsOfGroup($group);
            }
        }
    }

    public function getUserMembersOfGroup($groupname) {

        $conn = ldap_connect($this->ldap_conf['host'], $this->ldap_conf['port']);
        ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, $this->ldap_conf['version']);
        ldap_bind($conn, $this->ldap_conf['dn'], $this->ldap_conf['password']);

        $query = ldap_search($conn, $this->ldap_conf['base'], "(&(objectclass=User)(memberof=" . $groupname . "))");
        $data = ldap_get_entries($conn, $query);

        $users = Array();
        foreach ($data as $elem) {
            if (!empty($elem['name'][0])) {
                $users[] = $elem['dn'];
            }
        }
        return $users;
    }

    public function getUserNameOfUser($user) {

        $conn = ldap_connect($this->ldap_conf['host'], $this->ldap_conf['port']);
        ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, $this->ldap_conf['version']);
        ldap_bind($conn, $this->ldap_conf['dn'], $this->ldap_conf['password']);

        $query = ldap_search($conn, $this->ldap_conf['base'], "(&(distinguishedname=$user)(objectclass=User))", array($this->ldap_conf['username_attr']));
        $data = ldap_get_entries($conn, $query);

        $username = $data[0][strtolower($this->ldap_conf['username_attr'])][0];
        return $username;
    }

    public function getGroupsOfUserByEmail($mail) {

        $conn = ldap_connect($this->ldap_conf['host'], $this->ldap_conf['port']);
        ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, $this->ldap_conf['version']);
        ldap_bind($conn, $this->ldap_conf['dn'], $this->ldap_conf['password']);

        $query = ldap_search($conn, $this->ldap_conf['base'], "(mail=$mail)");
        $data = ldap_get_entries($conn, $query);

        $groups_name = array();

        if ($data['count'] > 0) {
            $groups = $data[0]['memberof'];
            
            for ($i = 0; $i < $groups['count']; $i++) {
                $groups_name[] = $groups[$i];
            }

            if ($this->ldap_conf['recursive_groups']) {
                foreach ($groups_name as $group) {
                    $this->findGroupParentsOfGroup($group);
                    $this->groups_parents[] = $group;
                }

                $groups_name = $this->groups_parents;
            }
        }
        
        return $groups_name;
    }

    // jpalanco: I don't like this function, this is more like view. We should provide arrays and objects
    // during the view it should be represented as we need, but here..
    public function getGroups() {

        $group_model = new GroupModel();
        $imported_groups = $group_model->getGroups();

        $groups = array();
        if ($imported_groups->count() > 0) {
            foreach ($imported_groups as $group) {
                $groups[] = $group['path'];
            }
            $groups = array_diff($this->groups, $groups);
        } else {
            $groups = $this->groups;
        }

        return array_values($groups);
    }
    
    public static function testConnection($conf){
        $code = -1;
        
        $conn = ldap_connect($conf['host'], $conf['port']);
            
        ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, $conf['version']);

        $bind = ldap_bind($conn, $conf['dn'], $conf['password']);

        if ($bind) {
            $code = -2;
            $query = ldap_search($conn, $conf['base'], "(objectclass=Group)");
            $data = ldap_get_entries($conn, $query);

            if ($data['count'] > 0) {
                $code = 0;
            }
        }

        return $code;
    }

}

?>
