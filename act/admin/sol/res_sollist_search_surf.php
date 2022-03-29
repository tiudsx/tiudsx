<?
include __DIR__.'/../../db.php';

$reqDate = $_REQUEST["selDate"];
if($reqDate == ""){
    // $selDate = str_replace("-", "", date("Y-m-d"));
    $selDate = date("Y-m-d");
}else{
    $selDate = $reqDate;
}
$arrDate = explode('-', $selDate);

$Year = $arrDate[0];
$Mon = $arrDate[1];
$Day = $arrDate[2];

$select_query = "SELECT * FROM AT_SOL_RES_MAIN as a INNER JOIN AT_SOL_RES_SUB as b 
                    ON a.resseq = b.resseq 
                    WHERE b.resdate = '$selDate'
                        AND b.res_type = 'surf'
                        AND a.res_confirm = '확정'
                        ORDER BY  b.ressubseq, b.prod_name, b.restime";
//echo $select_query;
$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);
?>

<div class="contentimg bd">
<form name="frmConfirm" id="frmConfirm" autocomplete="off">
    <div class="gg_first">예약 현황 (<span id="listdate"><?=$selDate?></span>)
        <input type="button" name="listtab" class="gg_btn gg_btn_grid large" style="width:80px; height:20px;" value="전체" onclick="fnListTab('all', this);" />
        <input type="button" name="listtab" class="gg_btn gg_btn_grid large " style="width:80px; height:20px;" value="숙박&바베큐" onclick="fnListTab('stay', this);" />
        <input type="button" name="listtab" class="gg_btn gg_btn_grid large gg_btn_color" style="width:80px; height:20px;" value="강습&렌탈" onclick="fnListTab('surf', this);" />
    </div>
    
