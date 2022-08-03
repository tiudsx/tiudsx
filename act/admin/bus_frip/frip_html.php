<?php
include __DIR__.'/../../db.php';
include __DIR__.'/../../common/kakaoalim.php';
include __DIR__.'/../../frip/inc_func.php';

$success = true;
$res_date = $_REQUEST["res_date"];
$param = $_REQUEST["resparam"];
$kakaoUse = $_REQUEST["kakaouse"];

$select_query_sub = "SELECT a.user_tel, a.user_name FROM AT_RES_MAIN a INNER JOIN AT_RES_SUB b on a.resnum = b.resnum WHERE b.res_confirm = 3 and b.res_date = '".$res_date."' GROUP by user_tel";
$resultSite = mysqli_query($conn, $select_query_sub);
$count = mysqli_num_rows($resultSite);
if($count == 0){
   return;
}
if($param == 2 || $param == 3){
   $msgTitle = '액트립 셔틀버스 성수기 유의사항 안내';
   $btnList = '';//'"button1":{"type":"WL","name":"공지사항","url_mobile":"http://actrip.co.kr/surfbus_yy"},';
   $tempName = "at_res_step4";
   $arryKakao = '';

   $i = 0;
   $z = 1;
   //"phn":"'.$user_tel.'",
   while ($rowSub = mysqli_fetch_assoc($resultSite)){
      $user_tel = $rowSub['user_tel'];
      $user_name = $rowSub['user_name'];

      if(($i % 90) == 0 && $i > 0){
         //중간 카톡 실행
         $rtnMsg = '['.$arryKakao.']';
         $arryKakao = '';
         
         // echo "<br><br>페이지.".$z;
         // echo "<br>결과 : ".$rtnMsg;

         if($kakaoUse == "Y"){
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => "https://alimtalk-api.bizmsg.kr/v2/sender/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $rtnMsg,
            CURLOPT_HTTPHEADER => array(
            "content-type: application/json", "userId: surfenjoy"
            ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            echo "<br><br>페이지.".$z;
            echo "<br>결과.".$response;
            echo "<br>에러.".$err;

            curl_close($curl);
         }else{
            echo "<br><br>페이지.".$z;
            echo "<br>결과.".$rtnMsg;
         }
         $z++;
      }

      if($param == 3){
         $kakaoMsg = $msgTitle.'\n\n안녕하세요. 액트립 고객님\n액트립 셔틀버스를 예약해주셔서 감사드립니다.\n여름시즌 극성수기 이용에 대한 유의사항 안내드립니다.\n\n성수기 유의사항 안내\n ▶ 예약자 : '.$user_name.'\n---------------------------------\n ▶ 안내사항\n8월 15일까지는 극성수기 기간으로 서울 및 양양고속도로는 극심한 교통정체가 발생하고 있습니다.\n\n이로 인해 기존 2~3시간 정도 소요되던 운행시간이 3~5시간 이상 소요될 수 있는데요.\n\n긴 운행시간으로 인해 불편하실 수 있습니다.\n\n서울 도착시점에서도 교통정체가 극심하기에 잠실역 하차하셔서 지하철 이용을 추천드리고 있습니다.\n\n이로 인해 불편함이 크신 분들께서는 대체 대중교통을 이용하셔서 일요일 가장 늦은시간 출발을 추천드리고 있습니다.\n\n이점 참고하셔서 액트립 셔틀버스 이용부탁드립니다.\n\n고객님들 모두 불편함 없는 즐거운 주말 여행이 되시길 바랍니다.\n\n    감사합니다~';
      }else{
         $kakaoMsg = $msgTitle.'\n\n안녕하세요. 액트립 고객님\n액트립 셔틀버스를 예약해주셔서 감사드립니다.\n여름시즌 극성수기 이용에 대한 유의사항 안내드립니다.\n\n성수기 유의사항 안내\n ▶ 예약자 : '.$user_name.'\n---------------------------------\n ▶ 안내사항\n8월 15일까지는 극성수기 기간으로 서울 및 양양고속도로는 극심한 교통정체가 발생하고 있습니다.\n\n이로 인해 기존 2~3시간 정도 소요되던 운행시간이 3~5시간 이상 소요될 수 있는데요.\n\n긴 운행시간으로 인해 불편하실 수 있습니다.\n\n또한 서핑강습을 예약하신 분들의 경우 시간 변경이 필요할 수도 있는데요.\n\n이로 인해 불편함이 크신 분들께서는 대체 대중교통을 이용하셔서 금요일 출발 또는 토요일 새벽 출발을 추천드리고 있습니다.\n\n이점 참고하셔서 액트립 셔틀버스 이용부탁드립니다.\n\n고객님들 모두 불편함 없는 즐거운 주말 여행이 되시길 바랍니다.\n\n감사합니다~';
      }
      
      //'.$user_tel.'
      $arryKakao .= '{"message_type":"at","phn":"'.$user_tel.'","profile":"70f9d64c6d3b9d709c05a6681a805c6b27fc8dca","tmplId":"'.$tempName.'","msg":"'.$kakaoMsg.'",'.$btnList.'"smsKind":"L","msgSms":"'.$kakaoMsg.'","smsSender":"010-3308-6080","smsLmsTit":"'.$msgTitle.'","smsOnly":"N"},';

      if($i == 0){
         //break;
      }

      $i++;
   }

   $arryKakao .= '{"message_type":"at","phn":"010-4437-0009","profile":"70f9d64c6d3b9d709c05a6681a805c6b27fc8dca","tmplId":"'.$tempName.'","msg":"'.$kakaoMsg.'",'.$btnList.'"smsKind":"L","msgSms":"'.$kakaoMsg.'","smsSender":"010-3308-6080","smsLmsTit":"'.$msgTitle.'","smsOnly":"N"}';

   $rtnMsg = '['.$arryKakao.']';

   if($kakaoUse == "Y"){
      $curl = curl_init();

      curl_setopt_array($curl, array(
         CURLOPT_URL => "https://alimtalk-api.bizmsg.kr/v2/sender/send",
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => "",
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 30,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_CUSTOMREQUEST => "POST",
         CURLOPT_POSTFIELDS => $rtnMsg,
         CURLOPT_HTTPHEADER => array(
         "content-type: application/json", "userId: surfenjoy"
         ),
      ));

      $response = curl_exec($curl);
      $err = curl_error($curl);

      echo "<br><br>페이지.".$z;
      echo "<br>결과.".$response;
      echo "<br>에러.".$err;

      curl_close($curl);
   }else{
      echo "<br><br>페이지.".$z;
      echo "<br>결과.".$rtnMsg;
   }
}else{
   return;
   $msgTitle = '액트립 셔틀버스 성수기 유의사항 안내';

   if($param == 1){
      $kakaoMsg = "안녕하세요.\n액트립 셔틀버스를 예약해주셔서 감사드립니다.\n여름시즌 극성수기 이용에 대한 유의사항 안내드립니다.\n\n8월 15일까지는 극성수기 기간으로 양양고속도로는 극심한 교통정체가 발생하고 있습니다.\n이로 인해 기존 2~3시간 정도 소요되던 운행시간이 3~5시간 이상 소요될 수 있는데요.\n긴 운행시간으로 인해 불편하실 수 있습니다.\n서울 도착시점에서도 교통정체가 극심하기에 잠실역 하차하셔서 지하철 이용을 추천드리고 있습니다.\n이로 인해 불편함이 크신 분들께서는 대체 대중교통을 이용하셔서 일요일 가장 늦은시간 출발을 추천드리고 있습니다.\n이점 참고하셔서 액트립 셔틀버스 이용부탁드립니다.\n고객님들 모두 불편함 없는 즐거운 주말 여행이 되시길 바랍니다.\n감사합니다~";
   }else{
      $kakaoMsg = "안녕하세요.\n액트립 셔틀버스를 예약해주셔서 감사드립니다.\n여름시즌 극성수기 이용에 대한 유의사항 안내드립니다.\n\n8월 15일까지는 극성수기 기간으로 서울 및 양양고속도로는 극심한 교통정체가 발생하고 있습니다.\n이로 인해 기존 2~3시간 정도 소요되던 운행시간이 3~5시간 이상 소요될 수 있는데요.\n긴 운행시간으로 인해 불편하실 수 있습니다.\n또한 서핑강습을 예약하신 분들의 경우 시간 변경이 필요할 수도 있는데요.\n이로 인해 불편함이 크신 분들께서는 대체 대중교통을 이용하셔서 금요일 출발 또는 토요일 새벽 출발을 추천드리고 있습니다.\n이점 참고하셔서 액트립 셔틀버스 이용부탁드립니다.\n고객님들 모두 불편함 없는 즐거운 주말 여행이 되시길 바랍니다.\n감사합니다~";
   }
   $arryKakao = '';
   $i = 0;
   $z = 1;
   //"phn":"'.$user_tel.'",
   while ($rowSub = mysqli_fetch_assoc($resultSite)){
      $user_tel = $rowSub['user_tel'];

      if(($i % 90) == 0 && $i > 0){
         //중간 카톡 실행
         $rtnMsg = '['.$arryKakao.']';
         $arryKakao = '';

         // echo "<br><br>페이지.".$z;
         // echo "<br>결과 : ".$rtnMsg;
         if($kakaoUse == "Y"){
            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => "https://alimtalk-api.bizmsg.kr/v2/sender/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $rtnMsg,
            CURLOPT_HTTPHEADER => array(
            "content-type: application/json", "userId: surfenjoy"
            ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            echo "<br><br>페이지.".$z;
            echo "<br>결과.".$response;
            echo "<br>에러.".$err;

            curl_close($curl);
         }else{
            echo "<br><br>페이지.".$z;
            echo "<br>결과.".$rtnMsg;
         }
         $z++;
      }
      
      $arryKakao .= '{
         "phn":"'.$user_tel.'",
         "profile":"70f9d64c6d3b9d709c05a6681a805c6b27fc8dca",
         "reserveDt":"00000000000000",
         "smsOnly":"Y",
         "smsKind":"L",
         "msgSms":"'.$kakaoMsg.'",
         "smsSender":"010-3308-6080",
         "smsLmsTit":"'.$msgTitle.'"
         },';

      if($i == 0){
         //break;
      }

      $i++;
   }

   $rtnMsg = '[
   '.$arryKakao.'
   {
      "phn":"010-4437-0009",
      "profile":"70f9d64c6d3b9d709c05a6681a805c6b27fc8dca",
      "reserveDt":"00000000000000",
      "smsOnly":"Y",
      "smsKind":"L",
      "msgSms":"'.$kakaoMsg.'",
      "smsSender":"010-3308-6080",
      "smsLmsTit":"'.$msgTitle.'"
      }
   ]';

   // echo "<br><br>페이지.".$z;
   // echo "<br>결과 : ".$rtnMsg;
   //return;
   if($kakaoUse == "Y"){
      $curl = curl_init();

      curl_setopt_array($curl, array(
      CURLOPT_URL => "https://alimtalk-api.bizmsg.kr/v2/sender/send",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $rtnMsg,
      CURLOPT_HTTPHEADER => array(
      "content-type: application/json", "userId: surfenjoy"
      ),
      ));

      $response = curl_exec($curl);
      $err = curl_error($curl);

      echo "<br><br>페이지.".$z;
      echo "<br>결과.".$response;
      echo "<br>에러.".$err;

      curl_close($curl);
   }else{
      echo "<br><br>페이지.".$z;
      echo "<br>결과.".$rtnMsg;
   }
}
?>