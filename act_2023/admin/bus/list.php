<?php 
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/logininfo.php';
$shopseq = 0;
?>

<link rel="stylesheet" type="text/css" href="/act/css/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="/act/css/surfview.css">
<link rel="stylesheet" type="text/css" href="/act/css/admin/admin_surf.css">
<link rel="stylesheet" type="text/css" href="/act/css/admin/admin_common.css">

<script type="text/javascript" src="/act/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="/act_2023/_js/common.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/admin/_js/common.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/_js/busday.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/admin/_js/admin_bus.js?v=<?=time()?>"></script>

<div class="bd_tl" style="width:100%;">
	<h1 class="ngeb clear"><i class="bg_color"></i>액트립 셔틀버스 예약관리</h1>
</div>

<script>
    var mobileuse = "";
</script>

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
                <li rel="tab3">정산관리</li>
                <li rel="tab4">타채널예약</li>
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
                            <col style="width:*;">
                            <col style="width:100px;">
                            <col style="width:80px;">
                            <col style="width:*;">
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
                                양양행
                            </td>
                        </tr>
                        <tr>
                            <th rowspan="2"><label><input type="checkbox" id="chkBusY1" name="chkBus[]" checked="checked" value="7" style="vertical-align:-3px;" onclick="fnChkBusAll(this, 'Y1')" />서울-양양행</label></th>
                            <th>사당선</th>
                            <td>
                                <label><input type="checkbox" id="chkbusNumY1" name="chkbusNum[]" checked="checked" value="Y|Sa1" style="vertical-align:-3px;" />1호차</label>
                                <label><input type="checkbox" id="chkbusNumY1" name="chkbusNum[]" checked="checked" value="Y|Sa2" style="vertical-align:-3px;" />2호차</label>
                                <label><input type="checkbox" id="chkbusNumY1" name="chkbusNum[]" checked="checked" value="Y|Sa3" style="vertical-align:-3px;" />3호차</label>
                            </td>
                            <th rowspan="2"><label><input type="checkbox" id="chkBusY2" name="chkBus[]" checked="checked" value="7" style="vertical-align:-3px;" onclick="fnChkBusAll(this, 'Y2')" />양양-서울행</label></th>
                            <th>양양 오후</th>
                            <td>
                                <label><input type="checkbox" id="chkbusNumY2" name="chkbusNum[]" checked="checked" value="S|Y21" style="vertical-align:-3px;" />1호차</label>
                                <label><input type="checkbox" id="chkbusNumY2" name="chkbusNum[]" checked="checked" value="S|Y22" style="vertical-align:-3px;" />2호차</label>
                                <label><input type="checkbox" id="chkbusNumY2" name="chkbusNum[]" checked="checked" value="S|Y23" style="vertical-align:-3px;" />3호차</label>
                            </td>
                        </tr>
                        <tr>
                            <th>종로선</th>
                            <td>
                                <label><input type="checkbox" id="chkbusNumY1" name="chkbusNum[]" checked="checked" value="Y|Jo1" style="vertical-align:-3px;" />1호차</label>
                                <label><input type="checkbox" id="chkbusNumY1" name="chkbusNum[]" checked="checked" value="Y|Jo2" style="vertical-align:-3px;" />2호차</label>
                                <label><input type="checkbox" id="chkbusNumY1" name="chkbusNum[]" checked="checked" value="Y|Jo3" style="vertical-align:-3px;" />3호차</label>
                            </td>
                            <th>양양 저녁</th>
                            <td>
                                <label><input type="checkbox" id="chkbusNumY2" name="chkbusNum[]" checked="checked" value="S|Y51" style="vertical-align:-3px;" />1호차</label>
                                <label><input type="checkbox" id="chkbusNumY2" name="chkbusNum[]" checked="checked" value="S|Y52" style="vertical-align:-3px;" />2호차</label>
                                <label><input type="checkbox" id="chkbusNumY2" name="chkbusNum[]" checked="checked" value="S|Y53" style="vertical-align:-3px;" />3호차</label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6">
                               동해행
                            </td>
                        </tr>
                        <tr>
                            <th rowspan="2"><label><input type="checkbox" id="chkBusD1" name="chkBus[]" checked="checked" value="14" style="vertical-align:-3px;" onclick="fnChkBusAll(this, 'D1')" />서울-동해행</label></th>
                            <th>사당선</th>
                            <td>
                                <label><input type="checkbox" id="chkbusNumD1" name="chkbusNum[]" checked="checked" value="E|Sa1" style="vertical-align:-3px;" />1호차</label>
                                <label><input type="checkbox" id="chkbusNumD1" name="chkbusNum[]" checked="checked" value="E|Sa2" style="vertical-align:-3px;" />2호차</label>
                            </td>
                            <th rowspan="2"><label><input type="checkbox" id="chkBusD2" name="chkBus[]" checked="checked" value="14" style="vertical-align:-3px;" onclick="fnChkBusAll(this, 'D2')" />동해-서울행</label></th>
                            <th>동해 오후</th>
                            <td>
                                <label><input type="checkbox" id="chkbusNumD2" name="chkbusNum[]" checked="checked" value="A|E21" style="vertical-align:-3px;" />1호차</label>
                                <label><input type="checkbox" id="chkbusNumD2" name="chkbusNum[]" checked="checked" value="A|E22" style="vertical-align:-3px;" />2호차</label>
                                <label><input type="checkbox" id="chkbusNumD2" name="chkbusNum[]" checked="checked" value="A|E23" style="vertical-align:-3px;" />3호차</label>
                            </td>
                        </tr>
                        <tr>
                            <th>종로선</th>
                            <td>
                                <label><input type="checkbox" id="chkbusNumD1" name="chkbusNum[]" checked="checked" value="E|Jo1" style="vertical-align:-3px;" />1호차</label>
                                <label><input type="checkbox" id="chkbusNumD1" name="chkbusNum[]" checked="checked" value="E|Jo2" style="vertical-align:-3px;" />2호차</label>
                                <label><input type="checkbox" id="chkbusNumD1" name="chkbusNum[]" checked="checked" value="E|Jo3" style="vertical-align:-3px;" />3호차</label>
                            </td>
                            <th>동해 저녁</th>
                            <td>
                                <label><input type="checkbox" id="chkbusNumD2" name="chkbusNum[]" checked="checked" value="A|E51" style="vertical-align:-3px;" />1호차</label>
                                <label><input type="checkbox" id="chkbusNumD2" name="chkbusNum[]" checked="checked" value="A|E52" style="vertical-align:-3px;" />2호차</label>
                                <label><input type="checkbox" id="chkbusNumD2" name="chkbusNum[]" checked="checked" value="A|E53" style="vertical-align:-3px;" />3호차</label>
                            </td>
                        </tr>
                        <tr>
                            <th>검색기간</th>
                            <td colspan="5">
                                <input type="hidden" id="hidsearch" name="hidsearch" value="init">
                                <input type="text" id="sDate" name="sDate" cal="sdate" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7" >&nbsp;~
                                <input type="text" id="eDate" name="eDate" cal="edate" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7" >
                                <input type="button" class="bd_btn" style="padding-top:4px;font-family: gulim,Tahoma,Arial,Sans-serif;" value="전체" onclick="fnDateReset();" />
                            </td>
                            
                        </tr>
                        <tr>
                            <th>검색어</th>
                            <td colspan="5"><input type="text" id="schText" name="schText" value="" class="itx2" style="width:100px;"></td>
                        </tr>
                        <tr>
                            <td colspan="6" style="text-align:center;"><input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="검색" onclick="fnSearchAdmin('bus/list_search.php');" /></td>
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
                    <?include 'res_bus_cal.php'?>
                </div>

                <!-- #tab3 -->
                <div id="tab4" class="tab_content" style="display:none;">
                    <form name="frmResKakao" id="frmResKakao" autocomplete="off">
                    <table class='et_vars exForm bd_tb'>
                        <tr>
                            <td colspan="6">
                                알림톡 발송 번호
                            </td>
                        </tr>
                        <tr>
                            <th>노선</th>
                            <th>채널</th>
                            <th>이름</th>
                            <th>연락처</th>
                            <th>이용일 (서울출발)</th>
                            <th>이용일 (서울복귀)</th>
                        </tr>
                        <tr>
                        <td>
                                <select id="resbus">
                                    <option value="YY">양양</option>
                                    <option value="DH">동해</option>
                                </select>
                            </td>
                            <td>
                                <select id="reschannel">
                                    <option value="11">프립</option>
                                    <option value="17">프립 패키지</option>
                                    <option value="16">클룩</option>
                                    <option value="7">네이버쇼핑</option>
                                    <option value="10">네이버예약</option>
                                    <option value="12">마이리얼트립</option>
                                    <option value="15">서프엑스</option>
                                </select>
                            </td>
                            <td><input type="text" id="username" name="username" style="width:66px;" value="" class="itx2" maxlength="7" ></td>
                            <td><input type="text" id="userphone" name="userphone" style="width:150px;" value="" class="itx2" maxlength="12"></td>
                            <td>
                                <input type="text" id="resDate1" name="resDate1" cal="date" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7" >
                                <select id="resbusseat1">
                                <?for ($i=0; $i < 20; $i++) { 
                                    echo '<option value="'.$i.'">'.$i.'명</option>';
                                }?>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="resDate2" name="resDate2" cal="date" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7" >
                                <select id="resbusseat2">
                                <?for ($i=0; $i < 20; $i++) { 
                                    echo '<option value="'.$i.'">'.$i.'명</option>';
                                }?>
                                </select>
                            </td>
                        </tr>
                        <tr id="fripMapping" style="display:;">
                            <td colspan="2">json 데이터 맵핑</td>
                            <td colspan="4">
                                <textarea id="html_1" cols="40" rows="7"></textarea>
                                <input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:40px; height:20px;" value="프립" onclick="fnMakeTable();" />
                                
                                <input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:40px; height:20px;" value="클룩" onclick="fnGetJson();" />

                                <textarea id="html_2" cols="40" rows="7" style="display: ;"></textarea>
                                <div id="divCopy" style="display: none;"></div>
                                <div style="width: 500px;" id="divSet"></div>

