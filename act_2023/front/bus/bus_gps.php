<?
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/func.php';

// 과거 수집 데이터 삭제
mysqli_query($conn, "SET AUTOCOMMIT=0");
mysqli_query($conn, "BEGIN");

$select_query = "DELETE FROM AT_PROD_BUS_GPS_LAST WHERE TIMESTAMPDIFF(MINUTE, insdate, now()) > 30";
$result_set = mysqli_query($conn, $select_query);

mysqli_query($conn, "COMMIT");

?>
<div id="wrap">
    <? include __DIR__.'/../../_layout/_layout_top.php'; ?>

    <link rel="stylesheet" type="text/css" href="/act_2023/_css/surfview.css">
    <link rel="stylesheet" type="text/css" href="/act_2023/_css/bus.css">
    <link rel="stylesheet" type="text/css" href="/act_2023/_css/jquery-ui.css" />

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
if($nowTime > 0500 && $nowTime < 1300){
    $busList = "'Y','E'";
}else if($nowTime >= 1300 && $nowTime < 2300){
    $busList = "'S','A'";
}else{
    $count = 0;
}
//$busList = "'Y','E', 'S','A'";

if($count == 1){
    $arrMapList = array();
    $select_query = "SELECT a.lat, a.lng, b.bus_gubun, b.bus_num, b.bus_gubun, b.bus_num,
                            concat(b.bus_gubun, '', b.bus_num) AS busName,
                            CASE 
                                WHEN LEFT(b.bus_num, 2) = 'Sa' THEN 1
                                WHEN LEFT(b.bus_num, 2) = 'Jo' THEN 2
                                WHEN LEFT(b.bus_num, 2) = 'Y2' THEN 3
                                WHEN LEFT(b.bus_num, 2) = 'Y5' THEN 4
                                WHEN LEFT(b.bus_num, 2) = 'E2' THEN 5
                                WHEN LEFT(b.bus_num, 2) = 'E5' THEN 6
                            END AS ordernum
                        FROM AT_PROD_BUS_GPS_LAST a INNER JOIN AT_PROD_BUS_DAY b
                        ON a.user_name = b.gpsname
                            AND a.gpsdate = b.bus_date
                        WHERE b.bus_gubun IN ($busList)
                            AND b.useYN = 'Y'
                        ORDER BY b.bus_gubun, ordernum, b.bus_num";
    $result_setlist = mysqli_query($conn, $select_query);
    $count = mysqli_num_rows($result_setlist);

    while ($row = mysqli_fetch_assoc($result_setlist)){
        $busNum = $row['busName'];
        $busgubun = $row["bus_gubun"];
        $busName = explode(" ", fnBusNum($busNum));
        $busName = $busName[0]." ".$busName[1].(($busName[1] == "오후" || $busName[1] == "저녁") ? " 출발" : "");

        $lat = $row['lat'];
        $lng = $row['lng'];
        $arrMapList[$row['bus_gubun']] .= '<input type="button" class="bd_btn" btnpoint="point" style="padding-top:4px;" value="'.$busName.'" bus="'.$busNum.'" onclick="fnBusGPSPoint(this);">&nbsp;';
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
    var params = "resparam=mappoint&busgubun=" + busnum;
    $j.ajax({
        type: "POST",
        url: "/act_2023/front/bus/bus_gps_json.php",
        data: params,
        success: function (data) {
            $j("input[btnpoint='point']").css("background", "").css("color", "");
            $j("input[btnpoint='point']").removeAttr("on");
            $j(obj).css("background", "#1973e1").css("color", "#fff");
            $j(obj).attr("on", "Y");

            if(data == 0){
                alert("셔틀버스의 GPS정보가 조회되지 않습니다\n\n페이지가 새로고침 됩니다.");
                setTimeout('window.location.reload();', 500);
            }else{
                var gubun = busnum.substring(0, 1);
                MARKER_POINT = busnum;
                if(gubun == "S" || gubun == "A"){
                    MARKER_ZOOM = 17;
                }else{
                    MARKER_ZOOM = 17;
                }

                MARKER_SPRITE_POSITION2 = eval(data);

                // var busPoint = getBusNum(busnum, 2);
                // $j.each(eval("busPointList" + busPoint), function(key, item) {
                //     //console.log(key + " / " + item);
                //     MARKER_SPRITE_POSITION2[key] = item;
                // });

                // $j.each(eval(data), function(key, item) {
                //     //console.log(key + " / " + item);
                //     MARKER_SPRITE_POSITION2[key] = item;
                // });
                
                $j("#ifrmBusMap").css("display", "block").attr("src", "/act_2023/front/bus/bus_gps_map.html");
            }
        }
    });
    
}
</script>
            
    <div class="bd" style="padding-top:5px;">
        <table class="et_vars">
            <colgroup>
                <col style="width:110px;">
                <col style="width:auto;">
            </colgroup>
            <tbody>
                <tr>
                    <th style="text-align: center;" colspan="2">
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
                        <h1 style='font-size:12px;height:50px;padding-top:20px;'>현재 서핑버스는 운행중이지 않습니다.</h1>
                    </td>
                </tr>
            <?}else{?>
                <?if($arrMapList["Y"]){?>
                <tr>
                    <th>서울 → 양양행</th>
                    <td style="line-height:3;">
                        <?=$arrMapList["Y"]?>
                    </td>
                </tr>
                <?
                }
                
                if($arrMapList["S"]){
                ?>
                <tr>
                    <th>양양 → 서울행</th>
                    <td style="line-height:3;">
                        <?=$arrMapList["S"]?>
                    </td>
                </tr>
                <?
                }
                
                if($arrMapList["E"]){
                ?>
                <tr>
                    <th>서울 → 동해행</th>
                    <td style="line-height:3;">
                        <?=$arrMapList["E"]?>
                    </td>
                </tr>
                <?
                }
                
                if($arrMapList["A"]){
                ?>
                <tr>
                    <th>동해 → 서울행</th>
                    <td style="line-height:3;">
                        <?=$arrMapList["A"]?>
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

<? include __DIR__.'/../../_layout/_layout_bottom.php'; ?>

<script>    
    $j(document).ready(function() {
        setTimeout('$j("input[type=button]").eq(0).click();', 500);
    });
</script>

<script type="text/javascript" src="/act_2023/_js/jquery-ui.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/_js/bus.js?v=<?=time()?>"></script>
<script type="text/javascript" src="/act_2023/_js/busday.js?v=<?=time()?>"></script>