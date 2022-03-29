<?php 
if(!defined('__ZBXE__')) exit();

define('__XE__',true);
require_once($_SERVER["DOCUMENT_ROOT"].'/config/config.inc.php');  //경로정상확인하였습니다.
$oContext = &Context::getInstance();
$oContext->init();

$module_srl = Context::get('module_srl');
$module_name = Context::get('mid');
$pcmobile = Mobile::isMobileCheckByAgent();
$is_logged = Context::get('is_logged');
$logged_info = Context::get('logged_info');
$mng_use = Context::get('manager');
$user_id = $logged_info->user_id;
$user_name = $logged_info->user_name;
$nick_name = $logged_info->nick_name;
$birthday = $logged_info->birthday;
$email_address = $logged_info->email_address;
$member_srl = $logged_info->member_srl;
$is_admin = $logged_info->is_admin;
$userphone = $logged_info->userphone;
$surftype = $logged_info->surftype;
$oContext->close;

$group_list = $logged_info->group_list;

$_UserType = "";
foreach ($group_list as $key => $value) {
	if($value == "사업자회원"){	
		$_UserType = "2";
	}else if($value == "운영자" || $value == "매니저"){
		$_UserType = "0";
		break;
	}
}
?>