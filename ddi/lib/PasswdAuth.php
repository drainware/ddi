<?php

class PasswdAuth {


    var $auth;

    var $htPasswdPath;

    // Where to go when authorized
    // If is is not set then PHP_SELF is used as a default
    var $authorizedUrl;

    // Where to go when not authorized
    var $notAuthorizedUrl;

    // The deliminator used in the passwd file
    var $deliminator = ':';


    function PasswdAuth($authorizedUrl = '', $notAuthorizedUrl = '',$file = '') {
        global $PHP_SELF;
        $file == '' ?  $this->htPasswdPath = $GLOBALS['conf']['prefix'] . "/etc/ddi/proxyusers" : $this->htPasswdPath = $file;
        $authorizedUrl == '' ? $this->authorizedUrl = $PHP_SELF : $this->authorizedUrl = $authorizedUrl;
        $this->notAuthorizedUrl = $notAuthorizedUrl;
    }


    /*
        Returns true if the given user with a given password is found in the passwd file. False otherwise.
    */

    function checkPasswd($user,$password) {
        $lines = &$this->_getUsersArray();

        foreach($lines as $line) {

            if (($this->_retrieveUsername($line) == $user) && ($this->_retrievePassword($line) == $password)) {
                return true;
            }
        }
        return false;
    }

    /*
        Returns true if the given user is found in the passwd file. False otherwise.
    */

    function checkUser($user) {
        $lines = &$this->_getUsersArray();
        foreach($lines as $line) {
            if($this->_retrieveUsername($line) == $user) {
                return true;
            }
        }
        return false;
    }

    /*
        Removes the given user from the passwd file. If the user was removed succesfully,
        true is returned, false otherwise (e.g.: if the user was not in the file, false
        is returned.
    */
    function deleteUser($user) {
        $lines = &$this->_getUsersArray();
        $usersArr = array();
        $result = false;
        foreach($lines as $line) {
            if($this->_retrieveUsername($line) != $user) {
                $usersArr[] = $line;
            } else {
                $result = true;
            }
        }
        $this->_saveUsersArray($usersArr);
        return $result;
    }

    /*
        Adds the given user to the passwd file. If the user was added succesfully,
        true is returned, false otherwise (e.g.: if the user was already in the file, false
        is returned.
    */
    function addUser($user, $password) {
        if(!$this->checkUser($user)) {
            $usersArr = &$this->_getUsersArray();
            $usersArr[] = $user.$this->deliminator.crypt($password,base64_encode(CRYPT_STD_DES));
            //$usersArr[] = $user.$this->deliminator.$password;
            $this->_saveUsersArray($usersArr);
            return true;
        }
        return false;
    }

    /*
        Changes the password of the given user. If the password was changes succesfully,
        true is returned, false otherwise (e.g.: if the user was not in the file, false
        is returned.
    */
    function changePassword($user, $newPassword) {
        $lines = &$this->_getUsersArray();
        $usersArr = array();
        $changed = false;
        foreach($lines as $line) {
            if($this->_retrieveUsername($line) != $user) {
                $usersArr[] = $line;
            } else {
                $usersArr[] = $user.$this->deliminator.crypt($newPassword,base64_encode(CRYPT_STD_DES));
                $changed = true;
            }
        }
        if($changed) {
            $this->_saveUsersArray($usersArr);
        }
        return $changed;
    }

    function getUsers() {
        $lines = &$this->_getUsersArray();
        $users = array();
        foreach($lines as $line) {
            $users[] = $this->_retrieveUsername($line);
        }
        return $users;
    }

    /*
        PRIVATE FUNCTIONS
    */

    function _getUsersArray() {
        $filename = $this->htPasswdPath;
        
        if(!filesize($filename)>0)
          return "";
        
        $fp = fopen($filename, 'r');
        $file_contents = fread($fp, filesize($filename));
        fclose($fp);

        //var_dump(trim($file_contents));

        return explode ("\n", trim($file_contents));
    }

    function setPasswdPath($path){
        $this->htPasswdPath = $path;
    }

    function _saveUsersArray(&$arr) {
        $file_contents = implode("\n", $arr);
        $filename = $this->htPasswdPath;
        $fp = fopen($filename, 'w');
        fwrite ($fp, trim($file_contents));
        fclose($fp);
    }

    function _retrieveUsername($line) {
        return substr($line, 0, strrpos($line, $this->deliminator));
    }

    function _retrievePassword($line) {
        return substr($line, strrpos($line, $this->deliminator) + 1);
    }
}

?>
