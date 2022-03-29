<?

include __DIR__.'/../db.php';
include __DIR__.'/../common/logininfo.php';

session_start();
$arrShop = explode(",", $surftype);
$Shopcnt = count($arrShop);

$seq = $_REQUEST["seq"];

if($Shopcnt == 0){
    echo '<script>alert("관리자 권한이 없습니다.");location.href="/";</script>';
    exit;
}else{
    if($seq == ""){
        $seq = $arrShop[0];
    }

    if(strpos($surftype, $seq) !== false) {  
        //샵seq 포함
    } else {  
        echo '<script>alert("관리자 권한이 없습니다.");location.href="/";</script>';
        exit;
    }

    $select_query = "SELECT * FROM `AT_PROD_MAIN` WHERE seq = $seq ORDER BY categoryname, code, shopname";
    $result_setlist = mysqli_query($conn, $select_query);
    $countAdmin = mysqli_num_rows($result_setlist);
    $rowAdmin = mysqli_fetch_array($result_setlist);

    if($countAdmin == 0){
        echo '<script>alert("관리자 권한이 없습니다.");location.href="/";</script>';
        exit;
    }

    $_SESSION['userid'] = $user_id;
	$_SESSION['shopseq'] = $rowAdmin["seq"];
    $_SESSION['shopname'] = $rowAdmin["shopname"];
    
    Header("Location:/surfshopadmin");
}

?>