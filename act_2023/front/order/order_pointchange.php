<? 
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/func.php';

$resNumber = str_replace(' ', '', $_REQUEST["resNumber"]);
$num = $_REQUEST["num"];

$now = date("Y-m-d");
$select_query = 'SELECT a.*, b.*, c.seat, d.couponseq, TIMESTAMPDIFF(MINUTE, b.insdate, now()) as timeM FROM `AT_RES_MAIN` a 
                    INNER JOIN `AT_RES_SUB` as b 
                        ON a.resnum = b.resnum 
                    INNER JOIN AT_PROD_BUS_DAY as c
                        ON b.bus_oper = c.bus_oper
                            AND b.bus_gubun = c.bus_gubun
                            AND b.bus_num = c.bus_num
                            AND b.res_date = c.bus_date
                    LEFT JOIN AT_COUPON_CODE as d
                        ON b.res_coupon = d.coupon_code
                    WHERE a.resnum = "'.$resNumber.'" AND b.res_confirm IN (0,3,8)
                        AND b.res_date >= "'.$now.'"
                    ORDER BY a.resnum, b.ressubseq';

$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);

if($count == 0){
    echo "<script>alert('예약된 정보가 없거나 이용일이 지났습니다.\\n\\n관리자에게 문의해주세요.');location.href='/ordersearch';</script>";
	return;
}

$i = 0;
$arrSeatList = "";

$start_line = "";
$return_line = "";
while ($row = mysqli_fetch_assoc($result_setlist)){
    if($i == 0){
        $user_name = $row["user_name"];
        $user_tel = $row["user_tel"];
        $shopseq = $row["seq"];
        $couponseq = $row["couponseq"];
        $couponcode = $row["res_coupon"];
    }
    
    $bus_oper = $row["bus_oper"];
    $bus_gubun = $row["bus_gubun"];
    $bus_num = $row["bus_num"];
    $res_seat = $row["res_seat"];
    $seat = $row["seat"];
    $res_date = $row["res_date"];
    
    if($bus_oper == "start"){ //서울 출발
        $start_line = '<li class="on" style="cursor:pointer;" bus_gubun="'.$bus_gubun.'" bus_num="'.$bus_num.'" seat="'.$seat.'"  caldate="'.$res_date.'" onclick="fnBusSeatInit(this, 0);">[출발] '.fnBusNum2023($row["bus_gubun"].$row["bus_num"])["full"].'</li>';

        $arrSeatList .= "<input type='hidden' name='$bus_gubun' value='$res_seat'>";
    }else{ //복귀
        $return_line = '<li style="cursor:pointer;" bus_gubun="'.$bus_gubun.'" bus_num="'.$bus_num.'" seat="'.$seat.'"  caldate="'.$res_date.'" onclick="fnBusSeatInit(this, 0);">[복귀] '.fnBusNum2023($row["bus_gubun"].$row["bus_num"])["full"].'</li>';

        $arrSeatList .= "<input type='hidden' name='$bus_gubun' value='$res_seat'>";
    }

    $i++;    
}
?>

<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;">
<link rel="stylesheet" type="text/css" href="/act_2023/front/_css/default.css">

