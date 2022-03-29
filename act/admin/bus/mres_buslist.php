<?php 
include __DIR__.'/../../db.php';
include __DIR__.'/../../common/logininfo.php';
$shopseq = 0;
?>

<link rel="stylesheet" type="text/css" href="/act/css/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="/act/css/surfview.css">
<link rel="stylesheet" type="text/css" href="/act/css/admin/admin_surf.css?v=1">
<link rel="stylesheet" type="text/css" href="/act/css/admin/admin_common.css">
<script type="text/javascript" src="/act/js/admin_surf.js?v=1"></script>
<script type="text/javascript" src="/act/js/surfview_bus.js"></script>
<script type="text/javascript" src="/act/js/jquery.blockUI.js"></script>

<div class="bd_tl" style="width:100%;">
	<h1 class="ngeb clear"><i class="bg_color"></i>액트립 셔틀버스 예약관리</h1>
</div>

<script>
    var mobileuse = "m";
</script>

<div class="container" id="contenttop">
<!-- .tab_container -->
<div id="containerTab" class="areaRight">
    <div id="right_article3" class="right_article4">
		<?include __DIR__.'/../shop/res_surfcalendar.php'?>
    </div>

    <ul class="tabs">
        <li class="active" rel="tab1">검색관리</li>
        <li rel="tab2">예약관리</li>
    </ul>

	<!-- #container -->
    <div class="tab_container">
        <!-- #tab1 -->
        <div id="tab1" class="tab_content">
		<form name="frmSearch" id="frmSearch" autocomplete="off">
			<table class='et_vars exForm bd_tb' style="width:100%">
				<colgroup>
					<col style="width:70px;">
					<col style="width:70px;">
					<col style="width:*;">
				</colgroup>
				<tr>
					<th>구분</th>
					<td colspan="2">
                        <label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" checked="checked" value="0" style="vertical-align:-3px;" />미입금</label>
                        <label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" checked="checked" value="1" style="vertical-align:-3px;" />예약대기</label>
                        <label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" checked="checked" value="8" style="vertical-align:-3px;" />입금완료</label><br>
						<label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" value="3" style="vertical-align:-3px;" />확정</label>
						<label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" value="7" style="vertical-align:-3px;" />취소</label>
						<label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" checked="checked" value="4" style="vertical-align:-3px;" />환불요청</label>
						<label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" value="5" style="vertical-align:-3px;" />환불완료</label>
					</td>
				</tr>
                <tr>
                    <td colspan="3">
						서울 - 양양행
					</td>
				</tr>
				<tr>
					<th rowspan="2"><label><input type="checkbox" id="chkBusY1" name="chkBus[]" checked="checked" value="7" style="vertical-align:-3px;" onclick="fnChkBusAll(this, 'Y1')" />양양행</label></th>
					<th>사당선</th>
					<td>
                        <label><input type="checkbox" id="chkbusNumY1" name="chkbusNum[]" checked="checked" value="Y1" style="vertical-align:-3px;" />1호차</label>
                        <label><input type="checkbox" id="chkbusNumY1" name="chkbusNum[]" checked="checked" value="Y3" style="vertical-align:-3px;" />3호차</label>
                        <label><input type="checkbox" id="chkbusNumY1" name="chkbusNum[]" checked="checked" value="Y5" style="vertical-align:-3px;" />5호차</label>
                    </td>
                </tr>
                <tr>
					<th>종로선</th>
					<td>
                        <label><input type="checkbox" id="chkbusNumY1" name="chkbusNum[]" checked="checked" value="Y2" style="vertical-align:-3px;" />2호차</label>
                        <label><input type="checkbox" id="chkbusNumY1" name="chkbusNum[]" checked="checked" value="Y4" style="vertical-align:-3px;" />4호차</label>
                        <label><input type="checkbox" id="chkbusNumY1" name="chkbusNum[]" checked="checked" value="Y6" style="vertical-align:-3px;" />6호차</label>
                    </td>
                </tr>
                <tr>
                    <th rowspan="2"><label><input type="checkbox" id="chkBusY2" name="chkBus[]" checked="checked" value="7" style="vertical-align:-3px;" onclick="fnChkBusAll(this, 'Y2')" />서울행</label></th>
                    <th>오후 2시</th>
					<td>
                        <label><input type="checkbox" id="chkbusNumY2" name="chkbusNum[]" checked="checked" value="S21" style="vertical-align:-3px;" />1호차</label>
                        <label><input type="checkbox" id="chkbusNumY2" name="chkbusNum[]" checked="checked" value="S22" style="vertical-align:-3px;" />2호차</label>
                        <label><input type="checkbox" id="chkbusNumY2" name="chkbusNum[]" checked="checked" value="S23" style="vertical-align:-3px;" />3호차</label>
					</td>
				</tr>
				<tr>
                    <th>오후 5시</th>
					<td>
                        <label><input type="checkbox" id="chkbusNumY2" name="chkbusNum[]" checked="checked" value="S51" style="vertical-align:-3px;" />1호차</label>
                        <label><input type="checkbox" id="chkbusNumY2" name="chkbusNum[]" checked="checked" value="S52" style="vertical-align:-3px;" />2호차</label>
                        <label><input type="checkbox" id="chkbusNumY2" name="chkbusNum[]" checked="checked" value="S53" style="vertical-align:-3px;" />3호차</label>
					</td>
				</tr>
                <tr>
					<td colspan="3">
						서울 - 동해행
					</td>
				</tr>
				<tr>
                    <th><label><input type="checkbox" id="chkBusD1" name="chkBus[]" checked="checked" value="14" style="vertical-align:-3px;" onclick="fnChkBusAll(this, 'D1')" />동해행</label></th>
					<th>사당선</th>
					<td>
                        <label><input type="checkbox" id="chkbusNumD1" name="chkbusNum[]" checked="checked" value="E1" style="vertical-align:-3px;" />1호차</label>
                        <label><input type="checkbox" id="chkbusNumD1" name="chkbusNum[]" checked="checked" value="E2" style="vertical-align:-3px;" />2호차</label>
                        <label><input type="checkbox" id="chkbusNumD1" name="chkbusNum[]" checked="checked" value="E3" style="vertical-align:-3px;" />3호차</label><br>
						<label><input type="checkbox" id="chkbusNumD1" name="chkbusNum[]" checked="checked" value="E4" style="vertical-align:-3px;" />4호차</label>
                        <label><input type="checkbox" id="chkbusNumD1" name="chkbusNum[]" checked="checked" value="E5" style="vertical-align:-3px;" />5호차</label>
                        <label><input type="checkbox" id="chkbusNumD1" name="chkbusNum[]" checked="checked" value="E6" style="vertical-align:-3px;" />6호차</label>
                    </td>
                </tr>
                <tr>
                    <th rowspan="2"><label><input type="checkbox" id="chkBusD2" name="chkBus[]" checked="checked" value="14" style="vertical-align:-3px;" onclick="fnChkBusAll(this, 'D2')" />서울행</label></th>
                    <th>오후 2시</th>
					<td>
                        <label><input type="checkbox" id="chkbusNumD2" name="chkbusNum[]" checked="checked" value="A21" style="vertical-align:-3px;" />1호차</label>
                        <label><input type="checkbox" id="chkbusNumD2" name="chkbusNum[]" checked="checked" value="A22" style="vertical-align:-3px;" />2호차</label>
                        <label><input type="checkbox" id="chkbusNumD2" name="chkbusNum[]" checked="checked" value="A23" style="vertical-align:-3px;" />3호차</label>
					</td>
                </tr>
                <tr>
                    <th>오후 5시</th>
					<td>
                        <label><input type="checkbox" id="chkbusNumD2" name="chkbusNum[]" checked="checked" value="A51" style="vertical-align:-3px;" />1호차</label>
                        <label><input type="checkbox" id="chkbusNumD2" name="chkbusNum[]" checked="checked" value="A52" style="vertical-align:-3px;" />2호차</label>
                        <label><input type="checkbox" id="chkbusNumD2" name="chkbusNum[]" checked="checked" value="A53" style="vertical-align:-3px;" />3호차</label>
					</td>
				</tr>
				<tr>
					<th>검색기간</th>
					<td colspan="2">
						<input type="text" id="sDate" name="sDate" cal="sdate" readonly="readonly" style="width:66px;" value="<?=$datDate?>" class="itx2" maxlength="7" >&nbsp;~
						<input type="text" id="eDate" name="eDate" cal="edate" readonly="readonly" style="width:66px;" value="<?=substr($datDate, 0, 8).$s_t?>" class="itx2" maxlength="7" >
						<input type="button" class="bd_btn" style="padding-top:4px;font-family: gulim,Tahoma,Arial,Sans-serif;" value="전체" onclick="fnDateReset();" />
					</td>
					
				</tr>
				<tr>
					<th>검색어</th>
					<td colspan="2"><input type="text" id="schText" name="schText" value="" class="itx2" style="width:100px;"></td>
				</tr>
				<tr>
					<td colspan="3" style="text-align:center;"><input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="검색" onclick="fnSearchAdmin('bus/mres_buslist_search.php');" /></td>
				</tr>
			</table>
		</form>

        <div id="mngSearch"><?include 'mres_buslist_search.php'?></div>
		</div>

        <!-- #tab2 -->
        <div id="tab2" class="tab_content" style="display:none;">
			<div style="text-align:center;font-size:14px;padding:50px;" id="initText2">
				<b>날짜를 선택하세요.</b>
			</div>
			<div id="divResList"></div>
		</div>
    </div>
    <!-- .tab_container -->
