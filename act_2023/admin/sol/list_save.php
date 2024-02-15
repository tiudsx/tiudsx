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

if($param == "solkakaoAll"){
	$chkresseq = $_REQUEST["chkresseq"];
	
	$arryKakao = array();
	for($i = 0; $i < count($chkresseq); $i++){
		$resseq = $chkresseq[$i];

		$select_query = "SELECT 
								@sdate:=MIN(CASE 
													WHEN sdate = '0000-00-00' THEN NULL 
													ELSE sdate END) 
								, @edate:=MAX(CASE 
													WHEN edate = '0000-00-00' THEN NULL 
													ELSE edate END) 
								, @resdate_max:=MAX(CASE 
													WHEN resdate = '0000-00-00' THEN NULL 
													ELSE resdate END) 
								, @resdate_min:=MIN(CASE 
													WHEN resdate = '0000-00-00' THEN NULL 
													ELSE resdate END) 
							FROM AT_SOL_RES_SUB WHERE resseq = $resseq;";
		$result = mysqli_query($conn, $select_query);

		$select_query = "SELECT user_name, user_tel, resnum, @sdate AS sdate, @edate AS edate, @resdate_max AS resdate_max, @resdate_min AS resdate_min FROM `AT_SOL_RES_MAIN` WHERE resseq = $resseq;";
		$result = mysqli_query($conn, $select_query);
		$rowMain = mysqli_fetch_array($result);
	
		$userName = $rowMain["user_name"];
		$userPhone = $rowMain["user_tel"];
		$resnum = $rowMain["resnum"];
		$sdate = $rowMain["sdate"];
		$edate = $rowMain["edate"];
		$resdate_max = $rowMain["resdate_max"];
		$resdate_min = $rowMain["resdate_min"];

		$date_start = "";
		$date_end = "";
		if($sdate == null){ //숙박일이 없는 경우 : 바베큐 또는 강습이용일
			$date_start = $resdate_min;
			$date_end = $resdate_max;
		}else{ //숙박일이 있는 경우
			if($resdate_min == null){ //바베큐 또는 강습일이 없는 경우
				$date_start = $sdate;
				$date_end = $edate;
			}else{
				if($sdate >= $resdate_min){
					$date_start = $resdate_min;
				}else{
					$date_start = $sdate;
				}
				
				if($edate <= $resdate_max){
					$date_end = $resdate_max;
				}else{
					$date_end = $edate;
				}
			}
		}

		$userDate = ($date_start == $date_end) ? $date_start : "$date_start ~ $date_end";
	
		//==========================카카오 메시지 발송 ==========================
		$DebugInfo = array(
			"PROD_NAME" => "솔게하"
			, "PROD_TABLE" => "AT_SOL_RES_MAIN"
			, "PROD_TYPE" => "sol_complete"
			, "RES_CONFIRM" => "-1"
			, "resnum" => $resnum
			, "resseq"=> $resseq
		);

		$arrKakao = array(
			"gubun"=> $code
			, "userName"=> $userName
			, "userPhone"=> $userPhone
			, "userDate"=> $userDate
			, "link1"=>shortURL("https://actrip.co.kr/sol_kakao?num=1&seq=".urlencode(encrypt($resseq))) //예약조회/취소
			, "DebugInfo"=> $DebugInfo
		);	

		$arryKakao[$i] = $arrKakao;
	}

	//==========================카카오 메시지 발송 ==========================
	$msgTitle = '솔게하&솔서프 동해점';

	$total_page = ceil(count($arryKakao) / 100);
	for ($x=0; $x < $total_page; $x++) {
		$arryKakao2 = array_filter($arryKakao, function($k) use ($x) {
			$page_cnt = ($x * 100);
			$start_cnt = 0 + $page_cnt;
			$end_cnt = 100 + $page_cnt;
			return $k >= $start_cnt && $k < $end_cnt;
		}, ARRAY_FILTER_USE_KEY);            
		
		$arrKakao = array(
			"arryData"=> $arryKakao2
			, "array"=> "true" //배열 여부
			, "tempName"=> "sol_info02" //템플릿 코드
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
				, "item"=> $arryKakao[($i + ($x * 100))]
				, "code"=> $code
				, "msgid"=> $msgid
				, "message"=> $message
				, "originMessage"=> $originMessage
			);
	
			// 카카오 알림톡 DB 저장 START
			$select_query = kakaoDebug2024($kakao_response, json_encode($data[$i]));
			$result_set = mysqli_query($conn, $select_query);
			// 카카오 알림톡 DB 저장 END
	
			$resseq = $arryKakao[($i + ($x * 100))]["DebugInfo"]["resseq"];
			$select_query = "UPDATE `AT_SOL_RES_MAIN` SET res_kakaoinfo = 'Y', res_kakao = res_kakao + 1, userinfo = '".$msgid."' WHERE resseq = $resseq";
			$result_set = mysqli_query($conn, $select_query);

			$errmsg = $select_query;
			if(!$result_set) goto errGo;
		}
	}
	
	mysqli_query($conn, "COMMIT");
	//==========================카카오 메시지 발송 End ==========================
}else if($param == "solrentyn"){ //렌탈 상태여부 변경
	$subseq = $_REQUEST["subseq"];
	$rentyn = $_REQUEST["rentyn"];

	$select_query = "UPDATE `AT_SOL_RES_SUB` SET surfrentYN = '".$rentyn."'
					WHERE ressubseq = ".$subseq.";";
	$result_set = mysqli_query($conn, $select_query);
	
	$errmsg = $select_query;
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
	$res_party = $_REQUEST["res_party"];

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
			$party = $res_party[$i];
			$stayM = $res_stayM[$i];

			//숙박
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
			}

			//바베큐
			if($res_bbqdate[$i] != ""){
				$resdate = $res_bbqdate[$i];
			}

			$select_query = "INSERT INTO `AT_SOL_RES_SUB`(`resseq`, `res_type`, `prod_name`, `sdate`, `edate`, `resdate`, `staysex`, `stayroom`, `staynum`, `party`, `stayM`) VALUES ($seq, 'stay', '$prod_name', '$sdate', '$edate', '$resdate', '$staysex', '$stayroom', '$staynum', '$party', $stayM)";
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

			//서핑강습
			if($res_surfshop[$i] != "N"){
				$resdate = $res_surfdate[$i];
				$restime = $res_surftime[$i];
				$surfM = $res_surfM[$i];
				$surfW = $res_surfW[$i];
			}

			//장비렌탈
			if($res_rent[$i] != "N"){
				$resdate = $res_surfdate[$i];
				$surfrentM = $res_rentM[$i];
				$surfrentW = $res_rentW[$i];
			}

			$select_query = "INSERT INTO `AT_SOL_RES_SUB`(`resseq`, `res_type`, `prod_name`, `resdate`, `restime`, `surfM`, `surfW`, `surfrent`, `surfrentM`, `surfrentW`, `sdate`, `edate`) VALUES ($seq, 'surf', '$prod_name', '$resdate', '$restime', $surfM, $surfW, '$surfrent', $surfrentM, $surfrentW, '', '')";
			$result_set = mysqli_query($conn, $select_query);

			$errmsg = $select_query;
			if(!$result_set) goto errGo;
		}
	}

	if(($res_kakao == "Y" && $res_confirm == "확정") || $res_kakao == "S"){
		$select_query = "SELECT 
								@sdate:=MIN(CASE 
													WHEN sdate = '0000-00-00' THEN NULL 
													ELSE sdate END) 
								, @edate:=MAX(CASE 
													WHEN edate = '0000-00-00' THEN NULL 
													ELSE edate END) 
								, @resdate_max:=MAX(CASE 
													WHEN resdate = '0000-00-00' THEN NULL 
													ELSE resdate END) 
								, @resdate_min:=MIN(CASE 
													WHEN resdate = '0000-00-00' THEN NULL 
													ELSE resdate END) 
							FROM AT_SOL_RES_SUB WHERE resseq = $seq;";
		$result = mysqli_query($conn, $select_query);

		$select_query = "SELECT user_name, user_tel, resnum, res_kakaoinfo, @sdate AS sdate, @edate AS edate, @resdate_max AS resdate_max, @resdate_min AS resdate_min FROM `AT_SOL_RES_MAIN` WHERE resseq = $seq;";
		$result = mysqli_query($conn, $select_query);
		$rowMain = mysqli_fetch_array($result);

		$userName = $rowMain["user_name"];
		$userPhone = $rowMain["user_tel"];
		$res_kakaoinfo = $rowMain["res_kakaoinfo"];
		$resnum = $rowMain["resnum"];
		$sdate = $rowMain["sdate"];
		$edate = $rowMain["edate"];
		$resdate_max = $rowMain["resdate_max"];
		$resdate_min = $rowMain["resdate_min"];

		$date_start = "";
		$date_end = "";
		if($sdate == null){ //숙박일이 없는 경우 : 바베큐 또는 강습이용일
			$date_start = $resdate_min;
			$date_end = $resdate_max;
		}else{ //숙박일이 있는 경우
			if($resdate_min == null){ //바베큐 또는 강습일이 없는 경우
				$date_start = $sdate;
				$date_end = $edate;
			}else{
				if($sdate >= $resdate_min){
					$date_start = $resdate_min;
				}else{
					$date_start = $sdate;
				}
				
				if($edate <= $resdate_max){
					$date_end = $resdate_max;
				}else{
					$date_end = $edate;
				}
			}
		}

		$userDate = ($date_start == $date_end) ? $date_start : "$date_start ~ $date_end";
	}

	if($res_kakao == "Y" && $res_confirm == "확정"){
		if($res_kakaoinfo == "N"){
			//==========================카카오 메시지 발송 ==========================
			$msgTitle = '솔게하&솔서프 동해점';
			$DebugInfo = array(
				"PROD_NAME" => "솔게하"
				, "PROD_TABLE" => "AT_SOL_RES_MAIN"
				, "PROD_TYPE" => "sol_complete"
				, "RES_CONFIRM" => "-1"
				, "resnum" => $resnum
				, "resseq"=> $seq
			);

			$arrKakao = array(
				"gubun"=> $code
				, "userName"=> $userName
				, "userPhone"=> $userPhone
				, "userDate"=> $userDate
				, "link1"=>shortURL("https://actrip.co.kr/sol_kakao?num=1&seq=".urlencode(encrypt($seq))) //예약조회/취소
				, "DebugInfo"=> $DebugInfo
			);	
		
			$arryKakao[0] = $arrKakao;

			$arrKakao = array(
				"arryData"=> $arryKakao
				, "array"=> "true" //배열 여부
				, "tempName"=> "sol_info02" //템플릿 코드
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
		
				$resseq = $arryKakao[$i]["DebugInfo"]["resseq"];
				$select_query = "UPDATE `AT_SOL_RES_MAIN` SET res_kakaoinfo = 'Y', res_kakao = res_kakao + 1, userinfo = '".$msgid."' WHERE resseq = $resseq";
				$result_set = mysqli_query($conn, $select_query);
		
				$errmsg = $select_query;
				if(!$result_set) goto errGo;
			}
		}
	}else if($res_kakao == "S"){
		$res_kakaoBank = $_REQUEST["res_kakaoBank"];

		//==========================카카오 메시지 발송 ==========================
		$arryKakao = array();
		$msgTitle = '솔게하&솔서프 동해점';
		$DebugInfo = array(
			"PROD_NAME" => "솔게하"
			, "PROD_TABLE" => "AT_SOL_RES_MAIN"
			, "PROD_TYPE" => "sol_bank"
			, "RES_CONFIRM" => "-1"
			, "resseq"=> $seq
			, "resnum"=> $ResNumber
		);

		$arrKakao = array(
			"gubun"=> $code
			, "userName"=> $user_name
			, "userPhone"=> $user_tel
			, "userDate"=> $userDate
			, "userPrice"=> number_format($res_kakaoBank).'원'
			, "DebugInfo"=> $DebugInfo
		);	

		$arryKakao[0] = $arrKakao;
	
		$arrKakao = array(
			"arryData"=> $arryKakao
			, "array"=> "true" //배열 여부
			, "tempName"=> "sol_info01" //템플릿 코드
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
	
			$resseq = $arryKakao[$i]["DebugInfo"]["resseq"];
			$select_query = "UPDATE `AT_SOL_RES_MAIN` SET res_bankchk = '$res_kakaoBank', userinfo = '".$msgid."' WHERE resseq = ".$resseq;
			$result_set = mysqli_query($conn, $select_query);

			$errmsg = $select_query;
			if(!$result_set) goto errGo;
		}
	}
		
	mysqli_query($conn, "COMMIT");
}else if($param == "solchef"){ //버스 예약안내 카톡 : 타채널예약건

	
	// echo $coupon_code." / ";
}else if($param == "solchefdel"){
    $codeseq = $_REQUEST["codeseq"];

	$select_query = "DELETE FROM AT_COUPON_CODE WHERE codeseq = $codeseq";
	$result_set = mysqli_query($conn, $select_query);

	$errmsg = $select_query;
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
