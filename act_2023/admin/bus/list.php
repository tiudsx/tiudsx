<?php 
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/logininfo.php';

$param_mid = $_REQUEST["mid"];

if($param_mid == ""){
	$param = str_replace("/", "", $_SERVER["REQUEST_URI"]);

	if (!empty(strpos($_SERVER["REQUEST_URI"], '?'))){
		$param = substr($param, 0, strpos($_SERVER["REQUEST_URI"], '?') - 1);
	}

	$param = explode('_', $param)[0];
}else{
	$param = $param_mid;
}

if($param == "busadmin"){ //양양 셔틀버스
    $shopseq = 7;
    $bus_type = "양양";
}else if($param == "busadmin_dh"){ //동해 셔틀버스
    $shopseq = 14;
    $bus_type = "동해"; 
}
?>
<script>
    var shopseq = <?=$shopseq?>;
</script>

<link rel="stylesheet" type="text/css" href="/act_2023/admin/_css/admin_bus.css">
<link rel="stylesheet" type="text/css" href="/act_2023/admin/_css/admin_common.css">
<link rel="stylesheet" type="text/css" href="/act_2023/front/_css/surfview.css">
<link rel="stylesheet" type="text/css" href="/act_2023/front/_css/jquery-ui.css" />

<script type="text/javascript" src="/act_2023/front/_js/jquery.blockUI.js"></script>
<script type="text/javascript" src="/act_2023/front/_js/common.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/front/_js/busday.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/admin/_js/common.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/admin/_js/admin_bus.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/admin/_js/admin_package.js?v=<?=time()?>"></script>

<div class="bd_tl" style="width:100%;">
	<h1 class="ngeb clear"><i class="bg_color"></i><?=$bus_type?> 셔틀버스 예약관리</h1>
</div>

