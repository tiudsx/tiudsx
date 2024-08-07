<?
include_once $_SERVER['DOCUMENT_ROOT'].'/_api/db.php';

header('Content-Type: application/json');
header("HTTP/1.1 200 OK");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
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