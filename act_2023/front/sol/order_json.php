<?php
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/func.php';

$type = $_REQUEST["type"];
$resDate = $_REQUEST["res_date"];
$resNumber = str_replace("-", "", $_REQUEST["resNumber"]);

if($type == "search"){
    $add_date = date("Y-m-d");
    
    $select_query = "SELECT a.resseq
                        FROM AT_SOL_RES_MAIN as a INNER JOIN AT_SOL_RES_SUB as b 
                                    ON a.resseq = b.resseq 
                                WHERE a.res_confirm = '확정' AND REPLACE(a.user_tel, '-', '') = '$resNumber'
                                AND ((b.sdate <= '$resDate' AND b.edate >= '$resDate')
                                        OR b.resdate = '$resDate')
                                        LIMIT 1";
    $result = mysqli_query($conn, $select_query);
    $rowMain = mysqli_fetch_array($result);
    $chkCnt = mysqli_num_rows($result); //체크 개수
    if($chkCnt > 0){
        echo urlencode(encrypt($rowMain["resseq"]));
    }else{
        echo "no";
    }
}else{

}

?>