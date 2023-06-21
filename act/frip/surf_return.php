<?php 
include __DIR__.'/../db.php';
include __DIR__.'/../common/kakaoalim.php';
include __DIR__.'/../frip/inc_func.php';

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
if($param == "PointChange"){ //정류장 변경
    $ResNumber = $_REQUEST["MainNumber"];
    $shopseq = $_REQUEST["shopseq"];
    $ressubseqs = $_REQUEST["ressubseqs"]; //셔틀버스 예약 시퀀스
    $ressubseqe = $_REQUEST["ressubseqe"]; //셔틀버스 예약 시퀀스
    $daytype = $_REQUEST["daytype"]; //편도 : 0, 왕복 : 1
    
    if($shopseq == 210){
        $busTypeY = "Y";
        $busTypeS = "S";
        $btn_ResPoint = "frip_bus1"; //예약 상세안내
        $busTitleName = "니지모리";
        $msgTitle = '니지모리 셔틀버스 예약안내';
    }else{
        $busTypeY = "E";
        $busTypeS = "A";
        $busTitleName = "제천";
        $msgTitle = '제천국제음악영화제 셔틀버스 예약안내';
        $btn_ResPoint = "frip_bus2"; //예약 상세안내
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
        $select_query = 'SELECT res_spoint FROM AT_RES_FRIP_SUB where res_date = "'.$SurfDateBusY[$i].'" AND res_bus = "'.$busNumY[$i].'" AND res_seat = "'.$arrSeatY[$i].'" AND res_confirm IN (0, 1, 2, 3) AND resnum != '.$ResNumber;
        //echo $select_query;
        $result_setlist = mysqli_query($conn, $select_query);
		$count = mysqli_num_rows($result_setlist);

		if($count > 0){
			echo '<script>alert("['.$SurfDateBusY[$i].'] '.$arrSeatY[$i].'번 좌석은 이미 예약된 자리입니다.\n\n다른좌석을 선택해주세요.");parent.fnUnblock("#divConfirm");</script>';
			return;
		}
	}

	for($i = 0; $i < count($SurfDateBusS); $i++){
		$select_query = 'SELECT res_spoint FROM AT_RES_FRIP_SUB where res_date = "'.$SurfDateBusS[$i].'" AND res_bus = "'.$busNumS[$i].'" AND res_seat = "'.$arrSeatS[$i].'" AND res_confirm IN (0, 1, 2, 3) AND resnum != '.$ResNumber;
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
        $select_query = "UPDATE AT_RES_FRIP_SUB SET 
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
        $select_query = "UPDATE AT_RES_FRIP_SUB SET 
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
            $weekday = fnWeek($SurfDatSurfDateBusSeBusS[$i]);
            $arrSeatInfo[$SurfDateBusS[$i].$busNumS[$i]] = ' ▶ ['.$SurfDateBusS[$i].'('.$weekday.')] '.fnBusNum($busNumS[$i]).'\n      - '.$arrSeatS[$i].'번 ('.$startLocationS[$i].' -> '.$endLocationS[$i].')\n';
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
            $busSeatInfo .= $bus.'\n';
        }
        
        // 정류장 정보
        foreach($arrStopInfo as $x) {
            $busStopInfo .= $x;
        }

        $resList = $busSeatInfo;
        $select_query = "SELECT a.user_name, a.user_tel, a.etc, a.user_email, b.* 
                            FROM AT_RES_FRIP_MAIN as a INNER JOIN AT_RES_FRIP_SUB as b 
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

        // 카카오톡 알림톡 발송
        $kakaoMsg = $msgTitle.'\n\n안녕하세요. '.$userName.'님\n좌석/정류장 변경신청하신 예약정보를 보내드립니다.\n\n예약정보 [예약확정]\n ▶ 예약자 : '.$userName.'\n'.$resList.'---------------------------------\n ▶ 안내사항\n      - 교통상황으로 인해 정류장에 지연 도착할 수 있으니 양해부탁드립니다.\n      - 이용일, 탑승시간, 탑승위치 꼭 확인 부탁드립니다.\n      - 탑승시간 5분전에는 도착해주세요~\n      - 문의는 프립 고객센터로 연락해주세요~';

        $tempName = "frip_bus02";
        $btn_ResSearch = "orderview?num=1&resNumber=".$ResNumber; //예약조회
        $btn_ResChange = "pointchangeFrip?num=1&resNumber=".$ResNumber; //예약변경
        $btn_ResGPS = "frip_gps"; //서핑버스 실시간위치 조회
        $btn_ResCustomer = ""; //문의하기
        $btn_Notice = "";
        $btn_ResContent = ""; //예약 상세안내
        
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
        if(!$result_set) goto errGo;
        // 카카오 알림톡 DB 저장 END
        
		mysqli_query($conn, "COMMIT");
        
        echo '<script>alert("셔틀버스 예약건 변경이 완료되었습니다.");parent.location.href="/orderview?num=0&resNumber='.$ResNumber.'";</script>';
        //echo '<script>alert("셔틀버스 예약건 변경이 완료되었습니다.");parent.fnUnblock("#divConfirm");</script>';
    }
}
?>