<div class="container" id="contenttop">
<!-- .tab_container -->
<div id="containerTab" class="areaRight">
    <section>
        <aside id="right_article3" class="left_article5">
        <?include '_calendar.php'?>
        </aside>
        <article class="right_article5">
            <ul class="tabs" style="margin-left:5px;">
                <li class="active" rel="tab1">검색관리</li>
                <li rel="tab2">예약관리</li>
                <li rel="tab3">패키지관리</li>
                <li rel="tab4">카톡안내</li>
            </ul>

            <!-- #container -->
            <div class="tab_container" style="margin-left:5px;">
                <!-- #tab1 -->
                <div id="tab1" class="tab_content">
                    <form name="frmSearch" id="frmSearch" autocomplete="off">
                    <table class='et_vars exForm bd_tb' style="width:100%">
                        <colgroup>
                            <col style="width:100px;">
                            <col style="width:80px;">
                            <col style="width:auto;">
                            <col style="width:100px;">
                            <col style="width:80px;">
                            <col style="width:auto;">
                        </colgroup>
                        <tr>
                            <th><label><input type="checkbox" id="chkGubun" onclick="fnChkAll(this, 'chkResConfirm')">구분</label></th>
                            <td colspan="5">
                                <label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" checked="checked" value="0" style="vertical-align:-3px;" />미입금</label>
                                <label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" checked="checked" value="1" style="vertical-align:-3px;" />예약대기</label>
                                <label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" checked="checked" value="8" style="vertical-align:-3px;" />입금완료</label>
                                <label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" value="3" style="vertical-align:-3px;" />확정</label>
                                <label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" value="7" style="vertical-align:-3px;" />취소</label>
                                <label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" checked="checked" value="4" style="vertical-align:-3px;" />환불요청</label>
                                <label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" value="5" style="vertical-align:-3px;" />환불완료</label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6">
                                
                            </td>
                        </tr>
                        <tr>
                            <th rowspan="2"><label><input type="checkbox" id="chkBusY1" checked="checked" value="7" style="vertical-align:-3px;" onclick="fnChkBusAll(this, 'Y1')" />서울 출발</label></th>
                            <th>사당선</th>
                            <td>
                                <label><input type="checkbox" id="chkbusNumY1" name="chkbusNum[]" checked="checked" value="SA1" style="vertical-align:-3px;" />1호차</label>
                                <label><input type="checkbox" id="chkbusNumY1" name="chkbusNum[]" checked="checked" value="SA2" style="vertical-align:-3px;" />2호차</label>
                                <label><input type="checkbox" id="chkbusNumY1" name="chkbusNum[]" checked="checked" value="SA3" style="vertical-align:-3px;" />3호차</label>
                            </td>
                            <th rowspan="2"><label><input type="checkbox" id="chkBusY2" checked="checked" value="7" style="vertical-align:-3px;" onclick="fnChkBusAll(this, 'Y2')" />서울 복귀</label></th>
                            <th>오후 출발</th>
                            <td>
                                <label><input type="checkbox" id="chkbusNumY2" name="chkbusNum[]" checked="checked" value="AM1" style="vertical-align:-3px;" />1호차</label>
                                <label><input type="checkbox" id="chkbusNumY2" name="chkbusNum[]" checked="checked" value="AM2" style="vertical-align:-3px;" />2호차</label>
                                <label><input type="checkbox" id="chkbusNumY2" name="chkbusNum[]" checked="checked" value="AM3" style="vertical-align:-3px;" />3호차</label>
                            </td>
                        </tr>
                        <tr>
                            <th>종로선</th>
                            <td>
                                <label><input type="checkbox" id="chkbusNumY1" name="chkbusNum[]" checked="checked" value="JO1" style="vertical-align:-3px;" />1호차</label>
                                <label><input type="checkbox" id="chkbusNumY1" name="chkbusNum[]" checked="checked" value="JO2" style="vertical-align:-3px;" />2호차</label>
                                <label><input type="checkbox" id="chkbusNumY1" name="chkbusNum[]" checked="checked" value="JO3" style="vertical-align:-3px;" />3호차</label>
                            </td>
                            <th>저녁 출발</th>
                            <td>
                                <label><input type="checkbox" id="chkbusNumY2" name="chkbusNum[]" checked="checked" value="PM1" style="vertical-align:-3px;" />1호차</label>
                                <label><input type="checkbox" id="chkbusNumY2" name="chkbusNum[]" checked="checked" value="PM2" style="vertical-align:-3px;" />2호차</label>
                                <label><input type="checkbox" id="chkbusNumY2" name="chkbusNum[]" checked="checked" value="PM3" style="vertical-align:-3px;" />3호차</label>
                            </td>
                        </tr>
                        <tr>
                            <th>검색기간</th>
                            <td colspan="5">
                                <input type="hidden" id="shopseq" name="shopseq" value="<?=$shopseq?>">
                                <input type="hidden" id="hidsearch" name="hidsearch" value="init">
                                <input type="text" id="sDate" name="sDate" cal="sdate" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7" >&nbsp;~
                                <input type="text" id="eDate" name="eDate" cal="edate" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7" >
                                <input type="button" class="bd_btn" style="padding-top:4px;font-family: gulim,Tahoma,Arial,Sans-serif;" value="전체" onclick="fnDateReset();" />
                            </td>
                            
                        </tr>
                        <tr>
                            <th>검색어</th>
                            <td colspan="5">
                                <input type="text" id="schText" name="schText" value="" class="itx2" style="width:100px;">
                                
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" style="text-align:center;">
                                <input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="검색" onclick="fnSearchAdmin('bus/list_search.php');" />
                            </td>
                        </tr>
                    </table>
                    </form>                
                </div>

                <!-- #tab2 -->
                <div id="tab2" class="tab_content" style="display:none;">
                    <div style="text-align:center;font-size:14px;padding:50px;" id="initText2">
                        <b>달력 날짜를 선택하세요.</b>
                    </div>
                    <div id="divResList"></div>
                </div>
                
                <!-- #tab3 -->
                <div id="tab3" class="tab_content" style="display:none;">
                    <?include 'list_package.php'?>
                </div>
                
                <!-- #tab4 -->
                <div id="tab4" class="tab_content" style="display:none;">
                    <?include 'list_cancel.php'?>
                </div>
            </div>
            <!-- .tab_container -->
        </article>
    </section>

    <div>
        <div id="mngSearch" style="display:inline-block;width:100%"></div>
    </div>
