<?php
include __DIR__.'/../common/db.php';
include __DIR__.'/../common/kakaoalim.php';
include __DIR__.'/../common/func.php';


$success = true;
$datetime = date('Y/m/d H:i'); 

$user_name = trim(urldecode($_REQUEST["username"]));
$weeknum = trim($_REQUEST["weeknum"]);
$timenum = trim($_REQUEST["timenum"]);
$timestart = trim($_REQUEST["timestart"]);
$timeend = trim($_REQUEST["timeend"]);

$select_query = "DELETE FROM AT_CALL_TIMEOVER WHERE TIMESTAMPDIFF(DAY, insdate, now()) > 8";
$result_set = mysqli_query($conn, $select_query);


$select_query = "INSERT INTO AT_CALL_TIMEOVER(`user_name`, `weeknum`, `timenum`, `insdate`, `stats`, `timestart`, `timeend`, `sqlquery`) VALUES ('$user_name', $weeknum, $timenum, now(), 'OK', $timestart, $timeend, '')";
$result_set = mysqli_query($conn, $select_query);
$seq = mysqli_insert_id($conn);

mysqli_query($conn, "COMMIT");

//============================ 실행 ============================
mysqli_query($conn, "SET AUTOCOMMIT=0");
mysqli_query($conn, "BEGIN");

$errChk = "01";
$countChk = "";

//==== 액트립 예약건 자동취소 : 1시간 체크 ====
$ressubseq = "";
$select_query = 'SELECT a.user_name, a.user_tel, a.etc, b.* 
                    FROM AT_RES_MAIN as a INNER JOIN AT_RES_SUB as b 
                        ON a.resnum = b.resnum 
                    WHERE b.res_confirm = 0
                        AND TIMESTAMPDIFF(MINUTE, b.insdate, now()) > 60
                    ORDER BY b.resnum, b.res_date, b.ressubseq';
$query_log .= '조회 AT_RES_SUB : '.$select_query;

$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);

$k = 0;
if($count > 0){
	$x = 0;
	$PreMainNumber = "";
	$arrSeatInfo = array();
	while ($rowTime = mysqli_fetch_assoc($result_setlist)){
		$MainNumber = $rowTime['resnum'];

//============================ 실행 단계 ============================
		if($MainNumber != $PreMainNumber && $x > 0){
			if($code == "bus"){
                foreach($arrSeatInfo as $bus) {
                    $busSeatInfo .= $bus;
                }
        
                $resList =' ▶ 좌석안내\n'.$busSeatInfo;
                $subtitlename = '액트립';
            }else{
                $resList =' ▶ 신청목록\n'.$surfshopMsg;
                $subtitlename = $shopname;
            }
        
            $msgTitle = '액트립 '.$shopname.' 자동취소 안내';
            $kakaoMsg = $msgTitle.'\n안녕하세요. '.$userName.'님\n\n'.$subtitlename.' 예약정보 [자동취소]\n ▶ 예약번호 : '.$PreMainNumber.'\n ▶ 예약자 : '.$userName.'\n'.$resList.'---------------------------------\n ▶ 안내사항\n      - 입금마감시간이 지나서 자동취소가 되었습니다.\n\n ▶ 문의\n      - http://pf.kakao.com/_HxmtMxl';
        
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
                , "PROD_NAME"=>"자동취소"
                , "PROD_URL"=>$shopseq
                , "PROD_TYPE"=>$code
                , "RES_CONFIRM"=>"7"
            );
            $arrRtn = sendKakao($arrKakao); //알림톡 발송
        
            // 카카오 알림톡 DB 저장 START
            $select_query = kakaoDebug($arrKakao, $arrRtn);
            $result_set = mysqli_query($conn, $select_query);
            // 카카오 알림톡 DB 저장 END

            $surfshopMsg = "";
			$busSeatInfo = "";
			$arrSeatInfo = array();

			$k++;
		}
//============================ 실행 단계 ============================
        
		$code = $rowTime['code'];
		$userName = $rowTime['user_name'];
		$userPhone = $rowTime['user_tel'];
		$sDate = $rowTime["res_date"];
        $shopname = $rowTime['shopname'];
        $optname = $rowTime["optname"];
        $shopseq = $rowTime["seq"];

        if($code == "bus"){
            if(array_key_exists($sDate.$rowTime['res_bus'], $arrSeatInfo)){
                $arrSeatInfo[$sDate.$rowTime['res_bus']] .= '     - '.$rowTime['res_seat'].'번\n';
            }else{
                $arrSeatInfo[$sDate.$rowTime['res_bus']] = '    ['.$sDate.'] '.fnBusNum($rowTime['res_bus']).'\n     - '.$rowTime['res_seat'].'번\n';
            }
        }else{
            $ResNum = "      - 인원 : ";
            if($rowTime['res_m'] > 0){
                $ResNum .= "남:".$rowTime['res_m'].'명';
            }
            if($rowTime['res_m'] > 0 && $rowTime['res_w'] > 0){
                $ResNum .= ",";
            }
            if($rowTime['res_w'] > 0){
                $ResNum .= "여:".$rowTime['res_w'].'명';
            }
            $ResNum .= '\n';

            $surfshopMsg .= '    ['.$optname.']\n      - 예약일 : '.$sDate.'\n'.$ResNum.'\n';
        }

        $x++;

		$PreMainNumber = $rowTime['resnum'];
		$ressubseq .= $rowTime['ressubseq'].',';
	}
	$ressubseq .= '0';

