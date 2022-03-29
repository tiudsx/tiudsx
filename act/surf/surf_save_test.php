<?php
include __DIR__.'/../db.php';
include __DIR__.'/../surf/surfkakao.php';
include __DIR__.'/../surf/surfmail.php';
include __DIR__.'/../surf/surffunc.php';


$datetime = date('Y/m/d H:i'); 
$TotalPrice = 0;
$coupon = "JOABUS1";
//쿠폰 코드가 있으면 예약확정
$coupon_array = array("JOABUS", "KLOOK", "NAVER", "FRIP", "MYTRIP");
if(in_array($coupon, $coupon_array))
{
    $res_confirm = 3;
    $InsUserID = $coupon;
}
else
{
    $res_confirm = 0;

    $couponseq = 8;
    //서핑버스 네이버예약 : 7, 네이버쇼핑 : 10, 프립 : 11, 마이리얼트립 : 12, 망고서프패키지 : 14
    if(in_array($couponseq, array(7, 10, 11, 12, 14)))
    {
        $res_confirm = 8;
        $InsUserID = $coupon;
    }
}


$gubun_title = $busTitleName.'서핑버스';
$msgChannelName = "";
$msgChannelName2 = '\n      - 예약취소는 예약하셨던 업체에서 접수가능합니다.';
$msgType = 1;

if($coupon == "JOABUS"){
    $gubun_title = "조아서프 패키지 서핑버스";
}else if($coupon == "KLOOK"){
    $gubun_title = "KLOOK 서핑버스";
}else if($coupon == "NAVER"){
    $gubun_title = "액트립 서핑버스";
}else if($coupon == "FRIP"){
    $gubun_title = "액트립 서핑버스";
}else if($coupon == "MYTRIP"){
    $gubun_title = "액트립 서핑버스";
}else{
    $gubun_title = $busTitleName.'서핑버스';
}



if($coupon == "JOABUS"){
    $gubun_title = "조아서프 패키지 서핑버스";
    $msgChannelName = "&조아서프";
    $msgChannelName2 = "";
}else if($coupon == "FRIP"){
    $gubun_title = "프립 서핑버스";
    $msgChannelName = '&프립';
}else if($coupon == "MYTRIP"){
    $gubun_title = "마이리얼트립 서핑버스";
    $msgChannelName = '&마이리얼트립';
}else if($coupon == "KLOOK"){
    $gubun_title = "클룩 서핑버스";
    $msgChannelName = '&클룩';
}else if($coupon == "NAVER"){

}else{
    //서핑버스 네이버예약 : 7, 네이버쇼핑 : 10, 프립 : 11, 마이리얼트립 : 12, 망고서프패키지 : 14
    if(in_array($couponseq, array(7, 10, 11, 12, 14))){
        $msgType = 0;
    }else{
        $msgType = 2;
    }
}

$msgTitle = '액트립'.$msgChannelName.' 서핑버스 예약안내';
$pointMsg = ' ▶ 탑승시간/위치 안내\n'.$busStopInfo;

if($msgType == 2){
    $kakaoMsg = $msgTitle.'\n안녕하세요. '.$userName.'님\n\n액트립 예약정보 [입금대기]\n ▶ 예약번호 : '.$ResNumber.'\n ▶ 예약자 : '.$userName.'\n ▶ 좌석안내\n'.$busSeatInfo.$pointMsg.$etcMsg.$totalPrice.'---------------------------------\n ▶ 안내사항\n      - 1시간 이내 미입금시 자동취소됩니다.\n\n ▶ 입금계좌\n      - 우리은행 / 1002-845-467316 / 이승철\n\n ▶ 문의\n      - http://pf.kakao.com/_HxmtMxl';
}else{
    $kakaoMsg = $msgTitle.'\n안녕하세요. '.$userName.'님 예약이 완료되었습니다. \n\n액트립 예약정보 [예약확정]\n ▶ 예약번호 : '.$ResNumber.'\n ▶ 예약자 : '.$userName.'\n ▶ 좌석안내\n'.$busSeatInfo.$pointMsg.$etcMsg.'---------------------------------\n ▶ 안내사항\n      - 좌석/정류장 변경은 하단 버튼에서 가능합니다.'.$msgChannelName2.'\n      - 이용일, 탑승시간, 탑승위치 꼭 확인 부탁드립니다.\n      - 탑승시간 5분전에는 도착해주세요~\n\n ▶ 문의\n      - http://pf.kakao.com/_HxmtMxl';
}

if($coupon == "JOABUS"){
    //조아서프 관리자 카카오톡 발송
    $arrKakao = array(
        "gubun"=> "bus"
        , "admin"=> "N"
        , "smsTitle"=> $msgTitle
        , "userName"=> $userName
        , "tempName"=> "at_bus_step1"
        , "kakaoMsg"=>$kakaoMsg
        //, "userPhone"=> "010-9509-9994"
        , "userPhone"=> "010-8368-6099"
        , "link1"=>"orderview?num=1&resNumber=".$ResNumber //예약조회/취소
        , "link2"=>"pointchange?num=1&resNumber=".$ResNumber //예약조회/취소
        , "link3"=>"surfbusgps" //셔틀버스 실시간위치 조회
        , "link4"=>"pointlist?resparam=".$resparam //셔틀버스 탑승 위치확인
        , "link5"=>"event" //공지사항
        , "smsOnly"=>"N"
        , "PROD_NAME"=>"서핑버스_조아서프"
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
}

// 고객 카카오톡 발송
$arrKakao = array(
    "gubun"=> "bus"
    , "admin"=> "N"
    , "smsTitle"=> $msgTitle
    , "userName"=> $userName
    , "tempName"=> "at_bus_step1"
    , "kakaoMsg"=>$kakaoMsg
    , "userPhone"=> $userPhone
    , "link1"=>"orderview?num=1&resNumber=".$ResNumber //예약조회/취소
    , "link2"=>"pointchange?num=1&resNumber=".$ResNumber //예약조회/취소
    , "link3"=>"surfbusgps" //셔틀버스 실시간위치 조회
    , "link4"=>"pointlist?resparam=".$resparam //셔틀버스 탑승 위치확인
    , "link5"=>"event" //공지사항
    , "smsOnly"=>"N"
    , "PROD_NAME"=>"서핑버스"
    , "PROD_URL"=>$shopseq
    , "PROD_TYPE"=>"bus"
    , "RES_CONFIRM"=>$res_confirm
);

//서핑버스 네이버예약 : 7, 네이버쇼핑 : 10, 프립 : 11, 마이리얼트립 : 12 알림톡 제외
if($msgType > 0){
    $arrRtn = sendKakao($arrKakao); //알림톡 발송

    // 카카오 알림톡 DB 저장 START
    $select_query = kakaoDebug($arrKakao, $arrRtn);
    $result_set = mysqli_query($conn, $select_query);
    if(!$result_set) goto errGo;
    // 카카오 알림톡 DB 저장 END
}
?>
