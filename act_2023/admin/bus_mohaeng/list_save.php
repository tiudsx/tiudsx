<?php
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/kakaoalim.php';
include __DIR__.'/../../common/channel_kakaoalim.php';
include __DIR__.'/../../common/func.php';

$success = true;
$datetime = date('Y/m/d H:i'); 

$param = $_REQUEST["resparam"];
$InsUserID = $_REQUEST["userid"];

$intseq = "";
$intseq3 = "";
$to = "lud1@naver.com";

mysqli_query($conn, "SET AUTOCOMMIT=0");
mysqli_query($conn, "BEGIN");

if($param == "changeConfirmNew"){ //셔틀버스 정보 업데이트
	//단일 컬럼
	$resnum = $_REQUEST["resnum"];
	$user_name = $_REQUEST["user_name"];
	$user_tel = $_REQUEST["user_tel"];
	$user_email = $_REQUEST["user_email"];
	$memo = $_REQUEST["memo"]; //직원메모
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

	$busSeatInfoS = "";
	$busSeatInfoE = "";
	$arrSeatInfoS = array();
	$arrSeatInfoE = array();

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
            $shopseq = $rowSub['seq'];
			$shopname = $rowSub['shopname'];
			$coupon = $rowSub['res_coupon'];
			$busGubun = substr($rowSub['res_bus'], 0, 1);

			//$arrPoint = explode("|", fnBusPoint($row['res_spointname'], $row['res_bus']));
			//$RtnBank = "탑승시간 : ".$arrPoint[0]." (".$arrPoint[1].")";

			$pointTime = explode("|", fnBusPoint($rowSub['res_spointname'], $rowSub['res_bus']))[0];

			if($busGubun == "Y" || $busGubun == "E"){ //양양, 동해
				if(array_key_exists($rowSub['res_date'].$rowSub['res_busnum'], $arrSeatInfoS)){
					$arrSeatInfoS[$rowSub['res_date'].$rowSub['res_busnum']] .= '      - '.$rowSub['res_seat'].'번 ('.$rowSub['res_spointname'].' / '.$pointTime.')\n';
				}else{
					$arrSeatInfoS[$rowSub['res_date'].$rowSub['res_busnum']] = '    ['.$rowSub['res_date'].'] '.fnBusNum($rowSub['res_busnum']).'\n      - '.$rowSub['res_seat'].'번 ('.$rowSub['res_spointname'].' / '.$pointTime.')\n';
				}
			}else{
				if(array_key_exists($rowSub['res_date'].$rowSub['res_busnum'], $arrSeatInfoE)){
					$arrSeatInfoE[$rowSub['res_date'].$rowSub['res_busnum']] .= '      - '.$rowSub['res_seat'].'번 ('.$rowSub['res_spointname'].' / '.$pointTime.')\n';
				}else{
					$arrSeatInfoE[$rowSub['res_date'].$rowSub['res_busnum']] = '    ['.$rowSub['res_date'].'] '.fnBusNum($rowSub['res_busnum']).'\n      - '.$rowSub['res_seat'].'번 ('.$rowSub['res_spointname'].' / '.$pointTime.')\n';
				}
			}
        }
        
		// 예약좌석 정보 : 양양행
		foreach($arrSeatInfoS as $x) {
			$busSeatInfoS .= $x;
		}

		// 예약좌석 정보 : 서울행
		foreach($arrSeatInfoE as $x) {
			$busSeatInfoE .= $x;
		}

        $busSeatInfoTotal = " ▶ 좌석안내\n";
        if($busSeatInfoS != ""){
            $busSeatInfoTotal .= $busSeatInfoS;
        }
        if($busSeatInfoE != ""){
            if($busSeatInfoS != ""){
                $busSeatInfoTotal .= "\n";
            }
            $busSeatInfoTotal .= $busSeatInfoE;
        }

        $busSeatInfo = $busSeatInfo;

		if($shopseq == 7){
			$busTypeY = "Y";
			$busTypeS = "S";
			$busTitleName = "양양";
			$resparam = "surfbus_yy";
		}else{
			$busTypeY = "E";
			$busTypeS = "A";    
			$busTitleName = "동해";    
			$resparam = "surfbus_dh";	
		}
        $gubun_title = $busTitleName.' 서핑버스';

		$tempName = "frip_bus02"; //예약확정
		$btn_ResSearch = "orderview?num=1&resNumber=".$ResNumber; //예약조회
		$btn_ResChange = "pointchange?num=1&resNumber=".$ResNumber; //좌석/정류장 변경
		$btn_ResGPS = "surfbusgps"; //서핑버스 실시간위치 조회
		$btn_ResPoint = "pointlist?num=1&resNumber=".$ResNumber; //탑승시간/위치안내
		$btn_Notice = "";
		$btn_ResContent = ""; //예약 상세안내

		$msgInfo = $busSeatInfoTotal;

		// 고객 카카오톡 발송
        $msgTitle = '액트립 서핑버스 예약안내';
		$arrKakao = array(
            "gubun"=> "bus"
            , "admin"=> "N"
            , "tempName"=> $tempName
            , "smsTitle"=> $msgTitle
            , "userName"=> $userName
            , "userPhone"=> $userPhone
            , "msgType"=>$msgType
            , "shopname"=>$gubun_title
            , "MainNumber"=>$ResNumber
            , "msgInfo"=>$msgInfo
            , "btn_ResContent"=> $btn_ResContent
            , "btn_ResSearch"=> $btn_ResSearch
            , "btn_ResChange"=> $btn_ResChange
            , "btn_ResGPS"=> $btn_ResGPS
            , "btn_ResPoint"=> $btn_ResPoint
            , "btn_Notice"=> $btn_Notice
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
	
}else if($param == "reskakaode2"){ //타채널 예약안내 재발송
	$kakao_msgid = $_REQUEST['kakao_msgid'];

	$select_query = "SELECT * FROM AT_RES_SUB WHERE ressubseq IN ($intseq3) ORDER BY res_date, ressubseq";
	$resultSite = mysqli_query($conn, $select_query_sub);

	$select_query = "SELECT * FROM `AT_KAKAO_HISTORY` WHERE prod_type = 'bus_channel' AND response LIKE '%$kakao_msgid%'";
	$result = mysqli_query($conn, $select_query);
	$rowMain = mysqli_fetch_array($result);

	$userName = $rowMain["USER_NAME"];
	$userPhone = $rowMain["USER_TEL"];
	$link1 = $rowMain["KAKAO_BTN1"];
	
	$msgTitle = '모행 셔틀버스 예약안내';
	$arrKakao = array(
		"gubun"=> "bus"
		, "admin"=> "N"
		, "tempName"=> "at_bus_kakao"
		, "smsTitle"=> $msgTitle
		, "userName"=> $userName
		, "userPhone"=> $userPhone
		, "shopname"=> "모행 셔틀버스"
		, "link1"=>$link1
		, "smsOnly"=>"N"
		, "PROD_NAME"=>"타채널 알림톡 재발송"
		, "PROD_URL"=>""
		, "PROD_TYPE"=>"bus_push"
		, "RES_CONFIRM"=>"-1"
	);

	$arrRtn = sendKakao($arrKakao); //알림톡 발송

	// 카카오 알림톡 DB 저장 START
	$select_query = kakaoDebug($arrKakao, $arrRtn);            
	$result_set = mysqli_query($conn, $select_query);
	// 카카오 알림톡 DB 저장 END

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
		$seatName2 = "양양";
	}else{ //동해행
		$seatName = "출발";
		$seatName2 = "동해";
	}

	$resseatMsg = "";
	if($resbusseat1 > 0){ //양양행,동해행 좌석예약
		$resseatMsg = "\n    [$seatName] ".$resDate1." / ".$resbusseat1."자리";
	}

	if($resbusseat2 > 0){ //서울행 좌석예약
		$resseatMsg .= "\n    [복귀] ".$resDate2." / ".$resbusseat2."자리";
	}

	if($reschannel == 31){ //모행
		$link1 = "surfbus_res?param=".urlencode(encrypt(date("Y-m-d").'|'.$coupon_code.'|resbus|'.$resDate1.'|'.$resDate2.'|'.$resbusseat1.'|'.$resbusseat2.'|'.$userName.'|'.$userPhone.'|'.$resbus.'|'.$reschannel.'|'));
		
		$data = array(
			'name' => $userName,
			'shop_name' => '모행 셔틀버스',
			'reservation_name' => $userName,
			'receiver_number' => $userPhone,
			'url' => $link1,
			'seat' => $resseatMsg
			// ,'reservation_information' =>"테스트",
			// 'information' =>"테스트"
		);
		
		//echo json_encode(at_config('acrtip_reservation',$data));
		fnKakaoSend(at_config('acrtip_reservation',$data));
	}else{
		if($reschannel == 11){ //프립
			$prodTitle = 'x프립버스';
			$seatName2 = $seatName2." 프립버스";
		}else if($reschannel == 17 || $reschannel == 20 || $reschannel == 21 || $reschannel == 22 || $reschannel == 26 || $reschannel == 27 || $reschannel == 28 || $reschannel == 29){ //당일 패키지
			$prodTitle = ' 서핑패키지';
			if($reschannel == 17 || $reschannel == 26){
				$seatName2 = $seatName2." 마린서프";
			}else if($reschannel == 20 || $reschannel == 27){
				$seatName2 = $seatName2." 인구서프";
			}else if($reschannel == 21 || $reschannel == 28){
				$seatName2 = "서프팩토리 동해점";
			}else if($reschannel == 22 || $reschannel == 29){
				$seatName2 = "힐링 서핑캠프";
			}
		}else if($reschannel == 12){ //마이리얼트립

		}else if($reschannel == 14){ //망고서프 패키지

		}else if($reschannel == 15){ //서프엑스
			$prodTitle = 'x서프엑스 서핑버스';
			$seatName2 = $seatName2." 서핑버스x서프엑스";
		}else if($reschannel == 16){ //클룩
			$prodTitle = 'X클룩 서핑버스';
			$seatName2 = $seatName2." 서핑버스x클룩";
		}else if($reschannel == 23){ //금진 브라보
			$prodTitle = 'x브라보서프 서핑버스';
			$seatName2 = $seatName2." 서핑버스x브라보서프";
		}else if($reschannel == 30){ //엑스크루
			$prodTitle = 'x엑스크루 서핑버스';
			$seatName2 = $seatName2." 서핑버스x엑스크루";
		}else{		
			$seatName2 = $seatName2." 서핑버스";
		}

		$msgTitle = "액트립$prodTitle 예약안내";
		$link1 = "surfbus_res?param=".urlencode(encrypt(date("Y-m-d").'|'.$coupon_code.'|resbus|'.$resDate1.'|'.$resDate2.'|'.$resbusseat1.'|'.$resbusseat2.'|'.$userName.'|'.$userPhone.'|'.$resbus.'|'.$reschannel.'|'));
		$arrKakao = array(
			"gubun"=> "bus"
			, "admin"=> "N"
			, "tempName"=> "at_bus_kakao"
			, "smsTitle"=> $msgTitle
			, "userName"=> $userName
			, "userPhone"=> $userPhone
			, "shopname"=> $seatName2
			, "msgInfo"=>$resseatMsg
			, "link1"=> $link1
			, "smsOnly"=>"N"
			, "PROD_NAME"=>"타채널 알림톡발송"
			, "PROD_URL"=>$reschannel
			, "PROD_TYPE"=>"bus_channel"
			, "RES_CONFIRM"=>"-1"
		);

		$arrRtn = sendKakao($arrKakao); //알림톡 발송
	}
	//------- 쿠폰코드 입력 -----
	$data = json_decode($arrRtn[0], true);
	$kakao_code = $data[0]["code"];
	$kakao_type = $data[0]["data"]["type"];
	$kakao_msgid = $data[0]["data"]["msgid"];
	$kakao_message = $data[0]["message"];
	$kakao_originMessage = $data[0]["originMessage"];

	$userinfo = "$userName|$userPhone|$resDate1|$resbusseat1|$resDate2|$resbusseat2|$kakao_code|$kakao_type|$kakao_message|$kakao_originMessage|$kakao_msgid|$resbus|$reschannel";
	$select_query = "INSERT INTO `AT_COUPON_CODE` (`couponseq`, `coupon_code`, `seq`, `use_yn`, `add_ip`, `add_date`, `insdate`, `userinfo`, `etc`) VALUES ('$reschannel', '$coupon_code', 'BUS', 'N', '$user_ip', '$add_date', now(), '$userinfo', '$link1');";
	$result_set = mysqli_query($conn, $select_query);
 	if(!$result_set) goto errGo;
	//------- 쿠폰코드 입력 -----

	// 카카오 알림톡 DB 저장 START
	$select_query = kakaoDebug($arrKakao, $arrRtn);            
	$result_set = mysqli_query($conn, $select_query);
	// 카카오 알림톡 DB 저장 END

   mysqli_query($conn, "COMMIT");
	
	// echo $coupon_code." / ";
}else if($param == "busDatadel"){ //데이터 완전삭제
	$resnum = $_REQUEST["resnum"];

	$select_query = "DELETE FROM AT_RES_MAIN WHERE resnum = '$resnum'";
	$result_set = mysqli_query($conn, $select_query);

	$errmsg = $select_query;
	if(!$result_set) goto errGo;

	$select_query = "DELETE FROM AT_RES_SUB WHERE resnum = '$resnum'";
	$result_set = mysqli_query($conn, $select_query);

	$errmsg = $select_query;
	if(!$result_set) goto errGo;

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
