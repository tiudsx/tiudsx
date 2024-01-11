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
        $arrBusInfo = array("bus_gubun" => $row["bus_gubun"], "bus_num" => $row["bus_num"], "bus_seat" => $row["seat"]);
        if($groupData[$row["bus_gubun"].$row["busjson"]] == null){
            $groupData[$row["bus_gubun"].$row["busjson"]] = array($arrBusInfo);
        }else{
            $groupData[$row["bus_gubun"].$row["busjson"]][] = $arrBusInfo;
        }
    }
//서핑버스 실시간 좌석 조회
}else if($reqCode == "busseat"){

    $shopseq = $_REQUEST["shopseq"];
    $bus_date = $_REQUEST["bus_date"];
    $bus_gubun = $_REQUEST["bus_gubun"];
    $bus_num = $_REQUEST["bus_num"];
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

    $select_query = "SELECT * FROM `AT_RES_SUB` where res_confirm IN (0, 1, 2, 3, 6, 8) AND res_date = '$bus_date' AND seq = $shopseq AND bus_gubun = '$bus_gubun' AND bus_num = '$bus_num'";
    $result_setlist = mysqli_query($conn, $select_query);
    while ($row = mysqli_fetch_assoc($result_setlist)){        
        $groupData[$row['res_seat']] = array("seatnum" => $row['res_seat'], "seatYN" => "N");
    }

}else if($reqCode == "busseatcnt"){
    //셔틀버스 노선표시
    $bus_date = $_REQUEST["bus_date"];
	$arrGubun = explode('_', $_REQUEST["bus_line"]);
    $bus_line = $arrGubun[0];
    $orderby = "";
    if($arrGubun[1] == "S"){
        $bus_gubun = "'SA', 'JO'";
        $orderby = "DESC";
    }else{
        $bus_gubun = "'AM', 'PM'";
        $orderby = "ASC";
    }

    $select_query = "SELECT bus_gubun, bus_num, seat, price,
            (SELECT COUNT(*) AS cnt FROM `AT_RES_SUB` where res_confirm IN (0, 1, 2, 3, 6, 8) AND res_date = a.bus_date AND bus_line = a.bus_line AND bus_gubun = a.bus_gubun AND bus_num = a.bus_num) AS seatcnt
         FROM `AT_PROD_BUS_DAY` AS a WHERE bus_date = '$bus_date' AND bus_line = '$bus_line' AND bus_gubun IN ($bus_gubun)
         ORDER BY bus_gubun $orderby, bus_num
         ";

    $result = mysqli_query($conn, $select_query);
    
    while ( $row = $result->fetch_assoc()){
        $bus_name = "";
        if($row['bus_gubun'] == "SA"){
            $bus_name = "사당선 ";
        }else if($row['bus_gubun'] == "JO"){
            $bus_name = "종로선 ";
        }else if($row['bus_gubun'] == "AM"){
            $bus_name = "오후 ";
        }else if($row['bus_gubun'] == "PM"){
            $bus_name = "저녁 ";
        }
        
        $row["bus_name"] = $bus_name.$row["bus_num"]."호차";
        $row["bus_price"] = $row["price"];

        $groupData[] = $row;
    }
}

$output = json_encode($groupData, JSON_UNESCAPED_UNICODE);
echo urldecode($output);
?>

