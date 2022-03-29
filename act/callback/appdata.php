<?php
header('Content-Type: application/json');
$groupData = array();

$arrInfo = array();
$arrInfo[] = array("imgurl" =>'https://actrip.co.kr/act/images/banner/banefit.jpg', "link" => 'https://cafe.naver.com/actrip', "useyn" => 'Y');
$arrInfo[] = array("imgurl" =>'https://actrip.co.kr/act/images/banner/reviewSurf.jpg', "link" => 'https://cafe.naver.com/actrip', "useyn" => 'Y');
$arrInfo[] = array("imgurl" =>'https://actrip.co.kr/act/images/banner/reviewBus.jpg', "link" => 'https://cafe.naver.com/actrip', "useyn" => 'Y');

$groupData['main_swiper'] = $arrInfo;

$arrTip = array();
$arrTip[] = array("imgurl" =>'https://actrip.co.kr/act/images/mainImg/mainEvent.png', "link" => 'https://cafe.naver.com/actrip', "useyn" => 'Y');
$arrTip[] = array("imgurl" =>'https://actrip.co.kr/act/images/mainImg/mainFood.png', "link" => 'https://cafe.naver.com/actrip', "useyn" => 'Y');
$arrTip[] = array("imgurl" =>'https://actrip.co.kr/act/images/mainImg/mainReview.png', "link" => 'https://cafe.naver.com/actrip', "useyn" => 'Y');
$arrTip[] = array("imgurl" =>'https://actrip.co.kr/act/images/mainImg/mainSurf.png', "link" => 'https://cafe.naver.com/actrip', "useyn" => 'Y');
$groupData['main_tip'] = array("titlename" => "알면 알수록 좋은~", "data" => $arrTip);

$arrTag = array();
$arrTag[] = array("text" =>'#액트립', "link" => 'https://cafe.naver.com/actrip', "useyn" => 'Y');
$arrTag[] = array("text" =>'#여행은액티비티다', "link" => 'https://cafe.naver.com/actrip', "useyn" => 'Y');
$arrTag[] = array("text" =>'#혜택빵빵', "link" => 'https://cafe.naver.com/actrip', "useyn" => 'Y');
$groupData['main_hashtag'] = $arrTag;

$groupData['main_banner'] = array("imgurl" => 'https://actrip.co.kr/act/images/banner/bnrBus.jpg', "link" => 'https://actrip.co.kr/surfbus', "useyn" => 'Y');

$arrPlan = array();
$arrPlan[] = array("imgurl" =>'https://actrip.co.kr/act/images/mainImg/01.jpg', "link" => 'https://actrip.co.kr/surf', "useyn" => 'Y');
$arrPlan[] = array("imgurl" =>'https://actrip.co.kr/act/images/mainImg/02.jpg', "link" => 'https://actrip.co.kr/bbq', "useyn" => 'Y');
$arrPlan[] = array("imgurl" =>'https://actrip.co.kr/act/images/mainImg/03.jpg', "link" => 'https://actrip.co.kr/surfbus', "useyn" => 'Y');
$arrPlan[] = array("imgurl" =>'https://actrip.co.kr/act/images/mainImg/04.jpg', "link" => 'https://actrip.co.kr/eatlist', "useyn" => 'Y');

$groupData['main_plan'] = array("titlename" => "기획전", "data" => $arrPlan);
// if($groupData[$row["busgubun"].$row["busjson"]] == null){
//     $groupData[$row["busgubun"].$row["busjson"]] = array($arrInfo);
// }else{
//     $groupData[$row["busgubun"].$row["busjson"]][] = $arrInfo;
// }

$output = json_encode($groupData, JSON_UNESCAPED_UNICODE);
echo urldecode($output);
?>