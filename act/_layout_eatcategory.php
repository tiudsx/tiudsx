<?
//해변 정보 가져오기
$select_query = "SELECT b.code, b.codename, b.uppercode, b.lat, b.lng, b.ordernum
                    FROM AT_CODE a INNER JOIN AT_CODE b
                        ON a.code = '$reqCode'
                            AND a.uppercode = b.uppercode
                            AND a.use_yn = 'Y'
                            AND b.use_yn = 'Y'
                        ORDER BY b.ordernum";
$result_setlist = mysqli_query($conn, $select_query);
$count = mysqli_num_rows($result_setlist);

while ($row = mysqli_fetch_assoc($result_setlist)){
    $areacodesel = $row["uppercode"];
    if($reqCode == $row["code"]){
        $pointFirsthtml = "<li><a href='/eatlist?code=".$row["code"]."'>".$row["codename"]." <i class='fas fa-caret-right'></i></a></li>";
    }else{

    }
    $pointhtml .= "<li><a href='/eatlist?code=".$row["code"]."'>".$row["codename"]."</a></li>";
}

//지역 정보 가져오기
$select_query = "SELECT a.code AS areacode, a.codename as areaname, b.code 
                    FROM `AT_CODE` a INNER JOIN `AT_CODE` b
                        ON a.uppercode = 'eat'
                            AND a.code = b.uppercode
                            AND b.ordernum = 1";
$result_setlist = mysqli_query($conn, $select_query);

while ($row = mysqli_fetch_assoc($result_setlist)){
    // $arealist .= "<li><a href='/eatlist?code=".$row["code"]."'>".$row["areaname"]."</a></li>";
    $arealist .= "<a style='float:left;padding-right:30px;' href='/eatlist?code=".$row["code"]."'>".$row["areaname"]."</a>";

    if($areacodesel == $row["areacode"]){
        $areasel = "<span class='btnContent'>".$row["areaname"]." <i class='fas fa-angle-down'></i></span>";
    }
}
?>

<script src="/act/js/surf.js"></script>
<link rel="stylesheet" href="/act/css/taste.css">

<section id="listMenu">
    <h2 class="hidden">액트립 서핑샵 카테고리</h2>
    <button type="button" class="btnArea">
        <?=$areasel?>
    </button>
    <ul class="listArea">
        <li>
            <?=$arealist?>
        </li>
    </ul>
    <ul class="listBeach">
        <?=$pointFirsthtml?>
        <?=$pointhtml?>
        <li><!--a href="#">+업데이트중</a--></li>
    </ul>
</section>