<?
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/func.php';

// 과거 수집 데이터 삭제
mysqli_query($conn, "SET AUTOCOMMIT=0");
mysqli_query($conn, "BEGIN");

$select_query = "DELETE FROM AT_PROD_BUS_GPS_LAST WHERE TIMESTAMPDIFF(MINUTE, insdate, now()) > 30";
$result_set = mysqli_query($conn, $select_query);

mysqli_query($conn, "COMMIT");

$param_mid = $_REQUEST["mid"];
$admin_use = $_REQUEST["admin"];

if($param_mid == ""){
	$param = str_replace("/", "", $_SERVER["REQUEST_URI"]);

	if (!empty(strpos($_SERVER["REQUEST_URI"], '?'))){
		$param = substr($param, 0, strpos($_SERVER["REQUEST_URI"], '?') - 1);
	}

	$param = explode('_', $param)[0];
}else{
	$param = $param_mid;
}

$coupon_seq = 2;
$gpsfolder = "";
if($param == "surfbusgps_2023"){
    $coupon_seq = 1;
    //$gpsfolder = "_2023";
}
?>
<div id="wrap">
    <? include __DIR__.'/../../_layout/_channel_layout_top.php'; ?>

    <link rel="stylesheet" type="text/css" href="/act_2023/front/_css/surfview.css">
    <link rel="stylesheet" type="text/css" href="/act_2023/front/_css/bus.css">

    <div class="top_area_zone">
        <section class="shoptitle">
            <div style="padding:6px;">
                <h1> 서핑버스 실시간 위치조회</h1>
                <div class="reviewcnt">※ 서핑버스 현재위치를 1분마다 수집하여 표시됩니다.</div>
                <div class="shopsubtitle">※ 실제위치와 오차가 있을 수 있으니 참고부탁드립니다.</div>
            </div>
        </section>

        <section class="notice">
            <div class="vip-tabwrap">
                <div id="tabnavi" class="fixed1" style="top: 49px;">
                    <div class="vip-tabnavi">
                        <ul>
                            <li class="on"><a>현재위치 조회</a></li>
                            <li><a id="counttimer" style="font-size:0.9em;font-weight:200;"></a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div id="view_tab2" style="min-height: 800px;">

<?
$now = date("Y-m-d H:i:s");
//$now = "2020-06-27 13:29:00";
$weekNum = date("w", strtotime($now));
$nowTime = date("Hi", strtotime($now));

$count = 1;
if($nowTime > 0500 && $nowTime < 1200){
    $busList = "start";
}else if($nowTime >= 1200 && $nowTime < 2300){
    $busList = "return";
}else{
    $count = 0;
}
//  $busList = "'Y','E', 'S','A'";

if($count == 1){
    $arrMapList = array();
    $select_query = "SELECT a.lat, a.lng, b.bus_gubun, b.bus_num, b.bus_gubun, b.bus_line,
                            concat(b.bus_gubun, '', b.bus_num) AS busNum,
                            CASE 
                                WHEN b.bus_gubun = 'SA' THEN 1
                                WHEN b.bus_gubun = 'JO' THEN 2
                                WHEN b.bus_gubun = 'AM' THEN 3
                                WHEN b.bus_gubun = 'PM' THEN 4
                            END AS ordernum
                        FROM AT_PROD_BUS_GPS_LAST a INNER JOIN AT_PROD_BUS_DAY b
                        ON a.user_name = b.gpsname
                        WHERE b.bus_oper = '$busList'
                            AND b.useYN = 'Y'
                            AND b.bus_date = '".date("Y-m-d")."'
                        ORDER BY ordernum";
    $result_setlist = mysqli_query($conn, $select_query);
    $count = mysqli_num_rows($result_setlist);
    
    while ($row = mysqli_fetch_assoc($result_setlist)){
        $busNum = $row['busNum'];
        $bus_line = $row["bus_line"];
        $busgubun = $row["bus_gubun"];
        $bus_num = $row["bus_num"];
        $busName = fnBusNum2023($busgubun.$bus_num)["full"];

        if($bus_line == "YY"){
            $shopseq = 7;
        }else{
            $shopseq = 14;            
        }

        $lat = $row['lat'];
        $lng = $row['lng'];
        $arrMapList[$row['bus_line']] .= '<input type="button" class="bd_btn" btnpoint="point" style="padding-top:4px;" value="'.$busName.'" bus="'.$busNum.'" shopseq="'.$shopseq.'" onclick="fnBusGPSPoint(this);">&nbsp;';
    }
}
?>

<script>
var MARKER_SPRITE_X_OFFSET = 29,
    MARKER_SPRITE_Y_OFFSET = 50;

