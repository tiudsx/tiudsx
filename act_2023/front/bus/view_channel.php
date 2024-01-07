<?
include __DIR__.'/../../common/func.php';

$arrChannel = $_REQUEST["param"];
if($arrChannel == ""){    
    echo "<script>alert('예약된 정보가 없습니다.');location.href='https://actrip.co.kr';</script>";
    return;
}else{
    $arrChk = explode("|", decrypt($arrChannel));
    $busgubun = trim($arrChk[0]);  //버스구분 : 양양/동해
    
    if($busgubun == "YY"){ //양양
        $url = "/surfbus_yy?param=";
    }else{ //동해
        $url = "/surfbus_dh_2023?param=";
    }
    
    echo "<script>location.href='".$url."'+encodeURIComponent('$arrChannel');</script>";
}
?>