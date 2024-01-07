<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.

function rtnProfile($type){
	if($type == "sol"){
		$profile = "97a40cc247321233e99ceb03ab355c1e6ee4b9dd"; //솔게하
	}else{
		$profile = "70f9d64c6d3b9d709c05a6681a805c6b27fc8dca"; //액트립
	}

	return $profile;
}

function sendKakao($arrKakao){
	$curl = curl_init();
	if($arrKakao["tempName"] == "sol_info01" || $arrKakao["tempName"] == "sol_info02" || $arrKakao["tempName"] == "actrip_info01"){
		$rtnMsg = kakaoMsg2024($arrKakao);
	}else{
		$rtnMsg = kakaoMsg($arrKakao);
	}
    
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


function kakaoMsg2024($arrKakao){
	if($arrKakao["tempName"] == "sol_info01" || $arrKakao["tempName"] == "sol_info02"){
		$profile = rtnProfile("sol");
	}else{
		$profile = rtnProfile("actrip");
	}

	$arryKakao = '';
	foreach($arrKakao["arryData"] as $item) {

		//내용 안내
		$msgKakao = kakaoContent2024($arrKakao, $item);
		$arrKakao["kakaoMsg"] = $msgKakao["kakaoMsg"];
		$items = $msgKakao["items"];
		
		//문자발송용 변수
		$msgSms = $msgKakao["items_text"].$arrKakao["kakaoMsg"];

		$arryKakao .= '{
			"message_type":"at"
			,"phn":"82'.substr(str_replace('-', '',$item["userPhone"]), 1).'"
			,"profile":"'.$profile.'"
			,"tmplId":"'.$arrKakao["tempName"].'"
			,"msg":"'.$arrKakao["kakaoMsg"].'"
			,"header":"'.$arrKakao["title"].'"
			'.$items.'
			,"smsKind":"L"
			,"msgSms":"'.$msgSms.'"
			,"smsSender":"'.str_replace('-', '',$item["userPhone"]).'"
			,"smsLmsTit":"'.$arrKakao["title"].'"
			,"smsOnly":"'.$arrKakao["smsOnly"].'"
		}';

		if( next( $arrKakao["arryData"] ) ) {
			$arryKakao .= ",";
		}
	}

	$arryKakao = '['.$arryKakao.']';
	
    
    return $arryKakao;
}

function kakaoContent2024($arrKakao, $item){
	if($arrKakao["tempName"] == "sol_info01"){ //솔게하 계좌안내
		$bankText = "농협 / 0-4337-5080-8";

		$kakaoMsg = '안녕하세요. '.$item["userName"].'님'
			.'\n솔게하&솔서프 동해점을 예약해주셔서 감사합니다.'
			.'\n입금 완료 후 예약확정 및 이용 가능합니다.'
			.'\n\n ▶ 계좌번호 : '.$bankText
			.'\n\n안내된 계좌로 입금 부탁드립니다.'
			.'\n감사합니다.';

		$items = '
		,"items": {
			"item": {
				"list": [
					{
					"title": "예약자",
					"description": "'.$item["userName"].'"
					},
					{
					"title": "이용일",
					"description": "'.$item["userDate"].'"
					},
					{
					"title": "계좌번호",
					"description": "'.$bankText.'"
					},
					{
					"title": "예금주명",
					"description": "이승철"
					}
				],
				"summary": {
					"title": "입금금액",
					"description": "'.$item["userPrice"].'"
				}
			},
			"itemHighlight": {
				"title": "입금계좌 안내",
				"description": "입금 완료 후 확정 됩니다."
			}
		}';

		//디버깅용 아이템리스트
		$items_text = $arrKakao["title"]
		.'\n\n입금계좌 안내'
		.'\n입금 완료 후 확정 됩니다.'
		.'\n\n예약자 : '.$item["userName"]
		.'\n이용일 : '.$item["userDate"]
		.'\n계좌번호 : '.$bankText
		.'\n예금주명 : 이승철'
		.'\n입금금액 : '.$item["userPrice"]
		.'\n\n';

	}else if($arrKakao["tempName"] == "sol_info02"){ //솔게하 확정안내
		$kakaoMsg = '안녕하세요. '.$item["userName"].'님'
			.'\n솔게하&솔서프 동해점을 예약해주셔서 감사합니다.'
			.'\n예약하신 정보를 안내드립니다.'
			.'\n\n예약안내 : '.$item["link1"]
			.'\n\n▶ 안내사항'
			.'\n - 숙박 고객님은 입실시간 확인 후 셀프 체크인 부탁드려요~';
			//.'\n - 기타 자세한 내용은 예약안내 링크에서 확인가능합니다.';

		$items = '
		,"items": {
			"item": {
				"list": [
					{
					"title": "예약자",
					"description": "'.$item["userName"].'"
					},
					{
					"title": "이용일",
					"description": "'.$item["userDate"].'"
					}
				]
			},
			"itemHighlight": {
				"title": "예약확정 안내",
				"description": "아래 예약안내 링크를 확인해주세요."
			}
		}';

		//디버깅용 아이템리스트
		$items_text = $arrKakao["title"]
		.'\n\n예약확정 안내'
		.'\n아래 예약안내 링크를 확인해주세요.'
		.'\n\n예약자 : '.$item["userName"]
		.'\n이용일 : '.$item["userDate"]
		.'\n\n';
	}else if($arrKakao["tempName"] == "actrip_info01"){ //액트립 버스예약
		if($item["gubun"] == "bus_stay"){
			//입금대기 안내
			$bankText = "우리은행 / 1002-845-467316";

			$kakaoMsg = '안녕하세요. '.$item["userName"].'님'
				.'\n액트립 셔틀버스를 예약해주셔서 감사합니다.'
				.'\n입금 완료 후 예약확정 및 이용 가능합니다.'
				.'\n\n - 계좌 : '.$bankText
				.'\n - 예금주 : 이승철'
				.'\n - 총금액 : '.$item["userPrice"]
				.'\n\n ▶ 안내사항'
				.'\n    - 1시간 이내 미입금시 자동취소됩니다.'
				.'\n    - 최소인원(20명) 모집이 안 될 경우 운행이 취소될 수 있습니다.'
				.'\n    - 운행취소 시 이용일 3~4일 전 연락드립니다.';

		}else if($item["gubun"] == "bus_channel"){
			//타채널 예약안내
			$kakaoMsg = '안녕하세요. '.$item["userName"].'님'
				.'\n'.$item["sub_name"].'에서 셔틀버스를 예약해주셔서 감사합니다.'
				.'\n액트립 사이트에서 좌석/정류장을 예약하셔야 확정 및 이용 가능합니다.'
				.'\n\n - 예약상태 : 예약대기'
				.'\n - 예약상품 : '.$item["sub_prod"]
				.'\n - 예약하기 : '.$item["link1"]
				.'\n\n▶ 안내사항'
				.'\n    - 잔여석이 없을 경우 예약이 취소 될 수 있으니 빠른 예약부탁드려요~'
				.'\n    - 취소/환불 및 문의는 채팅으로 연락주세요.';
		}

		$items = '
		,"items": {
			"item": {
				"list": [
					{
					"title": "이용노선",
					"description": "'.$item["bus_line"].'"
					},
					{
					"title": "출발일",
					"description": "'.$item["day_start"].'"
					},
					{
					"title": "복귀일",
					"description": "'.$item["day_return"].'"
					}
				]
			}
		}';

		//디버깅용 아이템리스트
		$items_text = $arrKakao["title"]
		.'\n\n이용노선 : '.$item["bus_line"]
		.'\n출발일 : '.$item["day_start"]
		.'\n복귀일 : '.$item["day_return"]
		.'\n\n';
	}

	return array(
		"kakaoMsg"=> $kakaoMsg
		, "items"=> $items
		, "items_text"=> $items_text //디버깅용 메시지
	);	
}

