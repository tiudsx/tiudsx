<?php
	/**
	 * @class  sejin7940_nickAdminView
	 * @author sejin7940 (sejin7940@nate.com)
	 * @brief  sejin7940_nick 모듈의 AdminView class
	 **/

	class sejin7940_nickAdminView extends sejin7940_nick {

		function init() {
            $oModuleModel = &getModel('module');
            $config = $oModuleModel->getModuleConfig('sejin7940_nick');
            // Set the configuration variable
            Context::set('config', $config);				

			$this->setTemplatePath($this->module_path.'tpl');										
		}


		// sejin7940_document 모듈 통합 관리자 페이지
		function dispSejin7940_nickAdminConfig() {

			// 템플릿 세팅
			$this->setTemplateFile('config.html');
		}



		function dispSejin7940_nickAdminNickChangeLog() {
			// option for a list
			$args->page = Context::get('page'); // /< Page
			$args->list_count = 30; // /< the number of posts to display on a single page
			$args->page_count = 10; // /< the number of pages that appear in the page navigation

//			$args->sort_index = 'document_declared.declared_count'; // /< sorting values
			$args->sort_index = 'nick.regdate'; // /< sorting values
			$args->order_type = 'desc'; // /< sorting values by order

			$args->member_srl = Context::get('member_srl');

			$args->search_target = $search_target = Context::get('search_target');
			$args->search_keyword = $search_keyword = Context::get('search_keyword');
			

			$oSejin7940_nickModel = &getModel('sejin7940_nick');
			$output = $oSejin7940_nickModel->getNickChangeList($args);

            // context::set for writing into a template 
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('nick_list', $output->data);
            Context::set('page_navigation', $output->page_navigation);

			// Set the template
            $this->setTemplateFile('nick_change_list');
		}

	}
?>