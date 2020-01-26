<?php

$lic = $_POST['LIC'];

$lic = str_replace("-", "", $lic);

$url = "";
$code = "";

$now = date('Y-m-d H:i:s');

$foo = fopen("/tmp/lic.log", "a+");



fwrite($foo, "$now : Receiving request...\n");


// 0 
//url = cadena vacia
//if(isValidLicense($lic) ){
//  $url = "http://www.drainware.com/ddi/index.php?module=main&action=register&lic=" . $lic;
//  $code = 1;
//}



$res = isValidLicense($lic);


switch ($res) {
    case 0:
        $url = "http://www.google.com";
        break;
    case 1:
        $url = "http://www.drainware.com/ddi/index.php?module=main&action=register&lic=" . $lic;
        break;
    case -1:
        $url = "http://www.drainware.com";
        break;
}



//$url = "http://www.google.es";
//$code = 31337;

$response = "{\"url\":\"$url\",\"code\":$res, \"server\": \"www.drainware.com\"}";

echo $response;

fwrite($foo, "$now : " . $response . "\n");

fclose($foo);

function isValidLicense($lic) {


    $mongo_model = new MongoModel();
    $m = $mongo_model->connect();
    $db = $m->ddi;
    $col = $db->licenses;

    $license_info = $col->findOne(array('lic' => $lic));
    //var_dump($license_info);
    if (!isset($license_info[lic])) {
        return -1;
    } else {
        if (isset($license_info[ends])) {
            $now_secs = time();
            $expiration_secs = $license_info[ends]->sec;
            if ($now_secs > $expiration_secs) {
                return -2;
            } else {
                return 0;
            }
        } else {
            return 1;
        }
    }
}

?>