<div id="wrap">

    <link rel="stylesheet" type="text/css" href="/act_2023/front/_css/surfview.css">
    <link rel="stylesheet" type="text/css" href="/act_2023/front/_css/bus.css">

    <div class="top_area_zone">
        <section class="notice">
            <div id="view_tab3" class="view_tab3" style="min-height: 800px;display:;">
            <form id="frmRes" method="post" target="ifrmResize" autocomplete="off">
                <span style="display:none;">
                    <br>resparam<input type="text" id="resparam" name="resparam" value="PointChange" />
                    <br>userId<input type="text" id="userId" name="userId" value="<?=$user_id?>">
                    <br>shopseq<input type="text" id="shopseq" name="shopseq" value="<?=$shopseq?>">
                    <br>편도/왕복<input type="text" id="daytype" name="daytype" value="<?=$daytype?>">
                    <br>행성지<input type="text" id="busgubun" name="busgubun" value="<?=$busgubun?>">
                    <br>MainNumber<input type="hidden" id="MainNumber" name="MainNumber" value="<?=$resNumber?>">
                    <br>num<input type="hidden" id="num" name="num" value="<?=$num?>">
                    <?=$resseq?>
                </span>

                <div id="seatTab" class="busOption01" style="padding-top: 10px;">
                    <span id="resseatnum" style="font-size: medium;width:100%;text-align:center;display: block;"></span>
                    <ul>
                        <li style="width:auto;"><img src="/act_2023/images/viewicon/bus.svg" alt="">좌석/정류장 변경 > 노선선택</li>
                    </ul>
                    <ul class="busLineTab" style="display: block;">
                    <?=$start_line?>
                    <?=$return_line?>
                </div>
                <div class="busOption02">
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
                            <?=$arrSeatList?>
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
                        <input type="button" class="btnsurfdel" style="width:160px;font-size: 1.2em;" value="다음단계" onclick="fnBusNext(2);">
                    </div>
                </div>
                <div class="busOption01" id="bus_step2" style="display:;">
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
                    <div style="padding:10px;text-align:center;" id="divBtnRes">
                        <input type="button" class="btnsurfadd" style="width:160px;font-size: 1.2em;" value="이전단계" onclick="fnBusPrev(1);">&nbsp;&nbsp;
                        <input type="button" class="gg_btn gg_btn_grid gg_btn_color" style="width:130px; height:40px;" value="예약 변경하기" onclick="fnBusSave();" />
                    </div>
                </div>
            </form>
            </div>
        </section>
    </div>
</div>
<iframe id="ifrmResize" name="ifrmResize" style="width:100%;height:400px;display:none;"></iframe>

<script>
    //비동기처리
    $j.ajaxSetup({ async: false });

    var shopseq = $j("#shopseq").val();
</script>

<script type="text/javascript" src="/act_2023/front/_js/bus.js?v=2"></script>
<script type="text/javascript" src="/act_2023/front/_js/busday.js?v=2"></script>

<script>
    fnBusPointList();

    var dayCode = "busseat";
    var busrestype = "change";
    var buschannel = "<?=$couponseq?>";
    jQuery(function() {
        //fnSeatLine();
        $j(".busLineTab li").eq(0).click(); //첫번째 노선버튼 클릭
    });

    function fnSeatLine(obj = null){
        if(obj != null){
            $j(".busLineTab li").removeClass("on");
            $j(obj).addClass("on");
        }
        
        var selObj = $j("ul[class=busLineTab] li[class=on]");
        if (selObj.attr("seat") == 44) {
            busSeatLast = '<td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="41"><br>41</td>' +
                '<td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="42"><br>42</td>' +
                '<td>&nbsp;</td>' +
                '<td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="43"><br>43</td>' +
                '<td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="44"><br>44</td>';
        } else {
            busSeatLast = '<td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="41"><br>41</td>' +
                '<td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="42"><br>42</td>' +
                '<td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="43"><br>43</td>' +
                '<td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="44"><br>44</td>' +
                '<td class="busSeatList busSeatListN" valign="top" onclick="fnSeatSelected(this);" style="font-weight: 700;" busSeat="45"><br>45</td>';
        }
        $j("#busSeatLast").html(busSeatLast);

        //좌석 초기화
        $j("#tbSeat .busSeatList").removeClass("busSeatListC").addClass("busSeatListN");

        var forObj = $j("input[name=" + selObj.attr("bus_gubun") + "]");
        for (var i = 0; i < forObj.length; i++) {
            //예약좌석 표시
            $j("#tbSeat .busSeatList[busSeat=" + forObj.eq(i).val() + "]").removeClass("busSeatListN").addClass("busSeatListC");
        }
    }

</script>