<?php
set_time_limit(0); 

include __DIR__.'/../db.php';

$img_name = $_FILES['filename']['name'];
$tmp_file = $_FILES['filename']['tmp_name']; //서버에 임시로 저장된 파일 경로

move_uploaded_file($tmp_file,dirname(__FILE__)."/".$img_name);//파일을 실제로 서버에 업로드 하는 php 함수

require_once 'reader.php';

$xlsx = new XLSXReader( $img_name );
$sheetNames = $xlsx->getSheetNames();

$sheet = $xlsx->getSheet($sheetNames[1]);
$data = $sheet->getData();

$groupData = array();
$i = 0;

mysqli_query($conn, "SET AUTOCOMMIT=0");
mysqli_query($conn, "BEGIN");

foreach($data as $key => $row) {
    // 첫번째 행은 제외할거임
    if($key == 0)
        continue;

    // //기존 회원번호 유무 체크하여 있으면 update 없으면 insert
    // $select_query = "SELECT * FROM AT_PROD_MAIN WHERE shopname = '".$row[0]."'";
    // $result = mysqli_query($conn, $select_query);
    // $count = mysqli_num_rows($result);
    
    // if($count == 0){
    $selData = array();
    //echo $key . " : " . $row[0] .'/' .$row[1]."<br/>";
    if($row[1] == "eat"){
        $selData[] = array("입력여부" => $row[0]
            , "code" => $row[1]
            , "샵이름" => $row[2]
            , "구분" => $row[3]
            , "카테고리명" => $row[4]
            , "카테고리 코드" => $row[5]
            , "대표번호" => $row[6]
            , "샵주소" => $row[7]
            , "위도" => $row[8]
            , "경도" => $row[9]
            , "대표메뉴" => $row[10]
            , "리스트 문구" => $row[11]
            , "제휴서비스" => $row[12]
            , "가격" => $row[13]
            , "썸네일" => $row[14]
            , "기타" => $row[15]);

        if($row[0] != "P"){
            // 기존데이터 삭제
            $select_query = "DELETE FROM AT_PROD_MAIN WHERE code = '$row[1]' AND shopname = '$row[2]'";
            $result_set = mysqli_query($conn, $select_query);
            if(!$result_set) goto errGo;

            $select_query = "INSERT INTO `AT_PROD_MAIN` (`code`, `bankseq`, `shopcharge`, `account_yn`, `category`, `categoryname`, `shopname`, `tel_kakao`, `tel_admin`, `shopaddr`, `shoplat`, `shoplng`, `img_ver`, `sub_title`, `sub_tag`, `shop_option`, `sub_info`, `shop_resinfo`, `shop_img`, `content_type`, `content`, `lesson_yn`, `rent_yn`, `bbq_yn`, `use_yn`, `view_yn`, `link_url`, `link_yn`, `sell_cnt`, `insuserid`, `insdate`, `upduserid`, `upddate`) VALUES
            ('$row[1]', 0, 0, 'N', '$row[5]', '$row[4]', '$row[2]', '', '$row[6]', '$row[7]', '$row[8]', '$row[9]', 0, '$row[10]', '$row[3]', '', '', '', 'https://surfenjoy.cdn3.cafe24.com/eat/thumfood.jpg', 'html', '', 'N', 'N', 'N', 'Y', 'Y', '', 'N', 0, 'admin', now(), 'admin', now());";
            //echo $select_query;
            $result_set = mysqli_query($conn, $select_query);
            if(!$result_set) goto errGo;

            $i++;
        }
    }else if($row[1] == "stay"){
        $selData[] = array("입력여부" => $row[0]
            , "code" => $row[1]
            , "구분" => $row[2]
            , "샵이름" => $row[3]
            , "카테고리명" => $row[4]
            , "카테고리 코드" => $row[5]
            , "대표번호" => $row[6]
            , "샵주소" => $row[7]
            , "위도" => $row[8]
            , "경도" => $row[9]
            , "비수기" => $row[10]
            , "준성수기" => $row[11]
            , "성수기" => $row[12]);

        if($row[0] != "P"){
            // 기존데이터 삭제
            $select_query = "DELETE FROM AT_PROD_MAIN WHERE code = '$row[1]' AND shopname = '$row[3]'";
            $result_set = mysqli_query($conn, $select_query);
            if(!$result_set) goto errGo;

            $sub_tag = "";
            if($row[10] != ""){
                $sub_tag .= "비수기|$row[10]";
            }
            if($row[11] != ""){
                $sub_tag .= "@준성수기|$row[11]";
            }
            if($row[12] != ""){
                $sub_tag .= "@성수기|$row[12]";
            }

            if($sub_tag == ""){
                $sub_tag = "|가격은 연락처로 문의해주세요.";
            }

            $select_query = "INSERT INTO `AT_PROD_MAIN` (`code`, `bankseq`, `shopcharge`, `account_yn`, `category`, `categoryname`, `shopname`, `tel_kakao`, `tel_admin`, `shopaddr`, `shoplat`, `shoplng`, `img_ver`, `sub_title`, `sub_tag`, `shop_option`, `sub_info`, `shop_resinfo`, `shop_img`, `content_type`, `content`, `lesson_yn`, `rent_yn`, `bbq_yn`, `use_yn`, `view_yn`, `link_url`, `link_yn`, `sell_cnt`, `insuserid`, `insdate`, `upduserid`, `upddate`) VALUES
            ('$row[1]', 0, 0, 'N', '$row[5]', '$row[4]', '$row[3]', '', '$row[6]', '$row[7]', '$row[8]', '$row[9]', 0, '$row[2]', '$sub_tag', '', '', '', '', 'html', '', 'N', 'N', 'N', 'Y', 'Y', '', 'N', 0, 'admin', now(), 'admin', now());";
            //echo $select_query;
            $result_set = mysqli_query($conn, $select_query);
            if(!$result_set) goto errGo;

            $i++;
        }
    }else if($row[1] == "surf"){
        $selData[] = array("입력여부" => $row[0]
            , "code" => $row[1]
            , "샵이름" => $row[2]
            , "수수료" => $row[3]
            , "정산여부" => $row[4]
            , "카테고리명" => $row[5]
            , "카테고리코드" => $row[6]
            , "알림톡번호" => $row[7]
            , "대표번호" => $row[8]
            , "샵주소" => $row[9]
            , "위도" => $row[10]
            , "경도" => $row[11]
            , "imgver" => $row[12]
            , "리스트문구1" => $row[13]
            , "리스트문구2" => $row[14]
            , "이벤트" => $row[15]
            , "리스트 강습" => $row[16]
            , "리스트 렌탈" => $row[17]
            , "리스트 숙박" => $row[18]
            , "리스트 썸네일" => $row[19]
            , "상세 슬라이드1" => $row[20]
            , "상세 슬라이드2" => $row[21]
            , "상세 슬라이드3" => $row[22]
            , "상세 슬라이드4" => $row[23]
            , "상세type" => $row[24]
            , "상세내용" => $row[25]
            , "강습여부" => $row[26]
            , "렌탈여부" => $row[27]
            , "바베큐여부" => $row[28]
            , "사용여부" => $row[29]
            , "리스트표시" => $row[30]
            , "링크여부" => $row[31]
            , "링크url" => $row[32]
            , "구매개수" => $row[33]
            , "삽계좌은행" => $row[34]
            , "샵계좌번호" => $row[35]
            , "삽예금주" => $row[36]
            , "비수기 운영일" => $row[37]
            , "비수기 금액" => $row[38]
            , "성수기 운영일" => $row[39]
            , "성수기 금액" => $row[40]
            , "성수기2 운영일" => $row[41]
            , "성수기2 금액" => $row[42]
            , "비수기2 운영일" => $row[43]
            , "비수기2 금액" => $row[44]
            , "강습시간" => $row[45]
            , "강습가격" => $row[46]
            , "강습+숙박" => $row[47]
            , "강습+2박" => $row[48]
            , "스펀지" => $row[49]
            , "에폭시" => $row[50]
            , "서핑슈트" => $row[51]
            , "바베큐파티" => $row[52]
            , "샵폴더" => $row[53]);

        if($row[0] == "i"){
            // 기존데이터 삭제
            $select_query = "DELETE FROM AT_PROD_MAIN WHERE code = '$row[1]' AND shopname = '$row[3]'";
            $result_set = mysqli_query($conn, $select_query);
            if(!$result_set) goto errGo;

            $code = "surf";
            $shopcharge = $row[3];
            $account_yn = $row[4];
            $category = $row[6];
            $categoryname = $row[5];
            $shopname = $row[2];
            $tel_kakao = $row[7];
            $tel_admin = $row[8];
            $shopaddr = $row[9];
            $shoplat = $row[10];
            $shoplng = $row[11];
            $img_ver = $row[12];
            $sub_title = $row[13]."@".$row[14];
            $sub_tag = "";
            $sub_info = "서핑강습|0|".$row[16]."@숙박|게하|0|".$row[18];
            $imgurl = "https://surfenjoy.cdn3.cafe24.com/act_content/".$row[53]."/";
            $shop_img = $imgurl.$row[19]."|".$imgurl.$row[20]."|".$imgurl.$row[21]."|".$imgurl.$row[22]."|";
            //상세설명 구분 : file / html
            $content_type = $row[24];
            $content = str_replace("surfshop", "act_content/".$row[53],str_replace("'", "\"",$row[25]));
            $lesson_yn = $row[26];
            $rent_yn = $row[27];
            $bbq_yn = "N";
            $use_yn = "Y";
            $view_yn = "Y";
            $link_url = "";
            $link_yn = "N";
            $sell_cnt = $row[33];
            
            /* 입점샵 은행 계좌 정보 */
            $full_bankname = $row[34]; //은행
            $full_banknum = $row[35]; //계좌번호
            $full_bankuser = $row[36]; //예금주
            
            //주문연동 계좌 정보
            $bankname = ""; //은행
            $banknum = ""; //계좌번호
            $bankuser = ""; //예금주
            $banklinkUse = "N"; //주문영동 여부
            
            $debug_query = "";
            /* 입점샵 은행계좌 입력 */
            $select_query = "INSERT INTO `AT_PROD_BANKNUM`(`shopname`, `full_bankname`, `full_banknum`, `full_bankuser`, `bankname`, `banknum`, `bankuser`, `banklinkUse`, `etc`) VALUES ('$shopname', '$full_bankname', '$full_banknum', '$full_bankuser', '$bankname', '$banknum', '$bankuser', '$banklinkUse', '');";
            $result_set = mysqli_query($conn, $select_query);
            echo 'AT_PROD_BANKNUM : '.$select_query.'<br><br>';
            if(!$result_set) goto errGo;
            
            /* 입점샵 메인정보 입력 */
            $select_query = "INSERT INTO `AT_PROD_MAIN` (`code`, `bankseq`, `shopcharge`, `account_yn`, `category`, `categoryname`, `shopname`, `tel_kakao`, `tel_admin`, `shopaddr`, `shoplat`, `shoplng`, `img_ver`, `sub_title`, `sub_tag`, `sub_info`, `shop_img`, `content_type`, `content`, `lesson_yn`, `rent_yn`, `bbq_yn`, `use_yn`, `view_yn`, `link_url`, `link_yn`, `sell_cnt`, `insuserid`, `insdate`, `upduserid`, `upddate`) VALUES
            ('$code', LAST_INSERT_ID(), '$shopcharge', '$account_yn', '$category', '$categoryname', '$shopname', '$tel_kakao', '$tel_admin', '$shopaddr', '$shoplat', '$shoplng', $img_ver, '$sub_title', '$sub_tag', '$sub_info', '$shop_img', '$content_type', '$content', '$lesson_yn', '$rent_yn', '$bbq_yn', '$use_yn', '$view_yn', '$link_url', '$link_yn', $sell_cnt, '$insuserid', '$datetime', '$insuserid', '$datetime');";
            $result_set = mysqli_query($conn, $select_query);
            $debug_query .= '<br><br>AT_PROD_MAIN:'.$select_query;
            echo 'AT_PROD_MAIN : '.$select_query.'<br><br>';
            if(!$result_set) goto errGo;
            
            $result = mysqli_query($conn, "select LAST_INSERT_ID() as identity");
            $rowMain = mysqli_fetch_array($result);
            $seq = $rowMain["identity"];
            
            /* 입점샵 운영기간 입력 */
            $select_query = "INSERT INTO `AT_PROD_DAY` (`seq`, `ordernum`, `day_type`, `day_name`, `sdate`, `edate`, `day_week`, `week0`, `week1`, `week2`, `week3`, `week4`, `week5`, `week6`, `lesson_price`, `rent_price`, `stay_price`, `bbq_price`, `use_yn`) VALUES 
            ($seq, 1, 1, '비수기1', '2020-04-01', '2020-06-30', '0,1,2,3,4,5,6', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0, 0, 0, 0, 'N'), 
            ($seq, 2, 3, '성수기1', '2020-07-01', '2020-08-31', '0,1,2,3,4,5,6', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0, 0, 5000, 0, 'N'), 
            ($seq, 3, 1, '비수기2', '2020-09-01', '2019-10-31', '0,1,2,3,4,5,6', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 0, 0, 0, 0, 'N');";
            $result_set = mysqli_query($conn, $select_query);
            echo 'AT_PROD_DAY : '.$select_query.'<br><br>';
            if(!$result_set) goto errGo;
        
            /* 입점샵 옵션 입력 */
            $select_query = "INSERT INTO `AT_PROD_OPT` (`seq`, `optcode`, `optname`, `optsubname`, `opttime`, `opt_sexM`, `opt_sexW`, `opt_info`, `default_price`, `account_price`, `sell_price`, `shopcharge`, `use_yn`, `list_yn`, `peak_yn`, `stay_day`, `ordernum`) VALUES 
            ($seq, 'lesson', '입문강습', '', '$row[45]', 9, 9, '', $row[46], ".($row[46] * 0.9).", $row[46], 10, 'Y', 'Y', 'N', -1, 1), 
            ($seq, 'lesson', '입문강습+숙박 1박(당일)', '', '$row[45]', 9, 9, '', $row[47], ".($row[47] * 0.9).", $row[47], 10, 'Y', 'Y', 'Y', 0, 2),
            ($seq, 'lesson', '입문강습+숙박 1박(전날)', '', '$row[45]', 9, 9, '', $row[47], ".($row[47] * 0.9).", $row[47], 10, 'Y', 'Y', 'Y', 1, 3),
            ($seq, 'lesson', '입문강습+숙박 2박', '', '$row[45]', 9, 9, '', $row[48], ".($row[48] * 0.9).", $row[48], 10, 'Y', 'Y', 'Y', 2, 4),
        
            ($seq, 'rent', '스펀지보드', '', '', 9, 9, '', $row[49], ".($row[49] * 0.9).", $row[49], 10, 'Y', 'Y', 'N', -1, 5), 
            ($seq, 'rent', '에폭시보드', '', '', 9, 9, '', $row[50], ".($row[50] * 0.9).", $row[50], 10, 'Y', 'Y', 'N', -1, 6), 
            ($seq, 'rent', '서핑슈트', '', '', 9, 9, '', $row[51], ".($row[51] * 0.9).", $row[51], 10, 'Y', 'Y', 'N', -1, 7);";
            $result_set = mysqli_query($conn, $select_query);
            echo 'AT_PROD_OPT : '.$select_query.'<br><br>';
            if(!$result_set) goto errGo;

            $i++;
        }
    }
    $groupData[$key] = $selData;
    // }else{
    // }      
}

if(!$result_set){
    errGo:
    mysqli_query($conn, "ROLLBACK");
    echo 'err';
}else{
    mysqli_query($conn, "COMMIT");
    echo $i.'건 등록완료';
}

// $output = json_encode($groupData, JSON_UNESCAPED_UNICODE);
// echo urldecode($output);

unlink($img_name);
?>