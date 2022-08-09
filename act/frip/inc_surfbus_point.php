<?php
include __DIR__.'/../frip/inc_func.php';

$res_spointname = $_REQUEST["res_spointname"];
$res_bus = $_REQUEST["res_bus"];
$selDate = $_REQUEST["selDate"];

echo str_replace("|", " / ", fnBusPoint($res_spointname, $res_bus, $selDate));
?>
