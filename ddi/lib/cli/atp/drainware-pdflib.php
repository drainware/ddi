<?php

//set memory and length of text data limits for regex searches
ini_set('pcre.backtrack_limit', 10000000);
ini_set('pcre.recursion_limit', 10000000);
ini_set('memory_limit', '256M');
//max running time
set_time_limit(99999);
$global_test = 0; //debug mode
include_once('drainware-pdfsig.php');


$encodingMethods = array('PA' => 'PDF ASCIIHexDecode', 'PL' => 'PDF LZWDecode', 'P8' => 'PDF ASCII85Decode',
	'PR' => 'PDF RunLengthDecode', 'PF' => 'PDF FlateDecode', 'pf' => 'PDF FlateDecode2', 'OC' => '', 
	'ES' => 'JavaScript Escaped', 'JA' => 'JavaScript Ascii codes', 'UC' => 'Unicode',
	'RH' => 'JavaScript Hex codes', 'CF' => 'JavaScript fromCharCode', 'OC' => 'PDF Octal codes',
	'oc' => 'PDF Octal codes2', 'pa' => 'PDF ASCIIHexDecode2', 'JB' => 'JavaScript in Annotation Block',
	'JR' => 'JavaScript in Block', 'CR' => 'PDF Standard Encryption',);







function pdfDecrypt($message, $key, $vector) {
    return mcrypt_decrypt(
        MCRYPT_RIJNDAEL_128, 
        $key, 
        $message, 
        MCRYPT_MODE_CBC, 
        $vector
    );
}



function pdfDecryptRC4($message, $key, $ishex = 0) {
	return rc4($key, $message, $ishex);
}


function rc4 ($pwd, $data, $ispwdHex = 0)
		{
			if ($ispwdHex)
				$pwd = @pack('H*', $pwd); // valid input, please!

			$key[] = '';
			$box[] = '';
			$cipher = '';

			$pwd_length = strlen($pwd);
			$data_length = strlen($data);

			for ($i = 0; $i < 256; $i++)
			{
				$key[$i] = ord($pwd[$i % $pwd_length]);
				$box[$i] = $i;
			}
			for ($j = $i = 0; $i < 256; $i++)
			{
				$j = ($j + $box[$i] + $key[$i]) % 256;
				$tmp = $box[$i];
				$box[$i] = $box[$j];
				$box[$j] = $tmp;
			}
			for ($a = $j = $i = 0; $i < $data_length; $i++)
			{
				$a = ($a + 1) % 256;
				$j = ($j + $box[$a]) % 256;
				$tmp = $box[$a];
				$box[$a] = $box[$j];
				$box[$j] = $tmp;
				$k = $box[(($box[$a] + $box[$j]) % 256)];
				$cipher .= chr(ord($data[$i]) ^ $k);
			}
			return $cipher;
		}



function lowOrder($data) {
	$new = '';
	for ($i = strlen($data)-2; $i>=0; $i-=2) {
		$new .= $data[$i].$data[$i+1];
	}
	return $new;
}


function flashExplode ($stream) {

	$magic = substr($stream, 0, 3);

	if ($magic == "CWS") {
		$header = substr($stream, 4, 5);
		$content = substr($stream, 10);

		$uncompressed = gzinflate($content);
		return "FWS".$header.$uncompressed;
	} else
		return $stream;

}



function asciihexdecode($hex)
{
	$bin = '';
	for ($i = 0; $i < strlen($hex)-1; $i++) {
		if (ctype_alnum($hex[$i]) &&  ctype_alnum($hex[$i+1])) {
			$n = $hex[$i].$hex[$i+1];
			$bin .= chr(hexdec($n));
			$i++;
 		} else {
		//do nothing
		}
	}
	return $bin;
}

function pdfhex($hex)
{
	$str = '';
	for ($i = 0; $i < strlen($hex); $i++) {
		if ($i+2 <= strlen($hex) && $hex[$i] == '#' && ctype_alnum($hex[$i+1]) &&  ctype_alnum($hex[$i+2])) {
			$n = $hex[$i+1].$hex[$i+2];
			$str .= chr(hexdec($n));
			$i+=2;
 		} else {
			$str .= $hex[$i];
		}

	}

  return $str;
}

function octal_decode($oct) {
		$dec = '';
		for ($i = 0; $i < strlen($oct); $i++) {
			if ($oct[$i] == '\\') {
				$n = '';
				$i++;
				for (; $i < strlen($oct); $i++) {
					if (is_numeric($oct[$i]))
						$n .= $oct[$i];
					else {
						$i--;
						break;
					}
				}
				$dec .= chr(octdec($n));
 			} else {
				$dec .= $oct[$i];
			}
		}
	return $dec;
}


function unliteral_octal($oct) {
		$dec = '';
		for ($i = 0; $i < strlen($oct); $i++) {
			if ($oct[$i] == '\\' && strlen($oct) > $i+3) {
				$octal = $oct[$i+1].$oct[$i+2].$oct[$i+3];
				if ( is_numeric($octal) ) {
					$dec .= chr(octdec($octal));
					//$dec .= chr($octal);
					$i+= 3;
				} else {
					$dec .= $oct[$i];
				}
 			} else {
				$dec .= $oct[$i];
			}
		}
	return $dec;
}



function hex2bin($h) {
	if (!is_string($h))
		return null;
	$r='';
	for ($a=0; $a<strlen($h); $a+=2) {
		if (isset($h{($a+1)}))
			$r.=chr(hexdec($h{$a}.$h{($a+1)}));
	}
  	return $r;
}


function flatedecode($data) {

	$errlev = error_reporting();
	error_reporting(0);
	$out = gzinflate($data);
	error_reporting($errlev);

	return $out;
}



function lzw_decode($data) {
	$lz = new LZW();
	$d = $lz->decode($data);
	return $d;
}


function ascii85_decode($data) {
    $output = '';

    //get rid of the whitespaces
    $whiteSpace = array("\x00", "\x09", "\x0A", "\x0C", "\x0D", "\x20");
    $data = str_replace($whiteSpace, '', $data);
    
    $data = substr($data, 0, (strlen($data) - 2));
    $dataLength = strlen($data);

    for ($i = 0; $i < $dataLength; $i += 5) {
        $b = 0;

        if (substr($data, $i, 1) == "z") {
            $i -= 4;
            $output .= pack("N", 0);
            continue;
        }

        $c = substr($data, $i, 5);

        if(strlen($c) < 5) {
            //partial chunk
            break;
        }

        $c = unpack('C5', $c);
        $value = 0;

        for ($j = 1; $j <= 5; $j++) {
            $value += (($c[$j] - 33) * pow(85, (5 - $j)));
        }

        $output .= pack("N", $value);
    }

    //decode partial
    if ($i < $dataLength) {
        $value = 0;
        $chunk = substr($data, $i);
        $partialLength = strlen($chunk);

        //pad the rest of the chunk with u's 
        //until the lenght of the chunk is 5
        for ($j = 0; $j < (5 - $partialLength); $j++) {
            $chunk .= 'u';
        }

        $c = unpack('C5', $chunk);

        for ($j = 1; $j <= 5; $j++) {
            $value += (($c[$j] - 33) * pow(85, (5 - $j)));
        }
        
        $foo = pack("N", $value);
        $output .= substr($foo, 0, ($partialLength - 1));
    }

    return $output;
}

function runlengthdecode($data) {
    $dataLength = strlen($data);
    $output = '';
    $i = 0;

    while($i < $dataLength) {
        $byteValue = ord($data[$i]);

        //EOD byte
        if ($byteValue == 128) {
            break;
        }

        if ($byteValue < 128) {
            $output .= substr($data, $i + 1, ($byteValue + 1));
            $i += $byteValue + 2;
        }

        if ($byteValue > 128) {
            $numOfTimesToCopy = 257 - $byteValue;
            $copyValue = $data[$i + 1];

            for($j = 0; $j < $numOfTimesToCopy; $j++) {
                $output .= $copyValue;
            }

            $i += 2;
        }
    }
    return $output;
}

function strhex($string) {

    $hex = '';
    $len = strlen($string);
   
    for ($i = 0; $i < $len; $i++) {
        
        $hex .= str_pad(dechex(ord($string[$i])), 2, 0, STR_PAD_LEFT);
   
    }
       
    return $hex;
    
}


function decryptObj(&$document, &$object, $key, $stream) {


	if ($key != '') {
		$object['key_long']  = $key.$object['decrypt_part'];
		$object['key'] = md5(hex2bin($object['key_long']));
			

		if ($document['r'] == 5) {
			$t = pdfDecrypt(
				substr($stream, 16),
				hex2bin($key),
				substr($stream, 0, 16));
				//remove padding - aes
			//echo "result=$t==\n";
			$last = ord(substr($t, -1));
			//echo "checking for padding of $last\n";
			$padding = substr($t, -$last);
			//echo "padding is ".strhex($padding)."\n";
			$pad_fail = 0;
			for($i = 0; $i < $last; $i++) {
				if ($padding[$i] != chr($last)) {
					$pad_fail = 1;
					break;
				}
			}

			if ($pad_fail == 0) {
				//echo "trimming padding\n";
				$t = substr($t, 0, (strlen($t)-$last) );
			}

		} else if ($document['r'] == 4) {
			$t = pdfDecrypt(
				substr($stream, 16),
				hex2bin($object['key']),
				substr($stream, 0, 16));
				//remove padding - aes
			$last = ord(substr($t, -1));
			//echo "checking for padding of $last\n";
			$padding = substr($t, -$last);
			//echo "padding is ".strhex($padding)."\n";
			$pad_fail = 0;
			for($i = 0; $i < $last; $i++) {
				if ($padding[$i] != chr($last)) {
					$pad_fail = 1;
					break;
				}
			}

			if ($pad_fail == 0) {
				//echo "trimming padding\n";
				$t = substr($t, 0, (strlen($t)-$last) );
			}

		} else {
			if ($document['v'] == 1){
				//$object['decrypt_part'] = "0a00000000";
				//$object['key_long']  = $key.$object['decrypt_part'];
				//$object['key'] = md5(hex2bin($object['key_long']));
				$object['key'] = substr($object['key'], 0, 20);
			}
			if ($document['v'] == 3) {
				$object['obj_hex'] = pdfxor(pdfhex2str($object['obj_hex']),  pdfhex2str('3569AC'));
				$object['gen_hex'] = pdfxor(pdfhex2str($object['gen_hex']),  pdfhex2str('CA96'));

				//echo "tyler ".$object['obj_hex']." ".$object['gen_hex']."\n";
				$object['decrypt_part'] = lowOrder($object['obj_hex']).lowOrder($object['gen_hex']);
				if ($document['v'] >= 3) {
					$object['decrypt_part'] .= "73416C54";
				}
				$object['key_long']  = $key.$object['decrypt_part'];
				$object['key'] = md5(hex2bin($object['key_long']));

			}
			//echo $object['object']." using key ".$object['key']."\n";
			$t = pdfDecryptRC4($stream,$object['key'], 1);
			//echo "rc4 ".strlen($t)." ".$t."\n";
		}
	} else 
		$t = $stream;
	return $t;
}

function pdfhex2str($hex)
{
	$str = '';
  for($i=0;$i<strlen($hex);$i+=2)
  {
    $str.=chr(hexdec(substr($hex,$i,2)));
  }
  return $str;
}





function pdfxor($InputString, $KeyPhrase){
 
    $KeyPhraseLength = strlen($KeyPhrase);
 
    // Loop trough input string
    for ($i = 0; $i < strlen($InputString); $i++){
 
        // Get key phrase character position
        $rPos = $i % $KeyPhraseLength;
 
        // Magic happens here:
        $r = ord($InputString[$i]) ^ ord($KeyPhrase[$rPos]);
 
        // Replace characters
        $InputString[$i] = chr($r);
    }
 
    return $InputString;
}

function unliteral($string) {
	$literalEncodings = array(
		'\n' => "\x0a",
		'\r' => "\x0d",
		'\t' => "\x09",
		'\b' => "\x08",
		'\f' => "\x0C",//FF
		'\(' => "\x28",
		'\)' => "\x29",
		"\\\\" => "\x5c",
		//plus octals \ddd
	);	
	foreach ($literalEncodings as $search => $replace) {
		$string = str_replace($search, $replace, $string);
	}
	$string = unliteral_octal($string); //need to handle 1 and 2 digit octals (0-7)
	
	return $string;

}


