function doCallModuleAction2(module, action, target_srl, vars1) {
	var params = new Array();
	params['target_srl'] = target_srl;
	params['cur_mid'] = current_mid;
	params['vars1'] = vars1;
	exec_xml(module, action, params, completeCallModuleAction);
}