function kakaoDebug2024($response, $returnCode, $insertType = 'I'){	
	$datetime = date('Y/m/d H:i'); 

	$resnum = $response["item"]["DebugInfo"]["resnum"];
	$PROD_NAME = $response["item"]["DebugInfo"]["PROD_NAME"];
	$PROD_TABLE = $response["item"]["DebugInfo"]["PROD_TABLE"];
	$PROD_TYPE = $response["item"]["DebugInfo"]["PROD_TYPE"];
	$RES_CONFIRM = $response["item"]["DebugInfo"]["RES_CONFIRM"];
	$USER_NAME = $response["item"]["userName"];
	$USER_TEL = $response["item"]["userPhone"];
	$KAKAO_DATE = $datetime;
	
	$items_text = kakaoContent2024($response["arrKakao"], $response["item"])["items_text"];
	$KAKAO_CONTENT = $items_text.kakaoContent2024($response["arrKakao"], $response["item"])["kakaoMsg"]; //카카오 메시지 내용
	
	$KAKAO_BTN1 = "";
	$KAKAO_BTN2 = "";
	$KAKAO_BTN3 = "";
	$KAKAO_BTN4 = "";
	$KAKAO_BTN5 = "";

	$code = $response["code"];
	$msgid = $response["msgid"];
	$message = $response["message"];
	$originMessage = $response["originMessage"];

	if($insertType == "U"){
		return "UPDATE AT_KAKAO_HISTORY SET KAKAO_DATE = '$KAKAO_DATE', KAKAO_CONTENT = '$KAKAO_CONTENT', response = '$returnCode', code = '$code', msgid = '$msgid', `message` = '$message', originMessage = '$originMessage' WHERE resnum = '$resnum'";
	}else{
		return "INSERT INTO `AT_KAKAO_HISTORY`(`resnum`, `PROD_NAME`, `PROD_TABLE`, `PROD_TYPE`, `RES_CONFIRM`, `USER_NAME`, `USER_TEL`, `KAKAO_DATE`, `KAKAO_CONTENT`, `KAKAO_BTN1`, `KAKAO_BTN2`, `KAKAO_BTN3`, `KAKAO_BTN4`, `KAKAO_BTN5`, `response`, `err`, `code`, `msgid`, `message`, `originMessage`) VALUES ('$resnum','$PROD_NAME','$PROD_TABLE','$PROD_TYPE',$RES_CONFIRM,'$USER_NAME','$USER_TEL','$KAKAO_DATE','$KAKAO_CONTENT','$KAKAO_BTN1','$KAKAO_BTN2','$KAKAO_BTN3','$KAKAO_BTN4','$KAKAO_BTN5', '$returnCode', '', '$code', '$msgid', '$message', '$originMessage');";
	}
}

