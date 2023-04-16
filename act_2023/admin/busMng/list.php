<?php 
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/logininfo.php';
$shopseq = -2;
?>

<link rel="stylesheet" type="text/css" href="/act/css/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="/act/css/surfview.css">
<link rel="stylesheet" type="text/css" href="/act/css/admin/admin_surf.css">
<link rel="stylesheet" type="text/css" href="/act/css/admin/admin_common.css">

<script type="text/javascript" src="/act/js/jquery.blockUI.js"></script>
<script type="text/javascript" src="/act_2023/_js/common.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/_js/busday.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/admin/_js/common.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/admin/_js/admin_bus.js?v=<?=time()?>"></script>

<div class="bd_tl" style="width:100%;">
	<h1 class="ngeb clear"><i class="bg_color"></i>액트립 셔틀버스 등록관리</h1>
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
                <li class="active" rel="tab1">일정등록</li>
                <li rel="tab2">버스관리</li>
                <li rel="tab3">정류장관리</li>
            </ul>

            <!-- #container -->
            <div class="tab_container" style="margin-left:5px;">
                <!-- #tab1 -->
                <div id="tab1" class="tab_content">
                    <form name="frmModify" id="frmModify" autocomplete="off">

                    <input type="hidden" id="hidselDate" name="hidselDate" value="">

                    <table class='et_vars exForm bd_tb' style="width:100%">
                        <colgroup>
                            <col style="width:100px;">
                            <col style="width:*;">
                            <col style="width:150px;">
                            <col style="width:100px;">
                            <col style="width:150px;">
                            <col style="width:80px;">
                            <col style="width:80px;">
                        </colgroup>
                        <tr>
                            <th>운행일</th>
                            <th>버스번호</th>
                            <th>노선</th>
                            <th>좌석수</th>
                            <th>GPS</th>
                            <th>사용여부</th>
                            <th><input type="button" class="btnsurfadd" style="width:40px;" value="추가" data-gubun="trbus"></th>
                        </tr>
                        <tr id="trbus" style="display:none;">
                            <td style="text-align:center;">
                                <input type="hidden" id="resseq" name="resseq[]" size="10" value="" class="itx">
                                <input type="text" id="res_busdate" name="res_busdate[]" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7">
                            </td>
                            <td style="text-align:center;">
                                <select id="res_busgubun" name="res_busgubun[]" class="select" sel="">
                                    <option value="" selected>--- 버스번호 ----</option>
                                    <option value="Y|Sa1|사당선 1호차">양양행(사당선) 1호차</option>
                                    <option value="Y|Sa2|사당선 2호차">양양행(사당선) 2호차</option>
                                    <option value="Y|Sa3|사당선 3호차">양양행(사당선) 3호차</option>
                                    <option value="Y|Jo1|종로선 1호차">양양행(종로선) 1호차</option>
                                    <option value="Y|Jo2|종로선 2호차">양양행(종로선) 2호차</option>
                                    <option value="Y|Jo3|종로선 3호차">양양행(종로선) 3호차</option>
                                    <option value="">-------</option>
                                    <option value="S|Y21|오후 1호차">양양>서울행 오후 1호차</option>
                                    <option value="S|Y22|오후 2호차">양양>서울행 오후 2호차</option>
                                    <option value="S|Y23|오후 3호차">양양>서울행 오후 3호차</option>
                                    <option value="S|Y51|저녁 1호차">양양>서울행 저녁 1호차</option>
                                    <option value="S|Y52|저녁 2호차">양양>서울행 저녁 2호차</option>
                                    <option value="S|Y53|저녁 3호차">양양>서울행 저녁 3호차</option>
                                    <option value="">-------</option>
                                    <option value="E|Sa1|사당선 1호차">동해행(사당선) 1호차</option>
                                    <option value="E|Sa2|사당선 2호차">동해행(사당선) 2호차</option>
                                    <option value="E|Sa3|사당선 3호차">동해행(사당선) 3호차</option>
                                    <option value="E|Jo1|종로선 1호차">동해행(종로선) 1호차</option>
                                    <option value="E|Jo2|종로선 2호차">동해행(종로선) 2호차</option>
                                    <option value="E|Jo3|종로선 3호차">동해행(종로선) 3호차</option>
                                    <option value="">-------</option>
                                    <option value="A|E21|오후 1호차">동해>서울행 오후 1호차</option>
                                    <option value="A|E22|오후 2호차">동해>서울행 오후 2호차</option>
                                    <option value="A|E23|오후 3호차">동해>서울행 오후 3호차</option>
                                    <option value="A|E51|저녁 1호차">동해>서울행 저녁 1호차</option>
                                    <option value="A|E52|저녁 2호차">동해>서울행 저녁 2호차</option>
                                    <option value="A|E53|저녁 3호차">동해>서울행 저녁 3호차</option>
                                </select>
                            </td>
                            <td style="text-align:center;">
                                <select id="res_point" name="res_point[]" class="select" sel="">
                                    <option value="" selected>--- 노선 ----</option>
                                    <option value="sadang">사당선</option>
                                    <option value="jongno">종로선</option>
                                    <option value="">-------</option>
                                    <option value="yang1">양양 오후</option>
                                    <option value="yang2">양양 저녁</option>
                                    <option value="">-------</option>
                                    <option value="dong1">동해 오후</option>
                                    <option value="dong2">동해 저녁</option>
                                </select>
                            </td>
                            <td style="text-align:center;">
                                <select id="res_seat" name="res_seat[]" class="select" sel="">
                                    <option value="44">44</option>
                                    <option value="45">45</option>
                                </select>
                            </td>
                            <td style="text-align:center;">
                                <select id="res_gpsname" name="res_gpsname[]" class="select" sel="">
                                    <option value="양양 1호차">양양 1호차</option>
                                    <option value="양양 2호차">양양 2호차</option>
                                    <option value="양양 3호차">양양 3호차</option>
                                    <option value="양양 4호차">양양 4호차</option>
                                </select>
                            </td>
                            <td style="text-align:center;">
                                <select id="res_useYN" name="res_useYN[]" class="select">
                                    <option value="Y">Y</option>
                                    <option value="N">N</option>
                                </select>
                            </td>
                            <td style="text-align:center;"><input type="button" class="btnsurfdel" style="width:40px;" value="삭제" onclick="fnBusDel(this);" ></td>
                        </tr>
                        <tr>
                            <td class="col-02" style="text-align:center;" colspan="7">
                                <input type="hidden" id="resparam" name="resparam" size="10" value="busMngadd" class="itx">
                                <input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="등록" onclick="fnBusMngDataAdd('add');" id="Add"/>
                                <input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;display:none;" value="수정" onclick="fnBusMngDataAdd('modify');" id="Modify" />&nbsp;
                                <input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="초기화" onclick="fnBusPopupReset();" />
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
                    <div style="text-align:center;font-size:14px;padding:50px;" id="initText3">
                        <b>달력 날짜를 선택하세요.</b>
                    </div>
                    <div id="divResList2"></div>
                </div>
            </div>
            <!-- .tab_container -->
        </article>
    </section>
</div>
<!-- #container -->
</div>

<iframe id="ifrmResize" name="ifrmResize" style="width:800px;height:400px;display:none;"></iframe>

<script>
$j(document).ready(function(){

});
</script>