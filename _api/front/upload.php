<?php
    // 파일이 업로드되었고 오류 없이 전송되었는지 확인
    if (isset($_FILES['upload']) && $_FILES['upload']['error'] === UPLOAD_ERR_OK) {
      $fileTmpPath = $_FILES['upload']['tmp_name']; // 임시 파일 경로
      $fileName = $_FILES['upload']['name']; // 원본 파일 이름
      $fileSize = $_FILES['upload']['size']; // 파일 크기
      $fileType = $_FILES['upload']['type']; // 파일 MIME 타입
      $fileNameCmps = explode(".", $fileName); // 파일 이름을 '.' 기준으로 나눔
      $fileExtension = strtolower(end($fileNameCmps)); // 확장자 추출 및 소문자로 변환

      // 허용되는 파일 확장자 리스트
      $allowedfileExtensions = array('jpg', 'gif', 'png', 'webp', 'pdf', 'docx');

      // 업로드된 파일 확장자가 허용되는지 확인
      if (in_array($fileExtension, $allowedfileExtensions)) {
          // 파일이 저장될 디렉토리 경로
          $uploadFileDir = './uploads/';
          $dest_path = $uploadFileDir . $fileName; // 최종 파일 저장 경로

          // uploads 디렉토리가 없으면 생성
          if (!is_dir($uploadFileDir)) {
              mkdir($uploadFileDir, 0777, true); // 디렉토리 생성 (권한 0777)
          }

          // 임시 파일을 최종 경로로 이동하여 저장
          if (move_uploaded_file($fileTmpPath, $dest_path)) {
              // 업로드가 성공한 경우, 파일의 URL을 반환
              $response = array(
                  'status' => 'success',
                  'url' => 'https://actrip.co.kr/_api/uploads/' . $fileName // 업로드된 파일 URL
              );
          } else {
              // 파일 이동에 실패한 경우 오류 응답
              $response = array(
                  'status' => 'error',
                  'message' => 'There was an error moving the uploaded file.'
              );
          }
      } else {
          // 허용되지 않는 파일 형식일 경우 오류 응답
          $response = array(
              'status' => 'error',
              'message' => 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions)
          );
      }
  } else {
      // 파일이 업로드되지 않았거나 업로드 오류가 있을 경우 오류 응답
      $response = array(
          'status' => 'error',
          'message' => 'No file uploaded or there was an upload error.'
      );
  }


$returnArray = $response;

?>