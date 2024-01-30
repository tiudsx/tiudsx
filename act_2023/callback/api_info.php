<?php
include __DIR__.'/../common/db.php';
include __DIR__.'/../common/kakaoalim.php';
include __DIR__.'/../common/channel_kakaoalim.php';
include __DIR__.'/../common/func.php';


$http_origin = $_SERVER['HTTP_ORIGIN']; 
if ($http_origin == "https://www.wairi.co.kr" || $http_origin == "http://www.mohaeng.co.kr" || $http_origin == "http://www.landingko.com") { 
    header("Access-Control-Allow-Origin: $http_origin"); 
}

header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Accept, Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization");
header("Content-type:text/html;charset=utf-8");

header('Content-Type: application/json');

$param = $_REQUEST["param"];
$type = $_REQUEST["type"];

mysqli_query($conn, "SET AUTOCOMMIT=0");
mysqli_query($conn, "BEGIN");

$rtn_data = array();
$success = false;

if($param == "mohaeng1"){

    if($type == "bus_kakao1"){
        //모행 파라미터
        $resbus = "DH"; //버스 노선
        $reschannel = "31"; //모행 코드
        $busgubun = "1"; //왕복 여부 / 1 : 1박 왕복   2 : 당일 왕복   3 : 편도
        
        $userName = $_REQUEST["username"];
        $userPhone = $_REQUEST["userphone"];
        $resDate1 = $_REQUEST["resDate"];
        $resDate2 = $NextDate = date("Y-m-d", strtotime($resDate1." +1 day"));;
        $resbusseat1 = $_REQUEST["resbusseat"];
        $resbusseat2 = $resbusseat1;

        if($userName == "" || $userPhone == "" || $resDate1 == "" || $resDate2 == "" || $resbusseat1 == "" || $resbusseat2 == ""){

            $data = array(
                'username' => $userName
                ,'userphone' => $userPhone
                ,'resDate1' => $resDate1
                ,'resDate2' => $resDate2
                ,'resbusseat1' => $resbusseat1
                ,'resbusseat1' => $resbusseat2
            );
            
            $rtn_data = $data;
            goto errGo;
        }

        //쿠폰코드 생성
        $coupon_code = RandString(5);
        $user_ip = $_SERVER['REMOTE_ADDR'];
        $add_date = date("Y-m-d");
    
        $seatName = "출발";
        $seatName2 = "동해";

        $resseatMsg = "";
        if($resbusseat1 > 0){ //양양행,동해행 좌석예약
            $resseatMsg = "\n    [$seatName] ".$resDate1." / ".$resbusseat1."자리";
        }
    
        if($resbusseat2 > 0){ //서울행 좌석예약
            $resseatMsg .= "\n    [복귀] ".$resDate2." / ".$resbusseat2."자리";
        }
    
        if($reschannel == 31){ //모행
            $link1 = "surfbus_res?param=".urlencode(encrypt(date("Y-m-d").'|'.$coupon_code.'|resbus|'.$resDate1.'|'.$resDate2.'|'.$resbusseat1.'|'.$resbusseat2.'|'.$userName.'|'.$userPhone.'|'.$resbus.'|'.$reschannel.'|'));
            
            $data = array(
                'name' => $userName,
                'shop_name' => '모행 셔틀버스',
                'reservation_name' => $userName,
                'receiver_number' => $userPhone,
                'url' => $link1,
                'seat' => $resseatMsg
            );
            
            fnKakaoSend(at_config('acrtip_reservation',$data));
        }

        //------- 쿠폰코드 입력 -----
        $data = json_decode($arrRtn[0], true);
        $kakao_code = $data[0]["code"];
        $kakao_type = $data[0]["data"]["type"];
        $kakao_msgid = $data[0]["data"]["msgid"];
        $kakao_message = $data[0]["message"];
        $kakao_originMessage = $data[0]["originMessage"];
    
        $userinfo = "$userName|$userPhone|$resDate1|$resbusseat1|$resDate2|$resbusseat2|$kakao_code|$kakao_type|$kakao_message|$kakao_originMessage|$kakao_msgid|$resbus|$reschannel";
        $select_query = "INSERT INTO `AT_COUPON_CODE` (`couponseq`, `coupon_code`, `seq`, `use_yn`, `add_ip`, `add_date`, `insdate`, `userinfo`, `etc`) VALUES ('$reschannel', '$coupon_code', 'BUS', 'N', '$user_ip', '$add_date', now(), '$userinfo', '$link1');";
        $result_set = mysqli_query($conn, $select_query);
         if(!$result_set) goto errGo;
        //------- 쿠폰코드 입력 -----
    
        // 카카오 알림톡 DB 저장 START
        $select_query = kakaoDebug($arrKakao, $arrRtn);            
        $result_set = mysqli_query($conn, $select_query);
        // 카카오 알림톡 DB 저장 END
    
        mysqli_query($conn, "COMMIT");
        $success = true;

        // $method = "POST";

        // $data = array(
        //     'resparam' => "reskakao"
        //     ,'username' => $userName
        //     ,'userphone' => $userPhone
        //     ,'resDate1' => $resDate1
        //     ,'resDate2' => $resDate2
        //     ,'resbusseat1' => $resbusseat1
        //     ,'resbusseat1' => $resbusseat2
        //     ,'resbus' => $resbus
        //     ,'reschannel' => $reschannel
        //     ,'busgubun' => $busgubun
        // );
        // $url = "https://actrip.co.kr/act_2023/admin/bus_mohaeng/list_save.php";
        
        // $ch = curl_init();                                 //curl 초기화
        // curl_setopt($ch, CURLOPT_URL, $url);               //URL 지정하기
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    //요청 결과를 문자열로 반환 
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        // curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);      //connection timeout 10초 
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   //원격 서버의 인증서가 유효한지 검사 안함
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));       //POST data
        // curl_setopt($ch, CURLOPT_POST, true);              //true시 post 전송 
         
        // $response = curl_exec($ch);
        // $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // $error = curl_error($ch);
        // curl_close($ch);
         
        // $rtn_data = json_encode($data, JSON_UNESCAPED_UNICODE);
        // $success = true;

        // $curl = curl_init();

        // $rtnMsg = json_encode($data, JSON_UNESCAPED_UNICODE);
        
        // curl_setopt_array($curl, array(
        //   CURLOPT_URL => "https://actrip.co.kr/act_2023/admin/bus_mohaeng/list_save.php",
        //   CURLOPT_RETURNTRANSFER => true,
        //   CURLOPT_ENCODING => "",
        //   CURLOPT_MAXREDIRS => 10,
        //   CURLOPT_TIMEOUT => 30,
        //   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //   CURLOPT_CUSTOMREQUEST => "GET",
        //   CURLOPT_POSTFIELDS => $rtnMsg,
        //   CURLOPT_HTTPHEADER => array(
        //     "content-type: application/json", "Accept: application/json"
        //   ),
        // ));
    
        // $response = curl_exec($curl);
        // $err = curl_error($curl);
    
        // curl_close($curl);
    
        // $rtn_data = array(
        //     'response' => $response
        //     ,'err' => $err
        // );
    }
    
    // if(!$result_set) goto errGo;
}


if(!$success){
	errGo:
	mysqli_query($conn, "ROLLBACK");
    $success_code = "에러";
}else{
    $success_code = "성공";
}

$data = array(
    'success' => $success_code,
    'content' => $rtn_data
);

$output = json_encode($data, JSON_UNESCAPED_UNICODE);
echo urldecode($output);

mysqli_close($conn);
?>