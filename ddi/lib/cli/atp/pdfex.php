<?php
include_once('drainware-cmdline.php');
include_once('drainware-pdflib.php');

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);


$fp = fopen("php://stderr", 'w');



if (!isset($PDFstringSearch)) {
	fputs($fp, "ERROR: Signatures not found. drainware-pdfsig.php is probably corrupt.\n");
	exit(0);
}


if (!isset($argv[1])) {
	fputs($fp,  "Please specify a file or directory to process\n");
	exit(0);
}



	if (is_file($argv[1])) {
		$file = array ('filename' => $argv[1], 'md5' => md5_file($argv[1]), 'sha256' => '');

		$result = analysePDF($file);

		//display only the requested element of the $results array, otherwise all
		if (isset($argv[2]) && isset($result[$argv[2]]))
			fputs($fp,  $result[$argv[2]]."\n");
		else
			print_result($result);
	} else if ($argv[1] == '--version' || $argv[1] == 'version' || $argv[1] == '-version' || $argv[1] == '--info') {
		if (!isset($global_engine) ) {
			fputs($fp,  "ERROR: Signatures not found.\n");
			exit(1);
		} 
		fputs($fp,  "Detection engine: $global_engine\n");
		fputs($fp,  "PDF string signatures: ".count($PDFstringSearch)."\n");
		fputs($fp,  "PDF hex signatures: ".count($PDFhexSearch)."\n");
		fputs($fp,  "PDF object hashes: ".count($PDFblockHash)."\n");
		
	} else if ($handle = opendir($argv[1])) {
		while (false !== ($filen = readdir($handle))) {
			if ($filen != "." && $filen != ".." && is_file($argv[1]."/".$filen) && strtolower(end(explode(".", $filen))) != 'txt' && strtolower(end(explode(".", $filen))) != 'php') {
				$file = array ('filename' => $argv[1]."/".$filen, 'md5' => md5_file($argv[1]."/".$filen), 'sha256' => '');
				fputs($fp,  $argv[1]."/".$filen."\n");
				$result = analysePDF($file);

				//display only the requested element of the $results array, otherwise all
				if (isset($argv[2]) && isset($result[$argv[2]]))
					fputs($fp,  $result[$argv[2]]."\n");
				else
					print_result($result);
			}
		}
	} else {
		fputs($fp,  "File <".$argv[1]."> not found\n");
		exit(0);
	}


//optional debugging handlers
function logdebug($string) {
	//fputs($fp,  $string."\n");
}
function logverbose($string) {
	//fputs($fp,  $string."\n");
}

function print_result($result) {
   echo json_encode($result);
}
?>
