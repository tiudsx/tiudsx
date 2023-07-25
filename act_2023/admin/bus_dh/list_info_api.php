<?php
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/func.php';

$param = $_REQUEST["resparam"];
$ressubseq = $_REQUEST["ressubseq"];

$http_origin = $_SERVER['HTTP_ORIGIN']; 
if ($http_origin == "http://www.landingko.com" || $http_origin == "http://www.domain2.com" || $http_origin == "http://www.domain3.info") { 
    header("Access-Control-Allow-Origin: $http_origin"); 
}

header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Accept, Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization");
header("Content-type:text/html;charset=utf-8");

header('Content-Type: application/json');
$arrType = false;
if($param == "busview"){ //상세정보
    $arrType = true;
    $resseq = $_REQUEST["resseq"];

    $select_query = "SELECT a.*, b.*, d.couponseq FROM AT_RES_MAIN as a 
                        INNER JOIN AT_RES_SUB as b 
                            ON a.resnum  = b.resnum  
                        LEFT JOIN AT_COUPON_CODE d 
                            ON b.res_coupon = d.coupon_code
                        WHERE a.resseq = $resseq
                            ORDER BY b.ressubseq";
}

$result = mysqli_query($conn, $select_query);
$count_sub = mysqli_num_rows($result);

if($count_sub == 0){
    echo "err";
}else{
    if($arrType){
        $dbdata = array();
        $i = 0;
        while ( $row = $result->fetch_assoc()){
            $row["res_couponname"] = coupontype("admin", $row["couponseq"], $row["res_coupon"]);
            $dbdata[$i] = $row;
            $i++;
        }
    }else{
        $dbdata;
        while ( $row = $result->fetch_assoc()){
            $dbdata = $row;
        }
    }

    $output = json_encode($dbdata, JSON_UNESCAPED_UNICODE);
    echo urldecode($output);
}
?>