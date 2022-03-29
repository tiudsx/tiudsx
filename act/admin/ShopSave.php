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
$code = "surf";
$shopcharge = "0";
$account_yn = "N";
$category = "jukdo";
$categoryname = "죽도";
$shopname = "액트립 서핑패키지";
$tel_kakao = "010-3308-6080";
$tel_admin = "010-3308-6080";
$shopaddr = "강원도 양양군 현남면 인구중앙길 97";
$shoplat = "37.5782053";
$shoplng = "129.1155484";
$img_ver = 0;
$sub_title = "★ 즐거운 서핑. 저녁엔 신나는 바베큐파티!!@
★ 서핑 올인원 패키지로 한방에~!!";
$sub_tag = "5월 초특가 할인";
$sub_info = "서핑패키지|0|90000@숙박|숙박패키지|0|110000";
$imgurl = "https://surfenjoy.cdn3.cafe24.com/content/bbqmain.jpg";
$shop_img = "https://surfenjoy.cdn3.cafe24.com/content/bbqmain.jpg|https://surfenjoy.cdn3.cafe24.com/act_content/mango/slide_1.jpg|https://surfenjoy.cdn3.cafe24.com/act_content/mango/slide_2.jpg|https://surfenjoy.cdn3.cafe24.com/act_content/mango/slide_3.jpg|";
//상세설명 구분 : file / html
$content_type = "html";
$content = "<img src=\'https://surfenjoy.cdn3.cafe24.com/act_content/act_bbq/bbq_yy.jpg\' class=\'placeholder\' />";
$lesson_yn = "Y";
$rent_yn = "Y";
$bbq_yn = "Y";
$use_yn = "Y";
$view_yn = "Y";
$link_url = "";
$link_yn = "N";
$sell_cnt = 275;

/* 입점샵 은행 계좌 정보 */
$full_bankname = "우리"; //은행
$full_banknum = "890-010002-01-002"; //계좌번호
$full_bankuser = "이승철"; //예금주

//주문연동 계좌 정보
$bankname = ""; //은행
$banknum = ""; //계좌번호
$bankuser = ""; //예금주
$banklinkUse = "N"; //주문영동 여부

$debug_query = "";
/* 입점샵 은행계좌 입력 */
$select_query = "INSERT INTO `AT_PROD_BANKNUM`(`shopname`, `full_bankname`, `full_banknum`, `full_bankuser`, `bankname`, `banknum`, `bankuser`, `banklinkUse`, `etc`) VALUES ('$shopname', '$full_bankname', '$full_banknum', '$full_bankuser', '$bankname', '$banknum', '$bankuser', '$banklinkUse', '');";
$result_set = mysqli_query($conn, $select_query);
$debug_query .= '<br><br>AT_PROD_BANKNUM:'.$select_query;
if(!$result_set) goto errGo;

/* 입점샵 메인정보 입력 */
$select_query = "INSERT INTO `AT_PROD_MAIN` (`code`, `bankseq`, `shopcharge`, `account_yn`, `category`, `categoryname`, `shopname`, `tel_kakao`, `tel_admin`, `shopaddr`, `shoplat`, `shoplng`, `img_ver`, `sub_title`, `sub_tag`, `sub_info`, `shop_img`, `content_type`, `content`, `lesson_yn`, `rent_yn`, `bbq_yn`, `use_yn`, `view_yn`, `link_url`, `link_yn`, `sell_cnt`, `insuserid`, `insdate`, `upduserid`, `upddate`) VALUES
('$code', LAST_INSERT_ID(), '$shopcharge', '$account_yn', '$category', '$categoryname', '$shopname', '$tel_kakao', '$tel_admin', '$shopaddr', '$shoplat', '$shoplng', $img_ver, '$sub_title', '$sub_tag', '$sub_info', '$shop_img', '$content_type', '$content', '$lesson_yn', '$rent_yn', '$bbq_yn', '$use_yn', '$view_yn', '$link_url', '$link_yn', $sell_cnt, '$insuserid', '$datetime', '$insuserid', '$datetime');";
$result_set = mysqli_query($conn, $select_query);
$debug_query .= '<br><br>AT_PROD_MAIN:'.$select_query;
//echo $select_query.'<br><br>';
if(!$result_set) goto errGo;

$result = mysqli_query($conn, "select LAST_INSERT_ID() as identity");
$rowMain = mysqli_fetch_array($result);
$seq = $rowMain["identity"];

/* 입점샵 운영기간 입력 */
	$select_query = "INSERT INTO `AT_PROD_DAY` (`seq`, `ordernum`, `day_name`, `sdate`, `edate`, `day_week`, `week0`, `week1`, `week2`, `week3`, `week4`, `week5`, `week6`, `lesson_price`, `rent_price`, `stay_price`, `bbq_price`, `use_yn`) VALUES 
	($seq, 1, '비수기1', '2020-05-01', '2020-05-30', '0,1,2,3,4,5,6', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 0, 0, 0, 0, 'Y');";
	$result_set = mysqli_query($conn, $select_query);
	$debug_query .= '<br><br>AT_PROD_DAY:'.$select_query;
	//echo $select_query.'<br><br>';
	if(!$result_set) goto errGo;

/* 입점샵 옵션 입력 */
//lesson, rent, bbq
	$select_query = "INSERT INTO `AT_PROD_OPT` (`seq`, `optcode`, `optname`, `optsubname`, `opttime`, `opt_sexM`, `opt_sexW`, `opt_info`, `default_price`, `account_price`, `sell_price`, `shopcharge`, `use_yn`, `list_yn`, `peak_yn`, `stay_day`, `ordernum`) VALUES 
	($seq, 'pkg', '서핑패키지', '', '11시|13시|15시|', 9, 9, '입문강습+바베큐', 90000, 90000, 90000, 0, 'N', 'Y', 'N', -1, 1), 
	($seq, 'lesson', '숙박패키지', '', '11시|13시|15시|', 9, 9, '입문강습+숙박+바베큐', 110000, 110000, 110000, 0, 'N', 'Y', 'N', -1, 2);";
	$result_set = mysqli_query($conn, $select_query);
	$debug_query .= '<br><br>AT_PROD_OPT:'.$select_query;
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