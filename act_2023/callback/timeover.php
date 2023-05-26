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
$select_query = 'SELECT a.user_name, a.user_tel, a.etc, b.* 
                    FROM AT_RES_MAIN as a INNER JOIN AT_RES_SUB as b 
                        ON a.resnum = b.resnum 
                    WHERE b.res_confirm = 0
                        AND TIMESTAMPDIFF(MINUTE, b.insdate, now()) > 60
                    ORDER BY b.resnum, b.res_date, b.ressubseq';
$query_log .= '
                조회 AT_RES_SUB : '.$select_query;

$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);

$k = 0;
if($count > 0){
	$x = 0;
	$PreMainNumber = "";
	while ($rowTime = mysqli_fetch_assoc($result_setlist)){
		$MainNumber = $rowTime['resnum'];

//============================ 실행 단계 ============================
		if($MainNumber != $PreMainNumber && $x > 0){        
            $msgTitle = '액트립 자동취소 안내';        
            $arrKakao = array(
                "gubun"=> $code
                , "admin"=> "N"
                , "tempName"=> "at_res_step4"
                , "smsTitle"=> $msgTitle
                , "userName"=> $userName
                , "userPhone"=> $userPhone
                , "shopname"=> $shopname
                , "MainNumber"=> $MainNumber
                , "smsOnly"=>"N"
                , "PROD_NAME"=>"자동취소"
                , "PROD_URL"=>$shopseq
                , "PROD_TYPE"=> $code."_cancel"
                , "RES_CONFIRM"=>"7"
            );
            $arrRtn = sendKakao($arrKakao); //알림톡 발송
        
            // 카카오 알림톡 DB 저장 START
            $select_query = kakaoDebug($arrKakao, $arrRtn);
            $result_set = mysqli_query($conn, $select_query);
            // 카카오 알림톡 DB 저장 END

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

        $x++;

		$PreMainNumber = $rowTime['resnum'];
		$ressubseq .= $rowTime['ressubseq'].',';
	}
	$ressubseq .= '0';

//============================ 실행 단계 ============================
    $msgTitle = '액트립 자동취소 안내';        
    $arrKakao = array(
        "gubun"=> $code
        , "admin"=> "N"
        , "tempName"=> "at_res_step4"
        , "smsTitle"=> $msgTitle
        , "userName"=> $userName
        , "userPhone"=> $userPhone
        , "shopname"=> $shopname
        , "MainNumber"=> $MainNumber
        , "smsOnly"=>"N"
        , "PROD_NAME"=>"자동취소"
        , "PROD_URL"=>$shopseq
        , "PROD_TYPE"=> $code."_cancel"
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

//==== 서핑버스 예약건 알림톡 발송 시작 ====

//==== 서핑버스 예약건 알림톡 발송 종료 ====


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
    
			$msgTitle = '솔게스트하우스&솔서프 예약안내';
            $arrKakao = array(
                "gubun"=> $code
                , "admin"=> "N"
                , "tempName"=> "at_surf_step3"
				, "smsTitle"=> $msgTitle
                , "userName"=> $userName
                , "userPhone"=> $userPhone
                , "link1"=>"sol_kakao?num=1&seq=".urlencode(encrypt($resseq)) //예약조회/취소
                , "link2"=>"sol_location?seq=".urlencode(encrypt($resseq)) //지도로 위치보기
                , "link3"=>"sol_location?seq=".urlencode(encrypt($resseq)) //이벤트
                , "smsOnly"=>"N"
                , "PROD_NAME"=>"솔게하"
                , "PROD_URL"=>""
                , "PROD_TYPE"=>"sol_complete"
                , "RES_CONFIRM"=>"-1"
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