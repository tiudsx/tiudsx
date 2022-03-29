<?php
include __DIR__.'/../db.php';
include __DIR__.'/../surf/surffunc.php';

$coupon = $_REQUEST["coupon"];
$type = $_REQUEST["type"];
$gubun = $_REQUEST["gubun"];
if($gubun == "load"){
    mysqli_query($conn, "SET AUTOCOMMIT=0");
    mysqli_query($conn, "BEGIN");
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $add_date = date("Y-m-d");

    $select_query = "UPDATE AT_COUPON_CODE 
                        SET use_yn = 'Y'
                        ,user_ip = '$user_ip'
                        ,use_date = now()
                    WHERE add_date < '$add_date' AND use_yn = 'N' AND issue_type == 'A';";
    $result_set = mysqli_query($conn, $select_query);
    mysqli_query($conn, "COMMIT");
    
    $select_query = "SELECT a.*, b.dis_price, b.dis_type, b.sdate, b.edate, b.issue_type FROM AT_COUPON_CODE a INNER JOIN AT_COUPON b ON a.couponseq = b.couponseq WHERE a.coupon_code = '$coupon' AND a.seq = '$type'";
    $result = mysqli_query($conn, $select_query);
    $rowMain = mysqli_fetch_array($result);
    $chkCnt = mysqli_num_rows($result); //체크 개수
    if($chkCnt > 0){
        if($rowMain["use_yn"] == "N"){
            echo $rowMain["dis_price"];
        }else{
            echo "yes";
        }
    }else{
        echo "no";
    }
}else{

}

?>