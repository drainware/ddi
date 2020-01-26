<?php 

include_once "../../lib/Spyc.php";
$conf = Spyc::YAMLLoad('/opt/drainware/etc/ddi/' .'conf.yml');

$lang = $conf['configuration']['language']['value'];

include_once "../../lib/lang/" . $lang . ".php"; 

//FIXME: if mimetype of url is image, get size to build a image with gears 

$url = $_GET['url'];
$categories = $_GET['cats'];
$groups = $_GET['groups'];
$user = $_GET['user'];
$host = parse_url($url,PHP_URL_HOST);
$img = rand(1,9);
$reason = json_decode($_GET['reason']);
$virus = $_GET['virus'];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- saved from url=(0024)http://www.drainware.es/ -->
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="es-ES" glassextensioninstalled="true"><head profile="http://gmpg.org/xfn/11"><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">


<title>
Drainware Content Filter&nbsp;|&nbsp;
Drainware Content Filter</title>

<script>
if (top === self) {
  //not iframe
} else {
  window.location.href = "urlad.html"
}
</script>



<link rel="stylesheet" href="style.css" type="text/css" media="screen">
</head>

<body><div id="glass-slide-container-div" class="glass-reset"></div>

<div id="header-wrapper">
  <!-- start #header -->
  <div id="header">
    <div id="logo"><a title="Drainware Content Filter" href="http://www.drainware.com"> <img src="logo.png" alt="Drainware Content Filter"></div>

<div id="trs">
<br>
<br>      

<h1 style="float:right;margin-left:20px;margin-top:15px;"><span style="font-size:60%;">o llámanos</span> 902 056 483</h1>
<div class="call-to-action2">

<!-- LiveZilla Chat Button Link Code (ALWAYS PLACE IN BODY ELEMENT) --><a href="javascript:void(window.open('http://support.drainware.com/chat.php','','width=590,height=610,left=0,top=0,resizable=yes,menubar=no,location=no,status=yes,scrollbars=yes'))"><img src="image.jpg" width="120" height="30" border="0" alt="LiveZilla Live Help"></a><!-- http://www.LiveZilla.net Chat Button Link Code --><!-- LiveZilla Tracking Code (ALWAYS PLACE IN BODY ELEMENT) --><div id="livezilla_tracking" style="display:none"><script type="text/javascript" src="./Drainware Content Filter    Drainware Content Filter_files/server.php"></script></div><script type="text/javascript">
var script = document.createElement("script");script.type="text/javascript";var src = "http://support.drainware.com/server.php?request=track&output=jcrpt&nse="+Math.random();setTimeout("script.src=src;document.getElementById('livezilla_tracking').appendChild(script)",1);</script><noscript>&lt;img src="http://support.drainware.com/server.php?request=track&amp;output=nojcrpt" width="0" height="0" style="visibility:hidden;" alt=""&gt;</noscript><!-- http://www.LiveZilla.net Tracking Code -->

</div>
        
</div>
  <!-- end #header -->
</div>
</div>
</div>
<div  id="page">
  <div id="content">
	<div class="left">
		<div class="title">
		  <div class="first-line"><?=$GLOBALS['lang']['Bk Access']?></div>
		  <div class="second-line"><?=$GLOBALS['lang']['Bk Denied']?></div>
		</div>
		<br>
		<center><img src="images/robots/robot<?php echo $img?>.png"></center>
	</div>
	
	<div class="right">
		<div id="info">
			
			<?=$GLOBALS['lang']['Bk General Message']?>
			<!-- Usted está viendo este mensaje debido a que, la navegación que intenta llevar a cabo viola una o más políticas de seguridad configuradas por el administrador de red. Si cree que no es así, por favor contacte al administrador indicando el motivo del bloqueo y la URL a la que trataba de acceder. -->
			
			
		</div>
		<div class="title-section"><?=$GLOBALS['lang']['Bk Information']?></div>
		<div class="section">
				<!--Se ha denegado el acceso para el usuario--><?=$GLOBALS['lang']['Bk Information Message 1']?> <strong><?php echo $user?></strong> <?=$GLOBALS['lang']['Bk Information Message 2']?>:<br /><br />
			<?php echo $url?><br />
			<?php
			if(!isset($virus)){
			?>
			(<?=$GLOBALS['lang']['Bk Groups']?>: <i><?php echo $groups?></i>)<br />	
			<?php
			}
			?>
		</div>
		<div class="title-section"><?=$GLOBALS['lang']['Bk Reason']?></div>
		<div class="section">
			<?php
			if(isset($virus)){

			  echo $virus;

			}else{
			  echo $GLOBALS['lang']['Bk Reason Message'], ": <?php echo $host?><br />";
			  foreach($reason->categories as $r){
			    echo '<strong>' . $r->name . '</strong>: ' . $r->keywords[0]->coef . ' ( keywords = ';
			    foreach($r->keywords[0]->tags as $tag){
			      echo "'" . $tag . "' ";
			    }
			    echo ')<br />';
			  }
			}
			?>
		</div>
		
		<div class="title-section"><?=$GLOBALS['lang']['Bk Categories']?></div>
		<div class="section">
			<?php
			if(isset($virus))
			  echo "Malware";
			else
			  echo $categories;
			?> 
		</div>

		<?php
		if(!isset($virus)){
		?>		
		<div class="title-section"><?=$GLOBALS['lang']['Bk Solution']?></div>
		<div class="section">
			<!--Esta página podría formar parte de una categoría bloqueada. En caso de no estar de acuerdo y disponer de los permisos necesarios, puedes incluirla en la lista blanca del menú Drainware que se encuentra en la barra de tareas--><?=$GLOBALS['lang']['Bk Solution Message 1']?><br></br>

<!--Se tiene que agregar está url--><?=$GLOBALS['lang']['Bk Solution Message 2']?>:  <?php echo $host?> 
		</div>
		<?php
		}
		?>
	</div>
  </div>
</div>

</div>
</body>
</html>
