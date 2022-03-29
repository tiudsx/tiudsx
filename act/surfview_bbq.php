<? include 'db.php'; ?>

<?
$param_mid = $_REQUEST["mid"];
$reqView = $_REQUEST["view"];

if($param_mid == ""){
	$param = str_replace("/", "", $_SERVER["REQUEST_URI"]);

	if (!empty(strpos($_SERVER["REQUEST_URI"], '?'))){
		$param = substr($param, 0, strpos($_SERVER["REQUEST_URI"], '?') - 1);
	}

	$param = explode('_', $param)[0];
}else{
	$param = $param_mid;
}

// if($param == "bbq_yy"){ //죽도 바베큐
//     $reqSeq = 13;

if($param == "bbq_pkg"){ //동해 패키지
    $reqSeq = 184;
}else{ //동해 바베큐
    $reqSeq = 15;
}

$select_query = "SELECT * FROM AT_PROD_MAIN WHERE seq = $reqSeq AND use_yn = 'Y'";
$result = mysqli_query($conn, $select_query);
$rowMain = mysqli_fetch_array($result);

$bbqtitle = $rowMain["shopname"];
$bbqsubinfo = $rowMain["sub_info"];
$coupon_yn = $rowMain["coupon_yn"];

// 옵션 매진여부 확인
$select_query = "SELECT a.*, b.optcode, b.optname FROM `AT_PROD_OPT_SOLDOUT` as a INNER JOIN AT_PROD_OPT as b
					ON a.optseq = b.optseq
					WHERE b.seq = $reqSeq AND b.use_yn = 'Y' ORDER BY a.soldout_date, a.optseq";
$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);

if($count > 0){
	$SoldoutList = "";
	$Presoldoutdate = "";
	$x = 0;

	while ($rowSold = mysqli_fetch_assoc($result_setlist)){
		$soldoutdate = $rowSold['soldout_date'];

		if($soldoutdate != $Presoldoutdate && $x > 0){
			$SoldoutList .= "main['".$Presoldoutdate."'] = sub;";
		}

		if($soldoutdate == $Presoldoutdate){
			$i++;
		}else{
			$i = 0;
		}

		$x++;
		$Presoldoutdate = $rowSold['soldout_date'];

		if($i == 0){
			$SoldoutList .= "sub = new Object();";
		}

		$soldoutdate = $rowSold["soldout_date"];
		$optseq = $rowSold["optseq"];
		$opt_sexM = $rowSold["opt_sexM"];
		$opt_sexW = $rowSold["opt_sexW"];
		$optcode = $rowSold["optcode"];
		$optname = $rowSold["optname"];
		
		$SoldoutList .= "sub['$optseq'] = {type: $optcode, opt_sexM: '$opt_sexM', opt_sexW: '$opt_sexW', optseq: $optseq, optname: '$optname' }; ";
	}
	
	$SoldoutList .= "main['".$Presoldoutdate."'] = sub;";
}

$select_query = 'SELECT * FROM `AT_PROD_OPT` where seq = '.$reqSeq.' AND use_yn = "Y" ORDER BY ordernum';
$result_setlist = mysqli_query($conn, $select_query);

$arrOpt = array();
$arrOptT = array();
while ($rowOpt = mysqli_fetch_assoc($result_setlist)){
	$arrOpt[$rowOpt["optcode"]][$rowOpt["optseq"]] = array("optseq" => $rowOpt["optseq"], "optname" => $rowOpt["optname"], "opttime" => $rowOpt["opttime"], "opt_sexM" => $rowOpt["opt_sexM"], "opt_sexW" => $rowOpt["opt_sexW"], "sell_price" => $rowOpt["sell_price"], "opt_info" => $rowOpt["opt_info"], "stay_day" => $rowOpt["stay_day"]);

	$arrOptT[$rowOpt["optcode"]] = $rowOpt["optcode"];
}

$sLng = $rowMain["shop_lat"];
$sLat = $rowMain["shop_lng"];

//연락처 모바일 여부
if(Mobile::isMobileCheckByAgent()) $inputtype = "number"; else $inputtype = "text";
?>

