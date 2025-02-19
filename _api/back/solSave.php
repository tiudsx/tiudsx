<?php
$success = true;

mysqli_query($conn, "SET AUTOCOMMIT=0");
mysqli_query($conn, "BEGIN");


$stayroom = 1;
$staynum = 2;
goto errGoRoom;

if($code == "calendar"){ //달력 조회
    $Year = substr($selDate,0,4);
    $Mon = substr($selDate,4,2);
    $selDate = ($reqDate == "") ? str_replace("-", "", date("Y-m-d")) : $reqDate;

    $select_query = "SELECT COUNT(*) AS Cnt, DAY(b.sdate) AS sDay, DAY(b.edate) AS eDay, DAY(b.resdate) AS resDay, 
        MONTH(b.sdate) AS sMonth, MONTH(b.edate) AS eMonth, MONTH(b.resdate) AS resMonth, b.res_type, 
        b.sdate, b.edate, b.resdate, a.res_confirm,
        DATEDIFF(b.sdate, b.edate) as sDateDiff, DATEDIFF(b.edate, b.sdate) as eDateDiff
	FROM AT_SOL_RES_MAIN as a INNER JOIN AT_SOL_RES_SUB as b 
	ON a.resseq = b.resseq 
		WHERE ((Year(b.sdate) = '$Year' AND Month(b.sdate) = '$Mon') OR (Year(b.edate) = '$Year' AND Month(b.edate) = '$Mon'))
			OR	(Year(b.resdate) = '$Year' AND Month(b.resdate) = '$Mon')
		GROUP BY b.res_type, b.sdate, b.edate, b.resdate, a.res_confirm";

    //SQL 실행 및 결과
    $result_set = mysqli_query($conn, $select_query);

    if(!$result_set) goto errGo;
}else if($code == "solstay"){ //상세정보
    $select_query = "SELECT * FROM AT_SOL_RES_MAIN as a INNER JOIN AT_SOL_RES_SUB as b 
                    ON a.resseq = b.resseq 
                    WHERE a.resseq = $resseq
                        ORDER BY b.ressubseq";
    //SQL 실행 및 결과
    $result_set = mysqli_query($conn, $select_query);
    
    if(!$result_set) goto errGo;
}

mysqli_query($conn, "COMMIT");

if(!$success){
	errGo:
	mysqli_query($conn, "ROLLBACK");
    extract(createResponse("false", "오류가 발생하였습니다.", [], [
        "conn" => $conn,
        "errMsg" => $select_query
    ]));
}else if(!$success){
	errGoRoom:
	mysqli_query($conn, "ROLLBACK");
    extract(createResponse("false", "오류가 발생하였습니다.", [], [
        "conn" => $conn,
        "errMsg" => array("stayroom" => $stayroom, "staynum" => $staynum)
    ]));
}else{
    extract(createResponse("true", "처리가 완료되었습니다.", $returnArray));
}


// mysqli_query($conn, "SET AUTOCOMMIT=0");
// mysqli_query($conn, "BEGIN");

// if(!$result_set) goto errGo;

// mysqli_query($conn, "ROLLBACK");

// mysqli_query($conn, "COMMIT");
?>