function pdfSlice($data) {
	global $global_test, $literalEncodings;
	$key = '';

	$master_block_encoding = '';
	$block_encoding = '';

	$result = array('document' => array());
	$result['document']['v'] = "0";


	logDebug("crypto check");

	if (preg_match("/\/AuthEvent\/DocOpen\/CFM\/AESV2/si", $data) || preg_match("/\/Encrypt\s+/s", $data)) {


		//find Encryption defns
		if (preg_match("/\/Encrypt (\d+)\D+(\d+)\D+R/si", $data,$matches)) {
			$result['document']['encrypt_obj'] = $matches[1];
			$result['document']['encrypt_gen'] = $matches[2];
			//echo "Looking for encryption obj ".$result['document']['encrypt_obj']." ".$result['document']['encrypt_gen']."\n";

			preg_match_all("/(\x0a|\x0d|\x20)".$result['document']['encrypt_obj']."[^\d]{1,3}".$result['document']['encrypt_gen']."[^\d]{1,3}obj(.+?)endobj/si", $data, $matches0, PREG_OFFSET_CAPTURE);
			
			//print_r($matches0);
			if (isset($matches0[0])) {
				$ordered = array();
				for($j = 0; $j< count($matches0[0]); $j++) {
					$ordered[$matches0[2][$j][1]] = array();
					for($i = 1; $i< count($matches0); $i++) {
						$ordered[$matches0[2][$j][1]][$i] = $matches0[$i][$j][0];
					}
				}
			}
			//print_r($ordered);
			$encrypt_block = end($ordered);
			$encrypt_block = $encrypt_block[2];
			//print_r($encrypt_block);
			
		}

		if ( !isset($encrypt_block) ) {
			preg_match("/\/Encrypt(.*?)(endobj|$)/si", $data,$matches);
			if (isset($matches[1]))
				$encrypt_block = $matches[1];
		}

		if ( !isset($encrypt_block) )
			$encrypt_block = $data;

		$encrypted = 1;	
		$result['document']['encrypted'] = 1;

		$result['document']['padding'] = '28BF4E5E4E758A4164004E56FFFA01082E2E00B6D0683E802F0CA9FE6453697A'; //standard padding

		$result['document']['u']  = "00000000000000000000000000000000";
		
		$result['document']['o'] = "";
		$result['document']['id'] = "";

		if (preg_match_all("/\/ID[^\[]{0,5}\[\s*<(.*?)>/si", $data, $matchi)) {
			//print_r($matchi);
			$last = count($matchi[1])-1;
			if ($last < 0) $last = 0;
			$result['document']['id'] = $matchi[1][$last];

		} else if (preg_match_all("/\/ID[^\[]{0,5}\[\s*\((.*?)\)/si", $data, $matchi)) {
			//print_r($matchi);
			$last = count($matchi[1])-1;
			if ($last < 0) $last = 0;
			$result['document']['id'] = strhex(unliteral($matchi[1][$last]));

		}
		if (preg_match("/\/O[^\(]{0,5}\((.{32,64}?)\)/si", $encrypt_block, $matcho))
			$result['document']['o'] = strhex($matcho[1]);
		else if (preg_match("/\/O[^\<]{0,5}\<(.{64}?)\>/si", $encrypt_block, $matcho))
			$result['document']['o'] = $matcho[1];


		if ($result['document']['o'] == "" && preg_match("/trailer.{1,400}\/O[^\<]{0,5}\<(.{32,64}?)\>/si", $data, $matcho))
			$result['document']['o'] = $matcho[1];
		$result['document']['o_orig'] = $result['document']['o'];
		if (strlen($result['document']['o']) > 64) { //fix escaped things
			$result['document']['o'] = strhex(unliteral(hex2str($result['document']['o'])));

		}

		$result['document']['key_length'] = 128;
		if (preg_match("/\/Length\s+(\d{1,4})\D/si",$encrypt_block, $matchl))
			$result['document']['key_length'] = $matchl[1];
		if ($result['document']['key_length'] <= 16)
			$result['document']['key_length'] *= 8;

		$result['document']['r'] = 1; //version
		if (preg_match("/\/R (\d{1})\D/si",$encrypt_block, $matchr))
			$result['document']['r'] = $matchr[1]; //version 1-4

		$result['document']['v'] = 4; //version
		if (preg_match("/\/V (\d{1})\D/si", $encrypt_block, $matchv))
			$result['document']['v'] = $matchv[1]; //version 1-4

		if (preg_match("/\/P ([0-9-]*)/si", $encrypt_block, $matchp))
			$result['document']['p'] = $matchp[1]; //permission - 32 bit
		
		if ($result['document']['r'] <= 2) $result['document']['key_length'] = 40;

		//r=5 AESV3 (AES-256) 2011 12 15
		if ($result['document']['r'] == 5) {
			$result['document']['key_length'] = 256;
			//StrF-EFF

			//O is 48 bytes
			if (preg_match("/\/O[^\(]{0,5}\((.{48,132}?)\)/si", $encrypt_block, $matcho))
				$result['document']['o'] = strhex($matcho[1]);
			else if (preg_match("/\/O[^\<]{0,5}\<(.{96,164}?)\>/si", $encrypt_block, $matcho))
				$result['document']['o'] = $matcho[1];

			if (strlen($result['document']['o']) > 96)  //fix escaped things
				$result['document']['o'] = strhex(unliteral(hex2str($result['document']['o'])));

			if (strlen($result['document']['o']) > 96)
				$result['document']['o'] = substr($result['document']['o'], 0, 96);


			if (preg_match("/\/U[\s]{0,5}\((.{48,132}?)\)/si", $encrypt_block, $matcho))
				$result['document']['u'] = strhex($matcho[1]);
			else if (preg_match("/\/U[\s]{0,5}\<(.{96,164}?)\>/si", $encrypt_block, $matcho))
				$result['document']['u'] = $matcho[1];
			if (strlen($result['document']['u']) > 96)  //fix escaped things
				$result['document']['u'] = strhex(unliteral(hex2str($result['document']['u'])));

			if (strlen($result['document']['u']) > 96)
				$result['document']['u'] = substr($result['document']['u'], 0, 96);

			$result['document']['oe'] = "";
			$result['document']['ue'] = "";
			$result['document']['perms'] = "";
		
			if (preg_match("/\/OE[^\(]{0,5}\((.{32,64}?)\)/si", $encrypt_block, $matcho))
				$result['document']['oe'] = strhex($matcho[1]);
			else if (preg_match("/\/OE[^\<]{0,5}\<(.{64}?)\>/si", $encrypt_block, $matcho))
				$result['document']['oe'] = $matcho[1];
			if (strlen($result['document']['oe']) > 64)  //fix escaped things
				$result['document']['oe'] = strhex(unliteral(hex2str($result['document']['oe'])));


			if (preg_match("/\/UE[^\(]{0,5}\((.{32,64}?)\)/si", $encrypt_block, $matcho))
				$result['document']['ue'] = strhex($matcho[1]);
			else if (preg_match("/\/UE[^\<]{0,5}\<(.{64}?)\>/si", $encrypt_block, $matcho))
				$result['document']['ue'] = $matcho[1];
			if (strlen($result['document']['ue']) > 64)  //fix escaped things
				$result['document']['ue'] = strhex(unliteral(hex2str($result['document']['ue'])));

			if (preg_match("/\/Perms[^\(]{0,5}\((.{16,32}?)\)/si", $encrypt_block, $matcho))
				$result['document']['perms'] = strhex($matcho[1]);
			else if (preg_match("/\/Perms[^\<]{0,5}\<(.{32}?)\>/si", $encrypt_block, $matcho))
				$result['document']['perms'] = $matcho[1];
			if (strlen($result['document']['perms']) > 32)  //fix escaped things
				$result['document']['perms'] = strhex(unliteral(hex2str($result['document']['perms'])));


			//Algorithm 3.2a proposed ISO 32000-2
/*To understand the algorithm below, it is necessary to treat the O and U strings in the Encrypt dictionary as made up of three sections. The first 32 bytes are a hash value (explained below). The next 8 bytes are called the Validation Salt. The final 8 bytes are called the Key Salt.*/


			$result['document']['password'] = '';


/*Compute an intermediate user key by computing the SHA-256 hash of the UTF-8 password concatenated with the 8 bytes of user Key Salt. The 32-byte result is the key used to decrypt the 32-byte UE string using AES-256 in CBC mode with no padding and an initialization vector of zero. The 32-byte result is the file encryption key.*/

			//echo "UE: ".$result['document']['ue']."\n";
			//echo "user key salt:".substr($result['document']['u'], 80, 16)."\n";

			$result['document']['ue_key']= hash('sha256', hex2bin($result['document']['password'].substr($result['document']['u'], 80, 16)));

			$result['document']['key'] = strhex(mcrypt_decrypt(MCRYPT_RIJNDAEL_128,hex2bin($result['document']['ue_key']), hex2bin($result['document']['ue']), MCRYPT_MODE_CBC), ''); //AES256

			//echo "ukey: ".$result['document']['key']."\n"; 

/*
Decrypt the 16-byte Perms string using AES-256 in ECB mode with an initialization vector of zero and the file encryption key as the key. Verify that bytes 9-11 of the result are the characters ‘a’, ‘d’, ‘b’. Bytes 0-3 of the decrypted Perms entry, treated as a little-endian integer, are the user permissions. They should match the value in the P key.*/

			//echo "check perms: ".$result['document']['perms']."\n";

			$result['document']['test2'] = strhex(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, hex2bin($result['document']['key']), hex2bin(substr($result['document']['perms'], 0, 32)), MCRYPT_MODE_ECB, ''));

			if (substr(hex2bin($result['document']['test2']), 9,3) == "abd")
				$result['document']['aesv3'] = 1;
			else
				$result['document']['aesv3'] = 0;


			$key = $result['document']['key'];
		} else {

			//Algorithm 3.2

			//pad pw

			//ISO 32000-1 2008 7.6.3.3 Encryption Key Algorithm 2, step a:
			$trimmed = rtrim($result['document']['u'], "0");
			//echo "trimmed ".strlen($trimmed)."\n";
			if (strlen($trimmed) % 2 == 1)
				$trimmed .= "0";
			//echo "trimmed ".strlen($trimmed)."\n";
			$result['document']['password'] = str_pad($trimmed, 64,  $result['document']['padding'], STR_PAD_RIGHT);


			//step b
			//echo "step b ".$result['document']['password']."\n";
			$hashbuilder = $result['document']['password'];

			//step c
			$hashbuilder .= $result['document']['o'];
			//echo "step c ".$result['document']['o']."\n";

			//step d Convert the integer value of the P entry to a 32-bit unsigned binary number
			//and pass these bytes to the MD5 hash function, low-order byte first
	

			if ($result['document']['p'] < 0)
				$permissions = pow(2, 32) + ($result['document']['p']);
			else
				$permissions = $result['document']['p'];

			$result['document']['p_hexh'] = str_pad(dechex( pow(2, 32)- pow(2, 32)+$permissions), 8, 0, STR_PAD_LEFT);
			$result['document']['p_hex'] = lowOrder($result['document']['p_hexh']);
			$result['document']['p_raw'] = $permissions;
			$result['document']['p_max'] = pow(2, 32);
			$result['document']['p_check'] = hexdec($result['document']['p_hexh']);

			$hashbuilder .= $result['document']['p_hex'];
	
			//echo "step c ".lowOrder(dechex($permissions))."\n";

			//step e add id
			//echo "step e ".$result['document']['id']."\n";
			$hashbuilder .= $result['document']['id'];

			//step f revision 4 or greater) If document metadata is not being encrypted,
			//pass 4 bytes with the value 0xFFFFFFFF
			//if ($result['document']['v'] == 4 && $result['document']['EncryptMetadata'] == 'false') {
				//$hashbuilder .= 'FFFFFFFF';
				//echo "step f FFFFFFFF\n";
			//}
	
			//echo "hashbuilder final [".strlen($hashbuilder)."] $hashbuilder\n";
			//step g Finish the hash
			$result['document']['hashbuilder'] = $hashbuilder;
			$hash = md5(hex2bin($hashbuilder));
			//echo "step g hash $hash\n";

			//step h
			if ($result['document']['r'] > 2) {
				for ($i = 0; $i < 50; $i++) {
					$partial = substr($hash,0,$result['document']['key_length']/4);
					$hash = md5(hex2bin($partial));
					//echo "step h $i md5($partial) = $hash\n";
				}
			}
			//echo "step h final hash $hash\n";


			//step i
			if ($result['document']['r'] > 2)
				$key = substr($hash,0,$result['document']['key_length']/4);
			else
				$key = substr($hash,0,10);

			//$key = "63AD011127";
	
			//echo "step i key $key\n";
			$result['document']['key'] = $key;

		}

		logDebug("PDF Encrypted - general key is $key");

	}






	//$block_no = 0;
	logDebug("all obj slicing");

	//all objects n
	unset($matches0);

	//preg_match_all("/(\x0a|\x0d|\x20)(\d{1,4})[^\d]{1,3}(\d{1,2})\sobj/si", $data, $matches0, PREG_OFFSET_CAPTURE);
	preg_match_all("/((\x0a|\x0d|\x20)(\d{1,4})[^\d]{1,3}(\d{1,2})\sobj|(\x0a|\x0d)(xref|trailer)(\x0a|\x0d))/si", $data, $matches0, PREG_OFFSET_CAPTURE);
	//print_r($matches0);
	$ordered = array();

	if (isset($matches0[1])) {
		for($j = 0; $j< count($matches0[0]); $j++) {
			$end = '';
			if (isset($matches0[0][$j+1][1]))
				$end = $matches0[0][$j+1][1]+1;
			else
				$end = 	strlen($data);
			$dup_id = $matches0[0][$j][1]+1;
			if (isset($matches0[6][$j][0]) && ($matches0[6][$j][0] == 'xref' || $matches0[6][$j][0] == 'trailer' )) {
				$start = $matches0[0][$j][1]+1;
				$len = ($end-$start);
				$ordered[$dup_id] = array('otype' => $matches0[6][$j][0], 'obj_id' => '0', 'gen_id' => '0', 'start' => $start,
					'end' => $end, 'len' => $len, 'dup_id' => $dup_id, 'parameters' => substr($data ,$start, $len) );
				//print_r($ordered[$dup_id]);

			} else {

				$start = $matches0[4][$j][1]+strlen($matches0[4][$j][0])+4;
				$len = ($end-$start);
				$ordered[$dup_id] = array('obj_id' => $matches0[3][$j][0], 'gen_id' => $matches0[4][$j][0], 'start' => $start,
					'end' => $end, 'len' => $len, 'dup_id' => $dup_id, 'parameters' => substr($data ,$start, $len) );

			}
				
			//$ordered[$matches0[0][$j][1]] = array();
		}
	}
	//print_r($ordered);

	foreach ($ordered as $dup_id => $vals){
		$index = $vals['obj_id'].".".$vals['gen_id'].".".$dup_id;
		$result[$index] = array('object' => $vals['obj_id'], 'generation' => $vals['gen_id'],
					'obj_hex' => str_pad(dechex($vals['obj_id']), 6, 0, STR_PAD_LEFT),
					'gen_hex' => str_pad(dechex($vals['gen_id']), 4, 0, STR_PAD_LEFT), 'dup_id' => $dup_id,
					'parameters' => $vals['parameters'], 'atype' => 'sas');
		if (isset($vals['otype']))
			$result[$index]['otype'] = $vals['otype'];
			
		$result[$index]['decrypt_part'] = lowOrder($result[$index]['obj_hex']).lowOrder($result[$index]['gen_hex']);
		if ($result['document']['v'] >= 3) {
			$result[$index]['decrypt_part'] .= "73416C54";
		}

				//handle encrypted strings
		if (isset($result['document']['key']) && $result['document']['key'] != '' && !isset($vals['otype']) ) {
					
			preg_match_all("/\((.*?)\)(\x0a|\x0d)/s", $result[$index]['parameters'], $param1);
					//var_dump($param1);
			for($j = 0; $j< count($param1[1]); $j++) {
				//echo "test=".$param1[1][$j]."=endtest\n";
				$p = unliteral($param1[1][$j]);
				//echo "test1=".$p."=endtest1\n";
				$newParams = decryptObj($result['document'], $result[$index], $key, $p);
						
				if ($newParams != '') {
					//echo $newParams;
					$result[$index]['parameters'] = $newParams."\n[encrypted params:]".$result[$index]['parameters'];
				}
			}


		}
	}

	//print_r($result);

	//$block_no = 0;
	logDebug("no stream objects");

	//all objects n
	unset($matches0);

	preg_match_all("/(\x0a|\x0d|\x20)(\d{1,4})[^\d]{1,3}(\d{1,2})\sobj(.*?)endobj/si", $data, $matches0, PREG_OFFSET_CAPTURE);
	//logDebug("1");

	if (isset($matches0[1])) {
		$ordered = array();
		for($j = 0; $j< count($matches0[0]); $j++) {
			$ordered[$matches0[2][$j][1]] = array();
			for($i = 1; $i< count($matches0); $i++) {
				$ordered[$matches0[2][$j][1]][$i] = $matches0[$i][$j][0];
			}
		}
	}
	foreach ($ordered as $dup_id => $val) {
			
		//$block_no++;
			if (!isset($result[$val[2].".".$val[3].".".$dup_id])) {
				//logDebug("2 - ".$val[2]);

				$result[$val[2].".".$val[3].".".$dup_id] = array('object' => $val[2], 'generation' => $val[3],
					'obj_hex' => str_pad(dechex($val[2]), 6, 0, STR_PAD_LEFT),
					'gen_hex' => str_pad(dechex($val[3]), 4, 0, STR_PAD_LEFT), 'dup_id' => $dup_id,
					'parameters' => $val[4], 'atype' => 'nos');

			
				$result[$val[2].".".$val[3].".".$dup_id]['decrypt_part'] = lowOrder($result[$val[2].".".$val[3].".".$dup_id]['obj_hex']).lowOrder($result[$val[2].".".$val[3].".".$dup_id]['gen_hex']);
				if ($result['document']['v'] >= 3) {
					$result[$val[2].".".$val[3].".".$dup_id]['decrypt_part'] .= "73416C54";
				}

				//handle encrypted strings
				if (isset($result['document']['key']) && $result['document']['key'] != '') {
					
					preg_match_all("/\((.*?)\)(\x0a|\x0d)/s", $result[$val[2].".".$val[3].".".$dup_id]['parameters'], $param1);
					//var_dump($param1);
					for($j = 0; $j< count($param1[1]); $j++) {
						//echo "test=".$param1[1][$j]."=endtest\n";
						$p = unliteral($param1[1][$j]);
						//echo "test1=".$p."=endtest1\n";
						$newParams = decryptObj($result['document'], $result[$val[2].".".$val[3].".".$dup_id], $key, $p);
						
						if ($newParams != '') {
							//echo $newParams;
							$result[$val[2].".".$val[3].".".$dup_id]['parameters'] = $newParams."\n[encrypted params:]".$result[$val[2].".".$val[3].".".$dup_id]['parameters'];
						}
					}


				}
			}

			
	}











	//$block_no = 0;
	logDebug("scan all streams");

	unset($matches0);
	//all streams o
	preg_match_all("/(\x0a|\x0d|\x20)(\d{1,4})[^\d]{1,3}(\d{1,2})\s+obj((?:(?! obj).){1,350}?)(s|#73)(t|#74)(r|#72)(e|#65)(a|#61)(m|#6d)(.*?)(e|#65)(n|#6e)(d|#64)(s|#73|o|#6f)/si", $data, $matches0, PREG_OFFSET_CAPTURE);

	if (isset($matches0[0])) {
		$ordered = array();
		for($j = 0; $j< count($matches0[0]); $j++) {
			$ordered[$matches0[2][$j][1]] = array();
			for($i = 1; $i< count($matches0); $i++) {
				$ordered[$matches0[2][$j][1]][$i] = $matches0[$i][$j][0];
			}
		}
	}
	foreach ($ordered as $dup_id => $val) {
		//$block_no++;
		
			$result[$val[2].".".$val[3].".".$dup_id] = array('object' => $val[2], 'generation' => $val[3],
					'obj_hex' => str_pad(dechex($val[2]), 6, 0, STR_PAD_LEFT),
					'gen_hex' => str_pad(dechex($val[3]), 4, 0, STR_PAD_LEFT), 'dup_id' => $dup_id,
					'parameters' => $val[4], 'atype' => 'alls');

			
			$result[$val[2].".".$val[3].".".$dup_id]['decrypt_part'] = lowOrder($result[$val[2].".".$val[3].".".$dup_id]['obj_hex']).lowOrder($result[$val[2].".".$val[3].".".$dup_id]['gen_hex']);
			if ($result['document']['v'] >= 3) {
				$result[$val[2].".".$val[3].".".$dup_id]['decrypt_part'] .= "73416C54";
			}





			$d = trim($val[11], "\x0A\x0D");
			$result[$val[2].".".$val[3].".".$dup_id]['md5_raw'] = md5(trim($d, "\x0A\x0D"));
			$result[$val[2].".".$val[3].".".$dup_id]['stream'] = trim($d, "\x0A\x0D");

			$t = decryptObj($result['document'], $result[$val[2].".".$val[3].".".$dup_id], $key, $d);




			$result[$val[2].".".$val[3].".".$dup_id]['md5'] = md5(trim($t, "\x0A\x0D"));
			$result[$val[2].".".$val[3].".".$dup_id]['decoded'] = trim($t, "\x0A\x0D");

			$result[$val[2].".".$val[3].".".$dup_id]['text'] = getPDFText($t);


				//handle encrypted strings
				if (isset($result['document']['key']) && $result['document']['key'] != '') {
					
					preg_match_all("/\((.*?)\)(\x0a|\x0d)/s", $result[$val[2].".".$val[3].".".$dup_id]['parameters'], $param1);
					//var_dump($param1);
					for($j = 0; $j< count($param1[1]); $j++) {
						//echo "test=".$param1[1][$j]."=endtest\n";
						$p = unliteral($param1[1][$j]);
						//echo "test1=".$p."=endtest1\n";
						$newParams = decryptObj($result['document'], $result[$val[2].".".$val[3].".".$dup_id], $key, $p);
						
						if ($newParams != '') {
							//echo $newParams;
							$result[$val[2].".".$val[3].".".$dup_id]['parameters'] = $newParams."\n[encrypted params:]".$result[$val[2].".".$val[3].".".$dup_id]['parameters'];
						}
					}


				}


	}


	logDebug("js streams");
	unset($matches0);

	//js streams endobj
	//preg_match_all("/(\x0a|\x0d)(\d{1,4})[^\d]{1,3}(\d{1,2})\sobj((?:(?!\s+\d{1,2}\s+obj).){1,350}?)JS[\s]{0,5}\((.*?)\)(\x0a|\x0d|>>)/si", $data, $matches0, PREG_OFFSET_CAPTURE);
	preg_match_all("/(\x0a|\x0d|\x20)(\d{1,4})[^\d]{1,3}(\d{1,2})\sobj((?:(?!\s+\d{1,2}\s+obj).){1,350}?)(#4a|J)(#53|S)[\s]{0,5}\((.+?)\)endobj/si", $data, $matches0, PREG_OFFSET_CAPTURE);
	if (isset($matches0[0])) {
		$ordered = array();
		for($j = 0; $j< count($matches0[0]); $j++) {
			$ordered[$matches0[2][$j][1]] = array();
			for($i = 1; $i< count($matches0); $i++) {
				$ordered[$matches0[2][$j][1]][$i] = $matches0[$i][$j][0];
			}
		}
	}
	foreach ($ordered as $dup_id => $val) {
	
		//$block_no++;
			$result[$val[2].".".$val[3].".".$dup_id] = array('object' => $val[2], 'generation' => $val[3],
					'obj_hex' => str_pad(dechex($val[2]), 6, 0, STR_PAD_LEFT),
					'gen_hex' => str_pad(dechex($val[3]), 4, 0, STR_PAD_LEFT), 'dup_id' => $dup_id,
					'parameters' => $val[4], 'atype' => 'js');

			
			$result[$val[2].".".$val[3].".".$dup_id]['decrypt_part'] = lowOrder($result[$val[2].".".$val[3].".".$dup_id]['obj_hex']).lowOrder($result[$val[2].".".$val[3].".".$dup_id]['gen_hex']);
			if ($result['document']['v'] >= 3) {
				$result[$val[2].".".$val[3].".".$dup_id]['decrypt_part'] .= "73416C54";
			}

			$d = '';
			preg_match("/(.*)\)$/is", $val[7], $stream);
			if (isset($stream[1]))
				$d = $stream[1];
			else
				$d = $val[7];

			$d = unliteral(trim($d, "\x0A\x0D"));

		$result[$val[2].".".$val[3].".".$dup_id]['md5_raw'] = md5(trim($d, "\x0A\x0D"));
		$result[$val[2].".".$val[3].".".$dup_id]['stream'] = trim($d, "\x0A\x0D");
			$t = decryptObj($result['document'], $result[$val[2].".".$val[3].".".$dup_id], $key, $d);



		$result[$val[2].".".$val[3].".".$dup_id]['md5'] = md5(trim($t, "\x0A\x0D"));
		$result[$val[2].".".$val[3].".".$dup_id]['decoded'] = trim($t, "\x0A\x0D");
				//handle encrypted strings
				if (isset($result['document']['key']) && $result['document']['key'] != '') {
					
					preg_match_all("/\((.*?)\)(\x0a|\x0d)/s", $result[$val[2].".".$val[3].".".$dup_id]['parameters'], $param1);
					//var_dump($param1);
					for($j = 0; $j< count($param1[1]); $j++) {
						//echo "test=".$param1[1][$j]."=endtest\n";
						$p = unliteral($param1[1][$j]);
						//echo "test1=".$p."=endtest1\n";
						$newParams = decryptObj($result['document'], $result[$val[2].".".$val[3].".".$dup_id], $key, $p);
						
						if ($newParams != '') {
							//echo $newParams;
							$result[$val[2].".".$val[3].".".$dup_id]['parameters'] = $newParams."\n[encrypted params:]".$result[$val[2].".".$val[3].".".$dup_id]['parameters'];
						}
					}


				}


	}



	logDebug("js hex streams");
	unset($matches0);

	//js streams
	preg_match_all("/(\x0a|\x0d|\x20)(\d{1,4})[^\d]{1,3}(\d{1,2})\sobj((?:(?!\s+\d{1,2}\s+obj).){1,350}?)(#4a|J)(#53|S)[\s]{0,5}\<(.*?)\>(\x20|\x0a|\x0d|>>|\)\/)/si", $data, $matches0, PREG_OFFSET_CAPTURE);
	if (isset($matches0[0])) {
		$ordered = array();
		for($j = 0; $j< count($matches0[0]); $j++) {
			$ordered[$matches0[2][$j][1]] = array();
			for($i = 1; $i< count($matches0); $i++) {
				$ordered[$matches0[2][$j][1]][$i] = $matches0[$i][$j][0];
			}
		}
	}
	foreach ($ordered as $dup_id => $val) {
		//$block_no++;

			$result[$val[2].".".$val[3].".".$dup_id] = array('object' => $val[2], 'generation' => $val[3],
					'obj_hex' => str_pad(dechex($val[2]), 6, 0, STR_PAD_LEFT),
					'gen_hex' => str_pad(dechex($val[3]), 4, 0, STR_PAD_LEFT), 'dup_id' => $dup_id,
					'parameters' => $val[4], 'atype' => 'js');

			
			$result[$val[2].".".$val[3].".".$dup_id]['decrypt_part'] = lowOrder($result[$val[2].".".$val[3].".".$dup_id]['obj_hex']).lowOrder($result[$val[2].".".$val[3].".".$dup_id]['gen_hex']);
			if ($result['document']['v'] >= 3) {
				$result[$val[2].".".$val[3].".".$dup_id]['decrypt_part'] .= "73416C54";
			}





			$d = trim($val[7], "\x0A\x0D");
			$d = hex2str($d);

		$result[$val[2].".".$val[3].".".$dup_id]['md5_raw'] = md5(trim($d, "\x0A\x0D"));
		$result[$val[2].".".$val[3].".".$dup_id]['stream'] = trim($d, "\x0A\x0D");
			$t = decryptObj($result['document'], $result[$val[2].".".$val[3].".".$dup_id], $key, $d);



		$result[$val[2].".".$val[3].".".$dup_id]['md5'] = md5(trim($t, "\x0A\x0D"));
		$result[$val[2].".".$val[3].".".$dup_id]['decoded'] = trim($t, "\x0A\x0D");

				//handle encrypted strings
				if (isset($result['document']['key']) && $result['document']['key'] != '') {
					
					preg_match_all("/\((.*?)\)(\x0a|\x0d)/s", $result[$val[2].".".$val[3].".".$dup_id]['parameters'], $param1);
					//var_dump($param1);
					for($j = 0; $j< count($param1[1]); $j++) {
						//echo "test=".$param1[1][$j]."=endtest\n";
						$p = unliteral($param1[1][$j]);
						//echo "test1=".$p."=endtest1\n";
						$newParams = decryptObj($result['document'], $result[$val[2].".".$val[3].".".$dup_id], $key, $p);
						
						if ($newParams != '') {
							//echo $newParams;
							$result[$val[2].".".$val[3].".".$dup_id]['parameters'] = $newParams."\n[encrypted params:]".$result[$val[2].".".$val[3].".".$dup_id]['parameters'];
						}
					}


				}

	}






	$duplicateSingle = array();

	logDebug("single filters");
	unset($matches0);
	$ordered = array();
	//single objects s
	//if ($malware['found'] < 2) {
		//echo "Looking for universal blocks with a single encoding method\n";
		//expand out Flate decoded blocks and look for javascript - large blocks
	preg_match_all("/(\d{1,4})[^\d]{1,3}(\d{1,2})\s+obj((?:(?!\s+\d{1,2}\s+obj).){1,350}?)\/(F|#46)(i|#69)(l|#6c)(t|#74)(e|#65)(r|#72).{0,8}?\/(.{1,200}?)(.{0})(.{0})(.{0})(.{0})(.{0})(.{0})(.{0})>>(.*?)(e|#65)(n|#6e)(d|#64)(s|#73|o|#6f)/si", $data, $matches0, PREG_OFFSET_CAPTURE);
		$block_no = 0;
	if (isset($matches0[0])) {
		
		for($j = 0; $j< count($matches0[0]); $j++) {
			$ordered[$matches0[1][$j][1]] = array();
			for($i = 1; $i< count($matches0); $i++) {
				$ordered[$matches0[1][$j][1]][$i] = $matches0[$i][$j][0];
			}
		}
	}
	unset($matches0);
		$block_no = 0;
		preg_match_all("/(\d{1,4})[^\d]{1,3}(\d{1,2})\s+obj((?:(?!\s+\d{1,2}\s+obj).){1,350}?)\/(F|#46)(i|#69)(l|#6c)(t|#74)(e|#65)(r|#72).{0,8}?\/(.{1,200}?)>>(.{0,100}?)(s|#73)(t|#74)(r|#72)(e|#65)(a|#61)(m|#6d)(.*?)(e|#65)(n|#6e)(d|#64)(s|#73|o|#6f)/si", $data, $matches0, PREG_OFFSET_CAPTURE);
	if (isset($matches0[0])) {
		
		for($j = 0; $j< count($matches0[0]); $j++) {
			$ordered[$matches0[1][$j][1]] = array();
			for($i = 1; $i< count($matches0); $i++) {
				$ordered[$matches0[1][$j][1]][$i] = $matches0[$i][$j][0];
			}
		}
	}
	foreach ($ordered as $dup_id => $val) {
			//var_dump($val);
			$block_no++;
			$master_block_encoding = $block_encoding;
			
				//echo "Universal Blocks2\n";
				//echo $val[7]."\n";
			//echo "CAUGHT OBJ ID ".$val[1]."\n";
			//echo "CAUGHT GEN ID ".$val[2]."\n";

			$filter_raw = pdfhex($val[10]);

			$filters = preg_split("/( |\/)/si", trim($filter_raw), -1, PREG_SPLIT_NO_EMPTY);
			//var_dump($val);
			//echo "Filters are\n";
			//var_dump($filters);

			//predictors
			$predictor = '';
			if (preg_match("/\/Predictor ([\d]*)/si", $filter_raw, $matchpre))
				$predictor = $matchpre[1]; 

			$colors = '';
			if (preg_match("/\/Colors ([\d]*)/si", $filter_raw, $matchcol))
				$colors = $matchcol[1]; 

			$bitsPerComponent = '';
			if (preg_match("/\/BitsPerComponent ([\d]*)/si", $filter_raw, $matchbpc))
				$bitsPerComponent = $matchbpc[1]; 
			$columns = '';
			if (preg_match("/\/Columns ([\d]*)/si", $filter_raw, $matchcl))
				$columns = $matchcl[1]; 

			//echo "$predictor, $colors, $bitsPerComponent, $columns";

			$field = 18;
			if (!isset($val[$field]) || $val[$field] == '')
				continue;

			$d = trim($val[$field], "\x0A\x0D");


			$result[$val[1].".".$val[2].".".$dup_id] = array('object' => $val[1], 'generation' => $val[2],
					'obj_hex' => str_pad(dechex($val[1]), 6, 0, STR_PAD_LEFT),
					'gen_hex' => str_pad(dechex($val[2]), 4, 0, STR_PAD_LEFT),'dup_id' => $dup_id,
					'parameters' => $val[3]." ".$val[11]."/Filter /$filter_raw", 'atype' => 'single');
			if (strlen($val[10])-3 > strlen(pdfhex($val[10])) ) {
				//logDebug("Warning: Filter encoding is obfuscated ".$val[10]);
				$obfuscation = 1;
				$result[$val[1].".".$val[2].".".$dup_id]['obfuscation'] = 1;
				$result[$val[1].".".$val[2].".".$dup_id]['obfuscation_raw'] = $val[10];
				$result[$val[1].".".$val[2].".".$dup_id]['obfuscation_decode'] = pdfhex($val[10]);

			}
			
			$result[$val[1].".".$val[2].".".$dup_id]['decrypt_part'] = lowOrder($result[$val[1].".".$val[2].".".$dup_id]['obj_hex']).lowOrder($result[$val[1].".".$val[2].".".$dup_id]['gen_hex']);
			if ($result['document']['v'] >= 3) {
				$result[$val[1].".".$val[2].".".$dup_id]['decrypt_part'] .= "73416C54";
			}

			//$d = trim($val[10], "\x0A\x0D");

			$result[$val[1].".".$val[2].".".$dup_id]['md5_raw'] = md5(trim($d, "\x0A\x0D"));
			$result[$val[1].".".$val[2].".".$dup_id]['stream'] = trim($d, "\x0A\x0D");

			$t = decryptObj($result['document'], $result[$val[1].".".$val[2].".".$dup_id], $key, $d);




			$d = $t;


			//echo "universal single $d\n";
			if ($global_test == 1) {
				echo "Found ".strlen($d)." bytes of encoded data.\n";
			}
			$result[$val[1].".".$val[2].".".$dup_id]['filter'] = '';
			foreach ($filters as $filter) {
				//echo "$filter\n";
				if ($d == '') continue;
				

				if (stripos($filter, 'ASCIIHexDecode') !== FALSE || stripos($filter, 'AHx') !== FALSE  ) {
					//echo "\n\nasciihex\n";
					$d = asciihexdecode($d);
					$master_block_encoding .= '-PA';
					$result[$val[1].".".$val[2].".".$dup_id]['filter'] .= "+ASCIIHexDecode";

				
				} else if (stripos($filter, 'LZWDecode') !== FALSE || stripos($filter, 'LZW') !== FALSE) {
					//echo "\n\nlzw\n";
					$d = lzw_decode($d);
					$result[$val[1].".".$val[2].".".$dup_id]['filter'] .= "+LZWDecode";

					$master_block_encoding .= '-PL';

				} else if (stripos($filter, 'ASCII85Decode') !== FALSE ||stripos($filter, 'A85') !== FALSE  ) {

					//echo "\n\nascii85\n";
					$d = ascii85_decode($d);
					$master_block_encoding .= '-P8';
					$result[$val[1].".".$val[2].".".$dup_id]['filter'] .= "+ASCII85Decode";

				/*} else if (stripos($filter, 'CCITTFaxDecode') !== FALSE || stripos($filter, 'CCF') !== FALSE) {

					//echo "\n\nascii85\n";
					echo "CCITT\n========\n$d\n=======\n";
					//$d = ascii85_decode($d);
					//$result[$val[1].".".$val[2].".".$dup_id]['filter'] .= "+ASCII85Decode";

					$master_block_encoding .= '-CC';
				*/
				} else if (stripos($filter, 'RunLengthDecode') !== FALSE || stripos($filter, 'RL') !== FALSE) {

					//echo "\n\nrun-length\n";
					$d = runlengthdecode($d);
					$master_block_encoding .= '-PR';
					$result[$val[1].".".$val[2].".".$dup_id]['filter'] .= "+RunLengthDecode";


				} else if (stripos($filter, 'FlateDecode') !== FALSE || stripos($filter, 'Fl') !== FALSE ) {
					$master_block_encoding .= '-PF';
					$result[$val[1].".".$val[2].".".$dup_id]['filter'] .= "+FlateDecode";

					//echo "\n\nflate\n";
					$t = $d;
					/*if (strlen($t) > 200000) {
						$d = substr($t,$i,strlen($t)-2);
						echo "Special ".strlen($d)." bytes.\n";
						$d = gzinflate($d, 232960);
						if ($d != '')
							break;

					} */

					for ($i = 0; $i <= 5; $i++) {
						//echo "Try $i flatedecode\n";
						$d = substr($t,$i);
						$d = flatedecode($d);
						if ($d != '')
							break;
					}
					if ($global_test == 1 && $d == '') {
						logDebug("Warning: FlateDecode failed .s");
					}
					


				} else {
					if ($global_test == 1)
						logDebug("Unknown filter $filter");
				}


			}

			//handle predictor
			if ($predictor > 0 && $colors > 0 && $bitsPerComponent >0 && $columns>0) {
				logDebug("Predictor running ".$val[1].".".$val[2].".".$dup_id);
				$d = decodePredictor($d, $predictor, $colors, $bitsPerComponent, $columns);
				//echo $d;
			}



			//logVerbose("decoded single universal: $d");
			$result[$val[1].".".$val[2].".".$dup_id]['decoded'] = $d;
			$result[$val[1].".".$val[2].".".$dup_id]['md5'] = md5($d);

			if ($global_test == 1) {
				echo "Found ".strlen($d)." bytes of decoded data.\n";
			}


			$result[$val[1].".".$val[2].".".$dup_id]['text'] = getPDFText($d);

			//in case there's embedded objects with objects
			/*if (preg_match("/(\x0a|\x0d)(\d{1,4})[^\d]{1,3}(\d{1,2})\sobj((?:(?!\s+\d{1,2}\s+obj).){1,350}?)(s|#73)(t|#74)(r|#72)(e|#65)(a|#61)(m|#6d)(.*?)(e|#65)(n|#6e)(d|#64)(s|#73)/si", $d)) {
				$ret = pdfSlice($d);
				unset($ret['document']);
				$result = array_merge($ret, $result);

			}*/
				//handle encrypted strings
				if (isset($result['document']['key']) && $result['document']['key'] != '') {
					
					preg_match_all("/\((.*?)\)(\x0a|\x0d)/s", $result[$val[1].".".$val[2].".".$dup_id]['parameters'], $param1);
					//var_dump($param1);
					for($j = 0; $j< count($param1[1]); $j++) {
						//echo "test=".$param1[1][$j]."=endtest\n";
						$p = unliteral($param1[1][$j]);
						//echo "test1=".$p."=endtest1\n";
						$newParams = decryptObj($result['document'], $result[$val[1].".".$val[2].".".$dup_id], $key, $p);
						
						if ($newParams != '') {
							//echo $newParams;
							$result[$val[1].".".$val[2].".".$dup_id]['parameters'] = $newParams."\n[encrypted params:]".$result[$val[1].".".$val[2].".".$dup_id]['parameters'];
						}
					}


				}


		}
	//}

	$master_block_encoding = $block_encoding;


	logDebug("multi filters");
	unset($matches0);
	$ordered = array();
	//multiple objects m
	//if ($malware['found'] < 2) {
		//echo "Looking for universal blocks with multiple encoding methods\n";
		//expand out Flate decoded blocks and look for javascript - large blocks
		$flagJS = 0;
		preg_match_all("/(\d{1,4})[^\d]{1,3}(\d{1,2})\s+obj((?:(?! obj).){1,300}?)\/(F|#46)(i|#69)(l|#6c)(t|#74)(e|#65)(r|#72).{0,8}?\[(.{1,200}?)\](.{0,300}?)>>(.{0})(.{0})(.{0})(.{0})(.{0})(.{0})(.*?)(e|#65)(n|#6e)(d|#64)(s|#73|o|#6f)/si", $data, $matches0, PREG_OFFSET_CAPTURE);
		//$block_no = 0;
	if (isset($matches0[0])) {
		
		for($j = 0; $j< count($matches0[0]); $j++) {
			$ordered[$matches0[1][$j][1]] = array();
			for($i = 1; $i< count($matches0); $i++) {
				$ordered[$matches0[1][$j][1]][$i] = $matches0[$i][$j][0];
			}
		}
	}
		

		unset($matches0);

		//var_dump($matches0);
		//$block_no = 0;
	preg_match_all("/(\d{1,4})[^\d]{1,3}(\d{1,2})\s+obj((?:(?! obj).){1,300}?)\/(F|#46)(i|#69)(l|#6c)(t|#74)(e|#65)(r|#72).{0,8}?\[(.{1,200}?)\](.{1,300}?)(s|#73)(t|#74)(r|#72)(e|#65)(a|#61)(m|#6d)(.*?)(e|#65)(n|#6e)(d|#64)(s|#73|o|#6f)/si", $data, $matches0, PREG_OFFSET_CAPTURE);
	if (isset($matches0[0])) {
		for($j = 0; $j< count($matches0[0]); $j++) {
			$ordered[$matches0[1][$j][1]] = array();
			for($i = 1; $i< count($matches0); $i++) {
				$ordered[$matches0[1][$j][1]][$i] = $matches0[$i][$j][0];
			}
		}
	}

	foreach ($ordered as $dup_id => $val) {
			//$block_no++;
			$master_block_encoding = $block_encoding;
			//echo "Universal Blocks\n";
			//echo $val[7]." ".pdfhex($val[7])." ".trim(pdfhex($val[7]))."\n";
			$filter_raw = pdfhex($val[10]);
			$filter_raw2 = pdfhex($val[11]);
			$filters = preg_split("/( |\/)/si", trim($filter_raw), -1, PREG_SPLIT_NO_EMPTY);
			//var_dump($val);
			//echo "Filters are\n";
			//var_dump($filters);
			//predictors
			$predictor = '';
			if (preg_match("/\/Predictor ([\d]*)/si", $filter_raw2, $matchpre))
				$predictor = $matchpre[1]; 

			$colors = '';
			if (preg_match("/\/Colors ([\d]*)/si", $filter_raw2, $matchcol))
				$colors = $matchcol[1]; 

			$bitsPerComponent = '';
			if (preg_match("/\/BitsPerComponent ([\d]*)/si", $filter_raw2, $matchbpc))
				$bitsPerComponent = $matchbpc[1]; 
			$columns = '';
			if (preg_match("/\/Columns ([\d]*)/si", $filter_raw2, $matchcl))
				$columns = $matchcl[1]; 

			//echo "$predictor, $colors, $bitsPerComponent, $columns";



			$field = 18;
			if (!isset($val[$field]) || $val[$field] == '')
				continue;

			$d = trim($val[$field], "\x0A\x0D");
			//echo $d;

			

		
			$result[$val[1].".".$val[2].".".$dup_id] = array('object' => $val[1], 'generation' => $val[2],
					'obj_hex' => str_pad(dechex($val[1]), 6, 0, STR_PAD_LEFT),
					'gen_hex' => str_pad(dechex($val[2]), 4, 0, STR_PAD_LEFT),'dup_id' => $dup_id,
					'parameters' => $val[3]." "."/Filter /$filter_raw ".$val[11], 'atype' => 'multiple');
			if (strlen($val[10])-3 > strlen(pdfhex($val[10])) ) {
				//logDebug("Warning: Filter encoding is obfuscated ".$val[10]);
				$obfuscation = 1;
				$result[$val[1].".".$val[2].".".$dup_id]['obfuscation'] = 1;
				$result[$val[1].".".$val[2].".".$dup_id]['obfuscation_raw'] = $val[10];
				$result[$val[1].".".$val[2].".".$dup_id]['obfuscation_decode'] = pdfhex($val[10]);
			}
			
			
			$result[$val[1].".".$val[2].".".$dup_id]['decrypt_part'] = lowOrder($result[$val[1].".".$val[2].".".$dup_id]['obj_hex']).lowOrder($result[$val[1].".".$val[2].".".$dup_id]['gen_hex']);
			if ($result['document']['v'] >= 3) {
				$result[$val[1].".".$val[2].".".$dup_id]['decrypt_part'] .= "73416C54";
			}


			//$d = trim($val[10], "\x0A\x0D");

			$result[$val[1].".".$val[2].".".$dup_id]['md5_raw'] = md5(trim($d, "\x0A\x0D"));
			$result[$val[1].".".$val[2].".".$dup_id]['stream'] = trim($d, "\x0A\x0D");
			$t = decryptObj($result['document'], $result[$val[1].".".$val[2].".".$dup_id], $key, $d);



			$d = $t;

			$result[$val[1].".".$val[2].".".$dup_id]['filter'] = '';
			foreach ($filters as $filter) {
				//echo "[$filter]"."\n";
				
				if ($d == '') continue;

				if (stripos($filter, 'ASCIIHexDecode') !== FALSE || stripos($filter, 'AHx') !== FALSE) {
					//echo "\n\nasciihex\n";
					$d = asciihexdecode($d);
					$master_block_encoding .= '-PA';
					$result[$val[1].".".$val[2].".".$dup_id]['filter'] .= "+ASCIIHexDecode";


			
				} else if (stripos($filter, 'LZWDecode') !== FALSE || stripos($filter, 'LZW') !== FALSE) {
					//echo "\n\nlzw\n";
					$d = lzw_decode($d);

					$result[$val[1].".".$val[2].".".$dup_id]['filter'] .= "+LZWDecode";
					$master_block_encoding .= '-PL';

				} else if (stripos($filter, 'ASCII85Decode') !== FALSE || stripos($filter, 'A85') !== FALSE) {

					//echo "\n\nascii85\n";
					$d = ascii85_decode($d);
					$result[$val[1].".".$val[2].".".$dup_id]['filter'] .= "+ASCII85Decode";

					$master_block_encoding .= '-P8';
				 
				/*} else if (stripos($filter, 'CCITTFaxDecode') !== FALSE || stripos($filter, 'CCF') !== FALSE) {

					//echo "\n\nascii85\n";
					echo "CCITT\n========\n$d\n=======\n";
					//$d = ascii85_decode($d);
					//$result[$val[1].".".$val[2].".".$dup_id]['filter'] .= "+ASCII85Decode";

					$master_block_encoding .= '-CC';
				*/
				} else if (stripos($filter, 'RunLengthDecode') !== FALSE || stripos($filter, 'RL') !== FALSE) {

					//echo "\n\nrun-length\n";
					$d = runlengthdecode($d);
					$master_block_encoding .= '-PR';
					$result[$val[1].".".$val[2].".".$dup_id]['filter'] .= "+RunLengthDecode";


				} else if (stripos($filter, 'FlateDecode') !== FALSE || stripos($filter, 'Fl') !== FALSE ) {
					//echo "\n\nflateencode\n";
					$master_block_encoding .= '-PF';
					$result[$val[1].".".$val[2].".".$dup_id]['filter'] .= "+FlateDecode";

					//echo "\n\nflate\n";
					$t = $d;

					for ($i = 0; $i <= 5; $i++) {
						//echo "Try $i flatedecode\n";
						$d = substr($t,$i);
						$d = flatedecode($d);
						if ($d != '')
							break;
					}
					if ($global_test == 1 && $d == '') {
					
						logDebug( "Warning: FlateDecode failed .m");
					}



				} else {
					logDebug("Unknown filter $filter");
				}


			}


			//handle predictor
			if ($predictor > 0 && $colors > 0 && $bitsPerComponent >0 && $columns>0) {
				logDebug("Predictor running ".$val[1].".".$val[2].".".$dup_id);
				$d = decodePredictor($d, $predictor, $colors, $bitsPerComponent, $columns);
				//echo $d;
			}



			//logVerbose("decoded universal: $d");
			$result[$val[1].".".$val[2].".".$dup_id]['decoded'] = $d;
			$result[$val[1].".".$val[2].".".$dup_id]['md5'] = md5($d);

			$result[$val[1].".".$val[2].".".$dup_id]['text'] = getPDFText($d);

			//in case there's embedded objects with objects
			/*if (preg_match("/(\x0a|\x0d)(\d{1,4})[^\d]{1,3}(\d{1,2})\sobj((?:(?!\s+\d{1,2}\s+obj).){1,350}?)(s|#73)(t|#74)(r|#72)(e|#65)(a|#61)(m|#6d)(.*?)(e|#65)(n|#6e)(d|#64)(s|#73)/si", $d)) {
				$ret = pdfSlice($d);
				unset($ret['document']);
				$result = array_merge($ret, $result);

			}*/
				//handle encrypted strings
				if (isset($result['document']['key']) && $result['document']['key'] != '') {
					
					preg_match_all("/\((.*?)\)(\x0a|\x0d)/s", $result[$val[1].".".$val[2].".".$dup_id]['parameters'], $param1);
					//var_dump($param1);
					for($j = 0; $j< count($param1[1]); $j++) {
						//echo "test=".$param1[1][$j]."=endtest\n";
						$p = unliteral($param1[1][$j]);
						//echo "test1=".$p."=endtest1\n";
						$newParams = decryptObj($result['document'], $result[$val[1].".".$val[2].".".$dup_id], $key, $p);
						
						if ($newParams != '') {
							//echo $newParams;
							$result[$val[1].".".$val[2].".".$dup_id]['parameters'] = $newParams."\n[encrypted params:]".$result[$val[1].".".$val[2].".".$dup_id]['parameters'];
						}
					}


				}

		}
	//}

	$master_block_encoding = $block_encoding;


	return $result;

}

