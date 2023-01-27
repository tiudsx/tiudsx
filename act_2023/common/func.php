<?
//요일 표시
function fnWeek($date){
    $week = array("일","월","화","수","목","금","토");
    return $week[date('w', strtotime($date))];
}

//휴무일 설정
function fnholidays(){
	$nowYear = "2022";
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

function fnBusPoint($vlu, $busNumber, $gubun){	
	if($gubun == "START"){
		$busData = array(
			"Y1_신도림"=> "06:20|홈플러스 신도림점 앞"
			, "Y1_대림역"=> "06:30|대림역 2번출구 앞"
			, "Y1_봉천역"=> "06:40|봉천역 1번출구 앞"
			, "Y1_사당역"=> "06:50|사당역 6번출구 방향 참약사 장수약국 앞"
			, "Y1_강남역"=> "07:05|강남역 1번출구 버스정류장"
			, "Y1_종합운동장역"=> "07:20|종합운동장역 4번출구 방향 버스정류장 뒤쪽"
			, "Y2_당산역"=> "06:05|당산역 13출구 방향 버거킹 앞"
			, "Y2_합정역"=> "06:10|합정역 3번출구 앞"
			, "Y2_종로3가역"=> "06:35|종로3가역 12번출구 새마을금고 앞"
			, "Y2_왕십리역"=> "06:50|왕십리역 11번출구 우리은행 앞"
			, "Y2_건대입구"=> "07:05|건대입구역 롯데백화점 스타시티점 입구"
			, "Y2_종합운동장역"=> "07:20|종합운동장역 4번출구 방향 버스정류장 뒤쪽"
		);
	}else{
		$busData = array(
			"S1_청시행비치"=> "14:15|청시행비치 주차장 입구"
			, "S1_남애3리"=> "14:30|남애3리 입구"
			, "S1_인구"=> "14:35|현남면사무소 맞은편"
			, "S1_죽도"=> "14:40|GS25 죽도비치점 맞은편"
			, "S1_동산항"=> "14:45|동산카센타 맞은편"
			, "S1_기사문"=> "14:50|기사문 해변주차장 입구"
			, "S1_서피비치"=> "15:00|서피비치 회전교차로 횡단보도 앞"
			, "S2_청시행비치"=> "17:15|청시행비치 주차장 입구"
			, "S2_남애3리"=> "17:30|남애3리 입구"
			, "S2_인구"=> "17:35|현남면사무소 맞은편"
			, "S2_죽도"=> "17:40|GS25 죽도비치점 맞은편"
			, "S2_동산항"=> "17:45|동산카센타 맞은편"
			, "S2_기사문"=> "17:50|기사문 해변주차장 입구"
			, "S2_서피비치"=> "18:00|서피비치 회전교차로 횡단보도 앞"
			, "A1_솔.동해점"=> "14:00|솔.동해점 입구"
			, "A1_대진항"=> "14:05|대진항 입구"
			, "A1_금진해변"=> "14:20|금진해변 공영주차장 입구"
			, "A2_솔.동해점"=> "17:00|솔.동해점 입구"
			, "A2_대진항"=> "17:05|대진항 입구"
			, "A2_금진해변"=> "17:20|금진해변 공영주차장 입구"
		);
	}

	if($busNumber == "Y1" || $busNumber == "Y3" || $busNumber == "Y5"){
		$busType = "Y1";
	}else if($busNumber == "Y2" || $busNumber == "Y4" || $busNumber == "Y6"){
		$busType = "Y2";
	}else if($busNumber == "E1" || $busNumber == "E3" || $busNumber == "E5"){
		$busType = "E1";
	}else if($busNumber == "E2" || $busNumber == "E4" || $busNumber == "E6"){
		$busType = "E1";
	}else if($busNumber == "S21" || $busNumber == "S22" || $busNumber == "S23"){
		$busType = "S1";
	}else if($busNumber == "S51" || $busNumber == "S52" || $busNumber == "S53"){
		$busType = "S2";
	}else if($busNumber == "A21" || $busNumber == "A22"){
		$busType = "A1";
	}else if($busNumber == "A51" || $busNumber == "A52"){
		$busType = "A2";
	}

	if($busData[$busType.'_'.$vlu] == null){
		return '|';
	}else{
		return $busData[$busType.'_'.$vlu];
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