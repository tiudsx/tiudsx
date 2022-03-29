<? include 'db.php'; ?>

<?
include __DIR__.'/surf/surffunc.php';

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

if($param == "surfbus_yy"){ //양양 셔틀버스
    $shopseq = 7;
    $pointurl = "surf/surfview_bus_tab3.html";
}else{ //동해 셔틀버스
    $shopseq = 14;
    $pointurl = "surf/surfview_bus_tab3_2.html";
}

//"surfbus_yy?param=".urlencode(encrypt(date("Y-m-d").'|'.$coupon_code.'|resbus|'.$resDate1.'|'.$resDate2.'|'.$resbusseat1.'|'.$resbusseat2))
$arrChannel = $_REQUEST["param"];
if($arrChannel != ""){
    $arrChk = explode("|", decrypt($arrChannel));
    $dateChk = $arrChk[0];
    $coupon_code = $arrChk[1];  //쿠폰코드
    $codeChk = $arrChk[2];  //예약코드 구분
    $resDate1 = $arrChk[3];  //서울 > 양양
    $resbusseat1 = $arrChk[5];  //인원
    $resDate2 = $arrChk[4];  //양양 < 서울
    $resbusseat2 = trim($arrChk[6]);  //인원
    $resusername = trim($arrChk[7]);  //이름
    $resusertel = str_replace("-", "", trim($arrChk[8]));  //연락처
    $resusertel1 = substr($resusertel, 0, 3);
    $resusertel2 = substr($resusertel, 3, 4);
    $resusertel3 = substr($resusertel, 7, 4);

    $daytype = 0;
    if($resbusseat1 > 0 && $resbusseat2 > 0){
        $daytype = 1;
    }
}

$select_query = "SELECT * FROM AT_PROD_MAIN WHERE seq = $shopseq AND use_yn = 'Y'";
$result = mysqli_query($conn, $select_query);
$rowMain = mysqli_fetch_array($result);

$bustitle = $rowMain["shopname"];
$bussubinfo = $rowMain["sub_info"];
$busData = explode("|", $rowMain["sub_tag"]);
$busgubun = $busData[0];
$sbusDate = $busData[1];

