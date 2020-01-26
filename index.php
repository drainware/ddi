<?php

chdir("ddi/");

try{
require_once("init.php");
}catch(MongoConnectionException $e)
{
  //Show allways the front page even if mongo is down
  //echo 'Excepción capturada: ',  $e->getMessage(), "\n";
}catch(MongoCursorException $e)
{
  //Show allways the front page even if mongo is down
  //echo 'Excepción capturada: ',  $e->getMessage(), "\n";
}

if($_SESSION['authorized'])
{
  header('Location: /ddi/');
  exit;
}


$smarty = new Smarty();
$smarty->template_dir = '..';

if(!isset($_GET['page'])){
  $page = "main";
  $prefix = "";
}else{
  $page =  $_GET['page'];
  $prefix = "/";
}

$smarty->assign('page', $page);
$smarty->assign('prefix', $prefix);
$smarty->display(realpath(dirname(__FILE__)) . "/" . 'front.tpl');


?>
