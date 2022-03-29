<?php 
include __DIR__.'/../db.php';
include __DIR__.'/../surf/surffunc.php';


$resNumber = str_replace(' ', '', $_REQUEST["resNumber"]);

$gubun = substr($resNumber, 0, 1);

$select_query = 'SELECT *, a.resnum as res_num, TIMESTAMPDIFF(MINUTE, b.insdate, now()) as timeM FROM `AT_RES_MAIN` a LEFT JOIN `AT_RES_SUB` as b 
ON a.resnum = b.resnum 
where a.resnum = "'.$resNumber.'" AND b.res_confirm IN (0,3,8)
ORDER BY a.resnum, b.ressubseq';

$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);
?>

<script src="../js/surfordersearch.js"></script>
<script src="../js/surfview_busday.js?v=1"></script>

<div id="wrap">
    <? include  __DIR__.'/../_layout_top.php'; ?>

    <link rel="stylesheet" href="../css/surfview.css">

    <div class="top_area_zone">
        <section class="shoptitle">
            <div style="padding:6px;">
                <h1>액트립 서핑버스 정류장 변경</h1>
            </div>
        </section>
        <section class="notice">
            <div class="bd" style="padding:0 4px;min-height:300px;" id="surfSelOk">
            <form name="frmPoint" id="frmPoint" target="ifrmResize" autocomplete="off">
                <?
                $i = 1;
                while ($row = mysqli_fetch_assoc($result_setlist)){
                    $now = date("Y-m-d");

                    $surfSeatInfo = "";
                    $arrSeatInfo = array();

                    $shopseq = $row['seq'];
                    $bankUserName = $row['user_name'];
                    $res_confirm = $row['res_confirm'];

                    $chkView = 0;
                    $datDate = $row['res_date'];
                    if($datDate >= $now){
                        if($res_confirm == 0 || $res_confirm == 2 || $res_confirm == 6){
                            $chkView = 1;
                        } else if($res_confirm == 3){
                            $cancelDate = date("Y-m-d", strtotime($datDate." -1 day"));
                            if($row['timeM'] <= 120 || $cancelDate > $now){
                                $chkView = 1;
                            }
                        }
                    }
                /*
                예약상태
                    0 : 미입금
                    1 : 예약대기
                    2 : 임시확정
                    3 : 확정
                    4 : 환불요청
                    5 : 환불완료
                    6 : 임시취소
                    7 : 취소
                    8 : 입금완료
                */
                    $ResColor = "";
                    $ResCss = "";
                    $ChangeChk = 1;

                    if($res_confirm == 0){
                        $ResConfirm = "미입금";
                    }else if($res_confirm == 1){
                        $ResConfirm = "확인중";
                        $ResColor = "rescolor2";
                    }else if($res_confirm == 2 || $res_confirm == 6 || $res_confirm == 8){
                        $ResConfirm = "입금완료";
                        $ResColor = "rescolor2";
                    }else if($res_confirm == 3){
                        $ResConfirm = "확정";
                    }else if($res_confirm == 4){
                        $ResConfirm = "환불요청";
                        $ResColor = "rescolor1";  
                        $ChangeChk = 0;                      
                    }else if($res_confirm == 5){
                        $ResConfirm = "환불완료";
                        $ResCss = "rescss";
                        $ChangeChk = 0;
                    }else if($res_confirm == 7){
                        $ResConfirm = "취소";
                        $ResCss = "rescss";
                        $ChangeChk = 0;
                    }

                    if ($datDate < date("Y-m-d", strtotime($now." 0 day")))
                    {
                        $ResCss = "resper";
                        $ChangeChk = 0;
                    }

                    $RtnBank = str_replace("|", " / ", fnBusPoint($row['res_spointname'], $row['res_bus']));
                    
                    $busNum = $row['res_bus'];
                    $busType = substr($row['res_bus'], 0, 1);

                    //============= 예약항목 구역 =============
                    if($i == 1){
                ?>
                    <table class="et_vars exForm bd_tb">
                        <colgroup>
                            <col width="20%" />
                            <col width="auto;" />
                        </colgroup>
                        <tbody>
                            <tr>
                                <th>예약번호</th>
                                <td><strong><?=$row['res_num']?></strong><span style="display:none;"> (<?=$row['insdate']?>)</span></td>
                            </tr>
                            <tr>
                                <th>예약자</th>
                                <td><?=$row['user_name']?><span style="display:;">  (<?=$row['user_tel']?>)</span></td>
                            </tr>
                            <tr>
                                <th colspan="2">[<?=$row['shopname']?>] 예약정보</th>
                            </tr>
                            <tr>
                                <td colspan="2">
                    <table class="et_vars exForm bd_tb" style="width:100%">
                        <tbody>
                            <colgroup>
                                <col style="width:85px;">
                                <col style="width:auto;">
                            </colgroup>
                            <tr>
                                <th style="text-align:center;">이용일</th>
                                <th style="text-align:center;">예약항목</th>
                            </tr>
                    
                <?	}?>
                            <tr class="<?=$ResCss?>">
                                <td style="text-align:center;" rowspan="2">
                                    <?=$row['res_date']?>
                                </td>
                                <td><b><?=fnBusNum($row['res_bus'])?> : <?=$row['res_seat']?>번</b><br>
                                <?if($ChangeChk == 1){?>
                                    <input type="hidden" id="ressubseq" name="ressubseq[]" value="<?=$row['ressubseq']?>">
                                    <select id="startLocation<?=$busType.$i?>" name="startLocation[]" class="select" onchange="fnBusTime(this, '<?=$busNum?>', <?=($i-1)?>);">
                                    </select>
                                     → 
                                     <select id="endLocation<?=$busType.$i?>" name="endLocation[]" class="select">
                                    </select>
<script>
var sPoint = "";
var ePoint = "";
var arrObjs = eval("busPoint.sPoint<?=$busNum?>");
var arrObje = eval("busPoint.ePoint<?=$busType?>");

arrObjs.forEach(function(el){
    if(el.code != "N"){
        var sel = "";
        if(el.code == "<?=$row['res_spointname']?>"){ sel = " selected='selected'" }
        sPoint += "<option value='" + el.code + "'" + sel + ">" + el.codename + "</option>";
    }
});

arrObje.forEach(function (el) {
    if(el.code != "N"){
        var sel = "";
        if(el.code == "<?=$row['res_epointname']?>"){ sel = " selected='selected'" }
        ePoint += "<option value='" + el.code + "'" + sel + ">" + el.codename + "</option>";
    }
});

$j("#startLocation<?=$busType.$i?>").html(sPoint);
$j("#endLocation<?=$busType.$i?>").html(ePoint);
</script>
                                <?}else{?>
                                <span style="padding-left:10px;"><?=$row['res_spointname']?> -> <?=$row['res_epointname']?></span>
                                <?}?>
                                </td>
                            </tr>
                            <tr class="<?=$ResCss?>">
                                <td id="stopLocation"><?=$RtnBank?></td>
                            </tr>
                <?
                    if($i == $count){?>
                        </tbody>
                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                <?
                    }
                    $i++;
                }
                ?>

                <div class="write_table" style="padding-top:15px; padding-bottom:15px;text-align:center;display:;">
                    <input type="button" class="gg_btn gg_btn_grid large" style="width:140px; height:40px;color: #fff !important; background: #008000;" value="변경 신청하기" onclick="fnPointChangeSave();" />&nbsp;
                    <input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:140px; height:40px;" value="돌아가기" onclick="history.back();" />
                </div>
                <input type="hidden" id="resparam" name="resparam" value="PointChange">
                <input type="hidden" id="shopseq" name="shopseq" value="<?=$shopseq?>">
                <input type="hidden" id="MainNumber" name="MainNumber" value="<?=$resNumber?>">
            </form>
            </div>
           
            <iframe id="ifrmResize" name="ifrmResize" style="width:800px;height:400px;display:none;"></iframe>

        </section>
    </div>
</div>

<? include __DIR__.'/../_layout_bottom.php'; ?>