function kakaoContent($arrKakao){
	if($arrKakao["tempName"] == "at_surf_step3"){ //솔게하 알림톡 발송
		$kakaoMsg = $arrKakao["smsTitle"]
			.'\n\n안녕하세요. '.$arrKakao["userName"].'님'
			.'\n솔게하&솔서프를 예약해주셔서 감사합니다.'
			.'\n예약하신 정보를 안내드립니다.'
			.'\n\n예약정보'
			.'\n ▶ 예약자 : '.$arrKakao["userName"]
			.'\n\n하단에 있는 <[필독]예약 상세안내> 버튼을 클릭하시고 내용을 꼭 확인해주세요'
			.'\n---------------------------------'
			.'\n ▶ 안내사항'
			.'\n   - 6월~8월 주말은 예상시간보다 많이 걸릴 수 있으니 일찍 출발부탁드려요~'
			.'\n   - 서핑강습은 고객님 편의를 위해 제휴된 서핑샵으로 안내하고 있습니다.'
			.'\n   - 상담 및 문의가 있으신 경우 채팅방을 통해 톡 남겨주시면 빠르게 답변드리겠습니다.';

	}else if($arrKakao["tempName"] == "at_res_step4"){
		if($arrKakao["PROD_TYPE"] == "sol_bank"){ //솔게하 계좌안내
			if($arrKakao["kakaoprice"] == "0"){ //일반 계좌안내
				$bankText = "\n이용하실 상품의 입금 계좌번호를 안내드립니다.";
				$bankPrice = "";
			}else{ //금액 계좌안내
				$bankText = "\n이용하실 상품은 안내된 금액으로 입금해주시면 됩니다.";
				$bankPrice = '\n ▶ 입금금액 : '.number_format($arrKakao["kakaoprice"]).'원';
			}

			$kakaoMsg = $arrKakao["smsTitle"]
				.'\n\n안녕하세요. '.$arrKakao["userName"].'님'
				.'\n솔게하&솔서프를 이용해주셔서 감사합니다.'
				.$bankText
				.'\n\n예약정보'
				.'\n ▶ 예약자 : '.$arrKakao["userName"]
				.'\n ▶ 예약상품 : '.$arrKakao["kakaoRes"]
				.$bankPrice
				.'\n ▶ 계좌번호 : 농협 / 302-1454-3429-61'
				.'\n ▶ 예금주명 : 이승철'
				.'\n---------------------------------'
				.'\n ▶ 안내사항'
				.'\n    - 예약을 원하실 경우 안내된 계좌로 입금부탁드립니다.'
				.'\n    - 입금이 확인되면 확정 및 이용 가능하니 참고부탁드립니다.'
				.'\n    - 추가 문의사항은 카카오채널 또는 010-4337-5080으로 연락주세요.';

		}else if($arrKakao["PROD_TYPE"] == "sol_standby"){ //솔게하 예약안내
			$kakaoMsg = $arrKakao["smsTitle"]
				.'\n\n안녕하세요. '.$arrKakao["userName"].'님'
				.'\n솔게하&솔서프를 예약해주셔서 감사합니다.'
				.'\n예약이 확정되어 안내드립니다.'
				.'\n자세한 이용안내는 이용일 하루전에 발송되니 꼭 확인해주세요~'
				.'\n\n예약정보'
				.'\n ▶ 예약자 : '.$arrKakao["userName"]
				.'\n ▶ 예약상품 : '.$arrKakao["kakaoRes"]
				.'\n---------------------------------'
				.'\n ▶ 안내사항'
				.'\n    - 6월~8월 주말은 예상시간보다 많이 걸릴 수 있으니 일찍 출발부탁드려요~'
				.'\n    - 서핑강습은 고객님 편의를 위해 제휴된 서핑샵으로 안내하고 있습니다.'
				.'\n    - 상담 및 문의가 있으신 경우 채팅방을 통해 톡 남겨주시면 빠르게 답변드리겠습니다.';

		}else if($arrKakao["PROD_TYPE"] == "bus_cancel" || $arrKakao["PROD_TYPE"] == "surf_cancel"){ //자동취소 안내
			$kakaoMsg = $arrKakao["smsTitle"]
				.'\n\n안녕하세요. '.$arrKakao["userName"].'님'
				.'\n예약건에 대해 입금처리가 안되어서 취소되었습니다.'
				.'\n\n예약정보 [자동취소]'
				.'\n ▶ 예약상품 : '.$arrKakao["shopname"]
				.'\n ▶ 예약번호 : '.$arrKakao["MainNumber"]
				.'\n ▶ 예약자 : '.$arrKakao["userName"]
				.'\n---------------------------------'
				.'\n ▶ 안내사항'
				.'\n      - 입금마감시간이 지나서 자동취소가 되었습니다.'
				.'\n      - 이용을 원하실 경우 다시 예약해주세요.';

		}else if($arrKakao["PROD_TYPE"] == "bus_return"){ // 환불안내
            $kakaoMsg = $arrKakao["smsTitle"]
				.'\n\n안녕하세요. '.$arrKakao["userName"].'님'
				.'\n신청하신 환불내역 안내입니다.'
				.'\n\n예약정보 [환불요청]'
				.'\n ▶ 예약상품 : '.$arrKakao["shopname"]
				.'\n ▶ 예약번호 : '.$arrKakao["MainNumber"]
				.'\n ▶ 예약자 : '.$arrKakao["userName"]
				.'\n'.$arrKakao["msgInfo"]
				.'---------------------------------'
				.'\n ▶ 안내사항'
				.'\n      - 환불처리기간은 1~7일정도 소요됩니다.'
				.'\n      - 계좌번호를 잘못 입력하신 경우 카카오채널로 문의주세요.';

		}else if($arrKakao["PROD_TYPE"] == "bus_cancelinfo"){ // 셔틀버스 취소 안내
			$kakaoMsg = $arrKakao["smsTitle"]
				.'\n\n안녕하세요. '.$arrKakao["userName"].'님'
				.'\n'.$arrKakao["shopname"]
				.'\n\n서핑버스 안내'
				.'\n ▶ 예약자 : '.$arrKakao["userName"]
				.'\n'.$arrKakao["PROD_NAME"]
				.'\n---------------------------------'
				.'\n ▶ 안내사항'
				.'\n'.$arrKakao["PROD_URL"];

		}else if($arrKakao["PROD_TYPE"] == "bus_kakaoinfo"){ // 셔틀버스 이용 안내
			$kakaoMsg = $arrKakao["smsTitle"]
				.'\n\n안녕하세요. '.$arrKakao["userName"].'님'
				.'\n'.$arrKakao["shopname"]
				.'\n\n서핑버스 이용안내'
				.'\n ▶ 예약자 : '.$arrKakao["userName"]
				.'\n'.$arrKakao["PROD_NAME"]
				.'\n---------------------------------'
				.'\n ▶ 안내사항'
				.'\n'.$arrKakao["PROD_URL"];

		}

	}else if($arrKakao["tempName"] == "frip_bus03"){ //셔틀버스 입금대기
		$kakaoMsg = $arrKakao["smsTitle"]
			.'\n\n안녕하세요. '.$arrKakao["userName"].'님'
			.'\n서핑버스를 예약해주셔서 감사합니다.'
			.'\n\n예약정보 [입금대기]'
			.'\n ▶ 예약상품 : '.$arrKakao["shopname"]
			.'\n ▶ 예약번호 : '.$arrKakao["MainNumber"]
			.'\n ▶ 예약자 : '.$arrKakao["userName"]
			.'\n'.$arrKakao["msgInfo"]
			.'---------------------------------'
			.'\n ▶ 안내사항'
			.'\n      - 1시간 이내 미입금시 자동취소됩니다.'
			.'\n      - 최소인원(20명) 모집이 안 될 경우 운행이 취소될 수 있습니다.'
			.'\n      - 운행취소 시 이용일 3~4일 전 연락드립니다.'
			.'\n\n ▶ 입금계좌'
			.'\n      - 우리은행 / 1002-845-467316 / 이승철';

	}else if($arrKakao["tempName"] == "frip_bus02"){ //셔틀버스 예약확정
		$kakaolink = "";
		$kakaoInfo = "";

		if($arrKakao["PROD_TYPE"] == "bus_kakaoconfirm"){ //확정안내 공지사항
			$kakaoMsg = $arrKakao["smsTitle"]
				.'\n\n안녕하세요. '.$arrKakao["userName"].'님'
				.'\n액트립 서핑버스를 예약해주셔서 감사합니다.'
				.'\n서핑버스 이용에 불편함이 없도록 다시한번 탑승시간 안내드립니다.'
				.'\n아래 나와있는 차량 및 탑승시간을 꼭 체크해주세요. :)'
				.'\n\n예약정보 [예약확정]'
				.'\n ▶ 예약상품 : '.$arrKakao["shopname"]
				.'\n ▶ 예약자 : '.$arrKakao["userName"]
				.'\n'.$arrKakao["msgInfo"]
				.'---------------------------------'
				.'\n ▶ 안내사항'
				.'\n      - 교통상황으로 인해 지연 도착할 수 있으니 양해부탁드립니다.'
				.'\n      - 이용일, 탑승위치 확인 및 탑승시간 10분전 도착 부탁드려요~';

		}else{
			if($arrKakao["couponseq"] == "17" || $arrKakao["couponseq"] == "26"){ //마린 패키지
				$kakaoInfo = "\n ▶ 마린서프 오픈톡에 꼭 참여해주세요.\n    참여시 [이용일]+실명으로 입장해주세요 :)";
				$kakaolink = "\n    https://open.kakao.com/o/goYwKe5e\n";
			}else if($arrKakao["couponseq"] == "20" || $arrKakao["couponseq"] == "27"){ //인구 패키지
				$kakaoInfo = "\n ▶ 인구서프 오픈톡에 꼭 참여해주세요.\n    참여시 [이용일]+실명으로 입장해주세요 :)";
				$kakaolink = "\n    https://open.kakao.com/o/gf4LMe5e\n";
			}else if($arrKakao["couponseq"] == "21" || $arrKakao["couponseq"] == "28"){ //서팩 패키지
				$kakaoInfo = "\n ▶ 서프팩토리 오픈톡에 꼭 참여해주세요.\n    참여시 [이용일]+실명으로 입장해주세요 :)";
				$kakaolink = "\n    https://open.kakao.com/o/g58J34ff\n";
			}else if($arrKakao["couponseq"] == "22" || $arrKakao["couponseq"] == "29"){ //솔게하 패키지
				$kakaoInfo = "\n\n ▶ 힐링 서핑캠프 오픈톡에 꼭 참여해주세요.\n    참여시 [이용일]+실명으로 입장해주세요 :)";
				$kakaolink = "\n    https://open.kakao.com/o/g15tGdBf\n";
			}

			$kakaoMsg = $arrKakao["smsTitle"]
				.'\n\n안녕하세요. '.$arrKakao["userName"].'님'
				.'\n서핑버스를 예약해주셔서 감사합니다.'
				.'\n\n예약정보 [예약확정]'
				.'\n ▶ 예약상품 : '.$arrKakao["shopname"]
				//.'\n ▶ 예약번호 : '.$arrKakao["MainNumber"]
				.'\n ▶ 예약자 : '.$arrKakao["userName"]
				.'\n'.$arrKakao["msgInfo"]
				.$kakaoInfo.$kakaolink
				.'---------------------------------'
				.'\n ▶ 안내사항'
				.'\n      - 교통상황으로 인해 지연 도착할 수 있으니 양해부탁드립니다.'
				.'\n      - 이용일, 탑승위치 확인 및 탑승시간 10분전 도착 부탁드려요~';
				// .'\n      - 최소인원(20명) 모집이 안 될 경우 운행이 취소될 수 있습니다.'
				// .'\n      - 운행취소 시 이용일 3~4일 전 연락드립니다.';

		}

	}else if($arrKakao["tempName"] == "at_surf_step1"){ //서핑샵 예약확정
		$kakaoMsg = $arrKakao["smsTitle"]
			.'\n\n안녕하세요. '.$arrKakao["userName"].'님'
			.'\n액트립을 이용해주셔서 감사합니다.'
			.'\n\n'.$arrKakao["PROD_NAME"].' 예약정보 [입금완료]'
			.'\n ▶ 예약번호 : '.$arrKakao["MainNumber"]
			.'\n ▶ 예약자 : '.$arrKakao["userName"]
			.'\n ▶ 신청목록'
			.'\n'.$arrKakao["msgInfo"]
			.'---------------------------------'
			.'\n ▶ 안내사항'
			.'\n      - 예약하신건은 예약확정 후 이용가능합니다.'
			.'\n      - 예약건 매진으로 인하여 취소 될 수 있으니 참고부탁드립니다.';

	}else if($arrKakao["tempName"] == "at_bus_kakao"){ //타채널 알림톡발송
		if($arrKakao["PROD_TYPE"] == "bus_channel"){ //타채널 알림톡발송

			$kakaoInfo = "";
			$kakaolink = "";
			if($arrKakao["PROD_URL"] == "17" || $arrKakao["PROD_URL"] == "26"){ //마린 패키지
				$kakaoInfo = "\n\n ▶ 마린서프 오픈톡에 꼭 참여해주세요.\n    참여시 [이용일]+실명으로 입장해주세요 :)";
				$kakaolink = "\n    https://open.kakao.com/o/goYwKe5e";
			}else if($arrKakao["PROD_URL"] == "20" || $arrKakao["PROD_URL"] == "27"){ //인구 패키지
				$kakaoInfo = "\n\n ▶ 인구서프 오픈톡에 꼭 참여해주세요.\n    참여시 [이용일]+실명으로 입장해주세요 :)";
				$kakaolink = "\n    https://open.kakao.com/o/gf4LMe5e";
			}else if($arrKakao["PROD_URL"] == "21" || $arrKakao["PROD_URL"] == "28"){ //서팩 패키지
				$kakaoInfo = "\n\n ▶ 서프팩토리 오픈톡에 꼭 참여해주세요.\n    참여시 [이용일]+실명으로 입장해주세요 :)";
				$kakaolink = "\n    https://open.kakao.com/o/g58J34ff";
			}else if($arrKakao["PROD_URL"] == "22" || $arrKakao["PROD_URL"] == "29"){ //솔게하 패키지
				$kakaoInfo = "\n\n ▶ 힐링 서핑캠프 오픈톡에 꼭 참여해주세요.\n    참여시 [이용일]+실명으로 입장해주세요 :)";
				$kakaolink = "\n    https://open.kakao.com/o/g15tGdBf";
			}

			$kakaoMsg = $arrKakao["smsTitle"]
				.'\n\n안녕하세요. '.$arrKakao["userName"].'님'
				.'\n액트립을 이용해주셔서 감사합니다.'
				.'\n서핑버스 좌석/정류장 예약관련 안내드립니다.'
				.'\n\n예약정보 [예약대기]'
				.'\n ▶ 예약상품 : '.$arrKakao["shopname"]
				//.'\n ▶ 예약번호 : -'
				.'\n ▶ 예약자 : '.$arrKakao["userName"]
				.'\n ▶ 예약좌석'.$arrKakao["msgInfo"]
				.$kakaoInfo.$kakaolink
				.'\n---------------------------------'
				.'\n ▶ 안내사항'
				.'\n      - [예약하기] 클릭 후 좌석/정류장을 예약해주세요.'
				.'\n      - 잔여석이 없을 경우 예약이 취소 될 수 있으니 빠른 예약부탁드려요~'
				.'\n      - 최소인원(20명) 모집이 안 될 경우 운행이 취소될 수 있습니다.'
				.'\n      - 운행취소 시 이용일 3~4일 전 연락드립니다.';

		}else if($arrKakao["PROD_TYPE"] == "bus_push"){ // 타채널 재발송 안내
			$kakaoMsg = $arrKakao["smsTitle"]
				.'\n\n안녕하세요. '.$arrKakao["userName"].'님'
				.'\n카카오채널로 예약링크를 안내드렸으나 예약확정이 안되어 다시 한번 안내드립니다.'
				.'\n\n예약정보 [예약대기]'
				.'\n ▶ 예약상품 : '.$arrKakao["shopname"]
				.'\n ▶ 예약자 : '.$arrKakao["userName"]
				.'\n---------------------------------'
				.'\n ▶ 안내사항'
				.'\n      - [예약하기] 클릭 후 좌석/정류장을 예약해주세요.'
				.'\n      - 잔여석이 없을 경우 예약이 취소 될 수 있으니 빠른 예약부탁드려요~'
				.'\n\n고객님들 모두 불편함 없는 즐거운 주말 여행이 되시길 바랍니다.'
				.'\n\n감사합니다~';

		}
	}

	return $kakaoMsg;
}

