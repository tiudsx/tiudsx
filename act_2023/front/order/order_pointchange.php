<? 
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/func.php';
?>

<?
$resNumber = str_replace(' ', '', $_REQUEST["resNumber"]);
$num = $_REQUEST["num"];

$now = date("Y-m-d");
$select_query = 'SELECT a.user_name, a.resnum, a.user_tel, b.*, a.resnum as res_num, TIMESTAMPDIFF(MINUTE, b.insdate, now()) as timeM, c.couponseq 
                    FROM `AT_RES_MAIN` a INNER JOIN `AT_RES_SUB` as b 
                        ON a.resnum = b.resnum 
                    LEFT JOIN AT_COUPON_CODE as c
                        ON b.res_coupon = c.coupon_code
                    WHERE a.resnum = "'.$resNumber.'" 
                        AND b.res_confirm IN (0,3,8)
                        AND b.res_date >= "'.$now.'"
                        ORDER BY a.resnum, b.ressubseq';
$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);

if($count == 0){
    echo "<script>alert('예약된 정보가 없거나 이용일이 지났습니다.\\n\\n관리자에게 문의해주세요.');location.href='/ordersearch';</script>";
	return;
}

$i = 0;
$busgubun0 = 0;
$busgubun1 = 0;
$arrResInfoS = array();
$arrResInfoE = array();
while ($row = mysqli_fetch_assoc($result_setlist)){
    if($i == 0){
        $user_name = $row["user_name"];
        $res_num = $row["resnum"];
        $user_tel = $row["user_tel"];
        $shopseq = $row["seq"];
        $couponseq = $row["couponseq"];
        $couponcode = $row["res_coupon"];

        $dayCode = "busseat";
        if($couponcode == "FRIPYY" || $couponcode == "FRIPDH"){
            $dayCode = "frip_busseat";            
        }
    }
    
    $gubun = substr($row["res_bus"], 0, 1);
    if($gubun == "Y" || $gubun == "E"){ //양양,동해행
        $busgubun0 = 1;
        $res_date0 = $row["res_date"];
        if($shopseq == 7){
            $res_busnum0 = $row["res_busnum"];

        }else{
            $res_busnum0 = fnBusCode($row["res_busnum"], $shopseq);
        }

        $arrResInfoS[$row["ressubseq"]] = array("ressubseq" => $row["ressubseq"]
                                                , "res_busnum" => $row["res_busnum"]
                                                , "res_seat" => $row["res_seat"]
                                                , "res_spointname" => $row["res_spointname"]
                                                , "res_epointname" => $row["res_epointname"]);

         $resseq .= "<input type='hidden' id='ressubseqs' name='ressubseqs[]' value='".$row["ressubseq"]."'>";
    }else{ //서울행
        $busgubun1 = 1;
        $res_date1 = $row["res_date"];
        if($shopseq == 7){
            $res_busnum1 = $row["res_busnum"];
        }else{
            $res_busnum1 = fnBusCode($row["res_busnum"], $shopseq);
        }

        $arrResInfoE[$row["ressubseq"]] = array("ressubseq" => $row["ressubseq"]
                                                , "res_busnum" => $row["res_busnum"]
                                                , "res_seat" => $row["res_seat"]
                                                , "res_spointname" => $row["res_spointname"]
                                                , "res_epointname" => $row["res_epointname"]);

         $resseq .= "<input type='hidden' id='ressubseqe' name='ressubseqe[]' value='".$row["ressubseq"]."'>";
    }

    $i++;    
}

if($shopseq == 7){ //양양 셔틀버스
    include __DIR__."/order_pointchange_yy.php";

}else{ //동해 셔틀버스
    echo "<script>alert('정류장 변경 요청은 상담톡으로 연락주세요~');location.href = '/';</script>";
    //include __DIR__."/order_pointchange_dh.php";
}