<?
$c = 0;
$surflist_rent = "";
$surflist_sol_9 = "";
$surflist_sol_11 = "";
$surflist_sol_13 = "";
$surflist_sol_15 = "";
$surflist_sp_9 = "";
$surflist_sp_11 = "";
$surflist_sp_13 = "";
$surflist_sp_15 = "";
$surflist_lala_9 = "";
$surflist_lala_11 = "";
$surflist_lala_13 = "";
$surflist_lala_15 = "";
$surflist_rang_9 = "";
$surflist_rang_11 = "";
$surflist_rang_13 = "";
$surflist_rang_15 = "";
while ($row = mysqli_fetch_assoc($result_setlist)){
    $c++;

	$now = date("Y-m-d");

    $resseq = $row['resseq'];
    $admin_user = $row['admin_user'];
	$res_company = $row['res_company'];
	$user_name = $row['user_name'];
    $user_tel = $row['user_tel'];
    $memo = $row['memo'];
    $memo2 = $row['memo2'];
    $ressubseq = $row['ressubseq'];
    $res_type = $row['res_type'];
    $prod_name = $row['prod_name'];
    $resdate = $row['resdate'];
    $restime = $row['restime'];
    $surfM = $row['surfM'];
    $surfW = $row['surfW'];
    $surfrent = $row['surfrent'];
    $surfrentM = $row['surfrentM'];
    $surfrentW = $row['surfrentW'];
    $surfrentYN = $row['surfrentYN'];

    $memoYN = "";
    $memoText = "";
    if($memo != "" || $memo2 != ""){
        $memoYN = "O";
    }

    if($memo != ""){
        $memoText .= "<b>요청사항</b><br>$memo<br><br>";
    }

    if($memo2 != ""){
        $memoText .= "<b>직원메모</b><br>".$memo2;
    }

    //강습&렌탈
    if($prod_name != "N"){
        $surflist_text = "
            <tr onmouseover=\"this.style.background='#ff9';\" onmouseout=\"this.style.background='#fff';\">
                <td style='cursor:pointer;' onclick='fnSolModify($resseq);'>$user_name</td>
                <td style='cursor:pointer;' onclick='fnSolModify($resseq);'>$user_tel</td>
                <td>".(($surfM == 0) ? "" : $surfM."명")."</td>
                <td>".(($surfW == 0) ? "" : $surfW."명")."</td>
                <td><span class='btn_view' seq='1$c'>$memoYN</span><span style='display:none;'>$memoText</span></td>
            </tr>
        ";
        if($prod_name == "솔게스트하우스"){
            if($restime == "9시"){
                $surflist_sol_9 .= $surflist_text;
                $surfMCnt_sol_9 += $surfM;
                $surfWCnt_sol_9 += $surfW;
            }else if($restime == "11시"){
                $surflist_sol_11 .= $surflist_text;
                $surfMCnt_sol_11 += $surfM;
                $surfWCnt_sol_11 += $surfW;
            }else if($restime == "13시"){
                $surflist_sol_13 .= $surflist_text;
                $surfMCnt_sol_13 += $surfM;
                $surfWCnt_sol_13 += $surfW;
            }else if($restime == "15시"){
                $surflist_sol_15 .= $surflist_text;
                $surfMCnt_sol_15 += $surfM;
                $surfWCnt_sol_15 += $surfW;
            }
        }else if($prod_name == "서프팩토리"){
            if($restime == "9시"){
                $surflist_sp_9 .= $surflist_text;
                $surfMCnt_sp_9 += $surfM;
                $surfWCnt_sp_9 += $surfW;
            }else if($restime == "11시"){
                $surflist_sp_11 .= $surflist_text;
                $surfMCnt_sp_11 += $surfM;
                $surfWCnt_sp_11 += $surfW;
            }else if($restime == "13시"){
                $surflist_sp_13 .= $surflist_text;
                $surfMCnt_sp_13 += $surfM;
                $surfWCnt_sp_13 += $surfW;
            }else if($restime == "15시"){
                $surflist_sp_15 .= $surflist_text;
                $surfMCnt_sp_15 += $surfM;
                $surfWCnt_sp_15 += $surfW;
            }
        }else if($prod_name == "라라서프"){
            if($restime == "9시"){
                $surflist_lala_9 .= $surflist_text;
                $surfMCnt_lala_9 += $surfM;
                $surfWCnt_lala_9 += $surfW;
            }else if($restime == "11시"){
                $surflist_lala_11 .= $surflist_text;
                $surfMCnt_lala_11 += $surfM;
                $surfWCnt_lala_11 += $surfW;
            }else if($restime == "13시"){
                $surflist_lala_13 .= $surflist_text;
                $surfMCnt_lala_13 += $surfM;
                $surfWCnt_lala_13 += $surfW;
            }else if($restime == "15시"){
                $surflist_lala_15 .= $surflist_text;
                $surfMCnt_lala_15 += $surfM;
                $surfWCnt_lala_15 += $surfW;
            }
        }else if($prod_name == "서퍼랑"){
            if($restime == "9시"){
                $surflist_rang_9 .= $surflist_text;
                $surfMCnt_rang_9 += $surfM;
                $surfWCnt_rang_9 += $surfW;
            }else if($restime == "11시"){
                $surflist_rang_11 .= $surflist_text;
                $surfMCnt_rang_11 += $surfM;
                $surfWCnt_rang_11 += $surfW;
            }else if($restime == "13시"){
                $surflist_rang_13 .= $surflist_text;
                $surfMCnt_rang_13 += $surfM;
                $surfWCnt_rang_13 += $surfW;
            }else if($restime == "15시"){
                $surflist_rang_15 .= $surflist_text;
                $surfMCnt_rang_15 += $surfM;
                $surfWCnt_rang_15 += $surfW;
            }
        }
    }

    if($surfrent != "N"){
        $sDel = "";
        $eDel = "";
        if($surfrentYN == "Y"){
            $sDel = "<del>";
            $eDel = "</del>";
        }
        $surflist_rent .= "
        <tr onmouseover=\"this.style.background='#ff9';\" onmouseout=\"this.style.background='#fff';\">
            <td style='cursor:pointer;' onclick='fnSolModify($resseq);'><b>$sDel$user_name$eDel</b></td>
            <td style='cursor:pointer;' onclick='fnSolModify($resseq);'><b>$sDel$user_tel$eDel</b></td>
            <td>$sDel$surfrent$eDel</td>
            <td>$sDel".(($surfrentM == 0) ? "" : $surfrentM."명")."$eDel</td>
            <td>$sDel".(($surfrentW == 0) ? "" : $surfrentW."명")."$eDel</td>
            <td>$sDel<span class='btn_view' seq='2$c'>$memoYN</span><span style='display:none;'>$memoText</span>$eDel</td>
            <td>
                <select class='select' onchange='fnRentYN(this, $ressubseq);'>
                    <option value='N'>N</option>
                    <option value='Y' ".(($surfrentYN == "Y") ? "selected" : "").">Y</option>
                </select>
            </td>
        </tr>
        ";
    }
//while end
}

