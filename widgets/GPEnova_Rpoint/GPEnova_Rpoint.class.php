<?php
    /**
     * 클래스명:class GPEnova_Rpoint
     * 제작:그레이브디자인(simpleeye) 
	 * 메일:simpleeye79@naver.com
     **/
    class GPEnova_Rpoint extends WidgetHandler {
        /**
         * info.xml에서 extra_vars는 $args로 받아옴
         * 결과 = return
         **/
        function proc($args) {
		
		//point_output를 클래스변수로 설정
			$point_output = new Object();
		
		// 설정변수 체크
            $title = $args->title;
            $list_count = (int)$args->list_count;
            $obj->list_count = $list_count;
            $mid_list = explode(",",$args->mid_list);
            $subject_cut_size = $args->subject_cut_size;
		
		//기본값 설정
			if(!$list_count) $list_count = 5; // list
            if(!$subject_cut_size) $subject_cut_size = 0;
		
		//
            $oMemberModel = &getModel('member');
            $this->oPointModel = &getModel('point');
		
		//관리자 포함,비포함 옵션
            $obj->is_admin = $args->output_admin == "true" ? "N" : "";
		
		//회원그룹별 출력
			//회원그룹이 체크될시
            if($args->output_group) {
                if($args->output_group) $obj->selected_group_srl = $args->output_group; //그룹체크시 포함,비포함
                $point_output = executeQuery('widgets.GPEnova_Rpoint.getMemberListWithinGroup', $obj); //쿼리id값 참조,쿼리실행후 출력
            }
            else {
				//회원그룹이 선택사항없을시, 전체포인트목록 출력
              $point_output = executeQuery("widgets.GPEnova_Rpoint.getMemberList",$obj); //쿼리id값 참조,쿼리실행후 출력
            }
		
		//오류 무시
            if(!$point_output->toBool()) return;
		
		//결과가 있으면 각 문서 객체화를 시킴
            if(count($point_output->data)) {
                foreach($point_output->data as $key => $val) {
                    $point_list[$key] = $val;
                }
            } else {
                $point_list = array();
            }
		//사용자변수 정의
            $widget_info->title = $title;
            $widget_info->list_count = $list_count;
            $widget_info->point_list = $point_list;
            $widget_info->subject_cut_size = $subject_cut_size;
            $widget_info->debug = $debug;
            Context::set('widget_info', $widget_info);
		// 템플릿의 스킨 경로를 지정 (skin, colorset에 따른 값을 설정)
            $tpl_path = sprintf('%sskins/%s', $this->widget_path, $args->skin);
            Context::set('colorset', $args->colorset);
		// 템플릿 파일을 지정
            $tpl_file = 'list';
		// 템플릿 컴파일
            $oTemplate = &TemplateHandler::getInstance();
            $point_output = $oTemplate->compile($tpl_path, $tpl_file);
            return $point_output;
        }// function proc($args) *end
    }// class GPEnova_Rpoint extends WidgetHandler *end
?>