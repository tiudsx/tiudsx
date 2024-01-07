<?php 
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/logininfo.php';
$shopseq = -2;
?>
<link rel="stylesheet" type="text/css" href="/act_2023/admin/_css/admin_bus.css">
<link rel="stylesheet" type="text/css" href="/act_2023/admin/_css/admin_common.css">
<link rel="stylesheet" type="text/css" href="/act_2023/front/_css/surfview.css">
<link rel="stylesheet" type="text/css" href="/act_2023/front/_css/jquery-ui.css" />

<script type="text/javascript" src="/act_2023/front/_js/jquery.blockUI.js"></script>
<script type="text/javascript" src="/act_2023/front/_js/common.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/_js/busday.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/admin/_js/common.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/admin/_js/admin_bus.js?v=<?=time()?>"></script>

<div class="bd_tl" style="width:100%;">
	<h1 class="ngeb clear"><i class="bg_color"></i>액트립 셔틀버스 등록관리</h1>
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

                    운행일 : <span id="res_busdate"></span>
                    <table class='et_vars exForm bd_tb' style="width:100%">
                        <colgroup>
                            <col style="width:auto;">
                            <col style="width:90px;">
                            <col style="width:100px;">
                            <col style="width:110px;">
                            <col style="width:80px;">
                            <col style="width:90px;">
                            <col style="width:80px;">
                        </colgroup>
                        <tr>
                            <th>버스정보</th>
                            <th>좌석수</th>
                            <th>가격</th>
                            <th>GPS</th>
                            <th>사용여부</th>
                            <th>예약가능</th>
                            <th><input type="button" class="btnsurfadd" style="width:40px;" value="추가" data-gubun="trbus"></th>
                        </tr>
                        <tr id="trbus" style="display:none;">
                            <td style="text-align:center;">
                                <input type="hidden" id="resseq" name="resseq[]" size="10" value="" class="itx">
                                <select id="res_busline" name="res_busline[]" class="select" sel="">
                                    <option value="" selected>행선지</option>
                                    <option value="">-------</option>
                                    <option value="YY">양양</option>
                                    <option value="DH">동해</option>
                                </select>
                                <select id="res_busgubun" name="res_busgubun[]" class="select" sel="">
                                    <option value="" selected>노선</option>
                                    <option value="">-------</option>
                                    <option value="SA">사당선</option>
                                    <option value="JO">종로선</option>
                                    <option value="">-------</option>
                                    <option value="AM">오후 복귀</option>
                                    <option value="PM">저녁 복귀</option>
                                </select>
                                <select id="res_busnum" name="res_busnum[]" class="select" sel="">
                                    <option value="" selected>호차</option>
                                    <option value="1">1호차</option>
                                    <option value="2">2호차</option>
                                    <option value="3">3호차</option>
                                    <option value="4">4호차</option>
                                </select>
                            </td>
                            <td style="text-align:center;">
                                <select id="res_seat" name="res_seat[]" class="select" sel="">
                                    <option value="44">44</option>
                                    <option value="45">45</option>
                                </select>
                            </td>
                            <td style="text-align:center;">
                                <input type="text" id="res_price" name="res_price[]" style="width:66px;" value="20000" class="itx2">
                            </td>
                            <td style="text-align:center;">
                                <select id="res_gpsname" name="res_gpsname[]" class="select" sel="">
                                    <option value="">-------</option>
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
                            <td style="text-align:center;">
                                <select id="res_channel" name="res_channel[]" class="select">
                                    <option value="N">전체</option>
                                    <option value="Y">타채널</option>
                                </select>
                            </td>
                            <td style="text-align:center;"><input type="button" class="btnsurfdel" style="width:40px;" value="삭제" onclick="fnBusDel(this);" ></td>
                        </tr>
                        <tr>
                            <td class="col-02" style="text-align:center;" colspan="8">
                                <input type="hidden" id="resparam" name="resparam" size="10" value="busMngadd" class="itx">
                                <input type="button" class="btnsurfdel" style="width:120px; height:40px;" value="저장" onclick="fnBusMngDataAdd('add');" id="Add"/>&nbsp;
                                <input type="button" class="btnsurfadd" style="width:120px; height:40px;" value="초기화" onclick="fnBusMngList($j('#hidselDate').val());" />
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