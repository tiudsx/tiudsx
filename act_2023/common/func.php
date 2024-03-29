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

	$busData = array(
		// "YSa_신도림"=> "06:00|홈플러스 신도림점 앞|37.5095592|126.8885712|00:00"
		// , "YSa_대림역"=> "06:07|대림역 2번출구 앞|37.4928008|126.8947074|00:00"
		// , "YSa_사당역"=> "06:20|사당역 6번출구 방향 참약사 장수약국 앞|37.4764807|126.9777336|00:00"
		// , "YSa_강남역"=> "06:35|강남역 1번출구 버스정류장|37.4982078|127.0290928|00:00"
		// , "YSa_종합운동장역"=> "06:50|종합운동장역 4번출구 방향 버스정류장 뒤쪽|37.5104765|127.0722925|00:00"

		// , "YJo_합정역"=> "05:50|합정역 3번출구 앞|37.5507926|126.9159159|00:00"
		// , "YJo_종로3가역"=> "06:10|종로3가역 12번출구 새마을금고 앞|37.5703347|126.99317687|00:00"
		// , "YJo_건대입구"=> "06:35|건대입구역 롯데백화점 스타시티점 입구|37.5393413|127.0716672|00:00"
		// , "YJo_종합운동장역"=> "06:50|종합운동장역 4번출구 방향 버스정류장 뒤쪽|37.5104765|127.0722925|00:00"
		
		// , "SY2_남애3리"=> "14:30|남애3리 입구|37.9452543|128.7814356|17:30"
		// , "SY2_인구해변"=> "14:35|현남면사무소 맞은편|37.9689758|128.7599915|17:35"
		// , "SY2_죽도해변"=> "14:42|GS25 죽도비치점 맞은편|37.9720003|128.7595433|17:42"
		// , "SY2_기사문해변"=> "14:50|기사문 해변주차장 입구|38.0053627|128.7306342|17:50"
		// , "SY2_서피비치"=> "15:00|서피비치 회전교차로 횡단보도 앞|38.0268271|128.7169575|18:00"

		  "YSa_신도림"=> "05:40|홈플러스 신도림점 앞|37.5095592|126.8885712|00:00"
		, "YSa_대림역"=> "05:47|대림역 2번출구 앞|37.4928008|126.8947074|00:00"
		, "YSa_사당역"=> "06:10|사당역 6번출구 방향 참약사 장수약국 앞|37.4764807|126.9777336|00:00"
		, "YSa_강남역"=> "06:25|강남역 1번출구 버스정류장|37.4982078|127.0290928|00:00"
		, "YSa_종합운동장역"=> "06:40|종합운동장역 4번출구 방향 버스정류장 뒤쪽|37.5104765|127.0722925|00:00"

		, "YJo_합정역"=> "05:35|합정역 3번출구 앞|37.5507926|126.9159159|00:00"
		, "YJo_종로3가역"=> "05:55|종로3가역 12번출구 새마을금고 앞|37.5703347|126.99317687|00:00"
		, "YJo_건대입구"=> "06:25|건대입구역 롯데백화점 스타시티점 입구|37.5393413|127.0716672|00:00"
		, "YJo_종합운동장역"=> "06:40|종합운동장역 4번출구 방향 버스정류장 뒤쪽|37.5104765|127.0722925|00:00"

		, "SY2_남애3리"=> "14:00|남애3리 입구|37.9452543|128.7814356|17:30"
		, "SY2_인구해변"=> "14:05|현남면사무소 맞은편|37.9689758|128.7599915|17:35"
		, "SY2_죽도해변"=> "14:10|GS25 죽도비치점 맞은편|37.9720003|128.7595433|17:42"
		, "SY2_기사문해변"=> "14:20|기사문 해변주차장 입구|38.0053627|128.7306342|17:50"
		, "SY2_서피비치"=> "14:30|서피비치 회전교차로 횡단보도 앞|38.0268271|128.7169575|18:00"

		, "SY5_남애3리"=> "17:30|남애3리 입구|37.9452543|128.7814356|00:00"
		, "SY5_인구해변"=> "17:35|현남면사무소 맞은편|37.9689758|128.7599915|00:00"
		, "SY5_죽도해변"=> "17:42|GS25 죽도비치점 맞은편|37.9720003|128.7595433|00:00"
		, "SY5_기사문해변"=> "17:50|기사문 해변주차장 입구|38.0053627|128.7306342|00:00"
		, "SY5_서피비치"=> "18:00|서피비치 회전교차로 횡단보도 앞|38.0268271|128.7169575|00:00"

		// , "ESa_신도림"=> "05:50|테크노마트 신도림점 앞|37.5095592|126.8885712|00:00"
		// , "ESa_사당역"=> "06:20|사당역 6번출구 방향 참약사 장수약국 앞|37.4764807|126.9777336|00:00"
		// , "ESa_올림픽공원역"=> "06:50|올림픽공원역 1번출구 버스정류장 앞쪽|37.5104765|127.0722925|00:00"

		, "AE2_솔.동해점"=> "14:30|솔게스트하우스 동해서핑점 입구|37.5782382|129.1156248|0"
		, "AE2_대진해변"=> "14:35|대진해변 공영주차장 입구|37.5807657|129.111344|0"
		, "AE2_나인비치"=> "14:40|망상 나인비치 주차장 입구|37.589873|129.0949103|0"
		, "AE2_금진해변"=> "14:55|금진해변 공영주차장 입구|37.6347202|129.0450586|0"
		, "AE2_서프홀릭"=> "15:00|금진해변 서프홀릭 입구|37.6380981|129.0440093|0"
		// , "AE5_솔.동해점"=> "17:10|솔게스트하우스 동해서핑점 입구|37.5782382|129.1156248|00:00"
		// , "AE5_대진해변"=> "17:15|대진해변 공영주차장 입구|37.5807657|129.111344|00:00"
		// , "AE5_금진해변"=> "17:35|금진해변 공영주차장 입구|37.6347202|129.0450586|00:00"

		// ,"YD-S-Sa_신도림"=> "05:40|홈플러스 신도림점 앞|37.5095592|126.8885712|00:00"
		// , "YD-S-Sa_대림역"=> "05:47|대림역 2번출구 앞|37.4928008|126.8947074|00:00"
		// , "YD-S-Sa_사당역"=> "06:10|사당역 6번출구 방향 참약사 장수약국 앞|37.4764807|126.9777336|00:00"
		// , "YD-S-Sa_강남역"=> "06:25|강남역 1번출구 버스정류장|37.4982078|127.0290928|00:00"
		// , "YD-S-Sa_종합운동장역"=> "06:40|종합운동장역 4번출구 방향 버스정류장 뒤쪽|37.5104765|127.0722925|00:00"

		// , "YD-S-Jo_합정역"=> "05:35|합정역 3번출구 앞|37.5507926|126.9159159|00:00"
		// , "YD-S-Jo_종로3가역"=> "05:55|종로3가역 12번출구 새마을금고 앞|37.5703347|126.99317687|00:00"
		// , "YD-S-Jo_건대입구"=> "06:25|건대입구역 롯데백화점 스타시티점 입구|37.5393413|127.0716672|00:00"
		// , "YD-S-Jo_종합운동장역"=> "06:40|종합운동장역 4번출구 방향 버스정류장 뒤쪽|37.5104765|127.0722925|00:00"

		// , "YD-E-E2_솔.동해점"=> "12:50|솔게스트하우스 동해서핑점 입구|37.5782382|129.1156248|17:10"
		// , "YD-E-E2_대진해변"=> "12:55|대진해변 공영주차장 입구|37.5807657|129.111344|17:15"
		// , "YD-E-E2_금진해변"=> "13:15|대진해변 공영주차장 입구|37.5807657|129.111344|17:15"
		// , "YD-E-E2_남애3리"=> "14:00|남애3리 입구|37.9452543|128.7814356|17:30"
		// , "YD-E-E2_인구해변"=> "14:05|현남면사무소 맞은편|37.9689758|128.7599915|17:35"
		// , "YD-E-E2_죽도해변"=> "14:10|GS25 죽도비치점 맞은편|37.9720003|128.7595433|17:42"
		// , "YD-E-E2_기사문해변"=> "14:20|기사문 해변주차장 입구|38.0053627|128.7306342|17:50"
		// , "YD-E-E2_서피비치"=> "14:30|서피비치 회전교차로 횡단보도 앞|38.0268271|128.7169575|18:00"

		// , "YD-E-E5_남애3리"=> "17:30|남애3리 입구|37.9452543|128.7814356|00:00"
		// , "YD-E-E5_인구해변"=> "17:35|현남면사무소 맞은편|37.9689758|128.7599915|00:00"
		// , "YD-E-E5_죽도해변"=> "17:42|GS25 죽도비치점 맞은편|37.9720003|128.7595433|00:00"
		// , "YD-E-E5_기사문해변"=> "17:50|기사문 해변주차장 입구|38.0053627|128.7306342|00:00"
		// , "YD-E-E5_서피비치"=> "18:00|서피비치 회전교차로 횡단보도 앞|38.0268271|128.7169575|00:00"
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
	}else if($gubun == 2){ //서울행 탑승시간
		$rtnData = explode(":", $arrDataList[0]);
		$rtnData1 = $rtnData[0]."시 ".$rtnData[1]. "분 / ";
		
		$rtnData = explode(":", $arrDataList[4]);
		return $rtnData1.$rtnData[0]."시 ".$rtnData[1]. "분";
	}else if($gubun == 3){ //서울행 오후/저녁 탑승시간
		$rtnData = explode(":", $arrDataList[0]);
		return $rtnData[0]."시 ".$rtnData[1]. "분";
	}
}

