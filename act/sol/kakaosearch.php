<? include __DIR__.'/../db.php'; ?>
<style>    
    .tbcenter th{text-align:center;}
    .tbcenter td{text-align:center;}
</style>
<?php 
include __DIR__.'/../common/func.php';

$resseq = $_REQUEST["seq"];
$chk = $_REQUEST["chk"];
if($resseq == ""){
    echo "<script>alert('예약된 정보가 없습니다.');location.href='https://actrip.co.kr';</script>";
    return;
}

mysqli_query($conn, "SET AUTOCOMMIT=0");
mysqli_query($conn, "BEGIN");

if($chk == 1){
    $resseq = trim($resseq);
}else{
    $resseq = trim(decrypt($resseq));
    
    $select_query = "UPDATE `AT_SOL_RES_MAIN` SET res_kakao_chk = 'Y' WHERE resseq = $resseq";
    $result_set = mysqli_query($conn, $select_query);
    mysqli_query($conn, "COMMIT");
}

$select_query = "SELECT * FROM AT_SOL_RES_MAIN as a INNER JOIN AT_SOL_RES_SUB as b 
                    ON a.resseq = b.resseq 
                    WHERE a.resseq = $resseq
                        ORDER BY b.ressubseq";
$result = mysqli_query($conn, $select_query);
$count_sub = mysqli_num_rows($result);
if($count_sub == 0){
    echo "<script>alert('예약된 정보가 없습니다.');location.href='https://actrip.co.kr';</script>";
    return;
}else{

    $i = 0;
    $stay_chk = 0;
    $surfbbq_chk = 0;
    $tablist = "";
    $arrStay = array();
    $arrBbq = array();
    $arrSurf = array();
    $arrRent = array();

    while ($row = mysqli_fetch_assoc($result)){
        if($i == 0){
            $user_name = $row["user_name"];
        }
        $i++;

        $res_room_chk = $row["res_room_chk"];
        $res_type = $row["res_type"];
        $prod_name = $row["prod_name"];

        $bbq = $row["bbq"];
        $surfrent = $row["surfrent"];

        $prod_name = $row["prod_name"];
        $prod_name = $row["prod_name"];
        $prod_name = $row["prod_name"];

        if($res_type == "stay"){ //숙박&바베큐
            if($prod_name != "N"){ //숙소 신청
                $staytext = "숙소,";
                $tablist1 = "<li tabid=\"viewli\" id=\"view_li2\" onclick=\"fnResViewSol(false, '#view_tab2', 70, this);\"><a>숙소</a></li>";

                if($row['stayroom'] == "201"){
                    $pw = "4437";
                }else if($row['stayroom'] == "202"){
                    $pw = "0009";
                }else if($row['stayroom'] == "203"){
                    $pw = "3308";
                }else if($row['stayroom'] == "204"){
                    $pw = "5080";
                }else if($row['stayroom'] == "301"){
                    $pw = "4437";
                }else if($row['stayroom'] == "302"){
                    $pw = "0009";
                }else if($row['stayroom'] == "303"){
                    $pw = "3308";
                }

                $arrStay[$row['ressubseq']] = $row['staysex']."|".$row['sdate']."|".$row['edate']."|".$row['stayroom']."|".$row['staynum']."|$pw";

                $stay_chk = 1;
            }

            if($bbq != "N"){ //바베큐 신청
                $bbqtext = "바베큐,";
                $tablist2 = "<li tabid=\"viewli\" id=\"view_li3\" onclick=\"fnResViewSol(false, '#view_tab3', 70, this);\"><a>바베큐</a></li>";

                $arrBbq[$row['ressubseq']] = $row['staysex']."|".$row['resdate']."|".$row['bbq'];

                $surfbbq_chk = 1;
            }
        }else{ //강습&렌탈
            if($row['restime'] != ""){ //강습 신청
                $surftext = "서핑강습,";
                $tablist3 = "<li tabid=\"viewli\" id=\"view_li4\" onclick=\"fnResViewSol(false, '#view_tab4', 70, this);\"><a>강습</a></li>";

                $arrSurf[$row['ressubseq']] = $row['prod_name']."|".$row['resdate']."|".$row['restime']."|".$row['surfM']."|".$row['surfW'];

                $surfbbq_chk = 1;
            }
            if($surfrent != "N"){ //렌탈 신청
                $renttext = "장비렌탈,";
                $tablist4 = "<li tabid=\"viewli\" id=\"view_li5\" onclick=\"fnResViewSol(false, '#view_tab5', 70, this);\"><a>렌탈</a></li>";

                $arrRent[$row['ressubseq']] = $row['prod_name']."|".$row['surfrent']."|".$row['surfrentM']."|".$row['surfrentW'];
            }
        }
    }

    $resText = $staytext.$bbqtext.$surftext.$renttext;
    $resText = substr($resText, 0, strlen($resText) - 1);
}
?>

