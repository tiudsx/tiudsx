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
        $busTitleName = "양양"; 
        $resparam = "surfbus_yy";
    }else if($shopseq == 14){
        $busTitleName = "동해";    
        $resparam = "surfbus_dh";	
    }

	$SurfDateBusS = $_REQUEST["hidbusDateS"]; //출발 날짜
    $SurfDateBusE = $_REQUEST["hidbusDateE"]; //복귀 날짜
    
	$busGubunS = $_REQUEST["hidbusGubunS"]; //출발 버스노선
    $busGubunE = $_REQUEST["hidbusGubunE"]; //복귀 버스노선

	$busNumS = $_REQUEST["hidbusNumS"]; //출발 버스번호
    $busNumE = $_REQUEST["hidbusNumE"]; //복귀 버스번호
    
	$arrSeatS = $_REQUEST["hidbusSeatS"]; //출발 좌석번호
    $arrSeatE = $_REQUEST["hidbusSeatE"]; //복귀 좌석번호
    
	$startLocationS = $_REQUEST["startLocationS"]; //출발 정류장
	$endLocationS = $_REQUEST["endLocationS"]; //출발 도착 정류장
	$startLocationE = $_REQUEST["startLocationE"]; //복귀 정류장
	$endLocationE = $_REQUEST["endLocationE"]; //복귀 도착 정류장

	$userName = $_REQUEST["userName"];
	$userId = $_REQUEST["userId"];
	$userPhone = $_REQUEST["userPhone1"]."-".$_REQUEST["userPhone2"]."-".$_REQUEST["userPhone3"];
	$usermail = $_REQUEST["usermail"];
	$etc = $_REQUEST["etc"];
    
    $price_start = 0;
    $price_return = 0;
	for($i = 0; $i < count($SurfDateBusS); $i++){
        //출발 셔틀버스 가격
        if($i == 0){
            $select_query = "SELECT price FROM `AT_PROD_BUS_DAY`  WHERE shopseq = '.$shopseq.' AND bus_gubun = '".$busGubunS[$i]."' AND bus_num = ".$busNumS[$i]." AND bus_date = '".$SurfDateBusS[$i]."'";
            $result = mysqli_query($conn, $select_query);
            $rowMain = mysqli_fetch_array($result);
            
            $price_start = $rowMain["price"];
        }

        $select_query = 'SELECT res_spoint FROM AT_RES_SUB WHERE seq = '.$shopseq.' AND res_date = "'.$SurfDateBusS[$i].'" AND bus_gubun = "'.$busGubunS[$i].'" AND bus_num = "'.$busNumS[$i].'" AND res_seat = "'.$arrSeatS[$i].'" AND res_confirm IN (0, 1, 2, 3)';
        //echo $select_query;
        $result_setlist = mysqli_query($conn, $select_query);
		$count = mysqli_num_rows($result_setlist);

		if($count > 0){
			echo '<script>alert("['.$SurfDateBusS[$i].'] '.$arrSeatS[$i].'번 좌석은 이미 예약된 자리입니다.\n\n다른좌석을 선택해주세요.");parent.fnUnblock("#divConfirm");</script>';
			return;
		}
	}

	for($i = 0; $i < count($SurfDateBusE); $i++){
        //복귀 셔틀버스 가격
        if($i == 0){
            $select_query = "SELECT price FROM `AT_PROD_BUS_DAY`  WHERE shopseq = '.$shopseq.' AND bus_gubun = '".$busGubunE[$i]."' AND bus_num = ".$busNumE[$i]." AND bus_date = '".$SurfDateBusE[$i]."'";
            $result = mysqli_query($conn, $select_query);
            $rowMain = mysqli_fetch_array($result);
            
            $price_return = $rowMain["price"];
        }

		$select_query = 'SELECT res_spoint FROM AT_RES_SUB WHERE seq = '.$shopseq.' AND res_date = "'.$SurfDateBusE[$i].'" AND bus_gubun = "'.$busGubunE[$i].'" AND bus_num = "'.$busNumE[$i].'" AND res_seat = "'.$arrSeatE[$i].'" AND res_confirm IN (0, 1, 2, 3)';
		$result_setlist = mysqli_query($conn, $select_query);
		$count = mysqli_num_rows($result_setlist);

		if($count > 0){
			echo '<script>alert("['.$SurfDateBusE[$i].'] '.$arrSeatE[$i].'번 좌석은 이미 예약된 자리입니다.\n\n다른좌석을 선택해주세요.");parent.fnUnblock("#divConfirm");</script>';
			return;
		}
    }
    
	mysqli_query($conn, "SET AUTOCOMMIT=0");
	mysqli_query($conn, "BEGIN");

    
