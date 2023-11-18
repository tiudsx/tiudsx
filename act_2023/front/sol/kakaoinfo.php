<style>    
    .tbcenter th{text-align:center;}
    .tbcenter td{text-align:center;}
</style>
<?php 
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/func.php';

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
    $tablist = "";
    $arrStay = array();
    $arrSurf = array();
    $surfShopName = "";

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

        if($res_type == "stay"){ //숙박&바베큐
            if($prod_name != "N"){ //숙소 신청
                $tablist1 = "<li tabid=\"viewli\" id=\"view_li2\" onclick=\"fnResViewSol(false, '#view_tab2', 80, this);\"><a>숙소</a></li>";

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
            }

            if($bbq != "N"){ //바베큐 신청
                $tablist2 = "<li tabid=\"viewli\" id=\"view_li3\" onclick=\"fnResViewSol(false, '#view_tab3', 70, this);\"><a>바베큐</a></li>";
            }
        }else{ //강습&렌탈
            if($row['restime'] != ""){ //강습 신청
                $tablist3 = "<li tabid=\"viewli\" id=\"view_li4\" onclick=\"fnResViewSol(false, '#view_tab4', 80, this);\"><a>강습</a></li>";

                $arrSurf[$row['ressubseq']."0"] = "서핑강습|".$row['resdate']."|".$row['restime']."|".$row['surfM']."|".$row['surfW'];

            }
            if($surfrent != "N"){ //렌탈 신청
                if($row['restime'] != ""){
                    $tablist3 = "<li tabid=\"viewli\" id=\"view_li4\" onclick=\"fnResViewSol(false, '#view_tab4', 80, this);\"><a>강습/렌탈</a></li>";
                }else{
                    $tablist3 = "<li tabid=\"viewli\" id=\"view_li4\" onclick=\"fnResViewSol(false, '#view_tab4', 80, this);\"><a>렌탈</a></li>";
                }

                $arrSurf[$row['ressubseq']."1"] = $row['surfrent']." 렌탈|".$row['resdate']."||".$row['surfrentM']."|".$row['surfrentW'];
            }

            //서핑샵 배정
            $surfShopName = $row['prod_name'];
        }
    }
}
?>

