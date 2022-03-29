<?php
include __DIR__.'/../db.php';

$param = $_REQUEST["resparam"];
$busgubun = $_REQUEST["busgubun"];

if($param == "mappoint"){ //상세정보
    $select_query = "DELETE FROM AT_PROD_BUS_GPS_LAST WHERE TIMESTAMPDIFF(MINUTE, insdate, now()) > 30";
    $result_set = mysqli_query($conn, $select_query);

    $select_query = "SELECT * FROM AT_PROD_BUS_GPS_LAST a INNER JOIN AT_PROD_BUS b
                        ON a.user_name = b.gpsname
                            AND a.gpsdate = b.busdate
                        WHERE concat(b.busgubun, '', b.busnum) = '".$busgubun."'
                            AND b.use_yn = 'Y'
                        ORDER BY b.busgubun DESC, b.busnum";
    $result_setlist = mysqli_query($conn, $select_query);
    $count = mysqli_num_rows($result_setlist);

    
    $now = date("Y-m-d H:i:s");
    $weekNum = date("w", strtotime($now));
    $nowTime = date("Hi", strtotime($now));

    $busGPS = "";
    $mapNum = 0;
    while ($row = mysqli_fetch_assoc($result_setlist)){
        $busNum = $row['busgubun'].$row['busnum'];
        $busgubun = $row["busgubun"];
        $busName = $row['busname'];
        $user_name = $row['user_name'];
        $lat = $row['lat'];
        $lng = $row['lng'];
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
    
        if($busNum == "Y1" || $busNum == "Y3" || $busNum == "Y5" || $busNum == "E1" || $busNum == "E3" || $busNum == "E5"){
            $mappoint = "'신도림': [0, MARKER_SPRITE_Y_OFFSET*3, '37.5095592', '126.8885712', '홈플러스 신도림점 앞', '탑승시간 : <font color=red>06시 20분</font>', 0, 's1', '', ''],".
                        "'대림역' : [MARKER_SPRITE_X_OFFSET*1, MARKER_SPRITE_Y_OFFSET*3, '37.4928008', '126.8947074', '대림역 2번출구 앞', '탑승시간 : <font color=red>06시 30분</font>', 1, 's2', '', ''],".
                        "'봉천역': [MARKER_SPRITE_X_OFFSET*2, MARKER_SPRITE_Y_OFFSET*3, '37.4821436', '126.9426997', '봉천역 1번출구 앞', '탑승시간 : <font color=red>06시 40분</font>', 2, 's3', '', ''],".
                        "'사당역': [MARKER_SPRITE_X_OFFSET*3, MARKER_SPRITE_Y_OFFSET*3, '37.4764763', '126.977734', '사당역 6번출구 방향 신한성약국 앞', '탑승시간 : <font color=red>06시 50분</font>', 3, 's4', '', ''],".
                        "'강남역': [MARKER_SPRITE_X_OFFSET*4, MARKER_SPRITE_Y_OFFSET*3, '37.4982078', '127.0290928', '강남역 1번출구 버스정류장', '탑승시간 : <font color=red>07시 05분</font>', 4, 's5', '', ''],".
                        "'종합운동장역': [MARKER_SPRITE_X_OFFSET*5, MARKER_SPRITE_Y_OFFSET*3, '37.5104765', '127.0722925', '종합운동장역 4번출구 방향 버스정류장', '탑승시간 : <font color=red>07시 20분</font>', 5, 's6', '', '']";
        }else if($busNum == "Y2" || $busNum == "Y4" || $busNum == "Y6" || $busNum == "E2" || $busNum == "E4" || $busNum == "E6"){
            $mappoint = "'당산역': [0, MARKER_SPRITE_Y_OFFSET*3, '37.5344135', '126.9012162', '당산역 13출구 IBK기업은행 앞', '탑승시간 : <font color=red>06시 05분</font>', 0, 's1', '', ''],".
                        "'합정역': [MARKER_SPRITE_X_OFFSET*1, MARKER_SPRITE_Y_OFFSET*3, '37.5507926', '126.9159159', '합정역 3번출구 앞', '탑승시간 : <font color=red>06시 10분</font>', 1, 's2', '', ''],".
                        "'종로3가역': [MARKER_SPRITE_X_OFFSET*2, MARKER_SPRITE_Y_OFFSET*3, '37.5703347', '126.99317687', '종로3가역 12번출구 방향 새마을금고 앞', '탑승시간 : <font color=red>06시 35분</font>', 2, 's3', '', ''],".
                        "'왕십리역': [MARKER_SPRITE_X_OFFSET*3, MARKER_SPRITE_Y_OFFSET*3, '37.5615557', '127.0348018', '왕십리역 11번출구 방향 우리은행 앞', '탑승시간 : <font color=red>06시 50분</font>', 3, 's4', '', ''],".
                        "'건대입구': [MARKER_SPRITE_X_OFFSET*4, MARKER_SPRITE_Y_OFFSET*3, '37.5393413', '127.0716672', '건대입구역 롯데백화점 스타시티점 입구', '탑승시간 : <font color=red>07시 05분</font>', 4, 's5', '', ''],".
                        "'종합운동장역': [MARKER_SPRITE_X_OFFSET*5, MARKER_SPRITE_Y_OFFSET*3, '37.5104765', '127.0722925', '종합운동장역 4번출구 방향 버스정류장', '탑승시간 : <font color=red>07시 20분</font>', 5, 's6', '', '']";
        }
        
        if($busgubun == "S"){
            $mappoint = "'청시행비치': [0, 0, '37.910099', '128.8168456', '청시행비치 주차장 입구', '탑승시간 : <font color=red>13시 15분 / 17시 15분</font>', 0, 's1', '', ''],".
                        "'남애해변': [MARKER_SPRITE_X_OFFSET*1, 0, '37.9452543', '128.7814356', '남애3리 입구', '탑승시간 : <font color=red>14시 30분 / 17시 30분</font>', 1, 's2', '', ''],".
                        "'인구해변': [MARKER_SPRITE_X_OFFSET*2, 0, '37.9689758', '128.7599915', '현남면사무소 맞은편', '탑승시간 : <font color=red>13시 35분 / 17시 35분</font>', 2, 's3', '', ''],".
                        "'죽도해변': [MARKER_SPRITE_X_OFFSET*3, 0, '37.9720003', '128.7595433', 'GS25 죽도비치점 맞은편', '탑승시간 : <font color=red>13시 40분 / 17시 40분</font>', 3, 's4', '', ''],".
                        "'동산항해변': [MARKER_SPRITE_X_OFFSET*4, 0, '37.9763045', '128.7586692', '동산항해변 입구', '탑승시간 : <font color=red>13시 45분 / 17시 45분</font>', 4, 's5', '', ''],".
                        "'기사문해변': [MARKER_SPRITE_X_OFFSET*5, 0, '38.0053627', '128.7306342', '기사문 해변주차장 입구', '탑승시간 : <font color=red>13시 50분 / 17시 50분</font>', 5, 's6', '', ''],".
                        "'서피비치': [MARKER_SPRITE_X_OFFSET*6, 0, '38.0268271', '128.7169575', '서피비치 회전교차로 횡단보도 앞', '탑승시간 : <font color=red>14시 00분 / 18시 00분</font>', 6, 's7', '', '']";
        }else if($busgubun == "A"){
            $mappoint = "'솔.동해점': [0, 0, '37.5782382', '129.1156248', '솔.동해점 입구', '탑승시간 : <font color=red>14시 00분 / 17시 00분</font>', 0, 's1', '', ''],".
                        "'대진항': [MARKER_SPRITE_X_OFFSET*1, 0, '37.5807657', '129.111344', '대진항 공영주차장 입구', '탑승시간 : <font color=red>14시 05분 / 17시 05분</font>', 1, 's2', '', ''],".
                        "'금진해변': [MARKER_SPRITE_X_OFFSET*2, 0, '37.6347202', '129.0450586', '금진해변 공영주차장 입구', '탑승시간 : <font color=red>14시 20분 / 17시 20분</font>', 2, 's3', '', '']";
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

        $busImg = "https://surfenjoy.cdn3.cafe24.com/act_bus/surfbus_".$busNum.".jpg?v=0|";
        $busGPS .= "busGPSList.bus = {".$mappoint.",'$busNum': [MARKER_SPRITE_X_OFFSET*$mapNum, MARKER_SPRITE_Y_OFFSET*4, '$lat', '$lng', '$busImg', '$insdate', '$gpsTime 위치', '$locationname', '$busName', '$busgubun']}";
        $mapNum++;
    }

    if($count == 0){
        echo 0;
    }else{
        // $dbdata = array();
        // $i = 0;
        // while ( $row = $result->fetch_assoc()){
        //     $dbdata[$i] = $row;
        //     $i++;
        // }

        // $output = json_encode($dbdata, JSON_UNESCAPED_UNICODE);
        // echo urldecode($output);

        echo $busGPS;
    }
}
?>