</div>
<!-- #container -->
</div>

<input type="hidden" id="hidselDate" value="">
<iframe id="ifrmResize" name="ifrmResize" style="width:800px;height:400px;display:none;"></iframe>

<div id="res_busmodify" style="display:none;padding:5px;"> 
    <form name="frmModify" id="frmModify" autocomplete="off">
    <div class="gg_first" style="margin-top:0px;">서핑버스 정보변경</div>
    <table class="et_vars exForm bd_tb" style="width:100%;display:;" id="infomodify">
        <colgroup>
            <col width="25%" />
            <col width="75%" />
        </colgroup>
        <tbody>
            <tr>
                <th>신청일</th>
                <td>
                    <input type="text" id="insdate" name="insdate" size="20" value="" class="itx">
                    <input type="text" id="resnum" name="resnum" size="10" value="" class="itx">
                </td>
            </tr>
            <tr>
                <th>확정일</th>
                <td><input type="text" id="confirmdate" name="confirmdate" size="20" value="" class="itx"></td>
            </tr>
            <tr>
                <th>상태</th>
                <td>
                    <select id="res_confirm" name="res_confirm" class="select">
                        <option value='0'>미입금</option>
                        <option value='1'>예약대기</option>
                        <option value='3'>확정</option>
                        <option value='4'>환불요청</option>
                        <option value='5'>환불완료</option>
                        <option value='7'>취소</option>
                        <option value='8'>입금완료</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>이용일</th>
                <td><input type="text" id="res_date" name="busDate" size="10" value="<?=$row["busDate"]?>" class="itx"></td>
            </tr>
            <tr>
                <th>이름</th>
                <td><input type="text" id="user_name" name="user_name" size="11" value="" class="itx"></td>
            </tr>
            <tr>
                <th>연락처</th>
                <td><input type="text" id="user_tel" name="user_tel" size="12" value="" class="itx"></td>
            </tr>
            <tr>
                <th>이메일</th>
                <td><input type="text" id="user_email" name="user_email" value="" class="itx" size="18"></td>
            </tr>
            <tr>
                <th>수수료여부</th>
                <td>
                    <select id="rtn_charge_yn" name="rtn_charge_yn" class="select">
                        <option value="Y">있음</option>
                        <option value="N">없음</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>할인금액</th>
                <td><input type="text" id="res_price_coupon" name="res_price_coupon" value="" class="itx" size="18"></td>
            </tr>
            <tr>
                <th>이용금액</th>
                <td><input type="text" id="res_totalprice" name="res_totalprice" size="12" value="" class="itx"></td>
            </tr>
            <tr>
                <th>좌석</th>
                <td>
                    <select id="res_seat" name="res_seat" class="select">
                    <?for ($i=1; $i < 46; $i++) { 
                        echo "<option value='$i'>$i</option>";
                    }?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>호차</th>
                <td>
                    <select id="res_busnum" name="res_busnum" class="select">
                        <option value="Y1">양양행 1호차</option>
                        <option value="Y2">양양행 2호차</option>
                        <option value="Y3">양양행 3호차</option>
                        <option value="Y4">양양행 4호차</option>
                        <option value="Y5">양양행 5호차</option>
                        <option value="Y6">양양행 6호차</option>
                        <option value="S1">(양양)서울행 2시 1호차</option>
                        <option value="S3">(양양)서울행 2시 2호차</option>
                        <option value="S3">(양양)서울행 2시 3호차</option>
                        <option value="S2">(양양)서울행 5시 1호차</option>
                        <option value="S2">(양양)서울행 5시 2호차</option>
                        <option value="S2">(양양)서울행 5시 3호차</option>
                        <option value="E1">동해행 1호차</option>
                        <option value="E2">동해행 2호차</option>
                        <option value="E3">동해행 3호차</option>
                        <option value="E4">동해행 4호차</option>
                        <option value="E5">동해행 5호차</option>
                        <option value="E6">동해행 6호차</option>
                        <option value="A1">(동해)서울행 2시 1호차</option>
                        <option value="A2">(동해)서울행 2시 2호차</option>
                        <option value="A2">(동해)서울행 2시 3호차</option>
                        <option value="A3">(동해)서울행 5시 1호차</option>
                        <option value="A3">(동해)서울행 5시 2호차</option>
                        <option value="A3">(동해)서울행 5시 3호차</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>정류장</th>
                <td>
                    <select id="res_spointname" name="res_spointname" class="select">
                        <option value="N">출발</option>
                    </select> →
                    <select id="res_epointname" name="res_epointname" class="select">
                        <option value="N">도착</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="col-02" style="text-align:center;" colspan="2">
                    <input type="hidden" id="res_price" name="res_price" size="10" value="0" class="itx">
                    <input type="hidden" id="gubun" name="gubun" size="10" value="0" class="itx">
                    <input type="hidden" id="resparam" name="resparam" size="10" value="busmodify" class="itx">
                    <input type="hidden" id="userid" name="userid" size="10" value="admin" class="itx">
                    <input type="hidden" id="ressubseq" name="ressubseq" size="10" value="" class="itx">
                    <input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="정보수정" onclick="fnDataModify();" />&nbsp;
                    <input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="닫기" onclick="fnModifyClose();" />
                </td>
            </tr>
        </tbody>
    </table>
    </form>
</div> 