<?php
require_once(dirname(__FILE__)."/perse.php");
function show_table(){
    $src_info = list_info();

    $pinfo_time = "";

    if ($src_info['si']['si']['isutc']) $pinfo_time=$pinfo_time." (UTC)";

    $ntime = (time() - $src_info['si']['si']['tzonec']);
    $cur_time=date("Y/m/d H:i:s",$ntime);

    {
        echo "<tr>";
        //echo "<td>Id</td>";
        echo "<td>Last Update$pinfo_time</td>";
        echo "<td colspan=4>$cur_time</td>";
        echo "</tr>";
        echo "\n";
    }
    {
        echo "<tr>";
        echo "<td>System Id</td>";
        echo "<td>IP Address</td>";
        echo "<td>MAC Address</td>";
        echo "<td>Address Entry$pinfo_time</td>";
        echo "<td>Address Expiration$pinfo_time</td>";
        echo "</tr>";
        echo "\n";
    }
    foreach($src_info as $info){
        if (isset($info['si'])){continue;}
        if (!isset($info['ip'])) continue;
        echo "<tr>";
        echo "<td>".$info['id']."</td>";
        echo "<td>".$info['ip']."</td>";
        echo "<td>".$info['mac']."</td>";
        echo "<td>".$info['start']."</td>";
        echo "<td>".$info['end']."</td>";
        echo "</tr>";
        echo "\n";
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Lease Info</title>
    </head>
    <body>
        <table border=1>
            <tbody>
<?php show_table(); ?>
            </tbody>
        </table>
    </body>
</html>