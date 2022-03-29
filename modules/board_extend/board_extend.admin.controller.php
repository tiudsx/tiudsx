<?php
    /**
     * @class  board_extendAdminController
     * @author xiso (xiso@xiso.co.kr)
     * @brief  board_extend module admin controller class
     **/
	require_once(_XE_PATH_.'modules/board/board.admin.controller.php');
    class board_extendAdminController extends boardAdminController {
		function procBoard_extendAdminWithOrder(){
			$order_value = Context::get('change_order');
			$args->change_order = $order_value;
			$args->document_srl = Context::get('document_srl');
			$output = executeQuery('board_extend.updateListOrder',$args);
		}
		
		function procBoard_extendAdminBoardModify(){
			$args->document_srl = Context::get('document_srl');
			$args->readed_count = Context::get('readed_count');
			$args->voted_count = Context::get('voted_count');
			$args->regdate = Context::get('regdate');
			$args->last_update = Context::get('last_update');
			$args->list_order = Context::get('list_order');
			$output = executeQuery("board_extend.updateDocument",$args);
			if(!$output->toBool()) return new Object(-1,"반영에 실패하였습니다.");
			return $this->setMessage("반영되었습니다.");
		}
	}
?>