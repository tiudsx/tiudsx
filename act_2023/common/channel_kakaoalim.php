<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.

function fnKakaoSend($data){
	// cURL 초기화
	$ch = curl_init();
	$data = json_encode($data);

	$API_PLEX_ID = "wairi";
	$API_PLEX_KEY = "1758a135-43db-481a-a367-65b4d6a666bf";
	$API_PLEX_URL = "https://27ep4ci1w0.apigw.ntruss.com/at-standard/v2/send";

	// Authorization 헤더 설정
	$authorizationHeader = $API_PLEX_ID . ';' . $API_PLEX_KEY;
	//Content-Type
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		  'Authorization: ' . $authorizationHeader,
		  'Accept: application/json',
		  'Content-Type: application/json;charset=utf-8')
	);
	// cURL 옵션 설정
	curl_setopt($ch, CURLOPT_URL, $API_PLEX_URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

	// HTTPS 요청을 위한 설정
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

	// 요청 실행
	$response = curl_exec($ch);

	// // 요청 실패 시 에러 출력
	// if ($response === false) {
	//    log_message('debug', 'cURL Error: ' . curl_error($ch));
	// }

	// // 응답 출력
	// log_message('debug', 'Plex response : ' . $response);

	// cURL 세션 종료
	curl_close($ch);

	return array($response, curl_error($curl));
}

function at_config($template_code, $data)
{
	$API_OUTGOING_KEY = "bba88a59f6f79c784c2ed3ce2a0c1bdacf8f0bef";
	$at_template = at_template($template_code, $data);
	if ($template_code == 'acrtip_reservation') {
	  return array(
		 "msg_type" => "AT",
		 "msg_data" => array(
			array(
			   "msg_key" => $template_code,
			   "sender_number" => "01027561810",
			   "receiver_number" => $data['receiver_number'],
			   "msg" => $at_template,
			   "sender_key" => $API_OUTGOING_KEY,
			   "template_code" => $template_code,
			   "attachment" => array(
				  "button" => array(
					 array(
						"name" => "예약하기",
						"type" => "WL",
						"url_mobile" => "https://actrip.co.kr/".$data['url']
					 )
				  )
			   )
			)
		 )
	  );
   }else if($template_code == 'actrip_notice'){
		return array(
			"msg_type" => "AT",
			"msg_data" => array(
			array(
				"msg_key" => $template_code,
				"sender_number" => "01027561810",
				"receiver_number" => $data['receiver_number'],
				"msg" => $at_template,
				"sender_key" => $API_OUTGOING_KEY,
				"template_code" => $template_code
				)
			)
		);
   }
}

function at_template($template_code, $data): string
{
   switch ($template_code) {
	  case 'acrtip_reservation' : //일반고객 구매_파트너 발송
		 $msg = acrtip_reservation($data);
		 break;
	case 'actrip_notice' : //예약 확정 안내
		$msg = actrip_notice($data);
		break;
	  default:
		 $msg = '';
		 break;
   }

   return $msg;
}

function acrtip_reservation($data)
{
       return '[모행 버스 예약 안내]

안녕하세요 '.$data['name'].'님, 모행입니다.

아래 내용으로 버스 예약이 접수되었습니다.
아래 링크를 통해 좌석/정류장 예약 진행 부탁드립니다 :)

■ 예약 정보
 - 예약상품: '.$data['shop_name'].'
 - 예약자: '.$data['reservation_name'].'
 - 예약좌석'.$data['seat'].'

■ 버스 예약 안내
 - [예약하기] 버튼을 클릭 후 좌석/정류장을 예약해주세요.
 - 잔여석이 없을 경우 예약이 취소될 수 있으니 빠른 예약 부탁드립니다 :)


※ 문의사항이 있으실 경우, 모행 홈페이지 내 채널톡을 이용해주시면 더욱 빠른 상담이 가능합니다. 감사합니다.';
}

function actrip_notice($data)
{
   return '[모행 이용 안내]

안녕하세요 '.$data['name'].'님, 액티비티 플랫폼 모행입니다 :)
예약하신 상품 관련 안내사항 전달드립니다.

■ 예약 정보
- 예약상품: '.$data['shop_name'].'
- '.$data['reservation_information'].'
- 예약자: '.$data['reservation_name'].'
---------------------------------
▶ 안내사항
'.$data['information'].'

※ 문의사항이 있으실 경우, 모행 홈페이지 내 채널톡을 이용해주시면 더욱 빠른 상담이 가능합니다. 감사합니다.';
}
?>