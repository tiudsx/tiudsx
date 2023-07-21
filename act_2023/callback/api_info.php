<?php
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/func.php';

$param = $_REQUEST["param"];

$http_origin = $_SERVER['HTTP_ORIGIN']; 
if ($http_origin == "https://www.wairi.co.kr" || $http_origin == "http://www.mohaeng.co.kr" || $http_origin == "http://www.landingko.com") { 
    header("Access-Control-Allow-Origin: $http_origin"); 
}

header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Accept, Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization");
header("Content-type:text/html;charset=utf-8");

header('Content-Type: application/json');

echo $param;
return;
?>