function kakaoMsg($arrKakao){
	$Url = "https://actrip.co.kr/";

	$btn_ResSearch = '{"type":"WL","name":"예약조회","url_mobile":"https://actrip.co.kr/'.$arrKakao["btn_ResSearch"].'"}';
	$btn_ResChange = '{"type":"WL","name":"좌석/정류장 변경","url_mobile":"https://actrip.co.kr/'.$arrKakao["btn_ResChange"].'"}';
	$btn_ResPoint = '{"type":"WL","name":"탑승시간/위치 안내","url_mobile":"https://actrip.co.kr/'.$arrKakao["btn_ResPoint"].'"}';
	$btn_ResGPS = '{"type":"WL","name":"셔틀버스 실시간위치 조회","url_mobile":"https://actrip.co.kr/'.$arrKakao["btn_ResGPS"].'"}';

	$btnList = "";
    if($arrKakao["tempName"] == "at_surf_step3"){ //솔게하 알림톡 발송
        $btnList = '"button1":{"type":"WL","name":"[필독]예약 상세안내","url_mobile":"'.$Url.$arrKakao["link1"].'"},'
			.'"button2":{"type":"WL","name":"위치안내","url_mobile":"'.$Url.$arrKakao["link2"].'"},'
			.'"button3":{"type":"WL","name":"이벤트&공지","url_mobile":"'.$Url.$arrKakao["link3"].'"},';

	}else if($arrKakao["tempName"] == "frip_bus03"){ //셔틀버스 입금대기
        $btnList = '"button1":'.$btn_ResSearch
			.',"button2":'.$btn_ResChange
			.',"button3":'.$btn_ResPoint.',';

	}else if($arrKakao["tempName"] == "frip_bus02"){ //셔틀버스 예약확정
        $btnList = '"button1":'.$btn_ResSearch
			.',"button2":'.$btn_ResChange
			.',"button3":'.$btn_ResPoint
			.',"button4":'.$btn_ResGPS.',';

	}else if($arrKakao["tempName"] == "at_bus_kakao"){ //타채녈 셔틀버스 예약안내
		$btnList = '"button1":{"type":"WL","name":"예약하기","url_mobile":"https://actrip.co.kr/'.$arrKakao["link1"].'"},';
	}

	$arryKakao = '';
	if($arrKakao["array"] == "true"){
		foreach($arrKakao["arryData"] as $item) {
			$arrKakao["kakaoMsg"] = kakaoContent($item); //카카오 메시지 내용

			$btn_ResSearch = '{"type":"WL","name":"예약조회","url_mobile":"https://actrip.co.kr/'.$item["btn_ResSearch"].'"}';
			$btn_ResChange = '{"type":"WL","name":"좌석/정류장 변경","url_mobile":"https://actrip.co.kr/'.$item["btn_ResChange"].'"}';
			$btn_ResPoint = '{"type":"WL","name":"탑승시간/위치 안내","url_mobile":"https://actrip.co.kr/'.$item["btn_ResPoint"].'"}';
			$btn_ResGPS = '{"type":"WL","name":"셔틀버스 실시간위치 조회","url_mobile":"https://actrip.co.kr/'.$item["btn_ResGPS"].'"}';

			if($item["tempName"] == "frip_bus02"){ //셔틀버스 예약확정
				$btnList = '"button1":'.$btn_ResSearch
					.',"button2":'.$btn_ResChange
					.',"button3":'.$btn_ResPoint
					.',"button4":'.$btn_ResGPS.',';
			}
		
			//문자발송용 변수
			$msgSmsBtn = $arrKakao["kakaoMsg"].'\n\n ▶ 문의 : http://pf.kakao.com/_HxmtMxl';

			$arryKakao .= '{"message_type":"at","phn":"82'.substr(str_replace('-', '',$item["userPhone"]), 1).'","profile":'.rtnProfile("actrip").',"tmplId":"'.$item["tempName"].'","msg":"'.$arrKakao["kakaoMsg"].'",'.$btnList.'"smsKind":"L","msgSms":"'.$msgSmsBtn.'","smsSender":"'.str_replace('-', '',$item["userPhone"]).'","smsLmsTit":"'.$item["smsTitle"].'","smsOnly":"'.$item["smsOnly"].'"}';

			if( next( $arrKakao["arryData"] ) ) {
				$arryKakao .= ",";
			}
		}

		$arryKakao = '['.$arryKakao.']';
	}else{
		$arrKakao["kakaoMsg"] = kakaoContent($arrKakao); //카카오 메시지 내용
	
		//문자발송용 변수
		$msgSmsBtn = $arrKakao["kakaoMsg"].'\n\n ▶ 문의 : http://pf.kakao.com/_HxmtMxl';

		$arryKakao = '[{
			"message_type":"at"
			,"phn":"82'.substr(str_replace('-', '',$arrKakao["userPhone"]), 1).'"
			,"profile":'.rtnProfile("actrip").'
			,"tmplId":"'.$arrKakao["tempName"].'"
			,"msg":"'.$arrKakao["kakaoMsg"].'"
			,'.$btnList.'"smsKind":"L"
			,"msgSms":"'.$msgSmsBtn.'"
			,"smsSender":"'.str_replace('-', '',$arrKakao["userPhone"]).'"
			,"smsLmsTit":"'.$arrKakao["smsTitle"].'"
			,"smsOnly":"'.$arrKakao["smsOnly"].'"
		}]';
	}
    
    return $arryKakao;
}