</div>
<!-- #container -->
</div>

<input type="hidden" id="hidselDate" value="">
<iframe id="ifrmResize" name="ifrmResize" style="width:800px;height:400px;display:none;"></iframe>

<div id="res_modify" style="display:none;padding:5px;height: 600px;overflow-y: auto;"> 
    <form name="frmModify" id="frmModify" autocomplete="off">
    <div class="gg_first" style="margin-top:0px;">
        액트립 서핑버스 (예약번호 : <span id="span_resnum"></span>)
        <input type="hidden" id="resnum" name="resnum" size="12" value="" class="itx" readonly="readonly">
        <input type="hidden" id="user_email" name="user_email" value="" class="itx" size="18">
        <input type="hidden" id="resparam" name="resparam" size="10" value="changeConfirmNew" class="itx">
        <input type="hidden" id="resseq" name="resseq" size="10" value="" class="itx">
    </div>
        
    <table class="et_vars exForm bd_tb" style="width:100%;" id="infomodify">
        <!-- <colgroup>
            <col width="8%" />
            <col width="17%" />
            <col width="8%" />
            <col width="17%" />
            <col width="8%" />
            <col width="17%" />
            <col width="8%" />
            <col width="17%" />
        </colgroup> -->
        <tbody>
			<tr>
				<th>예약자</th>
				<td>
                    <input type="text" id="user_name" name="user_name" size="5" value="" class="itx">
                    <input type="text" id="user_tel" name="user_tel" size="12" value="" class="itx">
                </td>
                <th>결제금액</th>
                <td><span id="span_res_price"></span></td>
				<th>할인쿠폰</th>
                <td><span id="span_res_disprice"></span><span id="span_res_couponname"></span></td>
            </tr>
            <tr>
                <th>신청일</th>
                <td><input type="text" id="insdate" name="insdate" size="16" value="" class="itx"></td>
                <th>확정일</th>
                <td><input type="text" id="confirmdate" name="confirmdate" size="16" value="" class="itx"></td>
			</tr>
			<tr>
                <th>예약정보</th>
                <td colspan="9">
					<table class="et_vars exForm bd_tb tbcenter" style="width:100%">
						<colgroup>
							<col width="110" />
							<col width="180" />
							<col width="63" />
							<col width="*" />
							<col width="70" />
							<col width="110" />
							<col width="90" />
							<col width="100" />
						</colgroup>
						<tbody>
                            <tr>
                                <th>이용일</th>
                                <th>호차</th>
                                <th>좌석</th>
                                <th>정류장</th>
                                <th>상태</th>
                                <th>예약상태
                                    <select class="select" onchange="fnSelChange(this, 0);">
                                        <option value="">전체</option>
                                        <option value='0'>미입금</option>
                                        <option value='1'>예약대기</option>
                                        <option value='3'>확정</option>
                                        <option value='4'>환불요청</option>
                                        <option value='5'>환불완료</option>
                                        <option value='7'>취소</option>
                                        <option value='8'>입금완료</option>
                                    </select>
                                </th>
								<th>수수료
                                    <select class="select" onchange="fnSelChange(this, 1);">
                                        <option value="">전체</option>
                                        <option value="Y">있음</option>
                                        <option value="N">없음</option>
                                    </select>
                                </th>
								<th>알림톡
                                    <select class="select" onchange="fnSelChange(this, 2);">
                                        <option value="">전체</option>
                                        <option value='N'>미발송</option>
                                        <option value='Y'>발송</option>
                                    </select>
                                </th>
							</tr>
							<tr id="trbus" style="display:none;">
                                <td><input type="text" calid="res_date" name="res_date[]" cal="date" size="10" class="itx" readonly="readonly" disabled="disabled"></td>
								<td>
									<input type="hidden" id="ressubseq" name="ressubseq[]" >
                                    <select id="res_busline" name="res_busline" class="select" disabled="disabled">
                                        <option value="SA1">출발 - 사당선 1호차</option>
                                        <option value="SA2">출발 - 사당선 2호차</option>
                                        <option value="SA3">출발 - 사당선 3호차</option>
                                        <option value="JO1">출발 - 종로선 1호차</option>
                                        <option value="JO2">출발 - 종로선 2호차</option>
                                        <option value="JO3">출발 - 종로선 3호차</option>
                                        <option value="AM1">복귀 - 오후 1호차</option>
                                        <option value="AM2">복귀 - 오후 2호차</option>
                                        <option value="AM3">복귀 - 오후 3호차</option>
                                        <option value="PM1">복귀 - 저녁 1호차</option>
                                        <option value="PM2">복귀 - 저녁 2호차</option>
                                        <option value="PM3">복귀 - 저녁 3호차</option>
                                    </select>
								</td>
								<td style="line-height:2.3em">
                                    <select id="res_seat" name="res_seat[]" class="select">
                                    <?for ($i=1; $i < 46; $i++) { 
                                        echo "<option value='$i'>$i</option>";
                                    }?>
                                    </select>
								</td>
                                <td>
                                    <select id="res_spointname" name="res_spointname[]" class="select">
                                        <option value="N">출발</option>
                                    </select> →
                                    <select id="res_epointname" name="res_epointname[]" class="select">
                                        <option value="N">도착</option>
                                    </select>
                                </td>
                                <td><span id="res_confirmText" style="font-weight:600;"></span></td>
                                <td>
                                    <select id="res_confirm" name="res_confirm[]" class="select allselect0">
                                        <option value='0'>미입금</option>
                                        <option value='1'>예약대기</option>
                                        <option value='3'>확정</option>
                                        <option value='4'>환불요청</option>
                                        <option value='5'>환불완료</option>
                                        <option value='7'>취소</option>
                                        <option value='8'>입금완료</option>
                                    </select> 
                                </td>
                                <td>
                                    <select id="rtn_charge_yn" name="rtn_charge_yn[]" class="select allselect1">
                                        <option value="Y">있음</option>
                                        <option value="N">없음</option>
                                    </select>
                                </td>
                                <td>
                                    <select id="res_kakao" name="res_kakao[]" class="select allselect2">		
                                        <option value='N'>미발송</option>
                                        <option value='Y'>발송</option>
                                    </select>
                                </td>

							</tr>
                        </tbody>
					</table>
                </td>
			</tr>
            <tr>
                <th>요청사항</th>
                <td colspan="9"><textarea id="etc" name="etc" rows="5" style="width: 60%; resize:none;"></textarea></td>
			</tr>
			<tr>
                <th>직원메모</th>
                <td colspan="9"><textarea id="memo" name="memo" rows="5" style="width: 60%; resize:none;"></textarea></td>
			</tr>
            <tr>
				<td class="col-02" style="text-align:center;" colspan="7">
					<input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="수정" onclick="fnBusDataAdd();" id="SolModify" />&nbsp;
					<input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="닫기" onclick="fnBlockClose();fnBusPopupReset();" />
                </td>
                <td style="text-align:center;">
                    <input type="button" class="gg_btn res_btn_color2" style="width:80px; height:40px;" value="삭제" onclick="fnBusDataDel();" />  
                </td>
            </tr>
        </tbody>
    </table>
    </form>
</div>


<script>
fnBusPointList();

$j(document).ready(function(){
	fnSearchAdmin('bus/list_search.php');
});
</script>