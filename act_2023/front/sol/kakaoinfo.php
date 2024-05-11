<style>    
    .tbcenter th{text-align:center;}
    .tbcenter td{text-align:center;}
</style>
<?php 
include __DIR__.'/../../common/db.php';
include __DIR__.'/../../common/func.php';

$couponseq = -1;

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

    $tablistALL = "N";
    $tablistBBQ = "N";
    $tablistPUB = "N";

    while ($row = mysqli_fetch_assoc($result)){
        if($i == 0){
            $user_name = $row["user_name"];
        }
        $i++;

        $res_room_chk = $row["res_room_chk"];
        $res_type = $row["res_type"];
        $prod_name = $row["prod_name"];

        $party = $row["party"];
        $surfrent = $row["surfrent"];

        if($res_type == "stay"){ //숙박&바베큐
            if($prod_name != "N"){ //숙소 신청
                $tablist1 = "게하";

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

            if($party == "ALL"){ //바베큐 신청
                $tablistALL = "Y"; //바베큐 신청
            }else if($party == "BBQ"){ //바베큐 신청
                $tablistBBQ = "Y"; //바베큐 신청
            }else if($party == "PUB"){ //바베큐 신청
                $tablistPUB = "Y"; //바베큐 신청
            }
        }else{ //강습&렌탈
            if($row['restime'] != ""){ //강습 신청
                $tablist3 = "강습";

                $arrSurf[$row['ressubseq']."0"] = "서핑강습|".$row['resdate']."|".$row['restime']."|".$row['surfM']."|".$row['surfW'];

            }
            if($surfrent != "N"){ //렌탈 신청
                if($row['restime'] != ""){
                    $tablist3 = "강습/렌탈";
                }else{
                    $tablist3 = "렌탈";
                }

                $arrSurf[$row['ressubseq']."1"] = $row['surfrent']." 렌탈|".$row['resdate']."||".$row['surfrentM']."|".$row['surfrentW'];
            }

            //서핑샵 배정
            $surfShopName = $row['prod_name'];
        }
    }
}

