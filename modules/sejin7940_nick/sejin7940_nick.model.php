<?php
	/**
	 * @class  sejin7940_nickModel
	 * @author sejin7940 (sejin7940@nate.com)
	 * @brief  sejin7940_nick 모듈의 Model class
	 **/

	class sejin7940_nickModel extends sejin7940_nick {

		/**
		 * @brief Initialization
		 **/
		function init() {
			
		}

		function getModuleConfig() {
			static $config = null;
			if(is_null($config)) {
				$oModuleModel = &getModel('module');
				$config = $oModuleModel->getModuleConfig('sejin7940_nick');
			}

			 if(!$config->skin) $config->skin = "default";

			return $config;
		}

		function getNickChangeList($obj) {
			$args->s_member_srl = $obj->member_srl;

			$args->page = Context::get('page'); // /< Page

			$args->list_count = 20; // /< the number of posts to display on a single page
			$args->page_count = 10; // /< the number of pages that appear in the page navigation

			$args->sort_index = 'regdate'; // /< sorting values
			$args->order_type = 'desc'; // /< sorting values by order
			if($obj->search_target == 'nick_name') {
				$args->nick_name_old = $obj->search_keyword;
				$args->nick_name_new = $obj->search_keyword;
			}

			if($obj->search_target == 'member_srl') $args->s_member_srl = $obj->search_keyword;
			if($obj->search_target == 'user_id') {
				$oMemberModel = &getModel('member');
				$member_info = $oMemberModel->getMemberInfoByUserID($obj->search_keyword);
				$args->s_member_srl = $member_info->member_srl;;
			}

	
			$output = executeQueryArray('sejin7940_nick.getNickChangeList', $args);
			return $output;
		}


	}
?>