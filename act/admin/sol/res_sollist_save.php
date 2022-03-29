<?php
include __DIR__.'/../../db.php';
include __DIR__.'/../../surf/surfkakao.php';
include __DIR__.'/../../surf/surfmail.php';
include __DIR__.'/../../surf/surffunc.php';

$success = true;
$datetime = date('Y/m/d H:i'); 

$param = $_REQUEST["resparam"];

$errmsg = "";
$intseq = "";
$intseq3 = "";
$to = "lud1@naver.com,ttenill@naver.com";

mysqli_query($conn, "SET AUTOCOMMIT=0");
mysqli_query($conn, "BEGIN");

if($param == "solkakao1"){ //상태 정보 업데이트
    $resseq = $_REQUEST["resseq"];

    $select_query = "SELECT user_name, user_tel FROM `AT_SOL_RES_MAIN` WHERE resseq = $resseq";
    $result = mysqli_query($conn, $select_query);
    $rowMain = mysqli_fetch_array($result);

	$userName = $rowMain["user_name"];
	$userPhone = $rowMain["user_tel"];

    //==========================카카오 메시지 발송 ==========================
	$select_query_sub = "SELECT * FROM AT_SOL_RES_SUB WHERE resseq = $resseq ORDER BY ressubseq";
	$resultSite = mysqli_query($conn, $select_query_sub);

	$resList = "";
	$resInfo = "";
	while ($rowSub = mysqli_fetch_assoc($resultSite)){

		$res_type = $rowSub['res_type'];
		if($res_type == "stay"){ //숙박,바베큐,펍파티
			if($rowSub['prod_name'] != "N"){ //숙박미신청
				$resList1 = "게스트하우스,";
				$resInfo1 = "   * 게스트하우스\n     - 입실:16시, 퇴실:익일 11시\n     - 방/침대 배정은 이용일 14시 이후로 하단에 있는 [필독]예약 상세안내 버튼에서 확인가능합니다\n\n";
			}

			if($rowSub['bbq'] != "N"){ 
				if(!(strpos($rowSub['bbq'], "바베큐") === false))
				{
					$resList2 = "바베큐파티,";
					$resInfo2 = "   * 바베큐파티\n     - 파티시간 : 19시 ~ 21시30분\n     - 파티시작 10분전에 1층으로 와주세요~\n\n";
				}

				if(!(strpos($rowSub['bbq'], "펍파티") === false))
				{
					$resList3 = "펍파티,";
					$resInfo3 = "   * 펍파티\n     - 파티시간 : 22시 ~ 24시\n\n";
				}
			}
		}else{ //강습,렌탈
			if($rowSub['prod_name'] != "N"){ //숙박미신청
				$resList4 = "서핑강습,";
				$resInfo4 = "   * 서핑강습\n     - 제휴된 서핑샵으로 안내됩니다~\n     - 상세안내 버튼을 클릭해주세요~\n\n";
			}

			if($rowSub['surfrent'] != "N"){ //숙박미신청
				$resList5 = "장비렌탈,";
				$resInfo5 = "   * 장비렌탈\n     - 제휴된 서핑샵으로 안내됩니다~\n     - 상세안내 버튼을 클릭해주세요~\n\n";
			}
		}
	}

	$resList = $resList1.$resList2.$resList3.$resList4.$resList5;
	$resList = substr($resList, 0, strlen($resList) - 1);
	
	$resInfo = $resInfo1.$resInfo2.$resInfo3.$resInfo4.$resInfo5;
	$resInfo = substr($resInfo, 0, strlen($resInfo) - 1);
	$resInfo = "하단에 있는 [필독]예약 상세안내 버튼을 클릭하시고 내용을 꼭 확인해주세요.\n";
	
	$msgTitle = '액트립 솔.동해서핑점 예약안내';
	$kakaoMsg = $msgTitle.'\n안녕하세요. '.$userName.'님\n\n솔.동해서핑점 예약정보\n ▶ 예약자 : '.$userName.'\n ▶ 예약내역 : '.$resList.'\n\n'.$resInfo.'---------------------------------\n ▶ 안내사항\n      - 예약하신 시간보다 늦게 도착하실 경우 꼭 연락주세요.\n      - \n\n ▶ 문의\n      - 010.4337.5080\n      - http://pf.kakao.com/_HxmtMxl';

	$arrKakao = array(
		"gubun"=> $code
		, "admin"=> "N"
		, "smsTitle"=> $msgTitle
		, "userName"=> $userName
		, "tempName"=> "at_surf_step3"
		, "kakaoMsg"=>$kakaoMsg
		, "userPhone"=> $userPhone
		, "link1"=>"sol_kakao?num=1&seq=".urlencode(encrypt($resseq)) //예약조회/취소
		, "link2"=>"surflocation?seq=5" //지도로 위치보기
		, "link3"=>"event" //공지사항
		, "link4"=>""
		, "link5"=>""
		, "smsOnly"=>"N"
	);

	$arrRtn = sendKakao($arrKakao); //알림톡 발송

	//------- 쿠폰코드 입력 -----
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
		$select_query_sub = "SELECT * FROM AT_SOL_RES_SUB WHERE resseq = $resseq ORDER BY ressubseq";
		$resultSite = mysqli_query($conn, $select_query_sub);

		$resList = "";
		$resInfo = "";
		while ($rowSub = mysqli_fetch_assoc($resultSite)){

			$res_type = $rowSub['res_type'];
			if($res_type == "stay"){ //숙박,바베큐,펍파티
				if($rowSub['prod_name'] != "N"){ //숙박미신청
					$resList1 = "게스트하우스,";
					$resInfo1 = "   * 게스트하우스\n     - 입실:16시, 퇴실:익일 11시\n     - 방/침대 배정은 이용일 14시 이후로 하단에 있는 [필독]예약 상세안내 버튼에서 확인가능합니다\n\n";
				}

				if($rowSub['bbq'] != "N"){ 
					if(!(strpos($rowSub['bbq'], "바베큐") === false))
					{
						$resList2 = "바베큐파티,";
						$resInfo2 = "   * 바베큐파티\n     - 파티시간 : 19시 ~ 21시30분\n     - 파티시작 10분전에 1층으로 와주세요~\n\n";
					}

					if(!(strpos($rowSub['bbq'], "펍파티") === false))
					{
						$resList3 = "펍파티,";
						$resInfo3 = "   * 펍파티\n     - 파티시간 : 22시 ~ 24시\n\n";
					}
				}
			}else{ //강습,렌탈
				if($rowSub['prod_name'] != "N"){ //숙박미신청
					$resList4 = "서핑강습,";
					$resInfo4 = "   * 서핑강습\n     - 제휴된 서핑샵으로 안내됩니다~\n     - 상세안내 버튼을 클릭해주세요~\n\n";
				}

				if($rowSub['surfrent'] != "N"){ //숙박미신청
					$resList5 = "장비렌탈,";
					$resInfo5 = "   * 장비렌탈\n     - 제휴된 서핑샵으로 안내됩니다~\n     - 상세안내 버튼을 클릭해주세요~\n\n";
				}
			}
		}

		$resList = $resList1.$resList2.$resList3.$resList4.$resList5;
		$resList = substr($resList, 0, strlen($resList) - 1);
		
		$resInfo = $resInfo1.$resInfo2.$resInfo3.$resInfo4.$resInfo5;
		$resInfo = substr($resInfo, 0, strlen($resInfo) - 1);
		$resInfo = "하단에 있는 [필독]예약 상세안내 버튼을 클릭하시고 내용을 꼭 확인해주세요.\n";
		
		$msgTitle = '액트립 솔.동해서핑점 예약안내';
		$kakaoMsg = $msgTitle.'\n안녕하세요. '.$userName.'님\n\n솔.동해서핑점 예약정보\n ▶ 예약자 : '.$userName.'\n ▶ 예약내역 : '.$resList.'\n\n'.$resInfo.'---------------------------------\n ▶ 안내사항\n      - 예약하신 시간보다 늦게 도착하실 경우 꼭 연락주세요.\n      - \n\n ▶ 문의\n      - 010.4337.5080\n      - http://pf.kakao.com/_HxmtMxl';

		$arrKakao = array(
			"gubun"=> $code
			, "admin"=> "N"
			, "smsTitle"=> $msgTitle
			, "userName"=> $userName
			, "tempName"=> "at_surf_step3"
			, "kakaoMsg"=>$kakaoMsg
			, "userPhone"=> $userPhone
			, "link1"=>"sol_kakao?num=1&seq=".urlencode(encrypt($resseq)) //예약조회/취소
			, "link2"=>"surflocation?seq=5" //지도로 위치보기
			, "link3"=>"event" //공지사항
			, "link4"=>""
			, "link5"=>""
			, "smsOnly"=>"N"
		);

		$arrRtn = sendKakao($arrKakao); //알림톡 발송

		//------- 쿠폰코드 입력 -----
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
		if(!$result_set) goto errGo;

		$select_query = "DELETE FROM AT_SOL_RES_SUB WHERE resseq = $resseq";
		$result_set = mysqli_query($conn, $select_query);
		// $errmsg = $select_query;
		// goto errGo;
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
				// $errmsg = $select_query;
				// goto errGo;

				if($count > 0){
					goto errGoRoom;
				}
			}

			if($res_bbq[$i] != "N"){
				$resdate = $res_bbqdate[$i];
			}

			$select_query = "INSERT INTO `AT_SOL_RES_SUB`(`resseq`, `res_type`, `prod_name`, `sdate`, `edate`, `resdate`, `staysex`, `stayroom`, `staynum`, `bbq`, `stayM`) VALUES ($seq, 'stay', '$prod_name', '$sdate', '$edate', '$resdate', '$staysex', '$stayroom', '$staynum', '$bbq', $stayM)";
			$result_set = mysqli_query($conn, $select_query);
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
			}

			if($res_rent[$i] != "N"){
				$resdate = $res_surfdate[$i];
				$surfrentM = $res_rentM[$i];
				$surfrentW = $res_rentW[$i];
			}

			$select_query = "INSERT INTO `AT_SOL_RES_SUB`(`resseq`, `res_type`, `prod_name`, `resdate`, `restime`, `surfM`, `surfW`, `surfrent`, `surfrentM`, `surfrentW`, `sdate`, `edate`) VALUES ($seq, 'surf', '$prod_name', '$resdate', '$restime', $surfM, $surfW, '$surfrent', $surfrentM, $surfrentW, '', '')";
			$result_set = mysqli_query($conn, $select_query);
			if(!$result_set) goto errGo;
		}
	}

	//알림톡 발송 (확정일경우)
	if($res_kakao == "Y" && $res_confirm == "확정"){
		$select_query = "SELECT user_name, user_tel FROM `AT_SOL_RES_MAIN` WHERE resseq = $seq";
		$result = mysqli_query($conn, $select_query);
		$rowMain = mysqli_fetch_array($result);
	
		$userName = $rowMain["user_name"];
		$userPhone = $rowMain["user_tel"];
	
		//==========================카카오 메시지 발송 ==========================
		$select_query_sub = "SELECT * FROM AT_SOL_RES_SUB WHERE resseq = $seq ORDER BY ressubseq";
		$resultSite = mysqli_query($conn, $select_query_sub);
	
		$resList = "";
		$resInfo = "";
		while ($rowSub = mysqli_fetch_assoc($resultSite)){
	
			$res_type = $rowSub['res_type'];
			if($res_type == "stay"){ //숙박,바베큐,펍파티
				if($rowSub['prod_name'] != "N"){ //숙박미신청
					$resList1 = "게스트하우스,";
					$resInfo1 = "   * 게스트하우스\n     - 입실:16시, 퇴실:익일 11시\n     - 방/침대 배정은 이용일 14시 이후로 하단에 있는 [필독]예약 상세안내 버튼에서 확인가능합니다\n\n";
				}
	
				if($rowSub['bbq'] != "N"){ 
					if(!(strpos($rowSub['bbq'], "바베큐") === false))
					{
						$resList2 = "바베큐파티,";
						$resInfo2 = "   * 바베큐파티\n     - 파티시간 : 19시 ~ 21시30분\n     - 파티시작 10분전에 1층으로 와주세요~\n\n";
					}
	
					if(!(strpos($rowSub['bbq'], "펍파티") === false))
					{
						$resList3 = "펍파티,";
						$resInfo3 = "   * 펍파티\n     - 파티시간 : 22시 ~ 24시\n\n";
					}
				}
			}else{ //강습,렌탈
				if($rowSub['prod_name'] != "N"){ //숙박미신청
					$resList4 = "서핑강습,";
					$resInfo4 = "   * 서핑강습\n     - 제휴된 서핑샵으로 안내됩니다~\n     - 상세안내 버튼을 클릭해주세요~\n\n";
				}
	
				if($rowSub['surfrent'] != "N"){ //숙박미신청
					$resList5 = "장비렌탈,";
					$resInfo5 = "   * 장비렌탈\n     - 제휴된 서핑샵으로 안내됩니다~\n     - 상세안내 버튼을 클릭해주세요~\n\n";
				}
			}
		}
	
		$resList = $resList1.$resList2.$resList3.$resList4.$resList5;
		$resList = substr($resList, 0, strlen($resList) - 1);
		
		$resInfo = $resInfo1.$resInfo2.$resInfo3.$resInfo4.$resInfo5;
		$resInfo = substr($resInfo, 0, strlen($resInfo) - 1);
		$resInfo = "하단에 있는 [필독]예약 상세안내 버튼을 클릭하시고 내용을 꼭 확인해주세요.\n";
		
		$msgTitle = '액트립 솔.동해서핑점 예약안내';
		$kakaoMsg = $msgTitle.'\n안녕하세요. '.$userName.'님\n\n솔.동해서핑점 예약정보\n ▶ 예약자 : '.$userName.'\n ▶ 예약내역 : '.$resList.'\n\n'.$resInfo.'---------------------------------\n ▶ 안내사항\n      - 예약하신 시간보다 늦게 도착하실 경우 꼭 연락주세요.\n\n ▶ 문의\n      - 010.4337.5080\n      - http://pf.kakao.com/_HxmtMxl';
	
		$arrKakao = array(
			"gubun"=> $code
			, "admin"=> "N"
			, "smsTitle"=> $msgTitle
			, "userName"=> $userName
			, "tempName"=> "at_surf_step3"
			, "kakaoMsg"=>$kakaoMsg
			, "userPhone"=> $userPhone
			, "link1"=>"sol_kakao?num=1&seq=".urlencode(encrypt($seq)) //예약조회/취소
			, "link2"=>"surflocation?seq=5" //지도로 위치보기
			, "link3"=>"event" //공지사항
			, "link4"=>""
			, "link5"=>""
			, "smsOnly"=>"N"
		);
	
		sendKakao($arrKakao); //알림톡 발송

		// 카카오 알림톡 DB 저장 START
		$select_query = kakaoDebug($arrKakao, $arrRtn);            
		$result_set = mysqli_query($conn, $select_query);
		// 카카오 알림톡 DB 저장 END
	
		$select_query = "UPDATE `AT_SOL_RES_MAIN` SET res_kakao = res_kakao + 1 WHERE resseq = $seq";
		$result_set = mysqli_query($conn, $select_query);
		if(!$result_set) goto errGo;
	}
		
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