function decodePredictor($data, $predictor, $colors, $bitsPerComponent,$columns) {
	   
	if ($predictor == 10 ||  //No prediction
		$predictor == 11 ||  //Sub prediction
		$predictor == 12 ||  //Up prediction
		$predictor == 13 ||  //Average prediction
		$predictor == 14 ||  //Paeth prediction
		$predictor == 15	//Optimal prediction
			) {

		$bitsPerSample = $bitsPerComponent*$colors;
		$bytesPerSample = ceil($bitsPerSample/8);
		$bytesPerRow = ceil($bitsPerSample*$columns/8);
		$rows = ceil(strlen($data)/($bytesPerRow + 1));
		$output = '';
		$offset = 0;

		$lastRow = array_fill(0, $bytesPerRow, 0);
		for ($count = 0; $count < $rows; $count++) {
			$lastSample = array_fill(0, $bytesPerSample, 0);
			switch (ord($data[$offset++])) {
			case 0: // None of prediction
				$output .= substr($data, $offset, $bytesPerRow);
				for ($count2 = 0; $count2 < $bytesPerRow  &&  $offset < strlen($data); $count2++) {
					$lastSample[$count2 % $bytesPerSample] = $lastRow[$count2] = ord($data[$offset++]);
				}
				break;

				case 1: // Sub prediction
				for ($count2 = 0; $count2 < $bytesPerRow  &&  $offset < strlen($data); $count2++) {
					$decodedByte = (ord($data[$offset++]) + $lastSample[$count2 % $bytesPerSample]) & 0xFF;
					$lastSample[$count2 % $bytesPerSample] = $lastRow[$count2] = $decodedByte;
					$output .= chr($decodedByte);
				}
				break;

				case 2: // Up prediction
				for ($count2 = 0; $count2 < $bytesPerRow  &&  $offset < strlen($data); $count2++) {
					$decodedByte = (ord($data[$offset++]) + $lastRow[$count2]) & 0xFF;
					$lastSample[$count2 % $bytesPerSample] = $lastRow[$count2] = $decodedByte;
					$output .= chr($decodedByte);
				}
				break;

				case 3: // Average prediction
				for ($count2 = 0; $count2 < $bytesPerRow  &&  $offset < strlen($data); $count2++) {
					$decodedByte = (ord($data[$offset++]) +
								floor(( $lastSample[$count2 % $bytesPerSample] + $lastRow[$count2])/2)
							   ) & 0xFF;
					$lastSample[$count2 % $bytesPerSample] = $lastRow[$count2] = $decodedByte;
					$output .= chr($decodedByte);
				}
				break;

				case 4: // Paeth prediction
				$currentRow = array();
				for ($count2 = 0; $count2 < $bytesPerRow  &&  $offset < strlen($data); $count2++) {
					$decodedByte = (ord($data[$offset++]) +
							paeth($lastSample[$count2 % $bytesPerSample],
										 $lastRow[$count2],
										 ($count2 - $bytesPerSample  <  0)?
										  0 : $lastRow[$count2 - $bytesPerSample])
							   ) & 0xFF;
					$lastSample[$count2 % $bytesPerSample] = $currentRow[$count2] = $decodedByte;
					$output .= chr($decodedByte);
				}
				$lastRow = $currentRow;
				break;

				default:
				die('Unknown prediction tag.');
			}
		}
		return $output;
		}

	  }


