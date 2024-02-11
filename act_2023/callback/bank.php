<?php
include __DIR__.'/../common/db.php';
include __DIR__.'/../common/kakaoalim.php';
include __DIR__.'/../common/func.php';

$success = true;
$datetime = date('Y/m/d H:i'); 

$content = trim($_REQUEST["content"]);
$keyword = trim($_REQUEST["keyword"]);

mysqli_query($conn, "SET AUTOCOMMIT=0");
mysqli_query($conn, "BEGIN");

if($content == "" || $keyword == ""){
	return;
}

// 문자내역
$content = preg_replace('/\s+/u', ' ', $content);
$arrSMS = explode("@", $content);

// 은행명
$bankname = explode("@", $keyword)[0];

//관리자 이메일 주소
$to = "lud1@naver.com";
//$to = "lud1@naver.com";

if($bankname == "신한"){
	//ex : https://actrip.co.kr/act_2023/callback/bank.php?content=[Web발신]@신한 04/09 14:20@389-02-188735@입금 76000@이승철&keyword=신한@입금
	//[Web발신]@신한 04/09 14:20@389-02-188735@입금         34@이승철
	$banknum = $arrSMS[2];

	$bankprice = explode(" ", $arrSMS[3])[1];
	$bankprice = str_replace(',', '', $bankprice);

	$bankuser = $arrSMS[4];
}else if($bankname == "우리"){
	//ex : https://actrip.co.kr/act_2023/callback/bank.php?content=[Web발신]@우리 04/05 19:05@*467316@입금 100원@이승철&keyword=우리@입금
	//[Web발신]@우리 06/08 08:58@8900*01@입금 200원@이승철
	//[Web발신]@우리 04/05 19:05@*467316@입금 100원@이승철

	$bankprice = explode(" ", $arrSMS[3])[1];
	$bankprice = str_replace(',', '', $bankprice);
	$bankprice = str_replace('입금', '', $bankprice);
	$bankprice = str_replace('원', '', $bankprice);

	$banknum = $arrSMS[2];
	$bankuser = $arrSMS[4];
}else{
	return;
}

$select_query = "INSERT INTO `AT_CALL_BANK`(`smscontent`, `keyword`, `shopSeq`, `bankprice`, `bankname`, `banknum`, `bankuser`, `insdate`) VALUES ('$content', '$keyword', 0, '$bankprice', '$bankname', '$banknum', '$bankuser', now())";
$result_set = mysqli_query($conn, $select_query);
$seq = mysqli_insert_id($conn);

$errCode = "01";
if(!$result_set) goto errGo;
//echo $select_query.'<br>';

//주문내역과 문자내역의 이름/금액 매칭
$select_query = "SELECT a.user_name, a.user_tel, a.user_email, a.etc, b.* 
					FROM AT_RES_MAIN as a INNER JOIN (SELECT resnum, SUM(res_totalprice) as price, MAX(seq) as shopSeq, MAX(shopname) as shopname, MAX(code) as code FROM AT_RES_SUB WHERE res_confirm IN (0, 1) GROUP BY resnum) as b 
						ON a.resnum = b.resnum 
					WHERE price = $bankprice AND a.user_name = '$bankuser'";
$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);