function kakaoDebug($arrKakao, $arrRtn){	
	$datetime = date('Y/m/d H:i'); 
	$shopseq = $arrKakao["PROD_URL"];
	if($shopseq == 7){ //서핑버스 양양
		$resparam = "surfbus_yy";
	}else if($shopseq == 14){  //서핑버스 동해
		$resparam = "surfbus_dh";
	}else{ //서핑샵
		$resparam = "surfview?seq=".$shopseq;
	}

	$PROD_NAME = $arrKakao["PROD_NAME"];
	$PROD_URL = "https://actrip.co.kr/".$resparam;
	$USER_NAME = $arrKakao["userName"];
	$USER_TEL = $arrKakao["userPhone"];
	$PROD_TYPE = $arrKakao["PROD_TYPE"];
	$KAKAO_DATE = $datetime;
	$RES_CONFIRM = $arrKakao["RES_CONFIRM"];
	$KAKAO_CONTENT = kakaoContent($arrKakao); //카카오 메시지 내용
	
	$KAKAO_BTN1 = "";
	$KAKAO_BTN2 = "";
	$KAKAO_BTN3 = "";
	$KAKAO_BTN4 = "";
	$KAKAO_BTN5 = "";

	if($arrKakao["tempName"] == "at_surf_step3"){ //솔게하 알림톡 발송
		$KAKAO_BTN1 = $arrKakao["link1"];
		$KAKAO_BTN2 = $arrKakao["link2"];
		$KAKAO_BTN3 = $arrKakao["link3"];

	}else if($arrKakao["tempName"] == "frip_bus02" || $arrKakao["tempName"] == "frip_bus03"){ //셔틀버스 예약확정, 입금대기
		$KAKAO_BTN1 = $arrKakao["btn_ResSearch"];
		$KAKAO_BTN2 = $arrKakao["btn_ResChange"];
		$KAKAO_BTN3 = $arrKakao["btn_ResPoint"];

	}else if($arrKakao["tempName"] == "at_bus_kakao"){ //타채녈 셔틀버스 예약안내
		$KAKAO_BTN1 = $arrKakao["link1"];

	}

	if($arrKakao["tempName"] == "frip_bus02"){ //셔틀버스 예약확정
		$KAKAO_BTN4 = $arrKakao["btn_ResGPS"];
	}

	if($PROD_TYPE == "bus_kakaoinfo"){
		$PROD_URL = "";
		$PROD_NAME = "서핑버스 안내발송";
	}else if($PROD_TYPE == "bus_kakaoconfirm"){
		$PROD_URL = "";
		$PROD_NAME = "서핑버스 확정 안내발송";
	}else if($PROD_TYPE == "bus_cancelinfo"){
		$PROD_URL = "";
		$PROD_NAME = "서핑버스 취소안내";
	}

	return "INSERT INTO `AT_KAKAO_HISTORY`(`PROD_NAME`, `PROD_URL`, `USER_NAME`, `USER_TEL`, `PROD_TYPE`, `KAKAO_DATE`, `RES_CONFIRM`, `KAKAO_CONTENT`, `KAKAO_BTN1`, `KAKAO_BTN2`, `KAKAO_BTN3`, `KAKAO_BTN4`, `KAKAO_BTN5`, `response`, `err`) VALUES ('$PROD_NAME','$PROD_URL','$USER_NAME','$USER_TEL','$PROD_TYPE','$KAKAO_DATE',$RES_CONFIRM,'$KAKAO_CONTENT','$KAKAO_BTN1','$KAKAO_BTN2','$KAKAO_BTN3','$KAKAO_BTN4','$KAKAO_BTN5', '$arrRtn[0]', '$arrRtn[1]');";
}

