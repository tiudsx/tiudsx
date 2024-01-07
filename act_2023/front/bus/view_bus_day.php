<?php
include __DIR__.'/../../common/db.php';

header('Content-Type: application/json');

$reqCode = ($_REQUEST["code"] == "") ? "busday" : $_REQUEST["code"];

$groupData = array();

//서핑버스 이용날짜 json
if($reqCode == "busday"){
    $shopseq = $_REQUEST["seq"];

    if($shopseq == 7){
        $busgubun = "YY";
    }else if($shopseq == 14){
        $busgubun = "DH";
    }

    $now = date("Y-m-d");
    $select_query = "SELECT *, REPLACE(RIGHT(bus_date, 5), '-', '') as busjson FROM `AT_PROD_BUS_DAY` WHERE useYN = 'Y' AND bus_line = '$busgubun' AND bus_date >= '$now' ORDER BY bus_date, bus_gubun";
    $result_buslist = mysqli_query($conn, $select_query);
    
    while ($row = mysqli_fetch_assoc($result_buslist)){
        $arrBusInfo = array("bus_gubun" => $row["bus_gubun"], "bus_num" => $row["bus_num"], "bus_name" => $row["bus_name"], "bus_seat" => $row["seat"]);
        if($groupData[$row["bus_gubun"].$row["busjson"]] == null){
            $groupData[$row["bus_gubun"].$row["busjson"]] = array($arrBusInfo);
        }else{
            $groupData[$row["bus_gubun"].$row["busjson"]][] = $arrBusInfo;
        }
    }
//서핑버스 실시간 좌석 조회
}else if($reqCode == "busseat"){

    /*
    예약상태
        0 : 미입금
        1 : 예약대기
        2 : 임시확정
        3 : 확정
        4 : 환불요청
        5 : 환불완료
        6 : 임시취소
        7 : 취소
        8 : 입금완료
    */

    for ($i=0; $i <= 45; $i++) { 
        $seatYN = "Y";
        // //$_REQUEST["busNum"] == "YSa1" || $_REQUEST["busNum"] == "SY51" ||         
        // if(($_REQUEST["busNum"] == "ESa1" || $_REQUEST["busNum"] == "AE51") && $i >= 29){
        //     $seatYN = "N";
        // }

        // // if(($_REQUEST["busNum"] == "ESa1" || $_REQUEST["busNum"] == "AE51") && $i <= 20){
        // //     $seatYN = "N";
        // // }

        // // if(($_REQUEST["busNum"] == "YSa1" || $_REQUEST["busNum"] == "SY51") && $i >= 29){
        // //     $seatYN = "N";
        // // }

        // //양양행 - 프립
        // if(($_REQUEST["busNum"] == "YSa1" ) && $i >= 33 && $_REQUEST["busDate"] == "2023-05-27"){
        //     $seatYN = "N";
        // }

        // //서울행 - 프립
        // if(($_REQUEST["busNum"] == "SY51") && $i >= 33 && $_REQUEST["busDate"] == "2023-05-28"){
        //     $seatYN = "N";
        // }
        $groupData[] = array("seatnum" => "$i", "seatYN" => $seatYN);
    }

    $select_query = 'SELECT * FROM `AT_RES_SUB` where res_date = "'.$_REQUEST["busDate"].'" AND res_confirm IN (0, 1, 2, 3, 6, 8) AND res_bus = "'.$_REQUEST["busNum"].'"';
    $result_setlist = mysqli_query($conn, $select_query);
    while ($row = mysqli_fetch_assoc($result_setlist)){        
        $groupData[$row['res_seat']] = array("seatnum" => $row['res_seat'], "seatYN" => "N");
    }
}else if($reqCode == "busseatcnt"){
    $select_query = "SELECT * FROM AT_PROD_BUS_DAY WHERE bus_date = '".$_REQUEST["busDate"]."' AND concat(bus_gubun, '', bus_num) = '".$_REQUEST["busNum"]."'";
    $result = mysqli_query($conn, $select_query);
    $rowMain = mysqli_fetch_array($result);

    $seat = $rowMain["seat"];
    $channel = $rowMain["channel"];
    $channel = "N";
    
    if($channel == "Y"){
        $groupData[] = array("seatcnt" => $seat);
    }else{
        $select_query = 'SELECT COUNT(*) AS cnt FROM `AT_RES_SUB` where res_date = "'.$_REQUEST["busDate"].'" AND res_confirm IN (0, 1, 2, 3, 6, 8) AND res_bus = "'.$_REQUEST["busNum"].'"';
        $result_setlist = mysqli_query($conn, $select_query);
        while ($row = mysqli_fetch_assoc($result_setlist)){
            $groupData[] = array("seatcnt" => $row['cnt']);
        }
    }
}else if($reqCode == "frip_seatcnt"){
    $seq = $_REQUEST["seq"];
    $select_query = 'SELECT COUNT(*) AS cnt FROM `AT_RES_SUB` where res_date = "'.$_REQUEST["busDate"].'" AND res_confirm IN (0, 1, 2, 3, 6, 8) AND res_bus = "'.$_REQUEST["busNum"].'" AND seq = '.$seq;
    $result_setlist = mysqli_query($conn, $select_query);
    while ($row = mysqli_fetch_assoc($result_setlist)){
        $groupData[] = array("seatcnt" => $row['cnt']);
    }
}else if($reqCode == "frip_busseat"){

    /*
    예약상태
        0 : 미입금
        1 : 예약대기
        2 : 임시확정
        3 : 확정
        4 : 환불요청
        5 : 환불완료
        6 : 임시취소
        7 : 취소
        8 : 입금완료
    */

    for ($i=0; $i <= 45; $i++) { 
        $seatYN = "Y";

        //동해행 - 프립
        if(($_REQUEST["busNum"] == "ESa1" || $_REQUEST["busNum"] == "AE51") && $i < 21){
            //$seatYN = "N";
        }

        //양양행 - 프립
        if(($_REQUEST["busNum"] == "YSa1" ) && $i < 33 && $_REQUEST["busDate"] == "2023-05-27"){
            $seatYN = "N";
        }

        //서울행 - 프립
        if(($_REQUEST["busNum"] == "SY51") && $i < 33 && $_REQUEST["busDate"] == "2023-05-28"){
            $seatYN = "N";
        }
        $groupData[] = array("seatnum" => "$i", "seatYN" => $seatYN);
    }

    $select_query = 'SELECT * FROM `AT_RES_SUB` where res_date = "'.$_REQUEST["busDate"].'" AND res_confirm IN (0, 1, 2, 3, 6, 8) AND res_bus = "'.$_REQUEST["busNum"].'"';
    $result_setlist = mysqli_query($conn, $select_query);
    while ($row = mysqli_fetch_assoc($result_setlist)){        
        $groupData[$row['res_seat']] = array("seatnum" => $row['res_seat'], "seatYN" => "N");
    }
}

$output = json_encode($groupData, JSON_UNESCAPED_UNICODE);
echo urldecode($output);
?>

