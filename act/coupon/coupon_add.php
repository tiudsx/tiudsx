<?php
include __DIR__.'/../db.php';
include __DIR__.'/../surf/surffunc.php';

function RandString($len){
    $return_str = "";

    for ( $i = 0; $i < $len; $i++ ) {
        mt_srand((double)microtime()*1000000);
        $return_str .= substr('123456789ABCDEFGHIJKLMNPQRSTUVWXYZ', mt_rand(0,33), 1);
    }

    return $return_str;
}

$coupon = $_REQUEST["coupon"];
$arrdate = explode("|", decrypt($coupon));

 //echo '<br>'.count($arrdate).'<br>';
// echo '<br>'.decrypt($coupon);
if(!($arrdate[2] == "BUS" || $arrdate[2] == "BBQ" || $arrdate[2] == "SUR")){
    echo "<script>alert('쿠폰다운로드 링크가 정상적이지 않습니다.\\n\\n관리자에게 문의하세요.');</script>";
    return;
}

//if($arrdate[0] >= date("Y-m-d")){
if(1 == 1){
    mysqli_query($conn, "SET AUTOCOMMIT=0");
    mysqli_query($conn, "BEGIN");

    $success = true;

    $coupon_code = RandString(5);
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $add_date = date("Y-m-d");

    $select_query = "UPDATE AT_COUPON_CODE 
                        SET use_yn = 'Y'
                        ,user_ip = '$user_ip'
                        ,use_date = now()
                    WHERE add_date < '$add_date' AND use_yn = 'N' AND issue_type == 'A';";
    $result_set = mysqli_query($conn, $select_query);
    mysqli_query($conn, "COMMIT");
    
    $select_query = "SELECT * FROM AT_COUPON_CODE where couponseq = $arrdate[1] AND seq = '$arrdate[2]' AND use_yn = 'N' AND add_ip = '$user_ip'";
    //echo '<br>'.$select_query.'<br>';
    $result = mysqli_query($conn, $select_query);
    $rowMain = mysqli_fetch_array($result);
    $chkCnt = mysqli_num_rows($result); //체크 개수
    if($chkCnt == 0){
        $select_query = "INSERT INTO AT_COUPON_CODE(`couponseq`, `coupon_code`, `seq`, `add_ip`, `add_date`, `insdate`) VALUES ($arrdate[1], '$coupon_code', '$arrdate[2]', '$user_ip', '$add_date', now())";
        //echo '<br>'.$select_query.'<br>';
        $result_set = mysqli_query($conn, $select_query);
        if(!$result_set) $success = false;

        if(!$success){
            mysqli_query($conn, "ROLLBACK");
            echo "<script>alert('쿠폰 생성중 오류가 발생하였습니다.\\n\\다시 시도하거나 관리자에게 문의하세요.');window.close();</script>";
            return;
        }else{
            mysqli_query($conn, "COMMIT");            
        }
    }else{
        //echo "이미 발급받은 쿠폰코드가 있습니다.<br>기존 쿠폰코드 : ".$rowMain["coupon_code"];
        //return;
        $coupon_code = $rowMain["coupon_code"];
    }
}else{
    echo "<script>alert('쿠폰다운로드 기간이 지났습니다.\\n\\관리자에게 문의하세요.');window.close();</script>";
    return;
}
?>

<script src="/act/js/popup.js"></script>

<script>
    gpe_setCookie1("act_pop2", "<?=$coupon_code?>|<?=$arrdate[2]?>", 1);
    location.href="/main"
</script>