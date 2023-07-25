<?php 
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/kakaoalim.php';
include __DIR__.'/../../common/func.php';

$param = $_REQUEST["resparam"];
$gubun = $_REQUEST["gubun"];
$num = $_REQUEST["num"];
$to = "lud1@naver.com";

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

        if($ressubseqInfo != ""){
            if($code == "bus"){
                // 예약좌석 정보
                foreach($arrSeatInfo as $x) {
                    $msgInfo .= $x;
                }

                $rtnText = '\n ▶ 환불요청 안내'
                    .'\n       - 결제금액 : '.number_format($TotalPrice).'원'
                    .'\n       - 환불수수료 : '.number_format($TotalFee).'원'
                    .'\n       - 환불금액 : '.number_format($TotalPrice-$TotalFee).'원'
                    .'\n  ▶환불계좌\n       - '.str_replace('|', ' / ', $FullBankText).'\n';

                $msgInfo = " ▶ 좌석안내\n".$msgInfo.$rtnText;
                $mailmsgInfo = $msgInfo;
                $shopname = '서핑버스';
            }else{
                $msgInfo = " ▶ 신청목록\n".$surfMsg;
                $mailmsgInfo = $surfMsg;
            }

            $msgTitle = '액트립 '.$shopname.' 환불안내';
            $arrKakao = array(
                "gubun"=> $code
                , "admin"=> "N"
                , "tempName"=> "at_res_step4"
                , "smsTitle"=> $msgTitle
                , "userName"=> $userName
                , "userPhone"=> $userPhone
                , "shopname"=> $shopname
                , "MainNumber"=> $ResNumber
                , "msgInfo"=>$msgInfo
                , "smsOnly"=>"N"
                , "PROD_NAME"=>"취소/환불요청"
                , "PROD_URL"=>$shopseq
                , "PROD_TYPE"=> $code."_return"
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

    $busSeatInfoS = "";
    $busSeatInfoE = "";
    $arrSeatInfoS = array();
    $arrSeatInfoE = array();
    
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
    
        if($res_confirm == 0){ //입금대기
            //$pointTime = ' -> '.$endLocationY[$i];
            $pointTime = ' / '.explode("|", fnBusPoint($startLocationY[$i], $busNumY[$i]))[0];
        }else{
            $pointTime = ' / '.explode("|", fnBusPoint($startLocationY[$i], $busNumY[$i]))[0];
        }

        if(array_key_exists($SurfDateBusY[$i].$busNumY[$i], $arrSeatInfoS)){
            $arrSeatInfoS[$SurfDateBusY[$i].$busNumY[$i]] .= '      - '.$arrSeatY[$i].'번 ('.$startLocationY[$i].$pointTime.')\n';
        }else{
            $arrSeatInfoS[$SurfDateBusY[$i].$busNumY[$i]] = '    ['.$SurfDateBusY[$i].'] '.fnBusNum($busNumY[$i]).'\n      - '.$arrSeatY[$i].'번 ('.$startLocationY[$i].$pointTime.')\n';
        }
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

        if($res_confirm == 0){ //입금대기
            //$pointTime = ' -> '.$endLocationS[$i];
            $pointTime = ' / '.explode("|", fnBusPoint($startLocationS[$i], $busNumS[$i]))[0];
        }else{
            $pointTime = ' / '.explode("|", fnBusPoint($startLocationS[$i], $busNumS[$i]))[0];
        }

        if(array_key_exists($SurfDateBusS[$i].$busNumS[$i], $arrSeatInfoE)){
            $arrSeatInfoE[$SurfDateBusS[$i].$busNumS[$i]] .= '      - '.$arrSeatS[$i].'번 ('.$startLocationS[$i].$pointTime.')\n';
        }else{
            $arrSeatInfoE[$SurfDateBusS[$i].$busNumS[$i]] = '    ['.$SurfDateBusS[$i].'] '.fnBusNum($busNumS[$i]).'\n      - '.$arrSeatS[$i].'번 ('.$startLocationS[$i].$pointTime.')\n';
        }
    }
    
	if(!$success){
        errGo:
		mysqli_query($conn, "ROLLBACK");
		echo '<script>alert("좌석/정류장 수정 중 오류가 발생하였습니다.\n\n관리자에게 문의해주세요.");parent.fnUnblock("#divConfirm");</script>';
	}else{
		// 예약좌석 정보 : 양양행
		foreach($arrSeatInfoS as $x) {
			$busSeatInfoS .= $x;
		}

		// 예약좌석 정보 : 서울행
		foreach($arrSeatInfoE as $x) {
			$busSeatInfoE .= $x;
		}

        $select_query = "SELECT a.user_name, a.user_tel, a.etc, a.user_email, b.* 
                            FROM AT_RES_MAIN as a INNER JOIN AT_RES_SUB as b 
                                ON a.resnum = b.resnum 
                            WHERE a.resnum = $ResNumber
                                AND b.res_confirm IN (0,3,8)
                            ORDER BY b.ressubseq";
        $result_setlist = mysqli_query($conn, $select_query);
        $count = mysqli_num_rows($result_setlist);

        $TotalPrice = 0;
        while ($rowTime = mysqli_fetch_assoc($result_setlist)){
            $userName = $rowTime['user_name'];
            $userPhone = $rowTime['user_tel'];
            $user_email = $rowTime['user_email'];
            $sDate = $rowTime["res_date"];
            $shopname = $rowTime['shopname'];
            $etc = $rowTime["etc"];
            $res_confirm = $rowTime["res_confirm"];
            $TotalPrice += $rowTime["res_totalprice"];
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
        
        if($res_confirm == 0){ //입금대기
            $totalPrice = "\n ▶ 총 결제금액 : ".number_format($TotalPrice)."원\n";
            
            $tempName = "frip_bus03"; //입금대기
            $btn_ResSearch = "orderview?num=1&resNumber=".$ResNumber; //예약조회/취소
            $btn_ResChange = "pointchange?num=1&resNumber=".$ResNumber; //예약조회/취소
            $btn_ResGPS = "";
            $btn_ResPoint = "pointlist?num=1&resNumber=".$ResNumber; //탑승시간/위치안내
            $btn_Notice = "";
            $btn_ResContent = ""; //예약 상세안내

            $msgInfo = $busSeatInfoTotal.$totalPrice;
        }else{
            $tempName = "frip_bus02"; //예약확정
            $btn_ResSearch = "orderview?num=1&resNumber=".$ResNumber; //예약조회
            $btn_ResChange = "pointchange?num=1&resNumber=".$ResNumber; //좌석/정류장 변경
            $btn_ResGPS = "surfbusgps"; //서핑버스 실시간위치 조회
            $btn_ResPoint = "pointlist?num=1&resNumber=".$ResNumber; //탑승시간/위치안내
            $btn_Notice = "";
            $btn_ResContent = ""; //예약 상세안내

            $msgInfo = $busSeatInfoTotal;
        }

        // 고객 카카오톡 발송
        $msgTitle = '액트립 서핑버스 변경신청 안내';

        
        $gubun_title = $busTitleName.' 서핑버스';
        $arrKakao = array(
            "gubun"=> "bus"
            , "admin"=> "N"
            , "tempName"=> $tempName
            , "smsTitle"=> $msgTitle
            , "userName"=> $userName
            , "userPhone"=> $userPhone
            , "shopname"=>$gubun_title
            , "msgType"=>$msgType
            , "MainNumber"=>$ResNumber
            , "msgInfo"=>$msgInfo
            , "btn_ResContent"=> $btn_ResContent
            , "btn_ResSearch"=> $btn_ResSearch
            , "btn_ResChange"=> $btn_ResChange
            , "btn_ResGPS"=> $btn_ResGPS
            , "btn_ResPoint"=> $btn_ResPoint
            , "btn_Notice"=> $btn_Notice
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
        //sendMail($arrMail); //메일 발송
        
		mysqli_query($conn, "COMMIT");
        
        echo '<script>alert("셔틀버스 예약건 변경이 완료되었습니다.");parent.location.href="/orderview?num=0&resNumber='.$ResNumber.'";</script>';
    }
}
?>