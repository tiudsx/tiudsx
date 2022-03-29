<? include 'db.php'; ?>

<?
$resNumber = str_replace(' ', '', $_REQUEST["resNumber"]);
$num = $_REQUEST["num"];

$now = date("Y-m-d");
$select_query = 'SELECT *, a.resnum as res_num, TIMESTAMPDIFF(MINUTE, b.insdate, now()) as timeM FROM `AT_RES_MAIN` a LEFT JOIN `AT_RES_SUB` as b 
ON a.resnum = b.resnum 
where a.resnum = "'.$resNumber.'" AND b.res_confirm IN (0,3,8)
AND b.res_date >= "'.$now.'"
ORDER BY a.resnum, b.ressubseq';

$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);

if($count == 0){
    echo "<script>alert('예약된 정보가 없거나 이용일이 지났습니다.\\n\\n관리자에게 문의해주세요.');location.href='/ordersearch';</script>";
	return;
}

$i = 0;
$busgubun0 = 0;
$busgubun1 = 0;
$arrResInfoS = array();
$arrResInfoE = array();
while ($row = mysqli_fetch_assoc($result_setlist)){
    if($i == 0){
        $user_name = $row["user_name"];
        $res_num = $row["resnum"];
        $user_tel = $row["user_tel"];
        $shopseq = $row["seq"];
    }
    
    $gubun = substr($row["res_bus"], 0, 1);
    if($gubun == "Y" || $gubun == "E"){ //양양,동해행
        $busgubun0 = 1;
        $res_date0 = $row["res_date"];
        $res_busnum0 = $row["res_busnum"];

        $arrResInfoS[$row["ressubseq"]] = array("ressubseq" => $row["ressubseq"]
                                                , "res_busnum" => $row["res_busnum"]
                                                , "res_seat" => $row["res_seat"]
                                                , "res_spointname" => $row["res_spointname"]
                                                , "res_epointname" => $row["res_epointname"]);

         $resseq .= "<input type='hidden' id='ressubseqs' name='ressubseqs[]' value='".$row["ressubseq"]."'>";
    }else{ //서울행
        $busgubun1 = 1;
        $res_date1 = $row["res_date"];
        $res_busnum1 = $row["res_busnum"];

        $arrResInfoE[$row["ressubseq"]] = array("ressubseq" => $row["ressubseq"]
                                                , "res_busnum" => $row["res_busnum"]
                                                , "res_seat" => $row["res_seat"]
                                                , "res_spointname" => $row["res_spointname"]
                                                , "res_epointname" => $row["res_epointname"]);

         $resseq .= "<input type='hidden' id='ressubseqe' name='ressubseqe[]' value='".$row["ressubseq"]."'>";
    }

    $i++;    
}

if($shopseq == 7){ //양양 셔틀버스
    $param = "surfbus_yy";
    $bustype0 = "Y";
    $bustype1 = "S";
    $bustypeText0 = "양양행";
    $bustypeText1 = "서울행";    
}else{ //동해 셔틀버스
    $param = "surfbus_dh";
    $bustype0 = "E";
    $bustype1 = "A";
    $bustypeText0 = "동해행";
    $bustypeText1 = "서울행";
}

$daytype = 0;
if($busgubun0 == 1 && $busgubun1 == 1){
    $daytype = 1;
}

