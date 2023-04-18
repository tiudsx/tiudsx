<?php
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/func.php';

$param = $_REQUEST["resparam"];
$busgubun = $_REQUEST["busgubun"];

if($param == "mappoint"){ //상세정보
    $select_query = "DELETE FROM AT_PROD_BUS_GPS_LAST WHERE TIMESTAMPDIFF(MINUTE, insdate, now()) > 30";
    $result_set = mysqli_query($conn, $select_query);

    $select_query = "SELECT b.gpsname, a.lat, a.lng, a.insdate, b.bus_gubun, b.bus_num, b.bus_gubun, b.bus_num, 
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
                        WHERE concat(b.bus_gubun, '', b.bus_num) = '$busgubun'
                            AND b.useYN = 'Y'
                        ORDER BY b.bus_gubun, ordernum, b.bus_num";
    $result_setlist = mysqli_query($conn, $select_query);
    $count = mysqli_num_rows($result_setlist);
    
    $now = date("Y-m-d H:i:s");
    $weekNum = date("w", strtotime($now));
    $nowTime = date("Hi", strtotime($now));

    $busGPS = "";
    $mappoint = "";
    $mapNum = 0;

    $busDataJson = fnBusPoint("", "");
    $arrBusGubun = substr($busgubun, 0, 3);
    $arrBusPoint = substr($busgubun, 1, 2);
    $arrBusData = array();
    foreach($busDataJson as $key => $value){
        if($arrBusGubun == substr($key, 0, 3)){
            $arrBusData[explode("_", $key)[1]] = $value;
        }    
    }

    $i = 0;

    if($arrBusPoint == "Sa" || $arrBusPoint == "Jo"){
        $MARKER_SPRITE_Y_OFFSET = "MARKER_SPRITE_Y_OFFSET*3";
    }else{
        $MARKER_SPRITE_Y_OFFSET = "0";
    }

    foreach($arrBusData as $key => $value){
        $arrVlu = explode("|", $value);
        
        $timeText1 = explode(":", $arrVlu[0])[0];
        $timeText2 = explode(":", $arrVlu[0])[1];

        $mappoint .= "'$key': [MARKER_SPRITE_X_OFFSET*".$i.", ".$MARKER_SPRITE_Y_OFFSET.", '".$arrVlu[2]."', '".$arrVlu[3]."', '".$arrVlu[1]."', '탑승시간 : <font color=red>".$timeText1."시 ".$timeText2."분</font>', 0, 's".($i + 1)."', '', '']";
        
        if(!next($arrBusData)) { //마지막 처리
        }else{
            $mappoint .= ",";
        }

        $i++;
    }

    while ($row = mysqli_fetch_assoc($result_setlist)){
        $busNum = $row['busName'];
        $busgubun = $row['bus_gubun'];
        $busName = explode(" ", fnBusNum($busNum));
        $busName = $busName[0]." ".$busName[1].(($busName[1] == "오후" || $busName[1] == "저녁") ? " 출발" : "");
        
        $lat = $row['lat'];
        $lng = $row['lng'];
        $insdate = $row['insdate'];
        $gpsname = $row['gpsname'];

        $todayTime = date("h시 i분", strtotime($insdate));

        $todayDate = date("Y-m-d H:i:s", strtotime($insdate));
        $toNow = (strtotime($now)-strtotime($todayDate));

        $gpsTime = $toNow."초 전";
        if($toNow > 60){
            $toNowMin = (int)((strtotime($now)-strtotime($todayDate)) / 60);
            $toNowS = $toNow - ($toNowMin * 60);

            $gpsTime = $toNowMin."분 ".$toNowS."초 전";
        }

        if($busgubun == "Y"){
            $locationname = "서울 → 양양행";
        }else if($busgubun == "S"){
            $locationname = "양양 → 서울행";
        }else if($busgubun == "E"){
            $locationname = "서울 → 동해행";
        }else if($busgubun == "A"){
            $locationname = "동해 → 서울행";
        }

        $busNumImg = "Y1";
        // if($gpsname == "양양 1호차"){
        //     $busNumImg = "Y1";
        // }else if($gpsname == "양양 2호차"){
        //     $busNumImg = "Y2";
        // }else if($gpsname == "양양 3호차"){
        //     $busNumImg = "Y3";
        // }

        $busImg = "https://actrip.cdn1.cafe24.com/act_bus/surfbus_".$busNumImg.".jpg?v=1|";
        $busGPS .= "busGPSList.bus = {".$mappoint.",'$busNum': [MARKER_SPRITE_X_OFFSET*$mapNum, MARKER_SPRITE_Y_OFFSET*4, '$lat', '$lng', '$busImg', '$insdate', '$gpsTime 위치', '$locationname', '$busName', '$busgubun']}";
        $mapNum++;
    }

    if($count == 0){
        echo 0;
    }else{
        echo $busGPS;
    }
}

?>