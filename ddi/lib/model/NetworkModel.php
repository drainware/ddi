<?php
class NetworkModel {


    public function __construct($type = null){

    }

    public function SaveConf($config) {
    $interfaces_file = "/etc/network/interfaces";
    $template_resolv_1 = 'nameserver [IP]
nameserver [DNS1]'.PHP_EOL;

    $template_resolv_2 = 'nameserver [IP]
nameserver [DNS1]
nameserver [DNS2]'.PHP_EOL;

        $template_interfaces = 'auto lo
iface lo inet loopback

auto eth0
iface eth0 inet static
	address [IP]
	netmask [NETMASK]
	gateway [GATEWAY]'.PHP_EOL. "#end ip-config";
    
    /*$template_interfaces = 'auto lo
iface lo inet loopback

auto eth0
iface eth0 inet manual
        up ifconfig $IFACE 0.0.0.0 up
        up ip link set $IFACE promisc on

auto eth1
iface eth1 inet manual
        up ifconfig $IFACE 0.0.0.0 up
        up ip link set $IFACE promisc on

auto br0
iface br0 inet static
        bridge_ports eth0 eth1
	address [IP]
	netmask [NETMASK]
	gateway [GATEWAY]'.PHP_EOL. "#end ip-config";*/

$template_dhcp = PHP_EOL .'auto lo
iface lo inet loopback

auto eth0
iface eth0 inet manual
        up ifconfig $IFACE 0.0.0.0 up
        up ip link set $IFACE promisc on

auto eth1
iface eth1 inet manual
        up ifconfig $IFACE 0.0.0.0 up
        up ip link set $IFACE promisc on

auto br0
iface br0 inet dhcp
        bridge_ports eth0 eth1'.PHP_EOL . "#end ip-config";

        $handle = @fopen($interfaces_file, "r");
        $contents = fread($handle, filesize($interfaces_file));
        fclose($handle);


        $pattern = "/#end ip-config(.*)/s";
        preg_match_all($pattern,$contents,$matches);
        $routes = $matches[1][0];


	if ($config['dhcp']) {

        $fhandle = fopen($interfaces_file,"w");
        fwrite($fhandle,$template_dhcp . PHP_EOL . $routes);
        fclose($fhandle);
        exec("rm /etc/network/if-up.d/drainware");
	} else {
           
        $interfaces = str_replace(array('[IP]','[NETMASK]','[GATEWAY]'), array($config['static'],$config['mask'],$config['gateway']), $template_interfaces);
	if (!empty($config['dns2'])) {
        $resolv = str_replace(array('[IP]','[DNS1]','[DNS2]'), array($config['static'],$config['dns'],$config['dns2']), $template_resolv_2);
	} else {
        $resolv = str_replace(array('[IP]','[DNS1]'), array($config['static'],$config['dns']), $template_resolv_1);
	}


       $resolv_file = "/etc/resolv.conf";
       $fhandle = fopen($resolv_file,"w");
       fwrite($fhandle,$resolv);
       fclose($fhandle);


       $fhandle = fopen($interfaces_file,"w");
       fwrite($fhandle,$interfaces . PHP_EOL . $routes);
       fclose($fhandle);

       exec("cp /etc/dhcp3/dhclient-exit-hooks.d/drainware /etc/network/if-up.d/drainware");
       exec("chmod +x /etc/network/if-up.d/drainware");

 	}

       //exec("/etc/init.d/networking restart &");

    }

}

?>
