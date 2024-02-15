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


$query_log .= "호출URL : https://actrip.co.kr/act_2023/callback/timeover.php?username=$user_name&weeknum=$weeknum&timenum=$timenum&timestart=$timestart&timeend=$timeend";

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
$select_query = 'SELECT a.user_name, a.user_tel, b.seq, a.resnum
                    FROM AT_RES_MAIN as a INNER JOIN AT_RES_SUB as b 
                        ON a.resnum = b.resnum 
                    WHERE b.res_confirm = 0
                        AND TIMESTAMPDIFF(MINUTE, b.insdate, now()) > 60
                    GROUP BY a.user_name, a.user_tel, b.seq, a.resnum';
$query_log .= '
                조회 AT_RES_SUB : '.$select_query;

$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);

if($count > 0){
	$i = 0;
	$ResNumberList = "";
	while ($rowTime = mysqli_fetch_assoc($result_setlist)){
		$ResNumber = $rowTime['resnum'];
		$userName = $rowTime["user_name"];
		$userPhone = $rowTime["user_tel"];
		$shopseq = $rowTime["seq"];

		$ResNumberList .= $ResNumber.",";

		//==========================카카오 메시지 발송 ==========================
        if($shopseq == 7){
            $busTitleName = "양양"; 
        }else if($shopseq == 14){
            $busTitleName = "동해";    
        }

        $kakao_gubun = "bus_autocancel";
        $msgTitle = "액트립 자동취소 안내";
        $PROD_NAME = "셔틀버스 자동취소";
    
        $DebugInfo = array(
            "PROD_NAME" => $PROD_NAME
            , "PROD_TABLE" => "AT_RES_MAIN"
            , "PROD_TYPE" => $kakao_gubun
            , "RES_CONFIRM" => 3
            , "resnum" => $ResNumber
        );
        $arrKakao = array(
            "gubun"=> $kakao_gubun
            , "userName"=> $userName
            , "userPhone"=> $userPhone
            , "link1"=> shortURL("https://actrip.co.kr/orderview?num=1&resNumber=".$ResNumber) //예약
            , "DebugInfo"=> $DebugInfo
        );

		$arryKakao[$i] = $arrKakao;
		//==========================카카오 메시지 발송 ==========================

        $i++;
	}
    
	$ResNumberList .= "0";

    $arrKakao = array(
		"arryData"=> $arryKakao
		, "array"=> "true" //배열 여부
		, "tempName"=> "actrip_info03" //템플릿 코드
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
	}

	$success = true;
	$select_query = "UPDATE `AT_RES_SUB` 
                        SET res_confirm = 7
                            ,upddate = now()
                            ,upduserid = 'timeover'
                        WHERE resnum IN (".$ResNumberList.") AND res_confirm = 0";

	$query_log .= '
                    자동취소 AT_RES_SUB : '.str_replace("'", '"',$select_query);

	$result_set = mysqli_query($conn, $select_query);

	$errChk .= "|07";
    if(!$result_set) $success = false;
    
    $countChk .= "@bus|".$i;   
}


//==== 솔게하 예약건 알림톡 발송 시작 ====
$select_querySol = "SELECT * FROM `AT_CALL_TIMEOVER` WHERE user_name = '솔알림톡' AND sqlquery = DATE_FORMAT(NOW(), '%Y-%m-%d')";
$result_setlist = mysqli_query($conn, $select_querySol);
$count = mysqli_num_rows($result_setlist);