<script>
$j(document).ready(function(){
});
</script>


<div id="wrap">
    <? include __DIR__.'/../_layout_top.php'; ?>

    <link rel="stylesheet" href="../css/surfview.css">

    <div class="top_area_zone bd">
        <section class="shoptitle">
            <div style="padding:6px;">
                <h1>솔.동해서핑점 예약안내</h1>
                <a class="reviewlink">
                    <span class="reviewcnt">[<?=$user_name?>] 님 예약상세 안내입니다.</span>
                </a>
                <!-- <div class="shopsubtitle"><?=$resText?></div> -->
            </div>
        </section>

        <section class="notice">
            <div class="vip-tabwrap">
                <div id="tabnavi" class="fixed1" style="top: 49px;">
                    <div class="vip-tabnavi">
                        <ul>
                            <?
                            $Displaytab4 = "none";
                            $Displaytab5 = "none";
                            
                            if($tablist1 == "" && $tablist2 == ""){
                                if($tablist3 != "" && $tablist4 != ""){
                                    echo "<li tabid=\"viewli\" id=\"view_li4\" onclick=\"fnResViewSol(false, '#view_tab4', 70, this);\"><a>강습 이용안내</a></li>";
                                    echo $tablist4;
                                    $Displaytab4 = "";
                                }elseif($tablist3 == ""){
                                    echo "<li tabid=\"viewli\" id=\"view_li4\" onclick=\"fnResViewSol(false, '#view_tab5, 70, this);\"><a>렌탈 이용안내</a></li>";
                                    $Displaytab5 = "";
                                }else{
                                    echo "<li tabid=\"viewli\" id=\"view_li4\" onclick=\"fnResViewSol(false, '#view_tab4', 70, this);\"><a>강습 이용안내</a></li>";
                                    $Displaytab4 = "";
                                }
                            }else{
                            ?>
                                <li tabid="viewli" id="view_li1" class="on" onclick="fnResViewSol(false, '#view_tab1', 70, this);"><a>예약안내</a></li>
                                <?=$tablist3?>
                                <?=$tablist4?>
                            <?}?>
                            <li tabid="viewli" id="view_li6" onclick="fnResViewSol(false, '#view_tab6', 80, this);"><a>리뷰이벤트</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div tabid="viewtab" id="view_tab1">
                <div class="contentimg">
                    <!-- <img src="https://actrip.co.kr/act/images/sol_kakao/sol_01.jpg?v=1" class="placeholder"> -->

                    <?if($tablist1 != ""){?>
                    <img src="https://actrip.cdn1.cafe24.com/sol_kakao/stay_01.jpg" class="placeholder">

                <?if($surfbbq_chk == 1 && $stay_chk == 1){?>
                    <!-- <center>
                    <p class="restitle">✔ 조식이용은 일요일 오전에만 제공됩니다.</p>
                    <p class="restitle">✔ 이용시 네이버 방문자 리뷰 작성 후 이용가능합니다.</p>
                    <p class="restitle">✔ 일행이 있을 경우 네이버에서 추가 예약, 리뷰 작성해주세요~</p>
                    </center>
                    <img src="https://actrip.co.kr/act/images/sol_kakao/stay_sol/04.jpg" class="placeholder"> -->
                <?}?>
                    <center>
                    <?
                    $layerCss = "none";
                    if($res_room_chk == "N"){
                        $layerCss = "";
                    ?>
                        
                    <?}else{?>
                        <!-- <p class="restitle">✔ 객실조회가 완료되었습니다.</p> -->
                    <p>배정된 객실은 <strong class="restitle">오후 4시</strong> 이후부터 입실 가능합니다.</p>
                    <?}?>
                    
                    <div style="position: relative; min-height:150px;">
                        <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:90%;">
                            <colgroup>
                                <col width="13%" />
                                <col width="*" />
                                <col width="17%" />
                                <col width="20%" />
                                <col width="19%" />
                            </colgroup>
                            <tbody id="tbStay">
                                <tr>
                                    <th>성별</th>
                                    <th>이용일</th>
                                    <th>호실</th>
                                    <th>침배번호</th>
                                    <th>비밀번호</th>
                                </tr>
                            
                            <?
                            foreach ($arrStay as $key => $value) {
                                $arrVlu = explode("|", $value);

                                if($res_room_chk == "N"){
                                    $arrVlu[3] = "";
                                    $arrVlu[4] = "";
                                    $arrVlu[5] = "";
                                }else{
                                    $arrVlu[3] = substr($arrVlu[3], 0, 1)."층 ".substr($arrVlu[3], 2, 1)."호";
                                    $arrVlu[4] = $arrVlu[4]."번 침대";
                                }
                            ?>
                            
                            <tr trid="stay">
                                <td><?=$arrVlu[0]?></td>
                                <td><?=$arrVlu[1]?> ~ <br><?=$arrVlu[2]?></td>
                                <td><?=$arrVlu[3]?></td>
                                <td><?=$arrVlu[4]?></td>
                                <td><?=$arrVlu[5]?></td>
                            </tr>

                            <?
                            }
                            ?>
                            </tbody>
                        </table>

                        <div class="SolLayer">
                            <div class="box">
                                <div class="in">
                                    <strong>&nbsp;</strong>
                                    <p class="restitle">✔ 이용당일 오후 2시 부터 조회 가능합니다.</p>
                                    <a class="SolLayer_btn" onclick="fnStaySearch(<?=$resseq?>);">객실 조회하기</a>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    </center>
                    <img src="https://actrip.cdn1.cafe24.com/sol_kakao/stay_02.jpg" class="placeholder">
                    
                    <img src="https://actrip.cdn1.cafe24.com/sol_kakao/stay_03.jpg" class="placeholder">
                    <?}?>
                    
                    <?if($tablist2 != ""){?>
                    <center>
                    바베큐 예약정보
                    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:80%;">
                        <tr>
                            <th>성별</th>
                            <th>이용일</th>
                            <th></th>
                        </tr>
                        <?
                        foreach ($arrBbq as $key => $value) {
                            $arrVlu = explode("|", $value);
                        ?>
                        
                        <tr>
                            <td><?=$arrVlu[0]?></td>
                            <td><?=$arrVlu[1]?></td>
                            <td><?=$arrVlu[2]?></td>
                        </tr>

                        <?
                        }
                        ?>
                    </table>
                    </center>
                    <img src="https://actrip.cdn1.cafe24.com/sol_kakao/bbq_01.jpg" class="placeholder">
                    <img src="https://actrip.cdn1.cafe24.com/sol_kakao/bbq_02.jpg" class="placeholder">
                    <?}?>

                    <?if($tablist3 != ""){ //서핑강습 이용안내?> 
                    <!-- <img src="https://actrip.co.kr/act/images/sol_kakao/sol_03.jpg" class="placeholder"> -->
                    <?}?>
                </div>
            </div>
            <div class="SolNoticeLayer">
                <div class="box">
                    <div class="in">
                        <?if($tablist1 != ""){ //숙소 이용안내?> 
                        <p class="restitle" style="font-size:22px;">숙소 이용안내</p>
                        <p class="info">✔ 입실은 셀프체크인이며, 안내사항 확인 후 입실하세요.</p>
                        <p class="info">✔ 배정된 객실은 [객실 조회] 후 확인 가능합니다.</p>
                        <?}
                        if($tablist2 != ""){ //바베큐 이용안내?> 
                        <p class="restitle" style="font-size:22px;">바베큐 이용안내</p>
                        <p class="info">✔ 이용시간 : 19:00 ~ 21:30</p>
                        <p class="info">✔ 18:50까지 1층 데크에서 대기해주세요.</p>
                        <?}
                        if($tablist3 != ""){ //서핑강습 이용안내?> 
                        <p class="restitle" style="font-size:22px;">서핑강습 이용안내</p>
                        <p class="info">✔ 강습예약시간 최소 10분 전 강습장소로 모여주세요.</p>
                        <p class="info">✔ [강습] 탭에 있는 안내사항을 확인하세요.</p>
                        <?}
                        if($tablist4 != ""){ //렌탈 이용안내?> 
                        <p class="restitle" style="font-size:22px;">서핑렌탈 이용안내</p>
                        <p class="info">✔ [렌탈] 탭에 있는 안내사항을 확인하세요.</p>
                        <?}?>

                        <p class="restitle" style="font-size:22px;">네이버예약 리뷰 이벤트</p>
                        <p class="info">✔ 리뷰 남겨주시면 즉석라면 또는 커피(택 1) 드려요~</p>

                        <a class="SolLayer_btn" onclick="$j('.SolNoticeLayer').css('display', 'none');">확인</a>
                        
                    </div>
                </div>
            </div>
            <!-- 숙소 안내 -->
            <div tabid="viewtab" id="view_tab2" style="display: none;min-height: 800px;"> 
                <img src="https://actrip.co.kr/act/images/sol_kakao/stay_sol/02.jpg" class="placeholder">
                <img src="https://actrip.co.kr/act/images/sol_kakao/stay_sol/07.jpg" class="placeholder">
                <img src="https://actrip.co.kr/act/images/sol_kakao/stay_sol/03.jpg" class="placeholder">

                <?if($surfbbq_chk == 1 && $stay_chk == 1){?>
                    <center>
                    <p class="restitle">✔ 조식이용은 일요일 오전에만 제공됩니다.</p>
                    <p class="restitle">✔ 이용시 네이버 방문자 리뷰 작성 후 이용가능합니다.</p>
                    <p class="restitle">✔ 일행이 있을 경우 네이버에서 추가 예약, 리뷰 작성해주세요~</p>
                    </center>
                    <img src="https://actrip.co.kr/act/images/sol_kakao/stay_sol/04.jpg" class="placeholder">
                <?}?>

                <img src="https://actrip.co.kr/act/images/sol_kakao/stay_sol/06.jpg" class="placeholder">
                
            </div>

            <!-- 바베큐 안내 -->
            <div tabid="viewtab" id="view_tab3" style="min-height: 800px;display:none;">
                <img src="https://actrip.co.kr/act/images/sol_kakao/bbq/02.jpg" class="placeholder">
                <br>
                <center>
                <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:80%;">
                    <tr>
                        <th>성별</th>
                        <th>이용일</th>
                        <th></th>
                    </tr>
                    <?
                    foreach ($arrBbq as $key => $value) {
                        $arrVlu = explode("|", $value);
                    ?>
                    
                    <tr>
                        <td><?=$arrVlu[0]?></td>
                        <td><?=$arrVlu[1]?></td>
                        <td><?=$arrVlu[2]?></td>
                    </tr>

                    <?
                    }
                    ?>
                </table>
                </center>
                <br>
                <img src="https://actrip.co.kr/act/images/sol_kakao/bbq/03.jpg" class="placeholder">
                <img src="https://actrip.co.kr/act/images/sol_kakao/bbq/04.jpg" class="placeholder">
            </div>

            <!-- 서핑강습 안내 -->
            <div tabid="viewtab" id="view_tab4" style="min-height: 800px;display:<?=$Displaytab4?>;">
                <?
                foreach ($arrSurf as $key => $value) {
                    $arrVlu = explode("|", $value);
                    $shopname = $arrVlu[0];

                    if($arrVlu[3] == 0){
                        $arrVlu[3] = "";
                    }

                    if($arrVlu[4] == 0){
                        $arrVlu[4] = "";
                    }
                }
                if($arrVlu[0] != "솔서프"){
                ?>
                <img src="https://actrip.cdn1.cafe24.com/sol_kakao/surf_00.jpg" class="placeholder">
                <?
                }
                if($arrVlu[0] == "서프팩토리"){
                ?>
                <img src="https://actrip.cdn1.cafe24.com/sol_kakao/surf_2_01.jpg" class="placeholder">
                <?
                }else if($arrVlu[0] == "서퍼랑"){
                ?>
                <img src="https://actrip.cdn1.cafe24.com/sol_kakao/surf_1_01.jpg" class="placeholder">
                <?
                }else if($arrVlu[0] == "라라서프"){
                ?>
                <img src="https://actrip.cdn1.cafe24.com/sol_kakao/surf_3_01.jpg" class="placeholder">
                <?
                }else if($arrVlu[0] == "솔서프"){
                ?>
                <img src="https://actrip.cdn1.cafe24.com/sol_kakao/surf_4_01.jpg" class="placeholder">
                <?
                }
                ?>
                <br>
                <center>
                <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:80%;">
                    <tr>
                        <th>서핑샵</th>
                        <th>이용일</th>
                        <th>강습시간</th>
                        <th>남</th>
                        <th>여</th>
                    </tr>
                    <?
                    foreach ($arrSurf as $key => $value) {
                        $arrVlu = explode("|", $value);
                        $shopname = $arrVlu[0];

                        if($arrVlu[3] == 0){
                            $arrVlu[3] = "";
                        }

                        if($arrVlu[4] == 0){
                            $arrVlu[4] = "";
                        }
                    ?>
                    
                    <tr>
                        <td><?=$arrVlu[0]?></td>
                        <td><?=$arrVlu[1]?></td>
                        <td><?=$arrVlu[2]?></td>
                        <td><?=$arrVlu[3]?></td>
                        <td><?=$arrVlu[4]?></td>
                    </tr>

                    <?
                    }
                    ?>
                </table>
                </center>
                <br>
                <?
                if($arrVlu[0] == "서프팩토리"){
                ?>
                <img src="https://actrip.co.kr/act/images/sol_kakao/surf/06.jpg" class="placeholder">
                <img src="https://actrip.co.kr/act/images/sol_kakao/surf/05.jpg" class="placeholder"> 
                <img src="https://actrip.co.kr/act/images/sol_kakao/surf/surffactory_04.jpg" class="placeholder">
                <?
                }else if($arrVlu[0] == "서퍼랑"){
                ?>
                <img src="https://actrip.cdn1.cafe24.com/sol_kakao/surf_1_02.jpg" class="placeholder">
                <img src="https://actrip.co.kr/act/images/sol_kakao/surf/05.jpg" class="placeholder"> 
                <img src="https://actrip.co.kr/act/images/sol_kakao/surf/surfrang_04.jpg" class="placeholder">
                <?
                }else if($arrVlu[0] == "라라서프"){
                ?>
                <img src="https://actrip.co.kr/act/images/sol_kakao/surf/06.jpg" class="placeholder">
                <img src="https://actrip.co.kr/act/images/sol_kakao/surf/05.jpg" class="placeholder"> 
                <img src="https://actrip.cdn1.cafe24.com/sol_kakao/lalasurf_04.jpg" class="placeholder">
                <?
                }else if($arrVlu[0] == "솔서프"){
                ?>
                <img src="https://actrip.co.kr/act/images/sol_kakao/surf/06.jpg" class="placeholder">
                <img src="https://actrip.co.kr/act/images/sol_kakao/surf/05.jpg" class="placeholder"> 
                <?
                }
                ?>
                
                <img src="https://actrip.cdn1.cafe24.com/sol_kakao/surf_01.jpg" class="placeholder">
            </div>

            <!-- 장비렌탈 안내 -->
            <div tabid="viewtab" id="view_tab5" style="min-height: 800px;display:<?=$Displaytab5?>;"><?
                foreach ($arrRent as $key => $value) {
                    $arrVlu = explode("|", $value);
                    $shopname = $arrVlu[0];

                    if($arrVlu[2] == 0){
                        $arrVlu[2] = "";
                    }

                    if($arrVlu[3] == 0){
                        $arrVlu[3] = "";
                    }
                }
                if($arrVlu[0] != "솔서프"){
                ?>
                <img src="https://actrip.cdn1.cafe24.com/sol_kakao/surf_00.jpg" class="placeholder">
                <?
                }
                if($arrVlu[0] == "서프팩토리"){
                ?>
                <img src="https://actrip.cdn1.cafe24.com/sol_kakao/surf_2_01.jpg" class="placeholder">
                <?
                }else if($arrVlu[0] == "서퍼랑"){
                ?>
                <img src="https://actrip.cdn1.cafe24.com/sol_kakao/surf_1_01.jpg" class="placeholder">
                <?
                }else if($arrVlu[0] == "라라서프"){
                ?>
                <img src="https://actrip.cdn1.cafe24.com/sol_kakao/surf_1_01.jpg" class="placeholder">
                <?
                }else if($arrVlu[0] == "솔서프"){
                ?>
                <img src="https://actrip.cdn1.cafe24.com/sol_kakao/surf_1_01.jpg" class="placeholder">
                <?
                }
                ?>
                <br>
                <center>

                <div>
                    <p class="restitle">✔ 배정된 샵으로 방문하셔서 성함 말씀하시면 렌탈 가능합니다.</p>
                </div>
                <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:80%;">
                    <tr>
                        <th>렌탈종류</th>
                        <th>남</th>
                        <th>여</th>
                    </tr>
                    <?
                    foreach ($arrRent as $key => $value) {
                        $arrVlu = explode("|", $value);

                        if($arrVlu[2] == 0){
                            $arrVlu[2] = "";
                        }
    
                        if($arrVlu[3] == 0){
                            $arrVlu[3] = "";
                        }
                    ?>
                    
                    <tr>
                        <td><?=$arrVlu[1]?></td>
                        <td><?=$arrVlu[2]?></td>
                        <td><?=$arrVlu[3]?></td>
                    </tr>

                    <?
                    }
                    ?>
                </table>
                </center>
                <br>
                <?
                if($arrVlu[0] == "서프팩토리"){
                ?>
                <img src="https://actrip.co.kr/act/images/sol_kakao/surf/06.jpg" class="placeholder">
                <img src="https://actrip.co.kr/act/images/sol_kakao/surf/surffactory_04.jpg" class="placeholder">
                <?
                }else if($arrVlu[0] == "서퍼랑"){
                ?>
                <img src="https://actrip.cdn1.cafe24.com/sol_kakao/surf_1_02.jpg" class="placeholder">
                <img src="https://actrip.co.kr/act/images/sol_kakao/surf/surfrang_04.jpg" class="placeholder">
                <?
                }else if($arrVlu[0] == "라라서프"){
                ?>
                <img src="https://actrip.co.kr/act/images/sol_kakao/surf/06.jpg" class="placeholder">
                <img src="https://actrip.co.kr/act/images/sol_kakao/surf/surfrang_04.jpg" class="placeholder">
                <?
                }else if($arrVlu[0] == "솔서프"){
                ?>
                <img src="https://actrip.co.kr/act/images/sol_kakao/surf/06.jpg" class="placeholder">
                <img src="https://actrip.co.kr/act/images/sol_kakao/surf/surfrang_04.jpg" class="placeholder">
                <?
                }
                ?>
            </div>

            <!-- 리뷰이벤트 안내 -->
            <div tabid="viewtab" id="view_tab6" style="min-height: 800px;display:none;">
                <img src="https://actrip.cdn1.cafe24.com/sol_kakao/event_review.jpg" class="placeholder">
            </div>

            <div style="padding:10px 0 5px 0;font-size:12px;">
                <img src="https://actrip.cdn1.cafe24.com/sol_kakao/sol_02.jpg" class="placeholder">
                <a href="http://pf.kakao.com/_HxmtMxl" target="_blank" rel="noopener"><img src="https://actrip.cdn1.cafe24.com/common/kakaochat.jpg" class="placeholder"></a>
            </div>
        </section>
    </div>
</div>

<? include __DIR__.'/../_layout_bottom.php'; ?>

<script src="../js/surfview.js"></script>

<style>
    .SolLayer {
        display: <?=$layerCss?>;
    }
</style>