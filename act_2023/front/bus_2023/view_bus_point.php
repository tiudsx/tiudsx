<?php
include __DIR__.'/../../common/func.php';

$res_spointname = $_REQUEST["res_spointname"];
$res_bus = $_REQUEST["res_bus"];
$gubun = $_REQUEST["gubun"];
$busSeq = $_REQUEST["busSeq"];

header('Content-Type: application/json');

//echo str_replace("|", " / ", fnBusPoint($res_spointname, $res_bus));
if($gubun == "point"){

    $output = json_encode(fnBusPoint2023($res_spointname, $res_bus, $busSeq), JSON_UNESCAPED_UNICODE);

}else if($gubun == "pointlist"){
    
    $output = json_encode(fnBusPoint2023("", "", $busSeq), JSON_UNESCAPED_UNICODE);
    
}
echo urldecode($output);
?>