function paeth($a, $b, $c) {
	// $a - left, $b - above, $c - upper left
	$p  = $a + $b - $c; // initial estimate
	$pa = abs($p - $a); // distances to a, b, c
	$pb = abs($p - $b);
	$pc = abs($p - $c);

	// return nearest of a,b,c,
	// breaking ties in order a,b,c.
	if ($pa <= $pb && $pa <= $pc) {
		return $a;
	} else if ($pb <= $pc) {
		return $b;
	} else {
		return $c;
	}
}




class LZW{ 
/**
 * Table for storing codes
 *
 * @var array
 * @access protected
 */
	var $code_value = array();
/**
 * Table for storing prefixes to codes
 *
 * @var array
 * @access protected
 */
	var $prefix_code = array();
/**
 * Table for storing individual characters
 *
 * @var array
 * @access protected
 */
	var $append_character = array();
/**
 * Output
 *
 * @var string
 * @access protected
 */
	var $out = "";
/**
 * Total size of table of values
 *
 * @var integer
 * @access protected
 */
	var $TABLE_SIZE = 5021;
/**
 * Number of bits available for encoding
 *
 * @var integer
 * @access protected
 */
	var $output_bit_count = 0;
/**
 * The actual bits for encoding
 *
 * @var string
 * @access protected
 */
	var $output_bit_buffer = "0";
/**
 * Next code in the table
 *
 * @var integer
 * @access protected
 */
	var $next_code = 258;
/**
 * Decoding: the table
 *
 * @var array
 * @access protected
 */
	var $sTable = array();
/**
 * Data to be decoded
 *
 * @var string
 * @access protected
 */
	var $data = NULL;
/**
 * Decoding: next code (same as $next_code)
 *
 * @var integer
 * @access protected
 */
	var $tIdx;
/**
 * bits in next code
 *
 * @var integer
 * @access protected
 */
	var $bitsToGet = 9;
/**
 * Position holder within data string
 *
 * @var string
 * @access protected
 */
	var $bytePointer;
/**
 * Position holder for bits in data string
 *
 * @var string
 * @access protected
 */
	var $bitPointer;
/**
 * Next value to be decoded
 *
 * @var integer
 * @access protected
 */
	var $nextData = 0;
/**
 * Next number of bits to be decoded
 *
 * @var string
 * @access protected
 */
	var $nextBits = 0;
/**
 * Table of max bit values per number of bits
 *
 * @var string
 * @access protected
 */
	var $andTable = array(511, 1023, 2047, 4095);
/**
  * Method: compress
  *      The primary method used by this class, accepts only a string as input and 
  *      returns the string compressed. 
  */
function compress($string){
  $this->output_code(256);
  $this->input = $string;

  $this->next_code=258;              /* Next code is the next available string code*/
  $string_code=ord($this->input{0});    /* Get the first code                         */

  for($i=1;$i<=strlen($this->input);$i++)
  {
	$character=ord($this->input{$i});
    $index=$this->find_match($string_code,$character);/* See if the string is in */
    if (isset($this->code_value[$index]))            /* the table.  If it is,   */
      $string_code=$this->code_value[$index];        /* get the code value.  If */
    else                                    /* the string is not in the*/
    {                                       /* table, try to add it.   */
      if ($this->next_code <= 4094)
      {
		$this->code_value[$index]=$this->next_code;
        $this->prefix_code[$index]=$string_code;
        $this->append_character[$index]=$character;
		$this->next_code++;
      }else{
	     $this->output_code(256);
		 $this->next_code = 258;
		 $this->code_value = array();
         $this->prefix_code = array();
         $this->append_character = array();
		 
		 $this->code_value[$index]=$this->next_code;
         $this->prefix_code[$index]=$string_code;
         $this->append_character[$index]=$character;
		 $this->next_code++;
	  }

      $this->output_code($string_code);  /* When a string is found  */
      $string_code=$character;            /* that is not in the table*/
    }                                   /* I output the last string*/
  }                                     /* after adding the new one*/
  
  $this->output_code(257);
  $this->output_code(0);  //Clean up
  return $this->out;
}
/**
 * Method: find_match - if PHP5 mark as private or protected
 *   Finds the matching index of the character with the table
 * @param string $hash_prefix
 * @param char $hash_character
 * @return int
 */
function find_match($hash_prefix,$hash_character){

  $index = ($hash_character << 4 ) ^ $hash_prefix;
  if ($index == 0)
    $offset = 1;
  else
    $offset = $this->TABLE_SIZE - $index;
    
	while (1){
      if (!isset($this->code_value[$index]))
        return $index;
      if ($this->prefix_code[$index] == $hash_prefix && $this->append_character[$index] == $hash_character)
        return $index;
        $index -= $offset;
      if ($index < 0)
        $index += $this->TABLE_SIZE;
    }
}
/**
 * Method: output_code - if PHP5 mark as private or protected
 *   Adds the input to the output buffer and 
 *     Adds the char code of next 8 bits of the output buffer
 * @param int $code
 */ 
function output_code($code){
	 $len = ($code < 512 ? 9 : ($code < 1024 ? 10 : ($code < 2048 ? 11 : 12)));
	 $this->output_bit_buffer = $this->bitOR($this->lshift(decbin($code),(32 - $len - $this->output_bit_count)),$this->output_bit_buffer);
     $this->output_bit_count += $len;
     while ($this->output_bit_count >= 8){
        $this->out .= chr($this->rshift($this->output_bit_buffer,24));
        $this->output_bit_buffer = $this->lshift($this->output_bit_buffer,8);
        $this->output_bit_count -= 8;
     }
}

