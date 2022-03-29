<?php 
include __DIR__.'/../../db.php';
include __DIR__.'/../../common/logininfo.php';

// 0 : 관리자
// 2 : 사업자회원
session_start();

if($_SESSION['shopseq'] == ""){	
	Header("Location:/shopadmin");
}

$shopseq = $_SESSION["shopseq"];

$arrShop = explode(",", $surftype);
$Shopcnt = count($arrShop);

if($Shopcnt > 0){
	$select_query = "SELECT * FROM `AT_PROD_MAIN` WHERE seq IN ($surftype) ORDER BY categoryname, code, shopname";
	$result_setlist = mysqli_query($conn, $select_query);
	$countAdmin = mysqli_num_rows($result_setlist);
	// if($countAdmin == 0){
	// 	echo '<script>alert("관리자 권한이 없습니다.");location.href="/";</script>';
	// 	exit;
	// }
}

// if($_SESSION['shopseq'] == ""){	

// 	$_SESSION['userid'] = $user_id;
// 	$_SESSION['shopseq'] = $rowAdmin["seq"];
// 	$_SESSION['shopname'] = $rowAdmin["shopname"];

// 	$shopseq = $rowAdmin["seq"];
// }else{
// 	$shopseq = $_SESSION['shopseq'];
// }


if($Shopcnt > 1){
	$css = ' style="line-height:2em;"';
}
?>
<script>
    var mobileuse = "";
</script>
<div class="bd_tl" style="width:100%;">
	<h1 class="ngeb clear"<?=$css?>>
		<i class="bg_color"></i>[<?=$_SESSION['shopname']?>] 예약관리
		<?
		if($Shopcnt > 1){
		?>
		<br>
		<select id='selShop' name='selShop' class='select' style='padding:1px 2px 4px 2px;'>

		<?while ($row = mysqli_fetch_assoc($result_setlist)){
			$seq = $row['seq'];
			$categoryname = $row['categoryname'];
			$shopname = $row['shopname'];

			$selected = "";
			if($_SESSION['shopseq'] == $seq){
				$selected = " selected='selected'";
			}

			echo "<option value='$seq'$selected>[$categoryname] $shopname</option>";
		}?>

		</select>
		<input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:45px; height:24px;background:green;" value="변경" onclick="fnChangeShop();">
		<?
		}
		?>
	</h1>
</div>

<script>
    var busDateinit = "2020-04-01";
</script>
<link rel="stylesheet" type="text/css" href="/act/css/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="/act/css/surfview.css">
<link rel="stylesheet" type="text/css" href="/act/css/admin/admin_surf.css">
<link rel="stylesheet" type="text/css" href="/act/css/admin/admin_common.css">
<script type="text/javascript" src="/act/js/admin_surf.js"></script>
<script type="text/javascript" src="/act/js/surfview_bus.js"></script>
<script type="text/javascript" src="/act/js/jquery.blockUI.js"></script>

<div class="container" id="contenttop">
  <section>
    <article id="right_article3" class="right_article3">
		<?include 'res_surfcalendar.php'?>
    </article>
    <aside id="left_article3" class="left_article3">
<!-- .tab_container -->
<div id="containerTab" class="areaRight">

