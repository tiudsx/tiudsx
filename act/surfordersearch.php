<?php 
include 'db.php';

$resNumber = $_REQUEST["resNumber"];

if($resNumber != ""){ 
    echo "<script>location.href = '/orderview?num=1&resNumber=$resNumber';</script>";
    return;
}
?>

<script>
$j(document).ready(function(){
});
</script>

<script src="js/surfordersearch.js"></script>

<div id="wrap">
    <? include '_layout_top.php'; ?>

    <link rel="stylesheet" href="css/surfview.css">

    <div class="top_area_zone">
        <section class="shoptitle">
            <div style="padding:6px;">
                <h1>액트립 예약조회</h1>
            </div>
        </section>
        <section class="notice">
            <div class="bd" style="padding:0 4px;min-height:300px;display:none;" id="surfSelOk">
			</div>

            <div class="bd" style="padding:0 4px;min-height:300px;" id="surfSel">
                <p class="restitle">예약번호로 조회</p>
                <table class="et_vars exForm bd_tb bustext" style="width:100%;margin-bottom:5px;">
                    <tbody>
                        <tr>
                            <th><em>*</em> 예약번호</th>
                            <td><input type="text" id="resNumber" name="resNumber" value="<?=$resNumber?>" class="itx" autocomplete="off"></td>
                        </tr>
                    </tbody>
                </table>
                <div class="write_table" style="padding-top:15px; text-align:center;" id="divBtnRes">
                    <div>
                        <input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:50%; height:40px;" value="예약조회" onclick="fnOrderSearch(0);" />
                    </div>
                </div>
            </div>
            
            <iframe id="ifrmResize" name="ifrmResize" style="width:800px;height:400px;display:none;"></iframe>

        </section>
    </div>
</div>

<? include '_layout_bottom.php'; ?>