<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/_api/utils/response.php';

$reqDate = $_REQUEST["selDate"]; // 날짜

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
}else if($code == "listDay"){ // 일별 조회
    $gubun = $_REQUEST["gubun"];
    if($reqDate == ""){
        $selDate = date("Y-m-d");
    }else{
        $selDate = $reqDate;
    }
    $arrDate = explode('-', $selDate);

    $Year = $arrDate[0];
    $Mon = $arrDate[1];
    $Day = $arrDate[2];

    $diffDate = date("Y-m-d", strtotime(date("Y-m-d")." -3 day"));

    if($gubun == "cancel"){
        $confirmText = "'취소', '환불'";
        $tabColor1 = "";
        $tabColor2 = "gg_btn_color";
    }else{
        $confirmText = "'대기', '확정'";
        $tabColor1 = "gg_btn_color";
        $tabColor2 = "";
    }

    $select_query = "SELECT 
        a.resseq, a.resnum, a.admin_user, a.res_confirm, a.res_kakao, a.res_kakao_chk, a.res_room_chk, a.res_company, a.user_name, a.user_tel, a.memo, a.memo2, a.history, a.insdate, 
        b.ressubseq, b.res_type, b.prod_name, b.sdate, b.edate, '' as resdate, b.staysex, b.stayM, b.stayroom, b.staynum, b.restime, b.surfM, b.surfW, b.surfrent, b.surfrentM, b.surfrentW, b.surfrentYN,
        DAY(b.sdate) AS sDay, DAY(b.edate) AS eDay, DAY(b.resdate) AS resDay, MONTH(b.sdate) AS sMonth, MONTH(b.edate) AS eMonth, MONTH(b.resdate) AS resMonth, DATEDIFF(b.edate, b.sdate) as eDateDiff, a.userinfo, a.res_bankchk, c.response, c.KAKAO_DATE, 
            CASE WHEN staysex = '남' AND (party IN ('ALL', 'BBQ')) AND resdate = '$selDate' THEN 1
                ELSE 0 END AS BBQ_M,
            CASE WHEN staysex = '여' AND (party IN ('ALL', 'BBQ')) AND resdate = '$selDate' THEN 1
                ELSE 0 END AS BBQ_W,
            CASE WHEN staysex = '남' AND (party IN ('ALL', 'PUB')) AND resdate = '$selDate' THEN 1
                ELSE 0 END AS PUB_M,
            CASE WHEN staysex = '여' AND (party IN ('ALL', 'PUB')) AND resdate = '$selDate' THEN 1
                ELSE 0 END AS PUB_W
            FROM AT_SOL_RES_MAIN as a INNER JOIN AT_SOL_RES_SUB as b 
                    ON a.resseq = b.resseq 
                LEFT JOIN AT_KAKAO_HISTORY as c
                    ON a.userinfo = c.msgid
                WHERE ((b.sdate <= '$selDate' AND DATE_ADD(b.edate, INTERVAL -1 DAY) >= '$selDate')
                        OR b.resdate = '$selDate')
                    AND res_type = 'stay'
                    AND a.res_confirm IN ($confirmText)
        UNION ALL
    SELECT 
        a.resseq, a.resnum, a.admin_user, a.res_confirm, a.res_kakao, a.res_kakao_chk, a.res_room_chk, a.res_company, a.user_name, a.user_tel, a.memo, a.memo2, a.history, a.insdate, 
        b.ressubseq, b.res_type, 
        CASE WHEN b.res_type = 'stay' THEN 'N' ELSE b.prod_name END as prod_name, 
        '' as sdate, '' as edate, b.resdate, b.staysex, b.stayM, null as stayroom, null as staynum, b.restime, b.surfM, b.surfW, b.surfrent, b.surfrentM, b.surfrentW, b.surfrentYN,
        DAY(b.sdate) AS sDay, DAY(b.edate) AS eDay, DAY(b.resdate) AS resDay, MONTH(b.sdate) AS sMonth, MONTH(b.edate) AS eMonth, MONTH(b.resdate) AS resMonth, DATEDIFF(b.edate, b.sdate) as eDateDiff, a.userinfo, a.res_bankchk, c.response, c.KAKAO_DATE,
        0 AS BBQ_M, 0 AS BBQ_W, 0 AS PUB_M, 0 AS PUB_W 
            FROM AT_SOL_RES_MAIN as a INNER JOIN AT_SOL_RES_SUB as b 
                    ON a.resseq = b.resseq 
                LEFT JOIN AT_KAKAO_HISTORY as c
                    ON a.userinfo = c.msgid
                WHERE b.resdate = '$selDate'                    
                    AND a.res_confirm IN ($confirmText)
                    AND res_type = 'surf'
        ORDER BY resseq, ressubseq";
}else if($code == "solview"){ //상세정보
    $resseq = $_REQUEST["resseq"];
    $select_query = "SELECT * FROM AT_SOL_RES_MAIN as a INNER JOIN AT_SOL_RES_SUB as b 
                        ON a.resseq = b.resseq 
                        WHERE a.resseq = $resseq
                            ORDER BY  b.ressubseq";
}else if($code == "solroom"){ //객실 침대체크
    $sdate = $_REQUEST["res_staysdate"];
    $edate = $_REQUEST["res_stayedate"];
    $stayroom = $_REQUEST["res_stayroom"];
    
    $eDate2 = date("Y-m-d", strtotime($edate." -1 day"));
    $select_query = "SELECT b.stayroom, b.staynum, a.resseq, b.ressubseq FROM AT_SOL_RES_MAIN as a INNER JOIN AT_SOL_RES_SUB as b
                        ON a.resseq = b.resseq
                        WHERE b.res_type = 'stay' 
                            AND b.prod_name = '솔게스트하우스'
                            AND b.stayroom = $stayroom
                            AND a.res_confirm IN ('대기','확정')
                            AND (('$sdate' BETWEEN b.sdate AND DATE_ADD(b.edate, INTERVAL -1 DAY) OR '$eDate2' BETWEEN b.sdate AND DATE_ADD(b.edate, INTERVAL -1 DAY))
                                OR (b.sdate BETWEEN '$sdate' AND '$eDate2' OR DATE_ADD(b.edate, INTERVAL -1 DAY) BETWEEN '$sdate' AND '$eDate2'))
                                ORDER BY a.resseq";
}else if($code == "solstay"){ //상세정보
    $select_query = "SELECT * FROM AT_SOL_RES_MAIN as a INNER JOIN AT_SOL_RES_SUB as b 
                    ON a.resseq = b.resseq 
                    WHERE a.resseq = $resseq
                        ORDER BY b.ressubseq";
}else if($code == "surfview"){ //상세정보
    $select_query = "SELECT * FROM AT_RES_MAIN as a INNER JOIN AT_RES_SUB as b 
                            ON a.resnum = b.resnum
                        INNER JOIN `AT_PROD_OPT` c
                            ON b.optseq = c.optseq
                        WHERE a.resnum = $resseq
                            ORDER BY  b.ressubseq";
}

//SQL 실행 및 결과
$result_set = mysqli_query($conn, $select_query);

//오류 여부 체크
if($result_set){
    $returnArray = [];
    while ($row = mysqli_fetch_assoc($result_set)){
        $returnArray[] = $row;
    }
    
    // 성공 응답 생성
    extract(createResponse("true", "조회가 완료되었습니다.", $returnArray));
} else {
    // 실패 응답 생성
    extract(createResponse("false", "오류가 발생하였습니다.", [], ["conn" => $conn]));
}

// mysqli_query($conn, "SET AUTOCOMMIT=0");
// mysqli_query($conn, "BEGIN");

// if(!$result_set) goto errGo;

// mysqli_query($conn, "ROLLBACK");

// mysqli_query($conn, "COMMIT");
?>