<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.

function channel_sendKakao($arrKakao){
	$curl = curl_init();

	$rtnMsg = channel_kakaoMsg($arrKakao);
    
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://alimtalk-api.bizmsg.kr/v2/sender/send",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => $rtnMsg,
	  CURLOPT_HTTPHEADER => array(
		"content-type: application/json", "userId: surfenjoy"
	  ),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	return array($response, $err);
}

function channel_kakaoContent($arrKakao){
	if($arrKakao["tempName"] == "frip_bus02"){ //셔틀버스 예약확정
		$kakaoMsg = $arrKakao["smsTitle"]
			.'\n\n안녕하세요. '.$arrKakao["userName"].'님'
			.'\n서핑버스를 예약해주셔서 감사합니다.'
			.'\n\n예약정보 [예약확정]'
			.'\n ▶ 예약상품 : '.$arrKakao["shopname"]
			.'\n ▶ 예약자 : '.$arrKakao["userName"]
			.'\n'.$arrKakao["msgInfo"]
			.'---------------------------------'
			.'\n ▶ 안내사항'
			.'\n      - 교통상황으로 인해 지연 도착할 수 있으니 양해부탁드립니다.'
			.'\n      - 이용일, 탑승위치 확인 및 탑승시간 10분전 도착 부탁드려요~';

	}else if($arrKakao["tempName"] == "at_bus_kakao"){ //타채널 알림톡발송
		if($arrKakao["PROD_TYPE"] == "bus_channel"){ //타채널 알림톡발송
			$kakaoMsg = $arrKakao["smsTitle"]
				.'\n\n안녕하세요. '.$arrKakao["userName"].'님'
				.'\n모행을 이용해주셔서 감사합니다.'
				.'\n셔틀버스 좌석/정류장 예약관련 안내드립니다.'
				.'\n\n예약정보 [예약대기]'
				.'\n ▶ 예약상품 : 모행 셔틀버스'
				.'\n ▶ 예약자 : '.$arrKakao["userName"]
				.'\n ▶ 예약좌석'.$arrKakao["msgInfo"]
				.'\n---------------------------------'
				.'\n ▶ 안내사항'
				.'\n      - [예약하기] 클릭 후 좌석/정류장을 예약해주세요.';

		}else if($arrKakao["PROD_TYPE"] == "bus_push"){ // 타채널 재발송 안내
			$kakaoMsg = $arrKakao["smsTitle"]
				.'\n\n안녕하세요. '.$arrKakao["userName"].'님'
				.'\n카카오채널로 예약링크를 안내드렸으나 예약확정이 안되어 다시 한번 안내드립니다.'
				.'\n\n예약정보 [예약대기]'
				.'\n ▶ 예약상품 : '.$arrKakao["shopname"]
				.'\n ▶ 예약자 : '.$arrKakao["userName"]
				.'\n---------------------------------'
				.'\n ▶ 안내사항'
				.'\n      - [예약하기] 클릭 후 좌석/정류장을 예약해주세요.';

		}
	}

	return $kakaoMsg;
}

function channel_kakaoMsg($arrKakao){
	$Url = "https://actrip.co.kr/";

	$btn_ResSearch = '{"type":"WL","name":"예약조회","url_mobile":"https://actrip.co.kr/'.$arrKakao["btn_ResSearch"].'"}';
	$btn_ResChange = '{"type":"WL","name":"좌석/정류장 변경","url_mobile":"https://actrip.co.kr/'.$arrKakao["btn_ResChange"].'"}';
	$btn_ResPoint = '{"type":"WL","name":"탑승시간/위치 안내","url_mobile":"https://actrip.co.kr/'.$arrKakao["btn_ResPoint"].'"}';
	$btn_ResGPS = '{"type":"WL","name":"셔틀버스 실시간위치 조회","url_mobile":"https://actrip.co.kr/'.$arrKakao["btn_ResGPS"].'"}';

	$btnList = "";
    if($arrKakao["tempName"] == "frip_bus02"){ //셔틀버스 예약확정
        $btnList = '"button1":'.$btn_ResSearch
			.',"button2":'.$btn_ResChange
			.',"button3":'.$btn_ResPoint
			.',"button4":'.$btn_ResGPS.',';

	}else if($arrKakao["tempName"] == "at_bus_kakao"){ //타채녈 셔틀버스 예약안내
		$btnList = '"button1":{"type":"WL","name":"예약하기","url_mobile":"https://actrip.co.kr/'.$arrKakao["link1"].'"},';
	}

	$arrKakao["kakaoMsg"] = channel_kakaoContent($arrKakao); //카카오 메시지 내용

	//문자발송용 변수
	$msgSmsBtn = $arrKakao["kakaoMsg"].'\n\n ▶ 문의 : http://pf.kakao.com/_HxmtMxl';

	$arryKakao = '[{"message_type":"at","phn":"82'.substr(str_replace('-', '',$arrKakao["userPhone"]), 1).'","profile":"70f9d64c6d3b9d709c05a6681a805c6b27fc8dca","tmplId":"'.$arrKakao["tempName"].'","msg":"'.$arrKakao["kakaoMsg"].'",'.$btnList.'"smsKind":"L","msgSms":"'.$msgSmsBtn.'","smsSender":"'.str_replace('-', '',$arrKakao["userPhone"]).'","smsLmsTit":"'.$arrKakao["smsTitle"].'","smsOnly":"'.$arrKakao["smsOnly"].'"}]';

    return $arryKakao;
}
?>