// 주문내역과 문자입금 내역 매칭이 맞을경우...
if($count == 1){
	while ($row = mysqli_fetch_assoc($result_setlist)){
		$ResNumber = $row['resnum'];
		$userName = $row["user_name"];
		$etc = $row["etc"];
		$userPhone = $row["user_tel"];
		$usermail = $row["user_email"];
		$shopseq = $row["shopSeq"];
		$code = $row['code'];
	}

	$select_query_sub = 'SELECT * FROM AT_RES_SUB WHERE res_confirm IN (0, 1) AND resnum = '.$ResNumber.' ORDER BY res_date, ressubseq';
	$resultSite = mysqli_query($conn, $select_query_sub);

	if($code == "bus"){
		$res_confirm = 3;
		$busSeatInfoS = "";
		$busSeatInfoE = "";
		$ressubseq = "";
		
		$arrSeatInfoS = array();
		$arrSeatInfoE = array();

		$day_start = "-";
		$day_return = "-";
		while ($rowSub = mysqli_fetch_assoc($resultSite)){
			$ressubseq .= $rowSub['ressubseq'].',';
	
			$res_date = $row["res_date"];

			$bus_oper = $row["bus_oper"];
			$bus_gubun = $row["bus_gubun"];
			$bus_num = $row["bus_num"];
	
			$arrBus = fnBusNum2023($bus_gubun[$i].$bus_num[$i]);
			$bus_line = '['.$res_date.'] '.$arrBus["point"].' '.$arrBus["num"];
			if($bus_oper == "start"){ //서울 출발
				$day_start = $bus_line;
			}else{ //복귀
				$day_return = $bus_line;
			}
		}
		
		$ressubseq .= '0';
		
		$link1 = shortURL("https://actrip.co.kr/orderview?num=1&resNumber=".$ResNumber);

		if($shopseq == 7){
			$busTitleName = "양양"; 
		}else if($shopseq == 14){
			$busTitleName = "동해";    
		}

        if($day_start != "-" && $day_return != "-"){ //왕복
            $bus_line = "서울 ↔ $busTitleName";
        }else if($day_start != "-"){ //서울 출발
            $bus_line = "서울 → $busTitleName";
        }else{ //서울 복귀
            $bus_line = "$busTitleName → 서울";
        }
		
        $kakao_gubun = "bus_confirm";
        $msgTitle = '액트립 셔틀버스 확정안내';
		$PROD_NAME = "셔틀버스 예약확정";

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
            
			$errCode = "02";
            if(!$result_set) goto errGo;
        }
		
		// 이메일 발송
		//$to = "lud1@naver.com";
		if(strrpos($usermail, "@") > 0){
            $to .= ','.$usermail;
		}

		$info1_title = "좌석안내";
        $info1 = str_replace('      -', '&nbsp;&nbsp;&nbsp;-', str_replace('\n', '<br>', $busSeatInfo));
        $info2_title = "탑승시간/위치 안내";			
		$info2 = "&nbsp;&nbsp;&nbsp;<a href=\"https://actrip.co.kr/pointlist\" target=\"_blank\" style=\"text-decoration:underline;color:#009e25\" rel=\"noreferrer noopener\">[안내사항 보기]</a>";

		$arrMail = array(
			"gubun"=> "bus"
			, "gubun_step" => 3
			, "gubun_title" => $shopname
            , "mailto"=> $to
			, "mailfrom"=> "surfbus_res@actrip.co.kr"
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

	}

	$select_query = "UPDATE `AT_RES_SUB` 
						SET res_confirm = $res_confirm
							,upddate = now()
							,confirmdate = now()
							,upduserid = 'autobank'
						WHERE ressubseq IN (".$ressubseq.")";
	// $result_set = mysqli_query($conn, $select_query);
	// if(!$result_set) goto errGo;
	
	$select_query = "INSERT INTO `AT_CALL_BANK_HISTORY`(`smscontent`, `keyword`, `shopSeq`, `goodstype`, `bankprice`, `bankname`, `banknum`, `bankuser`, `MainNumber`, `insdate`) VALUES ('$content', '$keyword', $shopseq, '$code', '$bankprice', '$bankname', '$banknum', '$bankuser', $ResNumber, now())";
	$result_set = mysqli_query($conn, $select_query);
	echo $select_query;
	$errCode = "03";
	if(!$result_set) goto errGo;

	$select_query = "DELETE FROM `AT_CALL_BANK` WHERE seq = $seq";
	$result_set = mysqli_query($conn, $select_query);
	$errCode = "04";
	if(!$result_set) goto errGo;

}else if($count > 1){ //같은 금액, 같은 이름 2명 이상
	$ResNumberList = "";
	
	$mailcontent = "은행명 : $bankname<br>계좌번호 : $banknum<br>입금자명 : $bankuser<br>금액 : ".number_format($bankprice)."원<br>";
	while ($row = mysqli_fetch_assoc($result_setlist)){
		$ResNumber = $row['resnum'];
		$userName = $row["user_name"];
		$etc = $row["etc"];
		$userPhone = $row["user_tel"];
		$usermail = $row["user_email"];
		$shopseq = $row["shopSeq"];
		$shopname = $row["shopname"];
		$code = $row['code'];
		
		$mailcontent .= $shopname.'('.$shopseq.') / 주문번호 : '.$ResNumber.' / 이름 : '.$bankuser.' / 금액 : '.number_format($bankprice).'원<br>';
		$ResNumberList .= $ResNumber."|";

		$select_query = "UPDATE `AT_RES_SUB` 
							SET res_confirm = 1
								,upddate = now()
								,upduserid = 'autobank_over'
							WHERE resnum = '$ResNumber' AND res_confirm = 0";
		$result_set = mysqli_query($conn, $select_query);
		if(!$result_set) goto errGo;
	}

	$select_query = "UPDATE `AT_CALL_BANK` SET goodstype = '$code', MainNumberList = '$ResNumberList', shopSeq = $shopseq WHERE seq = ".$seq;
	$result_set = mysqli_query($conn, $select_query);
	if(!$result_set) goto errGo;

	// 이메일 발송
	//$to = "lud1@naver.com";
	
	$info1_title = "";
	$info1 = "";
	$info2_title = "";
	$info2 = "";

	$arrMail = array(
		"gubun"=> "bank"
		, "gubun_step" => 0
		, "gubun_title" => "액트립 입금처리 오류"
		, "mailto"=> $to
		, "mailfrom"=> "surfbus_bank@actrip.co.kr"
		, "mailname"=> "actrip"
		, "userName"=> "관리자"
		, "ResNumber"=> ""
		, "userPhone" => ""
		, "etc" => $mailcontent
		, "totalPrice1" => ""
		, "totalPrice2" => ""
		, "banknum" => ""
		, "info1_title"=> $info1_title
		, "info1"=> $info1
		, "info2_title"=> $info2_title
		, "info2"=> $info2
	);

	sendMail($arrMail); //메일 발송

}else if ($count == 0){ //금액, 이름이 없을 경우
	//주문내역과 문자내역의 이름/금액 매칭
	$select_query = "SELECT a.user_name, a.user_tel, a.user_email, a.etc, b.* 
	FROM AT_RES_MAIN as a INNER JOIN (SELECT resnum, SUM(res_totalprice) as price, MAX(seq) as shopSeq, MAX(shopname) as shopname, MAX(code) as code FROM AT_RES_SUB WHERE res_confirm = 0 GROUP BY resnum) as b 
		ON a.resnum = b.resnum 
	WHERE price = $bankprice";
	$result_setlist = mysqli_query($conn, $select_query);
	$count = mysqli_num_rows($result_setlist);

	$ResNumberList = "";
	$noChk = $count;
	$mailcontent = "은행명 : $bankname<br>계좌번호 : $banknum<br>입금자명 : $bankuser<br>금액 : ".number_format($bankprice)."원<br>";

	// 주문내역과 문자내역 중 금액이 같은 경우 임시 체크
	if($count > 0){
		$ResNumberList = "Price:";
		while ($row = mysqli_fetch_assoc($result_setlist)){
			$ResNumber = $row['resnum'];
			$shopname = $row['shopname'];
			$shopseq = $row['shopSeq'];
			
			$mailcontent .= $shopname.'('.$shopseq.') / 주문번호 : '.$ResNumber.'<br>';
			$ResNumberList .= $ResNumber."|";
	
			$select_query = "UPDATE `AT_RES_SUB` 
								SET res_confirm = 1
									,upddate = now()
									,upduserid = 'autobank_none'
								WHERE resnum = '$ResNumber' AND res_confirm = 0";
			$result_set = mysqli_query($conn, $select_query);
			if(!$result_set) goto errGo;
		}

		// 이메일 발송
		//$to = "lud1@naver.com";

		$info1_title = "";
		$info1 = "";
		$info2_title = "";
		$info2 = "";

		$arrMail = array(
			"gubun"=> "bank"
			, "gubun_step" => 1
			, "gubun_title" => "액트립 입금처리 오류"
			, "mailto"=> $to
			, "mailfrom"=> "surfbus_bank@actrip.co.kr"
			, "mailname"=> "actrip"
			, "userName"=> "관리자"
			, "ResNumber"=> ""
			, "userPhone" => ""
			, "etc" => $mailcontent
			, "totalPrice1" => ""
			, "totalPrice2" => ""
			, "banknum" => ""
			, "info1_title"=> $info1_title
			, "info1"=> $info1
			, "info2_title"=> $info2_title
			, "info2"=> $info2
		);

		sendMail($arrMail); //메일 발송
	}else{
		$select_query = "SELECT a.user_name, a.user_tel, a.user_email, a.etc, b.* 
		FROM AT_RES_MAIN as a INNER JOIN (SELECT resnum, SUM(res_totalprice) as price, MAX(seq) as shopSeq, MAX(shopname) as shopname, MAX(code) as code FROM AT_RES_SUB WHERE res_confirm = 0 GROUP BY resnum) as b 
			ON a.resnum = b.resnum 
		WHERE a.user_name = '$bankuser'";
		$result_setlist = mysqli_query($conn, $select_query);
		$count = mysqli_num_rows($result_setlist);

		$noChk += $count;
		if($count > 0){
			$ResNumberList = "Name:";
			while ($row = mysqli_fetch_assoc($result_setlist)){
				$ResNumber = $row['resnum'];
				$shopname = $row['shopname'];
				$shopseq = $row['shopSeq'];

				$mailcontent .= $shopname.'('.$shopseq.') / 주문번호 : '.$ResNumber.'<br>';
				$ResNumberList .= $ResNumber."|";
		
				$select_query = "UPDATE `AT_RES_SUB` 
									SET res_confirm = 1
										,upddate = now()
										,upduserid = 'autobank_none'
									WHERE resnum = '$ResNumber' AND res_confirm = 0";
				$result_set = mysqli_query($conn, $select_query);
				if(!$result_set) goto errGo;
			}
			
			// 이메일 발송
			//$to = "lud1@naver.com";
			
			$info1_title = "";
			$info1 = "";
			$info2_title = "";
			$info2 = "";

			$arrMail = array(
				"gubun"=> "bank"
				, "gubun_step" => 2
				, "gubun_title" => "액트립 입금처리 오류"
				, "mailto"=> $to
				, "mailfrom"=> "surfbus_bank@actrip.co.kr"
				, "mailname"=> "actrip"
				, "userName"=> "관리자"
				, "ResNumber"=> ""
				, "userPhone" => ""
				, "etc" => $mailcontent
				, "totalPrice1" => ""
				, "totalPrice2" => ""
				, "banknum" => ""
				, "info1_title"=> $info1_title
				, "info1"=> $info1
				, "info2_title"=> $info2_title
				, "info2"=> $info2
			);

			sendMail($arrMail); //메일 발송
		}
	}

	// 금액, 이름동일 정보 하나도 없음
	if($noChk == 0){
		// 이메일 발송
		//$to = "lud1@naver.com";
		
		$info1_title = "";
		$info1 = "";
		$info2_title = "";
		$info2 = "";

		$arrMail = array(
			"gubun"=> "bank"
			, "gubun_step" => 3
			, "gubun_title" => "액트립 입금처리 오류"
			, "mailto"=> $to
			, "mailfrom"=> "surfbus_bank@actrip.co.kr"
			, "mailname"=> "actrip"
			, "userName"=> "관리자"
			, "ResNumber"=> ""
			, "userPhone" => ""
			, "etc" => $mailcontent
			, "totalPrice1" => ""
			, "totalPrice2" => ""
			, "banknum" => ""
			, "info1_title"=> $info1_title
			, "info1"=> $info1
			, "info2_title"=> $info2_title
			, "info2"=> $info2
		);

		sendMail($arrMail); //메일 발송
	}

	$select_query = "UPDATE `AT_CALL_BANK` SET goodstype = 'none', MainNumberList = '$ResNumberList' WHERE seq = ".$seq;
	$result_set = mysqli_query($conn, $select_query);
	// echo $select_query;
	if(!$result_set) goto errGo;
}

if(!$success){
	errGo:
	mysqli_query($conn, "ROLLBACK");
	echo $errCode;
}else{
	mysqli_query($conn, "COMMIT");
	echo '0';
}
mysqli_close($conn);
?>