<?
include __DIR__.'/../../surf/surffunc.php';
// echo "surfadminkakao?param=".urlencode(encrypt(date("Y-m-d").'|5'));
?>

    <ul class="tabs">
        <li class="active" rel="tab1">예약관리</li>
        <li rel="tab2">매진처리</li>
        <li rel="tab3">정산관리</li>
    </ul>

	<!-- #container -->
    <div class="tab_container">
        <!-- #tab1 -->
        <div id="tab1" class="tab_content">
		<form name="frmSearch" id="frmSearch" autocomplete="off">
			<div class="gg_first" style="margin-top:0px;">예약검색</div>
			<table class='et_vars exForm bd_tb' style="width:100%">
				<colgroup>
					<col style="width:65px;">
					<col style="width:*;">
					<col style="width:100px;">
				</colgroup>
				<tr>
					<th>구분</th>
					<td>
					<?if($user_id == "surfenjoy"){?>
						<label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" value="0" style="vertical-align:-3px;" />미입금</label>
					<?}?>
						<label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" value="8" checked="checked" style="vertical-align:-3px;" />입금완료</label>
						<label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" value="3" style="vertical-align:-3px;" />확정</label>
						<!-- <label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" value="7" style="vertical-align:-3px;" />취소</label><br> -->
						<label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" value="2" style="vertical-align:-3px;" />임시확정/취소</label>
						<!-- <label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" value="6" style="vertical-align:-3px;" />임시취소</label> -->
						<!-- <label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" value="4" checked="checked" style="vertical-align:-3px;" />환불요청</label> -->
						<!-- <label><input type="checkbox" id="chkResConfirm" name="chkResConfirm[]" value="5" style="vertical-align:-3px;" />취소</label> -->
					</td>
				</tr>
				<tr>
					<th>검색기간</th>
					<td>
						<input type="hidden" id="hidsearch" name="hidsearch" value="init">
						<input type="text" id="sDate" name="sDate" cal="sdate" readonly="readonly" value="" class="itx2" maxlength="7" style="width:66px;" >&nbsp;~
						<input type="text" id="eDate" name="eDate" cal="edate" readonly="readonly" value="" class="itx2" maxlength="7" style="width:66px;" >
						<input type="hidden" id="seq" name="seq" size="10" value="<?=$shopseq?>" class="itx">
						<input type="button" class="bd_btn" style="padding-top:4px;font-family: gulim,Tahoma,Arial,Sans-serif;" value="전체" onclick="fnDateReset();" />
					</td>
					
				</tr>
				<tr>
					<th>검색어</th>
					<td><input type="text" id="schText" name="schText" value="" class="itx2" style="width:140px;"></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:center;"><input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:120px; height:40px;" value="검색" onclick="fnSearchAdmin('shop/res_surflist_search.php');" /></td>
				</tr>
			</table>
		</form>
		</div>

		<div id="tab2" class="tab_content" style="display:none;">
<?
$select_query = "SELECT a.*, b.codename FROM `AT_PROD_OPT` a INNER JOIN `AT_CODE` b
					ON a.optcode = b.code
						AND b.uppercode = 'surfres'
 					WHERE a.seq = ".$_SESSION['shopseq']." 
						AND a.use_yn = 'Y' 
					ORDER BY a.optcode, a.ordernum";
$result_opt = mysqli_query($conn, $select_query);
?>
			<form name="frmSold" id="frmSold" autocomplete="off">
				<div class="gg_first" style="margin-top:0px;">매진항목 추가</div>
				<table class='et_vars exForm bd_tb' style="width:100%">
					<colgroup>
						<col style="width:65px;">
						<col style="width:*;">
					</colgroup>
					
					<tr>
						<th>날짜</th>
						<td>
							<input type="text" id="strDate" name="strDate" readonly="readonly" value="" class="itx2" cal="sdate" style="width:66px;"> ~ 
							<input type="text" id="strDateE" name="strDateE" readonly="readonly" value="" class="itx2" cal="edate" style="width:66px;">
							<input type="hidden" id="resparam" name="resparam" size="10" value="soldout" class="itx">
							<input type="hidden" id="userid" name="userid" size="10" value="<?=$user_id?>" class="itx">
						</td>
					</tr>
					<tr>
						<th>항목</th>
						<td>
							<?while ($rowOpt = mysqli_fetch_assoc($result_opt)){?>
								<label><input type="checkbox" id="selItem" name="selItem[]" value="<?=$rowOpt["optseq"]?>" style="vertical-align:-3px;" />[<?=$rowOpt["codename"]?>] <?=$rowOpt["optname"]?></label><br>
							<?}?>
						</td>
					</tr>
					<tr>
						<th>성별</th>
						<td>
							<label><input type="checkbox" id="chkSexM" name="chkSexM" value="1" checked="checked" style="vertical-align:-3px;" />남</label>&nbsp;
							<label><input type="checkbox" id="chkSexW" name="chkSexW" value="1" checked="checked" style="vertical-align:-3px;" />여</label>
						</td>
					</tr>
					<tr>
						<td style="text-align:center;" colspan="2"><input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:100px; height:30px;" value="매진 추가" onclick="fnSoldout();" /></td>
					</tr>
				</table>
			</form>
			<div id="divSoldOutList">
				<?include 'res_surflist_soldout.php'?>
			</div>
		</div>

		<div id="tab3" class="tab_content" style="display:none;">
			<div id="divCalList">
				<?include 'res_surflist_cal.php'?>
			</div>
		</div>
    </div>
    <!-- .tab_container -->
</div>
<!-- #container -->

		</aside>
	</section>
	<div id="mngSearch" style="display:inline-block;width:100%"><?include 'res_surflist_search.php'?></div>
</div>