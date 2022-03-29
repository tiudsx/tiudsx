<?php
include __DIR__.'/../db.php';
include __DIR__.'/../surf/surfkakao.php';
include __DIR__.'/../surf/surfmail.php';
include __DIR__.'/../surf/surffunc.php';

$success = true;
$datetime = date('Y/m/d H:i'); 

$resnum = trim($_REQUEST["resnum"]);

mysqli_query($conn, "SET AUTOCOMMIT=0");
mysqli_query($conn, "BEGIN");

//주문내역과 문자내역의 이름/금액 매칭
$select_query = "SELECT a.user_name, a.user_tel, a.user_email, a.etc, b.* 
					FROM AT_RES_MAIN as a INNER JOIN (SELECT resnum, SUM(res_totalprice) as price, MAX(seq) as shopSeq, MAX(shopname) as shopname, MAX(code) as code FROM AT_RES_SUB GROUP BY resnum) as b 
						ON a.resnum = b.resnum 
					WHERE a.resnum = '$resnum'";
$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);

// 주문내역과 문자입금 내역 매칭이 맞을경우...
if($count == 1){
	while ($row = mysqli_fetch_assoc($result_setlist)){
		$ResNumber = $row['resnum'];
		$userName = $row["user_name"];
		$etc = $row["etc"];
		$userPhone = $row["user_tel"];
		$usermail = $row["user_email"];
		$shopSeq = $row["shopSeq"];
		$code = $row['code'];
	}

	$select_query_sub = 'SELECT * FROM AT_RES_SUB WHERE resnum = '.$ResNumber.' ORDER BY res_date, ressubseq';
	$resultSite = mysqli_query($conn, $select_query_sub);
	
	$surfshopMsg .= "";
	$ressubseq = "";

	while ($rowSub = mysqli_fetch_assoc($resultSite)){
		$shopname = $rowSub['shopname'];
		$ressubseq .= $rowSub['ressubseq'].',';

		$TimeDate = '';
		if(($rowSub['sub_title'] == "lesson" || $rowSub['sub_title'] == "pkg") && $rowSub['res_time'] != ""){
			$TimeDate = '      - 강습시간 : '.$resTime[$i].'\n';
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
			}else{
				//$ResOptInfo = '      - 안내 : '.$arrOptInfo[$optseq].'\n';
				$ResOptInfo = $TimeDate;
			}
		}else if($rowSub['sub_title'] == "rent"){

		}else if($rowSub['sub_title'] == "pkg"){
			$ResOptInfo = '      - '.$optinfo.'\n'.$TimeDate;
		}else if($rowSub['sub_title'] == "bbq"){
			$ResOptInfo = '      - '.str_replace('<br>', '\n      - ', $optinfo).'\n';
		}
		$surfshopMsg .= '    ['.$optname.']\n      - 예약일 : '.$rowSub['res_date'].'\n'.$ResOptStay.$ResNum.'\n'.$ResOptInfo;	
	}
	$ressubseq .= '0';
	
	if($etc != ''){
		$etcMsg = ' ▶ 요청사항\n      '.$etc.'\n';
	}
	
	$msgTitle = '액트립 '.$shopname.' 예약안내';
	$kakaoMsg = $msgTitle.'\n안녕하세요. '.$userName.'님\n\n'.$shopname.' 예약정보 [예약확정]\n ▶ 예약번호 : '.$ResNumber.'\n ▶ 예약자 : '.$userName.'\n ▶ 신청목록\n'.$surfshopMsg.$etcMsg.'---------------------------------\n ▶ 안내사항\n      - 예약하신 내역이 확정처리되었습니다.\n      - 이용일 및 신청정보 확인부탁드립니다.\n\n ▶ 문의\n      - 010.3308.6080\n      - http://pf.kakao.com/_HxmtMxl';

	$navilink = "surflocation?seq=".$shopSeq;
	if($shopSeq == 13){
		$navilink = "bbq_yy?view=1";
	}else if($shopSeq == 15){
		$navilink = "bbq_dh?view=1";
	}

	$arrKakao = array(
		"gubun"=> $code
		, "admin"=> "N"
		, "smsTitle"=> $msgTitle
		, "userName"=> $userName
		, "tempName"=> "at_res_step5"
		, "kakaoMsg"=>$kakaoMsg
		, "userPhone"=> $userPhone
		, "link1"=>"orderview?num=1&resNumber=".$ResNumber //예약조회/취소
		, "link2"=>"surflocation?seq=".$shopSeq //지도로 위치보기
		, "link3"=>"eatlist" //제휴업체 목록
		, "link4"=>"event" //공지사항
		, "link5"=>""
		, "smsOnly"=>"N"
	);
	sendKakao($arrKakao); //알림톡 발송

	//카카오톡 업체 발송
	$select_query = 'SELECT * FROM AT_PROD_MAIN WHERE seq = '.$shopSeq;
	$result_setlist = mysqli_query($conn, $select_query);
	$rowshop = mysqli_fetch_array($result_setlist);

	$admin_tel = $rowshop["tel_kakao"];
	// $admin_tel = "010-4437-0009";

	$msgTitle = '액트립 ['.$userName.']님 예약안내';
	$kakaoMsg = $msgTitle.'\n안녕하세요. 액트립 '.$shopname.' 예약건 안내입니다.\n\n액트립 예약정보 [입금완료]\n ▶ 예약번호 : '.$ResNumber.'\n ▶ 예약자 : '.$userName.'\n'.$surfshopMsg.$etcMsg.'---------------------------------\n ▶ 안내사항\n      - 예약내역 확인 후 승인처리 부탁드립니다.\n      - 예약이 불가할 경우 임시취소해주시면 취소진행하겠습니다.\n\n';

	$arrKakao = array(
		"gubun"=> $code
		, "admin"=> "N"
		, "smsTitle"=> $msgTitle
		, "userName"=> $userName
		, "tempName"=> "at_shop_step1"
		, "kakaoMsg"=>$kakaoMsg
		, "userPhone"=> $admin_tel
		, "link1"=>"surfadminkakao?param=".urlencode(encrypt(date("Y-m-d").'|'.$shopSeq)) //전체 예약목록
		, "link2"=>"surfadminkakao?param=".urlencode(encrypt(date("Y-m-d").'|'.$ResNumber.'|'.$shopSeq)) //현재 예약건 보기
		, "link3"=>""
		, "link4"=>""
		, "link5"=>""
		, "smsOnly"=>"N"
	);
	//sendKakao($arrKakao);

	$info1_title = "신청목록";
	$info1 = str_replace('      -', '&nbsp;&nbsp;&nbsp;-', str_replace('\n', '<br>', $surfshopMsg));
	$info2_title = "";
	$info2 = "";

	$arrMail = array(
		"gubun"=> "surf"
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
	//sendMail($arrMail); //메일 발송
	
	$select_query = "UPDATE `AT_RES_SUB` 
						SET res_confirm = 3
							,upddate = now()
							,confirmdate = now()
							,upduserid = 'autobank'
						WHERE ressubseq IN (".$ressubseq.")";
	$result_set = mysqli_query($conn, $select_query);
	if(!$result_set) goto errGo;
}

if(!$success){
	errGo:
	mysqli_query($conn, "ROLLBACK");
	echo 'err';
}else{
	mysqli_query($conn, "COMMIT");
	echo '0';
}
mysqli_close($conn);
?>