<script>
function fnMakeTable() {
    //복사된 html을 가공 table[class='el-table__body']
    var strHtml = $j("#html_1").val().replace(/<!---->/gi,"");
    $j("#divCopy").html(strHtml.substring(strHtml.indexOf('<table'), strHtml.lastIndexOf('</table>') + 8));
    //$j("#divCopy").html($j("#divCopy").find("table[class='el-table__body']").html());

    //Json 인스턴스
    var objList = new Array();
    var objValue = new Object();

    //html 생성
    var addHtml = '<table width="100%" border="1" id="td_select">';
    $j("#divCopy .el-table__row").each(function(){

        objValue = new Object();

        addHtml += '<tr name="' + $j(this).find("td").eq(3).find(".cell").text() + '">';
        
        addHtml += '<td style="mso-data-placement:same-cell;">';
        addHtml += $j(this).find("td").eq(1).find(".cell").text(); //이름
        addHtml += '</td>';
        objValue.name = $j(this).find("td").eq(1).find(".cell").text(); //이름

        addHtml += '<td style="mso-data-placement:same-cell;">';
        addHtml += $j(this).find("td").eq(2).find(".cell").text(); //성별
        addHtml += '</td>';
        objValue.genser = $j(this).find("td").eq(2).find(".cell").text(); //성별

        addHtml += '<td style="mso-data-placement:same-cell;">';
        addHtml += $j(this).find("td").eq(3).find(".cell").text(); //연락처
        addHtml += '</td>';
        objValue.tel = $j(this).find("td").eq(3).find(".cell").text(); //연락처

        addHtml += '<td style="mso-data-placement:same-cell;">';
        addHtml += $j(this).find("td").eq(4).find(".cell").text(); //아이템명
        addHtml += '</td>';
        objValue.item = $j(this).find("td").eq(4).find(".cell").text(); //아이템명

        addHtml += '<td style="mso-data-placement:same-cell;">';
        addHtml += $j(this).find("td").eq(5).find(".cell").text(); //추가정보
        addHtml += '</td>';
        objValue.addinfo = $j(this).find("td").eq(5).find(".cell").text(); //추가정보

        addHtml += '<td style="mso-data-placement:same-cell;">';
        addHtml += $j(this).find("td").eq(6).find(".cell").text(); //예약상태
        addHtml += '</td>';
        objValue.state = $j(this).find("td").eq(6).find(".cell").text(); //예약상태

        addHtml += '<td style="mso-data-placement:same-cell;">';
        
        if ($j(this).find("button").length > 0) {
            addHtml += $j(this).find("button").text(); //액션
            objValue.btn = $j(this).find("button").text(); //액션
        }
        else{
            addHtml += 'none'; //액션
            objValue.btn = 'none'; //액션
        }
        
        addHtml += '</td>';

        addHtml += '<td style="mso-data-placement:same-cell;">';
        addHtml += '1';
        addHtml += '</td>';
        addHtml += '</tr>';

        objList.push(objValue);
    });
    addHtml += '</table>';
    
    $j("#divSet").html(addHtml);

    $j("#html_2").val(JSON.stringify(objList));
}

