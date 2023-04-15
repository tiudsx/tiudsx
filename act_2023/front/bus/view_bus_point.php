<?php
include __DIR__.'/../../common/func.php';

$res_spointname = $_REQUEST["res_spointname"];
$res_bus = $_REQUEST["res_bus"];
$gubun = $_REQUEST["gubun"];

header('Content-Type: application/json');

//echo str_replace("|", " / ", fnBusPoint($res_spointname, $res_bus));
if($gubun == "point"){

    $output = json_encode(fnBusPoint($res_spointname, $res_bus), JSON_UNESCAPED_UNICODE);

}else if($gubun == "pointlist"){
    
    $output = json_encode(fnBusPoint("", ""), JSON_UNESCAPED_UNICODE);
    
}
echo urldecode($output);
?>