      function decode($data) {

        if(ord($data{0}) == 0x00 && ord($data{1}) == 0x01) {
            die("LZW flavour not supported.");
        }

        $this->initsTable();

        $this->data =& $data;

        // Initialize pointers
        $this->bytePointer = 0;
        $this->bitPointer = 0;

        $this->nextData = 0;
        $this->nextBits = 0;

        $oldCode = 0;

        $string = "";
        $uncompData = "";

        while (($code = $this->getNextCode()) != 257) {
			if ($code == 256) {
                $this->initsTable();
                $code = $this->getNextCode();

                if ($code == 257) {
                    break;
                }

                $uncompData .= $this->sTable[$code];
                $oldCode = $code;

            } else {

                if ($code < $this->tIdx) {
                    $string = $this->sTable[$code];
                    $uncompData .= $string;

                    $this->addStringToTable($this->sTable[$oldCode], $string[0]);
                    $oldCode = $code;
                } else {
                    $string = $this->sTable[$oldCode];
                    $string = $string.$string[0];
                    $uncompData .= $string;

                    $this->addStringToTable($string);
                    $oldCode = $code;
                }
            }
        }
        
        return $uncompData;
    }


    /**
     * Initialize the string table. - if PHP5 mark as private or protected
     */
    function initsTable() {
        $this->sTable = array();

        for ($i = 0; $i < 256; $i++){
            $this->sTable[$i] = chr($i);
		}

        $this->tIdx = 258;
        $this->bitsToGet = 9;
    }

