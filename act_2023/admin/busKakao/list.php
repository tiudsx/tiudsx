<?php 
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/logininfo.php';
$shopseq = 0;
?>
<link rel="stylesheet" type="text/css" href="/act_2023/admin/_css/admin_bus.css">
<link rel="stylesheet" type="text/css" href="/act_2023/admin/_css/admin_common.css">
<link rel="stylesheet" type="text/css" href="/act_2023/front/_css/surfview.css">
<link rel="stylesheet" type="text/css" href="/act_2023/front/_css/jquery-ui.css" />

<script type="text/javascript" src="/act_2023/front/_js/jquery.blockUI.js"></script>
<script type="text/javascript" src="/act_2023/front/_js/common.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/admin/_js/common.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/admin/_js/admin_busKakao.js?v=<?=time()?>"></script>

<script>
$j(document).ready(function(){
	fnSearchAdmin('busKakao/list_search_channel.php', '#mngKakaoSearch', 'N');
});
</script>

<div class="bd_tl" style="width:100%;">
	<h1 class="ngeb clear"><i class="bg_color"></i>액트립 셔틀버스 타채널관리</h1>
</div>

<div class="container" id="contenttop">
<div> 
<!-- .tab_container -->
    <div id="containerTab" class="areaRight">
        <ul class="tabs">
            <li class="active" rel="tab1">타채널예약</li>
            <li rel="tab2">HTML 맵핑</li>
            <li rel="tab3">정산관리</li>
        </ul>

        <!-- #container -->
        <div class="tab_container">
            <!-- #tab1 -->
            <div id="tab1" class="tab_content">
            <form name="frmResKakao" id="frmResKakao" autocomplete="off">
                <input type="hidden" id="resparam" name="resparam" value="reskakao">

                <table class='et_vars exForm bd_tb'>
                    <colgroup>
                        <col style="width: 170px;">
                        <col style="width: auto;">
                        <col style="width: 100px;">
                        <col style="width: 100px;">
                        <col style="width: 140px;">
                        <col style="width: 260px;">
                        <col style="width: 260px;">
                    </colgroup>
                    <tr>
                        <td colspan="7">
                            알림톡 발송 번호
                        </td>
                    </tr>
                    <tr>
                        <th>초기화</th>
                        <th>채널</th>
                        <th>노선</th>
                        <th>이름</th>
                        <th>연락처</th>
                        <th>이용일 (서울출발)</th>
                        <th>이용일 (서울복귀)</th>
                    </tr>
                    <tr>
                        <td style="text-align:center;">
                            <select id="datareset">
                                <option value="1">-- 날짜/인원 초기화 --</option>
                                <option value="2">-- 인원 초기화 --</option>
                                <option value="0">-- 날짜/인원 유지 --</option>
                            </select>
                        </td>
                        <td>
                            <select id="busgubun" name="busgubun" onchange="fnAdminBusGubun(this, 1);">
                                <option value="3">편도</option>
                                <option value="2">당일 왕복</option>
                                <option value="1">1박 왕복</option>
                            </select>
                            <select id="reschannel" name="reschannel" onchange="fnChannel(this);">
                                <option value="11">프립</option>
                                <option value="17" kakaoUrl="https://open.kakao.com/o/goYwKe5e">프립-마린</option>
                                <option value="20" kakaoUrl="https://open.kakao.com/o/gf4LMe5e">프립-인구</option>
                                <option value="21" kakaoUrl="https://open.kakao.com/o/g58J34ff">프립-서팩 동해</option>
                                <option value="22" kakaoUrl="https://open.kakao.com/o/g15tGdBf">프립-힐링캠프</option>
                                <option value="16">클룩</option>
                                <option value="7">네이버쇼핑</option>
                                <option value="15">서프존</option>
                                <option value="10">네이버예약</option>
                                <option value="12">마이리얼트립</option>
                                <option value="26" kakaoUrl="https://open.kakao.com/o/goYwKe5e">네이버-마린</option>
                                <option value="27" kakaoUrl="https://open.kakao.com/o/gf4LMe5e">네이버-인구</option>
                                <option value="28" kakaoUrl="https://open.kakao.com/o/g58J34ff">네이버-서팩 동해</option>
                                <option value="29" kakaoUrl="https://open.kakao.com/o/g15tGdBf">네이버-힐링캠프</option>
                                <option value="23">금진 브라보</option>
                                <option value="30">엑스크루</option>
                                <option value="31">모행</option>
                            </select>
                        </td>
                        <td style="text-align:center;">
                            <select id="resbus" name="resbus">
                                <option value="YY">-- 양양 --</option>
                                <option value="DH">-- 동해 --</option>
                            </select>
                        </td>
                        <td><input type="text" id="username" name="username" style="width:66px;" value="" class="itx2" maxlength="20" onkeyup="spacetrim(this);"></td>
                        <td><input type="text" id="userphone" name="userphone" style="width:100px;" value="" class="itx2" maxlength="20" onkeyup="spacetrim(this);"></td>
                        <td>
                            <select id="start_bus_gubun" name="start_bus_gubun" class="select" sel="">
                                <option value="ALL">전체</option>
                                <option value="SA">사당선</option>
                                <option value="JO">종로선</option>
                            </select>
                            <input type="text" id="start_day" name="start_day" cal="sdate2" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7" >
                            <select id="start_cnt" name="start_cnt" onchange="fnseatcheck(this, 1);">
                            <?for ($i=0; $i < 20; $i++) { 
                                echo '<option value="'.$i.'">'.$i.'명</option>';
                            }?>
                            </select>
                        </td>
                        <td id="td_return">
                            <select id="return_bus_gubun" name="return_bus_gubun" class="select" sel="">
                                <option value="ALL">전체</option>
                                <option value="AM">오후 출발</option>
                                <option value="PM">저녁 출발</option>
                            </select>
                            <input type="text" id="return_day" name="return_day" cal="edate2" readonly="readonly" style="width:66px;" value="" class="itx2" maxlength="7" >
                            <select id="return_cnt" name="return_cnt" onchange="fnseatcheck(this. 2);">
                            <?for ($i=0; $i < 20; $i++) { 
                                echo '<option value="'.$i.'">'.$i.'명</option>';
                            }?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7" style="text-align:center;">
                            <input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="알림톡 발송" onclick="fnResKakaoAdmin();" />
                            &nbsp; &nbsp;
                            <input type="button" class="gg_btn res_btn_color2" style="width:80px; height:40px;" value="초기화" onclick='$j("#frmResKakao")[0].reset();' />
                        </td>
                    </tr>
                </table>
                </form>

                <div class="gg_first">알림톡 발송 정보 <input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:40px; height:20px;" value="조회" onclick="fnSearchAdmin('busKakao/list_search_channel.php', '#mngKakaoSearch', 'N');" /></div>
                <div id="mngKakaoSearch"></div>
            </div>

            <!-- #tab2 -->
            <div id="tab2" class="tab_content" style="display:none;">
            
                <table class='et_vars exForm bd_tb' style="width:900px;">
                    <tr>
                        <td colspan="4">
                            데이터 맵핑
                        </td>
                    </tr>
                    <tr>
                        <th>채널</th>
                        <th>타채널 HTML</th>
                        <th>맵핑 데이터</th>
                        <th></th>
                    </tr>
                    <tr>
                        <td style="text-align:center;">
                            <select id="reschannel">
                                <option value="frip">프립</option>
                                <option value="klook">클룩</option>
                                <option value="naver">네이버</option>
                            </select>
                        </td>
                        <td style="text-align:center;">
                            <textarea id="html_1" cols="50" rows="7"></textarea>
                        </td>
                        <td style="text-align:center;">
                            <textarea id="html_2" cols="50" rows="7"></textarea>
                        </td>
                        <td style="text-align:center;"><input type="button" class="gg_btn res_btn_color2" style="width:40px; height:20px;" value="맵핑" onclick="fnGetJson();" /></td>
                    </tr>
                </table>
                
                <div id="divCopy" style="display: none;"></div>
                    <div id="divCopyList">

                        <table class='et_vars exForm bd_tb' id="tbCopyList">
                        </table>

                        <table class='et_vars exForm bd_tb' id="tbCopyList2" style="display:none;">
                            <colgroup>
                                <col style="width:8%">
                                <col style="width:auto;">
                                <col style="width:10%">
                                <col style="width:18%">
                                <col style="width:20%">
                                <col style="width:8%">
                                <col style="width:8%">
                                <col style="width:8%">
                            </colgroup>
                            <tr>
                                <th>번호</th>
                                <th>노선</th>
                                <th>이름</th>
                                <th>연락처</th>
                                <th>이용일 (인원)</th>
                                <th>임시</th>
                                <th>확정</th>
                                <th>처리</th>
                            </tr>
                        </table>
                    </div>
                </div>
            
            
            <!-- #tab3 -->
            <div id="tab3" class="tab_content" style="display:none;">
                <?include 'list_cal.php'?>
            </div>
        </div>
        <!-- .tab_container -->
    </div>
</div>
<!-- #container -->
</div>

<input type="hidden" id="hidselDate" value="">
<iframe id="ifrmResize" name="ifrmResize" style="width:800px;height:400px;display:none;"></iframe>