//셔틀버스 정류장 목록
function fnBusPointList($vlu){
	$busData = array(
		"busPoint_Start1"=> "신도림 &gt; 대림역 &gt; 사당역 &gt; 강남역 &gt; 종합운동장역"
		, "busPoint_Start2"=> "합정역 &gt; 종로3가역 &gt; 건대입구 &gt; 종합운동장역"
		, "busPoint_End_yy"=> "서피비치 &gt; 기사문해변 &gt; 죽도해변 &gt; 인구해변 &gt; 남애3리"
		, "busPoint_End_dh"=> "금진해변 &gt; 금진 서프홀릭, 브라보서프 &gt; 망상 나인비치 &gt; 대진해변 &gt; 솔.동해점"
		, "busPoint_End"=> "잠실역 &gt; 강남역 &gt; 사당역"
		, "busPoint_End2"=> "올림픽공원역 &gt; 사당역 &gt; 신도림역"
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

function coupontype($type, $couponseq, $res_coupon){
	$sitename = ""; 
	$couponbool = false;
	if($couponseq == 7 || $couponseq == 26 || $couponseq == 27 || $couponseq == 28 || $couponseq == 29){
		$sitename = "[네이버쇼핑]"; 
	}else if($couponseq == 10){
		$sitename = "[네이버예약]"; 
	}else if($couponseq == 11 || $couponseq == 17 || $couponseq == 20 || $couponseq == 21 || $couponseq == 22){
		$sitename = "[프립]"; 
	}else if($couponseq == 16){
		$sitename = "[클룩]"; 
	}else if($couponseq == 12){
		$sitename = "[마이리얼트립]"; 
	}else if($couponseq == 15){
		$sitename = "[서프존]"; 
	}else if($couponseq == 23){
		$sitename = "[브라보서프]"; 
	}

	if($type == "admin"){
		if($couponseq == 17){
			$sitename = "[프립-마린]"; 
		}else if($couponseq == 20){
			$sitename = "[프립-인구]"; 
		}else if($couponseq == 21){
			$sitename = "[프립-서팩]"; 
		}else if($couponseq == 22){
			$sitename = "[프립-힐링캠프]"; 

		}else if($couponseq == 26){
			$sitename = "[네이버-마린]"; 
		}else if($couponseq == 27){
			$sitename = "[네이버-인구]"; 
		}else if($couponseq == 28){
			$sitename = "[네이버-서팩]"; 
		}else if($couponseq == 29){
			$sitename = "[네이버-힐링캠프]"; 
		}else if($couponseq == 30){
			$sitename = "[엑스크루]"; 
		}else if($couponseq == 31){
			$sitename = "[모행]"; 
		}else if($res_coupon == "MONS"){
			$sitename = "[몬스터]"; 
		}else if($res_coupon != "" && $sitename == ""){
			$sitename = "[할인]"; 
		}
	}

	if($type == "bool"){
		if($sitename == ""){
			return false;
		}else{
			return true;
		}
	}else{
		return $sitename;
	}
}



//셔틀버스 이름
function fnBusNum2023($vlu){
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
function fnBusPoint2023($point, $busNumber, $busSeq){
	//busSeq
	//7 : 양양      14 : 동해      212 : 양양/동해
	if($busSeq == 14){
		$busData = array(
			"동해_신도림역"=> "05:50|테크노마트 신도림점 앞|37.5061933|126.8909233"
			, "동해_사당역"=> "06:20|사당역 6번출구 방향 참약사 장수약국 앞|37.4764807|126.9777336"
			, "동해_올림픽공원역"=> "06:50|올림픽공원역 1번출구 버스정류장 앞쪽|37.5166186|127.1315229"
			, "오후_브라보서프"=> "15:00|금진해변 브라보서프 입구|37.6380981|129.0440093"
			, "오후_서프홀릭"=> "15:00|금진해변 서프홀릭 입구|37.6399525|129.043521"
			, "오후_금진해변"=> "14:55|금진해변 공영주차장 입구|37.6347202|129.0450586"
			, "오후_나인비치"=> "14:40|망상 나인비치 주차장 입구|37.589873|129.0949103"
			, "오후_대진해변"=> "14:35|대진해변 공영주차장 입구|37.5807657|129.111344"
			, "오후_솔.동해점"=> "14:30|솔게스트하우스 동해서핑점 입구|37.5782382|129.1156248"
		);
		
		if($busData[$busNumber.'_'.$point] == null){
			$busData["Send_서프홀릭"] = "End";
			$busData["Send_브라보서프"] = "End";
			$busData["Send_금진해변"] = "End";
			$busData["Send_나인비치"] = "End";
			$busData["Send_대진해변"] = "End";
			$busData["Send_솔.동해점"] = "End";
			
			$busData["Eend_올림픽공원역"] = "End";
			$busData["Eend_사당역"] = "End";
			$busData["Eend_신도림역"] = "End";
		}
	}else if($busSeq == 7){
		$busData = array(
			  "사당_신도림"=> "06:00|홈플러스 신도림점 앞|37.5095592|126.8885712"
			, "사당_대림역"=> "06:07|대림역 2번출구 앞|37.4928008|126.8947074"
			, "사당_사당역"=> "06:20|사당역 6번출구 방향 참약사 장수약국 앞|37.4764807|126.9777336"
			, "사당_강남역"=> "06:35|강남역 1번출구 버스정류장|37.4982078|127.0290928"
			, "사당_종합운동장역"=> "06:50|종합운동장역 4번출구 방향 버스정류장 뒤쪽|37.5104765|127.0722925"
			, "종로_합정역"=> "05:50|합정역 3번출구 앞|37.5507926|126.9159159"
			, "종로_종로3가역"=> "06:10|종로3가역 12번출구 새마을금고 앞|37.5703347|126.99317687"
			, "종로_건대입구"=> "06:35|건대입구역 롯데백화점 스타시티점 입구|37.5393413|127.0716672"
			, "종로_종합운동장역"=> "06:50|종합운동장역 4번출구 방향 버스정류장 뒤쪽|37.5104765|127.0722925"
			
			, "오후_남애3리"=> "14:30|남애3리 입구|37.9452543|128.7814356"
			, "오후_인구해변"=> "14:35|현남면사무소 맞은편|37.9689758|128.7599915"
			, "오후_죽도해변"=> "14:42|GS25 죽도비치점 맞은편|37.9720003|128.7595433"
			, "오후_기사문해변"=> "14:50|기사문 해변주차장 입구|38.0053627|128.7306342"
			, "오후_서피비치"=> "15:00|서피비치 회전교차로 횡단보도 앞|38.0268271|128.7169575"

			, "저녁_남애3리"=> "17:30|남애3리 입구|37.9452543|128.7814356"
			, "저녁_인구해변"=> "17:35|현남면사무소 맞은편|37.9689758|128.7599915"
			, "저녁_죽도해변"=> "17:42|GS25 죽도비치점 맞은편|37.9720003|128.7595433"
			, "저녁_기사문해변"=> "17:50|기사문 해변주차장 입구|38.0053627|128.7306342"
			, "저녁_서피비치"=> "18:00|서피비치 회전교차로 횡단보도 앞|38.0268271|128.7169575"
		);

		if($busData[$busNumber.'_'.$point] == null){
			$busData["Send_서피비치"] = "End";
			$busData["Send_기사문해변"] = "End";
			$busData["Send_죽도해변"] = "End";
			$busData["Send_인구해변"] = "End";
			$busData["Send_남애3리"] = "End";
	
			$busData["Eend_잠실역"] = "End";
			$busData["Eend_강남역"] = "End";
			$busData["Eend_사당역"] = "End";
		}

	}
	
	if($busData[$busNumber.'_'.$point] == null){
		return $busData;
	}else{
		return $busData[$busNumber.'_'.$point];
	}
}

function fnBusPointArr2023($vlu, $busSeq, $type){
	$arrData = explode("_", $vlu);
	$arrDataList = explode("|", fnBusPoint2023($arrData[1], $arrData[0], $busSeq));

	if($type == 0){ //정류장 위치
		return $arrDataList[1];
	}else if($type == 1){ //탑승시간
		$rtnData = explode(":", $arrDataList[0]);
		return $rtnData[0]."시 ".$rtnData[1]. "분";
	}else if($type == 2){ //서울행 탑승시간
		$rtnData = explode(":", $arrDataList[0]);
		$rtnData1 = $rtnData[0]."시 ".$rtnData[1]. "분 / ";
		
		$rtnData = explode(":", $arrDataList[4]);
		return $rtnData1.$rtnData[0]."시 ".$rtnData[1]. "분";
	}else if($type == 3){ //서울행 오후/저녁 탑승시간
		$rtnData = explode(":", $arrDataList[0]);
		return $rtnData[0]."시 ".$rtnData[1]. "분";
	}
}

function fnBusUrl($url, $type){
	$rtn = "";
	if($url == "surfbus_yy" || $url == "surfbus_yy_2023" || $url == "7"){ //양양 셔틀버스
		$shopseq = 7;
        $bustype = "양양";
		$taburl = "_view_tab3_yy.php";
        $pointurl = "_view_point_yy.php";
        $rtnUrl = "surfbus_yy_2023";
	}else if($url == "surfbus_dh" || $url == "surfbus_dh_2023" || $url == "14"){ //동해 셔틀버스
		$shopseq = 14;
		$bustype = "동해";
		$taburl = "_view_tab3_dh.php";
        $pointurl = "_view_point_dh.php";
        $rtnUrl = "surfbus_dh_2023";
	}else{ //양양,동해 셔틀버스
		$shopseq = 212;
		$bustype = "양양동해";
		$taburl = "_view_tab3_yd.php";
        $pointurl = "_view_point_yd.php";
        $rtnUrl = "surfbus_yd";
	}
	
	if($type == "seq"){
		$rtn = $shopseq;
	}else if($type == "tab"){
		$rtn = $taburl;
	}else if($type == "point"){
		$rtn = $pointurl;
	}else if($type == "type"){
		$rtn = $bustype;
	}else if($type == "url"){
		$rtn = $rtnUrl;
	}

	return $rtn;
}

//셔틀버스 코드 치환
function fnBusCode($code, $bustype){
	$rtnCode1 = ""; //양양
	$rtnCode2 = ""; // 동해

	$code1 = substr($code, 0, 1);
	$code2 = substr($code, 1, 3);
	if($bustype == 14){
		if($code1 == "E"){ //서울출발
			$rtnCode1 = "Y";
			$rtnCode2 = "S";
		}else if($code1 == "A"){ //서울출발
			$rtnCode1 = "S";
			$rtnCode2 = "E";
		}
	}else{
		if($code1 == "S"){ //서울출발
			$rtnCode1 = "Y";
			$rtnCode2 = "E";
		}else if($code1 == "E"){ //서울출발
			$rtnCode1 = "S";
			$rtnCode2 = "A";
		}
	}

	if($bustype == "양양"){
		return $rtnCode1.$code2;
	}else if($bustype == "14"){
		return $rtnCode2.$code2;
	}else{
		return $rtnCode2.$code2;
	}
}
?>