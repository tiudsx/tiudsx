<?php
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/kakaoalim.php';
include __DIR__.'/../../common/func.php';

$success = true;
$datetime = date('Y/m/d H:i'); 

$param = $_REQUEST["resparam"];

$errmsg = "";
$intseq = "";
$intseq3 = "";
$to = "lud1@naver.com";

mysqli_query($conn, "SET AUTOCOMMIT=0");
mysqli_query($conn, "BEGIN");

if($param == "solkakao1"){ //카톡 단일건 발송
    $resseq = $_REQUEST["resseq"];

    $select_query = "SELECT user_name, user_tel FROM `AT_SOL_RES_MAIN` WHERE resseq = $resseq";
    $result = mysqli_query($conn, $select_query);
    $rowMain = mysqli_fetch_array($result);

	$userName = $rowMain["user_name"];
	$userPhone = $rowMain["user_tel"];

    //==========================카카오 메시지 발송 ==========================
	$msgTitle = '솔게스트하우스&솔서프 예약안내';
	$arrKakao = array(
		"gubun"=> $code
		, "admin"=> "N"
		, "tempName"=> "at_surf_step3"
		, "smsTitle"=> $msgTitle
		, "userName"=> $userName
		, "userPhone"=> $userPhone
		, "link1"=>"sol_kakao?num=1&seq=".urlencode(encrypt($resseq)) //예약조회/취소
		, "link2"=>"sol_location?seq=".urlencode(encrypt($resseq)) //지도로 위치보기
		, "link3"=>"sol_location?seq=".urlencode(encrypt($resseq)) //이벤트
		, "smsOnly"=>"N"
		, "PROD_NAME"=>"솔게하"
		, "PROD_URL"=>""
		, "PROD_TYPE"=>"sol_complete"
		, "RES_CONFIRM"=>"-1"
	);

	$arrRtn = sendKakao($arrKakao); //알림톡 발송

	//------- 알림톡 디버깅 -----
	$data = json_decode($arrRtn[0], true);
	$kakao_code = $data[0]["code"];
	$kakao_type = $data[0]["data"]["type"];
	$kakao_msgid = $data[0]["data"]["msgid"];
	$kakao_message = $data[0]["message"];
	$kakao_originMessage = $data[0]["originMessage"];

	$userinfo = "$userName|$userPhone|$datetime||||$kakao_code|$kakao_type|$kakao_message|$kakao_originMessage|$kakao_msgid";

	// 카카오 알림톡 DB 저장 START
	$select_query = kakaoDebug($arrKakao, $arrRtn);            
	$result_set = mysqli_query($conn, $select_query);
	// 카카오 알림톡 DB 저장 END

	$select_query = "UPDATE `AT_SOL_RES_MAIN` SET res_kakao = res_kakao + 1, userinfo = '".$userinfo."' WHERE resseq = $resseq";
	$result_set = mysqli_query($conn, $select_query);
	if(!$result_set) goto errGo;
	
	mysqli_query($conn, "COMMIT");
	//==========================카카오 메시지 발송 End ==========================
}else if($param == "solkakaoAll"){
	$chkresseq = $_REQUEST["chkresseq"];
	
	for($i = 0; $i < count($chkresseq); $i++){
		$resseq = $chkresseq[$i];

		$select_query = "SELECT user_name, user_tel FROM `AT_SOL_RES_MAIN` WHERE resseq = $resseq";
		$result = mysqli_query($conn, $select_query);
		$rowMain = mysqli_fetch_array($result);
	
		$userName = $rowMain["user_name"];
		$userPhone = $rowMain["user_tel"];
	
		//==========================카카오 메시지 발송 ==========================
		$msgTitle = '솔게스트하우스&솔서프 예약안내';
		$arrKakao = array(
			"gubun"=> $code
			, "admin"=> "N"
			, "tempName"=> "at_surf_step3"
			, "smsTitle"=> $msgTitle
			, "userName"=> $userName
			, "userPhone"=> $userPhone
			, "link1"=>"sol_kakao?num=1&seq=".urlencode(encrypt($resseq)) //예약조회/취소
			, "link2"=>"sol_location?seq=".urlencode(encrypt($resseq)) //지도로 위치보기
			, "link3"=>"sol_location?seq=".urlencode(encrypt($resseq)) //이벤트
			, "smsOnly"=>"N"
			, "PROD_NAME"=>"솔게하"
			, "PROD_URL"=>""
			, "PROD_TYPE"=>"sol_complete"
			, "RES_CONFIRM"=>"-1"
		);

		$arrRtn = sendKakao($arrKakao); //알림톡 발송

		//------- 알림톡 디버깅 -----
		$data = json_decode($arrRtn[0], true);
		$kakao_code = $data[0]["code"];
		$kakao_type = $data[0]["data"]["type"];
		$kakao_msgid = $data[0]["data"]["msgid"];
		$kakao_message = $data[0]["message"];
		$kakao_originMessage = $data[0]["originMessage"];

		$userinfo = "$userName|$userPhone|$datetime||||$kakao_code|$kakao_type|$kakao_message|$kakao_originMessage|$kakao_msgid";

		// 카카오 알림톡 DB 저장 START
		$select_query = kakaoDebug($arrKakao, $arrRtn);            
		$result_set = mysqli_query($conn, $select_query);
		// 카카오 알림톡 DB 저장 END

		$select_query = "UPDATE `AT_SOL_RES_MAIN` SET res_kakao = res_kakao + 1, userinfo = '".$userinfo."' WHERE resseq = $resseq";
		$result_set = mysqli_query($conn, $select_query);
		if(!$result_set) goto errGo;
	}
		
	mysqli_query($conn, "COMMIT");
	//==========================카카오 메시지 발송 End ==========================
}else if($param == "solrentyn"){ //렌탈 상태여부 변경
	$subseq = $_REQUEST["subseq"];
	$rentyn = $_REQUEST["rentyn"];

	$select_query = "UPDATE `AT_SOL_RES_SUB` SET surfrentYN = '".$rentyn."'
					WHERE ressubseq = ".$subseq.";";
	$result_set = mysqli_query($conn, $select_query);
	if(!$result_set) goto errGo;
	
	mysqli_query($conn, "COMMIT");

}else if($param == "soldel"){
	$resseq = $_REQUEST["resseq"];

	//예약 메인데이터 삭제
	$select_query = "DELETE FROM AT_SOL_RES_MAIN WHERE resseq = $resseq";
	$result_set = mysqli_query($conn, $select_query);

	$errmsg = $select_query;
	if(!$result_set) goto errGo;

	//예약 상세데이터 삭제
	$select_query = "DELETE FROM AT_SOL_RES_SUB WHERE resseq = $resseq";
	$result_set = mysqli_query($conn, $select_query);

	$errmsg = $select_query;
	if(!$result_set) goto errGo;

	mysqli_query($conn, "COMMIT");
}else if($param == "soladd"){
	$resseq = $_REQUEST["resseq"];
	$res_adminname = $_REQUEST["res_adminname"];
	$user_name = $_REQUEST["user_name"];
	// $user_tel = $_REQUEST["user_tel1"]."-".$_REQUEST["user_tel2"]."-".$_REQUEST["user_tel3"];
	$user_tel = $_REQUEST["user_tel"];

	$res_stayshop = $_REQUEST["res_stayshop"];
	$res_staysdate = $_REQUEST["res_staysdate"];
	$res_stayedate = $_REQUEST["res_stayedate"];
	$res_staysex = $_REQUEST["res_staysex"];
	$res_stayM = $_REQUEST["res_stayM"];
	$res_stayroom = $_REQUEST["res_stayroom"];
	$res_staynum = $_REQUEST["res_staynum"];
	$res_company = $_REQUEST["res_company"];
	$res_bbqdate = $_REQUEST["res_bbqdate"];
	$res_bbq = $_REQUEST["res_bbq"];

	$res_surfshop = $_REQUEST["res_surfshop"];
	$res_surfdate = $_REQUEST["res_surfdate"];
	$res_surftime = $_REQUEST["res_surftime"];
	$res_surfM = $_REQUEST["res_surfM"];
	$res_surfW = $_REQUEST["res_surfW"];
	$res_rent = $_REQUEST["res_rent"];
	$res_rentM = $_REQUEST["res_rentM"];
	$res_rentW = $_REQUEST["res_rentW"];

	$memo = $_REQUEST["memo"];
	$memo2 = $_REQUEST["memo2"];
	$res_confirm = $_REQUEST["res_confirm"];
	$res_kakao = $_REQUEST["res_kakao"];

	$kakaocnt = 0;
	if($res_kakao == "Y"){
		$kakaocnt ++;
	}


	if($resseq == ""){
		$ResNumber = '4'.time().substr(mt_rand(0, 99) + 100, 1, 2); //예약번호 랜덤생성

		//메인 정보 등록
		$select_query = "INSERT INTO `AT_SOL_RES_MAIN`(`resnum`, `admin_user`, `res_confirm`, `res_kakao`, `res_kakao_chk`, `res_room_chk`, `res_company`, `user_name`, `user_tel`, `memo`, `memo2`, `history`, `insdate`) VALUES ('$ResNumber', '$res_adminname', '$res_confirm', 0, 'N', 'N', '$res_company', '$user_name', '$user_tel', '$memo', '$memo2', '', now())";
		$result_set = mysqli_query($conn, $select_query);
		$seq = mysqli_insert_id($conn);

		$errmsg = $select_query;
		if(!$result_set) goto errGo;
	}else{
		//메인 정보 수정
		$select_query = "UPDATE `AT_SOL_RES_MAIN` SET 
			`admin_user`='$res_adminname'
			,`res_confirm`='$res_confirm'
			,`res_company`='$res_company'
			,`user_name`='$user_name'
			,`user_tel`='$user_tel'
			,`memo`='$memo'
			,`memo2`='$memo2'
			,`history`= CONCAT(history,'$res_adminname:".date("Y-m-d A h:i:s")."@')
		WHERE resseq = $resseq";
		$result_set = mysqli_query($conn, $select_query);

		$errmsg = $select_query;
		if(!$result_set) goto errGo;

		$select_query = "DELETE FROM AT_SOL_RES_SUB WHERE resseq = $resseq";
		$result_set = mysqli_query($conn, $select_query);

		$errmsg = $select_query;
		if(!$result_set) goto errGo;

		$seq = $resseq;
	}

	//숙박 & 바베큐 정보 등록
	for($i = 1; $i < count($res_stayshop); $i++){
		if(!($res_stayshop[$i] == "N" && $res_bbq[$i] == "N")){
			$prod_name = $res_stayshop[$i];
			$sdate = null;
			$edate = null;
			$resdate = null;
			$staysex = $res_staysex[$i];
			$stayroom = null;
			$staynum = null;
			$bbq = $res_bbq[$i];
			$stayM = $res_stayM[$i];

			if($res_stayshop[$i] != "N"){
				$sdate = $res_staysdate[$i];
				$edate = $res_stayedate[$i];
				$stayroom = $res_stayroom[$i];
				$staynum = $res_staynum[$i];

				$eDate2 = date("Y-m-d", strtotime($edate." -1 day"));
				$select_query = "SELECT * FROM AT_SOL_RES_MAIN as a INNER JOIN AT_SOL_RES_SUB as b
									ON a.resseq = b.resseq
									WHERE b.res_type = 'stay' 
										AND b.prod_name = '솔게스트하우스'
										AND b.stayroom = $stayroom
										AND b.staynum = $staynum
										AND a.res_confirm IN ('대기','확정')
										AND (('$sdate' BETWEEN b.sdate AND DATE_ADD(b.edate, INTERVAL -1 DAY) OR '$eDate2' BETWEEN b.sdate AND DATE_ADD(b.edate, INTERVAL -1 DAY))
											OR (b.sdate BETWEEN '$sdate' AND '$eDate2' OR DATE_ADD(b.edate, INTERVAL -1 DAY) BETWEEN '$sdate' AND '$eDate2'))";
				$result_setlist = mysqli_query($conn, $select_query);
				$count = mysqli_num_rows($result_setlist);
				
				if($count > 0){
					goto errGoRoom;
				}

				$kaka_stay = "숙박,";
			}

			if($res_bbqdate[$i] != ""){
				$resdate = $res_bbqdate[$i];
				$kaka_bbq = "바베큐,";
			}

			$select_query = "INSERT INTO `AT_SOL_RES_SUB`(`resseq`, `res_type`, `prod_name`, `sdate`, `edate`, `resdate`, `staysex`, `stayroom`, `staynum`, `bbq`, `stayM`) VALUES ($seq, 'stay', '$prod_name', '$sdate', '$edate', '$resdate', '$staysex', '$stayroom', '$staynum', '$bbq', $stayM)";
			$result_set = mysqli_query($conn, $select_query);

			$errmsg = $select_query;
			if(!$result_set) goto errGo;
		}
	}

	
	//강습 & 렌탈 정보 등록
	for($i = 1; $i < count($res_surfshop); $i++){
		if(!($res_surfshop[$i] == "N" && $res_rent[$i] == "N")){
			$prod_name = $res_surfshop[$i];
			$resdate = null;
			$surfrent = $res_rent[$i];
			$restime = 0;
			$surfM = 0;
			$surfW = 0;
			$surfrentM = 0;
			$surfrentW = 0;

			if($res_surfshop[$i] != "N"){
				$resdate = $res_surfdate[$i];
				$restime = $res_surftime[$i];
				$surfM = $res_surfM[$i];
				$surfW = $res_surfW[$i];

				$kaka_surf = "서핑강습,";
			}

			if($res_rent[$i] != "N"){
				$resdate = $res_surfdate[$i];
				$surfrentM = $res_rentM[$i];
				$surfrentW = $res_rentW[$i];

				$kaka_rent = "장비렌탈,";
			}

			$select_query = "INSERT INTO `AT_SOL_RES_SUB`(`resseq`, `res_type`, `prod_name`, `resdate`, `restime`, `surfM`, `surfW`, `surfrent`, `surfrentM`, `surfrentW`, `sdate`, `edate`) VALUES ($seq, 'surf', '$prod_name', '$resdate', '$restime', $surfM, $surfW, '$surfrent', $surfrentM, $surfrentW, '', '')";
			$result_set = mysqli_query($conn, $select_query);

			$errmsg = $select_query;
			if(!$result_set) goto errGo;
		}
	}

	//알림톡 발송 (확정, 등록일경우)
	$kakaoRes = $kaka_stay.$kaka_bbq.$kaka_surf.$kaka_rent;

	if($kakaoRes != ""){
		$kakaoRes = substr($kakaoRes, 0, strlen($kakaoRes) - 1);
	}

	if($res_kakao == "Y" && $res_confirm == "확정"){
		$select_query = "SELECT user_name, user_tel, res_kakaoinfo FROM `AT_SOL_RES_MAIN` WHERE resseq = $seq";
		$result = mysqli_query($conn, $select_query);
		$rowMain = mysqli_fetch_array($result);
	
		$userName = $rowMain["user_name"];
		$userPhone = $rowMain["user_tel"];
		$res_kakaoinfo = $rowMain["res_kakaoinfo"];

		if($res_kakaoinfo == "N"){
		
			//==========================카카오 메시지 발송 ==========================
			$msgTitle = '솔게스트하우스&솔서프 예약안내';
			$arrKakao = array(
				"gubun"=> $code
				, "admin"=> "N"
				, "tempName"=> "at_res_step4" //이용안내
				, "smsTitle"=> $msgTitle
				, "userName"=> $userName
				, "userPhone"=> $userPhone
				, "kakaoRes"=> $kakaoRes
				, "smsOnly"=>"N"
				, "PROD_NAME"=>"솔게하"
				, "PROD_URL"=>""
				, "PROD_TYPE"=>"sol_standby"
				, "RES_CONFIRM"=>"-1"
			);
		
			$arrRtn = sendKakao($arrKakao); //알림톡 발송

			//------- 알림톡 디버깅 -----
			$data = json_decode($arrRtn[0], true);
			$kakao_code = $data[0]["code"];
			$kakao_type = $data[0]["data"]["type"];
			$kakao_msgid = $data[0]["data"]["msgid"];
			$kakao_message = $data[0]["message"];
			$kakao_originMessage = $data[0]["originMessage"];

			$userinfo = "$userName|$userPhone|$datetime||||$kakao_code|$kakao_type|$kakao_message|$kakao_originMessage|$kakao_msgid";

			// 카카오 알림톡 DB 저장 START
			$select_query = kakaoDebug($arrKakao, $arrRtn);            
			$result_set = mysqli_query($conn, $select_query);
			// 카카오 알림톡 DB 저장 END
			
			$select_query = "UPDATE `AT_SOL_RES_MAIN` SET res_kakaoinfo = 'Y', userinfo = '".$userinfo."' WHERE resseq = $seq";
			$result_set = mysqli_query($conn, $select_query);
		}
	}else if($res_kakao == "S"){
		$res_kakaoBank = $_REQUEST["res_kakaoBank"];

		//==========================카카오 메시지 발송 ==========================
		$msgTitle = '솔게스트하우스&솔서프 계좌안내';
		$arrKakao = array(
			"gubun"=> $code
			, "admin"=> "N"
			, "tempName"=> "at_res_step4" //이용안내
			, "smsTitle"=> $msgTitle
			, "userName"=> $user_name
			, "userPhone"=> $user_tel
			, "kakaoRes"=> $kakaoRes
			, "kakaoprice"=> $res_kakaoBank
			, "smsOnly"=>"N"
			, "PROD_NAME"=>"솔게하"
			, "PROD_URL"=>""
			, "PROD_TYPE"=>"sol_bank"
			, "RES_CONFIRM"=>"-1"
		);

		$arrRtn = sendKakao($arrKakao); //알림톡 발송

		// 카카오 알림톡 DB 저장 START
		$select_query = kakaoDebug($arrKakao, $arrRtn);            
		$result_set = mysqli_query($conn, $select_query);
		// 카카오 알림톡 DB 저장 END
			
		$select_query = "UPDATE `AT_SOL_RES_MAIN` SET res_bankchk = '$res_kakaoBank' WHERE resseq = $seq";
		$result_set = mysqli_query($conn, $select_query);
	}
		
	mysqli_query($conn, "COMMIT");
}else if($param == "solchef"){ //버스 예약안내 카톡 : 타채널예약건
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
		$seatName = "동해행";
		$seatName2 = "동해";
	}

	$resseatMsg = "";
	if($resbusseat1 > 0){ //양양행,동해행 좌석예약
		$resseatMsg = "\n    [$seatName] ".$resDate1." / ".$resbusseat1."자리";
	}

	if($resbusseat2 > 0){ //서울행 좌석예약
		$resseatMsg .= "\n    [서울행] ".$resDate2." / ".$resbusseat2."자리";
	}

	$prodTitle = ' 서핑버스';
	if($reschannel == 11){ //프립
		$prodTitle = 'x프립버스';
		$seatName2 = $seatName2." 프립버스";
	}else if($reschannel == 17 || $reschannel == 20 || $reschannel == 21 || $reschannel == 22){ //프립 패키지
		$prodTitle = 'x프립 서핑패키지';
		if($reschannel == 17){
			$seatName2 = $seatName2." 마린서프x프립";
		}else if($reschannel == 20){
			$seatName2 = $seatName2." 인구서프x프립";
		}else if($reschannel == 21){
			$seatName2 = "서프팩토리 동해점x프립";
		}else if($reschannel == 22){
			$seatName2 = "힐링 서핑캠프x프립";
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
}else if($param == "solchefdel"){
    $codeseq = $_REQUEST["codeseq"];

	$select_query = "DELETE FROM AT_COUPON_CODE WHERE codeseq = $codeseq";
	$result_set = mysqli_query($conn, $select_query);
	if(!$result_set) goto errGo;
	
	mysqli_query($conn, "COMMIT");
	
}

if(!$success){
	errGo:
	mysqli_query($conn, "ROLLBACK");
	echo "err|$errmsg";
}else if(!$success){
	errGoRoom:
	mysqli_query($conn, "ROLLBACK");
	echo "errRoom|$stayroom|$staynum";
}else{
	echo '0';
}

mysqli_close($conn);
?>