$tbrent = "none";
$tbsp = "none";
$tblala = "none";
$tbrang = "none";
if(!($surflist_rent == "")){
    $tbrent = "";
}

if(!($surflist_sp_9 == "" && $surflist_sp_11 == "" && $surflist_sp_13 == "" && $surflist_sp_15 == "")){
    $tbsp = "";
}
if(!($surflist_lala_9 == "" && $surflist_lala_11 == "" && $surflist_lala_13 == "" && $surflist_lala_15 == "")){
    $tblala = "";
}
if(!($surflist_rang_9 == "" && $surflist_rang_11 == "" && $surflist_rang_13 == "" && $surflist_rang_15 == "")){
    $tbrang = "";
}
?>

    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:40%;display:<?=$tbrent?>;">
        <tbody>
            <tr>
                <th>장비렌탈</th>
            </tr>
            <tr>
                <td style="vertical-align: top;">
                    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:100%;">
                        <colgroup>
                            <col width="*" />
                            <col width="25%" />
                            <col width="19%" />
                            <col width="10%" />
                            <col width="10%" />
                            <col width="10%" />
                            <col width="15%" />
                        </colgroup>
                        <tbody>
                            <tr>
                                <th>이름</th>
                                <th>연락처</th>
                                <th>렌탈종류</th>
                                <th>남</th>
                                <th>여</th>
                                <th>메모</th>
                                <th>이용여부</th>
                            </tr>
                            <?=$surflist_rent?>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:100%;">
        <colgroup>
            <col width="25%" />
            <col width="25%" />
            <col width="25%" />
            <col width="25%" />
        </colgroup>
        <tbody>
            <tr>
                <th colspan="4">솔게스트하우스 동해서핑점</th>
            </tr>
            <tr>
                <th>9시 서핑강습</th>
                <th>11시 서핑강습</th>
                <th>13시 서핑강습</th>
                <th>15시 서핑강습</th>
            </tr>
            <tr>
                <td style="vertical-align: top;">
                    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:100%;<?if($surflist_sol_9 == ""){ echo "display:none;"; }?>">
                        <colgroup>
                            <col width="*" />
                            <col width="35%" />
                            <col width="13%" />
                            <col width="13%" />
                            <col width="13%" />
                        </colgroup>
                        <tbody>
                            <tr>
                                <th>이름</th>
                                <th>연락처</th>
                                <th>남</th>
                                <th>여</th>
                                <th>메모</th>
                            </tr>
                            <?=$surflist_sol_9?>
                            <tr>
                                <th colspan="2">총 인원</th>
                                <td><?=($surfMCnt_sol_9 == 0) ? "" : $surfMCnt_sol_9."명"?></td>
                                <td><?=($surfWCnt_sol_9 == 0) ? "" : $surfWCnt_sol_9."명"?></td>
                                <th><?=($surfMCnt_sol_9+$surfWCnt_sol_9 == 0) ? "" : $surfMCnt_sol_9+$surfWCnt_sol_9."명"?></th>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="vertical-align: top;">
                    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:100%;<?if($surflist_sol_11 == ""){ echo "display:none;"; }?>">
                        <colgroup>
                            <col width="*" />
                            <col width="35%" />
                            <col width="13%" />
                            <col width="13%" />
                            <col width="13%" />
                        </colgroup>
                        <tbody>
                            <tr>
                                <th>이름</th>
                                <th>연락처</th>
                                <th>남</th>
                                <th>여</th>
                                <th>메모</th>
                            </tr>
                            <?=$surflist_sol_11?>
                            <tr>
                                <th colspan="2">총 인원</th>
                                <td><?=($surfMCnt_sol_11 == 0) ? "" : $surfMCnt_sol_11."명"?></td>
                                <td><?=($surfWCnt_sol_11 == 0) ? "" : $surfWCnt_sol_11."명"?></td>
                                <th><?=($surfMCnt_sol_11+$surfWCnt_sol_11 == 0) ? "" : $surfMCnt_sol_11+$surfWCnt_sol_11."명"?></th>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="vertical-align: top;">
                    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:100%;<?if($surflist_sol_13 == ""){ echo "display:none;"; }?>">
                        <colgroup>
                            <col width="*" />
                            <col width="35%" />
                            <col width="13%" />
                            <col width="13%" />
                            <col width="13%" />
                        </colgroup>
                        <tbody>
                            <tr>
                                <th>이름</th>
                                <th>연락처</th>
                                <th>남</th>
                                <th>여</th>
                                <th>메모</th>
                            </tr>
                            <?=$surflist_sol_13?>
                            <tr>
                                <th colspan="2">총 인원</th>
                                <td><?=($surfMCnt_sol_13 == 0) ? "" : $surfMCnt_sol_13."명"?></td>
                                <td><?=($surfWCnt_sol_13 == 0) ? "" : $surfWCnt_sol_13."명"?></td>
                                <th><?=($surfMCnt_sol_13+$surfWCnt_sol_13 == 0) ? "" : $surfMCnt_sol_13+$surfWCnt_sol_13."명"?></th>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="vertical-align: top;">
                    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:100%;<?if($surflist_sol_15 == ""){ echo "display:none;"; }?>">                        
                        <colgroup>
                            <col width="*" />
                            <col width="35%" />
                            <col width="13%" />
                            <col width="13%" />
                            <col width="13%" />
                        </colgroup>
                        <tbody>
                            <tr>
                                <th>이름</th>
                                <th>연락처</th>
                                <th>남</th>
                                <th>여</th>
                                <th>메모</th>
                            </tr>
                            <?=$surflist_sol_15?>
                            <tr>
                                <th colspan="2">총 인원</th>
                                <td><?=($surfMCnt_sol_15 == 0) ? "" : $surfMCnt_sol_15."명"?></td>
                                <td><?=($surfWCnt_sol_15 == 0) ? "" : $surfWCnt_sol_15."명"?></td>
                                <th><?=($surfMCnt_sol_15+$surfWCnt_sol_15 == 0) ? "" : $surfMCnt_sol_15+$surfWCnt_sol_15."명"?></th>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="et_vars exForm bd_tb tbcenter" style="margin-top:10px;margin-bottom:1px;width:100%;display:<?=$tbsp?>;" id="tbsp">
    <colgroup>
            <col width="25%" />
            <col width="25%" />
            <col width="25%" />
            <col width="25%" />
        </colgroup>
        <tbody>
            <tr>
                <th colspan="4">서프팩토리</th>
            </tr>
            <tr>
                <th>9시 서핑강습</th>
                <th>11시 서핑강습</th>
                <th>13시 서핑강습</th>
                <th>15시 서핑강습</th>
            </tr>
            <tr>
                <td style="vertical-align: top;">
                    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:100%;<?if($surflist_sp_9 == ""){ echo "display:none;"; }?>">
                        <colgroup>
                            <col width="*" />
                            <col width="35%" />
                            <col width="13%" />
                            <col width="13%" />
                            <col width="13%" />
                        </colgroup>
                        <tbody>
                            <tr>
                                <th>이름</th>
                                <th>연락처</th>
                                <th>남</th>
                                <th>여</th>
                                <th>메모</th>
                            </tr>
                            <?=$surflist_sp_9?>
                            <tr>
                                <th colspan="2">총 인원</th>
                                <td><?=($surfMCnt_sp_9 == 0) ? "" : $surfMCnt_sp_9."명"?></td>
                                <td><?=($surfWCnt_sp_9 == 0) ? "" : $surfWCnt_sp_9."명"?></td>
                                <th><?=($surfMCnt_sp_9+$surfWCnt_sp_9 == 0) ? "" : $surfMCnt_sp_9+$surfWCnt_sp_9."명"?></th>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="vertical-align: top;">
                    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:100%;<?if($surflist_sp_11 == ""){ echo "display:none;"; }?>">
                        <colgroup>
                            <col width="*" />
                            <col width="35%" />
                            <col width="13%" />
                            <col width="13%" />
                            <col width="13%" />
                        </colgroup>
                        <tbody>
                            <tr>
                                <th>이름</th>
                                <th>연락처</th>
                                <th>남</th>
                                <th>여</th>
                                <th>메모</th>
                            </tr>
                            <?=$surflist_sp_11?>
                            <tr>
                                <th colspan="2">총 인원</th>
                                <td><?=($surfMCnt_sp_11 == 0) ? "" : $surfMCnt_sp_11."명"?></td>
                                <td><?=($surfWCnt_sp_11 == 0) ? "" : $surfWCnt_sp_11."명"?></td>
                                <th><?=($surfMCnt_sp_11+$surfWCnt_sp_11 == 0) ? "" : $surfMCnt_sp_11+$surfWCnt_sp_11."명"?></th>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="vertical-align: top;">
                    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:100%;<?if($surflist_sp_13 == ""){ echo "display:none;"; }?>">
                        <colgroup>
                            <col width="*" />
                            <col width="35%" />
                            <col width="13%" />
                            <col width="13%" />
                            <col width="13%" />
                        </colgroup>
                        <tbody>
                            <tr>
                                <th>이름</th>
                                <th>연락처</th>
                                <th>남</th>
                                <th>여</th>
                                <th>메모</th>
                            </tr>
                            <?=$surflist_sp_13?>
                            <tr>
                                <th colspan="2">총 인원</th>
                                <td><?=($surfMCnt_sp_13 == 0) ? "" : $surfMCnt_sp_13."명"?></td>
                                <td><?=($surfWCnt_sp_13 == 0) ? "" : $surfWCnt_sp_13."명"?></td>
                                <th><?=($surfMCnt_sp_13+$surfWCnt_sp_13 == 0) ? "" : $surfMCnt_sp_13+$surfWCnt_sp_13."명"?></th>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="vertical-align: top;">
                    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:100%;<?if($surflist_sp_15 == ""){ echo "display:none;"; }?>">                        
                        <colgroup>
                            <col width="*" />
                            <col width="35%" />
                            <col width="13%" />
                            <col width="13%" />
                            <col width="13%" />
                        </colgroup>
                        <tbody>
                            <tr>
                                <th>이름</th>
                                <th>연락처</th>
                                <th>남</th>
                                <th>여</th>
                                <th>메모</th>
                            </tr>
                            <?=$surflist_sp_15?>
                            <tr>
                                <th colspan="2">총 인원</th>
                                <td><?=($surfMCnt_sp_15 == 0) ? "" : $surfMCnt_sp_15."명"?></td>
                                <td><?=($surfWCnt_sp_15 == 0) ? "" : $surfWCnt_sp_15."명"?></td>
                                <th><?=($surfMCnt_sp_15+$surfWCnt_sp_15 == 0) ? "" : $surfMCnt_sp_15+$surfWCnt_sp_15."명"?></th>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="et_vars exForm bd_tb tbcenter" style="margin-top:10px;margin-bottom:1px;width:100%;display:<?=$tbrang?>;" id="tbrang">
    <colgroup>
            <col width="25%" />
            <col width="25%" />
            <col width="25%" />
            <col width="25%" />
        </colgroup>
        <tbody>
            <tr>
                <th colspan="4">서퍼랑</th>
            </tr>
            <tr>
                <th>9시 서핑강습</th>
                <th>11시 서핑강습</th>
                <th>13시 서핑강습</th>
                <th>15시 서핑강습</th>
            </tr>
            <tr>
                <td style="vertical-align: top;">
                    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:100%;<?if($surflist_rang_9 == ""){ echo "display:none;"; }?>">
                        <colgroup>
                            <col width="*" />
                            <col width="35%" />
                            <col width="13%" />
                            <col width="13%" />
                            <col width="13%" />
                        </colgroup>
                        <tbody>
                            <tr>
                                <th>이름</th>
                                <th>연락처</th>
                                <th>남</th>
                                <th>여</th>
                                <th>메모</th>
                            </tr>
                            <?=$surflist_rang_9?>
                            <tr>
                                <th colspan="2">총 인원</th>
                                <td><?=($surfMCnt_rang_9 == 0) ? "" : $surfMCnt_rang_9."명"?></td>
                                <td><?=($surfWCnt_rang_9 == 0) ? "" : $surfWCnt_rang_9."명"?></td>
                                <th><?=($surfMCnt_rang_9+$surfWCnt_rang_9 == 0) ? "" : $surfMCnt_rang_9+$surfWCnt_rang_9."명"?></th>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="vertical-align: top;">
                    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:100%;<?if($surflist_rang_11 == ""){ echo "display:none;"; }?>">
                        <colgroup>
                            <col width="*" />
                            <col width="35%" />
                            <col width="13%" />
                            <col width="13%" />
                            <col width="13%" />
                        </colgroup>
                        <tbody>
                            <tr>
                                <th>이름</th>
                                <th>연락처</th>
                                <th>남</th>
                                <th>여</th>
                                <th>메모</th>
                            </tr>
                            <?=$surflist_rang_11?>
                            <tr>
                                <th colspan="2">총 인원</th>
                                <td><?=($surfMCnt_rang_11 == 0) ? "" : $surfMCnt_rang_11."명"?></td>
                                <td><?=($surfWCnt_rang_11 == 0) ? "" : $surfWCnt_rang_11."명"?></td>
                                <th><?=($surfMCnt_rang_11+$surfWCnt_rang_11 == 0) ? "" : $surfMCnt_rang_11+$surfWCnt_rang_11."명"?></th>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="vertical-align: top;">
                    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:100%;<?if($surflist_rang_13 == ""){ echo "display:none;"; }?>">
                        <colgroup>
                            <col width="*" />
                            <col width="35%" />
                            <col width="13%" />
                            <col width="13%" />
                            <col width="13%" />
                        </colgroup>
                        <tbody>
                            <tr>
                                <th>이름</th>
                                <th>연락처</th>
                                <th>남</th>
                                <th>여</th>
                                <th>메모</th>
                            </tr>
                            <?=$surflist_rang_13?>
                            <tr>
                                <th colspan="2">총 인원</th>
                                <td><?=($surfMCnt_rang_13 == 0) ? "" : $surfMCnt_rang_13."명"?></td>
                                <td><?=($surfWCnt_rang_13 == 0) ? "" : $surfWCnt_rang_13."명"?></td>
                                <th><?=($surfMCnt_rang_13+$surfWCnt_rang_13 == 0) ? "" : $surfMCnt_rang_13+$surfWCnt_rang_13."명"?></th>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="vertical-align: top;">
                    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:100%;<?if($surflist_rang_15 == ""){ echo "display:none;"; }?>">                        
                        <colgroup>
                            <col width="*" />
                            <col width="35%" />
                            <col width="13%" />
                            <col width="13%" />
                            <col width="13%" />
                        </colgroup>
                        <tbody>
                            <tr>
                                <th>이름</th>
                                <th>연락처</th>
                                <th>남</th>
                                <th>여</th>
                                <th>메모</th>
                            </tr>
                            <?=$surflist_rang_15?>
                            <tr>
                                <th colspan="2">총 인원</th>
                                <td><?=($surfMCnt_rang_15 == 0) ? "" : $surfMCnt_rang_15."명"?></td>
                                <td><?=($surfWCnt_rang_15 == 0) ? "" : $surfWCnt_rang_15."명"?></td>
                                <th><?=($surfMCnt_rang_15+$surfWCnt_rang_15 == 0) ? "" : $surfMCnt_rang_15+$surfWCnt_rang_15."명"?></th>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <table class="et_vars exForm bd_tb tbcenter" style="margin-top:10px;margin-bottom:1px;width:100%;display:<?=$tblala?>;" id="tblala">
    <colgroup>
            <col width="25%" />
            <col width="25%" />
            <col width="25%" />
            <col width="25%" />
        </colgroup>
        <tbody>
            <tr>
                <th colspan="4">라라서프</th>
            </tr>
            <tr>
                <th>9시 서핑강습</th>
                <th>11시 서핑강습</th>
                <th>13시 서핑강습</th>
                <th>15시 서핑강습</th>
            </tr>
            <tr>
                <td style="vertical-align: top;">
                    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:100%;<?if($surflist_lala_9 == ""){ echo "display:none;"; }?>">
                        <colgroup>
                            <col width="*" />
                            <col width="35%" />
                            <col width="13%" />
                            <col width="13%" />
                            <col width="13%" />
                        </colgroup>
                        <tbody>
                            <tr>
                                <th>이름</th>
                                <th>연락처</th>
                                <th>남</th>
                                <th>여</th>
                                <th>메모</th>
                            </tr>
                            <?=$surflist_lala_9?>
                            <tr>
                                <th colspan="2">총 인원</th>
                                <td><?=($surfMCnt_lala_9 == 0) ? "" : $surfMCnt_lala_9."명"?></td>
                                <td><?=($surfWCnt_lala_9 == 0) ? "" : $surfWCnt_lala_9."명"?></td>
                                <th><?=($surfMCnt_lala_9+$surfWCnt_lala_9 == 0) ? "" : $surfMCnt_lala_9+$surfWCnt_lala_9."명"?></th>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="vertical-align: top;">
                    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:100%;<?if($surflist_lala_11 == ""){ echo "display:none;"; }?>">
                        <colgroup>
                            <col width="*" />
                            <col width="35%" />
                            <col width="13%" />
                            <col width="13%" />
                            <col width="13%" />
                        </colgroup>
                        <tbody>
                            <tr>
                                <th>이름</th>
                                <th>연락처</th>
                                <th>남</th>
                                <th>여</th>
                                <th>메모</th>
                            </tr>
                            <?=$surflist_lala_11?>
                            <tr>
                                <th colspan="2">총 인원</th>
                                <td><?=($surfMCnt_lala_11 == 0) ? "" : $surfMCnt_lala_11."명"?></td>
                                <td><?=($surfWCnt_lala_11 == 0) ? "" : $surfWCnt_lala_11."명"?></td>
                                <th><?=($surfMCnt_lala_11+$surfWCnt_lala_11 == 0) ? "" : $surfMCnt_lala_11+$surfWCnt_lala_11."명"?></th>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="vertical-align: top;">
                    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:100%;<?if($surflist_lala_13 == ""){ echo "display:none;"; }?>">
                        <colgroup>
                            <col width="*" />
                            <col width="35%" />
                            <col width="13%" />
                            <col width="13%" />
                            <col width="13%" />
                        </colgroup>
                        <tbody>
                            <tr>
                                <th>이름</th>
                                <th>연락처</th>
                                <th>남</th>
                                <th>여</th>
                                <th>메모</th>
                            </tr>
                            <?=$surflist_lala_13?>
                            <tr>
                                <th colspan="2">총 인원</th>
                                <td><?=($surfMCnt_lala_13 == 0) ? "" : $surfMCnt_lala_13."명"?></td>
                                <td><?=($surfWCnt_lala_13 == 0) ? "" : $surfWCnt_lala_13."명"?></td>
                                <th><?=($surfMCnt_lala_13+$surfWCnt_lala_13 == 0) ? "" : $surfMCnt_lala_13+$surfWCnt_lala_13."명"?></th>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="vertical-align: top;">
                    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:100%;<?if($surflist_lala_15 == ""){ echo "display:none;"; }?>">                        
                        <colgroup>
                            <col width="*" />
                            <col width="35%" />
                            <col width="13%" />
                            <col width="13%" />
                            <col width="13%" />
                        </colgroup>
                        <tbody>
                            <tr>
                                <th>이름</th>
                                <th>연락처</th>
                                <th>남</th>
                                <th>여</th>
                                <th>메모</th>
                            </tr>
                            <?=$surflist_lala_15?>
                            <tr>
                                <th colspan="2">총 인원</th>
                                <td><?=($surfMCnt_lala_15 == 0) ? "" : $surfMCnt_lala_15."명"?></td>
                                <td><?=($surfWCnt_lala_15 == 0) ? "" : $surfWCnt_lala_15."명"?></td>
                                <th><?=($surfMCnt_lala_15+$surfWCnt_lala_15 == 0) ? "" : $surfMCnt_lala_15+$surfWCnt_lala_15."명"?></th>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>    
