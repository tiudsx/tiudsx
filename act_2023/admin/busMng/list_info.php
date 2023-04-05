<?php
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/func.php';

$param = $_REQUEST["resparam"];
$ressubseq = $_REQUEST["ressubseq"];

header('Content-Type: application/json');
$arrType = false;
if($param == "busmnglist"){ //상세정보
    $arrType = true;
    $selDate = $_REQUEST["selDate"];

    $select_query = "SELECT * FROM AT_PROD_BUS as a 
                        WHERE a.busdate = '$selDate'
                            ORDER BY a.busseq";
}
$result = mysqli_query($conn, $select_query);
$count_sub = mysqli_num_rows($result);

if($count_sub == 0){
    $dbdata = $count_sub;
}else{
    if($arrType){
        $dbdata = array();
        $i = 0;
        while ( $row = $result->fetch_assoc()){
            $dbdata[$i] = $row;
            $i++;
        }
    }else{
        $dbdata;
        while ( $row = $result->fetch_assoc()){
            $dbdata = $row;
        }
    }
}

$output = json_encode($dbdata, JSON_UNESCAPED_UNICODE);
echo urldecode($output);
?>