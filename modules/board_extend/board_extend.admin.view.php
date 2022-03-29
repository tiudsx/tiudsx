<?php
    /**
     * @class  board_extendAdminView
     * @author xiso (xiso@xiso.co.kr)
     * @brief  board_extend module admin view class
     **/
	require_once(_XE_PATH_.'modules/board/board.admin.view.php');
    class board_extendAdminView extends boardAdminView {

        /**
         * @brief initialization
         *
         * board_extend module can be divided into general use and admin use.\n
         **/
        function init() {
			Context::loadLang(_XE_PATH_.'modules/board/lang');
            // module_srl이 있으면 미리 체크하여 존재하는 모듈이면 module_info 세팅
            $module_srl = Context::get('module_srl');
            if(!$module_srl && $this->module_srl) {
                $module_srl = $this->module_srl;
                Context::set('module_srl', $module_srl);
            }

            // module model 객체 생성 
            $oModuleModel = &getModel('module');

            // module_srl이 넘어오면 해당 모듈의 정보를 미리 구해 놓음
            if($module_srl) {
                $module_info = $oModuleModel->getModuleInfoByModuleSrl($module_srl);
                if(!$module_info) {
                    Context::set('module_srl','');
                    $this->act = 'list';
                } else {
                    ModuleModel::syncModuleToSite($module_info);
                    $this->module_info = $module_info;
                    Context::set('module_info',$module_info);
                }
            }

            if($module_info && $module_info->module != 'board') return $this->stop("msg_invalid_request");

            // 모듈 카테고리 목록을 구함
            $module_category = $oModuleModel->getModuleCategories();
            Context::set('module_category', $module_category);

            // 템플릿 경로 지정 (board의 경우 tpl에 관리자용 템플릿 모아놓음)
            $template_path = sprintf("%stpl/",$this->module_path);
            $this->setTemplatePath($template_path);

            // 정렬 옵션을 세팅
            foreach($this->order_target as $key) $order_target[$key] = Context::getLang($key);
            $order_target['list_order'] = Context::getLang('document_srl');
            $order_target['update_order'] = Context::getLang('last_update');
            Context::set('order_target', $order_target);
		}
		
		function dispBoard_extendAdminBoardList(){
			//게시판 리스트는 기존board와 똑같이받아옴
			$this->dispBoardAdminContent();
		}
		
		function dispBoard_extendAdminBoardModify(){
			if(!in_array($this->module_info->module, array('admin', 'board','blog','guestbook'))) {
                return $this->alertMessage('msg_invalid_request');
            }
			
			//문서의 값들을 목록상에서 바로수정	
            $oDocumentModel = &getModel('document');

            // setup module_srl/page number/ list number/ page count
            $args->module_srl = $this->module_info->module_srl; 
            $args->page = Context::get('page');
            $args->list_count = Context::get('list_count') ? Context::get('list_count') : 20; 
            $args->page_count = $this->page_count; 

            // get the search target and keyword
            $args->search_target = Context::get('search_target'); 
            $args->search_keyword = Context::get('search_keyword'); 

            // if the category is enabled, then get the category
            if($this->module_info->use_category=='Y') $args->category_srl = Context::get('category'); 

            // setup the sort index and order index
            $args->sort_index = Context::get('sort_index');
            $args->order_type = Context::get('order_type');
            if(!in_array($args->sort_index, $this->order_target)) $args->sort_index = $this->module_info->order_target?$this->module_info->order_target:'list_order';
            if(!in_array($args->order_type, array('asc','desc'))) $args->order_type = $this->module_info->order_type?$this->module_info->order_type:'asc';

            // set the current page of documents  
            $_get = $_GET;
            if(!$args->page && ($_GET['document_srl'] || $_GET['entry'])) {
                $oDocument = $oDocumentModel->getDocument(Context::get('document_srl'));
                if($oDocument->isExists() && !$oDocument->isNotice()) {
                    $page = $oDocumentModel->getDocumentPage($oDocument, $args);
                    Context::set('page', $page);
                    $args->page = $page;
                }
            }

            // setup the list count to be serach list count, if the category or search keyword has been set
            if($args->category_srl || $args->search_keyword) $args->list_count = $this->search_list_count;

            // setup the list config variable on context
            Context::set('list_config', $this->listConfig);
            // setup document list variables on context 
            $output = $oDocumentModel->getDocumentList($args, $this->except_notice, true, $this->columnList);
            Context::set('document_list', $output->data);
			Context::set('list_count', $args->list_count);
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('page_navigation', $output->page_navigation);
			
			$this->setTemplateFile('contentlist');
		}
		
	}
?>