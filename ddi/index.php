<?php

require_once("init.php");

if (file_exists("configured")) {
    $_SESSION['configured'] = 1;
} else {
    touch("configured");
    $_SESSION['configured'] = 0;
}

$module = "main";
if (isset($_GET['module'])) {
    $module = $_GET['module'];
}

$action = "show";
if (isset($_GET['action'])) {
    $action = $_GET['action'];
}

//if (ioncube_file_is_encoded() === false) {
if (true) {
    $GLOBALS['conf']['ddi']['registered'] = "true";
    $GLOBALS['conf']['ddi']['licensed_to'] = "Drainware Development";
    $GLOBALS['conf']['ddi']['modules'] = array("dlp", "atp", "forensics");
    $GLOBALS['conf']['ddi']['max_groups'] = 10;
    $GLOBALS['conf']['ddi']['expiry'] = 3534451200;
} else {
    //Obtains the license properties
    $ic_prop = ioncube_license_properties();
    $ic_info = ioncube_file_info();

    $expiry = ioncube_license_has_expired();
    $registered = $ic_prop['features']['value']['registered'];
    if ($registered === "true") {
        if ($expiry === TRUE) {
            $module = "main";
            $action = "showLicense";
        } else {
            $GLOBALS['conf']['ddi']['registered'] = $ic_prop['features']['value']['registered'];
            $GLOBALS['conf']['ddi']['licensed_to'] = $ic_prop['features']['value']['licensed_to'];
            $GLOBALS['conf']['ddi']['modules'] = $ic_prop['features']['value']['modules'];
            $GLOBALS['conf']['ddi']['max_groups'] = $ic_prop['features']['value']['max_groups'];
            $GLOBALS['conf']['ddi']['expiry'] = $ic_info['FILE_EXPIRY'];
        }
    } else {
        if (!($module == "network" && $action == "show")) {
            if (!($module == "main" && $action == "uploadLicense")) {
                $module = "main";
                $action = "showLicense";
            }
        }

        $GLOBALS['conf']['ddi']['registered'] = $ic_prop['features']['value']['registered'];
        $GLOBALS['conf']['ddi']['licensed_to'] = $ic_prop['features']['value']['licensed_to'];
        $GLOBALS['conf']['ddi']['modules'] = array();
        $GLOBALS['conf']['ddi']['max_groups'] = 0;
        $GLOBALS['conf']['ddi']['expiry'] = mktime();
    }
}

$modules = new ModulesModel();
$menu = $modules->getAvaibleMenu();

if ($module != "api" && $action != "register" && $module != "cloud" && $action != 'showDailyDLPEvents' &&  $action != 'getDailyDLPEvents') {

    if (!$_SESSION['configured']) {
        $module = "wizard";
        $action = "show";
    } else if (!$_SESSION['authorized']) {
        //FIXME: implement $return_to
        //syslog(LOG_WARNING, debug_print_backtrace());
        $module = "main";
        $action = "login";
    }

    if (!in_array($module, $menu['menu']) && $module != 'api') {

        $module = $menu['menu'][0];
        $action = "show";
    }
}

if($module == "cloud" && $_SESSION['authorized']) {
    switch ($action) {
        case "downloadEndpoint":
            break;
        case "savePremiumUpgrade":
            break;
        case "paymentNotification":
            break;
        case "checkPayment":
            break;
        default:
            $module = "main";
            $action = "show";
            break;
    }
}

//UGLY HACK
if (isset($_SESSION['username'])) {
    if ($_SESSION['username'] != "admin") {
        if ($module == 'main') {
            switch ($action) {
                case "show":
                    break;
                case "showInviteFriend":
                    break;
                case "showCredentials":
                    break;
                case "showCloudConfig":
                    break;
                case "showWireTransfer":
                    break;
                case "showUserAuth":
                    break;
                case "showNotifications":
                    break;
                case "showTimeZone":
                    break;
                case "changeCredentials":
                    break;
                case "testLDAPConnection":
                    break;
                case "saveUserAuth":
                    break;
                case "saveWireTransfer":
                    break;
                case "saveNotifications":
                    break;
                case "saveTimeZone":
                    break;
                case "sendInvitations":
                    break;
                case "logout":
                    break;
                default:
                    $action = "show";
                    break;
            }
        }
    }
}

$controller_name = ucfirst($module) . "Controller";
$controller = new $controller_name($module);
$controller->setAction($action);
$controller->load();
?>
