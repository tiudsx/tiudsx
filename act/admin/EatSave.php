<?php
include __DIR__.'/../db.php';

$success = true;
$insuserid = "admin";
$datetime = date('Y/m/d H:i'); 

mysqli_query($conn, "SET AUTOCOMMIT=0");
mysqli_query($conn, "BEGIN");

/*
code
  - surf : 서핑샵
  - bbqparty : 바베큐파티
  - bus : 서틀버스
  - stay : 숙소
  - eat : 맛집
  - act : 액티비티
*/
$code = "eat";
$shopcharge = "0";
$account_yn = "N";
$category = "eatjukdo";
$categoryname = "죽도";
$shopname = "다락";
$tel_kakao = "033-672-5200";
$tel_admin = $tel_kakao;
$shopaddr = "현남면 인구중앙길 95";
$shoplat = "37.9729991";
$shoplng = "128.7591720";
$img_ver = 0;
$sub_title = "막국수";
$sub_tag = "한식@배달가능";
$sub_info = "쿠폰제공시 1인 1음료 제공";
$imgurl = "https://actrip.cdn1.cafe24.com/eat/";
$shop_img = $imgurl."thumfood.jpg";
//상세설명 구분 : file / html
$content_type = "html";
$content = "";
$lesson_yn = "N";
$rent_yn = "N";
$bbq_yn = "N";
$use_yn = "Y";
$view_yn = "Y";
$link_url = "";
$link_yn = "N";
$sell_cnt = 0;

/* 입점샵 은행 계좌 정보 */
$full_bankname = ""; //은행
$full_banknum = ""; //계좌번호
$full_bankuser = ""; //예금주

//주문연동 계좌 정보
$bankname = ""; //은행
$banknum = ""; //계좌번호
$bankuser = ""; //예금주
$banklinkUse = "N"; //주문영동 여부

$debug_query = "";

/* 입점샵 메인정보 입력 */
$select_query = "INSERT INTO `AT_PROD_MAIN` (`code`, `bankseq`, `shopcharge`, `account_yn`, `category`, `categoryname`, `shopname`, `tel_kakao`, `tel_admin`, `shopaddr`, `shoplat`, `shoplng`, `img_ver`, `sub_title`, `sub_tag`, `sub_info`, `shop_img`, `content_type`, `content`, `lesson_yn`, `rent_yn`, `bbq_yn`, `use_yn`, `view_yn`, `link_url`, `link_yn`, `sell_cnt`, `insuserid`, `insdate`, `upduserid`, `upddate`, `shop_resinfo`, `shop_option`) VALUES
('$code', LAST_INSERT_ID(), '$shopcharge', '$account_yn', '$category', '$categoryname', '$shopname', '$tel_kakao', '$tel_admin', '$shopaddr', '$shoplat', '$shoplng', $img_ver, '$sub_title', '$sub_tag', '$sub_info', '$shop_img', '$content_type', '$content', '$lesson_yn', '$rent_yn', '$bbq_yn', '$use_yn', '$view_yn', '$link_url', '$link_yn', $sell_cnt, '$insuserid', '$datetime', '$insuserid', '$datetime', '', '');";
$result_set = mysqli_query($conn, $select_query);
$debug_query .= '<br><br>AT_PROD_MAIN:'.$select_query;
//echo $select_query.'<br><br>';
if(!$result_set) goto errGo;


if(!$success){
	errGo:
	mysqli_query($conn, "ROLLBACK");
	echo $debug_query;
	echo '<script>alert("예약진행 중 오류가 발생하였습니다.");</script>';
}else{
	mysqli_query($conn, "COMMIT");

	echo '<script>alert("완료되었습니다.");</script>';
}
?>