function mailMsg($arrMail){
	$gubun = $arrMail["gubun"];
	$gubun_step = $arrMail["gubun_step"];
	$gubun_title = $arrMail["gubun_title"];

	$userName = $arrMail["userName"];
	$userPhone = $arrMail["userPhone"];
	$ResNumber = $arrMail["ResNumber"];
	$etc = $arrMail["etc"];
	$totalPrice1 = $arrMail["totalPrice1"];
	$totalPrice2 = $arrMail["totalPrice2"];
	$totalPrice2_display = "display:none;";
	$banknum = $arrMail["banknum"];

	$info1_title = $arrMail["info1_title"];
	$info1 = $arrMail["info1"];
	$info2_title = $arrMail["info2_title"];
	$info2 = $arrMail["info2"];
	$info2_display = "display:none;"; //탑승시간 안내
	$info3 = "입금계좌";
	$info3_display = "display:none;"; //입금계좌 안내
	$info4_display = "display:none;"; //추가정보 항목
	$totalinfo = "입금금액";

	$gubun_title1 = $gubun_title."를(을) 예약해 주셔서 진심으로 감사드립니다.";
	$gubun_title2 = "아래의 예약정보 내역확인 후 이용 부탁드립니다.";
	$gubun_title3 = "예약정보";
	$info5_display = ""; //기본정보

	$gubun_subtitle = " 예약안내";
	if($gubun == "surf" || $gubun == "bus"){
		if($gubun_step == 0){ //미입금 - 입금대기
			$info3_display = "";
			$info4_display = "";
		}else if($gubun_step == 2){ //임시확정/취소
			$info2_display = "";
		}else if($gubun_step == 3){ //확정
			$info2_display = "";
		}else if($gubun_step == 4){ //환불요청
			$gubun_subtitle = " 환불요청안내";
			$gubun_title1 = $gubun_title."를(을) 환불요청하셨습니다.";
			$gubun_title2 = "아래의 환불요청 내역 확인 부탁드립니다.";
			$gubun_title3 = "환불요청 정보";

			$info3 = "환불계좌";
			$info3_display = "";
			$info4_display = "";
			$totalPrice2_display = "";
			$totalinfo = "환불금액";
		}else if($gubun_step == 8){ //입금완료
			$info2_display = "";
		}else if($gubun_step == 9){ //정류장 변경
			if($info2_title != ""){
				$info2_display = "";
			}
		}
	}else if($gubun == "bank"){
		if($gubun_step == 0){
			$gubun_subtitle = " : 동일 이름, 금액건 발생";
		}else if($gubun_step == 1){
			$gubun_subtitle = " : 동일 금액건 발생";
		}else if($gubun_step == 2){
			$gubun_subtitle = " : 동일 이름건 발생";
		}else if($gubun_step == 3){
			$gubun_subtitle = " : 매칭내역 없음";
		}
		$gubun_title1 = "예약건 매칭오류가 발생하였습니다.";
		$gubun_title2 = $etc;
		$info5_display = "display:none;"; //기본정보
	}
	
	$gubun_title .= $gubun_subtitle;

	$returnMsg = "<table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"width:100%;background-color:#f8f8f9;letter-spacing:-1px\">
		<tbody>
			<tr>
				<td align=\"center\">
					<div style=\"max-width:595px; margin:0 auto\">
						<table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"max-width:595px;width:100%;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;background-color:#fff;-webkit-text-size-adjust:100%;text-align:left\">
							<tbody>
								<tr>
									<td height=\"30\"></td>
								</tr>
								<tr>
									<td style=\"padding-right:27px; padding-left:21px\">
										<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
											<tbody>
												<tr>
													<td style=\"\" width=\"61\"> <a href=\"https://actrip.co.kr\" target=\"_blank\"><img src=\"https://actrip.cdn1.cafe24.com/logo/weblogo01.png\"></a> </td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
								<tr>
									<td height=\"13\"></td>
								</tr>
								<tr>
									<td style=\"padding-right:27px; padding-left:18px;line-height:38px;font-size:29px;color:#424240;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\"> 액트립 예약안내 메일입니다.<br><span style=\"color:#1ec800\">$gubun_title</span> </td>
								</tr>
								<tr>
									<td height=\"22\"></td>
								</tr>
								<tr>
									<td height=\"1\" style=\"background-color: #e5e5e5;\"></td>
								</tr>
								<tr>
									<td style=\"padding-top:24px; padding-right:27px; padding-bottom:32px; padding-left:20px\">
										<table align=\"left\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\">
											<tbody>											
												<tr>
													<td style=\"font-size:14px;color:#696969;line-height:30px;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\"> 안녕하세요. <span style=\"color:#009E25\">$userName</span> 고객님. 
													<br><strong> $gubun_title1</strong><br>
													$gubun_title2 </td>
												</tr>
												<tr style=\"$info5_display\">
													<td height=\"24\"></td>
												</tr>
												<tr style=\"$info5_display\">
													<td style=\"font-size:14px;color:#696969;line-height:24px;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\">
														<table cellpadding=\"0\" cellspacing=\"0\" style=\"width:100%;margin:0;padding:0\">
															<tbody>
																<tr>
																	<td height=\"23\" style=\"font-weight:bold;color:#000;vertical-align:top;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\"> $gubun_title3 </td>
																</tr>
																<tr>
																	<td height=\"2\" style=\"background:#424240\"></td>
																</tr>
																<tr>
																	<td height=\"20\"></td>
																</tr>
																<tr>
																	<td>
																		<table cellpadding=\"0\" cellspacing=\"0\" style=\"width:100%;margin:0;padding:0\">
																			<tbody>
																				<tr>
																					<td width=\"105\" style=\"padding-bottom:5px;color:#696969;line-height:23px;vertical-align:top;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\"> 예 약 자 </td>
																					<td style=\"padding-bottom:5px;color:#000;line-height:23px;vertical-align:top;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\"> $userName ($userPhone) </td>
																				</tr>
																				<tr>
																					<td style=\"padding-bottom:5px;color:#696969;line-height:23px;vertical-align:top;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\"> 예약번호 </td>
																					<td style=\"padding-bottom:5px;;color:#000;line-height:23px;vertical-align:top;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\"> $ResNumber </td>
																				</tr>
																				<tr>
																					<td style=\"padding-bottom:5px;color:#696969;line-height:23px;vertical-align:top;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\"> $info1_title </td>
																					<td style=\"padding-bottom:5px;;color:#000;line-height:23px;vertical-align:top;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\">$info1</td>
																				</tr>
																				<tr style=\"$info2_display\">
																					<td style=\"padding-bottom:5px;color:#696969;line-height:23px;vertical-align:top;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\"> $info2_title </td>
																					<td style=\"padding-bottom:5px;;color:#000;line-height:23px;vertical-align:top;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\">$info2</td>
																				</tr>
																				<tr>
																					<td style=\"padding-bottom:5px;color:#696969;line-height:23px;vertical-align:top;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\"> 요청사항 </td>
																					<td style=\"padding-bottom:5px;padding-top:5px;color:#000;line-height:23px;vertical-align:top;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\"><textarea cols=\"42\" disabled=\"\" rows=\"8\" name=\"etc\" style=\"margin: 0px; width: 97%; height: 80px; resize: none;\">$etc</textarea></td>
																				</tr>
																			</tbody>
																		</table>
																	</td>
																</tr>
																<tr>
																	<td height=\"10\"></td>
																</tr>
																<tr>
																	<td height=\"1\" style=\"background:#d5d5d5\"></td>
																</tr>
															</tbody>
														</table>
													</td>
												</tr>
												<tr style=\"$info4_display\">
													<td height=\"24\"></td>
												</tr>
												<tr style=\"$info4_display\">
													<td style=\"font-size:14px;color:#696969;line-height:24px;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\">
														<table cellpadding=\"0\" cellspacing=\"0\" style=\"width:100%;margin:0;padding:0\">
															<tbody>
																<tr>
																	<td height=\"23\" style=\"font-weight:bold;color:#000;vertical-align:top;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\"> 추가정보 </td>
																</tr>
																<tr>
																	<td height=\"2\" style=\"background:#424240\"></td>
																</tr>
																<tr>
																	<td height=\"20\"></td>
																</tr>
																<tr>
																	<td>
																		<table cellpadding=\"0\" cellspacing=\"0\" style=\"width:100%;margin:0;padding:0\">
																			<tbody>
																				<tr style=\"$info3_display\">
																					<td width=\"105\" style=\"padding-bottom:5px;color:#696969;line-height:23px;vertical-align:top;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\"> $info3</td>
																					<td style=\"padding-bottom:5px;color:#000;line-height:23px;vertical-align:top;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\">$banknum </td>
																				</tr>
																				<tr>
																					<td width=\"105\" style=\"padding-bottom:5px;color:#696969;line-height:23px;vertical-align:top;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\">$totalinfo</td>
																					<td style=\"padding-bottom:5px;;color:#000;line-height:23px;vertical-align:top;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\">$totalPrice1 <span style=\"font-size:12px;$totalPrice2_display\">$totalPrice2</span></td>
																				</tr>
																			</tbody>
																		</table>
																	</td>
																</tr>
																<tr>
																	<td height=\"10\"></td>
																</tr>
																<tr>
																	<td height=\"1\" style=\"background:#d5d5d5\"></td>
																</tr>
															</tbody>
														</table>
													</td>
												</tr>
												<tr style=\"$info5_display\">
													<td height=\"24\"></td>
												</tr>
												<tr style=\"$info5_display\">
													<td style=\"font-size:14px;color:#696969;line-height:24px;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\"> <strong>예약관련 문의사항은 <a href=\"https://pf.kakao.com/_HxmtMxl\" target=\"_blank\" style=\"text-decoration:underline;color:#009e25\" rel=\"noreferrer noopener\">카카오톡 채널</a>을 이용해주세요.</strong> </td>
												</tr>
												<tr style=\"$info5_display\">
													<td style=\"font-size:14px;color:#696969;line-height:24px;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;\">더욱 편리한 서비스를 제공하기 위해 항상 최선을 다하겠습니다. </td>
												</tr>
												<tr style=\"$info5_display\">
													<td style=\"height:34px;font-size:14px;color:#696969;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;text-align:center;\"> <a href=\"https://actrip.co.kr/orderview?num=1&resNumber=$ResNumber\" style=\"display:inline-block;padding:10px 30px 10px; margin-top:10px; background-color:#08a600; color:#fff;text-align: center; text-decoration: none;\" target=\"_blank\" rel=\"noreferrer noopener\">예약조회</a></td>
												</tr>
												
											</tbody>
										</table>
									</td>
								</tr>
								<tr>
									<td style=\"padding-top:26px;padding-left:21px;padding-right:21px;padding-bottom:13px;background:#f9f9f9;font-size:12px;font-family:'나눔고딕',NanumGothic,'맑은고딕',Malgun Gothic,'돋움',Dotum,Helvetica,'Apple SD Gothic Neo',Sans-serif;color:#696969;line-height:17px\"> 본 메일은 발신전용 메일이므로 회신되지 않습니다.<br>액트립 서비스관련 궁금하신 사항은  <a href=\"https://pf.kakao.com/_HxmtMxl\" style=\"color:#696969;font-weight:bold;text-decoration:underline\" rel=\"noreferrer noopener\" target=\"_blank\">카카오톡 채널</a>에서 확인해주세요. </td>
								</tr>
								<tr>
									<td style=\"padding-left:21px;padding-right:21px;padding-bottom:57px;background:#f9f9f9;font-size:12px;font-family:Helvetica;color:#696969;line-height:17px\"> Copyright ⓒ <strong>ACTRIP</strong> Corp. All Rights Reserved. </td>
								</tr>
							</tbody>
						</table>
					</div>
				</td>
			</tr>
		</tbody>
	</table>";

	return $returnMsg;
}

