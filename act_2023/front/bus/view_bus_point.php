<?php
include __DIR__.'/../../common/func.php';

$bus_line = $_REQUEST["bus_line"];
$point = $_REQUEST["point"];
$shopseq = $_REQUEST["shopseq"];
$gubun = $_REQUEST["gubun"];

header('Content-Type: application/json');

//echo str_replace("|", " / ", fnBusPoint($res_spointname, $res_bus));
if($gubun == "point"){

    $output = json_encode(fnBusPoint2023($bus_line, $point, $shopseq), JSON_UNESCAPED_UNICODE);

}else if($gubun == "pointlist"){
    
    $output = json_encode(fnBusPoint2023("", "", $shopseq), JSON_UNESCAPED_UNICODE);
    
}
echo urldecode($output);
?>