if(date("H") >= 9 && $count == 0){
    $select_query = "INSERT INTO AT_CALL_TIMEOVER(`user_name`, `weeknum`, `timenum`, `insdate`, `stats`, `timestart`, `timeend`, `sqlquery`) VALUES ('솔알림톡', $weeknum, $timenum, now(), 'OK', $timestart, $timeend, DATE_FORMAT(NOW(), '%Y-%m-%d'))";
    $result_set = mysqli_query($conn, $select_query);

    $select_querySol = "SELECT a.resseq, a.resnum, a.user_name, a.user_tel FROM AT_SOL_RES_MAIN a INNER JOIN AT_SOL_RES_SUB b 
                                ON a.resseq = b.resseq
                            WHERE a.res_kakao_chk = 'N' 
                                AND (DATE_ADD(b.sdate, INTERVAL -1 DAY) = DATE_FORMAT(NOW(), '%Y-%m-%d') OR DATE_ADD(b.resdate, INTERVAL -1 DAY) = DATE_FORMAT(NOW(), '%Y-%m-%d'))
                                AND a.res_confirm = '확정'
                                GROUP BY a.resseq, a.resnum, a.user_name, a.user_tel";
    $query_log .= '
                    솔 카톡발송 AT_SOL_RES_MAIN : '.str_replace("'", '"',$select_querySol);
    $result_setlist = mysqli_query($conn, $select_querySol);
    $count = mysqli_num_rows($result_setlist);

    if($count > 0){
        $i = 0;
        while ($rowSol = mysqli_fetch_assoc($result_setlist)){
            $seq = $rowSol['resseq'];
            $resnum = $rowSol['resnum'];
            $userName = $rowSol["user_name"];
            $userPhone = $rowSol["user_tel"];

            $select_query = "SELECT 
                                MIN(CASE 
                                        WHEN sdate = '0000-00-00' THEN NULL 
                                        ELSE sdate END) AS sdate
                                , MAX(CASE 
                                        WHEN edate = '0000-00-00' THEN NULL 
                                        ELSE edate END) AS edate
                                , MAX(CASE 
                                        WHEN resdate = '0000-00-00' THEN NULL 
                                        ELSE resdate END) AS resdate_max
                                , MIN(CASE 
                                        WHEN resdate = '0000-00-00' THEN NULL 
                                        ELSE resdate END) AS resdate_min
                            FROM AT_SOL_RES_SUB WHERE resseq = $seq";
            $result = mysqli_query($conn, $select_query);
            $rowSub = mysqli_fetch_array($result);

            $sdate = $rowSub["sdate"];
            $edate = $rowSub["edate"];
            $resdate_max = $rowSub["resdate_max"];
            $resdate_min = $rowSub["resdate_min"];

            $date_start = "";
            $date_end = "";
            if($sdate == null){ //숙박일이 없는 경우 : 바베큐 또는 강습이용일
                $date_start = $resdate_min;
                $date_end = $resdate_max;
            }else{ //숙박일이 있는 경우
                if($resdate_min == null){ //바베큐 또는 강습일이 없는 경우
                    $date_start = $sdate;
                    $date_end = $edate;
                }else{
                    if($sdate >= $resdate_min){
                        $date_start = $resdate_min;
                    }else{
                        $date_start = $sdate;
                    }
                    
                    if($edate <= $resdate_max){
                        $date_end = $resdate_max;
                    }else{
                        $date_end = $edate;
                    }
                }
            }

            $userDate = ($date_start == $date_end) ? $date_start : "$date_start ~ $date_end";

            //==========================카카오 메시지 발송 ==========================
            $DebugInfo = array(
                "PROD_NAME" => "솔게하"
                , "PROD_TABLE" => "AT_SOL_RES_MAIN"
                , "PROD_TYPE" => "sol_complete"
                , "RES_CONFIRM" => "-1"
                , "resnum" => $resnum
                , "resseq"=> $seq
            );

            $arrKakao = array(
                "gubun"=> "timeover"
                , "userName"=> $userName
                , "userPhone"=> "01944370009"//$userPhone
                , "userDate"=> $userDate
                , "link1"=>shortURL("https://actrip.co.kr/sol_kakao?num=1&seq=".urlencode(encrypt($seq))) //예약조회/취소
                , "DebugInfo"=> $DebugInfo
            );	
        
            $arryKakao[$i] = $arrKakao;
            $i++;
        }
        
        //==========================카카오 메시지 발송 ==========================
        $msgTitle = '솔게하&솔서프 동해점';

        $total_page = ceil(count($arryKakao) / 100);
        for ($x=0; $x < $total_page; $x++) {
            $arryKakao2 = array_filter($arryKakao, function($k) use ($x) {
                $page_cnt = ($x * 100);
                $start_cnt = 0 + $page_cnt;
                $end_cnt = 100 + $page_cnt;
                return $k >= $start_cnt && $k < $end_cnt;
            }, ARRAY_FILTER_USE_KEY);            
            
            $arrKakao = array(
                "arryData"=> $arryKakao2
                , "array"=> "true" //배열 여부
                , "tempName"=> "sol_info02" //템플릿 코드
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
                    , "item"=> $arryKakao[($i + ($x * 100))]
                    , "code"=> $code
                    , "msgid"=> $msgid
                    , "message"=> $message
                    , "originMessage"=> $originMessage
                );
        
                // 카카오 알림톡 DB 저장 START
                $select_query = kakaoDebug2024($kakao_response, json_encode($data[$i]));
                $result_set = mysqli_query($conn, $select_query);
                // 카카오 알림톡 DB 저장 END
        
                $resseq = $arryKakao[($i + ($x * 100))]["DebugInfo"]["resseq"];
                $select_query = "UPDATE `AT_SOL_RES_MAIN` SET res_kakaoinfo = 'Y', res_kakao = res_kakao + 1, userinfo = '".$msgid."' WHERE resseq = $resseq";
                $result_set = mysqli_query($conn, $select_query);
            }
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
?>