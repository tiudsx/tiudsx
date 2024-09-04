<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/_api/db.php';

// $http_origin = $_SERVER['HTTP_ORIGIN']; 
// if ($http_origin == "http://localhost:5173") { 
//     header("Access-Control-Allow-Origin: $http_origin"); 
// }
header("Access-Control-Allow-Origin: *"); 
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Accept, Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization");
header("Content-type:text/html;charset=utf-8");

header('Content-Type: application/json');


$time = date("Y-m-d H시i분");
$type = $_REQUEST["type"]; //호출 URL
$code = $_REQUEST["code"]; //호출 구분코드

$count = -1;
$returnArray = array();

// GET 요청 처리
if($_SERVER['REQUEST_METHOD'] === 'GET'){
    require_once $_SERVER['DOCUMENT_ROOT']."/_api/front/$type.php";
}

// POST 요청 처리
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    require_once $_SERVER['DOCUMENT_ROOT']."/_api/front/$type.php";
}

// PUT 요청 처리
if($_SERVER['REQUEST_METHOD'] === 'PUT'){

}

// DELETE 요청 처리
if($_SERVER['REQUEST_METHOD'] === 'DELETE'){

}

$response = array("success" => $success
                , "code" => $returnCode
                // , "message" => $message
                , "count" => $count
                , "data" => $returnArray
                );

// echo $_SERVER['REQUEST_METHOD'];
echo json_encode($response, JSON_UNESCAPED_UNICODE);

mysqli_close($conn);
?>