function sendMail($arrMail){
	$admin_email = $arrMail["mailfrom"];
	$admin_name  = $arrMail["mailname"];
	$mailto = $arrMail["mailto"];
	$gubun = $arrMail["gubun"];
	$gubun_step = $arrMail["gubun_step"];
	$gubun_title = $arrMail["gubun_title"];

	$header  = "Return-Path: ".$admin_email."\n";
	$header .= "From: =?EUC-KR?B?".base64_encode($admin_name)."?= <".$admin_email.">\n";
	$header .= "MIME-Version: 1.0\n";
	$header .= "X-Priority: 3\n";
	$header .= "X-MSMail-Priority: Normal\n";
	$header .= "X-Mailer: FormMailer\n";
	$header .= "Content-Transfer-Encoding: base64\n";
	$header .= "Content-Type: text/html;\n \tcharset=UTF-8\n";

	if($gubun == "surf" || $gubun == "bus"){
		if($gubun_step == 0){ //미입금 - 입금대기
			$state_title = "입금대기";
		}else if($gubun_step == 1){ //예약대기
			$state_title = "예약대기";
		}else if($gubun_step == 2){ //임시확정
			$state_title = "임시확정/취소";
		}else if($gubun_step == 3){ //확정
			$state_title = "예약확정";
		}else if($gubun_step == 4){ //환불요청
			$state_title = "환불요청";
		}else if($gubun_step == 6){ //임시취소
			$state_title = "임시취소";
		}else if($gubun_step == 8){ //입금완료
			$state_title = "입금완료";
		}else if($gubun_step == 9){ //정류장 변경
			$state_title = "정류장 변경";
		}
		$state_title .= " (".$arrMail["userName"].")";
	}else if($gubun == "bank"){
		if($gubun_step == 0){
			$state_title = "동일 금액, 예약건 발생";
		}else if($gubun_step == 1){
			$state_title = "동일 금액건 발생";
		}else if($gubun_step == 2){
			$state_title = "동일 이름건 발생";
		}else if($gubun_step == 3){
			$state_title = "매칭내역 없음";
		}
	}

	$subject = "[액트립] ".$gubun_title." 예약안내 - ".$state_title;

	$message = base64_encode(mailMsg($arrMail));
	flush();
	@mail($mailto, '=?UTF-8?B?'.base64_encode($subject).'?=', $message, $header);
}

