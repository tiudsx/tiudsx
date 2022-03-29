<?php 
include __DIR__.'/../db.php';
include __DIR__.'/../surf/surfkakao.php';
include __DIR__.'/../surf/surfmail.php';
include __DIR__.'/../surf/surffunc.php';

$param = $_REQUEST["resparam"];
$gubun = $_REQUEST["gubun"];
$num = $_REQUEST["num"];
$to = "lud1@naver.com,ttenill@naver.com";

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
if($param == "RtnPrice"){
	$ressubseq = str_replace("'", "",$_REQUEST["subintseq"]);
	$arrSeq = explode(",",$ressubseq);

	$now = date("Y-m-d");
	$totalPrice = 0;
	$totalFee = 0;
	$totalOpt = 0;
	for($i=0;$i<count($arrSeq);$i++) {
        $select_query_sub = 'SELECT *, TIMESTAMPDIFF(MINUTE, confirmdate, now()) as timeM FROM AT_RES_SUB where ressubseq IN ('.$arrSeq[$i].')';
		$resultSite = mysqli_query($conn, $select_query_sub);
		$count = mysqli_num_rows($resultSite);

		if($count == 0){
			echo $arrSeq[i];
			exit;
		}

		while ($rowSub = mysqli_fetch_assoc($resultSite)){
			$arrOpt = 0;
			$boolConfirm = false;
            $ResConfirm = $rowSub['res_confirm'];
            $ResPrice = $rowSub['res_totalprice'];
            $rtn_charge_yn = $rowSub['rtn_charge_yn'];

            if(!($ResConfirm == "0" || $ResConfirm == "1" || $ResConfirm == "2" || $ResConfirm == "3" || $ResConfirm == "6" || $ResConfirm == "8")){
                echo 'err';
                exit;
            }
            $sDate = $rowSub['res_date'];
            
            if($ResConfirm == "1" || $ResConfirm == "2" || $ResConfirm == "3" || $ResConfirm == "6" || $ResConfirm == "8"){
                $boolConfirm = true;
            }
            
            $rtnFee = cancelPrice($sDate, $rowSub['timeM'], $ResConfirm, $ResPrice, $rtn_charge_yn);

			if($boolConfirm){
                $totalPrice += $ResPrice;
                $totalFee += $rtnFee;
				$totalOpt += $arrOpt;
			}
		}

		$totalPrice = $totalPrice + $totalOpt;
	}

	echo $totalPrice."|".$totalFee."|".($totalPrice - $totalFee);

}else if($param == "Cancel"){  //환불 및 취소
	$chkCancel = $_REQUEST["chkCancel"];
	$bankName = $_REQUEST["bankName"];
	$bankNum = $_REQUEST["bankNum"];
	$MainNumber = $_REQUEST["MainNumber"];

    for($i = 0; $i < count($chkCancel); $i++){
        $ressubseq .= $chkCancel[$i].",";
    }
    $ressubseq .= '0';
    $select_query = 'SELECT * FROM AT_RES_MAIN WHERE resnum = '.$MainNumber;

    $result_setlist = mysqli_query($conn, $select_query);
    $row = mysqli_fetch_array($result_setlist);

    $ResNumber = $row["resnum"];
    $userName = $row["user_name"];
    $InsUserID = $userName;
    $userPhone = $row["user_tel"];
    $user_email = $row["user_email"];
    $etc = $row["etc"];

    $FullBankText = "";
    if($bankNum != ""){
        $FullBankText = $bankName."|".$bankNum."|".$userName;
    }

    $arrSeatInfo = array();
    $select_query_sub = 'SELECT *, TIMESTAMPDIFF(MINUTE, confirmdate, now()) as timeM FROM AT_RES_SUB where res_confirm IN (0,1,2,3,6) AND ressubseq IN ('.$ressubseq.') AND resnum = '.$ResNumber;
    $resultSite = mysqli_query($conn, $select_query_sub);
    $chkSubCnt = mysqli_num_rows($resultSite); //체크 개수
    if($chkSubCnt == 0){
        // echo '<script>alert("환불신청 가능한 예약내역이 없습니다.\n\n관리자에게 문의해주세요.");</script>';
        echo 'err';
        exit;
    }

    mysqli_query($conn, "SET AUTOCOMMIT=0");
    mysqli_query($conn, "BEGIN");

    $TotalPrice = 0;
    $TotalFee = 0;
    $TotalOpt = 0;
    $arrSeatInfo = array();
    while ($rowSub = mysqli_fetch_assoc($resultSite)){
        if($success){
            $arrOpt = 0;
            $boolConfirm = false;
            
            $sDate = $rowSub['res_date'];
            $ResConfirm = $rowSub['res_confirm'];
            $ResPrice = $rowSub['res_totalprice'];
            $shopname = $rowSub['shopname'];
            $shopSeq = $rowSub['seq']; //입점샵 seq
            $code = $rowSub['code'];
            $rtn_charge_yn = $rowSub['rtn_charge_yn'];


            if($ResConfirm == "2" || $ResConfirm == "3" || $ResConfirm == "6" || $ResConfirm == "8"){
                $boolConfirm = true;
            }

            if($ResConfirm == "0"){ //미입금 상태 취소
                $select_query = "UPDATE AT_RES_SUB 
                                SET res_confirm = 7
                                ,upddate = now()
                                ,upduserid = '".$InsUserID."'
                            WHERE ressubseq = ".$rowSub['ressubseq'].";";
                $result_set = mysqli_query($conn, $select_query);
                if(!$result_set) $success = false;
            }else if($boolConfirm){ //확정 상태 환불요청
                $rtnFee = cancelPrice($sDate, $rowSub['timeM'], $ResConfirm, $ResPrice, $rtn_charge_yn);                

                $select_query = "UPDATE AT_RES_SUB  
                                SET res_confirm = 4
                                ,rtn_chargeprice = ".$rtnFee."
                                ,rtn_totalprice = ".(($ResPrice + $arrOpt) - $rtnFee)."
                                ,rtn_bankinfo = '".$FullBankText."'
                                ,upddate = now()
                                ,upduserid = '".$InsUserID."'
                            WHERE ressubseq = ".$rowSub['ressubseq'].";";

                $result_set = mysqli_query($conn, $select_query);
                if(!$result_set) $success = false;
                
                $ressubseqInfo .= $rowSub['ressubseq'].",";

                $TotalPrice +=($ResPrice + $arrOpt);
                $TotalFee +=$rtnFee;

                if($code == "bus"){
                    if(array_key_exists($rowSub['res_date'].$rowSub['res_bus'], $arrSeatInfo)){
                        $arrSeatInfo[$rowSub['res_date'].$rowSub['res_bus']] .= '      - '.$rowSub['res_seat'].'번\n';
                    }else{
                        $arrSeatInfo[$rowSub['res_date'].$rowSub['res_bus']] = '    ['.$rowSub['res_date'].'] '.fnBusNum($rowSub['res_bus']).'\n      - '.$rowSub['res_seat'].'번\n';
                    }
                }else{
                    $ResNum = "      - 인원 : ";
                    if($rowSub["res_m"] > 0){
                        $ResNum .= "남:".$rowSub["res_m"].'명';
                    }
                    if($row['res_m'] > 0 && $row['res_w'] > 0){
                        $ResNum .= ",";
                    }
                    if($rowSub["res_w"] > 0){
                        $ResNum .= "여:".$rowSub["res_w"].'명';
                    }
                    $ResNum .= '\n';

                    $optname = $rowSub["optname"];
                    $surfMsg .= '    ['.$optname.']\n      - 예약일 : '.$sDate.'\n'.$ResNum.'\n';
                }
            }else{
                $success = false;
            }
        }
    }

    if(!$success){
        mysqli_query($conn, "ROLLBACK");
        //echo '<script>alert("환불신청 중 오류가 발생하였습니다.\n\n관리자에게 문의해주세요.");</script>';
        echo 'err';
    }else{
        $rtnText = ' ▶ 환불요청 안내\n       - 결제금액 : '.number_format($TotalPrice).'원\n       - 환불수수료 : '.number_format($TotalFee).'원\n       - 환불금액 : '.number_format($TotalPrice-$TotalFee).'원\n  ▶환불계좌\n       - '.str_replace('|', ' / ', $FullBankText).'\n';

        if($ressubseqInfo != ""){
            if($code == "bus"){
                // 예약좌석 정보
                foreach($arrSeatInfo as $x) {
                    $msgInfo .= $x;
                }

                $msgInfo = " ▶ 좌석안내\n".$msgInfo;
                $mailmsgInfo = $msgInfo;
                $subtitlename = '액트립';
            }else{
                $msgInfo = " ▶ 신청목록\n".$surfMsg;
                $mailmsgInfo = $surfMsg;
                $subtitlename = $shopname;
            }

            $msgTitle = '액트립 '.$shopname.' 환불안내';
            $kakaoMsg = $msgTitle.'\n안녕하세요. '.$userName.'님\n\n'.$subtitlename.' 예약정보 [환불요청]\n ▶ 예약번호 : '.$ResNumber.'\n ▶ 예약자 : '.$userName.'\n'.$msgInfo.$rtnText.'---------------------------------\n ▶ 안내사항\n      - 환불처리기간은 1~7일정도 소요됩니다.\n\n ▶ 문의\n      - http://pf.kakao.com/_HxmtMxl';

            $arrKakao = array(
                "gubun"=> $code
                , "admin"=> "N"
                , "smsTitle"=> $msgTitle
                , "userName"=> $userName
                , "tempName"=> "at_res_step3"
                , "kakaoMsg"=>$kakaoMsg
                , "userPhone"=> $userPhone
                , "link1"=>"event" //공지사항
                , "link2"=>""
                , "link3"=>""
                , "link4"=>""
                , "link5"=>""
                , "smsOnly"=>"N"
                , "PROD_NAME"=>"취소/환불요청"
                , "PROD_URL"=>$shopseq
                , "PROD_TYPE"=>$code
                , "RES_CONFIRM"=>"4"
            );
            $arrRtn = sendKakao($arrKakao); //알림톡 발송

            // 카카오 알림톡 DB 저장 START
            $select_query = kakaoDebug($arrKakao, $arrRtn);
            $result_set = mysqli_query($conn, $select_query);
            // 카카오 알림톡 DB 저장 END

            // 이메일 발송
            if(strrpos($user_email, "@") > 0){
                $to .= ','.$usermail;
            }

            if($code == "bus"){
                $info1_title = "좌석안내";
                $mailform = "surfbus_return@actrip.co.kr";
            }else{
                $info1_title = "신청목록";
                $mailform = "surfshop_return@actrip.co.kr";
            }
            $info1 = str_replace('      -', '&nbsp;&nbsp;&nbsp;-', str_replace('\n', '<br>', $mailmsgInfo));
            $info2_title = "";
            $info2 = "";

            $arrMail = array(
                "gubun"=> $code
                , "gubun_step" => 4
                , "gubun_title" => $shopname
                , "mailto"=> $to
                , "mailfrom"=> $mailform
                , "mailname"=> "actrip"
                , "userName"=> $userName
                , "ResNumber"=> $ResNumber
                , "userPhone" => $userPhone
                , "etc" => $etc
                , "totalPrice1" => number_format($TotalPrice-$TotalFee)."원"
                , "totalPrice2" => "(결제금액 ".number_format($TotalPrice)."원 - 환불수수료 ".number_format($TotalFee)."원)"
                , "banknum" => str_replace('|', ' / ', $FullBankText)
                , "info1_title"=> $info1_title
                , "info1"=> $info1
                , "info2_title"=> $info2_title
                , "info2"=> $info2
            );
            
            sendMail($arrMail); //메일 발송
            
            if($code != "bus"){
                //카카오톡 업체 발송
                $select_query = 'SELECT * FROM AT_PROD_MAIN WHERE seq = '.$shopSeq;
                $result_setlist = mysqli_query($conn, $select_query);
                $rowshop = mysqli_fetch_array($result_setlist);

                $admin_tel = $rowshop["tel_kakao"];
                // $admin_tel = "010-4437-0009";

                $msgTitle = '액트립 ['.$userName.']님 예약취소';
                $kakaoMsg = $msgTitle.'\n안녕하세요. 액트립 예약취소건 안내입니다.\n\n액트립 예약정보 [예약취소]\n ▶ 예약번호 : '.$ResNumber.'\n ▶ 예약자 : '.$userName.'\n'.$msgInfo.'---------------------------------\n ▶ 안내사항\n      - 예약취소내역 확인부탁드립니다.\n\n';

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
                    , "PROD_NAME"=>"환불요청_업체"
                    , "PROD_URL"=>$shopseq
                    , "PROD_TYPE"=>"surf_shop"
                    , "RES_CONFIRM"=>"4"
                );
                $arrRtn = sendKakao($arrKakao); //알림톡 발송
    
                // 카카오 알림톡 DB 저장 START
                $select_query = kakaoDebug($arrKakao, $arrRtn);
                $result_set = mysqli_query($conn, $select_query);
                // 카카오 알림톡 DB 저장 END
            }
        }
        
        mysqli_query($conn, "COMMIT");

        //echo '<script>alert("환불신청이 완료되었습니다.");parent.location.href="/";</script>';
        echo '0';
    }
}else if($param == "PointChange"){ //정류장 변경
    $ResNumber = $_REQUEST["MainNumber"];
    $shopseq = $_REQUEST["shopseq"];
    $ressubseqs = $_REQUEST["ressubseqs"]; //셔틀버스 예약 시퀀스
    $ressubseqe = $_REQUEST["ressubseqe"]; //셔틀버스 예약 시퀀스
    $daytype = $_REQUEST["daytype"]; //편도 : 0, 왕복 : 1
    
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

    if(count($ressubseqs) != count($SurfDateBusY)){
        echo '<script>alert("예약된 좌석수('.count($ressubseqs).'자리)와 동일한 개수로 선택해주세요~");parent.fnUnblock("#divConfirm");</script>';
        return;
    }

    if(count($ressubseqe) != count($SurfDateBusS)){
        echo '<script>alert("예약된 좌석수('.count($ressubseqe).'자리)와 동일한 개수로 선택해주세요~");parent.fnUnblock("#divConfirm");</script>';
        return;
    }
    
    for($i = 0; $i < count($SurfDateBusY); $i++){
        $select_query = 'SELECT res_spoint FROM AT_RES_SUB where res_date = "'.$SurfDateBusY[$i].'" AND res_bus = "'.$busNumY[$i].'" AND res_seat = "'.$arrSeatY[$i].'" AND res_confirm IN (0, 1, 2, 3) AND resnum != '.$ResNumber;
        //echo $select_query;
        $result_setlist = mysqli_query($conn, $select_query);
		$count = mysqli_num_rows($result_setlist);

		if($count > 0){
			echo '<script>alert("['.$SurfDateBusY[$i].'] '.$arrSeatY[$i].'번 좌석은 이미 예약된 자리입니다.\n\n다른좌석을 선택해주세요.");parent.fnUnblock("#divConfirm");</script>';
			return;
		}
	}

	for($i = 0; $i < count($SurfDateBusS); $i++){
		$select_query = 'SELECT res_spoint FROM AT_RES_SUB where res_date = "'.$SurfDateBusS[$i].'" AND res_bus = "'.$busNumS[$i].'" AND res_seat = "'.$arrSeatS[$i].'" AND res_confirm IN (0, 1, 2, 3) AND resnum != '.$ResNumber;
		$result_setlist = mysqli_query($conn, $select_query);
		$count = mysqli_num_rows($result_setlist);

		if($count > 0){
			echo '<script>alert("['.$SurfDateBusS[$i].'] '.$arrSeatS[$i].'번 좌석은 이미 예약된 자리입니다.\n\n다른좌석을 선택해주세요.");parent.fnUnblock("#divConfirm");</script>';
			return;
		}
    }

	mysqli_query($conn, "SET AUTOCOMMIT=0");
    mysqli_query($conn, "BEGIN");

	$busSeatInfo = "";
	$busStopInfo = "";
	$arrSeatInfo = array();
    $arrStopInfo = array();
    
    //양양행 좌석예약
    for($i = 0; $i < count($SurfDateBusY); $i++){
        $select_query = "UPDATE AT_RES_SUB SET 
                            res_seat = '$arrSeatY[$i]', 
                            res_bus = '$busNumY[$i]', 
                            res_busnum = '$busNumY[$i]', 
                            res_spoint = '$startLocationY[$i]', 
                            res_spointname = '$startLocationY[$i]', 
                            res_epoint = '$endLocationY[$i]', 
                            res_epointname = '$endLocationY[$i]', 
                            upddate = now()
                                WHERE ressubseq = ".$ressubseqs[$i];
        $result_set = mysqli_query($conn, $select_query);
        if(!$result_set) goto errGo;
    
        if(array_key_exists($SurfDateBusY[$i].$busNumY[$i], $arrSeatInfo)){
            $arrSeatInfo[$SurfDateBusY[$i].$busNumY[$i]] .= '      - '.$arrSeatY[$i].'번 ('.$startLocationY[$i].' -> '.$endLocationY[$i].')\n';
        }else{
            $arrSeatInfo[$SurfDateBusY[$i].$busNumY[$i]] = '    ['.$SurfDateBusY[$i].'] '.fnBusNum($busNumY[$i]).'\n      - '.$arrSeatY[$i].'번 ('.$startLocationY[$i].' -> '.$endLocationY[$i].')\n';
        }

        $arrData = explode("|", fnBusPoint($startLocationY[$i], $busNumY[$i]));
        $arrStopInfo[$startLocationY[$i]] = '    ['.$startLocationY[$i].'] '.$arrData[0].'\n      - '.$arrData[1].'\n';
    }
    
    //서울행 좌석예약
    for($i = 0; $i < count($SurfDateBusS); $i++){
        $select_query = "UPDATE AT_RES_SUB SET 
                            res_seat = '$arrSeatS[$i]', 
                            res_bus = '$busNumS[$i]', 
                            res_busnum = '$busNumS[$i]', 
                            res_spoint = '$startLocationS[$i]', 
                            res_spointname = '$startLocationS[$i]', 
                            res_epoint = '$endLocationS[$i]', 
                            res_epointname = '$endLocationS[$i]', 
                            upddate = now()
                                WHERE ressubseq = ".$ressubseqe[$i];
        $result_set = mysqli_query($conn, $select_query);
        if(!$result_set) goto errGo;

        if(array_key_exists($SurfDateBusS[$i].$busNumS[$i], $arrSeatInfo)){
            $arrSeatInfo[$SurfDateBusS[$i].$busNumS[$i]] .= '      - '.$arrSeatS[$i].'번 ('.$startLocationS[$i].' -> '.$endLocationS[$i].')\n';
        }else{
            $arrSeatInfo[$SurfDateBusS[$i].$busNumS[$i]] = '    ['.$SurfDateBusS[$i].'] '.fnBusNum($busNumS[$i]).'\n      - '.$arrSeatS[$i].'번 ('.$startLocationS[$i].' -> '.$endLocationS[$i].')\n';
        }

        $arrData = explode("|", fnBusPoint($startLocationS[$i], $busNumS[$i]));
        $arrStopInfo[$startLocationS[$i]] = '    ['.$startLocationS[$i].'] '.$arrData[0].'\n      - '.$arrData[1].'\n';
    }
    
	if(!$success){
        errGo:
		mysqli_query($conn, "ROLLBACK");
		echo '<script>alert("좌석/정류장 수정 중 오류가 발생하였습니다.\n\n관리자에게 문의해주세요.");parent.fnUnblock("#divConfirm");</script>';
	}else{
            
        // 예약좌석 정보
        foreach($arrSeatInfo as $bus) {
            $busSeatInfo .= $bus;
        }
        
        // 정류장 정보
        foreach($arrStopInfo as $x) {
            $busStopInfo .= $x;
        }

        $resList =' ▶ 좌석안내\n'.$busSeatInfo;
        $info1_title = "좌석안내";
        $info1 = str_replace('      -', '&nbsp;&nbsp;&nbsp;-', str_replace('\n', '<br>', $busSeatInfo));

        $select_query = "SELECT a.user_name, a.user_tel, a.etc, a.user_email, b.* 
                            FROM AT_RES_MAIN as a INNER JOIN AT_RES_SUB as b 
                                ON a.resnum = b.resnum 
                            WHERE a.resnum = $ResNumber
                                AND b.res_confirm IN (0,3,8)
                            ORDER BY b.ressubseq";
        $result_setlist = mysqli_query($conn, $select_query);
        $count = mysqli_num_rows($result_setlist);

        while ($rowTime = mysqli_fetch_assoc($result_setlist)){
            $userName = $rowTime['user_name'];
            $userPhone = $rowTime['user_tel'];
            $user_email = $rowTime['user_email'];
            $sDate = $rowTime["res_date"];
            $shopname = $rowTime['shopname'];
            $etc = $rowTime["etc"];
            $res_confirm = $rowTime["res_confirm"];
    
        }

        if($res_confirm == 3){
            $pointMsg = ' ▶ 탑승시간/위치 안내\n'.$busStopInfo;
            $info2_title = "탑승시간/위치 안내";
            $info2 = str_replace('      -', '&nbsp;&nbsp;&nbsp;-', str_replace('\n', '<br>', $busStopInfo));
        }
    
        // 카카오톡 알림톡 발송
        // $msgTitle = '액트립 '.$shopname.' 변경 안내';
        $msgTitle = '액트립 서핑버스 정보변경 안내';
        $kakaoMsg = $msgTitle.'\n안녕하세요. '.$userName.'님\n\n액트립 예약정보 변경\n ▶ 예약번호 : '.$ResNumber.'\n ▶ 예약자 : '.$userName.'\n'.$resList.$pointMsg.'---------------------------------\n ▶ 안내사항\n      - 변경된 좌석/정류장 정보를 확인해주세요~\n\n ▶ 문의\n      - http://pf.kakao.com/_HxmtMxl';
    
        $arrKakao = array(
            "gubun"=> "bus"
            , "admin"=> "N"
            , "smsTitle"=> $msgTitle
            , "userName"=> $userName
            , "tempName"=> "at_bus_step1"
            , "kakaoMsg"=>$kakaoMsg
            , "userPhone"=> $userPhone
            , "link1"=>"orderview?num=1&resNumber=".$ResNumber //예약조회/취소
            , "link2"=>"pointchange?num=1&resNumber=".$ResNumber //예약조회/취소
            , "link3"=>"surfbusgps" //셔틀버스 실시간위치 조회
            , "link4"=>"pointlist?resparam=".$resparam //셔틀버스 탑승 위치확인
            , "link5"=>"event" //공지사항
            , "smsOnly"=>"N"
            , "PROD_NAME"=>"서핑버스 정류장변경"
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
        
        // 이메일 발송
        if(strrpos($user_email, "@") > 0){
            $to .= ','.$usermail;
        }

        $arrMail = array(
            "gubun"=> "bus"
            , "gubun_step" => 9
            , "gubun_title" => $shopname
            , "mailto"=> $to
            , "mailfrom"=> "surfbus_point@actrip.co.kr"
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
        
		mysqli_query($conn, "COMMIT");
        
        echo '<script>alert("셔틀버스 예약건 변경이 완료되었습니다.");parent.location.href="/orderview?num=0&resNumber='.$ResNumber.'";</script>';
        //echo '<script>alert("셔틀버스 예약건 변경이 완료되었습니다.");parent.fnUnblock("#divConfirm");</script>';
    }
}
?>