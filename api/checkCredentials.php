<?php

include "../ddi/lib/PasswdAuth.php";

$user = $_POST['user'];
$passwd = $_POST['passwd'];

$passwdmodel = new PasswdAuth('', '',  "/opt/drainware/etc/ddi/passwd");

if ($passwdmodel->checkPasswd($user, $passwd)) {
  echo "{\"status\":0}";
}else{
  echo "{\"status\":-1}";
}

?>
