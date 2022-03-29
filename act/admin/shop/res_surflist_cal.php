<?php
$Year = ($_REQUEST["selY"] == "") ? date("Y") : $_REQUEST["selY"];
$Mon = ($_REQUEST["selM"] == "") ? date("m") : $_REQUEST["selM"];

if($_REQUEST["selY"] != ""){
    include __DIR__.'/../../db.php';
    
    session_start();
}

?>

<form name="frmCal" id="frmCal" autocomplete="off">
	<div class="gg_first" style="margin-top:0px;">날짜검색</div>
	<table class='et_vars exForm bd_tb' style="width:100%">
		<colgroup>
			<col style="width:65px;">
			<col style="width:*;">
			<col style="width:100px;">
		</colgroup>
		
		<tr>
			<th>검색날짜</th>
			<td align="center">
				<select id="selY" name="selY" class="select">
				<?for($r=2019;$r<=date("Y");$r++){?>
					<option value="<?=$r?>" <?echo (($Year == $r) ? "selected='selected'" : "")?>><?=$r?></option>
				<?}?>
				</select>년&nbsp;
				<select id="selM" name="selM" class="select">
					<?for($r=1;$r<=12;$r++){?>
					<option value="<?=$r?>" <?echo (($Mon == $r) ? "selected='selected'" : "")?>><?=$r?></option>
				<?}?>
				</select>월
			</td>
			<td style="text-align:center;"><input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:60px; height:23px;" value="조회" onclick="fnCalSearch('shop/res_surflist_cal.php');" /></td>
		</tr>
	</table>
</form>

<?
$select_query = 'SELECT shopcharge, account_yn FROM `AT_PROD_MAIN` WHERE seq = '.$_SESSION['shopseq'];
$result_shop = mysqli_query($conn, $select_query);
$rowshop = mysqli_fetch_array($result_shop);

$shopcharge = $rowshop["shopcharge"];
$shopchargePer = ($rowshop["shopcharge"] / 100); //수수료
$account_yn = $rowshop["account_yn"]; //정산여부

$select_query = "SELECT COUNT(*) AS Cnt, res_date, DAY(res_date) AS sDay, a.res_confirm, SUM(res_price) AS price, SUM(rtn_totalprice) AS RtnPrice, d.cal_year, d.cal_month, d.cal_yn FROM `AT_RES_SUB` as a 
            INNER JOIN `AT_RES_MAIN` as b
                ON a.resnum = b.resnum
            LEFT JOIN AT_PROD_CAL as d
                ON a.seq = d.seq
                    AND d.cal_year = $Year AND d.cal_month = $Mon
            WHERE a.seq = ".$_SESSION['shopseq']."
                AND a.res_confirm IN (3,4,5)
                AND (Year(res_date) = $Year AND Month(res_date) = $Mon)
            GROUP BY a.res_date, a.res_confirm";
$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);
?>

<div class="gg_first" style="padding-bottom:7px;">[<?=$_SESSION['shopname']?>] - <?=$Year.'년 '.$Mon.'월'?> 일자별 정산</div>

<table class="et_vars exForm bd_tb" style="margin-bottom:5px;width:100%;">
<colgroup>
	<col style="width:100px;">
	<col style="width:*;">
	<col style="width:*;">
	<col style="width:*;">
	<col style="width:*;">
</colgroup>
<tbody>

<?
if($count == 0){
	echo '<tr><td><div style="text-align:center;font-size:14px;padding:50px;">
				<b>정산내역이 없습니다.</b>
			</div></td></tr>';
}else{
?>
	<tr>
		<th style="text-align:center;" rowspan="2">날짜</th>
		<th style="text-align:center;" colspan="3">구분</th>
		<th style="text-align:center;" rowspan="2">정산금액</th>
	</tr>
	<tr>
		<th style="text-align:center;">확정</th>
		<th style="text-align:center;">수수료율</th>
		<th style="text-align:center;">수수료</th>
	</tr>
<?
	$TotalPrice = 0;
	$TotalCalPrice = 0;
	$TotalRtnPrice = 0;

	$arrShopDate = array();
	$arrShopPrice1 = array(); //확정 금액
	$arrShopPrice2 = array(); //수수료 금액
	$arrShopPrice3 = array(); //환불 금액
	$arrShopPrice4 = array(); //정산금액
	while ($row = mysqli_fetch_assoc($result_setlist)){
        $cal_yn = $row['cal_yn'];
		if(array_key_exists($row['res_date'], $arrShopPrice1)){
		}else{
			$arrShopDate[$row['res_date']] = $row['res_date'];
			$arrShopPrice1[$row['res_date']] = 0;
			$arrShopPrice2[$row['res_date']] = 0;
			$arrShopPrice3[$row['res_date']] = 0;
			$arrShopPrice4[$row['res_date']] = 0;
		}

		if($row['res_confirm'] == 3){
			$ResConfirm = "<font color='blue'><b>확정<b/></font>";

			$arrShopPrice1[$row['res_date']] += $row["price"];
			$arrShopPrice2[$row['res_date']] += ($row["price"] * $shopchargePer);
		}else if($row['res_confirm'] == 4 || $row['res_confirm'] == 5){
			$ResConfirm = "<font color='red'>환불</font>";
			$arrShopPrice3[$row['res_date']] += $row["price"];
		}
	}

foreach($arrShopDate as $x => $x_value) {
	$TotalPrice += $arrShopPrice1[$x];
	$TotalCalPrice += $arrShopPrice2[$x];
	$TotalRtnPrice += $arrShopPrice3[$x];
?>
	<tr>
		<td style="text-align:center;"><?=$arrShopDate[$x]?></td>
		<td style="text-align:center;"><?=number_format($arrShopPrice1[$x])?>원</td>
		<td style="text-align:center;"><font color='blue'><?=$shopcharge?>%</font></td>
		<td style="text-align:center;"><font color='cc6600'><?=number_format($arrShopPrice2[$x])?>원</font></td>
		<td style="text-align:center;"><font color='red'><b><?=number_format($arrShopPrice1[$x] - $arrShopPrice2[$x])?>원</b></td>
	</tr>
<?
	}
}
?>
</tbody>
</table>

<div class="gg_first" style="padding-bottom:7px;">총 합계안내
<?if($account_yn == "Y"){?>
 - [정산여부 : <?if($cal_yn == "Y") { echo "정산완료"; }else{ echo "<font color='red'>미정산</font>"; }?>]
<?}?>
 </div>

<table class="et_vars exForm bd_tb" style="margin-bottom:5px;width:100%;">
<colgroup>
	<col style="width:25%;">
	<col style="width:25%;">
	<col style="width:25%;">
	<col style="width:25%;">
</colgroup>
<tbody>
	<tr>
		<th style="text-align:center;">확정</th>
		<th style="text-align:center;">수수료율</th>
		<th style="text-align:center;">수수료</th>
		<th style="text-align:center;">정산금액</th>
	</tr>
	<tr>
		<td style="text-align:center;"><?=number_format($TotalPrice)?>원</td>
		<td style="text-align:center;"><font color='blue'><?=$shopcharge?>%</font></td>
		<td style="text-align:center;"><font color='cc6600'><?=number_format($TotalCalPrice)?>원</font></td>
		<td style="text-align:center;"><font color='red'><b><?=number_format($TotalPrice - $TotalCalPrice)?>원</b></font></td>
	</tr>
</tbody>
</table>
