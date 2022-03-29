<?php
include __DIR__.'/../../db.php';
include __DIR__.'/../../surf/surfkakao.php';
include __DIR__.'/../../surf/surfmail.php';
include __DIR__.'/../../surf/surffunc.php';

$success = true;
$datetime = date('Y/m/d H:i'); 

$param = $_REQUEST["resparam"];
$InsUserID = $_REQUEST["userid"];

$chkCancel = $_REQUEST["chkCancel"];
$selConfirm = $_REQUEST["selConfirm"];
$resnum = $_REQUEST["MainNumber"];
$memo = $_REQUEST["memo"];
$shopseq = $_REQUEST["shopseq"];

$intseq = "";
$intseq2 = "";
$intseq3 = "";
$to = "lud1@naver.com,ttenill@naver.com";

mysqli_query($conn, "SET AUTOCOMMIT=0");
mysqli_query($conn, "BEGIN");

if($param == "changeConfirm"){ //상태 정보 업데이트
	for($i = 0; $i < count($chkCancel); $i++){
		$intseq .= $chkCancel[$i].",";
	}
	$intseq .= '0';

	//================= 예약상태 및 메모 저장 =================
	$select_query = "UPDATE AT_RES_MAIN SET memo = '$memo' WHERE resnum = $resnum";
	$result_set = mysqli_query($conn, $select_query);
// 	echo "<br>실행개수 : ".count($chkCancel);
//  echo "<br>메모저장 : ".$select_query;
//  echo "<br>메모 리턴값 : ".$result_set;
 	if(!$result_set) goto errGo;

	for($i = 0; $i < count($chkCancel); $i++){
		$confirmdate = "";
		if($selConfirm[$i] == 3){
			$confirmdate = ",confirmdate = now()";
		}

		$select_query = "UPDATE `AT_RES_SUB` 
							SET res_confirm = ".$selConfirm[$i]."
								".$confirmdate."
								,upddate = now()
								,upduserid = '".$InsUserID."'
							WHERE ressubseq = ".$chkCancel[$i].";";

//  echo "<br>상태변경 : ".$select_query;
		$result_set = mysqli_query($conn, $select_query);
// echo "<br>상태 리턴값 : ".$result_set;
		if(!$result_set) goto errGo;

		if($selConfirm[$i] == 2){ //임시확정
			$intseq2 .= $chkCancel[$i].",";
		}else if($selConfirm[$i] == 3){ //확정
			$intseq3 .= $chkCancel[$i].",";
		}else if($selConfirm[$i] == 6){ //임시취소
			$intseq2 .= $chkCancel[$i].",";
		}
	}
	$intseq2 .= '0';
	$intseq3 .= '0';

	mysqli_query($conn, "COMMIT");

	//==========================카카오 메시지 발송 ==========================
	$select_query = "SELECT user_name, user_tel, user_email, etc, memo FROM `AT_RES_MAIN` WHERE resnum = $resnum";
// echo "<br>메인 조회 : ".$select_query;

	$result = mysqli_query($conn, $select_query);
	$rowMain = mysqli_fetch_array($result);

	$ResNumber = $resnum;
	$userName = $rowMain["user_name"];
	$etc = $rowMain["etc"];
	$userPhone = $rowMain["user_tel"];
	$usermail = $rowMain["user_email"];

	if($intseq2 != "0"){ //임시확정, 임시취소 : 액트립에 메일 발송
		$select_query_sub = "SELECT * FROM AT_RES_SUB WHERE ressubseq IN ($intseq2) ORDER BY res_date, ressubseq";
		$resultSite = mysqli_query($conn, $select_query_sub);
		
		$surfshopMsg .= "";
	
		while ($rowSub = mysqli_fetch_assoc($resultSite)){
			$shopname = $rowSub['shopname'];
			$res_confirm = $rowSub['res_confirm'];
	
			$TimeDate = '';
			if(($rowSub['sub_title'] == "lesson" || $rowSub['sub_title'] == "pkg") && $rowSub['res_time'] != ""){
				$TimeDate = '      - 강습시간 : '.$rowSub['res_time'].'\n';
			}
	
			$ResNum = '      - 인원 : ';
			if($rowSub['res_m'] > 0){
				$ResNum .= '남:'.$rowSub['res_m'].'명';
			}
			if($rowSub['res_m'] > 0 && $rowSub['res_w'] > 0){
				$ResNum .= ',';
			}
			if($rowSub['res_w'] > 0){
				$ResNum .= '여:'.$rowSub['res_w'].'명';
			}
			$ResNum .= '\n';
	
			$ResOptInfo = "";
			$ResOptStay = "";
			$optname = $rowSub["optname"];
			$optinfo = $rowSub['optsubname'];
			if($rowSub['sub_title'] == "lesson"){
				$stayPlus = $rowSub['res_bus']; //숙박 여부
				
				$arrdate = explode("-", $rowSub['res_date']); // 들어온 날짜를 년,월,일로 분할해 변수로 저장합니다.
				$s_Y=$arrdate[0]; // 지정된 년도 
				$s_m=$arrdate[1]; // 지정된 월
				$s_d=$arrdate[2]; // 지정된 요일
				
				//이전일 요일구하기
				$preDate = date("Y-m-d", strtotime(date("Y-m-d",mktime(0,0,0,$s_m,$s_d,$s_Y))." -1 day"));
				$nextDate = date("Y-m-d", strtotime(date("Y-m-d",mktime(0,0,0,$s_m,$s_d,$s_Y))." +1 day"));
				if($stayPlus == 0){
					$ResOptStay = '      - 숙박일 : '.$rowSub['res_date'].'(1박)\n';
				}else if($stayPlus == 1){
					$ResOptStay = '      - 숙박일 : '.$preDate.'(1박)\n';
				}else if($stayPlus == 2){
					$ResOptStay = '      - 숙박일 : '.$preDate.'(2박)\n';
				}

				$ResOptInfo = $TimeDate;
			}else if($rowSub['sub_title'] == "rent"){
	
			}else if($rowSub['sub_title'] == "pkg"){
				$ResOptInfo = '      - '.$optinfo.'\n'.$TimeDate;
			}else if($rowSub['sub_title'] == "bbq"){
				$ResOptInfo = '      - '.str_replace('<br>', '\n      - ', $optinfo).'\n';
			}

			if($res_confirm == 2){ //임시확정
				$optname_sub = " - <b style='color:red;'>임시확정</b>";
			}else{ //임시취소
				$optname_sub = " - <b style='color:red;'>임시취소</b>";
			}

			$surfshopMsg .= '    ['.$optname.']'.$optname_sub.'\n      - 예약일 : '.$rowSub['res_date'].'\n'.$ResOptStay.$ResNum.$ResOptInfo;	
		}
		
		if($etc != ''){
			$etcMsg = ' ▶ 요청사항\n      '.$etc.'\n';
		}
	
		$info1_title = "신청목록";
		$info1 = str_replace('      -', '&nbsp;&nbsp;&nbsp;-', str_replace('\n', '<br>', $surfshopMsg));
		$info2_title = "";
		$info2 = "";
	
		$arrMail = array(
			"gubun"=> "surf"
			, "gubun_step" => 2
			, "gubun_title" => $shopname
			, "mailto"=> $to
			, "mailfrom"=> "surfshop_res@actrip.co.kr"
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

	// echo "<br>확정 조회 : ".$intseq3;
	if($intseq3 != "0"){ //예약 확정처리 : 업체, 고객발송
		$select_query_sub = "SELECT * FROM AT_RES_SUB WHERE ressubseq IN ($intseq3) ORDER BY res_date, ressubseq";
		$resultSite = mysqli_query($conn, $select_query_sub);
		
// echo "<br>정보 조회 : ".$select_query_sub;
		$surfshopMsg .= "";
	
		while ($rowSub = mysqli_fetch_assoc($resultSite)){
			$shopname = $rowSub['shopname'];
			$coupon = $rowSub['res_coupon'];
	
			$TimeDate = '';
			if(($rowSub['sub_title'] == "lesson" || $rowSub['sub_title'] == "pkg") && $rowSub['res_time'] != ""){
				$TimeDate = '      - 강습시간 : '.$rowSub['res_time'].'\n';
			}
	
			$ResNum = '      - 인원 : ';
			if($rowSub['res_m'] > 0){
				$ResNum .= '남:'.$rowSub['res_m'].'명';
			}
			if($rowSub['res_m'] > 0 && $rowSub['res_w'] > 0){
				$ResNum .= ',';
			}
			if($rowSub['res_w'] > 0){
				$ResNum .= '여:'.$rowSub['res_w'].'명';
			}
			$ResNum .= '\n';
	
			$ResOptInfo = "";
			$ResOptStay = "";
			$optname = $rowSub["optname"];
			$optinfo = $rowSub['optsubname'];
			if($rowSub['sub_title'] == "lesson"){
				$stayPlus = $rowSub['res_bus']; //숙박 여부
				
				$arrdate = explode("-", $rowSub['res_date']); // 들어온 날짜를 년,월,일로 분할해 변수로 저장합니다.
				$s_Y=$arrdate[0]; // 지정된 년도 
				$s_m=$arrdate[1]; // 지정된 월
				$s_d=$arrdate[2]; // 지정된 요일
				
				//이전일 요일구하기
				$preDate = date("Y-m-d", strtotime(date("Y-m-d",mktime(0,0,0,$s_m,$s_d,$s_Y))." -1 day"));
				$nextDate = date("Y-m-d", strtotime(date("Y-m-d",mktime(0,0,0,$s_m,$s_d,$s_Y))." +1 day"));
				if($stayPlus == 0){
					$ResOptStay = '      - 숙박일 : '.$rowSub['res_date'].'(1박)\n';
				}else if($stayPlus == 1){
					$ResOptStay = '      - 숙박일 : '.$preDate.'(1박)\n';
				}else if($stayPlus == 2){
					$ResOptStay = '      - 숙박일 : '.$preDate.'(2박)\n';
				}

				$ResOptInfo = $TimeDate;
			}else if($rowSub['sub_title'] == "rent"){
	
			}else if($rowSub['sub_title'] == "pkg"){
				$ResOptInfo = '      - '.$optinfo.'\n'.$TimeDate;
			}else if($rowSub['sub_title'] == "bbq"){
				$ResOptInfo = '      - '.str_replace('<br>', '\n      - ', $optinfo).'\n';
			}
			$surfshopMsg .= '    ['.$optname.']\n      - 예약일 : '.$rowSub['res_date'].'\n'.$ResOptStay.$ResNum.$ResOptInfo;	
		}
		
		if($etc != ''){
			$etcMsg = ' ▶ 요청사항\n      '.$etc.'\n';
		}

		$infomsg = "";
		$infomsg .= "\n      - 예약하신 내역이 확정처리되었습니다.\n      - 이용일 및 신청정보 확인부탁드립니다.";
		if($coupon == "NAVERA"){
			$infomsg .= "\n      - 취소 및 환불신청은 네이버에서 해주세요~";
		}
		$infomsg .= "\n\n";

		$msgTitle = '액트립 '.$shopname.' 예약안내';
		$kakaoMsg = $msgTitle.'\n안녕하세요. '.$userName.'님\n\n'.$shopname.' 예약정보 [예약확정]\n ▶ 예약번호 : '.$ResNumber.'\n ▶ 예약자 : '.$userName.'\n ▶ 신청목록\n'.$surfshopMsg.$etcMsg.'---------------------------------\n ▶ 안내사항'.$infomsg.' ▶ 문의\n      - 010.3308.6080\n      - http://pf.kakao.com/_HxmtMxl';
	
		$navilink = "surflocation?seq=".$shopseq;
		if($shopseq == 13){
			$navilink = "bbq_yy?view=1";
		}else if($shopseq == 15){
			$navilink = "bbq_dh?view=1";
		}else if($shopseq == 184){
			$navilink = "bbq_pkg?view=1";
		}
	
		$arrKakao = array(
			"gubun"=> ""
			, "admin"=> "N"
			, "smsTitle"=> $msgTitle
			, "userName"=> $userName
			, "tempName"=> "at_surf_step1"
			, "kakaoMsg"=>$kakaoMsg
			, "userPhone"=> $userPhone
			, "link1"=>"orderview?num=1&resNumber=".$ResNumber //예약조회/취소
			, "link2"=>$navilink //지도로 위치보기
			, "link3"=>"event" //제휴업체 목록
			, "link4"=>"" //공지사항
			, "link5"=>""
			, "smsOnly"=>"N"
			, "PROD_NAME"=>$shopname
			, "PROD_URL"=>$shopSeq
			, "PROD_TYPE"=>"surf_user"
			, "RES_CONFIRM"=>"3"
		);
		$arrRtn = sendKakao($arrKakao); //알림톡 발송
	
		// 카카오 알림톡 DB 저장 START
		$select_query = kakaoDebug($arrKakao, $arrRtn);
		$result_set = mysqli_query($conn, $select_query);
		// 카카오 알림톡 DB 저장 END
	
		//카카오톡 업체 발송
		$select_query = 'SELECT * FROM AT_PROD_MAIN WHERE seq = '.$shopseq;
		$result_setlist = mysqli_query($conn, $select_query);
		$rowshop = mysqli_fetch_array($result_setlist);
	
		$admin_tel = $rowshop["tel_kakao"];
		// $admin_tel = "010-4437-0009";
	
		$infomsg = "";
		if($coupon == "ATBLOG"){
			$infomsg .= "\n      - 블로그 체험단 예약신청입니다.";
		}
		$infomsg .= "\n      - 예약확정이 완료되었습니다.\n      - 이용일 및 신청정보 확인부탁드립니다.\n\n";

		$msgTitle = '액트립 ['.$userName.']님 예약안내';
		$kakaoMsg = $msgTitle.'\n안녕하세요. 액트립 '.$shopname.' 예약건 안내입니다.\n\n액트립 예약정보 [예약확정]\n ▶ 예약번호 : '.$ResNumber.'\n ▶ 예약자 : '.$userName.'\n ▶ 연락처 : '.$userPhone.'\n'.$surfshopMsg.$etcMsg.'---------------------------------\n ▶ 안내사항'.$infomsg;
	
		$arrKakao = array(
			"gubun"=> ""
			, "admin"=> "N"
			, "smsTitle"=> $msgTitle
			, "userName"=> $userName
			, "tempName"=> "at_shop_step1"
			, "kakaoMsg"=>$kakaoMsg
			, "userPhone"=> $admin_tel
			, "link1"=>"surfadminkakao?param=".urlencode(encrypt(date("Y-m-d").'|'.$shopseq)) //전체 예약목록
			, "link2"=>"surfadminkakao?param=".urlencode(encrypt(date("Y-m-d").'|'.$ResNumber.'|'.$shopseq)) //현재 예약건 보기
			, "link3"=>""
			, "link4"=>""
			, "link5"=>""
			, "smsOnly"=>"N"
			, "PROD_NAME"=>$shopname
			, "PROD_URL"=>$shopSeq
			, "PROD_TYPE"=>"surf_shop"
			, "RES_CONFIRM"=>"3"
		);
		$arrRtn = sendKakao($arrKakao); //알림톡 발송
	
		// 카카오 알림톡 DB 저장 START
		$select_query = kakaoDebug($arrKakao, $arrRtn);
		$result_set = mysqli_query($conn, $select_query);
		// 카카오 알림톡 DB 저장 END
	
		$info1_title = "신청목록";
		$info1 = str_replace('      -', '&nbsp;&nbsp;&nbsp;-', str_replace('\n', '<br>', $surfshopMsg));
		$info2_title = "";
		$info2 = "";
	
		if(strrpos($usermail, "@") > 0){
            $to .= ','.$usermail;
		}
		$arrMail = array(
			"gubun"=> "surf"
			, "gubun_step" => 3
			, "gubun_title" => $shopname
			, "mailto"=> $to
			, "mailfrom"=> "surfshop_res@actrip.co.kr"
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

}else if($param == "changeConfirmPop"){ //상태 정보 업데이트
	$resnum = $_REQUEST["resseq"];
	$shopseq = $_REQUEST["shopseq2"];
	$res_adminname = $_REQUEST["res_adminname"];
	$user_name = $_REQUEST["user_name"];
	$user_tel = $_REQUEST["user_tel1"]."-".$_REQUEST["user_tel2"]."-".$_REQUEST["user_tel3"];
	$res_kakao = $_REQUEST["res_kakao"];

	$etc = $_REQUEST["etc"];
	$memo2 = $_REQUEST["memo2"];

	for($i = 0; $i < count($chkCancel); $i++){
		$intseq .= $chkCancel[$i].",";
	}
	$intseq .= '0';

	//================= 예약상태 및 메모 저장 =================
	$select_query = "UPDATE AT_RES_MAIN SET etc = '$etc', memo = '$memo2', user_name = '$user_name', user_tel = '$user_tel' WHERE resnum = $resnum";
	$result_set = mysqli_query($conn, $select_query);
 	if(!$result_set) goto errGo;

	for($i = 0; $i < count($chkCancel); $i++){
		$confirmdate = "";
		if($selConfirm[$i] == 3){
			$confirmdate = ",confirmdate = now()";
		}

		$select_query = "UPDATE `AT_RES_SUB` 
							SET res_confirm = ".$selConfirm[$i]."
								".$confirmdate."
								,upddate = now()
								,upduserid = '".$res_adminname."'
							WHERE ressubseq = ".$chkCancel[$i].";";

		$result_set = mysqli_query($conn, $select_query);
		if(!$result_set) goto errGo;

		if($selConfirm[$i] == 2){ //임시확정
			$intseq2 .= $chkCancel[$i].",";
		}else if($selConfirm[$i] == 3){ //확정
			$intseq3 .= $chkCancel[$i].",";
		}else if($selConfirm[$i] == 6){ //임시취소
			$intseq2 .= $chkCancel[$i].",";
		}
	}
	$intseq2 .= '0';
	$intseq3 .= '0';

	mysqli_query($conn, "COMMIT");

	//==========================카카오 메시지 발송 ==========================
	$select_query = "SELECT user_name, user_tel, user_email, etc, memo FROM `AT_RES_MAIN` WHERE resnum = $resnum";

	$result = mysqli_query($conn, $select_query);
	$rowMain = mysqli_fetch_array($result);

	$ResNumber = $resnum;
	$userName = $rowMain["user_name"];
	$etc = $rowMain["etc"];
	$userPhone = $rowMain["user_tel"];
	$usermail = $rowMain["user_email"];

	if($intseq2 != "0"){ //임시확정, 임시취소 : 액트립에 메일 발송
		$select_query_sub = "SELECT * FROM AT_RES_SUB WHERE ressubseq IN ($intseq2) ORDER BY res_date, ressubseq";
		$resultSite = mysqli_query($conn, $select_query_sub);
		
		$surfshopMsg .= "";
	
		while ($rowSub = mysqli_fetch_assoc($resultSite)){
			$shopname = $rowSub['shopname'];
			$res_confirm = $rowSub['res_confirm'];
	
			$TimeDate = '';
			if(($rowSub['sub_title'] == "lesson" || $rowSub['sub_title'] == "pkg") && $rowSub['res_time'] != ""){
				$TimeDate = '      - 강습시간 : '.$rowSub['res_time'].'\n';
			}
	
			$ResNum = '      - 인원 : ';
			if($rowSub['res_m'] > 0){
				$ResNum .= '남:'.$rowSub['res_m'].'명';
			}
			if($rowSub['res_m'] > 0 && $rowSub['res_w'] > 0){
				$ResNum .= ',';
			}
			if($rowSub['res_w'] > 0){
				$ResNum .= '여:'.$rowSub['res_w'].'명';
			}
			$ResNum .= '\n';
	
			$ResOptInfo = "";
			$ResOptStay = "";
			$optname = $rowSub["optname"];
			$optinfo = $rowSub['optsubname'];
			if($rowSub['sub_title'] == "lesson"){
				$stayPlus = $rowSub['res_bus']; //숙박 여부
				
				$arrdate = explode("-", $rowSub['res_date']); // 들어온 날짜를 년,월,일로 분할해 변수로 저장합니다.
				$s_Y=$arrdate[0]; // 지정된 년도 
				$s_m=$arrdate[1]; // 지정된 월
				$s_d=$arrdate[2]; // 지정된 요일
				
				//이전일 요일구하기
				$preDate = date("Y-m-d", strtotime(date("Y-m-d",mktime(0,0,0,$s_m,$s_d,$s_Y))." -1 day"));
				$nextDate = date("Y-m-d", strtotime(date("Y-m-d",mktime(0,0,0,$s_m,$s_d,$s_Y))." +1 day"));
				if($stayPlus == 0){
					$ResOptStay = '      - 숙박일 : '.$rowSub['res_date'].'(1박)\n';
				}else if($stayPlus == 1){
					$ResOptStay = '      - 숙박일 : '.$preDate.'(1박)\n';
				}else if($stayPlus == 2){
					$ResOptStay = '      - 숙박일 : '.$preDate.'(2박)\n';
				}

				$ResOptInfo = $TimeDate;
			}else if($rowSub['sub_title'] == "rent"){
	
			}else if($rowSub['sub_title'] == "pkg"){
				$ResOptInfo = '      - '.$optinfo.'\n'.$TimeDate;
			}else if($rowSub['sub_title'] == "bbq"){
				$ResOptInfo = '      - '.str_replace('<br>', '\n      - ', $optinfo).'\n';
			}

			if($res_confirm == 2){ //임시확정
				$optname_sub = " - <b style='color:red;'>임시확정</b>";
			}else{ //임시취소
				$optname_sub = " - <b style='color:red;'>임시취소</b>";
			}

			$surfshopMsg .= '    ['.$optname.']'.$optname_sub.'\n      - 예약일 : '.$rowSub['res_date'].'\n'.$ResOptStay.$ResNum.$ResOptInfo;	
		}
		
		if($etc != ''){
			$etcMsg = ' ▶ 요청사항\n      '.$etc.'\n';
		}
	
		$info1_title = "신청목록";
		$info1 = str_replace('      -', '&nbsp;&nbsp;&nbsp;-', str_replace('\n', '<br>', $surfshopMsg));
		$info2_title = "";
		$info2 = "";
	
		$arrMail = array(
			"gubun"=> "surf"
			, "gubun_step" => 2
			, "gubun_title" => $shopname
			, "mailto"=> $to
			, "mailfrom"=> "surfshop_res@actrip.co.kr"
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

	// echo "<br>확정 조회 : ".$intseq3;
	if($intseq3 != "0"){ //예약 확정처리 : 업체, 고객발송
		$select_query_sub = "SELECT * FROM AT_RES_SUB WHERE ressubseq IN ($intseq3) ORDER BY res_date, ressubseq";
		$resultSite = mysqli_query($conn, $select_query_sub);
		
// echo "<br>정보 조회 : ".$select_query_sub;
		$surfshopMsg .= "";
	
		while ($rowSub = mysqli_fetch_assoc($resultSite)){
			$shopname = $rowSub['shopname'];
			$coupon = $rowSub['res_coupon'];
	
			$TimeDate = '';
			if(($rowSub['sub_title'] == "lesson" || $rowSub['sub_title'] == "pkg") && $rowSub['res_time'] != ""){
				$TimeDate = '      - 강습시간 : '.$rowSub['res_time'].'\n';
			}
	
			$ResNum = '      - 인원 : ';
			if($rowSub['res_m'] > 0){
				$ResNum .= '남:'.$rowSub['res_m'].'명';
			}
			if($rowSub['res_m'] > 0 && $rowSub['res_w'] > 0){
				$ResNum .= ',';
			}
			if($rowSub['res_w'] > 0){
				$ResNum .= '여:'.$rowSub['res_w'].'명';
			}
			$ResNum .= '\n';
	
			$ResOptInfo = "";
			$ResOptStay = "";
			$optname = $rowSub["optname"];
			$optinfo = $rowSub['optsubname'];
			if($rowSub['sub_title'] == "lesson"){
				$stayPlus = $rowSub['res_bus']; //숙박 여부
				
				$arrdate = explode("-", $rowSub['res_date']); // 들어온 날짜를 년,월,일로 분할해 변수로 저장합니다.
				$s_Y=$arrdate[0]; // 지정된 년도 
				$s_m=$arrdate[1]; // 지정된 월
				$s_d=$arrdate[2]; // 지정된 요일
				
				//이전일 요일구하기
				$preDate = date("Y-m-d", strtotime(date("Y-m-d",mktime(0,0,0,$s_m,$s_d,$s_Y))." -1 day"));
				$nextDate = date("Y-m-d", strtotime(date("Y-m-d",mktime(0,0,0,$s_m,$s_d,$s_Y))." +1 day"));
				if($stayPlus == 0){
					$ResOptStay = '      - 숙박일 : '.$rowSub['res_date'].'(1박)\n';
				}else if($stayPlus == 1){
					$ResOptStay = '      - 숙박일 : '.$preDate.'(1박)\n';
				}else if($stayPlus == 2){
					$ResOptStay = '      - 숙박일 : '.$preDate.'(2박)\n';
				}

				$ResOptInfo = $TimeDate;
			}else if($rowSub['sub_title'] == "rent"){
	
			}else if($rowSub['sub_title'] == "pkg"){
				$ResOptInfo = '      - '.$optinfo.'\n'.$TimeDate;
			}else if($rowSub['sub_title'] == "bbq"){
				$ResOptInfo = '      - '.str_replace('<br>', '\n      - ', $optinfo).'\n';
			}
			$surfshopMsg .= '    ['.$optname.']\n      - 예약일 : '.$rowSub['res_date'].'\n'.$ResOptStay.$ResNum.$ResOptInfo;	
		}
		
		if($etc != ''){
			$etcMsg = ' ▶ 요청사항\n      '.$etc.'\n';
		}
		
		$infomsg = "";
		$infomsg .= "\n      - 예약하신 내역이 확정처리되었습니다.\n      - 이용일 및 신청정보 확인부탁드립니다.";
		if($coupon == "NAVERA"){
			$infomsg .= "\n      - 취소 및 환불신청은 네이버에서 해주세요~";
		}
		$infomsg .= "\n\n";
		
		$msgTitle = '액트립 '.$shopname.' 예약안내';
		$kakaoMsg = $msgTitle.'\n안녕하세요. '.$userName.'님\n\n'.$shopname.' 예약정보 [예약확정]\n ▶ 예약번호 : '.$ResNumber.'\n ▶ 예약자 : '.$userName.'\n ▶ 신청목록\n'.$surfshopMsg.$etcMsg.'---------------------------------\n ▶ 안내사항'.$infomsg.' ▶ 문의\n      - 010.3308.6080\n      - http://pf.kakao.com/_HxmtMxl';
	
		$navilink = "surflocation?seq=".$shopseq;
		if($shopseq == 13){
			$navilink = "bbq_yy?view=1";
		}else if($shopseq == 15){
			$navilink = "bbq_dh?view=1";
		}else if($shopseq == 184){
			$navilink = "bbq_pkg?view=1";
		}
	
		$arrKakao = array(
			"gubun"=> ""
			, "admin"=> "N"
			, "smsTitle"=> $msgTitle
			, "userName"=> $userName
			, "tempName"=> "at_surf_step1"
			, "kakaoMsg"=>$kakaoMsg
			, "userPhone"=> $userPhone
			, "link1"=>"orderview?num=1&resNumber=".$ResNumber //예약조회/취소
			, "link2"=>$navilink //지도로 위치보기
			, "link3"=>"event" //공지사항
			, "link4"=>""
			, "link5"=>""
			, "smsOnly"=>"N"
			, "PROD_NAME"=>$shopname
			, "PROD_URL"=>$shopSeq
			, "PROD_TYPE"=>"surf_user"
			, "RES_CONFIRM"=>"3"
		);

		if($res_kakao == "A" || $res_kakao == "U"){
			$arrRtn = sendKakao($arrKakao); //알림톡 발송
		
			// 카카오 알림톡 DB 저장 START
			$select_query = kakaoDebug($arrKakao, $arrRtn);
			$result_set = mysqli_query($conn, $select_query);
			// 카카오 알림톡 DB 저장 END
		}
	
		//카카오톡 업체 발송
		$select_query = 'SELECT * FROM AT_PROD_MAIN WHERE seq = '.$shopseq;
		$result_setlist = mysqli_query($conn, $select_query);
		$rowshop = mysqli_fetch_array($result_setlist);
	
		$admin_tel = $rowshop["tel_kakao"];
		// $admin_tel = "010-4437-0009";
	
		$infomsg = "";
		if($coupon == "ATBLOG"){
			$infomsg .= "\n      - 블로그 체험단 예약신청입니다.";
		}
		$infomsg .= "\n      - 예약확정이 완료되었습니다.\n      - 이용일 및 신청정보 확인부탁드립니다.\n\n";

		$msgTitle = '액트립 ['.$userName.']님 예약안내';
		$kakaoMsg = $msgTitle.'\n안녕하세요. 액트립 '.$shopname.' 예약건 안내입니다.\n\n액트립 예약정보 [예약확정]\n ▶ 예약번호 : '.$ResNumber.'\n ▶ 예약자 : '.$userName.'\n ▶ 연락처 : '.$userPhone.'\n'.$surfshopMsg.$etcMsg.'---------------------------------\n ▶ 안내사항'.$infomsg;
	
		$arrKakao = array(
			"gubun"=> ""
			, "admin"=> "N"
			, "smsTitle"=> $msgTitle
			, "userName"=> $userName
			, "tempName"=> "at_shop_step1"
			, "kakaoMsg"=>$kakaoMsg
			, "userPhone"=> $admin_tel
			, "link1"=>"surfadminkakao?param=".urlencode(encrypt(date("Y-m-d").'|'.$shopseq)) //전체 예약목록
			, "link2"=>"surfadminkakao?param=".urlencode(encrypt(date("Y-m-d").'|'.$ResNumber.'|'.$shopseq)) //현재 예약건 보기
			, "link3"=>""
			, "link4"=>""
			, "link5"=>""
			, "smsOnly"=>"N"
			, "PROD_NAME"=>$shopname
			, "PROD_URL"=>$shopSeq
			, "PROD_TYPE"=>"surf_shop"
			, "RES_CONFIRM"=>"3"
		);

		if($res_kakao == "A" || $res_kakao == "Y"){
			$arrRtn = sendKakao($arrKakao); //알림톡 발송
		
			// 카카오 알림톡 DB 저장 START
			$select_query = kakaoDebug($arrKakao, $arrRtn);
			$result_set = mysqli_query($conn, $select_query);
			// 카카오 알림톡 DB 저장 END
		}
	
		$info1_title = "신청목록";
		$info1 = str_replace('      -', '&nbsp;&nbsp;&nbsp;-', str_replace('\n', '<br>', $surfshopMsg));
		$info2_title = "";
		$info2 = "";
	
		if(strrpos($usermail, "@") > 0){
            $to .= ','.$usermail;
		}
		$arrMail = array(
			"gubun"=> "surf"
			, "gubun_step" => 3
			, "gubun_title" => $shopname
			, "mailto"=> $to
			, "mailfrom"=> "surfshop_res@actrip.co.kr"
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

		if($res_kakao == "A" || $res_kakao == "U"){
			sendMail($arrMail); //메일 발송
		}
	}

}else if($param == "soldoutdel"){
	session_start();

	$shopseq = $_SESSION['shopseq']; //샵 seq
	$soldoutseq = $_REQUEST['soldoutseq'];

	$select_query = "DELETE FROM `AT_PROD_OPT_SOLDOUT` WHERE seq = $shopseq AND soldoutseq = $soldoutseq";
	$result_set = mysqli_query($conn, $select_query);
	if(!$result_set) goto errGo;
	
	mysqli_query($conn, "COMMIT");
}else if($param == "soldout"){
	session_start();
	
	$shopseq = $_SESSION['shopseq']; //샵 seq
	$InsUserID = $_SESSION['userid'];

	$strDate = $_REQUEST["strDate"];
	$strDateE = $_REQUEST["strDateE"];
	$selItem = $_REQUEST["selItem"];
	
	$chkSexM = "N";
	$chkSexW = "N";
	if($_REQUEST["chkSexM"] == 1) $chkSexM = "Y";
	if($_REQUEST["chkSexW"] == 1) $chkSexW = "Y";

	$diff = date_diff(new DateTime($strDate), new DateTime($strDateE)); 
	$daycnt = $diff->days;

	for ($x=0; $x <= $daycnt; $x++) { 
		$nextdate = date("Y-m-d", strtotime($strDate." +".$x." day"));

		for($i = 0; $i < count($selItem); $i++){
			$select_query = 'SELECT * FROM `AT_PROD_OPT_SOLDOUT` WHERE seq = '.$shopseq.' AND soldout_date = "'.$nextdate.'" AND optseq = '.$selItem[$i];
			$result = mysqli_query($conn, $select_query);
			$count = mysqli_num_rows($result);

			if($count > 0){
				$rowMain = mysqli_fetch_array($result);
				$soldoutseq = $rowMain["soldoutseq"];

				$select_query = "UPDATE `AT_PROD_OPT_SOLDOUT` 
									SET opt_sexM = '".$chkSexM."'
										,opt_sexW = '".$chkSexW."'
									WHERE soldoutseq = ".$soldoutseq.";";
			}else{
				$select_query = "INSERT INTO `AT_PROD_OPT_SOLDOUT`(`seq`, `soldout_date`, `optseq`, `opt_sexM`, `opt_sexW`, `insuserid`, `insdate`) VALUES ($shopseq, '$nextdate', $selItem[$i], '$chkSexM', '$chkSexW', '$InsUserID', now())";
			}

			$result_set = mysqli_query($conn, $select_query);
			if(!$result_set) goto errGo;
		}

	}
	
	mysqli_query($conn, "COMMIT");
}

if(!$success){
	errGo:
	mysqli_query($conn, "ROLLBACK");
	echo $select_query;
}else{
	echo '0';
}
mysqli_close($conn);
?>