</form>

<script type="text/javascript">
$j(document).ready(function(){
	$j(".btn_view[seq]").mouseover(function(e){ //조회 버튼 마우스 오버시
		var seq = $j(this).attr("seq");
		var obj = $j(".btn_view[seq="+seq+"]");
		var tX = (obj.position().left)-254; //조회 버튼의 X 위치 - 레이어팝업의 크기만 큼 빼서 위치 조절
		var tY = (obj.position().top - 20);  //조회 버튼의 Y 위치
		

		if($j(this).find(".box_layer").length > 0){
			if($j(this).find(".box_layer").css("display") == "none"){
				$j(this).find(".box_layer").css({
					"top" : tY
					,"left" : tX
					,"position" : "absolute"
				}).show();
			}
		}else{
				$j(".btn_view[seq="+seq+"]").append('<div class="box_layer" style="width:240px;"></div>');
				$j(".btn_view[seq="+seq+"]").find(".box_layer").html($j(".btn_view[seq="+seq+"]").next().html());
				$j(".btn_view[seq="+seq+"]").find(".box_layer").css({
					"top" : tY
					,"left" : tX
					,"position" : "absolute"
				}).show();
		}		
	});
	
	$j(".btn_view[seq]").mouseout(function(e){
			$j(this).find(".box_layer").css("display","none");
	});				 
}); 
</script>