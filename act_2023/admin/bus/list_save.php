<?php
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/kakaoalim.php';
include __DIR__.'/../../common/func.php';

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

if($param == "changeConfirmNew"){ //셔틀버스 정보 업데이트
	//단일 컬럼
	$resnum = $_REQUEST["resnum"];
	$user_name = $_REQUEST["user_name"];
	$user_tel = $_REQUEST["user_tel"];
	$user_email = $_REQUEST["user_email"];
	$memo = $_REQUEST["memo"]; //직원메모
	//$etc = $_REQUEST["etc"]; //요청사항
	$insdate = $_REQUEST["insdate"];
	$confirmdate = $_REQUEST["confirmdate"];
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
    $select_query = "UPDATE `AT_RES_MAIN` 
                    SET user_name = '".$user_name."'
                        ,memo = '".$memo."'
                        ,user_tel = '".$user_tel."'
                        ,user_email = '".$user_email."'
                        ,insdate = '".$insdate."'
                WHERE resnum = '".$resnum."';";
    $result_set = mysqli_query($conn, $select_query);
    if(!$result_set) goto errGo;

	for($i = 0; $i < count($chkCancel); $i++){
		if($chkCancel[$i] == ""){
			continue;
		}

		$insdate1 = "";
		if($res_confirm[$i] == 3){
			//$insdate1 = ",confirmdate = now()";
			$insdate1 = ",confirmdate = '".$confirmdate."'";
			if($res_kakao[$i] == "Y"){
				$intseq3 .= $chkCancel[$i].",";
			}
		}

		$select_query = "UPDATE AT_RES_SUB 
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

	$busSeatInfoS = "";
	$busSeatInfoE = "";
	$arrSeatInfoS = array();
	$arrSeatInfoE = array();

    $ResNumber = $resnum;
	$userName = $user_name;
	$etc = $_REQUEST["etc"];
	$userPhone = $user_tel;
	$usermail = $user_email;

	//==========================카카오 메시지 발송 ==========================
    if($intseq3 != "0"){ //예약 확정처리 : 고객발송
        $select_query_sub = "SELECT a.*, b.couponseq FROM AT_RES_SUB AS a LEFT JOIN AT_COUPON_CODE AS b
									ON a.res_coupon = b.coupon_code
								WHERE ressubseq IN ($intseq3) ORDER BY res_date, ressubseq";
        $resultSite = mysqli_query($conn, $select_query_sub);

		$day_start = "-";
		$day_return = "-";
        while ($rowSub = mysqli_fetch_assoc($resultSite)){
            $shopseq = $rowSub['seq'];
			$shopname = $rowSub['shopname'];
			$coupon = $rowSub['res_coupon'];
			$busGubun = substr($rowSub['res_bus'], 0, 1);
			$couponseq = $rowSub["couponseq"]; //채널

			$res_date = $rowSub["res_date"];
			
			$bus_oper = $rowSub["bus_oper"];
			$bus_gubun = $rowSub["bus_gubun"];
			$bus_num = $rowSub["bus_num"];
	
			$arrBus = fnBusNum2023($bus_gubun.$bus_num);
			$bus_line = '['.$res_date.'] '.$arrBus["point"].' '.$arrBus["num"];
			if($bus_oper == "start"){ //서울 출발
				$day_start = $bus_line;
			}else{ //복귀
				$day_return = $bus_line;
			}
        }

		$res_confirm = 3; //확정
		$InsUserID = $coupon;
		$msgType = 1; //100% 할인 쿠폰
		$kakao_gubun = "bus_confirm";
		$msgTitle = '액트립 셔틀버스 확정안내';
		$PROD_NAME = "셔틀버스 예약확정";
		$link1 = shortURL("https://actrip.co.kr/orderview?num=1&resNumber=".$ResNumber);
		
		if($couponseq == 17 || $couponseq == 26){ //마린서프
            $link2 = '\n\n - 마린서프 안내 : '.shortURL("https://actrip.co.kr/act_2023/front/bus_pkg/surf_gisa.html");
        }else if($couponseq == 20 || $couponseq == 27){ //인구서프, 엉클 프립
            $link2 = '\n\n - 인구서프 안내 : '.shortURL("https://actrip.co.kr/act_2023/front/bus_pkg/surf_ingu.html");
        }else if($couponseq == 22 || $couponseq == 29){ //솔게하
            $link2 = '\n\n - 솔게하 안내 : '.shortURL("https://actrip.co.kr/act_2023/front/bus_pkg/surf_dh.html");
        }

        if($shopseq == 7){ //양양행
			$busTitleName = "양양";
		}else{ //동해행
			$busTitleName = "동해";
		}

        if($day_start != "-" && $day_return != "-"){ //왕복
            $bus_line = "서울 ↔ $busTitleName";
        }else if($day_start != "-"){ //서울 출발
            $bus_line = "서울 → $busTitleName";
        }else{ //서울 복귀
            $bus_line = "$busTitleName → 서울";
        }

		//==========================카카오 메시지 발송 ==========================
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
            , "couponseq"=> $couponseq
            , "bus_line"=> $bus_line
            , "day_start"=> $day_start
            , "day_return"=> $day_return
            , "link1"=> $link1 //예약
            , "link2"=> $link2 //패키지 안내링크
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
    }

	mysqli_query($conn, "COMMIT");
}else if($param == "busDatadel"){ //데이터 완전삭제
	$resnum = $_REQUEST["resnum"];

	$select_query = "DELETE FROM AT_RES_MAIN WHERE resnum = '$resnum'";
	$result_set = mysqli_query($conn, $select_query);

	$errmsg = $select_query;
	if(!$result_set) goto errGo;

	$select_query = "DELETE FROM AT_RES_SUB WHERE resnum = '$resnum'";
	$result_set = mysqli_query($conn, $select_query);

	$errmsg = $select_query;
	if(!$result_set) goto errGo;

	mysqli_query($conn, "COMMIT");

}else if($param == "busCancel"){ //버스 취소 안내
	$user_name = $_REQUEST["user_name"]; //이름
	$user_tel = $_REQUEST["user_tel"]; //전화번호
	$user_channel = $_REQUEST["user_channel"]; //예약채널
	$html_1 = $_REQUEST["html_1"]; //타이틀
	$html_2 = $_REQUEST["html_2"]; //안내

	$arryKakao = array();
	for($i = 1; $i < count($user_name); $i++){
		$userName = $user_name[$i];
		$userPhone = $user_tel[$i];
		$userchannel = $user_channel[$i];
		
		$channelText = "";
		$channelTitle = "";
		if($userchannel == "안내공지"){

		}else{
			if($userchannel == "프립"){
				$channelTitle = "액트립x프립 ";
				$channelText = "  - 영업일 기준으로 1일 이내에 취소완료 됩니다.";
			}else if($userchannel == "액트립"){
				$channelText = "  - 예약자명, 환불계좌를 채널톡으로 보내주세요~";
			}else if($userchannel == "클룩"){
				$channelTitle = "액트립x클룩 ";
				$channelText = "  - 영업일 기준으로 3일 이내에 취소완료 됩니다.";
			}else if($userchannel == "네이버쇼핑"){
				$channelText = "  - 영업일 기준으로 1일 이내에 취소완료 됩니다.";
			} 
			
			$channelText = "\n\n ▶ 안내사항\n". $channelText;
		}

		// $channelText .= '\n  - 왕복으로 예약하셔서 모두 취소 원하실 경우 알려주시면 같이 취소 진행하겠습니다.'
		// 			.'\n  - 이용에 불편드려 죄송합니다.';

		//==========================카카오 메시지 발송 ==========================
        $kakao_gubun = "bus_notice";
		$msgTitle = $html_1;
        $PROD_NAME = $html_1;
    
        $DebugInfo = array(
            "PROD_NAME" => $PROD_NAME
            , "PROD_TABLE" => "AT_RES_MAIN"
            , "PROD_TYPE" => $kakao_gubun
            , "RES_CONFIRM" => 4
            , "resnum" => $user_tel
        );
        $arrKakao = array(
            "gubun"=> $kakao_gubun
            , "userName"=> $userName
            , "userPhone"=> $userPhone
			, "channel"=> $channelTitle
			, "notice"=> $html_2.$channelText
            , "DebugInfo"=> $DebugInfo
        );

		$arryKakao[$i] = $arrKakao;
		//==========================카카오 메시지 발송 ==========================
	}

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

	mysqli_query($conn, "COMMIT");
}else if($param == "busKakaoInfo"){ //버스 카톡 안내
	$kakao_sDate = $_REQUEST["kakao_sDate"]; //시작일
	$kakao_eDate = $_REQUEST["kakao_eDate"]; //종료일
	$chkbusNum = $_REQUEST["chkbusNum_Kakao"]; //예약채널
	$chkResInfo = $_REQUEST["chkResInfo"]; //확정안내
	$html_1 = $_REQUEST["kakao_1"]; //상단안내
	$html_2 = $_REQUEST["kakao_2"]; //안내내용
	$html_3 = $_REQUEST["kakao_3"]; //안내내용

	$inResType = "";
	$TestChk = "";
    for($b = 0; $b < count($chkbusNum); $b++){
		if($chkbusNum[$b] == "테스트" && $chkResInfo == ""){
        	$TestChk = "테스트";
			break;
		}else{
			if($chkbusNum[$b] == "테스트" && $chkResInfo == "확정"){
				$TestChk = "테스트";
			}else{
				$inResType .= '"'.$chkbusNum[$b].'",';
			}
		}
    }

	$msgTitle = '액트립 서핑버스 공지사항';
	$arryKakao = array();
	$arryKakao2 = array();

	if($TestChk == "테스트" && $chkResInfo == ""){
		$userName = "테스트";
		$userPhone = "010-4437-0009";
		
		$arrKakao = array(
			"gubun"=> "bus"
			, "admin"=> "N"
			, "tempName"=> "at_res_step4"
			, "smsTitle"=> $msgTitle
			, "userName"=> $userName
			, "userPhone"=> $userPhone
			, "shopname"=> $html_1
			, "smsOnly"=>"N"
			, "PROD_NAME"=> $html_2
			, "PROD_URL"=> $html_3
			, "PROD_TYPE"=>"bus_kakaoinfo"
			, "RES_CONFIRM"=>"-1"
		);

		$arrRtn = sendKakao($arrKakao); //알림톡 발송

	}else{
		$inResType .= '"99"';

		$view_column = "a.user_tel, a.user_name";
		if($chkResInfo == "확정"){
			$view_column = "a.user_tel, a.user_name, a.resnum";
		}

		$select_query_sub = "SELECT ".$view_column." FROM AT_RES_MAIN a 
			INNER JOIN AT_RES_SUB b 
				on a.resnum = b.resnum 
			WHERE b.res_confirm = 3 
				AND (res_date BETWEEN CAST('".$kakao_sDate."' AS DATE) AND CAST('".$kakao_eDate."' AS DATE)) 
				AND b.res_busnum IN (".$inResType.")
			GROUP by ".$view_column;
		$resultSite = mysqli_query($conn, $select_query_sub);
		$count = mysqli_num_rows($resultSite);

		// echo "<br><br>쿼리 : ".$select_query_sub;
		// return;
		if($count == 0){
			return;
		}

		$i = 0;
		while ($row = mysqli_fetch_assoc($resultSite)){
			$userName = $row['user_name'];
			$userPhone = $row['user_tel'];
			
			//확정안내 카톡 발송
			if($chkResInfo == "확정"){
				$ResNumber = $row['resnum']; //예약번호

				if($TestChk == "테스트"){
					$userPhone = "010-4437-0009";
				}
				
				$select_query_list = "SELECT * FROM AT_RES_SUB WHERE resnum = $ResNumber ORDER BY res_date, ressubseq";
				$resultSite_list = mysqli_query($conn, $select_query_list);

				//echo "<br><br>쿼리 : ".$select_query_list;
				
				$busSeatInfoS = "";
				$busSeatInfoE = "";
				$arrSeatInfoS = array();
				$arrSeatInfoE = array();
				while ($rowSub = mysqli_fetch_assoc($resultSite_list)){
					$shopseq = $rowSub['seq'];
					$shopname = $rowSub['shopname'];
					$coupon = $rowSub['res_coupon'];
					$busGubun = substr($rowSub['res_bus'], 0, 1);

					// $pointTime = explode("|", fnBusPoint($rowSub['res_spointname'], $rowSub['res_bus']))[0];

					// if($busGubun == "Y" || $busGubun == "E"){ //양양, 동해
					// 	if(array_key_exists($rowSub['res_date'].$rowSub['res_busnum'], $arrSeatInfoS)){
					// 		$arrSeatInfoS[$rowSub['res_date'].$rowSub['res_busnum']] .= '      - '.$rowSub['res_seat'].'번 ('.$rowSub['res_spointname'].' / '.$pointTime.')\n';
					// 	}else{
					// 		$arrSeatInfoS[$rowSub['res_date'].$rowSub['res_busnum']] = '    ['.$rowSub['res_date'].'] '.fnBusNum($rowSub['res_busnum']).'\n      - '.$rowSub['res_seat'].'번 ('.$rowSub['res_spointname'].' / '.$pointTime.')\n';
					// 	}
					// }else{
					// 	if(array_key_exists($rowSub['res_date'].$rowSub['res_busnum'], $arrSeatInfoE)){
					// 		$arrSeatInfoE[$rowSub['res_date'].$rowSub['res_busnum']] .= '      - '.$rowSub['res_seat'].'번 ('.$rowSub['res_spointname'].' / '.$pointTime.')\n';
					// 	}else{
					// 		$arrSeatInfoE[$rowSub['res_date'].$rowSub['res_busnum']] = '    ['.$rowSub['res_date'].'] '.fnBusNum($rowSub['res_busnum']).'\n      - '.$rowSub['res_seat'].'번 ('.$rowSub['res_spointname'].' / '.$pointTime.')\n';
					// 	}
					// }
				}

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

				$busSeatInfo = $busSeatInfo;

				if($shopseq == 7){
					$busTypeY = "Y";
					$busTypeS = "S";
					$busTitleName = "양양";
					$resparam = "surfbus_yy";
				}else if($shopseq == 14){
					$busTypeY = "E";
					$busTypeS = "A";    
					$busTitleName = "동해";    
					$resparam = "surfbus_dh";	
				}
				$gubun_title = $busTitleName.' 서핑버스';
	
				$tempName = "frip_bus02"; //예약확정
				$btn_ResSearch = "orderview?num=1&resNumber=".$ResNumber; //예약조회
				$btn_ResChange = "pointchange?num=1&resNumber=".$ResNumber; //좌석/정류장 변경
				$btn_ResGPS = "surfbusgps"; //서핑버스 실시간위치 조회
				$btn_ResPoint = "pointlist?num=1&resNumber=".$ResNumber; //탑승시간/위치안내
				$btn_Notice = "";
				$btn_ResContent = ""; //예약 상세안내

				$msgInfo = $busSeatInfoTotal;

				//$userPhone = "0101234";
				// 고객 카카오톡 발송
				$msgTitle = '액트립 서핑버스 예약안내';
				$arrKakao = array(
					"gubun"=> "bus"
					, "admin"=> "N"
					, "tempName"=> $tempName
					, "smsTitle"=> $msgTitle
					, "userName"=> $userName
					, "userPhone"=> $userPhone
					, "msgType"=>$msgType
					, "shopname"=>$gubun_title
					, "MainNumber"=>$ResNumber
					, "msgInfo"=>$msgInfo
					, "btn_ResContent"=> $btn_ResContent
					, "btn_ResSearch"=> $btn_ResSearch
					, "btn_ResChange"=> $btn_ResChange
					, "btn_ResGPS"=> $btn_ResGPS
					, "btn_ResPoint"=> $btn_ResPoint
					, "btn_Notice"=> $btn_Notice
					, "smsOnly"=>"N"
					, "PROD_NAME"=>"서핑버스"
					, "PROD_URL"=>$shopseq
					, "PROD_TYPE"=>"bus_kakaoconfirm"
					, "RES_CONFIRM"=>"3"
				);

				if($i < 99){
					$arryKakao[$i] = $arrKakao;
				}else{					
					$arryKakao2[$i] = $arrKakao;
				}
				
				if($TestChk == "테스트"){
					break;
				}
				$i++;

			}else{
		
				//일반 공지 안내
				$arrKakao = array(
					"gubun"=> "bus"
					, "admin"=> "N"
					, "tempName"=> "at_res_step4"
					, "smsTitle"=> $msgTitle
					, "userName"=> $userName
					, "userPhone"=> $userPhone
					, "shopname"=> $html_1
					, "smsOnly"=>"N"
					, "PROD_NAME"=> $html_2
					, "PROD_URL"=> $html_3
					, "PROD_TYPE"=>"bus_kakaoinfo"
					, "RES_CONFIRM"=>"-1"
				);
		
				$arryKakao[$i] = $arrKakao;
				$i++;
			}
		}

		// //마지막 테스트 계정
		// $arrKakao = array(
		// 	"gubun"=> "bus"
		// 	, "admin"=> "N"
		// 	, "tempName"=> "at_res_step4"
		// 	, "smsTitle"=> $msgTitle
		// 	, "userName"=> "테스트"
		// 	, "userPhone"=> "010-4437-0009"
		// 	, "shopname"=> $html_1
		// 	, "smsOnly"=>"N"
		// 	, "PROD_NAME"=> $html_2
		// 	, "PROD_URL"=> $html_3
		// 	, "PROD_TYPE"=>"bus_kakaoinfo"
		// 	, "RES_CONFIRM"=>"-1"
		// );
		// $arryKakao[$i] = $arrKakao;
	
		//배열 발송
		$arrKakao = array(
			"arryData"=> $arryKakao
			, "array"=> "true"
		);
		$arrRtn = sendKakao($arrKakao); //알림톡 발송
		
		if(count($arryKakao2) > 0){
			//배열 발송
			$arrKakao = array(
				"arryData"=> $arryKakao2
				, "array"=> "true"
			);
			$arrRtn = sendKakao($arrKakao); //알림톡 발송
			
		}
	}

	mysqli_query($conn, "COMMIT");
}else if($param == "tempSeat"){ //좌석선점
	$tempSeatN = $_REQUEST["tempSeatN"]; //타채널 체크
	$tempSeatY = $_REQUEST["tempSeatY"]; //일반 체크

	$shopseq = $_REQUEST["shopseq"];
	$bus_date = $_REQUEST["bus_date"];
	$bus_gubun = $_REQUEST["bus_gubun"];
	$bus_num = $_REQUEST["bus_num"];

	$select_query = "DELETE FROM AT_RES_TEMP_SEAT WHERE shopseq = $shopseq 
						AND bus_date = '$bus_date'
						AND bus_gubun = '$bus_gubun'
						AND bus_num = '$bus_num'";
	$result_set = mysqli_query($conn, $select_query);

	//타채널 좌석 저장
	for($i = 0; $i < count($tempSeatN); $i++){
		$seat = $tempSeatN[$i];
		
		$select_query = "INSERT INTO `AT_RES_TEMP_SEAT`(`shopseq`, `bus_date`, `bus_gubun`, `bus_num`, `bus_seat`, `use_yn`, `insdate`) VALUES ($shopseq, '$bus_date', '$bus_gubun', '$bus_num', $seat, 'N', now())";
		$result_set = mysqli_query($conn, $select_query);
		
		if(!$result_set) goto errGo;
	}

	//일반 좌석 저장
	for($i = 0; $i < count($tempSeatY); $i++){
		$seat = $tempSeatY[$i];
		
		$select_query = "INSERT INTO `AT_RES_TEMP_SEAT`(`shopseq`, `bus_date`, `bus_gubun`, `bus_num`, `bus_seat`, `use_yn`, `insdate`) VALUES ($shopseq, '$bus_date', '$bus_gubun', '$bus_num', $seat, 'Y', now())";
		$result_set = mysqli_query($conn, $select_query);
		
		if(!$result_set) goto errGo;
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
