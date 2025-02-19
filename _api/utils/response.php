<?php
function createResponse($success = "false", $message = "", $data = [], $options = []) {
    $default = [
        "success" => $success,
        "returnCode" => ($success === "true") ? "200" : "201",
        "message" => $message,
        "count" => is_array($data) ? count($data) : 0,
        "data" => $data,
        "method" => $_SERVER['REQUEST_METHOD'],
        "errCode" => "",
        "errMsg" => ""
    ];

    // DB 에러 정보가 있는 경우
    if (isset($options['conn']) && $success === "false") {
        $default["errCode"] = mysqli_errno($options['conn']);
        $default["errMsg"] = mysqli_error($options['conn']);
    }

    // 추가 옵션이 있는 경우 덮어쓰기
    return array_merge($default, array_diff_key($options, ['conn' => '']));
} 