<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// csrf.php (CSRF 토큰을 생성 및 세션에 저장)
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));  // CSRF 토큰 생성
}

// 프론트엔드에 토큰 전달 (HTML 페이지의 메타 태그 혹은 JSON 형태로 전달)
echo json_encode(['csrf_token' => $_SESSION['csrf_token']]);

?>