function fnGetJson() {
            
    $j("#divCopy").html($j("#html_1").val());
    $j("#divCopy").html($j("#divCopy").find(".booking-list-result").html());

    var $state = "";    //예약 상태영역
    var $info = "";     //예약 상세영역

    //Json 인스턴스
    var objList = new Array();
    var objValue = new Object();
    var colKey = "";
    var colValue = "";
    
    var colNameTitle = {
        "상품명":"prod_name",
        "패키지명":"prod_pkg",
        "단위":"ea",
        "이용시간":"bus_date",
        "전화번호":"user_tel_sub",
        "전화번호_2":"user_tel",
        "성":"user_name1",
        "이름":"user_name2",
        "성명":"user_fullname"
    }

    $j("#divCopy").find(".booking-item").each(function(){

        var objValue = new Object();

        //#region 예약 상태정보
        $state = $j(this).find(".boooking-general-info-operation");

        //예약확인ID
        objValue.res_id = $state.find(".info-item").eq(0).find("span").eq(1).text();
        //예약시간
        //objValue.res_time = $state.find(".info-item").eq(1).text().split(":")[1].trim().substring(0,10);
        //예약상태
        objValue.state = $state.find(".info-item").eq(2).find(".ant-tag").eq(0).text();;

        //#regionend

        /******************************************************************************/

        //#region 예약 상세정보
        $info = $j(this).find(".booking-info");

        $info.find("ul").each(function(){
            $j(this).find("li").each(function(){

                colKey = $j(this).find("p").eq(0).text().replace(":","").replace(/ /g, ''); //json Text
                colValue = $j(this).find("p").eq(1).text(); //json Value

                if (objValue.user_tel_sub != undefined && colKey == "전화번호") {
                    colKey = "전화번호_2";
                }

                if (colNameTitle[colKey] != undefined) {
                    objValue[colNameTitle[colKey]] = colValue;
                }
                
            });
        });

        //#regionend

        objList.push(objValue);

    });

    $j("#html_2").val(JSON.stringify(objList));
    //alert(JSON.stringify(objList));

}
</script>

                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" style="text-align:center;"><input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="알림톡 발송" onclick="fnResKakaoAdmin();" /></td>
                        </tr>
                    </table>
                    </form>

                    <div class="gg_first">알림톡 발송 정보 <input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:40px; height:20px;" value="조회" onclick="fnSearchAdmin('bus/list_search_channel.php', '#mngKakaoSearch', 'N');" /></div>
                    <div id="mngKakaoSearch"> (https://alimtalk-api.bizmsg.kr/codeList.html)</div>

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
    <div class="gg_first" style="margin-top:0px;">액트립 서핑버스</div>
    <table class="et_vars exForm bd_tb" style="width:100%;display:;" id="infomodify">
        <colgroup>
            <col width="6%" />
            <col width="14%" />
            <col width="6%" />
            <col width="14%" />
            <col width="6%" />
            <col width="14%" />
            <col width="6%" />
            <col width="14%" />
            <col width="6%" />
            <col width="14%" />
        </colgroup>
        <tbody>
			<tr>
				<th>예약번호</th>
				<td><input type="text" id="resnum" name="resnum" size="12" value="" class="itx" readonly="readonly"></td>
                <th>예약자이름</th>
                <td><input type="text" id="user_name" name="user_name" size="12" value="" class="itx"></td>
                <th>연락처</th>
				<td>
					<input type="text" id="user_tel" name="user_tel" size="12" value="" class="itx">
				</td>
				<th>할인쿠폰</th>
				<td>
                    <input type="text" id="res_price_coupon" name="res_price_coupon" value="" class="itx" size="6"> / 
                    <input type="text" id="res_coupon" name="res_coupon" value="" class="itx" size="8">
                </td>
                <th>결제금액</th>
                <td>
                    <input type="text" id="res_price" name="res_price" size="4" value="" class="itx"> 
                    할인 : <input type="text" id="res_disprice" name="res_disprice" size="4" value="" class="itx">
                </td>
				</td>
            </tr>
            <tr>
                <th>이메일</th>
                <td><input type="text" id="user_email" name="user_email" value="" class="itx" size="18"></td>
                <th>신청일</th>
                <td><input type="text" id="insdate" name="insdate" size="20" value="" class="itx"></td>
                <th>확정일</th>
                <td><input type="text" id="confirmdate" name="confirmdate" size="20" value="" class="itx"></td>
                <th>제휴업체</th>
                <td><input type="text" id="res_cooperate" name="res_cooperate" size="20" value="" class="itx" readonly="readonly"></td>
			</tr>
			<tr>
                <th>예약정보</th>
                <td colspan="9">
					<table class="et_vars exForm bd_tb tbcenter" style="width:100%">
						<colgroup>
							<col width="*" />
							<col width="*" />
							<col width="*" />
							<col width="*" />
							<col width="*" />
							<col width="*" />
							<col width="*" />
							<col width="*" />
						</colgroup>
						<tbody>
                            <tr>
                                <th>이용일</th>
                                <th>호차</th>
                                <th>좌석</th>
                                <th>정류장</th>
                                <th>현재예약</th>
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
                                    <select id="res_busnum" name="res_busnum" class="select" onchange="fnBusPointSel2(this, this.value, '', '', 2);" disabled="disabled">
                                        <option value="Y1">양양행 1호차</option>
                                        <option value="Y2">양양행 2호차</option>
                                        <option value="Y3">양양행 3호차</option>
                                        <option value="Y4">양양행 4호차</option>
                                        <option value="Y5">양양행 5호차</option>
                                        <option value="Y6">양양행 6호차</option>
                                        <option value="S21">(양양)서울행 15시 1호차</option>
                                        <option value="S23">(양양)서울행 15시 2호차</option>
                                        <option value="S23">(양양)서울행 15시 3호차</option>
                                        <option value="S51">(양양)서울행 18시 1호차</option>
                                        <option value="S52">(양양)서울행 18시 2호차</option>
                                        <option value="S53">(양양)서울행 18시 3호차</option>
                                        <option value="E1">동해행 1호차</option>
                                        <option value="E2">동해행 2호차</option>
                                        <option value="E3">동해행 3호차</option>
                                        <option value="E4">동해행 4호차</option>
                                        <option value="E5">동해행 5호차</option>
                                        <option value="E6">동해행 6호차</option>
                                        <option value="A21">(동해)서울행 15시 1호차</option>
                                        <option value="A22">(동해)서울행 15시 2호차</option>
                                        <option value="A22">(동해)서울행 15시 3호차</option>
                                        <option value="A51">(동해)서울행 18시 1호차</option>
                                        <option value="A52">(동해)서울행 18시 2호차</option>
                                        <option value="A53">(동해)서울행 18시 3호차</option>
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
				<td class="col-02" style="text-align:center;" colspan="10">
                    <input type="hidden" id="resparam" name="resparam" size="10" value="changeConfirmNew" class="itx">
                    <input type="hidden" id="resseq" name="resseq" size="10" value="" class="itx">
					<input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="수정" onclick="fnBusDataAdd();" id="SolModify" />&nbsp;
					<input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="닫기" onclick="fnBlockClose();fnBusPopupReset();" />
                </td>
            </tr>
        </tbody>
    </table>
    </form>
</div>

<script>
$j(document).ready(function(){
	fnSearchAdmin('bus/list_search.php');

	fnSearchAdmin('bus/list_search_channel.php', '#mngKakaoSearch', 'N');
});
</script>