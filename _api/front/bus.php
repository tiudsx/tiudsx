<?
//버스 조회
if($code == "bus"){
    $select_query = "SELECT *, REPLACE(RIGHT(bus_date, 5), '-', '') as busjson FROM `AT_PROD_BUS_DAY` WHERE useYN = 'Y' ORDER BY bus_date, bus_gubun";
}else if($code == "point"){ //정류장 조회
    $select_query = "SELECT *, REPLACE(RIGHT(bus_date, 5), '-', '') as busjson FROM `AT_PROD_BUS_DAY` WHERE useYN = 'Y' ORDER BY bus_date, bus_gubun limit 1";
}

//SQL 실행 및 결과
$result_query = mysqli_query($conn, $select_query);

//오류 여부 체크
if($result_query){
    $count = mysqli_num_rows($result_query); //총 개수
    // $returnArray = mysqli_fetch_array($result_query); //최상위행 반환
    while ($row = mysqli_fetch_assoc($result_query)){
        $returnArray[] = $row;
    }
    
    $success = "true";
    $returnCode = "200";
    $message = "OK";
    
}else {
    
    $success = "false";
    $returnCode = "201";
    $message = mysqli_error($conn);

}


// mysqli_query($conn, "SET AUTOCOMMIT=0");
// mysqli_query($conn, "BEGIN");

// if(!$result_set) goto errGo;

// mysqli_query($conn, "ROLLBACK");

// mysqli_query($conn, "COMMIT");
?>