<div id="wrap">
    <? include '_layout_top.php'; ?>

    <link rel="stylesheet" href="css/surfview.css">

    <div class="top_area_zone">
        <section class="shoptitle">
            <div style="padding:6px;">
                <h1><?=$bbqtitle?></h1>
                <a class="reviewlink">
                    <span class="reviewcnt">구매 <b><?=number_format($rowMain["sell_cnt"])?></b>개</span>
                </a>
                <div class="shopsubtitle"><?=$bbqsubinfo?></div>
            </div>
        </section>

        <section class="notice">
            <div class="vip-tabwrap">
                <div id="tabnavi" class="fixed1" style="top: 49px;">
                    <div class="vip-tabnavi">
                        <ul>
                            <li class="on"><a onclick="fnResView(true, '#content_tab1', 69, this);">상세설명</a></li>
                            <li onclick="fnResView(true, '#shopmap', 500, this);"><a>위치안내</a></li>
                            <li onclick="fnResView(true, '#cancelinfo', 69, this);"><a>취소/환불</a></li>
                            <li onclick="fnResView(false, '#view_tab3', 69, this);"><a>예약하기</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div id="view_tab1">
                <div class="noticeline" id="content_tab1">
                    <!-- <p class="noticetxt">예약안내</p> -->
                    <article>
                        <p class="noticesub">예약안내</p>
                        <ul>
                            <li class="litxt">이용상품 예약 > 입금안내 카톡 발송 > 무통장 입금 > 확정안내 카톡 발송</li>
                            <li class="litxt">무통장 입금시 예약자와 입금자명이 동일해야합니다.</li>
                            <li class="litxt">예약하신 이용일, 바베큐파티 장소를 꼭 확인해주세요.</li>
                            <li class="litxt">
                                <span>    
                                액트립 바베큐파티 이용금액은 부가세 별도금액입니다.<br>
                                <span>현금영수증 신청은 이용일 이후 <label style="color:#059bc0;">[카카오 채널 @액트립]</label> 에서 신청가능합니다.</span>
                                </span>
                            </li>
                        </ul>
                    </article>
                    <article>
                        <p class="noticesub">바베큐파티 이용안내</p>
                        <ul>
                            <?if($param == "bbq_yy"){?>
                            <li class="litxt">맥주나 음료는 제공되지 않으나 가지고 오셔서 드셔도 됩니다.</li>
                            <li class="litxt">메뉴는 인원에 따라 변경될 수 있습니다.</li>
                            <li class="litxt">미성년자 이용불가 / 신분증 확인!!</li>
                            <li class="litxt">양양 죽도해변 바베큐파티는 선착순 진행으로 인원 마감시 참여가 불가능합니다.</li>
                            <?}else{?>
                            <li class="litxt">외부음식은 반입불가이며, 맥주나 음료는 매장내에서 판매합니다.</li>
                            <li class="litxt">메뉴는 인원에 따라 변경될 수 있습니다.</li>
                            <li class="litxt">미성년자 이용불가 / 신분증 확인!!</li>
                            <li class="litxt">솔게스트하우스 동해서핑점 바베큐파티는 선착순 진행으로 인원 마감시 참여가 불가능합니다.</li>
                            <?}?>
                        </ul>
                    </article>
                </div>
                <div class="contentimg">
                    <?
                    include 'surfview_content.php';

                    if($rowMain["content_type"] == "html"){
                        echo $rowMain["content"];
                    }else{
                        include 'surfview/'.$rowMain["content"];
                    }
                    ?>
                </div>
                <div id="shopmap">
                    <iframe scrolling="no" frameborder="0" id="ifrmMap" name="ifrmMap" style="width:100%;height:490px;" src="surf/surfmap.html"></iframe>

                    <div style="padding:10px 0 5px 0;font-size:12px;">
                        <a href="http://pf.kakao.com/_HxmtMxl" target="_blank" rel="noopener"><img src="images/kakaochat.jpg" class="placeholder"></a>
                    </div>
                </div>
                <div class="noticeline2" id="cancelinfo">
                    <p class="noticetxt">취소/환불 안내</p>
                    <article>
                        <p class="noticesub">취소 안내</p>
                        <ul>
                            <li class="litxt">1시간 이내 미입금시 자동취소됩니다.</li>
                            <li class="litxt">우천시 바베큐파티는 취소 될 수 있습니다.</li>
                            <li class="litxt">기상악화 및 천재지변으로 인하여 이용이 불가능할 경우 전액환불됩니다.</li>
                        </ul>
                    </article>
                    <article>
                        <p class="noticesub">환불 규정안내</p>
                        <ul>
                            <li class="refund"><img src="images/refund.jpg" alt=""></li>
                        </ul>
                    </article>
                </div>
            </div>
            <div id="view_tab3" class="view_tab3" style="min-height: 800px;display: none;">
                <div id="tour_calendar" style="display: block;padding:10px 4px;">
                </div>

                <div id="initText" class="write_table" style="text-align: center;font-size:14px;padding-top:20px;padding-bottom:20px;display:;">
                    <b>예약날짜를 선택하세요.</b>
                </div>

                <div id="lessonarea" style="display:none;padding-left:5px;padding-right:5px;">
                <form id="frmResList">
                    <div class="fixed_wrap3" style="display:;">
                        <ul class="cnb3 btnColor" style="padding-inline-start:0px;">
                        <?if($arrOptT["lesson"] != null){?>
                            <li id="0"><a onclick="fnSurfList(this, 0);" style="padding:10px 15px 0px 15px;">강습</a></li>
                        <?}
                        
                        if($arrOptT["rent"] != null){?>
                            <li id="1"><a onclick="fnSurfList(this, 1);" style="padding:10px 15px 0px 15px;">렌탈</a></li>
                        <?}
                        
                        if($arrOptT["pkg"] != null){?>
                            <li id="2"><a onclick="fnSurfList(this, 2);" style="padding:10px 15px 0px 15px;">할인패키지</a></li>
                        <?}

                        if($arrOptT["bbq"] != null){?>
                            <li id="3"><a onclick="fnSurfList(this, 3);" style="padding:10px 15px 0px 15px;">바베큐파티</a></li>
                        <?}?>
                            
                        </ul>
                    </div>

                    <div class="bd" area="shopListArea" style="display:none;">
                        <!-- <div class="gg_first" style="padding-top:10px;">강습예약</div> -->
                        <div id="divsellesson" style="text-align:center;font-size:14px;padding:50px;display:none;">
                            <b>강습이 매진되어 예약이 불가능합니다.</b>
                        </div>
                        <table class="et_vars exForm bd_tb" style="width:100%;" id="tbsellesson">
                            <colgroup>
                                <col style="width:80px;">
                                <col style="width:auto;">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <th style="text-align:center;">종류</th>
                                    <td>
                                        <?
                                        $i = 0;
                                        foreach($arrOpt["lesson"] as $arrlesson){
                                            $sel1 .= '<option soldout="'.$arrlesson["optseq"].'" opt_info="'.$arrlesson["opt_info"].'" stay_day="'.$arrlesson["stay_day"].'" opt_sexM="N" opt_sexW="N" opttime="'.$arrlesson["opttime"].'" value="'.$arrlesson["optseq"].'|'.$arrlesson["optname"].'|'.$arrlesson["sell_price"].'">'.$arrlesson["optname"].'</option>';
                                            
                                            if($i == 0){
                                                foreach(explode("|", $arrlesson["opttime"]) as $arrtime){
                                                    if($arrtime != ""){
                                                        $sel2 .= '<option value="'.$arrtime.'">'.$arrtime.'</option>';
                                                    }
                                                }

                                                $sel3 = $arrlesson["opt_sexM"];
                                                $sel4 = $arrlesson["opt_sexW"];
                                                $opt_info = $arrlesson["opt_info"];
                                            }
                                        
                                            $i++;
                                        }
                                        ?>
                                        <select id="sellesson" name="sellesson" class="select" onchange="fnResChange('sellesson');">
                                            <?=$sel1?>
                                        </select>

                                        <select id="hidsellesson" style="display:none;">
                                            <?=$sel1?>
                                        </select>
                                        <select id="sellessonTime" name="sellessonTime" class="select">
                                            <?=$sel2?>
                                        </select>	
                                    </td>
                                </tr>
                                <tr>
                                    <th style="text-align:center;">인원</th>
                                    <td>
                                        <span>
                                            남:
                                            <span style="display:none;"><select class="select" class="soldsel"><option value="0" style="color:red; background:#EEFF00;">매진</option></select></span>
                                            <select id="sellessonM" name="sellessonM" class="select">
                                            <?for($i=0;$i<=$sel3;$i++){?>
                                                <option value="<?=$i?>"><?=$i?></option>
                                            <?}?>
                                            </select>명&nbsp;
                                        </span>
                                        <span>
                                            여:
                                            <span style="display:none;"><select class="select" class="soldsel"><option value="0" style="color:red; background:#EEFF00;">매진</option></select></span>
                                            <select id="sellessonW" name="sellessonW" class="select">
                                            <?for($i=0;$i<=$sel4;$i++){?>
                                                <option value="<?=$i?>"><?=$i?></option>
                                            <?}?>
                                            </select>명
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"><img src="images/icon/check.svg" alt="" style="width: 17px; vertical-align: middle; margin-right: 8px;"><label id="stayText"></label></td>
                                </tr>
                            <tbody>
                        </table>
                        <div style="text-align:center;"><input type="button" class="btnsurfadd" value="추가하기" onclick="fnSurfAdd('lesson', this);"></div>
                    </div>

                    <div class="bd" area="shopListArea" style="display:none;">
                        <!-- <div class="gg_first" style="padding-top:10px;">렌탈예약</div> -->
                        <div id="divselRent" style="text-align:center;font-size:14px;padding:50px;display:none;">
                            <b>렌탈예약이 매진되어 예약이 불가능합니다.</b>
                        </div>
                        <table class="et_vars exForm bd_tb" style="width:100%;" id="tbselRent">
                            <colgroup>
                                <col style="width:80px;">
                                <col style="width:auto;">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <th style="text-align:center;">종류</th>
                                        <td>
                                        <?
                                        $i = 0;
                                        $sel1 = "";
                                        $sel2 = "";
                                        $sel3 = "";
                                        $sel4 = "";
                                        $opt_info = "";
                                        foreach($arrOpt["rent"] as $arrlesson){
                                            $sel1 .= '<option soldout="'.$arrlesson["optseq"].'"  opt_info="'.$arrlesson["opt_info"].'" stay_day="'.$arrlesson["stay_day"].'" opt_sexM="N" opt_sexW="N" value="'.$arrlesson["optseq"].'|'.$arrlesson["optname"].'|'.$arrlesson["sell_price"].'">'.$arrlesson["optname"].'</option>';
                                            
                                            if($i == 0){
                                                $sel3 = $arrlesson["opt_sexM"];
                                                $sel4 = $arrlesson["opt_sexW"];
                                                $opt_info = $arrlesson["opt_info"];
                                            }
                                        
                                            $i++;
                                        }
                                        ?>

                                        <select id="selRent" name="selRent" class="select" onchange="fnResChange('selRent');">
                                            <?=$sel1?>
                                        </select>
                                        <select id="hidselRent" style="display:none;">
                                            <?=$sel1?>
                                        </select>
                                    </td>
                                    </tr>
                                <tr>
                                    <th style="text-align:center;">인원</th>
                                    <td>
                                        <span>
                                            남:
                                            <span style="display:none;"><select class="select" class="soldsel"><option value="0" style="color:red; background:#EEFF00;">매진</option></select></span>
                                            <select id="selRentM" name="selRentM" class="select">
                                            <?for($i=0;$i<=$sel3;$i++){?>
                                                <option value="<?=$i?>"><?=$i?></option>
                                            <?}?>
                                            </select>명&nbsp;
                                        </span>
                                        <span>
                                            여:
                                            <span style="display:none;"><select class="select" class="soldsel"><option value="0" style="color:red; background:#EEFF00;">매진</option></select></span>
                                            <select id="selRentW" name="selRentW" class="select">
                                            <?for($i=0;$i<=$sel4;$i++){?>
                                                <option value="<?=$i?>"><?=$i?></option>
                                            <?}?>
                                            </select>명
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"><img src="images/icon/check.svg" alt="" style="width: 17px; vertical-align: middle; margin-right: 8px;"><label id="rentText"></label></td>
                                </tr>
                            <tbody>
                        </table>
                        <div style="text-align:center;"><input type="button" class="btnsurfadd" value="추가하기" onclick="fnSurfAdd('rent', this);"></div>
                    </div>
                    
                    <div class="bd" area="shopListArea" style="display:none;">
                        <!-- <div class="gg_first" style="padding-top:10px;">패키지예약</div> -->
                        <div id="divselPkg" style="text-align:center;font-size:14px;padding:50px;display:none;">
                            <b>할인패키지 예약이 매진되어 예약이 불가능합니다.</b>
                        </div>
                        <table class="et_vars exForm bd_tb" style="width:100%;" id="tbselPkg">
                            <colgroup>
                                <col style="width:80px;">
                                <col style="width:auto;">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <th style="text-align:center;">종류</th>
                                    <td>
                                        <?
                                        $i = 0;
                                        $sel1 = "";
                                        $sel2 = "";
                                        $sel3 = "";
                                        $sel4 = "";
                                        $opt_info = "";
                                        foreach($arrOpt["pkg"] as $arrlesson){
                                            $sel1 .= '<option soldout="'.$arrlesson["optseq"].'" opt_info="'.$arrlesson["opt_info"].'" stay_day="'.$arrlesson["stay_day"].'"  optsexM="N" optsexW="N" opttime="'.$arrlesson["opttime"].'" value="'.$arrlesson["optseq"].'|'.$arrlesson["optname"].'|'.$arrlesson["sell_price"].'">'.$arrlesson["optname"].'</option>';
                                            
                                            if($i == 0){
                                                foreach(explode("|", $arrlesson["opttime"]) as $arrtime){
                                                    if($arrtime != ""){
                                                        $sel2 .= '<option value="'.$arrtime.'">'.$arrtime.'</option>';
                                                    }
                                                }

                                                $sel3 = $arrlesson["opt_sexM"];
                                                $sel4 = $arrlesson["opt_sexW"];
                                                $opt_info = $arrlesson["opt_info"];
                                            }
                                        
                                            $i++;
                                        }
                                        ?>
                                        <select id="selPkg" name="selPkg" class="select" onchange="fnResChange('selPkg');">
                                            <?=$sel1?>
                                        </select>

                                        <select id="hidselPkg" style="display:none;">
                                            <?=$sel1?>
                                        </select>
                                        <select id="selPkgTime" name="selPkgTime" class="select">
                                            <?=$sel2?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="text-align:center;">인원</th>
                                    <td>
                                        <span>
                                            남:
                                            <span style="display:none;"><select class="select"><option value="0" style="color:red; background:#EEFF00;">매진</option></select></span>
                                            <select id="selPkgM" name="selPkgM" class="select">
                                            <?for($i=0;$i<=$sel3;$i++){?>
                                                <option value="<?=$i?>"><?=$i?></option>
                                            <?}?>
                                            </select>명&nbsp;
                                        </span>
                                        <span>
                                            여:
                                            <span style="display:none;"><select class="select"><option value="0" style="color:red; background:#EEFF00;">매진</option></select></span>
                                            <select id="selPkgW" name="selPkgW" class="select">
                                            <?for($i=0;$i<=$sel4;$i++){?>
                                                <option value="<?=$i?>"><?=$i?></option>
                                            <?}?>
                                            </select>명
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"><img src="images/icon/check.svg" alt="" style="width: 17px; vertical-align: middle; margin-right: 8px;"><label id="pkgText"></label></td>
                                </tr>
                            <tbody>
                        </table>
                        <div style="text-align:center;"><input type="button" class="btnsurfadd" value="추가하기" onclick="fnSurfAdd('pkg', this);"></div>
                    </div>

                    <div class="bd" area="shopListArea" style="display:none;">
                        <!-- <div class="gg_first" style="padding-top:10px;">바베큐예약</div> -->
                        <div id="divselBBQ" style="text-align:center;font-size:14px;padding:50px;display:none;">
                            <b>바베큐예약이 매진되어 예약이 불가능합니다.</b>
                        </div>
                        <table class="et_vars exForm bd_tb" style="width:100%;" id="tbselBBQ">
                            <colgroup>
                                <col style="width:80px;">
                                <col style="width:auto;">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <th style="text-align:center;">종류</th>
                                    <td>
                                        <?
                                        $i = 0;
                                        $sel1 = "";
                                        $sel2 = "";
                                        $sel3 = "";
                                        $sel4 = "";
                                        $opt_info = "";
                                        foreach($arrOpt["bbq"] as $arrlesson){
                                            $sel1 .= '<option soldout="'.$arrlesson["optseq"].'" opt_info="'.$arrlesson["opt_info"].'" opt_sexM="N" opt_sexW="N" value="'.$arrlesson["optseq"].'|'.$arrlesson["optname"].'|'.$arrlesson["sell_price"].'">'.$arrlesson["optname"].'</option>';
                                            
                                            if($i == 0){
                                                $sel3 = $arrlesson["opt_sexM"];
                                                $sel4 = $arrlesson["opt_sexW"];
                                                $opt_info = $arrlesson["opt_info"];
                                            }
                                        
                                            $i++;
                                        }
                                        ?>
                                        <select id="selBBQ" name="selBBQ" class="select" onchange="fnResChange('selBBQ');">
                                            <?=$sel1?>
                                        </select>

                                        <select id="hidselBBQ" style="display:none;">
                                            <?=$sel1?>
                                        </select>
                                        <input type="hidden" id="strBBQDate" name="strBBQDate" readonly="readonly" value="" class="itx" cal="sdate" size="7" maxlength="7">		
                                    </td>
                                </tr>
                                <tr>
                                    <th style="text-align:center;">인원</th>
                                    <td>
                                        <span>
                                            남:
                                            <span style="display:none;"><select class="select" class="soldsel"><option value="0" style="color:red; background:#EEFF00;">매진</option></select></span>
                                            <select id="selBBQM" name="selBBQM" class="select">
                                            <?for($i=0;$i<=$sel3;$i++){?>
                                                <option value="<?=$i?>"><?=$i?></option>
                                            <?}?>
                                            </select>&nbsp;
                                        </span>
                                        <span>
                                            여:
                                            <span style="display:none;"><select class="select" class="soldsel"><option value="0" style="color:red; background:#EEFF00;">매진</option></select></span>
                                            <select id="selBBQW" name="selBBQW" class="select">
                                            <?for($i=0;$i<=$sel4;$i++){?>
                                                <option value="<?=$i?>"><?=$i?></option>
                                            <?}?>
                                            </select>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"><img src="images/icon/check.svg" alt="" style="width: 17px; vertical-align: middle; margin-right: 8px;"><label id="bbqText"></label></td>
                                </tr>
                            <tbody>
                        </table>
                        <div style="text-align:center;"><input type="button" class="btnsurfadd" value="추가하기" onclick="fnSurfAdd('bbq', this);"></div>
                    </div>                  
                </form>
                </div>

                <form id="frmRes" method="post" target="ifrmResize" autocomplete="off" style="display:none;">
				<span>
					<input type="hidden" id="resselDate" name="resselDate" value="" />
					<input type="hidden" id="resparam" name="resparam" value="SurfShopI" />
					<input type="hidden" id="shopseq" name="shopseq" value="<?=$reqSeq?>" />
					<input type="hidden" id="resNumAll" name="resNumAll" value="" />
				</span>
                <div class="bd" style="padding:0 4px;" id="divConfirm">
                    <p class="restitle" style="padding-top:30px;">신청한 예약 정보</p>
                    <table class="et_vars exForm bd_tb " style="width:100%;margin-bottom:5px;">
                        <colgroup>
                            <col style="width:auto;">
                            <col style="width:70px;">
                            <col style="width:35px;">
                        </colgroup>
                        <tbody id="surfAdd">
                        </tbody>
                    </table>

                    <p class="restitle">예약자 정보</p>
                    <table class="et_vars exForm bd_tb bustext" style="width:100%;margin-bottom:5px;">
                        <colgroup>
                            <col style="width:100px;">
                            <col style="width:auto;">
                        </colgroup>
                        <tbody>
                            <tr>
                                <th><em>*</em> 이름</th>
                                <td><input type="text" id="userName" name="userName" value="<?=$user_name?>" class="itx" maxlength="15"></td>
                            </tr>
                            <tr style="display:none;">
                                <th><em>*</em> 아이디</th>
                                <td><input type="text" id="userId" name="userId" value="<?=$user_id?>" class="itx" maxlength="30" readonly></td>
                            </tr>
                            <tr>
                                <th><em>*</em> 연락처</th>
                                <td>
                                    <input type="<?=$inputtype?>" name="userPhone1" id="userPhone1" value="<?=$userphone[0]?>" size="3" maxlength="3" class="tel itx" style="width:50px;"> - 
                                    <input type="<?=$inputtype?>" name="userPhone2" id="userPhone2" value="<?=$userphone[1]?>" size="4" maxlength="4" class="tel itx" style="width:60px;"> - 
                                    <input type="<?=$inputtype?>" name="userPhone3" id="userPhone3" value="<?=$userphone[2]?>" size="4" maxlength="4" class="tel itx" style="width:60px;">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"> 이메일</th>
                                <td><input type="text" id="usermail" name="usermail" value="<?=$email_address?>" class="itx"></td>
                            </tr>
                            <tr <?if($coupon_yn == "N"){ echo "style='display:none;'"; }?>>
                                <th scope="row"> 쿠폰코드</th>
                                <td>
                                    <input type="text" id="coupon" name="coupon" value="" size="10" class="itx" maxlength="10">
                                    <input type="hidden" id="couponcode" name="couponcode" value="">
                                    <input type="hidden" id="couponprice" name="couponprice" value="0">
                                    <input type="button" class="gg_btn gg_btn_grid gg_btn_color" style="width:50px; height:24px;" value="적용" onclick="fnCouponCheck(this);" />
                                    <span id="coupondis" style="display:none;"><br></span>
                                </td>
                            </tr>
                            <tr>
                                <th>요청사항</th>
                                <td>
                                    <textarea name="etc" id="etc" rows="8" cols="42" style="margin: 0px; width: 97%; height: 100px;resize:none;"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th>총 결제금액</th>
                                <td><span id="lastPrice" style="font-weight:700;color:red;">0원</span><span id="lastcouponprice"></span></td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="restitle">약관 동의</p>
                    <table class="et_vars exForm bd_tb exForm" width="100%">
                        <tbody>
                            <tr>
                                <td>
                                    <input type="checkbox" id="chk8" name="chk8"> <strong>예약할 상품설명에 명시된 내용과 사용조건을 확인하였으며, 취소. 환불규정에 동의합니다.</strong> (필수동의)
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" id="chk9" name="chk9"> <strong>개인정보 수집이용 동의 </strong> <a href="/act/clause/privacy.html" target="_blank" style="float:none;display: inline;">[내용확인]</a> (필수동의)
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="padding:10px;display:; text-align:center;" id="divBtnRes">
                    <div>
                        <input type="button" class="gg_btn gg_btn_grid gg_btn_color" style="width:200px; height:44px;" value="예약하기" onclick="fnSurfSave();" />
                    </div>
                </div>
                </form>
            </div>
        </section>
    </div>
