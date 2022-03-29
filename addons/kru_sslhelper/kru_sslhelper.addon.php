<?php
	if(!defined('__XE__') || !defined('__ZBXE__')) exit(); // 1.4~5.x 적용 구문.
	if(Context::get('module')=='admin') return; // 관리자 페이지 적용 제외.
	// License: Creative Commons License Attribution-ShareAlike 2.0 Korea (저작자표시-동일조건변경허락 2.0 대한민국) http://creativecommons.org/licenses/by-sa/2.0/kr/
	// Author: Name: 안태룡(AN TAERYONG) / Nick: OEZ

	if($addon_info->terms == 'Y'){ // 사용전 사용 확인.

	//HTTP_HOST설정 및 PORT설정에 추출 또는 기본값 지정.
	$http_host = $addon_info->http_host; if(!$addon_info->http_host) $addon_info->terms == 'N';
	$http_port = Context::get('_http_port'); if(!$http_port) $http_port = '80';
	$https_port = Context::get('_https_port'); if(!$http_port) $https_port = '443';

	// MID&ACT체크 기본값 확인 (미입력시 MID&ACT admin으로 정의 (admin으로 정의하는 이유는 mid&act 값에 admin이라는 값을 넣지 못하도록 XE가 설정되어있기 때문에 충돌 서비스가 없음.)
	if(!$addon_info->mids)  $addon_info->mids = 'admin';
	if(!$addon_info->acts)  $addon_info->acts = 'admin';

	// 애드온MID&ACT설정값을 받아와 ','로 문자열나누기
	$mid_array = explode(",",$addon_info->mids);
	$act_array = explode(",",$addon_info->acts);
	
	//MID&ACT 체크 = in_array(찾을값,배열)일치하면 TURE 불일치하면 FALSE
	foreach($mid_array as $key => $val_mid) $mid_array[$key] = trim($val_mid);
	foreach($act_array as $key => $val_act) $act_array[$key] = trim($val_act);

	if(Context::get('_use_ssl') == 'always' || in_array(Context::get('mid'),$mid_array) || in_array(Context::get('act'),$act_array)) { // SSL항상사용설정 또는 지정된 MID&ACT일경우 TRUE.
		Context::set('_use_ssl', 'always'); // XE페이지내 상대경로에대해 HTTPS링크로 변경.(다만절대경로가있는경우 SSL에러발생.)
		if(!isset($_SERVER["HTTPS"]) !=false) { //HTTPS가 아니면 HTTPS로 전환
			header('location: https://'.$http_host.':'.$https_port.$_SERVER['REQUEST_URI']);
		} return;
	}else{ // SSL미사용 또는 선택적 사용시 HTTPS로 접속시 HTTP로 전환.
		if(!isset($_SERVER["HTTPS"]) !=true && Context::get('act') !=true) { //HTTP가 아니며 특정 ACT값이 아니면 TRUE.
			header('location: http://'.$http_host.':'.$http_port.$_SERVER['REQUEST_URI']);
		} return;
	};

	};// 사용전 확인 종료.
	return;// 애드온 종료.
?>