function fnMessageText($code){
	switch ($code) {
		case 'K000':
			$text = "카카오 비즈메시지 발송 성공";
			break;
		case 'M000':
			$text = "SMS/LMS 발송 성공";
			break;
		case 'M001':
			$text = "SMS 발송 처리 중";
			break;
		case 'M107':
			$text = "미등록된 SMS 발신번호";
			break;
		case 'K208':
			$text = "링크버튼 형식 오류 (잘못된 파라메터 요청)";
			break;
		case 'K102':
			$text = "전화번호 오류";
			break;
		case 'K105':
			$text = "메시지 내용이 템플릿과 일치하지 않음";
			break;
		case 'E104':
			$text = "유효하지 않은 사용자 전화번호";
			break;
		case 'E110':
			$text = "MsgId를 찾을 수 없음";
			break;
		default:
			$text = $code;
			break;
	}

	return $text;
}

function getKakaoSearch($msgid){
	$params = array('profile'=>rtnProfile("actrip"), 'msgid'=>$msgid);
	$query = http_build_query($params);
	$api_server = "https://alimtalk-api.bizmsg.kr/v2/sender/report";

	$opts = array(
		CURLOPT_URL => $api_server . '?' . $query,
		CURLOPT_HEADER => true,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_HTTPHEADER => array(
			"userId: surfenjoy"
		),
	);

	// 응답요청
	$curl_session = curl_init();
	curl_setopt_array($curl_session, $opts);
	$curl_response = curl_exec($curl_session);
	$resMessage = (curl_error($curl_session))? null : $curl_response;

	if($resMessage == null){
		return '{"code":"no"}';
	}else{
		return substr($resMessage, strpos($resMessage, "code") - 2);
	}
}
?>