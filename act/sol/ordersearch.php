<?php 
$resNumber = $_REQUEST["resNumber"];
?>

<script>
$j(document).ready(function(){
    var params = "resparam=solview&resseq=<?=$resNumber?>";
    $j.ajax({
        type: "POST",
        url: "/act/admin/sol/res_sollist_info.php",
        data: params,
        success: function (data) {
            return;
            for (let i = 0; i < data.length; i++) {
                if(i == 0){
                    $j("#resseq").val(data[i].resseq);
                    $j("#res_adminname").val(data[i].admin_user);
                    $j("#user_name").val(data[i].user_name);
                    var arrTel = data[i].user_tel.split("-");
                    $j("#user_tel1").val(arrTel[0]);
                    $j("#user_tel2").val(arrTel[1]);
                    $j("#user_tel3").val(arrTel[2]);
                    $j("#res_company").val(data[i].res_company);
                    $j("#res_confirm").val(data[i].res_confirm);
                    $j("#memo").val(data[i].memo);
                    $j("#memo2").val(data[i].memo2);
                }

                if(data[i].res_type == "stay"){ //숙박&바베큐
                    fnSolAdd(null, 'trstay');
                    
                    var objTr = $j("tr[id=trstay]:last");
                    objTr.find("#stayseq").val(data[i].ressubseq);
                    objTr.find("#staytype").val("U");
                    objTr.find("#res_staysex").val(data[i].staysex);
                    objTr.find("#res_stayM").val(data[i].stayM);
                    
                    if(data[i].prod_name != "N"){
                        objTr.find("#res_stayshop").val(data[i].prod_name);
                        objTr.find("input[calid=res_staysdate]").val(data[i].sdate);
                        objTr.find("input[calid=res_stayedate]").val(data[i].edate);

                        if(data[i].stayroom != ""){  
                            objTr.find("#res_stayroom").val(data[i].stayroom);

                            fnRoomNum2(objTr.find("#res_staynum"), data[i].staynum);
                        }
                    }
                    if(data[i].bbq != "N"){
                        objTr.find("input[calid=res_bbqdate]").val(data[i].resdate);
                        objTr.find("#res_bbq").val(data[i].bbq);
                    }
                }else{ //강습&렌탈
                    fnSolAdd(null, 'trsurf');

                    var objTr = $j("tr[id=trsurf]:last");
                    objTr.find("#surfseq").val(data[i].ressubseq);
                    objTr.find("#surftype").val("U");
                    objTr.find("input[calid=res_surfdate]").val(data[i].resdate);
                    if(data[i].prod_name != "N"){
                        objTr.find("#res_surfshop").val(data[i].prod_name);
                        objTr.find("#res_surftime").val(data[i].restime);
                        objTr.find("#res_surfM").val(data[i].surfM);
                        objTr.find("#res_surfW").val(data[i].surfW);
                    }
                    if(data[i].surfrent != "N"){
                        objTr.find("#res_rent").val(data[i].surfrent);
                        objTr.find("#res_rentM").val(data[i].surfrentM);
                        objTr.find("#res_rentW").val(data[i].surfrentW);
                    }
                }
            }
        }
    });
});
</script>


<div id="wrap">
    <? include __DIR__.'/../_layout_top.php'; ?>

    <link rel="stylesheet" href="../css/surfview.css">

    <div class="top_area_zone">
        <section class="shoptitle">
            <div style="padding:6px;">
                <h1>액트립 예약조회</h1>
            </div>
        </section>
        <section class="notice">
        <div class="bd" style="padding:0 4px;min-height:300px;" id="contentbind">

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
                                        <col style="width:80px;">
                                        <col style="width:auto;">
                                        <col style="width:70px;">
                                    </colgroup>
                                    <tr>
                                        <th style="text-align:center;">이용일</th>
                                        <th style="text-align:center;">예약항목</th>
                                        <th style="text-align:center;">상태</th>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        </section>
    </div>
</div>

<? include __DIR__.'/../_layout_bottom.php'; ?>