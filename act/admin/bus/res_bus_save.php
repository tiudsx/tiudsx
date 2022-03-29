<?php
include __DIR__.'/../../db.php';
include __DIR__.'/../../surf/surfkakao.php';
include __DIR__.'/../../surf/surfmail.php';
include __DIR__.'/../../surf/surffunc.php';

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

// $rtn = $param.'/'.$_REQUEST["chkCancel"].'/'.$_REQUEST["selConfirm"].'/'.$_REQUEST["MainNumber"].'/'.$_REQUEST["memo"];
if($param == "changeConfirm"){ //상태 정보 업데이트
    $chkCancel = $_REQUEST["chkCancel"];
    $selConfirm = $_REQUEST["selConfirm"];
    $resnum = $_REQUEST["MainNumber"];
    $memo = $_REQUEST["memo"];

	//================= 예약상태 및 메모 저장 =================
	$select_query = "UPDATE AT_RES_MAIN SET memo = '$memo' WHERE resnum = $resnum";
	$result_set = mysqli_query($conn, $select_query);
 	if(!$result_set) goto errGo;

	for($i = 0; $i < count($chkCancel); $i++){
		$insdate1 = "";
		if($selConfirm[$i] == 3){
			$insdate1 = ",confirmdate = now()";
			$intseq3 .= $chkCancel[$i].",";
		}

		$select_query = "UPDATE `AT_RES_SUB` 
					   SET res_confirm = ".$selConfirm[$i]."
						".$insdate1."
						  ,upddate = now()
						  ,upduserid = '".$InsUserID."'
					WHERE ressubseq = ".$chkCancel[$i].";";
		$result_set = mysqli_query($conn, $select_query);
		if(!$result_set) goto errGo;
	}

    $intseq3 .= '0';

    $arrSeatInfo = array();
    $arrStopInfo = array();

    $select_query = "SELECT user_name, user_tel, user_email, etc, memo FROM `AT_RES_MAIN` WHERE resnum = $resnum";

    $result = mysqli_query($conn, $select_query);
    $rowMain = mysqli_fetch_array($result);

    $ResNumber = $resnum;
	$userName = $rowMain["user_name"];
	$etc = $rowMain["etc"];
	$userPhone = $rowMain["user_tel"];
	$usermail = $rowMain["user_email"];

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

            $arrData = explode("|", fnBusPoint($rowSub['res_spointname'], $rowSub['res_busnum'], 0));
            $arrStopInfo[$rowSub['res_spointname']] = '    ['.$rowSub['res_spointname'].'] '.$arrData[0].'\n      - '.$arrData[1].'\n';
        }
        
        foreach($arrSeatInfo as $x) {
            $busSeatInfo .= $x;
        }

        foreach($arrStopInfo as $x) {
            $busStopInfo .= $x;
        }

        $busSeatInfo = $busSeatInfo;
        $pointMsg = '  ▶ 탑승시간/위치 안내\n'.$busStopInfo;

        if($etc != ''){
            $etcMsg = '  ▶ 요청사항\n      '.$etc.'\n';
        }

		$infomsg = "\n      - 이용일, 탑승시간, 탑승위치 꼭 확인 부탁드립니다.\n      - 탑승시간 5분전에는 도착해주세요~";
		if($coupon == "NABUSA" || $coupon == "NABUSB"){
			$infomsg .= "\n      - 취소 및 환불신청은 네이버에서 해주세요~";
		}else if($coupon == "NABUSC"){
			$infomsg .= "\n      - 취소 및 환불신청은 프립에서 해주세요~";
		}

        $msgTitle = '액트립 '.$shopname.' 예약안내';
		$kakaoMsg = $msgTitle.'\n안녕하세요. '.$userName.'님\n\n액트립 예약정보 [예약확정]\n ▶ 예약번호 : '.$ResNumber.'\n ▶ 예약자 : '.$userName.'\n ▶ 좌석안내\n'.$busSeatInfo.$pointMsg.$etcMsg.'---------------------------------\n ▶ 안내사항'.$infomsg.'\n\n ▶ 문의\n      - 010.3308.6080\n      - http://pf.kakao.com/_HxmtMxl';

		if($shopSeq == 7){
			$resparam = "surfbus_yy";
		}else{
			$resparam = "surfbus_dh";			
        }
        $arrKakao = array(
			"gubun"=> "bus"
			, "admin"=> "N"
			, "smsTitle"=> $msgTitle
			, "userName"=> $userName
			, "tempName"=> "at_res_bus1"
			, "kakaoMsg"=>$kakaoMsg
			, "userPhone"=> $userPhone
			, "link1"=>"orderview?num=1&resNumber=".$ResNumber //예약조회/취소
			, "link2"=>"surfbusgps" //셔틀버스 실시간위치 조회
			, "link3"=>"pointlist?resparam=".$resparam //셔틀버스 탑승 위치확인
			, "link4"=>"eatlist" //제휴업체 목록
			, "link5"=>"event" //공지사항
			, "smsOnly"=>"N"
			, "PROD_NAME"=>"서핑버스"
			, "PROD_URL"=>$shopSeq
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
			$info2 = str_replace('      -', '&nbsp;&nbsp;&nbsp;-', str_replace('\n', '<br>', $busStopInfo));

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
    //==========================카카오 메시지 발송 End ==========================
}else if($param == "busmodify"){ //셔틀버스 정보 업데이트
	$res_date = $_REQUEST["res_date"];
	$res_busnum = $_REQUEST["res_busnum"];
	$res_seat = $_REQUEST["res_seat"];
	$res_spointname = $_REQUEST["res_spointname"];
	$res_epointname = $_REQUEST["res_epointname"];
	$res_confirm = $_REQUEST["res_confirm"];
	$res_price = $_REQUEST["res_price"];
	$res_price_coupon = $_REQUEST["res_price_coupon"];
	$rtn_charge_yn = $_REQUEST["rtn_charge_yn"];
	$insdate = $_REQUEST["insdate"];
	$confirmdate = $_REQUEST["confirmdate"];

	$user_name = $_REQUEST["user_name"];
	$user_tel = $_REQUEST["user_tel"];
	$user_email = $_REQUEST["user_email"];
	$memo = $_REQUEST["memo"];

	//$msgYN = $_REQUEST["msgYN"];
	$ressubseq = $_REQUEST["ressubseq"];
	$resnum = $_REQUEST["resnum"];

	if($res_price_coupon <= 100){ //퍼센트 할인
		$res_totalprice = $res_price * (1 - ($res_price_coupon / 100));
	}else{ //금액할인
		$res_totalprice = $res_price - $res_price_coupon;
	}
	
    $select_query = "UPDATE AT_RES_SUB 
                    SET res_date = '".$res_date."'
                        ,res_bus = '".$res_busnum."'
                        ,res_busnum = '".$res_busnum."'
                        ,res_seat = ".$res_seat."
                        ,res_spoint = '".$res_spointname."'
                        ,res_spointname = '".$res_spointname."'
                        ,res_epoint = '".$res_epointname."'
                        ,res_epointname = '".$res_epointname."'
                        ,res_confirm = '".$res_confirm."'
                        ,res_price = '".$res_price."'
                        ,res_price_coupon = '".$res_price_coupon."'
                        ,res_totalprice = '".$res_totalprice."'
                        ,rtn_charge_yn = '".$rtn_charge_yn."'
                        ,insdate = '".$insdate."'
                        ,upddate = now()
                        ,upduserid = 'admin'
                        ,confirmdate = '".$confirmdate."'
                WHERE ressubseq = ".$ressubseq." AND resnum = '".$resnum."';";
    $result_set = mysqli_query($conn, $select_query);
    if(!$result_set) goto errGo;

    $select_query = "UPDATE `AT_RES_MAIN` 
                    SET user_name = '".$user_name."'
                        ,memo = '".$memo."'
                        ,user_tel = '".$user_tel."'
                        ,user_email = '".$user_email."'
                WHERE resnum = '".$resnum."';";
    $result_set = mysqli_query($conn, $select_query);
    if(!$result_set) goto errGo;

	mysqli_query($conn, "COMMIT");

}else if($param == "reskakaodel"){
    $codeseq = $_REQUEST["codeseq"];

	$select_query = "DELETE FROM AT_COUPON_CODE WHERE codeseq = $codeseq";
	$result_set = mysqli_query($conn, $select_query);
	if(!$result_set) goto errGo;
	
	mysqli_query($conn, "COMMIT");
	
}else if($param == "reskakao"){ //버스 예약안내 카톡 : 타채널예약건
    $userName = $_REQUEST["username"];
    $userPhone = $_REQUEST["userphone"];
    $reschannel = $_REQUEST["reschannel"];
	
    $resDate1 = $_REQUEST["resDate1"];
    $resDate2 = $_REQUEST["resDate2"];
    $resbusseat1 = $_REQUEST["resbusseat1"];
    $resbusseat2 = $_REQUEST["resbusseat2"];

	//7:서핑버스 네이버쇼핑, 10:네이버예약, 11:프립, 12:마이리얼트립, 14:망고서프패키지, 15:서프엑스
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

	$infomsg = "\n      - [예약하기] 버튼을 클릭해서 좌석/정류장을 예약해주세요.";
	// $infomsg .= "\n      - 예약화면에서 안내된 쿠폰코드를 입력해주세요.";
	$infomsg .= "\n      - 예약하신 인원수와 동일한 좌석수로 예약해주세요.";

	if($reschannel == 7){ //네이버쇼핑

	}else if($reschannel == 10){ //네이버예약

	}else if($reschannel == 11){ //프립

	}else if($reschannel == 12){ //마이리얼트립

	}else if($reschannel == 14){ //망고서프 패키지

	}else if($reschannel == 15){ //서프엑스

	}else if($reschannel == 16){ //클룩

	}

	$resseatMsg = "";
	if($resbusseat1 > 0){ //양양행 좌석예약
		$resseatMsg = "\n    [양양행] ".$resDate1." / ".$resbusseat1."자리";
	}

	if($resbusseat2 > 0){ //양양행 좌석예약
		$resseatMsg .= "\n    [서울행] ".$resDate2." / ".$resbusseat2."자리";
	}

	$msgTitle = '액트립 서핑버스 예약안내';
	//$kakaoMsg = $msgTitle.'\n\n안녕하세요. '.$userName.'님\n액트립 서핑버스 좌석예약 안내입니다\n\n액트립 셔틀버스 예약코드\n ▶ 예약번호 : -\n ▶ 예약자 : '.$userName.'\n ▶ 쿠폰코드 : '.$coupon_code.'\n ▶ 예약가능 좌석'.$resseatMsg.'\n---------------------------------\n ▶ 안내사항'.$infomsg.'\n\n ▶ 문의\n      - http://pf.kakao.com/_HxmtMxl';
	$kakaoMsg = $msgTitle.'\n\n안녕하세요. '.$userName.'님\n액트립 서핑버스 좌석예약 안내입니다\n\n액트립 셔틀버스 예약정보\n ▶ 예약번호 : -\n ▶ 예약자 : '.$userName.'\n ▶ 예약가능 좌석'.$resseatMsg.'\n---------------------------------\n ▶ 안내사항'.$infomsg.'\n\n ▶ 문의\n      - http://pf.kakao.com/_HxmtMxl';
		
	$arrKakao = array(
		"gubun"=> "bus"
		, "admin"=> "N"
		, "smsTitle"=> $msgTitle
		, "userName"=> $userName
		, "tempName"=> "at_bus_kakao"
		, "kakaoMsg"=>$kakaoMsg
		, "userPhone"=> $userPhone
		, "link1"=>"surfbus_yy?param=".urlencode(encrypt(date("Y-m-d").'|'.$coupon_code.'|resbus|'.$resDate1.'|'.$resDate2.'|'.$resbusseat1.'|'.$resbusseat2.'|'.$userName.'|'.$userPhone.'|'))
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
