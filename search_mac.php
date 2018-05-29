<?php
    require_once(dirname(__FILE__)."/perse.php");

    $res;

    $pinfo_time = "";
    if ($src_info['si']['si']['isutc']) $pinfo_time=$pinfo_time." (UTC)";

    function mac_toq($mc_o){
        if ($mc_o == ""){return "";}
        $mc=str_replace(array(':','%3A'),'',$mc_o);
        $mc=strtolower($mc);
        if (!preg_match("/[a-f0-9]{12}/",$mc)) die("No valid MAC Address");
        return $mc;
    }

    function query($targ){
        global $res;
        if (!isset($res)){return "None";}
        return $res[$targ];
    }

    if (isset($_GET['mac'])){$res=get_per_mac(mac_toq($_GET['mac']));}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>MAC Address Search</title>
    </head>
    <body>
        <form action="search_mac.php" method="get">
            <input type="text" name="mac">
            <input type="submit" value="Search">
        </form>
        <table>
            <tbody>
                <tr><td>Mac Address</td><td>:</td><td><?php echo query("mac"); ?></td></tr>
                <tr><td>IP Address</td><td>:</td><td><?php echo query("ip"); ?></td></tr>
                <tr><td>Address Entry<?php echo $pinfo_time ?></td><td>:</td><td><?php echo query("end"); ?></td></tr>
                <tr><td>Address Expiration<?php echo $pinfo_time ?></td><td>:</td><td><?php echo query("start"); ?></td></tr>
            </tbody>
        </table>
    </body>
</html>