    /**
     * Add a new string to the string table. - if PHP5 mark as private or protected
     */
    function addStringToTable ($oldString, $newString="") {
        $string = $oldString.$newString;

        // Add this new String to the table
        $this->sTable[$this->tIdx++] = $string;

        if ($this->tIdx == 511) {
            $this->bitsToGet = 10;
        } else if ($this->tIdx == 1023) {
            $this->bitsToGet = 11;
        } else if ($this->tIdx == 2047) {
            $this->bitsToGet = 12;
        }
    }

    // Returns the next 9, 10, 11 or 12 bits - if PHP5 mark as private or protected
    function getNextCode() {
        if ($this->bytePointer == strlen($this->data)+1)
            return 257;

        $this->nextData = ($this->nextData << 8) | (ord($this->data{$this->bytePointer++}) & 0xff);
        $this->nextBits += 8;

        if ($this->nextBits < $this->bitsToGet) {
            $this->nextData = ($this->nextData << 8) | (ord($this->data{$this->bytePointer++}) & 0xff);
            $this->nextBits += 8;
        }

        $code = ($this->nextData >> ($this->nextBits - $this->bitsToGet)) & $this->andTable[$this->bitsToGet-9];
        $this->nextBits -= $this->bitsToGet;

		return $code;
    }
/**
 * The following methods allow PHP to deal with unsigned longs. 
 * They support the above primary methods. They are not warranted or guaranteed.
*/
/**
 * Method: lshift - if PHP5 mark as private or protected
 *   Used to allow class to deal with unsigned longs, bitwise left shift
 *    Two parameters, number to be shifted, and how much to shift
 * @param binary string $n
 * @param int $b
 * @return binary string
**/
  function lshift($n,$b){ return str_pad($n,($b+strlen($n)),"0");}
/**
 * Method: rshift - if PHP5 mark as private or protected
 *   Used to allow class to deal with unsigned longs, bitwise right shift
 *    Two parameters, number to be shifted, and how much to shift
 * @param binary string $n
 * @param int $b
 * @return int
 */  
  function rshift($n,$b){
   $ret = substr($n,0,(strlen($n) - $b));
   return ((int)bindec($ret));
  }
/**
 * Method: bitOR - if PHP5 mark as private or protected
 *   Used to allow class to deal with unsigned longs, bitwise OR (|)
 *    Bitwise comparison of two parameters, return string representation of not more than 32 bits
 * @param binary string $a
 * @param binary string $b
 * @return binary string
 */ 
  function bitOR($a,$b){
    $long = strlen($a) > strlen($b) ? $a : $b;
	$short = $long == $a ? $b : $a;
	$l = strrev($long);
	$s = strrev($short);
	for($r=0;$r<strlen($l);$r++){
	  $re[$r] = ($s{$r} == "1" || $l{$r} == "1") ? "1" : "0"; 
	}
	$ret = implode("",$re);
	$ret = strrev(substr($ret,0,32));
	return $ret;
  }

}






function javascriptScanEscaped(&$malware, $dec, $stringSearch, $hexSearch, $oloc = 0) {
	global $global_block_encoding;
		$is_js = 0;
		$block_encoding = $global_block_encoding;



		//check for shellcode here
		if ( strlen($dec) > 100) {


			$decAlt = $dec;
			$tiff = 0;
			if (stristr(strhex(substr($dec, 0, 4)),"49492a00")) {
				$decAlt = substr($dec, 4);
				$tiff = 1;
			}
			//logDebug( "checking for PDF string for shellcode\n");
			$shellcode = detectShellcodePlain($decAlt);

			if ($shellcode == 'SHELLCODE DETECTED') {
				logDebug( "found shell code PDF");
				$l = 0;
				if ($tiff == 0) {
					$malware["shellcode $l".uniqid('', TRUE)] = array ('searchtype' => 'shellcodePDF', 'matching' => 'full', 'keylength' =>  0, 'key' => '', 
					'search' => 'shellcode', 'location' => $l, 'top'=>0,  'keycount' => 0, 'keysum' => '',
					'keylocation' => 0, 'keyaccuracy' => 0, 'searcherrors' => 0, 'virustype' => "pdf.shellcode detected",
					'block' => strhex($decAlt),
					'block_is_decoded' => 1, 'block_encoding' => 'hex', 
					'block_size' => strlen($decAlt), 'block_type' => 'shellcode-hex',
					'block_md5' => md5($decAlt), 'block_sha1' => sha1($decAlt), 
					'block_sha256' => hash('sha256', $decAlt),
					'rawlocation' => 0, 'rawblock' => $decAlt,'rawclean' => '');//nasm($decAlt)
				} else {
					$malware["pdfshelltiff".uniqid('', TRUE)] = array ('searchtype' => 'pdfexploit', 'matching' => 'full', 'keylength' =>  0, 'key' => '', 
					'search' => 'pdfshelltiff', 'location' => $l, 'top'=>0,  'keycount' => 0,
					'keylocation' => 0, 'keyaccuracy' => 0, 'searcherrors' => 0, 'virustype' => "pdf.exploit base 64 shellcode in TIFF CVE-2010-0188",
					'block' => strhex($decAlt),
					'block_is_decoded' => 1, 'block_encoding' => 'hex', 
					'block_size' => strlen($decAlt), 'block_type' => 'shellcode-hex',
					'block_md5' => md5($decAlt), 'block_sha1' => sha1($decAlt), 
					'block_sha256' => hash('sha256', $decAlt),
					'rawlocation' => 0, 'rawblock' => $decAlt,'rawclean' => ''); //nasm($decAlt)
				}
				$malware['found'] = 1;
				$malware['shellcode'] = 1;
				$malware['shellcodedump'] = strhex($decAlt);


			}
		}


		//search hex signatures
		//logVerbose("Scan escaped: \n====================\n$dec\n===============================");
		//$hex = strhex($dec);
		foreach($hexSearch as $pattern => $name) {
			if ($l = stripos($dec, hex2str($pattern))) {
				logDebug( "found javascript encoded $name");
				$rawstart = $l - 64;
				if ($rawstart < 0 )
					$rawstart = 0;

				$malware[$pattern.uniqid('', TRUE)] = array ('searchtype' => 'pdfexploit', 'matching' => 'full', 'keylength' =>  0, 'key' => '', 
					'search' => $pattern, 'location' => $l, 'top'=>0,  'keycount' => 0,
					'keylocation' => 0, 'keyaccuracy' => 0, 'searcherrors' => 0, 'virustype' => $name,
					'block' => strhex($dec), 'keysum' => '',
						//'block_is_decoded' => 0,  
					'block_size' => strlen($dec), 'block_type' => 'javascript-shellcode',
					'block_md5' => md5($dec), 'block_sha1' => sha1($dec), 'block_sha256' => hash('sha256', $dec),
					'block_encoding' => $global_block_encoding,
					'rawlocation' => $rawstart, 'rawblock' => substr($dec, $rawstart, 64 * 2 + strlen(hex2str($pattern))),'rawclean' => nasm($dec,$l));

				$malware['found'] = 1;
				$is_js = 1;
				$malware['shellcode'] = $l;
				$malware['shellcodedump'] = strhex($dec);

			}
		}


		//search string signatures
		foreach($stringSearch as $pattern => $name) {
			if (stristr($pattern, '?') || stristr($pattern, "\x28")) {
				preg_match("/$pattern/is", $dec, $matches, PREG_OFFSET_CAPTURE);
				//var_dump($matches);
				if (isset($matches['0']['0']) ) {
					$l = $matches['0']['1'];
					$rawstart = $l - 64;
					if ($rawstart < 0 )
						$rawstart = 0;
					logDebug("found javascript encoded string $name");
					$malware[$pattern.uniqid('', TRUE)] = array ('searchtype' => 'pdfexploit', 'matching' => 'full', 'keylength' =>  0, 'key' => '', 
						'search' => $pattern, 'location' => $l, 'top'=>0,  'keycount' => 0,
						'keylocation' => 0, 'keyaccuracy' => 0, 'searcherrors' => 0, 'virustype' => $name, 'block' => $dec, 'keysum' => '',
						//'block_is_decoded' => 0,  
						'block_size' => strlen($dec), 'block_type' => 'javascript',
						'block_md5' => md5($dec), 'block_sha1' => sha1($dec), 'block_sha256' => hash('sha256', $dec),
						 'block_encoding' => $global_block_encoding,
						'rawlocation' => $rawstart, 'rawblock' => substr($dec, $rawstart, 64 * 2 + strlen($pattern)),'rawclean' => '');
					$malware['found'] = 1;
					$is_js = 1;
				}
			} else if ($l = stripos($dec, $pattern)) {
					logDebug("found javascript encoded2 string $name");
					$rawstart = $l - 64;
					if ($rawstart < 0 )
						$rawstart = 0;
					$malware[$pattern.uniqid('', TRUE)] = array ('searchtype' => 'pdfexploit', 'matching' => 'full', 'keylength' =>  0, 
						'key' => '',  'keysum' => '',
						'search' => $pattern, 'location' => $l, 'top'=>0,  'keycount' => 0,
						'keylocation' => 0, 'keyaccuracy' => 0, 'searcherrors' => 0, 'virustype' => $name, 'block' => $dec, 
						//'block_is_decoded' => 0,  
						'block_size' => strlen($dec), 'block_type' => 'javascript',
						'block_md5' => md5($dec), 'block_sha1' => sha1($dec), 'block_sha256' => hash('sha256', $dec),
						 'block_encoding' => $global_block_encoding,
						'rawlocation' => $rawstart, 'rawblock' => substr($dec, $rawstart, 64 * 2 + strlen($pattern)),'rawclean' => '');
					$malware['found'] = 1;
					$is_js = 1;
			}
		}





		//search quoted strings ""
		preg_match_all("/\"(.{1,9600}?)\"/is", $dec, $matches2, PREG_SET_ORDER);
		foreach ($matches2 as $encoded) {
			$global_block_encoding = $block_encoding;
			//echo "need to decode ".$encoded[1]."\n";

			$strings = reghex2str($encoded[1]);
			$global_block_encoding .= '-RH';
			$strings = jsascii2str($strings);
			$global_block_encoding .= '-JA';
			$strings = unicode_to_shellcode($strings);
			$global_block_encoding .= '-UC';

			if ($strings == 0x0000) {
				//logVerbose("invalid escaped string\n".$encoded[1]."\n");
				if (is_base64($encoded[1]) && strlen($encoded[1]) > 100 ) {
					//logVerbose("string is base 64 encoded, testing for shellcode\n");
					$global_block_encoding = $block_encoding . "-64";
					$strings = base64_decode($encoded[1]);
					if ($strings != "")
						javascriptScanEscaped($malware, $strings, $stringSearch, $hexSearch);
				}
			} else {

				javascriptScanEscaped($malware, $strings, $stringSearch, $hexSearch);
			}

		}
		//search quoted strings ''
		preg_match_all("/'(.{1,9600}?)'/is", $dec, $matches2, PREG_SET_ORDER);
		foreach ($matches2 as $encoded) {
			$global_block_encoding = $block_encoding;
			//echo "need to decode ".$encoded[1]."\n";

			$strings = reghex2str($encoded[1]);
			$global_block_encoding .= '-RH';
			$strings = jsascii2str($strings);
			$global_block_encoding .= '-JA';
			$strings = unicode_to_shellcode($strings);
			$global_block_encoding .= '-UC';

			if ($strings == 0x0000) {
				//logVerbose("invalid escaped string\n".$encoded[1]."\n");
				if (is_base64($encoded[1]) && strlen($encoded[1]) > 100 ) {
					//logVerbose("string is base 64 encoded, testing for shellcode\n");
					$global_block_encoding = $block_encoding . "-64";
					$strings = base64_decode($encoded[1]);
					if ($strings != "")
						javascriptScanEscaped($malware, $strings, $stringSearch, $hexSearch);
				}

			} else {
				javascriptScanEscaped($malware, $strings, $stringSearch, $hexSearch);
			}

			//echo $strings;


		}




		//try char from code decoding
		preg_match_all("/fromCharCode.{0,2}?\((.*?)\)/is", $dec, $matches2, PREG_SET_ORDER);
		foreach ($matches2 as $encoded) {
			$global_block_encoding = $block_encoding;

			//echo "need to decode ".$encoded[1]."\n";
			$strings = code2str($encoded[1]);
			$global_block_encoding .= '-CF';
			//echo "fixed\n$strings\n";
			javascriptScanEscaped($malware, $strings, $stringSearch, $hexSearch);
		}

		$global_block_encoding = $block_encoding;


	return $malware;
}

