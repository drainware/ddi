<?php

require 'lib/view/smarty/Smarty.class.php';
require 'lib/model/swift/swift_required.php';
require 'lib/model/Predis/Autoloader.php';

include realpath(dirname(__FILE__)) . '/../scripts/analyzefile/analyze_file.php';


$GLOBALS['conf']['prefix'] = "/opt/drainware/";
$GLOBALS['conf']['version'] = "<!-- Content Filter 2.0-->";
$GLOBALS['conf']['config_directory'] = $GLOBALS['conf']['prefix'] . "etc/";
$GLOBALS['conf']['config_mongo_directory'] = $GLOBALS['conf']['config_directory'] ."mongo/";
$GLOBALS['conf']['benchmark_file'] = '/tmp/benchmark.log';
$GLOBALS['conf']['cache_rss'] = '/tmp/rss~';

function benchmark($message) {
    static $start = NULL;
    if (is_null($start)) {
        $start = get_microtime();
    } else {
        $benchmark = get_microtime() - $start;
        $start = get_microtime();
        $fh = fopen($GLOBALS['conf']['benchmark_file'], 'a');
        fwrite($fh, "$benchmark: $message action: " . $_GET['action'] . " module: " . $_GET['module'] . "\n");
        fclose($fh);
        return $benchmark;
    }
}

function get_microtime() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}

function autoLoader($className) {
//Directories added here must be 
//relative to the script going to use this file. 
//New entries can be added to this list
    $directories = array(
        'lib/',
        'lib/controller/',
        'lib/model/',
        'lib/view/'
    );

    //Add your file naming formats here
    $fileNameFormats = array(
        '%s.php',
        '%s.class.php',
        'class.%s.php',
        '%s.inc.php'
    );

    // this is to take care of the PEAR style of naming classes
    $path = str_ireplace('_', '/', $className);
    if (@include_once $path . '.php') {
        return;
    }
    foreach ($directories as $directory) {
        foreach ($fileNameFormats as $fileNameFormat) {
            $path = $directory . sprintf($fileNameFormat, $className);
            if (file_exists($path)) {
                include_once $path;
                return;
            }
        }
    }
}

spl_autoload_register('autoLoader');

$GLOBALS['conf']['mongo'] = Spyc::YAMLLoad($GLOBALS['conf']['config_mongo_directory'] . 'mongo.yml');

//MongoSessionModel::register('session', 'session');

function session_started() {
    if (isset($_SESSION)) {
        return true;
    } else {
        return false;
    }
}

if (!session_started()) {
    session_start();
}

$config_model = new ConfigModel();
$config = $config_model->getAdvancedConfig();

$GLOBALS['conf']['ddi']['configuration'] = $config['config'];

if (isset($_SESSION['language'])) {
    $lang = $_SESSION['language'];
} else {
    $lang = $GLOBALS['conf']['ddi']['configuration']['language']['value'];
}

//print_r($ddiconf);
//$lm = new LangModel();
//$langs = $lm->getLanguages();

require_once 'lib/lang/' . $lang . '.php';


?>
