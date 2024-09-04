<?php
// CORS 헤더 설정
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Accept, Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization");
header("Content-type:text/html;charset=utf-8");
header('Content-Type: application/json');

// OPTIONS 요청에 대한 응답
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// JSON 요청 데이터 읽기
$json = file_get_contents('php://input');
$data = json_decode($json, true);

$response = array("success" => false);
$response["REQUEST_METHOD"] = $_SERVER['REQUEST_METHOD'];
switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        // 데이터 생성 예제
        $response["data"] = $data;
        $response["params"] = $_POST;
        $response["success"] = true;
        $response["message"] = "New record created successfully";
        break;

    case 'GET':
        // 데이터 조회 예제
        $response["data"] = $data;
        $response["success"] = true;
        $response["params"] = $_GET;
        break;

    case 'PUT':
        // 데이터 업데이트 예제
        $response["data"] = $data;
        $response["success"] = true;
        $response["message"] = "Record updated successfully";
        break;

    case 'DELETE':
        // 데이터 삭제 예제
        $response["data"] = $data;
        $response["success"] = true;
        $response["message"] = "Record deleted successfully";
        break;

    default:
        $response["message"] = "Invalid request method";
        break;
}

echo json_encode($response);
// // 로그인 API
// function apiData2() {
// 	const formData = new FormData();
// 	formData.append('type', 'bus');
// 	formData.append('code', 'point');

// 	var formData2 = 'type=bus&code=point';

// 	const config = {
// 		headers: {
// 			Accept: 'application/json;',
// 		},
// 	};
// 	return axios.post(
// 		'https://actrip.co.kr/_api/api.php',
// 		formData,
// 		// formData2,
// 		// config,
// 	);
// }
?>