function is_base64($data) {
	if (preg_match( '/^[A-Za-z=\/\+]+$/s', trim($data)) );
		return 1;
	return 0;
	}



function javascriptScan(&$malware, $dec, $stringSearch, $hexSearch) {
		global $global_block_encoding;
		$block_encoding = $global_block_encoding;

		//logVerbose("Scan javascript: \n$dec");


		$stringsFixed = reghex2str($dec);
		//logVerbose("DECODED HEX STR");
		//logVerbose($stringsFixed);

		if ($dec != $stringsFixed)
			$global_block_encoding .= '-RH';

		foreach($stringSearch as $pattern => $name) {
			//if ($l = stripos($stringsFixed, $pattern)) {
			if (stristr($pattern, '?') || strstr($pattern, "\x28")) {
				preg_match("/$pattern/is", $stringsFixed, $matches, PREG_OFFSET_CAPTURE);
				//var_dump($matches);
				if (isset($matches['0']['0']) ) {
					$l = $matches['0']['1'];
					$rawstart = $l - 64;
					if ($rawstart < 0 )
						$rawstart = 0;
					logDebug("found javascript string $name");
					$malware[$pattern.uniqid('', TRUE)] = array ('searchtype' => 'pdfexploit', 'matching' => 'full', 'keylength' =>  0, 'key' => '', 
						'search' => $pattern, 'location' => $l, 'top'=>0,  'keycount' => 0, 'keysum' => '',
						'keylocation' => 0, 'keyaccuracy' => 0, 'searcherrors' => 0, 'virustype' => $name,
						'block' => $stringsFixed, 'block_is_decoded' => 1, 'block_encoding' => 'reghex',
						'block_size' => strlen($stringsFixed), 'block_type' => 'javascript',
						'block_md5' => md5($stringsFixed), 'block_sha1' => sha1($stringsFixed),
						'block_sha256' => hash('sha256', $stringsFixed),
						'block_encoding' => $global_block_encoding,
						'rawlocation' => $rawstart, 'rawblock' => substr($stringsFixed, $rawstart, 64 * 2 + strlen($pattern)),'rawclean' => '');
					$malware['found'] = 1;
				}
			} else if ($l = stripos($stringsFixed, $pattern)) {
					logDebug("found javascript string $name");
					$rawstart = $l - 64;
					if ($rawstart < 0 )
						$rawstart = 0;
					$malware[$pattern.uniqid('', TRUE)] = array ('searchtype' => 'pdfexploit', 'matching' => 'full', 'keylength' =>  0, 
						'key' => '',  'keysum' => '',
						'search' => $pattern, 'location' => $l, 'top'=>0,  'keycount' => 0,
						'keylocation' => 0, 'keyaccuracy' => 0, 'searcherrors' => 0, 'virustype' => $name,
						'block' => $stringsFixed, 'block_is_decoded' => 1, 'block_encoding' => 'reghex', 
						'block_size' => strlen($stringsFixed), 'block_type' => 'javascript',
						'block_md5' => md5($stringsFixed), 'block_sha1' => sha1($stringsFixed), 
						'block_sha256' => hash('sha256', $stringsFixed),
						'block_encoding' => $global_block_encoding,
						'rawlocation' => $rawstart, 'rawblock' => substr($stringsFixed, $rawstart, 64 * 2 + strlen($pattern)),'rawclean' => '');
					$malware['found'] = 1;

			}
		}

		preg_match_all("/\"(.{1,32000}?)\"/is", $stringsFixed, $matches2, PREG_SET_ORDER);
		foreach ($matches2 as $encoded) {
			$global_block_encoding = $block_encoding;
			if ($dec != $stringsFixed)
				$global_block_encoding .= '-RH';
			$global_block_encoding .= '-ES';
			//echo "need to decode ".$encoded[1]."\n";
			//$strings = reghex2str($encoded[1]);
			$strings = jsascii2str($encoded[1]);
			//logVerbose("1: ===$strings===\n");
			$global_block_encoding .= '-JA';
			$strings = unicode_to_shellcode($strings);
			//logVerbose("2: ===$strings===\n");
			$global_block_encoding .= '-UC';
			if ($strings == 0x0000) {
				//logVerbose("invalid escaped string\n".$encoded[1]."\n");
				
				if (is_base64($encoded[1]) && strlen($encoded[1]) > 100 ) {
					//logVerbose("string is base 64 encoded, testing for shellcode\n");

					$global_block_encoding = $block_encoding . "-64";
					$strings = base64_decode($encoded[1]);
					if ($strings != "")
						javascriptScanEscaped($malware, $strings, $stringSearch, $hexSearch);
				}
			} else {
				//echo "fixed\n".$encoded[1]."\n===".strhex($strings)."===\n";
				javascriptScanEscaped($malware, $strings, $stringSearch, $hexSearch);

			}


		}

		preg_match_all("/'(.{1,32000}?)'/is", $stringsFixed, $matches2, PREG_SET_ORDER);

		foreach ($matches2 as $encoded) {
			$global_block_encoding = $block_encoding;
			if ($dec != $stringsFixed)
				$global_block_encoding .= '-RH';
			$global_block_encoding .= '-ES';
			//echo "need to decode ".$encoded[1]."\n";
			//$strings = reghex2str($encoded[1]);
			$strings = jsascii2str($encoded[1]);
			$global_block_encoding .= '-JA';
			$strings = unicode_to_shellcode($strings);
			$global_block_encoding .= '-UC';
			if ($strings == 0x0000) {
				//logVerbose("invalid escaped string\n".$encoded[1]."\n");
				if (is_base64($encoded[1]) && strlen($encoded[1]) > 100 ) {
					//logVerbose("string is base 64 encoded, testing for shellcode\n");
					$global_block_encoding = $block_encoding . "-64";
					$strings = base64_decode($encoded[1]);
					if ($strings != "")
						javascriptScanEscaped($malware, $strings, $stringSearch, $hexSearch);
				}
			} else {
				//echo "$strings\n";
				javascriptScanEscaped($malware, $strings, $stringSearch, $hexSearch);
			}

		}

		//CVE-2010-0188
		preg_match_all("/\>(.*?)\</is", $stringsFixed, $matches2, PREG_SET_ORDER);
		foreach ($matches2 as $encoded) {
			$global_block_encoding = $block_encoding;
			//if ($dec != $stringsFixed)
			//	$global_block_encoding .= '-RH';
			//$global_block_encoding .= '-ES';
			//echo "need to decode ".$encoded[1]."\n";
			//$strings = reghex2str($encoded[1]);
			//$strings = jsascii2str($encoded[1]);
			//$global_block_encoding .= '-JA';
			//$strings = unicode_to_shellcode($strings);
			//$global_block_encoding .= '-UC';
			//if ($strings == 0x0000) {
				//logVerbose("trying to process XFA block\n".$encoded[1]."\n");
				if (is_base64($encoded[1]) && strlen($encoded[1]) > 100 ) {
					//logVerbose("string is base 64 encoded, testing for shellcode\n");
					$global_block_encoding = $block_encoding . "-64";
					$strings = base64_decode($encoded[1]);
					if ($strings != "")
						javascriptScanEscaped($malware, $strings, $stringSearch, $hexSearch);
				}
			//} else {
			//	//echo "$strings\n";
			//	javascriptScanEscaped(&$malware, $strings, $stringSearch, $hexSearch);
			//}

		}


		preg_match_all("/fromCharCode.{0,2}?\((.*?)\)/is", $stringsFixed, $matches2, PREG_SET_ORDER);
		foreach ($matches2 as $encoded) {
			$global_block_encoding = $block_encoding;
			if ($dec != $stringsFixed)
				$global_block_encoding .= '-RH';
			//echo "need to decode ".$encoded[1]."\n";
			$strings = code2str($encoded[1]);
			$global_block_encoding .= '-CF';

			//echo "fixed\n$strings\n";
			javascriptScanEscaped($malware, $strings, $stringSearch, $hexSearch);
		}


		$global_block_encoding = $block_encoding;
	return $malware;
}


function findHiddenJS($string) {
	if (ctype_print($string) ) {
		$newstring = '';
		$tmp = '';
		$data = '';

		for($i = 0; $i < strlen($string) ; $i++) {
			if (ctype_xdigit($string[$i])) {
				$tmp .= $string[$i];
			} else {
				
				if (strlen($tmp) == 4) {
					$data .= chr(hexdec ($tmp[2].$tmp[3])).chr(hexdec($tmp[0].$tmp[1]));
					$tmp = '';
				} else {
					$ascii = base_convert ($tmp,16,10);
      					$data .= chr ($ascii);
					$tmp = '';
				}
			}
		}
		return $data;
	}
	return $string;
}



function cleanHex($string) {
	$tmp = '';
	for($i = 0; $i < strlen($string) ; $i++) {
		if (ctype_xdigit($string[$i])) {
			$tmp .= $string[$i];
		} 
	}
	return $tmp;
}




function reghex2str($hex)
{
	$str = '';
	for ($i = 0; $i < strlen($hex); $i++) {
		if ($i+3 <= strlen($hex) && $hex[$i] == '\\' && $hex[$i+1] == 'x' && ctype_alnum($hex[$i+2]) &&  ctype_alnum($hex[$i+3])) {
			$n = $hex[$i+2].$hex[$i+3];
			$str .= chr(hexdec($n));
			$i+=3;
 		} else {
			$str .= $hex[$i];
		}

	}
	

  return $str;
}

function code2str($hex)
{
	//echo "before $hex\n";
	$str = '';
	for ($i = 0; $i < strlen($hex); $i++) {
		if ($i+2 <= strlen($hex) && ctype_alnum($hex[$i]) && ctype_alnum($hex[$i+1]) && ctype_alnum($hex[$i+2]) && ctype_alnum($hex[$i+3])) {
			//$n = $hex[$i].$hex[$i+1].$hex[$i+2];
			$str .= chr($hex[$i+2].$hex[$i+3]).chr($hex[$i].$hex[$i+1]);
			$i+=3;
		} else if ($i+2 <= strlen($hex) && ctype_alnum($hex[$i]) && ctype_alnum($hex[$i+1]) && ctype_alnum($hex[$i+2])) {
			$n = $hex[$i].$hex[$i+1].$hex[$i+2];
			$str .= chr($n);
			$i+=2;
 		} else if ($i+2 <= strlen($hex) && ctype_alnum($hex[$i]) && ctype_alnum($hex[$i+1])) {
			$n = $hex[$i].$hex[$i+1];
			$str .= chr($n);
			$i+=1;
 		} else if ($i+1 <= strlen($hex) && ctype_alnum($hex[$i]) ) {
			$n = $hex[$i];
			$str .= chr($n);
			//$i+=1;
		} else {
			//$str .= $hex[$i];
		}

	}
	//echo "after $str\n";

  return $str;
}


function jsascii2str($hex)
{
	//echo "before $hex\n";
	$str = '';
	for ($i = 0; $i < strlen($hex); $i++) {
		if ($i+3 <= strlen($hex) && $hex[$i] == '\\' && ctype_alnum($hex[$i+1]) && ctype_alnum($hex[$i+2]) &&  ctype_alnum($hex[$i+3])) {
			$n = $hex[$i+1].$hex[$i+2].$hex[$i+3];
			$str .= chr($n);
			$i+=3;
 		} else if ($i+3 <= strlen($hex) && $hex[$i] == '\\' && ctype_alnum($hex[$i+1]) && ctype_alnum($hex[$i+2]) ) {
			$n = $hex[$i+1].$hex[$i+2];
			$str .= chr($n);
			$i+=2;
 		} else if ($i+2 <= strlen($hex) && $hex[$i] == '\\' && ctype_alnum($hex[$i+1]) ) {
			$n = $hex[$i+1];
			$str .= chr($n);
			$i+=1;
		} else {
			$str .= $hex[$i];
		}

	}
	//echo "after $str\n";

  return $str;
}



