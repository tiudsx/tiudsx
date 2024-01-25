<?
include __DIR__.'/../../common/db.php';

$reqDate = $_REQUEST["selDate"];
if($reqDate == ""){
    $selDate = date("Y-m-d");
}else{
    $selDate = $reqDate;
}

$select_query = "SELECT user_name, user_tel, 
                    SUM(CASE WHEN staysex = '남' AND (party IN ('ALL', 'BBQ')) THEN 1
                    ELSE 0 END) AS BBQ_M,
                    SUM(CASE WHEN staysex = '여' AND (party IN ('ALL', 'BBQ')) THEN 1
                    ELSE 0 END) AS BBQ_W,
                    SUM(CASE WHEN staysex = '남' AND (party IN ('ALL', 'PUB')) THEN 1
                    ELSE 0 END) AS PUB_M,
                    SUM(CASE WHEN staysex = '여' AND (party IN ('ALL', 'PUB')) THEN 1
                    ELSE 0 END) AS PUB_W
                    FROM AT_SOL_RES_MAIN as a INNER JOIN AT_SOL_RES_SUB as b 
                                    ON a.resseq = b.resseq 
                                    WHERE b.resdate = '$selDate'
                                        AND b.res_type = 'stay'
                                        AND b.party != 'N'
                                        AND a.res_confirm = '확정'
                    GROUP BY user_name, user_tel
                    ORDER BY user_name";
//echo $select_query;
$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);
?>

<div class="contentimg bd">
<form name="frmConfirm" id="frmConfirm" autocomplete="off">
    <div class="gg_first">예약 현황 (<span id="listdate"><?=$selDate?></span>)
        <input type="button" name="listtab" class="gg_btn gg_btn_grid large" style="width:80px; height:20px;" value="전체" onclick="fnListTab('all', this);" />
        <input type="button" name="listtab" class="gg_btn gg_btn_grid large gg_btn_color" style="width:80px; height:20px;" value="파티인원" onclick="fnListTab('stay', this);" />
        <input type="button" name="listtab" class="gg_btn gg_btn_grid large" style="width:80px; height:20px;" value="강습&렌탈" onclick="fnListTab('surf', this);" />
        <input type="button" name="listtab" class="gg_btn gg_btn_grid large" style="width:80px; height:20px;" value="취소건" onclick="fnListTab('cancel', this);" />
    </div>
<?
$c = 0;
$partyinfo = "";
while ($row = mysqli_fetch_assoc($result_setlist)){
    $c++;
	$now = date("Y-m-d");

	$user_name = $row['user_name'];
    $user_tel = $row['user_tel'];
    $BBQ_M = ($row['BBQ_M'] == 0) ? "" : $row['BBQ_M'].' 명';
    $BBQ_W = ($row['BBQ_W'] == 0) ? "" : $row['BBQ_W'].' 명';
    $PUB_M = ($row['PUB_M'] == 0) ? "" : $row['PUB_M'].' 명';
    $PUB_W = ($row['PUB_W'] == 0) ? "" : $row['PUB_W'].' 명';

    $staylist_text = "
                <tr>
                    <td>$user_name</td>
                    <td>$user_tel</td>
                    <td>$BBQ_M</td>
                    <td>$BBQ_W</td>
                    <td></td>
                    <td>$PUB_M</td>
                    <td>$PUB_W</td>
                    <td></td>
                </tr>
            ";

    $partyinfo .= $staylist_text;

    $partyBBQ_M += $BBQ_M;
    $partyBBQ_W += $BBQ_W;
    $partyPUB_M += $PUB_M;
    $partyPUB_W += $PUB_W;
//while end
}
?>

<table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:65%;">
    <colgroup>
        <col width="80px" />
        <col width="100px" />
        <col width="90px" />
        <col width="90px" />
        <col width="auto" />
        <col width="90px" />
        <col width="90px" />
        <col width="auto" />
    </colgroup>
    <tbody>
        <tr>
            <th rowspan="2">이름</th>
            <th rowspan="2">연락처</th>
            <th colspan="3">1차 바베큐</th>
            <th colspan="3">2차 파티</th>
        </tr>
        <tr>
            <th>남</th>
            <th>여</th>
            <th>비고</th>
            <th>남</th>
            <th>여</th>
            <th>비고</th>
        </tr>
        <?=$partyinfo?>
        <tr>
            <td colspan="8" style="height:1px;padding:0px;background-color:#efefef"></td>
        </tr>
        <tr>
            <td colspan="2">인원 합계</td>
            <td><?=$partyBBQ_M?> 명</td>
            <td><?=$partyBBQ_W?> 명</td>
            <td>&nbsp;</td>
            <td><?=$partyPUB_M?> 명</td>
            <td><?=$partyPUB_W?> 명</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <th colspan="2">총 인원</th>
            <th colspan="3"><?=$partyBBQ_M+$partyBBQ_W?> 명</th>
            <th colspan="3"><?=$partyPUB_M+$partyPUB_W?> 명</th>
        </tr>
    </tbody>
</table>