//============================ 실행 단계 ============================
    if($code == "bus"){
        foreach($arrSeatInfo as $bus) {
            $busSeatInfo .= $bus;
        }

        $resList =' ▶ 좌석안내\n'.$busSeatInfo;
        $subtitlename = '액트립';
    }else{
        $resList =' ▶ 신청목록\n'.$surfshopMsg;
        $subtitlename = $shopname;
    }

    $msgTitle = '액트립 '.$shopname.' 자동취소 안내';
    $kakaoMsg = $msgTitle.'\n안녕하세요. '.$userName.'님\n\n'.$subtitlename.' 예약정보 [자동취소]\n ▶ 예약번호 : '.$MainNumber.'\n ▶ 예약자 : '.$userName.'\n'.$resList.'---------------------------------\n ▶ 안내사항\n      - 입금마감시간이 지나서 자동취소가 되었습니다.\n\n ▶ 문의\n      - http://pf.kakao.com/_HxmtMxl';

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
        , "PROD_NAME"=>"자동취소"
        , "PROD_URL"=>$shopseq
        , "PROD_TYPE"=>$code
        , "RES_CONFIRM"=>"7"
    );
    $arrRtn = sendKakao($arrKakao); //알림톡 발송

    // 카카오 알림톡 DB 저장 START
    $select_query = kakaoDebug($arrKakao, $arrRtn);
    $result_set = mysqli_query($conn, $select_query);
    // 카카오 알림톡 DB 저장 END

	$k++;
//============================ 실행 단계 ============================

	$success = true;
	$select_query = "UPDATE `AT_RES_SUB` 
                        SET res_confirm = 7
                            ,upddate = now()
                            ,upduserid = 'timeover'
                        WHERE ressubseq IN (".$ressubseq.")";

	$query_log .= '
                    자동취소 AT_RES_SUB : '.str_replace("'", '"',$select_query);

	$result_set = mysqli_query($conn, $select_query);

	$errChk .= "|07";
    if(!$result_set) $success = false;
    
    $countChk .= "@".$code."|".$k;   
}

//==== 솔게하 예약건 알림톡 발송 시작 ====
$select_querySol = "SELECT * FROM `AT_CALL_TIMEOVER` WHERE user_name = '솔알림톡' AND sqlquery = DATE_FORMAT(NOW(), '%Y-%m-%d')";
$result_setlist = mysqli_query($conn, $select_querySol);
$count = mysqli_num_rows($result_setlist);

if(date("H") >= 9 && $count == 0){
    $select_query = "INSERT INTO AT_CALL_TIMEOVER(`user_name`, `weeknum`, `timenum`, `insdate`, `stats`, `timestart`, `timeend`, `sqlquery`) VALUES ('솔알림톡', $weeknum, $timenum, now(), 'OK', $timestart, $timeend, DATE_FORMAT(NOW(), '%Y-%m-%d'))";
    $result_set = mysqli_query($conn, $select_query);

    $select_querySol = "SELECT a.resseq FROM AT_SOL_RES_MAIN a INNER JOIN AT_SOL_RES_SUB b 
                                ON a.resseq = b.resseq
                            WHERE a.res_kakao = 0 AND a.res_kakao_chk = 'N' 
                                AND (DATE_ADD(b.sdate, INTERVAL -1 DAY) = DATE_FORMAT(NOW(), '%Y-%m-%d') OR DATE_ADD(b.resdate, INTERVAL -1 DAY) = DATE_FORMAT(NOW(), '%Y-%m-%d'))
                                AND a.res_confirm = '확정'
                                GROUP BY a.resseq";
    $query_log .= '
                    솔 카톡발송 AT_SOL_RES_MAIN : '.str_replace("'", '"',$select_querySol);

    $result_setlist = mysqli_query($conn, $select_querySol);
    $count = mysqli_num_rows($result_setlist);

    if($count > 0){
        while ($rowSol = mysqli_fetch_assoc($result_setlist)){
            $resseq = $rowSol['resseq'];

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
            $kakaoMsg = $msgTitle.'\n안녕하세요. '.$userName.'님\n\n솔.동해서핑점 예약정보\n ▶ 예약자 : '.$userName.'\n ▶ 예약내역 : '.$resList.'\n\n'.$resInfo.'---------------------------------\n ▶ 안내사항\n      - 예약하신 시간보다 늦게 도착하실 경우 꼭 연락주세요.\n\n ▶ 문의\n      - 010.4337.5080\n      - http://pf.kakao.com/_HxmtMxl';
    
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
            if(!$result_set) $success = false;
        }
    }
}
//==== 솔게하 예약건 알림톡 발송 종료 ====

$select_query = "UPDATE AT_CALL_TIMEOVER SET success = '".$success."', stats = '".$errChk."', gubuncount = '".$countChk."', sqlquery = '".$query_log."' WHERE seq = ".$seq;
// echo $success.'<br><br>'.$query_log.'<br><br>'.$select_query;
$result_set = mysqli_query($conn, $select_query);

if(!$success){
	mysqli_query($conn, "ROLLBACK");
	$success = 'err';
}else{
	mysqli_query($conn, "COMMIT");
	$success = 'ok';
}
// $query_log = '';

//mysqli_query($conn, "COMMIT");
?>