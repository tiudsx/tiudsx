<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/_api/db.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/_api/utils/response.php';

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

$returnArray = array();

// type 값에 따라 폴더명 정의
$front_types = ['bus', 'shop'];
$back_types = ['sol', 'solSave'];

$folder = 'front'; // 기본값
if (in_array($type, $back_types)) {
    $folder = 'back';
}

// 요청 처리
if(in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])){
    require_once $_SERVER['DOCUMENT_ROOT']."/_api/$folder/$type.php";
}

// PUT 요청 처리
if($_SERVER['REQUEST_METHOD'] === 'PUT'){
    // PUT 처리 로직
}

// DELETE 요청 처리
if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
    // DELETE 처리 로직
}

// 응답 생성 (각 type.php 파일에서 extract(createResponse())를 사용하여 변수들이 설정됨)
$response = createResponse($success ?? "false", $message ?? "", $returnArray ?? [], [
    "returnCode" => $returnCode ?? "201",
    "message" => $message ?? "처리되지 않은 요청입니다.",
    "errCode" => $errCode ?? "",
    "errMsg" => $errMsg ?? ""
]);

// echo $_SERVER['REQUEST_METHOD'];
echo json_encode($response, JSON_UNESCAPED_UNICODE);

mysqli_close($conn);
?>