if($tablistALL == "N" && $tablistBBQ == "N" && $tablistPUB == "N"){
    $party = "N";
}else{
    $party = "Y";
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

        <div style="padding-left:15px;">
        <?if($tablist1 != ""){ //숙소 이용안내?>
            <p>✔ 입실은 <strong style="color:red;">셀프체크인</strong>이며, 이용안내 확인 후 입실하세요.</p>
            <p>✔ <strong style="color:red;">[게하 이용안내 보기]</strong>에서 배정된 객실 정보를 확인해주세요!!</p>
        <?}else{?>
            <p>✔ <strong style="color:red;">[이용안내 보기]</strong> 클릭 하셔서 자세한 내용 확인 후 이용해주세요~</p>
        <?}?>
        </div>

        <div id="btnList" style="text-align:center;">
        <?if($tablist1 != ""){ //숙소 이용안내?>
            <p><input type="button" class="btn_default" style="width:160px;" value="게하 이용안내 보기" onclick="fnSolInfo(this, 0);"></p>
        <?}?>

        <?if($party == "Y"){ //파티 이용안내?>
            <p><input type="button" class="btn_default" style="width:160px;" value="파티 이용안내 보기" onclick="fnSolInfo(this, 1);"></p>
        <?}?>

        <?if($tablist3 != ""){ //서핑 이용안내?>
            <p><input type="button" class="btn_default" style="width:160px;" value="서핑 이용안내 보기" onclick="fnSolInfo(this, 2);"></p>
        <?}?>
        </div>

        <?if($tablist1 != ""){ //숙소 이용안내?>
            <div name="tabinfo" style="display:none;">
            

        <div style="padding-left:5px;">
            <p class="restitle" style="font-size:22px;">게스트하우스 필독사항</p>
            <p style="padding-left:15px;">✔ <strong style="color:red;">[객실 조회]</strong> 버튼 클릭하시면, 배정된 객실 정보 확인 가능합니다.</p>
            <p style="padding-left:15px;">✔ 객실 조회는 당일 <strong style="color:red;">[오후 3시]</strong>  부터 조회 가능합니다.
            </p>
        </div>

        <?
        $layerCss = "none";
        $layerCss2 = "";
        if($res_room_chk == "N"){
            $layerCss = "";
            $layerCss2 = "none";
        }?>

        <div style="position: relative; padding: 10px 0px 10px 0px;">
            <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:80%;margin:auto;">
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

                <img src="/act_2023/images/alim/stay.jpg" class="placeholder">

            </div>
        <?}else{
            echo '<div name="tabinfo" style="display:none;"></div>';
        }

        if($party != "N"){ //바베큐 이용안내?> 
            <div name="tabinfo" style="display:none;">
                <div style="padding-left:5px;">
                    <p class="restitle" style="font-size:22px;">파티 이용시간</p>
                    <p style="padding-left:15px;">✔ 1차 바베큐 : 19:00 ~ 21:00 <strong style="color:red;">(18:50까지 오세요!!)</strong></p>
                    <p style="padding-left:15px;">✔ 2차 파티 : 21:30 ~ 24:00<br>
                    &nbsp;&nbsp;&nbsp;<span style="font-size:12px;color:red;">※ 2차 파티가 없을 경우 1층에서 유료술집으로 운영됩니다.</span></p>
                </div>
                
                <?if($tablistALL == "Y" || $tablistBBQ == "Y"){?>
                <img src="/act_2023/images/alim/bbq.jpg" class="placeholder">
                <?}?>
                
                <?if($tablistALL == "Y" || $tablistPUB == "Y"){?>
                <img src="/act_2023/images/alim/pub.jpg" class="placeholder">
                <!-- <img src="/act_2023/images/alim/pub2.jpg" class="placeholder"> -->
                <?}?>
            </div>
        <?}else{
            echo '<div name="tabinfo" style="display:none;"></div>';
        }

        if($tablist3 != "" || $tablist4 != ""){ //서핑강습 이용안내?> 
            <div name="tabinfo" style="display:none;">
                <div style="padding-left:5px;">
                    <p class="restitle" style="font-size:22px;">서핑강습, 렌탈 필독사항</p>
                    <p style="padding-left:15px;">✔ 서핑샵은 고객님 편의를 위해 <strong style="color:red;">제휴된 서핑샵</strong>으로 안내 해드립니다.</p>
                    <p style="padding-left:15px;">✔ 예약시간 <strong style="color:red;">최소 20분 전</strong> 안내장소로 모여주세요.</p>
                    <p style="padding-left:15px;">✔ 서핑샵으로 방문하셔서 예약자분 성함 말씀해주세요~</p>
                </div>
        
                <div style="padding-top:10px;">
                    <table class="et_vars exForm bd_tb tbcenter" style="margin-bottom:1px;width:80%;margin:auto;">
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
                </div>
                <!-- 서핑강습 안내 -->
                <div tabid="viewtab" id="view_tab4" style="min-height: 800px;display:<?=$Displaytab4?>;">
                    <?
                    if($surfShopName != "솔서프"){ //제휴 서핑샵 안내 문구
                    ?>
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
            </div>
        <?}else{
            echo '<div name="tabinfo" style="display:none;"></div>';
        }?>

        <div style="padding:10px 0 5px 0;font-size:12px;">
            <a href="http://pf.kakao.com/_tHqMG" target="_blank" rel="noopener"><img src="/act_2023/images/mainImg/kakaochat_sol.jpg" class="placeholder"></a>
        </div>
                
        <div id="banner" class="on">
            <img src="https://developers.kakao.com/assets/img/about/logos/kakaotalksharing/kakaotalk_sharing_btn_medium.png" onclick="shareMessage('<?=$resseq?>', '<?=$user_name?>');" alt="카카오톡 공유 보내기 버튼" style="width:50px;">
        </div>
    </div>
</div>


<? include __DIR__.'/../../_layout/_layout_bottom.php'; ?>

<script src="/act_2023/front/_js/sol.js?v=<?=time()?>"></script>

<style>
    .SolLayer {
        display: <?=$layerCss?>;
    }
    
    #banner {
        position: fixed;
        right: 20px;
        bottom: 50px;
        width: 50px;
        height: 50px;
    }

    #banner.on {
        position: absolute;
        bottom: 157px;
    }
</style>

<script src="https://t1.kakaocdn.net/kakao_js_sdk/2.2.0/kakao.min.js" integrity="sha384-x+WG2i7pOR+oWb6O5GV5f1KN2Ko6N7PTGPS7UlasYWNxZMKQA63Cj/B2lbUmUfuC" crossorigin="anonymous"></script>
<script>
	Kakao.init('6be635d23fc4c6ed539d4f425521c026'); // 사용하려는 앱의 JavaScript 키 입력
    var type = "";
    var btnheight = "";

	$j(function() {
		var $w = $j(window),
			footerHei = $j('.footer_Util_wrap00').outerHeight(),
			$banner = $j('#banner');

		$w.on('scroll', function() {
			var sT = $w.scrollTop();
			var val = $j(document).height() - $w.height() - footerHei;
			
			if (sT >= val)
				$banner.addClass('on')
			else
				$banner.removeClass('on')
		});
	});
</script>