</div>

<iframe id="ifrmResize" name="ifrmResize" style="width:100%;height:400px;display:none;"></iframe>
<div class="con_footer">
    <div class="fixedwidth resbottom">
        <img src="https://surfenjoy.cdn3.cafe24.com/button/btnReserve.png" id="slide1"> 
    </div>
    <div id="sildeing" style="position:absolute;bottom:80px;display: none;">
    </div>
</div>

<? include '_layout_bottom.php'; ?>

<script src="js/surf.js"></script>
<script src="js/surfview.js"></script>
<script src="js/surfview_shop.js"></script>
<script>
$j("#tour_calendar").load("/act/surf/surfview_calendar.php?selDate=<?=str_replace("-", "", date("Y-m-d"))?>&seq=<?=$reqSeq?>");

$j(document).ready(function() {
<?if($reqView == 1){ echo '$j(".vip-tabnavi li").eq(1).click();'; }?>
});

var mapView = 1;
var sLng = "<?=$rowMain["shoplat"]?>";
var sLat = "<?=$rowMain["shoplng"]?>";
var MARKER_SPRITE_X_OFFSET = 29,
    MARKER_SPRITE_Y_OFFSET = 50,
    MARKER_SPRITE_POSITION2 = {
        '<?=$rowMain["shopname"]?>': [0, MARKER_SPRITE_Y_OFFSET * 3, sLng, sLat, '<?=$rowMain["shopaddr"]?>', '구매 <b><?=number_format($rowMain["sell_cnt"])?></b>개', 0, <?=$reqSeq?>, '<?=$rowMain["shop_img"]?>', '<?=$rowMain["categoryname"]?>', '<?=$rowMain["shopname"]?>']
    };

var main = new Object();
<?=$SoldoutList?>
</script>