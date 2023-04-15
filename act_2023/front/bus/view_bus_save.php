<?php
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/func.php';
include __DIR__.'/../../common/kakaoalim.php';

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

    if($userPhone == "010-4411-93901"){
        //echo '<script>alert("지속적인 미입금 예약으로 인해 예약진행이 불가능합니다.");parent.fnUnblock("#divConfirm");</script>';
        //return;
    }
    
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
     //7:서핑버스 네이버쇼핑, 10:네이버예약, 11:프립, 17:프립 패키지, 12:마이리얼트립, 14:망고서프패키지, 15:서프엑스
    $coupon_array = array("NAVER12");
    if(in_array($coupon, $coupon_array) || in_array($couponseq, array(7, 10, 11, 17, 12, 14, 15)))
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
            $weekday = fnWeek($SurfDateBusY[$i]);

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
            $weekday = fnWeek($SurfDateBusS[$i]);

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
            $busSeatInfoTotal .= " ▶ ".$busSeatInfoS;
        }
        if($busSeatInfoE != ""){
            if($busSeatInfoS != ""){
                $busSeatInfoTotal .= "\n";
            }
            $busSeatInfoTotal .= " ▶ ".$busSeatInfoE;
        }
        $busSeatInfoTotal .= '\n ▶ 탑승시간/위치 안내\n      - https://actrip.co.kr/pointlist\n';

        $totalPrice = " ▶ 총 결제금액 : ".number_format($TotalPrice)."원\n";
        
        //신규 로직 : 2022-01-04
        $gubun_title = $busTitleName.'서핑버스';
        
        if($coupon == "JOABUS"){
            //$gubun_title = "조아서프 패키지 서핑버스";
            //$msgChannelName2 = '\n      - 예약취소는 예약하셨던 조아서프에 문의해주세요.';
        }else if($coupon == "FRIP" || $couponseq == 11 || $couponseq == 17){
            //$gubun_title = "프립 서핑버스";
        }else if($coupon == "MYTRIP"){
            //$gubun_title = "마이리얼트립 서핑버스";
        }else if($coupon == "KLOOK"){
            //$gubun_title = "클룩 서핑버스";
        }else if($coupon == "SURFX" || $couponseq == 15){
            //$gubun_title = "SURFX 서핑버스";
        }else if($coupon == "NAVER" || $couponseq == 9 || $couponseq == 10){
            
        }else{

        }
        
        //$msgTitle = '액트립'.$msgChannelName.' 서핑버스 예약안내';
        $msgTitle = '액트립 서핑버스 예약안내';

        if($msgType == 2){ //입금대기
            $kakaoMsg = $msgTitle.'\n\n안녕하세요. '.$userName.'님\n서핑버스를 예약해주셔서 감사합니다.\n\n예약정보 [입금대기]\n ▶ 예약번호 : '.$ResNumber.'\n ▶ 예약자 : '.$userName.'\n'.$busSeatInfoTotal.$etcMsg.$totalPrice.'---------------------------------\n ▶ 안내사항\n      - 1시간 이내 미입금시 자동취소됩니다.\n\n ▶ 입금계좌\n      - 우리은행 / 1002-845-467316 / 이승철\n\n';

            $tempName = "frip_bus03"; //입금대기
            $btn_ResSearch = "orderview?num=1&resNumber=".$ResNumber; //예약조회/취소
            $btn_ResChange = "pointchange?num=1&resNumber=".$ResNumber; //예약조회/취소
            $btn_ResGPS = "";
            $btn_ResPoint = "pointlist?num=1&resNumber=".$ResNumber; //탑승시간/위치안내
            $btn_Notice = "";
            $btn_ResContent = ""; //예약 상세안내
        }else{ //예약확정
            $kakaoMsg = $msgTitle.'\n\n안녕하세요. '.$userName.'님\n서핑버스를 예약해주셔서 감사합니다.\n\n예약정보 [예약확정]\n ▶ 예약번호 : '.$ResNumber.'\n ▶ 예약자 : '.$userName.'\n'.$busSeatInfoTotal.$etcMsg.'---------------------------------\n ▶ 안내사항\n      - 교통상황으로 인해 정류장에 지연 도착할 수 있으니 양해부탁드립니다.'.$msgChannelName2.'\n      - 이용일, 탑승시간, 탑승위치 꼭 확인 부탁드립니다.\n      - 탑승시간 5분전에는 도착해주세요~\n\n ▶ 문의\n      - 010.3308.6080';

            $tempName = "frip_bus02"; //예약확정
            $btn_ResSearch = "orderview?num=1&resNumber=".$ResNumber; //예약조회
            $btn_ResChange = "pointchange?num=1&resNumber=".$ResNumber; //좌석/정류장 변경
            $btn_ResGPS = "surfbusgps"; //서핑버스 실시간위치 조회
            $btn_ResPoint = "pointlist?num=1&resNumber=".$ResNumber; //탑승시간/위치안내
            $btn_Notice = "";
            $btn_ResContent = ""; //예약 상세안내
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
            , "btn_ResPoint"=> $btn_ResPoint
            , "btn_Notice"=> $btn_Notice
            , "smsOnly"=>"N"
            , "PROD_NAME"=>"서핑버스"
            , "PROD_URL"=>$shopseq
            , "PROD_TYPE"=>"bus"
            , "RES_CONFIRM"=>$res_confirm
        );
        
        //서핑버스 네이버예약 : 7, 네이버쇼핑 : 10, 프립 : 11, 프립 패키지 : 17, 마이리얼트립 : 12 알림톡 제외
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
}

mysqli_close($conn);
?>
