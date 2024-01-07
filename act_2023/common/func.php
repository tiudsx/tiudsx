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

//셔틀버스 정류장 목록
function fnBusPointList($vlu){
	$busData = array(
		"busPoint_Start1"=> "신도림 &gt; 대림역 &gt; 사당역 &gt; 강남역 &gt; 종합운동장역"
		, "busPoint_Start2"=> "합정역 &gt; 종로3가역 &gt; 건대입구 &gt; 종합운동장역"
		, "busPoint_End_yy"=> "서피비치 &gt; 기사문해변 &gt; 죽도해변 &gt; 인구해변 &gt; 남애3리"
		, "busPoint_End_dh"=> "금진해변 &gt; 금진 서프홀릭, 브라보서프 &gt; 망상 나인비치 &gt; 대진해변 &gt; 솔.동해점"
		, "busPoint_End"=> "잠실역 &gt; 강남역 &gt; 사당역"
		, "busPoint_End2"=> "잠실역 &gt; 강남역 &gt; 사당역"
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

	$rtn = array( 
		"full"=> $busGubun.' '.$busPoint.' '.$busNumber.'호차'
		, "gubun"=> $busGubun
		, "point"=> $busPoint
		, "num"=> $busNumber.'호차' );

	return $rtn;
}

//셔틀버스 정류장 탑승시간, 위치
function fnBusPoint2023($bus_line, $point, $busSeq){
	//busSeq
	//7 : 양양      14 : 동해      212 : 양양/동해
	if($busSeq == 14){
		$busData = array(
			  "사당_신도림"=> "06:10|홈플러스 신도림점 앞|37.5095592|126.8885712"
			, "사당_대림역"=> "06:17|대림역 2번출구 앞|37.4928008|126.8947074"
			, "사당_사당역"=> "06:30|사당역 6번출구 방향 참약사 장수약국 앞|37.4764807|126.9777336"
			, "사당_강남역"=> "06:45|강남역 1번출구 버스정류장|37.4982078|127.0290928"
			, "사당_종합운동장역"=> "07:00|종합운동장역 4번출구 방향 버스정류장 뒤쪽|37.5104765|127.0722925"
			, "오후_솔.동해점"=> "12:30|솔게스트하우스 동해서핑점 입구|37.5782382|129.1156248"
			, "오후_대진해변"=> "12:35|대진해변 공영주차장 입구|37.5807657|129.111344"
			, "오후_나인비치"=> "12:40|망상 나인비치 주차장 입구|37.589873|129.0949103"
			, "오후_금진해변"=> "12:55|금진해변 공영주차장 입구|37.6347202|129.0450586"
			, "오후_서프홀릭"=> "13:00|금진해변 서프홀릭 입구|37.6399525|129.043521"
			, "오후_브라보서프"=> "13:00|금진해변 브라보서프 입구|37.6380981|129.0440093"
		);
		
		if($busData[$bus_line.'_'.$point] == null){
			$busData["Send_금진해변"] = "End";
			$busData["Send_서프홀릭"] = "End";
			$busData["Send_브라보서프"] = "End";
			$busData["Send_나인비치"] = "End";
			$busData["Send_대진해변"] = "End";
			$busData["Send_솔.동해점"] = "End";
	
			$busData["Eend_잠실역"] = "End";
			$busData["Eend_강남역"] = "End";
			$busData["Eend_사당역"] = "End";
		}
	}else if($busSeq == 7){
		$busData = array(
			  "사당_신도림"=> "06:00|홈플러스 신도림점 앞|37.5095592|126.8885712"
			, "사당_대림역"=> "06:07|대림역 2번출구 앞|37.4928008|126.8947074"
			, "사당_사당역"=> "06:30|사당역 6번출구 방향 참약사 장수약국 앞|37.4764807|126.9777336"
			, "사당_강남역"=> "06:45|강남역 1번출구 버스정류장|37.4982078|127.0290928"
			, "사당_종합운동장역"=> "07:00|종합운동장역 4번출구 방향 버스정류장 뒤쪽|37.5104765|127.0722925"
			, "종로_합정역"=> "05:55|합정역 3번출구 앞|37.5507926|126.9159159"
			, "종로_종로3가역"=> "06:15|종로3가역 12번출구 새마을금고 앞|37.5703347|126.99317687"
			, "종로_건대입구"=> "06:45|건대입구역 롯데백화점 스타시티점 입구|37.5393413|127.0716672"
			, "종로_종합운동장역"=> "07:00|종합운동장역 4번출구 방향 버스정류장 뒤쪽|37.5104765|127.0722925"
			
			, "오후_남애3리"=> "12:30|남애3리 입구|37.9452543|128.7814356"
			, "오후_인구해변"=> "12:35|현남면사무소 맞은편|37.9689758|128.7599915"
			, "오후_죽도해변"=> "12:42|GS25 죽도비치점 맞은편|37.9720003|128.7595433"
			, "오후_기사문해변"=> "12:50|기사문 해변주차장 입구|38.0053627|128.7306342"
			, "오후_서피비치"=> "13:00|서피비치 회전교차로 횡단보도 앞|38.0268271|128.7169575"

			, "저녁_남애3리"=> "17:30|남애3리 입구|37.9452543|128.7814356"
			, "저녁_인구해변"=> "17:35|현남면사무소 맞은편|37.9689758|128.7599915"
			, "저녁_죽도해변"=> "17:42|GS25 죽도비치점 맞은편|37.9720003|128.7595433"
			, "저녁_기사문해변"=> "17:50|기사문 해변주차장 입구|38.0053627|128.7306342"
			, "저녁_서피비치"=> "18:00|서피비치 회전교차로 횡단보도 앞|38.0268271|128.7169575"
		);

		if($busData[$bus_line.'_'.$point] == null){
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
	
	if($busData[$bus_line.'_'.$point] == null){
		return $busData;
	}else{
		return $busData[$bus_line.'_'.$point];
	}
}

function fnBusPointArr2023($vlu, $busSeq, $type){
	$arrData = explode("_", $vlu);
	$arrDataList = explode("|", fnBusPoint2023($arrData[0], $arrData[1], $busSeq));

	if($type == 0){ //정류장 위치
		return $arrDataList[1];
	}else if($type == 1){ //탑승시간
		$rtnData = explode(":", $arrDataList[0]);
		return $rtnData[0]."시 ".$rtnData[1]. "분";
	}
}

function fnBusUrl($url){
	$rtn = "";
	if($url == "surfbus_yy" || $url == "surfbus_yy_2023" || $url == "7"){ //양양 셔틀버스
		$rtn = array( 
			"seq"=> 7
			, "type"=> "양양"
			, "tab"=> "_view_tab3_yy.php"
			, "point"=> "_view_point_yy.php"
			, "rtnUrl"=> "surfbus_yy_2023" );
	}else if($url == "surfbus_dh" || $url == "surfbus_dh_2023" || $url == "14"){ //동해 셔틀버스
		$rtn = array( 
			"seq"=> 14
			, "type"=> "동해"
			, "tab"=> "_view_tab3_dh.php"
			, "point"=> "_view_point_dh.php"
			, "rtnUrl"=> "surfbus_dh_2023" );
	}else{ //양양,동해 셔틀버스
		$rtn = array( 
			"seq"=> 212
			, "type"=> "양양동해"
			, "tab"=> "_view_tab3_yd.php"
			, "point"=> "_view_point_yd.php"
			, "rtnUrl"=> "surfbus_yd" );
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

//네이버 단축URL 생성
function shortURL($url){
	$client_id = "zhh3svia3i";
	$client_secret = "HndcoWtPGYL7hw9TVvhviZzEL7Sg921WUEIDdWxw";
  
	$encText = urlencode($url);
	$postvars = "url=".$encText;
  
	$url = "https://naveropenapi.apigw.ntruss.com/util/v1/shorturl";
  
	$is_post = true;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, $is_post);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch,CURLOPT_POSTFIELDS, $postvars);
  
	$headers = array();
	$headers[] = "X-NCP-APIGW-API-KEY-ID: ".$client_id;
	$headers[] = "X-NCP-APIGW-API-KEY: ".$client_secret;
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  
	$response = curl_exec ($ch);
	$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  
	//echo "status_code:".$status_code."<br />";
	curl_close ($ch);
	if($status_code == 200) {
	  return json_decode($response,true)["result"]["url"];
	} else {
	  return "Error :".$response;
	}
}

//쿠폰코드 생성
function RandString($len){
	$return_str = "";

	for ( $i = 0; $i < $len; $i++ ) {
		mt_srand((double)microtime()*1000000);
		$return_str .= substr('123456789ABCDEFGHIJKLMNPQRSTUVWXYZ', mt_rand(0,33), 1);
	}

	return $return_str;
}

//쿠폰코드
function fnCouponCode($seq){
	// $sitename = ""; 
	// $couponbool = false;
	// if($couponseq == 7 || $couponseq == 26 || $couponseq == 27 || $couponseq == 28 || $couponseq == 29){
	// 	$sitename = "[네이버쇼핑]"; 
	// }else if($couponseq == 10){
	// 	$sitename = "[네이버예약]"; 
	// }else if($couponseq == 11 || $couponseq == 17 || $couponseq == 20 || $couponseq == 21 || $couponseq == 22){
	// 	$sitename = "[프립]"; 
	// }else if($couponseq == 16){
	// 	$sitename = "[클룩]"; 
	// }else if($couponseq == 12){
	// 	$sitename = "[마이리얼트립]"; 
	// }else if($couponseq == 15){
	// 	$sitename = "[서프존]"; 
	// }else if($couponseq == 23){
	// 	$sitename = "[브라보서프]"; 
	// }

	// if($type == "admin"){
	// 	if($couponseq == 17){
	// 		$sitename = "[프립-마린]"; 
	// 	}else if($couponseq == 20){
	// 		$sitename = "[프립-인구]"; 
	// 	}else if($couponseq == 21){
	// 		$sitename = "[프립-서팩]"; 
	// 	}else if($couponseq == 22){
	// 		$sitename = "[프립-힐링캠프]"; 

	// 	}else if($couponseq == 26){
	// 		$sitename = "[네이버-마린]"; 
	// 	}else if($couponseq == 27){
	// 		$sitename = "[네이버-인구]"; 
	// 	}else if($couponseq == 28){
	// 		$sitename = "[네이버-서팩]"; 
	// 	}else if($couponseq == 29){
	// 		$sitename = "[네이버-힐링캠프]"; 
	// 	}else if($couponseq == 30){
	// 		$sitename = "[엑스크루]"; 
	// 	}else if($couponseq == 31){
	// 		$sitename = "[모행]"; 
	// 	}else if($res_coupon == "MONS"){
	// 		$sitename = "[몬스터]"; 
	// 	}else if($res_coupon == "JOA"){
	// 		$sitename = "[조아서프]"; 
	// 	}else if($res_coupon != "" && $sitename == ""){
	// 		$sitename = "[할인]"; 
	// 	}
	// }

	// if($type == "bool"){
	// 	if($sitename == ""){
	// 		return false;
	// 	}else{
	// 		return true;
	// 	}
	// }else{
	// 	return $sitename;
	// }
	
	$name = "";
	$prod_name = "";

	if($seq == 11 || $seq == 17 || $seq == 20 || $seq == 22){ //프립
		$name = "프립";
	}else if($seq == 15){ //서프존
		$name = "서프존";
	}else if($seq == 16){ //클룩
		$name = "클룩";
	}else if($seq == 23){ //브라보서프
		$name = "브라보서프";
	}else if($seq == 26 || $seq == 27 || $seq == 29){ //네이버
		$name = "네이버 액트립";
	}else if($seq == 31){ //모행
		$name = "모행";
	}

	if($seq == 11){ //프립
		$prod_name = "프립 셔틀버스";
	}else if($seq == 15){ //서프존
		$prod_name = "서프존 셔틀버스";
	}else if($seq == 16){ //클룩
		$prod_name = "클룩 셔틀버스";
	}else if($seq == 17){ //프립 마린 당일
		$prod_name = "당일 마린서프 패키지";
	}else if($seq == 20){ //프립 인구 당일
		$prod_name = "당일 인구서프 패키지";
	}else if($seq == 22){ //프립 솔게하
		$prod_name = "동해 힐링캠프 (1박)";
	}else if($seq == 23){ //브라보서프
		$prod_name = "브라보서프 (1박)";
	}else if($seq == 26){ //네이버 마린 당일
		$prod_name = "당일 마린서프 패키지";
	}else if($seq == 27){ //네이버 인구 당일
		$prod_name = "당일 인구서프 패키지";
	}else if($seq == 29){ //네이버 솔게하
		$prod_name = "동해 힐링캠프 (1박)";
	}else if($seq == 31){ //모행
		$prod_name = "모행";
	}

	$rtn = array( 
		"name"=> $name
		, "prod_name"=> $prod_name);

	return $rtn;
}
?>