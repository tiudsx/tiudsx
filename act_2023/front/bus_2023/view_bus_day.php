<?php
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/func.php';

header('Content-Type: application/json');

$reqCode = ($_REQUEST["code"] == "") ? "busday" : $_REQUEST["code"];

$groupData = array();

//서핑버스 이용날짜 json
if($reqCode == "busday"){
    $seq = $_REQUEST["seq"];

    if($seq == 7){ //양양
        $busgubun = "('Y', 'S')";
    }else{ //동해
        $busgubun = "('E', 'A')";
    }

    $now = date("Y-m-d");
    $select_query = "SELECT *, REPLACE(RIGHT(bus_date, 5), '-', '') as busjson FROM `AT_PROD_BUS_DAY` WHERE useYN = 'Y' AND bus_gubun IN ".$busgubun." AND bus_date >= '$now' ORDER BY bus_date, bus_gubun, bus_name";
    $result_buslist = mysqli_query($conn, $select_query);
    
    while ($row = mysqli_fetch_assoc($result_buslist)){
        if($row["bus_gubun"] == "Y" || $row["bus_gubun"] == "E"){
            $busType = "S";
        }else{
            $busType = "E";
        }
        $arrBusInfo = array("busnum" => $busType.$row["bus_num"], "busname" => $row["bus_name"], "busseat" => $row["seat"]);
        if($groupData[$busType.$row["busjson"]] == null){
            $groupData[$busType.$row["busjson"]] = array($arrBusInfo);
        }else{
            $groupData[$busType.$row["busjson"]][] = $arrBusInfo;
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
        $groupData[] = array("seatnum" => "$i", "seatYN" => $seatYN);
    }

    $select_query = 'SELECT * FROM `AT_RES_SUB` where res_date = "'.$_REQUEST["busDate"].'" AND res_confirm IN (0, 1, 2, 3, 6, 8) AND res_bus = "'.fnBusCode($_REQUEST["busNum"], "동해").'"';
    $result_setlist = mysqli_query($conn, $select_query);
    while ($row = mysqli_fetch_assoc($result_setlist)){        
        $groupData[$row['res_seat']] = array("seatnum" => $row['res_seat'], "seatYN" => "N");
    }
}else if($reqCode == "busseatcnt"){
    $select_query = "SELECT * FROM AT_PROD_BUS_DAY WHERE bus_date = '".$_REQUEST["busDate"]."' AND concat(bus_gubun, '', bus_num) = '".fnBusCode($_REQUEST["busNum"], "동해")."'";
    
    $result = mysqli_query($conn, $select_query);
    $rowMain = mysqli_fetch_array($result);

    $seat = $rowMain["seat"];
    $channel = $rowMain["channel"];
    $channel = "N";
    
    if($channel == "Y"){
        $groupData[] = array("seatcnt" => $seat);
    }else{
        $select_query = 'SELECT COUNT(*) AS cnt FROM `AT_RES_SUB` where res_date = "'.$_REQUEST["busDate"].'" AND res_confirm IN (0, 1, 2, 3, 6, 8) AND res_bus = "'.fnBusCode($_REQUEST["busNum"], "동해").'"';
        $result_setlist = mysqli_query($conn, $select_query);
        while ($row = mysqli_fetch_assoc($result_setlist)){
            $groupData[] = array("seatcnt" => $row['cnt']);
        }
    }
}

$output = json_encode($groupData, JSON_UNESCAPED_UNICODE);
echo urldecode($output);
?>

