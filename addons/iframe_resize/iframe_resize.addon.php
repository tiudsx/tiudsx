<?php
if(!defined('__XE__')) exit();

/**
 * @file iframe_resize.addon.php
 * @author PASSIVE
 * @brief NONE
 **/

if($called_position == 'after_module_proc' && Context::getResponseMethod()=="HTML") {
		if(!isset($mobile_set)) {
	$mobile_set = false;
	if(Mobile::isFromMobilePhone()) {
		Context::loadFile(array('./addons/iframe_resize/js/resize.js', 'body'), true);
		$mobile_set = true;
	}
// ����� js �ε�

} elseif($mobile_set===true) {
		Context::loadFile(array('./addons/iframe_resize/js/resize.js', 'body'), true);
}
// PC js�ε�
if($mobile_set == false) {
		Context::loadFile(array('./addons/iframe_resize/js/resizePC.js', 'body'), true);
}
}