$calbusgubun = $bustype0;
if($daytype == 0){
    $bustypeview0 = "";
    $bustypeview1 = "none";

    $res_busnum0 = $res_busnum0;
    if($busgubun1 == 1){
        $res_date0 = $res_date1;
        $calbusgubun = $bustype1;
        $res_busnum0 = $res_busnum1;
    }
}else{
    $bustypeview0 = "none";
    $bustypeview1 = "";
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
                <h1>액트립 서핑버스 예약정보 변경</h1>
                <a class="reviewlink">
                    <span class="reviewcnt">예약번호 : <?=$res_num?></span>
                </a>
                <div class="shopsubtitle">예약자 : <?=$user_name?> (<?=$user_tel?>)</div>
            </div>
        </section>

        <section class="notice">
            <div class="vip-tabwrap">
                <div id="tabnavi" class="fixed1" style="top: 49px;">
                    <div class="vip-tabnavi">
                        <ul>
                            <li class="on"><a>좌석 및 정류장 변경</a></li>
                        </ul>
                    </div>
                </div>
            </div>
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

                <div id="resStep1">
                    <div class="busOption01" style="padding-bottom: 0px;">
                        <ul class="destination" id="ulDaytype" style="margin-bottom: 0px;">
                            <li><img src="images/viewicon/sign.svg" alt="">일정</li>
                            <?if($daytype == 0){?>
                            <li class="toYang on">편도<i class="fas fa-chevron-right"></i></li>
                            <?}else{?>
                            <li class="toYang on">왕복<i class="fas fa-chevron-right"></i></li>
                            <?}?>
                        </ul>
                    </div>
                    <div class="busOption01" style="padding-bottom: 0px;display:<?=$bustypeview0?>;" id="route">
                        <ul class="destination" id="ulroute" style="margin-bottom: 0px;">
                            <li><img src="images/viewicon/route.svg" alt="">행선지</li>

                            <?if($busgubun0 == 1){?>
                            <li class="toYang on" onclick="fnBusGubun('<?=$bustype0?>', this, 'change');"><?=$bustypeText0?><i class="fas fa-chevron-right"></i></li>
                            <?}?>

                            <?if($busgubun1 == 1){?>
                            <li class="toYang on" onclick="fnBusGubun('<?=$bustype1?>', this, 'change');"><?=$bustypeText1?><i class="fas fa-chevron-right"></i></li>
                            <?}?>
                        </ul>
                    </div>
                    <div id="layerbus1" class="busOption01" style="padding-top: 10px;">
                        <ul class="busDate" id="busdate" style="display:<?=$bustypeview0?>;">
                            <li><img src="images/viewicon/calendar.svg" alt="">이용일</li>
                            <li class="calendar"><input type="text" id="SurfBus" name="SurfBus" readonly="readonly" class="itx" gubun="<?=$calbusgubun?>" value="<?=$res_date0?>" readonly></li>
                        </ul>
                        <ul class="busLine" id="busLine0" style="display:<?=$bustypeview0?>;">
                            <li><img src="images/viewicon/bus.svg" alt="">노선</li>
                        </ul>
                        <ul class="busStop" id="buspointlist" style="display: <?=$bustypeview0?>;">
                            <li id="buspointtext"></li>
                        </ul>
                        <ul class="busDate" id="sbusdate" style="display:<?=$bustypeview1?>;">
                            <li><img src="images/viewicon/calendar.svg" alt="">출발일</li>
                            <li class="calendar"><input type="text" id="SurfBusS" name="SurfBusS" readonly="readonly" class="itx" gubun="<?=$calbusgubun?>" value="<?=$res_date0?>" readonly></li>
                        </ul>
                        <ul class="busLine" id="busLine1" style="display: <?=$bustypeview1?>;">
                            <li><img src="images/viewicon/bus.svg" alt="">출발노선</li>
                        </ul>
                        <ul class="busStop" id="buspointlist" style="display: <?=$bustypeview1?>;">
                            <li id="buspointtext"></li>
                        </ul>
                        <ul class="busDate" id="busLine2" id="ebusdate" style="display:<?=$bustypeview1?>;">
                            <li><img src="images/viewicon/calendar.svg" alt="">복귀일</li>
                            <li class="calendar"><input type="text" id="SurfBusE" name="SurfBusE" readonly="readonly" class="itx" gubun="<?=$calbusgubun?>" value="<?=$res_date1?>" readonly></li>
                        </ul>
                        <ul class="busLine" style="display: <?=$bustypeview1?>;">
                            <li><img src="images/viewicon/bus.svg" alt="">복귀노선</li>
                        </ul>
                        <ul class="busStop" id="buspointlist" style="display: <?=$bustypeview1?>;">
                            <li id="buspointtext"></li>
                        </ul>
                    </div>                
                    <div id="nextbtn" class="busOption01" style="text-align:center;display:none;">
                        <input type="button" id="exceldown" class="btnsurfdel" style="width:160px;font-size: 1.2em;" value="좌석선택하기">
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
                        <li style="display:none;"><img src="images/button/<?if($bustype0 == "Y"){ echo "btn061.png"; }else{ echo "btn064.png"; }?>" alt="<?=$bustypeText0?> 서핑버스"></li>
                        <li>
                            <div id="selBus<?=$bustype0?>" class="bd" style="padding-top:2px;">
                            </div>
                        </li>
                        <li style="display:none;"><img src="images/button/<?if($bustype1 == "S"){ echo "btn062.png"; }else{ echo "btn063.png"; }?>" alt="<?=$bustypeText1?> 서핑버스"></li>
                        <li>
                            <div id="selBus<?=$bustype1?>" class="bd" style="padding-top:2px;">
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="bd" style="padding:0 4px;display:none;" id="divConfirm">
                    <div style="padding:10px;display:; text-align:center;" id="divBtnRes">
                    
                        <div>
                            <?
                            if($num == 1){
                                echo '<input type="button" class="gg_btn gg_btn_grid" style="width:130px; height:40px;background:#3195db;color:#fff;" value="메인으로" onclick="location.href=\'/\';" />';
                            }else{
                                echo '<input type="button" class="gg_btn gg_btn_grid" style="width:130px; height:40px;background:#3195db;color:#fff;" value="돌아가기" onclick="history.back();" />';
                            }?>
                            &nbsp;&nbsp;
                            <input type="button" class="gg_btn gg_btn_grid gg_btn_color" style="width:130px; height:40px;" value="예약 변경하기" onclick="fnBusSave();" />
                        </div>
                    </div>
                </div>
            </form>
            </div>
        </section>
    </div>
</div>
<iframe id="ifrmResize" name="ifrmResize" style="width:100%;height:400px;display:none;"></iframe>
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
    var businit = 0;
    var busrestype = "change";
    var busDateinit = "<?=$sbusDate?>";
    var busData = {};
    var busResData = {};
    
    <?foreach ($arrResInfoS as $key => $value) {?>
    busResData["<?=$value["res_busnum"]?>_<?=$value["res_seat"]?>"] = "<?=$value["res_busnum"].'/'.$value["res_seat"].'/'.$value["res_spointname"].'/'.$value["res_epointname"]?>";
    <?}?>
    <?foreach ($arrResInfoE as $key => $value) {?>
    busResData["<?=$value["res_busnum"]?>_<?=$value["res_seat"]?>"] = "<?=$value["res_busnum"].'/'.$value["res_seat"].'/'.$value["res_spointname"].'/'.$value["res_epointname"]?>";
    <?}?>

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
    
    <?if($daytype == 0){?>
        $j('#ulroute li').eq(1).click();
    
        $j("li[busnum=<?=$res_busnum0?>]").click();
    <?}else{?>
        fnBusSearchDate($j("#SurfBusS").val(), $j("#SurfBusS").attr("gubun"), $j("#SurfBusS").attr("id"));
        fnBusSearchDate($j("#SurfBusE").val(), $j("#SurfBusE").attr("gubun"), $j("#SurfBusE").attr("id"));
        $j("li[busnum=<?=$res_busnum0?>]").click();
        $j("li[busnum=<?=$res_busnum1?>]").click();
    <?}?>

    fnBusChangeNext();

    function fnMapClick(){
        if($j("#ifrmBusMap").css("display") == "none"){
            setTimeout('$j("input[type=button]").eq(0).click();', 500);
        }
    }
</script>