var busGPSList = {}
var MARKER_POINT = "", MARKER_ZOOM = 16;
var MARKER_SPRITE_POSITION2 = {};
function fnBusGPSPoint(obj) {
    var busnum = $j(obj).attr("bus");
    var shopseq = $j(obj).attr("shopseq");
    var params = "resparam=mappoint&shopseq=" + shopseq + "&busgubun=" + busnum;
    $j.ajax({
        type: "POST",
        url: "/act_2023/front/bus<?=$gpsfolder?>/bus_gps_json.php",
        data: params,
        success: function (data) {
            $j("input[btnpoint='point']").css("background", "").css("color", "");
            $j("input[btnpoint='point']").removeAttr("on");
            $j(obj).css("background", "#1973e1").css("color", "#fff");
            $j(obj).attr("on", "Y");

            if(data == 0){
                alert("셔틀버스의 GPS정보가 조회되지 않습니다.\n\n고객센터에 문의주세요");
                //setTimeout('window.location.reload();', 500);
            }else{
                var gubun = busnum.substring(0, 1);
                MARKER_POINT = busnum;
                if(gubun == "S" || gubun == "A"){
                    MARKER_ZOOM = 17;
                }else{
                    MARKER_ZOOM = 17;
                }

                MARKER_SPRITE_POSITION2 = eval(data);
                
                $j("#ifrmBusMap").css("display", "block").attr("src", "/act_2023/front/bus/bus_gps_map.html");
            }
        }
    });
    
}
</script>
            
    <div class="bd" style="padding-top:5px;">
        <?if($admin_use == 1){?>
        <table class="et_vars">
            <colgroup>
                <col style="width:110px;">
                <col style="width:auto;">
            </colgroup>
            <tbody>
                <tr>
                    <th>호차</th>
                    <th>시간</th>
                </tr>
                <?
                    $select_query = "SELECT * FROM AT_PROD_BUS_GPS_LAST
                                        ORDER BY user_name";
                    $result_setlist = mysqli_query($conn, $select_query);
                    $count = mysqli_num_rows($result_setlist);

                    while ($row = mysqli_fetch_assoc($result_setlist)){
                        $user_name = $row['user_name'];
                        $insdate = $row['insdate'];

                        $todayTime = date("h시 i분", strtotime($insdate));

                        $todayDate = date("Y-m-d H:i:s", strtotime($insdate));
                        $toNow = (strtotime($now)-strtotime($todayDate));

                        $gpsTime = $toNow."초 전";
                        if($toNow > 60){
                            $toNowMin = (int)((strtotime($now)-strtotime($todayDate)) / 60);
                            $toNowS = $toNow - ($toNowMin * 60);

                            $gpsTime = $toNowMin."분 ".$toNowS."초 전";
                        }

                        echo "<tr><td>$user_name</td><td>$gpsTime</td></tr>";
                    }
                ?>
            </tbody>
        </table>
        <?}?>

        <table class="et_vars">
            <colgroup>
                <col style="width:110px;">
                <col style="width:auto;">
            </colgroup>
            <tbody>
                <tr>
                    <th colspan="2">
                        <strong style="line-height:2;">
                            ★ 액트립 셔틀버스 운행 차량
                        </strong>
                    </th>
                </tr>
                <!-- <tr>
                    <td style="text-align:center;line-height:3;" colspan="2">
                        <h1 style='font-size:12px;height:80px;padding-top:5px;'>
                            ※사당선 1,3호차와 종로선 2,4호차는 같은 위치로 조회됩니다.<br>
                            ※서울행 1,2호차는 같은 위치로 조회됩니다.
                        </h1>
                    </td>
                </tr> -->

            <?if($count == 0){?>
                <tr>
                    <td style="text-align:center;line-height:3;" colspan="2">
                        <h1 style='font-size:12px;height:50px;padding-top:20px;'>현재 셔틀버스는 운행중이지 않습니다.</h1>
                    </td>
                </tr>
            <?}else{?>
                <?if($arrMapList["YY"]){?>
                <tr>
                    <th>서울 ↔ 양양</th>
                    <td style="line-height:3;">
                        <?=$arrMapList["YY"]?>
                    </td>
                </tr>
                <?
                }

                if($arrMapList["DH"]){
                ?>
                <tr>
                    <th>서울 ↔ 동해</th>
                    <td style="line-height:3;">
                        <?=$arrMapList["DH"]?>
                    </td>
                </tr>
                <?
                }                      
            }
            ?>
            </tbody>
        </table>
    </div>

    <iframe scrolling="no" frameborder="0" id="ifrmBusMap" name="ifrmBusMap" style="width:100%;height:450px;display:none;"></iframe>

            </div>
        </section>
    </div>
</div>

<? include __DIR__.'/../../_layout/_channel_layout_bottom.php'; ?>

<script>    
    $j(document).ready(function() {
        setTimeout('$j("input[type=button]").eq(0).click();', 500);
    });
</script>