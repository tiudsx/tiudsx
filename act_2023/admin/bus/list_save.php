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
$to = "lud1@naver.com,ttenill@naver.com";

mysqli_query($conn, "SET AUTOCOMMIT=0");
mysqli_query($conn, "BEGIN");

if($param == "changeConfirmNew"){ //셔틀버스 정보 업데이트
	//단일 컬럼
	$resnum = $_REQUEST["resnum"];
	$user_name = $_REQUEST["user_name"];
	$user_tel = $_REQUEST["user_tel"];
	$user_email = $_REQUEST["user_email"];
	$memo = $_REQUEST["memo"]; //직원메모
	//$etc = $_REQUEST["etc"]; //요청사항
	$res_price_coupon = $rowSub['res_price_coupon'];
	$coupon = $rowSub['res_coupon'];
	$res_price = $_REQUEST["res_price"];
	$res_disprice = $_REQUEST["res_disprice"];
	$insdate = $_REQUEST["insdate"];
	$confirmdate = $_REQUEST["confirmdate"];
	$res_cooperate = $_REQUEST["res_cooperate"];
	$InsUserID = "admin";

	//배열 컬럼
    $chkCancel = $_REQUEST["ressubseq"];
    $res_seat = $_REQUEST["res_seat"];
    $res_spointname = $_REQUEST["res_spointname"];
    $res_epointname = $_REQUEST["res_epointname"];
    $res_confirm = $_REQUEST["res_confirm"];
    $rtn_charge_yn = $_REQUEST["rtn_charge_yn"];
    $res_kakao = $_REQUEST["res_kakao"];

	//================= 예약상태 및 메모 저장 =================
    $select_query = "UPDATE `AT_RES_MAIN` 
                    SET user_name = '".$user_name."'
                        ,memo = '".$memo."'
                        ,user_tel = '".$user_tel."'
                        ,user_email = '".$user_email."'
                WHERE resnum = '".$resnum."';";
    $result_set = mysqli_query($conn, $select_query);
    if(!$result_set) goto errGo;

	for($i = 0; $i < count($chkCancel); $i++){
		if($chkCancel[$i] == ""){
			continue;
		}

		$insdate1 = "";
		if($res_confirm[$i] == 3){
			$insdate1 = ",confirmdate = now()";

			if($res_kakao[$i] == "Y"){
				$intseq3 .= $chkCancel[$i].",";
			}
		}

		$select_query = "UPDATE AT_RES_SUB 
				SET res_seat = ".$res_seat[$i]."
					,res_spoint = '".$res_spointname[$i]."'
					,res_spointname = '".$res_spointname[$i]."'
					,res_epoint = '".$res_epointname[$i]."'
					,res_epointname = '".$res_epointname[$i]."'
					,res_confirm = ".$res_confirm[$i]."
					,rtn_charge_yn = '".$rtn_charge_yn[$i]."'
					".$insdate1."
					,upddate = now()
					,upduserid = 'admin'
			WHERE ressubseq = ".$chkCancel[$i].";";
		$result_set = mysqli_query($conn, $select_query);
		if(!$result_set) goto errGo;
	}

    $intseq3 .= '0';

    $arrSeatInfo = array();

    $ResNumber = $resnum;
	$userName = $user_name;
	$etc = $_REQUEST["etc"];
	$userPhone = $user_tel;
	$usermail = $user_email;

    //==========================카카오 메시지 발송 ==========================
    if($intseq3 != "0"){ //예약 확정처리 : 고객발송
        $select_query_sub = "SELECT * FROM AT_RES_SUB WHERE ressubseq IN ($intseq3) ORDER BY res_date, ressubseq";
        $resultSite = mysqli_query($conn, $select_query_sub);

        while ($rowSub = mysqli_fetch_assoc($resultSite)){
            $shopSeq = $rowSub['seq'];
			$shopname = $rowSub['shopname'];
			$coupon = $rowSub['res_coupon'];

            if(array_key_exists($rowSub['res_date'].$rowSub['res_busnum'], $arrSeatInfo)){
                $arrSeatInfo[$rowSub['res_date'].$rowSub['res_busnum']] .= '      - '.$rowSub['res_seat'].'번 ('.$rowSub['res_spointname'].' -> '.$rowSub['res_epointname'].')\n';
            }else{
                $arrSeatInfo[$rowSub['res_date'].$rowSub['res_busnum']] = '    ['.$rowSub['res_date'].'] '.fnBusNum($rowSub['res_busnum']).'\n      - '.$rowSub['res_seat'].'번 ('.$rowSub['res_spointname'].' -> '.$rowSub['res_epointname'].')\n';
            }
        }
        
		//좌석 / 정류장 정보
        foreach($arrSeatInfo as $x) {
            $busSeatInfo .= $x.'\n';
        }

		
		if($shopSeq == 7){ //양양
			$resparam = "surfbus_yy";
		}else{ //동해
			$resparam = "surfbus_dh";			
        }

        $busSeatInfo = $busSeatInfo;
		$pointMsg = "  ▶ 탑승시간/위치 안내\n      - https://actrip.co.kr/pointlist\n";

        if($etc != ''){
            $etcMsg = '  ▶ 요청사항\n      '.$etc.'\n';
        }

		$infomsg = "\n      - 교통상황으로 인해 정류장에 지연 도착할 수 있으니 양해부탁드립니다.\n      - 이용일, 탑승시간, 탑승위치 꼭 확인 부탁드립니다.\n      - 탑승시간 10분전에는 도착해주세요~";

        $msgTitle = '액트립 양양 셔틀버스 예약안내';
		$kakaoMsg = $msgTitle.'\n\n안녕하세요. '.$userName.'님\n액트립 셔틀버스를 예약해주셔서 감사합니다.\n좌석/정류장 예약이 확정되었습니다. \n\n액트립 셔틀버스 예약정보\n ▶ 예약번호 : [예약확정]\n ▶ 예약자 : '.$userName.'\n ▶ 좌석안내\n'.$busSeatInfo.$pointMsg.$etcMsg.'---------------------------------\n ▶ 안내사항'.$infomsg;

		$tempName = "frip_bus02"; //셔틀버스 예약확정
		$btn_ResSearch = "orderview?num=1&resNumber=".$ResNumber; //예약조회
		$btn_ResChange = "pointchange?num=1&resNumber=".$ResNumber; //좌석/정류장 변경
		$btn_ResGPS = "surfbusgps"; //서핑버스 실시간위치 조회
		$btn_ResPoint = "pointlist"; //문의하기
		$btn_Notice = "";
		$btn_ResContent = ""; //예약 상세안내

		// 고객 카카오톡 발송
		$arrKakao = array(
			"gubun"=> "bus"
			, "admin"=> "N"
			, "smsTitle"=> $msgTitle
			, "userName"=> $userName
			, "tempName"=> $tempName
			, "kakaoMsg"=>$kakaoMsg
			, "userPhone"=> $userPhone
			, "btn_ResSearch"=> $btn_ResSearch
			, "btn_ResChange"=> $btn_ResChange
			, "btn_ResGPS"=> $btn_ResGPS
            , "btn_ResPoint"=> $btn_ResPoint
			, "btn_Notice"=> $btn_Notice
			, "btn_ResContent"=> $btn_ResContent
			, "smsOnly"=>"N"
			, "PROD_NAME"=>"서핑버스"
			, "PROD_URL"=>$shopseq
			, "PROD_TYPE"=>"bus"
			, "RES_CONFIRM"=>"3"
		);
		$arrRtn = sendKakao($arrKakao); //알림톡 발송

		// 카카오 알림톡 DB 저장 START
		$select_query = kakaoDebug($arrKakao, $arrRtn);            
		$result_set = mysqli_query($conn, $select_query);

        if(strrpos($usermail, "@") > 0){
            // $to .= ','.$usermail;
			$to = $usermail;

			$info1_title = "좌석안내";
			$info1 = str_replace('      -', '&nbsp;&nbsp;&nbsp;-', str_replace('\n', '<br>', $busSeatInfo));
			$info2_title = "탑승시간/위치 안내";			
			$info2 = "&nbsp;&nbsp;&nbsp;<a href=\"https://actrip.co.kr/pointlist\" target=\"_blank\" style=\"text-decoration:underline;color:#009e25\" rel=\"noreferrer noopener\">[안내사항 보기]</a>";

			$arrMail = array(
				"gubun"=> "bus"
				, "gubun_step" => 3
				, "gubun_title" => $shopname
				, "mailto"=> $to
				, "mailfrom"=> "surfbus_res@actrip.co.kr"
				, "mailname"=> "actrip"
				, "userName"=> $userName
				, "ResNumber"=> $ResNumber
				, "userPhone" => $userPhone
				, "etc" => $etc
				, "totalPrice1" => ""
				, "totalPrice2" => ""
				, "banknum" => ""
				, "info1_title"=> $info1_title
				, "info1"=> $info1
				, "info2_title"=> $info2_title
				, "info2"=> $info2
			);
			sendMail($arrMail); //메일 발송
		}
    }

	mysqli_query($conn, "COMMIT");
}else if($param == "reskakaodel"){
    $codeseq = $_REQUEST["codeseq"];

	$select_query = "DELETE FROM AT_COUPON_CODE WHERE codeseq = $codeseq";
	$result_set = mysqli_query($conn, $select_query);
	if(!$result_set) goto errGo;
	
	mysqli_query($conn, "COMMIT");
	
}else if($param == "reskakaode2"){
	$user_tel = $_REQUEST['user_tel'];
	$user_name = $_REQUEST['user_name'];

	$msgTitle = '액트립 셔틀버스 예약안내';
	$btnList = '';
	$tempName = "at_res_step4";
	$arryKakao = '';
 
	$kakaoMsg = $msgTitle.'\n\n안녕하세요.\n액트립x프립 셔틀버스를 이용해주셔서 감사드립니다.\n카카오톡으로 예약링크를 안내드렸으나 예약이 안되고 있기에 다시 한번 안내드립니다.\n\n예약자 정보 안내\n ▶ 예약자 : '.$user_name.'\n---------------------------------\n ▶ 안내사항\n액트립x프립 셔틀버스는 실시간 예약으로 이루어지고 있습니다.\n예약을 늦게 하셔서 잔여석이 없을 경우 취소 처리 될 수 있으니 이점 참고하셔서 빠른 예약부탁드립니다.\n\n고객님들 모두 불편함 없는 즐거운 주말 여행이 되시길 바랍니다.\n\n감사합니다~';

	$arryKakao .= '{"message_type":"at","phn":"'.$user_tel.'","profile":"70f9d64c6d3b9d709c05a6681a805c6b27fc8dca","tmplId":"'.$tempName.'","msg":"'.$kakaoMsg.'",'.$btnList.'"smsKind":"L","msgSms":"'.$kakaoMsg.'","smsSender":"010-3308-6080","smsLmsTit":"'.$msgTitle.'","smsOnly":"N"}';
	$rtnMsg = '['.$arryKakao.']';

	$curl = curl_init();

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

	mysqli_query($conn, "COMMIT");
	
}else if($param == "reskakao"){ //버스 예약안내 카톡 : 타채널예약건
    $resbus = $_REQUEST["resbus"];
    $userName = $_REQUEST["username"];
    $userPhone = $_REQUEST["userphone"];
    $reschannel = $_REQUEST["reschannel"];
	
    $resDate1 = $_REQUEST["resDate1"];
    $resDate2 = $_REQUEST["resDate2"];
    $resbusseat1 = $_REQUEST["resbusseat1"];
    $resbusseat2 = $_REQUEST["resbusseat2"];

	//7:서핑버스 네이버쇼핑, 10:네이버예약, 11:프립, 17:프립 패키지, 12:마이리얼트립, 14:망고서프패키지, 15:서프엑스
	//16:클룩
	//18:프립-니지모리  19:프립-제천
	function RandString($len){
		$return_str = "";
	
		for ( $i = 0; $i < $len; $i++ ) {
			mt_srand((double)microtime()*1000000);
			$return_str .= substr('123456789ABCDEFGHIJKLMNPQRSTUVWXYZ', mt_rand(0,33), 1);
		}
	
		return $return_str;
	}

	$coupon_code = RandString(5);
	$user_ip = $_SERVER['REMOTE_ADDR'];
    $add_date = date("Y-m-d");

	if($resbus == "YY"){ //양양행
		$seatName = "양양행";
	}else{ //동해행
		$seatName = "동해행";
	}

	$infomsg = "\n      - [예약하기] 버튼을 클릭해서 좌석/정류장을 예약해주세요.";
	$infomsg .= "\n      - 셔틀버스 잔여석이 없을 경우 예약이 취소 될 수 있으니 빠른 예약부탁드려요~";
	
	$channelMsg = "액트립 셔틀버스를 예약해주셔서 감사합니다.\n셔틀버스 좌석/정류장 예약관련 안내드립니다.";

	$msgTitle = ' 셔틀버스';
	if($reschannel == 11){ //프립
		$msgTitle = 'x프립버스';
	}else if($reschannel == 17){ //프립 패키지
		$msgTitle = 'x프립 서핑패키지';
	}else if($reschannel == 12){ //마이리얼트립

	}else if($reschannel == 14){ //망고서프 패키지

	}else if($reschannel == 15){ //서프엑스
		$msgTitle = 'x서프엑스';
	}else if($reschannel == 16){ //클룩
		$msgTitle = 'X클룩 셔틀버스';
	}

	$msgTitle = "액트립$msgTitle 예약안내";

	$resseatMsg = "";
	if($resbusseat1 > 0){ //양양행 좌석예약
		$resseatMsg = "\n    [$seatName] ".$resDate1." / ".$resbusseat1."자리";
	}

	if($resbusseat2 > 0){ //양양행 좌석예약
		$resseatMsg .= "\n    [서울행] ".$resDate2." / ".$resbusseat2."자리";
	}

	$kakaoMsg = $msgTitle.'\n\n안녕하세요. '.$userName.'님\n'.$channelMsg.'\n\n액트립 셔틀버스 예약정보\n ▶ 예약번호 : -\n ▶ 예약자 : '.$userName.'\n ▶ 예약가능 좌석'.$resseatMsg.'\n---------------------------------\n ▶ 안내사항'.$infomsg;
		
	$arrKakao = array(
		"gubun"=> "bus"
		, "admin"=> "N"
		, "smsTitle"=> $msgTitle
		, "userName"=> $userName
		, "tempName"=> "at_bus_kakao"
		, "kakaoMsg"=>$kakaoMsg
		, "userPhone"=> $userPhone
		, "link1"=>"surfbus_res?param=".urlencode(encrypt(date("Y-m-d").'|'.$coupon_code.'|resbus|'.$resDate1.'|'.$resDate2.'|'.$resbusseat1.'|'.$resbusseat2.'|'.$userName.'|'.$userPhone.'|'.$resbus.'|'))
		, "link2"=>""
		, "link3"=>""
		, "link4"=>""
		, "link5"=>""
		, "smsOnly"=>"N"
		, "PROD_NAME"=>"타채널 알림톡발송"
		, "PROD_URL"=>$reschannel
		, "PROD_TYPE"=>"bus_kakao"
		, "RES_CONFIRM"=>"-1"
	);

	$arrRtn = sendKakao($arrKakao); //알림톡 발송

	//------- 쿠폰코드 입력 -----
	$data = json_decode($arrRtn[0], true);
	$kakao_code = $data[0]["code"];
	$kakao_type = $data[0]["data"]["type"];
	$kakao_msgid = $data[0]["data"]["msgid"];
	$kakao_message = $data[0]["message"];
	$kakao_originMessage = $data[0]["originMessage"];

	$userinfo = "$userName|$userPhone|$resDate1|$resbusseat1|$resDate2|$resbusseat2|$kakao_code|$kakao_type|$kakao_message|$kakao_originMessage|$kakao_msgid";
	$select_query = "INSERT INTO `AT_COUPON_CODE` (`couponseq`, `coupon_code`, `seq`, `use_yn`, `add_ip`, `add_date`, `insdate`, `userinfo`) VALUES ('$reschannel', '$coupon_code', 'BUS', 'N', '$user_ip', '$add_date', now(), '$userinfo');";
	$result_set = mysqli_query($conn, $select_query);
 	if(!$result_set) goto errGo;
	//------- 쿠폰코드 입력 -----

/*
	$tmp = '[{"code":"fail","data":{"phn":"82104437000","msgid":"WEB20220105180807623437","type":"L"},"message":"M107:DeniedSenderNumber","originMessage":"K102:InvalidPhoneNumber"}]';

	$tmp = '[{"code":"success","data":{"phn":"821044370009","msgid":"WEB20220105132932579875","type":"L"},"message":"M001","originMessage":"K208:InvalidParameterException"}]';

	$tmp = '[{"code":"success","data":{"phn":"821044370009","msgid":"WEB20220105163752467667","type":"AT"},"message":"K000","originMessage":null}]';

	echo $tmp."<br>";
	$data = json_decode($tmp, true);
	echo $data[0]["code"]."<br>";
	echo $data[0]["message"]."<br>";
	echo $data[0]["originMessage"]."<br>";
	echo $data[0]["data"]["type"]."<br>";
*/

	// 카카오 알림톡 DB 저장 START
	$select_query = kakaoDebug($arrKakao, $arrRtn);            
	$result_set = mysqli_query($conn, $select_query);
	// 카카오 알림톡 DB 저장 END

   mysqli_query($conn, "COMMIT");
	
	// echo $coupon_code." / ";
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