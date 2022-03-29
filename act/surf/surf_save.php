<?php
include __DIR__.'/../db.php';
include __DIR__.'/../surf/surfkakao.php';
include __DIR__.'/../surf/surfmail.php';
include __DIR__.'/../surf/surffunc.php';

$param = $_REQUEST["resparam"];
$InsUserID = ($_REQUEST["userId"] == "") ? $_REQUEST["userName"] : $_REQUEST["userId"];
$shopseq = $_REQUEST["shopseq"];
$datetime = date('Y/m/d H:i'); 
$TotalPrice = 0;

/*
예약상태
    0 : 미입금
    1 : 예약대기
    2 : 임시확정
    3 : 확정
    4 : 환불요청
    5 : 환불완료
    6 : 임시취소
    7 : 취소
    8 : 입금완료
*/

$success = true;
if($param == "BusI"){
    $ResNumber = '2'.time().substr(mt_rand(0, 99) + 100, 1, 2); //예약번호 랜덤생성
    
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

	$SurfDateBusY = $_REQUEST["hidbusDate".$busTypeY]; //양양행 날짜
    $SurfDateBusS = $_REQUEST["hidbusDate".$busTypeS]; //서울행 날짜
    
	$busNumY = $_REQUEST["hidbusNum".$busTypeY]; //양양행 버스번호
    $busNumS = $_REQUEST["hidbusNum".$busTypeS]; //서울행 버스번호
    
	$arrSeatY = $_REQUEST["hidbusSeat".$busTypeY]; //양양행 좌석번호
    $arrSeatS = $_REQUEST["hidbusSeat".$busTypeS]; //서울행 좌석번호
    
	$startLocationY = $_REQUEST["startLocation".$busTypeY]; //양양행 출발 정류장
	$endLocationY = $_REQUEST["endLocation".$busTypeY]; //양양행 도착 정류장
	$startLocationS = $_REQUEST["startLocation".$busTypeS]; //서울행 출발 정류장
	$endLocationS = $_REQUEST["endLocation".$busTypeS]; //서울행 도착 정류장

	$userName = $_REQUEST["userName"];
	$userId = $_REQUEST["userId"];
	$userPhone = $_REQUEST["userPhone1"]."-".$_REQUEST["userPhone2"]."-".$_REQUEST["userPhone3"];
	$usermail = $_REQUEST["usermail"];
	$etc = $_REQUEST["etc"];
    
	for($i = 0; $i < count($SurfDateBusY); $i++){
        $select_query = 'SELECT res_spoint FROM AT_RES_SUB where res_date = "'.$SurfDateBusY[$i].'" AND res_bus = "'.$busNumY[$i].'" AND res_seat = "'.$arrSeatY[$i].'" AND res_confirm IN (0, 1, 2, 3)';
        //echo $select_query;
        $result_setlist = mysqli_query($conn, $select_query);
		$count = mysqli_num_rows($result_setlist);

		if($count > 0){
			echo '<script>alert("['.$SurfDateBusY[$i].'] '.$arrSeatY[$i].'번 좌석은 이미 예약된 자리입니다.\n\n다른좌석을 선택해주세요.");parent.fnUnblock("#divConfirm");</script>';
			return;
		}
	}

	for($i = 0; $i < count($SurfDateBusS); $i++){
		$select_query = 'SELECT res_spoint FROM AT_RES_SUB where res_date = "'.$SurfDateBusS[$i].'" AND res_bus = "'.$busNumS[$i].'" AND res_seat = "'.$arrSeatS[$i].'" AND res_confirm IN (0, 1, 2, 3)';
		$result_setlist = mysqli_query($conn, $select_query);
		$count = mysqli_num_rows($result_setlist);

		if($count > 0){
			echo '<script>alert("['.$SurfDateBusS[$i].'] '.$arrSeatS[$i].'번 좌석은 이미 예약된 자리입니다.\n\n다른좌석을 선택해주세요.");parent.fnUnblock("#divConfirm");</script>';
			return;
		}
    }
    
	mysqli_query($conn, "SET AUTOCOMMIT=0");
	mysqli_query($conn, "BEGIN");

	$busSeatInfoS = "";
	$busSeatInfoE = "";
	$busStopInfoS = "";
	$busStopInfoE = "";
	$arrSeatInfoS = array();
	$arrSeatInfoE = array();
	$arrStopInfoS = array();
	$arrStopInfoE = array();

    $res_Price = 20000;
    $res_totalprice = $res_Price;
    // 할인 쿠폰적용
    $coupon = $_REQUEST["couponcode"];
    $res_price_coupon = 0;
    $select_query = "SELECT a.*, b.dis_price, b.dis_type, b.sdate, b.edate, b.issue_type FROM AT_COUPON_CODE a INNER JOIN AT_COUPON b ON a.couponseq = b.couponseq WHERE a.coupon_code = '$coupon' AND a.use_yn = 'N' AND a.seq = 'BUS'";
  
    $result = mysqli_query($conn, $select_query);
    $rowMain = mysqli_fetch_array($result);
    $chkCnt = mysqli_num_rows($result); //체크 개수

    $couponseq = 0;
    if($chkCnt > 0){
        $res_price_coupon = $rowMain["dis_price"];
        $issue_type = $rowMain["issue_type"];
        $couponseq = $rowMain["couponseq"];

        if($res_price_coupon <= 100){ //퍼센트 할인
            $res_totalprice = $res_Price * (1 - ($res_price_coupon / 100));
        }else{ //금액할인
            $res_totalprice = $res_Price - $res_price_coupon;
        }

        if($issue_type == "A"){
            $user_ip = $_SERVER['REMOTE_ADDR'];
            $select_query = "UPDATE AT_COUPON_CODE 
                                SET use_yn = 'Y'
                                ,user_ip = '$user_ip'
                                ,use_date = now()
                            WHERE seq = 'BUS' AND coupon_code = '$coupon';";
            $result_set = mysqli_query($conn, $select_query);
            if(!$result_set) goto errGo;
        }
    }

    //예약채널 사이트 쿠폰 코드가 있으면 예약확정
     //7:서핑버스 네이버쇼핑, 10:네이버예약, 11:프립, 12:마이리얼트립, 14:망고서프패키지, 15:서프엑스
    $coupon_array = array("JOABUS", "KLOOK", "NAVER", "FRIP", "MYTRIP", "SURFX");
    if(in_array($coupon, $coupon_array) || in_array($couponseq, array(7, 10, 11, 12, 14, 15)))
    {
        $res_confirm = 3; //확정
        $InsUserID = $coupon;
        $msgType = 1; //100% 할인 쿠폰
    }
    else
    {
        $res_confirm = 0; //입금대기
        $msgType = 2; //일반예약
    }

    $week = array("일","월","화","수","목","금","토");
    //양양행 좌석예약
    for($i = 0; $i < count($SurfDateBusY); $i++){
        $TotalPrice += $res_totalprice;
        $select_query = "INSERT INTO `AT_RES_SUB` (`resnum`, `code`, `seq`, `optseq`, `shopname`, `sub_title`, `optname`, `optsubname`, `res_date`, `res_time`, `res_bus`, `res_busnum`, `res_seat`, `res_spoint`, `res_spointname`, `res_epoint`, `res_epointname`, `res_confirm`, `res_price`, `res_price_coupon`, `res_coupon`, `res_totalprice`, `res_ea`, `res_m`, `res_w`, `rtn_charge_yn`, `rtn_chargeprice`, `rtn_totalprice`, `rtn_bankinfo`, `cashreceipt_yn`, `insuserid`, `insdate`, `upduserid`, `upddate`)  VALUES ('$ResNumber', 'bus', $shopseq, null, '".$busTitleName." 서핑버스', null, null, null, '$SurfDateBusY[$i]', null, '$busNumY[$i]', '$busNumY[$i]', '$arrSeatY[$i]', '$startLocationY[$i]', '$startLocationY[$i]', '$endLocationY[$i]', '$endLocationY[$i]', $res_confirm, $res_Price, $res_price_coupon, '$coupon', $res_totalprice, 1, 0, 0, 'Y', 0, 0, null, 'N', '$InsUserID', '$datetime', '$InsUserID', '$datetime');";
        $result_set = mysqli_query($conn, $select_query);
        //echo $select_query.'<br>';
        if(!$result_set) goto errGo;
    
        if(array_key_exists($SurfDateBusY[$i].$busNumY[$i], $arrSeatInfoS)){
            $arrSeatInfoS[$SurfDateBusY[$i].$busNumY[$i]] .= '      - '.$arrSeatY[$i].'번 ('.$startLocationY[$i].' -> '.$endLocationY[$i].')\n';
        }else{
            $weekday = $week[date('w', strtotime($SurfDateBusY[$i]))];

            $arrSeatInfoS[$SurfDateBusY[$i].$busNumY[$i]] = '['.$SurfDateBusY[$i].'('.$weekday.')] '.fnBusNum($busNumY[$i]).'\n      - '.$arrSeatY[$i].'번 ('.$startLocationY[$i].' -> '.$endLocationY[$i].')\n';
        }

        $arrData = explode("|", fnBusPoint($startLocationY[$i], $busNumY[$i]));
        $arrStopInfoS[$startLocationY[$i]] = '    ['.$startLocationY[$i].'] '.$arrData[0].'\n      - '.$arrData[1].'\n';
    }
    
    //서울행 좌석예약
    for($i = 0; $i < count($SurfDateBusS); $i++){
        $TotalPrice += $res_totalprice;
        $select_query = "INSERT INTO `AT_RES_SUB` (`resnum`, `code`, `seq`, `optseq`, `shopname`, `sub_title`, `optname`, `optsubname`, `res_date`, `res_time`, `res_bus`, `res_busnum`, `res_seat`, `res_spoint`, `res_spointname`, `res_epoint`, `res_epointname`, `res_confirm`, `res_price`, `res_price_coupon`, `res_coupon`, `res_totalprice`, `res_ea`, `res_m`, `res_w`, `rtn_charge_yn`, `rtn_chargeprice`, `rtn_totalprice`, `rtn_bankinfo`, `cashreceipt_yn`, `insuserid`, `insdate`, `upduserid`, `upddate`)  VALUES ('$ResNumber', 'bus', $shopseq, null, '".$busTitleName." 서핑버스', null, null, null, '$SurfDateBusS[$i]', null, '$busNumS[$i]', '$busNumS[$i]', '$arrSeatS[$i]', '$startLocationS[$i]', '$startLocationS[$i]', '$endLocationS[$i]', '$endLocationS[$i]', $res_confirm, $res_Price, $res_price_coupon, '$coupon', $res_totalprice, 1, 0, 0, 'Y', 0, 0, null, 'N', '$InsUserID', '$datetime', '$InsUserID', '$datetime');";
        $result_set = mysqli_query($conn, $select_query);
        //echo $select_query.'<br>';
        if(!$result_set) goto errGo;

        if(array_key_exists($SurfDateBusS[$i].$busNumS[$i], $arrSeatInfoE)){
            $arrSeatInfoE[$SurfDateBusS[$i].$busNumS[$i]] .= '      - '.$arrSeatS[$i].'번 ('.$startLocationS[$i].' -> '.$endLocationS[$i].')\n';
        }else{
            $weekday = $week[date('w', strtotime($SurfDateBusS[$i]))];

            $arrSeatInfoE[$SurfDateBusS[$i].$busNumS[$i]] = '['.$SurfDateBusS[$i].'('.$weekday.')] '.fnBusNum($busNumS[$i]).'\n      - '.$arrSeatS[$i].'번 ('.$startLocationS[$i].' -> '.$endLocationS[$i].')\n';
        }

        $arrData = explode("|", fnBusPoint($startLocationS[$i], $busNumS[$i]));
        $arrStopInfoE[$startLocationS[$i]] = '    ['.$startLocationS[$i].'] '.$arrData[0].'\n      - '.$arrData[1].'\n';
    }

    $select_query = "INSERT INTO `AT_RES_MAIN` (`resnum`, `pay_type`, `pay_info`, `user_id`, `user_name`, `user_tel`, `user_email`, `etc`, `insuserid`, `insdate`) VALUES ('$ResNumber', 'B', '무통장입금', '$InsUserID', '$userName', '$userPhone', '$usermail', '$etc', '$InsUserID', '$datetime');";
    //echo $select_query.'<br>';
    $result_set = mysqli_query($conn, $select_query);
    if(!$result_set) goto errGo;

    $select_query = "UPDATE AT_PROD_MAIN SET sell_cnt = sell_cnt + 1 WHERE seq = $shopseq;";
    $result_set = mysqli_query($conn, $select_query);
    if(!$result_set) goto errGo;

	if(!$success){
        errGo:
		mysqli_query($conn, "ROLLBACK");
		echo '<script>alert("예약진행 중 오류가 발생하였습니다.\n\n관리자에게 문의해주세요.");</script>';
	}else{
        // 예약좌석 정보 : 양양행
		foreach($arrSeatInfoS as $x) {
			$busSeatInfoS .= $x;
		}
        // 정류장 정보 : 양양행
		foreach($arrStopInfoS as $x) {
			$busStopInfoS .= $x;
		}
        
        // 예약좌석 정보 : 서울행
		foreach($arrSeatInfoE as $x) {
			$busSeatInfoE .= $x;
		}
        // 정류장 정보 : 서울행
		foreach($arrStopInfoE as $x) {
			$busStopInfoE .= $x;
		}

        $busSeatInfoTotal = "";

        if($busSeatInfoS != ""){
            //$busSeatInfoTotal .= " ▶ [양양행] 좌석안내\n".$busSeatInfoS.'\n\n ▶ 탑승시간/위치 안내\n'.$busStopInfoS;
            $busSeatInfoTotal .= " ▶ ".$busSeatInfoS;
        }
        if($busSeatInfoE != ""){
            //$busSeatInfoTotal .= " ▶ [서울행] 좌석안내\n".$busSeatInfoE.'\n\n ▶ 탑승시간/위치 안내\n'.$busStopInfoE;
            if($busSeatInfoS != ""){
                $busSeatInfoTotal .= "\n";
            }
            $busSeatInfoTotal .= " ▶ ".$busSeatInfoE;
        }
        $busSeatInfoTotal .= '\n ▶ 탑승시간/위치 안내\n      - https://actrip.co.kr/pointlist\n\n';

		if($etc != ''){
			//$etcMsg = ' ▶ 요청사항\n      '.$etc.'\n';
        }
        $totalPrice = " ▶ 총 결제금액 : ".number_format($TotalPrice)."원\n";
        
        //신규 로직 : 2022-01-04
        $gubun_title = $busTitleName.'서핑버스';
        $msgChannelName = "";
        $msgChannelName2 = '\n      - 예약취소는 예약하셨던 업체에서 접수가능합니다.';
        
        if($coupon == "JOABUS"){
            $gubun_title = "조아서프 패키지 서핑버스";
            $msgChannelName = "&조아서프";
            $msgChannelName2 = "";
        }else if($coupon == "FRIP"){
            $gubun_title = "프립 서핑버스";
            $msgChannelName = '&프립';
        }else if($coupon == "MYTRIP"){
            $gubun_title = "마이리얼트립 서핑버스";
            $msgChannelName = '&마이리얼트립';
        }else if($coupon == "KLOOK"){
            $gubun_title = "클룩 서핑버스";
            $msgChannelName = '&클룩';
        }else if($coupon == "SURFX"){
            $gubun_title = "SURFX 서핑버스";
            $msgChannelName = '&SURFX';
        }else if($coupon == "NAVER"){
            
        }else{

        }
        
        //$msgTitle = '액트립'.$msgChannelName.' 서핑버스 예약안내';
        $msgTitle = '액트립 서핑버스 예약안내';

        if($msgType == 2){
            $kakaoMsg = $msgTitle.'\n\n안녕하세요. '.$userName.'님\n서핑버스를 예약해주셔서 감사합니다.\n\n예약정보 [입금대기]\n ▶ 예약번호 : '.$ResNumber.'\n ▶ 예약자 : '.$userName.'\n'.$busSeatInfoTotal.$etcMsg.$totalPrice.'---------------------------------\n ▶ 안내사항\n      - 1시간 이내 미입금시 자동취소됩니다.\n\n ▶ 입금계좌\n      - 우리은행 / 1002-845-467316 / 이승철\n\n';

            $tempName = "at_bus_02";
            $btn_ResSearch = "orderview?num=1&resNumber=".$ResNumber; //예약조회/취소
            $btn_ResChange = "pointchange?num=1&resNumber=".$ResNumber; //예약조회/취소
            $btn_ResGPS = "";
            $btn_ResCustomer = "kakaocustomer"; //문의하기
            $btn_Notice = "";
            $btn_ResContent = ""; //예약 상세안내
        }else{
            $kakaoMsg = $msgTitle.'\n\n안녕하세요. '.$userName.'님\n서핑버스를 예약해주셔서 감사합니다.\n\n예약정보 [예약확정]\n ▶ 예약번호 : '.$ResNumber.'\n ▶ 예약자 : '.$userName.'\n'.$busSeatInfoTotal.$etcMsg.'---------------------------------\n ▶ 안내사항\n      - 교통상황으로 인해 정류장에 지연 도착할 수 있으니 양해부탁드립니다.'.$msgChannelName2.'\n      - 이용일, 탑승시간, 탑승위치 꼭 확인 부탁드립니다.\n      - 탑승시간 5분전에는 도착해주세요~\n\n';

            $tempName = "at_bus_12";
            $btn_ResSearch = "orderview?num=1&resNumber=".$ResNumber; //예약조회/취소
            $btn_ResChange = "pointchange?num=1&resNumber=".$ResNumber; //예약조회/취소
            $btn_ResGPS = "surfbusgps"; //서핑버스 실시간위치 조회
            $btn_ResCustomer = "kakaocustomer"; //문의하기
            $btn_Notice = "";
            $btn_ResContent = ""; //예약 상세안내
        }
        
        if($coupon == "JOABUS"){
            //조아서프 관리자 카카오톡 발송
            $arrKakao = array(
                "gubun"=> "bus"
                , "admin"=> "N"
                , "smsTitle"=> $msgTitle
                , "userName"=> $userName
                , "tempName"=> $tempName
                , "kakaoMsg"=>$kakaoMsg
                //, "userPhone"=> "010-9509-9994"
                , "userPhone"=> "010-8368-6099"
                , "btn_ResContent"=> $btn_ResContent
                , "btn_ResSearch"=> $btn_ResSearch
                , "btn_ResChange"=> $btn_ResChange
                , "btn_ResGPS"=> $btn_ResGPS
                , "btn_ResCustomer"=> $btn_ResCustomer
                , "btn_Notice"=> $btn_Notice
                , "smsOnly"=>"N"
                , "PROD_NAME"=>"서핑버스_조아서프"
                , "PROD_URL"=>$shopseq
                , "PROD_TYPE"=>"bus"
                , "RES_CONFIRM"=>$res_confirm
            );
            $arrRtn = sendKakao($arrKakao); //알림톡 발송
        
            // 카카오 알림톡 DB 저장 START
            $select_query = kakaoDebug($arrKakao, $arrRtn);
            $result_set = mysqli_query($conn, $select_query);
            if(!$result_set) goto errGo;
            // 카카오 알림톡 DB 저장 END
        }
        
        // 고객 카카오톡 발송
        $arrKakao = array(
            "gubun"=> "bus"
            , "admin"=> "N"
            , "smsTitle"=> $msgTitle
            , "userName"=> $userName
            , "tempName"=> $tempName
            , "kakaoMsg"=>$kakaoMsg
            , "userPhone"=> $userPhone
            , "btn_ResContent"=> $btn_ResContent
            , "btn_ResSearch"=> $btn_ResSearch
            , "btn_ResChange"=> $btn_ResChange
            , "btn_ResGPS"=> $btn_ResGPS
            , "btn_ResCustomer"=> $btn_ResCustomer
            , "btn_Notice"=> $btn_Notice
            , "smsOnly"=>"N"
            , "PROD_NAME"=>"서핑버스"
            , "PROD_URL"=>$shopseq
            , "PROD_TYPE"=>"bus"
            , "RES_CONFIRM"=>$res_confirm
        );
        
        //서핑버스 네이버예약 : 7, 네이버쇼핑 : 10, 프립 : 11, 마이리얼트립 : 12 알림톡 제외
        if($msgType > 0){
            $arrRtn = sendKakao($arrKakao); //알림톡 발송
        
            // 카카오 알림톡 DB 저장 START
            $select_query = kakaoDebug($arrKakao, $arrRtn);
            $result_set = mysqli_query($conn, $select_query);
            if(!$result_set) goto errGo;
            // 카카오 알림톡 DB 저장 END
        }

		mysqli_query($conn, "COMMIT");

        // 이메일 발송
		//$to = "lud1@naver.com,ttenill@naver.com";
        $to = "lud1@naver.com";
        if(strrpos($usermail, "@") > 0){
            $to .= ','.$usermail;
        }

        $info1_title = "좌석안내";
        $info1 = "";
        if($busSeatInfoS != ""){
            $info1 .= str_replace('      -', '&nbsp;&nbsp;&nbsp;-', str_replace('\n', '<br>', $busSeatInfoS));
        }

        if($busSeatInfoE != ""){
            $info1 .= str_replace('      -', '&nbsp;&nbsp;&nbsp;-', str_replace('\n', '<br>', $busSeatInfoE));
        }

        $info2_title = "탑승시간/<br>위치 안내";
        $info2 = "https://actrip.co.kr/pointlist";
        /*
        if($busSeatInfoS != ""){
            $info2 .= str_replace('      -', '&nbsp;&nbsp;&nbsp;-', str_replace('\n', '<br>', $busStopInfoS));
        }
        if($busSeatInfoE != ""){
            $info2 .= str_replace('      -', '&nbsp;&nbsp;&nbsp;-', str_replace('\n', '<br>', $busStopInfoE));
        }
        */

        $arrMail = array(
            "gubun"=> "bus"
            , "gubun_step" => $res_confirm
            , "gubun_title" => $gubun_title
            , "mailto"=> $to
            , "mailfrom"=> "surfbus_res@actrip.co.kr"
            , "mailname"=> "actrip"
            , "userName"=> $userName
            , "ResNumber"=> $ResNumber
            , "userPhone" => $userPhone
            , "etc" => $etc
            , "totalPrice1" => number_format($TotalPrice).'원'
            , "totalPrice2" => ""
            , "banknum" => "우리은행 / 1002-845-467316 / 이승철"
            , "info1_title"=> $info1_title
            , "info1"=> $info1
            , "info2_title"=> $info2_title
            , "info2"=> $info2
        );

        //네이버예약, 네이버쇼핑 알림톡 제외
        if($msgType > 0){
            sendMail($arrMail); //메일 발송
        }
        
        echo '<script>alert("'.$busTitleName.' 서핑버스 예약이 완료되었습니다.");parent.location.href="/orderview?num=2&resNumber='.$ResNumber.'";</script>';
	}
}else if($param == "SurfShopI"){
    $ResNumber = '1'.time().substr(mt_rand(0, 99) + 100, 1, 2); //예약번호 랜덤생성
	$resSeq = $_REQUEST["resSeq"]; //예약항목 Seq
	$resDate = $_REQUEST["resDate"]; //이용날짜
	$resTime = $_REQUEST["resTime"]; //이용시간
	$resDay = $_REQUEST["resDay"]; //숙박일
	$resGubun = $_REQUEST["resGubun"]; //예약항목 구분
	$resM = $_REQUEST["resM"]; //예약인원 : 남
	$resW = $_REQUEST["resW"]; //예약인원 : 여

	$resNumAll = $_REQUEST["resNumAll"]; //신청 옵션 전체
	$userName = $_REQUEST["userName"];
	$userId = $_REQUEST["userId"];
	$userPhone = $_REQUEST["userPhone1"]."-".$_REQUEST["userPhone2"]."-".$_REQUEST["userPhone3"];
	$usermail = $_REQUEST["usermail"];
    $etc = $_REQUEST["etc"];
    
    // 할인 쿠폰적용
    $coupon = $_REQUEST["couponcode"];
    $res_price_coupon = 0;
    $res_totalprice = 0;
    $select_query = "SELECT a.*, b.dis_price, b.dis_type, b.sdate, b.edate, b.issue_type FROM AT_COUPON_CODE a INNER JOIN AT_COUPON b ON a.couponseq = b.couponseq WHERE a.coupon_code = '$coupon' AND a.use_yn = 'N' AND a.seq IN ('SUR', 'BBQ')";

    $result = mysqli_query($conn, $select_query);
    $rowMain = mysqli_fetch_array($result);
    $chkCnt = mysqli_num_rows($result); //체크 개수
    if($chkCnt > 0){
        $res_price_coupon = $rowMain["dis_price"];
        $issue_type = $rowMain["issue_type"];

        if($res_price_coupon <= 100){ //퍼센트 할인
            $res_totalprice = (1 - ($res_price_coupon / 100));
        }else{ //금액할인
            $res_totalprice = $res_price_coupon;
        }

        if($issue_type == "A"){
            $user_ip = $_SERVER['REMOTE_ADDR'];
            $select_query = "UPDATE AT_COUPON_CODE 
                                SET use_yn = 'Y'
                                ,user_ip = '$user_ip'
                                ,use_date = now()
                            WHERE seq IN ('SUR', 'BBQ') AND coupon_code = '$coupon';";
            $result_set = mysqli_query($conn, $select_query);
            if(!$result_set) goto errGoSurf;
        }
    }

	$select_query = "SELECT a.*, b.code, b.shopname, b.tel_kakao, c.uppercode as areagubun, b.category FROM `AT_PROD_OPT` as a 
                        INNER JOIN AT_PROD_MAIN as b
                            ON a.seq = b.seq 
                        INNER JOIN AT_CODE c 
                            ON b.category = c.code 
                                AND b.code in ('surf', 'bbq') 
                        WHERE a.seq = $shopseq
                            AND a.use_yn = 'Y' 
                            AND a.optseq IN ($resNumAll)";
	$result_setlist = mysqli_query($conn, $select_query);

	$arrOpt = array();
	$arrOptVlu = array();
	$arrOptInfo = array();
	$arrStayDay = array();
	while ($rowOpt = mysqli_fetch_assoc($result_setlist)){
		$arrOpt[$rowOpt["optseq"]] = $rowOpt["sell_price"];
		$arrOptVlu[$rowOpt["optseq"]] = $rowOpt["optname"];
		$arrOptInfo[$rowOpt["optseq"]] = $rowOpt["opt_info"];
		$arrStayDay[$rowOpt["optseq"]] = $rowOpt["stay_day"];

		$shopname = $rowOpt["shopname"];
		$shopbank = "우리은행 / 1002-845-467316 / 이승철";
		$areagubun = $rowOpt["areagubun"];
		$tel_kakao = $rowOpt["tel_kakao"]; //카톡 발송 연락처

		$ordersearch = "surfres?seq=".$shopseq;
		if($areagubun == "surfeast1"){
			$area = "[양양]";
		}else if($areagubun = "surfeast2"){
			$area = "[고성]";
		}else if($areagubun == "surfeast3"){
			$area = "[강릉,동해]";
		}else if($areagubun == "surfsouth"){
			$area = "[부산]";
		}else if($areagubun == "surfjeju"){
			$area = "[제주]";
		}else if($areagubun == "surfsouth"){
			$area = "[남해]";
		}else if($areagubun == "surfwest"){
			$area = "[서해]";
		}else if($areagubun == "etc"){
			$area = "[기타]";
		}else{
			$area = "[기타]";
        }
	}

	mysqli_query($conn, "SET AUTOCOMMIT=0");
	mysqli_query($conn, "BEGIN");

	$surfshopMsg = "";
	for($i = 0; $i < count($resSeq); $i++){
        $select_query = "SELECT *, year(sdate) as yearS, month(sdate) as monthS, day(sdate) as dayS, year(edate) as yearE, month(edate) as monthE, day(edate) as dayE FROM AT_PROD_DAY where seq = $shopseq AND sdate <= '$resDate[$i]' AND edate >= '$resDate[$i]' AND use_yn = 'Y' limit 1";

		$result_cal = mysqli_query($conn, $select_query);
		$row_cal = mysqli_fetch_array($result_cal);

        $optseq = $resSeq[$i]; //옵션 seq
        /*
        resGubun
          lesson : 서핑강습
          rent : 렌탈
          stay : 숙소
          pkg : 패키지
          bbq : 바베큐
        */

        //성수기 항목
        $arrdate = explode("-", $resDate[$i]); // 들어온 날짜를 년,월,일로 분할해 변수로 저장합니다.
        $s_Y=$arrdate[0]; // 지정된 년도 
        $s_m=$arrdate[1]; // 지정된 월
        $s_d=$arrdate[2]; // 지정된 요일

        $thisWeekNum = date("w",mktime(0,0,0,$s_m,$s_d,$s_Y));
        if(strrpos($row_cal["day_week"], (string)$thisWeekNum) === false){
            $eaPrice = $arrOpt[$optseq];
        }else{
            if($resGubun[$i] == "lesson"){
                $stayPlus = $arrStayDay[$optseq]; //숙박 여부
                
                //이전일 요일구하기
                $preWeekNum = date("w", strtotime(date("Y-m-d",mktime(0,0,0,$s_m,$s_d,$s_Y))." -1 day"));
                if(strrpos($row_cal["day_week"], (string)$preWeekNum) === false){
                    $eaPrePrice = 0;
                }else{
                    $eaPrePrice = $row_cal["stay_price"];
                }

                if($stayPlus == 0){
                    $eaPrice = $arrOpt[$optseq] + $row_cal["stay_price"];
                }else if($stayPlus == 1){
                    $eaPrice = $arrOpt[$optseq] + $eaPrePrice;
                }else if($stayPlus == 2){
                    $eaPrice = $arrOpt[$optseq] + $eaPrePrice + $row_cal["stay_price"];
                }else{
                    $eaPrice = $arrOpt[$optseq];
                }
            }else if($resGubun[$i] == "rent"){
                $eaPrice = $arrOpt[$optseq];
            }else if($resGubun[$i] == "pkg"){
                $eaPrice = $arrOpt[$optseq] + $row_cal["stay_price"];
            }else if($resGubun[$i] == "bbq"){
                $eaPrice = $arrOpt[$optseq];
            }
        }
		$optname = $arrOptVlu[$optseq];

        $sumMem = $resM[$i] + $resW[$i];
        $sumMemPrice = ($eaPrice * $resM[$i]) + ($eaPrice * $resW[$i]);
        if($chkCnt > 0){
            if($res_price_coupon <= 100){ //퍼센트 할인
                $sumPrice = $sumMemPrice * $res_totalprice;
            }else{ //금액 할인
                $sumPrice = $sumMemPrice - $res_totalprice;
            }
        }else{
            $sumPrice = $sumMemPrice;
        }
		$TotalPrice += $sumPrice;

        if($coupon == "ATBLOG" || $coupon == "NAVERA"){
            $res_confirm = 8;
            $InsUserID = $coupon;
        }else if($coupon == "KLOOK11"){
            $res_confirm = 3;
            $InsUserID = $coupon;
        }else{
            $res_confirm = 0;
        }

        $select_query = "INSERT INTO `AT_RES_SUB` (`resnum`, `code`, `seq`, `optseq`, `shopname`, `sub_title`, `optname`, `optsubname`, `res_date`, `res_time`, `res_bus`, `res_busnum`, `res_seat`, `res_spoint`, `res_spointname`, `res_epoint`, `res_epointname`, `res_confirm`, `res_price`, `res_price_coupon`, `res_coupon`, `res_totalprice`, `res_ea`, `res_m`, `res_w`, `rtn_charge_yn`, `rtn_chargeprice`, `rtn_totalprice`, `rtn_bankinfo`, `cashreceipt_yn`, `insuserid`, `insdate`, `upduserid`, `upddate`)  VALUES ('$ResNumber', 'surf', $shopseq, $optseq, '$shopname', '$resGubun[$i]', '$optname', '$arrOptInfo[$optseq]', '$resDate[$i]', '$resTime[$i]', '$arrStayDay[$optseq]', '', '', '', '', '', '', $res_confirm, $sumMemPrice, $res_price_coupon, '$coupon', $sumPrice, $sumMem, $resM[$i], $resW[$i], 'Y', 0, 0, null, 'N', '$InsUserID', '$datetime', '$InsUserID', '$datetime');";
        $result_set = mysqli_query($conn, $select_query);
        if(!$result_set) goto errGoSurf;

		$TimeDate = '';
		if(($resGubun[$i] == "lesson" || $resGubun[$i] == "pkg") && $resTime[$i] != ""){
			$TimeDate = '      - 시간 : '.$resTime[$i].'\n';
		}

		$ResNum = '      - 인원 : ';
		if($resM[$i] > 0){
			$ResNum .= '남:'.$resM[$i].'명';
		}
        if($resM[$i] > 0 && $resW[$i] > 0){
            $ResNum .= ',';
        }
		if($resW[$i] > 0){
			$ResNum .= '여:'.$resW[$i].'명';
        }
        $ResNum .= '\n';

        $ResOptInfo = "";
        $ResOptStay = "";
        if($resGubun[$i] == "lesson"){
            $stayPlus = $arrStayDay[$optseq]; //숙박 여부
            
            //이전일 요일구하기
            $preDate = date("Y-m-d", strtotime(date("Y-m-d",mktime(0,0,0,$s_m,$s_d,$s_Y))." -1 day"));
            $nextDate = date("Y-m-d", strtotime(date("Y-m-d",mktime(0,0,0,$s_m,$s_d,$s_Y))." +1 day"));
            if($stayPlus == 0){
                $ResOptStay = '      - 숙박일 : '.$resDate[$i].'(1박)\n';
            }else if($stayPlus == 1){
                $ResOptStay = '      - 숙박일 : '.$preDate.'(1박)\n';
            }else if($stayPlus == 2){
                $ResOptStay = '      - 숙박일 : '.$preDate.'(2박)\n';
            }else{
                //$ResOptInfo = '      - 안내 : '.$arrOptInfo[$optseq].'\n';
            }
            $ResOptInfo = $TimeDate;
        }else if($resGubun[$i] == "rent"){

        }else if($resGubun[$i] == "pkg"){
            //$ResOptInfo = '      - '.$arrOptInfo[$optseq].'\n';
        }else if($resGubun[$i] == "bbq"){
            //$ResOptInfo = '      - '.str_replace('<br>', '\n      - ', $arrOptInfo[$optseq]).'\n';
        }
        
		$surfshopMsg .= '    ['.$optname.']\n      - 예약일 : '.$resDate[$i].'\n'.$ResOptStay.$ResNum.$ResOptInfo.'\n';
    }
    
    $select_query = "INSERT INTO `AT_RES_MAIN` (`resnum`, `pay_type`, `pay_info`, `user_id`, `user_name`, `user_tel`, `user_email`, `etc`, `insuserid`, `insdate`) VALUES ('$ResNumber', 'B', '무통장입금', '$InsUserID', '$userName', '$userPhone', '$usermail', '$etc', '$InsUserID', '$datetime');";
    //echo $select_query.'<br>';
    $result_set = mysqli_query($conn, $select_query);
    if(!$result_set) goto errGoSurf;

    $select_query = "UPDATE AT_PROD_MAIN SET sell_cnt = sell_cnt + 1 WHERE seq = $shopseq;";
    $result_set = mysqli_query($conn, $select_query);
    if(!$result_set) goto errGoSurf;

	if(!$success){
        errGoSurf:
		mysqli_query($conn, "ROLLBACK");
		echo '<script>alert("예약진행 중 오류가 발생하였습니다.\n\n관리자에게 문의해주세요.");parent.fnSaveErr("divConfirm");</script>';
	}else{
		//==================== 카카오톡 발송 Start ====================
		if($etc != ''){
			//$etcMsg = ' ▶ 요청사항\n      '.$etc.'\n';
        }
        $totalPrice = " ▶ 총 결제금액 : ".number_format($TotalPrice)."원\n";
        
        if($coupon == "ATBLOG" || $coupon == "NAVERA"){
            // $infomsg = "\n      - 예약하신 내역이 확정처리되었습니다.\n      - 이용일 및 신청정보 확인부탁드립니다.";
            

            // $msgTitle = '액트립 ['.$shopname.'] 예약안내';
            // $kakaoMsg = $msgTitle.'\n안녕하세요. '.$userName.'님\n\n'.$shopname.' 예약정보 [예약확정]\n ▶ 예약번호 : '.$ResNumber.'\n ▶ 예약자 : '.$userName.'\n ▶ 신청목록\n'.$surfshopMsg.$etcMsg.'---------------------------------\n ▶ 안내사항'.$infomsg.'\n\n ▶ 문의\n      - 010.3308.6080\n      - http://pf.kakao.com/_HxmtMxl';
        
            // $arrKakao = array(
            //     "gubun"=> $code
            //     , "admin"=> "N"
            //     , "smsTitle"=> $msgTitle
            //     , "userName"=> $userName
            //     , "tempName"=> "at_surf_step1"
            //     , "kakaoMsg"=>$kakaoMsg
            //     , "userPhone"=> $userPhone
            //     , "link1"=>"orderview?num=1&resNumber=".$ResNumber //예약조회/취소
            //     , "link2"=>"surflocation?seq=".$shopseq //위치안내
            //     , "link3"=>"event" //공지사항
            //     , "link4"=>""
            //     , "link5"=>""
            //     , "smsOnly"=>"N"
            // );
        }else{
            $msgTitle = "액트립 [$shopname] 예약안내";
            $kakaoMsg = $msgTitle.'\n안녕하세요. '.$userName.'님 예약이 완료되었습니다. \n\n'.$shopname.' 예약정보 [예약대기]\n ▶ 예약번호 : '.$ResNumber.'\n ▶ 예약자 : '.$userName.'\n ▶ 신청목록\n'.$surfshopMsg.$etcMsg.$totalPrice.'---------------------------------\n ▶ 안내사항\n      - 1시간 이내 미입금시 자동취소됩니다.\n\n ▶ 입금계좌\n      - 우리은행 / 1002-845-467316 / 이승철\n\n ▶ 문의\n      - http://pf.kakao.com/_HxmtMxl';

            $arrKakao = array(
                "gubun"=> "surf"
                , "admin"=> "N"
                , "smsTitle"=> $msgTitle
                , "userName"=> $userName
                , "tempName"=> "at_surf_step1"
                , "kakaoMsg"=>$kakaoMsg
                , "userPhone"=> $userPhone
                , "link1"=>"orderview?num=1&resNumber=".$ResNumber //예약조회/취소
                , "link2"=>"surflocation?seq=".$shopseq //위치안내
                , "link3"=>"event" //공지사항
                , "link4"=>""
                , "link5"=>""
                , "smsOnly"=>"N"
                , "PROD_NAME"=>$shopname
                , "PROD_URL"=>$shopseq
                , "PROD_TYPE"=>"surf_user"
                , "RES_CONFIRM"=>$res_confirm
            );

            $arrRtn = sendKakao($arrKakao); //알림톡 발송

             // 카카오 알림톡 DB 저장 START
            $select_query = kakaoDebug($arrKakao, $arrRtn);            
            $result_set = mysqli_query($conn, $select_query);
            if(!$result_set) goto errGoSurf;
            // 카카오 알림톡 DB 저장 END
        }

        //==================== 카카오톡 발송 End ====================

        if($coupon == "ATBLOG" || $coupon == "NAVERA"){
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
            $infomsg .= "\n      - 예약내역 확인 후 승인처리 부탁드립니다.\n      - 예약이 불가할 경우 취소 상태로 변경해주시면 됩니다.\n\n";

            $msgTitle = '액트립 ['.$userName.']님 예약안내';
            $kakaoMsg = $msgTitle.'\n안녕하세요. 액트립 '.$shopname.' 예약건 안내입니다.\n\n액트립 예약정보 [입금완료]\n ▶ 예약번호 : '.$ResNumber.'\n ▶ 예약자 : '.$userName.'\n'.$surfshopMsg.$etcMsg.'---------------------------------\n ▶ 안내사항'.$infomsg;

            $arrKakao = array(
                "gubun"=> $code
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
                , "PROD_URL"=>$shopseq
                , "PROD_TYPE"=>"surf_shop"
                , "RES_CONFIRM"=>$res_confirm
            );
            $arrRtn = sendKakao($arrKakao); //알림톡 발송

            // 카카오 알림톡 DB 저장 START
            $select_query = kakaoDebug($arrKakao, $arrRtn);            
            $result_set = mysqli_query($conn, $select_query);
            if(!$result_set) goto errGoSurf;
            // 카카오 알림톡 DB 저장 END
        }
        
        mysqli_query($conn, "COMMIT");
        //==================== 이메일 발송 Start ====================
        // 이메일 발송
        $to = "lud1@naver.com,ttenill@naver.com";
        if(strrpos($usermail, "@") > 0){
            $to .= ','.$usermail;
            //$to = $usermail;

            $info1_title = "신청목록";
            $info1 = str_replace('      -', '&nbsp;&nbsp;&nbsp;-', str_replace('\n', '<br>', $surfshopMsg));
            $info2_title = "";
            $info2 = "";
    
            $arrMail = array(
                "gubun"=> "surf"
                , "gubun_step" => $res_confirm
                , "gubun_title" => $shopname
                , "mailto"=> $to
                , "mailfrom"=> "surfshop_res@actrip.co.kr"
                , "mailname"=> "actrip"
                , "userName"=> $userName
                , "ResNumber"=> $ResNumber
                , "userPhone" => $userPhone
                , "etc" => $etc
                , "totalPrice1" => number_format($TotalPrice).'원'
                , "totalPrice2" => ""
                , "banknum" => "우리은행 / 1002-845-467316 / 이승철"
                , "info1_title"=> $info1_title
                , "info1"=> $info1
                , "info2_title"=> $info2_title
                , "info2"=> $info2
            );

            sendMail($arrMail); //메일 발송
        }
        //==================== 이메일 발송 End ====================
        
        echo '<script>alert("예약이 완료되었습니다.");parent.location.href="/orderview?num=2&resNumber='.$ResNumber.'";</script>';
        //echo '<script>alert("예약이 완료되었습니다.");parent.fnSaveErr("divConfirm");</script>';
	}
}

mysqli_close($conn);
?>
