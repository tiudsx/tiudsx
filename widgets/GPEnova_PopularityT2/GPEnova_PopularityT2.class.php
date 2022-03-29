<?php
    /**
     * 클래스명:class GPEnova_PopularityT2
     * 제작:그레이브디자인(simpleeye) 
	 * 메일:simpleeye79@naver.com
     **/
    class GPEnova_PopularityT2 extends WidgetHandler {
        /**
         * info.xml에서 extra_vars는 $args로 받아옴
         * 결과 = return
         **/
        function proc($args) {
            $oDocumentModel = &getModel('document');
            $oCommentModel = &getModel('comment');

			if(!$args->subject_cut_size_m1) $args->subject_cut_size_m1 = 39;
			if(!$args->subject_cut_size_m2) $args->subject_cut_size_m2 = 30;

            // info.xml 사용자변수 정의
            $widget_info->subject_cut_size = (int)$args->subject_cut_size; //글자 제목수
			$widget_info->subject_cut_size_m1 = (int)$args->subject_cut_size_m1; //글자 제목수
			$widget_info->subject_cut_size_m2 = (int)$args->subject_cut_size_m2; //글자 제목수
			$widget_info->popu_style = $args->popu_style; //스타일 선택
			$widget_info->popu_colorset = $args->popu_colorset; //컬러셋 선택
			$widget_info->popu_dayset = $args->popu_dayset; //날짜
			$widget_info->popu_moreurl = $args->popu_moreurl; //더보기url

            // 인수 정리
            $db_args->module_srls = $args->module_srls;
            $db_args->sort_index = 'documents.list_order';
            $db_args->order_type = 'asc';
            $db_args->list_count = $args->list_count;

            // 최신글을 구함
            $output = executeQueryArray('widgets.GPEnova_PopularityT2.getNewestDocuments', $db_args);
            if($output->data) {
                foreach($output->data as $k => $v) {
                    $oDocument = null;
                    $oDocument = $oDocumentModel->getDocument();
                    $oDocument->setAttribute($v, false);
                    $GLOBALS['XE_DOCUMENT_LIST'][$oDocument->document_srl] = $oDocument;
                    $output->data[$k] = $oDocument;
                }
                $oDocumentModel->setToAllDocumentExtraVars();
            }
            $widget_info->newest_documents = $output->data;

            // 최신 댓글을 구함
            $db_args->sort_index = 'list_order';
            $output = executeQueryArray('widgets.GPEnova_PopularityT2.getNewestComments', $db_args);
            if($output->data) {
                foreach($output->data as $k => $v) {
                    $oComment = null;
                    $oComment = $oCommentModel->getComment();
                    $oComment->setAttribute($v);
                    $output->data[$k] = $oComment;
                }
            }
            $widget_info->newest_comments = $output->data;

            // 인기글을 구함(조회수)
            $db_args->sort_index = 'readed_count';
            $db_args->order_type = 'desc';
			if($args->popu_term) $db_args->popu_term = date("Ymd", strtotime("-{$args->popu_term} day"));
            $output = executeQueryArray('widgets.GPEnova_PopularityT2.getPopularDocuments', $db_args);
            if($output->data) {
                foreach($output->data as $k => $v) {
                    $oDocument = null;
                    $oDocument = $oDocumentModel->getDocument();
                    $oDocument->setAttribute($v, false);
                    $GLOBALS['XE_DOCUMENT_LIST'][$oDocument->document_srl] = $oDocument;
                    $output->data[$k] = $oDocument;
                }
                $oDocumentModel->setToAllDocumentExtraVars();
            }
            $widget_info->popular_documents = $output->data;

            // 인기글을 구함(추천수)
            $db_args->sort_index = 'voted_count';
            $db_args->order_type = 'desc';
			if($args->popu_term) $db_args->popu_term = date("Ymd", strtotime("-{$args->popu_term} day"));
            $output = executeQueryArray('widgets.GPEnova_PopularityT2.getPopularVotDocuments', $db_args);
            if($output->data) {
                foreach($output->data as $k => $v) {
                    $oDocument = null;
                    $oDocument = $oDocumentModel->getDocument();
                    $oDocument->setAttribute($v, false);
                    $GLOBALS['XE_DOCUMENT_LIST'][$oDocument->document_srl] = $oDocument;
                    $output->data[$k] = $oDocument;
                }
                $oDocumentModel->setToAllDocumentExtraVars();
            }
            $widget_info->popular_vot_documents = $output->data;

            Context::set('widget_info', $widget_info);
            // 언어파일 로드
            Context::loadLang($this->widget_path.'lang');
            // 템플릿의 스킨 경로를 지정 (skin, colorset에 따른 값을 설정)
            $tpl_path = sprintf('%sskins/%s', $this->widget_path, $args->skin);
            Context::set('colorset', $args->colorset);
            // 템플릿 파일을 지정
            $tpl_file = 'list';
            // 템플릿 컴파일
            $oTemplate = &TemplateHandler::getInstance();
            return $oTemplate->compile($tpl_path, $tpl_file);
        }
    }
?>