<div id="wrap">
    <? include __DIR__.'/../../_layout/_layout_top.php'; ?>

    <link rel="stylesheet" href="/act_2023/front/_css/surfview.css">

    <div class="top_area_zone bd">
        <section class="shoptitle">
            <div style="padding:6px;">
                <h1>솔게하&솔서프 예약안내</h1>
                <a class="reviewlink">
                    <span class="reviewcnt">[<?=$user_name?>] 님 예약내역 안내입니다.</span>
                </a>
            </div>
        </section>

        <section class="notice">
            <div class="vip-tabwrap">
                <div id="tabnavi" class="fixed1" style="top: 49px;">
                    <div class="vip-tabnavi">
                        <ul>
                            <?
                            $Displaytab4 = "none";
                            
                            if($tablist1 == "" && $tablist2 == ""){
                                echo str_replace("tabid", "class='on' tabid", $tablist3);
                                $Displaytab4 = "";
                            }else{
                            ?>
                                <li tabid="viewli" id="view_li1" class="on" onclick="fnResViewSol(false, '#view_tab1', 80, this);"><a>예약안내</a></li>
                                <?=$tablist3?>
                            <?}?>
                            <li tabid="viewli" id="view_li6" onclick="fnResViewSol(false, '#view_tab6', 80, this);"><a>리뷰</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div tabid="viewtab" id="view_tab1">
                <div class="contentimg">
                    <?if($tablist1 != ""){?>
                    <center>
                    <!-- <p class="restitle">✔ 객실조회가 완료되었습니다.</p> -->
                    
                    <?
                    $layerCss = "none";
                    $layerCss2 = "";
                    if($res_room_chk == "N"){
                        $layerCss = "";
                        $layerCss2 = "none";
                    ?>
                    <p id="staysearch">✔ 객실 조회는 당일 <strong class="restitle">오후 3시</strong>  부터 조회 가능합니다.<br><strong style="color:red;">[객실 조회하기]</strong>  버튼을 클릭해주세요.</p>
                    <?}else{?>
                    <?}?>
                    
                    <p id="staysearch2" style="display:<?=$layerCss2?>;">✔ 객실조회가 완료되었습니다.<br><strong style="color:red;">호실,침대번호,도어락 비밀번호</strong> 확인 후 입실해주세요~</p>
                    <div style="position: relative; padding: 0px 0px 20px 0px;">
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
                                    <a class="SolLayer_btn" onclick="fnStaySearch(<?=$resseq?>);">객실 조회하기</a>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    </center>
                    <img src="/act_2023/images/alim/stay.jpg" class="placeholder">
                    <?}?>
                    
                    <?if($tablist2 != ""){?>
                    <img src="/act_2023/images/alim/bbq.jpg" class="placeholder">
                    <?}?>
                </div>
            </div>
            <div class="SolNoticeLayer">
                <div class="box">
                    <div class="in">
                        <?if($tablist1 != ""){ //숙소 이용안내?> 
                        <p class="restitle" style="font-size:22px;">솔게하&솔서프 이용안내</p>
                        <p class="info">✔ 입실은 셀프체크인이며, 안내사항 확인 후 입실하세요.</p>
                        <p class="info">✔ 객실 안내는 [객실 조회] 버튼 클릭 후 확인 가능합니다.</p>
                        <?}
                        if($tablist2 != ""){ //바베큐 이용안내?> 
                        <p class="restitle" style="font-size:22px;">바베큐 이용안내</p>
                        <p class="info">✔ 이용시간 : 18:50 ~ 21:30</p>
                        <p class="info">✔ 18:50까지 지하 1층으로 오세요~</p>
                        <?}
                        if($tablist3 != "" || $tablist4 != ""){ //서핑강습 이용안내?> 
                        <p class="restitle" style="font-size:22px;">서핑예약 이용안내</p>
                        <p class="info">✔ 예약시간 최소 20분 전 안내장소로 모여주세요.</p>
                        <p class="info">✔ 자세한 내용은 [강습] 메뉴를 확인하세요.</p>
                        <?}?> 

                        <a class="SolLayer_btn" onclick="$j('.SolNoticeLayer').css('display', 'none');">확인</a>
                        
                    </div>
                </div>
            </div>

            <!-- 서핑강습 안내 -->
            <div tabid="viewtab" id="view_tab4" style="min-height: 800px;display:<?=$Displaytab4?>;">
                <?
                if($surfShopName != "솔서프"){ //제휴 서핑샵 안내 문구
                ?>
                <img src="https://actrip.cdn1.cafe24.com/sol_kakao/surf_00.jpg" class="placeholder">
                <?
                }
                if($surfShopName == "서프팩토리"){
                ?>
                <img src="https://actrip.cdn1.cafe24.com/sol_kakao/surf_2_01.jpg" class="placeholder">
                <?
                }else if($surfShopName == "서퍼랑"){
                ?>
                <img src="https://actrip.cdn1.cafe24.com/sol_kakao/surf_1_01.jpg" class="placeholder">
                <?
                }else if($surfShopName == "솔서프"){
                ?>
                <img src="https://actrip.cdn1.cafe24.com/sol_kakao/surf_4_01.jpg" class="placeholder">
                <?
                }
                ?>
                <br>
                <center>

                <div>
                    <p class="restitle">✔ 서핑샵으로 방문하셔서 예약자분 성함<br>말씀하시면 이용 가능합니다.</p>
                </div>

                <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:85%;">
                    <tr>
                        <th>예약종류</th>
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
                        }else{
                            $arrVlu[3] .= "명";
                        }

                        if($arrVlu[4] == 0){
                            $arrVlu[4] = "";
                        }else{
                            $arrVlu[4] .= "명";
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
                if($surfShopName == "서프팩토리"){
                ?>
                <img src="/act_2023/images/alim/surffactory.jpg" class="placeholder">
                <?
                }else if($surfShopName == "서퍼랑"){
                ?>
                <img src="/act_2023/images/alim/surferrang.jpg" class="placeholder">
                <?
                }else if($surfShopName == "솔서프"){
                ?>
                <img src="/act_2023/images/alim/solsurf.jpg" class="placeholder">
                <?
                }
                ?>
            </div>


            <!-- 리뷰이벤트 안내 -->
            <div tabid="viewtab" id="view_tab6" style="min-height: 600px;display:none;">
                <!-- <img src="/act_2023/images/alim/event_sol_review.jpg" class="placeholder"> -->
                <img src="/act_2023/images/alim/review.jpg" class="placeholder">
            </div>

            <div style="padding:10px 0 5px 0;font-size:12px;">
            <a href="http://pf.kakao.com/_tHqMG" target="_blank" rel="noopener"><img src="/act_2023/images/mainImg/kakaochat_sol.jpg" class="placeholder"></a>
            </div>
        </section>
    </div>
</div>

<? include __DIR__.'/../../_layout/_layout_bottom.php'; ?>

<script src="/act_2023/front/_js/common.js?v=<?=time()?>"></script>
<script src="/act_2023/front/_js/sol.js?v=<?=time()?>"></script>

<style>
    .SolLayer {
        display: <?=$layerCss?>;
    }
</style>