<?
//요일 표시
function fnWeek($date){
    $week = array("일","월","화","수","목","금","토");
    return $week[date('w', strtotime($date))];
}

function fnholidays(){
	return array(
        "0101"=> array( "type"=> 0, "title"=> "신정", "year"=> "" ),
        "0301"=> array( "type"=> 0, "title"=> "삼일절", "year"=> "" ),
        // "0415"=> array( "type"=> 0, "title"=> "선거", "year"=> "" ),
        "0519"=> array( "type"=> 0, "title"=> "석가탄신일", "year"=> "" ),
        "0505"=> array( "type"=> 0, "title"=> "어린이날", "year"=> "" ),
        "0606"=> array( "type"=> 0, "title"=> "현충일", "year"=> "" ),
        "0815"=> array( "type"=> 0, "title"=> "광복절", "year"=> "" ),
        "1003"=> array( "type"=> 0, "title"=> "개천절", "year"=> "" ),
        "1009"=> array( "type"=> 0, "title"=> "한글날", "year"=> "" ),
        "1225"=> array( "type"=> 0, "title"=> "크리스마스", "year"=> "" ),

        "0930"=> array( "type"=> 0, "title"=> "추석", "year"=> "2020" ),
        "1001"=> array( "type"=> 0, "title"=> "추석", "year"=> "2020" ),
        "1002"=> array( "type"=> 0, "title"=> "추석", "year"=> "2020" ),


        "0920"=> array( "type"=> 0, "title"=> "추석", "year"=> "2021" ),
        "0921"=> array( "type"=> 0, "title"=> "추석", "year"=> "2021" ),
        "0922"=> array( "type"=> 0, "title"=> "추석", "year"=> "2021" ),
        "0211"=> array( "type"=> 0, "title"=> "설날", "year"=> "2021" ),
        "0212"=> array( "type"=> 0, "title"=> "설날", "year"=> "2021" ),
        "0213"=> array( "type"=> 0, "title"=> "설날", "year"=> "2021" )
    );
}

//날짜 더하기
function plusDate($date, $count) {
	$arrdate = explode("-",$date);
	$datDate = date("Y-m-d", mktime(0, 0, 0, $arrdate[1], $arrdate[2], $arrdate[0]));
	$NextDate = date("Y-m-d", strtotime($datDate." +".$count." day"));

	return $NextDate;
}

function fnBusNum($vlu){
	$busGubun = substr($vlu, 0, 1);
	$busNumber = substr($vlu, 1, 1);

	if($busGubun == 'Y'){
		return '서울출발 '.$busNumber.'호차';
	}else if($busGubun == 'E'){
		return '동해행 '.$busNumber.'호차';
	}else{
        $busTime = substr($vlu, 1, 1);
        $busNumber = substr($vlu, 2, 1);
		if($busTime == 2){
			$busTime = "15";
		}else{
			$busTime = "18";
		}

		return '서울복귀 '.$busNumber.'호차';
	}
}

function fnBusPoint($vlu, $busNumber){	
	$busData = array(
		"Y1_공덕역"=> "10:30|공덕역 3번출구 앞"
		, "Y1_건대입구역"=> "11:10|건대입구역 5번출구 앞"
		, "S1_니지모리"=> "21:00|니지모리스튜디오"
		, "E1_신도림"=> "06:20|홈플러스 신도림점 앞"
		, "E1_대림역"=> "06:30|대림역 2번출구 앞"
		, "E1_봉천역"=> "06:40|봉천역 1번출구 앞"
		, "E1_사당역"=> "06:50|사당역 6번출구 방향 참약사 장수약국 앞"
		, "E1_강남역"=> "07:05|강남역 1번출구 버스정류장"
		, "E1_종합운동장역"=> "07:20|종합운동장역 4번출구 방향 버스정류장 뒤쪽"
		, "E2_당산역"=> "06:05|당산역 13출구 방향 버거킹 앞"
		, "E2_합정역"=> "06:10|합정역 3번출구 앞"
		, "E2_종로3가역"=> "06:35|종로3가역 12번출구 새마을금고 앞"
		, "E2_왕십리역"=> "06:50|왕십리역 11번출구 우리은행 앞"
		, "E2_건대입구"=> "07:05|건대입구역 롯데백화점 스타시티점 입구"
		, "E2_종합운동장역"=> "07:20|종합운동장역 4번출구 방향 버스정류장 뒤쪽"
		, "A1_솔.동해점"=> "14:00|솔.동해점 입구"
		, "A1_대진항"=> "14:05|대진항 입구"
		, "A1_금진해변"=> "14:20|금진해변 공영주차장 입구"
		, "A2_솔.동해점"=> "17:00|솔.동해점 입구"
		, "A2_대진항"=> "17:05|대진항 입구"
		, "A2_금진해변"=> "17:20|금진해변 공영주차장 입구"
	);

	if($busNumber == "Y1" || $busNumber == "Y2" || $busNumber == "Y3" || $busNumber == "Y4" || $busNumber == "Y5" || $busNumber == "Y6"){
		$busType = "Y1";
	}else if($busNumber == "S21" || $busNumber == "S22" || $busNumber == "S23" || $busNumber == "S24" || $busNumber == "S25" || $busNumber == "S26"){
		$busType = "S1";
	}else if($busNumber == "E1" || $busNumber == "E3" || $busNumber == "E5"){
		$busType = "E1";
	}else if($busNumber == "E2" || $busNumber == "E4" || $busNumber == "E6"){
		$busType = "E1";
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