function unicode_to_shellcode($url)
{
  //split the URL into an array
	//echo $url."\n";
  $url=str_replace('\u', '%u', $url);
 	//echo $url."\n";
  $url_array = explode ("%", $url);
  //Make sure we have an array
  if (is_array($url_array))
  {
    //Loop while the key/value pair of the array
    //match our list items
	$ret = '';
    while (list ($k,$v) = each ($url_array))
    {
	if (stristr($v, 'u') || stristr($v, 'z')) {
		$unicode = trim($v, 'uUzZ');
		//$ascii = utf8_decode($unicode);
		if (isset ($unicode[3])) {
     			$ascii = chr(hexdec ($unicode[2].$unicode[3])).chr(hexdec($unicode[0].$unicode[1]));
			//echo "try to convert $unicode to ".$ascii." ".$unicode[0].$unicode[1]." ".$unicode[2].$unicode[3]."\n";
			$ret .= $ascii;
		}
	} else if (strlen($v) == 2) {

	       //use base_convert to convert each character
      		$ascii = base_convert ($v,16,10);
      		$ret .= chr ($ascii);
		//$ret .= $v;
	} else {

	       //use base_convert to convert each character
      		//$ascii = base_convert ($v,16,10);
      		//$ret .= chr ($ascii);
		$ret .= $v;
	}
    }
 }
 //return the decoded URL
 return ("$ret");
}


function hex2str($hex)
{
	$str = '';
  for($i=0;$i<strlen($hex);$i+=2)
  {
    $str.=chr(hexdec(substr($hex,$i,2)));
  }
  return $str;
}

function decode_replace($string)
{
	return preg_replace("/([A-Z])/" ,'%', $string);
}

function checkBlockHash($md5) {
	global $PDFblockHash;
	$malware = array('found' => 0);

	$malware['found'] = 0;
	//$md5 = md5(trim($data, "\x0A\x0D"));
	//echo "block hash is $md5 ".strlen(trim($data, "\x0A\x0D"))."\n";
	//echo "TEST ".substr(trim($data, "\x0A\x0D"), 0, 16)."\n";
	//echo "bottom ".strhex(substr(trim($data, "\x0A\x0D"), -16))."\n";

	if (isset($PDFblockHash[$md5]) ) {

		logDebug("Found ".$PDFblockHash[$md5]);

		$malware[$md5.uniqid('', TRUE)] = array ('searchtype' => 'block', 'matching' => 'full', 'keylength' => 0, 'key' => 0, 
					'search' => $md5, 'location' => 0, 'top'=>0,  'keycount' => 0,
					'keylocation' => 0, 'keyaccuracy' => 0, 'searcherrors' => 0, 'virustype' => $PDFblockHash[$md5],
					'rawlocation' => 0, 'rawblock' => '', 'block' => '', 'block_type' => '',
					'rawclean' => '',
					'keysum' => '');
		$malware['found'] = 1;
	}
	return $malware;
}



function detectShellcodePlain($data) {
	global $global_libemu, $malwaredir;

	if (isset($global_libemu) && is_executable($global_libemu)) {

		$filename = $malwaredir."shell_".uniqid();
		$fp = fopen($filename, "w");
		fwrite($fp, $data);
		fclose($fp);
		
		$shellcode_scan = exec("$global_libemu ".escapeshellarg($filename));
		unlink($filename);

		if (strstr($shellcode_scan, 'SHELLCODE DETECTED')) {
			return "SHELLCODE DETECTED";
		}
	

	}
	return "not found";
}


function nasm($data, $loc = 0) {
	global $malwaredir, $global_nasm;
	$output = '';

	if (isset($global_nasm) && is_executable($global_nasm)) {
		$filename = $malwaredir."shell_".uniqid();
		$fp = fopen($filename, "w");
		fwrite($fp, $data);
		fclose($fp);
		//echo "exec "."$global_nasm -o ".escapeshellarg($loc)." -u $filename\n";

		exec("$global_nasm -o ".escapeshellarg($loc)." -u $filename", $output0);
		$output = implode("\n", $output0);
		//echo $output;
	}

	return $output;
}


function detectShellcode($data) {
	global $global_libemu, $malwaredir, $global_shell;
	$malware['found'] = 0;

	preg_match_all("/\x90\x90\x90(.{1024})/s", $data, $matches2, PREG_OFFSET_CAPTURE);
	foreach ($matches2 as $match => $shellcode) {
		if (!isset($shellcode[0][1])) break;
		$filename = $malwaredir."shell_".uniqid();
		$fp = fopen($filename, "w");
		fwrite($fp, $shellcode[0][0]);
		fclose($fp);
		$l = $shellcode[0][1];
	
		//echo "shellcode\n";
		//var_dump($shellcode);
		//echo "match\n";
		//var_dump($match);

		logDebug( "scan for shell code nop at $l ".count($shellcode[0][0]));
		if (isset($global_libemu) && is_executable($global_libemu)) {
			$shellcode_scan = exec("$global_libemu ".escapeshellarg($filename));
		} else
			$shellcode_scan = '';
		unlink($filename);
		$altcheck = detectShellcodeHashes(strhex($shellcode[0][0]));

		if (strstr($shellcode_scan, 'SHELLCODE DETECTED')  || $altcheck['hits'] > 2) {
			logDebug( "found shell code A at $l");
			$malware["shellcode $l".uniqid('', TRUE)] = array ('searchtype' => 'shellcodeexec', 'matching' => 'full', 'keylength' =>  0, 'key' => '', 
				'search' => 'shellcode', 'location' => $l, 'top'=>0,  'keycount' => 0, 'keysum' => '',
				'keylocation' => 0, 'keyaccuracy' => 0, 'searcherrors' => 0, 'virustype' => "Shellcode detected at $l",
				'block' => strhex($shellcode[0][0]),
				'block_is_decoded' => 1, 'block_encoding' => 'hex', 
				'block_size' => strlen($shellcode[0][0]), 'block_type' => 'shellcode-hex',
				'block_md5' => md5($shellcode[0][0]), 'block_sha1' => sha1($shellcode[0][0]), 
				'block_sha256' => hash('sha256', $shellcode[0][0]),
				'block_ssdeep' => ssdeep($shellcode[0][0]),
				'rawlocation' => '', 'rawblock' => '','rawclean' => nasm($shellcode[0][0], $l));
			$malware['found'] = 1;
			$malware['shellcode'] = $l;
			$malware['shellcodedump'] = strhex($shellcode[0][0]);
			//return $malware;
		}
	}




	preg_match_all("/\x4F\x72\x69\x65\x6E\x74\x61\x74\x69\x6F\x6E.\x50\x4F(.{1024})/is", $data, $matches2, PREG_OFFSET_CAPTURE);
//var_dump($matches2);
//echo "try to get shellcode 0001\n";
	if (isset($matches2[1]))
	foreach ($matches2[1] as $match => $shellcode) {
		if (!isset($shellcode[1])) break;
		$filename = $malwaredir."shell_".uniqid();
		$fp = fopen($filename, "w");
		fwrite($fp, $shellcode[0]);
		fclose($fp);
		$l = $shellcode[1];
	
		//echo "shellcode\n";
		//var_dump($shellcode);
		//echo "match\n";
		//var_dump($match);

		logDebug( "scan for shell code block CVE-2009-3129 at $l ".count($shellcode[0]));
		if (isset($global_libemu) && is_executable($global_libemu)) {

			$shellcode_scan = exec("$global_libemu ".escapeshellarg($filename));
		} else
			$shellcode_scan = '';
		unlink($filename);
		$altcheck = detectShellcodeHashes(strhex($shellcode[0]));

		if (strstr($shellcode_scan, 'SHELLCODE DETECTED') || $altcheck['hits'] > 2) {
			logDebug( "found shell code HP at $l");
			$malware["shellcode $l".uniqid('', TRUE)] = array ('searchtype' => 'shellcodeexec', 'matching' => 'full', 'keylength' =>  0, 'key' => '', 
				'search' => 'shellcode', 'location' => $l, 'top'=>0,  'keycount' => 0, 'keysum' => '',
				'keylocation' => 0, 'keyaccuracy' => 0, 'searcherrors' => 0, 'virustype' => "Shellcode detected at $l CVE-2009-3129",
				'block' => strhex($shellcode[0]),
				'block_is_decoded' => 1, 'block_encoding' => 'hex', 
				'block_size' => strlen($shellcode[0]), 'block_type' => 'shellcode-hex',
				'block_md5' => md5($shellcode[0]), 'block_sha1' => sha1($shellcode[0]), 
				'block_sha256' => hash('sha256', $shellcode[0]),
				'block_ssdeep' => ssdeep($shellcode[0]),
				'rawlocation' => '', 'rawblock' => '','rawclean' => nasm($shellcode[0], $l));
			$malware['found'] = 1;
			$malware['shellcode'] = $l;
			$malware['shellcodedump'] = strhex($shellcode[0]);
			//return $malware;
		}
	}


	if ($malware['found'] == 0 && $global_shell == 1)
		$malware = detectShellcodeLong($data);


	if ($malware['found'] == 0) {

	$tmp = '';
	for($i = 0; $i < strlen($data)-4; $i++) {
		if ($data[$i] == "\x00" && $data[$i+1] == "\x00" && $data[$i+2] == "\x00" && $data[$i+3] == "\x00" && strlen($tmp) < 120) {
			$tmp = '';
		} else if (($data[$i] == "\x00" && $data[$i+1] == "\x00" && $data[$i+2] == "\x00" && $data[$i+3] == "\x00")) {
			if (strlen($tmp) >= 2048) {
				logDebug( "shell code too big at $i");


			} else {

				$tmp = substr($tmp, 3);
				//$i+= 3;
				$filename = $malwaredir."shell_".uniqid();
				$fp = fopen($filename, "w");
				fwrite($fp,$tmp);
				fclose($fp);
			
				$l = $i-strlen($tmp);
		
			

				logDebug( "scan for shell code block at $l ".strlen($tmp));
				//echo "$global_libemu ".escapeshellarg($filename)."\n";
				if (isset($global_libemu) && is_executable($global_libemu)) {

					$shellcode_scan = exec("$global_libemu ".escapeshellarg($filename));
				} else
					$shellcode_scan = '';
				unlink($filename);
				$altcheck = detectShellcodeHashes(strhex($tmp));

				if (strstr($shellcode_scan, 'SHELLCODE DETECTED')  || $altcheck['hits'] > 2) {
					//echo strhex($tmp)."\n";
					logDebug( "found shell code B @"."$l ".strlen($tmp)." bytes");
					$malware["shellcode $l".uniqid('', TRUE)] = array ('searchtype' => 'shellcodeexec', 'matching' => 'full', 'keylength' =>  0, 'key' => '', 
					'search' => 'shellcode', 'location' => $l, 'top'=>0,  'keycount' => 0,
					'keylocation' => 0, 'keyaccuracy' => 0, 'searcherrors' => 0, 'keysum' => '',
					'virustype' => "Shellcode detected at $l  ".strlen($tmp)." bytes",
					'block' => strhex($tmp),
					'block_is_decoded' => 1, 'block_encoding' => 'hex', 
					'block_size' => strlen($tmp), 'block_type' => 'shellcode-hex',
					'block_md5' => md5($tmp), 'block_sha1' => sha1($tmp), 'block_sha256' => hash('sha256', $tmp),
					'block_ssdeep' => ssdeep($tmp),
					'rawlocation' => '', 'rawblock' => '','rawclean' => nasm($tmp, $l));
					$malware['found'] = 1;
					$malware['shellcode'] = $l;
					$malware['shellcodedump'] = strhex($tmp);
					return $malware;
				} 
			}
	
			$tmp = '';

		} else {
			$tmp .= $data[$i];
		}

	}}



	return $malware;
}



function detectShellcodeLong($data) {
	global $global_libemu, $malwaredir, $global_shell_block;
	$malware['found'] = 0;
	logDebug( "scan for shell code at every byte");
	$id = uniqid();


	for($i = 0; $i < strlen($data)-$global_shell_block; $i++) {
			$tmp = substr($data, $i, $global_shell_block);
			$filename = $malwaredir."shell_".$id."_".uniqid();
			$fp = fopen($filename, "w");
			fwrite($fp,$tmp);
			fclose($fp);
			
			$l = $i;
		
			

			//logDebug( "scan for shell code at $l ".strlen($tmp));
			//echo "$global_libemu ".escapeshellarg($filename)."\n";
			if (isset($global_libemu) && is_executable($global_libemu)) {

				$shellcode_scan = exec("$global_libemu ".escapeshellarg($filename));
			} else
				$shellcode_scan = '';
			unlink($filename);

			if (strstr($shellcode_scan, 'SHELLCODE DETECTED')) {
				//echo strhex($tmp)."\n";
				logDebug( "found shell code C @"."$l ".strlen($tmp)." bytes");
				$malware["shellcode $l".uniqid('', TRUE)] = array ('searchtype' => 'shellcodeexec', 'matching' => 'full', 'keylength' =>  0, 'key' => '', 
				'search' => 'shellcode', 'location' => $l, 'top'=>0,  'keycount' => 0, 'keysum' => '',
				'keylocation' => 0, 'keyaccuracy' => 0, 'searcherrors' => 0,
				'virustype' => "Shellcode detected at $l  ".strlen($tmp)." bytes",
				'block' => strhex($tmp),
				'block_is_decoded' => 1, 'block_encoding' => 'hex', 
				'block_size' => strlen($tmp), 'block_type' => 'shellcode-hex',
				'block_md5' => md5($tmp), 'block_sha1' => sha1($tmp), 'block_sha256' => hash('sha256', $tmp),
				'block_ssdeep' => ssdeep($tmp),
				'rawlocation' => '', 'rawblock' => '','rawclean' => nasm($tmp, $l));
				$malware['found'] = 1;
				$malware['shellcode'] = $l;
				$malware['shellcodedump'] = strhex($tmp);

				$i += $global_shell_block-1;
				//return $malware;
			}

	}

	return $malware;
}


function dec_to_hex($dec)
{
    $sign = ""; // suppress errors
    if( $dec < 0){ $sign = "-"; $dec = abs($dec); }

    $hex = Array( 0 => 0, 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5,
                  6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 'a',
                  11 => 'b', 12 => 'c', 13 => 'd', 14 => 'e',   
                  15 => 'f' );
       
    do
    {
        $h = $hex[($dec%16)] . $h;
        $dec /= 16;
    }
    while( $dec >= 1 );
   
    return $sign . $h;
} 

function getPDFText($data) {
	$result = '';
	if (preg_match_all ('/\(([^\)]+)\)/', $data, $matches))
		$result .= join ('', $matches[1]); 
	return unliteral($result); //return what was found
}

?>
