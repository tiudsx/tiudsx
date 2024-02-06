<? 
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/func.php';

$resNumber = str_replace(' ', '', $_REQUEST["resNumber"]);
$num = $_REQUEST["num"];

$now = date("Y-m-d");
$select_query = 'SELECT a.*, b.*, c.seat, TIMESTAMPDIFF(MINUTE, b.insdate, now()) as timeM FROM `AT_RES_MAIN` a 
                    INNER JOIN `AT_RES_SUB` as b 
                        ON a.resnum = b.resnum 
                    INNER JOIN AT_PROD_BUS_DAY as c
                        ON b.bus_oper = c.bus_oper
                            AND b.bus_gubun = c.bus_gubun
                            AND b.bus_num = c.bus_num
                            AND b.res_date = c.bus_date
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
    }
    
    $bus_oper = $row["bus_oper"];
    $bus_gubun = $row["bus_gubun"];
    $bus_num = $row["bus_num"];
    $res_seat = $row["res_seat"];
    $seat = $row["seat"];
    
    if($bus_oper == "start"){ //서울 출발
        $start_line = '<li class="on" style="cursor:pointer;" bus_gubun="'.$bus_gubun.'" bus_num="'.$bus_num.'" seat="'.$seat.'" onclick="fnSeatLine(this);">[출발] '.fnBusNum2023($row["bus_gubun"].$row["bus_num"])["full"].'</li>';

        $arrSeatList .= "<input type='hidden' name='$bus_gubun' value='$res_seat'>";
    }else{ //복귀
        $return_line = '<li style="cursor:pointer;" bus_gubun="'.$bus_gubun.'" bus_num="'.$bus_num.'" seat="'.$seat.'"  onclick="fnSeatLine(this);">[복귀] '.fnBusNum2023($row["bus_gubun"].$row["bus_num"])["full"].'</li>';

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
            <div id="view_tab3" class="view_tab3" style="min-height: 800px;padding-bottom:30px;">
                <div id="seatTab" class="busOption01" style="padding-top: 10px;">
                    <span id="resseatnum" style="font-size: medium;width:100%;text-align:center;display: block;"></span>
                    <ul>
                        <li><img src="/act_2023/images/viewicon/bus.svg" alt="">노선선택</li>
                    </ul>
                    <ul class="busLineTab" style="display: block;">
                    <?=$start_line?>
                    <?=$return_line?>
                </div>
                <div class="busOption02">
                    <ul class="busSeat">
                        <div style="text-align:center">
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
                </div>
            </div>
        </section>
    </div>
</div>

<iframe id="ifrmResize" name="ifrmResize" style="width:100%;height:400px;display:none;"></iframe>

<script type="text/javascript" src="/act_2023/front/_js/bus.js?v=1"></script>

<script>
    jQuery(function() {
        fnSeatLine();
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