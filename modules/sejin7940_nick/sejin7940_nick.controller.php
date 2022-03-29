<?php
	/**
	 * @class  sejin7940_nickController
	 * @author sejin7940 (sejin7940@nate.com)
	 * @brief  sejin7940_nick 모듈의 Controller class
	 **/

	class sejin7940_nickController extends sejin7940_nick {

		/**
		 * @brief Initialization
		 **/
		function init() {
			
		}



		// 닉네임 변경 전 적요할 trigger ( before )
		function triggerChangeNick(&$obj)	{
			$oSejin7940_nickModel=&getmodel('sejin7940_nick');
			$module_config = $oSejin7940_nickModel->getModuleConfig();

			$logged_info=Context::get('logged_info');

			if($logged_info->is_admin=='Y' && $obj->member_srl!=$logged_info->member_srl) {
				$oMemberModel = &getModel('member');
				$orgMemberInfo = $oMemberModel->getMemberInfoByMemberSrl($obj->member_srl);
				$_SESSION['nick_name_old'] = $orgMemberInfo->nick_name;
			}


			if($logged_info->is_admin!='Y' && $module_config->use_change_nick=='Y') {
				$oMemberModel = &getModel('member');
				$orgMemberInfo = $oMemberModel->getMemberInfoByMemberSrl($obj->member_srl);

				$this->deleteDeniedNickName($orgMemberInfo->nick_name);

				if($module_config->change_nick_term) {
					if($obj->nick_name != $orgMemberInfo->nick_name) {
						$args_nick->member_srl = $logged_info->member_srl;
						$args_nick->regdate = date('YmdHis',mktime(date('H'), date('i'), date('s'), date('m'), date('d')-$module_config->change_nick_term,  date('Y')));

						$args_nick->change_regdate = date('YmdHis',mktime(date('H'), date('i'), date('s'), date('m'), date('d')+$module_config->change_nick_term,  date('Y')));

						$output_nick = executeQueryArray('sejin7940_nick.getNickLatestChange', $args_nick);
						
						$last_change_date ='';
						foreach($output_nick->data as $key_nick=>$val_nick) {
							if(!$last_change_date) {
								$last_change_date = $val_nick->regdate;
								$new_change_date = date('Y년 m월 d일 H시 i분',mktime(substr($last_change_date,8,2), substr($last_change_date,10,2), substr($last_change_date,12,2), substr($last_change_date,4,2), substr($last_change_date,6,2)+$module_config->change_nick_term,  substr($last_change_date,0,4) ));
							}
						}

						if(count($output_nick->data)) return new Object(-1,'닉네임 재변경은 변경 후 '.$new_change_date.'이후에 가능합니다.');
					}
				}
			}
		}


		// 닉네임 변경 후 적요할 trigger ( after )
		function triggerUpdateMember(&$obj)	{
			$oSejin7940_nickModel=&getmodel('sejin7940_nick');
			$module_config = $oSejin7940_nickModel->getModuleConfig();
			$logged_info=Context::get('logged_info');

			if($module_config->use_change_nick=='Y' || $module_config->use_deny_nick=='Y' || $module_config->use_same_nick=='Y') {
				$oMemberModel = &getModel('member');
				$orgMemberInfo = $oMemberModel->getMemberInfoByMemberSrl($obj->member_srl);
				
				if($_SESSION['nick_name_old']) {
					$nick_name_old = $_SESSION['nick_name_old'];
					unset($_SESSION['nick_name_old']);
				}
				else {
					$nick_name_old = $logged_info->nick_name;
				}

				if($obj->nick_name != $nick_name_old ) {
					$args->nick_name_old = $nick_name_old;
					$args->nick_name_new = $obj->nick_name;
					$args->member_srl = $obj->member_srl;

					if($module_config->use_change_nick=='Y') {
						$output = executeQuery('sejin7940_nick.insertMemberNickLog', $args);
					}

					// 금지 닉네임 등록
					if($module_config->use_deny_nick=='Y') {
						$output = $output = $this->insertDeniedNickName($nick_name_old, '닉네임 변경');
					}

					// 닉네임 동기화 
					if($module_config->use_same_nick=='Y') {
						$args->nick_name = $obj->nick_name;
						executeQuery('sejin7940_nick.updateDocumentsSameNick', $args);
						executeQuery('sejin7940_nick.updateCommentsSameNick', $args);
					}
				}
			}
		}


		function insertDeniedNickName($nick_name, $description = '')
		{
			$args->nick_name = $nick_name;
			$args->description = $description;

			return executeQuery('member.insertDeniedNickName', $args);
		}


		function deleteDeniedNickName($nick_name)
		{
			if(!$nick_name) unset($nick_name);

			$args = new stdClass;
			$args->nick_name = $nick_name;
			return executeQuery('member.deleteDeniedNickName', $args);
		}

	}
?>