//연락처 모바일 여부
if(Mobile::isMobileCheckByAgent()) $inputtype = "number"; else $inputtype = "text";
?>
<div id="wrap">
    <? include '_layout_top.php'; ?>

    <link rel="stylesheet" type="text/css" href="css/surfview.css">
    <link rel="stylesheet" type="text/css" href="css/surfview_bus.css">
    <link rel="stylesheet" type="text/css" href="css/jquery-ui.css" />

    <div class="top_area_zone">
        <section class="shoptitle">
            <div style="padding:6px;">
                <h1><?=$bustitle?></h1>
                <a class="reviewlink">
                    <span class="reviewcnt">구매 <b><?=number_format($rowMain["sell_cnt"])?></b>개</span>
                </a>
                <div class="shopsubtitle"><?=$bussubinfo?></div>
            </div>
        </section>

        <section class="notice">
            <div class="vip-tabwrap">
                <div id="tabnavi" class="fixed1" style="top: 49px;">
                    <div class="vip-tabnavi">
                        <ul>
                            <li class="on" onclick="fnResViewBus(true, '#content_tab1', 70, this);"><a>상세설명</a></li>
                            <li onclick="fnResViewBus(false, '#view_tab2', 70, this);fnMapClick();"><a>정류장안내</a></li>
                            <li onclick="fnResViewBus(true, '#cancelinfo', 70, this);"><a>취소/환불</a></li>
                            <li onclick="fnResViewBus(false, '#view_tab3', 70, this);"><a>셔틀예약</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div id="view_tab1">
                <div class="noticeline" id="content_tab1">
                    <!-- <p class="noticetxt">예약안내</p> -->
                    
                    <!--article>
                        <p class="noticesub">셔틀버스 운행 중지 안내</p>
                        <ul>
                            <li class="litxt">코로나 확산으로 인하여, 액트립 셔틀버스 운행은 조기 중단합니다.</li>
                            <li class="litxt">다음시즌에 더욱 좋은모습으로 찾아뵙겠습니다.</li>
                        </ul>
                    </article-->
                    <article>
                        <p class="noticesub">셔틀버스 예약안내</p>
                        <ul>
                            <li class="litxt">무통장 입금시 예약자와 입금자명이 동일해야합니다.</li>
                            <li class="litxt">예약하신 이용일, 탑승정류장, 탑승시간을 꼭 확인해주세요.</li>
                            <li class="litxt">
                                <span>    
                                액트립 서핑버스 이용금액은 부가세 별도금액입니다.<br>
                                <span>현금영수증 신청은 이용일 이후 <label style="color:#059bc0;">[카카오 채널 @액트립]</label> 에서 신청가능합니다.</span>
                                </span>
                            </li>
                        </ul>
                    </article>
                    <article>
                        <p class="noticesub">탑승 및 이용안내</p>
                        <ul>
                            <li class="litxt">탑승시간 5분전에 예약하신 정류장으로 도착해주세요.</li>
                            <li class="litxt">교통상황으로 인해 셔틀버스가 지연 도착할 수 있으니 양해부탁드립니다.</li>
                            <li class="litxt">사전 신청하지 않는 정류장은 정차 및 하차하지 않습니다.</li>
                            <li class="litxt">
                                <span>    
                                기상악화로 인하여 서핑강습이 취소되어도 셔틀버스는 정상운행되며, 기존 환불정책으로 적용됩니다.<br>
                                <span style="color:red;">단, 액트립에서 서핑강습이 사전예약 확정 된 경우 별도 환불정책으로 적용됩니다.</span>
                                </span>
                            </li>
                        </ul>
                    </article>
                </div>
                <div class="contentimg">
                <!-- <img src="https://surfenjoy.cdn3.cafe24.com/act_notice/bus_notice.jpg" class="placeholder"> -->
                    <?include 'surfview_content.php';?>
                    
                    <img src="https://surfenjoy.cdn3.cafe24.com/act_content/<?=$param?>_step1.jpg" class="placeholder">
                    <img src="https://surfenjoy.cdn3.cafe24.com/act_content/<?=$param?>_step2.jpg" class="placeholder">
                    <img src="https://surfenjoy.cdn3.cafe24.com/act_content/<?=$param?>_step3.jpg" class="placeholder">
                    <img src="https://surfenjoy.cdn3.cafe24.com/act_content/<?=$param?>_step4.jpg" class="placeholder">
                </div>
                <div>
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
                            <li class="litxt">최소인원(25명) 모집이 안될 경우 운행이 취소될 수 있으며, 전액 환불됩니다.</li>
                            <li class="litxt">천재지변으로 인하여 셔틀버스 운행이 취소될 경우 전액환불됩니다.</li>
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
            <div id="view_tab2" style="display: none;min-height: 800px;">
            
                <? include $pointurl; ?>

            </div>
            <div id="view_tab3" class="view_tab3" style="min-height: 800px;display:none;">
            <form id="frmRes" method="post" target="ifrmResize" autocomplete="off">
                <span style="display:none;">
                    <br>resparam<input type="text" id="resparam" name="resparam" value="BusI" />
                    <br>userId<input type="text" id="userId" name="userId" value="<?=$user_id?>">
                    <br>shopseq<input type="text" id="shopseq" name="shopseq" value="<?=$shopseq?>">
                    <br>편도/왕복<input type="text" id="daytype" name="daytype" value="0">
                    <br>행성지<input type="text" id="busgubun" name="busgubun" value="<?=$busgubun?>">
                </span>
                
                <div id="resStep1">
                    <div class="busOption01" style="padding-bottom: 0px;">
                        <ul class="destination" id="ulDaytype" style="margin-bottom: 0px;">
                            <li><img src="images/viewicon/sign.svg" alt="">일정</li>
                            <li class="toYang on" onclick="fnBusDayType(0, this);">편도<i class="fas fa-chevron-right"></i></li>
                            <li class="toYang" onclick="fnBusDayType(1, this);">왕복<i class="fas fa-chevron-right"></i></li>
                        </ul>
                    </div>
                    <div class="busOption01" style="padding-bottom: 0px;" id="route">
                        <ul class="destination" id="ulroute" style="margin-bottom: 0px;">
                            <li><img src="images/viewicon/route.svg" alt="">행선지</li>
                        <?if($param == "surfbus_yy"){?>
                            <li class="toYang on" onclick="fnBusGubun('Y', this);">양양행<i class="fas fa-chevron-right"></i></li>
                            <li class="toYang" onclick="fnBusGubun('S', this);">서울행<i class="fas fa-chevron-right"></i></li>
                        <?}else{?>
                            <li class="toYang on" onclick="fnBusGubun('E', this);">동해행<i class="fas fa-chevron-right"></i></li>
                            <li class="toYang" onclick="fnBusGubun('A', this);">서울행<i class="fas fa-chevron-right"></i></li>
                        <?}?>
                        </ul>
                    </div>
                    <div id="layerbus1" class="busOption01" style="padding-top: 10px;">
                        <ul class="busDate" id="busdate">
                            <li><img src="images/viewicon/calendar.svg" alt="">이용일</li>
                            <li class="calendar"><input type="text" id="SurfBus" name="SurfBus" readonly="readonly" class="itx" cal="busdate" gubun="<?=$busgubun?>" ></li>
                        </ul>
                        <ul class="busLine" style="display: ;">
                            <li><img src="images/viewicon/bus.svg" alt="">노선</li>
                        </ul>
                        <ul class="busStop" id="buspointlist" style="display: none;">
                            <li id="buspointtext"></li>
                        </ul>
                        <ul class="busDate" id="sbusdate" style="display:none;">
                            <li><img src="images/viewicon/calendar.svg" alt="">출발일</li>
                            <li class="calendar"><input type="text" id="SurfBusS" name="SurfBusS" readonly="readonly" class="itx" cal="busdate" gubun="<?=$busgubun?>"></li>
                        </ul>
                        <ul class="busLine" style="display: none;">
                            <li><img src="images/viewicon/bus.svg" alt="">출발노선</li>
                        </ul>
                        <ul class="busStop" id="buspointlist" style="display: none;">
                            <li id="buspointtext"></li>
                        </ul>
                        <ul class="busDate" id="ebusdate" style="display:none;">
                            <li><img src="images/viewicon/calendar.svg" alt="">복귀일</li>
                            <li class="calendar"><input type="text" id="SurfBusE" name="SurfBusE" readonly="readonly" class="itx" cal="busdate" gubun="<?=$busgubun?>"></li>
                        </ul>
                        <ul class="busLine" style="display: none;">
                            <li><img src="images/viewicon/bus.svg" alt="">복귀노선</li>
                        </ul>
                        <ul class="busStop" id="buspointlist" style="display: none;">
                            <li id="buspointtext"></li>
                        </ul>
                    </div>                
                    <div id="nextbtn" class="busOption01" style="text-align:center;">
                        <span id="resseatnum"></span>
                        <input type="button" id="exceldown" class="btnsurfdel" style="width:160px;font-size: 1.2em;" value="좌석선택하기" onclick="fnBusNext();">
                    </div>
                </div>

                <div id="seatTab" class="busOption01" style="padding-top: 10px;display:none;">
                    <ul class="busLineTab" style="display: block;">
                    </ul>
                </div>
                <div class="busOption02" style="display:none;">
                    <ul class="busSeat">
                        <div class="busSeatTable">
                            <div style="padding-bottom:155px;"></div>
                            <table style="width:312px;margin-left:7px;" id="tbSeat">
                                <colgroup>
                                    <col style="width:60px;height:68px;">
                                    <col style="width:60px;height:68px;">
                                    <col style="width:60px;height:68px;">
                                    <col style="width:60px;height:68px;">
                                    <col style="width:60px;height:68px;">
                                </colgroup>
                                <tbody>

                                <?
                                $chkSeat = "";
                                for($i=0; $i<=10; $i++){
                                    $num1 = ($i * 4) + 1;
                                    $num2 = ($i * 4) + 2;
                                    $num3 = ($i * 4) + 3;
                                    $num4 = ($i * 4) + 4;
                                    $num5 = ($i * 4) + 5;

                                    if($i == 10){
                                ?>
                                    <tr height="68" id="busSeatLast">
                                        <td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="<?=$num1?>"><br><?=$num1?></td>
                                        <td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="<?=$num2?>"><br><?=$num2?></td>
                                        <td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="<?=$num3?>"><br><?=$num3?></td>
                                        <td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="<?=$num4?>"><br><?=$num4?></td>
                                        <td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="<?=$num5?>"><br><?=$num5?></td>
                                    </tr>
                                <?
                                    }else{
                                ?>
                                    <tr height="68">
                                        <td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="<?=$num1?>"><br><?=$num1?></td>
                                        <td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="<?=$num2?>"><br><?=$num2?></td>
                                        <td>&nbsp;</td>
                                        <td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="<?=$num3?>"><br><?=$num3?></td>
                                        <td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="<?=$num4?>"><br><?=$num4?></td>
                                    </tr>
                                <?
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </ul>
                    <ul class="selectStop" style="padding:0 4px;">
                    <?if($param == "surfbus_yy"){?>
                        <li style="display:none;"><img src="images/button/btn061.png" alt="양양행 서핑버스"></li>
                        <li>
                            <div id="selBusY" class="bd" style="padding-top:2px;">
                            </div>
                        </li>
                        <li style="display:none;"><img src="images/button/btn062.png" alt="서울행 서핑버스"></li>
                        <li>
                            <div id="selBusS" class="bd" style="padding-top:2px;">
                            </div>
                        </li>
                    <?}else{?>
                        <li style="display:none;"><img src="images/button/btn064.png" alt="동해행 서핑버스"></li>
                        <li>
                            <div id="selBusE" class="bd" style="padding-top:2px;">
                            </div>
                        </li>
                        <li style="display:none;"><img src="images/button/btn063.png" alt="서울행 서핑버스"></li>
                        <li>
                            <div id="selBusA" class="bd" style="padding-top:2px;">
                            </div>
                        </li>
                    <?}?>
                    </ul>
                </div>
                <div class="bd" style="padding:0 4px;display:none;" id="divConfirm">
                    <p class="restitle">예약자 정보</p>
                    <table class="et_vars exForm bd_tb bustext" style="width:100%;margin-bottom:5px;">
                        <colgroup>
                            <col style="width:100px;">
                            <col style="width:auto;">
                        </colgroup>
                        <tbody>
                            <tr>
                                <th><em>*</em> 이름</th>
                                <td><input type="text" id="userName" name="userName" value="" class="itx" maxlength="15"></td>
                            </tr>
                            <tr>
                                <th><em>*</em> 연락처</th>
                                <td>
                                    <input type="<?=$inputtype?>" name="userPhone1" id="userPhone1" value="" size="3" maxlength="3" class="tel itx" style="width:50px;"> - 
                                    <input type="<?=$inputtype?>" name="userPhone2" id="userPhone2" value="" size="4" maxlength="4" class="tel itx" style="width:60px;"> - 
                                    <input type="<?=$inputtype?>" name="userPhone3" id="userPhone3" value="" size="4" maxlength="4" class="tel itx" style="width:60px;">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"> 이메일</th>
                                <td><input type="text" id="usermail" name="usermail" value="" class="itx"></td>
                            </tr>
                            <tr>
                                <th scope="row"> 쿠폰코드</th>
                                <td>
                                    <input type="text" id="coupon" name="coupon" value="<?=$coupon_code?>" size="10" class="itx" maxlength="10">
                                    <input type="hidden" id="couponcode" name="couponcode" value="">
                                    <input type="hidden" id="couponprice" name="couponprice" value="0">
                                    <input type="button" id="couponbtn" class="gg_btn gg_btn_grid gg_btn_color" style="width:50px; height:24px;" value="적용" onclick="fnCouponCheck(this);" />
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
                                    <input type="checkbox" id="chk9" name="chk9"> <strong>개인정보 수집이용 동의 </strong> <a href="/act/clause/privacy.html" target="_blank" style="float:none;">[내용확인]</a> (필수동의)
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="padding:10px;display:; text-align:center;" id="divBtnRes">
                        <div>
                            <input type="button" class="gg_btn gg_btn_grid" style="width:130px; height:40px;background:#3195db;color:#fff;" value="이전단계" onclick="fnBusPrev(0);" />&nbsp;&nbsp;
                            <input type="button" class="gg_btn gg_btn_grid gg_btn_color" style="width:130px; height:40px;" value="예약하기" onclick="fnBusSave();" />
                        </div>
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
        <!-- <img src="https://surfenjoy.cdn3.cafe24.com/button/btnReserve.png" id="slide1"> -->
    </div>
    <div id="sildeing" style="position:absolute;bottom:80px;display: none;">
    </div>
</div>

<? include '_layout_bottom.php'; ?>

<script>    
	var busTypeY = "E";
    var busTypeS = "A";	
    if($j("#shopseq").val() == 7){
		busTypeY = "Y";
		busTypeS = "S";
    }
</script>

<script src="js/surfview_bus.js"></script>
<script src="js/surfview.js"></script>
<script src="js/jquery-ui.js"></script>
<script src="js/surfview_busday.js?v=1"></script>
<script>    
    //$j('.busSeat').block({ message: null });
    var businit = 0;
    var busrestype = "none";
    var busDateinit = "<?=$sbusDate?>";
    var busData = {};
    
    var objParam = {
        "code":"busday",
        "bus":"<?=$busgubun?>",
        "seq":"<?=$shopseq?>"
    }
    $j.getJSON("/act/surf/surfbus_day.php", objParam,
        function (data, textStatus, jqXHR) {
            busData = data;
        }
    );
    function fnMapClick(){
        if($j("#ifrmBusMap").css("display") == "none"){
            setTimeout('$j("input[type=button]").eq(0).click();', 500);
        }
    }

    var resbusseat1 = 0;
    var resbusseat2 = 0;
    jQuery(function() {
        <?if($coupon_code != ""){?>
            
        var cp = fnCoupon("BUS", "load", $j("#coupon").val());
        if(cp == 0){
            location.href = "/";
            return;
        }else{
            busrestype = "channel";
            $j("#couponbtn").click();

            if($j("#coupon").val() == ""){
                
            }else{
                // $j("#coupon").prop("readonly", true);
                // $j("#coupon").css("display", "none");
                // $j("#couponbtn").css("display", "none");
            }

            resbusseat1 = <?=$resbusseat1?>;
            resbusseat2 = <?=$resbusseat2?>;

            <?if($daytype == 0){?>
                $j('#ulDaytype li').eq(1).click();
            <?}else{?>
                $j('#ulDaytype li').eq(2).click();
                $j("#userName").val("<?=$resusername?>");
                $j("#userPhone1").val("<?=$resusertel1?>");
                $j("#userPhone2").val("<?=$resusertel2?>");
                $j("#userPhone3").val("<?=$resusertel3?>");

                $j("#SurfBusS").val("<?=$resDate1?>");
                $j("#SurfBusE").val("<?=$resDate2?>");

                $j("#resseatnum").html("양양행 : " + resbusseat1 + "자리 예약가능 / 서울행 : " + resbusseat2 + "자리 예약가능<br><br>");
                
                fnBusSearchDate($j("#SurfBusS").val(), $j("#SurfBusS").attr("gubun"), $j("#SurfBusS").attr("id"));
                fnBusSearchDate($j("#SurfBusE").val(), $j("#SurfBusE").attr("gubun"), $j("#SurfBusE").attr("id"));

                $j("#SurfBusS").datepicker('option', 'disabled', true);
                $j("#SurfBusE").datepicker('option', 'disabled', true);

                $j('#ulDaytype li').eq(1).removeAttr("onclick");
                $j('#ulDaytype li').eq(2).removeAttr("onclick");
            <?}?>
        }
        <?}?>
    });
</script>

