<?php
include __DIR__.'/../../db.php';

include __DIR__.'/../../surf/surffunc.php';

$schText = $_REQUEST["param"];
$chk = $_REQUEST["chk"];

if($chk == 1){
    $arrChk = explode("|", $schText);
}else{
    $arrChk = explode("|", decrypt($schText));
}

$dateChk = $arrChk[0];
if(plusDate($dateChk, 2) <= date("Y-m-d")){
	//echo '<div style="text-align:center;font-size:14px;padding:50px 0px 50px 0px;">
	//			<b>빠른예약 조회 기간이 종료되었습니다.<br><br>로그인 후 [서프샵-예약관리] 메뉴를 이용해주세요.</b>
	//		</div>';
	//return;
}
?>
<script>
    var mobileuse = "";
</script>
<div id="wrap">
    <? include __DIR__.'/../../_layout_top.php'; ?>

    <link rel="stylesheet" type="text/css" href="/act/css/surfview.css">
    <link rel="stylesheet" type="text/css" href="/act/css/admin/admin_surf.css">
    <link rel="stylesheet" type="text/css" href="/act/css/admin/admin_common.css">
    <link rel="stylesheet" type="text/css" href="/act/css/jquery-ui.css" />
    
<?
if(count($arrChk) == 3){ //현재 예약건만 보기
    $MainNumber = trim($arrChk[1]);
    $shopseq = trim($arrChk[2]);

    include 'res_kakao_1.php';

}else{ //전체 예약건 임시로 보기
    $shopseq = trim($arrChk[1]);

    $select_query = "SELECT * FROM AT_PROD_MAIN WHERE seq = $shopseq AND use_yn = 'Y'";
    $result = mysqli_query($conn, $select_query);
    $rowMain = mysqli_fetch_array($result);

    $shopname = $rowMain["shopname"];
?>

<div class="top_area_zone">
    <section class="shoptitle">
        <div style="padding:6px;">
            <h1><?=$shopname?></h1>
            <div class="shopsubtitle">월별 예약목록 보기</div>
        </div>
    </section>

    <section class="notice">
        <div class="vip-tabwrap" style="position:initial;">
            <div id="tabnavi" class="fixed1" style="top: 49px;">
                <div class="vip-tabnavi">
                    <ul>
                        <li class="on"><a>예약건 안내</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="view_tab1" class="container">

            <section>
                <article id="right_article3" class="right_article4">
                    <?include 'res_kakaocalendar.php'?>
                </article>
                <aside id="left_article3" class="right_article4">
                    <div id="tab1" class="tab_content bd">
                    <form name="frmSearch" id="frmSearch" autocomplete="off">
                        <div class="gg_first" style="margin-top:0px;">예약검색</div>
                        <table class='et_vars exForm bd_tb' style="width:100%">
                            <colgroup>
                                <col style="width:65px;">
                                <col style="width:*;">
                                <col style="width:100px;">
                            </colgroup>
                            <tr>
                                <th>구분</th>
                                <td>
                                    <label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" value="8" checked="checked" style="vertical-align:-3px;" />입금완료</label>
                                    <label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" value="3" style="vertical-align:-3px;" />확정</label>
                                    <label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" value="2" style="vertical-align:-3px;" />임시확정/취소</label>
                                </td>
                            </tr>
                            <tr>
                                <th>검색기간</th>
                                <td>
                                    <input type="hidden" id="hidsearch" name="hidsearch" value="init">
                                    <input type="text" id="sDate" name="sDate" cal="sdate" readonly="readonly" value="" class="itx2" maxlength="7" style="width:66px;" >&nbsp;~
                                    <input type="text" id="eDate" name="eDate" cal="edate" readonly="readonly" value="" class="itx2" maxlength="7" style="width:66px;" >
                                    <input type="hidden" id="seq" name="seq" size="10" value="<?=$shopseq?>" class="itx">
                                    <input type="button" class="bd_btn" style="padding-top:4px;font-family: gulim,Tahoma,Arial,Sans-serif;" value="전체" onclick="fnDateReset();" />
                                </td>
                                
                            </tr>
                            <tr>
                                <th>검색어</th>
                                <td><input type="text" id="schText" name="schText" value="" class="itx2" style="width:140px;"></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center;"><input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="검색" onclick="fnSearchAdminKakao('shop/res_kakao_all.php');" /></td>
                            </tr>
                        </table>
                    </form>
                    </div>
                </aside>
            </section>
            
            <div id='rescontent'>
<?
    include 'res_kakao_all.php';
?>
            </div>
            <div>
                <div style="padding:10px 0 5px 0;font-size:12px;">
                    <a href="http://pf.kakao.com/_HxmtMxl" target="_blank" rel="noopener"><img src="/act/images/kakaochat.jpg" class="placeholder"></a>
                </div>
            </div>
        </div>
    </section>
</div>
<?
}
?>

                

</div>

<iframe id="ifrmResize" name="ifrmResize" style="width:800px;height:400px;display:none;"></iframe>

<script type="text/javascript" src="/act/js/admin_surf.js"></script>
<script type="text/javascript" src="/act/js/surfview_bus.js"></script>
<script type="text/javascript" src="/act/js/jquery.blockUI.js"></script>

<? include __DIR__.'/../../_layout_bottom.php'; ?>