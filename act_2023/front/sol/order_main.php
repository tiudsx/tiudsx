<?php 
include __DIR__.'/../../common/db.php';
?>

<script>
function fnSolSearch(num) {
    if ($j.trim($j("#res_date").val()) == "") {
        alert("이용일을 선택하세요.");
        return;
	}

    $j("#resNumber").val($j.trim($j("#resNumber").val().replace(/-/g, '')));

    if ($j.trim($j("#resNumber").val()) == "") {
        alert("전화번호를 입력하세요.");
        return;
	}

    var params = "type=search&res_date=" + $j("#res_date").val() + "&resNumber=" + $j.trim($j("#resNumber").val().replace(/ /g, ''));
    var rtn = $j.ajax({
        type: "POST",
        url: "/act_2023/front/sol/order_json.php",
        data: params,
        success: function(data) {
            return data;
        }
    }).responseText;

    if(rtn == "no"){
        alert("예약된 정보가 없습니다.\n\n다시 확인해주세요.");    
    }else{
        location.href = "/sol_kakao?seq=" + rtn;
    }
}
</script>

<script type="text/javascript" src="/act_2023/front/_js/ordersearch.js?v=<?=time()?>"></script>

<div id="wrap">
    <? include __DIR__.'/../../_layout/_layout_top_sol.php'; ?>

    <link rel="stylesheet" type="text/css" href="/act_2023/front/_css/surfview.css">

    <div class="top_area_zone">
        <section class="shoptitle">
            <div style="padding:6px;">
                <h1>솔게하&솔서프 예약조회</h1>
            </div>
        </section>
        <section class="notice">
            <div class="bd" style="padding:0 4px;min-height:300px;display:none;" id="surfSelOk">
			</div>

            <div class="bd" style="padding:0 4px;min-height:300px;" id="surfSel">
                <p class="restitle">예약정보 조회</p>
                <table class="et_vars exForm bd_tb bustext" style="width:100%;margin-bottom:5px;">
                    <tbody>
                        <tr>
                            <th><em>*</em> 이용일</th>
                            <td><input type="text" calid="res_date" id="res_date" name="res_date" cal="date" size="10" class="itx" readonly="readonly"></td>
                        </tr>
                        <tr>
                            <th><em>*</em> 전화번호</th>
                            <td>
                                <input type="text" id="resNumber" name="resNumber" value="" class="itx" autocomplete="off">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="write_table" style="padding-top:15px; text-align:center;" id="divBtnRes">
                    <div>
                        <input type="button" class="gg_btn gg_btn_grid large gg_btn_color" style="width:50%; height:40px;" value="예약조회" onclick="fnSolSearch(0);" />
                    </div>
                </div>
            </div>
            
            <iframe id="ifrmResize" name="ifrmResize" style="width:800px;height:400px;display:none;"></iframe>

        </section>
    </div>
</div>

<script type="text/javascript" src="/act_2023/front/_js/common.js?v=<?=time()?>"></script>