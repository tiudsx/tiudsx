<?php
include __DIR__.'/../../db.php';
include __DIR__.'/../../surf/surffunc.php';

$param = $_REQUEST["resparam"];
$resseq = $_REQUEST["resseq"];

header('Content-Type: application/json');

if($param == "solview"){ //상세정보
    $select_query = "SELECT * FROM AT_SOL_RES_MAIN as a INNER JOIN AT_SOL_RES_SUB as b 
                        ON a.resseq = b.resseq 
                        WHERE a.resseq = $resseq
                            ORDER BY  b.ressubseq";
    $result = mysqli_query($conn, $select_query);
    $count_sub = mysqli_num_rows($result);

    if($count_sub == 0){
        echo "err";
    }else{
        $dbdata = array();
        $i = 0;
        while ( $row = $result->fetch_assoc()){
            $dbdata[$i] = $row;
            $i++;
        }

        $output = json_encode($dbdata, JSON_UNESCAPED_UNICODE);
        echo urldecode($output);
    }
}else if($param == "surfview"){ //상세정보
    $select_query = "SELECT * FROM AT_RES_MAIN as a INNER JOIN AT_RES_SUB as b 
                            ON a.resnum = b.resnum
                        INNER JOIN `AT_PROD_OPT` c
                            ON b.optseq = c.optseq
                        WHERE a.resnum = $resseq
                            ORDER BY  b.ressubseq";
    $result = mysqli_query($conn, $select_query);
    $count_sub = mysqli_num_rows($result);

    if($count_sub == 0){
        echo "err";
    }else{
        $dbdata = array();
        $i = 0;
        while ( $row = $result->fetch_assoc()){
            $dbdata[$i] = $row;
            $i++;
        }

        $output = json_encode($dbdata, JSON_UNESCAPED_UNICODE);
        echo urldecode($output);
    }
}else if($param == "solstay"){ //상세정보
    $select_query = "SELECT * FROM AT_SOL_RES_MAIN as a INNER JOIN AT_SOL_RES_SUB as b 
                    ON a.resseq = b.resseq 
                    WHERE a.resseq = $resseq
                        ORDER BY b.ressubseq";
    $result = mysqli_query($conn, $select_query);
    $count_sub = mysqli_num_rows($result);
  
    $arrStay = array();
    $i = 0;
    $daychk = 1;
    while ($row = mysqli_fetch_assoc($result)){
        $res_type = $row["res_type"];
        $prod_name = $row["prod_name"];

        if($res_type == "stay"){ //숙박&바베큐
            if($prod_name != "N"){ //숙소 신청
            if($row['stayroom'] == "201"){
                    $pw = "4437";
                }else if($row['stayroom'] == "202"){
                    $pw = "0009";
                }else if($row['stayroom'] == "203"){
                    $pw = "3308";
                }else if($row['stayroom'] == "204"){
                    $pw = "5080";
                }else if($row['stayroom'] == "301"){
                    $pw = "4437";
                }else if($row['stayroom'] == "302"){
                    $pw = "0009";
                }else if($row['stayroom'] == "303"){
                    $pw = "3308";
                }

                $arrStay[$i] = $row['staysex']."|".$row['sdate']."|".$row['edate']."|".$row['stayroom']."|".$row['staynum']."|$pw";
                $i++;

                if($daychk > 0){
                    $sdate = $row['sdate'];
                    $nowdate = date("Y-m-d");
                
                    $r = strtotime($sdate) - strtotime($nowdate);
                    $r = ceil($r/(60*60*24));
                    if($r == 0){ //이용달일
                        $nowtime = date("H");
                        if($nowtime < 14){
                            $daychk = 1;
                        }else{
                            $daychk = 0;
                        }
                    }else if($r < 0){ //이용일이 현재일 이전
                        $daychk = 0;
                    }else{ //이용일이 현재일 이후 (입실 금지)
                        $daychk = 2;
                    }
                }
            }
        }
    }    

    if($daychk > 0){
        echo $daychk;
    }else{
        if($count_sub == 0){
            echo "err";
        }else{
            mysqli_query($conn, "SET AUTOCOMMIT=0");
            mysqli_query($conn, "BEGIN");
            $select_query = "UPDATE `AT_SOL_RES_MAIN` SET res_room_chk = 'Y' WHERE resseq = $resseq";
            $result_set = mysqli_query($conn, $select_query);
            mysqli_query($conn, "COMMIT");

            $output = json_encode($arrStay, JSON_UNESCAPED_UNICODE);
            echo urldecode($output);
        }
    }
}else if($param == "solroom"){ //객실 침대체크
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
    $result = mysqli_query($conn, $select_query);
    $count_sub = mysqli_num_rows($result);
    
    if($count_sub == 0){
        echo "";
    }else{
        $dbdata = array();
        $i = 0;
        while ( $row = $result->fetch_assoc()){
            $dbdata[$i] = $row;
            $i++;
        }

        $output = json_encode($dbdata, JSON_UNESCAPED_UNICODE);
        echo urldecode($output);
    }
}
?>