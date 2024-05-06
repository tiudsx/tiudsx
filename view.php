<?
$to      = '받는사람@gmail.com';
$subject = 'html을 보냈습니다.';
$fp = fopen('./act_2023/common/mail.html',"r");
$message = fread($fp,filesize('./act_2023/common/mail.html'));


$gubun_title = "test";
$gubun_title1 = "test";
$gubun_title2 = "test";
$gubun_title3 = "test";

$userName = "test";
$userPhone = "test";
$ResNumber = "test";

$info1_title = "test";
$info2_title = "test";
$info1 = "test";
$info2 = "test";
$info3 = "test";

$etc = "test";
$banknum = "test";

$totalinfo = "test";
$totalPrice1 = "test";
$totalPrice2 = "test";
$totalPrice2_display = "test";


$info2_display = "";
$info3_display = "";
$info4_display = "";
$info5_display = "";



$message = str_replace('{$gubun_title}', $gubun_title, $message);
$message = str_replace('{$gubun_title1}', $gubun_title1, $message);
$message = str_replace('{$gubun_title2}', $gubun_title2, $message);
$message = str_replace('{$gubun_title3}', $gubun_title3, $message);

$message = str_replace('{$userName}', $userName, $message);
$message = str_replace('{$userPhone}', $userPhone, $message);
$message = str_replace('{$ResNumber}', $ResNumber, $message);

$message = str_replace('{$info1_title}', $info1_title, $message);
$message = str_replace('{$info2_title}', $info2_title, $message);
$message = str_replace('{$info1}', $info1, $message);
$message = str_replace('{$info2}', $info2, $message);
$message = str_replace('{$info3}', $info3, $message);

$message = str_replace('{$etc}', $etc, $message);
$message = str_replace('{$banknum}', $banknum, $message);

$message = str_replace('{$totalinfo}', $totalinfo, $message);
$message = str_replace('{$totalPrice1}', $totalPrice1, $message);
$message = str_replace('{$totalPrice2}', $totalPrice2, $message);
$message = str_replace('{$totalPrice2_display}', $totalPrice2_display, $message);


$message = str_replace('{$info2_display}', $info2_display, $message);
$message = str_replace('{$info3_display}', $info3_display, $message);
$message = str_replace('{$info4_display}', $info4_display, $message);
$message = str_replace('{$info5_display}', $info5_display, $message);

echo $message;
?>