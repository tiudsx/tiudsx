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
    
    if($shopseq == 7){
        $busTitleName = "양양"; 
    }else if($shopseq == 14){
        $busTitleName = "동해";    
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

    $now = date("Y-m-d");
    $select_query = "SELECT a.user_name, a.user_tel, a.etc, a.user_email, b.bus_oper, b.res_confirm, b.ressubseq
                        FROM AT_RES_MAIN as a INNER JOIN AT_RES_SUB as b 
                            ON a.resnum = b.resnum 
                        WHERE a.resnum = $ResNumber
                            AND b.res_confirm IN (0,3,8)
                        ORDER BY b.ressubseq";
    $result_setlist = mysqli_query($conn, $select_query);
    $count = mysqli_num_rows($result_setlist);
    if($count == 0){
        echo "<script>alert('예약된 정보가 없거나 이용일이 지났습니다.\\n\\n관리자에게 문의해주세요.');parent.fnUnblock('#divConfirm');</script>";
        return;
    }
    $arrSeatInfoS = array();
    $arrSeatInfoE = array();
    
    $start_cnt = 0;
    $return_cnt = 0;
    $TotalPrice = 0;
    while ($row = mysqli_fetch_assoc($result_setlist)){
        $userName = $row["user_name"];
        $userPhone = $row["user_tel"];
        $res_confirm = $row["res_confirm"];  
        $ressubseq = $row["ressubseq"];

        $bus_oper = $row["bus_oper"];
        
        $TotalPrice += $row["res_totalprice"];

        if($bus_oper == "start"){ //서울 출발
            array_push($arrSeatInfoS, $ressubseq);
            $start_cnt++;
        }else{ //복귀
            array_push($arrSeatInfoE, $ressubseq);
            $return_cnt++;
        } 
    }

    if($start_cnt != count($SurfDateBusE)){
        echo '<script>alert("예약된 좌석수('.$start_cnt.'자리)와 동일한 개수로 선택해주세요~");parent.fnUnblock("#divConfirm");</script>';
        return;
    }

    if($return_cnt != count($SurfDateBusE)){
        echo '<script>alert("예약된 좌석수('.$return_cnt.'자리)와 동일한 개수로 선택해주세요~");parent.fnUnblock("#divConfirm");</script>';
        return;
    }
    
    for($i = 0; $i < count($SurfDateBusS); $i++){
        $select_query = 'SELECT res_spoint FROM AT_RES_SUB WHERE seq = '.$shopseq.' AND res_date = "'.$SurfDateBusS[$i].'" AND bus_gubun = "'.$busGubunS[$i].'" AND bus_num = "'.$busNumS[$i].'" AND res_seat = "'.$arrSeatE[$i].'" AND res_confirm IN (0, 1, 2, 3) AND resnum != '.$ResNumber;
        $result_setlist = mysqli_query($conn, $select_query);
		$count = mysqli_num_rows($result_setlist);

		if($count > 0){
			echo '<script>alert("['.$SurfDateBusS[$i].'] '.$arrSeatS[$i].'번 좌석은 이미 예약된 자리입니다.\n\n다른좌석을 선택해주세요.");parent.fnUnblock("#divConfirm");</script>';
			return;
		}
	}

	for($i = 0; $i < count($SurfDateBusE); $i++){
		$select_query = 'SELECT res_spoint FROM AT_RES_SUB WHERE seq = '.$shopseq.' AND res_date = "'.$SurfDateBusE[$i].'" AND bus_gubun = "'.$busGubunE[$i].'" AND bus_num = "'.$busNumE[$i].'" AND res_seat = "'.$arrSeatE[$i].'" AND res_confirm IN (0, 1, 2, 3) AND resnum != '.$ResNumber;
		$result_setlist = mysqli_query($conn, $select_query);
		$count = mysqli_num_rows($result_setlist);

		if($count > 0){
			echo '<script>alert("['.$SurfDateBusE[$i].'] '.$arrSeatE[$i].'번 좌석은 이미 예약된 자리입니다.\n\n다른좌석을 선택해주세요.");parent.fnUnblock("#divConfirm");</script>';
			return;
		}
    }

	mysqli_query($conn, "SET AUTOCOMMIT=0");
    mysqli_query($conn, "BEGIN");

    $day_start = "-";
    $day_return = "-";
    
    //출발 좌석예약
    for($i = 0; $i < count($SurfDateBusS); $i++){
        $select_query = "UPDATE AT_RES_SUB SET 
                            res_seat = '$arrSeatS[$i]', 
                            res_spoint = '$startLocationS[$i]', 
                            res_spointname = '$startLocationS[$i]', 
                            res_epoint = '$endLocationS[$i]', 
                            res_epointname = '$endLocationS[$i]', 
                            upddate = now()
                                WHERE ressubseq = ".$arrSeatInfoS[$i];
        $result_set = mysqli_query($conn, $select_query);
        if(!$result_set) goto errGo;
    
        //출발일 정보
        if($day_start == "-"){
            $arrBus = fnBusNum2023($busGubunS[$i].$busNumS[$i]);
            $day_start = '['.$SurfDateBusS[$i].'] '.$arrBus["point"].' '.$arrBus["num"];
        }
    }
    
    //서울행 좌석예약
    for($i = 0; $i < count($SurfDateBusE); $i++){
        $select_query = "UPDATE AT_RES_SUB SET 
                            res_seat = '$arrSeatE[$i]', 
                            res_spoint = '$startLocationE[$i]', 
                            res_spointname = '$startLocationE[$i]', 
                            res_epoint = '$endLocationE[$i]', 
                            res_epointname = '$endLocationE[$i]', 
                            upddate = now()
                                WHERE ressubseq = ".$arrSeatInfoE[$i];
        $result_set = mysqli_query($conn, $select_query);
        if(!$result_set) goto errGo;

        //복귀일 정보
        if($day_return == "-"){
            $arrBus = fnBusNum2023($busGubunE[$i].$busNumE[$i]);
            $day_return = '['.$SurfDateBusE[$i].'] '.$arrBus["point"].' '.$arrBus["num"];
        }
    }
    
	if(!$success){
        errGo:
		mysqli_query($conn, "ROLLBACK");
		echo '<script>alert("좌석/정류장 변경 중 오류가 발생하였습니다.\n\n관리자에게 문의해주세요.");parent.fnUnblock("#divConfirm");</script>';
	}else{
        if($res_confirm == 0){
            $kakao_gubun = "bus_stay";
            $msgTitle = '액트립 셔틀버스 입금안내';
            $PROD_NAME = "셔틀버스 입금대기";
        }else{
            $kakao_gubun = "bus_confirm_change";
            $msgTitle = '액트립 셔틀버스 변경안내';
            $PROD_NAME = "셔틀버스 예약확정";
            $link1 = shortURL("https://actrip.co.kr/orderview?num=1&resNumber=".$ResNumber);
        }

        if($day_start != "-" && $day_return != "-"){ //왕복
            $bus_line = "서울 ↔ $busTitleName";
        }else if($day_start != "-"){ //서울 출발
            $bus_line = "서울 → $busTitleName";
        }else{ //서울 복귀
            $bus_line = "$busTitleName → 서울";
        }
        //==========================카카오 메시지 발송 ==========================
        //$msgTitle = '액트립 셔틀버스 입금안내';
        $DebugInfo = array(
            "PROD_NAME" => $PROD_NAME
            , "PROD_TABLE" => "AT_RES_MAIN"
            , "PROD_TYPE" => $kakao_gubun
            , "RES_CONFIRM" => $res_confirm
            , "resnum" => $ResNumber
        );
        $arrKakao = array(
            "gubun"=> $kakao_gubun
            , "userName"=> $userName
            , "userPhone"=> $userPhone
            , "userPrice"=> number_format($TotalPrice).'원'
            , "bus_line"=> $bus_line
            , "day_start"=> $day_start
            , "day_return"=> $day_return
            , "link1"=> $link1 //예약
            , "DebugInfo"=> $DebugInfo
        );	

        $arryKakao[0] = $arrKakao;
    
        $arrKakao = array(
            "arryData"=> $arryKakao
            , "array"=> "true" //배열 여부
            , "tempName"=> "actrip_info02" //템플릿 코드
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
    
            $errmsg = $select_query;
            
            $errCode = "06";
            if(!$result_set) goto errGo;
        }
        
		mysqli_query($conn, "COMMIT");
        
        echo '<script>alert("셔틀버스 예약건 변경이 완료되었습니다.");parent.parent.fnLayerView("");parent.parent.location.reload();</script>';
    }
}
?>