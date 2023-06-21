<?php
include __DIR__.'/../../db.php';
include __DIR__.'/../../common/kakaoalim.php';
include __DIR__.'/../../frip/inc_func.php';

$success = true;
$datetime = date('Y/m/d H:i'); 

$param = $_REQUEST["resparam"];
$InsUserID = $_REQUEST["userid"];

$intseq = "";
$intseq3 = "";
//$to = "lud1@naver.com";
$to = "lud1@naver.com";

mysqli_query($conn, "SET AUTOCOMMIT=0");
mysqli_query($conn, "BEGIN");

if($param == "changeConfirmFrip"){ //셔틀버스 정보 업데이트
	//단일 컬럼
	$resnum = $_REQUEST["resnum"];
	$user_name = $_REQUEST["user_name"];
	$user_tel = $_REQUEST["user_tel"];
	$user_email = $_REQUEST["user_email"];
	$memo = $_REQUEST["memo"]; //직원메모
	//$etc = $_REQUEST["etc"]; //요청사항
	$res_price_coupon = $rowSub['res_price_coupon'];
	$coupon = $rowSub['res_coupon'];
	$res_price = $_REQUEST["res_price"];
	$res_disprice = $_REQUEST["res_disprice"];
	$insdate = $_REQUEST["insdate"];
	$confirmdate = $_REQUEST["confirmdate"];
	$res_cooperate = $_REQUEST["res_cooperate"];
	$InsUserID = "admin";

	//배열 컬럼
    $chkCancel = $_REQUEST["ressubseq"];
    $res_seat = $_REQUEST["res_seat"];
    $res_spointname = $_REQUEST["res_spointname"];
    $res_epointname = $_REQUEST["res_epointname"];
    $res_confirm = $_REQUEST["res_confirm"];
    $rtn_charge_yn = $_REQUEST["rtn_charge_yn"];
    $res_kakao = $_REQUEST["res_kakao"];

	//================= 예약상태 및 메모 저장 =================
    $select_query = "UPDATE `AT_RES_FRIP_MAIN` 
                    SET user_name = '".$user_name."'
                        ,memo = '".$memo."'
                        ,user_tel = '".$user_tel."'
                        ,user_email = '".$user_email."'
                WHERE resnum = '".$resnum."';";
    $result_set = mysqli_query($conn, $select_query);
    if(!$result_set) goto errGo;

	for($i = 0; $i < count($chkCancel); $i++){
		if($chkCancel[$i] == ""){
			continue;
		}

		$insdate1 = "";
		if($res_confirm[$i] == 3){
			$insdate1 = ",confirmdate = now()";

			if($res_kakao[$i] == "Y"){
				$intseq3 .= $chkCancel[$i].",";
			}
		}

		$select_query = "UPDATE AT_RES_FRIP_SUB 
				SET res_seat = ".$res_seat[$i]."
					,res_spoint = '".$res_spointname[$i]."'
					,res_spointname = '".$res_spointname[$i]."'
					,res_epoint = '".$res_epointname[$i]."'
					,res_epointname = '".$res_epointname[$i]."'
					,res_confirm = ".$res_confirm[$i]."
					,rtn_charge_yn = '".$rtn_charge_yn[$i]."'
					".$insdate1."
					,upddate = now()
					,upduserid = 'admin'
			WHERE ressubseq = ".$chkCancel[$i].";";
		$result_set = mysqli_query($conn, $select_query);
		if(!$result_set) goto errGo;
	}

    $intseq3 .= '0';

    $arrSeatInfo = array();
    $arrStopInfo = array();

    $ResNumber = $resnum;
	$userName = $user_name;
	$etc = $_REQUEST["etc"];
	$userPhone = $user_tel;
	$usermail = $user_email;

    //==========================카카오 메시지 발송 ==========================
    if($intseq3 != "0"){ //예약 확정처리 : 고객발송
        $select_query_sub = "SELECT * FROM AT_RES_FRIP_SUB WHERE ressubseq IN ($intseq3) ORDER BY res_date, ressubseq";
        $resultSite = mysqli_query($conn, $select_query_sub);

        while ($rowSub = mysqli_fetch_assoc($resultSite)){
            $shopSeq = $rowSub['seq'];
			$shopname = $rowSub['shopname'];
			$coupon = $rowSub['res_coupon'];

            if(array_key_exists($rowSub['res_date'].$rowSub['res_busnum'], $arrSeatInfo)){
                $arrSeatInfo[$rowSub['res_date'].$rowSub['res_busnum']] .= '      - '.$rowSub['res_seat'].'번 ('.$rowSub['res_spointname'].' -> '.$rowSub['res_epointname'].')\n';
            }else{
				
				$weekday = fnWeek($rowSub['res_date']);
                $arrSeatInfo[$rowSub['res_date'].$rowSub['res_busnum']] = ' ▶ ['.$rowSub['res_date'].'('.$weekday.')] '.fnBusNum($rowSub['res_busnum']).'\n      - '.$rowSub['res_seat'].'번 ('.$rowSub['res_spointname'].' -> '.$rowSub['res_epointname'].')\n';
            }

            $arrData = explode("|", fnBusPoint($rowSub['res_spointname'], $rowSub['res_busnum'], 0));
            $arrStopInfo[$rowSub['res_spointname']] = '    ['.$rowSub['res_spointname'].'] '.$arrData[0].'\n      - '.$arrData[1].'\n';
        }
        
        foreach($arrSeatInfo as $x) {
            $busSeatInfo .= $x.'\n';
        }

        foreach($arrStopInfo as $x) {
            $busStopInfo .= $x;
        }

		$kakaoMsg = $msgTitle.'\n안녕하세요. '.$userName.'님\n\n액트립 예약정보 [예약확정]\n ▶ 예약번호 : '.$ResNumber.'\n ▶ 예약자 : '.$userName.'\n ▶ 좌석안내\n'.$busSeatInfo.$pointMsg.$etcMsg.'---------------------------------\n ▶ 안내사항'.$infomsg.'\n\n ▶ 문의\n      - 010.3308.6080\n      - http://pf.kakao.com/_HxmtMxl';

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

        
       
        $msgTitle = '제천국제음악영화제 셔틀버스 예약안내';
        $btn_ResPoint = "frip_bus2"; //예약 상세안내

		//$msgTitle = '액트립x프립버스 예약안내';
        $busSeatInfoTotal = $busSeatInfo;
        $kakaoMsg = $msgTitle.'\n\n안녕하세요. '.$userName.'님\n액트립x프립버스를 예약해주셔서 감사합니다.\n\n예약정보 [예약확정]\n ▶ 예약자 : '.$userName.'\n'.$busSeatInfoTotal.'---------------------------------\n ▶ 안내사항\n      - 교통상황으로 인해 정류장에 지연 도착할 수 있으니 양해부탁드립니다.\n      - 이용일, 탑승시간, 탑승위치 꼭 확인 부탁드립니다.\n      - 탑승시간 5분전에는 도착해주세요~\n      - 문의는 프립 고객센터로 연락해주세요~';

		$tempName = "frip_bus02";
        $btn_ResSearch = "orderview?num=1&resNumber=".$ResNumber; //예약조회
        $btn_ResChange = "pointchangeFrip?num=1&resNumber=".$ResNumber; //예약변경
        $btn_ResGPS = "frip_gps"; //서핑버스 실시간위치 조회
        $btn_ResCustomer = ""; //문의하기
        $btn_Notice = "";
        $btn_ResContent = ""; //예약 상세안내
        //$btn_ResPoint = "frip_bus1"; //예약 상세안내
        
        // 고객 카카오톡 발송
        $arrKakao = array(
            "gubun"=> "bus"
            , "admin"=> "N"
            , "smsTitle"=> $msgTitle
            , "userName"=> $userName
            , "tempName"=> $tempName
            , "kakaoMsg"=>$kakaoMsg
            , "userPhone"=> $userPhone
            , "btn_ResSearchFrip"=> $btn_ResSearch
            , "btn_ResPoint"=> $btn_ResPoint
            , "btn_ResContent"=> $btn_ResContent
            , "btn_ResSearch"=> $btn_ResSearch
            , "btn_ResChange"=> $btn_ResChange
            , "btn_ResGPS"=> $btn_ResGPS
            , "btn_ResCustomer"=> $btn_ResCustomer
            , "btn_Notice"=> $btn_Notice
            , "smsOnly"=>"N"
            , "PROD_NAME"=>"셔틀버스"
            , "PROD_URL"=>$shopseq
            , "PROD_TYPE"=>"bus"
            , "RES_CONFIRM"=>$res_confirm
        );
        
        $arrRtn = sendKakao($arrKakao); //알림톡 발송

		// 카카오 알림톡 DB 저장 START
		$select_query = kakaoDebug($arrKakao, $arrRtn);            
		$result_set = mysqli_query($conn, $select_query);
    }

	mysqli_query($conn, "COMMIT");
}

if(!$success){
	errGo:
	mysqli_query($conn, "ROLLBACK");
	echo 'err';
}else{
	echo '0';
}

mysqli_close($conn);
?>
