<?php
	if(!defined('__XE__') || !defined('__ZBXE__')) exit(); // 1.4~5.x ���� ����.
	if(Context::get('module')=='admin') return; // ������ ������ ���� ����.
	// License: Creative Commons License Attribution-ShareAlike 2.0 Korea (������ǥ��-�������Ǻ������ 2.0 ���ѹα�) http://creativecommons.org/licenses/by-sa/2.0/kr/
	// Author: Name: ���·�(AN TAERYONG) / Nick: OEZ

	if($addon_info->terms == 'Y'){ // ����� ��� Ȯ��.

	//HTTP_HOST���� �� PORT������ ���� �Ǵ� �⺻�� ����.
	$http_host = $addon_info->http_host; if(!$addon_info->http_host) $addon_info->terms == 'N';
	$http_port = Context::get('_http_port'); if(!$http_port) $http_port = '80';
	$https_port = Context::get('_https_port'); if(!$http_port) $https_port = '443';

	// MID&ACTüũ �⺻�� Ȯ�� (���Է½� MID&ACT admin���� ���� (admin���� �����ϴ� ������ mid&act ���� admin�̶�� ���� ���� ���ϵ��� XE�� �����Ǿ��ֱ� ������ �浹 ���񽺰� ����.)
	if(!$addon_info->mids)  $addon_info->mids = 'admin';
	if(!$addon_info->acts)  $addon_info->acts = 'admin';

	// �ֵ��MID&ACT�������� �޾ƿ� ','�� ���ڿ�������
	$mid_array = explode(",",$addon_info->mids);
	$act_array = explode(",",$addon_info->acts);
	
	//MID&ACT üũ = in_array(ã����,�迭)��ġ�ϸ� TURE ����ġ�ϸ� FALSE
	foreach($mid_array as $key => $val_mid) $mid_array[$key] = trim($val_mid);
	foreach($act_array as $key => $val_act) $act_array[$key] = trim($val_act);

	if(Context::get('_use_ssl') == 'always' || in_array(Context::get('mid'),$mid_array) || in_array(Context::get('act'),$act_array)) { // SSL�׻��뼳�� �Ǵ� ������ MID&ACT�ϰ�� TRUE.
		Context::set('_use_ssl', 'always'); // XE�������� ����ο����� HTTPS��ũ�� ����.(�ٸ������ΰ��ִ°�� SSL�����߻�.)
		if(!isset($_SERVER["HTTPS"]) !=false) { //HTTPS�� �ƴϸ� HTTPS�� ��ȯ
			header('location: https://'.$http_host.':'.$https_port.$_SERVER['REQUEST_URI']);
		} return;
	}else{ // SSL�̻�� �Ǵ� ������ ���� HTTPS�� ���ӽ� HTTP�� ��ȯ.
		if(!isset($_SERVER["HTTPS"]) !=true && Context::get('act') !=true) { //HTTP�� �ƴϸ� Ư�� ACT���� �ƴϸ� TRUE.
			header('location: http://'.$http_host.':'.$http_port.$_SERVER['REQUEST_URI']);
		} return;
	};

	};// ����� Ȯ�� ����.
	return;// �ֵ�� ����.
?>