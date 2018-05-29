<?php
//$leasefile = "sample.dhcpd.lease";
$leasefile = "/var/lib/dhcp/dhcpd.leases";

$isutc=true;
$tzonec = 32400;

function get_dhcplease(){
    global $leasefile;
    if (!file_exists($leasefile)) die("FAIL : NO DHCP SESSION");
    // echo file_get_contents($leasefile);
    return file_get_contents($leasefile);
}

function check_dup($array,$ipaddr){
    $targ;
    foreach($array as $ps_info){
        if (isset($ps_info['si'])){continue;}
        if ($ps_info['ip'] == $ipaddr){
            $targ=$ps_info['id'];
            break;
        }
    }
    if (!isset($targ)){return false;}
    return $targ;
}

function perse($leases){
    global $isutc;
    global $tzonec;

    $leases = preg_replace("/^#.*/","",$leases);
    $leases = preg_replace("/^$/","",$leases);
    $leases = explode("\n",$leases);
    $ipa_list = array();
    $std_list = array();
    $edd_list = array();
    $maca_list = array();

    foreach ($leases as $ip_a){
        if (preg_match("/^lease .*/",$ip_a)) $ipa_list[] = str_replace("lease ","",str_replace(" {","",$ip_a));
    }
    //var_dump($ipa_list);
    foreach ($leases as $mac_a){
        if (preg_match("/^  hardware ethernet .*/",$mac_a)) $maca_list[] = str_replace("  hardware ethernet ","",str_replace(";","",str_replace(":","",$mac_a)));
    }
    //var_dump($maca_list);
    foreach ($leases as $st_date){
        if (preg_match("/^  starts [0-9]+ .*/",$st_date)) $std_list[] = str_replace(";","",preg_replace("/^  starts [0-9]+ /","",$st_date));
    }
    //var_dump($std_list);
    foreach ($leases as $ed_date){
        if (preg_match("/^  ends [0-9]+ .*/",$ed_date)) $edd_list[] = str_replace(";","",preg_replace("/^  ends [0-9]+ /","",$ed_date));
    }
    //var_dump($edd_list);

    $ret=array();

    $ret['si']['si']['isutc']=$isutc;
    $ret['si']['si']['tzonec']=$tzonec;

    $now=0;
    foreach ($ipa_list as $ipa_t){
        //$nowtime=1526727585;
        $nowtime=time();
        $exp = strtotime(str_replace(array("\r\n","\n","\r"), '', $edd_list[$now]));
        if ($isutc) {$nowtime = ($nowtime - $tzonec);}
        //echo "Now:".$nowtime." Exp:".$exp."<BR>\n";
        if ($exp < $nowtime) {$now++; continue;}

        $res_chk = check_dup($ret,$ipa_t);
        if ($res_chk !== false){
            $ret[$res_chk] = null;
        }

        $ret[$now]['id']=$now;
        $ret[$now]['end']=str_replace(array("\r\n","\n","\r"), '', $edd_list[$now]);
        $ret[$now]['start']=str_replace(array("\r\n","\n","\r"), '', $std_list[$now]);
        $ret[$now]['ip']=str_replace(array("\r\n","\n","\r"), '', $ipa_t);
        $ret[$now]['mac']=str_replace(array("\r\n","\n","\r"), '', $maca_list[$now]);
        $now++;
    }

    //var_dump($ret);
    return $ret;
}

function list_info(){
    return perse(get_dhcplease());
}
function get_per_mac($mac){
    $persed=perse(get_dhcplease());

    $targ;
    foreach($persed as $ps_info){
        if (isset($ps_info['si'])){continue;}
        if ($ps_info['mac'] == $mac){
            $targ=$ps_info['id'];
            break;
        }
    }

    //var_dump($persed[$targ]);
    return $persed[$targ];
}
?>