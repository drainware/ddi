<?

class InfoModel extends Model
{


  public function __construct(){
 
  }

  public function getDiskUsage() {

    exec("df -h | awk '{ print $5 }' | head -n 2 | tail -n 1 | sed 's/%//g'", $out, $error);
    return (int) $out;

  }


  public function getAdminNConnections() {

     exec('netstat -pnta | grep ":80" | wc -l', $out, $error);
     return (int) $out;

  }

  public function getWebFilterConnections() {

     exec('netstat -pnta | grep ":8080"| grep "ESTABLISHED" | wc -l', $out, $error);
     return (int) $out;

  }

  public function getCpuUsage() {
/*
     $pattern = "/Cpu\(s\): (.+)%us/";
     exec('/usr/bin/top -n 1 -b 2>&1', $out, $error );
     preg_match($pattern,$out[2],$matches);
     return (float) $matches[1];
*/
$stat1 = file('/proc/stat');
sleep(1);
$stat2 = file('/proc/stat');
$info1 = explode(" ", preg_replace("!cpu +!", "", $stat1[0]));
$info2 = explode(" ", preg_replace("!cpu +!", "", $stat2[0]));
$dif = array();
$dif['user'] = $info2[0] - $info1[0];
$dif['nice'] = $info2[1] - $info1[1];
$dif['sys'] = $info2[2] - $info1[2];
$dif['idle'] = $info2[3] - $info1[3];
$total = array_sum($dif);
$cpu = array();
foreach($dif as $x=>$y) {
  $cpu[$x] = round($y / $total * 100, 1);
}

return $cpu['user'];
     
 
  }
 
  public function getMemUsage() {

    $total_mem  = (int)  exec("free -m |tail -n +2 | head -n 1 | awk '{ print $2 }'");
    $used_mem  = (int) exec("free -m |tail -n +2 | head -n 1 | awk '{ print $3 }'");
    $percent  = (int) round(($used_mem*100)/$total_mem);
    $mem = Array($total_mem,$used_mem,$percent);

    return $mem;
    
  }

  public function getSwapUsage() {

    $used_swap  = exec("cat /proc/swaps | tail -n 1 | awk '{ print $4 }'");
    $total_swap = exec("cat /proc/swaps | tail -n 1 | awk '{ print $3 }'");
    $swap = Array();
    $swap[] = $total_swap;
    $swap[] = $used_swap;
    $swap[] = round(($used_swap*100)/$total_swap);
    return $swap;
  }

}
?>
