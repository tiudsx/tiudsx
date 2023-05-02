<?
//요일 표시
function fnWeek($date){
    $week = array("일","월","화","수","목","금","토");
    return $week[date('w', strtotime($date))];
}

//휴무일 설정
function fnholidays(){
	$nowYear = "2023";
	$nextYear = "2024";

	return array(
        "0101"=> array( "type"=> 0, "title"=> "신정", "year"=> "" ),
        "0301"=> array( "type"=> 0, "title"=> "삼일절", "year"=> "" ),
        "0505"=> array( "type"=> 0, "title"=> "어린이날", "year"=> "" ),
        "0606"=> array( "type"=> 0, "title"=> "현충일", "year"=> "" ),
        "0815"=> array( "type"=> 0, "title"=> "광복절", "year"=> "" ),
        "1003"=> array( "type"=> 0, "title"=> "개천절", "year"=> "" ),
        "1009"=> array( "type"=> 0, "title"=> "한글날", "year"=> "" ),
        "1225"=> array( "type"=> 0, "title"=> "크리스마스", "year"=> "" ),

		//매년 변경
        "0527"=> array( "type"=> 0, "title"=> "부처님오신날", "year"=> $nowYear ), 

        "0928"=> array( "type"=> 0, "title"=> "추석", "year"=> $nowYear ),
        "0929"=> array( "type"=> 0, "title"=> "추석", "year"=> $nowYear ),
        "0930"=> array( "type"=> 0, "title"=> "추석", "year"=> $nowYear ),

        "0209"=> array( "type"=> 0, "title"=> "설날", "year"=> $nextYear ),
        "0210"=> array( "type"=> 0, "title"=> "설날", "year"=> $nextYear ),
        "0212"=> array( "type"=> 0, "title"=> "설날", "year"=> $nextYear )

    );
}

//날짜 더하기
function plusDate($date, $count) {
	$arrdate = explode("-",$date);
	$datDate = date("Y-m-d", mktime(0, 0, 0, $arrdate[1], $arrdate[2], $arrdate[0]));
	$NextDate = date("Y-m-d", strtotime($datDate." +".$count." day"));

	return $NextDate;
}


//셔틀버스 이름
function fnBusNum($vlu){
	$busGubun = substr($vlu, 0, 1);
	$busPoint = substr($vlu, 1, 2);
	$busNumber = substr($vlu, 3, 1);

	if($busPoint == "Sa"){ //사당선
		$busPoint = "사당선";
	}else if($busPoint == "Jo"){ //종로선
		$busPoint = "종로선";
	}else if($busPoint == "Y2" || $busPoint == "E2"){ //서울행 오후
		$busPoint = "오후";		
	}else if($busPoint == "Y5" || $busPoint == "E5"){ //서울행 저녁
		$busPoint = "저녁";		
	}

	if($busGubun == 'Y'){
		$busGubun = '양양행';
	}else if($busGubun == 'E'){
		$busGubun = '동해행';
	}else{
		$busGubun = '서울행';
	}

	
	return $busGubun.' '.$busPoint.' '.$busNumber.'호차';
}

//셔틀버스 정류장 탑승시간, 위치
function fnBusPoint($vlu, $busNumber){
	$busNumber = substr($busNumber, 0, 3);

	if($busNumber == "ESa"){
		$busNumber = "YSa";
	}else if($busNumber == "EJo"){
		$busNumber = "YJo";
	}

	$busData = array(
		"YSa_신도림"=> "06:00|홈플러스 신도림점 앞|37.5095592|126.8885712|00:00"
		, "YSa_대림역"=> "06:07|대림역 2번출구 앞|37.4928008|126.8947074|00:00"
		, "YSa_사당역"=> "06:20|사당역 6번출구 방향 참약사 장수약국 앞|37.4764763|126.977734|00:00"
		, "YSa_강남역"=> "06:35|강남역 1번출구 버스정류장|37.4982078|127.0290928|00:00"
		, "YSa_종합운동장역"=> "06:50|종합운동장역 4번출구 방향 버스정류장 뒤쪽|37.5104765|127.0722925|00:00"

		, "YJo_합정역"=> "05:50|합정역 3번출구 앞|37.5507926|126.9159159|00:00"
		, "YJo_종로3가역"=> "06:10|종로3가역 12번출구 새마을금고 앞|37.5703347|126.99317687|00:00"
		, "YJo_건대입구"=> "06:35|건대입구역 롯데백화점 스타시티점 입구|37.5393413|127.0716672|00:00"
		, "YJo_종합운동장역"=> "06:50|종합운동장역 4번출구 방향 버스정류장 뒤쪽|37.5104765|127.0722925|00:00"

		, "SY2_남애3리"=> "14:30|남애3리 입구|37.9452543|128.7814356|17:30"
		, "SY2_인구해변"=> "14:35|현남면사무소 맞은편|37.9689758|128.7599915|17:35"
		, "SY2_죽도해변"=> "14:42|GS25 죽도비치점 맞은편|37.9720003|128.7595433|17:42"
		, "SY2_기사문해변"=> "14:50|기사문 해변주차장 입구|38.0053627|128.7306342|17:50"
		, "SY2_서피비치"=> "15:00|서피비치 회전교차로 횡단보도 앞|38.0268271|128.7169575|18:00"

		, "SY5_남애3리"=> "17:30|남애3리 입구|37.9452543|128.7814356|00:00"
		, "SY5_인구해변"=> "17:35|현남면사무소 맞은편|37.9689758|128.7599915|00:00"
		, "SY5_죽도해변"=> "17:42|GS25 죽도비치점 맞은편|37.9720003|128.7595433|00:00"
		, "SY5_기사문해변"=> "17:50|기사문 해변주차장 입구|38.0053627|128.7306342|00:00"
		, "SY5_서피비치"=> "18:00|서피비치 회전교차로 횡단보도 앞|38.0268271|128.7169575|00:00"

		, "AE2_솔.동해점"=> "12:10|솔.동해점 입구|37.5782382|129.1156248|17:10"
		, "AE2_대진해변"=> "12:15|대진해변 공영주차장 입구|37.5807657|129.111344|17:15"
		, "AE2_금진해변"=> "12:35|금진해변 공영주차장 입구|37.6347202|129.0450586|17:35"

		, "AE5_솔.동해점"=> "17:10|솔.동해점 입구|37.5782382|129.1156248|00:00"
		, "AE5_대진해변"=> "17:15|대진해변 공영주차장 입구|37.5807657|129.111344|00:00"
		, "AE5_금진해변"=> "17:35|금진해변 공영주차장 입구|37.6347202|129.0450586|00:00"
	);

	if($busData[$busNumber.'_'.$vlu] == null){
		$busData["Yend_서피비치"] = "End";
		$busData["Yend_기사문해변"] = "End";
		$busData["Yend_죽도해변"] = "End";
		$busData["Yend_인구해변"] = "End";
		$busData["Yend_남애3리"] = "End";

		$busData["Send_잠실역"] = "End";
		$busData["Send_강남역"] = "End";
		$busData["Send_사당역"] = "End";
		
		$busData["Eend_금진해변"] = "End";
		$busData["Eend_대진해변"] = "End";
		$busData["Eend_솔.동해점"] = "End";

		return $busData;
	}else{
		return $busData[$busNumber.'_'.$vlu];
	}
}

