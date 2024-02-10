<?php
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/kakaoalim.php';
include __DIR__.'/../../common/func.php';

$success = true;
$datetime = date('Y/m/d H:i'); 

$param = $_REQUEST["resparam"];
$InsUserID = $_REQUEST["userid"];

$intseq = "";
$intseq3 = "";
//$to = "lud1@naver.com";
$to = "lud1@naver.com";

mysqli_query($conn, "SET AUTOCOMMIT=0");
mysqli_query($conn, "BEGIN");

if($param == "reskakaodel"){ //예약건 삭제
    $codeseq = $_REQUEST["codeseq"];

	$select_query = "DELETE FROM AT_COUPON_CODE WHERE codeseq = $codeseq";
	$result_set = mysqli_query($conn, $select_query);
	if(!$result_set) goto errGo;

	$select_query = "DELETE FROM AT_RES_TEMP WHERE codeseq = $codeseq";
	$result_set = mysqli_query($conn, $select_query);
	if(!$result_set) goto errGo;
	
	mysqli_query($conn, "COMMIT");
	
}else if($param == "reskakaode2"){ //버스 예약안내 카톡 재발송 : 타채널예약건
	$resnum = $_REQUEST['resnum'];

	$select_query = "SELECT A.*, B.couponseq, B.etc
						FROM AT_RES_TEMP AS A INNER JOIN AT_COUPON_CODE AS B
								ON A.codeseq = B.codeseq
						WHERE resnum = '$resnum'";
	$result = mysqli_query($conn, $select_query);
	$rowMain = mysqli_fetch_array($result);

	$resbus = $rowMain["bus_line"]; //행선지
	$userName = $rowMain["user_name"]; //이름
	$userPhone = $rowMain["user_phone"];  //연락처
	$couponseq = $rowMain["couponseq"]; //채널
	
    $start_bus_gubun = $rowMain["start_bus_gubun"]; //출발노선
    $return_bus_gubun = $rowMain["return_bus_gubun"]; //복귀노선

    $start_day = $rowMain["start_day"];
    $return_day = $rowMain["return_day"];
    $start_cnt = $rowMain["start_cnt"];
    $return_cnt = $rowMain["return_cnt"];

	if($resbus == "YY"){ //양양행
		$busTitleName = "양양";
	}else{ //동해행
		$busTitleName = "동해";
	}

	if($start_cnt > 0 && $return_cnt > 0){ //왕복
		$bus_line = "서울 ↔ $busTitleName";
	}else if($start_cnt > 0){ //서울출발
		$bus_line = "서울 → $busTitleName";
	}else{ //서울복귀
		$bus_line = "$busTitleName → 서울";
	}
	
	if($start_cnt == 0){
		$start_bus_gubun = "";
		$start_day = "";
		$day_start = "-";
	}else{
		if($start_bus_gubun == "SA"){
			$bus_gubun = "사당선";
		}else if($start_bus_gubun == "JO"){
			$bus_gubun = "종로선";
		}else {
			$bus_gubun = "출발";
		}
		$day_start = "[$start_day] $bus_gubun $start_cnt"."자리";
	}
	
	if($return_cnt == 0){
		$return_bus_gubun = "";
		$return_day = "";
		$day_return = "-";
	}else{		
		if($return_bus_gubun == "AM"){
			$bus_gubun = "오후";
		}else if($return_bus_gubun == "AM"){
			$bus_gubun = "저녁";
		}else{
			$bus_gubun = "복귀";
		}

		$day_return = "[$return_day] $bus_gubun $return_cnt"."자리";
	}
	$link1 = $rowMain["etc"];

	//==========================카카오 메시지 발송 ==========================
	$msgTitle = '액트립 셔틀버스 예약안내';
	$DebugInfo = array(
		"PROD_NAME" => "셔틀버스 타채널예약 재발송"
		, "PROD_TABLE" => "AT_RES_TEMP"
		, "PROD_TYPE" => "busseat_stay_re"
		, "RES_CONFIRM" => "-1"
		, "resnum" => $resnum //쿠폰코드 seq
	);
	
	//쿠폰사용 업체명
	$arrCoupon = fnCouponCode($couponseq);

	$arrKakao = array(
		"gubun"=> "bus_channel"
		, "userName"=> $userName
		, "userPhone"=> $userPhone
		, "bus_line"=> $bus_line
		, "day_start"=> $day_start //출발일
		, "day_return"=> $day_return //복귀일
		, "sub_name"=> $arrCoupon["name"] //업체명
		, "sub_prod"=> $arrCoupon["prod_name"] //상품명
		, "link1"=> $link1 //예약
		, "DebugInfo"=> $DebugInfo
	);	

	$arryKakao[0] = $arrKakao;

	$arrKakao = array(
		"arryData"=> $arryKakao
		, "array"=> "true" //배열 여부
		, "tempName"=> "actrip_info02" //템플릿 코드
		, "title"=> $msgTitle //타이틀
		, "smsOnly"=> "N" //문자발송 여부
	);

	$arrRtn = sendKakao($arrKakao); //알림톡 발송

	$data = json_decode($arrRtn[0], true);

	for ($i=0; $i < count($data); $i++) { 
		//------- 알림톡 디버깅 -----
		$code = $data[$i]["code"];
		$msgid = $data[$i]["data"]["msgid"];
		$message = $data[$i]["message"];
		$originMessage = $data[$i]["originMessage"];
		
		$kakao_response = array(
			"arrKakao"=> $arrKakao
			, "item"=> $arryKakao[$i]
			, "code"=> $code
			, "msgid"=> $msgid
			, "message"=> $message
			, "originMessage"=> $originMessage
		);

		// 카카오 알림톡 DB 저장 START
		$select_query = kakaoDebug2024($kakao_response, json_encode($data[$i]), "U");
		$result_set = mysqli_query($conn, $select_query);
		// 카카오 알림톡 DB 저장 END
		
		$errmsg = $select_query;
		if(!$result_set) goto errGo;

		$select_query = "UPDATE `AT_RES_TEMP` SET kakao_cnt = kakao_cnt + 1, insdate = now() WHERE resnum = '$resnum'";
		$result_set = mysqli_query($conn, $select_query);

		$errmsg = $select_query;
		if(!$result_set) goto errGo;
	}
	//--------------------------------------

	mysqli_query($conn, "COMMIT");
	
}else if($param == "reskakao"){ //버스 예약안내 카톡 : 타채널예약건
    $resbus = $_REQUEST["resbus"]; //행선지
    $userName = $_REQUEST["username"]; //이름
    $userPhone = str_replace('-', '', $_REQUEST["userphone"]); //연락처
    $couponseq = $_REQUEST["reschannel"]; //채널
	
    $start_bus_gubun = $_REQUEST["start_bus_gubun"]; //출발노선
    $return_bus_gubun = $_REQUEST["return_bus_gubun"]; //복귀노선

    $start_day = $_REQUEST["start_day"];
    $return_day = $_REQUEST["return_day"];
    $start_cnt = $_REQUEST["start_cnt"];
    $return_cnt = $_REQUEST["return_cnt"];

	//쿠폰코드 생성
	$coupon_code = RandString(5);
	$user_ip = $_SERVER['REMOTE_ADDR'];

	if($resbus == "YY"){ //양양행
		$busTitleName = "양양";
	}else{ //동해행
		$busTitleName = "동해";
	}

	if($start_cnt > 0 && $return_cnt > 0){ //왕복
		$bus_line = "서울 ↔ $busTitleName";
	}else if($start_cnt > 0){ //서울출발
		$bus_line = "서울 → $busTitleName";
	}else{ //서울복귀
		$bus_line = "$busTitleName → 서울";
	}
	
	$couponDate = "";
	if($start_cnt == 0){
		$start_bus_gubun = "";
		$start_day = "";
		$day_start = "-";
	}else{
		if($start_bus_gubun == "SA"){
			$bus_gubun = "사당선";
		}else if($start_bus_gubun == "JO"){
			$bus_gubun = "종로선";
		}else if($start_bus_gubun == "ALL"){
			$bus_gubun = "출발";
		}
		$day_start = "[$start_day] $bus_gubun $start_cnt"."자리";
		
		$add_date = $start_day;
	}
	
	if($return_cnt == 0){
		$return_bus_gubun = "";
		$return_day = "";
		$day_return = "-";
	}else{		
		if($return_bus_gubun == "AM"){
			$bus_gubun = "오후";
		}else if($start_bus_gubun == "PM"){
			$bus_gubun = "저녁";
		}else if($start_bus_gubun == "ALL"){
			$bus_gubun = "복귀";
		}

		$day_return = "[$return_day] $bus_gubun $return_cnt"."자리";

		$add_date = $return_day;
	}

    $select_query = "UPDATE AT_COUPON_CODE 
                        SET use_yn = 'Y'
                        ,user_ip = '$user_ip'
                        ,use_date = now()
                    WHERE (add_date < '$add_date' AND add_date IS NOT NULL) AND use_yn = 'N';";
    $result_set = mysqli_query($conn, $select_query);
	if(!$result_set) goto errGo;
	
	//------- 쿠폰코드 입력 -----
	$select_query = "INSERT INTO `AT_COUPON_CODE` (`couponseq`, `coupon_code`, `seq`, `use_yn`, `add_ip`, `add_date`, `insdate`, `userinfo`, `etc`) VALUES ('$couponseq', '$coupon_code', 'BUS', 'N', '$user_ip', '$add_date', now(), '$userinfo', '');";
	$result_set = mysqli_query($conn, $select_query);
	$seq = mysqli_insert_id($conn); //seq값
 	if(!$result_set) goto errGo;
	
	$link1 = shortURL("https://actrip.co.kr/surfbus_res?param=".urlencode(encrypt("$resbus|$seq|resbus")));
	//------- 쿠폰코드 입력 -----
	
	//------- 타채널 데이터 입력 -----
	$select_query = "INSERT INTO `AT_RES_TEMP` (`codeseq`, `bus_line`, `user_name`, `user_phone`, `start_bus_gubun`, `start_day`, `start_cnt`, `return_bus_gubun`, `return_day`, `return_cnt`, `resnum`, `insdate`) VALUES ($seq, '$resbus', '$userName', '$userPhone', '$start_bus_gubun', '$start_day', '$start_cnt', '$return_bus_gubun', '$return_day', '$return_cnt', 'code_$seq', now());";
	$result_set = mysqli_query($conn, $select_query);
 	if(!$result_set) goto errGo;
	 //------- 타채널 데이터 입력 -----

	 $select_query = "UPDATE `AT_COUPON_CODE` SET etc = '$link1' WHERE codeseq = $seq";
	 $result_set = mysqli_query($conn, $select_query);

	//==========================카카오 메시지 발송 ==========================
	$msgTitle = '액트립 셔틀버스 예약안내';
	$DebugInfo = array(
		"PROD_NAME" => "셔틀버스 타채널예약"
		, "PROD_TABLE" => "AT_RES_TEMP"
		, "PROD_TYPE" => "busseat_stay"
		, "RES_CONFIRM" => "-1"
		, "resnum" => "code_".$seq //쿠폰코드 seq
	);
	
	//쿠폰사용 업체명
	$arrCoupon = fnCouponCode($couponseq);

	$arrKakao = array(
		"gubun"=> "bus_channel"
		, "userName"=> $userName
		, "userPhone"=> $userPhone
		, "bus_line"=> $bus_line
		, "day_start"=> $day_start //출발일
		, "day_return"=> $day_return //복귀일
		, "sub_name"=> $arrCoupon["name"] //업체명
		, "sub_prod"=> $arrCoupon["prod_name"] //상품명
		, "link1"=> $link1 //예약
		, "DebugInfo"=> $DebugInfo
	);	

	$arryKakao[0] = $arrKakao;

	$arrKakao = array(
		"arryData"=> $arryKakao
		, "array"=> "true" //배열 여부
		, "tempName"=> "actrip_info02" //템플릿 코드
		, "title"=> $msgTitle //타이틀
		, "smsOnly"=> "N" //문자발송 여부
	);

	$arrRtn = sendKakao($arrKakao); //알림톡 발송

	$data = json_decode($arrRtn[0], true);

	for ($i=0; $i < count($data); $i++) { 
		//------- 알림톡 디버깅 -----
		$code = $data[$i]["code"];
		$msgid = $data[$i]["data"]["msgid"];
		$message = $data[$i]["message"];
		$originMessage = $data[$i]["originMessage"];
		
		$kakao_response = array(
			"arrKakao"=> $arrKakao
			, "item"=> $arryKakao[$i]
			, "code"=> $code
			, "msgid"=> $msgid
			, "message"=> $message
			, "originMessage"=> $originMessage
		);

		// 카카오 알림톡 DB 저장 START
		$select_query = kakaoDebug2024($kakao_response, json_encode($data[$i]));
		$result_set = mysqli_query($conn, $select_query);
		// 카카오 알림톡 DB 저장 END

		$errmsg = $select_query;
		if(!$result_set) goto errGo;
	}
	//--------------------------------------

   mysqli_query($conn, "COMMIT");
}

if(!$success){
	errGo:
	mysqli_query($conn, "ROLLBACK");
	echo 'err';
}else{
	echo '0';
}

mysqli_close($conn);
?>
