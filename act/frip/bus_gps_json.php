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
                            AND b.seq IN (210,211)
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
        $busName = str_replace("서울출발", "", str_replace("서울복귀", "", $row["busname"]));
        $user_name = $row['user_name'];
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
    
        if($busNum == "Y1" || $busNum == "Y2" || $busNum == "Y3" || $busNum == "Y4" || $busNum == "Y5" || $busNum == "Y6"){
            $mappoint = "'공덕역': [0, MARKER_SPRITE_Y_OFFSET*3, '37.5453585', '126.9514437', '공덕역 3번출구 앞', '탑승시간 : <font color=red>10시 30분</font>', 0, 's1', '', ''],".
                        "'건대입구' : [MARKER_SPRITE_X_OFFSET*1, MARKER_SPRITE_Y_OFFSET*3, '37.5393413', '127.0716672', '건대입구역 롯데백화점 스타시티점 입구', '탑승시간 : <font color=red>11시 10분</font>', 1, 's2', '', ''],".
                        "'니지모리': [0, 0, '37.8792025', '127.0920022', '니지모리 스튜디오 주차장', '탑승시간 : <font color=red>21시 00분</font>', 0, 's1', '', '']";
        }else if($busNum == "E1" || $busNum == "E2"){
            $mappoint = "'사당역': [MARKER_SPRITE_X_OFFSET*0, MARKER_SPRITE_Y_OFFSET*3, '37.4764763', '126.977734', '사당역 6번출구 방향 참약사 장수약국 앞', '탑승시간 : <font color=red>12시 00분</font>', 0, 's1', '', ''],".
                        "'강남역': [MARKER_SPRITE_X_OFFSET*1, MARKER_SPRITE_Y_OFFSET*3, '37.4982078', '127.0290928', '강남역 1번출구 버스정류장', '탑승시간 : <font color=red>12시 20분</font>', 1, 's2', '', ''],".
                        "'종합운동장역': [MARKER_SPRITE_X_OFFSET*2, MARKER_SPRITE_Y_OFFSET*3, '37.5104765', '127.0722925', '종합운동장역 4번출구 방향 버스정류장 뒤쪽', '탑승시간 : <font color=red>12시 40분</font>', 2, 's3', '', ''],".
                        "'비행장 무대': [MARKER_SPRITE_X_OFFSET*3, MARKER_SPRITE_Y_OFFSET*3, '37.160496', '128.2139093', '제천제일감리교회 주차장 입구', '탑승시간<br><font color=red><span style=padding-left:20px;>8월 12일 : 25시 00분</span><br><span style=padding-left:20px;>8월 13일, 14일 : 23시 00분</span><br><span style=padding-left:20px;>8월 15일 : 25시 30분</span></font>', 3, 's4', '', ''],".
                        "'메가박스 제천': [MARKER_SPRITE_X_OFFSET*4, MARKER_SPRITE_Y_OFFSET*3, '37.1357647', '128.2110021', '제천 황금당 앞 노상주차장', '탑승시간<br><font color=red style=padding-left:20px;>8월 12일 : 25시 10분<br><span style=padding-left:20px;>8월 13일, 14일 : 23시 10분</span><br><span style=padding-left:20px;>8월 15일 : 25시 40분</font>', 4, 's5', '', '']";
        }
        
        if($busgubun == "S"){
            $mappoint = "'니지모리': [0, 0, '37.910099', '128.8168456', '니지모리 스튜디오 주차장', '탑승시간 : <font color=red>21시 00분</font>', 0, 's1', '', '']";
        }else if($busgubun == "A"){
            $mappoint = "'솔.동해점': [0, 0, '37.5782382', '129.1156248', '솔.동해점 입구', '탑승시간 : <font color=red>14시 00분 / 17시 00분</font>', 0, 's1', '', ''],".
                        "'대진항': [MARKER_SPRITE_X_OFFSET*1, 0, '37.5807657', '129.111344', '대진항 공영주차장 입구', '탑승시간 : <font color=red>14시 05분 / 17시 05분</font>', 1, 's2', '', ''],".
                        "'금진해변': [MARKER_SPRITE_X_OFFSET*2, 0, '37.6347202', '129.0450586', '금진해변 공영주차장 입구', '탑승시간 : <font color=red>14시 20분 / 17시 20분</font>', 2, 's3', '', '']";
        }
        
        if($busgubun == "Y"){
            $locationname = "니지모리";
        }else if($busgubun == "S"){
            $locationname = "양양 → 서울행";
        }else if($busgubun == "E"){
            $locationname = "제천국제음악영화제";
        }else if($busgubun == "A"){
            $locationname = "동해 → 서울행";
        }

        $busNumImg = "Y1";
        if($gpsname == "니지모리1호차"){
            $busNumImg = "Y1";
        }else if($gpsname == "니지모리2호차"){
            $busNumImg = "Y2";
        }else if($gpsname == "니지모리3호차"){
            $busNumImg = "Y3";
        }else if($gpsname == "니지모리4호차"){
            $busNumImg = "Y4";
        }

        //$busImg = "https://actrip.cdn1.cafe24.com/act_bus/frip/surfbus_".$busNumImg.".jpg?v=1|";
        $busImg = "https://actrip.cdn1.cafe24.com/act_bus/frip/surfbus_frip.jpg?v=1|";
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