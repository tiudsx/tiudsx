<script>
    //alert("2022년 액트립 셔틀버스를 이용해주셔서 대단히 감사합니다.\n\n이번시즌에는 운행이 종료되었으며, 2023년 4월에 오픈예정입니다.");
    //history.back();
</script>
<?
//return;
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/func.php';

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

$arrBus = fnBusUrl($param);

$shopseq = $arrBus["seq"];
$pointurl = $arrBus["tab"];
$bus_type = $arrBus["type"]; //양양, 동해

$arrChannel = $_REQUEST["param"];
$couponseq = "";

$view_tab1 = "";
$view_tab3 = "none";
$view_tab1_class = "class='on'";
$view_tab3_class = "";
$bus_gubun = "S"; //편도 - 서울 출발

//일정, 노선 표시 여부
$classS = "on";
$displayS_2 = "";
$displayE_2 = "style='display:none;'";

//타채널 알림톡 좌석예약
if($arrChannel != ""){
    $page_load = true; //페이지 로딩 표시

    $view_tab1 = "none";
    $view_tab3 = "";
    $view_tab1_class = "";
    $view_tab3_class = "class='on'";

    $arrChk = explode("|", decrypt($arrChannel));
    $codeseq = $arrChk[1];  //쿠폰코드 seq

	$select_query = "SELECT A.*, B.couponseq, B.coupon_code
                        FROM AT_RES_TEMP AS A INNER JOIN AT_COUPON_CODE AS B
                                ON A.codeseq = B.codeseq
                        WHERE A.codeseq = '$codeseq'";
    $result = mysqli_query($conn, $select_query);
    $rowMain = mysqli_fetch_array($result);

    $bus_line = $rowMain["bus_line"]; //행선지
    $userName = $rowMain["user_name"]; //이름
    $userPhone = str_replace("-", "", trim($rowMain["user_phone"]));  //연락처
    $userPhone1 = substr($userPhone, 0, 3);
    $userPhone2 = substr($userPhone, 3, 4);
    $userPhone3 = substr($userPhone, 7, 4);

    $couponseq = $rowMain["couponseq"]; //채널
    $coupon_code = $rowMain["coupon_code"]; //쿠폰코드

    $start_bus_gubun = $rowMain["start_bus_gubun"]; //출발노선
    $return_bus_gubun = $rowMain["return_bus_gubun"]; //복귀노선

    $start_day = $rowMain["start_day"]; //출발일
    $return_day = $rowMain["return_day"]; //복귀일
    $start_cnt = $rowMain["start_cnt"]; //출발 인원
    $return_cnt = $rowMain["return_cnt"]; //복귀인원


    if((date("Y-m-d") > $start_day && $start_cnt > 0) || (date("Y-m-d") > $return_day && $return_cnt > 0)){
        echo "<script>alert('이용일이 지난 예약건입니다.');location.href='/';</script>";
        return;
    }

    if($start_cnt > 0 && $return_cnt > 0){
        $bus_gubun = "A"; //왕복
    }else if($return_cnt > 0){
        $bus_gubun = "E"; //편도 - 서울 복귀
    }

    //일정, 노선 표시 여부
    if($bus_gubun == "A"){ //왕복
        $classA = "on";
        $displayS = "style='display:none;'";
        $displayE = "style='display:none;'";
        
        $displayS_2 = "";
        $displayE_2 = "";
    }else if($bus_gubun == "S"){ //출발
        $classS = "on";
        $displayA = "style='display:none;'";
        $displayE = "style='display:none;'";
        
        $displayS_2 = "";
        $displayE_2 = "style='display:none;'";
    }else if($bus_gubun == "E"){ //출발
        $classE = "on";
        $displayA = "style='display:none;'";
        $displayS = "style='display:none;'";
        
        $displayS_2 = "style='display:none;'";
        $displayE_2 = "";
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
    <? include __DIR__.'/../../_layout/_layout_top.php'; ?>

    <link rel="stylesheet" type="text/css" href="/act_2023/front/_css/surfview.css">
    <link rel="stylesheet" type="text/css" href="/act_2023/front/_css/bus.css">

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
                            <li <?=$view_tab1_class?> onclick="fnResViewBus(true, '#content_tab1', 70, this);"><a>상세설명</a></li>
                            <li onclick="fnResViewBus(false, '#view_tab2', 70, this);fnMapClick();"><a>정류장안내</a></li>
                            <li <?=$view_tab3_class?> onclick="fnResViewBus(false, '#view_tab3', 70, this);"><a>셔틀예약</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div id="view_tab1" style="display:<?=$view_tab1?>;">
                <div class="noticeline" id="content_tab1">
                    <?if($arrChannel == ""){?>
                        <article>
                            <p class="noticesub">셔틀버스 예약안내</p>
                            <ul>
                                <li class="litxt">1시간 이내 미입금시 자동취소됩니다.</li>
                                <li class="litxt">무통장 입금시 예약자와 입금자명이 동일해야합니다.</li>
                                <li class="litxt">최소인원(15명) 모집이 안될 경우 운행이 취소될 수 있으며, 전액 환불됩니다.</li>
                                <li class="litxt">천재지변으로 인하여 셔틀버스 운행이 취소될 경우 전액환불됩니다.</li>
                                <li class="litxt">현금영수증 신청은 이용일 이후&nbsp;<span style="color:#059bc0;">[카카오채널 : 액트립]</span> 에서 신청가능합니다.</li>
                            </ul>
                        </article>
                        <article>
                            <p class="noticesub">탑승 및 이용안내</p>
                            <ul>
                                <li class="litxt">탑승시간 10분전에 예약하신 정류장으로 도착해주세요.</li>
                                <li class="litxt">교통상황으로 인해 셔틀버스가 지연 도착할 수 있으니 양해부탁드립니다.</li>
                                <li class="litxt">사전 신청하지 않는 정류장은 정차 및 하차하지 않습니다.</li>
                                <li class="litxt">기상악화로 인하여 서핑강습이 취소되어도 셔틀버스는 정상운행되며, 기존 환불정책으로 적용됩니다.</li>
                            </ul>
                        </article>                        
                    <?}else{?>
                        <article>
                            <p class="noticesub">예약안내</p>
                            <ul>
                                <li class="litxt">탑승시간 10분전에 예약하신 정류장으로 도착해주세요.</li>
                                <li class="litxt">교통상황으로 인해 셔틀버스가 지연 도착할 수 있으니 양해부탁드립니다.</li>
                                <li class="litxt">사전 신청하지 않는 정류장은 정차 및 하차하지 않습니다.</li>
                            </ul>
                        </article>
                        <article>
                            <p class="noticesub">취소/환불 안내</p>
                            <ul>
                                <li class="litxt">잔여석이 없을 경우 예약이 취소 될 수 있으니 유의 부탁드립니다.</li>
                                <li class="litxt">취소 및 환불은 예약하신 사이트에서 신청가능합니다.</li>
                            </ul>
                        </article>
                    <?}?>
                </div>
                <div class="contentimg">
                <!-- <img src="https://actrip.cdn1.cafe24.com/act_notice/bus_notice.jpg" class="placeholder"> -->
                    <?include 'view_content.php';?>
                </div>
                <div>
                    <div style="padding:10px 0 5px 0;font-size:12px;">
                        <a href="http://pf.kakao.com/_HxmtMxl" target="_blank" rel="noopener"><img src="/act_2023/images/mainImg/kakaochat.jpg" class="placeholder"></a>
                    </div>
                </div>
                
                <?if($arrChannel == ""){?>
                <div class="noticeline2" id="cancelinfo">
                    <article>
                        <p class="noticesub">환불 규정안내</p>
                        <ul>
                            <li class="refund"><img src="/act_2023/images/refund.jpg" alt=""></li>
                        </ul>
                    </article>
                </div>
                <?}?>
            </div>
            <div id="view_tab2" style="display: none;min-height: 800px;">
            
                <? include $pointurl; ?>

            </div>
            <div id="view_tab3" class="view_tab3" style="min-height: 800px;display:<?=$view_tab3?>;">
            <form id="frmRes" method="post" target="ifrmResize" autocomplete="off">
                <span style="display:none;">
                    <br>resparam<input type="text" id="resparam" name="resparam" value="BusI" />
                    <br>userId<input type="text" id="userId" name="userId" value="<?=$user_id?>">
                    <br>shopseq<input type="text" id="shopseq" name="shopseq" value="<?=$shopseq?>">
                    <br>편도/왕복<input type="text" id="bus_gubun" name="bus_gubun" value="<?=$bus_gubun?>">
                    <br>행선지<input type="text" id="bus_line" name="bus_line" value="<?=$busgubun?>">
                </span>
                
                <div id="resStep1">
                    <div class="busOption01" style="text-align:center;display:none;">
                        <input type="button" class="btnsurfdel" style="width:100px;font-size: 1.2em;" value="일정 선택">
                         >
                        <input type="button" class="btnsurfdel" style="width:100px;font-size: 1.2em;" value="좌석 선택">
                         >
                        <input type="button" class="btnsurfdel" style="width:100px;font-size: 1.2em;" value="정류장 및 예약정보 입력">
                    </div>
                    <div class="busOption01" style="padding-bottom: 0px;" id="route">
                        <ul class="destination" id="ulroute" style="margin-bottom: 0px;">
                            <li><img src="/act_2023/images/viewicon/route.svg" alt="">일정</li>
                            <li class="toYang <?=$classS?>" <?=$displayS?> <?=($couponseq == "") ? "onclick=\"fnBusGubun('S', this);\"" : "" ?>>출발<i class="fas fa-chevron-right"></i></li>
                            <li class="toYang <?=$classE?>" <?=$displayE?> <?=($couponseq == "") ? "onclick=\"fnBusGubun('E', this);\"" : "" ?> style="margin-right: 10px;">복귀<i class="fas fa-chevron-right"></i></li>
                           
                            <li class="toYang <?=$classA?>" <?=$displayA?> <?=($couponseq == "") ? "onclick=\"fnBusGubun('A', this);\"" : "" ?>>왕복<i class="fas fa-chevron-right"></i></li>
                        </ul>
                    </div>
                    <div id="layerbus1" class="busOption01" style="padding-top: 10px;">
                        <ul class="busDate " data-key="bus_start" <?=$displayS_2?>>
                            <li><img src="/act_2023/images/viewicon/calendar.svg" alt="">출발일</li>
                            <li class="calendar"><input type="text" id="bus_start" name="bus_start" readonly="readonly" class="itx" cal="busdate"></li>
                        </ul>
                        <ul class="busLine" data-key="bus_start" <?=$displayS_2?>>
                            <li><img src="/act_2023/images/viewicon/bus.svg" alt="">출발노선</li>
                        </ul>
                        <ul class="busStop" data-key="bus_start" <?=$displayS_2?>>
                            <li id="buspointtext"></li>
                        </ul>
                        <ul class="busDate" data-key="bus_return" <?=$displayE_2?>>
                            <li><img src="/act_2023/images/viewicon/calendar.svg" alt="">복귀일</li>
                            <li class="calendar"><input type="text" id="bus_return" name="bus_return" readonly="readonly" class="itx" cal="busdate"></li>
                        </ul>
                        <ul class="busLine" data-key="bus_return" <?=$displayE_2?>>
                            <li><img src="/act_2023/images/viewicon/bus.svg" alt="">복귀노선</li>
                        </ul>
                        <ul class="busStop" data-key="bus_return" <?=$displayE_2?>>
                            <li id="buspointtext"></li>
                        </ul>
                    </div>
                    <div id="nextbtn" class="busOption01" style="text-align:center;">
                        <input type="button" id="exceldown" class="btnsurfdel" style="width:160px;font-size: 1.2em;" value="좌석선택하기" onclick="fnBusNext(1);">
                    </div>
                </div>

                <div id="seatTab" class="busOption01" style="padding-top: 10px;display:none;">
                    <div class="busOption01" style="text-align:center;display:none;">
                        <input type="button" class="btnsurfdel" style="width:100px;font-size: 1.2em;" value="일정 선택">
                         >
                        <input type="button" class="btnsurfdel" style="width:100px;font-size: 1.2em;" value="좌석 선택">
                         >
                        <input type="button" class="btnsurfdel" style="width:100px;font-size: 1.2em;" value="정류장 및 예약정보 입력">
                    </div>

                    <span id="resseatnum" style="font-size: medium;width:100%;text-align:center;display: block;"></span>
                    <ul>
                        <li><img src="/act_2023/images/viewicon/bus.svg" alt="">노선선택</li>
                    </ul>
                    <ul class="busLineTab" style="display: block;">
                    </ul>
                </div>
                <div class="busOption02" id="bus_step1" style="display:none;">
                    <ul class="busSeat">
                        <div style="text-align:center">
                            <span style="font-size: 1.3em;">
                                <img src="https://actrip.cdn1.cafe24.com/bus/bus_1.jpg" alt="">선택가능 &nbsp;&nbsp;
                                <img src="https://actrip.cdn1.cafe24.com/bus/bus_2.jpg" alt="">선택불가 &nbsp;&nbsp;
                                <img src="https://actrip.cdn1.cafe24.com/bus/bus_1.png" alt="">나의좌석
                            </span>
                            <p class="restitle" style="color:#d20000;">
                                # 배차된 셔틀버스에 따라 좌석번호는 <Br>좌/우<span style="font-size: 0.8em;font-weight: 400;color:black;">(창측/내측은 유지)</span> 방향이 바뀔수 있습니다.
                            </p>
                        </div>
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
                    <ul class="busLineTab2" style="display: none;padding-left: 10px;"></ul>
                    
                    <div id="nextbtn" class="busOption01" style="text-align:center;padding-top:0px;">
                        <input type="button" class="btnsurfadd" style="width:160px;font-size: 1.2em;" value="이전단계" onclick="fnBusPrev(0);">&nbsp;&nbsp;
                        <input type="button" class="btnsurfdel" style="width:160px;font-size: 1.2em;" value="다음단계" onclick="fnBusNext(2);">
                    </div>
                </div>

                <div class="busOption01" id="bus_step2" style="display:none;">
                
                    <div class="busOption01" style="text-align:center;display:none;">
                        <input type="button" class="btnsurfdel" style="width:100px;font-size: 1.2em;" value="일정 선택">
                         >
                        <input type="button" class="btnsurfdel" style="width:100px;font-size: 1.2em;" value="좌석 선택">
                         >
                        <input type="button" class="btnsurfdel" style="width:100px;font-size: 1.2em;" value="정류장 및 예약정보 입력">
                    </div>
                    <ul class="busLine">
                        <li><img src="/act_2023/images/viewicon/bus.svg" alt="">출발노선</li>
                        <li id="selBusName_S" class="on"></li>
                    </ul>
                    <ul class="busDate">
                        <li><img src="/act_2023/images/viewicon/calendar.svg" alt=""><span id="selBusDate_S"></span></li>
                        <li style="width:330px; height:auto;">
                            <div id="selBus_S" class="bd" style="padding-top:2px;">
                            </div>
                        </li>
                    </ul>
                    <ul class="busLine">
                        <li><img src="/act_2023/images/viewicon/bus.svg" alt="">복귀노선</li>
                        <li id="selBusName_E" class="on"></li>
                    </ul>
                    <ul class="busDate">
                        <li><img src="/act_2023/images/viewicon/calendar.svg" alt=""><span id="selBusDate_E"></span></li>
                        <li style="width:330px; height:auto;">
                            <div id="selBus_E" class="bd" style="padding-top:2px;">
                            </div>
                        </li>
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
                                <td><input type="text" id="userName" name="userName" value="<?=$userName?>" class="itx" maxlength="15"></td>
                            </tr>
                            <tr>
                                <th><em>*</em> 연락처</th>
                                <td>
                                    <input type="<?=$inputtype?>" name="userPhone1" id="userPhone1" value="010" size="3" maxlength="3" class="tel itx" style="width:50px;"> - 
                                    <input type="<?=$inputtype?>" name="userPhone2" id="userPhone2" value="<?=$userPhone2?>" size="4" maxlength="4" class="tel itx" style="width:60px;"> - 
                                    <input type="<?=$inputtype?>" name="userPhone3" id="userPhone3" value="<?=$userPhone3?>" size="4" maxlength="4" class="tel itx" style="width:60px;">
                                </td>
                            </tr>
                            <tr style="display:none;">
                                <th scope="row"> 이메일</th>
                                <td><input type="text" id="usermail" name="usermail" value="" class="itx"></td>
                            </tr>
                            <tr style="display:<?=$view_tab1?>;">
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
                            <tr style="display:<?=(($couponseq == "") ? "" : "none" )?>;">
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
                                    <input type="checkbox" id="chk9" name="chk9"> <strong>개인정보 수집이용 동의 </strong> <a href="/act_2023/_clause/privacy.html" target="_blank" style="float:none;">[내용확인]</a> (필수동의)
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="padding:10px; text-align:center;" id="divBtnRes">
                        <input type="button" class="btnsurfadd" style="width:160px;font-size: 1.2em;" value="이전단계" onclick="fnBusPrev(1);">&nbsp;&nbsp;
                        <input type="button" class="btnsurfdel" style="width:160px;font-size: 1.2em;" value="예약하기" onclick="fnBusSave();">
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
        <!-- <img src="https://actrip.cdn1.cafe24.com/button/btnReserve.png" id="slide1"> -->
    </div>
    <div id="sildeing" style="position:absolute;bottom:80px;display: none;">
    </div>
</div>

<? include __DIR__.'/../../_layout/_layout_bottom.php'; ?>
<script>    
    var shopseq = $j("#shopseq").val();
    var bus_line = "<?=$bus_type?>"; //행선지 양양, 동해
</script>

<script type="text/javascript" src="/act_2023/front/_js/bus.js?v=1"></script>
<script type="text/javascript" src="/act_2023/front/_js/busday.js?v=1"></script>

<!-- Swiper JS -->
<script type="text/javascript" src="/act_2023/front/_js/swiper.min.js"></script>
<script>
    fnBusPointList();
    
    var dayCode = "busseat";
    var busrestype = "none";
    var buschannel = "<?=$couponseq?>";
    var json_busData = {}; //셔틀버스 노선 이용가능 날짜 json
    
    fnBusDate(shopseq, "<?=$busgubun?>"); //버스 예약 가능한 날짜
    
    var start_cnt = 0;
    var return_cnt = 0;
    jQuery(function() {
        <?if($coupon_code != ""){?>
        var cp = fnCoupon("BUS", "load", $j("#coupon").val());
        if(cp == 0){
            location.href = "/";
            return;
        }else{
            busrestype = "channel";
            $j("#couponbtn").click();

            start_cnt = <?=$start_cnt?>;
            return_cnt = <?=$return_cnt?>;

            $j("#bus_start").datepicker('option', 'disabled', true);
            $j("#bus_return").datepicker('option', 'disabled', true);
            
            <?if($bus_gubun != "A"){?>

                <?if($start_cnt > 0){ //양양행 ?>
                    //$j("#resseatnum").html("출발 : " + start_cnt + "자리<br>");

                    $j("#bus_start").val("<?=$start_day?>");
                    fnBusSearchDate($j("#bus_start").val(), "bus_start", "<?=$start_bus_gubun?>");
                <?}?>

                <?if($return_cnt > 0){ //서울행  ?>
                    //$j("#resseatnum").html("복귀 : " + return_cnt + "자리<br>");

                    $j("#bus_return").val("<?=$return_day?>");
                    fnBusSearchDate($j("#bus_return").val(), "bus_return", "<?=$return_bus_gubun?>");
                <?}?>
            <?}else{?>

                $j("#bus_start").val("<?=$start_day?>");
                $j("#bus_return").val("<?=$return_day?>");
                
                //$j("#resseatnum").html("출발 : " + start_cnt + "자리 / 복귀 : " + return_cnt + "자리<br>");
                
                fnBusSearchDate($j("#bus_start").val(), "bus_start", "<?=$start_bus_gubun?>");
                fnBusSearchDate($j("#bus_return").val(), "bus_return", "<?=$return_bus_gubun?>");
            <?}?>
        }
        <?}?>
    });
</script>

