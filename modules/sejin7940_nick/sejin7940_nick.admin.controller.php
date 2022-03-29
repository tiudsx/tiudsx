<?php
	/**
	 * @class  sejin7940_nickAdminController
	 * @author sejin7940 (sejin7940@nate.com)
	 * @brief  sejin7940_nick 모듈의 AdminController class
	 **/

	class sejin7940_nickAdminController extends sejin7940_nick {

		/**
		 * @brief Initialization
		 **/
		function init() {
			
		}


		function procSejin7940_nickAdminConfig() {
			
			$config->use_change_nick = Context::get('use_change_nick');
			$config->use_deny_nick = Context::get('use_deny_nick');
			$config->change_nick_term = Context::get('change_nick_term');
			$config->use_same_nick = Context::get('use_same_nick');
			
			$oModuleController = &getController('module');
			$oModuleController->insertModuleConfig('sejin7940_nick',$config);
			
			if(!in_array(Context::getRequestMethod(),array('XMLRPC','JSON'))) {
				$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispSejin7940_nickAdminConfig');
				header('location:'.$returnUrl);
				return;
			}
			else return $output;
		}


		function procSejin7940_nickAdminDeleteLog() {
			$args->member_srl = Context::get('target_srl');;
			$args->regdate = Context::get('vars1');;

            return executeQuery('sejin7940_nick.deleteNickLog', $args);
		}

	}
?>