function fnBusPointArr($vlu, $gubun){
	$arrData = explode("_", $vlu);
	$arrDataList = explode("|", fnBusPoint($arrData[1], $arrData[0]));

	if($gubun == 0){ //정류장 위치
		return $arrDataList[1];
	}else if($gubun == 1){ //탑승시간
		$rtnData = explode(":", $arrDataList[0]);
		return $rtnData[0]."시 ".$rtnData[1]. "분";
	}
	else if($gubun == 2){ //서울행 탑승시간
		$rtnData = explode(":", $arrDataList[0]);
		$rtnData1 = $rtnData[0]."시 ".$rtnData[1]. "분 / ";
		
		$rtnData = explode(":", $arrDataList[4]);
		return $rtnData1.$rtnData[0]."시 ".$rtnData[1]. "분";
	}
}

//셔틀버스 정류장 목록
function fnBusPointList($vlu){
	$busData = array(
		"busPoint_Start1"=> "신도림 &gt; 대림역 &gt; 사당역 &gt; 강남역 &gt; 종합운동장역"
		, "busPoint_Start2"=> "합정역 &gt; 종로3가역 &gt; 건대입구 &gt; 종합운동장역"
		, "busPoint_End_yy"=> "서피비치 &gt; 기사문해변 &gt; 죽도해변 &gt; 인구해변 &gt; 남애3리"
		, "busPoint_End_dh"=> "금진해변 &gt; 대진해변 &gt; 솔.동해점"
		, "busPoint_End"=> "잠실역 &gt; 강남역 &gt; 사당역"
	);

	if($busData[$vlu] == null){
		return '|';
	}else{
		return $busData[$vlu];
	}
}

//환불수수료
function cancelPrice($regDate, $timeM, $ResConfirm, $ResPrice, $rtn_charge_yn){
	$now = date("Y-m-d");
	$resDate = date("Y-m-d", strtotime(substr($regDate, 0, 10)));
	$resNow = (strtotime($resDate)-strtotime($now)) / (60*60*24);

	$cancelPrcie = 0;

	//2시간 이내 또는 미입금 상태
	if($timeM <= 130 || $ResConfirm == 0 || $rtn_charge_yn == "N"){

	}else{
		if($ResConfirm == 3){
			if($resNow == 8){
				$cancelPrcie = $ResPrice * 0.1;
			}else if($resNow == 7){
				$cancelPrcie = $ResPrice * 0.2;
			}else if($resNow == 6){
				$cancelPrcie = $ResPrice * 0.3;
			}else if($resNow == 5){
				$cancelPrcie = $ResPrice * 0.4;
			}else if($resNow == 4){
				$cancelPrcie = $ResPrice * 0.5;
			}else if($resNow == 3){
				$cancelPrcie = $ResPrice * 0.6;
			}else if($resNow == 2){
				$cancelPrcie = $ResPrice * 0.7;
			}else if($resNow == 0 || $resNow == 1){
				$cancelPrcie = $ResPrice;
			}else{
				$cancelPrcie = 0;
			}
		}
	}

	return $cancelPrcie;
}

function encrypt($plaintext){
	$key = pack('H*', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");

	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
	$ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plaintext, MCRYPT_MODE_CBC, $iv);

	$ciphertext = $iv . $ciphertext;
	$ciphertext_base64 = base64_encode($ciphertext);

	return $ciphertext_base64;
}

function decrypt($plaintext){
	$key = pack('H*', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");
	$ciphertext_dec = base64_decode($plaintext);
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	$iv_dec = substr($ciphertext_dec, 0, $iv_size);
	$ciphertext_dec = substr($ciphertext_dec, $iv_size);
	$plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);

	return $plaintext_dec;
}
?>