return;
	$busSeatInfoS = "";
	$busSeatInfoE = "";
	$busStopInfoS = "";
	$busStopInfoE = "";
	$arrSeatInfoS = array();
	$arrSeatInfoE = array();
	$arrStopInfoS = array();
	$arrStopInfoE = array();

    $day_start = "-";
    $day_return = "-";

    //($price_start * count($SurfDateBusS))
    //($price_return * count($SurfDateBusE))

    $res_Price = 20000;
    $res_totalprice = $res_Price;

    // 할인 쿠폰적용
    $coupon = $_REQUEST["couponcode"];
    $res_price_coupon = 0;
    $couponseq = 0;

    if($coupon != ""){
        $select_query = "SELECT b.dis_price, b.dis_type, b.couponseq, b.issue_type FROM AT_COUPON_CODE a INNER JOIN AT_COUPON b ON a.couponseq = b.couponseq WHERE a.coupon_code = '$coupon' AND a.use_yn = 'N' AND a.seq = 'BUS'";
        $result = mysqli_query($conn, $select_query);
        $rowMain = mysqli_fetch_array($result);
        $chkCnt = mysqli_num_rows($result); //체크 개수

        if($chkCnt > 0){
            $res_price_coupon = $rowMain["dis_price"];
            $dis_type = $rowMain["dis_type"];
            $issue_type = $rowMain["issue_type"];
            $couponseq = $rowMain["couponseq"];

            if($dis_type == "P"){ //퍼센트 할인
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

                $errCode = "01";
                if(!$result_set) goto errGo;
            }
        }
    }

    //예약채널 사이트 쿠폰 코드가 있으면 예약확정
    if($res_totalprice == 0)
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

    //출발 좌석예약
    for($i = 0; $i < count($SurfDateBusS); $i++){
        $TotalPrice += $res_totalprice;
        $select_query = "INSERT INTO `AT_RES_SUB` (`resnum`, `code`, `seq`, `optseq`, `shopname`, `sub_title`, `optname`, `optsubname`, `res_date`, `res_time`, `res_bus`, `res_busnum`, `res_seat`, `res_spoint`, `res_spointname`, `res_epoint`, `res_epointname`, `res_confirm`, `res_price`, `res_price_coupon`, `res_coupon`, `res_totalprice`, `res_ea`, `res_m`, `res_w`, `rtn_charge_yn`, `rtn_chargeprice`, `rtn_totalprice`, `rtn_bankinfo`, `cashreceipt_yn`, `insuserid`, `insdate`, `upduserid`, `upddate`)  VALUES ('$ResNumber', 'bus', $shopseq, null, '".$busTitleName." 서핑버스', null, null, null, '$SurfDateBusS[$i]', null, '$busNumS[$i]', '$busNumS[$i]', '$arrSeatS[$i]', '$startLocationS[$i]', '$startLocationS[$i]', '$endLocationS[$i]', '$endLocationS[$i]', $res_confirm, $res_Price, $res_price_coupon, '$coupon', $res_totalprice, 1, 0, 0, 'Y', 0, 0, null, 'N', '$InsUserID', '$datetime', '$InsUserID', '$datetime');";
        $result_set = mysqli_query($conn, $select_query);
        //echo $select_query.'<br>';

        $errCode = "02_" + $i;
        if(!$result_set) goto errGo;
    
        //출발일 정보
        if($day_start == "-"){
            $arrBus = fnBusNum2023($busNumE[$i]);
            $day_start = '['.$SurfDateBusE[$i].'] '.$arrBus["point"].' '.$arrBus["num"];
        }
        
        if($msgType == 2){ //입금대기
            $pointTime = ' -> '.$endLocationS[$i];
        }else{
            $pointTime = ' / '.explode("|", fnBusPoint($startLocationS[$i], $busNumS[$i]))[0];
        }

        if(array_key_exists($SurfDateBusS[$i].$busNumS[$i], $arrSeatInfoS)){
            $arrSeatInfoS[$SurfDateBusS[$i].$busNumS[$i]] .= '      - '.$arrSeatS[$i].'번 ('.$startLocationS[$i].$pointTime.')\n';
        }else{
            // $weekday = fnWeek($SurfDateBusS[$i]); //요일정보

            $arrSeatInfoS[$SurfDateBusS[$i].$busNumS[$i]] = '    ['.$SurfDateBusS[$i].'] '.fnBusNum($busNumS[$i]).'\n      - '.$arrSeatS[$i].'번 ('.$startLocationS[$i].$pointTime.')\n';
        }

        $arrData = explode("|", fnBusPoint($startLocationS[$i], $busNumS[$i]));
        $arrStopInfoS[$startLocationS[$i]] = '    ['.$startLocationS[$i].'] '.$arrData[0].'\n      - '.$arrData[1].'\n';
    }
    
    //복귀 좌석예약
    for($i = 0; $i < count($SurfDateBusE); $i++){
        $TotalPrice += $res_totalprice;
        $select_query = "INSERT INTO `AT_RES_SUB` (`resnum`, `code`, `seq`, `optseq`, `shopname`, `sub_title`, `optname`, `optsubname`, `res_date`, `res_time`, `res_bus`, `res_busnum`, `res_seat`, `res_spoint`, `res_spointname`, `res_epoint`, `res_epointname`, `res_confirm`, `res_price`, `res_price_coupon`, `res_coupon`, `res_totalprice`, `res_ea`, `res_m`, `res_w`, `rtn_charge_yn`, `rtn_chargeprice`, `rtn_totalprice`, `rtn_bankinfo`, `cashreceipt_yn`, `insuserid`, `insdate`, `upduserid`, `upddate`)  VALUES ('$ResNumber', 'bus', $shopseq, null, '".$busTitleName." 서핑버스', null, null, null, '$SurfDateBusE[$i]', null, '$busNumE[$i]', '$busNumE[$i]', '$arrSeatE[$i]', '$startLocationE[$i]', '$startLocationE[$i]', '$endLocationE[$i]', '$endLocationE[$i]', $res_confirm, $res_Price, $res_price_coupon, '$coupon', $res_totalprice, 1, 0, 0, 'Y', 0, 0, null, 'N', '$InsUserID', '$datetime', '$InsUserID', '$datetime');";
        $result_set = mysqli_query($conn, $select_query);
        //echo $select_query.'<br>';
        $errCode = "03_" + $i;
        if(!$result_set) goto errGo;

        //복귀일 정보
        if($day_return == "-"){
            $arrBus = fnBusNum2023($busNumE[$i]);
            $day_return = '['.$SurfDateBusE[$i].'] '.$arrBus["point"].' '.$arrBus["num"];
        }

        
        if($msgType == 2){ //입금대기
            $pointTime = ' -> '.$endLocationE[$i];
        }else{
            $pointTime = ' / '.explode("|", fnBusPoint($startLocationE[$i], $busNumE[$i]))[0];
        }

        if(array_key_exists($SurfDateBusE[$i].$busNumE[$i], $arrSeatInfoE)){
            $arrSeatInfoE[$SurfDateBusE[$i].$busNumE[$i]] .= '      - '.$arrSeatE[$i].'번 ('.$startLocationE[$i].$pointTime.')\n';
        }else{
            // $weekday = fnWeek($SurfDateBusE[$i]); //요일정보

            $arrSeatInfoE[$SurfDateBusE[$i].$busNumE[$i]] = '    ['.$SurfDateBusE[$i].'] '.fnBusNum($busNumE[$i]).'\n      - '.$arrSeatE[$i].'번 ('.$startLocationE[$i].$pointTime.')\n';
        }

        $arrData = explode("|", fnBusPoint($startLocationE[$i], $busNumE[$i]));
        $arrStopInfoE[$startLocationE[$i]] = '    ['.$startLocationE[$i].'] '.$arrData[0].'\n      - '.$arrData[1].'\n';
    }

    $select_query = "INSERT INTO `AT_RES_MAIN` (`resnum`, `pay_type`, `pay_info`, `user_id`, `user_name`, `user_tel`, `user_email`, `etc`, `insuserid`, `insdate`) VALUES ('$ResNumber', 'B', '무통장입금', '$InsUserID', '$userName', '$userPhone', '$usermail', '$etc', '$InsUserID', '$datetime');";
    //echo $select_query.'<br>';
    $result_set = mysqli_query($conn, $select_query);
    $errCode = "04";
    if(!$result_set) goto errGo;

    $select_query = "UPDATE AT_PROD_MAIN SET sell_cnt = sell_cnt + 1 WHERE seq = $shopseq;";
    $result_set = mysqli_query($conn, $select_query);
    //echo $select_query.'<br>';
    $errCode = "05";
    if(!$result_set) goto errGo;

	if(!$success){
        errGo:
		mysqli_query($conn, "ROLLBACK");
        echo '<script>alert("예약진행 중 오류가 발생하였습니다.\n\n['.$errCode.'] 관리자에게 문의해주세요.");</script>';
	}else{
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

        $totalPrice = "\n ▶ 총 결제금액 : ".number_format($TotalPrice)."원\n";
        
        if($msgType == 2){ //입금대기
            $tempName = "frip_bus03"; //입금대기
            $btn_ResSearch = "orderview?num=1&resNumber=".$ResNumber; //예약조회/취소
            $btn_ResChange = "pointchange?num=1&resNumber=".$ResNumber; //예약조회/취소
            $btn_ResGPS = "";
            $btn_ResPoint = "pointlist?num=1&resNumber=".$ResNumber; //탑승시간/위치안내
            $btn_Notice = "";
            $btn_ResContent = ""; //예약 상세안내

            $msgInfo = $busSeatInfoTotal.$totalPrice;
        }else{ //예약확정
            $tempName = "frip_bus02"; //예약확정
            $btn_ResSearch = "orderview?num=1&resNumber=".$ResNumber; //예약조회
            $btn_ResChange = "pointchange?num=1&resNumber=".$ResNumber; //좌석/정류장 변경
            $btn_ResGPS = "surfbusgps"; //서핑버스 실시간위치 조회
            $btn_ResPoint = "pointlist?num=1&resNumber=".$ResNumber; //탑승시간/위치안내
            $btn_Notice = "";
            $btn_ResContent = ""; //예약 상세안내

            $msgInfo = $busSeatInfoTotal;
        }
                
        if($day_start != "-" && $day_start != "-"){ //왕복
            $bus_line = "서울 ↔ $busTitleName";
        }else if($day_start != "-"){ //서울 출발
            $bus_line = "서울 → $busTitleName";
        }else{ //서울 복귀
            $bus_line = "$busTitleName → 서울";
        }
        //==========================카카오 메시지 발송 ==========================
        $msgTitle = '액트립 셔틀버스 입금안내';
        $DebugInfo = array(
            "PROD_NAME" => "셔틀버스 입금대기"
            , "PROD_TABLE" => "AT_RES_MAIN"
            , "PROD_TYPE" => "bus_stay"
            , "RES_CONFIRM" => "-1"
            , "resnum" => $ResNumber
        );
        $arrKakao = array(
            "gubun"=> "bus_stay"
            , "userName"=> $userName
            , "userPhone"=> $userPhone
            , "userPrice"=> number_format($TotalPrice).'원'
            , "bus_line"=> $bus_line
            , "day_start"=> $day_start
            , "day_return"=> $day_return
            , "DebugInfo"=> $DebugInfo
        );	

        $arryKakao[0] = $arrKakao;
    
        $arrKakao = array(
            "arryData"=> $arryKakao
            , "array"=> "true" //배열 여부
            , "tempName"=> "actrip_info01" //템플릿 코드
            , "title"=> $msgTitle //타이틀
            , "smsOnly"=> "N" //문자발송 여부
        );

        //서핑버스 네이버예약 : 7, 네이버쇼핑 : 10, 프립 : 11, 프립 패키지 : 17, 마이리얼트립 : 12 알림톡 제외
        if($msgType > 0){
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
        
                // $resseq = $arryKakao[$i]["DebugInfo"]["resseq"];
                // $select_query = "UPDATE `AT_SOL_RES_MAIN` SET res_kakaoinfo = 'Y', res_kakao = res_kakao + 1, userinfo = '".$msgid."' WHERE resseq = $seq";
                // $result_set = mysqli_query($conn, $select_query);
        
                $errmsg = $select_query;
                
                $errCode = "06";
                if(!$result_set) goto errGo;
            }
        }

		mysqli_query($conn, "COMMIT");

        // 이메일 발송
		//$to = "lud1@naver.com";
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
            ///sendMail($arrMail); //메일 발송
        }
        
        //echo '<script>alert("'.$busTitleName.' 서핑버스 예약이 완료되었습니다.");parent.location.href="/orderview?num=2&resNumber='.$ResNumber.'";</script>';
	}
}

mysqli_close($conn);
?>
