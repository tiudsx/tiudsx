<?php
    /**
     * 클래스명:class GPEnova_PopularityT1
     * 제작:그레이브디자인(simpleeye) 
	 * 메일:simpleeye79@naver.com
     **/
    class GPEnova_PopularityT1 extends WidgetHandler {
        /**
         * info.xml에서 extra_vars는 $args로 받아옴
         * 결과 = return
         **/
        function proc($args) {
            $oDocumentModel = &getModel('document');
            $oCommentModel = &getModel('comment');

			if(!$args->subject_cut_size_m) $args->subject_cut_size_m = 28;

            // info.xml 사용자변수 정의
            $widget_info->subject_cut_size = (int)$args->subject_cut_size; // 글자제목수
			$widget_info->subject_cut_size_m = (int)$args->subject_cut_size_m; // 글자제목수(모바일)
			$widget_info->popu_height = $args->popu_height; //위젯 높이
			$widget_info->popu_tapwidth = $args->popu_tapwidth; //탭 넓이
			$widget_info->popu_tapcolor = $args->popu_tapcolor; //탭 컬러셋
			$widget_info->popu_tapstep = $args->popu_tapstep; //탭아이템 정렬
			$widget_info->popu_dayset = $args->popu_dayset; //날짜
			
            // 인수 정리
            $db_args->module_srls = $args->module_srls;
            $db_args->sort_index = 'documents.list_order';
            $db_args->order_type = 'asc';
            $db_args->list_count = $args->list_count;

            // 최신글을 구함
            $output = executeQueryArray('widgets.GPEnova_PopularityT1.getNewestDocuments', $db_args);
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
            $output = executeQueryArray('widgets.GPEnova_PopularityT1.getNewestComments', $db_args);
            if($output->data) {
                foreach($output->data as $k => $v) {
                    $oComment = null;
                    $oComment = $oCommentModel->getComment();
                    $oComment->setAttribute($v);
                    $output->data[$k] = $oComment;
                }
            }
            $widget_info->newest_comments = $output->data;

            // 인기글을 구함
            $db_args->sort_index = 'readed_count';
            $db_args->order_type = 'desc';
			if($args->popu_term) $db_args->popu_term = date("Ymd", strtotime("-{$args->popu_term} day"));
            $output = executeQueryArray('widgets.GPEnova_PopularityT1.getPopularDocuments', $db_args);
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
